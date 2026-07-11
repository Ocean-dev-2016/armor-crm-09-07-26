<?php
$page_id = 555;
$page_slug = 'channel_partner_customer';
$ctable = "channel_partner_customer";
$ctable1 = "Channel Partner Customer";
$main_page = "channel_partner";
$page = "channel_partner_customer";
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "add";
$page_title = ucwords($mode) . " " . $ctable1;
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "channel_partner_customer_manage.php", "title" => "Channel Partner"),
	array("link" => "channel_partner_customer_crud.php", "title" => "Add/Edit " . $ctable1)
);
include("connect.php");
require_once("../include/class.channel_partner_customer.php");
$objCP = new ChannelPartnerCustomer();

$company_name = "";
$person_name = "";
$mobile_no = "";
$email = "";
$gst = "";
$country = "";
$state = "";
$city = "";
$pincode = "";
$channel_partner_id = "";

$country_r = $db->rp_getData("country", "*", "isDelete=0", "name ASC", 0);
$channel_partner_r = $db->rp_getData(
	"executive",
	"id, company_name, cname, mobile_no1",
	"channel_partner_flag=1 AND customer_flag=0 AND isDelete=0",
	"company_name ASC",
	0
);

if (isset($_REQUEST['submit'])) {
	$detail = array();
	$detail['company_name'] = $db->clean($_REQUEST['company_name']);
	$detail['person_name'] = $db->clean($_REQUEST['person_name']);
	$detail['mobile_no'] = $db->clean($_REQUEST['mobile_no']);
	$detail['email'] = $db->clean($_REQUEST['email']);
	$detail['gst'] = $db->clean($_REQUEST['gst']);
	$detail['country'] = $db->clean($_REQUEST['country']);
	$detail['state'] = $db->clean($_REQUEST['state']);
	$detail['city'] = $db->clean($_REQUEST['city']);
	$detail['pincode'] = $db->clean($_REQUEST['pincode']);
	$detail['channel_partner_id'] = $db->clean($_REQUEST['channel_partner_id']);

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {
		if ($rights['insert_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply = $objCP->InsertChannelPartnerCustomer($detail);
		if ($reply['ack'] == 1) {
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("channel_partner_customer_manage.php?msg=inserted");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	} else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") {
		if ($rights['update_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=update_access_denied');
		}
		$detail['id'] = $db->clean($_REQUEST['id']);
		$reply = $objCP->UpdateChannelPartnerCustomer($detail);
		if ($reply['ack'] == 1) {
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("channel_partner_customer_manage.php?msg=updated");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
	if ($rights['update_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	$detail = array('id' => $_REQUEST['id']);
	$reply = $objCP->GetEditDataChannelPartnerCustomer($detail);
	if ($reply['ack'] == 1) {
		extract($reply['result']);
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") {
	if ($rights['delete_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail = array('id' => $_REQUEST['id']);
	$reply = $objCP->DeleteChannelPartnerCustomer($detail);
	if ($reply['ack'] == 1) {
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("channel_partner_customer_manage.php?msg=deleted");
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="channel_partner_customer_manage.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?php $db->printErrorMessage(); ?>
					<?php $db->printSuccessMessage(); ?>
				</div>
			</div>
			<form role="form" action="" onSubmit="return check_form();" method="post">
				<input type="hidden" name="mode" value="<?php echo isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 'add'; ?>">
				<?php if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0) { ?>
					<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
				<?php } ?>
				<div class="row">
					<div class="col-md-8">
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption"><i class="fa fa-user"></i> Channel Partner Customer Details</div>
							</div>
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Select Channel Partner <code>*</code></label>
												<select class="form-control input-medium" name="channel_partner_id" id="channel_partner_id" style="width:100%;">
													<option value="">-- Select Channel Partner --</option>
													<?php
													if ($channel_partner_r) {
														while ($cp_d = mysqli_fetch_assoc($channel_partner_r)) {
															$cp_label = trim($cp_d['company_name']);
															if ($cp_d['cname'] != "") {
																$cp_label .= " - " . $cp_d['cname'];
															}
															if ($cp_d['mobile_no1'] != "") {
																$cp_label .= " (" . $cp_d['mobile_no1'] . ")";
															}
															$selected = ((string) $channel_partner_id === (string) $cp_d['id']) ? "selected" : "";
															echo '<option value="' . (int) $cp_d['id'] . '" ' . $selected . '>' . htmlentities($cp_label) . '</option>';
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Customer Name <code>*</code></label>
												<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Person Name <code>*</code></label>
												<input type="text" class="form-control" name="person_name" id="person_name" value="<?php echo $person_name; ?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Mobile No <code>*</code></label>
												<input type="text" class="form-control" name="mobile_no" id="mobile_no" maxlength="15" value="<?php echo $mobile_no; ?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Email</label>
												<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>GST</label>
												<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $gst; ?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Country <code>*</code></label>
												<select class="form-control" name="country" id="country" onChange="State(this.value);">
													<option value="">-- Select Country --</option>
													<?php
													if ($country_r) {
														while ($country_d = mysqli_fetch_assoc($country_r)) {
															$isSelected = false;
															if ($mode == 'add' && $country_d['name'] == 'India') {
																$isSelected = true;
															} else if ($country != "" && $country_d['name'] == $country) {
																$isSelected = true;
															}
															$selected = $isSelected ? "selected" : "";
															echo '<option value="' . htmlentities($country_d['name']) . '" ' . $selected . '>' . htmlentities($country_d['name']) . '</option>';
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>State <code>*</code></label>
												<select class="form-control" name="state" id="state" onChange="city_data(this.value);">
													<option value="">-- Select State --</option>
													<?php
													if ($mode == 'edit') {
														$state_r = $db->rp_getData("class", "*", "isDelete=0", "name ASC", 0);
														if ($state_r) {
															while ($state_d = mysqli_fetch_assoc($state_r)) {
																$selected = (strtolower($state_d['name']) == strtolower($state)) ? "selected" : "";
																echo '<option value="' . htmlentities($state_d['name']) . '" ' . $selected . '>' . htmlentities($state_d['name']) . '</option>';
															}
														}
													}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>City <code>*</code></label>
												<select class="form-control" name="city" id="city">
													<option value="">-- Select City --</option>
													<?php
													if ($mode == 'edit' && $city != "" && $state != "") {
														$state_id = $db->rp_getValue("class", "id", "name='" . $db->clean($state) . "' AND isDelete=0", 0);
														if ($state_id) {
															$city_r = $db->rp_getData("city", "*", "state_id='" . $state_id . "' AND isDelete=0", "name ASC", 0);
															if ($city_r) {
																while ($city_d = mysqli_fetch_assoc($city_r)) {
																	$selected = (strtolower($city_d['name']) == strtolower($city)) ? "selected" : "";
																	echo '<option value="' . htmlentities($city_d['name']) . '" ' . $selected . '>' . htmlentities($city_d['name']) . '</option>';
																}
															}
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Pincode</label>
												<input type="text" class="form-control" name="pincode" id="pincode" maxlength="10" value="<?php echo $pincode; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<button type="submit" name="submit" class="btn green">Submit</button>
									<button type="button" class="btn btn-default" onClick="window.location.href='channel_partner_customer_manage.php'">Back</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
function check_form() {
	var valid = true;
	if ($("#channel_partner_id").val() === '' || $("#channel_partner_id").val() === '0') {
		alert("Please select Channel Partner.");
		valid = false;
	}
	if ($("#company_name").val().replace(/\s/g, '') === '') {
		alert("Please enter Customer Name.");
		valid = false;
	}
	if ($("#person_name").val().replace(/\s/g, '') === '') {
		alert("Please enter Person Name.");
		valid = false;
	}
	if ($("#mobile_no").val().replace(/\s/g, '') === '') {
		alert("Please enter Mobile No.");
		valid = false;
	}
	if ($("#country").val() === '') {
		alert("Please select Country.");
		valid = false;
	}
	if ($("#state").val() === '') {
		alert("Please select State.");
		valid = false;
	}
	if ($("#city").val() === '') {
		alert("Please select City.");
		valid = false;
	}
	return valid;
}
function State(val) {
	$.ajax({
		type: "POST",
		url: "ajax_get_state.php",
		data: 'cid=' + val,
		success: function(result) {
			$("#state").html(result);
			$("#city").html('<option value="">-- Select City --</option>');
		}
	});
}

function city_data(val, city_name) {
	if (typeof city_name === 'undefined') {
		city_name = '';
	}
	$.ajax({
		type: "POST",
		url: "ajax_get_main_city.php",
		data: 'sid=' + val + '&city=' + city_name,
		success: function(result) {
			$("#city").html(result);
		}
	});
}

$(document).ready(function() {
	var mode = "<?php echo $mode; ?>";
	if (mode === 'add') {
		State('India');
	} else if (mode === 'edit') {
		var editState = "<?php echo addslashes($state); ?>";
		var editCity = "<?php echo addslashes($city); ?>";
		if (editState !== '') {
			city_data(editState, editCity);
		}
	}
});
$("#mobile_no, #pincode").keyup(function(event) {
	if (event.keyCode != 46 && event.keyCode != 8 && /\D/g.test(this.value)) {
		this.value = this.value.replace(/\D/g, '');
	}
});
</script>
</body>
</html>
