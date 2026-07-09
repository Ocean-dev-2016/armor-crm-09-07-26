<?php
$page_id=522;$page_slug='page_search_tax';
include('connect.php');
$id = $_REQUEST['tax_id'];

$ctable="tax";

	$searchTerm = $_REQUEST['query'];

	///get Product Type
	//get matched data from skills table
	$query = $db->rp_getData($ctable,"*","name LIKE '%".$searchTerm."%'","name ASC",0);
	if($query)
	{
		
		$data=array();
		while ($row = mysqli_fetch_assoc($query)) {
			$data[] = array("id"=>$row['id'],"name"=>$row['name'],"value"=>$row['value']);
			
		}
		$reply=array("total_count"=>mysqli_num_rows($query),"incomplete_results"=>true,"items"=>$data);
	}
	else
	{
		$reply=array("total_count"=>0,"incomplete_results"=>true,"items"=>array());
	}	


	//return json data

echo json_encode($reply);
?>