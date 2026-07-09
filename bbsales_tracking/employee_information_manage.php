<?php
$page_id=658;$page_slug='sales_executive_info_form';
$ctable 	= "sales_executive_information";
$ctable1 	= "Sales Executive Information";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"employee_information_manage.php","title"=>"Manage Employee Information"));
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
				<div class="col-xl-12">
					<div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row">
                                    <div class="col-md-5 col-xs-5 col-sm-5">
									 	<?php
											echo $db->getAddButton("employee_information");
										?>
									</div>
			                        <div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px;">
									  	<form class="form-inline" role="form" onSubmit="return searchByName();">	
									  		<div class="form-group">
												<label>Search By First Name, Middle Name & Surname: &nbsp;</label>
												<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search Here" />
											</div>
											<div class="form-group">
												<input class="btn red-haze btn-sm" type="submit" value="search">
												<input class="btn green-haze btn-sm" type="button" value="clear" onClick="clearSearchByName();">
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
															<a name="print" onClick="genStatePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
														</li>
														<li>
															<a class="excel" name="excel" onClick="genstateExcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
														</li>
													</ul>
												</div>
		                                   	</div>
										</form>
									</div>	

									<!--   	<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										<button type="button" class="btn green-haze btn-sm excel " name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn btn-sm print pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genleaverequestPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div> -->
								</div>
							</div>

						</div>
             		</div>
                </div>
            </div>
			<?php $db->getMessageBlock(); ?>
		</div>
		<div class="portlet light">
			<div class="table-toolbar">
				<div class="row">
						<!-- <div class="col-md-6 pull-right">
							<form action="#" class="form-inline pull-right" onSubmit="return searchByName();">
								<div class="form-group">
									<label>Search By Name : &nbsp;</label>
									<input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Search Here"/>
									<span class="form-group-btn">
										<input class="btn btn-success" type="submit" value="search">
									</span>
									<span class="form-group-btn">
										<input class="btn btn-danger" type="button" value="clear" onClick="clearSearchByName();">
									</span>
								</div>
							</form>
						</div> -->
				</div>
			</div>
			<div class="col-xl-12">
				<div class="portlet-body">
					<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
					<div id="results"></div>
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
var data_url = "employee_information_get_ajax.php";
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
	searchName = "";
	$("#searchName").val("");
	$("#material_request_filter_input").val("");
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
			  { "sWidth": "30%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
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
	var leave_type = $("#leave_type").val();
	//var status = $("#status").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		//var status = $("#status").val();
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	var status = $("#status").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df + "&status=" + status + "&leave_type=" + leave_type,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });
	
	// $("#results").on( "change", "#leave_type", function (e){
	// 	e.preventDefault();
	// 	var status = $("#status").val();
	// 	var numRecords  = $("#numRecords").val();
	// 	var leave_type = $("#leave_type").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&leave_type=" + leave_type + "&df=" + df + "&status=" + status,{"page":page}, function(){ //get content from PHP page
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
		window.location.href='employee_information_crud.php?mode=delete&id='+id;
	}
}
function genReport()
{
	var searchName     = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	df = $("#material_request_filter_input").val();

      	$.ajax({
			method: "POST",
			url: "employee_information_genReport_ajax.php",
			data:{
        		searchName:searchName,
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

function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}

/*dispay order function*/
function CheckDispalyOrder(id)
{
	var display_order = $("#disp"+id).val();
	var size_id = $("#disp"+id).data("size-id");

	$.ajax({
		type: "POST",
		url: "check_display_order_ajax.php",
		data: 'display_order='+display_order+"&id="+size_id+"&table=weight",
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
/*dispay order function*/

</script>

<!-- <script type="text/javascript">
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
</script> -->
<script type="text/javascript">
	function genleaverequestPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_employee_information_ajax.php?searchName='+searchName,'','width=700,height=800');
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
</body>
</html>
</html>