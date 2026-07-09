<?php
$page_id   = 612;
$page_slug = 'packing_slip';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Packing Slip Info Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $dispatch_no_d=$db->rp_getValue("dispatch_detail","id"," dispatch_no LIKE '%".$_REQUEST['searchName'] ."%' ",0);
  $_REQUEST['searchName'] = $db->clean($_REQUEST['searchName']);
  $Where .= " ( packing_slip_no like '%" . $_REQUEST['searchName'] . "%' OR dispatch_id = '".$dispatch_no_d."'  ) AND ";
}
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
  if($rights['personal_flag']==1)
  {
      $Where .=" created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' AND ";
      $customer_type=$db->rp_getValue("sales_executive","type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
      $filter_where .=" AND id IN (".$customer_type.") ";
  }
  else
  {
    if($rights['all_data_flag']==1)
    {
        
    }
    else
    {
        // $customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
        // //$CustomerType = implode(",", $customer_type);
        // $Where .=" customer_type IN (".$customer_type.") AND ";
    }   
  }
}
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!=null && $_REQUEST['status']!="NULL" && $_REQUEST['status']!="null" && $_REQUEST['status']!=UNDEFINED && $_REQUEST['status']!=undefined && $_REQUEST['status']!="UNDEFINED" && $_REQUEST['status']!="undefined")
{
  $Where .= " status = '".$_REQUEST['status']."' AND ";
}
if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
{
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df1'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $Where .= " ( DATE(packing_slip_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(packing_slip_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}
///For ToDate & FromDate

$Where .=" isDelete=0 ";


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}

$ctable1_r = $db->rp_getData("packing_slip","*",$Where,"id DESC",0);

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

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");

  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Packing Slip No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Packing Slip Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Dispatch No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Invoice No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Customer Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Total Baggage");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Total Item Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Total Baggage Weight");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Actual Baggage Weight");
  
//end of adding column names  

$rowCount = 2; 
$count =0;
while($row = mysqli_fetch_assoc($ctable1_r))  
{ 
  $packing_slip_status_array = array("0"=>"Pending","1"=>"Invoice Generate"); 
  $count++;
  $column = 'A';
  for($j=0; $j<13;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = stripslashes($row['packing_slip_no']);
    }
    else if($j==2)
    {
      $value = date("d-m-Y",strtotime($row['packing_slip_date']));
    }
    else if($j==3)
    {
      $mainArray = array();
      $dids = explode(",",$row['dispatch_id']);
      foreach ($dids as $key => $value) {
        // $mainArray[$key][] = "<a href='view_dispatch.php?id=".$value."'>";
        $mainArray[$key][] = $db->rp_getValue("dispatch_detail","dispatch_no","id IN (".$value.")",0);

        $invoice_no=$db->rp_getValue("invoice_new","invoice_no","dispatch_ids='".$value."'",0);
        $invoice_id=$db->rp_getValue("invoice_new","id","dispatch_ids='".$value."'",0);

        // $mainArray[$key][] = "</a>";
        $mainArray[$key] = implode("", $mainArray[$key]);
      }
      $value = implode(",<br/>", $mainArray);
    }
    else if($j==4)
    {
      $value =$invoice_no;
    }
    else if($j==5)
    {
      $value = $packing_slip_status_array[$row['status']];
    }
    else if($j==6)
    {
      $value = $db->rp_getValue('executive','company_name','id="'.$row['customer_id'].'" AND isDelete=0 AND isActive=1');  
    }
    else if($j==7)
    {
      $type =  $db->rp_getValue('executive','type_of_executive','id="'.$row['customer_id'].'" AND isDelete=0 AND isActive=1');
        if($type=='1'){ $slug="Super Stockist";}else if($type=='2'){$slug="Distributor";}else if($type=='3'){$slug="Dealer";}else if($type=='4'){$slug="B2B Customer";}else if($type=='6'){$slug="B2C Customer";}else if($type=='normal_user'){$slug="Normal Customer";
        }
      $value = stripslashes($slug);
    }
    else if($j==8)
    {
      // $sales_id=$db->rp_getValue('dispatch_detail','sales_id','id="'.$row['dispatch_id'].'" AND isDelete=0 AND isActive=1');
     $sales_id=$db->rp_getValue('dealer_distributor_network','sales_executive_id','id="'.$row['created_by'].'" ');
      $value = $db->rp_getValue('sales_executive','name','id="'.$sales_id.'" AND isDelete=0 AND isActive=1');
    }
    else if($j==9)
    {

      $value = $db->rp_num($db->rp_getValue('packing_slip_item','MAX(main_carton_type_count)','packing_slip_id="'.$row['id'].'" AND isDelete=0 AND isActive=1'),3);
    }
    else if($j==10)
    {
      
      $value = $db->rp_num($db->rp_getValue('packing_slip_item','SUM(pro_qty)','packing_slip_id="'.$row['id'].'" AND isDelete=0 AND isActive=1'),3);
    }
    else if($j==11)
    {
      $Mdata = $db->rp_getData('packing_slip_item','main_carton_type_weight','packing_slip_id="'.$row['id'].'" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
      $total = 0;
      while ( $MdataD = mysqli_fetch_assoc($Mdata) )
      {
          $total += $MdataD['main_carton_type_weight'];
      }
      $value = $db->rp_num( $total+$db->rp_getValue('packing_slip_item','SUM(pro_weight)','packing_slip_id="'.$row['id'].'" AND isDelete=0 AND isActive=1'),3);
    }
    else if($j==12)
    {
      $Mdata = $db->rp_getData('packing_slip_item','main_carton_whole_actual_weight','packing_slip_id="'.$row['id'].'" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
      $total = 0;
      while ( $MdataD = mysqli_fetch_assoc($Mdata) )
      {
          $total += $MdataD['main_carton_whole_actual_weight'];
      }
      $value = $db->rp_num($total,3);
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
require_once 'disconnect.php';
echo json_encode($arr);
?>