<?php
$page_id=555;$page_slug='page_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "executive";
$ctable1 	= "Executive";
$ctable_where = "";
$where = "";
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							cname like '%".$db->clean($_REQUEST['searchName'])."%'
							OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
if($_REQUEST['type']=="1" )
{
 $where = " AND type_of_executive='2' AND super_stockist_id='".$uid."'";
}
if($_REQUEST['type']=="2" )
{
 $where = " AND type_of_executive='3' AND dealer_distributor_id='".$uid."'";
}
$ctable_where .= " isDelete=0 AND type_of_executive='2'";


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
if(isset($_REQUEST['class_name']) && $_REQUEST['class_name']!="" && $_REQUEST['class_name']!=NULL)
{
	 $ctable_where .= " AND class_id = '".$_REQUEST['class_name']."'";
}
if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL)
{
	$executive_id=array();
	$ctable_area = "class_id=".$_REQUEST['class_name']." AND area_id = '".$_REQUEST['area']."' AND executive_type='2' AND isDelete=0";
	$area_list=$db->rp_getData("executive_map_area","*",$ctable_area,"",0);
	while($area_list_d=mysqli_fetch_assoc($area_list))
	{
		$executive_id[]=$area_list_d['executive_id'];
	}
	$ids=implode(",",$executive_id);
	$ctable_where .= " AND id IN (".$ids.")";
}
if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
 $ctable_where .= " AND state = '".$_REQUEST['state']."' ";
}
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
 $ctable_where .= " AND city = '".$_REQUEST['city']."'";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND type_of_executive = '".$_REQUEST['status']."' ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where.$where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_dealer" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control status" name="state" id="state" onChange="filter_state(this.value);" autofocus >
	                    <option value="">--- Select State---</option>
						<?php
						$id_r = $db->rp_getData("state","*",0);
						if(mysqli_num_rows($id_r)>0)
						{
							while($id_d = mysqli_fetch_array($id_r))
							{
								?>
								<option value="<?php echo $id_d['name']; ?>"><?php echo $id_d['name']; ?></option>
								<?php
							}
						}
						?>
                	</select>
        		</th>
        		<th>
        			<select class="form-control status" name="city" id="city"  autofocus onChange="filter_city(this.value);">
					<option value="">--- Select City---</option>
                    </select>
        		</th>
        		<th></th>
        		<th></th>
        	</tr>
            <tr>
				<th style="width: 5%;"></th>
                <th>No.</th>
                <th>Price List</th>
                <th>Company Name</th>
                <th>Person Name</th>
				<th>Phone</th>	
				<th>City</th>	
				<th>State</th>	
				<th>Customer Type</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            if($ctable_d['type_of_executive']=="1"){
				$type="Super Stockist";
			}
			else if($ctable_d['type_of_executive']=="2"){
				$type="Distributor";
			}
			else{
				$type="Dealer";
			}
        ?>
            <tr>

            	<?php
				if($_REQUEST['type']=="1" || $_REQUEST['type']=="2")
				{
					?>
					<td>
						<div class="btn-group">
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle">More</button>
							<ul role="menu" class="dropdown-menu">
								<li>
									<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
										<span class="text-success">
											<i class="fa fa-circle"></i>
											&nbsp; View Information
										</span>
									</a>
								</li>
							</ul>
						</div>
					</td>
					<?php
				}
				else
				{
				?>
				<td>
					<div class="btn-group">
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
						<ul role="menu" class="dropdown-menu">
							<?php 
							if($rights['update_flag']==1)
							{
								$type_of_executive=$ctable_d['type_of_executive'];
								if($type_of_executive=='1')
								{
									?>
									<li>
										<a onClick="window.location.href='executive_crud.php?mode=edit&type=1&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
											<span class="text-primary">
												<i class="fa fa-pencil"></i>
												&nbsp;Edit
											</span>
										</a>
									</li>
									<?php 
								} 
								
								else if($type_of_executive=='2')
								{
									?>
									<li>
										<a onClick="window.location.href='executive_crud.php?mode=edit&type=2&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
											<span class="text-primary">
												<i class="fa fa-pencil"></i>
												&nbsp;Edit
											</span>
										</a>
									</li>
									<?php 
								} 
								else
								{
									?>
									<li>
										<a onClick="window.location.href='executive_crud.php?mode=edit&type=3&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
											<span class="text-primary">
												<i class="fa fa-pencil"></i>
												&nbsp;Edit
											</span>
										</a>
									</li>
									<?php 
								}
							}
							if($rights['delete_flag']==1)
							{
								?>
								<li>
									<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
										<span class="text-danger">
											<i class="fa fa-times"></i>
											&nbsp;Delete
										</span>
									</a>
								</li>
								<?php
							}
							if($rights['update_flag']==1)
							{
								?>
								<li>
									<?php
									if($ctable_d['isActive']==0)
									{
										?>
										<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
										<?php
									}
									else
									{
										?>
										<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate">
											<span class="text-danger" >
												<i class="fa fa-circle-o"></i>
												&nbsp; Deactivate
											</span>
										</a>
										<?php
									}
									?>
								</li>
								<li>
									<a href="#myModal" data-type="<?php echo $type_of_executive;?>" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist">
										<span class="text-success">
											<i class="fa fa-circle"></i>
											&nbsp; View Information
										</span>
									</a>
								</li>
								<?php
							}
							?>
						</ul>
					</div>				
                </td>
				<?php
				}
				?>

                <td><?php echo ++$count; ?></td>
                <td><?= $db->rp_getValue("price_list","pricelist_name","id='".$ctable_d['price_list_id']."' AND isDelete=0"); ?></td>
				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['company_name']); ?></span></td>
                <td><?php echo stripslashes($ctable_d['cname']); ?></td>
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo $ctable_d['city'];//$db->rp_getValue("city","name","id=".$ctable_d['city'].""); ?></td>
				<td><?php echo $ctable_d['state'];//$db->rp_getValue("state","name","id=".$ctable_d['state'].""); ?></td>
				<td><?php echo stripslashes($type); ?></td>
				<td><a style="margin-top: 5px;" href="add_executive_class_area.php?executive_id=<?php echo $ctable_d['id']?>&type=<?php echo $ctable_d['type_of_executive'] ?>" class="btn btn-success btn-sm" title="track">Add Class Area</a></td>

            </tr>
        <?php
            }
        }
		else{
			?>
			<tr>
			<td colspan="9"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			<?php
		}
        ?>
        </tbody>
    </table>
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
<script type="text/javascript">
	$("#state").select2();
    $("#city").select2();
</script>
<?php require_once 'disconnect.php';  ?>