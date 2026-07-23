<?php
$page_id = 650;
$page_slug = 'channel_partner_stock';
$ctable = "customer_inward_stock";
$ctable1 = "My Stock";
$main_page = "channel_partner";
$page = "channel_partner_stock";
$page_title = "Channel Partner Stock";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_stock_manage.php", "title" => $page_title)
);
include("connect.php");

$is_cp_login = cp_is_channel_partner_login($db);
$cp_login_id = cp_get_login_channel_partner_id();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="channel_partner_customer_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>
					<?php if (!$is_cp_login) { ?>
					<div class="alert alert-info">This stock screen is for Channel Partner login. Please login with a Channel Partner system user to view product-wise stock.</div>
					<?php } else { ?>
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><i class="fa fa-cubes"></i> Product-wise Stock</div>
						</div>
						<div class="portlet-body">
							<div class="loading-div" style="display:none;">
								<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin:10% auto;display:block;">
							</div>
							<div id="results"></div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<?php if ($is_cp_login) { ?>
<script type="text/javascript">
function displayRecords() {
	$(".loading-div").show();
	$("#results").hide();
	$.ajax({
		url: "channel_partner_stock_get_ajax.php",
		type: "POST",
		data: {},
		success: function (html) {
			$("#results").html(html);
			$(".loading-div").hide();
			$("#results").show();
		}
	});
}
$(document).ready(function () {
	displayRecords();
});
</script>
<?php } ?>
</body>
</html>
