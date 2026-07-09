<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include("../include/no_to_word.php");
$ctable       = "orders";
$ctable1      = "Orders";
$ctable_where = "";
$area         = $_REQUEST['area'];
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);
$Where='';$Where1='';$Where2='';$Where3='';$Where4='';$Where5='';$Where6=''; $Where7='';$Where8=''; $Where11='';$Where12='';

$Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE '%".$db->clean($_REQUEST['searchName'])."%'":"";


if($_REQUEST['ToDate'] == "" || $_REQUEST['ToDate'] == NULL)
{
    $Where11=' AND MONTH(created_date)=MONTH(now()) ';
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type'] != 'null'){
    //$Where1 .= " AND id = '".$_REQUEST['type']."' ";
    $Where1 .= " AND FIND_IN_SET (type,'".$_REQUEST['type']."')";
    $isFillter = true;
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id'] != NULL && $_REQUEST['sales_executive_id']!= "null"){
    $Where10 .= " AND id IN (".$_REQUEST['sales_executive_id'].") ";
    $isFillter = true;
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state'] != NULL){
    $Where9 .= " AND state = '".$_REQUEST['state']."' ";
    $isFillter = true;
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city'] != NULL){
    $Where9 .= " AND city = '".$_REQUEST['city']."' ";
    $isFillter = true;
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
    $isFillter = true;
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
    $isFillter = true;
}
// var_dump($isFillter);exit;

if ($isFillter) 
{
    // echo $month = date('m'); exit();
    $date = ($_REQUEST['FromDate']!="")?" - ".$_REQUEST['FromDate']." TO ":"";
    $date .=($_REQUEST['ToDate']!="")?$_REQUEST['ToDate']:"";

    //service_executive user not show condition start --//
        // $CID=array();
        // $SEID=array();
        // $sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
        // while($sales_type_d = mysqli_fetch_array($sales_type_r))
        // {
        //     $SEID[] = $sales_type_d['name'];
        // }
        // $SEID=implode("','",$SEID);
        // $Where .="  AND name NOT IN ('".$SEID."')  ";
    //service_executive user not show condition end --//
    if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
    {

        if($rights['personal_flag']==1)
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            $Where .= " AND id ='".$check_id."' ";
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
                        
                            $Where .= " AND id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")  ";  
                        
                        
                    }
                    // else
                    // {
                    //         $Where .= " AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";       
                    // }

            }
            // else
            // {
                    

            // }
        }

    }




    // $Query = "SELECT `sales_executive`.`id`, `sales_executive`.`name` AS name, `sales_executive`.`type` AS type, `sales_executive`.`state` AS state, `sales_executive`.`city` AS city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order, ( SELECT COUNT(*) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation, ( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT SUM(grand_total) FROM `orders` WHERE status=0 AND isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding_value, ( SELECT COUNT(*) FROM `orders` WHERE status=0 AND  isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding,( SELECT SUM(grand_total) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation_value, ( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND complain_assign_to=`sales_executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=0 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=-1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_prospect, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_lead, ( SELECT COUNT(*) FROM `followup` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where8." ) AS total_followups,( SELECT COUNT(*) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice, ( SELECT SUM(grand_total) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice_value ,( SELECT SUM(subtotal) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS subtotal_invoice_value ,( SELECT SUM(igst_amount) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_igst_invoice_value, ( SELECT COUNT(*) FROM `executive` WHERE isDelete = 0 AND seid=`sales_executive`.`id` ".$Where11." ) AS new_customer_onbord, ( SELECT COUNT(*) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment, ( SELECT SUM(paid_amount) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment_value FROM `sales_executive` WHERE isDelete = 0 ".$Where." ".$Where1." ".$Where9." ".$Where10." ORDER BY total_order_value DESC ;";
    $Query =  "SELECT
              `sales_executive`.`id`,
              `sales_executive`.`name` AS name,
              `sales_executive`.`type` AS type,
              `sales_executive`.`state` AS state,
              `sales_executive`.`city` AS city,
              (
                SELECT COUNT(*)
                FROM `orders`
                WHERE isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where2."
              ) AS total_order,
              (
                SELECT COUNT(*)
                FROM `quotation_detail`
                WHERE isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where7."
              ) AS total_quotation,
              (
                SELECT SUM(grand_total)
                FROM `orders`
                WHERE isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where2."
              ) AS total_order_value,
              (
                SELECT SUM(grand_total)
                FROM `orders`
                WHERE status = 0 AND isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where2."
              ) AS total_order_panding_value,
              (
                SELECT COUNT(*)
                FROM `orders`
                WHERE status = 0 AND isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where2."
              ) AS total_order_panding,
              (
                SELECT SUM(grand_total)
                FROM `quotation_detail`
                WHERE isDelete = 0 AND sales_id = `sales_executive`.`id` ".$Where7."
              ) AS total_quotation_value,
              (
                SELECT COUNT(*)
                FROM `visit`
                WHERE isDelete = 0 AND user_id = `sales_executive`.`id` ".$Where3."
              ) AS total_visit,
              (
                SELECT COUNT(*)
                FROM `complain`
                WHERE isDelete = 0 AND complain_assign_to = `sales_executive`.`id` ".$Where4."
              ) AS total_complain,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_lead_flag = 0 AND isDelete = 0 AND inquiry_assign_to = `sales_executive`.`id` ".$Where5."
              ) AS total_inquiry,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_lead_flag = -1 AND isDelete = 0 AND inquiry_assign_to = `sales_executive`.`id` ".$Where5."
              ) AS total_prospect,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_lead_flag = 1 AND isDelete = 0 AND inquiry_assign_to = `sales_executive`.`id` ".$Where5."
              ) AS total_lead,
              (
                SELECT COUNT(*)
                FROM `followup`
                WHERE isDelete = 0 AND user_id = `sales_executive`.`id` ".$Where8."
              ) AS total_followups,
              (
                SELECT COUNT(*)
                FROM `invoice_new`
                WHERE isDelete = 0 AND FIND_IN_SET(`sales_executive`.`id`, sales_id) ".$Where6."
              ) AS total_invoice,
              (
                SELECT SUM(grand_total)
                FROM `invoice_new`
                WHERE isDelete = 0 AND FIND_IN_SET(`sales_executive`.`id`, sales_id) ".$Where6."
              ) AS total_invoice_value,
              (
                SELECT SUM(subtotal)
                FROM `invoice_new`
                WHERE isDelete = 0 AND FIND_IN_SET(`sales_executive`.`id`, sales_id) ".$Where6."
              ) AS subtotal_invoice_value,
              (
                SELECT SUM(igst_amount)
                FROM `invoice_new`
                WHERE isDelete = 0 AND FIND_IN_SET(`sales_executive`.`id`, sales_id) ".$Where6."
              ) AS total_igst_invoice_value,
              (
                SELECT COUNT(*)
                FROM `executive`
                WHERE isDelete = 0 AND seid = `sales_executive`.`id` ".$Where11."
              ) AS new_customer_onbord,
              (
                SELECT COUNT(*)
                FROM `payment`
                WHERE isDelete = 0 AND sales_executive_id = `sales_executive`.`id` ".$Where12."
              ) AS total_payment,
              (
                SELECT SUM(paid_amount)
                FROM `payment`
                WHERE isDelete = 0 AND sales_executive_id = `sales_executive`.`id` ".$Where12."
              ) AS total_payment_value
            FROM
              `sales_executive`
            WHERE
              isDelete = 0 ".$Where." ".$Where1." ".$Where9." ".$Where10."
            ORDER BY
              total_order_value DESC;
            ";

    // echo $Query;exit();
    $ctable_r = $db->rp_getQuery($Query,$Where);
}
?>
<style type="text/css">
    .th{
        text-align: CENTER;
        VERTICAL-ALIGN: MIDDLE;
    }
    .top_value{
        font-weight: 700;
    }
    .repot_bg{
        background-color: #98989885;
    }
    /*td.repot_bg { background-color: gainsboro; }*/
    .table-scrollable {
    width: auto;
    height: 450px;
    overflow-x: scroll;
    overflow-y: scroll;
    border: 1px solid #e7ecf1;
    margin: 10px 0 !important;
    }
    .fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<div class="table-scrollable">
<form action="" name="frm" id="print_info" method="post">
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
        <thead class="fix-th">
			<tr>
				<td class="header" align="center" colspan="23" ><h3><b>Sales Officer WISE REPORT <?php echo $date;?></b></h3></td>
			</tr>
            <div style="position: sticky;">
                <!-- --- milan --- 22-06-2021 --- -->
                <tr>
                    <!-- <td></td> -->
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td> 
                    <!-- <td class="top_value text-right repot_bg" id="total-prospect"></td>
                    <td class="top_value text-right repot_bg" id="total-inquiry"></td>
                    <td class="top_value text-right repot_bg" id="total-lead"></td>
                    <td class="top_value text-right repot_bg" id="total-complain"></td>
                    <td class="top_value text-right repot_bg" id="total-followups"></td> -->
                    <td class="top_value text-right repot_bg" id="total-quotation"></td>
                    <td class="top_value text-right repot_bg" id=total-quotation-value></td>
                    <td class="top_value text-right repot_bg" id="total-order"></td>
                    <td class="top_value text-right repot_bg" id="total-order-value"></td>
                    <!-- <td class="top_value text-right repot_bg" id="total-invoice"></td> -->
                    <!-- <td class="top_value text-right repot_bg" id="total-without-invoice-value"></td> -->
                    <!-- <td class="top_value text-right repot_bg" id="total-invoice-value"></td> -->
                    <!-- <td class="top_value text-right repot_bg" id="total-order-pandding"></td>
                    <td class="top_value text-right repot_bg" id="total-order-pandding-value"></td>
                    <td class="top_value text-right repot_bg" id="total-payment-collection"></td>
                    <td class="top_value text-right repot_bg" id="total-payment-collection-value"></td>
                    <td class="top_value text-right repot_bg" id="total-visit"></td>
                    <td class="top_value text-right repot_bg" id="total-New-Customer-Onbord"></td> -->
                </tr>
                <!-- --- milan --- 22-06-2021 --- -->
                <tr>
                    <!-- <td></td> -->
                    <td colspan="5" style="text-align:center;font-size: 15px;border: 1px solid;"></td>
                    <!-- <td colspan="4" style="text-align:center;font-size: 15px;border: 1px solid;">Assign To</td> -->
                    <td colspan="7" style="text-align:center;font-size: 15px;border: 1px solid;">Created By</td>
                </tr>
                <tr class="tr">
                    <th class="th fix-th1" style="position: sticky;">Sr.No./Rank</th>
                    <!-- <th class="th" style="position: sticky;">Rank</th> -->
                    <th class="th fix-th1">Sales Person Type</th>
                    <th class="th fix-th1">Sales Person Name</th>
                    <th class="th fix-th1">Sales Person State</th>
                    <th class="th fix-th1">Sales Person City</th>
                    <!-- <th class="th">Total Taken Inquiry</th>
                    <th class="th">Total Assign Inquiry</th> -->
                    <!-- <th class="th repot_bg">Total Prospect</th>
                    <th class="th repot_bg">Total Inquiry</th>
                    <th class="th repot_bg">Total Lead</th>
                    <th class="th repot_bg">Total Complain</th>
                    <th class="th repot_bg">Total Followups</th> -->
                    <th class="th repot_bg fix-th1">Total Quotation</th>
                    <th class="th repot_bg fix-th1">Total Quotation Value</th>
                    <th class="th repot_bg fix-th1">Total Order</th>
                    <th class="th repot_bg fix-th1">Total Order Value</th>
                    <!-- <th class="th repot_bg">Total Invoice</th>
                    <th class="th repot_bg">Total Invoice Value<br/>Without GST</th>
                    <th class="th repot_bg">Total Invoice Value<br/>With GST</th> -->
                    <!-- <th class="th repot_bg">Total Pending Order</th>
                    <th class="th repot_bg">Total Pending Order Value</th>
                    <th class="th repot_bg">Total Payment Collection</th>
                    <th class="th repot_bg">Total Payment Collection Value</th>
                    <th class="th repot_bg">Total Visit</th>
                    <th class="th repot_bg">New-Customer-Onbord</th>                -->
                </tr>
            </div>
        </thead>
        <tbody>
        <?php
        if ($isFillter) 
        {
            if(mysqli_num_rows($ctable_r)>0){
                $count = 1;
                while($ctable_d = mysqli_fetch_array($ctable_r)){
                    if($_REQUEST['FromDate']!="" && $_REQUEST['ToDate']!="")
                    {
                        $taken_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_created_by='".$ctable_d['id']."' AND isDelete=0 AND inquiry_date <='" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'");
                        
                        $Assign_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_assign_to ='".$ctable_d['id']."' AND isDelete=0 AND inquiry_date <='" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'",0);
                    }
                    else
                    {
                        $taken_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_created_by='".$ctable_d['id']."' AND isDelete=0");

                        $Assign_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_assign_to ='".$ctable_d['id']."' AND isDelete=0 ");
                    } 
                ?>
                <tr class="tr" data-toggle="modal" data-id="1" data-target="#orderModal">
                    <td class="td" style="width:5px;" >
                    	<?php echo $count++; ?>
                    </td> 
                    <td class="td type_of_sales" >
                    	<?php  
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

                   <!--  <td class="td">
                        <?php echo $taken_inquiry; ?>
                    </td> -->

                    <!-- <td class="td">
                        <?php echo $Assign_inquiry; ?>
                    </td> -->

                    <!-- <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Prospect" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="prospect" data-toggle='modal'><?php echo ($ctable_d['total_prospect']!="")?stripslashes($ctable_d['total_prospect']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Inquiry" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="inquiry" data-toggle='modal'><?php echo ($ctable_d['total_inquiry']!="")?stripslashes($ctable_d['total_inquiry']):0; ?></a>
                    </td>
                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Lead" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="lead" data-toggle='modal'><?php echo ($ctable_d['total_lead']!="")?stripslashes($ctable_d['total_lead']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Complain" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="complain" data-toggle='modal'><?php echo ($ctable_d['total_complain']!="")?stripslashes($ctable_d['total_complain']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Followup" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="followup" data-toggle='modal'><?php echo ($ctable_d['total_followups']!="")?stripslashes($ctable_d['total_followups']):0; ?></a>
                    </td> -->

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Quotation" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="quotation" data-toggle='modal'><?php echo ($ctable_d['total_quotation']!="")?stripslashes($ctable_d['total_quotation']):0; ?></a>
                    </td>
                    
                    <td class="td text-right repot_bg">
                        <!-- <?php echo ($ctable_d['total_quotation_value']!="")?stripslashes($ctable_d['total_quotation_value']):0; ?>  -->
                        <?php echo ($ctable_d['total_quotation_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_quotation_value']),2):0; ?>
                    </td>

                    <td class="td text-right repot_bg">
                    	<a href='#myModal' data-title="Total Order" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="order" data-toggle='modal'><?php echo ($ctable_d['total_order']!="")?stripslashes($ctable_d['total_order']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                    	<?php echo ($ctable_d['total_order_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_order_value']),2):0; ?>
                    </td>

                   <!--  <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Invocie" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="invoice" data-toggle='modal'><?php echo ($ctable_d['total_invoice']!="")?stripslashes($ctable_d['total_invoice']):0; ?></a>
                    </td> 
                    <td class="td text-right repot_bg"> 
                        <?php $total_invoice_value_without_gst =  $ctable_d['subtotal_invoice_value']-$ctable_d['total_igst_invoice_value'];
                        echo $db->rp_number_format($total_invoice_value_without_gst);
                        ?>
                    </td>
                    <td class="td text-right repot_bg">
                        <?php echo ($ctable_d['total_invoice_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_invoice_value']),2):0; ?>
                    </td> -->

                    <!-- <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Total Pending Order" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="pending_order" data-toggle='modal'><?php echo ($ctable_d['total_order_panding']!="")?stripslashes($ctable_d['total_order_panding']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <?php echo ($ctable_d['total_order_panding_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_order_panding_value']),2):0; ?>
                    </td>

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Payment Collection" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="payment_collection" data-toggle='modal'><?php echo ($ctable_d['total_payment']!="")?stripslashes($ctable_d['total_payment']):0; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <?php echo ($ctable_d['total_payment_value']!="")?$db->rp_number_format(stripslashes($ctable_d['total_payment_value']),2):0; ?>
                    </td>

                    <td class="td text-right repot_bg">
                    	<a href='#myModal' data-title="Total Visit" data-todate="<?= $_REQUEST['ToDate'] ?>" data-fromdate="<?= $_REQUEST['FromDate'] ?>" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="visit" data-toggle='modal'><?php echo ($ctable_d['total_visit'])?stripslashes($ctable_d['total_visit']):0; ?></a>
                    </td>
                    

                    <td class="td text-right repot_bg">
                        <?php echo ($ctable_d['new_customer_onbord']!="")?stripslashes($ctable_d['new_customer_onbord']):0; ?>
                    </td> -->
                    <!--   <td class="td text-right">
                    	<?php echo ($ctable_d['total_inquiry']!="")?stripslashes($ctable_d['total_inquiry']):0; ?>
                    </td> -->
                   
                    <!-- --- milan --- 22-06-2021 --- -->
                    <?php
                    /*$t_prospect += $ctable_d['total_prospect']; 
                    $t_inquiry += $ctable_d['total_inquiry']; 
                    $t_lead += $ctable_d['total_lead']; 
                    $t_followups += $ctable_d['total_followups']; */
                    $t_quotation += $ctable_d['total_quotation']; 
                    //$t_quotation_val += $ctable_d['total_quotation_value']; 
                    $t_quotation_val = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status!=-1",0);
                    $t_order += $ctable_d['total_order']; 
                    //$t_order_val += $ctable_d['total_order_value'];  
                    $t_order_val = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status!=-1",0);  
                    $t_invoice += $ctable_d['total_invoice']; 
                    //$t_invoice_value += $ctable_d['total_invoice_value']; 
                    //$t_invoice_value = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status!=-1",0); 
                    $t_invoice_value = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status!=-1",0); 
                    $t_invoice_value_withtout_gst += $total_invoice_value_without_gst; 
                    // $t_order_pandding += $ctable_d['total_order_panding']; 
                    //$t_order_pandding_value += $ctable_d['total_order_panding_value']; 
                    /*$t_order_pandding_value = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status=0",0); 
                    $t_payment_pandding += $ctable_d['total_payment']; 
                    $t_payment_pandding_value = $db->rp_getValue("payment","SUM(paid_amount)","isDelete=0 AND payment_status=1",0); 
                    $t_visit += $ctable_d['total_visit']; 
                    $t_complain += $ctable_d['total_complain'];
                    $t_new_customer += $ctable_d['new_customer_onbord'];*/
                    ?>
                    <!-- --- milan --- 22-06-2021 --- -->
                </tr>
                <?php
                }
            }   
        }
        else
        {
            ?>
            <tr>
                <td colspan="9" class="text-center"><h3><strong><?= FILTER_INFO ?></strong></h3></td>
            </tr>
            <?php
        }
		?>
        </tbody>
    </table>
    
</form>
</div>
<div id="loading" class="modal fade" data-backdrop="false" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
					<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content modal-lg" style="margin-left: -143px;">
            <div class="modal-body portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <h4 class="modal-title model_title"></h4>
                        <input type="hidden" class="sales_id" value="">
                        <input type="hidden" class="mode" value="">
                        <input type="hidden" class="todate" value="">
                        <input type="hidden" class="fromdate" value="">
                    </div>
                    <div class="tools">

                        <a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

                        <a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
                    </div>
                </div>
                <div class="portlet-body portlet-empty" style="">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#myModal').on('show.bs.modal', function (event) 
    {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var requesting_id=button.data("id");
        $(".id").val(requesting_id);
        var requesting_title=button.data("title");
        $(".model_title").html(requesting_title);
        var sales_id=button.data("sales_id");
        $(".sales_id").val(sales_id);
        var todate=button.data("todate");
        $(".todate").val(todate);
        var fromdate=button.data("fromdate");
        $(".fromdate").val(fromdate);
        var mode=button.data("mode");
        $(".mode").val(mode);
        $("#requesting_ajax").attr("data-url","sales_performance_get_ajax.php?sales_id="+sales_id+"&mode="+mode+"&ToDate="+ToDate+"&FromDate="+FromDate);
        $("#requesting_ajax").click();
    })
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
</script>

<!-- --- milan --- 22-06-2021 --- -->
<script type="text/javascript">
    /*$("#total-prospect").html("<?php echo $t_prospect; ?>");
    $("#total-inquiry").html("<?php echo $t_inquiry; ?>");
    $("#total-lead").html("<?php echo $t_lead; ?>");
    $("#total-followups").html("<?php echo $t_followups; ?>");*/
    $("#total-quotation").html("<?php echo $t_quotation; ?>");
    $("#total-quotation-value").html("<?php echo $db->rp_number_format(stripslashes($t_quotation_val),2); ?>");
    $("#total-order").html("<?php echo $t_order; ?>");
    $("#total-order-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_val),2); ?>");
    $("#total-visit").html("<?php echo $t_visit; ?>");
    $("#total-complain").html("<?php echo $t_complain; ?>");
    $("#total-invoice").html("<?php echo $t_invoice; ?>");
    $("#total-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value),2); ?>");
    $("#total-without-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value_withtout_gst),2); ?>");
    /*$("#total-order-pandding").html("<?php echo $t_order_pandding; ?>");
    $("#total-order-pandding-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_pandding_value),2); ?>");
    $("#total-payment-collection").html("<?php echo $db->rp_number_format(stripslashes($t_payment_pandding),2); ?>");
    $("#total-payment-collection-value").html("<?php echo $db->rp_number_format(stripslashes($t_payment_pandding_value),2); ?>");
    $("#total-New-Customer-Onbord").html("<?php echo $t_new_customer; ?>");*/
</script>
<!-- --- milan --- 22-06-2021 --- -->

<script>
function genReport(cid){
	if($("#datatable_1").find("tbody").find("tr").length>=1)
	{
	    var rc = encodeURIComponent($("#print_info").html());
        $.ajax({
    		type: "POST",
    		url: "ordersReport_gen_ajax.php",
    		data: '&rc='+rc,
    		beforeSend: function() {
    			$(".transCover").fadeIn(800);
    			$("#loading").modal('show');
    		},
    		success: function(result)
            { 
                setTimeout(function()
                {
					$(".transCover").fadeOut(100);
					alert("Report file generated!!");
					$("#loading").modal('hide');
					window.open(result,'_blank');// <- This is what makes it open in a new window.
				},1500);
			}
    	});
    }
    else
    {
    	toastr.error("Report Can't generated");
    }
}
</script>

<script type="text/javascript">
    function ExportExcel(sales_id,mode)
    {
        $.ajax({
            method: "POST",
            url: "salesexecutive_performance_gen_ajax.php",
            data: 'sales_id=' + sales_id + '&mode=' + mode,
            dataType : 'json',
            beforeSend: function() {
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
        });
    }
    function ExportExcelQOI(sales_id,mode)
    {
        $.ajax({
            method: "POST",
            url: "salesexecutive_performance_one_gen_ajax.php",
            data: 'sales_id=' + sales_id + '&mode=' + mode,
            dataType : 'json',
            beforeSend: function() {
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
        });
    }
    function ExportExcelvisit(sales_id,mode)
    {
        $.ajax({
            method: "POST",
            url: "salesexecutive_performance_gen_visit_ajax.php",
            data: 'sales_id=' + sales_id + '&mode=' + mode,
            dataType : 'json',
            beforeSend: function() {
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
        });
    }
    function ExportExcelcomplain(sales_id,mode)
    {
        $.ajax({
            method: "POST",
            url: "salesexecutive_performance_gen_complain_ajax.php",
            data: 'sales_id=' + sales_id + '&mode=' + mode,
            dataType : 'json',
            beforeSend: function() {
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
        });
    }
    function ExportExcelfollowup(sales_id,mode)
    {
        $.ajax({
            method: "POST",
            url: "salesexecutive_performance_gen_followup_ajax.php",
            data: 'sales_id=' + sales_id + '&mode=' + mode,
            dataType : 'json',
            beforeSend: function() {
                $('.preloader').fadeIn('slow');
            },
            success: function(result){
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                window.location.href="<?=SITEURL?>"+result.file_path;
            },
        });
    }
</script>
<?php require_once("disconnect.php"); ?>