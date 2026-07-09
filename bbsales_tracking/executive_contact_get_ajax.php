<?php
$page_id=400;$page_slug='dashboard';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable = "executive_contact_person";
$ctable1 = "Contact Persons";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " ((
						 name like '%".$_REQUEST['searchName']."%'					
						) 
						)
						AND ";
}
$cid=$_REQUEST['cid'];
if($cid!="")
{
	$ctable_where .= "cid='".$cid."'";
}

$ctable_where .= "AND isDelete=0";

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
	<table id="datatable_2" class="table table-striped table-bordered table-hover datatable">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Name</th>                						               						                     						               					
                <th>Designation</th>                						               						                     						               					
                <th>Branch</th>                						               						
                <th>Contact Info</th>                						               						
                         						               					
				<th>Action</th>
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
                <td><?php echo $count; ?></td>
                <td><?php echo stripslashes($ctable_d['name']); ?></td>				
                <td><?php echo stripslashes($ctable_d['designation']); ?></td>				
                <td><?php $branch_info=$db->getExecutiveBranchInfo($cid,$ctable_d['branch'],0); echo $branch_info['branch_name'] ?></td>				
                <td><?php echo '<i class="fa fa-phone"></i>&nbsp;'.stripslashes($ctable_d['phone']).'<br><i class="fa  fa-envelope"></i>&nbsp;'.stripslashes($ctable_d['email']); ?></td>				              		
                <td>
                <a class="btn btn-info btn-sm" onClick="editContact(<?php echo $ctable_d['id']; ?>)" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf_contact('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				
				
				
                </td>
            </tr>
        <?php
            }
        }
		else{
			?>
			
			<?php
		}
        ?>
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords2" onChange="changeDisplayRowCountContact(this.value);">
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