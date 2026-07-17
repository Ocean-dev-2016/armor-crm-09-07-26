<?php
include("connect.php");
include('PHPExcel/IOFactory.php');

// $file_name  = "Visit_Report"."_".date("d-m-Y").".xlsx";
$file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {

  $sales_id = $db->rp_getData("sales_executive", "*", "name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
  if ($sales_id) {
    while ($K = mysqli_fetch_assoc($sales_id)) {
      $USER_IDS[] = $K['id'];
    }
    $USER_IDS = implode(",", $USER_IDS);
    $ctable_where .= "user_id IN (" . $USER_IDS . ") ";
  } else {
    $ctable_where .= "user_id IN (0) ";
  }

  $customer_id = $db->rp_getData("executive", "*", "cname LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
  if ($customer_id) {
    while ($K1 = mysqli_fetch_assoc($customer_id)) {
      $CUSTOMER_IDS[] = $K1['id'];
    }
    $CUSTOMER_IDS = implode(",", $CUSTOMER_IDS);
    $ctable_where .= " AND  customer_id IN (" . $CUSTOMER_IDS . ") ";
  } else {
    $ctable_where .= " AND customer_id IN (0)  ";
  }
  $inquiry_id = $db->rp_getData("no_order_inquiry", "*", "person_name LIKE '%" . $_REQUEST['searchName'] . "%' OR mobile_number LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
  if ($inquiry_id) {
    while ($D1 = mysqli_fetch_assoc($inquiry_id)) {
      $INQID[] = $D1['id'];
    }
    $INQID = implode(",", $INQID);
    $ctable_where .= " AND  inquiry_id IN (" . $INQID . ") ";
  } else {
    $ctable_where .= "  AND  inquiry_id IN (0)  ";
  }
  $ctable_where .= " AND ";


  /*$ctable_where .= " (
    name like '%".$_REQUEST['searchName']."%' OR
    email like '%".$_REQUEST['searchName']."%' OR
    contact_number like '%".$_REQUEST['searchName']."%' 
  ) AND ";*/
}

if (isset($_REQUEST["company_id"]) && $_REQUEST["company_id"] != "" && $_REQUEST["company_id"] != 'undefined') {
  $ctable_where .= " type_of_company='" . $_REQUEST["company_id"] . "' AND ";
  $company_ids = $_REQUEST["company_id"];
}
if (isset($_REQUEST["visit_type"]) && $_REQUEST["visit_type"] != "" && $_REQUEST["visit_type"] != 'undefined') {
  $ctable_where .= " visit_type='" . $_REQUEST["visit_type"] . "' AND ";
}
// echo sizeof($_REQUEST['sales_executive']);exit;
if (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != NULL && $_REQUEST['sales_executive'] != null && $_REQUEST['sales_executive'] != "NULL" && $_REQUEST['sales_executive'] != "null" && $_REQUEST['sales_executive'] != 'UNDEFINED' && $_REQUEST['sales_executive'] != 'undefined' && $_REQUEST['sales_executive'] != "UNDEFINED" && $_REQUEST['sales_executive'] != "undefined" && sizeof($_REQUEST['sales_executive']) > 0) {
  // $ctable_where .= " user_id='".implode(" ", $_REQUEST["sales_executive"])."' AND ";

  $ctable_where .= " user_id='" . $_REQUEST["sales_executive"] . "' AND ";
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != 'undefined') {
  $ctable_where .= "  DATE(created_date) >= '" . $_REQUEST['todate'] . "' AND ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != 'undefined') {
  $ctable_where .= "  DATE(created_date) <= '" . $_REQUEST['fromdate'] . "' AND ";
}


if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id'] != null && $_REQUEST['customer_id'] != "NULL" && $_REQUEST['customer_id'] != "null" && $_REQUEST['customer_id'] != 'UNDEFINED' && $_REQUEST['customer_id'] != 'undefined' && $_REQUEST['customer_id'] != "UNDEFINED" && $_REQUEST['customer_id'] != "undefined" && sizeof($_REQUEST['customer_id']) > 0) {
  // $ctable_where .= " customer_id='".implode(" ", $_REQUEST["customer_id"])."' AND ";

  $ctable_where .= " customer_id='" . $_REQUEST["customer_id"] . "' AND ";
}

if (isset($_REQUEST['df1']) && $_REQUEST['df1'] != "") {
  // echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode($_REQUEST['df1']);

  $date_filter_query_ex = explode(" to ", $date_filter_query);

  $ctable_where .= " ( DATE(created_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(created_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  )  AND ";
}
//service_executive user not show condition start --//
// $SEID=array();
// $sales_type_r=$db->rp_getData("sales_executive","id,username","type='service_executive'","",0);
// while($sales_type_d = mysqli_fetch_array($sales_type_r))
// {
//   $SEID[] = $sales_type_d['id'];
// }
// $SEID=implode(",",$SEID);
// $ctable_where .="   user_id NOT IN ('".$SEID."') AND ";
//service_executive user not show condition end --//

$ctable_where .= " isDelete=0";
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
while ($row = mysqli_fetch_array($ctable1_r)) {
  $datetime1 = new DateTime($row['stop_date_time']);
  $datetime2 = new DateTime($row['start_date_time']);
  $interval = $datetime1->diff($datetime2);
  $elapsed = $interval->format('%a days %h hours %i minutes %s seconds');

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

  $count++;
  $column = 'A';
  for ($j = 0; $j < 28; $j++) {
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
      $value = stripslashes($row['stop_remark']);
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
      if ($row['visit_stop_flag'] == "1") {
        $order_no = $db->rp_getValue("orders", "order_no", "customer_id='" . $row['customer_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($row['stop_date_time'])) . "' AND sales_id=" . $row['user_id']);
      }
      if ($order_no == "" && $row['visit_stop_flag'] == "1") {
        $style = "style='background-color: #f1acac;'";
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
      if ($followp == "" && $row['visit_stop_flag'] == "3") {
        $style = "style='background-color: #f1acac;'";
      }

      if ($row['visit_stop_flag'] == "1") {
        $value =  "Create Order<br/>" . $order_no;
      } else if ($row['visit_stop_flag'] == "2") {
        $value =  "Stop Visit With Edit Inquiry";
      } else if ($row['visit_stop_flag'] == "3") {
        $value =  "Create Followup";
      }
    } else {
      $value = $row[$j];
    }
    $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $value);
    $column++;
  }
  $rowCount++;
}
// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=" . $file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES . $file_name);
$file_path1 = trim(ADMINFOLDER . "/inquiry_documents/" . $file_name);
$arr = array("file_path" => $file_path1);
echo json_encode($arr);
