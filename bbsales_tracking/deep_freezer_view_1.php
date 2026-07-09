<?php
$page_id=651;$page_slug='deep_freezer_scheme';
require_once("connect_in.php");
include("../include/no_to_word.php");
   
$ntw = new NumToWord_RP;
$id	= $_REQUEST['id'];
$header_image= '../images/sheetal_ice_creame.jpg';
?>
<html>
	<head>
		<style>
		.mainDiv, table{
			border: 1px solid #595959;
			border-collapse: collapse;
			font-size: 13px;
			width:250mm!important;
			background-color: #FFF;
			margin:auto;
			padding:auto;
		}
		table , td, th {
			border: 1px solid #595959;
		}
		td, th {
			padding: 5px;
			height: 25px;
		}
		.text-center{
			text-align: center!important;
		}
		.text-right{
			text-align: right!important;
		}
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
		.border td{
			border-bottom: hidden !important;
		} 
		.color {
			background: #D3D3D3;
		} 
		.font-size td
		{
			font-size: 15px!important;
		}
		.image-width
		{
			width: 10% !important;
			min-width: 10% !important;
			max-width: 10% !important;
		}
		 
		.border-r-width
		{
			border-right-width: 5px;
		}
		.border-gray
		{
			border-right-color:#E5E5E5;
		}
		.border-blue
		{
		border-right-color:<?= VIEW_COLOR ?>;
		}
		.vertical-top
		{
		vertical-align: top;
		}
		.height-5
		{
		height: 5px;
		}
		.bg-gray
		{
		background-color: #E5E5E5 !important;
		}
		.font-13
		{
		font-size:13px !important;
		}
		.headerBorder
		{
		    border: 22px solid #eb268f;
		    border-bottom: none;
		    border-right: none;
		    border-left: none;
		}
		</style>
	</head>
	<body>
		<table>
			<tbody class="<?= $cl; ?>"> 
				<tr class="headerBorder">
					<td colspan="6" class="no-border-right" style="background: #f1f1f1;padding: 0px !important;">
						<img style="width: 100%;padding: 0px !important;"  src="<?= $header_image; ?>">
					</td>
					<td colspan="6" class="" style="font-size: 27px;background: #f1f1f1;">
						
					<!-- <h2><strong><?= $customer_address_d['company_name']; ?></strong></h2> -->
					<h2><strong><?= CLIENT_BRAND_NAME ?></strong></h2>
								<p class="font-13">
									<i class="fa fa-location-arrow"></i> <?= FACTORY_ADDRESS ?>
								</p>	
								
					

					<!-- <strong style="color: #eb268f;"><?php echo CLIENT_BRAND_NAME ?></strong> -->
							<!-- <br>
						<p class="font-13">
							<i class="fa fa-location-arrow"></i> <?= FACTORY_ADDRESS ?>
							<br> -->
							<i class="fa fa-envelope"></i> info@sheetalicecream.com <i class="fa fa-globe"></i> www.scplco.com
						<br>
						<strong>CIN: L15205GJ2013PLC077205</strong>
							<!-- <i class="fa fa-location-arrow"></i><strong> Office Address : </strong><?= CLIENT_ADDRESS ?>
							<br> -->
							<!-- <i class="fa fa-phone"></i> <?= OFFICE_PHONE ?><br> <?= OFFICE_EMAIL ?>  -->
						</p>
					</td>
				</tr>
				<tr>
													  <td colspan="16" class="color" align="center" style="background-color: #E5E5E5;height: 40px;"><strong>DEEP FREEZER SCHEME</strong></td>
												</tr>
											  <tr>
												       <td colspan="8" style="border: none;"></td>  
                               <td colspan="8" style="text-align: right; border-left: none; border-bottom: none;"><b>Serial No. :</b> <u><?php echo $cart_detail_d['serial_no']?></u>
                               </td>
                        </tr>
											  <tr>
		                         <td colspan="8" style="border: none;"></td>  
		                         <td colspan="8" style="text-align: right;border-left: none;border-top:    none;" ><b>Date :</b> <?= date("d-m-Y h:i a");?></td>
		                    </tr>
                       	<tr>
	                           <td colspan="8" style="border-right: none;">
	                           		<b>Type Of Customer</b>:	<?php echo $db->rp_getValue("customer_type","name","id='".$cart_detail_d['executive_type']."'")?>
	                           </td>
						                                 
									           <td colspan="8" style="border-left: none;">
									           	<b>Company</b>:<?php echo $db->rp_getValue("executive","company_name","id='".$cart_detail_d['customer_id']."'")?>
                              </td>
				                </tr>								
												<tr>
												     <td colspan="8" style="border-right: none;"><b>Customer</b>:
																	<td colspan="8" style="border-left: none;"><?php echo $cart_detail_d['shop_name'];?>
																	</td>
												      </td>
												</tr>

												<tr>
															<td colspan="4" style="border-right: none;">
																		<b>Contact person</b>:
																		<td colspan="4" style="border-left: none; border-right: none;"><?php echo $cart_detail_d['contact_person']?>
																		</td>

															    <td colspan="4" style="border-right: none; border-left: none;">
																	    <b>Mo.</b>:
																			<td  colspan="4" style="border-left: none;"><?php echo $cart_detail_d['mobile_no']?>
																			</td>        
														    	</td> 
															</td>
												</tr>

								      <tr>
														<td colspan="8" style="border-right: none;">
																	<b>Full Address</b>
																	<td colspan="8" style="border-left: none;"><?= $cart_detail_d['address']; ?>
														</td>
														</td>
										  </tr>
										<tr>
								 		  <td colspan="4" style="border-right: none;">
									         <b>Taluka</b>:
													<td  style="border-left: none; border-right: none;"><?php echo $cart_detail_d['taluka']?>
												  </td>
											   
											   <td colspan="4" style="border-right: none; border-left: none;">
													  <b>District</b>:
													  <td  style="border-left: none; border-right: none;"><?php echo $cart_detail_d['district']?>
													  </td>
													  </td>

													   <td colspan="4" style="border-right: none; border-left: none;">
											     
																  <b>State</b>:
																  <td  style="border-right: none;border-left: none;"><?php echo $cart_detail_d['state']?>
																   </td>
													    </td>
													     <td colspan="4" style="border-right: none; border-left: none;">
													     </td>
										   </td>
								   </tr>
								    <tr>
				                  <td colspan="4" style="border-right: none; border-left: none;">
																<b class="test">Distributor Agency Name</b>:
																
																<td colspan="4"  style="border-right: none; border-left: none;">
																	<?php echo $db->rp_getValue("executive","company_name","id='".$cart_detail_d['distributor_agency']."'")?></td> 
												
                                             <td colspan="4" style="border-right: none; border-left: none;">
											    
													  <b>Center</b>:
													  <td colspan="4" style="border-left: none;"><?php echo $cart_detail_d['center'];?>
													 </td>
													  </td>			
											</td>
							        </tr>
							         <tr>
				                           
									  <td colspan="2" style="border-right: none;">
									           <b>Freeze Model No.</b>
													   <td  style="border-left: none; border-right: none;"><?php echo $cart_detail_d['freeze_model_no'];?>
													   </td>			
													   <?php 
													   // echo $cart_detail_d['hard_top']; exit();
													   ?>		
													    <td colspan="2" style="border-right: none; border-left: none;">
													       <b>Hard Top</b>:						     
														    <td  style="border-right: none; border-left: none;">
														    	<input disabled class="form-check-input larger" type="checkbox" name="hard_top" id="hard_top" <?php if($cart_detail_d['hard_top'] == 1){ echo "checked";} ?>>
													        </td>
													    </td>
															 <td colspan="2" style="border-right: none; border-left: none;">
													       <b>Glass Top</b>:
													       <td  style="border-right: none; border-left: none;">
														    	 <input disabled class="form-check-input larger" type="checkbox" name="class_top" id="class_top" <?php if($cart_detail_d['class_top'] == 1){ echo "checked";} ?>>
													        </td>
													       
													       	</td>
													       	 <td colspan="2" style="border-right: none; border-left: none;">	
											   
											                	<b>Payment</b>:<?php if($cart_detail_d['payment'] == 1)
																					{
																						echo "cheque";
																					}
																					else if($cart_detail_d['payment'] == 2)
																					{
																						echo "RTGS";
																					}
																					else
																					{
																						echo "";
																					}

																			 ?>
					
													 </td>
													   <td colspan="4" style="border-right: none; border-left: none;">	
													         <b>Utr</b>:<?=$cart_detail_d['utr']?>
																
																 </td>
													  <td colspan="4" style="border-right: none; border-left: none;">	
														   
														<b>Language</b><?php if($cart_detail_d['language'] == 1)
																					{
																						echo "Gujarati";
																					}
																					else if($cart_detail_d['language'] == 2)
																					{
																						echo "Hindi";
																					}
																					else if($cart_detail_d['language'] == 3)
																					{
																						echo "English";
																					}

																			 ?>
							
														
													 </td>
											</td>
										   </tr>
												
											   
										        
											
									    <tr>
													<td colspan="16">
														<span style="text-align:center;" class="font-size"><?php if($cart_detail_d['language'] == 1)
																					{
																						echo TERMS_CONDITION_GUJRATI;
																					}
																					else if($cart_detail_d['language'] == 2)
																					{
																						echo TERMS_CONDITION_HINDI;
																					}
																					else if($cart_detail_d['language'] == 3)
																					{
																						 echo TERMS_CONDITION_ENGLISH;
																					} ?></span>
													</td>
								    	</tr>
							</tbody>
				        	</table>
                            </div>
                         <div class="page">
						<table>
									<tbody>
										<tr>
												<td colspan="16">
	                                            <?php

												             if($cart_detail_d['image_path']!="")
							                    {
							                        $img = explode(",", $cart_detail_d['image_path']);
							                        $imgpath = array();
							                        for ($i=0; $i < sizeof($img); $i++)
							                        { 
							                            $imgpath[] = "../images/document_list/".$db->rp_getValue("media","url","reference_id='".$cart_detail_d["id"]."' AND id='".$img[$i]."'",0);
							                        }

							                        for ($i=0; $i < sizeof($imgpath); $i++)
							                        {
							                        	?>
							                             <a href="" data-lightbox=" scheme<?=$count?>" data-title="scheme<?=$cart_detail_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>&nbsp;&nbsp;&nbsp;&nbsp;
							                             <?php
							                        }
							                    }
							       
	                                           ?>
									       </td>
									    	
										</tr>
                     <tr>
                       <td colspan="16">
														<div class="card text-center border" style="border-color:black;">	       
														<div class="card-body">

																<h5 class="card-title">Agency Permises Photo</h5>
																<label>Attachment</label>
																<br>	
															
																<?php
																if($cart_detail_d['agency_permises_photo']!=""){
																?>
																<img src="<?php echo  AGENCY_PERMISES_PHOTO_A.$cart_detail_d['agency_permises_photo']; ?>" width="151" height="226"/>
																<?php

																}else{
																echo "";
																}

																?>
														</div>
														</div>	
                      </td>	
									    	
									  </tr>
									
									  
								
									  <tr>
									
								
											  	<td colspan="2" style="border-right: none;border-left: none; text-align: left;" >
											  		    <div class="card " style="width:250px">   
											<div class="card-body border img-thumbnail" style="border-color:black;">
											                        	<?php
								                     if($cart_detail_d['dealer_image']!="" && file_exists(DEALER_PHOTO_A.$cart_detail_d['dealer_image'])){
															?>
															<img src="<?php echo DEALER_PHOTO_A.$cart_detail_d['dealer_image']; ?>" width="151"  height="226"/>
															<?php
														 }else{
															echo "";
														 }
														 
														 ?>
											</div>
                                           </div>
			                                         	<h3><b>Dealer</b>:</h3>
			                                            <b>Name</b>:	<?php echo $cart_detail_d['dealer_name']?><br/><br/>
			                                             <b>Mo.</b>:<?php echo $cart_detail_d['dealer_mo']?><br/><br/>
			                                             <b>Sign</b>:	<?php echo $cart_detail_d['dealer_sign']?>
			                                               <?php
													 if($cart_detail_d['dealer_sign_image']!="" && file_exists(DEALER_PHOTO_SIGN_A.$cart_detail_d['dealer_sign_image'])){
														?>
														<img src="<?php echo DEALER_PHOTO_SIGN_A.$cart_detail_d['dealer_sign_image']; ?>" width="70"  height="100">
														<?php
													 }else{
														echo "";
													 }
													 ?>
											  	</td>
											  	
											  		<td colspan="7" style="border-right: none;border-left: none;  text-align: center;">
											  			<div class="card" style="width:250px">   
											        <div class="card-body border img-thumbnail" style="border-color:black;">
															<?php
													 if($cart_detail_d['distributor_image']!="" && file_exists(DISTRIBUTOR_PHOTO_A.$cart_detail_d['distributor_image'])){
														?>
														<img src="<?php echo DISTRIBUTOR_PHOTO_A.$cart_detail_d['distributor_image']; ?>" width="151"  height="226"/>
														<?php
													 }else{
														echo "";
													 }
													
													 ?>
											</div>

                                           </div>
											  			
			                                           	<h3 class="font-size"><b>Distributor</b></h3>
			                                           	<b>Name</b>:	<?php echo $cart_detail_d['distributor_name']?><br/><br/>
			                                            <b>Mo.</b>:<?php echo $cart_detail_d['distributor_mob']?><br/><br/>
			                                             <b>Sign</b>:	<?php echo $cart_detail_d['distributor_sign']?>
			                                              <?php
													     if($cart_detail_d['distributor_sign_image']!="" && file_exists(DISTRIBUTOR_PHOTO_SIGN_A.$cart_detail_d['distributor_sign_image'])){
														?>
														<img src="<?php echo DISTRIBUTOR_PHOTO_SIGN_A.$cart_detail_d['distributor_sign_image']; ?>" width="70"  height="100">
														<?php
													 }else{
														echo "";
													 }
													 ?>

											  		      
											  		    
											  	</td>
									  		 <td colspan="7" style="border-right: none;border-left: none; text-align:right;">
									  	          <div class="card " style="width:250px">   
											<div class="card-body border img-thumbnail" style="border-color:black;">
											
																   <?php
													 if($cart_detail_d['company_office_image']!="" && file_exists(COMPANY_OFFICE_PHOTO_A.$cart_detail_d['company_office_image'])){
														?>
														<img src="<?php echo COMPANY_OFFICE_PHOTO_A.$cart_detail_d['company_office_image']; ?>"  width="151"  height="226" />
														<?php
													 }else{
														echo "";
													 }
													 
													 ?>
											</div>
                                           </div>
									  		  	<h3><b>Company Officer</b></h3>
									  		  	   <b>Name</b>:	<?php echo $cart_detail_d['company_office_name']?><br/><br/>
									  		     	<b>Mo.</b>:<?php echo $cart_detail_d['company_office_mob']?><br/><br/>
									  		  	    <b>Sign</b>:	<?php echo $cart_detail_d['company_office_sign']?>

									  		  	     <?php
													 if($cart_detail_d['company_office_sign_image']!="" && file_exists(COMPANY_OFFICE_PHOTO_SIGN_A.$cart_detail_d['company_office_sign_image'])){
														?>
														<img src="<?php echo COMPANY_OFFICE_PHOTO_SIGN_A.$cart_detail_d['company_office_sign_image']; ?>" width="70"  height="100">
														<?php
													 }else{
														echo "";
													 }
													 ?>

                                            
									  	    </td>

									  </tr>
									  <tr>
	                     <td colspan="16">
										      <div class="card text-center border" style="border-color:black;">
														<div class="card-body">
															<h3 class="card-title "><b> Sheetal Cool Products Limited  </b></h3>
															<div class="row">
																<div class="col-sm-6">
																	<p align="left" style="margin-left: 15px!important;">
																		<?=COMPANY_BANK_DETAILS?> 
																	</p>
																</div>
															</div>
														</div>
												</div>
	                    </td>	
									  </tr>
									  <tr>
													
													<td colspan="16" class="no-border-bottom">
														<b>For Office Use:</b><br/><br/>
													</td>

													
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
						</div>
						<!-- <div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='deep_freezer_scheme_manage.php'">Back</button>
						</div> -->
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
	</body>
</html>
