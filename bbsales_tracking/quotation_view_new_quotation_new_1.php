<?php
$page_id = 566;
$page_slug = 'page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");

$ntw = new NumToWord_RP;
$quotation_id	= $_REQUEST['quotation_id'];
$cart_detail_r 	= $db->rp_getData("quotation_detail", "*", "id='" . $quotation_id . "'", "", 0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$order_date = ($cart_detail_d['order_date'] != "0000-00-00 00:00:00") ? date("d-m-Y", strtotime($cart_detail_d['order_date'])) : "";
$type_of_customer = $db->rp_getValue("executive", "type_of_executive", "id =  '" . $cart_detail_d['customer_id'] . "' ", 0);
if ($type_of_customer == 3) {
	$customer_id = $db->rp_getValue("executive", "dealer_distributor_id", "id= '" . $cart_detail_d['customer_id'] . "' ", 0);
} else if ($type_of_customer == 2) {
	$customer_id = $db->rp_getValue("executive", "super_stockist_id", "id = '" . $cart_detail_d['customer_id'] . "' ");
} else {
	$customer_id = "";
}

if (isset($cart_detail_d['customer_id']) && !empty($cart_detail_d['customer_id'])) {
	$customer_address = $db->rp_getData("executive", "address,company_name,zip", "id = '" . $cart_detail_d['customer_id'] . "' ", "");
	$customer_address_d = mysqli_fetch_assoc($customer_address);
}

$company_detail_r = $db->rp_getData("company_master", "*", "id='" . $cart_detail_d['type_of_company'] . "' AND isDelete=0", "", 0);

$company_detail_d = mysqli_fetch_assoc($company_detail_r);

$quotationPrintTitle = 'Quotation';
if (!empty($cart_detail_d['quotation_no'])) {
	$quotationPrintTitle .= ' - ' . trim($cart_detail_d['quotation_no']);
}
if (!empty($cart_detail_d['company_name'])) {
	$quotationPrintTitle .= ' - ' . trim($cart_detail_d['company_name']);
}
$quotationPrintTitle = preg_replace('/[\\\\\/:*?"<>|]+/', '-', $quotationPrintTitle);
$quotationPrintTitle = preg_replace('/\s+/', ' ', $quotationPrintTitle);
$quotationPrintTitle = trim($quotationPrintTitle);

$isPrintMode = (isset($_REQUEST['print']) && $_REQUEST['print'] == '1') || (isset($_REQUEST['p']) && $_REQUEST['p'] == '1');
$isMpdfMode = isset($_REQUEST['mpdf']) && $_REQUEST['mpdf'] == '1';
$isAppPdfMode = isset($_REQUEST['app_pdf']) && $_REQUEST['app_pdf'] == '1';
$isPdfExportMode = ($isMpdfMode || $isAppPdfMode);
if ($isPrintMode || $isAppPdfMode || $isMpdfMode) {
	@ini_set('memory_limit', '1024M');
	@set_time_limit(180);
}
$quotationViewEmbedded = (basename($_SERVER['SCRIPT_NAME']) === 'quotation_viewer.php');
$quotationViewStandalone = !$quotationViewEmbedded;
if ($quotationViewStandalone && !$isPdfExportMode && !defined('ARMOR_PDF_EXPORT_EMBED')) {
	ob_start();
}
?>
<?php if ($quotationViewStandalone) { ?><html>

<head>
	<title><?= htmlspecialchars($quotationPrintTitle, ENT_QUOTES, 'UTF-8') ?></title>
<?php } ?>
	<style>
		.mainDiv,
		table {
			border-collapse: collapse;
			font-size: 13px;
			background-color: #FFF;
			margin: auto;
			padding: auto;
		}

		table,
		td,
		th {
			border: 1px solid #595959;
		}

		.quote-suggest-body table.qp-suggest-print-grid,
		.quote-suggest-body .qp-prod-card,
		.quote-suggest-body .qp-prod-card td,
		.quote-suggest-body .qp-prod-foot-table td,
		.quote-suggest-body .qp-suggest-wrap-table,
		.quote-suggest-body .qp-suggest-wrap-table td {
			border-color: transparent;
		}

		.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-print-cell,
		.quote-suggest-body table.qp-suggest-print-grid td.qp-suggest-cat-header {
			border: 1px solid #595959 !important;
		}

		.quote-suggest-body .qp-prod-img-cell {
			border-bottom: 1px solid #e8e8e8 !important;
		}

		.qp-suggest-print-header {
			border: none !important;
			border-bottom: 1px solid #595959 !important;
		}

		.quote-wrap {
			width: 100%;
			border: 1px solid #595959;
			box-sizing: border-box;
			background: #fff;
		}

		.quote-main-body {
			box-sizing: border-box;
			background: #fff;
			border: none;
		}

		.quote-main-body table {
			margin: 0 !important;
			width: 100% !important;
			max-width: 100% !important;
			border: none !important;
			border-collapse: collapse !important;
		}

		.quote-main-body .product-items-table {
			table-layout: fixed;
		}

		.quote-main-body > table + table,
		.quote-main-body .product-items-table,
		.quote-main-body .quote-footer-wrap {
			margin-top: 0 !important;
		}

		.quote-footer-wrap {
			margin: 0;
			padding: 0;
			border-top: 1px solid #595959;
		}

		td,
		th {
			padding: 5px;
			height: 18px;
		}

		.text-center {
			text-align: center !important;
		}

		.text-right {
			text-align: right !important;
		}

		.no-border-left {
			border-left: hidden;
		}

		.no-border-right {
			border-right: hidden;
		}

		.no-border-bottom {
			border-bottom: hidden !important;
		}

		.no-border-top {
			border-top: hidden !important;
		}

		.border td {
			border-bottom: hidden !important;
		}

		.color {
			background: #D3D3D3;
		}

		.font-size td {
			font-size: 15px !important;
		}

		.image-width {
			width: 10% !important;
			min-width: 10% !important;
			max-width: 10% !important;
		}

		.border-r-width {
			border-right-width: 5px;
		}

		.border-gray {
			border-right-color: #E5E5E5;
		}

		.border-blue {
			border-right-color: <?= VIEW_COLOR ?>;
		}

		.vertical-top {
			vertical-align: top;
		}

		.height-5 {
			height: 5px;
		}

		.bg-gray {
			background-color: #E5E5E5 !important;
		}

		.font-13 {
			font-size: 13px !important;
		}

		.headerBorder {
			border: 22px solid #eb268f;
			border-bottom: none;
			border-right: none;
			border-left: none;
		}

		.main-container {
			padding: 20px;
			width: 100% !important;
			max-width: 980px;
			background-color: #FFF;
			margin: auto;
			box-sizing: border-box;
		}

		.quote-wrap,
		.quote-main-body,
		.quote-suggest-body,
		.quote-summary-body {
			width: 100%;
		}

		.quote-summary-body {
			box-sizing: border-box;
			background: #fff;
			border: none;
			border-top: 1px solid #595959;
		}

		.quote-summary-terms-cell {
			vertical-align: top;
		}

		.quote-summary-totals-block {
			width: 100%;
		}

		.quote-summary-details-table,
		.quote-summary-amounts-table {
			width: 100% !important;
			border-collapse: collapse !important;
		}

		.quote-summary-amounts-cell {
			padding: 0 !important;
			vertical-align: top;
		}

		.quote-summary-amounts-table td,
		.quote-summary-amounts-table th {
			border: 1px solid #595959;
		}

		.quote-summary-amounts-table td.text-right,
		.quote-summary-amounts-table td.text-right strong {
			white-space: nowrap !important;
			vertical-align: middle !important;
		}

		.quote-summary-info-cell {
			vertical-align: top;
		}

		.quote-summary-body table {
			margin: 0 !important;
			width: 100% !important;
			max-width: 100% !important;
			border: none !important;
			border-collapse: collapse !important;
		}

		.quote-suggest-body {
			box-sizing: border-box;
			background: #fff;
			border: none;
			border-top: 1px solid #595959;
			margin: 0;
			padding: 0;
		}

		.quote-suggest-body .qp-suggest-wrap-table {
			border: none !important;
		}

		.quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell {
			min-height: 0;
			height: auto;
			padding: 0 !important;
			overflow: hidden;
		}

		.quote-suggest-body .qp-prod-card,
		.quote-suggest-body .qp-prod-card td {
			border: none !important;
		}

		.quote-suggest-body .qp-prod-img-cell {
			border-bottom: 1px solid #e8e8e8 !important;
		}

		.quote-main-body > table,
		.quote-main-body .quote-footer-table,
		.quote-summary-body > table,
		.quote-suggest-body .qp-suggest-wrap-table,
		.main-container .qp-suggest-wrap-table {
			width: 100% !important;
			max-width: 100%;
			box-sizing: border-box;
		}

		.quote-table {
			width: 100% !important;
			max-width: 100%;
			border-collapse: collapse;
			box-sizing: border-box;
		}

		.quote-header-cell,
		.quote-footer-cell {
			padding: 0 !important;
			margin: 0 !important;
			line-height: 0 !important;
			font-size: 0 !important;
			text-align: center;
			vertical-align: top;
			width: 100%;
			border-left: none !important;
			border-right: none !important;
			background: #fff;
		}

		.quote-header-cell {
			border-top: none !important;
			border-bottom: 1px solid #595959 !important;
		}

		.quote-footer-cell {
			border-top: 1px solid #595959 !important;
			border-bottom: none !important;
		}

		.quote-header-img,
		.quote-footer-img {
			width: 100% !important;
			max-width: 100% !important;
			height: auto !important;
			max-height: <?= defined('HEADER_IMAGE_HEIGHT') ? (int) HEADER_IMAGE_HEIGHT : 184 ?>px;
			object-fit: contain;
			object-position: center center;
			display: block;
			padding: 0 !important;
			margin: 0 auto;
			border: 0;
		}

		.product-items-table td,
		.product-items-table th {
			vertical-align: middle !important;
		}

		.product-items-table .model {
			text-align: left !important;
		}

		.product-items-table .product-item-row td,
		.product-items-table .product-filler-row td {
			height: 30px;
			vertical-align: middle !important;
		}

		.quote-footer-table {
			margin-top: 0 !important;
			border: none !important;
		}

		@media print {
			@page {
				size: A4 portrait;
				margin: 5mm;
			}

			html, body {
				margin: 0;
				padding: 0;
				background: #fff !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.main-container {
				padding: 10px !important;
				max-width: 100% !important;
				width: 100% !important;
				margin: 0 auto !important;
			}

			.quote-wrap {
				width: 100%;
				max-width: 100%;
				margin: 0;
			}

			.quote-suggest-body {
				page-break-before: auto !important;
				break-before: auto !important;
			}

			.qp-suggest-print-header {
				border: none !important;
				border-bottom: 1px solid #595959 !important;
				background: #4a4a4a !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.qp-suggest-print-title {
				color: #fff !important;
			}

			.qp-suggest-print-subtitle {
				color: #e0e0e0 !important;
			}

			.qp-suggest-product-row {
				page-break-inside: auto !important;
				break-inside: auto !important;
			}

			.quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell,
			.quote-suggest-body .qp-prod-card,
			.quote-suggest-body .qp-suggest-print-box,
			.quote-suggest-body .qp-suggest-cell-inner {
				min-height: 0 !important;
				height: auto !important;
				overflow: hidden !important;
				page-break-inside: avoid !important;
				break-inside: avoid-page !important;
			}

			.quote-suggest-body .qp-prod-badge-row {
				height: 14px !important;
				padding: 1px 2px 0 !important;
			}

			.quote-suggest-body .qp-prod-img-cell {
				height: 38px !important;
				padding: 1px !important;
			}

			.quote-suggest-body .qp-prod-img {
				max-height: 34px !important;
			}

			.quote-suggest-body .qp-prod-code-cell {
				font-size: 8.5px !important;
				line-height: 1.1 !important;
				padding: 1px 2px 0 !important;
			}

			.quote-suggest-body .qp-prod-name-cell {
				min-height: 16px !important;
				max-height: 22px !important;
				font-size: 8px !important;
				line-height: 1.1 !important;
				padding: 1px 2px 0 !important;
			}

			.quote-suggest-body .qp-prod-price-cell {
				padding: 1px 2px 2px !important;
			}

			.quote-suggest-body .qp-prod-price-line,
			.quote-suggest-body .qp-prod-price {
				color: #0a5c24 !important;
				font-weight: bold !important;
				font-size: 9px !important;
				line-height: 1.1 !important;
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
			}

			.quote-suggest-body .qp-prod-unit {
				color: #333333 !important;
				font-weight: 600 !important;
				font-size: 8px !important;
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
			}

			.quote-suggest-body .qp-suggest-cat-header {
				padding: 2px 5px !important;
				font-size: 10px !important;
				page-break-after: avoid !important;
				break-after: avoid !important;
			}

			.quote-suggest-body .qp-suggest-print-header {
				padding: 2px 5px !important;
			}

			.quote-suggest-body .qp-suggest-print-title {
				font-size: 11px !important;
				line-height: 1.2 !important;
			}

			.quote-suggest-body .qp-suggest-print-subtitle {
				font-size: 8.5px !important;
				line-height: 1.1 !important;
			}

			.qp-suggest-print-cell-empty {
				display: none !important;
			}

			.quote-summary-body {
				page-break-inside: avoid !important;
				break-inside: avoid-page !important;
				page-break-before: auto !important;
				break-before: auto !important;
				border: none !important;
				border-top: 1px solid #595959 !important;
			}

			.quote-summary-totals-block {
				page-break-inside: avoid !important;
				break-inside: avoid-page !important;
			}

			.quote-summary-details-row,
			.quote-summary-terms-table,
			.quote-summary-details-table {
				page-break-inside: avoid !important;
				break-inside: avoid-page !important;
			}

			.quote-summary-body table {
				margin: 0 !important;
				width: 100% !important;
				max-width: 100% !important;
				border: none !important;
			}

			.quote-footer-wrap,
			.quote-footer-table,
			.quote-hsn-table {
				page-break-inside: avoid !important;
				break-inside: avoid-page !important;
				page-break-before: auto !important;
				break-before: auto !important;
			}

			.quote-main-body {
				border: none !important;
			}

			.quote-wrap {
				border: 1px solid #595959 !important;
			}

			.quote-main-body table {
				margin: 0 !important;
				width: 100% !important;
				max-width: 100% !important;
				border: none !important;
			}

			.quote-header-cell,
			.quote-footer-cell {
				padding: 0 !important;
				border-left: none !important;
				border-right: none !important;
			}

			.quote-header-cell {
				border-top: none !important;
			}

			.quote-footer-cell {
				border-bottom: none !important;
			}

			.quote-header-img,
			.quote-footer-img {
				width: 100% !important;
				max-width: 100% !important;
				height: auto !important;
				object-fit: contain !important;
				object-position: center center !important;
				max-height: <?= defined('HEADER_IMAGE_HEIGHT') ? (int) HEADER_IMAGE_HEIGHT : 184 ?>px !important;
			}

			.quote-main-body > table,
			.quote-main-body .quote-footer-table,
			.quote-summary-body > table,
			.quote-suggest-body .qp-suggest-wrap-table {
				width: 100% !important;
			}

			.product-filler-row {
				display: none !important;
			}

			.quote-table td,
			.quote-table th,
			.product-items-table td,
			.product-items-table th {
				padding: 2px 4px !important;
			}

			.quote-summary-terms-cell {
				padding: 3px 5px !important;
			}

			.quote-summary-info-cell {
				padding: 4px 6px !important;
			}

			.quote-summary-amounts-table td,
			.quote-summary-amounts-table th {
				padding: 2px 4px !important;
			}

			.quote-summary-amounts-table td.text-right,
			.quote-summary-amounts-table td.text-right strong {
				white-space: nowrap !important;
				vertical-align: middle !important;
			}

			.qp-suggest-print-grid td.qp-suggest-print-cell {
				padding: 0 !important;
			}

			.qp-suggest-print-grid {
				border-collapse: separate !important;
				border-spacing: 0 !important;
			}

			.qp-suggest-cell-inner {
				padding-left: 4px !important;
				padding-right: 4px !important;
				box-sizing: border-box !important;
			}

			.qp-prod-badge-row {
				height: 18px !important;
				padding: 1px 4px 0 !important;
				text-align: right !important;
			}

			.qp-prod-badge-bar {
				display: flex !important;
				align-items: center !important;
				justify-content: flex-end !important;
				gap: 4px !important;
			}

			.qp-prod-disc-label {
				border: 1px solid #d9534f !important;
				color: #d9534f !important;
				font-size: 8.5px !important;
				padding: 1px 4px !important;
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
			}

			.qp-prod-disc {
				width: 18px !important;
				height: 18px !important;
				line-height: 18px !important;
				font-size: 8px !important;
				-webkit-print-color-adjust: exact !important;
				print-color-adjust: exact !important;
			}

			.qp-prod-img-cell {
				padding: 1px !important;
			}

			.qp-prod-code-cell {
				padding: 1px 4px 0 !important;
				font-size: 9.5px !important;
				line-height: 1.1 !important;
			}

			.qp-prod-name-cell {
				padding: 1px 4px 0 !important;
				font-size: 8.5px !important;
				line-height: 1.1 !important;
			}

			.qp-prod-price-cell {
				padding: 1px 4px 4px !important;
			}

			.qp-prod-disc,
			.qp-suggest-cat-header,
			.qp-suggest-print-header {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
		}

		<?php if ($isPrintMode && !$isAppPdfMode && !$isMpdfMode) { ?>
		html, body {
			margin: 0;
			padding: 0;
			background: #fff;
		}

		.main-container {
			padding: 20px !important;
			max-width: 980px !important;
			width: 100% !important;
			margin: 0 auto !important;
		}
		<?php } ?>
		<?php if ($isPdfExportMode) { ?>
		html, body {
			margin: 0;
			padding: 0;
			background: #fff;
		}

		.mpdf-export-mode .main-container {
			padding: 6px !important;
			max-width: 100% !important;
			width: 100% !important;
			margin: 0 !important;
		}

		.mpdf-export-mode .quote-header-img,
		.mpdf-export-mode .quote-footer-img {
			max-height: 90px !important;
		}

		.mpdf-export-mode table,
		.mpdf-export-mode tr,
		.mpdf-export-mode td,
		.mpdf-export-mode div {
			page-break-inside: auto !important;
			page-break-before: auto !important;
			page-break-after: auto !important;
		}

		.mpdf-export-mode .qp-prod-img-cell,
		.mpdf-export-mode .product-items-table .image-width {
			width: 6% !important;
			min-width: 6% !important;
			max-width: 6% !important;
		}

		.mpdf-export-mode .product-items-table img {
			max-width: 50px !important;
			max-height: 50px !important;
		}
		<?php } ?>
	</style>
	<?php
	if ((!$isAppPdfMode || $isMpdfMode)) {
		require_once('../include/quotation_pi_suggest_products_helper.php');
		if ($isPdfExportMode) {
			echo armor_quotation_pi_mpdf_suggest_styles();
			if ($quotationViewStandalone) {
				echo armor_quotation_pi_suggest_pi_view_overrides();
				echo armor_quotation_pi_order_view_layout_styles();
			}
		} elseif ($quotationViewStandalone) {
			echo armor_quotation_pi_suggest_styles();
		}
	}
	?>
<?php if ($quotationViewStandalone) { ?>
</head>

<body<?= $isPdfExportMode ? ' class="mpdf-export-mode"' : ($isPrintMode ? ' class="print-a4"' : '') ?>>
<?php } ?>
	<div class="main-container">
	<div class="quote-wrap">
	<div class="quote-main-body">
		<table class="quote-table">
			<tbody>
				<tr>
					<td class="quote-header-cell" colspan="16">
						<?php
						if (isset($company_detail_d['image_path']) && $company_detail_d['image_path'] != "") {
						?>
							<img class="quote-header-img" src="<?= SITEURL . HEADER . $company_detail_d['image_path'] ?>" alt="Header">
						<?php
						} else {
						?>
							<img class="quote-header-img" src="<?= SITEURL ?>images/craftbox_header.jpg" alt="Header">
						<?php
						}
						?>
					</td>
				</tr>
				<tr style="background-color: <?= VIEW_COLOR ?>; color: #000;">
					<td colspan="16" align="center" style="color: #000;"><b>Rate Confirmation Quotation</b></td>
				</tr>
			</tbody>
		</table>
		<table class="quote-table">
			<tbody class="<?= $cl; ?>">
				<tr>
					<td colspan="8" rowspan="4">
						Buyer
						<h5 style="font-weight: 600;text-transform: uppercase;"><strong><?php echo $cart_detail_d['company_name']; ?></strong></h5>
						<p style="margin:0"><?php echo wordwrap($cart_detail_d['address'], 40, "<br>\n") . "  <br/>" . $cart_detail_d['city'] . " , " . $cart_detail_d['state'] . " , " . $cart_detail_d['country'] ?></p>
						<?php
						if (!empty($customer_address_d['zip'])) {
						?>
							<p style="margin:0"><strong>Pincode :</strong> <?= $customer_address_d['zip']; ?></p>

						<?php
						}
						?>
						<?php
						if (!empty($cart_detail_d['contact_number'])) {
						?>
							<p style="margin:0"><strong>Mobile No. : </strong><?= $cart_detail_d['contact_number'] ?></p>
						<?php
						}
						?>

						<?php
						if (!empty($cart_detail_d['email'])) {
						?>
							<p style="margin:0"><strong>Email : </strong><?= $cart_detail_d['email'] ?></p>
						<?php
						}
						?>
						<?php
						if (!empty($cart_detail_d['gst'])) {
						?>
							<p style="margin:0"><strong>GSTIN / UIN : </strong><?= $cart_detail_d['gst'] ?></p>
						<?php
						}
						?>
					</td>
					<td colspan="8" style="text-align: left;vertical-align: top;">
						<p><b>Quotation No. : </b><?= $cart_detail_d['quotation_no'] ?></p>
						<p>
							<b>Quotation Date : </b>
							<?php
							if ($cart_detail_d['quotation_date'] != "0000-00-00") {
								echo date('d-M-Y', strtotime($cart_detail_d['quotation_date']));
							} else {
								echo "";
							}
							?>
						</p>
						<p><b>Client Code : </b><?= $cart_detail_d['client_code'] ?></p>
					</td>
				</tr>

			</tbody>
		</table>
		<table class="product-items-table quote-table">
			<tbody>
				<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>; color: #000;">
					<th colspan="1" class="text-center" style="width:4%; color: #000;">SR</th>
					<th colspan="1" class="image-width text-center" style="width:8%; color: #000;">Image</th>
					<th colspan="4" class="text-center" style="width:28%; color: #000;">Description of Goods</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Brand<br />Name</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Weight<br />(in kg)</th>
					<th colspan="1" class="text-center" style="width:6%; color: #000;">Qty</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Rate</th>
					<th colspan="1" class="text-center" style="width:8%; color: #000;">Discount %</th>
					<th colspan="1" class="text-center" style="width:10%; color: #000;">Discounted Value</th>
					<th colspan="1" class="text-center" style="width:12%; color: #000;">Total Amount</th>
				</tr>
				<?php
				$ITEMS = array();
				$total_item_discount = 0;
				$total_mrp_amount = 0;
				$items1 = $db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "'", "");
				while ($item1 = mysqli_fetch_assoc($items1)) {
					$item1['display_order'] = $db->rp_getValue("product", "display_order", "id='" . $item1['pro_id'] . "' AND isDelete=0");
					$item1['weight_display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $item1['weight_id'] . "' AND isDelete=0");

					$item1['image_path'] = $db->rp_getValue("product", "image_path", "id='" . $item1['pro_id'] . "' AND 
							isDelete=0", "", 0);
					if ($item1['image_path'] != "") {
						$img = SITEURL . PRODUCT . $item1['image_path'];
					} else {
						$img = SITEURL . "images/no_image_found.jpg";
					}

					$ITEMS[] = $item1;
				}
				if ($items1) {
					$count = 0;
					foreach ($ITEMS as $item) {
						$pro_name = $db->rp_getValue("product", "name", "id='" . $item['pro_id'] . "' AND isDelete=0");
						$size = $db->rp_getValue("weight", "name", "id='" . $item['weight_id'] . "' AND isDelete=0");
						$product_code = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'", 0);
						$unit_id = $db->rp_getValue("product", "display_unit", "id='" . $item['pro_id'] . "' AND isDelete=0");
						$unit_name = $db->rp_getValue("unit", "name", "id='" . $unit_id . "' AND isDelete=0");

						$hsncode = $db->rp_getValue("product", "hsn_code", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);

						$count++;

						if ($cart_detail_d['currency_code'] == 1) {
							$currency = CURR;
						} else if ($cart_detail_d['currency_code'] == 2) {
							$currency = DOLLAR;
						}

						$totalproqty += $item['pro_qty'];
						$totalprice1 += $item['totalprice'];

						$item_original_price = floatval($item['original_price']);
						$item_discount_per = floatval($item['discount']);
						$item_discount_val = floatval($item['discount_amount']);
						if ($item_discount_val <= 0 && $item_discount_per > 0 && $item_original_price > 0) {
							$item_discount_val = ($item_original_price * $item_discount_per) / 100;
						}
						if ($item_discount_per <= 0 && $item_discount_val > 0 && $item_original_price > 0) {
							$item_discount_per = ($item_discount_val / $item_original_price) * 100;
						}
						// if % and amount both missing, derive from MRP vs rate
						if ($item_discount_val <= 0 && $item_original_price > 0 && floatval($item['unitprice']) > 0) {
							$item_discount_val = $item_original_price - floatval($item['unitprice']);
							if ($item_discount_per <= 0 && $item_discount_val > 0) {
								$item_discount_per = ($item_discount_val / $item_original_price) * 100;
							}
						}
						$item_discount_total = $item_discount_val * floatval($item['pro_qty']);
						$total_item_discount += $item_discount_total;
						$total_mrp_amount += ($item_original_price > 0 ? $item_original_price : floatval($item['unitprice'])) * floatval($item['pro_qty']);
				?>
						<tr class="product-item-row">
							<td colspan="1" class="text-center srno"><strong><?php echo $count; ?></strong></td>
							<?php
							if ($item['image_path'] != "") {
							?>
								<td colspan="1" class="image-width text-center"><img style="width: 50px;" src="<?php echo SITEURL . PRODUCT . $item['image_path'] ?>"></td>
							<?php
							} else {
							?>
								<td colspan="1" class="image-width text-center"><img style="width: 50px;" src="<?php echo SITEURL . PRODUCT . 'default.png' ?>"></td>
							<?php
							}
							?>
							<td colspan="4" class="model" style="position: relative;"><?php if ($item['weight_id'] != -1) {
																							echo "<b>#".$product_code."</b>-".$pro_name . " - " . $size;
																						} else {
																							echo "<b>#".$product_code."</b>-".$pro_name;
																						} ?><?= (isset($item['pro_description']) && $item['pro_description'] != "") ? "<br/><br/>" . $item['pro_description'] : "" ?></td>

							<td colspan="1" class="text-center"><?php echo $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $item['order_item_brand_id'] . "'") ?></td>
							<td colspan="1" class="text-center">
								<?php
								$weight = $db->rp_getValue("product_weight_price", "pro_weight", "product_id='" . $item['pro_id'] . "' AND weight_id='" . $item['weight_id'] . "'");
								echo $kg = $weight / 1000;

								$weight_total += $kg;

								?>

							</td>
							<td colspan="1" class="text-center"><?= $item['pro_qty'] ?></td>
							<td colspan="1" class="text-center"><?php echo round($item_original_price > 0 ? $item_original_price : $item['unitprice'], 2); ?></td>
							<td colspan="1" class="text-center"><?php echo round($item_discount_per, 2); ?></td>
							<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item_discount_total, 2); ?></td>
							<td colspan="1" class="text-center"><?php echo $currency . ' ' . round($item['totalprice'], 2); ?></td>
						</tr>
						<?php
					}

				}
				?>
			</tbody>
		</table>
		<?php
		// Resolve Grand Total for print (fallback when DB grand_total_rounded is 0)
		if (!isset($totalprice1)) { $totalprice1 = 0; }
		$total_tax_amt = floatval($totalprice1) - floatval($cart_detail_d['cash_discount_amount']) - floatval($cart_detail_d['additional_discount_amount']) + floatval($cart_detail_d['transport_charge']) + floatval($cart_detail_d['packing_charge']);
		$calc_before_round = $total_tax_amt + floatval($cart_detail_d['igst_amount']) + floatval($cart_detail_d['tcs_amount']);
		$display_grand_total = floatval($cart_detail_d['grand_total_rounded']);
		if ($display_grand_total <= 0) {
			$display_grand_total = floatval($cart_detail_d['grand_total']);
		}
		if ($display_grand_total <= 0) {
			$display_grand_total = round($calc_before_round);
		}
		$display_roundoff = $cart_detail_d['roundoff'];
		if ((string)$display_roundoff === '' || $display_roundoff === null) {
			$display_roundoff = round($calc_before_round) - $calc_before_round;
		}
		// Summary layout uses separate terms row + details block (no rowspan) for clean print breaks.
		?>
	</div><!-- /.quote-main-body -->
	<?php if (!$isAppPdfMode || $isMpdfMode) { ?>
	<div class="quote-suggest-body">
	<?php armor_quotation_pi_echo_suggest_block_for_quotation($db, $quotation_id, $quotationViewEmbedded); ?>
	</div>
	<?php } ?>
	<div class="quote-summary-body">
		<table class="quote-table quote-summary-terms-table">
			<tbody>
				<tr class="font-size">
					<td colspan="16" class="quote-summary-terms-cell">
						<span class="font-13"><b>Terms & Condition : </b></span><br>
						<div class="row">
							<div class="col-md-12">
								<span class="font-13"><?php echo html_entity_decode($cart_detail_d['terms_comdition']); ?></span><br>
							</div>
						</div>
						<span class="font-13" style="color: red;"><b>This quotation is valid for 7 days.</b></span>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="quote-summary-totals-block">
		<table class="quote-table quote-summary-details-table">
			<tbody class="<?= $cl; ?>">
				<tr class="font-size quote-summary-details-row">
					<td colspan="8" class="quote-summary-info-cell" valign="top">
						<span class="font-13"><b>Grand Total In Words</b> :
							<?php
							$grand_total_words = $ntw->rp_convertNumToWord($display_grand_total);
							echo ucwords(strtolower($grand_total_words)); ?>
						</span>
						<br>
						<span class="font-13">
							<b>Bank Details : </b>
							<?php
							if (isset($company_detail_d['bank_details']) && $company_detail_d['bank_details'] != "") {
								echo html_entity_decode($company_detail_d['bank_details']);
							} else {
							?>
								Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
								Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?>
							<?php
							}
							?>
						</span><br>
						<span class="font-13"><b>Remarks</b></span><br>
						<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span><br>
						<span style="color: red;">Contact Sales Person : <?= strip_tags($cart_detail_d['faithfully']) ?> &nbsp; </span>
						<?php
						$modified_name = explode(",", $cart_detail_d['modified_by']);
						$last_modified_id = array_slice($modified_name, -1)[0];
						$modified_by_name = $db->rp_getValue("dealer_distributor_network", "name", "id='" . $last_modified_id . "'");
						?>
						<br /><span style="color: red;">Edited By : <?= $modified_by_name ?> &nbsp; </span>
					</td>
					<td colspan="8" class="quote-summary-amounts-cell" valign="top">
						<table class="quote-table quote-summary-amounts-table" width="100%">
							<tbody>
				<tr>
					<td colspan="2" class="text-left font-13"><strong>Discount</strong></td>
					<td colspan="2" class="text-center font-13"><strong><?php
						$overall_discount_per = ($total_mrp_amount > 0) ? round(($total_item_discount / $total_mrp_amount) * 100, 2) : 0;
						echo rtrim(rtrim(number_format($overall_discount_per, 2, '.', ''), '0'), '.') . '%';
					?></strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($total_item_discount, 2); ?></strong></td>

				</tr>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Sub Total</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($totalprice1, 2); ?></strong></td>
				</tr>

				<?php if ($cart_detail_d['cash_discount_amount'] != "" && $cart_detail_d['cash_discount_amount'] != "0") { ?>
					<tr>
						<td colspan="4" class="text-left font-13"><strong>Cash Discount</strong></td>
						<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['cash_discount_amount'], 2); ?></strong></td>
					</tr>
				<?php } ?>

				<?php if ($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0") { ?>
					<tr>
						<td colspan="4" class="text-left font-13"><strong>Additional Discount</strong></td>
						<td colspan="4" class="text-right font-13"><strong><?php echo $currency . ' ' . $db->rp_number_format($cart_detail_d['additional_discount_amount'], 2); ?></strong></td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Total Taxable Amount</strong></td>

					<?php
					$Invoice = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
					$InvoiceIds = array();
					while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
						$InvoiceIds[] = $Invoice_d['id'];
					}
					$InvoiceIds = implode(",", $InvoiceIds);
					$total_pro_taxable = $db->rp_getValue("quotation_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
					?>
					<td colspan="4" class="text-right rate "><strong><?php echo CURR . ' ' . number_format($total_tax_amt, 2); ?></strong></td>

				</tr>
				<?php
				if ($cart_detail_d['igst_amount'] != "0") {
					if ($cart_detail_d['type_of_executive'] == 8) {
						if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
				?>
							<tr>
								<td colspan="4" class="text-left"><strong>C GST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format(($cart_detail_d['igst_amount']) / 2, 2)) ?></strong> </td>
							</tr>
							<tr>
								<td colspan="4" class="text-left"><strong>S GST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
							</tr>
						<?php
						} else {
						?>
							<tr>
								<td colspan="4" class="text-left"><strong>IGST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
							</tr>
						<?php
						}
					} else {
						if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
						?>
							<tr>
								<td colspan="4" class="text-left"><strong>C GST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong> </td>
							</tr>
							<tr>
								<td colspan="4" class="text-left"><strong>S GST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'] / 2, 2)) ?></strong></td>
							</tr>
						<?php
						} else {
						?>
							<tr>
								<td colspan="4" class="text-left"><strong>IGST</strong></td>
								<td colspan="4" class="text-right "><strong><?= ($currency . $db->rp_number_format($cart_detail_d['igst_amount'], 2)) ?></strong></td>
							</tr>
					<?php
						}
					}
				}
				?>
				<?php
				if ($cart_detail_d['tcs_amount'] != "" && $cart_detail_d['tcs_amount'] != "0") {
				?>
					<tr>
						<td colspan="4">
							<strong>TCS (<?= TCS_CHARGE_IN_PER ?>%)</strong>
						</td>
						<td colspan="4" class="text-right"><strong><?= $currency . number_format($cart_detail_d['tcs_amount'], 2) ?></strong></td>
					</tr>
				<?php
				}
				?>
				<tr>
					<td colspan="4">
						<strong>Round Off</strong>
					</td>
					<td colspan="4" class="text-right"><strong>
							<?php echo $currency . $display_roundoff; ?>
						</strong></td>
				</tr>
				<tr style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;">
					<td colspan="4">
						<strong>Grand Total</strong>
					</td>
					<td colspan="4" class="text-right" style="background-color: <?= GRAND_TOTAL_COLOR ?>;font-size: 16px;"><strong>
							<?php
							echo $currency . ' ' . $db->rp_number_format($display_grand_total, 2);
							?>
						</strong>
					</td>
				</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
		<?php
		if ($cart_detail_d['igst_amount'] != 0) {
		?>

			<!-- hsn summary -->
			<table class="quote-table quote-hsn-table">
				<?php
				if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
					$gst_or_igst = "Total GST Rate";
					$gst_or_igst1 = "Total GST Amount";
				} else {
					$gst_or_igst = "IGST% Rate";
					$gst_or_igst1 = "IGST Amount";
				}
				?>
				<tr>
					<th class="text-center" colspan="2">HSN/SAC</th>
					<!-- <th class="text-center" colspan="1">Qty</th> -->
					<th class="text-center" colspan="2">Taxable Value</th>
					<th class="text-center" colspan="2"><?php echo $gst_or_igst; ?></th>
					<th class="text-center" colspan="2"><?php echo $gst_or_igst1; ?></th>
					<th class="text-center" colspan="1">CGST% Rate</th>
					<th class="text-center" colspan="2">CGST Amount</th>
					<th class="text-center" colspan="2">SGST% Rate</th>
					<th class="text-center" colspan="3">SGST Amount</th>
				</tr>
				<?php
				$ITEMS = array();
				$items1 = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' GROUP BY hsn_code", "", 0);
				while ($item = mysqli_fetch_assoc($items1)) {
					// echo $items1;exit;
					$ITEMS[] = $item1;
					if ($items1) {
						$gst_rate = $db->rp_getValue("product", "igst", "id='" . $item['pro_id'] . "' AND isDelete=0", 0);
						$count = 0;
						$totalprice = 0;
						$final_price = 0;
						$boxqty = 0;
						$cartoonqty = 0;
						$totalproqty = 0;
						$totalrate = 0;
						$totaldiscount = 0;
						$gst_per_amount = 0;
						$GST = 0;

						if ($cart_detail_d['igst_amount'] != 0) {
							$GST = $gst_rate;
							$CGST = $gst_rate / 2;
							$SGST = $gst_rate / 2;
						} else {
							$GST = "";
							$CGST = "";
							$SGST = "";
						}

						$Invoice = $db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $quotation_id . "' AND hsn_code='" . $item['hsn_code'] . "'", "", 0);
						$InvoiceIds = array();
						while ($Invoice_d = mysqli_fetch_assoc($Invoice)) {
							$InvoiceIds[] = $Invoice_d['id'];
						}
						$InvoiceIds = implode(",", $InvoiceIds);
						// echo $InvoiceIds;exit;
						$total_pro_qty = $db->rp_getValue("quotation_product_item", "SUM(pro_qty)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
						$total_pro_taxable = $db->rp_getValue("quotation_product_item", "SUM(taxable)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);

						$cash_amount = ($total_pro_taxable * $cart_detail_d['cash_discount']) / 100;
						if ($cash_amount > $total_pro_taxable) {
							// $SubPrice=$cash_amount-$total_pro_taxable;
							$SubPrice = $total_pro_taxable;
						} else {
							// $SubPrice=$total_pro_taxable-$cash_amount;
							$SubPrice = $total_pro_taxable;
						}
						// $gst_per_amount=($SubPrice*$GST)/100; 							
						if ($cart_detail_d['igst_amount'] != 0) {
							$gst_per_amount += $db->rp_getValue("quotation_product_item", "SUM(igst_amount)", "id In (" . $InvoiceIds . ") AND isDelete=0", 0);
							if (strtolower(CLIENT_STATE) == strtolower($cart_detail_d['state'])) {
								$cgst_per_amount = ($gst_per_amount) / 2;
								$sgst_per_amount = ($gst_per_amount) / 2;
								// $cgst_per_amount=($SubPrice*$CGST)/100;
								// $sgst_per_amount=($SubPrice*$SGST)/100;
								$CGST = $GST / 2;
								$SGST = $GST / 2;
							} else {
								$cgst_per_amount = "";
								$sgst_per_amount = "";
								$CGST = "";
								$SGST = "";
							}
						} else {
							$gst_per_amount = "";
							$cgst_per_amount = "";
							$sgst_per_amount = "";
							$CGST = "";
							$SGST = "";
						}
				?>
						<tr>
							<td colspan="2" class="box_qty " style="text-align: center;"><?= $db->rp_getValue("product", "hsn_code", "isDelete=0 AND id='" . $item['pro_id'] . "'", 0) ?></td>
							<!-- <td colspan="1" class="text-center b_qty "><?php echo $total_pro_qty; ?></td> -->
							<td colspan="2" class="text-right rate "><?php echo CURR . ' ' . number_format($total_pro_taxable, 2); ?></td>
							<td colspan="2" class="text-right b_qty "><?php echo $GST; ?><?= ($GST) ? "%" : ""; ?></td>
							<td colspan="2" class="text-right b_qty "><?= ($gst_per_amount) ? CURR : ""; ?><?= number_format($gst_per_amount, 2);  ?></td>
							<td colspan="1" class="text-right b_qty "><?php echo $CGST; ?><?= ($CGST) ? "%" : ""; ?></td>
							<td colspan="2" class="text-right b_qty "><?= ($CGST) ? CURR : ""; ?><?= number_format($cgst_per_amount, 2);  ?></td>
							<td colspan="2" class="text-right b_qty "><?php echo $SGST; ?><?= ($SGST) ? "%" : ""; ?></td>
							<td colspan="3" class="text-right b_qty "><?= ($SGST) ? CURR : ""; ?><?= number_format($sgst_per_amount, 2);  ?></td>
						</tr>
				<?php
						$Total_total_pro_qty += $total_pro_qty;
						$Total_total_pro_taxable += $total_pro_taxable;
						$Total_gst_per_amount += $gst_per_amount;
						$Total_cgst_per_amount += $cgst_per_amount;
						$Total_sgst_per_amount += $sgst_per_amount;
					}
				}
				?>
				<tr>
					<td colspan="2" class="text-center"><strong>Total</strong></td>
					<!-- <td colspan="1" class="text-right"><b><?php echo $Total_total_pro_qty; ?></b></td> -->
					<td colspan="2" class="text-right"><b><?php echo  CURR . ' ' . number_format($Total_total_pro_taxable, 2); ?></b></td>
					<td colspan="2" class="text-right"><b><?php ?></b></td>
					<td colspan="2" class="text-right"><b><?= ($Total_gst_per_amount) ? CURR : ""; ?><?= number_format($Total_gst_per_amount, 2); ?></b></td>
					<td colspan="1" class="text-right"><b><?php ?></b></td>
					<td colspan="2" class="text-right"><b><?= ($Total_cgst_per_amount) ? CURR : ""; ?><?= number_format($Total_cgst_per_amount, 2); ?></b></td>
					<td colspan="2" class="text-right"><b><?php ?></b></td>
					<td colspan="3" class="text-right"><b><?= ($Total_sgst_per_amount) ? CURR : ""; ?><?= number_format($Total_sgst_per_amount, 2); ?></b></td>
				</tr>
			</table>
		<?php
		}
		?>
		<!-- <table style="width:250mm!important;">
				<tbody>
					<tr>
						<td colspan="8" rowspan="4" class="no-border-right text-left">
							<br><br><br><br>
							<strong style="margin-right: 36px;">Customer Signature</strong>
						</td> 
						<td colspan="8" rowspan="4" class="text-right">
							<strong style="margin-right: 25px;">For,
								<?php
								if (isset($company_detail_d['name']) && $company_detail_d['name'] != "") {
									echo $company_detail_d['name'];
								} else {
									echo CLIENT_BRAND_NAME;
								}
								?>
							<br>
							</strong>
							<br><br><br>
							<strong  style="margin-right: 25px;">
							Authorised SIgnatory</strong>
						</td>
					</tr>
				</tbody> 
			</table> -->
	</div><!-- /.quote-summary-body -->
		<div class="quote-footer-wrap">
		<table class="quote-footer-table quote-table">
			<tbody>
				<tr>
					<td class="quote-footer-cell" colspan="16">
						<?php
						if (isset($company_detail_d['footer_image_path']) && $company_detail_d['footer_image_path'] != "") {
						?>
							<img class="quote-footer-img" src="<?= SITEURL . FOOTER . $company_detail_d['footer_image_path'] ?>" alt="Footer">
						<?php
						} else {
						?>
							<img class="quote-footer-img" src="<?= SITEURL ?>images/craftbox_header.jpg" alt="Footer">
						<?php
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div><!-- /.quote-wrap -->
	</div>
<?php if ($isPrintMode && !$isAppPdfMode && !$isMpdfMode) { ?>
<style>
	.quote-print-toolbar {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 9999;
		background: #2b3643;
		color: #fff;
		padding: 10px 16px;
		text-align: center;
		font-family: Arial, sans-serif;
		font-size: 14px;
	}
	.quote-print-toolbar button {
		background: #36c6d3;
		border: none;
		color: #fff;
		padding: 8px 18px;
		margin-left: 10px;
		cursor: pointer;
		font-size: 14px;
		border-radius: 3px;
	}
	@media print {
		.quote-print-toolbar {
			display: none !important;
		}
	}
</style>
<div class="quote-print-toolbar">
	Quotation ready — Quote items, Suggested Products, Terms & Footer
	<button type="button" onclick="quotePrintNow();">Print Quotation</button>
</div>
<?php } ?>
<?php if ($quotationViewStandalone) { ?>
<script type="text/javascript">
(function() {
	var quotePrintTitle = <?= json_encode($quotationPrintTitle) ?>;

	window.quotePrintNow = function() {
		document.title = quotePrintTitle;
		quoteWaitForImages(function() {
			setTimeout(function() {
				window.print();
			}, 300);
		});
	};

	function quoteWaitForImages(callback) {
		var imgs = document.images;
		if (!imgs || !imgs.length) {
			callback();
			return;
		}
		var pending = 0;
		var i;
		for (i = 0; i < imgs.length; i++) {
			if (!imgs[i].complete) {
				pending++;
			}
		}
		if (!pending) {
			callback();
			return;
		}
		function done() {
			pending--;
			if (pending <= 0) {
				callback();
			}
		}
		for (i = 0; i < imgs.length; i++) {
			if (!imgs[i].complete) {
				imgs[i].addEventListener('load', done);
				imgs[i].addEventListener('error', done);
			}
		}
	}

<?php if ($isPrintMode && !$isAppPdfMode && !$isMpdfMode) { ?>
	window.addEventListener('load', function() {
		document.title = quotePrintTitle;
		quoteWaitForImages(function() {
			setTimeout(function() {
				window.print();
			}, 1200);
		});
	});
	<?php } ?>
})();
</script>
</body>

</html>
<?php } ?>