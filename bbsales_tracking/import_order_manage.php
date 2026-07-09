<?php
$page_id=565;$page_slug='page_order';

$ctable 	= "orders";
$ctable1 	= "Import Order";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"import_order_manage.php","title"=>$page_title),array("link"=>"import_order_manage.php","title"=>$ctable1));
$ORDER_IDS=array();
$UploadURL1="";
$Member=0;
include("connect.php");
// echo "hello";exit;
if(isset($_POST['submit']))
{
	if(isset($_FILES['excel_upload']))
	{ 
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
					$UploadName1 = "order-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/orders/".$UploadName1;
				    move_uploaded_file($TempFile,$UploadURL1);
					include "PHPExcel/IOFactory.php";
					try
					{
						$objPHPExcel = PHPExcel_IOFactory::load($UploadURL1);
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						// print_r($allDataInSheet);exit;
						ob_end_clean();
						$arrayCount 	= count($allDataInSheet);  // Here get total count of row in that Excel sheet
						
						$Numbers=array();
						$SkippedArray=array(); 

						if($arrayCount>1)
						{
							// echo "hellooooo";exit;
							if(($allDataInSheet[2]["A"])!="order no. " ||  
							($allDataInSheet[2]["B"])!="order date" || 
							($allDataInSheet[2]["C"])!="po date" || 
							($allDataInSheet[2]["D"])!="company name" || 
							($allDataInSheet[2]["E"])!="customer name " || 
							($allDataInSheet[2]["F"])!="client code" || 
							($allDataInSheet[2]["G"])!="GST No." || 
							($allDataInSheet[2]["H"])!="shipping addess" || 
							($allDataInSheet[2]["I"])!="billing address" ||  
							($allDataInSheet[2]["J"])!="Booking place" || 
							($allDataInSheet[2]["K"])!="Booking pincode" || 
							($allDataInSheet[2]["L"])!="sales person name" || 
							($allDataInSheet[2]["M"])!="Product name" || 
							($allDataInSheet[2]["N"])!="product code" || 
							($allDataInSheet[2]["O"])!="qty" || 
							($allDataInSheet[2]["P"])!="Amount" || 
							($allDataInSheet[2]["Q"])!="GST (%)" || 
							($allDataInSheet[2]["R"])!="Total Amount") 
							{
								// echo "test";exit; 
								$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								throw new Exception();
							}
						}
						// echo "test54";exit;
						// echo $arrayCount;exit();
						for($i=3;$i<=$arrayCount;$i++)
						{
							$detail['order_no']=($allDataInSheet[$i]["A"])?$db->clean($allDataInSheet[$i]["A"]):""; 
							$detail['order_date']= ($allDataInSheet[$i]["B"])?$db->clean($allDataInSheet[$i]["B"]):"";	
							$detail['order_date']= date('Y-m-d', strtotime($detail['order_date']));
							// echo $detail['order_date'];exit;
							
							$detail['po_date']=($allDataInSheet[$i]["C"])?$db->clean($allDataInSheet[$i]["C"]):"";
							$detail['po_date']= date('Y-m-d', strtotime($detail['po_date']));
							
							$detail['company_name'] 		= ($allDataInSheet[$i]["D"])?$db->clean($allDataInSheet[$i]["D"]):"";
							$detail['customer_name'] = ($allDataInSheet[$i]["E"])?$db->clean($allDataInSheet[$i]["E"]):"";
							
							$client_code= ($allDataInSheet[$i]["F"])?$db->clean($allDataInSheet[$i]["F"]):"";
							$detail['gst']= ($allDataInSheet[$i]["G"])?$db->clean($allDataInSheet[$i]["G"]):"";
							$customer_d= $db->rp_getData("executive","*","isDelete=0 AND client_code='".$client_code."' AND gst='".$detail['gst']."'","",0);
							$customer_r=mysqli_fetch_assoc($customer_d);
							$detail['customer_id']=$customer_r['id'];
							$detail['customer_type']=$customer_r['type_of_executive']; 
							$detail['address']=addslashes($customer_r['address']);
							$detail['city']=$customer_r['city'];
							$detail['main_city']=$customer_r['main_city'];
							$detail['state']=$customer_r['state'];
							$detail['country']=$customer_r['country'];
							
							$detail['shipping_address']     	= ($allDataInSheet[$i]["H"])?$db->clean($allDataInSheet[$i]["H"]):"";
							$detail['billing_address']       = ($allDataInSheet[$i]["I"])?$db->clean($allDataInSheet[$i]["I"]):""; 
							$detail['booking_place'] 	= ($allDataInSheet[$i]["J"])?$db->clean($allDataInSheet[$i]["J"]):"";
							$detail['booking_pincode'] 	= ($allDataInSheet[$i]["K"])?$db->clean($allDataInSheet[$i]["K"]):"";
							$sales_person_name= ($allDataInSheet[$i]["L"])?$db->clean($allDataInSheet[$i]["L"]):"";
							$detail['sales_id']= $db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($sales_person_name)."'",0);
							$detail['entry_flag']=5;

							$isOrder=$db->rp_getTotalRecord("orders","order_no='".$detail['order_no']."'",0);

							$product_name=($allDataInSheet[$i]["M"])?$db->clean($allDataInSheet[$i]["M"]):"";
							$product_code=($allDataInSheet[$i]["N"])?$db->clean($allDataInSheet[$i]["N"]):"";
							$qty= ($allDataInSheet[$i]["O"])?$db->clean($allDataInSheet[$i]["O"]):"";
							$rate = ($allDataInSheet[$i]["P"])?$db->clean($allDataInSheet[$i]["P"]):"";
							$gst= ($allDataInSheet[$i]["Q"])?$db->clean($allDataInSheet[$i]["Q"]):"";
							$tot_amt= ($allDataInSheet[$i]["R"])?$db->clean($allDataInSheet[$i]["R"]):"";

							/*$item=[];
							$item[]=array("product_name"=>$product_name,"product_code"=>$product_code,"qty"=>$qty,"rate"=>$rate,"gst"=>$gst,"tot_amt"=>$tot_amt);*/

							// echo $i;

							if($detail['order_no']=="" || $detail['order_date']=="" || $detail['po_date']=="" || $detail['company_name']=="" || $detail['customer_name']=="" || $client_code=="" || $detail['gst']=="" || $detail['shipping_address']=="" || $detail['billing_address']=="" || $detail['booking_place']=="" || $detail['booking_pincode']=="" || $sales_person_name=="" )
							{ 
								$SkippedArray[$i] = "All column required!!"; 
							}

							else if($customer_r['id']=="")
							{ 
								$SkippedArray[$i] = "Customer not found in our system ".$detail['company_name']." - ".$detail['customer_name']; 
							}
							else if($detail['sales_id']=="")
							{ 
								$SkippedArray[$i] = "Sales Executive not found in our system ".$detail['sales_person_name']; 
							}
							else if($customer_r['id']=="" && $detail['sales_id']=="")
							{ 
								$SkippedArray[$i] = "Customer & Sales Executive not found in our system "; 
							}
							else if($isOrder>0)
							{ 
								$SkippedArray[$i] = "Order No. already exist ".$detail['order_no']; 
							} 
							else
							{ 
								$oid =$db->rp_insert("orders",array_values($detail),array_keys($detail),0);
								// echo $oid;
								if($oid)
								{
									// echo $product_name;
									$Member++;
									$ORDER_IDS[]=$oid;
									$isPro=$db->rp_getTotalRecord("product_weight_price","LOWER(catno)='".strtolower($product_code)."'",0);
									if($isPro)
									{
										$proR=$db->rp_getData("product_weight_price","*","LOWER(catno)='".strtolower($product_code)."'",0);
										$proD=mysqli_fetch_assoc($proR);

										$pR=$db->rp_getData("product","*","id='".$proD['product_id']."'",0);
										$pD=mysqli_fetch_assoc($pR);

										$final_price=$rate*$qty;
										$gst_amount_item=($final_price*$gst)/100;
										$sub_total=$final_price+$gst_amount_item;

										$rows 	= array(
											"order_id","top_cat_id","cat_id","pro_id","weight_id","pro_name","pro_qty","remaining_qty","inner_size","outer_size",
											// "box_qty","cartoon_qty","loose_qty", 

											"unitprice","original_price","totalprice","igst_tax","igst_amount","taxable","subtotal", "hsn_code"
										);
										$values = array(
											$oid,$pD['tcid'],$pD['cid'],$proD['product_id'],$proD['weight_id'],$db->clean($pD['name']),$qty,$qty,$proD['inner_size'],$proD['outer_size'],
											 
											$rate,$rate,$final_price,$gst,$gst_amount_item,$final_price,$sub_total,$pD['hsn_code'], 
										); 
										$item_id = $db->rp_insert("order_product_item",$values,$rows,0);
									}
									else
									{
										$SkippedArray[$i] = "Product not found in our system ".$i['product_code'];
									}
									 

									$orderitemR=$db->rp_getData("order_product_item","*","order_id='".$oid."'");
									$total_qty=0;
									$subtotal=0;
									$igst_amount=0;
									$grand_total=0;
									if($orderitemR)
									{
										while ($orderitemD=mysqli_fetch_assoc($orderitemR)) {
											$total_qty+=$orderitemD['qty'];
											$subtotal+=$orderitemD['totalprice'];
											$igst_amount+=$orderitemD['igst_amount'];
											$grand_total+=$orderitemD['subtotal'];
										}
									}
									// echo $grand_total;exit;
									$db->rp_update("orders",array("total_qty"=>$total_qty,"subtotal"=>$subtotal,"igst_amount"=>$igst_amount,"grand_total"=>$grand_total,"grand_total_rounded"=>round($grand_total)),"id='".$oid."'",0);
								}
							}
						}
						if($Member > 0)
						{
							if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2)
							{
								$sales_id=$_SESSION[SITE_SESS.'REFERANCE_ID'] ;
							}
							else
							{
								$sales_id="";
							}
							  $data = array(
					                'user_id' => $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],
					                'data_count' => $Member,
					                'date_time' => date('Y-m-d H:i s'),
					                'excel_sheet_path' => $UploadURL1,
					                'sales_executive_id' => $sales_id,
					                'order_id' => implode(",",$ORDER_IDS),
					            );

					           	 // Convert data to JSON string
					            $jsonString = json_encode($data);
					            $jsonString = addslashes($jsonString); // Escaping special characters

								
							$order_values = array($jsonString,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],$Member,date('Y-m-d H:i s'),$UploadURL1,$sales_id,implode(",",$ORDER_IDS)); 
											
							$order_rows = array("response","user_id","data_count","date_time","excel_sheet_path","sales_executive_id","order_id");
						     $db->rp_insert("import_order_history",$order_values,$order_rows,0);
						 }
											
									

							
						$Skipped=($arrayCount-2)-$Member;
						// print_r($SkippedArray);
						// echo $Skipped;exit;
						$SkipMessage="";  
							 
						$SkipMessage="Total <b>".$Skipped."</b> Row(s) Not Inserted And Total Update <b>".$Member."</b> Row(s)";
						if(sizeof($SkippedArray)>0)
						{
							$arrayCount = strlen((string)$arrayCount);

							$SkipMessage .="<br/>";
							$SkipMessage .="<br/>";
							$SkipMessage.= " ***Not Added List*** ";
							$SkipMessage.= "<br/> ";
							foreach ($SkippedArray as $key => $value) {
								$key = sprintf("%0".$arrayCount."d", $key);
								$SkipMessage .= "Row ".$key." - ".$value;
								$SkipMessage .="<br/>";
							}
						} 
						if($SkippedArray)
						{
                        	$db->addErrorMessage($SkipMessage);
						}
						else
						{
                        	$db->addSuccessMessage("Orders Upload Successfully");
						}
					}
					catch(Exception $e)
					{ 
						$db->addErrorMessage("File not supported to upload.");
					}
                }
				else
				{ 
					$db->addErrorMessage("Filesize must be less than 2 MB.");
				}
			}
			else
			{ 
				$db->addErrorMessage("File type must be xls or xlsx.");
			}
		}
		else
		{ 
			$db->addErrorMessage("File corrupted or not uploaded try again.");
		} 
	}	
	else
	{
		$db->addErrorMessage("excel file required.");
	}

	$db->rp_location("import_order_manage.php?mode=add");
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
				<h1><a href="<?php echo "executive_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
											<a download href="../order_import_new.xlsx" type="button" class="btn btn-success btn-sm" style="background-color: green;"><i class="fa fa-download"></i> Download Sample Excel </a>
										</div>                           
                                    </div>
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
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
</script>
</body>
</html>