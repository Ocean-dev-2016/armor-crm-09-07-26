<?php
// exit("hjello");
if (!isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
  $page_id = 616;
  $page_slug = 'prospect_customer';
} else {
  $page_id = 555;
  $page_slug = 'page_executive';
}
include("connect.php");
include('PHPExcel/IOFactory.php');
// $file_name  = "Executive Report"."_".date("d-m-Y").".xlsx";
$file_name  = CUSTOMER_EXPORT_EXCEL;
$Where = "";

// Get the total number of rows in the table

if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
  $Where .= "customer_flag=0 AND ";
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
  $Where .= "customer_flag=1 AND ";
}
if (isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName'])) {
  $Query = $_REQUEST['searchName'];

  $Where .= " (
  cname like '%" . $db->clean($_REQUEST['searchName']) . "%'
  OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
  OR gst  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'  OR zip  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%') AND ";
}

// if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
//   $Query=$_REQUEST['searchName'];
//   $Where.=" (cname like '%".$Query."%' OR company_name like '%".$Query."%' OR phone like '%".$Query."%') AND";
// }

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL) {
  $Where .= " type_of_executive = '" . $_REQUEST['customer_type'] . "' AND ";
}
if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL) {
  $Where .= " type_of_company = '" . $_REQUEST['type_of_company'] . "' AND ";
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
  $Where .= " state = '" . $_REQUEST['state'] . "' AND ";
}

if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
  $ctable_where .= " AND city = '" . $_REQUEST['city'] . "'";
  // $city = $_REQUEST['city'];
}
if (isset($_REQUEST['main_city']) && $_REQUEST['main_city'] != "" && $_REQUEST['main_city'] != NULL) {
  $ctable_where .= " AND main_city = '" . $_REQUEST['main_city'] . "'";
  $city = $_REQUEST['main_city'];
}
// if(isset($_REQUEST['zone']) && $_REQUEST['zone']!="" && $_REQUEST['zone']!=NULL)
// {
//   $Where .= " zone = '".$_REQUEST['zone']."' AND";
// }

if (isset($_REQUEST['price_list']) && $_REQUEST['price_list'] != "" && $_REQUEST['price_list'] != NULL) {
  $Where .= " price_list_id = '" . $_REQUEST['price_list'] . "' AND";
}

if (isset($_REQUEST['seid']) && $_REQUEST['seid'] != "" && $_REQUEST['seid'] != NULL) {
  $Where .= " seid = '" . $_REQUEST['seid'] . "' AND";
}
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
  if ($rights['personal_flag'] == 1) {
    $Where .= " created_by ='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' AND ";
    $customer_type = $db->rp_getValue("sales_executive", "customer_type", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
    $filter_where .= " id IN (" . $customer_type . ") AND ";
  } else {
    if ($rights['all_data_flag'] == 1) {
    } else {
      $customer_type = $db->rp_getValue("sales_executive", "customer_type", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
      //$CustomerType = implode(",", $customer_type);
      $Where .= " type_of_executive IN (" . $customer_type . ") AND ";
      $filter_where .= " id IN (" . $customer_type . ") AND ";
    }
  }
}
if (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != "" && $_REQUEST['category_id'] != "undefined" && $_REQUEST['category_id'] != "null") {
  $ctable_where .= " AND category_id IN (" . implode($_REQUEST['category_id'], ', ') . ")  ";
  $category_id = $_REQUEST['category_id'];
  // echo implode($_REQUEST['category_id'], ", ");exit();
}

if (isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id'] != "" && $_REQUEST['top_category_id'] != "undefined" && $_REQUEST['top_category_id'] != "null") {
  $top_category_idStr = implode(",", $_REQUEST['top_category_id']);
  $ctable_where .= " AND top_category_id IN (" . $top_category_idStr . ")  ";
  $top_category_id = $top_category_idStr;
}

$Where .= " isDelete=0 AND id!=-1  ";

$ctable1_r = $db->rp_getData("executive", "*", $Where . $ctable_where, "company_name ASC", 0);

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
$column16  = 'Q';
$column17  = 'R';
$column18  = 'S';
$column19  = 'T';

$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Type Of company");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Customer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Price List");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Saler Person");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Client Code");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Firm Name");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "Gst No");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "Phone/Mobile");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column11 . $rowCount, "City");
$objPHPExcel->getActiveSheet()->setCellValue($column12 . $rowCount, "Route");
$objPHPExcel->getActiveSheet()->setCellValue($column13 . $rowCount, "Pincode");
$objPHPExcel->getActiveSheet()->setCellValue($column14 . $rowCount, "Address");
$objPHPExcel->getActiveSheet()->setCellValue($column15 . $rowCount, "Billing Address");
$objPHPExcel->getActiveSheet()->setCellValue($column16 . $rowCount, "Turnover");
$objPHPExcel->getActiveSheet()->setCellValue($column17 . $rowCount, "Turnover Year");
$objPHPExcel->getActiveSheet()->setCellValue($column18 . $rowCount, "Customer Entry Type");
$objPHPExcel->getActiveSheet()->setCellValue($column19 . $rowCount, "Customer Create Date");


//end of adding column names  

$rowCount = 2;
$count = 0;
while ($row = mysqli_fetch_assoc($ctable1_r)) {
  $catid = explode(",", $top_category_id);
  $cat_name = array();
  for ($j = 0; $j < sizeof($catid); $j++) {
    $cat_name[] = $db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $catid[$j] . "'", 0);
  }
  $category_name = implode($cat_name, ", ");
  $entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
  $count++;
  $column = 'A';
  for ($j = 0; $j < 20; $j++) {
    if ($j == 0) {
      $value = $count;
    } else if ($j == 1) {
      $value = $db->rp_getValue("company_master", "name", "id='" . $row['type_of_company'] . "'");
    } else if ($j == 2) {
      $value = $db->rp_getValue("customer_type", "name", "id='" . $row['type_of_executive'] . "'");
    } else if ($j == 3) {
      $value = $db->rp_getValue("price_list", "pricelist_name", "id='" . $row['price_list_id'] . "'");
    } else if ($j == 4) {
      $value = $db->rp_getValue("sales_executive", "name", "id='" . $row['seid'] . "' AND isDelete=0", 0);
    } else if ($j == 5) {
      $value = $row['client_code'];;
    } else if ($j == 6) {
      if ($row['gst'] != "") {
        $value = $row['company_name'];
      } else {
        $value = $row['company_name'];
      }
    } else if ($j == 7) {
      $value = $row['cname'];
    } else if ($j == 8)
    // {
    //   $value = $row['cname'];
    // } 
    {
      $value = $row['gst'];
    } else if ($j == 9) {
 $value = trim(
    (!empty($row['phone']) ? $row['phone'] : '') .
    (!empty($row['phone']) && !empty($row['mobile_no1']) ? ' / ' : '') .
    (!empty($row['mobile_no1']) ? $row['mobile_no1'] : '')
);
    } else if ($j == 10) {
      $value = $row['state'];
    } else if ($j == 11) {
      $value = $row['main_city'];
    } else if ($j == 12) {
      $value = $row['city'];
    } else if ($j == 13) {
      $value = $row['zip'];
    } else if ($j == 14) {
      $value = $row['address'];
    } else if ($j == 15) {
      $value = $row['billing_address'];
    } else if ($j == 16) {
      $value = $row['turnover'];
    } else if ($j == 17) {
      $value = $row['turnover_year'];
    } else if ($j == 18) {
      $lead_time = $db->rp_getValue("no_order_inquiry", "created_date", "isDelete=0 AND dealer_id='" . $row['id'] . "'", 0);
      $date = date('Y-m-d', strtotime($row['created_date']));
      $customer_order = $db->rp_getValue("orders", "customer_id", "customer_id='" . $row['id'] . "' AND isDelete=0 AND isActive=1", 0);
      //$convert_customer =$db->rp_getValue("executive","id,customer_flag=0","id='".$customer_order."'",0);
      if ($customer_order) {
        $value = "Prospect Order Convert to Customer";
      } else if ($lead_time) {
        $value = "Inquiry To Lead";
      } else if ($row['entry_flag'] == "1") {
        $value = "Direct Customer";
      } else if ($row['entry_flag'] == "5") {
        $value = "Sales App";
      }
    } else if ($j == 19) {
      $value = date('d-m-Y h:i:s a', strtotime($row['created_date']));
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
// echo INQUIRY_REPORT_FILES.$file_name;exit;
$objWriter->save(INQUIRY_REPORT_FILES . $file_name);
$file_path1 = trim(ADMINFOLDER . "/inquiry_documents/" . $file_name);
$arr = array("file_path" => $file_path1);
require_once 'disconnect.php';
echo json_encode($arr);
