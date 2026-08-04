<?php
/**
 * Generates Postman v2.1 collection from live service/*.php source code.
 * Merges api_table metadata from DB when app.config.php is available.
 *
 * Usage: php service/generate_api_collection.php
 * Output: armor_crm_api_collection.postman.json (project root)
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$BASE_URL   = 'https://armor-crm.oceanhub.co.in';
$API_KEY    = '1226';
$OUT_FILE   = dirname(__DIR__) . '/armor_crm_api_collection.postman.json';
$SERVICE_DIR = __DIR__;

/* ------------------------------------------------------------------ *
 * Example values + field notes (backend vs API param name)
 * ------------------------------------------------------------------ */
$PARAM_EXAMPLES = array(
    'key' => '1226',
    's' => '',
    'id' => '',
    'sales_id' => '1',
    'sales_executive_id' => '1',
    'customer_id' => '1',
    'cid' => '1',
    'uid' => '1',
    'user_id' => '1',
    'company_id' => '1',
    'dealer_id' => '1',
    'inquiry_id' => '1',
    'order_id' => '1',
    'quotation_id' => '1',
    'cart_id' => '1',
    'dispatch_id' => '1',
    'lead_id' => '1',
    'mode' => 'add',
    'flag' => '0',
    'entry_flag' => '5',
    'update_entry_flag' => '0',
    'type' => '',
    'phone' => '9876543210',
    'mobile_number' => '9876543210',
    'mobile_no' => '9876543210',
    'password' => '123456',
    'new_password' => '654321',
    'username' => 'sales_user',
    'email' => 'customer@example.com',
    'email_address' => 'customer@example.com',
    'email_cc' => 'cc@example.com',
    'company_name' => 'Test Firm Pvt Ltd',
    'person_name' => 'John Doe',
    'cname' => 'John Doe',
    'address' => '123 Main Street',
    'address1' => 'Near City Mall',
    'shipping_address' => '123 Main Street, Ahmedabad',
    'billing_address' => '123 Main Street, Ahmedabad',
    'country' => 'India',
    'state' => 'Gujarat',
    'city' => 'Route Name / Area Route',
    'main_city' => 'Ahmedabad',
    'pincode' => '380001',
    'zip' => '380001',
    'latitude' => '23.0225',
    'longitude' => '72.5714',
    'gst' => '24AAAAA0000A1Z5',
    'gst_no' => '24AAAAA0000A1Z5',
    'pan' => 'AAAAA0000A',
    'pan_no' => 'AAAAA0000A',
    'class_id' => '1',
    'area_id' => '12',
    'price_list_id' => '1',
    'top_category_id' => '1',
    'company_type' => '0',
    'company_type_id' => '1',
    'type_of_executive' => 'outlets',
    'customer_flag' => '0',
    'channel_partner_flag' => '1',
    'client_code' => '',
    'super_stockist_id' => '0',
    'dealer_distributor_id' => '0',
    'whatsapp_no' => '9876543210',
    'turnover' => '5000000',
    'turnover_year' => '2025',
    'industry_type_id' => '1',
    'zone' => '1',
    'remark' => 'Test remark',
    'booking_place' => 'Ahmedabad',
    'transport_by_id' => '1',
    'transporter_id' => '1',
    'purchasing_from' => 'Local Market',
    'from_date' => '2025-01-01',
    'to_date' => '2025-12-31',
    'FromDate' => '2025-01-01',
    'ToDate' => '2025-12-31',
    'date' => date('Y-m-d'),
    'ul' => '0',
    'll' => '50',
    'term' => 'test',
    'searchName' => 'product',
    'search_name' => 'product',
    'imei' => '123456789012345',
    'refreshToken' => 'device_refresh_token',
    'products' => '[{"pid":"1","qty":"10","weight_id":"1"}]',
    'result' => '[]',
    'areas' => '[{"local_id":"1","area_id":"12","class_id":"1","executive_id":"1"}]',
    'channel_partner_id' => '12213',
    'mobile_no' => '9876543210',
    'gst' => '24AAAAA0000A1Z5',
);

$PARAM_NOTES = array(
    'pan_no' => 'API param pan_no maps to DB column pan',
    'company_type_id' => 'API param company_type_id maps to DB column type_of_company',
    'gst_no' => 'Use gst_no (not gst) for service 39 sync_offline_customers',
    'main_city' => 'CRM City column',
    'city' => 'CRM Route column (route/area name)',
    'area_id' => 'CRM Route ID; auto-resolved from main_city if empty',
    'person_name' => 'CRM Person Name (saved as cname in executive table)',
    'mobile_number' => 'CRM Phone (saved as mobile_no1)',
    'channel_partner_flag' => '0=No, 1=Yes (Channel Partner checkbox)',
    'sales_id' => 'Sales Person / executive seid',
    'client_code' => 'Leave empty on add for auto-generation',
    'entry_flag' => 'Default 5 for mobile offline sync',
    'channel_partner_id' => 'executive.id where channel_partner_flag=1 (Select Channel Partner)',
);

$SERVICE_CP_CUSTOMER_PARAMS = array(
    'channel_partner_id', 'company_name', 'person_name', 'mobile_no', 'email', 'gst',
    'address', 'country', 'state', 'city', 'pincode', 'id', 'search_name', 'ul', 'll',
);

$SERVICE_CP_CUSTOMER_MAP = "**Channel Partner Customer APIs (223-228) — same as web CRM form:**\n"
    . "| Web Field | API Parameter |\n|-----------|---------------|\n"
    . "| Select Channel Partner | channel_partner_id |\n"
    . "| Customer Name | company_name |\n"
    . "| Person Name | person_name |\n"
    . "| Mobile No | mobile_no |\n"
    . "| Email | email |\n"
    . "| GST | gst |\n"
    . "| Address | address (optional) |\n"
    . "| Country | country |\n"
    . "| State | state |\n"
    . "| City | city |\n"
    . "| Pincode | pincode |\n"
    . "| Edit/Delete id | id |\n";

$SERVICE_39_PARAMS = array(
    'id', 'type_of_executive', 'customer_flag', 'channel_partner_flag', 'sales_id',
    'company_name', 'person_name', 'mobile_number', 'phone', 'email', 'address', 'address1',
    'gst_no', 'super_stockist_id', 'dealer_id', 'country', 'state', 'city', 'main_city',
    'class_id', 'area_id', 'latitude', 'longitude', 'whatsapp_no', 'price_list_id',
    'company_type', 'pincode', 'email_cc', 'client_code', 'pan_no', 'zone',
    'industry_type_id', 'top_category_id', 'entry_flag', 'shipping_address', 'billing_address',
    'company_type_id', 'remark', 'booking_place', 'transport_by_id', 'transporter_id',
    'purchasing_from', 'turnover', 'turnover_year', 'type',
);

$SERVICE_39_CRM_MAP = "**CRM Column Mapping (Service #39):**\n"
    . "| CRM Field | API Parameter |\n|-----------|---------------|\n"
    . "| Price List | price_list_id |\n"
    . "| Sales Person | sales_id |\n"
    . "| Client Code | client_code (auto if empty) |\n"
    . "| Firm Name | company_name |\n"
    . "| Person Name | person_name |\n"
    . "| GST | gst_no |\n"
    . "| Phone | mobile_number |\n"
    . "| State | state |\n"
    . "| City | main_city |\n"
    . "| Route | city + area_id |\n"
    . "| Pincode | pincode |\n"
    . "| Turnover | turnover |\n"
    . "| Turnover Year | turnover_year |\n"
    . "| Category | top_category_id |\n"
    . "| Channel Partner | channel_partner_flag |\n";

/* ------------------------------------------------------------------ *
 * Load api_table metadata from DB (optional)
 * ------------------------------------------------------------------ */
function load_api_table_rows()
{
    $rows = array();
    $configFile = dirname(__DIR__) . '/include/app.config.php';
    if (!file_exists($configFile)) {
        $live = dirname(__DIR__) . '/include/app.config.live.php';
        if (file_exists($live)) {
            $configFile = $live;
        } else {
            return $rows;
        }
    }
    $cfg = include $configFile;
    if (!is_array($cfg) || empty($cfg['db_name'])) {
        return $rows;
    }
    $ports = isset($cfg['db_ports']) ? $cfg['db_ports'] : array(3306);
    $conn = null;
    foreach ($ports as $port) {
        $conn = @mysqli_connect($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], (int)$port);
        if ($conn) break;
    }
    if (!$conn) return $rows;
    $res = @mysqli_query($conn, "SELECT id, api_slug, api_title, api_description, api_url, isDelete FROM api_table WHERE isDelete=0 ORDER BY id ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }
    }
    mysqli_close($conn);
    return $rows;
}

function parse_api_url($apiUrl)
{
    $apiUrl = trim(str_replace('.php.php', '.php', $apiUrl));
    $qpos = strpos($apiUrl, '?');
    if ($qpos === false) {
        $filePart = $apiUrl;
        $query = '';
    } else {
        $filePart = substr($apiUrl, 0, $qpos);
        $query = substr($apiUrl, $qpos + 1);
    }
    $filePart = str_replace('\\', '/', $filePart);
    $basename = trim(substr($filePart, strrpos($filePart, '/') === false ? 0 : strrpos($filePart, '/') + 1));
    if ($basename === '') {
        $basename = 'service_genral.php';
    }
    $params = array();
    if ($query !== '') {
        foreach (explode('&', $query) as $piece) {
            if ($piece === '') continue;
            $eq = strpos($piece, '=');
            if ($eq === false) {
                $k = $piece;
                $v = '';
            } else {
                $k = substr($piece, 0, $eq);
                $v = substr($piece, $eq + 1);
            }
            $k = trim(urldecode($k));
            if ($k === '') continue;
            if (!array_key_exists($k, $params)) {
                $params[$k] = urldecode($v);
            }
        }
    }
    return array('basename' => $basename, 'params' => $params);
}

function parse_all_code_endpoints($serviceDir)
{
    $map = array();
    $files = glob($serviceDir . '/service*.php');
    sort($files);
    foreach ($files as $filePath) {
        $basename = basename($filePath);
        if (is_backup_service_file($basename)) {
            continue;
        }
        $blocks = extract_service_blocks(file_get_contents($filePath));
        foreach ($blocks as $block) {
            $key = $basename . ':' . $block['id'];
            if (isset($map[$key])) {
                continue;
            }
            $map[$key] = array(
                'file' => $basename,
                'id' => $block['id'],
                'slug' => $block['slug'],
                'params' => extract_request_params($block['body']),
            );
        }
    }
    return $map;
}

/* ------------------------------------------------------------------ *
 * Parse service PHP files
 * ------------------------------------------------------------------ */
function is_backup_service_file($basename)
{
    if (!preg_match('/^service.*\.php$/i', $basename)) return true;
    if (preg_match('/service_genral\(/i', $basename)) return true;
    if (preg_match('/^\d+service_/i', $basename)) return true;
    if (preg_match('/^service_customer1\.php$/i', $basename)) return true;
    if ($basename === 'service_sales_tracking_transfer_data.php') return true;
    if ($basename === 'service_customer_lead.php') return true;
    if ($basename === 'generate_api_collection.php') return true;
    return false;
}

function slug_to_title($slug)
{
    return ucwords(str_replace('_', ' ', $slug));
}

function extract_service_blocks($content)
{
    $blocks = array();
  // Match: if / } else if / }*/ else if  +  $service == 'slug' || $service == 123
    if (!preg_match_all(
        '/(?:^|\n)\s*(?:\}\s*\*\/\s*|\}\s*)?(?:else\s+)?if\s*\(\s*\$service\s*==\s*(["\'])([^"\']+)\1\s*\|\|\s*\$service\s*==\s*(\d+)\s*\)/',
        $content,
        $matches,
        PREG_OFFSET_CAPTURE
    )) {
        return $blocks;
    }
    $count = count($matches[0]);
    for ($i = 0; $i < $count; $i++) {
        $start = $matches[0][$i][1] + strlen($matches[0][$i][0]);
        $end = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($content);
        $body = substr($content, $start, $end - $start);
        $blocks[] = array(
            'slug' => $matches[2][$i][0],
            'id' => $matches[3][$i][0],
            'body' => $body,
        );
    }
    return $blocks;
}

function extract_request_params($body)
{
    $params = array();
    if (preg_match_all('/\$_REQUEST\s*\[\s*([\'"])([^\'"]+)\1\s*\]/', $body, $m)) {
        foreach ($m[2] as $k) {
            $params[$k] = true;
        }
    }
    if (preg_match_all('/getRequestedParam\s*\(\s*([\'"])([^\'"]+)\1/', $body, $m2)) {
        foreach ($m2[2] as $k) {
            $params[$k] = true;
        }
    }
    if (preg_match('/\$_FILES/', $body)) {
        $params['__has_files__'] = true;
    }
    return array_keys($params);
}

function is_file_param($key)
{
    return (bool)preg_match('/(^|_)(image|images|img|photo|file|attachment|attach|logo)(_|$)/i', $key);
}

function example_value($key, $serviceId, $PARAM_EXAMPLES, $API_KEY)
{
    if ($key === 'key') return $API_KEY;
    if ($key === 's') return (string)$serviceId;
    if (isset($PARAM_EXAMPLES[$key])) return $PARAM_EXAMPLES[$key];
    if (preg_match('/_id$/', $key)) return '1';
    if (preg_match('/date/i', $key)) return date('Y-m-d');
    if (preg_match('/flag$/i', $key)) return '0';
    if (preg_match('/^(ul|ll)$/', $key)) return ($key === 'ul') ? '0' : '50';
    return 'sample';
}

function build_formdata($params, $serviceId, $PARAM_EXAMPLES, $PARAM_NOTES, $API_KEY)
{
    $ordered = array('key', 's');
    foreach ($params as $p) {
        if ($p === '__has_files__') continue;
        if (!in_array($p, $ordered, true)) {
            $ordered[] = $p;
        }
    }
    $formdata = array();
    foreach ($ordered as $k) {
        if ($k === '__has_files__') continue;
        $desc = isset($PARAM_NOTES[$k]) ? $PARAM_NOTES[$k] : '';
        if (is_file_param($k)) {
            $formdata[] = array(
                'key' => $k,
                'type' => 'file',
                'src' => array(),
                'description' => $desc !== '' ? $desc : 'Upload file (multipart/form-data)',
            );
        } else {
            $item = array(
                'key' => $k,
                'value' => example_value($k, $serviceId, $PARAM_EXAMPLES, $API_KEY),
                'type' => 'text',
            );
            if ($desc !== '') {
                $item['description'] = $desc;
            }
            $formdata[] = $item;
        }
    }
    if (in_array('__has_files__', $params, true)) {
        $formdata[] = array(
            'key' => 'image',
            'type' => 'file',
            'src' => array(),
            'description' => 'Optional file upload ($_FILES passed to handler)',
        );
    }
    return $formdata;
}

function build_description($id, $slug, $serviceId, $params, $metaRow, $extra = '')
{
    $parts = array();
    $parts[] = "**API Code:** {$id}";
    $parts[] = "**Slug:** {$slug}";
    $parts[] = "**Service (s):** {$serviceId}";
    if ($metaRow && !empty($metaRow['api_description'])) {
        $text = trim(html_entity_decode(strip_tags(str_replace(array('<br />', '<br/>', '<br>'), "\n", $metaRow['api_description'])), ENT_QUOTES));
        if ($text !== '') {
            $parts[] = $text;
        }
    }
    if ($extra !== '') {
        $parts[] = $extra;
    }
    if (!empty($params)) {
        $parts[] = "**Parameters from backend code:** " . implode(', ', array_filter($params, function ($p) {
            return $p !== '__has_files__';
        }));
    }
    return implode("\n\n", $parts);
}

$apiRows = load_api_table_rows();
$codeEndpoints = parse_all_code_endpoints($SERVICE_DIR);
$folders = array();
$seenApiIds = array();
$total = 0;

function merge_params_for_endpoint($serviceId, $basename, $urlParams, $codeEndpoints, $SERVICE_39_PARAMS)
{
    $params = array();
    $key = $basename . ':' . $serviceId;
    if (isset($codeEndpoints[$key])) {
        $params = $codeEndpoints[$key]['params'];
    }
    foreach (array_keys($urlParams) as $k) {
        if ($k !== 'key' && $k !== 's' && !in_array($k, $params, true)) {
            $params[] = $k;
        }
    }
    if ($serviceId === '39' && $basename === 'service_genral.php') {
        $params = $SERVICE_39_PARAMS;
        $params[] = '__has_files__';
    }
    if (in_array($serviceId, array('223', '224', '225', '226', '227', '228'), true) && $basename === 'service_genral.php') {
        $params = $SERVICE_CP_CUSTOMER_PARAMS;
    }
    return $params;
}

if (!empty($apiRows)) {
    foreach ($apiRows as $metaRow) {
        $apiId = (string)$metaRow['id'];
        if (isset($seenApiIds[$apiId])) {
            continue;
        }
        $seenApiIds[$apiId] = true;
        $apiUrl = isset($metaRow['api_url']) ? trim($metaRow['api_url']) : '';
        if ($apiUrl === '') {
            continue;
        }
        $parsed = parse_api_url($apiUrl);
        $basename = $parsed['basename'];
        $urlParams = $parsed['params'];
        $serviceId = isset($urlParams['s']) ? (string)$urlParams['s'] : $apiId;
        $slug = !empty($metaRow['api_slug']) ? $metaRow['api_slug'] : $serviceId;
        $title = !empty($metaRow['api_title']) ? $metaRow['api_title'] : slug_to_title($slug);

        $params = merge_params_for_endpoint($serviceId, $basename, $urlParams, $codeEndpoints, $SERVICE_39_PARAMS);
        $extraDesc = ($serviceId === '39' && $basename === 'service_genral.php') ? $SERVICE_39_CRM_MAP : '';
        if (in_array($serviceId, array('223', '224', '225', '226', '227', '228'), true) && $basename === 'service_genral.php') {
            $extraDesc = $SERVICE_CP_CUSTOMER_MAP;
        }

        $formdata = build_formdata($params, $serviceId, $PARAM_EXAMPLES, $PARAM_NOTES, $API_KEY);
        foreach ($formdata as &$fd) {
            if ($fd['key'] !== 'key' && $fd['key'] !== 's' && isset($urlParams[$fd['key']]) && $urlParams[$fd['key']] !== '' && $fd['type'] === 'text') {
                $fd['value'] = $urlParams[$fd['key']];
            }
        }
        unset($fd);

        $description = build_description($apiId, $slug, $serviceId, $params, $metaRow, $extraDesc);
        $item = array(
            'name' => trim("{$title}  (#{$apiId})"),
            'request' => array(
                'method' => 'POST',
                'header' => array(),
                'body' => array('mode' => 'formdata', 'formdata' => $formdata),
                'url' => array(
                    'raw' => "{{base_url}}/service/{$basename}",
                    'host' => array('{{base_url}}'),
                    'path' => array('service', $basename),
                ),
                'description' => $description,
            ),
            'response' => array(),
        );
        if (!isset($folders[$basename])) {
            $folders[$basename] = array();
        }
        $folders[$basename][] = $item;
        $total++;
    }
} else {
    foreach ($codeEndpoints as $ep) {
        $serviceId = $ep['id'];
        $basename = $ep['file'];
        $slug = $ep['slug'];
        $params = ($serviceId === '39' && $basename === 'service_genral.php') ? array_merge($SERVICE_39_PARAMS, array('__has_files__')) : $ep['params'];
        $extraDesc = ($serviceId === '39' && $basename === 'service_genral.php') ? $SERVICE_39_CRM_MAP : '';
        $formdata = build_formdata($params, $serviceId, $PARAM_EXAMPLES, $PARAM_NOTES, $API_KEY);
        $description = build_description($serviceId, $slug, $serviceId, $params, null, $extraDesc);
        $item = array(
            'name' => trim(slug_to_title($slug) . "  (#{$serviceId})"),
            'request' => array(
                'method' => 'POST',
                'header' => array(),
                'body' => array('mode' => 'formdata', 'formdata' => $formdata),
                'url' => array(
                    'raw' => "{{base_url}}/service/{$basename}",
                    'host' => array('{{base_url}}'),
                    'path' => array('service', $basename),
                ),
                'description' => $description,
            ),
            'response' => array(),
        );
        if (!isset($folders[$basename])) {
            $folders[$basename] = array();
        }
        $folders[$basename][] = $item;
        $total++;
    }
}

ksort($folders);
$folderItems = array();
foreach ($folders as $file => $items) {
    usort($items, function ($a, $b) {
        return strnatcasecmp($a['name'], $b['name']);
    });
    $folderItems[] = array(
        'name' => $file . ' (' . count($items) . ')',
        'item' => $items,
    );
}

$collection = array(
    'info' => array(
        'name' => 'Armor Fire CRM - Full API Collection (Backend Verified)',
        'description' => "Complete API collection generated from live PHP service source code.\n\n"
            . "Generated: " . date('Y-m-d H:i:s') . "\n"
            . "All requests: POST multipart/form-data\n"
            . "Auth: `key` (API key) + `s` (service id or slug)\n\n"
            . "Environments:\n"
            . "- Live: https://armor-crm.oceanhub.co.in\n"
            . "- Local: http://localhost:8080/armor_crm_08_07/202526\n\n"
            . "Set collection variable `base_url` before testing.\n"
            . "API key: {$API_KEY}\n"
            . "Total endpoints: {$total}\n\n"
            . "Service #39 (sync_offline_customers) includes all CRM columns + channel_partner_flag.",
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ),
    'item' => $folderItems,
    'variable' => array(
        array('key' => 'base_url', 'value' => $BASE_URL, 'type' => 'string'),
        array('key' => 'key', 'value' => $API_KEY, 'type' => 'string'),
    ),
);

file_put_contents(
    $OUT_FILE,
    json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "API table rows (DB): " . count($apiRows) . "\n";
echo "Code endpoints parsed: " . count($codeEndpoints) . "\n";
echo "Endpoints written: {$total}\n";
echo "Service files with APIs: " . count($folders) . "\n";
echo "Output: " . realpath($OUT_FILE) . "\n";
