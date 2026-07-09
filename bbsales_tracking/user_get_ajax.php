<?php
$page_id=573;$page_slug='page_vendor';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "customer";
$ctable1 	= "User";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR company_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}

$ctable_where .= " isDelete=0";

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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
				<th>Email</th>
				<th>Phone</th>				
				<th>Country</th>				
				<th>State</th>				
				<th>City</th>	
				<th>Pincode</th>	
				<th>Action</th>	
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['company_name']); ?></span></td>
                <td><?php echo stripslashes($ctable_d['name']); ?></td>
                
				<td><?php echo stripslashes($ctable_d['email']); ?></td>
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo stripslashes($ctable_d['country']); ?></td>
				<td><?php echo stripslashes($ctable_d['state']); ?></td>
				<td><?php echo $ctable_d['city']; ?></td>
				<td><?php echo $ctable_d['zip']; ?></td>
				<td>
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
										<a  href="user_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
									<?php
									}else{
									?>
										<a  href="user_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger" ><i class="fa fa-circle-o"></i> &nbsp; Deactivate </span></a>
									<?php
									}
									?>
								</li>
								
							</ul>
						</div>
								<?php
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
<?php require_once 'disconnect.php';  ?>