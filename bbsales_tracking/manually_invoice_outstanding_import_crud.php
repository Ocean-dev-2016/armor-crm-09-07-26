<?php
$page_id=666;$page_slug='manually_invoice_outstanding_import';
$ctable 	= "manually_invoice_outstanding_import";
$ctable1 	= "Manually A/c. Receivable import";

$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"import_invoice_manage.php","title"=>$page_title),array("link"=>"import_invoice_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
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
				   	$UploadName1 = "invoice-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/invoice/".$UploadName1;
				    move_uploaded_file($TempFile,$UploadURL1);
					include "PHPExcel/IOFactory.php";
					try{ 
						$objPHPExcel = PHPExcel_IOFactory::load($UploadURL1);
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

						$highestCol = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn(); 

						$maxColCount = PHPExcel_Cell::columnIndexFromString($highestCol);
						// print_r($highestCol);
						// echo $maxColCount;
						ob_end_clean();
						$arrayCount 	= count($allDataInSheet);  // Here get total count of row in that Excel sheet
						$Member=0;
						$Numbers=array();
						$SkippedArray=array();
						
						if($arrayCount>1)
						{  
							if($allDataInSheet[7]["A"]!="Party Name" || $allDataInSheet[7]["B"]!="Party Alias" || $allDataInSheet[7]["C"]!="Bill No" || $allDataInSheet[7]["D"]!="Bill Date" || $allDataInSheet[7]["E"]!="Due Days" || $allDataInSheet[7]["F"]!="Bill Amount" || $allDataInSheet[7]["G"]!="Balance Amt. (Cumulative)" || $allDataInSheet[7]["H"]!="Sales Person" || $allDataInSheet[7]["I"]!="Mobile-1" || $allDataInSheet[7]["J"]!="Mobile-2" || $allDataInSheet[7]["K"]!="E-Mail" || $allDataInSheet[7]["L"]!="Detail" || $allDataInSheet[7]["M"]!="PDC Chq No." || $allDataInSheet[7]["N"]!="PDC Date" || $allDataInSheet[7]["O"]!="PDC Exp. Date" || $allDataInSheet[7]["P"]!="PDC Amount" || $allDataInSheet[7]["Q"]!="Securtiy Chq No" || $allDataInSheet[7]["R"]!="Security Chq Am")
							{  
								$Fail=true;
								$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								throw new Exception();
							} 
						} 
						for($i=8;$i<=$arrayCount-1;$i++)
						{  
							$allDataInSheet[$i]["C"]=str_replace(" ","",$allDataInSheet[$i]["C"]);
							if($allDataInSheet[$i]["A"]!="")
							{     
								$customer_id = $db->rp_getValue("executive","id","isDelete=0 AND client_code='".$db->clean($allDataInSheet[$i]["B"])."'");
								$client_code=($allDataInSheet[$i]["B"])?$db->clean($allDataInSheet[$i]["B"]):"";
								$party_name=($allDataInSheet[$i]["A"])?$db->clean($allDataInSheet[$i]["A"]):"";
							}
							else
							{
								$customer_id=$customer_id;
								$client_code=$client_code;
								$party_name=$party_name;
							}
							if($customer_id!="")
							{  
								$sales_name=($allDataInSheet[$i]["H"])?$db->clean($allDataInSheet[$i]["H"]):"";
								if($sales_name)
								{ 
									$sales_id=$db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($sales_name)."'");
								}
								$bill_no=$db->clean($allDataInSheet[$i]["C"]);
								$info_rows=array(
									"customer_id"=>$customer_id, 
									"client_code"=>$client_code,  
									"party_name"=>$party_name,  
									"bill_no"=>($bill_no)?$db->clean($bill_no):"",  
									"bill_date"=>($allDataInSheet[$i]["D"])?$db->clean(date('Y-m-d',strtotime($allDataInSheet[$i]["D"]))):"",  
									"due_days"=>($allDataInSheet[$i]["E"])?$db->clean($allDataInSheet[$i]["E"]):"",  
									"bill_amount"=>($allDataInSheet[$i]["F"])?$db->clean($allDataInSheet[$i]["F"]):"",   
									"balance_amt"=>($allDataInSheet[$i]["G"])?$db->clean($allDataInSheet[$i]["G"]):"", 

									"sales_id"=>($sales_id)?$sales_id:"",      
									"mobile_no1	"=>($allDataInSheet[$i]["I"])?$db->clean($allDataInSheet[$i]["I"]):"",      
									"mobile_no2"=>($allDataInSheet[$i]["J"])?$db->clean($allDataInSheet[$i]["J"]):"",      
									"email"=>($allDataInSheet[$i]["K"])?$db->clean($allDataInSheet[$i]["K"]):"",      
									"detail"=>($allDataInSheet[$i]["L"])?$db->clean($allDataInSheet[$i]["L"]):"",   
									"pdc_check_no"=>($allDataInSheet[$i]["M"])?$db->clean($allDataInSheet[$i]["M"]):"",      
									"pdc_date"=>($allDataInSheet[$i]["N"])?$db->clean(date('Y-m-d',strtotime($allDataInSheet[$i]["N"]))):"",  
									"pdc_exp_date"=>($allDataInSheet[$i]["O"])?$db->clean(date('Y-m-d',strtotime($allDataInSheet[$i]["O"]))):"",       
									"pdc_amount"=>($allDataInSheet[$i]["P"])?$db->clean($allDataInSheet[$i]["P"]):"",      
									"security_chq_no"=>($allDataInSheet[$i]["Q"])?$db->clean($allDataInSheet[$i]["Q"]):"",      
									"security_chq_amt"=>($allDataInSheet[$i]["R"])?$db->clean($allDataInSheet[$i]["R"]):"",      
								);	 

								$isDupcheck = $db->rp_getTotalRecord($ctable,"isDelete=0 AND bill_no='".$bill_no."'",0);	
								if($isDupcheck==0)
								{ 
									if($customer_id!="" && $bill_no != "")
									{  
										$itemcnt=0; 
										$inserted_id=$db->rp_insert($ctable,array_values($info_rows),array_keys($info_rows),0); 
										if($inserted_id)
										{
											$Member++; 
										}
										else
										{
											$SkippedArray[$i] = "Data Not Inserted For Bill No. -  ".$db->clean($bill_no);
										}
									} 
								}
								else
								{
									if($customer_id!="" && $bill_no != "")
									{  
										$itemcnt=0; 
										$inserted_id=$db->rp_update($ctable,$info_rows,"bill_no='".$bill_no."'",0); 
										if($inserted_id)
										{
											$Member++; 
										}
										else
										{
											$SkippedArray[$i] = "Data Not Inserted For Bill No. -  ".$db->clean($bill_no);
										}
									}
									// $SkippedArray[$i] = "Already Added Invoice For Bill No. -  ".$db->clean($allDataInSheet[$i]["C"]);
								}
							}
							else
							{
								$SkippedArray[$i] = "Customer Not Found For Bill No.  -  ".$db->clean($bill_no);
							} 
						}
						// exit;
						//print_r( $SkippedArray);exit;
						/*$Skipped=($arrayCount-8)-$Member;*/
						$Skipped=($arrayCount-8)-$Member;
						$SkipMessage="";
						if(sizeof($SkippedArray)>0)
						{
							$SkipMessage="Total <b>".$Skipped."</b> Row(s) Invalid And Total Update <b>".$Member."</b> Row(s)";
							if(sizeof($SkippedArray)>0)
							{
								$arrayCount = strlen((string)$arrayCount);

								$SkipMessage .="<br/>";
								$SkipMessage .="<br/>";
								$SkipMessage.= " ***Data Not Proper*** ";
								$SkipMessage .="<br/>";
								foreach ($SkippedArray as $key => $value) {
									$key = sprintf("%0".$arrayCount."d", $key);
									$SkipMessage .= /*$key." - ".*/$value;
									$SkipMessage .="<br/>";
								}
							}
							// skipped rows
							// total update count
						}
						if(sizeof($SkippedArray)>0)
						{
                        	$db->addErrorMessage($SkipMessage);
						}
						else
						{
                        	$db->addSuccessMessage("Invoice List Import Successfully");
						}
					}
					catch(Exception $e){
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
	}	
	else
	{
		$db->addErrorMessage("excel file required.");
	}

	$db->rp_location("manually_invoice_outstanding_import_crud.php");
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") 
{
	if ($rights['delete_flag'] != 1) 
	{
	$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	// $_REQUEST['id'];

	/*log entry*/
	$customer_id = $db->rp_getValue("import_invoice_info","customer_id","id='".$_REQUEST['id']."'");
	$voucher_no = $db->rp_getValue("import_invoice_info","voucher_no","id='".$_REQUEST['id']."'");
	$module_name = "Import Invoice";
	$flag = "Web";
	$log_description = $module_name." ".$voucher_no." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
	/*log entry*/
	$rows = array("isDelete"=>1);
	$deleted_id=$db->rp_update("import_invoice_info",$rows,"id='".$_REQUEST['id']."'",0,$log_description,$flag,$module_name,"",$customer_id);
	if($deleted_id)
	{ 
		$db->rp_update("import_invoice_item",array("isDelete"=>1),"invoice_id='".$_REQUEST['id']."'");
	 
		/*delete Customer Leager */
		$db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$_REQUEST['id']."' AND reference_table='import_invoice_info'",0);
		/*delete Customer Leager */
 
		$db->addSuccessMessage("invoice Delete Successfully.."); 
	} 
	else 
	{
		$db->addErrorMessage("Falied to delete invoice!!"); 
	}
	$db->rp_location("import_invoice_manage.php");
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
				<h1><a href="<?php echo "import_invoice_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
								<div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
											<label>Excel File<code>*</code></label>
											<input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB"  data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file"  name="excel_upload" id="excel_upload" data-validation="required"> 

											<br>
											<a download href="../customer_invoice_outstanding.xls" type="button" class="btn btn-success btn-sm" style="background-color: green;"><i class="fa fa-download"></i> Download Sample Excel </a>
									    </div>                           
                                    </div>
                                </div>
                                
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green submit_form">Submit</button>
								<a type="button" class="btn btn-default" href="import_invoice_manage.php">Back</a>
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
		// alert();
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
	    //$("#loading-modal").modal("show");
	})
</script>
  
</body>
</html>