<?php
	require_once("connect.php");
	$ctableMap = 'executive_map_area';
	$where = "isDelete=0 ";
	// $limit=array();
	// $limit['ul']=$_REQUEST['ul'];
	// $limit['ll']=$_REQUEST['ll'];
	// $limitt=$system->getLimit($limit);
	// exit("dfsdf");

	$customer_r = $db->rp_getData("executive","*",$where,"",0);
	if ($customer_r) 
	{
		$ids=array();
		$cnt=0;
		$class_change=0;
		$nagative=0;
		$no_area_found=0;
		$no_area_found_insert_success=0;
		$no_area_found_insert_failed=0;
		$executive_update_class_area=0;
		$executive_update_class_area_failed=0;
		$$class_area_already_fount=0;
		while($customer_d = mysqli_fetch_array($customer_r))
		{
			$cnt++;

			// Get the area ID based on the city name
			$area_id = $db->rp_getValue("area", "id", "isDelete=0 AND name LIKE '".$customer_d['city']."'");

			if (!$area_id) 
			{
				$no_area_found++;

				// Get the class ID based on the state name
				$class_id = $db->rp_getValue("class", "id", "isDelete=0 AND name LIKE '".$customer_d['state']."'");

				// Get the main city ID based on the main city name
				$main_city_id = $db->rp_getValue("city", "id", "isDelete=0 AND name LIKE '".$customer_d['main_city']."' ");

				// Define an array of column names
				$rows = array("name", "old_name", "class_id", "city_id", "area_slug");

				// Define an array of corresponding values
				$values = array($customer_d['city'], $customer_d['city'], $class_id, $main_city_id, $db->rp_createSlug($customer_d['city']));

				// Insert new area record
				$is_area_insert = $db->rp_insert("area", $values, $rows, 0);

				if ($is_area_insert != 0) 
				{
					$no_area_found_insert_success++;
				} 
				else
				{
					$no_area_found_insert_failed++;
				}
			}

			$area_id = $db->rp_getValue("area", "id", "isDelete=0 AND name LIKE '%".strtolower(trim($customer_d['city']))."%'");
			$class_id = $db->rp_getValue("area", "class_id", "isDelete=0 AND id = '".$area_id."'");
			$city_id = $db->rp_getValue("area", "city_id", "isDelete=0 AND id = '".$area_id."'");

			// Prepare data to update in the 'executive' table
			$rows = array(
				"class_id" => $class_id,
				// "city_id" => $city_id,
				"area_id" => $area_id
			);

			// Update 'executive' table with class and area information
			$is_update_inquiry = $db->rp_update("executive", $rows, "isDelete=0 AND id='".$customer_d['id']."'");

			if ($is_update_inquiry) 
			{
				$executive_update_class_area++;
			} 
			else 
			{
				$executive_update_class_area_failed++;
			}


			$dealer_has_area_or_not = $db->rp_getTotalRecord($ctableMap, "executive_id='" . $customer_d['id'] . "' AND area_id='" . $area_id . "' AND class_id= '".$class_id."' AND city_id='".$city_id."' AND isDelete=0 ", 0);

			if ($dealer_has_area_or_not <= 0) 
			{
				$mapping_id_dd = $db->rp_insert($ctableMap, array($customer_d['id'], $customer_d['type_of_executive'], $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id","city_id"), 0);

				if ($mapping_id_dd) 
				{
					$class_change++;
						
				}
				else
				{
					$nagative++;
				}
			}
			else
			{
				$class_area_already_fount++;
			}
			
		}

		$ack = array(
			"Total Record Found" => $cnt,
			"Total No Area Found" => $no_area_found,
			"Total No Area Found Insert" => $no_area_found_insert_success,
			"Total No Area Found Insert Failed" => $no_area_found_insert_failed,
			"Total CUSTOMER Class Area Update" => $executive_update_class_area,
			"Total CUSTOMER Class Area Update Failed" => $executive_update_class_area_failed,
			"Total Class Area Already Found" => $class_area_already_fount,
			"Total Class Area Add Successfully Insert" => $class_change,
			"Total Class Area Add Not Insert" => $nagative,
		);

		echo json_encode($ack);
	}
?>