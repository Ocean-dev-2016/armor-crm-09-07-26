<?php
$page_id   = 588;
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Order Info Report"."_".date("d-m-Y").".xlsx";
$file_name  = INVOICE_EXPORT_EXCEL;
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

//for admin login
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	/*if($_REQUEST['order_type'])
	{
	$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else
	{*/
	$Where .= " isDelete=0    ";	
	//}
	
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
		$Where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'  AND status!=-1";
	}
	else{
		$Where .= " isDelete=0 AND customer_type!='normal_user'  AND status!=-1";
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
 $Where .= " AND  status = '".$_REQUEST['status']."' ";
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
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
    $date_filter_query    = urldecode($_REQUEST['df']);
    $date_filter_query_ex = explode(" to ", $date_filter_query);
    $Where .= " AND ( DATE(invoice_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(invoice_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $Where .= " AND  customer_type = '".$_REQUEST['type']."' ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
 $Where .= " AND  sales_id = '".$_REQUEST['sales_id']."' ";
}

if (isset($_REQUEST['df2']) && $_REQUEST['df2'] != "" && $_REQUEST['df2'] != NULL && $_REQUEST['df2'] != undefined) {
    $date_filter_query_date    = urldecode($_REQUEST['df2']);
    $date_filter_query_ex_date = explode(" to ", $date_filter_query_date);
    $Where .= " AND ( DATE(adate)>='" . date("Y-m-d", strtotime($date_filter_query_ex_date['0'])) . "' AND DATE(adate)<='" . date("Y-m-d", strtotime($date_filter_query_ex_date['1'])) . "'  ) ";
}

// if(isset($_REQUEST['qid']) && $_REQUEST['qid']!="")
// {
//   $Where .= " AND   quotation_id = '".$db->clean($_REQUEST['qid'])."' ";
// }




// $Where .= " AND isDelete=0";
$ctable1_r = $db->rp_getData("invoice_new","*",$Where,"id DESC",0);

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

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, " No");

  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Dispatch No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Invoice No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Invoice Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Company Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Phone");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Shipping Address");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Billing Address");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Country");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "State");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "City");
  $objPHPExcel->getActiveSheet()->setCellValue($column13.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount, "Customer Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column15.$rowCount, "Transport Charge");
  $objPHPExcel->getActiveSheet()->setCellValue($column16.$rowCount, "Packing & Forwarding Charge");
  $objPHPExcel->getActiveSheet()->setCellValue($column17.$rowCount, "Subtotal");
  $objPHPExcel->getActiveSheet()->setCellValue($column18.$rowCount, "Gst Amount");
  $objPHPExcel->getActiveSheet()->setCellValue($column19.$rowCount, "Invoice Amount");
  $objPHPExcel->getActiveSheet()->setCellValue($column20.$rowCount, "GST No");
  $objPHPExcel->getActiveSheet()->setCellValue($column21.$rowCount, "Quotation No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column22.$rowCount, "Order No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column23.$rowCount, "Invoice Created Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column24.$rowCount, "LR No");
  
  
//end of adding column names  

$rowCount = 2; 
$count =0;

$customer_type = array('1' => "Super Stockist",'2' => "Distributor",'3' => "Dealer",'3' => "B2B Customer" ,'normal_user' => "Normal Customer");

$status_array = array('0' =>"Pending",'1' =>"Approved",'2' =>"Dispatched",'3' =>"Cancelled");

while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $order_id = $db->rp_getValue("dispatch_detail","order_id","id='".$row['dispatch_ids']."'",0);
  $quotation_id = $db->rp_getValue("orders","quotation_id","id='".$order_id."'");
  $count++;
  $column = 'A';
  for($j=0; $j<25;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $db->rp_getValue("dispatch_detail","dispatch_no","id='".$row['dispatch_ids']."'");
    }
    else if($j==2)
    {
      $value = $row['invoice_no'];
    }
    else if($j==3)
    {
     
       if($row['invoice_date']!=""){ $value = date('d-m-Y',strtotime($row['invoice_date'])); }else{ $value = "";}
         
    }
    else if($j==4)
    {
      $value = $status_array[$row['status']];
    }
    else if($j==5)
    {
      $value = $row['company_name'];
    }
    else if($j==6)
    {
      $value = $row['customer_name'];
    }
    else if($j==7)
    {
      $value = $row['contact_number'];
    }
    else if($j==8)
    {
      $value = $row['shipping_address'];
    }
    else if($j==9)
    {
      $value = $row['billing_address'];
    }
    else if($j==10)
    {
      $value = $row['country'];
    }
    else if($j==11)
    {
      $value = $row['state'];
    }
    else if($j==12)
    {
      $value = $row['city'];
    }
    else if($j==13)
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
    else if($j==14)
    {
        if($row['customer_type']=='1')
          {
            $slug="Super Stockist";
          }
          else if($row['customer_type']=='2')
          {
            $slug="Distributor";
          }
          else if($row['customer_type']=='3')
          {
            $slug="Dealer";
          }
          else if($row['customer_type']=='4')
          {
            $slug="B2B Customer";
          }
          else if($row['customer_type']=='normal_user')
          {
            $slug="Normal Customer";
          }
          $value = stripslashes($slug);
    }
    else if($j==15)
    {
      $value = $row['transport_charge'];
    }
    else if($j==16)
    {
      $value = $row['packing_charge'];
    }
    else if($j==17)
    {
      $value = $db->rp_num(round($row['subtotal']));
    }
    else if($j==18)
    {
      $value = ($row['subtotal']+$row['transport_charge']+$row['packing_charge'])*(0.18);
      $gst_amount = ($row['subtotal']+$row['transport_charge']+$row['packing_charge'])*(0.18);
    }
    else if($j==19)
    {
      $value = $row['subtotal']+$row['transport_charge']+$row['packing_charge']+$gst_amount;
    }
    else if($j==20)
    {
      $gst_no = $db->rp_getValue("executive","gst","id='".$row['customer_id']."'");
      if($gst_no=="")
      {
        $value =  "--";
      }
      else
      {
        $value = $gst_no; 
      }
    }

    else if($j==21)
    {
      $value = $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'");
    }
    else if($j==22)
    {
      $value = $db->rp_getValue("orders","order_no","id='".$order_id."'",0);
    }
    else if($j==23)
    {
       if($row['adate']!=""){ $value = date('d-m-Y',strtotime($row['adate'])); }else{ $value = "";}
    }
    else if($j==24)
    {
      $value = $db->rp_getValue("lr_detail","lr_number","invoice_id='".$row['id']."'");
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