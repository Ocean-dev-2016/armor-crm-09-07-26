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

		$remark_code = isset($remark_code) ? strtoupper(trim($remark_code)) : "";
		$reason_code = isset($reason_code) ? strtoupper(trim($reason_code)) : "";
		$approval_type = isset($approval_type) ? trim($approval_type) : "";
		$shortNoteText = ($remark_code == "F") ? trim($stop_remark) : "";

		$remarkNameMap = array(
			"A" => "OLD CUSTOMER VISIT",
			"B" => "PAYMENT COLLECTION VISIT",
			"C" => "NEED APPROVAL",
			"D" => "NEW CUSTOMER",
			"E" => "HIGH RATE",
			"F" => "SHORT NOTE",
			"G" => "CALL TO ORDER",
		);
		$reasonNameMap = array(
			"A1" => "Next Week Order",
			"A2" => "Next Month Order",
			"B1" => "Payment Collection With Order",
			"C1" => "Private Consultant",
			"C2" => "Government Consultant",
			"D1" => "Next Week Order",
			"D2" => "Next Month Order",
			"E1" => "High Rate Form",
		);
		/* Build one readable value used by all existing Visit web/print reports. */
		if ($remark_code != "") {
			$composed = "(" . $remark_code . ") " . (isset($remarkNameMap[$remark_code]) ? $remarkNameMap[$remark_code] : $remark_code);
			if ($remark_code == "F" && $shortNoteText != "") {
				$composed .= " - " . $shortNoteText;
			} else if ($reason_code != "" && isset($reasonNameMap[$reason_code])) {
				$composed .= " - " . $reason_code . ": " . $reasonNameMap[$reason_code];
			} else if ($reason_code != "") {
				$composed .= " - " . $reason_code;
			}
			$stop_remark = $composed;
		}

		$rows 	= array(
			"user_id"      => $user_id,
			"customer_id"  => $customer_id,
			"inquiry_id"   => $inquiry_id,
			"stop_latitude" => $stop_latitude,
			"stop_longitude"    => $stop_longitude,
			"stop_app_address"  => $stop_app_address,
			"stop_remark"       => $stop_remark,
			"remark_code"       => $remark_code,
			"reason_code"       => $reason_code,
			"approval_type"     => $approval_type,
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
			$followupCreated = false;
			$followupId = "";
			$consultantFormId = "";
			$highRateFormId = "";

			/*
			 * Every Visit End remark becomes a Follow-up for visited customer/inquiry.
			 * C1/C2 also store Private/Government Consultant Detail form.
			 * E1 also stores High Rate Analysis form + product rows.
			 * visit_followup_id / form ids prevent duplicates if Android retries #122.
			 */

			/* Save/link C1/C2 form first so Follow-up can include form details */
			if (($reason_code == "C1" || $reason_code == "C2") && $remark_code == "C") {
				$hasConsultantPayload = (
					(isset($consultant_firm_name) && trim($consultant_firm_name) != "") ||
					(isset($consultant_address) && trim($consultant_address) != "") ||
					(isset($consultant_mobile) && trim($consultant_mobile) != "")
				);
				if ($hasConsultantPayload) {
					$consultantFormId = $this->saveVisitConsultantForm(array(
						"visit_id" => $id,
						"user_id" => $user_id,
						"customer_id" => $customer_id,
						"inquiry_id" => $inquiry_id,
						"reason_code" => $reason_code,
						"approval_type" => $approval_type,
						"followup_id" => 0,
						"firm_name" => isset($consultant_firm_name) ? $consultant_firm_name : (isset($firm_name) ? $firm_name : ""),
						"address" => isset($consultant_address) ? $consultant_address : "",
						"city" => isset($consultant_city) ? $consultant_city : "",
						"state" => isset($consultant_state) ? $consultant_state : "",
						"pincode" => isset($consultant_pincode) ? $consultant_pincode : "",
						"contact_person" => isset($consultant_contact_person) ? $consultant_contact_person : "",
						"mobile" => isset($consultant_mobile) ? $consultant_mobile : "",
						"email" => isset($consultant_email) ? $consultant_email : "",
					));
				} else {
					$consultantFormId = $this->db->rp_getValue($this->ctable, "consultant_form_id", "id='" . $id . "'", 0);
				}
			}

			/* Save/link E1 High Rate form first so Follow-up can include summary */
			if ($reason_code == "E1" && $remark_code == "E") {
				$highRateItems = array();
				if (isset($high_rate_items) && $high_rate_items != "") {
					if (is_array($high_rate_items)) {
						$highRateItems = $high_rate_items;
					} else {
						$decodedItems = json_decode($high_rate_items, true);
						if (is_array($decodedItems)) {
							$highRateItems = $decodedItems;
						}
					}
				}
				$hasHighRatePayload = (
					(isset($high_rate_customer_name) && trim($high_rate_customer_name) != "") ||
					!empty($highRateItems)
				);
				if ($hasHighRatePayload) {
					$highRateFormId = $this->saveVisitHighRateForm(array(
						"visit_id" => $id,
						"user_id" => $user_id,
						"customer_id" => $customer_id,
						"inquiry_id" => $inquiry_id,
						"reason_code" => $reason_code,
						"followup_id" => 0,
						"customer_name" => isset($high_rate_customer_name) ? $high_rate_customer_name : "",
						"payment_option" => isset($payment_option) ? $payment_option : (isset($high_rate_payment_option) ? $high_rate_payment_option : ""),
						"payment_remark" => isset($payment_remark) ? $payment_remark : (isset($high_rate_payment_remark) ? $high_rate_payment_remark : ""),
						"items" => $highRateItems,
					));
				} else {
					$highRateFormId = $this->db->rp_getValue($this->ctable, "high_rate_form_id", "id='" . $id . "'", 0);
				}
			}

			$existingFollowupId = $this->db->rp_getValue(
				$this->ctable,
				"visit_followup_id",
				"id='" . $id . "'",
				0
			);

			if ($existingFollowupId != "" && $existingFollowupId != "0") {
				$followupCreated = true;
				$followupId = $existingFollowupId;
			} else if ($remark_code != "") {
				$followupReferenceTable = "executive";
				$followupReferenceId = $customer_id;
				$followupVisitorId = $customer_id;
				$inquiryCreatedBy = "";
				$inquiryAssignTo = "";

				if (($visit_type == "3" || ($customer_id == "" || $customer_id == "0")) && $inquiry_id != "" && $inquiry_id != "0") {
					$followupReferenceTable = "no_order_inquiry";
					$followupReferenceId = $inquiry_id;
					$followupVisitorId = $this->db->rp_getValue("no_order_inquiry", "dealer_id", "id='" . $inquiry_id . "'", 0);
					if ($followupVisitorId == "") {
						$followupVisitorId = $customer_id;
					}
					$inquiryCreatedBy = $this->db->rp_getValue("no_order_inquiry", "inquiry_created_by", "id='" . $inquiry_id . "'", 0);
					$inquiryAssignTo = $this->db->rp_getValue("no_order_inquiry", "inquiry_assign_to", "id='" . $inquiry_id . "'", 0);
				}

				if ($followupReferenceId != "" && $followupReferenceId != "0") {
					$followupDescription = "[Visit Stop] " . $stop_remark;
					if ($remark_code == "F" && $shortNoteText != "") {
						$followupDescription = "[Visit Stop - Short Note] " . $shortNoteText;
					} else if ($remark_code == "C") {
						$followupDescription = "[Visit Stop - Need Approval] " . $stop_remark;
						$consultantDetailText = $this->getConsultantFormFollowupText($id, $consultantFormId);
						if ($consultantDetailText != "") {
							$followupDescription .= " | " . $consultantDetailText;
						}
					} else if ($remark_code == "E") {
						$followupDescription = "[Visit Stop - High Rate] " . $stop_remark;
						$highRateDetailText = $this->getHighRateFormFollowupText($id, $highRateFormId);
						if ($highRateDetailText != "") {
							$followupDescription .= " | " . $highRateDetailText;
						}
					}

					$followupValues = array(
						$followupReferenceTable,
						$user_id,
						$followupVisitorId,
						$followupReferenceId,
						"",
						$followupDescription,
						"5",
						$stop_date_time,
						0,
						1,
						0,
						"",
						"2",
						$inquiryCreatedBy,
						$inquiryAssignTo
					);
					$followupColumns = array(
						"reference_table",
						"user_id",
						"visitor_id",
						"reference_id",
						"project_manager_id",
						"description",
						"through",
						"followup_date",
						"isDelete",
						"isActive",
						"next_followup_id",
						"refrence_media_id",
						"entry_type",
						"inquiry_created_by",
						"inquiry_assign_to"
					);
					$followupId = $this->db->rp_insert("followup", $followupValues, $followupColumns, 0);
					if ($followupId) {
						$visitUpdateRows = array("visit_followup_id" => $followupId);
						if ($visit_stop_flag == "" || $visit_stop_flag == "0") {
							$visitUpdateRows["visit_stop_flag"] = "3";
						}
						$this->db->rp_update(
							$this->ctable,
							$visitUpdateRows,
							"id='" . $id . "'",
							0
						);
						$followupCreated = true;
					}
				}
			}

			/* Link followup_id on consultant form */
			if ($consultantFormId != "" && $consultantFormId != "0" && $followupId != "" && $followupId != "0") {
				$this->db->rp_update("visit_consultant_form", array("followup_id" => $followupId), "id='" . $consultantFormId . "'", 0);
			}

			/* Link followup_id on high rate form */
			if ($highRateFormId != "" && $highRateFormId != "0" && $followupId != "" && $followupId != "0") {
				$this->db->rp_update("visit_high_rate_form", array("followup_id" => $followupId), "id='" . $highRateFormId . "'", 0);
			}

			$reply = array(
				"ack" => 1,
				"developer_msg" => "Visit Update successfully!!",
				"ack_msg" => "Visit Update successfully!!",
				"followup_created" => $followupCreated ? "1" : "0",
				"followup_id" => (string) $followupId,
				"consultant_form_id" => (string) $consultantFormId,
				"high_rate_form_id" => (string) $highRateFormId,
			);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Visit Not Update");
			return $reply;
		}
	}

	public function getConsultantFormFollowupText($visitId, $formId = "")
	{
		$form = null;
		if ($formId != "" && $formId != "0") {
			$r = $this->db->rp_getData("visit_consultant_form", "*", "id='" . $formId . "' AND isDelete=0", "", 0);
			if ($r) {
				$form = mysqli_fetch_assoc($r);
			}
		}
		if (!$form && $visitId != "" && $visitId != "0") {
			$r = $this->db->rp_getData("visit_consultant_form", "*", "visit_id='" . $visitId . "' AND isDelete=0", "id DESC", 0);
			if ($r) {
				$form = mysqli_fetch_assoc($r);
			}
		}
		if (!$form) {
			return "";
		}
		$parts = array();
		$typeLabel = (isset($form['consultant_type']) && $form['consultant_type'] == "government") ? "Government Consultant" : "Private Consultant";
		$parts[] = "Type: " . $typeLabel;
		if (!empty($form['firm_name'])) {
			$parts[] = "Firm: " . $form['firm_name'];
		}
		if (!empty($form['contact_person'])) {
			$parts[] = "Contact: " . $form['contact_person'];
		}
		if (!empty($form['mobile'])) {
			$parts[] = "Mo: " . $form['mobile'];
		}
		if (!empty($form['email'])) {
			$parts[] = "Mail: " . $form['email'];
		}
		if (!empty($form['city'])) {
			$parts[] = "City: " . $form['city'];
		}
		if (!empty($form['state'])) {
			$parts[] = "State: " . $form['state'];
		}
		if (!empty($form['pincode'])) {
			$parts[] = "Pincode: " . $form['pincode'];
		}
		if (!empty($form['address'])) {
			$parts[] = "Address: " . $form['address'];
		}
		return implode(" | ", $parts);
	}

	public function getConsultantFormHtml($visitId)
	{
		if ($visitId == "" || $visitId == "0") {
			return "";
		}
		$r = $this->db->rp_getData("visit_consultant_form", "*", "visit_id='" . $visitId . "' AND isDelete=0", "id DESC", 0);
		if (!$r) {
			return "";
		}
		$form = mysqli_fetch_assoc($r);
		if (!$form) {
			return "";
		}
		$typeLabel = (isset($form['consultant_type']) && $form['consultant_type'] == "government") ? "Government Consultant" : "Private Consultant";
		$html = '<div style="margin-top:6px;padding:6px;border:1px solid #ddd;background:#f9f9f9;font-size:12px;line-height:1.5;">';
		$html .= '<b>Consultant Detail (' . htmlspecialchars($typeLabel) . ')</b><br>';
		$html .= '<b>Firm Name:</b> ' . htmlspecialchars($form['firm_name']) . '<br>';
		$html .= '<b>Address:</b> ' . nl2br(htmlspecialchars($form['address'])) . '<br>';
		$html .= '<b>City:</b> ' . htmlspecialchars($form['city']) . ' &nbsp; <b>State:</b> ' . htmlspecialchars($form['state']) . ' &nbsp; <b>Pincode:</b> ' . htmlspecialchars($form['pincode']) . '<br>';
		$html .= '<b>Contact Person:</b> ' . htmlspecialchars($form['contact_person']) . '<br>';
		$html .= '<b>Mo:</b> ' . htmlspecialchars($form['mobile']);
		if (!empty($form['email'])) {
			$html .= ' &nbsp; <b>Mail ID:</b> ' . htmlspecialchars($form['email']);
		}
		$html .= '</div>';
		return $html;
	}

	public function getHighRateProductsMaster()
	{
		return array(
			array("slug" => "branch_pipe_heavy", "product_name" => "BRANCH PIPE HEAVY", "sort_order" => 1),
			array("slug" => "hydrant_single_double", "product_name" => "HYDRANT (SINGLE / DOUBLE)", "sort_order" => 2),
			array("slug" => "hose_reel_drom", "product_name" => "HOSE REEL DROM (REGU./ISI/MALASIYAN/BS EN 671)", "sort_order" => 3),
			array("slug" => "rrl_hose_isi", "product_name" => "RRL HOSE ISI (TYPE - A / B / C)", "sort_order" => 4),
			array("slug" => "extinguisher", "product_name" => "EXTINGHUISHER (CO2/ABC/FOAM)", "sort_order" => 5),
			array("slug" => "hose_box", "product_name" => "HOSE BOX", "sort_order" => 6),
			array("slug" => "way_2way_3way_4way", "product_name" => "2WAY / 3WAY / 4WAY", "sort_order" => 7),
			array("slug" => "sprinkler", "product_name" => "SPRINKLER (UL / NON UL)", "sort_order" => 8),
			array("slug" => "alarm_valve", "product_name" => "ALARM VALVE", "sort_order" => 9),
			array("slug" => "deluge_valve", "product_name" => "DELUGE VALVE", "sort_order" => 10),
			array("slug" => "flexible_pipe", "product_name" => "FLEXIBLE PIPE (BRAIDED / UNBRAIDED)", "sort_order" => 11),
			array("slug" => "water_moniter", "product_name" => "WATER MONITER (NOZZLE : SS/ALU./AQUA FOAM)", "sort_order" => 12),
			array("slug" => "butterfly_valve", "product_name" => "BUTTERFLY VALVE", "sort_order" => 13),
			array("slug" => "non_return_valve", "product_name" => "NON RETURN VALVE (SINGLE / DOUBLE)", "sort_order" => 14),
			array("slug" => "orifice_plate", "product_name" => "OFIFICE PLATE", "sort_order" => 15),
			array("slug" => "y_strainer", "product_name" => "Y STAINER", "sort_order" => 16),
			array("slug" => "gate_valve", "product_name" => "GATE VALVE", "sort_order" => 17),
			array("slug" => "air_release_valve", "product_name" => "AIR RELESE VALVE", "sort_order" => 18),
		);
	}

	/** payment_option: 0 = Advance, 1 = 30 Days (Android sends 0/1) */
	public function getHighRatePaymentOptions()
	{
		return array(
			array("value" => "0", "label" => "Advance"),
			array("value" => "1", "label" => "30 Days"),
		);
	}

	public function normalizeHighRatePaymentOption($value)
	{
		$value = trim((string) $value);
		if ($value === "0" || strcasecmp($value, "Advance") === 0) {
			return "0";
		}
		if ($value === "1" || strcasecmp($value, "30 Days") === 0 || strcasecmp($value, "30 days") === 0) {
			return "1";
		}
		return $value;
	}

	public function getHighRatePaymentOptionLabel($value)
	{
		$value = $this->normalizeHighRatePaymentOption($value);
		if ($value === "0") {
			return "Advance";
		}
		if ($value === "1") {
			return "30 Days";
		}
		return $value;
	}

	/** #235 — product list with Android camelCase keys */
	public function getHighRateProductsForApi()
	{
		$products = array();
		foreach ($this->getHighRateProductsMaster() as $p) {
			$products[] = array(
				"slug" => $p['slug'],
				"product_name" => $p['product_name'],
				"productName" => $p['product_name'],
				"sort_order" => (string) $p['sort_order'],
				"givenRate" => "",
				"given_rate" => "",
				"qty" => "",
				"customerRate" => "",
				"customer_rate" => "",
				"remark" => "",
			);
		}
		return $products;
	}

	public function getHighRateProductMaster()
	{
		$names = array();
		foreach ($this->getHighRateProductsMaster() as $p) {
			$names[] = $p['product_name'];
		}
		return $names;
	}

	public function getHighRateProductBySlug($slug)
	{
		$slug = strtolower(trim($slug));
		foreach ($this->getHighRateProductsMaster() as $p) {
			if ($p['slug'] === $slug) {
				return $p;
			}
		}
		return null;
	}

	/**
	 * Accept items as array OR slug-keyed object from Android.
	 * Each row: slug (preferred) or product_name + given_rate, qty, customer_rate, remark.
	 */
	public function normalizeHighRateItems($items)
	{
		$normalized = array();
		if (!is_array($items)) {
			return $normalized;
		}

		/* {"branch_pipe_heavy":{"given_rate":"100",...}} */
		$isAssoc = false;
		foreach (array_keys($items) as $k) {
			if (!is_numeric($k)) {
				$isAssoc = true;
				break;
			}
		}
		if ($isAssoc) {
			foreach ($items as $slug => $row) {
				if (!is_array($row)) {
					continue;
				}
				$row['slug'] = isset($row['slug']) ? $row['slug'] : $slug;
				$normalized[] = $this->resolveHighRateItemRow($row);
			}
			return $this->filterHighRateItemRows($normalized);
		}

		foreach ($items as $row) {
			if (!is_array($row)) {
				continue;
			}
			$normalized[] = $this->resolveHighRateItemRow($row);
		}
		return $this->filterHighRateItemRows($normalized);
	}

	private function resolveHighRateItemRow($row)
	{
		$slug = "";
		if (isset($row['slug'])) {
			$slug = strtolower(trim($row['slug']));
		} else if (isset($row['productSlug'])) {
			$slug = strtolower(trim($row['productSlug']));
		}

		$productName = "";
		if (isset($row['product_name'])) {
			$productName = trim($row['product_name']);
		} else if (isset($row['productName'])) {
			$productName = trim($row['productName']);
		} else if (isset($row['Product'])) {
			$productName = trim($row['Product']);
		}
		$sortOrder = isset($row['sort_order']) ? (int) $row['sort_order'] : 0;

		$master = $this->matchHighRateProductMaster($productName, $slug);
		if ($master) {
			$slug = $master['slug'];
			$productName = $master['product_name'];
			if ($sortOrder == 0) {
				$sortOrder = (int) $master['sort_order'];
			}
		}

		$givenRate = "";
		if (isset($row['given_rate'])) {
			$givenRate = $row['given_rate'];
		} else if (isset($row['givenRate'])) {
			$givenRate = $row['givenRate'];
		} else if (isset($row['Givan Rate'])) {
			$givenRate = $row['Givan Rate'];
		} else if (isset($row['Given Rate'])) {
			$givenRate = $row['Given Rate'];
		}

		$customerRate = "";
		if (isset($row['customer_rate'])) {
			$customerRate = $row['customer_rate'];
		} else if (isset($row['customerRate'])) {
			$customerRate = $row['customerRate'];
		} else if (isset($row['Customer rate'])) {
			$customerRate = $row['Customer rate'];
		}

		return array(
			"slug" => $slug,
			"product_name" => $productName,
			"given_rate" => $givenRate,
			"qty" => isset($row['qty']) ? $row['qty'] : (isset($row['Qty']) ? $row['Qty'] : ""),
			"customer_rate" => $customerRate,
			"remark" => isset($row['remark']) ? $row['remark'] : (isset($row['Remark']) ? $row['Remark'] : ""),
			"sort_order" => $sortOrder,
		);
	}

	private function normalizeHighRateProductKey($name)
	{
		$name = strtoupper(trim((string) $name));
		return preg_replace('/\s+/', '', $name);
	}

	private function matchHighRateProductMaster($productName = "", $slug = "")
	{
		$slug = strtolower(trim((string) $slug));
		if ($slug != "") {
			$master = $this->getHighRateProductBySlug($slug);
			if ($master) {
				return $master;
			}
		}

		$productName = trim((string) $productName);
		if ($productName == "") {
			return null;
		}

		foreach ($this->getHighRateProductsMaster() as $master) {
			if (strcasecmp($master['product_name'], $productName) === 0) {
				return $master;
			}
		}

		$nameKey = $this->normalizeHighRateProductKey($productName);
		foreach ($this->getHighRateProductsMaster() as $master) {
			if ($this->normalizeHighRateProductKey($master['product_name']) === $nameKey) {
				return $master;
			}
		}

		return null;
	}

	private function deleteHighRateFormItemsByVisit($visitId, $formId = "")
	{
		$visitId = (int) $visitId;
		if ($visitId <= 0) {
			return;
		}
		$where = "visit_id='" . $visitId . "'";
		if ($formId != "" && $formId != "0") {
			$where = "(visit_id='" . $visitId . "' OR high_rate_form_id='" . (int) $formId . "')";
		}
		@mysqli_query($this->db->myconn, "DELETE FROM `visit_high_rate_form_item` WHERE " . $where);
	}

	private function filterHighRateItemRows($rows)
	{
		$byKey = array();
		foreach ($rows as $row) {
			if ($row['product_name'] == "" && $row['slug'] == "") {
				continue;
			}
			if ($row['product_name'] == "" && $row['slug'] != "") {
				$master = $this->getHighRateProductBySlug($row['slug']);
				if ($master) {
					$row['product_name'] = $master['product_name'];
					$row['sort_order'] = (int) $master['sort_order'];
				} else {
					continue;
				}
			}
			/* Prefer fixed master slug as unique key — same slug never twice */
			$key = ($row['slug'] != "") ? $row['slug'] : ("name:" . $this->normalizeHighRateProductKey($row['product_name']));
			$byKey[$key] = $row;
		}
		$out = array_values($byKey);
		usort($out, function ($a, $b) {
			return (int) $a['sort_order'] - (int) $b['sort_order'];
		});
		return $out;
	}

	public function getConsultantFormFields($reasonCode = "C1")
	{
		$title = ($reasonCode == "C2") ? "Government Consultant Detail" : "Private Consultant Detail";
		return array(
			"form_title" => $title,
			"fields" => array(
				array("key" => "consultant_firm_name", "label" => "Firm Name", "type" => "text", "required" => "1"),
				array("key" => "consultant_address", "label" => "Address", "type" => "textarea", "required" => "1"),
				array("key" => "consultant_city", "label" => "City", "type" => "text", "required" => "1"),
				array("key" => "consultant_state", "label" => "State", "type" => "text", "required" => "1"),
				array("key" => "consultant_pincode", "label" => "Pincode", "type" => "text", "required" => "1"),
				array("key" => "consultant_contact_person", "label" => "Contact Person", "type" => "text", "required" => "1"),
				array("key" => "consultant_mobile", "label" => "Mo", "type" => "text", "required" => "1"),
				array("key" => "consultant_email", "label" => "Mail ID", "type" => "text", "required" => "0"),
			),
		);
	}

	public function getHighRateFormFields()
	{
		$products = array();
		foreach ($this->getHighRateProductsMaster() as $p) {
			$products[] = array(
				"slug" => $p['slug'],
				"product_name" => $p['product_name'],
				"given_rate" => "",
				"qty" => "",
				"customer_rate" => "",
				"remark" => "",
				"sort_order" => (string) $p['sort_order'],
			);
		}
		return array(
			"form_title" => "High Rate Analysis",
			"fields" => array(
				array("key" => "high_rate_customer_name", "label" => "Customer name", "type" => "text", "required" => "1"),
			),
			"columns" => array("Product", "Given Rate", "Qty", "Customer rate", "Remark"),
			"products" => $products,
		);
	}

	private function saveVisitConsultantForm($data)
	{
		$visitId = isset($data['visit_id']) ? $data['visit_id'] : 0;
		if ($visitId == "" || $visitId == "0") {
			return "";
		}

		$reasonCode = isset($data['reason_code']) ? strtoupper($data['reason_code']) : "C1";
		$approvalType = isset($data['approval_type']) ? $data['approval_type'] : (($reasonCode == "C2") ? "2" : "1");
		if ($approvalType == "2") {
			$reasonCode = "C2";
		} else if ($approvalType == "1") {
			$reasonCode = "C1";
		}
		$consultantType = ($reasonCode == "C2") ? "government" : "private";
		$formTitle = ($reasonCode == "C2") ? "Government Consultant Detail" : "Private Consultant Detail";

		$rowData = array(
			"visit_id" => $visitId,
			"user_id" => isset($data['user_id']) ? $data['user_id'] : 0,
			"customer_id" => isset($data['customer_id']) ? $data['customer_id'] : 0,
			"inquiry_id" => isset($data['inquiry_id']) ? $data['inquiry_id'] : 0,
			"reason_code" => $reasonCode,
			"approval_type" => $approvalType,
			"consultant_type" => $consultantType,
			"form_title" => $formTitle,
			"firm_name" => isset($data['firm_name']) ? $data['firm_name'] : "",
			"address" => isset($data['address']) ? $data['address'] : "",
			"city" => isset($data['city']) ? $data['city'] : "",
			"state" => isset($data['state']) ? $data['state'] : "",
			"pincode" => isset($data['pincode']) ? $data['pincode'] : "",
			"contact_person" => isset($data['contact_person']) ? $data['contact_person'] : "",
			"mobile" => isset($data['mobile']) ? $data['mobile'] : "",
			"email" => isset($data['email']) ? $data['email'] : "",
			"followup_id" => isset($data['followup_id']) ? $data['followup_id'] : 0,
			"isActive" => 1,
			"isDelete" => 0,
		);

		$existingId = $this->db->rp_getValue("visit_consultant_form", "id", "visit_id='" . $visitId . "' AND isDelete=0", 0);
		if ($existingId != "" && $existingId != "0") {
			$this->db->rp_update("visit_consultant_form", $rowData, "id='" . $existingId . "'", 0);
			$this->db->rp_update(
				$this->ctable,
				array(
					"consultant_form_id" => $existingId,
					"approval_type" => $approvalType,
					"reason_code" => $reasonCode,
					"remark_code" => "C",
				),
				"id='" . $visitId . "'",
				0
			);
			return $existingId;
		}

		$rowData["created_date"] = date("Y-m-d H:i:s");
		$columns = array_keys($rowData);
		$values = array_values($rowData);
		$formId = $this->db->rp_insert("visit_consultant_form", $values, $columns, 0);
		if ($formId) {
			$this->db->rp_update(
				$this->ctable,
				array(
					"consultant_form_id" => $formId,
					"approval_type" => $approvalType,
					"reason_code" => $reasonCode,
					"remark_code" => "C",
				),
				"id='" . $visitId . "'",
				0
			);
		}
		return $formId ? $formId : "";
	}

	/**
	 * Public API helper — Save / Update Consultant Detail form (C1 Private / C2 Government)
	 * Used by Android SAVE AND NEXT button.
	 */
	public function SaveConsultantDetailForm($detail)
	{
		$visitId = isset($detail['visit_id']) ? $detail['visit_id'] : "";
		$userId = isset($detail['user_id']) ? $detail['user_id'] : "";
		if ($visitId == "" || $visitId == "0") {
			return array("ack" => 0, "ack_msg" => "Visit id is required.", "developer_msg" => "visit_id missing");
		}
		if ($userId == "" || $userId == "0") {
			return array("ack" => 0, "ack_msg" => "User id is required.", "developer_msg" => "user_id missing");
		}

		$visitExists = $this->db->rp_getTotalRecord($this->ctable, "id='" . $visitId . "' AND isDelete=0", 0);
		if ($visitExists == 0) {
			return array("ack" => 0, "ack_msg" => "Visit not found.", "developer_msg" => "Invalid visit_id");
		}

		$firmName = isset($detail['firm_name']) ? trim($detail['firm_name']) : "";
		$address = isset($detail['address']) ? trim($detail['address']) : "";
		$city = isset($detail['city']) ? trim($detail['city']) : "";
		$state = isset($detail['state']) ? trim($detail['state']) : "";
		$pincode = isset($detail['pincode']) ? trim($detail['pincode']) : "";
		$contactPerson = isset($detail['contact_person']) ? trim($detail['contact_person']) : "";
		$mobile = isset($detail['mobile']) ? trim($detail['mobile']) : "";
		$email = isset($detail['email']) ? trim($detail['email']) : "";

		if ($firmName == "" || $address == "" || $city == "" || $state == "" || $pincode == "" || $contactPerson == "" || $mobile == "") {
			return array(
				"ack" => 0,
				"ack_msg" => "Please fill Firm Name, Address, City, State, Pincode, Contact Person, Mo.",
				"developer_msg" => "Required consultant form fields missing",
			);
		}

		$approvalType = isset($detail['approval_type']) ? trim($detail['approval_type']) : "";
		$reasonCode = isset($detail['reason_code']) ? strtoupper(trim($detail['reason_code'])) : "";
		if ($reasonCode == "" && $approvalType != "") {
			$reasonCode = ($approvalType == "2") ? "C2" : "C1";
		}
		if ($approvalType == "" && $reasonCode != "") {
			$approvalType = ($reasonCode == "C2") ? "2" : "1";
		}
		if ($reasonCode == "" || !in_array($reasonCode, array("C1", "C2"))) {
			return array(
				"ack" => 0,
				"ack_msg" => "Please select Private Consultant or Government Consultant.",
				"developer_msg" => "approval_type / reason_code required",
			);
		}

		$formId = $this->saveVisitConsultantForm(array(
			"visit_id" => $visitId,
			"user_id" => $userId,
			"customer_id" => isset($detail['customer_id']) ? $detail['customer_id'] : 0,
			"inquiry_id" => isset($detail['inquiry_id']) ? $detail['inquiry_id'] : 0,
			"reason_code" => $reasonCode,
			"approval_type" => $approvalType,
			"firm_name" => $firmName,
			"address" => $address,
			"city" => $city,
			"state" => $state,
			"pincode" => $pincode,
			"contact_person" => $contactPerson,
			"mobile" => $mobile,
			"email" => $email,
		));

		if ($formId == "" || $formId == "0") {
			$dbError = $this->db->rp_getLastDbError();
			return array(
				"ack" => 0,
				"ack_msg" => "Consultant Detail save failed.",
				"developer_msg" => ($dbError != "") ? $dbError : "Insert/Update failed",
			);
		}

		$formRow = array();
		$formRes = $this->db->rp_getData("visit_consultant_form", "*", "id='" . $formId . "' AND isDelete=0", "", 0);
		if ($formRes) {
			$formRow = mysqli_fetch_assoc($formRes);
		}

		return array(
			"ack" => 1,
			"ack_msg" => "Consultant Detail saved successfully.",
			"developer_msg" => "Consultant Detail saved successfully.",
			"consultant_form_id" => (string) $formId,
			"reason_code" => $reasonCode,
			"approval_type" => (string) $approvalType,
			"result" => $formRow,
		);
	}

	/**
	 * Save High Rate form by visit_id (+ customer_id).
	 * Android always sends fixed 18 products (productName + slug).
	 * Same visit_id → UPDATE existing form/items (NO duplicate 18 rows).
	 */
	private function saveVisitHighRateForm($data)
	{
		$visitId = isset($data['visit_id']) ? $data['visit_id'] : 0;
		if ($visitId == "" || $visitId == "0") {
			return "";
		}

		$customerId = isset($data['customer_id']) ? $data['customer_id'] : 0;
		if ($customerId == "" || $customerId == "0") {
			$customerId = $this->db->rp_getValue($this->ctable, "customer_id", "id='" . $visitId . "'", 0);
			if ($customerId === false) {
				$customerId = 0;
			}
		}

		$customerName = isset($data['customer_name']) ? $data['customer_name'] : "";
		$paymentOption = isset($data['payment_option']) ? $this->normalizeHighRatePaymentOption($data['payment_option']) : "";
		$paymentRemark = isset($data['payment_remark']) ? $data['payment_remark'] : "";
		$followupId = isset($data['followup_id']) ? $data['followup_id'] : 0;
		$items = isset($data['items']) && is_array($data['items']) ? $data['items'] : array();
		if (!empty($items)) {
			/* Unique by slug — max 18 fixed products, no duplicates in same call */
			$items = $this->normalizeHighRateItems($items);
		}

		$rowData = array(
			"visit_id" => $visitId,
			"user_id" => isset($data['user_id']) ? $data['user_id'] : 0,
			"customer_id" => $customerId ? $customerId : 0,
			"inquiry_id" => isset($data['inquiry_id']) ? $data['inquiry_id'] : 0,
			"reason_code" => isset($data['reason_code']) ? $data['reason_code'] : "E1",
			"customer_name" => $customerName,
			"payment_option" => $paymentOption,
			"payment_remark" => $paymentRemark,
			"followup_id" => $followupId,
			"isActive" => 1,
			"isDelete" => 0,
		);

		/* Same visit_id → reuse same form (never create 2nd form for same visit) */
		$formId = $this->db->rp_getValue("visit_high_rate_form", "id", "visit_id='" . $visitId . "' AND isDelete=0", 0);
		if ($formId != "" && $formId != "0") {
			$this->db->rp_update("visit_high_rate_form", $rowData, "id='" . $formId . "'", 0);
		} else {
			$rowData["created_date"] = date("Y-m-d H:i:s");
			$columns = array_keys($rowData);
			$values = array_values($rowData);
			$formId = $this->db->rp_insert("visit_high_rate_form", $values, $columns, 0);
			if (!$formId) {
				return "";
			}
		}

		/* Same visit_id → upsert 18 items by slug (update rates, no new duplicate set) */
		if (!empty($items)) {
			$this->upsertHighRateFormItems($formId, $visitId, $items);
		}

		$this->db->rp_update(
			$this->ctable,
			array(
				"high_rate_form_id" => $formId,
				"remark_code" => "E",
				"reason_code" => "E1",
			),
			"id='" . $visitId . "'",
			0
		);
		return $formId;
	}

	/**
	 * Upsert items by visit_id + product_slug.
	 * - Existing slug for this visit → UPDATE rates
	 * - New slug → INSERT once
	 * - Extra old rows for this visit → DELETE
	 * Result: always max 1 row per product slug per visit (≈18 rows).
	 */
	private function upsertHighRateFormItems($formId, $visitId, $items)
	{
		$hasSlugCol = false;
		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `visit_high_rate_form_item` LIKE 'product_slug'");
		if ($colCheck && mysqli_num_rows($colCheck) > 0) {
			$hasSlugCol = true;
		}

		$keptIds = array();
		$sort = 0;
		foreach ($items as $item) {
			$sort++;
			if (!is_array($item)) {
				continue;
			}
			$item = $this->resolveHighRateItemRow($item);
			$productName = isset($item['product_name']) ? $item['product_name'] : "";
			$productSlug = isset($item['slug']) ? strtolower(trim($item['slug'])) : "";
			$givenRate = isset($item['given_rate']) ? $item['given_rate'] : "";
			$qty = isset($item['qty']) ? $item['qty'] : "";
			$customerRate = isset($item['customer_rate']) ? $item['customer_rate'] : "";
			$remark = isset($item['remark']) ? $item['remark'] : "";
			$itemSort = isset($item['sort_order']) && (int) $item['sort_order'] > 0 ? (int) $item['sort_order'] : $sort;
			if ($productName == "" && $productSlug == "") {
				continue;
			}
			if ($productName == "" && $productSlug != "") {
				$master = $this->getHighRateProductBySlug($productSlug);
				if ($master) {
					$productName = $master['product_name'];
				}
			}
			if ($productName == "") {
				continue;
			}

			$existingId = "";
			if ($hasSlugCol && $productSlug != "") {
				$existingId = $this->db->rp_getValue(
					"visit_high_rate_form_item",
					"id",
					"visit_id='" . $visitId . "' AND product_slug='" . $this->db->clean($productSlug) . "'",
					0
				);
			}
			if (($existingId == "" || $existingId == "0" || $existingId === false) && $productName != "") {
				$existingId = $this->db->rp_getValue(
					"visit_high_rate_form_item",
					"id",
					"visit_id='" . $visitId . "' AND product_name='" . $this->db->clean($productName) . "'",
					0
				);
			}

			$updateData = array(
				"high_rate_form_id" => $formId,
				"visit_id" => $visitId,
				"product_name" => $productName,
				"given_rate" => $givenRate,
				"qty" => $qty,
				"customer_rate" => $customerRate,
				"remark" => $remark,
				"sort_order" => $itemSort,
				"isDelete" => 0,
			);
			if ($hasSlugCol) {
				$updateData["product_slug"] = $productSlug;
			}

			if ($existingId != "" && $existingId != "0" && $existingId !== false) {
				$this->db->rp_update("visit_high_rate_form_item", $updateData, "id='" . $existingId . "'", 0);
				$keptIds[] = (int) $existingId;
			} else {
				$itemColumns = array_keys($updateData);
				$itemValues = array_values($updateData);
				$newId = $this->db->rp_insert("visit_high_rate_form_item", $itemValues, $itemColumns, 0);
				if ($newId) {
					$keptIds[] = (int) $newId;
				}
			}
		}

		/* Remove leftover rows for this visit (old duplicates / products not in this submit) */
		$whereExtra = "visit_id='" . (int) $visitId . "'";
		if (!empty($keptIds)) {
			$whereExtra .= " AND id NOT IN (" . implode(",", $keptIds) . ")";
		}
		@mysqli_query($this->db->myconn, "DELETE FROM `visit_high_rate_form_item` WHERE " . $whereExtra);
	}

	/** @deprecated kept for any old callers — redirects to upsert */
	private function insertHighRateFormItems($formId, $visitId, $items)
	{
		$this->upsertHighRateFormItems($formId, $visitId, $items);
	}

	/**
	 * Public API helper — Save / Update High Rate Analysis form (E1)
	 * Android one-shot submit by visit_id only (NO high_rate_form_id required).
	 * Does not change #122 contract.
	 */
	public function SaveHighRateDetailForm($detail)
	{
		$visitId = isset($detail['visit_id']) ? $detail['visit_id'] : "";
		$userId = isset($detail['user_id']) ? $detail['user_id'] : "";
		if ($visitId == "" || $visitId == "0") {
			return array("ack" => 0, "ack_msg" => "Visit id is required.", "developer_msg" => "visit_id missing");
		}
		if ($userId == "" || $userId == "0") {
			return array("ack" => 0, "ack_msg" => "User id is required.", "developer_msg" => "user_id missing");
		}

		$visitExists = $this->db->rp_getTotalRecord($this->ctable, "id='" . $visitId . "' AND isDelete=0", 0);
		if ($visitExists == 0) {
			return array("ack" => 0, "ack_msg" => "Visit not found.", "developer_msg" => "Invalid visit_id");
		}

		$customerId = isset($detail['customer_id']) ? $detail['customer_id'] : 0;
		if ($customerId == "" || $customerId == "0") {
			$customerId = $this->db->rp_getValue($this->ctable, "customer_id", "id='" . $visitId . "'", 0);
		}

		$customerName = isset($detail['customer_name']) ? trim($detail['customer_name']) : "";
		if ($customerName == "" && isset($detail['high_rate_customer_name'])) {
			$customerName = trim($detail['high_rate_customer_name']);
		}
		/* Optional: if App does not send name, use visit customer name */
		if ($customerName == "" && $customerId != "" && $customerId != "0") {
			$customerName = $this->db->rp_getValue("executive", "company_name", "id='" . $customerId . "' AND isDelete=0", 0);
			if ($customerName === false || $customerName == "") {
				$customerName = $this->db->rp_getValue("executive", "cname", "id='" . $customerId . "' AND isDelete=0", 0);
			}
			if ($customerName === false) {
				$customerName = "";
			}
		}
		if ($customerName == "") {
			$customerName = "Customer";
		}

		$items = array();
		if (isset($detail['items']) && is_array($detail['items'])) {
			$items = $detail['items'];
		} else if (isset($detail['high_rate_items'])) {
			if (is_array($detail['high_rate_items'])) {
				$items = $detail['high_rate_items'];
			} else if ($detail['high_rate_items'] != "") {
				$decoded = json_decode($detail['high_rate_items'], true);
				if (is_array($decoded)) {
					$items = $decoded;
				}
			}
		}

		$paymentOption = isset($detail['payment_option']) ? $this->normalizeHighRatePaymentOption(trim($detail['payment_option'])) : "";
		$paymentRemark = isset($detail['payment_remark']) ? trim($detail['payment_remark']) : "";

		$formId = $this->saveVisitHighRateForm(array(
			"visit_id" => $visitId,
			"user_id" => $userId,
			"customer_id" => $customerId ? $customerId : 0,
			"inquiry_id" => isset($detail['inquiry_id']) ? $detail['inquiry_id'] : 0,
			"reason_code" => "E1",
			"customer_name" => $customerName,
			"payment_option" => $paymentOption,
			"payment_remark" => $paymentRemark,
			"items" => $items,
		));

		if ($formId == "" || $formId == "0") {
			$dbError = $this->db->rp_getLastDbError();
			return array(
				"ack" => 0,
				"ack_msg" => "High Rate Analysis save failed.",
				"developer_msg" => ($dbError != "") ? $dbError : "Insert/Update failed",
			);
		}

		$formRow = array();
		$formRes = $this->db->rp_getData("visit_high_rate_form", "*", "id='" . $formId . "' AND isDelete=0", "", 0);
		if ($formRes) {
			$formRow = mysqli_fetch_assoc($formRes);
		}
		$itemRows = array();
		$itemRes = $this->db->rp_getData("visit_high_rate_form_item", "*", "high_rate_form_id='" . $formId . "' AND isDelete=0", "sort_order ASC", 0);
		if ($itemRes) {
			while ($ir = mysqli_fetch_assoc($itemRes)) {
				$itemRows[] = $ir;
			}
		}
		$formRow['items'] = $itemRows;

		return array(
			"ack" => 1,
			"ack_msg" => "High Rate Analysis saved successfully.",
			"developer_msg" => "High Rate Analysis saved successfully.",
			"high_rate_form_id" => (string) $formId,
			"reason_code" => "E1",
			"result" => $formRow,
		);
	}

	public function getHighRateFormFollowupText($visitId, $formId = "")
	{
		$form = null;
		if ($formId != "" && $formId != "0") {
			$r = $this->db->rp_getData("visit_high_rate_form", "*", "id='" . $formId . "' AND isDelete=0", "", 0);
			if ($r) {
				$form = mysqli_fetch_assoc($r);
			}
		}
		if (!$form && $visitId != "" && $visitId != "0") {
			$r = $this->db->rp_getData("visit_high_rate_form", "*", "visit_id='" . $visitId . "' AND isDelete=0", "id DESC", 0);
			if ($r) {
				$form = mysqli_fetch_assoc($r);
			}
		}
		if (!$form) {
			return "";
		}
		$parts = array();
		$parts[] = "High Rate Form";
		if (!empty($form['customer_name'])) {
			$parts[] = "Customer: " . $form['customer_name'];
		}
		if (!empty($form['payment_option'])) {
			$parts[] = "Payment: " . $this->getHighRatePaymentOptionLabel($form['payment_option']);
		}
		$filled = 0;
		$itemRes = $this->db->rp_getData("visit_high_rate_form_item", "*", "high_rate_form_id='" . $form['id'] . "' AND isDelete=0", "sort_order ASC", 0);
		if ($itemRes) {
			while ($ir = mysqli_fetch_assoc($itemRes)) {
				if ($ir['given_rate'] != "" || $ir['customer_rate'] != "" || $ir['qty'] != "") {
					$filled++;
				}
			}
		}
		$parts[] = "Items filled: " . $filled;
		return implode(" | ", $parts);
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
