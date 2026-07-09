<?php
// Include the connect.php file
$PageConfig = array(
    "id" => 263,
    "navigation" => false
);
include('connect.php');
error_reporting(1);
$dbdetail = explode(",", $db->getDBName());
$hostname = $dbdetail[0];
$database = $dbdetail[3];
$username = $dbdetail[1];
$password = $dbdetail[2];

$tableName = "activity_log";
// Connect to the database
$mysqlii = new mysqlii($hostname, $username, $password, $database);
/* check connection */
if (mysqlii_connect_errno()) {
    printf("Connect failed: %s\n", mysqlii_connect_error());
    exit();
}

// used for office data rights
// $Where1 = $Application->createWhereSpecific("");
// if($Where1!="")
// {
    // $Where1 = " AND ".$Where1;
// }
$Where1 = " ORDER BY id DESC";
$Where1 = " ";

    // get first visible row.
    $firstvisiblerow = $_POST['recordstartindex'];
    // get the last visible row.
    $lastvisiblerow = $_POST['recordendindex'];
    $rowscount = $lastvisiblerow - $firstvisiblerow;
    $pagesize = $rowscount;
    $start = $firstvisiblerow;


    $query          = "SELECT SQL_CALC_FOUND_ROWS id, user_id, table_name, ip, ref_id, activity_type, before_description, after_description, created_date FROM ".$tableName."  WHERE ".$Where1." `isDelete`= 0 ORDER BY id DESC LIMIT ?, ?";

    //echo $query;exit;
    // SELECT COMMAND
    $result = $mysqlii->prepare($query);
    $result->bind_param('ii', $start, $pagesize);
    
    $filterquery = "";
    // filter data.
    if (isset($_POST['filterscount'])) {
        
        $filterscount = $_POST['filterscount'];
        if ($filterscount > 0) {
            $where             = " WHERE (";
            $tmpdatafield      = "";
            $tmpfilteroperator = "";
            $valuesPrep        = "";
            $value             = array();
            for ($i = 0; $i < $filterscount; $i++) {
                // get the filter's value.
                $filtervalue     = $_POST["filtervalue" . $i];
                // get the filter's condition.
                $filtercondition = $_POST["filtercondition" . $i];
                // get the filter's column.
                $filterdatafield = $_POST["filterdatafield" . $i];
                // get the filter's operator.
                $filteroperator  = $_POST["filteroperator" . $i];
                if ($tmpdatafield == "") {
                    $tmpdatafield = $filterdatafield;
                } else if ($tmpdatafield == $filterdatafield) {
                    $where .= ") AND (";
                } else if ($tmpdatafield != $filterdatafield) {
                    if ($tmpfilteroperator == 0) {
                        $where .= " AND ";
                    } else
                        $where .= " OR ";
                }
                // build the "WHERE" clause depending on the filter's condition, value and datafield.
                switch ($filtercondition) {
                    case "CONTAINS":
                        $condition    = " LIKE ";
                        $value[0][$i] = "%{$filtervalue}%";
                        $values[] =& $value[0][$i];
                        break;
                    
                    case "DOES_NOT_CONTAIN":
                        $condition    = " NOT LIKE ";
                        $value[1][$i] = "%{$filtervalue}%";
                        $values[] =& $value[1][$i];
                        break;
                    
                    case "EQUAL":
                        $condition    = " = ";
                        $value[2][$i] = $filtervalue;
                        $values[] =& $value[2][$i];
                        break;
                    
                    case "NOT_EQUAL":
                        $condition    = " <> ";
                        $value[3][$i] = $filtervalue;
                        $values[] =& $value[3][$i];
                        break;
                    
                    case "GREATER_THAN":
                        $condition    = " > ";
                        $value[4][$i] = $filtervalue;
                        $values[] =& $value[4][$i];
                        break;
                    
                    case "LESS_THAN":
                        $condition    = " < ";
                        $value[5][$i] = $filtervalue;
                        $values[] =& $value[5][$i];
                        break;
                    
                    case "GREATER_THAN_OR_EQUAL":
                        $condition    = " >= ";
                        $value[6][$i] = $filtervalue;
                        $values[] =& $value[6][$i];
                        break;
                    
                    case "LESS_THAN_OR_EQUAL":
                        $condition    = " <= ";
                        $value[7][$i] = $filtervalue;
                        $values[] =& $value[7][$i];
                        break;
                    
                    case "STARTS_WITH":
                        $condition    = " LIKE ";
                        $value[8][$i] = "{$filtervalue}%";
                        $values[] =& $value[8][$i];
                        break;
                    
                    case "ENDS_WITH":
                        $condition    = " LIKE ";
                        $value[9][$i] = "%{$filtervalue}";
                        $values[] =& $value[9][$i];
                        break;
                    
                    case "NULL":
                        $condition     = " IS NULL ";
                        $value[10][$i] = "%{$filtervalue}%";
                        $values[] =& $value[10][$i];
                        break;
                    
                    case "NOT_NULL":
                        $condition     = " IS NOT NULL ";
                        $value[11][$i] = "%{$filtervalue}%";
                        $values[] =& $value[11][$i];
                        break;
                }
                $where .= " " . $filterdatafield . $condition . "? ";
                $valuesPrep = $valuesPrep . "s";
                if ($i == $filterscount - 1) {
                    $where .= ")";
                }

                $tmpfilteroperator = $filteroperator;
                $tmpdatafield      = $filterdatafield;
            }
                $where .= " AND `isDelete`=0";
            $filterquery .= "SELECT SQL_CALC_FOUND_ROWS id, user_id, table_name, ip, ref_id, activity_type, before_description, after_description, created_date FROM ".$tableName." " . $where." ".$Where1." ORDER BY id DESC ";
            // build the query.
            $valuesPrep = $valuesPrep . "ii";
            $values[] =& $start;
            $values[] =& $pagesize;
            $query  = "SELECT SQL_CALC_FOUND_ROWS id, user_id, table_name, ip, ref_id, activity_type, before_description, after_description, created_date FROM ".$tableName." " . $where ." ".$Where1." ORDER BY id DESC ". " LIMIT ?, ?";
            $result = $mysqlii->prepare($query);
            call_user_func_array(array(
                $result,
                "bind_param"
            ), array_merge(array(
                $valuesPrep
            ), $values));
        }
    }
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
            if ($_POST['filterscount'] == 0) {
                if ($sortorder == "desc") {
                    $query = "SELECT SQL_CALC_FOUND_ROWS id, user_id, table_name, ip, ref_id, activity_type, before_description, after_description, created_date FROM ".$tableName." WHERE `isDelete`=0 ".$Where1." ORDER BY" . " " . $sortfield . " DESC LIMIT ?, ?";
                } else if ($sortorder == "asc") {
                    $query = "SELECT SQL_CALC_FOUND_ROWS id, user_id, table_name, ip, ref_id, activity_type, before_description, after_description, created_date FROM ".$tableName." WHERE `isDelete`=0 ".$Where1." ORDER BY" . " " . $sortfield . " ASC LIMIT ?, ?";
                }
                if(isset($_POST['sortdatafield']) && $_POST['sortdatafield']!="")
                {
                    $query = str_replace("ORDER BY id DESC","",$query);
                    $query = str_replace("ORDER BY id ASC","",$query);
                }
                $result = $mysqlii->prepare($query);
                $result->bind_param('ii', $start, $pagesize);
            } else {
                if ($sortorder == "desc") {
                    $filterquery .= " ORDER BY " . $sortfield . " DESC LIMIT ?, ?";
                } else if ($sortorder == "asc") {
                    $filterquery .= " ORDER BY " . $sortfield . " ASC LIMIT ?, ?";
                }
                // build the query.
                $query  = $filterquery;
                if(isset($_POST['sortdatafield']) && $_POST['sortdatafield']!="")
                {
                    $query = str_replace("ORDER BY id DESC","",$query);
                    $query = str_replace("ORDER BY id ASC","",$query);
                }
                $result = $mysqlii->prepare($query);
                call_user_func_array(array(
                    $result,
                    "bind_param"
                ), array_merge(array(
                    $valuesPrep
                ), $values));
            }
        }
    }
    
    // echo $query;exit;
    $result->execute();
    // $res = $result->execute() or trigger_error($result->error, E_USER_ERROR);
    // echo $res;exit;
    $result->bind_result($id, $user_id, $table_name, $ip, $ref_id, $activity_type, $before_description, $after_description, $created_date);
    $employees = null;
    // fetch values
    while ($result->fetch()) {
        
        $created_date = date("d-m-Y h:i:s A",strtotime($created_date));
        // $created_date = ($created_date == "00-00-0000" || $created_date=="30-11--0001" || $created_date == "01-01-1970" || $created_date=="30-11--0001")?"":$created_date;
        
        $user_id = $db->rp_getValue("dealer_distributor_network","name","id='".$user_id."'",0);

        $employees[] = array(
            'id'=>$id,
            'user_id'=>$user_id,
            'table_name'=>$table_name,
            'ip'=>$ip,
            'ref_id'=>$ref_id,
            'activity_type'=>$activity_type,
            'before_description'=>$before_description,
            'after_description'=>$after_description,
            'created_date'=>$created_date,
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

// /* close statement */
$result->close();
// /* close connection */
$mysqlii->close();
?>