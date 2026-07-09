<?php
$page_id=555;$page_slug='page_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$super_stockist_id=$_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 ";
$ctable_r = $db->rp_getData("executive","*",$ctable_where,"",0);

$ctable_where_branch	= "cid='".$_REQUEST['id']."' AND isDelete=0";
$ctable_branch = $db->rp_getData("executive_branch","*",$ctable_where_branch,"",0);

$ctable_where2	= "cid='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r2 = $db->rp_getData("executive_contact_person","*",$ctable_where2,"",0)

        ?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
		<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            $ctable_d = mysqli_fetch_array($ctable_r);
                $count++;
        ?>
			<h4><b>Personal Detail : </b><?php echo $ctable_d['company_name']; ?></h4>
			<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
			
			<tr>
			<th>Person Name</th>
			<td><?php echo $ctable_d['cname'];  ?></td>
			<th>Company Type</th>
			<td><?php echo  $db->rp_getValue("company_type","name","id='".$ctable_d['company_type']."'",0); ?></td>
			</tr>
			
			<tr>
			<th>Phone</th>
			<td><?php echo  $ctable_d['phone']; ?></td>									
			<th>Email</th>
			<td><?php echo  $ctable_d['email'];  ?></td>
			</tr>
			
			<tr>
			<th>CST Number</th>
			<td><?php echo  $ctable_d['cst'];  ?></td>
			<th>PAN Number</th>
			<td><?php echo  $ctable_d['pan']; ?></td>										
			</tr>
			
			<tr>
			<th>GST Number</th>
			<td><?php echo  $ctable_d['gst'];; ?></td>
			<th>VAT Number</th>
			<td><?php echo  $ctable_d['vat']; ?></td>
			</tr>
			
			<tr>
			<th>Address</th>
			<td><?php echo  $ctable_d['address']; ?></td>
			<th>Pin Code</th>
			<td><?php echo  $ctable_d['zip']; ?></td>
			</tr>
			
			<tr>
			<th>Country</th>
			<td><?php echo  $ctable_d['country'];//$db->rp_getValue("country","name","id='".$ctable_d['country']."'",0); ?></td>
			<th>State</th>
			<td><?php echo  $ctable_d['state'];//$db->rp_getValue("state","name","id='".$ctable_d['state']."'",0); ?></td>
			</tr>
			
			<tr>
			<th>City</th>
			<td><?php echo  $ctable_d['city'];//$db->rp_getValue("city","name","id='".$ctable_d['city']."'",0); ?></td>
			<th>Discount</th>
			<td><?php echo  $ctable_d['discount']; ?></td>
			</tr>
			
			</tbody>
			</table>
			<?php
			}
			?>
			</div>

			</div>
			
			
			<?php
			if($_REQUEST['type']=="super_stockist" )
			{
			?>
			<div class="row">
			<div class="col-sm-12">
			<h4><b>My Customer Information</b></h4>
			<table id="datatable_2" class="table table-striped table-bordered table-hover">
			<tbody>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Contact No.</th>
				<th>Email</th>
				<th>State</th>
				<th>City</th>
				<?php
				 $where = "type_of_executive='dealer' AND super_stockist_id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1";
				$ctable_r1=$db->rp_getData("executive","*",$where,"",0);
			if(mysqli_num_rows($ctable_r1)>0){
            
            while($ctable_d1 = mysqli_fetch_array($ctable_r1)){
           
			?>
			</tr>
			</thead>
			<tbody>
			
			<tr>
			<td><a href="#MyCustomerInfo" data-id="<?php echo  stripslashes($ctable_d1['id']); ?>" data-toggle="modal" title="View Super Stockist"><?php echo $ctable_d1['cname']; ?></td></a>
			<td><?php echo $ctable_d1['type_of_executive']; ?></td>
			<td><?php echo $ctable_d1['phone']; ?></td>
			<td><?php echo $ctable_d1['email']; ?></td>
			<td><?php echo $db->rp_getValue("state","name","id=".$ctable_d1['state'].""); ?></td>
			<td><?php echo $db->rp_getValue("city","name","id=".$ctable_d1['city'].""); ?></td>
			</tr>
			
<?php
			}
			}
			else
			{
				?>
				<tr>
				<td colspan="5" align="center"><?php echo "No Customer Found"; ?></td>
				</tr>
				<?php
			}
?>
			</tbody>
			</table>
			</div>
			</div>
			<?php
			
			}
			if($_REQUEST['type']=="dealer" )
			{
			?>
			<div class="row">
			<div class="col-sm-12">
			<h4><b>My Customer Information</b></h4>
			<table id="datatable_2" class="table table-striped table-bordered table-hover">
			<tbody>
			<tr>
				<th>Name</th>
				<th>Contact No.</th>
				<th>Email</th>
				<th>State</th>
				<th>City</th>
				<?php
			 $where = "type_of_executive='outlets' AND dealer_distributor_id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1";
			$ctable_r1=$db->rp_getData("executive","*",$where,"",0);
			if(mysqli_num_rows($ctable_r1)>0){
            
            while($ctable_d1 = mysqli_fetch_array($ctable_r1)){
           
			?>
			</tr>
			</thead>
			<tbody>
			<tr>
			<td><a href="#MyCustomerInfo" data-id="<?php echo  stripslashes($ctable_d1['id']); ?>" data-toggle="modal" title="View Super Stockist"><?php echo $ctable_d1['cname']; ?></a></td>
			<td><?php echo $ctable_d1['phone']; ?></td>
			<td><?php echo $ctable_d1['email']; ?></td>
			<td><?php echo $db->rp_getValue("satet","name","id=".$ctable_d1['state'].""); ?></td>
			<td><?php echo $db->rp_getValue("city","name","id=".$ctable_d1['city'].""); ?></td>
			</tr>
			<?php
			}
			}
			else
			{
				?>
				<tr>
				<td colspan="5" align="center"><?php echo "No Customer Found"; ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			</div>
			</div>
			<?Php
			}
			
			?>
			
			<!--div class="row">
			<div class="col-sm-12">
			<h4><b>Unit Information</b></h4>
			<table id="datatable_2" class="table table-striped table-bordered table-hover">
			<tbody>
			
			<tr>
			<th>Branch Name</th>
			// <?php
			// if(mysqli_num_rows($ctable_branch)>0){
            
            // while($ctable_branch_d = mysqli_fetch_array($ctable_branch)){
           
        // ?>
			</tr>
			</thead>
			<tbody>
			<tr>
			<td><?php echo $ctable_branch_d['branch_name']; ?></td>
			</tr>
// <?php
			// }
			// }
// ?>
			</tbody>
			</table>
			</div>
			</div>
			<div class="row">
<div class="col-sm-12">
<h4><b>Unit Contact Information</b></h4>
			<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
			<tr>
				<th>Name</th>
				<th>Designation</th>
				<th>Branch</th>
				<th>Phone</th>
				<th>Email</th>
			</tr>
			// <?php
			// if(mysqli_num_rows($ctable_r2)>0){
				// $count = 0;
				
				// while($ctable_d2 = mysqli_fetch_array($ctable_r2)){
					// $count++;
			// ?>
			
			<tr>
				<td><?php echo $ctable_d2['name']; ?></td>
				<td><?php echo  $ctable_d2['designation']; ?></td>
				<td><?php echo  $db->rp_getValue("executive_branch","branch_name","id='".$ctable_d2['branch']."'",0); ?></td>
				<td><?php echo  $ctable_d2['phone']; ?></td>									
				<td><?php echo  $ctable_d2['email']; ?></td>									
			</tr>
			
			// <?php
			// }
		// }
// ?>
			</tbody>
			</table>
	</div>

</div-->
</div>
<div class="row">
	<div class="col-md-2">
		<a onClick="printPDF('<?php echo $_REQUEST['id'];?>','<?php echo $_REQUEST['type'];?>')"  class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
	</div>
	<div class="col-md-2">
		<a class="btn btn-info" onClick="genReport('<?php echo $super_stockist_id; ?>','<?php echo $_REQUEST['type'];?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
	</div>
</div>
<?php include("disconnect.php"); ?>
