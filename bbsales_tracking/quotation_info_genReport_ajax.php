<?php
$page_id = 607;
include("connect.php");
include('PHPExcel/IOFactory.php');
// $file_name  = "Order Info Report"."_".date("d-m-Y").".xlsx";
$file_name  = QUOTATION_EXPORT_EXCEL;
$Where = "";

// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $Where .= " (customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR quotation_no like '%" . $db->clean($_REQUEST['searchName']) . "%' ) AND ";
}



if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] != "") {
  $Where .= "  inquiry_id = '" . $db->clean($_REQUEST['inquiry_id']) . "' AND";
}

if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != ""  && $_REQUEST['sales_executive_id'] != 'undefined') {
  $Where .= "  sales_id = '" . $db->clean($_REQUEST['sales_executive_id']) . "' AND";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "") {
  $Where .= "  customer_type = '" . $db->clean($_REQUEST['customer_type']) . "' AND";
}
if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "") {
  $Where .= "  type_of_company = '" . $db->clean($_REQUEST['type_of_company']) . "' AND";
}

//for admin login
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
  /*if($_REQUEST['order_type'])
  	{
  	$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
  	}
  	else
  	{*/
  $Where .= " isDelete=0 AND status!=-1";
  //}

} else {
  /*$ctable_where .= " isDelete=0 AND status!=-1 AND created_by='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";*/
  $Where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
}
// for customer login
// else
// {
//   if($_REQUEST['order_type'] && $_REQUEST['uid'])
// 	{
// 	$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
// 	}
// 	else if($_REQUEST['order_type'])
// 	{
// 		$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'  AND status!=-1";
// 	}
// 	else{
//     $Where .= " isDelete=0 AND customer_type!='normal_user'  AND status!=-1 ";
// 	}
// }

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if (!is_numeric($page_number)) {
    die('Invalid page number!');
  } //incase of invalid page number
} else {
  $page_number = 1; //if there's no page number, set it to 1
}
//status
if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL) {
  $Where .= "  AND status = '" . $_REQUEST['status'] . "' ";
}
///For ToDate & FromDate
if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && $_REQUEST['ToDate'] != undefined) {
  $Where .= " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL && $_REQUEST['FromDate'] != undefined) {
  $Where .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
  //echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode($_REQUEST['df']);

  $date_filter_query_ex = explode(" to ", $date_filter_query);

  $Where .= "  AND ( DATE(quotation_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(quotation_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL) {
  $Where .= " AND customer_type = '" . $_REQUEST['type'] . "' ";
}
if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL && $_REQUEST['sales_id'] != undefined) {
  $Where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "' ";
}




//$Where .= " AND isDelete=0";
$ctable1_r = $db->rp_getData("quotation_detail", "*", $Where, "id DESC", 0);

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
$column13  = 'O';
// $column13  = 'N';
// $column14  = 'O';


$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Sr No");

// $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Order No");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Quotation No");
// $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Revised From  Quotation No.");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Inquiry No.");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Quotation Date");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Status");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Type Of Company");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Company Name");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "Client Code");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "Company Mobile No");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount, "Sales Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column11 . $rowCount, "Quotation Type");
$objPHPExcel->getActiveSheet()->setCellValue($column12 . $rowCount, "Order Amount");
$objPHPExcel->getActiveSheet()->setCellValue($column13 . $rowCount, "Lost Reason");
// $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Entry Type");
// $objPHPExcel->getActiveSheet()->setCellValue($column13.$rowCount, "Update Entry Type");

//end of adding column names  

$rowCount = 2;
$count = 0;

$entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");

$customer_type = array('1' => "Super Stockist", '2' => "Distributor", '3' => "Dealer", '3' => "B2B Customer", 'normal_user' => "Normal Customer");

$status_array = array("-2" => "Disapproved", "0" => "Pending", "1" => "Approved", "3" => "Cancelled", "-1" => "Add to Cart", "4" => "Order Generated", "5" => "Lost");

while ($row = mysqli_fetch_assoc($ctable1_r)) {
  $count++;
  $column = 'A';
  $customer_flag = "";
  if ($row['customer_flag'] == 1) {
    $customer_flag = " - P";
  } else if ($row['customer_flag'] == 0) {
    $customer_flag = " - C";
  }
  for ($j = 0; $j < 13; $j++) {
    if ($j == 0) {
      $value = $count;
    }
    // else if($j==1)
    // {
    //   $value = $row['order_no'];
    // }
    else if ($j == 1) {
      $value = $row['quotation_no'];
    }
    // else if($j==2)
    // {
    //   $value =  $db->rp_getValue("quotation_detail","quotation_no","id='".$row['refrence_id']."'"); 
    // }
    else if ($j == 2) {
      $value = "#INQ/" . $row['inquiry_id'];
    } else if ($j == 3) {
      $value = date('d-m-Y', strtotime($row['quotation_date']));
    } else if ($j == 4) {
      $value = $status_array[$row['status']];
    } else if ($j == 5) {
      $value = $db->rp_getValue("company_master", "name", "id='" . $row['type_of_company'] . "'");
    } else if ($j == 6) {
      $value = $row['company_name'] . $customer_flag;
    } else if ($j == 7) {
      $value = $row['customer_name'];
    } else if ($j == 8) {
      $value = $row['client_code'];
    } else if ($j == 9) {
      $value = $row['contact_number'];
    } else if ($j == 10) {
      $sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $row['sales_id'] . "'");
      if ($sales_name == "") {
        $value =  "Admin";
      } else {
        $value = $sales_name;
      }
    } else if ($j == 11) {
      $value = $db->rp_getValue("customer_type", "name", "id='" . $row['customer_type'] . "'");
    } else if ($j == 12) {
      $value = $db->rp_num(round($row['grand_total']));
    } else if ($j == 13) {
      $value =  $row['lost_reason'];
    }

    // else if($j==12)
    // {
    //   $value = $entry_type_status[$row['entry_flag']];
    // } 
    // else if($j==13)
    // {
    //   $value = $entry_type_status[$row['update_entry_flag']];
    // } 

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
$arr = array("file_path" => trim($file_path1));
require_once "disconnect.php";
echo json_encode($arr);
