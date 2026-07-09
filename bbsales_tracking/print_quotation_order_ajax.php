<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable   = "quotation_detail";
$ctable1   = "Orders";
$uid = $_REQUEST['uid'];
$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
  $ctable_where .= " (
              customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
              company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
              quotation_no like '%" . $db->clean($_REQUEST['searchName']) . "%'
            ) AND ";
}

if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] != "" && $_REQUEST['inquiry_id'] != "undefined") {
  $ctable_where .= "  inquiry_id = '" . $db->clean($_REQUEST['inquiry_id']) . "' AND";
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

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "") {
  $ctable_where .= "  type_of_company = '" . $db->clean($_REQUEST['type_of_company']) . "' AND";
}

//for admin login
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] == 0)
// {
//   $ctable_where .= "    status!=-1 AND";
// }
// else {
/*$ctable_where .= " isDelete=0 AND status!=-1 AND created_by='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";*/
//   $ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "' AND ";
// }
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



if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {

  if ($rights['personal_flag'] == 1) {

    $ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
  } else {


    if ($rights['chain_vise_flag'] == 1) {


      $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

      $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
      if ($get_sales_type == "sales_manager") {
        $sales_executive_type = "Regional Sales Manager";
        $key = "sm_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "area_sales_manager") {
        $sales_executive_type = "National Sales Manager"; //Business Development Manager
        $key = "asm_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "sales_officer") {
        $sales_executive_type = "Area Sales Manager"; //Area Sales Manager
        $key = "so_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else if ($get_sales_type == "sales_executive") {
        $sales_executive_type = "Sales Officer";
        $key = "se_id";
        $WhereCondition .= ' ' . $key . '=' . $check_id;
      } else {
        $WhereCondition .= ' type = "service_engineer"';
      }

      $data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

      $SALEID1 = array();
      if ($data) {
        while ($data_d = mysqli_fetch_assoc($data)) {
          $SALEID1[] = $data_d['id'];
        }
      }
      if (!empty($SALEID1)) {
        $SALEID1 = implode(",", $SALEID1);

        $ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
      } else {
        $ctable_where .= " isDelete=0 AND status!=-1 AND sales_id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
      }
    } else {
      $ctable_where .= " isDelete=0 AND status!=-1";
    }
  }
} else {

  $ctable_where .= " isDelete=0 AND status!=-1";
}

//status
// if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL && $_REQUEST['status']!="undefined")
// {
//  $ctable_where .= "  status = '".$_REQUEST['status']."' AND  ";
// }

if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL && $_REQUEST['status'] != "undefined") {
  $ctable_where .= " AND  status = '" . $_REQUEST['status'] . "'  ";
}
///For ToDate & FromDate
if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && $_REQUEST['ToDate'] != undefined) {
  $ctable_where .= " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
  $ctable_where .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
  //echo $_REQUEST['df'];exit;
  $date_filter_query = urldecode($_REQUEST['df']);

  $date_filter_query_ex = explode(" to ", $date_filter_query);

  $ctable_where .= " AND  ( DATE(quotation_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(quotation_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'   )  ";
}

if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL && $_REQUEST['type'] != "undefined") {
  $ctable_where .= " AND  customer_type = '" . $_REQUEST['type'] . "'  ";
}

if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL && $_REQUEST['sales_id'] != undefined) {
  $ctable_where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "'  ";
}

// $ctable_where .= "  isDelete=0";

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
      <th colspan="15" class="center">
        <h2>Quotation Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
      </th>
    </tr>
    <tr>
      <th>No.</th>
      <th>Inquiry No.</th>
      <th>Quotation No.</th>
      <!-- <th>Revised From <br/> Quotation No.</th> -->
      <th>Quotation Date</th>
      <th>Status</th>
      <th>Type Of Company</th>
      <th>Company Name</th>
      <th>Person Name</th>
      <th>Client Code</th>
      <th>Company Mobile No</th>
      <th>Sales Person Name</th>
      <th>Quotation Type</th>
      <th style="text-align:right;">Order Amount</th>
      <th class="fix-th1">Lost Reason</th>
      <!-- <th>Entry Type</th>
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
    ?>
        <tr>
          <td><?php echo ++$count; ?></td>
          <td><?= "#INQ/" . $ctable_d['inquiry_id']; ?></td>
          <td><span class="text-success"><?php echo stripslashes($ctable_d['quotation_no']); ?></span></td>

          <!-- <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_d['refrence_id'] ?>"><span class="text-success"><?php echo $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $ctable_d['refrence_id'] . "'"); ?></span></a></td> -->


          <td><?php echo date('d-m-Y', strtotime($ctable_d['quotation_date'])); ?></td>
          <?php
          if ($ctable_d['status'] == -2) {
            $status = "Disapproved";
          } else if ($ctable_d['status'] == 0) {
            $status = "Pending";
          } else if ($ctable_d['status'] == 1) {
            // $status = "Order Generated";
            $status = "Approved";
          } else if ($ctable_d['status'] == 3) {
            // $status="Cancelled <br><b>Reason For Cancel</b><br/><span class='text-danger'>".$ctable_d['reason_of_cancel_order']."</span>";
            $status = "Cancelled";
          } else if ($ctable_d['status'] == -1) {
            $status = "Add to Cart";
          } else if ($ctable_d['status'] == 4) {
            $status = "Order Generated";
          }
          $customer_flag = "";
          if ($ctable_d['customer_flag'] == 1) {
            $customer_flag = " - P";
          } else if ($ctable_d['customer_flag'] == 0) {
            $customer_flag = " - C";
          }
          ?>
          <td><?php $ctable_d['status']; ?><?php echo stripslashes($status); ?></td>
          <td><?php echo $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?></td>
          <td><?php echo $ctable_d['company_name'] . $customer_flag; ?></td>
          <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
          <td><?php echo stripslashes($ctable_d['client_code']); ?></td>
          <td><?php echo stripslashes($ctable_d['mobile_no']); ?><?php echo $ctable_d['contact_number']; ?></td>
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
            /*if($ctable_d['customer_type']=='1')
                  {
                    $slug="Super Stockist";
                  }
                  else if($ctable_d['customer_type']=='2')
                  {
                    $slug="Distributor";
                  }
                  else if($ctable_d['customer_type']=='3')
                  {
                    $slug="Dealer";
                  }
                  else if($ctable_d['customer_type']=='normal_user')
                  {
                    $slug="Normal Customer";
                  }*/
            echo stripslashes($db->rp_getValue("customer_type", "name", "id='" . $ctable_d['customer_type'] . "'"));
            ?>
          </td>
          <!-- <?php
                $get_brand_id = $db->rp_getValue("order_product_item", "brand_id", "isDelete=0 AND isActive=1 AND order_id='" . $ctable_d['id'] . "'");
                $get_brand_name = $db->rp_getValue("brand", "name", "isDelete=0 AND isActive=1 AND id='" . $get_brand_id . "' ");
                ?>
        <td><?= $get_brand_name ?></td> -->
          <td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_d['grand_total']))); ?></td>
          <td><?php echo $ctable_d['lost_reason']; ?></td>
          <!-- <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
           <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
        </tr>
    <?php
      }
    }
    ?>
  </tbody>
</table>
<?php require_once "disconnect.php"; ?>