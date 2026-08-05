<?php
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	fwrite(STDERR, "JSON parse fail\n");
	exit(1);
}

function cp_stock_req($name, $s, $fields, $desc)
{
	$fd = array(
		array('key' => 'key', 'value' => '1226', 'type' => 'text'),
		array('key' => 's', 'value' => (string) $s, 'type' => 'text'),
	);
	foreach ($fields as $k => $v) {
		$item = array('key' => $k, 'value' => (string) $v['value'], 'type' => 'text');
		if (!empty($v['description'])) {
			$item['description'] = $v['description'];
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

$folder = array(
	'name' => 'Channel Partner App — My Stock (#257-#258)',
	'description' => "Same as web My Stock (channel_partner_stock_manage.php).\n\n**#257 Main Stock** — Product Code + Name + Available Qty\n**#258 Inward/Outward** — Bill No, Date, In/Out, Balance\n\nEndpoint: service/service_channel_partner.php\nkey=1226",
	'item' => array(
		cp_stock_req('CP My Stock Main (#257)', 257, array(
			'channel_partner_id' => array('value' => '', 'description' => 'From Login #2'),
			'search_name' => array('value' => '', 'description' => 'Optional product / code search'),
		), "Web tab: 1. Main Stock (Product & Code)\n\nReturns: product_code, product_name, product_label (code - name), available_qty\nSame data as Aarav Safety Solutions — Main Stock table."),
		cp_stock_req('CP My Stock Movements (#258)', 258, array(
			'channel_partner_id' => array('value' => '', 'description' => 'From Login #2'),
			'search_name' => array('value' => '', 'description' => 'Optional product / bill search'),
		), "Web tab: 2. Inward / Outward (Bill No & Date)\n\nReturns: date, bill_no, txn_label (INWARD/OUTWARD), product_label, qty_in, qty_out, balance"),
	),
);

$newItems = array();
foreach ($j['item'] as $it) {
	if (isset($it['name']) && strpos($it['name'], 'My Stock (#257') !== false) {
		continue;
	}
	$newItems[] = $it;
}
$j['item'] = $newItems;

/* Insert after Customer Order folder if present, else after first CP folder */
$insertAt = 1;
foreach ($j['item'] as $idx => $it) {
	if (isset($it['name']) && strpos($it['name'], 'Customer Order + Cart') !== false) {
		$insertAt = $idx + 1;
		break;
	}
}
array_splice($j['item'], $insertAt, 0, array($folder));

if (strpos($j['info']['description'], '#257') === false) {
	$j['info']['description'] .= "\n**CP My Stock:** #257-#258 in service_channel_partner.php";
}

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "OK stock folder inserted\n";
