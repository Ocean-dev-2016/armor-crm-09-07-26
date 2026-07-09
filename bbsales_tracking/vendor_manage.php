<?php
$page_id=408;$page_slug='page_vendor';
$ctable 	= "vendor";
$ctable1 	= "Vendor";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
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
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
								<div class="row">
									<div class="col-md-5  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-inline" role="form">
										<label>Search by State</label>
                                        <select class="form-control input-large status" name="state" id="state"  autofocus  onChange="getCity(this.value);">
											<option value="">--- Select State---</option>
											<?php
												$id_r = $db->rp_getData("state","*",0);
												if(mysqli_num_rows($id_r)>0){
												while($id_d = mysqli_fetch_array($id_r)){
											?>
												<option value="<?php echo $id_d['name']; ?>"><?php echo $id_d['name']; ?></option>
											<?php
													}
												}
											?>
                                        </select>
										</div>
                                    </div>
                                    <div class="col-md-7 col-xs-6 col-sm-6 " style="margin-top:10px">
										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
                                                <label>Search By Name : &nbsp;</label>
                                                <input type="text" class="form-control input-large" name="searchName" id="searchName" placeholder="Enter Name Or Phone Or Email" value="" />
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
                                <div class="row">
								<div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
                                    <div class="form-group">
                                        <label>Search By City : </label>
											
											<select class="form-control input-large status" name="city" id="city"  autofocus onChange="getCityName(this.value);">

                                                 <option value="">--- Select City---</option>
													<?php
													/*$id_r = $db->rp_getData("city","*",0);
													if(mysqli_num_rows($id_r)>0){
														while($id_d = mysqli_fetch_array($id_r)){
													?>
													<option value="<?php echo $id_d['id']; ?>"><?php echo $id_d['name']; ?></option>

													<?php
														}
													}*/
													?>
                                            </select>

                                        </div>      
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
						View Vendor Information </div>
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
<script type="text/javascript">
var searchName="";
var state="";
var city="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
  //alert("sd"+requesting_id);
	$("#requesting_ajax").attr("data-url","vendor_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();	
})

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	state = "";
	city = "";
	$('#state').select2("val","");
	$('#city').select2("val","");
	$("#searchName").val("");
	displayRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getCity(val){
	//state=$('#state').val();
	//$('#state').select2("val","");
	state=val;
	displayRecords(100,1);
	
	 $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'state_id='+val,
		beforeSend:function(){
			// $("#loading-modal").modal('show');	
			$('.preloader').fadeIn('slow');
				
		},
        success: function(data){
        $("#city").html(data);
		$('#city').select2("val","");
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
	
}
	 });
}
function getCityName(cid){
	//state=$('#state').val();
	city=cid;
	displayRecords(100,1);
	
}
function getState(id){
		state=id;
		displayRecords(100,1);
}
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
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());
	
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city,{"page":page}, function(){ //get content from PHP page
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
function genReport(vid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "vendor_ajax_genReport.php",
		data: 'vid='+vid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ //alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
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
</script>
</body>
</html>