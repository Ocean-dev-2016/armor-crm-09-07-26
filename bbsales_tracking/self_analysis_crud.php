<?php
$page_id=659;$page_slug='self_analysis';
include("connect.php");
$ctable 	= "self_analysis";
$ctable1 	= "self_analysis";
$main_page 	= "utility";
$page 		= "self_analysis_manage";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$employee_id = $_REQUEST['employee_id'];
$sales_executive_r=$db->rp_getData('sales_executive_information',"CONCAT(first_name, ' ', middle_name, ' ', surname, ' - ', contact_no) AS full_name_contact,id","id = ".$employee_id);
$sales_executive_d = mysqli_fetch_array($sales_executive_r);
// print_r($sales_executive_r);die;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"self_analysis_manage.php","title"=>"Self Analysis"),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit Self Analysis"));
// print_r($sales_executive_d['full_name_contact']);die;


if(isset($_REQUEST['submit'])){
	// echo "<pre>";
	// print_r($_REQUEST);die;
	$que_count=0;
	$sales_executive_form_id = $_REQUEST['sales_executive_form_id'];
	$questions		= $_REQUEST['question_id'];
	$answers		= $_REQUEST['answers'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		for ($i=0; $i < sizeof($_REQUEST['answers']); $i++) { 
			$inserted_id = $db->rp_insert($ctable,array($sales_executive_form_id,$_REQUEST['question_id'][$i],$_REQUEST['answers'][$i]),array("sales_executive_form_id","questions_id","answers"),0);
		}
		if ($inserted_id) {
			$db->addSuccessMessage("Self Analysis Add successfully!");
			$db->rp_location("employee_information_manage.php?msg=inserted");
		}
	}
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		// echo "<pre>";
		// print_r($_REQUEST);die;
		for ($i=0; $i < sizeof($_REQUEST['question_id']); $i++) {
			$self_analysis_id = $db->rp_getValue($ctable,"id","isDelete = 0 AND sales_executive_form_id = ".$_REQUEST['sales_executive_form_id']." AND questions_id = ".$_REQUEST['question_id'][$i],0);
			if($self_analysis_id != ""){
				$self_analysis = $db->rp_update($ctable,array("sales_executive_form_id"=>$sales_executive_form_id,"questions_id"=>$_REQUEST['question_id'][$i],"answers"=>$_REQUEST['answers'][$i]),"id = ".$self_analysis_id,0);
			}else{				
				$self_analysis = $db->rp_insert($ctable,array($_REQUEST['sales_executive_form_id'],$_REQUEST['question_id'][$i],$_REQUEST['answers'][$i]),array("sales_executive_form_id","questions_id","answers"),0);
			}

		}
		if($self_analysis){
			$db->addSuccessMessage("Self Analysis Updated successfully!");
			$db->rp_location("employee_information_manage.php?msg=updated");			
		}

	}

}



if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	$answers_rr=array();
	$where = " sales_executive_form_id ='".$_REQUEST['employee_id']."' AND isDelete = 0";

	$ctable_r = $db->rp_getData($ctable,"*",$where,"",0);
	 // mysqli_fetch_array($ctable_r);
	while($ctable_d =mysqli_fetch_array($ctable_r)){
		$answers_rr[$ctable_d['questions_id']] = $ctable_d['answers'];
	}
	$sales_executive=$ctable_d['sales_executive_form_id'];
	
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){

	$where = " id='".$_REQUEST['id']."'";
        $rows 	= array(
    		    "isDelete"	=> "1"
    	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->addSuccessMessage("Self Analysis Deleted successfully!");
	$db->rp_location("employee_information_manage.php?msg=deleted");

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

<link href="assets/css/demo.html5imageupload.css?v1.3" rel="stylesheet">

</head>
<style type="text/css">
	.custom-input {
	    height: 80px; /* Adjust the height as desired */
	    padding: 6px; /* Adjust the padding as needed */
  	}
  	input[type="text"]
{
    font-size:18px;
}
</style>

<body class="page-md">

<?php include("header.php"); ?>

<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="employee_information_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<?php $db->getMessageBlock(); ?>	
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label>Employee Name<code>*</code></label>
											<input class="form-control" disabled type="text" name="employee_name" value="<?=$sales_executive_d['full_name_contact']?>">
											<input class="form-control" type="hidden" name="sales_executive_form_id" id="sales_executive_form_id" value="<?=$sales_executive_d['id']?>">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-12">
										<!-- <label>Questions - Answers<code>*</code></label> -->
										<?php
											$c=0;
											// $ans_ddd=0;
											$que_r=$db->rp_getData("self_analysis_master","*","isDelete=0");
											while ($que_d=mysqli_fetch_array($que_r)){
												$c++;
												if ($c % 2 == 0) {
										?>
											<div class="form-group">
												<div class='col-md-12'>
													<b><?php echo $c."  "."Question";?></b>
													<input readonly class="form-control " type="text" name="questions" id="questions" value="<?php echo $que_d['questions'];?>">
													<br>
													<b>Answer:</b>
													<textarea style="height: auto; resize: vertical;" class="form-control custom-input valuecheck" name="answers[]" id="answers<?php echo $que_d['id'] ?>"><?= $answers_rr[$que_d['id']];?></textarea>
													<br>
													<input type="hidden" name="question_id[]" id="question_id" value="<?php echo $que_d['id']?>">
												</div>
											</div>
										<?php
												}
												// $ans_ddd++;
											}	
										?>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='employee_information_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 ">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-12">
										<label>Questions - Answers<code>*</code></label>
										<?php
											$c=0;
											// $ans_ddddd=0;
											$que_r=$db->rp_getData("self_analysis_master","*","isDelete=0");
											while ($que_d=mysqli_fetch_array($que_r)){
												$c++;
												if ($c % 2 != 0) {

											?>
											<div class="form-group">
												<div class='col-md-12'>
													<b><?php echo $c."  "."Question";?></b>
													<input readonly class="form-control " type="text" name="questions" id="questions" value="<?php echo $que_d['questions'];?>">
													<br>
													<b>Answer:</b>
													<textarea style="height: auto; resize: vertical;" class="form-control custom-input valuecheck" name="answers[]" id="answers<?php echo $que_d['id'] ?>"><?= $answers_rr[$que_d['id']];?></textarea>
													<br>
													<input type="hidden" name="question_id[]" id="question_id" value="<?php echo $que_d['id']?>">
												</div>
											</div>
										<?php
												}
												// $ans_ddddd++;
											}	
										?>
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

<?php include("footer.php"); ?>

<?php include("include_js.php"); ?>




<script type="text/javascript">
function check_form()
{
 $(".form-body").children().removeClass("has-error");
   var isValid = true;
   var count=0;
	if($("#sales_executive_form_id").val()=="" || $("#sales_executive_form_id").val().split(" ").join("")=="")
	{
		vd=aj.error('sales_executive_form_id',"Please Select Sales Person.","add_error");
		//alert("Please Select Sales Person");
		//$("#sales_executive_form_id").focus();
		isValid = false;
	}
	 $(".valuecheck").each(function()
	 {
	 	count++;
	 	var value = $(this).val();
	 	if(value=="")
	 	{
	 		toastr.error("Please Enter Answer");
	 		
			 isValid = false;
	 	}
	 });
	  if (isValid)
       {
       var r = confirm("Are You sure want to Save Answers??");
           if (r) {
           
           return true;
           } else {
           return false;
           }
       } else {
       return false;
       }
}

</script>

</body>

</html>