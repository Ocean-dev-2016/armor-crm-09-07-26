<?php
/**
 * Channel Partner web-login helpers (PHP 5.6 compatible).
 * CP system user: dealer_distributor_network.type=3, customer_id = executive.id (channel_partner_flag=1).
 */

if (!function_exists('cp_get_login_channel_partner_id')) {
	function cp_get_login_channel_partner_id()
	{
		if (!isset($_SESSION[SITE_SESS . 'REFERANCE_ID'])) {
			return 0;
		}
		return (int) $_SESSION[SITE_SESS . 'REFERANCE_ID'];
	}
}

if (!function_exists('cp_is_channel_partner_login')) {
	function cp_is_channel_partner_login($db)
	{
		if (!isset($_SESSION[SITE_SESS . 'REFERANCE_TYPE']) || (int) $_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 3) {
			return false;
		}
		$cpId = cp_get_login_channel_partner_id();
		if ($cpId <= 0 || !is_object($db)) {
			return false;
		}
		$flag = $db->rp_getValue(
			"executive",
			"channel_partner_flag",
			"id='" . $cpId . "' AND isDelete=0",
			0
		);
		return ((int) $flag === 1);
	}
}
