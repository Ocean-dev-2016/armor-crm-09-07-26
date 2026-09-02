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
			body { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; background: #fff; }
			table { border-collapse: collapse; }
			table, td, th { border: 1px solid #595959; }
			.main-container { width: 100%; max-width: 100%; padding: 10px; }
			.quote-wrap, .quote-main-body, .quote-suggest-body, .quote-summary-body { width: 100%; }
			.quote-header-img, .quote-footer-img { max-width: 100% !important; max-height: 90px !important; width: auto !important; height: auto !important; }
			.product-items-table img { max-width: 50px !important; max-height: 50px !important; }
			img { max-width: 50px; max-height: 50px; }
			.qp-suggest-wrap-table, .qp-suggest-wrap-table td { border: none !important; }
			.qp-suggest-print-section { width: 100%; font-size: 9px; }
			.qp-suggest-print-header { text-align: center; padding: 10px 8px; background: #4a4a4a; color: #fff; border-bottom: 1px solid #595959; }
			.qp-suggest-print-title { font-size: 14px; font-weight: bold; text-transform: uppercase; color: #fff; }
			.qp-suggest-print-subtitle { font-size: 10px; color: #e0e0e0; }
			.qp-suggest-print-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
			.qp-suggest-print-grid td.qp-suggest-print-cell { width: 25%; vertical-align: top; border: 1px solid #595959; padding: 0 !important; background: #fff; }
			.qp-suggest-print-cell-empty { border: none !important; background: transparent !important; }
			.qp-suggest-cat-header { background: #e8e8e8; font-weight: bold; text-align: center; font-size: 10px; padding: 4px; }
			.qp-suggest-cell-inner { padding: 0 4px 2px; }
			.qp-prod-card { width: 100%; border-collapse: collapse; table-layout: fixed; }
			.qp-prod-card td { border: none !important; vertical-align: top; }
			.qp-prod-badge-row { text-align: right !important; padding: 1px 2px 0 !important; }
			.qp-prod-disc-label { border: 1px solid #d9534f; color: #d9534f; font-size: 8px; font-weight: bold; padding: 1px 3px; background: #fff; }
			.qp-prod-disc { background: #e74c3c; color: #fff; font-size: 8px; font-weight: bold; padding: 1px 4px; border-radius: 8px; }
			.qp-prod-img-cell { height: 38px; background: #f7f7f7; text-align: center; vertical-align: middle !important; padding: 1px !important; }
			.qp-prod-img, .qp-suggest-print-grid img { max-width: 42px !important; max-height: 34px !important; }
			.qp-prod-code-cell { font-size: 8.5px; font-weight: 600; color: #555 !important; }
			.qp-prod-name-cell { font-size: 8px; line-height: 1.1; color: #000 !important; }
			.qp-prod-price-line { color: #0a5c24 !important; font-weight: bold; font-size: 9px; }
			.qp-prod-unit { color: #333 !important; font-size: 8px; font-weight: 600; }
			.qp-suggest-product-row, .qp-prod-card, .qp-suggest-print-box { page-break-inside: auto !important; }
		</style>';
	}
}

if (!function_exists('armor_pdf_export_sanitize_html')) {
	function armor_pdf_export_sanitize_html($html)
	{
		$html = (string) $html;
		$html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
		$html = preg_replace('/<style[^>]*>\s*\.quote-print-toolbar[\s\S]*?<\/style>/i', '', $html);
		$html = preg_replace('/<div[^>]*class="[^"]*quote-print-toolbar[^"]*"[^>]*>[\s\S]*?<\/div>/i', '', $html);
		// Drop embedded print CSS — mPDF uses armor_pdf_export_mpdf_css() (avoids page-break-inside:avoid explosion).
		$html = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html);
		$html = preg_replace('/background[^:]*:\s*[^;]*url\([^)]*\)[^;]*;?/i', '', $html);
		$html = preg_replace('/\sclass="[^"]*addwatermark[^"]*"/i', '', $html);
		$html = preg_replace('/(<\/tr>)\s*(?:<br\s*\/?>\s*)+/i', '$1', $html);
		$html = preg_replace('/(?:<br\s*\/?>\s*)+(<tr\b)/i', '$1', $html);
		$html = preg_replace('/(<tbody[^>]*>)\s*(?:<br\s*\/?>\s*)+/i', '$1', $html);
		$html = preg_replace('/(?:<br\s*\/?>\s*)+(<\/tbody>)/i', '$1', $html);
		$html = preg_replace('/(<br\s*\/?>\s*){4,}/i', '<br />', $html);
		$html = preg_replace('/\s+on\w+="[^"]*"/i', '', $html);
		$html = preg_replace('/position\s*:\s*absolute\s*;?/i', '', $html);
		$html = preg_replace('/display\s*:\s*flex[^;]*;?/i', '', $html);
		$html = preg_replace('/width:\s*250mm[^;]*;?/i', 'width:100%;', $html);
		$html = preg_replace('/page-break-inside\s*:\s*avoid[^;]*;?/i', 'page-break-inside:auto;', $html);
		$html = preg_replace('/break-inside\s*:\s*avoid[^;]*;?/i', 'break-inside:auto;', $html);
		$html = preg_replace('/page-break-before\s*:\s*always[^;]*;?/i', '', $html);
		$html = preg_replace('/page-break-after\s*:\s*always[^;]*;?/i', '', $html);

		require_once dirname(__FILE__) . '/quotation_pdf_image_helper.php';
		// Local cached JPEG paths — faster than base64 for mPDF on live server.
		$html = armor_pdf_compress_images_in_html($html, true);
		$html = armor_pdf_strip_remaining_remote_images($html);

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

		// Never HTTP-fetch on same server during API export — causes Apache worker deadlock (hang).
		if (!$ok && !$embedAttempted && defined('ADMINSITEURL')) {
			$query = array_merge($requestParams, array('print' => '1'));
			$bodyUrl = rtrim(ADMINSITEURL, '/') . '/' . ltrim($viewFileName, '/') . '?' . http_build_query($query);
			$html = @file_get_contents($bodyUrl);
			if (empty($html) && function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $bodyUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_TIMEOUT, 180);
				$html = curl_exec($ch);
				curl_close($ch);
			}
			$html = (string) $html;
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

		$mpdf = new mPDF('', 'A4', 10, 'sans-serif', 1, 3, 3, 3, 0, 0, 'P');
		$mpdf->autoScriptToLang = true;
		$mpdf->baseScript = 1;
		$mpdf->autoLangToFont = true;
		if (property_exists($mpdf, 'img_dpi')) {
			$mpdf->img_dpi = 72;
		}
		if (method_exists($mpdf, 'SetCompression')) {
			$mpdf->SetCompression(true);
		}
		if (property_exists($mpdf, 'simpleTables')) {
			$mpdf->simpleTables = true;
		}
		if (property_exists($mpdf, 'allow_html_optional_endtags')) {
			$mpdf->allow_html_optional_endtags = true;
		}
		if (property_exists($mpdf, 'shrink_tables_to_fit')) {
			$mpdf->shrink_tables_to_fit = 1;
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
