<?php
$page_id=580;$page_slug='price_list_master';
$ctable 	= "push_notification";
$ctable1 	= "Push Notification";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"push_notification_manage.php","title"=>"Manage ".$ctable1),array("link"=>"push_notification_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/push_notification.class.php");
$objPushNotification= new PushNotification();
$type			= "";
$title			= "";
$descr			= "";
$default_sound	= "";

if(isset($_REQUEST['submit'])){
	
	$detail['type']			  = $db->clean($_REQUEST['type']);
	$detail['title']		  = $db->clean($_REQUEST['title']);
	$detail['descr']		  = $db->clean($_REQUEST['descr']);
	$detail['default_sound']  = $db->clean($_REQUEST['default_sound']);
	$detail['image_path']     = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path'] = $db->clean($_REQUEST['old_image_path']);
	$detail['isDelete']		  = 0;
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objPushNotification->InsertNotification($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("push_notification_manage.php?msg=inserted");
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
		$reply=$objPushNotification->UpdateNotification($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("push_notification_manage.php?msg=updated");
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
		$reply=$objPushNotification->GetEditDataNotification($detail);
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
		$reply=$objPushNotification->DeleteNotification($detail);
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
	$db->rp_location("push_notification_manage.php?msg=updated");
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "push_notification_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="type">Type <code>*</code></label>
                                    		<select class="form-control edited autofocus" id="type" name="type" onChange="getItemType(this.value)" data-validation="required" data-validation-error-msg="Please Enter Type">
                    							<option value="">Select Type</option>	 
                    							<option data-type="general" <?=($type==4)?"selected":""?> value="4">General</option>
                    							<option data-type="list" <?=($type==5)?"selected":""?>  value="5">List</option>
                    							<option data-type="detail" <?=($type==6)?"selected":""?>  value="6">Detail</option>
                    						</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
								<div class="row hidden" id="itemTypeRow">
                                	<div class="col-md-6">
                            			<label for="name">Item Type <code>*</code></label>
                                		<div class="form-group">
                                			<select class="form-control edited" id="item_type" name="item_type" style="width: 100%" onchange="getItemName(this.value)" data-validation="required" data-validation-error-msg="Please Enter Item Type">
                								<option value="">Select Item Type</option>	 
                								<option <?=($item_type==1)?"selected":""?> value="1">B2C</option>
                								<option <?=($item_type==2)?"selected":""?>  value="2">Shopping</option>
                								<option <?=($item_type==3)?"selected":""?>  value="3">B2B</option>
                							</select>
                                		</div>
                                	</div>
                                </div>
                                <div class="row hidden" id="itemNameRow">
                                	<div class="col-md-6">
                                		<div class="form-group">
                                			<label for="name">Item Name <code>*</code></label>
                                			<input class="form-control search_item_data" value="<?php echo $item_name; ?>" name="item_name" id="item_name" type="text" data-validation="required" data-validation-error-msg="Please Enter Item Name">
                                			<input type="hidden" id="test_data">
                                		</div>
                                	</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="name">Title <code>*</code></label>
                                            <input class="form-control " value="<?php echo $title; ?>" name="title" id="title" type="text" data-validation="required" data-validation-error-msg="Please Enter Title">
                                        </div>
                                    </div>                        
                                </div>
								<div class="row">
                                	<div class="col-md-6">
                						<div class="form-group ">
                							<label for="descr">Description</label>
                							<textarea class="form-control" name="descr" rows="5" id="descr" type="text"  ><?php echo $descr; ?></textarea>
                						</div>
                					</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <input data-image="<?php echo ($image_path!="" && file_exists(NOTIFICATION_A.$image_path))?NOTIFICATION_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
                                        </div>                            
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                    	<label for="first_name">Notification Sound</label><br/>
                                        <div class="form-group ">
                                            <input checked type="radio"  id="default_sound" name="default_sound" value="1" <?php echo ($default_sound == '1') ?  "checked" : "" ;  ?>>Sound
                                            <input type="radio" id="default_sound" name="default_sound" value="0" <?php echo ($default_sound == '0') ?  "checked" : "" ;  ?>>Not Sound
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

<script type="text/javascript">

$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	if($("#title").val()=="" || $("#title").val().split(" ").join("")==""){
			
		vd=aj.error('title',"Please Enter Pricelist title.","add_error");
		isValid=false;
	}
	if($("#type").val()=="" || $("#type").val().split(" ").join("")==""){
			
		vd=aj.error('type',"Please Enter Notification type.","add_error");
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
</script>

</body>
</html>