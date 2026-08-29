<?php
$page_id = 607;
$page_slug = 'quotation';
$ctable 	= "quotation_detail";
$ctable1 	= "Quotation";
$main_page 	= $ctable;
$page 		= "view_" . $ctable;
$page_title = "View " . $ctable1;
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$classname = "Numbers_Words_Locale_en_IN";
$obj = new $classname;
$admin_type = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
$bid 	= $_REQUEST['quotation_id'];
$quotation_status = $db->rp_getValue("quotation_detail", "status", "id='" . $_REQUEST['quotation_id'] . "' AND isDelete=0");
$customer_id = $db->rp_getValue("quotation_detail", "customer_id", "id='" . $_REQUEST['quotation_id'] . "' AND isDelete=0");
$customer_mail_id = $db->rp_getValue("executive", "email", "id='" . $customer_id . "' AND isDelete=0", 0);
$customer_ccmail_id = $db->rp_getValue("executive", "email_cc", "id='" . $customer_id . "' AND isDelete=0");
$flag_r = $db->rp_getData("page_admin_right", "*", "page_id='" . $page_id . "' AND admin_id='" . $admin_type . "' AND isDelete=0", "", 0);
$flag_d = mysqli_fetch_array($flag_r);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>

	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
	<style type="text/css">
		#wrapper {
			width: 190mm;
			margin: 0 50mm;
		}

		#wrapper {
			width: auto !important;
		}

		#wrapper1 {
			max-width: 980px;
			margin: 0 auto;
			background: #fff;
		}

		#wrapper1 .main-container {
			padding: 20px;
		}

		@media print {
			@page {
				size: A4 portrait;
				margin: 5mm;
			}

			body.page-md {
				background: #fff !important;
				margin: 0 !important;
				padding: 0 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			body.page-md * {
				visibility: hidden;
			}

			#report_content,
			#report_content * {
				visibility: visible;
			}

			#report_content {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
			}

			.transCover,
			.page-header,
			.page-head,
			.page-toolbar,
			.page-container > .page-head,
			footer,
			.modal,
			.toast,
			#lostModal {
				display: none !important;
			}

			.page-container,
			.page-content,
			.page-content .container,
			.page-content .row,
			#report_content,
			#wrapper1,
			#wrapper1 .main-container {
				width: 100% !important;
				max-width: 100% !important;
				margin: 0 !important;
				padding: 0 !important;
				background: #fff !important;
				border: none !important;
				box-shadow: none !important;
			}

			.col-md-12 {
				width: 100% !important;
				padding: 0 !important;
				float: none !important;
			}
		}
	</style>
</head>

<body class="page-md">
	<div class="transCover"><img src="assets/admin/layout/img/89.gif" alt="" style="margin-top:20%;padding-left:48%;"></div>
	<?php include("header.php"); ?>
	<div class="page-container">

		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h2><?php echo $page_title; ?></h2>

				</div>
				<div class="page-toolbar">
					<?php
					/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{*/
					if ($quotation_status == 0) {
						if ($flag_d['approve_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {

					?>
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-success" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','1');" title="Approve">Approve</a>
							</div>
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-danger" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','-2');" title="Disapprove">Disapprove</a>
							</div>
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-danger" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','3');" title="Cancel">Cancel</a>
							</div>
							<!-- <div class="btn-group btn-theme-panel hide-app-dis">
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lostModal">Lost</button>
							</div> -->
							<!-- <div class="btn-group btn-theme-panel hide-app-dis"> -->
							<!-- <a class="btn btn-success" href="javascript:;" onClick="#" title="Print">Send Mail</a> -->
							<!-- <a onclick="sendEmail('<?= $bid; ?>')" class="btn btn-success" title="Send Mail">Send Mail</a>
							</div> -->
					<?php
						}
					}
					/*}*/
					?>

					<?php
					if ($flag_d['print_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
					?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
					<?php
					}
					?>
					<?php
					if ($flag_d['pdf_download_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
					?>
						<!-- <div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genReport('<?php echo $bid; ?>');" title="Download">Download</a>
						</div> -->
					<?php
					}
					?>

					<?php
					if ($quotation_status == 1 || $quotation_status == 4) {
						if ($flag_d['email_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {

					?>
							<!-- <div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div> -->
							<!-- <div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genReport('<?php echo $bid; ?>');" title="Download">Download</a>
						</div> -->
							<!-- <div class="btn-group btn-theme-panel hide-app-dis">
							<a onclick="sendEmail('<?= $bid; ?>')" class="btn btn-success" title="Send Mail">Send Mail</a>
						</div> -->
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-success" href='#SendMail' data-title="Quotation" data-id="<?= $_REQUEST['quotation_id'] ?>" data-mailid="<?= $customer_mail_id ?>" data-ccmailid="<?= $customer_ccmail_id ?>" data-type="quotation" data-toggle='modal'>Send Mail</a>
							</div>
					<?php
						}
					}

					?>

					<!-- direct order -->
					<?php
					$revisedCount = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $_REQUEST['quotation_id'] . "' AND isDelete=0");
					if ($revisedCount <= 0) {
						if ($quotation_status == 1) {
					?>
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a onclick="GenerateOrder('<?= $bid; ?>')" class="btn btn-danger" title="Generate Order">Generate Order</a>
							</div>
					<?php
						}
					}
					?>
					<!-- direct order -->
				</div>
			</div>
		</div>

		<div class="page-content">
			<div class="container">
				<div class="row">

					<div class="col-md-12" id="report_content">
						<div id="wrapper1">
							<?php
							include("quotation_view_new_quotation_new_1.php");
							?>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>


	<div class="modal fade" id="lostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="post" name="lost_form" id="lost_form">
					<div class="modal-header">
						<h4 class="modal-title" id="exampleModalLabel">Reason for Lost</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<label>Reason for Lost</label>
						<textarea rows="4" name="lost_reason" id="lost_reason" style="width:100%;"></textarea>
						<input type="hidden" name="quotation_id" id="quotation_id" value="<?= $bid ?>">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php include("footer.php"); ?>
	<?php include("include_js.php"); ?>
	<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
	<script>
		function genReport(bid) {
			var rc = encodeURIComponent($("#report_content").html());
			$.ajax({
				type: "POST",
				url: "quotation_generate.php",
				data: 'quotation_id=' + bid + '&staic=2',
				beforeSend: function() {
					$(".transCover").fadeIn(800);
				},
				success: function(result) {
					// alert(result);
					setTimeout(function() {
						window.location.href = result;
						$(".transCover").fadeOut(100);
					}, 1500);
				}
			});
		}

		function OrderStatus(qid, status) {
			// alert(qid)
			if (status == 1) {
				var txt = "Approve";
			} else if (status == -2) {
				var txt = "Dispprove";
			} else if (status == 3) {
				var txt = "Cancel";
			}
			var r = confirm("Are You Sure you want to " + txt + " this Quotation??");
			if (r) {
				$.ajax({
					type: "POST",
					// url: "update_order_status.php",
					url: "update_quotation_status.php",
					// data: 'order_id=' + qid + '&status=' + status,
					data: {
						quotation_id: qid,
						status: status
					},
					beforeSend: function() {
						$(".transCover").fadeIn(800);
					},
					success: function(result) {
						var result = $.parseJSON(result);
						if (result.ack == 1) {
							$(".hide-app-dis").addClass("hidden");
							setTimeout(function() {
								$(".transCover").fadeOut(100);
								toastr.success(result.ack_msg);
							}, 1500);
							location.reload();
						} else {
							toastr.error(result.ack_msg);
						}
					}
				});
			}
		}

		function printReport(id) {
			var printWin = window.open('quotation_view_new_quotation_new_1.php?quotation_id=' + id + '&print=1', '_blank');
			if (!printWin) {
				window.print();
			}
		}

		// for mail send
		function sendEmail(id) {
			$.ajax({
				type: "POST",
				url: "generate_email.php",
				data: {
					ref_id: id,
					type: "quotation_detail",
				},
				beforeSend: function() {
					$(".transCover").fadeIn(800);
				},
				success: function(result) {
					var result = $.parseJSON(result);
					if (result.ack == 1) {
						$(".transCover").fadeOut(100);
						toastr.success(result.ack_msg);
					} else {
						toastr.error(result.ack_msg);
					}
				}
			});
		}
		// for mail send
	</script>

	<script type="text/javascript">
		function GenerateOrder(qid) {
			var r = confirm("Are you sure to Generate Order???");
			if (r) {
				$.ajax({
					type: "post",
					url: "ajax_create_order.php",
					data: "qid=" + qid,
					beforeSend: function() {
						$(".transCover").fadeIn(800);
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
					success: function(result) {
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
						result = $.parseJSON(result);
						if (result.ack == 0) {
							toastr.error(result.ack_msg);
						} else {
							toastr.success(result.ack_msg);
							window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id + "&c_type=" + result.c_type;
						}
					}
				})
			}
		}


		$("#lost_form").on("submit", function(e) {
			e.preventDefault();
			// alert('test');
			var request_method = $(this).attr("method"); //get form GET/POST method
			var form_data = $("#lost_form").serialize();
			var lost_reason = $('#lost_reason').val();
			var quotation_id = $('#quotation_id').val();

			// if(error == false) {
			$.ajax({
				url: "lost_reason_ajax.php",
				type: "POST",
				data: $("#lost_form").serialize(),

				beforeSend: function() {
					// $("#loading-modal").modal({backdrop: 'static', keyboard: false});
					// $("#loading-modal").modal('show');
				},
				success: function(result) {
					// setTimeout(function(){
					//     $("#loading-modal").modal('hide');
					//  },1500);
					let jsonData = JSON.parse(result);
					if (jsonData.ack == 1) {
						// toastr.success("We will contact you soon...!");
						$("#lost_form")[0].reset();
						$("#lostModal").modal('hide');
						$("#warning-name").text('');
						$("#warning-email").text('');
						$("#warning-phone").text('');
						$("#warning-subject").text('');
						$("#warning-message").text('');
						// $("#reg_form")[0].reset();
					} else {
						// toastr.error("Something went wrong...");
						// $("#fail-show").text("Something went wrong");                        
					}
				}
			});
			// }

		});
	</script>
</body>

</html>