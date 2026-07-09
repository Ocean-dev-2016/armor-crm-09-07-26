<?php
// print_r($_REQUEST);
$page_id=619;$page_slug='product_vise_order_report';
include("connect.php");
include("../include/no_to_word.php");
$ctable     = "product_weight_price";
$ctable1    = "Orders";
$ctable_where = "";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
    $product_id = $db->rp_getData("product","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
    if($product_id)
    {       
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
    $isFillter=true;
}

if(!isset($_REQUEST['cat_id']) || $_REQUEST['cat_id']=="" || $_REQUEST['cat_id'] == NULL)
{
    if(isset($_REQUEST['top_cat']) && $_REQUEST['top_cat']!="" && $_REQUEST['top_cat'] != NULL)
    {
        $product_id2 = $db->rp_getData("product","*","tcid='".$_REQUEST['top_cat']."' AND isDelete=0","",0);
        if($product_id2)
        {       
            while($K1=mysqli_fetch_assoc($product_id2))
            {
                $PRODUCT_IDS2[]=$K1['id'];
            }
            $PRODUCT_IDS2=implode(",",$PRODUCT_IDS2);

            if($_REQUEST['cat_id'] != "")
            {
                $ctable_where .=" product_id IN (".$PRODUCT_IDS2.") OR";
            }
            else
            {
                $ctable_where .=" product_id IN (".$PRODUCT_IDS2.") AND";
            }
        }
        else
        {
            $ctable_where .=" product_id IN ('') AND ";
        }
        $isFillter=true;
    }
}

if(isset($_REQUEST['cat_id']) && $_REQUEST['cat_id']!="" && $_REQUEST['cat_id'] != NULL)
{
    $product_id1 = $db->rp_getData("product","*","cid='".$_REQUEST['cat_id']."' AND isDelete=0","",0);
    if($product_id1)
    {       
        while($K1=mysqli_fetch_assoc($product_id1))
        {
            $PRODUCT_IDS1[]=$K1['id'];
        }
        $PRODUCT_IDS1=implode(",",$PRODUCT_IDS1);
        $ctable_where .=" product_id IN (".$PRODUCT_IDS1.") AND";
    }
    else
    {
        $ctable_where .=" product_id IN ('') AND ";
    }
    $isFillter=true;
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where1 .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  )";
    $isFillter=true;
}

if ($isFillter)
{
    $ctable_where .= " isDelete=0 "; 

    $item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 50;

    if (isset($_REQUEST["page"])) 
    {
        $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if (!is_numeric($page_number)) 
        {
            die('Invalid page number!');
        } 
    } 
    else 
    {
        $page_number = 1; //if there's no page number, set it to 1
    }

    $get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where, 0); //hold total records in variable

    //break records into pages
    $total_pages = ceil($get_total_rows / $item_per_page);

    //get starting position to fetch the records
    $page_position = (($page_number - 1) * $item_per_page);

    $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
}
?>
<style type="text/css">
    .th{
        vertical-align: bottom;
    }
    .total_value{
        text-align: right;
    }
</style>
<div class="table-responsive">
<form action="" name="frm" id="print_info" method="post">
    <table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
        <thead>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class="total_value"><b id="total-value-quotation"></b></td>
                <td></td>
                <td class="total_value"><b id="total-value-order"></b></td>
               <!--  <td></td>
                <td class="total_value"><b id="total-value-invoce"></b></td> -->
            </tr>
            <!-- --- milan --- 22-06-2021 --- -->
            <tr class="tr">
                <th class="th">Sr. No.</th>
                <th class="th">Product Name.</th>
                <th class="th">Quotation Qty</th>
                <th class="th">Quotation Value</th>
                <th class="th">Order Qty</th>
                <th class="th">Order Value</th>
               <!--  <th class="th">Invoice Qty</th>
                <th class="th">Invoice Value</th> -->
            </tr>
        </thead>
        <tbody>
        <?php
        if ($isFillter) 
        {
            if(mysqli_num_rows($ctable_r)>0)
            {
                $count = 1;
                while($ctable_d = mysqli_fetch_array($ctable_r))
                {
                    $prodct_name = $db->rp_getValue("product","name","id='".$ctable_d['product_id']."' AND isDelete = 0 ",0);
                    $size = $db->rp_getValue("weight","name","id='".$ctable_d['weight_id']."' AND isDelete = 0 ",0);

                    ?>
                    <tr class="tr">
                        <td class="td" style="width:5px;"><?php echo $count++; ?></td>
                        <td class="td1">
                            <?php if($ctable_d['weight_id']!=-1){echo stripslashes($prodct_name)." - ".$size." - ".strtoupper($ctable_d['catno']); } else { echo $prodct_name." -- ".strtoupper($ctable_d['catno']); } ?>
                        </td>
                        <?php 
                        $quotation_total_qty = $db->rp_getValue("quotation_product_item","SUM(pro_qty)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1  AND isDelete = 0 ",0);
                        $quotation_total_price = $db->rp_getValue("quotation_product_item","SUM(totalprice)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1  AND isDelete = 0 ",0);

                        $order_total_qty = $db->rp_getValue("order_product_item","SUM(pro_qty)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1  AND isDelete = 0 ",0);
                        $order_total_price = $db->rp_getValue("order_product_item","SUM(totalprice)","pro_id='".$ctable_d['product_id']." AND weight_id='".$ctable_d['weight_id']." $ctable_where1  AND isDelete = 0 ",0);

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

                        <!-- <td class="td1" style="text-align: right;"><?php echo $invoice_total_qty; ?></td>
                        <td class="td1" style="text-align: right;"><?php echo number_format($invoice_total_price,2); ?></td> -->
                    </tr>
                    <?php
                }
            }
            
        }
        else
        {
            ?>
            <tr><td colspan="6" class="text-center"><h3><strong><?= FILTER_INFO ?></strong></h3></td></tr>
            <?php
        }
        ?>
        </tbody>
    </table>
     <!-- --- milan --- 22-06-2021 --- -->
    <script>
        $("#total-value-quotation").html("<?php echo number_format($final_total_quotation,2); ?>")
        $("#total-value-order").html("<?php echo number_format($final_total_order,2); ?>")
        $("#total-value-invoce").html("<?php echo number_format($final_total_Invoice,2); ?>")
    </script>
    <!-- --- milan --- 22-06-2021 --- -->
</form>
</div>
<div class="row">
        <div class="col-md-6">
            <div class="dataTables_info"> Rows Limit:
                <select id="numRecords" onChange="changeDisplayRowCount(this.value);">
                    <option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>50</option>
                    <option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>100</option>
                    <option value="500" <?php if ($_REQUEST["show"] == 500) {echo ' selected="selected"';}  ?>>500</option>
                    <option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
                    <option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
                    <option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <?php
                    echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
                    ?>
                </ul>
            </div>
        </div>
    </div>
<script>
function genReport(cid){
    if($("#datatable_1").find("tbody").find("tr").length>=2)
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
                setTimeout(function(){
                    $(".transCover").fadeOut(100);
                    alert("Report file generated!!");
                    $("#loading").modal('hide');
                    window.location.href=result;
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
    $(".filterBtn").on("click",function()
    {
        sales_executive = $("#sales_executive").val();
        customer_id = $("#customer_id").val();
        df1=$("#material_request_filter_input").val();
        df1 = encodeURI(df1)
        displayRecords(100,1);
    })
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
    $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
    });
</script>
<?php require_once "disconnect.php"; ?>