<form id="inquiry_form_distributor" onSubmit="return check_form()" action="sales_executive_form_function.php" method="post" enctype="multipart/form-data">

	<div class="row">
		<div class="col-md-6">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Dispatch Manager
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="form-group">
								<label for="address">Regional Sales Manager</label>
								<select class="form-control" name="sm_id" id="sales_manager" onChange="getAreaClass(this,'area_sales_manager')">
									<option value="">Select Regional Sales Manager</option>
									<?php
									$ss_d = $db->rp_getData('sales_executive', "*", "1=1 AND type='sales_manager' AND isDelete=0 AND isActive=1", "", 0);
									while ($ss_r = mysqli_fetch_assoc($ss_d)) {
									?>
										<option <?php echo ($sm_id == $ss_r['id']) ? "selected" : ""; ?> value="<?php echo $ss_r['id'] ?>">
											<?php echo $ss_r['username'] . " (" . $ss_r['name'] . ")"; ?>
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
								<label>Person Name <code>*</code></label>
								<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
								<p class="help-block"></p>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>CUG No. <code>*</code></label>
								<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" maxlength="10">
								<p class="help-block"></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>User Name <code>*</code></label>
								<input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>">
								<p class="help-block"></p>
							</div>
						</div>
						<div class="col-md-6" <?php if ($mode == 'edit') { ?> hidden <?php } ?>>
							<div class="form-group">
								<label>Password <code>*</code></label>
								<input type="password" class="form-control" autocomplete="new-password" name="password" id="password" value="<?php echo $password; ?>">
								<p class="help-block"></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Email</label>
								<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
								<p class="help-block"></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Min. Working Start Time <code>*</code></label>
								<input type="text" class="form-control timepicker" name="executive_in_min" id="executive_in_min" value="<?php echo date("h:i a", strtotime($executive_in_min)); ?>" onChange="tConvert(this.value)">

								<input type="hidden" class="form-control timepicker_hidden" id="executive_in_min_val" value="">

								<p class="help-block"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Max. Working Start Time <code>*</code></label>
								<input type="text" class="form-control timepicker" name="executive_in_max" id="executive_in_max" value="<?php echo date("h:i a", strtotime($executive_in_max)); ?>" onChange="tConvert_max(this.value)">

								<input type="hidden" class="form-control timepicker_hidden" id="executive_in_max_val" value="">

								<p class="help-block"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Working End Time <code>*</code></label>
								<input type="text" class="form-control timepicker" name="executive_out" id="executive_out" value="<?php echo date("h:i a", strtotime($executive_out)); ?>" onChange="tConvert_Out(this.value)">

								<input type="hidden" class="form-control timepicker_hidden" id="executive_out_val" value="">

								<p class="help-block"></p>
							</div>
						</div>

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
				</div>
			</div>
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Rights
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Name</th>
										<th align="center">View Flag</th>
										<th align="center">Insert Flag</th>
										<th align="center">Update Flag</th>
										<th align="center">Delete Flag</th>
										<th align="center">Check All</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Attendance</td>
										<td></td>
										<td align="center"><input type="checkbox" data-count="9" class="row-check row-check9" name="attendance_insert_flag" id="attendance_insert_flag" value="1" <?php echo ($attendance_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck9" data-count="9" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Tracking</td>
										<td align="center"><input type="checkbox" data-count="30" class="row-check row-check30" name="tracking_flag" id="tracking_flag" value="1" <?php echo ($tracking_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck30" data-count="30" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td><?= SITENAME ?> Customer</td>
										<td align="center"><input type="checkbox" data-count="8" class="row-check row-check8" name="customer_view_flag" id="customer_view_flag" value="1" <?php echo ($customer_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="8" class="row-check row-check8" name="customer_insert_flag" id="customer_insert_flag" value="1" <?php echo ($customer_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="8" class="row-check row-check8" name="customer_update_flag" id="customer_update_flag" value="1" <?php echo ($customer_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="8" class="row-check row-check8" name="customer_delete_flag" id="customer_delete_flag" value="1" <?php echo ($customer_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck8" data-count="8" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>Super Stockist</td>
										<td align="center"><input type="checkbox" data-count="1" class="row-check row-check1" name="super_stokist_order_view_flag" id="super_stokist_order_view_flag" value="1" <?php echo ($super_stokist_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td></td>
										<td></td>
										<td></td>

										<!-- <td align="center"><input type="checkbox" data-count="1" class="row-check row-check1" name="super_stokist_order_insert_flag" id="super_stokist_order_insert_flag" value="1" <?php echo ($super_stokist_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<!-- <td align="center"><input type="checkbox" data-count="1" class="row-check row-check1" name="super_stokist_order_update_flag" id="super_stokist_order_update_flag" value="1" <?php echo ($super_stokist_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<!-- <td align="center"><input type="checkbox" data-count="1" class="row-check row-check1"  name="super_stokist_order_delete_flag" id="super_stokist_order_delete_flag" value="1" <?php echo ($super_stokist_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<td align="center"><input type="checkbox" class="masterCheck masterCheck1" data-count="1" style="width:60px;text-align:center"></td>
									</tr>

									</tr>
									<tr>

									<tr>
										<td>Dealer</td>

										<td align="center"><input type="checkbox" data-count="2" class="row-check row-check2" name="outlets_order_view_flag" id="outlets_order_view_flag" value="1" <?php echo ($outlets_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td></td>
										<td></td>
										<td></td>

										<!-- 	<td align="center"><input type="checkbox" data-count="2" class="row-check row-check2" name="outlets_order_insert_flag" id="outlets_order_insert_flag" value="1" <?php echo ($outlets_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
								
								<td align="center"><input type="checkbox" data-count="2" class="row-check row-check2" name="outlets_order_update_flag" id="outlets_order_update_flag" value="1" <?php echo ($outlets_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
								
								<td align="center"><input type="checkbox" data-count="2" class="row-check row-check2" name="outlets_order_delete_flag" id="outlets_order_delete_flag" value="1" <?php echo ($outlets_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<td align="center"><input type="checkbox" class="masterCheck masterCheck2" data-count="2" style="width:60px;text-align:center"></td>
									</tr>
									</tr>

									<tr>
										<td>Distributor</td>

										<td align="center"><input type="checkbox" data-count="3" class="row-check row-check3" name="dealer_order_view_flag" id="dealer_order_view_flag" value="1" <?php echo ($dealer_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td></td>
										<td></td>
										<td></td>

										<!-- 	<td align="center"><input type="checkbox" data-count="3" class="row-check row-check3" name="dealer_order_insert_flag" id="dealer_order_insert_flag" value="1" <?php echo ($dealer_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
									
								<td align="center"><input type="checkbox" data-count="3" class="row-check row-check3" name="dealer_order_update_flag" id="dealer_order_update_flag" value="1" <?php echo ($dealer_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
									
								<td align="center"><input type="checkbox" data-count="3" class="row-check row-check3" name="dealer_order_delete_flag" id="dealer_order_delete_flag" value="1" <?php echo ($dealer_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<td align="center"><input type="checkbox" class="masterCheck masterCheck3" data-count="3" style="width:60px;text-align:center"></td>
									</tr>
									</tr>

									<tr>
										<td>B2B Customer (Type)</td>

										<td align="center"><input type="checkbox" data-count="4" class="row-check row-check4" name="project_order_view_flag" id="project_order_view_flag" value="1" <?php echo ($project_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="4" class="row-check row-check4" name="project_order_insert_flag" id="project_order_insert_flag" value="1" <?php echo ($project_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="4" class="row-check row-check4" name="project_order_update_flag" id="project_order_update_flag" value="1" <?php echo ($project_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="4" class="row-check row-check4" name="project_order_delete_flag" id="project_order_delete_flag" value="1" <?php echo ($project_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" class="masterCheck masterCheck4" data-count="4" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>B2C Customer (Type)</td>
										<td align="center"><input type="checkbox" data-count="5" class="row-check row-check5" name="oem_order_view_flag" id="oem_order_view_flag" value="1" <?php echo ($oem_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<!-- 	<td align="center"><input type="checkbox" data-count="5" class="row-check row-check5" name="oem_order_insert_flag" id="oem_order_insert_flag" value="1" <?php echo ($oem_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="5" class="row-check row-check5" name="oem_order_update_flag" id="oem_order_update_flag" value="1" <?php echo ($oem_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="5" class="row-check row-check5" name="oem_order_delete_flag" id="oem_order_delete_flag" value="1" <?php echo ($oem_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" class="masterCheck masterCheck5" data-count="5" style="width:60px;text-align:center"></td>
									</tr>
									<!-- <tr>
												<td>Promotional Customer (Type)</td>
												<td align="center"><input type="checkbox" data-count="35" class="row-check row-check35" name="promotional_customer_view_flag" id="promotional_customer_view_flag" value="1" <?php echo ($promotional_customer_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td></td>
												<td></td>
												<td></td>
												<td align="center"><input type="checkbox" class="masterCheck masterCheck35" data-count="35" style="width:60px;text-align:center"></td>
											</tr>

											<tr>
												<td>Merchant Customer (Type)</td>
												<td align="center"><input type="checkbox" data-count="33" class="row-check row-check33" name="marchent_customer_view_flag" id="marchent_customer_view_flag" value="1" <?php echo ($marchent_customer_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td></td>
												<td></td>
												<td></td>
												
												<td align="center"><input type="checkbox" class="masterCheck masterCheck33" data-count="33" style="width:60px;text-align:center"></td>
											</tr> -->

									<tr>
										<td>My Followup</td>
										<td align="center"><input type="checkbox" data-count="10" class="row-check row-check10" name="followup_view_flag" id="followup_view_flag" value="1" <?php echo ($followup_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="10" class="row-check row-check10" name="followup_insert_flag" id="followup_insert_flag" value="1" <?php echo ($followup_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck10" data-count="10" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Raw Data</td>

										<td align="center"><input type="checkbox" data-count="32" class="row-check row-check32" name="prospact_view_flag" id="prospact_view_flag" value="1" <?php echo ($prospact_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="32" class="row-check row-check32" name="prospact_insert_flag" id="prospact_insert_flag" value="1" <?php echo ($prospact_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="32" class="row-check row-check32" name="prospact_update_flag" id="prospact_update_flag" value="1" <?php echo ($prospact_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="32" class="row-check row-check32" name="prospact_delete_flag" id="prospact_delete_flag" value="1" <?php echo ($prospact_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck32" data-count="32" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Inquiry </td>

										<td align="center"><input type="checkbox" data-count="6" class="row-check row-check6" name="survey_customer_view_flag" id="survey_customer_view_flag" value="1" <?php echo ($survey_customer_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="6" class="row-check row-check6" name="survey_customer_insert_flag" id="survey_customer_insert_flag" value="1" <?php echo ($survey_customer_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="6" class="row-check row-check6" name="survey_customer_update_flag" id="survey_customer_update_flag" value="1" <?php echo ($survey_customer_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="6" class="row-check row-check6" name="survey_customer_delete_flag" id="survey_customer_delete_flag" value="1" <?php echo ($survey_customer_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck6" data-count="6" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Lead</td>

										<td align="center"><input type="checkbox" data-count="7" class="row-check row-check7" name="customer_leads_view_flag" id="customer_leads_view_flag" value="1" <?php echo ($customer_leads_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="7" class="row-check row-check7" name="customer_leads_insert_flag" id="customer_leads_insert_flag" value="1" <?php echo ($customer_leads_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="7" class="row-check row-check7" name="customer_leads_update_flag" id="customer_leads_update_flag" value="1" <?php echo ($customer_leads_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="7" class="row-check row-check7" name="customer_leads_delete_flag" id="customer_leads_delete_flag" value="1" <?php echo ($customer_leads_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck7" data-count="7" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Quotation</td>

										<td align="center"><input type="checkbox" data-count="31" class="row-check row-check31" name="quotation_view_flag" id="quotation_view_flag" value="1" <?php echo ($quotation_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="31" class="row-check row-check31" name="quotation_insert_flag" id="quotation_insert_flag" value="1" <?php echo ($quotation_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="31" class="row-check row-check31" name="quotation_update_flag" id="quotation_update_flag" value="1" <?php echo ($quotation_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="31" class="row-check row-check31" name="quotation_delete_flag" id="quotation_delete_flag" value="1" <?php echo ($quotation_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck31" data-count="31" style="width:60px;text-align:center"></td>
									</tr>
									</tr>

									<tr>
										<td>Create Order</td>
										<td align="center"><input type="checkbox" data-count="11" class="row-check row-check11" name="create_order_view_flag" id="create_order_view_flag" value="1" <?php echo ($create_order_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="11" class="row-check row-check11" name="create_order_insert_flag" id="create_order_insert_flag" value="1" <?php echo ($create_order_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="11" class="row-check row-check11" name="create_order_update_flag" id="create_order_update_flag" value="1" <?php echo ($create_order_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="11" class="row-check row-check11" name="create_order_delete_flag" id="create_order_delete_flag" value="1" <?php echo ($create_order_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck11" data-count="11" style="width:60px;text-align:center"></td>
									</tr>


									<tr>
										<td>My Expense</td>
										<td align="center"><input type="checkbox" data-count="18" class="row-check row-check18" name="expense_view_flag" id="expense_view_flag" value="1" <?php echo ($expense_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="18" class="row-check row-check18" name="expense_insert_flag" id="expense_insert_flag" value="1" <?php echo ($expense_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="18" class="row-check row-check18" name="expense_update_flag" id="expense_update_flag" value="1" <?php echo ($expense_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" data-count="18" class="row-check row-check18" name="expense_delete_flag" id="expense_delete_flag" value="1" <?php echo ($expense_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck18" data-count="18" style="width:60px;text-align:center"></td>
									</tr>


									<tr>
										<td>My Leave</td>
										<td align="center"><input type="checkbox" data-count="19" class="row-check row-check19" name="leave_view_flag" id="leave_view_flag" value="1" <?php echo ($leave_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="19" class="row-check row-check19" name="leave_insert_flag" id="leave_insert_flag" value="1" <?php echo ($leave_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="19" class="row-check row-check19" name="leave_update_flag" id="leave_update_flag" value="1" <?php echo ($leave_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="19" class="row-check row-check19" name="leave_delete_flag" id="leave_delete_flag" value="1" <?php echo ($leave_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck19" data-count="19" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Complain</td>
										<td align="center"><input type="checkbox" data-count="13" class="row-check row-check13" name="complain_view_flag" id="complain_view_flag" value="1" <?php echo ($complain_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="13" class="row-check row-check13" name="complain_insert_flag" id="complain_insert_flag" value="1" <?php echo ($complain_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<!-- <td align="center"><input type="checkbox" data-count="13" class="row-check row-check13" name="complain_update_flag" id="complain_update_flag" value="1" <?php echo ($complain_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<!-- <td align="center"><input type="checkbox" data-count="13" class="row-check row-check13" name="complain_delete_flag" id="complain_delete_flag" value="1" <?php echo ($complain_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck13" data-count="13" style="width:60px;text-align:center"></td>
									</tr>


									<tr>
										<td>Visit</td>
										<td align="center"><input type="checkbox" data-count="21" class="row-check row-check21" name="visit_view_flag" id="visit_view_flag" value="1" <?php echo ($visit_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" data-count="21" class="row-check row-check21" name="visit_insert_flag" id="visit_insert_flag" value="1" <?php echo ($visit_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="21" class="row-check row-check21" name="visit_update_flag" id="visit_update_flag" value="1" <?php echo ($visit_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" data-count="21" class="row-check row-check21" name="visit_delete_flag" id="visit_delete_flag" value="1" <?php echo ($visit_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck21" data-count="21" style="width:60px;text-align:center"></td>
									</tr>


									<tr>
										<td>Near By Me</td>
										<td align="center"><input type="checkbox" data-count="16" class="row-check row-check16" name="near_by_me_view_flag" id="near_by_me_view_flag" value="1" <?php echo ($near_by_me_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td align="center"></td>
										<td align="center"></td>
										<td align="center"></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck16" data-count="16" style="width:60px;text-align:center"></td>
									</tr>
									<!-- <tr>
												<td>Customer Meeting</td>
												<td align="center"><input type="checkbox" data-count="15" class="row-check row-check15" name="customer_meeting_view_flag" id="customer_meeting_view_flag" value="1" <?php echo ($customer_meeting_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="15" class="row-check row-check15" name="customer_meeting_insert_flag" id="customer_meeting_insert_flag" value="1" <?php echo ($customer_meeting_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="15" class="row-check row-check15" name="customer_meeting_update_flag" id="customer_meeting_update_flag" value="1" <?php echo ($customer_meeting_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="15" class="row-check row-check15" name="customer_meeting_delete_flag" id="customer_meeting_delete_flag" value="1" <?php echo ($customer_meeting_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" class="masterCheck masterCheck15" data-count="15" style="width:60px;text-align:center"></td>
											</tr> -->

									<tr>
										<td>Change Route</td>
										<td align="center"><input type="checkbox" data-count="17" class="row-check row-check17" name="change_root_view_flag" id="change_root_view_flag" value="1" <?php echo ($change_root_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="17" class="row-check row-check17" name="change_root_insert_flag" id="change_root_insert_flag" value="1" <?php echo ($change_root_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="17" class="row-check row-check17" name="change_root_update_flag" id="change_root_update_flag" value="1" <?php echo ($change_root_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="17" class="row-check row-check17" name="change_root_delete_flag" id="change_root_delete_flag" value="1" <?php echo ($change_root_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" class="masterCheck masterCheck17" data-count="17" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>My Area</td>
										<td align="center"><input type="checkbox" data-count="20" class="row-check row-check20" name="area_view_flag" id="area_view_flag" value="1" <?php echo ($area_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="20" class="row-check row-check20" name="area_insert_flag" id="area_insert_flag" value="1" <?php echo ($area_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="20" class="row-check row-check20" name="area_update_flag" id="area_update_flag" value="1" <?php echo ($area_update_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td align="center"><input type="checkbox" data-count="20" class="row-check row-check20" name="area_delete_flag" id="area_delete_flag" value="1" <?php echo ($area_delete_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										<td align="center"><input type="checkbox" class="masterCheck masterCheck20" data-count="20" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Price List </td>
										<td align="center"><input type="checkbox" data-count="22" class="row-check row-check22" name="price_list_view_flag" id="price_list_view_flag" value="1" <?php echo ($price_list_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck22" data-count="22" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Document Detail</td><!-- <td>Bank Detail</td> -->
										<td align="center"><input type="checkbox" data-count="23" class="row-check row-check23" name="bank_detail_view_flag" id="bank_detail_view_flag" value="1" <?php echo ($bank_detail_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck23" data-count="23" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Scheme</td>
										<!-- 	<td align="center"><input type="checkbox" data-count="24" class="row-check row-check24" name="scheme_view_flag" id="scheme_view_flag" value="1" <?php echo ($scheme_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td></td>
												<td></td>
												<td></td>
												<td align="center"><input type="checkbox" class="masterCheck masterCheck24" data-count="24" style="width:60px;text-align:center"></td>
											</tr>


											<tr>
												<td>Gst Details</td>
												<td align="center"><input type="checkbox" data-count="27" class="row-check row-check27" name="gst_view_flag" id="gst_view_flag" value="1" <?php echo ($gst_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
												<td></td>
												<td></td>
												<td></td>
												<td align="center"><input type="checkbox" class="masterCheck masterCheck27" data-count="27" style="width:60px;text-align:center"></td>
											</tr>
 -->
									<tr>
										<td>My Visting Card</td>
										<td align="center"><input type="checkbox" data-count="28" class="row-check row-check28" name="visit_card_view_flag" id="visit_card_view_flag" value="1" <?php echo ($visit_card_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck28" data-count="28" style="width:60px;text-align:center"></td>
									</tr>

									<tr>
										<td>Traveling</td>
										<td align="center"><input type="checkbox" data-count="29" class="row-check row-check29" name="traveling_view_flag" id="traveling_view_flag" value="1" <?php echo ($traveling_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="center"><input type="checkbox" class="masterCheck masterCheck29" data-count="29" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>My Route</td>
										<td align="center"><input type="checkbox" data-count="34" class="row-check row-check34" name="my_route_view_flag" id="my_route_view_flag" value="1" <?php echo ($my_route_view_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="34" class="row-check row-check34" name="my_route_insert_flag" id="my_route_insert_flag" value="1" <?php echo ($my_route_insert_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>


										<td align="center"><input type="checkbox" class="masterCheck masterCheck34" data-count="34" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>Customer stock</td>
										<td></td>

										<td align="center"><input type="checkbox" data-count="37" class="row-check row-check37" name="customer_stock_add_flag" id="customer_stock_add_flag" value="1" <?php echo ($customer_stock_add_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck37" data-count="37" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>Deep Freezer Scheme</td>
										<td></td>

										<td align="center"><input type="checkbox" data-count="38" class="row-check row-check38" name="deepfreezscheme_flag" id="deepfreezscheme_flag" value="1" <?php echo ($deepfreezscheme_flag == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>
										<td></td>
										<td></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck38" data-count="38" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>Monthly Order Planner</td>

										<td align="center"><input type="checkbox" data-count="43" class="row-check row-check43" name="monthlyorder_planner_view" id="monthlyorder_planner_view" value="1" <?php echo ($monthlyorder_planner_view == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="43" class="row-check row-check43" name="monthlyorder_planner_add" id="monthlyorder_planner_add" value="1" <?php echo ($monthlyorder_planner_add == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="43" class="row-check row-check43" name="monthlyorder_planner_edit" id="monthlyorder_planner_edit" value="1" <?php echo ($monthlyorder_planner_edit == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="43" class="row-check row-check43" name="monthlyorder_planner_delete" id="monthlyorder_planner_delete" value="1" <?php echo ($monthlyorder_planner_delete == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" class="masterCheck masterCheck43" data-count="43" style="width:60px;text-align:center"></td>
									</tr>
									<tr>
										<td>Consultant Process</td>

										<td align="center"><input type="checkbox" data-count="44" class="row-check row-check44" name="consultant_process_view" id="consultant_process_view" value="1" <?php echo ($consultant_process_view == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<td align="center"><input type="checkbox" data-count="44" class="row-check row-check44" name="consultant_process_add" id="consultant_process_add" value="1" <?php echo ($consultant_process_add == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td>

										<!-- <td align="center"><input type="checkbox" data-count="44" class="row-check row-check44" name="consultant_process_edit" id="consultant_process_edit" value="1" <?php echo ($consultant_process_edit == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->
										 <td></td>
											<td></td>
										<!-- <td align="center"><input type="checkbox" data-count="44" class="row-check row-check44" name="consultant_process_delete" id="consultant_process_delete" value="1" <?php echo ($consultant_process_delete == 1) ? "checked" : ""; ?> style="width:60px;text-align:center"></td> -->

										<td align="center"><input type="checkbox" class="masterCheck masterCheck44" data-count="44" style="width:60px;text-align:center"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Address Information
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="address">Address </label>
								<textarea class="form-control" rows="4" cols="10" name="address" id="address"> <?php echo $address; ?></textarea>
								<p class="help-block"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="zip">Pincode </label>
								<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $zip; ?>" maxlength="6">
								<p class="help-block"></p>
							</div>

						</div>
						<!--div class="col-md-4">
									<div class="form-group">
										<label for="imei">IMEI </label>
										<input type="text" class="form-control" name="imei" id="imei" value="<?php echo $imei; ?>">
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="refreshToken">Refresh Token </label>
										<input type="text" class="form-control" name="refreshToken" id="refreshToken" value="<?php echo $refreshToken; ?>">
										<p class="help-block"></p>
								</div-->
					</div>


					<div class="row">

						<div class="col-md-3">
							<label for="country">Country <code>*</code></label>
							<select name="country" id="country" class="form-control" onChange="return State(this.value);">
								<option value="">--Select Country--</option>
								<?php
								$country_r = $db->rp_getData("country", "*");
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
							<p class="help-block"></p>
						</div>

						<div class="col-md-3">
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
							<!-- <input type="hidden" name="area_id" id="area_id" value=""> -->
							<p class="help-block"></p>
						</div>
						<div class="col-md-3">
							<label for="city">Route <code>*</code></label>
							<select name="city" id="city" class="form-control" onChange="get_id(this.value);">
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
							<p class="help-block"></p>
						</div>


						<br />
						<!-- <div class="col-md-4">
								<div class="form-group">
									<label for="insentive_percentage">Insentive Percentage</label>
									<input type="text" class="form-control" name="insentive_percentage" id="insentive_percentage" value="<?php echo $insentive_percentage; ?>">
									<p class="help-block"></p>
								</div>
					    	</div> -->
					</div>
					<div class="row">
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
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6" hidden="">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-book"></i>Class map Area Information
					</div>
				</div>
				<div class="portlet-body" style="min-height:150px;">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-8">
								<div class="form-group">
									<label for="class_id">Class Type <code>*</code></label>
									<select class="form-control" name="class_id" id="class_id" onChange="return getArea(this.value,'#sales_manager',0)">
										<option value="">Select Class Type</option>
										<?php
										$sm_class_id = $db->rp_getValue("sales_executive", "class_id", "id='" . $sm_id . "'");
										$class_list_d = $db->rp_getData('class', "*", "1=1 AND isDelete=0 AND isActive=1 AND id='" . $sm_class_id . "'", "", 0);
										while ($class_list_r = mysqli_fetch_assoc($class_list_d)) {
										?>
											<option <?php echo ($class_id == $class_list_r['id']) ? "selected" : ""; ?> value="<?php echo $class_list_r['id'] ?>"><?php echo $class_list_r['name']; ?>
											</option>
										<?php
										}
										?>
									</select>
									<p class="help-block"></p>
								</div>
								<div class="form-group">
									<label for="class_id">Class Area <code>*</code></label></br>
									<label style="font-size:15px;"><a href='#' id='select-all'>select all/</a>
										<a href='#' id='deselect-all'>deselect all</a></label>
									<select multiple="multiple" class="multi-select" id="area_id" name="area_id[]">
										<?php
										$area_r = $db->rp_getData("sales_executive_map_area", "*", "class_id='" . $class_id . "' AND sales_executive_id='" . $sm_id . "' AND area_id!=0", "", 0);
										if ($area_r) {
											while ($area_d = mysqli_fetch_array($area_r)) {
												$area_d['name'] = $db->rp_getValue("area", "name", "id='" . $area_d['area_id'] . "'");
												$area_d['id'] = $area_d['area_id'];

										?>
												<option value="<?php echo $area_d['id']; ?>" <?php echo (in_array($area_d['id'], $areas)) ? "selected" : ""; ?> value="<?php echo $area_d['id']; ?>"><?php echo $area_d['name']; ?></option>
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
		<div class="col-md-6">
			<div class="portlet grey-cascade box">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-user"></i> &nbsp; Visiting Card
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<!-- <div class="col-md-6">
											<div class="form-group">
												<input data-image="<?php echo ($image_path != "" && file_exists(GST_VISITING_DETAIL_A . $image_path)) ? GST_VISITING_DETAIL_A . $image_path : ""; ?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
											</div>
										</div> -->

								<div class="col-md-6">
									<div class="form-group">
										<input data-image="<?php echo ($file_path != "" && file_exists(GST_VISITING_DETAIL_A . $file_path)) ? GST_VISITING_DETAIL_A . $file_path : ""; ?>" type="file" name="file_path" id="file_path" data-old-image-dom="old_file_path" data-old-image-path="<?php echo $file_path ?>" value="">
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
	<div class="row">
		<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
			<input type="hidden" value="<?php echo $type_of_inquiry; ?>" name="type_of_inquiry" />
			<input type="hidden" value="<?php echo $mode; ?>" name="mode" />
			<input type="hidden" value="<?php echo $_REQUEST['id']; ?>" name="id" />
			<button type="submit" name="submit" class="btn green">Submit</button>
			<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
		</div>
	</div>
</form>