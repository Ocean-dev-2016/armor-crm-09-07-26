<?php
$page_id=600;$page_slug='inquiry_report_page';
$ctable 	= "no_order_inquiry";
$ctable1 	= "Inquiry";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = "Inquiry Report";
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
				<div class="col-xl-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-xl-12 ">
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
										<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
								  			<label>Filter By Date</label>
									  		<div class="input-group">
												<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
												<span class="input-group-addon datetimerange-picker-btn">
													<i class="fa fa-calendar"></i>
												</span>
											
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
														<option value="0">All</option>
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
														if(mysqli_num_rows($id_r)>0){
															while($id_d = mysqli_fetch_array($id_r)){
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
                                    	<?php
										if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
				                    	{ 
										?>
		                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
		                                        <div class="form-group" >
		                                            <label>Search By Inquiry Taken By</label>
		                                            <select class="form-control status" multiple="multiple" name="type" id="type">
														<option value="">Search Inquiry Taken By</option>
														<?php 
															$se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1 AND type!='service_executive' ");
															
															if($se_r){
																while($se_d=mysqli_fetch_assoc($se_r)){
																	?>
																	<option value="<?php echo $se_d['id'];?>"><?php echo $se_d['name']; ?></option>
																	<?php
																}
															}
														?>
		                                            </select>
												</div>
		                                    </div>
	                                    <?php 
	                                	}
	                                	?>
	                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
	                                        <div class="form-group" >
	                                            <label>Search By Status </label>
	                                            <select class="form-control status" multiple="multiple" id="status_id" name="status_id">
							                    	<option value="">Select Status</option>
							                    	<option <?= ($status_id==0 && $status_id!="")?"selected":""; ?> value="0">Generate</option>
							                    	<option <?= ($status_id==1)?"selected":""; ?> value="1">In Followup</option> 
							                    	<option <?= ($status_id==2)?"selected":""; ?> value="2">Interested</option>
							                    	<option <?= ($status_id==3)?"selected":""; ?> value="3">Buy Later</option>
							                    	<option <?= ($status_id==4)?"selected":""; ?> value="4">Hot</option> 
							                    	<option <?= ($status_id==5)?"selected":""; ?> value="5">Cold</option> 
							                    	<option <?= ($status_id==6)?"selected":""; ?> value="6">Warm</option> 
							                    	<option <?= ($status_id==11)?"selected":""; ?> value="11">Lost</option> 
							                    	<option <?= ($status_id==-1)?"selected":""; ?> value="-1">Not Interested</option><option <?= ($status_id==-2)?"selected":""; ?> value="-2">Non Relavent</option>  
							               		</select>
											</div>
                                    	</div>
	                                    
                                    </div>
                                <div class="row">
                                	
						 <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                                <div class="form-inline" role="form">
                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                       	<div class="form-group">

                                          <input type="text" style="width: 350px!important" placeholder="Search By Search By Person / Company / Phone :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
														<a name="print" onClick="geninquiryPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
													</li>
													<?php
														}
														if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
														{ 
															?>
													<li>
														<a class="excel" name="excel" onClick="genReportttt()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
					<div class="portlet light">
					
						<!-- <div class="table-toolbar">
							<div class="row">
								<div class="col-md-12">
									<h4 class="text-right" style="font-weight: bold;">Total Count : <span id="totalcount">0</span></h4>
								</div>
							</div>
						</div> -->
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
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Sales Excecutive Information </div>
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
	$("#status_id").fSelect();
	$("#route").fSelect();
</script>

<script type="text/javascript">

var df1="";
var type="";
var searchName="";
var status_id="";
var city_id="";
var state_id="";
var route="";
var type_of_company="";
var isFillter=false;
var data_url = "inquiry_report_get_ajax.php";

$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","sales_executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})

function searchByName(){
	searchName = $("#searchName").val();
	state_id = $("#state").val();
	route = $("#route").val();
	city_id = $("#city").val();
	type = $("#type").val();
	status_id = $("#status_id").val();
	type_of_company=$("#type_of_company").val();
	isFillter=true;
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	status_id = "";
	searchName = "";
	type = "";
	df1 = "";
	city_id = "";
	route = "";
	type_of_company ="";
	isFillter=false;
	$("#material_request_filter_input").val("");
	$("#searchName").val("");
	$("#type_of_company").select2("val","");
	$("#type").select2("val","");
	$("#state").fSelect("destroy");
	$("#state").val("");
	$("#state").fSelect("create");

	$("#city").fSelect("destroy");
	$("#city").val("");
	$("#city").fSelect("create");

	$("#route").fSelect("destroy");
	$("#route").val("");
	$("#route").fSelect("create");

	$("#type").fSelect("destroy");
	$("#type").val("");
	$("#type").fSelect("create");

	$("#status_id").fSelect("destroy");
	$("#status_id").val("");
	$("#status_id").fSelect("create");
	displayRecords(100,1);
}

$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});


// $("#state").on('change', function() {
// 	var state = $("#state").val();
// 	filter_state(state,"");
// });

$("#state").on('change', function() {
	var state = $("#state").val();
	filter_class(state,"");
});

$("#city").on('change', function() {
	var city = $("#city").val();
	filter_city(city,"");
});


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

// function filter_state(state_id,city_id="")
// {
//    	$.ajax({
//         type: "POST",
//         url: "find_city.php",
//        	data:'state_id='+state_id+"&city="+city_id,
// 		beforeSend:function(){
// 			$('.preloader').fadeIn('slow');
// 		},
//         success: function(data){
//         	$("#city").select2("destroy");
//        		$("#city").fSelect("destroy");
//         	$("#city").html(data);
//        		$("#city").fSelect('create');
// 			$('.preloader').fadeOut('slow');
//         }
//     });
// }

// function filter_city(city_id){
// 	state_id = $("#state").val();
// 	city_id = $("#city").val();
// 	displayRecords(100,1);
// }	

function getStatus(s)
{
	status_id=s;
}

function getSalesExecutive(type){
	type=type;
}

function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(100,1);
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
	displayRecords(100,1);
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
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&status_id=" + status_id +"&state=" + state_id +"&city=" + city_id +"&route=" + route + "&df=" + df1 + "&type_of_company=" + type_of_company + "&isFillter="+isFillter,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&status_id=" + status_id +"&state=" + state_id +"&city=" + city_id +"&route=" + route + "&df=" + df1  + "&type_of_company=" + type_of_company + "&isFillter=" + true,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(100,1);
});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}

function genReport()
{
	$.ajax({
		type: "POST",
		url: "inquiry_genReport_ajax.php",
		data: '&status_id='+status_id+'&searchName='+searchName+'&type='+type +"&state=" + state_id +"&city=" + city_id +"&route=" + route+"&type_of_company=" + type_of_company,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result)
		{ 
			setTimeout(function()
			{
				$(".transCover").fadeOut(500);
				window.open(result,'_blank');// <- This is what makes it open in a new window.
			},1500);
		}
	});
}

function genReportexcel()
{
	var show = $("#numRecords").val();
	state = $("#state").val();
	city_id = $("#city").val();
	route = $("#route").val();
	status = $("#status_id").val();
	df1 = $("#material_request_filter_input").val();
	$.ajax({
		type: "POST",
		url: "inquiry_report_genreport_excel.php",
		data: '&status_id='+status_id+'&searchName='+searchName+'&type='+type +"&state=" + state +"&city_id=" + city_id +"&route=" + route +"&show=" + show +"&status=" + status +"&df1=" + df1,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result)
		{ 
			setTimeout(function()
			{
				$(".transCover").fadeOut(500);
				$("#loading").modal('hide');
				window.location.href=result;
			},1500);
		}
	});
}

function printPDF() 
{
	var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
}

function genReportttt()
{
	var searchName     = $("#searchName").val();
	var sales_executive = $("#sales_executive").val();
	var customer_id = $("#customer_id").val();
	var state = $("#state").val();
	var state = String($("#state").val());
	var type = String($("#type").val());
	var city_id = String($("#city").val());
	var route = String($("#route").val());
	var status_id = String($("#status_id").val());
	var type_of_company = String($("#type_of_company").val());
	var df1 = $("#material_request_filter_input").val();
    searchName     = encodeURIComponent(searchName.trim());
    	$.ajax({
	        method: "POST",
	        url: "inquiryyy_genReport_ajax.php",
	        data:{
        		searchName:searchName,
				sales_executive:sales_executive,
				customer_id:customer_id,
				state:state,
				type:type,
				city_id:city_id,
				route:route,
				status_id:status_id,
				type_of_company:type_of_company,
				df1:df1,
			},	
			dataType : 'json',
			beforeSend: function()
			{
				$('.preloader').fadeIn('slow');
			},
        	success: function(result){
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }

function editStatus(id){
	$("#inquiry_status"+id).removeAttr("disabled");
	$("#editStatus_"+id).hide(500);
	$("#editStatus2_"+id).show(400);
}

function cancelEditStatus(id){
	$("#editStatus2_"+id).hide(500);
	$("#editStatus_"+id).show(400);
	$("#inquiry_status"+id).attr("disabled","disabled");
}

function saveEditStatus(id)
{
	var newinquiry_status = $("#inquiry_status"+id).val();
	$.ajax({
		type: "POST",
		url: "ajax_update_status.php",
		data: "id=" + id + "&status=" + newinquiry_status+'&table=no_order_inquiry',
		cache: false,
		beforeSend: function() {
		},
		success: function(html) 
		{	
			var result=$.parseJSON(html);
			if(result.ack==1)
			{
				toastr.success(result.ack_msg);
				cancelEditStatus(id);
			}
			else
			{
				toastr.error(result.ack_msg);
			}
			if(html==1){
				
				toastr.success("Status Updated Successfully");
			}			
		}
	});
}
</script>

<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: 
	{
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
      	status_id = $("#status_id").val();
		type = $("#type").val();
		type_of_company = $("#type_of_company").val();
		df1 = $("#material_request_filter_input").val();
		var show = $("#numRecords").val();
		var myWindow = window.open('print_inquiry_report_ajax.php?searchName='+searchName + "&type=" + type + "&status_id=" + status_id +"&state=" + state_id +"&city=" + city_id  +"&route=" + route  +"&type_of_company=" + type_of_company + "&df=" + df1 + "&show=" + show ,'','width=700,height=800');
     	myWindow.print();
  	}
</script>
</body>
</html>