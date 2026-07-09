<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
$uid=$_REQUEST['uid'];
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							customer_name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR
							order_no like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
if($_REQUEST['uid'])
{
$dealer=$db->rp_getData("executive","id","super_stockist_id=".$_REQUEST['uid']." AND type_of_executive!='outlets'","",0);
$dealer_id=array();
while($dealer_d = mysqli_fetch_array($dealer))
{
	
	$dealer_id[]=array("customer_id"=>$dealer_d['id']);
	
}
//print_r($dealer_id);
}
/*if($_REQUEST['order_type'] && $_REQUEST['uid'])
		{
			 $ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND sales_id=".$d_id."";
		}*/


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= "  status = '".$_REQUEST['status']."' AND ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
		
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_dealer" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Order No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Sales Person Name</th>
                <th>Order Type</th>
                <th>Order Amount</th>
				<th>Order Date</th>
				<th>Status</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
		foreach($dealer_id as $d)
		{
			//print_r($d);
			
		$ctable_dealer = "isDelete=0 AND customer_id=".$d['customer_id']."";
		$ctable_r = $db->rp_getData($ctable,"*",$ctable_where.$ctable_dealer,"id DESC limit $page_position, $item_per_page",0);
		if($ctable_r){
          $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><a href="#myModal" target="_blank" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><?php echo stripslashes($ctable_d['order_no']); ?></a></td>
				<td><?php echo $db->rp_getValue('executive','company_name',"id=".$ctable_d['customer_id']."",0); ?></td>
                <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				<?php
					$sales_name=$db->rp_getValue("executive","cname","id='".$ctable_d['sales_id']."'");
				?>
                <td><?php
				if($sales_name=="")
				{
					echo "--";
				}
				else
				{
					echo $sales_name; 
				}
				?>
				</td>
                <td><?php echo stripslashes($ctable_d['customer_type']); ?></td>
                <td align="right"><?php echo stripslashes(CURR.$db->rp_num($ctable_d['grand_total'])); ?></td>
				<td>
					
					<?php echo date('d-m-Y',strtotime($ctable_d['order_date'])); ?>
				</td>
                <?php
                 if($ctable_d['status']==1)
                  {
                        $status="Completed";
                  }
                  else if($ctable_d['status']==0)
                  {
                       $status="Pending";
                  }
				  else if($ctable_d['status']==2)
                  {
                       $status="Dispatched";
                  }
				   else if($ctable_d['status']==3)
                  {
                       $status="Cancelled";
                  }
                  
                ?>
				<td><?php echo stripslashes($status); ?></td>
			
             <td>
			 <?php
			 /*if($rights['delete_flag']==1)
			{
						?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
			}
			if($rights['update_flag']==1)
			{
						?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='orders_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
			} */
					?>
                <div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Order</span></a>
							
						</li>
						<li>
							<a href="#ViewDispatchInfoModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Dispatch</span></a>
							
						</li>
					</ul>
				</div>
				<?php
				$file_path="order_generate.php?order_id=".$ctable_d['id']."";
				 if($rights['update_flag']==1)
				{
					?>
				<a class="btn btn-info btn-sm"  target="_blank" href="<?php echo $file_path;?>"  title="save"><i class="fa fa-file-pdf-o"></i>Download</a>
				<?php 
				}
				?>
				<a style="margin-top:5px;" class="btn btn-info btn-sm" href="dispatch_manage.php?id=<?php echo $ctable_d['id'];?>&order_no=<?php echo $ctable_d['order_no'];?>&flag=2"  title="save">Dispatch</a>
				
				</td>
            </tr>
        <?php
            }
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
<?php require_once "disconnect.php"; ?>