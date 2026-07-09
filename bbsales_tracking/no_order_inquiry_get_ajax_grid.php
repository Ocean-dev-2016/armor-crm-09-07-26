<?php
if($_REQUEST['inquiry_type']=="-1")
{
    $page_id=621;$page_slug='prospect_inquiry';
}
else if($_REQUEST['inquiry_type']=="0")
{
    $page_id        = 572;
    $page_slug      = 'no_order_inquiry';
}
else
{
    $page_id=620;$page_slug='lead_page';
}
include("connect.php");

$dbdetail = explode(",", $db->getDBName());
$hostname = $dbdetail[0];
$database = $dbdetail[3];
$username = $dbdetail[1];
$password = $dbdetail[2];

$tableName = "no_order_inquiry";
$mysqli = new mysqli($hostname, $username, $password, $database);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
// OLD CODE

$ctable_where = "";
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    
    $ctable_where .="(company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%' OR country like '%".$_REQUEST['searchName']."%' OR state like '%".$_REQUEST['searchName']."%' OR city like '%".$_REQUEST['searchName']."%' OR email_address like '%".$_REQUEST['searchName']."%') AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
    $ctable_where .= " status='".$_REQUEST['status_id']."' AND ";
    $status_id=$_REQUEST['status_id'];
}



if(isset($_REQUEST['industry_type']) && $_REQUEST['industry_type']!="")
{
    $ctable_where .= " industry_type_id='".$_REQUEST['industry_type']."' AND ";
    $industry_type=$_REQUEST['industry_type'];
}

if(isset($_REQUEST['status1']) && $_REQUEST['status1']!="")
{
    $ctable_where .= "status IN (".$_REQUEST['status1'].") AND "; 
}

if(isset($_REQUEST['end_followup']) && $_REQUEST['end_followup']!="")
{
    $ctable_where .= " followup_reason_id='".$_REQUEST['end_followup']."' AND ";
    // $status_id=$_REQUEST['status_id'];
}

//$ctable_where .= " isDelete=0 AND isActive=1";


if($_REQUEST['inquiry_type']=="-1")
{
    //$ctable_where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '-1' OR inquiry_lead_flag='0')";
    //$ctable_where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '-1' OR inq_status=2)";
    $ctable_where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '-1'";
}
else if($_REQUEST['inquiry_type']=="0")
{
    //$ctable_where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '0' OR inquiry_lead_flag = '1')";
    $ctable_where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '0'";
}
else
{
    $ctable_where .= " isDelete=0 AND isActive=1 AND inquiry_lead_flag = '1'";
}



$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['se_id']) && $_REQUEST['se_id']!="" && $_REQUEST['se_id']!=NULL)
{
    $ctable_where .= " AND sales_executive_id = '".$_REQUEST['se_id']."' ";
}

if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']!="" && $_REQUEST['c_type']!=NULL)
{
    $ctable_where .= " AND executive_type= '".$_REQUEST['c_type']."' ";
    $c_type=$_REQUEST['c_type'];
}

if(isset($_REQUEST['company_type']) && $_REQUEST['company_type']!="" && $_REQUEST['company_type']!=NULL)
{
    // echo "hell0";die;
    $ctable_where .= " AND type_of_company = '".$_REQUEST['company_type']."' ";
    $company_type=$_REQUEST['company_type'];
}

if(isset($_REQUEST['country']) && $_REQUEST['country']!="" && $_REQUEST['country']!=NULL)
{
    $ctable_where .= " AND country = '".$_REQUEST['country']."' ";
    $country=$_REQUEST['country'];
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
    $ctable_where .= " AND state = '".$_REQUEST['state']."' ";
    $state=$_REQUEST['state'];
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
    $ctable_where .= " AND main_city = '".$_REQUEST['city']."'";
    $city=$_REQUEST['city'];
}
if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL)
{
    $ctable_where .= " AND city = '".$_REQUEST['route']."'";
    $route=$_REQUEST['route'];
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
    $ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
    $type=$_REQUEST['type'];
}

if(isset($_REQUEST['inquiry_month']) && $_REQUEST['inquiry_month']!="" && $_REQUEST['inquiry_month']!=NULL)
{
    $ctable_where .= " AND MONTH(inquiry_date) = '".$_REQUEST['inquiry_month']."' ";
    // $type=$_REQUEST['type'];
}
if(isset($_REQUEST['inquiry_year']) && $_REQUEST['inquiry_year']!="" && $_REQUEST['inquiry_year']!=NULL)
{
    //$ctable_where .= " AND year(inquiry_date) = '".$_REQUEST['inquiry_year']."' ";
    $ctable_where .= " AND year(created_date) = '".$_REQUEST['inquiry_year']."' ";
    // $type=$_REQUEST['type'];
}

if(isset($_REQUEST['lead_month']) && $_REQUEST['lead_month']!="" && $_REQUEST['lead_month']!=NULL)
{
    $ctable_where .= " AND MONTH(inquiry_date) = '".$_REQUEST['lead_month']."' ";
    // $type=$_REQUEST['type'];
}
if(isset($_REQUEST['lead_year']) && $_REQUEST['lead_year']!="" && $_REQUEST['lead_year']!=NULL)
{
    $ctable_where .= " AND year(inquiry_date) = '".$_REQUEST['lead_year']."' ";
    // $type=$_REQUEST['type'];
}
if(isset($_REQUEST['todate']) && $_REQUEST['todate']!="" && $_REQUEST['todate']!=NULL)
{
    $ctable_where .= "AND  inquiry_date>='".$_REQUEST['todate']."' ";
    // $type=$_REQUEST['type'];
}
if(isset($_REQUEST['fromdate']) && $_REQUEST['fromdate']!="" && $_REQUEST['fromdate']!=NULL)
{
    $ctable_where .= "AND  inquiry_date<='".$_REQUEST['fromdate']."' ";
    // $type=$_REQUEST['type'];
}

if(isset($_REQUEST['lead_todate']) && $_REQUEST['lead_todate']!="" && $_REQUEST['lead_todate']!=NULL)
{
    $ctable_where .= "AND  inquiry_date>='".$_REQUEST['lead_todate']."' ";
    // $type=$_REQUEST['type'];
}

if(isset($_REQUEST['lead_fromdate']) && $_REQUEST['lead_fromdate']!="" && $_REQUEST['lead_fromdate']!=NULL)
{
    $ctable_where .= "AND  inquiry_date<='".$_REQUEST['lead_fromdate']."' ";
    // $type=$_REQUEST['type'];
}
if(isset($_REQUEST['lead_year']) && $_REQUEST['lead_year']!="" && $_REQUEST['lead_year']!=NULL)
{
    $ctable_where .= " AND year(inquiry_date) = '".$_REQUEST['lead_year']."' ";
    // $type=$_REQUEST['type'];
}

// update code sagar //
if(isset($_REQUEST['source_id']) && $_REQUEST['source_id']!="" && $_REQUEST['source_id']!=NULL)
{
    $ctable_where .= " AND source_of_inquiry = '".$_REQUEST['source_id']."' ";
    $source_id=$_REQUEST['source_id'];
}
// update code sagar //

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(inquiry_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(inquiry_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
{
    $ctable_where .= " AND inquiry_assign_to = '".$_REQUEST['assigned_to']."' ";
    $assigned_to=$_REQUEST['assigned_to'];
}
    
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
//     //$ctable_where .= " AND (inquiry_assign_to = '".$check_id."') ";
// }

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    if($rights['personal_flag']==1)
    {
        if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==1)
        { 
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            $ctable_where .= " AND inquiry_assign_to = '".$check_id."' OR dealer_id = '".$check_id."'";

        }
        else
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            //$ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
            $ctable_where .= " AND inquiry_assign_to = '".$check_id."'";
        }      


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
                    
                      
                       $ctable_where .= " AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID']."))"; 
                    
                    
                }
                else
                {
                       $ctable_where .= " AND ( inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";          
                }

        }
        else
        {

        }



    }

}


// OLD CODE OVER
$flagofSort = 0;

if (isset($_POST['update'])) {
    //$status_array_update = array("Generate"=>"0","In Followup"=>"1","Interested"=>"2","Not Interested"=>"-1","Buy Later"=>"3","Non Relavent Inquiry"=>"-2","Hot"=>"4","Cold"=>"5","Warm"=>"6","Wrong Call"=>"7","Will Interested"=>"8","Not Working"=>"9","Not Doing Business"=>"10","Lost"=>11);

    //$status_array_update = array("Generate"=>"0","In Followup"=>"1","Hot"=>"4","Cold"=>"5","Warm"=>"6","Non Relavent"=>-2,"Not Interested"=>-1,"Buy Later"=>3,"Lost"=>11);
   // $status_array_update = array("Generate"=>"0","In Followup"=>"1","Non Relavent"=>-2,"Not Interested"=>-1,"Buy Later"=>3,"Lost"=>11);
    $status_array_update = array("Generate"=>"0","In Followup"=>"1","Positive"=>"2","Buy Later"=>"3","Hot"=>"4","Cold"=>"5","Warm"=>"6","My Work"=>"-1","Cancel"=>"-2","Lost"=>"11");


    $_POST[$_POST["field"]] = $status_array_update[$_POST[$_POST["field"]]];
    // UPDATE COMMAND
    $query  = "UPDATE ".$tableName." SET `".$_POST["field"]."`=? WHERE `id`=? AND `isDelete`=0 ";
    $result = $mysqli->prepare($query);
    
    $result->bind_param('si',$_POST[$_POST["field"]],$_POST["id"]);

    $res = $result->execute() or trigger_error($result->error, E_USER_ERROR);    
    //echo $res;

    /* Status Time Line Logic Added Dinesh */
    if ($res) {
        $db->addStatusTimelineEntry($_POST["id"],$_POST[$_POST["field"]]);
    }
    /* Status Time Line Logic Added Dinesh */
    
    /*log entry*/
    /*$status_array1 = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");*/
    $status_array1 = array("0"=>"Generate","1"=>"In Followup","2"=>"Positive","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"My Work","-2"=>"Cancel","11"=>"Lost");

    $flag = "Web";
    $last_id = $_POST["id"];
    if($_REQUEST['inquiry_type']=="-1")
    {
        $ctable = "no_order_inquiry";
        $module_name = "Raw Data";
        $log_description = $module_name." #INQ/".$last_id." Status Change To ". $status_array1[$_POST[$_POST["field"]]]." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    }
    else if($_REQUEST['inquiry_type']=="0")
    {
        $ctable = "no_order_inquiry";
        $module_name = "Inquiry";
        $log_description = $module_name." #INQ/".$last_id." Status Change To ". $status_array1[$_POST[$_POST["field"]]]." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    }
    else
    {
        $ctable = "no_order_inquiry";
        $module_name = "Lead";
        $log_description = $module_name." #INQ/".$last_id." Status Change To ". $status_array1[$_POST[$_POST["field"]]]." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");   
    }
    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id);
    /*log entry*/
}  
else {

    // get first visible row.
    // $_POST['recordstartindex'] = 0;
    // $_POST['recordendindex'] = 16;
    $firstvisiblerow = $_POST['recordstartindex'];
    // get the last visible row.
    $lastvisiblerow = $_POST['recordendindex'];
    $rowscount = $lastvisiblerow - $firstvisiblerow;
    $pagesize = $rowscount;
    $start = $firstvisiblerow;
// echo $firstvisiblerow;exit;

    // $query = "SELECT SQL_CALC_FOUND_ROWS id,status,call_duration,subject,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,(SELECT company_name FROM executive WHERE `executive`.`id`=`".$tableName."`.`dealer_id`) AS dealer_id,person_name,mobile_number,email_address,country,state,city,main_city,description,industry_type_id,inquiry_date,followup_reason_id,cancel_inq_remark,inquiry_type,inq_status,image_path,entry_flag,update_entry_flag,lost_reason,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to,inquiry_lead_flag,(SELECT name FROM company_master WHERE `company_master`.`id`=`".$tableName."`.`type_of_company`) AS type_of_company FROM ".$tableName."  WHERE  ".$ctable_where." ORDER BY created_date DESC LIMIT ?, ?";

    $query = " SELECT SQL_CALC_FOUND_ROWS id, status, call_duration,subject, source_of_inquiry,
              (
                SELECT name
                FROM customer_type
                WHERE `customer_type`.`id` = `".$tableName."`.`executive_type`
              ) AS executive_type,
              company_name,
              (
                SELECT company_name
                FROM executive
                WHERE `executive`.`id` = `".$tableName."`.`dealer_id`
              ) AS dealer_id,
              person_name,
              mobile_number,
              email_address,
              country,
              state,
              city,
              main_city,
              description,
              industry_type_id,
              inquiry_date,
              followup_reason_id,
              cancel_inq_remark,
              inquiry_type,
              inq_status,
              image_path,
              entry_flag,
              update_entry_flag,
              lost_reason,
              pincode,
              (
                SELECT name
                FROM sales_executive
                WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id`
              ) AS sales_executive_id_a,
              sales_executive_id,
              (
                SELECT name
                FROM sales_executive
                WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to`
              ) AS inquiry_assign_to,
              inquiry_lead_flag,
              (
                SELECT name
                FROM company_master
                WHERE `company_master`.`id` = `".$tableName."`.`type_of_company`
              ) AS type_of_company
            FROM
              ".$tableName."
            WHERE
              ".$ctable_where."
            ORDER BY
              created_date DESC ";

    // echo $query; exit;

    // SELECT COMMAND
    $result = $mysqli->prepare($query);
    $result->bind_param('ii', $start, $pagesize);
    
    if (isset($_POST['sortdatafield']) && ($_POST['sortdatafield']=="count" || $_POST['sortdatafield']=="inquiry_no"))
    {
        $_POST['sortdatafield'] = "id";
        $flagofSort = 1;
        $SortOrderId = $_POST['sortorder'];
    }
    else if (isset($_POST['sortdatafield']) && $_POST['sortdatafield']!="count") {
        $sortfield = $_POST['sortdatafield'];
        $sortorder = $_POST['sortorder'];
        if ($sortorder != '') {
            if ($sortorder == "desc") {
                $query = "SELECT SQL_CALC_FOUND_ROWS id,status,call_duration,subject,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,(SELECT company_name FROM executive WHERE `executive`.`id`=`".$tableName."`.`dealer_id`) AS dealer_id,person_name,mobile_number,email_address,country,state,city,main_city,description,industry_type_id,inquiry_date,followup_reason_id,cancel_inq_remark,inquiry_type,inq_status,image_path,entry_flag,update_entry_flag,lost_reason,pincode,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to,inquiry_lead_flag,(SELECT name FROM company_master WHERE `company_master`.`id`=`".$tableName."`.`type_of_company`) AS type_of_company FROM".$tableName." WHERE  ".$ctable_where." ORDER BY" . " " . $sortfield . " DESC LIMIT ?, ?";
            } else if ($sortorder == "asc") {
                $query = "SELECT SQL_CALC_FOUND_ROWS id,status,call_duration,subject,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,(SELECT company_name FROM executive WHERE `executive`.`id`=`".$tableName."`.`dealer_id`) AS dealer_id,person_name,mobile_number,email_address,country,state,city,main_city,description,industry_type_id,inquiry_date,followup_reason_id,cancel_inq_remark,inquiry_type,inq_status,image_path,entry_flag,update_entry_flag,lost_reason,pincode,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to,inquiry_lead_flag,(SELECT name FROM company_master WHERE `company_master`.`id`=`".$tableName."`.`type_of_company`) AS type_of_company FROM ".$tableName." WHERE  ".$ctable_where." ORDER BY" . " " . $sortfield . " ASC LIMIT ?, ?";
            }
            $result = $mysqli->prepare($query);
            $result->bind_param('ii', $start, $pagesize);
        }
    }
    // echo $query;exit();
    $result->execute();
    $result->bind_result($id,$status,$call_duration,$subject,$source_of_inquiry,$executive_type,$company_name,$dealer_id,$person_name,$mobile_number,$email_address,$country,$state,$city,$main_city,$description,$industry_type_id,$inquiry_date,$followup_reason_id,$cancel_inq_remark,$inquiry_type,$inq_status,$image_path,$entry_flag,$update_entry_flag,$lost_reason,$pincode,$sales_executive_id_a,$sales_executive_id,$inquiry_assign_to,$inquiry_lead_flag,$type_of_company);
    // print_r($result);exit();
    $employees = null;
    // fetch values
    $count = $start;

    $inquiry_type_array_data_r=$db->rp_getData("source_of_inquiry","*","isDelete=0","",0);
    $inquiry_type_array_data = array();
    while($inquiry_type_array_data_d=mysqli_fetch_assoc($inquiry_type_array_data_r))
    {
        $inquiry_type_array_data[$inquiry_type_array_data_d['id']]=$inquiry_type_array_data_d['name'];
    }
    $inquiry_type_array = $inquiry_type_array_data;
    // $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");

  /*  $status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");*/

  $status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Positive","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"My Work","-2"=>"Cancel","11"=>"Lost");
    
    $inquiry_status = array("-1"=>"Prospect","0"=>"Inquiry","1"=>"Lead","2"=>"Convert To Inquiry","3"=>"Convert To Lead");
    $entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");

    //05-08-2021 by milan
    //$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"My Work","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");

    while ($result->fetch()) {

        /*if($source_of_inquiry==4)
        {
            if($call_duration!="" && $subject=="Buyer Call")
            {
               $inq="IND-Buyer Call";
            }
            else
            {
               $inq="IND-Inquiry";
            }
        }
        else
        {
            $inq=$inquiry_type_array[$source_of_inquiry];
        }*/

        //$india_mart_id=$db->rp_getValue("source_of_inquiry")
        if($inquiry_type_array[$source_of_inquiry]=="India Mart")
        {
            if($call_duration!="" && $subject=="Buyer Call")
            {
               $inq="IND-Buyer Call";
            }
            else
            {
               $inq="IND-Inquiry";
            }
        }
        else
        {
            $inq=$inquiry_type_array[$source_of_inquiry];
        }

        $count++;
        $inqyiry_status = $inquiry_status[$inq_status];
        $entry_flag = $entry_type_status[$entry_flag];
        $update_entry_flag = $entry_type_status[$update_entry_flag];
        $followup_count = $db->rp_getTotalRecord("followup","reference_id='".$id."' AND isDelete=0",0);
        $followup_reason_id = $db->rp_getValue("followup_reason","name","id='".$followup_reason_id."'");
        $industry_type_id = $db->rp_getValue("industry_type","name","id='".$industry_type_id."'");
        $inquiry_created_by_id = $db->rp_getValue("no_order_inquiry","inquiry_created_by","id='".$id."'");
        $inquiry_created_by = $db->rp_getValue("sales_executive","name","id='".$inquiry_created_by_id."'");
        $image_path_id1 = $db->rp_getValue("media","url","reference_id='".$id."' AND id='".$image_path."'",0);
        $image_path_id = SITEURL."resource/image/".$image_path_id1;

        $employees[] = array(
            'count' => $count,
            "action" => $id,
            "action2" => $id.",".$sales_executive_id,
            "id" => $id,
            "status" => $status_array[$status],
            "source_of_inquiry" => $inq,
            "executive_type" => $executive_type,
            "inquiry_no" => "#INQ/".$id,
            "company_name" => $company_name,
            "dealer_id" => $dealer_id,
            "person_name" => $person_name,
            "mobile_number" => $mobile_number,
            "email_address" => $email_address,
            "country" => $country,
            "state" => $state,
            "city" => $main_city,
            "route" => $city,
            "description" => $description,
            "industry_type_id" => $industry_type_id,
            "inquiry_date" =>($inquiry_date!="0000-00-00" && $inquiry_date!="1970-01-01")?date("d-m-Y",strtotime($inquiry_date)):"",
            "followup_reason_id" => $followup_reason_id,
            "cancel_inq_remark"  => $cancel_inq_remark,
            "inquiry_type_color" => $inquiry_type,
            "inq_status"         => $inqyiry_status,
            "sales_executive_id" => $inquiry_created_by,
            "inquiry_assign_to"  => $inquiry_assign_to,
            "inquiry_lead_flag"  => $inquiry_lead_flag,
            "followup_count"     => $followup_count,
            //"image_path" => $image_path_id,
            "entry_flag" => $entry_flag,
            "update_entry_flag" => $update_entry_flag,
            "lost_reason" => $lost_reason,
            "type_of_company" => $type_of_company,
            "pincode" => $pincode,
        );
    }
    $result = $mysqli->prepare("SELECT FOUND_ROWS()");
    $result->execute();
    $result->bind_result($total_rows);
    $result->fetch();
    
    if($flagofSort==1)
    {
        if($SortOrderId=="asc")
        {
            $ky = SORT_ASC;
        }
        else if($SortOrderId=="desc")
        {
            $ky = SORT_DESC;
        }
        array_multisort(array_column($employees, "count"), $ky, $employees);
    }
    // echo "<pre>";
    // print_r($employees);
    // exit();
    // print_r($total_rows);
    $datadd[] = array(
        'TotalRows' => $total_rows,
        'Rows' => (($employees!=null)?$employees:array())
    );
    // print_r($datadd);exit();
    echo json_encode($datadd);
}
// /* close statement */
$result->close();
// /* close connection */
$mysqli->close();
require_once "disconnect.php";
?>