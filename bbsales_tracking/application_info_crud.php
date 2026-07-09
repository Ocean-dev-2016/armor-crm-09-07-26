<?php
$page_id=562;$page_slug='page_category';
$ctable 	= "application_info";
$ctable2 	= "application_info";
$ctable1 	= "Application Info";
$page 		= "manage_".$ctable2;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
$version_name="";
$version_code="";
$type="";
$rand_file_name="";
$file_path="";
if(isset($_REQUEST['submit'])){

	$version_name			= addslashes(trim($_REQUEST['version_name']));
	$version_code			= addslashes(trim($_REQUEST['version_code']));
	$type					= addslashes(trim($_REQUEST['type']));
	$rand_file_name					= addslashes(trim($_REQUEST['old_file_path']));
	
	  $errors= array();
	  $file_name = $_FILES['file_path']['name'];
	
	
      $file_size =$_FILES['file_path']['size'];
      $file_tmp =$_FILES['file_path']['tmp_name'];
      $file_type=$_FILES['file_path']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['file_path']['name'])));
      
      $extensions= array("apk");
      
      if(in_array($file_ext,$extensions)=== false)
	  {
			$errors[]="extension not allowed, please choose APK file.";
			//$reply =array("ack_msg"=>"extension not allowed, please choose APK file.");
			//$db->addErrorMessage($reply['ack_msg']);
			//$db->rp_location($ctable."_crud.php?mode=add");
			//die; 	
      }
      
      if($file_size >= 1400000000)
	  {
         $errors[]='File size must be excately 2 MB';
		/* $reply =array("ack_msg"=>"File size must be excately 2 MB");
		 $db->addErrorMessage($reply['ack_msg']);
		 $db->rp_location($ctable."_crud.php?mode=add");*/
		 //die;
      }
     // print_r($errors);exit;
      if(empty($errors))
	  {
		 $rand_file_name=$db->rp_createSlug(SITETITLE)."_".str_replace(".","_",$version_code).".".$file_ext; 
		 $filename=APK_PATH.$rand_file_name;
         move_uploaded_file($file_tmp,$filename);
        
      } 
	 
	$isActive		= 1;	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		
		$dup_where = "version_code = '".$version_code."' AND type = '".$type."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where,0);
		if($r)
		{
			
			$reply =array("ack_msg"=>"Type And version_code is already Exist");
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location($ctable."_crud.php?mode=add&msg=duplicate");
		}
		else
		{
			$created_date= date('Y-m-d H:i:s');
			$rows 	= array(
					"version_name",
					"version_code",
					"type",
					"file",
					"isDelete",
					"isActive",
					
								
				);
			$values = array(
					$version_name,
					$version_code,
					$type,
					$rand_file_name,
					0,
					0,
				);
				
				
			$inserted_id = $db->rp_insert($ctable,$values,$rows,0);
			$reply=array("ack"=>1,"developer_msg"=>"Application Info Added.","ack_msg"=>"Success!  Insert Successfully.");
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
	
		}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		
		
		$dup_where = "version_code = '".$version_code."' AND type = '".$type."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
			if($r)
			{
				$reply=array("ack_msg"=>"Type And version_code is already Exist.");
				$db->addErrorMessage($reply['ack_msg']);
				$db->rp_location($ctable."_crud".".php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
				
			}
			else
			{
				
				$rows 	= array(
					"version_name"					=> $version_name,
					"version_code"					=> $version_code,
					"file"							=> $rand_file_name,	
					"type"							=> $type,	
						
				);
					
				// DELETE OLD IMAGES WHILE EDITING IF NEW IMAGE UPLOADED
				
				$where	= "id=".$_REQUEST['id'];
				$db->rp_update($ctable,$rows,$where);
				$reply =array("ack_msg"=>"Inserted Successfully");
				$db->rp_location($ctable."_manage.php?msg=updated");
			}
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	
	
	$where 	= " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r 	= $db->rp_getData($ctable,"*",$where);
	$ctable_d 	= mysqli_fetch_array($ctable_r);
	$version_name		= stripslashes($ctable_d['version_name']);
	$file_path			= stripslashes($ctable_d['file']);
	$version_code		= stripslashes($ctable_d['version_code']);
	$type				= stripslashes($ctable_d['type']);

}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$last_added=$db->rp_getValue($ctable,"MAX(id)","isDelete=0");
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$last_added."'";
	$db->rp_update($ctable,$rows,$where,0);	
	$db->rp_update($ctable,array("isActive"	=> 0),"id!='".$last_added."'",0);
	$db->rp_location($ctable."_manage.php?msg=updated");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$rows 	= array(
				"isDelete"	=> 1
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where,0);	
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable2."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
									<label>Version Type <code>*</code></label>
											<select class="form-control" name="type" id="type" >
											
												<option value="">--Select Type-</option>
												
												<option value="android_customer" <?php if($type=="android_customer"){ ?>selected <?php } ?> > 
												Android Customer</option>
												<option value="android_sales" <?php if($type=="android_sales"){ ?>selected <?php } ?> > 
												Android Sales</option>
												<option value="ios_customer" <?php if($type=="ios_customer"){ ?>selected <?php } ?> > 
												IOS Customer</option>
												<option value="ios_sales" <?php if($type=="ios_sales"){ ?>selected <?php } ?> > 
												IOS Sales</option>
											</select>
											<p class="help-block"></p>
									</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Version Name <code>*</code></label>
											<input type="text" class="form-control" name="version_name" id="version_name" value="<?php echo $version_name; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
									<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Version Code<code>*</code></label>
											<input type="text" class="form-control" name="version_code" id="version_code" value="<?php echo $version_code; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="input-group input-large">
													<label>Select APK File<code>*</code></label>
												    <input type="file" name="file_path" id="file_path" value=""><?php echo $file_path;?> 
													<input type="hidden" name="old_file_path" id="file_path" value="<?php echo $file_path; ?>" value=""> 
													<p class='help-block'></p>
											</div>
										</div>
                                    </div>
                                    </div>
							
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable2; ?>_manage.php'">Back</button>
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
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#version_name").val()=="" || $("#version_name").val().split(" ").join("")==""){		
		vd=aj.error('version_name',"Please Enter Version Name.","add_error");
		isValid=false;
	}
	if($("#version_code").val()=="" || $("#version_code").val().split(" ").join("")==""){		
		vd=aj.error('version_code',"Please Enter Version Code.","add_error");
		isValid=false;
	}
	if($("#type").val()=="" || $("#type").val().split(" ").join("")==""){		
		vd=aj.error('type',"Please Select Type.","add_error");
		isValid=false;
	}
	if($("#file_path").val()=="" || $("#file_path").val().split(" ").join("")==""){		
		vd=aj.error('file_path',"Please Select APK File.","add_error");
		isValid=false;
	}
	if($("#file_path").val()!=""){
	var ext = $('#file_path').val().split('.').pop().toLowerCase();
	if($.inArray(ext, ['apk']) == -1) {
	   toastr.error("Please Upload APK File","Error!");
	   aj.error('logo_path','Please  Upload APK File !!','add_error');
	   isValid=false;
	}
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