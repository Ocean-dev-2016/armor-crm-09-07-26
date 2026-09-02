<?php

/**
 * Resize/compress images for mPDF quotation export (keeps photos, low memory).
 */

if (!function_exists('armor_pdf_image_cache_dir')) {
	function armor_pdf_image_cache_dir()
	{
		$dir = dirname(__FILE__) . '/../bbsales_tracking/pdf/tmp_img_cache/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		return $dir;
	}
}

if (!function_exists('armor_pdf_image_cache_reset')) {
	function armor_pdf_image_cache_reset()
	{
		$GLOBALS['armor_pdf_image_cache'] = array();
	}
}

if (!function_exists('armor_pdf_resolve_local_image_path')) {
	function armor_pdf_resolve_local_image_path($src)
	{
		$src = trim(html_entity_decode($src));
		if ($src === '') {
			return '';
		}

		$roots = array();
		$includeDir = dirname(__FILE__);
		$roots[] = realpath($includeDir . '/..');
		$roots[] = realpath($includeDir . '/../bbsales_tracking');

		$tryPath = function ($candidate) {
			if ($candidate !== '' && is_file($candidate)) {
				return $candidate;
			}
			return '';
		};

		$preferWebp = function ($path) {
			if ($path === '' || !is_file($path)) {
				return '';
			}
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			if ($ext === 'webp') {
				return $path;
			}
			$webp = preg_replace('/\.[^.]+$/', '.webp', $path);
			if ($webp !== $path && is_file($webp)) {
				return $webp;
			}
			return $path;
		};

		if (preg_match('/^https?:\/\//i', $src)) {
			foreach (array('SITEURL', 'ADMINSITEURL') as $const) {
				if (!defined($const) || strpos($src, constant($const)) !== 0) {
					continue;
				}
				$rel = ltrim(substr($src, strlen(constant($const))), '/');
				foreach (array_unique(array_filter($roots)) as $root) {
					$candidate = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
					$found = $tryPath($candidate);
					if ($found !== '') {
						return $preferWebp($found);
					}
					$webpCandidate = preg_replace('/\.[^.]+$/', '.webp', $candidate);
					if ($webpCandidate !== $candidate) {
						$found = $tryPath($webpCandidate);
						if ($found !== '') {
							return $found;
						}
					}
				}
			}
			return '';
		}

		if (is_file($src)) {
			return $preferWebp($src);
		}

		$urlPath = ltrim($src, '/');
		foreach (array_unique(array_filter($roots)) as $root) {
			$candidate = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $urlPath);
			$found = $tryPath($candidate);
			if ($found !== '') {
				return $preferWebp($found);
			}
			$webpCandidate = preg_replace('/\.[^.]+$/', '.webp', $candidate);
			if ($webpCandidate !== $candidate) {
				$found = $tryPath($webpCandidate);
				if ($found !== '') {
					return $found;
				}
			}
		}

		return '';
	}
}

if (!function_exists('armor_pdf_load_image_bytes')) {
	function armor_pdf_load_image_bytes($src)
	{
		$src = trim(html_entity_decode($src));
		if ($src === '') {
			return false;
		}
		if (strpos($src, 'data:image') === 0) {
			$parts = explode(',', $src, 2);
			return isset($parts[1]) ? base64_decode($parts[1]) : false;
		}

		$local = armor_pdf_resolve_local_image_path($src);
		if ($local !== '' && is_file($local)) {
			return @file_get_contents($local);
		}

		if (!preg_match('/^https?:\/\//i', $src)) {
			return false;
		}

		if (function_exists('curl_init')) {
			$ch = curl_init($src);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			if ($data) {
				return $data;
			}
		}

		return @file_get_contents($src);
	}
}

if (!function_exists('armor_pdf_resize_to_jpeg_bytes')) {
	function armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality = 52)
	{
		if (extension_loaded('imagick') && class_exists('Imagick')) {
			try {
				$im = new Imagick();
				$im->readImageBlob($bytes);
				$im->setImageCompressionQuality((int) $quality);
				$im->thumbnailImage((int) $maxW, (int) $maxH, true, true);
				if (strtolower($im->getImageFormat()) === 'webp') {
					$im->setImageFormat('jpeg');
				} else {
					$im->setImageFormat('jpeg');
				}
				$jpeg = $im->getImageBlob();
				$im->clear();
				$im->destroy();
				return $jpeg ? $jpeg : '';
			} catch (Exception $e) {
			}
		}

		if (!$bytes || !function_exists('imagecreatefromstring')) {
			return '';
		}

		$img = @imagecreatefromstring($bytes);
		if (!$img) {
			return '';
		}

		$w = imagesx($img);
		$h = imagesy($img);
		if ($w < 1 || $h < 1) {
			imagedestroy($img);
			return '';
		}

		$ratio = min($maxW / $w, $maxH / $h, 1);
		$nw = max(1, (int) floor($w * $ratio));
		$nh = max(1, (int) floor($h * $ratio));

		$dst = imagecreatetruecolor($nw, $nh);
		$white = imagecolorallocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);
		imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
		imagedestroy($img);

		ob_start();
		imagejpeg($dst, null, (int) $quality);
		imagedestroy($dst);
		$jpeg = ob_get_clean();

		return $jpeg ? $jpeg : '';
	}
}

if (!function_exists('armor_pdf_compress_image_src')) {
	function armor_pdf_compress_image_src($src, $maxW, $maxH, $quality = 52)
	{
		if (!isset($GLOBALS['armor_pdf_image_cache'])) {
			$GLOBALS['armor_pdf_image_cache'] = array();
		}

		$key = md5($src . '|' . $maxW . 'x' . $maxH . '|' . $quality);
		if (isset($GLOBALS['armor_pdf_image_cache'][$key])) {
			return $GLOBALS['armor_pdf_image_cache'][$key];
		}

		$cacheFile = armor_pdf_image_cache_dir() . $key . '.jpg';
		if (is_file($cacheFile) && filesize($cacheFile) > 20) {
			$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
			return $cacheFile;
		}

		$bytes = armor_pdf_load_image_bytes($src);
		if ($bytes === false || strlen($bytes) > 3145728) {
			unset($bytes);
			$GLOBALS['armor_pdf_image_cache'][$key] = '';
			return '';
		}
		$jpeg = armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality);
		unset($bytes);

		if ($jpeg === '') {
			$GLOBALS['armor_pdf_image_cache'][$key] = '';
			return '';
		}

		@file_put_contents($cacheFile, $jpeg);

		// Optional webp cache copy (smaller on disk for reuse)
		if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
			$img = @imagecreatefromstring($jpeg);
			if ($img) {
				@imagewebp($img, armor_pdf_image_cache_dir() . $key . '.webp', 60);
				imagedestroy($img);
			}
		}
		unset($jpeg);

		$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
		if (function_exists('gc_collect_cycles')) {
			gc_collect_cycles();
		}
		return $cacheFile;
	}
}

if (!function_exists('armor_pdf_guess_image_limits')) {
	function armor_pdf_guess_image_limits($imgTag)
	{
		$tag = strtolower($imgTag);
		if (strpos($tag, 'quote-header') !== false || strpos($tag, 'craftbox_header') !== false || strpos($tag, 'view_logo') !== false || strpos($tag, 'max-height:70') !== false || strpos($tag, 'max-height:90') !== false) {
			return array(520, 72, 55);
		}
		if (strpos($tag, 'qp-prod') !== false || strpos($tag, '42px') !== false) {
			return array(40, 40, 50);
		}
		if (strpos($tag, 'width: 80px') !== false || strpos($tag, 'width:80px') !== false) {
			return array(54, 54, 58);
		}
		if (strpos($tag, 'width: 50px') !== false || strpos($tag, 'width:50px') !== false) {
			return array(48, 48, 58);
		}
		return array(50, 50, 58);
	}
}

if (!function_exists('armor_pdf_compress_images_in_html')) {
	function armor_pdf_compress_images_in_html($html, $useLocalPaths = false)
	{
		armor_pdf_image_cache_reset();
		$processed = 0;

		$html = preg_replace_callback('/<img\b[^>]*>/i', function ($m) use (&$processed, $useLocalPaths) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}

			$src = $srcMatch[2];
			if (strpos($src, 'data:image') === 0) {
				return $tag;
			}

			$processed++;
			if (function_exists('gc_collect_cycles')) {
				gc_collect_cycles();
			}

			list($maxW, $maxH, $quality) = armor_pdf_guess_image_limits($tag);
			$filePath = armor_pdf_compress_image_src($src, $maxW, $maxH, $quality);
			if ($filePath === '' || !is_file($filePath)) {
				return $tag;
			}

			if ($useLocalPaths) {
				$imgSrc = str_replace('\\', '/', $filePath);
			} else {
				$jpegBytes = @file_get_contents($filePath);
				if ($jpegBytes === false || $jpegBytes === '') {
					return $tag;
				}
				$imgSrc = 'data:image/jpeg;base64,' . base64_encode($jpegBytes);
				unset($jpegBytes);
			}

			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $imgSrc . '"', $tag, 1);
			$newTag = preg_replace('/\sstyle=(["\'])[^"\']*\1/i', '', $newTag);
			$newTag = preg_replace('/<img/i', '<img style="max-width:' . $maxW . 'px;max-height:' . $maxH . 'px;"', $newTag, 1);
			return $newTag;
		}, (string) $html);

		return $html;
	}
}
