<?php 
$page_id=615;$page_slug='lr_details';
$ctable 	= "lr_detail";
$ctable1 	= "LR Details";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"lr_manage.php","title"=>$page_title));
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
                            <div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row">	

	                                   <div class="col-md-5 col-xs-5 col-sm-5">
                                             <?php
										echo $db->getAddButton("lr");
									?>

                                        </div>

                                <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                                    <div class="form-inline" role="form">
                                        <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                            <div class="form-group">

                                              <input type="text" style="width: 450px!important" placeholder="Search By Invoice No/LR Number:  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
                                                            <a name="print" onClick="PricelistPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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

                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light" >
						<div class="table-toolbar">
							<div class="row">
								<!-- <div class="col-md-10">
									<?php
										echo $db->getAddButton("pricelist_master");
									?>
								</div>	 -->
														
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
var status="";
var searchName="";
var data_url = "lr_get_ajax.php";

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
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
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
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

function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='lr_crud.php?mode=delete&id='+id;
	}
}
</script>
</body>
</html>