<?php
/**
 * AJAX: scan / convert existing product images to WebP
 */
$page_id = 559;
$page_slug = 'page_product';
include("connect.php");
require_once("../include/image_webp_helper.php");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID'])) {
	echo json_encode(array('ack' => 0, 'message' => 'Unauthorized'));
	exit;
}

if (!armor_image_webp_supported()) {
	echo json_encode(array('ack' => 0, 'message' => 'WebP not supported on this server'));
	exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

function armor_product_image_needs_convert($imagePath)
{
	$imagePath = trim((string) $imagePath);
	if ($imagePath === '') {
		return false;
	}
	$ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
	if ($ext === 'webp') {
		return false;
	}
	if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'bmp'), true)) {
		return false;
	}
	$abs = PRODUCT_A . $imagePath;
	return is_file($abs);
}

if ($action === 'scan') {
	$total = 0;
	$already = 0;
	$pending = 0;
	$res = $db->rp_getData("product", "id,image_path", "isDelete=0 AND image_path IS NOT NULL AND image_path!=''", "", 0);
	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			$total++;
			$ext = strtolower(pathinfo($row['image_path'], PATHINFO_EXTENSION));
			if ($ext === 'webp') {
				$already++;
			} else if (armor_product_image_needs_convert($row['image_path'])) {
				$pending++;
			}
		}
	}
	echo json_encode(array(
		'ack' => 1,
		'total_with_image' => $total,
		'already_webp' => $already,
		'pending' => $pending,
	));
	exit;
}

if ($action === 'convert') {
	$limit = isset($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : 0;
	if ($limit < 0) {
		$limit = 0;
	}
	// Safety cap per request to avoid timeout (0 = up to 100)
	$maxPerRequest = ($limit > 0) ? $limit : 100;

	$converted = 0;
	$skipped = 0;
	$failed = 0;
	$details = array();

	$res = $db->rp_getData("product", "id,image_path,name", "isDelete=0 AND image_path IS NOT NULL AND image_path!=''", "id ASC", 0);
	$processed = 0;
	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			if (!armor_product_image_needs_convert($row['image_path'])) {
				continue;
			}
			if ($processed >= $maxPerRequest) {
				break;
			}
			$processed++;

			$old = $row['image_path'];
			$conv = armor_product_image_to_webp($old, 80);
			if (!empty($conv['ack']) && !empty($conv['image_path']) && $conv['image_path'] !== $old) {
				$newName = $db->clean($conv['image_path']);
				$db->rp_update("product", array('image_path' => $newName), "id='" . (int) $row['id'] . "'", 0);
				$converted++;
				$details[] = '#' . $row['id'] . ' ' . $old . ' => ' . $newName;
			} else if (!empty($conv['skipped'])) {
				$skipped++;
			} else {
				$failed++;
				$details[] = '#' . $row['id'] . ' FAIL ' . $old . ' (' . $conv['message'] . ')';
			}
		}
	}

	// recount remaining
	$remaining = 0;
	$res2 = $db->rp_getData("product", "id,image_path", "isDelete=0 AND image_path IS NOT NULL AND image_path!=''", "", 0);
	if ($res2) {
		while ($row2 = mysqli_fetch_assoc($res2)) {
			if (armor_product_image_needs_convert($row2['image_path'])) {
				$remaining++;
			}
		}
	}

	echo json_encode(array(
		'ack' => 1,
		'converted' => $converted,
		'skipped' => $skipped,
		'failed' => $failed,
		'remaining' => $remaining,
		'details' => $details,
		'message' => 'Batch complete',
	));
	exit;
}

/**
 * Fix DB rows where image_path points to missing WebP but JPG/PNG (or thumb) still exists
 * under images/product/ or images/product/thumb/. Restores DB filename to match disk.
 */
if ($action === 'repair_missing') {
	$fixed = 0;
	$restoredFromThumb = 0;
	$missing = 0;
	$ok = 0;
	$details = array();
	$abs = armor_product_image_abs_dirs();

	$res = $db->rp_getData("product", "id,image_path,name", "isDelete=0 AND image_path IS NOT NULL AND image_path!=''", "id ASC", 0);
	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			$old = trim($row['image_path']);
			$info = armor_product_resolve_image_info($old);

			if ($info['file'] !== '' && $info['subdir'] === '' && $info['file'] === $old) {
				$ok++;
				continue;
			}

			// Main folder has matching basename with real extension (usually .jpg)
			if ($info['file'] !== '' && $info['subdir'] === '' && $info['file'] !== $old) {
				$db->rp_update("product", array('image_path' => $db->clean($info['file'])), "id='" . (int) $row['id'] . "'", 0);
				$fixed++;
				$details[] = '#' . $row['id'] . ' ' . $old . ' => ' . $info['file'] . ' (product/)';
				continue;
			}

			// Only thumb has the file — copy back to main product/ so path is stable
			if ($info['file'] !== '' && $info['subdir'] === 'thumb/' && $abs['main'] !== '' && $abs['thumb'] !== '') {
				$src = $abs['thumb'] . $info['file'];
				$dest = $abs['main'] . $info['file'];
				if (is_file($src) && (!is_file($dest) || @filesize($dest) < 20)) {
					@copy($src, $dest);
				}
				if (is_file($dest) && @filesize($dest) > 20) {
					$db->rp_update("product", array('image_path' => $db->clean($info['file'])), "id='" . (int) $row['id'] . "'", 0);
					$restoredFromThumb++;
					$details[] = '#' . $row['id'] . ' ' . $old . ' => ' . $info['file'] . ' (from thumb/)';
					continue;
				}
				// Still show via thumb URL path in DB if copy failed — keep thumb filename
				$db->rp_update("product", array('image_path' => $db->clean($info['file'])), "id='" . (int) $row['id'] . "'", 0);
				$restoredFromThumb++;
				$details[] = '#' . $row['id'] . ' ' . $old . ' => ' . $info['file'] . ' (thumb only)';
				continue;
			}

			$missing++;
			if (count($details) < 60) {
				$details[] = '#' . $row['id'] . ' MISSING: ' . $old;
			}
		}
	}

	echo json_encode(array(
		'ack' => 1,
		'ok' => $ok,
		'fixed' => $fixed,
		'restored_from_thumb' => $restoredFromThumb,
		'missing' => $missing,
		'main_dir' => $abs['main'],
		'thumb_dir' => $abs['thumb'],
		'details' => $details,
		'message' => 'Repair complete — DB matched to files in product/ and product/thumb/',
	));
	exit;
}

echo json_encode(array('ack' => 0, 'message' => 'Invalid action'));
exit;
?>
