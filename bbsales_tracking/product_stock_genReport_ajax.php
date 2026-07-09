<?php
$page_id=638;$page_slug='product_stock';
include("connect.php");

include('../include/product.class.php');
$product=new Product();


include('PHPExcel/IOFactory.php'); 
$file_name  = "Stock_Report"."_".date("d-m-Y").".xlsx";
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
  $where11="";
  $pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
  $PROIDS1=array();
  if($pro_r1)
  {
    while($pro_d1=mysqli_fetch_assoc($pro_r1))
    {
      $PROIDS1[]=$pro_d1['product_id'];
    }
  }
  if(!empty($PROIDS1))
  {
    $PROIDS1=implode(",", $PROIDS1);
    $where11=" OR id IN (".$PROIDS1.")";
  }
  $ctable_where .= " (LOWER(name) like '%".strtolower(trim($_REQUEST['searchName']))."%' ".$where11.") AND ";
}

if(isset($_REQUEST['item_name']) && $_REQUEST['item_name']!="")
{
    $ctable_where .= " (name like '%".$db->clean($_REQUEST['item_name'])."%') AND ";
}

// if(isset($_REQUEST['df']) && $_REQUEST['df']!="")
// {
//   //echo $_REQUEST['df'];exit;
//   $date_filter_query = urldecode( $_REQUEST['df'] );

//   $date_filter_query_ex=explode(" to ",$date_filter_query);

//   $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
// }

$ctable_where .= " isDelete=0";
// $ctable_where = " isDelete=0";

// SELECT id,cid,name,max_price,sell_price,pro_tax,image_path,descr,cgst,sgst,igst,brand_id FROM product WHERE id='336' AND isDelete=0


$ctable1_r = $db->rp_getData("product","*",$ctable_where,"id DESC",0);  
 
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


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Id");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Item  Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Item Code");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Stock");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Order Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Diffrence");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Min Stock Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Max Stock Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Price");
  
  $column++;
//end of adding column names  

$rowCount = 2; 
$count =0;
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $current_prodcuts=$product->aj_getProductDetail($row['id'],$uid);    
  if(!empty($current_prodcuts))
  {
    foreach($current_prodcuts as $product_detail)
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
          $value = $row['name']." (".$product_detail['title'].")";
        }
        else if($j==2)
        {
          $value = $product_detail['catno'];
        }
        else if($j==3)
        {
          $value = $product_detail['stock_qty'];
        } 
        else if($j==4)
        {
          $value = $product_detail['order_qty'];
        }
         else if($j==5)
        {
          $value = $product_detail['stock_qty'];
        }
         else if($j==6)
        {
          $value = $product_detail['min_stock_qty'];
        } 
        else if($j==7)
        {
          $value = $product_detail['max_stock_qty'];
        } 
        else if($j==8)
        {
          $value = $product_detail['price'];
        } 
        
       
        /*else
        {
          $value = $row[$j];   
        }*/
        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
        $column++;
      }  
echo "string";exit; 
      $rowCount++;
    }
  }
}
// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
require_once 'disconnect.php';
$objWriter->save('php://output'); 
?>