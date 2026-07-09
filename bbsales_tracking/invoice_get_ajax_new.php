<?php
$page_id=588;$page_slug='invoice_page';
$page_slug = 'add_invoice';
include("connect.php");
$ctable       = "invoice_new";
$ctable1      = "Invoice";
$uid          = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
$invoice_type   = isset($_REQUEST['invoice_type']) ? $_REQUEST['invoice_type'] : "";
$type         = "";
$ctable_where = "";
// echo $_REQUEST['searchName'];
// echo "vishal";

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $ctable_where .= " (
        customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
        company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR
        invoice_no like '%" . $db->clean($_REQUEST['searchName']) . "%'
    ) AND ";
}
$ctable_where .= " isDelete=0 AND status!=-1 ";
if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") {
    $ctable_where .= " AND sales_id = '" . $db->clean($_REQUEST['sales_executive_id']) . "' ";
}
if (isset($_REQUEST['qid']) && $_REQUEST['qid'] != "" && $_REQUEST['qid']!=undefined && $_REQUEST['qid']!="null") {
    $ctable_where .= " AND quotation_id = '" . $db->clean($_REQUEST['qid']) . "' ";
}
if (isset($_REQUEST['customer_type']) && $_REQUEST['customer_type'] != "") {
    $ctable_where .= " AND customer_type = '" . $db->clean($_REQUEST['customer_type']) . "' ";
}

// if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 12) {
//     $ctable_where .= " ";
// } else {
//     $ctable_where .= " AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
//     //$ctable_where .= " isDelete=0 AND sales_id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'";
// }




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
else
{


}







$item_per_page = ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 100;
if (isset($_REQUEST["page"])) {
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
    if (!is_numeric($page_number)) {
        die('Invalid page number!');
    }
} else {
    $page_number = 1;
}
if (isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] != NULL) {
    $ctable_where .= " AND status = '" . $_REQUEST['status'] . "' ";
}
if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && $_REQUEST['ToDate'] != undefined) {
    $ctable_where .= " AND adate <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}
if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL && $_REQUEST['FromDate'] != undefined) {
    $ctable_where .= " AND adate >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined) {
	$ctable_where .= " AND DATE(adate) >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined) {
	$ctable_where .= " AND DATE(adate) <= '" .$_REQUEST['fromdate']. "' ";
}
if (isset($_REQUEST['df']) && $_REQUEST['df'] != "" && $_REQUEST['df'] != NULL && $_REQUEST['df'] != undefined) {
    $date_filter_query    = urldecode($_REQUEST['df']);
    $date_filter_query_ex = explode(" to ", $date_filter_query);
    $ctable_where .= " AND ( DATE(invoice_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(invoice_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
}

if (isset($_REQUEST['df2']) && $_REQUEST['df2'] != "" && $_REQUEST['df2'] != NULL && $_REQUEST['df2'] != undefined) {
    $date_filter_query_date    = urldecode($_REQUEST['df2']);
    $date_filter_query_ex_date = explode(" to ", $date_filter_query_date);
    $ctable_where .= " AND ( DATE(adate)>='" . date("Y-m-d", strtotime($date_filter_query_ex_date['0'])) . "' AND DATE(adate)<='" . date("Y-m-d", strtotime($date_filter_query_ex_date['1'])) . "'  ) ";
}

if (isset($_REQUEST['invoice_month']) && $_REQUEST['invoice_month'] != "" && $_REQUEST['invoice_month'] != NULL) {
	$ctable_where .= " AND MONTH(adate) = '".$_REQUEST['invoice_month']."'";
}

if (isset($_REQUEST['invoice_year']) && $_REQUEST['invoice_year'] != "" && $_REQUEST['invoice_year'] != NULL) {
	$ctable_where .= " AND YEAR(adate) = '".$_REQUEST['invoice_year']."'";
}

if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "" && $_REQUEST['customer_id'] != NULL) {
	$ctable_where .= " AND customer_id = '".$_REQUEST['customer_id']."'";
}
if (isset($_REQUEST['type']) && $_REQUEST['type'] != "" && $_REQUEST['type'] != NULL) {
    $ctable_where .= " AND customer_type = '" . $_REQUEST['type'] . "' ";
    $type = $_REQUEST['type'];
}
if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] != "" && $_REQUEST['sales_id'] != NULL && $_REQUEST['sales_id'] != undefined) {
    $ctable_where .= " AND sales_id = '" . $_REQUEST['sales_id'] . "' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where);
$total_pages    = ceil($get_total_rows / $item_per_page);
$page_position  = (($page_number - 1) * $item_per_page);
$ctable_r       = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page", 0);
?>
<style>
.table-scrollable 
{
    width: auto;
    height: 600px;
    overflow-x: scroll;
    overflow-y: scroll;
    border: 1px solid #e7ecf1;
    margin: 10px 0 !important;
}
.table_amount {
    width: 40%;
    max-width: 100%;
    margin-bottom: 20px;
    text-align: right;
}
</style>
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
	<!-- <div id="total-invoice-value" style="text-align: right;"></div>
	<div id="total-invoice-value-approve" style="text-align: right;"></div>
	<div id="total-invoice-value-pending" style="text-align: right;"></div>
	<div id="total-invoice-value-cancel" style="text-align: right;"></div>
	<div id="total-invoice-value-disapprove" style="text-align: right;"></div> -->

	<div style="text-align: -webkit-right;;">
		<table class="table_amount table-striped table-bordered table-hover">
			
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Invoice </strong></td>
				<td style="padding-right: 10px;" id="total-invoice"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-invoice-value"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Approve Invoice Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-invoice-value-approve"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Pending Invoice Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-invoice-value-pending"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Cancel Invoice Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-invoice-value-cancel"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Disapprove Invoice Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-invoice-value-disapprove"></td>
			</tr>
		</table>
	</div>

	<div class="table-scrollable" style="height: 600px;">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead class="fix-th">
        	<!-- <tr>
        		<th class="fix-th1" colspan="13" style="text-align: right;" id="total-order-value">
        			Total Amount: 
        		</th>
        		<th class="fix-th1" colspan="4"></th>
        	</tr> -->
        	<tr>
        		<th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                	<label>Filter By Created Date</label>
					<div class="input-group">
						<input class="form-control datetimerange-picker-input-date" id="material_request_filter_input_date" value="<?php echo $date_filter_query_date; ?>" name="df2" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
						<span class="input-group-addon datetimerange-picker-btn-date">
						<i class="fa fa-calendar"></i>
						</span>
						<span class="input-group-btn">
							<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
						</span>
					</div>
                </th>
                <th>
                	<label>Filter By Date</label>
					<div class="input-group">
						<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
						<span class="input-group-addon datetimerange-picker-btn">
						<i class="fa fa-calendar"></i>
						</span>
						<span class="input-group-btn">
							<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->
						</span>
					</div>
					<!-- <button class="btn btn-success filterBtn" type="submit" value="search">Filter</button> -->        	
                </th>
                <th>
					<select class="form-control input-small" id="status">
						<option value="">Select Status</option>
	            		<option value="-2" <?=("-2"==$_REQUEST['status'])?"selected":"";?>>Disapproved</option>
	            		<option value="0" <?=("0"==$_REQUEST['status'])?"selected":"";?>>Pending</option>
	            		<option value="1" <?=("1"==$_REQUEST['status'])?"selected":"";?>>Approved</option>
	            		<option value="3" <?=("3"==$_REQUEST['status'])?"selected":"";?>>Cancelled</option>
	            	</select>
				</th>
				<th></th>
				<th>
                	<!-- Sales Person Name -->
                	<?php
					// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
     				//{ 
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
		        	// }
		        	?>
	            </th>
	            <th>
	            	<select class="form-control input-small" id="type">
	            		<option value="">Select Invoice Type</option>
	            		<?php
							$type_r = $db->rp_getData("customer_type", "id,name", "isDelete=0");
							if ($type_r) 
							{
								while ($type_d = mysqli_fetch_assoc($type_r)) 
								{
									?>
									<option value="<?= $type_d['id'] ?>" <?= ($type_d['id'] == $_REQUEST['type']) ? "selected" : ""; ?>><?= $type_d['name']; ?></option>
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
				<th class="fix-th1"></th>
                <th class="fix-th1">No.</th>
                <th class="fix-th1">Company Name</th>
                <th class="fix-th1">Person Name</th>
                <th class="fix-th1">Quotation No.</th>
                <th class="fix-th1">Order No.</th>
                <th class="fix-th1">Dispatch No.</th>
                <th class="fix-th1">Invoice No.</th>
                <th class="fix-th1">Invoice Created Date</th>
                <th class="fix-th1">Invoice Date</th>
				<th class="fix-th1">Status</th>
				<th class="fix-th1">LR No</th>
                <th class="fix-th1">Sales Person Name</th>
                <th class="fix-th1">Invoice Type</th>
                <th class="fix-th1" style="text-align:right;">Invoice Amount without Gst</th>
                <th class="fix-th1" style="text-align:right;">Invoice Amount with Gst</th>
                <th class="fix-th1">Lr Attachment</th>
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
            	$invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");

            	$order_id = $db->rp_getValue("dispatch_detail","order_id","id='".$ctable_d['dispatch_ids']."'",0);
            	$quotation_id = $db->rp_getValue("orders","quotation_id","id='".$order_id."'");

            	/*$customer=$db->rp_getValue('executive','isActive',"id=".$ctable_d['customer_id']."",0);
				if($customer==0)
				{
					continue;
				}*/
				if ($ctable_d['status'] == 1) 
                {
                    $style = "style='background-color: #FFFF99;'";
                }
                else if($ctable_d['status'] == 3){
                	$style = "style='background-color: #ff9999;'";	
                }
                else
                {
                    $style = "style='background-color: #add8e6;'";
                }
        		?>
	            <tr <?= $style ?>>
	            	<td>
						<div class="btn-group">
							<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i> </button>
							<ul role="menu" class="dropdown-menu">
								<?php
								if($rights['update_flag'] == 1 && $ctable_d['status']==0)
								{
									?>
										<li>
				                            <!-- <a href="<?=$ctable;?>_crud.php?mode=edit&id=<?=$ctable_d['id'];?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</span>
				                            </a> -->
				                            <a href="invoice_crud_new.php?mode=edit&id=<?=$ctable_d['id'];?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</span>
				                            </a>
			                        	</li>
									<?php	
								}
								else if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
								{
									?>
										<li>
				                           <!--  <a href="<?=$ctable;?>_crud.php?mode=edit&id=<?=$ctable_d['id'];?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</span>
				                            </a> -->
				                            <a href="invoice_crud_new.php?mode=edit&id=<?=$ctable_d['id'];?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</span>
				                            </a>
			                        	</li>
									<?php	
								}
									
								if ($ctable_d['status'] == 0 && $rights['delete_flag'] == 1) 
								//if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 && $rights['delete_flag'] == 1) 
								{
									?>
									<li>
										<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
											<span class="text-danger"><i class="fa fa-times"></i>&nbsp;Delete</span>
										</a>
									</li>
									<?php
								}
								else if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
								{
									?>
									<li>
										<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
											<span class="text-danger"><i class="fa fa-times"></i>&nbsp;Delete</span>
										</a>
									</li>
									<?php
								}


								if ($ctable_d['status'] == 1) 
								{
									?>
									<li>
										<a href="#myLRModal" data-title="Add Material Receive Date" data-id="<?= $ctable_d['id'] ?>" data-toggle='modal' title="LR"><span class="text-success"><i class="fa fa-circle"></i>&nbsp;Add LR Date</span>
										</a>
									</li>
									<?php
								}
								
								$file_path="invoice_viewer.php?invoice_id=".$ctable_d['id']."";
								?>
								<li>
									<a class="" target="_blank" href="<?php echo $file_path; ?>" title="save">
										<i class="fa fa-file-pdf-o"></i>
										Download
									</a>
								</li>


								<!-- <?php if($ctable_d['status']==1)
								{
								?>
									<li>
										<a onClick="cancel_invoice('<?php echo $ctable_d['id']; ?>');" title="Cancel Invoice">
											<span>
												<i class="fa fa-times"></i>
												&nbsp;Cancel Invoice
											</span>
										</a>
									</li>
								<?php
								} ?> -->
							</ul>
						</div>
					</td>
	                <td><?php echo ++$count; ?></td>
	                <td><?php echo $ctable_d['company_name'];?></td>
	                <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
	                <td>
	                	<span class="text-success"><a target="_blank" href="quotation_viewer.php?quotation_id=<?= $quotation_id ?>"><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'") ?></a></span>
	                </td>
	                <td>
	                	<span class="text-success"><a target="_blank" href="order_viewer.php?order_id=<?= $order_id ?>"><?php echo $db->rp_getValue("orders","order_no","id='".$order_id."'",0) ?></a></span>
	                </td>
	                <td>
	                	<?php
	                		$mainArray = array();
	                		$dids = explode(",",$ctable_d['dispatch_ids']);
	                		foreach ($dids as $key => $value) {
	                			$mainArray[$key][] = "<a target='_blank' href='view_dispatch.php?id=".$value."'>";
	                			$mainArray[$key][] = $db->rp_getValue("dispatch_detail","dispatch_no","id IN (".$value.")",0);
	                			$mainArray[$key][] = "</a>";
	                			$mainArray[$key] = implode("", $mainArray[$key]);
	                		}
	                		echo implode(",", $mainArray);
	                	?>
	                </td>
	                
	                <td>
	                	<span class="text-success"><a target="_blank" href="invoice_viewer.php?invoice_id=<?= $ctable_d['id'] ?>"><?php echo stripslashes($ctable_d['invoice_no']); ?></a></span>
	                </td>
	                <td><?php if($ctable_d['adate']!=""){ echo date('d-m-Y',strtotime($ctable_d['adate'])); }else{ echo "";}?></td>
	                <td><?php if($ctable_d['invoice_date']!=""){ echo date('d-m-Y',strtotime($ctable_d['invoice_date'])); }else{ echo "";}?></td>
	                <td><?php echo $invoice_status[$ctable_d['status']]; ?></td>
	                <td><?= $db->rp_getValue("lr_detail","lr_number","invoice_id='".$ctable_d['id']."'") ?></td>
	                <?php $sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'");?>
	                <td><?php if($sales_name==""){echo "--";}else{ echo $sales_name;}?></td>
	                <td><?php if($ctable_d['customer_type']=='1'){ $slug="Super Stockist";}else if($ctable_d['customer_type']=='2'){$slug="Distributor";}else if($ctable_d['customer_type']=='3'){$slug="Dealer";}else if($ctable_d['customer_type']=='4'){$slug="B2B Customer";}else if($ctable_d['customer_type']=='6'){$slug="B2C Customer";}else if($ctable_d['customer_type']=='normal_user'){$slug="Normal Customer";}echo stripslashes($slug);?></td>


	                <!-- <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d['subtotal']))); ?></td>
	                <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_d['grand_total']))); ?></td> -->


	                <?php


	                	$totalprice=0;
						$final_price=0;
						$boxqty=0;
						$cartoonqty=0;
						$totalproqty=0;
						$totalrate=0;
						$totaldiscount=0;
						$totalgstamount=0;

    					$items1=$db->rp_getData("invoice_new_product_item","*","invoice_id='".$ctable_d['id']."' AND dispatch_item_type=1","",0);
						while($item1=mysqli_fetch_array($items1)) 
						{
    						$totalprice +=$item1['pro_qty']*$item1['unitprice'];
    						$totalgstamount+=$item1['igst_amount'];
    					}

    					$total_tax_amt = $totalprice - ($ctable_d['cash_discount_amount'] + $ctable_d['additional_discount_amount']) + ($ctable_d['transport_charge']+$ctable_d['packing_charge']);

						$totalgst = $totalgstamount;
						$cashdisgst = ($ctable_d['cash_discount_amount']*$ctable_d['cd_gst']/100);
						$adddisgst = ($ctable_d['additional_discount_amount']*$ctable_d['ad_gst']/100);
						$packinggst = ($ctable_d['packing_charge']*$ctable_d['packing_charge_gst']/100);
						$transportgst = ($ctable_d['transport_charge']*$ctable_d['transport_charge_gst']/100);
						$finalgst = $totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst);

						$grand_total = $total_tax_amt+$finalgst; 

	               		?>

	                <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($total_tax_amt))); ?></td>
	                <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($grand_total))); ?></td>
	                <td>

	                <td>
						<?php
						$lr_attachmrnt = $db->rp_getValue("lr_detail","image_path","invoice_id='".$ctable_d['id']."'");
	                	if($lr_attachmrnt!="")
	                	{
	                		$file_path = ADMINSITEURL.LRCOPY_DOCUMENTS.$lr_attachmrnt;
	                		?>
	                			<a href="<?= $file_path ?>" download  class="text-warning" title="View"><i class="fa fa-download" style="font-size: 15px;"></i></a>&nbsp;&nbsp;&nbsp;
		                		<a href="<?= $file_path ?>" target="_blank"  class="text-sucess" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
	                		<?php
	                	} 
	                	?>
			        </td>
	                
	            </tr>
        		<?php
            }
        	//$t_order_val += $ctable_d['grand_total']; 
        	$t_invoice = $db->rp_getTotalRecord("invoice_new",$ctable_where,0);
        	//$t_invoice_val = $db->rp_getValue("invoice_new","SUM(grand_total)",$ctable_where,0); 
        	$t_invoice_val = $db->rp_getValue("invoice_new","SUM(grand_total)",$ctable_where,0); 
        	$t_invoice_val_approve = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status=1 AND".$ctable_where,0); 
        	$t_invoice_val_pending = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status=0 AND".$ctable_where,0); 
        	$t_invoice_val_cancel = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status=3 AND".$ctable_where,0); 
        	$t_invoice_val_disapprove = $db->rp_getValue("invoice_new","SUM(grand_total)","isDelete=0 AND status=-2 AND".$ctable_where,0); 
        }
        ?>
        </tbody>
    </table>
    </div>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) {echo ' selected="selected"';}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
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
	$("#qid").select2();
	$("#sales_id").select2();
	$("#type").select2();
	$("#status").select2();
	$("#brand_id").select2();

	$("#total-invoice").html("<strong><?php echo $t_invoice; ?></strong>");

	$("#total-invoice-value").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_invoice_val),2); ?></strong>");
	$("#total-invoice-value-approve").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_invoice_val_approve),2); ?></strong>");
	$("#total-invoice-value-pending").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_invoice_val_pending),2); ?></strong>");
	$("#total-invoice-value-cancel").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_invoice_val_cancel),2); ?></strong>");
	$("#total-invoice-value-disapprove").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_invoice_val_disapprove),2); ?></strong>");
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

	// $(".filterBtn").on("click",function()
	// {
	// 	sales_executive = $("#sales_executive").val();
	// 	customer_id = $("#customer_id").val();
	// 	df1=$("#material_request_filter_input").val();
	// 	df1 = encodeURI(df1)
	// 	displayRecords(100,1);
	// })
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

	$(".datetimerange-picker-btn-date").on("click",function(){
        $(".datetimerange-picker-input-date",$(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input-date").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
       	'Today': [moment(), moment()],
       	'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       	'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       	'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       	'This Month': [moment().startOf('month'), moment().endOf('month')],
       	'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	}});
    $('.datetimerange-picker-input-date').on('apply.daterangepicker', function(ev, picker) {
		$(".datetimerange-picker-input-date").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
	});
</script>
<?php require_once 'disconnect.php';  ?>