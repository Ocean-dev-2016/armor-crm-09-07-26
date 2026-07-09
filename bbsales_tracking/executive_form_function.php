<?php
// echo "<pre>";
// print_r($_REQUEST);exit;
// print_r($_REQUEST['top_category_id']);exit;
$page_id = 555;
$page_slug = 'page_executive';
include('connect.php');
include('../include/class.executive.php');
//include('../include/notification.class.php');

if (isset($_REQUEST['submit']) && isset($_REQUEST['mode']) && $_REQUEST['mode'] != "") {
	$mode = $_REQUEST['mode'];
	$flag = $_REQUEST['flag'];
	//echo $flag; exit();
	if ($flag == "") {
		$customer_flag = 0;
	} else {
		$customer_flag = 1;
	}
	// $area_id = array();
	if (isset($_REQUEST['cname']) && isset($_REQUEST['mobile_no1'])) {
		$id						= $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		$price_list_id			= isset($_REQUEST['price_list_id']) ? $db->clean($_REQUEST['price_list_id']) : "";
		$end_user_type			= isset($_REQUEST['end_user_type']) ? $db->clean($_REQUEST['end_user_type']) : "";
		$type_of_executive		= isset($_REQUEST['type_of_inquiry']) ? $db->clean($_REQUEST['type_of_inquiry']) : "";
		$company_type			= isset($_REQUEST['company_type']) ? $db->clean($_REQUEST['company_type']) : "";
		$company_name			= isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "";
		$address				= isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
		$address2				= isset($_REQUEST['address2']) ? $db->clean($_REQUEST['address2']) : "";
		$zip					= isset($_REQUEST['zip']) ? $db->clean($_REQUEST['zip']) : "";
		$super_stockist_id		= isset($_REQUEST['super_stockist_id']) ? $db->clean($_REQUEST['super_stockist_id']) : "";

		$zone					= isset($_REQUEST['zone']) ? $db->clean($_REQUEST['zone']) : "";

		$city					= isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
		$main_city		= isset($_REQUEST['main_city']) ? $db->clean($_REQUEST['main_city']) : "";
		$state					= isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
		$country				= isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
		$image_path				= isset($_FILES);
		$old_image_path			= isset($_REQUEST['old_image_path']) ? $db->clean($_REQUEST['old_image_path']) : "";
		$email					= isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
		$email_cc				= isset($_REQUEST['email_cc']) ? $db->clean($_REQUEST['email_cc']) : "";
		$dealer_distributor_id	= isset($_REQUEST['dealer_distributor_id']) ? $db->clean($_REQUEST['dealer_distributor_id']) : "";
		$cname					= isset($_REQUEST['cname']) ? $db->clean($_REQUEST['cname']) : "";
		$cst					= isset($_REQUEST['cst']) ? $db->clean($_REQUEST['cst']) : "";
		$pan					= isset($_REQUEST['pan']) ? $db->clean($_REQUEST['pan']) : "";
		$phone					= isset($_REQUEST['phone']) ? $db->clean($_REQUEST['phone']) : "";
		$gst					= isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : "";
		$vat					= isset($_REQUEST['vat']) ? $db->clean($_REQUEST['vat']) : "";
		$excise					= isset($_REQUEST['excise']) ? $db->clean($_REQUEST['excise']) : "";
		$class_id				= isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
		$mobile_no1				= isset($_REQUEST['mobile_no1']) ? $db->clean($_REQUEST['mobile_no1']) : "";
		$whatsapp_no			= isset($_REQUEST['whatsapp_no']) ? $db->clean($_REQUEST['whatsapp_no']) : "";
		// $area_id				= $_REQUEST['area_id'];

		$area_id				= isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";
		//$discount				= $_REQUEST['discount'];					
		$password				= ($_REQUEST['password']) ? md5($db->clean($_REQUEST['password'])) : "";
		$latitude				= isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
		$longitude				= isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
		// $vendor_desk			= isset($_FILES['vendor_desk']) ? $db->clean($_FILES['vendor_desk']) : "";
		$vendor_desk			= isset($_FILES);
		$old_vendor_desk		= isset($_REQUEST['old_vendor_desk']) ? $db->clean($_REQUEST['old_vendor_desk']) : "";
		$office_supplier		= isset($_FILES);
		$old_office_supplier	= isset($_REQUEST['old_office_supplier']) ? $db->clean($_REQUEST['old_office_supplier']) : "";
		$gst_detail				= isset($_FILES);
		$old_gst_detail			= isset($_REQUEST['old_gst_detail']) ? $db->clean($_REQUEST['old_gst_detail']) : "";
		$other_image			= isset($_FILES);
		$old_other_image		= isset($_REQUEST['old_other_image']) ? $db->clean($_REQUEST['old_other_image']) : "";
		$inquiry_id             =  $_REQUEST['inquiry_id'];
		$seid                   = isset($_REQUEST['seid']) ? $db->clean($_REQUEST['seid']) : "";
		$client_code		    = isset($_REQUEST['client_code']) ? $db->clean($_REQUEST['client_code']) : "";
		$industry_type			= isset($_REQUEST['industry_type']) ? $db->clean($_REQUEST['industry_type']) : "";
		$cash_discount			= isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : "";
		$additional_discount	= isset($_REQUEST['additional_discount']) ? $db->clean($_REQUEST['additional_discount']) : "";
		$credit_limit		    = isset($_REQUEST['credit_limit']) ? $db->clean($_REQUEST['credit_limit']) : "";
		$credit_day		        = isset($_REQUEST['credit_day']) ? $db->clean($_REQUEST['credit_day']) : "";
		$shipping_address		= isset($_REQUEST['shipping_address']) ? $db->clean($_REQUEST['shipping_address']) : "";

		// print_r($_REQUEST['shipping_address']); exit;
		$shipping_address = implode(",", $_REQUEST['shipping_address']);
		if ($_REQUEST['shipping_address'] == "") {
			$shipping_address = "";
		}
		$phone		= isset($_REQUEST['phone']) ? $db->clean($_REQUEST['phone']) : "";

		// print_r($_REQUEST['shipping_address']); exit;
		$phone = implode(",", $_REQUEST['phone']);
		if ($_REQUEST['phone'] == "") {
			$phone = "";
		}
		$customer_name	= isset($_REQUEST['customer_name']) ? $db->clean($_REQUEST['customer_name']) : "";

		// print_r($_REQUEST['shipping_address']); exit;
		$customer_name = implode(",", $_REQUEST['customer_name']);
		if ($_REQUEST['customer_name'] == "") {
			$customer_name = "";
		}


		$billing_address		= isset($_REQUEST['billing_address']) ? $db->clean($_REQUEST['billing_address']) : "";
		$remark		= isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
		$entry_flag		        = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "1";
		$update_entry_flag		= isset($_REQUEST['update_entry_flag']) ? $db->clean($_REQUEST['update_entry_flag']) : "1";
		$file					= $_FILES;
		$credit_debit_type		= isset($_REQUEST['credit_debit_type']) ? $db->clean($_REQUEST['credit_debit_type']) : "";
		$openinig_balance		= isset($_REQUEST['openinig_balance']) ? $db->clean($_REQUEST['openinig_balance']) : "";
		$client_code_sr_by_type		= isset($_REQUEST['client_code_sr_by_type']) ? $db->clean($_REQUEST['client_code_sr_by_type']) : "";

		$order_view_flag        = isset($_REQUEST['order_view_flag']) ? $db->clean($_REQUEST['order_view_flag']) : 0;
		$order_insert_flag        = isset($_REQUEST['order_insert_flag']) ? $db->clean($_REQUEST['order_insert_flag']) : 0;
		$order_update_flag        = isset($_REQUEST['order_update_flag']) ? $db->clean($_REQUEST['order_update_flag']) : 0;
		$customer_insert_flag        = isset($_REQUEST['customer_insert_flag']) ? $db->clean($_REQUEST['customer_insert_flag']) : 0;
		$customer_update_flag        = isset($_REQUEST['customer_update_flag']) ? $db->clean($_REQUEST['customer_update_flag']) : 0;
		$dealer_order_view_flag        = isset($_REQUEST['dealer_order_view_flag']) ? $db->clean($_REQUEST['dealer_order_view_flag']) : 0;

		$dealer_order_insert_flag     = isset($_REQUEST['dealer_order_insert_flag']) ? $db->clean($_REQUEST['dealer_order_insert_flag']) : 0;

		$outlets_order_view_flag    = isset($_REQUEST['outlets_order_view_flag']) ? $db->clean($_REQUEST['outlets_order_view_flag']) : 0;

		$outlets_order_insert_flag  = isset($_REQUEST['outlets_order_insert_flag']) ? $db->clean($_REQUEST['outlets_order_insert_flag']) : 0;
		$order_approve_flag  = isset($_REQUEST['order_approve_flag']) ? $db->clean($_REQUEST['order_approve_flag']) : 0;
		$booking_place  = isset($_REQUEST['booking_place']) ? $db->clean($_REQUEST['booking_place']) : '';

		$top_category_id  = isset($_REQUEST['top_category_id']) ? $db->clean($_REQUEST['top_category_id']) : 0;

		$transport_by_id	     	 = isset($_REQUEST['transport_by_id']) ? $db->clean($_REQUEST['transport_by_id']) : "";
		$transporter_id	     	 = isset($_REQUEST['transporter_id']) ? $db->clean($_REQUEST['transporter_id']) : "";

		if (sizeof($_REQUEST['top_category_id']) != 0) {
			$top_category_id = implode(',', $_REQUEST['top_category_id']);
		} else {
			$top_category_id = "";
		}
		$type_of_company			= isset($_REQUEST['type_of_company']) ? $db->clean($_REQUEST['type_of_company']) : "";
		$purchasing_from			= isset($_REQUEST['purchasing_from']) ? $db->clean($_REQUEST['purchasing_from']) : "";
		$turnover = !empty($_REQUEST['turnover']) ? $_REQUEST['turnover'] : '';
		$turnover_year = !empty($_REQUEST['turnover_year']) ? $_REQUEST['turnover_year'] : '';
		$channel_partner_flag = isset($_REQUEST['channel_partner_flag']) ? $db->clean($_REQUEST['channel_partner_flag']) : 0;

		$detail                          = array();
		$detail['id']                    = $id;
		$detail['price_list_id']         = $price_list_id;
		$detail['type_of_executive']     = $type_of_executive;
		$detail['company_type']          = $company_type;
		$detail['company_name']          = $company_name;
		$detail['address']               = $address;
		$detail['address2']              = $address2;
		$detail['super_stockist_id']     = $super_stockist_id;
		$detail['zone']					 = $zone;
		$detail['city']                  = $city;
		$detail['main_city']                  = $main_city;
		$detail['state']                 = $state;
		$detail['country']               = $country;
		$detail['image_path']            = $_FILES;
		$detail['old_image_path']        = $old_image_path;
		$detail['email']                 = $email;
		$detail['email_cc']              = $email_cc;
		$detail['dealer_distributor_id'] = $dealer_distributor_id;
		$detail['cname']                 = $cname;
		$detail['phone']                 = $phone;
		$detail['customer_name']         = $customer_name;
		$detail['pan']                   = $pan;
		$detail['gst']                   = $gst;
		$detail['cst']                   = $cst;
		$detail['vat']                   = $vat;
		$detail['excise']                = $excise;
		$detail['class_id']              = $class_id;
		$detail['mobile_no1']            = $mobile_no1;
		$detail['whatsapp_no']           = $whatsapp_no;
		$detail['area_id']               = $area_id;
		$detail['discount']              = 0;
		$detail['password']              = $password;
		$detail['latitude']              = $latitude;
		$detail['longitude']             = $longitude;
		$detail['vendor_desk']           = $_FILES;
		$detail['old_vendor_desk']       = $old_vendor_desk;
		$detail['office_supplier']       = $_FILES;
		$detail['old_office_supplier']   = $old_office_supplier;
		$detail['gst_detail']            = $_FILES;
		$detail['old_gst_detail']        = $old_gst_detail;
		$detail['other_image']           = $_FILES;
		$detail['old_other_image']       = $old_other_image;
		$detail['seid']                  = $seid;
		$detail['client_code']           = $client_code;
		$detail['industry_type']         = $industry_type;
		$detail['cash_discount']         = $cash_discount;
		$detail['additional_discount']   = $additional_discount;
		$detail['credit_limit']          = $credit_limit;
		$detail['credit_day']            = $credit_day;
		$detail['shipping_address']      = $shipping_address;

		$detail['billing_address']       = $billing_address;
		$detail['remark']      			 = $remark;
		$detail['entry_flag']            = $entry_flag;
		$detail['update_entry_flag']     = $update_entry_flag;
		$detail['credit_debit_type']     = $credit_debit_type;
		$detail['openinig_balance']      = $openinig_balance;
		// $detail['category_id']      = $category_id;
		$detail['top_cat_id']      = $top_cat_id;
		$detail['customer_insert_flag'] = $customer_insert_flag;
		$detail['customer_update_flag'] = $customer_update_flag;
		$detail['order_view_flag'] = $order_view_flag;
		$detail['order_insert_flag'] = $order_insert_flag;
		$detail['order_update_flag'] = $order_update_flag;

		$detail['dealer_order_view_flag']   =  $dealer_order_view_flag;
		$detail['dealer_order_insert_flag'] = $dealer_order_insert_flag;

		$detail['outlets_order_view_flag']  = $outlets_order_view_flag;
		$detail['outlets_order_insert_flag']  = $outlets_order_insert_flag;
		$detail['order_approve_flag']  = $order_approve_flag;
		$detail['top_category_id']  = $top_category_id;
		$detail['type_of_company']  = $type_of_company;
		$detail['booking_place']  = $booking_place;
		$detail['transport_by_id']  = $transport_by_id;
		$detail['transporter_id']  = $transporter_id;
		$detail['purchasing_from']  = $purchasing_from;
		$detail['turnover'] = $turnover;
		$detail['turnover_year'] = $turnover_year;
		$detail['channel_partner_flag'] = $channel_partner_flag;

		//print_r($detail);exit();

		//echo $detail['type_of_company'];exit();

		// echo $outlets_order_view_flag;exit;


		// $detail['area_id']              = $area_id;

		// $value_check = sizeof($area_id);
		// if (in_array($value_check, $size)) {
		// 	$isValidArray = true;
		// } else {
		// 	$isValidArray = false;
		// }

		// if ($isValidArray && !empty($area_id)) {
		// 	for ($i = 0; $i < sizeof($area_id); $i++) {
		// 		$item[] = array("area_id" => $area_id[$i]);
		// 	}
		// }
		// $size  []                        = sizeof($shipping_address);


		// $size[]=sizeof($shipping_address);
		// $value_check=sizeof($shipping_address);

		// if(in_array($value_check,$size))
		// {
		// 	$isValidArray=true;
		// }
		// else
		// {
		// 	$isValidArray=false;
		// }
		// if($isValidArray && !empty($shipping_address))
		// {
		// 	for($i=0;$i<sizeof($shipping_address);$i++)
		// 	{



		// 		$item[]=array("shipping_address"=>$shipping_address[$i]);
		// 	}
		// }

		// echo "<pre>"; print_r($isValidArray); exit;


		$inquiry_date			= date("Y-m-d");
		$inquiry = new Executive();
		if ($mode == "add") {
			// echo '<pre>';
			// print_r($_REQUEST);
			// echo '</pre>';
			// exit;
			// if (isset($_FILES["image_path"])) {
			// 	$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
			// 	$temp = explode(".", $_FILES["image_path"]["name"]);
			// 	$extension = end($temp);

			// 	$fileName 	= $db->clean($_FILES["image_path"]["name"]);
			// 	if ($fileName != "") {
			// 		$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
			// 		$adate 		= date('Y-m-d H:i:m');

			// 		$extension	= end(explode(".", $fileName));
			// 		if (!in_array($extension, $allowedExts)) {
			// 			$file_error = true;
			// 		}

			// 		$image_path	= 'image_' . substr(sha1(time()), 0, 6) . "." . $extension;
			// 		$filePath 	= SUPER_STOCKIST_A . $image_path;
			// 		$_FILES['image_path']['tmp_name'];
			// 		move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);

			// 		$new_image = true;
			// 	} else {
			// 		$image_path = "";
			// 	}
			// } else {
			// 	$new_image = false;
			// 	$image_path = "";
			// }
			if (isset($_FILES["vendor_desk"])) {
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "pdf", "PDF");
				$temp = explode(".", $_FILES["vendor_desk"]["name"]);
				$extension = end($temp);

				$fileName 	= $db->clean($_FILES["vendor_desk"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($_FILES["vendor_desk"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$vendor_desk	= 'vendor_desk_' . time() . "." . $extension;
					$filePath 	= SUPER_STOCKIST_A . $vendor_desk;
					$_FILES['vendor_desk']['tmp_name'];
					move_uploaded_file($_FILES['vendor_desk']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$vendor_desk = "";
				}
			} else {
				$new_image = false;
				$vendor_desk = "";
			}
			if (isset($_FILES["office_supplier"])) {
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "pdf", "PDF");
				$temp = explode(".", $_FILES["office_supplier"]["name"]);
				$extension = end($temp);

				$fileName 	= $db->clean($_FILES["office_supplier"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($_FILES["office_supplier"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$office_supplier	= 'office_supplier_' . time() . "." . $extension;
					$filePath 	= SUPER_STOCKIST_A . $office_supplier;
					$_FILES['office_supplier']['tmp_name'];
					move_uploaded_file($_FILES['office_supplier']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$office_supplier = "";
				}
			} else {
				$new_image = false;
				$office_supplier = "";
			}
			if (isset($_FILES["gst_detail"])) {
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "pdf", "PDF");
				$temp = explode(".", $_FILES["gst_detail"]["name"]);
				$extension = end($temp);

				$fileName 	= $db->clean($_FILES["gst_detail"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($_FILES["gst_detail"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$gst_detail	= 'gst_detail_' . time() . "." . $extension;
					$filePath 	= SUPER_STOCKIST_A . $gst_detail;
					$_FILES['gst_detail']['tmp_name'];
					move_uploaded_file($_FILES['gst_detail']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$gst_detail = "";
				}
			} else {
				$new_image = false;
				$gst_detail = "";
			}
			if (isset($_FILES["other_image"])) {
				$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "pdf", "PDF");
				$temp = explode(".", $_FILES["other_image"]["name"]);
				$extension = end($temp);

				$fileName 	= $db->clean($_FILES["other_image"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($_FILES["other_image"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$other_image	= 'other_image_' . time() . "." . $extension;
					$filePath 	= SUPER_STOCKIST_A . $other_image;
					$_FILES['other_image']['tmp_name'];
					move_uploaded_file($_FILES['other_image']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$other_image = "";
				}
			} else {
				$new_image = false;
				$other_image = "";
			}
			// echo '<pre>';
			// print_r($other_image);
			// echo '</pre>';
			// exit;
			// echo $price_list_id;exit;
			// echo $password;exit;
			$ack = $inquiry->InsertExecutivePanel($end_user_type, $type_of_executive, $company_type, $company_name, $address, $address2, $super_stockist_id, $zone, $city, $state, $country, $image_path, $email, $email_cc, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $mobile_no1, $whatsapp_no, $area_id, $discount, $price_list_id, $vendor_desk, $office_supplier, $gst_detail, $other_image, $seid, $local_id, $type, $latitude, $longitude, $client_code, $industry_type, $cash_discount, $additional_discount, $credit_limit, $credit_day, $shipping_address, $billing_address, $remark, $password, $file, $entry_flag, $credit_debit_type, $openinig_balance, $client_code_sr_by_type, $top_cat_id, $customer_insert_flag, $customer_update_flag, $order_view_flag, $order_insert_flag, $order_update_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $outlets_order_insert_flag, $main_city, $order_approve_flag, $top_category_id, $outlets_order_view_flag, $type_of_company, $booking_place, $transporter_id, $transport_by_id, $purchasing_from, $customer_name, $turnover, $turnover_year, $channel_partner_flag);

			$user_id = $ack['inserted_id'];
			if ($ack['ack'] == 1) {
				/*delete inquiry*/
				$row = array("isDelete" => 1);
				$update = $db->rp_update("no_order_inquiry", $row, "id='" . $inquiry_id . "'", 0);
				/*delete inquiry*/
				/*$image_path=array();
				if (isset($_FILES["image_path"]) && $_FILES["image_path"]['size']!=0) 
				{
					
					$ri = $user_id;
					$rt = "executive";
					$tc = "executive";
					$rc = "id";
						//print_r($_FILES["image_path"]);exit();
					for($i=0;$i<sizeof($_FILES["image_path"]['name']);$i++)
					{

						$file_name = $_FILES['image_path']['name'][$i];
						$file_size = $_FILES['image_path']['size'][$i];
						$file_tmp = $_FILES['image_path']['tmp_name'][$i];
						$file_type = $_FILES['image_path']['type'][$i];
						$extension=explode(".",$file_name);
						
						$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
						$extension=$extension[sizeof($extension)-1];
						if(!in_array($extension,$allowed_extentions))
						{
							$file_error=true;
						}
						$orignal_file_name=$extension[0];
						if(in_array($extension,$allowed_extentions))
						{
							$attachment="../resource/image/";
							move_uploaded_file($file_tmp,$attachment.$file_name);
						}
						$MediaTitle=$file_name;
				    	$MediaOrignalTitle=$file_name;

						$MediaFileName=$file_name;
						// $MediaType=User::$ValidMediaType[$extension];
						$UploadDate=date("Y-m-d H:i:s");
						
						// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
						$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
						// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
						$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
						$MediaID=$db->rp_insert("media",$Values,$Columns,0);

						$image_path[] = $MediaID;
					}
					$image_path = implode(",", $image_path);
					$upadateid = $db->rp_update("executive",array("image_path"=>$image_path),"id='".$user_id."'",0);
				}*/
				$result = $ack['result'][0];
				//$db->addSuccessMessage("Executive successfully saved!!");
				$db->addSuccessMessage("Customer successfully saved !!");
				$db->rp_location("executive_manage.php?flag=" . $flag . "&back_request_url=" . "$mobile_no1");
				// $db->rp_location("executive_manage.php?flag=".$flag);
			} else {
				$db->addErrorMessage("Form submission failed Try again!!");
				$db->addErrorMessage($ack['ack_msg']);
				$_SESSION['detail'] = $detail;
				$db->rp_location("executive_crud.php?mode=add&type=" . $type_of_executive);
			}
		} else {
			if (isset($_REQUEST['id'])) {
				// echo '<pre>';
				// print_r($_FILES);
				// echo '</pre>';
				// exit;
				// if (isset($_FILES["image_path"]) && $_FILES["image_path"]['size'] != 0) {
				// 	// echo"(fds)";exit();
				// 	$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
				// 	$temp = explode(".", $_FILES["image_path"]["name"]);
				// 	$extension = end($temp);

				// 	$fileName 	= $db->clean($_FILES["image_path"]["name"]);
				// 	if ($fileName != "") {
				// 		$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
				// 		$adate 		= date('Y-m-d H:i:m');

				// 		$extension	= end(explode(".", $fileName));
				// 		if (!in_array($extension, $allowedExts)) {
				// 			$file_error = true;
				// 		}

				// 		$image_path	= 'image_' . substr(sha1(time()), 0, 6) . "." . $extension;
				// 		$filePath 	= SUPER_STOCKIST_A . $image_path;
				// 		$_FILES['image_path']['tmp_name'];
				// 		move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);

				// 		$new_image = true;
				// 	} else {
				// 		$image_path = $detail['old_image_path'];
				// 		$image_path = "";
				// 	}
				// } else {
				// 	$image_path = $detail['old_image_path'];
				// 	unset($detail['old_image_path']);
				// }
				if (isset($_FILES["vendor_desk"]) && $_FILES["vendor_desk"]['size'] != 0) {
					// echo"(fds)";exit();
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "PDF", "pdf");
					$temp = explode(".", $_FILES["vendor_desk"]["name"]);
					$extension = end($temp);

					$fileName 	= $db->clean($_FILES["vendor_desk"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["vendor_desk"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$vendor_desk	= 'vendor_desk_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= SUPER_STOCKIST_A . $vendor_desk;
						$_FILES['vendor_desk']['tmp_name'];
						move_uploaded_file($_FILES['vendor_desk']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$vendor_desk = $detail['old_vendor_desk'];
						$vendor_desk = "";
					}
				} else {
					$vendor_desk = $detail['old_vendor_desk'];
					unset($detail['old_vendor_desk']);
					// $vendor_desk = "";
				}
				if (isset($_FILES["office_supplier"]) && $_FILES["office_supplier"]['size'] != 0) {
					// echo"(fds)";exit();
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "pdf", "PDF");
					$temp = explode(".", $_FILES["office_supplier"]["name"]);
					$extension = end($temp);

					$fileName 	= $db->clean($_FILES["office_supplier"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["office_supplier"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$office_supplier	= 'office_supplier_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= SUPER_STOCKIST_A . $office_supplier;
						$_FILES['office_supplier']['tmp_name'];
						move_uploaded_file($_FILES['office_supplier']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$office_supplier = $detail['old_office_supplier'];
						$office_supplier = "";
					}
				} else {
					$office_supplier = $detail['old_office_supplier'];
					unset($detail['old_office_supplier']);
					// $office_supplier = "";
				}
				if (isset($_FILES["gst_detail"]) && $_FILES["gst_detail"]['size'] != 0) {
					// echo"(fds)";exit();
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "PDF", "pdf");
					$temp = explode(".", $_FILES["gst_detail"]["name"]);
					$extension = end($temp);

					$fileName 	= $db->clean($_FILES["gst_detail"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["gst_detail"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$gst_detail	= 'gst_detail_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= SUPER_STOCKIST_A . $gst_detail;
						$_FILES['gst_detail']['tmp_name'];
						move_uploaded_file($_FILES['gst_detail']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$gst_detail = $detail['old_gst_detail'];
						$gst_detail = "";
					}
				} else {
					$gst_detail = $detail['old_gst_detail'];
					unset($detail['old_gst_detail']);
					// $gst_detail = "";
				}
				if (isset($_FILES["other_image"]) && $_FILES["other_image"]['size'] != 0) {
					// echo"(fds)";exit();
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "PDF", "pdf");
					$temp = explode(".", $_FILES["other_image"]["name"]);
					$extension = end($temp);

					$fileName 	= $db->clean($_FILES["other_image"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["other_image"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$other_image	= 'other_image_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= SUPER_STOCKIST_A . $other_image;
						$_FILES['other_image']['tmp_name'];
						move_uploaded_file($_FILES['other_image']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$other_image = $detail['old_other_image'];
						$other_image = "";
					}
				} else {
					$other_image = $detail['old_other_image'];
					unset($detail['old_other_image']);
					// $other_image = "";
				}

				$executive_id = $_REQUEST['id'];
				//echo $executive_id;exit;
				// echo '<pre>';
				// print_r($address2);
				// echo '</pre>';
				// exit;
				$ack = $inquiry->UpdateExecutive($executive_id, $end_user_type, $type_of_executive, $company_type, $company_name, $address, $address2, $super_stockist_id, $zone, $city, $state, $country, $image_path, $email, $email_cc, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $mobile_no1, $whatsapp_no, $area_id, $discount, $price_list_id, $vendor_desk, $office_supplier, $gst_detail, $other_image, $latitude, $longitude, $seid, $client_code, $industry_type, $cash_discount, $additional_discount, $credit_limit, $credit_day, $shipping_address, $billing_address, $remark, $file, $update_entry_flag, $credit_debit_type, $openinig_balance, $top_cat_id, $customer_insert_flag, $customer_update_flag, $order_view_flag, $order_insert_flag, $order_update_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $outlets_order_insert_flag, $main_city, $order_approve_flag, $top_category_id, $outlets_order_view_flag, $type_of_company, $booking_place, $transporter_id, $transport_by_id, $purchasing_from, $customer_name, $turnover, $turnover_year, $channel_partner_flag);

				if ($ack['ack'] == 1) {
					if ($type_of_executive == "super_stockist") {
						$update_pricelist = $db->rp_update("executive", array("price_list_id" => $price_list_id), "super_stockist_id='" . $executive_id . "' AND isDelete=0 AND price_list_id=0");
					}
					$result['inquiry_status_slug'] = intval($result['inquiry_status_slug']) + 1;
					$result = $ack['result'][0];
					$db->addSuccessMessage("Customer successfully updated!!");
					if ($flag == "prospect") {
						$db->rp_location("executive_manage.php?flag=" . $flag);
					} else {
						$db->rp_location("executive_manage.php");
					}
				} else {
					$db->addErrorMessage("Form submission failed Try again!!");
					$db->addErrorMessage($ack['ack_msg']);
					$db->rp_location("executive_crud.php?mode=edit&id=" . $_REQUEST['id'] . "&type=" . $_REQUEST['type_of_inquiry'] . "");
				}
			} else {
				$db->addErrorMessage("Form submission failed Try again!!");
				$db->addErrorMessage("No Customer found to update!!");
				if ($flag == "prospect") {
					$db->rp_location("executive_manage.php?flag=" . $flag);
				} else {
					$db->rp_location("executive_manage.php");
				}
			}
		}
	} else {
		$db->addErrorMessage("Form submission failed Try again!!");
		$db->addErrorMessage("Person name, contact number and type of Customer are compalsary!!");
		if ($flag == "prospect") {
			$db->rp_location("executive_manage.php?flag=" . $flag);
		} else {
			$db->rp_location("executive_manage.php");
		}
	}
} else {

	$db->addErrorMessage("Form submission failed Try later!!");
	if ($flag == "prospect") {
		$db->rp_location("executive_manage.php?flag=" . $flag);
	} else {
		$db->rp_location("executive_manage.php");
	}
}
