<?php
$page_id=554;$page_slug='page_employee';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "emp_salary_info";
$etable		="employee";
$ctable1 	= "Employee Salary";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							cname like '%".$db->clean($_REQUEST['searchName'])."%'
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

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);
$ctable_where = "emp_id='".$_REQUEST['eid']."'";
$ctable_where .= " AND isDelete=0";
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Year</th>
				<th>Earning</th>
				<th>Deduction</th>
				<th>Net Payable</th>				
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                
				$earning=$ctable_d['hra']+$ctable_d['basic']+$ctable_d['medical']+$ctable_d['conv']+$ctable_d['wash']+$ctable_d['edu']+$ctable_d['lt']+$ctable_d['spe']+$ctable_d['gross'];
				$deduction=$ctable_d['it']+$ctable_d['pt']+$ctable_d['pf'];
        ?>
            <tr>
                <td><?php echo ++$count;?></td>
                <td><?php echo stripslashes($ctable_d['year']); ?></td>
				<td><?php echo $earning; ?></td>
				<td><?php echo $deduction; ?></td>
				<td><?php echo stripslashes($ctable_d['net_payable']); ?></td>
			
                <td>
                <?php 				
					if($rights['update_flag']==1)
					{
						?>
						<a class="btn btn-info btn-sm" onClick="editSalary(<?php echo $ctable_d['id']?>,<?php echo $ctable_d['emp_id']?>)" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php
					}
					if($rights['delete_flag']==1)
					{
						?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
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