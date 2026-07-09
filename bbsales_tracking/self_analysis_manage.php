<?php
$page_id=659;$page_slug='self_analysis';
$ctable 	= "self_analysis";
$ctable1 	= "Self Analysis";
$main_page 	= $ctable;
$page 		= "self_analysis_manage";
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"self_analysis_manage.php","title"=>$page_title ));
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
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
								<div class="row filter_list">
									<!-- <div class="col-md-4 col-xs-4 col-sm-4">
								  		<label>Search By To Date AND From Date: &nbsp;</label>
								  		<div class="input-group input-medium pull-left">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div> 
                                     </div> -->
                                    <div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px;">
									  	<form class="form-inline" role="form" onSubmit="return searchByName();">	
									  		<div class="form-group">
												<label>Search By Sales Person Name: &nbsp;</label>
												<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search Here" />
											</div>
											<div class="form-group">
												<input class="btn red-haze btn-sm" type="submit" value="search">
												<input class="btn green-haze btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
										</form>
									</div>
								  	<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">

								  		<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>

								  		<button type="button" class="btn btn-sm print pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genleaverequestPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
								  		<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
										<button type="button" class="btn green-haze btn-sm excel " name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<?php
										}
										?>
										
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

								<div class="col-md-6">
									
									<!-- 	echo $db->getAddButton("Self Analysis"); -->
									
									
								<div class="btn-group">

								<a id="add_questions" href="self_analysis_crud.php?mode=add" class="btn sbold blue-ebonyclay"> Add New

									<i class="fa fa-plus"></i>

								</a>

							</div>	
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
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Customer Information
					</div>
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
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">

var searchName="";
var leave_type = "";
var df=""

var data_url = "self_analysis_get_ajax.php";
var todate="<?=$_REQUEST['todate'] ?>";
var fromdate="<?=$_REQUEST['fromdate'] ?>";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
// function searchBystatus(type){
// 	    status=type;
//         displayRecords(500,1);
// }
function searchByName(){
	searchName = $("#searchName").val();
	var sales_executive = $("#sales_executive").val();
	//alert(sales_executive);
	//alert(sales_executive);
	//status = $("#status").val();
	displayRecords(500,1);
	return false;
}

// $(".filterBtn").on("click",function()
// {
// 	sales_executive = $("#sales_executive").val();
// 	status = $("#status").val();
// 	customer_id = $("#customer_id").val();
// 	df=$("#material_request_filter_input").val();
// 	df = encodeURI(df)
// 	displayRecords(500,1);
// })

function clearSearchByName(){
	status = "";
	sales_executive = "";
	searchName="";
	
	//$("#sales_executive").destroy();
	$("#sales_executive").val("");
	$("#searchName").val("");

	//$("#sales_executive").select2();

	// $("#status").val("");
	// $("#searchName").val("");
	// $("#leave_type").val("");
	// $("df").val("");
	// $("#material_request_filter_input").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	
	$('#example1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "20%" },
			  { "sWidth": "10%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%","bSortable": false }
			],
			// "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
			 // "order": [[1,'asc']], /* default order is index 1 */
             //        'columnDefs': [ {
             //            'targets': [0], /* column index */
             //            'orderable': false, /* true or false */
             //        }],

             //        "oLanguage": {
             //            "sEmptyTable": "Sorry No Data Available!!"
             //        }
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
var sales_executive = $("#sales_executive").val();
	$("#results" ).html("");

	$("#results" ).load( data_url+"?show=" + numRecords +"&searchName="+searchName+"&sales_executive="+sales_executive,function(){

		loadDataTable();

	}); //load initial records

	

	//executes code below when user click on pagination links

	$("#results").on( "click", ".paging_simple_numbers a", function (e){

		e.preventDefault();

		var numRecords  = $("#numRecords").val();

		$(".loading-div").show(); //show loading element

		var page = $(this).attr("data-page"); //get page number from link

		$("#results").load(data_url+"?show=" + numRecords+"&sales_executive="+sales_executive ,{"page":page}, function(){ //get content from PHP page

			$(".loading-div").hide(); //once done, hide loading element

			loadDataTable();

		});

		

	});

	$("#results").on( "change", "#numRecords", function (e){

		e.preventDefault();

		var numRecords  = $("#numRecords").val();

		$(".loading-div").show(); //show loading element

		var page = $(this).attr("data-page"); //get page number from link

		$("#results").load(data_url+"?show=" + numRecords +"&sales_executive="+sales_executive,{"page":page}, function(){ //get content from PHP page

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

// $(document).ready(function() {       

   // $('#datatable_1').dataTable();

// });


/*dispay order function*/
function CheckDispalyOrder(id)
{
	var display_order = $("#disp"+id).val();
	var size_id = $("#disp"+id).data("size-id");

	$.ajax({
		type: "POST",
		url: "check_display_order_ajax.php",
		data: 'display_order='+display_order+"&id="+size_id+"&table=news",
		success: function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				toastr.success("Update Successfully!!","Success");
			}
			else
			{
				toastr.error("Value Already Available","Error");
				var display_order = $("#disp"+id).val(0);
			}
		}
	});
}

	function genleaverequestPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	//status = $("#status").val();
      	var sales_executive = $("#sales_executive").val();
		
     	var myWindow = window.open('print_self_analysis_ajax.php?searchName='+searchName + "&sales_executive=" + sales_executive ,'','width=700,height=800');
     	myWindow.print();   
    }
    // 	function genReport()
    // 	{
	// 	var rc = encodeURIComponent($("#print_info1").html());
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "self_analysis_genReport_ajax.php",
	// 		data: '&rc='+rc,
	// 		beforeSend: function() {
	// 			$(".transCover").fadeIn(800);
	// 			$("#loading").modal('show');
	// 		},
	// 		success: function(result){ //alert(result);
	// 				setTimeout(function(){
	// 					$(".transCover").fadeOut(100);
	// 					$("#loading").modal('hide');
	// 					window.location.href=result;
						
	// 				},1500);
	// 			}
	// 	});
	// } 

    function genReport()
	{
	var searchName     = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	//status = $("#status").val();
  	sales_executive = $("#sales_executive").val();

      	$.ajax({
			method: "POST",
			url: "self_analysis_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		sales_executive:sales_executive,
        		
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
</html>