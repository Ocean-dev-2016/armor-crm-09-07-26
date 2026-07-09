<?php 
$page_id=580;$page_slug='price_list_master';
$ctable 	= "push_notification";
$ctable1 	= "Import Stock";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"import_stock_manage.php","title"=>"Manage ".$ctable1));
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

	                                <div class="col-md-12 col-xs-6 col-sm-6 " style="margin-top:10px">

										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
										   <div class="form-group">
												<label>Search By Name: &nbsp;</label>
												<input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Enter Name" />
											</div>
											 <div class="form-group">
												<input class="btn btn-danger btn-sm" type="submit" value="search">
											</div>
											 <div class="form-group">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
										</form>
									  </div>
                                </div>
                                

                            </div>
                            </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-12">
									<?php
										echo $db->getAddButton("import_stock");
									?>
									<!--<button type="submit" name="submit" value="print"  onClick="PricelistPrint()" class="btn yellow pull-right"><i class="fa fa-print"></i> Print</button>	-->
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
var data_url = "push_notification_get_ajax.php";

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
	function getTypeData(type)
	{
		if(type==1 || type==3)
		{
			$("#typeData").addClass("hidden");
			$("#typeTitle").html("");
			$(".sendpn").removeClass("hidden");
			if(type==1)
			{
				$("#send_msg_text").val("Are you sure you want to Send this Notification to All Sales Officer?");
				$("#refresh_token_table").val("sales_executive");
			}
			else
			{
				$("#send_msg_text").val("Are you sure you want to Send this Notification to All Customer?");
				$("#refresh_token_table").val("executive");
			}
		}
		else if(type==2)
		{
			$("#typeData").removeClass("hidden");
			$("#typeTitle").html("Select Seller");
			$(".sendpn").addClass("hidden");
			$("#send_msg_text").val("Are you sure you want to Send this Notification to Selected Sales Officer?");
			$("#refresh_token_table").val("sales_executive");
		}
		else if(type==4)
		{
			$("#typeData").removeClass("hidden");
			$("#typeTitle").html("Select User");
			$(".sendpn").addClass("hidden");
			$("#send_msg_text").val("Are you sure you want to Send this Notification to Selected Customer?");
			$("#refresh_token_table").val("executive");
		}
		else
		{	
			$("#typeData").addClass("hidden");
			$("#typeTitle").html("");
			$(".sendpn").addClass("hidden");
			$("#send_msg_text").val("");
		}	

		if(type==2 || type==4) 
		{
			$.ajax({
	            url:"push_notification_get_type_data.php",
	            type:"POST",
	            data:{ 
	                type:type,                
	            }, 
	            success:function(result) 
	            {
	               $("#typeVal").html(result);
	            },            
	        })
		}
	}

	function checkValue(v)
	{
		var t=$("#type1").val();
		if(v!="")
		{
			$(".sendpn").removeClass("hidden");
			// $("#isVaidToSend").val("1");
		}
		else
		{

			$(".sendpn").addClass("hidden");
			// $("#isVaidToSend").val("0");
		}
	}
	
</script>

</body>
</html>