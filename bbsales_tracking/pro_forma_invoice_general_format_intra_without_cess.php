	<tr class="header-row">
		<th></th>
		<th></th>
		<th  colspan="4" width="30%" ></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
		<th class="text-center" colspan="2" > CGST</th>
		<th class="text-center" colspan="2"> SGST</th>
		<th width="5%"></th>
		
	</tr>
	<tr class="header-row">
		<th>Sr.<br/>No.</th>
		<th> HSN Code</th>
		<th  colspan="4" width="30%" class="item-description"> Description of Services </th>
		<th class="text-center"> Qty </th>
		<th> Rate<br/>(Rs.)</th>
		<th> Total<br/>(Rs.)</th>
		<th> Disc.<br/>(Rs.)</th>
		<th> Taxable<br/> Value </th>
		<th width="3%">  %</th>
		<th >Amt <br/>(Rs.)</th>
		<th width="3%"> %</th>
		<th >Amt <br/> (Rs.)</th>
		
		<th class="text-center" width="5%"> Net<br/> Amt</th>
	</tr>
	
	<?php 
				$item_stotal=0;
				$item_qtotal=0;
				$invoice_grand_total=0;
				$invoice_total_igst=0;
				$invoice_total_cgst=0;
				$count=0;
				foreach($invoice_items as $key=>$item)
				{
					if($count>7)
					{
						continue;
					}
				
					$count++;
					$item['item_stotal']=$item['fg_item_qty']*$item['fg_item_price'];
					$item_stotal+=$item['item_stotal'];
					$item_qtotal+=$item['fg_item_qty'];
					$invoice_grand_total+=$item['fg_item_grand_total'];
					$invoice_total_igst+=$item['sgst_amount'];
					$invoice_total_cgst+=$item['cgst_amount'];
				?>
				<tr class="item-row">
					<td  class="text-center"><p><?php echo $count; ?></p></td>
					<td><p><?php echo $item['fg_item_code'] ?></p></td>
					<td   colspan="4"  width="30%" class="item-description"><p><?php echo $item['fg_item_name'] ?></p></td>
					<td class="text-center" width="5%"><p><?php echo $item['fg_item_qty']." ".$item['fg_item_unit_name'] ?></p></td>
					<td class="text-right"><p><?php echo $db->aNum($item['fg_item_price']) ?></p></td>
					<td class="text-right"><p><?php echo $db->aNum($item['fg_item_subtotal']) ?> </p> </td>
					<td class="text-right"><p><?php echo $db->aNum($item['item_discount']) ?>  </p></td>
					<td class="text-right"><p><?php echo $db->aNum($item['fg_item_subtotal']) ?></p> </td>
					<td class="text-right"><p> <?php echo $db->aNum($item['cgst_tax']) ?></p> </td>
					<td class="text-right"><p><?php echo $db->aNum($item['cgst_amount']) ?></p> </td>
					<td class="text-right"><p> <?php echo $db->aNum($item['sgst_tax']) ?></p> </td>
					<td class="text-right"><p><?php echo $db->aNum($item['sgst_amount']) ?> </p></td>
					<td width="8%" class="text-right" ><p><?php echo $db->aNum($item['fg_item_grand_total']) ?></p> </td>
					
				</tr>
			  
				<?php }
				
				$empty_row_count=8-$count;
				if($empty_row_count>0)
				$height=$empty_row_count*50;
				else
				$height=0;	
			
			
				if($height>0)
				{
					//$height=$height+50;
				?>
				<tr class="item-row" style="height:<?php echo $height; ?>px!important;padding:0px">
				<td></td>
				<td></td>
				<td  colspan="4"  width="30%"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				</tr>
				<?php 
				}
				?>
	<tr>
		<th colspan="5"></th>
		<th class="item-discount">Total</th>
		<th class="text-center"> <?php echo $db->aNum($item_qtotal);?> </th>
		<th class=""></th>
		<th class="text-right"> <?php echo $db->aNum($item_stotal); ?></th>
		<th class="text-right"> <?php echo $db->aNum($invoice_total_discount); ?></th>
		<th class="total-taxable text-right"> <?php echo $db->aNum($item_stotal); ?></th>
		<th></th>
		<th class="total-cgst text-right">  <?php echo $db->aNum($invoice_total_cgst); ?></th>
		<th></th>
		<th class="total-sgst text-right"> <?php echo $db->aNum($invoice_total_igst); ?></th>
		<th class="grand-total text-right"> <?php echo $db->aNum($invoice_grand_total); ?></th>
		<?php 
			$invoice['invoice_tax_total']= $invoice_total_cgst+ $invoice_total_igst+ $invoice_total_cess;
		?>
	</tr>
