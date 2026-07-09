<?php
$page_id=555;$page_slug='page_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$super_stockist_id=$_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 ";
$ctable_r = $db->rp_getData("executive","*",$ctable_where,"",0);
 ?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
			<h4><b>Personal Detail</b></h4>
			<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
			<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
			<th>Company Name</th>
			<td><?php echo $ctable_d['cname'];  ?></td>
			<th>Company Type</th>
			<td><?php echo  $db->rp_getValue("company_type","name","id='".$ctable_d['company_type']."'",0); ?></td>
			</tr>
			
			<tr>
			<th>Contact No.</th>
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
			<td><?php echo  $db->rp_getValue("country","name","id='".$ctable_d['country']."'",0); ?></td>
			<th>State</th>
			<td><?php echo  $db->rp_getValue("state","name","id='".$ctable_d['state']."'",0); ?></td>
			</tr>
			
			<tr>
			<th>City</th>
			<td><?php echo  $db->rp_getValue("city","name","id='".$ctable_d['city']."'",0); ?></td>
			<th>Discount</th>
			<td><?php echo  $ctable_d['discount']; ?></td>
			</tr>
			<?php
				}
			}
			?>
			</tbody>
			</table>
			</div>

			</div>
			
			
			
</div>
<!--div class="row">
	<div class="col-md-2">
		<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
	</div>
	<div class="col-md-2">
		<a class="btn btn-info" onClick="genReport('<?php echo $super_stockist_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
	</div>
</div-->
<?php require_once 'disconnect.php';  ?>