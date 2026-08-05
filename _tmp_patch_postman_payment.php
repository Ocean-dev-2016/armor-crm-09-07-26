<?php
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	fwrite(STDERR, "JSON parse fail\n");
	exit(1);
}

function cp_pay_req($name, $s, $fields, $desc)
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
	'name' => 'Channel Partner App — Receive Payment (#259-#261)',
	'description' => "Same as web Receive Payment (channel_partner_payment.php).\n\n**App flow:**\n1) #259 Parties dropdown + payment_types\n2) Select party → #260 Orders (Pending / Received)\n3) #261 Save payment (order_id, paid_amount, payment_type, remark)\n\nPayment types: 1=Cash, 2=Cheque, 3=Online/NEFT/UPI, 4=Other\n\nEndpoint: service/service_channel_partner.php\nkey=1226",
	'item' => array(
		cp_pay_req('CP Payment Parties (#259)', 259, array(
			'channel_partner_id' => array('value' => '', 'description' => 'From Login #2'),
			'search_name' => array('value' => '', 'description' => 'Optional party search'),
		), "Select Party / Customer list.\nReturns: party_id, display_name, payment_types[]"),
		cp_pay_req('CP Payment Orders (#260)', 260, array(
			'channel_partner_id' => array('value' => ''),
			'party_id' => array('value' => '', 'description' => 'From #259 party_id (channel_partner_customer.id)'),
		), "Orders for selected party.\nReturns: order_id, order_no, order_amount, payment_status_label, can_receive (1=show Receive button), suggested_amount"),
		cp_pay_req('CP Save Receive Payment (#261)', 261, array(
			'channel_partner_id' => array('value' => ''),
			'order_id' => array('value' => '', 'description' => 'From #260'),
			'paid_amount' => array('value' => '', 'description' => 'Required > 0'),
			'payment_type' => array('value' => '1', 'description' => '1=Cash 2=Cheque 3=Online 4=Other'),
			'remark' => array('value' => '', 'description' => 'Optional'),
		), "Save Payment Received against order (same as web Save Payment)."),
	),
);

$newItems = array();
foreach ($j['item'] as $it) {
	if (isset($it['name']) && strpos($it['name'], 'Receive Payment (#259') !== false) {
		continue;
	}
	$newItems[] = $it;
}
$j['item'] = $newItems;

$insertAt = 1;
foreach ($j['item'] as $idx => $it) {
	if (isset($it['name']) && strpos($it['name'], 'My Stock (#257') !== false) {
		$insertAt = $idx + 1;
		break;
	}
	if (isset($it['name']) && strpos($it['name'], 'Customer Order + Cart') !== false) {
		$insertAt = $idx + 1;
	}
}
array_splice($j['item'], $insertAt, 0, array($folder));

if (strpos($j['info']['description'], '#259') === false) {
	$j['info']['description'] .= "\n**CP Receive Payment:** #259-#261 in service_channel_partner.php";
}

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "OK payment folder inserted\n";
