<?php
/**
 * Shared Order / Quotation PDF export (Web + App).
 * - Same templates as print view (suggested products included)
 * - Compressed images (webp sources supported, embedded as JPEG for mPDF)
 */

if (!function_exists('armor_pdf_export_bbs_dir')) {
	function armor_pdf_export_bbs_dir()
	{
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bbsales_tracking' . DIRECTORY_SEPARATOR;
	}
}

if (!function_exists('armor_pdf_export_orders_dir')) {
	function armor_pdf_export_orders_dir()
	{
		$dir = armor_pdf_export_bbs_dir() . 'pdf' . DIRECTORY_SEPARATOR . 'orders' . DIRECTORY_SEPARATOR;
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		return $dir;
	}
}

if (!function_exists('armor_pdf_export_mpdf_css')) {
	function armor_pdf_export_mpdf_css()
	{
		return '<style>
			body { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; background: #fff; color: #000; }
			.main-container, .mainDiv, .quote-wrap { width: 100%; max-width: 100%; margin: 0; padding: 0; }
			.quote-wrap { border: 1px solid #595959; box-sizing: border-box; background: #fff; }
			table { width: 100% !important; border-collapse: collapse !important; background-color: #fff; margin: 0; }
			table, td, th { border: 1px solid #595959; font-size: 11px; }
			td, th { padding: 4px 6px; }

			.quote-header-cell, .quote-footer-cell {
				padding: 0 !important;
				margin: 0 !important;
				line-height: 0 !important;
				font-size: 0 !important;
				text-align: center !important;
				vertical-align: top !important;
				width: 100% !important;
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
			.quote-header-img, .quote-footer-img {
				width: 100% !important;
				max-width: 100% !important;
				height: auto !important;
				max-height: 180px !important;
				display: block !important;
				margin: 0 !important;
				padding: 0 !important;
			}

			.text-center { text-align: center !important; }
			.text-right { text-align: right !important; }
			.text-left { text-align: left !important; }
			.srno { width: 4% !important; }
			.image-width { width: 8% !important; text-align: center !important; padding: 2px !important; }
			.image-width img { max-width: 48px; max-height: 48px; display: inline-block; }
			.box_qty { text-align: center !important; }
			.quote-table { width: 100% !important; border-collapse: collapse !important; }
			.product-items-table { width: 100% !important; table-layout: fixed !important; }
			.quote-summary-terms-table, .quote-summary-details-table, .quote-footer-table { width: 100% !important; }
			.font-13 { font-size: 11px !important; }

			/* Suggested Products Grid */
			.qp-suggest-print-section { width: 100%; margin: 0; }
			.qp-suggest-print-header { text-align: center; padding: 6px; background: #595959; color: #fff; border-top: 1px solid #595959; border-bottom: 1px solid #595959; }
			.qp-suggest-print-title { font-size: 12px; color: #fff; font-weight: bold; text-transform: uppercase; }
			.qp-suggest-print-subtitle { font-size: 8.5px; color: #f0f0f0; }
			.qp-suggest-print-grid { width: 100% !important; border-collapse: collapse; table-layout: fixed; border-left: none !important; border-right: none !important; }
			.qp-suggest-cat-header { background: #e8e8e8; font-weight: bold; text-align: center; font-size: 9.5px; padding: 3px; border: 1px solid #595959; border-left: none !important; border-right: none !important; }
			.qp-suggest-print-cell { width: 25%; vertical-align: top; border: 1px solid #595959; padding: 2px; background: #fff; }
			.qp-prod-card { width: 100%; text-align: center; padding: 2px; }
			.qp-prod-disc-label { border: 1px solid #d9534f; color: #d9534f; font-size: 7.5px; padding: 1px 3px; border-radius: 2px; }
			.qp-prod-img { max-width: 44px; max-height: 34px; display: inline-block; }
			.qp-prod-code-cell { font-size: 8px; font-weight: bold; color: #555; }
			.qp-prod-name-cell { font-size: 7.5px; line-height: 1.1; color: #000; height: 18px; overflow: hidden; }
			.qp-prod-price-line { color: #0a5c24; font-size: 8.5px; font-weight: bold; }
			.qp-prod-unit { color: #333; font-size: 7.5px; }

			.quote-footer-wrap { width: 100%; margin: 0; padding: 0; border-top: 1px solid #595959; }
		</style>';
	}
}

if (!function_exists('armor_pdf_export_sanitize_html')) {
	function armor_pdf_export_sanitize_html($html)
	{
		$html = (string) $html;

		$html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
		$html = preg_replace('/<div[^>]*class="[^"]*quote-print-toolbar[^"]*"[^>]*>[\s\S]*?<\/div>/i', '', $html);

		// Clean CSS replacement
		$html = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html);
		$html = armor_pdf_export_mpdf_css() . $html;

		$html = preg_replace('/background[^:]*:\s*[^;]*url\([^)]*\)[^;]*;?/i', '', $html);
		$html = preg_replace('/\sclass="[^"]*addwatermark[^"]*"/i', '', $html);
		$html = preg_replace('/width:\s*250mm[^;]*;?/i', 'width:100%;', $html);

		require_once dirname(__FILE__) . '/quotation_pdf_image_helper.php';
		// Convert all images to compressed JPEG Base64 Data URIs so they render reliably in mPDF
		$html = armor_pdf_compress_images_in_html($html, false);

		return $html;
	}
}

if (!function_exists('armor_pdf_export_fetch_view_html')) {
	function armor_pdf_export_fetch_view_html($viewFileName, $requestParams, $validMarkers = array())
	{
		$bbsDir = armor_pdf_export_bbs_dir();
		$viewFile = $bbsDir . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $viewFileName), DIRECTORY_SEPARATOR);
		$oldGet = isset($_GET) ? $_GET : array();
		$oldReq = isset($_REQUEST) ? $_REQUEST : array();
		$cwd = getcwd();
		$html = '';
		$embedAttempted = false;

		foreach ($requestParams as $k => $v) {
			$_GET[$k] = $v;
			$_REQUEST[$k] = $v;
		}
		// Same HTML as web Print (print=1) — suggested products + layout match browser.
		$_GET['print'] = '1';
		$_REQUEST['print'] = '1';
		unset($_GET['app_pdf'], $_GET['mpdf'], $_REQUEST['app_pdf'], $_REQUEST['mpdf']);

		if (is_file($viewFile)) {
			if (!defined('ARMOR_PDF_EXPORT_EMBED')) {
				define('ARMOR_PDF_EXPORT_EMBED', 1);
			}
			$embedAttempted = true;
			@chdir($bbsDir);
			ob_start();
			@include $viewFile;
			$html = ob_get_clean();
			if ($cwd) {
				@chdir($cwd);
			}
		}

		$_GET = $oldGet;
		$_REQUEST = $oldReq;

		$html = (string) $html;
		$ok = trim($html) !== '';
		if ($ok && !empty($validMarkers)) {
			$ok = false;
			foreach ($validMarkers as $marker) {
				if (stripos($html, $marker) !== false) {
					$ok = true;
					break;
				}
			}
		}

		return $html;
	}
}

if (!function_exists('armor_pdf_export_create_mpdf')) {
	function armor_pdf_export_create_mpdf()
	{
		require_once dirname(__FILE__) . '/armor_mbstring_bootstrap.php';

		@ini_set('memory_limit', '1024M');
		@set_time_limit(300);

		if (!armor_prepare_mpdf_environment('1024M', 300)) {
			return null;
		}

		$mpdf = new mPDF('', 'A4', 10, 'sans-serif', 4, 4, 6, 6, 0, 0, 'P');
		$mpdf->pdf_version = '1.4';
		$mpdf->autoScriptToLang = true;
		$mpdf->baseScript = 1;
		$mpdf->autoLangToFont = true;
		if (property_exists($mpdf, 'showImageErrors')) {
			$mpdf->showImageErrors = false;
		}
		if (property_exists($mpdf, 'img_dpi')) {
			$mpdf->img_dpi = 96;
		}
		if (method_exists($mpdf, 'SetCompression')) {
			$mpdf->SetCompression(true);
		}
		if (property_exists($mpdf, 'simpleTables')) {
			$mpdf->simpleTables = false;
		}
		if (property_exists($mpdf, 'packTableData')) {
			$mpdf->packTableData = true;
		}
		if (method_exists($mpdf, 'SetDisplayMode')) {
			$mpdf->SetDisplayMode('fullpage');
		}

		return $mpdf;
	}
}

if (!function_exists('armor_pdf_export_write_html')) {
	function armor_pdf_export_write_html($mpdf, $html)
	{
		$html = armor_pdf_export_mpdf_css() . (string) $html;
		// Do not split mid-tag — causes hundreds of blank mPDF pages.
		$mpdf->WriteHTML($html);
		return true;
	}
}

if (!function_exists('armor_pdf_export_validate_file')) {
	function armor_pdf_export_validate_file($filePath)
	{
		if (!is_file($filePath) || filesize($filePath) < 100) {
			return false;
		}
		$fh = @fopen($filePath, 'rb');
		if (!$fh) {
			return false;
		}
		$head = fread($fh, 5);
		fclose($fh);
		return ($head === '%PDF-');
	}
}

if (!function_exists('armor_pdf_export_count_pages')) {
	function armor_pdf_export_count_pages($filePath)
	{
		$content = @file_get_contents($filePath);
		if ($content === false || $content === '') {
			return 0;
		}
		if (preg_match_all('/\/Type\s*\/Page\b/', $content, $matches)) {
			return count($matches[0]);
		}
		return 0;
	}
}

if (!function_exists('armor_pdf_export_public_url')) {
	function armor_pdf_export_public_url($relativePath)
	{
		$relativePath = ltrim(str_replace('\\', '/', (string) $relativePath), '/');
		if (defined('ADMINSITEURL')) {
			return rtrim(ADMINSITEURL, '/') . '/pdf/orders/' . $relativePath;
		}
		return $relativePath;
	}
}

if (!function_exists('armor_pdf_export_generate')) {
	function armor_pdf_export_generate($viewFileName, $requestParams, $validMarkers, $saveRelativePath)
	{
		@set_time_limit(300);
		@ini_set('max_execution_time', '300');
		@ini_set('memory_limit', '768M');
		if (function_exists('ignore_user_abort')) {
			@ignore_user_abort(true);
		}
		$html = armor_pdf_export_fetch_view_html($viewFileName, $requestParams, $validMarkers);
		$html = armor_pdf_export_sanitize_html($html);

		if (trim($html) === '') {
			return array('ok' => false, 'error' => 'HTML could not be loaded for PDF.');
		}

		$mpdf = armor_pdf_export_create_mpdf();
		if (!$mpdf) {
			return array('ok' => false, 'error' => 'mPDF/mbstring is not available on server.');
		}

		try {
			armor_pdf_export_write_html($mpdf, $html);
		} catch (Exception $e) {
			return array('ok' => false, 'error' => 'mPDF error: ' . $e->getMessage());
		}
		$pageCount = (property_exists($mpdf, 'page') && (int) $mpdf->page > 0) ? (int) $mpdf->page : 0;
		unset($html);

		$saveRelativePath = str_replace('\\', '/', $saveRelativePath);
		$fullPath = armor_pdf_export_orders_dir() . str_replace('/', DIRECTORY_SEPARATOR, $saveRelativePath);
		$dir = dirname($fullPath);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		if (is_file($fullPath)) {
			@unlink($fullPath);
		}

		try {
			$mpdf->Output($fullPath, 'F');
		} catch (Exception $e) {
			return array('ok' => false, 'error' => 'PDF save error: ' . $e->getMessage());
		}

		if (!armor_pdf_export_validate_file($fullPath)) {
			@unlink($fullPath);
			return array('ok' => false, 'error' => 'PDF file is invalid or was not created.');
		}

		return array(
			'ok' => true,
			'path' => $fullPath,
			'url' => armor_pdf_export_public_url($saveRelativePath),
			'size' => filesize($fullPath),
			'pages' => $pageCount > 0 ? $pageCount : armor_pdf_export_count_pages($fullPath),
		);
	}
}
