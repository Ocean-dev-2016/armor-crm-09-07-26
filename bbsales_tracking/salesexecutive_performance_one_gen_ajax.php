  <?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance".date('d_m_Y')."_".strtotime("now").".xlsx";
$sales_id = $_REQUEST['sales_id'];
$mode = $_REQUEST['mode'];

if($mode=="quotation")
{
    $ctable1_r = $db->rp_getData("quotation_detail","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
}
else if($mode=="order" || $mode=="pending_order")
{
    if($mode=="order")
    {
        $ctable1_r = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0
        ","id DESC",0);
    }
    else
    {
        $ctable1_r = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND status=0","id DESC",0);
    }
}
else
{
    $ctable1_r = $db->rp_getData("invoice_new","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0
        ","id DESC",0);
}

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 


$rowCount = 1; 
$column   = 'A';


  if($_REQUEST['sales_id']!="")
   {
      // echo "string";exit();
      // $source_id= $_REQUEST['source_id'];
  $SalesExecutiveType = $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'");  
      // echo "$source";exit();
        // echo "Source Medium=".$source.",";
   }
   if($_REQUEST['sales_id']!="")
   {
    // echo $_REQUEST['df'];
      $SalesExecutiveName= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'");
        // echo "Inquiry Date = ".$date.",";
   }

   
            if($SalesExecutiveName!="")
            {
              $SalesExecutiveName_r=" ".$SalesExecutiveName." , ";
            }
            $date=  date("d-m-Y h:i a");
            if($date!="")
            {
                // echo $df;exit();

              $date_r=" Date & Time = ".$date."  ";
            }
            // if($SalesExecutiveCity!="")
            // {
            //   $SalesExecutiveCity_r=" Sales Officer City  =".$SalesExecutiveCity." , ";
            // }
            
             
            

$downloadby = $SalesExecutiveType_r."  ".$SalesExecutiveName_r."  ".$date_r."  ".$SalesExecutiveCity_r."  ".$inquiryassignto_r."  ".$customer_type_r."  ".$industry_type_r."  ".$country_r." ".$state_r." ".$city_r."  ".$Query_desc;




$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Download  By : ".$downloadby."");




$rowCount = 2;  

//start of printing column names as names of mysqli fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
$column6  = 'G';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
if($mode=="quotation")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Inquiry No.");
}
else if($mode=="order"|| $mode=="pending_order")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Order No");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Invoice No");
}

if($mode=="quotation")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Quotation No");
}
else if($mode=="order" || $mode=="pending_order")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Quotation No");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Dispatch No");
}

if($mode=="quotation")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Quotation Date");
}
else if($mode=="order" || $mode=="pending_order")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Order Date");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Invoice Date");
}

if($mode=="quotation")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Quotation Amount");
}
else if($mode=="order" || $mode=="pending_order")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Order Amount");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Invoice Amount");
}


$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Status");

//end of adding column names  

$rowCount = 3; 
$count =0;

if($mode=="quotation")
{
    $status_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","-2"=>"Disapproved");
}
else if($mode=="order" || $mode=="pending_order")
{
    $status_array = array("0"=>"Pending","1"=>"Approved","2"=>"Ready For Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Ready For Partially Dispatched");
}
else
{
    $status_array = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
}

while($row = mysqli_fetch_assoc($ctable1_r))  
{ 
    $count++;
    $column = 'A';
    for($j=0; $j<6;$j++)  
    {
        if($j==0)
        {
            $value = $count;
        }
        else if($j==1)
        {
            if($mode=="quotation")
            {
                $value = "#INQ/".$row['inquiry_id'];
            }
            else if($mode=="order" || $mode=="pending_order")
            {
                $value = $row['order_no'];
            }
            else
            {
                $value = $row['invoice_no'];
            }
        }
        else if($j==2)
        {
            if($mode=="quotation")
            {
                $value = $row['quotation_no'];
            }
            else if($mode=="order" || $mode=="pending_order")
            {
                $value = $db->rp_getValue("quotation_detail","quotation_no","id='".$row['quotation_id']."'",0);
            }
            else
            {
                $value = $db->rp_getValue("dispatch_detail","dispatch_no","id='".$row['dispatch_ids']."'",0);
            }
        }
        else if($j==3)
        {
            if($mode=="quotation")
            {
                if($row['quotation_date']!="1970-01-01" && $row['quotation_date']!="0000-00-00" )
                { 
                    $value = date('d-m-Y', strtotime($row['quotation_date']));
                }
                else
                {
                    $value = ""; 
                }
            }
            else if($mode=="order" || $mode=="pending_order")
            {
                if($row['order_date']!="1970-01-01" && $row['order_date']!="0000-00-00" )
                { 
                    $value = date('d-m-Y',strtotime($row['order_date']));
                }
                else
                {
                    $value = ""; 
                }      
            }
            else
            {
                if($row['invoice_date']!="1970-01-01" && $row['invoice_date']!="0000-00-00" )
                { 
                    $value = date('d-m-Y',strtotime($row['invoice_date']));
                }
                else
                {
                    $value = ""; 
                }  
            }
        }
        else if($j==4)
        {
            if($mode=="quotation")
            {
                $value = stripslashes($db->rp_num(round($row['grand_total'])));
            }
            else if($mode=="order" || $mode=="pending_order")
            {
                $value = $row['grand_total'];
            }
            else
            {
                $value = $row['grand_total'];
            }
        }
        else if($j==5)
        {
          $value = $status_array[$row['status']];
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
require_once("disconnect.php");
echo json_encode($arr);
?>