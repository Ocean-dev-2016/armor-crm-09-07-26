<?php
$page_id=562;$page_slug='page_category';
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
include("../include/class.sales_executive.php");
$id=(isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
$sales_executive=new SalesExecutive();
$phone="";
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
	$response=$sales_executive->trackSalesAll($date);
}
else
{
	$name=ucfirst($db->rp_getValue("sales_executive","name","id='".$id."'"));
	$phone= " (".ucfirst($db->rp_getValue("sales_executive","phone","id='".$id."'")).") ";
	$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
	$date=date('d-m-Y',strtotime($_REQUEST['date']));
	$response=$sales_executive->trackSalesAll($date,$id);

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-ui/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 768px;
      }
      /* Optional: Makes the sample page fill the window. */

      	/*this is for all offline list*/
	  	/*#toggle {
	      width: 25px;
	      z-index: 10;
	      cursor: default;
	      font-size: 2em;
	      padding: 1px;
	      color: #999;
	      display: none;
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
	      width: 200px;
	    }
	    .block {
	        clear: both;
	        margin: 1.5em auto;
	        text-align: left;
	      }*/
      
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>

</head>
<body class="page-md" id="data_print">
	
	<div class="portlet-body">
		<div class="row">
		 <div class="col-md-6 col-xs-12 col-sm-3" style="margin-top:10px;margin-left: 10px;">

			<h3>
				<?php if($id != ""){ ?>
					<a style="margin-left: 15px;height: 60px;">
		          		<img src="<?=SITEURL."images/".$status.".gif"?>" height="25px;" border="0" alt="error" />(<?=$status?>)
		        	</a>
	        	<?php } ?>
       			<?php echo $name.$phone." date:-".date('d-m-Y',strtotime($date)).""; ?>
       		</h3>
			<input value="<?php echo date('d-m-Y',strtotime($_REQUEST['date'])); ?>" type="hidden" id="date">
		 </div>
		</div>
		<table cellpadding="2" style="margin-left: 25px;">
		<tr>
			<td><a class="btn btn-warning" href="<?=SITEURL?>">Back To <i class="icon-home"></i></i></a></td>
			<td>&nbsp;&nbsp; &nbsp;&nbsp;Search By Executive & Date : &nbsp;&nbsp; &nbsp;&nbsp; 
			</td>
			<td>
				<select style="width:230px !important;" class="form-control" name="exicutive_id" id="exicutive_id" onChange="getMonths(this.value);">
				<option value="">Select Exicutive</option>
				<?php
					$data = $db->rp_getData("sales_executive","*","isDelete=0","",0);
					$offline_member = array();
					while ($data_d = mysqli_fetch_assoc($data)) {

						// this is for all offline list
						/*$last_time = 0;
						if($data_d['id']!="")
						{
							$where1="isDelete=0";
							$where1 .=" AND sales_executive_id=".$data_d['id']."";
							$where1 .=" AND DATE(date)='".date("Y-m-d")."'";
							$data_data = $db->rp_getData("salesexecutive_tracking","*",$where1,"id desc",0);
						}
						$data1 = mysqli_fetch_assoc($data_data);
						$last_time = $data1['date'];
						if($last_time)
						{
							$last_time = date("G:i:s",strtotime($last_time));
						}
						$curr_time = date("G:i:s");
						$sec = ACTIVE_TIME*60;
						if(strtotime($curr_time)>strtotime($last_time)+$sec)
						{
							array_push($offline_member,$data_d['name']);
						}*/
					?>
						<option <?php echo ($id==$data_d['id'])?"selected":"" ; ?> value="<?php echo $data_d['id'];?>"   ><?php echo $data_d['name'];?></option>
					<?php
					}
				?>
				</select>
			</td>
			<td>
				<div class="form-group" style="width:230px !important;margin-top: -13px;margin-left:20px;padding: 0px;margin-right: 0px;">
					 <label>&nbsp;&nbsp;</label>
					<input type="text" style="height: 37px;padding: 0px;" name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $date; ?>" placeholder="Date" autocomplete="off">	
				</div>
			</td>
			<td>
				<div class="form-group">
					<input class="btn btn-success btn-sm" style="height: 37px;margin-top: 10px;" type="submit" value="show last punch" onclick="getByDate('punch');">
				</div>
			</td>
			<td>
				<div class="form-group">
					<input class="btn btn-primary btn-sm" style="height: 37px;margin-top: 10px;margin-left: 10px;" type="submit" value="Show All Pin" onclick="getByDate('punch_all');">
				</div>
			</td>
			<td>
				<div class="form-group">
					<input class="btn btn-danger btn-sm" style="height: 37px;margin-top: 10px;margin-left: 10px;" type="submit" value="Go to Route Map" onclick="getByDate('route');">
				</div>
			</td>
		</tr>
		</table>
		<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
		<div id="results">
		 <div id="map"></div>

		</div>
	</div>
</div>
<!-- <div class="floating" id="toggle"><img src="<?=SITEURL."images/off.png"?>" width="20px;"></div>
<div id="panel" >
  <div class="block">
   <div style="margin-left: 20%;">
    <strong style="color: red">All Ofline User List:</strong><br/>
    <?php 
    	$n = sizeof($offline_member);
    	for($i = 0; $i< $n; $i++)
    	{
    ?>
    	<?=($i+1)." - ".$offline_member[$i];?><br/>
    <?php
		}
    ?>
  </div>
  </div>
</div> -->
<?php include("include_js.php"); ?>
 <script>
		var map;
		var markerCluster;
		var markers=[]
      	function initMap() {
		if(locations.length>0)
		{
			var location_count = locations.length;
			var j = 0;
			var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 19,
				center: {lat:locations[0].lat, lng: locations[0].lng},
			});
			var infowindow = new google.maps.InfoWindow();
			$.each(locations,function(i,v){
				var marker = new google.maps.Marker({
				position: {lat:locations[i].lat, lng: locations[i].lng},
				map: map,
			    icon: '<?php echo SITEURL; ?>/images/marker/green.png',
				title: locations[i]['type'],
				
				});


				/*var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/'+locations[i]['icon'],
					title: locations[i]['type']
				});*/
				google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
				return function() {
					if(locations[i]['status']=="offline")
					{
						var color = "red";
					}
					else if(locations[i]['status']=="online")
					{
						var color = "green";
					}
				  	infowindow.setContent("<h1>"+(i+1)+") "+locations[i]['date']+"</h1><p>Lat:"+locations[i]['lat']+"<br/> Long:"+locations[i]['lng']+" <br/>"+locations[i]['type']+"<br/><h4><b>Name:"+locations[i]['name']+"<br/><a style='color:"+color+"'>"+locations[i]['status']+"</a></b></h4></p>");
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
				// map.drawRoute({
				//   origin: location,
				//   destination: location,
				//   travelMode: 'driving',
				//   strokeColor: '#131540',
				//   strokeOpacity: 0.6,
				//   strokeWeight: 6
				// });
				// var circle = new google.maps.Circle({
				//   map: map,
				//   radius: 16093,    // 10 miles in metres
				//   fillColor: '#AA0000'
				// });
				// circle.bindTo('center', marker, 'position');
				for (var i = 0; i < location_count; i++) {
				 var cityCircle = new google.maps.Circle({
		            strokeColor: '#518FFB',
		            strokeOpacity: 0.8,
		            strokeWeight: 2,
		            fillColor: '#518FFB',
		            fillOpacity: 0.35,
		            map: map,
		            center: {lat:locations[i].lat, lng: locations[i].lng},
		            radius: 10
		          });
				}
		}
        else
		{
			var mapOptions = {
			    center: {'lat': 22.2939994, 'lng': 70.7892855},
			    zoom: 14
		 	};

		  	// Map object
		 	map = new google.maps.Map(document.getElementById('map'), mapOptions);
		}
      }
	  
     function clearMarker(){
		 locations=[];
		
		  for (var i = 0; i < markers.length; i++) {							  
			markers[i].setMap(map);			
		  }
		  if(markerCluster)
			markerCluster.clearMarkers();
	 }
    </script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
	
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
    </script>
<script type="text/javascript">
var locations=[];
var labels=[];
var status="";
var waypts = [];
var result= <?php echo json_encode($response); ?>;
var searchName="";
var data_url = "sales_executive_ajax_function.php";
var sid = <?php echo ($id!="")?$id:0; ?>

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords();
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords();
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadMap(){
	
	alert("Map Loading Please Wait");
}
function displayRecords() {
	var date=$("#date").val();
	if(result.ack==1)
	{
		//clearMarker();
		var locs=result.result
		$.each(locs,function(i,v){
			locations.push({date:v.date,name:v.name,lat: parseFloat(v.lat), lng:  parseFloat(v.lng),type_slug:v.type_slug,type:v.type,icon:v.icon,status:v.status});
			labels.push(v.date);
		});
		initMap();
		toastr.success(result.ack_msg,"success");
	}
	else
	{
		clearMarker();
		toastr.error(result.ack_msg,"Sorry");
	}
}
$(document).ready(function() {

	// this is for all offline list
	/*$('#panel').addClass("floating panel");
	$('#toggle').show();*/
	displayRecords();
});

$('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });

function getByDate(flag)
{
	id=$("#exicutive_id").val();
	date=$("#ToDate").val();
	
	if(flag == 'route')
	{
		if(id != "")
		{
			window.location = 'maproute.php?id='+id+'&date='+date;
		}
		else
		{
			toastr.error("please select exicutive");
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
			toastr.error("please select exicutive");
		}
	}
	else if(flag == 'punch')
	{
		window.location = 'tracking_all.php?id='+id+'&date='+date;
	}
	
}

// this is for all offline list
/*$('#toggle').click(function(e) {
	if($('#panel').css("display") != 'none')
	{
		$('#toggle').html("<img src='<?=SITEURL.'images/on.png'?>' width='20px;'>");
		$('#panel').hide();
	}
	else
	{
		$('#toggle').html("<img src='<?=SITEURL.'images/off.png'?>' width='20px;'>");
		$('#panel').show();
	}
});*/
</script>
</body>
</html>