<?php
$page_id=634;$page_slug='customer_leager';
$ctable 	= "employee_leager";
$ctable1 	= "Employee Leager";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Account"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");

?>
<!DOCTYPE html> 
<html lang="en"> 
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
		<?php include("include_css.php"); ?>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
		<style type="text/css">
			.select2
			{
				width: 300px!important;
			}
		</style>
	</head>
	<body class="page-md">
		<?php include("header.php"); ?>
		<div class="page-container"> 
			<div class="page-head bg-grey">
				<div class="container">
					<div class="page-title">
						<h1><a href="<?php echo "dashboard.php";?>" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
			                            <div class="caption"><i class="fa fa-filter"></i>Filters </div>
		                             	<div class="tools">
			                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> 
			                            </div>
		                        	</div>
			                        <div class="portlet-body">
			                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;"> 
											<div class="row">
												<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
													<!-- <label>Select Customer Type</label>
													<select class="form-control" id="customer_type" name="customer_type" onchange="getCustomerList(this.value)">
														<option value="">Select Customer Type</option>
														<?php
														$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
														if ($cust_R) {
															while ($C = mysqli_fetch_assoc($cust_R)) {
														?>
															<option value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
														<?php
															}
														}
														?>
													</select> -->

													<label>Select Sales Officer Type</label>

														

		                                             <select class="form-control status" name="sales_executive_type" id="sales_executive_type" onchange="getCustomerList(this.value)" >
		                                             	<option value="">Select Sales Officer Type</option>
		                                                <option value="sales_manager" <?= ($sales_executive_type=="sales_manager")?"selected":""; ?>>Regional Sales Manager</option>
		                                               <option value="area_sales_manager" <?= ($sales_executive_type=="area_sales_manager")?"selected":""; ?>>Business Development Manager</option>

		                                               <option value="dispatch_sales_manager" <?= ($sales_executive_type=="dispatch_sales_manager")?"selected":""; ?>>Dispatch Manager</option>
		                                               
		                                               <option value="sales_officer" <?= ($sales_executive_type=="sales_officer")?"selected":""; ?>>Area Sales Manager</option>
		                                               <option value="sales_executive" <?= ($sales_executive_type=="sales_executive")?"selected":""; ?>>Sales Officer</option>
		                                               <option value="service_executive" <?= ($sales_executive_type=="service_executive")?"selected":""; ?>>Service Executive</option>
		                       
		                                             </select>

												</div>
												<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
		                                        	<label>Select Employee</label>
		                                        	<div class="form-group">
		                                             	<select class="form-control input-medium status select2" name="account" id="account" onChange="getCustomer();" style="width: 300px!important">
															<option value="">--- Select Employee ---</option>
			                                            </select>
													</div>
			                                    </div> 
			                                    <div class="col-md-6 col-xs-6 col-sm-6" style="margin-top:10px">
			                                    	<div class="form-inline" role="form">
									                    <div class="form-group">
									                        <label><b>From Date : &nbsp;</b></label><br/>
									                        <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date" autocomplete="off">
									                    </div>
									                    <div class="form-group">
									                        <label><b>To Date : &nbsp;</b></label><br/>
									                        <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date" autocomplete="off">
									                    </div>
									                    <div class="form-group" style="margin-top:20px;">
									                        <input class="btn btn-info btn-sm" type="button" value="View Ledger" onClick="getByDate();"> 
									                    </div>
									                     <!-- <button style="margin-left: 20px;" type="button" class="btn print btn-sm" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="printleager()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button> -->
									                </div> 
			                                    </div> 
			                                </div>
			                            </div>
			                    	</div>
		                    		<!-- END Portlet PORTLET-->
		                		</div>
								<div class="portlet light"> 
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
		</div>
		<?php include("footer.php"); ?>
		<?php include("include_js.php"); ?>
		<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
		<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		<script type="text/javascript">
		 	$('#FromDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy', maxDate:0 }); 
		 	$('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy', maxDate:0 }); 
		</script>
		<script type="text/javascript">
			var status="";
			var searchName="";
			var data_url = "employee_leager_get_ajax.php";

			function getByDate()
		 	{  
		        displayRecords(100,1); 
			} 
			function getCustomer(){ 
				displayRecords();
			}
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
					  	{ "sWidth": "20%","bSortable": false }
					],
					"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
				});
			}
			function displayRecords(numRecords) { 
				var account_id 	= $("#account").val(); 
				var ToDate = $("#ToDate").val();
    			var FromDate = $("#FromDate").val();
				// alert(account_id);
				$("#results" ).html("");
				$("#results" ).load( data_url+"?show=" + numRecords + "&sales_id=" + account_id + "&FromDate=" + FromDate + "&ToDate=" + ToDate,function(){
					loadDataTable();
				}); //load initial records
				
				//executes code below when user click on pagination links
				$("#results").on( "click", ".paging_simple_numbers a", function (e){
					e.preventDefault();
					var numRecords  = $("#numRecords").val();
					$(".loading-div").show(); //show loading element
					var page = $(this).attr("data-page"); //get page number from link
					$("#results").load(data_url+"?show=" + numRecords + "&sales_id=" + account_id + "&FromDate=" + FromDate + "&ToDate=" + ToDate,{"page":page}, function(){ //get content from PHP page
						$(".loading-div").hide(); //once done, hide loading element
						loadDataTable();
					});
					
				});
				$("#results").on( "change", "#numRecords", function (e){
					e.preventDefault();
					var numRecords  = $("#numRecords").val();
					$(".loading-div").show(); //show loading element
					var page = $(this).attr("data-page"); //get page number from link
					$("#results").load(data_url+"?show=" + numRecords + "&sales_id=" + account_id + "&FromDate=" + FromDate + "&ToDate=" + ToDate,{"page":page}, function(){ //get content from PHP page
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
				var ToDate=Date.parse($("#ToDate").val('<?= date('d-m-Y') ?>'));
	    		var FromDate=Date.parse($("#FromDate").val('<?= date('d-m-Y') ?>'));
				displayRecords(100,1);
			});
			function del_conf(id){
				var r = confirm("Are you sure you want to delete?");
				if(r){
					window.location.href='category_crud.php?mode=delete&id='+id;
				}
			}


			function getCustomerList(ctype) {
				$('#account').select2("val", "");
				$('#account').change();
				$.ajax({
					type: "post",
					url: "ajax_get_employee_plain_list.php",
					data: "sales_type=" + ctype,
					beforeSend: function() {
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
					success: function(result) {
						setTimeout(function() {
							
							$('#account').select2("destroy");
							$('#account').html(result);
							$('#account').select2();

							// $("#loading-modal").modal('hide');
							$('.preloader').fadeOut('slow');
						});
					}
				})
			}

			function printleager()
			{
				
		     
		      	    var account_id 	= $("#account").val(); 
					var ToDate = $("#ToDate").val();
	    			var FromDate = $("#FromDate").val();
		     
		     	var myWindow = window.open('print_customer_leager_ajax.php?sales_id='+account_id+'&ToDate='+ToDate+'&FromDate='+FromDate,'','width=700,height=800');
		     	myWindow.print();
	        }
		</script>
	</body>
</html>