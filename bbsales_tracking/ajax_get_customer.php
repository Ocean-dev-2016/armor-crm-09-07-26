<?php
$page_id = 400;
$page_slug = 'dashboard';
require_once("connect.php");
// print_r($_REQUEST);exit;
$customer_type         = $_POST["customer_type"];
$selected_value      = $_POST["selected_value"];
if (!isset($customer_id) || empty($customer_id) || $customer_id == "") {
    $selected_value      = $_POST["customer_id"];
    $customer_id      = $_POST["customer_id"];
}

$channel_partner_order = (isset($_POST["channel_partner_order"]) && $_POST["channel_partner_order"] == "1") ? 1 : 0;
$is_channel_partner = ($channel_partner_order == 1);

if ($is_channel_partner) {
    /* Channel Partner Order: only CP executives of selected customer type */
    $where = "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0 AND type_of_executive='" . $customer_type . "'";
} else {
    $where = "type_of_executive ='" . $customer_type . "' AND isDelete=0";
}

if ($_POST["companytype"] != "" && isset($_POST["companytype"])) {
    $where .= " AND type_of_company = " . $_POST["companytype"];
}

if (isset($customer_id) && !empty($customer_id) && $customer_id != "") {
    $where .= " AND id='" . $customer_id . "'";
}

$mode = $_POST['mode'];


if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) // customer and its chain wise order
{
    // for customer panel only
    $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
    if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db) && $is_channel_partner) {
        $where .= " AND id='" . (int) $check_id . "'";
        $selected_value = $check_id;
    } else {
    $get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $check_id . "'", 0);
    if ($get_customer_type == $customer_type)  //super stockist
    {
        $where .= " AND id='" . $check_id . "'";
        $selected_value = $check_id;
    }

    if ($get_customer_type == 1 && $customer_type == 2) {
        $where .= " AND super_stockist_id='" . $check_id . "'";
    } else if ($get_customer_type == 2 && $customer_type == 3) {
        $where .= " AND dealer_distributor_id='" . $check_id . "'";
    }
    }
    // for customer panel only
}

$customer_r = $db->rp_getData("executive", "*", $where, "company_name ASC", 0);
if ($customer_r && mysqli_num_rows($customer_r) > 0) {
?>
    <option value=""><?= $is_channel_partner ? 'Select Channel Partner' : 'Select Customer'; ?></option>
    <?php
    $customer_flag_array = array("0" => "C", "1" => "P");
    while ($customer_d = mysqli_fetch_array($customer_r)) {
        if ($customer_d['price_list_id'] != 0) {
            $price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . $customer_d['price_list_id'] . "'");
        } else {
            $price_list_name = "N/A";
        }

        /*for merchnt export*/
        if ($customer_d['type_of_executive'] == 8) {

            /*if(strtolower(CLIENT_STATE)==strtolower($customer_d['state']))
            {
                $gst_type="(IGST:18%)";
            }*/
            if (strtolower(CLIENT_STATE) == strtolower($customer_d['state'])) {
                $gst_type = "(CGST:0.05%,SGST:0.05%)";
            } else {
                $gst_type = "(IGST:0.1%)";
            }
        }
        /*for merchnt export*/ else {
            if (strtolower(CLIENT_STATE) == strtolower($customer_d['state'])) {
                $gst_type = "(CGST:9%,SGST:9%)";
            } else {
                $gst_type = "(IGST:18%)";
            }
        }

        $shipping_address = $db->rp_getValue("customer_vs_shipping_address", "shipping_address", "customer_id='" . $customer_d['id'] . "' AND isDelete=0 limit 1", 0);



        $customer_type1 = $db->rp_getValue("customer_type", "name", "id='" . $customer_d['type_of_executive'] . "'");

        if ($mode == 'assign_customer') {
    ?>
            <option <?php if ($customer_d['id'] == $selected_value) {
                        echo "selected";
                    }  ?> value="<?php echo $customer_d['id']; ?>">
                <?php echo $customer_d['company_name'] . " - " . $customer_d['cname'] . " (" . $customer_d['main_city'] . ")"; ?></option>
        <?php
        } else {
        ?>
            <option <?php if ($customer_d['id'] == $selected_value) {
                        echo "selected";
                    }  ?> value="<?php echo $customer_d['id']; ?>" data-phone="<?php echo $customer_d['phone'] ?>" data-email="<?php echo $customer_d['email'] ?>" data-address="<?php echo htmlentities($customer_d['address']); ?>" data-state="<?php echo $customer_d['state'] ?>" data-cname="<?= $customer_d['cname'] ?>" data-gstin="<?= $customer_d['gst'] ?>" data-top_category_id="<?= $customer_d['top_category_id'] ?>" data-price-list="<?= $price_list_name; ?>" data-cutomer-type="<?= $customer_type1; ?>" data-gst-type="<?= $gst_type ?>" data-c_id="<?php echo $customer_d['id'] ?>" data-cutomer-type="<?= $customer_type1; ?>" data-shipping-add="<?= htmlentities($shipping_address); ?>" data-billing-add="<?= htmlentities($customer_d['billing_address']); ?>" data-customer_cash_discount="<?= $customer_d['cash_discount'] ?>" data-customer_additional_discount="<?= $customer_d['additional_discount'] ?>" data-booking_place="<?php echo $customer_d['booking_place'] ?>" data-zip="<?php echo $customer_d['zip'] ?>" data-transporter_id="<?php echo $customer_d['transporter_id'] ?>" data-transport_thr="<?php echo $customer_d['transport_by_id'] ?>">
                <?php echo $customer_d['company_name'] . " - " . $customer_d['cname'] . "-" . $customer_d['client_code'] . " - " . $customer_flag_array[$customer_d['customer_flag']]; ?>
            </option>
    <?php
        }
    }
} else {
    ?>
    <option value=""><?= $is_channel_partner ? 'Select Channel Partner' : 'Select Customer'; ?></option>
<?php
}

require_once "disconnect.php";
?>