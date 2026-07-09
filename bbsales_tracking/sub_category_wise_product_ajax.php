<?php
	include('connect_in.php');
	$psc_id = isset( $_REQUEST['psc_id'] ) ? $db->clean( $_REQUEST['psc_id'] ):'';
	$product = isset( $_REQUEST['product'] ) ? $db->clean( $_REQUEST['product'] ):'';

	$productIds = $product;
	if ($productIds != "" && $productIds != NULL && $productIds != nulll && isset($productIds) && !empty($productIds)) {
		$productIdsArr = explode(",",$productIds);
		$productIdsArr = is_array($productIdsArr)?$productIdsArr:0;
	}

	if ($psc_id) {
		$product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1 AND cid IN (".$psc_id.")","",0);
        if($product_r)
        {
            while($product_d = mysqli_fetch_assoc($product_r))
            {
            	$product_weight = $db->rp_getData("product_weight_price","weight_id,id","product_id='".$product_d['id']."' AND isDelete=0");
				while($product_weight_d = mysqli_fetch_assoc($product_weight))
				{
					$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);
            		?>
                	<option value="<?=$product_weight_d['id']?>" <?= in_array($product_weight_d['id'],$productIdsArr)?"selected":""; ?>  ><?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']?></option>
            		<?php
        		}
            }
        } 
	}
?>