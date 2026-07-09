<?php
$page_id=514;$page_slug='page_bill';
 
require_once('connect.php');
if(isset($_REQUEST['id']) && $_REQUEST['id']!="")
{
	$error=0;
	$id=$_REQUEST['id'];
	$bill_info_r=$db->rp_getData("payment","*","id='".$id."'","",0);
	if($bill_info_r)
	{
		$bill_info=mysqli_fetch_assoc($bill_info_r);
		$bill_no=$bill_info['dispatch_id'];
		$customer_id=$bill_info['customer_id'];
		$bill_paymentby=$bill_info['payment_type'];
		$customer_name=$db->rp_getValue("dispatch_detail","customer_name","customer_id='".$customer_id."'");
		$bill_date=date("d-m-Y",strtotime($bill_info['payment_date']));
		$bill_paid=$bill_info['paid_amount'];
		
		// Bill Items
		
		
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
	margin-left:200px;
	padding:0;
}
.mainDiv, table{
    height: auto;
    width:75mm;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 8px;
	margin:0;
	padding:0;
}
table , td, th {
	border: 1px solid #595959;
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
	height:30px;
	text-align:left;
	vertical-align:middle;
	color: black;
    font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    text-decoration: none;
	font-size:12px;
	
}
th {
	background: #f0e6cc;
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
$(document).ready(function() {
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
});
</script>
</head>

<body>
<div class="mainDiv">



<table style="overflow: wrap" >
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
			<td colspan="12" class="center "><img src="logo.png" style="width:30mm; height:15mm;	"/></td>
				</tr>
				<tr>
					<td colspan="6">Bill No.:-</td> <td colspan="6"><?php echo $bill_no; ?>	</td>					
				</tr>
				<tr>
					<td  colspan="6">Date.:-</td><td colspan="6">  <?php echo $bill_date; ?></td>
				</tr>
				<tr>
					<td  colspan="6">Customer Name.:- </td><td colspan="6">  <?php echo $customer_name; ?></td>
				</tr>
				<tr>
					<td  colspan="6">Payment By.:- </td><td colspan="6">  <?php echo $bill_paymentby; ?></td>
				</tr>
				<tr>
					<td  colspan="6">Paid Amount:-  </td><td colspan="6" class="righter"> <?php echo $bill_paid; ?></td>
				</tr>
				<tr>
					<td colspan="6" class="righter" >Grand Total Rs:-							
								
		</td>
					<td colspan="3" class="righter" ><?php echo $bill_paid;?>

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