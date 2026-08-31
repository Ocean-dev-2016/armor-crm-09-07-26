<?php

if (!function_exists('consultant_approval_parse_list_field')) {
	function consultant_approval_parse_list_field($raw)
	{
		$raw = trim((string) $raw);
		if ($raw === '') {
			return array();
		}

		$decoded = json_decode($raw, true);
		if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
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

		$normalized = trim($raw, " \t\n\r\0\x0B[]");
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
			$html .= '<div class="consultant-project-item">';
			if ($showPlus) {
				$html .= '<span class="consultant-project-plus" title="Project ' . ($index + 1) . '">+</span>';
			}
			$html .= '<span class="consultant-project-text">' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</span>';
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
</style>';
	}
}
