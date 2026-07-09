<?php

$page_id=646;$page_slug='complain_sub_category';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Sales Officer Report"."_".date("d-m-Y").".xlsx";
$file_name  = "Complain Sub Category Report".date('d_m_Y')."_".strtotime("now").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $ctable_where .= " name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " isDelete='0' AND id!='0'";
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

   if($rights['personal_flag']==1)
   {

    $ctable_where .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";

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
          
            $ctable_where .= " AND created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")"; 
          
          
        }
        else
        {
            $ctable_where .= " AND  created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";   
        }
    }
    else
    {
      $ctable_where .= " ";
    }
  }
  
}
else
{

  $ctable_where .= " ";

}

$ctable1_r = $db->rp_getData("complain_sub_category","*",$ctable_where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Complain Sub Category";
$log_description = $module_name." Export Excel By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$ctable = "complain_sub_category";
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
$column2  = 'C';
// $column3  = 'D';
// $column4  = 'E';
// $column5  = 'F';
// $column6  = 'G';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
// $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Officer Type");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "  Complain/Request Category");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Complain/Request Sub Category");
// $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Phone");
// $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "State");
// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "City");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
    if($row['type']=="sales_manager")
    {
      $sales_executive_type="Regional Sales Manager";
    }

    if($row['type']=="area_sales_manager")
    {
      $sales_executive_type="Business Development Manager";
    }
    
    if($row['type']=="sales_officer")
    {
      $sales_executive_type="Area Sales Manager";
    }
    
    if($row['type']=="sales_executive")
    {
      $sales_executive_type="Sales Officer";
    }

  $count++;
  $column = 'A';
  for($j=0; $j<3;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    // else if($j==1)
    // {
    //   $value = $sales_executive_type;
    // }
    else if($j==1)
    {
      $value = $db->rp_getValue("complain_category","name","id='".$row['complain_category_id']."'",0);
      
    }
    else if($j==2)
    {
      $value = $row['name']; 
    }
    else if($j==4)
    {
      $value = $row['phone'];
    } 
    else if($j==5)
    {
      $value = $row['state'];
    } 
    else if($j==6)
    {
      $value = $row['city'];
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
require_once 'disconnect.php';
echo json_encode($arr);
?>