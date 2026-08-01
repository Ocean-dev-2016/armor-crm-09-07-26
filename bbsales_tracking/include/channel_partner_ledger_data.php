<?php
/**
 * Shared ledger rows builder for CP Customer Ledger (screen / print / excel).
 * Expects $db. Returns array($ledger, $opening).
 */
function cp_build_customer_ledger($db, $cpFilter, $partyFilter)
{
	$ledger = array();
	$opening = 0;
	$cpFilter = (int) $cpFilter;
	$partyFilter = (int) $partyFilter;
	if ($cpFilter <= 0) {
		return array($ledger, $opening);
	}

	$w = "customer_id='" . $cpFilter . "' AND channel_partner_order_flag=1 AND cp_order_mode='customer' AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3)";
	if ($partyFilter > 0) {
		$w .= " AND channel_partner_customer_id='" . $partyFilter . "'";
	}

	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,payment_received_date,channel_partner_customer_id,payment_received_type",
		$w,
		"order_date ASC, id ASC",
		0
	);
	if ($or) {
		while ($o = mysqli_fetch_assoc($or)) {
			$partyName = $db->rp_getValue("channel_partner_customer", "company_name", "id='" . (int) $o['channel_partner_customer_id'] . "'", 0);
			$ledger[] = array(
				'date' => $o['order_date'],
				'sort' => strtotime($o['order_date']) . '1' . str_pad($o['id'], 8, '0', STR_PAD_LEFT),
				'particular' => 'Sales Order ' . $o['order_no'] . ' — ' . $partyName,
				'party' => $partyName,
				'vch' => $o['order_no'],
				'debit' => (float) $o['grand_total'],
				'credit' => 0,
				'type' => 'order',
				'order_id' => (int) $o['id'],
			);
			if ((int) $o['payment_received_flag'] === 1 && (float) $o['payment_received_amount'] > 0) {
				$pdate = (!empty($o['payment_received_date']) && $o['payment_received_date'] != '0000-00-00 00:00:00')
					? date('Y-m-d', strtotime($o['payment_received_date']))
					: $o['order_date'];
				$ptypeLabels = array(1 => 'Cash', 2 => 'Cheque', 3 => 'Online', 4 => 'Other');
				$pt = isset($ptypeLabels[$o['payment_received_type']]) ? $ptypeLabels[$o['payment_received_type']] : 'Payment';
				$ledger[] = array(
					'date' => $pdate,
					'sort' => strtotime($pdate) . '2' . str_pad($o['id'], 8, '0', STR_PAD_LEFT),
					'particular' => 'Payment Received (' . $pt . ') against ' . $o['order_no'] . ' — ' . $partyName,
					'party' => $partyName,
					'vch' => 'RCPT/' . $o['order_no'],
					'debit' => 0,
					'credit' => (float) $o['payment_received_amount'],
					'type' => 'payment',
					'order_id' => (int) $o['id'],
				);
			}
		}
	}
	usort($ledger, function ($a, $b) {
		if ($a['sort'] == $b['sort']) {
			return 0;
		}
		return ($a['sort'] < $b['sort']) ? -1 : 1;
	});

	return array($ledger, $opening);
}
