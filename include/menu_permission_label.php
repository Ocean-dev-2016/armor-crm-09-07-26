<?php
function armor_menu_permission_labels()
{
	static $map = null;
	if ($map !== null) {
		return $map;
	}

	include dirname(__FILE__) . '/top_var.php';

	$groups = array(
		'Dashboard' => isset($left_tracking_array) ? $left_tracking_array : array(),
		'Sales & Marketing' => isset($left_sales_array) ? $left_sales_array : array(),
		'HR' => isset($left_hr_array) ? $left_hr_array : array(),
		'Master' => isset($left_main_array) ? $left_main_array : array(),
		'Sub Master' => isset($left_main_array_sub) ? $left_main_array_sub : array(),
		'Utility' => isset($left_utility_array) ? $left_utility_array : array(),
		'Customer Reports' => isset($left_Customer_reports_array) ? $left_Customer_reports_array : array(),
		'Sales Team Reports' => isset($left_Sales_Team_reports_array) ? $left_Sales_Team_reports_array : array(),
	);

	$names = array();
	foreach ($groups as $section => $items) {
		foreach ($items as $arr) {
			if (!is_array($arr) || !isset($arr[3])) {
				continue;
			}
			$id = (int) $arr[3];
			$label = $section . ' > ' . trim($arr[0]);
			$names[$id][$label] = 1;
		}
	}

	$names[565]['Order History > All Orders'] = 1;
	$names[565]['Order History > Channel Partner Portal Orders'] = 1;
	$names[565]['Order History > Channel Partner Orders'] = 1;
	$names[565]['Order History > Pending Payment'] = 1;
	$names[565]['Order History > Receive Payment'] = 1;
	$names[671]['Remark Analysis Report > Remark Wise Report'] = 1;
	$names[674]['Approval'] = 1;
	$names[406]['Utility > System User'] = 1;
	$names[679]['Utility > Push Notification'] = 1;
	$names[680]['Utility > Document List'] = 1;

	$map = array();
	foreach ($names as $id => $labels) {
		$map[$id] = implode(' | ', array_keys($labels));
	}
	return $map;
}

function armor_menu_file_page_id($url)
{
	static $cache = array();
	$url = trim((string) $url);
	if ($url === '' || $url === '#') {
		return 0;
	}
	$path = parse_url($url, PHP_URL_PATH);
	if (!$path) {
		$path = $url;
	}
	$file = basename($path);
	$flag = '';
	$query = parse_url($url, PHP_URL_QUERY);
	if ($query && preg_match('/(?:^|&)flag=([a-z0-9_]+)/i', $query, $flag_match)) {
		$flag = strtolower($flag_match[1]);
	}
	$cache_key = $file . '|' . $flag;
	if ($file === '' || substr($file, -4) !== '.php') {
		return 0;
	}
	if (isset($cache[$cache_key])) {
		return $cache[$cache_key];
	}
	$full = dirname(__FILE__) . '/../bbsales_tracking/' . $file;
	if (!is_file($full)) {
		$cache[$cache_key] = 0;
		return 0;
	}
	$src = file_get_contents($full);
	if ($flag !== '') {
		$flag_pattern = preg_quote($flag, '/');
		if (preg_match('/==\s*"' . $flag_pattern . '"\s*\)\s*\{[^}]*\$page_id\s*=\s*(\d+)/s', $src, $match)) {
			$cache[$cache_key] = (int) $match[1];
			return $cache[$cache_key];
		}
	}
	if (preg_match('/\$page_id\s*=\s*(\d+)/', $src, $match)) {
		$cache[$cache_key] = (int) $match[1];
		return $cache[$cache_key];
	}
	$cache[$cache_key] = 0;
	return 0;
}

function armor_menu_permission_name($page_id, $fallback)
{
	$labels = armor_menu_permission_labels();
	$page_id = (int) $page_id;
	if (isset($labels[$page_id]) && $labels[$page_id] !== '') {
		return $labels[$page_id];
	}
	return $fallback;
}

function armor_menu_permission_sections()
{
	static $cached = null;
	if ($cached !== null) {
		return $cached;
	}

	include dirname(__FILE__) . '/top_var.php';

	$order_pages = array(
		'1' => array('All Orders', 'all_orders', 'dealer_orders_manage.php?type=100', 565),
		'2' => array('Channel Partner Portal Orders', 'cp_portal_orders', 'dealer_orders_manage.php?type=channel_partner_portal', 565),
		'3' => array('Channel Partner Orders', 'cp_orders', 'dealer_orders_manage.php?type=channel_partner', 565),
		'4' => array('Pending Payment', 'pending_payment', 'dealer_orders_manage.php?type=pending_payment', 565),
		'5' => array('Receive Payment', 'receive_payment', 'armor_payment_receive.php', 565),
	);

	$groups = array(
		'Dashboard' => isset($left_tracking_array) ? $left_tracking_array : array(),
		'Sales & Marketing' => isset($left_sales_array) ? $left_sales_array : array(),
		'Order History' => array(array('Order History', 'order_history', $order_pages, 565)),
		'HR' => isset($left_hr_array) ? $left_hr_array : array(),
		'Master' => isset($left_main_array) ? $left_main_array : array(),
		'Sub Master' => isset($left_main_array_sub) ? $left_main_array_sub : array(),
		'Utility' => isset($left_utility_array) ? $left_utility_array : array(),
		'Customer Reports' => isset($left_Customer_reports_array) ? $left_Customer_reports_array : array(),
		'Sales Team Reports' => isset($left_Sales_Team_reports_array) ? $left_Sales_Team_reports_array : array(),
		'Remark Analysis Report' => array(array('Remark Wise Report', 'remark_wise_report', array(), 671)),
		'Approval' => array(array('Approval', 'approval', array(), 674)),
	);

	$sections = array();
	$owner = array();

	$add_alias = function ($page_id, $text) use (&$sections, &$owner) {
		if ($text === '' || !isset($owner[$page_id])) {
			return;
		}
		$section = $owner[$page_id][0];
		$index = $owner[$page_id][1];
		if (!in_array($text, $sections[$section][$index]['aliases'])) {
			$sections[$section][$index]['aliases'][] = $text;
		}
	};

	foreach ($groups as $section => $items) {
		if (!isset($sections[$section])) {
			$sections[$section] = array();
		}
		foreach ($items as $arr) {
			if (!is_array($arr) || !isset($arr[3])) {
				continue;
			}
			$page_id = (int) $arr[3];
			if ($page_id <= 0) {
				continue;
			}
			$title = trim($arr[0]);
			$subs = array();
			if (isset($arr[2]) && is_array($arr[2])) {
				foreach ($arr[2] as $sub) {
					if (!is_array($sub) || !isset($sub[0])) {
						continue;
					}
					$sub_id = isset($sub[3]) ? (int) $sub[3] : $page_id;
					$sub_title = trim($sub[0]);
					if ($sub_id === $page_id && $sub_title !== '' && strcasecmp($sub_title, $title) !== 0 && !in_array($sub_title, $subs)) {
						$subs[] = $sub_title;
					}
				}
			}
			if (isset($owner[$page_id])) {
				$add_alias($page_id, $section . ' > ' . $title);
				continue;
			}
			$sections[$section][] = array(
				'page_id' => $page_id,
				'title' => $title,
				'subs' => $subs,
				'aliases' => array(),
				'depth' => 0,
			);
			$owner[$page_id] = array($section, count($sections[$section]) - 1);
		}
	}

	foreach ($groups as $section => $items) {
		foreach ($items as $arr) {
			if (!is_array($arr) || !isset($arr[2]) || !is_array($arr[2])) {
				continue;
			}
			$parent = trim($arr[0]);
			$parent_id = isset($arr[3]) ? (int) $arr[3] : 0;
			$bucket = array();
			foreach ($arr[2] as $sub) {
				if (!is_array($sub) || !isset($sub[0])) {
					continue;
				}
				$sub_id = isset($sub[3]) ? (int) $sub[3] : 0;
				$file_id = armor_menu_file_page_id(isset($sub[2]) ? $sub[2] : '');
				if ($file_id > 0) {
					$sub_id = $file_id;
				}
				if ($sub_id <= 0 || $sub_id === $parent_id) {
					continue;
				}
				$bucket[$sub_id][] = trim($sub[0]);
			}
			foreach ($bucket as $sub_id => $titles) {
				$titles = array_values(array_unique($titles));
				$label = $parent . ' > ' . implode(' | ', $titles);
				if (isset($owner[$sub_id])) {
					$add_alias($sub_id, $section . ' > ' . $label);
					continue;
				}
				$sections[$section][] = array(
					'page_id' => $sub_id,
					'title' => $label,
					'subs' => array(),
					'aliases' => array(),
					'depth' => 1,
				);
				$owner[$sub_id] = array($section, count($sections[$section]) - 1);
			}
		}
	}

	$out = array();
	foreach ($groups as $section => $items) {
		if (!empty($sections[$section])) {
			$out[] = array('name' => $section, 'rows' => $sections[$section]);
		}
	}

	$cached = array('sections' => $out, 'page_ids' => array_keys($owner));
	return $cached;
}

function armor_echo_permission_board($db, $admin_type_id, $mode, $exclude_ids)
{
	$board = armor_menu_permission_sections();
	$admin_type_id = (int) $admin_type_id;
	$rights = array();
	$right_rows = $db->rp_getData('page_admin_right', '*', "admin_id='" . $admin_type_id . "' AND isDelete=0", '', 0);
	if ($right_rows) {
		while ($row = mysqli_fetch_assoc($right_rows)) {
			$rights[(int) $row['page_id']] = $row;
		}
	}

	$exclude = array();
	if (is_array($exclude_ids)) {
		foreach ($exclude_ids as $exclude_id) {
			$exclude[(int) $exclude_id] = 1;
		}
	}

	$fields = array('view', 'insert', 'update', 'delete', 'export_excel', 'print', 'pdf_download', 'email', 'all_data', 'approve', 'personal', 'chain_vise');
	$flag_map = array(
		'view' => 'view_flag',
		'insert' => 'insert_flag',
		'update' => 'update_flag',
		'delete' => 'delete_flag',
		'export_excel' => 'export_excel_flag',
		'print' => 'print_flag',
		'pdf_download' => 'pdf_download_flag',
		'email' => 'email_flag',
		'all_data' => 'all_data_flag',
		'approve' => 'approve_flag',
		'personal' => 'personal_flag',
		'chain_vise' => 'chain_vise_flag',
	);

	$render_row = function ($key, $page_id, $title, $subs, $aliases, $depth, $flags) use ($fields, $flag_map, $mode) {
		$pad = ((int) $depth === 1) ? '28px' : '8px';
		$all_checked = ($mode === 'manage');
		foreach ($fields as $field) {
			$flag = $flag_map[$field];
			if (!isset($flags[$flag]) || (int) $flags[$flag] !== 1) {
				$all_checked = false;
				break;
			}
		}
		echo '<tr class="perm-row" id="row' . (int) $key . '" data-key="' . (int) $key . '">';
		echo '<td class="perm-name" style="padding-left:' . $pad . ';">';
		echo '<strong>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</strong>';
		if (!empty($subs)) {
			echo '<div class="perm-sub">' . htmlspecialchars(implode(' | ', $subs), ENT_QUOTES, 'UTF-8') . '</div>';
		}
		if (!empty($aliases)) {
			echo '<div class="perm-alias">Same permission: ' . htmlspecialchars(implode(' | ', $aliases), ENT_QUOTES, 'UTF-8') . '</div>';
		}
		echo '</td>';
		echo '<td align="center"><input type="checkbox" class="perm-row-all" id="rowall' . (int) $key . '" data-key="' . (int) $key . '" title="All rights"' . ($all_checked ? ' checked="checked"' : '') . '></td>';
		foreach ($fields as $field) {
			$checked = '';
			$flag = $flag_map[$field];
			if (isset($flags[$flag]) && (int) $flags[$flag] === 1) {
				$checked = ' checked="checked"';
			}
			echo '<td align="center"><input type="checkbox" class="perm-flag" name="' . $field . (int) $key . '[]" id="' . $field . (int) $key . '" value="1"' . $checked . '></td>';
		}
		echo '<td>';
		if ($mode === 'manage') {
			echo '<a class="btn btn-info btn-sm clc" href="javascript:void(0);" title="Update" onClick="addPT(\'' . (int) $key . '\');"><i class="fa fa-pencil"></i> Update</a> ';
			echo '<a class="btn btn-danger btn-sm" onClick="del_conf(\'' . (int) $key . '\');" title="Delete"><i class="fa fa-times"></i></a>';
		} else {
			echo '<a class="btn btn-info btn-sm" href="javascript:void(0);" title="Add" onClick="addAR(\'' . (int) $page_id . '\');"><i class="fa fa-plus"></i> Add</a>';
		}
		echo '</td></tr>';
	};

	$placed = array();
	foreach ($board['page_ids'] as $placed_id) {
		$placed[(int) $placed_id] = 1;
	}

	$printed = 0;
	foreach ($board['sections'] as $section) {
		$chunk = '';
		ob_start();
		$count = 0;
		foreach ($section['rows'] as $row) {
			$page_id = (int) $row['page_id'];
			if (isset($exclude[$page_id])) {
				continue;
			}
			$has = isset($rights[$page_id]);
			if ($mode === 'manage' && !$has) {
				continue;
			}
			if ($mode === 'add' && $has) {
				continue;
			}
			$key = ($mode === 'manage') ? (int) $rights[$page_id]['id'] : $page_id;
			$flags = $has ? $rights[$page_id] : array();
			$render_row($key, $page_id, $row['title'], $row['subs'], $row['aliases'], $row['depth'], $flags);
			$count++;
		}
		$chunk = ob_get_clean();
		if ($count > 0) {
			echo '<tr class="perm-section">';
			echo '<td>' . htmlspecialchars($section['name'], ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td align="center"><input type="checkbox" class="perm-section-all" title="All rights for this menu"></td>';
			echo '<td colspan="13"></td>';
			echo '</tr>';
			echo $chunk;
			$printed += $count;
		}
	}

	$other = $db->rp_getData('page_table', '*', 'isDelete=0', 'page_title ASC', 0);
	$other_html = '';
	$other_count = 0;
	if ($other) {
		ob_start();
		while ($page = mysqli_fetch_assoc($other)) {
			$page_id = (int) $page['id'];
			if (isset($placed[$page_id]) || isset($exclude[$page_id])) {
				continue;
			}
			$has = isset($rights[$page_id]);
			if ($mode === 'manage' && !$has) {
				continue;
			}
			if ($mode === 'add' && $has) {
				continue;
			}
			$key = ($mode === 'manage') ? (int) $rights[$page_id]['id'] : $page_id;
			$flags = $has ? $rights[$page_id] : array();
			$title = trim(stripslashes($page['page_title']));
			if ($title === '') {
				$title = 'Page ' . $page_id;
			}
			$render_row($key, $page_id, $title, array(), array(), 0, $flags);
			$other_count++;
		}
		$other_html = ob_get_clean();
	}
	if ($other_count > 0) {
		echo '<tr class="perm-section">';
		echo '<td>Other Pages</td>';
		echo '<td align="center"><input type="checkbox" class="perm-section-all" title="All rights for this menu"></td>';
		echo '<td colspan="13"></td>';
		echo '</tr>';
		echo $other_html;
		$printed += $other_count;
	}

	if ($printed === 0) {
		echo '<tr><td colspan="15" style="text-align:center;">No pages in this list.</td></tr>';
	}
}
