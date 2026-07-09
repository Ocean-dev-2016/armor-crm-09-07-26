<?php
$page_id=571;$page_slug='payment';
require_once('connect_in.php');
include("../include/no_to_word.php");

if(isset($_REQUEST['id']) && $_REQUEST['id']!="")
{
	$error=0;
	$id=$_REQUEST['id'];
	$bill_info_r=$db->rp_getData("customer_payment","*","id='".$id."'","",0);
	if($bill_info_r)
	{
		$bill_info=mysqli_fetch_assoc($bill_info_r);
		$bill_no=$bill_info['dispatch_id'];
		$dispatch_bill_no=$db->rp_getValue("dispatch_detail","dispatch_no","id='".$bill_no."'");
		$receipt_no=$bill_info['receipt_no'];
		$customer_id=$bill_info['customer_id'];
		$bill_paymentby=$bill_info['payment_type'];
		$customer_name=$db->rp_getValue("dispatch_detail","customer_name","customer_id='".$customer_id."'");
		$bill_date=date("d-m-Y",strtotime($bill_info['payment_date']));
		$bill_paid=$bill_info['paid_amount'];
	}
	else
	{
		$error=1;
	}
}
else
{
	$error=1;
}
?>

<html>
<head>

<style type="text/css">
body,html{
	margin:0;
	padding:0;
}

.mainDiv, table{
    height: auto;
    width:100mm;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 10px;
	margin-left:170px;
	padding:0;
	align:center;
}
table , td, th {
	border: 1px solid black;
	border-collapse: collapse;
}
    }
.bolder
{
	font-weight: 700 !important;
}
.center
{
	text-align:center;

}
.font8{	font-size: 8pt !important;}
.righter
{
	text-align:right;

}

td, th {
	padding: 3px;
	height:40px;
	text-align:left;
	vertical-align:middle;
	color: black;
    font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    text-decoration: none;
	font-size:15px;
	
}
th {
	font-style: bold;
}
.title {
	background: #f0f0f0;
}
.odd {
	background: #fefcf9;
}
input{
	height:auto;
	width:100%;
	margin:0px !import.
}
.header_logo
{
	background-image: url("lakhmi.jpg");
    background-position: center center;
    background-repeat: no-repeat;
    background-size: contain;
    height: 161px;
    width: 190mm;
}
 
</style>
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>

<script>
/*$(document).ready(function() {
if(navigator.userAgent.toLowerCase().indexOf('chrome') > -1){   // Chrome Browser Detected?
    window.PPClose = false;                                     // Clear Close Flag
    window.onbeforeunload = function(){                         // Before Window Close Event
        if(window.PPClose === false){                           // Close not OK?
            return 'Leaving this page will block the parent window!\nPlease select "Stay on this Page option" and use the\nCancel button instead to close the Print Preview Window.\n';
        }
    }                   
    window.print();                                             // Print preview
    window.PPClose = true;                                      // Set Close Flag to OK.
}
else
{
	window.print();  
}
});*/
</script>
</head>

<body>
<div class="mainDiv">
<table style="overflow: wrap;" >
	<tbody style="padding:10px;">
<?php 
	if($error==1)
	{
		?>
				<tr>
					<td colspan="9" class="center"><b>Sorry !! Software Error. Couldn't Print Bill</b>		
				</td>
				</tr>
		<?php 
	}
	else{
		?>
		<tr>
			<td colspan="12" class="center "><img src="../images/xhdpi.png" style="width:40mm; height:20mm;	"/></td>
				</tr>
				<tr>
					<th colspan="6"><b>Receipt No.:-</b></th> <td colspan="6"><?php echo $receipt_no; ?>	</td>					
				</tr>
				<tr>
					<th colspan="6"><b>Dispatch Bill No.:-</b></th> <td colspan="6"><?php echo $dispatch_bill_no; ?>	</td>					
				</tr>
				
				<tr>
					<th  colspan="6"><b>Date.:-</b></th><td colspan="6">  <?php echo $bill_date; ?></td>
				</tr>
				<tr>
					<th  colspan="6"><b>Customer Name.:-</b> </th><td colspan="6">  <?php echo $customer_name; ?></td>
				</tr>
				<tr>
					<th  colspan="6"><b>Payment By.:-</b> </th><td colspan="6">  <?php echo $bill_paymentby; ?></td>
				</tr>
				<tr>
					<th class="righter"  colspan="6"><b>Paid Amount:- </b> </th><td colspan="6" class="righter"> <?php echo CURR.$db->rp_num($bill_paid); ?></td>
				</tr>
				<tr>
					<th colspan="6" class="righter" ><b>Grand Total Rs:-</b></th>
					<td colspan="6" class="righter" ><?php echo CURR.$db->rp_num($bill_paid);?>
				</td>
				</tr>
				
		<?php 
	}

?>	
		
		
		</tbody>
</table>		
</div>

</body>
</html>