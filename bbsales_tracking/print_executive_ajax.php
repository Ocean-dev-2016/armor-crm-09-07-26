<?php
if (!isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
  $page_id = 616;
  $page_slug = 'prospect_customer';
} else {
  $page_id = 555;
  $page_slug = 'page_executive';
}
// print_r($_REQUEST);exit;
include("connect.php");
$ctable   = "executive";
$Where = "";


if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
  $Where .= "customer_flag=0 AND ";
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
  $Where .= "customer_flag=1 AND ";
}
if (isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName'])) {
  $Query = $_REQUEST['searchName'];
  // $Where.=" (
  //             cname like '%".$db->clean($_REQUEST['searchName'])."%'
  //             OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
  //             OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%' OR gst  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR zip  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%') AND ";

  $Where .= " (
              cname like '%" . $db->clean($_REQUEST['searchName']) . "%'
              OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%'
               OR gst  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR client_code  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' OR zip  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%') AND ";
}

// if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
//   $Query=$_REQUEST['searchName'];
//   $Where.=" (cname like '%".$Query."%' OR company_name like '%".$Query."%' OR phone like '%".$Query."%') AND";
// }

if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "" && $_REQUEST['customer_type'] != NULL) {
  $Where .= " type_of_executive = '" . $_REQUEST['customer_type'] . "' AND ";
}

if (isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company'] != "" && $_REQUEST['type_of_company'] != NULL) {
  $Where .= " type_of_company = '" . $_REQUEST['type_of_company'] . "' AND ";
}

if (isset($_REQUEST['state']) && $_REQUEST['state'] != "" && $_REQUEST['state'] != NULL) {
  $Where .= " state = '" . $_REQUEST['state'] . "' AND ";
}

if (isset($_REQUEST['city']) && $_REQUEST['city'] != "" && $_REQUEST['city'] != NULL) {
  $ctable_where .= " AND city = '" . $_REQUEST['city'] . "'";
  // $city = $_REQUEST['city'];
}
if (isset($_REQUEST['main_city']) && $_REQUEST['main_city'] != "" && $_REQUEST['main_city'] != NULL) {
  $ctable_where .= " AND main_city = '" . $_REQUEST['main_city'] . "'";
  $city = $_REQUEST['main_city'];
}

// if(isset($_REQUEST['zone']) && $_REQUEST['zone']!="" && $_REQUEST['zone']!=NULL)
// {
//   $Where .= " zone = '".$_REQUEST['zone']."' AND";
// }

if (isset($_REQUEST['price_list']) && $_REQUEST['price_list'] != "" && $_REQUEST['price_list'] != NULL) {
  $Where .= " price_list_id = '" . $_REQUEST['price_list'] . "' AND";
}

if (isset($_REQUEST['seid']) && $_REQUEST['seid'] != "" && $_REQUEST['seid'] != NULL) {
  $ctable_where .= " AND seid = '" . $_REQUEST['seid'] . "'";
}


if (isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != "" && $_REQUEST['category_id'] != "undefined" && $_REQUEST['category_id'] != "null") {
  // echo $_REQUEST['category_id'];exit();
  $ctable_where .= " AND category_id IN (" . $_REQUEST['category_id'] . ")  ";
  $category_id = $_REQUEST['category_id'];
}

if (isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id'] != "" && $_REQUEST['top_category_id'] != "undefined" && $_REQUEST['top_category_id'] != "null") {
  $ctable_where .= " AND top_category_id IN (" . $_REQUEST['top_category_id'] . ")  ";
  $top_category_id = $_REQUEST['top_category_id'];
}

// if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL)
// {
//   $executive_id=array();
//   $ctable_area = "class_id=".$_REQUEST['class_name']." AND area_id = '".$_REQUEST['area']."' AND executive_type='1' AND isDelete=0";
//   $area_list=$db->rp_getData("executive_map_area","*",$ctable_area,"",0);
//   while($area_list_d=mysqli_fetch_assoc($area_list))
//   {
//   $executive_id[]=$area_list_d['executive_id'];
//   }
//   $ids=implode(",",$executive_id);
//   $Where .= " AND id IN (".$ids.")";
// }
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
  if ($rights['personal_flag'] == 1) {
    $Where .= " created_by ='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' AND ";
    $customer_type = $db->rp_getValue("sales_executive", "customer_type", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
    $filter_where .= " id IN (" . $customer_type . ") AND ";
  } else {
    if ($rights['all_data_flag'] == 1) {
    } else {
      $customer_type = $db->rp_getValue("sales_executive", "customer_type", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
      //$CustomerType = implode(",", $customer_type);
      $Where .= " type_of_executive IN (" . $customer_type . ") AND ";
      $filter_where .= " id IN (" . $customer_type . ") AND ";
    }
  }
}
$Where .= " isDelete=0 AND id!=-1  ";

// $ctable_r = $db->rp_getData($ctable, "*",$where, "id DESC",1);
$ctable_r = $db->rp_getData($ctable, "*", $Where . $ctable_where, "company_name ASC", 0);
?>
<!-- <style type="text/css">
.table-scrollable 
{
    width: auto;
    height: 810px;
    overflow-x: scroll;
    overflow-y: scroll;
}
</style> -->
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
<table id="datatable_1" class="table table-striped table-bordered table-hover">
  <thead>
    <tr>
      <th colspan="19" class="center">
        <h2>Executive Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
      </th>
    </tr>
    <tr>
      <th> No.</th>
      <th>Type Of Company</th>
      <th>Customer Type</th>
      <th>Price List</th>
      <th>Sales Person</th>
      <th>Client Code</th>
      <th>Firm Name</th>
      <th>Person Name</th>
      <th>Phone</th>
      <!-- <th>Mobile</th> -->
      <!-- <th>WhatsApp</th> -->
      <!--  <th>Credit Limit</th>
      <th>Credit Days</th> -->
      <th>State</th>
      <th>City</th>
      <th>Route</th>
      <th>Pincode</th>
      <th>Category</th>
      <!-- <th>Zone</th>      -->
      <!-- <th>Image path</th>	 -->
      <th>Turnover</th>
      <th>Turnover Year</th>
      <th>Customer Entry Type</th>
      <th>Customer Create Date</th>
      <!-- <th>Entry Type</th>
      <th>Update Entry Type</th> -->
      </
        </thead>
  <tbody>
    <?php
    if (mysqli_num_rows($ctable_r) > 0) {
      $count = 0;
      while ($ctable_d = mysqli_fetch_array($ctable_r)) {
        $entry_type_status = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
    ?>
        <tr>
          <td><?php echo ++$count; ?></td>
          <td><?php echo $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'"); ?></td>
          <td><?php echo $db->rp_getValue("customer_type", "name", "id='" . $ctable_d['type_of_executive'] . "'"); ?></td>
          <td><?= $db->rp_getValue("price_list", "pricelist_name", "id='" . $ctable_d['price_list_id'] . "' AND isDelete=0"); ?></td>
          <td>
            <?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['seid'] . "' AND isDelete=0", 0); ?>
          </td>
          <td><?= $ctable_d['client_code']; ?></td>
          <td><?php echo stripslashes($ctable_d['company_name']); ?>
            </br>

            <?php
            if ($ctable_d['gst'] != "") {
            ?>
              <strong>Gst :</strong>
            <?php
              echo stripslashes($ctable_d['gst']);
            }
            ?>
          </td>
          <td><?php echo stripslashes($ctable_d['cname']); ?></td>
          <td><?php echo stripslashes($ctable_d['phone']); ?></td>
          <!-- <td><?php echo stripslashes($ctable_d['mobile_no1']); ?></td> -->
          <!-- <td><?php echo stripslashes($ctable_d['whatsapp_no']); ?></td> -->
          <!--  <td><?php echo stripslashes($ctable_d['credit_limit']); ?></td>
          <td><?php echo stripslashes($ctable_d['credit_day']); ?></td>
          -->
          <td><?php echo $ctable_d['state']; ?></td>
          <td><?php echo $ctable_d['main_city']; ?></td>
          <td><?php echo $ctable_d['city']; ?></td>
          <td><?php echo $ctable_d['zip']; ?></td>
          <td><?php
              $catid = explode(",", $ctable_d['top_category_id']);
              $cat_name = array();
              for ($j = 0; $j < sizeof($catid); $j++) {

                $cat_name[] = $db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $catid[$j] . "'", 0);
              }
              echo implode($cat_name, ", ");

              ?>
          </td>

          <!-- <td><?php echo $db->rp_getValue("zone", "name", "id='" . $ctable_d['zone'] . "' AND isDelete=0", 0); ?></td> -->

          <!-- <td>
              <?php
              if ($ctable_d['image_path'] != "" && file_exists(SUPER_STOCKIST_A . $ctable_d['image_path'])) {
              ?>
                <img src="<?php echo SUPER_STOCKIST_A . $ctable_d['image_path']; ?>" width="50" />
              <?php
              } else {
                echo "No Image Available.";
              }
              ?>
          </td> -->
          <td><?= $ctable_d['turnover'] ?></td>
          <td><?= $ctable_d['turnover_year'] ?></td>
          <td>
            <?php
            $lead_time = $db->rp_getValue("no_order_inquiry", "created_date", "isDelete=0 AND dealer_id='" . $ctable_d['id'] . "'", 0);
            $date = date('Y-m-d', strtotime($ctable_d['created_date']));
            $customer_order = $db->rp_getValue("orders", "customer_id", "customer_id='" . $ctable_d['id'] . "' AND isDelete=0 AND isActive=1", 0);
            //$convert_customer =$db->rp_getValue("executive","id,customer_flag=0","id='".$customer_order."'",0);
            if ($customer_order) {
              echo "Prospect Order Convert to Customer";
            } else if ($lead_time) {
              echo "Inquiry To Lead";
            } else if ($ctable_d['entry_flag'] == "1") {
              echo "Direct Customer";
            } else if ($ctable_d['entry_flag'] == "5") {
              echo "Sales App";
            }
            ?>
          </td>
          <td><?= date('d-m-Y h:i:s a', strtotime($ctable_d['created_date'])) ?></td>

          <!-- <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
          <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
        </tr>
      <?php
      }
    } else {
      ?>
      <tr>
        <th colspan="15" style="text-align: center;">No Data Found</th>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>
<?php require_once 'disconnect.php';  ?>