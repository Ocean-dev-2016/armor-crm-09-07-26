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
 * Returns array(ack, webp_filename, message, deleted_old)
 */
if (!function_exists('armor_convert_file_to_webp')) {
	function armor_convert_file_to_webp($absPath, $quality = 80, $deleteOriginal = true)
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

		if ($deleteOriginal && realpath($absPath) !== realpath($webpPath) && is_file($absPath)) {
			@unlink($absPath);
			$result['deleted_old'] = 1;
		}

		return $result;
	}
}

/**
 * Resolve a product image filename that actually exists under PRODUCT_A.
 * Handles DB pointing to .webp after convert while only .jpg/.png remains (or vice versa).
 */
if (!function_exists('armor_product_resolve_image_path')) {
	function armor_product_resolve_image_path($relativeName)
	{
		$relativeName = trim((string) $relativeName);
		if ($relativeName === '' || !defined('PRODUCT_A')) {
			return '';
		}

		$direct = PRODUCT_A . $relativeName;
		if (is_file($direct) && filesize($direct) > 20) {
			return $relativeName;
		}

		$base = pathinfo($relativeName, PATHINFO_FILENAME);
		if ($base === '') {
			return '';
		}

		$exts = array('webp', 'jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'WEBP', 'GIF');
		foreach ($exts as $ext) {
			$candidate = $base . '.' . $ext;
			$path = PRODUCT_A . $candidate;
			if (is_file($path) && filesize($path) > 20) {
				return $candidate;
			}
		}

		return '';
	}
}

/**
 * Public URL for product list/detail images (falls back to placeholder when missing).
 */
if (!function_exists('armor_product_public_image_url')) {
	function armor_product_public_image_url($relativeName, $placeholder = '')
	{
		$resolved = armor_product_resolve_image_path($relativeName);
		if ($resolved !== '' && defined('SITEURL') && defined('PRODUCT')) {
			return SITEURL . PRODUCT . $resolved;
		}
		if ($placeholder !== '') {
			return $placeholder;
		}
		if (defined('SITEURL')) {
			return SITEURL . 'images/no_data_found.jpg';
		}
		return '';
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
			$res = armor_convert_file_to_webp($mainSrc, $quality, true);
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
			$res = armor_convert_file_to_webp($path, $quality, true);
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
 * After upload: convert source file to webp + write thumb webp.
 * Returns webp filename for DB.
 */
if (!function_exists('armor_product_process_uploaded_image')) {
	function armor_product_process_uploaded_image($absUploadedPath, $preferredBaseName = '', $quality = 80)
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

		$dir = dirname($absUploadedPath);
		$base = $preferredBaseName !== '' ? $preferredBaseName : pathinfo($absUploadedPath, PATHINFO_FILENAME);
		$base = preg_replace('/[^a-zA-Z0-9_\-]/', '', $base);
		if ($base === '') {
			$base = 'image_' . substr(sha1(uniqid((string) mt_rand(), true)), 0, 8);
		}

		$webpName = $base . '.webp';
		$webpPath = $dir . DIRECTORY_SEPARATOR . $webpName;

		if (!armor_image_webp_supported()) {
			// Keep original if webp unavailable
			$out['ack'] = 1;
			$out['image_path'] = basename($absUploadedPath);
			$out['message'] = 'WebP unavailable, kept original';
			return $out;
		}

		$img = armor_image_load_gd($absUploadedPath);
		if (!$img) {
			$out['ack'] = 1;
			$out['image_path'] = basename($absUploadedPath);
			$out['message'] = 'Could not decode image, kept original';
			return $out;
		}

		if (!armor_image_save_webp($img, $webpPath, $quality)) {
			@imagedestroy($img);
			$out['ack'] = 1;
			$out['image_path'] = basename($absUploadedPath);
			$out['message'] = 'WebP write failed, kept original';
			return $out;
		}

		// Also create/overwrite thumb as webp
		if (defined('PRODUCT_THUMB_A')) {
			$thumbPath = PRODUCT_THUMB_A . $webpName;
			$w = imagesx($img);
			$h = imagesy($img);
			$maxW = 400;
			$maxH = 400;
			$ratio = min($maxW / max($w, 1), $maxH / max($h, 1), 1);
			$nw = max(1, (int) round($w * $ratio));
			$nh = max(1, (int) round($h * $ratio));
			$thumb = imagecreatetruecolor($nw, $nh);
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
			$transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
			imagefilledrectangle($thumb, 0, 0, $nw, $nh, $transparent);
			imagecopyresampled($thumb, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
			armor_image_save_webp($thumb, $thumbPath, $quality);
			@imagedestroy($thumb);
		}

		if (defined('PRODUCT_THUMB_SMALL_A')) {
			$smallPath = PRODUCT_THUMB_SMALL_A . $webpName;
			$w = imagesx($img);
			$h = imagesy($img);
			$maxW = 120;
			$maxH = 120;
			$ratio = min($maxW / max($w, 1), $maxH / max($h, 1), 1);
			$nw = max(1, (int) round($w * $ratio));
			$nh = max(1, (int) round($h * $ratio));
			$small = imagecreatetruecolor($nw, $nh);
			imagealphablending($small, false);
			imagesavealpha($small, true);
			$transparent = imagecolorallocatealpha($small, 0, 0, 0, 127);
			imagefilledrectangle($small, 0, 0, $nw, $nh, $transparent);
			imagecopyresampled($small, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
			armor_image_save_webp($small, $smallPath, $quality);
			@imagedestroy($small);
		}

		@imagedestroy($img);

		// Remove original non-webp upload if different
		if (realpath($absUploadedPath) !== realpath($webpPath) && is_file($absUploadedPath)) {
			@unlink($absUploadedPath);
		}

		$out['ack'] = 1;
		$out['image_path'] = $webpName;
		$out['message'] = 'Converted to WebP';
		return $out;
	}
}
?>
