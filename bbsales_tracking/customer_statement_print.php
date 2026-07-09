<?php
$page_id=618;$page_slug='outstanding_report';

include("connect.php");

$zone_id=($_REQUEST['zone']);

if($zone_id!="")
{
    $zone_where=" AND zone=".$zone_id;
}
else
{
    $zone_where="";
}

$customer_r = $db->rp_getData("executive","*","isDelete=0".$zone_where,"",0);

$current_date1  = date('Y-m-d');
$current_date  = ($_REQUEST['invoice_date']!="")?date('Y-m-d',strtotime($_REQUEST['invoice_date'])):$current_date1;
?> 
<div class="table-responsive" id="print_info">  
	<table id="datatable_11" class="table table-bordered dataTable" style="overflow: scroll; !important;border:1px solid #000" cellpadding="0" cellspacing="0" width="100%">
        <thead>
			<tr>
				<td class="header" colspan="11" >
                    <h3 style="margin:0"><b><?= CLIENT_BRAND_NAME ?> (U-2)</b></h3>
                    <p style="margin:0;font-size: 16px"><?= CLIENT_ADDRESS; ?></p>
                    <h3 style="font-weight:500;color: #000;margin: 0;">Receivables : All Patry</h3>
                    <h4 style="font-weight:500;color: #000;margin: 0;">All Bills  As On &nbsp;&nbsp;&nbsp;&nbsp;<?= date("d F Y",strtotime($current_date)); ?></h4>
                </td>
			</tr>
            <tr class="tr">				
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: left;">Date</th>
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: left;">Bill No.</th>
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: right;">Bill Amt</th>
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: right;">Outstanding</th>
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: center;">Days</th>
                <th style="border-top:1px solid #000;padding: 2px;border-right: 0;border-left: 0;text-align: right;">Total</th> 				
            </tr>
        </thead>
        <tbody>
        	<?php 
        	if($customer_r){ 
            	while($customer_d = mysqli_fetch_array($customer_r)){ 
            		// print_r($customer_d);
            		$invoice_where = "isDelete=0 AND customer_id='".$customer_d['id']."' AND invoice_date<='".$current_date."'"; 
            		$total_cus_invoice = $db->rp_getTotalRecord("invoice_new",$invoice_where,"",0);
            		if($total_cus_invoice>0)
            		{
                        // echo "SDsd=";
            			$invocie_r = $db->rp_getData("invoice_new","*",$invoice_where,"",0);
            			$creditdays=$customer_d['credit_day'];
            ?>
        	<tr>
        		<td colspan="6" style="border-top: 1px solid #000;padding: 2px;border-bottom: 0;border-right: 0;">
                    <h4 style="margin: 0;font-weight: 600">
                        <?php echo stripslashes($customer_d['company_name']); ?>,
                        &nbsp;<?php echo $customer_d['city']; ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;Ph:<?php echo $customer_d['phone'].",".$customer_d['mobile_no1']; ?>
                        &nbsp;&nbsp;(<?php echo stripslashes($customer_d['cname']); ?>)
                        
                    </h4>
                </td>
        	</tr>
        	<?php 
        	if($invocie_r)
        	{
                $tot_cus_amt=0;
        		while($invocie_d = mysqli_fetch_assoc($invocie_r))
        		{
            		// $gst_amount_find = $invocie_d['grand_total'] + $invocie_d['packing_charge']   + $invocie_d['transport_charge'];
            		// $tax_amt = $gst_amount_find *18/100;  
            		// $gtotal = $gst_amount_find + $tax_amt;
                    $gtotal = $invocie_d['grand_total'];
                    $outstanding_amt = $invocie_d['remaining_amount'];
                    // $outstanding_amt = $invocie_d['grand_total_rounded'];

                    $tot_cus_amt = $tot_cus_amt + $outstanding_amt;

            		// over credit days
            		// echo $creditdays."-";
            		$diff = strtotime($invocie_d['invoice_date'])- strtotime($current_date); 
		            $over_creditdays=abs(round($diff / 86400)); 
		            $over_creditdays=$over_creditdays-$creditdays;
            		// over credit days

                    $tot_bill_amt += $gtotal;
                    $tot_outstanding_amt+=$outstanding_amt;
                    $tot_amt += $tot_cus_amt;
        	?>
        	<tr>
        		<td style="border:0;padding: 2px;text-align: left"><?php if($invocie_d['invoice_date']!=""){ echo date('d/m/Y',strtotime($invocie_d['invoice_date'])); }else{ echo "";}?></td>
        		<td style="border:0;padding: 2px;text-align: left"><?php echo stripslashes($invocie_d['invoice_no']); ?></td>
        		<td style="border:0;padding: 2px;text-align: right;"><?= number_format($gtotal,2) ?></td>
                <td style="border:0;padding: 2px;text-align: right;"><?= number_format($outstanding_amt,2) ?></td> 
        		<td style="border:0;padding: 2px;text-align: center"><?= $over_creditdays; ?></td> 
        		<td style="border:0;padding: 2px;text-align: right;"><?= number_format($tot_cus_amt,2) ?></td> 
        	</tr> 
        	<?php 
        		}
        	}
        	?> 
        	<?php 	
        			}
        		}
        	}
        	?>         
            <tr style="background-color:#ccc">
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;"></th>
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;">Total</th> 
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;text-align: right;"><?= number_format($tot_bill_amt,2) ?></th>
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;text-align: right;;"><?= number_format($tot_outstanding_amt,2) ?></th> 
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;"></th> 
                <th style="border-top: 1px solid #000;border-right:0;border-left:0;padding: 2px;text-align: right;;"><?= number_format($tot_amt,2) ?></th> 
            </tr> 
        </tbody>
    </table> 
</div>
<?php require_once("disconnect.php"); ?>
