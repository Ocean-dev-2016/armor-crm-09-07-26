<?php
$page_id=5895;
$ctable 	= "admin_type";
$ctable1 	= "Admin Type";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
$name			= "";

if(isset($_REQUEST['submit'])){
	
	$name			= $db->clean($_REQUEST['name']);
	$parent_id		= $db->clean($_REQUEST['parent_id']);
   	$department_id	= $db->clean($_REQUEST['department_id']);
	$isDelete		= 0;

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r)
		{
			$db->rp_location($ctable."_crud.php?mode=add&msg=duplicate");
			die;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"parent_id",
				"name",
				"department_id",
				"isDelete",
				"adate"
			);
			$values = array(
				$parent_id,
				$name,
				$department_id,
				$isDelete,
				$adate
			);
			$tid = $db->rp_insert($ctable,$values,$rows,0);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
	}
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r)
		{
			$db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
			die;
		}
		else
		{
			$rows 	= array(
				"parent_id"		=> $parent_id,
				"name"			=> $name,
				"department_id"	=> $department_id,
			);
			$where	= "id='".$_REQUEST['id']."'";
			$db->rp_update($ctable,$rows,$where);
			$db->rp_location($ctable."_manage.php?msg=updated");
		}
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit")
{
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$ctable_d = mysqli_fetch_array($ctable_r);
	$tid=$_REQUEST['id'];
	$name			= htmlentities($ctable_d['name']);
	$parent_id		= htmlentities($ctable_d['parent_id']);
	$department_id	= htmlentities($ctable_d['department_id']);
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	$rows 	= array(
		"isDelete"	=> "1"
	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=deleted");
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!="")
{
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">
					<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="duplicate"){ ?>
					<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
						<b>Error! This Name is Already Exist. Please Try Another Name.</b> </div>
					<?php } ?>
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Parent<code>*</code></label>
										   <select type="text" class="form-control" name="parent_id" id="parent_id" value="<?php echo $parent_id; ?>">
											<option value=""> -- Select Parent -- </option>
											<?php

												$Admin_dataR=$db->rp_getData("admin_type","*","isDelete=0","",0);
												while($Admin_dataD=mysqli_fetch_assoc($Admin_dataR))
												{
													?>
													<option <?php echo ($parent_id==$Admin_dataD['id'])?"selected":""; ?> value="<?php echo $Admin_dataD['id']; ?>"><?php echo $Admin_dataD['name']?></option>
													<?php
												}
											?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Name <code>*</code></label>
											<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
										</div>
									</div>
								</div>
							</div>
                            <div class="form-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Department Name <code>*</code></label>
										   <select type="text" class="form-control" name="department_id" id="department_id" value="<?php echo $department_id; ?>">
											<option value=""> -- Select Department -- </option>
											<?php

												$ctable_data=$db->rp_getData("department","*","isDelete=0","",0);
												while($ctable_r=mysqli_fetch_assoc($ctable_data))
												{
													?>
													<option <?php echo ($department_id==$ctable_r['id'])?"selected":""; ?> value="<?php echo $ctable_r['id']; ?>"><?php echo $ctable_r['name']?></option>
													<?php
												}
											?>
											</select>
										</div>
									</div>
								</div>
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
<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); } }); });
function check_form(){
	$(".form-body").children().removeClass("has-error");
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
		alert("Please enter agent name.");
		$("#name").focus().parent().addClass("has-error");
		return false;
	}	
}
</script>
</body>
</html>