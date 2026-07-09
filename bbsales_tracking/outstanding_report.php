<?php
$page_id=618;$page_slug='outstanding_report';
$ctable 	= "orders";
$ctable1 	= "Customer Statement";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Outstanding Report";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
// $FromDate="";
// $ToDate="";
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
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
				</div>
				<div class="col-xl-12 ">
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-filter"></i>Filters </div>
							 <div class="tools">
								<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
								<div class="row"> 
									<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
                                    	<?php $date=date('d-m-Y'); ?>
                                    	<label>Select Date</label>
								      	<div class="form-group">
							               <input type="text" name="invoice_date" class="form-control invoice_date" id="invoice_date" placeholder="Date" autocomplete="off" readonly>
							            </div>
									</div>
									<div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
										<br/><button class="btn btn-primary" onclick="displayRecords()">View</button>
									</div>

									<div class="col-md-3 col-xs-3 col-sm-3 text-left" style="margin-top:10px">
										<div class="form-group">
											<label class="test" >Zone</label>

											<select class="form-control" name="zone" id="zone" onchange="displayRecords()" >
						                        <option value="">Select Zone </option>
						                        <?php
						                        $zone_r = $db->rp_getData("zone","*","isDelete=0",0);
						                        if(mysqli_num_rows($zone_r)>0)
						                        {
						                            while($zone_d = mysqli_fetch_array($zone_r))
						                            {
						                                ?>
						                                <option value="<?php echo $zone_d['id']; ?>" <?=($zone == $zone_d['id'])?"selected":"";?>><?php echo $zone_d['name']; ?></option>
						                                <?php
						                            }
						                        }
						                        ?>
						                    </select>
						                </div>

									</div>



									<div class="col-md-3 col-xs-3 col-sm-3 text-right" style="margin-top:10px">
										<br/><button class="btn btn-warning" onclick="printData()">Print</button>
									</div>
								</div> 
							</div>
						</div> 
					</div>
					<div class="col-xl-12">
						<div class="portlet light">
							<div class="portlet-body">
								<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%; margin-top:10%;padding-left:48%;" > </div>
								<div id="results">
								</div>
							</div>
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
<script type="text/javascript"> 
	$('#invoice_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });
	
	var data_url = "outstanding_report_get_ajax.php";
	 
	function loadDataTable(){
		$('#datatable_1').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "5%" }, 
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },
				  { "sWidth": "20%","bSortable": false }
				]
		});
	}
	function displayRecords(numRecords) {
		var invoice_date = $("#invoice_date").val();
		var zone= $('#zone').val();
		$("#results" ).html("");
		$("#results" ).load( data_url+"?show=" + numRecords+"&invoice_date="+invoice_date  +"&zone="+ zone,function(){
			loadDataTable();
		}); //load initial records
		
		//executes code below when user click on pagination links
		$("#results").on( "click", ".paging_simple_numbers a", function (e){
			e.preventDefault();
			var numRecords  = $("#numRecords").val();
			$(".loading-div").show(); //show loading element
			var page = $(this).attr("data-page"); //get page number from link
			$("#results").load(data_url+"?show=" + numRecords+"&invoice_date="+invoice_date,{"page":page}, function(){ //get content from PHP page
				$(".loading-div").hide(); //once done, hide loading element
				loadDataTable();
			});		
		}); 
	}

	// used when user change row limit
	function changeDisplayRowCount(numRecords) {
		displayRecords(numRecords, 1);
	}

	$(document).ready(function() {
		displayRecords(100,1);
	});

	function printData()
	{
		var invoice_date = $("#invoice_date").val();
		var zone=$("#zone").val();
		var myWindow = window.open('customer_statement_print.php?invoice_date='+invoice_date + '&zone='+ zone,'','width=1000,height=800');
     	myWindow.print();
		// var myWindow = window.open('','','width=800,height=800')
	    // myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
	    // myWindow.print();
	} 
</script> 
</body>
</html>