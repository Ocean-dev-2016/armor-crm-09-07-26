<?php
$page_id=408;$page_slug='page_vendor';
$page_slug="manage_vendor";
$ctable 	= "api_table";
$ctable1 	= "API Table";
$main_page 	= $ctable;
include("connect.php");
require_once("../include/class.api.php");
$page 		= "add_".$ctable;
$id=$_REQUEST['id'];
$title=$db->rp_getValue("api_table","api_title","id='".$_REQUEST['id']."'");
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1." ".$title;

$api_obj=new Api();
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"a";
$id='';
$api_slug='';
$api_url='';
$api_title='';
$author='';
$api_description='';
$last_modification_date='';
$isActive		= 0;
//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);

if(isset($_REQUEST['submit'])){

$params=array();
//if(isset($_REQUEST['id']))
$params['id']=trim($db->clean($_REQUEST['id']));
$params['api_slug']=trim($db->clean($_REQUEST['api_slug']));
$params['api_title']=trim($db->clean($_REQUEST['api_title']));
$params['api_url']=trim($db->clean($_REQUEST['api_url']));
$params['api_description']=trim($db->clean($_REQUEST['api_description']));
$params['author']=trim($db->clean($_REQUEST['author']));
$params['last_modification_date']=date("Y-m-d H:i:s");

	if($mode=='a')
	{ 
		//$params['created_date']=date("Y-m-d");
	    $reply=$api_obj->insert($params,array("key"=>"api_title","value"=>$params['api_title']));
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
            $db->rp_location("api_manage.php");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location("api_manage.php");
		}
	}
	 else if($mode=='e')
	{
		$reply=$api_obj->update($params,$params['api_title'],$params['id']);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
            $db->rp_location("api_manage.php");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location("api_manage.php");
		}
	}
	
}

 if($mode=='d')
	{
		$params['id']=$_REQUEST['id'];
		$reply=$api_obj->delete(array("key"=>"id","value"=>$params['id']));
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
            $db->rp_location("api_manage.php");
		}
		else
		{
		    $db->addErrorMessage($reply['ack_msg']);
			$db->rp_location("api_manage.php");
		}
	} 
if($mode=="e")
{

	// GET REQUIRED VALUE FROM DATABASE

	$id=trim($db->clean($_REQUEST['id']));

	$reply=$api_obj->view("id='".$id."'");
	if($reply['ack']==1)
	{
		extract($reply['result'][0]);
	}
	else
	{
		 $db->addErrorMessage($reply['ack_msg']);
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
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>

</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="api_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
		<form id="inquiry_form_distributor" onSubmit="return check_form()" action="" method="post">

<div class="row">
	<div class="col-md-12">
		<div class="portlet grey-cascade box">
			<div class="portlet-title">
				<div class="caption">
				   <i class="fa fa-user"></i> &nbsp; API
				</div>
			</div>
			<div class="portlet-body">
			   
				<div class="row">																													
						
						<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<label for="address">API Slug<code>*</code></label>
								<input type="text" class="form-control" name="api_slug" id="api_slug" value="<?php echo $api_slug; ?>" >
								<p class="help-block"></p>	
							</div>
							</div>
							<div class="col-md-6">
							<div class="form-group">
								<label for="address">API Title<code>*</code></label>
								<input type="text" class="form-control" name="api_title" id="api_title" value="<?php echo $api_title; ?>">
								<p class="help-block"></p>	
							</div>
							</div>
						</div>
							
							<div class="row">																			
								<div class="col-md-6">
									<div class="form-group">
									<label>API URL <code>*</code></label>
											<div class="form-group">
											<input type="text" class="form-control" name="api_url" id="api_url" value="<?php echo $api_url; ?>" >
											</div>
											<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									<label >Author<code>*</code></label>
									<input type="text" class="form-control" name="author" id="author" value="<?php echo $author; ?>" >
									<p class="help-block"></p>		
									</div>
								</div>
							</div>
							<div class="row">
								<!--<div class="col-md-6">
									 <div class="form-group">
                                            <label>Last Modification Date<code>*</code> </label>
                                            <input type="text" class="form-control datepicker" name="last_modification_date" id="last_modification_date" value="<?php echo $last_modification_date; ?>" >
											<p class="help-block"></p>
										</div>
								</div>	-->
							<div class="col-md-12">
								<div class="form-group">
									<label>Description </label>
									<textarea class="form-control textarea" name="api_description" id="api_description"><?php echo $api_description; ?></textarea>
									<p class="help-block"></p>	
								</div>
							</div>										
							</div>															
						</div>	
						</div>
<div class="row">
<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
<button type="submit" name="submit" id="submit" class="btn btn-primary" value="Submit">Submit</button>
<button type="button" class="btn btn-primary" onClick="window.location.href='api_manage.php'">Back</button>
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
<script src="js/jquery.form-validator.min.js"></script>
<script src="js/ckeditor/ckeditor.js" type="text/javascript"></script>    
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
$('#last_modification_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy'});
    <!-- END PAGE LEVEL SCRIPTS -->

  $.validate({
	lang: 'en'
  });
 $(document).ready(function(){
		CKEDITOR.replace('api_description');
}); 

$('.form-control').each(function(i,v){
	ckInstnaces[$(v).prop("id")]=(CKEDITOR.replace($(v).prop("id")));
});


$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
function check_form(){
	$(".form-body").children().removeClass("has-error");
    var isValid=true;
    if($("#api_title").val()=="" || $("#api_title").val().split(" ").join("")==""){
		vd=aj.error('api_title',"Please Enter API Title!!","add_error");
        isValid=false;
    }
	if($("#api_url").val()=="" || $("#api_url").val().split(" ").join("")==""){
		vd=aj.error('api_url',"Please Enter API URL!!","add_error");
        isValid=false;
    }
	if($("#api_slug").val()=="" || $("#api_slug").val().split(" ").join("")==""){
		vd=aj.error('api_slug',"Please Enter API Slug!!","add_error");
        isValid=false;
    }
	/* if($("#api_description").val()=="" || $("#api_description").val().split(" ").join("")==""){
		vd=aj.error('api_description',"Please Enter API Description!!","add_error");
        isValid=false;
    } */
	if($("#author").val()=="" || $("#author").val().split(" ").join("")==""){
		vd=aj.error('author',"Please Enter Author Name!!","add_error");
        isValid=false;
    }
	/*if($("#last_modification_date").val()=="" || $("#last_modification_date").val().split(" ").join("")==""){
		vd=aj.error('last_modification_date',"Please Enter Date!!","add_error");
        isValid=false;
    }*/
	
	
    if(isValid)
    {
        return true;
    }
    else
    {
        return false;
    }
}




</script>
<!-- END PAGELEVEL JS -->
    </body>
</html>	