 <?php

	require_once("function.class.php");

	require_once("class.log.php");

	class Followup extends Functions
	{
		public $db, $log;
		public $ctable = "followup";
		public $ctableVisitor = "visitor";
		function __construct($id = "")
		{

			$db = new Functions();
			$conn = $db->connect();
			$this->db = $db;
			$this->log = new Log();
		}

		public function CreateFollowup($user_id, $visitor_id, $description, $through, $followup_date, $followup_flag, $reference_id, $entry_type, $followup_status)
		{
			if ($followup_flag == "no_order_inquiry" || $followup_flag == "inquiry_followup") {
				$reference_table = "no_order_inquiry";
				$cuscol = "dealer_id";
			}
			if ($followup_flag == "customer_inquiry" || $followup_flag == "leads_followup") {
				$reference_table = "customer_inquiry";
				$cuscol = "customer_id";
			}
			if ($followup_flag == "manual_invoice_import") {
				$reference_table = "manual_invoice_import";
			}
			if ($followup_flag == "request_followup") {
				$reference_table = "request";
			}
			if ($followup_flag == "complain_followup") {
				$reference_table = "complain";
			}
			if ($followup_flag == "followup") {
				$reference_table = "sales_executive";
			}
			if ($followup_flag == "sales_executive") {
				$reference_table = "sales_executive";
			}
			if ($followup_flag == "customer_followup") {
				$reference_table = "executive";
				$cuscol = "id";
			}
			if ($followup_flag == "quotation_followup" || $followup_flag == "quotation_detail") {
				$reference_table = "quotation_detail";
				$cuscol = "customer_id";
			}

			if ($visitor_id != "") {
				$followup_detail = $this->db->rp_getData($this->ctable, "*", "visitor_id='" . $visitor_id . "' AND isDelete=0 AND next_action=1", "id DESC", 0);
			} else {
				$followup_detail = $this->db->rp_getData($this->ctable, "*", "reference_id='" . $reference_id . "' AND isDelete=0 AND next_action=1 AND reference_table='" . $reference_table . "'", "id DESC", 0);
			}

			if ($followup_detail) {
				$followup_detail = mysqli_fetch_assoc($followup_detail);
				if ($followup_detail['next_action'] == 1) {
					$next_followup_id = $followup_detail['id'];
				} else {
					$next_followup_id = 0;
				}
			} else {
				$next_followup_id = 0;
			}

			/*$refrence_media_id = $this->db->rp_getValue("executive","reference_media_id","id='".$visitor_id."'",0);
		$project_manager_id = $this->db->rp_getValue("executive","project_manager_id","id='".$visitor_id."'",0);*/

			$refrence_media_id = "";
			$project_manager_id = "";

			// added by shivani for inquity created by and reference by 
			if ($followup_flag == "no_order_inquiry" || $followup_flag == "inquiry_followup") {
				$inquiry_created_by = $this->db->rp_getValue("no_order_inquiry", "inquiry_created_by", "id='" . $reference_id . "'", 0);
				$inquiry_assign_to = $this->db->rp_getValue("no_order_inquiry", "inquiry_assign_to", "id='" . $reference_id . "'", 0);
			} else {
				$inquiry_created_by = "";
				$inquiry_assign_to = "";
			}
			// added by shivani for inquity created by and reference by 

			$count = $this->db->rp_getTotalRecord("followup", "reference_id='" . $reference_id . "' AND reference_table='" . $reference_table . "' AND isDelete=0", 0);
			//echo $count; exit;

			$Values = array($reference_table, $user_id, $visitor_id, $reference_id, $project_manager_id, $description, $through, date("Y-m-d H:i:s", strtotime($followup_date)), 0, 1, $next_followup_id, $refrence_media_id, $entry_type, $inquiry_created_by, $inquiry_assign_to);

			$Columns = array("reference_table", "user_id", "visitor_id", "reference_id", "project_manager_id", "description", "through", "followup_date", "isDelete", "isActive", "next_followup_id", "refrence_media_id", "entry_type", "inquiry_created_by", "inquiry_assign_to");

			$ContentID = $this->db->rp_insert($this->ctable, $Values, $Columns, 0);

			if ($ContentID) {
				if (/*$count==0 &&*/$reference_table != "sales_executive" && $reference_table != "quotation_detail") {
					//echo "string";exit;
					$Update = $this->db->rp_update($reference_table, array("status" => $followup_status), "id='" . $reference_id . "'", 0);
				}
				$reply = array("a" => 1, "dmg" => "Followup Successfully Created", "mg" => " Followup Successfully Created", "followup_id" => $ContentID);
			} else {
				$reply = array("a" => 0, "dmg" => "Create Followup Failed!!", "mg" => "Create Followup Failed!!");
			}
			return $reply;
		}

		public function GetFollowupContent($visitor_id, $reference_id)
		{
			$result = array();
			$Content = array();
			$limit = self::getLimit();
			$status = array("1" => "Call", "2" => "SMS", "3" => "Email");
			$status_followup = array("1" => "Responsed", "0" => "Followup Created");
			$refrence_media_id = $_REQUEST['refrence_media_id'];

			if ($_REQUEST['followup_type'] == "followup") {
				$followup_type = "sales_executive";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else if ($_REQUEST['followup_type'] == "no_order_inquiry") {
				$followup_type = "no_order_inquiry";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else if ($_REQUEST['followup_type'] == "customer_inquiry") {
				$followup_type = "customer_inquiry";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else if ($_REQUEST['followup_type'] == 3) {
				$followup_type = "request";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else if ($_REQUEST['followup_type'] == 4) {
				$followup_type = "complain";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else if ($_REQUEST['followup_type'] == 'quotation_followup') {
				$followup_type = "quotation_detail";
				$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
			} else {
				$followup_type_where .= "";
			}

			// echo $followup_type;exit;
			if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
				$Where .= " AND DATE(followup_date) <= '" . date("Y-m-d", strtotime($_REQUEST['ToDate'])) . "' AND isDelete=0";
			}

			if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
				$Where .= " AND DATE(followup_date) >= '" . date("Y-m-d", strtotime($_REQUEST['FromDate'])) . "' AND isDelete=0 ";
			}
			// added by shivani
			$exeR = $this->db->rp_getData("executive", "id", "seid='" . $_REQUEST['sales_id'] . "' AND isDelete=0 AND isActive=1", "", 0);
			$exeArr = array();
			if ($exeR) {
				while ($exeD = mysqli_fetch_assoc($exeR)) {
					$exeArr[] = $exeD['id'];
				}
			}
			// added by shivani
			if ($_REQUEST['customer_id'] == "" && $_REQUEST['reference_id'] == "") {
				if ($exeArr) {
					$Contents = $this->db->rp_getTotalRecord($this->ctable, $followup_type_where . "isDelete='0' AND isActive='1' AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "' OR visitor_id='" . implode(",", $exeArr) . "')", 0);
				} else {
					$Contents = $this->db->rp_getTotalRecord($this->ctable, $followup_type_where . "isDelete='0' AND isActive='1' AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "')", 0);
				}
			} else if ($_REQUEST['sales_id'] != "" && $_REQUEST['customer_id'] == "") {
				$refrence_id = $this->db->rp_getValue("no_order_inquiry", "id", "dealer_id='" . $_REQUEST['customer_id'] . "' AND isDelete=0", 0);
				if ($exeArr) {
					$Contents = $this->db->rp_getTotalRecord($this->ctable, "isDelete='0' AND isActive='1' AND (visitor_id='" . $visitor_id . "' OR reference_id='" . $refrence_id . "') AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "' OR visitor_id='" . implode(",", $exeArr) . "')", 0);
				} else {
					$Contents = $this->db->rp_getTotalRecord($this->ctable, "isDelete='0' AND isActive='1' AND (visitor_id='" . $visitor_id . "' OR reference_id='" . $refrence_id . "') AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "')", 0);
				}
			} else if ($_REQUEST['sales_id'] != "" && $_REQUEST['customer_id'] != "") {
				$Contents = $this->db->rp_getTotalRecord($this->ctable, "isDelete='0' AND isActive='1' AND (visitor_id='" . $_REQUEST['customer_id'] . "' OR reference_id='" . $_REQUEST['customer_id'] . "') AND reference_table = 'executive'", 0);
			} else if ($_REQUEST['reference_id'] != "") {
				$Contents = $this->db->rp_getTotalRecord($this->ctable, "isDelete='0' AND isActive='1' AND reference_id='" . $_REQUEST['reference_id'] . "' AND reference_table='" . $followup_type . "'", 0);
			} else {
				$Contents = $this->db->rp_getTotalRecord($this->ctable, "isDelete='0' AND isActive='1' AND visitor_id='" . $visitor_id . "'", 0);
			}

			//print_r($Contents); exit;

			if ($Contents > 0) {
				if ($_REQUEST['customer_id'] != "" && $_REQUEST['reference_id'] == "") {
					// $refrence_id = $this->db->rp_getValue("no_order_inquiry","id","dealer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
					// $FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND (visitor_id='".$visitor_id."' AND user_id='".$_REQUEST['sales_id']."') ".$Where,"followup_date DESC",0,$limit);
					if ($exeArr) {
						$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND (visitor_id='" . $_REQUEST['customer_id'] . "' OR reference_id='" . $_REQUEST['customer_id'] . "') AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "' OR visitor_id='" . implode(",", $exeArr) . "') AND reference_table = 'executive'" . $Where, "followup_date DESC", 0, $limit);
					} else {
						$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND (visitor_id='" . $_REQUEST['customer_id'] . "' OR reference_id='" . $_REQUEST['customer_id'] . "') AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "') AND reference_table = 'executive'" . $Where, "followup_date DESC", 0, $limit);
					}
				} else if ($_REQUEST['customer_id'] == "" && $_REQUEST['reference_id'] == "") {
					if ($exeArr) {
						$FollowupContent = $this->db->rp_getData($this->ctable, "*", $followup_type_where . "isDelete='0' AND isActive='1' AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "' OR visitor_id='" . implode(",", $exeArr) . "') " . $Where, "followup_date DESC", 0, $limit);
					} else {
						$FollowupContent = $this->db->rp_getData($this->ctable, "*", $followup_type_where . "isDelete='0' AND isActive='1' AND (user_id='" . $_REQUEST['sales_id'] . "' OR inquiry_assign_to='" . $_REQUEST['sales_id'] . "' OR inquiry_created_by='" . $_REQUEST['sales_id'] . "') " . $Where, "followup_date DESC", 0, $limit);
					}
				} else {
					$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND  reference_id='" . $_REQUEST['reference_id'] . "' AND reference_table='" . $followup_type . "'" . $Where, "followup_date DESC", 0, $limit);
				}

				$customer_flag_array = array("0" => "Customer", "1" => "Prospect Customer");

				if ($FollowupContent) {
					while ($FollowupContent_d = mysqli_fetch_assoc($FollowupContent)) {

						if ($FollowupContent_d['reference_table'] == "sales_executive") {
							$FollowupContent_d['followup_slug'] = "Followup";
						} else if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
							$FollowupContent_d['followup_slug'] = "Inquiry Followup";
						} else if ($FollowupContent_d['reference_table'] == "request") {
							$FollowupContent_d['followup_slug'] = "Request Followup";
						} else if ($FollowupContent_d['reference_table'] == "complain") {
							$FollowupContent_d['followup_slug'] = "Complain Followup";
						}
						$FollowupContent_d['type_slug'] = $status[$FollowupContent_d['through']];
						$FollowupContent_d['status_slug'] = $status_followup[$FollowupContent_d['status']];
						if ($FollowupContent_d['next_action'] == -1) {
							$FollowupContent_d['status_slug'] = "Followup End";
						}
						$FollowupContent_d['refrence_media_id'] = $FollowupContent_d['refrence_media_id'];
						$FollowupContent_d['refrence_media_id'] = $this->db->rp_getValue("reference_media", "name", "id='" . $FollowupContent_d['refrence_media_id'] . "'", 0);
						$FollowupContent_d['followup_date'] = ($FollowupContent_d['followup_date'] != "0000-00-00 00:00:00") ? date('d F Y H:i', strtotime($FollowupContent_d['followup_date'])) : "";
						$FollowupContent_d['future_date'] = ($FollowupContent_d['future_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['future_date'])) : "";
						$FollowupContent_d['response_date'] = ($FollowupContent_d['response_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['response_date'])) : "";
						$FollowupContent_d['created_date'] = ($FollowupContent_d['created_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['created_date'])) : "";
						$FollowupContent_d['day'] =  date('l', strtotime($FollowupContent_d['followup_date']));
						$getName = $this->db->rp_getValue("sales_executive", "name", "id='" . $FollowupContent_d['user_id'] . "'", 0);
						$FollowupContent_d['user_name'] = $getName;
						$category_id = $this->db->rp_getValue("visitor", "category_id", "id='" . $FollowupContent_d['visitor_id'] . "' and isDelete=0", "", 0);
						if ($category_id != 0) {
							$category_name = $this->db->rp_getValue("category", "name", "id='" . $category_id . "' and isDelete=0", "", 0);
						} else {
							$category_name = "Other";
						}
						$FollowupContent_d['category_name'] = $category_name;

						if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
							$visitor_detail = $this->db->rp_getData("no_order_inquiry", "*", "id='" . $FollowupContent_d['reference_id'] . "'", "", 0);

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $visitor_detail['company_name'];
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_number'];
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['inquiry_id'] = $visitor_detail['id'];
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['inquiry_id'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						} else if ($FollowupContent_d['reference_table'] == "customer_inquiry") {
							$visitor_detail = $this->db->rp_getData("customer_inquiry", "*", "id='" . $FollowupContent_d['reference_id'] . "'", "", 0);

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $visitor_detail['company_name'];
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_number'];
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['inquiry_id'] = $visitor_detail['id'];
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['inquiry_id'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						} else if ($FollowupContent_d['reference_table'] == "request") {
							$visitor_detail = $this->db->rp_getData("request", "*", "id='" . $FollowupContent_d['reference_id'] . "'");

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $this->db->rp_getValue("executive", "company_name", "id='" . $visitor_detail['customer_id'] . "'");
								$FollowupContent_d['mobile_no'] = $this->db->rp_getValue("executive", "mobile_no1", "id='" . $visitor_detail['customer_id'] . "'");
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						} else if ($FollowupContent_d['reference_table'] == "quotation_detail") {
							$visitor_detail = $this->db->rp_getData("quotation_detail", "*", "id='" . $FollowupContent_d['reference_id'] . "'");

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $this->db->rp_getValue("executive", "company_name", "id='" . $visitor_detail['customer_id'] . "'");
								$FollowupContent_d['mobile_no'] = $this->db->rp_getValue("executive", "mobile_no1", "id='" . $visitor_detail['customer_id'] . "'");
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['client_code'] = $visitor_detail['client_code'];
								$FollowupContent_d['customer_name'] = $visitor_detail['customer_name'];
								$FollowupContent_d['customer_flag'] = $this->db->rp_getValue("executive", "customer_flag", "id='" . $visitor_detail['customer_id'] . "'");
								$FollowupContent_d['customer_flag'] = $customer_flag_array[$FollowupContent_d['customer_flag']];
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						} else if ($FollowupContent_d['reference_table'] == "executive" && $FollowupContent_d['entry_type'] != 2) {
							$visitor_detail = $this->db->rp_getData("executive", "*", "id='" . $FollowupContent_d['reference_id'] . "'", "");

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $visitor_detail['company_name'];
								$FollowupContent_d['customer_name'] = $visitor_detail['cname'];
								$FollowupContent_d['client_code'] = $visitor_detail['client_code'];
								$FollowupContent_d['email'] = $visitor_detail['email'];
								$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_no1'];
								$FollowupContent_d['rating'] = $visitor_detail['rating'];
								$FollowupContent_d['remark'] = $visitor_detail['remark'];
								$FollowupContent_d['customer_flag'] = $customer_flag_array[$visitor_detail['customer_flag']];
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						} else {
							$visitor_detail = $this->db->rp_getData("executive", "*", "id='" . $FollowupContent_d['visitor_id'] . "'", "");

							if ($visitor_detail) {
								$visitor_detail = mysqli_fetch_assoc($visitor_detail);
								$FollowupContent_d['name'] = $visitor_detail['company_name'];
								$FollowupContent_d['customer_name'] = $visitor_detail['cname'];
								$FollowupContent_d['client_code'] = $visitor_detail['client_code'];
								$FollowupContent_d['email'] = $visitor_detail['email'];
								$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_no1'];
								$FollowupContent_d['rating'] = $visitor_detail['rating'];
								$FollowupContent_d['remark'] = $visitor_detail['remark'];
								$FollowupContent_d['customer_flag'] = $customer_flag_array[$visitor_detail['customer_flag']];
							} else {
								$FollowupContent_d['name'] = "";
								$FollowupContent_d['email'] = "";
								$FollowupContent_d['mobile_no'] = "";
								$FollowupContent_d['rating'] = "";
								$FollowupContent_d['remark'] = "";
								$FollowupContent_d['customer_name'] = "";
								$FollowupContent_d['client_code'] = "";
								$FollowupContent_d['customer_flag'] = "";
							}
						}
						$Content[] = $FollowupContent_d;
					}
					$reply = array("ack" => 1, "developer_msg" => "Followup Get Sussess!!", "ack_msg" => "Followup Get Sussess!!", "result" => $Content);
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
				}
			} else {

				$reply = array("ack" => 0, "developer_msg" => "Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
			}
			return $reply;
		}
		public function AddFollowupResponse($response, $followup_action, $followup_id, $followup_future_date, $followup_reason_id, $entry_type, $followup_status)
		{

			$result = array();
			/*$Count=$this->db->rp_getTotalRecord($this->ctable,"response='".$response."' AND isDelete='0' AND isActive='1'",0);
		if($Count<=0)
		{*/
			if ($followup_future_date == "1970-01-01 05:30:00") {
				$followup_future_date = "";
			}

			if ($followup_reason_id == "") {
				$followup_reason_id = "1";
			}
			// $entry_type = "1";
			$Values = array("response" => $response, "next_action" => $followup_action, "response_date" => date("Y-m-d H:i:s"), "status" => 1, "future_date" => $followup_future_date, "followup_reason_id" => $followup_reason_id);
			$ContentID = $this->db->rp_update($this->ctable, $Values, "id='" . $followup_id . "'", 0);
			if ($ContentID) {
				/*update inquiry status*/
				$followup_reason = $this->db->rp_getValue("followup_reason", "name", "id='" . $followup_reason_id . "' AND isDelete=0");
				$inquiry_id = $this->db->rp_getValue("followup", "reference_id", "id='" . $followup_id . "' AND isDelete=0");

				$UPDATE = $this->db->rp_update("no_order_inquiry", array("followup_reason_id" => $followup_reason_id, "status" => $followup_status), "id='" . $inquiry_id . "'", 0);
				if ($UPDATE) {
					$this->db->addStatusTimelineEntry($inquiry_id, $followup_status, isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : '');
				}
				// if($followup_reason=="Non Relevant Inquiry")
				// {
				// 	$UPDATE = $this->db->rp_update("no_order_inquiry",array("status"=>"-2"),"id='".$inquiry_id."'",0);
				// }
				/*update inquiry status*/

				if ($followup_action == -1) {
					$visitor_id = $this->db->rp_getValue($this->ctable, "visitor_id", "id='" . $followup_id . "'", 0);
					//$this->db->rp_update("visitor",array("isActive"=>0),"id='".$visitor_id."'",0);
				}
				$reply = array("a" => 1, "dmg" => "Response Successfully Added", "mg" => "Response Successfully Added", "followup_id" => $followup_id, "reference_id" => $inquiry_id);
			} else {
				$reply = array("a" => 0, "dmg" => "Response Added Failed!!", "mg" => "Response Added Failed!!");
			}
			/*}
		else
		{
			$reply=array("a"=>0,"dmg"=>"This Followup Response Already Exist","mg"=>"This Followup Response Already Exist");
		}*/
			return $reply;
		}

		public function GetTodayFollowup($user_id, $limit = "")
		{
			$vistors = array();
			$inquiry = array();
			// $admin_type=$this->db->rp_getValue("sales_executive","type","id='".$user_id."'");
			/*if($admin_type==0)
		{
			$visitorsR=$this->db->rp_getData("executive","id,cname","isDelete=0 AND isActive=1","",0);
		}
		else if($admin_type==2)
		{
			$visitorsR=$this->db->rp_getData($this->ctableVisitor,"id,name","user_id='".$user_id."' AND isDelete=0 AND isActive=1","",0);	
		}
		else 
		{
			$visitorsR=$this->db->rp_getData($this->ctableVisitor,"id,name","project_id='".$project_id."' AND ( user_id='".$user_id."' OR project_manager_id=0 OR project_manager_id='".$user_id."' ) AND isDelete=0 AND isActive=1","",0);	
		}*/

			$visitorsR = $this->db->rp_getData("executive", "id,company_name", "isDelete=0 AND isActive=1", "", 0);
			if ($visitorsR) {
				while ($v = mysqli_fetch_assoc($visitorsR)) {
					$vistors[] = $v['id'];
				}
			}

			$InquiryR = $this->db->rp_getData("no_order_inquiry", "id,company_name", "isDelete=0 AND isActive=1", "", 0);
			if ($InquiryR) {
				while ($INQUIRY_D = mysqli_fetch_assoc($InquiryR)) {

					$inquiry[] = $INQUIRY_D['id'];
				}
			}

			if ($_REQUEST['executive_id'] != '') {
				$executive_id = $_REQUEST['executive_id'];
				$visitorsR = $this->db->rp_getData($this->ctableVisitor, "id,name", "user_id='" . $executive_id . "' AND isDelete=0 AND isActive=1", "", 0);
				$vistors = array();
				if ($visitorsR) {
					while ($v = mysqli_fetch_assoc($visitorsR)) {
						$vistors[] = $v['id'];
					}
				}
			}

			$result = array();
			$Content = array();
			$limit = self::getLimit();
			$status = array("1" => "Call", "2" => "SMS", "3" => "Email");
			$status_followup = array("1" => "Responsed", "0" => "Followup Created");
			if (!empty($vistors) || !empty($inquiry)) {
				$salesType = $this->db->rp_getValue("sales_executive", "type", "id='" . $user_id . "'", 0);
				$where1 = "isDelete='0' AND isActive='1' AND visitor_id IN (" . implode(",", $vistors) . ") ";
				if ($salesType == '0') {
					$where .= " AND isDelete=0";
				} else if ($salesType == '2') {
					$where .= " AND isDelete=0";
				} else {
					$exeR = $this->db->rp_getData("executive", "id", "seid='" . $user_id . "' AND isDelete=0 AND isActive=1", "", 0);
					$exeArr = array();
					if ($exeR) {
						while ($exeD = mysqli_fetch_assoc($exeR)) {
							$exeArr[] = $exeD['id'];
						}
					}
					// print_r($exeArr);exit;
					// OR project_manager_id=0 remove from below query by shivani
					if ($exeArr) {
						$where .= " AND (user_id='" . $user_id . "' OR project_manager_id='" . $user_id . "' OR inquiry_assign_to='" . $user_id . "' OR inquiry_created_by='" . $user_id . "' OR visitor_id='" . implode(",", $exeArr) . "')";
					} else {
						$where .= " AND (user_id='" . $user_id . "' OR project_manager_id='" . $user_id . "' OR inquiry_assign_to='" . $user_id . "' OR inquiry_created_by='" . $user_id . "')";
					}
				}

				$Contents = $this->db->rp_getTotalRecord($this->ctable, $where1, 0);
				//$Contents=$this->db->rp_getTotalRecord($this->ctable,"user_id ='".$user_id."' AND isDelete=0 AND isActive=1",0);
				// exit("helloooo");
				if ($Contents > 0) {
					//$today = date("Y-m-d");
					$today = $_REQUEST['todate'] ? date('Y-m-d', strtotime($_REQUEST['todate'])) : "";
					$fromdate = $_REQUEST['fromdate'] ? date('Y-m-d', strtotime($_REQUEST['fromdate'])) : "";
					$refrence_media_id = $_REQUEST['refrence_media_id'];

					if ($_REQUEST['mode'] == 'future') {
						if ($refrence_media_id == '' && $today == '' && $fromdate == '') {
							$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND DATE(followup_date)>'" . date('Y-m-d') . "' AND visitor_id IN (" . implode(",", $vistors) . ")" . $where, "followup_date ASC", 0, $limit);
						} else if ($refrence_media_id == '') {
							$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND DATE(followup_date)<='" . $today . "' AND DATE(followup_date)>='" . $fromdate . "' AND visitor_id IN (" . implode(",", $vistors) . ")" . $where, "followup_date ASC", 0, $limit);
						} else {
							$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1'  AND DATE(followup_date)<='" . $today . "' AND DATE(followup_date)>='" . $fromdate . "' AND refrence_media_id=" . $refrence_media_id . " AND visitor_id IN (" . implode(",", $vistors) . ")" . $where, "followup_date ASC", 0, $limit);
						}
					} else if ($refrence_media_id != '') {
						// echo $refrence_media_id ; exit;
						$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND DATE(followup_date)='" . date("Y-m-d") . "' AND refrence_media_id=" . $refrence_media_id . " AND visitor_id IN (" . implode(",", $vistors) . ")" . $where, "followup_date ASC", 0, $limit);
					} else {
						if ($_REQUEST['followup_type'] == "followup") {
							$followup_type = "sales_executive";
							/*$followup_type = "no_order_inquiry";*/
							$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
						} else if ($_REQUEST['followup_type'] == "no_order_inquiry") {
							$followup_type = "no_order_inquiry";
							/*$followup_type = "sales_executive";	*/
							$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
						} else if ($_REQUEST['followup_type'] == "customer_inquiry") {
							$followup_type = "customer_inquiry";
							/*$followup_type = "sales_executive";	*/
							$followup_type_where .= "reference_table='" . $followup_type . "' AND ";
							$InquiryR = $this->db->rp_getData("customer_inquiry", "id,company_name", "isDelete=0 AND isActive=1", "", 0);
							if ($InquiryR) {
								while ($INQUIRY_D = mysqli_fetch_assoc($InquiryR)) {

									$inquiry[] = $INQUIRY_D['id'];
								}
							}
						}

						if ($_REQUEST['sales_id'] != "") {
							//$FollowupContent=$this->db->rp_getData($this->ctable,"*",$followup_type_where."isDelete='0' AND isActive='1' AND DATE(followup_date)='".date("Y-m-d")."' AND user_id='".$_REQUEST['sales_id']."' AND (visitor_id IN ('".implode(",",$vistors)."') OR reference_id IN (".implode(",", $inquiry)."))".$where,"followup_date ASC",1,$limit);

							// AND user_id='".$_REQUEST['sales_id']."' remove from query by shivani
							$FollowupContent = $this->db->rp_getData($this->ctable, "*", $followup_type_where . "isDelete='0' AND isActive='1' AND DATE(followup_date)<='" . date("Y-m-d") . "' AND response='' " . $where, "followup_date DESC", 0, $limit);
						} else {
							$FollowupContent = $this->db->rp_getData($this->ctable, "*", $followup_type_where . "isDelete='0' AND isActive='1' AND DATE(followup_date)='" . date("Y-m-d") . "' AND (visitor_id IN ('" . implode(",", $vistors) . "') OR reference_id IN (" . implode(",", $inquiry) . ")" . $where, "followup_date ASC", 0, $limit);
						}
					}
					$customer_flag_array = array("0" => "Customer", "1" => "Prospect Customer");
					if ($FollowupContent) {
						while ($FollowupContent_d = mysqli_fetch_assoc($FollowupContent)) {
							if ($FollowupContent_d['reference_table'] == "sales_executive") {
								$FollowupContent_d['followup_slug'] = "Followup";
							} else if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
								$FollowupContent_d['followup_slug'] = "Inquiry Followup";
							} else if ($FollowupContent_d['reference_table'] == "request") {
								$FollowupContent_d['followup_slug'] = "Request Followup";
							} else if ($FollowupContent_d['reference_table'] == "complain") {
								$FollowupContent_d['followup_slug'] = "Complain Followup";
							} else if ($FollowupContent_d['reference_table'] == "customer_inquiry") {
								$FollowupContent_d['followup_slug'] = "Customer Inquiry Followup";
							}

							$FollowupContent_d['customer_id'] = $FollowupContent_d['visitor_id'];

							$FollowupContent_d['type_slug'] = $status[$FollowupContent_d['through']];

							$FollowupContent_d['status_slug'] = $status_followup[$FollowupContent_d['status']];

							if ($FollowupContent_d['next_action'] == -1) {
								$FollowupContent_d['status_slug'] = "Followup End";
							}

							$FollowupContent_d['refrence_media_id'] = $FollowupContent_d['refrence_media_id'];

							$FollowupContent_d['refrence_media_id'] = $this->db->rp_getValue("reference_media", "name", "id='" . $FollowupContent_d['refrence_media_id'] . "'", 0);

							$FollowupContent_d['followup_date'] = ($FollowupContent_d['followup_date'] != "0000-00-00 00:00:00") ? date('d F Y H:i', strtotime($FollowupContent_d['followup_date'])) : "";

							$FollowupContent_d['future_date'] = ($FollowupContent_d['future_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['future_date'])) : "";

							$FollowupContent_d['response_date'] = ($FollowupContent_d['response_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['response_date'])) : "";

							$FollowupContent_d['created_date'] = ($FollowupContent_d['created_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i', strtotime($FollowupContent_d['created_date'])) : "";

							$FollowupContent_d['day'] =  date('l', strtotime($FollowupContent_d['followup_date']));

							$FollowupContent_d['user_name'] = $getName;

							//$visitor_detail=$this->db->rp_getData($this->ctableVisitor,"*","id='".$FollowupContent_d['visitor_id']."'");
							//$visitor_detail=$this->db->rp_getData("executive","*","id='".$FollowupContent_d['visitor_id']."'");

							$getName = $this->db->rp_getValue("sales_executive", "name", "id='" . $FollowupContent_d['user_id'] . "'", 0);

							$FollowupContent_d['user_name'] = $getName;

							if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
								$visitor_detail = $this->db->rp_getData("no_order_inquiry", "*", "id='" . $FollowupContent_d['reference_id'] . "'", "", 0);
								if ($visitor_detail) {
									$visitor_detail = mysqli_fetch_assoc($visitor_detail);
									//print_r($visitor_detail);exit;

									//echo $visitor_detail['company_name'];exit;
									$FollowupContent_d['name'] = $visitor_detail['company_name'];
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_number'];
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								} else {
									$FollowupContent_d['name'] = "";
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								}
							} else if ($FollowupContent_d['reference_table'] == "customer_inquiry") {
								$visitor_detail = $this->db->rp_getData("customer_inquiry", "*", "id='" . $FollowupContent_d['reference_id'] . "'");

								if ($visitor_detail) {
									$visitor_detail = mysqli_fetch_assoc($visitor_detail);
									$FollowupContent_d['name'] = $visitor_detail['company_name'];
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_number'];
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								} else {
									$FollowupContent_d['name'] = "";
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								}
							} else if ($FollowupContent_d['reference_table'] == "request") {
								$visitor_detail = $this->db->rp_getData("request", "*", "id='" . $FollowupContent_d['reference_id'] . "'");

								if ($visitor_detail) {
									$visitor_detail = mysqli_fetch_assoc($visitor_detail);
									$FollowupContent_d['name'] = $this->db->rp_getValue("executive", "company_name", "id='" . $visitor_detail['customer_id'] . "'");
									$FollowupContent_d['mobile_no'] = $this->db->rp_getValue("executive", "mobile_no1", "id='" . $visitor_detail['customer_id'] . "'");
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								} else {
									$FollowupContent_d['name'] = "";
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								}
							} else if ($FollowupContent_d['reference_table'] == "executive" && $FollowupContent_d['entry_type'] != 2) {
								$visitor_detail = $this->db->rp_getData("executive", "*", "id='" . $FollowupContent_d['reference_id'] . "'", "");

								if ($visitor_detail) {
									$visitor_detail = mysqli_fetch_assoc($visitor_detail);
									$FollowupContent_d['name'] = $visitor_detail['company_name'];
									$FollowupContent_d['email'] = $visitor_detail['email'];
									$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_no1'];
									$FollowupContent_d['rating'] = $visitor_detail['rating'];
									$FollowupContent_d['remark'] = $visitor_detail['remark'];
									$FollowupContent_d['customer_name'] = $visitor_detail['cname'];
									$FollowupContent_d['client_code'] = $visitor_detail['client_code'];
									$FollowupContent_d['customer_flag'] = $customer_flag_array[$visitor_detail['customer_flag']];
								} else {
									$FollowupContent_d['name'] = "";
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								}
							} else {
								//print_r($FollowupContent_d);exit;

								$visitor_detail = $this->db->rp_getData("executive", "*", "id='" . $FollowupContent_d['visitor_id'] . "'");

								if ($visitor_detail) {
									$visitor_detail = mysqli_fetch_assoc($visitor_detail);
									$FollowupContent_d['name'] = $visitor_detail['company_name'];
									$FollowupContent_d['email'] = $visitor_detail['email'];
									$FollowupContent_d['mobile_no'] = $visitor_detail['mobile_no1'];
									$FollowupContent_d['rating'] = $visitor_detail['rating'];
									$FollowupContent_d['remark'] = $visitor_detail['remark'];
									$FollowupContent_d['customer_name'] = $visitor_detail['cname'];
									$FollowupContent_d['client_code'] = $visitor_detail['client_code'];
									$FollowupContent_d['customer_flag'] = $customer_flag_array[$visitor_detail['customer_flag']];
								} else {
									$FollowupContent_d['name'] = "";
									$FollowupContent_d['email'] = "";
									$FollowupContent_d['mobile_no'] = "";
									$FollowupContent_d['rating'] = "";
									$FollowupContent_d['remark'] = "";
									$FollowupContent_d['customer_name'] = "";
									$FollowupContent_d['client_code'] = "";
									$FollowupContent_d['customer_flag'] = "";
								}
							}
							$category_id = $this->db->rp_getValue("visitor", "category_id", "id='" . $FollowupContent_d['visitor_id'] . "' and isDelete=0", "", 0);
							if ($category_id != 0) {
								$category_name = $this->db->rp_getValue("category", "name", "id='" . $category_id . "' and isDelete=0", "", 0);
							} else {
								$category_name = "Other";
							}
							$FollowupContent_d['category_name'] = $category_name;
							$Content[] = $FollowupContent_d;
						}
					}
					if (!empty($Content)) {
						$reply = array("ack" => 1, "developer_msg" => "Todays Followup Get Sussess!!", "ack_msg" => "Followup Get Sussess!!", "result" => $Content);
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Todays Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Todays Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Todays Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
			}
			return $reply;
		}

		public function GetFollowupDetail($followup_id)
		{
			$Content = array();
			$status = array("1" => "Call", "2" => "SMS", "3" => "Email");

			$status_followup = array("1" => "Responsed", "0" => "Followup Created");

			$FollowupContent = $this->db->rp_getData($this->ctable, "*", "isDelete='0' AND isActive='1' AND id='" . $followup_id . "'", "", 0);

			if ($FollowupContent) {
				$FollowupContent_d = mysqli_fetch_assoc($FollowupContent);

				if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
					$FollowupContent_d['followup_slug'] = "inquiry_followup";
					$FollowupContent_d['inquiry_id'] = $FollowupContent_d['reference_id'];
				} else if ($FollowupContent_d['reference_table'] == "customer_inquiry") {
					$FollowupContent_d['followup_slug'] = "customer_inquiry";
				} else if ($FollowupContent_d['reference_table'] == "request") {
					$FollowupContent_d['followup_slug'] = "followup_request";
				} else if ($FollowupContent_d['reference_table'] == "complain") {
					$FollowupContent_d['followup_slug'] = "followup_complain";
				} else if ($FollowupContent_d['reference_table'] == "sales_executive") {
					$FollowupContent_d['followup_slug'] = "followup";
				} else if ($FollowupContent_d['reference_table'] == "executive") {
					$FollowupContent_d['followup_slug'] = "customer_followup";
				} else if ($FollowupContent_d['reference_table'] == "quotation_detail") {
					$FollowupContent_d['followup_slug'] = "quotation_followup";
				}

				$FollowupContent_d['type_slug'] = $status[$FollowupContent_d['through']];

				$FollowupContent_d['status_slug'] = $status_followup[$FollowupContent_d['status']];

				$FollowupContent_d['followup_date'] = ($FollowupContent_d['followup_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i:s', strtotime($FollowupContent_d['followup_date'])) : "";

				$FollowupContent_d['future_date'] = ($FollowupContent_d['future_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i:s', strtotime($FollowupContent_d['future_date'])) : "";

				$FollowupContent_d['response_date'] = ($FollowupContent_d['response_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i:s', strtotime($FollowupContent_d['response_date'])) : "";

				$FollowupContent_d['created_date'] = ($FollowupContent_d['created_date'] != "0000-00-00 00:00:00") ? date('d-m-Y H:i:s', strtotime($FollowupContent_d['created_date'])) : "";

				if ($FollowupContent_d['reference_table'] == "no_order_inquiry") {
					$FollowupContent_d['visitor_name'] = $this->rp_getValue("no_order_inquiry", "company_name", "id='" . $FollowupContent_d['reference_id'] . "'");
				} else {
					$FollowupContent_d['visitor_name'] = $this->rp_getValue("executive", "cname", "id='" . $FollowupContent_d['visitor_id'] . "'");
				}
				$FollowupContent_d['visitor_mobile'] = $this->rp_getValue("executive", "phone", "id='" . $FollowupContent_d['visitor_id'] . "'");

				$Content = $FollowupContent_d;

				$reply = array("ack" => 1, "developer_msg" => "Followup Get Successfully!!", "ack_msg" => "Followup Get Successfully!!", "result" => $Content);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Followup Not Found!!", "ack_msg" => "Followup Not Found!!");
			}
			return $reply;
		}

		public function EditFollowupResponse($response, $followup_id)

		{

			$result = array();



			$Values = array("response" => $response, "response_date" => date("Y-m-d H:i:s"));

			$ContentID = $this->db->rp_update($this->ctable, $Values, "id='" . $followup_id . "'", 0);

			if ($ContentID) {

				$reply = array("a" => 1, "dmg" => "Response Successfully Updated", "mg" => "Response Successfully Updated", "followup_id" => $followup_id);
			} else {

				$reply = array("a" => 0, "dmg" => "Response Added Failed!!", "mg" => "Response Added Failed!!");
			}

			return $reply;
		}

		public function DeleteFollowup($detail)
		{
			// echo "jkjk"; exit;
			$rows 	= array(
				"isDelete"	=> "1"
			);
			$where	= "id='" . $_REQUEST['id'] . "'";
			$uid = $this->db->rp_update("followup", $rows, $where, 0);
			if ($uid != 0) {
				$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Followup Successfully.");
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete Followup Failed.");
				return $reply;
			}
		}

		function getLimit()
		{

			$limit = array();

			if (isset($_REQUEST['ul'])) {

				$limit['ul'] = $_REQUEST['ul'];
			}

			if (isset($_REQUEST['ll'])) {

				$limit['ll'] = $_REQUEST['ll'];
			}

			if ($limit != "" && !empty($limit) && array_key_exists("ul", $limit) && array_key_exists("ll", $limit)) {

				return $limit['ul'] . "," . $limit['ll'];
			} else {

				return "";
			}
		}
	}



	?>