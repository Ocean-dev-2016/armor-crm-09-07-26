<?php
/**
 * Load mbstring polyfill before mPDF / API JSON — no server extension required.
 */

if (!function_exists('armor_load_mbstring_polyfill')) {

	function armor_load_mbstring_polyfill()
	{
		if (function_exists('mb_strlen')) {
			return true;
		}

		$root = dirname(__DIR__);
		$candidates = array(
			__DIR__ . '/mbstring_polyfill.php',
			$root . '/bbsales_tracking/include/mbstring_polyfill.php',
		);

		foreach ($candidates as $path) {
			if (is_file($path) && filesize($path) > 50) {
				require_once $path;
				if (function_exists('mb_strlen')) {
					return true;
				}
			}
		}

		return function_exists('mb_strlen');
	}

	function armor_prepare_mpdf_environment($memoryLimit = '1024M', $timeLimit = 180)
	{
		armor_load_mbstring_polyfill();

		if ($memoryLimit !== '') {
			@ini_set('memory_limit', $memoryLimit);
		}
		if ($timeLimit > 0) {
			@set_time_limit($timeLimit);
		}

		if (!function_exists('mb_strlen')) {
			return false;
		}

		if (!class_exists('mPDF', false)) {
			$mpdfFile = dirname(__DIR__) . '/bbsales_tracking/mpdf60/mpdf.php';
			if (!is_file($mpdfFile)) {
				return false;
			}
			require_once $mpdfFile;
		}

		return class_exists('mPDF', false);
	}
}

armor_load_mbstring_polyfill();
