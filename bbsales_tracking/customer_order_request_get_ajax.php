<?php
$page_id=436;$page_slug='page_pro_forma';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable = "customer_order_request_info";
$ctable1 = "Customer Order Request Info";
$status=array("0"=>"Generated","1"=>"Pending","2"=>"Completed");
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							customer_name like '%".$_REQUEST['searchName']."%'	OR				
							request_no like '%".$_REQUEST['searchName']."%'OR				
							company_name like '%".$_REQUEST['searchName']."%'					
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
                <th>Request No.</th>
                 <th>Company Name</th>
                <th>Customer Name</th>
               
                <th>Phone No.</th>
                <th>Email</th>
                <th>City</th>
                <th>Status</th>
                <th>Action</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
				/* if($ctable_d['pro_forma_invoice_date']=='0000-00-00')
				{
					$invoice_date='';
				}else{
					
					$invoice_date=date("d-m-Y",strtotime($ctable_d['pro_forma_invoice_date']));
				}
			 */
        ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><span class="text-success"><?php echo stripslashes($ctable_d['request_no']); ?></span></td>
                <td><?php echo stripslashes($ctable_d['company_name']); ?></td>
				<td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo stripslashes($ctable_d['email']); ?></td>
				<td><?php echo stripslashes($ctable_d['city']); ?></td>
				<td><?php echo stripslashes($status[$ctable_d['status']]); ?></td>
			    <td>
              <a class="btn btn-info btn-sm hidden" onClick="window.location.href='customer_order_request_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				
				<?php
				//$file_path="customer_order_generate.php?request_id=".$ctable_d['id']."";
				 $file_path="customer_order_viewer.php?request_id=".$ctable_d['id']."";?>
				
				<a  class="btn btn-info btn-sm" target="_blank" href="<?php echo $file_path;?>"  title="save"><i class="fa fa-file-pdf-o"></i>Download</a>
				
                </td>
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
<?php require_once "disconnect.php"; ?>