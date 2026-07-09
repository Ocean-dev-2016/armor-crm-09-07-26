<?php
$page_id=580;$page_slug='price_list_master';
$ctable 	= "document_list";
$ctable1 	= "Document List";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"document_list_manage.php","title"=>"Manage ".$ctable1),array("link"=>"document_list_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/document_list.class.php");
$objDocumentList= new DocumentList();

$document_type	= "";
$class_id		= "";


	// print_r($_REQUEST['customer_type']);exit;
if(isset($_REQUEST['submit'])){
	
	$customer_type = $_REQUEST['customer_type'];
	$sales_type = $_REQUEST['sales_type'];

	for ($ctype=0; $ctype < sizeof($customer_type); $ctype++) { 
		$customer_type_str = implode(",", $customer_type);
	}

	for ($stype=0; $stype <sizeof($sales_type) ; $stype++) { 
		$sales_type_str = implode(",",$sales_type);
	}

	// echo $sales_type_str;exit;

	$detail['document_type']  = $db->clean($_REQUEST['document_type']);
	$detail['class_id']	  	  = $db->clean($_REQUEST['class_id']);
	$detail['image_path']     = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path'] = $db->clean($_REQUEST['old_image_path']);
	$detail['isDelete']		  = 0;
	$detail['customer_type']  = $customer_type_str;
	$detail['sales_type']     = $sales_type_str;
	$detail['document_name']  = $db->clean($_REQUEST['document_name']);
	// print_r($detail);exit;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objDocumentList->InsertDocument($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("document_list_manage.php?msg=inserted");
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
		$reply=$objDocumentList->UpdateNotification($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("document_list_manage.php?msg=updated");
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
		$reply=$objDocumentList->GetEditDataNotification($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objDocumentList->DeleteNotification($detail);
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
	$db->rp_location("document_list_manage.php?msg=updated");
}


	// sales type dropdown
	$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
	$sales_type = $db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
    if ($sales_type== "sales_manager") 
    {
        $sales_executive_type = "Regional Sales Manager";
    }
    else if ($sales_type == "area_sales_manager") 
    {
        $sales_executive_type = "National Sales Manager";//Business Development Manager
    }
    else if ($sales_type == "sales_officer") 
    {
        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
    }
    else if ($sales_type == "sales_executive") 
    {
        $sales_executive_type = "Sales Officer";
    }
    else
    {
        $WhereCondition.=' type = "service_engineer"';
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
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "document_list_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
		<form role="form" action="" onSubmit="return check_form();" id="form-data" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label> State <code>*</code></label>
											<select class="form-control class_id" name="class_id" id="class_id">
												<option value="">-----Select State-----</option>
												<?php
													$class_r = $db->rp_getData("class","*","isDelete=0 AND isActive=1");
													if(mysqli_num_rows($class_r)>0){
														while($class_d = mysqli_fetch_array($class_r)){
														?>
														<option <?php echo (in_array($class_d['id'],$class_id))?"selected":""; ?> value="<?php echo $class_d['id']; ?>"><?php echo $class_d['name']; ?></option>
													<?php
														}
													}
													?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="document_type">Document Type <code>*</code></label>
                                    		<select class="form-control edited autofocus" id="document_type" name="document_type" data-validation="required" data-validation-error-msg="Please Enter Type">
                    							<option value="">Select Type</option>	 
                    							<?php
                    							$document_type = $db->rp_getData("document_type","*","isDelete=0");
                    							if($document_type)
                    							{
                    								while($document_type_d = mysqli_fetch_assoc($document_type))
                    								{ ?>
                    									<option value="<?php echo $document_type_d['id']; ?>" <?php if($document_type_d['id']==$tcid){?> selected <?php } ?>><?php echo $document_type_d['name']; ?></option>
                    								<?php
                    								}
                    							} 
                    							?>
                    						</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="customer_type">Customer Type <code>*</code></label>
                                    		<select class="form-control edited autofocus" multiple id="customer_type" name="customer_type[]">
                    							<option value="">Select Type</option>	 
                    							<?php
                    							$customer_type = $db->rp_getData("customer_type","*","isDelete=0","",0);
                    							if($customer_type)
                    							{
                    								while($customer_type_d = mysqli_fetch_assoc($customer_type))
                    								{ ?>
                    									<option value="<?php echo $customer_type_d['id'] ?>" <?php if($customer_type_d['id']==$tcid){?> selected <?php } ?>><?php echo $customer_type_d['name']; ?></option>
                    								<?php
                    								}
                    							} 
                    							?>
                    						</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="sales_type">Sales Type <code>*</code></label>
                                    		<select class="form-control edited autofocus" multiple id="sales_type" name="sales_type[]" data-validation="required" data-validation-error-msg="Please Enter Type">	
	            								<option value="">Select Sales Officer Type</option>
							                    <option value="sales_manager" <?= ($sales_executive_type=="sales_manager")?"selected":""; ?>>Regional Sales Manager</option>
							                    <option value="area_sales_manager" <?= ($sales_executive_type=="area_sales_manager")?"selected":""; ?>>Business Development Manager</option>
							                    <option value="sales_officer" <?= ($sales_executive_type=="sales_officer")?"selected":""; ?>>Area Sales Manager</option>
							                    <option value="sales_executive" <?= ($sales_executive_type=="sales_executive")?"selected":""; ?>>Sales Officer</option>
							                    <option value="service_executive" <?= ($sales_executive_type=="service_executive")?"selected":""; ?>>Service Executive</option>
                    						</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>

								
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label for="document_name">Document Name<code>*</code></label>
											<input type="text" class="form-control" name="document_name" id="document_name" value="<?php echo $document_name; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
								</div>

								<div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <input data-image="<?php echo ($image_path!="" && file_exists(NOTIFICATION_A.$image_path))?NOTIFICATION_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" onchange="ImageValidation()">
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
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">

$('#customer_type').fSelect();
$('#sales_type').fSelect();

$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
 
// jQuery("#form-data").validate({
//         rules: {
//             'customer_type[]': {
//                 required: true
//             }
//         },
//     });

  // $("#form-data").validate({
  //       rules: {
  //           "customer_type[]": "required"
  //       },
  //       messages: {
  //           "customer_type[]": "Please select category",
  //       }
  //   });

function check_form(){
	$(".form-body").children().removeClass("has-error");
	// var value = $("input[name='customer_type[]']").val();
		// var arr = $('input[name="customer_type"]').map(function () {
	 //    		return this.value; // $(this).val()
		// 	}).get();
		// console.log(arr);debugger;
	var isValid=true;	
	
	if($("#class_id").val()=="" || $("#class_id").val().split(" ").join("")==""){
			
		vd=aj.error('class_id',"Please Select State","add_error");
		isValid=false;
	}

	if($("#document_type").val()=="" || $("#document_type").val().split(" ").join("")==""){
		vd=aj.error('document_type',"Please Select document Type","add_error");
		isValid=false;
	}

	if($("#document_name").val()=="" || $("#document_name").val().split(" ").join("")==""){
		vd=aj.error('document_name',"Please Fill Document Name","add_error");
		isValid=false;
	}


	if($("#image_path").val()=="" || $("#image_path").val().split(" ").join("")==""){
		vd=aj.error('image_path',"Please Image","add_error");
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
</script>

<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF","PDF","pdf"]
		);
	})

	function ImageValidation(){
	    var fileInput = document.getElementById('image_path');
	    var filePath = fileInput.value;
	    var allowedExtensions = /(\.png|\.jpeg|\.jpg|\.gif|\.pdf)$/i;
	    if(!allowedExtensions.exec(filePath)){
	        toastr.error('Please upload file having extensions .png/.jpeg/.jpg/.gif/.pdf only.');
	        fileInput.value = '';
	        return false;
	    }else{
	        //Image preview
	        if (fileInput.files && fileInput.files[0]) {
	            var reader = new FileReader();
	            reader.onload = function(e) {
	                document.getElementById('imagePreview').innerHTML = '<img src="'+e.target.result+'"/>';
	            };
	            reader.readAsDataURL(fileInput.files[0]);
	        }
	    }
	}
</script>

</body>
</html>