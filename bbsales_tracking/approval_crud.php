<?php
$page_id = 674;
$page_slug = 'approval_entry';
$ctable = 'approval_entry';
$ctable1 = 'Approval';
$main_page = 'approval_entry';
$page = 'manage_approval_entry';
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add';
$page_title = ucwords($mode) . ' Approval';
$page_hierarchy = array(
	array('link' => '', 'title' => 'Approval'),
	array('link' => 'approval_manage.php', 'title' => 'Manage Approval'),
	array('link' => 'approval_crud.php?mode=' . $mode, 'title' => $page_title),
);
include('connect.php');
require_once('../include/approval_entry_helper.php');
armor_approval_require_unlock(false);
armor_approval_entry_ensure_table($db);

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$entry_date = date('Y-m-d');
$customer_id = 0;
$company_name = '';
$type_of_company = '';
$person_name = '';
$designation = '';
$mobile = '';
$email = '';
$amount = '';
$given_by = '';
$payment_mode = 'Cash';
$approval_status = 'Pending';
$attachment = '';
$project_name = '';
$project_builder = '';
$contractor = '';
$created_date = '';

if ($mode === 'edit' && $id > 0) {
	$row = $db->rp_getData($ctable, '*', "id='" . $id . "' AND isDelete=0", '', 0);
	if ($row && ($d = mysqli_fetch_assoc($row))) {
		$entry_date = $d['entry_date'];
		$customer_id = (int)$d['customer_id'];
		$company_name = $d['company_name'];
		$type_of_company = $d['type_of_company'];
		$person_name = $d['person_name'];
		$designation = $d['designation'];
		$mobile = $d['mobile'];
		$email = $d['email'];
		$amount = $d['amount'];
		$given_by = $d['given_by'];
		$payment_mode = $d['payment_mode'] ? $d['payment_mode'] : 'Cash';
		$approval_status = $d['approval_status'] ? $d['approval_status'] : 'Pending';
		$attachment = isset($d['attachment']) ? $d['attachment'] : '';
		$project_name = $d['project_name'];
		$project_builder = $d['project_builder'];
		$contractor = $d['contractor'];
		$created_date = !empty($d['created_date']) ? $d['created_date'] : '';
	} else {
		$db->addErrorMessage('Record not found.');
		$db->rp_location('approval_manage.php');
	}
}

if (isset($_REQUEST['submit'])) {
	$entry_date = trim($_REQUEST['entry_date']);
	$customer_id = (int)$_REQUEST['customer_id'];
	$type_of_company = trim($_REQUEST['type_of_company']);
	$person_name = trim($_REQUEST['person_name']);
	$designation = trim($_REQUEST['designation']);
	$mobile = trim($_REQUEST['mobile']);
	$email = trim($_REQUEST['email']);
	$amount = trim($_REQUEST['amount']);
	$given_by = trim($_REQUEST['given_by']);
	$payment_mode = trim($_REQUEST['payment_mode']);
	$approval_status = trim($_REQUEST['approval_status']);
	$project_name = trim($_REQUEST['project_name']);
	$project_builder = trim($_REQUEST['project_builder']);
	$contractor = trim($_REQUEST['contractor']);

	$company_name = '';
	if ($customer_id > 0) {
		$company_name = $db->rp_getValue('executive', 'company_name', "id='" . $customer_id . "' AND isDelete=0", 0);
	}
	if ($company_name == '' && isset($_REQUEST['company_name_text'])) {
		$company_name = trim($_REQUEST['company_name_text']);
	}

	$upload = armor_approval_handle_attachment_upload('attachment');
	if (!$upload['ok']) {
		$db->addErrorMessage($upload['error']);
	} elseif ($customer_id <= 0) {
		$db->addErrorMessage('Please select Company Name (Customer).');
	} elseif ($entry_date == '') {
		$db->addErrorMessage('Please select Due Date.');
	} else {
		$now = date('Y-m-d H:i:s');
		$amount_val = ($amount === '') ? null : (float)$amount;
		$new_attachment = $upload['filename'];
		$reminder_date = armor_approval_calc_reminder_date($entry_date);

		if ($mode === 'add') {
			if (empty($rights['insert_flag']) && (int)$_SESSION[SITE_SESS . '_ADMIN_TYPE'] !== 0) {
				$db->addErrorMessage('You do not have add permission.');
			} else {
				$rows = array(
					'entry_date', 'customer_id', 'company_name', 'type_of_company', 'person_name',
					'designation', 'mobile', 'email', 'amount', 'given_by', 'payment_mode',
					'reminder_date', 'reminder_notified', 'approval_status', 'attachment', 'project_name', 'project_builder', 'contractor',
					'isDelete', 'created_by', 'created_date'
				);
				$values = array(
					$entry_date, $customer_id, $company_name, $type_of_company, $person_name,
					$designation, $mobile, $email, $amount_val, $given_by, $payment_mode,
					$reminder_date, 0, $approval_status, $new_attachment, $project_name, $project_builder, $contractor,
					0, (int)$_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], $now
				);
				$ins = $db->rp_insert($ctable, $values, $rows, 0);
				if ($ins) {
					if ($reminder_date && $reminder_date <= date('Y-m-d')) {
						$row = $db->rp_getData($ctable, '*', "id='" . (int)$ins . "'", '', 0);
						if ($row && ($rr = mysqli_fetch_assoc($row))) {
							armor_approval_create_reminder_notification($db, $rr);
						}
					}
					$db->addSuccessMessage('Approval entry added successfully.');
					$db->rp_location('approval_manage.php');
				} else {
					$db->addErrorMessage('Failed to save approval entry.');
				}
			}
		} elseif ($mode === 'edit' && $id > 0) {
			if (empty($rights['update_flag']) && (int)$_SESSION[SITE_SESS . '_ADMIN_TYPE'] !== 0) {
				$db->addErrorMessage('You do not have edit permission.');
			} else {
				$rows = array(
					'entry_date' => $entry_date,
					'customer_id' => $customer_id,
					'company_name' => $company_name,
					'type_of_company' => $type_of_company,
					'person_name' => $person_name,
					'designation' => $designation,
					'mobile' => $mobile,
					'email' => $email,
					'amount' => $amount_val,
					'given_by' => $given_by,
					'payment_mode' => $payment_mode,
					'reminder_date' => $reminder_date,
					'reminder_notified' => 0,
					'approval_status' => $approval_status,
					'project_name' => $project_name,
					'project_builder' => $project_builder,
					'contractor' => $contractor,
					'modified_date' => $now,
				);
				if ($new_attachment !== '') {
					if ($attachment != '' && file_exists(armor_approval_upload_dir() . $attachment)) {
						@unlink(armor_approval_upload_dir() . $attachment);
					}
					$rows['attachment'] = $new_attachment;
					$attachment = $new_attachment;
				}
				$db->rp_update($ctable, $rows, "id='" . $id . "'");
				if ($reminder_date && $reminder_date <= date('Y-m-d')) {
					$row = $db->rp_getData($ctable, '*', "id='" . $id . "' AND isDelete=0", '', 0);
					if ($row && ($rr = mysqli_fetch_assoc($row))) {
						armor_approval_create_reminder_notification($db, $rr);
					}
				}
				$db->addSuccessMessage('Approval entry updated successfully.');
				$db->rp_location('approval_manage.php');
			}
		}
	}
}

$customers = $db->rp_getData(
	'executive',
	'id, company_name, cname, type_of_executive, type_of_company, mobile_no1, email, phone',
	'isDelete=0 AND company_name IS NOT NULL AND company_name!=""',
	'company_name ASC',
	0
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include('include_css.php'); ?>
<link href="assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
/* Match project Metronic: portlet blue + default select2 (include_js) */
.approval-crud-page .portlet.box.blue { margin-bottom: 20px; }
.approval-form .form-body { padding: 20px; }
.approval-form .form-group { margin-bottom: 18px; }
.approval-form .form-group label {
	font-weight: 600;
	color: #333;
	margin-bottom: 6px;
	display: block;
}
.approval-form .form-group label code {
	color: #e7505a;
	background: transparent;
	padding: 0;
}
.approval-form .form-control {
	width: 100%;
}
.approval-form textarea.form-control {
	min-height: 70px;
	resize: vertical;
}
.approval-form input[type="file"].form-control {
	height: auto;
	padding: 6px 10px;
	line-height: 22px;
}
.approval-form .help-block {
	margin: 5px 0 0;
	color: #888;
	font-size: 12px;
}
.approval-section {
	border: 1px solid #e1e5ec;
	margin-bottom: 18px;
	background: #fff;
}
.approval-section-title {
	background: #3598dc;
	color: #fff;
	font-size: 14px;
	font-weight: 600;
	padding: 10px 15px;
	margin: 0;
}
.approval-section-title i { margin-right: 6px; color: #fff; }
.approval-section-body {
	padding: 18px 15px 5px;
}
.approval-form .form-actions {
	background-color: #f5f6f8;
	border-top: 1px solid #e7ecf1;
	padding: 15px 20px;
	margin: 0;
}
.approval-form .form-actions .btn { margin-right: 6px; }
.approval-form .select2-container { width: 100% !important; }
.approval-attach-current { margin-top: 8px; }
</style>
</head>
<body class="page-md">
<?php include('header.php'); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="approval_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i></a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content approval-crud-page">
		<div class="container">
			<?php $db->getMessageBlock(); ?>
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-edit"></i> <?php echo $page_title; ?></div>
				</div>
				<div class="portlet-body form approval-form">
					<form method="post" action="" enctype="multipart/form-data" role="form">
						<input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
						<?php if ($id > 0) { ?><input type="hidden" name="id" value="<?php echo (int)$id; ?>"><?php } ?>
						<div class="form-body">

							<div class="approval-section">
								<div class="approval-section-title"><i class="fa fa-building-o"></i> Company &amp; Contact Details</div>
								<div class="approval-section-body">
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Due Date <code>*</code></label>
												<input type="date" class="form-control" name="entry_date" id="entry_date" value="<?php echo htmlspecialchars($entry_date); ?>" required>
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Company Name (Customer) <code>*</code></label>
												<select class="form-control" name="customer_id" id="customer_id" style="width:100%;" required>
													<option value="">Select Customer</option>
													<?php
													if ($customers) {
														while ($c = mysqli_fetch_assoc($customers)) {
															$label = trim($c['company_name']);
															if ($c['cname'] != '') {
																$label .= ' - ' . trim($c['cname']);
															}
															$sel = ((int)$c['id'] === (int)$customer_id) ? 'selected' : '';
															echo '<option value="' . (int)$c['id'] . '" ' . $sel
																. ' data-cname="' . htmlspecialchars($c['cname'], ENT_QUOTES) . '"'
																. ' data-mobile="' . htmlspecialchars($c['mobile_no1'] ? $c['mobile_no1'] : $c['phone'], ENT_QUOTES) . '"'
																. ' data-email="' . htmlspecialchars($c['email'], ENT_QUOTES) . '"'
																. '>' . htmlspecialchars($label) . '</option>';
														}
													}
													?>
												</select>
												<input type="hidden" name="company_name_text" id="company_name_text" value="<?php echo htmlspecialchars($company_name); ?>">
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Type of Company</label>
												<input type="text" class="form-control" name="type_of_company" id="type_of_company" value="<?php echo htmlspecialchars($type_of_company); ?>" placeholder="Enter type of company">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Person Name</label>
												<input type="text" class="form-control" name="person_name" id="person_name" value="<?php echo htmlspecialchars($person_name); ?>" placeholder="Enter person name">
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Designation</label>
												<input type="text" class="form-control" name="designation" id="designation" value="<?php echo htmlspecialchars($designation); ?>" placeholder="Enter designation">
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Mobile</label>
												<input type="text" class="form-control" name="mobile" id="mobile" value="<?php echo htmlspecialchars($mobile); ?>" placeholder="Enter mobile">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Email</label>
												<input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter email">
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Amount</label>
												<input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" value="<?php echo htmlspecialchars($amount); ?>" placeholder="0.00">
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Given by</label>
												<input type="text" class="form-control" name="given_by" id="given_by" value="<?php echo htmlspecialchars($given_by); ?>" placeholder="Enter given by">
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="approval-section">
								<div class="approval-section-title"><i class="fa fa-money"></i> Payment &amp; Approval</div>
								<div class="approval-section-body">
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Payment Mode</label>
												<select class="form-control" name="payment_mode" id="payment_mode" style="width:100%;">
													<?php foreach (armor_approval_payment_modes() as $pm) {
														$sel = ($payment_mode === $pm) ? 'selected' : '';
														echo '<option value="' . htmlspecialchars($pm) . '" ' . $sel . '>' . htmlspecialchars($pm) . '</option>';
													} ?>
												</select>
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Approval</label>
												<select class="form-control" name="approval_status" id="approval_status" style="width:100%;">
													<?php foreach (armor_approval_status_options() as $st) {
														$sel = ($approval_status === $st) ? 'selected' : '';
														echo '<option value="' . htmlspecialchars($st) . '" ' . $sel . '>' . htmlspecialchars($st) . '</option>';
													} ?>
												</select>
											</div>
										</div>
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Reminder Date <small class="text-muted">(2 Months before Due Date)</small></label>
												<?php
												$remDisp = armor_approval_calc_reminder_date($entry_date);
												$remDisp = $remDisp ? date('d/M/Y', strtotime($remDisp)) : '—';
												?>
												<input type="text" class="form-control" id="reminder_date_disp" value="<?php echo htmlspecialchars($remDisp); ?>" readonly>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8 col-sm-12">
											<div class="form-group">
												<label>Attachment</label>
												<input type="file" class="form-control" name="attachment" id="attachment">
												<span class="help-block">Max 5 MB — jpg, png, pdf, doc, xls, zip</span>
												<?php if ($attachment != '') {
													$attUrl = armor_approval_attachment_url($attachment);
												?>
													<div class="approval-attach-current">
														<a href="<?php echo htmlspecialchars($attUrl); ?>" target="_blank" class="btn btn-xs blue">
															<i class="fa fa-paperclip"></i> View Current Attachment
														</a>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="approval-section">
								<div class="approval-section-title"><i class="fa fa-briefcase"></i> Project Details</div>
								<div class="approval-section-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label>Project Name</label>
												<textarea class="form-control" name="project_name" id="project_name" rows="2" placeholder="Enter project name"><?php echo htmlspecialchars($project_name); ?></textarea>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-sm-6">
											<div class="form-group">
												<label>Project Builder</label>
												<input type="text" class="form-control" name="project_builder" id="project_builder" value="<?php echo htmlspecialchars($project_builder); ?>" placeholder="Enter project builder">
											</div>
										</div>
										<div class="col-md-6 col-sm-6">
											<div class="form-group">
												<label>Contractor</label>
												<input type="text" class="form-control" name="contractor" id="contractor" value="<?php echo htmlspecialchars($contractor); ?>" placeholder="Enter contractor">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 col-sm-6">
											<div class="form-group">
												<label>Created (Entry Date)</label>
												<?php
												$createdDisp = '—';
												if ($created_date != '' && $created_date != '0000-00-00 00:00:00') {
													$createdDisp = date('d/M/Y h:i A', strtotime($created_date));
												} elseif ($mode === 'add') {
													$createdDisp = 'Will set on save';
												}
												?>
												<input type="text" class="form-control" value="<?php echo htmlspecialchars($createdDisp); ?>" readonly>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="form-actions">
							<button type="submit" name="submit" value="1" class="btn green">Submit</button>
							<a href="approval_manage.php" class="btn default">Back</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
<?php include('include_js.php'); ?>
<script type="text/javascript">
$(document).ready(function () {
	function fillFromCustomer() {
		var $opt = $('#customer_id option:selected');
		if (!$opt.length || !$opt.val()) {
			return;
		}
		$('#company_name_text').val($.trim($opt.text().split(' - ')[0]));
		if ($opt.data('cname')) {
			$('#person_name').val($opt.data('cname'));
		}
		if ($opt.data('mobile')) {
			$('#mobile').val($opt.data('mobile'));
		}
		if ($opt.data('email')) {
			$('#email').val($opt.data('email'));
		}
	}

	$('#customer_id').on('change', fillFromCustomer);

	function updateReminderFromDueDate() {
		var due = $('#entry_date').val();
		if (!due) {
			$('#reminder_date_disp').val('—');
			return;
		}
		var parts = due.split('-');
		if (parts.length !== 3) {
			return;
		}
		var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
		d.setMonth(d.getMonth() - 2);
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var dd = ('0' + d.getDate()).slice(-2);
		$('#reminder_date_disp').val(dd + '/' + months[d.getMonth()] + '/' + d.getFullYear());
	}
	$('#entry_date').on('change', updateReminderFromDueDate);
});
</script>
</body>
</html>
