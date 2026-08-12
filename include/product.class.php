<?php
require_once("main.class.php");
require_once("function.class.php");
class Product extends Functions
{
	public $db;
	public $ctable = "product";
	public $order_status = array("-2" => "Disapproved", "0" => "Waiting For Approval", "1" => "Waiting For Account Approval", "3" => "Cancelled", "4" => "Account Approved", "5" => "Dispatch", "6" => "Order Complate");

	function __construct($id = "")
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
	}
	public function InsertProduct($detail, $file)
	{
		// print_r($detail);exit();
		extract($detail);
		$dup_where = "name = '" . $name . "' AND cid='" . $cid . "' AND isDelete=0";
		//$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if ($r) {
			$reply = array("ack" => 0, "developer_msg" => "Already Exist Catno Name", "ack_msg" => "Duplication! Already Exist Catno Name.");
			return $reply;
		} else {
			if (isset($file["image_path"])) {
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["image_path"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($file["image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$image_path	= 'image_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= PRODUCT_A . $image_path;
					$file['image_path']['tmp_name'];
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$image_path = "";
				}
			} else {
				$new_image = false;
				$image_path = "";
			}
			$maximum_display_order = $this->db->rp_getValue($this->ctable, "MAX(display_order)", "isDelete=0");
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"product_type",
				"tcid",
				"cid",
				"name",
				"product_code",
				"max_price",
				"sell_price",
				"pro_tax",
				"slug",
				"image_path",
				"display_order",
				"descr",
				"cgst",
				"sgst",
				"igst",
				"brand_id",
				"unit_id",
				"display_unit",
				"hsn_code",
				"is_free",
				"customer_unit_id",
			);
			$values = array(
				$product_type,
				$tcid,
				$cid,
				$name,
				$product_code,
				$max_price,
				$sell_price,
				$pro_tax,
				$slug,
				$image_path,
				$maximum_display_order + 1,
				$descr,
				$cgst,
				$sgst,
				$igst,
				$brand_id,
				$unit_id,
				$display_unit,
				$hsn_code,
				$is_free,
				$customer_unit_id,
			);

			/*genrate a compress image*/
			if (isset($image_path) && !empty($image_path)) {
				$localSource = PRODUCT_A . $image_path;
				if (file_exists($localSource)) {
					$compressedImage = PRODUCT_THUMB_A . $image_path;
					$this->db->compressImage($localSource, $compressedImage);
				}
			}
			/*genrate a compress image*/

			/*log entry*/
			$module_name = "Product";
			$flag = "Web";
			$log_description = $module_name . " " . $name . " Created By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			/*log entry*/

			$product_id = $this->db->rp_insert($this->ctable, $values, $rows, 0, $log_description, $flag, $module_name, "", "");

			if ($product_id != 0) {
				foreach ($weights as $w) {
					if ($w['id'] != "") {
						$weight_id = $w['id'];
						$price = $w['price'];
						// $stock=$w['stock'];
						$opening_stock = $w['stock'];
						$current_stock = $w['current_stock'];
						$min = 0;
						//$inner=$w['inner'];				
						// $inner=0;				
						// $outer=1;		
						$inner = $w['inner'];
						$outer = $w['outer'];
						$inner_unit = ($w['inner_unit']) ? $w['inner_unit'] : "";
						$outer_unit = ($w['outer_unit']) ? $w['outer_unit'] : "";

						$catno = $w['catno'];
						$weight_in_kg = $w['weight_in_kg'];
						$w['pro_active'] = (!isset($w['pro_active']) || $w['pro_active'] == "") ? 0 : 1;
						$pro_active = $w['pro_active'];
						$pro_weight = $w['pro_weight'];
						$size_inner = $w['inner_size'];
						$inner_cft = $w['inner_cft'];
						$outer_cbm = $w['outer_cbm'];
						$inner_cbm = $w['inner_cbm'];

						$min_stock_qty = $w['min_stock_qty'];
						$max_stock_qty = $w['max_stock_qty'];
						$minimum_selling_price = isset($w['minimum_selling_price']) ? $w['minimum_selling_price'] : 0;

						$size_outer = $w['outer_size'];
						$outer_cft = $w['outer_cft'];
						if ($w['is_including']) {
							$is_including = 1;
						} else {
							$is_including = 0;
						}


						$dup_where = "catno = '" . $catno . "' AND isDelete=0";
						$r1 = $this->db->rp_dupCheck("product_weight_price", $dup_where, 0);
						if ($r1) {
							$reply = array("ack" => 0, "developer_msg" => "Already Exist Product Name", "ack_msg" => "Duplication! Already Exist Product Name.");
							return $reply;
						} else {
							$rows1 = array("weight_id", "product_id", "price", "stock_qty", "opening_stock_qty", "min_qty", "inner_size", "outer_size", "catno", "pro_weight", "size_inner", "inner_cft", "size_outer", "outer_cft", "inner_cbm", "outer_cbm", "min_stock_qty", "max_stock_qty", "is_including", "inner_unit", "outer_unit", "minimum_selling_price");


							$values1 = array($weight_id, $product_id, $price, $current_stock, $opening_stock, $min, $inner, $outer, $catno, $pro_weight, $size_inner, $inner_cft, $size_outer, $outer_cft, $inner_cbm, $outer_cbm, $min_stock_qty, $max_stock_qty, $is_including, $inner_unit, $outer_unit, $minimum_selling_price);
							$weight = $this->db->rp_insert("product_weight_price", $values1, $rows1, 0);
						}
					}
				}
			}
			if ($product_id != 0) {
				$reply = array("ack" => 1, "developer_msg" => "Designation Added.", "ack_msg" => "Success! Product Insert Successfully.");
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Product Insert Failed.");
				return $reply;
			}
		}
	}

	public function UpdateProduct($detail, $file)
	{
		extract($detail);
		//print_r($detail);exit;
		$dup_where = "name = '" . $name . "' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable, $dup_where);
		/*if($r){
				print_r($detail);
				$reply=array("ack"=>1,"developer_msg"=>"Already Exist Product Name","ack_msg"=>"Duplication! Already Exist Product Name.");
				return $reply;
				
			}
			else
			{*/ //echo 'sd';exit;
		/*if($_REQUEST['old_image_path']!="" && $image_path!=""){
					if(file_exists(PRODUCT_A.$_REQUEST['old_image_path'])){
						unlink(PRODUCT_A.$_REQUEST['old_image_path']);
					}
					if(file_exists(PRODUCT_THUMB_A.$_REQUEST['old_image_path'])){
						unlink(PRODUCT_THUMB_A.$_REQUEST['old_image_path']);
					}
				}else{
					if($image_path==""){
						$image_path = $_REQUEST['old_image_path'];
						if($image_path == ""){
							$image_path = "";	
						}
					}
				}*/
		if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
			$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
			$temp = explode(".", $file["image_path"]["name"]);
			$extension = end($temp);

			$fileName 	= $this->db->clean($file["image_path"]["name"]);
			if ($fileName != "") {
				$fileSize 	= round($file["image_path"]["size"]); // BYTES									
				$adate 		= date('Y-m-d H:i:m');

				$extension	= end(explode(".", $fileName));
				if (!in_array($extension, $allowedExts)) {
					$file_error = true;
				}
				$image_path	= 'image_' . substr(sha1(time()), 0, 6) . "." . $extension;
				$filePath 	= PRODUCT_A . $image_path;
				$file['image_path']['tmp_name'];
				move_uploaded_file($file['image_path']['tmp_name'], $filePath);
				$new_image = true;
			} else {
				$image_path = $detail['old_image_path'];
				$image_path = "";
			}
		} else {
			$image_path = $detail['old_image_path'];
			unset($detail['old_image_path']);
		}
		/*genrate a compress image*/
		if (isset($image_path) && !empty($image_path)) {
			$localSource = PRODUCT_A . $image_path;
			if (file_exists($localSource)) {
				$compressedImage = PRODUCT_THUMB_A . $image_path;
				$this->db->compressImage($localSource, $compressedImage);
			}
		}
		/*genrate a compress image*/
		if (!isset($weights) || !is_array($weights)) {
			$weights = array();
		}
		$rows 	= array(
			"product_type" => $product_type,
			"tcid"         => $tcid,
			"cid"          => $cid,
			"name"         => $name,
			"product_code" => $product_code,
			"max_price"    => $max_price,
			"sell_price"   => $sell_price,
			"pro_tax"      => $pro_tax,
			"slug"         => $slug,
			"image_path"   => $image_path,
			"descr"        => $descr,
			"cgst"         => $cgst,
			"sgst"         => $sgst,
			"igst"         => $igst,
			"brand_id"     => $brand_id,
			"unit_id"      => $unit_id,
			"display_unit" => $display_unit,
			"hsn_code"     => $hsn_code,
			"is_free"     => $is_free,
			"customer_unit_id"     => $customer_unit_id,

			//"opening_stock" => $opening_stock,
			//"min_stock"		=> $min_stock,
			//"max_stock"		=> $max_stock,
			// "gujrati_name"	=> $gujrati_name,
			/*"ship_days"				=> $ship_days,
					"local_ship_charge"		=> $local_ship_charge,
					"zonal_ship_charge"		=> $zonal_ship_charge,
					"national_ship_charge"	=> $national_ship_charge,
					"stock_qty"			=> $qty,
					"qty"			=> $qty,
					"min_qty_alert"	=> $min_qty_alert,
					"status"		=> $status,
					"attr"			=> $attr,
					"packing"		=> $packing,
					"cartoon"		=> $cartoon,*/
		);
		$where	= "id='" . $id . "'";
		/*log entry*/
		$module_name = "Product";
		$flag = "Web";
		$log_description = $module_name . " " . $name . " Edited By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
		/*log entry*/
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0, $log_description, $flag, $module_name, "", "");

		$product_id = $id;
		$validWeights = array();
		foreach ($weights as $w) {
			if (isset($w['id']) && $w['id'] !== "") {
				$validWeights[] = $w;
			}
		}

		if (count($validWeights) > 0) {
			$this->db->rp_delete("product_weight_price", "product_id = '" . $id . "'");
		}

		foreach ($validWeights as $w) {
			//print_r($w);exit;
			if ($w['id'] != "") {
				$weight_id = $w['id'];
				$price = isset($w['price']) ? $w['price'] : 0;
				$opening_stock = isset($w['stock']) ? $w['stock'] : 0;
				$current_stock = isset($w['current_stock']) ? $w['current_stock'] : 0;
				$min = 0;
				//$inner=0;			
				$inner = isset($w['inner']) ? $w['inner'] : 0;
				$outer = isset($w['outer']) ? $w['outer'] : 0;
				$inner_unit = (isset($w['inner_unit']) && $w['inner_unit']) ? $w['inner_unit'] : "";
				$outer_unit = (isset($w['outer_unit']) && $w['outer_unit']) ? $w['outer_unit'] : "";
				$catno = isset($w['catno']) ? $w['catno'] : "";
				$weight_in_kg = 0;
				$w['pro_active'] = 0;
				$pro_active = $w['pro_active'];
				$pro_weight = isset($w['pro_weight']) ? $w['pro_weight'] : 0;
				$size_inner = isset($w['inner_size']) ? $w['inner_size'] : "";
				$inner_cft = isset($w['inner_cft']) ? $w['inner_cft'] : "";
				$inner_cbm = isset($w['inner_cbm']) ? $w['inner_cbm'] : "";
				$min_stock_qty = isset($w['min_stock_qty']) ? $w['min_stock_qty'] : 0;
				$max_stock_qty = isset($w['max_stock_qty']) ? $w['max_stock_qty'] : 0;
				$minimum_selling_price = isset($w['minimum_selling_price']) ? $w['minimum_selling_price'] : 0;
				$size_outer = isset($w['outer_size']) ? $w['outer_size'] : "";
				$outer_cft = isset($w['outer_cft']) ? $w['outer_cft'] : "";
				$outer_cbm = isset($w['outer_cbm']) ? $w['outer_cbm'] : "";
				$is_including = (isset($w['is_including']) && $w['is_including']) ? 1 : 0;

				$rows1 = array("weight_id", "product_id", "price", "stock_qty", "opening_stock_qty", "min_qty", "inner_size", "outer_size", "catno", "pro_weight", "size_inner", "inner_cft", "size_outer", "outer_cft", "inner_cbm", "outer_cbm", "min_stock_qty", "max_stock_qty", "is_including", "inner_unit", "outer_unit", "minimum_selling_price");

				$values1 = array($weight_id, $product_id, $price, $current_stock, $opening_stock, $min, $inner, $outer, $catno, $pro_weight, $size_inner, $inner_cft, $size_outer, $outer_cft, $inner_cbm, $outer_cbm, $min_stock_qty, $max_stock_qty, $is_including, $inner_unit, $outer_unit, $minimum_selling_price);
				$check_insert = $this->db->rp_insert("product_weight_price", $values1, $rows1, 0);

				// update to price list if item found
				if ($check_insert > 0) {
					$check_item_in_price_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $product_id . "' AND weight_id='" . $weight_id . "' AND isDelete=0");
					if ($check_item_in_price_list > 0) {
						$price_list_r = $this->db->rp_getData("product_price_list", "discount,price_list_id,id", "pid='" . $product_id . "' AND weight_id='" . $weight_id . "' AND isDelete=0", "", 0);
						if ($price_list_r) {
							while ($price_list_d = mysqli_fetch_assoc($price_list_r)) {
								//print_r($price_list_d);
								$discount = $price_list_d['discount'];
								$discounted_amount = ($discount * $price) / 100;
								$discounted_price = $price - $discounted_amount;

								$update_array = array(
									"price" => $price,
									"discounted_price" => $discounted_price,
									"discounted_amount" => $discounted_amount,
								);
								$update_price_list = $this->db->rp_update("product_price_list", $update_array, "id='" . $price_list_d['id'] . "'", 0);
								// check in order if item in added to cart
								$user_r = $this->db->rp_getData("customer", "id", "price_list_id='" . $price_list_d['price_list_id'] . "' AND isDelete=0");
								if ($user_r) {
									$USERIDS = array();
									while ($user_d = mysqli_fetch_assoc($user_r)) {
										$USERIDS[] = $user_d['id'];
									}
									if (count($USERIDS) > 0) {
									$USERIDS = implode(",", $USERIDS);

									// check order 
									$order_r = $this->db->rp_getData("orders", "id", "customer_id IN (" . $USERIDS . ") AND status=-1 AND isDelete=0");
									if ($order_r) {
										$ORDERIDS = array();
										while ($order_d = mysqli_fetch_assoc($order_r)) {
											$ORDERIDS[] = $order_d['id'];
										}
										if (count($ORDERIDS) > 0) {
										$ORDERIDS = implode(",", $ORDERIDS);

										$order_item_r = $this->db->rp_getData("order_product_item", "*", "pro_id='" . $product_id . "' AND weight_id='" . $weight_id . "' AND order_id IN (" . $ORDERIDS . ") AND isDelete=0");
										if ($order_item_r) {
											while ($order_item_d = mysqli_fetch_assoc($order_item_r)) {
												// print_r($order_item_d);		

												$totalprice = $order_item_d['pro_qty'] * $discounted_price;
												$totalprice = $this->db->rp_num($totalprice);

												$row_value = array(
													"original_price" => $price,
													"unitprice" => $discounted_price,
													"totalprice" => $totalprice,
													"discount_amount" => $discounted_amount,
													"discount" => round($discount, 2),
													"price_list_id" => $price_list_d['id'],
													"price_list_price" => $price,
													"price_list_discounted_price" => $discounted_price,
													"price_list_discounted_amount" => $discounted_amount,
													"price_list_discount_type" => "1",
													"price_list_discount" => round($discount, 2),
												);

												$updated_id = $this->db->rp_update("order_product_item", $row_value, "id='" . $order_item_d['id'] . "'", 0);
											}
										}
										}
									}
									// check order 
									}
								}
								// check in order if item in added to cart
							}
						}
					}
				}
				// update to price list if item found

			}
			//echo $w['id']."-".$w['name']." :".$w['price']."<br>";
		}
		//exit;
		if ($uid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "Product Update Successfull!!.", "ack_msg" => "Success! Product Update Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Product Update Failed.");
			return $reply;
		}
		//}	
	}
	public function GetEditDataProduct($detail)
	{
		//get product for update
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where);
		if (!$ctable_r) {
			return array("ack" => 0, "developer_msg" => "Product not found.", "ack_msg" => "Failed! Product not found.");
		}
		$ctable_d = mysqli_fetch_array($ctable_r);
		if (!$ctable_d) {
			return array("ack" => 0, "developer_msg" => "Product not found.", "ack_msg" => "Failed! Product not found.");
		}
		$result = array();

		$result['name']		= htmlentities($ctable_d['name']);
		$result['product_type']		= htmlentities($ctable_d['product_type']);
		$result['tcid']			= stripslashes($ctable_d['tcid']);
		$result['cid']			= stripslashes($ctable_d['cid']);
		$result['name']			= htmlentities($ctable_d['name']);
		$result['max_price']		= stripslashes($ctable_d['max_price']);
		$result['sell_price']		= stripslashes($ctable_d['sell_price']);
		$result['pro_tax']		= stripslashes($ctable_d['pro_tax']);
		$result['product_code']		= htmlentities($ctable_d['product_code']);
		$result['unit_id']		= htmlentities($ctable_d['unit_id']);
		$result['hsn_code']		= htmlentities($ctable_d['hsn_code']);
		$result['is_free']		= htmlentities($ctable_d['is_free']);
		/*$result['gujrati_name']		= htmlentities($ctable_d['gujrati_name']);	
		$result['ship_days']				= stripslashes($ctable_d['ship_days']);
		$result['local_ship_charge']		= $this->db->rp_num($ctable_d['local_ship_charge']);
		$result['zonal_ship_charge']		= $this->db->rp_num($ctable_d['zonal_ship_charge']);
		$result['national_ship_charge']	= $this->db->rp_num($ctable_d['national_ship_charge']);
		$result['qty']			= stripslashes($ctable_d['qty']);
		$result['min_qty_alert']	= stripslashes($ctable_d['min_qty_alert']);
		$result['subpid']			= stripslashes($ctable_d['subpid']);
		$result['attr']			= unserialize(stripslashes($ctable_d['attr']));
		$result['packing']		= stripslashes($ctable_d['packing']);
		$result['cartoon']		= stripslashes($ctable_d['cartoon']);
		$result['status']			= stripslashes($ctable_d['status']);*/
		$result['slug']			= stripslashes($ctable_d['slug']);
		$result['image_path'] 	= stripslashes($ctable_d['image_path']);
		$result['cgst'] 	= stripslashes($ctable_d['cgst']);
		$result['sgst'] 	= stripslashes($ctable_d['sgst']);
		$result['igst'] 	= stripslashes($ctable_d['igst']);
		$result['brand_id'] 	= stripslashes($ctable_d['brand_id']);
		$result['display_unit'] 	= stripslashes($ctable_d['display_unit']);
		$result['opening_stock'] 	= stripslashes($ctable_d['opening_stock']);
		$result['min_stock'] 	= stripslashes($ctable_d['min_stock']);
		$result['max_stock'] 	= stripslashes($ctable_d['max_stock']);
		$result['customer_unit_id'] 	= stripslashes($ctable_d['customer_unit_id']);
		// $result['is_including'] 	= stripslashes($ctable_d['is_including']);			

		$weight_ids = array();
		$weight_prices = array();
		$weight_stock = array();
		$weight_min = array();
		$weight_catnos = array();
		$weight_current_stock = array();
		$weight_inner = array();
		$weight_outer = array();
		$weight_inkg = array();
		$weight_pro_weight = array();
		$weight_inner_size = array();
		$weight_inner_cft = array();
		$weight_inner_cbm = array();
		$weight_outer_size = array();
		$weight_outer_cft = array();
		$weight_outer_cbm = array();
		$weight_min_stock_qty = array();
		$weight_max_stock_qty = array();
		$weight_is_including = array();
		$weight_inner_unit = array();
		$weight_outer_unit = array();
		$minimum_selling_price = array();
		$weight_id_d = $this->db->rp_getData("product_weight_price", "weight_id", "product_id='" . $detail['id'] . "' AND isDelete=0", "", 0);
		if ($weight_id_d) {
			while ($w = mysqli_fetch_array($weight_id_d)) {
				$weight_ids[] = $w['weight_id'];
			}
		}

		$result['weight_ids'] = $weight_ids;
		$weight_price_d = $this->db->rp_getData("product_weight_price", "*", "product_id='" . $detail['id'] . "'", "", 0);
		if ($weight_price_d) {
			while ($p = mysqli_fetch_array($weight_price_d)) {
			$weight_prices[] = $p['price'];
			$weight_catnos[] = $p['catno'];
			$weight_stock[] = $p['opening_stock_qty'];
			// $weight_stock[] = $p['opening_stock_qty'];
			$weight_current_stock[] = $p['stock_qty'];
			$weight_min[] = $p['min_qty'];
			$weight_inner[] = $p['inner_size'];
			$weight_outer[] = $p['outer_size'];
			$weight_inkg[] = $p['weight_in_kg'];
			$weight_pro_weight[] = $p['pro_weight'];
			$weight_inner_size[] = $p['size_inner'];
			$weight_inner_cft[] = $p['inner_cft'];
			$weight_inner_cbm[] = $p['inner_cbm'];
			$weight_outer_size[] = $p['size_outer'];
			$weight_outer_cft[] = $p['outer_cft'];
			$weight_outer_cbm[] = $p['outer_cbm'];

			$weight_min_stock_qty[] = $p['min_stock_qty'];
			$weight_max_stock_qty[] = $p['max_stock_qty'];
			$weight_is_including[] = $p['is_including'];
			$weight_inner_unit[] = $p['inner_unit'];
			$weight_outer_unit[] = $p['outer_unit'];
			$minimum_selling_price[] = 0; // Min sell removed — app uses MRP only
			}
		}
		$result['weight_prices'] = $weight_prices;
		$result['weight_catnos'] = $weight_catnos;
		$result['weight_stock'] = $weight_stock;
		$result['weight_current_stock'] = $weight_current_stock;
		$result['weight_min'] = $weight_min;
		$result['weight_inner'] = $weight_inner;
		$result['weight_outer'] = $weight_outer;
		$result['weight_inkg'] = $weight_inkg;
		$result['weight_is_including'] = $weight_is_including;
		$result['weight_inner_unit'] = $weight_inner_unit;
		$result['weight_outer_unit'] = $weight_outer_unit;
		$result['descr'] 			= html_entity_decode($ctable_d['descr']);
		$result['weight_pro_weight'] = $weight_pro_weight;
		$result['weight_inner_size'] = $weight_inner_size;
		$result['weight_inner_cft'] = $weight_inner_cft;
		$result['weight_inner_cbm'] = $weight_inner_cbm;
		$result['weight_outer_size'] = $weight_outer_size;
		$result['weight_outer_cft'] = $weight_outer_cft;
		$result['weight_outer_cbm'] = $weight_outer_cbm;

		$result['weight_min_stock_qty'] = $weight_min_stock_qty;
		$result['weight_max_stock_qty'] = $weight_max_stock_qty;
		$result['min_selling_price'] = $minimum_selling_price;
		$reply = array("ack" => 1, "developer_msg" => "Product detail fetched!!.", "ack_msg" => "Success! Product Edit Successfully.", "result" => $result);

		return $reply;
	}

	public function DeleteProduct($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='" . $_REQUEST['id'] . "'";
		/*log entry*/
		$name = $this->db->rp_getValue("category_master", "name", "id='" . $_REQUEST['id'] . "'");
		$module_name = "Product";
		$flag = "Web";
		$log_description = $module_name . " " . $name . " Deleted By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
		/*log entry*/
		$uid = $this->db->rp_update($this->ctable, $rows, $where, 0, $log_description, $flag, $module_name, "", "");
		if ($uid != 0) {
			$where	= "product_id='" . $_REQUEST['id'] . "'";
			$product_weight_price_id = $this->db->rp_update("product_weight_price", $rows, $where, 0);
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Product Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete Product Failed.");
			return $reply;
		}
	}
	function getRequiredColumns($required_columns = array())
	{
		if (!empty($required_columns)) {
			$required_columns_string = implode(",", $required_columns);
			return $required_columns_string;
		} else {
			return "*";
		}
	}
	function getProductDetail($required_columns)
	{
		$required_columns = $this->getRequiredColumns($required_columns);
		$name = $_REQUEST['name'];
		$limit = $this->getLimit();
		$result = array();
		$where = "name like '%" . $name . "%' AND isDelete=0";
		$data    = $this->db->rp_getData('product', $required_columns, $where, "", 0, $limit);
		if ($data) {
			while ($row = mysqli_fetch_assoc($data)) {

				// Fetching Price According to user if uid exits other wise accrofding to list 0
				$price = $row['max_price'];
				//if uid get from request than all price count upon discount as per executive given
				//else normal price get
				$uid = $_REQUEST['uid'];
				if ($uid) {
					$discountPer = $this->db->rp_getValue("executive", "discount", "id='" . $uid . "'", 0); //$discountPer=$this->db->rp_getValue("pricelist","percentage","id='".$pricelist."'");

					if ($discountPer != 0) {
						$discountAmount = $price * $discountPer / 100;
					} else {
						$discountAmount = 0;
					}

					$finalPrice = $price - $discountAmount;
				} else {
					$finalPrice = $row['max_price'];
				}
				$row['max_price'] = $price;
				$row['sell_price'] = $finalPrice;
				//$row['pricing']=array();
				$weights = $this->db->rp_getData("product_weight_price", "*", "product_id='" . $row['id'] . "'", "", 0);
				if (mysqli_num_rows($weights) > 0) {
					while ($w = mysqli_fetch_assoc($weights)) {
						$price = $w['price'];
						//$stock=$w['opening_stock_qty'];
						//$min=$w['min_qty'];
						//$w['orignal_price']=$price;
						$w['title'] = $this->db->rp_getValue("weight", "name", "id='" . $w['weight_id'] . "'");
						$result[] = $row;
						//$result[]=$w;
					}
				}
			}
			return $result;
		} else {
			return $result;
		}
	}

	function getLimit($limit = array())
	{
		$limit = $this->db->getLimit();
		if (!empty($limit) && array_key_exists("ul", $limit)) {
			$ul = $limit['ul'];
			if (array_key_exists("ll", $limit) && $limit['ll'] != "") {
				$ll = $limit['ll'];
			} else {
				$ll = "18446744073709551615";
			}
			$limit_string = "" . $ul . "," . $ll;
			return $limit_string;
		} else {
			return "";
		}
	}
	//----------Use for get All product Using Product Weight AND customer's Discount-------//
	function aj_getProductDetail($pid, $uid = "")
	{
		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $uid . "'", 0);
		$is_premium = $this->db->rp_getValue("price_list", "is_premium", "id='" . $price_list_id . "'", 0);
		$proudcts = array();
		$q = 1;
		$product_r = $this->db->rp_getData("product", "id,cid,name,max_price,sell_price,pro_tax,image_path,descr,cgst,sgst,igst,brand_id", "id='" . $pid . "' AND isDelete=0", "", 0);
		if (mysqli_num_rows($product_r) > 0) {

			$product_detail = mysqli_fetch_assoc($product_r);

			/*-----------------------------------------------------------*/
			// Fetching Comelete ImagePath of that product
			$product_detail['image_path'] = (file_exists("../" . PRODUCT . $product_detail['image_path'])) ? SITEURL . PRODUCT . $product_detail['image_path'] : "";
			/*-----------------------------------------------------------*/
			// Fetching Price According to user if uid exits other wise accrofding to list 0
			$weights = $this->db->rp_getData("product_weight_price", "id,product_id,weight_id,price,stock_qty,inner_size,outer_size,catno,is_including,minimum_selling_price", "product_id='" . $product_detail['id'] . "' ", "", 0);




			if ($weights) {
				while ($w = mysqli_fetch_assoc($weights)) {
					$total_purchse = $this->db->rp_getValue("purchase_invoice_item", "SUM(pro_qty)", "pro_id='" . $product_detail['id'] . "'  AND weight_id='" . $w['weight_id'] . "' AND isDelete=0");

					$total_sales = $this->db->rp_getValue("sales_invoice_item", "SUM(pro_qty)", "pro_id='" . $product_detail['id'] . "'  AND weight_id='" . $w['weight_id'] . "' AND isDelete=0");

					$diff = ($total_purchse - $total_sales);

					$price = $w['price'];
					// $stock_qty=$w['stock_qty'];
					$stock_qty = $diff;
					$w['orignal_price'] = $price;
					$w['title'] = $this->db->rp_getValue("weight", "name", "id='" . $w['weight_id'] . "'");
					if ($uid != "") {
						$discountPer = $this->db->rp_getValue("price_table", "discount", "uid='" . $uid . "' AND tcid='" . $product_detail['tcid'] . "'", 0);
						//$discountPer=0;
						if ($price_list_id != 0) {
							$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $product_detail['id'] . "' AND weight_id='" . $w['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
							if ($check_product_in_list > 0) {
								$add_price_list_id = $price_list_id;
								$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $product_detail['id'] . "' AND weight_id='" . $w['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
								$unitprice = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $product_detail['id'] . "' AND weight_id='" . $w['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
								$discountPer = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $product_detail['id'] . "' AND weight_id='" . $w['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
								$finalPrice = $this->db->rp_num($unitprice);
							} else {
								$finalPrice = $price;
							}
						} else {
							$finalPrice = $price;
						}
						// $finalPrice=$price-$discountAmount;
					} else {
						$finalPrice = $price;
					}

					// change here
					$w['sell_price'] = round($finalPrice, 2);

					$w['product_id'] = $product_detail['id'];
					$w['pro_id'] = $w['product_id'];
					$w['cid'] = $product_detail['cid'];
					$w['product_name'] = $w['title'] . " " . $product_detail['name'] . " ";
					$w['name1'] = htmlentities($w['title'] . " " . $product_detail['name'] . " ");
					if ($w['catno'] != "") {
						$w['name'] = $product_detail['name'] . " - " . $w['title'] . " - #" . $w['catno'] . "";
					} else {
						$w['name'] = $product_detail['name'] . "(" . $w['title'] . ")";
					}

					$w['max_price'] = $product_detail['max_price'];
					$w['pro_tax'] = $product_detail['pro_tax'];
					$w['image_path'] = $product_detail['image_path'];
					$w['descr'] = $product_detail['descr'];
					$w['cgst'] = $product_detail['cgst'];
					$w['igst'] = $product_detail['igst'];
					$w['sgst'] = $product_detail['sgst'];
					$w['brand_id'] = $product_detail['brand_id'];
					$w['title'] = $w['title'];
					$w['catno'] = $w['catno'];
					$w['bag_qty'] = $w['inner_size'];
					$w['box_qty'] = $w['outer_size'];
					$w['qty'] = $stock_qty;
					$w['discountPer'] = $discountPer;
					$w['final_qty'] = $this->db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $product_detail['id'] . "' AND bid='" . $w['bid'] . "' AND weight_id='" . $w['weight_id'] . "'", 0);
					$w['is_premium'] = ($is_premium) ? $is_premium : 0;
					// Min sell removed for app — MRP is orignal_price / price
					$w['minimum_selling_price'] = 0;

					// print_r($w);exit;
					$proudcts[] = $w;
					$product_detail[] = $w;
				}
			}

			// Fetching Favourite Flag if uid exits
			/*-----------------------------------------------------------*/

			// Fetching Category of that product
			$cid		= $product_detail['cid'];
			$cat_d		= mysqli_fetch_assoc($this->db->rp_getData("category_master", "name", "id=" . $cid));
			$product_detail['category'] = stripslashes($cat_d['name']);
			$product_detail['type'] = stripslashes($cat_d['name']);
			// Fetching Category of that product			
			/*-----------------------------------------------------------*/
			return $proudcts;
		} else {

			return array();
		}
	}
	//-----------------------------------------------------------------------------------//	
	function getProductFromQuery($name = "")
	{
		$result = array();
		$where = "name like '%" . $name . "%' AND isDelete=0";
		$data    = $this->db->rp_getData('product', "*", $where);
		if ($data) {

			while ($row = mysqli_fetch_assoc($data)) {
				$pid = $row['id'];
				$row['orignal_price'] = $row['sell_price'];

				$result[] = $row;
			}
			return $result;
		} else {
			return $result;
		}
	}
	///---------------------------------------------------------------------------------//  
	//24-04-2017-----------------#use for get_oreders service#---------------------------------------------------------//	

	function getOrders($sales_id, $customer_id, $customer_type, $filter, $limit = "")
	{
		$result = array();
		$where = "";
		$where_count = "";
		$class_id = $_REQUEST['class_id'];
		$area_id = $_REQUEST['area_id'];
		$type_of_company = $filter['type_of_company'];
		// $city_id = $_REQUEST['city_id'];
		$status = $_REQUEST['status'];
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";


		if (array_key_exists("first_date", $filter) && $filter['first_date'] != "" && array_key_exists("last_date", $filter) && $filter['last_date'] != "") {
			$where = "order_date	 >='" . date("Y-m-d", strtotime($filter['first_date'])) . "' AND order_date <='" . date("Y-m-d", strtotime($filter['last_date'])) . "'  AND ";
			$where_count = "order_date	 >='" . date("Y-m-d", strtotime($filter['first_date'])) . "' AND order_date <='" . date("Y-m-d", strtotime($filter['last_date'])) . "'  AND ";
		}

		if ($customer_id != "" && $type == "chain") {
			//echo "]";exit;
			$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $_REQUEST['customer_id'] . "'", 0);
			if ($type_of_executive == "1") {
				$dis_ids = array();
				$dis_data   = $this->db->rp_getData('executive', "id", "isDelete=0 AND type_of_executive =2 AND customer_flag=0 AND super_stockist_id=" . $customer_id, "", 0);
				if ($dis_data) {
					while ($dis_data_r = mysqli_fetch_assoc($dis_data)) {
						$dis_ids[] = $dis_data_r['id'];
					}
					$dis_id = implode(",", $dis_ids);
					if ($dis_id != "") {
						$where .= " customer_id IN(" . $dis_id . ")  AND";
						$where_count .= " customer_id IN(" . $dis_id . ")  AND";
					} else {
						$where .= "customer_id=0 AND ";
						$where_count .= "customer_id=0 AND ";
					}
				} else {
					$where .= "customer_id=0 AND ";
					$where_count .= "customer_id=0 AND ";
				}

				//echo $where;exit;

			} else if ($type_of_executive == "2") {

				$dealer_ids = array();
				$dealer_data   = $this->db->rp_getData('executive', "id", "isDelete=0 AND type_of_executive =3  AND customer_flag=0 AND dealer_distributor_id=" . $customer_id, "", 0);
				if ($dealer_data) {
					while ($dealer_data_r = mysqli_fetch_assoc($dealer_data)) {
						$dealer_ids[] = $dealer_data_r['id'];
					}
					$dealer_id = implode(",", $dealer_ids);
					if ($dealer_id != "") {
						$where .= " customer_id IN(" . $dealer_id . ")  AND";
						$where_count .= " customer_id IN(" . $dealer_id . ")  AND";
					} else {
						$where .= "customer_id=0 AND ";
						$where_count .= "customer_id=0 AND ";
					}
				} else {
					$where .= "customer_id=0 AND ";
					$where_count .= "customer_id=0 AND ";
				}
			} else {

				$where .= " customer_id='" . $_REQUEST['customer_id'] . "'  AND";
				$where_count .= " customer_id='" . $_REQUEST['customer_id'] . "'  AND";
			}
		}
		if ($customer_id != "" && $type == "") {
			$where .= " customer_id='" . $_REQUEST['customer_id'] . "'  AND";
			$where_count .= " customer_id='" . $_REQUEST['customer_id'] . "'  AND";
		}

		if ($filter['order_type'] == 0) {
			if ($_REQUEST['sales_id'] != "" && $_REQUEST['customer_id'] == "") {
				$where .= " sales_id ='" . $_REQUEST['sales_id'] . "' AND ";
				$where_count .= " sales_id ='" . $_REQUEST['sales_id'] . "' AND ";
			}
		}
		//}

		/*filter*/
		if ($class_id != "") {
			$where .= " class_id ='" . $_REQUEST['class_id'] . "'  AND";
			$where_count .= " class_id ='" . $_REQUEST['class_id'] . "'  AND";
		}

		if ($area_id != "") {
			$where .= " area_id ='" . $_REQUEST['area_id'] . "' AND ";
			$where_count .= " area_id ='" . $_REQUEST['area_id'] . "' AND ";
		}
		if ($type_of_company != "") {
			$where .= " type_of_company ='" . $type_of_company . "' AND ";
			$where_count .= " type_of_company ='" . $type_of_company . "' AND ";
		}
		// if($city_id!="")
		// {
		// 	$where.=" city_id ='".$_REQUEST['city_id']."' AND ";
		// 	$where_count.=" city_id ='".$_REQUEST['city_id']."' AND ";
		// }

		if ($status != "") {
			$where .= " status ='" . $_REQUEST['status'] . "' AND ";
		}

		// if($customer_id!="")
		// {
		// 	$where.=" AND customer_id ='".$_REQUEST['customer_id']."' AND isDelete=0 ";
		// }

		if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
			$where .= " order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "'  AND";
			$where_count .= " order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "'  AND";
		}

		if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
			$where .= " order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'  AND";
			$where_count .= " order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'  AND";
		}
		$where .= " isDelete=0 AND status!=-1";
		$where_count .= " isDelete=0 AND status!=-1";

		/*filter*/
		if ($filter['order_type'] == 0) {
			$data1   = $this->db->rp_getData('orders', "id,pdf_attachment,order_no,company_name
			,order_date,grand_total,grand_total_rounded,status,sales_id,type_of_company,lr_image,customer_name,client_code,customer_flag", $where, "id DESC", 0, $limit);
		} else {
			$check_id = $sales_id;
			$get_sales_type = $this->db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
			if ($get_sales_type == "sales_manager") {
				$key = "sm_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "area_sales_manager") {
				$key = "asm_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "sales_officer") {
				$key = "so_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else if ($get_sales_type == "sales_executive") {
				$key = "am_id";
				$WhereCondition .= ' ' . $key . '=' . $check_id;
			} else {
				$WhereCondition .= ' type = "service_executive"';
			}

			$data = $this->db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);
			$SALEID1 = array();
			if ($data) {
				while ($data_d = mysqli_fetch_assoc($data)) {
					$SALEID1[] = $data_d['id'];
				}
			}
			if (!empty($SALEID1)) {
				$SALEID1 = implode(",", $SALEID1);
				$ctable_where = "  AND sales_id IN (" . $SALEID1 . ")";
				$data1   = $this->db->rp_getData('orders', "*", $where . $ctable_where, "id DESC", 0, $limit);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Orders Found !!", "developer_msg" => "Not found!!", "result" => $result);
				return $ack;
				// $ctable_where .= "  AND sales_id IN (".$check_id.")";	
				// $data1   = $this->db->rp_getData('orders',"*",$where.$ctable_where,"id DESC",0,$limit);	
			}
		}
		// $data1   = $this->db->rp_getData('orders',"*",$where,"id DESC",0,$limit);
		//id,sales_id,sales_type,customer_id,customer_name,customer_type,contact_number,address,city,state,country,email,order_date,revised_date,status,total_amount,total_qty,discount,discount_type,grand_total,status

		if ($data1) {
			while ($r = mysqli_fetch_assoc($data1)) {
				$customer = $this->db->rp_getValue('sales_executive', 'isActive', "id=" . $r['sales_id'] . "", 0);
				/*if($customer==0)
				{
					continue;
				}
				else
				{
				}*/

				$r['sales_name'] = $this->db->rp_getValue("sales_executive", "name", "id='" . $r['sales_id'] . "'", 0);
				//$r['total_qty'] = $this->db->rp_getValue("order_product_item","SUM(pro_qty)","order_id='".$r['id']."' AND isDelete=0",0);
				//$r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);
				//$r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);
				//  $r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);
				$r['status'] = $r['status'];
				$r['status_slug'] = $this->order_status[intval($r['status'])];
				$r['status_slug'] = ($r['status_slug'] != "") ? $r['status_slug'] : "";
				$r['order_date'] = date('d F Y', strtotime($r['order_date']));
				$r['grand_total_rounded'] = $this->db->rp_round($r['grand_total'], 0);
				$r['grand_total'] = $this->db->rp_round($r['grand_total'], 0);
				$r['color_code'] = $this->db->status_color[$r['status_slug']];
				if ($r['lr_image'] != "") {
					$r['is_lrflag'] = "1";
				} else {
					$r['is_lrflag'] = "0";
				}

				if ($r['pdf_attachment'] != "") {
					$r['pdf_attachment'] = ADMINSITEURL . 'order_documents/' . $r['pdf_attachment'];
					$r['is_pdfflag'] = 1;
				} else {
					$r['pdf_attachment'] = "";
					$r['is_pdfflag'] = 0;
				}
				// $r['revised_date']=date('d-m-Y',strtotime($r['revised_date']));

				$result[] = $r;
			}
			/*Get Order Count*/
			//echo "hello";exit;

			$orderdata = array();
			if ($filter['order_type'] == 0) {
				$OrderData = $this->db->rp_getData('orders', "DISTINCT(status)", $where_count, "", 0, $limit);
			} else {
				$check_id_count = $_REQUEST['sales_id'];
				$get_sales_type_count = $this->db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id_count . "'", 0);
				if ($get_sales_type_count == "sales_manager") {
					$keyCount = "sm_id";
					$WhereConditionCount .= ' ' . $keyCount . '=' . $check_id_count;
				} else if ($get_sales_type_count == "area_sales_manager") {
					$keyCount = "asm_id";
					$WhereConditionCount .= ' ' . $keyCount . '=' . $check_id_count;
				} else if ($get_sales_type_count == "sales_officer") {
					$keyCount = "so_id";
					$WhereConditionCount .= ' ' . $keyCount . '=' . $check_id_count;
				} else if ($get_sales_type_count == "sales_executive") {
					$keyCount = "am_id";
					$WhereConditionCount .= ' ' . $keyCount . '=' . $check_id_count;
				} else {
					$WhereConditionCount .= ' type = "service_executive"';
				}

				$dataCount = $this->db->rp_getData("sales_executive", "id", $WhereConditionCount, "", 0);
				$SALEID1Count = array();
				if ($dataCount) {
					while ($dataCount_d = mysqli_fetch_assoc($dataCount)) {
						$sale_arr[] = $dataCount_d['id'];
					}

					if ($sale_arr) {
						// echo "string";exit;
						$sales_ids = implode(",", $sale_arr);
						$ctable_where_count = " sales_id IN (" . $sales_ids . ")";
						// $OrderData   = $this->db->rp_getData('orders',"*",$where_count.$ctable_where_count,"",0,$limit);
						$OrderData = $this->db->rp_getData('orders', "DISTINCT(status)", $ctable_where_count . " AND isDelete=0 AND status!='-1'", "", 0, $limit);
					}
				}
			}
			//$OrderData = $this->db->rp_getData('orders',"status",$where,"id DESC",0,$limit);
			/*$status_array = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Canceled","4"=>"Partially Dispatched");
			$status_key_array = array("-2","0","1","2","3","4");*/
			$status_array = array("-2" => "Disapproved", "0" => "Waiting For Approval", "1" => "Waiting For Account Approval", "3" => "Cancelled", "4" => "Account Approved", "5" => "Dispatch", "6" => "Order Complate");
			$status_key_array = array("-2", "0", "1", "3", "4", "5", "6");

			while ($Order_d = mysqli_fetch_assoc($OrderData)) {
				// print_r($Order_d);exit();
				if ($_REQUEST['sales_id'] != "") {
					if ($ctable_where_count) {
						$Order_d['count'] = $this->db->rp_getTotalRecord("orders", $ctable_where_count . " AND status='" . $Order_d['status'] . "' AND isDelete=0", 0);
					} else {
						$Order_d['count'] = $this->db->rp_getTotalRecord("orders", $where_count . " AND status='" . $Order_d['status'] . "' AND isDelete=0", 0);
					}
				} else {
					$Order_d['count'] = $this->db->rp_getTotalRecord("orders", $where_count . " AND status='" . $Order_d['status'] . "' AND isDelete=0", 0);
				}

				if (($key = array_search($Order_d['status'], $status_key_array)) !== false) {
					unset($status_key_array[$key]);
				}

				$Order_d['status_slug'] = $status_array[$Order_d['status']];

				$Order_d['status'] = $Order_d['status'];
				// echo "<pre>"; print_r($Order_d);
				$Order_d['color_code'] = $this->db->status_color[$Order_d['status_slug']];

				if ($Order_d['color_code'] == "") {
					$Order_d['color_code'] = "";
				}

				if ($Order_d['status_slug'] == "") {
					$Order_d['status_slug'] = "";
				}
				$orderdata[] = $Order_d;
			}
			foreach ($status_key_array as $key => $remainval) {
				$Order_d['count'] = 0;
				$Order_d['status'] = $remainval;
				$Order_d['status_slug'] = $status_array[$remainval];
				$Order_d['color_code'] = $this->db->status_color[$status_array[$remainval]];
				$orderdata[] = $Order_d;
			}

			$order = $orderdata;
			/*Get Order Count*/

			if (!empty($result)) {
				$result = $this->db->toUpperCaseAssocArray($result);
				$order = $this->db->toUpperCaseAssocArray($order);
				$ack = array("ack" => 1, "ack_msg" => "Successfully Get Orders !!", "developer_msg" => "You got it!!", "result" => $result, "order_count" => $order);
				return $ack;
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Orders Found !!", "developer_msg" => "Not found!!", "result" => $result);
				return $ack;
			}
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Orders Found  !!", "developer_msg" => "Not found !!", "result" => $result,);
			return $ack;
		}
	}


	function getOrderDetail($detail, $limit = "")
	{
		extract($detail);
		$result = array();

		if ($from_date != "" && $to_date != "") {
			$where = "customer_id ='" . $_REQUEST['customer_id'] . "' AND customer_type='" . $_REQUEST['customer_type'] . "' AND DATE(created_date)>= '" . date_format(date_create($_REQUEST['from_date']), "Y-m-d") . "' And DATE(created_date)<='" . date_format(date_create($_REQUEST['to_date']), "Y-m-d") . "'";
		} else {
			$where = "customer_id ='" . $_REQUEST['customer_id'] . "' AND customer_type='" . $_REQUEST['customer_type'] . "'";
		}

		$data    = $this->db->rp_getData('orders', "id,customer_id,customer_name,company_name,customer_type,contact_number,address,city,state,country,email,order_date,revised_date,status,total_amount,total_qty,discount,discount_type,grand_total,status,grand_total_rounded,proforma_invoice_id,order_no", $where, "id DESC", 0, $limit);
		if ($data) {
			while ($r = mysqli_fetch_assoc($data)) {

				$r['pro_forma_invoice_no'] = $this->db->rp_getValue("proforma_invoice_info", "invoice_no", "id='" . $r['proforma_invoice_id'] . "'", 0);
				$request_id = $this->db->rp_getValue("proforma_invoice_info", "request_id", "id='" . $r['proforma_invoice_id'] . "'", 0);
				$r['request_no'] = $this->db->rp_getValue("customer_order_request_info", "request_no", "id='" . $request_id . "'", 0);
				$r['customer_name'] = $this->db->rp_getValue("executive", "cname", "id='" . $r['customer_id'] . "'", 0);
				$r['country'] = $this->db->rp_getValue("country", "name", "id='" . $r['country'] . "'", 0);
				$r['state'] = $this->db->rp_getValue("state", "name", "id='" . $r['state'] . "'", 0);
				$r['city'] = $this->db->rp_getValue("city", "name", "id='" . $r['city'] . "'", 0);
				$r['status'] = $r['status'];
				$r['status_slug'] = $this->order_status[intval($r['status'])];
				$r['status_slug'] = ($r['status_slug'] != "") ? $r['status_slug'] : "";
				$r['order_date'] = date('d-m-Y', strtotime($r['order_date']));
				// $r['revised_date']=date('d-m-Y',strtotime($r['revised_date']));
				$result[] = $r;
			}
			if (!empty($result)) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Successfully Get Orders of Customer!!",
					"developer_msg" => "You got it!!",
					"result" => $result,
				);
				return $ack;
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No Orders Found !!",
					"developer_msg" => "Not found!!",
					"result" => $result,
				);
				return $ack;
			}
		} else {
			$ack = array(
				"ack" => 0,
				"ack_msg" => "No Orders Found !!",
				"developer_msg" => "Not found!!",
				"result" => $result,
			);
			return $ack;
		}
	}
	function getOrderItemDetail($order_id)
	{
		//get order item
		$result = array();
		$order_pro_detail = $this->db->rp_getData("orders", "*", "id='" . $order_id . "'", "id DESC", 0); //id,order_no,dealer_id,class_id,area_id,sales_id,sales_type,customer_id,customer_name,customer_type,remarks,contact_number,address,country,state,city,email,order_date,total_amount,total_qty,discount,discount_type,grand_total,created_date,status,adate,isDelete,isActive
		if ($order_pro_detail) {
			$order_pro_detail = mysqli_fetch_assoc($order_pro_detail);
			$order_pro_detail['order_date'] = date('Y-m-d', strtotime($order_pro_detail['order_date']));
			$order_pro_detail['country'] = $this->db->rp_getValue("country", "name", "id='" . $order_pro_detail['country'] . "'");
			$order_pro_detail['state'] = $this->db->rp_getValue("state", "name", "id='" . $order_pro_detail['state'] . "'");
			$order_pro_detail['city'] = $this->db->rp_getValue("city", "name", "id='" . $order_pro_detail['city'] . "'");
			$order_pro_detail['status'] = $order_pro_detail['status'];
			$order_pro_detail['status_slug'] = $this->order_status[intval($order_pro_detail['status'])];

			$where = "order_id='" . $order_pro_detail['id'] . "'";
			$dt = $this->db->rp_getData("order_product_item", "*", $where, "", 0);
			$r = array();
			if ($dt) {
				while ($r = mysqli_fetch_assoc($dt)) {
					$r['adate'] = date('d-m-Y', strtotime($r['adate']));
					$result[] = $r;
				}
			}
			$order_pro_detail['products'] = $result;
			//print_r($order_pro_detail);
			if (!empty($order_pro_detail)) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Successfully Get Orders !!",
					"developer_msg" => "You got it!!",
					"result" => $order_pro_detail,
				);
				return $ack;
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No Orders Found !!",
					"developer_msg" => "No Customer found!!",
					"result" => $result,
				);
				return $ack;
			}
		} else {
			$ack = array(
				"ack" => 0,
				"ack_msg" => "No Orders Found !!",
				"developer_msg" => "No Customer found!!",
				"result" => $result,
			);
			return $ack;
		}
	}

	function getNoOrderInquiry($sales_id, $filter)
	{
		extract($filter);
		require_once("class.system.php");
		$system = new System();
		$limit = $system->getLimit();
		$mobile_number = $_REQUEST['mobile_no'];
		$company_name = $_REQUEST['company_name'];
		$person_name = $_REQUEST['person_name'];
		$status = $_REQUEST['status'];
		$assign_to = $_REQUEST['assign_to'];
		$created_by = $_REQUEST['created_by'];
		$type = $_REQUEST['type'];
		$customer_id = $_REQUEST['customer_id'];
		$type_of_company = $filter['type_of_company'];
		$country_name = $_REQUEST['country_name'];
		$state_name = $_REQUEST['state_name'];
		$city_name = $_REQUEST['city_name'];
		$route_name = $_REQUEST['route_name'];
		$searchName = $_REQUEST['searchName'];
		//$purchasing_from = $_REQUEST['purchasing_from'];

		/*$status_type=array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");*/

		//	$status_type=array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","11"=>"Lost");

		// *** old array hato ***
		// $status_type=array("0"=>"Generate","1"=>"In Followup","2"=>"Position","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"My Work","-2"=>"Cancel","11"=>"Lost"); 


		$status_type = array("0" => "Generate", "1" => "In Followup", "2" => "Positive", "3" => "Buy Later", "4" => "Hot", "5" => "Cold", "6" => "Warm", "-1" => "My Work", "-2" => "Cancel", "11" => "Lost");

		// Array
		// (
		//     [0] => Generate
		//     [2] => Positive
		//     [1] => In Followup
		//     [4] => Hot
		//     [5] => Cold
		//     [6] => Warm
		//     [-2] => Cancel
		//     [-1] => My Work
		//     [3] => Buy Later
		//     [11] => Lost
		// )

		$result = array();
		$where = "isDelete=0";
		$where_count = "isDelete=0";
		if (array_key_exists("first_date", $filter) && $filter['first_date'] != "" && array_key_exists("last_date", $filter) && $filter['last_date'] != "") {
			$where .= " AND datetime	 >='" . date("Y-m-d", strtotime($filter['first_date'])) . "' AND datetime <='" . date("Y-m-d", strtotime($filter['last_date'])) . "' AND ";
			$where_count .= " AND datetime	 >='" . date("Y-m-d", strtotime($filter['first_date'])) . "' AND datetime <='" . date("Y-m-d", strtotime($filter['last_date'])) . "' AND ";
		}

		if ($mobile_number != "") {
			$where .= " AND mobile_number LIKE '%" . $mobile_number . "%' ";
			$where_count .= " AND mobile_number LIKE '%" . $mobile_number . "%' ";
		}

		if ($customer_id != "") {
			$where .= " AND dealer_id = '" . $customer_id . "'";
			$where_count .= " AND dealer_id = '" . $customer_id . "'";
		}

		if ($company_name != "") {
			$where .= " AND company_name LIKE '%" . $company_name . "%' ";
			$where_count .= " AND company_name LIKE '%" . $company_name . "%' ";
		}

		if ($person_name != "") {
			$where .= " AND person_name LIKE '%" . $person_name . "%' ";
			$where_count .= " AND person_name LIKE '%" . $person_name . "%' ";
		}

		if ($searchName != "") {
			$where .= " AND (
	        company_name like '%" . $this->db->clean($_REQUEST['searchName']) . "%' OR
	        mobile_number like '%" . $this->db->clean($_REQUEST['searchName']) . "%' OR
	        person_name like '%" . $this->db->clean($_REQUEST['searchName']) . "%'
		    )  ";
			$where_count .= " AND (
	        company_name like '%" . $this->db->clean($_REQUEST['searchName']) . "%' OR
	        mobile_number like '%" . $this->db->clean($_REQUEST['searchName']) . "%' OR
	        person_name like '%" . $this->db->clean($_REQUEST['searchName']) . "%'
		    )  ";
		}

		if ($status != "") {
			$where .= " AND status = '" . $status . "'";
		}

		if ($assign_to != "") {
			$where .= " AND inquiry_assign_to = '" . $assign_to . "'";
			$where_count .= " AND inquiry_assign_to = '" . $assign_to . "'";
		}

		if ($created_by != "") {
			$where .= "  AND inquiry_created_by = '" . $created_by . "'";
			$where_count .= "  AND inquiry_created_by = '" . $created_by . "'";
		}
		if ($country_name != "") {
			$where .= " AND country LIKE '%" . $country_name . "%' ";
			$where_count .= " AND country LIKE '%" . $country_name . "%' ";
		}
		if ($state_name != "") {
			$where .= " AND state LIKE '%" . $state_name . "%' ";
			$where_count .= " AND state LIKE '%" . $state_name . "%' ";
		}
		if ($city_name != "") {
			$where .= " AND main_city LIKE '%" . $city_name . "%' ";
			$where_count .= " AND main_city LIKE '%" . $city_name . "%' ";
		}
		if ($route_name != "") {
			$where .= " AND city LIKE '%" . $route_name . "%' ";
			$where_count .= " AND city LIKE '%" . $route_name . "%' ";
		}
		if ($pincode_no != "") {
			$where .= " AND pincode LIKE '%" . $pincode_no . "%' ";
			$where_count .= " AND pincode LIKE '%" . $pincode_no . "%' ";
		}

		if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
			$where .= " AND datetime <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
			$where_count .= " AND datetime <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
		}

		if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
			$where .= " AND datetime >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
			$where_count .= " AND datetime >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
		}
		if (isset($_REQUEST['id']) && $_REQUEST['id'] != "" && $_REQUEST['id'] != NULL) {
			$where .= " AND id = '" . $_REQUEST['id'] . "' ";
			$where_count .= " AND id = '" . $_REQUEST['id'] . "' ";
		}

		if ($sales_id != "") {
			/*if ($type == "-1") {
				$where.= " AND inquiry_assign_to = '".$sales_id."'";
				$where_count.= " AND inquiry_assign_to = '".$sales_id."'";
			} else {
				$where.= " AND inquiry_assign_to = '".$sales_id."'";
				$where_count.= " AND inquiry_assign_to = '".$sales_id."'";
			}*/
			$where .= "  AND (inquiry_assign_to = '" . $sales_id . "' OR inquiry_created_by = '" . $sales_id . "')";
			$where_count .= "  AND (inquiry_assign_to = '" . $sales_id . "' OR inquiry_created_by = '" . $sales_id . "')";
		}
		if ($type != "") {
			$where .= "  AND inquiry_type = '" . $type . "'";
			$where_count .= "  AND inquiry_type = '" . $type . "'";
			// if($type=="-1")
			// {
			//     //$ctable_where .= " isDelete=0 AND isActive=1 AND (inquiry_lead_flag = '-1' OR inquiry_lead_flag='0')";
			//     $where .= " AND (inquiry_lead_flag = '-1' OR inq_status=2)";
			// }
			// else if($type=="0")
			// {
			//     $where .= " AND (inquiry_lead_flag = '0' OR inquiry_lead_flag = '1')";
			// }
			// else
			// {
			//     $where .= " AND inquiry_lead_flag = '1'";
			// }
		}
		if ($type_of_company != "") {
			$where .= "  AND type_of_company = '" . $type_of_company . "'";
			$where_count .= "  AND type_of_company = '" . $type_of_company . "'";
		}

		$data  = $this->db->rp_getData('no_order_inquiry', "id,local_id,sales_executive_id,company_name,person_name,mobile_number,contact_person,country,
			state,city,description,action,inquiry_date,datetime,isDelete,isActive,created_date,status,image_path,latitude,longitude,address,class_id,area_id,executive_type,other_mobile_no,distributor_id,source_of_inquiry,designation,zone,product_id,quantity,u_w_flag,u_w_remark,quotation_flag,quotation_remark,customer_requirement,birth_date,inquiry_created_by,inquiry_assign_to,inquiry_assign_date,dealer_id,email_address,inquiry_lead_flag,lead_date,date_of_call,gst_no,shipping_address,billing_address,industry_type_id,main_city,top_category_id,type_of_company,purchasing_from,pincode", $where, "id DESC", 0, $limit);
		if ($data) {
			while ($r = mysqli_fetch_assoc($data)) {
				/*$inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");*/

				$u_w_array = array("1" => "Yes", "2" => "No");
				$quotation_array = array("1" => "Yes", "2" => "No");
				$r['source_of_inquiry_slug'] = $this->db->rp_getValue("source_of_inquiry", "name", "id='" . $r['source_of_inquiry'] . "' AND isDelete=0", 0);
				$r['type_of_inquiry'] = $this->db->rp_getValue("customer_type", "name", "id='" . $r['executive_type'] . "' AND isDelete=0", 0);
				//$r['source_of_inquiry_slug']	=$inquiry_type_array[$r['source_of_inquiry']];

				$r['u_w_flag_name']	= $u_w_array[$r['u_w_flag']];

				$r['quotation_flag_name']	= $u_w_array[$r['quotation_flag']];

				$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $r['product_id'] . "' AND isDelete=0", 0);

				$r['prouct_name']	= $this->db->rp_getValue("product", "name", "id='" . $r['product_id'] . "'", 0) . "-" . $weight_name;

				$r['status_slug']	= $status_type[$r['status']];
				$r['is_inquiry']	= $r['inquiry_lead_flag'];
				$r['address']	= htmlentities($r['address']);
				$r['inq_no'] = "#INQ/" . $r['id'];
				$r['color_code'] = $this->db->inquiry_status_color[$r['status_slug']];


				$r['sales_name'] = $this->db->rp_getValue("sales_executive", "name", "id='" . $r['sales_id'] . "' AND type='" . $r['sales_type'] . "'", 0);

				/*$r['country_slug'] = $r['country'];
				$r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);*/

				$r['executive_type_slug'] = $this->db->rp_getValue("customer_type", "name", "id='" . $r['executive_type'] . "'", 0);

				$r['dealer_name']	= $this->db->rp_getValue("executive", "company_name", "id='" . $r['dealer_id'] . "'", 0);

				/*$r['state_slug'] = $r['state'];		
				$r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);*/

				/*$r['city_slug'] =  $r['city'];
				$r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);*/

				$r['action_slug'] =  $r['action'];
				$r['action'] = $this->db->rp_getValue("no_order_inquiry_action", "name", "id='" . $r['action'] . "'", 0);
				$r['whatsapp_no'] = "91" . $r['mobile_number'] . "";
				$img = explode(",", $r['image_path']);
				$imgpath = array();
				$imgString = "";
				for ($i = 0; $i < sizeof($img); $i++) {
					$imgpath[] = SITEURL . "resource/image/" . $this->db->rp_getValue("media", "url", "reference_id='" . $r['id'] . "' AND id='" . $img[$i] . "'");
				}
				// print_r($imgpath);exit;
				$r['image_path'] = ($r['image_path'] != "") ? implode(",", $imgpath) : "";

				$r['created_date'] = date('d F Y h:i A', strtotime($r['created_date']));
				if ($r['datetime'] != "1970-01-01" && $r['datetime'] != "0000-00-00") {
					$r['datetime'] = date('d-m-Y h:i A', strtotime($r['datetime']));
				} else {
					$r['datetime'] = "";
				}

				if ($r['inquiry_date'] != "1970-01-01" && $r['inquiry_date'] != "0000-00-00") {
					$r['inquiry_date'] = date('d F Y', strtotime($r['inquiry_date']));
				} else {
					$r['inquiry_date'] = "";
				}

				if ($r['lead_date'] != "1970-01-01" && $r['lead_date'] != "0000-00-00") {
					$r['lead_date'] = date('d F Y', strtotime($r['lead_date']));
				} else {
					$r['lead_date'] = "";
				}

				if ($r['birth_date'] != "1970-01-01") {
					$r['birth_date'] = date('d F Y', strtotime($r['birth_date']));
				} else {
					$r['birth_date'] = "";
				}
				if ($r['date_of_call'] != "1970-01-01") {
					$r['date_of_call'] = date('d F Y', strtotime($r['date_of_call']));
				} else {
					$r['date_of_call'] = "";
				}

				if ($r['inquiry_assign_date'] != "1970-01-01" && $r['inquiry_assign_date'] != "0000-00-00") {
					$r['inquiry_assign_date'] = date('d F Y', strtotime($r['inquiry_assign_date']));
				} else {
					$r['inquiry_assign_date'] = "";
				}

				$r['inquiry_created_by_name'] = $this->db->rp_getValue("sales_executive", "name", "id='" . $r['inquiry_created_by'] . "'");
				$r['inquiry_assign_to_name'] = $this->db->rp_getValue("sales_executive", "name", "id='" . $r['inquiry_assign_to'] . "'");

				$r['country_id'] = $this->db->rp_getValue("country", "id", "name='" . $r['country'] . "' AND isDelete=0");
				$r['state_id'] = $this->db->rp_getValue("class", "id", "name='" . $r['state'] . "' AND isDelete=0");
				$r['city_id'] = $this->db->rp_getValue("area", "id", "name='" . $r['city'] . "' AND isDelete=0");
				$r['main_city_id'] = $this->db->rp_getValue("city", "id", "name='" . $r['main_city'] . "' AND isDelete=0");


				if ($r['top_category_id'] != "") {
					$tc_ids = explode(',', $r['top_category_id']);
					//echo "dssd".sizeof($cat_ids);exit;
					$tc_name = array();
					for ($l = 0; $l < sizeof($tc_ids); $l++) {


						$top_cat_name = $this->db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $tc_ids[$l] . "'");


						$tc_name[] = $top_cat_name;
					}
					//print_r($cat_name);exit;
					$r['top_category_name'] = implode(',', $tc_name);
				} else {
					$r['top_category_name'] = "";
				}

				// $result[] = $r; 
				$rx[] = $r;
			}
			$cntw = 0;
			for ($i = 0; $i < sizeof($rx); $i++) {
				foreach ($rx[$i] as $key => $value) {
					$result[$i][$key] = htmlentities($value);
				}
			}
			// print_r($result);exit;

			/*Get Inquiry Status*/
			$inquiry_status = array();
			$InquiryData = $this->db->rp_getData('no_order_inquiry', "DISTINCT(status)", "(inquiry_assign_to = '" . $sales_id . "' OR inquiry_created_by = '" . $sales_id . "') AND inquiry_type = '" . $type . "' AND isDelete=0", "", 0);

			//$inquiry_status_array=array("0"=>"Generate","1"=>"In Followup"/*,"2"=>"Interested"*/,"-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm"/*,"7"=>"Wrong Call"*/,"8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");

			// *** old array hato ***
			// $inquiry_status_array=array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","11"=>"Lost");


			$inquiry_status_array = array("0" => "Generate", "1" => "In Followup", "2" => "Positive", "3" => "Buy Later", "4" => "Hot", "5" => "Cold", "6" => "Warm", "-1" => "My Work", "-2" => "Cancel", "11" => "Lost");

			//$inquiry_status_key = array("0","1"/*,"2"*/,"-1","3","-2","4","5","6"/*,"7"*/,"8","9","10","11");
			$inquiry_status_key = array("0", "1", "2", "4", "5", "6", "-1", "3", "-2", "11");
			while ($Inquiry_d = mysqli_fetch_assoc($InquiryData)) {
				if ($_REQUEST['sales_id'] != "") {
					$Inquiry_d['count'] = $this->db->rp_getTotalRecord("no_order_inquiry", $where_count . " AND status='" . $Inquiry_d['status'] . "' AND isDelete=0", 0);
				} else {
					$Inquiry_d['count'] = $this->db->rp_getTotalRecord("no_order_inquiry", $where_count . " AND status='" . $Inquiry_d['status'] . "' AND isDelete=0", 0);
				}


				if (($key_inquiry = array_search($Inquiry_d['status'], $inquiry_status_key)) !== false) {
					unset($inquiry_status_key[$key_inquiry]);
				}

				$Inquiry_d['status_slug'] = $inquiry_status_array[$Inquiry_d['status']];
				$Inquiry_d['status'] = $Inquiry_d['status'];

				$Inquiry_d['color_code'] = $this->db->inquiry_status_color[$Inquiry_d['status_slug']];

				if ($Inquiry_d['color_code'] == "") {
					$Inquiry_d['color_code'] = "";
				}

				if ($Inquiry_d['status_slug'] == "") {
					$Inquiry_d['status_slug'] = "";
				}
				$inquiry_status[] = $Inquiry_d;
			}
			foreach ($inquiry_status_key as $key => $remainval_inquiry) {
				$Inquiry_d['count'] = 0;
				$Inquiry_d['status'] = $remainval_inquiry;
				$Inquiry_d['status_slug'] = $inquiry_status_array[$remainval_inquiry];
				$Inquiry_d['color_code'] = $this->db->inquiry_status_color[$inquiry_status_array[$remainval_inquiry]];
				$inquiry_status[] = $Inquiry_d;
			}
			/*Get Inquiry Status*/

			if (!empty($result)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Get Inquiry !!", "developer_msg" => "You got it!!", "result" => $result, "inquiry_status" => $inquiry_status);
				return $ack;
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Found !!", "developer_msg" => "Not found!!", "result" => $result);
				return $ack;
			}
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Data Found !!", "developer_msg" => "Not found!!", "result" => $result);
			return $ack;
		}
	}

	//-----------------------------------------------------------------------------//
	//24-04-2017-----------------#use for get order Product Item upon order service#---------------------------------------------------------//	
	function getOrders_forItem($order_id)
	{
		//get order item
		$result = array();

		$order_pro_detail = mysqli_fetch_assoc($this->db->rp_getData("orders", "*", "id='" . $order_id . "'", "id DESC", 0));

		$order_pro_detail['gst_no'] = $order_pro_detail['gst'];
		$order_pro_detail['quotation_no'] = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $order_pro_detail['quotation_id'] . "'") . "";
		if ($order_pro_detail['pdf_attachment'] != "" && isset($order_pro_detail['pdf_attachment'])) {
			$order_pro_detail['pdf_attachment'] = ADMINSITEURL . 'order_documents/' . $order_pro_detail['pdf_attachment'];
			$order_pro_detail['is_pdfflag'] = '1';
		} else {
			$order_pro_detail['pdf_attachment'] = "";
			$order_pro_detail['is_pdfflag'] = '0';
		}

		$order_pro_detail['quotation_date'] = $this->db->rp_getValue("quotation_detail", "quotation_date", "id='" . $order_pro_detail['quotation_id'] . "'") . "";
		$order_pro_detail['quotation_date'] = date('d-m-Y', strtotime($order_pro_detail['quotation_date']));
		if ($order_pro_detail['quotation_date'] == "01-01-1970" || $order_pro_detail['quotation_date'] == "00-00-0000") {

			$order_pro_detail['quotation_date'] = "";
		} else {

			$order_pro_detail['quotation_date'] = date('d F Y', strtotime($order_pro_detail['quotation_date']));
		}

		$order_pro_detail['po_date'] = date('d-m-Y', strtotime($order_pro_detail['po_date']));
		if ($order_pro_detail['po_date'] == "01-01-1970" || $order_pro_detail['po_date'] == "00-00-0000") {

			$order_pro_detail['po_date'] = "";
		} else {
			$order_pro_detail['po_date'] = date('d F Y', strtotime($order_pro_detail['po_date']));
		}
		$order_pro_detail['order_date'] = date('d F Y', strtotime($order_pro_detail['order_date']));
		$order_pro_detail['transport_through_name'] = $this->db->rp_getValue("transport_by", "name", "id='" . $order_pro_detail['transport_through'] . "'", 0);

		$order_pro_detail['transport_by_name'] = $this->db->rp_getValue("transport_master", "name", "id='" . $order_pro_detail['transport_name'] . "'", 0);
		if ($order_pro_detail['lr_image'] != "") {
			$order_pro_detail['is_lrflag'] = "1";
		} else {
			$order_pro_detail['is_lrflag'] = "0";
		}

		$where = "order_id='" . $order_pro_detail['id'] . "'";
		$total_qty = $this->db->rp_getValue('order_product_item', "SUM(pro_qty)", "order_id='" . $order_pro_detail['id'] . "' AND  isDelete=0");

		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $order_pro_detail['customer_id'] . "'", 0);
		$is_premium = $this->db->rp_getValue("price_list", "is_premium", "id='" . $price_list_id . "'", 0);
		$dt = $this->db->rp_getData("order_product_item", "*", $where);
		$r = array();
		if ($dt) {
			$subtotal = 0;
			$taxable_amount = 0;
			$grandtotal = 0;
			$order_unit_arr = array("-1" => "Box", "-2" => "Strip", "-3" => "Pallet", "1" => "Caret", "2" => "Big Box", "100" => "Nos");

			while ($r = mysqli_fetch_assoc($dt)) {

				//$unit_id = $this->db->rp_getValue("product", "unit_id", "id='".$r['pro_id']."'","",0);

				//$r['unit_name'] = $this->db->rp_getValue("unit", "name", "id='" . $unit_id . "'",0);

				$r['stock'] = $this->db->rp_getValue("product_weight_price", "stock_qty", "weight_id='" . $r['weight_id'] . "' AND product_id='" . $r['pro_id'] . "'  AND isDelete=0");


				$r['hsn_code'] = $this->db->rp_getValue("product", "hsn_code", "id='" . $r['pro_id'] . "'", "", 0);
				if ($r['discount'] == 0) {
				} else {
					$r['discount_amount'] = "0";
				}
				$subtotal += $r['totalprice'];
				$item_gst_total += $r['item_gst_amount'];
				$GST = $this->db->rp_getValue("product", "igst", "id='" . $r['pro_id'] . "'");
				$order_pro_detail['status'] = $order_pro_detail['status'];
				$order_pro_detail['status_slug'] = $this->order_status[intval($order_pro_detail['status'])];
				$order_pro_detail['color_code'] = $this->db->status_color[$order_pro_detail['status_slug']];
				$r['adate'] = date('d-m-Y', strtotime($r['adate']));

				$cash_discount += $r['cash_discount'];
				$cash_discount_amount += $r['cash_discount_amount'];
				$additional_discount += $r['additional_discount'];
				$additional_discount_amount += $r['additional_discount_amount'];
				$after_packing_charge += $r['other_charge'];
				$after_transport_charge += $r['fright_charge'];


				$taxable_amount = $r['totalprice'] - $r['cash_discount_amount'] - $r['additional_discount_amount'] + $r['other_charge'] + $r['fright_charge'];
				$total_taxable_amount += $taxable_amount;
				$gst_amount = ($taxable_amount * $r['igst_tax']) / 100;
				$total_gst_amount += $gst_amount;
				//echo $order_items_d['igst_tax'];exit;
				$final_total = $r['totalprice'] + $gst_amount;
				$main_total += $final_total;
				if ($r['item_order_unit'] < 0 && $r['item_order_unit'] != 100) {
					$item_order_unit_qty = $r['box_qty'];
				} else if ($r['item_order_unit'] > 0 && $r['item_order_unit'] != 100) {
					$item_order_unit_qty = $r['cartoon_qty'];
				} else if ($r['item_order_unit'] == 100) {
					$item_order_unit_qty = $r['pro_qty'];
				} else {
					$item_order_unit_qty = 0;
				}

				$r['minimum_selling_price'] = 0; // Min sell removed — app uses MRP only

				$r['item_order_unit_qty'] = $item_order_unit_qty;
				$r['unit_name'] =	$order_unit_arr[$r['item_order_unit']];
				$r['order_item_brand_name'] = $this->db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $r['order_item_brand_id'] . "'");
				$r['is_premium'] = ($is_premium) ? $is_premium : "0";
				// $r['is_premium']=$r['is_premium'];

				$result[] = $r;
			}
		}

		if ($order_pro_detail) {
			$after_additional_discounted = 0;
			$after_cash_discounted = 0;

			$cash_discount = $order_pro_detail['cash_discount'];
			$cash_discount_amount = $order_pro_detail['cash_discount_amount'];
			$after_cash_discounted = $this->db->rp_num($subtotal - $cash_discount_amount);

			$additional_discount = $order_pro_detail['additional_discount'];
			$additional_discount_amount =  $order_pro_detail['additional_discount_amount'];
			$after_additional_discounted = $after_cash_discounted - $additional_discount_amount;

			//$final_total = $after_additional_discounted;

			$sub_total = $this->db->rp_num((float)$sub_total, 2, '.', '');

			//$final_total = $final_total+$order_d['packing_charge']+$order_d['transport_charge'];
			$after_packing_charge = $final_total + $order_pro_detail['packing_charge'];
			$after_transport_charge = $after_packing_charge + $order_pro_detail['transport_charge'];

			$final_total = $total_taxable_amount;
			//	$gst_amount=$order_pro_detail['igst_amount'];
			//$tcs_amount=$order_pro_detail['tcs_amount'];
			//echo $order_pro_detail['igst_amount'];exit;
			if ($order_pro_detail['igst_amount'] == 0) {
				$total_gst_amount = 0;
			}
			$after_gst = $this->db->rp_num($final_total + $total_gst_amount);
			$grandtotal = $this->db->rp_num($final_total + $total_gst_amount + $tcs_amount);
			$before_roundoff = $this->db->rp_num($grandtotal, 2);
			$whole = floor($before_roundoff);
			$fraction = $before_roundoff - $whole;
			$f1 =  $this->db->rp_num((float)$fraction, 2, '.', '');
			$roundoff = $this->db->rp_num($f1, 2);

			//$grand_total=strval($this->db->rp_num($order_pro_detail['grand_total_rounded'],2));
			$grand_total = $this->db->rp_num(round($grandtotal), 2);
			if ($order_pro_detail['gst_amount'] != "" && $order_pro_detail['gst_amount'] != 0) {
				$customer_state = $this->db->rp_getValue("executive", "state", "id='" . $order_pro_detail['customer_id'] . "'", 0);
				if (strtolower(CLIENT_STATE) == strtolower($customer_state)) {
					/*	if($order_pro_detail['customer_type']==7){
						$GST = "(CGST:0.05%,SGST:0.05%)";
					}else{*/
					$GST = "(CGST:9%,SGST:9%)";
					//}

				} else {
					/*if($order_pro_detail['customer_type']==7){
						$GST = "(IGST:0.01%)";
					}else{*/
					$GST = "(IGST:18%)";
					//} 
				}
			} else {
				$GST = "";
			}

			$order_pro_detail['gst'] = $GST;
			//$order_pro_detail['gst']="";
			$order_pro_detail['gst_amount'] = $this->db->rp_num($total_gst_amount, 2);
			$order_pro_detail['after_additional_discounted'] = $this->db->rp_num($after_additional_discounted, 2);
			$order_pro_detail['after_cash_discounted'] = $this->db->rp_num($after_cash_discounted, 3);
			//$order_pro_detail['after_transport_charge']=$this->db->rp_num($after_transport_charge,2);
			//$order_pro_detail['after_packing_charge']=$this->db->rp_num($after_packing_charge,2);

			$order_pro_detail['additional_discount_amount'] = $this->db->rp_num($order_pro_detail['additional_discount_amount'], 2);
			$order_pro_detail['cash_discount_amount'] = $this->db->rp_num($order_pro_detail['cash_discount_amount'], 2);
			$order_pro_detail['after_transport_charge'] = $this->db->rp_num($order_pro_detail['transport_charge'], 2);
			$order_pro_detail['after_packing_charge'] = $this->db->rp_num($order_pro_detail['packing_charge'], 2);
			$order_pro_detail['tcs_amount'] = $this->db->rp_num($order_pro_detail['tcs_amount'], 2);

			$order_pro_detail['final_total'] = $this->db->rp_num($final_total, 2);
			$order_pro_detail['subtotal'] = $this->db->rp_num($subtotal, 2);
			$order_pro_detail['roundoff'] = $roundoff;
			$order_pro_detail['grand_total'] = $grand_total;
			$order_pro_detail['total_qty'] = $total_qty;

			$order_pro_detail['average_amount'] = $this->db->rp_num($subtotal / $total_qty, 3);
			$order_pro_detail['company_type'] = $this->db->rp_getValue("company_master", "name", "id='" . $this->db->rp_getValue("executive", "type_of_company", "id='" . $order_pro_detail['customer_id'] . "' AND isDelete=0", 0) . "' AND isDelete=0", 0);
			// $order_pro_detail['company_type']=$this->db->rp_getValue("company_master","name","id='".$db->rp_getValue("executive","company_type","id='".$order_pro_detail['customer_id']."' AND isDelete=0",0)."' AND isDelete=0",0);

			$order_pro_detail['products'] = $result;

			if (!empty($result)) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Successfully Get Orders !!",
					"developer_msg" => "You got it!!",
					"result" => $order_pro_detail,
				);
				return $ack;
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No Orders Found !!",
					"developer_msg" => "No Customer found!!",
					"result" => $result,
				);
				return $ack;
			}
		} else {
			$ack = array(
				"ack" => 0,
				"ack_msg" => "No Orders Found !!",
				"developer_msg" => "No Customer found!!",
				"result" => $result,
			);
			return $ack;
		}
	}
	function getProduct($detail, $limit = "")
	{
		// WHERE PARSING START //
		extract($detail);

		$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
		$entry_flag = isset($_REQUEST['entry_flag']) ? $_REQUEST['entry_flag'] : "";
		$where = "";
		$pro_where = "";
		if (isset($detail['tcid']) && $detail['tcid'] != ""  && $detail['cid'] == "") {
			$tcid = $detail['tcid'];
			$where = "isDelete=0 AND isActive=1 AND tcid='" . $tcid . "'";
			$pro_where .= " AND isDelete=0 AND isActive=1 AND tcid='" . $tcid . "'";
		} else if (isset($detail['cid']) && $detail['cid'] != "") {
			$cid = $detail['cid'];
			$where = "isDelete=0 AND isActive=1 AND id='" . $cid . "'";
			$pro_where .= " AND isDelete=0 AND isActive=1 AND cid='" . $cid . "'";
		} else {
			$where = "isDelete=0 AND isActive=1 ";
			$pro_where .= " AND isDelete=0 AND isActive=1 ";
		}
		if ($sales_id != "") {
			//echo "sd";exit;
			$get_top_ids = $this->db->rp_getValue("sales_executive", "top_category_id", "isDelete=0 AND id='" . $sales_id . "'");
			$where .= " AND tcid IN(" . $get_top_ids . ")";
			$pro_where .= " AND tcid IN(" . $get_top_ids . ")";
		}

		if ($entry_flag == "6") {
			//echo "string";exit();
			$get_top_ids = $this->db->rp_getValue("executive", "top_category_id", "isDelete=0 AND id='" . $uid . "'", 0);
			$where .= " AND tcid IN(" . $get_top_ids . ")";
			$pro_where .= " AND tcid IN(" . $get_top_ids . ")";
		}
		if (isset($detail['search_name']) && $detail['search_name'] != "") {
			$pro_where .= " AND (name LIKE '%" . $detail['search_name'] . "%'";
		} else {
			$pro_where .= " AND ";
		}
		//	echo $pro_where;exit;

		// WHERE PARSING OVER // 
		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['uid'] . "' AND isDelete=0", 0);

		$hcat_r = $this->db->rp_getData("category_master", "*", $where . "", "id ASC", 0);
		if ($hcat_r) {
			$category1 = array();
			while ($hcat_d = mysqli_fetch_assoc($hcat_r)) {
				//echo "9";
				//Fetching Only Id then using function getProductDetail get Information of that product
				$category_id = $hcat_d['id'];
				//echo $category_id;
				$product = array();
				$PROIDS = array();
				$PROIDS_W = array();
				$pro_where1 = "";

				$pro_r1 =	$this->db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND isVisible=0 AND cid=" . $category_id, "display_order ASC", 0);
				//echo "string";exit;
				if ($pro_r1) {
					while ($pro_d1 = mysqli_fetch_assoc($pro_r1)) {
						//print_r($pro_d1);
						$PROIDS[] = $pro_d1['id'];
					}

					$PROIDS_s = "";
					if (!empty($PROIDS)) {
						$PROIDS_s = implode(",", $PROIDS);
						$where_w = " product_id IN (" . $PROIDS_s . ") AND catno LIKE '%" . $_REQUEST['search_name'] . "%'";
					}
					//	print_r($PROIDS_s);exit;
					//	echo $where_w;
					if ($PROIDS_s != "") {
						$pro_w_r = $this->db->rp_getData("product_weight_price", "product_id", $where_w . "  AND isDelete=0 ", "", 0);
						//$pro_w_r=$this->db->rp_getData("product_weight_price","product_id",$where_w." AND catno LIKE '%".$_REQUEST['search_name']."%' AND isDelete=0 ","",0);
						if ($pro_w_r) {
							while ($pro_w_d = mysqli_fetch_assoc($pro_w_r)) {	//print_r($pro_w_d);
								$PROIDS_W[] = $pro_w_d['product_id'];
							}
						}
						//print_r($PROIDS_W);exit;
						if (!empty($PROIDS_W)) {
							if (isset($detail['search_name']) && $detail['search_name'] != "") {
								$pro_where1 .= " OR ";
							}
							$PROIDS_W_s = implode(",", $PROIDS_W);
							//echo $PROIDS_W_s;
							$pro_where1 .= " id IN (" . $PROIDS_W_s . ")";
						}
						if (isset($detail['search_name']) && $detail['search_name'] != "") {
							$pro_where1 .= ")";
						}
					}
					$final_where = $pro_where . $pro_where1;
					//print_r($limit);exit();

					$pro_r =	$this->db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND isVisible=0 " . $final_where, "display_order ASC", 0, $limit);
				}
				//echo $pro_where; 

				/*$proids_array = $this->db->getCommaSepretedData("product_weight_price","*",$detail['search_name'],"catno");
			
				if($proids_array!="")
				{
					$where = "(name LIKE '%".$detail['search_name']."%' OR id IN (".$proids_array.")) AND isDelete=0";
				}*/

				//$pro_r=	$this->db->rp_getData("product","*",$where,"display_order ASC",0,$limit);
				if ($pro_r) {
					$order_unit_arr = array("-1" => "Box", "-2" => "Strip", "-3" => "Pallet", "1" => "Caret", "2" => "Big Box", "100" => "Nos");
					while ($pro_d = mysqli_fetch_assoc($pro_r)) {
						// print_r($pro_d);
						$pro_d['name'] = htmlentities($pro_d['name']);
						$pro_d['cat_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $pro_d['cid'] . "' AND isDelete=0", 0);
						$pro_d['top_cat_name'] = $this->db->rp_getValue("top_category_master", "name", "id='" . $pro_d['tcid'] . "' AND isDelete=0", 0);
						$pro_d['product_code'] = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
						$pro_d['unitname'] = $this->db->rp_getValue("unit", "name", "id='" . $pro_d['display_unit'] . "' AND isDelete=0", 0);

						$pro_d['customer_order_unit_name'] = $order_unit_arr[$pro_d['customer_unit_id']];
						$pro_d['sales_order_unit_name'] = $order_unit_arr[$pro_d['unit_id']];
						/*
						$customer_type=$this->db->rp_getValue("executive","type_of_executive","id='".$uid."' AND isDelete=0",0);
						if($customer_type=="3" && ($sales_id!=0 || $sales_id!="" || $sales_id!=null)){
							$pro_d['unit_id']="2";
						}else if($sales_id==0 || $sales_id=="" || $sales_id==null){
							$pro_d['unit_id']="3";
						}else{
							$pro_d['unit_id']="3";
						}*/
						//$pro_d['unit_id']="1";
						$pro_d['total_size'] = $this->db->rp_getTotalRecord("product_weight_price", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);

						$descr = html_entity_decode($pro_d['descr']);
						$descr = strip_tags($descr);
						$descr = str_replace("\r\n", "", $descr);
						$descr = str_replace(",", ",<br/>", $descr);
						$pro_d['descr'] = $descr;
						$pid = $pro_d['id'];
						if ($pro_d['image_path'] != "") {
							$pro_d['image_path'] = SITEURL . PRODUCT . $pro_d['image_path'];
						}
						$user_discount = $this->db->rp_getValue("price_table", "discount", "tcid='" . $pro_d['tcid'] . "' AND uid='" . $uid . "' AND isDelete=0", 0);

						$price_r =	$this->db->rp_getData("product_weight_price", "id,weight_id,price,product_id,inner_size,outer_size,catno,stock_qty,is_including", "isDelete=0 AND product_id=" . $pid, "", 0);

						if ($price_r) {
							$product_weight_price = array();
							while ($price_d = mysqli_fetch_assoc($price_r)) {
								$price_d['original_price'] = $this->db->rp_number_format($price_d['price'], 2);
								$price_d['minimum_selling_price'] = 0; // Min sell removed — MRP only


								if ($price_d['is_including'] == 1) {
									if ($pro_d['igst'] != "" && $pro_d['igst'] != 0) {

										$gst_val = "1." . $pro_d['igst'];
										$price_d['including_gst_price'] = $this->db->rp_number_format($price_d['original_price'] / $gst_val, 2);
									} else {
										$price_d['including_gst_price'] = $price_d['original_price'] / 1;
									}
								} else {

									$price_d['including_gst_price'] = $price_d['original_price'];
								}
								// echo $price_d['including_gst_price'];exit();


								/*if($user_discount!=0 && $user_discount!="")
								{
									$discount_amount=($price_d['price']*$user_discount)/100;
									$price_d['price']=$this->db->rp_number_format($price_d['price']-$discount_amount);
									$price_d['discount']=$user_discount;									
									$price_d['discount_amount']=$this->db->rp_number_format($discount_amount);									
								}
								else
								{
									$price_d['price']=$this->db->rp_number_format($price_d['price']);
									$price_d['discount']=0;									
									$price_d['discount_amount']=0;	
								}*/
								$price_d['price'] = $this->db->rp_number_format($price_d['price'], 2);
								$price_d['discount'] = 0;
								$price_d['discounted_amount'] = 0;
								if ($price_list_id != 0) {
									$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
									if ($check_product_in_list > 0) {
										$price_d['price'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);
										$price_d['discount'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

										$price_d['discounted_amount'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "'"), 2);
									}
								}

								$price_d['name'] = $this->db->rp_getValue("weight", "name", "id='" . $price_d['weight_id'] . "'");

								$price_d['display_order'] = $this->db->rp_getValue("weight", "display_order", "id='" . $price_d['weight_id'] . "'");

								$product_weight_price[] = $price_d;

								$this->sortBy('display_order', $product_weight_price, 'asc');
							}
							$pro_d['product_weight_price'] = $product_weight_price;
							$product[] = $pro_d;
						}
						//	exit;
					}

					//print_r($product);

				}
				if (!empty($product)) {

					$hcat_d['products'] = $product;
					// print_r($hcat_d);
					// array_push($category1,$product);
					$category1 = array_merge($category1, $product);
				}
				//print_r($category1);
			}
			//exit();
			/*print_r($category1);
				exit();*/
			if (!empty($category1)) {
				//print_r($product);exit;
				$product1 = $this->db->unique_multi_array($category1, 'id');
				$ack = array("ack" => 1, "products" => $product1);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			}
			return $ack;
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			return $ack;
		}
	}
	function getProductaaaaaaaa($detail, $limit = "")
	{
		// WHERE PARSING START //
		extract($detail);

		$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
		$entry_flag = isset($_REQUEST['entry_flag']) ? $_REQUEST['entry_flag'] : "";
		$where = "";
		$pro_where = "";
		if (isset($detail['tcid']) && $detail['tcid'] != ""  && $detail['cid'] == "") {
			$tcid = $detail['tcid'];
			$where = "isDelete=0 AND isActive=1 AND tcid='" . $tcid . "'";
			$pro_where .= " AND isDelete=0 AND isActive=1 AND tcid='" . $tcid . "'";
		} else if (isset($detail['cid']) && $detail['cid'] != "") {
			$cid = $detail['cid'];
			$where = "isDelete=0 AND isActive=1 AND id='" . $cid . "'";
			$pro_where .= " AND isDelete=0 AND isActive=1 AND cid='" . $cid . "'";
		} else {
			$where = "isDelete=0 AND isActive=1 ";
			$pro_where .= " AND isDelete=0 AND isActive=1 ";
		}
		if ($sales_id != "") {
			//echo "sd";exit;
			$get_top_ids = $this->db->rp_getValue("sales_executive", "category_id", "isDelete=0 AND id='" . $sales_id . "'", 1);
			$where .= " AND id IN(" . $get_top_ids . ")";
			$pro_where .= " AND cid IN(" . $get_top_ids . ")";
		}

		if ($entry_flag == "6") {
			$get_top_ids = $this->db->rp_getValue("executive", "category_id", "isDelete=0 AND id='" . $uid . "'");
			$where .= " AND id IN(" . $get_top_ids . ")";
			$pro_where .= " AND cid IN(" . $get_top_ids . ")";
		}
		if (isset($detail['search_name']) && $detail['search_name'] != "") {
			$pro_where .= " AND (name LIKE '%" . $detail['search_name'] . "%'";
		} else {
			$pro_where .= " AND ";
		}

		//	echo $pro_where;exit;

		// WHERE PARSING OVER //

		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['uid'] . "' AND isDelete=0", 0);




		$hcat_r = $this->db->rp_getData("category_master", "*", $where . "", "id ASC", 0);
		if ($hcat_r) {

			$category1 = array();
			while ($hcat_d = mysqli_fetch_assoc($hcat_r)) {
				//echo "9";
				//Fetching Only Id then using function getProductDetail get Information of that product
				$category_id = $hcat_d['id'];
				//echo $category_id;
				$product = array();
				$PROIDS = array();
				$PROIDS_W = array();
				$pro_where1 = "";

				$pro_r1 =	$this->db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND isVisible=0 AND cid=" . $category_id, "display_order ASC", 0);
				//echo "string";exit;
				if ($pro_r1) {

					while ($pro_d1 = mysqli_fetch_assoc($pro_r1)) {
						//print_r($pro_d1);
						$PROIDS[] = $pro_d1['id'];
					}

					$PROIDS_s = "";
					if (!empty($PROIDS)) {
						$PROIDS_s = implode(",", $PROIDS);
						$where_w = " product_id IN (" . $PROIDS_s . ") AND catno LIKE '%" . $_REQUEST['search_name'] . "%'";
					}
					//	print_r($PROIDS_s);exit;
					//	echo $where_w;
					if ($PROIDS_s != "") {
						$pro_w_r = $this->db->rp_getData("product_weight_price", "product_id", $where_w . "  AND isDelete=0 ", "", 0);
						//$pro_w_r=$this->db->rp_getData("product_weight_price","product_id",$where_w." AND catno LIKE '%".$_REQUEST['search_name']."%' AND isDelete=0 ","",0);
						if ($pro_w_r) {
							while ($pro_w_d = mysqli_fetch_assoc($pro_w_r)) {	//print_r($pro_w_d);
								$PROIDS_W[] = $pro_w_d['product_id'];
							}
						}
						//print_r($PROIDS_W);exit;
						if (!empty($PROIDS_W)) {
							if (isset($detail['search_name']) && $detail['search_name'] != "") {
								$pro_where1 .= " OR ";
							}
							$PROIDS_W_s = implode(",", $PROIDS_W);
							//echo $PROIDS_W_s;
							$pro_where1 .= " id IN (" . $PROIDS_W_s . ")";
						}
						if (isset($detail['search_name']) && $detail['search_name'] != "") {
							$pro_where1 .= ")";
						}
					}



					$final_where = $pro_where . $pro_where1;

					$pro_r =	$this->db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND isVisible=0 " . $final_where, "display_order ASC", 0, $limit);
				}
				//echo $pro_where; 

				/*$proids_array = $this->db->getCommaSepretedData("product_weight_price","*",$detail['search_name'],"catno");
			
				if($proids_array!="")
				{
					$where = "(name LIKE '%".$detail['search_name']."%' OR id IN (".$proids_array.")) AND isDelete=0";
				}*/

				//$pro_r=	$this->db->rp_getData("product","*",$where,"display_order ASC",0,$limit);


				if ($pro_r) {

					while ($pro_d = mysqli_fetch_assoc($pro_r)) {
						//print_r($pro_d);
						$pro_d['cat_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $pro_d['cid'] . "' AND isDelete=0", 0);
						$pro_d['top_cat_name'] = $this->db->rp_getValue("top_category_master", "name", "id='" . $pro_d['tcid'] . "' AND isDelete=0", 0);
						$pro_d['product_code'] = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
						$pro_d['unitname'] = $this->db->rp_getValue("unit", "name", "id='" . $pro_d['display_unit'] . "' AND isDelete=0", 0);
						//$pro_d['unit_id']="1";
						$pro_d['total_size'] = $this->db->rp_getTotalRecord("product_weight_price", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
						$descr = html_entity_decode($pro_d['descr']);
						$descr = strip_tags($descr);
						$descr = str_replace("\r\n", "", $descr);
						$descr = str_replace(",", ",<br/>", $descr);
						$pro_d['descr'] = $descr;
						$pid = $pro_d['id'];
						if ($pro_d['image_path'] != "") {
							$pro_d['image_path'] = SITEURL . PRODUCT . $pro_d['image_path'];
						}
						$user_discount = $this->db->rp_getValue("price_table", "discount", "tcid='" . $pro_d['tcid'] . "' AND uid='" . $uid . "' AND isDelete=0", 0);

						$price_r =	$this->db->rp_getData("product_weight_price", "id,weight_id,price,product_id,inner_size,outer_size,catno,stock_qty,is_including", "isDelete=0 AND product_id=" . $pid, "", 0);

						if ($price_r) {
							$product_weight_price = array();
							while ($price_d = mysqli_fetch_assoc($price_r)) {
								$price_d['original_price'] = $this->db->rp_number_format($price_d['price'], 2);


								if ($price_d['is_including'] == 1) {
									if ($pro_d['igst'] != "" && $pro_d['igst'] != 0) {

										$gst_val = "1." . $pro_d['igst'];
										$price_d['including_gst_price'] = $this->db->rp_number_format($price_d['original_price'] / $gst_val, 2);
									} else {
										$price_d['including_gst_price'] = $price_d['original_price'] / 1;
									}
								} else {

									$price_d['including_gst_price'] = $price_d['original_price'];
								}
								// echo $price_d['including_gst_price'];exit();


								/*if($user_discount!=0 && $user_discount!="")
								{
									$discount_amount=($price_d['price']*$user_discount)/100;
									$price_d['price']=$this->db->rp_number_format($price_d['price']-$discount_amount);
									$price_d['discount']=$user_discount;									
									$price_d['discount_amount']=$this->db->rp_number_format($discount_amount);									
								}
								else
								{
									$price_d['price']=$this->db->rp_number_format($price_d['price']);
									$price_d['discount']=0;									
									$price_d['discount_amount']=0;	
								}*/
								$price_d['price'] = $this->db->rp_number_format($price_d['price'], 2);
								$price_d['discount'] = 0;
								$price_d['discounted_amount'] = 0;
								if ($price_list_id != 0) {
									$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
									if ($check_product_in_list > 0) {
										$price_d['price'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);
										$price_d['discount'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

										$price_d['discounted_amount'] = $this->db->rp_number_format($this->db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "'"), 2);
									}
								}

								$price_d['name'] = $this->db->rp_getValue("weight", "name", "id='" . $price_d['weight_id'] . "'");
								$price_d['display_order'] = $this->db->rp_getValue("weight", "display_order", "id='" . $price_d['weight_id'] . "'");
								$product_weight_price[] = $price_d;
								$this->sortBy('display_order', $product_weight_price, 'asc');
							}
							$pro_d['product_weight_price'] = $product_weight_price;
							$product[] = $pro_d;
						}
						//	exit;
					}

					//print_r($product);

				}
				if (!empty($product)) {

					$hcat_d['products'] = $product;
					// print_r($hcat_d);
					// array_push($category1,$product);
					$category1 = array_merge($category1, $product);
				}
				//print_r($category1);
			}
			//exit();
			/*print_r($category1);
				exit();*/


			if (!empty($category1)) {
				//print_r($product);exit;
				$product1 = $this->db->unique_multi_array($category1, 'id');
				$ack = array("ack" => 1, "products" => $product1);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			}
			return $ack;
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			return $ack;
		}
	}
	function getProductPriceList($detail, $limit = "")
	{
		// WHERE PARSING START //
		$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
		$where = "";
		if (isset($detail['tcid']) && $detail['tcid'] != ""  && $detail['cid'] == "") {
			$tcid = $detail['tcid'];
			$where = "isDelete=0 AND isActive=1 AND tcid='" . $tcid . "'";
		} else if (isset($detail['cid']) && $detail['cid'] != "") {
			$cid = $detail['cid'];
			$where = "isDelete=0 AND isActive=1 AND id='" . $cid . "'";
		} else {
			$where = "isDelete=0 AND isActive=1 ";
		}
		// WHERE PARSING OVER //

		$price_list_id = $this->db->rp_getValue("customer", "price_list_id", "id='" . $detail['uid'] . "' AND isDelete=0", 0);
		$hcat_r = $this->db->rp_getData("category_master", "*", $where, "", 0);
		if ($hcat_r) {
			$product = array();
			$category = array();
			while ($hcat_d = mysqli_fetch_assoc($hcat_r)) {
				//Fetching Only Id then using function getProductDetail get Information of that product
				$category_id = $hcat_d['id'];
				$pro_r =	$this->db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND cid=" . $category_id, "display_order ASC", 0, $limit);
				if ($pro_r) {

					while ($pro_d = mysqli_fetch_assoc($pro_r)) {
						$pro_d['cat_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $pro_d['cid'] . "' AND isDelete=0", 0);
						$pro_d['product_code'] = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
						$pro_d['total_size'] = $this->db->rp_getTotalRecord("product_weight_price", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
						$descr = html_entity_decode($pro_d['descr']);
						$descr = strip_tags($descr);
						$descr = str_replace("\r\n", "", $descr);
						$descr = str_replace(",", ",<br/>", $descr);
						$pro_d['descr'] = $descr;
						$pid = $pro_d['id'];
						if ($pro_d['image_path'] != "") {
							$pro_d['image_path'] = SITEURL . PRODUCT . $pro_d['image_path'];
						}

						$price_r =	$this->db->rp_getData("product_weight_price", "id,weight_id,price,product_id,inner_size,outer_size,catno", "isDelete=0 AND product_id=" . $pid, "", 0);

						if ($price_r) {
							$product_weight_price = array();
							while ($price_d = mysqli_fetch_assoc($price_r)) {
								$price_d['original_price'] = $price_d['price'];
								$price_d['price'] = $this->db->rp_number_format($price_d['price']);
								$price_d['discount'] = 0;
								$price_d['discounted_amount'] = 0;
								if ($price_list_id != 0) {
									$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
									if ($check_product_in_list > 0) {
										$price_d['price'] = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
										$price_d['discount'] = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
										$price_d['discounted_amount'] = $this->db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "'");
									}
								}
								$price_d['name'] = $this->db->rp_getValue("weight", "name", "id='" . $price_d['weight_id'] . "'");
								$price_d['display_order'] = $this->db->rp_getValue("weight", "display_order", "id='" . $price_d['weight_id'] . "'");
								$product_weight_price[] = $price_d;
								$this->sortBy('display_order', $product_weight_price, 'asc');
							}
							$pro_d['product_weight_price'] = $product_weight_price;
							$product[] = $pro_d;
						}
					}
				}
				// print_r($product);exit;
				$hcat_d['products'] = $product;
				// print_r($hcat_d);
				array_push($category, $product);
				// print_r($category);
			}
			if (!empty($product)) {
				$ack = array("ack" => 1, "products" => $product);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			}
			return $ack;
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Prodcuts found!!!");
			return $ack;
		}
	}

	function sortBy($field, &$array, $direction = 'asc')
	{
		usort($array, create_function('$a, $b', '
	        $a = $a["' . $field . '"];
	        $b = $b["' . $field . '"];

	        if ($a == $b) return 0;

	        $direction = strtolower(trim($direction));

	        return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
	    '));

		return true;
	}
}
