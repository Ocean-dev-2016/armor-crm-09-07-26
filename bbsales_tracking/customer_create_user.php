<?php
$page_id = 400;
$page_slug = 'dashboard';
include("connect.php");
$id = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";

if ($id != "") {
	$user_count = $db->rp_getTotalRecord("dealer_distributor_network", "customer_id='" . $id . "' AND isDelete=0", 0);
	if ($user_count == '0') {
		$sales_DataR = $db->rp_getData("executive", "*", "id='" . $id . "' AND isDelete=0 AND isActive=1", "", 0);
		if (!$sales_DataR || mysqli_num_rows($sales_DataR) == 0) {
			$result = array("ack" => 0, "ack_msg" => "Customer not found or inactive. Please activate customer first.");
			echo '{"data":' . json_encode($result) . '}';
			exit;
		}

		$sales_DataD = mysqli_fetch_array($sales_DataR);
		$adate = date('Y-m-d H:i:s');

		$loginUsername = trim($sales_DataD['mobile_no1']);
		if ($loginUsername == "" && !empty($sales_DataD['phone'])) {
			$phoneParts = explode(',', $sales_DataD['phone']);
			$loginUsername = trim($phoneParts[0]);
		}

		if ($loginUsername == "") {
			$result = array("ack" => 0, "ack_msg" => "Mobile number is required to create system user.");
			echo '{"data":' . json_encode($result) . '}';
			exit;
		}

		if ($sales_DataD['password'] != "") {
			$password = $sales_DataD['password'];
		} else {
			$password = md5($loginUsername);
		}

		$admin_type = ($sales_DataD['channel_partner_flag'] == 1) ? 1 : $sales_DataD['type_of_executive'];

		$rows = array(
			"name",
			"type",
			"admin_type",
			"user_id",
			"customer_id",
			"sales_executive_id",
			"username",
			"phone",
			"email",
			"password",
			"isDelete",
			"adate"
		);
		$values = array(
			$sales_DataD['cname'],
			3,
			$admin_type,
			0,
			$id,
			0,
			$loginUsername,
			$loginUsername,
			$sales_DataD['email'],
			$password,
			0,
			$adate
		);

		$Insert = $db->rp_insert("dealer_distributor_network", $values, $rows, 0);
		if ($Insert !== false && $Insert > 0) {
			$result = array('ack' => 1, 'ack_msg' => "System User Created Successfully. Username: " . $loginUsername);
			echo '{"data":' . json_encode($result) . '}';
		} else {
			$result = array("ack" => 0, "ack_msg" => "User Creation Failed. Please Try Again..");
			echo '{"data":' . json_encode($result) . '}';
		}
	} else {
		$result = array("ack" => 0, "ack_msg" => "System user already exists for this customer.");
		echo '{"data":' . json_encode($result) . '}';
	}
} else {
	$result = array("ack" => 0, "ack_msg" => "Something went wrong!! Please Try again!!");
	echo '{"data":' . json_encode($result) . '}';
}
?>
