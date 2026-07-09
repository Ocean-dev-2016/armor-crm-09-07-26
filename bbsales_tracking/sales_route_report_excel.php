<?php
$page_id=631;$page_slug='master_route_planning';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "route Report"."_".date("d-m-Y").".xlsx";
$Where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
     //echo $_REQUEST['searchName'];exit();
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","cname LIKE '%".trim($_REQUEST['searchName'])."%' OR phone LIKE '%".trim($_REQUEST['searchName'])."%' OR company_name LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);

   

    // $customer_ids_r = $db->rp_getData("executive","*","phone LIKE '%".trim($_REQUEST['searchName'])."%' OR cname LIKE '%".trim($_REQUEST['searchName'])."%'  OR company_name LIKE '%".trim($_REQUEST['searchName'])."%'  ","",0);

    if ($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }

        $phone_no_id = implode(",", $phone_id);

        $ctable_where.="customer_id IN (".$phone_no_id.") AND "; 
    }
    // if($order_ids_r)
    // {
    //     while($order_id_d = mysqli_fetch_assoc($order_ids_r))
    //     {
    //       $phone_id[] = $order_id_d['id'];
    //     }

    //     $phone_no_id = implode(",", $phone_id);

    //     $ctable_where.="reference_id IN (".$phone_no_id.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("complain","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     // print_r($phone_no_id_f);exit;

    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    // if($customer_ids_r)
    // {
    //     while($customer_id_d = mysqli_fetch_assoc($customer_ids_r))
    //     {
    //         $cname= $db->rp_getValue("request","customer_id","id='".$customer_id_d['id']."'",0);

    //       $phone_id[] = $cname;
    //     }

    //     $phone_no_id = implode(",", $phone_id);
    //     $phone_no_id_f = rtrim($phone_no_id,(","));
    //     $ctable_where.="reference_id IN (".$phone_no_id_f.") OR ";
    // }
    //$ctable_where.=" description LIKE '%".trim($_REQUEST['searchName'])."%' OR ";
    // $ctable_where.=" 0=1 AND ";
    else 
    {
        $ctable_where.="phone IN ('') AND ";
    }
}

$ctable_where .= " isDelete=0";

// if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
// {
//   $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
//   $sales_id=$_REQUEST['sales_id'];
// }

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="")
{
      //echo $_REQUEST['df'];exit;
      $date_filter_query = urldecode( $_REQUEST['df1'] );

      $date_filter_query_ex=explode(" to ",$date_filter_query);

      $ctable_where .= " AND ( DATE(date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
  if($rights['personal_flag']==1)
  {
      $ctable_where .= " AND sales_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";

  }
  else
  {
    if($rights['all_data_flag']==1)
    {
      if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
      {
        $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
        $sales_id=$_REQUEST['sales_id'];
      }
    }
    else
    {
        $ctable_where .= " AND sales_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";

    } 
  }
}
else
{

  if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
  {
    $ctable_where .= " AND sales_id = '".$_REQUEST['sales_id']."' ";
    $sales_id=$_REQUEST['sales_id'];
  }
}




$ctable1_r = $db->rp_getData("my_route","*",$ctable_where,"id DESC",0);

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

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Sales Person Name");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Company Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Mobile No");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Date");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "State");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "City");
$objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Remark");
  
  
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
while($ctable_d = mysqli_fetch_assoc($ctable1_r))  
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
      $value = $db->rp_getValue("executive","cname","isDelete=0 AND id='".$ctable_d['customer_id']."'");
    }
    else if($j==2)
    {
      $value = $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d['sales_id']."'");
      
    }
    else if($j==3)
    {
    $value = $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$ctable_d['customer_id']."'");
    }
    else if($j==4)
    {
      $value = $db->rp_getValue("executive","phone","isDelete=0 AND id='".$ctable_d['customer_id']."'");
    } 
    else if($j==5)
    {
      $value =  date('d-m-Y',strtotime($ctable_d['date']));
    } 
    else if($j==6)
    {
      $value = $db->rp_getValue("master_route","state","isDelete=0 AND id='".$ctable_d['route_id']."'");
    } 

    else if($j==7)
    {
      $value = $db->rp_getValue("master_route","city","isDelete=0 AND id='".$ctable_d['route_id']."'");
    }
    else if($j == 8)
    {
      $value = $ctable_d['remark'];
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