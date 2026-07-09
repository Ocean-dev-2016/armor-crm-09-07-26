<?php
$page_id=550;$page_slug='page_department';
$ctable 	= "department";
$ctable1 	= "Department";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/department.class.php");
$objDepartment= new Department();
$name			= "";
$code			= "";
$process_info			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['code']			= $db->clean($_REQUEST['code']);
	$detail['isDelete']		= 0;
	
	//Insert Production Process
	$process_id=$_REQUEST['my_multi_select1'];
    $size[]=sizeof($process_id);
    $value_check=sizeof($process_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}

	if($isValidArray)
	{
		for($i=0;$i<sizeof($process_id);$i++)
		{
			$process[]=array("process_id"=>$process_id[$i]);
		}
	}
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objDepartment->InsertDepartment($detail,$process);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
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
		$reply=$objDepartment->UpdateDepartment($detail,$process);
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
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $db->rp_getData($ctable,"*",$where);
		$detail['id']=$_REQUEST['id'];	
		$reply=$objDepartment->DepartGetEditData($detail);
		$process_info=$objDepartment->GetProcess($detail);
		//print_r($process_info);exit;
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
		if($process_info['ack']==1){

		$process_id=$_REQUEST['id'];
    		$process_info_r=$process_info['result'];
			
            $process_info=array();
            foreach($process_info_r as $i)
            {
                $process_info[]=$i['process_id'];
				
            }
			
        }
    	else{
    		//$process_info=array();
    	}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objDepartment->DeleteDepartment($detail);		
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
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
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
									<div class="col-md-6">
								<div class="form-group">
									<label>Department Name <code>*</code></label>
									<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
									<p class="help-block"></p>
								</div>
								</div>
								
								</div>
								
									
								<div class="row">
									<div class="col-md-6">
								<div class="form-group">
									<label>Code</label>
									<input type="text" class="form-control" name="code" id="code" value="<?php echo $code; ?>">
									<p class="help-block"></p>
								</div>
								</div>
								</div>
								
								<!-- <div class="row">
									<div class="col-md-9">
										<div class="form-group">
										
										<label class="control-label">Select Process Name</label>
										<select multiple="multiple" class="multi-select form-control" id="user_multi_select" name="my_multi_select1[]">
											<?php
											$depart_list_d=$db->rp_getData('department_map_process',"*","1=1 AND isDelete=0","",0);
												$process=array();	
												while($depart_list_r=mysqli_fetch_assoc($depart_list_d))
												{
													$process[] = array("id"=>$depart_list_r['id'],"process_id"=>$depart_list_r['process_id']);
												}
												$all_process_list_d=$db->rp_getData('production_process',"*","1=1 AND isDelete=0","process_name asc",0);
												while($all_process_list_r=mysqli_fetch_assoc($all_process_list_d))
												{
													$all_process[] = array("id"=>$all_process_list_r['id'],"process_name"=>$all_process_list_r['process_name']);
												}
												$foundflag=false;
												foreach($process as $i)
												{
													foreach($all_process as $u)
													{
														if($i['process_id'] == $u['id']){
														   $foundflag = true;
														   $array[]=$u['id'];
														
														}
													}
													if(!$foundflag){
														echo $i['process_id'];
													}
												}
												foreach($all_process as $u){
													
													if(!(in_array($u['id'],$array))){
														
												
												?>
													<option  value="<?php echo $u['id']?>" <?php echo (in_array($u['id'],$process_info))?"selected":""; ?>>
													<?php echo $u['process_name']; ?>
													</option>

													<?php
													}
													else if(in_array($u['id'],$process_info)){
															
													?>
													<option  value="<?php echo $u['id']?>" <?php echo (in_array($u['id'],$process_info))?"selected":""; ?>>
													<?php echo $u['process_name']; ?>
													</option>

													<?php
													}
													
												}
											
											?>
										</select>
										<p class="help-block"></p>
										</div>
									</div>
									
								</div> -->
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
							</div>
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
<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){		
		vd=aj.error('name',"Please Enter Department Name.","add_error");
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
$(function(){
	$("#user_multi_select").multiSelect();
	
});
</script>
</body>
</html>