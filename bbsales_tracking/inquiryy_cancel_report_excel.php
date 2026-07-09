<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Inquiry_Cancel_Report"."_".date("d-m-Y").".xlsx";
$Where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  
  $Where .="company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%'  AND ";
}

$date_select="";

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!=""){
  //echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode( $_REQUEST['df1'] ); 

  $date_filter_query_ex=explode(" to ",$date_filter_query);

  $Where = " DATE(datetime)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(datetime)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."' AND  ";

  $or_r=$db->rp_getData("orders","customer_id",$where1,"",0);
  if($or_r)
  {
    $CUSTID=array();
    while($or_d=mysqli_fetch_assoc($or_r))
    {
      $CUSTID[]=$or_d['customer_id'];
    }
    $CUSTID=implode(",",$CUSTID);
    if($_REQUEST['customer_status']==1)
    {
      $Where .="  id IN (".$CUSTID.") AND ";
    }
    else
    {
      $Where .="  id NOT IN (".$CUSTID.") AND ";
    }
  }
  else
  {
    if($_REQUEST['customer_status']==1)
    {
      $Where .=" AND id IN (0) ";
    }
    else
    {
      $Where .=" ";
    }
  }
}
if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!="null")
{
    $state_r = $db->rp_getData("state","name","id in (".$_REQUEST['state'].")","",0);
    while($state_d = mysqli_fetch_array($state_r)) 
    {
        $state_str[] = "'".$state_d['name']."'";
    }
    $class_str = implode(",",$state_str);
    $Where .= "   state IN (".$class_str.") AND ";
   
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
    $Where .= "  main_city IN (".implode(",",$city_str).") AND ";
   
            
}

if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
{
    $area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
    while($area_d = mysqli_fetch_array($area_r)) 
    {
        $area_str[] = "'".$area_d['name']."'";
    }
    // echo implode(",",$area_str);exit;
    $Where .= "  city IN (".implode(",",$area_str).") AND ";
   
            
}

if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!=NULL && $_REQUEST['customer_id']!=null && $_REQUEST['customer_id']!="NULL" && $_REQUEST['customer_id']!="null" && $_REQUEST['customer_id']!=UNDEFINED && $_REQUEST['customer_id']!=undefined && $_REQUEST['customer_id']!="UNDEFINED" && $_REQUEST['customer_id']!="undefined")
{
   $Where .= " id ='".implode(" , ", $_REQUEST['customer_id'])."' AND ";
}

// if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL && $_REQUEST['state']!=null && $_REQUEST['state']!="NULL" && $_REQUEST['state']!="null" && $_REQUEST['state']!=UNDEFINED && $_REQUEST['state']!=undefined && $_REQUEST['state']!="UNDEFINED" && $_REQUEST['state']!="undefined")
// {
  
//   $Where .= " state ='".implode(" , ", $_REQUEST['state'])."' AND ";
//   $state=$_REQUEST['state'];
// }


// if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL && $_REQUEST['city']!=null && $_REQUEST['city']!="NULL" && $_REQUEST['city']!="null" && $_REQUEST['city']!=UNDEFINED && $_REQUEST['city']!=undefined && $_REQUEST['city']!="UNDEFINED" && $_REQUEST['city']!="undefined")

// {
//   $Where .= " city ='".implode(" , ", $_REQUEST['city'])."' AND ";
//   $city=$_REQUEST['city'];
// }

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL && $_REQUEST['type']!="null")
{
  //$ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
  $Where .= "  sales_executive_id IN (".$_REQUEST['type'].") AND ";
}

if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="" && $_REQUEST['customer_type']!=NULL && $_REQUEST['customer_type']!=null && $_REQUEST['customer_type']!="NULL" && $_REQUEST['customer_type']!="null" && $_REQUEST['customer_type']!=UNDEFINED && $_REQUEST['customer_type']!=undefined && $_REQUEST['customer_type']!="UNDEFINED" && $_REQUEST['customer_type']!="undefined")
{
   // $Where .= " AND type_of_executive = '".$_REQUEST['customer_type']."' ";
    $Where .= " type_of_executive ='".implode(" , ", $_REQUEST['customer_type'])."' AND ";
   $customer_type=$_REQUEST['customer_type'];
}

// $Where .= " isDelete=0";
$Where .= " isDelete=0 AND status= -2"; 
$ctable1_r = $db->rp_getData("no_order_inquiry","*",$Where,"id DESC",0);

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
$column11 = 'L';


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Source Of Inquiry");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Inquiry No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Firm Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Phone");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "State");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "City");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Route");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Description");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Inquiry Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount,  "Inquiry Taken By");
//end of adding column names  

$rowCount = 2; 
$count =0; 

while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");
  // print_r($row);
  $count++;
  $column = 'A';
  for($j=0; $j<12;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $inquiry_type_array[$row['source_of_inquiry']];
    } 
    else if($j==2)
    {
      $value= "#INQ/"   .$row['id'];
    }
    else if($j==3)
    {
      $value=$row['company_name'];
    }
    else if($j==4)
    {
      $value=$row['person_name'];
    }
    else if($j==5)
    {
      $value=$row['mobile_number'];
    }
    else if($j==6)
    {
      $value=$row['state'];
    }
    else if($j==7)
    {
      $value=$row['main_city'];
    }
    else if($j==8)
    {
      $value=$row['city'];
    }
    else if($j==9)
    {
      $value=$row['description'];
    }
    else if($j==10)
    {
      $value= date('d-m-Y',strtotime($row['datetime']));
    }
    else if($j==11)
    {
       				
		$sales_executive=$db->rp_getValue("sales_executive","name","id='".$row['sales_executive_id']."'");
						
		$value = stripslashes($sales_executive); 
    }
    
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  }  
  $rowCount++;
}
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