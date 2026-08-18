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
var map = null;

function initMap() {
    if (locations.length > 0) {
        var location_count = locations.length;
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

        var bounds = [];
        $.each(locations, function(i, v) {
            var customIcon = L.icon({
                iconUrl: pinico + 'last-pin.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            var marker = L.marker([v.lat, v.lng], { icon: customIcon, title: v.type }).addTo(map);
            var popupContent = "<h4><b>" + (i + 1) + ") " + v.date + "</b></h4><p><b>Lat:</b> " + v.lat + "<br/><b>Long:</b> " + v.lng + "<br/><b>Type:</b> " + v.type + "<br/><b>Address:</b> " + v.address + "<br/><b>Name:</b> " + v.name + "</p>";
            marker.bindPopup(popupContent);
            bounds.push([v.lat, v.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [30, 30] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], 16);
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
    var date = $("#date").val();
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
                address: v.address,
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