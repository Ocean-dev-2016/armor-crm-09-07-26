<?php
//header('Content-Type: application/json');
$page_id=415;$page_slug='page_bom';
include("connect.php");
$ctable="tax";

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		
		if($service=="get_tax")
		{
			$taxs=array();
			$taxs_sgst=array();
			$taxs_igst=array();
			$item_id=$_REQUEST['item_id'];
			$count=$_REQUEST['count'];
			//$cgst_tax=$_REQUEST['cgst_tax'];
			$qty=$_REQUEST['qty'];
			$item_detail=$db->rp_getData("item_fg","*","id='".$item_id."'");
			$item_info=mysqli_fetch_assoc($item_detail);
			
			$taxs[]=array("id"=>$item_info['cgst_tax_id'],"variant_value"=>$item_info['cgst_no'],"selected"=>"selected");
			
			$taxs_sgst[]=array("id"=>$item_info['cgst_tax_id'],"variant_value"=>$item_info['sgst_no'],"selected"=>"selected");
			
			$taxs_igst[]=array("id"=>$item_info['igst_tax_id'],"variant_value"=>$item_info['igst_no'],"selected"=>"selected");
			
			/// For CGST
			$gather_all_taxs=$db->rp_getData($ctable,"*","isDelete=0 AND variant_for_cgst=1","",0);
			if($gather_all_taxs)
			{
				while($current_tax=mysqli_fetch_assoc($gather_all_taxs))
				{
					$taxs[]=array("id"=>$current_tax['id'],"variant_value"=>$current_tax['variant_value'],"selected"=>"");
				}
			}
			$spinner_tax="<option value=''> -- Select tax --</option>";
			foreach($taxs as $tax)
			{
				
				$spinner_tax.="<option data-cgst_tax='".$tax['variant_value']."' value='".$tax['variant_value']."' ".$tax['selected'].">".$tax['variant_value']."</option>";
			}
			
			
			$spinner_cgst="
					<select name='item[".$count."][cgst_tax]' class='text-right cgst_tax tax_label_cgst form-control' style='width:150px;' id='cgst_tax".$item_id."' onChange='recalculateFinalValues(this)'>
					".$spinner_tax."
					</select>
					";
					
			//// for SGST
			$gather_sgst_taxs=$db->rp_getData($ctable,"*","isDelete=0 AND variant_for_sgst=1","",0);
			if($gather_sgst_taxs)
			{
				while($current_sgsttax=mysqli_fetch_assoc($gather_sgst_taxs))
				{
					if($sgst_tax==$current_sgsttax['id']){
						$selected="selected";
					}
					$taxs_sgst[]=array("id"=>$current_sgsttax['id'],"variant_value"=>$current_sgsttax['variant_value'],"selected"=>$selected);
				}
			}
			$spinner_tax_sgst="<option value=''> -- Select tax --</option>";
			foreach($taxs_sgst as $tax_sgst)
			{
				
				$spinner_tax_sgst.="<option data-sgst_tax='".$tax_sgst['variant_value']."' value='".$tax_sgst['variant_value']."' ".$tax_sgst['selected'].">".$tax_sgst['variant_value']."</option>";
			}
			
			
			$spinner_sgst="
					<select name='item[".$count."][sgst_tax]' class='text-right sgst_tax tax_label_sgst form-control' style='width:150px;' id='sgst_tax".$item_id."' onChange='recalculateFinalValues(this)'>
					".$spinner_tax_sgst."
					</select>
					";
					
			//// for IGST
			$gather_igst_taxs=$db->rp_getData($ctable,"*","isDelete=0 AND variant_for_igst=1","",0);
			if($gather_igst_taxs)
			{
				while($current_igsttax=mysqli_fetch_assoc(gather_igst_taxs))
				{
					if($igst_tax==$current_igsttax['id']){
						$selected="selected";
					}
					$taxs_igst[]=array("id"=>$current_igsttax['id'],"variant_value"=>$current_igsttax['variant_value'],"selected"=>$selected);
				}
			}
			$spinner_tax_igst="<option value=''> -- Select tax --</option>";
			foreach($taxs_igst as $tax_igst)
			{
				
				$spinner_tax_igst.="<option data-igst_tax='".$tax_igst['variant_value']."' value='".$tax_igst['variant_value']."' ".$tax_igst['selected'].">".$tax_igst['variant_value']."</option>";
			}
			
			
			$spinner_igst="
					<select name='item[".$count."][igst_tax]' class='text-right igst_tax tax_label_igst form-control' style='width:150px;' id='igst_tax".$item_id."' onChange='recalculateFinalValues(this)'>
					".$spinner_tax_igst."
					</select>
					";
			$subtotal=$item_info['fg_sell_price']*$qty;
			
			$html.="<tr class='invoice_item'><td class='delete text-center'><i class='fa fa-trash text-danger'></i><input name='item[".$count."][fg_item_id]' id='item_id'  type='hidden' class='item_id' value='".$item_id."'></td><td>".$item_info['fg_item_name']."</td><td><input type='text' name='item[".$count."][fg_item_qty]' class='qty form-control input-small' id='qty".$item_id."' onChange='recalculateFinalValues(this)'  value='".$qty."' data-item_name='".$item_info['fg_item_name']."' data-current_packing_stock='".$item_info['fg_stock_qty']."'><p class='help-block'></p></td><td><input type='text' name='item[".$count."][fg_item_price]' class='price form-control input-small' type='text' id='price".$item_id."' value='".$item_info['fg_sell_price']."' onChange='recalculateFinalValues(this)'><p class='help-block'></p></td><td><input type='text' name=''item[".$count."][fg_item_subtotal]' class='total form-control input-small text-right' id='total'  value='".floatval($item_info['fg_sell_price']*$qty,2)."' disabled></td><td>".$spinner_cgst."</td><td><input type='text' readonly class='text-right cgst-tax-total form-control' value=''></td><td>".$spinner_sgst."</td><td><input type='text' readonly class='text-right sgst-tax-total form-control' value=''></td><td>".$spinner_igst."</td><td><input type='text' readonly class='text-right igst-tax-total form-control' value=''></td><td><input type='text' name=''item[".$count."][fg_item_amount]' class='amount form-control text-right input-small' id='amount".$item_id."'  value='' disabled></td></tr>";
			
			
			
			
			
			
			
			
			
			
			
			echo $html;
				
	}	
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
	}
?>