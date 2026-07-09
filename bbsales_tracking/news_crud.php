<?php

$page_id=579;$page_slug='news';

$ctable 	= "news";

$ctable1 	= "news";

$main_page 	= "utility";

$page 		= "manage_news";

$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;

$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"manage_news.php","title"=>"Manage News"),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit News"));

include("connect.php");



$name			= "";

$image_path		= "";

$display_order	= 0;





if(isset($_REQUEST['submit'])){

// var_dump($_SESSION);exit;
	// print_r($_FILES);exit;

	$title		= trim($_REQUEST['title']);

	$description		= addslashes(trim($_REQUEST['description']));

	/*if(isset($_SESSION['image_path']) && $_SESSION['image_path']!=""){

		copy(NEWS_T.$_SESSION['image_path'], NEWS_A.$_SESSION['image_path']);

		$image_path = $_SESSION['image_path'];

		unlink(NEWS_T.$_SESSION['image_path']);

		unset($_SESSION['image_path']);

	}*/

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){

		//echo $_FILES["image_path"]["name"];exit();

		$display_order	= $db->rp_getDisplayOrder($ctable,"isDelete=0");
		if (isset($_FILES["image_path"]) && $_FILES["image_path"]["size"] >= 1049576) 
		{

			$db->addErrorMessage("Your selected file exceeds the size limit of Max. 1 MB");	
			$db->rp_location($ctable."_crud.php?mode=add&msg=inserted");
		}
		else
		{
			if ($_FILES["image_path"]) 
			{
				// echo "hello";exit();
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $_FILES["image_path"]["name"]);
				 $extension = end($temp);
			 
					$fileName 	= $_FILES["image_path"]["name"];	
					if($fileName!=""){
						$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));		
						if(!in_array($extension,$allowedExts))
						{
							$file_error=true;
						}
											
						$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= "../".NEWS.$image_path;
						// echo $filePath;die;	
						// $_FILES['image_path']['tmp_name'];
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
		}

		$rows 	= array(

					"title",
					"description",
					"image_path",
					"display_order",

				);

		$values = array(

					$title,
					$description,
					$image_path,
					$display_order,

				);

		$inserted_id = $db->rp_insert($ctable,$values,$rows,0);
		
		/*Notification code*/
			require_once("../include/push_notification.class.php");
			$objPushNotification= new PushNotification();
			$user_id = $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
			$notification_title="Add New News";
			$notification_description = "New News ".$title." Add By Admin.";
			$notification_type="1";
			$type_slug="";

			$rows 	= array("user_id","referance_id","referance_type","notification_title","notification_description","notification_type","type_slug");
		    $values = array($user_id,$inserted_id,"News",$notification_title,$notification_description,$notification_type,$type_slug);
		    $insert = $db->rp_insert("notification",$values,$rows,0);
		    
		    $image_path = $db->rp_getValue('news','image_path',"id='".$inserted_id."'");
		    if($image_path!="")
			{					
				 $img=SITEURL.NEWS_A.$image_path;
			}
			else
			{
				$img="";
			}

		    $msg = array(
				"type"		     => 'News',
				"title"		     => $notification_title,
				"description"    => $notification_description,
				"user_id"        => $user_id,
				"reference_id"   => $inserted_id,
				"item_id"        => $inserted_id,
				"reference_type" => 'News',
				"image_path"	 =>	$img,
			);

		    //$where="refreshToken!='' AND id='".$user_id."'";
		    $where="refreshToken!=''";
			$refreshTokens[]=$db->rp_getValue("sales_executive","refreshToken",$where,0);
			$result=$objPushNotification->send_notification1($msg,$refreshTokens,1);
		/*Notification code*/
		$db->addSuccessMessage("News Inserted successfully!");
		$db->rp_location("manage_news.php?msg=inserted");

		

		

	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){

		

		/*if($_REQUEST['old_image_path']!="" && $image_path!=""){

			if(file_exists(NEWS_A.$_REQUEST['old_image_path'])){

				unlink(NEWS_A.$_REQUEST['old_image_path']);

			}

		}else{

			if($image_path==""){

				$image_path = $_REQUEST['old_image_path'];

				if($image_path == ""){

					$image_path = "";	

				}

			}

		}*/


		if (isset($_FILES["image_path"]) && $_FILES["image_path"]["size"] >= 1049576) 
		{

			$db->addErrorMessage("Your selected file exceeds the size limit of Max. 1 MB");	
			$db->rp_location($ctable."_crud.php?mode=edit&msg=inserted");
		}
		else
		{
			if (isset($_FILES["image_path"]) && $_FILES["image_path"]['size'] > 0) 
			{
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $_FILES["image_path"]["name"]);
				 $extension = end($temp);
			 
					$fileName 	= $_FILES["image_path"]["name"];	
					if($fileName!=""){
						$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));		
						if(!in_array($extension,$allowedExts))
						{
							$file_error=true;
						}
											
						$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= "../".NEWS.$image_path;
						// echo $filePath;die;	
						// $_FILES['image_path']['tmp_name'];
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
				$image_path = $_REQUEST['old_image_path'];
			}
		}

		$rows 	= array(

					"title"		=> $title,
					"description"	=> $description,
					"image_path"=> $image_path,

				);

		$where	= "id=".$_REQUEST['id'];

		$db->rp_update($ctable,$rows,$where,0);
			$db->addSuccessMessage("News Updated successfully!");
		$db->rp_location("manage_news.php?msg=updated");

		

	}

}



if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){

	$where = " id='".$_REQUEST['id']."' AND isDelete=0";

	$ctable_r = $db->rp_getData($ctable,"*",$where);

	$ctable_d = mysqli_fetch_array($ctable_r);

	

	$title		= stripslashes($ctable_d['title']);

	$description		= stripslashes($ctable_d['description']);

	$image_path = stripslashes($ctable_d['image_path']);
	//echo $image_path;

}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){

	$where = " id='".$_REQUEST['id']."' AND promo_type=1";

	$ctable_r = $db->rp_getData($ctable,"*",$where);

	$ctable_d = mysqli_fetch_array($ctable_r);

	$image_path = stripslashes($ctable_d['image_path']);

	if($image_path!="" && file_exists(NEWS_A.$image_path)){

		unlink(NEWS_A.$image_path);

	}
        $rows 	= array(
    		    "isDelete"	=> "1"
    	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->addSuccessMessage("News Deleted successfully!");
	$db->rp_location("manage_news.php?msg=deleted");

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

</head>

<body class="page-md">

<?php include("header.php"); ?>

<div class="page-container">

	<div class="page-head bg-grey">

		<div class="container">

			<div class="page-title">

				<h1><a href="manage_news.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>

				

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

									<div class="col-md-12">

										<div class="form-group">

											<label>Title <code>*</code></label>

											<input type="text" name="title" id="title" value="<?= $title; ?>" class="form-control" >

										</div>

									</div>								

									<div class="col-md-12">

										<div class="form-group">

											<label>Description <code>*</code></label>

											<textarea class="form-control" id="description" name="description" rows="5" style="resize: vertical;"><?= $description; ?></textarea>

										</div>

									</div>

								</div>

								<div class="row">

									<!-- <div class="col-md-6">

										<div class="form-group">

	                                        <label for="image_path">Image  <input type="hidden" name="filename" id="filename" class="form-control" /><code>*</code></label>

	                                        <small>minimum image size 370 x 260</small>

	                                        <br />

	                                        <div class="dropzone" data-width="370" data-height="260" data-ghost="false" data-originalsize="false" data-url="crop_news.php" style="width: 370;height:260px;">

	                                            <input type="file" id="image_path" name="image_path" value="<?php echo $image_path; ?>">

	                                        </div>

	                                        <input type="hidden" name="old_image_path" value="<?php echo $image_path; ?>" />

	                                        <?php

	                                        if($image_path!="" && file_exists(NEWS_A.$image_path)){

	                                        ?>

	                                        <br />

	                                        <img src="<?php echo NEWS_A.$image_path;?>" width="260" >

	                                        <?php

	                                        }

	                                        ?>

	                                    </div>

									</div> -->

									<div class="col-md-6">
										<div class="form-group">
											<input data-image="<?php echo ($image_path!="" && file_exists("../".NEWS.$image_path))?"../".NEWS.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
										</div>
									</div>

								</div>

							</div>

							<div class="form-actions">

								<button type="submit" name="submit" class="btn green">Submit</button>

								<button type="button" class="btn btn-default" onClick="window.location.href='manage_news.php'">Back</button>

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

<!-- <script>

	$('.dropzone').html5imageupload({

		onAfterProcessImage: function() {

			var imgName = $('#filename').val($(this.element).data('imageFileName'));

		},

		onAfterCancel: function() {

			$('#filename').val('');

		}

	});

</script> -->

<script type="text/javascript">

	var image_path="<?=$image_path?>";

function check_form(){

	if($("#title").val()=="" || $("#title").val().split(" ").join("")=="")

	{

		alert("Please Enter Title");

		$("#title").focus();

		return false;

	}

	if($("#description").val()=="" || $("#description").val().split(" ").join("")=="")

	{

		alert("Please Enter Description");

		$("#description").focus();

		return false;

	}

	if($("#image_path").val()=="" || $("#image_path").val().split(" ").join("")=="")

	{

		if(image_path == "")
		{
			alert("Please upload News Image.");

			$("#image_path").focus();

			return false;
		}
		else
		{

		}
	}

}



</script>

</body>

</html>