<?php 
include("connect.php");

$q = "INSERT INTO salesexecutive_tracking_past_data SELECT * FROM salesexecutive_tracking WHERE 1=1";
// $db->query($q);

$q1="TRUNCATE salesexecutive_tracking";
// $db->query($q1);
?>



<!-- INSERT INTO salesexecutive_tracking_april  SELECT * FROM salesexecutive_tracking WHERE `salesexecutive_tracking`.`date` > '2023-04-01 00:00:00' AND  `salesexecutive_tracking`.`date` < '2023-05-01 00:00:00'


INSERT INTO `salesexecutive_tracking_april` SELECT * FROM `salesexecutive_tracking` WHERE `salesexecutive_tracking`.`date` > '2023-04-01 00:00:00' AND `salesexecutive_tracking`.`date` < '2023-05-01 00:00:00' 

date("m",strtotime($date))


date("m",strtotime("01-04-2023")) -->