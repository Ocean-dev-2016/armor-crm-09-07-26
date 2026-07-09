<?php
if (isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id'] > 0 && $_REQUEST['mode'] == "add") {

	$inquiry = array();
	$where = " id='" . $_REQUEST['inquiry_id'] . "' AND isDelete=0";
	$inquiry_r = $db->rp_getData("no_order_inquiry", "*", $where, "", 0);
	if ($inquiry_d = mysqli_fetch_assoc($inquiry_r)) {
		$cname = $inquiry_d['person_name'];
		$phone = $inquiry_d['mobile_number'];
		$company_name = $inquiry_d['company_name'];
		$address = $inquiry_d['address'];
		$address2 = $inquiry_d['address2'];
		//$dealer_distributor_id = $inquiry_d['dealer_id'];
		$dealer_distributor_id = $inquiry_d['distributor_id'];
		$country = $inquiry_d['country'];
		$state = $inquiry_d['state'];
		$city = $inquiry_d['city'];
		//$inquiry[] = $inquiry_d;
	}
}
?>
<form id="inquiry_form_distributor" onSubmit="return check_form();" action="executive_form_function.php" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6 col-sm-12">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Sub Dealer Information
					</div>
				</div>
				<div class="portlet-body">

					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<label>Select Company <code>*</code></label>
									<select class="form-control" id="type_of_company" name="type_of_company">
										<option value="">Select Company</option>
										<?php
										$company_r = $db->rp_getData("company_master", "*", "isDelete=0");
										if ($company_r) {
											while ($company_d = mysqli_fetch_assoc($company_r)) {
										?>
												<option <?= ($type_of_company == $company_d['id']) ? "selected" : ""; ?> value="<?= $company_d['id']; ?>"><?= $company_d['name']; ?></option>
										<?php
											}
										}
										?>
									</select>
									<p class="help-block"></p>
								</div>
								<div class="col-md-6">

									<div class="form-group">
										<label for="address">CP <code>*</code></label>
										<select class="form-control" name="super_stockist_id" id="super_stockist" onChange="getDealerDistributor(this.value);">
											<option value="">Select CP</option>
											<?php
											$ss_d = $db->rp_getData('executive', "*", "1=1 AND type_of_executive='1' AND isDelete=0 AND isActive=1", "", 0);
											while ($ss_r = mysqli_fetch_assoc($ss_d)) {
											?>
												<option <?php echo ($super_stockist_id == $ss_r['id']) ? "selected" : ""; ?> value="<?php echo $ss_r['id'] ?>">
													<?php echo $ss_r['company_name'] . "-" . $ss_r['cname']; ?>
												</option>
											<?php
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="address">Dealer <code>*</code> </label>
										<select class="form-control" name="dealer_distributor_id" id="dealer_distributor_id" onChange="getClass(this.value);">
											<option value="">Select Distributor</option>
											<?php
											if ($mode == 'edit') {
												$dds = $db->rp_getData("executive", "*", "type_of_executive='2' AND isDelete=0 AND isActive=1 AND super_stockist_id='" . $super_stockist_id . "'", 0);
												if ($dds) {
													while ($dd = mysqli_fetch_assoc($dds)) {
											?>
														<option value="<?php echo $dd['id']; ?>" <?php echo ($dd['id'] == $dealer_distributor_id) ? "Selected" : ""; ?>> <?php echo $dd['company_name']; ?> </option>
											<?php
													}
												}
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<label>Select Sales Officer </label>
									<select class="form-control" id="seid" name="seid">
										<option value="">Select Sales Officer</option>
										<?php
										$sales_executive_r = $db->rp_getData("sales_executive", "*", "isDelete=0 AND isActive=1", "");
										if ($sales_executive_r) {
											while ($sales_executive_d = mysqli_fetch_assoc($sales_executive_r)) {

										?>
												<option <?= ($seid == $sales_executive_d['id']) ? "selected" : ""; ?>
													value="<?= $sales_executive_d['id']; ?>"><?= $sales_executive_d['name']; ?></option>
										<?php
											}
										}
										?>
									</select>
								</div>

							</div>

							<div class="row">

								<div class="col-md-6">
									<label>Select Price List</label>
									<select class="form-control" id="price_list_id" name="price_list_id" onchange="getDiscountType(this.value)">
										<option value="">Select Pricelist</option>
										<?php
										$price_list_r = $db->rp_getData("price_list", "*", "isDelete=0 AND isActive=1");
										if ($price_list_r) {
											while ($price_list_d = mysqli_fetch_assoc($price_list_r)) {
										?>
												<option <?= ($price_list_id == $price_list_d['id']) ? "selected" : ""; ?> value="<?= $price_list_d['id']; ?>"><?= $price_list_d['pricelist_name']; ?></option>
										<?php
											}
										}
										?>
									</select>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Firm Name <code>*</code></label>
										<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
										<p class="help-block"></p>
									</div>
								</div>
							</div>



							<div class="row" style="margin-top: 10px;">

								<div class="col-md-6">
									<div class="form-group">
										<label>Person Name <code>*</code></label>
										<input type="hidden" name="id" value="<?php echo $executive_id; ?>">
										<input type="text" class="form-control" name="cname" id="cname" value="<?php echo $cname; ?>" autofocus>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Phone <code>*</code></label>
										<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" maxlength="10" size="10">
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										<label>Mobile No </label>
										<input type="text" class="form-control" name="mobile_no1" id="mobile_no1" value="<?php echo $mobile_no1; ?>" maxlength="10" size="10">
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Email<code>*</code> </label>
										<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Turnover</label>
										<input type="text" class="form-control" name="turnover" id="turnover" value="<?php echo $turnover; ?>">
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Turnover Year</label>
										<input type="text" class="form-control" name="turnover_year" id="turnover_year" value="<?php echo $turnover_year; ?>">
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6" <?php if ($mode == 'edit') { ?> hidden <?php } ?>>
									<div class="form-group">
										<label for="password">Password<code>*</code> </label>
										<input type="password" class="form-control" autocomplete="new-password" name="password" id="password" value="<?php echo $password; ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="address">Address Line 1 <code>*</code></label>
										<textarea class="form-control" name="address" id="address"><?php echo $address; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="row">
									<div class="col-md-6">
										<label for="country">Country <code>*</code></label>
										<select name="country" id="country" class="form-control" onChange="return State(this.value);">
											<option value="">--Select Country--</option>
											<?php
											$country_r = $db->rp_getData("country", "*", "isDelete=0");
											if (mysqli_num_rows($country_r) > 0) {
												while ($country_d = mysqli_fetch_array($country_r)) {
													if ($_REQUEST['mode'] == 'add') {
											?>
														<option value="<?php echo $country_d['name']; ?>" <?php if ($country_d['name'] == 'India') { ?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
													<?php
													} else {
													?>
														<option value="<?php echo $country_d['name']; ?>" <?php if ($country_d['name'] == $country) { ?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
											<?php
													}
												}
											}
											?>
										</select>
									</div>
									<div class="col-md-6">
										<label for="state">State <code>*</code></label>
										<select name="state" id="state" class="form-control" onChange="return city_data(this.value);">
											<option value="">--Select State--</option>
											<?php
											if ($mode == 'edit') {
												$state_r = $db->rp_getData("class", "*", "isDelete=0", "", 0);
												if (mysqli_num_rows($state_r) > 0) {
													while ($state_d = mysqli_fetch_array($state_r)) {
											?>
														<option <?php if ($state_d['name'] == $state) {
																	echo "selected";
																}  ?> value="<?php echo $state_d['name']; ?>"><?php echo $state_d['name']; ?></option>
											<?php
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
									<label for="state">City <code>*</code></label>
									<select name="main_city" id="main_city" class="form-control" onChange="return City(this.value);">
										<option value="">--Select City--</option>
										<?php
										if ($mode == 'edit') {
											$city_r = $db->rp_getData("city", "*", "isDelete=0 AND name='" . $main_city . "'", "", 0);
											if (mysqli_num_rows($city_r) > 0) {
												while ($city_d = mysqli_fetch_array($city_r)) {
										?>
													<option <?php if ($city_d['name'] == $main_city) {
																echo "selected";
															}  ?> value="<?php echo $city_d['name']; ?>"><?php echo $city_d['name']; ?></option>
										<?php
												}
											}
										}
										?>
									</select>
									<input type="hidden" name="area_id" id="area_id" value="">
									<p class="help-block"></p>
								</div>

								<div class="col-md-6">
									<label for="city">Route <code>*</code></label>
									<select name="city" id="city" class="form-control">
										<option value="">--Select Route--</option>
										<?php
										if ($mode == 'edit') {
											$city_name = $db->rp_getValue("area", "name", "name='" . $city . "'", 0);
										?>
											<option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
										<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6" style="margin-top: 10px">
									<div class="form-group">
										<label class="test">Zone<code>*</code></label>

										<select class="form-control" name="zone" id="zone" onChange="client_code_data(this.value);">
											<option value="">Select Zone </option>
											<?php
											$zone_r = $db->rp_getData("zone", "*", "isDelete=0", 0);
											if (mysqli_num_rows($zone_r) > 0) {
												while ($zone_d = mysqli_fetch_array($zone_r)) {

											?>
													<option value="<?php echo $zone_d['id']; ?>" <?= ($zone == $zone_d['id']) ? "selected" : ""; ?>><?php echo $zone_d['name']; ?></option>
											<?php
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>



							<div class="row">

								<?php
								if ($_REQUEST['mode'] == 'add') {
									$lastInsertId = $db->rp_getValue("executive", "MAX(`client_code_sr_by_type`)", "type_of_executive='" . $_REQUEST['type'] . "'");
									$code = str_pad(($lastInsertId + 1), 4, '0', STR_PAD_LEFT);
									//$client_code = "DEL".($code);
								}
								?>
								<input type="hidden" name="client_code_sr_by_type" id="client_code_sr_by_type" value="<?= $code ?>">
								<div class="col-md-6" style="margin-top: 10px" id="client_code" name="client_code">
									<div class="form-group">
										<label>Client code</label><code>*</code>
										<?php
										if ($_REQUEST['mode'] == 'edit') {
										?>
											<input type="text" class="form-control" name="client_code" id="client_code" value="<?php echo $client_code; ?>" readonly>
										<?php
										}
										?>
										<!-- <input type="text" class="form-control" name="client_code" id="client_code" value="<?php echo $client_code; ?>" readonly> -->
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-6" style="margin-top: 10px;">
									<div class="form-group">
										<label class="">Category<code>*</code></label>
										<select name="top_category_id[]" id="top_category_id" class="form-control top_category_id" multiple>
											<option value="">Select Category</option>
											<?php
											$category_data_r = $db->rp_getData("top_category_master", "id,name", "isDelete=0", "", 0);

											$top_category_id = explode(',', $top_category_id);

											while ($category_data_d = mysqli_fetch_assoc($category_data_r)) {

											?>

												<option <?= (in_array($category_data_d['id'], $top_category_id)) ? "selected" : "" ?> value="<?= $category_data_d['id'] ?>"><?= $category_data_d['name'] ?>
												</option>

											<?php
											}

											?>
										</select>
									</div>
								</div>
							</div>











							<div class="row">

								<!--div class="col-md-6">
									<div class="form-group">
										<label for="discount">Discount % </label><code>*</code>
										<input type="text" class="form-control" name="discount" id="discount" value="<?php echo $discount; ?>" >
										<p class="help-block"></p>
									</div>
								</div-->

								<div class="col-md-6">
									<div class="form-group">
										<!-- <label for="#"><b>Customer Shop</b></label> -->
										<input data-image="<?php echo ($image_path != "" && file_exists(SUPER_STOCKIST_A . $image_path)) ? SUPER_STOCKIST_A . $image_path : ""; ?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="">
									</div>
									<?php
									if ($_REQUEST['mode'] == "edit") {

										if ($image_path != "") {
											$img = explode(",", $image_path);
											$imgpath = array();
											for ($i = 0; $i <= sizeof($img); $i++) {
												$imgpath[] = "../resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $_REQUEST["id"] . "' AND id='" . $img[$i] . "'", 0);
											}

											for ($i = 0; $i < sizeof($imgpath); $i++) {
												if ($i == 0) {
													//echo "hello";exit();
									?>
													<a href="<?= $imgpath[$i] ?>" data-lightbox="image<?= $count ?>" data-title="image<?= $_REQUEST['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
												<?php
												} else {
													//echo "hello123";exit();
												?>
													<div class="hidden">
														<a href="<?= $imgpath[$i] ?>" data-lightbox="image<?= $count ?>" data-title="image<?= $LeadD['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
													</div>
									<?php
												}
											}
										}
									}
									?>
								</div>
							</div>





							<!-- <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Credit Limit</label>
										<input type="text" class="form-control" name="credit_limit" id="credit_limit" value="<?php echo $credit_limit; ?>">
										<p class="help-block"></p>
									</div>
								</div>

								

								<div class="col-md-6">
									<div class="form-group">
										<label>Credit Days </label>
										<input type="text" class="form-control" name="credit_day" id="credit_day" value="<?php echo $credit_day; ?>">
										<p class="help-block"></p>
									</div>
								</div>
								
							</div> -->

							<div class="row">
								<!-- <div class="col-md-6">
									<div class="form-group">
										<label>Shipping Address</label>
										<textarea class="form-control" name="shipping_address" id="shipping_address"><?php echo $shipping_address; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div> -->

								<div class="col-md-6">


									<div class="form-group">
										<label>Shipping Address</label>

										<?php if ($_REQUEST['mode'] != 'edit') { ?>
											<textarea class="form-control" name="shipping_address[]" id="shipping_address"><?php echo $shipping_address; ?></textarea>
											<p class="help-block"></p>

											<div id="new_shipping_address">
											</div>
										<?php } else { ?>
											<div id="new_shipping_address">
											</div>


											<?php
											$count = 0;
											$customer_vs_shipping_address_r = $db->rp_getData("customer_vs_shipping_address", "*", "customer_id='" . $_REQUEST['id'] . "' AND isDelete=0", "", 0);

											while ($shipp_d = mysqli_fetch_array($customer_vs_shipping_address_r)) {
												$count++;
											?>
												<?php if ($count == 1) { ?>
													<div class="form-group">
														<textarea class="form-control" name="shipping_address[]" id="shipping_address"><?php echo $shipp_d['shipping_address']; ?></textarea>
													</div>
												<?php } else { ?>
													<div class="form-group" id="removeClass<?= $count; ?>">
														<button type="button" onclick="Remove_add(<?= $count; ?>)" class="remove-this-first text-danger" id="BtnDel"><i class="fa fa-trash"></i></button>
														<textarea class="form-control" name="shipping_address[]" id="shipping_address"><?php echo $shipp_d['shipping_address']; ?></textarea>
													</div>
											<?php }
											} ?>

										<?php } ?>
									</div>

									<div class="form-group">
										<button class="btn btn-primary" type="button" id="add_new">ADD</button>
										<p class="help-block"></p>
									</div>

								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="billing_address">Billing Address</label>
										<textarea class="form-control" name="billing_address" id="billing_address"><?php echo $billing_address; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div>
							</div>


							<br />
							<br />




						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12">
			<div class="portlet grey-cascade box">
				<!-- <div class="portlet-title">
					<div class="caption">
						<i class="fa fa-book"></i>Extra Information
					</div>

				</div> -->
				<div class="portlet-body" style="min-height:150px;">
					<div class="form-body">
						<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOne" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOne" style="color:#fff;">
							<div class="portlet grey-cascade box">
								<div class="portlet-title">
									<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Extra Information<span style="float: right; "><i class="fa fa-angle-down"></i></span>
									</div>
								</div>
							</div>
						</a>
						<hr />
						<div class="portlet grey-cascade box overflow-auto" style="box-shadow: none;" id="please-scroll">
							<div id="collapseOne" class="portlet-body collapse <?= $in_class; ?>">
								<div class="row">
									<div class="col-md-12 col-sm-12">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="address">Company Type </label>
													<select class="form-control" name="company_type" id="company_type">
														<option value="">Select Company Type</option>
														<?php
														$company_list_d = $db->rp_getData('company_type', "*", "1=1", "", 0);
														while ($company_list_r = mysqli_fetch_assoc($company_list_d)) {
														?>
															<option <?php echo ($company_type == $company_list_r['id']) ? "selected" : ""; ?> value="<?php echo $company_list_r['id'] ?>">
																<?php echo $company_list_r['name']; ?>
															</option>
														<?php
														}
														?>
													</select>
												</div>
												<div class="row">
													<!-- <div class="col-md-6">
											<div class="form-group">
												<label>CST Number</label>
												<input type="text" class="form-control" name="cst" id="cst" value="<?php echo $cst; ?>">
												<p class="help-block"></p>
											</div>
										</div> -->
													<div class="col-md-6">
														<div class="form-group">
															<label>GST Number <code>*</code></label>
															<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $gst; ?>" maxlength="15" size="15">
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>PAN Number</label>
															<input type="text" class="form-control" name="pan" id="pan" value="<?php echo $pan; ?>">
															<p class="help-block"></p>
														</div>
													</div>
												</div>
												<!-- <div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>VAT Number</label>
												<input type="text" class="form-control" name="vat" id="vat" value="<?php echo $vat; ?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Excise</label>
												<input type="text" class="form-control" name="excise" id="excise" value="<?php echo $excise; ?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div> -->
												<div class="row">

													<div class="col-md-6">
														<div class="form-group">
															<label>Latitude</label>
															<input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude; ?>">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>Longitude</label>
															<input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude; ?>">
															<p class="help-block"></p>
														</div>
													</div>
													<!-- <div class="col-md-6">
											<div class="form-group">
												<label>Other Contact</label>
												<input type="text" class="form-control" name="other_mobile" id="other_mobile" value="<?php echo $other_mobile; ?>" maxlength="10" size="10">
												<p class="help-block"></p>
											</div>
										</div> -->
												</div>

												<div class="row">



												</div>
												<div class="row">
													<div class="col-md-6 discount-type " style="margin-top: 10px;">
														<div class="form-group">
															<label>Cash Discount (in %)</label>
															<input type="text" name="cash_discount" id="cash_discount" class="form-control cash_discount float_positive" value="<?= $cash_discount; ?>">
														</div>
													</div>

													<div class="col-md-6 discount-type " style="margin-top: 10px;">
														<div class="form-group">
															<label>Additional Discount (in %)</label>
															<input type="text" name="additional_discount" id="additional_discount" class="form-control additional_discount float_positive" value="<?= $additional_discount; ?>">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Whatsapp</label>
															<!-- <input type="text" class="form-control" name="whatsapp_no" id="whatsapp_no" value="<?php echo $whatsapp_no; ?>" maxlength="10" size="10"> -->
															<input type="text" class="form-control" name="whatsapp_no" id="whatsapp_no" value="<?php echo $whatsapp_no; ?>" size="10">
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>CC Email</label>
															<input type="text" class="form-control" name="email_cc" id="email_cc" value="<?php echo $email_cc; ?>">
															<p class="help-block"></p>
														</div>
													</div>
												</div>
												<div class="row">

													<div class="col-md-6">
														<div class="form-group">
															<label for="address">Address Line 2</label>
															<textarea class="form-control" name="address2" id="address2"><?php echo $address2; ?></textarea>
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="zip">Pincode </label>
															<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $zip; ?>" maxlength="6" size="6">
															<p class="help-block"></p>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Select Type</label>
															<select id="credit_debit_type" name="credit_debit_type" class="form-control">
																<option value="">Select Type</option>
																<option value="1" <?php if ($credit_debit_type == "1") {
																						echo "selected";
																					} ?>>Credit</option>
																<option value="2" <?php if ($credit_debit_type == "2") {
																						echo "selected";
																					} ?>>Debit</option>
															</select>
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>Openinig Balance</label>
															<input type="text" class="form-control" name="openinig_balance" id="openinig_balance" value="<?php echo $openinig_balance; ?>" placeholder="Enter Openinig Balance">
															<p class="help-block"></p>
														</div>
													</div>
												</div>

												<div class="row">


													<div class="col-md-6" hidden="">
														<div class="form-group">
															<!-- vendor/desk -->
															<label for="vendor_desk"><b>Vendor/Desk</b></label>
															<input data-image="<?php echo ($vendor_desk != "" && file_exists(SUPER_STOCKIST_A . $vendor_desk)) ? SUPER_STOCKIST_A . $vendor_desk : ""; ?>" type="file" name="vendor_desk" id="vendor_desk" data-old-image-dom="old_vendor_desk" data-old-image-path="<?php echo $vendor_desk ?>" value="">
														</div>
													</div>
													<div class="col-md-6" hidden="">
														<div class="form-group">
															<!-- Office supplier -->
															<label for="vendor_desk"><b>Office supplier</b></label>
															<input data-image="<?php echo ($office_supplier != "" && file_exists(SUPER_STOCKIST_A . $office_supplier)) ? SUPER_STOCKIST_A . $office_supplier : ""; ?>" type="file" name="office_supplier" id="office_supplier" data-old-image-dom="old_office_supplier" data-old-image-path="<?php echo $office_supplier ?>" value="">
														</div>
													</div>
													<div class="col-md-6" hidden="">
														<div class="form-group">
															<!-- GST Detail -->
															<label for="vendor_desk"><b>Office supplier</b></label>
															<input data-image="<?php echo ($gst_detail != "" && file_exists(SUPER_STOCKIST_A . $gst_detail)) ? SUPER_STOCKIST_A . $gst_detail : ""; ?>" type="file" name="gst_detail" id="gst_detail" data-old-image-dom="old_gst_detail" data-old-image-path="<?php echo $gst_detail ?>" value="">
														</div>
													</div>
													<div class="col-md-6" hidden="">
														<div class="form-group">
															<!-- Others -->
															<label for="vendor_desk"><b>Other</b></label>
															<input data-image="<?php echo ($other_image != "" && file_exists(SUPER_STOCKIST_A . $other_image)) ? SUPER_STOCKIST_A . $other_image : ""; ?>" type="file" name="other_image" id="other_image" data-old-image-dom="old_other_image" data-old-image-path="<?php echo $other_image ?>" value="">
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12" hidden="">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-book"></i>Class map Area Information
					</div>

				</div>
				<div class="portlet-body" style="min-height:150px;">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">

									<div class="form-group">
										<label for="class_id">Class Type <code>*</code></label>
										<select class="form-control noSelect2" name="class_id" id="class_id" onChange="return getArea(this.value)">
											<?php
											if ($mode == "edit") {
												$class_list_d = $db->rp_getValue("executive", "class_id", "type_of_executive='dealer' AND id=" . $dealer_distributor_id . "", "", 0);
												if ($class_list_d == "") {
													$class_list_d = $class_id;
												}
												$class = $db->rp_getValue("class", "name", "id = '" . $class_list_d["class_id"] . "'AND isDelete=0 AND isActive=1", 0);
											?>
												<option <?php echo ($class_id == $class_list_d['class_id']) ? "selected" : ""; ?> value="<?php echo $class_list_d['class_id'] ?>">
													<?php echo $class; ?>
												</option>
											<?php

											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
									<div class="form-group">
										<label for="area_id">Area Type <code>*</code></label></br>
										<label style="font-size:15px;"><a href='#' id='select-all'>select all/</a>
											<a href='#' id='deselect-all'>deselect all</a></label>
										<select multiple="multiple" class="multi-select" id="area_id" name="area_id[]">

											<?php
											if ($mode == "edit") {
												$area_r = $db->rp_getData("executive_map_area", "DISTINCT area_id", "executive_id=" . $dealer_distributor_id . "", "", 0);
												if ($area_r) {
													while ($area_d = mysqli_fetch_array($area_r)) {
														$area = $db->rp_getValue("area", "name", "id='" . $area_d['area_id'] . "' AND isDelete=0 AND isActive=1");
											?>
														<option value="<?php echo $area_d['area_id']; ?>" <?php echo (in_array($area_d['area_id'], $areas)) ? "selected" : ""; ?> value="<?php echo $area_d['id']; ?>"><?php echo $area; ?></option>
													<?php
													}
												} else {
													foreach ($areas as $area_d) {
														$area = $db->rp_getValue("area", "name", "id='" . $area_d['area_id'] . "' AND isDelete=0 AND isActive=1");
													?>
														<option value="<?php echo $area_d['area_id']; ?>" <?php echo (in_array($area_d['area_id'], $areas)) ? "selected" : ""; ?> value="<?php echo $area_d['id']; ?>"><?php echo $area; ?></option>
											<?php
													}
												}
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
			<?php if (!$disabled) { ?>

				<input type="hidden" value="<?php echo $type_of_inquiry; ?>" name="type_of_inquiry" />
				<input type="hidden" value="<?php echo $flag; ?>" name="flag" />
				<input type="hidden" value="<?php echo $mode; ?>" name="mode" />
				<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
				<input type="hidden" value="<?php echo $_REQUEST['inquiry_id']; ?>" name="inquiry_id" />
				<button type="submit" name="submit" class="btn green">Submit</button>
			<?php } ?>
			<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
		</div>
	</div>
</form>