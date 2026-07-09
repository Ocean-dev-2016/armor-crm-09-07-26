<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect_in.php");
// include("../include/no_to_word.php");

// echo $_REQUEST['customer_type'];exit();

$ctable   = "orders";
$ctable1  = "Orders";
$ctable_where = "";
$area = $_REQUEST['area'];
$ctable_where .= " isDelete=0 AND";

// Get the total number of rows in the table

if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != "null") {
  //$ctable_where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
  $ctable_where .= "  sales_id=" . $_REQUEST['sales_id'] . " AND";
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL && $_REQUEST['state'] != "null") {
  $state_r = $db->rp_getData("state", "name", "id in (" . $_REQUEST['state'] . ")", "", 0);
  while ($state_d = mysqli_fetch_array($state_r)) {
    $state_str[] = "'" . $state_d['name'] . "'";
  }
  $class_str = implode(",", $state_str);
  $ctable_where .= " state IN (" . $class_str . ") AND ";
}
//for area----//
if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL && $_REQUEST['city'] != "null") {
  $city_r = $db->rp_getData("city", "name", "id in (" . $_REQUEST['city'] . ")", "", 0);
  while ($city_d = mysqli_fetch_array($city_r)) {
    $city_str[] = "'" . $city_d['name'] . "'";
  }
  // echo implode(",",$city_str);exit;
  $ctable_where .= " main_city IN (" . implode(",", $city_str) . ") AND ";
}

if (isset($_REQUEST['route']) && $_REQUEST['route'] != "" && $_REQUEST['route'] != NULL && $_REQUEST['route'] != "null") {
  $area_r = $db->rp_getData("area", "name", "id in (" . $_REQUEST['route'] . ")", "", 0);
  while ($area_d = mysqli_fetch_array($area_r)) {
    $area_str[] = "'" . $area_d['name'] . "'";
  }
  // echo implode(",",$area_str);exit;
  $ctable_where .= " city IN (" . implode(",", $area_str) . ") AND ";
}


if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != "null") {

  if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != "null") {
  } else {
    if ($_REQUEST['customer_type'] != 0) {
      $ctable_where .= " customer_type=" . $_REQUEST['customer_type'] . " AND ";
    }
  }
}


if (isset($_REQUEST['date']) && $_REQUEST['date'] != "" && $_REQUEST['date'] != "null") {
  //$ctable_where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
  $ctable_where .= "  ( DATE(order_date)='" . date("Y-m-d", strtotime($_REQUEST['date'])) . "') AND  ";
}


if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != "null") {


  $get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $_REQUEST['customer_id'] . "'");

  if ($get_customer_type == 2) {
    $get_retailer_customer = $db->rp_getData("executive", "*", "isDelete=0 AND dealer_distributor_id='" . $_REQUEST['customer_id'] . "'", "", 0);

    $retailer_id = array();
    while ($retailer_ids_d = mysqli_fetch_assoc($get_retailer_customer)) {
      $retailer_id[] = $retailer_ids_d['id'];
    }

    // print_r($retailer_id);exit;
    $retailer_id = implode(",", $retailer_id);

    if ($_REQUEST['customer_type'] == 2) {
      $dis_id = $_REQUEST['customer_id'];
      // $ctable_where.=" customer_id IN(".$dis_id.") AND ";
    }


    $ctable_where .= " customer_id IN(" . $retailer_id ./*",".$dis_id.*/ ") AND ";



    $total_retailer_count = $db->rp_getTotalRecord("executive", "isDelete=0 AND dealer_distributor_id='" . $_REQUEST['customer_id'] . "'", 0);

    $total_visit_count = $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id IN(" . $retailer_id . ")");
    $total_order_count = $db->rp_getTotalRecord("orders", "isDelete=0 AND customer_id IN(" . $retailer_id . ") GROUP BY customer_id", 0);
  } else if ($get_customer_type == 1) {
    $get_retailer_customer = $db->rp_getData("executive", "*", "isDelete=0 AND super_stockist_id='" . $_REQUEST['customer_id'] . "' AND type_of_executive=2", "", 0);

    $distributor_id = array();
    while ($distributor_ids_d = mysqli_fetch_assoc($get_retailer_customer)) {
      $distributor_id[] = $distributor_ids_d['id'];
    }

    // print_r($retailer_id);exit;
    $distributor_id = implode(",", $distributor_id);



    $ctable_where .= " customer_id IN(" . $distributor_id . ")  AND ";




    $total_retailer_count = $db->rp_getTotalRecord("executive", "isDelete=0 AND super_stockist_id='" . $_REQUEST['customer_id'] . "' AND type_of_executive=2", 0);

    $total_visit_count = $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id IN(" . $distributor_id . ")");
    $total_order_count = $db->rp_getTotalRecord("orders", "isDelete=0 AND customer_id IN(" . $distributor_id . ") GROUP BY customer_id");
  } else {
    $ctable_where .= " customer_id=" . $_REQUEST['customer_id'] . " AND ";
  }
}




$ctable_where .= " isDelete=0 AND status!=-1";

$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);

// $total_order_count=$db->rp_getTotalRecord("orders",$ctable_where." GROUP BY customer_id");
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

<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
  <thead>
    <tr>
      <th colspan="12" class="center">
        <h2>Order Report - <?= $_REQUEST['date'] . "(" . $db->rp_getValue("sales_executive", "name", "id='" . $_REQUEST['sales_id'] . "'") . ")" ?></h2>
      </th>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="right"><?php $get_total_sum = $db->rp_getValue("orders", "SUM(grand_total)", $ctable_where);
                        echo stripslashes(CURR . $db->rp_num($get_total_sum));

                        ?></td>
      <td></td>

    </tr>
    <tr class="tr">
      <th class="th">Sr. No.</th>
      <th class="th">Order No.</th>
      <th class="th">Company Name</th>
      <th class="th">Person Name</th>
      <th class="th">Client Code</th>
      <th class="th">Sales Person Name</th>
      <th class="th">Customer Type</th>
      <th class="th">State</th>
      <th class="th">City</th>
      <th class="th">Order Date</th>
      <th class="th">Order Amount</th>
      <th class="th">Product</th>

    </tr>
  </thead>
  <tbody>
    <?php
    if (mysqli_num_rows($ctable_r) > 0) {
      $count = 1;
      $sales_name = '';
      while ($ctable_d = mysqli_fetch_array($ctable_r)) {
        $customer = $db->rp_getValue('executive', 'isActive', "id=" . $ctable_d['customer_id'] . "", 0);
        if ($customer == 0) {
          continue;
        }
        $customer_flag_text = "";
        if ($ctable_d['customer_flag'] == 1) {
          $customer_flag_text = " - P";
        } else if ($ctable_d['customer_flag'] == 0) {
          $customer_flag_text = " - C";
        }
    ?>
        <tr class="tr">
          <td class="td" style="width:5px;"><?php echo $count++; ?></td>
          <td class="td"><?php echo stripslashes($ctable_d['order_no']); ?></td>
          <td><?php echo $db->rp_getValue('executive', 'company_name', "id=" . $ctable_d['customer_id'] . "", 0) . $customer_flag_text; ?></td>
          <td class="td"><?php echo stripslashes($ctable_d['customer_name']); ?></td>
          <td class="td"><?php echo stripslashes($ctable_d['client_code']); ?></td>
          <?php
          $sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['sales_id'] . "'");
          ?>
          <td class="td"><?php if ($sales_name == "") {
                            echo "--";
                          } else {
                            echo $sales_name;
                          }
                          ?></td>
          <td class="td"> <?php
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
                          }
                          echo stripslashes($slug); ?></td>
          <!-- <td><?php echo $db->rp_getValue("class", "name", "id='" . $ctable_d['class_id'] . "'"); ?></td> -->
          <td><?php echo $db->rp_getValue('executive', 'state', "id=" . $ctable_d['customer_id'] . "", 0); ?></td>
          <td><?php echo $db->rp_getValue('executive', 'city', "id=" . $ctable_d['customer_id'] . "", 0); ?></td>
          <td class="td"><?php echo date('d-m-Y', strtotime($ctable_d['order_date'])); ?></td>
          <td align="right" class="td"><?php echo stripslashes('₹' . $db->rp_num($ctable_d['grand_total'])); ?></td>

          <?php
          $total_value += $ctable_d['grand_total'];
          ?>
          <td class="td">
            <?php
            //$order_id=$_REQUEST['id'];
            $ctable_where_p = "order_id='" . $ctable_d['id'] . "' AND isDelete=0";
            $ctable_p = $db->rp_getData("order_product_item", "*", $ctable_where_p, "", 0);

            ?>
            <table style="width:300px;" class="table1 table-bordered table-striped dataTable">
              <thead>
                <?php
                if ($ctable_p) {
                ?>
                  <tr class="tr1">
                    <th class="th1">Product Name</th>
                    <th class="th1">QTY</th>
                    <th class="th1">Price</th>

                  </tr>
              </thead>
              <tbody>
                <?php
                  $total = 0;
                  while ($ctable_pro = mysqli_fetch_array($ctable_p)) {
                ?>
                  <tr class="tr1">
                    <td class="td1"><?php
                                    $pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro['pro_id'] . "'", 0);
                                    $weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro['weight_id'] . "'");
                                    $product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $ctable_pro['pro_id'] . "' AND weight_id='" . $ctable_pro['weight_id'] . "'", 0);
                                    echo "<b>#".$product_code."</b>-".$pro_name . "(" . $weight_name . ")";  ?></td>
                    <td class="td1" align="right">
                      <?php
                      $get_weight = $db->rp_getValue("product_weight_price", "weight_in_kg", "isDelete=0 AND product_id='" . $ctable_pro['pro_id'] . "' AND weight_id='" . $ctable_pro['weight_id'] . "'", 0);
                      echo $total_weight_count =/*$get_weight**/ $ctable_pro['pro_qty'];

                      /*echo  $ctable_pro['pro_qty'];*/ ?></td>
                    <td class="td1" align="right"><?php echo "Rs. " . $db->rp_num($ctable_pro['unitprice']);  ?></td>

                  </tr>

                <?php
                    $total += $ctable_pro['totalprice'];

                    $total_qty += $total_weight_count;

                    // $total+=$ctable_pro['totalprice'];
                  }
                ?>
                <tr>
                  <td><strong>Total</strong></td>
                  <td align="right"><strong><?= $total_qty ?></strong></td>
                  <td align="right"><strong><?= $total ?></strong></td>
                </tr>
              <?php

                  $total_qty = 0;
                } else {

              ?>
                <tr>
                  <td class="tr1" colspan="3" style="text-align:center;">No Product Order</td>

                </tr>
              <?php
                }
              ?>

              </tbody>
            </table>
          </td>

        </tr>
    <?php
      }
    }

    ?>
  </tbody>
</table>


<br>

<!--    <?php

        if ($_REQUEST['customer_type'] != "0" && $_REQUEST['customer_type'] == 2 && $_REQUEST['customer_id'] != "") {

        ?>

    
  <center><h2><strong>Route Wise Call Summary</strong></h2></center>


  <table class="table1 table-bordered table-striped dataTable">
    <thead>
      <tr class="tr">
          <th class="th"><strong>Route Name</strong></th>
          <th class="th" align="center" style="text-align: center;"><strong>TC</strong></th>
          <th class="th" align="center" style="text-align: center;"><strong>Visit</strong></th>
          <th class="th" style="text-align: center;"><strong>PC</strong></th>
          <th class="th" style="text-align: center;"><strong>NC</strong></th>
                       
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?=
            $route_name = $db->rp_getValue("executive", "city", "isDelete=0 AND id='" . $_REQUEST['customer_id'] . "'"); ?></td>
        <td style="text-align: center;"><?= $total_retailer_count ?></td>
        <td style="text-align: center;"><?= $total_visit_count ?></td>
        <td style="text-align: center;" align="center" style="text-align: center;"><?= $total_order_count ?></td>
        <td style="text-align: center;"><?= $total_nc_count = $total_retailer_count - $total_order_count ?></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="1" align="center"><strong>Total Call</strong></td>
        <td colspan="1" align="center"><strong><?= $total_retailer_count ?></strong></td>

        <td align="center" style="text-align: center;"><strong></strong></td>
        <td align="center" style="text-align: center;"><strong></strong></td>
        <td align="center" style="text-align: center;"><strong></strong></td>
      </tr>
    </tfoot>
  </table>





    <?php

        }

    ?> -->






<br>

<?php

if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != "null") {
  //$ctable_where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
  $ctable_where_order .= "  sales_id=" . $_REQUEST['sales_id'] . " AND";
}

if (isset($_REQUEST['date']) && $_REQUEST['date'] != "" && $_REQUEST['date'] != "null") {
  //$ctable_where_order .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
  $ctable_where_order .= "  ( DATE(order_date)='" . date("Y-m-d", strtotime($_REQUEST['date'])) . "') AND ";
}

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != "null") {
  if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != "null") {
  } else {
    if ($_REQUEST['customer_type'] != 0) {
      $ctable_where_order .= " customer_type=" . $_REQUEST['customer_type'] . " AND ";
    }
  }
}


if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != "null") {


  $get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $_REQUEST['customer_id'] . "'");

  if ($get_customer_type == 2) {
    $get_retailer_customer = $db->rp_getData("executive", "*", "isDelete=0 AND dealer_distributor_id='" . $_REQUEST['customer_id'] . "'", "", 0);

    $retailer_id = array();
    while ($retailer_ids_d = mysqli_fetch_assoc($get_retailer_customer)) {
      $retailer_id[] = $retailer_ids_d['id'];
    }

    // print_r($retailer_id);exit;
    $retailer_id = implode(",", $retailer_id);


    if ($_REQUEST['customer_type'] == 2) {
      $dis_id = $_REQUEST['customer_id'];
      // $ctable_where.=" customer_id IN(".$dis_id.") AND ";
    }


    $ctable_where_order .= " customer_id IN(" . $retailer_id ./*",".$dis_id.*/ ") AND ";



    $total_retailer_count = $db->rp_getTotalRecord("executive", "isDelete=0 AND dealer_distributor_id='" . $_REQUEST['customer_id'] . "'", 0);

    $total_visit_count = $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id IN(" . $retailer_id . ")");
  } else if ($get_customer_type == 1) {
    $get_retailer_customer = $db->rp_getData("executive", "*", "isDelete=0 AND super_stockist_id='" . $_REQUEST['customer_id'] . "' AND type_of_executive=2", "", 0);

    $distributor_id = array();
    while ($distributor_ids_d = mysqli_fetch_assoc($get_retailer_customer)) {
      $distributor_id[] = $distributor_ids_d['id'];
    }

    // print_r($retailer_id);exit;
    $distributor_id = implode(",", $distributor_id);



    $ctable_where .= " customer_id IN(" . $distributor_id . ") AND ";




    $total_retailer_count = $db->rp_getTotalRecord("executive", "isDelete=0 AND super_stockist_id='" . $_REQUEST['customer_id'] . "' AND type_of_executive=2", 0);

    $total_visit_count = $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id IN(" . $distributor_id . ")");
    $total_order_count = $db->rp_getTotalRecord("orders", "isDelete=0 AND customer_id IN(" . $distributor_id . ") GROUP BY customer_id");
  } else {
    $ctable_where_order .= " customer_id=" . $_REQUEST['customer_id'] . " AND ";
  }
}

$ctable_where_order .= " isDelete=0 ";

$get_order_data_search = $db->rp_getData("orders", "*", $ctable_where_order, "", 0);

$orders_ids = array();
while ($get_order_data_search_d = mysqli_fetch_assoc($get_order_data_search)) {

  $orders_ids[] = $get_order_data_search_d['id'];
}
// print_r($orders_ids);exit();
$orders_ids = implode(',', $orders_ids);




//$order_id=$_REQUEST['id'];
$ctable_where_sales = " order_id IN(" . $orders_ids . ") AND isDelete=0 Group By pro_id,weight_id";
$ctable_p_sales = $db->rp_getData("order_product_item", "*", $ctable_where_sales, "", 0);

?>
<table class="table1 table-bordered table-striped dataTable">
  <thead>
    <?php
    if ($ctable_p_sales) {
    ?>
      <tr>
        <th class="th" colspan="3">
          <h3>Product Wise Sales Summary</h3>
        </th>
      </tr>
      <tr class="tr">
        <th class="th">Category</th>
        <th class="th">Product Name</th>
        <th class="th" align="center" style="text-align: center;">Total Sale QTY</th>

      </tr>
  </thead>


  <tbody>
    <?php
      $total = 0;
      while ($ctable_pro_sales = mysqli_fetch_array($ctable_p_sales)) {
    ?>
      <tr class="tr1">
        <td class="td"><?php

                        $pro_cat = $db->rp_getValue("category_master", "name", "id='" . $ctable_pro_sales['cat_id'] . "'", 0);
                        $pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro_sales['pro_id'] . "'", 0);
                        $weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro_sales['weight_id'] . "'");


                        echo $pro_cat;

                        // echo $pro_name."(".$weight_name.")";  
                        ?></td>
        <td class="td" align="left">
          <?php


          $pro_name = $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $ctable_pro_sales['pro_id'] . "'", 0);
          $weight_name = $db->rp_getValue("weight", "name", "isDelete=0 AND id='" . $ctable_pro_sales['weight_id'] . "'");
          $product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $ctable_pro_sales['pro_id'] . "' AND weight_id='" . $ctable_pro_sales['weight_id'] . "'", 0);

          echo "<b>#".$product_code."</b>-".$pro_name . "(" . $weight_name . ")";





          /*echo  $ctable_pro_sales['pro_qty'];*/ ?></td>
        <td class="td" align="center"><?php


                                      $get_total_qty = $db->rp_getValue("order_product_item", "SUM(pro_qty)", "isDelete=0 AND pro_id='" . $ctable_pro_sales['pro_id'] . "' AND weight_id=" . $ctable_pro_sales['weight_id'] . " AND (order_id IN(" . $orders_ids . ")) GROUP By pro_id,weight_id", 0);

                                      // echo $get_total_qty;

                                      $get_total_kg = $get_total_qty
                                        /**$ctable_pro_sales['weight']*/
                                      ;

                                      echo $get_total_kg;

                                      ?></td>

      </tr>

    <?php


        // $total+=$ctable_pro['totalprice'];
      }
    ?>

  <?php
    } else {

  ?>
    <tr>
      <td class="tr1" colspan="3" style="text-align:center;">No Product Order</td>

    </tr>
  <?php
    }
  ?>

  </tbody>
</table>
<?php require_once("disconnect.php"); ?>