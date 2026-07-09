<?php 
require_once("connecting.php");

$vendor_id	= $_REQUEST['vendor_id'];
$ctable_r 	= $db->rp_getData("vendor","*","id='".$vendor_id."'","",0);
$ctable_d 	= mysqli_fetch_Assoc($ctable_r);

?>

<html>
<head>
   <style>
   table, tr ,td, th{
	   border:1px solid black;
   }
   </style>
    
</head>

<body>

<div class="mainDiv">
<div id="customer_info">
<center>
	<table>
	
	
	<thead>
		<tr>
			<th colspan="11">Vendor Information</th>
		</tr>
		<tr>
			<th>Vendor Name</th>
			<th>Company Type</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Country</th>
			<th>State</th>
			<th>City</th>
			<th>Bank Name</th>
			<th>Account NO.</th>
			<th>IFSC Code</th>
			<th>Address</th>
		</tr>
	</thead>
		<tbody>
		<?php
		$vendor_id	= $_REQUEST['vendor_id'];
		$ctable_r 	= $db->rp_getData("vendor","*","id='".$vendor_id."'","",0);
		$ctable_d 	= mysqli_fetch_Assoc($ctable_r);
		if($ctable_d['c_type']==0){
			$ctype="department";
		}else{
			$ctype="Company";
		}

?>
			<tr>
				<td><?php echo $ctable_d['cname'];?></td>
				<td><?php echo $ctype;?></td>
				<td><?php echo $ctable_d['phone'];?></td>
				<td><?php echo $ctable_d['email'];?></td>
				<td><?php echo  $db->rp_getValue("country","name","id='".$ctable_d['country']."'",0); ?></td>
				<td><?php echo  $db->rp_getValue("state","name","id='".$ctable_d['state']."'",0); ?></td>
				<td><?php echo  $db->rp_getValue("city","name","id='".$ctable_d['city']."'",0); ?></td>
				<td><?php echo $ctable_d['bank_name'];?></td>
				<td><?php echo $ctable_d['account_no'];?></td>
				<td><?php echo $ctable_d['ifsc_code'];?></td>
				<td><?php echo $ctable_d['address'];?></td>
			</tr>
		</tbody>
	</table>
	</center>
</div>

</body>
</html>