<?php
$page_id=651;$page_slug='deep_freezer_scheme';
$ctable 	= "freezer_scheme";
$ctable1 	= "freezer_scheme";
$page 		= $ctable."_manage";
 $page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"deep_freezer_scheme_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

include("connect.php");
include('../include/class.deep_freezer_scheme.php');
$objFreezer=new FreezerScheme();

$customer_id			= "";
$serial_no			= "";
// $current_date			= "";
$shop_name		        = "";
$contact_person		    = "";
$mobile_no			    = "";
$address			    = "";
$taluka			         = "";
$district			     = "";
$state			        = "";
$distributor_agency			        = "";
$center			        = "";
$freeze_model_no	= "";
$hard_top		= "";
$class_top			= "";
$agency_permises_photo			= "";
$dealer_image			= "";
$distributor_image			= "";
$company_office_image			= "";
$dealer_name			= "";
$dealer_mo			= "";
$dealer_sign			= "";
$distributor_name			= "";
$distributor_mob			= "";
$distributor_sign			= "";
$company_office_name			= "";
$company_office_mob			= "";
$company_office_sign			= "";
$image_path			= "";
$old_image_path			= "";
$executive_type			= "";
$old_file_path          ="";
$old_file_path1          ="";
$old_file_path2          ="";
$old_file_path3          ="";
$dealer_sign_image          ="";
$distributor_sign_image          ="";
$company_office_sign_image          ="";
$old_file_path4          ="";
$old_file_path5          ="";
$old_file_path6          ="";
$payment          ="";
$language          ="";
$utr          ="";


if(isset($_REQUEST['submit'])){

	// echo $detail;exit();
	// print_r($_FILES);exit();
	$detail['customer_id'] 	    = $db->clean($_REQUEST['customer_id']);
	$detail['serial_no']      = $db->clean($_REQUEST['serial_no']);
	// $detail['current_date']   = $db->clean($_REQUEST['current_date']);
	$detail['shop_name']       = $db->clean($_REQUEST['shop_name']);
	$detail['contact_person']          = $db->clean($_REQUEST['contact_person']);
	$detail['mobile_no']              = $db->clean($_REQUEST['mobile_no']);
	$detail['address']       = $db->clean($_REQUEST['address']);
	$detail['taluka']                = $db->clean($_REQUEST['taluka']);
	$detail['district']                 = $db->clean($_REQUEST['district']);
	$detail['state']               = $db->clean($_REQUEST['state']);
	$detail['distributor_agency']  = $db->clean($_REQUEST['distributor_agency']);
	$detail['center']   = $db->clean($_REQUEST['center']);
	$detail['freeze_model_no']        = $db->clean($_REQUEST['freeze_model_no']);
	$detail['hard_top']        = $db->clean($_REQUEST['hard_top']);
	$detail['class_top']        = $db->clean($_REQUEST['class_top']);
	$detail['agency_permises_photo']        = $db->clean($_REQUEST['agency_permises_photo']);
	$detail['dealer_image']        = $db->clean($_REQUEST['dealer_image']);
	$detail['distributor_image']        = $db->clean($_REQUEST['distributor_image']);
	$detail['company_office_image']        = $db->clean($_REQUEST['company_office_image']);
	$detail['dealer_name']        = $db->clean($_REQUEST['dealer_name']);
	$detail['dealer_mo']        = $db->clean($_REQUEST['dealer_mo']);
	$detail['dealer_sign']        = $db->clean($_REQUEST['dealer_sign']);
	$detail['distributor_name']        = $db->clean($_REQUEST['distributor_name']);
	$detail['distributor_mob']        = $db->clean($_REQUEST['distributor_mob']);
	$detail['distributor_sign']        = $db->clean($_REQUEST['distributor_sign']);
	$detail['company_office_name']        = $db->clean($_REQUEST['company_office_name']);
	$detail['company_office_mob']        = $db->clean($_REQUEST['company_office_mob']);
	$detail['company_office_sign']        = $db->clean($_REQUEST['company_office_sign']);
	$detail['image_path']           = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']       = $db->clean($_REQUEST['old_image_path']);
	$detail['executive_type']       = $db->clean($_REQUEST['executive_type']);
	$detail['old_file_path'] =    isset($_REQUEST['old_file_path'])?$db->clean($_REQUEST['old_file_path']):"";
	$detail['old_file_path1'] =    isset($_REQUEST['old_file_path1'])?$db->clean($_REQUEST['old_file_path1']):"";
	$detail['old_file_path2'] =    isset($_REQUEST['old_file_path2'])?$db->clean($_REQUEST['old_file_path2']):"";
	$detail['old_file_path3'] =    isset($_REQUEST['old_file_path3'])?$db->clean($_REQUEST['old_file_path3']):"";
	$detail['dealer_sign_image']           = $db->clean($_REQUEST['dealer_sign_image']);
	$detail['distributor_sign_image']           = $db->clean($_REQUEST['distributor_sign_image']);
	$detail['company_office_sign_image']           = $db->clean($_REQUEST['company_office_sign_image']);
	$detail['old_file_path4'] =    isset($_REQUEST['old_file_path4'])?$db->clean($_REQUEST['old_file_path4']):"";
	$detail['old_file_path5'] =    isset($_REQUEST['old_file_path5'])?$db->clean($_REQUEST['old_file_path5']):"";
	$detail['old_file_path6'] =    isset($_REQUEST['old_file_path6'])?$db->clean($_REQUEST['old_file_path6']):"";
	$detail['payment']        = $db->clean($_REQUEST['payment']);
	$detail['language']       = $db->clean($_REQUEST['language']);
	$detail['sales_id'] 	  = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
	$detail['entry_flag'] 	  = "1";
	$detail['utr']       = $db->clean($_REQUEST['utr']);

	
	// $detail['entry_flag']           ='3';
	 // echo $detail['agency_permises_photo'];exit();

// print_r ($_REQUEST);exit();
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		// echo "add";exit();
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objFreezer->AddFreezeScheme($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("deep_freezer_scheme_manage.php?msg=inserted");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		// echo "hello";exit();
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}

		$reply=$objFreezer->UpdateFreezeScheme($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("deep_freezer_scheme_manage.php?msg=updated");
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
		$reply=$objFreezer->GetEditDataFreezerScheme($detail);
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
		$reply=$objFreezer->DeleteNoOrderInquiry($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("manage_complain.php?msg=inserted");
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
	$db->rp_location("manage_complain.php?msg=updated");
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>

<style type="text/css">
	.mainDiv, table{
	border: 1px solid #000000;
	border-collapse: collapse;
	/*font-size: 13px;*/
	width:250mm!important;
	background-color: #FFF;
	margin:auto;
  	padding:auto;
  	/*margin-top: 20px!important;
  	margin-bottom: 20px!important;*/
}

table , td, th {
	border: 1px solid #000000;
}
td, th {
	padding: 5px;
	height: 25px;
}

/*.select2
{
	width: 300px!important;
}*/
.no-border-left{
	border-left: hidden;
}
.no-border-right{
	border-right: hidden;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.no-border-top{
	border-top: hidden !important;
}
.bootstrap-timepicker-widget table
{
	width: 100% !important;
}
input.larger {
        width: 30px;
        height: 30px;
      }

</style>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
		<div class="page-toolbar">
	<div class="page-content">

	<!-- 	<div class="container">
			<div class="row">
				<div class="col-sm-4">
		<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport();" title="Print">Print</a>
	</div>
</div>
	</div> -->
</div>
</div>
	<div class="row">
		<div class="col-sm-12">
			 <?php $db->printErrorMessage(); ?>
			 <?php $db->printSuccessMessage(); ?>		 
		</div>
			</div>
   <form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
				<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-body form">
					<div class="form-body">
						<!-- <h4><b>Service Report</b></h4>
						<hr/> -->
						<div>
							<table>
								<tbody>
									<tr>
		                           <td colspan="16">
			                          <img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_SCHEME ?>">
		                            </td>
	                                </tr>
					<tr>
						<!-- <td colspan="4" class="no-border-right"></td> -->
						<td colspan="16" class="color" align="center" style="background-color: #E5E5E5;height: 40px;"><strong>DEEP FREEZER SCHEME</strong></td>
						
						<!-- <td colspan="4"></td> -->
					</tr>
					<tr>
						<?php
						if($_REQUEST['mode'] == 'add')
				       {
						$last_inserted_id = $db->getlastInsertId("freezer_scheme"); 
					 
					   $serial_no=str_pad($last_inserted_id, 3, '0', STR_PAD_LEFT);
					}
					else{

						$serial_no= $serial_no;
					}

					
					?>
	                                 <td colspan="8" style="border: none;"></td>  
	                                 <td colspan="8" style="text-align: right;border: none;"><b>Serial No. :</b><u><?php echo $serial_no?></u></td>

                                       </tr>
					<tr>
	                                 <td colspan="8" style="border: none;"></td>  
	                                 <td colspan="8" style="text-align: right;border: none;"><b>Date :</b> <?= date("d-m-Y h:i a");?></td>

	                                 </tr>
	                               <tr>
	                               	<td colspan="8" style="border-right: none;">
	                                 <div class="col-md-12">
	                                 	<div class="row">
					       <div class="col-md-6">
					      <div class="form-group">
						<label>Type Of Customer<code>*</code></label>
						<select class="form-control" id="executive_type" name="executive_type"    onchange="Getcustomer(this.value);">
                     <option value="">Select Customer Type</option>
                     <?php   
							$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
							$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='". $check_id."'",0);
							?> 
							<?php
							if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) 
							{ 
							if($get_customer_type==1)
							{ 
							?>
							<option value="1">Super Stockist</option> 
							<?php 
							}  
							?>
							<option value="2">Distributor</option>  
							<?php 
							if($get_customer_type!=1)
							{ 
							?>
							<option value="3">Retailer</option>    
							<?php 
							}
							}
							else
							{ 
							$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
							if ($cust_R) {
								while ($C = mysqli_fetch_assoc($cust_R)) {
							?>
							<option value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
							<?php
								}
							}
							}
							?>
                 </select> 
				<p class="help-block"></p>
			</div>
		</div>
				</div>
			</div>
		</td>
	<td colspan="8" style="border-left: none;">
             <div class="col-md-12">
                <div class="row">
		<div class="col-md-6">
		<div class="form-group">
			<label class="test">Company</label>
			<select class="form-control" id="customer_id" name="customer_id" onchange="GetcustomerInfo(this.value);">>
		                        <option value="">Select Company</option>
		                         
		                        <?php
		                        $customer_r = $db->rp_getData("executive","*","isDelete=0","",0);
		                        if($customer_r)
		                        {
		                            while($customer_d = mysqli_fetch_assoc($customer_r))
		                            {?>
		                                <option value="<?=$customer_d['id']?>" <?=($customer_id == $customer_d['id'])?"selected":"";?>><?=$customer_d['company_name']?></option>
		                            <?php
		                            }
		                        } 
		                        ?>
		                          </select> 
		                          <p class="help-block"></p>
					</div>
				</div>
			</div>
		</div>
			</td>
		</tr>
		<tr>
		<td colspan="8" style="border-right: none;">
		
	<div class="col-md-12">
 	<div class="row">
	<div class="col-md-6">
	<div class="form-group">
		<label>Customer</label>
		<td colspan="8" style="border-left: none;"><input readonly class="form-control" name="shop_name" id="shop_name" value="<?php echo $shop_name?>">
		<p class="help-block"></p></td>
		
	</div>
	</div>
	</div>
          </div>

		</td>
				</tr>

			<tr>
			<td colspan="4" style="border-right: none;">

			<div class="col-md-12">
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
				<label>Contact person</label>
				<td colspan="4" style="border-left: none; border-right: none;"><input readonly class="form-control" name="contact_person" id="contact_person" value="<?php echo $contact_person?>">
				<p class="help-block"></p></td>
			</div>

			</div>
			<td colspan="4" style="border-right: none; border-left: none;">
			<div class="col-md-6">
			<div class="form-group">
				  <label>Mo.</label>
				  <td  colspan="4" style="border-left: none;"><input readonly class="form-control" name="mobile_no" id="mobile_no" value="<?php echo $mobile_no?>">
				   <p class="help-block"></p></td>


			</div>
			</div>
			</td>
			</div>
			</div>		

			</td>
			</tr>
			<tr>
			<td colspan="8" style="border-right: none;">

			 <div class="col-md-12">
			<div class="row">

			<div class="col-md-6">
			<div class="form-group">
				<label>Full Address</label>
				<td colspan="8" style="border-left: none;"><textarea  readonly class="form-control" name="address" id="address"><?= $address; ?></textarea></td>
				<p class="help-block"></p>
			</div>
			</div>

			</div>
			</div>


			</td>
			</tr>

			<tr>
			<td colspan="4" style="border-right: none;">

			<div class="col-md-12">
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
				<label>Taluka</label>
				<td  style="border-left: none; border-right: none;"><input  class="form-control" name="taluka" id="taluka" value="<?php echo $taluka?>">
				<p class="help-block"></p></td>
			</div>

			</div>
			<td colspan="4" style="border-right: none; border-left: none;">
			<div class="col-md-6">
			<div class="form-group">
				  <label>District</label>
				  <td  style="border-left: none; border-right: none;"><input  class="form-control" name="district" id="district" value="<?php echo $district?>">
				   <p class="help-block"></p></td>


			</div>
			</div>
			</td>
			<td colspan="4" style="border-right: none; border-left: none;">
			<div class="col-md-6">
			<div class="form-group">
				  <label>State</label>
				  <td  style="border-left: none;"><input readonly class="form-control" name="state" id="state" value="<?php echo $state?>">
				   <p class="help-block"></p></td>


			</div>
			</div>
			</td>

			</div>
			</div>		

			</td>
			</tr>

			<tr>
			<td colspan="4" style="border-right: none; border-left: none;">
			<div class="col-md-12">
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
		       <label class="test">Distributor Agency Name</label>		
		       <td colspan="4"  style="border-right: none; border-left: none;">	
		       	    <select class="form-control" id="distributor_agency" name="distributor_agency" disabled>
                               <option value="">Select Distributor</option>

		                <?php
		                $distributor_r = $db->rp_getData("executive","*","isDelete=0 AND type_of_executive=2","",0);
		                if($distributor_r)
		                {
		                    while($distributor_d = mysqli_fetch_assoc($distributor_r))
		                    {
		                    	?>
		                        <option value="<?=$distributor_d['id']?>" <?=($distributor_agency == $distributor_d['id'])?"selected":"";?>><?=$distributor_d['company_name']?></option>
		                    <?php
		                    }
		                } 
		                ?>
		                
		                ?>
		            </select>
		          </td>
		          <p class="help-block"></p>
			</div>
			</div>
                         <td colspan="4" style="border-right: none; border-left: none;">
			     <div class="col-md-6">
			     <div class="form-group">
			      <label>Center</label>
				  <td colspan="4" style="border-left: none;">
                                 <input  class="form-control" name="center" id="center" value="<?php echo $center?>">
				   <p class="help-block"></p></td>
				</div>
				</div>
		       </td>
		       </div>
                       </div>
		</td>
		   </tr>

		<tr> 
		<td colspan="2" style="border-right: none;">
		    	<div class="col-md-12">
                    	<div class="row">
				<div class="col-md-6">
				<div class="form-group" style="width:100px;">
           			 <label>Freeze Model No.</label>
				   <input  class="form-control"  name="freeze_model_no" id="freeze_model_no" value="<?php echo $freeze_model_no?>">
				   <p class="help-block"></p>
				</div>
			      </div>
			<td colspan="2" style="border-right: none; border-left: none;">
			     <div class="col-md-6">
			     <div class="form-check">
					  <label>Hard Top</label>
					 	<?php
					if($_REQUEST['mode'] == 'add')
			         {
					
			  	?><input  class="form-check-input larger" type="checkbox" name="hard_top" id="hard_top" value="1<?php if($hard_top == 1){ echo "checked";} ?>">
					   <p class="help-block"></p>
						<?php 
			
			          }

					else
					{
						?>
                      <input  class="form-check-input larger" type="checkbox" name="hard_top" id="hard_top" <?php if($hard_top == 1){ echo "checked";} ?>>
                     <p class="help-block"></p>
              <?php
		}
		?>
			</div>
		    </div>
							
        </td>
		<td colspan="2" style="border-right: none; border-left: none;">	
		     <div class="col-md-6">
		     <div class="form-check">
				  <label>Glass Top</label>
				
				  	<?php
				if($_REQUEST['mode'] == 'add')
		       {
				
			?><input  class="form-check-input larger" type="checkbox" name="class_top" id="class_top" value="1<?php if($class_top == 1){ echo "checked";} ?>">
				   <p class="help-block"></p>
				   <?php
				}
		
			 
				else{
					?>
                      <input  class="form-check-input larger" type="checkbox" name="class_top" id="class_top"  <?php if($class_top == 1){ echo "checked";} ?>>
                     <?php
								}
								?>
							</div>
						</div>
								 </td>

					<td colspan="2" style="border-right: none; border-left: none;">	
						     <div class="col-md-6">
						    <div class="form-group ">
						<label for="mobile_no">Payment</label>

						<select class="form-control" name="payment" id="payment" style="width: 150px;">
							<option value="">Select Payment</option>
							<option value="1"<?php if($payment== 1){ echo "selected";} ?>>Cheque </option>
							<option value="2" <?php if($payment== 2){ echo "selected";}?>>RTGS</option>
						</select>
						<span class="help-block"></span>
					</div>
						</div>
					</td>
					<td colspan="4" style="border-right: none; border-left: none;">
						<div class="col-md-6">
						    <div class="form-group">
								<label for="">Utr</label>
								<input  class="form-control" name="utr" id="utr" value="<?php echo $utr?>" style="width: 200px;">
								<p class="help-block"></p>
						     </div>

						</div>
						
				    </td>

								  
			         </div>
					</div>		
			        <td colspan="4" style="border-right: none; border-left: none;">
			        <div class="col-md-6">							   
			         <div class="form-group">
				<label for="mobile_no">Language</label>
				<select class="form-control" name="language" id="language" onchange style="width: 150px;">
					<option data-language="<?=TERMS_CONDITION_GUJRATI?>" value="1" <?php if($language == 1){ echo "selected";} ?>>Gujarati</option>
					<option  data-language="<?=TERMS_CONDITION_HINDI?>" value="2" <?php if($language == 2){ echo "selected";}?>>Hindi</option>
					<option data-language="<?=TERMS_CONDITION_ENGLISH?>" value="3"  <?php if($language == 3){ echo "selected";}?>>English</option>
				</select>
				<span class="help-block"></span>
			</div>
			 </td>
			</tr>

				<tr>
				<td colspan="16">
				<span style="text-align:center; font: 50px;" class="language1" ></span>
				</td>
				</tr>
				</tbody>
				</table>
				<table>
				<tbody>
				<tr>
				<td colspan="16">
				<div class="form-group">
				<input data-image="<?php echo ($image_path!="" && file_exists(DOCUMENT_LIST.$image_path))?DOCUMENT_LIST.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
				<input type="hidden" name="old_image_path" id="old_image_path" value="<?php echo $image_path;?>">
				</div>
				<?php 
				if($_REQUEST['mode']="edit") 
				{

				if($image_path!="")
				{
				$img = explode(",", $image_path);
				$imgpath = array();
				for ($i=0; $i < sizeof($img); $i++)
				{ 
				$imgpath[] = "../images/document_list/".$db->rp_getValue("media","url","reference_id='".$_REQUEST["id"]."' AND id='".$img[$i]."'",0);
				}

				for ($i=0; $i < sizeof($imgpath); $i++)
				{
				?>

				<a href="<?=$imgpath[$i]?>" data-lightbox="scheme<?=$count?>" data-title="scheme<?=$_REQUEST['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
				}
				}
				}
				?>		
				</td>	

				</tr>
				<tr>
				<td colspan="16">
				<div class="card text-center border" style="border-color:black;">
				<div class="card-body">
				<h5 class="card-title">Agency Permises Photo = 6*4</h5>
				<label>Attachment</label>
				<input width="226.77" height="151.18" data-image="<?php echo ($agency_permises_photo!="" && file_exists(AGENCY_PERMISES_PHOTO_A.$agency_permises_photo))?AGENCY_PERMISES_PHOTO_A.$agency_permises_photo:"";?>" type="file" accept="image/*" name="agency_permises_photo" id="agency_permises_photo" data-old-image-dom="old_file_path" data-old-image-path="<?php echo $agency_permises_photo ?>" value="">

				</div>

				</div>

				</td>	

				</tr>
				<tr>

				<td colspan="2" style="border-right: none;border-left: none; text-align: left;" >
				<div class="card " style="width:250px">   
				<div class="card-body border img-thumbnail" style="border-color:black;">

				<p class="card-text">3.5*4.5CM <br/>Passport Photo</p>
				<input width="132" height="170" data-image="<?php echo ($dealer_image!="" && file_exists(DEALER_PHOTO_A.$dealer_image))?DEALER_PHOTO_A.$dealer_image:""; ?>"  type="file" accept="image/*" name="dealer_image" id="dealer_image"  data-old-image-dom="old_file_path1" data-old-image-path="<?php echo $dealer_image ?>" value="" >
				</div>
				</div>
				<h5 style="text-align:center;"><b>Dealer</b></h5>
				<lable>Name</lable>:	<input type="text"  name="dealer_name" id="dealer_name" value="<?php echo $dealer_name?>" style="width: 108px;"><br/><br/>   
				<lable>Mo.</lable>:<input type="text" name="dealer_mo" id="dealer_mo" value="<?php echo $dealer_mo?>" style="width: 108px;"><br/><br/>

				<lable>Sign</lable>:	<input type="text" name="dealer_sign" id="dealer_sign" value="<?php echo $dealer_sign?>" style="width: 108px;">
				<input width="132" height="170" data-image="<?php echo ($dealer_sign_image!="" && file_exists(DEALER_PHOTO_SIGN_A.$dealer_sign_image))?DEALER_PHOTO_SIGN_A.$dealer_sign_image:"";?>" type="file" accept="image/*" name="dealer_sign_image" id="dealer_sign_image" data-old-image-dom="old_file_path4" data-old-image-path="<?php echo $dealer_sign_image ?>" value="" >
				<?php
				if($dealer_sign_image!="" && file_exists(DEALER_PHOTO_SIGN_A.$dealer_sign_image)){
				?>
				<img src="<?php echo DEALER_PHOTO_SIGN_A.$dealer_sign_image; ?>" width="100" />
				<?php
				}else{
				echo "";
				}

				?>

				</td>

				<td colspan="7" style="border-right: none;border-left: none;  text-align: center;">
				<div class="card" style="width:250px">   
				<div class="card-body border img-thumbnail" style="border-color:black;">

				<p class="card-text">3.5*4.5CM <br/>Passport Photo</p>
				<input  width="132" height="170" data-image="<?php echo ($distributor_image!="" && file_exists(DISTRIBUTOR_PHOTO_A.$distributor_image))?DISTRIBUTOR_PHOTO_A.$distributor_image:"";?>" type="file" accept="image/*" name="distributor_image" id="distributor_image" data-old-image-dom="old_file_path2" data-old-image-path="<?php echo $distributor_image ?>" value="" >
				</div>

				</div>
				<h5 style="text-align:center;"><b>Distributor</b></h5>
				<lable>Name</lable>:	<input type="text" name="distributor_name" id="distributor_name" value="<?php echo $distributor_name?>" style="width: 108px;"><br/><br/>

				<lable>Mo.</lable>:<input type="text" name="distributor_mob" id="distributor_mob" value="<?php echo $distributor_mob?>" style="width: 108px;"><br/><br/>

				<lable>Sign</lable>:	<input type="text" name="distributor_sign" id="distributor_sign" value="<?php echo $distributor_sign?>" style="width: 108px;">
				<input   width="132" height="170"data-image="<?php echo ($distributor_sign_image!="" && file_exists(DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image))?DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image:"";?>" type="file" accept="image/*" name="distributor_sign_image" id="distributor_sign_image" data-old-image-dom="old_file_path5" data-old-image-path="<?php echo $distributor_sign_image ?>" value="" >
				<?php
				if($distributor_sign_image!="" && file_exists(DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image)){
				?>
				<img src="<?php echo DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image; ?>" width="100" />
				<?php
				}else{
				echo " ";
				}
				?>

				</td>
				<td colspan="7" style="border-right: none;border-left: none; text-align:right;">

				<div class="card " style="width:250px">   
				<div class="card-body border img-thumbnail" style="border-color:black;">

				<p class="card-text">3.5*4.5CM <br/>Passport Photo</p>
				<input  width="132" height="170" data-image="<?php echo ($company_office_image!="" && file_exists(COMPANY_OFFICE_PHOTO_A.$company_office_image))?COMPANY_OFFICE_PHOTO_A.$company_office_image:"";?>" type="file" accept="image/*" name="company_office_image" id="company_office_image" data-old-image-dom="old_file_path3" data-old-image-path="<?php echo $company_office_image ?>" value="" >
				</div>
				</div>
				<h5 style="text-align:center;"><b>Company Officer</b></h5>
				<lable>Name</lable>:	<input type="text" name="company_office_name" id="company_office_name" value="<?php echo $company_office_name?>" style="width: 108px;"><br/><br/>

				<lable>Mo.</lable>:<input type="text" name="company_office_mob" id="company_office_mob" value="<?php echo $company_office_mob?>" style="width: 108px;"><br/><br/>

				<lable>Sign</lable>:	<input type="text" name="company_office_sign" id="company_office_sign" value="<?php echo $company_office_sign?>" style="width: 108px;">
				<input width="132" height="170" data-image="<?php echo ($company_office_sign_image!="" && file_exists(COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image))?COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image:"";?>" type="file" accept="image/*" name="company_office_sign_image" id="company_office_sign_image" data-old-image-dom="old_file_path6" data-old-image-path="<?php echo $company_office_sign_image ?>" value="" >
				<?php
				if($company_office_sign_image!="" && file_exists(COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image)){
				?>
				<img src="<?php echo COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image; ?>" width="100" />
				<?php
				}else{
				echo " ";
				}
				?>

				</td>

				</tr>
				<tr>
				<td colspan="16">
				<div class="card text-center border" style="border-color:black;">

				<div class="card-body">
				<h4 class="card-title "><b> Sheetal Cool Products Limited  </b></h4>
				<div class="row">
			       <div class="col-sm-6">
			      <p align="left" style="margin-left: 15px!important;"><?=COMPANY_BANK_DETAILS?>
			      </p>
				</div>
				</div>
				</div>
	                        </td>	
			    </tr>
			  <tr>				
			<td colspan="16" class="no-border-bottom"><b>For Office Use:</b><br/><br/></td>
			</tr>
			<tr height="40px;">
			<td colspan="2" class="color no-border-right" align="center"><strong>Deep Freeze HOD</strong></td>
			<td colspan="2" class="color" align="center" style="border-right:none;"><strong>Account HOD</strong></td>
	               <td colspan="4" class="color" align="center" style="border-right:none; border-left:none;"><strong>Voucher HOD</strong></td>
	              <td colspan="4" class="color" align="center"style="border-left:none; border-right:none;"><strong>Dispatch HOD</strong></td>
	              <td colspan="4" class="color" align="center" style="border-left:none"><strong>Authority</strong></td>
		</tr>
</tbody>
</table>
</div>
</div>
</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn green">Submit</button>
		<button type="button" class="btn btn-default" onClick="window.location.href='deep_freezer_scheme_manage.php'">Back</button>
	</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>


<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
	/*$("#complain_assign_to").fSelect();*/
	$('#complain_date').datepicker({ datepicker: true, autoclose: true, format: 'yyyy-mm-dd'});
</script>

<script type="text/javascript">
	var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=agency_permises_photo]"),
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })

var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=company_office_image]"),
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })

	var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=dealer_image]"),
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })

	var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=distributor_image]"),
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })


	var isImageThumbnailLoaded=false;
	var isImageThumbnailValid=false;

	$(function()
	{
		aj.imageHolder($("input[id=image_path]"),
			function(isImageThumbnailLoadedReply,isImageThumbnailValidReply)
			{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Old Image Found!!");
         	},
         	function(file,img)
         	{
         		if(!file)
         		{
         			toastr.error("File may be corrupted or missing. Try again!!");
         		}
         	},
         	function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height)
         	{
         		isImageThumbnailLoaded=isImageThumbnailLoadedReply;
         		isImageThumbnailValid=isImageThumbnailValidReply;
         		toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
         	},
         	function(data)
         	{
         		isImageThumbnailLoadedReply
         	}
         	);
        })

	
	


</script>

<script type="text/javascript">
	$(document).ready(function() {
              
			var mode = "<?= isset($_REQUEST['mode']) ? $_REQUEST['mode'] : ""; ?>";

			if (mode == "edit") {
			
				var langauge = $("#language").find(':selected').data('language');
				
				$('.language1').html(langauge);
				

			}
			
		});

	function Getcustomer(customer_type)
	{

		if(customer_type==3){
			$("#distributor_agency").removeAttr("disabled");
		}
		else{
			if(!$("#distributor_agency").is(':disabled')){
				$("#distributor_agency").attr("disabled",true);
			}
			
		}
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer.php",
        	data:'customer_type='+customer_type,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#customer_id").select2("destroy");
	            $("#customer_id").html(data);
	            $("#customer_id").select2();
	            $("#customer_id").trigger("change");

       		}
   	 	});
	}

	function GetcustomerInfo(customer_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer_info.php",
        	data:'customer_id='+customer_id,
        	beforeSend:function(){
            },
        	success: function(data){
        		var data =$.parseJSON(data);
        		$("#address").html(data.address);
        		$("#contact_person").val(data.cname);
        		$("#state").val(data.state);
        		$("#mobile_no").val(data.phone);
        		$("#shop_name").val(data.cname);
        		// GetDistributor()
        	
        	}
   	 	});
	}

	
	function GetDistributor(state_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_distributor_info.php",
        	data:'state_id='+state_id,
        	beforeSend:function(){
            },
        	success: function(data){
        		var data =$.parseJSON(data);
        		// $("#state").val(data.state);
        	}
   	 	});
	}


    $("#language").change(function(){
        var langauge = $(this).find(':selected').data('language');
        $('.language1').html(langauge);
        
    });

    


	


	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
	function check_form()
	{
		$(".form-body").children().removeClass("has-error");
		var isValid=true;	
		
		

		if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")=="")
		{
			vd=aj.error('customer_id',"Please Select Name Of Company.","add_error");
			isValid=false;
		}

		if($("#address").val()=="" || $("#address").val().split(" ").join("")=="")
		{
			vd=aj.error('address',"Please Enter Address.","add_error");
			isValid=false;
		}

		if($("#contact_person").val()=="" || $("#contact_person").val().split(" ").join("")=="")
		{
			vd=aj.error('contact_person',"Please Enter Contact Person.","add_error");
			isValid=false;
		}

		if($("#state").val()=="" || $("#state").val().split(" ").join("")=="")
		{
			vd=aj.error('state',"Please Enter State.","add_error");
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

// 	function printReport(id) 
// {	
// 	// var myWindow =  window.open('view_order_new_1.php?order_id='+id+"&p=1",'','width=500,height=800');

// 	var myWindow =  window.open('deep_freezer_scheme_print.php?id='+id,'','width=500,height=800');

// 	setTimeout(function () 
// 	{
// 		myWindow.print();
	
// 	});
// }
</script>
</body>
</html>