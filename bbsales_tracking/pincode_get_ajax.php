
<?php
$page_id=528;$page_slug='page_pincode';
include('connect.php');

//get search term
$searchTerm = $_GET['term'];

$query = $db->rp_getData("delivery_pincode","*","pincode LIKE '%".$searchTerm."%'","pincode ASC",0);
while ($row = mysqli_fetch_assoc($query)) {
    $data[] =$row['pincode'];
}
//return json data
echo json_encode($data);
?>
<?php require_once 'disconnect.php';  ?>