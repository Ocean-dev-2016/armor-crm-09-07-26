<?php
/**
 * Generates a Postman v2.1 collection (multipart/form-data) for every API
 * defined in the `api_table` of the CRM database dump.
 *
 * Usage:  php generate_api_collection.php
 * Output: armor_crm_api_collection.postman.json (workspace root)
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$SQL_FILE   = __DIR__ . '/../crm2025.sql';
$OUT_FILE   = __DIR__ . '/../../armor_crm_api_collection.postman.json';
$BASE_URL   = 'https://crm.mahadevcasting.in/202526';
$API_KEY    = '1226';

if (!file_exists($SQL_FILE)) {
    fwrite(STDERR, "SQL dump not found: $SQL_FILE\n");
    exit(1);
}

$sql = file_get_contents($SQL_FILE);

/* ------------------------------------------------------------------ *
 * 1. Extract every row of every `INSERT INTO `api_table` ... VALUES`  *
 * ------------------------------------------------------------------ */
$rows = [];
$offset = 0;
$needle = "INSERT INTO `api_table`";
while (($pos = strpos($sql, $needle, $offset)) !== false) {
    $vpos = strpos($sql, 'VALUES', $pos);
    if ($vpos === false) break;
    $i = $vpos + strlen('VALUES');
    $parsed = parse_values($sql, $i); // returns [rows[], endPos]
    $rows = array_merge($rows, $parsed[0]);
    $offset = $parsed[1];
}

/**
 * Parse a MySQL "VALUES (..),(..);" tuple list starting at $i.
 * Returns [array_of_rows, position_after_terminating_semicolon].
 */
function parse_values($s, $i)
{
    $len = strlen($s);
    $rows = [];
    while ($i < $len) {
        // skip whitespace and commas between tuples
        while ($i < $len && ($s[$i] === ' ' || $s[$i] === "\n" || $s[$i] === "\r" || $s[$i] === "\t" || $s[$i] === ',')) {
            $i++;
        }
        if ($i >= $len) break;
        if ($s[$i] === ';') { $i++; break; }
        if ($s[$i] !== '(') { $i++; continue; }
        $i++; // consume '('
        $fields = [];
        $buf = '';
        $inStr = false;
        while ($i < $len) {
            $c = $s[$i];
            if ($inStr) {
                if ($c === '\\') {          // backslash escape
                    $next = $s[$i + 1] ?? '';
                    switch ($next) {
                        case 'n': $buf .= "\n"; break;
                        case 'r': $buf .= "\r"; break;
                        case 't': $buf .= "\t"; break;
                        default:  $buf .= $next; break;
                    }
                    $i += 2;
                    continue;
                }
                if ($c === "'") {
                    // doubled '' escape
                    if (($s[$i + 1] ?? '') === "'") { $buf .= "'"; $i += 2; continue; }
                    $inStr = false; $i++; continue;
                }
                $buf .= $c; $i++; continue;
            } else {
                if ($c === "'") { $inStr = true; $i++; continue; }
                if ($c === ',') { $fields[] = trim_field($buf); $buf = ''; $i++; continue; }
                if ($c === ')') { $fields[] = trim_field($buf); $buf = ''; $i++; break; }
                $buf .= $c; $i++; continue;
            }
        }
        if (count($fields) >= 5) $rows[] = $fields;
    }
    return [$rows, $i];
}

function trim_field($v)
{
    $v = trim($v);
    if (strtoupper($v) === 'NULL') return null;
    return $v;
}

/* ------------------------------------------------------------------ *
 * 2. Build Postman items grouped by service file                     *
 * ------------------------------------------------------------------ */
$folders = [];   // basename => [items]

foreach ($rows as $r) {
    // columns: id, api_slug, api_title, api_description, api_url, ...
    $id       = $r[0];
    $slug     = $r[1];
    $title    = $r[2];
    $descHtml = $r[3];
    $apiUrl   = $r[4];
    $isDelete = isset($r[7]) ? $r[7] : '0';

    if ($isDelete === '1') continue;            // skip soft-deleted
    if ($apiUrl === null || trim($apiUrl) === '') continue;

    $apiUrl = trim($apiUrl);
    $apiUrl = str_replace('.php.php', '.php', $apiUrl);   // fix documented typos

    // split file + query
    $qpos = strpos($apiUrl, '?');
    if ($qpos === false) { $filePart = $apiUrl; $query = ''; }
    else { $filePart = substr($apiUrl, 0, $qpos); $query = substr($apiUrl, $qpos + 1); }

    // basename of the service file (drop any absolute host/path)
    $filePart = str_replace('\\', '/', $filePart);
    $basename = trim(substr($filePart, strrpos($filePart, '/') === false ? 0 : strrpos($filePart, '/') + 1));
    if ($basename === '') $basename = 'service_genral.php';

    // parse query into ordered params (dedupe by key, keep first)
    $params = [];
    if ($query !== '') {
        foreach (explode('&', $query) as $piece) {
            if ($piece === '') continue;
            $eq = strpos($piece, '=');
            if ($eq === false) { $k = $piece; $v = ''; }
            else { $k = substr($piece, 0, $eq); $v = substr($piece, $eq + 1); }
            $k = trim($k);
            if ($k === '') continue;
            if (!array_key_exists($k, $params)) $params[$k] = $v;
        }
    }

    // guarantee auth params
    if (!array_key_exists('key', $params)) $params = ['key' => $API_KEY] + $params;
    // extract service code for description
    $serviceCode = isset($params['s']) ? $params['s'] : '';

    // build multipart/form-data fields
    $formdata = [];
    foreach ($params as $k => $v) {
        $isFile = (bool) preg_match('/(^|_)(image|images|img|photo|file|attachment|attach|logo)(_|$)/i', $k);
        if ($isFile) {
            $formdata[] = ['key' => $k, 'type' => 'file', 'src' => [], 'description' => 'file upload (multipart)'];
        } else {
            $formdata[] = ['key' => $k, 'value' => (string)$v, 'type' => 'text'];
        }
    }

    $descText = trim(html_entity_decode(strip_tags(str_replace(['<br />', '<br/>', '<br>'], "\n", $descHtml)), ENT_QUOTES));
    $descriptionParts = [];
    $descriptionParts[] = "**API Code:** {$id}";
    $descriptionParts[] = "**Slug:** {$slug}";
    if ($serviceCode !== '') $descriptionParts[] = "**Service (s):** {$serviceCode}";
    if ($descText !== '') $descriptionParts[] = "\n" . $descText;
    $descriptionParts[] = "\n**Documented example:** `service/{$apiUrl}`";
    $description = implode("\n\n", $descriptionParts);

    $item = [
        'name' => trim("{$title}  (#{$id})"),
        'request' => [
            'method' => 'POST',
            'header' => [],
            'body' => [
                'mode' => 'formdata',
                'formdata' => $formdata,
            ],
            'url' => [
                'raw'  => "{{base_url}}/service/{$basename}",
                'host' => ['{{base_url}}'],
                'path' => ['service', $basename],
            ],
            'description' => $description,
        ],
        'response' => [],
    ];

    $folders[$basename][] = $item;
}

/* ------------------------------------------------------------------ *
 * 3. Assemble collection                                             *
 * ------------------------------------------------------------------ */
ksort($folders);
$folderItems = [];
$total = 0;
foreach ($folders as $file => $items) {
    $total += count($items);
    $folderItems[] = [
        'name' => $file . ' (' . count($items) . ')',
        'item' => $items,
    ];
}

$collection = [
    'info' => [
        'name'        => 'Armor Fire CRM - Full API Collection',
        'description' => "Complete API collection for Armor Fire / Mahadev Casting CRM.\n\n"
            . "All requests use POST with body type multipart/form-data.\n"
            . "Auth: every request must include `key` (API key) and `s` (service code) as form-data fields.\n\n"
            . "Base service endpoint: {$BASE_URL}/service/<service_file>.php\n"
            . "API key: {$API_KEY}\n"
            . "Total endpoints: {$total}\n\n"
            . "Set the `base_url` collection variable to switch environments.",
        'schema'      => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    ],
    'item' => $folderItems,
    'variable' => [
        ['key' => 'base_url', 'value' => $BASE_URL, 'type' => 'string'],
        ['key' => 'key',      'value' => $API_KEY,  'type' => 'string'],
    ],
];

file_put_contents(
    $OUT_FILE,
    json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "Parsed rows: " . count($rows) . "\n";
echo "Endpoints written: {$total}\n";
echo "Service files: " . count($folders) . "\n";
echo "Output: " . realpath($OUT_FILE) . "\n";
