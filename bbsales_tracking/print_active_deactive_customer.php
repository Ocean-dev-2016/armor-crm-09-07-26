<?php
$page_id = 555;
$page_slug = 'page_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "executive";
$ctable1     = "Executive";
$ctable_where = "";
$where = "";

// Get the total number of rows in the tabl
$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
// Get the total number of rows in the tabl
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $ctable_where .= " (
                            cname like '%" . $db->clean($_REQUEST['searchName']) . "%'
                            OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
                            OR phone  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%'
                        ) AND ";
}

$ctable_where .= " isDelete=0 ";


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    } //incase of invalid page number
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

$date_select = "";

/*if(isset($_REQUEST['customer_status']) && $_REQUEST['customer_status']!="" && $_REQUEST['customer_status']!=NULL && $_REQUEST['customer_status']!=null && $_REQUEST['customer_status']!="NULL" && $_REQUEST['customer_status']!="null" && $_REQUEST['customer_status']!=UNDEFINED && $_REQUEST['customer_status']!=undefined && $_REQUEST['customer_status']!="UNDEFINED" && $_REQUEST['customer_status']!="undefined")
{
    $ctable_where .= " AND isActive IN (".$_REQUEST['customer_status'].")";
}*/

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id'] != null && $_REQUEST['customer_id'] != "NULL" && $_REQUEST['customer_id'] != "null" && $_REQUEST['customer_id'] != UNDEFINED && $_REQUEST['customer_id'] != undefined && $_REQUEST['customer_id'] != "UNDEFINED" && $_REQUEST['customer_id'] != "undefined") {
    $ctable_where .= " AND id IN(" . $_REQUEST['customer_id'] . ")";
}
if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL && $_REQUEST['state'] != null && $_REQUEST['state'] != "NULL" && $_REQUEST['state'] != "null" && $_REQUEST['state'] != UNDEFINED && $_REQUEST['state'] != undefined && $_REQUEST['state'] != "UNDEFINED" && $_REQUEST['state'] != "undefined") {


    $stringA = $_REQUEST['state'];
    $stringAArray = explode(",", $stringA);
    $stringiO = "(";
    foreach ($stringAArray as $key => $value) {
        $stringiO .= "'" . $value . "',";
    }
    $stringiO = rtrim($stringiO, ",");
    $stringiO .= ")";
    $ctable_where .= " AND state IN " . $stringiO . " ";
    $state = $_REQUEST['state'];
}


if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL && $_REQUEST['city'] != null && $_REQUEST['city'] != "NULL" && $_REQUEST['city'] != "null" && $_REQUEST['city'] != UNDEFINED && $_REQUEST['city'] != undefined && $_REQUEST['city'] != "UNDEFINED" && $_REQUEST['city'] != "undefined") {
    $stringD = $_REQUEST['city'];
    $stringDArray = explode(",", $stringD);
    $stringio = "(";
    foreach ($stringDArray as $key => $value) {
        $stringio .= "'" . $value . "',";
    }
    $stringio = rtrim($stringio, ",");
    $stringio .= ")";
    $ctable_where .= " AND main_city IN " . $stringio . " ";
    $city = $_REQUEST['city'];
}
if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL && $_REQUEST['route'] != null && $_REQUEST['route'] != "NULL" && $_REQUEST['route'] != "null" && $_REQUEST['route'] != UNDEFINED && $_REQUEST['route'] != undefined && $_REQUEST['route'] != "UNDEFINED" && $_REQUEST['route'] != "undefined") {
    $stringD = $_REQUEST['route'];
    $stringDArray = explode(",", $stringD);
    $stringio = "(";
    foreach ($stringDArray as $key => $value) {
        $stringio .= "'" . $value . "',";
    }
    $stringio = rtrim($stringio, ",");
    $stringio .= ")";
    $ctable_where .= " AND city IN " . $stringio . " ";
    $route = $_REQUEST['route'];
}
// $get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); 
//hold total records in variable

/*if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND type_of_executive = '".$_REQUEST['status']."' ";
}*/

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL && $_REQUEST['customer_type'] != null && $_REQUEST['customer_type'] != "NULL" && $_REQUEST['customer_type'] != "null" && $_REQUEST['customer_type'] != UNDEFINED && $_REQUEST['customer_type'] != undefined && $_REQUEST['customer_type'] != "UNDEFINED" && $_REQUEST['customer_type'] != "undefined") {
    $ctable_where .= " AND type_of_executive IN ( " . $_REQUEST['customer_type'] . ") ";
    $customer_type = $_REQUEST['customer_type'];
}

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL && $_REQUEST['type_of_company'] != null && $_REQUEST['type_of_company'] != "NULL" && $_REQUEST['type_of_company'] != "null" && $_REQUEST['type_of_company'] != UNDEFINED && $_REQUEST['type_of_company'] != undefined && $_REQUEST['type_of_company'] != "UNDEFINED" && $_REQUEST['type_of_company'] != "undefined") {
    $ctable_where .= " AND type_of_company IN ( " . $_REQUEST['type_of_company'] . ") ";
    $type_of_company = $_REQUEST['type_of_company'];
}
if (isset($_REQUEST['days']) && $_REQUEST['days'] != "" && $_REQUEST['days'] != NULL) {
    $date1 = date('Y-m-d');
    $prev_days_date = date('Y-m-d', strtotime($date1 . ' - ' . $_REQUEST['days'] . ' days'));
    $order_Where = " AND order_date>='" . $prev_days_date . "'";
}

$order = $db->rp_getData("orders", "customer_id", "isDelete=0" . $order_Where . " GROUP BY customer_id", "", 0);
$customer_ids = array();
while ($order_d = mysqli_fetch_array($order)) {
    $customer_ids[] = $order_d['customer_id'];
}
$custome_id = implode(",", $customer_ids);
// echo ($custome_id);exit();

if (isset($_REQUEST['customer_status']) && $_REQUEST['customer_status'] != "" && $_REQUEST['customer_status'] != NULL && $_REQUEST['customer_status'] != null && $_REQUEST['customer_status'] != "NULL" && $_REQUEST['customer_status'] != "null" && $_REQUEST['customer_status'] != UNDEFINED && $_REQUEST['customer_status'] != undefined && $_REQUEST['customer_status'] != "UNDEFINED" && $_REQUEST['customer_status'] != "undefined") {
    if ($_REQUEST['customer_status'] == 0) {

        $ctable_where .= " AND id NOT IN (" . $custome_id . ")";
    } else {
        $ctable_where .= " AND id  IN (" . $custome_id . ")";
    }
}


$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows / $item_per_page);

//get starting position to fetch the records
$page_position = (($page_number - 1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where . $where, "id DESC limit $page_position, $item_per_page", 0); ?>
<style>
    .mainDiv,
    table {
        height: auto;
        width: 100%;
        font-family: Calibri, sans-serif;
        font-style: normal;
        font-weight: 400;
        padding: 0;
        text-decoration: none;
        font-size: 10pt;
        margin: auto;
        padding: auto;
    }

    tr {
        height: 30px;
    }

    table,
    td,
    th {
        border-collapse: collapse;
        border: 1px solid #000;
    }

    td,
    th {
        padding: 5px;
    }

    th {
        border: 1px solid #595959;
        background: #f0e6cc;
    }

    .text-right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .space {
        padding: 10px;
    }

    .no-border {
        border-bottom: 1px solid #fff;
    }

    h2 {
        text-transform: uppercase;
        margin-bottom: 0px;
    }
</style>
<table id="datatable_super" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="11" class="center">
                <h2>Active/Deactive Customer Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
                <?php
                if ($date_select != "") {
                ?>
                    <h2 style="margin: 0"> Report From <?= $date_select; ?></h2>
                <?php
                }
                ?>

            </th>
        </tr>

        <tr>
            <th>No.</th>
            <th>Customer Type</th>
            <th>Company Name</th>
            <th>Person Name</th>
            <th>Client Code</th>
            <th>Phone</th>
            <th>Mobile</th>
            <!-- <th>WhatsApp</th>	 -->
            <th>State</th>
            <th>City</th>
            <th>Route</th>
            <th>Last Order Detail</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($ctable_r) > 0) {
            $count = 0;
            while ($ctable_d = mysqli_fetch_array($ctable_r)) {
                if ($ctable_d['type_of_executive'] == "1") {
                    $type = "Super Stockist";
                } else if ($ctable_d['type_of_executive'] == "2") {
                    $type = "Distributor";
                } else if ($ctable_d['type_of_executive'] == "3") {
                    $type = "Dealer";
                } else if ($ctable_d['type_of_executive'] == "4") {
                    $type = "B2B Customer";
                } else {
                    $type = "Dealer";
                }

                $customer_flag_text = "";
                if ($ctable_d['customer_flag'] == 1) {
                    $customer_flag_text = " - P";
                } else if ($ctable_d['customer_flag'] == 0) {
                    $customer_flag_text = " - C";
                }
        ?>
                <tr>
                    <td><?php echo ++$count; ?></td>
                    <td><?php echo stripslashes($type); ?></td>
                    <td>
                        <span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
                            <?php echo stripslashes($ctable_d['company_name']) . $customer_flag_text; ?>
                        </span>
                    </td>
                    <td><?php echo stripslashes($ctable_d['cname']); ?></td>
                    <td><?php echo stripslashes($ctable_d['client_code']); ?></td>
                    <td><?php echo stripslashes($ctable_d['phone']); ?></td>
                    <td><?php echo stripslashes($ctable_d['mobile_no1']); ?></td>
                    <!-- <td><?php echo stripslashes($ctable_d['whatsapp_no']); ?></td> -->
                    <td><?php echo $ctable_d['state']; ?></td>
                    <td><?php echo $ctable_d['main_city']; ?></td>
                    <td><?php echo $ctable_d['city']; ?></td>
                    <td>
                        <?php
                        $order_r = $db->rp_getData("orders", "order_no,order_date,grand_total", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0", "id DESC", "0", 1);
                        if ($order_r) {
                            $order_d = mysqli_fetch_assoc($order_r);
                            $order_dt = date("d-m-Y", strtotime($order_d['order_date']));
                            echo "#" . $order_d['order_no'] . "<br/>" . $order_dt . "<br/><b>" . $db->rp_num($order_d['grand_total']) . "</b>";
                        } else {
                            echo "No Order Found";
                        }
                        ?>
                    </td>


                </tr>
            <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="10">
                    <p style="text-align:center;">No data available in table</p>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<?php require_once("disconnect.php"); ?>