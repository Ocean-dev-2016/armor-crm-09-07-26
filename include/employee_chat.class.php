<?php
/**
 * Employee-to-employee chat helpers (PHP 5.6 safe)
 */
require_once dirname(__FILE__) . '/main.class.php';
require_once dirname(__FILE__) . '/function.class.php';

class EmployeeChat
{
	public $db;
	public $threadTable = 'employee_chat_thread';
	public $messageTable = 'employee_chat_message';

	function __construct()
	{
		$db = new Functions();
		$db->connect();
		$this->db = $db;
	}

	public function currentUserId()
	{
		return isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] : 0;
	}

	public function normalizePair($a, $b)
	{
		$a = (int) $a;
		$b = (int) $b;
		if ($a <= 0 || $b <= 0 || $a == $b) {
			return false;
		}
		if ($a < $b) {
			return array($a, $b);
		}
		return array($b, $a);
	}

	public function getOrCreateThread($userA, $userB)
	{
		$pair = $this->normalizePair($userA, $userB);
		if ($pair === false) {
			return array('ack' => 0, 'ack_msg' => 'Invalid users');
		}
		$userOne = $pair[0];
		$userTwo = $pair[1];
		$where = "user_one_id='{$userOne}' AND user_two_id='{$userTwo}' AND isDelete=0";
		$existing = $this->db->rp_getValue($this->threadTable, 'id', $where, 0);
		if ($existing) {
			return array('ack' => 1, 'thread_id' => (int) $existing);
		}
		$now = date('Y-m-d H:i:s');
		$rows = array('user_one_id', 'user_two_id', 'last_message_id', 'last_message_date', 'created_date', 'modified_date', 'isActive', 'isDelete');
		$values = array($userOne, $userTwo, 0, $now, $now, $now, 1, 0);
		$ins = $this->db->rp_insert($this->threadTable, $values, $rows, 0);
		if ($ins) {
			return array('ack' => 1, 'thread_id' => (int) $ins);
		}
		return array('ack' => 0, 'ack_msg' => 'Could not create conversation');
	}

	public function userCanAccessThread($threadId, $userId)
	{
		$threadId = (int) $threadId;
		$userId = (int) $userId;
		$where = "id='{$threadId}' AND isDelete=0 AND (user_one_id='{$userId}' OR user_two_id='{$userId}')";
		$id = $this->db->rp_getValue($this->threadTable, 'id', $where, 0);
		return $id ? true : false;
	}

	public function getPeerId($threadId, $userId)
	{
		$threadId = (int) $threadId;
		$userId = (int) $userId;
		$row = $this->db->rp_getData($this->threadTable, 'user_one_id,user_two_id', "id='{$threadId}' AND isDelete=0", '', 0);
		if (!$row) {
			return 0;
		}
		$d = mysqli_fetch_assoc($row);
		if ((int) $d['user_one_id'] === $userId) {
			return (int) $d['user_two_id'];
		}
		return (int) $d['user_one_id'];
	}

	public function getUserDisplayName($userId)
	{
		$userId = (int) $userId;
		$seId = (int) $this->db->rp_getValue(CTABLE_ADMIN, 'sales_executive_id', "id='{$userId}' AND isDelete=0", 0);
		if ($seId > 0) {
			$seName = $this->db->rp_getValue('sales_executive', 'name', "id='{$seId}' AND isDelete=0", 0);
			if ($seName != '') {
				return $seName;
			}
		}
		$name = $this->db->rp_getValue(CTABLE_ADMIN, 'name', "id='{$userId}' AND isDelete=0", 0);
		if ($name != '') {
			return $name;
		}
		return 'User #' . $userId;
	}

	/**
	 * Ensure sales person has a System User row so chat thread can use login id.
	 */
	public function ensureLoginForSalesExecutive($seId)
	{
		$seId = (int) $seId;
		if ($seId <= 0) {
			return 0;
		}
		$existing = $this->loginIdFromSalesExecutive($seId);
		if ($existing > 0) {
			return $existing;
		}
		$seR = $this->db->rp_getData('sales_executive', 'id,name,username,phone,email', "id='{$seId}' AND isDelete=0", '', 0);
		if (!$seR) {
			return 0;
		}
		$se = mysqli_fetch_assoc($seR);
		$uname = 'se_chat_' . $seId;
		if (!empty($se['phone'])) {
			$uname = preg_replace('/[^0-9A-Za-z_@.\-]/', '', $se['phone']);
		} else if (!empty($se['username'])) {
			$uname = preg_replace('/[^0-9A-Za-z_@.\-]/', '', $se['username']);
		}
		if ($uname === '') {
			$uname = 'se_chat_' . $seId;
		}
		$unameEsc = mysqli_real_escape_string($this->db->myconn, $uname);
		// unique username if taken
		$dup = $this->db->rp_getValue(CTABLE_ADMIN, 'id', "username='{$unameEsc}' AND isDelete=0", 0);
		if ($dup) {
			$uname = 'se_chat_' . $seId;
			$unameEsc = mysqli_real_escape_string($this->db->myconn, $uname);
		}
		$now = date('Y-m-d H:i:s');
		$name = !empty($se['name']) ? $se['name'] : ('SE #' . $seId);
		$nameEsc = mysqli_real_escape_string($this->db->myconn, $name);
		$rows = array('username', 'password', 'name', 'admin_type', 'type', 'sales_executive_id', 'customer_id', 'isDelete');
		$values = array($unameEsc, md5('Chat@' . $seId), $nameEsc, 29, 2, $seId, 0, 0);
		// optional created_date
		$tableCols = method_exists($this->db, 'rp_getTableColumnNames') ? $this->db->rp_getTableColumnNames(CTABLE_ADMIN) : array();
		if (is_array($tableCols) && in_array('created_date', $tableCols)) {
			$rows[] = 'created_date';
			$values[] = $now;
		}
		$ins = $this->db->rp_insert(CTABLE_ADMIN, $values, $rows, 0);
		return $ins ? (int) $ins : 0;
	}

	/**
	 * All active Sales Persons (+ admin) for Employees tab
	 */
	public function listUsers($meId, $search)
	{
		$meId = (int) $meId;
		$meSeId = (int) $this->db->rp_getValue(CTABLE_ADMIN, 'sales_executive_id', "id='{$meId}' AND isDelete=0", 0);
		$list = array();
		$seenSe = array();

		$where = "isDelete=0 AND isActive=1";
		if ($meSeId > 0) {
			$where .= " AND id!='{$meSeId}'";
		}
		if ($search != '') {
			$s = $this->db->clean($search);
			$where .= " AND (name LIKE '%{$s}%' OR phone LIKE '%{$s}%' OR username LIKE '%{$s}%')";
		}
		$res = $this->db->rp_getData('sales_executive', 'id,name,phone,username,type', $where, 'name ASC', 0);
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$seId = (int) $row['id'];
				$seenSe[$seId] = 1;
				$loginId = $this->loginIdFromSalesExecutive($seId);
				$typeLabel = $row['type'] != '' ? $row['type'] : 'sales_person';
				$list[] = array(
					'id' => $loginId > 0 ? $loginId : 0,
					'sales_executive_id' => $seId,
					'peer_se_id' => $seId,
					'name' => $row['name'],
					'username' => !empty($row['phone']) ? $row['phone'] : $row['username'],
					'role' => $typeLabel,
					'has_login' => $loginId > 0 ? 1 : 0,
				);
			}
		}

		// Also include admin / system users without SE (e.g. Super Admin) so they appear in list for SEs
		$where2 = "isDelete=0 AND type IN (0,2) AND id!='{$meId}' AND (sales_executive_id IS NULL OR sales_executive_id=0)";
		if ($search != '') {
			$s = $this->db->clean($search);
			$where2 .= " AND (name LIKE '%{$s}%' OR username LIKE '%{$s}%')";
		}
		$res2 = $this->db->rp_getData(CTABLE_ADMIN, 'id,name,username,admin_type', $where2, 'name ASC', 0);
		if ($res2) {
			while ($row = mysqli_fetch_assoc($res2)) {
				$role = $this->db->rp_getValue('admin_type', 'name', "id='" . (int) $row['admin_type'] . "'", 0);
				$list[] = array(
					'id' => (int) $row['id'],
					'sales_executive_id' => 0,
					'peer_se_id' => 0,
					'name' => $row['name'],
					'username' => $row['username'],
					'role' => $role != '' ? $role : 'System User',
					'has_login' => 1,
				);
			}
		}

		// sort by name
		usort($list, array($this, '_sortByName'));
		return $list;
	}

	public function _sortByName($a, $b)
	{
		return strcasecmp($a['name'], $b['name']);
	}

	public function openChatWithPeer($meId, $peerLoginId, $peerSeId)
	{
		$meId = (int) $meId;
		$peerLoginId = (int) $peerLoginId;
		$peerSeId = (int) $peerSeId;
		if ($peerLoginId <= 0 && $peerSeId > 0) {
			$peerLoginId = $this->ensureLoginForSalesExecutive($peerSeId);
		}
		if ($peerLoginId <= 0) {
			return array('ack' => 0, 'ack_msg' => 'Employee login could not be prepared for chat');
		}
		if ($peerLoginId === $meId) {
			return array('ack' => 0, 'ack_msg' => 'Cannot chat with yourself');
		}
		$res = $this->getOrCreateThread($meId, $peerLoginId);
		if ($res['ack'] != 1) {
			return $res;
		}
		$msgs = $this->getMessages($res['thread_id'], $meId, 0);
		return array(
			'ack' => 1,
			'thread_id' => $res['thread_id'],
			'peer_id' => $msgs['peer_id'],
			'peer_name' => $msgs['peer_name'],
			'messages' => $msgs['messages'],
		);
	}

	public function listThreads($meId)
	{
		$meId = (int) $meId;
		$where = "isDelete=0 AND (user_one_id='{$meId}' OR user_two_id='{$meId}')";
		$res = $this->db->rp_getData($this->threadTable, '*', $where, 'last_message_date DESC', 0);
		$list = array();
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$peerId = ((int) $row['user_one_id'] === $meId) ? (int) $row['user_two_id'] : (int) $row['user_one_id'];
				$lastText = '';
				if ((int) $row['last_message_id'] > 0) {
					$lastText = $this->db->rp_getValue($this->messageTable, 'message_text', "id='" . (int) $row['last_message_id'] . "' AND isDelete=0", 0);
				}
				$unread = (int) $this->db->rp_getTotalRecord($this->messageTable, "thread_id='" . (int) $row['id'] . "' AND sender_id!='{$meId}' AND is_read=0 AND isDelete=0", 0);
				$list[] = array(
					'thread_id' => (int) $row['id'],
					'peer_id' => $peerId,
					'peer_name' => $this->getUserDisplayName($peerId),
					'last_message' => $lastText,
					'last_message_date' => $row['last_message_date'],
					'unread' => $unread,
				);
			}
		}
		return $list;
	}

	public function getMessages($threadId, $meId, $afterId)
	{
		$threadId = (int) $threadId;
		$meId = (int) $meId;
		$afterId = (int) $afterId;
		if (!$this->userCanAccessThread($threadId, $meId)) {
			return array('ack' => 0, 'ack_msg' => 'Access denied', 'messages' => array());
		}
		$where = "thread_id='{$threadId}' AND isDelete=0";
		if ($afterId > 0) {
			$where .= " AND id>'{$afterId}'";
		}
		$res = $this->db->rp_getData($this->messageTable, '*', $where, 'id ASC', 0);
		$messages = array();
		if ($res) {
			while ($row = mysqli_fetch_assoc($res)) {
				$messages[] = array(
					'id' => (int) $row['id'],
					'sender_id' => (int) $row['sender_id'],
					'is_mine' => ((int) $row['sender_id'] === $meId) ? 1 : 0,
					'message_text' => $row['message_text'],
					'created_date' => $row['created_date'],
					'is_read' => (int) $row['is_read'],
				);
			}
		}
		// mark peer messages as read
		$nowRead = date('Y-m-d H:i:s');
		@mysqli_query(
			$this->db->myconn,
			"UPDATE `{$this->messageTable}` SET is_read=1, read_date='{$nowRead}'
			 WHERE thread_id='{$threadId}' AND sender_id!='{$meId}' AND is_read=0 AND isDelete=0"
		);

		return array('ack' => 1, 'messages' => $messages, 'peer_id' => $this->getPeerId($threadId, $meId), 'peer_name' => $this->getUserDisplayName($this->getPeerId($threadId, $meId)));
	}

	public function sendMessage($threadId, $meId, $text)
	{
		$threadId = (int) $threadId;
		$meId = (int) $meId;
		$text = trim($text);
		if ($text === '') {
			return array('ack' => 0, 'ack_msg' => 'Message cannot be empty');
		}
		if (strlen($text) > 4000) {
			$text = substr($text, 0, 4000);
		}
		if (!$this->userCanAccessThread($threadId, $meId)) {
			return array('ack' => 0, 'ack_msg' => 'Access denied');
		}
		$now = date('Y-m-d H:i:s');
		$safe = $this->db->clean($text);
		$rows = array('thread_id', 'sender_id', 'message_text', 'is_read', 'created_date', 'isActive', 'isDelete');
		$values = array($threadId, $meId, $safe, 0, $now, 1, 0);
		$mid = $this->db->rp_insert($this->messageTable, $values, $rows, 0);
		if (!$mid) {
			return array('ack' => 0, 'ack_msg' => 'Failed to send');
		}
		$this->db->rp_update($this->threadTable, array(
			'last_message_id' => (int) $mid,
			'last_message_date' => $now,
			'modified_date' => $now,
		), "id='{$threadId}'", 0);

		$peerId = $this->getPeerId($threadId, $meId);
		$this->notifyNewMessage($meId, $peerId, $threadId, (int) $mid, $text);

		return array(
			'ack' => 1,
			'ack_msg' => 'Sent',
			'message' => array(
				'id' => (int) $mid,
				'sender_id' => $meId,
				'is_mine' => 1,
				'message_text' => $text,
				'created_date' => $now,
				'is_read' => 0,
			),
		);
	}

	/**
	 * Resolve System User id from sales_executive_id (mobile App)
	 */
	public function loginIdFromSalesExecutive($seId)
	{
		$seId = (int) $seId;
		if ($seId <= 0) {
			return 0;
		}
		$id = $this->db->rp_getValue(CTABLE_ADMIN, 'id', "sales_executive_id='{$seId}' AND type=2 AND isDelete=0", 0);
		return $id ? (int) $id : 0;
	}

	public function notifyNewMessage($senderLoginId, $peerLoginId, $threadId, $messageId, $text)
	{
		$senderLoginId = (int) $senderLoginId;
		$peerLoginId = (int) $peerLoginId;
		$threadId = (int) $threadId;
		$messageId = (int) $messageId;
		if ($peerLoginId <= 0) {
			return;
		}
		$senderName = $this->getUserDisplayName($senderLoginId);
		$preview = $text;
		if (strlen($preview) > 100) {
			$preview = substr($preview, 0, 97) . '...';
		}
		$title = 'New Chat Message';
		$descr = $senderName . ': ' . $preview;

		require_once dirname(__FILE__) . '/push_notification.class.php';
		$push = new PushNotification();

		$peerSeId = (int) $this->db->rp_getValue(CTABLE_ADMIN, 'sales_executive_id', "id='{$peerLoginId}' AND isDelete=0", 0);
		if ($peerSeId > 0) {
			$push->commonNotification($peerSeId, $threadId, 'employee_chat', $title, $descr, 'sales_executive', 'employee_chat');
		} else {
			// Admin / non-SE login: store notification against login id + push web token
			$rows = array('user_id', 'referance_id', 'referance_type', 'notification_title', 'notification_description', 'user_type', 'notification_type');
			$values = array($peerLoginId, $threadId, 'employee_chat', $title, $descr, 'admin', 'employee_chat');
			$this->db->rp_insert('notification', $values, $rows, 0);
			if (defined('NOTIFICATION_SEND') && NOTIFICATION_SEND) {
				$token = $this->db->rp_getValue(CTABLE_ADMIN, 'refresh_token_web', "id='{$peerLoginId}' AND refresh_token_web!='' AND isDelete=0", 0);
				if ($token != '') {
					$msg = array(
						'type' => 'employee_chat',
						'title' => $title,
						'description' => $descr,
						'body' => $descr,
						'user_id' => $peerLoginId,
						'reference_id' => $threadId,
						'item_id' => $messageId,
						'reference_type' => 'employee_chat',
						'thread_id' => $threadId,
					);
					$push->send_notification1($msg, array($token), 0);
				}
			}
		}
	}

	public function unreadCount($meId)
	{
		$meId = (int) $meId;
		$threads = $this->db->rp_getData($this->threadTable, 'id', "isDelete=0 AND (user_one_id='{$meId}' OR user_two_id='{$meId}')", '', 0);
		$total = 0;
		if ($threads) {
			while ($t = mysqli_fetch_assoc($threads)) {
				$total += (int) $this->db->rp_getTotalRecord($this->messageTable, "thread_id='" . (int) $t['id'] . "' AND sender_id!='{$meId}' AND is_read=0 AND isDelete=0", 0);
			}
		}
		return $total;
	}

	/**
	 * Live poll payload: unread count + newest unread messages (for toast without refresh)
	 */
	public function getLiveNotify($meId, $afterMsgId)
	{
		$meId = (int) $meId;
		$afterMsgId = (int) $afterMsgId;
		$unread = $this->unreadCount($meId);
		$items = array();
		$threadIds = array();
		$tr = $this->db->rp_getData($this->threadTable, 'id', "isDelete=0 AND (user_one_id='{$meId}' OR user_two_id='{$meId}')", '', 0);
		if ($tr) {
			while ($t = mysqli_fetch_assoc($tr)) {
				$threadIds[] = (int) $t['id'];
			}
		}
		if (!empty($threadIds)) {
			$in = implode(',', $threadIds);
			$where = "thread_id IN ({$in}) AND sender_id!='{$meId}' AND isDelete=0 AND is_read=0";
			if ($afterMsgId > 0) {
				$where .= " AND id>'{$afterMsgId}'";
			}
			$mr = $this->db->rp_getData($this->messageTable, 'id,thread_id,sender_id,message_text,created_date', $where, 'id DESC', 0);
			// limit to last 5
			$count = 0;
			if ($mr) {
				while ($m = mysqli_fetch_assoc($mr)) {
					$items[] = array(
						'id' => (int) $m['id'],
						'thread_id' => (int) $m['thread_id'],
						'sender_id' => (int) $m['sender_id'],
						'sender_name' => $this->getUserDisplayName((int) $m['sender_id']),
						'message_text' => $m['message_text'],
						'created_date' => $m['created_date'],
					);
					$count++;
					if ($count >= 5) {
						break;
					}
				}
			}
			$items = array_reverse($items);
		}
		$latestId = $afterMsgId;
		foreach ($items as $it) {
			if ($it['id'] > $latestId) {
				$latestId = $it['id'];
			}
		}
		// Also track absolute latest message in my threads (for baseline without toast flood)
		$maxId = $latestId;
		if (!empty($threadIds)) {
			$in = implode(',', $threadIds);
			$maxGot = (int) $this->db->rp_getValue($this->messageTable, 'MAX(id)', "thread_id IN ({$in}) AND isDelete=0", 0);
			if ($maxGot > $maxId) {
				$maxId = $maxGot;
			}
		}
		return array(
			'ack' => 1,
			'unread_total' => $unread,
			'latest_msg_id' => $maxId,
			'messages' => ($afterMsgId > 0) ? $items : array(),
		);
	}

	/**
	 * Mobile: list other employees who have System User login (type 0/2)
	 */
	public function listUsersForMobile($meLoginId, $search)
	{
		return $this->listUsers($meLoginId, $search);
	}
}
