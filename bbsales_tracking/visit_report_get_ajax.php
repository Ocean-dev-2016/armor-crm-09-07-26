<?php
$page_id=599;$page_slug='visit_report_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "visit";
$ctable1 	= "User";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

	$sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	if($sales_id)
	{		
		while($K=mysqli_fetch_assoc($sales_id))
		{
			$USER_IDS[]=$K['id'];
		}
		$USER_IDS=implode(",",$USER_IDS);
		$ctable_where .="user_id IN (".$USER_IDS.") ";
	}
	else
	{
		$ctable_where .="user_id IN (0) ";
	}

	$customer_id = $db->rp_getData("executive","*","cname LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	if($customer_id)
	{		
		while($K1=mysqli_fetch_assoc($customer_id))
		{
			$CUSTOMER_IDS[]=$K1['id'];
		}
		$CUSTOMER_IDS=implode(",",$CUSTOMER_IDS);
		$ctable_where .=" OR customer_id IN (".$CUSTOMER_IDS.") ";
	}
	else
	{
		$ctable_where .=" OR customer_id IN (0)  ";
	}
	$ctable_where .=" AND ";
	/*$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";*/
						$isFillter = true;
}

$ctable_where .= " isDelete=0";


//for class----//
if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL)
{
	$exe_id = array();
	$get_exe_id = $db->rp_getData("executive_map_area","executive_id","class_id='".$_REQUEST['class_id']."' AND isDelete=0");
	if($get_exe_id)
	{
		while($get_exe_id_d = mysqli_fetch_assoc($get_exe_id))
		{
			$exe_id[] = $get_exe_id_d['executive_id'];
		}
		$exe_id=implode(",",$exe_id);
		$ctable_where .=" AND customer_id IN (".$exe_id.") ";
	}
	else
	{
		$ctable_where .=" AND customer_id IN (0)  ";
	}
	$isFillter = true;
}
//for class----//

//for area----//

if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL)
{
	$exe_idS = array();
	$get_exe_area = $db->rp_getData("executive_map_area","executive_id","area_id='".$_REQUEST['area']."' AND isDelete=0","",0);
	if($get_exe_area)
	{
		while($get_exe_area_d = mysqli_fetch_assoc($get_exe_area))
		{
			$exe_idS[] = $get_exe_area_d['executive_id'];
		}
		$exe_idS=implode(",",$exe_idS);
		$ctable_where .=" AND customer_id IN (".$exe_idS.") ";
	}
	else
	{
		$ctable_where .=" AND customer_id IN (0)  ";
	}
	$isFillter = true;
			
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=NULL && $_REQUEST['sales_executive']!=null && $_REQUEST['sales_executive']!="NULL" && $_REQUEST['sales_executive']!="null" && $_REQUEST['sales_executive']!=UNDEFINED && $_REQUEST['sales_executive']!=undefined && $_REQUEST['sales_executive']!="UNDEFINED" && $_REQUEST['sales_executive']!="undefined")
{
	$ctable_where .= " AND user_id IN(".$_REQUEST["sales_executive"].")";
	$sid = $_REQUEST["sales_executive"];
	$isFillter = true;
}

// if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!=NULL && $_REQUEST['customer_id']!=null && $_REQUEST['customer_id']!="NULL" && $_REQUEST['customer_id']!="null" && $_REQUEST['customer_id']!=UNDEFINED && $_REQUEST['customer_id']!=undefined && $_REQUEST['customer_id']!="UNDEFINED" && $_REQUEST['customer_id']!="undefined")
// {
// 	$ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
// 	$cid = $_REQUEST["customer_id"];
// 	$isFillter = true;
// }

// if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
// 	$ctable_where .= " ";
// } else {
// 	$ctable_where .= " isDelete=0 AND status!=-1 AND created_by='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";
// 	$ctable_where .= " user_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
// }

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['personal_flag']==1)
	{
		$ctable_where .= "  AND user_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
	}
	else
	{
		if($rights['chain_vise_flag'] == 1)
	 	{

	 		$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

			    $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
			    if ($get_sales_type== "sales_manager") 
			    {
			        $sales_executive_type = "Regional Sales Manager";
			        $key="sm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }

			    else if ($get_sales_type == "area_sales_manager") 
			    {
			        $sales_executive_type = "National Sales Manager";//Business Development Manager
			        $key="asm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }

			    else if ($get_sales_type == "sales_officer") 
			    {
			        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
			        $key="so_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_executive") 
			    {
			        $sales_executive_type = "Sales Officer";
			        $key="se_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else
			    {
			    	$WhereCondition.=' type = "service_engineer"';
			    }

			    $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

			    $SALEID1=array();
				if($data)
				{
					while($data_d=mysqli_fetch_assoc($data))
					{
						$SALEID1[]=$data_d['id'];
					}
				}
				if(!empty($SALEID1))
				{
					$SALEID1=implode(",", $SALEID1);
					
						$ctable_where .= "  AND user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					
					
				}
				else
				{
						$ctable_where .= "  AND user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
				}

	 	}
	 	else
	 	{
	 			

	 	}

	}

}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	$isFillter = true;
}
if ($isFillter) 
{
	$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
	//break records into pages
	$total_pages = ceil($get_total_rows/$item_per_page);

	//get starting position to fetch the records
	$page_position = (($page_number-1) * $item_per_page);

	$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
}
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
	<div class="table-scrollable">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
	        <thead class="fix-th">
	            <tr>
	                <!-- <th>No.</th>
	                <th>Sales Person Name</th>
	                <th>Customer Name</th>
	                <th>Mobile No.</th>
	                <th>Date and Time</th>
	                <th>Current Business</th>
	                <th>Meeting Discussion</th> -->
					<!-- <th>Latitude</th>
					<th>Longitude</th> -->
					<!-- <th>Meeting Purpose</th>
					<th>Location Map</th>
					<th>Address</th>
					<th>Image</th> -->
					<!-- <th>Action</th>	 -->

					<!--  <th>No.</th>
	                <th>Sales Person Name</th>
	                <th>Customer Name</th>
	                <th>Mobile No.</th>
	                <th>Date and Time</th>
	                <th>Current <br/>Business</th>
	                <th>Meeting <br/> Discussion</th>
					<th>Meeting Purpose</th> -->
					<!-- <th>Visit  <br/> Location Map</th> -->
					<!-- <th>Visit  <br/> Address</th>
					<th>Visit  <br/> Image</th>
					<th>Visit  <br/> Time</th>
					<th>Report</th> -->

					<th class="fix-th1">No.</th>
	                <th class="fix-th1">Sales Person Name</th>
	                <th class="fix-th1">Company & Person Name</th>
	                <th class="fix-th1">Mobile No.</th>
	                <th class="fix-th1">Date and Time</th>
	                <th class="fix-th1">Visit Purpose</th>
	                <th class="fix-th1">Name</th>
					<th class="fix-th1">Visit Start <br/> Remark</th>
					<th class="fix-th1">Visit Start <br/> Location Map</th>
					<th class="fix-th1">Visit Start <br/> Address</th>
					<th class="fix-th1">Visit Start <br/> Image</th>
					<th class="fix-th1">Visit Start <br/> Time</th>
					<th class="fix-th1">Visit Stop <br/> Remark</th>
					<th class="fix-th1">Visit Stop <br/> Location Map</th>
					<th class="fix-th1">Visit Stop <br/> Address</th>
					<th class="fix-th1">Visit Stop <br/> Image</th>
					<th class="fix-th1">Visit Stop <br/> Time</th>
					<th class="fix-th1">Purchasing <br/>From</th>
					<th class="fix-th1">Total Time</th>
					<th class="fix-th1">Visit Type</th>
					<th class="fix-th1">Visit Stop<br/>Flag</th>




					<!-- <th>Visit Stop <br/> Location Map</th>
					<th>Visit Stop <br/> Address</th>
					<th>Visit Stop <br/> Image</th>
					<th>Visit Stop <br/> Time</th> -->
					<!-- <th>Total Time</th> -->

	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        if ($isFillter) 
	        {
		        if(mysqli_num_rows($ctable_r)>0){
		            $count = 0;
		            
		            while($ctable_d = mysqli_fetch_array($ctable_r)){
		            	$datetime1 = new DateTime($ctable_d['stop_date_time']);
						$datetime2 = new DateTime($ctable_d['start_date_time']);
						$interval = $datetime1->diff($datetime2);
						$elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
		            //print_r($ctable_d);
		        	?>
		           	<tr>
	                	<td><?php echo ++$count; ?></td>
	                	<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
	                	<?php if($ctable_d['customer_id']==0 && $ctable_d['inquiry_id']!="0"){
	                		?>
	                		<td>
	                		<span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d['inquiry_id']."'",0)." - ".$db->rp_getValue("no_order_inquiry","person_name","id='".$ctable_d['inquiry_id']."'",0) ?></span>
	                	   </td>
	                		<?php
	                	}
	                	else{
	                		?> 
	                		<td>
	                		<span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'")." - ".$db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'"); ?></span>
	                	   </td> 
                        <?php 
	                	}
	                	?>
	                	<?php if($ctable_d['customer_id']==0 && $ctable_d['inquiry_id']!="0"){
	                		?>
	                		<td><?php echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d['inquiry_id']."'") ?></td>
	                		<?php
	                	}
	                	else{
	                		?>
	                		<td><?php echo $db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></td>
	                		<?php
	                	}
	                	?>
	                	
	                	<td><?php echo date("d-m-Y H:i:s",strtotime($ctable_d['created_date'])); ?></td>
	                	<td><?php echo  $db->rp_getValue("purpose_master","name","isDelete=0 AND id=".$ctable_d['purpose_id'],0); ?></td>
	                	<td><?php echo  $ctable_d['name'];?></td>
	                	<td><?php echo stripslashes($ctable_d['remark']); ?></td>
						<td>
							<!-- Trigger the modal with a button -->
							<a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
								<img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
							</a>
						</td>
						<td><?php echo $ctable_d['app_address']; ?></td>
						<td>
							<?php 
							$img = explode(",", $ctable_d['image_path']);
							$imgpath = array();
							for ($i=0; $i < sizeof($img); $i++)
							{ 
								$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
							}
							for ($i=0; $i < sizeof($imgpath); $i++)
							{
								if($i==0)
								{
							?>
								<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							<?php 
								}
								else
								{
							?>
							<div class="hidden">
								<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							</div>
							<?php
								}
							}
							?>
						</td>
						<td><?php if($ctable_d['start_date_time']!="0000-00-00 00:00:00"){echo date('d-m-Y h:i A',strtotime($ctable_d['start_date_time']));} else{echo "";}?></td>
						<td><?php echo stripslashes($ctable_d['stop_remark']); ?></td>
						<td>
							<!-- Trigger the modal with a button -->
							<?php if($ctable_d['stop_longitude']!=""){
								?>
								<a class="mapbtn1" data-app_address="<?php echo stripslashes($ctable_d['stop_app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['stop_latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['stop_longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap1">
								<img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
							</a>
							<?php 
							}?>
							
						</td>
						<td><?php echo $ctable_d['stop_app_address']; ?></td>
						<td>
							<?php 
							if($ctable_d['stop_date_time']!="0000-00-00 00:00:00")
							{
								$img = explode(",", $ctable_d['stop_image_path']);
								$imgpath = array();
								for ($i=0; $i < sizeof($img); $i++)
								{ 
									$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
								}
								for ($i=0; $i < sizeof($imgpath); $i++)
								{
									if($i==0)
									{
							?>
							<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							<?php 
									}
									else
									{
							?>
							<div class="hidden">
								<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							</div>
							<?php
									}
								}
							}
							?>
						</td>
						<td><?php if($ctable_d['stop_date_time']!="0000-00-00 00:00:00"){echo date('d-m-Y h:i A',strtotime($ctable_d['stop_date_time']));} else{echo "";}?></td>
							<td><?php echo $ctable_d['product_name']; ?></td>
						<td>
							<?php
							if($ctable_d['stop_date_time']!="0000-00-00 00:00:00")
							{
								echo $elapsed;
							} 
							?>
						</td>
								
						<td>
							<?php 
								if($ctable_d['visit_type']=="1")
								{
									echo "Existing Customer";
								}
								else if($ctable_d['visit_type']=="3")
								{
									echo "Inquiry";
								}
								else if($ctable_d['visit_type']=="4")
								{
									echo "New Customer";
								}
								else
								{
									echo " ";
								}
							?>
						</td>
							<?php 
								if($ctable_d['visit_stop_flag']=="1"){
									$order_no=$db->rp_getValue("orders","order_no","customer_id='".$ctable_d['customer_id']."' AND DATE(created_date)='".date('Y-m-d',strtotime($ctable_d['stop_date_time']))."' AND sales_id=".$ctable_d['user_id']);
								}
								if($order_no=="" && $ctable_d['visit_stop_flag']=="1"){
										$style = "style='background-color: #f1acac;'";
								}


								if($ctable_d['visit_stop_flag']=="3"){
									$followp=$db->rp_getValue("followup","id","visitor_id='".$ctable_d['customer_id']."' AND reference_id='".$ctable_d['inquiry_id']."' AND DATE(created_date)='".date('Y-m-d',strtotime($ctable_d['stop_date_time']))."' AND user_id=".$ctable_d['user_id'],0);
								}
								if($followp=="" && $ctable_d['visit_stop_flag']=="3"){
										$style = "style='background-color: #f1acac;'";
								}
							?>
							<td <?php 
									if($ctable_d['visit_stop_flag']=="1" && $order_no=="")
									{ 
										echo $style; 
									}
									else if($ctable_d['visit_stop_flag']=="3" && $followp=="")
									{
										echo $style;
									}
								?> 
							>
								<?php 
									if($ctable_d['visit_stop_flag']=="1")
									{
										echo "Create Order<br/>".$order_no;
									}
									else if($ctable_d['visit_stop_flag']=="2")
									{
										echo "Stop Visit With Edit Inquiry";
									}
									else if($ctable_d['visit_stop_flag']=="3")
									{
										echo "Create Followup";
									} 
								?>
							</td>
					</tr>
		        <?php
		            }
		        }
	        	
	        }
	        else
	        {
	        	?>
	        	<tr><td colspan="20" class="text-center"><h3><strong><?= FILTER_INFO ?></strong></h3></td></tr>
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
	        <h4 class="modal-title">Visit</h4>
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
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
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
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
</script>
<script type="text/javascript">
	$(".mapbtn").click(function(){
		var date = $(this).data("date");
		var salesexename = $(this).data("salesexename");
		var lat = $(this).data("lat");
		var lng = $(this).data("long");
		var app_address = $(this).data("app_address");
		$.ajax({
            url: "get_visit_map.php",
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
	$("#sales_executive").select2();
	$("#customer_id").select2();

	$(".filterBtn").on("click",function()
{
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})

</script>

<script type="text/javascript">
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
<?php require_once 'disconnect.php';  ?>