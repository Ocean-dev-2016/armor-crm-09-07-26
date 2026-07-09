<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");

$ntw = new NumToWord_RP;
$quotation_id    = $_REQUEST['quotation_id'];
$cart_detail_r     = $db->rp_getData("quotation_detail", "*", "id='" . $quotation_id . "'", "", 0);
$cart_detail_d     = mysqli_fetch_assoc($cart_detail_r);
$order_date = ($cart_detail_d['order_date'] != "0000-00-00 00:00:00") ? date("d-m-Y", strtotime($cart_detail_d['order_date'])) : "";
$type_of_customer = $db->rp_getValue("executive", "type_of_executive", "id =  '" . $cart_detail_d['customer_id'] . "' ", 0);
if ($type_of_customer == 3) {
    $customer_id = $db->rp_getValue("executive", "dealer_distributor_id", "id= '" . $cart_detail_d['customer_id'] . "' ", 0);
} else if ($type_of_customer == 2) {
    $customer_id = $db->rp_getValue("executive", "super_stockist_id", "id = '" . $cart_detail_d['customer_id'] . "' ");
} else {
    $customer_id = "";
}

if (isset($customer_id) && !empty($customer_id)) {
    $customer_address = $db->rp_getData("executive", "address,company_name", "id = '$customer_id' ");
    $customer_address_d = mysqli_fetch_assoc($customer_address);
}

// if(isset($_REQUEST['quotation_id']) && !empty($_REQUEST['quotation_id']))
// {
// 	$cat_id = $db->rp_getValue("quotation_product_item","top_cat_id","quotation_id = '".$_REQUEST['quotation_id']."' ",0);
// }
// if ($cat_id == 1) 
// {
// 	$header_image = '../images/category_sheetal_icecream.jpg';
// }
// else if($cat_id == 2)
// {
// 	$header_image = '../images/category_jadore_ice_cream.jpg';
// }
// else
// {
// 	$header_image = '../images/sheetal_ice_creame.jpg';
// }

$company_detail_r = $db->rp_getData("company_master", "*", "id='" . $cart_detail_d['type_of_company'] . "' AND isDelete=0", "", 0);

$company_detail_d = mysqli_fetch_assoc($company_detail_r);

?>
<html>

<head>
    <style>
        .mainDiv,
        table {
            border: 1px solid #595959;
            border-collapse: collapse;
            font-size: 13px;
            /*			width:250mm!important;*/
            background-color: #FFF;
            margin: auto;
            padding: auto;
        }

        table,
        td,
        th {
            border: 1px solid #595959;
        }

        td,
        th {
            padding: 5px;
            height: 18px;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .no-border-left {
            border-left: hidden;
        }

        .no-border-right {
            border-right: hidden;
        }

        .no-border-bottom {
            border-bottom: hidden !important;
        }

        .no-border-top {
            border-top: hidden !important;
        }

        .border td {
            border-bottom: hidden !important;
        }

        .color {
            background: #D3D3D3;
        }

        .font-size td {
            font-size: 15px !important;
        }

        .image-width {
            width: 10% !important;
            min-width: 10% !important;
            max-width: 10% !important;
        }

        .border-r-width {
            border-right-width: 5px;
        }

        .border-gray {
            border-right-color: #E5E5E5;
        }

        .border-blue {
            border-right-color: <?= VIEW_COLOR ?>;
        }

        .vertical-top {
            vertical-align: top;
        }

        .height-5 {
            height: 5px;
        }

        .bg-gray {
            background-color: #E5E5E5 !important;
        }

        .font-13 {
            font-size: 13px !important;
        }

        .headerBorder {
            border: 22px solid #eb268f;
            border-bottom: none;
            border-right: none;
            border-left: none;
        }

        .main-container {
            /*border: 1px solid #595959;*/
            padding: 40px;
            /* Add space around the content */
            width: 100% !important;
            background-color: #FFF;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <table style="border: 1px solid #595959;border-collapse: collapse;">
            <tbody>
                <tr>
                    <td>
                        <?php
                        if (isset($company_detail_d['image_path']) && $company_detail_d['image_path'] != "") {
                        ?>
                            <img style="width: 933px;height: 184px;padding: 0px !important;" src="<?= HEADER_A . $company_detail_d['image_path'] ?>">
                        <?php
                        } else {
                        ?>
                            <img style="width: 933px;height: 184px;padding: 0px !important;" src="../images/craftbox_header.jpg">
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr style="background-color: <?= VIEW_COLOR ?>;">
                    <td colspan="16" align="center"><b>Rate Confirmation Quotation</b></td>
                </tr>
            </tbody>
        </table>
        <table style="width:250mm!important;">
            <tbody class="<?= $cl; ?>">
                <!-- <tr class="headerBorder"> -->
                <!-- <td colspan="6" class="no-border-right" style="background: #f1f1f1;padding: 0px !important;">
							<img style="width: 100%;padding: 0px !important;"  src="<?= $header_image; ?>">
						</td>
						<td colspan="6" class="" style="font-size: 27px;background: #f1f1f1;">
							<?php
                            if ($customer_address) {
                            ?>
									<h2><strong><?= $customer_address_d['company_name']; ?></strong></h2>
									<p class="font-13">
										<i class="fa fa-location-arrow"></i> <?= $customer_address_d['address'] ?>
									</p>	
									<?php
                                }
                                    ?> -->

                <!-- 	<strong style="color: #eb268f;"><?php echo CLIENT_BRAND_NAME ?></strong> -->
                <!-- <br>
							<p class="font-13">
								<i class="fa fa-location-arrow"></i> <?= FACTORY_ADDRESS ?>
								<br>
								<i class="fa fa-envelope"></i> info@sheetalicecream.com <i class="fa fa-globe"></i> www.scplco.com
							<br>
							<strong>CIN: L15205GJ2013PLC077205</strong> -->
                <!-- <i class="fa fa-location-arrow"></i><strong> Office Address : </strong><?= CLIENT_ADDRESS ?>
								<br> -->
                <!-- <i class="fa fa-phone"></i> <?= OFFICE_PHONE ?><br> <?= OFFICE_EMAIL ?>  -->
                <!-- </p>
						</td>
					</tr> -->

                <!-- <tr>
						<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
					</tr> -->
                <!-- <tr>
						<td colspan="5" class="no-border-right no-border-bottom bg-gray"></td>
						<td colspan="5" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Quotation</b></td>
						<td colspan="6" class="no-border-bottom bg-gray"></td>
					</tr> -->
                <!-- <tr>
						<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
					</tr> -->

                <tr>
                    <td colspan="8" rowspan="4">
                        <h4 style="font-weight: 600;text-transform: uppercase;">
                            <?php
                            if (isset($company_detail_d['name']) && $company_detail_d['name'] != "") {
                                echo $company_detail_d['name'];
                            } else {
                                echo CLIENT_NAME;
                            }
                            ?>
                        </h4>
                        <p style="margin:0">
                            <?php
                            if (isset($company_detail_d['address']) && $company_detail_d['address'] != "") {
                                echo html_entity_decode($company_detail_d['address']);
                            } else {
                                echo CLIENT_ADDRESS;
                            }
                            ?>
                        </p>
                        <p style="margin:0"><?= OFFICE_PHONE; ?></p>
                        <p style="margin:0"><?= OFFICE_EMAIL; ?></p>
                        <!-- <p style="margin:0"><strong>CIN : </strong></p> -->
                        <p style="margin:0"><strong>GSTIN / UIN : </strong>
                            <?php
                            if (isset($company_detail_d['gst']) && $company_detail_d['gst'] != "") {
                                echo $company_detail_d['gst'];
                            } else {
                                echo GST_No;
                            }
                            ?>
                        </p>
                        <p style="margin:0"><strong>PAN No. : </strong>
                            <?php
                            if (isset($company_detail_d['pan_crad']) && $company_detail_d['pan_crad'] != "") {
                                echo $company_detail_d['pan_crad'];
                            } else {
                                echo CLIENT_PANNO;
                            }
                            ?>
                        </p>
                    </td>
                    <td colspan="8"><b>Quotation No. : </b><?= $cart_detail_d['quotation_no'] ?></td>
                </tr>
                <tr>
                    <td colspan="8"><b>Quotation Date : </b>
                        <?php
                        if ($cart_detail_d['quotation_date'] != "0000-00-00") {
                            echo date('d-M-Y', strtotime($cart_detail_d['quotation_date']));
                        }
                        // else if($cart_detail_d['quotation_date']=="0000-00-00")
                        // {
                        // 	echo "";
                        // }
                        else {
                            echo "";
                        }
                        ?>
                    </td>

                </tr>
                <tr>
                    <td colspan="8" class="no-border-bottom"><!--<b>Customer Quotation No. : </b><?= $cart_detail_d['quotation_no'] ?>--></td>
                </tr>
                <!-- <tr>
						<td colspan="8"><b>Payment Terms : </b></td>
					</tr> -->
                <tr>
                    <td colspan="8" class="no-border-bottom"><!-- <b>Terms of Delivery : </b> --></td>
                </tr>
                <tr>
                    <td colspan="8">
                        Buyer
                        <h5 style="font-weight: 600;text-transform: uppercase;"><strong><?php echo $cart_detail_d['company_name']; ?> (<?= $cart_detail_d['customer_name'] ?>)</strong></h5>
                        <p style="margin:0"><?php echo wordwrap($cart_detail_d['address'], 40, "<br>\n") . "  <br/>" . $cart_detail_d['city'] . " , " . $cart_detail_d['state'] . " , " . $cart_detail_d['country'] ?></p>
                        <p style="margin:0"><strong>Pincode : <?= $cart_detail_d['zip']; ?></strong></p>
                        <p style="margin:0"><strong>Mobile No. : </strong><?= $cart_detail_d['contact_number'] ?></p>
                        <p style="margin:0"><strong>Email : </strong><?= $cart_detail_d['email'] ?></p>
                        <p style="margin:0"><strong>GSTIN / UIN : </strong><?= $cart_detail_d['gst'] ?></p>
                    </td>
                    <td colspan="8"> </td>
                </tr>
            </tbody>
        </table>
        <table style="width:250mm!important;">
            <tbody>
                <tr class="text-center" style="background-color: <?= VIEW_COLOR ?>;">
                    <th colspan="1" class="text-center">SR</th>
                    <th colspan="1" class="image-width text-center">Image</th>
                    <th colspan="5" class="text-center">Description of Goods</th>
                    <th colspan="" class="text-center">Brand<br />Name</th>
                    <th colspan="2" class="text-center">Weight (in kg)</th>
                    <!-- <th colspan="2" class="text-center">Qty</th> -->
                    <th colspan="3" class="text-center">Qty</th>
                    <!-- <th colspan="3" class="text-center">Price</th> -->
                    <!-- <th colspan="3" class="text-center">Discount</th> -->
                    <!-- <th colspan="3" class="text-center">Alt. Qty</th> -->
                    <th colspan="3" class="text-center">Rate</th>
                    <th colspan="" class="text-center">Total Amount</th>
                </tr>
                <?php
                $ITEMS = array();
                $items1 = $db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "'", "");
                while ($item1 = mysqli_fetch_assoc($items1)) {
                    $item1['display_order'] = $db->rp_getValue("product", "display_order", "id='" . $item1['pro_id'] . "' AND isDelete=0");
                    $item1['weight_display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $item1['weight_id'] . "' AND isDelete=0");

                    $item1['image_path'] = $db->rp_getValue("product", "image_path", "id='" . $item1['pro_id'] . "' AND 
							isDelete=0", "", 0);
                    if ($item1['image_path'] != "") {
                        $img = SITEURL . PRODUCT . $item1['image_path'];
                    } else {
                        $img = SITEURL . "images/no_image_found.jpg";
                    }

                    $ITEMS[] = $item1;
                }
                if ($items1) {
                    $count = 0;
                    foreach ($ITEMS as $item) {
                        $pro_name = $db->rp_getValue("product", "name", "id='" . $item['pro_id'] . "' AND isDelete=0");
                        $size = $db->rp_getValue("weight", "name", "id='" . $item['weight_id'] . "' AND isDelete=0");
                        $product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'", 0);
                        $unit_id = $db->rp_getValue("product", "display_unit", "id='" . $item['pro_id'] . "' AND isDelete=0");
                        $unit_name = $db->rp_getValue("unit", "name", "id='" . $unit_id . "' AND isDelete=0");

                        $hsncode = $db->rp_getValue("product", "hsn_code", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);

                        $count++;

                        if ($cart_detail_d['currency_code'] == 1) {
                            $currency = CURR;
                        } else if ($cart_detail_d['currency_code'] == 2) {
                            $currency = DOLLAR;
                        }

                        $totalproqty += $item['pro_qty'];
                        $totalprice1 += $item['totalprice'];
                ?>
                        <tr>
                            <td colspan="1" class="text-center srno"><strong><?php echo $count; ?></strong></td>
                            <?php
                            if ($item['image_path'] != "") {
                            ?>
                                <td colspan="1" class="image-width text-center"><img style="width: 50px;" src="<?php echo SITEURL . PRODUCT . $item['image_path'] ?>"></td>
                            <?php
                            } else {
                            ?>
                                <td colspan="1" class="image-width text-center"><img style="width: 50px;" src="<?php echo SITEURL . PRODUCT . 'default.png' ?>"></td>
                            <?php
                            }
                            ?>
                            <td colspan="5" class="model" style="position: relative;"><?php if ($item['weight_id'] != -1) {
                                                                                            echo $pro_name . " - " . $size;
                                                                                        } else {
                                                                                            echo $pro_name;
                                                                                        } ?><?= (isset($item['pro_description']) && $item['pro_description'] != "") ? "<br/><br/>" . $item['pro_description'] : "" ?></td>

                            <td colspan="1" class="text-center"><?php echo $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $item['order_item_brand_id'] . "'") ?></td>
                            <td colspan="2" class="text-center">
                                <?php
                                $weight = $db->rp_getValue("product_weight_price", "pro_weight", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");
                                echo $kg = $weight / 1000;

                                $weight_total += $kg;

                                ?>

                            </td>

                            <td colspan="3" class="text-center"><?= $item['pro_qty'] ?></td>
                            <!-- <td colspan="3" class="text-center"><?= $item['original_price'] ?></td> -->
                            <!-- <td colspan="3" class="text-center">
								<?php
                                if ($item['discount_amount'] != 0) {
                                    echo $item['discount_amount'];
                                }
                                ?>
									
							</td> -->
                            <!-- <td colspan="3" class="text-center"><?php echo $item['cartoon_qty'] . " Caret"; ?></td> -->
                            <td colspan="3" class="text-center"><?php echo $item['unitprice']; ?></td>
                            <td colspan="" class="text-center"><?php echo $currency . ' ' . round($item['totalprice'], 2); ?></td>
                        </tr>
                        <?php
                    }

                    if ($count < 5) {
                        for ($i = 0; $i < 12 - $count; $i++) {
                        ?>
                            <tr class="border">
                                <td colspan="1"></td>
                                <td colspan="1"></td>
                                <td colspan="5"></td>
                                <td colspan="1"></td>
                                <td colspan="2"></td>
                                <td colspan="3"></td>
                                <!-- <td colspan="3"></td> -->
                                <!-- <td colspan="3"></td> -->
                                <td colspan="3"></td>
                                <td colspan=""></td>
                            </tr>
                <?php
                        }
                    }
                }
                ?>
                <tr class="">
                    <td colspan="1"></td>
                    <td colspan="1"></td>
                    <td colspan="5"></td>
                    <td colspan="1"></td>
                    <td colspan="2"></td>
                    <td colspan="3"></td>
                    <!-- <td colspan="3"></td> -->
                    <!-- <td colspan="3"></td> -->
                    <td colspan="3"></td>
                    <td colspan=""></td>
                </tr>


                <!-- <tr>
						<td colspan="1"></td>
						<td colspan="5" class="text-center"><b></b></td>
						<td colspan="2" class="text-center"><b><?php echo $totalproqty; ?></b></td>
						<td colspan="3" class="text-center"></td>
						<td colspan="3" class="rate" class="text-center"></td>
						<td colspan="3"></td>
						<td colspan="3"></td>
						<td colspan=""></td>
					</tr> -->
            </tbody>
        </table>
        <table style="width:250mm!important;">
            <tbody class="<?= $cl; ?>">
                <tr class="font-size">
                    <td colspan="8" class="" rowspan="11" style="vertical-align: top;">
                        <span class="font-13"><b>Terms & Condition : </b></span><br>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="font-13"><?php echo html_entity_decode($cart_detail_d['terms_comdition']); ?></span><br>
                            </div>
                        </div>

                        <span class="font-13"><b>Grand Total In Words</b> :
                            <?php
                            $grand_total_words = $ntw->rp_convertNumToWord($cart_detail_d['grand_total_rounded']);
                            echo ucwords(strtolower($grand_total_words)); ?>
                        </span>
                        <span></span><br>
                        <span class="font-13">
                            <b>Bank Details : </b>
                            <?php
                            if (isset($company_detail_d['bank_details']) && $company_detail_d['bank_details'] != "") {
                                echo html_entity_decode($company_detail_d['bank_details']);
                            } else {
                            ?>
                                Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
                                Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?>
                            <?php
                            }
                            ?>
                        </span><br>
                        <span class="font-13"><b>Remarks</b></span><br>
                        <span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span><br>
                        <span style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?> &nbsp; </span>
                        <?php
                        $modified_name = explode(",", $cart_detail_d['modified_by']);
                        $last_modified_id = array_slice($modified_name, -1)[0];
                        $modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
                        ?>
                        <br /><span style="color: red;">Edited By : <?= $modified_by_name ?> &nbsp; </span>
                    </td>
                    <td colspan="4" class="text-left font-13"><strong>Sub Total</strong></td>
                    <td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($totalprice1, 2); ?></strong></td>

                </tr>

                <?php if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { ?>
                    <tr>
                        <td colspan="4" class="text-left font-13"><strong>Cash Discount</strong></td>
                        <td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['cash_discount_amount'], 2); ?></strong></td>
                    </tr>
                <?php } ?>

                <?php if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { ?>
                    <tr>
                        <td colspan="4" class="text-left font-13"><strong>Additional Discount</strong></td>
                        <td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['additional_discount_amount'], 2); ?></strong></td>
                    </tr>
                <?php } ?>
                <!-- <tr>
						<td colspan="4" class="text-left font-13"><strong>Transport Charge</strong></td>
						<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['transport_charge'], 2); ?></strong></td>
					</tr> -->
                <!-- <tr>
						<td colspan="4" class="text-left font-13"><strong>Packing & Forwarding Charge</strong></td>
						<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['packing_charge'], 2); ?></strong></td>
					</tr> -->
                <tr>
                    <td colspan="4" class="text-left font-13"><strong>Total Taxable Amount</strong></td>

                    <!-- <td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['subtotal'], 2); ?></strong></td> -->

                    <?php
                    $Invoice = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
                    $InvoiceIds = array();
                    while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
                        $InvoiceIds[] = $Invoice_d['id'];
                    }
                    $InvoiceIds = implode(",", $InvoiceIds);
                    $total_pro_taxable = $db->rp_getValue("quotation_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);

                    // added by shivani
                    $total_tax_amt = $totalprice1 - ($cart_detail_d['cash_discount_amount'] + $cart_detail_d['additional_discount_amount']) + ($cart_detail_d['transport_charge'] + $cart_detail_d['packing_charge']);
                    ?>
                    <td colspan="4" class="text-right rate "><strong><?php echo CURR . ' ' . number_format($total_tax_amt, 2); ?></strong></td>

                </tr>
                <?php
                if ($cart_detail_d['igst_amount'] != "0") {
                    if ($cart_detail_d['type_of_executive'] == 8) {
                        if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
                ?>
                            <tr>
                                <td colspan="4" class="text-left"><strong>C GST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format(($cart_detail_d['igst_amount']) / 2, 2)) ?></strong> </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-left"><strong>S GST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr>
                                <td colspan="4" class="text-left"><strong>IGST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-left"></td>
                                <td colspan="4" class="text-right "></td>
                            </tr>
                        <?php
                        }
                    } else {
                        if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
                        ?>
                            <tr>
                                <td colspan="4" class="text-left"><strong>C GST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong> </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-left"><strong>S GST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr>
                                <td colspan="4" class="text-left"><strong>IGST</strong></td>
                                <td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-left"></td>
                                <td colspan="4" class="text-right "></td>
                            </tr>
                    <?php
                        }
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4" class="text-left"></td>
                        <td colspan="4" class="text-right "></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-left"></td>
                        <td colspan="4" class="text-right "></td>
                    </tr>
                <?php
                }
                ?>
                <?php
                if ($cart_detail_d['tcs_amount'] != "0") {
                ?>
                    <tr>
                        <td colspan="4">
                            <strong>TCS (<?= TCS_CHARGE_IN_PER ?>%)</strong>
                        </td>
                        <td colspan="4" class="text-right"><strong><?= $currency . number_format($cart_detail_d['tcs_amount'], 2) ?></strong></td>
                    </tr>
                <?php
                } else {
                ?>
                    <tr>
                        <td colspan="4"></td>
                        <td colspan="4" class="text-right"><strong></td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td colspan="4">
                        <strong>Round Off</strong>
                    </td>
                    <td colspan="4" class="text-right"><strong>
                            <?php echo $currency . $cart_detail_d['roundoff']; ?>
                        </strong></td>
                </tr>
                <tr style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;">
                    <td colspan="4">
                        <strong>Grand Total</strong>
                    </td>
                    <td colspan="4" class="text-right" style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;"><strong>
                            <?php
                            echo $currency . ' ' . $db->rp_number_format($cart_detail_d['grand_total_rounded'], 2);
                            ?>
                        </strong>
                    </td>
                </tr>

            </tbody>
            <!-- <tr> -->

            <!-- </tr> -->
        </table>
        <?php
        if ($cart_detail_d['igst_amount'] != 0) {
        ?>

            <!-- hsn summary -->
            <table style="width:250mm!important;">
                <?php
                if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
                    $gst_or_igst = "Total GST Rate";
                    $gst_or_igst1 = "Total GST Amount";
                } else {
                    $gst_or_igst = "IGST% Rate";
                    $gst_or_igst1 = "IGST Amount";
                }
                ?>
                <tr>
                    <th class="text-center" colspan="2">HSN/SAC</th>
                    <!-- <th class="text-center" colspan="1">Qty</th> -->
                    <th class="text-center" colspan="2">Taxable Value</th>
                    <th class="text-center" colspan="2"><?php echo $gst_or_igst; ?></th>
                    <th class="text-center" colspan="2"><?php echo $gst_or_igst1; ?></th>
                    <th class="text-center" colspan="1">CGST% Rate</th>
                    <th class="text-center" colspan="2">CGST Amount</th>
                    <th class="text-center" colspan="2">SGST% Rate</th>
                    <th class="text-center" colspan="3">SGST Amount</th>
                </tr>
                <?php
                $ITEMS = array();
                $items1 = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' GROUP BY hsn_code", "", 0);
                while ($item = mysqli_fetch_assoc($items1)) {
                    // echo $items1;exit;
                    $ITEMS[] = $item1;
                    if ($items1) {
                        $gst_rate = $db->rp_getValue("product", "igst", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);
                        $count = 0;
                        $totalprice = 0;
                        $final_price = 0;
                        $boxqty = 0;
                        $cartoonqty = 0;
                        $totalproqty = 0;
                        $totalrate = 0;
                        $totaldiscount = 0;
                        $gst_per_amount = 0;
                        $GST = 0;

                        if ($cart_detail_d['igst_amount'] != 0) {
                            $GST = $gst_rate;
                            $CGST = $gst_rate / 2;
                            $SGST = $gst_rate / 2;
                        } else {
                            $GST = "";
                            $CGST = "";
                            $SGST = "";
                        }

                        $Invoice = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
                        $InvoiceIds = array();
                        while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
                            $InvoiceIds[] = $Invoice_d['id'];
                        }
                        $InvoiceIds = implode(",", $InvoiceIds);
                        // echo $InvoiceIds;exit;
                        $total_pro_qty = $db->rp_getValue("quotation_product_item", "SUM(pro_qty)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
                        $total_pro_taxable = $db->rp_getValue("quotation_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);

                        $cash_amount = ($total_pro_taxable * $cart_detail_d['cash_discount']) / 100;
                        if ($cash_amount > $total_pro_taxable) {
                            // $SubPrice=$cash_amount-$total_pro_taxable;
                            $SubPrice = $total_pro_taxable;
                        } else {
                            // $SubPrice=$total_pro_taxable-$cash_amount;
                            $SubPrice = $total_pro_taxable;
                        }
                        // $gst_per_amount=($SubPrice*$GST)/100; 							
                        if ($cart_detail_d['igst_amount'] != 0) {
                            $gst_per_amount += $db->rp_getValue("quotation_product_item", "SUM(igst_amount)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
                            if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
                                $cgst_per_amount = ($gst_per_amount) / 2;
                                $sgst_per_amount = ($gst_per_amount) / 2;
                                // $cgst_per_amount=($SubPrice*$CGST)/100;
                                // $sgst_per_amount=($SubPrice*$SGST)/100;
                                $CGST = $GST / 2;
                                $SGST = $GST / 2;
                            } else {
                                $cgst_per_amount = "";
                                $sgst_per_amount = "";
                                $CGST = "";
                                $SGST = "";
                            }
                        } else {
                            $gst_per_amount = "";
                            $cgst_per_amount = "";
                            $sgst_per_amount = "";
                            $CGST = "";
                            $SGST = "";
                        }
                ?>
                        <tr>
                            <td colspan="2" class="box_qty " style="text-align: center;"><?= $db->rp_getValue("product", "hsn_code", "isDelete=0 AND id='" . $item['pro_id'] . "'", 0) ?></td>
                            <!-- <td colspan="1" class="text-center b_qty "><?php echo $total_pro_qty; ?></td> -->
                            <td colspan="2" class="text-right rate "><?php echo CURR . ' ' . number_format($total_pro_taxable, 2); ?></td>
                            <td colspan="2" class="text-right b_qty "><?php echo $GST; ?><?= ($GST) ? "%" : ""; ?></td>
                            <td colspan="2" class="text-right b_qty "><?= ($gst_per_amount) ? CURR : ""; ?><?= number_format($gst_per_amount, 2);  ?></td>
                            <td colspan="1" class="text-right b_qty "><?php echo $CGST; ?><?= ($CGST) ? "%" : ""; ?></td>
                            <td colspan="2" class="text-right b_qty "><?= ($CGST) ? CURR : ""; ?><?= number_format($cgst_per_amount, 2);  ?></td>
                            <td colspan="2" class="text-right b_qty "><?php echo $SGST; ?><?= ($SGST) ? "%" : ""; ?></td>
                            <td colspan="3" class="text-right b_qty "><?= ($SGST) ? CURR : ""; ?><?= number_format($sgst_per_amount, 2);  ?></td>
                        </tr>
                <?php
                        $Total_total_pro_qty += $total_pro_qty;
                        $Total_total_pro_taxable += $total_pro_taxable;
                        $Total_gst_per_amount += $gst_per_amount;
                        $Total_cgst_per_amount += $cgst_per_amount;
                        $Total_sgst_per_amount += $sgst_per_amount;
                    }
                }
                ?>
                <tr>
                    <td colspan="2" class="text-center"><strong>Total</strong></td>
                    <!-- <td colspan="1" class="text-right"><b><?php echo $Total_total_pro_qty; ?></b></td> -->
                    <td colspan="2" class="text-right"><b><?php echo  CURR . ' ' . number_format($Total_total_pro_taxable, 2); ?></b></td>
                    <td colspan="2" class="text-right"><b><?php ?></b></td>
                    <td colspan="2" class="text-right"><b><?= ($Total_gst_per_amount) ? CURR : ""; ?><?= number_format($Total_gst_per_amount, 2); ?></b></td>
                    <td colspan="1" class="text-right"><b><?php ?></b></td>
                    <td colspan="2" class="text-right"><b><?= ($Total_cgst_per_amount) ? CURR : ""; ?><?= number_format($Total_cgst_per_amount, 2); ?></b></td>
                    <td colspan="2" class="text-right"><b><?php ?></b></td>
                    <td colspan="3" class="text-right"><b><?= ($Total_sgst_per_amount) ? CURR : ""; ?><?= number_format($Total_sgst_per_amount, 2); ?></b></td>
                </tr>
            </table>
        <?php
        }
        ?>
        <!-- <table style="width:250mm!important;">
				<tbody>
					<tr>
						<td colspan="8" rowspan="4" class="no-border-right text-left">
							<br><br><br><br>
							<strong style="margin-right: 36px;">Customer Signature</strong>
						</td> 
						<td colspan="8" rowspan="4" class="text-right">
							<strong style="margin-right: 25px;">For,
								<?php
                                if (isset($company_detail_d['name']) && $company_detail_d['name'] != "") {
                                    echo $company_detail_d['name'];
                                } else {
                                    echo CLIENT_BRAND_NAME;
                                }
                                ?>
							<br>
							</strong>
							<br><br><br>
							<strong  style="margin-right: 25px;">
							Authorised SIgnatory</strong>
						</td>
					</tr>
				</tbody> 
			</table> -->
        <table style="border: 1px solid #595959;border-collapse: collapse;">
            <tbody>
                <tr>
                    <td>
                        <?php
                        if (isset($company_detail_d['footer_image_path']) && $company_detail_d['footer_image_path'] != "") {
                        ?>
                            <img style="width: 933px;height: 184px;padding: 0px !important;" src="<?= FOOTER_A . $company_detail_d['footer_image_path'] ?>">
                        <?php
                        } else {
                        ?>
                            <img style="width: 933px;height: 184px;padding: 0px !important;" src="../images/craftbox_header.jpg">
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>