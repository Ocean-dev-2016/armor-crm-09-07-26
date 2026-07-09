<!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>

<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script type="text/javascript" src="js/toastr.js"></script>
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.js"></script>
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
function rp_removeImage(c,f,i,l,d=''){
	if(confirm("Are you sure to delete this image?")){
		$.ajax({
			type: "POST",
			url: "ajax_removeImage.php",
			data: 'c='+c+'&f='+f+'&i='+i+'&l='+l,
			success: function(result){
					if(result==1){
						$(".mainImg"+d).fadeOut(800);
						alert("Image deleted successfully.");
					}else{
						alert("Something went wrong. Please try again.");
						window.location.href='';
					}
				}
		});
	}
}
</script>  
<script>
 //aj.getQuickNotification(".notification-container");
jQuery(document).ready(function() {   
$("ul.dropdown-menu").each(function(){
	if($(this).html().trim()=="")
	{
		$(this).parent("li.menu-dropdown ").remove();
	}
})

   	Metronic.init();
   	Layout.init();
   	QuickSidebar.init();
	Demo.init();
	$('select:not(.noSelect2)').select2({
	});
});

/*$(document).ready(function(){
	 $('input').focus(function(){		 
		 $(this).css("border","2px red solid");
	 });
	 
	 $('textarea').focus(function(){
		$(this).css("border","2px red solid");
	 });
	 $('textarea').blur(function(){
		$(this).css("border","1px grey solid");
	 });
    $('select').focus(function(){
		$(this).css("border","2px red solid");
		 $(this).attr("size",5);
        var x = "select[tabindex='" + (parseInt($(this).attr('tabindex'),10) + 1) + "']";
        $(x).fadeTo(50,0);
    });
	$('select2').focus(function(){
		$(this).css("border","2px red solid");
		 $(this).attr("size",5);
        var x = "select2[tabindex='" + (parseInt($(this).attr('tabindex'),10) + 1) + "']";
        $(x).fadeTo(50,0);
    });
    $('select').blur(function(){
		$(this).css("border","1px grey solid");
        $(this).attr("size",1); 
        var x = "select[tabindex='" + (parseInt($(this).attr('tabindex'),10) + 1) + "']";       
        $(x).fadeTo('fast',1.0);            
    });
	 $('input').blur(function(){	
		$(this).css("border","1px grey solid");
	 });
}); */

var $ = jQuery.noConflict();
$("#loading-modal").modal('hide');


$(window).load(function() {
   $('.preloader').fadeOut('slow');
});

</script>

<div id="loading-modal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-body text-center">
		<h1><img src="../images/loading.gif" class="img-responsive center-block">
        <p>Loading Data...</p>
		</h1>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Common Send mail modal -->
<div class="modal" id="SendMail" role="dialog" aria-labelledby="myModalLabel1" >
	<div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<form role="form" action="" method="post" id="formLocation" enctype="multipart/form-data">
				<div class="modal-header">
				  	<h4 class="modal-title model_title" id="myModalLabel1"></h4>
			  		<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
			  		</button>
				</div>
				<div class="modal-body">
					<fieldset class="form-group floating-label-form-group">
	  				<label for="email">To Email</label>
	  				<input type="text" class="form-control to_email" name="to_email" id="to_email" value="<?= $email ?>">
					</fieldset>
					<fieldset class="form-group floating-label-form-group">
	  				<label for="email">CC Email</label>
	  				<input type="text" class="form-control cc_email" name="cc_email" id="cc_email" value="<?= $email ?>">
					</fieldset>
					<fieldset class="form-group floating-label-form-group">
	  				<label for="email">Description</label>
	  				<textarea class="form-control" id="mail_description" rows="5" name="mail_description" style="resize: vertical;"></textarea>
					</fieldset>

					<!-- <fieldset class="form-group floating-label-form-group">
	  				<label for="email">Image</label>
	  					<input data-image="<?php echo ($payment_document!="" && file_exists(PAYMENT_APPROVE_DOUCUMENT.$payment_document))?PAYMENT_APPROVE_DOUCUMENT.$payment_document:"";?>" type="file" name="payment_document[]" id="payment_document" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $payment_document ?>" value="" multiple>
					</fieldset> -->

				</div>
				<div class="modal-footer">
					<input type="hidden" class="mail_type" id="mail_type" value="">
					<input type="hidden" class="id" id="id" value="">
	  			<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="send_mail" class="btn btn-success">Send Mail </button>
				</div>
			</form>
  		</div>
	</div>
</div>
<!-- Common Send mail modal -->


<!-- Common Customer Edit modal -->
<div id="CustomerEditModel" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Customer Details</div>
					<div class="tools">

						<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Common Customer Edit modal -->

<!-- Common Customer Edit modal -->
<div id="CustomerChangeModel" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Customer Details</div>
					<div class="tools">

						<a href="javascript:;" id="requesting_ajax_chnage_customer" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Common Customer Edit modal -->


<!-- Common Customer Chnage Shipping address -->
<div id="CustomerChangeShippingAddressModel" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Customer Change Shipping Address</div>
					<div class="tools">

						<a href="javascript:;" id="requesting_ajax_change_shipping" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Common Customer Chnage Shipping address -->


<script type="text/javascript">
	CKEDITOR.replace('mail_description');
	CKEDITOR.instances.mail_description.getData().replace(/(\r\n|\n|\r)/gm,"");

	$('#SendMail').on('show.bs.modal', function (event) 
	{
		var button = $(event.relatedTarget) // Button that triggered the modal
	  var requesting_id=button.data("id");
	  $(".id").val(requesting_id);
	  var requesting_title=button.data("title");
	  $(".model_title").html(requesting_title);
	  var to_email =button.data("mailid");
	  $(".to_email").val(to_email);
	  var cc_email =button.data("ccmailid");
	  $(".cc_email").val(cc_email);
	  var type =button.data("type");
	  $(".mail_type").val(type);
	})

	$(function()
	{
		$("#send_mail").on('click',function(){
			SendMail();
		});
	})

	function SendMail()
	{
		var id = $("#id").val();
		var to_email = $("#to_email").val();
		var cc_email = $("#cc_email").val();
		var mail_type = $("#mail_type").val();
		var description = CKEDITOR.instances['mail_description'].getData();
		$.ajax({
			type: "POST",
			url: "generate_email.php",
			data: {
				id: id,
				to_email: to_email,
				cc_email: cc_email,
				mail_type: mail_type,
				description: description,
			},
			beforeSend: function() {
				$(".transCover").fadeIn(800);
			},
			success: function(result) 
			{
				var result = $.parseJSON(result);
				if (result.ack == 1)
				{ 
					$(".transCover").fadeOut(100);
					toastr.success(result.ack_msg);
					$("#send_mail").hide();
				} 
				else 
				{
					toastr.error(result.ack_msg);
				}
			}
		})
	}

	/*Customer Edit*/ 
	$('#CustomerEditModel').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var customer_id=button.data("customer_id");
	  var quotation_id=button.data("quotation_id");
	  var mode=button.data("mode");
	  $("#requesting_ajax").attr("data-url","customer_data_get_ajax.php?customer_id="+customer_id+"&id="+quotation_id+"&mode="+mode);
		$("#requesting_ajax").click();
	})
	/*Customer Edit*/ 

	/*Customer change*/ 
	$('#CustomerChangeModel').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var customer_id=button.data("customer_id");
	  var quotation_id=button.data("quotation_id");
	  var mode=button.data("mode");
	  $("#requesting_ajax_chnage_customer").attr("data-url","customer_change_data_get_ajax.php?customer_id="+customer_id+"&id="+quotation_id+"&mode="+mode);
		$("#requesting_ajax_chnage_customer").click();
	})
	/*Customer change*/ 


	/*Customer Chnage Shipping address*/ 
	// $('#CustomerChangeShippingAddressModel').on('show.bs.modal', function (event) {
	//   var button = $(event.relatedTarget) // Button that triggered the modal
	//   var customer_id=button.data("customer_id");
	//   var quotation_id=button.data("quotation_id");
	//   var mode=button.data("mode");
	//   $("#requesting_ajax_change_shipping").attr("data-url","customer_change_shipping_get_ajax.php?customer_id="+customer_id+"&id="+quotation_id+"&mode="+mode);
	// 	$("#requesting_ajax_change_shipping").click();
	// })

	// $('#CustomerChangeShippingAddressModel').on('show.bs.modal', function (event) {
	//   var button = $(event.relatedTarget) // Button that triggered the modal
	//   var customer_id = $('.customer_id_s').val();

	//   alert(customer_id);
	//   var quotation_id=button.data("quotation_id");
	//   var mode=button.data("mode");
	//   $("#requesting_ajax_change_shipping").attr("data-url","customer_change_shipping_get_ajax.php?customer_id="+customer_id+"&id="+quotation_id+"&mode="+mode);
	// 	$("#requesting_ajax_change_shipping").click();
	// })
	/*Customer Chnage Shipping address*/ 

	/*for notification*/
		var config = {
		    apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
			  authDomain: "cmk-crm.firebaseapp.com",
			  projectId: "cmk-crm",
			  storageBucket: "cmk-crm.appspot.com",
			  messagingSenderId: "345899882377",
			  appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
			  measurementId: "G-2TS49WRQ29",
	  /*	apiKey: "AIzaSyAsS3yLpbKE0TAcVmxJyc-flH4y8Lwd2TQ",
	    authDomain: "test-d2b90.firebaseapp.com",
	   // databaseURL: "https://test-d2b90.firebaseio.com",
	    projectId: "test-d2b90",
	    storageBucket: "test-d2b90.appspot.com",
	    messagingSenderId: "936332442505",
	    appId: "1:936332442505:web:ebb14b2f2a512234ccff58",
	    measurementId: "G-8XQY6RR2YW"*/
	  };
	  firebase.initializeApp(config);
		const messaging = firebase.messaging();
	  messaging.onMessage(function(payload) {
	     // alert( payload.data.click_action);
		console.log("Message received. ", payload);
		notificationTitle = payload.data.title;
		notificationOptions = {
	  	body: payload.data.body,
	  	icon: payload.data.icon,
	  	image:  payload.data.image,
	  	//data: { url:payload.data.click_action }
	  	//link: payload.data.click_action,
	  	
	  	   click_action: payload.data.click_action, // To handle notification click when notification is moved to notification tray
          data: {
              click_action: payload.data.click_action
          }
    };

		var notification = new Notification(notificationTitle,notificationOptions);
	
		
		notification.onclick = function(event) {
        event.preventDefault(); // prevent the browser from focusing the Notification's tab
        window.open(notification.data.click_action, '_blank');
      }
    });
		
	
	/*for notification*/
</script>

<!-- for licence expire -->
<div id="licence-modal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <div class="modal-body text-center">
		<h1>
        <p style="color:red;font-size: 22px;">Your Licence Is Expired In <span class="diffdays"></span> days </p>
		</h1>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
$currdate = date('d-m-Y');
$remainingdate = date('d-m-Y',strtotime("+".$db->encrypt_decrypt('decrypt', DO_NOT_CHANGE_ANOTHER)." days"));
$lastdatedate = $db->encrypt_decrypt('decrypt', DO_NOT_CHANGE);
$currdate = strtotime($currdate);
$remainingdate = strtotime($remainingdate);
$lastdatedate = strtotime($lastdatedate);
$diff_days = ($lastdatedate - $currdate)/60/60/24;
if($remainingdate>=$lastdatedate)
{
	?>
	<script type="text/javascript">
		$("#licence-modal").modal("show");
		$(".diffdays").html("<?=$diff_days?>");
	</script>
	<?php
}
?>
<!-- for licence expire -->