<?php
$page_id=400;$page_slug='dashboard';
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
include("../include/class.sales_executive.php");
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

	// Get Attendance In Photo on that date
	$att_date = date('Y-m-d', strtotime($_REQUEST['date']));
	$att_img_raw = $db->rp_getValue("attendance", "image_path", "sales_id='" . $id . "' AND DATE(date_time)='" . $att_date . "' AND isDelete=0 AND inout_status='in' ORDER BY date_time ASC LIMIT 1", 0);
	if (!$att_img_raw) {
		$att_img_raw = $db->rp_getValue("attendance", "image_path", "sales_id='" . $id . "' AND DATE(date_time)='" . $att_date . "' AND isDelete=0 ORDER BY date_time ASC LIMIT 1", 0);
	}
	$user_avatar = "";
	if ($att_img_raw != "") {
		$user_avatar = function_exists('armor_attendance_image') ? armor_attendance_image($att_img_raw) : (SITEURL . "resource/attendance/" . $att_img_raw);
	}
	if ($user_avatar == "") {
		$sales_profile_img = $db->rp_getValue("sales_executive", "image_path", "id='" . $id . "'", 0);
		if ($sales_profile_img != "") {
			$user_avatar = SITEURL . "resource/image/" . $sales_profile_img;
		} else {
			$user_avatar = SITEURL . "images/noimage.png";
		}
	}


	// this code for status (offline/online) purpose only (this is for single user purpose)
	/*if($id!="")
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
	}*/
}
?>
<style type="text/css">
	#map
	{
		height: <?=$_REQUEST['reqheight']?>px;
	}
</style>
<!-- <div class="portlet-body">
   <div class="row ">
      <div class="col-md-10 col-xs-12 col-sm-3 <?= $H; ?>" style="margin-top: -15px">
         <span style="font-size: 15px">
         <?php 
            if($id != "" && 1==2)
            { ?>
         <a style="height: 60px;">
         <img src="<?= "../images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
         </a>
         <?php 
          } 
          ?>
          
          <?php $total_km = $db->rp_getValue("salesexecutive_tracking_km","total_km","sales_executive_id='".$id."' AND DATE(route_date)='".date('Y-m-d',strtotime($date))."'",0) ?>
         <?php echo $name.$phone; ?> 
         <?php
         if($total_km)
         {
          echo "<b>(Total Kilometer : ".$total_km." KM)</b>";
         }
         ?> -->
         <!-- <button type="button" class="btn btn-primary" onclick="update_km('<?= $id ?>','<?= $date ?>')">Calculate KM</button>

         

          &nbsp;&nbsp;&nbsp;<input class="form-check-input" type="checkbox" value="" id="tracking_sync">
          <label class="form-check-label" for="tracking_sync">Tracking Sync Pin Hide Or Show</label>
        </span>
      </div> -->
      <!-- <button class="btn btn-info" onclick="playPin()">Play</button> -->
   <!-- </div> -->
   &nbsp;&nbsp;&nbsp;<input class="form-check-input" type="checkbox" value="" id="tracking_sync">
          <label class="form-check-label" for="tracking_sync">Tracking Sync Pin Hide Or Show</label>
   <input type="hidden" id="animinput">
   <div id="results">
      <div id="map"></div>
   </div>
</div>
<script>

$('#tracking_sync').click(function() {
  initMap();
});

var map = null;
var markersLayer = null;
var routeLine = null;

function initMap() { 
    if (locations.length > 0) {
        var location_count = locations.length;
        var j = 0;
        var pinico = "../images/pin/";

        if (map) {
            map.remove();
            map = null;
        }

        var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        });

        // High-Quality Google Hybrid / Satellite Layer (Free, Watermark-free)
        var googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Google Streets Layer
        var googleStreets = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var baseMaps = {
            "Google Satellite (HD)": googleSat,
            "Google Streets": googleStreets,
            "OpenStreetMap": osmLayer
        };

        map = L.map('map', {
            center: [locations[0].lat, locations[0].lng],
            zoom: 15,
            layers: [googleSat] // default to Google Satellite
        });

        L.control.layers(baseMaps).addTo(map);

        var latlngs = [];
        var typePinArray = [
            "visit123",
            "login",
            "logout",
            "attandance-in",
            "attandance-out",
            "traking-sync",
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
            "hour-sync",
            "addfollowup",
            "addleave"
        ];

        $.each(locations, function(i, v) {
            if ($("#tracking_sync").prop("checked") != true && v.mytype == 5) {
                // skip tracking sync pins if not checked
                latlngs.push([v.lat, v.lng]);
                return;
            }

            var pinName = (typePinArray[v.mytype] !== undefined) ? typePinArray[v.mytype] : "traking-sync";
            var iconUrl = pinico + pinName + ".png";
            if (i === location_count - 1) {
                iconUrl = pinico + "last-pin.png";
            }

            var customIcon = L.icon({
                iconUrl: iconUrl,
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -28]
            });

            var marker = L.marker([v.lat, v.lng], { icon: customIcon, title: v.type }).addTo(map);
            var popupContent = "<div class='map-popup-card' style='min-width:260px;max-width:340px;'>" +
                "<h4>" + (i + 1) + ") " + v.date + "</h4>" +
                "<p><b>Sales Person:</b> " + v.name + "</p>" +
                (v.type ? "<p><b>Type:</b> <span class='badge' style='background:#0b58a2;color:#fff;'>" + v.type + "</span></p>" : "") +
                "<p class='map-popup-address'><b>📍 Address:</b> " + (v.address ? v.address : "N/A") + "</p>" +
                "<p style='font-size:11px;color:#777;'><b>GPS:</b> " + v.lat + ", " + v.lng + "</p>" +
                "</div>";
            marker.bindPopup(popupContent, { autoPan: true, autoPanPadding: [50, 50], maxWidth: 360 });

            latlngs.push([v.lat, v.lng]);
        });

        if (latlngs.length > 0) {
            // Fetch real road route geometry from OSRM (Open Source Routing Machine) in batches of waypoints
            function buildOsrmRoute(points, callback) {
                if (points.length < 2) {
                    callback(points);
                    return;
                }
                // Limit waypoints for url to avoid length overflow (sample key checkpoints + all stops)
                var sampled = [];
                var maxWaypoints = 25;
                if (points.length <= maxWaypoints) {
                    sampled = points;
                } else {
                    var step = (points.length - 1) / (maxWaypoints - 1);
                    for (var s = 0; s < maxWaypoints; s++) {
                        var idx = Math.min(points.length - 1, Math.round(s * step));
                        if (sampled.length === 0 || sampled[sampled.length - 1] !== points[idx]) {
                            sampled.push(points[idx]);
                        }
                    }
                }

                var coordStr = sampled.map(function(pt) {
                    return pt[1].toFixed(6) + ',' + pt[0].toFixed(6); // lng,lat
                }).join(';');

                var osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' + coordStr + '?overview=full&geometries=geojson';
                
                $.ajax({
                    url: osrmUrl,
                    dataType: 'json',
                    timeout: 5000,
                    success: function(data) {
                        if (data && data.routes && data.routes.length > 0 && data.routes[0].geometry && data.routes[0].geometry.coordinates) {
                            var roadCoords = data.routes[0].geometry.coordinates.map(function(c) {
                                return [c[1], c[0]]; // lat,lng
                            });
                            callback(roadCoords);
                        } else {
                            callback(points);
                        }
                    },
                    error: function() {
                        callback(points); // fallback to recorded points
                    }
                });
            }

            buildOsrmRoute(latlngs, function(actualRoutePath) {
                // Swiggy/Zomato Style Glowing Road Path
                // 1. Casing / Shadow Glow
                var routeCasing = L.polyline(actualRoutePath, {
                    color: '#004c8f',
                    weight: 8,
                    opacity: 0.7,
                    lineCap: 'round',
                    lineJoin: 'round'
                }).addTo(map);

                // 2. Main Delivery Line (Cyan / Neon Blue like Zomato/Swiggy Delivery Route)
                routeLine = L.polyline(actualRoutePath, {
                    color: '#00d0ff',
                    weight: 5,
                    opacity: 0.95,
                    lineCap: 'round',
                    lineJoin: 'round'
                }).addTo(map);

                map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
                
                // User Attendance In Photo Avatar Badge with Pulse Animation along the Route
                if (actualRoutePath.length > 1) {
                    var userPhotoUrl = "<?= !empty($user_avatar) ? $user_avatar : SITEURL . 'images/noimage.png' ?>";
                    
                    var createAvatarBadge = function(angle) {
                        return '<div class="user-track-avatar-wrapper" style="position:relative;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">' +
                            // Outer Pulsing Aura
                            '<div style="position:absolute;width:52px;height:52px;border-radius:50%;background:rgba(11,88,162,0.3);animation:userAvatarPulse 1.6s infinite;"></div>' +
                            // Circular Attendance Photo Frame
                            '<div style="width:44px;height:44px;border-radius:50%;background:#ffffff;box-shadow:0 4px 12px rgba(0,0,0,0.4);border:2.5px solid #0b58a2;overflow:hidden;display:flex;align-items:center;justify-content:center;position:relative;z-index:2;">' +
                            '<img src="' + userPhotoUrl + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.src=\'../images/noimage.png\'">' +
                            '</div>' +
                            // Mini Floating Bike / Scooter Badge at the bottom-right corner
                            '<div class="mini-bike-badge" style="position:absolute;bottom:0px;right:0px;width:22px;height:22px;border-radius:50%;background:#fc8019;border:1.5px solid #ffffff;box-shadow:0 2px 5px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;z-index:3;transform:rotate(' + angle + 'deg);">' +
                            '<svg style="width:13px;height:13px;fill:#ffffff;" viewBox="0 0 24 24"><path d="M15.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM5 12c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5s1.6-3.5 3.5-3.5 3.5 1.6 3.5 3.5-1.6 3.5-3.5 3.5zm14-8.5c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5s1.6-3.5 3.5-3.5 3.5 1.6 3.5 3.5-1.6 3.5-3.5 3.5zm-8.2-7.8l-1.9-3.2c-.3-.5-.9-.8-1.5-.8h-3.4v2h2.6l1.2 2-3.1 5.3 1.7 1 3-5.2 2.7 4.5c.3.5.9.8 1.5.8h4.4v-2h-3.7l-3.5-5.9z"/></svg>' +
                            '</div>' +
                            '</div>';
                    };

                    if (!$('#user-avatar-track-style').length) {
                        $('head').append('<style id="user-avatar-track-style">@keyframes userAvatarPulse{0%{transform:scale(0.85);opacity:0.9;}70%{transform:scale(1.4);opacity:0;}100%{transform:scale(1.4);opacity:0;}}</style>');
                    }

                    // Pre-compute interpolated dense steps and angle bearings for smooth directional movement
                    var fullInterpolated = [];
                    function getBearing(p1, p2) {
                        var lat1 = p1[0] * Math.PI / 180;
                        var lat2 = p2[0] * Math.PI / 180;
                        var dLng = (p2[1] - p1[1]) * Math.PI / 180;
                        var y = Math.sin(dLng) * Math.cos(lat2);
                        var x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
                        var brng = Math.atan2(y, x) * 180 / Math.PI;
                        return (brng + 360) % 360;
                    }

                    for (var r = 0; r < actualRoutePath.length - 1; r++) {
                        var pt1 = actualRoutePath[r];
                        var pt2 = actualRoutePath[r + 1];
                        var heading = getBearing(pt1, pt2);
                        var dist = Math.sqrt(Math.pow(pt2[0] - pt1[0], 2) + Math.pow(pt2[1] - pt1[1], 2));
                        var subSteps = Math.max(15, Math.round(dist * 7000));
                        for (var st = 0; st < subSteps; st++) {
                            var factor = st / subSteps;
                            fullInterpolated.push({
                                lat: pt1[0] + (pt2[0] - pt1[0]) * factor,
                                lng: pt1[1] + (pt2[1] - pt1[1]) * factor,
                                angle: heading
                            });
                        }
                    }

                    if (fullInterpolated.length > 0) {
                        var initialItem = fullInterpolated[0];
                        var avatarIcon = L.divIcon({
                            className: 'user-track-avatar-marker',
                            html: createAvatarBadge(initialItem.angle),
                            iconSize: [56, 56],
                            iconAnchor: [28, 28]
                        });

                        var bikeMarker = L.marker([initialItem.lat, initialItem.lng], { icon: avatarIcon, zIndexOffset: 1000 }).addTo(map);

                        var animIndex = 0;
                        var animSpeedMs = 60; // Slow, realistic driving speed
                        var animInterval = window.setInterval(function() {
                            if (!map || !bikeMarker) {
                                window.clearInterval(animInterval);
                                return;
                            }
                            animIndex = (animIndex + 1) % fullInterpolated.length;
                            var currentPos = fullInterpolated[animIndex];
                            bikeMarker.setLatLng([currentPos.lat, currentPos.lng]);

                            // Rotate mini bike badge towards movement direction
                            var $miniBike = $(bikeMarker._icon).find('.mini-bike-badge');
                            if ($miniBike.length) {
                                $miniBike.css('transform', 'rotate(' + currentPos.angle + 'deg)');
                            }
                        }, animSpeedMs);
                    }
                }
            });
        }
    } else {
        if (map) {
            map.remove();
            map = null;
        }
        map = L.map('map').setView([22.2939994, 70.7892855], 13);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);
    }
}

function clearMarker() {
    locations = [];
    if (map) {
        map.remove();
        map = null;
    }
}
var locations = [];
var labels = [];
var status = "";
var waypts = [];
var result = <?php echo json_encode($response); ?>;
displayRecords();

function displayRecords() {

    if (result.ack == 1) {
        var locs = result.result;
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

function update_km(sales_id,route_date)
{
  $.ajax({
    type: "POST",
    url: "../service/service_sales_km_update.php?key=1226&s=197",
    data: 'sales_id=' + sales_id+'&route_date=' + route_date+'&save_flag=1',
 
    success: function(data) { 
        getDataMap(sales_id,'route'); 
    }
  }); 
}
</script>
<?php
$db->disconnect(); 
?>