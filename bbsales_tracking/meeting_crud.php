<?php
$page_id=585;$page_slug='meeting_master';
$ctable 	= "meeting";
$ctable1 	= "Meeting";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"meeting_manage.php","title"=>"Manage ".$ctable1),array("link"=>"meeting_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.meeting.php");
$objMeeting= new Meeting();
$meeting_type			= "";
$customer_id			= "";
$customer_type			= "";
/*$meeting_host			= "";
$meeting_host_name		= "";*/
$meeting_date			= "";
$meeting_venue			= "";
$expence				= "";
$gift_details			= "";
//$title			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	$detail['meeting_venue']			= html_entity_decode($db->clean($_REQUEST['meeting_venue']));
	$detail['gift_details']				= html_entity_decode($db->clean($_REQUEST['gift_details']));
	$detail['expence']					= html_entity_decode($db->clean($_REQUEST['expence']));
	$detail['meeting_date']				= $db->clean($_REQUEST['meeting_date']);
	$detail['customer_id']				= $db->clean($_REQUEST['customer_id']);
	$detail['customer_type']				= $db->clean($_REQUEST['customer_type']);
	$detail['dealer_id']				= 0;
	/*$detail['meeting_host']			= $db->clean($_REQUEST['meeting_host']);
	$detail['meeting_host_name']			= $db->clean($_REQUEST['meeting_host_name']);*/
	$detail['meeting_type']			= $db->clean($_REQUEST['meeting_type']);
	//$detail['title']			= $db->clean($_REQUEST['title']);
	$detail['sales_id']			= "";

	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		// print_r($_FILES);exit();
		$reply=$objMeeting->InsertMeeting($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("meeting_manage.php?msg=inserted");
		}else{
				 $db->addErrorMessage($reply['ack_msg']);
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objMeeting->UpdateMeeting($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("meeting_manage.php?msg=updated");
		}
		else{
				 $db->addErrorMessage($reply['ack_msg']);
			} 
		
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $db->rp_getData($ctable,"*",$where);
		$detail['id']=$_REQUEST['id'];	
		$reply=$objMeeting->GetEditDataMeeting($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
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
	$db->rp_location("meeting_manage.php?msg=updated");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$rows 	= array(
				"isDelete"	=> "1"
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location("meeting_manage.php?msg=updated");
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "meeting_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
		<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-12">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label>Customer Type <code>*</code></label>
										<select class="form-control" id="customer_type" name="customer_type" onChange="getDealerDistributor(this.value);">
											<option value="">Customer Type </option>
											<?php
											$type_r = $db->rp_getData("customer_type", "id,name", "isDelete=0");
											if ($type_r) {
												while ($type_d = mysqli_fetch_assoc($type_r)) {
											?>
													<option value="<?= $type_d['id'] ?>" <?= ($type_d['id'] == $customer_type) ? "selected" : ""; ?>><?= $type_d['name']; ?></option>
													<?php
												}
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Select Customer<code>*</code></label>
										<select class="form-control" id="customer_id" name="customer_id">
											<option value="">Select Customer</option>
											<?php 
												if ($_REQUEST['mode']=='edit')
												{
													$dd_r = $db->rp_getData("executive","*","type_of_executive='".$customer_type."' AND isDelete=0 AND isActive=1","",0);
													while($dd_d = mysqli_fetch_assoc($dd_r))
													{
														?>
															<option <?= ($customer_id==$dd_d['id'])?"selected":""; ?> value="<?php echo $dd_d['id']; ?>"><?php echo $dd_d['company_name']." - ".$dd_d['cname']; ?></option>
														<?php
													}
												}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Meeting Type <code>*</code></label>
										<select class="form-control" id="meeting_type" name="meeting_type" >
											<option value="">Select Meeting Type</option>
											<?php
											$meeting_r=$db->rp_getData("meeting_type","name,slug","isDelete=0 AND isActive=1");
											if($meeting_r)
											{
												while($meeting_d=mysqli_fetch_assoc($meeting_r))
												{													
											?>
											<option <?= ($meeting_type==$meeting_d['slug'])?"selected":""; ?> value="<?= $meeting_d['slug']; ?>" data-host-name="<?= $meeting_d['name']; ?>"><?= $meeting_d['name'];?></option>
													<?php
												}
											}
											?>											
										</select>
										<p class="help-block"></p>
									</div>
								</div>

								<!-- <div class="col-md-4">
									<div class="form-group">
										<label>Meeting Host <code>*</code></label>
										<input type="hidden" name="meeting_host_name" id="meeting_host_name" value="<?= $meeting_host_name; ?>">
										<select class="form-control" id="meeting_host" name="meeting_host" onchange="getHostName(this.value);">
											<option value="">Select Meeting Host</option>
											<?php
											$host_r=$db->rp_getData("executive","cname,id","isDelete=0 AND isActive=1");
											if($host_r)
											{
												while($host_d=mysqli_fetch_assoc($host_r))
												{
													?>
													<option <?= ($meeting_host==$host_d['id'])?"selected":""; ?> value="<?= $host_d['id']; ?>" data-host-name="<?= $host_d['cname']; ?>"><?= $host_d['cname'];?></option>
													<?php
												}
											}
											?>
										</select>
											<p class="help-block"></p>
									</div>
								</div> -->
								<!-- <div class="col-md-4">
									<div class="form-group">
										<label>Title <code>*</code></label>
										<input type="text" class="form-control" name="title" id="title" value="<?php echo $title; ?>" >
										<p class="help-block"></p>
									</div>
								</div> -->
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label>Meeting Date & Time<code>*</code></label>
										<input type="text" name="meeting_date" id="meeting_date" value="<?= $meeting_date; ?>" class="form-control" readonly>	
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Meeting Address <code>*</code></label>
										<input type="text" class="form-control" name="meeting_venue" id="meeting_venue" value="<?php echo $meeting_venue; ?>" >
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Gift Details <code>*</code></label>
										<input type="text" class="form-control" name="gift_details" id="gift_details" value="<?php echo $gift_details; ?>" >
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Expence <code>*</code></label>
										<input type="text" class="form-control" name="expence" id="expence" value="<?php echo $expence; ?>" >
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input data-image="<?php echo ($image_path!="" && file_exists(MEETING_A.$image_path))?MEETING_A.$image_path:"";?>" type="file" name="image_path[]" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" multiple >
									</div>
								</div>
							</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
$("#expence").numeric();
$('#meeting_date').datetimepicker({datetimepicker: true, autoclose: true });
function getHostName(hid)
{
	var cname=$("#meeting_host").find("option:selected").data("host-name");
	$("#meeting_host_name").val(cname);
}
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	if($("#meeting_type").val()=="" || $("#meeting_type").val().split(" ").join("")==""){	
		vd=aj.error('meeting_type',"Please Select Meeting Type","add_error");
		isValid=false;
	}
	if($("#customer_type").val()=="" || $("#customer_type").val().split(" ").join("")==""){	
		vd=aj.error('customer_type',"Please Select Customer Type","add_error");
		isValid=false;
	}
	if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")==""){	
		vd=aj.error('customer_id',"Please Select Customer","add_error");
		isValid=false;
	}
	// if($("#meeting_host").val()=="" || $("#meeting_host").val().split(" ").join("")==""){	
	// 	vd=aj.error('meeting_host',"Please Select Meeting Host","add_error");
	// 	isValid=false;
	// }
	// if($("#title").val()=="" || $("#title").val().split(" ").join("")==""){	
	// 	vd=aj.error('title',"Please Enter Title.","add_error");
	// 	isValid=false;
	// }
	if($("#meeting_date").val()=="" || $("#meeting_date").val().split(" ").join("")==""){	
		vd=aj.error('meeting_date',"Please Select Meeting Date & Time","add_error");
		isValid=false;
	}
	if($("#meeting_venue").val()=="" || $("#meeting_venue").val().split(" ").join("")==""){	
		vd=aj.error('meeting_venue',"Please Select Meeting Venue","add_error");
		isValid=false;
	}
	if($("#expence").val()=="" || $("#expence").val().split(" ").join("")==""){	
		vd=aj.error('expence',"Please Enter Expence","add_error");
		isValid=false;
	}
	if($("#gift_details").val()=="" || $("#gift_details").val().split(" ").join("")==""){
		vd=aj.error('gift_details',"Please Enter Gift Details","add_error");
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
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[id=image_path]"),"","",
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
</script>

<script type="text/javascript">
// var mode = '<?php echo $_REQUEST['mode']; ?>';
// if(mode=='edit')
// {
// 	var type = $("#customer_type").val();
// 	getDealerDistributor(type);
// }
function getDealerDistributor(val)
{	
	$.ajax({
    type: "POST",
    url: "find_customer.php",
    data:'type_of_executive='+val,
	    success: function(data){
		    $("#customer_id").html(data);
		}
    });
}
</script>

</body>
</html>