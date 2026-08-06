<?php
/**
 * Channel Partner — Receive Payment (App API, same as web channel_partner_payment.php).
 * PHP 5.6 compatible.
 */
require_once dirname(__FILE__) . '/main.class.php';
require_once dirname(__FILE__) . '/function.class.php';
require_once dirname(__FILE__) . '/class.channel_partner_stock.php';

class ChannelPartnerPayment
{
	public $db;
	private $objStock;

	function __construct($db = null)
	{
		if ($db !== null) {
			$this->db = $db;
		} else {
			$db = new Functions();
			$db->connect();
			$this->db = $db;
		}
		$this->objStock = new ChannelPartnerStock($this->db);
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

	private function assertParty($cpId, $partyId)
	{
		$partyId = (int) $partyId;
		if ($partyId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'party_id is required.');
		}
		$own = (int) $this->db->rp_getTotalRecord(
			'channel_partner_customer',
			"id='" . $partyId . "' AND channel_partner_id='" . (int) $cpId . "' AND isDelete=0",
			0
		);
		if ($own <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Party does not belong to this Channel Partner.');
		}
		return array('ack' => 1);
	}

	public function getPaymentTypes()
	{
		return array(
			array('id' => 1, 'label' => 'By Cash'),
			array('id' => 2, 'label' => 'By Cheque'),
			array('id' => 3, 'label' => 'Online / NEFT / RTGS / UPI'),
			array('id' => 4, 'label' => 'Other'),
		);
	}

	/**
	 * Parties + payment type masters for Receive Payment screen.
	 */
	public function GetPaymentParties($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$search = isset($detail['search_name']) ? trim($detail['search_name']) : '';

		$where = "channel_partner_id='" . $cpId . "' AND isDelete=0";
		if ($search !== '') {
			$s = $this->db->clean($search);
			$where .= " AND (company_name LIKE '%" . $s . "%' OR person_name LIKE '%" . $s . "%' OR mobile_no LIKE '%" . $s . "%')";
		}

		$result = array();
		$r = $this->db->rp_getData(
			'channel_partner_customer',
			'id,company_name,person_name,mobile_no',
			$where,
			'company_name ASC',
			0
		);
		if ($r) {
			while ($p = mysqli_fetch_assoc($r)) {
				$label = $p['company_name'];
				if (!empty($p['person_name'])) {
					$label .= ' / ' . $p['person_name'];
				}
				if (!empty($p['mobile_no'])) {
					$label .= ' (' . $p['mobile_no'] . ')';
				}
				$result[] = array(
					'party_id' => (int) $p['id'],
					'company_name' => $p['company_name'],
					'person_name' => isset($p['person_name']) ? $p['person_name'] : '',
					'mobile_no' => isset($p['mobile_no']) ? $p['mobile_no'] : '',
					'display_name' => $label,
				);
			}
		}

		$company = $this->db->rp_getValue('executive', 'company_name', "id='" . $cpId . "'", 0);

		return array(
			'ack' => 1,
			'ack_msg' => 'Payment parties ready',
			'channel_partner_id' => $cpId,
			'company_name' => $company ? $company : '',
			'title' => 'Receive Payment',
			'subtitle' => 'Receive payment from your customer (Party) against Sales Order',
			'payment_types' => $this->getPaymentTypes(),
			'total' => count($result),
			'result' => $result,
		);
	}

	/**
	 * Orders for selected party — same columns as web Receive Payment table.
	 */
	public function GetPaymentOrders($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$partyId = isset($detail['party_id']) ? (int) $detail['party_id'] : (isset($detail['channel_partner_customer_id']) ? (int) $detail['channel_partner_customer_id'] : 0);

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$partyCheck = $this->assertParty($cpId, $partyId);
		if ($partyCheck['ack'] != 1) {
			return $partyCheck;
		}

		$party_r = $this->db->rp_getData(
			'channel_partner_customer',
			'id,company_name,person_name,mobile_no',
			"id='" . $partyId . "' AND channel_partner_id='" . $cpId . "' AND isDelete=0",
			'',
			0
		);
		$party = $party_r ? mysqli_fetch_assoc($party_r) : array();

		$selectCols = 'id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status';
		$colPayDate = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_date'");
		if ($colPayDate && mysqli_num_rows($colPayDate) > 0) {
			$selectCols .= ',payment_received_date';
		}
		$colPayType = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_type'");
		if ($colPayType && mysqli_num_rows($colPayType) > 0) {
			$selectCols .= ',payment_received_type';
		}

		$where = "customer_id='" . $cpId . "' AND channel_partner_order_flag=1"
			. " AND channel_partner_customer_id='" . $partyId . "' AND isDelete=0 AND status NOT IN (-2,3)";

		$result = array();
		$pendingCount = 0;
		$receivedCount = 0;
		$or = $this->db->rp_getData('orders', $selectCols, $where, 'id DESC', 0);
		if ($or) {
			$typeMap = array();
			foreach ($this->getPaymentTypes() as $t) {
				$typeMap[(int) $t['id']] = $t['label'];
			}
			while ($o = mysqli_fetch_assoc($or)) {
				$paidFlag = isset($o['payment_received_flag']) ? (int) $o['payment_received_flag'] : 0;
				$paidAmt = ($paidFlag === 1) ? (float) $o['payment_received_amount'] : 0;
				$grand = (float) $o['grand_total'];
				$canReceive = ($paidFlag !== 1) ? 1 : 0;
				if ($canReceive) {
					$pendingCount++;
				} else {
					$receivedCount++;
				}
				$typeId = isset($o['payment_received_type']) ? (int) $o['payment_received_type'] : 0;
				$payDate = '';
				$payDateDisplay = '';
				if ($paidFlag === 1 && !empty($o['payment_received_date']) && $o['payment_received_date'] != '0000-00-00 00:00:00') {
					$payDate = $o['payment_received_date'];
					$payDateDisplay = date('d-m-Y', strtotime($o['payment_received_date']));
				}
				$result[] = array(
					'order_id' => (int) $o['id'],
					'order_no' => $o['order_no'],
					'order_date' => $o['order_date'],
					'order_date_display' => ($o['order_date'] != '' && $o['order_date'] != '0000-00-00') ? date('d-m-Y', strtotime($o['order_date'])) : '-',
					'order_amount' => round($grand, 2),
					'order_amount_display' => number_format($grand, 2),
					'payment_received_flag' => $paidFlag,
					'payment_status' => ($paidFlag === 1) ? 'received' : 'pending',
					'payment_status_label' => ($paidFlag === 1)
						? ('Received ' . number_format($paidAmt, 2))
						: 'Pending',
					'payment_received_amount' => round($paidAmt, 2),
					'payment_received_date' => $payDate,
					'payment_received_date_display' => $payDateDisplay,
					'payment_type' => $typeId,
					'payment_type_label' => isset($typeMap[$typeId]) ? $typeMap[$typeId] : '',
					'can_receive' => $canReceive,
					'suggested_amount' => round($grand, 2),
				);
			}
		}

		$partyLabel = !empty($party['company_name']) ? $party['company_name'] : '';
		if (!empty($party['person_name'])) {
			$partyLabel .= ' / ' . $party['person_name'];
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Party orders ready',
			'party_id' => $partyId,
			'party_name' => !empty($party['company_name']) ? $party['company_name'] : '',
			'person_name' => !empty($party['person_name']) ? $party['person_name'] : '',
			'mobile_no' => !empty($party['mobile_no']) ? $party['mobile_no'] : '',
			'display_name' => $partyLabel,
			'payment_types' => $this->getPaymentTypes(),
			'pending_count' => $pendingCount,
			'received_count' => $receivedCount,
			'total' => count($result),
			'result' => $result,
		);
	}

	/**
	 * Save payment received against order — same as order_payment_received_ajax.php.
	 */
	public function SaveReceivePayment($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$orderId = isset($detail['order_id']) ? (int) $detail['order_id'] : 0;
		$paidAmount = isset($detail['paid_amount']) ? (float) $detail['paid_amount'] : 0;
		$paymentType = isset($detail['payment_type']) ? (int) $detail['payment_type'] : 0;
		$remark = isset($detail['remark']) ? trim($detail['remark']) : '';

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		if ($orderId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'order_id is required.');
		}
		if ($paidAmount <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Please enter Payment Received Amount.');
		}
		if (!in_array($paymentType, array(1, 2, 3, 4), true)) {
			return array('ack' => 0, 'ack_msg' => 'Please select Payment Type.');
		}

		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_flag'");
		if (!$colCheck || mysqli_num_rows($colCheck) === 0) {
			return array('ack' => 0, 'ack_msg' => 'Please run db_sync to add payment_received columns.');
		}

		$order = $this->db->rp_getData(
			'orders',
			'id,order_no,customer_id,customer_type,sales_id,grand_total,payment_received_flag,isDelete,status,channel_partner_order_flag,channel_partner_customer_id',
			"id='" . $orderId . "' AND isDelete=0",
			'',
			0
		);
		if (!$order) {
			return array('ack' => 0, 'ack_msg' => 'Order not found.');
		}
		$row = mysqli_fetch_assoc($order);

		if ((int) $row['customer_id'] !== $cpId || (int) $row['channel_partner_order_flag'] !== 1) {
			return array('ack' => 0, 'ack_msg' => 'Not your Channel Partner order.');
		}
		if ((int) $row['status'] === -2 || (int) $row['status'] === 3) {
			return array('ack' => 0, 'ack_msg' => 'Cannot receive payment for cancelled/rejected order.');
		}
		if ((int) $row['payment_received_flag'] === 1) {
			return array(
				'ack' => 1,
				'ack_msg' => 'Payment already marked as received for ' . $row['order_no'],
				'already' => 1,
				'order_id' => $orderId,
				'order_no' => $row['order_no'],
			);
		}

		$now = date('Y-m-d H:i:s');
		$payDate = date('Y-m-d');
		$orderNo = $row['order_no'];

		$updRows = array(
			'payment_received_flag' => 1,
			'payment_received_date' => $now,
			'payment_received_by' => $cpId,
		);
		$amtCol = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_amount'");
		if ($amtCol && mysqli_num_rows($amtCol) > 0) {
			$updRows['payment_received_amount'] = $paidAmount;
		}
		$typeCol = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'payment_received_type'");
		if ($typeCol && mysqli_num_rows($typeCol) > 0) {
			$updRows['payment_received_type'] = $paymentType;
		}

		$upd = $this->db->rp_update('orders', $updRows, "id='" . $orderId . "' AND isDelete=0", 0);
		if (!$upd) {
			return array('ack' => 0, 'ack_msg' => 'Failed to update order.');
		}

		$paymentTypes = array(1 => 'By Cash', 2 => 'By Cheque', 3 => 'Online', 4 => 'Other');
		$payRemark = $remark != '' ? $remark : ('Payment Received against Order ' . $orderNo);
		$payInsertOk = false;
		$payTable = @mysqli_query($this->db->myconn, "SHOW TABLES LIKE 'payment'");
		if ($payTable && mysqli_num_rows($payTable) > 0) {
			require_once dirname(__FILE__) . '/class.payment.php';
			$paymentObj = new Payment();
			$payDetail = array(
				'customer_type' => isset($row['customer_type']) ? $row['customer_type'] : '',
				'customer_id' => (int) $row['customer_id'],
				'sales_executive_id' => (int) $row['sales_id'],
				'paid_amount' => $paidAmount,
				'payment_date' => $payDate,
				'payment_type' => $paymentType,
				'remark' => $payRemark,
				'cheque_no' => '',
				'receipt_type' => 2,
				'invoice_id' => $orderId,
			);
			$payRes = $paymentObj->InsertPayment($payDetail);
			$payInsertOk = (isset($payRes['ack']) && (int) $payRes['ack'] === 1);
		}

		$typeLabel = isset($paymentTypes[$paymentType]) ? $paymentTypes[$paymentType] : '';
		$stockMsg = '';
		$endCustId = isset($row['channel_partner_customer_id']) ? (int) $row['channel_partner_customer_id'] : 0;
		if ($endCustId > 0) {
			$debitRes = $this->objStock->debitForCustomerOrder($orderId);
			if (!empty($debitRes['ack'])) {
				if (empty($debitRes['already'])) {
					$stockMsg = ' Stock outward posted.';
				}
			} else if (!empty($debitRes['ack_msg'])) {
				$stockMsg = ' (Stock: ' . $debitRes['ack_msg'] . ')';
			}
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'Payment Received saved for Order ' . $orderNo . ' — Amount: ' . number_format($paidAmount, 2) . ($typeLabel != '' ? ' (' . $typeLabel . ')' : '') . $stockMsg,
			'order_id' => $orderId,
			'order_no' => $orderNo,
			'party_id' => $endCustId,
			'paid_amount' => round($paidAmount, 2),
			'payment_type' => $paymentType,
			'payment_type_label' => $typeLabel,
			'payment_received_date' => $now,
			'payment_saved' => $payInsertOk ? 1 : 0,
		);
	}

	/**
	 * Generate Payment Receive Statement PDF for selected party (same as web Print / Share PDF).
	 * Returns file_url for app download / open.
	 */
	public function GetPaymentPdf($detail)
	{
		$cpId = isset($detail['channel_partner_id']) ? (int) $detail['channel_partner_id'] : 0;
		$partyId = isset($detail['party_id']) ? (int) $detail['party_id'] : (isset($detail['channel_partner_customer_id']) ? (int) $detail['channel_partner_customer_id'] : 0);

		$cpCheck = $this->validateCp($cpId);
		if ($cpCheck['ack'] != 1) {
			return $cpCheck;
		}
		$partyCheck = $this->assertParty($cpId, $partyId);
		if ($partyCheck['ack'] != 1) {
			return $partyCheck;
		}

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

		$pr = $this->db->rp_getData(
			'channel_partner_customer',
			'id,company_name,person_name,mobile_no,address,city,state,pincode,gst',
			"id='" . $partyId . "' AND channel_partner_id='" . $cpId . "' AND isDelete=0",
			'',
			0
		);
		$partyRow = $pr ? mysqli_fetch_assoc($pr) : null;
		if (!$partyRow) {
			return array('ack' => 0, 'ack_msg' => 'Invalid Party.');
		}
		$partyLabel = $partyRow['company_name'];

		$orders = array();
		$or = $this->db->rp_getData(
			'orders',
			'id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,status',
			"customer_id='" . $cpId . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id='" . $partyId . "' AND isDelete=0 AND status NOT IN (-2,3)",
			'order_date ASC, id ASC',
			0
		);
		if ($or) {
			while ($o = mysqli_fetch_assoc($or)) {
				$orders[] = $o;
			}
		}

		$totalOrder = 0;
		$totalPaid = 0;
		$totalPending = 0;
		$rowsHtml = '';
		$sr = 0;
		foreach ($orders as $o) {
			$sr++;
			$paidFlag = isset($o['payment_received_flag']) ? (int) $o['payment_received_flag'] : 0;
			$orderAmt = (float) $o['grand_total'];
			$paidAmt = ($paidFlag === 1) ? (float) $o['payment_received_amount'] : 0;
			$totalOrder += $orderAmt;
			$totalPaid += $paidAmt;
			$totalPending += ($paidFlag === 1) ? 0 : $orderAmt;
			$statusTxt = ($paidFlag === 1) ? 'RECEIVED' : 'PENDING';
			$statusCls = ($paidFlag === 1) ? 'ok' : 'pend';
			$rowsHtml .= '<tr><td class="tc">' . $sr . '</td><td><b>' . htmlspecialchars($o['order_no']) . '</b></td>'
				. '<td class="tc">' . date('d-m-Y', strtotime($o['order_date'])) . '</td>'
				. '<td class="tr">' . number_format($orderAmt, 2) . '</td>'
				. '<td class="tc ' . $statusCls . '">' . $statusTxt . '</td>'
				. '<td class="tr">' . number_format($paidAmt, 2) . '</td></tr>';
		}
		if ($rowsHtml == '') {
			$rowsHtml = '<tr><td colspan="6" class="tc">No orders found.</td></tr>';
		}

		$parts = array();
		if (!empty($partyRow['address'])) {
			$parts[] = $partyRow['address'];
		}
		if (!empty($partyRow['city'])) {
			$parts[] = $partyRow['city'];
		}
		if (!empty($partyRow['state'])) {
			$parts[] = $partyRow['state'];
		}
		if (!empty($partyRow['pincode'])) {
			$parts[] = $partyRow['pincode'];
		}
		$partyAddr = implode(', ', $parts);
		$partyMobile = isset($partyRow['mobile_no']) ? $partyRow['mobile_no'] : '';
		$partyGst = isset($partyRow['gst']) ? $partyRow['gst'] : '';

		$css = 'table{width:100%;border-collapse:collapse;font-size:11px;font-family:dejavusans,Arial,sans-serif;}'
			. 'td,th{border:1px solid #595959;padding:5px 6px;vertical-align:top;}'
			. '.th{background:#1a6b8a;color:#fff;text-align:center;}'
			. '.title{background:#A9A9A9;text-align:center;font-weight:bold;font-size:14px;}'
			. '.tr{text-align:right;}.tc{text-align:center;}'
			. '.ok{color:#1e7e34;font-weight:bold;}.pend{color:#c0392b;font-weight:bold;}'
			. '.gray{background:#f0f0f0;}';

		$html = '<html><head><style>' . $css . '</style></head><body>';
		$html .= '<table><tr><td colspan="6" class="title">PAYMENT RECEIVE STATEMENT</td></tr><tr>'
			. '<td colspan="3"><b>Party / Buyer</b><br><b>' . htmlspecialchars($partyLabel) . '</b><br>'
			. htmlspecialchars($partyAddr)
			. ($partyMobile != '' ? '<br>Mobile: ' . htmlspecialchars($partyMobile) : '')
			. ($partyGst != '' ? '<br>GSTIN: ' . htmlspecialchars($partyGst) : '') . '</td>'
			. '<td colspan="3"><b>From:</b> ' . htmlspecialchars($pi_company) . '<br>'
			. ($pi_gst != '' ? '<b>GSTIN:</b> ' . htmlspecialchars($pi_gst) . '<br>' : '')
			. '<b>Print Date:</b> ' . date('d-M-Y') . '</td></tr></table>';
		$html .= '<table><tr class="th"><th>Sr</th><th>Order No</th><th>Date</th><th>Order Amount</th><th>Status</th><th>Received</th></tr>'
			. $rowsHtml
			. '<tr class="gray"><td colspan="3" class="tr"><b>Total Order</b></td><td class="tr"><b>' . number_format($totalOrder, 2) . '</b></td>'
			. '<td class="tr"><b>Total Received</b></td><td class="tr"><b>' . number_format($totalPaid, 2) . '</b></td></tr>'
			. '<tr class="gray"><td colspan="5" class="tr"><b>Pending</b></td><td class="tr pend"><b>' . number_format($totalPending, 2) . '</b></td></tr></table>';
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
		$oldFiles = @glob($saveDir . 'CP_Payment_*.pdf');
		if ($oldFiles) {
			foreach ($oldFiles as $old) {
				if (is_file($old) && ($now - @filemtime($old)) > 86400) {
					@unlink($old);
				}
			}
		}

		$fileBase = 'CP_Payment_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $partyLabel) . '_' . date('Ymd_His');
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
			$fileUrl = '../bbsales_tracking/inquiry_documents/' . $fileName;
		}

		return array(
			'ack' => 1,
			'ack_msg' => 'PDF ready',
			'party_id' => $partyId,
			'party_name' => $partyLabel,
			'file_url' => $fileUrl,
			'file_name' => $fileName,
			'title' => 'Payment Receive Statement - ' . $partyLabel,
			'total_order' => round($totalOrder, 2),
			'total_received' => round($totalPaid, 2),
			'total_pending' => round($totalPending, 2),
			'order_count' => count($orders),
			'pdf_ok' => 1,
		);
	}
}
