<?php
$page_id = 581;
$page_slug = 'price_list_master';

include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Pricelist_Sample_Report"."_".strtotime("now").".xlsx";
$ctable_where = "";
 

if(isset($_REQUEST["tcid"]) && $_REQUEST["tcid"]!="" && $_REQUEST["tcid"]!=undefined){ 
  $top_cat_pro_r = $db->rp_getData("product","id","tcid='".$_REQUEST['tcid']."'  AND isDelete=0","",0);
  if($top_cat_pro_r)
  {       
    while($top_cat_pro_d=mysqli_fetch_assoc($top_cat_pro_r))
    {
      $topcatProIds[]=$top_cat_pro_d['id'];
    }
    $topcatProIds=implode(",",$topcatProIds);
    $ctable_where .="product_id IN (".$topcatProIds.") AND ";
  }
  else
  {
    $ctable_where .="product_id IN (0) AND ";
  }
}
if(isset($_REQUEST["cid"]) && $_REQUEST["cid"]!="" && $_REQUEST["cid"]!=undefined){
  $cat_pro_r = $db->rp_getData("product","id","cid='".$_REQUEST['cid']."'  AND isDelete=0","",0);
  if($cat_pro_r)
  {       
    while($cat_pro_d=mysqli_fetch_assoc($cat_pro_r))
    {
      $catProIds[]=$cat_pro_d['id'];
    }
    $catProIds=implode(",",$catProIds);
    $ctable_where .="product_id IN (".$catProIds.") AND ";
  }
  else
  {
    $ctable_where .="product_id IN (0) AND ";
  }
}
 
$ctable_where .= " isDelete=0";
$ctable1_r = $db->rp_getData("product_weight_price","*",$ctable_where,"catno ASC",0);  
 
// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  
// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of mysql fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "product name");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "product code");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Min Sell Price");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Product Price");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Discount Price");
  //$column++;
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<5;$j++)  
  {
    if($j==0)
    {
      $get_product_name= $db->rp_getValue("product","name","isDelete=0 AND id='".$row['product_id']."'",0);
      $get_weight=$db->rp_getValue("weight","name","isDelete=0 AND id='". $row['weight_id']."'",0);
      $value = $get_product_name."  ".$get_weight;
    } 
    else if($j==1)
    {
      $value = $row['catno'];
    }
    else if($j==2)
    {
      $value =  $db->rp_getValue("product_weight_price","minimum_selling_price","isDelete=0 AND product_id='".$row['product_id']."' AND weight_id='".$row['weight_id']."'",0);
    }
    else if($j==3)
    {
      $value =  $db->rp_getValue("product_weight_price","price","isDelete=0 AND product_id='".$row['product_id']."' AND weight_id='".$row['weight_id']."'",0);
    }
    else if($j==4)
    {
      $value = "";
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

$objWriter->save("sheet_import/".$file_name); 
$file_path1 = trim(ADMINFOLDER."/sheet_import/".$file_name);
$arr = array("file_path"=>$file_path1);
echo json_encode($arr);
//$objWriter->save('php://output'); 
require_once 'disconnect.php'; 
?>