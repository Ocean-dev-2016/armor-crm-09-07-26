<?php
include("connect.php");
include('PHPExcel/IOFactory.php');
$file_name  = "Customer_Performance_Report" . "_" . date("d-m-Y") . ".xlsx";

$ctable   = "orders";
$ctable1  = "Orders";

// $Where = "";


$ctable_where = "";
$area         = $_REQUEST['area'];

$Where = '';
$Where1 = '';
$Where2 = '';
$Where3 = '';
$Where4 = '';
$Where5 = '';
$Where6 = '';
$Where7 = '';
$Where8 = '';

// $Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE %'".$_REQUEST['searchName']."'%":"";
// $Where1 = (isset($_REQUEST['type']) && $_REQUEST['type'] != "")?" AND type_of_executive ='".$_REQUEST['type']."'":"";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);

$Where = "";

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $Where .= " AND (cname LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code LIKE '%" . $db->clean($_REQUEST['searchName']) . "%') ";
}

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != 'null') {
  // $Where1 = (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL)?" AND type ='".$_REQUEST['type']."'":"";

  // $Where1 .= " AND type_of_executive = '".$_REQUEST['type']."' ";
  $Where1 .= "  AND type_of_executive IN (" . $_REQUEST['type'] . ") ";
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
  $Where9 .= " AND state = '" . $_REQUEST['state'] . "' ";
}

if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
  $Where9 .= " AND main_city = '" . $_REQUEST['city'] . "' ";
}
if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL) {
  $Where9 .= " AND city = '" . $_REQUEST['route'] . "' ";
}
if (isset($_REQUEST['zone']) && $_REQUEST['zone'] != "" && $_REQUEST['zone'] != NULL) {
  $Where9 .= " AND zone = '" . $_REQUEST['zone'] . "' ";
}
if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
  $Where2 = " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
  $Where3 = " AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
  $Where4 = " AND complain_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
  $Where5 = " AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
  $Where6 = " AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
  $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
  $Where2 .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
  $Where3 .= " AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
  $Where4 .= " AND complain_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
  $Where5 .= " AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
  $Where6 .= " AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
  $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}
$date = ($_REQUEST['FromDate'] != "") ? " - " . $_REQUEST['FromDate'] . " TO " : "";
$date .= ($_REQUEST['ToDate'] != "") ? $_REQUEST['ToDate'] : "";
$Query = "SELECT `executive`.`id`, `executive`.`company_name` AS name,`executive`.`cname` as person_name,`executive`.`client_code` as client_code,`executive`.`customer_flag` as customer_flag, ( SELECT name FROM `customer_type` WHERE id = `executive`.`type_of_executive` ) AS type, `executive`.`state` AS state, `executive`.`main_city` AS main_city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where2 . " ) AS total_order,  ( SELECT COUNT(*) FROM `quotation_detail` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where7 . " ) AS total_quotation,( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where2 . " ) AS total_order_value, ( SELECT SUM(grand_total) FROM `quotation_detail` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where7 . " ) AS total_quotation_value,( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where3 . " ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where4 . " ) AS total_complain, ( SELECT COUNT(*) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where6 . " ) AS total_invoice, ( SELECT SUM(grand_total) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where6 . " ) AS total_invoice_value , ( SELECT SUM(subtotal) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where6 . " ) AS subtotal_invoice_value , ( SELECT SUM(igst_amount) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` " . $Where6 . " ) AS total_igst_invoice_value, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=-1 AND  isDelete = 0 AND dealer_id=`executive`.`id` " . $Where5 . " ) AS total_prospect ,( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=0 AND  isDelete = 0 AND dealer_id=`executive`.`id` " . $Where5 . " ) AS total_inquiry, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=1 AND  isDelete = 0 AND dealer_id=`executive`.`id` " . $Where5 . " ) AS total_lead, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE status=1 AND  isDelete = 0 AND dealer_id=`executive`.`id` " . $Where5 . " ) AS total_followups, ( SELECT SUM(credit + debit) FROM `account_transaction` WHERE isDelete = 0 AND cid=`executive`.`id` " . $Where5 . " ) AS outstanding_amount FROM `executive` WHERE isDelete = 0 " . $Where . " " . $Where1 . " " . $Where9;

// echo $Query;exit;

$ctable_r = $db->rp_getQuery($Query);

// $ctable1_r = $db->rp_getData("executive","id,type_of_executive,price_list_id,company_name,cname,phone,mobile_no1,whatsapp_no,state,city,id",$ctable_where,"id DESC",0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);
// Initialise the Excel row number 
$rowCount = 1;

//start of printing column names as names of mysqli fields  
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
$column10  = 'K';
$column11  = 'L';
$column12  = 'M';
$column13  = 'N';
$column14  = 'O';
$column15  = 'P';

$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Customer Type");
// $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Price List");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Customer Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Client Code");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Customer Name");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Customer State");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, " CustomerCity");
// $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Total Prospect");
// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Total Inquiry");
// $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Total Lead");
// $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Total Followups");

$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount,  "Total Quotation");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount,  "Total Quotation Value");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount,  "Total Order");
$objPHPExcel->getActiveSheet()->setCellValue($column11 . $rowCount,  "Total Order Value");

// $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount,  "Total Visit");
// $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount,  "Total Complain");

$objPHPExcel->getActiveSheet()->setCellValue($column12 . $rowCount,  "Total Invoice");
$objPHPExcel->getActiveSheet()->setCellValue($column13 . $rowCount,  "Total Invoice Value Without GST");
$objPHPExcel->getActiveSheet()->setCellValue($column14 . $rowCount,  "Total Invoice Value With GST");
$objPHPExcel->getActiveSheet()->setCellValue($column15 . $rowCount,  "Outstanding Amount");
// $objPHPExcel->getActiveSheet()->setCellValue($column16.$rowCount,  "Total Pending Order Value");

// $objPHPExcel->getActiveSheet()->setCellValue($column19.$rowCount,  "New-Customer-Onbord");
//end of adding column names  

$rowCount = 2;
$count = 0;

while ($row = mysqli_fetch_assoc($ctable_r)) {

  $customer_flag_text = "";
  if ($row['customer_flag'] == 1) {
    $customer_flag_text = " - P";
  } else if ($row['customer_flag'] == 0) {
    $customer_flag_text = " - C";
  }
  if ($row['type_of_executive'] == "1") {
    $type = "Super Stockist";
  } else if ($row['type_of_executive'] == "2") {
    $type = "Distributor";
  } else if ($row['type_of_executive'] == "3") {
    $type = "Dealer";
  } else if ($row['type_of_executive'] == "4") {
    $type = "B2B Customer";
  } else {
    $type = "Dealer";
  }

  $status_array = array(1 => "Super Stockist", 2 => "Distributor", 3 => "Dealer", 4 => "B2B Customer");
  // print_r($row);
  $count++;
  $column = 'A';
  for ($j = 0; $j < 16; $j++) {
    if ($j == 0) {
      $value = $count;
    } else if ($j == 1) {
      $value = $row['type'];
    } else if ($j == 2) {
      $value = stripslashes($row['name'] . $customer_flag_text);
    } else if ($j == 3) {
      $value = stripslashes($row['person_name']);
    } else if ($j == 4) {
      $value = stripslashes($row['client_code']);
    } else if ($j == 5) {
      $value = stripslashes($row['state']);
    } else if ($j == 6) {
      $value = stripslashes($row['main_city']);
    }
    // else if($j==5)
    // {
    //   $value=$row['total_prospect'];
    // }
    // else if($j==6)
    // {
    //   $value=$row['total_inquiry'];
    // }
    // else if($j==7)
    // {
    //   $value=$row['total_lead'];
    // }
    // else if($j==8)
    // {
    //   $value=$row['total_followups'];
    // }
    else if ($j == 7) {
      $value = $row['total_quotation'];
    } else if ($j == 8) {
      $value = $row['total_quotation_value'];
    } else if ($j == 9) {
      $value = $row['total_order'];
    } else if ($j == 10) {
      $value = $row['total_order_value'];
    }

    /*else if($j==13)
    {
      $value=$row['total_visit'];
    }
    else if($j==14)
    {
      $value=$row['total_complain'];
    }*/ else if ($j == 11) {
      $value = $row['total_invoice'];
    } else if ($j == 12) {
      $value = $row['subtotal_invoice_value'] - $row['total_igst_invoice_value'];
    } else if ($j == 13) {
      $value = $row['total_invoice_value'];
    } else if ($j == 14) {
      $value = $row['outstanding_amount'];
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
require_once 'disconnect.php';
echo json_encode($arr);
