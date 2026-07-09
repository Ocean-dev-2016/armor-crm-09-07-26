<?php
$page_id        = 572;$page_slug = 'no_order_inquiry';
$ctable 	= "Import Inquiry";
$ctable1 	= "Import Raw Data";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = "Raw Data";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"import_inquiry_manage.php","title"=>$page_title),array("link"=>"import_inquiry_manage.php","title"=>$ctable1));
include("connect.php");
function removeBlankArrays($array) {
    $result = array();

    foreach ($array as $subArray) {
        if (!empty(array_filter($subArray))) {
            $result[] = $subArray;
        }
    }

    return $result;
}
if(isset($_POST['submit']))
{
	if(isset($_FILES['excel_upload']))
	{
	    $Fail=false;
		$file=$_FILES['excel_upload'];
		$TempFile=$file['tmp_name'];
		$FileName=$file['name'];
		$FileType=$file['type'];
		$FileError=$file['error'];
		$FileSize=$file['size'];
		if($FileError==0)
		{
		    if($FileType=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $FileType=='application/vnd.ms-excel')
			{
				if($FileSize<=1024*1024*4)// 2MB
				{ 
					$UploadName1 = "Inquiry-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/customer_inquiry_import/".$UploadName1;
				    move_uploaded_file($TempFile,$UploadURL1);
					include "PHPExcel/IOFactory.php";
					try
					{
						$objPHPExcel = PHPExcel_IOFactory::load($UploadURL1);
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						ob_end_clean();

						$resultArray = removeBlankArrays($allDataInSheet);
						$arrayCount 	= count($resultArray);

						  // Here get total count of row in that Excel sheet
						$Member=0;
						$Numbers=array();
						$SkippedArray=array();
						if($arrayCount>1)
						{
						 //echo "hello234";exit(); 
							if(($allDataInSheet[1]["A"])!="Select Company" || 
								($allDataInSheet[1]["B"])!="Source Medium" || 
								($allDataInSheet[1]["C"])!="Customer Type" || 
								($allDataInSheet[1]["D"])!="Firm Name" ||
								($allDataInSheet[1]["E"])!="Contact Person" || 
								($allDataInSheet[1]["F"])!="GST NO" || 
								($allDataInSheet[1]["G"])!="Contact  Number" ||
								//($allDataInSheet[1]["F"])!="Whatsapp Number" || 
								($allDataInSheet[1]["H"])!="Email Address" || 
								// ($allDataInSheet[1]["I"])!="Designation" || 
								($allDataInSheet[1]["I"])!="Remark" || 
								($allDataInSheet[1]["J"])!="Country" ||
								($allDataInSheet[1]["K"])!="State" || 
								($allDataInSheet[1]["L"])!="City" || 
								($allDataInSheet[1]["M"])!="Route" || 
								($allDataInSheet[1]["N"])!="Pincode" ||
								($allDataInSheet[1]["O"])!="Zone" || 

							//	($allDataInSheet[1]["O"])!="Pincode" ||
								($allDataInSheet[1]["P"])!="Inquiry Date" ||
								($allDataInSheet[1]["Q"])!="Inquiry Taken By" ||
								($allDataInSheet[1]["R"])!="Inquiry Assigned to" ||
								($allDataInSheet[1]["S"])!="Date Of Call" ||
								($allDataInSheet[1]["T"])!="Birth Date" || 
								($allDataInSheet[1]["U"])!="Shipping Address" || 
								($allDataInSheet[1]["V"])!="Billing Address" || 
								($allDataInSheet[1]["W"])!="Address" || 
								($allDataInSheet[1]["X"])!="Industry Type" ||
								($allDataInSheet[1]["Y"])!="Mobile No1" ||
								($allDataInSheet[1]["Z"])!="Name1" ||
								($allDataInSheet[1]["AA"])!="Mobile No2" ||
								($allDataInSheet[1]["AB"])!="Name2" ||
								($allDataInSheet[1]["AC"])!="Mobile No3" ||
								($allDataInSheet[1]["AD"])!="Name3" ||
								($allDataInSheet[1]["AE"])!="Mobile No4" ||
								($allDataInSheet[1]["AF"])!="Name4")
							{
					//echo "hello234erf";exit();
								$Fail=false;	
								//$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								//throw new Exception();
							}
						}
						
						// for($i=4;$i<=$arrayCount;$i++)
						for($i=2;$i<=$arrayCount;$i++)
						{ 
							//echo "hello";exit();						
							$detail['type_of_company']= $db->clean($allDataInSheet[$i]["A"]);
							$detail['source_of_inquiry']= $db->clean($allDataInSheet[$i]["B"]);
							$detail['executive_type']  	= $db->clean($allDataInSheet[$i]["C"]);
							$detail['company_name']	   	= $db->clean($allDataInSheet[$i]["D"]);	
							$detail['person_name']     	= $db->clean($allDataInSheet[$i]["E"]);
							$detail['gst_no'] 			= $db->clean($allDataInSheet[$i]["F"]);
							$detail['mobile_number'] 	= substr((string) $db->clean($allDataInSheet[$i]["G"]), 0, 10);
							//$detail['type_of_gst_id']  	= $db->clean($allDataInSheet[$i]["G"]);
							//$detail['other_mobile_no']  = $db->clean($allDataInSheet[$i]["G"]);
							 // $detail['industry_type_id']	= $db->clean($allDataInSheet[$i]["H"]);
							$detail['email_address']	= $db->clean($allDataInSheet[$i]["H"]);
							//$detail['designation'] 		= $db->clean($allDataInSheet[$i]["I"]);
							// $detail['pan_no']     		= $db->clean($allDataInSheet[$i]["L"]);
							// $detail['website_name']    	= $db->clean($allDataInSheet[$i]["M"]);
							$detail['description'] = $db->clean($allDataInSheet[$i]["I"]);
							$detail['country']     = $db->clean($allDataInSheet[$i]["J"]);
							$detail['state']       = $db->clean($allDataInSheet[$i]["K"]);
							$detail['main_city']   = $db->clean($allDataInSheet[$i]["L"]);
							$detail['city']        = $db->clean($allDataInSheet[$i]["M"]);
							$detail['pincode']    = $db->clean($allDataInSheet[$i]["N"]);
							$detail['zone']  			= $db->clean($allDataInSheet[$i]["O"]);
							//$detatl['pincode'] 			= $db->clean($allDataInSheet[$i]["O"]); 
							$detail['inquiry_date']     = date('Y-m-d',strtotime($allDataInSheet[$i]["P"]));
							$detail['inquiry_created_by'] = $db->clean($allDataInSheet[$i]["Q"]);
							$detail['inquiry_assign_to']  = $db->clean($allDataInSheet[$i]["R"]);
							$detail['date_of_call'] 	= date('Y-m-d',strtotime($allDataInSheet[$i]["S"]));
							$detail['birth_date'] 		= date('Y-m-d',strtotime($allDataInSheet[$i]["T"]));  
							$detail['shipping_address']	= $db->clean($allDataInSheet[$i]["U"]);
							$detail['billing_address'] 	= $db->clean($allDataInSheet[$i]["V"]);
							$detail['address']  		= $db->clean($allDataInSheet[$i]["W"]);
							 $detail['industry_type_id']	= $db->clean($allDataInSheet[$i]["X"]);
							 // echo $department;exit();
							
							$detail['source_of_inquiry'] = $db->rp_getValue("source_of_inquiry","id","LOWER(name)='".strtolower($detail['source_of_inquiry'])."' AND isDelete=0",0);
							$detail['executive_type'] = $db->rp_getValue("customer_type","id","LOWER(name)='".strtolower($detail['executive_type'])."' AND isDelete=0");

							$mobile_no1=$db->clean($allDataInSheet[$i]["Y"]);
							$mobile_no2=$db->clean($allDataInSheet[$i]["AA"]);
							$mobile_no3=$db->clean($allDataInSheet[$i]["AC"]);
							$mobile_no4=$db->clean($allDataInSheet[$i]["AE"]);

							$name1=$db->clean($allDataInSheet[$i]["Z"]);
							$name2=$db->clean($allDataInSheet[$i]["AB"]);
							$name3=$db->clean($allDataInSheet[$i]["AD"]);
							$name4=$db->clean($allDataInSheet[$i]["AF"]);


							$phone_arr=array();
							$name_arr=array();

							if($mobile_no1!="")
							{
								$phone_arr[]=$mobile_no1;
							}
							if($mobile_no2!="")
							{
								$phone_arr[]=$mobile_no2;
							}
							if($mobile_no3!="")
							{
								$phone_arr[]=$mobile_no3;
							}
							if($mobile_no4!="")
							{
								$phone_arr[]=$mobile_no4;
							}
							$phone_arr=implode(",",$phone_arr);

							if($name1!="")
							{
								$name_arr[]=$name1;
							}
							if($name2!="")
							{
								$name_arr[]=$name2;
							}
							if($name3!="")
							{
								$name_arr[]=$name3;
							}
							if($name4!="")
							{
								$name_arr[]=$name4;
							}
							$name_arr=implode(",",$name_arr);

							// $detail['phone']= $db->clean($allDataInSheet[$i]["Y"]).",".$db->clean($allDataInSheet[$i]["AA"]).",".$db->clean($allDataInSheet[$i]["AC"]).",".$db->clean($allDataInSheet[$i]["AE"]);
							// $detail['type_of_gst_id'] = 		$db->rp_getValue("type_of_gst","id","LOWER(name)='".strtolower($detail['type_of_gst_id'])."'");
							//	$detail['industry_type_id'] = $db->rp_getValue("industry_type","id","LOWER(name)='".strtolower($detail['industry_type_id'])."'",0);


							$inquiry_created_by= $db->rp_getValue("sales_executive","id","lower(username)='".strtolower($detail['inquiry_created_by'])."' AND isDelete=0",0);
							if($inquiry_created_by=="")
							{
								$detail['inquiry_created_by']="";
							} else {
								$detail['inquiry_created_by']=$inquiry_created_by;
							}
							$inquiry_assign_to = $db->rp_getValue("sales_executive","id","lower(username)='".strtolower($detail['inquiry_assign_to'])."' AND isDelete=0",0);
							if($inquiry_assign_to=="")
							{
								$detail['inquiry_assign_to']="";
							} else {
								$detail['inquiry_assign_to']=$inquiry_assign_to;
							}

							$detail['sales_executive_id'] = $detail['inquiry_created_by'];
							
							$detail['city_id'] = $db->rp_getValue("city","id","LOWER(name)='".strtolower($detail['main_city'])."' AND isDelete=0");
							$detail['area_id'] = $db->rp_getValue("area","id","LOWER(name)='".strtolower($detail['city'])."' AND isDelete=0");
							$detail['class_id'] = $db->rp_getValue("class","id","LOWER(name)='".strtolower($detail['state'])."' AND isDelete=0");
							$detail['city_id']=($detail['city_id'])?$detail['city_id']:"";
							$detail['area_id']=($detail['area_id'])?$detail['area_id']:"";
							$detail['class_id']=($detail['class_id'])?$detail['class_id']:"";

							$detail['type_of_company'] = $db->rp_getValue("company_master","id","LOWER(name)='".strtolower($detail['type_of_company'])."' AND isDelete=0",0);
							$detail['inquiry_type'] = "-1";
							$detail['inq_status'] 	= "-1";
							$detail['inquiry_lead_flag'] 	= "-1";

							if($detail['industry_type_id'] !="")
							{
								$rows 	= array("name","isDelete");
			                    $values = array($detail['industry_type_id'],"0");

								$insert_industry_type=$db->rp_insert("industry_type",$values,$rows,0);	
                                $detail['industry_type_id'] = $db->rp_getValue("industry_type","id","LOWER(name)='".strtolower($detail['industry_type_id'])."' AND isDelete=0",0);
							}
								
							$IsDuplicateGroupName = $db->rp_getTotalRecord("no_order_inquiry","mobile_number='".$detail['mobile_number']."' AND isDelete=0",0);
							 
                           /* ===> Old Mandetory Field Condition*/
                           	// if($IsDuplicateGroupName > 0 || $detail['source_of_inquiry'] == "" || $detail['executive_type'] == "" || $detail['company_name'] == "" || $detail['mobile_number'] == "" || $detail['country'] == "" || $detail['state'] == ""|| $detail['city'] == "") 
                           /* ===> Old Mandetory Field Condition*/
                           // echo '<pre>';

                            /*echo "company_type" . $detail['type_of_company'];
                            echo "<br/>company_name" . $detail['company_name'];
                            echo "<br/>mobile_number". $detail['mobile_number'];
                            echo  "<br/>country".$detail['country'];
                            echo  "<br/>state".$detail['state'];
                            echo  "<br/>inquiry_created_by".$detail['inquiry_created_by'];
                            echo  "<br/>inquiry_assign_to".$detail['inquiry_assign_to'];
                            echo  "<br/>pincode".$detail['pincode'];
                            exit();*/


							// if($IsDuplicateGroupName > 0 || $detail['type_of_company'] == "" || $detail['company_name'] == "" || $detail['mobile_number'] == "" || $detail['country'] == "" || $detail['state'] == "" || $detail['inquiry_created_by'] == "" || $detail['inquiry_assign_to'] == "" || $detail['pincode'] == "")
							if($IsDuplicateGroupName > 0)
							{
								$SkippedArray[$i] = "Duplicate Data Found : ".$db->clean($allDataInSheet[$i]["C"]);
							}
							else if($detail['type_of_company'] == "" || $detail['company_name'] == "" || $detail['mobile_number'] == "" || $detail['country'] == "" || $detail['state'] == "" || $detail['inquiry_created_by'] == "" || $detail['inquiry_assign_to'] == "" || $detail['pincode'] == "")
							{
								 //echo $detail['inquiry_created_by'];exit();
								//echo "12";exit();	   
								$SkippedArray[$i] = "Some Mandetory Filed Are Blank : ".$db->clean($allDataInSheet[$i]["C"]);
							}
							else
							{ 
								// echo "sdfsf";exit();
								$MemberID = $db->rp_insert("no_order_inquiry",array_values($detail),array_keys($detail),0);
								if($MemberID)
								{
									if(!empty($phone_arr))
									{
										$phn_no=explode(",", $phone_arr);
										$name_d=explode(",", $name_arr);

									    //	print_r(sizeof($phn_no));
										//print_r($name_d);
										//exit();
										for ($phn=0; $phn <sizeof($phn_no); $phn++) 
										{ 
											//echo $phn;
											$item_rows = array("customer_id","phone_no","name","ref_table");
											// $item_values = array($uid,$key);
											$item_values = array($MemberID,$phn_no[$phn],$name_d[$phn],"no_order_inquiry");
											$item_id = $db->rp_insert("customer_vs_phone_no",$item_values,$item_rows,0);
										}
									}
									//exit();
									$db->addStatusTimelineEntry($MemberID,0);
							    	$Member++;
							    	//echo "1";
								}
								else
								{
									//echo "12";exit();
									$SkippedArray[$i] = "Not Inserted : ".$db->clean($allDataInSheet[$i]["E"]);
								}
							}
						}
						//print_r(sizeof($SkippedArray));exit();
						$Skipped=($arrayCount-1)-$Member;
						$SkipMessage="";
					//	print_r(sizeof($SkippedArray));exit();
						//echo count($SkippedArray);exit;
						// if($Skipped>0)
						// {
						    // $SkipMessage="Total <b>".$Skipped."</b> Row(s) Not Updated  And Total <b>".$Member."</b> Row(s) Successfully Updated";

							if(sizeof($SkippedArray)>0)
							{
								
								$arrayCount = strlen((string)$arrayCount);

								// $SkipMessage .="<br/>";
								// $SkipMessage .="<br/>";
								$SkipMessage.= " ***Not Added List*** ";
								$SkipMessage.= "<br/> Data duplicate or data not proper Or Some Mandatory Field Is Blank";
								$SkipMessage .="<br/>";
								foreach ($SkippedArray as $key => $value) {
								// exit("sdfdd");
									$key = sprintf("%0".$arrayCount."d", $key);
									$SkipMessage .= "Rows ".$key." - ".$value;
									$SkipMessage .="<br/>";
								}

								$db->addErrorMessage($SkipMessage);
							} 
							else {
	                        	$db->addSuccessMessage("Inquiry Upload Successfully");
							}
							// skipped rows
							// total update count
						// }
						// if($Skipped>1)
						// {
                        // 	$db->addErrorMessage($SkipMessage);
						// }
						// else
						// {
						// 	$db->addSuccessMessage("Inquiry Upload Successfully");
						// }
					}
					catch(Exception $e)
					{
						$Fail=true;
						$db->addErrorMessage("File not supported to upload.");
					}
                }
				else
				{
					$Fail=true;
					$db->addErrorMessage("Filesize must be less than 2 MB.");
				}
			}
			else
			{
				$Fail=true;
				$db->addErrorMessage("File type must be xls or xlsx.");
			}
		}
		else
		{
			$Fail=true;
			$db->addErrorMessage("File corrupted or not uploaded try again.");
		}
		
		if($Fail)
		{
			$db->rp_delete($ctable,"id='".$GroupID."'");				
		}
	}	
	else
	{
		$db->addErrorMessage("excel file required.");
	}

	$db->rp_location("import_inquiry_manage.php?mode=add");
}
?>
<!DOCTYPE html> 
<html lang="en"> 
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
						<h1><a href="<?php echo "no_order_inquiry_grid_new.php?type=0";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
							<div class="col-md-6 ">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body"> 
	                                        <div class="form-group">
												<label>Excel File<code>*</code></label>
												<input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB"  data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file"  name="excel_upload" id="excel_upload" data-validation="required">
												<br>
												<a download href="../inquiry_import_new.xlsx" type="button" class="btn btn-success btn-sm" style="background-color: green;"><i class="fa fa-download"></i> Download Sample Excel </a>
											</div>  
			                            </div>
										<div class="form-actions">
											<button type="submit" name="submit" class="btn green submit_form">Submit</button>
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
			$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
			  
			function check_form(){
				$(".form-body").children().removeClass("has-error");
				var isValid=true;	
				
				if($("#excel_upload").val()=="" || $("#excel_upload").val().split(" ").join("")==""){
						
					vd=aj.error('excel_upload',"Please Select File.","add_error");
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
			$(".submit_form").on('click',function(){
			    $("#loading-modal").modal("show");
			})
		</script>
	</body>
</html>