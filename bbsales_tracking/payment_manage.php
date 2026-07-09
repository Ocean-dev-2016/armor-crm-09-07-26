<?php
$page_id=571;$page_slug='payment';
$ctable 	= "payment";
$ctable1 	= "Payment";
$main_page 	= $ctable;
$page 		= "Customer Receipt";
$page_title = "Customer Receipt";
$FromDate="";
$ToDate="";
$page_hierarchy=array(array("link"=>"","title"=>"Account"),array("link"=>$ctable."_manage.php","title"=>$page_title));
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
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
				<div class="col-sm-12">
					<?php $db->printSuccessMessage(); ?>
					<?php $db->printErrorMessage(); ?>
				</div>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-12">
                        	<!-- BEGIN EXAMPLE TABLE PORTLET-->
                        	<!-- <div class="portlet light"> -->
					    		<div class="table-toolbar">
									<div class="row">
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
						                        <!-- <div class="slimScrollDiv"  style="position: relative;width: auto; height: auto;"> -->
						                            <div class="row">
						                                <!-- <div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
															<div class="form-inline" role="form">
																<div class="form-group">
						                                            <label>Filter By Created Date : &nbsp;</label><br/>
						                                                <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="" placeholder="From Date">
						                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="" placeholder="To Date">
						                                        </div>
					                                            <div class="form-group">
					                                                 <label>&nbsp;&nbsp;</label>
					                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="" placeholder="To Date">
					                                            </div>
					                                            <div class="form-group">
					                                                <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
					                                                <input class="btn btn-success btn-sm" type="submit" value="Clear" onClick="clearSearchByName();">
					                                            </div>
															</div>
						                                </div> -->
						                                <div class="col-md-4 col-xs-4 col-sm-4" style="margin-top:10px">
															<div class="form-inline" role="form">
                                             <div class="form-group">
                                                <label>Filter By Payment Date : &nbsp;</label></br>
                                                <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
                                             </div>
                                          </div>
				                                       	</div>
				                                       	<!-- <div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															
						                                </div> -->
						                                <div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															
						                                </div>
						                                <div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															
						                                </div>
						                                	<div class="col-md-4 col-xs-4 col-sm-4 pull-right" style="margin-top:10px;">
							                                	<div class="form-inline" role="form">
							                                		<label>Search By Company/Customer Name:</label>
							                                    	<form class="form-inline" role="form" onSubmit="return searchByName();">
							                                       	<div class="form-group">

							                                          <input type="text" style="width:180px!important" placeholder="  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
																							<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right" >
																								<!-- <li>
																									<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
																								</li> -->
																								<?php
																								if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																								{ 
																									?>
																									<li>
																										<a name="print" onClick="genSalesExecutivePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																									</li>
																								<?php
																								}
																								if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																								{ 
																									?>
																								<li>
																									<a class="excel" name="excel" onClick="genSalesExecutiveExcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
												<!-- </div> -->
											</div>
            							</div>
            						</div>
            					</div>
            				
            		
            
    


	 
										
										<div class="col-md-6">
											<table class="table" style="margin-bottom:-40px;">
											<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
													<tr>
														<td><input type="hidden"  name="FromDate" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
														</td>
														<td>
															<input type="hidden"  name="ToDate" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
														</td>											
														<td>
														<input class="btn btn-danger btn-sm" type="hidden" value="Filter" onClick="getByDate();">
														</td>
														<td>
														<input class="btn btn-success btn-sm" type="hidden" value="Clear" onClick="getByDate();">
														</td>
													</tr>
											</table> 						
										</div>
									</div>
								</div>
							<!-- </div> -->
                        <!-- END EXAMPLE TABLE PORTLET-->
                    	</div>
                    </div>
				</div>
				<div class="portlet light">
					<div class="table-toolbar">
						
							<div class="row">
								<div class="col-md-6">
									<div class="btn-group">
										<a class="btn sbold blue-ebonyclay" href='payment_crud.php?mode=add'> Add New
										<i class="fa fa-plus"></i>
										</a>
									</div>
								</div>
								
								
							</div>
						</div>
					<div class="table-toolbar">
						<div class="portlet-body">
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
						<i class="fa fa-gift"></i>View Sales Excecutive Information </div>
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
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
var type="";
var payment_status="";
var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });

var ToDate="";
var FromDate="";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","sales_executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})

function getByDate() {
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	displayRecords(500,1);
	if(FromDate<=ToDate)
	{

	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}

}
function getPaymentType(val){
	    type=val;
        displayRecords(500,1);
}
function searchByName(){
	searchName = $("#searchName").val();
	type = $("#type").val();
	payment_status = $("#payment_status").val();
	displayRecords(500,1);
	return false;
}

function clearSearchByName(){
	searchName = "";
	type = "";
	ToDate = "";
	FromDate = "";
	$("#ToDate").val("");
	$("#FromDate").val("");
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
	searchName 	= encodeURIComponent(searchName.trim());
	type 	= encodeURIComponent(type.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&payment_status=" + payment_status,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&payment_status=" + payment_status,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
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
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
function genReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "sales_executive_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					$(".transCover").fadeOut(500);
					window.location.href=result;
				},1500);
			}
	});
}
function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}


function genSalesExecutiveExcel(){
    	var searchName     = $("#searchName").val();
      	searchName     	   = searchName.trim();
      	// searchName     	   = encodeURIComponent(searchName.trim());
      	var ToDate          = $("#ToDate").val();
      	var FromDate          = $("#FromDate").val();
      	var type          = $("#type").val();
      	$.ajax({
	        method: "POST",
	        url: "payment_excel.php",
	        data:{
        		searchName:searchName,
				ToDate:ToDate,
				FromDate:FromDate,
				type:type
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

    function genSalesExecutivePrint() {

    	var searchName     = $("#searchName").val();
      	searchName     	   = encodeURIComponent(searchName.trim());
      	var ToDate          = $("#ToDate").val();
      	var FromDate          = $("#FromDate").val();
      	var type          = $("#type").val();

    	var myWindow = window.open('print_payment_ajax.php?searchName='+searchName+ "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate ,'','width=700,height=800');
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