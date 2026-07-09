<?php
$page_id=601;$page_slug='complain_report_page';
include("connect.php");
$ctable 	= "complain";

$ctable_where = "";
$status_id="";
$isFillter = filter_var($_REQUEST['isFillter'], FILTER_VALIDATE_BOOLEAN);
// Get the total number of rows in the table
/*print_r($_REQUEST);exit();
*/
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

	$ctable_where .= " (complain_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

	// $sales_id = $db->rp_getData("sales_executive","*","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($sales_id)
	// {		
	// 	while($K=mysqli_fetch_assoc($sales_id))
	// 	{
	// 		$USER_IDS[]=$K['id'];
	// 	}
	// 	$USER_IDS=implode(",",$USER_IDS);
	// 	$ctable_where .="user_id IN ('".$USER_IDS."') OR";
	// }
	// else
	// {
	// 	$ctable_where .="user_id IN ('') OR";
	// }

	// $customer_id = $db->rp_getData("executive","*","cname LIKE '%".$_REQUEST['searchName']."%' OR phone LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	// if($customer_id)
	// {		
	// 	while($K1=mysqli_fetch_assoc($customer_id))
	// 	{
	// 		$CUSTOMER_IDS[]=$K1['id'];
	// 	}
	// 	$CUSTOMER_IDS=implode(",",$CUSTOMER_IDS);
	// 	$ctable_where .=" customer_id IN ('".$CUSTOMER_IDS."') AND";
	// }
	// else
	// {
	// 	$ctable_where .=" customer_id IN ('') AND ";
	// }
	$isFillter=true;
}

$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined && $_REQUEST["sales_executive"]!="null"){
	
	//$ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
	$ctable_where .= " AND user_id IN (".$_REQUEST['sales_executive'].")";
	$isFillter=true;
	
}

// if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined && $_REQUEST["customer_id"]!="null"){

// 	//$ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
// 	$ctable_where .= " AND customer_id IN (".$_REQUEST['customer_id'].")";
// 	$isFillter=true;
// }

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="" && $_REQUEST['status_id']!="null")
{
	//$ctable_where .= " AND status='".$_REQUEST['status_id']."' ";
	// $ctable_where .= " AND status IN (".$_REQUEST['status_id'].") ";
	 $Where .= " status='".implode(" , ", $_REQUEST['status_id'])."' AND ";
	 $isFillter=true;
	
}

// if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!="null")
// {
// 	//$ctable_where .= " AND class_id='".$_REQUEST['class_id']."' ";
// 	$ctable_where .= " AND class_id IN (".$_REQUEST['class_id'].") ";
// }

// if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="" && $_REQUEST['area_id']!="null")
// {
// 	//$ctable_where .= " AND area_id='".$_REQUEST['area_id']."' ";
// 	$ctable_where .= " AND area_id IN (".$_REQUEST['area_id'].")";
// }

// if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL && $_REQUEST['class_id']!="null")
// {
// 	$state_str=array();
// 	  $state_str1=array();
//     $state_r = $db->rp_getData("class","name","id in (".$_REQUEST['class_id'].")","",0);
//     while($state_d = mysqli_fetch_array($state_r)) 
//     {
//         $state_str[] = "'".$state_d['name']."'";
//     }
//     $class_str1 = implode(",",$state_str);
//     $isFillter=true;
//     $class_name_r=$db->rp_getData("executive","id","isDelete=0 AND state in (".$class_str1.")","",0);
//     while ($class_name_d= mysqli_fetch_array($class_name_r)) {
//     	$state_str1[]=$class_name_d['id'];
//     }
//       $class_str1 = implode(",",$state_str1);
//       $ctable_where .= " AND customer_id IN (".$class_str1.")  ";
// 	}


// if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="" && $_REQUEST['area_id']!=NULL && $_REQUEST['area_id']!="null")
// {
// 	$cty=array();
// 	  $cty1=array();
//     $city_r = $db->rp_getData("area","name","id in (".$_REQUEST['area_id'].")","",0);
//     while($city_d = mysqli_fetch_array($city_r)) 
//     {
//         $cty[] = "'".$city_d['name']."'";
//     }
//     $cityStr1 = implode(",",$cty);
//     $isFillter=true;
//     $city_name_r=$db->rp_getData("executive","id","isDelete=0 AND main_city in (".$cityStr1.")","",0);
//     while ($city_name_d= mysqli_fetch_array($city_name_r)) {
//     	//echo "fgry";exit();
//     	$cty1[]=$city_name_d['id'];
//     }

//       $cityStr1 = implode(",",$cty1);
//       $ctable_where .= " AND customer_id IN (".$cityStr1.")  ";
// 	}



//  //echo $ctable_where;exit();


// if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
// {
//     // $area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
//     // while($area_d = mysqli_fetch_array($area_r)) 
//     // {
//     //     $area_str[] = "'".$area_d['name']."'";
//     // }
//     // // echo implode(",",$area_str);exit;
//     // $ctable_where .= " AND area_id IN (".implode(",",$area_str).") ";

   

//  //    $area_str=array();
// 	//   $area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
//  //    while($area_d = mysqli_fetch_array($area_r)) 
//  //   	 {
//  //        $area_str[] = "'".$area_d['name']."'";
//  //    }
//  //    $area_str = implode(",",$area_str);

// 	// //$area_str=array();
// 	// $area_name_r=$db->rp_getData("executive","id","isDelete=0 AND city in (".$area_str.")","",0);
// 	// while($area_name_d=mysqli_fetch_array($area_name_r))
// 	//  {
// 	// 	$area_str[]=$area_name_d['id'];
// 	// }
// 	//   $area_str = implode(",",$area_str);
//  //      $ctable_where .= " AND customer_id IN (".$area_str.")  ";
//  //      $isFillter=true;
// }

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	$isFillter=true;
}




// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
// }


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['personal_flag']==1)
	{
		 $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    	$ctable_where .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
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
					
						$ctable_where .= "  AND (complain_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR  complain_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") ) ";	
					
					
				}
				else
				{
						$ctable_where .= "  AND sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
				}

	 	}
	 	else
	 	{
	 		
	 	}
	}
}

/*if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="" && $_REQUEST['customer_type']!="null" &&  $_REQUEST['customer_type']!="0" )
{
		
	   $ctable_where.=" AND executive_type='".$_REQUEST['customer_type']."'";
		$isFillter=true;
}*/

if(isset($_REQUEST['company_type']) && $_REQUEST['company_type']!="" && $_REQUEST['company_type']!=NaN && $_REQUEST['company_type']!="null" &&  $_REQUEST['company_type']!="0" )
{
	
		$ctable_where.=" AND type_of_company='".$_REQUEST['company_type']."'";
		$isFillter=true;
	
}

if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!="null")
{
       $ctable_where.=" AND customer_id='".$_REQUEST['customer_id']."'";
       $isFillter=true;

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

</style>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
                <th></th>
                <th></th>
                <!-- <th></th> -->
                <th>
                	
                </th>
                <th>
                	
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="width: 10%!important;"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>

            </tr>
            <tr>
                <th>No.</th>
                <th>Complain No.</th>
                <th>Sales Person Name</th>
                <th>Customer Name</th>
               <!--  <th>Title </th> -->
                <!-- <th>Mobile No.</th> -->
				<!-- <th>Latitude</th>
				<th>Longitude</th> -->
				<th>Description</th>
				<!-- <th>Location Map</th> -->
				<th>Address</th>
				<th>Image</th>
				<th>State</th>
				<th>City</th>
                <th>Date and Time</th>
				<th>Status</th>	
            </tr>
        </thead>
        <tbody>
        <?php
        if ($isFillter) 
        {
	        if(mysqli_num_rows($ctable_r)>0){
	            $count = 0;
	            
	            while($ctable_d = mysqli_fetch_array($ctable_r)){
	            //print_r($ctable_d);
	        ?>
	            <tr>
	                <td><?php echo ++$count; ?></td>
	                <td><?php echo "#".stripslashes($ctable_d['complain_no']); ?></td>
	                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
	                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")."</span><span><br/>".$db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></span></td>
	                <!-- <td><?php echo stripslashes($ctable_d['latitude']); ?></td>
					<td><?php echo stripslashes($ctable_d['longitude']); ?></td> -->
	               <!--  <td><?php echo stripslashes($ctable_d['title']); ?></td> -->
					<td><?php echo stripslashes($ctable_d['remark']); ?></td>
					<!-- <td> -->
						<!-- Trigger the modal with a button -->
						<!-- <a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
							<img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
						</a>
					</td> -->
					<td>	
						<a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">

							<!-- <img src="<?=SITEURL?>resource/map.png" style="height: 80px;"> -->
						<?php echo $ctable_d['app_address']; ?></td>
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
						if($i==0){
					?>
					<a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px; width: 120px;"></a>
					<?php }else{
						?>
							<div class="hidden">
								<a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
							</div>
						<?php
							}
						}
					?>
					</td>

					<!-- <td><?php echo ($ctable_d['state']); ?></td>

					<td><?php echo ($ctable_d['city']); ?></td> -->
					<!-- <td><?php echo $db->rp_getValue("class","name","id='".$ctable_d['class_id']."'") ?></td> -->
					
					<!-- <td><?php echo $db->rp_getValue("area","name","id='".$ctable_d['area_id']."'") ?></td> -->
					<td><?php echo $db->rp_getValue("executive","state","id='".$ctable_d['customer_id']."'") ?></td>

					<td><?php echo $db->rp_getValue("executive","main_city","id='".$ctable_d['customer_id']."'") ?></td>

	                <td><?php echo date("d-m-Y H:i A",strtotime($ctable_d['created_date'])); ?></td>

	                <td>

	                	<?php
						if ($ctable_d['status']==0)
						{
							echo $ctable_d['status']="Generate";
						}
						else if ($ctable_d['status']==1)
						{
							echo $ctable_d['status']="Progress";
						}
						else if ($ctable_d['status']==2)
						{
							echo $ctable_d['status']="Complete";
						}
						else if ($ctable_d['status']==-1)
						{
							echo $ctable_d['status']="Reject";
						}
						else if ($ctable_d['status']==-2)
						{
							echo $ctable_d['status']="Not Done";
						}
						// else if ($ctable_d['status_id']==-3)
						// {
						// 	echo $ctable_d['status_id']="Cancel";
						// }
						?>

	                	<!-- <select class="form-control" disabled="disabled" id="complain_status<?= $ctable_d['id']?>" style="width:200px;text-align:center;margin: auto;">
		                    	<option value="">Select Status</option>
		                    	<option <?= ($ctable_d['status']==0)?"selected":""; ?> value="0">Generate</option>              
		                    	<option <?= ($ctable_d['status']==1)?"selected":""; ?> value="1">In Progress</option>              
		                    	<option <?= ($ctable_d['status']==2)?"selected":""; ?> value="2">Complete</option>              
		                    	<option <?= ($ctable_d['status']==-1)?"selected":""; ?> value="-1">Reject</option>              
		                    	<option <?= ($ctable_d['status']==-2)?"selected":""; ?> value="-2">Not Done</option> -->              
		                    	<!-- <option <?= ($ctable_d['status']==-3)?"selected":""; ?> value="-3">Cancel</option>  -->             
		                    <!-- </select> -->
		                    <!-- <a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
		                    <span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
		                        <a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
		                        <a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
		                    </span> -->
	                </td>
					<!-- <td>
						<?php 
						
						if($rights['update_flag']==1)
						{
							?>
							
							<div class="btn-group">
							
								<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
									More
								</button>
								<ul role="menu" class="dropdown-menu">
									<li>
										<?php
										if($ctable_d['isActive']==0){
										?>
											<a  href="complain_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
										<?php
										}else{
										?>
											<a  href="complain_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger" ><i class="fa fa-circle-o"></i> &nbsp; Deactivate </span></a>
										<?php
										}
										?>
									</li>
								</ul>
							</div>
							<?php
						}
						?>
					</td> -->
	            </tr>
	        <?php
	            }
	        }
        	
        }
        else
        {
        	?>
        	<tr><td class="text-center" colspan="13"><h3><strong><?= FILTER_INFO ?></strong></h3></td></tr>
        	<?php
        }
        ?>
        </tbody>
    </table>
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
	$("#sales_executive").select2();
	$("#customer_id").select2();
	$("#status_id").select2();
</script>
<?php require_once 'disconnect.php';  ?>