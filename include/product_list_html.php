<?php
/**
 * Build product list HTML for print / PDF (browser Save as PDF).
 */
function productListBuildHtml($db, $request, $with_price = false)
{
	$ctable = "product";
	$ctable_where = productListBuildWhere($db, $request);

	$no_image_web = SITEURL."images/no_image_found.jpg";
	$no_image_file = realpath(__DIR__."/../images/no_image_found.jpg");
	if(!$no_image_file || !file_exists($no_image_file))
	{
		$no_image_file = realpath(__DIR__."/../images/no_data_found.jpg");
		$no_image_web = SITEURL."images/no_data_found.jpg";
	}

	$filter_parts = productListFilterLabels($db, $request);
	$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"display_order ASC, name ASC",0);

	ob_start();
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>Product List</title>
<style>
@page { size: A4; margin: 10mm; }
body { font-family: Arial, sans-serif; font-size: 11pt; margin: 0; padding: 10px; color: #000; }
.title { text-align: center; margin-bottom: 12px; }
.title h2 { margin: 0 0 4px 0; font-size: 18pt; }
.title h3 { margin: 0; font-size: 12pt; color: #555; }
.subtitle { text-align: center; font-size: 9pt; color: #666; margin-bottom: 12px; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #333; padding: 6px; vertical-align: middle; }
th { background: #f0e6cc; text-align: center; }
.center { text-align: center; }
.right { text-align: right; }
.product-name { font-weight: 600; }
.variant-tag { font-size: 9pt; color: #666; }
.product-photo { width: 70px; height: 70px; object-fit: contain; }
.no-print { margin: 10px 0 15px; text-align: center; }
@media print {
	.no-print { display: none !important; }
}
</style>
</head>
<body>
<div class="no-print">
	<button type="button" onclick="window.print();" style="padding:8px 16px;font-size:14px;cursor:pointer;">Print / Save as PDF</button>
</div>
<div class="title">
	<h2><?= htmlspecialchars(CLIENT_NAME); ?></h2>
	<h3>Product List</h3>
</div>
<?php if(!empty($filter_parts)) { ?>
<div class="subtitle"><?= htmlspecialchars(implode(" | ", $filter_parts)); ?></div>
<?php } ?>
<table>
<thead>
<tr>
	<th width="4%">Sr.</th>
	<th width="12%">Photo</th>
	<th width="18%">Product Code</th>
	<th width="<?= $with_price ? '36' : '66'; ?>%">Product Name</th>
	<?php if($with_price) { ?><th width="30%">Price (INR)</th><?php } ?>
</tr>
</thead>
<tbody>
<?php
$cnt = 0;
if($ctable_r && mysqli_num_rows($ctable_r) > 0)
{
	while($product = mysqli_fetch_assoc($ctable_r))
	{
		$img_web = $no_image_web;
		if($product['image_path'] != "")
		{
			$local_image = realpath(__DIR__."/../".PRODUCT.$product['image_path']);
			if($local_image && file_exists($local_image))
			{
				$img_web = SITEURL.PRODUCT.$product['image_path'];
			}
		}

		$variants_r = $db->rp_getData("product_weight_price","catno,price,weight_id","product_id='".$product['id']."' AND isDelete=0","catno ASC",0);
		if($variants_r && mysqli_num_rows($variants_r) > 0)
		{
			while($variant = mysqli_fetch_assoc($variants_r))
			{
				$cnt++;
				?>
				<tr>
					<td class="center"><?= $cnt; ?></td>
					<td class="center"><img class="product-photo" src="<?= $img_web; ?>" alt=""></td>
					<td class="center"><?= htmlspecialchars(strtoupper($variant['catno'])); ?></td>
					<td class="center">
						<span class="product-name"><?= htmlspecialchars(stripslashes($product['name'])); ?></span>
						<?php if((int)$variant['weight_id'] != -1) {
							$size_name = $db->rp_getValue("weight","name","id='".(int)$variant['weight_id']."'",0);
							if($size_name != "") { ?><br/><span class="variant-tag">Size: <?= htmlspecialchars($size_name); ?></span><?php }
						} ?>
					</td>
					<?php if($with_price) { ?><td class="center"><?= htmlspecialchars(number_format((float)$variant['price'], 2, '.', '')); ?></td><?php } ?>
				</tr>
				<?php
			}
		}
		else
		{
			$cnt++;
			?>
			<tr>
				<td class="center"><?= $cnt; ?></td>
				<td class="center"><img class="product-photo" src="<?= $img_web; ?>" alt=""></td>
				<td class="center">-</td>
				<td class="center"><span class="product-name"><?= htmlspecialchars(stripslashes($product['name'])); ?></span></td>
				<?php if($with_price) { ?><td class="center">-</td><?php } ?>
			</tr>
			<?php
		}
	}
}
else
{
	$colspan = $with_price ? 5 : 4;
	?>
	<tr><td colspan="<?= $colspan; ?>" class="center">No products found for selected filters.</td></tr>
	<?php
}
?>
</tbody>
</table>
<script>
window.onload = function() {
	setTimeout(function(){ window.print(); }, 400);
};
</script>
</body>
</html>
	<?php
	return ob_get_clean();
}
?>
