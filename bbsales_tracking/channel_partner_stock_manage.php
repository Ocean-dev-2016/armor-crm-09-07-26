<?php
$page_id = 650;
$page_slug = 'channel_partner_stock';
$ctable = "customer_inward_stock";
$ctable1 = "Channel Partner Stock";
$main_page = "channel_partner";
$page = "channel_partner_stock";
$page_title = "Channel Partner Stock";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_stock_manage.php", "title" => $page_title)
);
include("connect.php");

$is_cp_login = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = function_exists('cp_get_login_channel_partner_id') ? cp_get_login_channel_partner_id() : 0;
$selected_cp = $is_cp_login ? (int) $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
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
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption"><i class="fa fa-cubes"></i> Product-wise Stock</div>
							<?php if ($is_cp_login) { ?>
							<div class="actions">
								<a href="channel_partner_print_settings.php" class="btn btn-sm blue"><i class="fa fa-file-text-o"></i> SO/PI Format</a>
							</div>
							<?php } ?>
						</div>
						<div class="portlet-body">
							<?php if (!$is_cp_login) { ?>
							<div class="row" style="margin-bottom:12px;">
								<div class="col-md-4">
									<label>Select Channel Partner</label>
									<select class="form-control" id="cp_id">
										<option value="">-- All Channel Partners --</option>
										<?php
										$cp_r = $db->rp_getData("executive", "id,company_name,client_code", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
										if ($cp_r) {
											while ($cp = mysqli_fetch_assoc($cp_r)) {
												$sel = ($selected_cp == (int) $cp['id']) ? 'selected' : '';
												echo '<option value="' . (int) $cp['id'] . '" ' . $sel . '>' . htmlspecialchars($cp['company_name'] . ' (' . $cp['client_code'] . ')') . '</option>';
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-2" style="padding-top:24px;">
									<button type="button" class="btn blue" id="btn_load_stock"><i class="fa fa-search"></i> Load</button>
								</div>
							</div>
							<?php } ?>
							<div class="loading-div" style="display:none;">
								<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin:10% auto;display:block;">
							</div>
							<div id="results"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
function displayRecords() {
	$(".loading-div").show();
	$("#results").hide();
	var cpId = <?php echo $is_cp_login ? (int) $cp_login_id : 0; ?>;
	<?php if (!$is_cp_login) { ?>
	cpId = $("#cp_id").val() || 0;
	<?php } ?>
	$.ajax({
		url: "channel_partner_stock_get_ajax.php",
		type: "POST",
		data: { cp_id: cpId },
		success: function (html) {
			$("#results").html(html);
			$(".loading-div").hide();
			$("#results").show();
		}
	});
}
$(document).ready(function () {
	displayRecords();
	$("#btn_load_stock").on("click", function () { displayRecords(); });
	$("#cp_id").on("change", function () { displayRecords(); });
});
</script>
</body>
</html>
