<?php
$page_id=651;$page_slug='deep_freezer_scheme';
$ctable 	= "freezer_scheme";
$ctable1	= "deep_freezer_scheme";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"manage_".$ctable.".php","title"=>"Manage ".$ctable1));
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
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
				</div>
				<div class="col-md-12">
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
                                	<div class="col-md-7 col-xs-7 col-sm-7">
                                        <?php
											echo $db->getAddButton($ctable1);
										?>
                                    </div>
                                	<div class="col-md-5 col-xs-5 col-sm-5 pull-right">
                                		<div class="form-inline" role="form">
											<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group">
	                                                <input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search By Complain No, Mobile No.:" value="" />
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
	                                                            <a name="print" onClick="genFreezePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
	                                                        </li>
	                                                        <?php
															}
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{  
															?>
	                                                        <li>
	                                                            <a class="excel" name="excel" onClick="genReportFreeze()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
				<div class="col-md-12">
					<div class="portlet light">
						<!-- <div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $db->getAddButton($ctable);

									?> 
								</div>
							</div>
						</div> -->
						<div class="portlet-body">
							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div class="">
								<div id="results"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 
<!-- view image modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
        <div class="modal-content" style="margin-top: -41px">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel"><b>View Image</b></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 15px;top: 25px">
              <span aria-hidden="true">&times;</span>
            </button>
          </div> 
          <div class="portlet-body" id="requesting_ajax" style=""></div> 
        </div>
  	</div>
</div>
<!-- view image modal -->

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
	
var searchName="";
var state="";
var city="";
var df1="<?= date('d-m-Y') ." to ". date('d-m-Y') ?>";
df1 = encodeURI(df1);

var sales_executive="<?= $_REQUEST['sales_id']?>";
var customer_id="<?= $_REQUEST['customer_id'] ?>";
var status="<?= $_REQUEST['status'] ?>";
var complain_month="<?= $_REQUEST['complain_month']?>";
var complain_year="<?= $_REQUEST['complain_year']?>";
var data_url = "deep_freezer_scheme_get_ajax.php";
var todate="<?=$_REQUEST['todate'] ?>";
var fromdate="<?=$_REQUEST['fromdate'] ?>";
// var data_url = "index_demo.php";

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	status = $("#status").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	displayRecords(50,1);
	return false;
}
$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(50,1);
})

/*$("#status_id").on("change",function()
{
	var status_id=$(this).val();
	alert(status_id);
});*/

// function getStatus(s)
// {
// 	status_id=s;
// 	displayRecords(50,1);
// }
function clearSearchByName(){
	searchName = "";
	sales_executive = "";
	customer_id = "";
	status = "";
	df1 = "";
	$("#searchName").val("");
	$("#customer_id").val("");
	$("#status").val("");
	$("#sales_executive").val("");
	$("#material_request_filter_input").val("");
	displayRecords(50,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "30%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%"},
			  { "sWidth": "5%"},
			  { "sWidth": "5%"},
			
			  { "sWidth": "5%" ,"bSortable": false },
			]
	});
	// $('#datatable_1').dataTable({
	// 	"bPaginate": false,
	// 	"bFilter": false,
	// 	"bInfo": false,
	// 	"bAutoWidth": false, 
		// "aoColumns": [
		// 	  { "sWidth": "10%" }, 
		// 	  { "sWidth": "10%" }, 
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "10%" },
		// 	]

// 		"order": [[1,'asc']], /* default order is index 1 */
//             'columnDefs': [ {
//                 'targets': [0], /* column index */
//                 'orderable': false, /* true or false */
//             }],

//             "oLanguage": {
//                 "sEmptyTable": "Sorry No Data Available!!"
//             }
// 	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	// $("#loading-modal").modal('show');
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status=" + status+ "&complain_month=" + complain_month + "&complain_year=" + complain_year+ "&todate=" + todate+ "&fromdate=" + fromdate +"&df="+df1 ,function(){
		// $("#loading-modal").modal('hide');
		loadDataTable();
		$('.preloader').fadeOut('slow');
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status=" + status + "&complain_month=" + complain_month + "&complain_year=" + complain_year+ "&todate=" + todate+ "&fromdate=" + fromdate + "&df="+df1,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(50,1);
});

function approvestatus(id)
	{   
		var r=confirm("Are You Sure you want to Approve this??");
	    if(r)
	    {
	    	$.ajax({
	            url:"deep_freezer_status_ajax.php",
	            type:"POST",
	            data:{
	               	  id:id,           
	            },
	           beforeSend: function() {
				$(".transCover").fadeIn(800);
			},
			success: function(result){ 
				var result=$.parseJSON(result);
				if(result.ack==1)
				{
									
						$(".transCover").fadeOut(100);				
						toastr.success(result.ack_msg);
						
					
				}
				else
				{
					toastr.error(result.ack_msg);
				}
				displayRecords(50,1);
			}         
	        })
	    }
	}
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script type="text/javascript">
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
</script>

<script type="text/javascript">
	function genReportFreeze()
	{
		var searchName     = $("#searchName").val(); 
      	searchName     = encodeURIComponent(searchName.trim());
      		customer_id = $("#customer_id").val();
      		status = $("#status").val();
      	$.ajax({
			method: "POST",
			url: "deep_freezer_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		customer_id:customer_id,
        		status:status
        		
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

<script type="text/javascript">
	function genFreezePrint(){
		var searchName = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());

      	customer_id = $("#customer_id").val();
      	status = $("#status").val();
     	var myWindow = window.open('print_deep_freeze_ajax.php?&searchName='+searchName+ "&customer_id=" + customer_id+ "&status=" + status ,'','width=700,height=800');
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