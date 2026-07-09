<?php
$page_id=640;$page_slug='product_stock_page';
include("connect.php");

include('../include/product.class.php');
$product=new Product();


include('PHPExcel/IOFactory.php'); 
$file_name  = "Product_Report"."_".date("d-m-Y").".xlsx";
$Where = "";
$warehouse_id = urldecode($_REQUEST['warehouse_id']); 
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

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

if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="")
{
    $ctable_where .= " tcid='".$_REQUEST['category_id']."' AND ";
}
if(isset($_REQUEST['sub_category_id']) && $_REQUEST['sub_category_id']!="")
{
    $ctable_where .= " cid='".$_REQUEST['sub_category_id']."' AND ";
}
 
$ctable_where .= " isDelete=0";
 
$ctable1_r = $db->rp_getData("product","*",$ctable_where,"id DESC",0);  
 
// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  
// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
// $column4  = 'E';
/*$column5  = 'F';
$column6  = 'G';
$column7  = 'H';
$column8  = 'I';
$column9  = 'J';
$column10  = 'K';
$column11  = 'L';
$column12  = 'M';*/
 
 /* $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Id");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Category");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Sub Category");*/
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Item Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Item Code");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Daily Production");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Availbale Stock");
  /*$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Order Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Balance");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Min Stock Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Max Stock Qty");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Price");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Amount");*/
  
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
      // $actual_Stock = $db->rp_getValue("product_weight_price","stock_qty","isDelete=0 AND product_id='".$row['id']."' AND weight_id='".$product_detail['weight_id']."'",0); 
      $actual_Stock =$db->get_available_stock($row['id'],$product_detail['weight_id'],$warehouse_id);
 
      $count++;
      $column = 'A';
      for($j=0; $j<4;$j++)  
      {
        /*if($j==0)
        {
          $value = $count;
        }
        else if($j==1)
        {
          $value = $categoty_name;
        }
         else if($j==2)
        {
          $value = $sub_categoty_name;
        }*/ 
        if($j==0)
        {
          $value = $row['name']." (".$product_detail['title'].")";
        }
        else if($j==1)
        {
          $value = $product_detail['catno'];
        }
        else if($j==2)
        {
          $value="";
        }
        else if($j==3)
        {
          $value = $actual_Stock;
        } 
        /*else if($j==7)
        {
          $value = $order_qty;
        }
         else if($j==8)
        {
          $value = $actual_Stock;
        }
         else if($j==9)
        {
          $value = $min_Stock;
        } 
        else if($j==10)
        {
          $value = $max_Stock;
        } 
        else if($j==11)
        {
          $value = $product_detail['price'];
        } 
        else if($j==12)
        {
          $value = $product_detail['price']*$actual_Stock;
        }*/  
        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
        $column++;
      }  
// echo "string";exit; 
      $rowCount++;
    }
  }
}// Redirect output to a client’s web browser (Excel5) 

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("sheet_import/".$file_name); 
$file_path1 = trim(ADMINFOLDER."/sheet_import/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once "disconnect.php";
echo json_encode($arr);
?> 