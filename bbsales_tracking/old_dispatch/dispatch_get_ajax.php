<?php
$page_id=569;$page_slug='dispatch_pages';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "dispatch_detail";
$ctable1 	= "Dispatch_detail";
$order_id=$_REQUEST['id'];
$ctable_where = "";
$flag=$_REQUEST['flag']; 
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							customer_name like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
//for admin login


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
$ctable_where .="order_id='".$order_id."' AND isDelete=0";
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND dispatch_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND dispatch_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $ctable_where .= " AND order_type = '".$_REQUEST['type']."' ";
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
                <th>No</th>
                <th>Dispatch No</th>
                <th>Order No</th>
                <th style="text-align:right;">Dispatch Qty</th>
				<th style="text-align:right;">Amount</th>
                <th>Customer Name</th>
                <th>Sales Person Name</th>
                <th>Order Type</th>
				<th>dispatch Date</th>
				<th>Remark</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
           $customer=$db->rp_getValue('executive','isActive',"id=".$ctable_d['customer_id']."",0);
			if($customer==0)
			{
				continue;
			}
			
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo stripslashes($ctable_d['dispatch_no']); ?></td>
				<td><?php
					$order_no=$db->rp_getValue("orders","order_no","id='".$ctable_d['order_id']."'");
					echo $order_no; ?>
				</td>
                <td align="right"><?php echo stripslashes($ctable_d['dispatch_qty']); ?></td>
                <td align="right"><?php echo stripslashes($db->rp_num($ctable_d['grand_total'])); ?></td>
                <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
                <td><?php 
				if($ctable_d['sales_name']=="")
				{
					echo "--";
				}
				else
				{
				echo stripslashes($ctable_d['sales_name']); ?></td>
				<?php
				}
				?>
                <td><?php echo stripslashes($ctable_d['order_type']); ?></td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['dispatch_date'])); ?></td>
               <td><?php echo stripslashes($ctable_d['remark']); ?></td> 
             <td>
			  
			 <?php
			 if($rights['delete_flag']==1)
			{
						?>
						<a class="btn btn-danger btn-sm" style="margin-top:10px;" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
			}
					?>
					
                <div class="btn-group">
					<button aria-expanded="false" style="margin-top:10px;" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a href="#myDispatchModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Dispatch</span></a>
							
						</li>
						<li>
							<a href="#DispatchPaymentModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Payment Dispatch"><span class="text-success"><i class="fa fa-circle"></i>&nbsp;View Payment</span></a>
							
						</li>
						
					</ul>
				</div>
				<?php
				$file_path="dispatch_generate.php?dispatch_id=".$ctable_d['id']."";
				 if($rights['update_flag']==1)
				{
					?>
				<div class="" style="margin-top:10px;">
				<a  class="btn btn-info btn-sm" target="_blank" href="<?php echo $file_path;?>"  title="save"><i class="fa fa-file-pdf-o"></i>Download</a>
				
				<?php 
				}
				?>
				
				</div>
				<?php
				$file_path="dispatch_generate.php?dispatch_id=".$ctable_d['id']."";
				 if($ctable_d['LR_copy']!="")
				{
					?>
				<div class="" style="margin-top:10px;">
				<a  class="btn btn-info btn-sm" target="_blank" href="<?php echo LRCOPY_DOCUMENTS.$ctable_d['LR_copy'];?>" download title="save"><i class="fa fa-file-pdf-o"></i>LR Copy</a>
				
				<?php 
				}
				?>
				
				</div>
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