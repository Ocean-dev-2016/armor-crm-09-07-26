<?php 
$page_id = 555;$page_slug = 'page_executive';
include("connect.php");
if($_POST['mode']=="get_leads")
{
	?>
		<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$LeadR = $db->rp_getData("no_order_inquiry","*","id='".$_POST['customer_id']."' AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Lead No</th>
						<th>Source Of Medium</th>
						<th>Lead Date</th>
						<th>Lead Taken By</th>
						<th>Lead Assign To</th>
						<th>Status</th>
						<th>Image</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($LeadR)
					{
						while($LeadD = mysqli_fetch_assoc($LeadR))
						{
							
							$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working","-2"=>"Cancel","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business");
							?>
								<tr>
									<td>#INQ/<?= $LeadD['id']; ?></td>
									<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$LeadD['source_of_inquiry']."'"); ?></td>
									<td><?php if($LeadD['inquiry_date']!="1970-01-01"){ echo date('d-m-Y',strtotime($LeadD['lead_date']));}else{ echo ""; }?></td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$LeadD['inquiry_created_by']."'"); ?></td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$LeadD['inquiry_assign_to']."'"); ?></td>
									<td><?= $status_array[$LeadD['status']]; ?></td>
									<td>
										<?php 
					                    if($LeadD['image_path']!="")
					                    {
					                        $img = explode(",", $LeadD['image_path']);
					                        $imgpath = array();
					                        for ($i=0; $i < sizeof($img); $i++)
					                        { 
					                            $imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$LeadD["id"]."' AND id='".$img[$i]."'",0);
					                        }

					                        for ($i=0; $i < sizeof($imgpath); $i++)
					                        {
					                            if($i==0){
					                            ?>
					                                <a href="<?=$imgpath[$i]?>" data-lightbox="Lead<?=$count?>" data-title="Lead <?=$LeadD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                            <?php 
					                            }else{
					                            ?>
					                                <div class="hidden">
					                                    <a href="<?=$imgpath[$i]?>" data-lightbox="Lead<?=$count?>" data-title="Lead <?=$LeadD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                                </div>
					                            <?php
					                            }
					                        }
					                    }
					                    else
					                    {
					                        $img = $LeadD['image_path'] = DEFAULTIMG;
					                        ?>
					                        <a href="<?=$img?>" data-lightbox="Lead<?=$count?>" data-title="Lead <?=$LeadD['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
					                        <?php
					                    }
					                    ?>
									</td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="6" class="text-center">No Lead found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
}
if($_POST['mode']=="get_prospect")
{
	?>
		<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$ProspectR = $db->rp_getData("no_order_inquiry","*","id='".$_POST['customer_id']."' AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Raw Data No</th>
						<th>Source Of Medium</th>
						<th>Raw Data Date</th>
						<th>Raw Data Taken By</th>
						<th>Raw Data Assign To</th>
						<th>Status</th>
						<th>Image</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($ProspectR)
					{
						while($prospectD = mysqli_fetch_assoc($ProspectR))
						{
							
							$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working","-2"=>"Cancel","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business");
							?>
								<tr>
									<td>#INQ/<?= $prospectD['id']; ?></td>
									<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$prospectD['source_of_inquiry']."'"); ?></td>
									<td><?php if($prospectD['created_date']!="1970-01-01" && $prospectD['created_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($prospectD['created_date']));}else{echo ""; }?>
									</td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$prospectD['inquiry_created_by']."'"); ?></td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$prospectD['inquiry_assign_to']."'"); ?></td>
									<td><?= $status_array[$prospectD['status']]; ?></td>
									<td>
										<?php 
					                    if($prospectD['image_path']!="")
					                    {
					                        $img = explode(",", $prospectD['image_path']);
					                        $imgpath = array();
					                        for ($i=0; $i < sizeof($img); $i++)
					                        { 
					                            $imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$prospectD["id"]."' AND id='".$img[$i]."'",0);
					                        }

					                        for ($i=0; $i < sizeof($imgpath); $i++)
					                        {
					                            if($i==0){
					                            ?>
					                                <a href="<?=$imgpath[$i]?>" data-lightbox="Prospect<?=$count?>" data-title="Prospect <?=$prospectD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                            <?php 
					                            }else{
					                            ?>
					                                <div class="hidden">
					                                    <a href="<?=$imgpath[$i]?>" data-lightbox="Prospect<?=$count?>" data-title="Prospect <?=$prospectD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                                </div>
					                            <?php
					                            }
					                        }
					                    }
					                    else
					                    {
					                        $img = $prospectD['image_path'] = DEFAULTIMG;
					                        ?>
					                        <a href="<?=$img?>" data-lightbox="Prospect<?=$count?>" data-title="Prospect <?=$prospectD['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
					                        <?php
					                    }
					                    ?>
									</td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="6" class="text-center">No Raw Data found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
}
else if($_POST['mode']=="get_inquiry")
{
	?>
		<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$inquiryR = $db->rp_getData("no_order_inquiry","*","id='".$_POST['customer_id']."' AND isDelete=0 AND inquiry_lead_flag=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Inquiry No</th>
						<th>Source Of Medium</th>
						<th>Inquiry Date</th>
						<th>Inquiry Taken By</th>
						<th>Inquiry Assign To</th>
						<th>Status</th>
						<th>Image</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($inquiryR)
					{
						while($inquiryD = mysqli_fetch_assoc($inquiryR))
						{
							
							$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working","-2"=>"Cancel","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business");
							?>
								<tr>
									<td>#INQ/<?= $inquiryD['id']; ?></td>
									<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$inquiryD['source_of_inquiry']."'"); ?></td>
									<td><?php if($inquiryD['inquiry_date']!="1970-01-01"){ echo date('d-m-Y',strtotime($inquiryD['inquiry_date']));}else{ echo ""; }?></td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$inquiryD['inquiry_created_by']."'"); ?></td>
									<td><?= $db->rp_getValue("sales_executive","name","id='".$inquiryD['inquiry_assign_to']."'"); ?></td>
									<td><?= $status_array[$inquiryD['status']]; ?></td>
									<td>
										<?php 
					                    if($inquiryD['image_path']!="")
					                    {
					                        $img = explode(",", $inquiryD['image_path']);
					                        $imgpath = array();
					                        for ($i=0; $i < sizeof($img); $i++)
					                        { 
					                            $imgpath[] = "../resource/image/".$db->rp_getValue("media","url","reference_id='".$inquiryD["id"]."' AND id='".$img[$i]."'",0);
					                        }

					                        for ($i=0; $i < sizeof($imgpath); $i++)
					                        {
					                            if($i==0){
					                            ?>
					                                <a href="<?=$imgpath[$i]?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$inquiryD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                            <?php 
					                            }else{
					                            ?>
					                                <div class="hidden">
					                                    <a href="<?=$imgpath[$i]?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$inquiryD['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
					                                </div>
					                            <?php
					                            }
					                        }
					                    }
					                    else
					                    {
					                        $img = $inquiryD['image_path'] = DEFAULTIMG;
					                        ?>
					                        <a href="<?=$img?>" data-lightbox="Inquiry<?=$count?>" data-title="Inquiry <?=$inquiryD['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
					                        <?php
					                    }
					                    ?>
									</td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="6" class="text-center">No Inquiry found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
}

else if($_POST['mode']=="get_quotation")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$quotationR = $db->rp_getData("quotation_detail","*","customer_id='".$_POST['customer_id']."' AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Inquiry No.</th>
						<th>Quotation No</th>
						<th>Quotation Date</th>
						<th>Quotation Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($quotationR)
					{
						while($quotationD = mysqli_fetch_assoc($quotationR))
						{
							
							$quotationstatus_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","-2"=>"Disapproved");
							?>
								<tr>
									<td><?= "#INQ/" . $quotationD['inquiry_id']; ?></td>
									<td><a href="quotation_viewer.php?quotation_id=<?= $quotationD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo stripslashes($quotationD['quotation_no']); ?></span></a></td>
									<td><?php echo date('d-m-Y', strtotime($quotationD['quotation_date'])); ?></td>
									<td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($quotationD['grand_total']))); ?></td>
									<td><?= $quotationstatus_array[$quotationD['status']]; ?></td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="6" class="text-center">No Quotation found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
	</div>
	<?php
}

else if($_POST['mode']=="get_order")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$orderR = $db->rp_getData("orders","*","customer_id='".$_POST['customer_id']."' AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Order No</th>
						<th>Quotation No</th>
						<th>Order Date</th>
						<th>Order Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($orderR)
					{
						while($orderD = mysqli_fetch_assoc($orderR))
						{
							
							$orderstatus_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved");
							?>
								<tr>
									<td><a href="order_viewer.php?order_id=<?= $orderD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $orderD['order_no']; ?></span></a></td>
									<td><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$orderD['quotation_id']."'",0) ?></td>
									<td><?php echo date('d-m-Y',strtotime($orderD['order_date'])); ?></td>
									<td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($orderD['grand_total']))); ?></td>
									<td><?= $orderstatus_array[$orderD['status']]; ?></td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="5" class="text-center">No Order found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
	</div>
	<?php
}

else if($_POST['mode']=="get_invoice")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<?php
	    	$InvoiceR = $db->rp_getData("invoice_new","*","customer_id='".$_POST['customer_id']."' AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Invoice No</th>
						<th>Invoice Date</th>
						<th>Invoice Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($InvoiceR)
					{
						while($InvoiceD = mysqli_fetch_assoc($InvoiceR))
						{
							
							$orderstatus_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved");
							?>
								<tr>
									<td><a href="invoice_viewer.php?invoice_id=<?= $InvoiceD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $InvoiceD['invoice_no']; ?></span></a></td>
									<td><?php if($InvoiceD['invoice_date']==""){echo "";} else { echo date('d-m-Y',strtotime($InvoiceD['invoice_date'])); }?>
									</td>
									<td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($InvoiceD['grand_total']))); ?></td>
									<td><?= $orderstatus_array[$InvoiceD['status']]; ?></td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="5" class="text-center">No Order found!!</td></tr>
			    		<?php
					}
					?>
				</tbody>
			</table>
	</div>
	<?php
}

else if($_POST['mode']=="get_followup")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<?php
			$refrence_id = $db->rp_getValue("no_order_inquiry","id","id='".$_POST['customer_id']."' AND isDelete=0",0);

			if($refrence_id != "")
			{
				$followupR = $db->rp_getData("followup","*","(reference_id='".$refrence_id."' OR visitor_id='".$_POST['customer_id']."') AND isDelete=0","id DESC",0);
	    	?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Sales Officer Name</th>
						<th>Date and Time</th>
						<th>Description</th>
						<th>Through</th>
						<th>Response Date</th>
						<th>Response</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($followupR)
					{
						while($followupD = mysqli_fetch_assoc($followupR))
						{
							$followupthrough_array = array("1"=>"Call","2"=>"Sms","3"=>"Email");
							?>
								<tr>
									<td><?php echo $db->rp_getValue("sales_executive","name","id='".$followupD['user_id']."'") ?></td>
									<?php 
									if($followupD['followup_date']=="0000-00-00 00:00:00" || $followupD['followup_date']=="1970-01-01 00:00:00")
										{
											?>
                    						<td></td>
					                		<?php 
					                	} 
					                	else
					                	{ 
					                		?>
					                    	<td><?php echo date('d-m-Y h:i A',strtotime($followupD['followup_date'])); ?></td>
					                	<?php 
					                	} 
					                	?> 
									 	<td><?php echo $followupD['description']; ?></td>      
										<td><?php echo $followupthrough_array[$followupD['through']]?></td>
                						<?php if($followupD['response_date']=="0000-00-00 00:00:00")
                						{
                							?>
                    						<td></td>
					                	<?php 
					                	} 
					                	else 
					                	{ 
					                		?>
					                    	<td><?php echo date('d-m-Y',strtotime($followupD['response_date'])); ?></td>
					                	<?php 
					                	} 
					                	?>   
										<td><?php echo $followupD['response']; ?></td>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="6" class="text-center">No Followup found!!</td></tr>
			    		<?php
					}
					?>
					<?php
			}
			else
			{
				?>
			    			<tr><td colspan="6" class="text-center">No Followup found!!</td></tr>
			    <?php
			}
			?>




	    	
				</tbody>
			</table>
	</div>
	<?php
}
else if($_POST['mode']=="get_timeline")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<?php
		$refrence_id = $db->rp_getValue("no_order_inquiry","id","id='".$_POST['customer_id']."' AND isDelete=0",0);
		if($refrence_id!="")
		{
	    	$logdataR = $db->rp_getData("activity_log","*","(ref_id='".$refrence_id."' OR ref_id='".$_POST['customer_id']."' OR customer_id='".$_POST['customer_id']."') AND isDelete=0 AND log_description!=''","activity_date ASC",0);
		}
		else
		{
			/*followup*/
			$follId = array();
			$followupid = $db->rp_getData("followup","id","reference_id='".$_REQUEST['customer_id']."' AND isDelete=0","",0);
			while($followupidD = mysqli_fetch_assoc($followupid))	
			{
				$follId[] = $followupidD['id'];
			}
			if($follId)
			{
				$follId = implode(",",$follId);
				$NewWhre = "(ref_id='".$_REQUEST['customer_id']."' OR ref_id IN (".$follId.") ) AND isDelete=0 AND log_description!=''";
			}
			else
			{
				$NewWhre = "ref_id='".$_REQUEST['customer_id']."'  AND isDelete=0 AND log_description!=''";
			}
			/*followup*/
			/*quotation*/
			$Quotid = array();
			$Quotationid = $db->rp_getData("quotation_detail","id","inquiry_id='".$_REQUEST['customer_id']."'","",0);
			while($QuotationidD = mysqli_fetch_assoc($Quotationid))	
			{
				$Quotid[] = $QuotationidD['id'];
			}
			if($Quotid)
			{
				$Quotid = implode(",",$Quotid);
				$NewWhre1 = " OR (ref_id='".$_REQUEST['customer_id']."' OR ref_id IN (".$Quotid.") ) AND isDelete=0 AND log_description!=''";
			}
			else
			{
				$NewWhre1 = "ref_id='".$_REQUEST['customer_id']."'  AND isDelete=0 AND log_description!=''";
			}
			/*quotation*/
			$logdataR = $db->rp_getData("activity_log","*",$NewWhre.$NewWhre1,"activity_date ASC",0);	
		}
	    ?>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No.</th>
					<th>Main Module</th>
					<th>Sub module</th>
					<th>Type</th>
					<th>Ref No</th>
					<th>User Name</th>
					<th>Description</th>
					<th>Date and Time</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if($logdataR)
					{
						$count = 0;
						while($logdataD = mysqli_fetch_assoc($logdataR))
						{
							?>
								<tr>
									<th><?= ++$count ?></th>
									<th>Sales & Marketing</th>
									<th><?= $logdataD['module_name'] ?></th>
									<th><?= $logdataD['activity_type'] ?></th>
									<th><?= "#INQ/".$logdataD['ref_id'] ?></th>
									<!-- <th><?= $db->rp_getValue("dealer_distributor_network","name","sales_executive_id='".$logdataD['user_id']."'") ?></th> -->
									<th><?= $logdataD['user_name']?></th>
									<th><?= $logdataD['log_description']." (".$logdataD['flag'].")" ?></th>
									<th><?= date('d-m-Y H:i',strtotime($logdataD['activity_date'])) ?></th>
								</tr>
							<?php
						}
					}
					else
					{
						?>
			    			<tr><td colspan="8" class="text-center">No Data found!!</td></tr>
			    		<?php
					}
					?>
			</tbody>
		</table>
	</div>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>