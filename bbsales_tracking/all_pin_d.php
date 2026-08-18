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

        map = L.map('map').setView([locations[0].lat, locations[0].lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

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
            var popupContent = "<div style='min-width:220px;'><h4><b>" + (i + 1) + ") " + v.date + "</b></h4><p style='margin:0;font-size:12px;line-height:1.4;'><b>Lat:</b> " + v.lat + "<br/><b>Long:</b> " + v.lng + "<br/><b>Type:</b> " + v.type + "<br/><b>Address:</b> " + v.address + "<br/><b>Name:</b> " + v.name + "</p></div>";
            marker.bindPopup(popupContent, { autoPan: true, autoPanPadding: [50, 50], maxWidth: 300 });

            latlngs.push([v.lat, v.lng]);
        });

        if (latlngs.length > 0) {
            routeLine = L.polyline(latlngs, {
                color: '#005db5',
                weight: 5,
                opacity: 0.9,
                smoothFactor: 1
            }).addTo(map);
            map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
        }
    } else {
        if (map) {
            map.remove();
            map = null;
        }
        map = L.map('map').setView([22.2939994, 70.7892855], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
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