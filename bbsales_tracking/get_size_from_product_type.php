<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$type=$_REQUEST['type'];
$mode=$_REQUEST['mode'];
$id=$_REQUEST['id'];
require_once("../include/product.class.php");
$objProduct1= new Product();
$in_class="in";
$a_class="";
$aria_expanded="true";
if($id!="" && $mode=="edit")
{
	$stock_edit_disable="readonly";
	/*$in_class="in";
	$a_class="";
	$aria_expanded="true";*/

	$detail=array();
	$detail['id']=$id;
	$reply1=$objProduct1->GetEditDataProduct($detail);
	if($reply1['ack']==1)
	{
		$result1=$reply1['result'];		
		extract($result1);

		// print_r($result1);exit();
		// echo $stock;exit;	
	}
}
else
{
	$stock_edit_disable="readonly";
	/*$in_class="";
	$a_class="collapse";
	$aria_expanded="false";*/
}
$order_unit_arr = array("-1"=>"Box","-2"=>"Strip","-3"=>"Pallet","1"=>"Caret","2"=>"Big Box","100"=>"Nos");
?>
<style>
table{
    height: auto;	
    width:100%;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 16px;
	margin:auto;
	padding:auto;
}
.box1 th
{
	border-color: #000;
	color: #FFF;
}
table{
    height: auto;	
    width:100%;
}
table , td, th {
 border: 1px solid #95A5A6;
 border-collapse: collapse;
}
td, th {
 padding: 2px;
 width: 30px;
 height: 15px;
 /*color: #FFF;*/
 /*line-height: 0.8;*/
 text-align: center;
}
h4{
	padding-left:40px;
}
th {
}
.center{
	text-align:center;
}
.left{
	text-align:left;
	padding-left:15px;
}
.right{
	text-align:right;
	padding-right:15px;
}
#mian_scroll{
    overflow-y: scroll;
    width: initial;
}
/*.table-scrollable 
{
	width: auto;
	height: 600px;
	overflow-x: scroll;
	overflow-y: scroll;

	border: 1px solid #e7ecf1;
	margin: 10px 0 !important;
}*/


/*#please-scroll{
  height: calc(100% - 110px); overflow-y: scroll
}*/


</style>
<?php
if($type==1)
{
?>

<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOne" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOne" style="color:#fff;">
	<div class="portlet grey-cascade box">
	 	<div class="portlet-title">
			<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Variant and Pricing <span style="float: right; "><i class="fa fa-angle-down"></i></span></div>
  		</div>
  	</div>
	</a>
<div class="portlet grey-cascade box overflow-auto" style="box-shadow: none;" id="please-scroll">
  	<div id="collapseOne" class="portlet-body collapse <?= $in_class; ?>">
		<div  class="portlet-body ">	
		<!-- <div class="row" style="margin-bottom: 30px;">
			<div class="col-md-4">	
			<label>Select Size</label>			
				<select name="searchSize" multiple="multiple" id="searchSize" class="form-control searchSize" onchange="getSize()">
					<option value="">Select Size</option>	
					<?php
					$w_r = $db->rp_getData("weight","*","isDelete=0 AND id!=-1","display_order ASC");
					if($w_r)
					{
						while($w_d=mysqli_fetch_assoc($w_r))
						{
							?>
							<option <?php echo (in_array($w_d['id'],$weight_ids))?"selected":""; ?> value="<?= $w_d['id']?>"><?= $w_d['name'];?></option>
							<?php
						}
					}
					?>
				</select>
			</div>												
		</div> -->
	  		<div class="row ">									
	  			<div class="col-sm-12 col-xs-12">
	  				<div id="mian_scroll">
		  			<table style="width: 1500px;">
		  				<thead class="portlet grey-cascade box1">
		  					<tr>
		  						<!-- <th>Including Gst</br><input type="checkbox" name="check_all"  class="check_all_box"></th> -->
		  						<th>Action</th>
		  						<th style="width: 5%;">Variant</th>
		  						<th style="width: 10%;">Product code</th>
		  						<th style="width: 5%;">Product Weight</th>
		  						<th style="width: 10%;">Price<br><span style="font-size:12px">(INR)</span></th>
		  						<th style="width: 10%;">Inner Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Inner Unit</th>
		  						<!-- <th style="width: 10%;">Inner Size<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Inner CFT<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Inner CBM<br><span style="font-size:12px">(NOS)</span></th>
		  						 --><th style="width: 10%;">Outer Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Outer Unit</th>
		  						<!-- <th style="width: 10%;">Master Size<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Master CFT<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Master CBM<br><span style="font-size:12px">(NOS)</span></th>-->
		  						<th style="width: 10%;">Min Stock Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Max Stock Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 5%;">Stock<br><span style="font-size:12px">(NOS)</span></th>
		  						<th>Opening Stock<br><span style="font-size:12px">(NOS)</span></th>
		  						
		  					</tr>
		  				</thead>
						<?php					
						$pro_r = $db->rp_getData("weight","*","isDelete=0 AND id!=-1","display_order ASC");
						if(mysqli_num_rows($pro_r)>0)
						{
							$count_row=0;
						 	while($pro_d = mysqli_fetch_array($pro_r))
							{
								$count_row++;
								// print_r($pro_d);
					
								if(in_array($pro_d['id'],$weight_ids))
								{

									$key = array_search ($pro_d['id'], $weight_ids);
									$checked="checked";
									$disabled="";
													
									$price=$weight_prices[$key];					
									$stock=$weight_stock[$key];				
									$current_stock = $weight_current_stock[$key];				
									//$min=$weight_min[$key];		
									$inner=$weight_inner[$key];
									$inner_size=$weight_inner_size[$key];		
									$inner_cft=$weight_inner_cft[$key];				
									$inner_cbm=$weight_inner_cbm[$key];				
									$outer=$weight_outer[$key];		
									$outer_size=$weight_outer_size[$key];		
									$outer_cft=$weight_outer_cft[$key];	
									$outer_cbm=$weight_outer_cbm[$key];	


									$min_stock_qty=$weight_min_stock_qty[$key];	
									$max_stock_qty=$weight_max_stock_qty[$key];	

									$catno=$weight_catnos[$key];	
									$pro_weight=$weight_pro_weight[$key];		
									$is_including=$weight_is_including[$key];	

									$inner_unit=$weight_inner_unit[$key];		
									$outer_unit=$weight_outer_unit[$key];
									$minimum_selling_price=$min_selling_price[$key];	
								}
						
								else
								{
									$checked="";
									$disabled="disabled";
									$price="";
									$stock="";
									$current_stock="";
									$min="";
									$inner="";
									$inner_size="";
									$inner_cft="";
									$inner_cbm="";

									$min_stock_qty="";
									$max_stock_qty="";

									$outer="";
									$outer_size="";
									$outer_cft="";
									$outer_cbm="";
									$catno="";
									$pro_weight="";
									$is_including="";
									$inner_unit="";
									$outer_unit="";
									$minimum_selling_price="";
								}  
								?>
								<?php
							if($is_including == 1)
							{
								$checked_gst="checked";
							}
							else
							{
								$checked_gst="";	
							}
							?>
								<tr>
							<!-- <td><input type="checkbox" name="weights_chk[weights][<?php echo $pro_d['id']?>][is_including]" id="check_gst" class="check_all_pro" <?=$checked_gst?>></td> -->

							<td>
							 	<span class="input-group-addon"><input data-name="<?php echo $pro_d['name']?>" <?php echo $checked;?> id="size_checkbox" type="checkbox" class="weights_chk" name="weights_chk[weights][<?php echo $pro_d['id']?>][id]" value="<?php echo $pro_d['id']?>" aria-label="..."></span>
							</td>

							<td>
								<label for="cid"><?php  echo $pro_d['name']?><!-- <code>*</code> --></label>
								<input placeholder="Title" type="hidden" name="weights_chk[weights][<?php echo $pro_d['id']?>][name]" value="<?php echo $pro_d['name']?>" class="form-control" aria-label="15g">
							</td>
							<td> 
								<input <?php echo $disabled;?> placeholder="Product Code" id="catnoInput<?php echo $pro_d['id']?>" value="<?php echo $catno;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][catno]" class="form-control catnoInput" aria-label="15g">
						  	</td>
						  	<td> 
								<input <?php echo $disabled;?> placeholder="Product Weight" id="weightInput<?php echo $pro_d['id']?>" value="<?php echo $pro_weight;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][pro_weight]" class="form-control weightInput" aria-label="15g">
							</td>
							<td>
							  <input <?php echo $disabled;?> placeholder="MRP" id="weightPriceInput<?php echo $pro_d['id']?>" data-pro_id="<?php echo $pro_d['id']?>" value="<?php echo $price;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][price]" class="form-control weightPriceInput"  aria-label="15g">
							</td>
							<td>
								<input <?php echo $disabled;?> placeholder="Inner Qty" id="weightInnerInput<?php echo $pro_d['id']?>" value="<?php echo $inner;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner]" class="form-control weightInnerInput"  aria-label="15g">
							</td>
							<td>
								<select <?php echo $disabled;?> placeholder="Inner Unit" id="weightInnerUnitInput<?php echo $pro_d['id']?>"  type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_unit]" class="form-control weightInnerUnitInput">
									<option value="">Inner Unit</option>
									<?php 
									foreach ($order_unit_arr as $key => $value) {   
									?>
									<option <?=($inner_unit==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
									<?php 
									}
									?>
								</select>
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner Size" id="weightInnerSizeInput<?php echo $pro_d['id']?>" value="<?php echo $inner_size;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_size]" class="form-control weightInnerSizeInput"  aria-label="15g">
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner CFT" id="weightInnercftInput<?php echo $pro_d['id']?>" value="<?php echo $inner_cft;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_cft]" class="form-control weightInnercftInput"  aria-label="15g">
							</td>

							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner CBM" id="weightInnercbmInput<?php echo $pro_d['id']?>" value="<?php echo $inner_cbm;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_cbm]" class="form-control weightInnercbmInput"  aria-label="15g">
							</td>

							<td>
								<input <?php echo $disabled;?> placeholder="Outer Qty" id="weightOuterInput<?php echo $pro_d['id']?>"  value="<?php echo $outer;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][outer]" class="form-control weightouterInput" aria-label="15g">
							</td>
							<td>
								<select <?php echo $disabled;?> placeholder="Inner Unit" id="weightOuterUnitInput<?php echo $pro_d['id']?>"  type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_unit]" class="form-control weightOuterUnitInput">
									<option value="">Outer Unit</option>
									<?php 
									foreach ($order_unit_arr as $key => $value) {   
									?>
									<option value="<?= $key ?>" <?=($outer_unit==$key)?"selected":""; ?>><?= $value ?></option>
									<?php 
									}
									?>
								</select>
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Master Size" id="weightouterSizeInput<?php echo $pro_d['id']?>" value="<?php echo $outer_size;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_size]" class="form-control weightouterSizeInput"  aria-label="15g">
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Master CFT" id="weightoutercftInput<?php echo $pro_d['id']?>" value="<?php echo $outer_cft;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_cft]" class="form-control weightoutercftInput"  aria-label="15g">
							</td>	

							<td hidden>
								<input style="width: 90px;" <?php echo $disabled;?> placeholder="Master CBM" id="weightoutercbmInput<?php echo $pro_d['id']?>" value="<?php echo $outer_cbm;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_cbm]" class="form-control weightoutercbmInput"  aria-label="15g">
							</td>

							<td>
								<input style="" <?php echo $disabled;?> placeholder="Min Stock Qty" id="weightminqty<?php echo $pro_d['id']?>" value="<?php echo $min_stock_qty;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][min_stock_qty]" class="form-control weightminqty"  aria-label="15g">
							</td>

							<td>
								<input style="" <?php echo $disabled;?> placeholder="Max Stock Qty" id="weightmaxqty<?php echo $pro_d['id']?>" value="<?php echo $max_stock_qty;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][max_stock_qty]" class="form-control weightmaxqty"  aria-label="15g">
							</td>

						  	<td hidden>
						  		<input style="width: 75px;" <?php  echo($_REQUEST['mode']=='edit')?"disabled":"";?> placeholder="Stock" id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g">							  
							  	<?php 
							  	if($stock!="")
							  	{
							 	?>
							   	<input type="hidden"  placeholder="Stock"  id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g">
								<?php
								}
								?>
						  	</td>

						  	<td>
								  		<input  style="width: 90px;" placeholder="Stock" id="weightCurrentStockInput<?php echo $pro_d['id']?>" value="<?php echo $current_stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][current_stock]" class="form-control weightCurrentStockInput" aria-label="15g" <?=$stock_edit_disable?>>							  
							</td>

						  	<td>
								  		<input style=""  placeholder="Stock" id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g" <?=$stock_edit_disable?>>							  
							</td>
							
						</tr>
								<?php
								
								}													
							}
							?>	
					</table>
				</div>
				</div>		
			</div>												
		</div>
	</div>
</div>

<?php
}
else if($type==2)
{
?>
<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOneWithoutSize" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOneWithoutSize" style="color:#fff;">
	<div class="portlet grey-cascade box">
	 	<div class="portlet-title">
			<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Default Variant and Pricing <span style="float: right; "><i class="fa fa-angle-down"></i></span></div>
  		</div>
  	</div>
	</a>
<div class="portlet grey-cascade box" style="box-shadow: none;">
  	<div id="collapseOneWithoutSize" class="portlet-body collapse <?= $in_class; ?>">
		<div  class="portlet-body ">									
	  		<div class="row">	
		  		<div class="col-sm-12 col-xs-12">
		  			<div id="mian_scroll">
		  			<table style="width: 1500px;">
		  				<thead class="portlet grey-cascade box1">
		  					<tr>
		  						<!-- <th>Including Gst</br><input type="checkbox" name="check_all"  class="check_all_box"></th> -->
		  						<th>Action</th>
		  						<th style="width: 5%;">Variant</th>
		  						<th style="width: 10%;">Product code</th>
		  						<th style="width: 5%;">Product Weight</th>
		  						<th style="width: 10%;">Price<br><span style="font-size:12px">(INR)</span></th>
		  						<th style="width: 10%;">Inner Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Inner Unit</th>
		  						<!-- <th style="width: 10%;">Inner Size<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Inner CFT<br><span style="font-size:12px">(NOS)</span></th>

		  						<th style="width: 10%;">Inner CBM<br><span style="font-size:12px">(NOS)</span></th> -->

		  						<th style="width: 10%;">Outer Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Outer Unit</th>
		  						<!-- <th style="width: 10%;">Master Size<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Master CFT<br><span style="font-size:12px">(NOS)</span></th>

		  						<th style="width: 10%;">Master CBM<br><span style="font-size:12px">(NOS)</span></th> -->

		  						<th style="width: 10%;">Min Stock Qty<br><span style="font-size:12px">(NOS)</span></th>
		  						<th style="width: 10%;">Max Stock Qty<br><span style="font-size:12px">(NOS)</span></th>

		  						<th style="width: 5%;">Stock<br><span style="font-size:12px">(NOS)</span></th>
		  						<th>Opening Stock<br><span style="font-size:12px">(NOS)</span></th>
		  						
		  					</tr>
		  				</thead>
		  			
		  		<!-- </div>								 -->
				<?php					
				$pro_r1 = $db->rp_getData("weight","*","isDelete=0 AND id=-1","",0);
				if($pro_d = mysqli_fetch_assoc($pro_r1))
				{
					if(in_array($pro_d['id'],$weight_ids))
					{
						$key = array_search ($pro_d['id'], $weight_ids);
						$checked="checked";
						$disabled="";
										
						$price=$weight_prices[$key];					
						$stock=$weight_stock[$key];				
						$current_stock=$weight_current_stock[$key];				
						$inner=$weight_inner[$key];		
						$inner_size=$weight_inner_size[$key];		
						$inner_cft=$weight_inner_cft[$key];		
						$inner_cbm=$weight_inner_cbm[$key];	

						$min_stock_qty=$weight_min_stock_qty[$key];		
						$max_stock_qty=$weight_max_stock_qty[$key];		

						$outer=$weight_outer[$key];	
						$outer_size=$weight_outer_size[$key];		
						$outer_cft=$weight_outer_cft[$key];		
						$outer_cbm=$weight_outer_cbm[$key];		
						$catno=$weight_catnos[$key];	
						$pro_weight=$weight_pro_weight[$key];			
						$is_including=$weight_is_including[$key];	
						$inner_unit=$weight_inner_unit[$key];		
						$outer_unit=$weight_outer_unit[$key];
						$minimum_selling_price=$min_selling_price[$key];			
					}
			
					else
					{
						$checked="";
						$disabled="disabled";
						$price="";
						$stock="";
						$current_stock="";
						$min="";
						$inner="";
						$inner_size="";
						$inner_cft="";
						$inner_cbm="";

						$min_stock_qty="";
						$max_stock_qty="";

						$outer="";
						$outer_size="";
						$outer_cft="";
						$outer_cbm="";
						$catno="";
						$pro_weight="";
						$is_including="";
						$inner_unit="";
						$outer_unit="";
						$minimum_selling_price="";
					}
					?>
						<tr>

							<?php
							if($is_including == 1)
							{
								$checked_gst="checked";
							}
							else
							{
								$checked_gst="";	
							}
							?>
							<!-- <td><input type="checkbox" name="weights_chk[weights][<?php echo $pro_d['id']?>][is_including]" id="check_gst" class="check_all_pro" <?=$checked_gst?>></td> -->

							<td>
							 	<span class="input-group-addon"><input data-name="<?php echo $pro_d['name']?>" <?php echo $checked;?> id="size_checkbox" type="checkbox" class="weights_chk" name="weights_chk[weights][<?php echo $pro_d['id']?>][id]" value="<?php echo $pro_d['id']?>" aria-label="..."></span>
							</td>

							<td>
								<label for="cid"><?php  echo $pro_d['name']?><!-- <code>*</code> --></label>
								<input placeholder="Title" type="hidden" name="weights_chk[weights][<?php echo $pro_d['id']?>][name]" value="<?php echo $pro_d['name']?>" class="form-control" aria-label="15g">
							</td>
							<td> 
								<input <?php echo $disabled;?> placeholder="Product Code" id="catnoInput<?php echo $pro_d['id']?>" value="<?php echo $catno;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][catno]" class="form-control catnoInput" aria-label="15g">
						  	</td>
						  	<td> 
								<input <?php echo $disabled;?> placeholder="Product Weight" id="weightInput<?php echo $pro_d['id']?>" value="<?php echo $pro_weight;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][pro_weight]" class="form-control weightInput" aria-label="15g">
							</td>
							<td>
							  <input <?php echo $disabled;?> placeholder="MRP" id="weightPriceInput<?php echo $pro_d['id']?>" data-pro_id="<?php echo $pro_d['id']?>"  value="<?php echo $price;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][price]" class="form-control weightPriceInput"  aria-label="15g">
							</td>
							<td>
								<input <?php echo $disabled;?> placeholder="Inner Qty" id="weightInnerInput<?php echo $pro_d['id']?>" value="<?php echo $inner;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner]" class="form-control weightInnerInput"  aria-label="15g">
							</td>
							<td>
								<select <?php echo $disabled;?> placeholder="Inner Unit" id="weightInnerUnitInput<?php echo $pro_d['id']?>"  type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_unit]" class="form-control weightInnerUnitInput">
									<option value="">Inner Unit</option>
									<?php 
									foreach ($order_unit_arr as $key => $value) {   
									?>
									<option <?=($inner_unit==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
									<?php 
									}
									?>
								</select>
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner Size" id="weightInnerSizeInput<?php echo $pro_d['id']?>" value="<?php echo $inner_size;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_size]" class="form-control weightInnerSizeInput"  aria-label="15g">
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner CFT" id="weightInnercftInput<?php echo $pro_d['id']?>" value="<?php echo $inner_cft;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_cft]" class="form-control weightInnercftInput"  aria-label="15g">
							</td>

							<td hidden>
								<input <?php echo $disabled;?> placeholder="Inner CBM" id="weightInnercbmInput<?php echo $pro_d['id']?>" value="<?php echo $inner_cbm;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][inner_cbm]" class="form-control weightInnercbmInput"  aria-label="15g">
							</td>

							<td>
								<input <?php echo $disabled;?> placeholder="Outer Qty" id="weightOuterInput<?php echo $pro_d['id']?>"  value="<?php echo $outer;?>" type="text" 
						  		name="weights_chk[weights][<?php echo $pro_d['id']?>][outer]" class="form-control weightouterInput" aria-label="15g">
							</td>
							<td>
								<select <?php echo $disabled;?> placeholder="Inner Unit" id="weightOuterUnitInput<?php echo $pro_d['id']?>"  type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_unit]" class="form-control weightOuterUnitInput">
									<option value="">Outer Unit</option>
									<?php 
									foreach ($order_unit_arr as $key => $value) {   
									?>
									<option value="<?= $key ?>" <?=($outer_unit==$key)?"selected":""; ?>><?= $value ?></option>
									<?php 
									}
									?>
								</select>
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Master Size" id="weightouterSizeInput<?php echo $pro_d['id']?>" value="<?php echo $outer_size;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_size]" class="form-control weightouterSizeInput"  aria-label="15g">
							</td>
							<td hidden>
								<input <?php echo $disabled;?> placeholder="Master CFT" id="weightoutercftInput<?php echo $pro_d['id']?>" value="<?php echo $outer_cft;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_cft]" class="form-control weightoutercftInput"  aria-label="15g">
							</td>	

							<td hidden>
								<input style="width: 90px;" <?php echo $disabled;?> placeholder="Master CBM" id="weightoutercbmInput<?php echo $pro_d['id']?>" value="<?php echo $outer_cbm;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][outer_cbm]" class="form-control weightoutercbmInput"  aria-label="15g">
							</td>

							<td>
								<input style="" <?php echo $disabled;?> placeholder="Min Stock Qty" id="weightminqty<?php echo $pro_d['id']?>" value="<?php echo $min_stock_qty;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][min_stock_qty]" class="form-control weightminqty"  aria-label="15g">
							</td>

							<td>
								<input style="" <?php echo $disabled;?> placeholder="Max Stock Qty" id="weightmaxqty<?php echo $pro_d['id']?>" value="<?php echo $max_stock_qty;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][max_stock_qty]" class="form-control weightmaxqty"  aria-label="15g">
							</td>

						  	<td hidden>
						  		<input style="width: 75px;" <?php  echo($_REQUEST['mode']=='edit')?"disabled":"";?> placeholder="Stock" id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g">							  
							  	<?php 
							  	if($stock!="")
							  	{
							 	?>
							   	<input type="hidden"  placeholder="Stock"  id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>" type="text"  name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g">
								<?php
								}
								?>
						  	</td>

						  	<td>
								  		<input  style="width: 90px;" placeholder="Stock" id="weightCurrentStockInput<?php echo $pro_d['id']?>" value="<?php echo $current_stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][current_stock]" class="form-control weightCurrentStockInput" aria-label="15g" <?=$stock_edit_disable?>>							  
							</td>

						  	<td>
								  		<input style=""  placeholder="Stock" id="weightStockInput<?php echo $pro_d['id']?>" value="<?php echo $stock;?>"type="text" name="weights_chk[weights][<?php echo $pro_d['id']?>][stock]" class="form-control weightStockInput" aria-label="15g" <?=$stock_edit_disable?>>							  
							</td>
							
						</tr>
														
					<?php								
				}
				?>
				</table>
			</div>
				</div>			
			</div>												
			</div>
	</div>
</div>
<?php
}
?>


<script type="text/javascript">	

function getSize()
{
	var selected=[];
	var count = 0;
    $('#searchSize :selected').each(function(i, sel){
	    selected[count++] = $(sel).val();
	});
	// alert(selected);
}
$(function(){
$('input.weights_chk').live('click', function(event){	
	var c=$(this).prop("checked");
	var id=$(this).val();
	if(c==true)
	{
		// $("#weightPriceInput"+id).removeAttr('disabled');
		$("#weightStockInput"+id).removeAttr('disabled');
		$("#weightMinAlertInput"+id).removeAttr('disabled');
		$("#weightInnerInput"+id).removeAttr('disabled');
		$("#weightInnerSizeInput"+id).removeAttr('disabled');
		$("#weightInnercftInput"+id).removeAttr('disabled');
		$("#weightInnercbmInput"+id).removeAttr('disabled');


		$("#weightoutercbmInput"+id).removeAttr('disabled');

		$("#weightminqty"+id).removeAttr('disabled');
		$("#weightmaxqty"+id).removeAttr('disabled');



		$("#weightOuterInput"+id).removeAttr('disabled');
		$("#weightouterSizeInput"+id).removeAttr('disabled');
		$("#weightoutercftInput"+id).removeAttr('disabled');
		$("#catnoInput"+id).removeAttr('disabled');
		$("#weightInput"+id).removeAttr('disabled');

		$("#weightInnerUnitInput"+id).removeAttr('disabled');
		$("#weightOuterUnitInput"+id).removeAttr('disabled');
		$("#weightPriceInput"+id).removeAttr('disabled');
	}
	else
	{
		// $("#weightPriceInput"+id).attr('disabled','disabled');		 
		$("#weightStockInput"+id).attr('disabled','disabled');
		$("#weightMinAlertInput"+id).attr('disabled','disabled');	
		$("#weightInnerInput"+id).attr('disabled','disabled');	
		$("#weightInnerSizeInput"+id).attr('disabled','disabled');	
		$("#weightInnercftInput"+id).attr('disabled','disabled');	

		$("#weightoutercbmInput"+id).attr('disabled','disabled');
		$("#weightminqty"+id).attr('disabled','disabled');
		$("#weightmaxqty"+id).attr('disabled','disabled');


		$("#weightInnercbmInput"+id).attr('disabled','disabled');	
		$("#weightOuterInput"+id).attr('disabled','disabled');	  
		$("#weightouterSizeInput"+id).attr('disabled','disabled');	  
		$("#weightoutercftInput"+id).attr('disabled','disabled');	  
		$("#catnoInput"+id).attr('disabled','disabled');
		$("#weightInput"+id).attr('disabled','disabled');
		$("#weightInnerUnitInput"+id).attr('disabled','disabled');
		$("#weightOuterUnitInput"+id).attr('disabled','disabled');
		$("#weightPriceInput"+id).attr('disabled','disabled');
	}
});
});

$('input.check_all_box').on('change', function() 
{
	// var check_var=$(".check_all_box").val();

	if($(".check_all_box").prop('checked') == true )
	{
		// alert("test")

    $('input.check_all_pro').not(this).prop('checked', true);  
	}
	else
	{
		// alert("test")

		$('input.check_all_pro').not(this).prop('checked', false);  
	}
});

</script>
