<?php
$page_id=580;$page_slug='price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Price List";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$price_list_id=$_REQUEST['price_list_id'];
$tcid=$_REQUEST['tcid'];
$cid=$_REQUEST['cid'];
?>
<style>
	.price_table
	{
		width:250mm;
	}
table{
  height: auto;   
  /*width:100%;*/

 /* margin:0 50mm;*/
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;border: 1px solid #000;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.uppercase
{
	text-transform: uppercase;
}
.m-b-0
{
	margin-bottom: 0;
}
.m-t-0
{
	margin-top: 0;
}
.bg-color
{
	background: #D3D3D3;
}
</style>
<table class="price_table">
	<thead>
		<tr>
			<th><h1 class="m-b-0"><span class="caption-subject bold  uppercase"><?= CLIENT_NAME; ?></span></h1><h1 class="m-b-0 m-t-0"><span class="caption-subject bold  uppercase">Price List for <?= $db->rp_getValue("price_list","pricelist_name","id='".$price_list_id."'"); ?>  </span></h1></th>
		</tr>
	</thead>
</table>
<table class="price_table">
		<?php
		$price_list_r=$db->rp_getData("product_price_list","cid,tcid,pid","price_list_id='".$price_list_id."' AND isDelete=0 GROUP BY tcid","",0);
		if($price_list_r)
		{
			while($price_list_d=mysqli_fetch_assoc($price_list_r))
			{				
				?>
				<thead>					
					<tr class="bg-color">
						<td class="center uppercase" colspan="2"><h3 class="m-b-0" style="color: #b36615;font-style: italic;"><?= $db->rp_getValue("top_category_master","name","id='".$price_list_d['tcid']."'"); ?></h3></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$price_list_r1=$db->rp_getData("product_price_list","cid,tcid,pid","price_list_id='".$price_list_id."' AND tcid='".$price_list_d['tcid']."' AND isDelete=0 GROUP BY cid","",0);
					while($price_list_d1=mysqli_fetch_assoc($price_list_r1))
					{
					?>
						<tr>
							<td class="center" colspan="2" style="font-weight: 600"><?= $db->rp_getValue("category_master","name","id='".$price_list_d1['cid']."'"); ?></td>
						</tr>
						<tr>
							<?php
							$price_list_r11=$db->rp_getData("product_price_list","cid,tcid,pid","price_list_id='".$price_list_id."' AND tcid='".$price_list_d1['tcid']."' AND cid='".$price_list_d1['cid']."' AND isDelete=0 GROUP BY cid,pid","",0);
							if($price_list_r11)
							{
								$count=0;
								while($price_list_d11=mysqli_fetch_assoc($price_list_r11))
								{
									$pro_img=$db->rp_getValue("product","image_path","id='".$price_list_d11['pid']."'");
									if($pro_img!="")
									{
										$image_path=SITEURL.PRODUCT.$pro_img;
									}
									else
									{
										$image_path=SITEURL."images/no_image_found.jpg";
									}
									$total_size=$db->rp_getTotalRecord("product_price_list","price_list_id='".$price_list_id."' AND isDelete=0 AND tcid='".$price_list_d11['tcid']."' AND cid='".$price_list_d11['cid']."' AND pid='".$price_list_d11['pid']."'",0);
									// $total_size1=$total_size+1;
									$count++;
									?>
									<td>
										<table>
											<thead>
												<tr>
													<td colspan="7" class="center"><b><?= $db->rp_getValue("product","name","id='".$price_list_d11['pid']."'"); ?></b></td>
												</tr>
												<tr>
													<td rowspan="<?= $total_size1;?>"></td>
													<td class="center">Size</td>
													<td class="center">Box Qty <br/><span style="font-size:11px">(nos)</span></td>
													<td class="center" >Carton Qty<br/><span style="font-size:11px">(nos)</span></td>
													<td class="center">MRP<br/><span style="font-size:11px">(INR)</span></td>
													<td class="center">Price<br/><span style="font-size:11px">(INR)</span></td>									
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="center"  rowspan="<?= $total_size;?>"><img src="<?= $image_path; ?>" width="100" height="100" ></td>
													<?php
												
													$item_r=$db->rp_getData("product_price_list","price,discounted_price,pid,weight_id","price_list_id='".$price_list_id."' AND isDelete=0 AND tcid='".$price_list_d11['tcid']."' AND cid='".$price_list_d11['cid']."' AND pid='".$price_list_d11['pid']."'","weight_id ASC",0);
													if($item_r)
													{
														while($item_d=mysqli_fetch_assoc($item_r))
														{								
															?>												
															<td ><?= $db->rp_getValue("weight","name","id='".$item_d['weight_id']."'"); ?></td>
															<td class="center"><?= $db->rp_getValue("product_weight_price","inner_size","weight_id='".$item_d['weight_id']."' AND product_id='".$item_d['pid']."'"); ?></td>
															<td class="center"><?= $db->rp_getValue("product_weight_price","outer_size","weight_id='".$item_d['weight_id']."' AND product_id='".$item_d['pid']."'"); ?></td>	
															<td class="text-right"><?= CURR.$item_d['price']; ?></td>
															<td class="text-right"><?= CURR.$item_d['discounted_price']; ?></td>
															</tr>									
														<?php
														}
													}
													?>
												</tr>										
											</tbody>
										</table>
									</td>					
									<?php
									if($count%2==0)
									{
									?>
										</tr>

										<!-- <tr> -->
									<?php
									}
								}
								if($count%2!=0)
								{
									?><td></td><?php
								}
							}
							else
							{
								?>

								<?php
							}
							?>
						</tr>
					<?php
					}
					?>
				</tbody>
				<?php
			}
		}
		?>
		<tfoot>
			<tr>
				<td colspan="2" class="center">website of customer:<a href="www.dbi.com">www.dbi.com</a></td>
			</tr>
		</tfoot>
</table>
<?php require_once 'disconnect.php';  ?>
