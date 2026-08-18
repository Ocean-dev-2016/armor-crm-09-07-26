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
            routeLine = L.polyline(latlngs, {
                color: '#00d0ff',
                weight: 6,
                opacity: 0.95,
                smoothFactor: 1
            }).addTo(map);
            map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });

            // Animated Moving Pulse Pin along the route
            if (latlngs.length > 1) {
                var animCircle = L.circleMarker(latlngs[0], {
                    radius: 9,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: '#ff0055',
                    fillOpacity: 1
                }).addTo(map);

                var animIndex = 0;
                var animInterval = window.setInterval(function() {
                    if (!map || !animCircle) {
                        window.clearInterval(animInterval);
                        return;
                    }
                    animIndex = (animIndex + 1) % latlngs.length;
                    animCircle.setLatLng(latlngs[animIndex]);
                }, 120);
            }
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