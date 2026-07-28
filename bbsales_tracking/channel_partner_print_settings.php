<?php
/**
 * Channel Partner SO/PI Format Settings — like Admin Company Master
 * (header/footer images, GST, PAN, address, bank, terms).
 */
$page_id = 650;
$page_slug = 'channel_partner_stock';
$page_title = "SO / PI Format Settings";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_print_settings.php", "title" => $page_title)
);
include("connect.php");

if (!function_exists('cp_is_channel_partner_login') || !cp_is_channel_partner_login($db)) {
	$db->addErrorMessage("Only Channel Partner login can set PI format.");
	$db->rp_location("dashboard.php");
}

$cp_login_id = (int) cp_get_login_channel_partner_id();

/* Ensure PI format columns exist (local + live without waiting for db_sync only) */
$cpPrintCols = array(
	'cp_print_header' => "text",
	'cp_print_footer' => "text",
	'cp_print_company_name' => "varchar(255) NOT NULL DEFAULT ''",
	'cp_print_gst' => "varchar(30) NOT NULL DEFAULT ''",
	'cp_print_pan' => "varchar(30) NOT NULL DEFAULT ''",
	'cp_print_header_image' => "varchar(255) NOT NULL DEFAULT ''",
	'cp_print_footer_image' => "varchar(255) NOT NULL DEFAULT ''",
	'cp_print_address' => "text",
	'cp_print_bank_details' => "text",
	'cp_print_terms' => "text",
);
foreach ($cpPrintCols as $colName => $colDef) {
	$chk = @mysqli_query($db->myconn, "SHOW COLUMNS FROM `executive` LIKE '" . $colName . "'");
	if (!($chk && mysqli_num_rows($chk) > 0)) {
		@mysqli_query($db->myconn, "ALTER TABLE `executive` ADD COLUMN `" . $colName . "` " . $colDef);
	}
}

function cp_upload_print_image($fileKey, $prefix)
{
	if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey])) {
		return '';
	}
	$f = $_FILES[$fileKey];
	if (!isset($f['name']) || $f['name'] == '' || !isset($f['tmp_name']) || $f['tmp_name'] == '') {
		return '';
	}
	if (!empty($f['error'])) {
		return '';
	}
	$allowed = array('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
	$parts = explode('.', $f['name']);
	$ext = end($parts);
	if (!in_array($ext, $allowed)) {
		return '';
	}
	$dir = defined('HEADER_A') ? HEADER_A : '../images/header/';
	if (!is_dir($dir)) {
		@mkdir($dir, 0755, true);
	}
	$fname = $prefix . '_' . substr(sha1(uniqid((string) mt_rand(), true)), 0, 8) . '.' . strtolower($ext);
	$dest = $dir . $fname;
	if (@move_uploaded_file($f['tmp_name'], $dest)) {
		return $fname;
	}
	return '';
}

if (isset($_REQUEST['btn_save']) || isset($_REQUEST['submit'])) {
	$company = isset($_REQUEST['cp_print_company_name']) ? trim($_REQUEST['cp_print_company_name']) : '';
	$gst = isset($_REQUEST['cp_print_gst']) ? trim($_REQUEST['cp_print_gst']) : '';
	$pan = isset($_REQUEST['cp_print_pan']) ? trim($_REQUEST['cp_print_pan']) : '';
	$address = isset($_REQUEST['cp_print_address']) ? $_REQUEST['cp_print_address'] : '';
	$bank = isset($_REQUEST['cp_print_bank_details']) ? $_REQUEST['cp_print_bank_details'] : '';
	$terms = isset($_REQUEST['cp_print_terms']) ? $_REQUEST['cp_print_terms'] : '';

	$oldHeaderImg = isset($_REQUEST['old_header_image']) ? $_REQUEST['old_header_image'] : '';
	$oldFooterImg = isset($_REQUEST['old_footer_image']) ? $_REQUEST['old_footer_image'] : '';
	$headerImg = $oldHeaderImg;
	$footerImg = $oldFooterImg;

	$newHeader = cp_upload_print_image('cp_print_header_image', 'cp_header');
	if ($newHeader != '') {
		$headerImg = $newHeader;
	} else if (isset($_REQUEST['remove_header_image']) && $_REQUEST['remove_header_image'] == '1') {
		$headerImg = '';
	}

	$newFooter = cp_upload_print_image('cp_print_footer_image', 'cp_footer');
	if ($newFooter != '') {
		$footerImg = $newFooter;
	} else if (isset($_REQUEST['remove_footer_image']) && $_REQUEST['remove_footer_image'] == '1') {
		$footerImg = '';
	}

	$rows = array(
		'cp_print_company_name' => $company,
		'cp_print_gst' => $gst,
		'cp_print_pan' => $pan,
		'cp_print_address' => $address,
		'cp_print_bank_details' => $bank,
		'cp_print_terms' => $terms,
		'cp_print_header_image' => $headerImg,
		'cp_print_footer_image' => $footerImg,
		'modified_date' => date('Y-m-d H:i:s'),
	);

	$ok = $db->rp_update(
		'executive',
		$rows,
		"id='" . $cp_login_id . "' AND channel_partner_flag=1",
		0
	);
	if ($ok) {
		$db->addSuccessMessage("SO / PI format saved. New Customer Orders will print with this format.");
	} else {
		$db->addErrorMessage("Save failed. Please try again.");
	}
	$db->rp_location("channel_partner_print_settings.php");
}

$exec = array(
	'company_name' => '',
	'gst' => '',
	'cp_print_company_name' => '',
	'cp_print_gst' => '',
	'cp_print_pan' => '',
	'cp_print_header_image' => '',
	'cp_print_footer_image' => '',
	'cp_print_address' => '',
	'cp_print_bank_details' => '',
	'cp_print_terms' => '',
	'cp_print_header' => '',
	'cp_print_footer' => '',
);
$r = $db->rp_getData("executive", "*", "id='" . $cp_login_id . "' AND isDelete=0", "", 0);
if ($r) {
	$exec = array_merge($exec, mysqli_fetch_assoc($r));
}

/* Prefill from CP profile when print fields empty */
if ($exec['cp_print_company_name'] == '' && !empty($exec['company_name'])) {
	$exec['cp_print_company_name'] = $exec['company_name'];
}
if ($exec['cp_print_gst'] == '' && !empty($exec['gst'])) {
	$exec['cp_print_gst'] = $exec['gst'];
}
if ($exec['cp_print_address'] == '' && !empty($exec['address'])) {
	$exec['cp_print_address'] = $exec['address'];
}
if ($exec['cp_print_bank_details'] == '') {
	$exec['cp_print_bank_details'] = "Bank Name : <br>Bank Account No : <br>Bank IFSC Code : <br>Bank Branch : ";
}

$headerImgFile = isset($exec['cp_print_header_image']) ? $exec['cp_print_header_image'] : '';
$footerImgFile = isset($exec['cp_print_footer_image']) ? $exec['cp_print_footer_image'] : '';
$headerImgUrl = ($headerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $headerImgFile)) ? (HEADER_A . $headerImgFile) : '';
$footerImgUrl = ($footerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $footerImgFile)) ? (HEADER_A . $footerImgFile) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<style type="text/css">
label { font-weight: bold; }
.cp-pi-hint {
	background: #e8f4f8;
	border-left: 4px solid #1a6b8a;
	padding: 10px 14px;
	margin-bottom: 16px;
	font-size: 13px;
}
.cp-img-preview { max-width: 100%; margin-top: 8px; border: 1px solid #ddd; }
.cp-img-preview img { max-width: 100%; height: auto; display: block; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="channel_partner_customer_manage.php" class="primary">
						<i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i>
					</a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<?php $db->printErrorMessage(); ?>
			<?php $db->printSuccessMessage(); ?>

			<div class="cp-pi-hint">
				Set your <strong>Pro Forma Invoice / Sales Order</strong> format here (same style as Admin Company Master).
				Header image, footer image, bank details and terms will appear on print after Customer Order save.
			</div>

			<form role="form" method="post" action="" enctype="multipart/form-data" id="cpPiFormatForm" onsubmit="return cpCheckPiForm();">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption"><i class="fa fa-file-text-o"></i> Add / Edit PI Format — <?php echo htmlspecialchars($exec['company_name']); ?></div>
					</div>
					<div class="portlet-body form">
						<div class="row">
							<div class="col-md-6">
								<div class="form-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Company Name <code>*</code></label>
												<input type="text" class="form-control" name="cp_print_company_name" id="cp_print_company_name" value="<?php echo htmlspecialchars($exec['cp_print_company_name']); ?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>GST</label>
												<input type="text" class="form-control" name="cp_print_gst" id="cp_print_gst" maxlength="15" value="<?php echo htmlspecialchars($exec['cp_print_gst']); ?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Pan Card</label>
												<input type="text" class="form-control" name="cp_print_pan" id="cp_print_pan" value="<?php echo htmlspecialchars($exec['cp_print_pan']); ?>">
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label>Header Image (933 X 184)</label>
												<input type="file" name="cp_print_header_image" id="cp_print_header_image" accept="image/*"
													data-image="<?php echo htmlspecialchars($headerImgUrl); ?>"
													data-old-image-dom="old_header_image"
													data-old-image-path="<?php echo htmlspecialchars($headerImgFile); ?>">
												<input type="hidden" name="old_header_image" id="old_header_image" value="<?php echo htmlspecialchars($headerImgFile); ?>">
												<input type="hidden" name="remove_header_image" id="remove_header_image" value="0">
												<?php if ($headerImgUrl != '') { ?>
												<div class="cp-img-preview" id="cpHeaderPreview">
													<img src="<?php echo htmlspecialchars($headerImgUrl); ?>" alt="Header">
													<button type="button" class="btn btn-danger btn-xs" style="margin-top:6px;" onclick="cpRemoveImg('header');">Remove</button>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label>Footer Image (943 X 103)</label>
												<input type="file" name="cp_print_footer_image" id="cp_print_footer_image" accept="image/*"
													data-image="<?php echo htmlspecialchars($footerImgUrl); ?>"
													data-old-image-dom="old_footer_image"
													data-old-image-path="<?php echo htmlspecialchars($footerImgFile); ?>">
												<input type="hidden" name="old_footer_image" id="old_footer_image" value="<?php echo htmlspecialchars($footerImgFile); ?>">
												<input type="hidden" name="remove_footer_image" id="remove_footer_image" value="0">
												<?php if ($footerImgUrl != '') { ?>
												<div class="cp-img-preview" id="cpFooterPreview">
													<img src="<?php echo htmlspecialchars($footerImgUrl); ?>" alt="Footer">
													<button type="button" class="btn btn-danger btn-xs" style="margin-top:6px;" onclick="cpRemoveImg('footer');">Remove</button>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label>Address</label>
												<textarea name="cp_print_address" id="cp_print_address"><?php echo html_entity_decode($exec['cp_print_address']); ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label>Bank Details</label>
												<textarea name="cp_print_bank_details" id="cp_print_bank_details"><?php echo html_entity_decode($exec['cp_print_bank_details']); ?></textarea>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label>Terms And Condition</label>
												<textarea name="cp_print_terms" id="cp_print_terms"><?php echo html_entity_decode($exec['cp_print_terms']); ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions" style="text-align:center;padding:16px;">
							<button type="submit" name="btn_save" value="1" class="btn green">Submit</button>
							<a href="channel_partner_customer_manage.php" class="btn btn-default">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script src="js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script type="text/javascript">
function cpCheckPiForm() {
	if (typeof CKEDITOR !== 'undefined') {
		for (var name in CKEDITOR.instances) {
			CKEDITOR.instances[name].updateElement();
		}
	}
	var nm = $.trim($("#cp_print_company_name").val() || "");
	if (nm === "") {
		toastr.error("Please Enter Company Name.");
		return false;
	}
	return true;
}
function cpRemoveImg( wh) {
	if (wh === 'header') {
		$("#old_header_image").val("");
		$("#remove_header_image").val("1");
		$("#cpHeaderPreview").hide();
		$("#cp_print_header_image").val("");
	} else {
		$("#old_footer_image").val("");
		$("#remove_footer_image").val("1");
		$("#cpFooterPreview").hide();
		$("#cp_print_footer_image").val("");
	}
}
$(function () {
	if (typeof CKEDITOR !== 'undefined') {
		CKEDITOR.replace('cp_print_address');
		CKEDITOR.replace('cp_print_bank_details');
		CKEDITOR.replace('cp_print_terms');
	}
	if (typeof aj !== 'undefined' && typeof aj.imageHolder === 'function') {
		try {
			aj.imageHolder($("input[name=cp_print_header_image]"), "", "", function () {}, function () {});
			aj.imageHolder($("input[name=cp_print_footer_image]"), "", "", function () {}, function () {});
		} catch (e) {}
	}
});
</script>
</body>
</html>
<?php include("disconnect.php"); ?>
