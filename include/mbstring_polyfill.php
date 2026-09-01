<?php
/**
 * mbstring polyfill (canonical copy in include/ for API bootstrap).
 * Used when server php_mbstring extension is disabled.
 */
if (!function_exists('mb_strlen')) {

	if (!defined('MB_CASE_UPPER')) {
		define('MB_CASE_UPPER', 0);
	}
	if (!defined('MB_CASE_LOWER')) {
		define('MB_CASE_LOWER', 1);
	}
	if (!defined('MB_CASE_TITLE')) {
		define('MB_CASE_TITLE', 2);
	}

	function _armor_mb_encoding($encoding = null)
	{
		static $internal = 'UTF-8';
		if ($encoding === null || $encoding === '') {
			return $internal;
		}
		return strtoupper($encoding);
	}

	function mb_internal_encoding($encoding = null)
	{
		static $internal = 'UTF-8';
		if ($encoding !== null && $encoding !== '') {
			$internal = strtoupper($encoding);
			return true;
		}
		return $internal;
	}

	function mb_regex_encoding($encoding = null)
	{
		return mb_internal_encoding($encoding);
	}

	function mb_strlen($str, $encoding = null)
	{
		$str = (string) $str;
		if (function_exists('iconv_strlen')) {
			$len = @iconv_strlen($str, _armor_mb_encoding($encoding));
			if ($len !== false) {
				return $len;
			}
		}
		if (preg_match_all('/./us', $str, $m)) {
			return count($m[0]);
		}
		return strlen($str);
	}

	function mb_substr($str, $start, $length = null, $encoding = null)
	{
		$str = (string) $str;
		if (function_exists('iconv_substr')) {
			if ($length === null) {
				$res = @iconv_substr($str, $start, 2147483647, _armor_mb_encoding($encoding));
			} else {
				$res = @iconv_substr($str, $start, $length, _armor_mb_encoding($encoding));
			}
			if ($res !== false) {
				return $res;
			}
		}
		$chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
		if (!is_array($chars)) {
			return ($length === null) ? substr($str, $start) : substr($str, $start, $length);
		}
		if ($start < 0) {
			$start = count($chars) + $start;
		}
		if ($start < 0) {
			$start = 0;
		}
		if ($length === null) {
			$slice = array_slice($chars, $start);
		} else {
			$slice = array_slice($chars, $start, $length);
		}
		return implode('', $slice);
	}

	function mb_strpos($haystack, $needle, $offset = 0, $encoding = null)
	{
		$haystack = (string) $haystack;
		$needle = (string) $needle;
		if ($needle === '') {
			return 0;
		}
		$hay = preg_split('//u', $haystack, -1, PREG_SPLIT_NO_EMPTY);
		$nee = preg_split('//u', $needle, -1, PREG_SPLIT_NO_EMPTY);
		if (!is_array($hay) || !is_array($nee)) {
			$p = strpos($haystack, $needle, $offset);
			return ($p === false) ? false : $p;
		}
		$hLen = count($hay);
		$nLen = count($nee);
		for ($i = $offset; $i <= $hLen - $nLen; $i++) {
			$ok = true;
			for ($j = 0; $j < $nLen; $j++) {
				if ($hay[$i + $j] !== $nee[$j]) {
					$ok = false;
					break;
				}
			}
			if ($ok) {
				return $i;
			}
		}
		return false;
	}

	function mb_substr_count($haystack, $needle, $encoding = null)
	{
		if ($needle === '') {
			return 0;
		}
		return substr_count((string) $haystack, (string) $needle);
	}

	function mb_strtolower($str, $encoding = null)
	{
		return strtolower((string) $str);
	}

	function mb_strtoupper($str, $encoding = null)
	{
		return strtoupper((string) $str);
	}

	function mb_convert_case($str, $mode, $encoding = null)
	{
		$str = (string) $str;
		if ($mode === MB_CASE_UPPER) {
			return mb_strtoupper($str, $encoding);
		}
		if ($mode === MB_CASE_LOWER) {
			return mb_strtolower($str, $encoding);
		}
		return ucwords(strtolower($str));
	}

	function mb_convert_encoding($str, $to_encoding, $from_encoding = null)
	{
		$str = (string) $str;
		$to = strtoupper((string) $to_encoding);
		$from = $from_encoding === null ? mb_internal_encoding() : strtoupper((string) $from_encoding);
		if ($from === $to || $from === 'UTF-8' && $to === 'UTF-8') {
			return $str;
		}
		if (function_exists('iconv')) {
			$out = @iconv($from, $to . '//IGNORE', $str);
			if ($out !== false) {
				return $out;
			}
		}
		return $str;
	}

	function mb_ereg($pattern, $string, &$regs = null)
	{
		$p = '/' . str_replace('/', '\/', (string) $pattern) . '/u';
		$m = array();
		$ok = @preg_match($p, (string) $string, $m);
		if ($ok && $regs !== null) {
			$regs = $m;
		}
		return $ok ? 1 : false;
	}

	function mb_split($pattern, $string, $limit = -1)
	{
		$p = '/' . str_replace('/', '\/', (string) $pattern) . '/u';
		$parts = @preg_split($p, (string) $string, $limit);
		return is_array($parts) ? $parts : array((string) $string);
	}
}
