<?php
$page_id=590;$page_slug='add_to_cart_orders';
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
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
 $ctable_where .= " sales_id = '".$_REQUEST['sales_id']."' AND";
}
if(isset($_REQUEST['o_type']) && $_REQUEST['o_type']!="" && $_REQUEST['o_type']!=NULL)
{
 $ctable_where .= "customer_type = '".$_REQUEST['o_type']."' AND  ";
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
	$ctable_where .= " isDelete=0 AND status=-1";	
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
		$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND status=-1";
	}
	else{
		$ctable_where .= " isDelete=0 AND status=-1";
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
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL && $_REQUEST['ToDate']!=undefined)
{
  $ctable_where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL && $_REQUEST['FromDate']!=undefined)
{
     $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!="")
{
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(order_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(order_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
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
                <th></th>
                <th></th>
                <th></th>
                <th>
                	<label>Filter By Date</label>
					<div class="input-group">
						<input class="form-control datetimerange-picker-input" id="df" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
						<span class="input-group-addon datetimerange-picker-btn">
						<i class="fa fa-calendar"></i>
						</span>
									
					    <span class="input-group-btn">
							<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
						</span>
					</div>
					<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
				</th>        	
				<th></th>
       
                <th></th>
                <th>
                	<!-- Sales Person Name -->
                	<select class="form-control input-small" id="sales_id" name="sales_id">
	            		<option value="">Select Sales Person Name</option>
	                	<?php 
	                		$salesExe = $db->rp_getData("sales_executive","*","isDelete=0","",0);
	                		if($salesExe)
	                		{
	                		while ($salesD = mysqli_fetch_assoc($salesExe))
	                		{
	                	?>
	            			<option value="<?=$salesD['id']?>" <?=($salesD['id']==$_REQUEST['sales_id'])?"selected":"";?>><?=$salesD['name'];?></option>
	            		<?php } } ?>
	            	</select>
	            </th>
                <th>
	            	<select class="form-control input-small" id="o_type" name="o_type">
	            		<option value="">Select Customer Type</option>
	            		<?php
							$type_r = $db->rp_getData("customer_type", "id,name", "isDelete=0");
							if ($type_r) 
							{
								while ($type_d = mysqli_fetch_assoc($type_r))
								{
									?>
									<option value="<?= $type_d['id'] ?>" <?= ($type_d['id'] == $_REQUEST['o_type']) ? "selected" : ""; ?>><?= $type_d['name']; ?></option>
									<?php
								}
							}
							?>
	            	</select>
                </th>
				<th></th>
				<th></th>
				<th></th>
            </tr>
            <tr>
            	<th></th>
                <th>No.</th>
                <th>Order No.</th>
				<th>Order Date</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Sales Person Name</th>
                <th>Customer Type</th>
                <th style="text-align:right;">Order Amount</th>
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
           		
			$subtotal=$db->rp_getValue("order_product_item","SUM(totalprice)","order_id='".$ctable_d['id']."' AND isDelete=0");
			$pro_id=$db->rp_getValue("order_product_item","pro_id","order_id='".$ctable_d['id']."' AND isDelete=0");
			$GST=$db->rp_getValue("product","igst","id='".$pro_id."' AND isDelete=0");
			$gst_amount=($subtotal*$GST)/100;
			$grand_total=$subtotal+$gst_amount;
        ?>
            <tr>
            	<td>
                <?php $ctable_d['id'];              
                if($rights['delete_flag']==1)
                {
                    ?>
                    <div class="btn-group">             
                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                            <i class="fa fa-gear"></i>
                        </button>
                        <ul role="menu" class="dropdown-menu">
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
							<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
                                        <span class="text-danger">
                                            <i class="fa fa-times"></i>
                                            &nbsp;Delete
                                        </span>
                                    </a>
							<?php
							}
                                ?>
                               <!--  <li>
                                    <a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
                                        <span class="text-danger">
                                            <i class="fa fa-times"></i>
                                            &nbsp;Delete
                                        </span>
                                    </a>
                                </li> -->
                                <?php
                            }
                            ?>  
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </td>
				<td><?php echo ++$count; ?></td>
				<td><!-- <a href="#myModal" target="_blank" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"> --><span class="text-success"><?php echo stripslashes($ctable_d['order_no']); ?><!-- </a> --></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['order_date'])); ?></td>
				<td><?php echo $ctable_d['company_name']; ?></td>
				<td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				<?php
					$sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'");
				?>
				<td><?php if($sales_name=="")
				{
					echo "--";
				}
				else
				{ 
					echo $sales_name;
				}
				?></td>
				<td><?php 
				if($ctable_d['customer_type']=='1')
				{
					$slug="Super Stockist";
				}
				else if($ctable_d['customer_type']=='2')
				{
					$slug="Distributor";
				}
				else if($ctable_d['customer_type']=='3')
				{
					$slug="Dealer";
				}
				else if($ctable_d['customer_type']=='4')
				{
					$slug="B2B Customer";
				}
				else if($ctable_d['customer_type']=='normal_user')
				{
					$slug="Normal Customer";
				}
				echo stripslashes($slug); ?></td>
				<td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($grand_total))); ?></td>
               
				<td>Added to Cart</td>
				<td>
				 <?php
				//  if($rights['delete_flag']==1)
				// {
							// $total_dispatch_record=$db->rp_getTotalRecord("dispatch_map_order","order_id='".$ctable_d['id']."' AND isDelete=0");
							// if($total_dispatch_record>0)
							// {
							// }
							// else
							// {
								?>
							<!-- <a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a> -->
							<?php
				// 			}
				// }
				if($rights['update_flag']==1)
				{
					if($ctable_d['customer_type']=='dealer')
					{
					?>
					<!-- <a class="btn btn-info btn-sm" onClick="window.location.href='orders_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>&flag=<?php echo $_REQUEST['flag']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a> -->
					<?php 	
					}
					else if($ctable_d['customer_type']=='super_stockist')
					{
					?>
					<!-- <a class="btn btn-info btn-sm" onClick="window.location.href='orders_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>&flag=<?php echo $_REQUEST['flag']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a> -->
					<?php 	
					}
				}
						?>               
					<?php
					//$file_path="order_generate.php?order_id=".$ctable_d['id']."";
					$file_path="order_viewer.php?order_id=".$ctable_d['id']."";
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
				
             <!-- <td>
			 <?php
			 if($rights['delete_flag']==1)
			 { ?>
					<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
					<?php
			 }
			 ?>
                <div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View Order</span></a>
						
						<?php
						$file_path="customer_orders_viewer.php?order_id=".$ctable_d['id']."";
						 if($rights['update_flag']==1)
						{
							?>
						</li>
						<li>
							<a href="<?php echo $file_path; ?>" title="Download"><span class="text-success"><i class="fa fa-file-pdf-o"></i>&nbsp; Download</span></a>
							
						</li>
						<?php } ?>
					</ul>
				</div>
				
				</td> -->
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
</form>

<script type="text/javascript">
	$("#sales_id").select2();
	$("#o_type").select2();
	$("#df1").select2();
</script>
<script type="text/javascript">
	$(".filterBtn").on("click",function()
	{
		df1=$("#df").val();
		df1 = encodeURI(df1)
		displayRecords(100,1);
	})
    $(".datetimerange-picker-btn").on("click",function(){
        $(".datetimerange-picker-input",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<?php require_once "disconnect.php"; ?>