<?php
$page_id = 572;
$page_slug = 'no_order_inquiry_page';
include("connect.php");
include('PHPExcel/IOFactory.php');
$file_name  = "Inquiry Report" . date('d_m_Y') . "_" . strtotime("now") . ".xlsx";
$Where = "";
// $_REQUEST['searchName']=urldecode($_REQUEST['searchName']);

// // Get the total number of rows in the table

// if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
//   $Query=$_REQUEST['searchName'];
//   //$Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%' OR id like '%".$Query."%' OR person_name like '%".$Query."%' OR country like '%".$Query."%' OR state like '%".$Query."%' OR city like '%".$Query."%' OR email_address like '%".$Query."%' OR pincode like '%".$Query."%') AND ";

//     $Where.=" (company_name like '%".$Query."%' OR mobile_number like '%".$Query."%'  OR person_name like '%".$Query."%' OR pincode like '%".$Query."%') AND ";

// }

// if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']!="" && $_REQUEST['c_type']!=NULL)
// {
//   $Where .= "executive_type = '".$_REQUEST['c_type']."' AND ";
// }
// // echo "string";exit();
// if(isset($_REQUEST['company_type']) && $_REQUEST['company_type']!="" && $_REQUEST['company_type']!=NULL)
// {
//     // echo "hell0";die;
//     $Where .= " AND type_of_company = '".$_REQUEST['company_type']."' ";
//     $company_type=$_REQUEST['company_type'];
// }
// if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
// {
//   $Where .= "status= '".$_REQUEST['status_id']."' AND ";
// }

// if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
// {
//  $Where .= "inquiry_created_by = '".$_REQUEST['type']."' AND ";
// }

// if(isset($_REQUEST['industry_type']) && $_REQUEST['industry_type']!="")
// {
//     $Where .= " industry_type_id='".$_REQUEST['industry_type']."' AND ";
//     $industry_type=$_REQUEST['industry_type'];
// }

// if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
// {
//  $Where .= "inquiry_assign_to = '".$_REQUEST['assigned_to']."' AND ";
// }

// if(isset($_REQUEST['dealer_id']) && $_REQUEST['dealer_id']!="" && $_REQUEST['dealer_id']!=NULL)
// {
//  $Where .= "dealer_id = '".$_REQUEST['dealer_id']."' AND ";
// }


// if(isset($_REQUEST['country']) && $_REQUEST['country']!="" && $_REQUEST['country']!=NULL)
// {
//   $Where .= "country = '".$_REQUEST['country']."' AND ";
// }

// if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
// {
//   $Where .= "state = '".$_REQUEST['state']."' AND ";
// }

// if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
// {
//   $Where .= "main_city = '".$_REQUEST['city']."' AND ";
// }
// if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL)
// {
//     $Where .= " city = '".$_REQUEST['route']."' AND ";
//     $route=$_REQUEST['route'];
// }
// // update code sagar //
// if(isset($_REQUEST['source_id']) && $_REQUEST['source_id']!="" && $_REQUEST['source_id']!=NULL)
// {
//     $Where .= "  source_of_inquiry = '".$_REQUEST['source_id']."' AND ";
//     $source_id=$_REQUEST['source_id'];
// }
// // update code sagar //

// if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
//     //echo $_REQUEST['df'];exit;
//     $date_filter_query = urldecode( $_REQUEST['df'] );

//     $date_filter_query_ex=explode(" to ",$date_filter_query);

//     $Where .= " ( DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
// }

// if(isset($_REQUEST['end_followup']) && $_REQUEST['end_followup']!="")
// {
//     $Where .= " followup_reason_id='".$_REQUEST['end_followup']."' AND ";
//     // $status_id=$_REQUEST['status_id'];
// }

// // if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// // {
// //   $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
// //   $Where .= " (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') AND ";
// // }



// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//   if($rights['personal_flag']==1)
//   {
//    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//    $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
//   }
//   else
//   {
//     if($rights['chain_vise_flag'] == 1)
//     {
//       $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//       $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
//       if ($get_sales_type== "sales_manager") 
//       {
//         $sales_executive_type = "Regional Sales Manager";
//         $key="sm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//       }
//       else if ($get_sales_type == "area_sales_manager") 
//       {
//         $sales_executive_type = "National Sales Manager";//Business Development Manager
//         $key="asm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//       }
//       else if ($get_sales_type == "sales_officer") 
//       {
//         $sales_executive_type = "Area Sales Manager";//Area Sales Manager
//         $key="so_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//       }
//       else if ($get_sales_type == "sales_executive") 
//       {
//         $sales_executive_type = "Sales Officer";
//         $key="se_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//       }
//       else
//       {
//         $WhereCondition.=' type = "service_engineer"';
//       }

//       $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);
//       $SALEID1=array();
//       if($data)
//       {
//         while($data_d=mysqli_fetch_assoc($data))
//         {
//             $SALEID1[]=$data_d['id'];
//         }
//       }
//       if(!empty($SALEID1))
//       {
//         $SALEID1=implode(",", $SALEID1);
//         $ctable_where .= " AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID']."))";         
//       }
//       else
//       {
//         $ctable_where .= " AND ( inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";      
//       }
//     } 
//   } 
// }

// if($_REQUEST['inquiry_type']=="-1")
// { 
//   $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '-1'";
// }
// else if($_REQUEST['inquiry_type']=="0")
// { 
//   $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '0'";
// }
// else
// {
//   $Where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
// }
$datas = json_decode($_REQUEST['data'], true);
$ctable = $datas['table'];
$Where = $datas['where'];
$columns = $datas['data'];
$order_by = $datas['order_by'];
$limit = $datas['limit'];
// $ctable1_r = $db->rp_getData("no_order_inquiry", "*", $Where, "id DESC", 0);
$ctable1_r = $db->rp_getData($ctable, $columns, $Where, $order_by, 0, $limit);

/*for log*/
$flag = "Web";
if ($_REQUEST['inquiry_type'] == "-1") {
  $ctable = "no_order_inquiry";
  $module_name = "Raw Data";
  $log_description = $module_name . " Export Excel By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
} else if ($_REQUEST['inquiry_type'] == "0") {
  $ctable = "no_order_inquiry";
  $module_name = "Inquiry";
  $log_description = $module_name . " Export Excel By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
} else {
  $ctable = "no_order_inquiry";
  $module_name = "Lead";
  $log_description = $module_name . " Export Excel By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
}
$last_id = "";
$db->insertLog($ctable, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $user_id);
/*for log*/
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '1000MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

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
$column20  = 'U';
$column21  = 'V';
$column22  = 'W';
$column23  = 'X';
$column24  = 'Y';
$column25  = 'Z';
$column26  = 'AA';
$column27  = 'AB';
$column28  = 'AC';
$column29  = 'AD';
$column30  = 'AE';
$column31  = 'AF';
$column32  = 'AG';

$column33  = 'AH';
$column34  = 'AI';
$column35  = 'AJ';
$column36  = 'AK';
$column37  = 'AL';
$column38  = 'AM';
$column39  = 'AN';
$column40  = 'AO';


$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Inquiry No");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "Status");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Company Type");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "Source Of Medium");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Customer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "Firm Name");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column8 . $rowCount, "GST No.");
$objPHPExcel->getActiveSheet()->setCellValue($column9 . $rowCount, "Mobile Number");
$objPHPExcel->getActiveSheet()->setCellValue($column10 . $rowCount, "Email Address");
$objPHPExcel->getActiveSheet()->setCellValue($column11 . $rowCount, "Description");
$objPHPExcel->getActiveSheet()->setCellValue($column12 . $rowCount, "Country");
$objPHPExcel->getActiveSheet()->setCellValue($column13 . $rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column14 . $rowCount, "City");
$objPHPExcel->getActiveSheet()->setCellValue($column15 . $rowCount, "Route");
$objPHPExcel->getActiveSheet()->setCellValue($column16 . $rowCount, "Pincode");
$objPHPExcel->getActiveSheet()->setCellValue($column17 . $rowCount, "Zone");
$objPHPExcel->getActiveSheet()->setCellValue($column18 . $rowCount, "Inquiry Date");
$objPHPExcel->getActiveSheet()->setCellValue($column19 . $rowCount, "Inquiry Taken By");
$objPHPExcel->getActiveSheet()->setCellValue($column20 . $rowCount, "Inquiry Assign To");
$objPHPExcel->getActiveSheet()->setCellValue($column21 . $rowCount, "Date Of Call");
$objPHPExcel->getActiveSheet()->setCellValue($column22 . $rowCount, "Birth Date");
$objPHPExcel->getActiveSheet()->setCellValue($column23 . $rowCount, "Shipping Address");
$objPHPExcel->getActiveSheet()->setCellValue($column24 . $rowCount, "Billing Address");
$objPHPExcel->getActiveSheet()->setCellValue($column25 . $rowCount, "Address");
$objPHPExcel->getActiveSheet()->setCellValue($column26 . $rowCount, "Industry Type");
$objPHPExcel->getActiveSheet()->setCellValue($column27 . $rowCount, "End Followup Reason");
$objPHPExcel->getActiveSheet()->setCellValue($column28 . $rowCount, "Cancel Reason");
$objPHPExcel->getActiveSheet()->setCellValue($column29 . $rowCount, "Quotation Lost Reason");
$objPHPExcel->getActiveSheet()->setCellValue($column30 . $rowCount, "Inquiry Type");
$objPHPExcel->getActiveSheet()->setCellValue($column31 . $rowCount, "Entry Type");
$objPHPExcel->getActiveSheet()->setCellValue($column32 . $rowCount, "Update Entry Type");

$objPHPExcel->getActiveSheet()->setCellValue($column33 . $rowCount, "Mobile No1");
$objPHPExcel->getActiveSheet()->setCellValue($column34 . $rowCount, "Name1");
$objPHPExcel->getActiveSheet()->setCellValue($column35 . $rowCount, "Mobile No2");
$objPHPExcel->getActiveSheet()->setCellValue($column36 . $rowCount, "Name2");
$objPHPExcel->getActiveSheet()->setCellValue($column37 . $rowCount, "Mobile No3");
$objPHPExcel->getActiveSheet()->setCellValue($column38 . $rowCount, "Name3");
$objPHPExcel->getActiveSheet()->setCellValue($column39 . $rowCount, "Mobile No4");
$objPHPExcel->getActiveSheet()->setCellValue($column40 . $rowCount, "Name4");

//end of adding column names  

$rowCount = 2;
$count = 0;
$u_w_flag_arr = array('1' => "YES", '0' => "NO");
$quotation_flag_arr = array('1' => "YES", '2' => "NO");

$inquiry_type_array_data_r = $db->rp_getData("source_of_inquiry", "*", "isDelete=0", "", 0);
$inquiry_type_array_data = array();
while ($inquiry_type_array_data_d = mysqli_fetch_assoc($inquiry_type_array_data_r)) {
  $inquiry_type_array_data[$inquiry_type_array_data_d['id']] = $inquiry_type_array_data_d['name'];
}
$inquiry_type_array = $inquiry_type_array_data;
while ($row = mysqli_fetch_array($ctable1_r)) {
  $status_array = array("0" => "Generate", "1" => "In Followup", "2" => "Position", "3" => "Buy Later", "4" => "Hot", "5" => "Cold", "6" => "Warm", "-1" => "My Work", "-2" => "Cancel", "11" => "Lost");
  $entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
  $inq_statuss = array("-1" => "Prospect", "0" => "Inquiry", "1" => "Lead", "2" => "Convert To Inquiry", "3" => "Convert To Lead");
  $count++;
  $column = 'A';
  for ($j = 0; $j < 41; $j++) {
    if ($j == 0) {
      $value = $count;
    } else if ($j == 1) {
      $value =  "INQ/" . $row['id'];
    } else if ($j == 2) {
      $value = $status_array[$row['status']];
    } else if ($j == 3) {
      $value = $db->rp_getValue("company_master", "name", "id='" . $row['type_of_company'] . "'");
    } else if ($j == 4) {
      $value = $inquiry_type_array[$row['source_of_inquiry']];
    } else if ($j == 5) {
      $value = $db->rp_getValue("customer_type", "name", "id='" . $row['executive_type'] . "'");
    } else if ($j == 6) {
      $value = $row['company_name'];
    } else if ($j == 7) {
      $value = $row['person_name'];
    } else if ($j == 8) {
      $value = $row['gst_no'];
    } else if ($j == 9) {
      $value = $row['mobile_number'];
    } else if ($j == 10) {
      $value = $row['email_address'];
    } else if ($j == 11) {
      $description = htmlspecialchars_decode($row['description']);
      $description1 = str_replace("<", "", $description);
      $description2 = str_replace(">", "", $description1);
      $value = $row['description'];
    } else if ($j == 12) {
      $value = $row['country'];
    } else if ($j == 13) {
      $value = $row['state'];
    } else if ($j == 14) {
      $value = $row['main_city'];
    } else if ($j == 15) {
      $value = $row['city'];
    } else if ($j == 16) {
      $value = $row['pincode'];
    } else if ($j == 17) {
      $value = $db->rp_getValue("zone", "name", "id='" . $row['zone'] . "'", 0);
    } else if ($j == 18) {
      $value = date('d-m-Y', strtotime($row['datetime']));
    } else if ($j == 19) {
      $value = $db->rp_getValue("sales_executive", "name", "id='" . $row['inquiry_created_by'] . "'", 0);
    } else if ($j == 20) {
      $value = $db->rp_getValue("sales_executive", "name", "id='" . $row['inquiry_assign_to'] . "'");
    } else if ($j == 21) {
      $value = date('d-m-Y', strtotime($row['date_of_call']));
    } else if ($j == 22) {
      $value = date('d-m-Y', strtotime($row['birth_date']));
    } else if ($j == 23) {
      $value = $row['shipping_address'];
    } else if ($j == 24) {
      $value = $row['billing_address'];
    } else if ($j == 25) {
      $value = $row['address'];
    } else if ($j == 26) {
      $value = $db->rp_getValue("industry_type", "name", "id='" . $row['industry_type_id'] . "'");
    } else if ($j == 27) {
      $value = $db->rp_getValue("followup_reason", "name", "id='" . $row['followup_reason_id'] . "'");
    } else if ($j == 28) {
      $value = $row['cancel_inq_remark'];
    } else if ($j == 29) {
      $value = $row['lost_reason'];
    } else if ($j == 30) {
      $value = $inq_statuss[$row['inq_status']];
    } else if ($j == 31) {
      $value = $entry_type_status[$row['entry_flag']];
    } else if ($j == 32) {
      $value = $entry_type_status[$row['update_entry_flag']];
    } else if ($j == 33) {
      $value = $db->rp_getValue("customer_vs_phone_no", "phone_no", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 0,1", 0);
    } else if ($j == 34) {
      $value = $db->rp_getValue("customer_vs_phone_no", "name", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 0,1", 0);
    } else if ($j == 35) {
      $value = $db->rp_getValue("customer_vs_phone_no", "phone_no", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 1,2", 0);
    } else if ($j == 36) {
      $value = $db->rp_getValue("customer_vs_phone_no", "name", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 1,2", 0);
    } else if ($j == 37) {
      $value = $db->rp_getValue("customer_vs_phone_no", "phone_no", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 2,3", 0);
    } else if ($j == 38) {
      $value = $db->rp_getValue("customer_vs_phone_no", "name", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 2,3", 0);
    } else if ($j == 39) {
      $value = $db->rp_getValue("customer_vs_phone_no", "phone_no", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 3,4", 0);
    } else if ($j == 40) {
      $value = $db->rp_getValue("customer_vs_phone_no", "name", "customer_id='" . $row['id'] . "' AND ref_table='no_order_inquiry' LIMIT 3,4", 0);
    }
    $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $value);
    $column++;
  }
  $rowCount++;
}
// Redirect output to a client’s web browser (Excel5) 

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8");
header("Content-Disposition: attachment;filename=" . $file_name);
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES . $file_name);
$file_path1 = trim(ADMINFOLDER . "/inquiry_documents/" . $file_name);
$arr = array("file_path" => $file_path1);
require_once 'disconnect.php';
echo json_encode($arr);
