<?php
$page_id=583;$page_slug='future_followup_manage';
$ctable 	= "followup";
$ctable1 	= "Followup";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
include("../include/followup.class.php");
$ObjFollowup = new  Followup();
//echo $_REQUEST['mode'];exit();
// $reply=$ObjFollowup->GetFollowupContent($visitor_id,$reference_id);
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
 <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/components.min.css"/>
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" /> 
<link href="assets/global/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="executive_manage.php" class="btn primary"><i class="fa fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
		    <div class="row">
				<div class="col-md-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-sm-12">
					<div class="portlet light portlet-fit" id="channel-container">
						<div class="portlet-title">
							<div class="caption">
								<i class="icon-microphone font-green"></i>
								<span class="caption-subject bold font-green uppercase"> Manage
									<?php

									if($_REQUEST['mode']=="inquiry_followup")
									{

										$txt = "Inquiry Followup";
									} 
									else if($_REQUEST['mode']=="leads_followup")
									{
										$txt = "Leads Followup";
									} 
									else if($_REQUEST['mode']=="quotation_followup")
									{
										$txt = "Quotation Followup";
									}
									else if($_REQUEST['mode']=="customer_followup")
									{
										$txt = "Customer Followup";
									}
									else
									{
										$txt =  $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'"); 	
									}
									echo $txt;
									?> 's </span>
								<span class="caption-helper"></span>
							</div>
							<div class="actions">
								<div class="btn-group btn-group-devided" data-toggle="buttons" style="margin-top: -6px">
									<a href="#createFollowup" style="padding: 7px" class="btn btn-success " target="#createFollowup" data-toggle="modal" title="Edit"><i class="fa fa-plus"></i>Create Followup</a>
								</div>								 
							</div>
							<div style="margin-right:5px " class="btn-group pull-right" data-toggle="buttons">
                <!-- <label class="btn default  "  title="Icon View">
                  <input class="toggle" type="radio" name="view" value="0" ><i class="fa fa-th-large"></i>
                </label> -->
                <label class="btn default active red"  title="Tile View">
                  <input class="toggle" type="radio" name="view" value="1" ><i class="fa fa-th"></i>
                </label>
              </div>
						</div>
						<div class="portlet-body">
						<div id="followup-ajax-result-container-1"></div>
						</div> 
				</div>
			</div>
		</div>
	</div>
	<?php
	if($_REQUEST['mode']=="inquiry_followup" || $_REQUEST['mode']=="leads_followup")
	{
		?>
		<input type="hidden" name="inquiry_id" id="inquiry_id" value="<?php echo $_REQUEST['inquiry_id']; ?>">
		<input type="hidden" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>">
	<input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['visitor_id']; ?>">
		<?php
	} 	
	else
	{
	?>
	<!-- <input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['visitor_id']; ?>"> -->
	<!-- <input type="hidden" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>"> -->
	<?php
	}
	?>

	<?php
	if($_REQUEST['mode']=="quotation_followup")
	{
		?>
		<input type="hidden" name="quotation_id" id="quotation_id" value="<?php echo $_REQUEST['quotation_id']; ?>">
		<input type="hidden" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>">
		<input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['visitor_id']; ?>">
		<?php
	} 	
	else
	{
		?>
		<!-- <input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['visitor_id']; ?>"> -->
		<!-- <input type="hidden" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>"> -->
		<?php
	}
	?>

	<?php
	if($_REQUEST['mode']=="customer_followup")
	{
		?>
		<input type="hidden" name="executive_id" id="executive_id" value="<?php echo $_REQUEST['executive_id']; ?>">
		<input type="hidden" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>">
		<input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['executive_id']; ?>">
		<?php
	} 	
	else
	{
		?> 
		<!-- <input type="text" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['visitor_id']; ?>"> -->
		<!-- <input type="hidden" name="visitor_id" id="visitor_id" value="<?php echo $_REQUEST['sales_id']; ?>"> -->
		<!-- <input type="text" name="followup_flag" id="followup_flag" value="<?php echo $_REQUEST['mode']; ?>"> -->
		<?php
	}
	?>
</div>

</div>
<div class="modal" id="createFollowup" role="dialog" aria-labelledby="myModalLabel1" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="formLocation">
	<div class="modal-header">
	  <h4 class="modal-title" id="myModalLabel1">Create Followup</h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="email">Description<span class="text-danger">*</span></label>
	  <textarea class="form-control" width="150px" id="description" name="description" placeholder="Enter Description" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="email">Followup Through<span class="text-danger">*</span></label>
	  <select class="form-control" id="through" name="through">
	  <option value="0">Select Followup Through</option>
	  <option value="1">Call</option>
	  <option value="2">Sms</option>
	  <option value="3">Email</option>
	  <!-- <option value="4">Whatsapp</option> -->
	  </select>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label>Followup Date <span class="text-danger">*</span></label>	
		 <div class="input-group input-medium date ">
			<input type="text" class="form-control datetime-picker" disabled name="followup_date" id="followup_date" placeholder="Followup Date">
			<span class="input-group-btn">
				<button class="btn default" type="button">
					<i class="fa fa-calendar"></i>
				</button>
			</span>
		</div>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="followup_status">Followup Status<span class="text-danger">*</span></label>
	  <select class="form-control" id="followup_status" name="followup_status">
	  <option value="">Select Followup Status</option>
	  	<option value="0">Generate</option> 
	  	<option value="2">Positive</option> 
			<option value="1">In Followup</option>
			<option value="4">Hot</option>
			<option value="5">Cold</option> 
			<option value="6">Warm</option> 
			<option value="-1">My Work</option>
			<option value="3">Buy Later</option>
			<option value="-2">Cancel</option> 
			<option value="11">Lost</option>
	  </select>
	</fieldset>
	
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" id="save_followup" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>
<div class="modal" id="FollowupResponse" role="dialog" aria-labelledby="myModalLabel2" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="followuprespose">
	<div class="modal-header">
	  <h4 class="modal-title">Followup Response  <span id="response_followup_title"></span></h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="response">Response<span class="text-danger">*</span></label>
	  <textarea class="form-control" value="" width="150px" id="response" name="response" placeholder="Enter Response" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="followup_action">Followup Action<span class="text-danger">*</span></label>
	  <select class="form-control" id="followup_action" name="followup_action" onChange="showRelatedBlock(this)">
	  <option value="">Select Followup Action</option>
	  <option value="1">Next Followup</option>
	  <!-- <option value="2">In Future</option> -->
	  <option value="-1">End Followup</option>
	  </select>
	</fieldset>
	<!-- <fieldset class="form-group floating-label-form-group">
		<label for="email">Followup Reason<span class="text-danger">*</span></label>
		<select class="form-control" id="followup_reason_id" name="followup_reason_id">
			<option value="">Select Followup Reason</option>
			<?php
			$f_reason_r=$db->rp_getData("followup_reason","*","isDelete=0","",0);
			while($f_reason_d=mysqli_fetch_assoc($f_reason_r))
			{
			?>
			<option value="<?= $f_reason_d['id'] ?>"><?= $f_reason_d['name'] ?></option>
			<?php
			}
			?>
		</select>
	</fieldset> -->
	<fieldset class="form-group floating-label-form-group followup_block_future" style="display:none">
	
	  <label>Followup Future Date <span class="text-danger">*</span></label>	
		 <div class="input-group input-medium date ">
			<input type="text" class="form-control datetime-picker1" name="followup_future_date" id="followup_future_date" placeholder="Followup Date">
		</div>
		
	</fieldset>
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
	  	<input type="hidden" name="followup_id" id="followup_id" value="">
		<button type="button" id="response_followup_btn" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>
<div class="modal" id="EndFollowupResponse" role="dialog" aria-labelledby="myModalLabel2" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="followuprespose">
	<div class="modal-header">
	  <h4 class="modal-title">Followup End Response  <span id="end_response_followup_title"></span></h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="response">Response<span class="text-danger">*</span></label>
	  <input type="hidden" name="end_followup_id" id="end_followup_id">
	  <textarea class="form-control" readonly="" value="" width="150px" id="end_response" name="end_response" placeholder="Enter Response" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
		<label for="email">Followup Reason<span class="text-danger">*</span></label>
		<select class="form-control" id="followup_reason_id" name="followup_reason_id">
			<option value="">Select Followup Reason</option>
			<?php
			$f_reason_r=$db->rp_getData("followup_reason","*","isDelete=0","",0);
			while($f_reason_d=mysqli_fetch_assoc($f_reason_r))
			{
			?>
			<option value="<?= $f_reason_d['id'] ?>"><?= $f_reason_d['name'] ?></option>
			<?php
			}
			?>
		</select>
	</fieldset>

	<fieldset class="form-group floating-label-form-group">
		<label for="email">Followup Status<span class="text-danger">*</span></label>
		<select class="form-control" id="followup_status_id" name="followup_status_id">
			<option value="">Select Followup Status</option> 
			<?php
			// $status1 = "";
				// $status1 = $db->rp_getValue("no_order_inquiry","status","id='".$_REQUEST['inquiry_id']."'","",0);
			?>
			<option <?=($status1==0)?"selecte":""; ?> value="0">Generate</option> 
			<option <?=($status1==2)?"selecte":""; ?> value="2">Positive</option> 
			<option <?=($status1==1)?"selecte":""; ?> value="1">In Followup</option>
			<option <?=($status1==4)?"selecte":""; ?> value="4">Hot</option>
			<option <?=($status1==5)?"selecte":""; ?> value="5">Cold</option> 
			<option <?=($status1==6)?"selecte":""; ?> value="6">Warm</option> 
			<option <?=($status1==-2)?"selecte":""; ?> value="-2">Cancel</option> 
			<option <?=($status1==-1)?"selecte":""; ?> value="-1">Working</option>
			<option <?=($status1==3)?"selecte":""; ?> value="3">Buy Later</option>
			<option <?=($status1==11)?"selecte":""; ?> value="11">Lost</option>
		</select>
	</fieldset>
	<!-- <fieldset class="form-group floating-label-form-group">
	  <label for="end_followup_action">Followup Action<span class="text-danger">*</span></label>
	  <select class="form-control" id="end_followup_action" name="end_followup_action" >
	  <option value="">Select Followup Action</option>
	  <option value="3">Cancel</option>
	  <option value="2">No Requirement</option>
	  <option value="-1">Others</option>
	  </select>
	</fieldset> -->	
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" id="end_response_followup_btn" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>						  
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script type="text/javascript">
	var CurrentView=1;	
	$("input[name=view]").on('change',function(){
    CurrentView=$(this).val();  
    ChannelAjax.init();
    $("input[name=view]").parent("label").removeClass("red");
    $(this).parent("label").addClass("red");
  });
</script>

<script type="text/javascript" src="js/followup.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>

<script type="text/javascript">
	 
function showRelatedBlock(spn)
{	
	$("fieldset.followup_block_future").hide(10);
	if($(spn).val()==1)
	{
		// $("#createFollowup").modal('show');
		// $("#FollowupResponse").modal('hide');
	}
	else if($(spn).val()==2)
	{
		$("fieldset.followup_block_future").show(100);
	}
	else if($(spn).val()==-1)
	{
		var response1=$("#response").val();	
		if(response1=="")	
		{
			toastr.error("Please Enter first Response");
			$("#followup_action").select2("val","");
		}
		else
		{
			var fid=$("#response_followup_btn").data("id");
			var title=$("#response_followup_title").html();
			$("#FollowupResponse").modal('hide');
			$("#EndFollowupResponse").modal('show');
			$("#end_response").html(response1);
			$("#end_response_followup_title").html(title);
			$("#end_followup_id").val(fid);
		}
	}
}

	$('#FollowupResponse').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var followup_id=button.data("id");
		  var title=button.data("date");
		  var next_action=button.data("next_action");
		  var mode=button.data("mode");
		  if(mode=="edit")
		  {
			  $("#followup_action").attr("disabled","disabled");
		  }
		  else
		  {
			  $("#followup_action").removeAttr("disabled","disabled");
		  }
		  var response=button.data("response");
		  $("#response_followup_btn").attr("data-id",followup_id);
		  $("#followup_id").val(followup_id);
		  $("#response_followup_title").html(title);
		  $("#response").val(response);
		  $("#followup_action").val(next_action);
		  $("#followup_action").select2("destroy");
		  $("#followup_action").select2();
	});

		$('.datetime-picker').datetimepicker({
			formatTime:'H:i',
			formatDate:'d.m.Y',
			minDate:'0',
			timepickerScrollbar:false,
			container: '#modal_followup modal-body'
		});
		$('.datetime-picker').parent("div.input-group").find("span.input-group-btn").on("click",function(){
			$('.datetime-picker').removeAttr("disabled");
			$('.datetime-picker').datetimepicker("show");
		})
		$('.date-picker').datetimepicker({
			format:'Y/m/d',
			minDate:'0',
			timepicker:false,
			container: '#createFollowup modal-body'
		});
		$('.date-picker').parent("div.input-group").find("span.input-group-btn").on("click",function(){
			$('.date-picker').removeAttr("disabled");
			$('.date-picker').datetimepicker("show");
		})
		// add followup Response
		$('.datetime-picker1').datetimepicker({
			formatTime:'H:i',
			formatDate:'d.m.Y',
			minDate:'0',
			timepickerScrollbar:false,
			container: '#modal_followup modal-body'
		});
		$('.datetime-picker1').find("div.input-group").on("click",function(){
			$('.datetime-picker1').removeAttr("disabled");
			$('.datetime-picker1').datetimepicker("show");
		})
		$('.date-picker1').datetimepicker({
			format:'Y/m/d',
			minDate:'0',
			timepicker:false,
			container: '#createFollowup modal-body'
		});
		$('.date-picker1').find("div.input-group").on("click",function(){
			$('.date-picker1').removeAttr("disabled");
			$('.date-picker1').datetimepicker("show");
		})
   	
		$(function(){
   		$("#save_followup").on('click',function(){
   			var followupdate= new Date($('#followup_date').val());
				var day_followup = followupdate.getDate();
				var month_followup=followupdate.getMonth()+1;
				var year_followup=followupdate.getFullYear();
				var followup_date_format=`${year_followup}/${month_followup}/${day_followup}`;
				var date= new Date() ;
				let day = date.getDate();
				let month = date.getMonth() + 1;
				let year = date.getFullYear();
				let currentDate = `${year}/${month}/${day}`;
				var date1=new Date(followup_date_format);
				var date2=new Date(currentDate);
				if(date2>date1)
				{
					toastr.error("Please Select Valid Followup Date!!");
				}
				else
				{
	   			CreateFollowup();
	   		}
   		});
   	})
   	
   	function CreateFollowup()
   	{
		  var isValid =true;
		  var sales_id = '<?php echo $_REQUEST['sales_id'] ?>';
   		var followup_flag=$("#followup_flag").val();
   		if(followup_flag=="inquiry_followup" || followup_flag=="leads_followup")
   		{
   			var inquiry_id=$("#inquiry_id").val();
   		}
   		if(followup_flag=="quotation_followup" || followup_flag=="quotation_followup")
   		{
   			var quotation_id=$("#quotation_id").val();
   		}
   		if(followup_flag=="customer_followup" || followup_flag=="customer_followup")
   		{
   			var executive_id=$("#executive_id").val();
   		}
   		// else
   		// {
   			// var visitor_id=$("#visitor_id").val();
   		// }
   		var visitor_id=$("#visitor_id").val();
   		var description=$('#description').val();
   		var through=$('#through').val();
   		var followup_date=$('#followup_date').val();
   		var followup_status=$('#followup_status').val();

   		if(description!="")
   		{
   			if(through!="" && through!=0)
	   		{
	   			if(followup_date!="")
	   			{
		   			$.ajax({
						url:"followup_ajax_function.php",
						data:{
							m:"save_followup",
							description:description,   						
							through:through, 						
							followup_date:followup_date,
							visitor_id:visitor_id,
							inquiry_id:inquiry_id,
							quotation_id:quotation_id,
							executive_id:executive_id,
							followup_flag:followup_flag,
							sales_id:sales_id,
							followup_status:followup_status,
						},
						success:function(result){

							var result=$.parseJSON(result);

							if(result.a==1)
							{
								$("#createFollowup").modal('hide');
								$("#followup-ajax-result-container-1").empty();
								ChannelAjax.init();
								toastr.success(result.mg);
								//location.reload();
								$("#description").val("");
								$("#through").val("");
								$("#followup_date").val("");
							}
							else
							{
								toastr.error(result.mg);
							}
						}
					})
	   			}
	   			else
	   			{
	   				toastr.error("Followup Date Required!!");
	   			}
			}
	   		else
	   		{
	   			toastr.error("Followup Through Required!!");
	   		}
	   	}
	   	else
	   	{
	   		toastr.error("Description Required!!");
	   	}
   	}

   	function del_conf(id,inquiry_id){
    var r = confirm("Are you sure you want to delete?");
    // var sales_executive_id = '<?= $SEID ?>';
     var sales_executive_id = '<?= $_REQUEST['sales_id'] ?>';
    if(r){
        window.location.href='followuplist_crud.php?mode=inquiry_followup&inquiry_id='+inquiry_id+"&sales_id="+sales_executive_id+'&id='+id;
    }
}

var mode = "<?php echo $_REQUEST['mode'] ?>";
if(mode=="quotation_followup")
{
	var sales_id = "<?php echo $_REQUEST['sales_id'] ?>";
	function del_conf(id,inquiry_id){
    var r = confirm("Are you sure you want to delete?");
    // var sales_executive_id = '<?= $SEID ?>';
     var sales_executive_id = '<?= $_REQUEST['sales_id'] ?>';
    if(r){
        window.location.href='followuplist_crud.php?mode=quotation_followup&quotation_id='+inquiry_id+"&sales_id="+sales_id+'&id='+id;
    }
	}
}

var mode = "<?php echo $_REQUEST['mode'] ?>";
if(mode=="followup")
{
	var sales_id = "<?php echo $_REQUEST['sales_id'] ?>";
	function del_conf(id,inquiry_id){
    var r = confirm("Are you sure you want to delete?");
    // var sales_executive_id = '<?= $SEID ?>';
     var sales_executive_id = '<?= $_REQUEST['sales_id'] ?>';
    if(r){
        window.location.href="followuplist_crud.php?mode=followup&sales_id="+sales_id+'&id='+id;
    }
	}
}

	$("#response_followup_btn").on('click',function(){
			var isValid=true;
			var followup_future_date="";
			var response=$("#response").val();
			var followup_reason_id=$("#followup_reason_id").val();
			var followup_action=$("#followup_action").val();
			followup_future_date=$("#followup_future_date").val();
			/*var followup_id=$(this).data("id");*/
			var followup_id=$("#followup_id").val();
			if(response=="")
			{
				isValid=false;
				toastr.error("Enter response!!","Error");
			}
			if(followup_action=="")
			{
				isValid=false;
				toastr.error("Select Next Action!!","Error");
			}
			if(followup_action==2)
			{
				if(followup_future_date=="")
				{
					isValid=false;
					toastr.error("Select Next Action!!","Error");
				}
			}
			$.ajax({
				type: "GET",
				url:"followup_ajax_function.php",
				data:{
					response:response,
					followup_id:followup_id,
					followup_reason_id:followup_reason_id,
					followup_action:followup_action,
					followup_future_date:followup_future_date,
					m:"add_response"
				},
				success: function(json) {
					json=$.parseJSON(json);
					msg=json.mg;
					if(json.a==1)
					{
						toastr.success(msg,"Success!!");
						$("#FollowupResponse").modal("hide");
						if(followup_action==1){
							$("#createFollowup").modal('show');
						}
						$("response_followup_title").val("");
						$("#followup-ajax-result-container-1").empty();
						ChannelAjax.init();
						//location.reload();
						$("#response").val("");
						$("#followup_reason_id").val("");
						$("#followup_action").val("");
						$("#followup_future_date").val("");
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});			
		});
	$("#end_response_followup_btn").on('click',function(){
			var isValid=true;
			var followup_future_date="";
			var response=$("#end_response").val();
			var inquiry_id="<?= $_REQUEST['inquiry_id']?>";
			var quotation_id="<?= $_REQUEST['quotation_id']?>";
			var followup_action=-1;
			var status=$("#end_followup_action").val();
			var followup_id=$("#end_followup_id").val();
			var followup_reason_id=$("#followup_reason_id").val();
			var followup_status_id=$("#followup_status_id").val(); 
			if(response=="")
			{
				isValid=false;
				toastr.error("Enter response!!","Error");
			}
			if(status=="")
			{
				isValid=false;
				toastr.error("Select Next Action!!","Error");
			}
			if(followup_reason_id=="")
			{
				isValid=false;
				toastr.error("Select Followup Reason!!","Error");
			}
			if(followup_status_id=="")
			{
				isValid=false;
				toastr.error("Select Followup Status!!","Error");
			}
			if(isValid)
			{				
				$.ajax({
					type: "GET",
					url:"followup_ajax_function.php",
					data:{
						response:response,
						followup_id:followup_id,
						followup_action:followup_action,
						followup_future_date:followup_future_date,
						inquiry_id:inquiry_id,
						quotation_id:quotation_id,
						status:status,
						followup_reason_id:followup_reason_id,
						followup_status_id:followup_status_id,
						m:"end_followup"
					},
					success: function(json) {
						json=$.parseJSON(json);
						msg=json.mg;
						if(json.a==1)
						{
							toastr.success(msg,"Success!!");						
							$("#EndFollowupResponse").modal('hide');
							$("#end_response").html("");
							$("#end_response_followup_title").html("");
							$("#end_followup_id").val("");
							$("#followup-ajax-result-container-1").empty();
							ChannelAjax.init();
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					}
				});			
			}
		});
			
	</script>
</body>
</html>