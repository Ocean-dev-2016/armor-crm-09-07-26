<?php
$page_id=599;$page_slug='visit_report_page';
$ctable 	= "visit";
$ctable1 	= "Visit";
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
				<div class="col-xl-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-xl-12">
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
								<div class="row ">

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
                                    <?php
									if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
			                    	{ 
										?>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Sales Person</label><br/>
									  		<div class="form-group" role="form">
												<select class="form-control status" multiple="multiple" name="sales_executive" id="sales_executive">
							                		<option value="">Sales Person</option>
							                		<?php
							                			$D_r = $db->rp_getData("sales_executive","*","1=1 AND isDelete=0 AND isActive=1 GROUP By name","",0);
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
											<select class="form-control status" multiple="multiple" name="customer_id" id="customer_id">
						                		<option value="">Customer Name</option>
						                		<?php
						                			$E_r = $db->rp_getData("executive","*","1=1 GROUP By cname","",0);
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
                                     <div class="col-md-5 col-xs-5 col-sm-5" style="margin-top:10px">
                                     	<label>Search By Phone No :</label>
		                                <div class="form-inline" role="form">
		                                    <form class="form-inline" role="form" onSubmit="return searchByName();">
		                                       	<div class="form-group">

		                                          <input type="text" style="width: 250px!important" placeholder="Search By Phone No :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
																<a name="print" onClick="genVisitPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
									<!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<div class="form-group status" role="form" >
											<label>Search by State: </label>
                                        	<select class="form-control " name="class_id" id="class_id" onChange="getArea(this.value);" autofocus >
                                        		<option value="">--- Select State---</option>
													<?php
													$id_r = $db->rp_getData("class","*",0);
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
                                    </div> -->
								</div>
                            </div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
				<div class="col-xl-12">
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
							<div class="">
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
var isFillter=false;
var data_url = "visit_report_get_ajax.php";
// var data_url = "index_demo.php";

function getArea(cid)
{
	class_id= cid;
	displayRecords(100,1);
	
        $.ajax({
        type: "POST",
        url: "find_area_filter.php",
        data:'class_id='+class_id,
        success: function(data){
		$("#area").html(data);
		displayRecords(100,1);
        }
    });
}

function getareaName(aid){
	class_id=$('#class_id').val();
	area=aid;
	displayRecords(100,1);
	
}

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	isFillter=true;
	displayRecords(100,1);
	return false;
}
$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})

function clearSearchByName(){
	searchName = "";
	sales_executive = "";
	customer_id = "";
	df1 = "";
	isFillter=false;
	$("#searchName").val("");
	// $("#customer_id").val("");
	// $("#sales_executive").val("");

	$("#sales_executive").fSelect("destroy");
	$("#sales_executive").val("");
	$("#sales_executive").fSelect("create");

	$("#customer_id").fSelect("destroy");
	$("#customer_id").val("");
	$("#customer_id").fSelect("create");

	$("#material_request_filter_input").val("");
	displayRecords(100,1);
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
			  // { "sWidth": "10%" },
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1 + "&class_id=" + class_id + "&area=" + area + "&isFillter="+isFillter,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1 + "&class_id=" + class_id + "&area=" + area + "&isFillter=" + true,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(100,1);
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
		var sales_executive = String($("#sales_executive").val());
		var customer_id = String($("#customer_id").val());
		var df1 = $("#material_request_filter_input").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	// window.location.href='visit_genReport_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&customer_id='+customer_id+'&df='+df1;
      	$.ajax({
	        method: "POST",
	        url: "visit_genReport_ajax.php",
	        data:{
        		searchName:searchName,
				sales_executive:sales_executive,
				customer_id:customer_id,
				df1:df1,
			},	
			dataType : 'json',
			beforeSend: function()
			{
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
        	success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }
</script>

<script type="text/javascript">
	function genVisitPrint(){
		var searchName     = $("#searchName").val();
      	var sales_executive = $("#sales_executive").val();
      	df1=$("#material_request_filter_input").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_visit_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&df='+df1+'&customer_id='+customer_id,'','width=700,height=800');
     	myWindow.print();
    }
</script>


<script>
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