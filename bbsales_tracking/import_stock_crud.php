<?php
$page_id=641;$page_slug='import_stock_page';
$ctable 	= "import_stock";
$ctable1 	= "Import Stock";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"import_stock_manage.php","title"=>$page_title),array("link"=>"import_stock_crud.php","title"=>"Add/Edit ".$ctable1));
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
				   	$UploadName1 = "Product-Stock-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/stock/".$UploadName1;
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
						$SkippedArray1=array();
						
						if($arrayCount>1)
						{ 
							if($allDataInSheet[1]["A"]!="Item Name" || $allDataInSheet[1]["B"]!="Item Code" || $allDataInSheet[1]["C"]!="Daily Production" || $allDataInSheet[1]["D"]!="Availbale Stock")
							{								
								$Fail=true;
								$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								throw new Exception();
							}
						}
						
						$warehouse_id=$_REQUEST['warehouse_id'];

						for($i=2;$i<=$arrayCount;$i++)
						{ 
						    $alias 	    = $db->clean($allDataInSheet[$i]["B"]);
							 
							// $catno=$allDataInSheet[$i]['B'];
							$catno = str_replace(' ', '', $alias);
							$stock_qty=$allDataInSheet[$i]['C'];

							if($stock_qty=="")
							{
							    $stock_qty=0;
							}
							else
							{
							    $stock_qty = (float)$stock_qty;
							}
							    
							$IsDuplicateGroupName=$db->rp_getTotalRecord("product_weight_price","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);
							if($IsDuplicateGroupName>0)
							{
								$product_id=$db->rp_getValue("product_weight_price","product_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);
								$weight_id=$db->rp_getValue("product_weight_price","weight_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);

								$p_name=$db->rp_getValue("product","name","id='".$product_id."' AND isDelete=0",0);

								$get_current_stock =$db->get_available_stock($product_id,$weight_id,$warehouse_id);
								$new_stock=$get_current_stock+$stock_qty;
								if($stock_qty<0 && $new_stock<0)
								{   
									$SkippedArray1[$i] = $db->clean($allDataInSheet[$i]["A"]);
								}
								else{ 
									$planning_date = date('Y-m-d');
									$remark = "Import Stock At ".$planning_date;

									$insert = $db->rp_insert("inward_stock",array($product_id,$weight_id,$p_name,$stock_qty,$planning_date,$remark,$warehouse_id),array("pro_id","weight_id","pro_name","pro_qty","planning_date","remark","warehouse_id"),0);
									if($insert)
									{
										/*update main stock*/
										$get_current_stock =$db->get_available_stock($product_id,$weight_id,$warehouse_id); 
										 
										$new_stock_qty = $get_current_stock;
										$update = $db->rp_update("product_weight_price",array("stock_qty"=>$new_stock_qty),"LOWER(replace(catno,' ', ''))='".strtolower($catno)."' AND isDelete=0");		
										/*update main stock*/
										$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
										$Member++;
									}
								}
							}
							else
							{
							    $SkippedArray[$i] = $db->clean($allDataInSheet[$i]["A"]);
							}
						}
						$Skipped=($arrayCount-1)-$Member;
						$SkipMessage="";
						/*if($Skipped>0)
						{*/
							$SkipMessage="Total <b>".$Skipped."</b> Row(s) Not Found And Total Update <b>".$Member."</b> Row(s)";
							if(sizeof($SkippedArray)>0)
							{
								$arrayCount = strlen((string)$arrayCount);

								$SkipMessage .="<br/>";
								$SkipMessage .="<br/>";
								$SkipMessage.= " ***Not Found List*** ";
								$SkipMessage .="<br/>";
								foreach ($SkippedArray as $key => $value) {
									$key = sprintf("%0".$arrayCount."d", $key);
									$SkipMessage .= $key." - ".$value;
									$SkipMessage .="<br/>";
								}
							}
							if(sizeof($SkippedArray1)>0)
							{
								$arrayCount = strlen((string)$arrayCount);

								$SkipMessage .="<br/>";
								$SkipMessage .="<br/>";
								$SkipMessage.= " ***You cannot add minus stock QTY more than your Available Stock*** ";
								$SkipMessage .="<br/>";
								foreach ($SkippedArray1 as $key => $value) {
									$key = sprintf("%0".$arrayCount."d", $key);
									$SkipMessage .= "Rows ".$key." - ".$value;
									$SkipMessage .="<br/>";
								}
							}
							// skipped rows
							// total update count
						/*}*/
						if($Skipped>0)
						{
                        	$db->addErrorMessage($SkipMessage);
						}
						else
						{
                        	$db->addSuccessMessage("Stock Update Successfully");
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

	$db->rp_location("import_stock_crud.php?mode=add");
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
				<h1><a href="<?php echo "push_notification_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
									<div class="col-md-6">
										<div class="form-group">
											<label>Select Warehouse <code>*</code></label>
											<select class="form-control" name="warehouse_id" id="warehouse_id">
												<option value="">Select Warehouse</option>
												<?php
												$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
												while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
												{
												?>
												<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
												<?php echo $WarehouseD['name']; ?>
												</option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
								<br/><div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
											<label>Excel File<code>*</code></label>
											<input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB"  data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file"  name="excel_upload" id="excel_upload" data-validation="required">
											
											<br/><button style="margin-top: 5px!important;" type="button" class="btn btn-primary hidden" name="excel" onClick="genReport()" id="sample" href="" title="Download Sample XL Report"><i class="fa fa-file-excel-o"></i>Download Sample Excel</button>
											<!-- <a href="download/product_stock.xlsx" download="" class="btn btn-link">Download Sample Excel</a>
											<p class="help-block"></p> -->
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
	
	if($("#warehouse_id").val()=="" || $("#warehouse_id").val().split(" ").join("")==""){
			
		vd=aj.error('warehouse_id',"Please Select Warehouse.","add_error");
		isValid=false;
	}

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
		$("#loading-modal").modal("hide");
		return false;
	}
	
}
</script>

<script type="text/javascript">
	$(".submit_form").on('click',function(){
	    $("#loading-modal").modal("show");
	})

	$("#warehouse_id").on('change',function(){
		var warehouse_id=$("#warehouse_id").val();
		if(warehouse_id!="")
		{
	    	$("#sample").removeClass("hidden");
		}
		else{
			$("#sample").addClass("hidden");
		}
	})
</script>

<script type="text/javascript">
	function genReport(){ 
		var searchName     = $("#searchName").val(); 
		var warehouse_id=$("#warehouse_id").val();
	  	$.ajax({
	        method: "POST",
	        url: "product_stock_sample_excel_ajax.php",
	        data:{
	    		searchName:searchName,
	    		warehouse_id:warehouse_id,
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