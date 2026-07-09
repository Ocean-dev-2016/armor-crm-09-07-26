<?php
$page_id=408;$page_slug='page_vendor';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable_where	= "vid='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("vendor_contact_person","*",$ctable_where,"",0);

        ?>
<div class="row">
<div class="col-sm-12">
			<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
		
			<tr>
				<th>Name</th>
				<th>Designation</th>
				<th>Branch</th>
				<th>Phone</th>
				<th>Email</th>
			</tr>
			<?php
			if(mysqli_num_rows($ctable_r)>0){
				$count = 0;
				
				while($ctable_d = mysqli_fetch_array($ctable_r)){
					$count++;
			?>
			
			<tr>
				<td><?php echo $ctable_d['name']; ?></td>
				<td><?php echo  $ctable_d['designation']; ?></td>
				<td><?php echo   $db->rp_getValue("vendor_branch","branch_name","id='".$ctable_d['branch']."'",0); ?></td>
				<td><?php echo  $ctable_d['phone']; ?></td>									
				<td><?php echo  $ctable_d['email']; ?></td>									
			</tr>
			
			<?php
			}
		}
?>
			</tbody>
			</table>
	</div>

</div>
<div class="row">
		<div class="col-md-2">
			<a class="btn btn-info" onClick="window.location.href='add_<?php echo $etable; ?>.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="window.location.href='add_<?php echo $etable; ?>.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>
			