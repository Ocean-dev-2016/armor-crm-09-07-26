<?php
$page_id=627;$page_slug='dashboard_statical_report_page';
$ctable 	= "customer_leager";
$ctable1 	= "Dashboard Statical Report";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");

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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-md-12 "><br/>
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="">
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
								<div class="row">
									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<label>Select Sales Person</label>
                                        <div class="form-group" role="form">
                                            <select class="form-control" name="account" id="account"  onChange="getCustomer(this.value);">
                                            	<?php
                                            	if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{?>
													<option value="">--- Select Sales Person ---</option>
                                                <?php } ?>
												 <?php 
												 	$whereCustom = "";
													if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
													{
														$whereCustom = "id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' AND ";
													}
													$sales_executive_r=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1 AND ".$whereCustom." type!='service_engineer' AND type!='service_executive'","",0);
													while($sales_executive_d=mysqli_fetch_assoc($sales_executive_r))
													{
														?>
														<option <?=($sales_executive_d['id']==$_SESSION[SITE_SESS.'REFERANCE_ID'])?"selected":"";?> <?php echo ($sales_executive_d==$sales_executive_d['id'])?"selected":"" ; ?>  value="<?php echo $sales_executive_d['id']?>"><?php echo $sales_executive_d['name'];?></option>
														<?php
													}
												?>
											</select>
										</div>
                                    </div>

                                    <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
                                    	<?php //$date=date('d-m-Y'); ?>
                                    	<label>To Date</label>
								      	<div class="form-group">
							               <input type="text" name="ToDate" class="form-control to_date" id="ToDate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off" readonly>
							            </div>
									</div>

									 <div class="col-md-3 col-xs-4 col-sm-3" style="margin-top:10px">
                                    	<?php //$date=date('d-m-Y'); ?>
                                    	<label>From Date</label>
								      	<div class="form-group">
							               <input type="text" name="fromdate" class="form-control to_date" id="fromdate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off" readonly>
							            </div>
									</div>

								    <!-- <div class="col-md-3 col-xs-3 col-sm-3 pull-right" style="margin-top:34px;">		
								    	<?php
											if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
												<button type="button" class="btn btn-success print btn-sm pull-right" name="print" onClick="gendailysalesPrint()" style="background-color: #f0ad4e;color: #fff;" id="print" title="print"><i class="fa fa-print"></i>Print</button>
												<?php
											}
											?>
									</div> -->
									<div class="col-md-3 col-xs-3 col-sm-3 pull-right" style="margin-top:34px;">
											<?php
											if($rights['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
											?>
										<button type="button" class="btn btn-success pdf btn-sm pull-right" name="pdf" onClick="genPdfReport()" style="background-color: #f0ad4e;color: #fff;" id="pdf" title="Download"><i class="fa fa-file-pdf-o"></i>Download</button>
											<?php
											}
											?>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
						<div class="portlet-body">
							<div id="results"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<?php include("footer.php"); ?>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap"></script>
<?php include("include_js.php"); ?>




<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript">
var status="";
var searchName="";
var date="";
var data_url = "dashboard_statical_report_get_ajax.php";

$('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });
$('#fromdate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });

$("#fromdate").change(function(){
   var date = $('#ToDate').val();
   var date1 = $('#fromdate').val();
   var sales_id = $('#account').val();
   callAjax(sales_id,date,date1);
});

function getCustomer(id){
	sales_id=id;
	var date = $('#ToDate').val();
	var date1 = $('#fromdate').val();
	callAjax(sales_id,date,date1);
}
	
	function callAjax(sales_id,date,date1){
			var sales_id = $('#account').val();
			$.ajax({
	        url: data_url,
	        data: {
	            sales_id: sales_id,
	            date: date,
	            date1: date1,
	        },
	        beforeSend: function() {
	            $("#results").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
	        },
	        success: function(result) {
	            $("#results").html(result);
	        }
	    });	
	}
	
</script>


<!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js" integrity="sha256-w6/1B0uwkpR3uX0YUw3k2zzHnq6xDNdVZHLIdz8xV6I=" crossorigin="anonymous"></script> -->

<!-- <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script> -->

<!-- <script src="https://cdn.jsdelivr.net/npm/canvas2image@1.0.5/canvas2image.min.js"></script> -->
		
<!-- <script type="text/javascript">
	document.querySelector('.test').addEventListener('click', function() {
        html2canvas(document.querySelector('#mapd'), {
        	// useCORS: true,
            onrendered: function(canvas) {
                // document.body.appendChild(canvas);
              return Canvas2Image.saveAsPNG(canvas);
            }
        });
    });
</script> -->

<script type="text/javascript">
	
    function gendailysalesPrint(){
		var date = $('#ToDate').val();
		var date1 = $('#fromdate').val();
     	var myWindow = window.open('print_dailysales_ajax.php?sales_id='+ sales_id + "&date=" + date + "&date1=" + date1,'','width=700,height=800');
     	myWindow.print();
    }
    var admintype = "<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE']?>";
    if(admintype!=0)
    {
    	$("#account").change();
    }
    window.onload = function(){
	getCustomer();
};

function genPdfReport()
{
	var date = $('#ToDate').val();
   	var date1 = $('#fromdate').val();
   	var sales_id = $('#account').val();
   	var rc = encodeURIComponent($("#report_content").html());
	$.ajax({
		type: "POST",
		url: "dashboard_statical_report_genpdf.php",
		// data: 'date=' + date +'&date1=' + date1  +'&sales_id=' + sales_id,
		data: 'rc=' + rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result) {
			// alert(result);
			setTimeout(function() {
				// window.location.href = result;
				window.open( result , '_blank');
				$(".transCover").fadeOut(100);
			}, 1500);
		}
	});
}

</script>

</body>
</html>