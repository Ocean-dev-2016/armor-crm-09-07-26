<?php
$page_id=639;$page_slug='page_top_category';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$file_name  = "TopCategoryMaster_Report_".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $Where .= " (
              name like '%".$_REQUEST['searchName']."%'         
            ) AND ";
}

$Where .= " isDelete=0";


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

   if($rights['personal_flag']==1)
   {

    $Where .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";

   }
   else
   {


    if($rights['chain_vise_flag'] == 1)
    {
        

        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

          $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
          if ($get_sales_type== "sales_manager") 
          {
              $sales_executive_type = "Regional Sales Manager";
              $key="sm_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }

          else if ($get_sales_type == "area_sales_manager") 
          {
              $sales_executive_type = "National Sales Manager";//Business Development Manager
              $key="asm_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }

          else if ($get_sales_type == "sales_officer") 
          {
              $sales_executive_type = "Area Sales Manager";//Area Sales Manager
              $key="so_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }
          else if ($get_sales_type == "sales_executive") 
          {
              $sales_executive_type = "Sales Officer";
              $key="se_id";
              $WhereCondition.=' ' .$key.'='.$check_id;
          }
          else
          {
            $WhereCondition.=' type = "service_engineer"';
          }

          $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

          $SALEID1=array();
        if($data)
        {
          while($data_d=mysqli_fetch_assoc($data))
          {
            $SALEID1[]=$data_d['id'];
          }
        }
        if(!empty($SALEID1))
        {
          $SALEID1=implode(",", $SALEID1);
          
            $Where .= " AND created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";  
          
          
        }
        else
        {
            $Where .= " AND  created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";    
        }
    }
    else
    {
      $Where .= " ";
    }
  }
  
}
else
{

  $Where .= " ";

}




$ctable1_r = $db->rp_getData("top_category_master","id,name",$Where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Category";
$log_description = $module_name." Export Excel By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$ctable = "top_category_master";
$db->insertLog($ctable,$last_id,"excel","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
/*for log*/

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of mysqli fields  
$column   = 'A';
$column1  = 'B';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, " Name");

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $count++;
  $column = 'A';
  for($j=0; $j<2;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = $row['name'];
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
//echo INQUIRY_REPORT_FILES.$file_name;;exit();
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>$file_path1);
require_once 'disconnect.php'; 
echo json_encode($arr);
?>