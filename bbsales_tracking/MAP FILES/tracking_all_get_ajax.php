<?php
$page_id=562;$page_slug='page_category';
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
require_once("connect.php");
require_once("../include/class.sales_executive.php");
$id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
$sales_executive=new SalesExecutive();
if($id=="")
{
	$name="ALL SALSE EXECUTIVE LAST PUNCH";
	// $date=date('30-03-2019');
	if($_REQUEST['date'])
	{
		$date=date('d-m-Y',strtotime($_REQUEST['date']));
	}
	else
	{
		$date=date('d-m-Y');
	}
	$response=$sales_executive->trackSalesAll($date);
}
else
{
	$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
	$phone= " (".ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'")).") ";
	$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
	$date=date('d-m-Y',strtotime($_REQUEST['date']));
	$response=$sales_executive->trackSalesAll($date,$id);
}
?>
<style>
  /* Always set the map height explicitly to define the size of the div
   * element that contains the map. */
  #map {
    height: 768px;
  }
</style>

		 <div id="map"></div>

<?php //require_once("include_js.php"); ?>
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
				var marker = new google.maps.Marker({
				position: {lat:locations[i].lat, lng: locations[i].lng},
				map: map,
			    icon: '<?php echo SITEURL; ?>/images/marker/green.png',
				title: locations[i]['type'],
				
				});


				/*var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/'+locations[i]['icon'],
					title: locations[i]['type']
				});*/
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
				  infowindow.setContent("<h1>"+(i+1)+") "+locations[i]['date']+"</h1><p>Lat:"+locations[i]['lat']+"<br/> Long:"+locations[i]['lng']+" "+locations[i]['type']+"<br/>name:"+locations[i]['name']+"</p>");
				  infowindow.open(map, marker);
				}
			  })(marker, i));
			})
			  var flightPath = new google.maps.Polyline({
				  //path: locations,
				  geodesic: true,
				  strokeColor: '#FF0000',
				  strokeOpacity: 1.0,
				  strokeWeight: 2
				});
				flightPath.setMap(map);
				// map.drawRoute({
				//   origin: location,
				//   destination: location,
				//   travelMode: 'driving',
				//   strokeColor: '#131540',
				//   strokeOpacity: 0.6,
				//   strokeWeight: 6
				// });
		}
        else
		{
			var mapOptions = {
			    center: {'lat': 22.2939994, 'lng': 70.7892855},
			    zoom: 14
		 	};

		  	// Map object
		 	map = new google.maps.Map(document.getElementById('map'), mapOptions);
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
			locations.push({date:v.date,name:v.name,lat: parseFloat(v.lat), lng:  parseFloat(v.lng),type_slug:v.type_slug,type:v.type,icon:v.icon});
			labels.push(v.date);
		});
		initMap();
		toastr.success(result.ack_msg,"success");
	}
	else
	{
		clearMarker();
		toastr.error(result.ack_msg,"Sorry");
	}
}
	displayRecords();
</script>
