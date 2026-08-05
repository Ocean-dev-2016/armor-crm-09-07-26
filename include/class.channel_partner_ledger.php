<?php
/**
 * Channel Partner — Party Ledger / CP Customer Ledger (App API).
 * Same as web channel_partner_ledger.php (Tally style).
 * PHP 5.6 compatible.
 */
require_once dirname(__FILE__) . '/main.class.php';
require_once dirname(__FILE__) . '/function.class.php';

class ChannelPartnerLedger
{
	public $db;

	function __construct($db = null)
	{
		if ($db !== null) {
			$this->db = $db;
		} else {
			$db = new Functions();
			$db->connect();
			$this->db = $db;
		}
	}

	private function validateCp($cpId)
	{
		$cpId = (int) $cpId;
		if ($cpId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'channel_partner_id is required.');
		}
		$where = "id='" . $cpId . "' AND channel_partner_flag=1 AND customer_flag=0 AND isDelete=0";
		if ((int) $this->db->rp_getTotalRecord('executive', $where, 0) <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Invalid Channel Partner.');
		}
		return array('ack' => 1);
	}

	private function loadLedgerBuilder()
	{
		$fn = dirname(__FILE__) . '/../bbsales_tracking/include/channel_partner_ledger_data.php';
		if (!function_exists('cp_build_customer_ledger') && file_exists($fn)) {
			require_once $fn;
		}
	}

	/**
	 * Party filter list for ledger dropdown (All Parties + each customer).
	 */
	private function getParties($cpId)
	{
		$parties = array(
			array(
				'party_id' => 0,
				'company_name' => 'All Parties',
				'display_name' => 'All Parties',
			),
		);
		$pr = $this->db->rp_getData(
			'channel_partner_customer',
			'id,company_name,person_name,mobile_no',
			"channel_partner_id='" . (int) $cpId . "' AND isDelete=0",
			'company_name ASC',
			0
		);
		if ($pr) {
			while ($p = mysqli_fetch_assoc($pr)) {
				$label = $p['company_name'];
				if (!empty($p['person_name'])) {
					$label .= ' / ' . $p['person_name'];
				}
				$parties[] = array(
					'party_id' => (int) $p['id'],
					'company_name' => $p['company_name'],
					'person_name' => isset($p['person_name']) ? $p['person_name'] : '',
					'mobile_no' => isset($p['mobile_no']) ? $p['mobile_no'] : '',
					'display_name' => $label,
				);
			}
		}
		return $parties;
	}

	/**
	 * CP Customer Ledger — same rows as web Party Ledger.
	 *
	 * @param array $detail channel_partner_id, party_id (0=All Parties)
	 */
	public function GetPartyLedger($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$partyId = isset($detail['party_id']) ? (int) $detail['party_id'] : 0;
		if ($partyId < 0) {
			$partyId = 0;
		}

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}

		if ($partyId > 0) {
			$own = (int) $this->db->rp_getTotalRecord(
				'channel_partner_customer',
				"id='" . $partyId . "' AND channel_partner_id='" . $cpId . "' AND isDelete=0",
				0
			);
			if ($own <= 0) {
				return array('ack' => 0, 'ack_msg' => 'Party does not belong to this Channel Partner.');
			}
		}

		$this->loadLedgerBuilder();
		if (!function_exists('cp_build_customer_ledger')) {
			return array('ack' => 0, 'ack_msg' => 'Ledger builder not found. Please deploy channel_partner_ledger_data.php');
		}

		list($ledger, $opening) = cp_build_customer_ledger($this->db, $cpId, $partyId);
		$opening = (float) $opening;

		$company = $this->db->rp_getValue('executive', 'company_name', "id='" . $cpId . "'", 0);
		$partyName = 'All Parties';
		if ($partyId > 0) {
			$partyName = $this->db->rp_getValue('channel_partner_customer', 'company_name', "id='" . $partyId . "'", 0);
			if ($partyName == '') {
				$partyName = 'Party';
			}
		}

		$bal = $opening;
		$totalDebit = 0;
		$totalCredit = 0;
		$result = array();
		$sr = 0;

		foreach ($ledger as $row) {
			$debit = isset($row['debit']) ? (float) $row['debit'] : 0;
			$credit = isset($row['credit']) ? (float) $row['credit'] : 0;
			$bal += $debit - $credit;
			$totalDebit += $debit;
			$totalCredit += $credit;
			$sr++;

			$balAbs = abs($bal);
			$balSide = ($bal >= 0) ? 'Dr' : 'Cr';
			$dateRaw = isset($row['date']) ? $row['date'] : '';
			$dateDisplay = ($dateRaw != '' && $dateRaw != '0000-00-00') ? date('d-m-Y', strtotime($dateRaw)) : '-';

			$result[] = array(
				'sr' => $sr,
				'date' => $dateRaw,
				'date_display' => $dateDisplay,
				'particulars' => isset($row['particular']) ? $row['particular'] : '',
				'party_name' => isset($row['party']) ? $row['party'] : '',
				'voucher' => isset($row['vch']) ? $row['vch'] : '',
				'type' => isset($row['type']) ? $row['type'] : '',
				'order_id' => isset($row['order_id']) ? (int) $row['order_id'] : 0,
				'debit' => round($debit, 2),
				'credit' => round($credit, 2),
				'debit_display' => $debit > 0 ? number_format($debit, 2) : '',
				'credit_display' => $credit > 0 ? number_format($credit, 2) : '',
				'balance' => round($bal, 2),
				'balance_abs' => round($balAbs, 2),
				'balance_side' => $balSide,
				'balance_display' => number_format($balAbs, 2) . ' ' . $balSide,
			);
		}

		$closingAbs = abs($bal);
		$closingSide = ($bal >= 0) ? 'Dr' : 'Cr';

		return array(
			'ack' => 1,
			'ack_msg' => 'Party Ledger ready',
			'channel_partner_id' => $cpId,
			'company_name' => $company ? $company : '',
			'title' => 'CP Customer Ledger',
			'subtitle' => 'Tally / Miracle style',
			'party_id' => $partyId,
			'party_name' => $partyName,
			'parties' => $this->getParties($cpId),
			'opening_balance' => round($opening, 2),
			'opening_balance_display' => number_format(abs($opening), 2) . ($opening >= 0 ? ' Dr' : ' Cr'),
			'total_debit' => round($totalDebit, 2),
			'total_credit' => round($totalCredit, 2),
			'closing_balance' => round($bal, 2),
			'closing_balance_abs' => round($closingAbs, 2),
			'closing_balance_side' => $closingSide,
			'closing_balance_display' => number_format($closingAbs, 2) . ' ' . $closingSide,
			'total' => count($result),
			'result' => $result,
			'receive_payment_api' => array('s' => 259, 'slug' => 'get_cp_payment_parties'),
			'print_url' => 'bbsales_tracking/channel_partner_ledger_print.php?party_id=' . $partyId,
		);
	}
}
