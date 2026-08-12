<?php
include("main.class.php");
class Functions extends Database
{
	// public $common_type = array("0"=>"Contact to Admin","1"=>"login", "2"=>"logout", "3"=>"attandance In", "4"=>"attandance Out", "5"=>"sync", "6"=>"visit"); 
	// public $common_type=array("0" => "Contact to Admin","1"=>"login","2"=>"logout","3"=>"attandance In", "4"=>"attandance Out","5"=>"tracking sync","6"=>"create order","7"=>"create customer","8"=>"add expance","9"=>"add visit","10"=>"add inquiry","11"=>"edit inquiry","12"=>"delete inquiry","13"=>"add area","14"=>"add complain");

	public $common_type = array("Contact to Admin", "Login", "Logout", "Attandance In", "Attandance Out", "Traking Sync", "Create Order", "Create Customer", "Add Expance", "Add Visit", "Add Inquiry", "Edit Inquiry", "Delete Inquiry", "Add Area", "Add Complain", "Add Meeting", "Add Meeting Member", "Edit Meeting", "Delete Meeting Member", "Change Pasdsword", "Hour Sync", "Add Follow Up", "Add Leave");


	/*order status color array*/
	/* public $status_color = array("Pending"=>"#9fc1ff","Approved"=>"#7bd0a9","Dispatch"=>"#696969",
        	"Disapproved"=>"#ffe167","Canceled"=>"#ec9b97","Partially Dispatched"=>"#126608");  */
	public $status_color = array("Order Complate" => "#9fc1ff", "Waiting For Approval" => "#add8e6", "Disapproved" => "#ffe167", "Waiting For Account Approval" => "#7C9D96", "Canceled" => "#ec9b97", "Account Approved" => "#FA9C5B", "Dispatch" => "#f1acac", "Partially Dispatched" => "#126608");
	/*order status color array*/

	/*quotation status color array*/
	public $quotation_status_color = array(
		"Pending" => "#9fc1ff",
		"Approved" => "#7bd0a9",
		"Disapproved" => "#ffe167",
		"Canceled" => "#ec9b97",
		"Order Generated" => "#126608",
		"Lost" => "#ff4500"
	);
	/*quotation status color array*/

	/*inquiry status color array*/
	public $inquiry_status_color = array("Generate" => "#696969", "In Followup" => "#9fc1ff", "Interested" => "#7bd0a9", "Not Interested" => "#ec9b97", "My Work" => "#126608", "Non Relavent Inquiry" => "#D3705B", "Hot" => "#65B237", "Cold" => "#3787B2", "Warm" => "#B2A137", "Wrong Call" => "#B23759", "Will Interested" => "#37B24F", "Not Working" => "#4A6DA9", "Not Doing Business" => "#4AA9A5", "Lost" => "#ff4500", "Buy Later" => "#ff4500");
	/*inquiry status color array*/

	/*complain status color array*/
	public $complain_status_color = array("Generate" => "#696969", "In Progress" => "#9fc1ff", "Complete" => "#7bd0a9", "Reject" => "#ec9b97", "Not Done" => "#126608");
	/*complain status color array*/


	/*leave status color array*/
	public $leave_status_color = array("Generate" => "#696969", "Accepted" => "#7bd0a9", "Rejected" => "#ec9b97", "Cancel" => "#D3705B");
	/*leave status color array*/

	public $approval_type_arr = array("1" => "A. PRIVATE CONSULTANT APPROVAL", "2" => "B. GOVERNMENT APPROVAL");

	/*
		*** Main Function Developed By Ravi Patel :) <<<
			-> rp_getData() 
				- return single and multi records
			-> rp_getValue() 
				- return single records
			-> rp_getTotalRecord()
				- return number of records
			-> rp_getMaxVal()
				- return maximum value
			-> rp_insert()
				- insert record
			-> rp_delete()
				- delete record
			-> rp_update()
				- update record
			-> tableExists()
				- check whether table exist or not
			-> rp_limitChar()
				- return trimed character string
			-> rp_dupCheck()
				- check for duplicate record in table
			-> rp_location()
				- redirect to given URL
			-> rp_getDisplayOrder()
				- get next display order
			-> rp_createSlug()
				- create alias of given string
			-> rp_getTotalReview()
				- number of total review of product
			-> rp_catData()
				- get cid/sid/ssid from slug
			-> clean()
				- prevent mysqli injction
			-> rp_productQty()
				- Current Product Qty
			-> rp_getProductPriceDiv()
				- Product Price Div
			-> getCommaSepretedData()
			    - GET COMMA SEPRETED STRING
			-> getUpperLevelToken()
				- GET Refresh token up to single level
			-> getAllUpperLevelToken()
				- GET Refresh token up to admin level
	*/


	public function rp_getData($table, $rows = '*', $where = null, $order = null, $die = 0, $limit = "") // Select Query, $die==1 will print query By Ravi Patel
	{
		$results = array();
		$q = 'SELECT ' . $rows . ' FROM ' . $table;
		if ($where != null)
			$q .= ' WHERE ' . $where;
		if ($order != null)
			$q .= ' ORDER BY ' . $order;
		if ($limit != null)
			$q .= ' LIMIT ' . $limit;
		if ($die == 1) {
			echo $q;
			die;
		}
		if ($this->tableExists($table)) {
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_query($this->myconn, $q);
			// 	// print_r($results);exit;
			// 	return $results;
			// } else {
			// 	return false;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				return $results;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function rp_getDataByRights($table, $rows = '*', $where = null, $order = null, $die = 0) // Select Query, $die==1 will print query By Ravi Patel
	{
		$results = array();
		$q = 'SELECT ' . $rows . ' FROM ' . $table;
		if ($where != null) {
			$where = $where . " AND " . $this->rightsWhere();
			$q .= ' WHERE ' . $where;
		}


		if ($order != null)
			$q .= ' ORDER BY ' . $order;
		if ($die == 1) {
			echo $q;
			die;
		}
		if ($this->tableExists($table)) {
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_query($this->myconn, $q);
			// 	return $results;
			// } else {
			// 	return false;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				return $results;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function rightsWhere()
	{

		if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] != "" && isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != "") {
			$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
			$user_type = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
			$where = "((created_by_type >" . $user_type . ") OR (created_by_type='" . $user_type . "' AND created_by='" . $uid . "'))";
		} else {
			if (is_writable(LOG_FILE)) {
				$ip = $this->rp_get_client_ip();
				fopen(LOG_FILE, "a");
				fwrite(LOG_FILE, "From IP " . $ip . " Entry is modified or inserted but Session not created DATETIME " . date("Y-m-d H:i:s") . PHP_EOL);
				fclose(LOG_FILE);
			}
			$where = "1=0";
		}
		return $where;
	}
	public function rp_getValue($table, $row = null, $where = null, $die = 0) // single records ref HB function
	{
		if ($this->tableExists($table) && $row != null && $where != null) {
			$q = 'SELECT ' . $row . ' FROM ' . $table . ' WHERE ' . $where;
			if ($die == 1) {
				echo $q;
				die;
			}
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) >= 0) {
			// 	$results = @mysqli_fetch_array(mysqli_query($this->myconn, $q));
			// 	return ($results[$row] != null) ? $results[$row] : "";
			// } else {
			// 	return false;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				$result = @mysqli_fetch_array($results);
				return ($result[$row] != null) ? $result[$row] : "";
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function rp_getMaxVal($table, $row = null, $where = null, $die = 0)
	{
		if ($this->tableExists($table) && $row != null && $where != null) {
			$q = 'SELECT MAX(' . $row . ') as ' . $row . ' FROM ' . $table . ' WHERE ' . $where;
			if ($die == 1) {
				echo $q;
				die;
			}
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_fetch_array(mysqli_query($this->myconn, $q));
			// 	return $results[$row];
			// } else {
			// 	return 0;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				$result = @mysqli_fetch_array($results);
				return $result[$row];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	public function rp_getMinVal($table, $row = null, $where = null, $die = 0)
	{
		if ($this->tableExists($table) && $row != null && $where != null) {
			$q = 'SELECT MIN(' . $row . ') as ' . $row . ' FROM ' . $table . ' WHERE ' . $where;
			if ($die == 1) {
				echo $q;
				die;
			}
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_fetch_array(mysqli_query($this->myconn, $q));
			// 	return $results[$row];
			// } else {
			// 	return 0;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				$result = @mysqli_fetch_array($results);
				return $result[$row];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function rp_getSumVal($table, $row = null, $where = null, $die = 0)
	{
		if ($this->tableExists($table) && $row != null && $where != null) {
			$q = 'SELECT SUM(' . $row . ') as ' . $row . ' FROM ' . $table . ' WHERE ' . $where;
			if ($die == 1) {
				echo $q;
				die;
			}
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_fetch_array(mysqli_query($this->myconn, $q));
			// 	return $results[$row];
			// } else {
			// 	return 0;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				$result = @mysqli_fetch_array($results);
				return $result[$row];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function rp_getAvgVal($table, $row = null, $where = null, $die = 0)
	{
		if ($this->tableExists($table) && $row != null && $where != null) {
			$q = 'SELECT AVG(' . $row . ') as ' . $row . ' FROM ' . $table . ' WHERE ' . $where;
			if ($die == 1) {
				echo $q;
				die;
			}
			// if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			// 	$results = @mysqli_fetch_array(mysqli_query($this->myconn, $q));
			// 	return $results[$row];
			// } else {
			// 	return 0;
			// }
			$results = @mysqli_query($this->myconn, $q);
			if (mysqli_num_rows($results) > 0) {
				$result = @mysqli_fetch_array($results);
				return $result[$row];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}


	public function rp_getTotalRecord($table, $where = null, $die = 0) // return number of records By Ravi Patel
	{
		$q = 'SELECT * FROM ' . $table;
		if ($where != null)
			$q .= ' WHERE ' . $where;
		if ($die == 1) {
			echo $q;
			die;
		}
		if ($this->tableExists($table))
			return mysqli_num_rows(mysqli_query($this->myconn, $q)) + 0;
		else
			return 0;
	}

	public function rp_insert($table, $values, $rows = 0, $die = 0, $log_description = "", $flag = "", $module_name = "", $user_id = "", $customer_id = "") // rp_insert - Insert and Die Values By Rav-i Pa-tel
	{
		// Modify by Jai
		// Add six column and there value automatically
		// created_by,created_by_type,modified_by,modified_by_type,created_date,modified_date

		if ($this->tableExists($table)) {
			$extras = $this->autoGeneratedColumnsForInsert();
			if (array_key_exists("columns", $extras) && array_key_exists("values", $extras)) {
				$tableColumns = $this->rp_getTableColumnNames($table);
				$existingRows = is_array($rows) ? $rows : array();
				for ($i = 0; $i < count($extras['columns']); $i++) {
					$col = $extras['columns'][$i];
					if (in_array($col, $tableColumns) && !in_array($col, $existingRows)) {
						$rows[] = $col;
						$values[] = $extras['values'][$i];
						$existingRows[] = $col;
					}
				}
			}

			$insert = 'INSERT INTO ' . $table;
			if (count($rows) > 0) {
				$insert .= ' (' . implode(",", $rows) . ')';
			}

			for ($i = 0; $i < count($values); $i++) {
				if (is_string($values[$i]))
					$values[$i] = '"' . $values[$i] . '"';
			}



			$values = implode(',', $values);


			$insert .= ' VALUES (' . $values . ')';


			if ($die == 1) {
				echo $insert;
				die;
			}
			$ins = @mysqli_query($this->myconn, $insert);
			if ($ins) {

				$last_id = mysqli_insert_id($this->myconn);
				// $this->insertLog($table, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
				// $this->update_table_information($table, date("Y-m-d H:i:s"));
				return $last_id;
			} else {
				return false;
			}
		}
	}

	/*insert log*/
	public function insertLog($table_name = "", $ref_id = "", $activity_type = "", $before_description = "", $after_description = "", $die = 0, $log_description = "", $flag = "", $module_name = "", $userid = "", $customer_id = "")
	{
		return true;
		// if ($flag == "Application") {
		// 	$user_name = $this->rp_getValue("sales_executive", "name", "id='" . $userid . "'", 0);
		// } else {
		// 	$user_name = $_SESSION[SITE_SESS . 'SESS_NAME'];
		// }
		// $before_description = $this->rp_escapeString($before_description);
		// $after_description = $this->rp_escapeString($after_description);
		// $ip = $this->rp_get_client_ip();


		// if ($flag == "Application") {
		// 	$user_id = $userid;
		// 	$user_type = $this->rp_getValue("dealer_distributor_network", "admin_type", "sales_executive_id='" . $userid . "'", 0);
		// } else {
		// 	if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] != "" && 		isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != "") {
		// 		$user_id = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		// 		$user_type = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
		// 	} else {
		// 		if (is_writable(LOG_FILE)) {
		// 			fopen(LOG_FILE, "a");
		// 			fwrite(LOG_FILE, "From IP " . $ip . " Entry is modified or inserted but Session not created DATETIME " . date("Y-m-d H:i:s") . PHP_EOL);
		// 			fclose(LOG_FILE);
		// 		}
		// 		$user_id = 5895;
		// 		$user_type = 0712;
		// 	}
		// }

		// if ($table_name == "dealer_distributor_network") {
		// 	$before_description = "";
		// 	$after_description = "login Details Update";
		// }

		// $rows = array("table_name", "ref_id", "activity_type", "before_description", "after_description", "activity_date", "user_id", "user_type", "ip", "created_date", "customer_id", "log_description", "flag", "module_name", "user_name");
		// $values = array($table_name, $ref_id, $activity_type, $before_description, $after_description, date("Y-m-d H:i:s"), $user_id, $user_type, $ip, date("Y-m-d H:i:s"), $customer_id, $log_description, $flag, $module_name, $user_name);

		// if ($this->tableExists("activity_log")) {
		// 	$insert = 'INSERT INTO activity_log';
		// 	if (count($rows) > 0) {
		// 		$insert .= ' (' . implode(",", $rows) . ')';
		// 	}
		// 	for ($i = 0; $i < count($values); $i++) {
		// 		if (is_string($values[$i]))
		// 			$values[$i] = "'$values[$i]'";
		// 	}
		// 	$values = implode(',', $values);
		// 	$insert .= ' VALUES (' . $values . ')';
		// 	if ($die == 1) {
		// 		echo $insert;
		// 		die;
		// 	}
		// 	$ins = @mysqli_query($this->myconn, $insert);
		// 	if ($ins) {
		// 		$last_id = mysqli_insert_id($this->myconn);
		// 	}
		// }
	}
	/*insert log*/

	public function rp_escapeString($string)
	{
		$string = stripslashes($string);
		$string = mysqli_real_escape_string($this->myconn, $string);
		return $string;
	}


	public function autoGeneratedColumnsForInsert()
	{
		if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] != "" && isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != "") {
			$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
			$user_type = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
			return array("columns" => array("created_by", "created_by_type", "modified_by", "modified_by_type", "created_date", "modified_date"), "values" => array($uid, $user_type, "", "", date("Y-m-d H:i:s"), ""));
		} else {
			if (is_writable(LOG_FILE)) {
				$ip = $this->rp_get_client_ip();
				/*fopen(LOG_FILE,"a");
				fwrite(LOG_FILE,"From IP ".$ip." Entry is modified or inserted but Session not created DATETIME ".date("Y-m-d H:i:s").PHP_EOL);
				fclose(LOG_FILE);*/
			}

			return array("columns" => array("created_by", "created_by_type", "modified_by", "modified_by_type", "created_date", "modified_date"), "values" => array("5895", "0712", "", "", date("Y-m-d H:i:s"), ""));
		}
	}
	// Send Notification by GCM to Android
	public function send_notification($data, $ids, $type = 1)
	{
		// print_r($data); exit;
		//$type=2 Customer 1=>Sales

		if ($type == 2)
			$apiKey = 'AIzaSyAVsMPQZDZZciZX34U2J2aFNeOMoyWvxWQ'; // This is Server Legacy Key From Cloud Messaging Firebase
		else
			$apiKey = 'AIzaSyDJhRtHFEeaPJhLN7zqkq17Kw87oucXbOw'; // This is Server Legacy Key From Cloud Messaging Firebase
		$url = 'https://android.googleapis.com/gcm/send';
		$post = array(
			'registration_ids'  => $ids,
			'data'              => $data,
		);

		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //////// SSL Verifier False ////////
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		$result = curl_exec($ch);

		//print_r($result); exit;

		curl_close($ch);
		return $result;
	}
	public function autoGeneratedColumnsForUpdate($last_users_modified, $last_user_types_modified, $last_modified_dates)
	{
		if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] != "" && isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != "") {
			$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
			$user_type = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];

			if ($last_users_modified != "") {
				$last_users_modified = explode(",", $last_users_modified);
				$last_users_modified[] = $uid;
				$last_users_modified = implode(",", $last_users_modified);
			} else {
				$last_users_modified = $uid;
			}

			if ($last_user_types_modified != "") {
				$last_user_types_modified = explode(",", $last_user_types_modified);
				$last_user_types_modified[] = $user_type;
				$last_user_types_modified = implode(",", $last_user_types_modified);
			} else {
				$last_user_types_modified = $user_type;
			}
			if ($last_modified_dates != "") {
				$last_modified_dates = explode(",", $last_modified_dates);
				$last_modified_dates[] = date("Y-m-d H:i:s");
				$last_modified_dates = implode(",", $last_modified_dates);
			} else {
				$last_modified_dates = date("Y-m-d H:i:s");
			}

			return array("values" => array("modified_by" => $last_users_modified, "modified_by_type" => $last_user_types_modified, "modified_date" => $last_modified_dates));
		} else {
			if (is_writable(LOG_FILE)) {
				$ip = $this->rp_get_client_ip();
				/*fopen(LOG_FILE,"a");
				fwrite(LOG_FILE,"From IP ".$ip." Entry is modified or inserted but Session not created DATETIME ".date("Y-m-d H:i:s").PHP_EOL);
				fclose(LOG_FILE);*/
			}
			$uid = 5895;
			$user_type = 0712;

			if ($last_users_modified != "") {
				$last_users_modified = explode(",", $last_users_modified);
				$last_users_modified[] = $uid;
				$last_users_modified = implode(",", $last_users_modified);
			} else {
				$last_users_modified = $uid;
			}

			if ($last_user_types_modified != "") {
				$last_user_types_modified = explode(",", $last_user_types_modified);
				$last_user_types_modified[] = $user_type;
				$last_user_types_modified = implode(",", $last_user_types_modified);
			} else {
				$last_user_types_modified = $user_type;
			}
			if ($last_modified_dates != "") {
				$last_modified_dates = explode(",", $last_modified_dates);
				$last_modified_dates[] = date("Y-m-d H:i:s");
				$last_modified_dates = implode(",", $last_modified_dates);
			} else {
				$last_modified_dates = date("Y-m-d H:i:s");
			}

			return array("values" => array("modified_by" => $last_users_modified, "modified_by_type" => $last_user_types_modified, "modified_date" => $last_modified_dates));
		}
	}
	public function rp_delete($table, $where = null, $die = 0)
	{
		if ($this->tableExists($table)) {
			if ($where != null) {
				$delete = 'DELETE FROM ' . $table . ' WHERE ' . $where;
				if ($die == 1) {
					echo $delete;
					die;
				}
				$del = @mysqli_query($this->myconn, $delete);
			}
			if ($del) {
				$this->update_table_information($table, date("Y-m-d H:i:s"));
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function rp_update($table, $rows, $where, $die = 0, $log_description = "", $flag = "", $module_name = "", $user_id = "", $customer_id = "") //update query by Ravi Patel
	{
		if ($this->tableExists($table)) {
			// Parse the where values
			// even values (including 0) contain the where rows
			// odd values contain the clauses for the row
			//print_r($where);die;
			// $extra_r = $this->rp_getData($table, "modified_by,modified_by_type,modified_date", $where);
			// if ($extra_r) {
			// 	$extra_v = mysqli_fetch_assoc($extra_r);
			// 	$extra = $this->autoGeneratedColumnsForUpdate($extra_v['modified_by'], $extra_v['modified_by_type'], $extra_v['modified_date']);
			// 	$rows = array_merge($extra['values'], $rows);
			// }
			$update = 'UPDATE ' . $table . ' SET ';
			$keys = array_keys($rows);
			for ($i = 0; $i < count($rows); $i++) {
				if (is_string($rows[$keys[$i]])) {
					//$update .= $keys[$i].'="'.$rows[$keys[$i]].'"';
					$update .= $keys[$i] . '="' . $this->rp_escapeString($rows[$keys[$i]]) . '"';
				} else {
					$update .= $keys[$i] . '=' . $rows[$keys[$i]];
				}

				// Parse to add commas
				if ($i != count($rows) - 1) {
					$update .= ',';
				}
			}
			$update .= ' WHERE ' . $where;
			if ($die == 1) {
				echo $update;
				die;
			}

			$rowArray = array_keys($rows);
			if (!in_array("id", $rowArray)) {
				$rowArray[] = "id";
			}
			$activity_type = "update";
			if (in_array("isDelete", $rowArray)) {
				if ($rows['isDelete'] == 1) {
					$activity_type = "delete";
				}
			}

			if (in_array("status", $rowArray)) {
				$activity_type = "status_change";
			}

			$rowArray = implode(",", $rowArray);
			$beforeUpdateR = $this->rp_getData($table, $rowArray, $where);

			//$update = trim($update," AND");
			$query = @mysqli_query($this->myconn, $update);
			if ($query) {
				$beforeUpdateD = $this->fetch_all($beforeUpdateR);
				foreach ($beforeUpdateD as $keyLog => $valueLog) {

					$rowDataFromArray = implode(', ', array_map(
						function ($v, $k) {
							return sprintf("%s='%s'", $k, $v);
						},
						$valueLog,
						array_keys($valueLog)
					));

					// $this->insertLog($table, $valueLog['id'], $activity_type, $rowDataFromArray, $update, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
				}
				// $this->update_table_information($table, date("Y-m-d H:i:s"));
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function fetch_all($resource, $type = 'ASSOC')
	{
		$Result = array();
		if ($type == 'ASSOC') {
			while ($Data = mysqli_fetch_assoc($resource)) {
				$Result[] = $Data;
			}
		} else {
			while ($Data = mysqli_fetch_array($resource)) {
				$Result[] = $Data;
			}
		}
		return $Result;
	}

	public function tableExists($table)
	{
		$tablesInDb = @mysqli_query($this->myconn, 'SHOW TABLES FROM ' . $this->db_name . ' LIKE "' . $table . '"');
		if ($tablesInDb) {
			if (mysqli_num_rows($tablesInDb) == 1) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function rp_getTableColumnNames($table)
	{
		static $columnCache = array();
		if (isset($columnCache[$table])) {
			return $columnCache[$table];
		}

		$columns = array();
		if ($this->tableExists($table)) {
			$result = @mysqli_query($this->myconn, 'SHOW COLUMNS FROM `' . $table . '`');
			if ($result) {
				while ($row = mysqli_fetch_assoc($result)) {
					$columns[] = $row['Field'];
				}
			}
		}

		$columnCache[$table] = $columns;
		return $columns;
	}

	public function rp_getLastDbError()
	{
		return mysqli_error($this->myconn);
	}

	public function rp_limitChar($content, $limit, $url = "javascript:void(0);", $txt = "&hellip;")
	{
		if (strlen($content) <= $limit) {
			return $content;
		} else {
			$ans = substr($content, 0, $limit);
			if ($url != "") {
				$ans .= "<a href='$url' class='desc'>$txt</a>";
			} else {
				$ans .= "&hellip;";
			}
			return $ans;
		}
	}

	public function rp_dupCheck($table, $where = null, $die = 0)
	{
		$q = 'SELECT id FROM ' . $table;
		if ($where != null)
			$q .= ' WHERE ' . $where;
		if ($die == 1) {
			echo $q;
			die;
		}
		if ($this->tableExists($table)) {
			$results = @mysqli_num_rows(mysqli_query($this->myconn, $q));
			if ($results > 0) {
				return true;
			} else {
				return false;
			}
		} else
			return false;
	}

	public function rp_location($redirectPageName = null)
	{
		if ($redirectPageName == null) {
			header("Location:" . $this->SITEURL);
			exit;
		} else {
			header("Location:" . $redirectPageName);
			exit;
		}
	}

	public function rp_getDisplayOrder($table, $where = null, $die = 0)
	{
		$q = 'SELECT MAX(display_order) as display_order FROM ' . $table;
		if ($where != null)
			$q .= ' WHERE ' . $where;
		if ($die == 1) {
			echo $q;
			die;
		}
		if ($this->tableExists($table)) {
			$results = @mysqli_query($this->myconn, $q);
			if (@mysqli_num_rows($results) > 0) {
				$disp_d = mysqli_fetch_array($results);
				return intval($disp_d['display_order']) + 1;
			} else {
				return 1;
			}
		} else {
			return 1;
		}
	}

	public function rp_createSlug($string)
	{
		$slug = strtolower(trim(preg_replace('/-{2,}/', '-', preg_replace('/[^a-zA-Z0-9-]/', '-', $string)), "-"));
		return $slug;
	}

	public function rp_createProSlug($string)
	{
		$slug = strtolower(trim(preg_replace('/-{2,}/', '-', preg_replace('/[^a-zA-Z0-9-.]/', '-', $string)), "-"));

		return $slug;
	}

	public function getDBName()
	{
		$dbData = $this->db_host . "," . $this->db_user . "," . $this->db_pass . "," . $this->db_name;
		return $dbData;
	}

	public function setViewCounter($tableName, $counterFieldName, $setCounterOnField, $setCounterOnFieldValue)
	{
		setcookie($counterFieldName . '_' . $setCounterOnFieldValue, "productViewCookie", time() + 3600);
		$counterUpdateQuery = "UPDATE " . $tableName . " SET " . $counterFieldName . " = " . $counterFieldName . "+1 WHERE " . $setCounterOnField . "=" . $setCounterOnFieldValue;
		//echo $counterUpdateQuery; exit;
		mysqli_query($this->myconn, $counterUpdateQuery);
	}

	public function rp_num($val, $deci = "2", $sep = ".", $thousand_sep = "")
	{
		return number_format($val, $deci, $sep, $thousand_sep);
	}

	public function catData($cslug = null, $sslug = null, $ssslug = null)
	{
		if ($cslug != null && $sslug == null && $ssslug == null) {
			return $this->rp_getData("category", "*", "slug='" . $cslug . "' AND isDelete=0");
		} else if ($cslug != null && $sslug != null && $ssslug == null) {
			$cid	= $this->rp_getValue("category", "id", "slug='" . $cslug . "'");
			return $this->rp_getData("sub_category", "*", "cid='" . $cid . "' AND slug='" . $sslug . "' AND isDelete=0");
		} else if ($cslug != null && $sslug != null && $ssslug != null) {
			$cid	= $this->rp_getValue("category", "id", "slug='" . $cslug . "'");
			$sid	= $this->rp_getValue("sub_category", "id", "slug='" . $sslug . "'");
			return $this->rp_getData("sub_sub_category", "*", "cid='" . $cid . "' AND sid='" . $sid . "' AND slug='" . $ssslug . "' AND isDelete=0");
		} else {
			return false;
		}
		return number_format($val, $deci, $sep, $thousand_sep);
	}

	public function rp_getTotalReview($pid)
	{
		return $this->rp_getTotalRecord("product_review", "pid = '" . $pid . "'");
	}

	public function clean($string)
	{
		$string = trim($string);								// Trim empty space before and after
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);					        // Stripslashes
		}
		$string = mysqli_real_escape_string($this->myconn, $string);			        // mysqli_real_escape_string
		return $string;
	}
	public function rp_getProductQty($pid)
	{
		$proQty = $this->rp_getValue("product", "qty", "id='" . $pid . "'");
		return $proQty;
	}
	public function rp_getProductPriceDiv($max_price, $sell_price)
	{
		if ($sell_price < $max_price && $sell_price != $max_price) {
?>
			<span class="price"><?php echo CURR; ?><?php echo $sell_price; ?></span>
			<span class="price-before-discount"><?php echo CURR; ?><?php echo $max_price; ?></span>
		<?php
		} else {
		?>
			<span class="price"><?php echo CURR; ?><?php echo $sell_price; ?></span>
			<span class="price-before-discount"></span>
			<?php
		}
	}
	public function rp_getShippingCharge($pincode, $pid, $subpid = 0)
	{
		if ($subpid > 0) {
			$tabName = "sub_product";
			$pro_id	= $subpid;
		} else {
			$tabName = "product";
			$pro_id	= $pid;
		}
		$deliveryPin_r = $this->rp_getData("delivery_pincode", "*", "pincode='" . $pincode . "' AND isDelivery=1");
		if (mysqli_num_rows($deliveryPin_r) > 0) {
			$deliveryPin_d = mysqli_fetch_array($deliveryPin_r);
			$area_type 	= $deliveryPin_d["area_type"];

			if ($area_type == 0) {
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName, "local_ship_charge", "id='" . $pro_id . "'"));
			} else if ($area_type == 1) {
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName, "zonal_ship_charge", "id='" . $pro_id . "'"));
			} else {
				$shipping_charge = $this->rp_num($this->rp_getValue($tabName, "national_ship_charge", "id='" . $pro_id . "'"));
			}
			return $shipping_charge;
		} else {
			return $this->rp_num($this->rp_getValue($tabName, "national_ship_charge", "id='" . $pro_id . "'"));
		}
	}
	public function rp_checkDeliveryAndShipping($pincode, $pid)
	{
		if ($this->rp_getTotalRecord("delivery_pincode", "pincode='" . $pincode . "'") > 0) {
			if ($this->rp_getTotalRecord("delivery_pincode", "pincode='" . $pincode . "' AND isDelivery=1") > 0) {
				$shipping_charge = $this->rp_getShippingCharge($pincode, $pid);
				if ($shipping_charge == 0.00) {
					$shipping_charge = "Free";
				} else {
					$shipping_charge = CURR . $shipping_charge;
				}
				$_SESSION['SHOPWALA_SESS_PINCODE'] = $pincode;

			?>
				<div class="col-md-5"><strong>Delivery available at pincode:</strong> <?php echo $pincode; ?></div>
				<div class="col-md-5"><strong>Shipping Charges:</strong> <?php echo $shipping_charge; ?></div>
			<?php
			} else {
			?>
				<div class="col-md-12"><strong>Delivery not available at pincode:</strong> <?php echo $pincode; ?></div>
			<?php
			}
		} else {
			?>
			<div class="col-md-12"><strong>Sorry, we couldn't find pincode:</strong><?php echo $pincode; ?></div>
		<?php
		}
	}
	public function getlastInsertId($ctable, $die = 0)
	{
		$lastInsertId = $this->rp_getValue($ctable, "MAX(`id`)", "1=1", 0);
		return $lastInsertId + 1;
	}
	public function printr($val, $isDie = 1)
	{
		echo "<pre>";
		print_r($val);
		if ($isDie) {
			die;
		}
	}
	public function rp_randomString($len = 5)
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = "";
		for ($i = 0; $i < $len; $i++) {
			$str .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $str;
	}
	public function rp_get_client_ip()
	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if (getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if (getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if (getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if (getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';

		return $ipaddress;
	}
	function getCustomers($debug = 0)
	{
		$result = array();
		$rows = $this->rp_getData("customer", "*", "isDelete='0'", "", $debug);
		while ($data = mysqli_fetch_assoc($rows)) {
			$result[] = $data;
		}

		return $result;
	}
	function getPayees($debug = 0)
	{
		$result = array();
		$rows = $this->rp_getData("payee", "*", "isDelete='0'", "", $debug);
		while ($data = mysqli_fetch_assoc($rows)) {
			$result[] = $data;
		}

		return $result;
	}
	function getCustomerInfo($cid = "", $debug = 0)
	{
		$result = array();
		if ($cid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("customer", "*", "id='" . $cid . "' AND isDelete=0", "", $debug));
		}
		return $result;
	}
	function getExecutiveBranches($cid = "", $debug = 0)
	{

		$result = array();
		if ($cid != "") {

			$rows = $this->rp_getData("executive_branch", "*", "cid='" . $cid . "' AND isDelete=0", "", $debug);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		}

		return $result;
	}
	function getOutletsBranches($cid = "", $debug = 0)
	{
		$result = array();
		if ($cid != "") {
			$rows = $this->rp_getData("outlets_branch", "*", "cid='" . $cid . "' AND isDelete=0", "", $debug);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		}

		return $result;
	}
	function getExecutiveBranchInfo($cid = "", $cbranchid = "", $debug = 0)
	{

		$result = array();
		if ($cid != "" && $cbranchid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("executive_branch", "*", "id='" . $cbranchid . "' AND cid='" . $cid . "' AND isDelete=0", "", $debug));
		}
		return $result;
	}
	function getOutletsBranchInfo($cid = "", $cbranchid = "", $debug = 0)
	{

		$result = array();
		if ($cid != "" && $cbranchid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("outlets_branch", "*", "id='" . $cbranchid . "' AND cid='" . $cid . "' AND isDelete=0", "", $debug));
		}
		return $result;
	}
	function getVendorBranchInfo($vid = "", $cbranchid = "", $debug = 0)
	{

		$result = array();
		if ($vid != "" && $cbranchid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("dealer_distributor_branch", "*", "id='" . $cbranchid . "' AND vid='" . $vid . "' AND isDelete=0", "", $debug));
		}
		return $result;
	}
	function getCustomerBrancheJobs($cbranchid = "", $status = 1, $debug = 0)
	{

		$result = array();
		if ($cbranchid != "") {
			$rows = $this->rp_getData("job", "*", "branch='" . $cbranchid . "' AND status='" . $status . "' AND isDelete=0", "", $debug);
			if ($rows) {
				while ($data = mysqli_fetch_assoc($rows)) {
					$result[] = $data;
				}
			}
		}
		return $result;
	}

	function getProducts($debug = 0)
	{
		$result = array();
		$rows = $this->rp_getData("product", "*", "isDelete=0 AND 1=1", "", $debug);
		while ($data = mysqli_fetch_assoc($rows)) {
			$result[] = $data;
		}
		return $result;
	}
	function getProductInfo($pid = "", $debug = 0)
	{

		$result = array();
		if ($pid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("product", "*", "id='" . $pid . "' AND isDelete=0 ", "", $debug));
		}

		return $result;
	}
	function getLab($job_id, $debug = 0)
	{
		$result = array();
		$rows = $this->rp_getData("lab", "*", "job_id='" . $job_id . "' AND isDelete=0", "", $debug);
		while ($data = mysqli_fetch_assoc($rows)) {
			$result[] = $data;
		}
		return $result;
	}
	function getJobMaterial($job_id, $customer_id, $where = "", $debug = 0)
	{

		$result = array();
		$row = mysqli_query($this->myconn, "SELECT * FROM lab WHERE job_id='" . $job_id . "' AND isDelete=0");
		$job_information = $this->getJobDetail($job_id);
		$price_list = $this->rp_getValue("customer", "price_list", "id='" . $customer_id . "'");

		while ($r = mysqli_fetch_assoc($row)) {

			$count = 0;
			$tests = explode(",", $r['tests']);
			$testprices = explode(",", $r['test_prices']);
			$sum = 0;
			/*foreach($tests as $t)
			{
				$price=$this->rp_getValue("price_list_map_test","price","test_id='".$t."' AND price_list_id='".$price_list."' AND isDelete=0");
				if($price==0)
				{
					$price=$this->rp_getValue("test","price","id='".$t."' AND isDelete=0",0);
				}
				$r['test_price'][$t]=$price;
				//echo "Test price:".$price."<br>";
				$sum=$sum+intval($price);
				//echo "Total: ".$sum."<br>";
			}*/

			foreach ($testprices as $t) {
				$price = $t;
				$r['test_price'][$t] = $price;
				//echo "Test price:".$price."<br>";
				$sum = $sum + intval($price);
				//echo "Total: ".$sum."<br>";
			}
			if (array_key_exists($r['pid'], $result)) {

				$result[$r['pid']]['total_price'];
				$result[$r['pid']]['labs'] = $result[$r['pid']]['labs'] . "," . $r['id'];
				$result[$r['pid']]['job_no'] = SITE_SHORT . "/" . FINANCIAL_YEAR . "/" . $job_id;
				$result[$r['pid']]['letter_no'] = $job_information['letter_no'];
				$result[$r['pid']]['letter_date'] = date("d-M-y", strtotime($job_information['letter_date']));
				$result[$r['pid']]['qty'] = $result[$r['pid']]['qty'] + 1;
				$result[$r['pid']]['total_price'] = $result[$r['pid']]['total_price'] + $sum;
				$result[$r['pid']]['sample_rate'] = $result[$r['pid']]['total_price'] / $result[$r['pid']]['qty'];
			} else {
				$result[$r['pid']]['pid'] = $r['pid'];
				$result[$r['pid']]['name'] = $this->rp_getValue("product", "name", "id='" . $r['pid'] . "'");
				$result[$r['pid']]['labs'] = $r['id'];
				$result[$r['pid']]['qty'] = 1;
				$result[$r['pid']]['job_no'] = SITE_SHORT . "/" . FINANCIAL_YEAR . "/" . $job_id;
				$result[$r['pid']]['letter_no'] = $job_information['letter_no'];
				$result[$r['pid']]['letter_date'] = date("d-M-y", strtotime($job_information['letter_date']));
				$result[$r['pid']]['total_price'] = $sum;
				$result[$r['pid']]['sample_rate'] = $result[$r['pid']]['total_price'] / $result[$r['pid']]['qty'];
			}

			$result[$r['pid']]['detail'][] = $r;
			$count++;
		}

		return $result;
	}
	function getTest($pid, $debug = 0)
	{
		$result = array();
		$rows = $this->rp_getData("product_map_test", "*", "product_id='" . $pid . "' AND isDelete=0", "", $debug);
		while ($data = mysqli_fetch_assoc($rows)) {
			$test_id = $data['test_id'];
			$r = $this->rp_getData("test", "*", "id='" . $test_id . "'", "", 0);
			$test = mysqli_fetch_assoc($r);
			$result[] = $test;
		}
		return $result;
	}
	function getTests($tid = "", $debug = 0)
	{
		$result = array();
		if ($tid != "") {
			$rows = $this->rp_getData("test", "*", "id='" . $tid . "' AND isDelete=0", "", $debug);
			$result = mysqli_fetch_assoc($rows);
		} else {
			$rows = $this->rp_getData("test", "*", "isDelete=0", "", $debug);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		}

		return $result;
	}
	function getJobDetail($jid, $debug = 0)
	{
		$result = array();
		if ($jid != "") {
			$result = mysqli_fetch_assoc($this->rp_getData("job", "*", "id='" . $jid . "' AND isDelete=0", "", 0));
		}
		return $result;
	}
	function getJobStatus($jobStatus, $html)
	{

		$status = array("In Progress", "Completed", "Billed");
		$statusHtml = array("<span class='text-warning'><i class='fa fa-clock-o'></i> &nbsp;In Progress</span>", "<span class='text-success'><i class='fa fa-check'></i> &nbsp;Completed</span>", "<span class='text-success'><i class='fa fa-print'></i> &nbsp;Billed</span>");
		$jobStatus = intval($jobStatus);
		if (array_key_exists($jobStatus, $status)) {
			if ($html) {
				return $statusHtml[$jobStatus];
			} else {
				return $status[$jobStatus];
			}
		} else {
			return false;
		}
	}
	function getLabStatus($labStatus, $html)
	{
		$status = array("In Progress", "Completed");
		$statusHtml = array("<span class='text-warning'><i class='fa fa-clock-o'></i> &nbsp;In Progress</span>", "<span class='text-success'><i class='fa fa-check'></i> &nbsp;Completed</span>");
		$labStatus = intval($labStatus);
		if (array_key_exists($labStatus, $status)) {
			if ($html) {
				return $statusHtml[$labStatus];
			} else {
				return $status[$labStatus];
			}
		} else {
			return false;
		}
	}
	function labAssistant($lab_id, $debug = 0)
	{
		$lab_assistant_id = $this->rp_getValue("lab", "lab_assistant_id", "id='" . $lab_id . "' AND isDelete=0", "", $debug);
		return $lab_assistant_id;
	}
	function labTests($lab_id, $debug = 0)
	{
		$tests = $this->rp_getValue("lab", "tests", "id='" . $lab_id . "' AND isDelete=0", "", $debug);
		return $tests;
	}
	function isJobsCompleted($jobs)
	{
		if (!empty($jobs)) {
			foreach ($jobs as $j) {
				$status = $this->rp_getValue("job", "status", "id='" . $j . "'");
				if ($status == 0) {
					return false;
				}
			}
			return true;
		} else {
			return false;
		}
	}
	function changeJobsStatus($job_ids, $status)
	{
		$isGoingWell = true;
		foreach ($job_ids as $job_id) {
			$rows = array(
				"status" => $status
			);
			if ($job_id = $this->rp_update("job", $rows, "id='" . $job_id . "'", 0)) {
			} else {
				$isGoingWell = false;
			}
		}
		return $isGoingWell;
	}
	function changeLabsStatus($job_ids, $status)
	{
		$isGoingWell = true;
		foreach ($job_ids as $job_id) {

			$rows = array(
				"status" => $status
			);
			if ($job_id = $this->rp_update("lab", $rows, "job_id='" . $job_id . "'", 0)) {
			} else {
				$isGoingWell = false;
			}
		}
		return $isGoingWell;
	}
	function addCustomerBranch($cid = "", $branch_name = "", $debug = 0)
	{
		if ($branch_name != "" && $cid != "") {
			$adate	= date('Y-m-d H:i:s');
			$rows = array("cid", "branch_name", "adate", "isDelete");
			$values = array($cid, $branch_name, $adate, 0);
			$cbid = $this->rp_insert("customer_branch", $values, $rows, $debug);
			if ($cbid != 0) {
				return $response = array('ack' => 1, 'ack_msg' => 'Branch added Successfully !!!');
			} else {
				return $response = array('ack' => 0, 'ack_msg' => 'Branch name can not be empty !!!');
			}
		} else {
			return $response = array('ack' => 0, 'ack_msg' => 'Branch name can not be empty !!!');
		}
	}
	function changeJobStatus($job_id)
	{
		$allLabStatus = $this->rp_getTotalRecord("lab", "job_id='" . $job_id . "' AND status='0' AND isDelete='0'", 0);

		if ($allLabStatus == 0) {

			$jobStatus = 0;
			$rows = array(
				"status" => 1
			);
			if ($job_id = $this->rp_update("job", $rows, "id='" . $job_id . "'", 0)) {
				$jobStatus = 1;
			}
		} else {

			$rows = array(
				"status" => 0
			);
			if ($job_id = $this->rp_update("job", $rows, "id='" . $job_id . "'", 0)) {
				$jobStatus = 0;
			}
		}
		return $jobStatus;
	}
	function getAdmin($type, $debug)
	{
		$result = array();
		if ($type != "") {
			$rows = $this->rp_getData("stern", "*", "type='" . $type . "' AND isDelete=0", "", $debug);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		}
		return $result;
	}
	function getTaxs($id = "", $debug = 0)
	{
		if ($id == "") {
			$result = array();
			$current_date = date('Y-m-d');
			$rows = $this->rp_getData("tax", "*", "DATE(applied_from)<='" . $current_date . "' AND isDelete=0", "", 0);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		} else {
			$result = array();
			$current_date = date('Y-m-d');
			$result = mysqli_fetch_assoc($this->rp_getData("tax", "*", "DATE(applied_from)<='" . $current_date . "' AND isDelete=0", "", 0));
		}
		return $result;
	}
	function getTaxValues($tax_ids, $debug = 0)
	{
		$result = array();
		foreach ($tax_ids as $tax) {
			$result[] = $this->rp_getValue("tax", "value", "id='" . $tax . "' AND isDelete=0", "", $debug);
		}


		return $result;
	}

	function billTaxCalculation($tax_ids, $tax_values, $tax_type, $total_price)
	{
		$result = array();
		$orignal = $total_price;
		if ($tax_type == 1) {
			$additional_fig = 1.145;
		} else {
			$additional_fig = 1;
		}
		$calculated = $total_price;
		for ($i = 0; $i < sizeof($tax_ids); $i++) {
			$title = $this->rp_getValue("tax", "name", "id='" . $tax_ids[$i] . "'") . "</b> @ " . $tax_values[$i] . "%";
			$value = round(((floatval($total_price) / $additional_fig) * floatval($tax_values[$i])) / 100, 2);
			$result[] = array("title" => $title, "value" => $value);
			$calculated = $value + $calculated;
		}
		if ($tax_type == 2) {
			$result['final_total'] = $calculated;
		} else {
			$result['final_total'] = $orignal;
		}
		return $result;
	}
	function getBillType($bill_id = "", $debug = 0)
	{
		if ($bill_id == "") {
			$result = array();
			$rows = $this->rp_getData("bill_type", "*", "isDelete=0", "", 0);
			while ($data = mysqli_fetch_assoc($rows)) {
				$result[] = $data;
			}
		} else {
			$result = array();
			$result = mysqli_fetch_assoc($this->rp_getData("tax", "*", "id='" . $tbill_id . "'", "", 0));
		}
		return $result;
	}
	function addErrorMessage($message)
	{
		if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != "") {
			unset($_SESSION['error_message']);
		}
		if ($message != "")
			$_SESSION['error_message'] = $message;
	}
	function addSuccessMessage($message)
	{
		if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != "") {
			unset($_SESSION['success_message']);
		}
		if ($message != "")
			$_SESSION['success_message'] = $message;
	}
	function printErrorMessage()
	{
		if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != "") {
		?>
			<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b><?php echo $_SESSION['error_message']; ?></b>
			</div>

		<?php
			unset($_SESSION['error_message']);
		}
	}

	function printSuccessMessage()
	{
		if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != "") {
		?>

			<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b><?php echo $_SESSION['success_message']; ?></b>
			</div>

		<?php
			unset($_SESSION['success_message']);
		}
	}
	public function checkAPI($api_slug, $die = 0)
	{
		$count = $this->rp_getTotalRecord("api_table", "api_slug='" . $api_slug . "' OR id='" . $api_slug . "'");
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function printJSON($val, $die = 0)
	{
		//$val["extra"]=array("requested_params"=>$_REQUEST);
		echo json_encode($val);
		if ($die)
			exit();
	}
	public function checkAPIKey($key, $die = 0)
	{
		$count = $this->rp_getTotalRecord("api_key_table", "api_key='" . $key . "'");
		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function aj_updateUserPassword($id, $newPassword, $password)
	{

		$rows = array("password" => $newPassword);
		$where = " id='" . $id . "'";
		return $this->rp_update("sales_executive", $rows, $where, 0);
	}
	public function aj_updateUserPasswordCustomer($id, $newPassword, $password)
	{

		$rows = array("password" => $newPassword);
		$where = " id='" . $id . "'";
		return $this->rp_update("executive", $rows, $where, 0);
	}
	function getItemMaster($category_id)
	{

		$ctable_fg = "fg_item_master";
		$ctable_rm = "rm_item_master";
		$ctable_cg = "cg_item_master";
		$ctable_cog = "cog_item_master";

		if ($category_id == 1) {
			$ctable = $ctable_fg;
		} else if ($category_id == 2) {
			$ctable = $ctable_rm;
		} else if ($category_id == 3) {
			$ctable = $ctable_cg;
		} else if ($category_id == 4) {
			$ctable = $ctable_cog;
		}
		return $ctable;
	}
	public function getRequestedParam($val, $die = 0)
	{
		if ($val != "") {
			return (isset($_REQUEST[$val]) && $_REQUEST[$val] != "") ? $_REQUEST[$val] : "";
		} else {
			return "";
		}
		if ($die)
			exit();
	}
	public function update_table_information($table_slug, $date)
	{
		//echo "UPDATE ".CTABLE_INFORMATION_SCHEMA." SET last_modify_date='".$date."' WHERE `table_slug`='".$table_slug."'";
		//exit;
		$isUpdated = mysqli_query($this->myconn, "UPDATE " . CTABLE_INFORMATION_SCHEMA . " SET last_modify_date='" . $date . "' WHERE `table_slug`='" . $table_slug . "'");
		if ($isUpdated) {
			return true;
		} else {
			return false;
		}
	}
	public function getLimit()
	{
		$ul = $this->getRequestedParam("ul"); //upper_limit
		$ll = $this->getRequestedParam("ll"); //lower_limit
		if ($ul != "") {
			return array("ul" => $ul, "ll" => $ll);
		} else {
			return array();
		}
	}
	function pageBar($hierarchy, $pageToolbar = "")
	{
		if (!empty($hierarchy)) {
		?>
			<!-- BEGIN PAGE BAR -->

			<!--ul class="page-breadcrumb"-->
			<?php for ($i = 0; $i < sizeof($hierarchy); $i++) {
			?>
				<!--li-->
				<?php if ($i != sizeof($hierarchy) - 1) {
				?>
					<a href="<?php echo $hierarchy[$i]['link']; ?>"><?php echo $hierarchy[$i]['title']; ?></a>
					<!--i class="fa fa-circle"></i-->
					<i class="fa fa-chevron-right"></i>

				<?php
				} else {
				?>
					<span><?php echo $hierarchy[$i]['title']; ?></span>
				<?php
				}
				?>
				<!--/li-->
			<?php
			}
			?>
			<!--/ul-->
			<div class="page-toolbar">
				<?php echo $pageToolbar; ?>
			</div>

			<!-- END PAGE BAR -->
<?php
		}
	}

	public function rp_getQuery($query, $die = 0) // Select Query, $die==1 will print query By Ravi Patel
	{
		$results = array();
		$q = $query;
		if ($die == 1) {
			echo $q;
			die;
		}
		if (mysqli_num_rows(mysqli_query($this->myconn, $q)) > 0) {
			$results = @mysqli_query($this->myconn, $q);
			return $results;
		} else {
			return false;
		}
	}

	function rp_round($value, $precision = 3)
	{
		return round($value, $precision);
	}
	function rp_number_format($value, $precision = 3)
	{
		$value = $this->rp_round($value, $precision);
		return number_format($value, $precision);
	}
	function pretty_print($json_data)
	{

		//Initialize variable for adding space
		$space = 0;
		$flag = false;

		//Using <pre> tag to format alignment and font
		echo "<pre>";

		//loop for iterating the full json data
		for ($counter = 0; $counter < strlen($json_data); $counter++) {

			//Checking ending second and third brackets
			if ($json_data[$counter] == '}' || $json_data[$counter] == ']') {
				$space--;
				echo "\n";
				echo str_repeat(' ', ($space * 2));
			}


			//Checking for double quote(“) and comma (,)
			if ($json_data[$counter] == '"' && ($json_data[$counter - 1] == ',' ||
				$json_data[$counter - 2] == ',')) {
				echo "\n";
				echo str_repeat(' ', ($space * 2));
			}
			if ($json_data[$counter] == '"' && !$flag) {
				if ($json_data[$counter - 1] == ':' || $json_data[$counter - 2] == ':')

					//Add formatting for question and answer
					echo '<span style="color:blue;font-weight:bold">';
				else

					//Add formatting for answer options
					echo '<span style="color:red;">';
			}
			echo $json_data[$counter];
			//Checking conditions for adding closing span tag
			if ($json_data[$counter] == '"' && $flag)
				echo '</span>';
			if ($json_data[$counter] == '"')
				$flag = !$flag;

			//Checking starting second and third brackets
			if ($json_data[$counter] == '{' || $json_data[$counter] == '[') {
				$space++;
				echo "\n";
				echo str_repeat(' ', ($space * 2));
			}
		}
		echo "</pre>";
	}

	function reverce_gst($tax, $amount)
	{
		if ($tax <= 100) {
			if ($tax == 100) {
				$reply = $amount / 2;
			} else {
				$prezero = sprintf("%02d", $tax);
				$div = "1." . $prezero;
				$reply = $amount / $div;
			}
		} else {
			$reply = "tax not be grater than 100";
		}
		return $reply;
	}
	function formatPhoneNumber($phoneNumber)
	{
		$phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

		if (strlen($phoneNumber) > 10) {
			$countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
			$areaCode = substr($phoneNumber, -10, 3);
			$nextThree = substr($phoneNumber, -7, 3);
			$lastFour = substr($phoneNumber, -4, 4);

			$phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
		} else if (strlen($phoneNumber) == 10) {
			$areaCode = substr($phoneNumber, 0, 3);
			$nextThree = substr($phoneNumber, 3, 3);
			$lastFour = substr($phoneNumber, 6, 4);

			$phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
		} else if (strlen($phoneNumber) == 7) {
			$nextThree = substr($phoneNumber, 0, 3);
			$lastFour = substr($phoneNumber, 3, 4);

			$phoneNumber = $nextThree . '-' . $lastFour;
		}

		return $phoneNumber;
	}
	function get_report($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);
		$request = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		$error = curl_error($ch);
		curl_close($ch);
		return ($output);
	}
	function getAddress($lat, $lang)
	{
		if ($lat != "" && $lang != "") {
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lang . "&key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4";
			$result = "";
			$data = "";
			// $result = json_encode($this->get_report($url), true);
			$response = file_get_contents($url);
			$jsonText = $response;
			$decodedText = html_entity_decode($jsonText);
			$data = json_decode($decodedText, true);
			$result = $data['results']['0']['formatted_address'];
		} else {
			$result = $lat . "," . $lang;
		}
		return $result;
	}

	function custom_html_entity_decode2($string)
	{
		// return $string;
		return html_entity_decode(str_replace("\\", "", str_replace("\\r", "", str_replace("\\n", "", $string))));
	}

	function getCassAreaIdFromName($class_name, $main_city_name, $area_name)
	{
		$class_id = 0;
		$area_id = 0;
		if ($class_name != "") {
			$CheckClassAvailable = $this->rp_getValue("class", "COUNT(*)", "LOWER(name) = '" . trim(strtolower($class_name)) . "' AND isDelete = 0 AND isActive = 1");
			if ($CheckClassAvailable > 0) {
				$class_id = $this->rp_getValue("class", "id", "LOWER(name) = '" . trim(strtolower($class_name)) . "' AND isDelete = 0 AND isActive = 1");
			} else {
				$InsertData = array(
					"name" => trim($this->clean($class_name)),
					"slug" => $this->rp_createSlug($this->clean($class_name)),
					"isDelete" => 0,
					"isActive" => 1,
				);
				$class_id = $this->rp_insert("class", array_values($InsertData), array_keys($InsertData), 0);
			}
			if ($area_name != "") {
				$CheckAreaAvailable = $this->rp_getValue("area", "COUNT(*)", "LOWER(name) = '" . trim(strtolower($area_name)) . "'  AND class_id='" . $class_id . "' AND isDelete = 0 AND isActive = 1");
				if ($CheckAreaAvailable > 0) {
					$area_id = $this->rp_getValue("area", "id", "LOWER(name) = '" . trim(strtolower($area_name)) . "' AND class_id='" . $class_id . "' AND isDelete = 0 AND isActive = 1");
				} else {
					$InsertData = array(
						"name" => trim($this->clean($area_name)),
						"area_slug" => $this->rp_createSlug($this->clean($area_name)),
						"class_id" => $class_id,
						"isDelete" => 0,
						"isActive" => 1,
					);
					$area_id = $this->rp_insert("area", array_values($InsertData), array_keys($InsertData), 0);
				}
			}
			if ($main_city_name != "") {
				$CheckcityAvailable = $this->rp_getValue("city", "COUNT(*)", "LOWER(name) = '" . trim(strtolower($main_city_name)) . "'  AND state_id='" . $class_id . "' AND isDelete = 0", 0);
				if ($CheckcityAvailable > 0) {
					$city_id = $this->rp_getValue("city", "id", "LOWER(name) = '" . trim(strtolower($main_city_name)) . "' AND state_id='" . $class_id . "' AND isDelete = 0");
				} else {
					$InsertData = array(
						"name" => trim($this->clean($main_city_name)),
						"city" => $this->rp_createSlug($this->clean($main_city_name)),
						"state_id" => $class_id,
						"isDelete" => 0,

					);
					$city_id = $this->rp_insert("city", array_values($InsertData), array_keys($InsertData), 0);
				}
			}
		}
		return array("class_id" => $class_id, "area_id" => $area_id, "city_id" => $city_id);
	}

	/*for get Commasepretd Value*/
	public function getCommaSepretedData($table, $value = "*", $searchvalue, $comparevalue, $order_by = null, $die = 0, $limit = "")
	{
		$data_array = array();
		$product_codeR = $this->rp_getData($table, $value, $comparevalue . " LIKE '%" . $searchvalue . "%' AND isDelete=0", $order_by, $die = 0, $limit);
		while ($product_codeD = mysqli_fetch_assoc($product_codeR)) {
			$data_array[] =  $product_codeD['product_id'];
		}
		$data_array = implode(",", $data_array);
		return $data_array;
	}
	/*for get Commasepretd Value*/

	/*use below function for pipeline*/

	/*quotation*/
	public function GetTaskContainer($V)
	{

		$StartContainer = "";
		$EndContainer = "";
		if ($V == 0) {
			$StartContainer = '<div class="row  row-eq-height">';
			$EndContainer = '</div>';
		} else if ($V == 1) {
			$StartContainer = '<div class="table-scrollable">
							<table id="datatable_1" class="table table-bordered">
							<thead>
								<tr>
									<th>SP NO.</th>
									<th>DATE</th>
									<th>CUSTOMER NAME</th>
									<th>CITY</th>
									<th>OPPORTUNITY</th>
									<th>PRIORITY</th>
									<th>EXPECTED CLOSING DATE</th>
									<th>STATUS</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody class="task-row-container">';
			$EndContainer = '</tbody>
							<tfoot>
							<tr>
								<td colspan="8" class="text-center">End Of Pipeline</td>
							</tr>
							</tfoot>
							</table>
							</div>';
		}

		return array($StartContainer, $EndContainer);
	}

	public function GetTaskStatusView($ViewType, $TS)
	{
		$View = "";
		$deadlineColor = "";

		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
			$where .= " AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
		}

		$sales_quotation = $this->rp_getValue("quotation_detail", "SUM(grand_total)", "status='" . $TS['id'] . "' AND  isDelete=0" . $where, 0);

		$sales_quotation = ($sales_quotation != "" && $sales_quotation != null && $sales_quotation != 'null' && $sales_quotation != 'NULL' && $sales_quotation != NULL && $sales_quotation != undefined && $sales_quotation != 'undefined') ? $sales_quotation : 0;
		$sales_quotation = $this->rp_num($sales_quotation);


		$sales_order = $this->rp_getTotalRecord("quotation_detail", "status='" . $TS['id'] . "' AND  isDelete=0" . $where, 0);
		$sales_order = ($sales_order != "" && $sales_order != null && $sales_order != 'null' && $sales_order != 'NULL' && $sales_order != NULL && $sales_order != undefined && $sales_order != 'undefined') ? $sales_order : 0;
		//$sales_order = $this->rp_num($sales_order);

		if ($TS['name'] == "Pending") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#9fc1ff;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Approved") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#7bd0a9;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Disapproved") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:red;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Cancelled") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#ec9b97;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Order Generated") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#126608;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Lost") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:grey;float:right;font-size:30px;"></i>';
		}

		if ($ViewType == '0') {
			// /$TS['id'] = 1;
			$View = '<div class="col-sm-2 ">
                <div id="Task' . $TS['id'] . '" class="task-stage" data-id="' . $TS['id'] . '">
                    <div class="task-title">
                      ' . $TS['name'] . ' ' . $deadlineColor . '
                      <div style="font-size: 12px;position: absolute;top: 38px;">Quotation No : <b style="color:#4aff4a" class="all-quotation-count quotation-count-' . $TS['id'] . '">' . $sales_order . '</b></div>
                      <div style="font-size: 12px;position: absolute;top: 57px;">Quotation Amt : <b style="color:#4aff4a" class="all-order-count order-count-' . $TS['id'] . '">' . $sales_quotation . '</b></div>';
			$View .= '</div>
                   		<ul class="task-body" >                     
                        </ul>
             		</div>
            	</div>';
		} else {
		}

		return $View;
	}
	/*quotation*/

	/*order*/
	public function GetTaskContainerOrder($V)
	{
		$StartContainer = "";
		$EndContainer = "";
		if ($V == 0) {
			$StartContainer = '<div class="row  row-eq-height">';
			$EndContainer = '</div>';
		} else if ($V == 1) {
			$StartContainer = '<div class="table-scrollable">
							<table id="datatable_1" class="table table-bordered">
							<thead>
								<tr>
									<th>SP NO.</th>
									<th>DATE</th>
									<th>CUSTOMER NAME</th>
									<th>CITY</th>
									<th>OPPORTUNITY</th>
									<th>PRIORITY</th>
									<th>EXPECTED CLOSING DATE</th>
									<th>STATUS</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody class="task-row-container">';
			$EndContainer = '</tbody>
							<tfoot>
							<tr>
								<td colspan="8" class="text-center">End Of Pipeline</td>
							</tr>
							</tfoot>
							</table>
							</div>';
		}

		return array($StartContainer, $EndContainer);
	}

	public function GetTaskStatusViewOrder($ViewType, $TS)
	{
		$View = "";
		$deadlineColor = "";

		if ($TS['id'] == "6") {
			$TS['id'] = "2";
		}

		if ($TS['id'] == "7") {
			$TS['id'] = "4";
		}

		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
			$where .= " AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
		}

		$order_total = $this->rp_getValue("orders", "SUM(grand_total)", "status='" . $TS['id'] . "' AND  isDelete=0" . $where, 0);
		$order_total = ($order_total != "" && $order_total != null && $order_total != 'null' && $order_total != 'NULL' && $order_total != NULL && $order_total != undefined && $order_total != 'undefined') ? $order_total : 0;
		$order_total = $this->rp_num($order_total);

		$order_count = $this->rp_getTotalRecord("orders", "status='" . $TS['id'] . "' AND  isDelete=0" . $where, 0);
		$order_count = ($order_count != "" && $order_count != null && $order_count != 'null' && $order_count != 'NULL' && $order_count != NULL && $order_count != undefined && $order_count != 'undefined') ? $order_count : 0;

		if ($TS['name'] == "Pending") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#9fc1ff;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Approved") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#7bd0a9;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Disapproved") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:red;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Cancelled") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#ec9b97;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Dispatch") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#126608;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Partially Dispatched") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:grey;float:right;font-size:30px;"></i>';
		}

		if ($ViewType == '0') {
			// /$TS['id'] = 1;
			$View = '<div class="col-sm-2 ">
                <div id="Task' . $TS['id'] . '" class="task-stage" data-id="' . $TS['id'] . '">
                    <div class="task-title" style="font-size: 15px!important;">
                      ' . $TS['name'] . ' ' . $deadlineColor . '
                      <div style="font-size: 12px;position: absolute;top: 38px;">Order No : <b style="color:#4aff4a" class="all-quotation-count quotation-count-' . $TS['id'] . '">' . $order_count . '</b></div>
                      <div style="font-size: 12px;position: absolute;top: 57px;">Order Amt : <b style="color:#4aff4a" class="all-order-count order-count-' . $TS['id'] . '">' . $order_total . '</b></div>';
			$View .= '</div>
                   		<ul class="task-body" >                     
                        </ul>
             		</div>
            	</div>';
		} else {
		}

		return $View;
	}

	/*order*/

	/*inquiry*/
	public function GetTaskContainerInquiry($V)
	{

		$StartContainer = "";
		$EndContainer = "";
		if ($V == 0) {
			$StartContainer = '<div class="row  row-eq-height">';
			$EndContainer = '</div>';
		} else if ($V == 1) {
			$StartContainer = '<div class="table-scrollable">
							<table id="datatable_1" class="table table-bordered">
							<thead>
								<tr>
									<th>SP NO.</th>
									<th>DATE</th>
									<th>CUSTOMER NAME</th>
									<th>CITY</th>
									<th>OPPORTUNITY</th>
									<th>PRIORITY</th>
									<th>EXPECTED CLOSING DATE</th>
									<th>STATUS</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody class="task-row-container">';
			$EndContainer = '</tbody>
							<tfoot>
							<tr>
								<td colspan="8" class="text-center">End Of Pipeline</td>
							</tr>
							</tfoot>
							</table>
							</div>';
		}

		return array($StartContainer, $EndContainer);
	}

	public function GetTaskStatusViewInquiry($ViewType, $TS, $type)
	{
		$View = "";
		$deadlineColor = "";

		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
			$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
			$where .= " AND (inquiry_assign_to = '" . $check_id . "' OR inquiry_created_by = '" . $check_id . "') ";
		}

		$total_inquiry = $this->rp_getTotalRecord("no_order_inquiry", "status='" . $TS['id'] . "' AND  isDelete=0 AND inquiry_lead_flag='" . $type . "'" . $where, 0);

		$total_inquiry = ($total_inquiry != "" && $total_inquiry != null && $total_inquiry != 'null' && $total_inquiry != 'NULL' && $total_inquiry != NULL && $total_inquiry != undefined && $total_inquiry != 'undefined') ? $total_inquiry : 0;

		if ($TS['name'] == "Generate") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#7bd0a9;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "In followup") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#9fc1ff;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Hot") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#65B237;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Cold") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#3787B2;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Warm") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#B2A137;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Non Relevant") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#126608;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Not Intrested") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#ec9b97;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Buy Later") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:grey;float:right;font-size:30px;"></i>';
		} else if ($TS['name'] == "Lost") {
			$deadlineColor = '<i class="fa fa-circle blink_me_dead" style="color:#ffa07a;float:right;font-size:30px;"></i>';
		}


		if ($ViewType == '0') {
			// /$TS['id'] = 1;
			$View = '<div class="col-sm-2 ">
                <div id="Task' . $TS['id'] . '" class="task-stage" data-id="' . $TS['id'] . '">
                    <div class="task-title">
                      ' . $TS['name'] . ' ' . $deadlineColor . '
                      <div style="font-size: 12px;position: absolute;top: 38px;">Total Inquiry : <b style="color:#4aff4a" class="all-quotation-count quotation-count-' . $TS['id'] . '">' . $total_inquiry . '</b></div>';
			$View .= '</div>
                   		<ul class="task-body" >                     
                        </ul>
             		</div>
            	</div>';
		} else {
		}

		return $View;
	}
	/*inquiry*/

	public function print_json($val, $die = 0)
	{
		echo json_encode($val);
		if ($die)
			exit();
	}
	/*use below function for pipeline*/

	/*Send notification Funcrtion*/
	public function send_notificationpanel($data, $ids, $ReferanceArray = array(), $upperleval1, $UpperlevelAll)
	{
		if (!array_key_exists("icon", $data)) {
			$data['icon'] = NOTIFICATIONICON;
		}
		if (!array_key_exists("image", $data)) {
			$data['image'] = NOTIFICATIONIMAGE;
		}
		if (!array_key_exists("link", $data)) {
			$data['link'] = ADMINSITEURL;
		}
		if (!array_key_exists("sound", $data)) {
			$data['sound'] = 'default';
		}
		if (!array_key_exists("show_in_foreground", $data)) {
			$data['show_in_foreground'] = true;
		}
		if (!array_key_exists("targetScreen", $data)) {
			$data['targetScreen'] = 'detail';
		}
		if (!array_key_exists("color", $data)) {
			$data['color'] = '#203E78';
		}

		if ($upperleval1 == "1") {
			//$RefreshTokenIds[] = $this->getUpperLevelToken($ids);
			//$RefreshTokenIds[] = $this->getUpperLevelToken($ids);
			$sales_type = $this->rp_getValue("sales_executive", "type", "id='" . $ids . "' AND isDelete=0", 0);

			if ($sales_type == "sales_officer") {
				$GetUpperIDS = $this->rp_getValue("sales_executive", "asm_id", "id='" . $ids . "' AND isDelete=0", 0);
			} else if ($sales_type == "sales_executive") {
				$GetUpperIDS  = $this->rp_getValue("sales_executive", "so_id", "id='" . $ids . "' AND isDelete=0", 0);
			} else if ($sales_type == "area_sales_manager") {
				$GetUpperIDS  = $this->rp_getValue("sales_executive", "sm_id", "id='" . $ids . "' AND isDelete=0", 0);
			} else {
				$GetUpperIDS  = 0;
			}
		}

		if ($UpperlevelAll == "1") {
			//$RefreshTokenIds[] = $this->getAllUpperLevelToken($ids);
			$IDS = array();
			$IdsR = $this->rp_getData("sales_executive", "sm_id,asm_id,so_id", "id='" . $ids . "' AND isDelete=0", "", 0);
			while ($IdsD = mysqli_fetch_assoc($IdsR)) {
				$IDS[] = $IdsD['sm_id'];
				$IDS[] = $IdsD['asm_id'];
				$IDS[] = $IdsD['so_id'];
				$IDS[] = 0;
			}
			$IDS = implode(",", $IDS);
		}

		$RefreshTokenR = $this->rp_getData("dealer_distributor_network", "refresh_token,refresh_token_mobile", "isDelete=0  AND sales_executive_id IN(" . $ids . ")", "", 0);
		$RefreshTokenIds = array();
		while ($RefreshTokenD = mysqli_fetch_assoc($RefreshTokenR)) {
			if ($RefreshTokenD['refresh_token'] != "") {
				$RefreshTokenIds[] = $RefreshTokenD['refresh_token'];
			}
			if ($RefreshTokenD['refresh_token_mobile'] != "") {
				$RefreshTokenIds[] = $RefreshTokenD['refresh_token_mobile'];
			}
		}

		$RefreshTokenIds = array_filter($RefreshTokenIds, function ($value) {
			return !is_null($value) && $value !== '' && trim($value) !== '';
		});

		$apiKey = 'AAAAUIk9V4k:APA91bFFHkvLlnxxOk9QV7Ua6d1zlonsfbJIcEf0yoF1a9g2Dte_zvk2i7sgpW_kz2bDz2CAR0cuyi4Y2qG1AeZkWT_LPErU0NbB2o6yT7ZQueHYQ1mVEoYt-JxQjacdp9zQvtnD8eUW'; // This is Server Legacy Key From Cloud Messaging Firebase
		$url = 'https://fcm.googleapis.com/fcm/send';
		$payload = array(
			'registration_ids'  => $RefreshTokenIds,
			'data'              => $data,
			'priority' 			=> 'high',
		);

		//	print_r($payload);
		$header = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: Application/json'
		);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_HTTPHEADER => $header
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		if ($err) {
			// 	  echo "cURL Error #:" . $err;
			return $err;
		} else {

			$idsArray = explode(",", $ids);
			$DataToInsert = array();
			foreach ($idsArray as $key => $value) {
				$DataToInsert['user_id'] = $value;
				$DataToInsert['notification_title'] = $data['title'];
				$DataToInsert['notification_description'] = $data['body'];
				//$DataToInsert['created_date'] = date("Y-m-d H:i:s");
				//	$DataToInsert['created_by'] = $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
				//	$DataToInsert['created_by_type'] = $_SESSION[SITE_SESS.'_ADMIN_TYPE'];
				if (sizeof($ReferanceArray) > 0) {
					$DataToInsert['referance_id'] = $ReferanceArray['reference_id'];
					$DataToInsert['referance_type'] = $ReferanceArray['reference_table'];
				}

				$this->rp_insert("notification", array_values($DataToInsert), array_keys($DataToInsert), 0);
			}
		}

		return $response;
		//	print_r($response);exit;
	}
	/*Send notification Funcrtion*/

	public function send_notificationApplication($data, $ids, $which_notification)
	{
		// print_r($ids);
		if ($which_notification == 1) {
			$apiKey = 'AAAAUIk9V4k:APA91bFFHkvLlnxxOk9QV7Ua6d1zlonsfbJIcEf0yoF1a9g2Dte_zvk2i7sgpW_kz2bDz2CAR0cuyi4Y2qG1AeZkWT_LPErU0NbB2o6yT7ZQueHYQ1mVEoYt-JxQjacdp9zQvtnD8eUW'; // This is Server Legacy Key From Cloud Messaging Firebase
		} else {
			$apiKey = 'AAAAUIk9V4k:APA91bFFHkvLlnxxOk9QV7Ua6d1zlonsfbJIcEf0yoF1a9g2Dte_zvk2i7sgpW_kz2bDz2CAR0cuyi4Y2qG1AeZkWT_LPErU0NbB2o6yT7ZQueHYQ1mVEoYt-JxQjacdp9zQvtnD8eUW'; // This is Server Legacy Key From Cloud Messaging Firebase
		}
		//$url = 'https://android.googleapis.com/gcm/send';
		$url = 'https://fcm.googleapis.com/fcm/send';
		$post = array(
			'registration_ids'  => $ids,
			'data'              => $data,
		);

		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		//print_r($post);exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //////// SSL Verifier False ////////
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		$result = curl_exec($ch);
		curl_close($ch);
		//print_r($result);exit;
		return $result;
	}

	public function SendFollowupNotification()
	{
		/*
		if (strtotime($given_time) >= time()+300) echo "You are online";

		$followusps=$this->rp_getData("followup","*","DATE_FORMAT(followup_date, '%Y-%m-%d %H')='".date('Y-m-d H',strtotime('+1 hour'))."' AND status=0 AND next_action!= -1 AND isDelete=0 AND is_notification_send=0","",0);*/


		$followusps = $this->rp_getData("followup", "*", "followup_date <  NOW() + INTERVAL + '" . FOLLOUP_NOTIFICATION_TIME . "' MINUTE AND status=0 AND next_action!= -1 AND isDelete=0 AND is_notification_send=0", "", 0);




		if ($followusps) {
			while ($followusp = mysqli_fetch_assoc($followusps)) {
				// print_r($followusp);exit;
				///// send notification And Add notification
				$status = array("1" => "Call", "2" => "SMS", "3" => "Email");
				$visitor_name = $this->rp_getValue("visitor", "name", "id='" . $followusp['visitor_id'] . "'");
				$notification_title = "Followup required for " . $visitor_name . " by " . $status[$followusp['through']] . " on " . date("H:i", strtotime($followusp['followup_date']));
				$notification_description = $followusp['description'];
				$notification_type = "1";
				$type_slug = "";
				$rows 	= array(
					"user_id",
					"reference_id",
					"reference_type",
					"notification_title",
					"notification_description",
					"notification_type",
					"type_slug",
					"created_date",
				);
				$values = array(
					$followusp['user_id'],
					$followusp['id'],
					"followup",
					$notification_title,
					$notification_description,
					$notification_type,
					$type_slug,
					date('Y-m-d H:i:s'),
				);
				$this->rp_insert("notification", $values, $rows, 0);
				$msg = array(
					"type"		 => $notification_type,
					"title"		 => $notification_title,
					"description" => $notification_description,
					"user_id" => $followusp['user_id'],
					"reference_id" => $followusp['id'],
					"reference_type =>'followup'",
				);


				$Data = [
					'title' => $notification_title,
					'body' =>  $notification_description,
					'icon' => NOTIFICATIONICON,
					'image' => NOTIFICATIONIMAGE,
				];
				$ReferanceArray =
					[
						'reference_id' => 	$followusp['id'],
						'reference_table' => "followup",
					];

				$user_id = $this->rp_getData("sales_executive", "*", "type=0 AND id!='" . $followusp['user_id'] . "' AND isDelete=0", "", 0);
				if ($user_id) {
					while ($v = mysqli_fetch_assoc($user_id)) {
						$user[] = $v['id'];
					}
				}
				// print_r($user); exit;
				/*	$refresh_tokens_web=$this->rp_getData("dealer_distributor_network","refresh_token","sales_executive_id='".$followusp['user_id']."'","",0);
					if($refresh_tokens_web){
						$tokens_web=array();
						while($refresh_token_web=mysqli_fetch_assoc($refresh_tokens_web))
						{
								$tokens_web[]=$refresh_token_web['refresh_token'];
						}*/

				$this->send_notificationpanel($Data, $followusp['user_id'], $ReferanceArray);
				//	}
				//print_r($result);

				$refresh_tokens_app = $this->rp_getData("sales_executive", "refreshToken", "id='" . $followusp['user_id'] . "'", "", 0);
				if ($refresh_tokens_app) {
					$tokens_app = array();
					while ($refresh_token_app = mysqli_fetch_assoc($refresh_tokens_app)) {
						$tokens_app[] = $refresh_token_app['refreshToken'];
					}


					$result = $this->send_notificationApplication($msg, $tokens_app);
				}
				$this->rp_update("followup", array("is_notification_send" => 1), "id='" . $followusp['id'] . "'");
			}
			$reply = array("ack" => 1, "developer_msg" => "Notification send successfully!", "ack_msg" => "Notification send successfully!");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Followup not found", "ack_msg" => "Followup not found");
			return $reply;
		}
	}

	public function getUpperLevelToken($salesid)
	{
		$TokenIds = array();
		$sales_type = $this->rp_getValue("sales_executive", "type", "id='" . $salesid . "' AND isDelete=0", 0);

		if ($sales_type == "sales_officer") {
			$GetUpperIDS = $this->rp_getValue("sales_executive", "asm_id", "id='" . $salesid . "' AND isDelete=0", 0);
		} else if ($sales_type == "sales_executive") {
			$GetUpperIDS  = $this->rp_getValue("sales_executive", "so_id", "id='" . $salesid . "' AND isDelete=0", 0);
		} else if ($sales_type == "area_sales_manager") {
			$GetUpperIDS  = $this->rp_getValue("sales_executive", "sm_id", "id='" . $salesid . "' AND isDelete=0", 0);
		} else {
			$GetUpperIDS  = 0;
		}

		$TokenR = $this->rp_getData("dealer_distributor_network", "refresh_token_web,refresh_token_mobile_web,refresh_token_android_app,refresh_token_ios_app", "isDelete=0  AND sales_executive_id = '" . $GetUpperIDS . "'", "", 0);
		while ($TokenD = mysqli_fetch_assoc($TokenR)) {
			if ($TokenD['refresh_token_web'] != "") {
				$TokenIds[] = $TokenD['refresh_token_web'];
			}
			if ($TokenD['refresh_token_mobile_web'] != "") {
				$TokenIds[] = $TokenD['refresh_token_mobile_web'];
			}
			if ($TokenD['refresh_token_android_app'] != "") {
				$TokenIds[] = $TokenD['refresh_token_android_app'];
			}
			if ($TokenD['refresh_token_ios_app'] != "") {
				$TokenIds[] = $TokenD['refresh_token_ios_app'];
			}
		}
		return $TokenIds;
	}

	public function getAllUpperLevelToken($salesid)
	{
		$AllTokenIds = array();
		$IDS = array();
		$IdsR = $this->rp_getData("sales_executive", "sm_id,asm_id,so_id", "id='" . $salesid . "' AND isDelete=0", 0);
		while ($IdsD = mysqli_fetch_assoc($IdsR)) {
			$IDS[] = $IdsD['sm_id'];
			$IDS[] = $IdsD['asm_id'];
			$IDS[] = $IdsD['so_id'];
		}
		$IDS = implode(",", $IDS);
		$get_TokenR = $this->rp_getData("dealer_distributor_network", "refresh_token_web,refresh_token_mobile_web,refresh_token_android_app,refresh_token_ios_app", "isDelete=0  AND sales_executive_id IN (" . $IDS . ") ", "", 0);
		while ($get_TokenD = mysqli_fetch_assoc($get_TokenR)) {
			if ($get_TokenD['refresh_token_web'] != "") {
				$AllTokenIds[] = $get_TokenD['refresh_token_web'];
			}
			if ($get_TokenD['refresh_token_mobile_web'] != "") {
				$AllTokenIds[] = $get_TokenD['refresh_token_mobile_web'];
			}
			if ($get_TokenD['refresh_token_android_app'] != "") {
				$AllTokenIds[] = $get_TokenD['refresh_token_android_app'];
			}
			if ($get_TokenD['refresh_token_ios_app'] != "") {
				$AllTokenIds[] = $get_TokenD['refresh_token_ios_app'];
			}
		}
		return $AllTokenIds;
	}

	/*for licence */
	public function encrypt_decrypt($action, $string)
	{

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'This is my secret key';
		$secret_iv = 'This is my secret iv';
		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	/*for licence */

	public	function unique_multi_array($array, $key)
	{
		$temp_array = array();
		$i = 0;
		$key_array = array();

		foreach ($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}

	/*for Get Available Stock */
	// public function get_available_stock($pro_id,$weight_id)
	// {
	// 	$packing_slip_qty = $this->rp_getValue("packing_slip_item","SUM(pro_qty)","isDelete=0 AND pro_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0",0);

	// 	$opening_stock_qty = $this->rp_getValue("product_weight_price","opening_stock_qty","isDelete=0 AND product_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0",0);

	// 	$manual_stock_qty = $this->rp_getValue("inward_stock","SUM(pro_qty)","isDelete=0 AND pro_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0",0);
	// 	$available_stock=(($opening_stock_qty) + ($manual_stock_qty))-($packing_slip_qty);

	// 	 return $available_stock;
	// }
	public function get_available_stock($pro_id, $weight_id, $warehouse_id = "")
	{
		$where = "pro_id='" . $pro_id . "' AND weight_id='" . $weight_id . "' AND isDelete=0 AND isActive";
		$where1 = "product_id'" . $pro_id . "' AND weight_id='" . $weight_id . "' AND isDelete=0 AND isActive";
		if ($warehouse_id != "") {
			$where .= " AND warehouse_id='" . $warehouse_id . "'";
			$where1 .= " AND warehouse_id='" . $warehouse_id . "'";
		}
		// $packing_slip_qty = $this->rp_getValue("packing_slip_item","SUM(pro_qty)","pro_id='".$pro_id."' AND weight_id='".$weight_id."' AND isDelete=0",0);
		// $dispatch_qty = $this->rp_getValue("dispatch_item","SUM(qty)",$where,0);

		// $opening_stock_qty=$this->rp_getValue("product_weight_price","opening_stock_qty",$where1,0);

		$manual_stock_qty = $this->rp_getValue("inward_stock", "SUM(pro_qty)", $where, 0);
		$available_stock = $manual_stock_qty;
		// $available_stock=(($opening_stock_qty) + ($manual_stock_qty))-($dispatch_qty);

		return $available_stock;
	}
	/*for Get Available Stock */
	public function compressImage($source, $destination, $quality = 40)
	{
		// Get image info 
		$imgInfo = getimagesize($source);
		if ($imgInfo === false || !isset($imgInfo['mime'])) {
			return false;
		}
		$mime = $imgInfo['mime'];

		// Create a new image from file 
		switch ($mime) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg($source);
				break;
			case 'image/png':
				$image = imagecreatefrompng($source);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($source);
				break;
			default:
				$image = imagecreatefromjpeg($source);
		}

		// Save image 
		imagejpeg($image, $destination, $quality);

		// Return compressed image 
		return $destination;
	}
	public	function convert_filesize($bytes, $decimals = 2)
	{
		$size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}


	function query($q, $debug = 0)
	{
		if ($debug == 1) {
			echo $q;
			die;
		}
		if ($q != "") {
			$result = mysqli_query($this->myconn, $q);
			if ($result) {
				return $result;
			} else {
				echo mysqli_error($this->myconn);
				return false;
			}
		} else {
			return false;
		}
	}

	function addStatusTimelineEntry($inquiry_id, $new_status = 0, $uid = '')
	{
		// echo $new_status;exit;
		/* Folloup Status */
		$status_array1 = array("0" => "Generate", "1" => "In Followup", "2" => "Positive", "3" => "Buy Later", "4" => "Hot", "5" => "Cold", "6" => "Warm", "-1" => "My Work", "-2" => "Cancel", "11" => "Lost");
		/* Folloup Status */
		$last_status = $this->rp_getValue("no_order_inquiry_status_timeline", "new_status", "isDelete=0 AND isActive=1 AND inquiry_id = '" . $inquiry_id . "' ORDER BY id DESC LIMIT 1 ", 0);
		$date_time = $this->rp_getValue("no_order_inquiry_status_timeline", "date_time", "isDelete=0 AND isActive=1 AND inquiry_id = '" . $inquiry_id . "' ORDER BY id DESC LIMIT 1 ", 0);
		$inq_status = $this->rp_getValue("no_order_inquiry", "inq_status", "isDelete=0 AND isActive=1 AND id = '" . $inquiry_id . "'", 0);
		$last_status = ($last_status) ? $last_status : 0;
		$new_status = ($new_status) ? $new_status : 0;
		$sess_uid = ($_SESSION[SITE_SESS . 'REFERANCE_ID']) ? $_SESSION[SITE_SESS . 'REFERANCE_ID'] : '';
		if ($uid) {
			$user_name = $this->rp_getValue("sales_executive", "name", "isDelete=0 AND isActive=1 AND id = '" . $uid . "' ");
			$username = $this->rp_getValue("sales_executive", "username", "isDelete=0 AND isActive=1 AND id = '" . $uid . "' ");
		} else {
			$user_name = $this->rp_getValue("dealer_distributor_network", "name", "isDelete=0 AND id = '" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
			$username = $this->rp_getValue("dealer_distributor_network", "username", "isDelete=0 AND id = '" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ");
		}

		if (empty($user_name)) {
			$userName = "Admin";
		} else {
			$userName = $user_name . " - " . $username;
		}
		$uid = ($uid) ? $uid : $sess_uid;
		$totalStatusTl = $this->rp_getTotalRecord("no_order_inquiry_status_timeline", "isDelete=0 AND isActive=1 AND inquiry_id = '" . $inquiry_id . "'");
		if ($totalStatusTl > 0) {
			$remark = $status_array1[$last_status] . " Status Has Changed To " . $status_array1[$new_status] . " Status " . "Date " . date("d-m-Y h:i:s A", strtotime($date_time)) . " To " . " Date " . date("d-m-Y h:i:s A") . " By " . $userName;
		} else {
			$remark = $status_array1[$last_status] . " Status Added Date " . date("d-m-Y h:i:s A") . " By " . $userName;
		}
		$rows = array("past_status", "new_status", "inquiry_id", "date_time", "user_id", "inq_status", "remark");
		$values = array($last_status, $new_status, $inquiry_id, date("Y-m-d H:i:s"), $uid, $inq_status, addslashes($remark));
		$this->rp_insert("no_order_inquiry_status_timeline", $values, $rows, 0);
	}
	//"key" : "9a4dddf1a8a24bf699f7cb5b6506c728",
	public function send_whatsapp($to, $msg)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://whatsapp.hakimisolution.com/api/v1/sendMessage',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>
			'{
		          "key" : "849f3acc36384a5992964acbd1e9464f",
		          "to" : "' . $to . '",
		          "message" : "' . $msg . '",
		          "IsUrgent" : true,
		         }',
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		// echo $response; exit;
		return $response;
	}

	public function logMysqlError($message, $sql = true)
	{

		if ($sql) {
			$logMessage = sprintf(
				"[%s] MySQL Error: %s\n",
				date('Y-m-d H:i:s'),
				$message . "\n\n"
			);
		} else {
			$logMessage = sprintf(
				"[%s] Request Source: %s\n",
				date('Y-m-d H:i:s'),
				$message . "\n\n"
			);
		}
		error_log($logMessage, 3, __DIR__ . '/../log/php_requests_' . date("d_M_Y") . '.log');

		$data = json_encode(array("GET" => $_GET, "POST" => $_POST, "BODY_DATA" => file_get_contents('php://input'), "FILES_DATA" => $_FILES));
		// Log request information
		$logMessage = sprintf(
			"[%s] %s %s %s",
			date('Y-m-d H:i:s'),
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI'] . "\n\n" . $data . "\n\n"
		);

		// Append request information to a log file
		error_log($logMessage . PHP_EOL, 3, __DIR__ . '/../log/php_requests_' . date("d_M_Y") . '.log');
	}

	function toUpperCaseAssocArray($arr)
	{
		if (!is_array($arr)) {
			return $arr;
		}
		$hasMb = function_exists('mb_strtoupper');
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$arr[$key] = $this->toUpperCaseAssocArray($val);
			} elseif (is_string($val)) {
				if (strpos($val, 'http://') !== 0 && strpos($val, 'https://') !== 0 && strpos($val, 'ftp://') !== 0) {
					$arr[$key] = $hasMb ? mb_strtoupper($val, 'UTF-8') : strtoupper($val);
				}
			}
		}
		return $arr;
	}

}
include("admin.class.php");
?>