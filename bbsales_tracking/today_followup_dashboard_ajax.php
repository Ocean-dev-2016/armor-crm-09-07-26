<?php
$page_id = 583;
$page_slug = 'page_followup';
include("connect.php");

if (!isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	exit;
}

$today = date('Y-m-d');
$ctable_where = "DATE(followup_date) = '" . $today . "' AND isDelete=0 AND isActive=1";

$SEID = array();
$sales_type_r = $db->rp_getData("sales_executive", "id", "type='service_executive'", "", 0);
if ($sales_type_r) {
	while ($sales_type_d = mysqli_fetch_array($sales_type_r)) {
		$SEID[] = $sales_type_d['id'];
	}
}
if (!empty($SEID)) {
	$ctable_where .= " AND user_id NOT IN (" . implode(",", $SEID) . ")";
}

$sales_filter = "";
if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != "null") {
	$ctable_where .= " AND user_id='" . (int) $_REQUEST['sales_id'] . "'";
	$sales_filter = $db->rp_getValue("sales_executive", "name", "id='" . (int) $_REQUEST['sales_id'] . "'", 0);
}

$total_today = $db->rp_getTotalRecord("followup", $ctable_where, 0);
$ctable_r = $db->rp_getData("followup", "*", $ctable_where, "followup_date ASC", 0, 200);

$through_map = array("1" => "Call", "2" => "SMS", "3" => "Email", "4" => "Whatsapp", "5" => "Visit");
$through_class_map = array("1" => "call", "2" => "sms", "3" => "email", "4" => "whatsapp", "5" => "visit");
$entry_type_map = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", "4" => "Web Customer", "5" => "Sales App", "6" => "Customer App");

$rows = array();
$pending_count = 0;
$responded_count = 0;

if ($ctable_r && mysqli_num_rows($ctable_r) > 0) {
	while ($row = mysqli_fetch_assoc($ctable_r)) {
		$company_name = "";
		$person_name = "";
		$mobile_no = "";
		$type_label = "";
		$followup_link = "followuplist_manage.php?followup_type=today";

		if ($row['reference_table'] == "sales_executive" || $row['reference_table'] == "executive") {
			$customer_id_val = ($row['reference_table'] == "executive") ? $row['reference_id'] : $row['visitor_id'];
			$customer_r = $db->rp_getData("executive", "company_name,cname,mobile_no1,customer_flag", "id='" . $customer_id_val . "'", "", 0);
			if ($customer_r && $customer_d = mysqli_fetch_assoc($customer_r)) {
				$suffix = ($customer_d['customer_flag'] == 1) ? " - P" : " - C";
				$company_name = $customer_d['company_name'] . $suffix;
				$person_name = $customer_d['cname'];
				$mobile_no = $customer_d['mobile_no1'];
			}
		} else if ($row['reference_table'] == "no_order_inquiry") {
			$company_name = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $row['reference_id'] . "'", 0);
			$person_name = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $row['reference_id'] . "'", 0);
			$mobile_no = $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $row['reference_id'] . "'", 0);
			$followup_link = "followup.php?mode=inquiry_followup&inquiry_id=" . $row['reference_id'] . "&sales_id=" . $row['user_id'];
		} else if ($row['reference_table'] == "quotation_detail") {
			$company_name = $db->rp_getValue("quotation_detail", "company_name", "id='" . $row['reference_id'] . "'", 0);
			$person_name = $db->rp_getValue("quotation_detail", "customer_name", "id='" . $row['reference_id'] . "'", 0);
			$followup_link = "followup.php?mode=quotation_followup&quotation_id=" . $row['reference_id'] . "&sales_id=" . $row['user_id'];
		} else if ($row['reference_table'] == "customer_inquiry") {
			$company_name = $db->rp_getValue("customer_inquiry", "company_name", "id='" . $row['reference_id'] . "'", 0);
		} else if ($row['reference_table'] == "manual_invoice_import") {
			$company_name = $db->rp_getValue("manually_invoice_outstanding_import", "bill_no", "id='" . $row['reference_id'] . "'", 0);
		}

		if ($row['reference_table'] == "no_order_inquiry") {
			$lead_flag = $db->rp_getValue("no_order_inquiry", "inquiry_lead_flag", "id='" . $row['reference_id'] . "'", 0);
			if ($lead_flag == '-1') {
				$type_label = "Prospects";
			} else if ($lead_flag == '1') {
				$type_label = "Leads";
			} else {
				$type_label = "Inquiry";
			}
		} else if ($row['reference_table'] == "sales_executive") {
			$type_label = "Customer Followup";
		} else if ($row['reference_table'] == "quotation_detail") {
			$type_label = "Quotation";
		} else if ($row['reference_table'] == "executive") {
			$type_label = "Customer";
		} else if ($row['reference_table'] == "manual_invoice_import") {
			$type_label = "Manual Invoice";
		} else if ($row['reference_table'] == "customer_inquiry") {
			$type_label = "Customer Inquiry";
		}

		$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $row['user_id'] . "'", 0);
		$through_label = isset($through_map[$row['through']]) ? $through_map[$row['through']] : "-";
		$through_class = isset($through_class_map[$row['through']]) ? $through_class_map[$row['through']] : "default";
		$entry_label = isset($entry_type_map[$row['entry_type']]) ? $entry_type_map[$row['entry_type']] : "-";
		$followup_time = ($row['followup_date'] != "0000-00-00 00:00:00") ? date('d-m-Y h:i A', strtotime($row['followup_date'])) : "-";
		$is_responded = (trim($row['response']) != "" || (int) $row['status'] == 1);

		if ($is_responded) {
			$responded_count++;
		} else {
			$pending_count++;
		}

		$rows[] = array(
			'company_name' => $company_name,
			'person_name' => $person_name,
			'mobile_no' => $mobile_no,
			'sales_name' => $sales_name,
			'followup_time' => $followup_time,
			'description' => $row['description'],
			'through_label' => $through_label,
			'through_class' => $through_class,
			'type_label' => $type_label,
			'entry_label' => $entry_label,
			'is_responded' => $is_responded,
			'followup_link' => $followup_link
		);
	}
}
?>
<div class="portlet light bordered admin-dash-panel today-followup-panel">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-calendar-check-o"></i>
			<span class="caption-subject bold uppercase">Today's Followup</span>
			<span class="tf-subtitle">Full Details — All Team</span>
			<span class="badge"><?php echo (int) $total_today; ?></span>
		</div>
		<div class="actions">
			<a href="followuplist_manage.php?followup_type=today" class="btn btn-circle btn-sm" title="Manage all followups">
				<i class="fa fa-list"></i> Manage All
			</a>
			<a href="javascript:;" onclick="loadTodayFollowupDashboard();" class="btn btn-circle btn-sm" title="Refresh list">
				<i class="fa fa-refresh"></i> Refresh
			</a>
		</div>
	</div>

	<div class="tf-summary-bar">
		<div class="tf-stat">
			<span class="num"><?php echo (int) $total_today; ?></span>
			<span class="lbl">Total</span>
		</div>
		<div class="tf-stat tf-pending">
			<span class="num"><?php echo (int) $pending_count; ?></span>
			<span class="lbl">Pending</span>
		</div>
		<div class="tf-stat tf-done">
			<span class="num"><?php echo (int) $responded_count; ?></span>
			<span class="lbl">Responded</span>
		</div>
		<div class="tf-date-label">
			<i class="fa fa-calendar"></i>
			<?php echo date('d M Y, l'); ?>
			<?php if ($sales_filter != "") { ?>
				&nbsp;|&nbsp; <i class="fa fa-user"></i> <?php echo htmlspecialchars(stripslashes($sales_filter)); ?>
			<?php } ?>
		</div>
	</div>

	<div class="portlet-body" style="padding:0;">
		<div class="dash-table-wrap today-followup-table-wrap">
			<table class="table table-bordered table-hover dash-table today-followup-table">
				<colgroup>
					<col style="width:4%;">
					<col style="width:13%;">
					<col style="width:10%;">
					<col style="width:8%;">
					<col style="width:10%;">
					<col style="width:11%;">
					<col style="width:18%;">
					<col style="width:7%;">
					<col style="width:9%;">
					<col style="width:8%;">
					<col style="width:7%;">
					<col style="width:5%;">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">No.</th>
						<th>Customer Name</th>
						<th>Person Name</th>
						<th>Mobile No.</th>
						<th>Sales Person</th>
						<th>Date &amp; Time</th>
						<th>Description</th>
						<th>Through</th>
						<th>Type</th>
						<th>Entry Type</th>
						<th>Status</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (!empty($rows)) {
						$count = 0;
						foreach ($rows as $item) {
							$status_class = $item['is_responded'] ? 'tf-badge-status-done' : 'tf-badge-status-pending';
							$status_label = $item['is_responded'] ? 'Responded' : 'Pending';
							?>
							<tr>
								<td class="text-center tf-col-no"><?php echo ++$count; ?></td>
								<td class="tf-col-customer"><?php echo htmlspecialchars(stripslashes($item['company_name'])); ?></td>
								<td><?php echo htmlspecialchars(stripslashes($item['person_name'])); ?></td>
								<td class="tf-col-mobile">
									<?php if (trim($item['mobile_no']) != "") { ?>
										<a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $item['mobile_no']); ?>">
											<i class="fa fa-phone"></i> <?php echo htmlspecialchars(stripslashes($item['mobile_no'])); ?>
										</a>
									<?php } else { echo "-"; } ?>
								</td>
								<td><?php echo htmlspecialchars(stripslashes($item['sales_name'])); ?></td>
								<td class="tf-col-datetime"><?php echo $item['followup_time']; ?></td>
								<td class="tf-col-desc" title="<?php echo htmlspecialchars(stripslashes($item['description'])); ?>">
									<?php echo htmlspecialchars(stripslashes($item['description'])); ?>
								</td>
								<td>
									<span class="tf-badge tf-badge-through-<?php echo $item['through_class']; ?>">
										<?php echo $item['through_label']; ?>
									</span>
								</td>
								<td><span class="tf-badge-type"><?php echo $item['type_label']; ?></span></td>
								<td><?php echo $item['entry_label']; ?></td>
								<td><span class="tf-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
								<td class="text-center">
									<a href="<?php echo $item['followup_link']; ?>" class="tf-btn-view" title="View / Response">
										<i class="fa fa-eye"></i>
									</a>
								</td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="12">
								<div class="tf-empty-state">
									<i class="fa fa-calendar-times-o"></i>
									<p>No Today's Followup found<?php echo ($sales_filter != "") ? " for <strong>" . htmlspecialchars(stripslashes($sales_filter)) . "</strong>" : " for any team member"; ?>.</p>
								</div>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php if ($total_today > 200) { ?>
			<p class="tf-table-footer">
				Showing first 200 of <?php echo (int) $total_today; ?> records.
				<a href="followuplist_manage.php?followup_type=today">View all &raquo;</a>
			</p>
		<?php } ?>
	</div>
</div>
<?php require_once 'disconnect.php'; ?>
