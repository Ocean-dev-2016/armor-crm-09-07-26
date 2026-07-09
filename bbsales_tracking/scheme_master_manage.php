<?php
$page_id=652;$page_slug='scheme_master';
$ctable 	= "scheme_master";
$ctable1 	= "Scheme Master";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
// $page_title = $ctable1;
$page_title = 'Scheme Master';
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>$page_title));
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
                            <div class="slimScrollDiv" style="position: relative; width: auto; height: auto;">
								
								<div class="row">
									<div class="col-md-5 col-xs-5 col-sm-5">
										<?php
											echo $db->getAddButton("scheme_master");
										?>
									</div>
                                 	<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
								  		<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
								   			<div class="form-group">
												
												<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search By Name:" />
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
														<li>
															<a name="print" onClick="genTopCategoryPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
														</li>
														<li>
															<a class="excel" name="excel" onClick="genTopCategoryExcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
														</li>
													</ul>
												</div>
	                                    	</div>
										</form>
								  	</div>
                                </div>
                                

                                </div>
                            </div>
                    
                    	<!-- END Portlet PORTLET-->
                		</div>
					<div class="portlet light">
					
						<!-- <div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
										
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript">
var status="";
var searchName="";
var data_url = "scheme_master_get_ajax.php";


function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [

			  { "sWidth": "3%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status,function(){
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
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

function genTopCategoryPrint() {

	var searchName     = $("#searchName").val();
  	searchName     	   = encodeURIComponent(searchName.trim());

	var myWindow = window.open('print_top_category_master_ajax.php?searchName='+searchName,'','width=700,height=800');
 	myWindow.print();
 // 	setTimeout(function () 
	// {
	// 	myWindow.print();
	// 	var ival = setInterval(function() 
	// 	{
	// 	    myWindow.close();
	// 	    clearInterval(ival);
	// 	}, 200);
	// }, 500);
}

function genTopCategoryExcel(){
	var searchName     = $("#searchName").val();
  	searchName     	   = searchName.trim();
  	$.ajax({
        method: "POST",
        url: "top_category_master_excel.php",
        data:{
    		searchName:searchName,
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
		window.location.href='scheme_master_crud.php?mode=delete&id='+id;
	}
}
</script>
</body>
</html>