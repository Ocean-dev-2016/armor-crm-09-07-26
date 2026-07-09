<?php
   $page_id=400;$page_slug='dashboard';
   $ctable  = "salesexecutive_tracking";
   $ctable1   = "Sales Officer Tracking";
   $main_page   = $ctable;
   $page    = "manage_".$ctable;
   $page_title = "Manage ".$ctable1;
   include("connect.php");
   $id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
   $date=date('Y-m-d',strtotime($_REQUEST['date']));
   
   $originalR=$db->rp_getData("salesexecutive_tracking","*","sales_executive_id='".$id."' AND date(date)='".$date."'","",0);
   $total_row_o=mysqli_num_rows($originalR);
   $original_data = "";
   while($routeo=mysqli_fetch_assoc($originalR))
   {
      $original_data .= $routeo['latitude'].",".$routeo['longitude']."|";
   }

   
   $response=$db->rp_getData("snapped_calls","*","sales_executive_id='".$id."' AND date='".$date."'","",0);
   $total_row=mysqli_num_rows($response);
   $result = array();
    while($route=mysqli_fetch_assoc($response))
    {
      $result[]=$route;
    }
    // echo json_encode($result);exit;
?>
<style type="text/css">
  #map
  {
    height: <?=$_REQUEST['reqheight']?>px;
  }
</style>
<div class="portlet-body">
   <div id="results">
      <div id="map"></div>
   </div>
</div>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=AIzaSyADYWIGFSnn3DHlJblK0hntz5KQiwbD0hk"></script>
<script>
  var result = '<?php echo json_encode($result); ?>';
  var total_row = '<?php echo json_encode($total_row); ?>';
  var total_row_o = '<?php echo json_encode($total_row_o); ?>';
  var coords = '<?php echo json_encode($original_data); ?>';
  google.maps.event.addDomListener(window, 'load');
  var originalIndexes = [];
  var snappedCoordinates = [];
  var placeIds  = [];
  var distPolylines   = [];

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

function fitBounds(markers) {
  var bounds = new google.maps.LatLngBounds;
  for (var i = 0; i < markers.length; i++) {
    bounds.extend(markers[i].getPosition());
  }
  map.fitBounds(bounds);
}
  /*function getType(originalIndexes, originalCoordsLength) {
    var unsnappedPoints1 = [];
    var typeArray = data1.split('|');
    var hasMissingCoords = false;
    for (var i = 0; i < originalCoordsLength; i++) {
      if (originalIndexes.indexOf(i) < 0) {
        hasMissingCoords = true;
        var type = {
          'type': typeArray[i]
        };

        unsnappedPoints1.push(type);
        type.unsnapped = true;
      }
    }
    return unsnappedPoints1;
  }*/

  function getMissingPoints(originalIndexes, originalCoordsLength) {
      var unsnappedPoints = [];
      var coordsArray = coords.split('|');
      var hasMissingCoords = false;
      for (var i = 0; i < originalCoordsLength; i++) {
        if (originalIndexes.indexOf(i) < 0) {
          hasMissingCoords = true;
          var latlng = {
            'lat': parseFloat(coordsArray[i].split(',')[0]),
            'lng': parseFloat(coordsArray[i].split(',')[1])
          };

          unsnappedPoints.push(latlng);
          latlng.unsnapped = true;
        }
      }
      return unsnappedPoints;
  }

  for (var i = 0; i < result.length; i++) {
    var latlng = {
      'lat': result[i].latitude,
      'lng': result[i].longitude
    };
    var interpolated = true;
    originalIndexes.push(result[i].originalIndex);
    latlng.interpolated = interpolated;
    snappedCoordinates.push(latlng);
    placeIds.push(result[i].placeId);
  }
  var unsnappedPoints = getMissingPoints(
      originalIndexes,
      total_row
  );
  /*var unsnappedPoints1 = getType(
      originalIndexes,
      total_row
  );*/
  if (coords) {
    for (var i = 0; i < distPolylines.length; i++) {
        distPolylines[i].setVisible(!distPolylines[i].getVisible());
    }
  }
  else
  {
      toastr.error("no data available");
  }

  var snappedPolyline = new google.maps.Polyline({
    path: snappedCoordinates,
    strokeColor: '#005db5',
    strokeWeight: 6,
    icons: [{
      icon: lineSymbol,
      offset: '100%'
    }]
  });

  snappedPolyline.setMap(map);
  animateCircle(snappedPolyline);

  polylines.push(snappedPolyline);
  fitBounds(markers)
</script>
<?php
$db->disconnect(); 
?>