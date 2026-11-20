<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$sid = isset($_POST["sid"]) ? $_POST["sid"] : "";
$city = isset($_POST["city"]) ? $_POST["city"] : "";

$state_id = array();
$stateArr = explode(",", $sid);
foreach ($stateArr as $value) {
	$value = trim($value);
	if ($value === "") {
		continue;
	}
	$safe = mysqli_real_escape_string($db->myconn, $value);
	$found_id = $db->rp_getValue("class", "id", "name = '" . $safe . "' AND isDelete=0", 0);
	if ($found_id === "" || $found_id === null || $found_id === false) {
		$found_id = $db->rp_getValue("class", "id", "name LIKE '" . $safe . "' AND isDelete=0", 0);
	}
	if ($found_id !== "" && $found_id !== null && $found_id !== false) {
		$state_id[] = (int) $found_id;
	}
}

echo '<option value="">Select City</option>';
if (!empty($state_id)) {
	$city_r = $db->rp_getData("city", "*", "state_id IN (" . implode(",", $state_id) . ") AND isDelete=0", "name ASC", 0);
	if ($city_r) {
		while ($city_d = mysqli_fetch_assoc($city_r)) {
			$selected = (strtolower($city_d['name']) == strtolower($city)) ? "selected" : "";
			echo '<option ' . $selected . ' value="' . htmlspecialchars($city_d['name'], ENT_QUOTES) . '">' . htmlspecialchars($city_d['name']) . '</option>';
		}
	}
}
require_once("disconnect.php");
?>

