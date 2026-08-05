<?php
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	exit(1);
}

foreach ($j['item'] as &$folder) {
	if (!isset($folder['name']) || strpos($folder['name'], 'Customer Order + Cart') === false) {
		continue;
	}
	$folder['description'] = "CP Customer Order — App draft cart (web form has NO cart; App-only APIs).\n\n"
		. "**NORMAL Add-to-Cart button — send ONLY these:**\n"
		. "key=1226, s=250, channel_partner_id, channel_partner_customer_id, pwp_id, qty\n"
		. "Optional: rate, discount, gst_apply_flag\n"
		. "Do NOT send `products` for single add.\n\n"
		. "**products:** batch only, JSON array e.g. [{\"pwp_id\":2095,\"qty\":1}] — leave empty normally.\n\n"
		. "Flow: #241 party → #248 pick pwp_id → #250 add → #249 cart → #254 place";

	foreach ($folder['item'] as &$req) {
		if (strpos($req['name'], '#250') === false) {
			continue;
		}
		$req['request']['description'] = "Add ONE product (Add button).\nRequired: channel_partner_id, channel_partner_customer_id, pwp_id (#248), qty\nOptional: rate, discount, gst_apply_flag\nLeave products EMPTY.";
		foreach ($req['request']['body']['formdata'] as &$f) {
			if ($f['key'] === 'products') {
				$f['value'] = '';
				$f['description'] = 'LEAVE EMPTY for normal add. Batch only: [{"pwp_id":2095,"qty":1}]';
				$f['disabled'] = true;
			}
			if ($f['key'] === 'pwp_id') {
				$f['description'] = 'REQUIRED. From #248 result[].pwp_id';
			}
		}
		unset($f);
	}
	unset($req);
}
unset($folder);

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "OK\n";
