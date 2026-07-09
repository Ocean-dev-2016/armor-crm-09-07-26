<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$ctable = 'account_transaction';
$target_where="isDelete=0 GROUP BY reference_id ";

if($_REQUEST['customer_id'] != "")
{
	$target_where .= " AND  reference_id ='".$_REQUEST['customer_id']."'";
	$selected_id = $_REQUEST['customer_id'];
}

$outstanding_data_r = $db->rp_getData($ctable,"*",$target_where,"",0);
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
				<span class="caption-subject font-dark bold uppercase"> OutStanding</span>
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getoutstanding();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3" style="margin-bottom: 10px;">
							<?php
							$sales_person_data=array();
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								$selected_id = $_REQUEST['customer_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control customer_id" name="customer_id" id="customer_id" value="<?php echo $customer_id;?>" <?php echo $disabled; ?>>
								<option value="">Select Customer</option>
								<?php 
								$customer_r = $db->rp_getData("executive","company_name,id,cname","isDelete=0 AND isActive=1");
								while($customer_d=mysqli_fetch_assoc($customer_r))
								{
									?>
									<option <?php if($customer_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $customer_d['id']?>"><?php echo $customer_d['company_name']." - ". $customer_d['cname'] ?></option>
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
							      <th scope="col">Phone No</th>
							      <th scope="col">Outstanding</th>
							      <th scope="col">Email ID</th>
							    </tr>
							  </thead>
							  <tbody>
							  	<?php
							  		$cnt=0;
							  		$credit = 0;
							  		$debit = 0;
							  		while($outstanding_data_d = mysqli_fetch_assoc($outstanding_data_r)) 
							  		{
							  			
							  			$credit = $db->rp_getValue($ctable,"SUM(credit)","isDelete=0 AND isActive=1 AND reference_id='".$outstanding_data_d['reference_id']."' ");

							  			$debit = $db->rp_getValue($ctable,"SUM(debit)","isDelete=0 AND isActive=1 AND reference_id= '".$outstanding_data_d['reference_id']."' ");

							  			if ($debit == 'null') 
							  			{
							  				$debit = 0;
							  			}

							  			if ($credit == 'null') 
							  			{
							  				$credit = 0;
							  			}
							  			$total_credit = $credit;
							  			$pending = $total_credit - (-0-$debit);
							  			$pending = round($pending,2);
							  			if ($pending<0) 
							  			{
							  				$cnt++;
							  				$total += round($pending,2);
							  				$customer_company = $db->rp_getValue("executive","company_name","id = '".$outstanding_data_d['reference_id']."'");
							  				$customer_name = $db->rp_getValue("executive","cname","id='".$outstanding_data_d['reference_id']."' ");
							  				$customer_phone = $db->rp_getValue("executive","phone","id='".$outstanding_data_d['reference_id']."' ");

							  				$customer_email = $db->rp_getValue("executive","email","id='".$outstanding_data_d['reference_id']."' ");
							  				if ($customer_name != "") 
							  				{
							  					$customer = $customer_company. "(".$customer_name.") ";
							  				}
							  				else
							  				{
							  					$customer = $customer_company;
							  				}
							  			?>
									    <tr>
									      <th scope="row"><?= $cnt ?></th>
									      <td><?= $customer; ?></td>
									      <td><?= $customer_phone; ?></td>
									      <td align="right"><?= $db->rp_num(round(-0-$pending,2)); ?></td>
									      <td><?= $customer_email ?></td>
									    </tr>
							  			<?php
							  			}
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
		$("#customer_id").select2();
	</script>
<?php
$db->disconnect(); 
?>