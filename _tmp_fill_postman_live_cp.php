<?php
/**
 * Fill Postman CP cart collection with LIVE verified values:
 * CP Aarav Safety Solutions (id=12229)
 */
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	fwrite(STDERR, "JSON fail\n");
	exit(1);
}

/* Collection variables for App/Postman */
$vars = array(
	array('key' => 'base_url', 'value' => 'https://armor-crm.oceanhub.co.in', 'type' => 'string'),
	array('key' => 'api_key', 'value' => '1226', 'type' => 'string'),
	array('key' => 'channel_partner_id', 'value' => '12229', 'type' => 'string'),
	array('key' => 'cp_company', 'value' => 'Aarav Safety Solutions', 'type' => 'string'),
	array('key' => 'channel_partner_customer_id', 'value' => '8', 'type' => 'string'),
	array('key' => 'party_name', 'value' => 'Orbit Electricals', 'type' => 'string'),
	array('key' => 'pwp_id', 'value' => '2095', 'type' => 'string'),
	array('key' => 'product_catno', 'value' => '2301', 'type' => 'string'),
	array('key' => 'product_id', 'value' => '335', 'type' => 'string'),
	array('key' => 'product_name', 'value' => 'S.S. TWIST TYPE DOUBLE HYDRANT VALVE (STAR)', 'type' => 'string'),
	array('key' => 'qty', 'value' => '1', 'type' => 'string'),
	array('key' => 'rate', 'value' => '9300', 'type' => 'string'),
	array('key' => 'cart_item_id', 'value' => '', 'type' => 'string'),
	array('key' => 'order_id', 'value' => '', 'type' => 'string'),
);

if (!isset($j['variable']) || !is_array($j['variable'])) {
	$j['variable'] = array();
}
$byKey = array();
foreach ($j['variable'] as $i => $v) {
	if (isset($v['key'])) {
		$byKey[$v['key']] = $i;
	}
}
foreach ($vars as $nv) {
	if (isset($byKey[$nv['key']])) {
		$j['variable'][$byKey[$nv['key']]] = $nv;
	} else {
		$j['variable'][] = $nv;
	}
}

function set_fd_value(&$formdata, $key, $value, $desc = null, $disabled = null)
{
	foreach ($formdata as &$f) {
		if (isset($f['key']) && $f['key'] === $key) {
			$f['value'] = (string) $value;
			if ($desc !== null) {
				$f['description'] = $desc;
			}
			if ($disabled !== null) {
				if ($disabled) {
					$f['disabled'] = true;
				} else {
					unset($f['disabled']);
				}
			}
			return;
		}
	}
	unset($f);
	$item = array('key' => $key, 'value' => (string) $value, 'type' => 'text');
	if ($desc !== null) {
		$item['description'] = $desc;
	}
	if ($disabled) {
		$item['disabled'] = true;
	}
	$formdata[] = $item;
}

$stockNote = "LIVE verified (Aarav Safety Solutions / CP 12229):\n"
	. "Stock (#257) → Order Products (#248) pwp_id map:\n"
	. "| CatNo | product_id | pwp_id | stock | rate |\n"
	. "| 2301 | 335 | 2095 | 27 | 9300 |\n"
	. "| 2893 | 458 | 2119 | 30 | 975 |\n"
	. "| 2290 | 13 | 2106 | 33 | 5700 |\n"
	. "| 2293 | 307 | 2096 | 22 | 12009 |\n"
	. "Party (#241): id=8 Orbit Electricals (also 6 Metro Mall, 7 Shiv Buildtech)\n"
	. "NOTE: Add Cart (#250) — do NOT send `products`. Only pwp_id+qty.";

foreach ($j['item'] as &$folder) {
	if (!isset($folder['name'])) {
		continue;
	}

	/* Fill all CP folders with CP id */
	if (strpos($folder['name'], 'Channel Partner App') === false) {
		continue;
	}

	if (strpos($folder['name'], 'Customer Order + Cart') !== false) {
		$folder['description'] = $stockNote . "\n\n"
			. "**Add Cart (#250) — ONLY these fields:**\n"
			. "key=1226, s=250, channel_partner_id={{channel_partner_id}}, channel_partner_customer_id={{channel_partner_customer_id}}, pwp_id={{pwp_id}}, qty={{qty}}\n"
			. "Optional: rate, discount, gst_apply_flag. Leave products EMPTY.\n\n"
			. "Flow: #257 stock → #248 products (pwp_id) → #250 add → #249 get cart → #254 place";
	}

	if (!isset($folder['item']) || !is_array($folder['item'])) {
		continue;
	}

	foreach ($folder['item'] as &$req) {
		if (!isset($req['request']['body']['formdata'])) {
			continue;
		}
		$fd = &$req['request']['body']['formdata'];
		$name = isset($req['name']) ? $req['name'] : '';

		set_fd_value($fd, 'key', '1226');
		set_fd_value($fd, 'channel_partner_id', '{{channel_partner_id}}', 'LIVE: 12229 Aarav Safety Solutions');

		if (strpos($name, '#241') !== false || strpos($name, '#247') !== false || strpos($name, '#257') !== false || strpos($name, '#258') !== false || strpos($name, '#259') !== false || strpos($name, '#262') !== false) {
			/* already set cp id */
		}
		if (strpos($name, '#248') !== false) {
			set_fd_value($fd, 'search_name', 'TWIST', 'Try: TWIST / 2301 name / COUPLING. LIVE stock catno 2301 → pwp_id 2095');
			set_fd_value($fd, 'only_in_stock', '1');
		}
		if (strpos($name, '#249') !== false) {
			set_fd_value($fd, 'channel_partner_customer_id', '{{channel_partner_customer_id}}', 'LIVE party id=8 Orbit Electricals');
			set_fd_value($fd, 'party_id', '8', 'Alias', true);
		}
		if (strpos($name, '#250') !== false) {
			set_fd_value($fd, 'channel_partner_customer_id', '{{channel_partner_customer_id}}', 'LIVE: 8 Orbit Electricals (from #241)');
			set_fd_value($fd, 'gst_apply_flag', '1');
			set_fd_value($fd, 'pwp_id', '{{pwp_id}}', 'LIVE stock: 2095 = CatNo 2301 (from #248)');
			set_fd_value($fd, 'qty', '{{qty}}');
			set_fd_value($fd, 'rate', '{{rate}}', 'Optional. LIVE default 9300 for pwp 2095');
			set_fd_value($fd, 'discount', '0');
			set_fd_value($fd, 'products', '', 'LEAVE EMPTY / disabled. Do NOT put catno here', true);
			set_fd_value($fd, 'catno', '', 'Alt only if no pwp_id', true);
			set_fd_value($fd, 'product_id', '', 'Alt only if no pwp_id', true);
			set_fd_value($fd, 'weight_id', '', '', true);
			set_fd_value($fd, 'party_id', '', '', true);
			$req['request']['description'] = "VERIFIED LIVE: CP 12229 + party 8 + pwp_id 2095 + qty 1 → ack=1 cart.\n"
				. "Required: channel_partner_id, channel_partner_customer_id, pwp_id, qty\n"
				. "Do NOT send products field.";
		}
		if (strpos($name, '#251') !== false) {
			set_fd_value($fd, 'cart_item_id', '{{cart_item_id}}', 'From #249/#250 items[].cart_item_id');
			set_fd_value($fd, 'qty', '1');
		}
		if (strpos($name, '#252') !== false) {
			set_fd_value($fd, 'cart_item_id', '{{cart_item_id}}');
		}
		if (strpos($name, '#254') !== false) {
			set_fd_value($fd, 'channel_partner_customer_id', '{{channel_partner_customer_id}}');
			set_fd_value($fd, 'gst_apply_flag', '1');
		}
		if (strpos($name, '#256') !== false) {
			set_fd_value($fd, 'order_id', '{{order_id}}');
		}
		if (strpos($name, '#260') !== false) {
			set_fd_value($fd, 'party_id', '{{channel_partner_customer_id}}', 'LIVE: 8');
		}
		if (strpos($name, 'One Party') !== false || (strpos($name, '#262') !== false && strpos($name, 'One') !== false)) {
			set_fd_value($fd, 'party_id', '{{channel_partner_customer_id}}', 'LIVE: 8 Orbit Electricals');
		}
		if (strpos($name, 'All Parties') !== false) {
			set_fd_value($fd, 'party_id', '0');
		}
	}
	unset($req);
}
unset($folder);

/* Add a ready-to-run example folder at top of CP cart section */
$exampleFolder = array(
	'name' => 'CP READY — Aarav CP12229 Cart Demo (verified)',
	'description' => $stockNote . "\n\nRun in order: 1 Clear → 2 Products → 3 Add → 4 Get Cart",
	'item' => array(),
);

function cp_req($name, $s, $fields, $desc)
{
	$fd = array(
		array('key' => 'key', 'value' => '1226', 'type' => 'text'),
		array('key' => 's', 'value' => (string) $s, 'type' => 'text'),
	);
	foreach ($fields as $k => $meta) {
		$item = array('key' => $k, 'value' => (string) $meta['value'], 'type' => 'text');
		if (!empty($meta['description'])) {
			$item['description'] = $meta['description'];
		}
		if (!empty($meta['disabled'])) {
			$item['disabled'] = true;
		}
		$fd[] = $item;
	}
	return array(
		'name' => $name,
		'request' => array(
			'method' => 'POST',
			'header' => array(),
			'body' => array('mode' => 'formdata', 'formdata' => $fd),
			'url' => array(
				'raw' => '{{base_url}}/service/service_channel_partner.php',
				'host' => array('{{base_url}}'),
				'path' => array('service', 'service_channel_partner.php'),
			),
			'description' => $desc,
		),
		'response' => array(),
	);
}

$exampleFolder['item'] = array(
	cp_req('1) My Stock (#257)', 257, array(
		'channel_partner_id' => array('value' => '12229'),
	), 'See available_qty for CatNo 2301/2893/2290/2293'),
	cp_req('2) My Customers (#241)', 241, array(
		'channel_partner_id' => array('value' => '12229'),
		'll' => array('value' => '20'),
	), 'Use party id=8 Orbit Electricals'),
	cp_req('3) Products search TWIST (#248)', 248, array(
		'channel_partner_id' => array('value' => '12229'),
		'search_name' => array('value' => 'TWIST', 'description' => 'Returns pwp_id 2095 catno 2301 stock 27'),
		'only_in_stock' => array('value' => '1'),
	), 'Copy result[].pwp_id'),
	cp_req('4) Clear Cart (#253)', 253, array(
		'channel_partner_id' => array('value' => '12229'),
	), 'Reset draft cart'),
	cp_req('5) Add Cart (#250) — CORRECT', 250, array(
		'channel_partner_id' => array('value' => '12229'),
		'channel_partner_customer_id' => array('value' => '8', 'description' => 'Orbit Electricals'),
		'gst_apply_flag' => array('value' => '1'),
		'pwp_id' => array('value' => '2095', 'description' => 'CatNo 2301 from #248'),
		'qty' => array('value' => '1'),
		'rate' => array('value' => '9300'),
		'discount' => array('value' => '0'),
		'products' => array('value' => '', 'description' => 'EMPTY', 'disabled' => 1),
	), "VERIFIED LIVE success.\nack=1, cart with Orbit Electricals + product #2301"),
	cp_req('6) Get Cart (#249)', 249, array(
		'channel_partner_id' => array('value' => '12229'),
		'channel_partner_customer_id' => array('value' => '8'),
	), 'Returns channel_partner_customer_id, items[], grand_total'),
);

/* Remove old demo folder if any */
$newItems = array();
foreach ($j['item'] as $it) {
	if (isset($it['name']) && strpos($it['name'], 'CP READY') !== false) {
		continue;
	}
	$newItems[] = $it;
}
$j['item'] = $newItems;

/* Insert after first CP folder or at index 0 */
array_unshift($j['item'], $exampleFolder);

if (strpos($j['info']['description'], 'CP READY') === false) {
	$j['info']['description'] .= "\n**CP READY demo:** Aarav CP12229 — folder 'CP READY — Aarav CP12229 Cart Demo (verified)' with live stock pwp_id 2095.";
}

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "OK collection updated with live CP12229 values\n";
