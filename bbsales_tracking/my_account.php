<?php
$page_id=401;
include("connect.php");

$main_page 	= "my_account";
$page 		= "my_account";
$page_title	= "My Account";

$name		= "";
$username		= "";
$email			= "";

if(isset($_REQUEST['submit'])){

	$name		= addslashes(trim($_REQUEST['name']));
	$username	= addslashes(trim($_REQUEST['username']));
	$email		= trim($_REQUEST['email']);
	$email_password	= isset($_REQUEST['email_password']) ? $db->clean($_REQUEST['email_password']) : ""; 
	
	if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=""){
		$uname = $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
		$rows 	= array("name"=>$name,"username"=>$username,"email"=>$email,"email_password"=>$email_password);
	}
	$where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
	$db->rp_update(CTABLE_ADMIN,$rows,$where);
	$_SESSION[SITE_SESS.'SESS_NAME']=$username;
	$_SESSION['SESS_NAME'] = $name;
	// $db->rp_location("dashboard.php?msg=1");
	$db->addSuccessMessage("<b>Success! </b>Account details has been updated.");
	$db->rp_location("my_account.php?msg=1");	
}
if(isset($_REQUEST['submit1'])){

	$where = " id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
	$admin_r = $db->rp_getData(CTABLE_ADMIN,"*",$where,"",0);
	$admin_d = mysqli_fetch_array($admin_r);
		
	$old_password		= $admin_d['password'];
    $opassword		= md5(trim($_REQUEST['opassword']));
	$password		= md5(trim($_REQUEST['password']));
	if($admin_d['password']!=md5(trim($_REQUEST['opassword'])))
	{
		$db->rp_location("my_account.php?msg=3");
	}
	 else if($old_password!=$opassword){
		$db->rp_location("my_account.php?msg=2");
	}else{
		$rows 	= array("password"=>$password);
		
		$where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
		$db->rp_update(CTABLE_ADMIN,$rows,$where);
		// $db->rp_location("dashboard.php?msg=1");
		$db->addSuccessMessage("<b>Success! </b>Account details has been updated.");
		$db->rp_location("my_account.php?msg=1");
	}
}
//logo detail
if(isset($_REQUEST['logosave']))
{

	$logo_detail	= isset($_FILES);
	$old_logo_path =$_REQUEST['old_logo_path'];
     // Order Header

    if (isset($_FILES["logoImg"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["logoImg"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["logoImg"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["logoImg"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$logo_detail	= 'logo_'.substr(sha1(time()+3), 0, 6).".".$extension;

		$logo_path 	= LOGO_A.$logo_detail;	
		$_FILES['logoImg']['tmp_name'];
		move_uploaded_file($_FILES['logoImg']['tmp_name'], $logo_path);
		
		$new_image=true;
		}
		else{
			$logo_path=$old_logo_path;
		}
    }
    else
    {
	    $new_image=false;
	    $logo_detail=$old_logo_path;
	    $old_logo_path =$_REQUEST['logo_path'];
    }
    $rows 	= array("logo_detail"=>$logo_detail);
    $where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
	$db->rp_update(CTABLE_ADMIN,$rows,$where);
	//$db->rp_location("dashboard.php?msg=1");
}



if(isset($_REQUEST['submit_form'])){
    $image_path	 = isset($_FILES);
    if (isset($_FILES["image_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
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
							
		$image_path	= 'gst_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$image_path;	
		$_FILES['image_path']['tmp_name'];
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
    
    $where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
	$db->rp_update(CTABLE_ADMIN,array("file_path"=>$image_path),$where,0);
	// $db->rp_location("dashboard.php?msg=1");
	$db->addSuccessMessage("<b>Success! </b>Account details has been updated.");
	$db->rp_location("my_account.php?msg=1");
}


if(isset($_REQUEST['submit_form_other'])){
    $price_list_path		 = isset($_FILES);
    $bank_detail_path		 = isset($_FILES);
    $scheme_path	 		 = isset($_FILES);
    $dealer_discount_path	 = isset($_FILES);
    $distributor_discount_path	 = isset($_FILES);

    /*price_list_path*/
    if (isset($_FILES["price_list_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["price_list_path"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["price_list_path"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["price_list_path"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$price_list_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$price_list_path;	
		$_FILES['price_list_path']['tmp_name'];
		move_uploaded_file($_FILES['price_list_path']['tmp_name'], $filePath);
		
		$new_image=true;
		}
		else{
			$price_list_path="";
		}
    }
    else
    {
	    $new_image=false;
	    $price_list_path="";
    }

    /*price_list_path*/

    /*bank_detail_path*/

    if (isset($_FILES["bank_detail_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["bank_detail_path"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["bank_detail_path"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["bank_detail_path"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$bank_detail_path	= 'bank_detail_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$bank_detail_path;	
		$_FILES['bank_detail_path']['tmp_name'];
		move_uploaded_file($_FILES['bank_detail_path']['tmp_name'], $filePath);
		
		$new_image=true;
		}
		else{
			$bank_detail_path="";
		}
    }
    else
    {
	    $new_image=false;
	    $bank_detail_path="";
    }
    /*bank_detail_path*/

    /*scheme_path*/

    if (isset($_FILES["scheme_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["scheme_path"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["scheme_path"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["scheme_path"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$scheme_path	= 'scheme_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$scheme_path;	
		$_FILES['scheme_path']['tmp_name'];
		move_uploaded_file($_FILES['scheme_path']['tmp_name'], $filePath);
		
		$new_image=true;
		}
		else{
			$scheme_path="";
		}
    }
    else
    {
	    $new_image=false;
	    $scheme_path="";
    }

    /*scheme_path*/

    /*dealer_discount_path*/
    if (isset($_FILES["dealer_discount_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["dealer_discount_path"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["dealer_discount_path"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["dealer_discount_path"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$dealer_discount_path	= 'dealer_discount_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$dealer_discount_path;	
		$_FILES['dealer_discount_path']['tmp_name'];
		move_uploaded_file($_FILES['dealer_discount_path']['tmp_name'], $filePath);
		
		$new_image=true;
		}
		else{
			$dealer_discount_path="";
		}
    }
    else
    {
	    $new_image=false;
	    $dealer_discount_path="";
    }
    /*dealer_discount_path*/

    /*distributor_discount_path*/
    if (isset($_FILES["distributor_discount_path"]) ) 
    {
	    $allowedExts = array("jpg","JPG","pdf","PDF");
	    $temp = explode(".", $_FILES["distributor_discount_path"]["name"]);
	    $extension = end($temp);
 
		$fileName 	= $db->clean($_FILES["distributor_discount_path"]["name"]);	
		if($fileName!=""){
		$fileSize 	= round($_FILES["distributor_discount_path"]["size"]); // BYTES									
		$adate 		= date('Y-m-d H:i:m');
		
		$extension	= end(explode(".", $fileName));		
		if(!in_array($extension,$allowedExts))
		{
			$file_error=true;
		}
							
		$distributor_discount_path	= 'distributor_discount_'.substr(sha1(time()), 0, 6).".".$extension;
		$filePath 	= GST_VISITING_DETAIL_A.$distributor_discount_path;	
		$_FILES['distributor_discount_path']['tmp_name'];
		move_uploaded_file($_FILES['distributor_discount_path']['tmp_name'], $filePath);
		
		$new_image=true;
		}
		else{
			$distributor_discount_path="";
		}
    }
    else
    {
	    $new_image=false;
	    $distributor_discount_path="";
    }
    /*distributor_discount_path*/


    

    $where	= "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
	$db->rp_update(CTABLE_ADMIN,array("price_list_path"=>$price_list_path,"bank_detail_path"=>$bank_detail_path,"scheme_path"=>$scheme_path,"dealer_discount_path"=>$dealer_discount_path,"distributor_discount_path"=>$distributor_discount_path),$where,0);
	$db->addSuccessMessage("<b>Success! </b>Account details has been updated.");
	$db->rp_location("my_account.php?msg=1");
}




$where = " id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";
$admin_r = $db->rp_getData(CTABLE_ADMIN,"*",$where);
$admin_d = mysqli_fetch_array($admin_r);

$id				= $admin_d['id'];
$name			= $admin_d['name'];
$username		= $admin_d['username'];
$email 			= $admin_d['email'];
$email_password = $admin_d['email_password'];
$image_path 	= $admin_d['file_path'];
$price_list_path 	= $admin_d['price_list_path'];
$bank_detail_path 	= $admin_d['bank_detail_path'];
$scheme_path 	= $admin_d['scheme_path'];
$dealer_discount_path 	= $admin_d['dealer_discount_path'];
$distributor_discount_path 	= $admin_d['distributor_discount_path'];
$logo_data 	= $admin_d['logo_detail'];
//$name=$_SESSION['SESS_NAME'];
					
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
				<h1><?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<div class="row">
			<div class="col-xl-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
			</div>
		</div>
		<?php 
			if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="1"){
			?>			
			<!-- <div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Success! </b>Account details has been updated.
			</div> -->
			<?php
			}
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="3"){
			?>
			<!-- <div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Error! </b>Current Password Not Match.Please try again.
			</div>
			<div class="row"> -->
			<?php
			}
			?>
			<div class="row">
				<div class="col-md-6 ">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase"> Personal Information</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" onSubmit="return check_form();" method="post">
								<div class="form-body">
									<div class="form-group">
										<label for="name">Name</label>
										<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo $name; ?>">
									</div>
									<div class="form-group">
										<label for="username">Username</label>
										<input type="text" class="form-control" name="username" id="username" placeholder="Enter User Name" value="<?php echo $username; ?>">
									</div>
									<div class="form-group">
										<label for="email">Email</label>
										<input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo $email; ?>">
									</div>
									<div class="form-group">
										<label for="email_password">Email Password</label>
										<input type="password" class="form-control" name="email_password" id="email_password" placeholder="Enter Password" value="<?php echo $email_password; ?>">
									</div>
								</div>
								<div class="form-actions">
									<button type="submit" name="submit" class="btn green">Save Profile</button>
									<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-6 ">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-settings font-grey-gallery"></i>
								<span class="caption-subject bold uppercase"> Change Password</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" onSubmit="return check_form2();" method="post">
							<div class="form-body">
								<div class="form-group">
									<label for="opassword">Current Password</label>
									<input type="password" class="form-control" name="opassword" id="opassword" placeholder="Enter Current Password" value="" autocomplete="off">
								</div>
								<div class="form-group">
									<label for="password">New Password</label>
									<input type="password" class="form-control" name="password" id="password" placeholder="Enter New Password" value="" autocomplete="off">
								</div>
								<div class="form-group">
									<label for="cpassword">Confirm New Password</label>
									<input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Enter New Password" value="<?php //echo $password; ?>" autocomplete="off">
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit1" class="btn green">Change Password</button>
								<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<!-- <div class="col-md-6">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase">Gst Detail</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" action=""  method="post" enctype="multipart/form-data">
							<div class="form-body">
	                            <div class="form-group">
	                            	<?php 
											$imgpath5 = GST_VISITING_DETAIL_A.$image_path;
											$ext5 = strtolower(pathinfo($imgpath5, PATHINFO_EXTENSION)); 
											if($ext5=="pdf")
											{
												$imgpath5 = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext5=="doc" || $ext5=="docx")
											{
												$imgpath5 = GST_VISITING_DETAIL_A.'pdf.png';
											}
										?>
	                                <input data-image="<?php echo ($image_path!="" && file_exists($imgpath5))?$imgpath5:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
								</div>
                           	</div>
                            
							<div class="form-actions">
								<button type="submit" name="submit_form" class="btn green">Save</button>
								<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
							</div>
							</form>
						</div>
					</div>
				</div> -->

			

				<div class="col-md-6">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase">Other Detail</span>
							</div>
							
						</div>
						<div class="portlet-body form">
							<form role="form" action=""  method="post" enctype="multipart/form-data">
								<div class="form-body">
									<div class="form-group">
										<label>Select Catalogue <span style="color:red;">( Maximum Size 10 MB )</span></label>
										<?php 
											$imgpath = GST_VISITING_DETAIL_A.$price_list_path;
											$ext = strtolower(pathinfo($imgpath, PATHINFO_EXTENSION)); 
											if($ext=="pdf")
											{
												$imgpath = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext=="doc" || $ext=="docx")
											{
												$imgpath = GST_VISITING_DETAIL_A.'doc.png';
											}
										?>
		                                <input data-image="" type="file" name="price_list_path" id="price_list_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $price_list_path ?>" value="" >
									</div>
									<?php if($price_list_path != ""): ?>
                                	<a href="<?php echo SITEURL.GST_VISITING_DETAIL.$price_list_path; ?>" download><i class="fa fa-download" style="font-size: 21px;padding: 13px;"></i><?php echo $price_list_path; ?></a>
	                                <?php endif; ?>

									<!-- <div class="form-group">
										<label>Select Bank Details</label>
										<?php 
											$imgpath1 = GST_VISITING_DETAIL_A.$bank_detail_path;
											$ext1 = strtolower(pathinfo($imgpath1, PATHINFO_EXTENSION)); 
											if($ext1=="pdf")
											{
												$imgpath1 = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext1=="doc" || $ext1=="docx")
											{
												$imgpath1 = GST_VISITING_DETAIL_A.'doc.png';
											}
										?>
		                                <input data-image="<?php echo ($bank_detail_path!="" && file_exists($imgpath1))?$imgpath1:"";?>" type="file" name="bank_detail_path" id="bank_detail_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $bank_detail_path ?>" value="" >
									</div>

									<div class="form-group">
										<label>Select Scheme</label>
										<?php 
											$imgpath2 = GST_VISITING_DETAIL_A.$scheme_path;
											$ext2 = strtolower(pathinfo($$imgpath2, PATHINFO_EXTENSION)); 
											if($$ext2=="pdf")
											{
												$$imgpath2 = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext2=="doc" || $ext2=="docx")
											{
												$imgpath2 = GST_VISITING_DETAIL_A.'doc.png';
											}
										?>
		                                <input data-image="<?php echo ($scheme_path!="" && file_exists($imgpath2))?$imgpath2:"";?>" type="file" name="scheme_path" id="scheme_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $scheme_path ?>" value="" >
									</div>

									<div class="form-group">
										<label>Select Dealer Discount</label>
										<?php 
											$imgpath3 = GST_VISITING_DETAIL_A.$dealer_discount_path;
											$ext3 = strtolower(pathinfo($imgpath3, PATHINFO_EXTENSION)); 
											if($ext3=="pdf")
											{
												$imgpath3 = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext3=="doc" || $ext3=="docx")
											{
												$imgpath3= GST_VISITING_DETAIL_A.'doc.png';
											}
										?>
		                                <input data-image="<?php echo ($dealer_discount_path!="" && file_exists($imgpath3))?$imgpath3:"";?>" type="file" name="dealer_discount_path" id="dealer_discount_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $dealer_discount_path ?>" value="" >
									</div>

									<div class="form-group">
										<label>Select Distributor Discount</label>
										<?php 
											$imgpath4 = GST_VISITING_DETAIL_A.$distributor_discount_path;
											$ext4 = strtolower(pathinfo($imgpath4, PATHINFO_EXTENSION)); 
											if($ext4=="pdf")
											{
												$imgpath4 = GST_VISITING_DETAIL_A.'pdf.png';
											}
											else if($ext4=="doc" || $ext4=="docx")
											{
												$imgpath4 = GST_VISITING_DETAIL_A.'doc.png';
											}
										?>
		                                <input data-image="<?php echo ($distributor_discount_path!="" && file_exists($imgpath4))?$imgpath4:"";?>" type="file" name="distributor_discount_path" id="distributor_discount_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $distributor_discount_path ?>" value="" >
									</div> -->
								</div>
                           
                           		<div class="form-actions">
									<button type="submit" name="submit_form_other" class="btn green">Save</button>
									<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<?php 
					// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 )
					// {
					?>

				<!-- <div class="col-md-6">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase">Logo Detail</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form role="form" action="" method="post" enctype="multipart/form-data" id="logoImgfrm">
								<div class="form-body">
									<h4 style="font-size: 14px;font-weight: 600;color: red">(Logo image size must me of size 406px X 260px)</h4>
									<h4 class="headerimgtitl">Logo</h4>
									<div class="form-group">

		                                  <input data-image="<?php echo ($logo_data!="" && file_exists(LOGO_A.$logo_data))?LOGO_A.$logo_data:"";?>" type="file" accept="image/*" name="logoImg" id="logoImg" data-old-image-dom="old_logo_path"  data-old-image-path="<?php echo $logo_data ?>" value="">

		                                   <input type="hidden" name="old_logo_path" id="old_logo_path" value="<?php echo $logo_data;?>">	
		                                
									</div>
									<div class="form-actions" style="background-color: transparent;padding-top: 10px;">
									<button type="submit" name="logosave" id="logosave" class="btn green">Save</button>
									<button onclick="window.location.href='dashboard.php'" class="btn btn-default" type="button">Cancel</button>
								</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div> -->
			<?php 
			//}
			?>

					<!-- notification setting tab -->
				<!-- 	<div class="row">
				<div class="col-md-6">
					<div class="portlet box">
						<div class="portlet-title">
							<div class="caption font-grey-gallery">
								<i class="icon-user font-grey-gallery"></i>
								<span class="caption-subject bold uppercase">Notification Setting</span>
							</div>
						</div>
						<div class="portlet-body form">
							<form action="#">
	                            <table class="table table-light table-hover">
	                                <tr>
	                                    <td>
	                                        <div class="mt-checkbox-list" style="margin-left:11px;">
	                                            <button type="button" class="btn btn-primary" onclick="GenerateFcmToken()">Notification Web</button>

	                                            <button type="button" class="btn btn-primary" onclick="GenerateFcmTokenMobile()">Notification Web Mobile</button>
	                                        </div>
	                                    </td>
	                                </tr>
	                            </table>                                            
                            </form>
						</div>
					</div>
				</div>
			</div> -->
				<!-- notification setting tab -->
			</div>
			
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<!-- <script type="text/javascript" src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<link rel="manifest" href="manifest.json"> -->

<script type="text/javascript">
function check_form(){
	$(".form-body").children().removeClass("has-error");
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
		alert("Please enter admin name.");
		$("#name").focus().parent().addClass("has-error");
		return false;
	}
	if($("#username").val()=="" || $("#username").val().split(" ").join("")==""){
		alert("Please enter admin username.");
		$("#username").focus();
		return false;
	}
	if($("#email").val()=="" || $("#email").val().split(" ").join("")==""){
		alert("Please enter admin email.");
		$("#email").focus().parent().addClass("has-error");
		return false;
	}else{
		if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())){    
		}else{
			alert("Please enter valid admin email.");
			$("#email").focus().parent().addClass("has-error");
			return false;
		}
	}

	if($("#username").val()=="" || $("#username").val().split(" ").join("")==""){
		alert("Please enter admin username.");
		$("#username").focus();
		return false;
	}
}
function check_form2(){
	if($("#opassword").val()=="" || $("#opassword").val().split(" ").join("")==""){
		alert("Please enter current password.");
		$("#opassword").focus().parent().addClass("has-error");
		return false;
	}
	if($("#password").val()=="" || $("#password").val().split(" ").join("")==""){
		alert("Please enter new password.");
		$("#password").focus().parent().addClass("has-error");
		return false;
	}
	if($("#cpassword").val()=="" || $("#cpassword").val().split(" ").join("")==""){
		alert("Please enter confirm password.");
		$("#password").focus().parent().addClass("has-error");
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
	// image path

	//price_list_path
	$(function(){
		aj.imageHolder($("input[name=price_list_path]"),"","",
		// function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
		// 	isImageThumbnailLoaded=isImageThumbnailLoadedReply;
		// 	isImageThumbnailValidT=isImageThumbnailValidReply;
		// 	toastr.success("Old Image Found!!");
		// },
		// function(file,img)
		// {
		// 	if(!file)
		// 	{
		// 		toastr.error("File may be corrupted or missing. Try again!!");
		// 	}
		// },
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

	//bank_detail_path
	$(function(){
		aj.imageHolder($("input[name=bank_detail_path]"),"","",
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

	//scheme_path
	$(function(){
		aj.imageHolder($("input[name=scheme_path]"),"","",
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

	//distributor_discount_path
	$(function(){
		aj.imageHolder($("input[name=dealer_discount_path]"),"","",
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

	//distributor_discount_path
	$(function(){
		aj.imageHolder($("input[name=distributor_discount_path]"),"","",
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
	function GenerateFcmToken()
    {
        var config = {
	     apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
	    authDomain: "cmk-crm.firebaseapp.com",
	    projectId: "cmk-crm",
	    storageBucket: "cmk-crm.appspot.com",
	    messagingSenderId: "345899882377",
	    appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
	    measurementId: "G-2TS49WRQ29",
        
      // databaseURL: "https://craftbox-5d2bb.firebaseio.com",
        };
        if (!firebase.apps.length) {
 			  firebase.initializeApp({});
		}else {
  			 firebase.app(); // if already initialized, use that one
		}
        // Retrieve Firebase Messaging object.
      //  const messaging = firebase.messaging();
        const messaging = firebase.messaging();
        
        messaging.requestPermission().then(function() {
            getRegToken();
        }).catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        })
    }

    function getRegToken(argument) {     
     //   alert("hello");
              const messaging = firebase.messaging();
         messaging
           .requestPermission()
           .then(function () {
             //MsgElem.innerHTML = "Notification permission granted." 
             console.log("Notification permission granted.");
        
             // get the token in the form of promise
            return messaging.getToken()
           })
           .then(function(token) {
            //   alert(token);
            console.log( token);
            if(token){
            
            saveToken(token);
                
            }
             // print the token on the HTML page
            // TokenElem.innerHTML = "Device token is : <br>" + token
           })
           .catch(function (err) {
           //ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
           console.log("Unable to get permission to notify.", err);
         });
    }


    function saveToken(currentToken) {
    	var UpdateTo = "refresh_token_web";
    	// alert(currentToken);
        $.ajax({
            url: 'ajax_update_token.php',
            method: 'post',
            data: {token : currentToken,update_to:UpdateTo},
        }).done(function(result){
            console.log(result);
            result = $.parseJSON(result);
        if (result.ack == 0) {
				toastr.error(result.ack_msg);
			} else {
				toastr.success(result.ack_msg);
				// window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
			}
        })

    }

    function GenerateFcmTokenMobile()
	{
	    var config = {
		    apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
		    authDomain: "cmk-crm.firebaseapp.com",
		    projectId: "cmk-crm",
		    storageBucket: "cmk-crm.appspot.com",
		    messagingSenderId: "345899882377",
		    appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
		    measurementId: "G-2TS49WRQ29",
		    // databaseURL: "https://craftbox-5d2bb.firebaseio.com",
	    };
	    if (!firebase.apps.length) 
	    {
				firebase.initializeApp({});
		}
		else 
		{
				firebase.app(); // if already initialized, use that one
		}
	    // Retrieve Firebase Messaging object.
	  	// const messaging = firebase.messaging();
	    const messaging = firebase.messaging();
	    messaging.requestPermission().then(function() {
	        getRegTokenMobile();
	    }).catch(function(err) {
	        console.log('Unable to get permission to notify.', err);
	    })
	}

	function getRegTokenMobile(argument) 
	{     
		const messaging = firebase.messaging();
	    messaging
	   	.requestPermission()
	   	.then(function () 
	   	{
	     	//MsgElem.innerHTML = "Notification permission granted." 
	    	console.log("Notification permission granted.");
	    	// get the token in the form of promise
	    	return messaging.getToken()
	   	})
	   	.then(function(token) {
	    	console.log( token);
	    	if(token){
	    		saveTokenMobile(token);
	    	}
	    	// print the token on the HTML page
	    	// TokenElem.innerHTML = "Device token is : <br>" + token
	   	})
	   	.catch(function (err) {
	   		//ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
	   		console.log("Unable to get permission to notify.", err);
	 	});
	}

	function saveTokenMobile(currentToken) 
	{
		var UpdateTo = "refresh_token_mobile_web";
	    $.ajax({
	        url: 'ajax_update_token.php',
	        method: 'post',
	        data: {token : currentToken,update_to:UpdateTo},
	    }).done(function(result){
	        console.log(result);
	        result = $.parseJSON(result);
	    if (result.ack == 0) {
				toastr.error(result.ack_msg);
			} else {
				toastr.success(result.ack_msg);
				// window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
			}
	    })
	}

	$(function(){
		aj.imageHolder($("input[name=logoImg]"),"<?= LOGO_IMG_WIDTH; ?>","<?= LOGO_IMG_HEIGHT; ?>",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValid=isImageThumbnailValidReply;
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
			isImageThumbnailValid=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	});

		$("#logoImgfrm").submit( function( e ) {
 		var isValid=true;
 		var form = this;
 		if(!isImageThumbnailValid)
 		{	
 			toastr.error("Please Select Image!!","error");
 			isValid=false;			
 		}
 		 return isValid;
	 });

	document.getElementById('price_list_path').addEventListener('change', function(event) 
	{
	    const file = event.target.files[0];
	    if (file.size > 11 * 1024 * 1024) 
	    {
	        toastr.error("Image size exceeds 50 MB. Please choose a smaller image.","error");
			isValid=false;			
	        event.target.value = ''; // Clear the input field
	    }
		return isValid;
	});

</script>

</body>
</html>

