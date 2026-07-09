<?php
$page_id = 581;
$page_slug = 'manage_complain';

/*
 * Author: Ravi Patel
 */

include("connect.php");
// print_r($_REQUEST);exit;

$ctable = "complain";
$ctable_where = "";
$status_id = "";
$status_array = array(
    "0" => "Generate",
    "1" => "In Progress",
    "2" => "Complete",
    "-1" => "Reject",
    "-2" => "Not Done",
    "-3" => "Cancel"
);

// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $ctable_where .= " (complain_no like '%" . $db->clean($_REQUEST['searchName']) . "%') AND ";
}

$ctable_where .= " isDelete=0 ";

$item_per_page = ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;

if (isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    } //in case of an invalid page number
} else {
    $page_number = 1; //if there's no page number, set it to 1
}

// print_r($_REQUEST["sales_executive"]);exit;
if (isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"] != "" && $_REQUEST["sales_executive"] != undefined) {
    $ctable_where .= " AND user_id='" . $_REQUEST["sales_executive"] . "'";
    $sid = $_REQUEST["sales_executive"];
}

// Code related to 'state' and 'city' filtering is commented out

if (isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"] != "" && $_REQUEST["customer_id"] != undefined) {
    $ctable_where .= " AND customer_id='" . $_REQUEST["customer_id"] . "'";
    $cid = $_REQUEST["customer_id"];
}

if (isset($_REQUEST["company_type"]) && $_REQUEST["company_type"] != "" && $_REQUEST["company_type"] != undefined) {
    $ctable_where .= " AND type_of_company='" . $_REQUEST["company_type"] . "'";
    $company_type = $_REQUEST["company_type"];
}

if (isset($_REQUEST['status_id']) && $_REQUEST['status_id'] != "" && $_REQUEST['status_id'] != 'undefined') {
    $ctable_where .= " AND status='" . $_REQUEST['status_id'] . "' ";
    $status_id = $_REQUEST['status_id'];
}

if (isset($_REQUEST['df']) && $_REQUEST['df'] != "") {
    $date_filter_query = urldecode($_REQUEST['df']);

    $date_filter_query_ex = explode(" to ", $date_filter_query);

    $ctable_where .= " AND (DATE(complain_date) >= '" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(complain_date) <= '" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if (isset($_REQUEST['complain_month']) && $_REQUEST['complain_month'] != "" && $_REQUEST['complain_month'] != NULL) {
    $ctable_where .= " AND MONTH(complain_date) = '" . $_REQUEST['complain_month'] . "'";
}

if (isset($_REQUEST['complain_year']) && $_REQUEST['complain_year'] != "" && $_REQUEST['complain_year'] != NULL) {
    $ctable_where .= " AND YEAR(complain_date) = '" . $_REQUEST['complain_year'] . "'";
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate']!="01-01-1970") {
    $ctable_where .= " AND complain_date >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate']!="01-01-1970") {
    $ctable_where .= " AND complain_date <= '" . $_REQUEST['fromdate'] . "' ";
}

if ($_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 0) {
    if ($rights['personal_flag'] == 1) {
        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
        $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
    } else {
        if ($rights['chain_vise_flag'] == 1) {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
            $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='".$check_id."'", 0);
            
            if ($get_sales_type == "sales_manager") {
                $sales_executive_type = "Regional Sales Manager";
                $key = "sm_id";
                $WhereCondition .= ' '.$key.'='.$check_id;
            } else if ($get_sales_type == "area_sales_manager") {
                $sales_executive_type = "National Sales Manager";
                $key = "asm_id";
                $WhereCondition .= ' '.$key.'='.$check_id;
            } else if ($get_sales_type == "sales_officer") {
                $sales_executive_type = "Area Sales Manager";
                $key = "so_id";
                $WhereCondition .= ' '.$key.'='.$check_id;
            } else if ($get_sales_type == "sales_executive") {
                $sales_executive_type = "Sales Officer";
                $key = "se_id";
                $WhereCondition .= ' '.$key.'='.$check_id;
            } else {
                $WhereCondition .= ' type = "service_engineer"';
            }

            $data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);
            $SALEID1 = array();

            if ($data) {
                while ($data_d = mysqli_fetch_assoc($data)) {
                    $SALEID1[] = $data_d['id'];
                }
            }

            if (!empty($SALEID1)) {
                $SALEID1 = implode(",", $SALEID1);
                $ctable_where .= " AND (complain_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")) ";
            } else {
                $ctable_where .= " AND (complain_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";
            }
        } else {
            // Code block when chain_vise_flag is not 1
        }
    }
}
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
// }

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

// SELECT * FROM complain WHERE isDelete=0 AND user_id='4' ORDER BY id DESC limit 0, 500

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<!-- <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script>  -->

<style type="text/css">
.table-scrollable {
		width: auto;
		height: 450px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
	}

	.fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<form action="" name="frm" id="frm" method="post">
		 <span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>
	<div class="table-scrollable">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
	        <thead class="fix-th">
	        	<tr>
	        		<th></th>
	                <th></th>
	                <th></th>
	                <th>
	                	<div class="input-group">
							<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
							<span class="input-group-addon datetimerange-picker-btn">
							<i class="fa fa-calendar"></i>
							</span>			
						  <!--   <span class="input-group-btn">
								<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
							</span> -->
						</div>
						<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
	                </th>
	                <th>
	                	<?php
						if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
	                	{ 
						?>
		                	<select class="form-control input-small" name="sales_executive" id="sales_executive">
		                		<option value="">Select Sales Person</option>
		                		<?php
		                			$D_r = $db->rp_getData("sales_executive","id,name","isDelete=0 AND isActive=1","",0);
		                			while ($D = mysqli_fetch_assoc($D_r))
		                			{
		                				?>
		                				<option value="<?=$D['id']?>" <?=($sid == $D['id'])?"selected":"";?>><?=$D['name']?></option>
		                				<?php
		                			}
		                		?>
		                	</select>
		                <?php 
		            	}
		            	?>
	                </th>
	                <th>
	                	<select class="form-control input-small" name="company_type" id="company_type">
	                		<option value="">Select Company type</option>
	                		<?php
	                			$company_r = $db->rp_getData("company_master","*","isDelete = 0","",0);
	                			while ($company_d = mysqli_fetch_assoc($company_r))
	                			{
	                				?>
	                				<option value="<?=$company_d['id']?>" <?=($company_type == $company_d['id'])?"selected":"";?>><?=$company_d['name']?></option>
	                				<?php
	                			}
	                		?>
	                	</select>
	                </th>
	                <th>
	                	<select class="form-control input-small" name="customer_id" id="customer_id">
	                		<option value="">Select Customer</option>
	                		<?php
	                			$E_r = $db->rp_getData("executive","*","1=1 GROUP By cname","cname ASC",0);
	                			while ($E = mysqli_fetch_assoc($E_r))
	                			{
	                				?>
	                				<option value="<?=$E['id']?>" <?=($cid == $E['id'])?"selected":"";?>><?=$E['cname']?></option>
	                				<?php
	                			}
	                		?>
	                	</select>
	                </th>
	                <th></th>
	                <th></th>
	                <th></th>
	                <!-- <th></th> -->
	                <th></th>
	              <!--   <th></th>  -->
	                <!-- <th>
						<select class="form-control status" style="width:118px;" name="state" id="state" onChange="filter_state(this.value);" autofocus>
							<option value="">Select State</option>
							<?php
							$state_r = $db->rp_getData("state", "*", 0);
							if (mysqli_num_rows($state_r) > 0) {
								while ($state_d = mysqli_fetch_array($state_r)) {
							?>
									<option value="<?php echo $state_d['name']; ?>" <?= ($state == $state_d['name']) ? "selected" : ""; ?>><?php echo $state_d['name']; ?></option>
							<?php
								}
							}
							?>
						</select>
					</th>
	                <th>
						<select class="form-control status" style="width:118px;" name="main_city" id="main_city" onChange="filter_city(this.value);" autofocus>
							<option selected value="<?= $_REQUEST['main_city'] ?>">Select City</option>
							 <?php
								if (isset($_REQUEST['main_city']) && $_REQUEST['main_city'] != "" && $_REQUEST['main_city'] != NULL) {
									
								}
							?>
						</select>
					</th>
	                <th></th> -->
	                <th>  
	                	<!-- <select class="form-control" id="status_id" name="status_id" onchange="getStatus(this.value)">
	                    	<option value="">Select Status</option>
	                    	<option <?= ($status_id==0 && $status_id!="")?"selected":""; ?> value="0">Generate</option>
	                    	<option <?= ($status_id==1)?"selected":""; ?> value="1">In Progress</option> 
	                    	<option <?= ($status_id==2)?"selected":""; ?> value="2">Complete</option>
	                    	<option <?= ($status_id==-1)?"selected":""; ?> value="-1">Reject</option>
	                    	<option <?= ($status_id==-2)?"selected":""; ?> value="-2">Not Done</option>
		                </select> -->
	                </th>
	                <th></th>
	                <!-- <th></th> -->
	                <!-- <th></th> -->
	            </tr>
	        	<tr>
	        		<th class="fix-th1"></th>
	                <th class="fix-th1">No.</th>
	                <th class="fix-th1">Complain No.</th>
	                <th class="fix-th1">Complain Date</th>
	                <th class="fix-th1">Sales Person Name</th>
	                <th class="fix-th1">Company Type</th>
	                <th class="fix-th1">Customer Name</th>
	                <!-- <th>Source of complain</th> -->
	                <th class="fix-th1">Complain Category</th>
	                <th class="fix-th1">Complain Sub Category</th>
	                <th class="fix-th1">Description</th>
					<!-- <th>Location Map</th> -->
					<th class="fix-th1">Address</th>
					<!-- <th>State</th> -->
					<!-- <th>City</th> -->
					<!-- <th>Image</th> -->
					<th class="fix-th1">Status</th>	
					<th class="fix-th1">Compalain Assign To</th>	
					<!-- <th>Entry Type</th> -->
						
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        if(mysqli_num_rows($ctable_r)>0)
	        {
	            $count = 0;
	            while($ctable_d = mysqli_fetch_array($ctable_r))
	            {
		            $complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
		            $ENTRY_FLAG = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
		            ?>
		            <tr>
		            	<td>
		            		<div class="btn-group">				
								<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i>
								</button>
								<ul role="menu" class="dropdown-menu">
									<?php
										$file_path = "complain_viewer.php?id=".$ctable_d['id'] . "";
							  	    ?>
											<li>
												<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
													<span class="text-primary">
													<i class="fa fa-file-pdf-o"></i>
													Complain View</span>
												</a>
											</li>
											<?php
											if($rights['update_flag']==1)
											{
											?>
											<li>
												<a href="complain_crud.php?mode=edit&id=<?= $ctable_d['id']?>" title="Edit">
													<span class="text-success">
														<i class="fa fa-pencil"></i>
														&nbsp;Edit
													</span>
												</a>
											</li>
											<?php
											}
											?>
								<!-- 	<li>

										<a title="View">
											<span class="text-primary">
												<i class="fa fa-print"></i>
												&nbsp;Complain View
											</span>
										</a>
									</li> -->
								</ul>
							</div>
		            	</td>
		                <td><?php echo ++$count; ?></td>
						<td>
						    <?php echo "#" . stripslashes($ctable_d['complain_no']); ?>
						    <br>
						    <a href="service_crud.php?complain_id=<?= $ctable_d['id'] ?>" class="btn btn-info">Service Form</a>
						</td>
						<td>
						    <?php 
						        if ($ctable_d['complain_date'] == "1970-01-01" || $ctable_d['complain_date'] == "0000-00-00") {
						            echo "";
						        } else {
						            echo date("d-m-Y", strtotime($ctable_d['complain_date']));
						        }
						    ?>
						</td>
						<td>
						    <span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
						        <?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d['user_id'] . "'") ?>
						    </span>
						</td>
						<td>
						    <?= $db->rp_getValue("company_master", "name", "id = '".$ctable_d['type_of_company']."'") ?>
						</td>
						<td>
						    <span class="<?php echo ($ctable_d['isActive'] == 0) ? "text-danger" : "text-success"; ?>">
						        <?php echo $db->rp_getValue("executive", "cname", "id='" . $ctable_d['customer_id'] . "'") ?>
						    </span>
						    <span>
						        <br/>
						        <?php echo $db->rp_getValue("executive", "phone", "id='" . $ctable_d['customer_id'] . "'") ?>
						    </span>
						</td>

		                <!-- <td><?php echo stripslashes($complain_type_array[$ctable_d['complain_type']]); ?></td> -->
						<td><?php echo  $db->rp_getValue("complain_category", "name", "id='" . $ctable_d['complain_cat_id'] . "'"); ?></td>
						<td><?php echo  $db->rp_getValue("complain_sub_category", "name", "id='" . $ctable_d['complain_subcat_id'] . "'"); ?></td>
						<td><?php echo stripslashes($ctable_d['remark']); ?></td>
						<!-- <td> -->
						    <!-- Trigger the modal with a button -->
						    <!-- <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap">
						        <img src="<?= SITEURL ?>resource/map.png" style="height: 80px;">
						    </a> -->
						<!-- </td> -->
						<td>
						    <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?= date("d M H:i", strtotime($ctable_d['created_date'])); ?>" data-salesexename="<?= $db->rp_getValue("sales_executive", "name", "id='" . $ctable_d["user_id"] . "'", 0); ?>" data-toggle="modal" data-target="#OpenMap">
						        <?php echo $ctable_d['app_address']; ?>
						    </a>
						</td>
						<!-- <td><?php echo $ctable_d['state']; ?></td>
						<td><?php echo $ctable_d['city']; ?></td> -->

						<!-- <td>
						    <?php 
						        if ($ctable_d['image_path'] != "") {
						            $img = explode(",", $ctable_d['image_path']);
						            $imgpath = array();
						            for ($i = 0; $i < sizeof($img); $i++) { 
						                $imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $ctable_d["id"] . "' AND id='" . $img[$i] . "'", 0);
						            }
						            for ($i = 0; $i < sizeof($imgpath); $i++) {
						                if ($i == 0) {
						                    ?>
						                    <a href="<?= $imgpath[$i] ?>" data-lightbox="complain<?= $count ?>" data-title="complain <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
						                    <?php 
						                } else {
						                    ?>
						                    <div class="hidden">
						                        <a href="<?= $imgpath[$i] ?>" data-lightbox="complain<?= $count ?>" data-title="complain <?= $ctable_d['id'] ?>"><img src="<?= $imgpath[$i] ?>" style="height: 80px;"></a>
						                    </div>
						                    <?php
						                }
						            }
						        } else {
						            $img = $ctable_d['image_path'] = DEFAULTIMG;
						            ?>
						            <a href="<?= $img ?>" data-lightbox="complain<?= $count ?>" data-title="complain <?= $ctable_d['id'] ?>"><img src="<?= $img ?>" style="height: 80px;"></a>
						            <?php
						        }
						    ?>
						</td> -->
						<td>
						    <?= $status_array[$ctable_d['status']]; ?>
						    <!-- <select class="form-control" disabled="disabled" id="complain_status<?= $ctable_d['id']?>" style="width:200px;text-align:center;margin: auto;">
						        <option value="">Select Status</option>
						        <option <?= ($ctable_d['status']==0)?"selected":""; ?> value="0">Generate</option>
						        <option <?= ($ctable_d['status']==1)?"selected":""; ?> value="1">In Progress</option>
						        <option <?= ($ctable_d['status']==2)?"selected":""; ?> value="2">Complete</option>
						        <option <?= ($ctable_d['status']==-1)?"selected":""; ?> value="-1">Reject</option>
						        <option <?= ($ctable_d['status']==-2)?"selected":""; ?> value="-2">Not Done</option>
						    </select> -->
						    <!-- <a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
						    <span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
						        <a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
						        <a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
						    </span> -->
						</td>
						<td>
						    <?php
						        echo $db->rp_getValue("sales_executive", "name", "id='".$ctable_d['complain_assign_to']."'");
						    ?>
						    <!-- <select class="form-control" disabled="disabled" id="complain_assign<?= $ctable_d['id']?>" style="width:200px;text-align:center;margin: auto;">
						        <option value="">Select Status</option>
						        <?php
						        $sales_id_r = $db->rp_getValue("sales_executive", "*", "isDelete=0 AND isActive=1", "", 0);
						        if ($sales_id_r) {
						            while ($sales_id_d = mysqli_fetch_assoc($sales_id_r)) {
						                ?>
						                <option value="<?= $sales_id_d['id'] ?>" <?= ($ctable_d['complain_assign_to'] == $sales_id_d['id']) ? "selected" : ""; ?> ><?= $sales_id_d['name'] ?></option>
						                <?php
						            }
						        }  
						        ?>
						    </select>
						    <a href="javascript:void(0);" id="editcomplain_<?php echo $ctable_d['id']; ?>" onClick="editCompalain('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
						    <span id="editcomplain2_<?php echo $ctable_d['id']; ?>" style="display:none;">
						        <a href="javascript:void(0);" id="savecomplain<?php echo $ctable_d['id']; ?>" onClick="saveCompalain('<?php echo $ctable_d['id']; ?>')">Save</a> |
						        <a href="javascript:void(0);" id="cancelcomplain<?php echo $ctable_d['id']; ?>" onClick="cancelCompalain('<?php echo $ctable_d['id']; ?>')">Cancel</a>
						    </span> -->
						</td>
		                <!-- <td><?php echo $ENTRY_FLAG[$ctable_d['entry_flag']]; ?></td> -->
		                <!-- <td><?php //echo $ENTRY_FLAG[$ctable_d['update_entry_flag']]; ?></td> -->
		                <!-- <td><a href="service_crud.php?complain_id=<?=$ctable_d['id']?>" class="btn btn-info">Service Form</a></td> -->
					</tr>
		        	<?php
	            }
	        }
	        else
	        {
	        	?>
	        	<tr><td align="center" colspan="10"><h2><strong>No Data Available</strong></h2></td></tr>
	        	<?php
	        }
	        ?>
	        </tbody>
	    </table>
	</div>
    <!-- Modal -->
	<div id="OpenMap" class="modal fade" role="dialog">
	  <div class="modal-dialog" style="width: 970px;">

	    <!-- Modal content-->
	    <div class="modal-content" >
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">complain</h4>
	      </div>
	      <div class="modal-body">
	        <div id="map_canvas"></div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>
	</div>
	<!-- Modal -->
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>50</option>
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
												echo ' selected="selected"';
											}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
												echo ' selected="selected"';
											}  ?>>5000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>

<div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4">
</script>
<script type="text/javascript">
	$(".mapbtn").click(function(){
		var date = $(this).data("date");
		var salesexename = $(this).data("salesexename");
		var lat = $(this).data("lat");
		var lng = $(this).data("long");
		var app_address = $(this).data("app_address");
		$.ajax({
            url: "get_complain_map.php",
            data: {
                lat: lat,
                lng: lng,
                date: date,
                app_address: app_address,
                salesexename: salesexename,
            },
            beforeSend: function() {
                $("#map_canvas").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
            },
            success: function(result) {
                $("#map_canvas").html(result);
            }
        });
	});
</script>
<!-- zoom image js -->
<!-- <script src="js/zoom-jquery-1.4.4.min.js" type="text/javascript"></script> -->
<!-- <script type="text/javascript" src="http://code.jquery.com/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="zoom-image/fancybox/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="zoom-image/cloud-zoom/cloud-zoom.1.0.2.js"></script> -->
<!-- zoom image js -->
<script type="text/javascript">
	$(".filterBtn").on("click",function()
	{
		sales_executive = $("#sales_executive").val();
		customer_id = $("#customer_id").val();
		df1=$("#material_request_filter_input").val();
		df1 = encodeURI(df1)
		displayRecords(100,1);
	})
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	}});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 	$(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<script type="text/javascript">
	$("#sales_executive").select2();
	$("#state").select2();
	$("#city").select2();
	$("#main_city").select2();
	$("#customer_id").select2();
	$("#company_type").select2();
	$("#status_id").select2();

	function filter_state(state_id, city = "") {
		// alert(state_id+" "+ city);
		if (state_id != "") 
		{
			$("#main_city").select2('val',"");
			$("#city").select2('val',"");
		}
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'state_id=' + state_id + "&city=" + city,
			beforeSend: function() {
				 $("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				// $("#main_city").select2("destroy");
				// alert(data)
				$("#main_city").html(data);
				$("#main_city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}

	function filter_city(main_city, route = "") {
		// alert(main_city+" "+ route);
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'main_city=' + main_city + "&city=" + route,
			beforeSend: function() {
				 $("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				// $("#city").select2("destroy");
				$("#city").html(data);
				$("#city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}
</script>
<?php require_once 'disconnect.php';  ?>