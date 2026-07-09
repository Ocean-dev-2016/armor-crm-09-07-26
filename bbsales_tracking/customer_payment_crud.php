<?php
$page_id=575;$page_slug='payment';
$page_slug="manage_super_stockist";
$ctable 	= "customer_payment";
$ctable1 	= "Customer Payment";
$main_page 	= $ctable;
$page 		= $ctable."_crud";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Account"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.customer_payment.php");
$objCustomerPayment= new CustomerPayment();
$customer_id	= "";
$dispatch_id			= "";
$amount			= "";
$payment_date 			= "";
$payment_type		= "";
$remark 			= "";
$paid_amount 			= "";
if(isset($_REQUEST['submit'])){
	$detail['customer_id']		= $db->clean($_REQUEST['customer_id']);
	$detail['dispatch_id']			= $db->clean($_REQUEST['dispatch_id']);
	$detail['paid_amount']			= $db->clean($_REQUEST['paid_amount']);
	$detail['old_paid_amount']			= $db->clean($_REQUEST['old_paid_amount']);
	$detail['payment_date']				= $db->clean($_REQUEST['payment_date']);
	$detail['payment_type']				= $db->clean($_REQUEST['payment_type']);
	$detail['remark']				= $db->clean($_REQUEST['remark']);
	$detail['isActive']			= 1;
	//print_r($detail);exit;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		//print_r($detail);exit;
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objCustomerPayment->InsertCustomerPayment($detail);
			if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
			}
			else{
				 $db->addErrorMessage($reply['ack_msg']);				 
			}
	}
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$detail['id']=$_REQUEST['id'];
		$reply=$objCustomerPayment->UpdateCustomerPayment($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
		}
		else{
				 $db->addErrorMessage($reply['ack_msg']);
			} 
		
	}
}

//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	//print_r($detail);exit;
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where,"",0);
	$detail['id']=$_REQUEST['id'];
	$payment=$db->rp_getValue("payment","receipt_no","id=".$_REQUEST['id']." AND isDelete=0",0);
	$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Receipt Nubmer"."- ".ucwords($payment).'&nbsp';	
	$reply=$objCustomerPayment->EditCustomerPayment($detail);
	if($reply['ack']==1){
		$result=$reply['result'];
		extract($result);
	}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objCustomerPayment->DeleteCustomerPayment($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location($ctable."_manage.php?msg=inserted");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);		
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
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
<link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				
				<h1><a href="<?php echo  $ctable;?>_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
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
			
<form id="inquiry_form_distributor" onSubmit="return check_form()" action="" method="post">

<div class="row">
	<div class="col-md-12">
		<div class="portlet grey-cascade box">
			<div class="portlet-title">
				<div class="caption">
				   <i class="fa fa-user"></i> &nbsp; Payment
				</div>
			</div>
			<div class="portlet-body">
			   
				<div class="row">																													
						
						<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<?php
									if($_REQUEST['mode']=='edit')
									{
										$disable='disabled=disabled';
									}
									else
									{
										$disable='enabled=enabled';
									}
									?>
								<label for="customer">Customer<code>*</code></label>
								<select class="form-control" name="customer_id" id="customer_id" onChange="getBill_no(this.value);"  <?php echo $disable;?>>
									
									<option value="">Select Customer</option>
									<?php
									 
										$customer_d=$db->rp_getData('dispatch_detail',"DISTINCT customer_name,customer_id,company_name","isDelete=0  AND isActive=1 AND order_type='normal_user'","",0);
										while($customer_r=mysqli_fetch_assoc($customer_d))
										{
											?>
											
											<option <?php if($_REQUEST['mode']=='edit'){echo ($customer_id==$customer_r['customer_id'])?"selected":"" ;} ?> value="<?php echo $customer_r['customer_id']?>">
											<?php echo $customer_r['company_name'];?>
											</option>
											<?php
										}
										
										?>
										</select>
										<!--input type="hidden"  class="form-control" name="customer_id" id="customer_id" value="<?php echo $customer_id;?>" onChange="getBill_no(this.value);" -->
								<p class="help-block"></p>	
							</div>
							</div>
							<?php
								if($_REQUEST['mode']=='edit')
								{
									$disable='disabled';
								}
								else
								{
									$disable='enable';
								}
							?>
							<div class="col-md-6">
							<div class="form-group">
								<label for="address">Bill No.<code>*</code></label>
								<!--Get dispatch no from getBill_no(this.value)function insert in db dispatch_id-->
								<select <?php echo $disable;?> class="form-control" name="dispatch_id" id="dispatch_id" onChange="getAmount(this.value);">
									<option value="">Select Bill No.</option>
									<?php
									if($_REQUEST['mode']=='edit')
									{
									$disaptch_r = $db->rp_getData("dispatch_detail","*","customer_id='".$customer_id."'","",0);
									if(mysqli_num_rows($disaptch_r)>0){
										while($disaptch_d= mysqli_fetch_array($disaptch_r)){
										?>
									<option value="<?php echo $disaptch_d['id']; ?>" <?php if($disaptch_d['id']==$dispatch_id){?> selected <?php } ?>><?php echo $disaptch_d['dispatch_no']; ?></option>
									<?php
										}
									}
									}
									?>
									
								</select>
									
								<p class="help-block"></p>	
							</div>
							</div>
						</div>
							
							<div class="row">																			
								<div class="col-md-6">
									<div class="form-group">
									<label>Amount <code>*</code></label>
											<div class="input-group">
											<input type="text" class="form-control" name="paid_amount" id="paid_amount" value="<?php echo $paid_amount;?>" >
											
											<input type="hidden" class="form-control" name="old_paid_amount" id="old_paid_amount" value="<?php echo $paid_amount;?>" >
											
											<?php 
											//show remaining amount
										//	$remaining_Amount=$db->rp_getValue("customer_payment","amount_remaining_that_time","id='".$_REQUEST['id']."'");
											?>
											<span class="input-group-addon" id="amount"><?php if($_REQUEST['mode']=='edit'){ echo $amount_remaining_that_time;}else{?>/--<?php
											}
											?>
											</span>
											</div>
											<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									<label >Date<code>*</code></label>
									<input type="text" class="form-control" name="payment_date" id="payment_date" data-date-end-date="0d" value="<?php echo $payment_date; ?>">
										<p class="help-block"></p>		
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									 <div class="form-group">
                                            <label>Payment BY<code>*</code> </label>
                                            <select class="form-control" name="payment_type" id="payment_type"  value="<?php echo $payment_type;?>" autofocus >
												<option value="">--- Select Payment Type ---</option>
                                                <option value="case" <?php if("case"==$payment_type){echo "selected";}?>>By Cash</option>
                                                <option value="cheque" <?php if("cheque"==$payment_type){echo "selected";}?>>By Cheque</option>
                                                <option value="online" <?php if("online"==$payment_type){echo "selected";}?>>Online</option>
                                                <option value="other" <?php if("other"==$payment_type){echo "selected";}?>>Other</option>
                                             </select>
										</div>
								</div>	
							<div class="col-md-6">
								<div class="form-group">
									<label>Remark </label>
									<textarea class="form-control" name="remark" id="remark" ><?php echo $remark; ?></textarea>
									<p class="help-block"></p>	
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
</form>
										</div>
									</div>
								</div>
								
		</div>
		</div>
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery.quicksearch.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
//-------#numeric field  validation----------//
 $("#paid_amount").numeric();
 //$('#payment_date').datepicker({  datepicker: true, autoclose: true ,minDate:0 });
 $('#payment_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy', maxDate:0 });
//---------------get dispatch_id after selecting customer--------------------------------//
function getBill_no(val)
{
        $.ajax({
        type: "POST",
        url: "find_bill.php",
        data:'customer_id='+val,
        success: function(data){
        $("#dispatch_id").html(data);
        //$("#bill_no").multiSelect("refresh");
        }
    });
}
$("#submit").click(function(){
	
});
//for enter only Number no '-' or '.'//
$(document).ready(function() {
$("#paid_amount").keyup(function(event) {
if ( event.keyCode == 46 || event.keyCode == 8 ) {
// let it happen, don't do anything
} else if (/\D/g.test(this.value)) {
	alert("sorry!! Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});
});

//----------------------Get Amount inspan-------------------------------------------------//
function getAmount(spn){
	if($(spn).val()!="")
	{
		$("#amount").html($("#dispatch_id").find("option:selected").data("grand_total"));
	}
	else
	{
		$("#amount").html("/--");
	}
}
//----------------------------------------//
$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) {$(this).parent().find('.help-block').html(""); $(this).parent().removeClass("has-error"); } }); });

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")==""){		
		vd=aj.error('customer_id',"Please Select User.","add_error");
		isValid=false;
	}
	if($("#dispatch_id").val()=="" || $("#dispatch_id").val().split(" ").join("")==""){		
		vd=aj.error('dispatch_id',"Please Select Bill No.","add_error");
		isValid=false;
	}
	if($("#paid_amount").val()=="" || $("#paid_amount").val().split(" ").join("")==""){
			aj.error('paid_amount','Please enter Amount!!','add_error');
			isValid=false;
	}
	if($("#payment_date").val()=="" || $("#payment_date").val().split(" ").join("")==""){
			aj.error('payment_date','Please Select Payment Date!!','add_error');
			isValid=false;
		
	}
	if($("#payment_type").val()=="" || $("#payment_type").val().split(" ").join("")==""){
			aj.error('payment_type','Please Select Payment Type.','add_error');
			isValid=false;
		
	}
//-------------------------get alert if paid amount is big than remaining amount-----------------------------//	
	<?php 
	
	/*if($_REQUEST['mode']=='add')
	{
	?>
	var amount=$("#amount").html($("#dispatch_id").find("option:selected").data("grand_total"));
	<?php
	}
	else{
		?>
		var amount=$("#amount").html();
		<?php
		}*/
	?>
	var amount=$("#amount").html();
	var paid_amount=$("#paid_amount").val();
	
	amount=(amount!="")?parseFloat(amount):0;
	paid_amount=(paid_amount!="")?parseFloat(paid_amount):0;
	if(paid_amount<=amount){
				/*amount=amount-paid_amount;
				$("#amount").html(amount);
				isValid=true;*/
	}
	else
	{
		toastr.error("You can't enter more than Remaining Payment");
		$("#paid_amount").val("");
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

var searchName="";
	// used when user change row limit
	function changeDisplayRowCount(numRecords) 
	{
		displayRecords(numRecords, 1);
	}
	// used when user change row limit
	function changeDisplayRowCountContact(numRecords) 
	{
		displayContactRecords(numRecords, 1);
	}
	function displayRecords(numRecords) 
	{
	var searchName 	= ($("#searchName").val()==undefined)?"":$("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	
}
function displayContactRecords(numRecords) 
	{
	var searchName 	= ($("#searchContactName").val()==undefined)?"":$("#searchContactName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results2" ).html("");
	$("#results2" ).load( data_cotact_person_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,function(){
		loadContactDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results2").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div2").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?cid="+cid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
	$("#results2").on( "change", "#numRecords2", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_cotact_person_url+"?jid="+jid+"&show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadContactDataTable();
		});
		
	});
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
				  { "sWidth": "15%" },
				  { "sWidth": "10%" },			
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-sitemap '></i> &nbsp; No Branch Found"},
		});
    }
	function loadContactDataTable()
	{
		$('#datatable_2').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "0.4%" }, 
				  { "sWidth": "10%" },
				  { "sWidth": "5%" },
				  { "sWidth": "5%" },			
				  { "sWidth": "8%" },							  		
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-user-plus '></i> &nbsp; No Contact Found"},
		});
    }
	function searchByName()
	{
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
	}
	function clearSearchByName()
	{
		searchName = "";
		$("#searchName").val("");
		displayRecords(100,1);
	}
	function searchByContactName()
	{
	searchName = $("#searchContactName").val();
	displayContactRecords(100,1);
	return false;
	}
	function clearSearchByContactName()
	{
		searchName = "";
		$("#searchContactName").val("");
		displayContactRecords(100,1);
	}


</script>

</body>
</html>