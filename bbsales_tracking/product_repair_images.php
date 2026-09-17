<?php
/**
 * Repair product.image_path to match real files in:
 *   images/product/
 *   images/product/thumb/
 * Use when DB still has .webp but only .jpg remains (or vice versa).
 */
$page_id = 559;
$page_slug = 'page_product';
include("connect.php");
require_once("../include/image_webp_helper.php");

if (!isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID'])) {
	header("Location: index.php");
	exit;
}

$abs = armor_product_image_abs_dirs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Repair Product Images | <?php echo SITETITLE; ?></title>
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
					&nbsp;Repair Product Images (DB ↔ Folder)
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="portlet light">
				<div class="portlet-body">
					<div class="alert alert-warning">
						<strong>Recover logic:</strong> DB path (webp <b>or</b> jpg) mate folder ma search thase —
						<code>images/product/</code> ane <code>images/product/thumb/</code> ma
						<b>.jpg / .jpeg / .png / .gif / .webp</b> — <b>je male te recover</b> (DB update + thumb mathi main ma copy).
					</div>
					<div class="alert alert-info">
						Disk dirs:<br>
						Main: <code><?php echo htmlspecialchars($abs['main']); ?></code><br>
						Thumb: <code><?php echo htmlspecialchars($abs['thumb']); ?></code>
					</div>
					<p>
						<button type="button" id="btnScan" class="btn blue">1) Scan (find JPG + WebP)</button>
						<button type="button" id="btnRepair" class="btn red">2) Recover All (je male te set)</button>
					</p>
					<div id="statsBox" style="margin:10px 0;font-weight:600;"></div>
					<pre id="logBox" style="max-height:420px;overflow:auto;background:#111;color:#0f0;padding:12px;border-radius:4px;"></pre>
					<div class="alert alert-danger" style="margin-top:12px;">
						<strong>Jo JPG ane WebP banne missing</strong> — recover nathi thatu. Backup restore athva re-upload karo.
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
(function() {
	function log(msg) {
		var box = document.getElementById('logBox');
		box.textContent += msg + "\n";
		box.scrollTop = box.scrollHeight;
	}
	function setStats(text) {
		document.getElementById('statsBox').textContent = text;
	}

	document.getElementById('btnScan').onclick = function() {
		log('Scanning DB vs disk...');
		$.getJSON('product_convert_webp_ajax.php', { action: 'scan_match' }, function(res) {
			if (!res || !res.ack) {
				log('Scan failed: ' + (res && res.message ? res.message : 'unknown'));
				return;
			}
			setStats('OK: ' + res.ok + ' | Recoverable: ' + res.fixable + ' | Missing: ' + res.missing + ' | Found JPG/PNG: ' + (res.found_jpg || 0) + ' | Found WebP: ' + (res.found_webp || 0));
			log('Scan done (JPG + WebP both searched).');
			log('OK (file matches DB): ' + res.ok);
			log('Recoverable (jpg OR webp found): ' + res.fixable);
			log('Found as JPG/PNG/GIF: ' + (res.found_jpg || 0));
			log('Found as WebP: ' + (res.found_webp || 0));
			log('Truly missing (both formats absent): ' + res.missing);
			if (res.details && res.details.length) {
				log('--- sample ---');
				for (var i = 0; i < res.details.length; i++) {
					log(res.details[i]);
				}
			}
		}).fail(function() {
			log('Scan AJAX failed');
		});
	};

	document.getElementById('btnRepair').onclick = function() {
		if (!confirm('Je file male (JPG athva WebP) te DB ma set karvu?')) {
			return;
		}
		log('Recovering (JPG + WebP)...');
		$.ajax({
			url: 'product_convert_webp_ajax.php',
			type: 'POST',
			dataType: 'json',
			data: { action: 'repair_missing' },
			success: function(res) {
				if (!res || !res.ack) {
					log('Recover failed: ' + (res && res.message ? res.message : 'unknown'));
					return;
				}
				setStats('Recovered: ' + res.fixed + ' | From thumb: ' + (res.restored_from_thumb || 0) + ' | Still missing: ' + res.missing + ' | Already OK: ' + res.ok);
				log('Recover done.');
				log('Already OK: ' + res.ok);
				log('Recovered (jpg or webp): ' + res.fixed);
				log('Restored from thumb: ' + (res.restored_from_thumb || 0));
				log('Still missing: ' + res.missing);
				if (res.details && res.details.length) {
					for (var i = 0; i < res.details.length; i++) {
						log(res.details[i]);
					}
				}
				log('Have Product page refresh karo.');
			},
			error: function() {
				log('Repair AJAX failed');
			}
		});
	};
})();
</script>
</body>
</html>
