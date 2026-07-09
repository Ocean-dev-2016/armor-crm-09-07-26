<?php
$page_id=605;$page_slug='customer_inquiry';
include("connect.php");

$dbdetail = explode(",", $db->getDBName());
$hostname = $dbdetail[0];
$database = $dbdetail[3];
$username = $dbdetail[1];
$password = $dbdetail[2];

$tableName = "customer_inquiry";
// Connect to the database
$mysqlii = new mysqlii($hostname, $username, $password, $database);
/* check connection */
if (mysqlii_connect_errno()) {
    printf("Connect failed: %s\n", mysqlii_connect_error());
    exit();
}



// OLD CODE
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    
    $ctable_where .="(company_name like '%".$_REQUEST['searchName']."%' OR mobile_number like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR person_name like '%".$_REQUEST['searchName']."%' OR country like '%".$_REQUEST['searchName']."%' OR state like '%".$_REQUEST['searchName']."%' OR city like '%".$_REQUEST['searchName']."%' OR email_address like '%".$_REQUEST['searchName']."%') AND ";
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
    $ctable_where .= " status='".$_REQUEST['status_id']."' AND ";
    $status_id=$_REQUEST['status_id'];
}

$ctable_where .= " isDelete=0";

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
    $ctable_where .= " AND executive_type = '".$_REQUEST['c_type']."' ";
    $c_type=$_REQUEST['c_type'];
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
    $ctable_where .= " AND city = '".$_REQUEST['city']."'";
    $city=$_REQUEST['city'];
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
    $ctable_where .= " AND sales_executive_id = '".$_REQUEST['type']."' ";
    $type=$_REQUEST['type'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(date_of_call)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(date_of_call)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to']!="" && $_REQUEST['assigned_to']!=NULL)
{
    $ctable_where .= " AND inquiry_assign_to = '".$_REQUEST['assigned_to']."' ";
    $assigned_to=$_REQUEST['assigned_to'];
}
    


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
}
// OLD CODE OVER
$flagofSort = 0;

if (isset($_POST['update'])) {
    $status_array_update = array("Generate"=>"0","In Followup"=>"1","Interested"=>"2","Not Interested"=>"-1","Working"=>"3");
    $_POST[$_POST["field"]] = $status_array_update[$_POST[$_POST["field"]]];
    // UPDATE COMMAND
    $query  = "UPDATE ".$tableName." SET `".$_POST["field"]."`=? WHERE `id`=? AND `isDelete`=0 ";
    $result = $mysqlii->prepare($query);
    
    $result->bind_param('si',$_POST[$_POST["field"]],$_POST["id"]);

    $res = $result->execute() or trigger_error($result->error, E_USER_ERROR);    
    echo $res;
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

    $query          = "SELECT SQL_CALC_FOUND_ROWS id,status,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,person_name,mobile_number,email_address,country,state,city,date_of_call,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to FROM ".$tableName."  WHERE  ".$ctable_where." ORDER BY id DESC LIMIT ?, ?";


    // SELECT COMMAND
    $result = $mysqlii->prepare($query);
    $result->bind_param('ii', $start, $pagesize);
    
    if (isset($_POST['sortdatafield']) && $_POST['sortdatafield']=="count")
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
                $query = "SELECT SQL_CALC_FOUND_ROWS id,status,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,person_name,mobile_number,email_address,country,state,city,date_of_call,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to FROM ".$tableName." WHERE  ".$ctable_where." ORDER BY" . " " . $sortfield . " DESC LIMIT ?, ?";
            } else if ($sortorder == "asc") {
                $query = "SELECT SQL_CALC_FOUND_ROWS id,status,source_of_inquiry,(SELECT name FROM customer_type WHERE `customer_type`.`id`=`".$tableName."`.`executive_type`) AS executive_type,company_name,person_name,mobile_number,email_address,country,state,city,date_of_call,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`sales_executive_id` ) AS sales_executive_id_a,sales_executive_id,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `".$tableName."`.`inquiry_assign_to` ) AS inquiry_assign_to FROM ".$tableName." WHERE  ".$ctable_where." ORDER BY" . " " . $sortfield . " ASC LIMIT ?, ?";
            }
            $result = $mysqlii->prepare($query);
            $result->bind_param('ii', $start, $pagesize);
        }
    }
    $result->execute();
    $result->bind_result($id,$status,$source_of_inquiry,$executive_type,$company_name,$person_name,$mobile_number,$email_address,$country,$state,$city,$date_of_call,$sales_executive_id_a,$sales_executive_id,$inquiry_assign_to);
    $employees = null;
    // fetch values
    $count = $start;
    $inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");
    $status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working");
    while ($result->fetch()) {
        $count++;
        $followup_count = $db->rp_getTotalRecord("followup","reference_id='".$id."' AND isDelete=0",0);
        $employees[] = array(
            'count' => $count,
            "action" => $id,
            "action1" => $id,
            "action2" => $id.",".$sales_executive_id,
            "action3" => $id,
            "id" => $id,
            "status" => $status_array[$status],
            "source_of_inquiry" => $inquiry_type_array[$source_of_inquiry],
            "executive_type" => $executive_type,
            "company_name" => $company_name,
            "person_name" => $person_name,
            "mobile_number" => $mobile_number,
            "email_address" => $email_address,
            "country" => $country,
            "state" => $state,
            "city" => $city,
            "date_of_call" =>($date_of_call!="0000-00-00" && $date_of_call!="1970-01-01")?date("d-m-Y",strtotime($date_of_call)):"",
            
            "sales_executive_id" => $sales_executive_id_a,
            "inquiry_assign_to" => $inquiry_assign_to,
            "followup_count" => $followup_count,
        );
    }
    $result = $mysqlii->prepare("SELECT FOUND_ROWS()");
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

    $data[] = array(
        'TotalRows' => $total_rows,
        'Rows' => (($employees!=null)?$employees:array())
    );
    echo json_encode($data);
}
// /* close statement */
$result->close();
// /* close connection */
$mysqlii->close();
?>
<?php require_once "disconnect.php"; ?>