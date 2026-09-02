<?php
function armor_get_app_config()
{
	static $config = null;
	if ($config !== null) {
		return $config;
	}

	$defaults = array(
		'environment' => 'live',
		'site_url' => 'https://armor-crm.oceanhub.co.in/',
		'db_host' => 'localhost',
		'db_user' => 'jrosvllq_armor_crm_09_07',
		'db_pass' => 'ZA.e9[wUlgiu{6(%',
		'db_name' => 'jrosvllq_armor_crm_09_07',
		'db_ports' => array(3306, 3307),
	);

	$configFile = __DIR__ . '/app.config.php';
	$userConfig = (file_exists($configFile) && is_file($configFile)) ? include $configFile : array();
	if (!is_array($userConfig)) {
		$userConfig = array();
	}

	$config = array_merge($defaults, $userConfig);

	// Auto-detect domain from HTTP_HOST
	if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
		$host = $_SERVER['HTTP_HOST'];
		if ($host === 'localhost' || strpos($host, 'localhost:') === 0 || $host === '127.0.0.1') {
			$config['site_url'] = 'http://localhost:8080/armor_crm_08_07/202526/';
		} else {
			$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
			$config['site_url'] = $proto . rtrim($host, '/') . '/';
		}
	}

	return $config;
}
