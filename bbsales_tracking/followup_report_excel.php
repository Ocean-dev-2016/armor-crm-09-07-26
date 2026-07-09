<?php
$page_id=583;$page_slug='future_followup_manage';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
// $file_name  = "Followup Report"."_".date("d-m-Y").".xlsx";
$file_name  = FOLLOWUP_EXPORT_EXCEL;

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
    $ctable_where.="(";
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","mobile_no1 LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
    if($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }
        $phone_no_id = implode(",", $phone_id);
        $ctable_where.="visitor_id IN (".$phone_no_id.") "; 
    }
    $exe_ids_r1 = $db->rp_getData("no_order_inquiry","*","mobile_number LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
    if ($exe_ids_r1)
    {
      if($phone_id)
      {
          $ctable_where.=" OR ";
      } 
      while($exe_id_d1 = mysqli_fetch_assoc($exe_ids_r1))
      {
        $inqArr[] = $exe_id_d1['id'];
      }

      $inqids = implode(",", $inqArr);
      $ctable_where.=" reference_id IN (".$inqids.")"; 
    }
    $ctable_where.=") AND ";
    $isFillter = true;
}

if($_REQUEST['df']!="" && $_REQUEST['df']!=undefined)
{
    if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
    {
        //echo $_REQUEST['df'];exit;
        $date_filter_query = urldecode( $_REQUEST['df'] );
        $date_filter_query_ex=explode(" to ",$date_filter_query);
        $ctable_where .= " ( DATE(followup_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(followup_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}
else if($_REQUEST['df1']!="" && $_REQUEST['df1']!=undefined)
{
    if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
    {
        //echo $_REQUEST['df'];exit;
        $date_filter_query_1 = urldecode( $_REQUEST['df1'] );
        $date_filter_query_1_ex=explode(" to ",$date_filter_query_1);
        $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_1_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_1_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}else if($_REQUEST['df2']!="" && $_REQUEST['df2']!=undefined)
{
    if(isset($_REQUEST['df2']) && $_REQUEST['df2']!="" && $_REQUEST['df2']!=NULL && $_REQUEST['df2']!=undefined)
    {
        //echo $_REQUEST['df'];exit;
        $date_filter_query_2 = urldecode( $_REQUEST['df2'] );
        $date_filter_query_2_ex=explode(" to ",$date_filter_query_2);
        $ctable_where .= " ( DATE(response_date)>='".date("Y-m-d",strtotime($date_filter_query_2_ex['0']))."' AND DATE(response_date)<='".date("Y-m-d",strtotime($date_filter_query_2_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}
else
{ 
    if($_REQUEST['followup_type'] == "today")
    {
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) = '".$today."' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "future")
    {
        $future = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) > '".$future."' AND response='' AND";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "pending")
    {
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) < '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "today,pending")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) <= '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "today,future")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) >= '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "future,pending")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " (DATE(followup_date) < '".$today."' OR DATE(followup_date) > '".$today."') AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "all")
    {
     $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "responsed") {
        $ctable_where .= " response!='' AND ";
        $isFillter = true;
    }
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate']!="01-01-1970") {
    $ctable_where .= "  DATE(followup_date) >= '" . $_REQUEST['todate'] . "' AND ";
    $isFillter = true;
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate']!="01-01-1970") {
    $ctable_where .= "  DATE(followup_date) <= '" .$_REQUEST['fromdate']. "' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['reference_media_id']) && $_REQUEST['reference_media_id']!="")
{
    $ctable_where .=" refrence_media_id='".$_REQUEST['reference_media_id']."' AND";
    $isFillter = true;
}

if(isset($_REQUEST['executive']) && $_REQUEST['executive']!="" && $_REQUEST['executive']!=NULL)
{
    $ctable_where .= " visitor_id= '".$_REQUEST['executive']."' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['through']) && $_REQUEST['through']!="" && $_REQUEST['through']!=NULL)
{
    $ctable_where .= "through= '".$_REQUEST['through']."' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=NULL && $_REQUEST['sales_executive']!=undefined)
{
    $ctable_where .= " user_id= '".$_REQUEST['sales_executive']."' AND ";
    $sales_executive=$_REQUEST['sales_executive'];
    $isFillter = true;
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $loginid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
    if($rights['personal_flag']==1)
    {
        $ctable_where .= " (inquiry_assign_to='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR inquiry_created_by='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR user_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR created_by='".$loginid."') AND ";
        $sales_executive=$_REQUEST['sales_executive'];
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
                // echo "d0";
                $SALEID1=implode(",", $SALEID1);
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."') AND ";                   
            }
            else
            {
                // echo "der0";
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."') AND "; 
            }
        }
        // else
        // {

        // }
    }
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= "user_id='".$check_id."' AND ";
// }

//service_executive user not show condition start --//
$CID=array();
$SEID=array();
$sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
while($sales_type_d = mysqli_fetch_array($sales_type_r))
{
    $SEID[] = $sales_type_d['id'];
}
$SEID=implode(",",$SEID);
if($SEID)
{ 
    $ctable_where .=" user_id NOT IN ('".$SEID."') AND ";
}
//service_executive user not show condition end --//

$ctable_where .= " isDelete=0";

$ctable1_r = $db->rp_getData("followup","id,reference_table,visitor_id,reference_id,user_id,created_date,followup_date,description,through,response_date,response,entry_type,response_entry_flag,response_update_flag",$ctable_where,"id DESC",0);

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
$column12  = 'M';
// $column13  = 'N';
// $column14  = 'O';


  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Customer Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Mobile No");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Created Date and Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Followup Date and Time");
  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Description");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Through");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Type of Followup");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount, "Entry Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount, "Response Date");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount, "Response");
  // $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Response Entry Type");
  // $objPHPExcel->getActiveSheet()->setCellValue($column13.$rowCount, "Response Entry Update Type");
  $objPHPExcel->getActiveSheet()->setCellValue($column12.$rowCount, "Inq No / Bill No");
  
//end of adding column names  

$rowCount = 2; 
$count =0; 
$entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
$response_entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
while($row = mysqli_fetch_assoc($ctable1_r))  
{  
  $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
  $count++;
  $column = 'A';
  for($j=0; $j<13;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      if($row['reference_table']=="sales_executive")
      {
          $followup_flag="followup";
          $value = $db->rp_getValue("executive","company_name","id='".$row['visitor_id']."'").$db->rp_getValue("executive","cname","id='".$row['visitor_id']."'");
      }
      else if($row['reference_table']=="quotation_detail")
      {
          $followup_flag="quotation_followup";
          $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$row['reference_id']."'");
          $value = $db->rp_getValue("executive","company_name","id='".$customer_id."'").$db->rp_getValue("executive","cname","id='".$customer_id."'");
      }
      else if($row['reference_table']=="manual_invoice_import")
      {
          $followup_flag="manual_invoice_import";
          $customer_id=$db->rp_getValue("manually_invoice_outstanding_import","customer_id","id='".$row['reference_id']."'");
          $value = $db->rp_getValue("executive","company_name","id='".$customer_id."'").$db->rp_getValue("executive","cname","id='".$customer_id."'");
      }
      else if($row['reference_table']=="no_order_inquiry")
      {
          $followup_flag="inquiry_followup";
          $value =  $db->rp_getValue("no_order_inquiry","company_name","id='".$row['reference_id']."'").$db->rp_getValue("no_order_inquiry","person_name","id='".$row['reference_id']."'");
      }
      else if($row['reference_table']=="customer_inquiry")
      {
          $followup_flag="leads_followup";
          $value = $db->rp_getValue("customer_inquiry","company_name","id='".$row['reference_id']."'").$db->rp_getValue("no_order_inquiry","person_name","id='".$row['reference_id']."'");
      }
      else if($row['reference_table']=="executive")
      {
        $value = $db->rp_getValue("executive","company_name","id='".$row['reference_id']."'").$db->rp_getValue("executive","cname","id='".$row['reference_id']."'"); 
      }
    }
    else if($j==2)
    {
      if($row['reference_table']=="sales_executive")
      {
          $value =  $db->rp_getValue("executive","mobile_no1","id='".$row['visitor_id']."'");
      }
      else if($row['reference_table']=="no_order_inquiry")
      {
          $value = $db->rp_getValue("no_order_inquiry","mobile_number","id='".$row['reference_id']."'");
      }
      else if($row['reference_table']=="quotation_detail")
      {
          $followup_flag="quotation_followup";
          $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$row['reference_id']."'");
          $value =  $db->rp_getValue("executive","mobile_no1","id='".$customer_id."'");
      }
      else if($row['reference_table']=="manual_invoice_import")
      { 
          $customer_id=$db->rp_getValue("manually_invoice_outstanding_import","customer_id","id='".$row['reference_id']."'");
          $value = $db->rp_getValue("executive","mobile_no1","id='".$customer_id."'");
      }
      else if($row['reference_table']=="customer_inquiry")
      {
          $value =  $db->rp_getValue("customer_inquiry","mobile_number","id='".$row['reference_id']."'");
      }
      else if($row['reference_table']=="executive")
      {
        $value =  $db->rp_getValue("executive","mobile_no1","id='".$row['reference_id']."'");  
      }
    }
    else if($j==3)
    {
      $value = $db->rp_getValue("sales_executive","name","id='".$row['user_id']."'");
    }
    else if($j==4)
    {
      $value = date('d-m-Y h:i A',strtotime($row['created_date']));
    }
    else if($j==5)
    {
      $value = date('d-m-Y h:i A',strtotime($row['followup_date']));
    }
    else if($j==6)
    {
      $value = $row['description'];
    } 
    else if($j==7)
    {
      $value = $msg[$row['through']];
    }

    else if($j==8)
    {
      if($row['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$row['reference_id']."' AND inquiry_lead_flag = '0'",0))
      {
          $slagf = "Inquiry";
      }

      else if ($row['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$row['reference_id']."' AND inquiry_lead_flag = '-1'",0)) {
          $slagf= "Prospects";
      }

      else if ( $row['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$row['reference_id']."' AND inquiry_lead_flag = '1'",0)) {
          $slagf= "Leads";
      }

      else if ($row['reference_table'] == "sales_executive") {
          $slagf= "Sales Officer";
      }
      else if ($row['reference_table'] == "customer_inquiry") {
          
          $slagf= "Customer Inquiry";
      }
      else if ($row['reference_table'] == "quotation_followup") {
          
          $slagf= "Quotation";
      }
      else if ($row['reference_table'] == "executive") {
          
          $slagf= "Executive";
      }
      else if ($ctable_d['reference_table'] == "manual_invoice_import") {
          $slagf= "Manually Invoice Import";
      }
      else if ($row['reference_table'] == "customer_inquiry") {
          
          $slagf= "Executive";
      }
      else if ($row['reference_table'] == "quotation_detail") {
          
          $slagf= "Quotation";
      }
      
      $value = $slagf;
    }

    else if($j==9)
    {
      $value = $entry_type_status[$row['entry_type']]; 
    }
    else if($j==10)
    {
      if($row['response_date']=="0000-00-00 00:00:00")
      {
        $value = " ";
      }
      else
      {
        $value = date('d-m-Y',strtotime($row['response_date']));
      }
    }
    else if($j==11)
    {
      $value = $row['response'];
    }
    //  else if($j==12)
    // {
    //   $value = $entry_type_status[$row['response_entry_flag']]; 
    // }
    //  else if($j==13)
    // {
    //   $value = $entry_type_status[$row['response_update_flag']]; 
    // }
    else if($j==12)
    {
      
      if($row['reference_table']=="no_order_inquiry")
      {
        $value = "INQ/".$row['reference_id']; 
      }
      else if($row['reference_table']=="manual_invoice_import")
      {
        $value = $db->rp_getValue("manually_invoice_outstanding_import","bill_no","id='".$row['reference_id']."' AND isDelete=0");
      }
      else
      {
        $value = "";
      }
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