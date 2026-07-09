<?php
	include 'connect_in.php';
// print_r($_REQUEST);exit();
?>
<form method="post" name="assign_customer" id="assign_customer">
  <div class="modal-body">
		<?php
  			$no_order_inq_table_get_r = $db->rp_getData("no_order_inquiry","*","isDelete=0","",0);
			$no_order_inq_table_get_d = mysqli_fetch_assoc($no_order_inq_table_get_r);

			$company_id = $no_order_inq_table_get_d['type_of_company'];
			$customer_type = $no_order_inq_table_get_d['executive_type'];
			$customer_id = $no_order_inq_table_get_d['dealer_id'];
  		?>
  		<label>Select Company</label>
  		<input type="hidden" id="inq_company_id" name="inq_company_id" value="">
  		<select class="form-control b-3" id="company_id" name="company_id">
          <option value="" readonly>Select Company</option>
          <?php
	          	$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
	              if($company_r)
	              {
	              	while($company_d = mysqli_fetch_assoc($company_r))
	              	{
	  		?>
	              			<option <?=($company_id==$company_d['id'])?"selected":"";?> value="<?=$company_d['id']?>" data-company-id="<?=$company_d['id']?>">
	              				<?=$company_d['name']?>
	              			</option>
	  		<?php
	              	}
	              }
	          ?>
       </select> 
  </div>
  <div class="modal-body">
  		<label>Select Customer Type</label>
  		<input type="hidden" id="inq_company_type" name="inq_company_type" value="">
  		<select class="form-control b-3" id="customer_type" name="customer_type" onclick="getCustomer(this.value)">
          <option value="" readonly>Select Customer Type</option>
          <?php
              	// echo $executive_type;exit;
	          	$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
	              if($cust_R)
	              {
	              	while($C = mysqli_fetch_assoc($cust_R))
	              	{
	  		?>
              			<option <?=($customer_type==$C['id'])?"selected":"";?> value="<?=$C['id']?>" data-ctype="<?=$C['id']?>">
              				<?=$C['name']?>
              			</option>
	  		<?php
	              	}
	              }
	          ?>
       </select> 
  </div>
  <div class="modal-body">
  		<label>Select Customer</label>
  		<input type="hidden" id="inq_customer" name="inq_customer" value="">
  		<select  class="form-control customer_id_s" name="customer_id" placeholder="Select Customer" id="customer_id"   type="text" >
				<option value="">Select Customer</option>
			</select>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <input type="hidden" id="inquiry_id" value="">
    <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
  </div>
</form>