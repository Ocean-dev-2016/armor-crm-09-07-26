<?php
$page_id=662;$page_slug='target_report';/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "target";
$ctable1    = "target";

$ctable_where = "";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    $sales_id = $db->rp_getData("sales_executive","id","name LIKE '%".$_REQUEST['searchName']."%'  AND isDelete=0","",0);
    if($sales_id){
        $SALESID = array();
        while ($sales_d=mysqli_fetch_assoc($sales_id)) {
            $SALESID[]=$sales_d['id'];
            # code...
        }
        $SALESID = implode(",", $SALESID);
        $ctable_where .=" sales_executive_id IN (".$SALESID.") AND ";
    }
    else
    {
        $ctable_where .=" sales_executive_id IN (0) AND ";
        // $ctable_where .="(sales_executive_id IN (0) OR ";
    }
}


if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!="null")
{
    //$ctable_where .=" sales_executive_id='".$_REQUEST['sales_executive_id']."' AND ";
    $ctable_where .=" sales_executive_id IN (".$_REQUEST['sales_executive_id'].") AND ";
}

if(isset($_REQUEST['filter_month']) && $_REQUEST['filter_month']!="")
{
    $ctable_where .= "  target_month ='".$_REQUEST['filter_month']."' AND";
   

}


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " sales_executive_id='".$check_id."' AND ";
}
else
{
    $ctable_where .= " ";   
}

$ctable_where .= "  isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

/*if($_REQUEST['sales_executive_id']!="null" && $_REQUEST['sales_executive_id']!="")
{*/
    $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
/*}*/


$imgExts = array("gif", "jpg", "jpeg", "png", "tiff", "tif");
?>
<style type="text/css">

</style>
<form action="" name="print_info1" id="print_info1" method="post">
    <table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
          
            <tr>
                <th>No.</th>
                <th>Sales Person Name</th>
                <th>Current Month Target</th>                
                <th>Carried Forward Target</th>                
                <th>Total Target</th>
                <th>Achieved Target</th>
                <th>Pending Target</th>
                <th>Achieved Target in %</th>
                <th>Pending Target in %</th>
               
            </tr>
        </thead>
        <tbody>
        <?php
       
            if(mysqli_num_rows($ctable_r)>0)
            {
            $count = 0;
            $months = array('January'=>'1','February'=>'2','March'=>'3','April'=>'4','May'=>'5','June'=>'6','July'=>' 7','August'=>'8','September'=>'9','October'=>'10','November'=>'11','December'=>'12');
            while($ctable_d = mysqli_fetch_array($ctable_r))
            {

                $request_month = $ctable_d['target_month'];
                $date = DateTime::createFromFormat('F', $request_month);
                $date->modify('-1 month');
                $pr_month = $date->format('F');
                $t_amount = $db->rp_getValue($ctable, "target_amount", "isDelete=0 AND target_month='" . $pr_month . "'", 0);
                $t_quantity = $db->rp_getValue($ctable, "target_quantity", "isDelete=0 AND target_month='" . $pr_month . "'", 0);

                $total_amt12 = 0;
                $order_target22 = 0;
                $month1 = date("m");
                $month = date("F");
                $year = $ctable_d['target_year'];

                $target_ex122 = explode(",", $months[$pr_month]);

                foreach ($target_ex122 as $tmonth) {
                    if (!in_array($tmonth, $month_rr)) {
                        $month_rr[] = $tmonth;
                    }
                }

                $monthids_dd = implode(",", $month_rr);
                $orders_r = $db->rp_getData("orders", "id", "(MONTH(order_date) IN($monthids_dd) OR  0!=0) AND sales_id='" . $ctable_d['sales_executive_id'] . "' AND isDelete=0 AND (status=1)  AND YEAR(order_date) = '" . $year . "'", "", 0);



                while ($orders_d = mysqli_fetch_assoc($orders_r)) {
                    $order_target22 = $db->rp_getValue("order_product_item", "SUM(totalprice)", "order_id='" . $orders_d['id'] . "'", 0);
                    $dispatch_qty = $db->rp_getValue("dispatch_detail", "SUM(dispatch_qty)", "isDelete=0 AND order_id IN (".$orders_d.")", 0);
                    $total_amt12 = $order_target22;
                    $total_qty12 = $dispatch_qty;
                }

                $pending_amount1 = $t_amount - $total_amt12;
                $pending_quantity1 = $t_quantity - $total_qty12;

            ?>
            <tr>
              <td rowspan="2"><?php echo ++$count; ?></td>
              <td rowspan="2"><?php echo $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d['sales_executive_id']."'"); ?></td>
               <td><?= CURR.$ctable_d['target_amount']; ?></td>
               <td>
                    <?php 
                    if ($pending_amount1 > 0) {
                        echo $pending_amount1;
                    } else {
                        echo "0";
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    if ($pending_amount1 > 0) {
                        echo $ctable_d['target_amount'] + $pending_amount1;
                    } else {
                        echo $ctable_d['target_amount'];
                    }
                    ?>

                </td>
                <td>
                    <?php
                        $total_amt = 0;
                        $order_target = 0;
                        $month1 = date("m");
                        $month = date("F");
                        $year = $ctable_d['target_year'];

                        $target_explode = explode(",", $months[$ctable_d['target_month']]);

                        foreach ($target_explode as $tmv) {
                            if (!in_array($tmv, $month_target_arr)) {
                                $month_target_arr[] = $tmv;
                            }
                        }

                        $monthids = implode(",", $month_target_arr);

                        $order_amount_r = $db->rp_getData("orders", "id", "(MONTH(order_date) IN($monthids) OR  0!=0) AND sales_id='" . $ctable_d['sales_executive_id'] . "' AND isDelete=0 AND (status=1)  AND YEAR(order_date) = '" . $year . "'", "", 0);

                        while ($order_amount_d = mysqli_fetch_assoc($order_amount_r)) {
                            $order_target = $db->rp_getValue("order_product_item", "SUM(totalprice)", "order_id='" . $order_amount_d['id'] . "'", 0);
                            $total_amt += $order_target;
                        }

                        $target_amount = $ctable_d['target_amount'];
                        $pending_amount = $target_amount - $total_amt;

                        if ($total_amt != "") {
                            echo $total_amt;
                        } else {
                            echo "0";
                        }
                    ?>
                </td>
              <td style="text-align: right;">
                <?php 
                
                if($pending_amount<0){
                        echo "0";
                }
                else{
                
                 echo $pending_amount;
                }
                
                 ?>
                    
                </td>
               <td style="text-align: right;"><?= $total_amt*100/$ctable_d['target_amount']; ?>%</td>
               <td style="text-align: right;"><?= $pending_amount*100/$ctable_d['target_amount'];?>%</td>
            </tr>
            <tr>
                <td><?= $ctable_d['target_quantity'] ?></td>
                <td>
                    <?php 
                    if ($pending_quantity1 > 0) {
                        echo $pending_quantity1;
                    } else {
                        echo "0";
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    if ($pending_quantity1 > 0) {
                        echo $ctable_d['target_quantity'] + $pending_quantity1;
                    } else {
                        echo $ctable_d['target_quantity'];
                    }
                    ?>
                </td>
                <td>
                    <?php
                        $total_quo = 0;
                        $order_target = 0;
                        $month1 = date("m");
                        $month = date("F");
                        $year = $ctable_d['target_year'];

                        $target_explode = explode(",", $months[$ctable_d['target_month']]);

                        foreach ($target_explode as $tmv) {
                            if (!in_array($tmv, $month_target_arr)) {
                                $month_target_arr[] = $tmv;
                            }
                        }

                        $monthids = implode(",", $month_target_arr);

                        $order_amount_r = $db->rp_getData("orders", "id", "(MONTH(order_date) IN($monthids) OR  0!=0) AND sales_id='" . $ctable_d['sales_executive_id'] . "' AND isDelete=0 AND (status=1)  AND YEAR(order_date) = '" . $year . "'", "", 0);

                        while ($order_amount_d = mysqli_fetch_assoc($order_amount_r)) {
                            $order_target = $db->rp_getValue("order_product_item", "SUM(totalprice)", "order_id='" . $order_amount_d['id'] . "'", 0);
                            $total_amt += $order_target;
                        }

                        $target_amount = $ctable_d['target_amount'];
                        $pending_amount = $target_amount - $total_amt;

                        if ($total_amt != "") {
                            echo $total_amt;
                        } else {
                            echo "0";
                        }
                    ?>
                </td>
                <td style="text-align: right">
                   <?php 
                    if ($pending_quantity1 > 0) {
                        $pending_amt= $ctable_d['target_quantity'] + $pending_quantity1;
                    } else {
                        $pending_amt= $ctable_d['target_quantity'];
                    }
                    $pending= $pending_amt - $total_amt;
                    echo $pending;
                   ?>
                </td>

                <td style="text-align: right">
                    <?php 
                    if ($pending_quantity1 > 0) {
                        $target_quantity= $ctable_d['target_quantity'] + $pending_quantity1;
                    } else {
                        $target_quantity= $ctable_d['target_quantity'];
                    }

                    echo $total_amt*100/$target_quantity; 
                    ?>%
                </td>

                <td style="text-align: right"> <?php 
                if ($pending_quantity1 > 0) {
                        $target_quantity= $ctable_d['target_quantity'] + $pending_quantity1;
                    } else {
                        $target_quantity= $ctable_d['target_quantity'];
                    }

                echo 
                 $pending*100/$target_quantity; ?>%</td>
               
            </tr>
        <?php
            $total_target += $ctable_d['target_amount'];
            $archive_target += $order_amount;
            $pending_target += $pending_amount;

            }
        ?>
            <tr>
                <td></td>
                <td><b>Total</b></td>
                <td style="text-align: right;"><?= $total_target;?></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?= $pending_target; ?></td>
                <td></td>
                <td></td>
            </tr>
        <?php
        }
    
        ?>
        </tbody>
    </table>
</form>
   <div class="row">
        <div class="col-md-6">
            <div class="dataTables_info"> Rows Limit:
                <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                    <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
                                            echo ' selected="selected"';
                                        }  ?>>100</option>
                    <option value="500" <?php if ($_REQUEST["show"] == 500) {
                                            echo ' selected="selected"';
                                        }  ?>>500</option>
                    <option value="1000" <?php if ($_REQUEST["show"] == 1000) {
                                                echo ' selected="selected"';
                                            }  ?>>1000</option>
                    <option value="2000" <?php if ($_REQUEST["show"] == 2000) {
                                                echo ' selected="selected"';
                                            }  ?>>2000</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                <?php 
                echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
                ?>
                </ul>
            </div>
        </div>
<?php require_once "disconnect.php"; ?>