<?php
   $page_id=400;$page_slug='dashboard';
   $ctable  = "salesexecutive_tracking";
   $ctable1   = "Sales Officer Tracking";
   $main_page   = $ctable;
   $page    = "manage_".$ctable;
   $page_title = "Manage ".$ctable1;
   include("connect.php");
   include("../include/class.sales_executive_snap_road.php");
   $id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
   $report_flag=(isset($_REQUEST['flag']) && $_REQUEST['flag']!="")?$_REQUEST['flag']:"";
   $H="";
   if($report_flag!="")
   {
      $H="hidden";
   }
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
    $response=$sales_executive->trackSalesPin("",$date);

   }
   else
   {
    $name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
    $phone= " (".ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'")).") ";
    $sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
    $date=date('d-m-Y',strtotime($_REQUEST['date']));
    $response=$sales_executive->trackSalesPin($id,$date);
    
   
    // this code for status (offline/online) purpose only (this is for single user purpose)
    if($id!="")
    {
      $where="isDelete=0";
      $where .=" AND sales_executive_id=".$id."";
      $where.=" AND DATE(date)='".date("Y-m-d")."'";
      $data_d = $db->rp_getData("salesexecutive_tracking","*",$where,"id desc",0);
      $data = mysqli_fetch_assoc($data_d);
      $last_time = $data['date'];
    }
    if($last_time)
    {
      $last_time = date("G:i:s",strtotime($last_time));
    }
    $curr_time = date("G:i:s");
    $sec = ACTIVE_TIME*60;
    if(strtotime($curr_time)>strtotime($last_time)+$sec)
    {
      $status = "offline";
    }
    else
    {
      $status = "online";
    }
   }
?>
<style type="text/css">
  #map
  {
    height: <?=$_REQUEST['reqheight']?>px;
  }
</style>
<div class="portlet-body">
   <div class="row ">
      <div class="col-md-6 col-xs-12 col-sm-3 <?= $H; ?>" style="margin-top: -15px">
         <span style="font-size: 15px">
         <?php 
            if($id != "" && 1==2)
            { ?>
         <a style="height: 60px;">
         <img src="<?=SITEURL."images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
         </a>
         <?php 
            } 
            ?>
         <!-- <?php echo $name.$phone." ON ".date('d-m-Y',strtotime($date)).""; ?> -->
         <?php echo $name.$phone; ?>
         </span>
      </div>
      <!-- <button class="btn btn-info" onclick="playPin()">Play</button> -->
   </div>
   <input type="hidden" id="animinput">
   <div id="results">
      <div id="map"></div>
   </div>
</div>
<script>
var map;
var markerCluster;
var markers = []
// Symbol that gets animated along the polyline
var lineSymbol = {
  path: google.maps.SymbolPath.CIRCLE,
  scale: 8,
  strokeColor: '#005db5',
  strokeWidth: '#005db5'
};
function animateCircle(polyline) {
  var count = 0;
  // fallback icon if the poly has no icon to animate
  var defaultIcon = [
    {
      icon: lineSymbol,
      offset: '100%'
    }
  ];
  window.setInterval(function() {
    // count = (count + 1) % 200;
    count = (count + 1);
    var icons = polyline.get('icons') || defaultIcon;
    icons[0].offset = (count / 2) + '%';
    polyline.set('icons', icons);
  }, 20);
}
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
        var bounds = new google.maps.LatLngBounds();
        var pinico = "<?php echo SITEURL; ?>images/pin/";
        var pin = "";
        var typePinArray = [
                            "visit123",
                            "login",
                            "logout",
                            "attandance-in",
                            "attandance-out",
                            "traking-sync1",
                            "create-order",
                            "create-customer",
                            "add-expance",
                            "add-visit",
                            "add-inquiry",
                            "edit-inquiry",
                            "delete-inquiry",
                            "add-area",
                            "add-complain",
                            "add-meeting",
                            "add-meeting-member",
                            "edit-meeting",
                            "delete-meeting-member",
                            "change-pasdsword",
                            "hour-sync1",
                            "addfollowup",
                            "addleave",
                            ];
        $.each(locations, function(i, v) {

            pin = pinico +''+ typePinArray[locations[i]['mytype']]+".png";
            
            /*if (locations[i]['mytype'] == "1") {
                pin = pinico + "login.png";
            } else if (locations[i]['mytype'] == "2") {
                pin = pinico + "logout.png";
            } else if (locations[i]['mytype'] == "3") {
                pin = pinico + "attendance_in.png";
            } else if (locations[i]['mytype'] == "4") {
                pin = pinico + "attendance_out.png";
            } else if (locations[i]['mytype'] == "5") {
                pin = pinico + "capture.png1";
                // pin = "";
            } else if (locations[i]['mytype'] == "6") {
                pin = pinico + "visit.png";
            } else if (locations[i]['mytype'] == "logout") {
                pin = pinico + "logout.png";
            } else {
                pin = pinico + "visit123.png";
            }*/

            if (++j === location_count) {
                var marker = new google.maps.Marker({
                    position: {
                        lat: locations[i].lat,
                        lng: locations[i].lng
                    },
                    map: map,
                    icon: pinico + "last-pin.png",
                    title: locations[i]['type'],
                });
            } else {
                var marker = new google.maps.Marker({
                    position: {
                        lat: locations[i].lat,
                        lng: locations[i].lng
                    },
                    map: map,
                    icon: pin,
                    title: locations[i]['type'],

                });
            }
            bounds.extend(marker.getPosition());
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    if (locations[i]['status'] == "offline") {
                        var color = "red";
                    } else if (locations[i]['status'] == "online") {
                        var color = "green";
                    }
                  
                    // infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] + "<p><b>Address</b>:" + locations[i]['address'] + "<br/><h4><b>Name:" + locations[i]['name'] + "<br/><a style='color:" + color + "'>" + locations[i]['status'] + "</a></b></h4></p>");
                    infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] + "<p><b>Address</b>:" + locations[i]['address'] + "<br/><h4><b>Name:" + locations[i]['name'] +"</b></h4></p>");
                    infowindow.open(map, marker);
                }
            })(marker, i));
        })
        map.fitBounds(bounds);

        /*var flightPath = new google.maps.Polyline({
            path: locations,
            geodesic: true,
            strokeColor: 'orange',
            strokeOpacity: 1.0,
            strokeWeight: 8,
            icons: [{
                icon: {path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW},
                offset: '100%',
                repeat: '1000px'
            }]
        });*/
        var flightPath2 = new google.maps.Polyline({
            path: locations,
            geodesic: true,
            strokeColor: '#005db5',
            strokeOpacity: 1.0,
            strokeWeight: 6,
            icons: [{
                icon: lineSymbol,
                offset: '100%',
                // repeat: '1000px'
            }]
        });
        // flightPath.setMap(map);
        flightPath2.setMap(map);
        // console.log(flightPath2);
        // $('#animinput').val(data(flightPath2));
        animateCircle(flightPath2);
        map.drawRoute({
            origin: location,
            destination: location,

            travelMode: 'DRIVING',
            transitOptions: TransitOptions,
            drivingOptions: DrivingOptions,
            unitSystem: UnitSystem,
            avoidHighways: Boolean,
            avoidTolls: Boolean,
            strokeColor: '#FF0000',
            strokeOpacity: 0.6,
            strokeWeight: 6,
        });
        var circle = new google.maps.Circle({
            map: map,
            radius: 16093, // 10 miles in metres
            fillColor: '#AA0000'
        });
        circle.bindTo('center', marker, 'position');
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

    if (result.ack == 1) {
        //clearMarker();
        var locs = result.result
        $.each(locs, function(i, v) {
            locations.push({
                date: v.date,
                name: v.name,
                lat: parseFloat(v.lat),
                lng: parseFloat(v.lng),
                type_slug: v.type_slug,
                type: v.type,
                icon: v.icon,
                status: v.status,
                mytype: v.mytype,
                address: v.address
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

function playPin()
{
  // initMap();
  var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 19,
            center: {
                lat: locations[0].lat,
                lng: locations[0].lng
            },
        });
      var locs = result.result;
        $.each(locs, function(i, v) {
            locations.push({
                date: v.date,
                name: v.name,
                lat: parseFloat(v.lat),
                lng: parseFloat(v.lng),
                type_slug: v.type_slug,
                address: v.address,
                type: v.type,
                icon: v.icon,
                status: v.status,
                mytype: v.mytype,
                address: v.address
            });
           // labels.push(v.date);
        });
       /*google.maps.Map(document.getElementById('map'), {
            zoom: 19,
            center: {
                lat: locations[0].lat,
                lng: locations[0].lng
            },
        });*/
        // console.log(locations);
      var flightPath2 = new google.maps.Polyline({
            path: locations,
            geodesic: true,
            strokeColor: '#005db5',
            strokeOpacity: 1.0,
            strokeWeight: 6,
            icons: [{
                icon: lineSymbol,
                offset: '100%',
                // repeat: '1000px'
            }]
        });  
        // console.log(flightPath2);
        flightPath2.setMap(map);
        animateCircle(flightPath2);
}
</script>