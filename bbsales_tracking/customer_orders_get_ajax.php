<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
$uid=isset($_REQUEST['uid'])?$_REQUEST['uid']:"";
$order_type=isset($_REQUEST['order_type'])?$_REQUEST['order_type']:"";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							customer_name like '%".$db->clean($_REQUEST['searchName'])."%' OR
							company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR
							order_no like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!=""){
	$ctable_where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
}
if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!=""){
	$ctable_where .= "  customer_type = '".$db->clean($_REQUEST['customer_type'])."' AND";
}

//for admin login
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	/*if($_REQUEST['order_type'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else
	{*/
	$ctable_where .= " isDelete=0 AND customer_type='normal_user'";	
	//}
	
}
// for customer login
else
{
	if($_REQUEST['order_type'] && $_REQUEST['uid'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
	}
	else if($_REQUEST['order_type'])
	{
		$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else{
		$ctable_where .= " isDelete=0 AND customer_type='normal_user'";
	}
}

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
 $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $ctable_where .= " AND customer_type = '".$_REQUEST['type']."' ";
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
                <th>Order No.</th>
                <th>Pro Invoice No.</th>
                <th>Request No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Order Type</th>
                <th style="text-align:right;">Order Amount</th>
				<th>Order Date</th>
				<th>Status</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
           		$r['pro_forma_invoice_no'] = $db->rp_getValue("proforma_invoice_info","invoice_no","id='".$ctable_d['proforma_invoice_id']."'",0);
				 $request_id = $db->rp_getValue("proforma_invoice_info","request_id","id='".$ctable_d['proforma_invoice_id']."'",0);
				 $r['request_no'] = $db->rp_getValue("customer_order_request_info","request_no","id='".$request_id."'",0);
			
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><a href="#myModal" target="_blank" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><?php echo stripslashes($ctable_d['order_no']); ?></a></td>
                <td><?php echo $r['pro_forma_invoice_no']; ?></td>
                <td><?php echo $r['request_no']; ?></td>
                <td><?php echo $ctable_d['company_name']; ?></td>
                <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				
                <td><?php 
				if($ctable_d['customer_type']=='dealer')
				{
					$slug="Dealer";
				}
				else if($ctable_d['customer_type']=='super_stockist')
				{
					$slug="Super Stockist";
				}
				else if($ctable_d['customer_type']=='outlets')
				{
					$slug="Outlet";
				}
				else if($ctable_d['customer_type']=='normal_user')
				{
					$slug="Normal Customer";
				}
				echo stripslashes($slug); ?></td>
                <td align="right"><?php echo stripslashes(CURR.$db->rp_number_format($ctable_d['grand_total_rounded'],2)); ?></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['order_date'])); ?></td>
                <?php
                  if($ctable_d['status']==1)
                  {
                        $status="Completed";
                  }
                  else if($ctable_d['status']==0)
                  {
                      $status="Pending";
                  }
				  else if($ctable_d['status']==3){
					  $status="Cancelled <br><b>Reason For Cancel</b><br/><span class='text-danger'>".$ctable_d['reason_of_cancel_order']."</span>";
				  }
				  else if($ctable_d['status']==2)
                  {
                       $status="Dispatched";
                  }
				   
                  
                ?>
				<td><?php echo stripslashes($status); ?></td>
				
             <td>
			 <?php
			 if($rights['delete_flag']==1)
			{
						$total_dispatch_record=$db->rp_getTotalRecord("dispatch_map_order","order_id='".$ctable_d['id']."' AND isDelete=0");
						if($total_dispatch_record>0)
						{
						}
						else
						{
							?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
						}
			}
			if($rights['update_flag']==1)
			{
				if($ctable_d['customer_type']=='dealer')
				{
				?>
				<a class="btn btn-info btn-sm" onClick="window.location.href='orders_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>&flag=<?php echo $_REQUEST['flag']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<?php 	
				}
				else if($ctable_d['customer_type']=='super_stockist')
				{
				?>
				<a class="btn btn-info btn-sm" onClick="window.location.href='orders_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>&flag=<?php echo $_REQUEST['flag']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<?php 	
				}
			}
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
							<a href="#ViewDispatchInfoModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Dispatch"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Dispatch</span></a>
							
						</li>
						
					</ul>
				</div>
				
				<?php
				//$file_path="customer_orders_generate.php?order_id=".$ctable_d['id']."";
				$file_path="customer_orders_viewer.php?order_id=".$ctable_d['id']."";
				 if($rights['update_flag']==1)
				{
					?>
				<div class="" style="margin-top:10px;">
				<a  class="btn btn-info btn-sm" target="_blank" href="<?php echo $file_path;?>"  title="save"><i class="fa fa-file-pdf-o"></i>Download</a>
				
				<?php 
				}
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					if($ctable_d['status']!=2)
					{
					
					?>
					<!--a  class="btn btn-info btn-sm" href="dispatch_manage.php?id=<?php echo $ctable_d['id'];?>&flag=<?php echo $_REQUEST['flag'];?>&order_no=<?php echo $ctable_d['order_no'];?>"  title="save">Dispatch</a-->
					<?php
					
					}
				}
				else
				{
					if($ctable_d['status']!=2)
					{
					
					?>
					<!--a  class="btn btn-info btn-sm" href="dispatch_manage.php?id=<?php echo $ctable_d['id'];?>&flag=<?php echo $_REQUEST['flag'];?>&order_no=<?php echo $ctable_d['order_no'];?>"  title="save">Dispatch</a-->
					<?php
					
					}
				}
				?>
				<!--a  class="btn btn-info" style="margin-top:10px;" href="dispatch_manage.php?id=<?php echo $ctable_d['id'];?>&flag=<?php echo $_REQUEST['flag'];?>"  title="save">Payment</a-->
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
<?php require_once "disconnect.php"; ?>