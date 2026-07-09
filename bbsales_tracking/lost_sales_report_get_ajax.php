<?php
$page_id=599;$page_slug='visit_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "sales_executive";
$ctable1 	= "Lost Sales";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

	$ctable_where .= " ( name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<!-- <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script>  -->

<style type="text/css">

</style>
<form action="" name="frm" id="frm" method="post">
	<table id="" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="5%;">No.</th>
                <th>Sales Person Name</th>
                <th>Lost Quotation Count</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $order=0;          
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?= $ctable_d['name'] ?></td>
                <td>
                	<a href='#myModal' data-title="Lost Quotation" data-sales_id="<?= $ctable_d['id'] ?>" data-mode="lost_quotation" data-toggle='modal'>
                	<?= $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND sales_id='".$ctable_d['id']."' AND status=5 ") ?>
                	</a>
                </td>
                <?php
                // $lead_r=$db->rp_getData("no_order_inquiry","*","isDelete=0 AND inquiry_lead_flag=1 AND source_of_inquiry='".$ctable_d['id']."' GROUP BY dealer_id","",0);
                // while($lead_d=mysqli_fetch_assoc($lead_r))
                // {
                // 	$order+=$db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND (status = 1 OR status = 2 Or status = 4) AND customer_id='".$lead_d['dealer_id']."' ",0);
                // 	// if($order=0 || $order="null")
                // 	// {
                // 	// 	$order=0;
                // 	// }
                // }
                
                ?>
                <!-- <td><?= $order ?></td> -->
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
												echo ' selected="selected"';
											}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
												echo ' selected="selected"';
											}  ?>>5000</option>
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
</form>

<div id="myModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content modal-lg" style="margin-left: -143px;">
            <div class="modal-body portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <h4 class="modal-title model_title"></h4>
                        <input type="hidden" class="mode" value="">
                        <input type="hidden" class="mode" value="">
                    </div>
                    <div class="tools">

                        <a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

                        <a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
                    </div>
                </div>
                <div class="portlet-body portlet-empty" style="">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
</script>

<!-- zoom image js -->
<!-- <script src="js/zoom-jquery-1.4.4.min.js" type="text/javascript"></script> -->
<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="zoom-image/cloud-zoom/cloud-zoom.1.0.2.js"></script> -->
<!-- zoom image js -->

<script type="text/javascript">
	$("#sales_executive").select2();
	$("#customer_id").select2();

	$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})

</script>

<script type="text/javascript">
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


$('#myModal').on('show.bs.modal', function (event) 
{
	var button = $(event.relatedTarget) // Button that triggered the modal
	var sales_id=button.data("sales_id");
    $(".sales_id").val(sales_id);
    $("#requesting_ajax").attr("data-url","lost_quotation_get_ajax.php?sales_id="+sales_id);
    $("#requesting_ajax").click();
})

$('body').removeClass('modal-open');
$('.modal-backdrop').remove();

</script>
<?php require_once("disconnect.php"); ?>