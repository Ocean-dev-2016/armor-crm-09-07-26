<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$ctable = 'executive';
$where="isDelete=0";

if($_REQUEST['type'] != "")
{
	$where .= " AND  type_of_executive ='".$_REQUEST['type']."'";
	
}

if($_REQUEST['customer_id'] != "")
{
	$where .= " AND  id ='".$_REQUEST['customer_id']."'";
	$customer_id = $_REQUEST['customer_id'];
	
}

if ($_REQUEST['state'] != "" && !empty($_REQUEST['state'])) 
{
	$where .= " AND state = '".$_REQUEST['state']."' ";
}

if ($_REQUEST['city'] != "" && !empty($_REQUEST['city'])) 
{
	$where .= " AND city = '".$_REQUEST['city']."' ";
}

$data_r = $db->rp_getData($ctable,"*",$where,"",0);
?>
<style type="text/css">
	.fix-th
    {
    	background-color: #f5f5f5 !important;
        position: sticky;
        top: 0; 
        z-index: 1;
    }
    .fix-th1
    {
        position: sticky;top: 0; z-index: 1;
    }
</style>
	<div class="portlet light div-set-height" style=" overflow:auto;height: 402px;margin-top: 10px">
		<div class="portlet-title">
			<div class="caption caption-md customerType">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase  "><?= ($_REQUEST['type'] == 2 )?"Distributor":"Dealer"; ?></span>
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getcustomerData();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3" style="margin-bottom: 10px;">
							<select class="form-control customer_id" name="customer_id" id="customer_id" value="<?php echo $customer_id;?>" <?php echo $disabled; ?>>
								<option value="">Select Customer Name</option>
								<?php 
								$customer_r = $db->rp_getData("executive","cname,id,company_name","isDelete=0 AND isActive=1");
								while($customer_d=mysqli_fetch_assoc($customer_r))
								{
									?>
									<option <?php if($customer_d['id']==$customer_id){?> selected <?php } ?>  value="<?php echo $customer_d['id']?>"><?php echo $customer_d['company_name']." - ". $customer_d['cname'] ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3" style="margin-bottom: 10px;">
							<select class="form-control state" name="state" id="state" onChange="filter_state(this.value);" value="<?php echo $state;?>" <?php echo $disabled; ?>>
								<option value="">Select State</option>
								<?php 
								$state_r = $db->rp_getData("state","name,id","isDelete=0","",0);
								while($state_d=mysqli_fetch_assoc($state_r))
								{
									?>
									<option <?php if($state_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $state_d['name']?>"><?php echo $state_d['name'] ?></option>
									<?php
								}
								?>
							</select>
						</div>

						<div class="col-sm-3" style="margin-bottom: 10px;">
							<select class="form-control city" name="city" id="city" value="<?php echo $city;?>" <?php echo $disabled; ?>>
								<option value="">Select City</option>
								<?php 
								$city_r = $db->rp_getData("city","name,id","isDelete=0");
								while($city_d=mysqli_fetch_assoc($city_r))
								{
									?>
									<option <?php if($city_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $city_d['name']?>"><?php echo $city_d['name'] ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<br>
							<table class="table" id="sales_target_datatables">
							  <thead  class="fix-th">
							    <tr>
							      <th scope="col">Sr.No</th>
							      <th scope="col">Customer</th>
							      <th scope="col">State</th>
							    </tr>
							  </thead>
							  <tbody>
							  	<?php
							  		$cnt=0;
							  		while($data_d = mysqli_fetch_assoc($data_r)) 
							  		{
							  			$cnt++;
							  			?>
									    <tr>
									      <th scope="row"><?= $cnt ?></th>
									      <td><?= $data_d['company_name']."(".$data_d['cname'].")"; ?></td>
									      <td><?= $data_d['state']; ?></td>
									    </tr>
							  			<?php
							  		}
							  	?>
							  </tbody>
							</table>

						<br>
					</div>
					<br>
					
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$("#state").select2();
		$("#customer_id").select2();
		$("#city").select2();
	</script>
	<?php require_once 'disconnect.php';  ?>