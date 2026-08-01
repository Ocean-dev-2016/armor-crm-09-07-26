<?php
$f = __DIR__ . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($f), true);
if (!$j) {
	fwrite(STDERR, "bad json\n");
	exit(1);
}
$j['info']['description'] = str_replace(
	'Updated: 2026-08-01 — Common Login API #2 supports Sales Executive + Channel Partner (same App login screen)',
	'Updated: 2026-08-01 — Common Login #2 + CP App My Customers APIs #241-#246 (service_channel_partner.php)',
	$j['info']['description']
);

$make = function ($name, $s, $fields, $desc) {
	$form = array(
		array('key' => 'key', 'value' => '1226', 'type' => 'text'),
		array('key' => 's', 'value' => (string) $s, 'type' => 'text'),
	);
	foreach ($fields as $k => $v) {
		$form[] = array('key' => $k, 'value' => $v, 'type' => 'text');
	}
	return array(
		'name' => $name,
		'request' => array(
			'method' => 'POST',
			'header' => array(),
			'body' => array('mode' => 'formdata', 'formdata' => $form),
			'url' => array(
				'raw' => '{{base_url}}/service/service_channel_partner.php',
				'host' => array('{{base_url}}'),
				'path' => array('service', 'service_channel_partner.php'),
			),
			'description' => $desc,
		),
		'response' => array(),
	);
};

$folder = array(
	'name' => 'Channel Partner App — My Customers (#241-#246)',
	'description' => "Same as web My Customers.\n1) Login API #2 → result.channel_partner_id\n2) List #241 / Add #242\nEndpoint: service/service_channel_partner.php\nkey=1226",
	'item' => array(
		$make('CP My Customers List (#241)', 241, array('channel_partner_id' => '', 'search_name' => '', 'ul' => '0', 'll' => '50'), 'Required: channel_partner_id from Login #2'),
		$make('CP Add My Customer (#242)', 242, array('channel_partner_id' => '', 'company_name' => '', 'person_name' => '', 'mobile_no' => '', 'email' => '', 'gst' => '', 'country' => 'India', 'state' => '', 'city' => '', 'pincode' => ''), 'Same fields as web Add Customer form'),
		$make('CP Update My Customer (#243)', 243, array('id' => '', 'channel_partner_id' => '', 'company_name' => '', 'person_name' => '', 'mobile_no' => '', 'email' => '', 'gst' => '', 'country' => 'India', 'state' => '', 'city' => '', 'pincode' => ''), 'Update customer'),
		$make('CP My Customer Detail (#244)', 244, array('id' => '', 'channel_partner_id' => ''), 'Load one customer'),
		$make('CP Delete My Customer (#245)', 245, array('id' => '', 'channel_partner_id' => ''), 'Soft delete'),
		$make('CP Customer Form Masters (#246)', 246, array(), 'Countries + field map'),
	),
);

/* Avoid duplicate folder if re-run */
$filtered = array();
foreach ($j['item'] as $it) {
	if (!isset($it['name']) || strpos($it['name'], 'Channel Partner App — My Customers') === false) {
		$filtered[] = $it;
	}
}
array_unshift($filtered, $folder);
$j['item'] = $filtered;

file_put_contents($f, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Postman updated. Top folders: " . count($j['item']) . "\n";
