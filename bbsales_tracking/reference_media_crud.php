<?php
$page_id=584;$page_slug='reference_media_page';
include("connect.php");

$ctable 	= "reference_media";
$ctable1 	= "Reference Media";
$main_page 	= "Reference Media Manage";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;

require_once("../include/class.reference_media.php");
$ref_obj= new ReferenceMedia();
$cid="";
$name	= "";
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$detail['id']		= $db->clean($_REQUEST['reference_media_id']);	
		$detail['name']		= $db->clean($_REQUEST['name']);	
		$detail['slug']		= $db->rp_createSlug($_REQUEST['name']);	
		$dupcheckarray=array("key"=>"name","value"=>$detail['name']);
		if($detail['id']!=""){
			$reply=$ref_obj->update($detail,$detail['name'],$detail['id']);
		}else{
			$reply=$ref_obj->insert($detail,$dupcheckarray);
		}
		echo json_encode($reply);exit;
	}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
	$detail['id']=$_REQUEST['id'];

	$reply=$ref_obj->Delete(array("key"=>"id","value"=>$detail['id']));
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		
		$db->rp_location($ctable."_crud.php?msg=deleted");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	
	$db->rp_location($ctable."_crud.php?msg=updated");
}

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
<link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link href="assets/global/css/demo.html5imageupload.css?v1.3" rel="stylesheet">
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				
				<h1><a href="dashboard.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
				
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					 <?php $db->printErrorMessage(); ?>
					 <?php $db->printSuccessMessage(); ?>
				</div>
			</div>
			<div class="col-md-12 col-sm-12">
						
						 <div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									<i class="fa fa-filter"></i> &nbsp; Filter
								</div>
							</div>
							<div class="portlet-body">
					
								<div class="row">
									<div class="col-md-4 pull-right">
										<form action="#" onSubmit="return searchByName();">
									
										<label> Search</label>
											<div class="input-group">
												
												<input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Enter Reference Media Name"/>
												<span class="input-group-btn">
													<input class="btn btn-success" type="submit" value="search">
												</span>
												<span class="input-group-btn">
													<input class="btn btn-danger" type="button" value="clear" onClick="clearSearchByName();">
												</span>
											</div>
										</form>
										</div>
								</div>
							</div>
					
						</div>	

				</div>	
				<div class="col-sm-12">
					<div class="portlet light">
					<?php //if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==2){?>
						<div class="table-toolbar">
							<div class="row">
                               <div class="col-md-6">
									<div class="btn-group">
										<a class="btn sbold blue-ebonyclay" data-toggle="modal" href="#priority"> Add New
											<i class="fa fa-plus"></i>
										</a>
									</div>
								</div> 
								<!--<div class="col-md-12" align="right">
									<a class="btn btn-success " target="_blank" onClick="genReport('<?php echo $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']; ?>','<?php echo $_SESSION[SITE_SESS.'_ADMIN_TYPE']?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Download</a>
									<button type="button" class="btn green-haze excel" name="excel" onClick="genReport1()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
								</div>
								<div class="col-md-6" align="right">
									
									<button type="button" class="btn green-haze excel" name="excel" onClick="genReport1()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
								</div>-->


							</div>
						</div>
						<?php// } ?>
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results"></div>
						</div>
					</div>
				</div>
			
		</div>
	</div>
</div>
<div class="modal fade bs-modal-sm" id="priority" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Add Reference Media</h4>
			</div>
			<form role="form" action="" method="post" id="primary_add">
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Reference Media Name<code>*</code></label>
							<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>"  data-validation="length alphanumeric"   data-validation-length="4-10" data-validation-error-msg="Only alphanumeric Values is allow">
							<p class="help-block"></p>
						</div>
					</div>
					
				</div>
			</div>
			</form>
			<div class="modal-footer">
				<input type="hidden" name="reference_media_id" id="reference_media_id">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
				<button type="button" name="btn_save" id="btn_save" class="btn green">Save</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
<!-- /.modal-dialog -->
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript">
var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
$(function(){
	$("#btn_save").click(function(){
		AddReferenceMedia();
	})
})
function AddReferenceMedia(){
	var valid=check_form();
	if(valid){
		var name=$("#name").val();
		var reference_media_id=$("#reference_media_id").val();
		$.ajax({
			type: "GET",
			url:"reference_media_crud.php",
			data:{
				name:name,
				reference_media_id:reference_media_id,
				mode:"add"
			},
			success: function(json) {
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{
					toastr.success(msg,"Success!!");
					$("#priority").modal("hide");
					$("#name").val("");
					displayRecords(100,1);
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}
}
	$('#priority').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var reference_media_id=button.data("id");
		  var caste_name=button.data("name");
		  $("#name").val(caste_name);
		  $("#reference_media_id").val(reference_media_id);
	});
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords(100,1);
}
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			 { "sWidth": "2%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Category Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords  + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords +  "&searchName=" + searchName ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords +"&searchName=" + searchName ,{"page":page}, function(){ //get content from PHP page
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
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){		
		vd=aj.error('name',"Please Enter Category Name.","add_error");
		isValid=false;
	}		
	if(isValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}

</script>


</body>
</html>