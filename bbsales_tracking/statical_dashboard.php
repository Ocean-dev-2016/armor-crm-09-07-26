<?php 
$page_id=580;$page_slug='price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Statical Dashboard";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"pricelist_master_manage.php","title"=>$page_title));
include("connect.php");

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<style type="text/css">
   .amcharts-chart-div > a {
       display: none !important;
   }
   #chartdiv {
     width: 100%;
     height: 500px;
   }
</style>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>

</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="portlet light" >
				<div class="table-toolbar">
					<div class="row">
						
							<div class="col-md-2">
								<label>Type</label>
								<select class="form-control" id="type" name="type" onchange="getreport(this.value);">
									<option value="0">Select Type</option>
									<option value="1">Year</option>
									<option value="2">Month</option>
									<option value="3">Date</option>
								</select>
							</div>
							<div class="col-md-2" id="year_div" style="display:none;">
								<label>Year</label>
								<?php
								  $currently_selected = date('Y'); 
								  $earliest_year = 2020; 
								  $latest_year = date('Y'); 
								  ?>
								  <select id="year" name="year" class="form-control">
								  	<option value="">Select Year</option>
								  <?php
								  foreach ( range( $latest_year, $earliest_year ) as $i ) {
								    print '<option value="'.$i.'"'.($i === $currently_selected ? ' selected="selected"' : '').'>'.$i.'</option>';
								  }
								  print '</select>';
								  ?>
							</div>
							<div class="col-md-2" id="month_div" style="display:none;">
								<label>Month</label>
								<select class="form-control" name="month" id="month">
									<option value="">Select Month</option>
									<option value="13">All Month</option>
									<option value="1" label="31">January</option>
									<option value="2" label="31">February</option>
									<option value="3" label="31">March</option>
									<option value="4" label="31">April</option>
									<option value="5" label="31">May</option>
									<option value="6" label="31">June</option>
									<option value="7" label="31">July</option>
									<option value="8" label="31">August</option>
									<option value="9" label="31">September</option>
									<option value="10" label="31">October</option>
									<option value="11" label="31">November</option>
									<option value="12" label="31">December</option>
								</select>
							</div>
							<div class="col-md-2" id="todate_div" style="display:none;">
								<label>To Date</label>
								<input type="date" name="todate" id="todate" class="form-control">
							</div>
							<div class="col-md-2" id="fromdate_div" style="display:none;">
								<label>From Date</label>
								<input type="date" name="fromdate" id="fromdate" class="form-control">
							</div>
							<!-- <div class="col-md-3" id="month_report">
								<label>Select Report</label>
								<select name="report" id="report" class="form-control">
									<option value="">Select Report</option>
									<option value="1">Pie-Sales Order Report</option>
									<option value="8">Bar-Sales Order Report</option>
									<option value="2">Pie-Quotation Report</option>
									<option value="9">bar-Quotation Report</option>
									<option value="3">Pie-Invoice Report</option>
									<option value="10">Bar-Invoice Report</option>
									<option value="4">Pie-Visit Report</option>
									<option value="11">Bar-Visit Report</option>
									<option value="5">Pie-Complain Report</option>
									<option value="12">Bar-Complain Report</option>
									<option value="6">Pie-Inquiry Report</option>
									<option value="13">Bar-Inquiry Report</option>
									<option value="7">Pie-Lead Report</option>
									<option value="14">bar-Lead Report</option>
								</select>
							</div> -->
							<div class="col-md-3" id="year_report">
								<label>Select Report</label>
								<select name="report" id="report" class="form-control">
										<option>Select Report</option>
								</select>
							</div>
							<div class="col-md-2">
								<input style="margin-top:25px;" type="button" name="submit" id="submit" class="btn btn-primary" value="Submit" onClick="return get_graph();">
							</div>
						
					</div><br><br>
					<div class="row">
						<div class="col-md-12" id="quick_notes">
							<div class="row">
									<div class="col-md-12">
										<h1 align="center"><span class="datesso"></span></h1>
                        <div id="orders" class="CSSAnimationChart m-t-40 text-center"></div>
									</div>
							</div>
						</div>
					</div>
                </div>
            </div>
		</div>
	</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/sales_order_graph.js"></script>
<!-- <script type="text/javascript" src="js/sales_order_graph_chart.js"></script> -->
<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
<script type="text/javascript" src="js/amcharts/serial.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/core.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/charts.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<script type="text/javascript">
$('#type').on('change', function() {
  if ( this.value == '1')
  {
    $("#year_div").show();
    $("#month_div").hide();
  	$("#todate_div").hide();
  	$("#fromdate_div").hide();
  	// $("#year_report").show();
  	// $("#month_report").hide();
  }
  else if( this.value == '2')
  {
  	$("#year_div").show();
  	$("#month_div").show();
  	$("#todate_div").hide();
  	$("#fromdate_div").hide();

  }
  else if(this.value == '3')
  {
  	$("#year_div").hide();
    $("#month_div").hide();
  	$("#todate_div").show();
  	$("#fromdate_div").show();
  }
  else
  {
    $("#year_div").hide();
    $("#month_div").hide();
  	$("#todate_div").hide();
  	$("#fromdate_div").hide();
  }
});










/*$("#submit").click(function()
	{
		var year = $("#year").val();
		var month = $("#month").val();
		var todate = $("#todate").val();
		var fromdate = $("#fromdate").val();
		var report = $("#report").val();
		var myFormData = new FormData();
		myFormData.append("year",year);
		myFormData.append("month",month);
		myFormData.append("todate",todate);
		myFormData.append("fromdate",fromdate);
		myFormData.append("report",report);
		myFormData.append("mode","add_data");
		$.ajax({
      	url:"statical_dashboard_ajax.php",
      	type:"POST",
      	data:myFormData,
      	processData: false,
      	contentType: false,
      	beforeSend:function(){
      	},
      	success:function(result)
	    {
	    	// alert(result);
	        $("#quick_notes").html(result);
      		// toastr.success("Insert Successful...");
        	// GetInquiryAttachment('<?php echo $_REQUEST['id'] ?>','get_attachment')
        }
    });
});*/




function get_graph()
{   

    var report_name=$("#report").val();
    var month_no=$("#month").val();
    if(report_name == "8" || report_name == "9" || report_name == "10")
    {	
    	if(month_no == "13")
    	{
    		Graph2.init2();
    	}
    	else
    	{
    		Graph2.init2();
    	}

	}
	else if (report_name == "11" || report_name == "12"  || report_name == "13" || report_name =="14")
	{
		Graph3.init3();

	}
    else
    {
        Graph1.init1();

    }

}


</script>


<script type="text/javascript">
		function getreport(val) 
		{

		
			$.ajax({
				type: "POST",
				url: "get_reports_ajax.php",
				data: 'val=' + val,

				beforeSend: function() {

				},
				success: function(data) {
					$("#report").html(data);
					// $('#year_report').select2();


				
				
					
				}
			});
				


		}



	



</script>
 