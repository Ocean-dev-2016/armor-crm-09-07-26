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
$view = isset($_REQUEST['view']) ? strtolower(trim($_REQUEST['view'])) : 'main';
if ($view !== 'inout') {
	$view = 'main';
}
if ($is_cp_login) {
	$page_title = "My Stock";
	$page_hierarchy = array(
		array("link" => "", "title" => "Channel Partner"),
		array("link" => "channel_partner_stock_manage.php", "title" => $page_title)
	);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<style>
.cp-stock-tabs { margin-bottom: 14px; border-bottom: 2px solid #ddd; }
.cp-stock-tabs .nav-tabs { border-bottom: 0; }
.cp-stock-tabs .nav-tabs > li > a {
	font-weight: 600; color: #555; border-radius: 0; margin-right: 4px;
}
.cp-stock-tabs .nav-tabs > li.active > a,
.cp-stock-tabs .nav-tabs > li.active > a:hover,
.cp-stock-tabs .nav-tabs > li.active > a:focus {
	background: #1f4e79; color: #fff; border-color: #1f4e79;
}
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<?php if (!$is_cp_login) { ?>
					<a href="channel_partner_customer_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;
					<?php } ?>
					<?php $db->pageBar($page_hierarchy); ?>
				</h1>
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
							<div class="caption"><i class="fa fa-cubes"></i> <?php echo $is_cp_login ? 'My Stock' : 'Channel Partner Stock'; ?></div>
							<div class="actions">
								<a href="javascript:;" id="btn_cp_stock_excel" class="btn btn-sm yellow-crusta" style="color:#fff;" title="Export Excel">
									<i class="fa fa-file-excel-o"></i> Export Excel
								</a>
								<?php if ($is_cp_login) { ?>
								<a href="channel_partner_print_settings.php" class="btn btn-sm blue"><i class="fa fa-file-text-o"></i> SO/PI Format</a>
								<?php } ?>
							</div>
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

							<div class="cp-stock-tabs" id="cp_stock_view_tabs" style="<?php echo (!$is_cp_login && $selected_cp <= 0) ? 'display:none;' : ''; ?>">
								<ul class="nav nav-tabs">
									<li class="<?php echo ($view === 'main') ? 'active' : ''; ?>">
										<a href="javascript:;" data-view="main"><i class="fa fa-cubes"></i> 1. Main Stock <small>(Product &amp; Code)</small></a>
									</li>
									<li class="<?php echo ($view === 'inout') ? 'active' : ''; ?>">
										<a href="javascript:;" data-view="inout"><i class="fa fa-exchange"></i> 2. Inward / Outward <small>(Bill No &amp; Date)</small></a>
									</li>
								</ul>
							</div>

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
var currentStockView = "<?php echo $view === 'inout' ? 'inout' : 'main'; ?>";
var isCpLogin = <?php echo $is_cp_login ? 'true' : 'false'; ?>;
var cpLoginId = <?php echo (int) $cp_login_id; ?>;

function getSelectedCpId() {
	if (isCpLogin) {
		return cpLoginId;
	}
	return parseInt($("#cp_id").val() || 0, 10) || 0;
}

function stockExcelUrl() {
	var cpId = getSelectedCpId();
	return "channel_partner_stock_excel.php?view=" + encodeURIComponent(currentStockView) + "&cp_id=" + cpId;
}

function displayRecords() {
	$(".loading-div").show();
	$("#results").hide();
	var cpId = <?php echo $is_cp_login ? (int) $cp_login_id : 0; ?>;
	<?php if (!$is_cp_login) { ?>
	cpId = $("#cp_id").val() || 0;
	if (parseInt(cpId, 10) > 0) {
		$("#cp_stock_view_tabs").show();
	} else {
		$("#cp_stock_view_tabs").hide();
		currentStockView = "main";
		$("#cp_stock_view_tabs .nav-tabs li").removeClass("active");
		$("#cp_stock_view_tabs .nav-tabs li:first").addClass("active");
	}
	<?php } ?>
	$.ajax({
		url: "channel_partner_stock_get_ajax.php",
		type: "POST",
		data: { cp_id: cpId, view: currentStockView },
		success: function (html) {
			$("#results").html(html);
			$(".loading-div").hide();
			$("#results").show();
		},
		error: function () {
			$(".loading-div").hide();
			$("#results").html('<div class="alert alert-danger">Failed to load stock.</div>').show();
		}
	});
}
$(document).ready(function () {
	displayRecords();
	$("#btn_load_stock").on("click", function () { displayRecords(); });
	$("#cp_id").on("change", function () { displayRecords(); });
	$("#cp_stock_view_tabs").on("click", "a[data-view]", function (e) {
		e.preventDefault();
		currentStockView = $(this).data("view");
		$("#cp_stock_view_tabs .nav-tabs li").removeClass("active");
		$(this).closest("li").addClass("active");
		displayRecords();
	});
	$("#btn_cp_stock_excel").on("click", function (e) {
		e.preventDefault();
		var cpId = getSelectedCpId();
		if (!cpId) {
			alert("Please select Channel Partner first.");
			return;
		}
		window.location.href = stockExcelUrl();
	});
});
</script>
</body>
</html>
