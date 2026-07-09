<?php
$page_id=638;$page_slug='page_product_final';
include("connect.php");
$FromDate = date('d-m-Y',strtotime('01-04-2021'));
$ToDate = date("d-m-Y");
?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datetimepicker/jquery.datetimepicker.css"/>
</head>
<div id="print_info">
	<div class="row">
		<div class="col-md-6  col-xs-6  col-sm-6 pull-right" style="margin-top:10px">
			<div class="form-inline" role="form">
			   <div class="form-group">
					<label>Filter By Date : &nbsp;</label>
					<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
				</div>
				<div class="form-group">
					<label>&nbsp;&nbsp;</label>
					<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
				</div>
				<div class="form-group">
					<input class="btn btn-danger btn-sm" type="submit" value="Filter" id="getByDate">
					<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="put_date"></div>
	</div>
</div>

<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
	$('#ToDate').datepicker({  datepicker: true, autoclose: true });
	$('#FromDate').datepicker({  datepicker: true, autoclose: true });
</script>

<script type="text/javascript">
	$("#getByDate").click(function(){
		var date1 = $("#ToDate").val();  /*february 25th*/
		var date2 = $("#FromDate").val();  /*february 26th*/

		if ($.datepicker.parseDate('dd-mm-yy', date2) <= $.datepicker.parseDate('dd-mm-yy', date1)) {
			GetStockDetail();
		}else
		{
			alert("From Date Should Be Less Than To Date");
		}
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
		GetStockDetail();
	});

	function GetStockDetail()
	{
		var pro_id='<?php echo $_REQUEST['pro_id']?>';
		var weight_id='<?php echo $_REQUEST['weight_id']?>';
		ToDate_1 = String($("#ToDate").val());
		FromDate_1 = String($("#FromDate").val());
		$.ajax({
			type: "POST",
			url: "view_stock_detail_get_ajax.php",
			data: 'pro_id='+pro_id+'&weight_id='+weight_id+'&to_date='+ToDate_1+'&from_date='+FromDate_1,
			beforeSend: function() {
				$(".transCover").fadeIn(800);
			},
			success: function(json)
			{
				$(".put_date").html(json);
			}
		});
	}

	function clearSearchByName()
	{
		ToDate = "";
		FromDate = "";
		$("#ToDate").val("");
		$("#FromDate").val("");
		GetStockDetail();
	}
</script>