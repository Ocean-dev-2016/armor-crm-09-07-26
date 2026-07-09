<?php
// print_r($_REQUEST);exit;
$page_id=606;$page_slug='page_area';
include("connect.php");
$ctable = "area";
$ctable1 = "Area";
$ctable_where = "";
$order_ids=array();
$isFillter=false;
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    //$ctable_where .= " (name like '%".$_REQUEST['searchName']."%') AND ";
    
    $data_search = $db->rp_getData("area","city_id","name = '".$_REQUEST['searchName']."' AND isDelete=0","",0);
    if($data_search)
    {
        while($data_search_d=mysqli_fetch_assoc($data_search))
        {
            $order_ids[]=$data_search_d['city_id'];
        }
        $order_ids=implode(",",$order_ids);
        $ctable_where .= "  Id IN (".$order_ids.") AND ";
    }
    
}
$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
if ($_REQUEST['class_id'] == 'all') 
{
	$isFillter=true;
}
else
{
	if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL)
	{
	 $ctable_where .= " AND state_id = '".$_REQUEST['class_id']."' ";
	 $isFillter=true;
	}
}

if ($_REQUEST['city_id'] == 'all') 
{
	$isFillter=true;
}
else
{
	if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="" && $_REQUEST['city_id']!=NULL)
	{
	 $ctable_where .= " AND id = '".$_REQUEST['city_id']."' ";
	 $isFillter=true;
	}
}

if ($_REQUEST['country_id'] == 'all') 
{
	$isFillter=true;
}
else
{
	if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="" && $_REQUEST['country_id']!=NULL)
	{
	 $ctable_where .= " AND country_id = '".$_REQUEST['country_id']."' ";
	 $isFillter=true;
	}	
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

// $ctable_r = $db->rp_getData("city","*",$ctable_where,"id DESC limit 50, 50",0);
$ctable_r = $db->rp_getData("city","*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_sales_executive_type" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control input-medium" name="country_id" id="country_id">
						<option value="">Select Country</option>
						<option value="all">all</option>
						<?php 
						$country_r=$db->rp_getData('country',"*","1=1 AND isDelete=0","",0);
						while($country_d=mysqli_fetch_assoc($country_r))
						{
							?>
						<!-- <option value="<?php echo $country_d['id']?>">
						<?php echo $country_d['name'];?>
						</option> -->

						<option <?php echo ($_REQUEST['country_id']==$country_d['id'])?"selected":"" ; ?> value="<?php echo $country_d['id']?>">
						<?php echo $country_d['name'];?>
						</option>
							<?php
						}
						?>
					</select>
        		</th>
        		<th>
        			<select class="form-control input-medium" name="class_id" id="class_id">
						<option value="">Select State</option>
						<option value="all">all</option>
						<?php 
						$c_id=$db->rp_getData('class',"*","1=1 AND isDelete=0","",0);
						while($class_r=mysqli_fetch_assoc($c_id))
						{
							?>
						<!-- <option value="<?php echo $class_r['id']?>">
						<?php echo $class_r['name'];?>
						</option> -->

						<option <?php echo ($_REQUEST['class_id']==$class_r['id'])?"selected":"" ; ?> value="<?php echo $class_r['id']?>">
						<?php echo $class_r['name'];?>
						</option>
							<?php
						}
						?>
					</select>
        		</th>
        		<th>
        			<select class="form-control input-medium" name="city_id" id="city_id">
						<option value="">Select State</option>
						<option value="all">all</option>
						<?php 
						$city_r=$db->rp_getData('city',"*","1=1 AND isDelete=0","",0);
						while($city_d=mysqli_fetch_assoc($city_r))
						{
							?>
						<option <?php echo ($_REQUEST['city_id']==$city_d['id'])?"selected":"" ; ?> value="<?php echo $city_d['id']?>">
						<?php echo $city_d['name'];?>
						</option>
							<?php
						}
						?>
					</select>
        		</th>
        		<th></th>
        	</tr>
            <tr>
                <th style="width: 5%;"></th>
                <th>No.</th>
                <th>Country Name</th>
                <th>State Name</th>
                <th>City Name</th>
                <th>Route</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if ($isFillter) 
        {
        	if($ctable_r)
	        {
	            $count = 0;
	            
	            while($ctable_d = mysqli_fetch_array($ctable_r)){
	                $count++;
	        ?>
	            <tr>

	            	<td>
					<?php $ctable_d['id']; 				
					if($rights['update_flag']==1)
					{
						?>
						<div class="btn-group">				
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
								<i class="fa fa-gear"></i>
							</button>
							<ul role="menu" class="dropdown-menu">
								<li>
									<a onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit">
										<span class="text-primary">
											<i class="fa fa-pencil"></i>
											&nbsp;Edit
										</span>
									</a>
								</li>
								<?php
								if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
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
								?>	
							</ul>
						</div>
						<?php
					}
					$country_id=$db->rp_getValue("class","country_id","id='".$ctable_d['state_id']."' AND isDelete=0",0);
					$country_name=$db->rp_getValue("country","name","id='".$country_id."' AND isDelete=0",0);
					// $state_name=$db->rp_getValue("class","name","id='".$ctable_d['class_id']."' AND isDelete=0",0);
					$state_name=$db->rp_getValue("class","name","id='".$ctable_d['state_id']."' AND isDelete=0",0);
					$country_isActive=$db->rp_getValue("country","isActive","id='".$country_id."' AND isDelete=0");

					?>
				</td>
	            	
	                <td><?php echo $count; ?></td>
					
	                <td><span class="<?php echo ($country_isActive==0)?"text-success":"text-success"; ?>"><?php echo stripslashes($country_name); ?></span></td>

	                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-success":"text-success"; ?>"><?php echo stripslashes($state_name); ?></span></td>
	                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-success":"text-success"; ?>"><?php echo stripslashes($ctable_d['name']); ?></span></td>
				    
					<td>
					<?php
					$area=$db->rp_getData("area","name","city_id='".$ctable_d['id']."' AND isDelete=0");
					
					while($area_d = mysqli_fetch_array($area)){
					?>
	                <span class="<?php echo ($ctable_d['isActive']==0)?"text-success":"text-success"; ?>"><?php echo $area_d['name']; ?></span>
				   <br/>
					<?php
					}
					?>
					</td>
					<!-- <td>
	                <a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
					<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
					<?php
					if($rights['update_flag']==1)
						{
							?> -->
							
					<!--div class="btn-group">
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
							 More
						</button>
						<ul role="menu" class="dropdown-menu">
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
							
						</ul>
					</div-->
							<!-- <?php
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
	    		<tr><td colspan="6" class="text-center"><h4><strong>Use filter for the show data of Area.</strong></h4></td></tr>
	    	<?php
	    }
        ?>
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-1">
			<div class="dataTables_info">
				<label >Rows Limit:</label>
				<select id="numRecords" class="form-control" onChange="changeDisplayRowCount(this.value);">
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
		<?php
		if ($isFillter) 
        {
		?>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
		<?php
		}
		?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<br>
			<!-- <?php
				echo $db->getAddButton($ctable);
			?> -->
		</div>
	</div>
</form>
<script type="text/javascript">
	$("#class_id").select2();
	$("#country_id").select2();
	$("#city_id").select2();
</script>
<?php require_once "disconnect.php"; ?>