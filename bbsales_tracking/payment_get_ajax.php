<?php
$page_id=571;$page_slug='payment';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "payment";
$ctable1 	= "Payment";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	// $customer_r=$db->rp_getData("invoice","DISTINCT customer_id","customer_name like '%".$_REQUEST['searchName']."%'","",0);
	$customer_r=$db->rp_getData("executive","id","company_name like '%".$_REQUEST['searchName']."%' OR cname like '%".$_REQUEST['searchName']."%'","",0);
	$cust_id=array();
	if($customer_r){
		while($customer_d=mysqli_fetch_assoc($customer_r))
		{
			$cust_id[]=$customer_d['id'];
		}
	}
	$cust_id=implode(",",$cust_id);
	$ctable_where .="customer_id IN (".$cust_id.") AND";
}
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];



/*$customer_type=$db->rp_getValue("executive","type_of_executive","id='".$uid."'",0);
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$type='1';
	$c_id=array();
	$dealer=$db->rp_getData("executive","id","type_of_executive='1'","",0);
	if($dealer){
		while($dealer_d = mysqli_fetch_array($dealer))
		{
			$c_id[]=array("customer_id"=>$dealer_d['id']);
		}
	}
}
else{
    echo "hello"; exit;
	if($customer_type=='1')
	{
		$type='2';
		$dealer=$db->rp_getData("executive","id","super_stockist_id=".$uid." AND type_of_executive='2'","",0);
		$c_id=array();
		if($dealer){
			while($dealer_d = mysqli_fetch_array($dealer))
			{
				
				$c_id[]=array("customer_id"=>$dealer_d['id']);
				
			}
		}
	}
	
	else if($customer_type=='1')
	{
		$type='3';
		$dealer=$db->rp_getData("executive","id","dealer_distributor_id=".$uid." AND type_of_executive='3'","",0);
		$c_id=array();
		if($dealer){
			while($dealer_d = mysqli_fetch_array($dealer))
			{
				
				$c_id[]=array("customer_id"=>$dealer_d['id']);
				
			}
		}
	}
	
}*/


$ctable_where .= " isDelete=0";



if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{

}
else if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']==12) 
{
	
}
else
{
	$ctable_where .="  AND sales_executive_id IN(".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";
}

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
 	$type = $_REQUEST['type'];
}

if(isset($_REQUEST['payment_status']) && $_REQUEST['payment_status']!="" && $_REQUEST['payment_status']!=NULL)
{
 	$ctable_where .= " AND payment_status = '".$_REQUEST['payment_status']."' ";
 	$payment_status = $_REQUEST['payment_status'];
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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"payment_date DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<div id="total-payment-value" style="text-align: right;"></div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	<tr>
        		<th></th>
        	</tr>
        	<tr>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control input-medium status" name="type" id="type">
						<option value="">--- Select Payment Type ---</option>
                        <option value="1" <?php if("1"==$type){echo "selected";}?>>By Cash</option>
                        <option value="2" <?php if("2"==$type){echo "selected";}?>>By Cheque</option>
                        <option value="3" <?php if("3"==$type){echo "selected";}?>>Online</option>
                        <option value="4" <?php if("4"==$type){echo "selected";}?>>Other</option>
                     </select>
        		</th>
        		<th></th>
        		<th></th>
        		<th></th>
        		<th>
        			<select class="form-control input-medium" name="payment_status" id="payment_status">
						<option value="">--- Select Payment Status ---</option>
                        <option value="1" <?php if("1"==$payment_status){echo "selected";}?>>Approved</option>
                        <option value="0" <?php if("0"==$payment_status){echo "selected";}?>>Pending</option>
                    </select>
        		</th>
        	</tr>
            <tr>
            	<th></th>
                <th>No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Sales Person Name</th>
                <!-- <th>Bill No.</th> -->
                <th>Receipt No.</th>
				<th>Payment by</th>	
				<th>Payment Date</th>
				<th>Cheque No</th>
				<th style="text-align:right;">Payment Amount</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
		/*foreach($c_id as $c)
		{*/
		
		$ctable_type = " AND customer_id=".$c['customer_id']."";
		//$ctable_r = $db->rp_getData($ctable,"*",$ctable_where.$ctable_type,"id DESC limit $page_position, $item_per_page","",1);
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr>
            	<td>
                <?php $ctable_d['id'];              
                if($rights['update_flag']==1)
                {
                    ?>
                    <div class="btn-group">             
                        <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                            <i class="fa fa-gear"></i>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
                                    <span class="text-primary">
                                        <i class="fa fa-pencil"></i>
                                        &nbsp;Edit
                                    </span>
                                </a>
                            </li>
                            <?php
                            if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
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
                        </ul>
                    </div>
                    <?php
                }
                ?>
                
            </td>
                <td><?php echo ++$count; ?></td>
                
                <td>
				<?php				
				$company_name=$db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'");
				?>
				<span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($company_name); ?></span></td>
				<td><?php				
				echo $customer_name=$db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'");
				?></td>
				<td><?php				
				echo $customer_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");
				?></td>
				<!-- <td><?php
					//$dispatch_bill_no=$db->rp_getValue("invoice","invoice_no","id='".$ctable_d['dispatch_id']."'");
					//echo $dispatch_bill_no; ?>
				</td> -->
				<td><?php echo stripslashes($ctable_d['receipt_no']); ?></td>
				<td>
					<?php
					if($ctable_d['payment_type']==1)
					{
						$type = "By Cash"; 
					}
					else if ($ctable_d['payment_type']==2)
					{
						$type = "By Cheque"; 
					}
					else if ($ctable_d['payment_type']==3)
					{
						$type = "Online"; 
					}
					else if ($ctable_d['payment_type']==4)
					{
						$type = "Other"; 
					}
					echo $type;
					?>
				</td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
				<td><?php echo $ctable_d['cheque_no']; ?></td>
				<td align="right"><?php echo stripslashes(CURR.($db->rp_num($ctable_d['paid_amount']))); ?></td>
				<td>
					<?php
					if($ctable_d['payment_status']==0)
					{ 
						?>
						<button type="button" name="submit" value="print"  onClick="approvepayment('<?php echo $ctable_d['id']; ?>','approve','Are you sure you want to Approve payment ?')" class="btn yellow btn-sm"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Payment Approve</button>
						<?php 
					}
					else
					{
						?>
						<span class="text-success"><i class="fa fa-check-circle"></i>&nbsp;Approved</span>
						<?php
					}
					?>
				</td> 
            </tr>
        	<?php
        	$t_payment_val += $ctable_d['paid_amount']; 
            }
		//}
		}
		?>
	
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>500</option>
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
	$("#type").select2(); 
	$("#payment_status").select2(); 
	$("#total-payment-value").html("<strong>Total Amount : <?php echo CURR.$db->rp_number_format(stripslashes(round($t_payment_val)),2); ?></strong>");
</script>
<script type="text/javascript">
	function approvepayment(id,mode,msg)
	{   
		var payment_status = '<?=$_REQUEST['payment_status'] ?>';
		var r = confirm(msg);
        if(r)
        {
        	$.ajax({
                url:"payment_ajax_function.php",
                type:"POST",
                data:{
                    m:mode,
                    id:id,                
                },
                beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
                success:function(result) 
                {
                    var result=JSON.parse(result);
                    // $("#loading-modal").modal('hide');
                    $('.preloader').fadeOut('slow');
                    if(result.ack==1)
                    {                       
                        toastr.success(result.ack_msg,"Success!!"); 
                        location.reload();
                        //window.location.href = "http://24.24.25.41/cmk/bbsales_tracking/payment_manage.php?payment_status="+payment_status;
                    }
                    else
                    {
                        toastr.error(result.ack_msg, 'Error!!');
                    }
                },            
            })
        }
	}
</script> 
<?php require_once("disconnect.php"); ?>