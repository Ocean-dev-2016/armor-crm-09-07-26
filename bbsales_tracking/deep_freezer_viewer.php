<?php
$page_id=581;$page_slug='manage_complain';
$page_slug = 'complain';
$ctable 	= "freezer_scheme";
$ctable1 	= "Deep Freezer Scheme";
$main_page 	= $ctable;
$page 		= "view_" . $ctable;
$page_title = "View " . $ctable1;
include("connect_in.php");
$admin_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
$bid 	= $_REQUEST['id'];
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>

	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
	<style type="text/css">
		#wrapper {
			width: 190mm;
			margin: 0 50mm;
		}

		#wrapper {
			width: auto !important;
		}
	</style>
</head>

<body class="page-md">
	<div class="transCover"><img src="assets/admin/layout/img/89.gif" alt="" style="margin-top:20%;padding-left:48%;"></div>
	<?php include("header.php"); ?>
	<div class="page-container">

		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h2><?php echo $page_title; ?></h2>

				</div>
				<div class="page-toolbar">
					<?php
					if($flag_d['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
					<?php
					}
					?>
					
				</div>
			</div>
		</div>

		<div class="page-content">
			<div class="container">
				<div class="row">

					<div class="col-md-12" id="report_content">
						<div id="wrapper1">
							<?php 
							require_once("deep_freezer_scheme_print.php");
							?>
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
	<script>
		function printReport(id) {
			var id = "<?= $_REQUEST['id'] ?>";
			var myWindow = window.open('deep_freezer_scheme_print.php?id=' + id, '', 'width=500,height=800');
			// var myWindow = window.open('quotation_view_new_quotation_new_1.php?quotation_id=' + id + "&p=1", '', 'width=500,height=800');
			myWindow.print();
			// setTimeout(function () 
			// {
			// 	myWindow.print();
			// 	var ival = setInterval(function() 
			// 	{
			// 	    myWindow.close();
			// 	    clearInterval(ival);
			// 	}, 200);
			// }, 500);
		}
	</script>

</body>

</html>