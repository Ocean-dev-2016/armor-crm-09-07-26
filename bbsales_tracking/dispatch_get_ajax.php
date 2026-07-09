<?php
$page_id=569;$page_slug='dispatch_pages';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "dispatch_detail";
$ctable1 	= "Dispatch_detail";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	// echo $_REQUEST['searchName']; exit;
	$ctable_where .= " (sales_name like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%' OR dispatch_no like '%".$db->clean($_REQUEST['searchName'])."%' OR order_no like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
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
$ctable_where .="isDelete=0";

if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 	$ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}

// ///For ToDate & FromDate
// if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
// {
//   	$ctable_where .= " AND dispatch_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
// }

// if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
// {
//      $ctable_where .= " AND dispatch_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
// }
if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined) {
	$ctable_where .= " AND dispatch_date >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined) {
	$ctable_where .= " AND dispatch_date <= '" .$_REQUEST['fromdate']. "' ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 	$ctable_where .= " AND order_type = '".$_REQUEST['type']."' ";
}

if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="")
{
	$ctable_where .= " AND sales_id = '".$db->clean($_REQUEST['sales_id'])."' ";
}

if(isset($_REQUEST['company_name']) && $_REQUEST['company_name']!="")
{
	$ctable_where .= " AND customer_id = '".$db->clean($_REQUEST['company_name'])."' ";
}

if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
{
    //echo $_REQUEST['df1'];exit;
    $date_filter_query = urldecode( $_REQUEST['df1'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $ctable_where .= " AND ( DATE(dispatch_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(dispatch_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

if (isset($_REQUEST['dispatch_month']) && $_REQUEST['dispatch_month'] != "" && $_REQUEST['dispatch_month'] != NULL) {
	$ctable_where .= " AND MONTH(dispatch_date) = '".$_REQUEST['dispatch_month']."'";
}

if (isset($_REQUEST['dispatch_year']) && $_REQUEST['dispatch_year'] != "" && $_REQUEST['dispatch_year'] != NULL) {
	$ctable_where .= " AND YEAR(dispatch_date) = '".$_REQUEST['dispatch_year']."'";
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL && $_REQUEST['customer_id']!="undefined") {
	$ctable_where .= " AND customer_id = '".$_REQUEST['customer_id']."'";
}

/*if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
	$ctable_where .= " ";
} else {
	$ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
}*/

// if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0 || $rights['all_data_flag'] == 1) {
// 	$ctable_where .= "";
// } 
// else if($rights['chain_vise_flag'] == 1)
// {
	
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

//     $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
//     if ($get_sales_type== "sales_manager") 
//     {
//         $sales_executive_type = "Regional Sales Manager";
//         $key="sm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }

//     else if ($get_sales_type == "area_sales_manager") 
//     {
//         $sales_executive_type = "National Sales Manager";//Business Development Manager
//         $key="asm_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }

//     else if ($get_sales_type == "sales_officer") 
//     {
//         $sales_executive_type = "Area Sales Manager";//Area Sales Manager
//         $key="so_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }
//     else if ($get_sales_type == "sales_executive") 
//     {
//         $sales_executive_type = "Sales Officer";
//         $key="se_id";
//         $WhereCondition.=' ' .$key.'='.$check_id;
//     }
//     else
//     {
//     	$WhereCondition.=' type = "service_engineer"';
//     }

//     $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

//     $SALEID1=array();
// 	if($data)
// 	{
// 		while($data_d=mysqli_fetch_assoc($data))
// 		{
// 			$SALEID1[]=$data_d['id'];
// 		}
// 	}
// 	if(!empty($SALEID1))
// 	{
// 		$SALEID1=implode(",", $SALEID1);
		
// 			$ctable_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
		
		
// 	}
// 	else
// 	{
// 			$ctable_where .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
// 	}
// }
// else 
// {
// 	$ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
// }






if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

	 
	 if($rights['personal_flag']==1)
	 {

	 	$ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";

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
					
						$ctable_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					
					
				}
				else
				{
						$ctable_where .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
				}
		}
		else
		{
			
		}
	}
  
}


// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
// 	if($rights['personal_flag']==1)
// 	{
// 		$ctable_where .=" AND created_by ='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ";
// 		$customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
// 		$filter_where .=" AND id IN (".$customer_type.") ";
// 	}
// 	else
// 	{
// 		if($rights['all_data_flag']==1)
// 		{
			
// 		}
// 		else
// 		{
// 			$customer_type=$db->rp_getValue("sales_executive","customer_type","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
// 			//$CustomerType = implode(",", $customer_type);
// 		    $Order_IDS_r=$db->rp_getData("orders","*","customer_type IN (".$customer_type.") ","",0);
// 			$ORDER_IDS = array();
// 			while($Order_IDS_d = mysqli_fetch_array($Order_IDS_r))
// 			{
// 				$ORDER_IDS[] = $Order_IDS_d['id'];
// 			}
// 			$order_ids= implode(",", $ORDER_IDS);
// 			// print_r($order_ids);exit;
// 		    $ctable_where .=" AND order_id IN (".$order_ids.")  ";
// 		}	
// 	}
// }

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style type="text/css">
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
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
        	<tr>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<!-- <th></th>
        		<th></th> -->
        		<th>
        			<select class="form-control input-small" id="company_name">
	            		<option value="">Select Company Name</option>
	                	<?php 
	                	$com_name_r = $db->rp_getData("executive","*","isDelete=0","",0);
	                	if($com_name_r)
                		{
	                		while ($com_name_d = mysqli_fetch_assoc($com_name_r))
	                		{
	                			?>
	            				<option value="<?=$com_name_d['id']?>" <?=($com_name_d['id']==$_REQUEST['company_name'])?"selected":"";?>><?=$com_name_d['company_name'];?></option>
	            				<?php 
	            			} 
	            		} 
	            		?>
		            </select>
        		</th>
        		<th>
        			<?php
					if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                	{ 
						?>
	                	<select class="form-control input-small" id="sales_id">
		            		<option value="">Select Sales Person Name</option>
		                	<?php 
		                	$salesExe = $db->rp_getData("sales_executive","*","isDelete=0","",0);
		                	if($salesExe)
	                		{
		                		while ($salesD = mysqli_fetch_assoc($salesExe))
		                		{
		                			?>
		            				<option value="<?=$salesD['id']?>" <?=($salesD['id']==$_REQUEST['sales_id'])?"selected":"";?>><?=$salesD['name'];?></option>
		            				<?php 
		            			} 
		            		} 
		            		?>
		            	</select>
		            	<?php 
		        	}
		        	?>
        		</th>
        		<th></th>
        		<th>
        			<label>Filter By Date</label>
                    <div class="input-group">
                        <input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
                        <span class="input-group-addon datetimerange-picker-btn">
                        <i class="fa fa-calendar"></i>
                        </span>
                                    
                        <span class="input-group-btn">
                            <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
                        </span>
                    </div>
        		</th>
        		<th>
        			<select class="form-control" id="status">
                        <option value="">Select Status</option>
                        <option value="0" <?=("0"==$_REQUEST['status'])?"selected":"";?>>Pending</option>
                        <option value="1" <?=("1"==$_REQUEST['status'])?"selected":"";?>>Complete</option>
                        <option value="2" <?=("2"==$_REQUEST['status'])?"selected":"";?>>Packing Slip Created</option>
                    </select>
        		</th>
        	</tr>
            <tr>
				<th class="fix-th1"></th>
                <th class="fix-th1">No</th>
                <th class="fix-th1">Dispatch No</th>
                <th class="fix-th1">Order No</th>
                <th class="fix-th1" style="text-align:right;">Dispatch Qty</th>
				<!-- <th style="text-align:right;">Amount</th> -->
				<!-- <th style="text-align:right;">Without GST Amount</th>
				<th style="text-align:right;">GST Amount</th> -->
				<th class="fix-th1" style="text-align:right;">Transport Charge</th>
                <th class="fix-th1">Company Name</th>
                <th class="fix-th1">Sales Person Name</th>
                <th class="fix-th1">Order Type</th>
				<th class="fix-th1">Dispatch Date</th>
				<th class="fix-th1">Status</th>
				<th class="fix-th1">LR Attachment</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r)
        {
            $count = 0;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r))
            {
            	$lr_image_path = $db->rp_getValue("lr_detail","image_path","isDelete=0 AND dispatch_no='".$ctable_d['id']."'",0);

            	if($lr_image_path!="")
                {
                    $lr_image = ADMINSITEURL.LRCOPY_A.$lr_image_path;
                } 
                else
                {
                    $lr_image = "";
                }


            	$dispatch_status_array = array("0"=>"Pending","1"=>"Complete","2"=>"Packing Slip Created");
         		$gst_amount = ($ctable_d['grand_total']*18)/100;
         		$invoice_dispatch_ids = $db->rp_getValue("invoice_new","dispatch_ids","dispatch_ids='".$ctable_d['id']."' AND isDelete=0",0);
         		//if($ctable_d['id']==$invoice_dispatch_ids)
         		if ($ctable_d['status'] == 1) 
         		{
         			$style = "style='background-color: #cef5c7;'";
         		}
         		else if ($ctable_d['status'] == 0) 
				{
					$style = "style='background-color: #add8e6;'";
				}
         		else
         		{
         			$style = "";
         		}
         		?>
            	<tr <?=$style?>>
            		<td>
						<?php 
						$ctable_d['id']; 				
						if($rights['update_flag']==1)
						{
							?>
							<div class="btn-group">				
								<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
									<i class="fa fa-gear"></i>
								</button>
								<ul role="menu" class="dropdown-menu">
									<?php
									/*if($rights['delete_flag']==1 && $ctable_d['id']==0)*/
									if($rights['delete_flag']==1 && $ctable_d['cash_payment']==0)
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
									<!-- <li>
										<a href="#myDispatchModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer">
											<span class="text-success">
												<i class="fa fa-circle"></i>
												&nbsp; View Dispatch
											</span>
										</a>
									</li>
									<li>
										<a href="#DispatchPaymentModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Payment Dispatch">
											<span class="text-success">
												<i class="fa fa-circle"></i>
												&nbsp;View Payment
											</span>
										</a>	
									</li> -->
									<li>
										<?php
										$file_path="view_dispatch.php?id=".$ctable_d['id'];
										if($rights['update_flag']==1)
										{
											?>
											<a target="_blank" href="<?php echo $file_path;?>"  title="save">
												<span class="text-success">
													<i class="fa fa-file-pdf-o"></i>
													&nbsp;Download
												</span>
											</a>
											<?php 
										}
										?>
									</li>
									<li>
										<?php
										if($ctable_d['cash_payment']==0)
										{
										?>
										<a onClick="cash_payment('<?php echo $ctable_d['id']; ?>');" title="Manual Close">
											<span>
												<i class="fa fa-times"></i>
												&nbsp;Manual Close
											</span>
										</a>
										<?php
										}
										?>
									</li>
									<li>
										<?php
										if($ctable_d['status']==0)
										{
											?>
											<!-- <a class="" target="_blank" href="packing_slip_crud.php?mode=add&dispatch_id=<?php echo $ctable_d['id'] ?>" title="save"><i class="fa fa-file-pdf-o"></i>  Packing Slip</a> -->
										<!-- 	<a onclick="GeneratePackingSlip('<?= $ctable_d['id'] ?>')" title="Generate Packing Slip"><span class="text-warning"><i class="fa fa-shopping-cart"></i>&nbsp;Packing Slip</span>
											</a> -->
											<?php
										}
										?>
									</li>
									<li>
										<a href="#lrattachment" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Customer">
											<span class="text-success">
												<i class="fa fa-paperclip"></i>
												&nbsp; LR Attachment
											</span>
										</a>
									</li>
								</ul>
							</div>
							<?php
						}
						?>
					</td>
					<td><?php echo ++$count; ?></td>
					<td><?php echo "<a target='_blank' href='view_dispatch.php?id=".$ctable_d['id']."'>".stripslashes($ctable_d['dispatch_no'])."</a>" ?></td>
					<td><?php echo "<a target='_blank' href='order_viewer.php?order_id=".$ctable_d['order_id']."'>". $db->rp_getValue("orders","order_no","id='".$ctable_d['order_id']."'")."</a>";
					?></td>
					<td align="right"><?php echo stripslashes($ctable_d['dispatch_qty']); ?></td>
					<!-- <td align="right"><?php echo stripslashes(CURR.$db->rp_num($ctable_d['grand_total'])); ?></td>
	                <td align="right"><?php echo stripslashes(CURR.$db->rp_num($gst_amount)); ?></td> -->
	                <td align="right"><?php echo CURR.$db->rp_getValue("orders","transport_charge","id='".$ctable_d['order_id']."'"); ?></td>
	                <td><?php echo stripslashes($ctable_d['company_name']); ?></td>
	                <td><?php if($ctable_d['sales_name']==""){echo "--";}else{echo stripslashes($ctable_d['sales_name']);}?>
	                </td>
	                <td><?php if($ctable_d['order_type']=='1'){ $slug="Super Stockist";}else if($ctable_d['order_type']=='2'){$slug="Distributor";}else if($ctable_d['order_type']=='3'){
							$slug="Dealer";}else if($ctable_d['order_type']=='4'){$slug="B2B Customer";}else if($ctable_d['order_type']=='6'){$slug="B2C Customer";}else if($ctable_d['order_type']=='normal_user'){$slug="Normal Customer";}echo stripslashes($slug);?></td>
					<td><?php echo date('d-m-Y',strtotime($ctable_d['dispatch_date'])); ?></td>
					<td><?php echo $dispatch_status_array[$ctable_d['status']]; ?></td>
					<td>
		                <?php 
			                if($lr_image_path!="")
			                {
				                ?>
				                <a href="<?= $lr_image ?>" download  class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
				                <a href="<?= $lr_image ?>" target="_blank"  class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
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
	$("#company_name").select2();
</script>
<script type="text/javascript">
    $(".filterBtn").on("click",function()
    {
        sales_executive = $("#sales_executive").val();
        customer_id = $("#customer_id").val();
        df1=$("#material_request_filter_input").val();
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


<script type="text/javascript">
	function GeneratePackingSlip(dispatchid) 
	{
		var r = confirm("Are you sure to Generate PackingSlip???");
		if (r)
		{
			$.ajax({
				type: "post",
				url: "ajax_create_packing_slip.php",
				data: "dispatchid=" + dispatchid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					result = $.parseJSON(result);
					if (result.ack == 0) {
						toastr.error(result.ack_msg);
					} else {
						toastr.success(result.ack_msg);
						window.location.href = "packing_slip_crud_new.php?mode=edit&dispatch_id="+dispatchid+"&packing_id="+result.packing_slip_id;
					}
				}
			})
		}
	}
</script>
<?php require_once 'disconnect.php';  ?>