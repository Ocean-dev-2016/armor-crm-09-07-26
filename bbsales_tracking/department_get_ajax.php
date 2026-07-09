<?php
/*
 * @author Ravi Patel
 */
$page_id=550;$page_slug='page_department';
include("connect.php");
$ctable = "department";
$ctable1 = "Department";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							name like '%".$_REQUEST['searchName']."%'					
						) AND ";
}

$ctable_where .= " isDelete=0";

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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page");
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
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
            <tr>
                <th class="fix-th1" style="width: 5%;"></th>
                <th class="fix-th1" style="width: 5%;">No.</th>
                <th class="fix-th1">Department Name</th>
                <th class="fix-th1">Department Code</th>
                <!-- <th>Action</th> -->
			</tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
            <tr>
            	<td><?php $ctable_d['id']; ?>				
				<?php				
				if($rights['update_flag']==1)
				{
					?>
					<div class="btn-group">				
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
						<ul role="menu" class="dropdown-menu">
							<li>
								<a href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
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
                <td><?php echo stripslashes($ctable_d['name']); ?></td>
                <td><?php echo stripslashes($ctable_d['code']); ?></td>
			    <!-- <td>
                <a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				
				
                </td> -->
            </tr>
        <?php
            }
        }
		else{
			?>
			<tr>
			<td colspan="4"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			<?php
		}
        ?>
        </tbody>
    </table>
</div>
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
	<div class="row">
		<div class="col-md-12">
			<br>
			<!-- <?php
				echo $db->getAddButton("department");
			?> -->
		</div>
	</div>	
		
</form>
<?php require_once 'disconnect.php';  ?>