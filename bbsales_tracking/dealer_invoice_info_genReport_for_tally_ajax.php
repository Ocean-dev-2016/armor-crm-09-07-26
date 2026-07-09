<?php
$page_id   = 588;
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Order Info Report"."_".date("d-m-Y").".xlsx";
$file_name  = "Invoicetallydata_".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";

// Get the total number of rows in the table
 // print_r($_REQUEST);exit();
if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) )
{
  $Query=$_REQUEST['searchName'];
  // $Where.=" (
  //       customer_name like '%".$db->clean($Query)."%' OR
  //       company_name like '%".$db->clean($Query)."%' OR
  //       order_no like '%".$db->clean($Query)."%'
  //     ) AND ";
  $invoice_new_seachname = $db->rp_getData("invoice_new","*"," isDelete=0 AND (
        customer_name like '%".$db->clean($Query)."%' OR
        company_name like '%".$db->clean($Query)."%' OR
        invoice_no like '%".$db->clean($Query)."%'
      ) ","",0);
  $IDS = array();
  while($invoice_new_seachname_d = mysqli_fetch_assoc($invoice_new_seachname))
  {
    $IDS[] = $invoice_new_seachname_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " invoice_id IN (".$IDS.") AND ";
}




if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="")
{
  // $Where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
  $invoice_new_sales = $db->rp_getData("invoice_new","*"," isDelete=0 AND sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."'","",0);
  $IDS = array();
  while($invoice_new_sales_d = mysqli_fetch_assoc($invoice_new_sales))
  {
    $IDS[] = $invoice_new_sales_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " invoice_id IN (".$IDS.") AND ";
}

if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="")
{
  // $Where .= "  customer_type = '".$db->clean($_REQUEST['customer_type'])."' AND";
  $invoice_new_customer_type = $db->rp_getData("invoice_new","*"," isDelete=0 AND customer_type = '".$db->clean($_REQUEST['customer_type'])."'","",0);
  $IDS = array();
  while($invoice_new_customer_type_d = mysqli_fetch_assoc($invoice_new_customer_type))
  {
    $IDS[] = $invoice_new_customer_type_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " invoice_id IN (".$IDS.") AND ";
}

//for admin login
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
  /*if($_REQUEST['order_type'])
  {
  $Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
  }
  else
  {*/
  $Where .= " isDelete=0 ";  
  //}
  
}
// for customer login
else
{
  if($_REQUEST['order_type'] && $_REQUEST['uid'])
  {
    //$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
    $invoice_new_order_uid = $db->rp_getData("invoice_new","*"," isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'","",0);
    $IDS = array();
    while($invoice_new_order_uid_d = mysqli_fetch_assoc($invoice_new_order_uid))
    {
      $IDS[] = $invoice_new_order_uid_d['id'];
    }
    $IDS = implode(",",$IDS);
    $Where.= " isDelete=0 AND invoice_id IN (".$IDS.") AND ";
  }
  else if($_REQUEST['order_type'])
  {
    //$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'  AND status!=-1";
    $invoice_new_ordertype = $db->rp_getData("invoice_new","*"," isDelete=0 AND  customer_type='".$_REQUEST['order_type']."'  AND status!=-1","",0);
    $IDS = array();
    while($invoice_new_ordertype_d = mysqli_fetch_assoc($invoice_new_ordertype))
    {
      $IDS[] = $invoice_new_ordertype_d['id'];
    }
    $IDS = implode(",",$IDS);
    $Where.= " isDelete=0 AND invoice_id IN (".$IDS.") AND ";
  }
  else
  {
    //$Where .= " isDelete=0 AND customer_type!='normal_user'  AND status!=-1";
    $invoice_new_customertype_status = $db->rp_getData("invoice_new","*"," isDelete=0 AND customer_type!='normal_user'  AND status!=-1","",0);
    $IDS = array();
    while($invoice_new_customertype_status_d = mysqli_fetch_assoc($invoice_new_customertype_status))
    {
      $IDS[] = $invoice_new_customertype_status_d['id'];
    }
    $IDS = implode(",",$IDS);
    $Where.= " isDelete=0 AND invoice_id IN (".$IDS.") AND ";
  }
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}
//status
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 // $Where .= " AND  status = '".$_REQUEST['status']."' ";
 $invoice_new_status = $db->rp_getData("invoice_new","*"," isDelete=0 AND status = '".$_REQUEST['status']."'","",0);
  $IDS = array();
  while($invoice_new_status_d = mysqli_fetch_assoc($invoice_new_status))
  {
    $IDS[] = $invoice_new_status_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " AND invoice_id IN (".$IDS.") ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
{
  // $Where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
  $invoice_new_todate = $db->rp_getData("invoice_new","*"," isDelete=0 AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."'","",0);
  $IDS = array();
  while($invoice_new_todate_d = mysqli_fetch_assoc($invoice_new_todate))
  {
    $IDS[] = $invoice_new_todate_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " AND invoice_id IN (".$IDS.") ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
{
    //$Where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
    $invoice_new_fromdate = $db->rp_getData("invoice_new","*"," isDelete=0 AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."'","",0);
    $IDS = array();
    while($invoice_new_fromdate_d = mysqli_fetch_assoc($invoice_new_fromdate))
    {
      $IDS[] = $invoice_new_fromdate_d['id'];
    }
    $IDS = implode(",",$IDS);
    $Where.= " AND invoice_id IN (".$IDS.") ";
}
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
    $date_filter_query    = urldecode($_REQUEST['df']);
    $date_filter_query_ex = explode(" to ", $date_filter_query);
    //$Where .= " AND ( DATE(invoice_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(invoice_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
    $invoice_new_df = $db->rp_getData("invoice_new","*"," isDelete=0 AND ( DATE(invoice_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(invoice_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  )","",0);
    $IDS = array();
    while($invoice_new_df_d = mysqli_fetch_assoc($invoice_new_df))
    {
      $IDS[] = $invoice_new_df_d['id'];
    }
    $IDS = implode(",",$IDS);
    $Where.= " AND invoice_id IN (".$IDS.") ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 // $Where .= " AND  customer_type = '".$_REQUEST['type']."' ";
  $invoice_new_type = $db->rp_getData("invoice_new","*"," isDelete=0 AND customer_type = '".$_REQUEST['type']."' ","",0);
  $IDS = array();
  while($invoice_new_type_d = mysqli_fetch_assoc($invoice_new_type))
  {
    $IDS[] = $invoice_new_type_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " AND invoice_id IN (".$IDS.") ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
  //$Where .= " AND  sales_id = '".$_REQUEST['sales_id']."' ";
  $invoice_new_sales = $db->rp_getData("invoice_new","*"," isDelete=0 AND sales_id = '".$_REQUEST['sales_id']."' ","",0);
  $IDS = array();
  while($invoice_new_sales_d = mysqli_fetch_assoc($invoice_new_sales))
  {
    $IDS[] = $invoice_new_sales_d['id'];
  }
  $IDS = implode(",",$IDS);
  $Where.= " AND invoice_id IN (".$IDS.") ";
}

// if(isset($_REQUEST['qid']) && $_REQUEST['qid']!="")
// {
//   $Where .= " AND   quotation_id = '".$db->clean($_REQUEST['qid'])."' ";
// }

// $Where .= " AND isDelete=0";
$ctable1_r = $db->rp_getData("invoice_new_product_item","*",$Where,"id ASC",0);

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

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Bill No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Bill Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Reference No");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Voucher Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Customer Code");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "State Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "GST No");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Product Code");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Product Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Quantity");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Unit");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Rate");
  $objPHPExcel->getActiveSheet()->setCellValue($column13.$rowCount, "Disc%");
  $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount, "Amount");
  $objPHPExcel->getActiveSheet()->setCellValue($column15.$rowCount, "CGST%");
  $objPHPExcel->getActiveSheet()->setCellValue($column16.$rowCount, "CGST");
  $objPHPExcel->getActiveSheet()->setCellValue($column17.$rowCount, "SGST%");
  $objPHPExcel->getActiveSheet()->setCellValue($column18.$rowCount, "SGST");
  $objPHPExcel->getActiveSheet()->setCellValue($column19.$rowCount, "IGST%");
  $objPHPExcel->getActiveSheet()->setCellValue($column20.$rowCount, "IGST");
  $objPHPExcel->getActiveSheet()->setCellValue($column21.$rowCount, "Narration");
  
//end of adding column names  

$rowCount = 2; 
$count =0;

$customer_type = array('1' => "Super Stockist",'2' => "Distributor",'3' => "Dealer",'3' => "B2B Customer" ,'normal_user' => "Normal Customer");

$status_array = array('0' =>"Pending",'1' =>"Approved",'2' =>"Dispatched",'3' =>"Cancelled");
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $GST=0;
  $CGST=0;
  $cgst_amount=0;
  $SGST=0;
  $sgst_amount=0;
  $IGST=0;
  $igst_amount=0;
  $invoice_r = $db->rp_getData("invoice_new","*","id='".$row['invoice_id']."'","id DESC",0);
  $invoice_d = mysqli_fetch_assoc($invoice_r);
  $order_id = $db->rp_getValue("dispatch_detail","order_id","id='".$invoice_d['dispatch_ids']."'",0);
  $quotation_id = $db->rp_getValue("orders","quotation_id","id='".$order_id."'");
  $unit_id = $db->rp_getValue("product","unit_id","id='".$row['pro_id']."'");
  $GetDistributor_a = $db->rp_getData("executive","*","id='".$invoice_d['customer_id']."' AND isDelete=0","",0);
  $Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
  $count++;
  $column = 'A';
  if($invoice_d['igst_amount']!=0)
  {
    if($Distri_data_a['type_of_executive']==7)
    {
      $GST=0.1;    
    }
    else
    {
      $GST=18;    
    }    
  }
  else
  {
      $GST=9;    
  }
  $totalprice =$row['pro_qty']*$row['unitprice'];
  $final_price1 = $totalprice-$invoice_d['cash_discount_amount'];
  $final_price2 = $final_price1-$invoice_d['additional_discount_amount'];
  $final_price = $final_price2+$invoice_d['transport_charge']+$invoice_d['packing_charge'];
  $gst_amount=($final_price*$GST)/100;
  if($invoice_d['igst_amount']!="0")
  {
    if($Distri_data_a['type_of_executive']==7)
    {
        if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
        {
          $CGST=0.05;
          $cgst_amount=$db->rp_number_format($gst_amount/2);
          $SGST=0.05;
          $sgst_amount=$db->rp_number_format($gst_amount/2);
          $IGST=0;
          $igst_amount=0;
        }
        else
        {
          $CGST=0;
          $cgst_amount=0;
          $IGST=0.01;
          $igst_amount=$db->rp_number_format($gst_amount/2);
          $SGST=0;
          $sgst_amount=0;
        }
    }
    else
    {
      if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
      {
        $CGST=9;
        $cgst_amount=$db->rp_number_format($gst_amount/2);
        $SGST=9;
        $sgst_amount=$db->rp_number_format($gst_amount/2);
        $IGST=0;
        $igst_amount=0;
      }
      else
      {
        $CGST=0;
        $cgst_amount=0;
        $SGST=0;
        $sgst_amount=0;
        $IGST=18;
        $igst_amount=$db->rp_number_format($gst_amount);
      }
    }
  }
  
  for($j=0; $j<22;$j++)  
  {
    if($j==0)
    {
      $value = $invoice_d['invoice_no'];
    }
    else if($j==1)
    {
      if($invoice_d['invoice_date']!="")
      { 
        $value = date('d-m-Y',strtotime($invoice_d['invoice_date'])); 
      }
      else
      { 
        $value = "";
      }
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("orders","order_no","id='".$order_id."'");
    }
    else if($j==3)
    {
      $value = "GST SALES";
         
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("executive","client_code","id='".$invoice_d['customer_id']."'");
    }
    else if($j==5)
    {
      $value = $invoice_d['customer_name'];
    }
    else if($j==6)
    {
      $value = $invoice_d['state'];
    }
    else if($j==7)
    {
      $value = $db->rp_getValue("executive","gst","id='".$invoice_d['customer_id']."'");
    }
    else if($j==8)
    {
      $value = $db->rp_getValue("product_weight_price","catno","id='".$row['pro_id']."'");
    }
    else if($j==9)
    {
      $value = $db->rp_getValue("product","name","id='".$row['pro_id']."'");
    }
    else if($j==10)
    {
      $value = $row['pro_qty'];
    }
    else if($j==11)
    {
      $value = $db->rp_getValue("unit","name","id='".$unit_id."'");
    }
    else if($j==12)
    {
      $value = $row['original_price'];
    }
    else if($j==13)
    {
      $value = $row['discount'];
    }
    else if($j==14)
    {
      $value = $row['totalprice'];
        
    }
    else if($j==15)
    {
      // $value = $row['cgst_tax'];
      $value = $CGST;
    }
    else if($j==16)
    {
      // $value = $row['cgst_amount'];
      $value = $cgst_amount;
    }
    else if($j==17)
    {
      // $value = $row['sgst_tax'];
      $value = $SGST;
    }
    else if($j==18)
    {
      // $value = $row['sgst_amount'];
      $value = $sgst_amount;
    }
    else if($j==19)
    {
      // $value = $row['igst_tax'];
      $value = $IGST;
    }
    else if($j==20)
    {
      // $value = $row['igst_amount'];
      $value = $igst_amount;
    }
    else if($j==21)
    {
      $value = $invoice_d['remarks'];
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