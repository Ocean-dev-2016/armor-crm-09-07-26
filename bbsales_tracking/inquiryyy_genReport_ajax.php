<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 

// $file_name  = "Visit_Report"."_".date("d-m-Y").".xlsx";
$file_name  = INQUIRY_REPORT_EXPORT_EXCEL;
$ctable_where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $ctable_where .="company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%'  AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="" && $_REQUEST['status_id']!="null")
{
  $ctable_where .= " status IN (".$_REQUEST['status_id'].") AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL && $_REQUEST['type']!="null")
{
  $ctable_where .= "  sales_executive_id IN (".$_REQUEST['type'].") AND ";
}

if(isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company']!="" && $_REQUEST['type_of_company']!=NULL && $_REQUEST['type_of_company']!="null")
{
  $ctable_where .= "  type_of_company IN (".$_REQUEST['type_of_company'].") AND ";
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
  $date_filter_query = urldecode( $_REQUEST['df'] );
  $date_filter_query_ex=explode(" to ",$date_filter_query);
  $ctable_where .= " AND ( DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!="null")
{
    $state_r = $db->rp_getData("state","name","id in (".$_REQUEST['state'].")","",0);
    while($state_d = mysqli_fetch_array($state_r)) 
    {
        $state_str[] = "'".$state_d['name']."'";
    }
    $class_str = implode(",",$state_str);
    $ctable_where .= "   state IN (".$class_str.") AND ";
    $isFillter=true;
}
//for area----//
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL && $_REQUEST['city']!="null")
{
    $city_r = $db->rp_getData("city","name","id in (".$_REQUEST['city'].")","",0);
    while($city_d = mysqli_fetch_array($city_r)) 
    {
        $city_str[] = "'".$city_d['name']."'";
    }
    // echo implode(",",$city_str);exit;
    $ctable_where .= "  main_city IN (".implode(",",$city_str).") AND ";
    $isFillter=true;
            
}

if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
{
    $area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
    while($area_d = mysqli_fetch_array($area_r)) 
    {
        $area_str[] = "'".$area_d['name']."'";
    }
    // echo implode(",",$area_str);exit;
    $ctable_where .= "  city IN (".implode(",",$area_str).") AND ";
    $isFillter=true;
            
}


//service_executive user not show condition start --//
  $SEID=array();
  $sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
  while($sales_type_d = mysqli_fetch_array($sales_type_r))
  {
    $SEID[] = $sales_type_d['id'];
  }
  $SEID=implode(",",$SEID);
  $ctable_where .="   sales_executive_id NOT IN ('".$SEID."') AND ";
//service_executive user not show condition end --//  
  
if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="")
{
  $date_filter_query = urldecode( $_REQUEST['df1'] );
  $date_filter_query_ex=explode(" to ",$date_filter_query);
  $ctable_where .= " ( DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )  AND ";
}

$ctable_where .= " isDelete=0 AND inquiry_lead_flag=0";
$ctable1_r = $db->rp_getData("no_order_inquiry","id,source_of_inquiry,id,company_name,person_name,mobile_number,state,main_city,city,description,datetime,status,sales_executive_id",$ctable_where,"id DESC",0);  
 
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
$column10 = 'K';
$column11 = 'L';
$column12 = 'M';

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Source Of Inquiry");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Inquiry No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Firm Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Persoan Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Phone");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "State");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "City");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Route");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Description");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Inquiry Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Inquiry Taken By");
//end of adding column names  
 
$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_array($ctable1_r))  
{ 
  $inquiry_status_array = array("0"=>"Generate","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-2"=>"Non Relavent","-1"=>"Not Interested","11"=>"Lost");
  $count++;
  $column = 'A';
  // print_r($row);exit;
  for($j=0; $j<12;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $db->rp_getValue("source_of_inquiry","name","id='".$row['source_of_inquiry']."'");
    }
    else if($j==2)
    {
      $value = "#INQ/" . $row[$j];
    }
     else if($j==9)
    {
      $value= date('d-m-Y',strtotime($row['datetime']));
    }
    else if($j==10)
    {
      $value= $inquiry_status_array[$row['status']];
    }
    else if($j==11)
    {
      $sales_executive=$db->rp_getValue("sales_executive","name","id='".$row['sales_executive_id']."'");
      $value = $sales_executive; 
    }
    else
    {
      $value = $row[$j];   
      // $value = "";   
    }

    // echo $value;
    // echo "<br/>"; exit;  
    $value = (isset($value) && $value!="")?$db->clean($value):"";
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  } 
  $rowCount++;
}
// echo "string";exit;
// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php'; 
echo json_encode($arr); 
?>