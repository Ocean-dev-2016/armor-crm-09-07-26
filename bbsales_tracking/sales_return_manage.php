<?php
$page_id=639;$page_slug='sales_return_page';
$ctable 	= "sales_return";
$ctable1 	= "Sales Return";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Sales Return";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"sales_return_manage.php","title"=>$page_title));
include("connect.php");
$FromDate='';
$ToDate='';
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
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
									<?php
									if($rights['insert_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
									{
										?>
									<div class="col-md-5 col-xs-5 col-sm-5">
                                        <a class="btn sbold blue-ebonyclay" href="sales_return_crud.php?mode=add"> Add New<i class="fa fa-plus"></i></a>
                                    </div>
                                    <?php
                                    }
                                    ?>

	                                <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
	                                    <div class="form-inline" role="form">
	                                        <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
	                                            <div class="form-group">
	                                            	<input type="text" style="width: 450px!important" placeholder="Search By   Person Name/Credit Note No:  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{
																?>
	                                                        	<li>
	                                                            	<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
	                                                        	</li>
	                                                        	<li>
	                                                            	<a class="excel" name="export_for_tally" onClick="genReportTally()" id="export_for_tally" title="Download XL Tally Report"><i class="fa fa-file-excel-o"></i>Export For Tally</a>
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
					</div>
					<!-- END Portlet PORTLET-->
				</div>
				<div class="portlet-body">
					
						<div class="tab-content">
							<div class="tab-pane active" id="my_order_info">	
								<div class="row">
									<div class="col-sm-12">
										<div class="portlet light">
											<!-- <div class="col-md-6">
												<?php echo $db->getAddButton($ctable); ?>	
											</div> -->
											<div class="portlet-body">
												<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												<div id="results"></div>
											</div>
										</div>
									</div>											
								</div>
							</div>
							<div class="tab-pane" id="dealer_order_info">	
								<div class="row">
									<div class="col-sm-12">
										<div class="portlet light">
											<div class="col-md-6">
											</div>
											<div class="portlet-body">
												<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												<div id="results_outlets"></div>
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
</div>
<!-- PaymentApproveModel -->
<div class="modal" id="myLRModal" role="dialog" aria-labelledby="myModalLabel1" >
	<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		<form role="form" action="" method="post" id="formLocation" enctype="multipart/form-data">
			<div class="modal-header">
		  	<h4 class="modal-title model_title" id="myModalLabel1"></h4>
	  		<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
	  		</button>
			</div>
			<div class="modal-body">
				<fieldset class="form-group floating-label-form-group">
					<label for="email">Material Receive Date</label>
						<input type="hidden" id="sales_return_id" value="" >
						<input type="date" class="form-control" name="material_receive_date" id="material_receive_date">
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
				<button type="button" id="save_date" class="btn btn-success">Save </button>
			</div>
			</form>
  	</div>
	</div>
</div>
<!-- PaymentApproveModel -->
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
	<script type="text/javascript">
		var searchName="";
		var data_url = "sales_return_get_ajax.php";
		$('#ToDate').datepicker({  datepicker: true, autoclose: true });
		$('#FromDate').datepicker({  datepicker: true, autoclose: true });
		var ToDate="";
		var invoice_type="";
		var FromDate="";
		var df1="";
		var df2="";
		var status="<?= $_REQUEST['status'] ?>";
		var type="";
		var qid="";
		var sales_id="<?= $_REQUEST['sales_id'] ?>";
		var flag="3";
		var invoice_month="<?= $_REQUEST['invoice_month']?>";
		var invoice_year="<?= $_REQUEST['invoice_year']?>";
		var customer_id="<?=$_REQUEST['customer_id']?>";
		var todate="<?=$_REQUEST['todate'] ?>";
		var fromdate="<?=$_REQUEST['fromdate'] ?>";
		var uid="<?php echo $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']?>";

		function searchByName(){
			searchName = $("#searchName").val();
			type = $("#type").val();
			sales_id = $("#sales_id").val();
			status = $("#status").val();
			qid=$("#qid").val();
			df1=$("#material_request_filter_input").val();
			df1 = encodeURI(df1);
			df2=$("#material_request_filter_input_date").val();
			df2 = encodeURI(df2);
			displayRecords(100,1);
			return false;
		}
		function clearSearchByName(){
			searchName = "";
			ToDate = "";
			FromDate = "";
			df1 = "";
			df2 = "";
			status = "";
			type = "";
			sales_id = "";
			qid = "";
			$("#searchName").val("");
			$("#ToDate").val("");
			$("#FromDate").val("");
			$("#material_request_filter_input").val("");
			$("#material_request_filter_input_date").val("");
			$("#status").val("");
			$("#type").val("");
			$("#sales_id").val("");
			$("#qid").val("");
			displayRecords(100,1);
		}
		$("#searchName").keyup(function(event){
			if(event.keyCode == 13){
				$("#searchByName").click();
			}
		});
		function getByDate() {

			var checkindatestr = $("#FromDate").val();
			var dateParts = checkindatestr.split("-");
			var checkindate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

			var checkindatestr1 = $("#ToDate").val();
			var dateParts1 = checkindatestr1.split("-");
			var now = new Date(dateParts1[2], dateParts1[1] - 1, dateParts1[0]);
			var difference = now - checkindate;
			var days = difference / (1000*60*60*24);
			ToDate = $("#ToDate").val();
			FromDate = $("#FromDate").val();
			if(days>=0)
			{
				displayRecords(100,1);
			}
			else
			{
				toastr.error("From Date Should Be Less Than To Date");
			}
		}
		function getSubCat(cid){
			status=cid;
			displayRecords(100,1);
		}
		function loadDataTable(){
			$('#datatable_1').dataTable({
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false, 
				"order": [[ 4, "desc" ]], 

				 "order": [[1,'asc']], /* default order is index 1 */
                    'columnDefs': [ {
                        'targets': [0], /* column index */
                        'orderable': false, /* true or false */
                    }],

                    "oLanguage": {
                        "sEmptyTable": "Sorry No Data Available!!"
                    }

			});
		}
		function displayRecords(numRecords) {
			var searchName 	= $("#searchName").val();
			searchName 	= encodeURIComponent(searchName.trim()); 
			$('.preloader').fadeIn('slow');
			$("#results" ).html("");
			$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status +"&invoice_type=" + invoice_type  + "&uid=" + uid + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&qid=" + qid+ "&invoice_month=" + invoice_month + "&invoice_year=" + invoice_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&df2=" + df2,function(){
				// $("#loading-modal").modal('hide');
				$('.preloader').fadeOut('slow');
				loadDataTable();
			}); //load initial records
			
			//executes code below when user click on pagination links
			$("#results").on( "click", ".paging_simple_numbers a", function (e){
				var searchName 	= $("#searchName").val();
				searchName 	= encodeURIComponent(searchName.trim());
				var type = $("#type").val();
				var status = $("#status").val();
				var sales_id = $("#sales_id").val();
				e.preventDefault();
				var numRecords  = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName+ "&invoice_type=" + invoice_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&qid=" + qid+ "&invoice_month=" + invoice_month + "&invoice_year=" + invoice_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate + "&df2=" + df2,{"page":page}, function(){ //get content from PHP page
					$(".loading-div").hide(); //once done, hide loading element
					loadDataTable();
				});
				
			});
		}
		function loadDataTable_outlets(){
			$('#datatable_outlets').dataTable({
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false, 
				"aoColumns": [
					  { "sWidth": "5%" }, 
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "5%" },
					  { "sWidth": "20%","bSortable": false }
					]
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
				// window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
				window.location.href='sales_return_crud.php?mode=delete&id='+id;
				
			}
		}
		function genReport()
		{
			var searchName = $("#searchName").val();
			searchName     = encodeURIComponent(searchName.trim());
			df1 = 	$("#material_request_filter_input").val();
			df2=$("#material_request_filter_input_date").val();
			status = $("#status").val();
			type = $("#type").val();
			sales_id = $("#sales_id").val();
			qid = $("#qid").val();

			$.ajax({
				method: "POST",
				url: "dealer_invoice_info_genReport_ajax.php",
				data: 'searchName=' + searchName + '&ToDate=' + ToDate + '&FromDate=' + FromDate + '&df=' + df1 + '&df2=' + df2 + '&status=' + status + '&type=' + type + '&sales_id=' + sales_id + '&qid=' + qid,
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
		function genReportTally()
		{
			var searchName = $("#searchName").val();
			searchName     = encodeURIComponent(searchName.trim());
			df1 = 	$("#material_request_filter_input").val();
			status = $("#status").val();
			type = $("#type").val();
			sales_id = $("#sales_id").val();
			qid = $("#qid").val();

			$.ajax({
				method: "POST",
				url: "dealer_invoice_info_genReport_for_tally_ajax.php",
				data: 'searchName=' + searchName + '&ToDate=' + ToDate + '&FromDate=' + FromDate + '&df=' + df1 + '&status=' + status + '&type=' + type + '&sales_id=' + sales_id + '&qid=' + qid,
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
			function genInvoicePrint(){
			var searchName     = $("#searchName").val();
		  	searchName     = encodeURIComponent(searchName.trim());
			df1 = 	$("#material_request_filter_input").val();
			qid = 	$("#qid").val();
			status = $("#status").val();
			type = $("#type").val();
			sales_id = $("#sales_id").val();
		 	var myWindow = window.open('print_dealer_invoice_ajax.php?searchName='+searchName+"&uid=" + uid + "&status=" + status + "&sales_id=" + sales_id + "&type=" + type + "&df=" + df1+ "&qid=" + qid,'','width=700,height=800');
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
	$('#myLRModal').on('show.bs.modal', function (event) {
  		var button = $(event.relatedTarget) // Button that triggered the modal
  		var requesting_id=button.data("id");
  		var requesting_title=button.data("title");
  		$(".model_title").html(requesting_title);
  		$("#sales_return_id").val(requesting_id);
	})

	$(function(){
		$("#save_date").on('click',function(){
			AddMRDate();
		});
	})

	function AddMRDate()
	{
		var sales_return_id  = $('#sales_return_id').val();
 		var material_receive_date = $('#material_receive_date').val();
 		var myFormData = new FormData();
		myFormData.append('sales_return_id',sales_return_id);
 		myFormData.append('material_receive_date',material_receive_date);
		myFormData.append('mode',"addmaterialreceivedate");

		$.ajax({
      		url:"add_ajax_eway_bill_no.php",
      		type:"POST",
      		data:myFormData,
      		processData: false, // important
      		contentType: false, // important
      		beforeSend:function() {
      		},
      		success:function(result)
      		{
        		result=$.parseJSON(result);
		        if(result.ack==1)
		        {                          
		          toastr.success(result.ack_msg);
		          $("#myLRModal").modal('hide');
		          location.reload();
		        }               
	        	else
	        	{
	         		toastr.error(result.ack_msg);
	        	}         
      		},                    
   		});
	}


	function cancel_invoice(id){
	
		var r = confirm("Are you sure you want to Cancel Invoice ?");
		if(r){
			window.location.href='sales_return_crud.php?mode=cancel_invoice_flag&id='+id;
		}
	}

</script>
</body>
</html>