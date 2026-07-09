<?php
$page_id=648;$page_slug='top_20_customer';
$ctable 	= "executive";
$ctable1 	= "Top 20 Customer";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = $ctable1." Report";
$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
			<div class="row">
				<div class="col-md-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-md-12">
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters </div>
                             <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
							</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
								<div class="row">
                                    <!-- <div class="col-md-6 col-xs-6 col-sm-6" style="margin-top:10px">
										<form class="form-inline" role="form" onSubmit="return searchByName();">
											<div class="form-group">
                                                <label>Search By Name : &nbsp;</label>
                                                <input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search Here" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input class="btn btn-danger btn-sm" type="submit" value="search">
                                            </div>
                                             <div class="form-group">
                                                   	<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                            </div>
										</form>
                                    </div>
                                    <div class="col-md-3 col-xs-2 col-sm-2 pull-right" style="margin-top:10px" >
										<button type="button" class="btn green-haze excel btn-sm" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn print btn-sm" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genVisitPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
                                	</div> -->
                                	<div class="col-md-6 col-xs-6 col-sm-6" style="margin-top:10px"> 
                                	</div>
                                	<div class="col-md-6 col-xs-6 col-sm-6" style="margin-top:10px">
		                                <div class="form-inline" role="form">
		                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
		                                       	<div class="form-group">

		                                          <input type="text" style="width: 250px!important" placeholder="Search By Company / Customer Name" class="form-control input-large" name="searchName" id="searchName" value="" />

		                                       	</div>

		                                       	<div class="form-group">
		                                          <input class="btn btn-danger btn-sm" type="submit" value="search">
		                                       	</div>

		                                       	<div class="form-group">
		                                          <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
		                                       	</div>

		                                       	<div class="form-group">
				                                    <div class="btn-group">

														<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
															<i class="fa fa-gear"></i>
														</button>
														
														<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">
															<!-- <li>
																<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
															</li> -->
															<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>
															<li>
																<a name="print" onClick="genReportPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
															</li>
															<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
															<li>
																<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
															</li>
															<?php
										}
										?>
														</ul>
													</div>
		                                       	</div>
		                                    </form>
		                                </div>
                         			</div>
                            	</div>
                            </div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
				<div class="col-md-12">
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										//echo $db->getAddButton($ctable);
									?>	
								</div>
							</div>
						</div>
						<div class="portlet-body">
							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div class="table-responsive">
								<div id="results"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 
<!-- view image modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
        <div class="modal-content" style="margin-top: -41px">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel"><b>View Image</b></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 15px;top: 25px">
              <span aria-hidden="true">&times;</span>
            </button>
          </div> 
          <div class="portlet-body" id="requesting_ajax" style=""></div> 
        </div>
  	</div>
</div>
<!-- view image modal -->

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
	$("#sales_executive").fSelect();
	$("#customer_id").fSelect();
</script>

<script type="text/javascript">
var searchName="";
var state="";
var city="";
var df1="";
var sales_executive="";
var customer_id="";
var area="";
var class_id="";
var data_url = "top20_customer_report_get_ajax.php";
// var data_url = "index_demo.php";


function searchByName(){
	searchName = $("#searchName").val();
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	displayRecords(500,1);
	return false;
}
$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(500,1);
})

function clearSearchByName(){
	searchName = "";
	sales_executive = "";
	customer_id = "";
	df1 = "";
	$("#searchName").val("");
	$("#customer_id").val("");
	$("#sales_executive").val("");
	$("#material_request_filter_input").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  // { "sWidth": "10%" },
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1 + "&class_id=" + class_id + "&area=" + area ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });

	// $("#results").on( "change", "#sales_executive", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1  + "&class_id=" + class_id + "&area=" + area,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });

	// $("#results").on( "change", "#customer_id", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1 + "&class_id=" + class_id + "&area=" + area,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
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

<script type="text/javascript">
	function genReport(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	// window.location.href='visit_genReport_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&customer_id='+customer_id+'&df='+df1;
      	$.ajax({
	        method: "POST",
	        url: "top20_customer_genReport_ajax.php",
	        data:{
        		searchName:searchName,
			},	
			dataType : 'json',
			beforeSend: function()
			{
				$("#loading-modal").modal('show');
			},
        	success: function(result){
        		$("#loading-modal").modal('hide');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }
</script>

<script type="text/javascript">
	function genReportPrint(){
		var searchName     = $("#searchName").val();
      	var sales_executive = $("#sales_executive").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_top20_customer_ajax.php?searchName='+searchName,'','width=700,height=800');
     	myWindow.print();
  //    	setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	}, 200);
		// }, 500);
    }
</script>

</body>
</html>