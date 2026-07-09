<?php
$page_id=570;$page_slug='customer_manage';
$ctable 	= "executive";
$ctable1 	= "Executive";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage My Customer";
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
                                    <div class="col-md-4 col-xs-12 col-sm-12" style="margin-top:10px">
                                        <div class="form-group">
                                            <label>Search by Customer Type: </label>
                                            <select class="form-control input-small status" name="status" id="status"  autofocus onChange="getExecutive(this.value);">

                                                 <option value="">Select Customer</option>
                                                 <option value="super_stockist">Super Stockist</option>
                                                <option value="dealer">Dealer Distributor</option>
                                                <option value="outlets">Outlets</option>
                                             </select>
										</div>
                                    </div>
                                         
                                <div class="col-md-8 col-xs-6 col-sm-6 " style="margin-top:10px">

								  <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
								   <div class="form-group">
										<label>Search By Name: &nbsp;</label>
										<input type="text" placeholder="Enter Name Or Email Or Phone" class="form-control input-large" name="searchName" id="searchName" value="" />
									</div>
									 <div class="form-group">
										<input class="btn btn-danger btn-sm" type="submit" value="search">
									</div>
									 <div class="form-group">
											<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
									</div>
								  </div>
								</form>
                                </div>
								<div class="row">
                                <div class="col-md-4  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-inline" role="form">
										<label>Search by State: </label>
                                            <select class="form-control input-large status" name="state" id="state" onChange="getCity(this.value);" autofocus >

                                                 <option value="">--- Select State---</option>
													<?php
													$id_r = $db->rp_getData("state","*",0);
													if(mysqli_num_rows($id_r)>0){
														while($id_d = mysqli_fetch_array($id_r)){
													?>
													<option value="<?php echo $id_d['id']; ?>"><?php echo $id_d['name']; ?></option>

													<?php
														}
													}
													?>
                                            </select>
											
                                         </div>
                                     </div>
									 <div class="col-md-4  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-group">
										<label>Search by City: </label>
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
					<div class="portlet light">
					
						<div class="table-toolbar">
						
							<div class="row">
								<div class="col-md-6">
									<div class="btn-group">
										<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn sbold blue-ebonyclay dropdown-toggle"> 
											 Add NEW<i class="fa fa-plus"></i>
										</button>
										<?php
										if($_REQUEST['type']=='super_stockist')
										{
											?>
											<ul role="menu" class="dropdown-menu">
											<li>
											<a  href="executive_crud.php?type=dealer&mode=add" title="Dealer distributor"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Dealer Distributor</span></a>
											</li>
										</ul>
										<?php
										}
										else if($_REQUEST['type']=='dealer')
										{
										?>
										<ul role="menu" class="dropdown-menu">
											<li>
											<a  href="executive_crud.php?type=outlets&mode=add" title="outlets"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Outlets</span></a>
											
											</li>
										</ul>
										<?php
										}
										?>
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
						<i class="fa fa-gift"></i>View Customer Information </div>
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
var status="";
var searchName="";
var city="";
var state="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
function getExecutive(type){
	    status=type;
        displayRecords(100,1);
}
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	status = "";
	city = "";
	state = "";
	$("#searchName").val("");
	$("#status").select2("val","");
	$("#city").select2("val","");
	$("#state").select2("val","");
	displayRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getCity(val)
        {
        $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'state_id='+val,
        success: function(data){
        $("#city").html(data);
		$('#city').select2("val","");
        }
    });
}
function getCityName(cid){
	state=$('#state').val();
	//$('#state').select2("val","");
	city=cid;
	displayRecords(100,1);
	
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
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status +"&state=" + state +"&city=" + city,function(){
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
function genReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "executive_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
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