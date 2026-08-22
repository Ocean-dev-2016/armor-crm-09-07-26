<?php
$page_id = 400;
$page_slug = 'dashboard';
include("connect.php");
require_once("../include/class.log.php");
include("translations/timeago.inc.php");
$timeZone = "ASIA/KOLKATA";

if (!isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	exit;
}

$notifications = $system->getQuickNotifications(50);
$total_count = $notifications ? count($notifications) : 0;
?>
<div class="portlet light bordered admin-dash-panel admin-notif-panel">
	<div class="portlet-title" style="background:#e87e04;">
		<div class="caption">
			<i class="fa fa-bell"></i>
			<span class="caption-subject bold uppercase">Notifications</span>
			<span class="badge dashboard-notif-count"><?php echo (int) $total_count; ?></span>
		</div>
		<div class="actions">
			<a href="notification_manage.php?mode=all" class="btn btn-circle btn-sm">
				<i class="fa fa-list"></i> View All
			</a>
			<a href="javascript:;" onclick="aj.refreshAdminNotifications();" class="btn btn-circle btn-sm">
				<i class="fa fa-refresh"></i>
			</a>
		</div>
	</div>
	<div class="portlet-body" style="padding:0;">
		<ul class="dashboard-notification-list" id="dashboard-notification-list">
			<?php
			if ($notifications) {
				foreach ($notifications as $notification) {
					$time = timeAgoInWords($notification['created_date'], $timeZone, 'en');
					$title = htmlspecialchars(stripslashes($notification['notification_title']), ENT_QUOTES, 'UTF-8');
					$desc = htmlspecialchars(stripslashes($notification['notification_description']), ENT_QUOTES, 'UTF-8');
					$notif_type = isset($notification['notification_type']) ? $notification['notification_type'] : '';
					$ref_type = isset($notification['referance_type']) ? $notification['referance_type'] : '';
					$ref_id = isset($notification['referance_id']) ? (int) $notification['referance_id'] : 0;
					$view_link = '';
					$followup_meta = '';
					if ($notif_type == 'followup' || $ref_type == 'followup') {
						$view_link = '<a href="followuplist_manage.php?followup_type=today" class="btn btn-xs btn-info" style="margin-right:4px;"><i class="fa fa-eye"></i> View</a>';
						$details = $system->resolveFollowupPartyNames($ref_id);
						$followup_meta = '
							<div class="notif-followup-meta">
								<span class="notif-meta-item"><i class="fa fa-user"></i> <strong>Employee:</strong> ' . htmlspecialchars(stripslashes($details['sales_name'])) . '</span>
								<span class="notif-meta-item"><i class="fa fa-building"></i> <strong>Customer:</strong> ' . htmlspecialchars(stripslashes($details['customer_name'])) . '</span>
							</div>';
						if ($details['followup_time'] != "") {
							$followup_meta .= '<div class="notif-followup-time"><i class="fa fa-clock-o"></i> ' . $details['followup_time'];
							if ($details['through_label'] != "") {
								$followup_meta .= ' &nbsp;|&nbsp; ' . $details['through_label'];
							}
							$followup_meta .= '</div>';
						}
					}
					?>
					<li class="dashboard-notif-item">
						<div class="notif-item-title"><?php echo $title; ?></div>
						<?php echo $followup_meta; ?>
						<?php if ($desc != '') { ?>
							<div class="notif-item-desc"><?php echo $desc; ?></div>
						<?php } ?>
						<div class="notif-item-time"><i class="fa fa-clock-o"></i> <?php echo $time; ?></div>
						<div class="notif-item-actions">
							<?php echo $view_link; ?>
							<a class="btn btn-xs btn-success" onclick="aj.delete_notification(this,<?php echo (int) $notification['id']; ?>)">
								<i class="fa fa-check"></i> Done
							</a>
						</div>
					</li>
					<?php
				}
			} else {
				?>
				<li class="notif-empty" style="padding:30px;text-align:center;color:#999;">
					<i class="fa fa-bell-slash fa-2x"></i><br/>No Notifications
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>
<style>
.admin-notif-panel .portlet-title .badge { background:#fff; color:#e87e04; }
.admin-notif-panel .dashboard-notification-list {
	list-style:none; margin:0; padding:0;
	max-height:420px; overflow-y:auto;
}
.admin-notif-panel .dashboard-notif-item {
	border-bottom:1px solid #f0f0f0;
	padding:12px 15px;
}
.admin-notif-panel .dashboard-notif-item:hover { background:#fafafa; }
.admin-notif-panel .notif-item-title { color:#333; font-weight:600; font-size:13px; margin-bottom:4px; }
.admin-notif-panel .notif-item-desc { color:#666; font-size:12px; line-height:1.4; margin-bottom:6px; }
.admin-notif-panel .notif-item-time { color:#999; font-size:11px; margin-bottom:6px; }
</style>
<?php require_once 'disconnect.php'; ?>
