<?php
/**
 * Employee Chat mobile APIs
 * #236 get_employee_chat_users
 * #237 get_employee_chat_threads
 * #238 get_employee_chat_messages
 * #239 send_employee_chat_message
 * #240 get_employee_chat_unread
 *
 * Entry: service/service_employee_chat.php?key=1226&s=236&sales_executive_id=
 */
include('connect.php');
require_once('../include/employee_chat.class.php');

if ($is_valid_api_key) {
	if ($is_valid_service) {
		$chat = new EmployeeChat();
		$seId = isset($_REQUEST['sales_executive_id']) ? (int) $_REQUEST['sales_executive_id'] : 0;
		$meLogin = $chat->loginIdFromSalesExecutive($seId);
		// App SE without System User → auto-create chat login (same as Web Employees open)
		if ($meLogin <= 0 && $seId > 0) {
			$meLogin = $chat->ensureLoginForSalesExecutive($seId);
		}

		if ($service == 'get_employee_chat_users' || $service == 236) {
			if ($meLogin <= 0) {
				$db->printJSON(array('ack' => 0, 'ack_msg' => 'sales_executive_id required / no System User linked', 'developer_msg' => 'Map SE to dealer_distributor_network'));
			} else {
				$q = isset($_REQUEST['search']) ? $_REQUEST['search'] : (isset($_REQUEST['q']) ? $_REQUEST['q'] : '');
				$users = $chat->listUsersForMobile($meLogin, $q);
				$db->printJSON(array(
					'ack' => 1,
					'ack_msg' => 'Users fetched',
					'developer_msg' => 'OK',
					'login_id' => $meLogin,
					'result' => $users,
				));
			}
		} else if ($service == 'get_employee_chat_threads' || $service == 237) {
			if ($meLogin <= 0) {
				$db->printJSON(array('ack' => 0, 'ack_msg' => 'sales_executive_id required / no System User linked', 'developer_msg' => 'Map SE login'));
			} else {
				$db->printJSON(array(
					'ack' => 1,
					'ack_msg' => 'Threads fetched',
					'developer_msg' => 'OK',
					'unread_total' => $chat->unreadCount($meLogin),
					'result' => $chat->listThreads($meLogin),
				));
			}
		} else if ($service == 'get_employee_chat_messages' || $service == 238) {
			if ($meLogin <= 0) {
				$db->printJSON(array('ack' => 0, 'ack_msg' => 'sales_executive_id required / no System User linked', 'developer_msg' => 'Map SE login'));
			} else {
				$threadId = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
				$peerLoginId = isset($_REQUEST['peer_login_id']) ? (int) $_REQUEST['peer_login_id'] : 0;
				$peerSeId = isset($_REQUEST['peer_sales_executive_id']) ? (int) $_REQUEST['peer_sales_executive_id'] : 0;
				$afterId = isset($_REQUEST['after_id']) ? (int) $_REQUEST['after_id'] : 0;

				if ($threadId <= 0) {
					if ($peerLoginId <= 0 && $peerSeId > 0) {
						$peerLoginId = $chat->ensureLoginForSalesExecutive($peerSeId);
					}
					if ($peerLoginId <= 0 && isset($_REQUEST['peer_id'])) {
						$peerLoginId = (int) $_REQUEST['peer_id'];
					}
					$open = $chat->getOrCreateThread($meLogin, $peerLoginId);
					if ($open['ack'] != 1) {
						$db->printJSON($open);
						exit;
					}
					$threadId = (int) $open['thread_id'];
				}

				$msgs = $chat->getMessages($threadId, $meLogin, $afterId);
				$msgs['thread_id'] = $threadId;
				$db->printJSON($msgs);
			}
		} else if ($service == 'send_employee_chat_message' || $service == 239) {
			if ($meLogin <= 0) {
				$db->printJSON(array('ack' => 0, 'ack_msg' => 'sales_executive_id required / no System User linked', 'developer_msg' => 'Map SE login'));
			} else {
				$threadId = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
				$peerLoginId = isset($_REQUEST['peer_login_id']) ? (int) $_REQUEST['peer_login_id'] : 0;
				$peerSeId = isset($_REQUEST['peer_sales_executive_id']) ? (int) $_REQUEST['peer_sales_executive_id'] : 0;
				$text = isset($_REQUEST['message_text']) ? $_REQUEST['message_text'] : (isset($_REQUEST['message']) ? $_REQUEST['message'] : '');

				if ($threadId <= 0) {
					if ($peerLoginId <= 0 && $peerSeId > 0) {
						$peerLoginId = $chat->ensureLoginForSalesExecutive($peerSeId);
					}
					if ($peerLoginId <= 0 && isset($_REQUEST['peer_id'])) {
						$peerLoginId = (int) $_REQUEST['peer_id'];
					}
					$open = $chat->getOrCreateThread($meLogin, $peerLoginId);
					if ($open['ack'] != 1) {
						$db->printJSON($open);
						exit;
					}
					$threadId = (int) $open['thread_id'];
				}

				$res = $chat->sendMessage($threadId, $meLogin, $text);
				$res['thread_id'] = $threadId;
				$db->printJSON($res);
			}
		} else if ($service == 'get_employee_chat_unread' || $service == 240) {
			if ($meLogin <= 0) {
				$db->printJSON(array('ack' => 0, 'ack_msg' => 'sales_executive_id required / no System User linked', 'developer_msg' => 'Map SE login'));
			} else {
				$db->printJSON(array(
					'ack' => 1,
					'ack_msg' => 'Unread count',
					'developer_msg' => 'OK',
					'unread_total' => $chat->unreadCount($meLogin),
				));
			}
		} else {
			$db->printJSON(array('ack' => 0, 'ack_msg' => 'Invalid chat service', 'developer_msg' => 'Unknown s=' . $service));
		}
	} else {
		$ack = array('ack' => 0, 'ack_msg' => 'Internal error!!', 'developer_msg' => 'Check your API Key or contact Admin', 'extra' => array('requested_params' => $_REQUEST, 'other' => array()));
		$db->printJSON($ack);
	}
} else {
	$ack = array('ack' => 0, 'ack_msg' => 'Internal error!!', 'developer_msg' => 'Check your API Key or contact Admin', 'extra' => array('requested_params' => $_REQUEST, 'other' => array()));
	$db->printJSON($ack);
}
