<?php
$page_id = 656;
$page_slug = 'company_master';
$ctable 	= "company_master";
$ctable1 	= "Company Master";
$main_page 	= "product_mgmt";
$page 		= "manage_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
$page_hierarchy = array(array("link" => "", "title" => "Master"), array("link" => "weight_manage.php", "title" => "Manage " . $ctable1), array("link" => $ctable1 . "_crud.php", "title" => "Add/Edit " . $ctable1));
include("connect.php");
require_once("../include/class.company_master.php");
$objCompany = new Company();
$name			= "";
$code			= "";
if (isset($_REQUEST['submit'])) {
	// print_r($_FILES);
	// exit;
	$detail['name']					= $db->clean($_REQUEST['name']);
	$detail['gst']					= $db->clean($_REQUEST['gst']);
	$detail['india_mart_api_key']   = ($_REQUEST['india_mart_api_key']) ? $db->clean($_REQUEST['india_mart_api_key']) : "";
	$detail['pan_crad']				= $db->clean($_REQUEST['pan_crad']);
	$detail['image_path']         	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']      	= $db->clean($_REQUEST['old_image_path']);
	$detail['footer_image_path']         	= $db->clean($_REQUEST['footer_image_path']);
	$detail['old_footer_image_path']      	= $db->clean($_REQUEST['old_footer_image_path']);
	$detail['address']      		= str_replace('rn', '', $_REQUEST['address']);
	$detail['bank_details']      	= str_replace('rn', '', $_REQUEST['bank_details']);
	$detail['trems_and_condition']  = str_replace('rn', '', $_REQUEST['trems_and_condition']);
	$detail['isDelete']		= 0;
	$detail['prefix']				= $db->clean($_REQUEST['prefix']);

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {
		// print_r($_FILES);exit;
		if ($rights['insert_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply = $objCompany->InsertCompany($detail, $_FILES);
		if ($reply['ack'] == 1) {
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable . "_manage.php?msg=inserted");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	} else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") {
		if ($rights['update_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply = $objCompany->UpdateCompany($detail, $_FILES);
		if ($reply['ack'] == 1) {
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable . "_manage.php?msg=updated");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
	if ($rights['update_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$where = " id='" . $_REQUEST['id'] . "' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable, "*", $where);
	$detail['id'] = $_REQUEST['id'];
	$reply = $objCompany->GetEditDataCompany($detail);
	if ($reply['ack'] == 1) {
		//$SuccessMsg = $reply['ack_msg'];
		$result = $reply['result'];
		//print_r($result);
		extract($result);
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") {
	if ($rights['delete_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id'] = $_REQUEST['id'];
	$reply = $objCompany->DeleteCompany($detail);
	if ($reply['ack'] == 1) {
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable . "_manage.php?msg=inserted");
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "isActive" && isset($_REQUEST['status'])  && $_REQUEST['status'] != "") {
	$status = $_REQUEST['status'];
	$rows 	= array(
		"isActive"	=> $status
	);
	$where	= "id='" . $_REQUEST['id'] . "'";
	$db->rp_update($ctable, $rows, $where);
	$db->rp_location($ctable . "_manage.php?msg=updated");
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
	<style type="text/css">
		label {
			font-weight: bold;
		}
	</style>
</head>

<body class="page-md">
	<?php include("header.php"); ?>
	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h1><a href="<?php echo $ctable . "_manage.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
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
				<!-- <form role="form" action="" onSubmit="return check_form();" method="post"> -->
				<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data" id="companyImage">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet box blue">
								<div class="portlet-body form">
									<div class="col-md-6 ">
										<div class="form-body">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Company Name <code>*</code></label>
														<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>">
														<p class="help-block"></p>
													</div>
													<div class="form-group">
														<label>Prefix<code>*</code></label>
														<input type="text" class="form-control" name="prefix" id="prefix" value="<?php echo $prefix; ?>">
														<p class="help-block"></p>
													</div>
													<div class="form-group">
														<label>Pan Card</label>
														<input type="text" class="form-control" name="pan_crad" id="pan_crad" value="<?php echo $pan_crad; ?>">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>GST</label>
														<input type="text" class="form-control" name="gst" id="gst" value="<?php echo $gst; ?>" maxlength="15">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Indiamart APi Key</label>
														<input type="text" class="form-control" name="india_mart_api_key" id="india_mart_api_key" value="<?php echo $india_mart_api_key; ?>">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label>Header Image (933 X 184)</label>
														<input data-image="<?php echo ($image_path != "" && file_exists(HEADER_A . $image_path)) ? HEADER_A . $image_path : ""; ?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="">
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label>Footer Image (943 X 103)</label>
														<input data-image="<?php echo ($footer_image_path != "" && file_exists(HEADER_A . $footer_image_path)) ? HEADER_A . $footer_image_path : ""; ?>" type="file" name="footer_image_path" id="footer_image_path" data-old-image-dom="old_footer_image_path" data-old-footer_image_path="<?php echo $footer_image_path ?>" value="">
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label>Address</label>
														<textarea name="address" id="address">
	                                                <?php echo html_entity_decode($address); ?>
	                                            </textarea>

													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6 ">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label>Bank Details</label>
														<textarea name="bank_details" id="bank_details">
													<?php
													if ($_REQUEST['mode'] == "add") {
													?>
														   	Bank Name :   
													   	 	<br>
														   	Bank Account No :  
													   	 	<br>
														   	Bank IFSC Code :  
													   	 	<br>
														   	Bank Branch : 
														<?php
													}
														?>
	                                                <?php echo html_entity_decode($bank_details); ?>
	                                            </textarea>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group">
														<label>Terms And Condition</label>
														<textarea name="trems_and_condition" id="trems_and_condition">
	                                                <?php echo html_entity_decode($trems_and_condition); ?>
	                                            </textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-actions">
										<button type="submit" name="submit" class="btn green">Submit</button>
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
	<script src="js/ckeditor/ckeditor.js" type="text/javascript"></script>>
	<script type="text/javascript">
		$("#checkAll").change(function() {
			$(".md-check").prop('checked', $(this).prop("checked"));
		});
	</script>
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

			if ($("#name").val() == "" || $("#name").val().split(" ").join("") == "") {

				vd = aj.error('name', "Please Enter Company Name.", "add_error");
				isValid = false;
			}
			if ($("#prefix").val() == "" || $("#prefix").val().split(" ").join("") == "") {

				vd = aj.error('prefix', "Please Enter Company Prefix.", "add_error");
				isValid = false;
			}
			if (isValid) {
				return true;
			} else {
				return false;
			}

		}


		var isImageThumbnailLoaded = false;
		var isImageThumbnailValid = false;

		$(function() {
			// aj.imageHolder($("input[name=image_path]"), "<?= HEADER_IMAGE_WIDTH; ?>", "<?= HEADER_IMAGE_HEIGHT ?>",
			aj.imageHolder($("input[name=image_path]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValid = isImageThumbnailValidReply;
					toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValid = isImageThumbnailValidReply;
					toastr.success("Selected File Dimension: " + image_width + " X " + image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				}
			);


		});

		var isImageThumbnailLoaded = false;
		var isImageThumbnailValid = false;

		$(function() {
			// aj.imageHolder($("input[name=footer_image_path]"), "<?= FOOTER_IMAGE_WIDTH; ?>", "<?= FOOTER_IMAGE_HEIGHT ?>",
			aj.imageHolder($("input[name=footer_image_path]"), "", "",
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValid = isImageThumbnailValidReply;
					toastr.success("Old Image Found!!");
				},
				function(file, img) {
					if (!file) {
						toastr.error("File may be corrupted or missing. Try again!!");
					}
				},
				function(isImageThumbnailLoadedReply, isImageThumbnailValidReply, image_width, image_height) {
					isImageThumbnailLoaded = isImageThumbnailLoadedReply;
					isImageThumbnailValid = isImageThumbnailValidReply;
					toastr.success("Selected File Dimension: " + image_width + " X " + image_height);
				},
				function(data) {
					isImageThumbnailLoadedReply
				}
			);


		});

		$("#companyImage").submit(function(e) {
			var isValid = true;
			var form = this;
			// if (!isImageThumbnailValid) {
			// 	toastr.error("Please Select Valid Size Image !!", "error");
			// 	isValid = false;
			// }
			return isValid;
		});

		$(document).ready(function() {
			CKEDITOR.replace('address');
			CKEDITOR.replace('bank_details');
			CKEDITOR.replace('trems_and_condition');
			CKEDITOR.instances.description.getData().replace(/(\r\n|\n|\r)/gm, "");
		});
	</script>
</body>

</html>