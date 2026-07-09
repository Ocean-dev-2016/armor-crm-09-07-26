<?php  
   $page_id=400;$page_slug='dashboard';
   $ctable  = "salesexecutive_tracking";
   $ctable1   = "Sales Officer Tracking";
   $main_page   = $ctable;
   $page    = "manage_".$ctable;
   $page_title = "Manage ".$ctable1;
   include("connect.php");
   include("../include/class.sales_executive.php");
   // print_r($_REQUEST);exit;
   $id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
   $sales_executive=new SalesExecutive();
   $phone="";
   if($id=="")
   {
   // echo "String";;exit;
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
    //$response=$sales_executive->trackSalesAll($date);
    $response=array("ack"=>0,"ack_msg"=>"This Service is Not Available!!");
   }
   else
   {
    $name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
    $phone= " (".ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'")).") ";
    $sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
    $date=date('d-m-Y',strtotime($_REQUEST['date']));
    $response=$sales_executive->trackSalesAll($date,$id);
    /*print_r($response); exit;*/
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
    // status code over here
   }
   // print_r($response);exit;
?>
<style type="text/css">
  #map
  {
    height: <?=$_REQUEST['reqheight']?>px;
  }
</style>
<div class="portlet-body">
   <div class="row">
      <div class="col-md-6 col-xs-12 col-sm-3" style="margin-top: -15px">
         <span style="font-size: 15px">
         <?php if($id != "" && 1==2){ ?>
         <a style="height: 60px;">
         <img src="<?="../images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
         </a>
         <?php } ?>
         <!-- <?php echo $name.$phone." ON ".date('d-m-Y',strtotime($date)).""; ?> -->
         <?php echo $name.$phone; ?>
         </span>
         <input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
      </div>
   </div>
   <div id="results">
      <?php
      if($id=="")
      {
      ?>
      <div id="map"><h1>Please Select User To see Tracking.</h1></div>
      <?php
      } 
      else
      {
      ?>
      <div id="map"></div>
      <?php
      }
      ?>
   </div>
</div>
<script>
var map;
var markerCluster;
var markers = []

function initMap() {
    if (locations.length > 0) {
        var location_count = locations.length;
        var j = 0;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: {
                lat: locations[0].lat,
                lng: locations[0].lng
            },
        });
        var infowindow = new google.maps.InfoWindow();
        var bounds = new google.maps.LatLngBounds();
        $.each(locations, function(i, v) {
            var marker = new google.maps.Marker({
                position: {
                    lat: locations[i].lat,
                    lng: locations[i].lng
                },
                map: map,
                icon: '../images/pin/last-pin.png',
                title: locations[i]['type'],

            });
            bounds.extend(marker.getPosition());
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    if (locations[i]['status'] == "offline") {
                        var color = "red";
                    } else if (locations[i]['status'] == "online") {
                        var color = "green";
                    }
                    //alert(locations[i]['address']);
                    /*if(locations[i]['address']==undefined){
                        var address = "";
                    }else{
                        var address = locations[i]['address'];
                    }*/
                    // infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] + "<p><b>Address</b>:" + locations[i]['address'] + "<br/><p><b>Name:" + locations[i]['name'] + "<br/><a style='color:" + color + "'>" + locations[i]['status'] + "</a></b></h4></p>");
                    infowindow.setContent("<h1>" + (i + 1) + ") " + locations[i]['date'] + "</h1><p>Lat:" + locations[i]['lat'] + "<br/> Long:" + locations[i]['lng'] + " <br/>" + locations[i]['type'] + "<p><b>Address</b>:" + locations[i]['address'] + "<br/><p><b>Name:" + locations[i]['name'] +"</b></h4></p>");
                    infowindow.open(map, marker);
                }
            })(marker, i));
        })
        map.fitBounds(bounds);
        var flightPath = new google.maps.Polyline({
            // path: locations,
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
    var date = $("#date").val();
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
<?php
$db->disconnect(); 
?>