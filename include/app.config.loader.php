<?php
function armor_get_app_config()
{
	static $config = null;
	if ($config !== null) {
		return $config;
	}

	$configFile = __DIR__ . '/app.config.php';
	if (!file_exists($configFile)) {
		$configFile = __DIR__ . '/app.config.sample.php';
	}

	$config = is_file($configFile) ? include $configFile : array();
	if (!is_array($config)) {
		$config = array();
	}

	// Auto-detect live server domain from HTTP_HOST
	if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
		$host = $_SERVER['HTTP_HOST'];
		if ($host !== 'localhost' && $host !== '127.0.0.1' && strpos($host, 'localhost:') !== 0) {
			$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
			$config['site_url'] = $proto . rtrim($host, '/') . '/';
		}
	}

	return $config;
}
