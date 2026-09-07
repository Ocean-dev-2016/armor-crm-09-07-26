<?php

/**
 * Resize/compress images for mPDF quotation export (keeps photos, low memory).
 */

if (!function_exists('armor_pdf_image_cache_dir')) {
	function armor_pdf_image_cache_dir()
	{
		$dir = dirname(__FILE__) . '/../bbsales_tracking/pdf/tmp_img_cache/';
		if (!is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
		if (!is_dir($dir) || !is_writable($dir)) {
			$dir = rtrim(sys_get_temp_dir(), '/\\') . '/armor_pdf_cache/';
			if (!is_dir($dir)) {
				@mkdir($dir, 0777, true);
			}
		}
		return rtrim($dir, '/\\') . '/';
	}
}

if (!function_exists('armor_pdf_image_cache_reset')) {
	function armor_pdf_image_cache_reset()
	{
		$GLOBALS['armor_pdf_image_cache'] = array();
		$GLOBALS['armor_pdf_src_map'] = array();
		$GLOBALS['armor_pdf_mpdf_vars'] = array();
		$GLOBALS['armor_pdf_mpdf_var_i'] = 0;
		$GLOBALS['armor_pdf_var_by_hash'] = array();
		$GLOBALS['armor_pdf_http_count'] = 0;
		$GLOBALS['armor_pdf_http_cache'] = array();
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
		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
			$doc = realpath($_SERVER['DOCUMENT_ROOT']);
			if ($doc) $roots[] = $doc;
		}
		$roots = array_values(array_unique(array_filter($roots)));

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
			// Prefer formats GD can decode on PHP 5.6 live (webp often missing).
			if ($ext === 'webp') {
				$jpg = preg_replace('/\.webp$/i', '.jpg', $path);
				if ($jpg !== $path && is_file($jpg)) {
					return $jpg;
				}
				$png = preg_replace('/\.webp$/i', '.png', $path);
				if ($png !== $path && is_file($png)) {
					return $png;
				}
				if (function_exists('imagecreatefromwebp')) {
					return $path;
				}
				return '';
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
		// Normalize bbsales_tracking-relative paths: ../images/header/foo.jpg
		while (strpos($urlPath, '../') === 0) {
			$urlPath = substr($urlPath, 3);
		}
		if (strpos($urlPath, './') === 0) {
			$urlPath = substr($urlPath, 2);
		}

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
							$converted = $preferWebp($found);
							if ($converted !== '') {
								return $converted;
							}
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
					$converted = $preferWebp($found);
					if ($converted !== '') {
						return $converted;
					}
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
		if (strpos($src, 'data:image') === 0 || strpos($src, 'var:') === 0) {
			return true;
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
		// Allow same-host HTTP fallback when file is not on local disk path.
		if (preg_match('/^https?:\/\//i', $src) && preg_match('/\.(jpe?g|png|webp)(\?|$)/i', $src)) {
			return true;
		}
		if (preg_match('/\.(jpe?g|png|webp)(\?|$)/i', $src)) {
			return true;
		}
		return false;
	}
}

if (!function_exists('armor_pdf_blank_jpeg_path')) {
	function armor_pdf_blank_jpeg_path()
	{
		// 40x40 light placeholder (1x1 becomes a black/dot in mPDF when scaled).
		$file = armor_pdf_image_cache_dir() . '_blank40.jpg';
		if (!is_file($file) || filesize($file) < 50) {
			if (function_exists('imagecreatetruecolor')) {
				$img = imagecreatetruecolor(40, 40);
				$bg = imagecolorallocate($img, 245, 245, 245);
				$bd = imagecolorallocate($img, 210, 210, 210);
				imagefill($img, 0, 0, $bg);
				imagerectangle($img, 0, 0, 39, 39, $bd);
				imagejpeg($img, $file, 70);
				imagedestroy($img);
			}
		}
		return is_file($file) ? $file : '';
	}
}

if (!function_exists('armor_pdf_is_jpeg_bytes')) {
	function armor_pdf_is_jpeg_bytes($bytes)
	{
		return is_string($bytes) && strlen($bytes) > 20 && ord($bytes[0]) === 0xFF && ord($bytes[1]) === 0xD8;
	}
}

if (!function_exists('armor_pdf_safer_source_path')) {
	/**
	 * Prefer smaller product thumbs when present.
	 * Headers: always keep the real company header (same as web print) — never swap to craftbox here.
	 */
	function armor_pdf_safer_source_path($local, $maxW, $maxH, $isHeader)
	{
		if ($local === '' || !is_file($local)) {
			return '';
		}
		// Keep real header/footer (web print image). Huge files are downscaled in resize step.
		if ($isHeader) {
			return $local;
		}

		$info = @getimagesize($local);
		$w = ($info && isset($info[0])) ? (int) $info[0] : 0;
		$h = ($info && isset($info[1])) ? (int) $info[1] : 0;
		$sz = (int) @filesize($local);
		$pixels = ($w > 0 && $h > 0) ? ($w * $h) : 0;

		// Product thumbs
		$ext = strtolower(pathinfo($local, PATHINFO_EXTENSION));
		$base = pathinfo($local, PATHINFO_FILENAME);
		$dir = dirname($local);
		$parent = dirname($dir);
		$thumbCandidates = array(
			$dir . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $base . '.' . $ext,
			$dir . DIRECTORY_SEPARATOR . 'small' . DIRECTORY_SEPARATOR . $base . '.' . $ext,
			$dir . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $base . '.jpg',
			$dir . DIRECTORY_SEPARATOR . 'small' . DIRECTORY_SEPARATOR . $base . '.jpg',
			$parent . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $base . '.jpg',
			$parent . DIRECTORY_SEPARATOR . 'small' . DIRECTORY_SEPARATOR . $base . '.jpg',
		);
		if ($pixels > 900000 || $sz > 250000) {
			foreach ($thumbCandidates as $cand) {
				if (is_file($cand) && filesize($cand) > 20) {
					return $cand;
				}
			}
		}

		return $local;
	}
}

if (!function_exists('armor_pdf_http_get_image_bytes')) {
	function armor_pdf_http_get_image_bytes($url)
	{
		$url = trim($url);
		if (!preg_match('/^https?:\/\//i', $url)) {
			return false;
		}
		// Only fetch images under known public folders.
		if (!preg_match('#/(images|upload|bbsales_tracking)/#i', $url)) {
			return false;
		}
		// Cap remote fetches — same-server curl during PDF can deadlock / timeout.
		if (!isset($GLOBALS['armor_pdf_http_count'])) {
			$GLOBALS['armor_pdf_http_count'] = 0;
		}
		if (!isset($GLOBALS['armor_pdf_http_cache'])) {
			$GLOBALS['armor_pdf_http_cache'] = array();
		}
		$ck = md5($url);
		if (array_key_exists($ck, $GLOBALS['armor_pdf_http_cache'])) {
			return $GLOBALS['armor_pdf_http_cache'][$ck];
		}
		if ($GLOBALS['armor_pdf_http_count'] >= 8) {
			$GLOBALS['armor_pdf_http_cache'][$ck] = false;
			return false;
		}
		$GLOBALS['armor_pdf_http_count']++;

		$data = false;
		if (function_exists('curl_init')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 4);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'ArmorPDF/1.0');
			$raw = curl_exec($ch);
			$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($code >= 200 && $code < 300 && $raw !== false && strlen($raw) > 20) {
				$data = $raw;
			}
		} else {
			$ctx = stream_context_create(array(
				'http' => array('timeout' => 4, 'header' => "User-Agent: ArmorPDF/1.0\r\n"),
				'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
			));
			$raw = @file_get_contents($url, false, $ctx);
			if ($raw !== false && strlen($raw) > 20) {
				$data = $raw;
			}
		}

		$GLOBALS['armor_pdf_http_cache'][$ck] = $data;
		return $data;
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

		if (preg_match('/^https?:\/\//i', $src)) {
			return armor_pdf_http_get_image_bytes($src);
		}

		// Relative path → try SITEURL absolute
		if (defined('SITEURL') && $src !== '' && strpos($src, 'images/') !== false) {
			$rel = $src;
			while (strpos($rel, '../') === 0) {
				$rel = substr($rel, 3);
			}
			$abs = rtrim(SITEURL, '/') . '/' . ltrim($rel, '/');
			return armor_pdf_http_get_image_bytes($abs);
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

		$info = @getimagesize($path);
		$w0 = ($info && isset($info[0])) ? (int) $info[0] : 0;
		$h0 = ($info && isset($info[1])) ? (int) $info[1] : 0;
		$pixels = ($w0 > 0 && $h0 > 0) ? ($w0 * $h0) : 0;

		// Imagick first for large images (company headers can be 10k+ px — must keep real image, not craftbox).
		if ($pixels > 1500000 && extension_loaded('imagick') && class_exists('Imagick')) {
			try {
				$im = new Imagick($path);
				$im->setImageCompressionQuality((int) $quality);
				$im->thumbnailImage((int) $maxW, (int) $maxH, true, true);
				$im->setImageFormat('jpeg');
				$im->setImageBackgroundColor('white');
				$jpeg = $im->getImageBlob();
				$im->clear();
				$im->destroy();
				if (armor_pdf_is_jpeg_bytes($jpeg)) {
					return $jpeg;
				}
			} catch (Exception $e) {
			}
		}

		// Huge JPEG headers: raise memory and still decode real file (web print image).
		if ($pixels > 4000000) {
			@ini_set('memory_limit', '1024M');
		}
		// Hard refuse only absurd sizes (prevents live OOM kill).
		if ($pixels > 40000000) {
			return '';
		}

		// GD direct-from-file is faster than Imagick for small PDF thumbnails.
		// Never decode GIF here — animated/corrupt GIFs hang GD/mPDF for minutes (gif.php).
		if (function_exists('imagecreatefromjpeg')) {
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			if ($ext === 'gif') {
				return '';
			}
			$img = false;
			if ($ext === 'jpg' || $ext === 'jpeg') {
				$img = @imagecreatefromjpeg($path);
			} elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
				$sz = @filesize($path);
				if ($sz !== false && $sz > 0 && $sz < 12000000) {
					$img = @imagecreatefrompng($path);
				}
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
					if (armor_pdf_is_jpeg_bytes($jpeg)) {
						return $jpeg;
					}
				} else {
					imagedestroy($img);
				}
			}
		}

		$bytes = @file_get_contents($path);
		if ($bytes === false || $bytes === '') {
			return '';
		}
		$jpeg = armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality);
		unset($bytes);
		return armor_pdf_is_jpeg_bytes($jpeg) ? $jpeg : '';
	}
}

if (!function_exists('armor_pdf_compress_image_src')) {
	function armor_pdf_compress_image_src($src, $maxW, $maxH, $quality = 52, $isHeader = false)
	{
		if (!isset($GLOBALS['armor_pdf_image_cache'])) {
			$GLOBALS['armor_pdf_image_cache'] = array();
		}

		$key = md5($src . '|' . $maxW . 'x' . $maxH . '|' . $quality . '|' . ($isHeader ? 'H' : 'P'));
		if (isset($GLOBALS['armor_pdf_image_cache'][$key])) {
			return $GLOBALS['armor_pdf_image_cache'][$key];
		}

		$local = armor_pdf_resolve_local_image_path($src);
		if ($local !== '' && is_file($local)) {
			$local = armor_pdf_safer_source_path($local, $maxW, $maxH, $isHeader);
			$key = md5($local . '|' . filesize($local) . '|' . @filemtime($local) . '|' . $maxW . 'x' . $maxH . '|' . $quality);
			if (isset($GLOBALS['armor_pdf_image_cache'][$key])) {
				return $GLOBALS['armor_pdf_image_cache'][$key];
			}
		}

		$cacheFile = armor_pdf_image_cache_dir() . $key . '.jpg';
		if (is_file($cacheFile) && filesize($cacheFile) > 20) {
			$head = @file_get_contents($cacheFile, false, null, 0, 3);
			if ($head !== false && strlen($head) >= 2 && ord($head[0]) === 0xFF && ord($head[1]) === 0xD8) {
				$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
				return $cacheFile;
			}
		}

		$jpeg = '';
		if ($local !== '' && is_file($local)) {
			$ext = strtolower(pathinfo($local, PATHINFO_EXTENSION));
			$sz = @filesize($local);
			if ($ext === 'gif') {
				$siblingJpg = preg_replace('/\.gif$/i', '.jpg', $local);
				if ($siblingJpg !== $local && is_file($siblingJpg)) {
					$local = $siblingJpg;
					$ext = 'jpg';
					$sz = @filesize($local);
				} else {
					$GLOBALS['armor_pdf_image_cache'][$key] = '';
					return '';
				}
			}
			// Already-small JPEG: copy as-is (big live speed win on warm/cold).
			if (($ext === 'jpg' || $ext === 'jpeg') && $sz > 20 && $sz < 12000) {
				@copy($local, $cacheFile);
				if (is_file($cacheFile) && filesize($cacheFile) > 20) {
					$GLOBALS['armor_pdf_image_cache'][$key] = $cacheFile;
					return $cacheFile;
				}
			}
			$jpeg = armor_pdf_resize_to_jpeg_bytes_from_file($local, $maxW, $maxH, $quality);
		}
		if ($jpeg === '' || !armor_pdf_is_jpeg_bytes($jpeg)) {
			$bytes = armor_pdf_load_image_bytes($src);
			if ($bytes !== false && $bytes !== '') {
				$jpeg = armor_pdf_resize_to_jpeg_bytes($bytes, $maxW, $maxH, $quality);
				unset($bytes);
			}
		}
		// Header last-resort: craftbox
		if (($jpeg === '' || !armor_pdf_is_jpeg_bytes($jpeg)) && $isHeader) {
			$projectRoot = realpath(dirname(__FILE__) . '/..');
			$craft = $projectRoot ? ($projectRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'craftbox_header.jpg') : '';
			if ($craft !== '' && is_file($craft)) {
				$jpeg = armor_pdf_resize_to_jpeg_bytes_from_file($craft, $maxW, $maxH, $quality);
			}
		}

		if ($jpeg === '' || !armor_pdf_is_jpeg_bytes($jpeg)) {
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
		if (strpos($tag, 'quote-header') !== false || strpos($tag, 'craftbox_header') !== false || strpos($tag, 'view_logo') !== false || strpos($tag, 'quote-footer') !== false || strpos($tag, 'images/header') !== false) {
			// Match web print header (~933x184)
			return array(900, 170, 78);
		}
		if (strpos($tag, 'qp-prod') !== false || strpos($tag, '42px') !== false) {
			return array(42, 34, 70);
		}
		if (strpos($tag, 'image-width') !== false || strpos($tag, 'product') !== false || strpos($tag, 'width: 50px') !== false || strpos($tag, 'width:50px') !== false || strpos($tag, 'width: 80px') !== false || strpos($tag, 'width:80px') !== false) {
			return array(48, 48, 72);
		}
		return array(48, 48, 70);
	}
}

if (!function_exists('armor_pdf_blank_jpeg_data_uri')) {
	function armor_pdf_blank_jpeg_data_uri()
	{
		$file = armor_pdf_blank_jpeg_path();
		if ($file === '' || !is_file($file)) {
			// 1x1 JPEG (not GIF) — mPDF gif.php hangs on GIF decode.
			return 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAGcP//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAQUCf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQMBAT8Bf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQIBAT8Bf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEABj8Cf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAT8hf//Z';
		}
		$bytes = @file_get_contents($file);
		return ($bytes !== false && $bytes !== '') ? 'data:image/jpeg;base64,' . base64_encode($bytes) : 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAGcP//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAQUCf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQMBAT8Bf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQIBAT8Bf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEABj8Cf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAT8hf//Z';
	}

if (!function_exists('armor_pdf_force_jpeg_only_images')) {
	/**
	 * mPDF gif.php can hang 300s on GIF product images — only allow JPEG/data JPEG to mPDF.
	 */
	function armor_pdf_force_jpeg_only_images($html)
	{
		$blankSrc = 'var:armorpdf_blank';
		$blankFile = armor_pdf_blank_jpeg_path();
		if ($blankFile !== '' && is_file($blankFile)) {
			$GLOBALS['armor_pdf_mpdf_vars']['armorpdf_blank'] = @file_get_contents($blankFile);
		} else {
			$blankSrc = armor_pdf_blank_jpeg_data_uri();
		}

		return preg_replace_callback('/<img\b[^>]*>/i', function ($m) use ($blankSrc) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}
			$src = $srcMatch[2];
			if (strpos($src, 'var:') === 0) {
				return $tag;
			}
			if (strpos($src, 'data:image/jpeg') === 0 || strpos($src, 'data:image/jpg') === 0) {
				return $tag;
			}
			$isHeader = (stripos($tag, 'quote-header') !== false || stripos($tag, 'quote-footer') !== false);
			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $blankSrc . '"', $tag, 1);
			if ($isHeader) {
				$newTag = preg_replace('/\sstyle=(["\'])[^"\']*\1/i', '', $newTag);
				$newTag = preg_replace('/<img/i', '<img style="width:100%;max-height:170px;display:block;"', $newTag, 1);
			}
			return $newTag;
		}, (string) $html);
	}
}
}

if (!function_exists('armor_pdf_strip_remaining_remote_images')) {
	function armor_pdf_strip_remaining_remote_images($html)
	{
		$blankSrc = 'var:armorpdf_blank';
		$blankFile = armor_pdf_blank_jpeg_path();
		if ($blankFile !== '' && is_file($blankFile)) {
			if (empty($GLOBALS['armor_pdf_mpdf_vars']['armorpdf_blank'])) {
				$GLOBALS['armor_pdf_mpdf_vars']['armorpdf_blank'] = @file_get_contents($blankFile);
			}
		} else {
			$blankSrc = armor_pdf_blank_jpeg_data_uri();
		}
		return preg_replace_callback('/<img\b[^>]*>/i', function ($m) use ($blankSrc) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}
			$src = $srcMatch[2];
			if (strpos($src, 'var:') === 0 || strpos($src, 'data:image') === 0) {
				return $tag;
			}
			if (!preg_match('/^https?:\/\//i', $src)) {
				// Non-http leftover file path that is not jpeg → blank var
				$ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH) ? parse_url($src, PHP_URL_PATH) : $src, PATHINFO_EXTENSION));
				if ($ext === 'jpg' || $ext === 'jpeg') {
					return $tag;
				}
			}
			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $blankSrc . '"', $tag, 1);
			if (stripos($tag, 'quote-header') !== false || stripos($tag, 'quote-footer') !== false) {
				$newTag = preg_replace('/<img/i', '<img style="width:100%;max-height:170px;display:block;"', $newTag, 1);
			} else {
				$newTag = preg_replace('/<img/i', '<img style="max-width:48px;max-height:48px;"', $newTag, 1);
			}
			return $newTag;
		}, (string) $html);
	}
}

if (!function_exists('armor_pdf_compress_images_in_html')) {
	function armor_pdf_compress_images_in_html($html, $useLocalPaths = false)
	{
		@ini_set('pcre.backtrack_limit', '10000000');
		@ini_set('pcre.recursion_limit', '1000000');
		armor_pdf_image_cache_reset();
		if (!isset($GLOBALS['armor_pdf_mpdf_vars']) || !is_array($GLOBALS['armor_pdf_mpdf_vars'])) {
			$GLOBALS['armor_pdf_mpdf_vars'] = array();
		}
		if (!isset($GLOBALS['armor_pdf_mpdf_var_i'])) {
			$GLOBALS['armor_pdf_mpdf_var_i'] = 0;
		}

		$html = preg_replace_callback('/<img\b[^>]*>/i', function ($m) {
			$tag = $m[0];
			if (!preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $tag, $srcMatch)) {
				return $tag;
			}

			$src = $srcMatch[2];
			if (strpos($src, 'data:image') === 0 || strpos($src, 'var:') === 0) {
				return $tag;
			}

			list($maxW, $maxH, $quality) = armor_pdf_guess_image_limits($tag);
			$isHeader = (stripos($tag, 'quote-header') !== false || stripos($tag, 'quote-footer') !== false || stripos($tag, 'craftbox_header') !== false || stripos($src, 'images/header') !== false || stripos($src, 'craftbox_header') !== false);
			$mapKey = $src . '|' . $maxW . 'x' . $maxH;
			$localForKey = armor_pdf_resolve_local_image_path($src);
			if ($localForKey !== '') {
				$mapKey = $localForKey . '|' . $maxW . 'x' . $maxH;
			}

			if (!armor_pdf_is_valid_image_src($src)) {
				$filePath = armor_pdf_blank_jpeg_path();
			} elseif (isset($GLOBALS['armor_pdf_src_map'][$mapKey]) && is_file($GLOBALS['armor_pdf_src_map'][$mapKey])) {
				$filePath = $GLOBALS['armor_pdf_src_map'][$mapKey];
			} else {
				$filePath = armor_pdf_compress_image_src($src, $maxW, $maxH, $quality, $isHeader);
				if ($filePath !== '' && is_file($filePath)) {
					$GLOBALS['armor_pdf_src_map'][$mapKey] = $filePath;
				}
			}
			if ($filePath === '' || !is_file($filePath)) {
				$filePath = armor_pdf_blank_jpeg_path();
			}

			$jpegBytes = ($filePath !== '' && is_file($filePath)) ? @file_get_contents($filePath) : false;
			if (!armor_pdf_is_jpeg_bytes($jpegBytes)) {
				$filePath = armor_pdf_blank_jpeg_path();
				$jpegBytes = ($filePath !== '' && is_file($filePath)) ? @file_get_contents($filePath) : false;
			}
			if (!armor_pdf_is_jpeg_bytes($jpegBytes)) {
				return $tag;
			}

			// mPDF-native embedding: src="var:name" + $mpdf->name = jpeg bytes (reliable vs data-URI).
			// Reuse same var for identical JPEG bytes (many Suggested Products share default.png).
			if (!isset($GLOBALS['armor_pdf_var_by_hash']) || !is_array($GLOBALS['armor_pdf_var_by_hash'])) {
				$GLOBALS['armor_pdf_var_by_hash'] = array();
			}
			$hash = md5($jpegBytes);
			if (isset($GLOBALS['armor_pdf_var_by_hash'][$hash])) {
				$varName = $GLOBALS['armor_pdf_var_by_hash'][$hash];
			} else {
				$varName = 'armorpdf_' . ((int) $GLOBALS['armor_pdf_mpdf_var_i']++);
				$GLOBALS['armor_pdf_mpdf_vars'][$varName] = $jpegBytes;
				$GLOBALS['armor_pdf_var_by_hash'][$hash] = $varName;
			}
			$imgSrc = 'var:' . $varName;

			$newTag = preg_replace('/\bsrc=(["\'])([^"\']+)\1/i', 'src="' . $imgSrc . '"', $tag, 1);
			$newTag = preg_replace('/\sstyle=(["\'])[^"\']*\1/i', '', $newTag);
			if ($isHeader) {
				$newTag = preg_replace('/<img/i', '<img style="width:100%;max-width:100%;height:auto;max-height:170px;display:block;"', $newTag, 1);
			} else {
				$newTag = preg_replace('/<img/i', '<img style="max-width:' . $maxW . 'px;max-height:' . $maxH . 'px;"', $newTag, 1);
			}
			return $newTag;
		}, (string) $html);

		return $html;
	}
}

if (!function_exists('armor_pdf_apply_mpdf_image_vars')) {
	function armor_pdf_apply_mpdf_image_vars($mpdf)
	{
		if (!$mpdf || empty($GLOBALS['armor_pdf_mpdf_vars']) || !is_array($GLOBALS['armor_pdf_mpdf_vars'])) {
			return;
		}
		foreach ($GLOBALS['armor_pdf_mpdf_vars'] as $name => $bytes) {
			if ($name === '' || $bytes === '' || $bytes === false) {
				continue;
			}
			// mPDF6: <img src="var:name"> reads $mpdf->name
			$mpdf->{$name} = $bytes;
		}
	}
}
