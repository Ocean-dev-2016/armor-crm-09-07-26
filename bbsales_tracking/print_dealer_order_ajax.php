<?php
$page_id = 565;
$page_slug = 'page_order';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable   = "orders";
$ctable1   = "Orders";
$uid = $_REQUEST['uid'];
$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $ctable_where .= " (
              customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
              company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
              order_no like '%" . $db->clean($_REQUEST['searchName']) . "%'
            ) AND ";
}

if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") {
  $ctable_where .= "  sales_id = '" . $db->clean($_REQUEST['sales_executive_id']) . "' AND";
}
if (isset($_REQUEST['qid']) && $_REQUEST['qid'] != "") {
  $ctable_where .= "  quotation_id = '" . $db->clean($_REQUEST['qid']) . "' AND";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "") {
  $ctable_where .= "  customer_type = '" . $db->clean($_REQUEST['customer_type']) . "' AND";
}
if (isset($_REQUEST['company_name']) && $_REQUEST['company_name'] != "") {
  $ctable_where .= "  customer_id = '" . $db->clean($_REQUEST['company_name']) . "' AND";
}

//for admin login
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
  $ctable_where .= " isDelete=0   AND status!=-1";
} else {
  $ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
}
// for customer login
// else
// {
//   if($_REQUEST['order_type'] && $_REQUEST['uid'])
//   {
//   $ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
//   }
//   else if($_REQUEST['order_type'])
//   {
//     $ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'  AND status!=-1";
//   }
//   else{
//     $ctable_where .= " isDelete=0   AND status!=-1";
//   }
// }

//status
if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL) {
  $ctable_where .= " AND status = '" . $_REQUEST['status'] . "' ";
}

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL) {
  $ctable_where .= " AND type_of_company = '" . $_REQUEST['type_of_company'] . "' ";
}

if (isset($_REQUEST['dispatch_status']) && $_REQUEST['dispatch_status'] != "" && $_REQUEST['dispatch_status'] != NULL) {
  $ctable_where .= " AND dispatch_status = '" . $_REQUEST['dispatch_status'] . "' ";
}
///For ToDate & FromDate
if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && $_REQUEST['ToDate'] != undefined) {
  $ctable_where .= " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
  $ctable_where .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
  // echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode($_REQUEST['df']);

  $date_filter_query_ex = explode(" to ", $date_filter_query);

  $ctable_where .= " AND ( DATE(order_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(order_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL && $_REQUEST['type'] != undefined) {
  if ($_REQUEST['type'] == "channel_partner") {
    $ctable_where .= " AND channel_partner_order_flag=1 ";
  } else if ($_REQUEST['type'] != 100) {
    $ctable_where .= " AND customer_type = '" . $_REQUEST['type'] . "' AND (channel_partner_order_flag=0 OR channel_partner_order_flag IS NULL) ";
  }
}

if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL && $_REQUEST['sales_id'] != undefined) {
  $ctable_where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "' ";
}

$ctable_where .= " AND isDelete=0";

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);

?>
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

<table id="datatable_dealer" class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th colspan="16" class="center">
        <h2>Order History Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
      </th>
    </tr>
    <tr>
      <th>No.</th>
      <th>Order No.</th>
      <th>Approved By</th>
      <th>Quotation No.</th>
      <th>Order Date</th>
      <th>Status</th>
      <th>Type Of Company</th>
      <th>Dispatch Order Status</th>
      <th>Company Name</th>
      <th>Customer Name</th>
      <th>Client Code</th>
      <!-- <th>Person Name</th> -->
      <th>Sales Person Name</th>
      <th>Order Type</th>
      <th style="text-align:right;">Order Amount</th>
      <!--  <th>Transport Name</th>
                <th>Transport By</th>
                <th>Dispatch Date</th>
                <th>Entry Type</th>
                <th>Update Entry Type</th> -->
    </tr>
  </thead>
  <tbody>
    <?php

    if ($ctable_r) {
      $count = 0;
      $sales_name = '';

      $customer_type = array('1' => "Super Stockist", '2' => "Distributor", '3' => "Dealer", 'normal_user' => "Normal Customer");

      $status_array = array('0' => "Pending", '1' => "Approved", '2' => "Dispatched", '3' => "Cancelled");

      while ($ctable_d = mysqli_fetch_array($ctable_r)) {
        $entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
        $orders_status = array("-1" => "Add to Cart", "-2" => "Disapproved", "0" => "Waiting For Approval", "1" => "Waiting For Account Approval", "3" => "Cancelled", "4" => "Account Approved", "5" => "Dispatch", "6" => "Order Complate", "7" => "Pending LR");
        $get_approved_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $ctable_d['approve_by_id'] . "'", 0);
    ?>
        <tr>
          <td><?php echo ++$count; ?></td>
          <td>
            <span class="text-success"><?php echo stripslashes($ctable_d['order_no']); ?>
          </td>
          		<td><?php echo $get_approved_by_name ?></td>
          <td>

            <?php
            if ($ctable_d['quotation_id'] != 0) {
              echo "<a class='text-success' target='_blank' href='quotation_viewer.php?quotation_id=" . $ctable_d['quotation_id'] . "'>" . $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $ctable_d['quotation_id'] . "'") . "</a>";
            }
            ?>

          </td>
          <td><?php echo date('d-m-Y', strtotime($ctable_d['order_date'])); ?></td>
          <td><?php $ctable_d['status']; ?><?php echo $orders_status[$ctable_d['status']]; ?></td>
          <td><?php echo   $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?></td>
          <td>
            <?php echo $db->rp_getValue("dispatch_order_status", "name", "isDelete=0 AND id = '" . $ctable_d['dispatch_status'] . "' ", "", 0); ?>
          </td>
          <?php
          $customer_flag_text = "";
          if ($ctable_d['customer_flag'] == 1) {
            $customer_flag_text = " - P";
          } else if ($ctable_d['customer_flag'] == 0) {
            $customer_flag_text = " - C";
          }
          ?>
          <td><b><?php echo $ctable_d['company_name'] . $customer_flag_text; ?></b></td>
          <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
          <td><?php echo stripslashes($ctable_d['client_code']); ?></td>
          <!-- <td></td> -->
          <?php
          $sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['sales_id'] . "'");
          ?>
          <td>
            <?php if ($sales_name == "") {
              echo "Admin";
            } else {
              echo $sales_name;
            }
            ?>
          </td>
          <td>
            <?php
            if ($ctable_d['customer_type'] == '1') {
              $slug = "Super Stockist";
            } else if ($ctable_d['customer_type'] == '2') {
              $slug = "Distributor";
            } else if ($ctable_d['customer_type'] == '3') {
              $slug = "Dealer";
            } else if ($ctable_d['customer_type'] == '4') {
              $slug = "B2B Customer";
            } else if ($ctable_d['customer_type'] == '6') {
              $slug = "B2C Customer";
            } else if ($ctable_d['customer_type'] == 'normal_user') {
              $slug = "Normal Customer";
            }
            echo stripslashes($slug);
            ?>
          </td>
          <!-- <?php
                $get_brand_id = $db->rp_getValue("order_product_item", "brand_id", "isDelete=0 AND isActive=1 AND order_id='" . $ctable_d['id'] . "'");
                $get_brand_name = $db->rp_getValue("brand", "name", "isDelete=0 AND isActive=1 AND id='" . $get_brand_id . "' ");
                ?>
        <td><?= $get_brand_name ?></td> -->
          <td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_d['grand_total']))); ?></td>

          <?php
          $transport_through_id = $db->rp_getValue("orders", "transport_through", "id='" . $ctable_d['id'] . "'", 0);
          $transport_through = $db->rp_getValue("transport_by", "name", "id='" . $transport_through_id . "'");
          $transport_name_id = $db->rp_getValue("orders", "transport_name", "id='" . $ctable_d['id'] . "'");
          $transport_name =  $db->rp_getValue("transport_master", "name", "transport_by_id='" . $transport_through_id . "' AND id='" . $transport_name_id . "'", 0);
          ?>
          <!--  <td><?php echo $transport_through; ?></td>
        <td><?php echo $transport_name; ?></td>
        <td><?php $dispatch_date =  $db->rp_getValue("dispatch_detail", "dispatch_date", "order_id='" . $ctable_d['id'] . "'", 0);
            if ($dispatch_date == "0000-00-00" || $dispatch_date == "1970-01-01" || $dispatch_date == "") {
              echo "";
            } else {
              echo date('d-m-Y', strtotime($dispatch_date));
            }
            ?>
                </td>
        <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
        <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
        </tr>
    <?php
      }
    }
    ?>
  </tbody>
</table>
<?php require_once "disconnect.php"; ?>