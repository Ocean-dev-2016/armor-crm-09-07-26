<?php
include("connect.php");
include('PHPExcel/IOFactory.php');
$file_name  = "Active_Deactive_Customer_Report" . "_" . date("d-m-Y") . ".xlsx";
$Where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);

// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $ctable_where .= " (
                            cname like '%" . $db->clean($_REQUEST['searchName']) . "%'
                            OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
                            OR phone  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'
                        ) AND ";
}

$ctable_where .= " isDelete=0 ";


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if (!is_numeric($page_number)) {
    die('Invalid page number!');
  } //incase of invalid page number
} else {
  $page_number = 1; //if there's no page number, set it to 1
}

$date_select = "";

if (isset($_REQUEST['customer_status']) && $_REQUEST['customer_status'] != "" && $_REQUEST['customer_status'] != NULL && $_REQUEST['customer_status'] != null && $_REQUEST['customer_status'] != "NULL" && $_REQUEST['customer_status'] != "null" && $_REQUEST['customer_status'] != UNDEFINED && $_REQUEST['customer_status'] != undefined && $_REQUEST['customer_status'] != "UNDEFINED" && $_REQUEST['customer_status'] != "undefined") {
  // $_REQUEST['customer_status'] = implode(", ", $_REQUEST['customer_status']);
  $ctable_where .= " AND isActive IN (" . $_REQUEST['customer_status'] . ")";
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id'] != null && $_REQUEST['customer_id'] != "NULL" && $_REQUEST['customer_id'] != "null" && $_REQUEST['customer_id'] != UNDEFINED && $_REQUEST['customer_id'] != undefined && $_REQUEST['customer_id'] != "UNDEFINED" && $_REQUEST['customer_id'] != "undefined") {
  // $_REQUEST['customer_id'] = implode(", ", $_REQUEST['customer_id']);
  $ctable_where .= " AND id IN (" . $_REQUEST['customer_id'] . ")";
}
if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL && $_REQUEST['state'] != null && $_REQUEST['state'] != "NULL" && $_REQUEST['state'] != "null" && $_REQUEST['state'] != UNDEFINED && $_REQUEST['state'] != undefined && $_REQUEST['state'] != "UNDEFINED" && $_REQUEST['state'] != "undefined") {


  $stringA = $_REQUEST['state'];
  $stringAArray = explode(",", $stringA);
  $stringiO = "(";
  foreach ($stringAArray as $key => $value) {
    $stringiO .= "'" . $value . "',";
  }
  $stringiO = rtrim($stringiO, ",");
  $stringiO .= ")";
  $ctable_where .= " AND state IN " . $stringiO . " ";
  $state = $_REQUEST['state'];
}


if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL && $_REQUEST['city'] != null && $_REQUEST['city'] != "NULL" && $_REQUEST['city'] != "null" && $_REQUEST['city'] != UNDEFINED && $_REQUEST['city'] != undefined && $_REQUEST['city'] != "UNDEFINED" && $_REQUEST['city'] != "undefined") {
  $stringD = $_REQUEST['city'];
  $stringDArray = explode(",", $stringD);
  $stringio = "(";
  foreach ($stringDArray as $key => $value) {
    $stringio .= "'" . $value . "',";
  }
  $stringio = rtrim($stringio, ",");
  $stringio .= ")";
  $ctable_where .= " AND main_city IN " . $stringio . " ";
  $city = $_REQUEST['city'];
}
if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL && $_REQUEST['route'] != null && $_REQUEST['route'] != "NULL" && $_REQUEST['route'] != "null" && $_REQUEST['route'] != UNDEFINED && $_REQUEST['route'] != undefined && $_REQUEST['route'] != "UNDEFINED" && $_REQUEST['route'] != "undefined") {
  $stringD = $_REQUEST['route'];
  $stringDArray = explode(",", $stringD);
  $stringio = "(";
  foreach ($stringDArray as $key => $value) {
    $stringio .= "'" . $value . "',";
  }
  $stringio = rtrim($stringio, ",");
  $stringio .= ")";
  $ctable_where .= " AND city IN " . $stringio . " ";
  $route = $_REQUEST['route'];
}
// $get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); 
//hold total records in variable

/*if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND type_of_executive = '".$_REQUEST['status']."' ";
}*/

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL && $_REQUEST['customer_type'] != null && $_REQUEST['customer_type'] != "NULL" && $_REQUEST['customer_type'] != "null" && $_REQUEST['customer_type'] != UNDEFINED && $_REQUEST['customer_type'] != undefined && $_REQUEST['customer_type'] != "UNDEFINED" && $_REQUEST['customer_type'] != "undefined") {
  // $Where .= " status='".implode(" , ", $_REQUEST['status_id'])."' AND ";
  // $_REQUEST['customer_type'] = implode(", ", $_REQUEST['customer_type']);
  $ctable_where .= "  AND type_of_executive IN ( " . $_REQUEST['customer_type'] . ") ";
  $customer_type = $_REQUEST['customer_type'];
}

if (isset($_REQUEST['days']) && $_REQUEST['days'] != "" && $_REQUEST['days'] != NULL) {
  $date1 = date('Y-m-d');
  $prev_days_date = date('Y-m-d', strtotime($date1 . ' - ' . $_REQUEST['days'] . ' days'));
  $order_Where = " AND order_date>='" . $prev_days_date . "'";
}

$order = $db->rp_getData("orders", "customer_id", "isDelete=0" . $order_Where . " GROUP BY customer_id", "", 0);
$customer_ids = array();
while ($order_d = mysqli_fetch_array($order)) {
  $customer_ids[] = $order_d['customer_id'];
}
$custome_id = implode(",", $customer_ids);
// echo ($custome_id);exit();

if (isset($_REQUEST['customer_status']) && $_REQUEST['customer_status'] != "" && $_REQUEST['customer_status'] != NULL && $_REQUEST['customer_status'] != null && $_REQUEST['customer_status'] != "NULL" && $_REQUEST['customer_status'] != "null" && $_REQUEST['customer_status'] != UNDEFINED && $_REQUEST['customer_status'] != undefined && $_REQUEST['customer_status'] != "UNDEFINED" && $_REQUEST['customer_status'] != "undefined") {
  if ($_REQUEST['customer_status'] == 0) {

    $ctable_where .= " AND id NOT IN (" . $custome_id . ")";
  } else {
    $ctable_where .= " AND id  IN (" . $custome_id . ")";
  }
}

$Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("executive", "id,customer_flag,type_of_executive,price_list_id,company_name,cname,phone,mobile_no1,whatsapp_no,state,main_city,city,id,client_code", $ctable_where, "id DESC", 0);

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



$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Customer Type");
// $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Price List");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Company Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Client Code");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Phone");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Mobile");
// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "WhatsApp");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "City");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "Route");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount,  "Last Order Detail");
//end of adding column names  

$rowCount = 2;
$count = 0;

while ($row = mysqli_fetch_assoc($ctable1_r)) {
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
  for ($j = 0; $j < 11; $j++) {
    if ($j == 0) {
      $value = $count;
    } else if ($j == 1) {
      $value =  stripslashes($type);
    } else if ($j == 2) {
      $value = $row['company_name'] . $customer_flag_text;
    } else if ($j == 3) {
      $value = $row['cname'];
    } else if ($j == 4) {
      $value = $row['client_code'];
    } else if ($j == 5) {
      $value = $row['phone'];
    } else if ($j == 6) {
      $value = $row['mobile_no1'];
    } else if ($j == 7) {
      $value = $row['state'];
    } else if ($j == 8) {
      $value = $row['main_city'];
    } else if ($j == 9) {
      $value = $row['city'];
    } else if ($j == 10) {
      $order_r = $db->rp_getData("orders", "order_no,order_date,grand_total", "customer_id='" . $row['id'] . "' AND isDelete=0", "id DESC", "0", 1);
      if ($order_r) {
        $order_d = mysqli_fetch_assoc($order_r);
        $order_dt = date("d-m-Y", strtotime($order_d['order_date']));
        $value = "#" . $order_d['order_no'] . " , " . $order_dt . " , " . $order_d['grand_total'];
      } else {
        $value = "No Order Found";
      }
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
