<?php
require_once("main.class.php");
require_once("function.class.php");
class Company extends Functions
{
	public $db;
	public $ctable = "company_master";
	function __construct($id = "")
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
	}
	public function InsertCompany($detail, $file)
	{
		// print_r($file);exit;
		extract($detail);
		$dup_where = "name = '" . $name . "' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable, $dup_where);
		if ($r) {
			$reply = array("ack" => 0, "developer_msg" => "Already Exist Company Name", "ack_msg" => "Duplication! Already Exist Company Name.");
			return $reply;
		} else {
			if (isset($file["image_path"])) {
				// print_r($file);exit();
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["image_path"]["name"]);
				// echo "heelo";exit();
				if ($fileName != "") {
					$fileSize 	= round($file["image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$image_path	= 'header_img_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= HEADER_A . $image_path;
					$file['image_path']['tmp_name'];
					// print_r($filePath); exit;
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$image_path = "";
				}
			}
			if (isset($file["footer_image_path"])) {
				// print_r($file);exit();
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
				$temp = explode(".", $file["footer_image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["footer_image_path"]["name"]);
				// echo "heelo";exit();
				if ($fileName != "") {
					$fileSize 	= round($file["footer_image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$footer_image_path	= 'footer_img_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= HEADER_A . $footer_image_path;
					$file['footer_image_path']['tmp_name'];
					// print_r($filePath); exit;
					move_uploaded_file($file['footer_image_path']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$footer_image_path = "";
				}
			}

			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"name",
				"gst",
				"pan_crad",
				"image_path",
				"footer_image_path",
				"address",
				"bank_details",
				"trems_and_condition",
				"isDelete",
				"india_mart_api_key",
				"prefix",
			);
			$values = array(
				$name,
				$gst,
				$pan_crad,
				$image_path,
				$footer_image_path,
				$address,
				$bank_details,
				$trems_and_condition,
				$isDelete,
				$india_mart_api_key,
				$prefix,
			);

			/*log entry*/
			$module_name = "Company";
			$flag = "Web";
			$log_description = $module_name . " " . $name . " Created By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			/*log entry*/

			$uid = $this->db->rp_insert($this->ctable, $values, $rows, 0, $log_description, $flag, $module_name, "", "");

			if ($uid != 0) {
				$reply = array("ack" => 1, "developer_msg" => "Company Added.", "ack_msg" => "Success! Company Insert Successfully.");
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Company Insert Failed.");
				return $reply;
			}
		}
	}

	public function UpdateCompany($detail, $file)
	{
		extract($detail);
		$dup_where = "name = '" . $name . "' AND id!='" . $_REQUEST['id'] . "' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable, $dup_where);
		if ($r) {
			$reply = array("ack" => 0, "developer_msg" => "Already Exist Company Name", "ack_msg" => "Duplication! Already Exist Company Name.");
			return $reply;
		} else {

			if (isset($file["image_path"])) {
				// print_r($file);exit();
				// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["image_path"]["name"]);
				// echo "heelo";exit();
				if ($fileName != "") {
					$fileSize 	= round($file["image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$image_path	= 'header_img_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= HEADER_A . $image_path;
					$file['image_path']['tmp_name'];
					// print_r($filePath); exit;
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);
					$image_path = $image_path;
					unset($old_image_path);
				} else {
					$image_path = $old_image_path;
					unset($old_image_path);
				}
			}
			if (isset($file["footer_image_path"])) {
				// print_r($file);exit();
				// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["footer_image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["footer_image_path"]["name"]);
				// echo "heelo";exit();
				if ($fileName != "") {
					$fileSize 	= round($file["footer_image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$footer_image_path	= 'footer_img_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= HEADER_A . $footer_image_path;
					$file['footer_image_path']['tmp_name'];
					// print_r($filePath); exit;
					move_uploaded_file($file['footer_image_path']['tmp_name'], $filePath);
					$footer_image_path = $footer_image_path;
					unset($old_footer_image_path);
				} else {
					$footer_image_path = $old_footer_image_path;
					unset($old_footer_image_path);
				}
			}

			$rows 	= array(
				"name"					=> $name,
				"gst"					=> $gst,
				"pan_crad"				=> $pan_crad,
				"image_path"			=> $image_path,
				"footer_image_path"		=> $footer_image_path,
				"address"				=> $address,
				"bank_details"			=> $bank_details,
				"trems_and_condition"	=> $trems_and_condition,
				"india_mart_api_key"	=> $india_mart_api_key,
				"prefix"	=> $prefix,
			);
			$where	= "id='" . $_REQUEST['id'] . "'";
			/*log entry*/
			$module_name = "Company";
			$flag = "Web";
			$log_description = $module_name . " " . $name . " Edited By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			/*log entry*/
			$uid = $this->db->rp_update($this->ctable, $rows, $where, 0, $log_description, $flag, $module_name, "", "");
			if ($uid != 0) {
				$reply = array("ack" => 1, "developer_msg" => "Company Update Successfull!!.", "ack_msg" => "Success! Company Update Successfully.");
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Company Update Failed.");
				return $reply;
			}
		}
	}
	public function GetEditDataCompany($detail)
	{
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, 0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result = array();

		$result['name']		= htmlentities($ctable_d['name']);
		$result['gst']		= htmlentities($ctable_d['gst']);
		$result['pan_crad']		= htmlentities($ctable_d['pan_crad']);
		$result['image_path']		= htmlentities($ctable_d['image_path']);
		$result['footer_image_path']		= htmlentities($ctable_d['footer_image_path']);
		$result['address']		= htmlentities($ctable_d['address']);
		$result['bank_details']		= htmlentities($ctable_d['bank_details']);
		$result['trems_and_condition']		= htmlentities($ctable_d['trems_and_condition']);
		$result['india_mart_api_key']		= htmlentities($ctable_d['india_mart_api_key']);
		$result['prefix']		= htmlentities($ctable_d['prefix']);

		$reply = array("ack" => 1, "developer_msg" => "Company detail fetched!!.", "ack_msg" => "Success! Company Edit Successfully.", "result" => $result);
		return $reply;
	}

	public function DeleteCompany($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='" . $_REQUEST['id'] . "'";
		/*log entry*/
		$name = $this->db->rp_getValue("company_master", "name", "id='" . $_REQUEST['id'] . "'");
		$module_name = "Company";
		$flag = "Web";
		$log_description = $module_name . " " . $name . " Deleted By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
		/*log entry*/
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0, $log_description, $flag, $module_name, "", "");
		if ($uid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Company Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete Company Failed.");
			return $reply;
		}
	}
}
