<?php 
	$page_id=577;$page_slug='visit_page';
	include("connect.php");
	$ctable 	= "visit";
	$ctable1 	= "User";
    $address    = $_REQUEST['app_address'];//$db->getAddress($_REQUEST['lat'],$_REQUEST['lng']);
    $response = array("ack"=>1,"ack_msg"=>"Sales Tracking Fetched!!");
	$response["result"][] = array("lat"=>$_REQUEST['lat'],"lng"=>$_REQUEST['lng'],"name"=>$_REQUEST['salesexename'],"dates"=>$_REQUEST['date'],"type"=>"Visit","address"=>$address);
?>
<div id="results">
  <div id="map" style="height: 400px;"></div>
</div>
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
                    infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] +"<p><b>Address</b>:" + locations[i]['address'] + "<br/><h4><b>Name:" + locations[i]['name'] + "</b></h4></p>");
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
displayRecords();

function displayRecords() {
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
                address:v.address,
            });
            labels.push(v.date);
        });
        initMap();
        toastr.success(result.ack_msg, "success");
    } else {
        clearMarker();
        toastr.error(result.ack_msg, "Sorry");
    }
}
</script>