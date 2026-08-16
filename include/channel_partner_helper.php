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

if (!function_exists('cp_order_payment_state')) {
	/**
	 * Order paid vs remaining. Completed only when remaining is 0.
	 * Example: 16000 order + 10000 received => pending 6000 (not Completed).
	 */
	function cp_order_payment_state($grandTotal, $paidAmount)
	{
		$grand = round((float) $grandTotal, 2);
		$paid = round((float) $paidAmount, 2);
		if ($paid < 0) {
			$paid = 0;
		}
		$remaining = round(max(0, $grand - $paid), 2);
		$isPaid = ($paid > 0.009 && $remaining <= 0.009);
		$isPartial = (!$isPaid && $paid > 0.009);
		return array(
			'order_amount' => $grand,
			'paid_amount' => $paid,
			'remaining_amount' => $remaining,
			'is_paid' => $isPaid ? 1 : 0,
			'is_partial' => $isPartial ? 1 : 0,
			'can_receive' => $isPaid ? 0 : 1,
			'status_key' => $isPaid ? 'received' : ($isPartial ? 'partial' : 'pending'),
			'status_label' => $isPaid
				? ('Received ' . number_format($paid, 2))
				: ($isPartial
					? ('Partial ' . number_format($paid, 2) . ' / Pending ' . number_format($remaining, 2))
					: 'Pending'),
			'status_short' => $isPaid ? 'RECEIVED' : ($isPartial ? 'PARTIAL' : 'PENDING'),
		);
	}
}

if (!function_exists('cp_prepare_receive_payment')) {
	/**
	 * Accumulate this receipt. Flag=1 only after remaining becomes 0.
	 */
	function cp_prepare_receive_payment($row, $thisAmount)
	{
		$grand = isset($row['grand_total']) ? (float) $row['grand_total'] : 0;
		$already = isset($row['payment_received_amount']) ? (float) $row['payment_received_amount'] : 0;
		$orderNo = isset($row['order_no']) ? $row['order_no'] : '';
		$state = cp_order_payment_state($grand, $already);
		if ((int) $state['can_receive'] !== 1) {
			return array(
				'ack' => 1,
				'already' => 1,
				'ack_msg' => 'Payment already fully received for ' . $orderNo,
				'new_paid' => $state['paid_amount'],
				'remaining' => 0,
				'flag' => 1,
				'this_amount' => 0,
			);
		}
		$thisAmount = round((float) $thisAmount, 2);
		if ($thisAmount <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Please enter Payment Received Amount.');
		}
		if ($thisAmount > ($state['remaining_amount'] + 0.009)) {
			return array(
				'ack' => 0,
				'ack_msg' => 'Amount cannot exceed pending ' . number_format($state['remaining_amount'], 2),
			);
		}
		$newPaid = round($already + $thisAmount, 2);
		$after = cp_order_payment_state($grand, $newPaid);
		$msg = $after['is_paid']
			? ('Payment fully received for Order ' . $orderNo . ' — Amount: ' . number_format($thisAmount, 2))
			: ('Partial payment saved for Order ' . $orderNo . ' — Received: ' . number_format($thisAmount, 2) . ', Pending: ' . number_format($after['remaining_amount'], 2));
		return array(
			'ack' => 1,
			'already' => 0,
			'this_amount' => $thisAmount,
			'new_paid' => $newPaid,
			'remaining' => $after['remaining_amount'],
			'flag' => $after['is_paid'] ? 1 : 0,
			'is_paid' => $after['is_paid'],
			'ack_msg' => $msg,
		);
	}
}

if (!function_exists('cp_ledger_payment_credits')) {
	/**
	 * Ledger credit rows: each receipt from payment table, else one row from orders.amount.
	 */
	function cp_ledger_payment_credits($db, $order, $partyName)
	{
		$orderId = (int) $order['id'];
		$orderNo = isset($order['order_no']) ? $order['order_no'] : '';
		$ptypeLabels = array(1 => 'Cash', 2 => 'Cheque', 3 => 'Online', 4 => 'Other');
		$rows = array();
		$seq = 0;
		$payTable = @mysqli_query($db->myconn, "SHOW TABLES LIKE 'payment'");
		if ($payTable && mysqli_num_rows($payTable) > 0) {
			$pr = $db->rp_getData(
				'payment',
				'id,paid_amount,payment_date,payment_type,remark,receipt_no',
				"invoice_id='" . $orderId . "' AND receipt_type=2 AND isDelete=0",
				'id ASC',
				0
			);
			if ($pr) {
				while ($p = mysqli_fetch_assoc($pr)) {
					$amt = (float) $p['paid_amount'];
					if ($amt <= 0.009) {
						continue;
					}
					$seq++;
					$pdate = (!empty($p['payment_date']) && $p['payment_date'] != '0000-00-00')
						? date('Y-m-d', strtotime($p['payment_date']))
						: (isset($order['order_date']) ? $order['order_date'] : date('Y-m-d'));
					$pt = isset($ptypeLabels[(int) $p['payment_type']]) ? $ptypeLabels[(int) $p['payment_type']] : 'Payment';
					$vch = !empty($p['receipt_no']) ? $p['receipt_no'] : ('RCPT/' . $orderNo . '-' . $seq);
					$rows[] = array(
						'date' => $pdate,
						'sort' => strtotime($pdate) . '2' . str_pad((string) $orderId, 8, '0', STR_PAD_LEFT) . str_pad((string) $seq, 4, '0', STR_PAD_LEFT),
						'particular' => 'Payment Received (' . $pt . ') against ' . $orderNo . ($partyName != '' ? ' — ' . $partyName : ''),
						'party' => $partyName,
						'vch' => $vch,
						'debit' => 0,
						'credit' => $amt,
						'type' => 'payment',
						'order_id' => $orderId,
					);
				}
			}
		}
		if (empty($rows)) {
			$amt = isset($order['payment_received_amount']) ? (float) $order['payment_received_amount'] : 0;
			if ($amt > 0.009) {
				$pdate = (!empty($order['payment_received_date']) && $order['payment_received_date'] != '0000-00-00 00:00:00')
					? date('Y-m-d', strtotime($order['payment_received_date']))
					: (isset($order['order_date']) ? $order['order_date'] : date('Y-m-d'));
				$pt = isset($ptypeLabels[(int) $order['payment_received_type']]) ? $ptypeLabels[(int) $order['payment_received_type']] : 'Payment';
				$rows[] = array(
					'date' => $pdate,
					'sort' => strtotime($pdate) . '2' . str_pad((string) $orderId, 8, '0', STR_PAD_LEFT) . '0001',
					'particular' => 'Payment Received (' . $pt . ') against ' . $orderNo . ($partyName != '' ? ' — ' . $partyName : ''),
					'party' => $partyName,
					'vch' => 'RCPT/' . $orderNo,
					'debit' => 0,
					'credit' => $amt,
					'type' => 'payment',
					'order_id' => $orderId,
				);
			}
		}
		return $rows;
	}
}
