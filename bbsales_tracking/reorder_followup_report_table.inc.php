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
.ror-table { width: 100%; min-width: 1400px; margin: 0 !important; border-collapse: separate; border-spacing: 0; }
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
.ror-badge {
	display: inline-block; padding: 2px 8px; border-radius: 10px;
	font-size: 11px; font-weight: 700; color: #fff; line-height: 1.4;
}
.ror-badge-blue { background: #3598dc; }
.ror-badge-green { background: #26C281; }
.ror-badge-orange { background: #E67E22; }
.ror-badge-gray { background: #95A5A6; }
.ror-app-box {
	min-width: 230px; max-width: 280px; max-height: 96px;
	overflow-y: auto; overflow-x: hidden;
	border: 1px solid #e5e9ef; border-radius: 4px;
	background: #fcfdff; padding: 6px 8px;
}
.ror-app-item {
	padding: 4px 0; border-bottom: 1px dashed #e6eaf0;
	line-height: 1.35; font-size: 11px;
}
.ror-app-item:last-child { border-bottom: 0; }
.ror-app-item b { color: #2f353b; }
.ror-app-item .muted { color: #888; }
.ror-due { font-weight: 700; color: #c0392b; white-space: nowrap; }
.ror-empty { text-align: center; padding: 40px 10px !important; color: #888; }
.ror-footer { margin-top: 10px; }
</style>

<div class="ror-wrap">
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
		<div class="ror-field" style="min-width:240px;max-width:300px;">
			<label>Due Date (Created + 20 days)</label>
			<div class="input-group">
				<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo htmlspecialchars($date_filter_query); ?>" name="df" placeholder="Select Due Date Range" type="text" style="border-radius:0;height:34px!important;">
				<span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
		<div class="ror-field" style="min-width:240px;max-width:300px;">
			<label>Created Date</label>
			<div class="input-group">
				<input class="form-control datetimerange-picker-input-1" id="material_request_filter_input_1" value="<?php echo htmlspecialchars($date_filter_query_1); ?>" name="df1" placeholder="Select Created Date Range" type="text" style="border-radius:0;height:34px!important;">
				<span class="input-group-addon datetimerange-picker-btn-1"><i class="fa fa-calendar"></i></span>
			</div>
		</div>
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
		<div>
			<button class="btn btn-success filterBtn" type="button">Filter Due Date</button>
			<button class="btn btn-success filterBtn_1" type="button">Filter Created</button>
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
					<th>Created</th>
					<th>Due Date<br><small style="font-weight:400;opacity:.85;">Created + 20 days</small></th>
					<th>Description</th>
					<th>Through</th>
					<th>BRM Response</th>
					<th>Sales Person Visit</th>
					<th>Sales Person Follow-up</th>
					<th>Quotation<br><small style="font-weight:400;opacity:.85;">Not converted to Order</small></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($isFillter && $ctable_r && mysqli_num_rows($ctable_r) > 0) {
				$count = 0;
				while ($ctable_d = mysqli_fetch_array($ctable_r)) {
					$company_name = "";
					$person_name = "";
					$mobile_no = "";
					$client_code = "";
					$customer_flag_text = "";
					$linked_customer_id = 0;
					$followup_flag = "customer_followup";

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
						$created_txt = date('d-m-Y h:i A', strtotime($ctable_d['created_date']));
					}
					$due_txt = "";
					if ($created_txt !== "") {
						$due_ts = strtotime($ctable_d['created_date'] . " +20 days");
						if ($due_ts !== false) $due_txt = date('d-m-Y', $due_ts);
					}

					$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'");

					/* App Visit compact */
					$app_visit_html = '<span class="ror-badge ror-badge-gray">0</span>';
					$app_followup_html = '<span class="ror-badge ror-badge-gray">0</span>';
					if ($linked_customer_id > 0) {
						$app_visit_total = (int) $db->rp_getTotalRecord("visit", "isDelete=0 AND customer_id='" . $linked_customer_id . "' AND entry_flag IN (2,5)", 0);
						$app_visit_r = $db->rp_getData("visit", "id,start_date_time,remark,user_id", "isDelete=0 AND customer_id='" . $linked_customer_id . "' AND entry_flag IN (2,5)", "id DESC", 0, "0,3");
						$lines = array();
						if ($app_visit_r && mysqli_num_rows($app_visit_r) > 0) {
							while ($av = mysqli_fetch_assoc($app_visit_r)) {
								$av_date = (!empty($av['start_date_time']) && $av['start_date_time'] != "0000-00-00 00:00:00")
									? date('d-m-Y h:i A', strtotime($av['start_date_time'])) : "-";
								$av_by = $db->rp_getValue("sales_executive", "name", "id='" . $av['user_id'] . "'", 0);
								$lines[] = '<div class="ror-app-item"><b>' . htmlspecialchars($av_date) . '</b>'
									. ($av_by ? ' <span class="muted">(' . htmlspecialchars($av_by) . ')</span>' : '')
									. '</div>';
							}
						}
						$app_visit_html = '<div><span class="ror-badge ror-badge-blue">Total ' . $app_visit_total . '</span></div>';
						if (!empty($lines)) {
							$app_visit_html .= '<div class="ror-app-box" style="margin-top:6px;">' . implode('', $lines)
								. ($app_visit_total > 3 ? '<div class="ror-app-item muted">+' . ($app_visit_total - 3) . ' more</div>' : '')
								. '</div>';
						}

						$app_fu_where = "isDelete=0 AND entry_type IN (2,5) AND (visitor_id='" . $linked_customer_id . "' OR (reference_table='executive' AND reference_id='" . $linked_customer_id . "'))";
						$app_fu_total = (int) $db->rp_getTotalRecord("followup", $app_fu_where, 0);
						$app_fu_r = $db->rp_getData("followup", "id,followup_date,description,through,user_id", $app_fu_where, "followup_date DESC", 0, "0,3");
						$lines = array();
						if ($app_fu_r && mysqli_num_rows($app_fu_r) > 0) {
							while ($af = mysqli_fetch_assoc($app_fu_r)) {
								$af_date = (!empty($af['followup_date']) && $af['followup_date'] != "0000-00-00 00:00:00")
									? date('d-m-Y h:i A', strtotime($af['followup_date'])) : "-";
								$af_by = $db->rp_getValue("sales_executive", "name", "id='" . $af['user_id'] . "'", 0);
								$af_through = "";
								if ($af['through'] == '1') $af_through = "Call";
								else if ($af['through'] == '2') $af_through = "Sms";
								else if ($af['through'] == '3') $af_through = "Email";
								else if ($af['through'] == '4') $af_through = "Whatsapp";
								else if ($af['through'] == '5') $af_through = "Visit";
								$af_desc = trim(strip_tags((string) $af['description']));
								if (strlen($af_desc) > 40) $af_desc = substr($af_desc, 0, 40) . "...";
								$lines[] = '<div class="ror-app-item"><b>' . htmlspecialchars($af_date) . '</b>'
									. ($af_through ? ' · ' . htmlspecialchars($af_through) : '')
									. ($af_by ? ' <span class="muted">(' . htmlspecialchars($af_by) . ')</span>' : '')
									. ($af_desc !== '' ? '<br><span class="muted">' . htmlspecialchars($af_desc) . '</span>' : '')
									. '</div>';
							}
						}
						$app_followup_html = '<div><span class="ror-badge ror-badge-green">Total ' . $app_fu_total . '</span></div>';
						if (!empty($lines)) {
							$app_followup_html .= '<div class="ror-app-box" style="margin-top:6px;">' . implode('', $lines)
								. ($app_fu_total > 3 ? '<div class="ror-app-item muted">+' . ($app_fu_total - 3) . ' more</div>' : '')
								. '</div>';
						}
					}

					/* Same customer: quotations not converted to order (status 4 = Order Generated, -1 = cart) */
					$quo_html = '<span class="ror-badge ror-badge-gray">0</span>';
					if ($linked_customer_id > 0) {
						$quo_where = "isDelete=0 AND customer_id='" . $linked_customer_id . "' AND status!=-1 AND status!=4";
						$quo_total = (int) $db->rp_getTotalRecord("quotation_detail", $quo_where, 0);
						$quo_r = $db->rp_getData(
							"quotation_detail",
							"id,quotation_no,quotation_date,status,grand_total",
							$quo_where,
							"id DESC",
							0,
							"0,5"
						);
						$quo_status_lbl = array(
							"0" => "Pending",
							"1" => "Approved",
							"3" => "Cancelled",
							"5" => "Lost",
							"-2" => "Disapproved",
						);
						$qLines = array();
						if ($quo_r && mysqli_num_rows($quo_r) > 0) {
							while ($qq = mysqli_fetch_assoc($quo_r)) {
								$qNo = !empty($qq['quotation_no']) ? $qq['quotation_no'] : ("QT/" . $qq['id']);
								$qDate = (!empty($qq['quotation_date']) && $qq['quotation_date'] != "0000-00-00")
									? date('d-m-Y', strtotime($qq['quotation_date'])) : "-";
								$qSt = isset($quo_status_lbl[(string) $qq['status']])
									? $quo_status_lbl[(string) $qq['status']]
									: ("Status " . $qq['status']);
								$qAmt = isset($qq['grand_total']) ? number_format((float) $qq['grand_total'], 2) : "0.00";
								$qLines[] = '<div class="ror-app-item"><b>'
									. htmlspecialchars($qNo) . '</b> · ' . htmlspecialchars($qDate)
									. '<br><span class="muted">' . htmlspecialchars($qSt) . ' · ' . htmlspecialchars($qAmt) . '</span>'
									. ' <a href="quotation_viewer.php?quotation_id=' . (int) $qq['id'] . '" target="_blank" title="Open">View</a>'
									. '</div>';
							}
						}
						$quo_html = '<div><span class="ror-badge ror-badge-orange">Total ' . $quo_total . '</span></div>';
						if (!empty($qLines)) {
							$quo_html .= '<div class="ror-app-box" style="margin-top:6px;">' . implode('', $qLines)
								. ($quo_total > 5 ? '<div class="ror-app-item muted">+' . ($quo_total - 5) . ' more</div>' : '')
								. '</div>';
						} else if ($quo_total == 0) {
							$quo_html .= '<div class="muted" style="margin-top:4px;font-size:11px;">None pending</div>';
						}
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
					<td class="ror-due"><?php echo htmlspecialchars($due_txt); ?></td>
					<td class="ror-nowrap"><span class="ror-badge ror-badge-blue"><?php echo htmlspecialchars($ctable_d['description']); ?></span></td>
					<td class="ror-nowrap"><?php echo htmlspecialchars($slug); ?></td>
					<td class="ror-nowrap">
						<?php
						if (!empty($ctable_d['response'])) {
							echo htmlspecialchars($ctable_d['response']);
						}
						if ($ctable_d['status'] == 0) {
						?>
							<a type="button" href="#FollowupResponse" data-toggle="modal" class="btn btn-circle btn-sm yellow" data-mode="add" data-followup-flag="<?= $followup_flag; ?>" data-sales-id="<?= $ctable_d['user_id']; ?>" data-visitor_id="<?= $ctable_d['visitor_id'] ?>" data-date="<?php echo date('d-m-Y H:i:s', strtotime($ctable_d['followup_date'])); ?>" data-id="<?php echo $ctable_d['id']; ?>" data-ref_id="<?php echo $ctable_d['reference_id']; ?>" data-next_action="<?php echo $ctable_d['next_action']; ?>">Response</a>
						<?php } ?>
					</td>
					<td><?php echo $app_visit_html; ?></td>
					<td><?php echo $app_followup_html; ?></td>
					<td><?php echo $quo_html; ?></td>
				</tr>
			<?php
				}
			} else {
			?>
				<tr>
					<td colspan="14" class="ror-empty">
						<?php echo $isFillter ? "No Re-Order followup found for selected filters." : FILTER_INFO; ?>
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
	$("#sales_executive").select2({ width: "100%" });
	$("#through").select2({ width: "100%" });

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
