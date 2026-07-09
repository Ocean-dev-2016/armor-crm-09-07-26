<?php
$page_id=562;$page_slug='page_category';
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
include("../include/class.sales_executive.php");
$id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
if($id=="")
{
	$system->addErrorMessage("No Sales Officer Found!!");
	$db->rp_location("sales_executive_manage.php");
}
$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
$phone=ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'"));
$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
$date=date('d-m-Y',strtotime($_REQUEST['date']));
$sales_executive=new SalesExecutive();
$response=$sales_executive->trackSales($id,$date);
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
<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 768px;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>

</head>
<body class="page-md">
						<div class="portlet-body">
							<div class="row">
							 <div class="col-md-6 col-xs-12 col-sm-3" style="margin-top:10px">
								<h3><?php echo $name." (".$phone.") "."date:-".date('d-m-Y',strtotime($_REQUEST['date'])).""; ?></h3>
								<input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
							 </div>
							 
							</div>
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results">
							 <div id="map"></div>

							</div>
						</div>
					</div>
<?php include("include_js.php"); ?>
 <script>
		var map;
		var markerCluster;
		var markers=[]
      	function initMap() {
		if(locations.length>0)
		{
			var location_count = locations.length;
			var j = 0;
			var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 19,
				center: {lat:locations[0].lat, lng: locations[0].lng},
			});
			var infowindow = new google.maps.InfoWindow();
			$.each(locations,function(i,v){

				if(++j === location_count)
				{

					var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/green.png',
					title: locations[i]['type'],
					
					});
				}
				else
				{
					var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/'+locations[i]['icon'],
					title: locations[i]['type'],
					
					});
				}


				/*var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/'+locations[i]['icon'],
					title: locations[i]['type']
				});*/
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
				  infowindow.setContent("<h1>"+(i+1)+") "+locations[i]['date']+"</h1><p>Lat:"+locations[i]['lat']+"<br/> Long:"+locations[i]['lng']+" "+locations[i]['type']+"</p>");
				  infowindow.open(map, marker);
				}
			  })(marker, i));
			})
			  var flightPath = new google.maps.Polyline({
				  // path: locations,
				  geodesic: true,
				  strokeColor: '#FF0000',
				  strokeOpacity: 1.0,
				  strokeWeight: 2
				});
				flightPath.setMap(map);
				/*map.drawRoute({
				  origin: location,
				  destination: location,
				  travelMode: 'driving',
				  strokeColor: '#131540',
				  strokeOpacity: 0.6,
				  strokeWeight: 6
				});*/
		}
        else
		{
			$("#map").html(aj.getLoadingBlock());
		}
      }
	  
     function clearMarker(){
		 locations=[];
		
		  for (var i = 0; i < markers.length; i++) {							  
			markers[i].setMap(map);			
		  }
		  if(markerCluster)
			markerCluster.clearMarkers();
	 }
    </script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
	
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
    </script>
<script type="text/javascript">
var locations=[];
var labels=[];
var status="";
var waypts = [];
var result= <?php echo json_encode($response); ?>;
var searchName="";
var data_url = "sales_executive_ajax_function.php";
var sid = <?php echo ($id!="")?$id:0; ?>

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords();
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords();
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadMap(){
	
	alert("Map Loading Please Wait");
}
function displayRecords() {
	var date=$("#date").val();
	if(result.ack==1)
	{
		//clearMarker();
		var locs=result.result
		$.each(locs,function(i,v){
			locations.push({date:v.date,lat: parseFloat(v.lat), lng:  parseFloat(v.lng),type_slug:v.type_slug,type:v.type,icon:v.icon});
			labels.push(v.date);
		});
		initMap();
		toastr.success(result.ack_msg,"success");
	}
	else
	{
		//clearMarker();
		toastr.error(result.ack_msg,"Sorry");
	}
}
$(document).ready(function() {
	displayRecords();
	
});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='category_crud.php?mode=delete&id='+id;
	}
}

</script>
</body>
</html>