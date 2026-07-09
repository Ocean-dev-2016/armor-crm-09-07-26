<?php
$page_id=630;$page_slug='to_do_list';
$ctable 	= "customer_leager";
$ctable1 	= "To Do List";
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
                                            <select class="form-control" name="sales_id" id="sales_id">
												<option value="">--- Select Sales Person ---</option>
                                                 
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

                                    <div class="col-md-5 col-xs-3 col-sm-3 pull-right" style="margin-top:34px;">		
								    	<?php
								    		if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
												<button type="button" class="btn btn-success print btn-sm pull-right" name="print" onClick="TodolistPrint()" style="background-color: #f0ad4e;color: #fff;" id="print" title="print"><i class="fa fa-print"></i>Print</button>
												<?php
											}
											?>

											<?php
											if($rights['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
											?>
												<!-- <button type="button" class="btn btn-warning pdf btn-sm pull-right" name="pdf" onClick="genPdfReport()" style="margin-right: 20px;" id="pdf" title="Download"><i class="fa fa-file-pdf-o"></i>Download</button> -->
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
	var data_url = "to_do_list_get_ajax.php";

	$("#sales_id").change(function(){
	   var sales_id = $('#sales_id').val();
	   callAjax(sales_id);
	});

	function callAjax(sales_id)
	{
		var sales_id = $('#sales_id').val();
		$.ajax({
    		url: data_url,
    		data: {
        		sales_id: sales_id,
       		},
		    beforeSend: function() {
		        $("#results").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
		    },
		    success: function(result) {
		        $("#results").html(result);
		    }
		});
	}

	function genPdfReport()
	{
		var date = $('#ToDate').val();
	   	var date1 = $('#fromdate').val();
	   	var sales_id = $('#account').val();
	   	var rc = encodeURIComponent($("#report_content1").html());
		$.ajax({
			type: "POST",
			url: "daily_sales_report_genpdf.php",
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


	function forceDownload(href,filename='') {
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();

		today = dd + '-' + mm + '-' + yyyy;
		// document.write(today);
		if(filename=='')
		{
			filename = today;
			alert(filename);
		}

		var anchor = document.createElement('a');
		anchor.href = href;
		anchor.download = "Sales-Report-"+today+".zip";
		document.body.appendChild(anchor);
		anchor.click();
	}

	function genPdfReportzip()
	{
		var date = $('#ToDate').val();
	   	var date1 = $('#fromdate').val();
	   	var sales_id = $('#account').val();
	   	var rc = encodeURIComponent($("#report_content1").html());
		$.ajax({
			type: "POST",
			url: "daily_sales_report_genpdf_zip.php",
			data: 'date=' + date +'&date1=' + date1  +'&sales_id=' + sales_id,
			// data: 'rc=' + rc,
			beforeSend: function() {
				// $(".transCover").fadeIn(800);
				$("#loading-modal").modal("show");
			},
			success: function(result) 
			{
				result = $.parseJSON(result);
				$("#loading-modal").modal("hide");
	    		toastr.success("Zip Generate Successfully");
	    		forceDownload(result.url,"Sales-Report-"+result.datedt+".zip");
	    		// window.open( result.url, '_blank');
			}
			/*error:function(error)
	        {
	        	$("#loading-modal").modal("hide");
	           	toastr.error("Failed To Download Zip!!");
	        }*/
		});
	}	
</script>


<script type="text/javascript">
	
    function TodolistPrint()
    {
    	var sales_id = $('#sales_id').val();
		var myWindow = window.open('print_todolist_ajax.php?sales_id='+ sales_id ,'','width=700,height=800');
     	myWindow.print();
    }
    var admintype = "<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE']?>";
    if(admintype!=0)
    {
    	$("#account").change();
    }
    window.onload = function(){
    	 var date = $('#ToDate').val();
   		 var date1 = $('#fromdate').val();
   		 var sales_id = $('#account').val();
	callAjax(sales_id,date,date1);
};
</script>

</body>
</html>