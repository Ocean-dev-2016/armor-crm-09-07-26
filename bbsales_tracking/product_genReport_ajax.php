<?php
$page_id = 559;
$page_slug = 'page_product';
include("connect.php");
include('PHPExcel/IOFactory.php');
$file_name  = "Product_Report" . "_" . date("d-m-Y") . ".xlsx";
$Where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']);

// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $where11 = "";
  $pro_r1 = $db->rp_getData("product_weight_price", "product_id", "catno LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
  $PROIDS1 = array();
  if ($pro_r1) {
    while ($pro_d1 = mysqli_fetch_assoc($pro_r1)) {
      $PROIDS1[] = $pro_d1['product_id'];
    }
  }
  if (!empty($PROIDS1)) {
    $PROIDS1 = implode(",", $PROIDS1);
    $where11 = " OR id IN (" . $PROIDS1 . ")";
  }
  $ctable_where .= " (name like '%" . $_REQUEST['searchName'] . "%' " . $where11 . ") AND ";
}

$ctable_where .= " 1=1 AND isDelete='0' AND id!='0'";
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {

  if ($rights['personal_flag'] == 1) {

    $ctable_where .= " AND created_by='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";
  } else {


    if ($rights['chain_vise_flag'] == 1) {


      $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

      $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
      if ($get_sales_type == "sales_manager") {
        $sales_executive_type = "Regional Sales Manager";
        $key = "sm_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "area_sales_manager") {
        $sales_executive_type = "National Sales Manager"; //Business Development Manager
        $key = "asm_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "sales_officer") {
        $sales_executive_type = "Area Sales Manager"; //Area Sales Manager
        $key = "so_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "sales_executive") {
        $sales_executive_type = "Sales Officer";
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

        $ctable_where .= " AND created_by IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . ")";
      } else {
        $ctable_where .= " AND  created_by IN (" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . ")";
      }
    } else {
      $ctable_where .= " ";
    }
  }
} else {

  $ctable_where .= " ";
}
if (isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id'] != "" && $_REQUEST['top_category_id'] != NULL) {
  $ctable_where .= " AND tcid = '" . $_REQUEST['top_category_id'] . "' ";
}
if (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != "" && $_REQUEST['category_id'] != NULL) {
  $ctable_where .= " AND cid = '" . $_REQUEST['category_id'] . "' ";
}

if (isset($_REQUEST['product_type']) && $_REQUEST['product_type'] != "" && $_REQUEST['product_type'] != NULL && $_REQUEST['product_type'] != undefined) {
  $ctable_where .= " AND product_type = '" . $_REQUEST['product_type'] . "' ";
  $product_type = $_REQUEST['product_type'];
}

if (isset($_REQUEST['unit_id']) && $_REQUEST['unit_id'] != "" && $_REQUEST['unit_id'] != NULL && $_REQUEST['unit_id'] != undefined) {
  $product_weight_data_r = $db->rp_getData("product_weight_price", "product_id", "isDelete=0 AND inner_unit='" . $_REQUEST['unit_id'] . "' OR outer_unit='" . $_REQUEST['unit_id'] . "'", "", 0);

  while ($product_weight_data_d = mysqli_fetch_array($product_weight_data_r)) {
    $product_weight_data_arr[] = $product_weight_data_d['product_id'];
  }

  $product_weight_data_str = implode(",", $product_weight_data_arr);
  // echo $product_weight_data_str;exit;

  $ctable_where .= " AND id IN(" . $product_weight_data_str . ") ";
}

if (isset($_REQUEST['sales_order_unit_filter']) && $_REQUEST['sales_order_unit_filter'] != "" && $_REQUEST['sales_order_unit_filter'] != NULL && $_REQUEST['sales_order_unit_filter'] != undefined) {
  $ctable_where .= " AND unit_id = '" . $_REQUEST['sales_order_unit_filter'] . "' OR customer_unit_id='" . $_REQUEST['sales_order_unit_filter'] . "' ";
  // $sales_order_unit_filter = $_REQUEST['sales_order_unit_filter'];
}

$ctable1_r = $db->rp_getData("product", "id,product_type,name,tcid,cid,igst,unit_id,customer_unit_id,hsn_code", $ctable_where, "id DESC", 0);

/*for log*/
$flag = "Web";
$module_name = "Product";
$log_description = $module_name . " Export Excel By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
$last_id = "";
$ctable = "product";
$db->insertLog($ctable, $last_id, "excel", "", $insert, 0, $log_description, $flag, $module_name, $user_id, "");
/*for log*/

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



$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Type");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Product Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Product Code");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Price");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Category");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Sub Category");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "GST");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Order Unit");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "Inner / Outer Unit");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "HSN Code");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount, "Min Sell Price");

//end of adding column names  

$rowCount = 2;
$count = 0;
while ($row = mysqli_fetch_assoc($ctable1_r)) {
  $top_category_name = $db->rp_getValue("top_category_master", "name", "id='" . $row['tcid'] . "'");
  $category_name = $db->rp_getValue("category_master", "name", "id='" . $row['cid'] . "'");
  $TYPE = array('1' => "With Variant", '2' => "Without Variant");

  $unit_r = $db->rp_getData("product_weight_price", "inner_unit,outer_unit", "product_id='" . $row['id'] . "' AND isDelete=0", "", 0);

  $unit_d = mysqli_fetch_array($unit_r);

  $unit_arr = array("1" => "Caret", "2" => "Big Box", "100" => "Nos", "-1" => "Box", "-2" => "Strip", "-3" => "Pallet");

  $column = 'A';
  for ($j = 0; $j < 11; $j++) {
    if ($j == 0) {
      $value = $TYPE[$row['product_type']];
    }
    if ($j == 1) {
      $value = $row['name'];
    } else if ($j == 2) {
      $pro_r1 = $db->rp_getData("product_weight_price", "catno,price", "product_id='" . $row['id'] . "' AND isDelete=0", "", 0);
      if ($pro_r1) {
        $PROIDS = array();
        while ($pro_d1 = mysqli_fetch_assoc($pro_r1)) {
          // $PROIDS[]=$pro_d1['catno']."----"."&#x20B9;".$pro_d1['price'];
          // $PROIDS[]=$pro_d1['catno']."----".$pro_d1['price'];
          $PROIDS[] = $pro_d1['catno'];
        }
        $PROIDS = implode(" / ", $PROIDS);
        $value = $PROIDS;
      }
    } else if ($j == 3) {
      $value = $db->rp_getValue("product_weight_price", "price", "product_id='" . $row['id'] . "' AND isDelete=0", "", 0);
    } else if ($j == 4) {
      $value = $top_category_name;
    } else if ($j == 5) {
      $value = $category_name;
    } else if ($j == 6) {
      $value = $row['igst'];
    } else if ($j == 7) {
      $value = "Sales Order Unit : " . $unit_arr[$row['unit_id']] . ", Customer Order Unit : " .
        $unit_arr[$row['customer_unit_id']];
    } else if ($j == 8) {
      $value = "Inner Unit : " . $unit_arr[$unit_d['inner_unit']] . ", Outer Unit : " . $unit_arr[$unit_d['outer_unit']];
    } else if ($j == 9) {
      $value = $row['hsn_code'];
    } else if ($j == 10) {
      $value = $db->rp_getValue("product_weight_price", "minimum_selling_price", "product_id='" . $row['id'] . "' AND isDelete=0", "", 0);
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
