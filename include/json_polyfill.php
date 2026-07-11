<?php
/**
 * JSON polyfill — safety net for hosts where the PHP JSON extension is disabled.
 *
 * These functions are only defined when the native json_* functions are missing.
 * When the JSON extension is enabled (normal case), this file does nothing.
 *
 * Root cause it guards against:
 *   Fatal error: Call to undefined function json_encode()
 * which makes every App API return HTTP 500.
 */

if (!function_exists('json_encode')) {
	define('ARMOR_JSON_POLYFILL', 1);

	function _armor_json_encode_value($value)
	{
		if (is_null($value)) {
			return 'null';
		}
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}
		if (is_int($value) || is_float($value)) {
			return (string) $value;
		}
		if (is_string($value)) {
			return _armor_json_encode_string($value);
		}
		if (is_array($value)) {
			$isList = true;
			$expected = 0;
			foreach ($value as $k => $v) {
				if ($k !== $expected) {
					$isList = false;
					break;
				}
				$expected++;
			}
			$parts = array();
			if ($isList) {
				foreach ($value as $v) {
					$parts[] = _armor_json_encode_value($v);
				}
				return '[' . implode(',', $parts) . ']';
			}
			foreach ($value as $k => $v) {
				$parts[] = _armor_json_encode_string((string) $k) . ':' . _armor_json_encode_value($v);
			}
			return '{' . implode(',', $parts) . '}';
		}
		if (is_object($value)) {
			return _armor_json_encode_value(get_object_vars($value));
		}
		return 'null';
	}

	function _armor_json_encode_string($str)
	{
		$result = '';
		$length = strlen($str);
		for ($i = 0; $i < $length; $i++) {
			$char = $str[$i];
			$ord = ord($char);
			switch ($char) {
				case '"':
					$result .= '\\"';
					break;
				case '\\':
					$result .= '\\\\';
					break;
				case '/':
					$result .= '\\/';
					break;
				case "\b":
					$result .= '\\b';
					break;
				case "\f":
					$result .= '\\f';
					break;
				case "\n":
					$result .= '\\n';
					break;
				case "\r":
					$result .= '\\r';
					break;
				case "\t":
					$result .= '\\t';
					break;
				default:
					if ($ord < 32) {
						$result .= sprintf('\\u%04x', $ord);
					} else {
						$result .= $char;
					}
			}
		}
		return '"' . $result . '"';
	}

	function json_encode($value, $options = 0)
	{
		return _armor_json_encode_value($value);
	}

	function _armor_json_decode_parse($str, &$pos, $assoc)
	{
		$len = strlen($str);
		while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
			$pos++;
		}
		if ($pos >= $len) {
			return null;
		}
		$char = $str[$pos];

		if ($char === '{') {
			$pos++;
			$obj = array();
			while ($pos < $len) {
				while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
					$pos++;
				}
				if ($str[$pos] === '}') {
					$pos++;
					break;
				}
				$key = _armor_json_decode_parse($str, $pos, $assoc);
				while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
					$pos++;
				}
				$pos++; // skip ':'
				$val = _armor_json_decode_parse($str, $pos, $assoc);
				$obj[$key] = $val;
				while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
					$pos++;
				}
				if ($pos < $len && $str[$pos] === ',') {
					$pos++;
				} elseif ($pos < $len && $str[$pos] === '}') {
					$pos++;
					break;
				}
			}
			return $assoc ? $obj : (object) $obj;
		}

		if ($char === '[') {
			$pos++;
			$arr = array();
			while ($pos < $len) {
				while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
					$pos++;
				}
				if ($str[$pos] === ']') {
					$pos++;
					break;
				}
				$arr[] = _armor_json_decode_parse($str, $pos, $assoc);
				while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\t" || $str[$pos] === "\n" || $str[$pos] === "\r")) {
					$pos++;
				}
				if ($pos < $len && $str[$pos] === ',') {
					$pos++;
				} elseif ($pos < $len && $str[$pos] === ']') {
					$pos++;
					break;
				}
			}
			return $arr;
		}

		if ($char === '"') {
			$pos++;
			$result = '';
			while ($pos < $len && $str[$pos] !== '"') {
				if ($str[$pos] === '\\') {
					$pos++;
					$esc = $str[$pos];
					switch ($esc) {
						case 'n': $result .= "\n"; break;
						case 't': $result .= "\t"; break;
						case 'r': $result .= "\r"; break;
						case 'b': $result .= "\b"; break;
						case 'f': $result .= "\f"; break;
						case '/': $result .= '/'; break;
						case '"': $result .= '"'; break;
						case '\\': $result .= '\\'; break;
						case 'u':
							$hex = substr($str, $pos + 1, 4);
							$result .= html_entity_decode('&#x' . $hex . ';', ENT_QUOTES, 'UTF-8');
							$pos += 4;
							break;
						default: $result .= $esc;
					}
					$pos++;
				} else {
					$result .= $str[$pos];
					$pos++;
				}
			}
			$pos++; // skip closing quote
			return $result;
		}

		// literal: true / false / null / number
		$start = $pos;
		while ($pos < $len && strpos(" \t\n\r,}]", $str[$pos]) === false) {
			$pos++;
		}
		$token = substr($str, $start, $pos - $start);
		if ($token === 'true') {
			return true;
		}
		if ($token === 'false') {
			return false;
		}
		if ($token === 'null') {
			return null;
		}
		if (is_numeric($token)) {
			return (strpos($token, '.') !== false || stripos($token, 'e') !== false) ? (float) $token : (int) $token;
		}
		return $token;
	}

	function json_decode($json, $assoc = false, $depth = 512, $options = 0)
	{
		if (!is_string($json) || $json === '') {
			return null;
		}
		$pos = 0;
		return _armor_json_decode_parse($json, $pos, $assoc);
	}

	if (!function_exists('json_last_error')) {
		function json_last_error()
		{
			return 0;
		}
	}
	if (!function_exists('json_last_error_msg')) {
		function json_last_error_msg()
		{
			return 'No error';
		}
	}
}
