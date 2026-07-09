<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    include('connect_in.php');
    
    if (isset($_POST["checkboxes"])) {

        // Get the values of the checked checkboxes
        $checkboxValues = $_POST["checkboxes"];
        $is_up = 0;
        
        for ($k = 0; $k < sizeof($checkboxValues); $k++) {
            $isUpdated = $db->rp_update("my_route", array("isDelete" => 1), "isDelete = 0 AND id = '" . $checkboxValues[$k] . "'");
            if ($isUpdated) {
                $is_up++;
            }
        }

        $response = "Total " . $is_up . " Sales Route Deleted Successfully.";
        $ack = array("ack" => 1, "ack_msg" => $response);

    } else {
        $ack = array("ack" => 0, "ack_msg" => "Something Went Wrong!!!");
    }

    echo json_encode($ack);
}

require_once "disconnect.php";
?>
