<?php
$page_id = 583;
$page_slug = 'page_followup';
include("connect.php");

if (!isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	exit;
}

$today = date('Y-m-d');
$ctable_where = "DATE(followup_date) = '" . $today . "' AND isDelete=0";

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

$total_today = $db->rp_getTotalRecord("followup", $ctable_where, 0);
$ctable_r = $db->rp_getData("followup", "*", $ctable_where, "followup_date ASC", 0, 50);

$through_map = array("1" => "call", "2" => "sms", "3" => "email", "4" => "Whatsapp", "5" => "Visit");
$entry_type_map = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", "4" => "Web Customer", "5" => "Sales App", "6" => "Customer App");
?>
<div class="portlet light bordered admin-dash-panel">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-phone"></i>
			<span class="caption-subject bold uppercase">Today's Followup (All Team)</span>
			<span class="badge"><?php echo (int) $total_today; ?></span>
		</div>
		<div class="actions">
			<a href="followuplist_manage.php?followup_type=today" class="btn btn-circle btn-sm">
				<i class="fa fa-list"></i> View All
			</a>
			<a href="javascript:;" onclick="$('#today-followup-data').load('today_followup_dashboard_ajax.php');" class="btn btn-circle btn-sm">
				<i class="fa fa-refresh"></i> Refresh
			</a>
		</div>
	</div>
	<div class="portlet-body" style="padding:0;">
		<div class="dash-table-wrap">
			<table class="table table-striped table-bordered table-hover dash-table">
				<thead>
					<tr>
						<th width="40">No.</th>
						<th>Customer Name</th>
						<th>Person Name</th>
						<th>Mobile No.</th>
						<th>Sales Person</th>
						<th>Followup Date &amp; Time</th>
						<th>Description</th>
						<th>Through</th>
						<th>Type</th>
						<th>Entry Type</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($ctable_r && mysqli_num_rows($ctable_r) > 0) {
						$count = 0;
						while ($row = mysqli_fetch_assoc($ctable_r)) {
							$company_name = "";
							$person_name = "";
							$mobile_no = "";
							$type_label = "";

							if ($row['reference_table'] == "sales_executive" || $row['reference_table'] == "executive") {
								$customer_id = ($row['reference_table'] == "executive") ? $row['reference_id'] : $row['visitor_id'];
								$customer_r = $db->rp_getData("executive", "company_name,cname,mobile_no1,customer_flag", "id='" . $customer_id . "'", "", 0);
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
							} else if ($row['reference_table'] == "quotation_detail") {
								$company_name = $db->rp_getValue("quotation_detail", "company_name", "id='" . $row['reference_id'] . "'", 0);
								$person_name = $db->rp_getValue("quotation_detail", "customer_name", "id='" . $row['reference_id'] . "'", 0);
							} else if ($row['reference_table'] == "customer_inquiry") {
								$company_name = $db->rp_getValue("customer_inquiry", "company_name", "id='" . $row['reference_id'] . "'", 0);
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
								$type_label = "Sales Executive";
							} else if ($row['reference_table'] == "quotation_detail") {
								$type_label = "Quotation";
							} else if ($row['reference_table'] == "executive") {
								$type_label = "Customer";
							} else if ($row['reference_table'] == "manual_invoice_import") {
								$type_label = "Manually Invoice Import";
							} else if ($row['reference_table'] == "customer_inquiry") {
								$type_label = "Customer Inquiry";
							}

							$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $row['user_id'] . "'", 0);
							$through_label = isset($through_map[$row['through']]) ? $through_map[$row['through']] : "";
							$entry_label = isset($entry_type_map[$row['entry_type']]) ? $entry_type_map[$row['entry_type']] : "";
							$followup_time = ($row['followup_date'] != "0000-00-00 00:00:00") ? date('d-m-Y h:i A', strtotime($row['followup_date'])) : "";
							?>
							<tr>
								<td><?php echo ++$count; ?></td>
								<td><strong><?php echo stripslashes($company_name); ?></strong></td>
								<td><?php echo stripslashes($person_name); ?></td>
								<td><?php echo stripslashes($mobile_no); ?></td>
								<td><?php echo stripslashes($sales_name); ?></td>
								<td><strong><?php echo $followup_time; ?></strong></td>
								<td><?php echo stripslashes($row['description']); ?></td>
								<td><span class="label-call"><?php echo $through_label; ?></span></td>
								<td><?php echo $type_label; ?></td>
								<td><?php echo $entry_label; ?></td>
							</tr>
							<?php
						}
					} else {
						?>
						<tr>
							<td colspan="10" class="no-records">No Today's Followup found for any team member.</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php if ($total_today > 50) { ?>
			<p style="padding:10px 15px;margin:0;color:#666;font-size:12px;border-top:1px solid #eee;">
				Showing latest 50 of <?php echo (int) $total_today; ?> records.
				<a href="followuplist_manage.php?followup_type=today">View all</a>
			</p>
		<?php } ?>
	</div>
</div>
<?php require_once 'disconnect.php'; ?>
