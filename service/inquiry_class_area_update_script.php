<?php
	include('connect.php');

	// Retrieve distinct city names from the 'no_order_inquiry' table where isDelete=0
	$city_name = $db->rp_getData("no_order_inquiry", "*", "isDelete=0 GROUP BY city", "", 0);

	if ($city_name) 
	{
		$no_area_found = 0;
		$no_area_found_insert_success = 0;
		$no_area_found_insert_failed = 0;
		$no_order_inq_update_class_area = 0;
		$no_order_inq_update_class_area_failed = 0;
		$total_records = 0;

		// Loop through each city name
		while ($city_name_r = mysqli_fetch_assoc($city_name)) 
		{
			$total_records++;

			// Get the area ID based on the city name
			$area_id = $db->rp_getValue("area", "id", "isDelete=0 AND name LIKE '".$city_name_r['city']."'");

			if (!$area_id) 
			{
				$no_area_found++;

				// Get the class ID based on the state name
				$class_id = $db->rp_getValue("class", "id", "isDelete=0 AND name LIKE '".$city_name_r['state']."'");

				// Get the main city ID based on the main city name
				$main_city_id = $db->rp_getValue("city", "id", "isDelete=0 AND name LIKE '".$city_name_r['main_city']."' ");

				// Define an array of column names
				$rows = array("name", "old_name", "class_id", "city_id", "area_slug");

				// Define an array of corresponding values
				$values = array($city_name_r['city'], $city_name_r['city'], $class_id, $main_city_id, $db->rp_createSlug($city_name_r['city']));

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

			$area_id = $db->rp_getValue("area", "id", "isDelete=0 AND name LIKE '".$city_name_r['city']."'");
			$class_id = $db->rp_getValue("area", "class_id", "isDelete=0 AND id = '".$area_id."'");
			$city_id = $db->rp_getValue("area", "city_id", "isDelete=0 AND id = '".$area_id."'");

			// Prepare data to update in the 'no_order_inquiry' table
			$rows = array(
				"class_id" => $class_id,
				"city_id" => $city_id,
				"area_id" => $area_id
			);

			// Update 'no_order_inquiry' table with class and area information
			$is_update_inquiry = $db->rp_update("no_order_inquiry", $rows, "isDelete=0 AND id='".$city_name_r['id']."'");

			if ($is_update_inquiry) 
			{
				$no_order_inq_update_class_area++;
			} 
			else 
			{
				$no_order_inq_update_class_area_failed++;
			}
		}
	}

	$ack = array(
		"Total Record Found" => $total_records,
		"Total No Area Found" => $no_area_found,
		"Total No Area Found Insert" => $no_area_found_insert_success,
		"Total No Area Found Insert Failed" => $no_area_found_insert_failed,
		"Total NO ORDER INQUIRY Class Area Update" => $no_order_inq_update_class_area,
		"Total NO ORDER INQUIRY Class Area Update Failed" => $no_order_inq_update_class_area_failed,
	);

	echo json_encode($ack);
?>
