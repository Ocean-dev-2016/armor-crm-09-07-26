<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Add To Cart Order Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) )
{
  $Query=$_REQUEST['searchName'];
  $Where.=" (
				customer_name like '%".$db->clean($Query)."%' OR
				company_name like '%".$db->clean($Query)."%' OR
				order_no like '%".$db->clean($Query)."%'
			) AND ";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="")
{
	$Where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
}

if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="")
{
	$Where .= "  customer_type = '".$db->clean($_REQUEST['customer_type'])."' AND";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
 $Where .= " sales_id = '".$_REQUEST['sales_id']."' AND";
}

if(isset($_REQUEST['o_type']) && $_REQUEST['o_type']!="" && $_REQUEST['o_type']!=NULL)
{
 $Where .= "customer_type = '".$_REQUEST['o_type']."' AND  ";
}

//for admin login
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
  $Where .= " isDelete=0 AND status=-1";
}
// for customer login
else
{
  if($_REQUEST['order_type'] && $_REQUEST['uid'])
  {
  $Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
  }
  else if($_REQUEST['order_type'])
  {
    $Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND status=-1";
  }
  else{
    $Where .= " isDelete=0 AND status=-1";
  }
}

//status
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $Where .= " AND status = '".$_REQUEST['status']."' ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
{
  $Where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
{
     $Where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
{
	// echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$Where .= " AND ( DATE(order_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(order_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}


$Where .= " AND isDelete=0";
$ctable1_r = $db->rp_getData("orders","id,order_no,order_date,company_name,customer_name,sales_id,customer_type,grand_total,status",$Where,"id DESC",0);

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

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Order No");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Order Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Customer Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Order Amount");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Status");
  
//end of adding column names  

$rowCount = 2; 
$count =0;

$customer_type = array('1' => "Super Stockist",'2' => "Distributor",'3' => "Dealer",'4' => "B2B Customer",'normal_user' => "Normal Customer");

while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<9;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $row['order_no'];
    }
    else if($j==2)
    {
      $value = date('d-m-Y',strtotime($row['order_date']));
    }
    else if($j==3)
    {
      $value = $row['company_name'];
    }
    else if($j==4)
    {
      $value = $row['customer_name'];
    }
    else if($j==5)
    {
    	$sales_name = $db->rp_getValue("sales_executive","name","id='".$row['sales_id']."'");
    	if($sales_name=="")
		{
			$value =  "--";
		}
		else
		{
			$value = $sales_name;	
		}
    }
    else if($j==6)
    {
      $value = $customer_type[$row['customer_type']];
    }
    else if($j==7)
    {
      $value = $db->rp_num(round($row['grand_total']));
    }
    else if($j==8)
    {
      $value = "Added to Cart";
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
$arr = array("file_path"=>trim($file_path1));
require_once "disconnect.php";
echo json_encode($arr);
?>