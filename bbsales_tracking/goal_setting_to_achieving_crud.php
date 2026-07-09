<?php
$page_id=661;$page_slug='goal_setting_to_achieving';
include("connect.php");

$ctable 	= "goal_setting_to_achieving";

$ctable1 	= "goal_setting_to_achieving";

$main_page 	= "HR";

$page 		= "goal_setting_to_achieving_manage";

$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;

$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"employee_information_manage.php","title"=>"Goal Setting To Goal Achieving"),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit Goal Setting To Goal Achieving"));
$employee_id = $_REQUEST['employee_id'];
$sales_executive_r=$db->rp_getData('sales_executive_information',"CONCAT(first_name, ' ', middle_name, ' ', surname, ' - ', contact_no) AS full_name_contact,id","id = ".$employee_id);
$sales_executive_d = mysqli_fetch_array($sales_executive_r);


if(isset($_REQUEST['submit'])){

  $sales_executive_form_id = $_REQUEST['sales_executive_form_id'];
	$professional		= $_REQUEST['professional'];
	$personal		= $_REQUEST['personal'];
	$answer1		=$_REQUEST['answer1'];
	$answer2		=$_REQUEST['answer2'];
	$answer3		=$_REQUEST['answer3'];
	$answer4		=$_REQUEST['answer4'];
	$answer5		=$_REQUEST['answer5'];
	$answer6		=$_REQUEST['answer6'];
	$answer7		=$_REQUEST['answer7'];
	$answer8		=$_REQUEST['answer8'];
	$answer9		=$_REQUEST['answer9'];

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		$rows 	= array(
           
          "sales_executive_form_id",
					"professional",
					"personal",
					"answer1",
					"answer2",
					"answer3",
					"answer4",
					"answer5",
					"answer6",
					"answer7",
					"answer8",
					"answer9",
				);

		$values = array(
          
          $sales_executive_form_id,
					$professional,
					$personal,
					$answer1,
					$answer2,
					$answer3,
					$answer4,
					$answer5,
					$answer6,
					$answer7,
					$answer8,
					$answer9,
				);

		$inserted_id = $db->rp_insert($ctable,$values,$rows);
		
		$db->addSuccessMessage(" Goal Setting To Goal Achieving Inserted successfully!");
		$db->rp_location("employee_information_manage.php?msg=inserted");

		

		

	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){

		$rows 	= array(

          "sales_executive_form_id"=>$sales_executive_form_id,
					"professional"		=> $professional,
					"personal"	=> $personal,
					"answer1"=> $answer1,
					"answer2"=> $answer2,
					"answer3"=> $answer3,
					"answer4"=> $answer4,
					"answer5"=> $answer5,
					"answer6"=> $answer6,
					"answer7"=> $answer7,
					"answer8"=> $answer8,
					"answer9"=> $answer9,

				);

		$where	= "id=".$_REQUEST['id'];

		$db->rp_update($ctable,$rows,$where,0);
			$db->addSuccessMessage("Data Updated successfully!");
		$db->rp_location("employee_information_manage.php?msg=updated");

		

	}

}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){

	$where = " id='".$_REQUEST['id']."' AND isDelete=0";

	$ctable_r = $db->rp_getData($ctable,"*",$where);

	$ctable_d = mysqli_fetch_array($ctable_r);
  
  $sales_executive= $ctable_d['sales_executive_form_id'];
	$professional		= stripslashes($ctable_d['professional']);
	$personal		= stripslashes($ctable_d['personal']);
	$answer1 = stripslashes($ctable_d['answer1']);
	$answer2 = stripslashes($ctable_d['answer2']);
	$answer3 = stripslashes($ctable_d['answer3']);
	$answer4 = stripslashes($ctable_d['answer4']);
	$answer5 = stripslashes($ctable_d['answer5']);
	$answer6 = stripslashes($ctable_d['answer6']);
	$answer7 = stripslashes($ctable_d['answer7']);
	$answer8 = stripslashes($ctable_d['answer8']);
	$answer9 = stripslashes($ctable_d['answer9']);

}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){

	$where = " id='".$_REQUEST['id']."'";
        $rows 	= array(
    		    "isDelete"	=> "1"
    	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->addSuccessMessage("Data Deleted successfully!");
	$db->rp_location("employee_information_manage.php?msg=deleted");

}

?>


<!DOCTYPE html>


<html lang="en">

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

.jander2{
  font-weight: bold;
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
	<div>
		<div class="container" style="background-color: #fff;">
			<?php $db->getMessageBlock(); ?>	
			<form role="form" action="" onSubmit="return check_form();" method="post">
				<div class="row" >
					<div class="col-md-6">
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
											<div class="form-group">
												<br>
											  	<div >
														<p style="font-size:18px;"><b>If we were meeting here three years from now, what do you think should have happened in your both life, both personally and professionally for you to feel happy about your progress?"</b></p><br><br>
														<input readonly class="form-control jander2" type="text" value="Professional">
														<textarea class="form-control  custom-input" name="professional" id="professional"><?= $professional; ?>
														</textarea><br>
														<input readonly class="form-control jander2" type="text" value="Personal">
														<textarea class="form-control custom-input" id="personal" name="personal"><?= $personal; ?>
														</textarea><br>
											    </div>
										    </div>
										</div>
									</div>
								</div>
							</div>
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">
										<div class="col-md-12" style="margin-top: 3%;">
	                    <div>
                        <p style="font-size:18px;"><b>What are your Roadblocks and challenges are from the market place that can stop you from achieving your goal?</b></p><br><br>
									      <textarea class="form-control custom-input" name="answer1" id="answer1"><?= $answer1; ?></textarea><br>
												<textarea class="form-control custom-input" name="answer2" id="answer2"><?= $answer2; ?></textarea><br>
												<textarea class="form-control custom-input" name="answer3" id="answer3"><?= $answer3; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">
										<div class="col-md-12" style="margin-top: 3%;">
	                    <div >
	                      <p style="font-size:18px;"><b>What support/help you require from Management to achieve your goal?</b></p><br><br>
								      	<textarea class="form-control custom-input" name="answer4" id="answer4"><?= $answer4; ?></textarea><br>
											 	<textarea class="form-control custom-input" name="answer5" id="answer5"><?= $answer5; ?></textarea><br>
											 	<textarea class="form-control custom-input" name="answer6" id="answer6"><?= $answer6; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">
										<div class="col-md-12" style="margin-top: 13%;">
	                    <div >
	                      <p style="font-size:18px;"><b>What are your Personal Roadblocks and challenges that can stop you from achieving your goal?</b></p><br><br>
										    <textarea class="form-control custom-input" name="answer7" id="answer7"><?= $answer7; ?></textarea><br>
											 	<textarea class="form-control custom-input" name="answer8" id="answer8"><?= $answer8; ?></textarea><br>
												<textarea class="form-control custom-input" name="answer9" id="answer9"><?= $answer9; ?></textarea>
											</div>
									 	</div>
									</div>
								</div>
							</div>
							<div class="portlet-body form">
								<div class="form-body">
									<div class="form-actions text-left" style="margin-bottom: 10px;">
										<button type="submit" name="submit" class="btn green">Submit</button>
										<button type="button" class="btn btn-default" onClick="window.location.href='employee_information_manage.php'">Back</button>
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
		isValid = false;
	}	

	if($("#professional").val()=="" || $("#professional").val().split(" ").join("")=="")
	{
		vd=aj.error('professional',"Please Enter Professional.","add_error");
		isValid = false;
	}

	if($("#personal").val()=="" || $("#personal").val().split(" ").join("")=="")
	{
		vd=aj.error('personal',"Please Enter Personal.","add_error");
		isValid = false;
	}
	
	  if (isValid)
       {
       
         return true;
          
       } 
       else {
       return false;
       }
}



</script>

</body>

</html>