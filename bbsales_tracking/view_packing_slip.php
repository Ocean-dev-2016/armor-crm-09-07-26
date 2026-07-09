<?php
$page_id    = 612;
$page_slug  = 'packing_slip';
$ctable     = "packing_slip";
$page_title = "Packing Slip";
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$admin_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
$flag_r=$db->rp_getData("page_admin_right","*","page_id='".$page_id."' AND admin_id='".$admin_type."' AND isDelete=0","",0);
$flag_d = mysqli_fetch_array($flag_r);

$bid 	= $_REQUEST['id'];
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<!-- <style type="text/css">
	 #wrapper
	{
	    width:190mm;
	    margin:0 64mm;
	}
</style> -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<style type="text/css">
	 #wrapper
	{
	    width:190mm;
	    /*margin:0 50mm;*/
	}
	#wrapper {
   		 width: auto!important;
   	}
</style>
</head>
<body class="page-md">
<div class="transCover"><img src="assets/admin/layout/img/89.gif" alt="" style="margin-top:20%;padding-left:48%;" ></div>
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h2><?php echo $page_title;?></h2>
				
			</div>
			<div class="page-toolbar">
				<?php
					if($flag_d['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
						<?php
					}
					if($flag_d['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{ 
						?>
						<!-- <div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genReport('<?php echo $bid; ?>');" title="Download">Download</a>
						</div> -->
						<?php
					}
					?>
					<!-- <div class="btn-group btn-theme-panel hide-app-dis">
						<a onclick="sendEmail('<?= $bid; ?>')" class="btn btn-success" title="Send Mail">Send Mail</a>
					</div> -->
				</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				
				<div class="col-md-12" id="report_content">
					<div id="wrapper">
						<?php //include("packing_slip_format.php");?>
						<?php include("packing_slip_format_new.php");?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script>
function genReport(bid){
	var rc = encodeURIComponent($("#report_content").html());
	$.ajax({
		type: "POST",
		url: "packing_slip_generate.php",
		data: 'id='+bid+'&rc='+rc+'&staic=2',
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
			setTimeout(function(){
				window.location.href=result;
				$(".transCover").fadeOut(100);				
			},1500);
		}
	});
}

function printReport(id) 
{	
	var myWindow = window.open('','','width=800,height=900')
	myWindow.document.write($("#report_content").html());
	/*setTimeout(function () 
	{
		var ival = setInterval(function() 
		{
		    myWindow.close();
		    clearInterval(ival);
		}, 1000);
	}, 1000);*/
}

// for mail send
function sendEmail(id)
{
	$.ajax({
		type: "POST",
		url: "generate_email.php",
		data: {
			ref_id: id,
			type: "dispatch_detail",
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
</body>
</html>
?>