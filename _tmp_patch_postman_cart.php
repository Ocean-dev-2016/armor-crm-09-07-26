<?php
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	fwrite(STDERR, "JSON parse fail\n");
	exit(1);
}

function find_folder(&$items, $needle) {
	foreach ($items as $i => &$it) {
		if (isset($it['name']) && strpos($it['name'], $needle) !== false) {
			return $i;
		}
	}
	return -1;
}

$fi = find_folder($j['item'], 'Customer Order + Cart');
if ($fi < 0) {
	fwrite(STDERR, "folder not found\n");
	exit(1);
}

$j['item'][$fi]['description'] = "CP Customer Order (same as web channel_partner_order_simple.php).\n\n"
	. "**pwp_id explained:** product_weight_price.id — web form field `line_product[]`. One product can have multiple sizes; each size/Cat No row has its own pwp_id.\n\n"
	. "**App flow:**\n"
	. "1) #241 pick customer (party)\n"
	. "2) #249 with channel_partner_customer_id → bind party on cart (returns party_id)\n"
	. "3) #248 search_name = name / Cat No / product id → pick pwp_id\n"
	. "4) #250 add: pwp_id OR catno OR product_id(+weight_id) + qty\n"
	. "5) #249 get cart (always returns channel_partner_customer_id / party_id)\n"
	. "6) #254 place order\n\n"
	. "Endpoint: service/service_channel_partner.php key=1226";

foreach ($j['item'][$fi]['item'] as &$req) {
	$name = isset($req['name']) ? $req['name'] : '';
	if (strpos($name, '#248') !== false) {
		$req['request']['description'] = "Product list for order.\nsearch_name matches: Product Name, Cat No (catno), Product id.\nUse result.pwp_id when adding to cart (preferred). Also returns product_id, catno, display_name.";
		$fd = &$req['request']['body']['formdata'];
		$hasSearchHint = false;
		foreach ($fd as &$f) {
			if ($f['key'] === 'search_name') {
				$f['description'] = 'Name OR Cat No OR Product id';
				$hasSearchHint = true;
			}
		}
		unset($f);
	}
	if (strpos($name, '#249') !== false) {
		$req['request']['description'] = "Get draft cart.\nALWAYS returns channel_partner_customer_id + party_id + customer_name.\nOptional: send channel_partner_customer_id / party_id to SET/bind party on cart (even if empty).";
		$fd = &$req['request']['body']['formdata'];
		$keys = array();
		foreach ($fd as $f) {
			$keys[$f['key']] = 1;
		}
		if (!isset($keys['channel_partner_customer_id'])) {
			$fd[] = array(
				'key' => 'channel_partner_customer_id',
				'value' => '',
				'type' => 'text',
				'description' => 'Optional. Party from #241. Sets party on cart.',
			);
		}
		if (!isset($keys['party_id'])) {
			$fd[] = array(
				'key' => 'party_id',
				'value' => '',
				'type' => 'text',
				'description' => 'Alias of channel_partner_customer_id',
			);
		}
	}
	if (strpos($name, '#250') !== false) {
		$req['request']['description'] = "Add to cart.\nProduct key (any ONE):\n- pwp_id (best, from #248)\n- catno / cat_no\n- product_id + weight_id (if multiple sizes)\nParty: channel_partner_customer_id (or reuse from cart if already set).";
		$fd = &$req['request']['body']['formdata'];
		$keys = array();
		foreach ($fd as &$f) {
			$keys[$f['key']] = 1;
			if ($f['key'] === 'pwp_id') {
				$f['description'] = 'Preferred. product_weight_price.id from #248 (web line_product)';
			}
			if ($f['key'] === 'channel_partner_customer_id') {
				$f['description'] = 'Party id. Optional if already set via #249';
			}
		}
		unset($f);
		$extra = array(
			array('key' => 'catno', 'value' => '', 'type' => 'text', 'description' => 'Alt to pwp_id — Cat No'),
			array('key' => 'product_id', 'value' => '', 'type' => 'text', 'description' => 'Alt to pwp_id — product.id (send weight_id if multiple)'),
			array('key' => 'weight_id', 'value' => '', 'type' => 'text', 'description' => 'With product_id when multiple variants'),
			array('key' => 'party_id', 'value' => '', 'type' => 'text', 'description' => 'Alias of channel_partner_customer_id'),
		);
		foreach ($extra as $e) {
			if (!isset($keys[$e['key']])) {
				$fd[] = $e;
			}
		}
	}
}
unset($req);

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "OK postman cart docs updated\n";
