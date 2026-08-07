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

		/* Absolute web print page (fallback) + generated PDF file_url like #265 */
		$printPageUrl = $this->buildAbsoluteUrl(
			'bbsales_tracking/channel_partner_ledger_print.php?party_id=' . $partyId
			. '&cp_id=' . $cpId
		);
		$pdfRes = $this->generateLedgerPdf($cpId, $partyId, $partyName, $ledger, $opening, $totalDebit, $totalCredit, $bal);

		$out = array(
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
			'print_url' => $printPageUrl,
		);

		if (!empty($pdfRes['ack']) && !empty($pdfRes['file_url'])) {
			$out['file_url'] = $pdfRes['file_url'];
			$out['file_name'] = isset($pdfRes['file_name']) ? $pdfRes['file_name'] : '';
			$out['pdf_ok'] = 1;
			/* App that used print_url — point to full PDF like #265 */
			$out['print_url'] = $pdfRes['file_url'];
		} else {
			$out['file_url'] = $printPageUrl;
			$out['file_name'] = '';
			$out['pdf_ok'] = 0;
			$out['pdf_msg'] = isset($pdfRes['ack_msg']) ? $pdfRes['ack_msg'] : 'PDF not generated';
		}

		return $out;
	}

	/**
	 * Build absolute URL (same host style as #265 file_url).
	 */
	private function buildAbsoluteUrl($relativePath)
	{
		$relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
		if (defined('INQUIRY_REPORT_FILES1') && strpos($relativePath, 'inquiry_documents/') === 0) {
			$fileOnly = substr($relativePath, strlen('inquiry_documents/'));
			return rtrim(INQUIRY_REPORT_FILES1, '/') . '/' . $fileOnly;
		}
		if (defined('ADMINSITEURL')) {
			/* ADMINSITEURL usually ends at /bbsales_tracking */
			$base = rtrim(ADMINSITEURL, '/');
			if (strpos($relativePath, 'bbsales_tracking/') === 0) {
				$relativePath = substr($relativePath, strlen('bbsales_tracking/'));
			}
			return $base . '/' . $relativePath;
		}
		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
		return $scheme . '://' . $host . '/' . $relativePath;
	}

	/**
	 * Generate Party Ledger PDF (same as web channel_partner_share_pdf_ajax.php type=ledger).
	 * Returns file_url full absolute path like #265 GetPaymentPdf.
	 */
	private function generateLedgerPdf($cpId, $partyId, $partyLabel, $ledger, $opening, $totalDebit, $totalCredit, $closingBal)
	{
		$cpId = (int) $cpId;
		$partyId = (int) $partyId;
		$partyLabel = ($partyLabel != '') ? $partyLabel : 'All Parties';

		$cp_r = $this->db->rp_getData(
			'executive',
			'company_name,cp_print_company_name,cp_print_gst,gst',
			"id='" . $cpId . "' AND isDelete=0",
			'',
			0
		);
		$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
		$cp_name = isset($cp['company_name']) ? $cp['company_name'] : '';
		$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : $cp_name;
		$pi_gst = !empty($cp['cp_print_gst']) ? $cp['cp_print_gst'] : (isset($cp['gst']) ? $cp['gst'] : '');

		$css = 'table{width:100%;border-collapse:collapse;font-size:11px;font-family:dejavusans,Arial,sans-serif;}'
			. 'td,th{border:1px solid #595959;padding:5px 6px;vertical-align:top;}'
			. '.th{background:#1a6b8a;color:#fff;text-align:center;}'
			. '.title{background:#A9A9A9;text-align:center;font-weight:bold;font-size:14px;}'
			. '.tr{text-align:right;}.tc{text-align:center;}'
			. '.gray{background:#f0f0f0;}';

		$bal = (float) $opening;
		$rowsHtml = '';
		if ((float) $opening != 0) {
			$obLbl = number_format(abs($opening), 2) . ((float) $opening >= 0 ? ' Dr' : ' Cr');
			$rowsHtml .= '<tr><td class="tc"></td><td><b>Opening Balance</b></td><td class="tc">—</td>'
				. '<td class="tr">' . ((float) $opening > 0 ? number_format((float) $opening, 2) : '') . '</td>'
				. '<td class="tr">' . ((float) $opening < 0 ? number_format(abs((float) $opening), 2) : '') . '</td>'
				. '<td class="tr">' . $obLbl . '</td></tr>';
		}
		foreach ($ledger as $row) {
			$debit = isset($row['debit']) ? (float) $row['debit'] : 0;
			$credit = isset($row['credit']) ? (float) $row['credit'] : 0;
			$bal += $debit - $credit;
			$balLbl = number_format(abs($bal), 2) . ($bal >= 0 ? ' Dr' : ' Cr');
			$dateRaw = isset($row['date']) ? $row['date'] : '';
			$dateDisplay = ($dateRaw != '' && $dateRaw != '0000-00-00') ? date('d-m-Y', strtotime($dateRaw)) : '-';
			$particular = isset($row['particular']) ? $row['particular'] : '';
			$vch = isset($row['vch']) ? $row['vch'] : '';
			$rowsHtml .= '<tr><td class="tc">' . $dateDisplay . '</td>'
				. '<td>' . htmlspecialchars($particular) . '</td>'
				. '<td class="tc">' . htmlspecialchars($vch) . '</td>'
				. '<td class="tr">' . ($debit > 0 ? number_format($debit, 2) : '') . '</td>'
				. '<td class="tr">' . ($credit > 0 ? number_format($credit, 2) : '') . '</td>'
				. '<td class="tr">' . $balLbl . '</td></tr>';
		}
		if ($rowsHtml == '') {
			$rowsHtml = '<tr><td colspan="6" class="tc">No ledger entries.</td></tr>';
		}
		$closingAbs = abs((float) $closingBal);
		$closingSide = ((float) $closingBal >= 0) ? 'Dr' : 'Cr';

		$html = '<html><head><style>' . $css . '</style></head><body>';
		$html .= '<table><tr><td colspan="6" class="title">PARTY LEDGER</td></tr><tr>'
			. '<td colspan="3"><b>Party</b><br><b>' . htmlspecialchars($partyLabel) . '</b></td>'
			. '<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
			. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
			. '<b>Print Date:</b> ' . date('d-M-Y') . '</td></tr></table>';
		$html .= '<table><tr class="th"><th>Date</th><th>Particulars</th><th>Voucher</th><th>Debit</th><th>Credit</th><th>Balance</th></tr>'
			. $rowsHtml
			. '<tr class="gray"><td colspan="3" class="tr"><b>Total</b></td><td class="tr"><b>' . number_format((float) $totalDebit, 2) . '</b></td>'
			. '<td class="tr"><b>' . number_format((float) $totalCredit, 2) . '</b></td><td></td></tr>'
			. '<tr class="gray"><td colspan="5" class="tr"><b>Closing Balance</b></td><td class="tr"><b>'
			. number_format($closingAbs, 2) . ' ' . $closingSide . '</b></td></tr></table>';
		$html .= '</body></html>';

		$bbsDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bbsales_tracking' . DIRECTORY_SEPARATOR;
		$saveDir = $bbsDir . 'inquiry_documents' . DIRECTORY_SEPARATOR;
		if (!is_dir($saveDir)) {
			@mkdir($saveDir, 0777, true);
		}
		if (!is_dir($saveDir) || !is_writable($saveDir)) {
			return array('ack' => 0, 'ack_msg' => 'inquiry_documents folder missing or not writable.');
		}

		$now = time();
		$oldFiles = @glob($saveDir . 'CP_Ledger_*.pdf');
		if ($oldFiles) {
			foreach ($oldFiles as $old) {
				if (is_file($old) && ($now - @filemtime($old)) > 86400) {
					@unlink($old);
				}
			}
		}

		$fileBase = 'CP_Ledger_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
		$fileName = $fileBase . '_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.pdf';
		$savePath = $saveDir . $fileName;

		$mpdfFile = $bbsDir . 'mpdf60' . DIRECTORY_SEPARATOR . 'mpdf.php';
		if (!file_exists($mpdfFile)) {
			return array('ack' => 0, 'ack_msg' => 'mPDF library missing (mpdf60).');
		}

		$polyfill = $bbsDir . 'include' . DIRECTORY_SEPARATOR . 'mbstring_polyfill.php';
		if (file_exists($polyfill)) {
			include_once $polyfill;
		}

		require_once $mpdfFile;
		$mpdf = new mPDF('', 'A4', 10, 'sans-serif', 8, 8, 8, 8, 0, 0, 'P');
		$mpdf->WriteHTML($html);
		$mpdf->Output($savePath, 'F');

		if (!file_exists($savePath) || @filesize($savePath) < 50) {
			return array('ack' => 0, 'ack_msg' => 'PDF file was not created. Check folder permissions.');
		}

		$fileUrl = '';
		if (defined('INQUIRY_REPORT_FILES1')) {
			$fileUrl = rtrim(INQUIRY_REPORT_FILES1, '/') . '/' . $fileName;
		} else if (defined('ADMINSITEURL')) {
			$fileUrl = rtrim(ADMINSITEURL, '/') . '/inquiry_documents/' . $fileName;
		} else {
			$fileUrl = $this->buildAbsoluteUrl('bbsales_tracking/inquiry_documents/' . $fileName);
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'PDF ready',
			'file_url' => $fileUrl,
			'file_name' => $fileName,
			'pdf_ok' => 1,
		);
	}
}
