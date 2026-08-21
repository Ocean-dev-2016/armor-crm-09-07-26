<?php
$page_id = 607;
$page_slug = 'quotation';
$page_slug = "manage_inward_store";
$ctable    = "quotation_detail";
$ctable1    = "Quotation";
$main_page    = $ctable;
$page       = "add_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
include("connect.php");
// print_r($rights);exit;
include('../include/product.class.php');
include("../include/quotation.class.php");
$ObjQuotation = new Quotation();
$uid          = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$product      = new Product();
$isActive     = 0;
$count        = "";
$total_qty    = "";
$total_amount = "";
$grand_total  = "";
$total        = "";
$flag         = "";
$remarks      = "";
$reference    = "";
$attn         = "";
$item_info    = array();
$flag         = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : "";
$quotation_no = $db->getLastInsertId("quotation_detail");
$quotation_no = DEALER_QUOTATION_NO . str_pad($quotation_no, 2, '0', STR_PAD_LEFT);

if (isset($_REQUEST['submit'])) {
   $isActive                       = 1;
   $detail['isDelete']               = 0;
   $detail['remarks']                = $db->clean($_REQUEST['remarks']);
   $detail['uid']                    = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
   $detail['reference']              = isset($_REQUEST['reference']) ? $db->clean($_REQUEST['reference']) : "";
   $detail['reference_date']         = isset($_REQUEST['reference_date']) ? date('Y-m-d', strtotime($_REQUEST['reference_date'])) : "";
   $detail['attn']                   = isset($_REQUEST['attn']) ? $db->clean($_REQUEST['attn']) : "";
   $detail['attn_no']                = isset($_REQUEST['attn_no']) ? $db->clean($_REQUEST['attn_no']) : "";
   $detail['attn_email']             = isset($_REQUEST['attn_email']) ? $db->clean($_REQUEST['attn_email']) : "";
   $detail['transport_charge']        = isset($_REQUEST['transport_charge']) ? $db->clean($_REQUEST['transport_charge']) : "";
   $detail['terms_comdition']        = isset($_REQUEST['terms_comdition']) ? html_entity_decode($_REQUEST['terms_comdition']) : "";
   $detail['faithfully']            = isset($_REQUEST['faithfully']) ? html_entity_decode($_REQUEST['faithfully']) : "";
   $detail['vendor_code']            = isset($_REQUEST['vendor_code']) ? trim($_REQUEST['vendor_code']) : "";
   $detail['tendor_code']            = isset($_REQUEST['tendor_code']) ? trim($_REQUEST['tendor_code']) : "";
   $detail['tendor_no']            = isset($_REQUEST['tendor_no']) ? trim($_REQUEST['tendor_no']) : "";
   $detail['transport_name']        = isset($_REQUEST['transport_name']) ? trim($_REQUEST['transport_name']) : "";
   $detail['transport_through']     = isset($_REQUEST['transport_through']) ? trim($_REQUEST['transport_through']) : "";
   $detail['packing_charge']        = isset($_REQUEST['packing_charge']) ? $db->clean($_REQUEST['packing_charge']) : "";
   $detail['shipping_address']        = isset($_REQUEST['shipping_address']) ? trim($_REQUEST['shipping_address']) : "";
   $detail['billing_address']        = isset($_REQUEST['billing_address']) ? trim($_REQUEST['billing_address']) : "";
   $detail['gstin']              = isset($_REQUEST['name_gstin']) ? trim($_REQUEST['name_gstin']) : "";
   $detail['cash_discount']      = isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : "";
   $detail['additional_discount']   = isset($_REQUEST['additional_discount']) ? $db->clean($_REQUEST['additional_discount']) : "";
   $detail['gst_apply_flag']      = isset($_REQUEST['gst_apply_flag']) ? $db->clean($_REQUEST['gst_apply_flag']) : "";
   $detail['tcs_apply_flag']      = isset($_REQUEST['tcs_apply_flag']) ? $db->clean($_REQUEST['tcs_apply_flag']) : "";
   $detail['round_off']         = isset($_REQUEST['round_off']) ? $db->clean($_REQUEST['round_off']) : "";
   $detail['currency_code']      = isset($_REQUEST['currency_code']) ? $db->clean($_REQUEST['currency_code']) : "";
   $detail['transport_charge_gst']   = isset($_REQUEST['transport_charge_per']) ? $db->clean($_REQUEST['transport_charge_per']) : "";
   $detail['packing_charge_gst']   = isset($_REQUEST['packing_charge_per']) ? $db->clean($_REQUEST['packing_charge_per']) : "";
   $detail['cd_gst']            = isset($_REQUEST['cd_gst']) ? $db->clean($_REQUEST['cd_gst']) : "";
   $detail['ad_gst']            = isset($_REQUEST['ad_gst']) ? $db->clean($_REQUEST['ad_gst']) : "";
   $detail['type_of_company']               = isset($_REQUEST['type_of_company']) ? $db->clean($_REQUEST['type_of_company']) : "";
   $detail['terms_condition_id']               = isset($_REQUEST['terms_condition_id']) ? $db->clean($_REQUEST['terms_condition_id']) : "";
   $detail['attachment']               = isset($_REQUEST['attachment']) ? $db->clean($_REQUEST['attachment']) : "";

   $product_id = $_REQUEST['product_id'];
   $qty = $_REQUEST['qty'];
   $original_price = $_REQUEST['original_price'];
   $price = $_REQUEST['price'];
   $pro_name = $_REQUEST['pro_name'];
   $pro_description = $_REQUEST['pro_description'];
   $weight_id = $_REQUEST['weight_id'];
   $box_qty = $_REQUEST['box_qty'];
   $bag = $_REQUEST['bag'];
   $loose          = $_REQUEST['loose'];
   $discount = $_REQUEST['discount'];
   $brand_id = $_REQUEST['brand_id'];
   $cd_discount = $_REQUEST['cd_discount'];
   $ad_discount = $_REQUEST['ad_discount'];
   $gst_amount_item = $_REQUEST['gst_amount_item'];
   $taxable_amount = $_REQUEST['taxable_amount'];
   $sub_total = $_REQUEST['sub_total'];
   $other_charge = $_REQUEST['other_charge'];
   $fright_charge = $_REQUEST['fright_charge'];
   $discount_amount = $_REQUEST['discount_amount'];
   $is_including = $_REQUEST['is_including'];
   $item_order_unit    = $_REQUEST['item_order_unit'];
   $order_qty = $_REQUEST['order_qty'];
   $order_item_brand_id = $_REQUEST['order_item_brand_id'];

   $size[] = sizeof($product_id);
   $size[] = sizeof($qty);
   $size[] = sizeof($price);
   $size[] = sizeof($pro_name);
   $pro_description[] = sizeof($pro_description);

   $weight_id[] = sizeof($weight_id);
   $box_qty[] = sizeof($box_qty);
   $loose[] = sizeof($loose);
   $is_including[] = sizeof($is_including);
   $order_item_brand_id[] = sizeof($order_item_brand_id);

   $value_check = sizeof($product_id);

   if (in_array($value_check, $size)) {
      $isValidArray = true;
   } else {
      $isValidArray = false;
   }
   // print_r($product_id);exit;
   /*"cd_discount"=>$cd_discount[$i],"ad_discount"=>$ad_discount[$i]*/
   if ($isValidArray && !empty($product_id)) {
      for ($i = 0; $i < sizeof($product_id); $i++) {
         $item[] = array("qty" => $qty[$i], "pid" => $product_id[$i], "original_price" => $original_price[$i], "price" => $price[$i], "pro_name" => $pro_name[$i], "pro_description" => $pro_description[$i], "weight_id" => $weight_id[$i], "cartoon_qty" => $box_qty[$i], "box_qty" => $bag[$i], "loose" => $loose[$i], "discount" => $discount[$i], "brand_id" => $brand_id[$i], "cd_discount" => $cd_discount[$i], "ad_discount" => $ad_discount[$i], "gst_amount_item" => $gst_amount_item[$i], "taxable_amount" => $taxable_amount[$i], "sub_total" => $sub_total[$i], "other_charge" => $other_charge[$i], "fright_charge" => $fright_charge[$i], "discount_amount" => $discount_amount[$i], "is_including" => $is_including[$i], "item_order_unit" => $item_order_unit[$i], "order_qty" => $order_qty[$i], "order_item_brand_id" => $order_item_brand_id[$i]);
      }
   }
   // print_r($item);exit;
   if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {
      if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] != "") {
         $detail['cid']           = $db->clean($_REQUEST['dealer_id']);
         $detail['dealer_id']     = $db->clean($_REQUEST['dealer_id']);
         $detail['inquiry_id']    = $_REQUEST['inquiry_id'];
      } else if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "") {

         $detail['cid']           = $db->clean($_REQUEST['customer_id']);
         $detail['dealer_id']     = $db->clean($_REQUEST['customer_id']);
         $detail['inquiry_id']    = 0;
      } else if ($_REQUEST['inquiry_id'] == "") {
         $detail['cid']             = $db->clean($_REQUEST['dealer_id1']);
         $detail['inquiry_id']      = 0;
      } else {
         $detail['cid']             = $db->clean($_REQUEST['edit_dealer_id']);
         $detail['dealer_id']       = $db->clean($_REQUEST['edit_dealer_id']);
         $detail['inquiry_id']      = $db->rp_getValue("quotation_detail", "inquiry_id", "id='" . $_REQUEST['quotation_id'] . "'");
      }
      $detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
      $detail['quotation_date']     = date('Y-m-d', strtotime($_REQUEST['quotation_date']));
      $detail['quotation_no']         = isset($_REQUEST['quotation_no']) ? $db->clean($_REQUEST['quotation_no']) : "";

      if (isset($_REQUEST['quotation_id']) && $_REQUEST['quotation_id'] != "") {
         $detail['reference_id'] = $_REQUEST['quotation_id'];
         $revised_quotation_main_id = $db->rp_getValue("quotation_detail", "revised_quotation_main_id", "id='" . $_REQUEST['quotation_id'] . "'");
         if ($revised_quotation_main_id == 0) {
            $revised_quotation_main_id = $_REQUEST['quotation_id'];
         }

         if (!isset($revised_quotation_main_id) || $revised_quotation_main_id == "") {
            $revised_quotation_main_id = 0;
         }
         $detail['revised_quotation_main_id'] = $revised_quotation_main_id;
      } else {
         $detail['reference_id'] = 0;
         $detail['revised_quotation_main_id'] = 0;
      }

      if ($rights['insert_flag'] != 1) {
         $db->rp_location('access_denied.php?msg=delete_access_denied');
      }

      $reply = $ObjQuotation->AddQuotation($detail, $item, $_FILES);
      if ($reply['ack'] == 1) {
         unset($detail['quotation_date']);

         $detail['cash_discount_flag']             = 0;
         $detail['quotation_id']                  = $reply['quotation_id'];
         $detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
         $detail['cash_discount']               = isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : "";
         $detail['additional_discount']            = isset($_REQUEST['additional_discount']) ? $db->clean($_REQUEST['additional_discount']) : "";
         $detail['gst_apply_flag']               = isset($_REQUEST['gst_apply_flag']) ? $db->clean($_REQUEST['gst_apply_flag']) : "";
         $detail['tcs_apply_flag']               = isset($_REQUEST['tcs_apply_flag']) ? $db->clean($_REQUEST['tcs_apply_flag']) : "";
         $detail['round_off']                  = isset($_REQUEST['round_off']) ? $db->clean($_REQUEST['round_off']) : "";
         $detail['cash_discount_amount']            = isset($_REQUEST['cash_discount_amount']) ? $db->clean($_REQUEST['cash_discount_amount']) : "";
         $detail['additional_discount_amount']       = isset($_REQUEST['addtional_discount_amount']) ? $db->clean($_REQUEST['addtional_discount_amount']) : "";

         $ObjQuotation->PlaceQuotationPanel($detail, $_FILES);
         $db->addSuccessMessage($reply['ack_msg']);
         $db->rp_location("quotation_manage.php");
         //for redirect to location after insert
         $db->rp_location("quotation_manage.php?msg=inserted");
      } else {
         $db->addErrorMessage($reply['ack_msg']);
      }
   } else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") {
      if ($rights['update_flag'] != 1) {
         $db->rp_location('access_denied.php?msg=delete_access_denied');
      }
      // $reply=$ObjQuotation->UpdateProductItem($detail,$item);
      $detail['transport_charge']     = isset($_REQUEST['transport_charge']) ? $db->clean($_REQUEST['transport_charge']) : "";
      $detail['packing_charge']     = isset($_REQUEST['packing_charge']) ? $db->clean($_REQUEST['packing_charge']) : "";
      $detail['shipping_address']     = isset($_REQUEST['shipping_address']) ? trim($_REQUEST['shipping_address']) : "";
      $detail['billing_address']     = isset($_REQUEST['billing_address']) ? trim($_REQUEST['billing_address']) : "";
      $detail['gstin']             = isset($_REQUEST['name_gstin']) ? trim($_REQUEST['name_gstin']) : "";
      $detail['dealer_id']        = $db->clean($_REQUEST['edit_dealer_id']);
      $detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
      $detail['cid']              = $db->clean($_REQUEST['edit_dealer_id']);
      $detail['quotation_id']          = $db->clean($_REQUEST['id']);
      $reply = $ObjQuotation->UpdateQuotation($detail, $item);
      // print_r($reply);exit;
      if ($reply['ack'] == 1) {
         unset($detail['quotation_date']);
         $detail['cash_discount_flag']    = 0;
         $detail['quotation_id']             = $reply['quotation_id'];
         // $detail['sales_executive_id'] 	= "";
         $detail['cash_discount']            = isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : "";
         $detail['additional_discount']   = isset($_REQUEST['additional_discount']) ? $db->clean($_REQUEST['additional_discount']) : "";
         $detail['gst_apply_flag']            = isset($_REQUEST['gst_apply_flag']) ? $db->clean($_REQUEST['gst_apply_flag']) : "";
         $detail['round_off']                  = isset($_REQUEST['round_off']) ? $db->clean($_REQUEST['round_off']) : "";
         $detail['cash_discount_amount']   = isset($_REQUEST['cash_discount_amount']) ? $db->clean($_REQUEST['cash_discount_amount']) : "";
         $detail['additional_discount_amount']   = isset($_REQUEST['addtional_discount_amount']) ? $db->clean($_REQUEST['addtional_discount_amount']) : "";

         $ObjQuotation->PlaceQuotationPanel($detail, $_FILES);
         $db->addSuccessMessage($reply['ack_msg']);
         $type = $db->rp_getValue("quotation_detail", "customer_type", "id='" . $reply['type'] . "'");
         $db->rp_location("quotation_manage.php");
      } else {
         $db->addErrorMessage($reply['ack_msg']);
      }
   }
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {

   $detail['id']  = $_REQUEST['id'];
   $OrderNo           = $db->rp_getValue("quotation_detail", "quotation_no", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
   $Order              = $db->rp_getValue("quotation_detail", "dealer_id", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
   $customer_name = $db->rp_getValue("customer", "company_name", "id=" . $Order . " AND isDelete=0", 0);
   $page_title    = ucwords($_REQUEST['mode']) . '&nbsp' . "Quotation" . "- " . ucwords($OrderNo) . '&nbsp';
   $reply              = $ObjQuotation->GetQuotationDetail($detail);
   $item_info     = $ObjQuotation->GetQuotationDetailItems($detail);

   if ($reply['ack'] == 1) {
      $id = $_REQUEST['id'];
      $result = $reply['result'];
      // print_r($result); exit;
      extract($result);
      $invoice_date = date("d-m-Y", strtotime($invoice_date));
   }
   if ($item_info['ack'] == 1) {
      $store_inward_id = $_REQUEST['id'];
      $item_info = $item_info['result'];
   } else {
      $item_info = array();
   }
}
if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] > 0 && $_REQUEST['mode'] == "add") {
   $customer_id = $_REQUEST['customer_id'];
   $detail['id'] = $_REQUEST['customer_id'];

   $reply = $ObjQuotation->GetCustomer($detail);

   if ($reply['ack'] == 1) {
      $result = $reply['result'];
      extract($result);
   } else {
      $item_info = array();
   }
} else if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] > 0 && $_REQUEST['mode'] == "add") {
   $inquiry_id = $_REQUEST['inquiry_id'];
   $detail['id'] = $_REQUEST['inquiry_id'];

   $reply = $ObjQuotation->GetInquiry($detail);
   $reply1 = $ObjQuotation->GetInquiryItem($detail);
   // print_r($reply1);exit;
   //print_r($reply);exit;
   if ($reply['ack'] == 1) {
      $result = $reply['result'];
      //print_r($result);exit;
      //$item_info=$reply['item'];
      extract($result);
   }

   if ($reply1['ack'] == 1) {
      $store_inward_id = $_REQUEST['id'];
      $item_info = $reply1['result'];
   } else {
      $item_info = array();
   }
}

if (isset($_REQUEST['quotation_id']) && $_REQUEST['quotation_id'] > 0 && $_REQUEST['mode'] == "add") {
   $quotation_id = $_REQUEST['quotation_id'];
   $detail['id'] = $_REQUEST['quotation_id'];
   $OrderNo = $db->rp_getValue("quotation_detail", "quotation_no", "id=" . $quotation_id . " AND isDelete=0", 0);

   $revised_quotation_main_id = $db->rp_getValue("quotation_detail", "revised_quotation_main_id", "id='" . $_REQUEST['quotation_id'] . "'");
   $quotation_no = $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $revised_quotation_main_id . "'");
   $totalCount = $db->rp_getTotalRecord("quotation_detail", "revised_quotation_main_id='" . $revised_quotation_main_id . "'", 0);
   $totalCount = $totalCount + 2;
   $detail['revised_quotation_main_id'] = $revised_quotation_main_id;

   /*$new_quotation_no = "R1 - ".$totalCount." ".$quotation_no;*/
   $quotation_no = "R1 - " . $totalCount . " " . $quotation_no;

   $Order = $db->rp_getValue("quotation_detail", "dealer_id", "id=" . $quotation_id . " AND isDelete=0", 0);
   $customer_name = $db->rp_getValue("customer", "company_name", "id=" . $Order . " AND isDelete=0", 0);
   $reply = $ObjQuotation->GetRevisedQuotationDetail($detail);
   $reply1 = $ObjQuotation->GetRevisedQuotationDetailItems($detail);
   if ($reply['ack'] == 1) {
      $result = $reply['result'];
      extract($result);
   }

   if ($reply1['ack'] == 1) {
      $store_inward_id = $_REQUEST['id'];
      $item_info = $reply1['result'];
   } else {
      $item_info = array();
   }
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") {
   if ($rights['delete_flag'] != 1) {
      $db->rp_location('access_denied.php?msg=delete_access_denied');
   }
   $detail['id'] = $_REQUEST['id'];
   $reply = $ObjQuotation->DeleteQuotation($detail);
   if ($reply['ack'] == 1) {
      $db->addSuccessMessage($reply['ack_msg']);
      //for redirect to location after Delete
      $db->rp_location("quotation_manage.php?msg=inserted");
   } else {
      $db->addErrorMessage($reply['ack_msg']);
   }
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "isActive" && isset($_REQUEST['status'])  && $_REQUEST['status'] != "") {
   $status = $_REQUEST['status'];
   $rows    = array(
      "isActive"   => $status
   );
   $where   = "id='" . $_REQUEST['id'] . "'";
   $db->rp_update($ctable, $rows, $where);
   $db->rp_location("customer_orders_manage.php?msg=updated");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
   <?php include("include_css.php"); ?>
   <link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
   <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
   <link rel="stylesheet" type="text/css" href="css/fSelect.css" />
   <style type="text/css">
      tbody td,
      th {
         border-left: none !important;
         border-right: none !important;
      }

      tfoot td {
         border: none !important;
      }

      .f-10 {
         font-size: 13px;
      }
   </style>
</head>

<body class="page-md">
   <?php include("header.php"); ?>
   <div class="page-container">
      <div class="page-head bg-grey">
         <div class="container">
            <div class="page-title">
               <?php
               $type = $db->rp_getValue("executive", "type_of_executive", "id=" . $uid . "", 0);
               $back = 'quotation_manage.php';
               ?>
               <h1><a href="<?php echo  $back; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
            </div>
         </div>
      </div>
      <div class="page-content">
         <div class="container">
            <div class="row">
               <div class="col-sm-12">
                  <?php $db->printErrorMessage(); ?>
                  <?php $db->printSuccessMessage(); ?>
               </div>
            </div>
            <!-- Employee ID-->
            <form role="form" action="" method="post" onSubmit="return check_form();" enctype="multipart/form-data">
               <div class="row">
                  <div class="col-md-12">
                     <div class="portlet box blue">
                        <div class="portlet-body form">
                           <div class="row">
                              <div class="col-sm-12">
                                 <div class="col-md-12 col-sm-12 col-xs-12 portlet box grey-cascade box">
                                    <div class="portlet-title">
                                       <div class="caption">
                                          <i class="fa fa-user"></i>
                                          <span class="caption-subject bold uppercase"> QUOTATION DETAIL</span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-body">
                                          <div class="form-group">
                                             <div class="row">
                                                <div class="col-md-4 col-sm-4">

                                                   <div class="form-group">
                                                      <?php
                                                      if ($_REQUEST['mode'] == "edit" || (isset($_REQUEST['inquiry_id']) && $_REQUEST['mode'] == "add") || (isset($_REQUEST['quotation_id']) && $_REQUEST['mode'] == "add") || (isset($_REQUEST['customer_id']) && $_REQUEST['mode'] == "add")) {
                                                         $disabled = "disabled";
                                                      ?>
                                                         <input type="hidden" name="type_of_company" id="type_of_company" value="<?= $type_of_company; ?>">
                                                      <?php
                                                      } else {
                                                         $disabled = "";
                                                      }
                                                      ?>
                                                      <label class="test">Select Company<code>*</code></label>
                                                      <select class="form-control b-3" id="type_of_company" name="type_of_company" autofocus <?php echo $disabled; ?> onchange="if($('#customer_type').val()){ getCustomer($('#customer_type').val()); }">
                                                         <option value="">Select Company</option>
                                                         <?php

                                                         // echo $type_of_company;exit();
                                                         $company_r = $db->rp_getData("company_master", "*", "isDelete=0", "id DESC", 0);
                                                         if (mysqli_num_rows($company_r) > 0) {
                                                            while ($company_d = mysqli_fetch_array($company_r)) {
                                                         ?>
                                                               <option value="<?php echo $company_d['id']; ?>" <?= ($type_of_company == $company_d['id']) ? "selected" : ""; ?>><?php echo $company_d['name']; ?></option>
                                                         <?php
                                                               $company_id = $company_d['id'];
                                                            }
                                                         }
                                                         ?>
                                                      </select>
                                                      <p class="help-block"></p>
                                                   </div>

                                                   <div class="form-group">
                                                      <input type="hidden" name="inquiry_id" id="inquiry_id" value="<?= $inquiry_id; ?>">
                                                      <?php
                                                      if ($_REQUEST['mode'] == "edit" || (isset($_REQUEST['inquiry_id']) && $_REQUEST['mode'] == "add") || (isset($_REQUEST['quotation_id']) && $_REQUEST['mode'] == "add") || (isset($_REQUEST['customer_id']) && $_REQUEST['mode'] == "add")) {
                                                         $disabled = "disabled";
                                                      ?>
                                                         <input type="hidden" name="customer_type" id="customer_type" value="<?= $customer_type; ?>">
                                                      <?php
                                                      } else {
                                                         $disabled = "";
                                                      }
                                                      ?>
                                                      <label>Select Customer Type<code>*</code></label>
                                                      <select class="form-control" <?php echo $disabled; ?> id="customer_type" name="customer_type1" onchange="getCustomer(this.value)">
                                                         <option value="">Select Customer Type <?= $customer_type; ?></option>
                                                         <?php
                                                         $cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
                                                         if ($cust_R) {
                                                            while ($C = mysqli_fetch_assoc($cust_R)) {
                                                         ?>
                                                               <option <?= ($customer_type == $C['id']) ? "selected" : ""; ?> value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
                                                         <?php
                                                            }
                                                         }
                                                         ?>
                                                      </select>
                                                      <p class="help-block"></p>
                                                   </div>
                                                   <div class="form-group">
                                                      <label>Select Customer<code>*</code></label>
                                                      <?php
                                                      if ($_REQUEST['mode'] == "edit" || (isset($_REQUEST['quotation_id']) && $_REQUEST['mode'] == "add")) {
                                                      ?>
                                                         <input type="hidden" name="edit_dealer_id" id="edit_dealer_id" value="<?= $dealer_id; ?>" class="customer_id_s">
                                                      <?php
                                                      } else if ((isset($_REQUEST['inquiry_id']) && $_REQUEST['mode'] == "add")) {
                                                      ?>
                                                         <input type="hidden" name="dealer_id" id="dealer_id1" value="<?= $dealer_id; ?>" class="customer_id_s">
                                                      <?php
                                                      } else if ((isset($_REQUEST['customer_id']) && $_REQUEST['mode'] == "add")) {
                                                      ?>
                                                         <input type="hidden" name="dealer_id" id="dealer_id1" value="<?= $dealer_id; ?>" class="customer_id_s">
                                                      <?php
                                                      }
                                                      ?>
                                                      <select class="form-control customer_id_s" name="dealer_id1" placeholder="Select Customer" id="dealer_id" type="text" <?php echo $disabled; ?> onChange="getCategory(this.value)">
                                                         <option value="">Select Customer <?php echo $disabled; ?></option>
                                                         <?php
                                                         if ($_REQUEST['mode'] == "edit" || (isset($_REQUEST['quotation_id']) && $_REQUEST['mode'] == "add")  || (isset($_REQUEST['inquiry_id']) && $_REQUEST['mode'] == "add") || (isset($_REQUEST['customer_id']) && $_REQUEST['mode'] == "add")) {

                                                            $customers = $db->rp_getData("executive", "*", "isDelete=0 AND type_of_executive='" . $customer_type . "'", "company_name ASC", 0);


                                                            if ($customers) {
                                                               while ($customer = mysqli_fetch_assoc($customers)) {
                                                                  if ($customer['price_list_id'] != 0) {
                                                                     $price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . $customer['price_list_id'] . "'");
                                                                  } else {
                                                                     $price_list_name = "N/A";
                                                                  }

                                                                  /*for merchnt export*/
                                                                  if ($customer['type_of_executive'] == 8) {
                                                                     if (strtolower(CLIENT_STATE) == strtolower($customer['state'])) {
                                                                        $gst_type = "(CGST:0.05%,SGST:0.05%)";
                                                                     } else {
                                                                        $gst_type = "(IGST:0.1%)";
                                                                     }
                                                                  }
                                                                  /*for merchnt export*/ else {
                                                                     if (strtolower(CLIENT_STATE) == strtolower($customer['state'])) {
                                                                        $gst_type = "(CGST:9%,SGST:9%)";
                                                                     } else {
                                                                        $gst_type = "(IGST:18%)";
                                                                     }
                                                                  }
                                                                  $customer_type1 = $db->rp_getValue("customer_type", "name", "id='" . $customer['type_of_executive'] . "'");
                                                         ?>
                                                                  <option value="<?php echo $customer['id']; ?>" <?php if ($dealer_id == $customer['id']) {
                                                                                                                     echo "selected";
                                                                                                                  } ?> data-phone="<?php echo $customer['phone'] ?>" data-email="<?php echo $customer['email'] ?>" data-address="<?php echo $customer['address'] ?>" data-state="<?php echo $customer['state'] ?>" data-cname="<?= $customer['cname'] ?>" data-company_name="<?= $customer['company_name'] ?>" data-gstin="<?= $customer['gst'] ?>" data-price-list="<?= $price_list_name; ?>
																					" data-cutomer-type="<?= $customer_type1; ?>" data-gst-type="<?= $gst_type ?>" data-channel-partner-flag="<?php echo ((int) $customer['channel_partner_flag'] === 1) ? '1' : '0'; ?>">
                                                                     <?php echo $customer['company_name'] . " - " . $customer['cname']; ?>
                                                                  </option>
                                                                  <?php
                                                               }
                                                            }
                                                         } else {
                                                            /* Add mode: do not preload all customers (11k+ rows hangs page).
                                                             * Customers load via getCustomer() AJAX after Customer Type is selected. */
                                                         }
                                                         ?>
                                                      </select>
                                                      <p class="help-block"></p>
                                                   </div>
                                                   <!-- CUSTOMER EDIT BUTTON -->
                                                   <?php
                                                   if ($_REQUEST['mode'] == "edit" && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                                                   ?>
                                                      <div class="form-group" style="text-align: right;margin-right: 64px;">
                                                         <?php
                                                         $Check_Order = $db->rp_getTotalRecord("orders", "quotation_id='" . $_REQUEST['id'] . "' AND isDelete=0");
                                                         if ($Check_Order == 0) {
                                                         ?>
                                                            <a class="btn btn-primary" href='#CustomerChangeModel' data-title="Customer Details" data-customer_id="<?= $dealer_id; ?>" data-mode="quotation" data-quotation_id="<?= $_REQUEST['id']; ?>" data-toggle='modal'><i class="fa fa-pencil"></i> Change Customer</a>
                                                         <?php
                                                         }
                                                         ?>
                                                         <a class="btn btn-success" href='#CustomerEditModel' data-title="Customer Details" data-customer_id="<?= $dealer_id; ?>" data-mode="quotation" data-quotation_id="<?= $_REQUEST['id']; ?>" data-toggle='modal'><i class="fa fa-edit"></i> Edit Customer</a>
                                                      </div>
                                                   <?php
                                                   }
                                                   ?>
                                                   <!-- CUSTOMER EDIT BUTTON -->

                                                   <div class="form-group">
                                                      <p><b>Total Quotation Count : </b><span id="tot_quotation_cnt"></span></p>
                                                      <p><b>Monthly Quotation Count : </b><span id="monthly_quotation_cnt"></span></p>
                                                      <p></p>
                                                   </div>
                                                   <div class="form-group">
                                                      <label class="test">Select Terms & Condition <code>*</code></label>
                                                      <select class="form-control" id="terms_condition_id" name="terms_condition_id" onchange="getTermsDescr()">
                                                         <option value="">Select Terms & Condition</option>
                                                         <?php
                                                         $terms_r = $db->rp_getData("terms_condition", "*", "isDelete=0");
                                                         if ($terms_r) {
                                                            while ($terms_d = mysqli_fetch_assoc($terms_r)) {
                                                         ?>
                                                               <option <?= ($terms_condition_id == $terms_d['id']) ? "selected" : ""; ?> value="<?= $terms_d['id']; ?>"><?= $terms_d['name']; ?></option>
                                                         <?php
                                                            }
                                                         }
                                                         ?>
                                                      </select>
                                                      <p class="help-block"></p>
                                                   </div>
                                                   <div class="form-group">
                                                      <div class="row static-info phone">
                                                         <div class="col-md-5 name"> Firm Name : </div>
                                                         <div class="col-md-7 value" name="company_name" id="company_name"><?php echo $company_name; ?> </div>
                                                      </div>
                                                      <div class="row static-info phone">
                                                         <div class="col-md-5 name"> Person Name : </div>
                                                         <div class="col-md-7 value" name="name" id="name"><?php echo $customer_name; ?> </div>
                                                      </div>
                                                      <div class="row static-info phone">
                                                         <div class="col-md-5 name"> Phone : </div>
                                                         <div class="col-md-7 value" name="name_phone" id="name_phone"><?php echo $contact_number; ?> </div>
                                                      </div>
                                                      <div class="row static-info address">
                                                         <div class="col-md-5 name"> Address : </div>
                                                         <div class="col-md-7 value" name="name_address" id="name_address"><?php echo $address; ?></div>
                                                      </div>
                                                      <div class="row static-info address">
                                                         <div class="col-md-5 name"> State : </div>
                                                         <div class="col-md-7 value" name="name_state" id="name_state"><?php echo $customer_state; ?></div>
                                                      </div>
                                                      <div class="row static-info address">
                                                         <div class="col-md-5 name"> GSTIN : </div>
                                                         <!-- <div class="col-md-7 value" name="name_gstin" id="name_gstin">
                                                                 <?php echo $customer_gstin; ?>
                                                                 <input class="form-control" type="text" name="gstin" id="gstin" value="<?= $gstin ?>">
                                                              </div> -->
                                                         <div class="col-md-7">
                                                            <input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php echo $name_gstin ?>" maxlength="15">
                                                            <p class="help-block"></p>
                                                         </div>
                                                      </div>
                                                      <div class="row static-info address">
                                                         <div class="col-md-5 name"> Pricelist : </div>
                                                         <div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php echo $customer_pricelist; ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                   <div class="form-group">
                                                      <label>Shipping Address</label>
                                                      <a class="btn-sm btn-success" href='#CustomerChangeShippingAddressModel' data-title="Customer Details" data-customer_id="<?= $dealer_id; ?>" data-mode="quotation_change_shipping" data-quotation_id="<?= $_REQUEST['id']; ?>" data-toggle='modal'><i class="fa fa-edit"></i> Change Shipping </a>
                                                      <textarea class="form-control" id="shipping_address" name="shipping_address" value="<?php $shipping_address ?>" rows="6"><?php echo $shipping_address ?></textarea>
                                                      <p class="help-block"></p>
                                                   </div>
                                                   <div class="form-group">
                                                      <label>Billing Address</label>
                                                      <textarea class="form-control" id="billing_address" name="billing_address" value="<?php $billing_address ?>" rows="6"><?php echo $billing_address ?></textarea>
                                                      <p class="help-block"></p>
                                                   </div>
                                                </div>
                                                <div class="col-md-4 col-sm-4">
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>Quotation No. <code>*</code></label>
                                                         <input type="text" readonly="" class="form-control" name="quotation_no" id="quotation_no" value="<?php echo $quotation_no; ?>" />
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>Quotation Date <code>*</code></label>
                                                         <input type="text" readonly="" class="form-control" name="quotation_date" id="quotation_date" value="<?php if ($_REQUEST['mode'] == 'add') {
                                                                                                                                                                  echo date('d-m-Y');
                                                                                                                                                               } else {
                                                                                                                                                                  echo $quotation_date;
                                                                                                                                                               } ?>" />
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>vendor no / rfq no</label>
                                                         <input style="resize: vertical;" type="text" class="form-control" name="vendor_code" id="vendor_code" value="<?php echo $vendor_code ?>">
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>Tendor Code</label>
                                                         <input style="resize: vertical;" type="text" class="form-control" name="tendor_code" id="tendor_code" value="<?php echo $tendor_code ?>">
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>Tendor No</label>
                                                         <input style="resize: vertical;" type="text" class="form-control" name="tendor_no" id="tendor_no" value="<?php echo $tendor_no ?>">
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label>Transport By</label>
                                                         <!-- update code - sagar  -->
                                                         <input type="hidden" id="transport_name_selected_id" value="<?= $transport_name ?>">
                                                         <!-- update code - sagar  -->
                                                         <select class="form-control" name="transport_through" id="transport_through" onchange="getTransportname(this.value);">
                                                            <option value="">Select Transport By</option>
                                                            <?php
                                                            $transport_by_r = $db->rp_getData("transport_by", "*", "isDelete=0", "name ASC");
                                                            if (mysqli_num_rows($transport_by_r) > 0) {
                                                               while ($transport_by_d = mysqli_fetch_array($transport_by_r)) {
                                                            ?>
                                                                  <option value="<?php echo $transport_by_d['id']; ?>" <?php if ($transport_by_d['id'] == $transport_through) { ?> selected <?php } ?>><?php echo $transport_by_d['name']; ?></option>
                                                            <?php
                                                               }
                                                            }
                                                            ?>
                                                         </select>
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6" style="margin-top:30px;">
                                                      <div class="form-group">
                                                         <label>Transporter Detail</label>
                                                         <select class="form-control" name="transport_name" id="transport_name">
                                                            <option value="">Select Transporter Detail</option>
                                                         </select>
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-6">
                                                      <div class="form-group">
                                                         <label for="#"><b>Add Attachment</b></label>
                                                         <input data-image="<?php echo ($attachment != "" && file_exists(QUOTATION_ATTACHMENT_A . $attachment)) ? QUOTATION_ATTACHMENT_A . $attachment : ""; ?>" type="file" name="attachment" id="attachment" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $attachment ?>" value="" multiple>
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <div class="col-md-12">
                                                      <div class="form-group">
                                                         <label>Sales Executive <code>*</code></label>
                                                         <select class="form-control" onchange="getRegards(this)" name="sales_executive" id="sales_executive">
                                                            <option value="">Select Sales Executive</option>
                                                            <?php
                                                            if ($sales_executive_id == "" || $sales_executive_id == 0 || $sales_executive_id == NULL || empty($sales_executive_id)) {
                                                               $sales_executive_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
                                                            }

                                                            $sales_R = $db->rp_getData("sales_executive", "id,name,phone", "isDelete=0 AND isActive=1");
                                                            if ($sales_R) {
                                                               while ($sales_d = mysqli_fetch_assoc($sales_R)) {
                                                            ?>
                                                                  <option data-regrads="<?php echo $sales_d['name'] . " <br> " . $sales_d['phone'] ?>" value="<?php echo $sales_d['id'] ?>" <?php echo $sales_d['id'] == $sales_executive_id ? "selected" : ''; ?>><?php echo $sales_d['name'] . " - " . $sales_d['phone'] ?></option>
                                                            <?php
                                                               }
                                                            }
                                                            ?>
                                                         </select>
                                                         <p class="help-block"></p>
                                                      </div>
                                                   </div>
                                                   <!-- <div class="col-md-6">
                                                           <div class="form-group">
                                                              <label>Select Currency</label>
                                                              <select class="form-control" name="currency_code" id="currency_code">
                                                                 <option value="">Select Currency</option>
                                                                 <option selected value="1">INR</option>
                                                                 <option <?= ($currency_code == "2") ? "selected" : "" ?> value="2">USD</option>
                                                              </select>
                                                              <p class="help-block"></p>
                                                           </div>
                                                        </div>  -->
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12 col-sm-12">
                                 <div class="portlet grey-cascade box">
                                    <div class="portlet-title">
                                       <div class="caption">
                                          <i class="fa fa-user"></i>
                                          <span class="caption-subject bold uppercase"> QUOTATION ITEM</span>
                                       </div>
                                    </div>
                                    <div class="portlet-body">
                                       <div class="row">
                                          <div class="col-sm-12">
                                             <div class="row">
                                                <div class="col-md-12">
                                                   <div class="form-body">
                                                      <div class="form-group">
                                                         <div class="row">
                                                            <div class="col-md-12">
                                                               <div class="row">
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <label>Category <code>*</code></label>
                                                                        <select class="form-control" name="category_id" id="category_id">
                                                                           <option value="">select Category</option>
                                                                           <?php
                                                                           $cat_r = $db->rp_getData("top_category_master", "*", "isDelete=0 AND isActive=1", 1);
                                                                           while ($cat_d = mysqli_fetch_assoc($cat_r)) {
                                                                           ?>
                                                                              <option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
                                                                           <?php
                                                                           }
                                                                           ?>
                                                                        </select>
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <label>Products <code>*</code></label>
                                                                        <select class="form-control" name="product_id" id="product_id">
                                                                           <option value="">select product</option>
                                                                        </select>
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <label>Select Brand <code>*</code></label>
                                                                        <select class="form-control" name="order_item_brand" id="order_item_brand">
                                                                           <option value="">Select Brand</option>
                                                                           <?php
                                                                           $orderItemBrand_R = $db->rp_getData("order_item_brand_master", "id,name", "isDelete=0 AND isActive=1");
                                                                           if ($orderItemBrand_R) {
                                                                              while ($orderItemBrand_D = mysqli_fetch_assoc($orderItemBrand_R)) {
                                                                           ?>
                                                                                 <option data-order_item_brand_name="<?= $orderItemBrand_D['name'] ?>" value="<?= $orderItemBrand_D['id'] ?>"><?= $orderItemBrand_D['name'] ?></option>
                                                                           <?php
                                                                              }
                                                                           }
                                                                           ?>
                                                                        </select>
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <label>Select Order Unit <code>*</code></label>
                                                                        <select class="form-control" name="bag_box_id" id="bag_box_id">
                                                                           <option value="">Select Order Unit</option>
                                                                           <!-- <option value="2">Box Qty</option>
                                                                                <option value="3">Caret Qty</option>
                                                                                <option value="1">Qty</option> -->
                                                                        </select>
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <label>Quantity<code>*</code></label>
                                                                        <input type="text" class="form-control positive" name="qty" id="qty" value="" />
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-1 hidden" style="width:7%important">
                                                                     <div class="form-group" style="margin-top:21px;">
                                                                        <label><b>Box <br>Qty : </b> </label>
                                                                        <span class='inner_size'></span>
                                                                        <br /><label><b>Caret <br>Qty : </b></label>
                                                                        <span class='outer_size'></span>
                                                                        <br /><label><b>Qty : </b></label>
                                                                        <span class='qty'></span>
                                                                        <input type="hidden" id="final_qty" value="">
                                                                     </div>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                     <div class="form-group">
                                                                        <br /><button class="btn btn-primary" type="button" id="add">ADD</button>
                                                                        <p class="help-block"></p>
                                                                     </div>
                                                                  </div>
                                                                  <!--  <div class="col-md-2" style="margin-left: -144px;">
                                                                          <div class="form-group">
                                                                             <br /><button class="btn btn-success" type="button" id="add_discount">ADD Category Discount</button>
                                                                             <p class="help-block"></p>
                                                                          </div> -->
                                                               </div>
                                                               <!-- <div class="col-md-2">
                                                                          <div class="form-group">
                                                                             <br /><button class="btn btn-danger" type="button" id="add_topcat_discount">ADD Top Category Discount</button>
                                                                             <p class="help-block"></p>
                                                                          </div>
                                                                       </div> -->
                                                            </div>
                                                         </div>
                                                      </div>
                                                      <div class="row">
                                                         <div class="col-md-12" style="overflow:scroll;">
                                                            <table border="1px" id="datatable_1" class="table table-striped table-bordered table-hover">
                                                               <thead>
                                                                  <tr>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th></th>
                                                                     <th>Total QTY</th>
                                                                     <th>
                                                                        <input type="text" style="text-align: right;width: 80px;" id="sum_qty" class="form-control" disabled value="<?php echo $qty_total; ?>">
                                                                     </th>
                                                                     <th hidden>Average</th>
                                                                     <th hidden>
                                                                        <input type='text' style="text-align:right;" align="right" class="average_value form-control" disabled value="">
                                                                     </th>
                                                                     <th></th>
                                                                     <th>Total Amount</th>
                                                                     <th><input type='text' style="text-align:right;" id='finalTotal' align="right" class="form-control" disabled value="<?php echo $db->rp_number_format($total_amount); ?>" name='finalTotal[]'></th>
                                                                  </tr>
                                                                  <tr>
                                                                     <!-- <th>No.</th> -->
                                                                     <th style="padding: 1px!important;" class="text-center">Delete</th>
                                                                     <th style="padding: 1px!important;" class="" width="200px;">Product Name</th>
                                                                     <th style="padding: 1px!important;" class="text-center" width="100px;">Brand
                                                                        <?php if ($_REQUEST['mode'] == 'edit'): ?>
                                                                           <input type="checkbox" id="changeallbrand" />
                                                                        <?php endif; ?>
                                                                     </th>
                                                                     <th style="padding: 1px!important;" class="" width="50px;">Unit</th>
                                                                     <th style="padding: 1px!important;" class="text-center">Order Qty</th>
                                                                     <th hidden style="padding: 1px!important;" class="text-center">Box Qty<br />(NOS)</th>
                                                                     <th hidden style="padding: 1px!important;" class="text-center">Caret Qty<br />(in NOS)</th>
                                                                     <th hidden style="padding: 1px!important;" class="text-center">Loose Qty<br /><span class="f-10">(in NOS)</span></th>
                                                                     <th hidden style="padding: 1px!important;" class="text-center">Stock</th>
                                                                     <th style="padding: 1px!important;" class="" width="100px;">Product <br /> Description</th>
                                                                     <th style="padding: 1px!important;" class="text-center" width="50px;">HSN Code</th>
                                                                     <th style="padding: 1px!important;" class="text-center">Qty<br /><span class="f-10">(in NOS)</span></th>
                                                                     <th style="padding: 1px!important;" class="text-center">Price<br /><span class="f-10">(in INR)</span></th>
                                                                     <th style="padding: 1px!important;" class="text-center">Dis(Flat)</th>
                                                                     <th style="padding: 1px!important;" class="text-center">Dis(%)</th>
                                                                     <th style="padding: 1px!important;" class="text-center">RATE<br /><span class="f-10">(in INR)</span></th>
                                                                     <th style="padding: 1px!important;" class="text-center" width="150px;">Amount</th>
                                                                     <!-- <th style="padding: 1px!important;" class="text-center" width="150px;">CD DIS</th> -->
                                                                     <!-- <th style="padding: 1px!important;" class="text-center" width="150px;">AD DIS</th>  -->
                                                                     <th style="padding: 1px!important;" class="text-center" width="150px;">Taxable Amount</th>
                                                                     <th style="padding: 1px!important;" class="text-center" width="150px;">GST Amount</th>
                                                                     <th style="padding: 1px!important;min-width: 41px;" class="text-center" width="150px;"></th>
                                                                     <th style="padding: 1px!important;" class="text-center" width="150px;">Sub Total</th>
                                                                  </tr>
                                                               </thead>
                                                               <tbody>
                                                                  <?php
                                                                  $price_list_id = $db->rp_getValue("executive", "price_list_id", "id='" . $dealer_id . "'", 0);
                                                                  $is_premium = $db->rp_getValue("price_list", "is_premium", "id='" . $price_list_id . "'", 0);
                                                                  // print_r($item_info); exit;
                                                                  if (!empty($item_info)) {
                                                                     $total_amount = 0;
                                                                     $total_gst = $igst_amount;
                                                                     foreach ($item_info as $i) {

                                                                        $box_qty += $i['box_qty'];
                                                                        $qty_total += $i['qty'];
                                                                        $total_amount += $i['product_total'];
                                                                        $discount_amount = $i['discount_amount'];
                                                                        $final_price = $total_amount;
                                                                        //$total = $subtotal;

                                                                        $top_cat_id = $db->rp_getValue("product", "tcid", "id='" . $i['product_id'] . "'");

                                                                        $unit_id = $db->rp_getValue("product", "unit_id", "id='" . $i['product_id'] . "'");
                                                                        $unit_name = $db->rp_getValue("unit", "name", "id='" . $unit_id . "'");
                                                                        if ($i['is_including'] == 1) {
                                                                           $get_pro_price = "Including Gst : " . $db->rp_getValue("product_weight_price", "price", "isDelete=0 AND product_id='" . $i['product_id'] . "' AND weight_id='" . $i['weight_id'] . "'");
                                                                        } else {
                                                                           $get_pro_price = "";
                                                                        }
                                                                  ?>
                                                                        <tr>
                                                                           <td style="padding: 1px!important;" class="text-center">
                                                                              <?php
                                                                              $total_dispatch_record = $db->rp_getTotalRecord("dispatch_map_order", "quotation_id='" . $i['quotation_id'] . "' AND isDelete=0", 0);
                                                                              if ($total_dispatch_record > 0) {
                                                                              } else {
                                                                              ?>
                                                                                 <a style="padding: 6px 9px 6px 9px;!important" class='delete btn btn-danger btn-sm' title='Delete'><i class='fa fa-times'></i></a>
                                                                              <?php
                                                                              }
                                                                              ?>
                                                                           </td>
                                                                           <td style="min-width: 187px;padding: 1px!important;">
                                                                              <input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
                                                                              <input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">
                                                                              <input type='hidden' style="text-align:right" name='subtotal[]' value="">
                                                                              <input type='hidden' style="text-align:right" name='total[]' value="">
                                                                              <input type='hidden' style="text-align:right" name='item_name[]'>
                                                                              <input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
                                                                              <input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
                                                                              <input type='hidden' name='brand_id[]' value="<?php echo $i['brand_id']; ?>" id='brand_id'>
                                                                              <input type='hidden' class="cid" name='cid[]' value="<?php echo $i['cid']; ?>" id='cid'>
                                                                              <input class="tcid" type='hidden' name='tcid[]' value="<?php echo $i['tcid']; ?>" id='tcid'>
                                                                              <?php echo $i['product_name'] . " - " . $i['cat_no'] . " - <br/>" . $i['top_cat_name'] . " - " . $i['category_name']; ?>
                                                                           </td>
                                                                           <td class='text-center' style="width: 100px;text-align: center;padding: 1px!important;"><a href='#brandChange' data-item_idfbrand="<?php echo $i['item_idfbrand'] ?>" data-toggle='modal'><input type='hidden' class="order_item_brand_id" name='order_item_brand_id[]' value="<?php echo $i['order_item_brand_id']; ?>" /><?php echo $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $i['order_item_brand_id'] . "'") ?></a></td>
                                                                           <td style="padding: 1px!important"><?= $unit_name ?><input type='hidden' name='item_order_unit[]' class='item_order_unit' value='<?= $i['item_order_unit'] ?>'></td>
                                                                           <td style='padding: 1px!important;' ; class='text-center'><input type='text' name='order_qty[]' class='form-control positive  order_qty' style='text-align:right;width:80px;' value='<?= $i['order_qty'] ?>' onChange='recalculateRow(this)' id='order_qty' /></td>
                                                                           <!-- <td style="padding: 1px!important;"><?= $unit_name ?></td> -->
                                                                           <td hidden style="text-align:right;padding: 1px!important;">
                                                                              <input class="inner_size" type='hidden' name='inner_size[]' value="<?php echo $i['inner_size']; ?>">
                                                                              <input readonly name='bag[]' class='form-control bag positive' style="text-align:right;width:70px;" type='text' value="<?php echo $i['bag']; ?>">
                                                                           </td>
                                                                           <td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='outer_size' class='outer_size' value="<?php echo $i['outer_size']; ?>"><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>"></td>
                                                                           <td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='loose_qty' class='loose_qty' value="<?php echo $i['loose']; ?>"><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value="<?php echo $i['loose']; ?>"></td>
                                                                           <td hidden style="padding: 1px!important;" class='text-center'><?php echo $i['stock']; ?></td>
                                                                           <td style="padding: 1px!important;">
                                                                              <textarea rows="2" cols="10" id="pro_description" name="pro_description[]" style="margin: 0!important;"><?= $i['pro_description'] ?></textarea>
                                                                           </td>
                                                                           <td style="padding: 1px!important;" class='text-center'><?php echo $i['hsn_code']; ?></td>
                                                                           <td style="text-align:right;padding: 1px!important;">
                                                                              <input readonly type='text' style="text-align:right;width: 80px;" onChange='recalculateRow(this)' class='qty form-control' name='qty[]' value="<?php echo $i['qty']; ?>">
                                                                              <input class='new_qty' type='hidden' value="<?php echo $i['qty']; ?>" id='qty'>
                                                                           </td>
                                                                           <td style="text-align:right;padding: 1px!important;">
                                                                              <input type='hidden' value="<?php echo $i['original_price']; ?>" class='original_price_hidden' />
                                                                              <input name='original_price[]' class='original_price form-control' style="text-align:right;width: 80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['original_price']; ?>">
                                                                              <?php $i['original_price']; ?>
                                                                              <input type="hidden" name="is_including[]" value="<?= $i['is_including'] ?>">
                                                                              <span>
                                                                                 <?= $get_pro_price ?>
                                                                              </span>
                                                                              <input type='hidden' value="<?= ($is_premium) ? $is_premium : 0; ?>" class='is_premium' />
                                                                           </td>
                                                                           <td style='text-align:right;padding: 1px!important;'>
                                                                              <?php
                                                                              if ($i['discount_per'] != 0) {
                                                                                 $discount_amt = 0;
                                                                              } else {
                                                                                 $discount_amt = $i['discount_amount'];
                                                                              }
                                                                              ?>
                                                                              <input type='text' name='discount_amount[]' style='text-align:right;width: 80px;' class='form-control discount_amount' onChange='recalculateRow(this,1)' value='<?php echo $discount_amt; ?>'>
                                                                           </td>
                                                                           <td style="text-align:right;padding: 1px!important;">
                                                                              <input type="text" style="text-align: right;width: 80px;" name="discount[]" class="form-control discount" onChange='recalculateRow(this,2)' value="<?php echo $i['discount_per']; ?>">
                                                                           </td>
                                                                           <td style="text-align:right;padding: 1px!important;">
                                                                              <input readonly name='price[]' class='price form-control' style="text-align:right;width: 80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['product_price']; ?>">
                                                                              <?php $i['product_price']; ?>
                                                                           </td>
                                                                           <td style="text-align:right;padding: 1px!important;">
                                                                              <input type='text' style="text-align:right;width: 150px;" disabled class='total form-control' disabled onChange='recalculateRow(this)' name='subtotal[]' value="<?php echo $i['product_total']; ?>">
                                                                           </td>
                                                                           <td class="hidden" style="padding: 1px!important;">
                                                                              <input style="width: 150px;text-align: right" readonly type='text' class='cd_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='cd_discount[]' value="<?php echo $i['cd_amount']; ?>">
                                                                           </td>
                                                                           <td class="hidden" style="padding: 1px!important;"><input style="width: 150px;text-align: right" readonly type='text' class='ad_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='ad_discount[]' value="<?php echo $i['ad_amount']; ?>"></td>
                                                                           <td class="hidden" style='padding: 1px!important;' ;><input readonly type='text' class='other_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='other_charge[]' value=<?php echo $i['other_charge']; ?>></td>
                                                                           <td class="hidden" style='padding: 1px!important;' ;><input readonly type='text' class='fright_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='fright_charge[]' value=<?php echo $i['fright_charge']; ?>></td>
                                                                           <td style="padding: 1px!important;"><input readonly type='text' class='taxable_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='taxable_amount[]' value="<?php echo $i['taxable_amount'] ?>"><input class='new_taxable' type='hidden' value='<?php echo $i['taxable_amount'] ?>' id='taxable_amount'></td>
                                                                           <td style="padding: 1px!important;"><input readonly type='text' class='gst_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='gst_amount_item[]' value="<?php echo $i['gst_amount_item'] ?>"></td>
                                                                           <?php
                                                                           /*for merchnt export*/
                                                                           if ($customer_type == 8) {
                                                                              $gst =  "0.1";
                                                                           } else {
                                                                              $gst = $i['gst'];
                                                                           }
                                                                           ?>
                                                                           <td style="text-align: center;padding:1px;"><input type='hidden' style='text-align:right;width: 80px;' id='gst_tax' class='gst_tax form-control' value='<?php echo $gst ?>'><?php echo $gst ?>%</td>
                                                                           <td style="padding: 1px!important;"><input readonly type='text' class='sub_total form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='sub_total[]' value="<?php echo $i['sub_total'] ?>"></td>
                                                                        </tr>
                                                                  <?php
                                                                     }
                                                                  }
                                                                  ?>
                                                               </tbody>
                                                               <tfoot>
                                                                  <tr>
                                                                     <td colspan="8" align="right"></td>
                                                                     <td style="text-align: right;">
                                                                        <!-- <input type="text" style="text-align: right;width: 80px;" id="sum_qty" class="form-control" disabled value="<?php echo $qty_total; ?>"> -->
                                                                     </td>
                                                                     <td></td>
                                                                     <td></td>
                                                                     <td></td>
                                                                     <td></td>
                                                                     <!-- <td style="text-align:right"> -->
                                                                     <!-- <input type='text' style="text-align:right;" id='finalTotal' align="right" class="form-control" disabled value="<?php echo $db->rp_number_format($total_amount); ?>" name='finalTotal[]'> -->
                                                                     <!-- </td> -->
                                                                     <td class="hidden" style="text-align:right">
                                                                        <input type='text' style="text-align:right;" id='cd_total_sum' align="right" class="form-control" disabled value="" name='cd_total_sum[]'>
                                                                     </td>
                                                                     <td class="hidden" style="text-align:right">
                                                                        <input type='text' style="text-align:right;" id='ad_total_sum' align="right" class="form-control" disabled value="" name='ad_total_sum[]'>
                                                                     </td>
                                                                     <td style="text-align:right">
                                                                        <input type='text' style="text-align:right;" id='taxable_total_sum' align="right" class="form-control" disabled value="" name='taxable_total_sum[]'>
                                                                     </td>
                                                                     <td style="text-align:right">
                                                                        <input type='text' style="text-align:right;" id='gst_total_sum' align="right" class="form-control" disabled value="" name='gst_total_sum[]'>
                                                                     </td>
                                                                     <td></td>
                                                                     <td style="text-align:right">
                                                                        <input type='text' style="text-align:right;" id='sub_total_sum' align="right" class="form-control" disabled value="" name='sub_total_sum[]'>
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="12" align="right"></td>
                                                                     <td style="text-align: right;">
                                                                        <!-- <input type='text' style="text-align:right;" align="right" class="average_value form-control" disabled value=""> -->
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="12" align="right">Cash Discount (%)</td>
                                                                     <td style="text-align: right;">
                                                                        <input type="text" style="text-align: right;width: 100px" id="cash_discount" name="cash_discount" onChange='cdadCalculate()' class="form-control" value="<?php echo ($cash_discount > 0) ? $cash_discount : ""; ?>">
                                                                     </td>
                                                                     <td style="text-align: right;">
                                                                        <input type="text" style="text-align: right;" id="cash_discount_amount" onChange='cdadCalculate()' name="cash_discount_amount" class="form-control cd_calculate" value="<?php echo $cash_discount_amount; ?>">
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="12" align="right">Additional Discount (%)</td>
                                                                     <td style="text-align: right;">
                                                                        <input type="text" style="text-align: right;" id="additional_discount" name="additional_discount" onChange='cdadCalculate()' class="form-control" value="<?php echo ($additional_discount > 0) ? $additional_discount : ""; ?>">
                                                                     </td>
                                                                     <td style="text-align: right;">
                                                                        <input type="text" style="text-align: right;" id="addtional_discount_amount" onChange='cdadCalculate()' name="addtional_discount_amount" class="ad_calculate form-control" value="<?php echo $additional_discount_amount; ?>">
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="13" align="right" hidden>Packing & Forwarding Charge</td>
                                                                     <td style="text-align: right;" hidden>
                                                                        <input type="text" style="text-align: right;" id="packing_charge" name="packing_charge" onChange='recalculateRow(this)' class="form-control packing_calculate" value="<?php echo $packing_charge; ?>">
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="13" align="right" hidden>Transport</td>
                                                                     <td style="text-align: right;" hidden>
                                                                        <input type="text" style="text-align: right;" id="transport_charge" name="transport_charge" onChange='recalculateRow(this)' class="form-control transport_calculate" value="<?php echo $transport_charge; ?>">
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan="13" align="right">Total Taxable</td>
                                                                     <td style="text-align: right;">
                                                                        <input type="text" disabled style="text-align: right;" id="total1" class="form-control total1" value="<?php echo $db->rp_number_format($subtotal); ?>">
                                                                     </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan='12' align="right"></td>
                                                                     <td align="right">GST On Off Switch
                                                                        <input type="checkbox"
                                                                           <?php
                                                                           if ($_REQUEST['mode'] == "add" && $_REQUEST['inquiry_id'] != "") {
                                                                              echo "checked";
                                                                              $gst_apply_flag = 1;
                                                                           } else if ($_REQUEST['mode'] == "add") {
                                                                              echo "checked";
                                                                              $gst_apply_flag = 1;
                                                                           } else if ($igst_amount == 0) {
                                                                              echo "";
                                                                              $gst_apply_flag = 0;
                                                                           } else {
                                                                              echo "checked";
                                                                              $gst_apply_flag = 1;
                                                                           }
                                                                           ?>
                                                                           name="gst_apply" id="gst_apply">
                                                                        <input type="hidden" name="gst_apply" id="gst_apply" value="<?= $gst_apply ?>">
                                                                        <input type="hidden" name="gst_apply_flag" id="gst_apply_flag" value="<?= $gst_apply_flag ?>">
                                                                     </td>
                                                                     <td style="text-align:right">
                                                                        <input type="hidden" class="item_gst_amount" value="">
                                                                        <input type='text' style="text-align:right;width:150px;" id='gst_amount' align="right" class="form-control" readonly value="<?= $igst_amount ?>" name='gst_amount'>
                                                                     </td>
                                                                     <td></td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan='13' align="right">TCS On Off Switch
                                                                        <input type="checkbox"
                                                                           <?php
                                                                           if ($tcs_amount == 0) {
                                                                              echo "";
                                                                              $tcs_apply_flag = 0;
                                                                           } else {
                                                                              echo "checked";
                                                                              $tcs_apply_flag = 1;
                                                                           }
                                                                           ?>
                                                                           name="tcs_apply" id="tcs_apply">
                                                                        <input type="hidden" name="tcs_apply" id="tcs_apply" value="<?= $tcs_apply ?>">
                                                                        <input type="hidden" name="tcs_apply_flag" id="tcs_apply_flag" value="<?= $tcs_apply_flag ?>">
                                                                     </td>
                                                                     <td style="text-align:right">
                                                                        <input readonly type='text' class="form-control tcs_put" style="text-align:right;width:150px;" name='tcs_amount' id='tcs_amount' value="">
                                                                     </td>
                                                                     <td></td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan='13' align="right">Round Off</td>
                                                                     <td style="text-align:right">
                                                                        <input id="round_off" name="round_off" type='text' style="text-align:right;width:150px;" class="form-control" readonly value="<?php echo $db->rp_number_format($roundoff); ?>">
                                                                     </td>
                                                                     <td></td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td colspan='13' align="right"> Grand Total </td>
                                                                     <td style="text-align:right">
                                                                        <input type='hidden' style="text-align:right" class="form-control" id='finalQty' disabled name='finalQty[]' value="<?php echo $total_qty; ?>">
                                                                        <input type='text' style="text-align:right;width:150px;" id='finalgrandTotal' class="form-control" disabled value="<?php echo $db->rp_number_format($grand_total); ?>" name='finalgrandTotal[]'>
                                                                     </td>
                                                                     <td></td>
                                                                  </tr>
                                                               </tfoot>
                                                            </table>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
                                       <div class="col-md-5">
                                          <div class="form-group">
                                             <label>Terms & Conditions<code>*</code></label>
                                             <textarea class="form-control" id="terms_comdition" rows="5" name="terms_comdition" style="resize: vertical;"><?= str_replace('rn', '', $terms_comdition); ?></textarea>
                                             <p class="help-block"></p>
                                          </div>
                                       </div>
                                       <div class="col-md-5">
                                          <div class="form-group">
                                             <label>Regards<code>*</code></label>
                                             <!--  <?php
                                                   if ($_REQUEST['mode'] == "edit") {
                                                      $disabled_regards = "disabled";
                                                   ?>
                                                                 <input type="hidden" name="faithfully" value="<?= $faithfully ?>">
                                                        <?php
                                                      } else {
                                                         $disabled_customer = "";
                                                      }
                                                         ?> -->

                                             <textarea readonly class="form-control" id="faithfully" name="faithfully" style="resize: vertical;"><?php if ($_REQUEST['mode'] == "add" && $quotation_id != "") {
                                                                                                                                                      echo $db->rp_getValue("quotation_detail", "faithfully", "id='" . $quotation_id . "'", 0);
                                                                                                                                                   } else if ($_REQUEST['mode'] == "add") {
                                                                                                                                                      echo $db->custom_html_entity_decode2($_SESSION[SITE_SESS . 'SESS_NAME'] . "<br/>" . $db->rp_getValue("sales_executive", "phone", "id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'"));
                                                                                                                                                   } else {
                                                                                                                                                      echo $faithfully;
                                                                                                                                                   } ?></textarea>
                                             <p class="help-block"></p>
                                          </div>
                                       </div>
                                       <div class="col-md-5">
                                          <div class="form-group">
                                             <label>Remarks</label>
                                             <textarea class="form-control" id="remarks" name="remarks" style="resize: vertical;"><?= $remarks; ?></textarea>
                                             <p class="help-block"></p>
                                          </div>
                                       </div>
                                       <div class="col-md-5" style="margin-top: 25px;">
                                          <button type="submit" name="submit" class="btn green">Submit</button>
                                          <button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $back; ?>'">Back</button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
         </div>
      </div>
   </div>
   </form>
   </div>
   </div>
   </div>
   <!-- Brand Item Change Modal -->
   <div id="brandChange" class="modal fade" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-body portlet box blue">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="fa fa-gift"></i>Brand Change
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
   <!-- Brand Item Change Modal -->

   <?php include("footer.php"); ?>
   <?php include("include_js.php"); ?>
   <script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
   <script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
   <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
   <script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
   <script type="text/javascript" src="js/jquery.numeric.min.js"></script>
   <script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
   <script type="text/javascript" src="js/fSelect.js"></script>
   <script type="text/javascript">
      $(".discount_amount").numeric();
      $(".discount").numeric();
      $(".original_price").numeric();
      $(".price").numeric();
   </script>
   <script type="text/javascript">
      /*Customer Chnage Shipping address*/
      $('#CustomerChangeShippingAddressModel').on('show.bs.modal', function(event) {
         // alert("hello")
         var button = $(event.relatedTarget) // Button that triggered the modal                
         var customer_id = $('#dealer_id').val();
         var quotation_id = button.data("quotation_id");
         var mode = button.data("mode");
         $("#requesting_ajax_change_shipping").attr("data-url", "customer_change_shipping_get_ajax.php?customer_id=" + customer_id + "&id=" + quotation_id + "&mode=" + mode);
         $("#requesting_ajax_change_shipping").click();
      })
      /*Customer Chnage Shipping address*/

      $('#brandChange').on('show.bs.modal', function(event) {
         var changeallbrand = ($("#changeallbrand").prop('checked')) ? 'all' : '';
         var button = $(event.relatedTarget) // Button that triggered the modal
         var order_idfbrand = "<?php echo $_REQUEST['id'] ?>";
         var item_idfbrand = button.data("item_idfbrand");
         $("#requesting_ajax").attr("data-url", "brand_item_change_modal_ajax.php?mode=show&id=" + item_idfbrand + "&order_idfbrand=" + order_idfbrand + "&checkall=" + changeallbrand + "&reference_table=quotation_product_item");
         $("#requesting_ajax").click();
      })

      $("#changeallbrand").on('change', function() {
         ($("#changeallbrand").prop('checked')) ? $("#brandChange").modal("show"): false;
      });
   </script>
   <script type="text/javascript">
      $(document).ready(function() {
         var admintype = '<?= $_SESSION[SITE_SESS . '_ADMIN_TYPE'] ?>';
         if (admintype == "0") {
            CKEDITOR.config.readOnly = true;
         }
      });
      $(document).ready(function() {
         var admin = '<?= $_SESSION[SITE_SESS . '_ADMIN_TYPE'] ?>';
         if (admin != "0") {
            CKEDITOR.config.readOnly = true;
         }
      });
   </script>
   <script type="text/javascript">
      CKEDITOR.replace('terms_comdition');
      CKEDITOR.instances.terms_comdition.getData().replace(/(\r\n|\n|\r)/gm, ""); +

      CKEDITOR.replace('faithfully');
      CKEDITOR.instances.faithfully.getData().replace(/(\r\n|\n|\r)/gm, "");

      var mode = "<?= $_REQUEST['mode'] ?>";
      const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
         minimumFractionDigits: decimals,
         maximumFractionDigits: decimals,
      });

      var gst_apply_flag = 0;
      var gstamount = 0;
      var tcs_apply_flag = 0;
      var tcsamount = 0;

      $("#attn_no").numeric();
      $("#qty").numeric();
      $(".qty").numeric();
      $('#quotation_date').datepicker({
         datepicker: true,
         autoclose: true,
         dateFormat: 'dd-mm-yy'
      });
      $('#reference_date').datepicker({
         datepicker: true,
         autoclose: true,
         dateFormat: 'dd-mm-yy'
      });

      $("#dealer_id").change(function() {
         var customer_name = $("#dealer_id").find('option:selected').data("name");
         var phone = $("#dealer_id").find('option:selected').data("phone");
         var address = $("#dealer_id").find('option:selected').data("address");
         var place_of_supply = $("#dealer_id").find('option:selected').data("state");
         var cust_type = $("#dealer_id").find('option:selected').data("cutomer-type");
         var state = $("#dealer_id").find('option:selected').data("state");
         var gstin = $("#dealer_id").find('option:selected').data("gstin");
         var pricelist = $("#dealer_id").find('option:selected').data("price-list");
         var cname = $("#dealer_id").find('option:selected').data("cname");
         var company_name = $("#dealer_id").find('option:selected').data("company_name");
         var gst_type = $("#dealer_id").find('option:selected').data("gst-type");
         var shipping_add = $("#dealer_id").find('option:selected').data("shipping-add");
         var billing_add = $("#dealer_id").find('option:selected').data("billing-add");
         var customer_cash_discount = $("#dealer_id").find('option:selected').data("customer_cash_discount");
         var customer_additional_discount = $("#dealer_id").find('option:selected').data("customer_additional_discount");
         var shipping_addreses = $("#dealer_id").find('option:selected').data("shipping_address");
         var billing_address = $("#dealer_id").find('option:selected').data("billing_address");
         var transport_through = $("#dealer_id").find('option:selected').data("transport_thr");
         var transport_name = $("#dealer_id").find('option:selected').data("transport_name");

         $("#shipping_address").html(shipping_add);
         $("#shipping_address").html(shipping_addreses);
         $("#billing_address").html(billing_add);
         $("#billing_address").html(billing_address);
         //$("#place_of_supply").val(place_of_supply);
         $("#company_name").html(company_name);
         $("#name_value").html(customer_name);
         $("#name_phone").html(phone);
         $("#name_address").html(address);
         $("#name_state").html(state);
         // $("#name_gstin").html(gstin);
         $("#gstin").val(gstin);
         $("#name").html(cname);
         $("#name_pricelist").html(pricelist);
         $(".gst_type").html(gst_type);

         /*$('#transport_through').select2('destroy');
				$('#transport_through').val(transport_through);
				$('#transport_through').select2();

				getTransportname(transport_through,transport_name);*/
         var add_mode = "<?= $_REQUEST['mode'] ?>";

         if (mode == "add") {
            $("#name_gstin").val(gstin);
            $("#cash_discount").val(customer_cash_discount);
            $("#additional_discount").val(customer_additional_discount);
            $("#transport_through").select2("val", transport_name);
            getTransportname(transport_name, transport_through);

            var cusid = $("#dealer_id").val();
            // alert(cusid);
            getCustomerCount(cusid);
         }
      })

      function getCustomer(ctype) {
         var companytype = $("#type_of_company").val();
         $('#dealer_id').select2("val", "");
         if (!ctype) {
            $('#dealer_id').html('<option value="">Select Customer</option>');
            $('.preloader').fadeOut('slow');
            return;
         }
         $.ajax({
            type: "post",
            url: "ajax_get_customer.php",
            data: "customer_type=" + ctype + "&companytype=" + companytype,
            beforeSend: function() {
               $('.preloader').fadeIn('slow');
            },
            success: function(result) {
               setTimeout(function() {
                  $('#dealer_id').html(result);
                  $('.preloader').fadeOut('slow');
               });
            },
            error: function() {
               $('.preloader').fadeOut('slow');
               toastr.error("Failed to load customers. Please try again.");
            }
         })
      }

      function getCategory(cus_id) {
         refreshMaxItemDiscountFromCustomer();
         $.ajax({
            type: "post",
            url: "ajax_get_category_from_customer.php",
            data: "customer_id=" + cus_id,
            beforeSend: function() {
               // $(".transCover").fadeIn(800);
               $("#loading-modal").modal('show');
               // $('.preloader').fadeIn('slow');
            },
            success: function(result) {
               setTimeout(function() {
                  $('#category_id').html(result);
                  $("#loading-modal").modal('hide');
                  $('.preloader').fadeOut('slow');
               });
            }

         })
      }

      $("#category_id").on('change', function() {
         var tcid = $("#category_id").val();
         getProductList(tcid);
      });

      function getProductList(tcid) {
         var mode = '<?= $_REQUEST['mode'] ?>';
         var inquiry_id = '<?= $_REQUEST['inquiry_id'] ?>';
         if (mode == "edit") {
            var cid = $("#edit_dealer_id").val();
         } else if (mode == "add" && inquiry_id != "") {
            var cid = $("#dealer_id1").val();
         } else {
            var cid = $("#dealer_id").val();
         }
         $.ajax({
            type: "post",
            url: "ajax_get_product.php",
            data: "cid=" + cid + "&tcid=" + tcid,
            beforeSend: function() {
               $(".transCover").fadeIn(800);
               $('.preloader').fadeIn('slow');
            },
            success: function(result) {
               setTimeout(function() {
                  $('#product_id').html(result);
                  // $("#loading-modal").modal('hide');
                  $('.preloader').fadeOut('slow');
               });
            }
         })

         $("#product_id").change(function() {
            var pro_id = $("#product_id").find('option:selected').data('pro_id');
            $.ajax({
               type: "post",
               url: "ajax_get_order_unit_from_product.php",
               data: "pro_id=" + pro_id,
               success: function(result) {
                  $('#bag_box_id').html(result);

                  <?php
                  if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) {
                  ?>
                     var item_order_unit = $("#product_id").find('option:selected').data('item_order_unit');
                     // alert(item_order_unit);
                     $("#bag_box_id").select2("destroy");
                     $("#bag_box_id").val(item_order_unit);
                     //$("#bag_box_id").attr("disabled","");
                     $("#bag_box_id").select2();
                  <?php
                  }
                  ?>
               }
            })

            var inner_size = $("#product_id").find('option:selected').data('inner_size');
            $(".inner_size").html(inner_size);
            var outer_size = $("#product_id").find('option:selected').data('outer_size');
            $(".outer_size").html(outer_size);
         })

         $("#qty").change(function() {
            var bagids = $("#bag_box_id").val();
            var qtys = $("#qty").val();
            var inner_size = $("#product_id").find('option:selected').data('inner_size');
            var outer_size = $("#product_id").find('option:selected').data('outer_size');
            if (bagids == 2) {
               var new_qty_bag = inner_size * qtys;
               $(".qty").html(new_qty_bag);
            } else if (bagids == 3) {
               var new_qty_box = outer_size * qtys;
               $(".qty").html(new_qty_box);
            } else if (bagids == 1) {
               $(".qty").html(qtys);
            }
         })
         if (mode == "add") {
            var l = $("#datatable_1").find('tbody').find('tr').length;
         }
      }

      //--------------Calculation for qty ,total------------------------//
      <?php
      $quot_discount_customer_id = isset($dealer_id) ? (int) $dealer_id : 0;
      $MAX_ITEM_DISCOUNT_PCT = function_exists('cp_get_max_item_discount_percent')
         ? (int) cp_get_max_item_discount_percent($db, $quot_discount_customer_id, false)
         : 44;
      ?>
      var MAX_ITEM_DISCOUNT_PCT = <?php echo (int) $MAX_ITEM_DISCOUNT_PCT; ?>;

      function refreshMaxItemDiscountFromCustomer() {
         var cpFlag = 0;
         if ($("#dealer_id").length && $("#dealer_id").val()) {
            cpFlag = parseInt($("#dealer_id option:selected").attr("data-channel-partner-flag") || "0", 10) || 0;
         }
         MAX_ITEM_DISCOUNT_PCT = (cpFlag === 1) ? 50 : 44;
      }

      function recalculateRow(t, discount_type = "") {
         var row = $(t).parent('td').parent('tr');
         if (discount_type == 1) {
            $(row).find("td").find("input.discount").val("");
         } else if (discount_type == 2) {
            $(row).find("td").find("input.discount_amount").val("");
         } else {

         }
         var original_price = $(row).find("td").find("input.original_price").val();
         var original_price_hidden = $(row).find("td").find("input.original_price_hidden").val();
         var discount = $(row).find("td").find("input.discount").val();
         var item_gst = $(row).find("td").find("input.item_gst").val();
         var price = $(row).find("td").find("input.price").val();
         var old_price = $(row).find("td").find("input.old_price").val();
         var qty = $(row).find("td").find("input.qty").val();
         var inner_size = $(row).find("td").find("input.inner_size").val();
         var outer_size = $(row).find("td").find("input.outer_size").val();
         var box_qty = $(row).find("td").find("input.box_qty").val();
         var stock = $(row).find("input.stock_value").val();
         var cd_discount = $(row).find("input.cd_discount").val();
         var ad_discount = $(row).find("input.ad_discount").val();
         var other_charge = $(row).find("input.other_charge").val();
         var fright_charge = $(row).find("input.fright_charge").val();
         var bag_box_id = $("#bag_box_id").val();
         var gst_tax = $(row).find("input.gst_tax").val();
         var discount_amount_new = $(row).find("td").find("input.discount_amount").val();
         var item_order_unit = $(row).find("td").find("input.item_order_unit").val();
         var order_qty = $(row).find("td").find("input.order_qty").val();
         var changed_before_price = $(row).find("td").find("input.price").val();
         var is_premium = $(row).find("td").find("input.is_premium").val();
         // alert(is_premium);
         /*if(bag_box_id==""){
          var bag_box_id = 3;	
         }
         else{
          var bag_box_id = bag_box_id;
         }*/

         if (isNaN(cd_discount) || cd_discount == "NaN" || cd_discount == "") {
            cd_discount = 0;
         }

         if (isNaN(ad_discount) || ad_discount == "NaN" || ad_discount == "") {
            ad_discount = 0;
         }

         if (isNaN(other_charge) || other_charge == "NaN" || other_charge == "") {
            other_charge = 0;
         }

         if (isNaN(fright_charge) || fright_charge == "NaN" || fright_charge == "") {
            fright_charge = 0;
         }

         /*if(bag_box_id==2)
               {
                   var new_qty_bag = inner_size*qty;
                   $("#final_qty").val(new_qty_bag);
                   var bag = Math.floor(qty / inner_size);
                   bag = Math.floor(bag);
                   var box = 0;
                   var loose = 0;
               }
               else if(bag_box_id==3)
               {
                   var new_qty_box = outer_size*qty;
                   $("#final_qty").val(new_qty_bag);
                   //var qty = new_qty_box;
                   var box = Math.floor(qty / outer_size)
                   var bag = 0;
                   var loose = 0;           
               }
               else if(bag_box_id==1)
               {
                   $("#final_qty").val(qty);
                   var qty = qty;
                   var box = Math.floor(qty / outer_size);
                   if (box != 0) 
                   {
                       var bagqty = qty - (outer_size * box);
                       if (bagqty < 0)
                       {
                       bagqty = bagqty * -1;
                       }
                       bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
                       var bag = (bagqty / inner_size);
                       //bag = format(bag, 3);
                       bag = Math.floor(bag);
                   } 
                   else 
                   {
                       var bag = (qty / inner_size);
                       //bag = format(bag, 3);
                       bag = Math.floor(bag);
                   }
               }
            
               var total_bag = bag*inner_size;
               var total_box = box*outer_size;
               var totalsum = total_bag+total_box;
               var loose = Math.floor(qty-totalsum); */


         // unit logic by shivani //04-03-2023
         var bag = 0;
         var box = 0;
         var loose = 0;
         if (item_order_unit < 0 && item_order_unit != 100) {
            bag = order_qty;
            qty = order_qty * inner_size;
            box = 0;
         } else if (item_order_unit > 0 && item_order_unit != 100) {
            box = order_qty;
            qty = order_qty * outer_size;
            bag = 0;
         } else if (item_order_unit == 100) {
            qty = order_qty;
            bag = 0;
            box = 0;
         } else {
            qty = 0;
            bag = 0;
            box = 0;
         }
         // unit logic by shivani //04-03-2023

         $(row).find("td").find("input.box_qty").val(box);
         $(row).find("td").find("input.bag").val(bag);
         $(row).find("td").find("input.loose").val(loose);


         if (parseFloat(price) > parseFloat(original_price)) {
            $(row).find("td").find("input.price").val(old_price);
            price = $(row).find("td").find("input.price").val();
         }

         if (parseFloat(discount_amount_new) > (parseFloat(original_price) * MAX_ITEM_DISCOUNT_PCT / 100)) {
            toastr.error("You cant add Discount More Than " + MAX_ITEM_DISCOUNT_PCT + "%");
            $(row).find("td").find("input.discount_amount").val(0);
            discount_amount_new = 0;
         }

         if (parseFloat(discount) > MAX_ITEM_DISCOUNT_PCT) {
            toastr.error("You cant add Discount More Than " + MAX_ITEM_DISCOUNT_PCT + "%");
            $(row).find("td").find("input.discount").val(0);
            discount = 0;
         }

         var customer_type = $("#customer_type").val();
         if (customer_type == 8) {
            var item_gst = 0.1;
         } else {
            item_gst = gst_tax;
         }

         if (discount_amount_new != "" && discount_amount_new != 0) {
            var discount_amount = (parseFloat(original_price) - parseFloat(discount_amount_new));
            price = discount_amount;
            price1 = discount_amount;
         } else {
            var discount_amount = (original_price * discount) / 100;
            price = (original_price - discount_amount);
            price1 = (original_price - discount_amount);
         }

         /*price = (original_price - discount_amount);
         price1 = (original_price - discount_amount);*/
         price1 = price1.toFixed(2);
         price = price + ((price * item_gst) / 100);
         price = price.toFixed(2);
         // alert(""+price1);
         // alert("qty"+qty);
         $(row).find("td").find("input.price").val(price1);
         var total = price * qty;
         var total1 = price1 * qty;
         total = total.toFixed(2);
         total1 = total1.toFixed(2);
         var total_balance = stock - qty;
         /*
             alert("total1"+total1);
             alert("cd_discount"+cd_discount);
             alert("ad_discount"+ad_discount);*/

         var taxable_amount = parseFloat(total1) - parseFloat(cd_discount) - parseFloat(ad_discount) + parseFloat(other_charge) + parseFloat(fright_charge);
         // alert(taxable_amount);
         var item_gst_amount1 = ((taxable_amount * item_gst) / 100);
         item_gst_amount1 = item_gst_amount1.toFixed(2);
         var taxable_amount = taxable_amount.toFixed(2);
         var sub_total = (parseFloat(total1) + parseFloat(item_gst_amount1)).toFixed(2);

         $(row).find("td").find("input.qty").val(qty);
         $(row).find("td").find("input.total").val(total1);
         $(row).find("td").find("input.gst_amount").val(item_gst_amount1);
         $(row).find("td").find("input.final_balance").val(total_balance);
         $(row).find("td").find("input.sub_total").val(sub_total);
         $(row).find("td").find("input.taxable_amount").val(taxable_amount);
         $(row).find("td").find("input.new_taxable").val(taxable_amount);
         cdadCalculate();
      }


      function recalculateFinalValues() {
         var customer_type = $("#customer_type").val();
         var sum = 0;
         var final_sum = 0;
         var qtytotal = 0;
         var sum_qty = 0;
         var grand_total = 0;
         var additional_discount_amount = 0;
         var cash_discount_amount = 0;

         var gst = 18;

         var tcs_per = '<?= TCS_CHARGE_IN_PER; ?>';
         var total = 0;
         var item_gst_amount2 = 0;
         var total1 = 0;
         var taxable_amount = 0;
         var gst_total_sum = 0;
         var item_gst_amount1 = 0;
         var sub_total = 0;
         var sum1 = 0;
         var sum2 = 0;

         $('.total').each(function() {
            total = parseFloat($(this).val());
            total = (total != "") ? parseFloat(total) : 0;
            sum += total;
         });

         $('.qty').each(function() {
            qty = parseFloat($(this).val());
            if (isNaN(qty)) {
               qty = 0;
            } else {
               qty = parseFloat($(this).val());
            }
            sum_qty += qty;
            //only digit enter if i enter '-' or '.' value it replace and get alert//
            if (event.keyCode == 46 || event.keyCode == 8) {
               // let it happen, don't do anything
            }
         });

         $('.taxable_amount').each(function() {
            total1 = parseFloat($(this).val());
            total1 = (total1 != "") ? parseFloat(total1) : 0;
            taxable_amount += total1;
            // this.value = this.value.replace(/\D/g, '');
         });

         $('.gst_amount').each(function() {
            item_gst_amount1 = parseFloat($(this).val());
            item_gst_amount1 = (item_gst_amount1 != "") ? parseFloat(item_gst_amount1) : 0;
            gst_total_sum += item_gst_amount1;
            // this.value = this.value.replace(/\D/g, '');
         });

         $('.cd_discount').each(function() {
            cd_discount = parseFloat($(this).val());
            cd_discount = (cd_discount != "") ? parseFloat(cd_discount) : 0;
            cash_discount_amount += cd_discount;
         });

         $('.ad_discount').each(function() {
            ad_discount = parseFloat($(this).val());
            ad_discount = (ad_discount != "") ? parseFloat(ad_discount) : 0;
            additional_discount_amount += ad_discount;
         });


         $('.sub_total').each(function() {
            total = parseFloat($(this).val());
            sub_total = (total != "") ? parseFloat(total) : 0;
            sum1 += sub_total;
            sum2 += sub_total;
         });

         sum = sum.toFixed(2);
         sum_qty = sum_qty.toFixed(2);
         gst_total_sum = gst_total_sum.toFixed(2);
         sum1 = sum1.toFixed(2);
         sum2 = sum2.toFixed(2);
         taxable_amount = taxable_amount.toFixed(2);
         cash_discount_amount = cash_discount_amount.toFixed(2);
         additional_discount_amount = additional_discount_amount.toFixed(2);

         //$("#gst_amount").val('' + item_gst_amount2);
         $("#finalTotal").val('' + sum);
         $("#sum_qty").val('' + sum_qty);
         //$("#total1").val('' + sum1);
         $("#total1").val('' + taxable_amount);
         $("#taxable_total_sum").val('' + taxable_amount);
         $("#gst_total_sum").val('' + gst_total_sum);
         $("#sub_total_sum").val('' + sum2);
         $("#cd_total_sum").val('' + cash_discount_amount);
         $("#ad_total_sum").val('' + additional_discount_amount);
         var averagevalue = (sum / sum_qty);
         averagevalue = averagevalue.toFixed(2);
         $(".average_value").val(averagevalue)

         var transport_charge = $("#transport_charge").val();
         var transport_charge_per = $("#transport_charge_per").val();
         var packing_charge = $("#packing_charge").val();
         var packing_charge_per = $("#packing_charge_per").val();
         var cd_gst = $("#cd_gst").val();
         var ad_gst = $("#ad_gst").val();

         if (gst_apply_flag == 0) {
            gst = 0
            gst_total_sum = 0
            packing_charge_per = 0
            transport_charge_per = 0
            cd_gst = 0
            ad_gst = 0
         }

         if (tcs_apply_flag == 0) {
            tcs_per = 0
         }

         var sum1 = $("#total1").val();
         if (sum1 != "" && sum1 != "0.00") {
            var gst_amount = parseFloat(gst_total_sum);
            gst_amount = gst_amount.toFixed(2);

            $("#gst_amount").val(parseFloat(gst_amount));

            var final_total = (parseFloat(sum1) + parseFloat(gst_amount));
            var ft = Math.round(final_total);
            ft = ft.toFixed(2);
            var integr = Math.floor(final_total);
            var round_off = final_total - integr;
            round_off = round_off.toFixed(2);
            // round_off=format(round_off,3);
            $("#round_off").val('' + round_off);
            $("#finalgrandTotal").val('' + ft);
         }

         var sum1 = $("#finalgrandTotal").val();
         if (tcs_per != "0") {
            var tcs_amount1 = (sum1 * tcs_per) / 100;
            tcs_amount1 = tcs_amount1.toFixed(2);
            $("#tcs_amount").val(parseFloat(tcs_amount1));
            $(".tcs_put").val(parseFloat(tcs_amount1));
            final_total = parseFloat(final_total) + parseFloat(tcs_amount1);
         } else {
            $("#tcs_amount").val("0");
            $(".tcs_put").val(0);
            $("#finalgrandTotal").val('' + ft);
         }
         var ft = Math.round(final_total);
         ft = ft.toFixed(2);
         var integr = Math.floor(final_total);
         var round_off = final_total - integr;
         round_off = round_off.toFixed(2);
         // round_off=format(round_off,3);
         $("#round_off").val('' + round_off);
         $("#finalgrandTotal").val('' + ft);
      }

      function hasValue(elem) {
         return $(elem).filter(function() {
            return $(this).val();
         }).length > 0;
      }

      $("#add").click(function() {
         var product_id = $("#product_id").val();
         var qty = $("#qty").val();
         var bag_box_id = $("#bag_box_id").val();
         var order_item_brand = $("#order_item_brand").val();
         var order_item_brand_name = $("#order_item_brand").find("option:selected").data("order_item_brand_name");
         count = 0;
         if (product_id == "") {
            toastr.error('Please Select product!!');
         } else if (qty == "" || qty == 0) {
            toastr.error('Please Enter At least one Quantity!!');
         } else if (order_item_brand == "" || order_item_brand == 0) {
            toastr.error('Please Select Brand!!');
         } else if (bag_box_id == 0) {
            toastr.error('Please Select Order Unit!!');
         } else {
            var count = $('.count').length;
            count = ++count;
            var product_id = $("#product_id").val();
            var price = $("#product_id").find('option:selected').data('pricelist');
            var p_name = $("#product_id").find('option:selected').data('name');
            var weight = $("#product_id").find('option:selected').data('weight');
            var weight_id = $("#product_id").find('option:selected').data('weight-id');
            var inner_size = $("#product_id").find('option:selected').data('inner_size');
            var outer_size = $("#product_id").find('option:selected').data('outer_size');
            var pro_id = $("#product_id").find('option:selected').data('pro_id');
            var original_price = $("#product_id").find('option:selected').data('original-price');
            var discountPer = $("#product_id").find('option:selected').data('discount');
            var stock = $("#product_id").find('option:selected').data('stock');
            var last_quotation_price = $("#product_id").find('option:selected').data('last_quot_price');
            var brand_id = $("#product_id").find('option:selected').data('brand-id');
            var catno = $("#product_id").find('option:selected').data('catno');
            var hsncode = $("#product_id").find('option:selected').data('hsncode');
            var cid = $("#product_id").find('option:selected').data('cid');
            var tcid = $("#product_id").find('option:selected').data('tcid');
            var top_cat_name = $("#product_id").find('option:selected').data('topcat_name');
            var category_name = $("#product_id").find('option:selected').data('cat_name');
            var pro_master_price = $("#product_id").find('option:selected').data('pro_master_price');
            var is_including = $("#product_id").find('option:selected').data('is_including');
            var unit_name = $("#product_id").find('option:selected').data('unit_name');
            var is_premium = $("#product_id").find('option:selected').data('is_premium');
            //var gst = $("#product_id").find('option:selected').data('gst');
            var qty = $("#qty").val();

            var unitname = $("#bag_box_id").find('option:selected').data('unitname');
            var item_order_unit = bag_box_id;
            var order_qty = qty;

            original_price = parseFloat(original_price);
            // alert(original_price)


            if (pro_master_price != original_price) {
               // alert(pro_master_price)
               // $("#pro_master_price").html("ori.Price:"+pro_master_price);
               var pro_price_main = "Including Gst :" + pro_master_price;
            } else {
               var pro_price_main = "";
            }

            var customer_type = $("#customer_type").val();
            if (customer_type == 8) {
               var gst = 0.1;
            } else {
               var gst = $("#product_id").find('option:selected').data('gst');
            }

            /*if(bag_box_id==2)
                   {
                       var new_qty_bag = inner_size*qty;
                       $("#final_qty").val(new_qty_bag);
                       var qty = new_qty_bag;
                       var bag = (qty / inner_size);
                       bag = Math.floor(bag);
                       var box = 0;
                       var loose = 0;
                   }
                    else if(bag_box_id==3)
                    {
                       var new_qty_box = outer_size*qty;
                       $("#final_qty").val(new_qty_bag);
                       var qty = new_qty_box;
                       var box = Math.floor(qty / outer_size)
                       var bag = 0;
                       var loose = 0;
                    }
                    else if(bag_box_id==1)
                    {
                        $("#final_qty").val(qty);
                       var qty = qty;
                       var box = Math.floor(qty / outer_size);
                       if (box != 0) 
                       {
                           var bagqty = qty - (outer_size * box);
                           if (bagqty < 0)
                       {
                       bagqty = bagqty * -1;
                       }
                           bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
                           var bag = (bagqty / inner_size); 
                           bag = Math.floor(bag);
                       } 
                       else 
                       {
                           var bag = (qty / inner_size);
                           //bag = format(bag, 3);
                           bag = Math.floor(bag);
                       } 
                       var total_bag = bag*inner_size;
                       var total_box = box*outer_size;
                       var totalsum = total_bag+total_box;
                       var loose =  Math.floor(qty-totalsum); 
                    }
               
                   qty = (qty != "") ? parseFloat(qty) : 0;
                   outer_size = (outer_size != "") ? parseFloat(outer_size) : 0;
                    
                   var total_bag = bag*inner_size;
                   var total_box = box*outer_size;
                   var totalsum = total_bag+total_box;
                   var loose =  Math.floor(qty-totalsum); */

            // unit logic by shivani //04-03-2023
            var bag = 0;
            var box = 0;
            var loose = 0;
            // alert(bag_box_id);
            if (bag_box_id < 0 && bag_box_id != 100) {
               bag = order_qty;
               qty = order_qty * inner_size;
               box = 0;
            } else if (bag_box_id > 0 && bag_box_id != 100) {
               box = order_qty;
               qty = order_qty * outer_size;
               bag = 0;
            } else if (bag_box_id == 100) {
               qty = order_qty;
               bag = 0;
               box = 0;
            } else {
               qty = 0;
               bag = 0;
               box = 0;
            }
            // unit logic by shivani //04-03-2023

            var customer_type = $("#customer_type").val();
            if (customer_type == 8) {
               var item_gst = 0.1;
            } else {
               item_gst = gst;
            }

            var cd_discount = 0;
            var ad_discount = 0;
            var discount_amount = (original_price * discountPer) / 100;
            price = original_price - discount_amount;
            price1 = (original_price - discount_amount);
            price = price.toFixed(2);
            var total = qty * price;
            price1 = price1.toFixed(2);
            total = total.toFixed(2);
            // alert(price1)
            var total1 = price1 * qty;
            total1 = total1.toFixed(2);
            var balance = stock - qty;
            var taxable_amount = (total1 - cd_discount - ad_discount);
            var item_gst_amount1 = ((taxable_amount * item_gst) / 100);
            var new_total = (parseFloat(total1) + parseFloat(item_gst_amount1)).toFixed(2);
            item_gst_amount1 = item_gst_amount1.toFixed(2);
            var taxable_amount = taxable_amount.toFixed(2);
            original_price = original_price.toFixed(2);
            discountPer = format(discountPer, 3);
            // var duplicate = hasValue($("input.pro_id[value='" + pro_id + weight_id + "']"));
            var duplicate = 0;
            if (duplicate == 0) {
               var new_row = "<tr><td style='padding: 1px!important;' class='text-center'><a style='padding: 6px 9px 6px 9px!important;' class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td><td style='padding: 1px!important;min-width: 187px;' class='text-center'><input type='hidden' class='pro_id' name='pro_id' id='pro_id' value='" + pro_id + weight_id + "'><input type='hidden' name='product_id[]' value='" + pro_id + "' class='product_id' id='product_id'/><input type='hidden' name='pro_name[]' value='" + p_name + "' id='pro_name'><input type='hidden' name='weight_id[]' value='" + weight_id + "' id='weight_id'><input type='hidden' name='brand_id[]' value='" + brand_id + "' id='brand_id'><input class='cid' type='hidden' name='cid[]' value='" + cid + "' id='cid'><input class='tcid' type='hidden' name='tcid[]' value='" + tcid + "' id='tcid'>" + p_name + " - " + catno + " - " + top_cat_name + " - " + category_name + "</td>" +

                  "<td class='text-center'><a><input type='hidden' class='order_item_brand_id' name='order_item_brand_id[]' value='" + order_item_brand + "' /></a>" + order_item_brand_name + "</td>" +

                  // "<td style='padding: 1px!important;'; class='text-center'>"+unit_name+"</td>" +
                  "<td style='padding: 1px!important;'; class='text-center'>" + unitname + "<input type='hidden' name='item_order_unit[]' class='item_order_unit' value='" + item_order_unit + "'></td>" +
                  "<td style='padding: 1px!important;'; class='text-center'><input type='text' name='order_qty[]' class='form-control positive  order_qty' style='text-align:right;width:80px;' value='" + order_qty + "' onChange='recalculateRow(this)'  id='order_qty'/></td>" +

                  "<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='inner_size' class='inner_size' value='" + inner_size + "'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:70px;' value='" + bag + "''></td>" +

                  "<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='outer_size' class='outer_size' value='" + outer_size + "'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value='" + box + "'></td>" +

                  "<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='loose_qty' class='loose_qty' value='" + outer_size + "'><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value='" + loose + "'></td>" +

                  "<td hidden style='padding: 1px!important;'; class='text-center'>" + stock + "</td>" +

                  "<td style='padding: 1px!important;'; ><textarea rows='2' cols='10' id='pro_description' name='pro_description[]' style='margin: 0!important;'></textarea></td>" +

                  "<td style='padding: 1px!important;'; class='text-center'>" + hsncode + "</td>" +

                  "<td style='padding: 1px!important;'; class='text-center'><input type='text' name='qty[]' class='form-control positive  qty' style='text-align:right;width: 80px;' readonly value='" + qty + "' onChange='recalculateRow(this)'  id='qty'/><input class='new_qty' type='hidden' value='" + qty + "' id='qty'></td>" +

                  "<td style='text-align:right;padding: 1px!important;'><input type='text' name='original_price[]' class='form-control  original_price' style='text-align:right;width: 80px;' value='" + original_price + "' onChange='recalculateRow(this)'  id='original_price'/><input type='hidden' value=" + original_price + " class='original_price_hidden'/><input type='hidden' name=is_including[] id=is_including value=" + is_including + "><span id='pro_master_price'>" + pro_price_main + "</span><span><input type='hidden' value=" + is_premium + " class='is_premium'/></span></td>" +

                  "<td style='text-align:right;padding: 1px!important;'><input type='text' name='discount_amount[]' style='text-align:right;width: 80px;' class='form-control discount_amount' onChange='recalculateRow(this,1)' value=''></td>" +

                  "<td style='text-align:right;padding: 1px!important;'><input type='text' name='discount[]' style='text-align:right;width: 80px;' class='form-control discount' onChange='recalculateRow(this,2)' value='" + discountPer + "'></td>" +

                  "<td style='padding: 1px!important;';  class='price_val' ><input readonly type='text' style='text-align:right;width: 80px;' name='price[]' class='price form-control' onChange='recalculateRow(this)' value='" + price + "'><input type='hidden' style='text-align:right;width: 80px;' class='old_price form-control' value='" + price + "'></td>" +

                  "<td style='padding: 1px!important;';><input type='text' class='total form-control' disabled onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='subtotal[]' value='" + total + "' ></td>" +

                  "<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='cd_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='cd_discount[]' value='' ></td>" +

                  "<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='ad_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='ad_discount[]' value='' ></td>" +

                  "<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='other_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='other_charge[]' value='' ></td>" +

                  "<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='fright_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='fright_charge[]' value='' ></td>" +

                  "<td style='padding: 1px!important;';><input id='taxable_amount' readonly type='text' class='taxable_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='taxable_amount[]' value='" + taxable_amount + "' ><input class='new_taxable' type='hidden' value='" + taxable_amount + "' id='taxable_amount'></td>" +

                  "<td style='padding: 1px!important;';><input readonly  type='text' class='gst_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='gst_amount_item[]' value='" + item_gst_amount1 + "' ></td>" +

                  "<td style='padding: 1px!important;' class='text-center'>" + gst + "%<input type='hidden' style='text-align:right;width: 80px;' id='gst_tax'  class='gst_tax form-control' value='" + gst + "'></td>" +

                  "<td style='padding: 1px!important;';><input readonly type='text' class='sub_total form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='sub_total[]' value='" + new_total + "' ></td>" +

                  "</tr>";

               $("#datatable_1").find('tbody').append(new_row);
               //$("#datatable_1").find('tbody').prepend(new_row);

               // $('#category_id').prop('disabled',true); 
               recalculateRow();
            } else {
               $mainInputBox = $("input.pro_id[value='" + pro_id + weight_id + "']").parent().parent().find("td>input.qty");
               var old_va = $mainInputBox.val();
               var new_va = parseFloat(old_va) + parseFloat(qty);
               $mainInputBox.val(new_va);
               $mainInputBox.change();
               toastr.success("Product Qty Update Successfuly.");
            }
         }
         $("#qty").val("");
         $("#bag_box_id").select2("val", "");
         $("#product_id").select2("val", "");
         $(".inner_size").html("");
         $(".outer_size").html("");
         $(".qty").html("");
         $("#order_item_brand").val("");
         $("#order_item_brand").select2("val", "");
      })

      function check_form() {
         $(".form-body").children().removeClass("has-error");
         var isValid = true;

         <?php if ($_REQUEST['mode'] == "add") {
         ?>
            if ($("#customer_type").val() == "" || $("#customer_type").val().split(" ").join("") == "") {
               vd = aj.error('customer_type', "Please Select Customer Type", "add_error");
               isValid = false;
            }
            if ($("#dealer_id").val() == "" || $("#dealer_id").val().split(" ").join("") == "") {
               vd = aj.error('dealer_id', "Please Select Customer.", "add_error");
               isValid = false;
            }
            if ($("#type_of_company").val() == "" || $("#type_of_company").val().split(" ").join("") == "") {
               vd = aj.error('type_of_company', "Please Select Companuy.", "add_error");
               isValid = false;
            }
            if ($("#quotation_date").val() == "" || $("#quotation_date").val().split(" ").join("") == "") {
               vd = aj.error('quotation_date', "Please Enter Order Date.", "add_error");
               isValid = false;
            }
            if ($("#gstin").val() == "" || $("#gstin").val().split(" ").join("") == "") {
               vd = aj.error('gstin', "Please Enter GST No.", "add_error");
               isValid = false;
            }



         <?php } ?>
         if ($("#sales_executive").val() == "" || $("#sales_executive").val().split(" ").join("") == "") {
            vd = aj.error('sales_executive', "Please Select Sales Executive", "add_error");
            isValid = false;
         }
         // if ($("#shipping_address").val() == "" || $("#shipping_address").val().split(" ").join("") == "") {
         // vd = aj.error('shipping_address', "Please Select Shipping Address.", "add_error");
         // isValid = false;
         // }
         // if ($("#billing_address").val() == "" || $("#billing_address").val().split(" ").join("") == "") {
         // vd = aj.error('billing_address', "Please Select Billing Address.", "add_error");
         // isValid = false;
         // }

         const table = document.querySelector("#datatable_1 tbody");
         $sdfcnt = 0;
         table.querySelectorAll("tr").forEach(row => {
            // console.log($(row).find("td").find("a").find("input.order_item_brand_id").val());
            if ($(row).find("td").find("a").find("input.order_item_brand_id").val() == "" || $(row).find("td").find("a").find("input.order_item_brand_id").val() == undefined || $(row).find("td").find("a").find("input.order_item_brand_id").val() == null || $(row).find("td").find("a").find("input.order_item_brand_id").val() == 0) {
               $sdfcnt++
            }
         });
         // alert($sdfcnt);
         if ($sdfcnt > 0) {
            toastr.error("Please Add Brand!!");
            isValid = false;
         }
         var count_row = $("#datatable_1").find('tbody tr').length;
         // alert(count_row);
         if (count_row == 0) {
            toastr.error("Please Add At Least one Product!!");
            isValid = false;
         }
         if (isValid) {
            var r = confirm("Are You sure want to Save this Quotation??");
            if (r) {

               return true;
            } else {
               return false;
            }
         } else {
            return false;
         }
      }
      $(".form-control").bind("keyup change", function() {
         if ($(this).parent().hasClass("has-error")) {
            $(this).parent().removeClass("has-error");
            $(this).parent().find('p.help-block').html("");
         }
      });
      $(document).ready(function() {
         recalculateRow();
         recalculateFinalValues();
         $("#datatable_1").on('click', '.delete', function() {
            //var rows = document.getElementById("#datatable_1").getElementsByTagName("tr").length;
            var rows = $("#datatable_1").find("tbody").find("tr").length;

            var r = confirm("Are you sure you want to delete?");
            if (r) {
               $(this).closest('tr').remove();
               cdadCalculate();

               if (rows == 1) {
                  $('#category_id').prop('disabled', false);
               }
            }
         });
      });

      $(document).ready(function() {
         var mode = "<?= $_REQUEST['mode']; ?>";
         $("#dealer_id").trigger("change");
         var cid = "<?= $dealer_id; ?>";
         // getProductList(cid);
         if (mode == "edit") {

            getCustomerCount(cid);

            var top_cat_id = "<?= $top_cat_id; ?>";

            $("#category_id").select2("destroy");
            // $('#category_id option[value='+top_cat_id+']').attr('selected', 'selected');
            $("#category_id").select2();
            // $('#category_id').prop('disabled',true);

            getProductList(top_cat_id);
            if ($("#cd").prop("checked") == true) {
               recalculateFinalValues();
            }
            if ($("#ad").prop("checked") == true) {
               recalculateFinalValues();
            }

         }
      })

      $(document).ready(function() {
         if (mode == "edit") {
            if ($("#gst_apply").prop("checked") == true) {
               gst_apply_flag = 1;
               recalculateFinalValues();
            }

            if ($("#tcs_apply").prop("checked") == true) {
               tcs_apply_flag = 1;
               recalculateFinalValues();
            }
         }
         if (mode == "add") {
            if ($("#gst_apply").prop("checked") == true) {
               gst_apply_flag = 1;
               $("#gst_apply").prop("checked");
               recalculateFinalValues();
            }
            if ($("#tcs_apply").prop("checked") == true) {
               tcs_apply_flag = 1;
               recalculateFinalValues();
            }
         }
      })

      $("#gst_apply").on("click", function() {
         if ($('#gst_apply').is(":checked")) {
            gst_apply_flag = 1;
            $("#gst_apply_flag").val("1");
         } else {
            gst_apply_flag = 0;
            $("#gst_apply_flag").val("0");
         }
         recalculateFinalValues();
      })

      $("#tcs_apply").on("click", function() {
         if ($('#tcs_apply').is(":checked")) {
            tcs_apply_flag = 1;
            $("#tcs_apply_flag").val("1");
         } else {
            tcs_apply_flag = 0;
            $("#tcs_apply_flag").val("0");
         }
         recalculateFinalValues();
      })

      $(".positive").keyup(function(event) {
         if (event.keyCode == 46 || event.keyCode == 8) {
            // let it happen, don't do anything
         } else if (/\D/g.test(this.value)) {
            toastr.error("Only Digits Allowed");
            this.value = this.value.replace(/\D/g, '');
         }
      });
   </script>
   <script type="text/javascript">
      var mode = '<?= $_REQUEST['mode']; ?>';
      if (mode == "edit") {
         var id = $("#transport_through").val();
         var transport_name_selected_id = $("#transport_name_selected_id").val();
         getTransportname(id, transport_name_selected_id);
      }

      var mode = '<?= $_REQUEST['mode']; ?>';
      var quotation_id = '<?= $_REQUEST['quotation_id']; ?>';
      if (mode == "add" && quotation_id != "") {
         var id = $("#transport_through").val();
         var transport_name_selected_id = $("#transport_name_selected_id").val();
         getTransportname(id, transport_name_selected_id);
      }

      function getTransportname(id, transport_name_selected_id = "") {
         $.ajax({
            type: "post",
            url: "ajax_get_transport_detail.php",
            data: "id=" + id + "&selected_id=" + transport_name_selected_id,
            beforeSend: function() {
               $(".transCover").fadeIn(800);
               $('.preloader').fadeIn('slow');
            },
            success: function(result) {
               setTimeout(function() {
                  $("#transport_name").select2("destroy");
                  $('#transport_name').html(result);
                  $("#transport_name").select2();
                  // $("#loading-modal").modal('hide');
                  $('.preloader').fadeOut('slow');
               });
            }
         })
      }
   </script>
   <!-- subcategory discount Modal -->
   <div id="addCartonMoadal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title carton-modal-title"></h4>
            </div>
            <div class="modal-body">
               <table class="table table-borderd" border="1" style="height: 400px;overflow: scroll;display: inline-block;">
                  <thead style="width: 100%;display: table;">
                     <tr>
                        <th>Subcategory</th>
                        <th>Discount Percentage</th>
                     </tr>
                  </thead>
                  <tbody class="carton-modal-body" style="width: 100%;display: table;">
                     <?php
                     $SubcategoryR = $db->rp_getData("category_master", "*", "isDelete=0", "id DESC");
                     while ($SubcategoryD = mysqli_fetch_assoc($SubcategoryR)) {
                     ?>
                        <tr>
                           <td><?= $SubcategoryD['name']; ?></td>
                           <td><input class="form-control cart_discount" type="text" id="cat_discount_<?= $SubcategoryD['id']; ?>" name="cat_discount[]" data-cat_id="<?= $SubcategoryD['id'] ?>" value=""></td>
                        </tr>
                     <?php
                     }
                     ?>
                  </tbody>
               </table>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success discount-modal-save">Save</button>
            </div>
         </div>
      </div>
   </div>
   <!-- subcategory discount Modal -->
   <!-- category discount Modal -->
   <div id="addTopcartMoadal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title carton-modal-title"></h4>
            </div>
            <div class="modal-body">
               <table class="table table-borderd" border="1" style="height: 400px;overflow: scroll;display: inline-block;">
                  <thead style="width: 100%;display: table;">
                     <tr>
                        <th>Category</th>
                        <th>Discount Percentage</th>
                     </tr>
                  </thead>
                  <tbody class="carton-modal-body" style="width: 100%;display: table;">
                     <?php
                     $categoryR = $db->rp_getData("top_category_master", "*", "isDelete=0", "id DESC");
                     while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                     ?>
                        <tr>
                           <td><?= $categoryD['name']; ?></td>
                           <td><input class="form-control topcat_discount" type="text" id="top_cat_discount_<?= $categoryD['id']; ?>" name="top_cat_discount[]" data-top_cat_id="<?= $categoryD['id'] ?>" value=""></td>
                        </tr>
                     <?php
                     }
                     ?>
                  </tbody>
               </table>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success maincat-modal-save">Save</button>
            </div>
         </div>
      </div>
   </div>
   <!--category discount Modal -->
   <!-- subcategory discount code -->
   <script type="text/javascript">
      $("#add_discount").on('click', function() {
         $("#addCartonMoadal").modal("show")
      });
      $(".discount-modal-save").on('click', function() {
         var cart_discount = [];
         $(".cart_discount").each(function(index) {
            var thisdiscount = $(this).val();
            var cat_id = $(this).data("cat_id");
            var thisdiscount = parseFloat(thisdiscount);
            if (isNaN(thisdiscount)) {
               var thisdiscount = 0;
            }
            cart_discount.push({
               "cartdiscount": thisdiscount,
               "cat_id": cat_id
            });
         });
         $("#addCartonMoadal").modal("hide");
         var result = [];
         $(".cid").each(function(index) {
            var cid = $(this).val();
            cid = parseFloat(cid);
            var cartdiscountarray = cart_discount.find(function(e) {
               return e.cat_id === cid;
            })
            var row1 = $(this).parent('td').parent('tr');
            $(row1).find("td").find("input.discount").val(cartdiscountarray.cartdiscount);
            recalculateRow($(this));
         });
         //product item master ma cart_discount no cid match thy tya discount input ma cart discount ni discount nakvani  
      });
   </script>
   <!-- subcategory discount code -->

   <!-- category discount code -->
   <script type="text/javascript">
      $("#add_topcat_discount").on('click', function() {
         $("#addTopcartMoadal").modal("show")
      });

      $(".maincat-modal-save").on('click', function() {
         var top_cart_discount = [];
         $(".topcat_discount").each(function(index) {
            var thismaindiscount = $(this).val();
            var tc_id = $(this).data("top_cat_id");
            var thismaindiscount = parseFloat(thismaindiscount);
            if (isNaN(thismaindiscount)) {
               var thismaindiscount = 0;
            }
            top_cart_discount.push({
               "maindiscount": thismaindiscount,
               "tc_id": tc_id
            });
         });
         $("#addTopcartMoadal").modal("hide");

         var result1 = [];
         $(".tcid").each(function(index) {
            var topcid = $(this).val();
            topcid = parseFloat(topcid);
            var topcatdiscountarray = top_cart_discount.find(function(e) {
               return e.tc_id === topcid;
            })
            var row2 = $(this).parent('td').parent('tr');
            $(row2).find("td").find("input.discount").val(topcatdiscountarray.maindiscount);
            recalculateRow($(this));
         });
      });
   </script>
   <!-- category discount code -->
   <script type="text/javascript">
      $("#cash_discount").change(function() {
         cdadCalculate();
      })
      $("#additional_discount").change(function() {
         cdadCalculate();
      })

      function cdadCalculate() {
         var additional_discount = $("#additional_discount").val();
         var cash_discount = $("#cash_discount").val();
         var addtional_discount_amount = $("#addtional_discount_amount").val();
         var cash_discount_amount = $("#cash_discount_amount").val();

         var packing_charge = $("#packing_charge").val();
         var transport_charge = $("#transport_charge").val();

         var sum1 = 0;
         $('.total').each(function() {
            total1 = parseFloat($(this).val());
            total1 = (total1 != "") ? parseFloat(total1) : 0;
            sum1 += total1;
         });
         packing_charge = (packing_charge) ? packing_charge : 0;
         transport_charge = (transport_charge) ? transport_charge : 0;
         // alert("cash_discount"+cash_discount);
         // cash_discount = (cash_discount)?cash_discount:0;
         // additional_discount = (additional_discount)?additional_discount:0;

         if (parseFloat(cash_discount) > 100) {
            toastr.error("You cant add Cash Discount More Than 100");
            cash_discount = 0;
            $("#cash_discount").val(0);
         }
         if (parseFloat(additional_discount) > 100) {
            toastr.error("You cant add Additional Discount More Than 100");
            additional_discount = 0;
            $("#additional_discount").val(0);
         }

         var cd_value;
         var ad_value;

         if (cash_discount == "") {
            cd_value = cash_discount_amount;
         } else if (cash_discount >= 0) {
            cd_value = (sum1 * 0) / 100;
            cd_value = (sum1 * cash_discount) / 100;
            cd_value = Math.floor(cd_value * 100) / 100;
         }
         $("#cash_discount_amount").val(cd_value);

         if (additional_discount == "") {
            ad_value = addtional_discount_amount;
         } else if (additional_discount >= 0) {
            var adsum = sum1 - cd_value;
            ad_value = (adsum * 0) / 100;
            ad_value = (adsum * additional_discount) / 100;
            ad_value = Math.floor(ad_value * 100) / 100;
         }
         $("#addtional_discount_amount").val(ad_value);

         cd_value = (cd_value) ? cd_value : 0;
         ad_value = (ad_value) ? ad_value : 0;
         var totltaxabaleSum = parseFloat(sum1) - parseFloat(cd_value) - parseFloat(ad_value);
         totltaxabaleSum = totltaxabaleSum.toFixed(2);

         var cash_discount_amount = $("#cash_discount_amount").val();
         var additional_discount_amount = $("#addtional_discount_amount").val();
         // $("#addtional_discount_amount").val(parseFloat(ad).toFixed(2));   
         $('.new_taxable').each(function() {
            var row = $(this).parent('td').parent('tr');
            var total = $(row).find("input.total").val();
            // var other_charge_item = $(row).find("input.other_charge_item").val(); 
            // var fright_charge_item = $(row).find("input.fright_charge").val();  

            // var cditem = (total*cash_discount)/100;
            var cditem = ((cash_discount_amount * total) / sum1);
            var cditem_amt = total - cditem;

            var aditem = ((additional_discount_amount * cditem_amt) / (sum1 - cash_discount_amount));
            // var aditem = (cditem_amt*additional_discount)/100;
            var aditem_amt = cditem_amt - aditem;

            cditem = (cditem) ? cditem : 0;
            aditem = (aditem) ? aditem : 0;
            cditem_amt = (cditem_amt) ? cditem_amt : 0;
            aditem_amt = (aditem_amt) ? aditem_amt : 0;
            cditem = cditem.toFixed(2);
            aditem = aditem.toFixed(2);

            var itemWithCDAD_for_packing_value = parseFloat(total) - parseFloat(cditem) - parseFloat(aditem);
            // alert("itemWithCDAD_for_packing_value="+itemWithCDAD_for_packing_value);
            // alert("packing_charge="+packing_charge);
            // alert("totltaxabaleSum="+totltaxabaleSum);
            itemWithCDAD_for_packing_value = itemWithCDAD_for_packing_value.toFixed(2);
            var packing_value = ((parseFloat(packing_charge) * parseFloat(itemWithCDAD_for_packing_value)) / parseFloat(totltaxabaleSum));
            // alert("packing_value="+packing_value);
            packing_value = (packing_value) ? packing_value : 0;
            packing_value = packing_value.toFixed(2);

            var itemWithCDAD_for_transport = parseFloat(itemWithCDAD_for_packing_value) + parseFloat(packing_value);
            itemWithCDAD_for_transport = itemWithCDAD_for_transport.toFixed(2);
            /*  alert("transport_charge-"+transport_charge);
            alert("itemWithCDAD_for_transport-"+itemWithCDAD_for_transport);
            alert("totltaxabaleSum-"+(totltaxabaleSum+packing_value));*/
            var transport_value = ((parseFloat(transport_charge) * parseFloat(itemWithCDAD_for_transport)) / (parseFloat(totltaxabaleSum) + parseFloat(packing_charge)));

            transport_value = (transport_value > 0) ? transport_value : 0;
            transport_value = transport_value.toFixed(2);
            // alert(packing_value);
            // alert(transport_value);

            itemFinalTotal = parseFloat(total) - parseFloat(cditem) - parseFloat(aditem) + parseFloat(packing_value) + parseFloat(transport_value);
            itemFinalTotal = itemFinalTotal.toFixed(2);

            // for gst
            gst_tax = $(row).find("input.gst_tax").val();
            gst_amount = (parseFloat(itemFinalTotal) * gst_tax) / 100;
            gst_amount = gst_amount.toFixed(2);
            // for gst

            $(row).find("input.cd_discount").val(cditem);
            $(row).find("input.ad_discount").val(aditem);
            $(row).find("input.other_charge").val(packing_value);
            $(row).find("input.fright_charge").val(transport_value);
            $(row).find("td").find("input.taxable_amount").val(itemFinalTotal);
            $(row).find("td").find("input.new_taxable").val(itemFinalTotal)
            $(row).find("td").find("input.gst_amount").val(gst_amount)

            recalculateFinalValues();

         });
      }
   </script>
   <script type="text/javascript">
      //$("#packing_calculate").click(function() {
      $(".packing_calculate").change(function() {
         // alert();
         cdadCalculate();
      })

      $(".transport_calculate").change(function() {
         cdadCalculate();
      })

      $(document).ready(function() {
         var inquiry_id = '<?= $_REQUEST['inquiry_id'] ?>';
         var mode = '<?= $_REQUEST['mode'] ?>';
         if (mode == "add" && inquiry_id != "") {
            $(".discount").trigger('change');
            cdadCalculate();
            /* $(".cd_calculate").trigger("change");
             $(".ad_calculate").trigger("change");
             $(".packing_calculate").trigger("change");
             $(".transport_calculate").trigger("change");	*/
         }
      });
   </script>
   <script type="text/javascript">
      // function getTermescondition() 
      // {

      //     var companytype = $("#type_of_company").val();
      //     $.ajax({
      //         type: "post",
      //         url: "get_terms_condition_ajax.php",
      //         data: "companytype=" + companytype,
      //         beforeSend: function() {
      //             $('.preloader').fadeIn('slow'); 
      //         },
      //         success: function(result) {
      //             setTimeout(function() {
      //                 $('.preloader').fadeOut('slow'); 
      //                 CKEDITOR.instances['terms_comdition'].setData(result); 
      //             });
      //         }           
      //    })            
      // }
   </script>
   <script type="text/javascript">
      $(function() {
         aj.imageHolder($("input[name=attachment]"), "", "",
            function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
               isImageThumbnailLoaded = isImageThumbnailLoadedReply;
               isImageThumbnailValidT = isImageThumbnailValidReply;
               //toastr.success("Old Image Found!!");
            },
            function(file, img) {
               if (!file) {
                  toastr.error("File may be corrupted or missing. Try again!!");
               }
            },
            function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
               isImageThumbnailLoaded = isImageThumbnailLoadedReply;
               isImageThumbnailValidT = isImageThumbnailValidReply;
               //toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
            },
            function(data) {
               isImageThumbnailLoadedReply
            },
            ["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF"]
         );
      })
   </script>
   <script type="text/javascript">
      $("#dealer_id").click(function() {
         var customer_id = $("#dealer_id").val();
         $.ajax({
            type: 'POST',
            url: 'get_company_customer_type_detail_ajax.php',
            data: {
               customer_id: customer_id
            },
            beforeSend: function() {
               // 
            },
            success: function(result) {
               result = JSON.parse(result);
               if (result.ack == 1) {
                  $("#type_of_company").select2("val", result.type_of_company);
                  $("#customer_type").select2("val", result.type_of_executive);
                  // getTermescondition(result.type_of_company);
                  $('#requesting_ajax_change_shipping').trigger('show.bs.modal');
                  // setTimeout($("#customer_id").select2("val",result.customer_id), 3000);
                  // $("#customer_id").select2("val",result.customer_id);
               }
            }
         });
      })
   </script>
   <script type="text/javascript">
      function getTermsDescr() {
         var terms_condition_id = $("#terms_condition_id").val();
         $.ajax({
            type: "post",
            url: "ajax_get_terms_description.php",
            data: "id=" + terms_condition_id,
            beforeSend: function() {
               $(".transCover").fadeIn(800);
               $('.preloader').fadeIn('slow');
            },
            success: function(result) {
               $('.preloader').fadeOut('slow');
               CKEDITOR.instances['terms_comdition'].setData(result);
            }
         })
      }

      function getCustomerCount(cusid) {
         // alert();
         $.ajax({
            type: "post",
            url: "ajax_get_customer_count.php",
            data: "customer_id=" + cusid + "&type=quotation_detail",
            success: function(data) {
               var result = JSON.parse(data);
               $("#tot_quotation_cnt").html(result.total_customer_cnt);
               $("#monthly_quotation_cnt").html(result.current_month_customer_cnt);
            }
         })
      }

      function getRegards(R) {
         // alert($("#sales_executive").find("option:selected").attr("data-regrads"));
         CKEDITOR.instances['faithfully'].setData($("#sales_executive").find("option:selected").attr("data-regrads"));
      }
   </script>
</body>

</html>