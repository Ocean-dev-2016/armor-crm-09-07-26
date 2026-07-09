<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable       = "orders";
$ctable1      = "Orders";
$ctable_where = "";
$area         = $_REQUEST['area'];

$Where='';$Where1='';$Where2='';$Where3='';$Where4='';$Where5='';$Where6=''; $Where7='';$Where8=''; $Where11='';$Where12='';

$Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE '%".$db->clean($_REQUEST['searchName'])."%'":"";


if($_REQUEST['ToDate'] == "" || $_REQUEST['ToDate'] == NULL)
{
    $Where11=' AND MONTH(created_date)=MONTH(now()) ';
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type'] != 'null'){
    //$Where1 .= " AND id = '".$_REQUEST['type']."' ";
    $Where1 .= " AND FIND_IN_SET (type,'".$_REQUEST['type']."')";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id'] != NULL && $_REQUEST['sales_executive_id']!= "null"){
    $Where10 .= " AND id IN (".$_REQUEST['sales_executive_id'].") ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state'] != NULL){
    $Where9 .= " AND state = '".$_REQUEST['state']."' ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city'] != NULL){
    $Where9 .= " AND city = '".$_REQUEST['city']."' ";
}
// echo $Where; exit;

if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
    $Where2 = " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where3 = " AND DATE(created_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where4 = " AND DATE(complain_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where5 = " AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where6 = " AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where7 .= " AND quotation_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where8 .= " AND DATE(followup_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where11 .= " AND DATE(created_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where12 .= " AND DATE(payment_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";


    // $Where8 .= " AND MONTH(created_date)=MONTH(now()) ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
    $Where2 .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where3 .= " AND DATE(created_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where4 .= " AND DATE(complain_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where5 .= " AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where6 .= " AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where8 .= " AND DATE(followup_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where11 .= " AND DATE(created_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where12 .= " AND DATE(payment_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";

    // $Where8 .= " AND MONTH(created_date)=MONTH(now()) ";

    // MONTH(date_joined)=MONTH(now()) AND YEAR(date_joined)=YEAR(now());
}
// echo $month = date('m'); exit();
$date = ($_REQUEST['FromDate']!="")?" - ".$_REQUEST['FromDate']." TO ":"";
$date .=($_REQUEST['ToDate']!="")?$_REQUEST['ToDate']:"";

//service_executive user not show condition start --//
    $CID=array();
    $SEID=array();
    $sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
    while($sales_type_d = mysqli_fetch_array($sales_type_r))
    {
        $SEID[] = $sales_type_d['name'];
    }
    $SEID=implode("','",$SEID);
    $Where .="  AND name NOT IN ('".$SEID."')  ";
//service_executive user not show condition end --//

$Query = "SELECT `sales_executive`.`id`, `sales_executive`.`name` AS name, `sales_executive`.`type` AS type, `sales_executive`.`state` AS state, `sales_executive`.`city` AS city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order, ( SELECT COUNT(*) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation, ( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT SUM(grand_total) FROM `orders` WHERE status=0 AND isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding_value, ( SELECT COUNT(*) FROM `orders` WHERE status=0 AND  isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding,( SELECT SUM(grand_total) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation_value, ( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND complain_assign_to=`sales_executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=0 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=-1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_prospect, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_lead, ( SELECT COUNT(*) FROM `followup` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where8." ) AS total_followups,( SELECT COUNT(*) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice, ( SELECT SUM(grand_total) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice_value ,( SELECT SUM(subtotal) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS subtotal_invoice_value ,( SELECT SUM(igst_amount) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_igst_invoice_value, ( SELECT COUNT(*) FROM `executive` WHERE isDelete = 0 AND seid=`sales_executive`.`id` ".$Where11." ) AS new_customer_onbord, ( SELECT COUNT(*) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment, ( SELECT SUM(paid_amount) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment_value FROM `sales_executive` WHERE isDelete = 0 ".$Where." ".$Where1." ".$Where9." ".$Where10." ORDER BY total_order_value DESC ;";
// echo $Query;exit();
$ctable_r = $db->rp_getQuery($Query,$Where);
?>
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
        <thead>
            <tr>
                <td class="header" align="center" colspan="22" ><h3><b>Sales Officer PERFORMANCE REPORT <?php echo $date;?></b></br>
                Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?>
                </h3></td>
            </tr>
            <div style="position: sticky;">
                    <!-- --- milan --- 22-06-2021 --- -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td id="total-prospect"></td>
                        <td id="total-inquiry"></td>
                        <td id="total-lead"></td>
                        <td id="total-followups"></td> -->
                        <td id="total-quotation"></td>
                        <td id=total-quotation-value></td>
                        <td id="total-order"></td>
                        <td id="total-order-value"></td>
                        <td id="total-invoice"></td>
                        <td id="total-invoice-value-without"></td>
                        <td id="total-invoice-value-with-gst"></td>
                        <!-- <td id="total-order-pandding"></td>
                        <td id="total-order-pandding-value"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-payment-collection"></td>
                        <td class="top_value text-right repot_bg" id="total-payment-collection-value"></td> -->
                        <!-- <td id="total-visit"></td>
                        <td id="total-complain"></td>
                        <td id="total-New-Customer-Onbord"></td> -->
                    </tr>
                    <!-- --- milan --- 22-06-2021 --- -->
                <tr class="tr">
                    <th class="th" style="position: sticky;">Sr.No./Rank</th>
                    <th class="th">Sales Person Type</th>
                    <th class="th">Sales Person Name</th>
                    <th class="th">Sales Person State</th>
                    <th class="th">Sales Person City</th>
                    <!-- <th class="th">Total Prospect</th>
                    <th class="th">Total Inquiry</th>
                    <th class="th">Total Lead</th>
                    <th class="th">Total Complain</th>
                    <th class="th">Total Followups</th> -->
                    <th class="th">Total Quotation</th>
                    <th class="th">Total Quotation Value</th>
                    <th class="th">Total Order</th>
                    <th class="th">Total Order Value</th>
                    <th class="th">Total Invoice</th>
                    <th class="th repot_bg">Total Invoice Value<br/>Without GST</th>
                    <th class="th repot_bg">Total Invoice Value<br/>With GST</th>
                    <!-- <th class="th">Total Pending Order</th>
                    <th class="th">Total Pending Order Value</th>
                    <th class="th repot_bg">Total Payment Collection</th>
                    <th class="th repot_bg">Total Payment Collection Value</th>
                    <th class="th">Total Visit</th>
                    <th class="th">New-Customer-Onbord</th> -->               

                </tr>

            </div>
            
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 1;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr class="tr">
                <td class="td" style="width:5px;">
                    <?php echo $count++; ?>
                </td>
                <td class="td">
                    <?php 
                        // if($ctable_d['type']=="sales_manager")
                        // {
                        //     $ctable_d['type']="Regional Sales Manager";
                        // }

                        // if($ctable_d['type']=="area_sales_manager")
                        // {
                        //     $ctable_d['type']="Business Development Manager";
                        // }

                        // if($ctable_d['type']=="sales_officer")
                        // {
                        //     $ctable_d['type']="Area Sales Manager";
                        // }

                        // if($ctable_d['type']=="sales_executive")
                        // {
                        //     $ctable_d['type']="Sales Officer";
                        // }

                        if($ctable_d['type']=="sales_manager")
                        {
                            $ctable_d['type']="Regional Sales Manager";
                        }

                        if($ctable_d['type']=="area_sales_manager")
                        {
                            $ctable_d['type']="Business Development Manager";
                        }

                        if($ctable_d['type']=="dispatch_sales_manager")
                        {
                            $ctable_d['type']="Dispatch Manager";
                        }
                        
                        if($ctable_d['type']=="sales_officer")
                        {
                            $ctable_d['type']="Area Sales Manager";
                        }
                        
                        if($ctable_d['type']=="sales_executive")
                        {
                            $ctable_d['type']="Sales Officer";
                        }
                        if($ctable_d['type']=="service_executive")
                        {
                            $ctable_d['type']="Service Executive";
                        }
                        echo stripslashes($ctable_d['type']); 
                    ?>
                </td>
                <td class="td">
                    <?php echo stripslashes($ctable_d['name']); ?>
                </td>
                <td class="td">
                    <?php echo stripslashes($ctable_d['state']); ?>
                </td>
                <td class="td">
                    <?php echo stripslashes($ctable_d['city']); ?>
                </td>

               <!--  <td class="td text-right">
                    <?php echo ($ctable_d['total_prospect']!="")?stripslashes($ctable_d['total_prospect']):0; ?>
                </td>

                <td class="td text-right">
                    <?php echo ($ctable_d['total_inquiry']!="")?stripslashes($ctable_d['total_inquiry']):0; ?>
                </td>
                <td class="td text-right">
                    <?php echo ($ctable_d['total_lead']!="")?stripslashes($ctable_d['total_lead']):0; ?>
                </td>

                <td class="td text-right">
                    <?php echo ($ctable_d['total_complain']!="")?stripslashes($ctable_d['total_complain']):0; ?>
                </td>

                <td class="td text-right">
                    <?php echo ($ctable_d['total_followups']!="")?stripslashes($ctable_d['total_followups']):0; ?>
                </td> -->
                <td class="td text-right">

                     <?php echo ($ctable_d['total_quotation']!="")?stripslashes($ctable_d['total_quotation']):0; ?> 

                </td>
                 <td class="td text-right">

                     <?php echo ($ctable_d['total_quotation_value']!="")?stripslashes($ctable_d['total_quotation_value']):0; ?> 

                </td>
                <td class="td text-right">
                    <?php echo ($ctable_d['total_order']!="")?stripslashes($ctable_d['total_order']):0; ?>
                </td>
                <td class="td text-right">
                    <?php echo ($ctable_d['total_order_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_order_value']),2):0; ?>
                </td>
                 <td class="td text-right">
                    <?php echo ($ctable_d['total_invoice']!="")?stripslashes($ctable_d['total_invoice']):0; ?>
                </td> 
                <td class="td text-right repot_bg"> 
                    <?php $total_invoice_value_without_gst =  $ctable_d['subtotal_invoice_value']-$ctable_d['total_igst_invoice_value'];
                    echo $db->rp_number_format($total_invoice_value_without_gst);
                    ?>
                </td>
                <td class="td text-right repot_bg">
                    <?php echo ($ctable_d['total_invoice_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_invoice_value']),2):0; ?>
                </td>

                <!-- <td class="td text-right">
                    <?php echo ($ctable_d['total_order_panding']!="")?stripslashes($ctable_d['total_order_panding']):0; ?>
                </td>

                <td class="td text-right">
                    <?php echo ($ctable_d['total_order_panding_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_order_panding_value']),2):0; ?>
                </td>

                <td class="td text-right repot_bg">
                   <?php echo ($ctable_d['total_payment']!="")?stripslashes($ctable_d['total_payment']):0; ?>
                </td>

                <td class="td text-right repot_bg">
                    <?php echo ($ctable_d['total_payment_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_payment_value']),2):0; ?>
                </td>

                <td class="td text-right">
                    <?php echo ($ctable_d['total_visit'])?stripslashes($ctable_d['total_visit']):0; ?>
                </td>
                
                <td class="td text-right">
                    <?php echo ($ctable_d['new_customer_onbord']!="")?stripslashes($ctable_d['new_customer_onbord']):0; ?>
                </td> -->
              <!--   <td class="td text-right">
                    <?php echo ($ctable_d['total_inquiry']!="")?stripslashes($ctable_d['total_inquiry']):0; ?>
                </td> -->
               
                <!-- --- milan --- 22-06-2021 --- -->
                <?php
                $t_prospect += $ctable_d['total_prospect']; 
                $t_inquiry += $ctable_d['total_inquiry']; 
                $t_lead += $ctable_d['total_lead']; 
                $t_followups += $ctable_d['total_followups']; 
                $t_quotation += $ctable_d['total_quotation']; 
                $t_quotation_val += $ctable_d['total_quotation_value']; 
                $t_order += $ctable_d['total_order']; 
                $t_order_val += $ctable_d['total_order_value'];  
                $t_invoice += $ctable_d['total_invoice']; 
                $t_invoice_value += $ctable_d['total_invoice_value']; 
                $t_order_pandding += $ctable_d['total_order_panding']; 
                $t_order_pandding_value += $ctable_d['total_order_panding_value']; 
                $t_visit += $ctable_d['total_visit']; 
                $t_complain += $ctable_d['total_complain'];
                $t_new_customer += $ctable_d['new_customer_onbord'];
                ?>
                <!-- --- milan --- 22-06-2021 --- -->
                
            </tr>
        <?php
            }
        }
        
        ?>
        </tbody>
    </table>
    <!-- <script type="text/javascript">
    $("#total-prospect").html("<?php echo $t_prospect; ?>");
    $("#total-inquiry").html("<?php echo $t_inquiry; ?>");
    $("#total-lead").html("<?php echo $t_lead; ?>");
    $("#total-followups").html("<?php echo $t_followups; ?>");
    $("#total-quotation").html("<?php echo $t_quotation; ?>");
    $("#total-quotation-value").html("<?php echo $db->rp_number_format(stripslashes($t_quotation_val),2); ?>");
    $("#total-order").html("<?php echo $t_order; ?>");
    $("#total-order-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_val),2); ?>");
    $("#total-visit").html("<?php echo $t_visit; ?>");
    $("#total-complain").html("<?php echo $t_complain; ?>");
    $("#total-invoice").html("<?php echo $t_invoice; ?>");
    $("#total-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value),2); ?>");
    $("#total-order-pandding").html("<?php echo $t_order_pandding; ?>");
    $("#total-order-pandding-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_pandding_value),2); ?>");
    $("#total-New-Customer-Onbord").html("<?php echo $t_new_customer; ?>");
</script> -->
