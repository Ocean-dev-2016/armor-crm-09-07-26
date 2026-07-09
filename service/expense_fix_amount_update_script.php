<?php
	include('connect.php');


	$expense_r = $db->rp_getData("expense","sales_executive_id,id,start_kilometer,end_kilometer");
?>