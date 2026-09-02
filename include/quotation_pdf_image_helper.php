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
		$r1 = realpath($includeDir . '/..');
		$r2 = realpath($includeDir . '/../bbsales_tracking');
		if ($r1) $roots[] = $r1;
		if ($r2) $roots[] = $r2;

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

		if (is_file($src)) {
			return $preferWebp($src);
		}

		// Extract URL path if full URL
		$urlPath = $src;
		if (preg_match('/^https?:\/\//i', $src)) {
			$parsed = parse_url($src, PHP_URL_PATH);
			if ($parsed !== null && $parsed !== false) {
				$urlPath = $parsed;
			}
		}

		$urlPath = str_replace(array('\\', '//'), '/', $urlPath);

		// Match known root relative subdirectories
		foreach (array('/images/', '/bbsales_tracking/', '/upload/', '/pdf/', '/assets/') as $subDir) {
			$pos = strpos($urlPath, $subDir);
			if ($pos !== false) {
				$rel = ltrim(substr($urlPath, $pos), '/');
				foreach ($roots as $root) {
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
		}

		// Try direct root join with trimmed path
		$cleanPath = ltrim($urlPath, '/');
		foreach ($roots as $root) {
			$candidate = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);
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

if (!function_exists('armor_pdf_is_valid_image_src')) {
	function armor_pdf_is_valid_image_src($src)
	{
		$src = trim(html_entity_decode((string) $src));
		if ($src === '' || $src === '#') {
			return false;
		}
		$path = parse_url($src, PHP_URL_PATH);
		if ($path !== null && preg_match('#/images/product/?$#i', $path)) {
			return false;
		}
		if (preg_match('#/images/product/?$#i', $src)) {
			return false;
		}
		$local = armor_pdf_resolve_local_image_path($src);
		if ($local !== '' && is_file($local) && filesize($local) > 20) {
			return true;
		}
		if (preg_match('/^https?:\/\//i', $src)) {
			return ($path !== null && $path !== '' && substr($path, -1) !== '/');
		}
		return ($local !== '' && is_file($local));
	}
}

if (!function_exists('armor_pdf_blank_jpeg_path')) {
	function armor_pdf_blank_jpeg_path()
	{
		$file = armor_pdf_image_cache_dir() . '_blank.jpg';
		if (!is_file($file) || filesize($file) < 10) {
			if (function_exists('imagecreatetruecolor')) {
				$img = imagecreatetruecolor(1, 1);
				$white = imagecolorallocate($img, 255, 255, 255);
				imagefill($img, 0, 0, $white);
				imagejpeg($img, $file, 60);
				imagedestroy($img);
			}
		}
		return is_file($file) ? $file : '';
	}
}

if (!function_exists('armor_pdf_load_image_bytes')) {
	function armor_pdf_load_image_bytes($src)
	{
		$src = trim(html_entity_decode($src));
		if ($src === '' || !armor_pdf_is_valid_image_src($src)) {
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

		// Never do slow loopback curl to own server during PDF generation
		$host = parse_url($src, PHP_URL_HOST);
		$curHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		if ($host === 'localhost' || $host === '127.0.0.1' || ($curHost && stripos($host, $curHost) !== false)) {
			return false;
		}

		if (function_exists('curl_init')) {
			$ch = curl_init($src);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			if ($data) {
				return $data;
			}
		}

		return false;
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

if (!function_exists('armor_pdf_resize_to_jpeg_bytes_from_file')) {
	function armor_pdf_resize_to_jpeg_bytes_from_file($path, $maxW, $maxH, $quality = 52)
	{
		if ($path === '' || !is_file($path)) {
			return '';
		}

		// GD direct-from-file is faster than Imagick for small PDF thumbnails.
		if (function_exists('imagecreatefromjpeg')) {
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			$img = false;
			if ($ext === 'jpg' || $ext === 'jpeg') {
				$img = @imagecreatefromjpeg($path);
			} elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
				$img = @imagecreatefrompng($path);
			} elseif ($ext === 'gif' && function_exists('imagecreatefromgif')) {
				$img = @imagecreatefromgif($path);
			} elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
				$img = @imagecreatefromwebp($path);
			}
			if ($img) {
				$w = imagesx($img);
				$h = imagesy($img);
				if ($w > 0 && $h > 0) {
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
					if ($jpeg) {
						return $jpeg;
					}
				}
				imagedestroy($img);
			}
		}

		$bytes = @file_get_contents($path);
		if ($bytes === false || $bytes === '') {
			return '';
		}
		$jpeg = armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality);
		unset($bytes);
		return $jpeg;
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

		$local = armor_pdf_resolve_local_image_path($src);
		if ($local !== '' && is_file($local)) {
			$key = md5($local . '|' . filesize($local) . '|' . @filemtime($local) . '|' . $maxW . 'x' . $maxH . '|' . $quality);
			if (isset($GLOBALS['armor_pdf_image_cache'][$key])) {
				return $GLOBALS['armor_pdf_image_cache'][$key];
			}
		}

		$cacheFile = armor_pdf_image_cache_dir() . $key . '.jpg';
		if (is_file($cacheFile) && filesize($cacheFile) > 20) {
			$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
			return $cacheFile;
		}

		$jpeg = '';
		if ($local !== '' && is_file($local)) {
			$jpeg = armor_pdf_resize_to_jpeg_bytes_from_file($local, $maxW, $maxH, $quality);
		}
		if ($jpeg === '') {
			$bytes = armor_pdf_load_image_bytes($src);
			if ($bytes !== false && $bytes !== '') {
				$jpeg = armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality);
				unset($bytes);
			}
		}

		if ($jpeg === '') {
			$GLOBALS['armor_pdf_image_cache'][$key] = '';
			return '';
		}

		@file_put_contents($cacheFile, $jpeg);
		unset($jpeg);

		$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
		return $cacheFile;
	}
}

if (!function_exists('armor_pdf_guess_image_limits')) {
	function armor_pdf_guess_image_limits($imgTag)
	{
		$tag = strtolower($imgTag);
		if (strpos($tag, 'quote-header') !== false || strpos($tag, 'craftbox_header') !== false || strpos($tag, 'view_logo') !== false || strpos($tag, 'quote-footer') !== false || strpos($tag, 'footer') !== false || strpos($tag, 'header') !== false) {
			return array(1200, 260, 90);
		}
		if (strpos($tag, 'qp-prod') !== false) {
			return array(42, 34, 75);
		}
		if (strpos($tag, 'image-width') !== false || strpos($tag, 'product') !== false || strpos($tag, 'width: 50px') !== false || strpos($tag, 'width:50px') !== false || strpos($tag, 'width: 80px') !== false || strpos($tag, 'width:80px') !== false) {
			return array(36, 36, 80);
		}
		return array(40, 40, 75);
	}
}

if (!function_exists('armor_pdf_blank_jpeg_data_uri')) {
	function armor_pdf_blank_jpeg_data_uri()
	{
		$file = armor_pdf_blank_jpeg_path();
		if ($file === '' || !is_file($file)) {
			return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		}
		$bytes = @file_get_contents($file);
		return ($bytes !== false && $bytes !== '') ? 'data:image/jpeg;base64,' . base64_encode($bytes) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
	}
}

if (!function_exists('armor_pdf_strip_remaining_remote_images')) {
	function armor_pdf_strip_remaining_remote_images($html)
	{
		$blank = armor_pdf_blank_jpeg_data_uri();
		return preg_replace_callback('/<img\b[^>]*>/i', function ($m) use ($blank) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}
			$src = $srcMatch[2];
			if (strpos($src, 'data:image') === 0) {
				return $tag;
			}
			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $blank . '"', $tag, 1);
			$newTag = preg_replace('/<img/i', '<img style="max-width:42px;max-height:42px;"', $newTag, 1);
			return $newTag;
		}, (string) $html);
	}
}

if (!function_exists('armor_pdf_compress_images_in_html')) {
	function armor_pdf_compress_images_in_html($html, $useLocalPaths = false)
	{
		armor_pdf_image_cache_reset();
		if (!isset($GLOBALS['armor_pdf_src_map'])) {
			$GLOBALS['armor_pdf_src_map'] = array();
		}

		$html = preg_replace_callback('/<img\b[^>]*>/i', function ($m) use ($useLocalPaths) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}

			$src = $srcMatch[2];
			if (strpos($src, 'data:image') === 0) {
				return $tag;
			}

			list($maxW, $maxH, $quality) = armor_pdf_guess_image_limits($tag);
			$mapKey = $src;
			$localForKey = armor_pdf_resolve_local_image_path($src);
			if ($localForKey !== '') {
				$mapKey = $localForKey;
			}

			if (!armor_pdf_is_valid_image_src($src)) {
				$filePath = armor_pdf_blank_jpeg_path();
			} elseif (isset($GLOBALS['armor_pdf_src_map'][$mapKey]) && is_file($GLOBALS['armor_pdf_src_map'][$mapKey])) {
				$filePath = $GLOBALS['armor_pdf_src_map'][$mapKey];
			} else {
				$filePath = armor_pdf_compress_image_src($src, $maxW, $maxH, $quality);
				if ($filePath !== '' && is_file($filePath)) {
					$GLOBALS['armor_pdf_src_map'][$mapKey] = $filePath;
				}
			}
			if ($filePath === '' || !is_file($filePath)) {
				$filePath = armor_pdf_blank_jpeg_path();
			}

			if ($useLocalPaths && $filePath !== '' && is_file($filePath)) {
				$resolved = realpath($filePath);
				$imgSrc = str_replace('\\', '/', ($resolved !== false ? $resolved : $filePath));
			} else {
				$jpegBytes = ($filePath !== '' && is_file($filePath)) ? @file_get_contents($filePath) : false;
				if ($jpegBytes === false || $jpegBytes === '') {
					$imgSrc = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
				} else {
					$imgSrc = 'data:image/jpeg;base64,' . base64_encode($jpegBytes);
					unset($jpegBytes);
				}
			}

			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $imgSrc . '"', $tag, 1);
			$newTag = preg_replace('/\sstyle=(["\'])[^"\']*\1/i', '', $newTag);
			$newTag = preg_replace('/<img/i', '<img style="max-width:' . $maxW . 'px;max-height:' . $maxH . 'px;"', $newTag, 1);
			return $newTag;
		}, (string) $html);

		return $html;
	}
}
