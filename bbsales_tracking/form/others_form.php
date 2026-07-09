<form id="inquiry_form_distributor" action="executive_form_function.php" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="col-md-6 col-sm-12">
		<div class="portlet grey-cascade box">
			<div class="portlet-title">
				<div class="caption">
				   <i class="fa fa-user"></i> &nbsp; Other Information
				</div>
			</div>
			<div class="portlet-body">
			   
				<div class="row">																													
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<label>Select Price List</label>
									<select class="form-control" id="price_list_id" name="price_list_id">
										<option value="">Select Pricelist</option>
										<?php
										$price_list_r=$db->rp_getData("price_list","*","isDelete=0 AND isActive=1");
										if($price_list_r)
										{
											while($price_list_d=mysqli_fetch_assoc($price_list_r))
											{
												?>
												<option <?= ($price_list_id==$price_list_d['id'])?"selected":"";?> value="<?= $price_list_d['id']; ?>"><?= $price_list_d['pricelist_name']; ?></option>
												<?php
											}
										}
										?>
									</select>
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
									<label >Phone <code>*</code></label>
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
									<input type="text" class="form-control" name="whatsapp_no" id="whatsapp_no" value="<?php echo $whatsapp_no; ?>" maxlength="10" size="10">
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
								
								<div class="col-md-6" <?php if($mode=='edit'){ ?> hidden <?php } ?>>
									<div class="form-group">
										<label>Password</label>
										<input type="password" class="form-control" autocomplete="new-password" name="password" id="password" value="<?php echo $password; ?>">
										<p class="help-block"></p>	
									</div>
								</div>	
																				
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="address">Address </label><code>*</code>
										<textarea class="form-control" name="address" id="address" ><?php echo $address; ?></textarea>
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
								<!--div class="col-md-6">
									<div class="form-group">
										<label for="discount">Discount % </label><code>*</code>
										<input type="text" class="form-control" name="discount" id="discount" value="<?php echo $discount; ?>" >
										<p class="help-block"></p>
									</div>
										
								</div-->
								<div class="col-md-6">
									<div class="form-group">
										<label>Company Name </label><code>*</code>
										<input type="text" class="form-control" name="company_name" id="company_name" value="<?php echo $company_name; ?>">
										<p class="help-block"></p>	
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<input data-image="<?php echo ($image_path!="" && file_exists(SUPER_STOCKIST_A.$image_path))?SUPER_STOCKIST_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
									</div>
								</div>	
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-md-4">
										<label for="country">Country</label>
										<select name="country" id="country" class="form-control" onChange="return State(this.value);">
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
									<div class="col-md-4">
										<label for="state">State</label>
										<select name="state" id="state" class="form-control" onChange="return City(this.value);">
											<option value="">--Select State--</option>	
												<?php
												if($mode=='edit')
												{
												    $state_r = $db->rp_getData("class","*","isDelete=0","",0);
												    if(mysqli_num_rows($state_r)>0)
												    {
    												    while($state_d = mysqli_fetch_array($state_r))
    												    {?>
    												        <option <?php if($state_d['name']==$state){ echo "selected"; }  ?> value="<?php echo $state_d['name']; ?>"><?php echo $state_d['name']; ?></option>
    												    <?php    
    												    }
												        
												    }
												}
												?>
											</select>
									</div>
									<div class="col-md-4">
										<label for="city">City</label>
										<select name="city" id="city" class="form-control">
											<option value="">--Select City--</option>	
												<?php
        										if($mode=='edit')
        										{
        										$city_name=$db->rp_getValue("area","name","name='".$city."'",0);
        										?>
        										<option value="<?php echo $city; ?>" "<?php echo $city_name ?>" selected > <?php echo $city_name; ?>  </option>
        										<?php
        										}
        										?>
											</select>
									</div>
									<br/>
									<br/>
									
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
						<select class="form-control" name="company_type" id="company_type" >
							<option value="">Select Company Type</option>
							<?php 
								$company_list_d=$db->rp_getData('company_type',"*","1=1","",0);
								while($company_list_r=mysqli_fetch_assoc($company_list_d))
								{
									?>
									<option <?php echo ($company_type==$company_list_r['id'])?"selected":"" ; ?> value="<?php echo $company_list_r['id']?>">
									<?php echo $company_list_r['name'];?>
									</option>
									<?php
								}
							?>
						</select>
						<p class="help-block"></p>	
					</div>
					<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>CST Number</label>
										<input type="text" class="form-control" name="cst" id="cst" value="<?php echo $cst; ?>">
										<p class="help-block"></p>	
									</div>
								</div>		
								<div class="col-md-6">
									<div class="form-group">
										<label>GST Number</label>
										<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $gst; ?>" maxlength="15" size="15">
										<p class="help-block"></p>	
									</div>
								</div>														
							</div>
							<div class="row">
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
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>PAN Number</label>
										<input type="text" class="form-control" name="pan" id="pan" value="<?php echo $pan; ?>">
										<p class="help-block"></p>	
									</div>
								</div>	
								<div class="col-md-6">
									<div class="form-group">
										<label>Other Contact</label>
										<input type="text" class="form-control" name="other_mobile" id="other_mobile" value="<?php echo $other_mobile; ?>"  maxlength="10" size="10">
										<p class="help-block"></p>	
									</div>
								</div>														
							</div>
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
						<label for="class_id">Class Type<code>*</code></label>
						<select class="form-control" name="class_id" id="class_id" onChange="return getArea(this.value)">
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
							
								<label for="area_id">Area Type <code>*</code></label></br>
								<label style="font-size:15px;"><a href='#' id='select-all'>select all/</a>
								<a href='#' id='deselect-all'>deselect all</a></label>
									<select multiple="multiple" class="multi-select" id="area_id" name="area_id[]">
												
									<?php
											$area_r = $db->rp_getData("area","*","class_id='".$class_id." AND isDelete=0 AND isActive=1'","",0);
											if($area_r){
												while($area_d = mysqli_fetch_array($area_r)){
													
											?>
												<option <?php echo (in_array($area_d['id'],$areas))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
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
<script>
	
</script>