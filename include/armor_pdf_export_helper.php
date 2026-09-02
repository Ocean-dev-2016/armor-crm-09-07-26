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
			body { margin: 0; padding: 0; font-family: sans-serif; font-size: 11px; }
			table { border-collapse: collapse; }
			.main-container { width: 100%; max-width: 100%; padding: 4px; }
			.quote-wrap, .quote-main-body, .quote-suggest-body, .quote-summary-body { width: 100%; }
			img { max-width: 50px; max-height: 50px; }
			.quote-header-img, .quote-footer-img { max-width: 100% !important; max-height: 90px !important; width: auto !important; height: auto !important; }
			.qp-suggest-print-header { text-align: center; padding: 6px; background: #4a4a4a; color: #fff; }
			.qp-suggest-print-title { font-size: 12px; font-weight: bold; color: #fff; }
			.qp-suggest-print-subtitle { font-size: 8px; color: #eee; }
			.qp-suggest-cat-header { background: #e8e8e8; font-weight: bold; text-align: center; }
			.qp-suggest-print-grid td { border: 1px solid #595959; width: 25%; }
			.qp-suggest-print-grid img { max-width: 42px !important; max-height: 42px !important; }
		</style>';
	}
}

if (!function_exists('armor_pdf_export_sanitize_html')) {
	function armor_pdf_export_sanitize_html($html)
	{
		$html = (string) $html;
		$html = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $html);
		$html = preg_replace('/<style[^>]*>[\s\S]*?quote-print-toolbar[\s\S]*?<\/style>/i', '', $html);
		$html = preg_replace('/<div[^>]*class="[^"]*quote-print-toolbar[^"]*"[^>]*>[\s\S]*?<\/div>/i', '', $html);
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

		require_once dirname(__FILE__) . '/quotation_pdf_image_helper.php';
		$html = armor_pdf_compress_images_in_html($html, true);

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

		foreach ($requestParams as $k => $v) {
			$_GET[$k] = $v;
			$_REQUEST[$k] = $v;
		}
		$_GET['app_pdf'] = '1';
		$_GET['mpdf'] = '1';
		$_REQUEST['app_pdf'] = '1';
		$_REQUEST['mpdf'] = '1';

		if (is_file($viewFile)) {
			if (!defined('ARMOR_PDF_EXPORT_EMBED')) {
				define('ARMOR_PDF_EXPORT_EMBED', 1);
			}
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

		if (!$ok && defined('ADMINSITEURL')) {
			$query = array_merge($requestParams, array('app_pdf' => '1', 'mpdf' => '1'));
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
		$chunkSize = 180000;
		$html = armor_pdf_export_mpdf_css() . (string) $html;
		if (strlen($html) <= $chunkSize) {
			$mpdf->WriteHTML($html);
			return true;
		}
		$offset = 0;
		$len = strlen($html);
		while ($offset < $len) {
			$mpdf->WriteHTML(substr($html, $offset, $chunkSize));
			$offset += $chunkSize;
		}
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
		if (preg_match_all('/\/Type\s*\/Page[^s]/', $content, $matches)) {
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
		);
	}
}
