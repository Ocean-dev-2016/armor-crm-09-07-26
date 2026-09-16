<?php
/**
 * Fix mojibake in app_crm_docs HTML/MD (UTF-8 Gujarati + punctuation).
 * Run: php database/fix_docs_encoding.php
 */
$files = array(
	__DIR__ . '/app_crm_docs/frontend.html',
	__DIR__ . '/app_crm_docs/backend.html',
	__DIR__ . '/app_crm_docs/index.html',
	__DIR__ . '/APP_CRM_FRONTEND_CHANGES.md',
	__DIR__ . '/APP_CRM_BACKEND_CHANGES.md',
);

/* Common punctuation mojibake */
$common = array(
	'Â·' => '·',
	'â€”' => '—',
	'â€“' => '–',
	'â€¢' => '•',
	'â†’' => '→',
	'â€™' => "'",
	'â€œ' => '"',
	'â€' => '"',
	'1â€“19' => '1–19',
	'271â€“273' => '271–273',
	'233â€“235' => '233–235',
);

/* Gujarati / mixed strings that got double-encoded */
$gujarati = array(
	'àª†àªœàª¨à«‹ àªªà«àª²àª¾àª¨' => 'આજનો પ્લાન',
	'àª¶à«àª‚ àª†àªœà«‡ àª•à«‡àªŸàª²àª¾ àª°à«‚àªªàª¿àª¯àª¾àª¨àª¾ àª“àª°à«àª¡àª° àª†àªµàª¶à«‡?' => 'શું આજે કેટલા રૂપિયાના ઓર્ડર આવશે?',
	'àª¶à«àª‚ àª†àªœà«‡ àª•à«‡àªŸàª²àª¾ Approval àª†àªµàª¶à«‡?' => 'શું આજે કેટલા Approval આવશે?',
	'àª†àªœà«‡ àª•à«‡àªŸàª²àª¾ project àª¨à«€ Detail àª†àªµàª¶à«‡?' => 'આજે કેટલા project ની Detail આવશે?',
	'àª•à«ƒàªªàª¾ àª•àª°à«€àª¨à«‡ àª“àª›àª¾àª®àª¾àª‚ àª“àª›à«àª‚ àªàª• target àª­àª°à«‹' => 'કૃપા કરીને ઓછામાં ઓછું એક target ભરો',
	'àª†àªœà«‡ àª•à«‡àªŸàª²à«àª‚ complete àª¥àª¯à«àª‚' => 'આજે કેટલું complete થયું',
	'Punch out àª•àª°àª¤àª¾ àªªàª¹à«‡àª²àª¾àª‚ completion submit àª•àª°àªµà«àª‚ àªœàª°à«‚àª°à«€ àª›à«‡' => 'Punch out કરતા પહેલાં completion submit કરવું જરૂરી છે',
	'Visit Start Type àªªàª¸àª‚àª¦ àª•àª°à«‹' => 'Visit Start Type પસંદ કરો',
	'àª¶à«àª‚ àª† visit àª®àª¾àª‚ order àª†àªµà«àª¯à«‹?' => 'શું આ visit માં order આવ્યો?',
	'àª¶à«àª‚ àª† visit àª®àª¾àª‚ approval àª†àªµà«àª¯à«àª‚?' => 'શું આ visit માં approval આવ્યું?',
	'àª¶à«àª‚ àª† visit àª®àª¾àª‚ project details àª†àªµà«€?' => 'શું આ visit માં project details આવી?',
	'àª¶à«àª‚ àª† visit àª®àª¾àª‚ contract details àª†àªµà«€?' => 'શું આ visit માં contract details આવી?',
	'àª¶à«àª‚ àª† visit àª®àª¾àª‚ payment àª†àªµà«àª¯à«àª‚?' => 'શું આ visit માં payment આવ્યું?',
	'Submit àªªàª¹à«‡àª²àª¾àª‚' => 'Submit પહેલાં',
	'Customer àªªàª¸àª‚àª¦ àª•àª°à«‹' => 'Customer પસંદ કરો',
	'Punch out àªªàª¹à«‡àª²àª¾àª‚ completion submit àª•àª°à«‹' => 'Punch out પહેલાં completion submit કરો',
	'Visit type àªªàª¸àª‚àª¦ àª•àª°à«‹' => 'Visit type પસંદ કરો',
	'àª¬àª§àª¾ àªªà«àª°àª¶à«àª¨à«‹àª¨àª¾ àªœàªµàª¾àª¬ àª†àªªà«‹' => 'બધા પ્રશ્નોના જવાબ આપો',
	'Submit àªªàª¹à«‡àª²àª¾àª‚ questionnaire àªªà«‚àª°à«àª£ àª•àª°à«‹' => 'Submit પહેલાં questionnaire પૂર્ણ કરો',
	'High Rate form àª­àª°à«‹' => 'High Rate form ભરો',
	'Approval form àª­àª°à«‹' => 'Approval form ભરો',
);

/* Also fix broken heading from earlier replace */
$common['SAVE_DAILY_PLAN_PLACEHOLDER</span>'] = 'save_daily_plan</code> <span class="svc">271</span>';
$common['SAVE_DAILY_PLAN_PLACEHOLDER'] = 'save_daily_plan';

$map = array_merge($common, $gujarati);

foreach ($files as $f) {
	if (!file_exists($f)) {
		echo "SKIP missing: $f\n";
		continue;
	}
	$c = file_get_contents($f);
	$orig = $c;
	$c = str_replace(array_keys($map), array_values($map), $c);

	/* Ensure HTML has charset meta already; force UTF-8 save without BOM */
	if (substr($f, -5) === '.html' && strpos($c, 'charset=UTF-8') === false && strpos($c, 'charset="UTF-8"') === false) {
		$c = preg_replace('/<meta charset="[^"]*">/', '<meta charset="UTF-8">', $c, 1);
	}

	if ($c !== $orig) {
		file_put_contents($f, $c);
		echo "FIXED: " . basename($f) . "\n";
	} else {
		echo "OK (no change): " . basename($f) . "\n";
	}

	/* Remaining mojibake check */
	$bad = 0;
	if (preg_match_all('/àª|Â·|â€/', $c, $m)) {
		$bad = count($m[0]);
	}
	echo "  remaining bad markers: $bad\n";
}

echo "DONE\n";
