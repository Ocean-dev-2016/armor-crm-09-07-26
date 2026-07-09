<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable     = "product_weight_price";
$ctable1    = "Orders";
$ctable_where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    // $ctable_where .= " (name LIKE '%".$db->clean(trim($_REQUEST['searchName']))."%' ) AND ";

    $product_id = $db->rp_getData("product","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
    if($product_id)
    {       
        // echo "hello"; exit;
        while($K1=mysqli_fetch_assoc($product_id))
        {
            $PRODUCT_IDS[]=$K1['id'];
        }
        $PRODUCT_IDS=implode(",",$PRODUCT_IDS);
        $ctable_where .=" product_id IN ('".$PRODUCT_IDS."') AND";
    }
    else
    {
        $ctable_where .=" product_id IN ('') AND ";
    }

}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where1 .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )";
}

if(isset($_REQUEST['top_cat']) && $_REQUEST['top_cat']!="" && $_REQUEST['top_cat'] != NULL){
    $ctable_where2 .= " AND top_cat_id = '".$_REQUEST['top_cat']."' ";
}

if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id']!="" && $_REQUEST['cat_id'] != NULL){
    $ctable_where3 .= " AND cat_id = '".$_REQUEST['cat_id']."' ";
}


$ctable_where .= " isDelete=0 "; 

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
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
.th{
    vertical-align: bottom;
}
.total_value{
    text-align: right;
}
</style>
<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
        <thead>
             <!-- <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="total_value"><b id="total-value-quotation"></b></td>
                <td></td>
                <td class="total_value"><b id="total-value-order"></b></td>
                <td></td>
                <td class="total_value"><b id="total-value-invoce"></b></td>
            </tr> -->
           <tr class="tr">
                
                <th class="th">Sr. No.</th>
                <th class="th">Product Name.</th>
                
                <th class="th">Quotation Qty</th>
                <th class="th">Quotation Value</th>
                <th class="th">Order Qty</th>
                <th class="th">Order Value</th>
                <th class="th">Invoice Qty</th>
                <th class="th">Invoice Value</th>
                
            </tr>
            
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 1;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            ?>
                <tr class="tr">
                    <td class="td" style="width:5px;"><?php echo $count++; ?></td>
                    <td class="td1"><?php echo $db->rp_getValue("product","name","id='".$ctable_d['product_id']."' AND isDelete = 0 ",0); echo $db->rp_getValue("weight","name","id='".$ctable_d['weight_id']."' AND isDelete = 0 ",0); ?> 
                                    </td>
                    <?php 
                    // echo $ctable_where1; exit();
                    $quotation_total_qty = $db->rp_getValue("quotation_product_item","SUM(pro_qty)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);
                    $quotation_total_price = $db->rp_getValue("quotation_product_item","SUM(totalprice)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);

                    $order_total_qty = $db->rp_getValue("order_product_item","SUM(pro_qty)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);
                    $order_total_price = $db->rp_getValue("order_product_item","SUM(totalprice)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 $ctable_where2 $ctable_where3 AND isDelete = 0 ",0);

                    $invoice_total_qty = $db->rp_getValue("invoice_new_product_item","SUM(pro_qty)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 AND isDelete = 0 ",0);
                    $invoice_total_price = $db->rp_getValue("invoice_new_product_item","SUM(totalprice)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1 AND isDelete = 0 ",0);

                    $final_total_quotation += $quotation_total_price;
                    $final_total_order += $order_total_price;
                    $final_total_Invoice += $invoice_total_price;
                        
                    ?>
                    <td class="td1" style="text-align: right;"><?php echo $quotation_total_qty; ?></td>
                    <td class="td1" style="text-align: right;"><?php echo number_format($quotation_total_price,2); ?></td>
                    <td class="td1" style="text-align: right;"><?php echo $order_total_qty; ?></td>
                    <td class="td1" style="text-align: right;"><?php echo number_format($order_total_price,2); ?></td>

                    <td class="td1" style="text-align: right;"><?php echo $invoice_total_qty; ?></td>
                    <td class="td1" style="text-align: right;"><?php echo number_format($invoice_total_price,2); ?></td>
                </tr>
            <?php
                }
            }
            
        ?>
        </tbody>
    </table>
     <script>
        $("#total-value-quotation").html("<?php echo number_format($final_total_quotation,2); ?>")
        alert()
        $("#total-value-order").html("<?php echo number_format($final_total_order,2); ?>")
        $("#total-value-invoce").html("<?php echo number_format($final_total_Invoice,2); ?>")
    </script>
    <?php require_once "disconnect.php"; ?>
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
