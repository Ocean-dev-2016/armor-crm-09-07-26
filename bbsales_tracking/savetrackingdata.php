<?php
$page_id    = 400;
$page_slug  = 'dashboard';
$ctable     = "salesexecutive_tracking";
$ctable1    = "Sales Officer Tracking";
$main_page  = $ctable;
$page       = "manage_" . $ctable;
$page_title = "Manage " . $ctable1;
include("connect.php");

$id = 20;
$date = date("Y-m-d");
$limitToCall = 5;



$id = (isset($_REQUEST['id']) && $_REQUEST['id'] != "") ? $_REQUEST['id'] : $id;
$date = (isset($_REQUEST['date']) && $_REQUEST['date'] != "") ? date('Y-m-d', strtotime($_REQUEST['date'])) : $date;


$tracking_pin_count = $db->rp_getValue("salesexecutive_tracking", "COUNT(*)", "isDelete=0 AND isSnapped!=1 AND DATE(date) = '" . $date . "'", 0);


$Query = "SELECT GROUP_CONCAT(id) AS selectedids FROM salesexecutive_tracking WHERE isDelete=0 AND isSnapped!=1 AND DATE(date) = '" . $date . "'";
$tobeSnappedPins = $db->rp_getQuery($Query,0);
$tobeSnappedPins = mysqli_fetch_assoc($tobeSnappedPins);
$tobeSnappedPinsArray = explode(",", $tobeSnappedPins['selectedids']);
$tobeSnappedPinsArray = array_chunk($tobeSnappedPinsArray,$limitToCall);


$stored_array = array();
$count = 0;
for ($i = 0; $i < $tracking_pin_count; $i = $i + $limitToCall) {
  $tracking_pin_r = $db->rp_getData("salesexecutive_tracking", "latitude,longitude", "isDelete=0 AND isSnapped!=1 AND DATE(date) = '" . $date . "'", "",0,$i.",".$limitToCall);
  while($tracking_pin_d = mysqli_fetch_assoc($tracking_pin_r))
  {
    $stored_array[$count][] = implode(",", $tracking_pin_d);
  }
  $stored_array[$count] = implode("|", $stored_array[$count]);
  $count++;
}

$url = "";
$newCount = 0;
foreach ($stored_array as $key => $value)
{
  $url = "https://roads.googleapis.com/v1/snapToRoads?interpolate=true&key=AIzaSyADYWIGFSnn3DHlJblK0hntz5KQiwbD0hk&path=".$value;
  $returnedData = file_get_contents($url); // get json content
  $mainTobeStoreDataArray = json_decode($returnedData, true);
  foreach ($mainTobeStoreDataArray['snappedPoints'] as $key => $value)
  {
    $getDataOflocatedPin = array();
    if(isset($value['originalIndex']) && $value['originalIndex']!="")
    {
      $getDataOflocatedPin = $db->rp_getData("salesexecutive_tracking","*","id='".$tobeSnappedPinsArray[$newCount][$value['originalIndex']]."'");
      $getDataOflocatedPin = mysqli_fetch_assoc($getDataOflocatedPin);
    }
    $inserArray = array(
          "sales_executive_id"=>$id,
          "longitude"=>$value['location']['longitude'],
          "latitude"=>$value['location']['latitude'],
          "placeId"=>$value['placeId'],
          "type"=>(isset($getDataOflocatedPin['type']) && $getDataOflocatedPin['type']!="")?$getDataOflocatedPin['type']:0,
          "app_address"=>(isset($getDataOflocatedPin['app_address']) && $getDataOflocatedPin['app_address']!="")?$getDataOflocatedPin['app_address']:"",
          "BatteryPercent"=>(isset($getDataOflocatedPin['BatteryPercent']) && $getDataOflocatedPin['BatteryPercent']!="")?$getDataOflocatedPin['BatteryPercent']:"",
          "isGps"=>(isset($getDataOflocatedPin['isGps']) && $getDataOflocatedPin['isGps']!="")?$getDataOflocatedPin['isGps']:"",
          "isWifiEnabled"=>(isset($getDataOflocatedPin['isWifiEnabled']) && $getDataOflocatedPin['isWifiEnabled']!="")?$getDataOflocatedPin['isWifiEnabled']:"",
          "isNetworkAvailable"=>(isset($getDataOflocatedPin['isNetworkAvailable']) && $getDataOflocatedPin['isNetworkAvailable']!="")?$getDataOflocatedPin['isNetworkAvailable']:"",
          "bearing"=>(isset($getDataOflocatedPin['bearing']) && $getDataOflocatedPin['bearing']!="")?$getDataOflocatedPin['bearing']:"",
          "date"=>(isset($getDataOflocatedPin['date']) && $getDataOflocatedPin['date']!="")?$getDataOflocatedPin['date']:$date
      );
    $db->rp_insert("salesexecutive_tracking_snapped",array_values($inserArray),array_keys($inserArray),0);
    $db->rp_update("salesexecutive_tracking",array("isSnapped"=>1),"id IN (".implode(",", $tobeSnappedPinsArray[$newCount]).")",0);
  }
  $newCount++;
}
?>