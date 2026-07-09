<?php
$page_id=593;$page_slug='attendance_page';

include("connect.php");
include('PHPExcel/IOFactory.php');
$file_name    = "Attandance_Report" . "_" . date("d-m-Y") . ".xlsx";
$ctable_where = "";
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $sales_id = $db->rp_getData("sales_executive", "*", "name LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%'  AND isDelete=0", "", 0);
    if ($sales_id) {
        while ($K = mysqli_fetch_assoc($sales_id)) {
            $USER_IDS[] = $K['id'];
        }
        $USER_IDS = implode(",", $USER_IDS);
        $ctable_where .= "sales_id IN (" . $USER_IDS . ") AND ";
    } else {
        $ctable_where .= "sales_id IN (0) AND ";
    }
}
if (isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"] != "" && $_REQUEST["sales_executive"] != undefined && $_REQUEST["sales_executive"] != 'null') {
    // $ctable_where .= " sales_id='" . $_REQUEST["sales_executive"] . "' AND ";
    $ctable_where .= " sales_id IN (" . $_REQUEST["sales_executive"] . ") AND ";
    $sid = $_REQUEST["sales_executive"];
}
if (isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"] != "" && $_REQUEST["customer_id"] != '' && $_REQUEST["customer_id"] != undefined) {
    $ctable_where .= " customer_id='" . $_REQUEST["customer_id"] . "' AND ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="")
{

    $sales_executive_r = $db->rp_getData("sales_executive","id","state='". $_REQUEST['state']."' AND isDelete=0 ","",0);
    while ($sales_executive_d = mysqli_fetch_assoc($sales_executive_r)) 
    {
        $sales_executive_arr[] = $sales_executive_d['id'];
    }

    $sales_executive_str = implode(",",$sales_executive_arr);
    // echo $sales_executive_str;exit;

    $ctable_where .=" sales_id IN(".$sales_executive_str.") AND ";

    $state = $_REQUEST['state'];
}

if (isset($_REQUEST['df1']) && $_REQUEST['df1'] != "") {
    $date_filter_query    = urldecode($_REQUEST['df1']);
    $date_filter_query_ex = explode(" to ", $date_filter_query);
    $ctable_where .= " ( DATE(date_time)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(date_time)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) AND ";
}
if (isset($_REQUEST['io']) && $_REQUEST['io'] != "" && $_REQUEST['io'] != undefined && $_REQUEST['io'] != 'null') {
    // $ctable_where .= " inout_status='" . $_REQUEST['io'] . "' AND ";
    $ctable_where .= " inout_status IN ('" . $_REQUEST['io'] . "') AND ";
    $io = $_REQUEST['io'];
}

if(isset($_REQUEST['attendance_type']) && $_REQUEST['attendance_type']!="")
{
    $ctable_where .=" attandance_type='".$_REQUEST['attendance_type']."' AND ";
    
}
$AttandanceType= array('1' =>"In" ,'2' =>"Out" ,'3' =>"Auto Out" ,'4' =>"Logout With Out" ,'5' =>"Out On Nextday","6"=>"Out From Server"  );

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= "  sales_id='".$check_id."' AND ";
}

$ctable_where .= " isDelete=0";
$ctable1_r   = $db->rp_getData("attendance", "*", $ctable_where, "id DESC", 0);
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$rowCount = 1;
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
$column6  = 'G';
$column7  = 'H';
// $column7 = 'H';
// $column6  = 'G';
$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, "Id");
$objPHPExcel->getActiveSheet()->setCellValue($column1 . $rowCount, "Sales Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2 . $rowCount, "CUG No.");
$objPHPExcel->getActiveSheet()->setCellValue($column3 . $rowCount, "Date and Time");
$objPHPExcel->getActiveSheet()->setCellValue($column4 . $rowCount, "In/Out");
// $objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Attendance Type");
$objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column6 . $rowCount, "City");
// $objPHPExcel->getActiveSheet()->setCellValue($column5 . $rowCount, "Imei");
$objPHPExcel->getActiveSheet()->setCellValue($column7 . $rowCount, "Address");
$rowCount = 2;
$count    = 0;
while ($row = mysqli_fetch_array($ctable1_r)) {
    $count++;
    $column = 'A';

    $sales_person_table_r = $db->rp_getData("sales_executive","*","id='".$row['sales_id']."' AND isDelete=0 ","",0);

    $sales_person_table_d = mysqli_fetch_assoc($sales_person_table_r);

    for ($j = 0; $j < 8; $j++) {
        if ($j == 0) {
            $value = $count;
        } else if ($j == 1) {
            $value = $sales_person_table_d['name'];
        } else if ($j == 2) {
            $value = $sales_person_table_d['phone'];
        } else if ($j == 3) {
            $value = date("d-m-Y h:i A", strtotime($row['date_time']));
        } else if ($j == 4) {
            $value = $row['inout_status'];
        } 
        /*else if ($j == 5) {
            
            $value = $AttandanceType[$row['attandance_type']];
        } */ 
        else if ($j == 5) 
        {
            $value = $sales_person_table_d['state'];
        }
        else if ($j == 6) 
        {
             $address = $row['app_address'];
             $arr = explode(',', $address);
            $value = $arr[count($arr) - 3];
        }
         else if ($j == 7)
        {
            $value = $row['app_address'];
        } else {
            $value = $row[$j];
        }
        $objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $value);
        $column++;
    }
    $rowCount++;
}
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=" . $file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES . $file_name);
$file_path1 = trim(ADMINFOLDER . "/inquiry_documents/" . $file_name);
$arr        = array(
    "file_path" => $file_path1
);
require_once 'disconnect.php';
echo json_encode($arr);
?>