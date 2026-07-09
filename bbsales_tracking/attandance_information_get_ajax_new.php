<?php 
$page_id=598;$page_slug='attendance_page';
include("connect.php");
$ctable = "attendance";
$id=$_REQUEST['id'];
$ctable_r = $db->rp_getData($ctable,"*","id='".$_REQUEST['id']."'","",0);
$ctable_d = mysqli_fetch_array($ctable_r);
if($ctable_d['image_path']!="" && file_exists(ATTENDANCE.$ctable_d['image_path'])) 
{
	$img = ATTENDANCE.$ctable_d['image_path'];
}
else
{
	$img = $ctable_d['image_path'] = DEFAULTIMG;
}
$salesexename = $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'");
$response = array("ack"=>1,"ack_msg"=>"Sales Tracking Fetched!!");
$response["result"][] = array("lat"=>$ctable_d['latitude'],"lng"=>$ctable_d['longitude'],"name"=>$salesexename,"dates"=>date("d M H:i",strtotime($ctable_d['created_date'])),"type"=>"Attendance","address"=>$address);
?>
<div class="row">
	<div class="col-sm-12">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<th colspan="2" style="text-align: center;"><?php echo date('d-m-Y h:i A',strtotime($ctable_d['date_time']))." | <b style='color:green;font-size:18px;'>".$ctable_d['inout_status']."</b> | "."Address : " .$ctable_d['app_address'];  ?></th>
				</tr>
				<tr>
					<td>
						<div id="map" style="height: 400px;width: 600px;"></div>
					</td>
					<td>
						<img src="<?=$img?>" style="width: 350px;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- <div class="col-sm-6">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<th>Address</th>
					<td><?php echo $ctable_d['app_address'];  ?></td>
				</tr>

				<tr>
					<th>Image</th>
					<td>
                    	<img src="<?=$img?>" style="height: 80px; width: 120px;">
		            </td>									
				</tr>
			</tbody>
		</table>
	</div> -->

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
</script>
<script>
var map;
var markerCluster;
var markers = [];

function initMap() {
	if (locations.length > 0) {

    	var location_count = locations.length;
        var j = 0;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 19,
            center: {
                lat: locations[0].lat,
                lng: locations[0].lng
            },
        });
        var infowindow = new google.maps.InfoWindow();
        $.each(locations, function(i, v) {
            var marker = new google.maps.Marker({
                position: {
                    lat: locations[i].lat,
                    lng: locations[i].lng
                },
                map: map,
                icon: '<?php echo SITEURL; ?>/images/pin/last-pin.png',
                title: locations[i]['type'],

            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    /*infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] +"<p><b>Address</b>:" + locations[i]['address'] + "<br/><h4><b>Name:" + locations[i]['name'] + "</b></h4></p>");*/
                    infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] + "<br/><h4><b>Name:" + locations[i]['name'] + "</b></h4></p>");
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
        for (var i = 0; i < location_count; i++) {
            var cityCircle = new google.maps.Circle({
                strokeColor: '#518FFB',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#518FFB',
                fillOpacity: 0.35,
                map: map,
                center: {
                    lat: locations[i].lat,
                    lng: locations[i].lng
                },
                radius: 10
            });
        }
    } else {
        var mapOptions = {
            center: {
                'lat': 22.2939994,
                'lng': 70.7892855
            },
            zoom: 14
        };

        // Map object
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
    }
}

function clearMarker() {
    locations = [];

    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
    if (markerCluster)
        markerCluster.clearMarkers();
}
var locations = [];
var labels = [];
var status = "";
var waypts = [];
var result = <?php echo json_encode($response); ?>;
tracking();

function tracking() {
	// var date = $("#date").val();
    if (result.ack == 1) {
        //clearMarker();
        var locs = result.result
        $.each(locs, function(i, v) {
            locations.push({
                date: v.dates,
                name: v.name,
                lat: parseFloat(v.lat),
                lng: parseFloat(v.lng),
                type: v.type,
            });
            labels.push(v.date);
        });
        initMap();
        // toastr.success(result.ack_msg, "success");
    } else {
        clearMarker();
        // toastr.error(result.ack_msg, "Sorry");
    }
}
</script>
<?php require_once 'disconnect.php';  ?>

