<?php
$page_id=546;$page_slug='view_purchase_order_page';
$ctable 	= "order";
$ctable1 	= "ProForma Invoice";
$main_page 	= $ctable;
$page 		= "view_".$ctable;
$page_title = "View ".$ctable1;
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$classname = "Numbers_Words_Locale_en_IN" ;
$obj = new $classname; 

$bid 	= $_REQUEST['pro_forma_invoice_id'];
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<style type="text/css">
	 #wrapper
	{
	    width:190mm;
	    margin:0 50mm;
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
					<div class="btn-group btn-theme-panel">
						<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
					</div>
					<div class="btn-group btn-theme-panel">
						<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genReport('<?php echo $bid; ?>');" title="Download">Download</a>
					</div>
					
				</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				
				<div class="col-md-12" id="report_content">
					<div id="wrapper">
							<?php include("formate.php");?>
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
		url: "view_pro_forma_invoice.php",
		data: 'pro_forma_invoice_id='+bid,
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
	//var myWindow =  window.open('formate.php?pro_forma_invoice_id='+id+"&p=1",'','width=800,height=800');
	//  myWindow.print();

	  var myWindow = window.open('','','width=800,height=900')
	  myWindow.document.write($("#wrapper").html());
	  myWindow.print();
}
</script>
</body>
</html>