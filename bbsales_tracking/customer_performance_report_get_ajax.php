    <?php
    $page_id = 602;
    $page_slug = 'customer_performance_report_page';
    /*
 * @author Ravi Patel
 */
    include("connect.php");
    include("../include/no_to_word.php");
    $ctable       = "orders";
    $ctable1      = "Orders";
    $ctable_where = "";
    $area         = $_REQUEST['area'];
    $isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);

    $Where = '';
    $Where1 = '';
    $Where2 = '';
    $Where3 = '';
    $Where4 = '';
    $Where5 = '';
    $Where6 = '';
    $Where7 = '';
    $Where9 = '';

    // $Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND company_name LIKE %'".$_REQUEST['searchName']."'%":"";
    // $Where1 = (isset($_REQUEST['type']) && $_REQUEST['type'] != "")?" AND type_of_executive ='".$_REQUEST['type']."'":"";

    $Where = "";

    if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
        $Where .= " AND (cname LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code LIKE '%" . $db->clean($_REQUEST['searchName']) . "%') ";
    }


    if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != 'null') {
        // $Where1 = (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL)?" AND type ='".$_REQUEST['type']."'":"";

        $Where1 .= " AND type_of_executive IN (" . $_REQUEST['type'] . ") ";
        $isFillter = true;
    }

    if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
        $Where9 .= " AND state = '" . $_REQUEST['state'] . "' ";
        $isFillter = true;
    }

    if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
        $Where9 .= " AND main_city = '" . $_REQUEST['city'] . "' ";
        $isFillter = true;
    }
    if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL) {
        $Where9 .= " AND city = '" . $_REQUEST['route'] . "' ";
        $isFillter = true;
    }
    if (isset($_REQUEST['zone']) && $_REQUEST['zone'] != "" && $_REQUEST['zone'] != NULL) {
        $Where9 .= " AND zone = '" . $_REQUEST['zone'] . "' ";
        $isFillter = true;
    }

    if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
        $Where2 = " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $Where3 = " AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $Where4 = " AND complain_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $Where5 = " AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $Where6 = " AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
        $isFillter = true;
    }

    if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
        $Where2 .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $Where3 .= " AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $Where4 .= " AND complain_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $Where5 .= " AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $Where6 .= " AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
        $isFillter = true;
    }
    $date = ($_REQUEST['FromDate'] != "") ? " - " . $_REQUEST['FromDate'] . " TO " : "";
    $date .= ($_REQUEST['ToDate'] != "") ? $_REQUEST['ToDate'] : "";
    // $Query = "SELECT `sales_executive`.`id`, `sales_executive`.`company_name` AS name, ( SELECT name FROM `customer_type` WHERE id = `sales_executive`.`type_of_executive` ) AS type, `sales_executive`.`state` AS state, `sales_executive`.`city` AS city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order, ( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `invoice` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_executive_id) ".$Where6." ) AS total_invoice, ( SELECT SUM(amount) FROM `invoice` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_executive_id) ".$Where6." ) AS total_invoice_value FROM `sales_executive` WHERE isDelete = 0 ".$Where." ".$Where1.";";
    if ($isFillter) {
        // $Query = "SELECT `executive`.`id`, `executive`.`company_name` AS name, ( SELECT name FROM `customer_type` WHERE id = `executive`.`type_of_executive` ) AS type, `executive`.`state` AS state, `executive`.`main_city` AS main_city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where2." ) AS total_order,  ( SELECT COUNT(*) FROM `quotation_detail` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where7." ) AS total_quotation,( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT SUM(grand_total) FROM `quotation_detail` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where7." ) AS total_quotation_value,( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where6." ) AS total_invoice, ( SELECT SUM(grand_total) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where6." ) AS total_invoice_value , ( SELECT SUM(subtotal) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where6." ) AS subtotal_invoice_value , ( SELECT SUM(igst_amount) FROM `invoice_new` WHERE isDelete = 0 AND customer_id=`executive`.`id` ".$Where6." ) AS total_igst_invoice_value, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=-1 AND  isDelete = 0 AND dealer_id=`executive`.`id` ".$Where5." ) AS total_prospect ,( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=0 AND  isDelete = 0 AND dealer_id=`executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_type=1 AND  isDelete = 0 AND dealer_id=`executive`.`id` ".$Where5." ) AS total_lead, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE status=1 AND  isDelete = 0 AND dealer_id=`executive`.`id` ".$Where5." ) AS total_followups, ( SELECT SUM(credit + debit) FROM `account_transaction` WHERE isDelete = 0 AND cid=`executive`.`id` ".$Where5." ) AS outstanding_amount FROM `executive` WHERE isDelete = 0 ".$Where." ".$Where1." ".$Where9." LIMIT 0,200 ";

        $Query = "SELECT
              `executive`.`id`,
              `executive`.`company_name` AS name,
              `executive`.`cname` as person_name,
              `executive`.`client_code` as client_code,
              `executive`.`customer_flag` as customer_flag,
              (
                SELECT name
                FROM `customer_type`
                WHERE id = `executive`.`type_of_executive`
              ) AS type,
              `executive`.`state` AS state,
              `executive`.`main_city` AS main_city,
              (
                SELECT COUNT(*)
                FROM `orders`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where2 . "
              ) AS total_order,
              (
                SELECT COUNT(*)
                FROM `quotation_detail`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where7 . "
              ) AS total_quotation,
              (
                SELECT SUM(grand_total)
                FROM `orders`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where2 . "
              ) AS total_order_value,
              (
                SELECT SUM(grand_total)
                FROM `quotation_detail`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where7 . "
              ) AS total_quotation_value,
              (
                SELECT COUNT(*)
                FROM `visit`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where3 . "
              ) AS total_visit,
              (
                SELECT COUNT(*)
                FROM `complain`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where4 . "
              ) AS total_complain,
              (
                SELECT COUNT(*)
                FROM `invoice_new`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where6 . "
              ) AS total_invoice,
              (
                SELECT SUM(grand_total)
                FROM `invoice_new`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where6 . "
              ) AS total_invoice_value,
              (
                SELECT SUM(subtotal)
                FROM `invoice_new`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where6 . "
              ) AS subtotal_invoice_value,
              (
                SELECT SUM(igst_amount)
                FROM `invoice_new`
                WHERE isDelete = 0 AND customer_id = `executive`.`id` " . $Where6 . "
              ) AS total_igst_invoice_value,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_type = -1 AND isDelete = 0 AND dealer_id = `executive`.`id` " . $Where5 . "
              ) AS total_prospect,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_type = 0 AND isDelete = 0 AND dealer_id = `executive`.`id` " . $Where5 . "
              ) AS total_inquiry,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE inquiry_type = 1 AND isDelete = 0 AND dealer_id = `executive`.`id` " . $Where5 . "
              ) AS total_lead,
              (
                SELECT COUNT(*)
                FROM `no_order_inquiry`
                WHERE status = 1 AND isDelete = 0 AND dealer_id = `executive`.`id` " . $Where5 . "
              ) AS total_followups,
              (
                SELECT SUM(credit + debit)
                FROM `account_transaction`
                WHERE isDelete = 0 AND cid = `executive`.`id` " . $Where5 . "
              ) AS outstanding_amount
            FROM
              `executive`
            WHERE
              isDelete = 0 " . $Where . " " . $Where1 . " " . $Where9 . "
            LIMIT
              0, 200";


        // echo $Query;exit;
        $ctable_r = $db->rp_getQuery($Query);
        $total_order = 0;
    }
    ?>
    <!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button> -->
    <style type="text/css">
        .table-scrollable {
            width: auto;
            height: 450px;
            overflow-x: scroll;
            overflow-y: scroll;
            border: 1px solid #e7ecf1;
            margin: 10px 0 !important;
        }

        .th {
            text-align: CENTER;
            VERTICAL-ALIGN: MIDDLE;
        }

        .top_value {
            font-weight: 700;

        }

        .repot_bg {
            /*background-color: #b7b5b5;*/
            background-color: #98989885;
            /*#98989885*/
            /*border-color: black;*/
        }

        .fix-th {
            background-color: #f5f5f5 !important;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .fix-th1 {
            background-color: #e5e5e5 !important;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        /*td.repot_bg { background-color: gainsboro; }*/
    </style>

    <div class="">
        <form action="" name="frm" id="print_info" method="post">
            <table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
                <thead class="fix-th">
                    <tr>
                        <td class="header" align="center" colspan="18">
                            <h3><b>CUSTOMER WISE REPORT <?php echo $date; ?></b></h3>
                        </td>
                    </tr>
                    <!-- --- milan --- 22-06-2021 --- -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td class="top_value text-right repot_bg" id="total-prospect"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-inquiry"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-lead"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-followups"></td> -->
                        <td class="top_value text-right repot_bg" id="total-quotation"></td>
                        <td class="top_value text-right repot_bg" id=total-quotation-value></td>
                        <td class="top_value text-right repot_bg" id="total-order"></td>
                        <td class="top_value text-right repot_bg" id="total-order-value"></td>
                        <!-- <td class="top_value text-right repot_bg" id="total-visit"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-complain"></td> -->
                        <!-- <td class="top_value text-right repot_bg" id="total-invoice"></td>
                <td class="top_value text-right repot_bg" id="total-without-invoice-value"></td>
                <td class="top_value text-right repot_bg" id="total-invoice-value"></td>
                <td class="top_value text-right repot_bg" id="total-Outstanding-Amount"></td> -->
                    </tr>
                    <tr class="tr">
                        <th class="th fix-th1">No.</th>
                        <th class="th fix-th1">Customer Type</th>
                        <th class="th fix-th1">Customer Name</th>
                        <th class="th fix-th1">Person Name</th>
                        <th class="th fix-th1">Client Code</th>
                        <th class="th fix-th1">Customer State</th>
                        <th class="th fix-th1">Customer City</th>

                        <!-- <th class="th repot_bg">Total Prospect</th>                
                <th class="th repot_bg">Total Inquiry</th>
                <th class="th repot_bg">Total Lead</th>
                <th class="th repot_bg">Total Followups</th> -->
                        <th class="th repot_bg fix-th1">Total Quotation</th>
                        <th class="th repot_bg fix-th1">Total Quotation Value</th>

                        <th class="th repot_bg fix-th1">Total Order</th>
                        <th class="th repot_bg fix-th1">Total Order Value</th>
                        <!-- <th class="th repot_bg">Total Visit</th> -->
                        <!-- <th class="th repot_bg">Total Complain</th> -->
                        <!-- <th class="th">Total Inquiry</th> -->
                        <!-- <th class="th repot_bg">Total Invoice</th>
                <th class="th repot_bg">Total Invoice Value<br/>Without GST</th>
                <th class="th repot_bg">Total Invoice Value<br/>With GST</th>
                <th class="th repot_bg">Outstanding Amount</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($isFillter) {
                        if (mysqli_num_rows($ctable_r) > 0) {
                            $count = 1;

                            while ($ctable_d = mysqli_fetch_array($ctable_r)) {
                                $customer_flag_text = "";
                                if ($ctable_d['customer_flag'] == 1) {
                                    $customer_flag_text = " - P";
                                } else if ($ctable_d['customer_flag'] == 0) {
                                    $customer_flag_text = " - C";
                                }
                    ?>
                                <tr class="tr">
                                    <td class="td" style="width:5px;">
                                        <?php echo $count++; ?>
                                    </td>

                                    <td class="td">
                                        <?php echo stripslashes($ctable_d['type']); ?>
                                    </td>

                                    <td class="td"><a target="_blank" href="item_wise_sales_report_manage.php?customer_id=<?= $ctable_d['id'] ?>"><?php echo stripslashes($ctable_d['name'] . $customer_flag_text); ?></a>
                                    </td>
                                    <td class="td"><?php echo stripslashes($ctable_d['person_name']); ?></td>
                                    <td class="td"><?php echo stripslashes($ctable_d['client_code']); ?></td>
                                    <td class="td">
                                        <?php echo stripslashes($ctable_d['state']); ?>
                                    </td>
                                    <td class="td">
                                        <?php echo stripslashes($ctable_d['main_city']); ?>
                                    </td>
                                    <!-- <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Prospect" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="prospect" data-toggle='modal'><?php echo stripslashes($ctable_d['total_prospect']);
                                                                                                                                                    $total_prospect += $ctable_d['total_prospect']; ?></a>
                    </td>

                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Inquiry" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="inquiry" data-toggle='modal'><?php echo ($ctable_d['total_inquiry'] != "") ? stripslashes($ctable_d['total_inquiry']) : 0; ?></a>
                    </td>
                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Lead" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="lead" data-toggle='modal'><?php echo ($ctable_d['total_lead'] != "") ? stripslashes($ctable_d['total_lead']) : 0; ?></a>
                    </td>
                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Followup" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="followup" data-toggle='modal'><?php echo ($ctable_d['total_followups'] != "") ? stripslashes($ctable_d['total_followups']) : 0; ?></a>
                    </td> -->

                                    <td class="td text-right repot_bg">
                                        <a href='#myModal' data-title="Quotation" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="quotation" data-toggle='modal'><?php echo stripslashes($ctable_d['total_quotation']);
                                                                                                                                                                        $total_quotation += $ctable_d['total_quotation']; ?></a>
                                    </td>
                                    <td class="td text-right repot_bg">
                                        <?php echo ($ctable_d['total_quotation_value'] != "") ? $db->rp_number_format(stripslashes($ctable_d['total_quotation_value']), 2) : 0; ?>
                                    </td>
                                    <td class="td text-right repot_bg">
                                        <a href='#myModal' data-title="Order" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="order" data-toggle='modal'><?php echo ($ctable_d['total_order'] != "") ? stripslashes($ctable_d['total_order']) : 0;
                                                                                                                                                                $total_order += $ctable_d['total_order']; ?></a>
                                    </td>
                                    <td class="td text-right repot_bg">
                                        <?php echo ($ctable_d['total_order_value'] != "") ? $db->rp_number_format(stripslashes($ctable_d['total_order_value']), 2) : 0; ?>
                                    </td>
                                    <!--  <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Visit" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="visit" data-toggle='modal'><?php echo ($ctable_d['total_visit']) ? stripslashes($ctable_d['total_visit']) : 0; ?></a>
                    </td>
                    <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Complain" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="complain" data-toggle='modal'><?php echo ($ctable_d['total_complain'] != "") ? stripslashes($ctable_d['total_complain']) : 0; ?></a>
                    </td> -->
                                    <!-- <td class="td text-right">
                        <?php echo ($ctable_d['total_inquiry'] != "") ? stripslashes($ctable_d['total_inquiry']) : 0; ?>
                    </td> -->
                                    <!-- <td class="td text-right repot_bg">
                        <a href='#myModal' data-title="Invoice" data-customer_id="<?= $ctable_d['id'] ?>" data-mode="invoice" data-toggle='modal'><?php echo ($ctable_d['total_invoice'] != "") ? stripslashes($ctable_d['total_invoice']) : 0; ?></a>
                    </td>
                    <td class="td text-right repot_bg"> 
                        <?php $total_invoice_value_without_gst =  $ctable_d['subtotal_invoice_value'] - $ctable_d['total_igst_invoice_value'];
                                echo $db->rp_number_format($total_invoice_value_without_gst);
                        ?>
                    </td>
                    <td class="td text-right repot_bg"> 
                        <?php echo ($ctable_d['total_invoice_value'] != "") ? $db->rp_number_format(stripslashes($ctable_d['total_invoice_value']), 2) : 0; ?>
                    </td>

                    <td class="td text-right repot_bg">
                        <?php echo ($ctable_d['outstanding_amount'] != "") ? $db->rp_number_format(stripslashes($ctable_d['outstanding_amount']), 2) : 0; ?>
                    </td> -->

                                    <!-- --- milan --- 22-06-2021 --- -->
                                    <?php

                                    /*$t_prospect += $ctable_d['total_prospect']; 
                    $t_inquiry += $ctable_d['total_inquiry']; 
                    $t_lead += $ctable_d['total_lead']; 
                    $t_followups += $ctable_d['total_followups'];*/

                                    $t_quotation += $ctable_d['total_quotation'];
                                    $t_quotation_val += $ctable_d['total_quotation_value'];
                                    $t_order += $ctable_d['total_order'];
                                    $t_order_val += $ctable_d['total_order_value'];
                                    // $t_visit += $ctable_d['total_visit']; 
                                    // $t_complain += $ctable_d['total_complain']; 
                                    $t_invoice += $ctable_d['total_invoice'];
                                    $t_invoice_value += $ctable_d['total_invoice_value'];
                                    $t_invoice_value_withtout_gst += $total_invoice_value_without_gst;
                                    ?>
                                    <!-- --- milan --- 22-06-2021 --- -->
                                </tr>
                        <?php
                            }
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                <h3><strong><?= FILTER_INFO ?></strong></h3>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

        </form>
    </div>
    <div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false">
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
                            <input type="hidden" class="customer_id" value="">
                            <input type="hidden" class="mode" value="">
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
        $('#myModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var requesting_id = button.data("id");
            $(".id").val(requesting_id);
            var requesting_title = button.data("title");
            $(".model_title").html(requesting_title);
            var customer_id = button.data("customer_id");
            $(".customer_id").val(customer_id);
            var mode = button.data("mode");
            $(".mode").val(mode);
            $("#requesting_ajax").attr("data-url", "customer_performance_get_ajax.php?customer_id=" + customer_id + "&mode=" + mode);
            $("#requesting_ajax").click();
        })
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    </script>

    <!-- --- milan --- 22-06-2021 --- -->
    <script type="text/javascript">
        // $("#total-prospect").html("<?php echo $t_prospect; ?>");
        // $("#total-inquiry").html("<?php echo $t_inquiry; ?>");
        // $("#total-lead").html("<?php echo $t_lead; ?>");
        // $("#total-followups").html("<?php echo $t_followups; ?>");
        $("#total-quotation").html("<?php echo $t_quotation; ?>");
        $("#total-quotation-value").html("<?php echo $db->rp_number_format(stripslashes($t_quotation_val), 2); ?>");
        $("#total-order").html("<?php echo $t_order; ?>");
        $("#total-order-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_val), 2); ?>");
        // $("#total-visit").html("<?php echo $t_visit; ?>");
        // $("#total-complain").html("<?php echo $t_complain; ?>");
        $("#total-invoice").html("<?php echo $t_invoice; ?>");
        $("#total-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value), 2); ?>");
        $("#total-without-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value_withtout_gst), 2); ?>");
    </script>
    <!-- --- milan --- 22-06-2021 --- -->

    <script>
        function genReport(cid) {
            if ($("#datatable_1").find("tbody").find("tr").length >= 1) {
                var rc = encodeURIComponent($("#print_info").html());

                $.ajax({
                    type: "POST",
                    url: "ordersReport_gen_ajax.php",
                    data: '&rc=' + rc,
                    beforeSend: function() {
                        $(".transCover").fadeIn(800);
                        $("#loading").modal('show');
                    },
                    success: function(result) {
                        //alert(result);
                        setTimeout(function() {
                            $(".transCover").fadeOut(100);
                            alert("Report file generated!!");
                            $("#loading").modal('hide');
                            window.open(
                                result,
                                '_blank' // <- This is what makes it open in a new window.
                            );
                            // window.location.href=result;
                        }, 1500);
                    }
                });
            } else {
                toastr.error("Report Can't generated");
            }

        }
    </script>
    <?php require_once 'disconnect.php';  ?>