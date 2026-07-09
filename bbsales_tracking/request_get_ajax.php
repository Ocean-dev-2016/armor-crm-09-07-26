<?php
$page_id=591;$page_slug='request_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "request";

$ctable_where = "";
$status_id="";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

	$ctable_where .= " (request_no like '%".$db->clean($_REQUEST['searchName'])."%') AND ";

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
}

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;
if(isset($_REQUEST["sales_executive"]) && $_REQUEST["sales_executive"]!="" && $_REQUEST["sales_executive"]!=undefined){
	$ctable_where .= " AND user_id='".$_REQUEST["sales_executive"]."'";
	$sid = $_REQUEST["sales_executive"];
}
if(isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"]!="" && $_REQUEST["customer_id"]!=undefined){
	$ctable_where .= " AND customer_id='".$_REQUEST["customer_id"]."'";
	$cid = $_REQUEST["customer_id"];
}

if(isset($_REQUEST['status_id']) && $_REQUEST['status_id']!="")
{
	$ctable_where .= " AND status='".$_REQUEST['status_id']."' ";
	$status_id=$_REQUEST['status_id'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
    $ctable_where .= " AND (request_assign_to = '".$check_id."' OR request_created_by = '".$check_id."') ";
}


$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
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
                	<select class="form-control input-small" name="sales_executive" id="sales_executive">
                		<option value="">Select Sales Officer</option>
                		<?php
                			$D_r = $db->rp_getData("sales_executive","id,name","","",0);
                			while ($D = mysqli_fetch_assoc($D_r))
                			{
                				?>
                				<option value="<?=$D['id']?>" <?=($sid == $D['id'])?"selected":"";?>><?=$D['name']?></option>
                				<?php
                			}
                		?>
                	</select>
                </th>
                <th>
                	<select class="form-control input-small" name="customer_id" id="customer_id">
                		<option value="">Select Customer</option>
                		<?php
                			$E_r = $db->rp_getData("executive","*","1=1 GROUP By cname","",0);
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
                <th></th>
                <th></th>
                <th></th>
                
                <th>  
                	<select class="form-control" id="status_id" name="status_id" onchange="getStatus(this.value)">
	                    	<option value="">Select Status</option>
	                    	<option <?= ($status_id==0 && $status_id!="")?"selected":""; ?> value="0">Generate</option>        
	                    	<option <?= ($status_id==1)?"selected":""; ?> value="1">In Progress</option> 
	                    	<option <?= ($status_id==2)?"selected":""; ?> value="2">Complete</option>        
	                    	<option <?= ($status_id==-1)?"selected":""; ?> value="-1">Reject</option>        
	                    	<option <?= ($status_id==-2)?"selected":""; ?> value="-2">Not Done</option>       
	                    	<!-- <option <?= ($status_id==-3)?"selected":""; ?> value="-3">Cancel</option> -->              
	               	</select>
                </th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Request No.</th>
                <th>Date and Time</th>
                <th>Sales Officer Name</th>
                <th>Customer Name</th>
                <th>Source of Request</th>
                <th>Request Category</th>
                <th>Request Sub Category</th>
               	<th>Description</th>
				<!-- <th>Location Map</th> -->
				<th>Address</th>
				<th>Image</th>
				<th>Status</th>	
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            	$request_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo "#".stripslashes($ctable_d['request_no']); ?></td>
                <td><?php echo date("d-m-Y h:i A",strtotime($ctable_d['created_date'])); ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'") ?></span></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'")."</span><span><br/>".$db->rp_getValue("executive","phone","id='".$ctable_d['customer_id']."'") ?></span></td>
                <td><?php echo stripslashes($request_type_array[$ctable_d['request_type']]); ?></td>
                <td><?php echo  $db->rp_getValue("complain_category","name","id='".$ctable_d['request_cat_id']."'"); ?></td>
                <td><?php echo  $db->rp_getValue("complain_sub_category","name","id='".$ctable_d['request_subcat_id']."'");?></td>
				<td><?php echo stripslashes($ctable_d['remark']); ?></td>
				<!-- <td>
					<a class="mapbtn" data-app_address="<?php echo stripslashes($ctable_d['app_address']); ?>" data-lat="<?php echo stripslashes($ctable_d['latitude']); ?>" data-long="<?php echo stripslashes($ctable_d['longitude']); ?>" data-date="<?=date("d M H:i",strtotime($ctable_d['created_date']));?>" data-salesexename="<?=$db->rp_getValue("sales_executive","name","id='".$ctable_d["user_id"]."'",0);?>" data-toggle="modal" data-target="#OpenMap">
						<img src="<?=SITEURL?>resource/map.png" style="height: 80px;">
					</a>
				</td> -->
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
					if($i==0){
				?>
				<a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="complain <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
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
                <td>
                	<select class="form-control" disabled="disabled" id="complain_status<?= $ctable_d['id']?>" style="width:200px;text-align:center;margin: auto;">
	                    	<option value="">Select Status</option>
	                    	<option <?= ($ctable_d['status']==0)?"selected":""; ?> value="0">Generate</option>              
	                    	<option <?= ($ctable_d['status']==1)?"selected":""; ?> value="1">In Progress</option>              
	                    	<option <?= ($ctable_d['status']==2)?"selected":""; ?> value="2">Complete</option>              
	                    	<option <?= ($ctable_d['status']==-1)?"selected":""; ?> value="-1">Reject</option>              
	                    	<option <?= ($ctable_d['status']==-2)?"selected":""; ?> value="-2">Not Done</option>              
	                    	<!-- <option <?= ($ctable_d['status']==-3)?"selected":""; ?> value="-3">Cancel</option>  -->             
	                    </select>
	                    <a href="javascript:void(0);" id="editStatus_<?php echo $ctable_d['id']; ?>" onClick="editStatus('<?php echo $ctable_d['id']; ?>')">Edit</a>                    
	                    <span id="editStatus2_<?php echo $ctable_d['id']; ?>" style="display:none;">
	                        <a href="javascript:void(0);" id="saveEditStatus<?php echo $ctable_d['id']; ?>" onClick="saveEditStatus('<?php echo $ctable_d['id']; ?>')">Save</a> |
	                        <a href="javascript:void(0);" id="cancelEditStatus<?php echo $ctable_d['id']; ?>" onClick="cancelEditStatus('<?php echo $ctable_d['id']; ?>')">Cancel</a>
	                    </span>
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
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
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
	$("#customer_id").select2();
	$("#status_id").select2();
</script>
<?php require_once 'disconnect.php';  ?>