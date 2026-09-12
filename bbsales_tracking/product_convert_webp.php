<?php
/**
 * Convert existing product images (jpg/png/gif) to WebP — batch tool
 */
$page_id = 559;
$page_slug = 'page_product';
include("connect.php");
require_once("../include/image_webp_helper.php");

if (!isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID'])) {
	header("Location: index.php");
	exit;
}

$webpOk = armor_image_webp_supported() ? 1 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Convert Product Images to WebP | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="product_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left"></i></a>
					&nbsp;Convert Existing Product Images → WebP
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="portlet light">
				<div class="portlet-body">
					<?php if (!$webpOk) { ?>
						<div class="alert alert-danger">This server PHP/GD does not support WebP. Conversion disabled.</div>
					<?php } else { ?>
						<div class="alert alert-info">
							This will convert existing <b>jpg / jpeg / png / gif</b> product images to <b>.webp</b>,
							update DB <code>product.image_path</code>, and regenerate thumb/small copies.
							Already-webp images are skipped.
						</div>
						<p>
							<button type="button" id="btnScan" class="btn blue">Scan Pending Images</button>
							<button type="button" id="btnConvert" class="btn green" disabled>Convert All Pending</button>
							<button type="button" id="btnConvertBatch" class="btn yellow" disabled>Convert Next 25</button>
						</p>
						<div id="statsBox" style="margin:10px 0;font-weight:600;"></div>
						<pre id="logBox" style="max-height:360px;overflow:auto;background:#111;color:#0f0;padding:12px;border-radius:4px;"></pre>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
(function() {
	var pending = 0;
	function log(msg) {
		var box = document.getElementById('logBox');
		box.textContent += msg + "\n";
		box.scrollTop = box.scrollHeight;
	}
	function setStats(text) {
		document.getElementById('statsBox').textContent = text;
	}
	function setButtons(enabled) {
		document.getElementById('btnConvert').disabled = !enabled;
		document.getElementById('btnConvertBatch').disabled = !enabled;
	}

	document.getElementById('btnScan').onclick = function() {
		log('Scanning...');
		$.getJSON('product_convert_webp_ajax.php', { action: 'scan' }, function(res) {
			if (!res || !res.ack) {
				log('Scan failed: ' + (res && res.message ? res.message : 'unknown'));
				return;
			}
			pending = parseInt(res.pending || 0, 10);
			setStats('Total products with image: ' + res.total_with_image + ' | Already WebP: ' + res.already_webp + ' | Pending convert: ' + pending);
			log('Scan done. Pending = ' + pending);
			setButtons(pending > 0);
		}).fail(function() {
			log('Scan AJAX failed');
		});
	};

	function runConvert(limit) {
		log('Converting' + (limit ? (' next ' + limit) : ' ALL') + '...');
		setButtons(false);
		$.ajax({
			url: 'product_convert_webp_ajax.php',
			type: 'POST',
			dataType: 'json',
			data: { action: 'convert', limit: limit || 0 },
			success: function(res) {
				if (!res || !res.ack) {
					log('Convert failed: ' + (res && res.message ? res.message : 'unknown'));
					setButtons(true);
					return;
				}
				log('Converted: ' + res.converted + ' | Skipped: ' + res.skipped + ' | Failed: ' + res.failed);
				if (res.details && res.details.length) {
					for (var i = 0; i < res.details.length; i++) {
						log(res.details[i]);
					}
				}
				pending = parseInt(res.remaining || 0, 10);
				setStats('Remaining pending: ' + pending);
				setButtons(pending > 0);
				if (pending > 0 && !limit) {
					// safety: if "all" was requested but server batches, continue
					log('More remaining — click Convert again if needed.');
				}
			},
			error: function() {
				log('Convert AJAX failed');
				setButtons(true);
			}
		});
	}

	document.getElementById('btnConvert').onclick = function() { runConvert(0); };
	document.getElementById('btnConvertBatch').onclick = function() { runConvert(25); };
})();
</script>
</body>
</html>
