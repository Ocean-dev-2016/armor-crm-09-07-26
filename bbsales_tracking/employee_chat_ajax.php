<?php
$page_id = 670;
$page_slug = 'employee_chat';
include('connect.php');
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__FILE__) . '/../include/employee_chat.class.php';

$chat = new EmployeeChat();
$me = $chat->currentUserId();
if ($me <= 0) {
	echo json_encode(array('ack' => 0, 'ack_msg' => 'Not logged in'));
	exit;
}

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

if ($mode === 'users') {
	$search = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
	echo json_encode(array('ack' => 1, 'users' => $chat->listUsers($me, $search)));
	exit;
}

if ($mode === 'threads') {
	echo json_encode(array('ack' => 1, 'threads' => $chat->listThreads($me), 'unread_total' => $chat->unreadCount($me)));
	exit;
}

if ($mode === 'open') {
	$peerId = isset($_REQUEST['peer_id']) ? (int) $_REQUEST['peer_id'] : 0;
	$peerSeId = isset($_REQUEST['peer_se_id']) ? (int) $_REQUEST['peer_se_id'] : 0;
	echo json_encode($chat->openChatWithPeer($me, $peerId, $peerSeId));
	exit;
}

if ($mode === 'messages') {
	$threadId = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
	$afterId = isset($_REQUEST['after_id']) ? (int) $_REQUEST['after_id'] : 0;
	echo json_encode($chat->getMessages($threadId, $me, $afterId));
	exit;
}

if ($mode === 'send') {
	$threadId = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
	$text = isset($_REQUEST['message_text']) ? $_REQUEST['message_text'] : '';
	echo json_encode($chat->sendMessage($threadId, $me, $text));
	exit;
}

if ($mode === 'unread') {
	echo json_encode(array('ack' => 1, 'unread_total' => $chat->unreadCount($me)));
	exit;
}

if ($mode === 'live_notify') {
	$afterId = isset($_REQUEST['after_id']) ? (int) $_REQUEST['after_id'] : 0;
	echo json_encode($chat->getLiveNotify($me, $afterId));
	exit;
}

if ($mode === 'delete_thread') {
	$threadId = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
	$isSuperAdmin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);
	echo json_encode($chat->deleteThread($threadId, $me, $isSuperAdmin));
	exit;
}

echo json_encode(array('ack' => 0, 'ack_msg' => 'Invalid mode'));
