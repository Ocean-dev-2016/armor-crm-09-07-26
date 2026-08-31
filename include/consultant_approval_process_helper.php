<?php

if (!function_exists('consultant_approval_decode_json_array')) {
	function consultant_approval_decode_json_array($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return null;
		}

		$candidates = array($raw);
		$decodedHtml = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
		if ($decodedHtml !== $raw) {
			$candidates[] = $decodedHtml;
		}
		$stripped = stripslashes($raw);
		if ($stripped !== $raw) {
			$candidates[] = $stripped;
		}
		if ($decodedHtml !== $raw) {
			$candidates[] = stripslashes($decodedHtml);
		}

		foreach ($candidates as $candidate) {
			$candidate = preg_replace('/^\xEF\xBB\xBF/', '', trim((string) $candidate));
			if ($candidate === '') {
				continue;
			}
			$decoded = json_decode($candidate, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				return $decoded;
			}
		}

		return null;
	}
}

if (!function_exists('consultant_approval_regex_parse_product_groups')) {
	function consultant_approval_regex_parse_product_groups($raw)
	{
		$groups = array();
		if (!preg_match_all('/"([^"]+)"\s*:\s*\[(.*?)\]/s', (string) $raw, $matches, PREG_SET_ORDER)) {
			return $groups;
		}

		foreach ($matches as $match) {
			$items = array();
			if (preg_match_all('/"((?:\\\\.|[^"\\\\])*)"/', $match[2], $productMatches)) {
				foreach ($productMatches[1] as $product) {
					$product = trim(stripslashes($product));
					if ($product !== '') {
						$items[] = $product;
					}
				}
			}
			if (!empty($items)) {
				$groups[] = array(
					'category' => stripslashes($match[1]),
					'items' => $items,
				);
			}
		}

		return $groups;
	}
}

if (!function_exists('consultant_approval_parse_list_field')) {
	function consultant_approval_parse_list_field($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return array();
		}

		$decoded = consultant_approval_decode_json_array($raw);
		if (is_array($decoded)) {
			$items = array();
			foreach ($decoded as $item) {
				$item = trim((string) $item);
				if ($item !== '') {
					$items[] = $item;
				}
			}
			if (!empty($items)) {
				return $items;
			}
		}

		if (preg_match('/\(\s*\d+\s*\)/', $raw)) {
			$parts = preg_split('/\(\s*\d+\s*\)\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
			$items = array();
			if (is_array($parts)) {
				foreach ($parts as $part) {
					$part = trim($part, " \t\n\r\0\x0B,;");
					if ($part !== '') {
						$items[] = $part;
					}
				}
			}
			if (!empty($items)) {
				return $items;
			}
		}

		$normalized = trim($raw, " \t\n\r\0\x0B[]()");
		if ($normalized === '') {
			return array();
		}

		$parts = preg_split('/\s*,\s*/', $normalized);
		$items = array();
		if (is_array($parts)) {
			foreach ($parts as $part) {
				$part = trim($part, " \t\n\r\0\x0B\"'");
				if ($part !== '') {
					$items[] = $part;
				}
			}
		}

		return !empty($items) ? $items : array($raw);
	}
}

if (!function_exists('consultant_approval_render_project_list_html')) {
	function consultant_approval_render_project_list_html($items)
	{
		$items = array_values(array_filter(array_map('trim', (array) $items), function ($item) {
			return $item !== '';
		}));

		if (empty($items)) {
			return '';
		}

		$html = '<div class="consultant-project-list">';
		$showPlus = count($items) > 1;

		foreach ($items as $index => $item) {
			$item = preg_replace('/^\(\s*\d+\s*\)\s*/', '', trim((string) $item));
			$html .= '<div class="consultant-project-item">';
			if ($showPlus) {
				$html .= '<span class="consultant-project-plus" title="Project ' . ($index + 1) . '">+</span>';
				$html .= '<span class="consultant-project-text">(' . ($index + 1) . ') ' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
			} else {
				$html .= '<span class="consultant-project-text">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
			}
			$html .= '</div>';
		}

		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('consultant_approval_render_project_cells')) {
	function consultant_approval_render_project_cells($nameRaw, $locationRaw)
	{
		$names = consultant_approval_parse_list_field($nameRaw);
		$locations = consultant_approval_parse_list_field($locationRaw);
		$count = max(count($names), count($locations));

		if ($count === 0) {
			return array(
				'name_html' => '',
				'location_html' => '',
			);
		}

		for ($i = count($names); $i < $count; $i++) {
			$names[] = '';
		}
		for ($i = count($locations); $i < $count; $i++) {
			$locations[] = '';
		}

		return array(
			'name_html' => consultant_approval_render_project_list_html($names),
			'location_html' => consultant_approval_render_project_list_html($locations),
		);
	}
}

if (!function_exists('consultant_approval_parse_product_groups')) {
	function consultant_approval_parse_product_groups($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return array();
		}

		$decoded = consultant_approval_decode_json_array($raw);
		if (is_array($decoded)) {
			$isCategoryMap = false;
			foreach ($decoded as $key => $value) {
				if (is_string($key) && is_array($value)) {
					$isCategoryMap = true;
					break;
				}
			}

			if ($isCategoryMap) {
				$groups = array();
				foreach ($decoded as $category => $products) {
					$items = array();
					if (is_array($products)) {
						foreach ($products as $product) {
							$product = trim((string) $product);
							if ($product !== '') {
								$items[] = $product;
							}
						}
					} else {
						$product = trim((string) $products);
						if ($product !== '') {
							$items[] = $product;
						}
					}
					if (!empty($items)) {
						$groups[] = array(
							'category' => trim((string) $category),
							'items' => $items,
						);
					}
				}
				return $groups;
			}

			$items = array();
			foreach ($decoded as $product) {
				$product = trim((string) $product);
				if ($product !== '') {
					$items[] = $product;
				}
			}
			if (!empty($items)) {
				return array(array('category' => '', 'items' => $items));
			}
		}

		$regexGroups = consultant_approval_regex_parse_product_groups($raw);
		if (!empty($regexGroups)) {
			return $regexGroups;
		}

		$items = consultant_approval_parse_list_field($raw);
		if (!empty($items)) {
			return array(array('category' => '', 'items' => $items));
		}

		return array();
	}
}

if (!function_exists('consultant_approval_render_product_list_html')) {
	function consultant_approval_render_product_list_html($raw)
	{
		$groups = consultant_approval_parse_product_groups($raw);
		if (empty($groups)) {
			return '';
		}

		$html = '<div class="consultant-product-list">';
		foreach ($groups as $group) {
			if ($group['category'] !== '') {
				$html .= '<div class="consultant-product-category">' . htmlspecialchars($group['category'], ENT_QUOTES, 'UTF-8') . '</div>';
			}
			foreach ($group['items'] as $item) {
				$html .= '<div class="consultant-product-item">';
				$html .= '<span class="consultant-product-tick" title="Selected">&#10003;</span>';
				$html .= '<span class="consultant-product-text">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
				$html .= '</div>';
			}
		}
		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('consultant_approval_process_styles')) {
	function consultant_approval_process_styles()
	{
		return '<style type="text/css">
.consultant-project-list {
	margin: 0;
	padding: 0;
}
.consultant-project-item {
	display: flex;
	align-items: flex-start;
	gap: 6px;
	margin: 0 0 6px 0;
	line-height: 1.35;
}
.consultant-project-item:last-child {
	margin-bottom: 0;
}
.consultant-project-plus {
	display: inline-block;
	min-width: 16px;
	height: 16px;
	line-height: 14px;
	text-align: center;
	border: 1px solid #3598dc;
	color: #3598dc;
	border-radius: 2px;
	font-size: 12px;
	font-weight: bold;
	flex: 0 0 16px;
}
.consultant-project-text {
	display: block;
	word-break: break-word;
}
.consultant-report-table td.consultant-project-cell {
	vertical-align: top;
}
.consultant-product-list {
	margin: 0;
	padding: 0;
}
.consultant-product-category {
	font-weight: bold;
	font-size: 11px;
	margin: 0 0 4px 0;
	color: #333;
}
.consultant-product-category + .consultant-product-item,
.consultant-product-item + .consultant-product-category {
	margin-top: 6px;
}
.consultant-product-item {
	display: flex;
	align-items: flex-start;
	gap: 6px;
	margin: 0 0 4px 0;
	line-height: 1.35;
}
.consultant-product-item:last-child {
	margin-bottom: 0;
}
.consultant-product-tick {
	display: inline-block;
	min-width: 14px;
	color: #27ae60;
	font-size: 12px;
	font-weight: bold;
	flex: 0 0 14px;
}
.consultant-product-text {
	display: block;
	word-break: break-word;
}
.consultant-report-table td.consultant-product-cell {
	vertical-align: top;
}
@media print {
	.consultant-project-plus,
	.consultant-product-tick {
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}
}
</style>';
	}
}
