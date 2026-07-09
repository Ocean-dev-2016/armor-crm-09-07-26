<?php
$page_id=405;$page_slug='app_pages';
$page_slug="app_pages" ;
$ctable 	= "page_table";
$ctable1 	= "Pages";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title1 = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
$page_title			= "";
$page_slug			= "";
$page_count			= "";
$page_urls			= "";
if(isset($_REQUEST['submit'])){
	
	$page_title			= $db->clean($_REQUEST['page_title']);
	$page_slug			= $db->clean($_REQUEST['page_slug']);
	$page_count			= $db->clean($_REQUEST['page_count']);
	$page_urls			= $db->clean($_REQUEST['page_urls']);
	$isDelete		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		$dup_where = "page_slug = '".$page_slug."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		
		if($r){
			$db->rp_location($ctable."_crud.php?mode=add&msg=duplicate");
			die;
		}else{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(						
						"page_title",
						"page_slug",
						"page_count",
						"page_urls",
						"isDelete",
						"adate"
					);
			$values = array(
						$page_title,
						$page_slug,		
						$page_count,		
						$page_urls,		
						$isDelete,
						$adate
					);
					
			$tid = $db->rp_insert($ctable,$values,$rows,0);			
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r){
			$db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
			die;
		}else{
			$rows 	= array(
						"page_title"			=> $page_title,
						"page_slug"				=> $page_slug,
						"page_count"			=> $page_count,					
						"page_urls"				=>	$page_urls,
											
					);
			$where	= "id='".$_REQUEST['id']."'";
			$db->rp_update($ctable,$rows,$where);
			$db->rp_location($ctable."_manage.php?msg=updated");
		}
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$ctable_d = mysqli_fetch_array($ctable_r);
	$tid=$_REQUEST['id'];
	$page_title			= htmlentities($ctable_d['page_title']);
	$page_slug			= htmlentities($ctable_d['page_slug']);
	$page_count			= htmlentities($ctable_d['page_count']);
	$page_urls			= htmlentities($ctable_d['page_urls']);
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$rows 	= array(
				"isDelete"	=> "1"
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	
	$db->rp_location($ctable."_manage.php?msg=deleted");
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-tags-input/jquery.tagsinput.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title1; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">
					<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="duplicate"){ ?>
					<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
						<b>Error! This Page Slug is Already Exist. Please Try Another Slug.</b> </div>
					<?php } ?>
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
									<div class="col-md-6">
								<div class="form-group">
									<label>Page Title<code>*</code></label>
									<input type="text" class="form-control" name="page_title" id="page_title" value="<?php echo $page_title; ?>" autofocus>
									<p class="help-block"></p>
								</div>
								</div>
								
								</div>
								
									
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Page Slug<code>*</code></label>
											<input type="text" class="form-control" name="page_slug" id="page_slug" value="<?php echo $page_slug; ?>">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Page Count<code>*</code></label>
											<input type="text" class="form-control" name="page_count" id="page_count" value="<?php echo $page_count; ?>">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Page Urls<code>*</code></label>
											<input type="text" class="form-control" name="page_urls" id="page_urls" value="<?php echo $page_urls; ?>">
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
<script src="assets/global/plugins/jquery-tags-input/jquery.tagsinput.js"></script>
<script type="text/javascript">
$('#page_urls').tagsInput({  'defaultText':'Add a page url', 'placeholderColor' : '#333333'});
$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); } }); });
function check_form(){
	$(".form-body").children().removeClass("has-error");
	isValid=true;
	if($("#page_title").val()=="" || $("#page_title").val().split(" ").join("")==""){		
		aj.error('page_title','Please enter page title!!','add_error');
		isValid=false;		
	}
	if($("#page_count").val()=="" || $("#page_count").val().split(" ").join("")==""){
		aj.error('page_count','Please enter page count!!','add_error');
		isValid=false;	
	}
	if($("#page_slug").val()=="" || $("#page_slug").val().split(" ").join("")==""){
		aj.error('page_slug','Please enter page slug!!','add_error');
		isValid=false;	
	}
	if($("#page_urls").val()=="" || $("#page_urls").val().split(" ").join("")==""){
		aj.error('page_urls','Please enter page url!!','add_error');
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