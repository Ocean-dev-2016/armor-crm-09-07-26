<form id="inquiry_form_distributor" onSubmit="return check_form()" action="sales_executive_form_function.php" method="post" enctype="multipart/form-data">

<div class="row">
	<div class="col-md-6 " >
		<div class="portlet grey-cascade box">
			<div class="portlet-title">
				<div class="caption">
				   <i class="fa fa-user"></i> &nbsp;  Regional Sales Manager
				</div>
			</div>
			<div class="portlet-body">
			   <div class="row">																													
						<div class="col-md-12">
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
										<label >Phone <code>*</code></label>
										<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>"  maxlength="10">
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
							
								<div class="col-md-6" <?php if($mode=='edit'){ ?> hidden <?php } ?>>
									<div class="form-group">
										<label>Password <code>*</code></label>
										<input type="password" class="form-control" name="password" id="password" value="<?php echo $password; ?>">
										<p class="help-block"></p>	
									</div>
								</div>	
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Email </label>
										<input type="text" class="form-control" name="email" id="email" value="<?php echo $email; ?>">
										<p class="help-block"></p>	
									</div>
								</div>														
																						
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Min. Working Start Time <code>*</code></label>
										<input type="text" class="form-control timepicker" name="executive_in_min" id="executive_in_min" value="<?php echo $executive_in_min; ?>">
										<p class="help-block"></p>	
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Max. Working Start Time <code>*</code></label>
										<input type="text" class="form-control timepicker" name="executive_in_max" id="executive_in_max" value="<?php echo $executive_in_max; ?>">
										<p class="help-block"></p>	
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Working End Time <code>*</code></label>
										<input type="text" class="form-control timepicker" name="executive_out" id="executive_out" value="<?php echo $executive_out; ?>">
										<p class="help-block"></p>	
									</div>
								</div>								
																						
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
							<th align="center">Insert Flag</th>
							<th align="center">Update Flag</th>
							<th align="center">Delete Flag</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<td>Super Stockist Order</td>
							<td align="center"><input type="checkbox" name="super_stokist_order_insert_flag" id="super_stokist_order_insert_flag" value="1" <?php echo ($super_stokist_order_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							
							<td align="center"><input type="checkbox" name="super_stokist_order_update_flag" id="super_stokist_order_update_flag" value="1" <?php echo ($super_stokist_order_update_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							</tr>
							
							<tr>
							<td >Dealer Order</td>
							<td align="center"><input type="checkbox" name="dealer_order_insert_flag" id="dealer_order_insert_flag" value="1" <?php echo ($dealer_order_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							
							<td align="center"><input type="checkbox" name="dealer_order_update_flag" id="dealer_order_update_flag" value="1" <?php echo ($dealer_order_update_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							</tr>
							
							<tr>
							<td>Outlets Order</td>
							<td align="center"><input type="checkbox" name="outlets_order_insert_flag" id="outlets_order_insert_flag" value="1" <?php echo ($outlets_order_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							
							<td align="center"><input type="checkbox" name="outlets_order_update_flag" id="outlets_order_update_flag" value="1" <?php echo ($outlets_order_update_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							</tr>
							
							<tr>
							<td>Inquiry</td>
							<td align="center"><input type="checkbox" name="inquiry_insert_flag" id="inquiry_insert_flag" value="1" <?php echo ($inquiry_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							
							<td align="center"><input type="checkbox" name="inquiry_update_flag" id="inquiry_update_flag" value="1" <?php echo ($inquiry_update_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							
							<td align="center"><input type="checkbox" name="inquiry_delete_flag" id="inquiry_delete_flag" value="1" <?php echo ($inquiry_delete_flag==1)?"checked":""; ?> 	style="width:60px;text-align:center"></td>
							</tr>
							
							<tr>
							<td>Customer</td>
							<td align="center"><input type="checkbox" name="customer_insert_flag" id="customer_insert_flag" value="1" <?php echo ($customer_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td align="center"><input type="checkbox" name="customer_update_flag" id="customer_update_flag" value="1" <?php echo ($customer_update_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							</tr>

							<tr>
							<td>Attendance </td>
							<td align="center"><input type="checkbox" name="attendance_insert_flag" id="attendance_insert_flag" value="1" <?php echo ($attendance_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Expanse</td>
							<td align="center"><input type="checkbox" name="expense_insert_flag" id="expense_insert_flag" value="1" <?php echo ($expense_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Add Area</td>
							<td align="center"><input type="checkbox" name="add_area_insert_flag" id="add_area_insert_flag" value="1" <?php echo ($add_area_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Price List </td>
							<td align="center"><input type="checkbox" name="price_list_insert_flag" id="price_list_insert_flag" value="1" <?php echo ($price_list_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Gst Details</td>
							<td align="center"><input type="checkbox" name="gst_insert_flag" id="gst_insert_flag" value="1" <?php echo ($gst_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Visting Card</td>
							<td align="center"><input type="checkbox" name="visit_card_insert_flag" id="visit_card_insert_flag" value="1" <?php echo ($visit_card_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Export Database</td>
							<td align="center"><input type="checkbox" name="export_db_insert_flag" id="export_db_insert_flag" value="1" <?php echo ($export_db_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Reports</td>
							<td align="center"><input type="checkbox" name="reports_insert_flag" id="reports_insert_flag" value="1" <?php echo ($reports_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Visit</td>
							<td align="center"><input type="checkbox" name="visit_insert_flag" id="visit_insert_flag" value="1" <?php echo ($visit_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Complain</td>
							<td align="center"><input type="checkbox" name="complain_insert_flag" id="complain_insert_flag" value="1" <?php echo ($complain_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Task</td>
							<td align="center"><input type="checkbox" name="task_insert_flag" id="task_insert_flag" value="1" <?php echo ($task_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
							</tr>

							<tr>
							<td>Discount</td>
							<td align="center"><input type="checkbox" name="discount_insert_flag" id="discount_insert_flag" value="1" <?php echo ($discount_insert_flag==1)?"checked":""; ?> style="width:60px;text-align:center"></td>
							<td></td>
							<td></td>
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
				   <i class="fa fa-user"></i> &nbsp;  Address Information
				</div>
			</div>
			<div class="portlet-body">
			   
				<div class="row">																													
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="address">Address </label>
										<textarea type="text" class="form-control" name="address" id="address" rows="4" ><?php echo $address; ?></textarea>
										<p class="help-block"></p>	
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="zip">Pincode </label>
										<input type="text" class="form-control" name="zip" id="zip" value="<?php echo $zip; ?>"  maxlength="6">
										<p class="help-block"></p>
									</div>
										
								</div>
								<!--div class="col-md-4">
									<div class="form-group">
										<label for="refreshToken">Refresh Token </label>
										<input type="text" class="form-control" name="refreshToken" id="refreshToken" value="<?php echo $refreshToken; ?>">
										<p class="help-block"></p>
								</div>
										
								</div>
								<!--div class="col-md-4">
									<div class="form-group">
										<label for="imei">IMEI </label>
										<input type="text" class="form-control" name="imei" id="imei" value="<?php echo $imei; ?>">
										<p class="help-block"></p>
									</div>
								</div-->
										
								</div>
							</div>
							</div>
							
						<div class="row">
							<div class="col-md-4">
							<div class="form-group">
								<label for="country">Country</label>
								<select name="country" id="country" class="form-control" onChange="aj.fetchState(this.value,'#state',function(mode,result){callbackState(mode,result)});">
									<option value="">--Select Country--</option>
									<?php
									$country_r = $db->rp_getData("country","*");
									if(mysqli_num_rows($country_r)>0){
										while($country_d = mysqli_fetch_array($country_r)){
										?>
									<option value="<?php echo $country_d['name']; ?>" <?php if($country_d['name']==$country){?> selected <?php } ?>><?php echo $country_d['name']; ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
							</div>
							<div class="col-md-4">
							<div class="form-group">
								<label for="state">State</label>
								<select name="state" id="state" class="form-control"  onChange="aj.fetchCity(this.value,'#city',function(mode,result){callbackCity(mode,result)});">
										<option value="">--Select State--</option>
										<?php
										if($mode=='edit')
										{
										$state_name=$db->rp_getValue("state","name","name='".$state."'",0);
										?>
										<option value="<?php echo $state; ?>" "<?php echo $state_name ?>" selected > <?php echo $state_name; ?>  </option>
										<?php
										}
										?>
									</select>
							</div>
							</div>
							<div class="col-md-4">
							<div class="form-group">
								<label for="city">City</label>
								<select name="city" id="city" class="form-control">
										<option value="">--Select City--</option>
										<?php
										if($mode=='edit')
										{
										$city_name=$db->rp_getValue("city","name","name='".$city."'",0);
										?>
										<option value="<?php echo $city; ?>" "<?php echo $city_name ?>" selected > <?php echo $city_name; ?>  </option>
										<?php
										}
										?>
									</select>
							</div>
							<br/>
							
							
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="insentive_percentage">Insentive Percentage</label>
								<input type="text" class="form-control" name="insentive_percentage" id="insentive_percentage" value="<?php echo $insentive_percentage; ?>">
								<p class="help-block"></p>
							</div>
					    </div>
						
					</div>																				
							
						</div>
					</div>
			</div>
	<div class="col-md-6 pull-right"">
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
										<select class="form-control" name="class_id" id="class_id" onChange="return getArea(this.value,'#sm_id',1)">
											<option value="">Select Class Type</option>
											<?php 
											$class_list_d=$db->rp_getData('class',"*","1=1 AND isDelete=0 AND isActive=1","",0);
											while($class_list_r=mysqli_fetch_assoc($class_list_d))
											{
												?>
												<option <?php echo ($class_id==$class_list_r['id'])?"selected":"" ; ?> value="<?php echo $class_list_r['id']?>">
												<?php echo $class_list_r['name'];?>
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
										<select multiple="multiple" class="multi-select" id="area_id" name="area_id[]" >
										<option value=""></option>
											<?php
											$area_r = $db->rp_getData("area","*","class_id='".$class_id."' AND isDelete=0 AND isActive=1","",0);
											if($area_r){
												while($area_d = mysqli_fetch_array($area_r)){
													
											?>
												<option  value="<?php echo $area_d['id']; ?>"  <?php echo (in_array($area_d['id'],$areas))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
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
				   <i class="fa fa-user"></i> &nbsp;  Gst Details And Visiting Card
				</div>
			</div>
			<div class="portlet-body">
			   	<div class="row">
			   	    <div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<input data-image="<?php echo ($image_path!="" && file_exists(GST_VISITING_DETAIL_A.$image_path))?GST_VISITING_DETAIL_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
								</div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<input data-image="<?php echo ($file_path!="" && file_exists(GST_VISITING_DETAIL_A.$file_path))?GST_VISITING_DETAIL_A.$file_path:"";?>" type="file" name="file_path" id="file_path" data-old-image-dom="old_file_path" data-old-image-path="<?php echo $file_path ?>" value="" >
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
<?php if(!$disabled){?>
		
		<input type="hidden" value="<?php echo $type_of_inquiry;?>" name="type_of_inquiry"/>
		<input type="hidden" value="<?php echo $mode;?>" name="mode"/>
		<input type="hidden" value="<?php echo $_REQUEST['id'];?>" name="id"/>
	<button type="submit" name="submit" class="btn green">Submit</button>
	<?php }?>
	<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
</div>
</div>
</form>