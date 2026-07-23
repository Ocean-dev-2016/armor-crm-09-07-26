<?php
$page_id = 670;
$page_slug = 'employee_chat';
$ctable = 'employee_chat';
$ctable1 = 'Chat';
$main_page = 'employee_chat';
$page = 'manage_employee_chat';
$page_title = 'Chat';
$page_hierarchy = array(
	array('link' => 'employee_chat_manage.php', 'title' => 'Chat'),
);
include('connect.php');
$meName = isset($_SESSION[SITE_SESS . 'SESS_NAME']) ? $_SESSION[SITE_SESS . 'SESS_NAME'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>Chat | <?php echo SITETITLE; ?></title>
	<?php include('include_css.php'); ?>
	<style type="text/css">
		:root {
			--wa-header: #075E54;
			--wa-header-2: #128C7E;
			--wa-accent: #25D366;
			--wa-chat-bg: #E5DDD5;
			--wa-out: #DCF8C6;
			--wa-in: #FFFFFF;
			--wa-panel: #FFFFFF;
			--wa-muted: #667781;
			--wa-border: #e9edef;
		}
		/* Full screen chat — hide CRM chrome margins */
		body.page-md.ec-fullscreen .page-head { display: none !important; }
		body.page-md.ec-fullscreen .page-footer { display: none !important; }
		body.page-md.ec-fullscreen .page-container { padding: 0 !important; margin: 0 !important; }
		body.page-md.ec-fullscreen .page-content { padding: 0 !important; background: #fff !important; }
		body.page-md.ec-fullscreen .page-content > .container {
			width: 100% !important;
			max-width: 100% !important;
			padding: 0 !important;
			margin: 0 !important;
		}
		.wa-shell {
			background: var(--wa-panel);
			border-radius: 0;
			overflow: hidden;
			box-shadow: none;
			border: 0;
			height: calc(100vh - 75px);
			min-height: calc(100vh - 75px);
		}
		.wa-layout {
			display: flex;
			height: 100%;
			min-height: 100%;
			max-height: none;
		}
		.wa-left {
			width: 380px; max-width: 40%;
			display: flex; flex-direction: column;
			border-right: 1px solid var(--wa-border);
			background: #fff;
		}
		.wa-left-top {
			background: var(--wa-header);
			color: #fff;
			padding: 12px 14px 10px;
		}
		.wa-left-top h4 { margin: 0 0 10px; font-size: 17px; font-weight: 600; letter-spacing: .2px; }
		.wa-tabs { display: flex; gap: 6px; margin-bottom: 10px; }
		.wa-tabs a {
			flex: 1; text-align: center; padding: 7px 8px; border-radius: 18px;
			font-size: 12px; font-weight: 700; color: #d2f8e5; text-decoration: none;
			background: rgba(255,255,255,.12);
		}
		.wa-tabs a.active { background: #fff; color: var(--wa-header); }
		.wa-search { position: relative; }
		.wa-search input {
			width: 100%; border: 0; border-radius: 8px; padding: 9px 12px 9px 34px;
			font-size: 13px; background: #f0f2f5; color: #111;
		}
		.wa-search i { position: absolute; left: 11px; top: 10px; color: #8696a0; }
		.wa-list { overflow-y: auto; flex: 1; background: #fff; }
		.wa-item {
			display: flex; gap: 12px; align-items: center;
			padding: 12px 14px; border-bottom: 1px solid #f0f2f5; cursor: pointer;
		}
		.wa-item:hover { background: #f5f6f6; }
		.wa-item.active { background: #f0f2f5; }
		.wa-avatar {
			width: 49px; height: 49px; border-radius: 50%;
			background: #dfe5e7; color: #54656f;
			display: flex; align-items: center; justify-content: center;
			font-weight: 700; font-size: 18px; flex-shrink: 0;
			overflow: hidden;
		}
		.wa-avatar.g1 { background: #53bdeb; color: #fff; }
		.wa-avatar.g2 { background: #06cf9c; color: #fff; }
		.wa-avatar.g3 { background: #a5b4c0; color: #fff; }
		.wa-meta { flex: 1; min-width: 0; }
		.wa-meta .top { display: flex; justify-content: space-between; gap: 8px; }
		.wa-meta .name { font-weight: 600; color: #111b21; font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
		.wa-meta .time { color: var(--wa-muted); font-size: 12px; white-space: nowrap; }
		.wa-meta .preview { color: var(--wa-muted); font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; }
		.wa-badge {
			background: var(--wa-accent); color: #fff; border-radius: 12px;
			min-width: 20px; text-align: center; padding: 1px 6px; font-size: 11px; font-weight: 700;
		}
		.wa-right { flex: 1; display: flex; flex-direction: column; min-width: 0; }
		.wa-right-head {
			display: flex; align-items: center; gap: 12px;
			padding: 10px 16px; background: #f0f2f5; border-bottom: 1px solid #e9edef;
		}
		.wa-right-head .info strong { display: block; color: #111b21; font-size: 16px; }
		.wa-right-head .info span { font-size: 13px; color: var(--wa-muted); }
		.wa-messages {
			flex: 1; overflow-y: auto; padding: 18px 6% 12px;
			background-color: #E5DDD5;
			background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h60v60H0z' fill='%23E5DDD5'/%3E%3Cpath d='M10 10h2v2h-2zm20 0h2v2h-2zm20 0h2v2h-2zM0 30h2v2H0zm20 0h2v2h-2zm20 0h2v2h-2zm20 0h2v2h-2zM10 50h2v2h-2zm20 0h2v2h-2zm20 0h2v2h-2z' fill='%23d5cdc4' fill-opacity='.35'/%3E%3C/svg%3E");
		}
		.wa-row { display: flex; margin-bottom: 3px; }
		.wa-row.mine { justify-content: flex-end; }
		.wa-row.theirs { justify-content: flex-start; }
		.wa-bubble {
			max-width: 65%; padding: 6px 9px 4px 9px; border-radius: 8px;
			box-shadow: 0 1px 0.5px rgba(0,0,0,.13);
			font-size: 14.2px; line-height: 1.45; color: #111b21; word-wrap: break-word;
			position: relative;
		}
		.wa-row.mine .wa-bubble { background: var(--wa-out); border-top-right-radius: 0; }
		.wa-row.theirs .wa-bubble { background: var(--wa-in); border-top-left-radius: 0; }
		.wa-bubble .time {
			display: inline-block; float: right; margin: 4px 0 0 12px;
			font-size: 11px; color: #667781; white-space: nowrap;
		}
		.wa-compose {
			display: none; background: #f0f2f5; padding: 10px 12px;
			border-top: 1px solid #e9edef;
		}
		.wa-compose-inner { display: flex; gap: 8px; align-items: flex-end; }
		.wa-compose textarea {
			flex: 1; border: 0; border-radius: 8px; padding: 11px 14px;
			resize: none; height: 46px; max-height: 120px; font-size: 15px;
			background: #fff; box-shadow: none;
		}
		.wa-send {
			width: 46px; height: 46px; border-radius: 50%; border: 0;
			background: var(--wa-header-2); color: #fff; font-size: 18px;
		}
		.wa-send:hover { background: var(--wa-header); }
		.wa-empty { text-align: center; color: #667781; padding: 100px 24px; }
		.wa-empty i { font-size: 48px; color: #c5d0db; display: block; margin-bottom: 12px; }
		.wa-welcome {
			margin: auto; background: #fff; border-radius: 8px; padding: 28px 36px;
			box-shadow: 0 1px 3px rgba(0,0,0,.08); text-align: center; color: #41525d; max-width: 420px;
		}
		.wa-welcome i { font-size: 54px; color: #25D366; display: block; margin-bottom: 12px; }
		@media (max-width: 768px) {
			.wa-left { width: 100%; max-width: 100%; }
			.wa-layout.chat-open .wa-left { display: none; }
			.wa-right { display: none; }
			.wa-layout.chat-open .wa-right { display: flex; }
		}
	</style>
</head>
<body class="page-md ec-fullscreen">
<?php include('header.php'); ?>
<div class="page-container">
	<div class="page-content">
		<div class="container">
			<div class="wa-shell">
				<div class="wa-layout" id="wa_layout">
					<div class="wa-left">
						<div class="wa-left-top">
							<h4>
								<a href="dashboard.php" style="color:#fff;margin-right:8px;" title="Back"><i class="fa fa-arrow-left"></i></a>
								<i class="fa fa-whatsapp"></i> Chat
							</h4>
							<div class="wa-tabs">
								<a href="javascript:;" class="active" id="tab_chats">Chats</a>
								<a href="javascript:;" id="tab_users">Employees</a>
							</div>
							<div class="wa-search">
								<i class="fa fa-search"></i>
								<input type="text" id="ec_search" placeholder="Search or start new chat" />
							</div>
						</div>
						<div class="wa-list" id="ec_list"></div>
					</div>
					<div class="wa-right">
						<div class="wa-right-head" id="ec_head" style="display:none;">
							<div class="wa-avatar g1" id="ec_peer_avatar">?</div>
							<div class="info" style="flex:1;">
								<strong id="ec_peer_name">Select chat</strong>
								<span id="ec_peer_status">click for contact info</span>
							</div>
							<div style="font-size:12px;color:#667781;">You: <?php echo htmlspecialchars($meName); ?></div>
						</div>
						<div class="wa-messages" id="ec_messages">
							<div class="wa-welcome">
								<i class="fa fa-whatsapp"></i>
								<div style="font-size:20px;margin-bottom:6px;">Armor CRM Chat</div>
								<div style="font-size:13px;line-height:1.5;">Select an employee from the left to start messaging.<br>All sales persons are listed under <b>Employees</b>.</div>
							</div>
						</div>
						<div class="wa-compose" id="ec_compose">
							<div class="wa-compose-inner">
								<textarea id="ec_text" placeholder="Type a message"></textarea>
								<button type="button" class="wa-send" id="ec_send" title="Send"><i class="fa fa-send"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
<?php include('include_js.php'); ?>
<script type="text/javascript">
var EC = { mode:'threads', threadId:0, peerId:0, peerSeId:0, lastMsgId:0, pollTimer:null, knownIds:{} };
window.EC = EC;

function esc(s){ return $('<div/>').text(s||'').html(); }
function initial(name){ name=(name||'?').replace(/\s+/g,''); return (name.charAt(0)||'?').toUpperCase(); }
function avatarClass(name){ var n=(name||'').charCodeAt(0)||0; return 'g'+((n%3)+1); }
function shortTime(d){ if(!d) return ''; var p=String(d).split(' '); return p.length>1 ? p[1].substr(0,5) : d; }

function loadList(){
	var q=$.trim($('#ec_search').val());
	if(EC.mode==='users'){
		$.getJSON('employee_chat_ajax.php',{mode:'users',q:q},function(res){
			if(!res||res.ack!=1) return;
			var html='';
			if(!res.users||!res.users.length) html='<div class="wa-empty">No employees found</div>';
			$.each(res.users||[],function(i,u){
				var se=u.peer_se_id||u.sales_executive_id||0;
				var pid=u.id||0;
				html+='<div class="wa-item" data-peer="'+pid+'" data-se="'+se+'">'+
					'<div class="wa-avatar '+avatarClass(u.name)+'">'+esc(initial(u.name))+'</div>'+
					'<div class="wa-meta"><div class="top"><div class="name">'+esc(u.name)+'</div></div>'+
					'<div class="preview">'+esc(u.role||'')+(u.username?' · '+esc(u.username):'')+'</div></div></div>';
			});
			$('#ec_list').html(html);
		});
	} else {
		$.getJSON('employee_chat_ajax.php',{mode:'threads'},function(res){
			if(!res||res.ack!=1) return;
			var n=parseInt(res.unread_total,10)||0;
			var $b=$('.chat-unread-count');
			if(n>0) $b.text(n).show(); else $b.hide();
			var list=res.threads||[];
			if(q){ list=$.grep(list,function(t){ return (t.peer_name||'').toLowerCase().indexOf(q.toLowerCase())>=0; }); }
			var html='';
			if(!list.length) html='<div class="wa-empty">No chats yet.<br>Open <b>Employees</b> to start chatting.</div>';
			$.each(list,function(i,t){
				var active=t.thread_id==EC.threadId?' active':'';
				var badge=t.unread>0?' <span class="wa-badge">'+t.unread+'</span>':'';
				html+='<div class="wa-item'+active+'" data-peer="'+t.peer_id+'" data-se="0" data-thread="'+t.thread_id+'">'+
					'<div class="wa-avatar '+avatarClass(t.peer_name)+'">'+esc(initial(t.peer_name))+'</div>'+
					'<div class="wa-meta"><div class="top"><div class="name">'+esc(t.peer_name)+badge+'</div>'+
					'<div class="time">'+esc(shortTime(t.last_message_date))+'</div></div>'+
					'<div class="preview">'+esc(t.last_message||'')+'</div></div></div>';
			});
			$('#ec_list').html(html);
		});
	}
}

function appendMessages(messages, replace){
	if(replace){ $('#ec_messages').html(''); EC.lastMsgId=0; EC.knownIds={}; }
	if(!messages||!messages.length){
		if(replace) $('#ec_messages').html('<div class="wa-empty"><i class="fa fa-comments-o"></i>No messages yet.<br>Say hello!</div>');
		return;
	}
	if(replace) $('#ec_messages').html('');
	$.each(messages,function(i,m){
		if(EC.knownIds[m.id]) return;
		EC.knownIds[m.id]=1;
		var cls=m.is_mine==1?'mine':'theirs';
		$('#ec_messages').append('<div class="wa-row '+cls+'"><div class="wa-bubble">'+esc(m.message_text)+'<span class="time">'+esc(shortTime(m.created_date))+'</span></div></div>');
		if(m.id>EC.lastMsgId) EC.lastMsgId=m.id;
		if(m.is_mine!=1 && typeof toastr!=='undefined' && !replace){
			toastr.options={timeOut:3500,positionClass:'toast-top-right'};
			toastr.info(m.message_text,'New message');
		}
	});
	var box=document.getElementById('ec_messages');
	box.scrollTop=box.scrollHeight;
}

function openPeer(peerId, peerSeId){
	EC.peerId=peerId||0;
	EC.peerSeId=peerSeId||0;
	$.getJSON('employee_chat_ajax.php',{mode:'open', peer_id:EC.peerId, peer_se_id:EC.peerSeId},function(res){
		if(!res||res.ack!=1){ alert((res&&res.ack_msg)||'Could not open chat'); return; }
		EC.threadId=res.thread_id;
		EC.peerId=res.peer_id||EC.peerId;
		$('#ec_head').show();
		$('#ec_peer_name').text(res.peer_name||'Chat');
		$('#ec_peer_avatar').attr('class','wa-avatar '+avatarClass(res.peer_name)).text(initial(res.peer_name));
		$('#ec_peer_status').text('online');
		$('#ec_compose').show();
		$('#wa_layout').addClass('chat-open');
		appendMessages(res.messages,true);
		EC.mode='threads';
		$('#tab_chats').addClass('active'); $('#tab_users').removeClass('active');
		loadList();
		startPoll();
		$('#ec_text').focus();
	});
}

function pollMessages(){
	if(!EC.threadId) return;
	$.getJSON('employee_chat_ajax.php',{mode:'messages',thread_id:EC.threadId,after_id:EC.lastMsgId},function(res){
		if(!res||res.ack!=1) return;
		if(res.messages&&res.messages.length){
			if($('#ec_messages .wa-empty, #ec_messages .wa-welcome').length) $('#ec_messages').html('');
			appendMessages(res.messages,false);
			loadList();
		}
	});
}
function startPoll(){ if(EC.pollTimer) clearInterval(EC.pollTimer); EC.pollTimer=setInterval(pollMessages,3000); }

function sendMessage(){
	var text=$.trim($('#ec_text').val());
	if(!text||!EC.threadId) return;
	$('#ec_send').prop('disabled',true);
	$.post('employee_chat_ajax.php',{mode:'send',thread_id:EC.threadId,message_text:text},function(res){
		$('#ec_send').prop('disabled',false);
		if(typeof res==='string'){ try{res=$.parseJSON(res);}catch(e){res=null;} }
		if(!res||res.ack!=1){ alert((res&&res.ack_msg)||'Send failed'); return; }
		$('#ec_text').val('');
		if($('#ec_messages .wa-empty, #ec_messages .wa-welcome').length) $('#ec_messages').html('');
		appendMessages([res.message],false);
		loadList();
	},'json');
}

$(function(){
	loadList();
	$('#tab_chats').on('click',function(){ EC.mode='threads'; $(this).addClass('active'); $('#tab_users').removeClass('active'); loadList(); });
	$('#tab_users').on('click',function(){ EC.mode='users'; $(this).addClass('active'); $('#tab_chats').removeClass('active'); loadList(); });
	var t=null; $('#ec_search').on('keyup',function(){ clearTimeout(t); t=setTimeout(loadList,250); });
	$('#ec_list').on('click','.wa-item',function(){
		openPeer(parseInt($(this).data('peer'),10)||0, parseInt($(this).data('se'),10)||0);
	});
	$('#ec_send').on('click',sendMessage);
	$('#ec_text').on('keydown',function(e){ if(e.keyCode===13 && !e.shiftKey){ e.preventDefault(); sendMessage(); } });
	setInterval(function(){ if(EC.mode==='threads') loadList(); },8000);
});
</script>
</body>
</html>
