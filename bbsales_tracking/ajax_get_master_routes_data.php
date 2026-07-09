<?php
$page_id = 400;
$page_slug = 'dashboard';
require_once("connect.php");

$master_id = $_POST["master_id"];

// Fetch data from the database based on the given master_id
$master_r = $db->rp_getData("master_route", "*", "isDelete=0 AND id='" . $master_id . "'");
if ($master_r) {
    $master_d = mysqli_fetch_assoc($master_r);

    // Create an array with the required details
    $masterRouteDetailArr = array(
        "start_date" => date("d-m-Y", strtotime($master_d['start_date'])),
        "end_date" => date("d-m-Y", strtotime($master_d['end_date'])),
        "min_date" => date("Y-m-d", strtotime($master_d['start_date'])),
        "max_date" => date("Y-m-d", strtotime($master_d['end_date'])),
        "state" => $master_d['state'],
        "area" => $master_d['city'],
        "main_city" => $master_d['main_city'],
        "sales_name" => $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $master_d['sales_id'] . "'")
    );

    // Convert the array to JSON and output the result
    echo json_encode($masterRouteDetailArr);
}

?>

<?php
// Close the database connection
require_once 'disconnect.php';
?>
