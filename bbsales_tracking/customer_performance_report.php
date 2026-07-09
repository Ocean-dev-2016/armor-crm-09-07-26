<?php
$page_id=602;$page_slug='customer_performance_report_page';
$ctable 	= "orders";
$ctable1 	= "Customer Wise";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1." Reports";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$FromDate="";
$ToDate="";
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
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
				<div class="col-md-12 ">
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
                                    <div class="col-md-4  col-xs-4  col-sm-4" style="margin-top:10px">
                                       <div class="form-inline" role="form">
                                          <div class="form-group">
                                             <label>Filter By Order Date : &nbsp;</label><br>
                                             <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
                                          </div>
                                          <div class="form-group">
                                             <label>&nbsp;&nbsp;</label><br>
                                             <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
                                          </div>
                                          <div class="form-group">
                                             <label>&nbsp;&nbsp;</label><br>
                                             <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
                                          </div>
                                       </div>
                                    </div>
                                    
                                    <div class="col-md-5 col-xs-5 col-sm-5 pull-right" style="margin-top:10px;">
                                       <div class="form-inline" role="form">
                                          <label>Search By Customer Name :</label>
                                          <form class="form-inline" role="form" onSubmit="return searchByName();">
                                             <div class="form-group">
                                                <input type="text" style="width:230px!important" placeholder="Search By Customer Name :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
                                                   <ul role="menu" class="dropdown-menu dropdown-menu-right pull-right" >
                                                      <!-- <li>
                                                         <a name="print" onClick="genReportPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                      </li> -->

                                                      <?php
																		
																		if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																		{ 
																			?>
                                                      <li>
                                                         <a class="excel" name="excel" onClick="genReport_excel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                      </li>
                                                      <?php
																		}
																		?>
                                                      <li>
                                                         <a class="excel" name="excel" onClick="printPDF() ()" id="excel" title="Download PDF"><i class="fa fa-file-pdf-o"></i>PDF</a>
                                                      </li>
                                                   </ul>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
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
                                    <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
                                       <div class="form-group">
                                          <label>Search by Customer Type</label>
                                          <select class="form-control input-large status" multiple="multiple" name="customer_type" id="customer_type" >
                                             <option value=""> Select Customer Type </option>
                                             <?php
                                                $customer_typeR = $db->rp_getData("customer_type","name,id","isDelete=0","",0);
                                                while ( $customer_typeD = mysqli_fetch_assoc($customer_typeR) ) {
                                                ?>
                                             <option value="<?=$customer_typeD['id']?>"><?=$customer_typeD['name']?></option> 
                                             <?php
                                                }
                                                ?>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
												  <div class="form-group">
												    <label>Select State</label>
												    <select name="state" id="state" class="form-control" onChange="return City(this.value);">
												      <option value="">--Select State--</option>
												      <?php
												        $state_r = $db->rp_getData("state", "*", "isDelete=0", "", 0);
												        if (mysqli_num_rows($state_r) > 0) {
												          while ($state_d = mysqli_fetch_array($state_r)) {
												            ?>
												            <option <?php if ($state_d['name'] == $state) { echo "selected"; } ?>
												              value="<?php echo $state_d['name']; ?>"><?php echo $state_d['name']; ?></option>
												            <?php
												          }
												        }
												      ?>
												    </select>
												  </div>
												</div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
												  <div class="form-group">
												    <label>Select City</label>
												    <select name="city" id="city" class="form-control" onChange="return Route(this.value);">
												      <option value="">--Select City--</option>
												      <?php
												      if ($mode == 'edit') {
												        $city_name = $db->rp_getValue("city", "name", "name='" . $city . "'", 0);
												        ?>
												        <option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
												        <?php
												      }
												      ?>
												    </select>
												  </div>
												</div>
												<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
												  	<div class="form-group">
												    <label>Search by Route:</label>
												    <select class="form-control" name="route" id="route" autofocus>
												      <option value="">Select Route</option>
												    </select>
												  	</div>
												</div>

                                 </div>
                                 <div class="row">
                                        

                                        
                                       
                                       <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px;">
                                          <div class="form-group">
                                             <label>Select Zone</label>
	                                             <select name="zone" id="zone" class="form-control">
																	<option value="">--Select Zone--</option>
																	<?php																
																	$zone_r = $db->rp_getData("zone", "*", "isDelete=0", "", 0);
																	if (mysqli_num_rows($zone_r) > 0) {
																		while ($zone_d = mysqli_fetch_array($zone_r)) { 
																	?>
																	<option <?php if ($zone_d['id'] == $zone) {
																						echo "selected";
																					}  ?> value="<?php echo $zone_d['id']; ?>"><?php echo $zone_d['name']; ?></option>
																<?php
																		}
																	} 
																?>
															</select>
                                          </div>
                                       </div>  
                                    </div>
                              </div>
                           </div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>
				<div class="col-sm-12">
					<div class="portlet light">
					
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Order Information </div>
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
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
	$("#customer_type").fSelect();
</script>
<script type="text/javascript">
var searchName="";
var sales_executive_id="";
var data_url = "customer_performance_report_get_ajax.php";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var FromDate="";
var status="";
var type="";
var city="";
var route="";
var state="";
var zone="";
var flag="1";
var isFillter=false;
$('#myModal').on('show.bs.modal', function (event) {
 var button = $(event.relatedTarget) // Button that triggered the modal
 var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
function searchByName(){
	searchName = $("#searchName").val();
	type = $("#customer_type").val();
	city = encodeURIComponent($("#city").val());
	state = encodeURIComponent($("#state").val());
	route = encodeURIComponent($("#route").val());
	isFillter=true;
	zone = $("#zone").val();
	displayRecords(50,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate = "";
	FromDate = "";
	status = "";
	type = "";
	city = "";
	route = "";
	zone = "";
	isFillter=false;
	$("#searchName").val("");
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#status").select2("val","");
	// $("#type").select2("val","");

	$("#customer_type").fSelect("destroy");
	$("#customer_type").val("");
	$("#customer_type").fSelect("create");

	$("#status").select2("val","");
	displayRecords(50,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getByDate() {
	if(FromDate<=ToDate)
	{
		
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(50,1);
	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}

}
function searchBySales(sid){
		
		sales_executive_id=sid;
		displayRecords(50,1);
}
function getType(tid){
		type=tid;
		displayRecords(50,1);
}
 
function loadDataTable(){
	$.fn.dataTable.ext.errMode = 'none';
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [/*
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" },*/
			  { "sWidth": "5%" },
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_executive_id=" + sales_executive_id + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city + "&route=" + route+ "&zone=" + zone + "&isFillter="+isFillter ,function(){
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&state=" + state + "&city=" + city+ "&route=" + route+ "&zone=" + zone,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&class_id=" + class_id + "&city=" + city+ "&route=" + route+ "&zone=" + zone,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(50,1);
});
function del_conf(id,quotation_id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id +'&flag=1';
	}
}

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}
function genReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "orders_info_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
}

function genReport_excel(){
               		var searchName 	= $("#searchName").val();
               		searchName 	= encodeURIComponent(searchName.trim());
               		var type = String($("#customer_type").val());
	               	var state = $("#state").val();
	               	var city = $("#city").val();
	               	var route = $("#route").val();
	               	var zone = $("#zone").val();
	               	var ToDate = $("#ToDate").val();
	               	var FromDate = $("#FromDate").val();
               		// alert(month);
               		/*window.location.href='complain_genReport_ajax.php?searchName='+searchName+'&customer_type='+customer_type+'&customer_id='+customer_id+'&df='+df1+"&state="+state+"&city="+city;*/
               		$.ajax({
               	        method: "POST",
               	        url: "customer_performance_genreport_excel_1.php",
               	        data:{
                       		searchName:searchName,
               				type:type,
               				ToDate:ToDate,
               				FromDate:FromDate,
               				route:route,
               				state:state,
               				city:city,
               				zone:zone,
               			},	
               			dataType : 'json',
               			beforeSend: function()
               			{
               				
               			},
                       	success: function(result){
                       		// alert(result);
                       		window.location.href="<?=SITEURL?>"+result.file_path;
                       	},
               			/*error:function(result){
               				window.location.href="<?=SITEURL?>"+result.file_path;
               			}*/
                   	});
                   } 
function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}
</script>

<script type="text/javascript">
   function State(val) {
   	$.ajax({
   		type: "POST",
   		url: "ajax_get_state.php",
   		data: 'cid=' + val,		
   		success: function(result) {
   			$("#state").html(result);
   		}
   	});
   }
   
   function City(val) {
   	$.ajax({
   		type: "POST",
   		url: "ajax_get_main_city.php",
   		data: 'sid=' + val,
   		success: function(result) {
   			$("#city").html(result);
   		}
   	});
   }
   function Route(val) { 
   	$.ajax({
   		type: "POST",
   		url: "ajax_get_city.php",
   		data: 'sid=' + val,
   		success: function(result) {
   			$("#route").html(result);
   		}
   	});
   }
</script>
</body>
</html>