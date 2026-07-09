<?php 
$page_id=585;$page_slug='meeting_master';
$ctable 	= "meeting";
$ctable1 	= "Meeting";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Customer Meeting";
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"meeting_manage.php","title"=>$page_title));
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
	                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
									<div class="row">  
										<div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px;">
										  	<form class="form-inline" role="form" onSubmit="return searchByName();">
										  		<div class="form-group">
													<label>Search By Customer Name: &nbsp;</label>
													<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search Here" />
												</div>
												<div class="form-group">
													<input class="btn red-haze btn-sm" type="submit" value="search">
													<input class="btn green-haze btn-sm" type="button" value="clear" onClick="clearSearchByName();">
												</div>
											</form>
										</div>                             
	                                    <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px;">

	                                    	<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>

	                                    	<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genmeetingPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>

	                                    	<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
											<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
											<?php
										}
										?>
											
										</div>				
		                            </div>
	                             </div>
	                        </div>
	                    </div>
	                </div>
                    <!-- END Portlet PORTLET-->
                
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-10">
									<?php
										echo $db->getAddButton("meeting");
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
var status="";
var searchName="";
var type = "";
var data_url = "meeting_get_ajax.php";

function searchByName(){
	searchName = $("#searchName").val();
	type = $("#type").val();
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	type = "";
	$("#searchName").val("");
	$("#type").select2("val","");
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
			  { "sWidth": "10%" }, 
			  { "sWidth": "30%" },
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
	var type        = $("#type").val();
	searchName 	= encodeURIComponent(searchName.trim());
	//type 	= encodeURIComponent(type.trim());

	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		var type        = $("#type").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type,{"page":page}, function(){ //get content from PHP page
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
function printPDF() 
{
	var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
}

function printData(id)
{
	var myWindow = window.open('pricelist_print.php?id='+id+"&p=1",'','width=1000,height=800');
	myWindow.print();
}

function PricelistPrint(id)
{
	var search =$("#searchName").val();
	var myWindow = window.open('pricelistprint.php?search='+search+"&p=1",'','width=1000,height=800');
	myWindow.print();
}
</script>
<script type="text/javascript">
	function genmeetingPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	var type = $("#type").val();
     	var myWindow = window.open('print_customer_meeting_ajax.php?searchName='+searchName+ "&type=" + type,'','width=700,height=800');
     	// myWindow.print();
     	setTimeout(function () 
		{
			myWindow.print();
			var ival = setInterval(function() 
			{
			    myWindow.close();
			    clearInterval(ival);
			}, 200);
		}, 500);
    }
</script>

<script type="text/javascript">
	function genReport()
	{
		var searchName = $("#searchName").val();
      	searchName     = searchName.trim();
      	// searchName     = encodeURIComponent(searchName.trim());
      	var type       = $("#type").val();
      	$.ajax({
			method: "POST",
			url: "meeting_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		type:type,
			},
			dataType : 'json',
			beforeSend: function() {
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
			success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
	        },
		});
    }
</script>

</body>
</html>