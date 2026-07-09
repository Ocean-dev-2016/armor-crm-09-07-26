<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("notification.class.php");
require_once("class.system.php");
class Visit extends Functions
{
	public $db;
	public $ctable = "visit";
	//public $sales_type_title=array("sales_manager"=>"Sales Manager","area_sales_manager"=>"Area Sales Manager","sales_officer"=>"Area Sales Manager","sales_executive"=>"Sales Officer");
	function __construct($id = "")
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
		$this->notification = new Notification();
		$this->system = new System();
	}

	//-----------------------------------------------------------------------------------------------//
	public function AddVisit($detail, $file)
	{
		// print_r($file);exit;
		extract($detail);

		if ($customer_id != 0) {
			$visit_type = "1";

			$product_name = $this->db->rp_getValue("executive", "purchasing_from", "id='" . $customer_id . "'", 0);

			if ($product_name != "") {
				$product_name1 = $product_name;
			} else {
				$product_name1 = "";
			}
		} else {
			$visit_type = "";
			$product_name1 = "";
		}

		$rows 	= array(
			"user_id",
			"customer_id",
			"latitude",
			"longitude",
			"app_address",
			"remark",
			"start_date_time",
			"isActive",
			"entry_flag",
			"purpose_id",
			"type_of_company",
			"visit_type",
			"product_name",
			"inquiry_id",
		);
		$values = array(
			$user_id,
			$customer_id,
			$latitude,
			$longitude,
			$app_address,
			$remark,
			$start_date_time,
			1,
			5,
			$purpose_id,
			$type_of_company,
			$visit_type,
			$product_name1,
			$inquiry_id,
		);
		$eid = $this->db->rp_insert($this->ctable, $values, $rows, 0);
		$image_path = array();
		if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
			$ri = $eid;
			$rt = "visit";
			$tc = "visit";
			$rc = "id";
			$current_year = date("Y");
			$current_month = date("M");

			$yearlyFolderPath = "../resource/image/{$current_year}/{$current_month}/";
			if (!is_dir($yearlyFolderPath)) {
				mkdir($yearlyFolderPath, 0777, true);
			}

			for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
				//print_r($file["image_path"]);
				$file_name = $eid . "_" . $file['image_path']['name'][$i];
				$file_size = $file['image_path']['size'][$i];
				$file_tmp = $file['image_path']['tmp_name'][$i];
				$file_type = $file['image_path']['type'][$i];
				$extension = explode(".", $file_name);

				$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
				$extension = $extension[sizeof($extension) - 1];
				if (!in_array($extension, $allowed_extentions)) {
					$file_error = true;
				}
				$orignal_file_name = $extension[0];

				if (in_array($extension, $allowed_extentions)) {
					$attachment = $yearlyFolderPath;
					//	compressImage($file_tmp,$attachment.$file_name,60);
					$compressedImage = $this->db->compressImage($file_tmp, $attachment . $file_name);

					if ($compressedImage) {
						$compressedImageSize = filesize($compressedImage);
						$compressedImageSize = $this->db->convert_filesize($compressedImageSize);

						$status = 'success';
						$statusMsg = "Image compressed successfully.";
					} else {
						$statusMsg = "Image compress failed!";
					}
					//move_uploaded_file($file_tmp,$attachment.$file_name);
				}
				$MediaTitle = $file_name;
				$MediaOrignalTitle = $file_name;

				$MediaFileName = $current_year . "/" . $current_month . "/" . $file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate = date("Y-m-d H:i:s");

				// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
				$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

				$image_path[] = $MediaID;
			}
			$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $eid . "'", 0);
		}
		if ($eid) {
			$dt = date("Y-m-d");
			$root_count = $this->db->rp_getTotalRecord("my_route", "date='" . $dt . "' AND customer_id='" . $customer_id . "' AND sales_id='" . $user_id . "'", 0);

			if ($root_count > 0) {
				$isrootUpdate = $this->db->rp_update("my_route", array("visit_flag" => 1), "date='" . $dt . "' AND customer_id='" . $customer_id . "' AND sales_id='" . $user_id . "'", 0);
			}
		}
		/*$row = array(
			"type",
			"sales_executive_id",
			"longitude",
			"latitude",
			"date",
			"isDelete",
			"isActive",
		);

		$value = array(
			"visit",
			$user_id,
			$longitude,
			$latitude,
			date("Y-m-d H:i:s"),
			0,
			1,
		);
		$insert = $this->db->rp_insert("salesexecutive_tracking",$value,$row);*/

		if ($eid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "Visit Add successfully!!", "ack_msg" => "Visit Add successfully!!", "id" => $eid);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Visit Not Add");
			return $reply;
		}
	}


	public function UpdateVisit($detail, $file)
	{
		// print_r($file);exit;
		extract($detail);
		if ($visit_type == "1") {
			$reference_table = "executive";
		} else if ($visit_type == "3") {
			$reference_table = "no_order_inquiry";
		} else {
			$reference_table = "";
		}

		$rows 	= array(
			"user_id"      => $user_id,
			"customer_id"  => $customer_id,
			"inquiry_id"   => $inquiry_id,
			"stop_latitude" => $stop_latitude,
			"stop_longitude"    => $stop_longitude,
			"stop_app_address"  => $stop_app_address,
			"stop_remark"       => $stop_remark,
			"stop_date_time"    => $stop_date_time,
			"visit_type"       => $visit_type,
			"reference_table"  => $reference_table,
			"visit_stop_flag"  => $visit_stop_flag,
			"update_entry_flag"   => 5,
			"product_name"   => $product_name,
			"firm_name"   	=> $firm_name,
			"client_name"   => $client_name,
			"contact_number"   => $contact_number,
			"name"   => $name,
			"mobile_no"   => $mobile_no,
			"email_id"   => $email_id,
			"designation"   => $designation_id,

		);

		$Where = "id='" . $id . "'";
		$eid = $this->db->rp_update($this->ctable, $rows, $Where, 0);

		$image_path = array();
		if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
			$ri = $id;
			$rt = "visit";
			$tc = "visit";
			$rc = "id";
			$current_year = date("Y");
			$current_month = date("M");

			$yearlyFolderPath = "../resource/image/{$current_year}/{$current_month}/";
			if (!is_dir($yearlyFolderPath)) {
				mkdir($yearlyFolderPath, 0777, true);
			}

			for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
				//print_r($file["image_path"]);
				$file_name =  $id . "_" . $file['image_path']['name'][$i];
				$file_size = $file['image_path']['size'][$i];
				$file_tmp = $file['image_path']['tmp_name'][$i];
				$file_type = $file['image_path']['type'][$i];
				$extension = explode(".", $file_name);

				$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
				$extension = $extension[sizeof($extension) - 1];
				if (!in_array($extension, $allowed_extentions)) {
					$file_error = true;
				}
				$orignal_file_name = $extension[0];
				if (in_array($extension, $allowed_extentions)) {
					$attachment = $yearlyFolderPath;
					//move_uploaded_file($file_tmp,$attachment.$file_name);
					$compressedImage = $this->db->compressImage($file_tmp, $attachment . $file_name);

					if ($compressedImage) {
						$compressedImageSize = filesize($compressedImage);
						$compressedImageSize = $this->db->convert_filesize($compressedImageSize);

						$status = 'success';
						$statusMsg = "Image compressed successfully.";
					} else {
						$statusMsg = "Image compress failed!";
					}
				}
				$MediaTitle = $file_name;
				$MediaOrignalTitle = $file_name;

				$MediaFileName = $current_year . "/" . $current_month . "/" . $file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate = date("Y-m-d H:i:s");

				// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
				$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

				$image_path[] = $MediaID;
			}
			$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable, array("stop_image_path" => $image_path), "id='" . $id . "'", 0);
		}

		if ($eid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "Visit Update successfully!!", "ack_msg" => "Visit Update successfully!!");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Visit Not Update");
			return $reply;
		}
	}

	public function DownloadVisit($visit_id = '')
	{

		if ($visit_id) {

			$cid = $this->db->rp_getValue("visit", "customer_id", "id='" . $visit_id . "'", 0);
			$uname = $this->db->rp_getValue("executive", "company_name", "id='" . $cid . "'", 0);
			$uname = $this->db->rp_createSlug($uname);
			$count = $this->db->rp_getTotalRecord("visit", "id='" . $visit_id . "'", 0);

			if ($count > 0) {
				$body_url = ADMINSITEURL_STATIC . "bbsales_tracking/customer_visit_print_app.php?visit_id=" . $visit_id . "&flag=1";
				// echo $body_url;exit;
				// $body_url=ADMINSITEURL."view_order_new_1.php?order_id=".$order_id;
				//$body_url=ADMINSITEURL."view_order_new_1.php?order_id=".$order_id;
				// $body_url=ADMINSITEURL."order_view_download.php?order_id=".$order_id;
				//$d=file_get_contents(ADMINSITEURL.'order_view_download.php?order_id='.$order_id.'');

				$d = file_get_contents($body_url);
				// print_r($d); exit;
				$d = html_entity_decode($d);
				$relCertFileNames = array();
				$merge_file = array();
				require('../bbsales_tracking/mpdf60/mpdf.php');
				$mpdf = new mPDF(
					'', // mode - default ''

					'A4', // format - A4, for example, default ''

					10,     // font size - default 0

					'sans-serif',  // default font family

					1,    // margin_left

					1,    // margin right

					10,   // margin top

					5,    // margin bottom

					0,    // margin header

					0,    // margin footer

					'P'
				); // L - landscape, P - portrait

				/*$mpdf->use_kwt = true;*/

				/*$mpdf->autoPageBreak = false;*/

				$mpdf->WriteHTML($d);

				/*log entry*/
				$sales_id = $this->db->rp_getValue("visit", "user_id", "id='" . $visit_id . "'", 0);
				$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);

				$last_id = $visit_id;
				$flag = "Application";
				$ctable = "visit";
				$module_name = "visit";
				$log_description = $module_name . " PDF Download By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				$this->db->insertLog($ctable, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $sales_id, $uname);
				/*log entry*/

				//$fileName = "orders".$order_id;
				$date = date("d-m-Y-H-i-s");
				$fileName = $date . "-" . $uname . "-" . $visit_id;

				// if(!is_dir("../bbsales_tracking/pdf/visit/".$fileName)){ 
				// 	mkdir("../bbsales_tracking/pdf/visit/".$fileName);
				// }

				$pdf_file_path	= "../bbsales_tracking/pdf/visit/" . $fileName . '.pdf';
				if (file_exists($pdf_file_path)) {
					unlink($pdf_file_path);
				}

				$mpdf->Output($pdf_file_path);
				$pdf_file_path;

				$result = array();
				$result['pdf'] = ADMINSITEURL . "pdf/visit/" . $fileName . '.pdf';
				$result['fileName'] = $fileName . '.pdf';

				$reply = array("ack" => 1, "developer_msg" => "Visit Generate Successfully", "ack_msg" => "Visit Generate Successfully", "result" => $result);
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Visit Not Generate!!", "ack_msg" => "Visit Not Generate!!");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Visit Id Require!!", "ack_msg" => "Visit Id Require!!");
			return $reply;
		}
	}
}
