<?php
$page_id=601;$page_slug='complain_report_page';
$ctable 	= "complain";
$ctable1 	= "Complain";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = $ctable1." Report";

$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>"manage_".$ctable.".php","title"=>$page_title));
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
								<div class="row filter_list">
									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<label>Filter By Date:</label>
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
							  <select class="form-control status"  name="company_type" id="company_type">
									<option value=""> Select Company</option>
									<?php
                                    	$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
                                    	if(mysqli_num_rows($company_r)>0)
                                    	{
                                    		while($company_d = mysqli_fetch_array($company_r))
                                    		{
                                    		?>
                                    			<option value="<?php echo $company_d['id']; ?>" <?=($company_type == $company_d['id'])?"selected":"";?>><?php echo $company_d['name']; ?></option>
                                    		<?php
                                    		}
                                    	}
                                    ?>
                                                        
		                	</select>
				        </div>
                    </div>

                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
						<div class="form-group">
							<label>Search by Customer Type</label><br/>
							  <select class="form-control status"  name="customer_type" id="customer_type" onchange="getcustomer(this.value)">
									<option value=""> Select Customer Type </option>
									<option value="0">ALL</option>
			                		<?php   
									$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
									$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='". $check_id."'",0);
									?> 
									<?php
									if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) 
									{ 
									if($get_customer_type==1)
									{ 
									?>
									<option value="1">Super Stockist</option> 
									<?php 
									}  
									?>
									<option value="2">Distributor</option>  
									<?php 
									if($get_customer_type!=1)
									{ 
									?>
									<option value="3">Retailer</option>    
									<?php 
									}
									}
									else
									{ 
									$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
									if ($cust_R) {
										while ($C = mysqli_fetch_assoc($cust_R)) {
									?>
									<option value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
									<?php
										}
									}
									}
									?>
		                	</select>
				        </div>
                    </div>
					<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
						<div class="form-group">
							<label>Search by Customer</label><br/>
							  <select class="form-control status"  name="customer_id" id="customer_id" >
								<option value="">Select Customer</option>
							</select>
						</div>
					</div>
                                    <?php
									if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
			                    	{ 
									?>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Sales Person</label><br/>
									  		<div class="form-group" role="form">
												<select class="form-control" multiple="multiple" name="sales_executive" id="sales_executive">
							                		<option value="">Sales Person</option>
							                		<?php
							                			$D_r = $db->rp_getData("sales_executive","id,name","isDelete=0 AND isActive=1","",0);
							                			while ($D = mysqli_fetch_assoc($D_r))
							                			{
							                				?>
							                				<option value="<?=$D['id']?>" <?=($sid == $D['id'])?"selected":"";?>><?=$D['name']?></option>
							                				<?php
							                			}
							                		?>
							                	</select>
									        </div>
	                                    </div>
                                    <?php
                                	}
                                	?>
                                    <!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
                                    	<label>Search By Customer Name</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" multiple="multiple" name="customer_id" id="customer_id">
						                		<option value="">Customer Name</option>
						                		<?php
						                			$E_r = $db->rp_getData("executive","id,cname","","",0);
						                			while ($E = mysqli_fetch_assoc($E_r))
						                			{
						                				?>
						                				<option value="<?=$E['id']?>" <?=($cid == $E['id'])?"selected":"";?>><?=$E['cname']?></option>
						                				<?php
						                			}
						                		?>
						                	</select>
								        </div>
                                    </div> -->
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
                                    	<label>Search By Status</label><br/>
                                    	<select class="form-control" multiple="multiple" id="status_id" name="status_id">
					                    	<option value="">Select Status</option>
					                    	<option <?= ($status_id==0 && $status_id!="")?"selected":""; ?> value="0">Generate</option>        
					                    	<option <?= ($status_id==1)?"selected":""; ?> value="1">In Progress</option> 
					                    	<option <?= ($status_id==2)?"selected":""; ?> value="2">Complete</option>        
					                    	<option <?= ($status_id==-1)?"selected":""; ?> value="-1">Reject</option>        
					                    	<option <?= ($status_id==-2)?"selected":""; ?> value="-2">Not Done</option>
					                    </select>
                                    </div>
									<!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<div class="form-group" role="form">
											<label>Search by State: </label>
                                        	<select class="form-control" multiple="multiple" name="class_id" id="class_id">
                                        		<option value="">--- Select State---</option>
													<?php
													$cid_r = $db->rp_getData("class","*",0);
													if(mysqli_num_rows($cid_r)>0){
														while($cid_d = mysqli_fetch_array($cid_r)){
													?>
														<option value="<?php echo $cid_d['id']; ?>"><?php echo $cid_d['name']; ?></option>

													<?php
														}
													}
													?>
                                        	</select>
										</div>
                                    </div> -->
                                    <!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
										<label>Search by City</label><br/>
											<select class="form-control" multiple="multiple" name="area_id[]" id="area_id">
												<option value="">Select City</option>
											</select>
                                        </div>      
                                    </div> -->
                                    <!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
                                    <div class="form-group">
											<label>Search by Route</label>
												<select class="form-control" multiple="multiple" name="route" id="route">
													<option value="">Select Route</option>
	                                            </select>
	                                        </div>      
                                    	</div> -->
                                   
								<!-- <div class="row"> -->
									<div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top:10px">
									</div>
                                    <div class="col-md-6 col-xs-6 col-sm-6 pull-right" style="margin-top:10px">
										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
                                                <input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search By Complain No:" value="" />
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
															<a name="print" onClick="genComplainPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
								   	<!-- <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">

								   		<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>


								   		<button type="button" class="btn print btn-sm" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genComplainPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
								   		<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
										<button type="button" class="btn green-haze excel btn-sm" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<?php
										}
										?>
										
									</div> -->
                                    
                                <!-- </div> -->
								</div>
							</div>
                                                             
                                </div>
                                <!-- <div class="row"> -->
                                    <!-- <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										<button type="button" class="btn green-haze excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn print" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genComplainPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div> -->
                                	<!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<div class="form-group" role="form">
											<label>Search by State: </label>
                                        	<select class="form-control input-small" name="class_id" id="class_id" onChange="filter_class(this.value);" autofocus >
                                        		<option value="">--- Select State---</option>
													<?php
													$cid_r = $db->rp_getData("class","*",0);
													if(mysqli_num_rows($cid_r)>0){
														while($cid_d = mysqli_fetch_array($cid_r)){
													?>
													<option value="<?php echo $cid_d['id']; ?>"><?php echo $cid_d['name']; ?></option>

													<?php
														}
													}
													?>
                                        	</select>
										</div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
										<label>Search by City: </label>
											<select class="form-control input-small" name="area_id" id="area_id"  onChange="filter_area(this.value);" autofocus>
												<option value="">--- Select City---</option>
												<?php
													$aid_r = $db->rp_getData("area","*",0);
													if(mysqli_num_rows($aid_r)>0){
														while($aid_d = mysqli_fetch_array($aid_r)){
													?>
													<option value="<?php echo $aid_d['id']; ?>"><?php echo $aid_d['name']; ?></option>

													<?php
														}
													}
													?>
											</select>
                                        </div>      
                                     </div> -->
                                <!-- </div> -->
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
	$("#class_id").fSelect();
	$("#area_id").fSelect();
	$("#sales_executive").fSelect();
	$("#customer_id").select2();
	$("#status_id").fSelect();
	$("#route").fSelect();
</script>

<script type="text/javascript">
	
var searchName="";
var state="";
var city="";
var df1="";
var sales_executive="";
var customer_id="";
var status_id="";
var area_id="";
var class_id="";
var route="";
var customer_type="";
var company_type="";
var isFillter=false;
var data_url = "complain_report_get_ajax.php";
// var data_url = "index_demo.php";

function searchByName(){
	searchName = $("#searchName").val();
	area_id = $("#area_id").val();
	
	class_id = $("#class_id").val();
	
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	status_id = $("#status_id").val();
	df1=$("#material_request_filter_input").val();
	company_type=$("#company_type").val();
	customer_type=$("#customer_type").val();
	isFillter=true;
	df1 = encodeURI(df1)
	displayRecords(500,1);
	// if(class_id!="" && area_id!="")
	// {
	// 	filter_class(class_id,area_id);
	// }
	return false;
}
$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(500,1);
})

/*$("#status_id").on("change",function()
{
	var status_id=$(this).val();
	alert(status_id);
});*/


$("#class_id").on('change', function() {
	var class_id = $("#class_id").val();
	filter_class(class_id,"");
});
$("#area_id").on('change', function() {

	var area_id = $("#area_id").val();
	filter_city(area_id,"");
});

function filter_class(class_id,area_id=""){
    $.ajax({
        type: "POST",
        url: "find_area_filter.php",
        data:'class_id='+class_id+"&area_id="+area_id,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
        success: function(data){
            $("#area_id").select2("destroy");
       		$("#area_id").fSelect("destroy");
        	$("#area_id").html(data);
       		$("#area_id").fSelect('create');
            // $("#loading-modal").modal('hide');
            $('.preloader').fadeOut('slow');
        }
    });
}

function filter_city(area_id,route=""){
	//alert(area_id);
    $.ajax({
        type: "POST",
        url: "find_route_filter.php",
        data:'area='+area_id+"&route="+route,
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
// function filter_area(aid)
// {
// 	area_id= aid;
// 	displayRecords(500,1);
// 	$.ajax({
//         type: "POST",
//         url: "find_area_filter.php",
//         data:'area_id='+area_id,
//         success: function(data){
// 			$("#area").html(data);
// 			displayRecords(500,1);
// 		}
//     });
// }

// function getareaName(aid){
// 	class_id = $('#class_id').val();
// 	area_id = $("#area").val();
// }


function getStatus(s)
{
	status_id=s;
}
function clearSearchByName(){
	searchName = "";
	class_id = "";
	area_id = "";
	route="";
	sales_executive = "";
	status_id = "";
	customer_id = "";
	customer_type = "";
	company_type="";
	df1 = "";
	isFillter=false;
	$("#searchName").val("");

	$("#class_id").fSelect("destroy");
	$("#class_id").val("");
	$("#class_id").fSelect("create");

	$("#area_id").fSelect("destroy");
	$("#area_id").val("");
	$("#area_id").fSelect("create");

	$("#sales_executive").fSelect("destroy");
	$("#sales_executive").val("");
	$("#sales_executive").fSelect("create");

	$("#status_id").fSelect("destroy");
	$("#status_id").val("");
	$("#status_id").fSelect("create");

	$("#route").fSelect("destroy");
	$("#route").val("");
	$("#route").fSelect("create");

	$("#customer_type").select2("val","");
	$("#customer_id").select2("val","");
	$("#company_type").select2("val","");



	// $("#class_id").select2("val","");
	// $("#area_id").select2("val","");
	// $("#sales_executive").val("");
	// $("#status_id").val("");
	// $("#customer_id").val("");
	$("#material_request_filter_input").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$.fn.dataTable.ext.errMode = 'none';
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
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id + "&class_id=" + class_id + "&area_id=" + area_id +"&route="+route +"&company_type="+company_type +"&customer_type="+customer_type + "&isFillter="+isFillter,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id + "&class_id=" + class_id + "&area_id=" + area_id+"&route="+route +"&company_type=" +company_type  +"&customer_type="+customer_type,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id + "&class_id=" + class_id + "&area_id=" + area_id,{"page":page}, function(){ //get content from PHP page
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
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id + "&class_id=" + class_id + "&area_id=" + area_id,{"page":page}, function(){ //get content from PHP page
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
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id + "&class_id=" + class_id + "&area_id=" + area_id ,{"page":page}, function(){ //get content from PHP page
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
 $(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD')+" to "+picker.endDate.format('YYYY-MM-DD'));
});
</script>

<script type="text/javascript">
	function genReport(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	// var sales_executive = $("#sales_executive").val();
      	var sales_executive = String($("#sales_executive").val());
      	var class_id = String($("#class_id").val());
      	var area_id = String($("#area_id").val());
      	var route = String($("#route").val());
      	// var customer_id = string($("#customer_id").val());
      	var customer_id = String($("#customer_id").val());
      	var df1=$("#material_request_filter_input").val();
      	// var class_id = $('#class_id').val();
		// var area_id = $("#area_id").val();
		// var status_id = $("#status_id").val();
		var status_id = String($("#status_id").val());
		var company_type =  String($("#company_type").val());
		// alert(status_id);
		// alert(status_id);
		/*window.location.href='complain_genReport_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&customer_id='+customer_id+'&df='+df1+"&class_id="+class_id+"&area_id="+area_id;*/
		$.ajax({
	        method: "POST",
	        url: "complain_genReport_ajax.php",
	        data:{
        		searchName:searchName,
				sales_executive:sales_executive,
				customer_id:customer_id,
				df1:df1,
				class_id:class_id,
				area_id:area_id,
				route:route,
				status_id:status_id,
				company_type:company_type
			},	
			dataType : 'json',
			beforeSend: function()
			{
				
			},
        	success: function(result){
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }
</script>

<script type="text/javascript">
	function genComplainPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	// var sales_executive = $("#sales_executive").val();
      	var sales_executive = String($("#sales_executive").val());
      	var customer_id = String($("#customer_id").val());
      	var status_id = String($("#status_id").val());
      	
      	var df1=$("#material_request_filter_input").val();
      	var class_id = $('#class_id').val();
		var area_id = $("#area_id").val();
		var route=$("#route").val();
		var company_type=$("#company_type").val();

     	var myWindow = window.open('print_complain_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&df='+df1+'&customer_id='+customer_id+"&class_id="+class_id+"&area_id="+area_id+"&status_id="+status_id+"&route="+route+"&company_type="+company_type,'','width=700,height=800');
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


<script>

	function editStatus(id){
		$("#complain_status"+id).removeAttr("disabled");
		$("#editStatus_"+id).hide(500);
		$("#editStatus2_"+id).show(400);
}
	function cancelEditStatus(id){
		$("#editStatus2_"+id).hide(500);
		$("#editStatus_"+id).show(400);
		$("#complain_status"+id).attr("disabled","disabled");
}

function saveEditStatus(id){
var newcomplain_status = $("#complain_status"+id).val();
// alert(newcomplain_status);
	$.ajax({
		type: "POST",
		url: "ajax_update_status.php",
		data: "id=" + id + "&status=" + newcomplain_status+'&table=complain',
		cache: false,
		beforeSend: function() {
			
		},
		success: function(html) {		

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

$("#company_type").on("change",function () {
		// alert("hello");
		var customer_type = $("#customer_type").val();
		getcustomer(customer_type);
	});

	function getcustomer(ctype) 
	{
			var companytype = $("#company_type").val();
			$('#customer_id').select2("val", "");
			if (companytype == "" || companytype == undefined) {
				toastr.warning('Select Company To Show A Company And Customer Type Wise Customer Data');
				companytype = "";
			}
			if (ctype != "" && ctype != undefined) {
				$.ajax({
					type: "post",
					url: "ajax_get_customer.php",
					data: "customer_type=" + ctype + "&companytype=" + companytype,
					beforeSend: function() {
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
					success: function(result) {
						setTimeout(function() {
							$('#customer_id').html(result);
							// getBillNo(ctype)
							// $("#loading-modal").modal('hide');
							$('.preloader').fadeOut('slow');

						});
					}

				})
			}


	}

/*function PopUp(src){
	$("#myModal").css("display","block");
	$("#img01").attr("src",src);
};


//image slider

$('#myModal1').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Button that triggered the modal
  	var requesting_id=button.data("id");

  	var type=button.data("type");
	$("#requesting_ajax").load("image_info_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();   
});*/
</script>



</body>
</html>