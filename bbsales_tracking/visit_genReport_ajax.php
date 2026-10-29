<?php
$src_page_id = isset($_REQUEST['src_page_id']) ? (int) $_REQUEST['src_page_id'] : 599;
$page_id = in_array($src_page_id, array(577, 599), true) ? $src_page_id : 599;
$page_slug = ($page_id === 577) ? 'visit_page' : 'visit_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php');

// $file_name  = "Visit_Report"."_".date("d-m-Y").".xlsx";
$file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";

if (!function_exists('armor_visit_excel_parse_date')) {
	function armor_visit_excel_parse_date($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return '';
		}
		$dt = DateTime::createFromFormat('d-m-Y', $value);
		if ($dt instanceof DateTime) {
			return $dt->format('Y-m-d');
		}
		$ts = strtotime($value);
		return ($ts !== false) ? date('Y-m-d', $ts) : '';
	}
}

function armor_visit_excel_ids($raw)
{
	if (is_array($raw)) {
		$ids = array_map('intval', $raw);
	} else {
		$ids = array_map('intval', explode(',', (string) $raw));
	}
	$ids = array_values(array_filter($ids));
	return !empty($ids) ? implode(',', $ids) : '';
}

$searchName = isset($_REQUEST['searchName']) ? trim(urldecode($_REQUEST['searchName'])) : '';
if ($searchName !== '' && strtolower($searchName) !== 'undefined' && strtolower($searchName) !== 'null') {
	$searchName = mysqli_real_escape_string($db->myconn, $searchName);
	$USER_IDS = array();
	$CUSTOMER_IDS = array();
	$INQ_IDS = array();
	$sales_id = $db->rp_getData("sales_executive", "id", "name LIKE '%" . $searchName . "%' AND isDelete=0", "", 0);
	if ($sales_id) {
		while ($K = mysqli_fetch_assoc($sales_id)) {
			$USER_IDS[] = (int) $K['id'];
		}
	}
	$customer_r = $db->rp_getData("executive", "id", "(cname LIKE '%" . $searchName . "%' OR phone LIKE '%" . $searchName . "%' OR mobile_no1 LIKE '%" . $searchName . "%' OR company_name LIKE '%" . $searchName . "%' OR client_code LIKE '%" . $searchName . "%') AND isDelete=0", "", 0);
	if ($customer_r) {
		while ($K1 = mysqli_fetch_assoc($customer_r)) {
			$CUSTOMER_IDS[] = (int) $K1['id'];
		}
	}
	$inquiry_r = $db->rp_getData("no_order_inquiry", "id", "(person_name LIKE '%" . $searchName . "%' OR mobile_number LIKE '%" . $searchName . "%' OR company_name LIKE '%" . $searchName . "%') AND isDelete=0", "", 0);
	if ($inquiry_r) {
		while ($D1 = mysqli_fetch_assoc($inquiry_r)) {
			$INQ_IDS[] = (int) $D1['id'];
		}
	}
	$parts = array();
	if (!empty($USER_IDS)) {
		$parts[] = "user_id IN (" . implode(",", $USER_IDS) . ")";
	}
	if (!empty($CUSTOMER_IDS)) {
		$parts[] = "customer_id IN (" . implode(",", $CUSTOMER_IDS) . ")";
	}
	if (!empty($INQ_IDS)) {
		$parts[] = "inquiry_id IN (" . implode(",", $INQ_IDS) . ")";
	}
	if (!empty($parts)) {
		$ctable_where .= "(" . implode(" OR ", $parts) . ") AND ";
	} else {
		$ctable_where .= "1=0 AND ";
	}
}

if (isset($_REQUEST["company_id"]) && $_REQUEST["company_id"] != "" && $_REQUEST["company_id"] != 'undefined') {
	$ctable_where .= " type_of_company='" . (int) $_REQUEST["company_id"] . "' AND ";
}
if (isset($_REQUEST["visit_type"]) && $_REQUEST["visit_type"] != "" && $_REQUEST["visit_type"] != 'undefined') {
	$ctable_where .= " visit_type='" . (int) $_REQUEST["visit_type"] . "' AND ";
}

$sales_exec_filter = "";
if (isset($_REQUEST['sales_executive'])) {
	$sales_exec_filter = armor_visit_excel_ids($_REQUEST['sales_executive']);
}
if ($sales_exec_filter != "") {
	$ctable_where .= " user_id IN (" . $sales_exec_filter . ") AND ";
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != 'undefined') {
	$todate = armor_visit_excel_parse_date($_REQUEST['todate']);
	if ($todate != "") {
		$ctable_where .= " DATE(created_date) >= '" . $todate . "' AND ";
	}
}
if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != 'undefined') {
	$fromdate = armor_visit_excel_parse_date($_REQUEST['fromdate']);
	if ($fromdate != "") {
		$ctable_where .= " DATE(created_date) <= '" . $fromdate . "' AND ";
	}
}

$customer_filter = "";
if (isset($_REQUEST['customer_id'])) {
	$customer_filter = armor_visit_excel_ids($_REQUEST['customer_id']);
}
if ($customer_filter != "") {
	$ctable_where .= " customer_id IN (" . $customer_filter . ") AND ";
}

if (isset($_REQUEST['class_id']) && $_REQUEST['class_id'] != "" && $_REQUEST['class_id'] != 'undefined') {
	$exe_id = array();
	$get_exe_id = $db->rp_getData("executive_map_area", "executive_id", "class_id='" . (int) $_REQUEST['class_id'] . "' AND isDelete=0");
	if ($get_exe_id) {
		while ($get_exe_id_d = mysqli_fetch_assoc($get_exe_id)) {
			$exe_id[] = (int) $get_exe_id_d['executive_id'];
		}
	}
	$ctable_where .= !empty($exe_id) ? (" customer_id IN (" . implode(",", $exe_id) . ") AND ") : " customer_id IN (0) AND ";
}

if (isset($_REQUEST['area']) && $_REQUEST['area'] != "" && $_REQUEST['area'] != 'undefined') {
	$exe_idS = array();
	$get_exe_area = $db->rp_getData("executive_map_area", "executive_id", "area_id='" . (int) $_REQUEST['area'] . "' AND isDelete=0", "", 0);
	if ($get_exe_area) {
		while ($get_exe_area_d = mysqli_fetch_assoc($get_exe_area)) {
			$exe_idS[] = (int) $get_exe_area_d['executive_id'];
		}
	}
	$ctable_where .= !empty($exe_idS) ? (" customer_id IN (" . implode(",", $exe_idS) . ") AND ") : " customer_id IN (0) AND ";
}

$date_filter_query = "";
if (isset($_REQUEST['df1']) && $_REQUEST['df1'] != "") {
	$date_filter_query = urldecode($_REQUEST['df1']);
} else if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
	$date_filter_query = urldecode($_REQUEST['df']);
}
if ($date_filter_query != "") {
	$date_filter_query_ex = explode(" to ", $date_filter_query);
	$date_from = isset($date_filter_query_ex[0]) ? armor_visit_excel_parse_date($date_filter_query_ex[0]) : '';
	$date_to = isset($date_filter_query_ex[1]) ? armor_visit_excel_parse_date($date_filter_query_ex[1]) : $date_from;
	if ($date_from != "" && $date_to != "") {
		$ctable_where .= " ( DATE(created_date)>='" . $date_from . "' AND DATE(created_date)<='" . $date_to . "' ) AND ";
	}
}

if (isset($_REQUEST['visit_month']) && $_REQUEST['visit_month'] != "" && $_REQUEST['visit_month'] != 'undefined' && $_REQUEST['visit_month'] != 'null') {
	$ctable_where .= " MONTH(created_date) = '" . (int) $_REQUEST['visit_month'] . "' AND ";
}
if (isset($_REQUEST['visit_year']) && $_REQUEST['visit_year'] != "" && $_REQUEST['visit_year'] != 'undefined' && $_REQUEST['visit_year'] != 'null') {
	$ctable_where .= " YEAR(created_date) = '" . (int) $_REQUEST['visit_year'] . "' AND ";
}

$ctable_where .= " isDelete=0";

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	if (isset($rights['personal_flag']) && (int) $rights['personal_flag'] === 1) {
		$ctable_where .= " AND user_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
	} else if (isset($rights['chain_vise_flag']) && (int) $rights['chain_vise_flag'] === 1) {
		$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
		$WhereCondition = "";
		$get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
		if ($get_sales_type == "sales_manager") {
			$key = "sm_id";
			$WhereCondition .= ' ' . $key . '=' . $check_id;
		} else if ($get_sales_type == "area_sales_manager") {
			$key = "asm_id";
			$WhereCondition .= ' ' . $key . '=' . $check_id;
		} else if ($get_sales_type == "sales_officer") {
			$key = "so_id";
			$WhereCondition .= ' ' . $key . '=' . $check_id;
		} else if ($get_sales_type == "sales_executive") {
			$key = "se_id";
			$WhereCondition .= ' ' . $key . '=' . $check_id;
		} else {
			$WhereCondition .= ' type = "service_engineer"';
		}
		$data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);
		$SALEID1 = array();
		if ($data) {
			while ($data_d = mysqli_fetch_assoc($data)) {
				$SALEID1[] = $data_d['id'];
			}
		}
		if (!empty($SALEID1)) {
			$SALEID1 = implode(",", $SALEID1);
			$ctable_where .= " AND user_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
		} else {
			$ctable_where .= " AND user_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
		}
	}
}

$ctable1_r = $db->rp_getData("visit", "*", $ctable_where, "id DESC", 0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();
// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);
// Initialise the Excel row number 
$rowCount = 1;

//start of printing column names as names of MySQL fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
$column6  = 'G';
$column7  = 'H';
$column8  = 'I';
$column9  = 'J';
$column10 = 'K';
$column11  = 'L';
$column12  = 'M';
$column13  = 'N';
$column14  = 'O';
$column15  = 'P';
$column16  = 'Q';
$column17  = 'R';
$column18  = 'S';
$column19  = 'T';
$column20  = 'U';
$column21  = 'V';
$column22  = 'W';
$column23  = 'X';
$column24  = 'Y';
$column25  = 'Z';
$column26  = 'AA';
$column27  = 'AB';


$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Id");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Sales Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Company");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Company Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Client Code");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Customer Mobile No");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Customer Email");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "Customer GST");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "Customer Turn Over");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount, "Customer Turn Over Year");
$objPHPExcel->getActiveSheet()->setCellValue($column11 . $rowCount, "Date and Time");
$objPHPExcel->getActiveSheet()->setCellValue($column12 . $rowCount, "Visit Purpose");
$objPHPExcel->getActiveSheet()->setCellValue($column13 . $rowCount, "Customer Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column14 . $rowCount, "Customer Person Mobile No.");
$objPHPExcel->getActiveSheet()->setCellValue($column15 . $rowCount, "Customer Person Email ID");
$objPHPExcel->getActiveSheet()->setCellValue($column16 . $rowCount, "Customer Person Designation");
$objPHPExcel->getActiveSheet()->setCellValue($column17 . $rowCount, "Visit Start Address");
$objPHPExcel->getActiveSheet()->setCellValue($column18 . $rowCount, "Visit Stop Address");
$objPHPExcel->getActiveSheet()->setCellValue($column19 . $rowCount, "Customer Address");
$objPHPExcel->getActiveSheet()->setCellValue($column20 . $rowCount, "Visit Start Remark");
$objPHPExcel->getActiveSheet()->setCellValue($column21 . $rowCount, "Visit Start Time");
$objPHPExcel->getActiveSheet()->setCellValue($column22 . $rowCount, "Visit Stop Remark");
$objPHPExcel->getActiveSheet()->setCellValue($column23 . $rowCount, "Visit Stop Time");
$objPHPExcel->getActiveSheet()->setCellValue($column24 . $rowCount, "Purchasing From");
$objPHPExcel->getActiveSheet()->setCellValue($column25 . $rowCount, "Total Time");
$objPHPExcel->getActiveSheet()->setCellValue($column26 . $rowCount, "Visit Type");
$objPHPExcel->getActiveSheet()->setCellValue($column27 . $rowCount, "Visit Stop Flag");

//end of adding column names  

$rowCount = 2;
$count = 0;
if ($ctable1_r) {
while ($row = mysqli_fetch_array($ctable1_r)) {
  $elapsed = "";
  if (!empty($row['stop_date_time']) && !empty($row['start_date_time'])
    && $row['stop_date_time'] != "0000-00-00 00:00:00"
    && $row['start_date_time'] != "0000-00-00 00:00:00") {
    try {
      $datetime1 = new DateTime($row['stop_date_time']);
      $datetime2 = new DateTime($row['start_date_time']);
      $interval = $datetime1->diff($datetime2);
      $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
    } catch (Exception $e) {
      $elapsed = "";
    }
  }

  $customer_email = "";
  $customer_turn_over = "";
  $customer_turn_year = "";
  $customer_gst = "";
  $customer_address = "";
  $company_name = "";
  $customer_flag = "";
  $cname = "";
  $client_code = "";
  $mobile_no1 = "";

  if ($row['visit_stop_flag'] == 4) {
  } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
  } else {
    $customer_detail_get = $db->rp_getData("executive", "gst,email,turnover,turnover_year,address,company_name,customer_flag,cname,client_code,mobile_no1", "isDelete=0 AND id = '" . $row['customer_id'] . "' ");
    if ($customer_detail_get) {
      $customer_detail_get_d = mysqli_fetch_assoc($customer_detail_get);
      $customer_email = $customer_detail_get_d['email'];
      $customer_turn_over = $customer_detail_get_d['turnover'];
      $customer_turn_year = $customer_detail_get_d['turnover_year'];
      $customer_gst = $customer_detail_get_d['gst'];
      $customer_address = $customer_detail_get_d['address'];
      $company_name = $customer_detail_get_d['company_name'];
      $customer_flag = $customer_detail_get_d['customer_flag'];
      $cname = $customer_detail_get_d['cname'];
      $client_code = $customer_detail_get_d['client_code'];
      $mobile_no1 = $customer_detail_get_d['mobile_no1'];
    }
  }

  $count++;
  $column = 'A';
  for ($j = 0; $j < 28; $j++) {
    $value = "";
    if ($j == 0) {
      $value = $count;
    } else if ($j == 1) {
      $value = $db->rp_getValue("sales_executive", "name", "id='" . $row['user_id'] . "'");
    } else if ($j == 2) {
      $value = $db->rp_getValue("company_master", "name", "id='" . $row['type_of_company'] . "'", 0);
    } else if ($j == 3) {
      if ($row['visit_stop_flag'] == 4) {
        $value = $row['firm_name'];
      } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
        $value = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $row['inquiry_id'] . "'", 0);
      } else {
        $customer_flag_text = "";
        if ($customer_flag == 1) {
          $customer_flag_text = " - P";
        } else if ($customer_flag == 0) {
          $customer_flag_text = " - C";
        }
        $value = $company_name . $customer_flag_text;
      }
    } else if ($j == 4) {
      if ($row['visit_stop_flag'] == 4) {
        $value = $row['contact_person'] ?:  '-';
      } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
        $value = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $row['inquiry_id'] . "'", 0);
      } else {
        $value = $cname;
      }
    } else if ($j == 5) {
      if ($row['visit_stop_flag'] == 4) {
      } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
      } else {
        $value =  $client_code;
      }
    } else if ($j == 6) {
      if ($row['visit_stop_flag'] == 4) {
        $value =  $row['contact_number'];
      } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
        $value =  $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $row['inquiry_id'] . "'");
      } else {
        $value =  $mobile_no1;
      }
    } else if ($j == 7) {
      $value = $customer_email;
    } else if ($j == 8) {
      $value = $customer_gst;
    } else if ($j == 9) {
      $value = $customer_turn_over;
    } else if ($j == 10) {
      $value = $customer_turn_year;
    } else if ($j == 11) {
      $value = date("d-m-Y H:i:s", strtotime($row['created_date']));
    } else if ($j == 12) {
      $value = $db->rp_getValue("purpose_master", "name", "isDelete=0 AND id=" . $row['purpose_id'], 0);
    } else if ($j == 13) {
      $value = $row['name'];
    } else if ($j == 14) {
      $value = $row['mobile_no'];
    } else if ($j == 15) {
      $value = $row['email_id'];
    } else if ($j == 16) {
      $value = $db->rp_getValue("visit_designation", "name", "isDelete=0 AND id = '" . $row['designation'] . "' ");
    } else if ($j == 17) {
      $value = $row['app_address'];
    } else if ($j == 18) {
      $value = $row['stop_app_address'];
    } else if ($j == 19) {
      if ($row['visit_stop_flag'] == 4) {
      } else if ($row['customer_id'] == 0 && $row['inquiry_id'] != "0") {
      } else {
        $value = $customer_address;
      }
    } else if ($j == 20) {
      $value = stripslashes($row['remark']);
    } else if ($j == 21) {
      if ($row['start_date_time'] != "0000-00-00 00:00:00") {
        $value =  date('d-m-Y h:i A', strtotime($row['start_date_time']));
      } else {
        $value =  "";
      }
    } else if ($j == 22) {
      $stopRemarkVal = stripslashes($row['stop_remark']);
      $isShortNote = (!empty($row['remark_code']) && $row['remark_code'] == 'F') || (stripos($stopRemarkVal, 'SHORT NOTE') !== false);
      if ($isShortNote) {
        $shortNoteVal = !empty($row['note']) ? trim($row['note']) : (!empty($row['remark']) ? trim($row['remark']) : "");
        if ($shortNoteVal != "") {
          $stopRemarkVal .= " | Remark / Note: " . $shortNoteVal;
        }
      }
      if (!empty($row['consultant_form_id']) || (!empty($row['remark_code']) && $row['remark_code'] == 'C')) {
        $vcf = $db->rp_getData("visit_consultant_form", "*", "visit_id='" . $row['id'] . "' AND isDelete=0", "id DESC", 0);
        if ($vcf) {
          $vf = mysqli_fetch_assoc($vcf);
          $typeLabel = (isset($vf['consultant_type']) && $vf['consultant_type'] == "government") ? "Government Consultant" : "Private Consultant";
          $stopRemarkVal .= " | Consultant Detail (" . $typeLabel . ") - Firm: " . $vf['firm_name'] . ", Contact: " . $vf['contact_person'] . ", Mo: " . $vf['mobile'] . ", " . $vf['city'] . ", " . $vf['state'];
        }
      }
      if (!empty($row['high_rate_form_id']) || (!empty($row['remark_code']) && $row['remark_code'] == 'E')) {
        $hrf = $db->rp_getData("visit_high_rate_form", "*", "visit_id='" . $row['id'] . "' AND isDelete=0", "id DESC", 0);
        if ($hrf) {
          $hf = mysqli_fetch_assoc($hrf);
          $payLabel = !empty($hf['payment_option']) ? ($hf['payment_option'] === '0' ? 'Advance' : ($hf['payment_option'] === '1' ? '30 Days' : $hf['payment_option'])) : '';
          $stopRemarkVal .= " | High Rate Analysis - Customer: " . $hf['customer_name'] . ($payLabel != '' ? ", Payment: " . $payLabel : '');
        }
      }
      $value = $stopRemarkVal;
    } else if ($j == 23) {
      if ($row['stop_date_time'] != "0000-00-00 00:00:00") {
        $value = date('d-m-Y h:i A', strtotime($row['stop_date_time']));
      } else {
        $value = "";
      }
    } else if ($j == 24) {
      $value = $row['product_name'];
    } else if ($j == 25) {
      if ($row['stop_date_time'] != "0000-00-00 00:00:00") {
        $value = $elapsed;
      }
    } else if ($j == 26) {
      if ($row['visit_type'] == "1") {
        $value =  "Existing Customer";
      } else if ($row['visit_type'] == "3") {
        $value =  "Inquiry";
      } else if ($row['visit_type'] == "4") {
        $value =  "New Customer";
      } else {
        $value =  " ";
      }
    } else if ($j == 27) {
      $order_no = "";
      $followp = "";
      if ($row['visit_stop_flag'] == "1") {
        $order_no = $db->rp_getValue("orders", "order_no", "customer_id='" . $row['customer_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($row['stop_date_time'])) . "' AND sales_id=" . $row['user_id']);
      }

      if ($row['visit_stop_flag'] == "3") {
        if (isset($row['visit_followup_id']) && $row['visit_followup_id'] != "" && $row['visit_followup_id'] != "0") {
          $followp = $row['visit_followup_id'];
        } else {
          $followp = $db->rp_getValue("followup", "id", "visitor_id='" . $row['customer_id'] . "' AND reference_id='" . $row['inquiry_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($row['stop_date_time'])) . "' AND user_id=" . $row['user_id'], 0);
          if ($followp == "" && $row['customer_id'] != "" && $row['customer_id'] != "0") {
            $followp = $db->rp_getValue("followup", "id", "(visitor_id='" . $row['customer_id'] . "' OR reference_id='" . $row['customer_id'] . "') AND DATE(created_date)='" . date('Y-m-d', strtotime($row['stop_date_time'])) . "' AND user_id='" . $row['user_id'] . "' AND isDelete=0", 0);
          }
        }
      }

      if ($row['visit_stop_flag'] == "1") {
        $value =  "Create Order " . $order_no;
      } else if ($row['visit_stop_flag'] == "2") {
        $value =  "Stop Visit With Edit Inquiry";
      } else if ($row['visit_stop_flag'] == "3") {
        $value =  "Create Followup";
      } else if ($row['visit_stop_flag'] == "4") {
        $value =  "Consultant Visit";
      } else {
        $value =  "";
      }
    } else {
      $value = isset($row[$j]) ? $row[$j] : "";
    }
    $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $value);
    $column++;
  }
  $rowCount++;
}
}
// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=" . $file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES . $file_name);
$file_path1 = trim(ADMINFOLDER . "/inquiry_documents/" . $file_name);
$arr = array("file_path" => $file_path1);
echo json_encode($arr);
