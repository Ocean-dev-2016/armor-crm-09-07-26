<?php
$page_id=575;$page_slug='payment';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "customer_payment";
$ctable1 	= "Customer Payment";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$customer_r=$db->rp_getData("dispatch_detail","DISTINCT customer_id","customer_name like '%".$_REQUEST['searchName']."%'","",0);
	$cust_id=array();
	while($customer_d=mysqli_fetch_assoc($customer_r))
	{
		$cust_id[]=$customer_d['customer_id'];
	}
	$cust_id=implode(",",$cust_id);
	$ctable_where .="customer_id IN (".$cust_id.") AND";
}

	$user=$db->rp_getData("customer","id","","",0);
	while($user_d = mysqli_fetch_array($user))
	{
		$c_id[]=array("customer_id"=>$user_d['id']);
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

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $ctable_where .= " AND payment_type = '".$_REQUEST['type']."' ";
}
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

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
                <th>Dispatch Bill No.</th>
                <th>Order No.</th>
                <th>Receipt No.</th>
				<th>Payment by</th>	
				<th>Payment Date</th>
				<th style="text-align:right;">Payment Amount</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
		foreach($c_id as $c)
		{
		$ctable_type = " AND customer_id=".$c['customer_id']."";
		$ctable_r = $db->rp_getData($ctable,"*",$ctable_where.$ctable_type,"id DESC limit $page_position, $item_per_page",0);
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $company_name=$db->rp_getValue("customer","company_name","id='".$ctable_d['customer_id']."'",0); ?></span></td>
                <td>
				<?php				
				$company_name=$db->rp_getValue("customer","name","id='".$ctable_d['customer_id']."'");
				?>
				<?php echo stripslashes($company_name); ?></td>
				<td><?php
					$dispatch_bill_no=$db->rp_getValue("dispatch_detail","dispatch_no","id='".$ctable_d['dispatch_id']."'");
					echo $dispatch_bill_no; ?>
				</td>
				<td><?php
					$order_no=$db->rp_getValue("orders","order_no","id='".$ctable_d['order_id']."'");
					echo $order_no; ?>
				</td>
				<td><?php echo stripslashes($ctable_d['receipt_no']); ?></td>
				<td><?php echo $ctable_d['payment_type']; ?></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
				<td align="right"><?php echo stripslashes(CURR.($db->rp_num($ctable_d['paid_amount']))); ?></td>
				<td>				
				<?php 
					if($rights['update_flag']==1)
					{
						?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='customer_payment_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
					}
					if($rights['delete_flag']==1)
					{
						?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
					}
					if($rights['update_flag']==1)
					{
						?>
						
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
				<?php 
				$file_path="bill_generate.php?id=".$ctable_d['id']."";
				?>
				<a href="<?php echo $file_path;?>"  target="_blank" class="btn btn-info btn-sm" title="save"><i class="fa fa-file-pdf-o"></i>Download</a>
						<?php
					}
					?>
					
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
<?php require_once 'disconnect.php';  ?>