<?php
$page_id=606;$page_slug='page_city';
$ctable 	= "city";
$ctable1 	= "city";
$main_page 	= $ctable;
$page 		= $ctable."_manage";
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title = ucwords($mode)." "."City";
include("connect.php");
require_once("../include/class.city.php");
$objcity= new city();
$state_id			= "";
$name			= "";
if($_REQUEST['id']==""){
	$_REQUEST['id']="";
}
if(isset($_REQUEST['submit'])){
	
	//$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['state_id']			= $db->clean($_REQUEST['state_id']);
	$detail['isDelete']		= 0;
	$detail['isActive']		= 1;
	$name=$_REQUEST['name_city'];
	$size[]=sizeof($name);

	$value_check=sizeof($name);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}

	if($isValidArray && !empty($name))
	{
		for($i=0;$i<sizeof($name);$i++)
		{
			$item[]=array("name"=>$name[$i]);

		}
	}
	if($mode=="add"){
		$db->checkRightFlag("insert_flag");
		$reply=$objcity->cityInsert($detail,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		else{
			 $db->addErrorMessage($reply['ack_msg']);
		}
		
	}
	else if($mode=="edit"){
		$db->checkRightFlag("update_flag");
		$reply=$objcity->cityUpdate($detail,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		} 
	}
}
$reply=array();
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
		$db->checkRightFlag("update_flag");
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $db->rp_getData($ctable,"*",$where);
		$detail['id']=$_REQUEST['id'];	
		$reply=$objcity->cityGetEditData($detail);
		if($reply['ack']==1){

		$store_inward_id=$_REQUEST['id'];
		$reply=$reply['result'];
	}
		else{
			$reply=array();
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$db->checkRightFlag("delete_flag");
	$detail['id']=$_REQUEST['id'];
	$reply=$objcity->cityDelete($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['city_id']."");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete_class"){
	$db->checkRightFlag("delete_flag");
	$detail['id']=$_REQUEST['id'];
	$reply=$objcity->cityDeleteClass($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
/*if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	
	$db->checkRightFlag("update_flag");
	$id = $_REQUEST['id'];
	$status = $_REQUEST['status'];
	$detail 	= array(
				"isActive"	=> $status,
				"id"	=> $id
			);
	$reply=$objcity->cityActive($detail);	
	if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);
	}	
}*/
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title;?>&nbsp; -&nbsp;<?php echo $db->rp_getValue("class","name","id='".$_REQUEST['id']."'",0);?></h1>
				
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<?php $db->getMessageBlock(); ?>			
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-12 ">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<label for="state_id">State <code>*</code></label>
								<?php 
								if($_REQUEST['mode']=="edit"){
									$disabled="disabled";
									$get_state_id=$db->rp_getValue("district","state_id","isDelete=0 AND id='".$_REQUEST['id']."'");
								}
								else{
									$disabled="enabled";
								}
								?>
								<select class="form-control" name="state_id" id="state_id" onChange="district(this.value);" <?php echo $disabled;?>>
									<option value="">Select State</option>
									<?php 
										$c_id=$db->rp_getData('class',"*","1=1 AND isDelete=0","",0);
										while($class_r=mysqli_fetch_assoc($c_id))
										{
											?>
											<option <?php if($_REQUEST['id']==$class_r['id']){ echo "selected"; } ?> value="<?php echo $class_r['id']?>">
											<?php echo $class_r['name'];?>
											</option>
											<?php
										}
									?>
								</select>

								<p class="help-block"></p>	
							</div>
							</div>

							
							<div class="row">
								<div class="col-md-5">
								<div class="form-group">
									<label>Name<code>*</code></label>
									<input type="text" class="form-control" name="name" class='name' id="name" value="<?php echo $name; ?>" autofocus>
									<p class="help-block"></p>
								</div>
								</div>
								
								<div class="form-group">
								<br/>
									<button style="margin-top:6px;" type="button" class="btn btn-primary" id="add_city">ADD city</button>
									<p class="help-block"></p>
								</div>
								
							</div>
							
							</div>
							
							<!--div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
							</div-->
						</div>
						<div class="portlet-body">
								<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
								<div id="results"></div>
						</div>
					</div>
				</div>
			
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript">
var state_id="";
var district_id="";

var data_url = "<?php echo $ctable ?>_get_ajax_crud.php";
function getSubCat(cid){
		state_id=cid;
		displaycityRecords(500,1);
}
function loadDataTable(){
	$('#datatable_city').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "23%","bSortable": false },
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displaycityRecords(numRecords) {
	var state_id = $("#state_id").val();
	// var district_id = $("#district_id").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&state_id=" + state_id + "&district_id=" + district_id,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords  + "&state_id=" + state_id + "&district_id=" + district_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords +"&state_id=" + state_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displaycityRecords(numRecords, 1);
}

$(document).ready(function() {
	displaycityRecords(500,1);
});

$("#add_city").click(function(){
	
	var name = $("#name").val();
	if(name=="")
	{
		toastr.error('Please Enter city Name');
	}
	else
	{
			var city_name=$('#name').val();	
			city_name.trim()						
			var state_id=$('#state_id').val();							
			var district_id=$('#district_id').val();							
			$.ajax({
				url:"city_ajax_function.php",
				type:"POST",
				data:{
					mode:'add_city',
					city_name:city_name,
					state_id:state_id,
					district_id:district_id,
					
				},
				 success:function(json, textStatus, jqXHR) 
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{
						
						toastr.success(msg,"Success!!");
						$('#name').val("");
						displaycityRecords(500);
						
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					toastr.error('Sorry, Server Error!!.', 'Error!!')
				}
				
			})
		
					
		
	}
	
})
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#state_id").val()=="" || $("#state_id").val().split(" ").join("")==""){		
		vd=aj.error('state_id',"Please Select class name.","add_error");
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
function del_conf(id){
	
	var r = confirm("Are you sure you want to delete?");
	if(r){
		var state_id = $("#state_id").val();
		$.ajax({
			url:"city_ajax_function.php",
			type:"POST",
			data:{
				mode:'delete_city',
				state_id:state_id,
				city_id:id,
				
			},
			 success:function(json, textStatus, jqXHR) 
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					displaycityRecords(500);
					
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				toastr.error('Sorry, Server Error!!.', 'Error!!')
			}
		})
	}
}
//quick edit functions
function hideQuickButton(id)
{
	$("#"+id).hide();
}
function showQuickButton(id)
{
	$("#"+id).show();
}
function quickEdit(pid){
	
	$(".lblQk").show(200);
	$(".btnQuickEdit").show(200);
	$(".txtQk").hide();
	$(".btnQk").hide();
	$(".btnEdit").show(200);
	$("#btnQuickEdit"+pid).hide();
	$("#btnSave"+pid).show(200);
	$("#btnCancel"+pid).show(200);
	$("#lblName"+pid).hide();
	$("#txtName"+pid).show(400);
	$("#name"+pid).focus();
	$("#lblCat"+pid).hide();
	$("#ddCat"+pid).show(400);
}
function cancelQuickEdit(pid){
	
	$("#txtName"+pid).hide();
	$("#lblName"+pid).show(200);
	$("#ddCat"+pid).hide();
	$("#lblCat"+pid).show(200);
	$("#btnSave"+pid).hide();
	$("#btnCancel"+pid).hide();
	$("#btnQuickEdit"+pid).show(200);
}
function saveQuickEdit(pid){
	var name 	= $("#name"+pid).val();
	var state_id 	= $("#state_id").val();
	var city_id 	= pid;
	if(city_id!=""){
		$.ajax({
			type: "POST",
			url: "city_ajax_function.php",
			data: {
				
				state_id:state_id,
				city_id:city_id,
				city_name:name,
				mode:"edit_city",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json) {
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					displaycityRecords();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}else{
		alert("Category is not selected.");
	}
}


function district(val) {
			$.ajax({
				type: "POST",
				url: "ajax_get_district.php",
				data: 'state_id=' + val,
				success: function(result) {
					// $("#district_id").html(result);
					// var state_id = $("#state").find(':selected').attr('data-state_id');
					// $("#state_id").val(state_id);
					$("#district_id").select2("destroy");
	            $("#district_id").html(result);
	            $("#district_id").select2();

				}
			});
		}
</script>
</body>
</html>