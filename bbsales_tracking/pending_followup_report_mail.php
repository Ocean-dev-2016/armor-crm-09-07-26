<?php 
include("connect_in.php");
$today = date('Y-m-d',strtotime($_REQUEST['date']));
$ctable_where .= " DATE(followup_date) = '".$today."' AND status=0 ";
$Pending_FollowupR = $db->rp_getData("followup","*",$ctable_where,"id DESC",0);
?>

<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
</style>
<table id="customers">
    <thead>
    	<tr>
    		<th colspan="11" style="text-align: center;">Pending Followup Report - <?= date('d-m-Y',strtotime($_REQUEST['date'])) ?></th>
    	</tr>
      	<tr>
        	<th style="width: 5%">No.</th>
            <th>Customer Name</th>
            <th>Mobile No</th>
            <th>State</th>
            <th>City</th>
            <th>Area Sales Manager Name</th>
            <th>Inquiry By</th>
            <th>Followup By</th>
            <th>Date and Time</th>
            <th>Description</th>
            <th>Through</th>
        </tr>
    </thead>
    <tbody>
    	<?php
    	if(mysqli_num_rows($Pending_FollowupR)>0)
    	{
    		$count = 0;
    		while($Pending_FollowupD = mysqli_fetch_array($Pending_FollowupR))
    		{
    			?>
    			<tr>
    				<td><?php echo ++$count; ?></td>
    				<td>
	                    <?php
	                    if($Pending_FollowupD['reference_table']=="sales_executive")
	                    {
	                        $followup_flag="followup";
	                        echo $db->rp_getValue("executive","cname","id='".$Pending_FollowupD['visitor_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="no_order_inquiry")
	                    {
	                        $followup_flag="inquiry_followup";
	                        echo $db->rp_getValue("no_order_inquiry","company_name","id='".$Pending_FollowupD['reference_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="customer_inquiry")
	                    {
	                        $followup_flag="leads_followup";
	                        echo $db->rp_getValue("customer_inquiry","company_name","id='".$Pending_FollowupD['reference_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="quotation_detail")
	                    {
	                        $followup_flag="quotation_followup";
	                        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$Pending_FollowupD['reference_id']."'");
	                        echo $db->rp_getValue("executive","cname","id='".$customer_id."'");
	                    }
	                    ?>
                	</td>
                	<td>
	                    <?php 
	                    if($Pending_FollowupD['reference_table']=="sales_executive")
	                    {
	                        echo $db->rp_getValue("executive","phone","id='".$Pending_FollowupD['visitor_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="no_order_inquiry")
	                    {
	                        echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$Pending_FollowupD['reference_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="customer_inquiry")
	                    {
	                        echo $db->rp_getValue("customer_inquiry","mobile_number","id='".$Pending_FollowupD['reference_id']."'");
	                    }
	                    else if($Pending_FollowupD['reference_table']=="quotation_detail")
	                    {
	                        $followup_flag="quotation_followup";
	                        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$Pending_FollowupD['reference_id']."'");
	                        echo $db->rp_getValue("executive","phone","id='".$customer_id."'");
	                    }
	                    ?>
                	</td>
                	<td><?php echo $db->rp_getValue("no_order_inquiry","state","id='".$Pending_FollowupD['reference_id']."'");?></td>
	                <td><?php echo $db->rp_getValue("no_order_inquiry","city","id='".$Pending_FollowupD['reference_id']."'");?></td>
	                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$Pending_FollowupD['user_id']."'");?></td>  
	                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$Pending_FollowupD['user_id']."'");?></td> 
	                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$Pending_FollowupD['user_id']."'");?></td>
                    <?php 
                    if($Pending_FollowupD['followup_date']=="0000-00-00 00:00:00" || $Pending_FollowupD['followup_date']=="1970-01-01 00:00:00")
                	{
                		?>
                    	<td></td>
                		<?php 
                	} 
                	else
                	{ 
                		?>
                    	<td><?php echo date('d-m-Y h:i A',strtotime($Pending_FollowupD['followup_date'])); ?></td>
                		<?php 
                	} 
                    ?> 
                    <td><?php echo $Pending_FollowupD['description']; ?></td> 
                    <td><?php if($Pending_FollowupD['through']=='1'){ $slug="call";}else if($Pending_FollowupD['through']=='2'){ $slug="sms";}else if($Pending_FollowupD['through']=='3'){$slug="email";}
                    echo $slug;?></td>
    			</tr>
    			<?php
    		}
    	}
    	?>
    </tbody>
</table>