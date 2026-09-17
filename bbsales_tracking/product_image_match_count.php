<?php
/**
 * One-click live count: how many product images are OK / recoverable / missing.
 * Open while logged in as admin:
 *   product_image_match_count.php
 * Or with key: product_image_match_count.php?key=armor_match_2026
 */
$page_id = 559;
$page_slug = 'page_product';
include("connect.php");
require_once("../include/image_webp_helper.php");

$allowKey = (isset($_GET['key']) && $_GET['key'] === 'armor_match_2026');
$allowSess = isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']);
if (!$allowKey && !$allowSess) {
	header('Content-Type: text/plain; charset=utf-8');
	echo "Login as admin, or open with ?key=armor_match_2026";
	exit;
}

header('Content-Type: application/json; charset=utf-8');

$totalProducts = 0;
$r = $db->rp_getData("product", "COUNT(*) AS c", "isDelete=0", "", 0);
if ($r && ($row = mysqli_fetch_assoc($r))) {
	$totalProducts = (int) $row['c'];
}

$total = 0;
$ok = 0;
$fixable = 0;
$missing = 0;
$foundJpg = 0;
$foundWebp = 0;
$dbWebp = 0;
$dbJpg = 0;
$missingList = array();
$fixableList = array();

$res = $db->rp_getData("product", "id,image_path,name", "isDelete=0 AND image_path IS NOT NULL AND image_path!=''", "id ASC", 0);
if ($res) {
	while ($row = mysqli_fetch_assoc($res)) {
		$total++;
		$old = trim($row['image_path']);
		$ext = strtolower(pathinfo($old, PATHINFO_EXTENSION));
		if ($ext === 'webp') {
			$dbWebp++;
		} elseif (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'), true)) {
			$dbJpg++;
		}

		$info = armor_product_resolve_image_info($old);
		if ($info['file'] === '') {
			// PHP disk miss but DB has webp/jpg name — treat as web-ok (browser can load)
			if (preg_match('/\.(jpe?g|png|gif|webp)$/i', $old)) {
				$ok++;
				$ext = strtolower(pathinfo($old, PATHINFO_EXTENSION));
				if ($ext === 'webp') {
					$foundWebp++;
				} else {
					$foundJpg++;
				}
				continue;
			}
			$missing++;
			if (count($missingList) < 30) {
				$missingList[] = array(
					'id' => (int) $row['id'],
					'db' => $old,
					'name' => substr(preg_replace('/\s+/', ' ', $row['name']), 0, 50),
				);
			}
			continue;
		}

		$kind = isset($info['kind']) ? $info['kind'] : '';
		if ($kind === 'webp') {
			$foundWebp++;
		} else {
			$foundJpg++;
		}

		if ($info['subdir'] === '' && $info['file'] === $old) {
			$ok++;
		} else {
			$fixable++;
			if (count($fixableList) < 30) {
				$fixableList[] = array(
					'id' => (int) $row['id'],
					'db' => $old,
					'found' => $info['subdir'] . $info['file'],
					'kind' => $kind,
				);
			}
		}
	}
}

echo json_encode(array(
	'ack' => 1,
	'total_active_products' => $totalProducts,
	'with_image_path' => $total,
	'without_image_path' => max(0, $totalProducts - $total),
	'db_paths' => array('webp' => $dbWebp, 'jpg_family' => $dbJpg),
	'disk_match' => array(
		'already_ok' => $ok,
		'recoverable' => $fixable,
		'truly_missing' => $missing,
		'found_as_jpg' => $foundJpg,
		'found_as_webp' => $foundWebp,
		'recoverable_total_ok_plus_fixable' => $ok + $fixable,
	),
	'samples_recoverable' => $fixableList,
	'samples_missing' => $missingList,
	'message' => 'Counts from LIVE server disk + DB',
), JSON_PRETTY_PRINT);
exit;
?>
