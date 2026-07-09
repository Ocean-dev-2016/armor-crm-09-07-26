<?php
$page_id=519;$page_slug='page_inward_store_page';
$page_slug="manage_inward_store";
$ctable 	= "inward_store";
$ctable1 	= "Inward Store";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
include("../include/inward_store_with.class.php");
$objStore= new Store();

$vendor_name	= "";
$remark	= "";
$isActive		= 0;
$count		= 0;

$category_type="";
$item_name="";
$item_qty="";
$item_code="";
$sub_total ="";
$total_qty ="";
$total_qty ="";
$tax_total = "";
$item_info=array();
$phone="";
$email="";
$address="";
$grand_total="";

if(isset($_REQUEST['submit'])){
		 
	$detail['vendor_name']		= $db->clean($_REQUEST['vendor_name']); 
	$detail['phone']		= $db->clean($_REQUEST['phone']);
	$detail['email']		= $db->clean($_REQUEST['email']);
	$detail['item_name']		= $db->clean($_REQUEST['product_name']);
	$detail['remark']		= $db->clean($_REQUEST['remark']);
	$detail['po_no']		= $_REQUEST['purchase_order_id'];
	$isActive					= 1;
	$detail['isDelete']			= 0;
	//$po_no=$_REQUEST['purchase_order_id'];
	
	//Insert Purchase Item 
	
	
	$category_id=$_REQUEST['category_id']; 
	$item_id=$_REQUEST['item_id']; 
	$po_no=$_REQUEST['purchase_order_id']; 
	$item_name=$_REQUEST['item_name'];
	$item_code=$_REQUEST['item_code'];
	$order_qty=$_REQUEST['order_qty'];
	$received_qty=$_REQUEST['received_qty']; 
	$remaining_qty=$_REQUEST['remaining_qty']; 
	$price=$_REQUEST['price'];
	$total=$_REQUEST['total']; 
	//var_dump($_REQUEST);exit;
	$size[]=sizeof($category_id);
	$size[]=sizeof($item_id);
	$size[]=sizeof($po_no);
	$size[]=sizeof($item_name);
	$size[]=sizeof($item_code);
	$size[]=sizeof($order_qty);
	$size[]=sizeof($remaining_qty);
	$size[]=sizeof($received_qty);
	$size[]=sizeof($price);
	$size[]=sizeof($total);
	
	$value_check=sizeof($item_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}
		
	if($isValidArray  && !empty($po_no) && !empty($item_name) && !empty($item_code) && !empty($order_qty)&& !empty($received_qty)&& !empty($price))
	{
		for($i=0;$i<sizeof($item_id);$i++)
		{
			$item[]=array("category_id"=>$category_id[$i],"item_id"=>$item_id[$i],"po_no"=>$po_no[$i],"item_name"=>$item_name[$i],"item_code"=>$item_code[$i],"order_qty"=>$order_qty[$i],"received_qty"=>$received_qty[$i],"remaining_qty"=>$remaining_qty[$i],"price"=>$price[$i]);
			
		}
		//print_r($item);exit;
	}
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		// print_r($item);exit;
		$reply=$objStore->InsertInwardStore($detail,$item);
		//$reply_qty=$objStore->Received_qty($po_id,$category_id,$product_id);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("manage_".$ctable.".php?msg=inserted");
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
		//print_r($item);exit;
		$reply=$objStore->UpdateInwardStore($detail,$item);
		//print_r($reply);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("manage_".$ctable.".php?msg=updated");
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
		$reply=$objStore->GetInwardStore($detail);
		$item_info=$objStore->GetInwardStoreItem($detail);
		//print_r($item_info);exit;
		
	if($reply['ack']==1){
		
		$id=$_REQUEST['id'];
		$result=$reply['result'];
		extract($result);		
	}	
	
	if($item_info['ack']==1){	
		
		$store_inward_id=$_REQUEST['id'];
		$item_info=$item_info['result'];
		//print_r($item_info);exit;
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
	$reply=$objStore->DeleteInwardStore($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location("manage_".$ctable.".php?msg=inserted");
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
	$db->rp_location("manage_".$ctable.".php?msg=updated");
}


if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location("manage_".$ctable.".php?msg=updated");
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="manage_<?php echo  $ctable;?>.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
						<div class="portlet-title">
							<div class="caption">
								
								<span class="caption-subject font-dark sbold uppercase"> Inward Store With PO                                               
								</span>
							</div>
							<div class="actions">
								
							</div>
						</div>
						<div class="portlet-body">
							<div class="tabbable-line">
								<ul class="nav nav-tabs nav-tabs-lg">
									<li class="active">
										<a href="#vendor_details" data-toggle="tab"> Details </a>
									</li>
									
								</ul>
								<div class="tab-content">
								<form role="form" action="" method="post" onSubmit="return check_form();">
									<div class="tab-pane active" id="vendor_details">	
										<div class="row">
											<div class="col-md-6 col-sm-12">
												<div class="portlet grey-cascade box">
													<div class="portlet-title">
														<div class="caption">
														   Vendor Details
														</div>
													   
													</div>
													<div class="portlet-body">
														<div class="row static-info">
															<div class="col-md-5 name"> Select Vendor </div>
															<div class="col-md-7 value"> 
																<select class="form-control" name="vendor_name" id="vendor_name" class="vendor_name">
																<option value=""> -- Select vendor Name --</option>
																	<?php 
																		$vendor_list_d=$db->rp_getData('vendor',"*","1=1","",0);
																		while($vendor_list_r=mysqli_fetch_assoc($vendor_list_d))
																		{
																			
																			?>
																			<option <?php echo ($vendor_name==$vendor_list_r['cname'])?"selected":"" ; ?> value="<?php echo $vendor_list_r['id']?>" >
																			<?php $vendor_id=$vendor_list_r['cname'];
																			echo $vendor_id;
																			
																			?>
																			</option>
																			<?php
																		}
																	?>
																</select>
																
															</div>
														</div>
														<div class="row static-info v_name">
															<div class="col-md-5 name v_name"> Vendor Name: </div>
															<div class="col-md-7 value" id="name_value"><?php echo $vendor_name; ?> </div>
															
														</div>
														<div class="row static-info phone">
															<div class="col-md-5 name"> Phone : </div>
															<div class="col-md-7 value" id="phone_value"><?php echo $phone;?> </div>
														</div>
														<div class="row static-info email">
															<div class="col-md-5 name"> Email : </div>
															<div class="col-md-7 value" id="email_value"><?php echo $email;?></div>
														</div>
														<div class="row static-info address">
															<div class="col-md-5 name"> Address : </div>
														   <div class="col-md-7 value" id="address_value"><?php echo $address;?></div>
														</div>
														
														
													</div>
												</div>
											</div>
											<div class="col-md-6 col-sm-12">
												<div class="portlet grey-cascade box">
													<div class="portlet-title">
														<div class="caption">
														   Vendor POs </div>
														
													</div>
													<div class="portlet-body">
													<div class="row">
													<br/>
													<div class="col-md-9">
													<div class="mt-radio-list">  
													<?php if($_REQUEST['mode']=="edit"){?>																
														<div class='form-group'>
														<label class='mt-radio mt-radio-outline'>
														<input name='po_ids[]' id='po_id_row' class='list_row' value="<?php echo $po_no;?>"  type='radio' checked=''> &nbsp;PO Id : <?php echo $po_no;?>&nbsp;<span class='label label-success'><?php echo $po_date;?></span>
														<span></span>
														</label>
													</div>
													<?php } ?>
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
															<i class="fa fa-cogs"></i>Purchase Order Items </div>
														<div class="actions">
															<a href="javascript:;" class="btn btn-default btn-sm">
																<i class="fa fa-pencil"></i> Edit </a>
														</div>
													</div>
													<div class="portlet-body">
														<div class="table-responsive">
															<table class="table table-hover table-bordered table-striped" id="po_items_list">
																<thead>
																	<tr>
																		<th> No. </th>
																		<th> Product Name</th>
																		<th> Product Code</th>
																		<th> Ordered Quantity </th>
																		<th> Received Quantity </th>
																		<th> Remaining Quantity </th>
																		<th> Per Pice Price </th>
																		<th> Total </th>
																	</tr>
																	<?php 
										
																	if(!empty($item_info))
																	{
																		
																		foreach($item_info as $i)
																		{
																										
																		?>
																		<tr>
																			<td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count;?>" class='count'></td>
																			<input name='inward_store_id[]'  type='hidden' value="<?php echo $i['id'];?>">
																			<input type='hidden' name='purchase_order_id' value="<?php echo $i['po_no'];?>">
																			<input type='hidden' name='item_id[]' value="<?php echo $i['item_id']; ?>">
																			<input type='hidden' name='category_id[]' value="<?php echo $i['category_id']; ?>">
																			
																			<td><input name='item_name[]' class='item_name' type='hidden' value="<?php echo $i['product_name'];?>"><?php echo $i['product_name'];?></td>
																			<td><input name='item_code[]' class='item_code' type='hidden' value="<?php echo $i['product_code'];?>"><?php echo $i['product_code'];?></td>
																			<td><input name='order_qty[]' type='hidden' value="<?php echo $i['order_qty'];?>" class='order_qty'><?php echo $i['order_qty'];?></td>
																			
																			<td><input name='received_qty[]' type='text' value="<?php echo $i['receive_qty'];?>" onChange='recalculateRow(this)' class='qty'></td>

																			<td><input name='remaining_qty[]' type='text' value="<?php echo $i['remaining_qty'];?>" onChange='recalculateRow(this)' disabled class='remaining_qty'></td><td><input name='price[]'  type='hidden' value="<?php echo $i['product_price'];?>" class='price'><?php echo $i['product_price'];?></td>
																			<td><input type='text' class='total' disabled onChange='recalculateRow(this)' name='total[]' id='total' value="<?php echo $i['product_total']; ?>"></td>
																		</tr>
																		<?php
																		}
																	}
																	?>
																</thead>
																<tbody>
																
																</tbody>
																<tfoot>
																	<tr><td colspan='3'></td>
																	<td colspan='1'>Total Qty </td>
																	<td> 
																	
																		<input type='text' id='finalQty' disabled name='finalQty[]' value="<?php echo $total_qty;?>" onChange=''></td>	
																		<td></td>
																	<td colspan='1'>SubTotal </td>
																	<td><input type='text' id='finalTotal' disabled value="<?php echo $grand_total;?>" onChange='' name='finalTotal[]'></td>
																	</tr>
																	</tfoot>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
										<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
											<button type="submit" name="submit" class="btn green">Submit</button>
											<button type="button" class="btn btn-default" onClick="window.location.href='manage_<?php echo $ctable; ?>.php'">Back</button>
										</div>
										</div>
								</form>		
								</div>
								  
							</div>
						</div>
					</div>
				</div>
				<!-- End: life time stats -->
			</div>
			</div>
		</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
<?php
if($_REQUEST['mode']=="edit"){
?>
$("#vendor_name").attr('disabled','disabled');
<?php
}
?>
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	
	<?php
	if($_REQUEST['mode']=="add")
	{
		?>
	if($("#vendor_name").val()=="" || $("#vendor_name").val().split(" ").join("")==""){
		alert("Please enter Vendor name.");
		isValid=false;
	}
    if($(".qty").val()==0 \\)
    {
        alert("Please Enter AtLeast one Qty");
        isvalid=false;
    }
<?php
	}
?>
	if(isValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });
function loadDataTable()
	{
		$('#datatable_1').dataTable({
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false, 
			"aoColumns": [
				  { "sWidth": "1%" }, 
				  { "sWidth": "10%" },
				  { "sWidth": "15%" },				  
				  { "sWidth": "10%","bSortable": false }
				],
			 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
		});
	
    }

 function repoFormatResult(repo) {
      var markup = '<div class="row-fluid">' +
         '<div class="span10">' +
            '<div class="row-fluid">' +
               '<div class="span6">' + repo.name + '</div>' +
            '</div>';

      if (repo.description) {
         markup += '<div>' + repo.description + '</div>';
      }

      markup += '</div></div>';

      return markup;
   }

   function repoFormatSelection(repo) {
      return "";
   }
    
   $(document).ready(function(){

 $("#datatable_1").on('click','.delete',function(){
       $(this).closest('tr').remove();
	   recalculateFinalValues();
     });

});

</script>
<script>
$(document).ready(function() {

	
	$("#datatable_1").on('tbody tr td input[name=price]','change',function(){
		
	});
	$("#datatable_1").on('tbody p input[name=subtotal]','change',function(){
		
	});
  
});
//var count=$('.count').length;
var count=1;
 $("#vendor_name").change(function (){ //change event for select
 var vendor_id=$("#vendor_name").val();
 
 //alert(vendor_id);
 count=++count;
    $.ajax({
       // url :  instead of writing the function to execute the request we use Select2's convenient helper
	   
        url: 'search_product_with.php?id='+ vendor_id,
        type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
        data:{          
				vendor_id:vendor_id,
            },
        success: function (data) { // parse the results into the format expected by Select2.
				//alert(JSON.stringify(data));
				
				$("#name_value").html(data.result.vendor.vendor_name);
				$("#email_value").html(data.result.vendor.email);
				$("#address_value").html(data.result.vendor.address);
				$("#phone_value").html(data.result.vendor.phone_no);
				
				///Po list				
				var po_list=data.result.PO;
				$.each(po_list,function(index,value){
				var p_order_id=po_list[index];
				var new_row="<div class='form-group'><label class='mt-radio mt-radio-outline'><input name='po_ids[]' id='po_id_row' class='list_row' value="+p_order_id.id+"  type='radio'> &nbsp;PO Id : "+p_order_id.id+" <span class='label label-success'>"+p_order_id.po_date+"</span><span></span></label></div>";
				
				$(".mt-radio-list").append(new_row);
			
			});	
			
        },
		
        cache: true
    });
	$('.mt-radio-list').empty();
   })
    $(".list_row").live('change', function() {
    var po_id=$('input[type="radio"]:checked').val();	
	//alert(po_id);
	
    $.ajax({
       // url :  instead of writing the function to execute the request we use Select2's convenient helper
	   
        url: 'ajax_get_po_items.php',
        type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
        data:{          
				po_id:po_id,
            },
        success: function (data) { // parse the results into the format expected by Select2.
            //alert(JSON.stringify(data.result.items));
			var po_items=data.result.items;
			
			$.each(po_items,function(index,value){
				count=++count;
				var single_po_item=po_items[index];
				
				var new_row="<tr><td>"+count+"<input type='hidden' name='count[]' id='count' value='"+count+"'><input name='inward_store_id[]'  type='hidden' value='"+single_po_item.inward_store_id+"'><input type='hidden' name='purchase_order_id' value='"+single_po_item.purchase_order_id+"'><input type='hidden' name='item_id[]' value='"+single_po_item.item_id+"'><input type='hidden' name='category_id[]' value='"+single_po_item.category_id+"'></td><td>"+single_po_item.item_name+"<input name='item_name[]' class='item_name' type='hidden' value='"+single_po_item.item_name+"'></td><td>"+single_po_item.item_code+"<input name='item_code[]' class='item_code' type='hidden' value='"+single_po_item.item_code+"'></td><td>"+single_po_item.order_qty+"<input name='order_qty[]' type='hidden' value='"+single_po_item.order_qty+"' class='order_qty'></td><td><input name='received_qty[]' type='text' value='0' onChange='recalculateRow(this)' class='qty'></td><td class='remaining_qty'><input type='text' class='remaining_qty' disabled onChange='recalculateRow(this)' disabled name='remaining_qty[]' id='remaining_qty' value='"+single_po_item.remaining_qty+"'></td><td>"+single_po_item.price+"<input name='price[]'  type='hidden' value='"+single_po_item.price+"' class='price'></td><td><input type='text' class='total' disabled onChange='recalculateRow(this)' name='total[]' id='total' value='0'></td></tr>";
				
				$("#po_items_list").find('tbody').append(new_row);
				recalculateFinalValues();
			});
			
        },
		
        cache: true
    });
	$("#po_items_list").find('tbody').empty();
});
 
 function recalculateRow(t)
   {
	  // alert($(t).val());
	   var row = $(t).parent('td').parent('tr');
	   //alert($(row).html());
	   var price=$(row).find("td").find("input.price").val();
	   var qty=parseInt($(row).find("td").find("input.qty").val());
	   var order_qty=parseInt($(row).find("td").find("input.order_qty").val());
	  // var remain_qty=parseInt($(row).find("td").find("input.remaining_qty").val());
	   var remaining_qty=order_qty-qty;
	   
	 //alert(remaining_qty);
	   //alert(qty);
	   var total=price*qty;
	  //alert(total);
	  $(row).find("td").find("input.total").val(total);
	  $(row).find("td").find("input.remaining_qty").val(remaining_qty);
		
	if(qty>order_qty){
		$(row).find("td").find("input.qty").val("0");
		$(row).find("td").find("input.total").val("0");
		alert('received Quantity is not more than Ordered Quantity.');
	}else{
		
	}
	  
	recalculateFinalValues();
   }
   function recalculateFinalValues()
   {
	  var sum=0;
	  var sumQty=0;
		$('.total').each(function () {
			total=parseFloat($(this).val());
			//alert(total);
		sum +=total;
		//alert(sum);
		
    });
	
	$('.qty').each(function () {
			qty=parseInt($(this).val());
		sumQty +=qty;
		
    });
	    $("#finalTotal").val(''+sum);
	    $("#finalQty").val(''+sumQty);

   }
   
</script>
</body>
</html>
