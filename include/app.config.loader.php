<?php
function armor_get_app_config()
{
	static $config = null;
	if ($config !== null) {
		return $config;
	}

	$configFile = __DIR__ . '/app.config.php';
	if (!file_exists($configFile) && isset($_SERVER['HTTP_HOST'])) {
		$host = $_SERVER['HTTP_HOST'];
		if ($host !== 'localhost' && strpos($host, '127.0.0.1') === false) {
			$liveConfig = __DIR__ . '/app.config.live.php';
			if (file_exists($liveConfig)) {
				$configFile = $liveConfig;
			}
		}
	}

	if (!file_exists($configFile)) {
		$configFile = __DIR__ . '/app.config.sample.php';
	}

	$config = include $configFile;
	if (!is_array($config)) {
		$config = array();
	}

	return $config;
}
