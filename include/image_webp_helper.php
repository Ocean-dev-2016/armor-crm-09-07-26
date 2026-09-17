<?php
/**
 * Product image -> WebP converter (PHP 5.6 + GD)
 * Converts jpg/png/gif to webp and keeps thumb/small copies in sync.
 */
if (!function_exists('armor_image_webp_supported')) {
	function armor_image_webp_supported()
	{
		return function_exists('imagewebp') && function_exists('imagecreatefromstring');
	}
}

if (!function_exists('armor_image_load_gd')) {
	function armor_image_load_gd($path)
	{
		if (!is_file($path) || !is_readable($path)) {
			return false;
		}
		$data = @file_get_contents($path);
		if ($data === false || $data === '') {
			return false;
		}
		$img = @imagecreatefromstring($data);
		if (!$img) {
			return false;
		}
		// Preserve alpha for PNG/GIF
		@imagealphablending($img, true);
		@imagesavealpha($img, true);
		return $img;
	}
}

if (!function_exists('armor_image_save_webp')) {
	function armor_image_save_webp($gdImage, $destPath, $quality = 80)
	{
		if (!$gdImage || !armor_image_webp_supported()) {
			return false;
		}
		$dir = dirname($destPath);
		if (!is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
		$quality = (int) $quality;
		if ($quality < 1) {
			$quality = 1;
		}
		if ($quality > 100) {
			$quality = 100;
		}
		$ok = @imagewebp($gdImage, $destPath, $quality);
		return ($ok && is_file($destPath));
	}
}

/**
 * Convert a single image file to WebP.
 * KEEP original by default (deleteOriginal=false) so JPG remains if WebP is later missing.
 * Returns array(ack, webp_filename, message, deleted_old)
 */
if (!function_exists('armor_convert_file_to_webp')) {
	function armor_convert_file_to_webp($absPath, $quality = 80, $deleteOriginal = false)
	{
		$result = array(
			'ack' => 0,
			'webp_filename' => '',
			'message' => '',
			'deleted_old' => 0,
			'skipped' => 0,
		);

		if (!armor_image_webp_supported()) {
			$result['message'] = 'WebP not supported on this PHP/GD build';
			return $result;
		}
		if (!is_file($absPath)) {
			$result['message'] = 'File not found';
			return $result;
		}

		$ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
		$base = pathinfo($absPath, PATHINFO_FILENAME);
		$dir = dirname($absPath);
		$webpName = $base . '.webp';
		$webpPath = $dir . DIRECTORY_SEPARATOR . $webpName;

		if ($ext === 'webp') {
			$result['ack'] = 1;
			$result['skipped'] = 1;
			$result['webp_filename'] = basename($absPath);
			$result['message'] = 'Already WebP';
			return $result;
		}

		if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'bmp'), true)) {
			$result['message'] = 'Unsupported extension: ' . $ext;
			return $result;
		}

		$img = armor_image_load_gd($absPath);
		if (!$img) {
			$result['message'] = 'Unable to read image';
			return $result;
		}

		if (!armor_image_save_webp($img, $webpPath, $quality)) {
			@imagedestroy($img);
			$result['message'] = 'Failed to write WebP';
			return $result;
		}
		@imagedestroy($img);

		$result['ack'] = 1;
		$result['webp_filename'] = $webpName;
		$result['message'] = 'Converted';

		// Only delete original when explicitly requested AND webp file is valid.
		if ($deleteOriginal && is_file($webpPath) && filesize($webpPath) > 20
			&& realpath($absPath) !== realpath($webpPath) && is_file($absPath)) {
			@unlink($absPath);
			$result['deleted_old'] = 1;
		}

		return $result;
	}
}

/**
 * Absolute product image directories (CWD-safe).
 * Tries project root AND DOCUMENT_ROOT (live often serves /images from docroot).
 */
if (!function_exists('armor_product_image_abs_dirs')) {
	function armor_product_image_abs_dirs()
	{
		static $dirs = null;
		if ($dirs !== null) {
			return $dirs;
		}

		$candidates = array();
		$root = realpath(dirname(__FILE__) . '/..');
		if ($root) {
			$candidates[] = $root;
		}
		// bbsales_tracking/../..
		$root2 = realpath(dirname(__FILE__) . '/../..');
		if ($root2) {
			$candidates[] = $root2;
		}
		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
			$doc = realpath($_SERVER['DOCUMENT_ROOT']);
			if ($doc) {
				$candidates[] = $doc;
			}
		}
		if (defined('PRODUCT_A')) {
			$rp = @realpath(PRODUCT_A);
			if ($rp) {
				$candidates[] = dirname($rp);
			}
		}

		$main = '';
		$thumb = '';
		foreach ($candidates as $baseRoot) {
			$mainCand = $baseRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'product';
			if (is_dir($mainCand)) {
				// Prefer a folder that actually contains image files
				$hasFiles = (bool) glob($mainCand . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
				if ($main === '' || $hasFiles) {
					$main = $mainCand . DIRECTORY_SEPARATOR;
					$thumbCand = $mainCand . DIRECTORY_SEPARATOR . 'thumb';
					$thumb = is_dir($thumbCand) ? ($thumbCand . DIRECTORY_SEPARATOR) : '';
					if ($hasFiles) {
						break;
					}
				}
			}
		}

		if ($main === '' && defined('PRODUCT_A')) {
			$rp = @realpath(PRODUCT_A);
			$main = $rp ? (rtrim($rp, '/\\') . DIRECTORY_SEPARATOR) : PRODUCT_A;
		}
		if ($thumb === '' && defined('PRODUCT_THUMB_A')) {
			$rp = @realpath(PRODUCT_THUMB_A);
			$thumb = $rp ? (rtrim($rp, '/\\') . DIRECTORY_SEPARATOR) : PRODUCT_THUMB_A;
		}

		$dirs = array(
			'main' => $main,
			'thumb' => $thumb,
		);
		return $dirs;
	}
}

/**
 * Find an existing product image file for a DB filename.
 * Searches BOTH formats: jpg/jpeg/png/gif AND webp
 * in BOTH folders: images/product/ and images/product/thumb/
 * Returns first match found (main preferred, then thumb; jpg preferred, then webp).
 * Returns array('file' => 'image_x.jpg', 'subdir' => ''|'thumb/', 'kind' => 'jpg'|'webp'|...)
 */
if (!function_exists('armor_product_resolve_image_info')) {
	function armor_product_resolve_image_info($relativeName)
	{
		$empty = array('file' => '', 'subdir' => '', 'kind' => '');
		$relativeName = trim((string) $relativeName);
		if ($relativeName === '') {
			return $empty;
		}

		$base = pathinfo($relativeName, PATHINFO_FILENAME);
		if ($base === '') {
			return $empty;
		}

		// Both JPG family + WebP — whichever exists can be recovered
		$exts = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP');
		$abs = armor_product_image_abs_dirs();

		$locations = array();
		if ($abs['main'] !== '') {
			$locations[] = array('dir' => $abs['main'], 'subdir' => '');
		}
		if ($abs['thumb'] !== '') {
			$locations[] = array('dir' => $abs['thumb'], 'subdir' => 'thumb/');
		}

		$makeResult = function ($file, $subdir) {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			$kind = ($ext === 'jpeg') ? 'jpg' : $ext;
			return array('file' => $file, 'subdir' => $subdir, 'kind' => $kind);
		};

		// 1) Exact DB filename (webp OR jpg) in main, then thumb
		foreach ($locations as $loc) {
			$path = $loc['dir'] . $relativeName;
			if (is_file($path) && @filesize($path) > 20) {
				return $makeResult($relativeName, $loc['subdir']);
			}
		}

		// 2) Same basename — try JPG first, then WebP, in main then thumb
		foreach ($locations as $loc) {
			foreach ($exts as $ext) {
				$candidate = $base . '.' . $ext;
				$path = $loc['dir'] . $candidate;
				if (is_file($path) && @filesize($path) > 20) {
					return $makeResult($candidate, $loc['subdir']);
				}
			}
		}

		return $empty;
	}
}

/**
 * Resolve a product image filename that actually exists under PRODUCT_A (main only).
 */
if (!function_exists('armor_product_resolve_image_path')) {
	function armor_product_resolve_image_path($relativeName)
	{
		$info = armor_product_resolve_image_info($relativeName);
		if ($info['file'] === '') {
			return '';
		}
		// If only thumb exists, still return filename (repair may copy it to main)
		return $info['file'];
	}
}

/**
 * Ordered public image URLs to try (JPG first — many live .webp 404; then webp).
 */
if (!function_exists('armor_product_image_candidate_urls')) {
	function armor_product_image_candidate_urls($relativeName)
	{
		$urls = array();
		$relativeName = trim((string) $relativeName);
		if ($relativeName === '' || !defined('SITEURL') || !defined('PRODUCT')) {
			return $urls;
		}

		$relativeName = str_replace('\\', '/', $relativeName);
		if (strpos($relativeName, 'images/product/') !== false) {
			$relativeName = substr($relativeName, strrpos($relativeName, '/') + 1);
		}
		$relativeName = ltrim($relativeName, '/');
		$base = pathinfo($relativeName, PATHINFO_FILENAME);
		if ($base === '') {
			return $urls;
		}

		$push = function ($url) use (&$urls) {
			if ($url !== '' && !in_array($url, $urls, true)) {
				$urls[] = $url;
			}
		};

		$info = armor_product_resolve_image_info($relativeName);

		// 1) JPG/PNG FIRST (other PCs get WebP 404; folder still has JPG)
		foreach (array('jpg', 'jpeg', 'png', 'gif') as $ext) {
			$push(SITEURL . PRODUCT . $base . '.' . $ext);
		}
		foreach (array('jpg', 'jpeg', 'png', 'gif') as $ext) {
			$push(SITEURL . PRODUCT . 'thumb/' . $base . '.' . $ext);
		}

		// 2) Disk-resolved non-webp (if PHP can see a jpg)
		if ($info['file'] !== '' && isset($info['kind']) && $info['kind'] !== 'webp') {
			$push(SITEURL . PRODUCT . $info['subdir'] . $info['file']);
		}

		// 3) WebP last among real images
		$push(SITEURL . PRODUCT . $base . '.webp');
		$push(SITEURL . PRODUCT . 'thumb/' . $base . '.webp');
		if ($info['file'] !== '' && isset($info['kind']) && $info['kind'] === 'webp') {
			$push(SITEURL . PRODUCT . $info['subdir'] . $info['file']);
		}

		// 4) Exact DB filename (may be webp)
		$push(SITEURL . PRODUCT . $relativeName);

		$push(SITEURL . 'images/no_image_found.jpg');
		$push(SITEURL . 'images/no_data_found.jpg');

		return $urls;
	}
}

/**
 * Public URL for product list/detail images.
 * Prefers JPG when available in candidate list; always returns a usable URL if DB has a path.
 */
if (!function_exists('armor_product_public_image_url')) {
	function armor_product_public_image_url($relativeName, $placeholder = '')
	{
		$relativeName = trim((string) $relativeName);
		if ($relativeName === '') {
			if ($placeholder !== '') {
				return $placeholder;
			}
			return defined('SITEURL') ? (SITEURL . 'images/no_image_found.jpg') : '';
		}

		$cands = armor_product_image_candidate_urls($relativeName);
		if (!empty($cands)) {
			return $cands[0];
		}

		if ($placeholder !== '') {
			return $placeholder;
		}
		if (defined('SITEURL') && defined('PRODUCT') && preg_match('/\.(jpe?g|png|gif|webp)$/i', $relativeName)) {
			return SITEURL . PRODUCT . ltrim($relativeName, '/');
		}
		return defined('SITEURL') ? (SITEURL . 'images/no_image_found.jpg') : '';
	}
}

/**
 * <img> tag with automatic JPG/WebP/fallback chain (fixes client 404 on missing webp).
 */
if (!function_exists('armor_product_img_tag')) {
	function armor_product_img_tag($relativeName, $style = 'width:80px;height:80px;object-fit:contain;', $extraAttr = '')
	{
		$cands = armor_product_image_candidate_urls($relativeName);
		if (empty($cands)) {
			$src = defined('SITEURL') ? (SITEURL . 'images/no_image_found.jpg') : '';
			return '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '" ' . $extraAttr . ' />';
		}

		$primary = array_shift($cands);
		$json = htmlspecialchars(json_encode(array_values($cands)), ENT_QUOTES, 'UTF-8');
		$onerror = 'var a=this.getAttribute(\'data-armor-fallbacks\');if(!a){this.onerror=null;return;}var L=[];try{L=JSON.parse(a);}catch(e){this.onerror=null;return;}var i=parseInt(this.getAttribute(\'data-armor-i\')||\'0\',10);if(i>=L.length){this.onerror=null;return;}this.setAttribute(\'data-armor-i\',String(i+1));this.src=L[i];';

		return '<img src="' . htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') . '"'
			. ' style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '"'
			. ' data-armor-fallbacks="' . $json . '"'
			. ' data-armor-i="0"'
			. ' onerror="' . $onerror . '"'
			. ' ' . $extraAttr . ' />';
	}
}

/**
 * Convert product main/thumb/small images for one filename stored in DB.
 * DB may only change when the MAIN product file is successfully available as .webp.
 * Returns new filename (webp) or original on failure.
 */
if (!function_exists('armor_product_image_to_webp')) {
	function armor_product_image_to_webp($relativeName, $quality = 80)
	{
		$relativeName = trim((string) $relativeName);
		if ($relativeName === '') {
			return array('ack' => 0, 'image_path' => '', 'message' => 'Empty image path');
		}
		if (!defined('PRODUCT_A')) {
			return array('ack' => 0, 'image_path' => $relativeName, 'message' => 'PRODUCT_A not defined');
		}

		$base = pathinfo($relativeName, PATHINFO_FILENAME);
		$webpName = $base . '.webp';
		$mainSrc = PRODUCT_A . $relativeName;
		$mainWebp = PRODUCT_A . $webpName;
		$messages = array();

		// Already webp on main disk
		if (strtolower(pathinfo($relativeName, PATHINFO_EXTENSION)) === 'webp' && is_file($mainSrc) && filesize($mainSrc) > 20) {
			return array('ack' => 1, 'image_path' => $relativeName, 'message' => 'Already WebP', 'skipped' => 1);
		}

		$mainOk = false;
		if (is_file($mainSrc)) {
			// Keep JPG/PNG original in folder — never delete after convert.
			$res = armor_convert_file_to_webp($mainSrc, $quality, false);
			$messages[] = 'main: ' . $res['message'];
			if (!empty($res['ack']) && !empty($res['webp_filename']) && is_file(PRODUCT_A . $res['webp_filename']) && filesize(PRODUCT_A . $res['webp_filename']) > 20) {
				$webpName = $res['webp_filename'];
				$mainWebp = PRODUCT_A . $webpName;
				$mainOk = true;
			}
		} elseif (is_file($mainWebp) && filesize($mainWebp) > 20) {
			$mainOk = true;
			$messages[] = 'main: already had webp file';
		} else {
			$messages[] = 'main: source file missing';
		}

		// Never report success (for DB update) unless MAIN webp exists.
		if (!$mainOk || !is_file($mainWebp) || filesize($mainWebp) <= 20) {
			return array(
				'ack' => 0,
				'image_path' => $relativeName,
				'message' => implode(' | ', $messages) . ' | refused DB update (main webp missing)',
			);
		}

		// Sync thumb/small when present (best-effort; does not affect DB ack)
		$extraDirs = array();
		if (defined('PRODUCT_THUMB_A')) {
			$extraDirs[] = PRODUCT_THUMB_A;
		}
		if (defined('PRODUCT_THUMB_SMALL_A')) {
			$extraDirs[] = PRODUCT_THUMB_SMALL_A;
		}
		foreach ($extraDirs as $dir) {
			$path = $dir . $relativeName;
			if (!is_file($path)) {
				// also try converting already-renamed webp sibling nothing to do
				continue;
			}
			$res = armor_convert_file_to_webp($path, $quality, false);
			$messages[] = basename(rtrim($dir, '/\\')) . ': ' . $res['message'];
		}

		return array(
			'ack' => 1,
			'image_path' => $webpName,
			'message' => implode(' | ', $messages),
		);
	}
}

/**
 * Resize GD image if larger than maxW/maxH. Returns (possibly new) GD resource.
 */
if (!function_exists('armor_gd_resize_max')) {
	function armor_gd_resize_max($img, $maxW = 1600, $maxH = 1600)
	{
		if (!$img) {
			return false;
		}
		$w = imagesx($img);
		$h = imagesy($img);
		if ($w < 1 || $h < 1) {
			return $img;
		}
		$ratio = min($maxW / $w, $maxH / $h, 1);
		if ($ratio >= 1) {
			return $img;
		}
		$nw = max(1, (int) round($w * $ratio));
		$nh = max(1, (int) round($h * $ratio));
		$dst = imagecreatetruecolor($nw, $nh);
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
		$transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
		imagefilledrectangle($dst, 0, 0, $nw, $nh, $transparent);
		imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
		@imagedestroy($img);
		return $dst;
	}
}

/**
 * After upload: resize + compress + convert to WebP (DB stores .webp).
 * Also keeps a compressed .jpg fallback in same folder for recovery.
 * Default quality ~50 for noticeable compression.
 */
if (!function_exists('armor_product_process_uploaded_image')) {
	function armor_product_process_uploaded_image($absUploadedPath, $preferredBaseName = '', $quality = 50)
	{
		$out = array(
			'ack' => 0,
			'image_path' => '',
			'message' => '',
		);

		if (!is_file($absUploadedPath)) {
			$out['message'] = 'Upload file missing';
			return $out;
		}

		$absDirs = function_exists('armor_product_image_abs_dirs') ? armor_product_image_abs_dirs() : array('main' => '', 'thumb' => '');
		$dir = dirname($absUploadedPath);
		if ($absDirs['main'] !== '') {
			$dir = rtrim($absDirs['main'], '/\\');
		}

		$base = $preferredBaseName !== '' ? $preferredBaseName : pathinfo($absUploadedPath, PATHINFO_FILENAME);
		$base = preg_replace('/[^a-zA-Z0-9_\-]/', '', $base);
		if ($base === '') {
			$base = 'image_' . substr(sha1(uniqid((string) mt_rand(), true)), 0, 8);
		}

		$webpName = $base . '.webp';
		$jpgName = $base . '.jpg';
		$webpPath = $dir . DIRECTORY_SEPARATOR . $webpName;
		$jpgPath = $dir . DIRECTORY_SEPARATOR . $jpgName;

		$img = armor_image_load_gd($absUploadedPath);
		if (!$img) {
			$out['ack'] = 1;
			$out['image_path'] = basename($absUploadedPath);
			$out['message'] = 'Could not decode image, kept original';
			return $out;
		}

		// Compress size: max 1600px on longest side
		$img = armor_gd_resize_max($img, 1600, 1600);
		if (!$img) {
			$out['ack'] = 1;
			$out['image_path'] = basename($absUploadedPath);
			$out['message'] = 'Resize failed, kept original';
			return $out;
		}

		$quality = (int) $quality;
		if ($quality < 30) {
			$quality = 30;
		}
		if ($quality > 85) {
			$quality = 85;
		}

		$webpOk = false;
		if (armor_image_webp_supported()) {
			$webpOk = armor_image_save_webp($img, $webpPath, $quality);
		}

		// Always write compressed JPG fallback (recovery + servers without webp)
		$jpgOk = false;
		if (function_exists('imagejpeg')) {
			$jpgOk = @imagejpeg($img, $jpgPath, max(40, min(75, $quality + 10)));
		}

		// Thumb / small as webp (or jpg if webp unavailable)
		$thumbDir = ($absDirs['thumb'] !== '') ? rtrim($absDirs['thumb'], '/\\') : (defined('PRODUCT_THUMB_A') ? rtrim(PRODUCT_THUMB_A, '/\\') : '');
		$smallDir = defined('PRODUCT_THUMB_SMALL_A') ? rtrim(PRODUCT_THUMB_SMALL_A, '/\\') : '';
		if ($thumbDir === '' && defined('PRODUCT_THUMB_A')) {
			$rp = @realpath(PRODUCT_THUMB_A);
			$thumbDir = $rp ? $rp : rtrim(PRODUCT_THUMB_A, '/\\');
		}

		$makeThumb = function ($srcImg, $destDir, $fileName, $maxW, $maxH, $q) use ($webpOk) {
			if ($destDir === '' || !$srcImg) {
				return;
			}
			if (!is_dir($destDir)) {
				@mkdir($destDir, 0777, true);
			}
			$w = imagesx($srcImg);
			$h = imagesy($srcImg);
			$ratio = min($maxW / max($w, 1), $maxH / max($h, 1), 1);
			$nw = max(1, (int) round($w * $ratio));
			$nh = max(1, (int) round($h * $ratio));
			$thumb = imagecreatetruecolor($nw, $nh);
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
			$transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
			imagefilledrectangle($thumb, 0, 0, $nw, $nh, $transparent);
			imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $nw, $nh, $w, $h);
			$dest = $destDir . DIRECTORY_SEPARATOR . $fileName;
			if (armor_image_webp_supported() && preg_match('/\.webp$/i', $fileName)) {
				armor_image_save_webp($thumb, $dest, $q);
			} else {
				@imagejpeg($thumb, preg_replace('/\.webp$/i', '.jpg', $dest), max(40, min(75, $q + 10)));
			}
			@imagedestroy($thumb);
		};

		if ($webpOk) {
			$makeThumb($img, $thumbDir, $webpName, 400, 400, $quality);
			if ($smallDir !== '') {
				$makeThumb($img, $smallDir, $webpName, 120, 120, $quality);
			}
		} elseif ($jpgOk) {
			$makeThumb($img, $thumbDir, $jpgName, 400, 400, $quality);
			if ($smallDir !== '') {
				$makeThumb($img, $smallDir, $jpgName, 120, 120, $quality);
			}
		}

		@imagedestroy($img);

		// Remove original upload if it was a different heavy file (e.g. huge png) and we have webp/jpg
		$origReal = realpath($absUploadedPath);
		$webpReal = is_file($webpPath) ? realpath($webpPath) : false;
		$jpgReal = is_file($jpgPath) ? realpath($jpgPath) : false;
		if ($origReal && (($webpOk && $webpReal && $origReal !== $webpReal) || ($jpgOk && $jpgReal && $origReal !== $jpgReal))) {
			// Keep compressed jpg; delete only if original path differs from jpgPath
			if ($jpgReal && $origReal !== $jpgReal) {
				@unlink($absUploadedPath);
			} elseif ($webpReal && $origReal !== $webpReal && !$jpgOk) {
				@unlink($absUploadedPath);
			}
		}

		if ($webpOk && is_file($webpPath) && filesize($webpPath) > 20) {
			$out['ack'] = 1;
			$out['image_path'] = $webpName;
			$out['message'] = 'Compressed + converted to WebP (jpg fallback kept)';
			return $out;
		}

		if ($jpgOk && is_file($jpgPath) && filesize($jpgPath) > 20) {
			$out['ack'] = 1;
			$out['image_path'] = $jpgName;
			$out['message'] = 'WebP unavailable — saved compressed JPG';
			return $out;
		}

		$out['ack'] = 1;
		$out['image_path'] = basename($absUploadedPath);
		$out['message'] = 'Compress/WebP failed, kept original upload';
		return $out;
	}
}
?>
