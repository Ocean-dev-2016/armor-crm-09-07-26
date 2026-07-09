<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Product_Stock_Sample_Report"."_".strtotime("now").".xlsx";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{ 
  $query=$_REQUEST['searchName'];
  $ctable_where.= "name LIKE '%".$query."' AND ";
}
 
if(isset($_REQUEST["item_name"]) && $_REQUEST["item_name"]!="" && $_REQUEST["item_name"]!=undefined){
  $ctable_where .= " name='".$_REQUEST["item_name"]."' AND";
} 
$ctable_where .= " isDelete=0";
$ctable1_r = $db->rp_getData("product_weight_price","*",$ctable_where,"id DESC",0);  
 
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

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "product name");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "product code");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "stock qty");
  //$column++;
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_array($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<3;$j++)  
  {
    if($j==0)
    {
      //$value = $count;
      //$get_weight_r= $db->rp_getValue("product_weight_price","weight_id","isDelete=0 AND product_id='".$row['id']."'",0);
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
      // $value = $row['stock_qty'];
      $value = $db->get_available_stock($row['product_id'],$row['weight_id']);
    }
    /*else if($j==3)
    {
      // $value = date('d-m-Y',strtotime($row[$j]));
      
      //$value = $db->rp_getValue("product_weight_price","inner_size","isDelete=0 AND product_id='".$row['id']."'",0);
    }
    else if($j==4)
    {
      //$value = $db->rp_getValue("product_weight_price","outer_size","isDelete=0 AND product_id='".$row['id']."'",0);
      // $value = $row['stock_qty'];
    }
    else if($j==5)
    {
      $value = $db->rp_getValue("product_weight_price","stock_qty","isDelete=0 AND product_id='".$row['id']."'",0);
      // $value = $row['stock_qty'];
    }
     else if($j==6)
    {
      $value ="₹ ".$db->rp_getValue("product_weight_price","price","isDelete=0 AND product_id='".$row['id']."'",0);
      // $value = $row['stock_qty'];
    }*/
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
 require_once "disconnect.php";
echo json_encode($arr);
//$objWriter->save('php://output'); 
?>