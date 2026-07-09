<?php
$page_id=569;$page_slug='dispatch_pages';
$page_slug = "manage_inward_store";
$ctable 	= "dispatch_detail";
$ctable1 	= "Dispatch";
$main_page 	= $ctable;
$page 		= "add_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
include("connect.php");


include('../include/product.class.php');
include("../include/dispatch.class.php");
$objDispatch= new Dispatch();
$product=new Product();


if (isset($_REQUEST['submit'])) 
{
$mode=$_REQUEST['submit'];
$isActive					      = 1;
$detail['isDelete']			      = 0;
$detail['order_id']               = $_REQUEST['order_id'];
$detail['remark']                 = $_REQUEST['remark'];
$detail['dispatch_date']          = $_REQUEST['dispatch_date'];
$detail['expected_dispatch_date'] = $_REQUEST['expected_dispatch_date'];
$detail['warehouse_id'] = $_REQUEST['warehouse_id'];
// $detail['warehouse_id']           = isset($_REQUEST['warehouse_id'])?$db->clean(implode(",", $_REQUEST['warehouse_id'])):"";
$detail['transport_through']      = isset($_REQUEST['transport_through'])?$db->clean($_REQUEST['transport_through']):"";
$detail['transport_name']         = isset($_REQUEST['transport_name'])?$db->clean($_REQUEST['transport_name']):"";


$product_id      = $_REQUEST['product_id'];
$weight_id       = $_REQUEST['weight_id'];
$qty             = $_REQUEST['qty'];
$pro_description = $_REQUEST['pro_description'];
$order_item_id = $_REQUEST['order_item_id'];

$size[] 		= sizeof($product_id);
$size[] 		= sizeof($qty);
$size[] 		= sizeof($pro_description);
$size[] 		= sizeof($order_item_id);
$weight_id[] 	= sizeof($weight_id);
$value_check 	= sizeof($product_id);


if (in_array($value_check, $size)) 
{
$isValidArray = true;
} 
else 
{
$isValidArray = false;
}

if ($isValidArray && !empty($product_id)) 
{
for ($i = 0; $i < sizeof($product_id); $i++) 
{
$item[] = array("qty" => $qty[$i], "product_id" => $product_id[$i], "weight_id" => $weight_id[$i], "pro_description" =>$pro_description[$i],"order_item_id"=>$order_item_id[$i]);
}
/*echo "<pre>";
print_r($item); exit;*/
}

if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
{		
if($rights['insert_flag']!=1)
{
$db->rp_location('access_denied.php?msg=delete_access_denied');
}
//echo "hello";exit();
$reply=$objDispatch->InsertDispatch($detail,$item,$_FILES);
// print_r($reply);die;
if($reply['ack']==1)
{
	//echo "helloww";exit();
$db->addSuccessMessage($reply['ack_msg']);
$type=$db->rp_getValue("orders","customer_type","id='".$reply['type']."'");
if($mode=="print")
{
$dispatch_id=$reply['dispatch_id'];
$_SESSION['print']=$dispatch_id;
$db->rp_location("dispatch_crud_new.php?mode=add&id=".$dispatch_id);
}
else
{
$db->rp_location("dispatch_manage.php?msg=inserted");
}
}
else
{
$db->addErrorMessage($reply['ack_msg']);
}
}
}

if (isset($_REQUEST['order_id']) && $_REQUEST['order_id'] > 0 && $_REQUEST['mode'] == "add") 
{
$order_id = $_REQUEST['order_id'];
$detail['id'] = $_REQUEST['order_id'];

$reply = $objDispatch->GetDispatchDetails($detail);
$reply1=$objDispatch->GetDispatchItems($detail);


if ($reply['ack'] == 1) 
{
$result = $reply['result'];
extract($result);
}
if($reply1['ack']==1)
{
$store_inward_id=$_REQUEST['id'];
$item_info=$reply1['result'];
}
else
{
$item_info=array();
}
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
<meta charset="utf-8" />
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css" />
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css" />
<style type="text/css">
tbody td,
th {
border-left: none !important;
border-right: none !important;
}
tbody td :focus {
border: 4px solid black;
}
</style>

</head>

<body class="page-md">
<?php 

include("header.php");
 ?>
<div class="page-container">
<div class="page-head bg-grey">
<div class="container">
<div class="page-title">
<h1><a href="dispatch_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
<form role="form" enctype="multipart/form-data" action="" method="post" onSubmit="return check_form();">
<div class="row">
<div class="col-md-12">
<div class="portlet box blue">
<div class="portlet-body form">
<div class="row">
<div class="col-sm-12">
<div class="col-md-12 col-sm-12 col-xs-12 portlet box grey-cascade box">
<div class="portlet-title">
<div class="caption">
<i class="fa fa-user"></i>
<span class="caption-subject bold uppercase"> Dispatch DETAIL</span>
</div>
</div>
</div>
<div class="row">
<div class="col-md-12">
<div class="form-body">
<div class="form-group">
<div class="row">
	<div class="col-md-4 col-sm-4">
		<div class="form-group">
			<?php
			if ($_REQUEST['mode'] == "add") 
			{
				$disabled = "disabled";
			} 
			else 
			{
				$disabled = "";
			}

			if($_REQUEST['mode']=="add" && $_REQUEST['order_id']!="")
			{
				$customer_type = $db->rp_getValue("orders","customer_type","id='".$_REQUEST['order_id']."'");

				$customer_id = $db->rp_getValue("orders","customer_id","id='".$_REQUEST['order_id']."'");
			}

			?>
			<label>Select Customer Type<code>*</code></label>
			<select class="form-control" <?php echo $disabled; ?> id="customer_type" name="customer_type" onchange="getCustomer(this.value)">
				<option value="">Select Customer Type</option>
				<?php
				$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
				if ($cust_R) 
				{
					while ($C = mysqli_fetch_assoc($cust_R)) 
					{
						?>
						<option <?= ($customer_type == $C['id']) ? "selected" : ""; ?> value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
						<?php
					}
				}
				?>
			</select>
			<p class="help-block"></p>
		</div>
		<div class="form-group">
			<label>Select Customer<code>*</code></label>
			<?php
			if ($_REQUEST['mode'] == "add") 
			{
				?>
				<input type="hidden" name="edit_customer_id" id="edit_customer_id" value="<?= $customer_id; ?>">
				<?php
			}
			?>
			<select class="form-control" name="customer_id" placeholder="Select Customer" id="customer_id" type="text" <?php echo $disabled; ?> >
				<option value="">Select Customer</option>
				<?php
				if($_REQUEST['mode']=="add" && $_REQUEST['order_id']!="")
				{
					$customers = $db->rp_getData("executive", "*", "isDelete=0");
					if ($customers) 
					{
						while ($customer = mysqli_fetch_assoc($customers)) 
						{
							if ($customer['price_list_id'] != 0) 
							{
								$price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . $customer['price_list_id'] . "'");
							} 
							else 
							{
								$price_list_name = "N/A";
							}
							?>
							<option value="<?php echo $customer['id']; ?>" <?php if ($customer_id == $customer['id']) {echo "selected"; } ?> ><?php echo $customer['company_name']." - ".$customer['cname']." - ".$customer['city']; ?></option>
							<?php
						}
					}
				}
				?>
			</select>
			<p class="help-block"></p>
		</div>
		<?php
		if(isset($_REQUEST['order_id']) && $_REQUEST['mode'] == "add")
		{
			?>
			<div class="form-group">
				<div class="row static-info phone">
					<div class="col-md-5 name"> Company Name : <?php echo $company_name; ?> </div>
					<div class="col-md-7 value" name="company_name" id="company_name"><?php echo $db->rp_getValue("executive","company_name","id='".$customer_id."'",0); ?></div>
				</div>
				<div class="row static-info phone">
					<div class="col-md-5 name"> Name : </div>
					<div class="col-md-7 value" name="name" id="name"><?php echo $db->rp_getValue("executive","cname","id='".$customer_id."'",0); ?> </div>
				</div>
				<div class="row static-info phone">
					<div class="col-md-5 name"> Phone : </div>
					<div class="col-md-7 value" name="name_phone" id="name_phone"><?php echo $db->rp_getValue("executive","phone","id='".$customer_id."'"); ?> </div>
				</div>
				<div class="row static-info address">
					<div class="col-md-5 name"> Address : </div>
					<div class="col-md-7 value" name="name_address" id="name_address"><?php echo $db->rp_getValue("executive","address","id='".$customer_id."'"); ?></div>
				</div>
				<div class="row static-info address">
					<div class="col-md-5 name"> State : </div>
					<div class="col-md-7 value" name="name_state" id="name_state"><?php echo $db->rp_getValue("executive","state","id='".$customer_id."'"); ?></div>
				</div>
				<div class="row static-info address">
					<div class="col-md-5 name"> GSTIN : </div>
					<div class="col-md-7">
						<!-- <input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php  echo $db->rp_getValue("executive","gst","id='".$customer_id."'"); ?>"> -->
						<input readonly class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php  echo $db->rp_getValue("executive","gst","id='".$customer_id."'"); ?>" maxlength="15">
						<p class="help-block"></p>
					</div>
				</div>
				<div class="row static-info address">
					<div class="col-md-5 name"> Pricelist : </div>
					<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php if($price_list_name!=""){ echo $price_list_name;} else {echo "N/A"; } ?></div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<div class="col-md-6 col-sm-6">
		<div class="col-md-6">
			<div class="form-group">
				<label>Dispatch Date <code>*</code></label>
				<input type="text" readonly="" class="form-control" name="dispatch_date" id="dispatch_date" value="<?php echo date('d-m-Y'); ?>" />
				<p class="help-block"></p>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label>Expected Dispatch Date </label>
				<input type="text" readonly="" class="form-control" name="expected_dispatch_date" id="expected_dispatch_date" value="<?php echo $expected_dispatch_date; ?>" />
				<p class="help-block"></p>
			</div>
		</div>

		
		<div class="col-md-6">
			<div class="form-group">
				<label>Transport By</label>
				<select class="form-control" name="transport_through" id="transport_through" onchange="getTransportname(this.value);">
                	<option value="">Select Transport By</option>
                    <?php
                    $transport_by_r = $db->rp_getData("transport_by","*","isDelete=0");
                    if(mysqli_num_rows($transport_by_r)>0)
                    {
                    	while($transport_by_d = mysqli_fetch_array($transport_by_r))
                        {
                            if($_REQUEST['mode']=='add' && $_REQUEST['order_id']!="")
                        	{
                        		$transport_through = $db->rp_getValue("orders","transport_through","id='".$_REQUEST['order_id']."' AND isDelete=0",0);
                        	}
                            ?>
                            <option value="<?php echo $transport_by_d['id']; ?>" <?php if($transport_by_d['id']==$transport_through){?> selected <?php } ?>><?php echo $transport_by_d['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
				<p class="help-block"></p>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Transporter Detail</label>
				<select class="form-control" name="transport_name" id="transport_name">
					<option value="">Select Transporter Detail</option>
					<?php
                    $transport_nameR = $db->rp_getData("transport_master","*","isDelete=0");
                    if(mysqli_num_rows($transport_nameR)>0)
                    {
                    	if($_REQUEST['mode']=='add' && $_REQUEST['order_id']!="")
                    	{
                    		$transport_name = $db->rp_getValue("orders","transport_name","id='".$_REQUEST['order_id']."' AND isDelete=0",0);
                    	}
                        while($transport_named = mysqli_fetch_array($transport_nameR))
                        {
                            ?>
                            <option value="<?php echo $transport_named['id']; ?>" <?php if($transport_named['id']==$transport_name){?> selected <?php } ?>><?php echo $transport_named['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
				</select>
				<p class="help-block"></p>
			</div>
		</div>
		
		 <div class="col-md-6" >
		<div class="form-group">
		<label>Select Warehouse <code>*</code></label>
		<select class="form-control" name="warehouse_id" id="warehouse_id" onchange="getproductOrder()">
		<option value="">Select Warehouse</option>
		<?php
		$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
		while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
		{
		?>
		<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
		<?php echo $WarehouseD['name']; ?>
		</option>
		<?php
		}
		?>
		</select>
		<p class="help-block"></p>
		</div>
		</div> 

		<div class="col-md-4">
			<div class="form-group">
				<label>LR Copy </label>
				<input type="file" name="LR_copy" id="LR_copy" value="" />
				<p class="help-block"></p>
				<input class="form-control " value="<?php echo $LR_copy; ?>" name="old_path" id="old_path" type="hidden">
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
<i class="fa fa-user"></i>
<span class="caption-subject bold uppercase"> Dispatch ITEM</span>
</div>
</div>
<div class="portlet-body">
<div class="row">
<div class="col-sm-12">
<div class="row">
<div class="col-md-12">
<div class="form-body">
<div class="form-group">
<div class="row">
<div class="col-md-12">
<table border="1px" id="datatable_1" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<th>No.</th>
<th class="text-center" width="200px;">Product Name</th>
<th class="text-center" width="50px;">Unit</th>
<th class="text-center">Order Box Qty</th>
<th class="text-center">Order Bag Qty</th>
<th class="text-center">Order Loose Qty</th>
<th class="text-center">Order Qty</th>
<th class="text-center">Available Qty</th>
<th class="text-center">Dispatch Qty</th>
<th class="text-center">Add Manual Stock</th>
<th class="text-center">Delete</th>
</tr>
<?php
if($_REQUEST['mode']=='add') 
{
?>
<tr id="nodata"><td colspan="11" class="text-center"><b>Select Warehouse To Load Product</b></td></tr>
<?php
}
?>
</thead>
<tbody id="orderItms_div">
<?php
if (!empty($item_info) && $_REQUEST['mode']!='add') 
{
$count = 0;
foreach ($item_info as $i) 
{
	$box_qty += $i['box_qty'];
	$qty_total += $i['qty'];
	$unit_id = $db->rp_getValue("product","unit_id","id='". $i['product_id']."'");
	$unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");

	if(($i['stock']=="0" || $i['stock']=="") || ($i['stock'] < $i['qty']))
	{
		$style = 'background-color: #f13e46!important';
	}
	else
	{
		$style = "";	
	}
	?>
	<tr style='<?=$style ?>'>
		<td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count; ?>" class='count'></td>
		
		<td style="text-align: center;">
			<input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
			<input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">
			<input type='hidden' style="text-align:right" name='subtotal[]' value="">
			<input type='hidden' style="text-align:right" name='total[]' value="">
			<input type='hidden' style="text-align:right" name='item_name[]'>
			<input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
			<input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
			<input type='hidden' class="cid" name='cid[]' value="<?php echo $i['cid']; ?>" id='cid'>
			<input class="tcid" type='hidden' name='tcid[]' value="<?php echo $i['tcid']; ?>" id='tcid'>
			<input class="order_item_id" type='hidden' name='order_item_id[]' value="<?php echo $i['order_item_id']; ?>" id='order_item_id'>


			<?php
			if($i['top_cat_name']!="" && $i['category_name']!="")
			{
				echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>T</b> :".$i['top_cat_name']." - <b>C</b> :".$i['category_name'];
			}
			else if($i['top_cat_name']!="")
			{
				echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>T</b> :".$i['top_cat_name'];
			}
			else if($i['category_name']!="")
			{
				echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>C</b> :".$i['category_name'];
			}
			else
			{
				echo $i['product_name']." - ".$i['cat_no'];
			}
			?>
		</td>

		<td style="width: 100px;text-align: center;"><?= $unit_name ?></td>

		<td style='text-align:right'>
			<input type='hidden' name='outer_size' class='outer_size' value="<?php echo $i['outer_size']; ?>">
			<input readonly  type='text' class='form-control box_qty'  style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>">
		</td>


		<td style="text-align:right">
			<input class="inner_size" type='hidden' name='inner_size[]' value="<?php echo $i['inner_size']; ?>">
			<input readonly name='bag[]' class='form-control bag positive' style="text-align:right;width:100px;"  type='text' value="<?php echo $i['bag']; ?>">
		</td>

		<td style='text-align:right'>
			<input type='hidden' name='loose_qty' class='loose_qty' value="<?php echo $i['loose']; ?>">
			<input readonly type='text' class='form-control loose' style='text-align:right;width:100px;' name='loose[]' class='loose positive' value="<?php echo $i['loose']; ?>">
		</td>

		<td style="text-align:right;width: 40px;">
			<input readonly type='text' style="text-align:right;width: 100px;" class='order_qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='order_qty[]' value="<?php echo $i['qty']; ?>">
		</td>

		<td style="width: 100px;text-align: center;"><input readonly type='text' style="text-align:right;width: 100px;" class='available_qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='available_qty[]' value="<?= $i['stock'] ?>"></td>

		<td style="text-align:right;width: 40px;">
			<input type='text' onChange='Checkstock(this,<?= $i['product_id']?>,<?=$i['weight_id']?>)' style="text-align:right;width: 100px;" class='qty1 qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='qty[]' value="<?php echo $i['qty']; ?>">
		</td>

		<td class="text-center">
			<?php
			$total_dispatch_record = $db->rp_getTotalRecord("dispatch_map_order", "order_id='" . $i['order_id'] . "' AND isDelete=0", 0);
			if ($total_dispatch_record > 0) 
			{

			} 
			else 
			{
				?>
				<a class='delete btn btn-danger btn-sm' title='Delete'><i class='fa fa-times'></i></a>
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
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label>Remarks</label>
<textarea class="form-control" name="remark" value="<?php echo $remark; ?>"></textarea>
<p class="help-block"></p>
</div>
</div>
<div class="col-md-5" style="margin-top: 25px;">
<button type="submit" name="submit" class="submit_form btn green">Submit</button>
<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $back; ?>'">Back</button>
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
</form>
</div>
</div>
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
$(".qty1").numeric();
//$("#warehouse_id").fSelect();

var mode = "<?= $_REQUEST['mode'] ?>";
const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
minimumFractionDigits: decimals,
maximumFractionDigits: decimals,
});

$('#dispatch_date').datepicker({
datepicker: true,
autoclose: true,
dateFormat: 'dd-mm-yy',
//maxDate: 0
});

$('#expected_dispatch_date').datepicker({
datepicker: true,
autoclose: true,
dateFormat: 'dd-mm-yy',
//maxDate: 0
});

function check_form() 
{
$(".form-body").children().removeClass("has-error");
var isValid = true;
if($("#warehouse_id").val()=="" || $("#warehouse_id").val().split(" ").join("")=="")
{		
vd=aj.error('warehouse_id',"Please Select Warehouse.","add_error");
isValid=false;
}
if($("#dispatch_date").val()=="" || $("#dispatch_date").val().split(" ").join("")=="")
{		
vd=aj.error('dispatch_date',"Please Select Dispatch Date.","add_error");
isValid=false;
}

$(".qty1").each(function() {
if ($(this).val() == "" || $(this).val() == 0) {
toastr.error("Please Enter At Least one Quantity!!");
$(".qty1").focus();
isValid = false;
}
});

if (isValid) 
{
var r = confirm("Are You sure want to Save this Dispatch??");
if (r) 
{
return true;
} 
else 
{
return false;
}
} 
else 
{
return false;
}
}

$(".form-control").bind("keyup change", function() {
if ($(this).parent().hasClass("has-error")) {
$(this).parent().removeClass("has-error");
$(this).parent().find('p.help-block').html("");
}
});

$(document).ready(function() {
$("#datatable_1").on('click', '.delete', function() {
var r = confirm("Are you sure you want to delete?");
if (r) {
$(this).closest('tr').remove();
}
});
});

$(".positive").keyup(function(event) 
{
if (event.keyCode == 46 || event.keyCode == 8) 
{
// let it happen, don't do anything
}
else if (/\D/g.test(this.value)) 
{
// toastr.error("Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});


function Checkstock(val,pro_id,weight_id)
{
var dispatch_qty = $(".qty"+pro_id+"_"+weight_id).val();
var order_qty = $(".order_qty"+pro_id+"_"+weight_id).val();
var stock_qty = $(".available_qty"+pro_id+"_"+weight_id).val();
if(parseFloat(dispatch_qty) > parseFloat(stock_qty))
{
toastr.error("Enter less than or equal to order qty ");
$(".qty"+pro_id+"_"+weight_id).val(0);
}
else
{
if(parseFloat(dispatch_qty) > parseFloat(order_qty))
{
toastr.error("Enter less than or equal to order qty ");
$(".qty"+pro_id+"_"+weight_id).val(0);
}
}
}
</script>

<script type="text/javascript">
var mode = '<?= $_REQUEST['mode']; ?>';
if(mode=="edit")
{
var id = $("#transport_through").val(); 
var transport_name_selected_id = $("#transport_name_selected_id").val();
getTransportname(id,transport_name_selected_id);
}
function getTransportname(id,transport_name_selected_id="")
{	
$.ajax({
type: "post",
url: "ajax_get_transport_detail.php",
data: "id=" + id+"&selected_id="+transport_name_selected_id,
beforeSend: function() {
$(".transCover").fadeIn(800);
// $("#loading-modal").modal('show');
$('.preloader').fadeIn('slow');
},
success: function(result) {
setTimeout(function() {
$("#transport_name").select2("destroy");
$('#transport_name').html(result);
$("#transport_name").select2();
// $("#loading-modal").modal('hide');
$('.preloader').fadeOut('slow');
});
}
})
}
</script>
<script type="text/javascript">
$(".submit_form").on('click',function(){
$("#loading-modal").modal({backdrop: 'static', keyboard: false});
})

	function getproductOrder()
	{
		var warehouse_id = $('#warehouse_id').val();
		var order_id ='<?= $_REQUEST['order_id'] ?>';

		var r = confirm("You lost all added product.Are you sure want to change warehouse?");
		if (r) 
		{
			if(warehouse_id!="")
			{
				$.ajax({
					type: "post",
					url: "dispatch_warehouse_product_from_order.php",
					data: "warehouse_id=" + warehouse_id+"&order_id="+order_id,
					beforeSend: function() { 
					$('.preloader').fadeIn('slow');
					},
					success: function(result) { 
					$('.preloader').fadeOut('slow'); 
					$('#nodata').html(""); 
					$('#orderItms_div').html(result); 
					}
				})
			}
		}
		else
		{
			$("#warehouse_id").select2("val","");
		}
	}
</script>
</body>
</html>