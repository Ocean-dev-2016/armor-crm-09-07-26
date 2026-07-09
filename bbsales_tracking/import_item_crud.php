<?php
$page_id=580;$page_slug='price_list_master';
$ctable 	= "import_stock";
$ctable1 	= "Item Stock";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"import_stock_manage.php","title"=>$page_title),array("link"=>"import_item_crud.php","title"=>"Add/Edit ".$ctable1));
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
				   	$UploadName1 = "Product-Item-upload-".date("d-m-Y-h-i-s")."-".$FileName;
				    $UploadURL1="sheet_import/uploads/item/".$UploadName1;
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
							// if(strtolower($allDataInSheet[1]["A"])!="product name" || strtolower($allDataInSheet[1]["B"])!="product code" || strtolower($allDataInSheet[1]["C"])!="price" || strtolower($allDataInSheet[1]["D"])!="hsn code" || strtolower($allDataInSheet[1]["E"])!="igst")

							if(strtolower($allDataInSheet[1]["A"])!="product name" || strtolower($allDataInSheet[1]["B"])!="product code" || strtolower($allDataInSheet[1]["C"])!="price")
							{
								
								$Fail=true;
								$db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
								throw new Exception();
							}
						}
						
						// for($i=4;$i<=$arrayCount;$i++)
						for($i=2;$i<=$arrayCount;$i++)
						{
							$catno 	  = $db->clean($allDataInSheet[$i]["A"]);
						    $alias 	  = $db->clean($allDataInSheet[$i]["B"]);
							$price	  = $db->clean($allDataInSheet[$i]["C"]);
							$hsn_code = $db->clean($allDataInSheet[$i]["D"]);
							$igst	  = $db->clean($allDataInSheet[$i]["E"]);
							
							$catno    = $allDataInSheet[$i]['B'];
							$catno    = str_replace(' ', '', $alias);
							$price    = $allDataInSheet[$i]['C'];
							$hsn_code = $allDataInSheet[$i]['D'];
							$igst     = $allDataInSheet[$i]['E'];

							if($price=="")
							{
							    $price=0;
							}
							else
							{
							    $price = (float)$price;
							}
							    
							$IsDuplicateGroupName=$db->rp_getTotalRecord("product_weight_price","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);
							if($IsDuplicateGroupName>0)
							{
								$row = array("price"=>$price);
								$MemberID=$db->rp_update("product_weight_price",$row,"LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);

								if($hsn_code!="")
								{
									$product_id = $db->rp_getValue("product_weight_price","product_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);

									$db->rp_update("product",array("hsn_code"=>$hsn_code),"id='".$product_id."'",0);
								}

								if($igst!="")
								{
									$product_id = $db->rp_getValue("product_weight_price","product_id","LOWER(replace(catno, ' ', ''))='".strtolower($catno)."' AND isDelete=0",0);

									$cgst 	= ($igst!=0)?$db->clean($igst/2):"9";
									$sgst 	= ($igst!=0)?$db->clean($igst/2):"9";
									$db->rp_update("product",array("cgst"=>$cgst,"sgst"=>$sgst,"igst"=>$igst),"id='".$product_id."'",0);
								}
								$Member++;
							}
							else
							{
							    $SkippedArray[$i] = $db->clean($allDataInSheet[$i]["A"]);
							}
						}
						$Skipped=($arrayCount-8)-$Member;
						$SkipMessage="";
						/*if($Skipped>0)
						{*/
							$SkipMessage="Total <b>".$Skipped."</b> Row(s) Not Found And Total Update <b>".$Member."</b> Row(s)";
							if(sizeof($SkippedArray)>0)
							{
								$arrayCount = strlen((string)$arrayCount);

								$SkipMessage .="<br/>";
								$SkipMessage .="<br/>";
								$SkipMessage .= " ***Not Found List*** ";
								$SkipMessage .="<br/>";
								foreach ($SkippedArray as $key => $value) {
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
                        	$db->addSuccessMessage("Items Update Successfully");
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

	$db->rp_location("import_item_crud.php?mode=add");
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
                                    <div class="col-sm-6">
                                        <div class="form-group">
											<label>Excel File<code>*</code></label>
											<input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB"  data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file"  name="excel_upload" id="excel_upload" data-validation="required">
											<br/>
											<button style="margin-top: 5px!important;" type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="sample" href="" title="Download Sample XL Report"><i class="fa fa-file-excel-o"></i>Download Sample Excel</button>
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
	function genReport(){
		//window.location.href='product_stock_sample_excel_ajax.php';
		var searchName     = $("#searchName").val();
	  	// searchName     	   = searchName.trim();
	  	$.ajax({
	        method: "POST",
	        url: "product_item_sample_excel_ajax.php",
	        data:{
	    		searchName:searchName,
			},	
			dataType : 'json',
			beforeSend: function()
			{
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
	    	success: function(result){
	    		// $("#loading-modal").modal('hide');
	    		$('.preloader').fadeOut('slow');
	    		window.location.href="<?=SITEURL?>"+result.file_path;
	    	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
		});
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
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
</script>

</body>
</html>