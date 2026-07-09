<?php
$page_id=556;$page_slug='page_slaes_executive';
$page_slug="manage_super_stockist";
$ctable 	= "sales_executive";
$ctable1 	= "Sales Officer";
$main_page 	= $ctable;
$page 		= $ctable."_crud";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.sales_executive.php");
$objClass= new SalesExecutive();
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$type_of_inquiry= isset($_REQUEST['type'])?$_REQUEST['type']:"sales_manager";
$error=0;
$disabled          =false;
$sm_id	= "";
$asm_id	= "";
$so_id	= "";
$se_id	= "";
$class_id	= "";
$password	= "";
$name			= "";
$email			= "";
$phone 			= "";
$address		= "";
$zip 			= "";
$country		= "";
$state			= "";
$city			= "";
$zone 			= "";
$imei			= "";
$username			= "";
$area_id			= "";
$isActive		= 0;
$refreshToken ="";
$executive_in_min ="";
$executive_in_max ="";
$executive_out ="";
$super_stokist_order_insert_flag="";
$super_stokist_order_update_flag="";$dealer_order_insert_flag="";$dealer_order_update_flag="";$outlets_order_insert_flag="";$outlets_order_update_flag="";$inquiry_insert_flag="";$inquiry_update_flag="";$inquiry_delete_flag="";$customer_insert_flag="";$customer_update_flag="";$attendance_insert_flag="";$expense_insert_flag="";$add_area_insert_flag="";$price_list_insert_flag="";$gst_insert_flag="";$visit_card_insert_flag="";$export_db_insert_flag="";$complain_insert_flag="";$task_insert_flag="";$discount_insert_flag="";

	$oem_order_insert_flag="";$oem_order_update_flag="";$oem_order_delete_flag="";
	$quotation_insert_flag="";$quotation_update_flag="";$quotation_delete_flag="";
	$prospact_insert_flag="";$prospact_update_flag="";$prospact_delete_flag="";
	$monthlyorder_planner_view=""; $monthlyorder_planner_add=""; $monthlyorder_planner_edit=""; $monthlyorder_planner_delete="";
if($_SESSION['detail']!="")
{
	$detail=array();
	$detail=$_SESSION['detail'];
	extract($detail);
	unset($_SESSION['detail']);
}
//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$detail['id']=$_REQUEST['id'];	
	$executive=$db->rp_getValue("sales_executive","username","id=".$_REQUEST['id']." AND isDelete=0",0);
	$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Sales Officer"."- ".ucwords($executive).'&nbsp';	
	$reply=$objClass->EditSalesExecutive($detail);
	if($reply['ack']==1){

		$result=$reply['result'];
		//print_r($result); exit;
		$areas=$reply['area_id'];
		extract($result);
	}
      
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	
	
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objClass->SaledExecutiveDelete($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location($ctable."_manage.php?msg=inserted");
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
	$db->rp_location($ctable."_manage.php?msg=updated");
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
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>

</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				
				<h1><a href="<?php echo  $ctable;?>_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
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
			<div class="row">
				<div class="col-md-12">						
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="row">	
								<div class="col-sm-12">
									<div class="tabbable-custom nav-justified">
										<ul class="nav nav-tabs ">
											<li class="active">
												<a href="#tab_super_stockist_info" data-toggle="tab" aria-expanded="false"> Basic Information </a>
											</li>
											<!--<li>
												<a href="#tab_branch_info" data-toggle="tab" aria-expanded="false"> Unit Information </a>
											</li>
											<li>
												<a href="#tab_contact_info" data-toggle="tab" aria-expanded="false"> Unit Contact Information </a>
											</li>-->														
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tab_super_stockist_info">
												<br/>
                           						<?php 
												if($error!=1)
												{ 
												    if($type_of_inquiry=='sales_manager')
													{
														include('form/sales_manager_form.php');
													}
													else if($type_of_inquiry=='area_sales_manager')
													{
														include('form/area_sales_manager_form.php');
													}

													else if($type_of_inquiry=='dispatch_sales_manager')
													{
														include('form/dispatch_sales_manager_form.php');
													}
													
													else if($type_of_inquiry=='sales_officer')
													{
														include('form/sales_officer_form.php');
													}
													else if($type_of_inquiry=='sales_executive')
													{
														include('form/sales_executive_form.php');
													}
													else if($type_of_inquiry=='service_executive')
													{
														include('form/service_executive_form.php');
													}
														else if($type_of_inquiry=='area_manager')
													{
														include('form/area_manager_form.php');
													}
													
													else
													{
														$error=1;
														$error_msg="Something went wrong with page!! Try Again :("
													 ?>
													<h1 class="text-center">
														<?php echo $error_msg; ?>
														<br>
														<br>
													<a class="btn btn-lg btn-primary">
														<i class="fa fa-refresh"></i>&nbsp; Try Again!!
													</a>
													</h1>
															
															<?php
														}
														
												   }
												   else
												   {
													   ?>
														<h1 class="text-center">
															<?php echo $error_msg; ?>
															<br>
															<br>
														<a class="btn btn-lg btn-primary">
															<i class="fa fa-refresh"></i>&nbsp; Try Again!!
														</a>
														</h1>
														
														<?php
												   }
													
												   ?>
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
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
 <script src="js/jquery.datetimepicker.js"></script>
<script src="assets/global/plugins/jquery.quicksearch.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<!-- <script src="//jonthornton.github.io/jquery-timepicker/jquery.timepicker.js"></script> -->

<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
	// $(".category_id").fSelect();
	$("#type_of_company").fSelect();
	$(".top_category_id").fSelect();
</script>
<script type="text/javascript">
    // image path
    $(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
	// image path
	
	// file path
	$(function(){
		aj.imageHolder($("input[name=file_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
	// file path
	
</script>
<script type="text/javascript">
//-------#numeric field  validation----------//
$("#phone").numeric();
$("#imei").numeric();
$("#zip").numeric();
$("#insentive_percentage").numeric();

$(document).ready(function(){
	$('.timepicker').datetimepicker({
	  datepicker:false,


	  // format: 'H:i',
	  // // format:'h:i',
	  // formatTime:'H:i',

	  format:'h:i a',
	  formatTime:'h:i a',
	  ampm: true
	  // use24hours: true,
      // format: 'H:m'

	}); 
});

 $(document).ready(function(){
	$(".form-control").bind("keyup change",function(){ 
		if($(this).parent().hasClass("has-error")) 
		{
			$(this).parent().find('.help-block').html(""); 
			$(this).parent().removeClass("has-error"); 
		} 
	}); 
});
$('#area_id').multiSelect();
$('#select-all').click(function(){
  $('#area_id').multiSelect('select_all');
  return false;
});
$('#deselect-all').click(function(){
  $('#area_id').multiSelect('deselect_all');
  return false;
}); 
 function check_form(){
 	// alert();
	$(".form-body").children().removeClass("has-error");

	var isValid=true;
	if($("#executive_in_min").val()=="")
    {
        aj.error('executive_in_min','Min. Working Start Time Required!!','add_error');
		isValid= false;       
    }	
	if($("#executive_in_max").val()=="")
    {       
		aj.error('executive_in_max','Max. Working Start Time Required!!','add_error');
		isValid= false;       
    }	
	if($("#executive_out").val()=="")
    {       
		aj.error('executive_out','Working End Time Required!!','add_error');
		isValid= false;       
    }
 	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){		
		vd=aj.error('name',"Please enter Name.","add_error");
		isValid=false;
	}

	if($("#username").val()=="" || $("#username").val().split(" ").join("")==""){		
		vd=aj.error('username',"Please enter User name.","add_error");
		isValid=false;
	}
	if($("#phone").val()=="" || $("#phone").val().split(" ").join("")==""){
		aj.error('phone','Please enter phone number!!','add_error');
		isValid=false;
	}
	// else
	// {
		if($("#phone").val().length!=10){
			aj.error('phone','Please enter valid Phone number!!','add_error');
			isValid=false;
		}
	// }
	<?php
	if($_REQUEST['mode'] == "add")
	{
	?>
	if($("#password").val()=="" || $("#password").val().split(" ").join("")==""){
		aj.error('password','Please enter Password!!','add_error');
		isValid=false;
	}		

	<?php
	}
	?>

	if($("#email").val()!="")
    {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())){

        }else{
           	aj.error('email','Please enter valid email!!','add_error');
        	isValid= false;
        }
    }
	 
	<?php
	if($_REQUEST['type']=='area_sales_manager')
	{
	?>
	// alert("test")
	if($("#sales_manager").val()==null || $("#sales_manager").val()=="" || $("#sales_manager").val().split(" ").join("")=="")
	{		
		vd=aj.error('sales_manager',"Please Select Sales Manager.","add_error");
		isValid=false;
	}
	<?php
	}
	if($_REQUEST['type']=='sales_officer')
	{
	?>
	if($("#sm_sales_manager").val()==null || $("#sm_sales_manager").val()=="" || $("#sm_sales_manager").val().split(" ").join("")==""){		
		vd=aj.error('sm_sales_manager',"Please Select Sales Manager.","add_error");
		isValid=false;
	}
	if($("#asm_area_sm").val()==null || $("#asm_area_sm").val()=="" || $("#asm_area_sm").val().split(" ").join("")==""){		
		vd=aj.error('asm_area_sm',"Please Select Area Sales Manager.","add_error");
		isValid=false;
	}
	<?php
	}
	if($_REQUEST['type']=='sales_executive')
	{
	?>
	if($("#sales_manager").val()==null || $("#sales_manager").val()=="" || $("#sales_manager").val().split(" ").join("")==""){		
		vd=aj.error('sales_manager',"Please Select Sales Manager.","add_error");
		isValid=false;
	}
	if($("#area_sm").val()==null || $("#area_sm").val()=="" || $("#area_sm").val().split(" ").join("")==""){		
		vd=aj.error('area_sm',"Please Select Area Sales Manager.","add_error");
		isValid=false;
	}
	if($("#sales_officer").val()==null || $("#sales_officer").val()=="" || $("#sales_officer").val().split(" ").join("")==""){		
		vd=aj.error('sales_officer',"Please Select Area Sales Manager.","add_error");
		isValid=false;
	}
	<?php
	}
	?>
	if($("#country").val()=="" || $("#country").val().split(" ").join("")==""){		
		vd=aj.error('country',"Please Select Country.","add_error");
		isValid=false;
	} 
 	if($("#state").val()=="" || $("#state").val().split(" ").join("")==""){		
		vd=aj.error('state',"Please Select State.","add_error");
		isValid=false;
	}
	if($("#main_city").val()=="" || $("#main_city").val().split(" ").join("")==""){		
		vd=aj.error('main_city',"Please Select City.","add_error");
		isValid=false;
	}
	// if($("#city").val()=="" || $("#city").val().split(" ").join("")==""){		
	// 	vd=aj.error('city',"Please Select Route.","add_error");
	// 	isValid=false;
	// } 
	/*if($("#zone").val()=="" || $("#zone").val().split(" ").join("")==""){		
		vd=aj.error('zone',"Please Select zone.","add_error");
		isValid=false;
	} */
	if($("#type_of_company").val()=="" || $("#type_of_company").val()==null){		
		toastr.error('type_of_company',"Please Select Company.","add_error");
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
//enter Only Number not('-' OR '.')//
$(document).ready(function() {
$("#phone").keyup(function(event) {
if ( event.keyCode == 46 || event.keyCode == 8 ) {
// let it happen, don't do anything
} else if (/\D/g.test(this.value)) {
	alert("sorry!! Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});
});
//-------------------------------------//

//----#Get Area onchange Class-----------//
function getArea(val,spn,type)
{
		var eid=0;
		if($(spn).val()!=undefined)
		{
			eid=$(spn).val()
		}
        $.ajax({
        type: "POST",
        url: "sales_executive_ajax_function.php",
		beforeSend:function(){
			$("#area_id").html("");
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
		},
        data:{
			mode:"executive_area",
			cid:val,
			eid:eid,
			type:type
		},
        success: function(data){
        $("#area_id").html(data);
        $("#area_id").multiSelect("refresh");
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
        }
    });
}
//----------------------------------------//
/*$("select").select2({
	});*/
$('#area_id').multiSelect({
	  selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Search Area'>",
	  selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Search Area'>",
	  afterInit: function(ms){
		 
		var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
		
    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(){
    this.qs1.cache();
    this.qs2.cache();
  },
  afterDeselect: function(){
    this.qs1.cache();
    this.qs2.cache();
  }
});

var searchName="";
	// used when user change row limit
	function changeDisplayRowCount(numRecords) 
	{
		displayRecords(numRecords, 1);
	}
	// used when user change row limit
	function changeDisplayRowCountContact(numRecords) 
	{
		displayContactRecords(numRecords, 1);
	}
	function displayRecords(numRecords) 
	{
	var searchName 	= ($("#searchName").val()==undefined)?"":$("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	
}
function displayContactRecords(numRecords) 
	{
	var searchName 	= ($("#searchContactName").val()==undefined)?"":$("#searchContactName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results2" ).html("");
	$("#results2" ).load( data_cotact_person_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadContactDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results2").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div2").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
	$("#results2").on( "change", "#numRecords2", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
}
	function loadDataTable()
	{
		$('#datatable_1').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "1%" }, 
				  { "sWidth": "15%" },
				  { "sWidth": "10%" },			
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-sitemap '></i> &nbsp; No Branch Found"},
		});
    }
	function loadContactDataTable()
	{
		$('#datatable_2').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "0.4%" }, 
				  { "sWidth": "10%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },			
				  { "sWidth": "8%" },							  		
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-user-plus '></i> &nbsp; No Contact Found"},
		});
    }
	function searchByName()
	{
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
	}
	function clearSearchByName()
	{
		searchName = "";
		$("#searchName").val("");
		displayRecords(100,1);
	}
	function searchByContactName()
	{
	searchName = $("#searchContactName").val();
	displayContactRecords(100,1);
	return false;
	}
	function clearSearchByContactName()
	{
		searchName = "";
		$("#searchContactName").val("");
		displayContactRecords(100,1);
	}

function callbackState(mode,result){
	if(mode==0){
		$('#state').html('<option value="">Select State</option>');
		$('#state').select2("val","");
		$('#city').html('<option value="">Select City</option>');
		$('#city').select2("val","");
		}
}
function callbackCity(mode,result){
	if(mode==0){
		$('#city').html('<option value="">Select City</option>');
		$('#city').select2("val","");
			}
}
function getAreaClass(spn,type)
{
	 $.ajax({
        type: "POST",
        url: "sales_executive_ajax_function.php",
		beforeSend:function(){
			$("#class_id").html("<option value=''> Select Class </option>");
			$("#class_id").select2("val","");
			$("#area_id").html("");
			$("#area_id").multiSelect("refresh");
		},
        data:'mode=executive_class&eid='+$(spn).select2('val')+"&type="+type,
        success: function(data){
			
			$("#class_id").html(data);
			
        }
    });
}
function getSalesExecutive(spn,val,container,superior_type,executive_type)
{
	$(container).html("<option value=''>Select Executive</option>");
	 $.ajax({
        type: "POST",
        url: "sales_executive_ajax_function.php",
		 data:{
			mode:"under_executive",
			eid:val,
			suprior_type:superior_type,
			executive_type:executive_type	
		},
		beforeSend:function(){
			
			$(container).select2("val","");			
		},
        success: function(data){
			
			$(container).html(data);
			
        }
    });
}
</script>

<script type="text/javascript">
$(document).ready(function(){
    var mode="<?= isset($_REQUEST['mode'])?$_REQUEST['mode']:"";?>";

    if(mode=="add")
    {
		$('#executive_in_min').val("");
		$('#executive_in_max').val("");
		$('#executive_out').val("");
	}
	else{
		$('#executive_in_min_val').val('<?php echo date("H:i",strtotime($executive_in_min)); ?>');
		$('#executive_in_max_val').val('<?php echo date("H:i",strtotime($executive_in_max)); ?>');
		$('#executive_out_val').val('<?php echo date( "H:i",strtotime($executive_out)); ?>'); 
	}

    if (mode == "edit") {
		var state_data = $("#country").val();
		State(state_data);

		var city_data1 = $("#state").val();
		// alert(city_data1);
		// var main_city_data = $("#main_city").val();
		var city_name="<?=$city?>"
		var main_city_name="<?=$main_city?>";
		// get_id(main_city_name+" "+city_name);

		city_data(city_data1,main_city_name); 
		City(main_city_name,city_name);
	}
	if (mode == 'add') 
	{
		State('India');
	}
});
</script>

<script type="text/javascript">
		function State(val) {
			var state1="<?=$state?>"
			$.ajax({
				type: "POST",
				url: "ajax_get_state.php",
				data: 'cid=' + val+"&state_id="+state1,
				success: function(result) {
					// $("#state").html(result);
					$("#state").select2("destroy");
	            	$("#state").html(result);
	           	 	$("#state").select2();
				}
			});
		}

		function city_data(val,city_name="") 
		{
			$.ajax({
				type: "POST",
				url: "ajax_get_main_city.php",
				data: 'sid=' + val + '&city='+city_name,
				success: function(result) {
					$("#main_city").html(result);
					$("#main_city").select2("destroy");
	            	$("#main_city").html(result);
	           	 	$("#main_city").select2(); 
				}
			});
		}
		
		function City(val,city_name="") {

			$.ajax({
				type: "POST",
				url: "ajax_get_city.php",
				data: 'sid=' + val + '&city='+city_name,
				success: function(result) {
			    $("#city").html(result);
			    var class_id = $("#state").find(':selected').attr('data-state_id');
				$("#class_id").val(class_id);
				$("#city").select2("destroy");
	            $("#city").html(result);
	            $("#city").select2();

				}
			});
		}

		function get_id(){
		var area_id = $("#city").find(':selected').attr('data-city_id');
		$("#area_id").val(area_id);
	}
</script>

<script type="text/javascript">
	/*$(".checkall").on('click',function(){
		$(".applicationpagerightsto_view").each(function(){
			$(this).prop('checked',true);
			if($(this).prop("checked") == true){
                alert('1');
            }
            else if($(this).prop("checked") == false){
              alert('0');
            }
		})
	})*/


	

	$(function () {
        // Header Master Checkbox Event
        $(".masterCheck").on("change", function () {
        	var count = $(this).data("count");
            if ($( this ).prop("checked")) {
	        	$( ".row-check"+count ).prop("checked", "checked");
	        	$( ".row-check"+count ).parent().addClass("checked");
            } else {
            	$( ".row-check"+count ).prop("checked", "");
	        	$( ".row-check"+count ).parent().removeClass("checked");
            }
        });

        // Check event on each table row checkbox
        $(".row-check").on("change", function () {
        	var count = $(this).data("count");
            var total_check_boxes = $(".row-check"+count).length;
            var total_checked_boxes = $(".row-check"+count+":checked").length;

            // If all checked manually then check master checkbox
            if (total_check_boxes === total_checked_boxes) {
                $(".masterCheck"+count).prop("checked", "checked");
                $(".masterCheck"+count).parent().addClass("checked");
            }
            else {
                $(".masterCheck"+count).prop("checked", "");
                $(".masterCheck"+count).parent().removeClass("checked");
            }
        });

        // Header Master Checkbox Event
        $(".masterAllCheck").on("change", function () {
        	var count = $(this).data("count");
        	if ($( this ).prop("checked")) {
	        	$( ".masterCheck" ).prop("checked", "checked");
	        	$( ".masterCheck" ).parent().addClass("checked");
            } else {
            	$( ".masterCheck" ).prop("checked", "");
	        	$( ".masterCheck" ).parent().removeClass("checked");
            }
        });

        // Check event on each table row checkbox
        $(".masterCheck").on("change", function () {
        	var count = $(this).data("count");
            var total_check_boxes = $(".masterCheck").length;
            var total_checked_boxes = $(".masterCheck:checked").length;

            // If all checked manually then check master checkbox
            if (total_check_boxes === total_checked_boxes) {
                $(".masterAllCheck").prop("checked", "checked");
                $(".masterAllCheck").parent().addClass("checked");
            }
            else {
                $(".masterAllCheck").prop("checked", "");
                $(".masterAllCheck").parent().removeClass("checked");
            }
        });


    });
</script>

<script type="text/javascript">

	function tConvert (time) {
		const timeStr = time;
		const convertTime = timeStr => {
		   const [time, modifier] = timeStr.split(' ');
		   let [hours, minutes] = time.split(':');
		   if (hours === '12') {
		      hours = '00';
		   }
		   if (modifier === 'pm') {
		      hours = parseInt(hours, 10) + 12;
		   }
		   return `${hours}:${minutes}`;
		};

		$("#executive_in_min_val").val(convertTime(timeStr));

		$('#executive_in_max').val("");
		$('#executive_out').val("");

	}


	function tConvert_max (time) {
		const timeStr = time;
		const convertTime = timeStr => {
		   const [time, modifier] = timeStr.split(' ');
		   let [hours, minutes] = time.split(':');
		   if (hours === '12') {
		      hours = '00';
		   }
		   if (modifier === 'pm') {
		      hours = parseInt(hours, 10) + 12;
		   }
		   return `${hours}:${minutes}`;
		};
		// console.log(convertTime(timeStr));
		$("#executive_in_max_val").val(convertTime(timeStr));
		
		var endtime = $("#executive_in_max_val").val()
		var maxtime = $("#executive_in_min_val").val();

		var executive_in_max = $("#executive_in_max").val()
		
		// console.log(endtime);
		// console.log(maxtime);
		// alert(maxtime);
		  // var endtime = $("#executive_in_max").val();

		if (maxtime >= endtime) {
	  		toastr.error("Max.Working Start Time is Not Greater Working End Time");
	  		$('#executive_in_max').val("");
	  		$('#executive_out').val("");
	  		$("#executive_out").prop('disabled', true);
	  	}
	  	else if(executive_in_max==""){
	  		$('#executive_out').val("");
	  		$("#executive_out").prop('disabled', true);
	  	}
	  	else{
	  		$("#executive_out").prop('disabled', false);	
	  	}

	}

	function tConvert_Out (time) {
		const timeStr = time;
		const convertTime = timeStr => {
		   const [time, modifier] = timeStr.split(' ');
		   let [hours, minutes] = time.split(':');
		   if (hours === '12') {
		      hours = '00';
		   }
		   if (modifier === 'pm') {
		      hours = parseInt(hours, 10) + 12;
		   }
		   return `${hours}:${minutes}`;
		};
		// console.log(convertTime(timeStr));
		$("#executive_out_val").val(convertTime(timeStr));
		
		var endtime = $("#executive_in_max_val").val()
		var outtime = $("#executive_out_val").val()
		

		if (endtime > outtime) {
	  		toastr.error("Working End Time is Not Less Max Working Start  Time");
	  		$('#executive_out_val').val("");
	  	}

	}


	// $('#executive_in_min').on('change', function() {


		
	//   // tConvert();
	  
	// 	  var maxtime =  $('#executive_in_min').val();
	// 	  var endtime = $("#executive_in_max").val();

	// 	  if(maxtime!=""){

	// 		  $('.timepicker_hidden').datetimepicker({
	// 			  datepicker:false,
	// 			  format: 'H:i',
	// 			  formatTime:'H:i',
	// 			  use24hours: true,
	// 			  ampm: false

	// 			}); 
	// 		}

	// 	  // $("#executive_in_min_val").val(maxtime);

	// });

	// $('#executive_in_max').on('change', function() {


	// 	// $('.timepicker_hidden').datetimepicker({
	// 	//   datepicker:false,
	// 	//   format: 'H:i',
	// 	//   formatTime:'H:i',
	// 	//   use24hours: true,

	// 	// }); 
	//   // var maxtime = $(this).val();

	//   // alert(maxtime);
	  
	//   var maxtime =  $('#executive_in_min').val();
	//   var endtime = $("#executive_in_max").val();

	//   // $("#executive_in_min_val").val(maxtime);
	//   // console.log(maxtime);
	//   // console.log(typeof(maxtime));

	//   // console.log(endtime);
	//   // console.log(typeof(endtime));

	//   // if (parseFloat(maxtime) > parseFloat(endtime)) {
	//   	if (maxtime > endtime) {
	//   		toastr.error("Max.Working Start Time is Not Greater Working End Time");
	//   		$('#executive_in_max').val("");
	//   		$("#executive_out").prop('disabled', true);
	//   	}
	//   	else{
	//   		$("#executive_out").prop('disabled', false);	
	//   	}
	// });

	// $('#executive_out').on('change', function() {
	//   // var endtime1 = $(this).val();

	//   var endtime1 = $("#executive_out").val();
	//   var maxtime1 = $("#executive_in_max").val();

	//   if (endtime1 < maxtime1) {
	//   	toastr.error("Working End Time is Not Less Max Working Start  Time");
	//   	$('#executive_out').val("");
	//   }
	// });
</script>

</body>
</html>