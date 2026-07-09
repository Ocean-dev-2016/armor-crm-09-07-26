<?php
$page_id=588;$page_slug='add_invoice';
$ctable 	= "sales_return";
$ctable1 	= "Sales Return";
$main_page 	= $ctable;
$page 		= "view_".$ctable;
$page_title = "View ".$ctable1;
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$classname = "Numbers_Words_Locale_en_IN" ;
$obj = new $classname; 
$admin_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
$flag_r=$db->rp_getData("page_admin_right","*","page_id='".$page_id."' AND admin_id='".$admin_type."' AND isDelete=0","",0);
$flag_d = mysqli_fetch_array($flag_r);

$bid 	= $_REQUEST['sales_return_id'];
$invoice_status=$db->rp_getValue("sales_return","status","id='".$bid."' AND isDelete=0");
$customer_id = $db->rp_getValue("sales_return","customer_id","id='".$bid."' AND isDelete=0");
$customer_mail_id = $db->rp_getValue("executive","email","id='".$customer_id."' AND isDelete=0",0);
$customer_ccmail_id = $db->rp_getValue("executive","email_cc","id='".$customer_id."' AND isDelete=0");
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>

<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<style type="text/css">
	 #wrapper
	{
	    width:190mm;
	    margin:0 50mm;
	}
	#wrapper {
   		 width: auto!important;
   	}
</style>
</head>
<body class="page-md">
<div class="transCover"><img src="assets/admin/layout/img/89.gif" alt="" style="margin-top:20%;padding-left:48%;" ></div>
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h2><?php echo $page_title;?></h2>
				
			</div>
			<div class="page-toolbar">
				<?php
					if($invoice_status==0)
					{
				if($flag_d['approve_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
				{
				?> 
				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-success" href='#Invocemodal' data-title="Sales Return Detail" data-entry_slug="advance_payment_approve" data-status_slug="4" data-id=".$items[$i]['id']." data-toggle='modal'>Approve</a>
				</div> 

				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-danger" href="javascript:;" onClick="InvoiceStatus('<?php echo $bid; ?>','-2');" title="Print">Disapprove</a>
				</div>
					<?php
						}
						?>
						
						<?php
					}


					?>

					<?php
					if($invoice_status!=3)
					{
						?>
					<div class="btn-group btn-theme-panel hide-app-dis">
						<a class="btn btn-danger" href="javascript:;" onClick="InvoiceStatus('<?php echo $bid; ?>','3');" title="Cancel">Cancel</a>
					</div>

					<?php
					} ?>
					<?php
					if($flag_d['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
						<?php
					}
					?>
					<?php

					if($invoice_status==1)
					{
						if($flag_d['email_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
						{
					?>
						<!-- <div class="btn-group btn-theme-panel hide-app-dis">
							<a onclick="sendEmail('<?= $bid; ?>')" class="btn btn-success" title="Send Mail">Send Mail</a>
						</div> -->
						<div class="btn-group btn-theme-panel hide-app-dis">
							<a class="btn btn-success" href='#SendMail' data-title="Invoice" data-id="<?= $bid ?>" data-mailid="<?= $customer_mail_id ?>" data-ccmailid="<?= $customer_ccmail_id ?>" data-type="sales_invoice_detail" data-toggle='modal'>Send Mail</a>
						</div>
					<?php
						}
					}
				?>
 
				</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				
				<div class="col-md-12" id="report_content">
					<div id="wrapper1">
						<?php 
						include("sales_return_view_new.php"); 
						?>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
</div>
<!-- model code -->
<div class="modal fade" id="orderDownload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form class="form-horizontal" >    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Choice Format</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="bid" id="bid">
          	<div class="col-md-9">
         		<div class="form-group">            
            		<input class="form-control" type="radio" name="order_format" id="tax_order_format" value="1" style="width: 27px;">Original
            	</div>
          	</div>
          	<div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="order_format" id="per_order_format" value="2" style="width: 27px;">Duplicate
            	</div>
            </div>
            <div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="order_format" id="per_order_format" value="3" style="width: 27px;">Triplicate
            	</div>
            </div> 
            <div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="order_format" id="per_order_format" value="4" style="width: 27px;">Normal
            	</div>
            </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary order_downlaod">DOWNLOAD</button>
      </div>
    </form>
    </div>
  </div>
</div>
<!-- model code -->
 
<!-- incvoice detail model -->
<div class="modal" id="Invocemodal" role="dialog" aria-labelledby="myModalLabel1" >
	<div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<form role="form" action="" method="post" id="formLocation" enctype="multipart/form-data">
				<div class="modal-header">
				  	<h4 class="modal-title model_title" id="myModalLabel1"></h4>
			  		<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
			  		</button>
				</div>
				<div class="modal-body">
					<?php 
					$cart_detail_r 	= $db->rp_getData("sales_return","*","id='".$bid."'","",0);
					$cart_detail_d1 	= mysqli_fetch_assoc($cart_detail_r);
					?>
					<fieldset class="form-group floating-label-form-group">
	  					<label for="email">Credit Note Series</label><code>*</code>
	  					<select class="form-control" name="prefix_id" id="prefix_id" onchange="getBillNo(this.value)">
	  						<option>Select Prefix</option>
	  						<?php
	  						$prefix_R = $db->rp_getData("prefix_master","*","isDelete=0");
	  						if($prefix_R)
	  						{
	  							while($prefix_D = mysqli_fetch_array($prefix_R))
	  							{
							?>
							<option data-prefix="<?= $prefix_D['name'];?>" value="<?=$prefix_D['id'] ?>"><?php echo $prefix_D['name'] ?></option>
							<?php
	  							}
	  						}
	  						?>
	  					</select>
					</fieldset>
					<fieldset class="form-group floating-label-form-group">
	  					<label for="email">Credit Note No</label><code>*</code>
	  					<input type="text" class="form-control" name="invoice_no" id="invoice_no" value="" readonly>
					</fieldset>
					<fieldset class="form-group floating-label-form-group">
					<label for="email">Credit Note Date </label><code>*</code>
						<input type="date" class="form-control" name="invoice_date" id="invoice_date" value="<?php echo date('Y-m-d'); ?>">
					</fieldset>
					<fieldset class="form-group floating-label-form-group" hidden>
	  					<label for="email">E-Way Bill No</label>
	  					<input type="text" class="form-control" name="way_bill_no1" id="way_bill_no1">
					</fieldset>
				</div>
				<div class="modal-footer">
  				<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="save_invoice_detail" class="btn btn-success">Save </button>
				</div>
			</form>
  		</div>
	</div>
</div>
<!-- incvoice detail model -->


<!-- print model code -->
<div class="modal fade" id="printinvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form class="form-horizontal" >    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Choice Format</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="bid" id="bid">
          	<div class="col-md-9">
         		<div class="form-group">            
            		<input class="form-control" type="radio" name="print_format" id="tax_print_format" value="1" style="width: 27px;">Original
            	</div>
          	</div>
          	<div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="print_format" id="per_print_format" value="2" style="width: 27px;">Duplicate
            	</div>
            </div>
            <div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="print_format" id="per_print_format" value="3" style="width: 27px;">Triplicate
            	</div>
            </div> 
            <div class="col-md-9">
          		<div class="form-group">
            		<input class="form-control" type="radio" name="print_format" id="per_print_format" value="4" style="width: 27px;">Normal
            	</div>
            </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary invoice_print">PRINT</button>
      </div>
    </form>
    </div>
  </div>
</div>
<!-- print model code -->

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script>
function genReport(bid){
	$("#bid").val(bid);
	$("#orderDownload").modal("show");
	 

	$(".order_downlaod").on("click",function()
	{
		var format_type = $("input[name='order_format']:checked").val();

		if(format_type==1 || format_type==2 || format_type==3 || format_type==4)
		{
			var bid=$("#bid").val();
			var rc = encodeURIComponent($("#report_content").html());
			$.ajax({
				type: "POST",
				url: "invoice_generate.php",
				data: 'invoice_id='+bid+'&staic=2'+'&format_type='+format_type,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
				},
				success: function(result){ 
						$("#bid").val("");
						$("#orderDownload").modal("hide");
						setTimeout(function(){
							window.location.href=result;
							$(".transCover").fadeOut(100);				
						},1500);
					}
			});
		}
		else
		{
			toastr.error("Please Select Atleast one Format for the Download");
		}

	})

}

function genExcel(bid){
	var rc = encodeURIComponent($("#report_content").html());
	$.ajax({
		type: "POST",
		url: "invoice_generate_excel.php",
		data: 'invoice_id='+bid+'&staic=2',
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					window.location.href=result;
					$(".transCover").fadeOut(100);				
				},1500);
			}
	});
}
function InvoiceStatus(oid,status)
{
	if(status==1)
	{
		var txt="Approve";
	}
	else if(status==-2)
	{
		var txt="Dispprove";
	}
	else if(status==3)
	{
		var txt="Cancel";
	}
	var r=confirm("Are You Sure you want to "+txt+" this Invoice?");
	if(r)
	{		
		$.ajax({
			type: "POST",
			url: "update_sales_return_status.php",
			data: 'invoice_id='+oid+'&status='+status,
			beforeSend: function() {
				$(".transCover").fadeIn(800);
			},
			success: function(result){ 
				var result=$.parseJSON(result);
				if(result.ack==1)
				{
					$(".hide-app-dis").addClass("hidden");
					setTimeout(function(){					
						$(".transCover").fadeOut(100);				
						toastr.success(result.ack_msg);
						location.reload();
					},1500);
				}
				else
				{
					toastr.error(result.ack_msg);
				}
			}
		});
	}
} 
function printReport(bid){
	$("#bid").val(bid);
	// $("#printinvoice").modal("show");
	

	// $(".invoice_print").on("click",function()
	// {
		// var print_format_type = $("input[name='print_format']:checked").val();

		// if(print_format_type==1 || print_format_type==2 || print_format_type==3 || print_format_type==4)
		// {
			var myWindow =  window.open('sales_return_view_print.php?invoice_id='+bid+'&format_type=1'+"&p=1",'','width=500,height=800');
			$("#printinvoice").modal("hide");
			setTimeout(function () 
			{
				myWindow.print();
		    var ival = setInterval(function() 
		    {
		      myWindow.close();
		      clearInterval(ival);
		    }, 500);
		  },1500);
		// }
		// else
		// {
		// 	toastr.error("Please Select Atleast one Format for the Download");
		// }

	// })

}

// for mail send
function sendEmail(id)
{
	/*$.ajax({
		type: "POST",
		url: "generate_email.php",
		data: {
			ref_id: id,
			type: "orders",
		},
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result) {
			var result = $.parseJSON(result);
			if (result.ack == 1) { 
				$(".transCover").fadeOut(100);
				toastr.success(result.ack_msg);
			} else {
				toastr.error(result.ack_msg);
			}
		}
	});*/
}
// for mail send

</script>

 

<script type="text/javascript">

	$('#Invocemodal').on('show.bs.modal', function (event) {
  		var button = $(event.relatedTarget) // Button that triggered the modal
  		var requesting_id=button.data("id");
  		var requesting_title=button.data("title");
  		$(".model_title").html(requesting_title);
  		var requesting_slug=button.data("entry_slug");
  		$(".entry_slug").val(requesting_slug);
  		var requesting_status_slug=button.data("status_slug");
  		$(".status_slug").val(requesting_status_slug);
	})

	$(function(){
		$("#save_invoice_detail").on('click',function(){
			UpdateInvoceDetail();
		});
	})

	function UpdateInvoceDetail()
 	{




 		$('#save_invoice_detail').prop('disabled', true);

 		var invoice_id = '<?php echo $bid ?>';
 		var prefix_id = $('#prefix_id').val();
 		var invoice_no = $('#invoice_no').val();
 		var invoice_date = $('#invoice_date').val();
 		var way_bill_no1 = $('#way_bill_no1').val();

 		var myFormData = new FormData();
 		myFormData.append('invoice_id',invoice_id);
 		myFormData.append('prefix_id',prefix_id);
 		myFormData.append('invoice_no',invoice_no);
 		myFormData.append('invoice_date',invoice_date);
 		myFormData.append('way_bill_no1',way_bill_no1);
		myFormData.append('mode',"sales_return_detail");


 		if(prefix_id!="" && invoice_no!="" && invoice_date !="")
 		{
 			$.ajax({
			    url:"add_ajax_eway_bill_no.php",
			    type:"POST",
			    data:myFormData,
			    processData: false, // important
			    contentType: false, // important
			    beforeSend:function() {
			      },
			    success:function(result)
			    {
			        result=$.parseJSON(result);
			        if(result.ack==1)
			        {                          
			          toastr.success(result.ack_msg);
			          $("#Invocemodal").modal('hide');
			          location.reload();
			        }               
			        else
			        {
			         	toastr.error(result.ack_msg);
			         	$('#save_invoice_detail').prop('disabled', false);
			        }         
			    },                    
    		});
 		}
 		else
 		{
 			toastr.error("Please Select Prefix And Invoice No And Invoice Date");
 			$('#save_invoice_detail').prop('disabled', false);
 		}
	}

	function getBillNo(typeid)
	{
		var prefix=$("#prefix_id").find("option:selected").data("prefix");
		var invoice_id = '<?php echo $bid ?>';
		$.ajax({
			type: "POST",
			url: "ajax_get_challan_no.php",
			data: 'typeid='+typeid+'&prefix='+prefix+'&invoice_id='+invoice_id+'&ctable=sales_return',
			success: function(result){
				var result = $.parseJSON(result);
				$("#invoice_no").val(result.order_no);
				$("#bill_under").val(result.bill_under);
				$("#bill_book_office").val(result.bill_book_office);
			}
		});
	}
	
</script>
</body>
</html>