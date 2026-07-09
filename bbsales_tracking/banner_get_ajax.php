<?php
$page_id=574;$page_slug='banner';
include("connect.php");
$ctable = "promotion";
$ctable1 = "banner";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
$ctable_where .= " (
				title like '%".$_REQUEST['searchName']."%'						
			) AND ";
}
$ctable_where .= " promo_type=1 AND isDelete=0";

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

	 if($rights['personal_flag']==1)
	 {

	 	$ctable_where .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";

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
					
						$ctable_where .= " AND created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";	
					
					
				}
				else
				{
						$ctable_where .= " AND  created_by IN (".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'].")";		
				}
		}
		else
		{
			$ctable_where .= " ";
		}
	}
  
}
else
{

	$ctable_where .= " ";

}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"display_order ASC limit $page_position, $item_per_page",0);

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


<form action="banner_manage.php" name="frm" id="frm" method="post">
	<div class="row">
		<div class="col-md-8">
			<?php
				if($rights['insert_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
				{ 
			?>
					<div class="btn-group">
						<a id="add_banner" href="banner_crud.php?mode=add" class="btn sbold blue-ebonyclay"> Add New
							<i class="fa fa-plus"></i>
						</a>
					</div>
			<?php
				}
			?>
			
			<br/>
			<br/>
		</div>

		<!-- <div style="margin-left: -350px;" class="col-md-2">
			<div class="btn-group">
				<input type="hidden" name="disp_count" value="<?php echo $count; ?>">
				<button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button>
			</div>
		</div> -->

	</div>
	<div class="table-scrollable">
	<table id="datatable_sales_executive_type" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
        	<!-- <tr>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th><div class="btn-group">
				<input type="hidden" name="disp_count" value="<?php echo $count; ?>">
				<button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button>
			</div></th>
        		<th></th>
        		<th></th>
        	</tr> -->
            <tr>
				<th class="fix-th1" style="width: 5%;"></th>
                <th class="fix-th1">No.</th>
				<th class="fix-th1">Banner</th>
				<th class="fix-th1">Display Order</th>
				<th class="fix-th1">Class</th>
				<th class="fix-th1">Area</th>
			</tr>
        </thead>
        <tbody>
        <?php
		if(mysqli_num_rows($ctable_r)>0){
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
								<a href="banner_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
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
				?>
			</td>
				<td><?php echo $count; ?></td>
				<td>
				<?php
				 if($ctable_d['image_path']!="" && file_exists(BANNER_A.$ctable_d['image_path'])){
					?>
					<img src="<?php echo BANNER_A.$ctable_d['image_path']; ?>" width="50" />
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
				</td>
				<td align="center">
					<!-- <input type="text" name="disp<?php echo $count; ?>" id="disp<?php echo $count; ?>" value="<?php echo $ctable_d['display_order']; ?>" style="width:40px;text-align:center" onChange="CheckDispalyOrder('<?php echo $ctable_d['id'];?>');"> -->
				<!-- <input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>"> -->

				<input type="text" name="disp<?php echo $count; ?>" id="disp<?php echo $ctable_d['id']; ?>" value="<?php echo $ctable_d['display_order']; ?>" data-size-id="<?php echo $ctable_d['id'];?>" style="width:40px;text-align:center" onChange="CheckDispalyOrder('<?php echo $ctable_d['id'];?>');">
				<input type="hidden" name="b_id<?php echo $count ?>" value="<?php echo $ctable_d['id']; ?>">
				</td>
				<td><?php echo $db->rp_getValue("class","name","id='".$ctable_d['class_id']."'",0); ?></td>
				<td><?php $area_id  = explode(",",$ctable_d['area_id']);
				 		foreach ($area_id as $area) {
				 		echo $db->rp_getValue("area","name","id='".$area."'",0);
				 		echo ",";
				 	} ?></td>
				<!-- <td>
				<a class="btn btn-info btn-sm" onClick="window.location.href='banner_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				</td> -->
			</tr>
		<?php
			}
		}
		?>
        </tbody>
    </table>
</div>
    <div class="row">
		<div class="col-md-8 pull-left">
			<!-- <div class="btn-group">
				<a id="add_banner" href="banner_crud.php?mode=a" class="btn sbold blue-ebonyclay"> Add New
					<i class="fa fa-plus"></i>
				</a>
			</div> -->

			
		</div>

		<div style="margin-left: -95px;" class="col-md-4">
			<div class="btn-group">
				<input type="hidden" name="disp_count" value="<?php echo $count; ?>">
				<!-- <button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button> -->
			</div>
		</div>

		<div class="col-md-1 pull-left">
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
		<div class="col-md-6 ">
			<div class="dataTables_paginate paging_simple_numbers ">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
	<br/>
</form>
 <?php require_once("disconnect.php"); ?>

