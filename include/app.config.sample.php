<?php
/**
 * Copy this file to app.config.php (local) or app.config.live.php (production).
 * Never commit real credentials to Git.
 */
return array(
	'environment' => 'local',
	'site_url' => 'http://localhost:8080/armor_crm_08_07/202526/',
	'db_host' => 'localhost',
	'db_user' => 'your_db_user',
	'db_pass' => 'your_db_password',
	'db_name' => 'your_db_name',
	'db_ports' => array(3307, 3306),
);
