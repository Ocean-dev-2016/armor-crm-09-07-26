<?php
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
?>
<div class="col-md-12 col-sm-12">
<div class="portlet light ">
	<div class="portlet-title">
		<div class="caption ">
			<span class="caption-subject bold uppercase font-dark">Orders</span>
			<span class="caption-helper">monthly order stats...</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-sm-12 pull-right">
			<div class="col-sm-4">
			<select onChange="" class="form-control" name="year_order" id="year_order">
					<option value="">Select Year </option>
					<?php 
						$reg_year=date("Y","2017");
						$curr_year=date("Y");
						$current_date=date('Y-m-d');
						$adate1 = date('Y', strtotime($current_date));
							//echo $adate1;
						for ($i=$curr_year-$reg_year; $i>=0;$i--) {
							
							?>
							
							<option <?php echo ($i==$curr_year-$reg_year)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
							<?php
							
						}
					?>
					</select>
			</div>
			<div class="col-sm-4">
				<select class="form-control" name="month_order" id="month_order">
				<option value="">Select Month</option>
				<?php 
					$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
					
					foreach ($months as $month) {
						?>
						<option <?php echo ($month==date("F"))?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
						<?php
						
					}
				?>
				</select>
				
			</div>
			<div class="col-xs-1">
				<a href="javascript:;"  onClick="return Graph.init();" class="btn btn-circle red-sunglo ">
					<i class="fa fa-refresh"></i> Refresh </a>
				
			</div>
		</div>
		
		</div>
		<div id="dashboard_amchart_3" class="CSSAnimationChart" style="height: auto"></div>
	</div>
</div>
</div>
	<div class="col-md-12 col-sm-12">
		<div class="portlet light ">
			<div class="portlet-title">
				<div class="caption caption-md">
					<i class="icon-bar-chart font-dark hide"></i>
					<span class="caption-subject font-dark bold uppercase">Orders Product Detail</span>
					<span class="caption-helper">monthly stats...</span>
				</div>
				<!--div class="actions">
					<a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
				</div-->
			</div>
			<div class="portlet-body">
				<div class="row">
					<div class="col-sm-12 pull-right">
						<div class="row">
							<div class="col-sm-3">
								<select onChange="" class="form-control" name="year_sales_item" id="year_sales_item" >
									<option value="">Select Year </option>
									<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) {
									?>
										<option <?php echo ($i==$curr_year-$reg_year)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="month_sales_item" id="month_sales_item">
									<option value="">Select Month</option>
									<?php 
									$months = array("January", "February", "March", "April", "May", "June","july","August","September ","October ","November","December");
									foreach ($months as $month) {
									?>
										<option <?php echo ($month==date("F"))?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
										<?php
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="customer_id" id="customer_id" value="<?php echo $customer_id;?>">
									<option value="">Select Customer</option>
									<?php 
									$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
									while($cus_d=mysqli_fetch_assoc($cus_r))
									{
										?>
										<option <?php if($cus_d['id']==$_REQUEST['uid']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['cname']?></option>
										<?php
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="sales_id" id="sales_id" value="<?php echo $sales_id;?>">
									<option value="">Select Executive</option>
									<?php 
									$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0","name ASC",0);
									while($sales_d=mysqli_fetch_assoc($sales_r))
									{
										?>
										<option <?php if($sales_d['id']==$_REQUEST['uid']){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="row" style="margin-top: 10px">
							<div class="col-sm-4" >
								<div class="input-group">
									<input type="text"  class="form-control" name="order_product_name" id="order_product_name" value="" placeholder="Enter Product Name"/>
								</div>
							</div>
							<div class="col-sm-6">
								<a href="javascript:;"  onClick="return Graph.init();" class="btn btn-circle red-sunglo ">
									<i class="fa fa-refresh"></i> Refresh </a>
							</div>
						</div>
					</div>
				</div>
				<div  class="table-scrollable table-scrollable-borderless" id="product_item_container">
				</div>
			</div>
		</div>
	</div>
</div>

<?php
}
?>