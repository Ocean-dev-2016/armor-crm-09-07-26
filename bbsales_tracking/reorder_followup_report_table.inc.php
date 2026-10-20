<?php
/**
 * Clean table UI for Re-Order Followup Report only.
 * Expects vars from followuplist_get_ajax.php: $db, $ctable_r, $isFillter, $rights,
 * $item_per_page, $page_number, $get_total_rows, $total_pages, $sales_executive, date filters.
 */
$entry_type_status = array("1" => "Admin Panel", "2" => "Sales App", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
$date_filter_query = isset($date_filter_query) ? $date_filter_query : "";
$date_filter_query_1 = isset($date_filter_query_1) ? $date_filter_query_1 : "";
$sales_executive = isset($sales_executive) ? $sales_executive : "";
$get_total_rows = isset($get_total_rows) ? $get_total_rows : 0;
$total_pages = isset($total_pages) ? $total_pages : 0;
?>
<style type="text/css">
.ror-wrap { margin: 8px 0 0; }
.ror-toolbar {
	display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;
	padding: 12px 14px; margin-bottom: 10px;
	background: #f7f9fb; border: 1px solid #e1e5ea; border-radius: 4px;
}
.ror-toolbar .ror-field { min-width: 180px; max-width: 260px; }
.ror-toolbar label { display: block; font-size: 11px; font-weight: 600; color: #666; margin-bottom: 4px; text-transform: uppercase; }
.ror-toolbar .form-control { height: 34px !important; }
.ror-toolbar .btn { height: 34px; }
.ror-scroll {
	width: 100%; max-height: 62vh; overflow: auto;
	border: 1px solid #d9dee4; background: #fff;
}
.ror-table { width: 100%; min-width: 1200px; margin: 0 !important; border-collapse: separate; border-spacing: 0; }
.ror-table thead th {
	position: sticky; top: 0; z-index: 2;
	background: #2f353b !important; color: #fff !important;
	font-size: 12px; font-weight: 600; white-space: nowrap;
	padding: 10px 10px !important; vertical-align: middle !important;
	border-bottom: 2px solid #1f2428 !important;
}
.ror-table tbody td {
	padding: 8px 10px !important; vertical-align: top !important;
	font-size: 12px; border-color: #e8ecf0 !important;
	background: #fff;
}
.ror-table tbody tr:nth-child(even) td { background: #fafbfc; }
.ror-table tbody tr:hover td { background: #eef6ff; }
.ror-table .ror-nowrap { white-space: nowrap; }
.ror-table .ror-cust { min-width: 160px; max-width: 220px; font-weight: 600; }
.ror-table .ror-person { min-width: 120px; max-width: 160px; }
.ror-table .ror-response {
	min-width: 180px; max-width: 280px; width: 240px;
	white-space: normal !important;
	word-wrap: break-word; overflow-wrap: anywhere;
	word-break: break-word; line-height: 1.35;
	vertical-align: top !important;
}
.ror-table .ror-response .ror-response-txt {
	display: block; max-height: 72px; overflow: auto;
	white-space: pre-wrap; word-wrap: break-word; overflow-wrap: anywhere;
	font-size: 12px; color: #333; margin-bottom: 4px;
}
.ror-badge {
	display: inline-block; padding: 4px 10px; border-radius: 12px;
	font-size: 12px; font-weight: 700; color: #fff; line-height: 1.4;
	text-decoration: none !important; cursor: pointer;
}
a.ror-badge:hover { opacity: .9; color: #fff !important; box-shadow: 0 1px 4px rgba(0,0,0,.2); }
.ror-badge-blue { background: #3598dc; }
.ror-badge-green { background: #26C281; }
.ror-badge-orange { background: #E67E22; }
.ror-badge-gray { background: #95A5A6; }
a.ror-badge-gray { cursor: pointer; }
span.ror-badge-gray { cursor: default; }
.ror-due { font-weight: 700; color: #c0392b; white-space: nowrap; }
.ror-empty { text-align: center; padding: 40px 10px !important; color: #888; }
.ror-footer { margin-top: 10px; }
.ror-table tbody td { vertical-align: middle !important; }
.ror-view-tabs { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
.ror-view-tabs .ror-tab {
	display:inline-block; padding:8px 14px; border-radius:4px; border:1px solid #cfd6de;
	background:#fff; color:#333; font-weight:600; font-size:13px; text-decoration:none !important; cursor:pointer;
}
.ror-view-tabs .ror-tab.active { background:#2f353b; color:#fff; border-color:#2f353b; }
.ror-view-tabs .ror-tab.ror-tab-quo.active { background:#E67E22; border-color:#E67E22; }
.ror-view-note { margin:0 0 10px; color:#666; font-size:12px; }
.ror-quo-amt {
	margin-top: 4px; font-size: 12px; font-weight: 700; color: #c0392b; white-space: nowrap;
}
.ror-value-bar {
	margin: 0 0 10px; padding: 8px 12px; background: #fff7ed; border: 1px solid #f0d2b0;
	border-radius: 4px; font-size: 13px; color: #7a4a12;
}
</style>
<?php
if (!isset($ror_view) || ($ror_view !== 'quotation' && $ror_view !== 'reorder')) {
	$ror_view = (isset($_REQUEST['ror_view']) && $_REQUEST['ror_view'] === 'quotation') ? 'quotation' : 'reorder';
}
$is_quo_view = ($ror_view === 'quotation');
?>
<div class="ror-wrap">
	<div class="ror-view-tabs">
		<a href="javascript:;" class="ror-tab <?php echo !$is_quo_view ? 'active' : ''; ?>" data-ror-view="reorder">Re-Order Followup</a>
		<a href="javascript:;" class="ror-tab ror-tab-quo <?php echo $is_quo_view ? 'active' : ''; ?>" data-ror-view="quotation">Quotation Pending <small>(No Re-Order)</small></a>
	</div>
	<p class="ror-view-note">
		<?php if ($is_quo_view) { ?>
			Showing customers who have <b>quotation not converted to order</b>, and do <b>not</b> have any Re-Order followup sent to BRM.
		<?php } else { ?>
			Showing Admin <b>Re-Order</b> followups already sent to BRM. Due date = Created + 20 days.
		<?php } ?>
	</p>
	<?php if ($is_quo_view) {
		if (!isset($ror_quo_value_total)) {
			$ror_quo_value_total = 0;
		}
	?>
	<div class="ror-value-bar">
		<b>Quotation Value Total (All filtered):</b>
		&#8377; <?php echo number_format((float) $ror_quo_value_total, 2); ?>
		&nbsp;|&nbsp; <b>Customers:</b> <?php echo (int) $get_total_rows; ?>
	</div>
	<?php } ?>

	<div class="ror-toolbar">
		<div class="ror-field" style="min-width:220px;max-width:280px;">
			<label>Sales Person</label>
			<select class="form-control" name="sales_executive" id="sales_executive" onchange="searchBysalesExecutive(this.value);">
				<option value="">All Sales Person</option>
				<?php
				$executives_r = $db->rp_getData("sales_executive", "*", "isDelete=0 AND type!='service_executive' AND isActive=1 ", "name ASC", 0);
				if ($executives_r) {
					while ($executive_d = mysqli_fetch_array($executives_r)) {
						$sel = ((string) $sales_executive === (string) $executive_d['id']) ? "selected" : "";
						echo '<option value="' . htmlspecialchars($executive_d['id']) . '" ' . $sel . '>' . htmlspecialchars($executive_d['name']) . '</option>';
					}
				}
				?>
			</select>
		</div>
		<?php if (!$is_quo_view) { ?>
		<div class="ror-field" style="min-width:240px;max-width:300px;">
			<label>Due Date (Created + 20 days)</label>
			<div class="input-group">
				<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo htmlspecialchars($date_filter_query); ?>" name="df" placeholder="Select Due Date Range" type="text" style="border-radius:0;height:34px!important;">
				<span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
		<?php } ?>
		<div class="ror-field" style="min-width:240px;max-width:300px;">
			<label><?php echo $is_quo_view ? 'Quotation Date' : 'Created Date'; ?></label>
			<div class="input-group">
				<input class="form-control datetimerange-picker-input-1" id="material_request_filter_input_1" value="<?php echo htmlspecialchars($date_filter_query_1); ?>" name="df1" placeholder="<?php echo $is_quo_view ? 'Select Quotation Date Range' : 'Select Created Date Range'; ?>" type="text" style="border-radius:0;height:34px!important;">
				<span class="input-group-addon datetimerange-picker-btn-1"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
		<?php if (!$is_quo_view) { ?>
		<div class="ror-field" style="min-width:140px;max-width:180px;">
			<label>Through</label>
			<select class="form-control" id="through" name="through" onchange="searchByName();">
				<option value="">All</option>
				<option value="1" <?= ("1" == $_REQUEST['through']) ? "selected" : ""; ?>>Call</option>
				<option value="2" <?= ("2" == $_REQUEST['through']) ? "selected" : ""; ?>>Sms</option>
				<option value="3" <?= ("3" == $_REQUEST['through']) ? "selected" : ""; ?>>Email</option>
				<option value="4" <?= ("4" == $_REQUEST['through']) ? "selected" : ""; ?>>Whatsapp</option>
				<option value="5" <?= ("5" == $_REQUEST['through']) ? "selected" : ""; ?>>Visit</option>
			</select>
		</div>
		<?php } ?>
		<div>
			<?php if (!$is_quo_view) { ?>
			<button class="btn btn-success filterBtn" type="button">Filter Due Date</button>
			<?php } ?>
			<button class="btn btn-success filterBtn_1" type="button"><?php echo $is_quo_view ? 'Filter Quotation Date' : 'Filter Created'; ?></button>
		</div>
	</div>

	<div class="ror-scroll">
		<table id="datatable_1" class="table table-bordered ror-table">
			<thead>
				<tr>
					<th style="width:40px;">No.</th>
					<th>Customer</th>
					<th>Person</th>
					<th>Client Code</th>
					<th>Mobile</th>
					<th>Sales Person</th>
					<th><?php echo $is_quo_view ? 'Quotation Date' : 'Created'; ?></th>
					<?php if (!$is_quo_view) { ?>
					<th>Due Date<br><small style="font-weight:400;opacity:.85;">Created + 20 days</small></th>
					<?php } ?>
					<th>Description</th>
					<?php if (!$is_quo_view) { ?>
					<th>Through</th>
					<th>BRM Response</th>
					<?php } ?>
					<th>Sales Person Visit</th>
					<th>Sales Person Follow-up</th>
					<th>Quotation<br><small style="font-weight:400;opacity:.85;">Count + Value (Not converted)</small></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (!isset($ror_report_rows) || !is_array($ror_report_rows)) {
				$ror_report_rows = array();
				if ($isFillter && $ctable_r && mysqli_num_rows($ctable_r) > 0) {
					while ($_ror_tmp = mysqli_fetch_assoc($ctable_r)) {
						$ror_report_rows[] = $_ror_tmp;
					}
				}
			}
			if ($isFillter && !empty($ror_report_rows)) {
				$count = 0;
				foreach ($ror_report_rows as $ctable_d) {
					$company_name = "";
					$person_name = "";
					$mobile_no = "";
					$client_code = "";
					$customer_flag_text = "";
					$linked_customer_id = 0;
					$followup_flag = "customer_followup";
					$is_quo_only = !empty($ctable_d['ror_quotation_only']);

					if ($ctable_d['reference_table'] == "sales_executive") {
						$get_customer_d = mysqli_fetch_assoc($db->rp_getData("executive", "company_name,cname,mobile_no1,client_code,customer_flag", "id='" . $ctable_d['visitor_id'] . "'"));
						$company_name = $get_customer_d['company_name'];
						$person_name = $get_customer_d['cname'];
						$client_code = $get_customer_d['client_code'];
						$mobile_no = $get_customer_d['mobile_no1'];
						$linked_customer_id = (int) $ctable_d['visitor_id'];
						$followup_flag = "followup";
						if ($get_customer_d['customer_flag'] == 1) $customer_flag_text = " - P";
						else if ($get_customer_d['customer_flag'] == 0) $customer_flag_text = " - C";
					} else if ($ctable_d['reference_table'] == "quotation_detail") {
						$get_customer_d = mysqli_fetch_assoc($db->rp_getData("quotation_detail", "company_name,customer_name,client_code,customer_id,customer_flag", "id='" . $ctable_d['reference_id'] . "'"));
						$company_name = $get_customer_d['company_name'];
						$person_name = $get_customer_d['customer_name'];
						$client_code = $get_customer_d['client_code'];
						$mobile_no = $db->rp_getValue("executive", "mobile_no1", "isDelete=0 AND id = '" . $get_customer_d['customer_id'] . "'  ");
						$linked_customer_id = (int) $get_customer_d['customer_id'];
						$followup_flag = "quotation_followup";
						if ($get_customer_d['customer_flag'] == 1) $customer_flag_text = " - P";
						else if ($get_customer_d['customer_flag'] == 0) $customer_flag_text = " - C";
					} else if ($ctable_d['reference_table'] == "manual_invoice_import") {
						$customer_id = $db->rp_getValue("manually_invoice_outstanding_import", "customer_id", "id='" . $ctable_d['reference_id'] . "'");
						$get_customer_d = mysqli_fetch_assoc($db->rp_getData("executive", "company_name,cname,mobile_no1,client_code,customer_flag", "id='" . $customer_id . "'"));
						$company_name = $get_customer_d['company_name'];
						$person_name = $get_customer_d['cname'];
						$client_code = $get_customer_d['client_code'];
						$mobile_no = $get_customer_d['mobile_no1'];
						$linked_customer_id = (int) $customer_id;
						$followup_flag = "manual_invoice_import";
						if ($get_customer_d['customer_flag'] == 1) $customer_flag_text = " - P";
						else if ($get_customer_d['customer_flag'] == 0) $customer_flag_text = " - C";
					} else if ($ctable_d['reference_table'] == "no_order_inquiry") {
						$company_name = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $ctable_d['reference_id'] . "'");
						$person_name = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['reference_id'] . "'");
						$mobile_no = $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $ctable_d['reference_id'] . "'");
						$linked_customer_id = (int) $ctable_d['visitor_id'];
						$followup_flag = "inquiry_followup";
					} else if ($ctable_d['reference_table'] == "executive") {
						$get_customer_d = mysqli_fetch_assoc($db->rp_getData("executive", "company_name,cname,mobile_no1,client_code,customer_flag", "id='" . $ctable_d['reference_id'] . "'"));
						$company_name = $get_customer_d['company_name'];
						$person_name = $get_customer_d['cname'];
						$client_code = $get_customer_d['client_code'];
						$mobile_no = $get_customer_d['mobile_no1'];
						$linked_customer_id = (int) $ctable_d['reference_id'];
						$followup_flag = "customer_followup";
						if ($get_customer_d['customer_flag'] == 1) $customer_flag_text = " - P";
						else if ($get_customer_d['customer_flag'] == 0) $customer_flag_text = " - C";
					}

					$slug = "";
					if ($ctable_d['through'] == '1') $slug = "Call";
					else if ($ctable_d['through'] == '2') $slug = "Sms";
					else if ($ctable_d['through'] == '3') $slug = "Email";
					else if ($ctable_d['through'] == '4') $slug = "Whatsapp";
					else if ($ctable_d['through'] == '5') $slug = "Visit";

					$created_txt = "";
					if (!empty($ctable_d['created_date']) && $ctable_d['created_date'] != "0000-00-00 00:00:00" && $ctable_d['created_date'] != "1970-01-01 00:00:00") {
						$created_txt = $is_quo_only
							? date('d-m-Y', strtotime($ctable_d['created_date']))
							: date('d-m-Y h:i A', strtotime($ctable_d['created_date']));
					}
					$due_txt = "";
					if (!$is_quo_only && $created_txt !== "") {
						$due_ts = strtotime($ctable_d['created_date'] . " +20 days");
						if ($due_ts !== false) $due_txt = date('d-m-Y', $due_ts);
					}

					$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'");

					/* Counts only — click opens popup with full list */
					$custAttr = htmlspecialchars($company_name . $customer_flag_text, ENT_QUOTES, 'UTF-8');
					$app_visit_total = 0;
					$app_fu_total = 0;
					$quo_total = 0;
					$quo_value = 0;
					if ($linked_customer_id > 0) {
						$app_visit_total = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $linked_customer_id . "' AND entry_flag IN (2,5)", 0);
						$app_fu_where = "isDelete=0 AND entry_type IN (2,5) AND (visitor_id='" . $linked_customer_id . "' OR (reference_table='executive' AND reference_id='" . $linked_customer_id . "'))";
						$app_fu_total = (int) $db->rp_getTotalRecord("followup", $app_fu_where, 0);
						$quo_where = "isDelete=0 AND customer_id='" . $linked_customer_id . "' AND status!=-1 AND status!=4";
						$quo_total = (int) $db->rp_getTotalRecord("quotation_detail", $quo_where, 0);
						$quo_value = (float) $db->rp_getValue("quotation_detail", "SUM(grand_total)", $quo_where, 0);
					}
					if ($linked_customer_id > 0) {
						$vCls = $app_visit_total > 0 ? 'ror-badge-blue' : 'ror-badge-gray';
						$fCls = $app_fu_total > 0 ? 'ror-badge-green' : 'ror-badge-gray';
						$qCls = $quo_total > 0 ? 'ror-badge-orange' : 'ror-badge-gray';
						$app_visit_html = '<a href="javascript:;" class="ror-badge ' . $vCls . ' ror-detail-btn" data-type="visit" data-cid="' . (int) $linked_customer_id . '" data-cname="' . $custAttr . '" data-title="Sales Person Visit">Total ' . $app_visit_total . '</a>';
						$app_followup_html = '<a href="javascript:;" class="ror-badge ' . $fCls . ' ror-detail-btn" data-type="followup" data-cid="' . (int) $linked_customer_id . '" data-cname="' . $custAttr . '" data-title="Sales Person Follow-up">Total ' . $app_fu_total . '</a>';
						$quo_val_txt = number_format($quo_value, 2);
						$quo_html = '<a href="javascript:;" class="ror-badge ' . $qCls . ' ror-detail-btn" data-type="quotation" data-cid="' . (int) $linked_customer_id . '" data-cname="' . $custAttr . '" data-title="Quotation (Not converted to Order)">Total ' . $quo_total . '</a>'
							. '<div class="ror-quo-amt" title="Quotation Value Total">&#8377; ' . htmlspecialchars($quo_val_txt) . '</div>';
					} else {
						$app_visit_html = '<span class="ror-badge ror-badge-gray">Total 0</span>';
						$app_followup_html = '<span class="ror-badge ror-badge-gray">Total 0</span>';
						$quo_html = '<span class="ror-badge ror-badge-gray">Total 0</span><div class="ror-quo-amt">&#8377; 0.00</div>';
					}
			?>
				<tr>
					<td class="ror-nowrap"><?php echo ++$count; ?></td>
					<td class="ror-cust"><?php echo htmlspecialchars($company_name . $customer_flag_text); ?></td>
					<td class="ror-person"><?php echo htmlspecialchars($person_name); ?></td>
					<td class="ror-nowrap"><?php echo htmlspecialchars($client_code); ?></td>
					<td class="ror-nowrap"><?php echo htmlspecialchars($mobile_no); ?></td>
					<td class="ror-nowrap"><?php echo htmlspecialchars($sales_name); ?></td>
					<td class="ror-nowrap"><?php echo htmlspecialchars($created_txt); ?></td>
					<?php if (!$is_quo_view) { ?>
					<td class="ror-due"><?php echo htmlspecialchars($due_txt); ?></td>
					<?php } ?>
					<td class="ror-nowrap">
						<?php if ($is_quo_only || $is_quo_view) { ?>
							<span class="ror-badge ror-badge-orange"><?php echo htmlspecialchars($ctable_d['description']); ?></span>
						<?php } else { ?>
							<span class="ror-badge ror-badge-blue"><?php echo htmlspecialchars($ctable_d['description']); ?></span>
						<?php } ?>
					</td>
					<?php if (!$is_quo_view) { ?>
					<td class="ror-nowrap"><?php echo htmlspecialchars($slug); ?></td>
					<td class="ror-response">
						<?php
						if (!empty($ctable_d['response'])) {
							echo '<div class="ror-response-txt">' . htmlspecialchars($ctable_d['response']) . '</div>';
						}
						if ($ctable_d['status'] == 0) {
						?>
							<a type="button" href="#FollowupResponse" data-toggle="modal" class="btn btn-circle btn-sm yellow" data-mode="add" data-followup-flag="<?= $followup_flag; ?>" data-sales-id="<?= $ctable_d['user_id']; ?>" data-visitor_id="<?= $ctable_d['visitor_id'] ?>" data-date="<?php echo date('d-m-Y H:i:s', strtotime($ctable_d['followup_date'])); ?>" data-id="<?php echo $ctable_d['id']; ?>" data-ref_id="<?php echo $ctable_d['reference_id']; ?>" data-next_action="<?php echo $ctable_d['next_action']; ?>">Response</a>
						<?php } ?>
					</td>
					<?php } ?>
					<td><?php echo $app_visit_html; ?></td>
					<td><?php echo $app_followup_html; ?></td>
					<td><?php echo $quo_html; ?></td>
				</tr>
			<?php
				}
			} else {
				$colspan = $is_quo_view ? 11 : 14;
			?>
				<tr>
					<td colspan="<?php echo (int) $colspan; ?>" class="ror-empty">
						<?php
						if ($isFillter) {
							echo $is_quo_view
								? "No Quotation Pending (without Re-Order) found for selected filters."
								: "No Re-Order followup found for selected filters.";
						} else {
							echo FILTER_INFO;
						}
						?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="row ror-footer">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") echo ' selected="selected"'; ?>>50</option>
					<option value="100" <?php if ($_REQUEST["show"] == 100) echo ' selected="selected"'; ?>>100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) echo ' selected="selected"'; ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) echo ' selected="selected"'; ?>>1000</option>
				</select>
				&nbsp; <span class="text-muted">Total: <?php echo (int) $get_total_rows; ?></span>
				<?php if ($is_quo_view) { ?>
					&nbsp; <span class="label label-warning">Quotation Pending view</span>
				<?php } else { ?>
					&nbsp; <span class="label label-default">Re-Order Followup view</span>
				<?php } ?>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
					<?php echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	if (typeof ror_view === "undefined") { var ror_view = "reorder"; }
	ror_view = "<?php echo $is_quo_view ? 'quotation' : 'reorder'; ?>";

	$("#sales_executive").select2({ width: "100%" });
	if ($("#through").length) {
		$("#through").select2({ width: "100%" });
	}

	$(".ror-view-tabs").off("click.rorTab").on("click.rorTab", ".ror-tab", function (e) {
		e.preventDefault();
		ror_view = $(this).attr("data-ror-view") || "reorder";
		df = "";
		df1 = "";
		through = "";
		displayRecords($("#numRecords").val() || 50, 1);
	});

	$(".filterBtn").on("click", function () {
		df = $("#material_request_filter_input").val();
		df = encodeURI(df);
		df1 = "";
		displayRecords(100, 1);
	});
	$(".filterBtn_1").on("click", function () {
		df1 = $("#material_request_filter_input_1").val();
		df1 = encodeURI(df1);
		df = "";
		displayRecords(100, 1);
	});
	$(".datetimerange-picker-btn").on("click", function () {
		$("#material_request_filter_input").focus();
	});
	$(".datetimerange-picker-btn-1").on("click", function () {
		$("#material_request_filter_input_1").focus();
	});

	$(".datetimerange-picker-input, .datetimerange-picker-input-1").daterangepicker({
		format: "dd-mm-yy ",
		autoUpdateInput: false,
		timePicker: false,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function (ev, picker) {
		$(this).val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});
	$('.datetimerange-picker-input-1').on('apply.daterangepicker', function (ev, picker) {
		$(this).val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});
</script>
<?php require_once("disconnect.php"); ?>
