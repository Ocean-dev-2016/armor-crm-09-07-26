<?php
$page_id=581;$page_slug='manage_complain';
$ctable 	= "complain";
$ctable1 	= "Complain";
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
											echo $db->getAddButton($ctable);
										?>
                                    </div>
                                	<div class="col-md-5 col-xs-5 col-sm-5 pull-right">
                                		<div class="form-inline" role="form">
											<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group">
	                                                <input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search By Complain No:" value="" />
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
	                                                            <a name="print" onClick="genComplainPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
	                                                        </li>
	                                                        <?php
															}
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{  
															?>
	                                                        <li>
	                                                            <a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
var df1="";
//var df1=encodeURI("<?= date('d-m-Y') ." to ". date('d-m-Y') ?>");
var sales_executive="<?= $_REQUEST['sales_id']?>";
var customer_id="<?= $_REQUEST['customer_id'] ?>";
var company_type="";
var status_id="<?= $_REQUEST['status'] ?>";
var complain_month="<?= $_REQUEST['complain_month']?>";
var complain_year="<?= $_REQUEST['complain_year']?>";
var data_url = "complain_get_ajax.php";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])); ?>";
var fromdate="<?=date('d-m-Y',strtotime($_REQUEST['fromdate'])); ?>";

if (todate != "" && fromdate != "" && todate != "01-01-1970" && fromdate != "01-01-1970" ) {
	
	df1 = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	complain_year = "";
	df1 = encodeURI(df1);
}


// var data_url = "index_demo.php";

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive = $("#sales_executive").val();
	// state = $("#state").val();
	// city = $("#main_city").val();
	//main_city = $("#main_city").val();
	customer_id = $("#customer_id").val();
	company_type = $("#company_type").val();
	status_id = $("#status_id").val();
	//displayRecords(50,1);
	displayRecords(50, 1);
	// displayRecords_super(50, 1);
	// if (state != "" && city != "") {
	// 	filter_state(state, city);
	// 	//filter_city(city, main_city)
	// }

	
	return false;
}
$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	company_type = $("#company_type").val();
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
	state = "";
	city = "";
	//main_city = "";	
	customer_id = "";
	company_type = "";
	status_id = "";
	df1 = "";
	$("#searchName").val("");
	$("#customer_id").val("");
	$("#company_type").val("");
	$("#status_id").val("");
	$("#sales_executive").val("");
	$("#state").select2("val", "");
	$("#city").select2("val", "");
	//$("#main_city").select2("val", "");	
	$("#material_request_filter_input").val("");
	displayRecords(50,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
// function getCity(val) {

// 	state = val;
// 	displayRecords(50, 1);
// 	displayRecords_super(50, 1);
// 	displayRecords_dealer(50, 1);
// 	displayRecords_outlet(50, 1);
// 	$.ajax({
// 		type: "POST",
// 		url: "find_city.php",
// 		data: 'state_id=' + val,

// 		beforeSend: function() {
// 			// $("#loading-modal").modal('show');
// 			$('.preloader').fadeIn('slow');

// 		},
// 		success: function(data) {
// 			$("#city").html(data);
// 			$('#city').select2("val", "");
// 			// $("#loading-modal").modal('hide');
// 			$('.preloader').fadeOut('slow');
// 		}
// 	});
// }

// function getCityName() {
// 	state = $('#state').val();
// 	//$('#state').select2("val","");
// 	//alert(cid);
// 	city = cid;
// 	city = encodeURIComponent(city.trim());

// 	displayRecords(50, 1);
// 	displayRecords_super(50, 1);
// 	displayRecords_dealer(50, 1);
// 	displayRecords_outlet(50, 1);
// }

function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
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

		"order": ['desc'], /* default order is index 1 */
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
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	state = encodeURIComponent(state.trim());
	city = encodeURIComponent(city.trim());
	//main_city = encodeURIComponent(main_city.trim());
	// $("#loading-modal").modal('show');
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&state=" + state + "&city=" + city + "&customer_id=" + customer_id + "&company_type=" + company_type + "&df=" + df1+ "&status_id=" + status_id+ "&complain_month=" + complain_month + "&complain_year=" + complain_year+ "&todate=" + todate+ "&fromdate=" + fromdate ,function(){
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&state=" + state + "&city=" + city + "&customer_id=" + customer_id + "&company_type=" + company_type + "&df=" + df1+ "&status_id=" + status_id + "&complain_month=" + complain_month + "&complain_year=" + complain_year+ "&todate=" + todate+ "&fromdate=" + fromdate,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(50,1);
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
		var state = $("#state").val();
		var city = $("#city").val();
		//var main_city = $("#main_city").val();
		// alert(sales_executive);
		var customer_id = $("#customer_id").val();
		var company_type = $("#company_type").val();
		var df1 = $("#material_request_filter_input").val();
		df1 		   	= encodeURI(df1);
      	searchName     = encodeURIComponent(searchName.trim());
      	var status_id = $("#status_id").val();


      	$.ajax({
			method: "POST",
			url: "complain_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		sales_executive:sales_executive,
        		state: state,
				city: city,
				//main_city: main_city,
        		df1:df1,
        		status_id:status_id,
        		customer_id:customer_id,
        		company_type:company_type,
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
	function genComplainPrint(){
		var searchName     = $("#searchName").val();
      	var sales_executive = $("#sales_executive").val();
      	var state = $("#state").val();
		var city = $("#city").val();
		//var main_city = $("#main_city").val();
      	var customer_id = $("#customer_id").val();
      	var company_type = $("#company_type").val();
      	var status_id = $("#status_id").val();
      	df1=$("#material_request_filter_input").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_complain_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+ '&state='+state+'&city='+city+'&df='+df1+'&customer_id='+customer_id+"&company_type="+company_type+'&status_id='+status_id,'','width=700,height=800');
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


<script>

// 	function editStatus(id){
// 		$("#complain_status"+id).removeAttr("disabled");
// 		$("#editStatus_"+id).hide(500);
// 		$("#editStatus2_"+id).show(400);
// }
// 	function cancelEditStatus(id){
// 		$("#editStatus2_"+id).hide(500);
// 		$("#editStatus_"+id).show(400);
// 		$("#complain_status"+id).attr("disabled","disabled");
// }

// function saveEditStatus(val,id){
// //var newcomplain_status = $("#complain_status"+id).val();
// var newcomplain_status = val;
// // alert(newcomplain_status);
// 	$.ajax({
// 		type: "POST",
// 		url: "ajax_update_status.php",
// 		data: "id=" + id + "&status=" + newcomplain_status+'&table=complain',
// 		cache: false,
// 		beforeSend: function() {
			
// 		},
// 		success: function(html) {		

// 			var result=$.parseJSON(html);
// 			if(result.ack==1)
// 			{
// 				toastr.success(result.ack_msg);
// 				cancelEditStatus(id);
// 			}
// 			else
// 			{
// 				toastr.error(result.ack_msg);
// 			}
// 			if(html==1){
				
// 				toastr.success("Status Updated Successfully");
// 			}			
// 		}
// 	});
		
// }

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


<script type="text/javascript">
// 	function editCompalain(id)
// 	{
// 		$("#complain_assign"+id).removeAttr("disabled");
// 		$("#editcomplain_"+id).hide(500);
// 		$("#editcomplain2_"+id).show(400);
// 	}
	
// 	function cancelCompalain(id)
// 	{
// 		$("#editcomplain2_"+id).hide(500);
// 		$("#editcomplain_"+id).show(400);
// 		$("#complain_assign"+id).attr("disabled","disabled");
// 	}


// 	function saveCompalain(id){
// 	var newcomplain_assign = $("#complain_assign"+id).val();
// 	$.ajax({
// 		type: "POST",
// 		url: "ajax_update_complain_assign.php",
// 		data: "id=" + id + "&status=" + newcomplain_assign+'&table=complain',
// 		cache: false,
// 		beforeSend: function() {
			
// 		},
// 		success: function(html) {		

// 			var result=$.parseJSON(html);
// 			if(result.ack==1)
// 			{
// 				toastr.success(result.ack_msg);
// 				cancelEditStatus(id);
// 			}
// 			else
// 			{
// 				toastr.error(result.ack_msg);
// 			}
// 			if(html==1){
				
// 				toastr.success("Complain Assign To New User Successfully");
// 			}			
// 		}
// 	});
		
// }

</script>

</body>
</html>