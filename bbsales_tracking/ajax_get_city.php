<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid = isset($_POST["sid"]) ? $_POST["sid"] : "";
$city = isset($_POST["city"]) ? $_POST["city"] : "";

$city_id = array();
$cityArr = explode(",", $sid);
foreach ($cityArr as $value) {
	$value = trim($value);
	if ($value === "") {
		continue;
	}
	$safe = mysqli_real_escape_string($db->myconn, $value);
	$found_id = $db->rp_getValue("city", "id", "name = '" . $safe . "' AND isDelete=0", 0);
	if ($found_id === "" || $found_id === null || $found_id === false) {
		$found_id = $db->rp_getValue("city", "id", "name LIKE '" . $safe . "' AND isDelete=0", 0);
	}
	if ($found_id !== "" && $found_id !== null && $found_id !== false) {
		$city_id[] = (int) $found_id;
	}
}

if ($city == "" || empty($city)) {
	$city = $sid;
}

echo '<option value="">Select City</option>';
if (!empty($city_id)) {
	$city_r = $db->rp_getData("area", "*", "city_id IN (" . implode(",", $city_id) . ") AND isDelete=0", "name ASC", 0);
	if ($city_r) {
		while ($city_d = mysqli_fetch_assoc($city_r)) {
			$selected = (strtolower($city_d['name']) == strtolower($city)) ? "selected" : "";
			echo '<option ' . $selected . ' value="' . htmlspecialchars($city_d['name'], ENT_QUOTES) . '" data-city_id="' . (int) $city_d['id'] . '">' . htmlspecialchars($city_d['name']) . '</option>';
		}
	}
}
require_once("disconnect.php");
?>
