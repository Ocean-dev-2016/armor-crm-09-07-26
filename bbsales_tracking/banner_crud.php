<?php
$page_id=574;$page_slug='banner';
	$ctable 	= "promotion";
	$ctable1 	= "banner";
	$main_page 	= "utility";
		$page 		= "manage_banner";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable1."_manage.php","title"=>"Manage Banner"),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit Banner"));
include("connect.php");
		$name		= "";
	$image_path	= "";
	$display_order	= 0;
$class_id       = "";
$area_id        = "";
$area_id        = array();
$class_id        = array();
if(isset($_REQUEST['submit']))
{
		$url	= addslashes(trim($_REQUEST['url']));
		$target	= addslashes(trim($_REQUEST['target']));
	// print_r($_REQUEST); exit;
	// if(isset($_REQUEST['image_path']) && $_REQUEST['image_path']!="")
	// {
	// 	copy(BANNER_T.$_REQUEST['image_path'], BANNER_A.$_REQUEST['image_path']);
	// 	$image_path = $_REQUEST['image_path'];
	// 	unlink(BANNER_T.$_REQUEST['image_path']);
	// 	unset($_REQUEST['image_path']);
	// }
	// echo "<pre>"; print_r($_FILES); exit;

	if (isset($_FILES["image_path"]) ) {
		$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
		$temp = explode(".", $_FILES["image_path"]["name"]);
		 $extension = end($temp);
	 
			$fileName 	= $db->clean($_FILES["image_path"]["name"]);	
			if($fileName!=""){
			$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
			$adate 		= date('Y-m-d H:i:m');
			
			$extension	= end(explode(".", $fileName));		
			if(!in_array($extension,$allowedExts))
			{
				$file_error=true;
			}
								
			$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
			$filePath 	= BANNER_A.$image_path;	
			$_FILES['image_path']['tmp_name'];
			// print_r($filePath); exit;
			move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);
			
			$new_image=true;
			}
			else{
				$image_path="";
			}
	}
	else
	{
		$new_image=false;
		$image_path="";
		
	}
			//$class_id		= addslashes(trim($_FILES['class_id']));
	$area_id=implode(",",$_REQUEST['area_id']);
	$area_id=addslashes($area_id);
	$class_id=implode(",",$_REQUEST['class_id']);
	$class_id=addslashes($class_id);
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
			$display_order	= $db->rp_getDisplayOrder($ctable,"promo_type=1");
			$rows 	= array(
			"url",
			"target",
			"image_path",
			"display_order",
			"promo_type",
			"class_id",
			"area_id",
		);
		$values = array(
			$url,
			$target,
			$image_path,
			$display_order,
			"1",
			$class_id,
			$area_id
		);

		// echo "<pre>"; print_r($rows);
		// echo "<pre>"; print_r($values); exit;
		$db->rp_insert($ctable,$values,$rows,0);
		$db->addSuccessMessage("Banner Inserted successfully!");
		$db->rp_location("banner_manage.php?msg=inserted");
	}
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($_REQUEST['old_image_path']!="" && $image_path!="")
		{
			if(file_exists(BANNER_A.$_REQUEST['old_image_path']))
			{
				unlink(BANNER_A.$_REQUEST['old_image_path']);
			}
		}
		else
		{
			if($image_path=="")
			{
				$image_path = $_REQUEST['old_image_path'];
				if($image_path == "")
				{
						$image_path = "";
				}
			}
		}
			$rows 	= array(
					"url"		=> $url,
				"target"	=> $target,
			"image_path"=> $image_path,
			"class_id"  => $class_id,
				"area_id"	=> $area_id,
		);
			$where	= "id=".$_REQUEST['id'];
		$db->rp_update($ctable,$rows,$where);
		$db->addSuccessMessage("Banner Updated successfully!");
		$db->rp_location("banner_manage.php?msg=updated");
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit")
{
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$ctable_d = mysqli_fetch_array($ctable_r);
	
			$url		= stripslashes($ctable_d['url']);
			$target		= stripslashes($ctable_d['target']);
	$image_path = stripslashes($ctable_d['image_path']);
	$class_id   = stripslashes($ctable_d['class_id']);
	$class_id    = explode(",",$class_id);
		$area_id 	= stripslashes($ctable_d['area_id']);
	// $area_id    = explode(",",$area_id);
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	$check_total=$db->rp_getTotalRecord("promotion","isDelete=0",0);
	if($check_total >1)
	{
		$where = " id='".$_REQUEST['id']."' AND promo_type=1";
		$ctable_r = $db->rp_getData($ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$image_path = stripslashes($ctable_d['image_path']);
		if($image_path!="" && file_exists(BANNER_A.$image_path))
		{
			unlink(BANNER_A.$image_path);
		}
		$db->rp_delete($ctable,"id='".$_REQUEST['id']."'");
		$db->addSuccessMessage("Banner Deleted successfully!");
		$db->rp_location("banner_manage.php?msg=deleted");
	}
	else
	{
		$db->addErrorMessage("At Least One Banner should be in grid");
		$db->rp_location("banner_manage.php?msg=inserted");
	}
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
		<link href="assets/css/demo.html5imageupload.css?v1.3" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
	</head>
	<body class="page-md">
		<?php include("header.php"); ?>
		<div class="page-container">
			<div class="page-head bg-grey">
				<div class="container">
					<div class="page-title">
						<h1><a href="<?php echo $ctable1."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
					</div>
				</div>
			</div>
			<div class="page-content">
				<div class="container">
					<?php $db->getMessageBlock(); ?>
					<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6 ">
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-6"> 
													<div class="form-group">
														<input data-image="<?php echo ($image_path!="" && file_exists(BANNER_A.$image_path))?BANNER_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
													</div>

													<!-- <div class="form-group">
														<label for="image_path">Image  <input type="hidden" name="filename" id="filename" class="form-control" /><code>*</code></label>
														<small>minimum image size 370 x 260</small>
														<br />
														<div class="dropzone" data-width="370" data-height="260" data-ghost="false" data-originalsize="false" data-url="crop_banner.php" style="width: 370;height:260px;">
															<input type="file" id="image_path" name="image_path" data>
														</div>
														<input type="hidden" name="old_image_path" value="<?php echo $image_path; ?>" />
														<?php
														if($image_path!="" && file_exists(BANNER_A.$image_path))
														{
														?>
														<br />
														<img src="<?php echo BANNER_A.$image_path;?>" width="260">
														<?php
														}
														?>
													</div> -->
												</div>
											</div>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="class_id">Class <code>*</code></label>
														<select class="form-control" name="class_id[]" multiple="multiple"  id="class_id" onChange="getArea(this.value,'<?php echo $area_id; ?>');" >
															<option value="">Select Class Type</option>
															<?php
															$class_list_d=$db->rp_getData('class',"*","1=1 AND isDelete=0 AND isActive=1","",0);
															while($class_list_r=mysqli_fetch_assoc($class_list_d))
															{
															?>
															<option <?php echo (in_array($class_list_r['id'],$class_id))?"selected":""; ?> value="<?php echo $class_list_r['id']; ?>" ><?php echo $class_list_r['name']; ?></option>
															<?php
															}
															?>
														</select>
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="area_id">Area<code>*</code></label>
														<div class="abc">
															<select class="form-control area_id" name="area_id[]" id="area_id" multiple="multiple">
																<option value="">Select Area</option>
															</select>
														</div>
														<p class="help-block"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="form-actions">
											<button type="submit" name="submit" class="btn green">Submit</button>
											<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable1; ?>_manage.php'">Back</button>
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
		<script src="assets/js/banner_html5imageupload.js?v1.3.4"></script>
		<script type="text/javascript" src="js/fSelect.js"></script>
		<script type="text/javascript">
		var mode="<?= $_REQUEST['mode']?>";
		function getArea(val,area_id="")
		{
		$.ajax({
		type: "POST",
		url: "ajax_get_area.php",
		data:'class_id='+val+'&area_id='+area_id,
		success: function(data){
		$(".abc").html(data);
		$("#area_id").fSelect();
		}
		});
		}
		if(mode == "edit")
		{
		// getArea('<?= $class_id?>','<?=$area_id?>');
		}
		</script>
		<script>
			$('.dropzone').html5imageupload({
				onAfterProcessImage: function() {
					var imgName = $('#filename').val($(this.element).data('imageFileName'));
				},
				onAfterCancel: function() {
					$('#filename').val('');
				}
			});
		</script>
		<script type="text/javascript">
		var image_path="<?=$image_path?>";
		function check_form(){
		$(".form-body").children().removeClass("has-error");
		var isValid=true;
		// if($("#image_path").val()=="" || $("#image_path").val().split(" ").join("")==""){
		
		// 	vd=aj.error('image_path',"Please Select Leave Type.","add_error");
		// 	isValid=false;
		// }
		if($("#image_path").val()=="" || $("#image_path").val().split(" ").join("")=="")
		{
		if(image_path == "")
		{
		alert("Please upload Banner Image.");
		$("#image_path").focus();
		return false;
		}
		else
		{
		}
		}
		if($("#class_id").val()=="" || $("#class_id").val().split(" ").join("")==""){
		
		vd=aj.error('class_id',"Please Select Class.","add_error");
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
		/*if($("#mode").val()=="add" && $("#image_path").val()==""){
		alert("Please upload slideshow image.");
		$("#image_path").focus();
		return false;
		}*/
		}
		</script>
		<script type="text/javascript">
			$("#area_id").fSelect();
			$("#class_id").fSelect();
			
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