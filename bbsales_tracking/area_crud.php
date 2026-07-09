<?php
$page_id=606;$page_slug='page_area';
$ctable 	= "area";
$ctable1 	= "Area";
$main_page 	= $ctable;
$page 		= $ctable."_manage";
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title = ucwords($mode)." "."Route";
include("connect.php");
require_once("../include/class.area.php");
$objarea= new Area();
$class_id			= "";
$name			= "";
if($_REQUEST['id']==""){
	$_REQUEST['id']="";
}
if(isset($_REQUEST['submit'])){
	
	//$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['class_id']			= $db->clean($_REQUEST['class_id']);
	$detail['isDelete']		= 0;
	$detail['isActive']		= 1;
	$name=$_REQUEST['name_area'];
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
		$reply=$objarea->areaInsert($detail,$item);
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
		$reply=$objarea->areaUpdate($detail,$item);
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
		$reply=$objarea->areaGetEditData($detail);
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
	$reply=$objarea->areaDelete($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['area_id']."");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete_class"){
	$db->checkRightFlag("delete_flag");
	$detail['id']=$_REQUEST['id'];
	$reply=$objarea->areaDeleteClass($detail);
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
	$reply=$objarea->areaActive($detail);	
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
								<label for="class_id">State <code>*</code></label>
								<?php 
								if($_REQUEST['mode']=="edit"){

									$get_state_id=$db->rp_getValue("city","state_id","isDelete=0 AND id='".$_REQUEST['id']."'");
									$disabled="disabled";
								}
								else{
									$disabled="enabled";
								}
								?>
								<select class="form-control" name="class_id" id="class_id" onChange="city(this.value);" <?php echo $disabled;?>>
									<option value="">Select State</option>
									<?php 
										$c_id=$db->rp_getData('class',"*","1=1 AND isDelete=0","",0);
										while($class_r=mysqli_fetch_assoc($c_id))
										{
											?>
											<option <?php if($get_state_id==$class_r['id']){ echo "selected"; } ?> value="<?php echo $class_r['id']?>">
											<?php echo $class_r['name'];?>
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
										<label for="class_id">City <code>*</code></label>
										<?php 
										if($_REQUEST['mode']=="edit")
										{
											$disabled="disabled";
										}
										else{
											$disabled="enabled";
										}
										?>
										<select class="form-control" name="city_id" id="city_id"  <?php echo $disabled;?> >
											<option value="">Select City</option>
										 <?php 
												$d_id=$db->rp_getData('city',"*","1=1 AND isDelete=0 AND id='".$_REQUEST['id']."'","",0);
												while($district_r=mysqli_fetch_assoc($d_id))
												{
													?>
													<option <?php if($_REQUEST['id']==$district_r['id']){ echo "selected"; } ?> value="<?php echo $district_r['id']?>">
													<?php echo $district_r['name'];?>
													</option>
													<?php
												}
											?> 
										</select>
								
									<p class="help-block"></p>	
								</div>
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
									<button style="margin-top:6px;" type="button" class="btn btn-primary" id="add_area">ADD Route</button>
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
var class_id="";
var data_url = "<?php echo $ctable ?>_get_ajax_crud.php";
function getSubCat(cid){
		class_id=cid;
		displayAreaRecords(500,1);
}
function loadDataTable(){
	$('#datatable_area').dataTable({
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
function displayAreaRecords(numRecords) {
	var class_id = $("#class_id").val();
	var city_id = $("#city_id").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&class_id=" + class_id+ "&city_id=" + city_id,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords  + "&class_id=" + class_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords +"&class_id=" + class_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayAreaRecords(numRecords, 1);
}

$(document).ready(function() {
	displayAreaRecords(500,1);
});

$("#add_area").click(function(){
	
	var name = $("#name").val();
	var class_id = $("#class_id").val();
	var city_id = $("#city_id").val();
	if(class_id=="")
	{
		toastr.error('Please Select State');
	}
	if(city_id=="")
	{
		toastr.error('Please Select City');
	}
	if(name=="")
	{
		toastr.error('Please Enter Area Name');
	}
	else
	{
			var area_name=$('#name').val();							
			var class_id=$('#class_id').val();							
			var city_id=$('#city_id').val();							
			$.ajax({
				url:"area_ajax_function.php",
				type:"POST",
				data:{
					mode:'add_area',
					area_name:area_name,
					class_id:class_id,
					city_id:city_id,
					
				},
				 success:function(json, textStatus, jqXHR) 
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{
						
						toastr.success(msg,"Success!!");
						$('#name').val("");
						displayAreaRecords(500);
						
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
	if($("#class_id").val()=="" || $("#class_id").val().split(" ").join("")==""){		
		vd=aj.error('class_id',"Please Select class name.","add_error");
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
		var class_id = $("#class_id").val();
		$.ajax({
			url:"area_ajax_function.php",
			type:"POST",
			data:{
				mode:'delete_area',
				class_id:class_id,
				area_id:id,
				
			},
			 success:function(json, textStatus, jqXHR) 
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					displayAreaRecords(500);
					
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
	var class_id 	= $("#class_id").val();
	var area_id 	= pid;
	if(area_id!=""){
		$.ajax({
			type: "POST",
			url: "area_ajax_function.php",
			data: {
				
				class_id:class_id,
				area_id:area_id,
				area_name:name,
				mode:"edit_area",
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
					displayAreaRecords();	
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




function city(val) {
			$.ajax({
				type: "POST",
				url: "ajax_get_city_master.php",
				data: 'state_id=' + val,
				success: function(result) {
					// $("#district_id").html(result);
					// var class_id = $("#state").find(':selected').attr('data-state_id');
					// $("#class_id").val(class_id);
					$("#city_id").select2("destroy");
	            $("#city_id").html(result);
	            $("#city_id").select2();

				}
			});
		}
</script>
</body>
</html>