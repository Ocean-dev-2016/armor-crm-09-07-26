<?php
$path = dirname(__FILE__) . '/armor_crm_api_collection.postman.json';
$j = json_decode(file_get_contents($path), true);
if (!$j) {
	exit(1);
}

/*
 * Clean confusion: TWO #250 requests were same API in two folders.
 * Keep ONE official folder with verified Aarav CP12229 values.
 * Remove "CP READY" demo folder duplicate.
 */
$newItems = array();
$cartFolderIdx = -1;
foreach ($j['item'] as $it) {
	$name = isset($it['name']) ? $it['name'] : '';
	if (strpos($name, 'CP READY') !== false) {
		echo "Removing duplicate demo folder: $name\n";
		continue;
	}
	$newItems[] = $it;
	if (strpos($name, 'Customer Order + Cart') !== false) {
		$cartFolderIdx = count($newItems) - 1;
	}
}
$j['item'] = $newItems;

if ($cartFolderIdx < 0) {
	fwrite(STDERR, "Cart folder not found\n");
	exit(1);
}

$stockNote = "Channel Partner App — Customer Order + Cart (#248-#256)\n"
	. "SAME as web CP login (Aarav / channel_partner_id) — App draft cart.\n"
	. "Web form has no cart; App uses #249-#253 draft order (status=-1).\n\n"
	. "**Who:** Only Channel Partner login. Always send channel_partner_id from Login #2.\n"
	. "**Stock products only for that CP** (My Stock #257).\n\n"
	. "LIVE verified Aarav Safety Solutions (CP 12229):\n"
	. "Party: channel_partner_customer_id=8 Orbit Electricals\n"
	. "pwp_id map (from #248):\n"
	. "  CatNo 2301 → pwp_id 2095 (stock 27, rate 9300)\n"
	. "  CatNo 2893 → pwp_id 2119 (stock 30, rate 975)\n"
	. "  CatNo 2290 → pwp_id 2106 (stock 33, rate 5700)\n"
	. "  CatNo 2293 → pwp_id 2096 (stock 22, rate 12009)\n\n"
	. "**Add Cart #250 (ONLY ONE API):** key, s=250, channel_partner_id, channel_partner_customer_id, pwp_id, qty\n"
	. "Do NOT send products field.\n"
	. "Flow: #257 → #241 → #248 → #250 → #249 → #254";

$j['item'][$cartFolderIdx]['description'] = $stockNote;

function set_fd(&$fd, $key, $value, $desc = null, $disabled = null)
{
	foreach ($fd as &$f) {
		if ($f['key'] === $key) {
			$f['value'] = (string) $value;
			if ($desc !== null) {
				$f['description'] = $desc;
			}
			if ($disabled === true) {
				$f['disabled'] = true;
			} elseif ($disabled === false) {
				unset($f['disabled']);
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
	$fd[] = $item;
}

/* Ensure only one #250 and values are CP-login ready */
$count250 = 0;
foreach ($j['item'][$cartFolderIdx]['item'] as &$req) {
	$name = isset($req['name']) ? $req['name'] : '';
	if (!isset($req['request']['body']['formdata'])) {
		continue;
	}
	$fd = &$req['request']['body']['formdata'];
	set_fd($fd, 'key', '1226');
	set_fd($fd, 'channel_partner_id', '12229', 'From Login #2 — Aarav Safety Solutions (demo CP)');

	if (strpos($name, '#248') !== false) {
		set_fd($fd, 'search_name', 'TWIST', 'Returns pwp_id 2095 (CatNo 2301) for this CP stock');
		set_fd($fd, 'only_in_stock', '1');
		$req['name'] = 'CP Order Products (#248)';
	}
	if (strpos($name, '#249') !== false) {
		set_fd($fd, 'channel_partner_customer_id', '8', 'Orbit Electricals — binds party on cart');
		$req['name'] = 'CP Order Get Cart (#249)';
	}
	if (strpos($name, '#250') !== false) {
		$count250++;
		$req['name'] = 'CP Order Add Cart (#250)';
		$req['request']['description'] = "ONE Add-Cart API for Channel Partner App.\n"
			. "Verified live: CP 12229 + party 8 + pwp_id 2095 + qty 1 → ack=1.\n"
			. "Required: channel_partner_id, channel_partner_customer_id, pwp_id, qty\n"
			. "Leave products EMPTY.";
		set_fd($fd, 'channel_partner_customer_id', '8', 'From #241 — Orbit Electricals');
		set_fd($fd, 'gst_apply_flag', '1');
		set_fd($fd, 'pwp_id', '2095', 'From #248 — CatNo 2301 (CP stock product)');
		set_fd($fd, 'qty', '1');
		set_fd($fd, 'rate', '9300', 'Optional');
		set_fd($fd, 'discount', '0');
		set_fd($fd, 'products', '', 'LEAVE EMPTY — do not send', true);
	}
	if (strpos($name, '#253') !== false) {
		$req['name'] = 'CP Order Clear Cart (#253)';
	}
	if (strpos($name, '#254') !== false) {
		set_fd($fd, 'channel_partner_customer_id', '8');
		set_fd($fd, 'gst_apply_flag', '1');
	}
}
unset($req);

/* Count all #250 across collection */
$total250 = 0;
$paths = array();
foreach ($j['item'] as $folder) {
	$fname = isset($folder['name']) ? $folder['name'] : '';
	if (!isset($folder['item'])) {
		continue;
	}
	foreach ($folder['item'] as $req) {
		$n = isset($req['name']) ? $req['name'] : '';
		$sVal = '';
		if (isset($req['request']['body']['formdata'])) {
			foreach ($req['request']['body']['formdata'] as $f) {
				if ($f['key'] === 's') {
					$sVal = $f['value'];
				}
			}
		}
		if ($sVal === '250' || strpos($n, '#250') !== false) {
			$total250++;
			$paths[] = $fname . ' → ' . $n;
		}
	}
}

$j['info']['description'] = preg_replace('/\n\*\*CP READY demo:.*$/s', '', $j['info']['description']);
if (strpos($j['info']['description'], 'ONE Add Cart #250') === false) {
	$j['info']['description'] .= "\n**CP Cart:** ONE Add Cart API #250 (service_channel_partner.php). Folder: Channel Partner App — Customer Order + Cart. Demo values: CP 12229 / party 8 / pwp_id 2095.";
}

file_put_contents($path, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Cart folder #250 count in folder: $count250\n";
echo "Total #250 requests in collection: $total250\n";
foreach ($paths as $p) {
	echo "  - $p\n";
}
echo "OK\n";
