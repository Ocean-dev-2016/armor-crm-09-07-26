<form id="inquiry_form_distributor" action="executive_form_function.php" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6 col-sm-12">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Merchant Customer Information
					</div>
				</div>
				<div class="portlet-body">

					<div class="row">
						<div class="col-md-12">
							<div class="row">
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

							<div class="row" style="margin-top: 10px;">
								<div class="col-md-6">
									<div class="form-group">
										<label>Person Name <code>*</code></label>
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
										<label>Mobile No</label>
										<input type="text" class="form-control" name="mobile_no1" id="mobile_no1" value="<?php echo $mobile_no1; ?>" maxlength="10" size="10">
										<p class="help-block"></p>
									</div>
								</div>
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
										<label>Email </label>
										<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
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

								<div class="col-md-6">
									<div class="form-group">
										<label class="test">Type of Industry</label>
										<select class="form-control b-3" id="industry_type" name="industry_type" onchange="get_distributor(this.value);">
											<option value="">Select Type of Industry</option>
											<?php
											$customer_type = $db->rp_getData("industry_type", "*", "isDelete=0");
											if ($customer_type) {
												while ($customer_type_d = mysqli_fetch_assoc($customer_type)) { ?>
													<option value="<?= $customer_type_d['id'] ?>" <?= ($industry_type == $customer_type_d['id']) ? "selected" : ""; ?>><?= $customer_type_d['name'] ?></option>
											<?php
												}
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
								</div>

							</div>
							<div class="row">
								<div class="col-md-6" <?php if ($mode == 'edit') { ?> hidden <?php } ?>>
									<div class="form-group">
										<label>Password</label>
										<input type="password" class="form-control" autocomplete="new-password" name="password" id="password" value="<?php echo $password; ?>">
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
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="address">Address Line 1<code>*</code></label>
										<textarea class="form-control" name="address" id="address"><?php echo $address; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="address2">Address Line 2</label>
										<textarea class="form-control" name="address2" id="address2"><?php echo $address2; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div>

							</div>
							<!-- Dhaval -->
							<div class="row">
								<!-- <div class="col-md-6">
									<div class="form-group">
										<label for="address">Shipping Address</label>
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
										<label for="address">Billing Address</label>
										<textarea class="form-control" name="billing_address" id="billing_address"><?php echo $billing_address; ?></textarea>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<!-- Dhaval -->
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
										<label>Firm Name </label>
										<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<?php
								if ($_REQUEST['mode'] == 'add') {
									$lastInsertId = $db->rp_getValue("executive", "MAX(`client_code_sr_by_type`)", "type_of_executive='" . $_REQUEST['type'] . "'");
									$code = str_pad(($lastInsertId + 1), 4, '0', STR_PAD_LEFT);
									$client_code = "EXP" . ($code);
								}
								?>
								<input type="hidden" name="client_code_sr_by_type" id="client_code_sr_by_type" value="<?= $code ?>">
								<div class="col-md-6">
									<div class="form-group">
										<label>Client code</label><code>*</code>
										<input type="text" class="form-control" name="client_code" id="client_code" value="<?php echo $client_code; ?>" readonly>
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<!-- <label for="#"><b>Customer Shop</b></label> -->
										<input data-image="<?php echo ($image_path != "" && file_exists(SUPER_STOCKIST_A . $image_path)) ? SUPER_STOCKIST_A . $image_path : ""; ?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="">
									</div>
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
								
							</div>
 -->

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Shipping Address</label>
										<textarea class="form-control" name="shipping_address" id="shipping_address"><?php echo $shipping_address; ?></textarea>
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

							<div class="form-group">
								<div class="row">
									<div class="col-md-3">
										<label for="country">Country</label><code>*</code>
										<select name="country" id="country" class="form-control" onChange="return State(this.value);">
											<option value="">--Select Country--</option>
											<?php
											$country_r = $db->rp_getData("country", "*", "isDelete=0");
											if (mysqli_num_rows($country_r) > 0) {
												while ($country_d = mysqli_fetch_array($country_r)) {
											?>
													<option value="<?php echo $country_d['name']; ?>" <?php if ($country_d['name'] == $country) { ?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
											<?php
												}
											}
											?>
										</select>
										<p class="help-block"></p>
									</div>
									<div class="col-md-3">
										<label for="state">State</label><code>*</code>
										<select name="state" id="state" class="form-control" onChange="return City(this.value);">
											<option value="">--Select State--</option>
											<?php
											if ($mode == 'edit') {
												// echo $state;exit();
												$state_r = $db->rp_getData("class", "*", "isDelete=0", "", 0);
												if (mysqli_num_rows($state_r) > 0) {
													while ($state_d = mysqli_fetch_array($state_r)) {

											?>
														<option <?php if (strtolower($state_d['name']) == strtolower($state)) {
																	echo "selected";
																}  ?> value="<?php echo $state_d['name']; ?>"><?php echo $state_d['name']; ?></option>
											<?php
													}
												}
											}
											?>
										</select>
										<p class="help-block"></p>
										<input type="hidden" name="class_id" id="class_id" value="">

									</div>
									<div class="col-md-3">
										<label for="city">City</label><code>*</code>
										<select name="city" id="city" class="form-control" onChange="get_id(this.value);">
											<option value="">--Select City--</option>
											<?php
											if ($mode == 'edit') {
												$city_name = $db->rp_getValue("area", "name", "name='" . $city . "'", 0);
											?>
												<option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?></option>
											<?php
											}
											?>
										</select>
										<input type="hidden" name="area_id" id="area_id" value="">
										<p class="help-block"></p>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="test">Zone</label>

											<select class="form-control" name="zone" id="zone">
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
									<br />
									<br />

								</div>

							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Select Type<code>*</code></label>
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
										<label>Openinig Balance <code>*</code></label>
										<input type="text" class="form-control" name="openinig_balance" id="openinig_balance" value="<?php echo $openinig_balance; ?>" placeholder="Enter Openinig Balance">
										<p class="help-block"></p>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-book"></i>Extra Information
					</div>

				</div>
				<div class="portlet-body" style="min-height:150px;">
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
										<p class="help-block"></p>
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
												<label>GST Number<code>*</code></label>
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

										<div class="col-md-6">
											<div class="form-group">
												<label class="">Category</label>
												<select name="category_id[]" id="category_id" class="form-control category_id" multiple>
													<option value="">Select Category</option>
													<?php
													$category_data_r = $db->rp_getData("category_master", "id,name,tcid", "isDelete=0", "", 0);

													$category_id = explode(',', $category_id);

													while ($category_data_d = mysqli_fetch_assoc($category_data_r)) {

													?>

														<option <?= (in_array($category_data_d['id'], $category_id)) ? "selected" : "" ?> value="<?= $category_data_d['id'] ?>">
															<?= $db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $category_data_d['tcid'] . "'") . " - " . $category_data_d['name'] ?>

														</option>

													<?php
													}

													?>


												</select>

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
		<!-- <div class="col-md-6 col-sm-12" hidden="">
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
										<label for="class_id">Class Type<code>*</code></label>
										<select class="form-control" name="class_id" id="class_id" onChange="return getArea(this.value)">
											<option value="">Select Class Type</option>
											<?php

											$class_list_d = $db->rp_getData('class', "*", "1=1 AND isDelete=0 AND isActive=1", "", 0);
											while ($class_list_r = mysqli_fetch_assoc($class_list_d)) {
											?>
												<option <?php echo ($class_id == $class_list_r['id']) ? "selected" : ""; ?> value="<?php echo $class_list_r['id'] ?>">
													<?php echo $class_list_r['name']; ?>
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
											$area_r = $db->rp_getData("area", "*", "class_id='" . $class_id . " AND isDelete=0 AND isActive=1'", "", 0);
											if ($area_r) {
												while ($area_d = mysqli_fetch_array($area_r)) {

											?>
													<option <?php echo (in_array($area_d['id'], $areas)) ? "selected" : ""; ?> value="<?php echo $area_d['id']; ?>"><?php echo $area_d['name']; ?></option>
											<?php
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
		</div> -->
	</div>

	<div class="row">
		<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
			<?php if (!$disabled) { ?>

				<input type="hidden" value="<?php echo $type_of_inquiry; ?>" name="type_of_inquiry" />
				<input type="hidden" value="<?php echo $mode; ?>" name="mode" />
				<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
				<button type="submit" name="submit" class="btn green">Submit</button>
			<?php } ?>
			<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
		</div>
	</div>

</form>
<script>

</script>