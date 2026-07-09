<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Complain_Report"."_".date("d-m-Y").".xlsx";
$file_name  = COMPLAIN_EXPORT_EXCEL;
$Where = "";
$ctable_where = "";
$_REQUEST['searchName'] = urldecode($_REQUEST['searchName']); 

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

    $ctable_where .= " (complain_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

    $isFillter=true;
}

$ctable_where .= " isDelete=0";

if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!="null")
{
       $ctable_where.=" AND customer_id='".$_REQUEST['customer_id']."'";
}

if(isset($_REQUEST["company_type"]) && $_REQUEST["company_type"]!="" && $_REQUEST["company_type"]!=undefined){
    $ctable_where .= " AND type_of_company='".$_REQUEST["company_type"]."'";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST["sales_executive"]!="null"){
    
    //$ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
    $ctable_where .= " AND user_id IN (".$_REQUEST['sales_executive'].")";
    $isFillter=true;
    
}


if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="" && $_REQUEST['status_id']!="null")
{
    $Where .= " status='".implode(" , ", $_REQUEST['status_id'])."' AND ";
    $isFillter=true;
    
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
    $isFillter=true;
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($rights['personal_flag']==1)
    {
         $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
        $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
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
                    
                        $ctable_where .= "  AND (complain_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR  complain_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") ) ";    
                    
                    
                }
                else
                {
                        $ctable_where .= "  AND (complain_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";     
                }

        }
        else
        {
            
        }
    }
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
}
// $Where .= " isDelete=0";
$ctable1_r = $db->rp_getData("complain","*",$ctable_where,"id DESC",0);

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
// $column12  = 'M';
// $column13  = 'N';
// $column14  = 'O';
// $column15  = 'P';


 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Complain No.");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Complain Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Company Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Customer Name");
  // $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Title");
  // $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Source of complain");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Complain Category");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Complain Sub Category");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Description");
  // $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Latitude");
  // $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Longitude");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Address");
  // $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "State");
  // $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "City");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount,  "Status");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount,  "Compalain Assign To");
  // $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount,  "Entry Type");
  // $objPHPExcel->getActiveSheet()->setCellValue($column14.$rowCount,  "Update Entry Type");
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
$status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
$ENTRY_FLAG = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
  $count++;
  $column = 'A';
  for($j=0; $j<12;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      $value = "#".$row['complain_no'];
    } 
    else if($j==2)
    {
      if($row['complain_date']=="1970-01-01" || $row['complain_date']=="0000-00-00")
        { $value = "";}
      else
      {
           $value = date("d-m-Y",strtotime($row['complain_date']));
      }
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("company_master","name","id = '".$row['type_of_company']."'");
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['user_id']."'");
    }
    else if($j==5)
    {
      $value = $db->rp_getValue("executive","cname","id='".$row['customer_id']."'");

      $value .= "  " .$db->rp_getValue("executive","phone","id='".$row['customer_id']."'");
    }
    // else if($j==5)
    // {
    //   $value = $row['title'];
    // }
    //  else if($j==5)
    // {
    //   $value =$complain_type_array[$row['complain_type']];
    // }
     else if($j==6)
    {
      $value = $db->rp_getValue("complain_category","name","id='".$row['complain_cat_id']."'");
    }
     else if($j==7)
    {
      $value = $db->rp_getValue("complain_sub_category","name","id='".$row['complain_subcat_id']."'");
    }
    else if($j==8)
    {
      $value = $row['remark'];
    }
    // else if($j==10)
    // {
    //   $value = $row['latitude'];
    // }
    // else if($j==11)
    // {
    //   $value = $row['longitude'];
    // }
    else if($j==9)
    {
      $value = $row['app_address'];
    }
    // else if($j==10)
    // {
    //   $value = $row['state'];
    // } 
    // else if($j==11)
    // {
    //   $value = $row['main_city'];
    // }
    // else if($j==11)
    // {
    //   $value = $row['city'];
    // }
    //   else if($j==13)
    // {
    //   $value = $db->rp_getValue("class","name","id='".$row['class_id']."'");
    // }
    //   else if($j==14)
    // {
    //   $value = $db->rp_getValue("area","name","id='".$row['area_id']."'");
    // }
    
    else if($j==10)
    {
      $value = $status_array[$row['status']];
    }
    else if($j==11)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['complain_assign_to']."'",0);
    }
    // else if($j==14)
    // {
    //   $value = $ENTRY_FLAG[$row['entry_flag']];
    // }
    // else if($j==14)
    // {
    //   $value = $ENTRY_FLAG[$row['update_entry_flag']];
    // }
    
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