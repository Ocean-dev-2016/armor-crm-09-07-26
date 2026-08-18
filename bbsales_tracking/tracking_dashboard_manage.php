<?php 
$page_id=632;$page_slug='tracking_dashboard';
$main_page = "home";
require_once("connect.php");
$date=date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
   	<head>
		<meta charset="utf-8">
		<title>Dashboard | <?php echo SITETITLE; ?></title>
		<?php include("include_css.php"); ?>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
		<link rel="stylesheet" type="text/css" href="css/dashboard.css"/>
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
		<style type="text/css">
			/*#mapd
			{
				height: 450px!important;
				width: 100%;
 				overflow-y: scroll;
			}*/


			/* width */
			::-webkit-scrollbar {
			  width: 5px!important;
			}

			/* Track */
			::-webkit-scrollbar-track {
			  background: #f1f1f1!important; 
			}
			 
			/* Handle */
			::-webkit-scrollbar-thumb {
			  background: #a7a7a770!important; 
			}

			/* Handle on hover */
			::-webkit-scrollbar-thumb:hover {
			  background: #a7a7a7!important; 
			}
			.p-0
			{
				padding: 0!important;
			}
			#map, .leaflet-container {
				z-index: 1 !important;
			}
			.leaflet-pane {
				z-index: 10 !important;
			}
			.leaflet-top, .leaflet-bottom {
				z-index: 20 !important;
			}
			.device_floating, .pin_floating, #pin_panel, #device_panel, .floating, .floating.panel {
				z-index: 9999 !important;
				background-color: #ffffff !important;
				box-shadow: 0 4px 15px rgba(0,0,0,0.3) !important;
				border: 1px solid #ccc !important;
			}
			.leaflet-popup {
				z-index: 500 !important;
			}
			.leaflet-popup-content-wrapper {
				border-radius: 8px !important;
				box-shadow: 0 5px 20px rgba(0,0,0,0.25) !important;
				padding: 4px !important;
			}
			.leaflet-popup-content {
				font-size: 14px !important;
				line-height: 1.5 !important;
				color: #222 !important;
				margin: 12px 14px !important;
			}
			.map-popup-card h4 {
				font-size: 16px !important;
				font-weight: 700 !important;
				color: #0b58a2 !important;
				margin: 0 0 8px 0 !important;
				border-bottom: 1px solid #eee;
				padding-bottom: 6px;
			}
			.map-popup-card p {
				margin: 0 !important;
				font-size: 13px !important;
				color: #333 !important;
				line-height: 1.6 !important;
			}
			.map-popup-card b {
				color: #111 !important;
				font-weight: 600 !important;
			}
			.map-popup-address {
				background: #f8f9fa;
				border-left: 3px solid #00d0ff;
				padding: 5px 8px;
				margin: 6px 0 !important;
				border-radius: 3px;
				font-size: 13px !important;
				color: #222 !important;
			}
		</style>
   	</head>
	<body class="page-md">
		<?php include("header.php"); ?>
		<div class="page-container">
			<div class="page-content">
				<div class="container">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							 <div class="row">
								<div class="col-md-2" >
									<div class="portlet light com-height">
									   <div class="portlet-body">
									      <div class="row">
									      	<div class="col-md-12">
										      	<div class="form-group" style="margin-bottom: 0px!important">
									               <input type="text" name="ToDate" class="form-control " id="ToDate" value="<?php echo $date; ?>" placeholder="Date" style="text-align: center;" autocomplete="off" readonly>
									            </div>
									      	</div>
									      	<div class="row text-center">
										      	<div class="col-md-12 mt-15">
										      		<div class = "btn-group">
													   <button type = "button" class = "main-html-ul btn btn-success dropdown-toggle btn-sm" data-toggle = "dropdown">
													      Last <i class="fa fa-map-marker"></i>
													      <span class = "caret"></span>
													   </button>
													   
													   <ul class = "dropdown-menu" role = "menu">
													      
													      <li><a href = "#" class="last-ul" onclick="getDataMap($('#selected_user').val(),'last');">Last <i class="fa fa-map-marker" style="position: absolute;right: 10px;"></i></a></li>

													      <li><a href = "#" class="route-ul" onclick="getDataMap($('#selected_user').val(),'route');">Route <i class="fa fa-sitemap" style="position: absolute;right: 10px;"></i></a></li>

													      <li><a href = "#" class="visit-ul" onclick="getDataMap($('#selected_user').val(),'visit');">Visit <i  style="position: absolute;right: 10px;"class='fa fa-bus'></i></a></li>
													      
													      <li class = "divider"></li>
													      <li><a href = "#" class="attendance-ul" onclick="getDataMap($('#selected_user').val(),'attendance');">Attendance <i class='fa fa-clock-o' style="position: absolute;right: 10px;"></i></a></li>

													      <!-- <li><a href = "#" class="daily-report-ul" onclick="getDataMap($('#selected_user').val(),'daily_report');">Daily Report <i class='fa fa-file' style="position: absolute;right: 10px;"></i></a></li> -->
													       
													   </ul>
													</div>
										      	</div> 
									      	</div>
									      	<div class="col-md-12" style="padding: 0!important">
									         	<hr style="margin-bottom: 10px!important" />
									         	<div class="text-center">Last Update On <br/><span class="lastupdate"><?=date('d-m-Y H:i:s')?></span></div>
									      		<div class="text-center"><span style="font-size: 18px!important" class="se-span">Sales Person</span></div>
									      		

									      		<select class="form-control" id="s_id" name="s_id" onchange="getDataMap((this.value),$('#selected_map').val());">
									      			<option value="">Select Sales Person</option>
									      			<?php 
									      			$WhereCondition1.= "isDelete=0 AND isActive=1";
								      				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
													{ 
												     	if($rights['personal_flag']==1)
														{
															$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

														    $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);

														    $WhereCondition1.=" AND id='".$check_id."'";  
														}
														else
														{
															if($rights['all_data_flag']==1)
															{
																
															}
															else
															{
																$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

															    $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
															    if ($get_sales_type== "sales_manager") 
															    {
															        $sales_executive_type = "Regional Sales Manager";
															        $key="sm_id";
															        $chain_where_d.=' ' .$key.'='.$check_id;
															    }

															    else if ($get_sales_type == "area_sales_manager") 
															    {
															        $sales_executive_type = "National Sales Manager";//Business Development Manager
															        $key="asm_id";
															        $chain_where_d.=' ' .$key.'='.$check_id;
															    }

															    else if ($get_sales_type == "sales_officer") 
															    {
															        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
															        $key="so_id";
															        $chain_where_d.=' ' .$key.'='.$check_id;
															    }
															    else if ($get_sales_type == "sales_executive") 
															    {
															        $sales_executive_type = "Sales Officer";
															        $key="se_id";
															        $chain_where_d.=' ' .$key.'='.$check_id;
															    }
															    else
															    {
															    	$chain_where_d.=' type = "service_engineer"';
															    }

															    $data = $db->rp_getData("sales_executive","id",$chain_where_d,"",0);

															    $SALEID1=array();
																if($data)
																{
																	while($data_d=mysqli_fetch_assoc($data))
																	{
																		$SALEID1[]=$data_d['id'];
																	}
																}
																if(!empty($SALEID1))
																{
																	$SALEID1=implode(",", $SALEID1);
																	
																		$WhereCondition1 .= "  AND id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
																	
																	
																}
																else
																{
																		$WhereCondition1 .= "  AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
																}
															}	
														}
													} 
									                $data_id = $db->rp_getData("sales_executive","*",$WhereCondition1,"",0);
									               while ($data_d1 = mysqli_fetch_assoc($data_id)) 
									               { 
									               	?>
									               	<option value="<?= $data_d1['id']; ?>"  ><?php echo $data_d1['name']; ?></option>
									               	<?php 
													} 
									      			?>
									      		</select>  
									      	</div>
									      	<input type="hidden" id="selected_user" value="0">
									      	<input type="hidden" id="selected_map" value="last">
									      </div>
									  </div>
									</div>
								</div>
								<div class="col-md-10">
									<div class="portlet light com-height" style="position: relative;">
									   <div class="portlet-body" style="position: relative;">
									   		
									   		<!-- <device INFO -->
									   		<div class="device_floating" id="device_toggle"><img src="<?="../images/device_info.png"?>" width="20px;" height="25px;">
									   		</div>
											<div id="device_panel">
												<div class="block">
												   	<div class="ml-20-p">
												   		<div class="dvinfo">
													   	</div>
												  	</div>
												</div>
											</div>

									   		<!-- <PIN INFO -->
									   		<div class="pin_floating" id="pin_toggle"><img src="<?="../images/pin/visit.png"?>" width="20px;">
									   		</div>
											<div id="pin_panel">
												<div class="block">
												   	<div class="ml-20-p">
												    	<strong style="color: red">Pin Information:</strong><br/>
 
													   	<li class="form-control li-pin">Login: <img class="pin_size" src="../images/pin/login.png"></li>
														<li class="form-control li-pin">Logout: <img class="pin_size" src="../images/pin/logout.png"></li>
														<li class="form-control li-pin">Attandance In: <img class="pin_size" src="../images/pin/attandance-in.png"></li>
														<li class="form-control li-pin">Attandance Out: <img class="pin_size" src="../images/pin/attandance-out.png"></li>
														<li class="form-control li-pin">Traking Sync: <img class="pin_size" src="../images/pin/traking-sync.png"></li>
														<li class="form-control li-pin">Create Order: <img class="pin_size" src="../images/pin/create-order.png"></li>
														<li class="form-control li-pin">Create Customer: <img class="pin_size" src="../images/pin/create-customer.png"></li>
														<li class="form-control li-pin">Add Expance: <img class="pin_size" src="../images/pin/add-expance.png"></li>
														<li class="form-control li-pin">Add Visit: <img class="pin_size" src="../images/pin/add-visit.png"></li>
														<li class="form-control li-pin">Add Inquiry: <img class="pin_size" src="../images/pin/add-inquiry.png"></li>
											 			<li class="form-control li-pin">Add Complain: <img class="pin_size" src="../images/pin/add-complain.png"></li>
											 			<li class="form-control li-pin">Add Follow Up: <img class="pin_size" src="../images/pin/addfollowup.png"></li>
														<li class="form-control li-pin">Add Leave: <img class="pin_size" src="../images/pin/addleave.png"></li>
														<li class="form-control li-pin">Last Pin : <img class="pin_size" src="../images/pin/last-pin.png"></li>
												  	</div>
												</div>
											</div>
 											<div><button type="button" class="btn btn-default test hidden">Take a Screenshot!</button></div>
											<div id="mapd"></div>
									   </div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
			$("s_id").select2();
    	</script>

		<script async defer
	    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
	    </script>
	    
	    <!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=AIzaSyADYWIGFSnn3DHlJblK0hntz5KQiwbD0hk"></script> -->

		<?php include("footer.php"); ?>
		<script type="text/javascript">
			var img_path_on = "<img src='<?='../images/on.png'?>' width='20px;'>";
			var img_path_device = "<img src='<?='../images/device_info.png'?>' width='20px;'>";
			var img_path_off = "<img src='<?='../images/offline.gif'?>' width='20px;'>";
			var img_path_location = "<img src='<?='../images/pin/visit.png'?>' width='20px;'>";
		</script>
		<?php include("include_js.php"); ?>
		<script src="js/dashboard.js"></script>


		<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js" integrity="sha256-w6/1B0uwkpR3uX0YUw3k2zzHnq6xDNdVZHLIdz8xV6I=" crossorigin="anonymous"></script>
		<!-- <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script> -->
		<script src="https://cdn.jsdelivr.net/npm/canvas2image@1.0.5/canvas2image.min.js"></script>
		
		<script type="text/javascript">
			document.querySelector('.test').addEventListener('click', function() {
		        html2canvas(document.querySelector('#mapd'), {
		        	// useCORS: true,
		            onrendered: function(canvas) {
		                // document.body.appendChild(canvas);
		              return Canvas2Image.saveAsPNG(canvas);
		            }
		        });
		    });

		    
		</script>
	</body>
</html>