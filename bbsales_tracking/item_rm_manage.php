<?php
$page_id=430;$page_slug='page_item';
$ctable 	= "item_rm";
$ctable1 	= "Row Material Items";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?php $db->getMessageBlock(); ?>
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
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">

                                <div class="row">
								<div class="col-md-4 col-xs-4 col-sm-4" >
                                        <div class="form-group">
                                            <label>Search by Category</label>
                                            <select class="form-control category" name="category" id="category"  autofocus onChange="getcategory(this.value);">

                                                 <option value="">--- Select Category ---</option>
												 <?php
													$category_d=$db->rp_getData("item_rm_category","*","isDelete=0");
													while($category=mysqli_fetch_assoc($category_d)){
														?>
                                                 <option value="<?php echo $category['id'] ?>"><?php echo $category['item_rm_category_name'] ?></option>
														<?php
													}
													?>
                                            </select>

                                        </div>
                                    </div>
									<div class="col-md-4 col-xs-4 col-sm-4" >
                                        <div class="form-group">
                                            <label>Search by Packaging</label>
                                            <select class="form-control  pakaging" name="pakaging" id="pakaging"  autofocus onChange="getpakaging(this.value);">
											<option value="">--- Select Packaging ---</option>
											 <option value="0">Box</option>
											 <option value="1">Pounch</option>
											 <option value="2">Loose</option>
                                            </select>

                                        </div>
                                    </div>
                                         <div class="col-md-4 pull-right">
										<form action="#" onSubmit="return searchByName();">
									
										<label> &nbsp;</label>
											<div class="input-group">
												
												<input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Enter Item Name or Code"/>
												<span class="input-group-btn">
													<input class="btn btn-success" type="submit" value="search">
												</span>
												<span class="input-group-btn">
													<input class="btn btn-danger" type="button" value="clear" onClick="clearSearchByName();">
												</span>
											</div>

										</form>
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
										echo $db->getAddButton($ctable);
									?>	
								</div>
							</div>
						</div>
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
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript">
var searchName="";
var category="";
var pakaging="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
function searchByName(){
	searchName = $("#searchName").val();
	displayFGItemMasterRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	category="";
	pakaging="";
	$("#searchName").val("");
	$("#category").select2("val","");
	$("#pakaging").select2("val","");
	displayFGItemMasterRecords(100,1);
}
function getcategory(id){
	
	category = id;
	displayFGItemMasterRecords(100,1);
	return false;
}

function getpakaging(paid){
	
	pakaging = paid;
	displayFGItemMasterRecords(100,1);
	return false;
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$('#datatable_fg_item_master').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displayFGItemMasterRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName+ "&category=" + category+ "&pakaging=" + pakaging,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&category=" + category+ "&pakaging=" + pakaging,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&category=" + category+ "&pakaging=" + pakaging,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayFGItemMasterRecords(numRecords, 1);
}

$(document).ready(function() {
	displayFGItemMasterRecords(100,1);
});
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_fg_item_master').dataTable();
});
</script>
</body>
</html>