<?php
$page_id=516;$page_slug='page_purchase_bill';
$page_slug="manage_sales_invoice";
$ctable 	= "fg_in_out";
$ctable1 	= "Job Work";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$_REQUEST['mode']=$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
include("../include/fg_in_out.class.php");
$objJobWork= new FGInOut();
$job_work_no	= JOB_WORK_NO.$db->getlastInsertId($ctable);
$job_work_date	= date('Y-m-d H:i:s');
$cname			= "";
$address		= "";
$item_name		= "";
$total			= "";
$phone			= "";
$count			= 1;
$isActive		= 0;
$name_of_excisable_good	="";
$tariff_sub_head_no	="";
$date_time_remo_1	="";
$date_time_remo_2	="";
$document_through	="";
$payment_terms	="";
$against_form	="";
$transport_by	="";
$packing	="";
$weight	="";
$postal_charge	="";
$asses_value	="";
$excise_duty	="";
$grandTotal	="";
$subtotal	="";
$remarks	="";
$taxs_selected=array();
$totalqty="";
$grand_total="";
$description1="";
$description2="";
$description3="";
$description4="";
$planning_id="";
$process_id="";
$fg_in_out_type=1;
$item_info		=array();
if(isset($_REQUEST['submit'])){
     $detail['vendor_id']		= $db->clean($_REQUEST['vname']);
     $detail['job_work_no']		= $db->clean($_REQUEST['job_work_no']);
	 $detail['job_work_date']	= $db->clean($_REQUEST['job_work_date']);
	 $detail['process_id']= $db->clean($_REQUEST['process_id']); 
	 $detail['planning_id']= $db->clean($_REQUEST['planning_id']); 
	 $detail['description1']= $db->clean($_REQUEST['description1']); 
	 $detail['description2']= $db->clean($_REQUEST['description2']); 
	 $detail['description3']= $db->clean($_REQUEST['description3']); 
	 $detail['description4']= $db->clean($_REQUEST['description4']); 
	 $detail['isActive']= 1;
	 $detail['isDelete']= 0;
	 
	//Insert Purchase Item 
	$fg_item_id	=	$_REQUEST['item_id']; 
	$qty	=	$_REQUEST['qty'];
	$fg_item_price	=	$_REQUEST['fg_item_price'];	
	$size[]=sizeof($fg_item_id);
	$size[]=sizeof($qty);
	$size[]=sizeof($fg_item_price);
	$value_check=sizeof($fg_item_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}	
	if($isValidArray && !empty($fg_item_id) && !empty($qty))
	{
		for($i=0;$i<sizeof($fg_item_id);$i++)
		{
			$item[]=array("id"=>$fg_item_id[$i],"qty"=>$qty[$i],"price"=>$fg_item_price[$i]);
		}
		$detail['items']=$item;
	}	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
			
			
		$reply=$objJobWork->createOut($detail);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("job_work_manage.php?msg=inserted");
		}else{
				 $db->addErrorMessage($reply['ack_msg']);
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objJobWork->UpdateJobwork($detail,$item);		
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		   $db->rp_location($ctable."_manage.php?msg=updated");
		}
		else{
				$db->addErrorMessage($reply['ack_msg']);
			} 		
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$detail['id']=$_REQUEST['id'];
		$reply=$objJobWork->GetJobWork($detail);
		//print_r($reply);
		$item_info=$objJobWork->GetJobWorkItem($detail);
		
	if($reply['ack']==1){
		
		$id=$_REQUEST['id'];
		$result=$reply['result'];
		extract($result);		
	}		
	if($item_info['ack']==1){			
		$store_inward_id=$_REQUEST['id'];
		$item_info=$item_info['result'];
	}
	else{
		$item_info=array();
	}

}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){		
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objJobWork->deleteFGOut($detail['id']);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);
	}
	$db->rp_location("job_work_manage.php?msg=deleted");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"status"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="statusChange" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"status"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where,0);
	echo json_encode(array("ack"=>1,"ack_msg"=>"Sales Invoice sent to client!!"));
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datetimepicker/jquery.datetimepicker.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<form role="form" action="" id="sales_invoice_form" method="post" onSubmit="return check_form();">
                                            
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo  $ctable;?>_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
			</div>
			<!-- Employee ID-->
			<div class="row">
                            <div class="col-md-12">
                                <!-- Begin: life time stats -->
                                <div class="portlet light portlet-fit portlet-datatable bordered">
                                   
                                    <div class="portlet-body">
                                        <div class="tabbable-line">
										
                                            <div class="tab-content">
											    <div class="tab-pane active" id="vendor_details">	
													<div class="row">
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="portlet blue-hoki box">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                       Vendor Details
																	</div>
                                                                   <div class="actions">
																	
																	<a data-toggle="modal" href="#addVendorModal" class="btn btn-default btn-sm">
																		<i class="fa fa-plus"></i> Add </a>
																</div>
                                                                </div>
																
                                                                <div class="portlet-body">
                                                                   
																	<?php
																	if($_REQUEST['mode']=="add")
																	{
																		
																	
																	?>
																	 <div class="row static-info">
                                                                        <div class="col-md-5 name"> Select Vendor </div>
                                                                        <div class="col-md-7 value"> 
																			<select onChange="return getVendorInformation(this.value)" class="form-control" name="vname" id="vname" class="vname" >
																				<option  value="">
        																		-- Select Vendor --
        																		</option>
																				<?php 
																					$ctable_r=$db->rp_getData("vendor","*","isDelete=0");
																					if($ctable_r)
																					{
																							while($ctable_d=mysqli_fetch_assoc($ctable_r))
																							{
																				?>
        																		<option  value="<?php echo $ctable_d['id']?>">
        																		<?php 
        																		echo $ctable_d['cname'];

        																		?>
        																		</option>
																					<?php } }

																					?>

																			</select>
																			
                                                                        </div>
                                                                    </div>
																	
                                                                    <div class="row static-info v_name">
                                                                        <div class="col-md-5 name v_name"> Vendor Name: </div>
																		<div class="col-md-7 value" id="name_value"> </div>
                                                                        
                                                                    </div>
																	<div class="row static-info phone">
                                                                        <div class="col-md-5 name"> Phone : </div>
                                                                        <div class="col-md-7 value" id="phone_value">
																		</div>
                                                                    </div>
                                                                    <div class="row static-info address">
                                                                        <div class="col-md-5 name"> Address : </div>
                                                                       <div class="col-md-7 value" id="address_value"></div>
                                                                    </div>
                                                                    <?php
																	}
																	else{
																	?>
																	 <div class="row static-info v_name">
                                                                        <div class="col-md-5 name v_name"> Vendor Name: </div>
																		<div class="col-md-7 value" id="name_value"><?php 
																		echo $vname;   ?> </div>
                                                                        
                                                                    </div>
																	<div class="row static-info phone">
                                                                        <div class="col-md-5 name"> Phone : </div>
                                                                        <div class="col-md-7 value" id="phone_value">
																		<?php echo $phone; ?> </div>
                                                                    </div>
                                                                    <div class="row static-info address">
                                                                        <div class="col-md-5 name"> Address : </div>
                                                                       <div class="col-md-7 value" id="address_value"><?php echo $address; ?></div>
                                                                    </div>
																	<?php
																	}
																	?>
                                                                </div>
                                                            </div>
                                                        </div>
														 <div class="col-md-6 col-sm-12">
                                                            <div class="portlet grey-cascade box">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                      Job Work Detail
																	</div>
                                                                   
                                                                </div>
                                                                <div class="portlet-body" style="min-height:150px;">
                                                                    <div class="row">
																		<div class="col-md-12 col-sm-12">
																			<div class="row">
																			<br/>
																			<div class="col-md-6 col-sm-6 ">
																				<div class="form-group">
																					<label class="control-label">Job Work Date</label>											
																					 <input  class="form-control" type="text" name="job_work_date" id="job_work_date" value="<?php echo $job_work_date; ?>"/>
																				</div>
																				</div>
																				<div class="col-md-6 col-sm-6">
																				<div class="form-group">
																					<label class="control-label">Job Work No.</label>
																					<input type="hidden" name="job_work_no" id="job_work_no" value="<?php echo $job_work_no; ?>"/>
																					<input  class="form-control" type="text" disabled="disabled" name="job_work_no" id="job_work_no" value="<?php echo $job_work_no; ?>"/>
																				</div>
																				</div>
																				
																				
																			
																			</div>
																			<div class="row">
																			<br/>
																			<div class="col-md-6 col-sm-6 ">
																				<div class="form-group">
																					<label class="control-label">Process</label>											
																					<select  id="process_id" name="process_id" type="text" class="form-control" onChange="return resetPlanning(this)">
																						<option value="">Select Process </option>
																						<?php
																							$process_list_d=$db->rp_getData('production_process',"*","1=1 AND isDelete=0","",0);
																							while($process_list_r=mysqli_fetch_assoc($process_list_d))
																							{
																								
																								
																						?>
																						<option  <?php echo ($process_id==$process_list_r['id'])?"selected":"" ; ?> class="price_option" value="<?php echo $process_list_r['id']?>"><?php echo $process_list_r['process_name'];?>
																						</option>
																								<?php
																							}
																						?>
																					</select>
																				</div>
																				</div>
																				<div class="col-md-6 col-sm-6 ">
																					<div class="form-group">
																						<label class="control-label">Planning</label>	
																						<select  id="planning_id" name="planning_id" type="text" class="form-control" onChange="return getPlanningItems(this.value)">
																							<option value="">Select Planning </option>
																							<?php
																								$planning_list_d=$db->rp_getData('fg_planning_info',"*","isDelete=0 AND (status=2 OR status=0)","",0);
																								while($planning_list_r=mysqli_fetch_assoc($planning_list_d))
																								{
																									
																									
																							?>
																							<option  <?php echo ($planning_id==$planning_list_r['id'])?"selected":"" ; ?> class="planning_id" value="<?php echo $planning_list_r['id']?>"><?php echo $planning_list_r['planning_title'];?>
																							</option>
																									<?php
																								}
																							?>
																						</select>
																					</div>
																					</div>
																				
																			</div>
																			
																			</div>
																	</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12">
                                                            <div class="portlet grey-cascade box">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                        Finish Good Items </div>
                                                                   
                                                                </div>
                                                                <div class="portlet-body">
											<div class="row">
												<div class="col-md-12">
													<div class="form-body">
														
														<div class="row">
															
															<div class="col-md-3">
																<div class="form-group">
																	<select  id="fg_item_name" name="fg_item_name[]" onChange="return changeQty(this);" type="text" class="form-control" placeholder="Select Finish Good Item.">
																		<option value="">Select Finsh Good Item </option>
																		
																	</select>
																	<p class="help-block"></p>												
																</div>
															</div>
															<div class="col-md-2">
																<div class="form-group">
																	<div class="input-group">
																		<input  id="qty" name="" type="text" class="form-control" placeholder="Enter Qty."/>
																		
																		<span class="input-group-addon" id="planning_qty">
																			/--
																		</span>
																		<p class="help-block"></p>
																	</div>																
																	
																</div>
															</div>
															<div class="col-md-2">
																<div class="form-group">	
																	<input  id="price" name="" type="text" class="form-control" placeholder="Enter Price."/>
																	<p class="help-block"></p>
																</div>
															</div>
															<div class="col-md-1">
																<div class="form-group">	
																	<button type="button" data-mode="add_specification" name="add_fg_item" id="add_fg_item" class="btn pull-right green"><i class="fa fa-user-plus"></i>&nbsp;Add</button>
																</div>
															</div>	
																<div class="col-md-12">
													<div class="portlet-body">
														<div class="table-responsive">
															<table class="table table-hover table-bordered table-striped" id="extra_spec_item">
																<thead>
																	<tr>
																		<th width="5%"> No. </th>
																		<th width="30%" class="text-center"> FG Item Name</th>
																		<th width="5%" class="text-center"> Quantity</th>
																		<th width="10%" class="text-center"> Price</th>
																		<th width="10%" class="text-center"> Total</th>
																		<th width="5%"> Action</th>
																	</tr>
																	
																</thead>
																<tbody>
																<?php

																	if(!empty($item_info))
																	{
																		 $count=1;
																		foreach($item_info as $i)
																		{
																			//print_r($i);
																		?>
																		<tr class='issued_item'>
																			<td>
																			<?php echo $count; ?><input type='hidden' name='count[]' id='count' value='"+count+"'><input name='item_id[]' id='item_id'  type='hidden' class='item_id' value='<?php echo $i['fg_item_id'] ?>'></td>
																			<td ><?php echo $i['fg_item_name'] ?></td><td class="text-center"><input type='hidden' name='qty[]' class='qty' type='text' value='<?php echo $i['qty'] ?>'><?php echo $i['qty'] ?></td>
																			<td class="text-center"><input type="hidden" name="fg_item_price[]" value="<?php echo $i['fg_item_price'] ?>" class="fg_item_price"><?php echo $i['fg_item_price'] ?></td>
																			<td><input type='text' disabled name='total[]' class='total text-right form-control' value='<?php echo $i['total']; ?>'></td>
																			<td><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a></td>
																		</tr>
																  <?php
																   $count++;
																		}
																		$hide_fg="hidden";
																	}
																	else
																	{
																		$hide_fg="";
																	}
																	?>
																	<tr class="no-row-item text-center <?php echo $hide_fg; ?>"><td colspan="6"><i class="fa fa-cubes"></i> No Items</td> </tr>	
																
																</tbody>
																<tfoot>
																	<tr>
																	<td></td>
																	<td class="text-right">Total Qty </td>
																	<td><input type='text' id='totalQty' class='totalQty form-control text-center' disabled value="<?php echo $totalqty;?>" onChange='' name='totalQty'></td>
																	<td >Total </td>
																	<td><input type='text' id='finalTotal' class='finalTotal form-control  text-right' disabled value="<?php echo $grand_total;?>" onChange='' name='finalTotal[]'></td>
																	</tr>
																	
																</tfoot>
																
															</table>
															
														</div>
		
													</div>
										
													</div>	
												
															</div>	
														</div>	
													</div>	
												</div>
											</div>
										 </div>
                                                        </div>
														
																
														
														<div class="col-md-12 col-sm-12">
                                                            <div class="portlet grey-cascade box">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                       Terms And Conditions
																	</div>
                                                                   
                                                                </div>
                                                                <div class="portlet-body">
                                                                   
																	
																	<div class="row">
																		<div class="col-md-6">
																			<div class="form-group">
																			<label>Description Of Goods <code>*</code></label>
																			<textarea type="text" class="form-control" name="description1" id="description1" value="" >	<?php echo $description1; ?> </textarea>
																			<p class="help-block"></p>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																			<label><small>Quantity Dispatch(Nos/Weight/Liter/Meter) as entered in account <small>    </label>
																			<textarea type="text" class="form-control" name="description2" id="description2" value="" >	<?php echo $description2; ?> </textarea>
																			<p class="help-block"></p>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																			<label> Nature of processing / manufacturing done    </label>
																			<textarea type="text" class="form-control" name="description3" id="description3" value="" >	 
																			<?php echo $description3; ?>
																			</textarea>
																			<p class="help-block"></p>
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																			<label> Quantity waste material return to the parent factory   </label>
																			<textarea type="text" class="form-control" name="description4" id="description4" value="" >	 
																			<?php echo $description4; ?>
																			</textarea>
																			<p class="help-block"></p>
																			</div>
																		</div>
																	</div>
																</div>
                                                            </div>
                                                        </div>
														
                                                    </div>
													
													
                                                    <div class="row">
													<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
														<button type="submit" name="submit" class="btn green">Submit</button>
														<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
                                                    </div>
                                                    </div>
											
                                            </div>
                                              
										</div>
									</div>
								</div>
							</div>
							<!-- End: life time stats -->
						</div>
                        </div>
		</div>
</form>	
<div class="modal fade" id="addVendorModal" tabindex="-1" role="addVendorModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Create Quick Vendor</h4>
			</div>
			<div class="modal-body"> Modal body goes here </div>
			<div class="modal-footer">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
				<button type="button" class="btn green">Save changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
	</div>	
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
var count=0;
$(function(){
	$("#price").numeric();
	$("#qty").numeric();
	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });
	
	$('#job_work_date').datepicker({ dateFormat: 'yy/mm/dd', datetimepicker: true, autoclose: true });	
	$("#item_name").select2({
	});
	$("#datatable_1").on('click','.delete',function(){
       $(this).closest('tr').remove();
	   recalculateFinalValues();
     });
	$("#extra_spec_item").on('click','.delete',function(){
       $(this).closest('tr').remove();
	   recalculateFinalValues();
	   maintainDatatable();
     });
	$("#add_fg_item").click(function (){
	count=++count;
	var item_id=$("#fg_item_name").val();
	var price=$("#price").val();
	var item_name=$('#fg_item_name option:selected').text();
	var qty=$("#qty").val();
	var total=qty*price;
	if(item_id!=0 && qty!="" && qty!=0){
		var duplicate=$("input.item_id[value='"+item_id+"']").length;
		var planning_qty=$("#fg_item_name").find("option:selected").data("planning-qty");
		if(duplicate==0)
		{
			if(qty<=planning_qty)
			{
				var new_row2="<tr class='issued_item'><td>"+count+"<input type='hidden' name='count[]' id='count' value='"+count+"'><input name='item_id[]' id='item_id'  type='hidden' class='item_id' value='"+item_id+"'></td><td>"+item_name+"</td><td class='text-center'><input type='hidden' name='qty[]' class='qty text-center' type='text' value='"+qty+"'>"+qty+"</td><td class='text-center'><input type='hidden' name='fg_item_price[]' class='fg_item_price text-right' type='text' value='"+price+"'>"+price+"</td><td><input type='text'  disabled name='total[]' class='total form-control text-right' value='"+total.toFixed(2)+"'></td><td><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a></td></tr>";
				$("#extra_spec_item").find('tbody').append(new_row2);
				maintainDatatable();
				
				$('#fg_item_name').focus();
				$("#fg_item_name").select2("val","");
				$("#planning_qty").html("/--");
				$("#price").val("");
				$("#qty").val("");
				recalculateFinalValues();
			}
			else
			{
				toastr.error("You can't enter more than planned qty");
			}	
			
		}
		else
		{
			toastr.error("Product already there remove first to add it again!!");
		}
		

	}
	else{
		toastr.error('Please Select Atleast One Product With Valid Qty');
		
	}

	}) 

});
function maintainDatatable()
{
		if($("#extra_spec_item").find("tbody").find("tr.issued_item").length>=1)
		{
			$("tr.tax_containers").show();
			$("tr.no-row-item").hide();
		}
		else
		{
			$("tr.tax_containers").hide();
			$("tr.no-row-item").removeClass("hidden");
			$("tr.no-row-item").show();
		}
	}

function check_form()
{
	
	var isValid=true;
	if($("#vname").val()=="" || $("#vname").val().split(" ").join("")==""){
		aj.error("vname","Please Select Vendor","add_error");
		toastr.error("Select Vendor.","Error!!");
		isValid=false;
	}
    if($("#finalTotal").val()==0)
	{
		toastr.error("Select Atleast One Item.","Error!!");
		isValid=false;
	}
	if(isValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function loadDataTable()
{
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "1%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "15%" },				  
			  { "sWidth": "10%","bSortable": false }
			],
		 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});

}
    
$(document).ready(function(){
	
});
function recalculateFinalValues()
{
	var sum=0;
	var sumQty=0;
	$('.total').each(function () {
		total=parseFloat($(this).val());
		sum +=total;		
	});	
	$('.qty').each(function () {
		qty=parseInt($(this).val());
		sumQty +=qty;
	
	});
	$("#finalTotal").val(''+sum);
	$("#totalQty").val(''+sumQty);
}
function getVendorInformation(id){
	$.ajax({
		url:"search_vendor_for_job_work.php",
		data:{id:id},
		success:function(result){
			result=$.parseJSON(result);
			customer_info=result.result.customer;
			$("#phone_value").html(customer_info.phone);
			$("#name_value").html(customer_info.cname);
			$("#address_value").html(customer_info.address);
			
		}
	})
}
function getPlanningItems(id){
	var process_id=$("#process_id").val();
	if(process_id!="")
	{
		$.ajax({
		url:"job_work_ajax_function.php",
		data:{id:id,process_id:process_id,mode:"get_planning_items"},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			$("#fg_item_name").html(result.result);
			else
			toastr.error(result.ack_msg);	
			
		}
	})
	}
	else
	{
		toastr.error("Select Process First!!");
	}
	
}
function changeQty(spn){
	if($(spn).val()!="")
	{
		$("#planning_qty").html("/"+$(spn).find("option:selected").data("planning-qty"));
	}
	else
	{
		$("#planning_qty").html("/--");
	}
}

function resetPlanning(spn)
{
	$("#planning_id").select2("val","");
	$("#fg_item_name").html("<option value=''>-- Select Finish Good Item --</option>");
	$("#fg_item_name").select2();
}
</script>
</body>
</html>