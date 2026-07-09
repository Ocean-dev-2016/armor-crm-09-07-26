<?php
$page_id = 555;
$page_slug = 'page_executive';

$ctable 	= "executive";
$ctable1 	= "Import Customer";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy = array(array("link" => "", "title" => "Sales & Marketing"), array("link" => "import_customer_manage.php", "title" => $page_title), array("link" => "import_customer_manage.php", "title" => $ctable1));
include("connect.php");

if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
	$customer_flag = 1;
} else {
	$customer_flag = 0;
}
function removeBlankArrays($array)
{
	foreach ($array as $key => $value) {
		// var_dump($value);
		if (is_array($value)) {
			$array[$key] = removeBlankArrays($value); // Recursively clean nested arrays
			if (empty($array[$key])) {
				unset($array[$key]); // Remove empty subarrays
			}
		} else {
			if (trim($value) === '' || $value === null || $value === NULL) {
				unset($array[$key]); // Remove empty or null values
			}
		}
	}
	return $array;
}

// echo "hello";exit;
if (isset($_POST['submit'])) {
	if (isset($_FILES['excel_upload'])) {
		$Fail = false;
		$file = $_FILES['excel_upload'];

		$TempFile = $file['tmp_name'];
		$FileName = $file['name'];
		$FileType = $file['type'];
		$FileError = $file['error'];
		$FileSize = $file['size'];
		if ($FileError == 0) {
			if ($FileType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $FileType == 'application/vnd.ms-excel') {
				if ($FileSize <= 1024 * 1024 * 4) // 2MB
				{
					$UploadName1 = "customer-upload-" . date("d-m-Y-h-i-s") . "-" . $FileName;
					$UploadURL1 = "sheet_import/uploads/customer/" . $UploadName1;
					move_uploaded_file($TempFile, $UploadURL1);
					include "PHPExcel/IOFactory.php";
					try {
						$objPHPExcel = PHPExcel_IOFactory::load($UploadURL1);
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
						ob_end_clean();
						// Remove blank arrays
						$resultArray = removeBlankArrays($allDataInSheet);
						$arrayCount 	= count($resultArray);  // Here get total count of row in that Excel sheet
						$Member = 0;
						$Numbers = array();
						$SkippedArray = array();

						if ($arrayCount > 1) {
							// echo "hellooooo";exit;
							if (($allDataInSheet[1]["A"]) != "Customer Type" ||
								($allDataInSheet[1]["B"]) != "Company Name" ||
								($allDataInSheet[1]["C"]) != "Prise List" ||
								($allDataInSheet[1]["D"]) != "Sales Executive" ||
								($allDataInSheet[1]["E"]) != "Firm Name" ||
								($allDataInSheet[1]["F"]) != "Person Name" ||
								($allDataInSheet[1]["G"]) != "turnover" ||
								($allDataInSheet[1]["H"]) != "turnover_year" ||
								($allDataInSheet[1]["I"]) != "Email" ||
								($allDataInSheet[1]["J"]) != "Phone" ||
								($allDataInSheet[1]["K"]) != "Mobile" ||
								($allDataInSheet[1]["L"]) != "Address Line 1" ||
								($allDataInSheet[1]["M"]) != "Country" ||
								($allDataInSheet[1]["N"]) != "State" ||
								($allDataInSheet[1]["O"]) != "City" ||
								($allDataInSheet[1]["P"]) != "Route" ||
								($allDataInSheet[1]["Q"]) != "Zone" ||
								($allDataInSheet[1]["R"]) != "Category" ||
								($allDataInSheet[1]["S"]) != "Shipping Address" ||
								($allDataInSheet[1]["T"]) != "Billing Address" ||
								($allDataInSheet[1]["U"]) != "Company Type" ||
								($allDataInSheet[1]["V"]) != "GST" ||
								($allDataInSheet[1]["W"]) != "Pan Number" ||
								($allDataInSheet[1]["X"]) != "Longitude" ||
								($allDataInSheet[1]["Y"]) != "Latitude" ||
								($allDataInSheet[1]["Z"]) != "Whatsapp" ||
								($allDataInSheet[1]["AA"]) != "CC Email" ||
								($allDataInSheet[1]["AB"]) != "Address Line 2" ||
								($allDataInSheet[1]["AC"]) != "Pincode" ||
								($allDataInSheet[1]["AD"]) != "Transport By" ||
								($allDataInSheet[1]["AE"]) != "Remark" ||
								($allDataInSheet[1]["AF"]) != "Transporter Detail" ||
								($allDataInSheet[1]["AG"]) != "Booking Place" ||
								($allDataInSheet[1]["AH"]) != "Type of Industry" ||
								($allDataInSheet[1]["AI"]) != "Purchasing From" ||
								($allDataInSheet[1]["AJ"]) != "Openinig BalanceType" ||
								($allDataInSheet[1]["AK"]) != "Openinig Balance" ||

								($allDataInSheet[1]["AL"]) != "Mobile No1" ||
								($allDataInSheet[1]["AM"]) != "Name1" ||
								($allDataInSheet[1]["AN"]) != "Mobile No2" ||
								($allDataInSheet[1]["AO"]) != "Name2" ||
								($allDataInSheet[1]["AP"]) != "Mobile No3" ||
								($allDataInSheet[1]["AQ"]) != "Name3" ||
								($allDataInSheet[1]["AR"]) != "Mobile No4" ||
								($allDataInSheet[1]["AS"]) != "Name4" ||
								($allDataInSheet[1]["AT"]) != "client_code"
							) {
								$Fail = false;
								// $db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								// throw new Exception();
							}
						}
						for ($i = 2; $i <= $arrayCount; $i++) {
							$customer_type 			= $db->clean($allDataInSheet[$i]["A"]);
							$type_of_company 		= $db->clean($allDataInSheet[$i]["B"]);
							$price_list_id	   		= $db->clean($allDataInSheet[$i]["C"]);
							$seid 					= $db->clean($allDataInSheet[$i]["D"]);
							$company_name 			= $db->clean($allDataInSheet[$i]["E"]);
							$cname     				= $db->clean($allDataInSheet[$i]["F"]);
							$turnover     				= $db->clean($allDataInSheet[$i]["G"]);
							$turnover_year     				= $db->clean($allDataInSheet[$i]["H"]);
							$email  				= $db->clean($allDataInSheet[$i]["I"]);
							$phone     				= $db->clean($allDataInSheet[$i]["J"]);
							$mobile_no1         	= $db->clean($allDataInSheet[$i]["K"]);
							$address 				= $db->clean($allDataInSheet[$i]["L"]);
							$country 				= $db->clean($allDataInSheet[$i]["M"]);
							$state  				= $db->clean($allDataInSheet[$i]["N"]);
							$city  					= $db->clean($allDataInSheet[$i]["O"]);
							$route  				= $db->clean($allDataInSheet[$i]["P"]);
							$zone  					= $db->clean($allDataInSheet[$i]["Q"]);
							$top_category_id  		= $db->clean($allDataInSheet[$i]["R"]);
							$shipping_address 		= $db->clean($allDataInSheet[$i]["S"]);
							$billing_address 		= $db->clean($allDataInSheet[$i]["T"]);
							$company_type       	= $db->clean($allDataInSheet[$i]["U"]);
							$gst 					= $db->clean($allDataInSheet[$i]["V"]);
							$pan 					= $db->clean($allDataInSheet[$i]["W"]);
							$longitude 				= $db->clean($allDataInSheet[$i]["X"]);
							$latitude  				= $db->clean($allDataInSheet[$i]["Y"]);
							$whatsapp_no        	= $db->clean($allDataInSheet[$i]["Z"]);
							$email_cc 				= $db->clean($allDataInSheet[$i]["AA"]);
							$address2 				= $db->clean($allDataInSheet[$i]["AB"]);
							$pincode 				= $db->clean($allDataInSheet[$i]["AC"]);
							$transport_by 			= $db->clean($allDataInSheet[$i]["AD"]);
							$remark 				= $db->clean($allDataInSheet[$i]["AE"]);
							$transport_detail 		= $db->clean($allDataInSheet[$i]["AF"]);
							$booking_place 			= $db->clean($allDataInSheet[$i]["AG"]);
							$type_of_industry 		= $db->clean($allDataInSheet[$i]["AH"]);
							$purchasing_from 		= $db->clean($allDataInSheet[$i]["AI"]);
							$openinig_balance_type	= $db->clean($allDataInSheet[$i]["AJ"]);
							$openinig_balance 		= $db->clean($allDataInSheet[$i]["AK"]);
							$mobile_no1A 		= $db->clean($allDataInSheet[$i]["AL"]);
							$name1 				= $db->clean($allDataInSheet[$i]["AM"]);
							$mobile_no2 		= $db->clean($allDataInSheet[$i]["AN"]);
							$name2 				= $db->clean($allDataInSheet[$i]["AO"]);
							$mobile_no3 		= $db->clean($allDataInSheet[$i]["AP"]);
							$name3 				= $db->clean($allDataInSheet[$i]["AQ"]);
							$mobile_no4 		= $db->clean($allDataInSheet[$i]["AR"]);
							$name4 				= $db->clean($allDataInSheet[$i]["AS"]);
							$client_code 				= $db->clean($allDataInSheet[$i]["AT"]);

							$customer_type = $customer_type ? $customer_type : '';

							$phone_arr = array();
							$name_arr = array();
							if ($mobile_no1A != "") {
								$phone_arr[] = $mobile_no1A;
							}
							if ($mobile_no2 != "") {
								$phone_arr[] = $mobile_no2;
							}
							if ($mobile_no3 != "") {
								$phone_arr[] = $mobile_no3;
							}
							if ($mobile_no4 != "") {
								$phone_arr[] = $mobile_no4;
							}


							if ($name1 != "") {
								$name_arr[] = $name1;
							}
							if ($name2 != "") {
								$name_arr[] = $name2;
							}
							if ($name3 != "") {
								$name_arr[] = $name3;
							}
							if ($name4 != "") {
								$name_arr[] = $name4;
							}
							$phone_arr = implode(",", $phone_arr);
							$name_arr = implode(",", $name_arr);


							$seid_arr = explode(",", $seid);

							$sales_executive_id = array();
							for ($se = 0; $se < sizeof($seid_arr); $se++) {
								$sales_executive_id[] = $db->rp_getValue("sales_executive", "id", "name='" . $seid_arr[$se] . "'");
							}
							$sales_executive_str = implode(",", $sales_executive_id);


							$top_category_id = array();
							$top_category_id_r = $db->rp_getData("top_category_master", "id", "isDelete=0 AND isActive=1", "", 0);
							while ($top_category_id_d = mysqli_fetch_assoc($top_category_id_r)) {
								$top_category_id[] =	$top_category_id_d['id'];
							}

							$top_category_arr = implode(",", $top_category_id);

							$state_id   = $db->rp_getValue("state", "id", "LOWER(name)='" . strtolower($state) . "'");

							$city_id    = $db->rp_getValue("city", "id", "LOWER(name)='" . strtolower($city) . "'");

							$zone_id    = $db->rp_getValue("zone", "id", "LOWER(name)='" . strtolower($zone) . "'", 0);
							$route_id    = $db->rp_getValue("area", "id", "LOWER(name)='" . strtolower($route) . "'");

							$company_type_id = $db->rp_getValue("company_type", "id", "LOWER(name)='" . strtolower($company_type) . "'", 0);

							$type_of_company = $db->rp_getValue("company_master", "id", "LOWER(name)='" . strtolower($type_of_company) . "'", 0);

							$price_list_id_r = $db->rp_getValue("price_list", "id", "LOWER(pricelist_name)='" . strtolower($price_list_id) . "'", 0);


							// code for client code // 
							if (empty($client_code)) {
								$client_code_prefix = $db->rp_getValue("company_master", "prefix", "id='" . $type_of_company . "' AND isDelete=0", 0);
								$lastInsertIds = $db->rp_getValue("executive", "MAX(`client_code_sr_by_type`)", "type_of_company='" . $type_of_company . "' AND isDelete=0", 0);

								$code = str_pad(($lastInsertIds + 1), 4, '0', STR_PAD_LEFT);
								$client_code = $client_code_prefix . ($code);
							} else {
								$code = "";
							}

							// code for client code // 

							if ($transport_by != "" && $transport_by != NULL) {
								$transport_by = $db->rp_getValue("transport_by", "id", "name = '" . $transport_by . "'");
								if ($transport_by == "" || $transport_by == NULL) {
									$transport_by = "";
								}
							} else {
								$transport_by = "";
							}

							if ($transport_detail != "" && $transport_by != "") {
								$transport_detail_id = $db->rp_getValue("transport_master", "id", "name = '" . $transport_detail . "' AND transport_by_id = '" . $transport_by . "' ", 0);
								if ($transport_detail_id != "") {
									$transport_detail = $transport_detail_id;
								} else {
									$transport_detail = "";
								}
							} else {
								$transport_detail = "";
							}

							if ($type_of_industry != "") {
								$type_of_industry = $db->rp_getValue("industry_type", "id", "isDelete = 0 AND isActive = 1 AND name = '" . $type_of_industry . "'");
								if ($type_of_industry == "" || $type_of_industry == NULL) {
									$type_of_industry = "";
								}
							} else {
								$type_of_industry = "";
							}

							if ($openinig_balance_type != "") {
								if (strtolower($openinig_balance_type) == "credit") {
									$openinig_balance_type = "1";
								} else if (strtolower($openinig_balance_type) == "debit") {
									$openinig_balance_type = "2";
								} else {
									$openinig_balance_type = "";
								}
							} else {
								$openinig_balance_type = "";
							}

							$dup_where = " (mobile_no1 = '" . $mobile_no1 . "' OR client_code = '" . $client_code . "') AND company_name = '" . $company_name . "' AND isDelete=0";
							$IsDuplicateGroupName = $db->rp_getTotalRecord("executive", $dup_where, 0);

							if ($company_name == "" || $cname == "" || $country == "" || $state == "" || $city == "" || $customer_type == "") {
								$SkippedArray[$i] = "Mandetory Field are blank " . $db->clean($allDataInSheet[$i]["E"]) . " - " . $db->clean($allDataInSheet[$i]["F"]);
							} else if ($IsDuplicateGroupName > 0) {
								$SkippedArray[$i] = "Duplicate " . $db->clean($allDataInSheet[$i]["E"]) . " - " . $db->clean($allDataInSheet[$i]["F"]);
							} else {
								$MemberID = $db->rp_insert(
									"executive",
									array(
										$type_of_company,
										$price_list_id_r ? $price_list_id_r : "",
										$sales_executive_str ? $sales_executive_str : "",
										$company_name,
										$cname,
										$email,
										$phone,
										$mobile_no1,
										$address,
										$country,
										$state,
										$city,
										$route,
										$zone_id ? $zone_id : "",
										$top_category_arr,
										$shipping_address ? $shipping_address : "",
										$billing_address ? $billing_address : "",
										$company_type_id ? $company_type_id : "",
										$gst,
										$pan,
										$longitude,
										$latitude,
										$whatsapp_no,
										$email_cc,
										$address2,
										$state_id,
										$city_id,
										$client_code ? $client_code : "",
										$code,
										$customer_type,

										$pincode,
										$transport_by,
										$remark,

										$transport_detail,
										$booking_place,
										$type_of_industry,

										$purchasing_from,
										$openinig_balance_type,
										$openinig_balance,
										$customer_flag,
										$turnover,
										$turnover_year,
									),
									array(
										"type_of_company",
										"price_list_id",
										"seid",
										"company_name",
										"cname",
										"email",
										"phone",
										"mobile_no1",
										"address",
										"country",
										"state",
										"main_city",
										"city",
										"zone",
										"top_category_id",
										"shipping_address",
										"billing_address",
										"company_type",
										"gst",
										"pan",
										"longitude",
										"latitude",
										"whatsapp_no",
										"email_cc",
										"address2",
										"class_id",
										"area_id",
										"client_code",
										"client_code_sr_by_type",
										"type_of_executive",
										"zip",
										"transport_by_id",
										"remark",
										"transporter_id",
										"booking_place",
										"industry_type_id",
										"purchasing_from",
										"credit_debit_type",
										"openinig_balance",
										"customer_flag",
										"turnover",
										"turnover_year",
									),
									0
								);

								if ($MemberID > 0) {
									$shipping_add = explode(",", $shipping_address);

									for ($sp = 0; $sp < sizeof($shipping_add); $sp++) {
										$item_rows = array("customer_id", "shipping_address");
										$item_values = array($MemberID, addslashes(html_entity_decode($shipping_add[$sp])));
										$item_id = $db->rp_insert("customer_vs_shipping_address", $item_values, $item_rows, 0);
									}

									if ($city != "" && !empty($city)) {
										$class_id = $db->rp_getValue("class", "id", "name LIKE '%" . strtolower(trim($state)) . "%'", 0);
										$area_id = $db->rp_getValue("area", "id", "name LIKE '%" . strtolower(trim($city)) . "%'", 0);
										$city_id = $db->rp_getValue("city", "id", "name LIKE '%" . strtolower(trim($main_city)) . "%'", 0);
										if ($area_id == "") {
											$area_id = $db->rp_insert("area", array($city, $class_id, $city, 1), array("name", "class_id", "area_slug", "isActive"), 0);
										}

										$mapping_id = $db->rp_insert("executive_map_area", array($MemberID, $customer_type, $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
									}
									if (!empty($phone_arr)) {
										$phn_no = explode(",", $phone_arr);
										$name_d = explode(",", $name_arr);

										for ($phn = 0; $phn < sizeof($phn_no); $phn++) {
											$item_rows = array("customer_id", "phone_no", "name", "ref_table");
											$item_values = array($MemberID, $phn_no[$phn], $name_d[$phn], "executive");
											$item_id = $db->rp_insert("customer_vs_phone_no", $item_values, $item_rows, 0);
										}
									}
									$Member++;
								} else {
									$SkippedArray[$i] = "Not Inserted " . $db->clean($allDataInSheet[$i]["E"]) . " - " . $db->clean($allDataInSheet[$i]["F"]);
								}
							}
						}
						$Skipped = ($arrayCount - 1) - $Member;
						$SkipMessage = "";
						/*if($Skipped>0)
						{*/
						$SkipMessage = "Total <b>" . $Skipped . "</b> Row(s) Not Inserted And Total Update <b>" . $Member . "</b> Row(s)";
						if (sizeof($SkippedArray) > 0) {
							$arrayCount = strlen((string)$arrayCount);

							$SkipMessage .= "<br/>";
							$SkipMessage .= "<br/>";
							$SkipMessage .= " ***Not Added List*** ";
							$SkipMessage .= "<br/> ";
							foreach ($SkippedArray as $key => $value) {
								$key = sprintf("%0" . $arrayCount . "d", $key);
								$SkipMessage .= "Row " . $key . " - " . $value;
								$SkipMessage .= "<br/>";
							}
						}
						// skipped rows
						// total update count
						/*}*/
						if ($Skipped > 0) {
							$db->addErrorMessage($SkipMessage);
						} else {
							$db->addSuccessMessage("Inquiry Upload Successfully");
						}
					} catch (Exception $e) {
						$Fail = true;
						$db->addErrorMessage("File not supported to upload.");
					}
				} else {
					$Fail = true;
					$db->addErrorMessage("Filesize must be less than 2 MB.");
				}
			} else {
				$Fail = true;
				$db->addErrorMessage("File type must be xls or xlsx.");
			}
		} else {
			$Fail = true;
			$db->addErrorMessage("File corrupted or not uploaded try again.");
		}

		if ($Fail) {
			$db->rp_delete($ctable, "id='" . $GroupID . "'");
		}
	} else {
		$db->addErrorMessage("excel file required.");
	}

	$db->rp_location("import_customer_manage.php?mode=add");
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
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
</head>

<body class="page-md">
	<?php include("header.php"); ?>
	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h1><a href="<?php echo "executive_manage.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
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
				<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-6 ">
							<div class="portlet box blue">
								<div class="portlet-body form">
									<div class="form-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label>Excel File<code>*</code></label>
													<input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB" data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file" name="excel_upload" id="excel_upload" data-validation="required">
													<br>
													<a download href="../customer_import_new.xlsx" type="button" class="btn btn-success btn-sm" style="background-color: green;"><i class="fa fa-download"></i> Download Sample Excel </a>
													<br>
													<br>
													<ul>
														<li>
															<b><code>Customer Type</code></b>
															<ul>
																<!-- <li>HTML</li>
																<li>CSS
																	<ul>
																		<li>onsubmit Attribute</li>
																		<li>onclick Attribute</li>
																	</ul>
																</li>
																<li>JavaScript</li> -->
																<li>Government Office = <code>4</code></li>
																<!-- <li>Trader = <code>6</code></li> -->
																<li>Customer = <code>7</code></li>
																<li>MEP Consultant = <code>9</code></li>
																<li>Builder = <code>10</code></li>
																<li>Brand Approval Visit = <code>11</code></li>
															</ul>
														</li>
														<!-- <li>
															<b>Library/Framework</b>
															<ul>
																<li>ReactJS
																	<ul>
																		<li>Hoisting</li>
																		<li>Props</li>
																	</ul>
																</li>
															</ul>
														</li> -->
													</ul>
												</div>
											</div>
										</div>
									</div>
									<div class="form-actions">
										<button type="submit" name="submit" class="btn green submit_form">Submit</button>
										<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php include("footer.php"); ?>
	<?php include("include_js.php"); ?>

	<script type="text/javascript">
		$(".form-control").bind("keyup change", function() {
			if ($(this).parent().hasClass("has-error")) {
				$(this).parent().removeClass("has-error");
				$(this).parent().find('p.help-block').html("");
			}
		});

		function check_form() {
			$(".form-body").children().removeClass("has-error");
			var isValid = true;

			if ($("#excel_upload").val() == "" || $("#excel_upload").val().split(" ").join("") == "") {

				vd = aj.error('excel_upload', "Please Select File.", "add_error");
				isValid = false;
			}
			if (isValid) {
				return true;
			} else {
				return false;
			}
		}
	</script>

	<script type="text/javascript">
		$(".submit_form").on('click', function() {
			$("#loading-modal").modal("show");
		})
	</script>

	<script type="text/javascript">
		$(function() {
			aj.imageHolder($("input[name=image_path]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValidT = isImageThumbnailValidReply;
					//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				},
				["png", "PNG", "jpeg", "JPEG", "jpg", "JPG", "gif", "GIF"]
			);
		})
	</script>
</body>

</html>