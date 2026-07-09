<?php
$page_id=608;$page_slug='inquiry_cancel_report';
$ctable 	= "no_order_inquiry";
$ctable1 	= "Inquiry";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = "Inquiry Cancel Report";
$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>$ctable."_manage.php","title"=>$page_title));
$FromDate="";
$ToDate="";
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
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
				<div class="col-md-12 "><br/>
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
									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<label>Filter By Date</label>
									  	<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span>
											<span class="input-group-btn">
									        <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
									        </span>
									        </div>
                                    </div>
                                    	<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group">
												<label>Search by Company</label><br/>
												<select class="form-control status"  name="type_of_company" id="type_of_company">
														<option value=""> Select Company</option>
														<?php
						                                	$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
						                                	if(mysqli_num_rows($company_r)>0)
						                                	{
						                                		while($company_d = mysqli_fetch_array($company_r))
						                                		{
						                                		?>
						                                			<option value="<?php echo $company_d['id']; ?>" <?=($type_of_company == $company_d['id'])?"selected":"";?>><?php echo $company_d['name']; ?></option>
						                                		<?php
						                                		}
						                                	}
						                                ?>                          
								                </select>
										     </div>
                                        </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group" role="form">
												<label>Search by State </label>
	                                            <select class="form-control status" multiple="multiple" name="state" id="state">
	                                            	<option value="">Select State</option>
													<?php
													$id_r = $db->rp_getData("state","*",0);
													if(mysqli_num_rows($id_r)>0)
													{
														while($id_d = mysqli_fetch_array($id_r))
														{
															?>
															<option value="<?php echo $id_d['id']; ?>"><?php echo $id_d['name']; ?></option>
															<?php
														}
													}
													?>
	                                            </select>	
	                                        </div>
                                    </div>
									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
											<label>Search by City</label>
											<select class="form-control" multiple="multiple" name="city" id="city">
												<option value="">Select City</option>
	                                        </select>
	                                    </div>      
                                    </div>

                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
											<label>Search by Route</label>
											<select class="form-control" multiple="multiple" name="route" id="route">
												<option value="">Select Route</option>
	                                        </select>
	                                    </div>      
                                    </div>


	                                <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
	                                        <div class="form-group" >
	                                            <label>Search By Inquiry Taken By</label>
	                                            <select class="form-control status" multiple="multiple" name="type" id="type">
													<option value="">Search Inquiry Taken By</option>
													<?php 
													$se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
													if($se_r)
													{
														while($se_d=mysqli_fetch_assoc($se_r))
														{
															?>
															<option value="<?php echo $se_d['id'];?>"><?php echo $se_d['name']; ?></option>
															<?php
														}
													}
													?>
	                                            </select>
											</div>
	                                </div> 
	                                <div class="col-md-4 col-xs-4 col-sm-4 pull-right" style="margin-top:34px" >
										<div>
									  		<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
									   			<div class="form-group ">
													<input type="text" placeholder="Search By Person / Company / Phone:" style="width: 300px!important" class="form-control input-medium" name="searchName" id="searchName" value=""  />
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
															<?php
																if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																{ 
																	?>
															<li>
																<a name="print" onClick="geninquiryPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
															</li>
															<?php
																}
																if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																{ 
																	?>
															<li>
																<a class="excel" name="excel" onClick="genReporttt()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
                                <div class="row">
                                	
									<!-- <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										<?php
										if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
										{ 
											?>
											<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="geninquiryPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
											<?php
										}
										if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
										{ 
											?>
											<button type="button" class="btn green-haze btn-sm" name="export" onClick="genReporttt(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i>Excel</button>
											<?php
										}
										?>
									</div> -->
								</div>
							</div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results"></div>
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>

<script type="text/javascript">
	$("#state").fSelect();
	$("#city").fSelect();
	$("#type").fSelect(); 
	$("#route").fSelect(); 
</script>

<script type="text/javascript"> 
var df1="";
var type="";
var searchName=""; 
var city_id="";
var state_id="";
var type_of_company="";
var isFillter=false;
var data_url = "inquiry_cancel_report_get_ajax.php";
 
function searchByName(){
	searchName = $("#searchName").val();
	state_id = $("#state").val();
	city_id = $("#city").val();
	type = $("#type").val(); 
	type_of_company = $("#type_of_company").val(); 
	isFillter=true;
	displayRecords(500,1); 
	return false;
}
function clearSearchByName(){ 
	searchName = "";
	type = "";
	type_of_company = "";
	df1 = "";
	// ToDate = "";
	// FromDate = "";
	city_id = "";
	state_id = "";
	isFillter=false;
	// $("#ToDate").val("");
	// $("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#searchName").val("");

	$("#state").fSelect("destroy");
	$("#state").val("");
	$("#state").fSelect("create");

	$("#city").fSelect("destroy");
	$("#city").val("");
	$("#city").fSelect("create");

	$("#type").fSelect("destroy");
	$("#type").val("");
	$("#type").fSelect("create");

	$("#state_id").fSelect("destroy");
	$("#state_id").val("");
	$("#state_id").fSelect("create");

	$("#state").fSelect("destroy");
	$("#state").val("");
	$("#state").fSelect("create");

	$("#type_of_company").select2("val","");

	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});

$("#state").on('change', function() {
	var state = $("#state").val();
	filter_class(state,"");
});

$("#city").on('change', function() {
	var city = $("#city").val();
	filter_city(city,"");
});

// $("#state").on('change', function() {
// 	var state = $("#state").val();

// 	filter_state(state,"");
// });

function filter_class(class_id,area=""){
    $.ajax({
        type: "POST",
        url: "find_area_filter.php",
        data:'class_id='+class_id+"&area="+area,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
        success: function(data){
            $("#city").select2("destroy");
       		$("#city").fSelect("destroy");
        	$("#city").html(data);
       		$("#city").fSelect('create');
            // $("#loading-modal").modal('hide');
            $('.preloader').fadeOut('slow');
        }
    });
}

function filter_city(area,route=""){
    $.ajax({
        type: "POST",
        url: "find_route_filter.php",
        data:'area='+area+"&route="+route,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
        success: function(data){
            $("#route").select2("destroy");
       		$("#route").fSelect("destroy");
        	$("#route").html(data);
       		$("#route").fSelect('create');
            // $("#loading-modal").modal('hide');
            $('.preloader').fadeOut('slow');
        }
    });
}

// function filter_state(state_id,city_id=""){
// 	// alert(state_id);
//    	$.ajax({
//         type: "POST",
//         url: "find_city.php",
//        	data:'state_id='+state_id+"&city="+city_id,
// 		beforeSend:function(){
// 			// $("#loading-modal").modal('show');	
// 			$('.preloader').fadeIn('slow');
// 		},
//         success: function(data){
//         	$("#city").select2("destroy");
//        		$("#city").fSelect("destroy");
//         	$("#city").html(data);
//        		$("#city").fSelect('create');
// 			// $("#loading-modal").modal('hide');
// 			$('.preloader').fadeOut('slow');
//         }
//     });
// }

// function filter_city(city_id){
// 	state_id = $("#state").val();
// 	city_id = $("#city").val();
// 	displayRecords(500,1);
// }	 
function getSalesExecutive(type){
	type=type;
}

function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(500,1);
	}
	else
	{
		alert("Please Select Date");
	}
}

$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	// sales_executive = $("#sales_executive").val();
	df1 = encodeURI(df1)
	displayRecords(500,1);
})

function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			{ "sWidth": "10%" }, 
			{ "sWidth": "30%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%" },
			{ "sWidth": "20%","bSortable": false }
		],
		"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}

function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1 +  "&type_of_company=" + type_of_company + "&isFillter="+isFillter,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1 + "&type_of_company=" + type_of_company + "&isFillter=" + true,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
	}); 
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
}); 

function genReportexcel()
{
	var show = $("#numRecords").val();
	state = $("#state").val();
	city_id = $("#city").val();
	df1 = $("#material_request_filter_input").val();
	// alert(status);
	$.ajax({
		type: "POST",
		url: "inquiry_cancel_report_genreport_excel.php",
		data: '&searchName='+searchName+'&type='+type +"&state=" + state +"&city_id=" + city_id +"&show=" + show +"&status=" + status +"&df1=" + df1,
		beforeSend: function() 
		{
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result)
		{ 
			setTimeout(function(){
				$(".transCover").fadeOut(500);
				$("#loading").modal('hide');
				window.location.href=result;
			},1500);
		}
	});
}  

function genReporttt()
{
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	var customer_type = $("#customer_type").val();
	var customer_id = $("#customer_id").val();
	var df1 = $("#material_request_filter_input").val();
	var state = String($("#state").val());
	var city = String($("#city").val());
	var type = String($("#type").val());
	var customer_status = $("#customer_status").val();
	var month = $("#month").val();
	var type_of_company = $("#type_of_company").val();
	var df1=$("#material_request_filter_input").val();
	$.ajax({
        method: "POST",
        url: "inquiryy_cancel_report_excel.php",
        data:{
    		searchName:searchName,
			customer_type:customer_type,
			customer_id:customer_id,
			df1:df1,
			customer_status:customer_status,
			state:state,
			city:city,
			type:type,
			month:month,
			type_of_company:type_of_company
		},	
		dataType : 'json',
		beforeSend: function()
		{
			
		},
    	success: function(result)
    	{
    		// alert(result);
    		window.location.href="<?=SITEURL?>"+result.file_path;
    	},
		/*error:function(result){
			window.location.href="<?=SITEURL?>"+result.file_path;
		}*/
	});
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
 		$(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD')+" to "+picker.endDate.format('YYYY-MM-DD'));
	});
</script>
<script type="text/javascript">
	function geninquiryPrint()
	{
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
		var type = $("#type").val(); 
		state_id = $("#state").val();
		city_id = $("#city").val();
		type_of_company = $("#type_of_company").val();
		df1 = $("#material_request_filter_input").val();
		var show = $("#numRecords").val();
		var myWindow = window.open('print_inquiry_cancel_report_ajax.php?searchName='+searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1 + "&type_of_company=" + type_of_company + "&show=" + show ,'','width=700,height=800');
		myWindow.print();
  //    	setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	}, 1000);
		// }, 500);
    }
</script>
</body>
</html>