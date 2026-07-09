<?php
$page_id=409;$page_slug='page_category';
$ctable 	= "category";
$ctable1 	= "WEB APIS";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = $ctable1;
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
				<div class="col-md-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<!-- START FILTERS-->
				<!--END FILTERS-->
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
								<div class="row">
									<div class="portlet-body pull-right" style="margin-top:-40px;">
                                        <h4>Search API</h4>
                                        <form class="form-inline" action="#"  onSubmit="return searchByName();" role="form">
                                            <div class="form-group">
                                                <label class="sr-only" for="searchName">Search API</label>
                                                <input class="form-control" id="searchName" placeholder="Enter API Title" type="text"> </div>
                                            <button type="submit" onClick="searchByName()"  class="btn btn-primary">Search</button>
                                            <button type="button" onClick="clearSearchByName()" class="btn btn-danger">Clear</button>
                                        </form>
                                    </div>
									<div class="theme-options">
										<div class="theme-option" style="margin-left:20px;">
											<a class="btn btn-primary " href="javascript:;"  onClick="genReport(this,'1');" title="Save">Save</a>
											<button name="newapi" id="newapi" class="btn btn-primary" onClick="location.href='api_crud.php'" style="margin-left:10px;">Generate New API</button>
										</div>
									</div>
                                    <div class="portlet-title">

                                    <div class="actions">

                                        <div class="btn-group btn-group-devided" data-toggle="buttons">
											
                                        </div>
                                    </div>
                                    </div>

                                    <div class="portlet-body">
                                        <div class="table-toolbar">
											<div class="row">
                                                
                                            </div>
                                        </div>

                                        <div id="results" >

                                        </div>
                                    </div>
                                
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
<script type="text/javascript">
var searchName="";
var data_url = "api_document_design/design1.php";
function searchByName(){
	searchName = $("#searchName").val();
	displayItemClassRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayItemClassRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$('#datatable_category').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "20%" },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displayItemClassRecords(numRecords) {
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
	displayItemClassRecords(numRecords, 1);
}

$(document).ready(function() {
	displayItemClassRecords(100,1);
});

function genReport(btn,bid){
    var last_text=$(btn).html();

	var rc = encodeURIComponent($("#results").html());
	$.ajax({
		type: "POST",
		url: "ajax_genAPIReport.php",
		data: 'rc='+rc,
		beforeSend: function() {
		     $(btn).attr("disabled","disabled");
                $(btn).html("Saving..");
		},
		success: function(result){ //alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
                    $(btn).removeAttr("disabled");
                    $(btn).html(last_text);
                    window.location=  result;
				},1500);
			}
	});
}

</script>

<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='api_crud.php?mode=d&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_category').dataTable();
});
</script>
</body>
</html>