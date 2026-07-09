<?php
$page_id=605;$page_slug='customer_inquiry';
$ctable 	= "customer_inquiry";
$ctable1 	= "Customer Inquiry";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = "Customer Leads";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>$ctable."_manage.php","title"=>$page_title));
$FromDate="";
$ToDate="";
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
									<div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px">
										<div class="form-inline" role="form">
									  		<form class="form-inline" role="form" onSubmit="return searchByName();">
									   			<div class="form-group">
													<label>Search By Person / Company Name / Mobile Number/ Email: &nbsp;</label>
													<input type="text" placeholder="Search Here" class="form-control input-large" name="searchName" id="searchName" value="" />
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

								 	<div class="col-md-3 col-xs-3 col-sm-3 pull-right" style="margin-top:10px">

								 		<button type="button" class="btn yellow btn-sm" onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</button>

										<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genExcelReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>

										<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genInquiryPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div>
								</div>
                            </div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
					
						<div class="table-toolbar">
							
							<!-- <?php
							if($rights['insert_flag	']==0)
							{							
							?> -->
							<!-- <div class="row">
								<div class="col-md-6">
									<div class="btn-group">
										<a class="btn sbold blue-ebonyclay" href='<?php echo $ctable; ?>_crud.php?mode=add'> Add New
										<i class="fa fa-plus"></i>
										</a>
									</div>
								</div>
							</div> -->
							<!-- <?php 
							} ?> -->
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- sheet upload function -->
<link href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<div class="modal fade" id="uploadLeeds" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  	<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Upload Excel Data</h4>
			  </div>
		  	<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">									
							<div class="fileinput fileinput-new" data-provides="fileinput">
								<label class="control-label">Select XLS/XLSX File</label>	
								<div class="input-group input-large">
									<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
										<i class="fa fa-file fileinput-exists"></i>&nbsp;
										<span class="fileinput-filename"> </span>
									</div>
									<span class="input-group-addon btn default btn-file">
										<span class="fileinput-new"> Select file </span>
										<span class="fileinput-exists"> Change </span>
										<input type="file" name="excel_sheet" id="excel_sheet"> </span>
									<a href="javascript:;" id="remove_file" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
								</div>
							</div>					
						</div>
					</div>
					<div class="col-sm-12">
						<div id="result_upload">
							<div class="row">
								<div class="col-sm-12">
								<h3 class="inserted_log">Updated:</h3>
								</div>
								
							</div>
						</div>
					</div>
				</div>
		  	</div>
		  	<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="upload_sheet"><i class="fa fa-upload"></i> &nbsp;Start Upload</button>
		  	</div>
		</div>
 	</div>
</div>
<!-- sheet upload function -->

<script type="text/javascript">

$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });

var ToDate="";
var FromDate="";
var type="";
var assigned_to="";
var searchName="";
var status_id="";
var c_type = "";
var country = "";
var city="";
var country=""
var state="";
var df1 = "";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

// function getStatus(s)
// {
// 	status_id=s;
// 	//displayRecords(100,1);
// }

// function getSalesExecutive(val){
//     type=val;
//     //displayRecords(100,1);
// }

function searchByName(){
	searchName = $("#searchName").val();
	country = $("#country").val();
	state = $("#state").val();
	city = $("#city").val();
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	status_id = $("#status_id").val();
	c_type  = $("#c_type").val();
	type  = $("#type").val();
	assigned_to  = $("#assigned_to").val();
	//displayRecords(100,1);
	callAjax();

	
	return false;
}


function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' )
	{
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
	}
	else
	{
		alert("Please Select Date");
	}
}


function clearSearchByName(){
	searchName = "";
	c_type = "";
	type = "";
	assigned_to = "";
	ToDate = "";
	FromDate = "";
	country = "";
	state = "";
	city = "";
	status_id = "";
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#searchName").val("");
	$("#type").select2("val","");
	$("#assigned_to").select2("val","");
	$("#c_type").select2("val","");
	$("#country").select2("val","");
	$("#state").select2("val","");
	$("#city").select2("val","");
	$("#status_id").select2("val","");
	//displayRecords(100,1);
	callAjax();
}

$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});

function callAjax(sales_id,date){
	$.ajax({
	    url: data_url,
	    data: {
	    	numRecords: "100",
	        searchName: searchName,
	        type:type,
	        assigned_to:assigned_to,
	        status_id:status_id,
	        c_type:c_type,
	        country:country,
	        state:state,
	        city:city,
	        df:df1,
	    },
	    beforeSend: function() {
	        $("#results").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
	    },
	    success: function(result) {
	        $("#results").html(result);
	        if(country!="" && state!="")
	        {
	        	filter_country(country,state,city);
	        }
		}
	});	
}

function filter_country(country_id,state="",city=""){
        $.ajax({
            type: "POST",
            url: "find_city.php",
            data:'country_id='+country_id+'&state='+state,
            beforeSend:function(){
                // $("#loading-modal").modal('show');  
                $('.preloader').fadeIn('slow');
            },
           success: function(data){
                $("#state").select2("destroy");
                $("#state").html(data);
                $("#state").select2();
                // $("#loading-modal").modal('hide');
                $('.preloader').fadeOut('slow');
                if (state!="" && city!="")
                {
                    filter_state(state,city);
                }
            }
        });
    }
function filter_state(state_id,city=""){
    $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'state_id='+state_id+"&city="+city,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
        },
        success: function(data){
            $("#city").select2("destroy");
            $("#city").html(data);
            $("#city").select2();
            // $("#loading-modal").modal('hide');
        }
    });
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
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Inquiry Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	state 	= encodeURIComponent(state.trim());
	city 	= encodeURIComponent(city.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate+ "&status_id=" + status_id +"&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city + "&df=" + df1,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		var c_type  = $("#c_type").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate+ "&status_id=" + status_id +"&c_type=" + c_type + "&country=" + country  +"&state=" + state +"&city=" + city + "&df=" + df1,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});

	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var c_type  = $("#c_type").val();
	// 	var country = $("#country").val();
	// 	var state = $("#state").val();
	// 	var city = $("#city").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate+ "&status_id=" + status_id +"&c_type=" + c_type + "&country=" + country  +"&state=" + state +"&city=" + city,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });

	/*$("#results").on( "change", "#c_type", function (e){
		e.preventDefault();
		var c_type  = $("#c_type").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&ToDate=" + ToDate + "&FromDate=" + FromDate+ "&status_id=" + status_id +"&c_type=" + c_type + "&country=" + country  +"&state=" + state +"&city=" + city,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});*/

	
	// alert(country);
	// alert(state);
	// alert(city);
	
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	//displayRecords(100,1);
	callAjax();
});

function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}

function editStatus(id){
	$("#inquiry_status"+id).removeAttr("disabled");
	$("#editStatus_"+id).hide(100);
	$("#editStatus2_"+id).show(400);
}

function cancelEditStatus(id){
	$("#editStatus2_"+id).hide(100);
	$("#editStatus_"+id).show(400);
	$("#inquiry_status"+id).attr("disabled","disabled");
}

function saveEditStatus(id){
var newinquiry_status = $("#inquiry_status"+id).val();

	$.ajax({
		type: "POST",
		url: "ajax_update_status_request.php",
		data: "id=" + id + "&status=" + newinquiry_status+'&table=customer_inquiry',
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
</script>
<script type="text/javascript">
	
	function genInquiryPrint(){

		var searchName  = $("#searchName").val();
      	searchName     	= encodeURIComponent(searchName.trim());
      	var c_type  = $("#c_type").val();
		var country = $("#country").val();
		var state = $("#state").val();
		var city = $("#city").val();
		var c_type  = $("#c_type").val();
		var type  = $("#type").val();
		var assigned_to  = $("#assigned_to").val();

		var myWindow = window.open('print_customer_inquiry_ajax.php?searchName='+searchName+ "&type=" + type + "&assigned_to=" + assigned_to +"&c_type=" + c_type + "&country=" + country  +"&state=" + state +"&city=" + city,'','width=700,height=800');
     	myWindow.print();
    }


    function genExcelReport(){
    	var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	var ToDate = $("#ToDate").val();
		var FromDate = 	$("#FromDate").val();
      	var c_type  = $("#c_type").val();
		var country = $("#country").val();
		var state = $("#state").val();
		var city = $("#city").val();
		var c_type  = $("#c_type").val();
		var status_id  = $("#status_id").val();
		var type  = $("#type").val();
		var assigned_to  = $("#assigned_to").val();
		$.ajax({
	        method: "POST",
	        url: "customer_inquiry_report_excel.php",
	        data:{
        		searchName:searchName,
				country:country,
				state:state,
				city:city,
				c_type:c_type,
				type:type,
				assigned_to:assigned_to,
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
</script>


<script type="text/javascript">
$("#upload_sheet").on("click",function(){
if($("#excel_sheet").val()!="")
{
	if(confirm("Don't press refresh or back button while uploading. This can't be undone. Are you sure to continue this?"))
	{
		var data=new FormData();
		data.append('mode',"upload_discount");
		$.each($('#excel_sheet')[0].files, function(i, file) {
			data.append('discount_sheet', file);
		});	
		var lst_text=$(this).html();
		$.ajax({
			type:"POST",
			url:"customer_inquiry_upload_excel_function.php",
			data:data,
			cache: false,
			processData:false,
			contentType:false,
			beforeSend:function(){
				$(this).attr("disabled","disabled");
				$(this).attr("disabled","disabled");
				$(this).html("Uploading");
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
			success:function(data)
			{
				// $("#loading-modal").modal('hide');
				$('.preloader').fadeOut('slow');

				$(this).removeAttr("disabled");
				$(this).html(lst_text);
				var json_obj=$.parseJSON(data);
				if(json_obj.ack==1)
				{
					toastr.success(""+json_obj.ack_msg);
					$("#excel_sheet").val("");
					$("#remove_file").click();
					
					$("#result_upload").find("h3.inserted_log").html("Updated:"+json_obj.log.updated);
					//$("#result_upload").find("div.list-group").html(duplicate);
					/*if(confirm("Do you want to download backup file of database before uploaded data.?"))
					{
					}*/
						// location.reload();
					// displayFGItemMasterRecords(100,1);
				}	
				else
				{
					toastr.error(""+json_obj.ack_msg);
				}					
				
			},
			error:function()
			{	
				$("#loading-modal").modal('hide');
				$(this).removeAttr("disabled");
				$(this).html(lst_text);
				toastr.error('Connection Error Try Again Later');
				$("#uploadDiscount").modal("hide");
			},
			xhr: function () {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;                
						console.log(percentComplete);
						$(this).html("Uploading "+percentComplete+"%");
						$('div.progressbar').css({
							width: percentComplete * 100 + '%'
						});
						if (percentComplete === 1) { 
							$(this).html(lst_text)
						}
					}
				}, false);
				xhr.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						console.log(percentComplete);
						$(this).html("Uploading "+percentComplete+"%");
						$('div.progressbar').css({
							width: percentComplete * 100 + '%'
						});
						if (percentComplete === 1) { 
							$(this).html(lst_text)
						}
					}
				}, false);
				return xhr;
			}
		});
	}
}
else
{
	toastr.error("Select Excel File!!");
}

})
</script>

</body>
</html>