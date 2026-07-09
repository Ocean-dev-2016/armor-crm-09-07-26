<?php 
// print_r($_REQUEST);exit;
$page_id=556;$page_slug='page_sales_executive';
include("connect.php");
$ctable = 'target';
$sales_where = " isDelete=0 AND isActive=1 ";
$target_where = " isDelete=0 AND isActive=1 ";
if($_REQUEST['target_year'] =="")
{
	$_REQUEST['target_year']=date("Y");
}
$target_where="isDelete=0";

if($_REQUEST['target_year'] != "")
{
	$target_where.="  AND  target_year ='".$_REQUEST['target_year']."'";
}

if($_REQUEST['target_month'] != "")
{
	$target_where.="  AND  target_month = '".$_REQUEST['target_month']."'";
}
if($_REQUEST['target_sales_id'] != "")
{
	$target_where .= " AND  sales_executive_id ='".$_REQUEST['target_sales_id']."'";
}

$sales_person_data_r = $db->rp_getData("target","*",$target_where,"",0);
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
	<div class="portlet light div-set-height" style=" overflow:auto;">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Sales Person Target</span>
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return gettarget();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3" style="margin-bottom: 20px;">
							<select onChange="" class="form-control" name="target_year" id="target_year" >
								<option value="">Select Year </option>
								<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) 
									{
										?>
										<option <?php echo ($_REQUEST['target_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
								?>
							</select>
						</div>
						<div class="col-sm-3" style="margin-bottom: 20px;">
							<select class="form-control" name="target_month" id="target_month">
								<option value="">Select Month</option>
								<?php 
								
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo ($month == $selected_month)?"selected":"" ; ?> value="<?php echo $month ;?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3" style="margin-bottom: 20px;">
							<?php
							$sales_person_data=array();
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								$selected_id = $_REQUEST['target_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control target_sales_id" name="sales_id" id="target_sales_id" value="<?php echo $sales_id;?>" <?php echo $disabled; ?>>
								<option value="">Select Executive</option>
								<?php 
								$sales_r = $db->rp_getData("sales_executive","name,id","isDelete=0 AND isActive=1");
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
									?>
									<option <?php if($sales_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
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
							      <th scope="col">Sales Person</th>
							      <th scope="col">Mobile No</th>
							      <th scope="col">Target Month</th>
							      <th scope="col">Total Target</th>
							      <th scope="col">Achive Target</th>
							      <th scope="col">Pending Target</th>
							    </tr>
							  </thead>
							  <tbody>
							  	<?php
							  			$cnt=0;
							  		while($sales_person_data_d = mysqli_fetch_assoc($sales_person_data_r)) 
							  		{
							  			$cnt++;
							  			?>
									    <tr>
									      <th scope="row"><?= $cnt ?></th>
									      <td><?= $db->rp_getValue("sales_executive","name","id= '".$sales_person_data_d['sales_executive_id']."' ") ?></td>
									      <td><?= $db->rp_getValue("sales_executive","phone","id= '".$sales_person_data_d['sales_executive_id']."' ") ?></td>
									      <td><?= $sales_person_data_d['target_month'] ?></td>
									      <td><?= $sales_person_data_d['target_amount'] ?></td>
									      <td> - </td>
									      <td> - </td>
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
		$("#target_year").select2();
		$("#target_month").select2();
		$("#target_sales_id").select2();
		// $(".order_customer_id").select2();
	</script>
<?php
$db->disconnect(); 
?>