<?php
$main_page 	= "product_mgmt";
$page_title = "Access Denied!!";
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
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
				<div class="col-md-6 col-xs-offset-3">
				<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']!=""){ ?>
				<div class="alert alert-danger alert-dismissable"><h4> <i style="font-size:20px!important;margin-top:20px;" class="fa fa-warning"></i>
					<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
					<strong>Alert! </strong><br><br>
					<?php
						if($_REQUEST['msg']=="insert_access_denied"){
							echo "You have no right to insert data !! If there are any issue then contact your administrator!!!";
						}
						else if($_REQUEST['msg']=="view_access_denied"){
							echo "You have no right to view data !! If there are any issue then contact your administrator!!!";
						}
						else if($_REQUEST['msg']=="page_not_registered"){
							echo "Its Look like requested page is missing or not registered !! If there are any issue then contact your administrator!!!";
						}else if($_REQUEST['msg']=="update_access_denied"){
							echo "You have no right to update data !! If there are any issue then contact your administrator!!!";
							
						}else if($_REQUEST['msg']=="delete_access_denied"){
							echo "You have no right to delete data !! If there are any issue then contact your administrator!!!";
							
						}
						else 
						{
							echo "You have no right to view this data !! If there are any issue then contact your administrator!!!";
						}
					?>
					</h4>
				</div>
			<?php } ?>
					<div class="portlet light center">
						<h1 class="text-danger"><i class="fa fa-warning" style="font-size:70px!important;margin-top:20px;"></i> &nbsp;<span style="font-size:70px!important;margin-top:20px;"> ERROR </span><span style="font-size:90px!important;margin-top:20px;"> 909 </span></h1>
						<h2 class="text-danger text-center">Access Denied!!</h2>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<?php include("include_js.php"); ?>
</script>
</body>
</html>