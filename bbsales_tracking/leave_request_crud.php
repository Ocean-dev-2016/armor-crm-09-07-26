<?php
$page_id=594;$page_slug='leave_request';
$ctable 	= "leave_request";
$ctable1 	= "Leave Request";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"leave_request_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.leave_request.php");
$objLeave= new LeaveRequest();
$leave_type			 = "";
$sales_executive_id	 = "";
$leave_details       = "";
$latitude            = "";
$longitude           = "";
$file_path           ="";
$user_id             =($_REQUEST['id'])?$_REQUEST['id']:$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];

if(isset($_REQUEST['submit'])){
	// echo "<pre>";
	// print_r($_REQUEST);die;
	$detail['leave_type']	        = $db->clean($_REQUEST['leave_type']);
	$detail['user_id']              = $user_id;
	$detail['sales_executive_id']	= $db->clean($_REQUEST['sales_executive_id']);
	$detail['reason']	            = $db->clean($_REQUEST['leave_details']);
	$detail['latitude']	            = $db->clean($_REQUEST['latitude']);
	$detail['longitude']	        = $db->clean($_REQUEST['longitude']);
	$detail['image_path']   	    = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']       = $db->clean($_REQUEST['old_image_path']);
	$detail['start_date']           = isset($_REQUEST['start_date'])?date("Y-m-d", strtotime($_REQUEST['start_date'])) : "";
	$detail['start_time']           = isset($_REQUEST['start_time']) && $_REQUEST['start_time'] != ""?date("H:i a", strtotime($_REQUEST['start_time'])) : "";
	$detail['end_date']             = isset($_REQUEST['end_date'])?date("Y-m-d", strtotime($_REQUEST['end_date'])) : "";
	$detail['end_time']             = isset($_REQUEST['end_time']) && $_REQUEST['end_time'] != "" ?date("H:i a", strtotime($_REQUEST['end_time'])) : "";
	$detail['leave_category']	        = $db->clean($_REQUEST['leave_category']);
	$detail['isDelete']		        = 0;
	$detail['status']		        = 0;
	$detail['entry_flag']=1;
	$detail['update_entry_flag']=1;
	

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objLeave->InsertLeave($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
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
		$reply=$objLeave->UpdateLeave($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
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
		$reply=$objLeave->GetEditDataLeaveType($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
    if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objLeave->DeleteLeave($detail);
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
<!-- <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/> -->
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/redmond/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
								<div class="col-md-4">
									<div class="form-group">
										<label>Leave Type <code>*</code></label>
										<select class="form-control" id="leave_type" name="leave_type">
											<option value="">Select Leave Type</option>
											<?php 
											$leave_type_r = $db->rp_getData("leave_type","*","isDelete=0","",0);
											while($leave_type_d = mysqli_fetch_assoc($leave_type_r))
											{
											?>
											<option <?= ($leave_type==$leave_type_d['id'])?"selected":""; ?> value="<?php echo $leave_type_d['id']; ?>"><?php echo $leave_type_d['name']; ?></option>
											<?php
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Select Sales Person<code>*</code></label>
										<?php
											if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{
										?>
										<select class="form-control" id="sales_executive_id" name="sales_executive_id">
											<option value="">Select Sales Person</option>
											<?php 
											$sales_executive_r = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1 AND type!='service_engineer'","",0);
											while($sales_executive_d = mysqli_fetch_assoc($sales_executive_r))
											{
											?>
											<option <?=($sales_executive_d['id']==$_SESSION[SITE_SESS.'REFERANCE_ID'])?"selected":"";?> <?= ($sales_executive_id==$sales_executive_d['id'])?"selected":""; ?> value="<?php echo $sales_executive_d['id']; ?>"><?php echo $sales_executive_d['name']; ?></option>
											<?php
											}
											?>
										</select>
										<?php
											}
											else
											{
										?>
											<input type="hidden" id="sales_executive_id" name="sales_executive_id" value="<?=$_SESSION[SITE_SESS.'REFERANCE_ID']?>">
											<input type="text" readonly="" class="form-control" value="<?=$db->rp_getValue("sales_executive","name","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'","",0)?>">
										<?php
											}
										?>
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Leave Details</label><code>*</code>
										<textarea type="text" class="form-control" name="leave_details" id="leave_details" value="<?php echo $leave_details; ?>"><?php echo $leave_details;?></textarea>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Latitude</label>
										<input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude; ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label>Longitude</label>
										<input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude; ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group ">
                                            <!--<input data-image="<?php echo ($image_path!="" && file_exists(LEAVE_A.$image_path))?LEAVE_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >-->
                                            
                                        <input data-image="<?php echo ($image_path!="" && file_exists(LEAVE_A.$image_path))?LEAVE_A.$image_path:"";?>" type="file" name="image_path[]" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" multiple >
                                    </div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label>From Date<code>*</code></label>
										<input type="text" name="start_date" id="start_date" value="<?= $start_date; ?>" class="form-control" readonly>	
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label>From Time</label>
										<input type="time" name="start_time" id="start_time" value="" class="form-control">	
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>To Date<code>*</code></label>
										<input type="text" name="end_date" id="end_date" value="<?= $end_date; ?>" class="form-control" readonly>	
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label>To Time</label>
										<input type="time" name="end_time" id="end_time" value="" class="form-control">	
										<p cass="help-block"></p>
									</div>
								</div>

								
                            </div>
                            <div class="row">
                           <div class="col-md-3">
								<div class="form-group">
								<label class="test">Leave Category<code>*</code></label>
								<select class="form-control" name="leave_category" id="leave_category">
									<option value="">Select Leave Category</option>
									<option value="1" <?=($leave_category == "1")?"selected":"";?>><?php echo "First Half"; ?></option>
									<option value="2" <?=($leave_category == "2")?"selected":"";?>><?php echo "Second Half"; ?></option>
									<option value="3" <?=($leave_category == "3")?"selected":"";?>><?php echo "Full Day"; ?></option>
								</select>      
								<p class="help-block"></p>
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
<!-- <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>

<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
	// $('#start_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy',"setDate": new Date(), });
	// $("#start_date").datepicker("setDate", new Date());

	$("#start_date").datepicker({
	  minDate: 0,

	  onSelect: function(date) {
	    $("#end_date").datepicker('option', 'minDate', date);
	  }
	});

	$("#end_date").datepicker({});

	
</script>
<!-- <script type="text/javascript">
	$('#start_time').timepicker({timepicker: true, autoclose: true });
	$('#end_time').timepicker({timepicker: true, autoclose: true });
</script> -->
<script type="text/javascript">

$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
// $("#start_date, #end_date").datepicker();

// $("#end_date").change(function () {
//     var startDate = document.getElementById("start_date").value;
//     var endDate = document.getElementById("end_date").value;
 
//     if ((Date.parse(endDate) <= Date.parse(startDate))) {
//         alert("End date should be greater than Start date");
//         document.getElementById("end_date").value = "";
//     }
// });

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	if($("#leave_type").val()=="" || $("#leave_type").val().split(" ").join("")==""){
			
		vd=aj.error('leave_type',"Please Select Leave Type.","add_error");
		isValid=false;
	}
	if($("#leave_category").val()=="" || $("#leave_category").val().split(" ").join("")==""){
			
		vd=aj.error('leave_category',"Please Select Leave Category.","add_error");
		isValid=false;
	}
	if($("#leave_details").val()=="" || $("#leave_details").val().split(" ").join("")==""){
			
		vd=aj.error('leave_details',"Please Select Leave Details.","add_error");
		isValid=false;
	}
	
	if($("#sales_executive_id").val()=="" || $("#sales_executive_id").val().split(" ").join("")==""){
			
		vd=aj.error('sales_executive_id',"Please Select Sales Officer.","add_error");
		isValid=false;
	}
	
	if($("#start_date").val()=="" || $("#start_date").val().split(" ").join("")==""){
			
		vd=aj.error('start_date',"Please Select Date.","add_error");
		isValid=false;
	}
	
	// if($("#start_time").val()=="" || $("#start_time").val().split(" ").join("")==""){
			
	// 	vd=aj.error('start_time',"Please Select Time.","add_error");
	// 	isValid=false;
	// }

	if($("#end_date").val()=="" || $("#end_date").val().split(" ").join("")==""){
			
		vd=aj.error('end_date',"Please Select Date.","add_error");
		isValid=false;
	}
	
	// if($("#start_date").val() > $("#end_date")){
	// 		toastr.error("File may be corrupted or missing. Try again!!");
	// }

	// if($("#end_time").val()=="" || $("#end_time").val().split(" ").join("")==""){
			
	// 	vd=aj.error('end_time',"Please Select Time.","add_error");
	// 	isValid=false;
	// }
	
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
	$("#add_item").click(function(){
		var sales_executive_id = $("#sales_executive_id").val();
		var start_date	=$('#start_date').val();
		var start_time 	= $("#start_time").val();
		var end_date    = $("#end_date").val();
		var end_time 	= $("#end_time").val();

		if(sales_executive_id!=0 && start_date!='' && start_time!='' &&  end_date!=""  && end_time!="")
		{
			createItem(sales_executive_id,start_date,start_time,end_date,end_time);
		}
		else
		{
			toastr.error("Please Select Sales Officer,Date And Time");
		}
	})

	function createItem(sales_executive_id,start_date,start_time,end_date,end_time)
	{
		// var sd=start_date.replace(" ","");
		//  sd=sd.replace(":","");
		// var st=start_time.replace(" ","");
		//  st=st.replace(":","");
		// var et=end_time.replace(" ","");
		//  et=et.replace(":","");
		
		// var duplicate=$("#po_items_list").find("tbody").find("tr.leave_"+sd+st+et).length;
		// if(duplicate!=1)
		// {
			// var timefrom = new Date();
			// temp = start_time.val().split(":");
			// timefrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			// timefrom.setMinutes(parseInt(temp[1]));

			// var timeto = new Date();
			// temp = $('#timeto').val().split(":");
			// timeto.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			// timeto.setMinutes(parseInt(temp[1]));

			//if (timeto < timefrom) 
			    // alert('start time should be smaller');
			$.ajax({
				url:"leave_ajax_function.php",
				type:"POST",
				data:{
					sales_executive_id:sales_executive_id,
					start_date:start_date,
					start_time:start_time,
					end_date:end_date,
					end_time:end_time,
					m:"create_item",
					
				},
				beforeSend:function(){
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success:function(result){
					if(result!="")
					{
						var html=result;
						$("#po_items_list").find('tbody').append(html);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
						// $("#start_date").val("");
						// $("#start_time").val("");
						// $("#end_time").val("");
					}
				},
				error:function(){
						toastr.error("We could not process right now try again!!","Error");
					}
				})
		// }
		// else
		// {
		// 	toastr.error("Record already exist Please Remove First to add");
		// }
	}

	function maintainDatatable()
	{
		if($("#po_items_list").find("tbody").find("tr").length>=1)
		{
			$(".no-item").hide();
		}
		else
		{
			$(".no-item").show();
		}
	}

 $(document).ready(function(){
 $("#po_items_list").on('click','.delete',function(){	
       $(this).closest('tr').remove();
	   // recalculateFinalValues();
     });

});
</script>

</body>
</html>