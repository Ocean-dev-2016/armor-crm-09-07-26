<?php
/**
 * Channel Partner web-login helpers (PHP 5.6 compatible).
 * CP system user: dealer_distributor_network.type=4 (new) or type=3 with channel_partner_flag=1 (legacy).
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
		$refType = isset($_SESSION[SITE_SESS . 'REFERANCE_TYPE']) ? (int) $_SESSION[SITE_SESS . 'REFERANCE_TYPE'] : 0;
		/* type 4 = Channel Partner login; type 3 = Customer (legacy CP users also type 3) */
		if ($refType != 3 && $refType != 4) {
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
		if ($refType == 4 || (int) $flag === 1) {
			return true;
		}
		/* Fallback: CP customers exist even if dump reset the flag to 0 */
		$cpCust = $db->rp_getTotalRecord(
			"channel_partner_customer",
			"channel_partner_id='" . $cpId . "' AND isDelete=0",
			0
		);
		return ((int) $cpCust > 0);
	}
}

if (!function_exists('cp_whatsapp_phone_digits')) {
	/**
	 * Return CP WhatsApp number as 91XXXXXXXXXX (empty if not found).
	 */
	function cp_whatsapp_phone_digits($db, $cpId)
	{
		$cpId = (int) $cpId;
		if ($cpId <= 0 || !is_object($db)) {
			return '';
		}
		$phone = '';
		$ddn = $db->rp_getData(
			"dealer_distributor_network",
			"phone,username",
			"customer_id='" . $cpId . "' AND type IN ('3','4') AND isDelete=0",
			"id DESC",
			0
		);
		if ($ddn) {
			$dd = mysqli_fetch_assoc($ddn);
			if ($dd) {
				$phone = !empty($dd['phone']) ? $dd['phone'] : $dd['username'];
			}
		}
		if ($phone == '') {
			$phone = $db->rp_getValue("executive", "mobile_no1", "id='" . $cpId . "'", 0);
		}
		if ($phone == '') {
			$phone = $db->rp_getValue("executive", "phone", "id='" . $cpId . "'", 0);
		}
		$digits = preg_replace('/\D+/', '', (string) $phone);
		if (strlen($digits) === 10) {
			return '91' . $digits;
		}
		if (strlen($digits) === 12 && substr($digits, 0, 2) === '91') {
			return $digits;
		}
		return '';
	}
}

if (!function_exists('cp_whatsapp_share_url')) {
	/**
	 * Build WhatsApp Web share URL (api.whatsapp.com) for CP number + message text.
	 */
	function cp_whatsapp_share_url($phoneDigits, $text)
	{
		$q = 'text=' . rawurlencode($text);
		if ($phoneDigits != '') {
			$q = 'phone=' . rawurlencode($phoneDigits) . '&' . $q;
		}
		return 'https://api.whatsapp.com/send?' . $q;
	}
}
