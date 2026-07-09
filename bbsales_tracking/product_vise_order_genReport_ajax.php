<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 

$file_name  = "Product_wise_order_report"."_".date("d-m-Y").".xlsx";
// $file_name  = CUSTOMERVISIT_EXPORT_EXCEL;
$ctable_where = "";
$ctable     = "product_weight_price";
$ctable1    = "Orders";
$ctable_where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    // $ctable_where .= " (name LIKE '%".$db->clean(trim(urldecode(($_REQUEST['searchName']))))."%' ) AND ";
    $product_id = $db->rp_getData("product","*","name LIKE '%".$db->clean(trim(urldecode(($_REQUEST['searchName']))))."%' AND isDelete=0","",0);
    if($product_id)
    {       
        // echo "hello"; exit;
        while($K1=mysqli_fetch_assoc($product_id))
        {
            $PRODUCT_IDS[]=$K1['id'];
        }
        $PRODUCT_IDS=implode(",",$PRODUCT_IDS);
        $ctable_where .=" product_id IN ('".$PRODUCT_IDS."') AND";
    }
    else
    {
        $ctable_where .=" product_id IN ('') AND ";
    }
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where1 .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )";
}

if(isset($_REQUEST['top_cat']) && $_REQUEST['top_cat']!="" && $_REQUEST['top_cat'] != NULL){
    $ctable_where2 .= " AND top_cat_id = '".$_REQUEST['top_cat']."' ";
}

if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id']!="" && $_REQUEST['cat_id'] != NULL){
    $ctable_where3 .= " AND cat_id = '".$_REQUEST['cat_id']."' ";
}

$ctable_where .= " isDelete=0 "; 

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
 
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
// $column8  = 'I';

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Product Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Quotation Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Quotation Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Order Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Order Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Invoice Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Invoice Value");
  // $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Address");
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_array($ctable_r))  
{  

  // $quotation_total_qty = $db->rp_getValue("quotation_product_item","SUM(pro_qty)","pro_id='".$row['id']."' AND isDelete = 0 ",0);
  // $quotation_total_price = $db->rp_getValue("quotation_product_item","SUM(totalprice)","pro_id='".$row['id']."' AND isDelete = 0 ",0);

  // $order_total_qty = $db->rp_getValue("order_product_item","SUM(pro_qty)","pro_id='".$row['id']."' AND isDelete = 0 ",0);
  // $order_total_price = $db->rp_getValue("order_product_item","SUM(totalprice)","pro_id='".$row['id']."' AND isDelete = 0 ",0);

  // $invoice_total_qty = $db->rp_getValue("invoice_new_product_item","SUM(pro_qty)","pro_id='".$row['id']."' AND isDelete = 0 ",1);
  // $invoice_total_price = $db->rp_getValue("invoice_new_product_item","SUM(totalprice)","pro_id='".$row['id']."' AND isDelete = 0 ",0);


  $quotation_total_qty = $db->rp_getValue("quotation_product_item","SUM(pro_qty)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);
  $quotation_total_price = $db->rp_getValue("quotation_product_item","SUM(totalprice)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);

  $order_total_qty = $db->rp_getValue("order_product_item","SUM(pro_qty)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);
  $order_total_price = $db->rp_getValue("order_product_item","SUM(totalprice)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);

  $invoice_total_qty = $db->rp_getValue("invoice_new_product_item","SUM(pro_qty)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 AND isDelete = 0 ",0);
  $invoice_total_price = $db->rp_getValue("invoice_new_product_item","SUM(totalprice)","pro_id='".$row['product_id']." AND weight_id='".$row['weight_id']." $ctable_where1 AND isDelete = 0 ",0);

  $count++;
  $column = 'A';
  for($j=0; $j<8;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    } 
    else if($j==1)
    {
      $value = $db->rp_getValue("product","name","id='".$row['product_id']."' AND isDelete = 0 ",0);
    }
    else if($j==2)
    {
      $value = $quotation_total_qty;
    }
    else if($j==3)
    {
      $value = $quotation_total_price;
    }
    else if($j==4)
    {
      $value = $order_total_qty;
    }
    else if($j==5)
    {
      $value = $order_total_price;
    }
     else if($j==6)
    {
      $value = $invoice_total_qty;
    }
     else if($j==7)
    {
      $value = $invoice_total_price;
    }
    else
    {
      $value = $row[$j];   
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
require_once "disconnect.php";
echo json_encode($arr); 

?>