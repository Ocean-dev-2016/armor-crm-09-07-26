<?php
$page_id=565;$page_slug='page_order';
$ctable 	= "order";
$ctable1 	= "Sales Officer Order";
$main_page 	= $ctable;
$page 		= "view_".$ctable;
$page_title = "View ".$ctable1;
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$classname = "Numbers_Words_Locale_en_IN" ;
$obj = new $classname; 
$admin_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
$flag_r=$db->rp_getData("page_admin_right","*","page_id='".$page_id."' AND admin_id='".$admin_type."' AND isDelete=0","",0);
$flag_d = mysqli_fetch_array($flag_r);

$bid 	= $_REQUEST['order_id'];
$order_status=$db->rp_getValue("orders","status","id='".$_REQUEST['order_id']."' AND isDelete=0");
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
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=11)
				{ 
					if($flag_d['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
						<?php
					}
				} 
				?>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12" id="report_content">
					<div id="wrapper1">
						<?php 
						include("view_pending_order.php");
						?>
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

function printReport(id) 
{	
	var myWindow =  window.open('pending_order_print.php?order_id='+id+"&p=1",'','width=500,height=800');
	setTimeout(function () 
	{
		myWindow.print();
		var ival = setInterval(function() 
		{
		    myWindow.close();
		    clearInterval(ival);
		}, 500);
	}, 1500);
}
</script>
</body>
</html>