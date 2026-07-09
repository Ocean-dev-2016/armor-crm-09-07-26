<?php
$page_id=515;$page_slug='page_inquiry';
include('connect.php');

//get search term
$searchTerm = $_GET['term'];

$query = $db->rp_getData("customer","cname","cname LIKE '%".$searchTerm."%'","cname ASC");
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row['cname'];
}
echo json_encode($data);
?>