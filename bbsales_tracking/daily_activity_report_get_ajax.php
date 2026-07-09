<?php
$page_id=617;$page_slug='daily_activity_report';
include("connect.php");
include("../include/no_to_word.php");
$ctable       = "activity_log";
$ctable1      = "Orders";
$where = "isDelete=0 AND log_description!=''";

if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) 
{
	$where .= " AND DATE(activity_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL)
{
	$where .= " AND DATE(activity_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'";
}

if(isset($_REQUEST['ids']) && $_REQUEST['ids']!="" && $_REQUEST['ids'] != NULL && $_REQUEST['ids']!="null")
{
	if($_REQUEST['user_type']=="2")
	{
		$sales_id = $db->rp_getValue("dealer_distributor_network","sales_executive_id","id='".$_REQUEST['ids']."'",0);
	   	if($sales_id!="0")
	   	{
	   		$where .= " AND ((user_id = '".$sales_id."' AND flag='Application' ) OR (user_id='".$_REQUEST['ids']."'))";
	   	}
	   	else
	   	{
	   		$where .= " AND user_id = '".$_REQUEST['ids']."' ";	
	   	}	
	}
   	
   	if($_REQUEST['user_type']=="1")
   	{
	   	$customer_id = $db->rp_getValue("executive","id","id='".$_REQUEST['ids']."'");
   		if($customer_id)
   		{
   			$where .= " AND ((customer_id = '".$customer_id."' AND flag='Application' ) OR (customer_id='".$customer_id."'))";
   		}
   		else
   		{
   			$where .= " AND ((ref_id = '".$_REQUEST['ids']."' AND flag='Application' ) OR (ref_id='".$_REQUEST['ids']."'))";	
   		}
	}
}

if((isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL) || (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL) || (isset($_REQUEST['ids']) && $_REQUEST['ids']!="" && $_REQUEST['ids']!=NULL))
{
	$ctable_r = $db->rp_getData($ctable,"*",$where,"id ASC",0);
	?>
	<div class="table-responsive">
	<form action="" name="frm" id="print_info" method="post">
		<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
	        <thead>
				<tr>
					<td class="header" align="center" colspan="7" ><h3><b>Daily Activity REPORT <?php echo $date;?></b></h3></td>
				</tr>
	            <div style="position: sticky;">
	                <tr class="tr">
	                    <th class="th" style="position: sticky;">No.</th>
	                    <th class="th">Sub module</th>
	                    <th class="th">User Name</th>
	                    <th class="th">Description</th>
	                    <th class="th">Date and Time</th>
	                </tr>
	            </div>
	        </thead>
	        <tbody>
	        <?php
	        if(mysqli_num_rows($ctable_r)>0)
	        {
	            $count = 1;
	            while($ctable_d = mysqli_fetch_array($ctable_r))
	            {
	        		?>
	            	<tr class="tr" data-toggle="modal" data-id="1" data-target="#orderModal">
		                <td class="td" style="width:5px;" ><?php echo $count++; ?></td>
		               	<td class="td"><?php echo $ctable_d['module_name']; ?></td>
		                <td class="td"><?php echo stripslashes($ctable_d['user_name']); ?></td>
		                <td class="td"><?php echo stripslashes($ctable_d['log_description']." (".$ctable_d['flag'].")"); ?></td>
		                <td class="td"><?php echo date('d-m-Y H:i',strtotime($ctable_d['activity_date'])); ?></td>
		            </tr>
	        		<?php
	            }
	        }
	        else
	        {
	        	?>
	        	<tr>
	        		<td colspan="8" class="text-center">No Data found!!</td>
	        	</tr>
	        	<?php
	        }
	        ?>
	        </tbody>
	    </table>
	</form>
	</div>
<?php
}
else
{
	?>
	<div class="row">
		<div class="col-sm-12 text-center">
			<h3> Select Any Filter To See Order Record</h3>
		</div>
	</div>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>