<?php
$page_id=556;$page_slug='page_sales_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "sales_executive";
$ctable1 	= "Sales Officer";

$ctable_where = "";
// Get the total number of rows in the table

// print_r($_REQUEST);exit;
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (name like '%".$db->clean($_REQUEST['searchName'])."%' OR email like '%".$db->clean($_REQUEST['searchName'])."%' OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%' OR username  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
	) AND ";
}

//$ctable_where .= " isDelete=0 AND type='sales_manager'";

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
if(isset($_REQUEST['class_name']) && $_REQUEST['class_name']!="" && $_REQUEST['class_name']!=NULL)
{
	 $ctable_where .= " AND class_id = '".$_REQUEST['class_name']."'";
}
if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL)
{
	$executive_id=array();
	$ctable_area = "class_id=".$_REQUEST['class_name']." AND area_id = '".$_REQUEST['area']."' AND executive_type='sales_manager' AND isDelete=0";
	$area_list=$db->rp_getData("sales_executive_map_area","*",$ctable_area,"",0);
	while($area_list_d=mysqli_fetch_assoc($area_list))
	{
		$executive_id[]=$area_list_d['sales_executive_id'];
	}
	//print_r($executive_id);exit;
	$ids=implode(",",$executive_id);
	$ctable_where .= " AND id IN (".$ids.")";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 	$ctable_where .= " AND type = '".$_REQUEST['type']."' ";
 	$sale_typ = $_REQUEST['type'];
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
 	$ctable_where .= " AND state = '".$_REQUEST['state']."' ";
 	$state=$_REQUEST['state'];
}
if(isset($_REQUEST['main_city']) && $_REQUEST['main_city']!="" && $_REQUEST['main_city']!=NULL)
{
 	$ctable_where .= " AND main_city = '".$_REQUEST['main_city']."'";
 	$main_city=$_REQUEST['main_city'];
}
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
 	$ctable_where .= " AND city = '".$_REQUEST['city']."'";
 	$city=$_REQUEST['city'];
}

if(isset($_REQUEST['zone']) && $_REQUEST['zone']!="" && $_REQUEST['zone']!=NULL)
{
 	$ctable_where .= " AND zone = '".$_REQUEST['zone']."' ";
 	$zone=$_REQUEST['zone'];
}


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	if($rights['personal_flag']==1)
	{
		$ctable_where .= "  AND id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
	}
	else
	{
		if($rights['chain_vise_flag'] == 1)
	 	{
 			$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

		    $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
		    if ($get_sales_type== "sales_manager") 
		    {
		        $sales_executive_type = "M.D.";
		        $key="sm_id";
		        $WhereCondition.=' ' .$key.'='.$check_id;
		    }

		    else if ($get_sales_type == "area_sales_manager") 
		    {
		        $sales_executive_type = "General Manager";//Business Development Manager
		        $key="asm_id";
		        $WhereCondition.=' ' .$key.'='.$check_id;
		    }

		    else if ($get_sales_type == "sales_officer") 
		    {
		        $sales_executive_type = "Regional Sales Manager";//Area Sales Manager
		        $key="so_id";
		        $WhereCondition.=' ' .$key.'='.$check_id;
		    }
		    else if ($get_sales_type == "sales_executive") 
		    {
		        $sales_executive_type = "Sales Officer";
		        $key="se_id";
		        $WhereCondition.=' ' .$key.'='.$check_id;
		    }
		     else if ($get_sales_type == "area_manager") 
		    {
		        $sales_executive_type = "Area Sales Manager";
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
				$ctable_where .= "  AND id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
				
				
			}
			else
			{
				$ctable_where .= "  AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
			} 
	 	}  
	} 
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page); 
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"isActive DESC,id DESC",0);
?>
<style>
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
	<table id="datatable_sm" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
        	<tr>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control" name="sales_executive_type" id="sales_executive_type" onChange="SalesexecutiveType(this.value);" autofocus >
	                    <option value="">Select Sales  Type</option>
	                    <option value="sales_manager" <?= ($sale_typ == "sales_manager")?"selected":""; ?>>M.D.</option>
	                    <option value="area_sales_manager" <?= ($sale_typ == "area_sales_manager")?"selected":""; ?>>General Manager</option>

	                <!--     <option value="dispatch_sales_manager" <?= ($sales_executive_type=="dispatch_sales_manager")?"selected":""; ?>>Dispatch Manager</option> -->
	                    
	                    <option value="sales_officer" <?= ($sale_typ=="sales_officer")?"selected":""; ?>>Regional Sales Manager</option>
	                    <option value="sales_executive" <?= ($sale_typ=="sales_executive")?"selected":""; ?>>Sales Officer</option>
	                      <option value="area_manager" <?= ($sale_typ=="area_manager")?"selected":""; ?>>Area Sales Manager</option>
	                    <option value="service_executive" <?= ($sale_typ=="service_executive")?"selected":""; ?>>Service Executive</option>
					</select>
        		</th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control status" name="state" id="state" onChange="filter_state(this.value);" autofocus >
	                    <option value="">Select State</option>
						<?php
						$state_r = $db->rp_getData("state","*",0);
						if(mysqli_num_rows($state_r)>0)
						{
							while($state_d = mysqli_fetch_array($state_r))
							{
								?>
								<option value="<?php echo $state_d['name']; ?>" <?=($state == $state_d['name'])?"selected":"";?>><?php echo $state_d['name']; ?></option>
								<?php
							}
						}
						?>
                	</select>
        		</th>
        		<th>
						<select class="form-control status" name="main_city" id="main_city"  onChange="filter_city(this.value);">
							<option value="">Select City</option>
						</select>
					</th>
					<th>
						<select class="form-control status" name="city" id="city" >
							<option value="">Select Route</option>
						</select>
					</th>


        		<th>
        			<select class="form-control status" name="zone" id="zone" autofocus >
	                    <option value="">Select Zone</option>
						<?php
						$zone_r = $db->rp_getData("zone","*",0);
						if(mysqli_num_rows($zone_r)>0)
						{
							while($zone_d = mysqli_fetch_array($zone_r))
							{
								?>
								<option value="<?php echo $zone_d['id']; ?>" <?=($zone == $zone_d['id'])?"selected":"";?>><?php echo $zone_d['name']; ?></option>
								<?php
							}
						}
						?>
                	</select>
        		</th>
        		<!-- <th></th>
        		<th></th> -->
        	</tr>
            <tr>
            	<th class="fix-th1"></th>
                <th class="fix-th1">No.</th>
				<th class="fix-th1">Sales Officer Type</th>
                <th class="fix-th1">Name</th>
                <th class="fix-th1">Username</th>
				<th class="fix-th1">Email</th>
				<th class="fix-th1">CUG No</th>	
				<th class="fix-th1">State</th>	
				<th class="fix-th1">City</th>	
				<th class="fix-th1">Route</th>	
				<th class="fix-th1">Zone</th>	
				<!-- <th>Action</th>
				<th>User Create</th> -->
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $bg_color = "";
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            $sales_executive_type = "";
            if($ctable_d['type']=="sales_manager")
            {
				$sales_executive_type="M.D.";
			}

			if($ctable_d['type']=="area_sales_manager")
			{
				$sales_executive_type="General Manager";
			}

			if($ctable_d['type']=="dispatch_sales_manager")
			{
				$sales_executive_type="Dispatch Manager";
			}
			
			if($ctable_d['type']=="sales_officer")
			{
				$sales_executive_type="Regional Sales Manager";
			}
			
			if($ctable_d['type']=="sales_executive")
			{
				$sales_executive_type="Sales Officer";
			}
			if($ctable_d['type']=="area_manager")
			{
				$sales_executive_type="Area Sales Manager";
			}
			if($ctable_d['type']=="service_executive")
			{
				$sales_executive_type="Service Executive";
			}

			if ($ctable_d['isActive']=="0") 
			{

				$bg_color = "background-color:#f7dada";
				$exe_deactive_color = "color:#000;";
			}
			else
			{
				$exe_deactive_color = "color:#26A69A";
			}

        ?>
            <tr style="<?= $bg_color ?>;">
            	<td>
            		<div class="btn-group">
            			<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"><i class="fa fa-gear"></i></button>
            			<ul role="menu" class="dropdown-menu">
            				<?php 
							if($rights['update_flag']==1)
							{?>
								<li>
									<?php
									$type=$ctable_d['type'];
									if($type=='sales_manager')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=sales_manager&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}
									else if($type=='area_sales_manager')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=area_sales_manager&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}

									else if($type=='dispatch_sales_manager')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=dispatch_sales_manager&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}

									else if($type=='sales_officer')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=sales_officer&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}
									else if($type=='sales_executive')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=sales_executive&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}
									else if($type=='area_manager')
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=area_manager&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}
									else 
									{ ?>
										<a href="sales_executive_crud.php?mode=edit&type=service_executive&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
									<?php
									}
									?>
								</li>
							<?php
							}
							if($rights['delete_flag']==1)
							{
								?>
								<!-- <li>
									<a  onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
								</li> -->
								<?php
							}
							?>
							<li>
								<a class="" href="#changePasswordModal" data-id="<?php echo $ctable_d['id']; ?>" class="btn sbold blue-ebonyclay" data-toggle="modal" title="Change Password"> <i class="fa fa-gear"></i>&nbsp;Change Password</a>
							</li>

							<li>
								<?php
								if($ctable_d['isActive']==1){
								?>
									<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger"><i class="fa fa-circle"></i> &nbsp;Deactivate</span></a>
								<?php
								}else{
								?>
									<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Aactivate"><span class="text-success" ><i class="fa fa-circle-o"></i> &nbsp; Activate </span></a>
								<?php
								}
								?>
							</li>
							

							<!-- <li>
								<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View information</span></a>
							</li> -->

							<li>
								<a href="attendance_manage.php?id=<?php echo $ctable_d['id']?>" class="" title="track"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; Attendance</span></a>
							</li>

							<li>
								<a href="target_manage.php?sales_id=<?php echo $ctable_d['id']?>" class="" title="track"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; Target</span></a>
							</li>

							<!-- <li>
								<a href="followup.php?mode=followup&sales_id=<?php echo $ctable_d['id']?>" class="" title="track"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; Followup</span></a>
							</li> -->
							<li>
								<a href="add_sales_executive_class_area.php?sales_id=<?php echo $ctable_d['id']?>&type=<?php echo $type ?>" title="track"><span class="text-success"><i class="fa fa-circle"></i> &nbsp; Add Class Area</a>
							</li>
							<li>
								<?php
								if($ctable_d['isActive']==1 && $ctable['isDelete']==0)
								{
									$user_count = $db->rp_getTotalRecord("dealer_distributor_network","sales_executive_id='".$ctable_d['id']."' AND id!='1' AND isDelete=0");
									if($user_count=='0')
									{
										?>
											<a  href='#usercreate' data-title="Create System User" data-id="<?php echo $ctable_d['id']; ?>" data-toggle='modal'><span class="text-success"><i class="fa fa-circle"></i> &nbsp; Create System User</a>
										<?php
									}
								}
								?>
					</li>	
				
						</ul>
            		</div>
            	</td>
            	<td><?php echo ++$count; ?></td>
				<td><?php echo $sales_executive_type; ?></td>
                <td >
                	<span style="<?= $exe_deactive_color?>;">	
                		<?php echo stripslashes($ctable_d['name']); ?>
                	</span>
                </td>
                <td >
                	<span style="<?= $exe_deactive_color?>;">	
                		<strong><?php echo stripslashes($ctable_d['username']); ?></strong>
                	</span>
                </td>
				<td><?php echo stripslashes($ctable_d['email']); ?></td>
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo stripslashes($ctable_d['state']); ?></td>

				<td><?php echo stripslashes($ctable_d['main_city']); ?></td>
				<td><?php echo stripslashes($ctable_d['city']); ?></td>
				<?php $zone = $db->rp_getValue("zone","name","id='".$ctable_d['zone']."' AND isDelete=0",0); ?>
				<td><?php echo stripslashes($zone); ?></td>

				
			<!-- 	<td><a style="margin-top: 5px;" href="add_sales_executive_class_area.php?sales_id=<?php echo $ctable_d['id']?>&type=<?php echo $type ?>" class="btn btn-success btn-sm" title="track">Add Class Area</a></td>
				<td>
					<?php
					if($ctable_d['isActive']==1 && $ctable['isDelete']==0)
					{
						$user_count = $db->rp_getTotalRecord("dealer_distributor_network","sales_executive_id='".$ctable_d['id']."' AND id!='1' AND isDelete=0");
						if($user_count=='0')
						{
							?>
								<a class="btn btn-success btn-sm" href='#usercreate' data-title="Create System User" data-id="<?php echo $ctable_d['id']; ?>" data-toggle='modal'>Create System User</a>
							<?php
						}
					}
					?>	
				</td> -->
				<?php
            }
        }
		else{
			?>
			<tr>
			<td colspan="10"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			<?php
		}
        ?>
        </tbody>
    </table>
    </div>
  <!--   <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
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
	</div> -->
</form>
<script type="text/javascript">
	$("#state").select2();
	$("#main_city").select2();
    $("#city").select2();
    $("#zone").select2();
    $("#sales_executive_type").select2();

    function filter_state(state_id,main_city=""){
	    $.ajax({
	        type: "POST",
	        url: "find_city.php",
	        data:'state_id='+state_id+"&main_city="+main_city,
	        beforeSend:function(){
	            // $("#loading-modal").modal('show');  
	            $('.preloader').fadeIn('slow');
	        },
	        success: function(data){
	            $("#main_city").select2("destroy");
	            $("#main_city").html(data);
	            $("#main_city").select2();
	            // $("#loading-modal").modal('hide');
	            $('.preloader').fadeOut('slow');
	        }
	    });
	}
	function filter_city(main_city_id, city = "") {
		// alert(city_id);
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'main_city=' + main_city_id + "&city=" + city,
			beforeSend: function() {
				 $("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				$("#city").select2("destroy");
				$("#city").html(data);
				$("#city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}
</script>

<!-- system user create script -->
<script type="text/javascript">
	function CreateSystemUser(id)
	{
		var r = confirm("Are you sure you want to Create User ?");
		if(r)
		{
			$.ajax({
	            url:"create_user_ajax_function.php",
	            type:"POST",
	            data:{
	                id:id, 
	            },
	            beforeSend: function() {
					$('.preloader').fadeIn('slow');
				},
	            success:function(result) 
	            {
                    var result=JSON.parse(result);
                    $('.preloader').fadeOut('slow');
                    if(result.ack==1)
                    {                       
                        toastr.success(result.ack_msg,"Success!!"); 
                        location.reload();
                    }
                    else
                    {
                        toastr.error(result.ack_msg, 'Error!!');
                    }
	            },            
	        })
		}
	}
</script>
<!-- system user create script -->
<?php require_once("disconnect.php"); ?>