<?php
$page_id=648;$page_slug='top_20_customer';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "executive";
$ctable1 	= "Top 20 Customer";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
// echo "ecec"; exit;
	$company_name=$db->rp_getValue("executive","id","isDelete=0 AND company_name LIKE '%".$db->clean($_REQUEST['searchName'])."%' ",0);
	$c_name=$db->rp_getValue("executive","id","isDelete=0 AND cname LIKE '%".$db->clean($_REQUEST['searchName'])."%' ");

	$ctable_where .= " customer_id='".$company_name."' OR customer_id='".$c_name."' AND ";
	// $ctable_where .= " ( company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR cname like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}

// $ctable_where .= " isDelete=0";

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

	$ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_where.=" isDelete=0 AND (status = 1 OR status = 2 Or status = 4) GROUP BY customer_id ";
// $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0,"20");
$ctable_r = $db->rp_getData("orders","SUM(grand_total),customer_id",$ctable_where,"SUM(grand_total) DESC",0,"20");

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
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Order Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $order=0;          
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            // print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?= $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$ctable_d['customer_id']."' ",0); ?></td>
                <td><?= $db->rp_getValue("executive","cname","isDelete=0 AND id='".$ctable_d['customer_id']."' ",0); ?></td>
                <td><?= $db->rp_num($ctable_d['SUM(grand_total)'],2)?></td>
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    
    <!-- <div class="row">
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
	</div> -->
</form>

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
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
</script>
 <?php require_once("disconnect.php"); ?>