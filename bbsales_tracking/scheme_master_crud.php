<?php
$page_id=652;$page_slug='scheme_master';
$ctable 	= "scheme_master";
$ctable2 	= "scheme_master";
// $ctable1 	= "Top scheme";
$ctable1 	= "scheme";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable2;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.scheme_master.php");
$objTopCate= new Topscheme();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	$detail['name']				= $db->clean($_REQUEST['name']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['start_date']   	= $db->clean($_REQUEST['start_date']);
	$detail['end_date']   	= $db->clean($_REQUEST['end_date']);
	// $detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);

	$detail['isDelete']		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTopCate->Insertscheme($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("scheme_master_crud.php?mode=edit&id=".$reply['inserted_id']);
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
		$reply=$objTopCate->Updatescheme($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable2."_manage.php?msg=updated");
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
		$reply=$objTopCate->GetEditDatascheme($detail);
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
		$reply=$objTopCate->Deletescheme($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable2."_manage.php?msg=inserted");
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
	$db->rp_location($ctable2."_manage.php?msg=updated");
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
 <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable2."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
		<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-12 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label> Scheme Name <code>*</code></label>
											<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label> Scheme Start Date <code>*</code></label>
											<input type="text" class="form-control" name="start_date" id="start_date" value="<?php echo $start_date; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label> Scheme End Date <code>*</code></label>
											<input type="text" class="form-control" name="end_date" id="end_date" value="<?php echo $end_date; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable2; ?>_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>
			<?php 
				if($_REQUEST['mode'] == "edit" && $_REQUEST['id'] != "")
				{
			?>
				<div class="col-md-12 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							
							<div class="form-body">
								<div class="portlet-title">
								<h3>Add Scheme Items</h3>
								</div>
								<div class="row">
									<div class="col-md-1">
										<div class="form-group">
												<label>Type <code>*</code></label>
                                                 <select class="form-control" name="scheme_type" id="scheme_type" >
                                                    <option value="">select Type</option>
                                                   	<option value="1">SP</option>
                                                   	<option value="2">PP</option>
                                                   	<option value="3">PF</option>
                                                 </select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
												<label>Category <code>*</code></label>
                                                 <select class="form-control" name="category_id" id="category_id" >
                                                    <option value="">select Category</option>
                                                    <?php
                                                       $cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",1);
                                                       while($cat_d=mysqli_fetch_assoc($cat_r))
                                                       {
                                                       ?>
                                                    <option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
                                                    <?php
                                                       }
                                                       ?>
                                                 </select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
                                        <div class="form-group">
                                             <label>Products <code>*</code></label>
                                             <select class="form-control" name="product_id" id="product_id">
                                                <option value="">select product</option>
                                             </select>
                                             <p class="help-block"></p>
                                          </div>
                                       </div>
									<div class="col-md-1">
										<div class="form-group">
											<label> Qty <code>*</code></label>
											<input type="text" class="form-control" name="qty" id="qty" value="<?php echo $qty; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
												<label>Category <code>*</code></label>
                                                 <select class="form-control" name="category_id_2" id="category_id_2" >
                                                    <option value="">select Category</option>
                                                    <?php
                                                       $cat_r_2=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",0);
                                                       while($cat_d_2=mysqli_fetch_assoc($cat_r_2))
                                                       {
                                                       ?>
                                                    <option value="<?= $cat_d_2['id'] ?>"><?= $cat_d_2['name'] ?></option>
                                                    <?php
                                                       }
                                                       ?>
                                                 </select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
                                        <div class="form-group">
                                             <label> Free Products <code>*</code></label>
                                             <select class="form-control" name="product_id_2" id="product_id_2">
                                                <option value="">select product</option>
                                             </select>
                                             <p class="help-block"></p>
                                          </div>
                                       </div>
									<div class="col-md-1">
										<div class="form-group">
											<label> Free Qty <code>*</code></label>
											<input type="text" class="form-control" name="free_qty" id="free_qty" value="<?php echo $qty; ?>" autofocus>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-1">
										<div class="form-group">
											<button type="button" id="add_scheme_item" name="add_scheme_item" class="btn sbold blue-ebonyclay" style="margin-top: 20px;"><i class="fa fa-plus"></i> ADD</button>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div id="results"></div>
									</div>
								</div>

							</div>
							<div class="form-actions">
								
							</div>
						</div>
					</div>
				</div>
			<?php
				}
			?>
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
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
		vd=aj.error('name',"Please Enter scheme Name.","add_error");
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

	var scheme_id="<?= $_REQUEST['id']?>";
$('#start_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});

	$('#end_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});


	 $("#category_id").on('change', function() {
               var tcid = $("#category_id").val();
               getProductList(tcid);
            });

	 $("#category_id_2").on('change', function() {
               var tcid = $("#category_id_2").val();
               getProductList2(tcid);
            });

	  function getProductList(tcid) {
               var mode = '<?= $_REQUEST['mode'] ?>';
               var inquiry_id = '<?= $_REQUEST['inquiry_id'] ?>';
               if(mode=="edit")
               {
               var cid=$("#edit_dealer_id").val();
               }
               else if(mode=="add" && inquiry_id!="")
               {
               var cid=$("#dealer_id1").val();
               }
               else
               {
               var cid=$("#dealer_id").val();	
               }
               $.ajax({
                   type: "post",
                   url: "ajax_get_product.php",
                   data: "cid=" + cid+"&tcid="+tcid, 
                       beforeSend: function() {
                       $(".transCover").fadeIn(800);
                       $('.preloader').fadeIn('slow');                    
                   },
                   success: function(result) {                    
                        setTimeout(function() {
                           $('#product_id').html(result);
                           // $("#loading-modal").modal('hide');
                           $('.preloader').fadeOut('slow');
                        });
                    }               
                })
           }


      function getProductList2(tcid) {
               var mode = '<?= $_REQUEST['mode'] ?>';
               var inquiry_id = '<?= $_REQUEST['inquiry_id'] ?>';
               if(mode=="edit")
               {
               var cid=$("#edit_dealer_id").val();
               }
               else if(mode=="add" && inquiry_id!="")
               {
               var cid=$("#dealer_id1").val();
               }
               else
               {
               var cid=$("#dealer_id").val();	
               }
               $.ajax({
                   type: "post",
                   url: "ajax_get_product.php",
                   data: "cid=" + cid+"&tcid="+tcid, 
                       beforeSend: function() {
                       $(".transCover").fadeIn(800);
                       $('.preloader').fadeIn('slow');                    
                   },
                   success: function(result) {                    
                        setTimeout(function() {
                           $('#product_id_2').html(result);
                           // $("#loading-modal").modal('hide');
                           $('.preloader').fadeOut('slow');
                        });
                    }               
                })
           }



$("#add_scheme_item").on("click",function()
{
	// var product_id=$("#product_id").val();
	var weight_id = $("#product_id").find('option:selected').data("weight");
	var product_id = $("#product_id").find('option:selected').data("pro_id");
	var qty=$("#qty").val();
	// var product_id_2=$("#product_id_2").val();
	var product_id_2 = $("#product_id_2").find('option:selected').data("pro_id");
	var weight_id_2 = $("#product_id_2").find('option:selected').data("weight");
	var free_qty=$("#free_qty").val();
	var scheme_type=$("#scheme_type").val();
	if(product_id!="" && product_id_2!="" &&  qty!="" && free_qty!="") 
	{
		$.ajax({
		type: "POST",
		url: "add_scheme_master_item_ajax.php",
			data: {
				product_id:product_id,
				weight_id:weight_id,
				qty:qty,
				product_id_2:product_id_2,
				weight_id_2:weight_id_2,
				free_qty:free_qty,
				scheme_type:scheme_type,
				scheme_id:scheme_id,
				mode:"add_scheme_item",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					$("#scheme_type").select2('val',"");
					$("#category_id").select2('val',"");
					$("#category_id_2").select2('val',"");
					$("#product_id").select2('val',"");
					$("#qty").val('');
					$("#product_id_2").select2('val',"");
					$("#free_qty").val('');
					// $('.area_id').fSelect();
					getscheme_items();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}	
	else
	{
		toastr.error("Please Select Required Fields");
	}
});


function del_conf(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r)
	{
		$.ajax({
			type: "POST",
			url: "add_scheme_master_item_ajax.php",
			data: {
				scheme_id:scheme_id,
				id:id,
				mode:"delete_scheme_items",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					getscheme_items();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
			});
	}
}



function getscheme_items()
{	
	$.ajax({
		type: "POST",
		url: "scheme_master_item_get_ajax.php",
		data: {
			scheme_id:scheme_id,
		},
		cache: false,
		beforeSend: function() {
			
		},
		success: function(json)
		{
			$("#results").html(json);
		}
		});
}

$(document).ready(function() {
	getscheme_items();
});
           
               
           
</script>

</body>
</html>