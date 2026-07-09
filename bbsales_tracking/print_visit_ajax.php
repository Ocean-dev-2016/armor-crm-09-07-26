<?php
$page_id = 577;
$page_slug = 'visit_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "visit";
$ctable1 	= "User";

$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {

	// $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($sales_id)
	// {		
	// 	while($K=mysqli_fetch_assoc($sales_id))
	// 	{
	// 		$USER_IDS[]=$K['id'];
	// 	}
	// 	$USER_IDS=implode(",",$USER_IDS);
	// 	$ctable_where .="user_id IN (".$USER_IDS.") ";
	// }
	// else
	// {
	// 	$ctable_where .="user_id IN (0) ";
	// }

	$customer_id = $db->rp_getData("executive", "*", "cname LIKE '%" . $_REQUEST['searchName'] . "%' OR phone LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
	if ($customer_id) {
		while ($K1 = mysqli_fetch_assoc($customer_id)) {
			$CUSTOMER_IDS[] = $K1['id'];
		}
		$CUSTOMER_IDS = implode(",", $CUSTOMER_IDS);
		$ctable_where .= " customer_id IN (" . $CUSTOMER_IDS . ") ";
	} else {
		$ctable_where .= " customer_id IN (0)  ";
	}
	$inquiry_id = $db->rp_getData("no_order_inquiry", "*", "person_name LIKE '%" . $_REQUEST['searchName'] . "%' OR mobile_number LIKE '%" . $_REQUEST['searchName'] . "%' OR company_name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0", "", 0);
	if ($inquiry_id) {
		while ($D1 = mysqli_fetch_assoc($inquiry_id)) {
			$INQID[] = $D1['id'];
		}
		$INQID = implode(",", $INQID);
		$ctable_where .= " AND  inquiry_id IN (" . $INQID . ") ";
	} else {
		$ctable_where .= "  AND  inquiry_id IN (0)  ";
	}
	$ctable_where .= " AND ";
	/*$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";*/
}
//service_executive user not show condition start --//
// $SEID=array();
// $sales_type_r=$db->rp_getData("sales_executive","id,username","type='service_executive'","",0);
// while($sales_type_d = mysql_fetch_array($sales_type_r))
// {
// 	$SEID[] = $sales_type_d['id'];
// }
// $SEID=implode(",",$SEID);
// $ctable_where .="   user_id NOT IN ('".$SEID."') AND ";
//service_executive user not show condition end	--// 

$ctable_where .= " isDelete=0";

// print_r($_REQUEST["sales_executive"]);exit;
if (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != NULL && $_REQUEST['sales_executive'] != null && $_REQUEST['sales_executive'] != "NULL" && $_REQUEST['sales_executive'] != "null" && $_REQUEST['sales_executive'] != UNDEFINED && $_REQUEST['sales_executive'] != undefined && $_REQUEST['sales_executive'] != "UNDEFINED" && $_REQUEST['sales_executive'] != "undefined") {
	$ctable_where .= " AND user_id IN (" . $_REQUEST["sales_executive"] . ") ";
	$sid = $_REQUEST["sales_executive"];
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id'] != null && $_REQUEST['customer_id'] != "NULL" && $_REQUEST['customer_id'] != "null" && $_REQUEST['customer_id'] != UNDEFINED && $_REQUEST['customer_id'] != undefined && $_REQUEST['customer_id'] != "UNDEFINED" && $_REQUEST['customer_id'] != "undefined") {
	$ctable_where .= " AND customer_id IN (" . $_REQUEST["customer_id"] . ") ";
	$cid = $_REQUEST["customer_id"];
}
if (isset($_REQUEST["visit_type"]) && $_REQUEST["visit_type"] != "" && $_REQUEST["visit_type"] != undefined) {
	$ctable_where .= " AND visit_type='" . $_REQUEST["visit_type"] . "'";
}
if (isset($_REQUEST["company_id"]) && $_REQUEST["company_id"] != "" && $_REQUEST["company_id"] != undefined) {
	$ctable_where .= " AND type_of_company='" . $_REQUEST["company_id"] . "' ";
	$company_ids = $_REQUEST["company_id"];
}

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode($_REQUEST['df']);

	$date_filter_query_ex = explode(" to ", $date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(created_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}


$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC", 0);
?>
<style>
	.mainDiv,
	table {
		height: auto;
		width: 100%;
		font-family: Calibri, sans-serif;
		font-style: normal;
		font-weight: 400;
		padding: 0;
		text-decoration: none;
		font-size: 10pt;
		margin: auto;
		padding: auto;
	}

	tr {
		height: 30px;
	}

	table,
	td,
	th {
		border-collapse: collapse;
		border: 1px solid #000;
	}

	td,
	th {
		padding: 5px;
	}

	th {
		border: 1px solid #595959;
		background: #f0e6cc;
	}

	.text-right {
		text-align: right;
	}

	.center {
		text-align: center;
	}

	.space {
		padding: 10px;
	}

	.no-border {
		border-bottom: 1px solid #fff;
	}
</style>
<table id="datatable_1" class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th colspan="23" class="center">
				<h2>Visit Report <?= date("d-m-Y h:i a"); ?> Printed By : <?= $_SESSION[SITE_SESS . 'SESS_NAME']; ?></h2>
			</th>
		</tr>
		<tr>
			<th>No.</th>
			<th>Sales Person Name</th>
			<th>Company</th>
			<th>Company Name</th>
			<th>Person Name</th>
			<th>Client Code</th>
			<th>Customer Mobile No.</th>
			<th>Date and Time</th>
			<th>Visit Purpose</th>
			<th>Name</th>
			<th>Mobile no</th>
			<th>Visit Start <br /> Remark</th>
			<th>Visit Start <br /> Address</th>
			<th>Visit Start <br /> Image</th>
			<th>Visit Start <br /> Time</th>
			<th>Visit Stop <br /> Remark</th>
			<th>Visit Stop <br /> Address</th>
			<th>Visit Stop <br /> Image</th>
			<th>Visit Stop <br /> Time</th>
			<th>Purchasing From</th>
			<th>Total Time</th>
			<th>Visit Type</th>
			<th>Visit Stop<br />Flag</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (mysqli_num_rows($ctable_r) > 0) {
			$count = 0;

			while ($ctable_d = mysqli_fetch_array($ctable_r)) {
				$datetime1 = new DateTime($ctable_d['stop_date_time']);
				$datetime2 = new DateTime($ctable_d['start_date_time']);
				$interval = $datetime1->diff($datetime2);
				$elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
				//print_r($ctable_d);
		?>
				<tr>
					<td><?php echo ++$count; ?></td>
					<td><span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>"><?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'") ?></span></td>
					<td>
						<?php

						echo $db->rp_getValue("company_master", "name", "id='" . $ctable_d['type_of_company'] . "'", 0);
						?>
					</td>
					<?php
					if ($ctable_d['visit_stop_flag'] == 4) {

						echo "<td>" . $ctable_d['firm_name'] . " - " . $ctable_d['client_name'] . "</td>";
					} else {
						if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
					?>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>"><?php echo $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $ctable_d['inquiry_id'] . "'", 0) . " - " . $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['inquiry_id'] . "'", 0) ?></span>
							</td>
						<?php
						} else {
						?>
							<td>
								<span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
									<?php
									$company_name = $db->rp_getValue("executive", "company_name", "id='" . $ctable_d['customer_id'] . "'");
									$customer_flag = $db->rp_getValue("executive", "customer_flag", "id='" . $ctable_d['customer_id'] . "'");
									$customer_flag_text = "";
									if ($customer_flag == 1) {
										$customer_flag_text = " - P";
									} else if ($customer_flag == 0) {
										$customer_flag_text = " - C";
									}
									echo $company_name . $customer_flag_text;
									?>

								</span>
							</td>
					<?php
						}
					}
					?>
					<td>
						<?php
						if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
						} else {
							echo $db->rp_getValue("executive", "cname", "id='" . $ctable_d['customer_id'] . "'");
						}
						?>
					</td>
					<td>
						<?php
						if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
						} else {
							echo $db->rp_getValue("executive", "client_code", "id='" . $ctable_d['customer_id'] . "'");
						}
						?>
					</td>
					<?php
					if ($ctable_d['visit_stop_flag'] == 4) {

						echo "<td>" . $ctable_d['contact_number'] . "</td>";
					} else {
						if ($ctable_d['customer_id'] == 0 && $ctable_d['inquiry_id'] != "0") {
					?>
							<td><?php echo $db->rp_getValue("no_order_inquiry", "mobile_number", "id='" . $ctable_d['inquiry_id'] . "'") ?></td>
						<?php
						} else {
						?>
							<td><?php echo $db->rp_getValue("executive", "phone", "id='" . $ctable_d['customer_id'] . "'") ?></td>
					<?php
						}
					}
					?>
					<td><?php echo date("d-m-Y H:i:s", strtotime($ctable_d['created_date'])); ?></td>
					<td><?php echo  $db->rp_getValue("purpose_master", "name", "isDelete=0 AND id=" . $ctable_d['purpose_id'], 0); ?></td>
					<td><?php echo  $ctable_d['name']; ?></td>
					<td><?php echo  $ctable_d['mobile_no']; ?></td>
					<td><?php echo stripslashes($ctable_d['remark']); ?></td>
					<!-- <td> -->
					<!-- Trigger the modal with a button -->
					<!-- <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap">
								<img src="<?= SITEURL ?>resource/map.png" style="height: 80px;">
							</a> -->
					<!-- </td> -->
					<td><?php echo $ctable_d['app_address']; ?></td>
					<td>
						<?php
						$img = explode(",", $ctable_d['image_path']);
						$imgpath = array();
						for ($i = 0; $i < sizeof($img); $i++) {
							$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
						}
						for ($i = 0; $i < sizeof($imgpath); $i++) {
							if ($i == 0) {
						?>
								<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 40px; width:40px;"></a>
							<?php
							} else {
							?>
								<div class="hidden">
									<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 40px; width:40px;"></a>
								</div>
						<?php
							}
						}
						?>
					</td>
					<td><?php if ($ctable_d['start_date_time'] != "0000-00-00 00:00:00") {
							echo date('d-m-Y h:i A', strtotime($ctable_d['start_date_time']));
						} else {
							echo "";
						} ?></td>
					<td><?php echo stripslashes($ctable_d['stop_remark']); ?></td>
					<!-- <td> -->
					<!-- Trigger the modal with a button -->
					<?php
					//if($ctable_d['stop_longitude']!=""){
					?>
					<!-- <a class="mapbtn1" data-app_address="<?php echo stripslashes($ctable_d['stop_app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['stop_latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['stop_longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap1">
								<img src="<?= SITEURL ?>resource/map.png" style="height: 80px;"> -->
					<!-- </a> -->
					<?php
					//}
					?>

					<!-- </td> -->
					<td><?php echo $ctable_d['stop_app_address']; ?></td>
					<td>
						<?php
						if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
							$img = explode(",", $ctable_d['stop_image_path']);
							$imgpath = array();
							for ($i = 0; $i < sizeof($img); $i++) {
								$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
							}
							for ($i = 0; $i < sizeof($imgpath); $i++) {
								if ($i == 0) {
						?>
									<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 40px; width:40px;"></a>
								<?php
								} else {
								?>
									<div class="hidden">
										<a href="<?= $imgpath[$i] ?>" data-lightbox="visit<?= $count ?>" data-title="visit <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 40px; width:40px;"></a>
									</div>
						<?php
								}
							}
						}
						?>
					</td>
					<td><?php if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
							echo date('d-m-Y h:i A', strtotime($ctable_d['stop_date_time']));
						} else {
							echo "";
						} ?></td>
					<td><?php echo $ctable_d['product_name']; ?></td>
					<td>
						<?php
						if ($ctable_d['stop_date_time'] != "0000-00-00 00:00:00") {
							echo $elapsed;
						}
						?>
					</td>
					<td>
						<?php
						if ($ctable_d['visit_type'] == "1") {
							echo "Existing Customer";
						} else if ($ctable_d['visit_type'] == "3") {
							echo "Inquiry";
						} else if ($ctable_d['visit_type'] == "4") {
							echo "New Customer";
						} else {
							echo " ";
						}
						?>
					</td>
					<?php
					if ($ctable_d['visit_stop_flag'] == "1") {
						$order_no = $db->rp_getValue("orders", "order_no", "customer_id='" . $ctable_d['customer_id'] . "' AND DATE(created_date)='" . date('Y-m-d', strtotime($ctable_d['stop_date_time'])) . "' AND sales_id=" . $ctable_d['user_id']);
					}
					if ($order_no == "" && $ctable_d['visit_stop_flag'] == "1") {
						$style = "style='background-color: #f1acac;'";
					}
					?>
					<td <?php if ($ctable_d['visit_stop_flag'] == "1" && $order_no == "") {
							echo $style;
						} ?>> <?php
								if ($ctable_d['visit_stop_flag'] == "1") {
									echo "Create Order<br/>" . $order_no;
								} else if ($ctable_d['visit_stop_flag'] == "2") {
									echo "Stop Visit With Edit Inquiry";
								}
								?>
					</td>
				</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>