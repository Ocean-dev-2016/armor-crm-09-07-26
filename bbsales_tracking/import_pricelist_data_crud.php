<?php
$page_id = 581;
$page_slug = 'price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Import Price List";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"price_table_manage.php","title"=>$page_title),array("link"=>"import_pricelist_data_crud.php","title"=>"Add/Edit ".$ctable1));
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
				   	$UploadName1 = "Pricelist-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/pricelist/".$UploadName1;
				    move_uploaded_file($TempFile,$UploadURL1);
					include "PHPExcel/IOFactory.php";
					try{ 
						$objPHPExcel = PHPExcel_IOFactory::load($UploadURL1);
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						ob_end_clean();
						$arrayCount 	= count($allDataInSheet);  // Here get total count of row in that Excel sheet
						$Member=0;
						$Numbers=array();
						$SkippedArray=array();
						
						if($arrayCount>1)
						{
							// if(strtolower($allDataInSheet[3]["A"])!="product" || strtolower($allDataInSheet[3]["B"])!="alias" || strtolower($allDataInSheet[3]["C"])!="closing qty")
							if(strtolower($allDataInSheet[1]["A"])!="product name" || strtolower($allDataInSheet[1]["B"])!="product code" ||  $allDataInSheet[1]["C"]!="Min Sell Price" || $allDataInSheet[1]["D"]!="Product Price" || $allDataInSheet[1]["E"]!="Discount Price")
							{ 
								$Fail=true;
								$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								throw new Exception();
							}
						}
						// echo "566";
						// echo $arrayCount;exit;
						// for($i=4;$i<=$arrayCount;$i++)
						for($i=2;$i<=$arrayCount;$i++)
						{
							//$catno 	    = $db->clean($allDataInSheet[$i]["A"]);
						    $alias 	    = $db->clean($allDataInSheet[$i]["B"]);
							$discount_price	= $db->clean($allDataInSheet[$i]["E"]);
							$min_selling_price	= $db->clean($allDataInSheet[$i]["C"]);
							
							//$catno=$allDataInSheet[$i]['B'];
							$catno = str_replace(' ', '', $alias);
							$discount_price=$allDataInSheet[$i]['E'];
							// $main_product_price=$allDataInSheet[$i]['C'];

							if($discount_price!="" && $discount_price!=0 && $_REQUEST['price_list_id']!="")
							{ 
							    $discount_price = (float)$discount_price;
							    $min_selling_price = (float)$min_selling_price;

								$price_list_id = $_REQUEST['price_list_id'];
								$is_premium=$db->rp_getValue("price_list","is_premium","id='".$price_list_id."'",0);
								$product_id = $db->rp_getValue("product_weight_price","product_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);
								$weight_id = $db->rp_getValue("product_weight_price","weight_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);


								$main_product_price = $db->rp_getValue("product_weight_price","price","weight_id='".$weight_id."' AND product_id='".$product_id."'  AND isDelete=0",0);
 
								$tcid = $db->rp_getValue("product","tcid","id='".$product_id."' AND isDelete=0",0);
								$cid = $db->rp_getValue("product","cid","id='".$product_id."' AND isDelete=0",0);
								 
								$new_unit_price =  $discount_price;
								$new_unit_price = round($new_unit_price,2);
 								// $new_unit_price
 								 
 								$discounted_amount = $main_product_price - $new_unit_price;
								$discounted_amount = round($discounted_amount,2);
 								$discount = ($discounted_amount * 100) / $main_product_price;
								$discount = round($discount,2);
	 							

	 							if (($discount_price < $min_selling_price) && $is_premium==0)  
	 							{
	 								$SkippedArray[$i] = " Cannot Give Discount Less Than Minimum Selling Amount  ".$db->clean($allDataInSheet[$i]["A"])." - ".$db->clean($allDataInSheet[$i]["B"]);
	 							}
	 							// if ($discount_price >= $min_selling_price) 
	 							else
	 							{
	 								// echo $discount_price." ";exit;
	 								// echo $main_product_price;exit;
		 							if($discount_price<$main_product_price)
		 							{ 
	 									if($product_id!=""){
											$rows=array("tcid"=>$tcid,
												"cid"=>$cid,
												"pid"=>$product_id,
												"weight_id"=>$weight_id,
												"price_list_id"=>$price_list_id,
												"price"=>$main_product_price,
												"discounted_price"=>$new_unit_price,
												"discounted_amount"=>$discounted_amount,
												"discount_type"=>2,
												"discount"=>round($discount,2),
											);			
		 
											$pricelist_where = "pid='".$product_id."' AND weight_id='".$weight_id."' AND isDelete=0 AND price_list_id='".$price_list_id."'";

											$isAdd=$db->rp_getTotalRecord("product_price_list",$pricelist_where,0);
											if($isAdd==0)
											{
		 										$updatedId=$db->rp_insert("product_price_list",array_values($rows),array_keys($rows),0);
											}else{

		 										$updatedId=$db->rp_update("product_price_list",$rows,$pricelist_where,0);
											}
											
											 
											if($updatedId)
											{ 
												$Member++;
											}
											else
											{
											    $SkippedArray[$i] = "Data update failed in this product  ".$db->clean($allDataInSheet[$i]["A"])." - ".$db->clean($allDataInSheet[$i]["B"]);
											} 
									   	}
									   	else
										{
											$SkippedArray[$i] = "Product code not found for this item  ".$db->clean($allDataInSheet[$i]["A"])." - ".$db->clean($allDataInSheet[$i]["B"]);
										}
									}
									else
									{
										$SkippedArray[$i] = " price is grater then old of product  ".$db->clean($allDataInSheet[$i]["A"])." - ".$db->clean($allDataInSheet[$i]["B"]);
									} 
								}
							}
						}
						//print_r( $SkippedArray);exit;
						/*$Skipped=($arrayCount-8)-$Member;*/
						$Skipped=($arrayCount-1)-$Member;
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
									$SkipMessage .= "Rows ".$key." - ".$value;
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
                        	$db->addSuccessMessage("Pricelist Update Successfully");
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
		
		if($Fail)
		{
			$db->rp_delete($ctable,"id='".$GroupID."'");				
		}
	}	
	else
	{
		$db->addErrorMessage("excel file required.");
	}

	$db->rp_location("import_pricelist_data_crud.php?price_list_id=".$_REQUEST['price_list_id']."&tcid=".$_REQUEST['tcid']."&cid=".$_REQUEST['cid']);
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
				<h1><a href="price_table_manage.php?pid=<?= $_REQUEST['price_list_id'] ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
											
											<button style="margin-top: 5px!important;" type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="sample" href="" title="Download Sample XL Report"><i class="fa fa-file-excel-o"></i>Download Sample Excel</button>
											<!-- <a href="download/product_stock.xlsx" download="" class="btn btn-link">Download Sample Excel</a>
											<p class="help-block"></p> -->
											<input type="hidden" name="price_list_id" id="price_list_id" value="<?= $_REQUEST['price_list_id'] ?>">
									    </div>                           
                                    </div>
                                </div>
                                
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green submit_form">Submit</button>
								<a type="button" class="btn btn-default" href="price_table_manage.php?pid=<?= $_REQUEST['price_list_id'] ?>">Back</a>
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


<script type="text/javascript">
	function genReport(){
		var tcid = '<?= $_REQUEST['tcid'] ?>';
		var cid = '<?= $_REQUEST['cid'] ?>';
	  	$.ajax({
	        method: "POST",
	        url: "pricelist_sample_excel_ajax.php",
	        data:{
	    		tcid:tcid,
	    		cid:cid, 
			},	
			dataType : 'json',
			beforeSend: function()
			{ 
				$('.preloader').fadeIn('slow');
			},
	    	success: function(result){ 
	    		$('.preloader').fadeOut('slow');
	    		window.location.href="<?=SITEURL?>"+result.file_path;
	    	}, 
		});
	}
</script> 
</body>
</html>