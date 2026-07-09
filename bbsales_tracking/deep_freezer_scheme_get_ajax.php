<?php
$page_id=651;$page_slug='deep_freezer_scheme';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "freezer_scheme";

$ctable_where = "";
// $status_id="";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$ctable_where .= " (mobile_no  LIKE '%" . $db->clean($_REQUEST['searchName']) . "%' )  OR customer_id like '%" . $db->clean($_REQUEST['searchName']) . "%' AND ";
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

if(isset($_REQUEST['status']) && $_REQUEST['status']!="")
{
	$ctable_where .= " AND status='".$_REQUEST['status']."' ";
	$status=$_REQUEST['status'];
}

if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if (isset($_REQUEST['complain_month']) && $_REQUEST['complain_month'] != "" && $_REQUEST['complain_month'] != NULL) {
	$ctable_where .= " AND MONTH(complain_date) = '".$_REQUEST['complain_month']."'";
}

if (isset($_REQUEST['complain_year']) && $_REQUEST['complain_year'] != "" && $_REQUEST['complain_year'] != NULL) {
	$ctable_where .= " AND YEAR(complain_date) = '".$_REQUEST['complain_year']."'";
}
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined) {
	$ctable_where .= " AND complain_date >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined) {
	$ctable_where .= " AND complain_date <= '" .$_REQUEST['fromdate']. "' ";
}

// if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
// {
// 	//echo $_REQUEST['df'];exit;
// 	$date_filter_query = urldecode( $_REQUEST['df'] );

// 	$date_filter_query_ex=explode(" to ",$date_filter_query);

// 	$ctable_where .= " AND ( DATE(complain_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(complain_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
// }


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
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
					$ctable_where .= "  AND (complain_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR complain_created_by(".$_SESSION[SITE_SESS.'REFERANCE_ID']."))";		
				}

		 	} 
		}
	}
	else if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) // customer and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$ctable_where .= " AND customer_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
		}
		else
		{ 
			if($rights['chain_vise_flag'] == 1)
		 	{
				$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
				$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='". $check_id."'",0);
			    if ($get_customer_type== 1)  //super stockist
			    {  
			        $cus_WhereCondition.="super_stockist_id='".$check_id."' AND dealer_distributor_id=0";
			    }
			    else if ($get_customer_type == 2) //distributor 
			    {
			        $cus_WhereCondition.="dealer_distributor_id='".$check_id."'" ;
			    }  

			    $data = $db->rp_getData("executive","id",$cus_WhereCondition,"",0);

			    $CUSIDS=array();
				if($data)
				{
					while($data_d=mysqli_fetch_assoc($data))
					{
						$CUSIDS[]=$data_d['id'];
					}
				} 
				if(!empty($CUSIDS))
				{
					$CUSIDS=implode(",", $CUSIDS);
					$ctable_where .= "  AND customer_id IN (".$CUSIDS.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
				}
				else
				{
					$ctable_where .= "  AND customer_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
				}
			}
			else
			{
				// $ctable_where .= " isDelete=0 AND status!=-1";
			}
		}
	}
}


$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

// SELECT * FROM complain WHERE isDelete=0 AND user_id='4' ORDER BY id DESC limit 0, 500

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?> 
<form action="" name="frm" id="frm" method="post">
	<div class="table-responsive">
		 <span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>
		<table id="datatable_1" class="table table-striped table-bordered table-hover ">
	        <thead>
	        <tr>
	        	<th></th>
	        	<th></th>
	        	<th></th>
	        	     <th>
	                	<label>Filter By Date</label>
							<div class="input-group">
								<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
								<span class="input-group-addon datetimerange-picker-btn">
								<i class="fa fa-calendar"></i>
								</span>
								<span class="input-group-btn">
									<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
								</span>
							</div>
							<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
					</th>
	        	<th></th>
	        	<th>
	        		<select class="form-control input-small" id="customer_id">
			            		<option value="">Select Customer</option>
			                	<?php 
			                	$customer_r = $db->rp_getData("executive","*","isDelete=0 AND isActive=1","",0);
			                	if($customer_r)
		                		{
			                		while ($customer_d = mysqli_fetch_assoc($customer_r))
			                		{
			                			?>
			            					<option value="<?=$customer_d['id']?>" <?=($customer_d['id']==$_REQUEST['customer_id'])?"selected":"";?>><?=$customer_d['cname'];?></option>
			            					<?php 
			            				} 
			            			} 
			            			?>
			            		
			            	</select>
	        	</th>
	        	<th></th>
	        	<th></th>
	        	<th></th>
	        	<th></th>
	        	<th></th>
	        	<!-- <th></th> -->
	        	<!-- <th></th>
	        	<th></th>
	        	<th></th>
	        	<th></th>
	        	<th></th> -->
	        	<th>
						<select class="form-control input-small" id="status">
							<option value="">Select Status</option>
		            		<option value="0" <?=("0"==$_REQUEST['status'])?"selected":"";?>>Pending</option>
		            		<option value="1" <?=("1"==$_REQUEST['status'])?"selected":"";?>>Approved</option>
						</select>
			    </th>
	        </tr> 
	            <tr>
	            	<th></th>
	                <th>No.</th>
	                <th>Serial NO.</th>
	                <th>Date</th>
	                <th>Type Of Customer</th>
	                <th>Customer </th>
	                <!-- <th>Center</th> -->
	                <th>Contact person</th>
	                <th>Mobile Number</th>
	                <th>Distributer</th>
	                <th>Order Amt</th>
	                <th>Utr</th>
	                <!-- <th>Images</th>
	                <th>Agency Permises Image</th>
	                <th>Dealer Images</th>
	                <th>Distributor Image</th>
	                <th>Company Office Image</th> -->
	                <th>Status</th>
	            </tr>
	        </thead>
	        <tbody>
		        <?php
		        if(mysqli_num_rows($ctable_r)>0)
		        {
		            $count = 0;
		            while($ctable_d = mysqli_fetch_array($ctable_r))
		            { 	
		            $Status = array('0'=>"Pending",'1'=>"Approve");
		             if ($ctable_d['status']==0) 
					{
						$style = "style='background-color: #add8e6;'";
					}
					else if($ctable_d['status']==1){
						$style ="style='background-color: #ffffff;'";
					}
			    ?>   
	          <tr <?= $style ?>>
	            	<td>
						<div class="btn-group">
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"><i class="fa fa-gear"></i></button>
							<ul role="menu" class="dropdown-menu">
							   <?php
			            		if ($rights['update_flag'] == 1  && $ctable_d['id'] != -1 && $ctable_d['status']!=1)
			            		{
			                       $edit_url = "mode=edit";
			                       // echo $edit_url;exit();
			                      ?>
			                    <li>
			                     <a href='deep_freezer_scheme_crud.php?id=<?=$ctable_d['id'];?>&<?= $edit_url ?>' title="Edit"><span class="text-success"><i class="fa fa-pencil"></i></span>&nbsp;Edit</a>
			                     
			                      </li>   
			                      <?php     
			            		} 
			            		?>	
			            		<?php
						        if($ctable_d['status']==0)
						        { 
							     	?>
								    <li>
								     	<a onClick="approvestatus('<?php echo $ctable_d['id']; ?>')" ><span class="text-primary"><i class="fa fa-check">&nbsp;Approve</i></span></a>
								    </li>
							   		<?php
						        }
						        ?>	
						       <!--  <li>
									<a name="print" onClick="printReport('<?php echo  $ctable_d['id'] ; ?>')" title="Print Report"><i class="fa fa-print"></i>Download</a>
								</li> -->	
								<?php
									$file_path = "deep_freezer_view.php?id=" . $ctable_d['id'] . "";
												?>
												<!-- <li>
													<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
														<i class="fa fa-file-pdf-o"></i>
														Download
													</a>
												</li> -->
								<!-- <li>
									<a name="print" onClick="printReport('<?php echo  $ctable_d['id'] ; ?>')" title="Print Report"><i class="fa fa-print"></i>Print</a>
								</li> -->
								<?php
									$file_path = "deep_freezer_viewer.php?id=".$ctable_d['id'] . "";
								?>
								<li>
									<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
										<span class="text-primary">
										<i class="fa fa-download"></i>
										Download</span>
									</a>
								</li>
								
							</ul>
						</div>
					</td>
	                <td><?php echo ++$count; ?></td>
	                <td><?php echo stripslashes($ctable_d['serial_no']); ?></td>
	                <td><?php if($ctable_d['created_date']=="1970-01-01" || $ctable_d['created_date']=="0000-00-00"){ echo "";}else{
	                	echo date("d-m-Y",strtotime($ctable_d['created_date']));}?>
	                </td>
	                <td><?php echo $db->rp_getValue("customer_type","name","id='".$ctable_d['executive_type']."'AND isDelete=0")?></td>
	                <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'AND isDelete=0")?></td>
	                <!-- <td><?php echo stripslashes($ctable_d['center']); ?></td> -->
	                <td><?php echo stripslashes($ctable_d['contact_person']); ?></td>  
	                <td><?php echo stripslashes($ctable_d['mobile_no']); ?></td>
	                <td><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['distributor_agency']."' AND isDelete=0")?></td> 
	                <td>
	                	<?php 
	                	$order_amt = $db->rp_getValue("orders","SUM(grand_total)","customer_id='".$ctable_d['customer_id']."' AND isDelete=0");
	                	$order_amt = ($order_amt)?$order_amt:0;
	                	echo CURR.$db->rp_num($order_amt);
	                	?>
	                </td>
	                <td><?php echo stripslashes($ctable_d['utr']); ?></td>
	                <!-- <td> 
	            		<?php
	                    if($ctable_d['image_path']!="")
	                    {
	                        $img = explode(",", $ctable_d['image_path']);
	                      	$imgpath = array();
	                      	for ($i=0; $i < sizeof($img); $i++)
	                      	{ 
	                         $imgpath = $db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
	                    ?>
		               	<a href="<?=SITEURL.'images/document_list/'.$imgpath ?>" download  class="text-warning" title="Download"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
	                    <a href="<?=SITEURL.'images/document_list/'.$imgpath ?>" target="_blank"  class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
	                    <?php
		                     }
		                }
		                else {
		                  	$imgpath ="";
		                           
	                  	}
	                  	?> 
				    </td> -->
	              <!--   <td>
						<?php
						if($ctable_d['agency_permises_photo']!=""){
						?>
						<img src="<?php echo  AGENCY_PERMISES_PHOTO_A.$ctable_d['agency_permises_photo']; ?>" width="100" />
						<?php
							 // echo AGENCY_PERMISES_PHOTO_A.$ctable_d['agency_permises_photo'];exit();
						}else{
							echo "No Image Available.";
						}
						if($ctable_d['url']!=""){
						?>
						<br>
						<a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
						<?php
						}
						?>
					</td> -->
					<!-- <td>
						<?php
						 if($ctable_d['dealer_image']!="" && file_exists(DEALER_PHOTO_A.$ctable_d['dealer_image'])){
							?>
							<img src="<?php echo DEALER_PHOTO_A.$ctable_d['dealer_image']; ?>" width="100" />
							<?php
						 }else{
							echo "No Image Available.";
						 }
						 if($ctable_d['url']!=""){
						 ?>
						 <br>
						 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
						 <?php
						 }
						 ?>
					</td> -->
					<!-- <td>
						<?php
						 if($ctable_d['distributor_image']!="" && file_exists(DISTRIBUTOR_PHOTO_A.$ctable_d['distributor_image'])){
							?>
							<img src="<?php echo DISTRIBUTOR_PHOTO_A.$ctable_d['distributor_image']; ?>" width="100" />
							<?php
						 }else{
							echo "No Image Available.";
						 }
						 if($ctable_d['url']!=""){
						 ?>
						 <br>
						 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
						 <?php
						 }
						 ?>
					</td> -->
				<!-- 	<td>
						<?php
						 if($ctable_d['company_office_image']!="" && file_exists(COMPANY_OFFICE_PHOTO_A.$ctable_d['company_office_image'])){
							?>
							<img src="<?php echo COMPANY_OFFICE_PHOTO_A.$ctable_d['company_office_image']; ?>" width="100" />
							<?php
						 }else{
							echo "No Image Available.";
						 }
						 if($ctable_d['url']!=""){
						 ?>
						 <br>
						 <a href="<?php echo $ctable_d['url']; ?>" target="_blank"><?php echo $ctable_d['url']; ?></a>
						 <?php
						 }
						 ?>
					</td> -->
					<td><?= $Status[$ctable_d['status']];?></td>
		        </tr>
		        <?php
	            	}
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
	        <h4 class="modal-title">scheme</h4>
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
</form>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="50" <?php if ($_REQUEST["show"] == 50 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>50</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) {
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

<div id="myModal" class="modal">
  <span class="close1" onclick='$("#myModal").css("display","none");'>&times;</span>
  <img class="modal-content" style="height: 80%;width: auto;" id="img01">
</div>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4">
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
		status = $("#status").val();
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
	// $("#sales_executive").select2();
	$("#customer_id").select2();
	$("#status").select2();
	// $("#status_id").select2();

	function printReport(id) 
{	
	// var myWindow =  window.open('view_order_new_1.php?order_id='+id+"&p=1",'','width=500,height=800');

	var myWindow =  window.open('deep_freezer_scheme_print.php?id='+id,'','width=500,height=800');
	// alert(id);
			myWindow.print();
		
}
</script>

<?php include("disconnect.php"); ?>