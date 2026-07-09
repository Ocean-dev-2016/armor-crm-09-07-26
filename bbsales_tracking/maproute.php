<?php
// print_r($_REQUEST);exit;
$page_id=562;$page_slug='page_category';
$ctable   = "salesexecutive_tracking";
$ctable1  = "Sales Officer Tracking";
$main_page  = $ctable;
$page     = "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");

include("../include/class.sales_executive.php");
$id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
if($id=="")
{
  $system->addErrorMessage("No Sales Officer Found!!");
  $db->rp_location("sales_executive_manage.php");
}
$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
$phone=ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'"));
$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
$date=date('d-m-Y',strtotime($_REQUEST['date']));
$date_new=date('Y-m-d',strtotime($_REQUEST['date']));
$sales_executive=new SalesExecutive();
$response=$sales_executive->trackSales($id,$date);

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
  // status code over here

$track = $db->rp_getData("salesexecutive_tracking","*","isDelete=0 AND DATE(date)='".$date_new."' AND sales_executive_id=".$id."","",0,100);

$data = "";
$data1 = "";

while($Track_data = mysqli_fetch_assoc($track))
{
  $data .= $Track_data['latitude'].",".$Track_data['longitude']."|";
  $data1 .= $Track_data['type']."|";
}
$data = rtrim($data,"|");
$data1 = rtrim($data1,"|");
$data .= "'";
// var eg4 = '36.28881,-80.8525|36.287038,-80.85313|36.286161,-80.85369|' +
    // '36.28654,-80.85418|36.2846,-80.84766|36.28355,-80.84669';
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
    <style type="text/css">
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: Roboto, Noto, sans-serif;
      }

      #map {
        height: 500px;
      }

      #interpolate {
        width: 2em;
        height: 2em;
      }

      #coords {
        resize: vertical;
        min-height: 75px;
        max-height: 200px;
      }

      .block {
        clear: both;
        margin: 1.5em auto;
        text-align: center;
      }

      #legend {
        float: center;
        margin: 5px 15px;
        font-size: 13px;
      }

      .button {
      display: inline-block;
      position: relative;
      border: 0;
      padding: 0 1.7em;
      min-width: 120px;
      height: 32px;
      line-height: 32px;
      border-radius: 2px;
      font-size: 0.9em;
      background-color: #fff;
      color: #646464;
    }

    .button.narrow {
      width: 60px;
    }

    .button.grey {
      background-color: #eee;
    }

    .button.blue {
      background-color: #4285f4;
      color: #fff;
    }

    .button.green {
      background-color: #0f9d58;
      color: #fff;
    }

    .button.raised {
      transition: box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      transition-delay: 0.2s;
      box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
    }

    .button.raised:active {
      box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2);
      transition-delay: 0s;
    }

    .floating {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 5;
      background-color: rgba(255, 255, 255, 0.75);
      padding: 1px;
      border: 1px solid #999;
      text-align: center;
      line-height: 18px;
    }

    .floating.panel {
      width: 400px;
    }

    .coords-small {
      width: 350px;
    }

    .coords-large {
      width: 400px;
    }

    .button-div {
      padding: 0px 50px;
      width: 300px;
      line-height: 40px;
    }

    #toggle {
      width: 25px;
      z-index: 10;
      cursor: default;
      font-size: 2em;
      padding: 1px;
      color: #999;
      display: none;
    }

    </style>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
    <?php include("include_js.php"); ?>
    <?php include("include_css.php"); ?>

    <!-- google key -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=AIzaSyADYWIGFSnn3DHlJblK0hntz5KQiwbD0hk"></script>

    <!-- our key -->
    <!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=AIzaSyBZ83CrCOo-fDxIlr1Q5nBaXM9adPljjKQ"></script> -->
    <script src="/_static/js/jquery-bundle.js"></script>
    
    <script>

    // Replace with your own API key

    // google key
    var API_KEY = 'AIzaSyADYWIGFSnn3DHlJblK0hntz5KQiwbD0hk';

    // our key
    // var API_KEY = 'AIzaSyBZ83CrCOo-fDxIlr1Q5nBaXM9adPljjKQ';

    // Icons for markers
    // var RED_MARKER = 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';
    // var GREEN_MARKER = 'https://maps.google.com/mapfiles/ms/icons/green-dot.png';
    // var BLUE_MARKER = 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
    // var YELLOW_MARKER = 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';

    // URL for places requests
    var PLACES_URL = 'https://maps.googleapis.com/maps/api/place/details/json?' +
                    'key=' + API_KEY + '&placeid=';

    // URL for Speed limits
    var SPEED_LIMIT_URL = 'https://roads.googleapis.com/v1/speedLimits';

    var coords;

    /**
     * Current Roads API threshold (subject to change without notice)
     * @const {number}
     */
    var DISTANCE_THRESHOLD_HIGH = 300;
    var DISTANCE_THRESHOLD_LOW = 200;

    /**
     * @type Array<ExtendedLatLng>
     */
    var originals = [];     // the original input points, a list of ExtendedLatLng

    var interpolate = true;
    var map;
    var placesService;
    var originalCoordsLength;

    // Settingup Arrays
    var infoWindows = [];
    var markers = [];
    var placeIds = [];
    var polylines = [];
    var snappedCoordinates = [];
    var distPolylines = [];

    // Symbol that gets animated along the polyline
    var lineSymbol = {
      path: google.maps.SymbolPath.CIRCLE,
      scale: 8,
      strokeColor: '#005db5',
      strokeWidth: '#005db5'
    };
   var eg4 = '<?=$data;?>';
   var data1 = '<?=$data1;?>';

    // Initialize
    function initialize() {
      $('#eg4').click(function(e) {
        $('#coords').val(eg4);
        $('#data1').val(data1);
        $('#plot').trigger('click');
      });

      $('#toggle').click(function(e) {
         if ($('#panel').css("display") != 'none') {
            $('#toggle').html("+");
            $('#panel').hide();
         } else {
            // $('#toggle').html("&mdash;");
            $('#toggle').html("-");
            $('#panel').show();
         }
      });

      // Centre the map on Sydney
      var mapOptions = {
        center: {'lat': 22.2939994, 'lng': 70.7892855},
        zoom: 14
      };

      // Map object
      map = new google.maps.Map(document.getElementById('map'), mapOptions);

      // Places object
      placesService = new google.maps.places.PlacesService(map);

      // Reset the map to a clean state and reset all variables
      // used for displaying each request
      function clearMap() {
        // Clear the polyline
        for (var i = 0; i < polylines.length; i++) {
          polylines[i].setMap(null);
        }
        // Clear all markers
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(null);
        }
        // Clear all the distance polylines
        for (var i = 0; i < distPolylines.length; i++) {
          distPolylines[i].setMap(null);
        }
        // Clear all info windows
        for (var i = 0; i < infoWindows.length; i++) {
          infoWindows[i].close();
        }

        // Empty everything
        polylines = [];
        markers = [];
        distPolylines = [];
        snappedCoordinates = [];
        placeIds = [];
        infoWindows = [];
        $('#unsnappedPoints').empty();
        $('#warningMessage').empty();
      }

      // Parse the value in the input element
      // to get all coordinates
      function parseCoordsFromQuery(input) {
        var coords;
        input = decodeURIComponent(input);
        if (input.split('path=').length > 1) {
          input = decodeURIComponent(input);
          // Split on the ampersand to get all params
          var parts = input.split('&');
          // Check each part to see if it starts with 'path='
          // grabbing out the coordinates if it does
          for (var i = 0; i < parts.length; i++) {
            if (parts[i].split('path=').length > 1) {
              coords = parts[i].split('path=')[1];
              break;
            }
          }
        } else {
          coords = decodeURIComponent(input);
        }

        // Parse the "Lat,Lng|..." coordinates into an array of ExtendedLatLng
        originals = [];
        originals1 = [];
        var points = coords.split('|');
        var data11 = data1.split('|');
        for (var i = 0; i < points.length; i++) {
          var point = points[i].split(',');
          originals.push({lat: Number(point[0]), lng: Number(point[1]), index:i});
          originals1.push({type: data11[i], index:i});
        }
        return coords;
      }

      // Clear the map of any old data and plot the request
      $('#plot').click(function(e) {
        clearMap();
        bendAndSnap();
        drawDistance();
        e.preventDefault();
      });

      // Make AJAX request to the snapToRoadsAPI
      // with coordinates parsed from text input element.
      function bendAndSnap() {
        interpolate= $('#interpolate').is(':checked');
        coords = parseCoordsFromQuery($('#coords').val());
        if (coords) {
          // location.hash = coords;
          $.ajax({
            type: 'GET',
            url: 'https://roads.googleapis.com/v1/snapToRoads',
            data: {
              interpolate: $('#interpolate').is(':checked'),
              key: API_KEY,
              path: coords
            },
            success: function(data) {
              $('#requestURL').html('<a target="blank" href="' +
                  this.url + '">Request URL</a>');
              processSnapToRoadResponse(data);
              drawSnappedPolyline(snappedCoordinates);
              // drawOriginals(originals,originals1);
              fitBounds(markers);
            },
            error: function() {
              $('#requestURL').html('<strong>That query didn\'t work :(</strong>' +
                  '<p>Try looking at the <a href="' + this.url +
                  '">Request URL</a></p>');
              // toastr.error("no data available");
              clearMap();
            }
          });
        }
        else
        {
              toastr.error("no data available");
        }
      }

      // Toggle the distance polylines of the original points to show on the maps
      $('#distance').click(function(e) {
        if (coords) {
            for (var i = 0; i < distPolylines.length; i++) {
                distPolylines[i].setVisible(!distPolylines[i].getVisible());
            }
            // Clear all infoWindows associated with distance polygons on toggle
            for (var i = 0; i < infoWindows.length; i++) {
              if (infoWindows[i].dist) {
                infoWindows[i].close();
              }
            }
            e.preventDefault();
        }
        else
        {
              toastr.error("no data available");
        }
      });

      /**
       * Compute the distance between each original point and create a polyline
       * for each pair. Polylines are initially hidden on creation
       */
      function drawDistance() {
        for (var i = 0; i < originals.length - 1; i++) {
          var origin = new google.maps.LatLng(originals[i]);
          var destination = new google.maps.LatLng(originals[i+1]);
          var distance =
        google.maps.geometry.spherical.computeDistanceBetween(origin, destination);

          // Round the distance value to two decimal places
          distance = Math.round(distance * 100) / 100;

          var color;
          var weight;
          if (distance > DISTANCE_THRESHOLD_HIGH) {
            color = '#CC0022';
            weight = 7;
          } else if (distance < DISTANCE_THRESHOLD_HIGH &&
                     distance > DISTANCE_THRESHOLD_LOW) {
            color = '#FF6600';
            weight = 6;
          } else {
            color = '#22CC00';
            weight = 5;
          }
          var polyline = new google.maps.Polyline({
            strokeColor: color,
            strokeOpacity: 0.4,
            strokeWeight: weight,
            geodesic: true,
            visible: false,
            map: map
          });
          polyline.setPath([origin, destination]);

          distPolylines.push(polyline);
          infoWindows.push(addPolyWindow(polyline, distance, i));
        }
      }

      /**
       * Add an info window to the polyline displaying the original
       * points and the distance
       */
      function addPolyWindow(polyline, distance, index) {
        var infoWindow = new google.maps.InfoWindow();
        var content = '<div style="width:100%"><p>' +
            '<strong>Original Index: </strong>' + index + '<br>' +
            '<strong>Coords:</strong> (' +
            originals[index].lat + ',' + originals[index].lng + ')' +
            '<br>to<br>' +
            '<strong>Original Index: </strong>' + (index+1) + '<br>' +
            '<strong>Coords:</strong> (' +
            originals[index+1].lat + ',' + originals[index+1].lng + ')';
        if (distance > DISTANCE_THRESHOLD_HIGH) {
          content += '<span style="color:#CC0022;font-style:italic">' +
              '*Large distance (>300m) may affect snapping</span><br>' +
              'Please see <a href="https://developers.google.com/maps/' +
              'documentation/roads/snap#parameter_usage" ' +
              'target="_blank">Roads API documentation</a>';
        }
        content += '</p></div>';

        infoWindow.setContent(content);
        infoWindow.dist = true;

        polyline.addListener('click', function(e) {
            infoWindow.setPosition(e.latLng);
            infoWindow.open(map);
        });

        polyline.addListener('mouseover', function(e) {
            polyline.setOptions({strokeOpacity: 1.0});
        });

        polyline.addListener('mouseout', function(e) {
            polyline.setOptions({strokeOpacity: 0.4});
        });

        return infoWindow;
      }

      // Parse the value in the input element
      // to get all coordinates
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

      function getType(originalIndexes, originalCoordsLength) {
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
      }

      // Parse response from snapToRoads API request
      // Store all coordinates in response
      // Calls functions to add markers to map for unsnapped coordinates
      function processSnapToRoadResponse(data) {
        var obj = JSON.stringify(data);
        // alert(obj);
        var originalIndexes = [];
        var unsnappedMessage = '';
        for (var i = 0; i < data.snappedPoints.length; i++) {
          var latlng = {
            'lat': data.snappedPoints[i].location.latitude,
            // 'lat': 22.2797087,
            'lng': data.snappedPoints[i].location.longitude
            // 'lng': 70.7704936
          };
          var interpolated = true;

          // if (data.snappedPoints[i].originalIndex != undefined) {
            interpolated = false;
            // interpolated = false;
            originalIndexes.push(data.snappedPoints[i].originalIndex);
            // latlng.originalIndex = data.snappedPoints[i].originalIndex;
          // }
          /*else
          {
            data.snappedPoints[i].originalIndex = i;
            interpolated = false;
            originalIndexes.push(data.snappedPoints[i].originalIndex);
            // latlng.originalIndex = data.snappedPoints[i].originalIndex;
          }*/

          latlng.interpolated = interpolated;
          snappedCoordinates.push(latlng);
          placeIds.push(data.snappedPoints[i].placeId);

          // Cross-reference the original point and this snapped point.
          // latlng.related = originals[latlng.originalIndex];
          // originals[latlng.originalIndex].related = latlng;
        }
        var unsnappedPoints = getMissingPoints(
            originalIndexes,
            coords.split('|').length
        );
        var unsnappedPoints1 = getType(
            originalIndexes,
            data1.split('|').length
        );
        // for (var i = 0; i < unsnappedPoints.length; i++) {
        //   var marker = addMarker(unsnappedPoints[i]);
        //   var infowindow = addBasicInfoWindow(marker, unsnappedPoints[i], i, unsnappedPoints1[i]);
        //   infoWindows.push(infowindow);

        //   unsnappedMessage += unsnappedPoints[i].lat + ',' +
        //       unsnappedPoints[i].lng + '<br>';
        // }

        if (unsnappedPoints.length) {
          unsnappedMessage = '<strong>' +
             'These points weren\'t snapped: ' +
             '</strong><br>' + unsnappedMessage;
          $('#unsnappedPoints').html(unsnappedMessage);
        }

        if (data.warningMessage) {
          $('#warningMessage').html('<span style="color:#CC0022;' +
              'font-style:italic;font-size:12px">' + data.warningMessage + '<br/>' +
              '<a target="_blank" href="https://developers.google.com/maps/' +
              'documentation/roads/snap">https://developers.google.com/maps/' +
              'documentation/roads/snap</a>');
          $('#distance').trigger('click');
        }
      }

      // Draw the polyline for the snapToRoads API response
      // Call functions to add markers and infowindows for each snapped
      // point along the polyline.
      function drawSnappedPolyline(snappedCoords) {
        var snappedPolyline = new google.maps.Polyline({
          path: snappedCoords,
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


        for (var i = 0; i < snappedCoords.length; i++) {
          var marker = addMarker(snappedCoords[i]);
          var infoWindow = addDetailedInfoWindow(marker,
              snappedCoords[i],
              placeIds[i]);
          infoWindows.push(infoWindow);
        }
      }

      // Draw the original input.
      // Call functions to add markers and infowindows for each point.
      function drawOriginals(originalCoords,originalCoords1) {
        // for (var i = 0; i < originalCoords.length; i++) {
        //   var marker = addMarker(originalCoords[i]);
        //   // var typePunch = "sync";
        //   var infoWindow = addBasicInfoWindow(marker, originalCoords[i], i, originalCoords1[i]);
        //   infoWindows.push(infoWindow);
        // }
      }

      // Infowindow used for unsnappable coordinates
      function addBasicInfoWindow(marker, coords, index, typePunch) {
        var infowindow = new google.maps.InfoWindow();
        var content = '<div style="width:99%"><p>' +
            '<strong>Lat/Lng:</strong><br>' +
            '(' + coords.lat + ',' + coords.lng + ')<br>' +
            (index != undefined ? '<strong>Index: </strong>' + (index+1) : '') +'<br>'+(typePunch.type != undefined ? '<strong>Puch Type: </strong>' + typePunch.type : '') +
            '</p></div>';

        infowindow.setContent(content);

        google.maps.event.addListener(marker, 'click', function() {
          openInfoWindow(infowindow, marker);
        });

        return infowindow;
      }

      // Infowindow used for snapped points
      // Makes request to Places Details API to get data about each
      // Place ID.
      // Requests speed limit of each location using Roads SpeedLimit API
      function addDetailedInfoWindow(marker, coords, placeId) {
        var infowindow = new google.maps.InfoWindow();
        var placesRequestUrl = PLACES_URL + placeId;
        var detailsUrl = '<a target="_blank" href="' +
            placesRequestUrl + '">' +
            placeId + '</a></li>';

        // On click we make a request to the Places API
        // This is to avoid OVER_QUERY_LIMIT if we just requested everything
        // at the same time
        google.maps.event.addListener(marker, 'click', function() {
          content = '<div style="width:99%"><p>';

          function finishInfoWindow(placeDetails) {
            content += '<strong>Place Details: </strong>' + placeDetails + '<br>' +
                '<strong>' +
                (coords.interpolated ? 'Coords' : 'Snapped coords') +
                ': </strong>' +
                '(' + coords.lat.toFixed(7) + ',' +
                coords.lng.toFixed(7) + ')<br>';

            if (!(coords.interpolated)) {
              var original = originals[coords.originalIndex];
              content += '<strong>Original coords: </strong>' +
                  '(' + original.lat + ',' + original.lng + ')<br>' +
                  '<strong>Original Index: </strong>' +
                  coords.originalIndex;
            }
            content += '</p></div>';
            infowindow.setContent(content);
            openInfoWindow(infowindow, marker);
          };

          getPlaceDetails(placeId, function(place) {
            if (place.name) {
              content += '<strong>' + place.name + '</strong><br>';
            }
            getSpeedLimit(placeId, function(data) {
              if (data.speedLimits) {
                content += '<strong>Speed Limit: </strong>' +
                    data.speedLimits[0].speedLimit + ' km/h <br>';
              }
              finishInfoWindow(detailsUrl);
            });
          }, function() { finishInfoWindow("<em>None available</em>"); });
        });
        return infowindow;
      }

      // Avoid infoWindows staying open if the pano changes
      listenForPanoChange();

      // If the user came to the page with a particular path or URL,
      // immediately plot it.
      // if (location.hash.length > 1) {
      //   coords = parseCoordsFromQuery(location.hash.slice(1));
      //   $('#coords').val(coords);
      //   $('#plot').click();
      // }
    } // End init function

    // Call the initialize function once everything has loaded
    google.maps.event.addDomListener(window, 'load', initialize);

    // Load the control panel in a floating div if it is not loaded in an iframe
    // after the textarea has been rendered
    $("#coords").ready(function() {
        if (!window.frameElement) {
           $('#panel').addClass("floating panel");
           $('#button-div').addClass("button-div");
           $('#coords').removeClass("coords-large").addClass("coords-small");
           $('#toggle').show();
           $('#map').height('100%');
        }
    });

        /**
    *  latlng literal with extra properties to use with the RoadsAPI
    *  @typedef {Object} ExtendedLatLng
    *   lat:string|float
    *   lng:string|float
    *   interpolated:boolean
    *   unsnapped:boolean
    */

    /**
     * Add a line to the map for highlighting the connection between two
     * markers while the mouse is over it.
     * @param {ExtendedLatLng} from - The origin of the line
     * @param {ExtendedLatLng} to - The destination of the line
     * @return {!Object} line - the polyline object created
     */
    function addOverline(from, to) {
      return addLine("overline", from, to, '#ff77ff', 4, 1.0, 2.0, false);
    }

    /**
     * Add a line to the map for highlighting the connection between two
     * markers while the mouse is NOT over it.
     * @param {ExtendedLatLng} from - The origin of the line
     * @param {ExtendedLatLng} to - The destination of the line
     * @return {!Object} line - the polyline object created
     */
    function addOutline(from, to) {
      // return addLine("outline", from, to, '#bb33bb', 2, 0.5, 1.35, true);
    }

    /**
     * Add a line to the map for highlighting the connection between two
     * markers.
     * @param {string}         attrib  - The attribute to use for managing the line
     * @param {ExtendedLatLng} from    - The origin of the line
     * @param {ExtendedLatLng} to      - The destination of the line
     * @param {string}         color   - The color of the line
     * @param {number}         weight  - The weight of the line
     * @param {number}         opacity - The opacity of the line (0..1)
     * @param {number}         scale   - The scale of the arrow-head (pt)
     * @param {boolean}        visible - The visibility of the line
     * @return {!Object}       line    - the polyline object created
     */
    function addLine(attrib, from, to, color, weight, opacity, scale, visible) {
      from[attrib] = new google.maps.Polyline({
        path:         [from, to],
        strokeColor:  color,
        strokeWeight:  weight,
        strokeOpacity: opacity,
        icons:[{
          offset: "0%",
          icon: {
            scale: scale/*pt*/,
            path:  google.maps.SymbolPath.BACKWARD_CLOSED_ARROW
          }
        }]
      });
      from[attrib].setVisible(visible);
      from[attrib].setMap(map);
      to[attrib] = from[attrib];
      polylines.push(from[attrib]);
      return from[attrib];
    }

    /**
     * Add a pair of lines to the map for highlighting the connection between two
     * markers; one visible while the mouse is over the marker (the "overline"),
     * the other while it is not (the "outline").
     * @param {ExtendedLatLng} from - The origin of the line (the original input)
     * @param {ExtendedLatLng} to - The destination of the line (the snapped point)
     * @return {!Object} line - the polyline object created
     */
    function addCorrespondence(coords, marker) {
      if (!coords.overline) { addOverline(coords, coords.related); }
      if (!coords.outline)  { addOutline(coords, coords.related); }

      marker.addListener('mouseover', function(mevt) {
        coords.outline.setVisible(false);
        coords.overline.setVisible(true);
        coords.related.marker.setOpacity(1.0);
      });
      marker.addListener('mouseout', function(mevt) {
        coords.overline.setVisible(false);
        coords.outline.setVisible(true);
        coords.related.marker.setOpacity(0.5);
      });
    }

    /**
     * Add a marker to the map and check for special 'interpolated'
     * and 'unsnapped' properties to control which colour marker is used
     * @param {ExtendedLatLng} coords - Coords of where to add the marker
     * @return {!Object} marker - the marker object created
     */
    function addMarker(coords) {
      var marker = new google.maps.Marker({
        position: coords,
        title: coords.lat + ',' + coords.lng,
        map: map,
        opacity: 0.5,
        icon: RED_MARKER
      });

      // Coord should NEVER be interpolated AND unsnapped
      if (coords.interpolated) {
        marker.setIcon(BLUE_MARKER);
      } else if (!coords.related) {
        marker.setIcon(YELLOW_MARKER);
      } else if (coords.originalIndex != undefined) {
        marker.setIcon(RED_MARKER);
        addCorrespondence(coords, marker);
      } else {
        marker.setIcon({url: GREEN_MARKER,
                        scaledSize: {width: 20, height: 20}});
        addCorrespondence(coords, marker);
      }

      // Make markers change opacity when the mouse scrubs across them
      marker.addListener('mouseover', function(mevt) {
        marker.setOpacity(1.0);
      });
      marker.addListener('mouseout', function(mevt) {
        marker.setOpacity(0.5);
      });

      coords.marker = marker;  // Save a reference for easy access later
      markers.push(marker);

      return marker;
    }

    /**
     * Animate an icon along a polyline
     * @param {Object} polyline The line to animate the icon along
     */
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

    /**
     * Fit the map bounds to the current set of markers
     * @param {Array<Object>} markers Array of all map markers
     */
    function fitBounds(markers) {
      var bounds = new google.maps.LatLngBounds;
      for (var i = 0; i < markers.length; i++) {
        bounds.extend(markers[i].getPosition());
      }
      map.fitBounds(bounds);
    }

    /**
     * Uses Places library to get Place Details for a Place ID
     * @param {string}   placeId         The Place ID to look up
     * @param {Function} foundCallback   Called if the place is found
     * @param {Function} missingCallback Called if nothing is found
     * @param {Function} errorCallback   Called if request fails
     */
    function getPlaceDetails(placeId,foundCallback, missingCallback, errorCallback) {
      var request = {
        placeId: placeId
      };

      placesService.getDetails(request, function(place, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          foundCallback(place);
        } else if (status == google.maps.places.PlacesServiceStatus.NOT_FOUND) {
          missingCallback();
        } else if (errorCallback) {
          errorCallback();
        }
      });
    }

    /**
     * AJAX request to the Roads Speed Limit API.
     * Request the speed limit for the Place ID
     * @param {string}   placeId         Place ID to request the speed limit for
     * @param {Function} successCallback Called if request is successful
     * @param {Function} errorCallback   Called if request fails
     */
    function getSpeedLimit(placeId, successCallback, errorCallback) {
      // $.ajax({
      //   type: 'GET',
      //   url: SPEED_LIMIT_URL,
      //   data: {
      //     placeId: placeId,
      //     key: API_KEY
      //   },
      //   success: successCallback,
      //   error: errorCallback
      // });
    }

    /**
     * Open an infowindow on either the map or the active streetview pano
     * @param {Object} infowindow Infowindow to be opened
     * @param {Object} marker Marker the infowindow is anchored to
     */
    function openInfoWindow(infowindow, marker) {
      // If streetView is visible display the infoWindow over the pano
      // and anchor to the marker
      if (map.getStreetView().getVisible()) {
        infowindow.open(map.getStreetView(), marker);
      }
      // Otherwise open it on the map and anchor to the marker
      else {
        infowindow.open(map, marker);
      }
    }

    /**
     * Add event listener to for when the active pano changes
     */
    function listenForPanoChange() {
      var pano = map.getStreetView();

      // Close all open markers when the pano changes
      google.maps.event.addListener(pano, 'position_changed', function() {
        closeAllInfoWindows(infoWindows);
      });
    }

    /**
     * Close all open infoWindows
     * @param {Array<Object>} infoWindows - all infowindow objects
     */
    function closeAllInfoWindows(infoWindows) {
      for (var i = 0; i < infoWindows.length; i++) {
        infoWindows[i].close();
      }
    }

    </script>
  </head>
  <body>

    <div class="row">
      <div class="col-md-6 col-xs-12 col-sm-3" style="margin-top:10px;margin-left: 10px;">
        <h4>
          <?php if($id != ""){ ?>
            <a style="margin-left: 10px;height: 60px;">
                <img src="<?=SITEURL."images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
            </a>
          <?php } ?>
            <?php echo $name." (".$phone.") "."Date:-".date('d-m-Y',strtotime($_REQUEST['date'])).""; ?></h4>
        <input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
      </div>
    </div>
    <table cellpadding="2" style="margin-left: 10px;">
    <tr>
      <td><a class="btn btn-warning" href="<?=SITEURL?>">Back To <i class="icon-home"></i></i></i></a></td>
      <td>&nbsp;&nbsp; &nbsp;&nbsp;Search By Executive & Date : &nbsp;&nbsp; &nbsp;&nbsp; 
      </td>
      <td>
        <select style="width:230px !important;" class="form-control" onchange="getByDate('exe_id')" name="exicutive_id" id="exicutive_id">
        <option value="">Select Executive</option>
        <?php
          $data = $db->rp_getData("sales_executive","*","isDelete=0","",0);
          while ($data_d = mysqli_fetch_assoc($data)) {
          ?>
            <option <?php echo ($id==$data_d['id'])?"selected":"" ; ?> value="<?php echo $data_d['id'];?>"   ><?php echo $data_d['name'];?></option>
          <?php
          }
        ?>
        </select>
      </td>
      <td>
        <div class="form-group" style="width:150px !important;margin-top: -13px;margin-left:20px;padding: 0px;margin-right: 0px;">
           <label>&nbsp;&nbsp;</label>
          <input type="text" style="height: 37px;padding: 0px;" onchange="getByDate('exe_id')" name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off">  
        </div>
      </td>
      <td>
        <div class="form-group">
          <input class="btn btn-success btn-sm" style="height: 37px;margin-top: 10px;" type="submit" value="show last punch" onclick="getByDate('punch');">
        </div>
      </td>
      <!-- <td>
        <div class="form-group">
          <input class="btn btn-danger btn-sm" style="height: 37px;margin-top: 10px;margin-left: 10px;" type="submit" value="show route" onclick="getByDate('route');">
        </div>
      </td> -->
      <td>
        <div class="form-group">
          <input class="btn btn-primary btn-sm" style="height: 37px;margin-top: 10px;margin-left: 10px;" type="submit" value="Show All Pin" onclick="getByDate('punch_all');">
        </div>
      </td>
      <td>
        <div class="form-group">
            <!-- <button style="height: 37px;margin-top: 10px;margin-left: 10px;" onclick="clearAll()" id="eg4" class="button raised blue" >LOAD ROUTE</button> -->
            <button style="height: 37px;margin-top: 10px;margin-left: 10px;" id="eg4" class="button raised blue">LOAD ROUTE</button>
        </div>
      </td>
      <td>
        <a style="margin-left: 10px;height: 60px;">
          <!-- <img src="http://www.animatedimages.org/data/media/111/animated-arrow-image-0309.gif" height="25px;" border="0" alt="animated-arrow-image-0104" /> -->
          <img src="<?=SITEURL."images/arrow.gif"?>" height="25px;" border="0" alt="animated-arrow-image-0104" />
        </a><b>Click Here To Load Route</b>
      </td>
    </tr>
    </table>

    <div class="floating hidden" id="toggle" style="margin-top: 56px;">-</div>

    <div id="panel" class="hidden" style="margin-top: 56px;">
      <div class="block">
        <!-- <strong>Sample Queries</strong> -->
        <div id="button-div" class="col-md-12">
         <!--  <button id="eg1" class="button raised blue">EXAMPLE 1</button>
          <button id="eg2" class="button raised blue">EXAMPLE 2</button>
          <button id="eg3" class="button raised blue">EXAMPLE 3</button> -->
          <div class="col-md-6">
                  <button id="eg4" class="button raised blue" >LOAD ROUTE</button>
          </div>
          <div class="col-md-6">
                  <button id="distance" class="button raised blue">Toggle Distances</button>
          </div>
        </div>
      </div>
      <form id="controls">
        <div class="block" >
          <div>
            <strong hidden><span id="requestURL">Request URL</span> or Path (Pipe Separated)</strong><br>
            <textarea hidden id="coords" class="u-full-width coords-large" type="text" placeholder="-35.123,150.332 | 80.654,22.439" id="exampleEmailInput"></textarea>
            <textarea hidden id="data1" class="u-full-width coords-large" type="text" placeholder="sync|login"></textarea>
          </div>
          <div>
            <!-- <label>Interpolate: </label> -->
            <input for="interpolate" hidden checked id="interpolate" type="checkbox"/>
            <!-- <input for="interpolate" hidden id="interpolate" type="checkbox"/> -->
          </div>
        </div>
        <div>
          <div class="block">
            <button id="plot" class="button raised blue" hidden>Plot a Course</button>
          </div>
          <div id="legend">
            <img src="https://maps.google.com/mapfiles/ms/icons/green-dot.png" style="height:16px;"> Original
            <img src="https://maps.google.com/mapfiles/ms/icons/red-dot.png"/> Snapped
            <img src="https://maps.google.com/mapfiles/ms/icons/blue-dot.png"/> Interpolated
            <img src="https://maps.google.com/mapfiles/ms/icons/yellow-dot.png"/> Unsnappable
          </div>
          <div>
            <p id="warningMessage"></p>
            <p id="unsnappedPoints" hidden></p>
          </div>
        </div>
      </form>
    </div>
    <div id="map">
    </div>
  </body>
  <script type="text/javascript">
    $( document ).ready(function(e) {
        $('#coords').val(eg4);
        clearMap();
        bendAndSnap();
        drawDistance();
        e.preventDefault();
      });

  $('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });
    function getByDate(flag)
    {
      id=$("#exicutive_id").val();
      date=$("#ToDate").val();
      
      if(flag == 'route' || flag == 'exe_id')
      {
        if(id != "")
        {
          window.location = 'maproute.php?id='+id+'&date='+date;
          
        }
        else
        {
          toastr.error("please select executive");
        }
      }
      else if(flag == 'punch_all')
      {
        if(id != "")
        {
          window.location = 'tracking_all_pin.php?id='+id+'&date='+date;
        }
        else
        {
          toastr.error("please select executive");
        }
      }
      else if(flag == 'punch')
      {
        window.location = 'tracking_all.php?id='+id+'&date='+date;
       
      }
    }
  </script>
</html>