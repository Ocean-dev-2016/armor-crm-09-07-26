	<?php
	$page_id=559;$page_slug='page_product';
		$ctable 	= "product";
		$ctable1 	= "Product";
		$main_page 	= "product_mgmt";
			$page 		= "manage_".$ctable;
	$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
	$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));
	include("connect.php");
	require_once("../include/product.class.php");
	$objProduct= new Product();
	$gst=$db->rp_getValue("dealer_distributor_network","gst","id=1");
	$tcid                 = "";
	$name                 = "";
	$gujrati_name         = "";
	$sku                  = "";
	$weight               = "";
	$max_price            = "";
	$sell_price           = "";
	$weight_ids           = array();
	$attr                 = "";
	$image_path           = "";
	$status               = "";
	$cgst                 = "";
	$sgst                 = "";
	$igst                 = "";
	$descr                = "";
	$pro_tax              = 0;
	$qty                  = 0;
	$min_qty_alert        = 0;
	$display_order        = 0;
	$isWhatsNew           = 0;
	$isSale               = 0;
	$isFeatured           = 0;
	$isHot                = 0;
	$isOffer              = 0;
	$isDeal               = 0;
	$ship_days            = 0;
	$local_ship_charge    = 0;
	$zonal_ship_charge    = 0;
	$national_ship_charge = 0;
	$product_code         = "";
	$brand_id             = "";
	$unit_id             = "";
			$display_unit		  = "";
	$hsn_code             = "";
	$subpid               = "";
	$is_free              = 0;
	if(isset($_REQUEST['submit'])){
		// var_dump($_REQUEST);exit;
		$detail=array();
						// $detail['image_path']				= "";
		$detail['image_path']     = $db->clean($_REQUEST['image_path']);
		$detail['old_image_path'] = isset($_REQUEST['old_image_path']) ? $db->clean($_REQUEST['old_image_path']) : '';
		$detail['product_type']   = $db->clean($_REQUEST['product_type']);
		$detail['name']           = $db->clean($_REQUEST['name']);
				// $detail['gujrati_name']		= $db->clean($_REQUEST['gujrati_name']);
					//$detail['weight']			= $db->clean(trim($_REQUEST['weight']));
		$weights = array();
		if (isset($_REQUEST['weights_chk']['weights']) && is_array($_REQUEST['weights_chk']['weights'])) {
			$weights = $_REQUEST['weights_chk']['weights'];
		}

		//$detail['opening_stock_qty']=$weights['stock'];
					//$detail['min_qty']			=$weights['min'];
		$detail['weights']      = $weights;
		$detail['tcid']         = $db->clean($_REQUEST['tcid']);
		$detail['brand_id']     = 0;
		$detail['cid']          = $db->clean($_REQUEST['cid']);
		$detail['unit_id']      = $db->clean($_REQUEST['unit_id']);
		$detail['customer_unit_id']      = $db->clean($_REQUEST['customer_unit_id']);
		$detail['display_unit']      = $db->clean($_REQUEST['display_unit']);
		$detail['is_free']      = isset($_REQUEST['is_free']) ? $db->clean($_REQUEST['is_free']) : 0;
		
		$detail['hsn_code']     = $db->clean($_REQUEST['hsn_code']);
		$detail['name']         = $db->clean(trim($_REQUEST['name']));
		$detail['product_code'] = $db->clean(trim($_REQUEST['product_code']));
		$detail['max_price']    = $db->clean($db->rp_num(trim($_REQUEST['max_price'])));
		$detail['sell_price']   = $db->clean($db->rp_num(trim($_REQUEST['sell_price'])));
		$detail['pro_tax']      = isset($_REQUEST['pro_tax']) ? round($_REQUEST['pro_tax']) : 0;
				/*$detail['ship_days'] 		= $db->clean(intval(trim($_REQUEST['ship_days'])));
		$detail['local_ship_charge']= $db->clean($db->rp_num(trim($_REQUEST['local_ship_charge'])));
		$detail['zonal_ship_charge']= $db->clean($db->rp_num(trim($_REQUEST['zonal_ship_charge'])));
		$detail['national_ship_charge']= $db->clean($db->rp_num(trim($_REQUEST['national_ship_charge'])));
						$detail['qty'] 				= $db->clean($_REQUEST['qty']);
					$detail['packing'] 			= $db->clean($_REQUEST['packing']);
						$detail['cartoon'] 				= $db->clean($_REQUEST['cartoon']);
			$detail['min_qty_alert'] 	= $db->clean($_REQUEST['min_qty_alert']);*/
			$detail['igst'] 	= isset($_REQUEST['igst'])?$db->clean($_REQUEST['igst']):"18";
			$detail['cgst'] 	= ($_REQUEST['igst']!=0)?$db->clean($_REQUEST['igst']/2):"9";
			$detail['sgst'] 	= ($_REQUEST['igst']!=0)?$db->clean($_REQUEST['igst']/2):"9";
			/*$detail['igst'] 	= $db->clean($_REQUEST['igst']);
			$detail['cgst'] 	= $db->clean($_REQUEST['cgst']);
			$detail['sgst'] 	= $db->clean($_REQUEST['sgst']);*/
			$detail['status'] 	= isset($_REQUEST['status']) ? $db->clean($_REQUEST['status']) : 1;
					$detail['isDelete']			= 0;
		/*	if($qty==0){
					$detail['status']  		= 1;
			}*/
				$detail['slug'] 		= $db->clean($db->rp_createProSlug($detail['name']));
			// $detail['display_order']	= $db->rp_getDisplayOrder($ctable,"cid='".$cid."'");
				$detail['descr'] 		= $db->clean(htmlentities($_REQUEST['descr']));
				$ctable_q = "SELECT MAX(sell_price) as max_price,MIN(sell_price) as min_price FROM ( SELECT sell_price FROM product p WHERE $ctable_where UNION SELECT sell_price FROM sub_product s WHERE $ctable_where ) AS tmp  ";
			$attr_count = isset($_REQUEST['attr_count']) ? intval($_REQUEST['attr_count']) : 0;
			$detail['attr_count'] 	= $attr_count;
		$arrayAttr = array();
		for($i=1;$i<=$attr_count;$i++){
			$attrValArr = isset($_REQUEST['attr_'.$i]) ? $_REQUEST['attr_'.$i] : array();
			if(is_array($attrValArr) && count($attrValArr)>0){
				//array_push($arrayAttr,array($i=>$attrValArr));
				$arrayAttr[$i] = $attrValArr;
			}
		}
			$detail['attr'] 	= addslashes(serialize($arrayAttr));
		
		/*if(isset($_SESSION['image_path']) && $_SESSION['image_path']!=""){
			copy(PRODUCT_T.$_SESSION['image_path'], PRODUCT_A.$_SESSION['image_path']);
			$detail['image_path'] = $_SESSION['image_path'];
			
			////// - Product Thumb Starts - //////
			include('resize_image.php');
			$image = new SimpleImage();
			$image->load(PRODUCT_A.$detail['image_path']);
			$image->resize(195,245);
			$image->save(PRODUCT_THUMB_A.$detail['image_path']);
			$image->resize(85,100);
			$image->save(PRODUCT_THUMB_SMALL_A.$detail['image_path']);
			////// - Product Thumb Ends - //////
			
			//unlink(PRODUCT_T.$_SESSION['image_path']);
			unset($_SESSION['image_path']);
		}*/


		// print_r($detail);exit;
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
			if($rights['insert_flag']!=1)
			{
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}
			//print_r($detail);
			$reply=$objProduct->InsertProduct($detail,$_FILES);
			if($reply['ack']==1)
			{
				$db->addSuccessMessage($reply['ack_msg']);
				$db->rp_location($ctable."_manage.php?msg=inserted");
			}else{
					$db->addErrorMessage($reply['ack_msg']);
				}
			}
			
		else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
		{
				// print_r($detail);exit;
			$detail['id']=$_REQUEST['id'];
			if($rights['update_flag']!=1)
			{
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}
			
			$reply=$objProduct->UpdateProduct($detail,$_FILES);
			if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=updated");
			}
			else{
					$db->addErrorMessage($reply['ack_msg']);
				}
			
		}
	}
	if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
		if($rights['update_flag']!=1)
			{
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}
			$where = " id='".$_REQUEST['id']."' AND isDelete=0";
			$ctable_r = $db->rp_getData($ctable,"*",$where);
				$detail['id']=$_REQUEST['id'];
			$reply=$objProduct->GetEditDataProduct($detail);
			// print_r($reply);exit;
			$product_name=$db->rp_getValue("product","name","id='".$detail['id']."' AND isDelete=0",0);
			$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Product".'&nbsp'." - ".ucwords($product_name);
			if($reply['ack']==1){
				//$SuccessMsg = $reply['ack_msg'];
				$result=$reply['result'];
				
				extract($result);
			
			}else{
				$db->addErrorMessage($reply['ack_msg']);
			}
		
	}
	if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
		if($rights['delete_flag']!=1)
			{
				$db->rp_location('access_denied.php?msg=delete_access_denied');
				}
			$detail['id']=$_REQUEST['id'];
			$reply=$objProduct->DeleteProduct($detail);
			if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
			}
			else{
				$db->addErrorMessage($reply['ack_msg']);
			}
	}
	if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
		$status = $_REQUEST['status'];
			$rows 	= array(
						"isActive"	=> $status
				);
			$where	= "id='".$_REQUEST['id']."'";
		$db->rp_update($ctable,$rows,$where);
		$db->rp_location($ctable."_manage.php?msg=updated");
	}
	?>
	<!DOCTYPE html>
	<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
	<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
	<!--[if !IE]><!-->
	<html lang="en">
		<!--<![endif]-->
		<!-- BEGIN HEAD -->
		<head>
			<meta charset="utf-8"/>
			<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
			<?php include("include_css.php"); ?>
			<!-- <link href="assets/css/demo.html5imageupload.css?v1.3" rel="stylesheet"> -->
			<link rel="stylesheet" type="text/css" href="css/fselect.css"/>
			<link href="assets/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
		</head>
		<body class="page-md">
			<?php include("header.php"); ?>
			<div class="page-container">
				<div class="page-head bg-grey">
					<div class="container">
						<div class="page-title">
							<h1><a href="<?= "product_manage.php"?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
						</div>
					</div>
				</div>
				
				<div class="page-content">
					<div class="container">
						
						<div class="row">
							<div class="col-sm-12">
								<?php $db->printErrorMessage(); ?>
								<?php $db->printSuccessMessage(); ?>
							</div>
						</div>
						
						<div class="row">
							
							<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data"> 
								<div class="col-md-12">
									
									<div class="box-header">
										<div class="col-md-12">
											<button type="submit" name="submit" id="submit" class="btn btn-success" value="Submit">Submit</button>
											<button type="button" class="btn btn-success" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
										</div>
										
									</div>
									
									<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']; ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>">
									<input type="hidden" name="subpid" id="subpid" value="<?php echo isset($subpid) ? $subpid : ''; ?>">
									<input type="hidden" name="status" id="status" value="<?php echo isset($status) && $status !== '' ? $status : 1; ?>">
									<input type="hidden" name="pro_tax" id="pro_tax" value="<?php echo $pro_tax; ?>">
									<input type="hidden" name="is_free" id="is_free" value="<?php echo isset($is_free) ? $is_free : 0; ?>">
									<input type="hidden" name="attr_count" id="attr_count" value="0">
									
									<div class="box-body"  >
										<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="duplicate"){ ?>
										<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
											<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
										<b>Error! This Name is Already Exist. Please Try Another Name</b> </div>
										<?php } ?>
										
										<div class="col-md-5" style="margin-top:20px;">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																Product General Information
															</div>
														</div>
														<div class="portlet-body">
															<div class="form-group">
																<label for="product_type">Select Type <code>*</code></label>
																<select class="form-control" name="product_type" id="product_type" onchange="getTypeData(this.value);">
																	<option value="">Select Type</option>
																	<option value="1" <?= ($product_type==1)?"selected":""; ?>>With Variant</option>
																	<option value="2" <?= ($product_type==2)?"selected":""; ?>>Without Variant</option>
																</select>
															</div>
															<div class="form-group">
																<label for="tcid">Category <code>*</code></label>
																<select class="form-control" name="tcid" id="tcid" onchange="getCategory(this.value);">
																	<option value="">Select Category</option>
																	<?php
																		$cat_r = $db->rp_getData("top_category_master","*","isDelete=0");
																		if($cat_r && mysqli_num_rows($cat_r)>0){
																			while($cat_d = mysqli_fetch_array($cat_r)){
																	?>
																	<option data-sales_order_unit="<?= $cat_d['unit_id'] ?>" data-customer_order_unit="<?= $cat_d['customer_unit_id'] ?>" value="<?php echo $cat_d['id']; ?>" <?php if($cat_d['id']==$tcid){?> selected <?php } ?>><?php echo $cat_d['name']; ?></option>
																	<?php
																			}
																		}
																	?>
																</select>
															</div>
															<div class="form-group">
																<label for="cid">Sub Category <code>*</code></label>
																<select class="form-control" name="cid" id="cid">
																	<option value="">Select Sub Category</option>
																	<?php
																	if($tcid!="" && $tcid>0){
																		$scat_r = $db->rp_getData("category_master","*","isDelete=0 AND tcid='".$tcid."'","","0");
																		if($scat_r && mysqli_num_rows($scat_r)>0){
																			while($scat_d = mysqli_fetch_array($scat_r)){
																	?>
																	<option value="<?php echo $scat_d['id']; ?>" <?php if($scat_d['id']==$cid){?> selected <?php } ?>><?php echo $scat_d['name']; ?></option>
																	<?php
																			}
																		}
																	}
																	?>
																</select>
															</div>
															<!-- <div class="form-group">
																	<label for="brand_id">Brand <code>*</code></label>
																	<select class="form-control" name="brand_id" id="brand_id">
																			<option value="">Select Brand</option>
																	<?php
																		$brand_r = $db->rp_getData("brand","*","isDelete=0");
																		if(mysqli_num_rows($brand_r)>0){
																			while($brand_d = mysqli_fetch_array($brand_r)){
																	?>
																	<option value="<?php echo $brand_d['id']; ?>" <?php if($brand_d['id']==$brand_id){?> selected <?php } ?>><?php echo $brand_d['name']; ?></option>
																	<?php
																			}
																		}
																	?>
																</select>
															</div>		 -->
															<div class="form-group">
																<label for="name">Name<code>*</code></label>
																<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>">
															</div>
															<div class="form-group without_size1 hidden">
																<label for="product_code">Product Code</label>
																<input type="text" class="form-control" name="product_code" id="product_code" value="<?php echo $product_code; ?>">
															</div>
															<div class="form-group without_size1 hidden">
																<label for="qty">Qty</label>
																<input type="text" class="form-control positive" name="qty" id="qty" value="<?php echo $qty; ?>">
															</div>
															<!--div class="form-group">
																<label for="gujrati_name">Name in Gujrati</label>
															<input type="text" class="form-control" name="gujrati_name" id="gujrati_name" value="<?php echo $gujrati_name; ?>" >
														</div-->
														
														<div class="form-group hidden">
															<div class="row">
																<div class="col-md-4">
																	<label for="gujrati_name">CGST<code>*</code></label>
																	<input type="text" class="form-control" name="cgst" id="cgst" value="<?php echo $cgst; ?>" onchange="gst_value()">
																</div>
																
																<div class="col-md-4 ">
																	<label for="gujrati_name">SGST<code>*</code></label>
																	<input type="text" class="form-control" name="sgst" id="sgst" value="<?php echo $sgst; ?>"  onchange="gst_value()">
																</div>
																
																<div class="col-md-4 ">
																	<label for="gujrati_name">IGST<code>*</code></label>
																	<input type="text" class="form-control" name="igst" id="igst" value="<?php echo $igst; ?>" >
																	<!-- <select class="form-control" name="igst" id="igst">
																			<option>Select GST</option>
																		<?php
																		$tax_r=$db->rp_getData("tax","*","isDelete=0");
																		if($tax_r){
																			while($tax_d=mysqli_fetch_assoc($tax_r))
																			{
																		?>
																		<option value="<?= $tax_d['variant_value'];?>" <?= ($igst==$tax_d['variant_value'])?"selected":""; ?>><?= $tax_d['variant_value'];?></option>
																		<?php
																		}
																		}
																		?>
																	</select> -->
																</div>
															</div>
														</div>
														
														<div class="form-group">
															<label for="igst">GST <code>*</code></label>
															<select class="form-control" name="igst" id="igst">
																<option value="">Select GST</option>
																<?php
																	$tax_r=$db->rp_getData("tax","*","isDelete=0");
																	if($tax_r){
																		while($tax_d=mysqli_fetch_assoc($tax_r))
																		{
																?>
																<option value="<?= $tax_d['value'];?>" <?= ($igst==$tax_d['value'])?"selected":""; ?>><?= $tax_d['value'];?></option>
																<?php
																}
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label for="unit_id">Sales Order Unit</label>
															<select class="form-control" name="unit_id" id="unit_id">
																<option value="">Select Sales Order Unit</option>

																<option <?=($unit_id==-1)?"selected":""; ?> value="-1">Box</option>
																<option <?=($unit_id==-2)?"selected":""; ?> value="-2">Strip</option>
																<option <?=($unit_id==-3)?"selected":""; ?> value="-3">Pallet</option>
																<option <?=($unit_id==1)?"selected":""; ?> value="1">Caret</option>
																<option <?=($unit_id==2)?"selected":""; ?> value="2">Big Box</option>
																<option <?=($unit_id==100)?"selected":""; ?> value="100">Nos</option>
																<!-- <option <?=($unit_id==1)?"selected":""; ?> value="1">Qty</option>
																<option <?=($unit_id==2)?"selected":""; ?> value="2">Bag</option>
																<option <?=($unit_id==3)?"selected":""; ?> value="3">Box</option>
																<option <?=($unit_id==4)?"selected":""; ?> value="4">Bag, Box</option>
																<option <?=($unit_id==5)?"selected":""; ?> value="5">Bag, Box, Pcs</option> -->
															</select>
														</div>
														<div class="form-group">
															<label for="customer_unit_id">Customer Order Unit</label>
															<select class="form-control" name="customer_unit_id" id="customer_unit_id">
																<option value="">Select Customer Order Unit</option>
																<option <?=($customer_unit_id==-1)?"selected":""; ?> value="-1">Box</option>
																<option <?=($customer_unit_id==-2)?"selected":""; ?> value="-2">Strip</option>
																<option <?=($customer_unit_id==-3)?"selected":""; ?> value="-3">Pallet</option>
																<option <?=($customer_unit_id==1)?"selected":""; ?> value="1">Caret</option>
																<option <?=($customer_unit_id==2)?"selected":""; ?> value="2">Big Box</option>
																<option <?=($customer_unit_id==100)?"selected":""; ?> value="100">Nos</option>
																<!-- <option <?=($unit_id==1)?"selected":""; ?> value="1">Qty</option>
																<option <?=($unit_id==2)?"selected":""; ?> value="2">Bag</option>
																<option <?=($unit_id==3)?"selected":""; ?> value="3">Box</option>
																<option <?=($unit_id==4)?"selected":""; ?> value="4">Bag, Box</option>
																<option <?=($unit_id==5)?"selected":""; ?> value="5">Bag, Box, Pcs</option> -->
															</select>
														</div>
														<div class="form-group">
															<label for="unit_id">Dispaly Unit</label>
															<select class="form-control" name="display_unit" id="display_unit">
																<option value="">Select Unit</option>
																<?php
																$unit_r = $db->rp_getData("unit","*","isDelete=0");
																if($unit_r)
																{
																	while($unit_d=mysqli_fetch_assoc($unit_r))
																	{
																?>
																<option value="<?= $unit_d['id'];?>" <?= ($display_unit==$unit_d['id'])?"selected":""; ?>><?= $unit_d['name'];?></option>
																<?php
																}
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label for="name">HSN Code</label>
															<input type="text" class="form-control" name="hsn_code" id="hsn_code" value="<?php echo $hsn_code; ?>">
														</div>
														
													</div>
												</div>
												
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-12">
												<div class="portlet grey-cascade box">
													<div class="portlet-title">
														<div class="caption">Product Image
														</div>
													</div>
													<div class="portlet-body">
														
														<div class="form-group pimg">
															<div class="row">
																<div class="col-md-6">
																	<!-- <input type="hidden" name="filename" id="filename" class="form-control" />
																<small>minimum image size 100 x 100</small> </label>
																<br />
																<div class="dropzone" data-width="100" data-height="100" data-ghost="false" data-originalsize="false" data-url="crop_product.php" style="width: 240px;height:240px;">
																		<input type="file" id="image_path" name="image_path" >
																</div>
																<input type="hidden" id="old_image_path" name="old_image_path" value="<?php echo $image_path; ?>" /> -->
																<div class="form-group">
																	<input type="hidden" id="old_image_path" name="old_image_path" value="<?php echo isset($image_path) ? $image_path : ''; ?>" />
																	<input data-image="<?php echo ($image_path!="" && file_exists(PRODUCT_A.$image_path))?PRODUCT_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
																</div>
															</div>
															<!-- <div class="col-md-4">
																<?php
																if($image_path!="" && file_exists(PRODUCT_THUMB_A.$image_path)){
																?>
																<br>
																<img src="<?php echo PRODUCT_THUMB_A.$image_path;?>" width="100" height="100" style="margin-top:5px;">
																<?php
																}
																?>
															</div> -->
														</div>
													</div>
													
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="portlet grey-cascade box">
												<div class="portlet-title">
													<div class="caption">Extra Information
													</div>
												</div>
												<div class="portlet-body">
													<div class="form-group">
														<label for="descr">Description</label>
														<textarea type="text" class="form-control" id="descr" name="descr" rows="3" cols="60"><?php echo $descr; ?></textarea>
													</div>
													<!-- <div class="form-group">
														<label for="product">Product Type</label>
														<select name="is_free" id="is_free" class="form-control">
															<option value="0" <?=($is_free==0)?"selected":"";?>>Product</option>
															<option value="1" <?=($is_free==1)?"selected":"";?>>Free Product</option>
														</select>													
													</div> -->
												</div>
											</div>
										</div>
									</div>
									<div class="box-footer">
										<div class="col-md-12">
											<button type="submit" name="submit" id="submit" class="btn btn-success" value="Submit">Submit</button>
											<button type="button" class="btn btn-success" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
										</div>
									</div>
								</div>
								<div class="col-md-7 getSize" style="margin-top:20px;">
								</div>
								
							</div>
							
							
						</div>
					</div>
				</div>
			</div>
		</div><?php include("footer.php"); ?>
		<?php include("include_js.php"); ?>
		<!-- <script src="assets/js/pro_html5imageupload.js?v1.3.4"></script> -->
		<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
		<script src="js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
		<script  type="text/javascript" src="js/fselect.js"></script>
		<script src="js/select2/select2.min.js"></script>
		<script src="assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
		<script type="text/javascript">
			$(function(){
				var isImageThumbnailLoadedReply = '';
				aj.imageHolder($("input[name=image_path]"),"","",
				function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
					isImageThumbnailLoaded=isImageThumbnailLoadedReply;
					isImageThumbnailValidT=isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file,img)
				{
					if(!file)
					{
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
					isImageThumbnailLoaded=isImageThumbnailLoadedReply;
					isImageThumbnailValidT=isImageThumbnailValidReply;
						//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
					},
				function(data){
					isImageThumbnailLoadedReply
				},
				["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
				);
			})
		</script>
		<script type="text/javascript">
			
			// $('input.weights_chk').iCheck();
			jQuery(document).on('icheck', function(){ jQuery('input.weights_chk').iCheck({ checkboxClass: 'icheckbox_square-blue' }); }).trigger('icheck');
			
					$(document).ready(function(){
				$(".weightPriceInput").numeric();
				$("#qty").numeric();
				$(".weightStockInput").numeric();
				$(".weightMinAlertInput").numeric();
				
			});
		</script>
		<script type="text/javascript">
		//---------code for enter only number(0-9) not '.' or '-'-------------//
		//--------------------------------------------------------------//
		$(document).ready(function(){
				CKEDITOR.replace('descr');
		var mode="<?= $_REQUEST['mode']; ?>";
		if(mode=="edit")
		{
		var typeval=$("#product_type").val();
		getTypeData(typeval);
		}
		});
		</script>
		<script type="text/javascript">
		function check_form(){
			var isValid=true;
			var l=$('input[type="checkbox"]:checked').length;
			if($("#product_type").val()==""){
				toastr.error("Please select Type.");
				$("#product_type").focus();
				isValid= false;
				}
			if($("#tcid").val()==""){
				toastr.error("Please select top category.");
				$("#tcid").focus();
				isValid= false;
				}
			if($("#brand_id").val()==""){
				toastr.error("Please select top category.");
				$("#brand_id").focus();
				isValid= false;
				}
			if($("#cid").val()==""){
				toastr.error("Please select category.");
				$("#cid").focus();
				isValid= false;
				}
			if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
				toastr.error("Please enter name.");
				$("#name").focus();
				isValid= false;
			}
			if(l==0)
			{
				toastr.error("Please Select Atleast one Size");
				isValid= false;
			}
			var check_count=0;
			var minAlert = 0;
			var stock = 0;
			$('input.weights_chk').each(function(i,v){
				if($(this).prop("checked")==true)
				{
					check_count++;
					var id=$(this).val();
					var price =$("#weightPriceInput"+id).val();
				// var stock=parseInt($("#weightStockInput"+id).val());
				//var minAlert=parseInt($("#weightMinAlertInput"+id).val());
					var inner=parseInt($("#weightInnerInput"+id).val());
					var outer=parseInt($("#weightOuterInput"+id).val());
					
					
					if(price=="" || inner=="" || outer=="")
					{
						
						toastr.error("Enter valid value for price,stock,inner and outer and min.qty alert for varient "+$(this).data("name"));
						$("#weightPriceInput").focus();
						isValid=false;
					}
					else
					{
						if(minAlert>stock)
						{
							toastr.error("Min.qty alert must be less than or equal to opening qty. for varient "+$(this).data("name"));
							isValid=false;
						}
					}
						}
			});
			// alert(check_count);
			return isValid;
		}
		function getTypeData(typeval)
			{
		var mode="<?= $_REQUEST['mode']; ?>";
		var id="<?= isset($_REQUEST['id'])?$_REQUEST['id']:""; ?>";
		$.ajax({
		type: "POST",
		url: "get_size_from_product_type.php",
		data: 'type='+typeval+'&mode='+mode+'&id='+id,
		success: function(result){
		$(".getSize").html(result);
		}
		});
		}
		</script>
		<script>
		function getCategory(id)
		{
			/*var sales_order_unit = $("#tcid").find('option:selected').data('sales_order_unit');
			var customer_order_unit = $("#tcid").find('option:selected').data('customer_order_unit'); 

			$("#customer_unit_id").select2("destroy");
			$("#customer_unit_id").val(customer_order_unit);
			$("#customer_unit_id").select2();
			
			$("#unit_id").select2("destroy");
			$("#unit_id").val(sales_order_unit);
			$("#unit_id").select2()*/

			$.ajax({
				type: "POST",
				url: "ajax_getCategory.php",
				data: 'tcid='+id,
				success: function(result){
						//$("#cid").html(result);
						$('#cid').select2('destroy');
						$('#cid').html(result);
						$('#cid').select2();
					
					}
			});
			//category_id_display(id);
		}
		
		// function category_id_display(id)
		// {
		// 	$.ajax({
		// 		type: "POST",
		// 		url: "ajax_category_unit.php",
		// 		data: 'tcid='+id,
		// 		success: function(result){
		// 				//$("#cid").html(result);
		// 			}
		// 	});
		// }
		$(".positive").keypress(function(event) {
				if ( event.keyCode == 46 || event.keyCode == 8 ) {
				// let it happen, don't do anything
				} else if (/\D/g.test(this.value)) {
				toastr.error("Sorry!! Only Digits Allowed");
				this.value = this.value.replace(/\D/g, '');
				}
				});
		/*function gst_value()
		{
			var x = $("#cgst").val();
			var y = $("#sgst").val();
			var z = $("#igst").val();
		if(x > 100)
			{
				$("#cgst").val(0);
				toastr.error("CGST tax not greater than 100%");
			}
			
			if(y > 100)
			{
				$("#sgst").val(0);
				toastr.error("SGST tax not greater than 100%");
			}
			
			if(z > 100)
			{
				$("#igst").val(0);
				toastr.error("IGST tax not greater than 100%");
			}
		}*/
		</script>
	</body>
	</html>