<?php
$page_id=591;$page_slug='request_page';
$ctable 	= "request";
$ctable1 	= "Request";
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
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
								<!-- <div class="row filter_list">
									<div class="col-md-4 col-xs-4 col-sm-4" style="margin-top:10px">
								  		<div class="input-group pull-left">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div>
                                    </div>
                                </div> -->
                                <div class="row filter_list">
                                	<div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px">
										<form class="form-inline" role="form" onSubmit="return searchByName();">
											<div class="form-group">
                                                <label>Search Request No: &nbsp;</label>
                                                <input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search Here" value="" />
                                            </div>
                                            <div class="form-group">
                                                <input class="btn btn-danger btn-sm" type="submit" value="search">
                                            </div>
                                             <div class="form-group">
                                                   	<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                            </div>
										</form>
                                    </div>
                                     <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn btn-sm print pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genRequestPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div>
                                </div>                             
                                </div>
                            </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
				<div class="col-md-12">
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $db->getAddButton($ctable);
									?>	
								</div>
							</div>
						</div>
						<div class="portlet-body">
							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div class="table-responsive">
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
var df1="";
var sales_executive="";
var customer_id="";
var status_id="";
var data_url = "request_get_ajax.php";
// var data_url = "index_demo.php";

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	status_id = $("#status_id").val();
	displayRecords(100,1);
	return false;
}
$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})

/*$("#status_id").on("change",function()
{
	var status_id=$(this).val();
	alert(status_id);
});*/

// function getStatus(s)
// {
// 	status_id=s;
// 	displayRecords(100,1);
// }
function clearSearchByName(){
	searchName = "";
	sales_executive = "";
	customer_id = "";
	status_id = "";
	df1 = "";
	$("#searchName").val("");
	$("#customer_id").val("");
	$("#status_id").val("");
	$("#sales_executive").val("");
	$("#material_request_filter_input").val("");
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
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		// 
	// });

	// $("#results").on( "change", "#sales_executive", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });

	// $("#results").on( "change", "#customer_id", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&status_id=" + status_id ,{"page":page}, function(){ //get content from PHP page
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
	function genReport()
	{
		var searchName     = $("#searchName").val();
		var sales_executive = $("#sales_executive").val();
		var customer_id = $("#customer_id").val();
		var df1 = $("#material_request_filter_input").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	var status_id = $("#status_id").val();


      	$.ajax({
			method: "POST",
			url: "request_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		sales_executive:sales_executive,
        		df1:df1,
        		status_id:status_id,
        		customer_id:customer_id,
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
	function genRequestPrint(){
		var searchName     = $("#searchName").val();
      	var sales_executive = $("#sales_executive").val();
      	var customer_id = $("#customer_id").val();
      	var status_id = $("#status_id").val();
      	df1=$("#material_request_filter_input").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_request_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&df='+df1+'&customer_id='+customer_id+'&status_id='+status_id,'','width=700,height=800');
     	myWindow.print();
    }
</script>


<script>

	function editStatus(id){
		$("#complain_status"+id).removeAttr("disabled");
		$("#editStatus_"+id).hide(100);
		$("#editStatus2_"+id).show(400);
}
	function cancelEditStatus(id){
		$("#editStatus2_"+id).hide(100);
		$("#editStatus_"+id).show(400);
		$("#complain_status"+id).attr("disabled","disabled");
}

function saveEditStatus(id){
var newcomplain_status = $("#complain_status"+id).val();
// alert(newcomplain_status);
	$.ajax({
		type: "POST",
		url: "ajax_update_status_request.php",
		data: "id=" + id + "&status=" + newcomplain_status+'&table=request',
		cache: false,
		beforeSend: function() {
			
		},
		success: function(html) {		

			var result=$.parseJSON(html);
			if(result.ack==1)
			{
				toastr.success(result.ack_msg);
				cancelEditStatus(id);
			}
			else
			{
				toastr.error(result.ack_msg);
			}
			if(html==1){
				
				toastr.success("Status Updated Successfully");
			}			
		}
	});
		
}

/*function PopUp(src){
	$("#myModal").css("display","block");
	$("#img01").attr("src",src);
};


//image slider

$('#myModal1').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Button that triggered the modal
  	var requesting_id=button.data("id");

  	var type=button.data("type");
	$("#requesting_ajax").load("image_info_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();   
});*/
</script>



</body>
</html>