<?php
$page_id=638;$page_slug='product_stock';
include("connect.php");

$pro_id=$_REQUEST['pro_id'];
$weight_id=$_REQUEST['weight_id'];
$to_date=$_REQUEST['to_date'];
$from_date=$_REQUEST['from_date'];

$ctable_where1	= " pro_id='".$pro_id."' AND weight_id='".$weight_id."'  AND isDelete=0 ";
$ctable_where2	= " pro_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0  ";

if($to_date!="" && $from_date!="")
{
	$Where_sales = "AND DATE(created_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ";
	$ctable_where1.= $Where_sales;
}

$today_opening_date = date('Y-m-d', strtotime($from_date. ' - 1 days'));

$previous_inward_stock =  $db->rp_getValue("inward_stock","sum(pro_qty)","pro_id='".$pro_id."' AND weight_id='".$weight_id."'  AND isDelete=0 AND planning_date<='".$today_opening_date."'",0);

$previous_outward_stock =  $db->rp_getValue("packing_slip_item","sum(pro_qty)","pro_id='".$pro_id."' AND weight_id='".$weight_id."'  AND isDelete=0 AND Date(created_date)<='".$today_opening_date."'",0);

$opening_stock_qty = $db->rp_getValue("product_weight_price","opening_stock_qty"," product_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0");
$today_opening = (($previous_inward_stock) - ($previous_outward_stock))+($opening_stock_qty);
?>
	<br/>
	<div class="col-md-12">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<thead>
				<?php
				$proname = $db->rp_getValue("product","name"," id='".$pro_id."' AND isDelete=0");
		        $procode = $db->rp_getValue("product_weight_price","catno"," product_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0");
		        $newproname = $proname." - ".$procode;

				?>
				<tr>
					<td colspan="5" style="font-size: 16px;">
						<strong>Product Name : </strong><?=$proname?><br>
						<strong>Product Code  : </strong><?=$procode?><br>
					</td>
				</tr>
				<tr>
					<th colspan="2"></th>
					<th style="text-align:right;">Opening Qty : <?php echo "( ".date('d-m-Y',strtotime($today_opening_date))." ) "; ?></th>
					<th style="background: grey;"><?php echo ($today_opening>0)?$today_opening:"";?></th> 
	            	<th style="background: grey;"><?php echo ($today_opening<0)?$today_opening:"";?></th>
				</tr>
			    <tr>
	                <th><b>No.</b></th>
	                <th><b>Date</b></th>
	                <!-- <th><b>Product Name</b></th> -->
	                <!-- <th><b>Customer Name</b></th> -->
	                <th>Remark</th>
	                <th><b>Purchase Qty</b></th>
	                <th><b>Sales Qty</b></th>   
	                <!-- <th><b>Closing Qty</b></th> -->
	                <!-- <th><b>Sale Rs</b></th> -->
			    </tr>
			</thead>
		    <tbody>
	    		<?php
            	
	    	
    			$count = 0;
            	
            		$product_price = $db->rp_getValue("product_weight_price","price"," product_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0");
            	$begin = new DateTime($_REQUEST['from_date']);
				$end = new DateTime($_REQUEST['to_date']);

				$interval = DateInterval::createFromDateString('1 day');
				$period = new DatePeriod($begin, $interval, $end);
				
				for($i = $begin; $i <= $end; $i->modify('+1 day')){
                    // echo $i->format("Y-m-d");
                	$loop_Date = $i->format("Y-m-d");

	            	$inward = $db->rp_getData("inward_stock","*","pro_id='".$pro_id."' AND weight_id='".$weight_id."'  AND isDelete=0 AND planning_date = '".$loop_Date."'","",0);
	            	while($inward_d = mysqli_fetch_assoc($inward))
	            	{
	            		$purchase_remark="";
	            		$purchase_qty = $inward_d['pro_qty'];
	            		if($inward_d['invoice_no'])
	            		{
	            			$purchase_remark .=  "invoice no=".$inward_d['invoice_no'].", " ;
	            		}
	            		if($inward_d['invoice_date']!='0000-00-00' && $inward_d['invoice_date']!='1970-01-01')
	            		{
	            			$purchase_remark .= "invoice date=".$inward_d['invoice_date'].", ";
	            		}
	            		$purchase_remark .= $inward_d['remark'];

	            		$tot_purchase_qty+=$purchase_qty;
	           ?>
	           <tr>
            		<td><?php echo ++$count; ?></td>
            		<td><?php echo date('d-m-Y',strtotime($loop_Date)); ?></td> 
            		<td><?php echo $purchase_remark; ?></td> 
            		<td><?php echo ($purchase_qty!="")?$purchase_qty:"";?></td>
            		<td> </td>
            	</tr>
	           <?php
	            	}
	            	$sales_r = $db->rp_getData("packing_slip_item","packing_slip_id, SUM(pro_qty) as pqty","pro_id='".$pro_id."' AND weight_id='".$weight_id."'  AND isDelete=0 AND Date(created_date) = '".$loop_Date."' GROUP BY packing_slip_id","",0);
	            	while($sales_d = mysqli_fetch_assoc($sales_r))
	            	{
	            		$sales_qty = $sales_d['pqty'];

	            		$packing_slip_no = $db->rp_getValue("packing_slip","packing_slip_no","id='".$sales_d['packing_slip_id']."' AND isDelete=0",0);
	            		$customer_id = $db->rp_getValue("packing_slip","customer_id","id='".$sales_d['packing_slip_id']."' AND isDelete=0",0);

	            		$customer_name = $db->rp_getValue("executive","company_name","id='".$customer_id."' AND isDelete=0",0);
	            		$sales_remark = "<b>".$packing_slip_no."</b>, ".$customer_name;
	            		$tot_sales_qty+=$sales_qty;
	            ?>
	             <tr>
            		<td><?php echo ++$count; ?></td>
            		<td><?php echo date('d-m-Y',strtotime($loop_Date)); ?></td> 
            		<td><?php echo $sales_remark; ?></td> 
            		<td> </td>
            		<td><?php echo ($sales_qty!="")?$sales_qty:"";?></td>
            	</tr>
            	<?php 
            		}
            	}
            	?> 	
            	<tfoot>
	            	<tr>	            		
	            		<td></td>
	            		<td></td>
	            		<td></td>
	            		<td style="background: grey;"><?php echo ($tot_purchase_qty!="")?$tot_purchase_qty:"";?></td>
	            		<td style="background: grey;"><?php echo ($tot_sales_qty!="")?$tot_sales_qty:"";?></td> 
	            	</tr>
	            	<?php $today_closing = ($today_opening+$tot_purchase_qty)-($tot_sales_qty); ?>
	            	<tr>	            		
	            		<td></td>
	            		<td></td>
	            		<th style="text-align:right;">Closing Stock</th>
	            		<th style="background: grey;"><?php echo ($today_closing>0)?$today_closing:"";?></th> 
	            		<th style="background: grey;"><?php echo ($today_closing<0)?$today_closing:"";?></th>
	            	</tr>
	            </tfoot>	
		</table>
	</div>
	<?php require_once 'disconnect.php';  ?>