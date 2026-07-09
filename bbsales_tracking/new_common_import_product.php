<?php
$page_id        = 608;
$page_slug      = 'price_list_master';
$ctable         = "product";
$page_title     = "Import Product";
$page_hierarchy = array(
    array(
        "link" => "",
        "title" => "Utility"
    ),
    array(
        "link" => "new_common_import_product.php",
        "title" => $page_title
    ),
    array(
        "link" => "new_common_import_product.php",
        "title" => "" . $page_title
    )
);
// $typeArray = array(1=>"Without Variant",2=>"With Variant");
include("connect.php");
$_SESSION['topcatName'] = "";
$_SESSION['topcatId']   = "";
function FindOrInsertTopCategory($db, $topcatName)
{
    if ($topcatName == "" && $_SESSION['topcatName'] == "") {
        $topcatName             = "Default";
        $_SESSION['topcatName'] = "Default";
    }
    if ($topcatName == "") {
        $topcatName = $_SESSION['topcatName'];
    } else {
        $_SESSION['topcatName'] = $topcatName;
    }
    $topcatId = $db->rp_getValue("top_category_master", "id", "LOWER(name) = '" . strtolower($topcatName) . "' AND isDelete = 0 AND isActive = 1");
    if (!isset($topcatId) || $topcatId == "" || $topcatId == 0 || $topcatId == null || !$topcatId) {
        $topcatId = $db->rp_insert("top_category_master", array(
            $topcatName
        ), array(
            "name"
        ), 0);
    }
    $_SESSION['topcatId'] = $topcatId;
    return $topcatId;
}
$_SESSION['catName'] = "";
$_SESSION['catId']   = "";
function FindOrInsertCategory($db, $catName, $topCatId)
{
    if ($catName == "" && $_SESSION['catName'] == "") {
        $catName             = "Default";
        $_SESSION['catName'] = "Default";
    }
    if ($catName == "") {
        $catName = $_SESSION['catName'];
    } else {
        $_SESSION['catName'] = $catName;
    }
    $catId = $db->rp_getValue("category_master", "id", "LOWER(name) = '" . strtolower($catName) . "' AND tcid = '" . $topCatId . "' AND isDelete = 0 AND isActive = 1");
    if (!isset($catId) || $catId == "" || $catId == 0 || $catId == null || !$catId) {
        $catId = $db->rp_insert("category_master", array(
            $catName,
            $topCatId
        ), array(
            "name",
            "tcid"
        ), 0);
    }
    $_SESSION['catId'] = $catId;
    return $catId;
}
$_SESSION['subcatName'] = "";
$_SESSION['subcatId']   = "";
function FindOrInsertSubCategory($db, $subcatName, $catId, $topCatId)
{
    if ($subcatName == "" && $_SESSION['subcatName'] == "") {
        $subcatName             = "Default";
        $_SESSION['subcatName'] = "Default";
    }
    if ($subcatName == "") {
        $subcatName = $_SESSION['subcatName'];
    } else {
        $_SESSION['subcatName'] = $subcatName;
    }
    $subcatId = $db->rp_getValue("sub_category_master", "id", "LOWER(name) = '" . strtolower($subcatName) . "' AND tcid = '" . $topCatId . "' AND cid = '" . $catId . "' AND isDelete = 0 AND isActive = 1");
    if (!isset($subcatId) || $subcatId == "" || $subcatId == 0 || $subcatId == null || !$subcatId) {
        $subcatId = $db->rp_insert("sub_category_master", array(
            $subcatName,
            $topCatId,
            $catId
        ), array(
            "name",
            "tcid",
            "cid"
        ), 0);
    }
    $_SESSION['subcatId'] = $subcatId;
    return $subcatId;
}
$_SESSION['orderUnit']   = "";
$_SESSION['OrderUnitId'] = 0;
function FindOrInsertOrderUnit($db, $orderUnit)
{
    if ($orderUnit == "" && $_SESSION['orderUnit'] == "") {
        $orderUnit             = "PCS";
        $_SESSION['orderUnit'] = "PCS";
    }
    if ($orderUnit == "") {
        $orderUnit = $_SESSION['orderUnit'];
    } else {
        $_SESSION['orderUnit'] = $orderUnit;
    }
    $OrderUnitId = 0;
    if ($orderUnit != "") {
        $OrderUnitId = $db->rp_getValue("unit", "id", "LOWER(name) = '" . strtolower($orderUnit) . "' AND isDelete = 0 AND isActive = 1");
        if (!isset($OrderUnitId) || $OrderUnitId == "" || $OrderUnitId == 0 || $OrderUnitId == null || !$OrderUnitId) {
            $OrderUnitId = $db->rp_insert("unit", array(
                $orderUnit
            ), array(
                "name"
            ), 0);
        }
        $_SESSION['OrderUnitId'] = $OrderUnitId;
    }
    return $OrderUnitId;
}
$_SESSION['displayUnit']   = "";
$_SESSION['DisplayUnitId'] = 0;
function FindOrInsertDispayUnit($db, $displayUnit)
{
    if ($displayUnit == "" && $_SESSION['displayUnit'] == "") {
        $displayUnit             = "PCS";
        $_SESSION['displayUnit'] = "PCS";
    }
    if ($displayUnit == "") {
        $displayUnit = $_SESSION['displayUnit'];
    } else {
        $_SESSION['displayUnit'] = $displayUnit;
    }
    $DisplayUnitId = 0;
    if ($displayUnit != "") {
        $DisplayUnitId = $db->rp_getValue("unit", "id", "LOWER(name) = '" . strtolower($displayUnit) . "' AND isDelete = 0 AND isActive = 1");
        if (!isset($DisplayUnitId) || $DisplayUnitId == "" || $DisplayUnitId == 0 || $DisplayUnitId == null || !$DisplayUnitId) {
            $DisplayUnitId = $db->rp_insert("unit", array(
                $displayUnit
            ), array(
                "name"
            ), 0);
        }
        $_SESSION['DisplayUnitId'] = $DisplayUnitId;
    }
    return $DisplayUnitId;
}
$_SESSION['productDescription'] = "";
function FindOrGetProductDscr($dscr = "")
{
    if ($dscr == "") {
        $dscr = $_SESSION['topcatName'];
    }
    $_SESSION['productDescription'] = $dscr;
    return $dscr;
}
$_SESSION['type']        = 1;
$_SESSION['productName'] = "";
$_SESSION['ProductId']   = 0;
$_SESSION['tax']         = 18;
$_SESSION['hsn']         = "0000";
$_SESSION['image_path']  = "";
function FindOrInsertProduct($db, $type = 1, $productName = "", $subcatId = 0, $catId, $topcatId, $orderUnitId, $displayUnitId, $tax = 18, $hsn = "0000", $Description = "", $imagePath = "",$customer_unit_id="")
{
   // echo $customer_unit_id;exit();
   
    $customer_order_unit_id=strtolower($customer_unit_id);
    $unit_id=strtolower($orderUnitId);
    if($unit_id=="box")
    {
        $orderUnitId="-1";
    }
    else if($unit_id=="strip")
    {
        $orderUnitId="-2";
    }
    else if($unit_id=="pallet")
    {
        $orderUnitId="-3";
    }
    else if($unit_id=="caret")
    {
        $orderUnitId="1";
    }
    else if($unit_id=="big box")
    {
        $orderUnitId="2";
    }
    else if($unit_id=="nos")
    {
        $orderUnitId="100";
    }
    else
    {
        $orderUnitId="";
    }

  
      if($customer_order_unit_id=="box")
    {
        $customer_order_unit="-1";
    }
    else if($customer_order_unit_id=="strip")
    {
        $customer_order_unit="-2";
    }
    else if($customer_order_unit_id=="pallet")
    {
        $customer_order_unit="-3";
    }
    else if($customer_order_unit_id=="caret")
    {
        $customer_order_unit="1";
    }
    else if($customer_order_unit_id=="big box")
    {
        $customer_order_unit="2";
    }
    else if($customer_order_unit_id=="nos")
    {
        $customer_order_unit="100";
    }
    else
    {
        $customer_order_unit="";
    }



    $CollectionArray = array();
    if ($productName != "") {
        $_SESSION['image_path'] = $imagePath;
        if ($productName == "" && $_SESSION['productName'] != "") {
            $productName = $_SESSION['productName'];
        } else {
            $_SESSION['productName'] = $productName;
        }
        $CollectionArray['tcid']         = $topcatId;
        $CollectionArray['cid']          = $catId;
        // if available
        // $CollectionArray['scid'] = $subcatId;
        $CollectionArray['name']         = $db->clean($productName);
        $CollectionArray['slug']         = $db->clean($db->rp_createProSlug(trim($productName)));
        $CollectionArray['descr']        = $Description;
        $CollectionArray['cgst']         = ($tax / 2);
        $CollectionArray['sgst']         = ($tax / 2);
        $CollectionArray['igst']         = $tax;
        $CollectionArray['unit_id']      = $orderUnitId;
        //$CollectionArray['order_type']      = $orderUnitId;
        $CollectionArray['hsn_code']         = $hsn;
        // if available
        $CollectionArray['display_unit'] = $displayUnitId;
        $CollectionArray['product_type'] = $type;
        $CollectionArray['image_path']   = $imagePath;
        $CollectionArray['customer_unit_id'] = $customer_order_unit;
        // is subcatid available then check subcatid also
        /*$ProductId  = $db->rp_getValue("product", "id", "tcid = '" . $topcatId . "' AND cid = '" . $catId . "' AND isDelete = 0 AND LOWER(name) = '" . trim(strtolower($productName)) . "'", 0);
        if (!isset($ProductId) || $ProductId == "" || $ProductId == 0 || $ProductId == null || !$ProductId) {
       */     $ProductId = $db->rp_insert("product", array_values($CollectionArray), array_keys($CollectionArray), 0);

            if($ProductId!=0)
            {
                /* Add By Naveen */

                $catId = ",". $CollectionArray['tcid'];

                $allCustomerCatUpdateSql = "UPDATE executive SET top_category_id = CONCAT(top_category_id, '".$catId."') WHERE isDelete = 0";
                $allCustomerCatUpdate = $db->query($allCustomerCatUpdateSql);
                /* Badha Sales Executive Ma Top Cat ID Update Karavel */

                $catId = ",".$CollectionArray['tcid'];
                $allSalesCatUpdateSql = "UPDATE sales_executive SET top_category_id = CONCAT(top_category_id, '".$catId."') WHERE isDelete = 0";
                $allSalesCatUpdate = $db->query($allSalesCatUpdateSql);
                /* Badha Sales Executive Ma Top Cat ID Update Karavel */
            }
        //}
        $_SESSION['ProductId'] = $ProductId;
    } else {
        $ProductId = $_SESSION['ProductId'];
    }
    return $ProductId;
}
$_SESSION['VariantWeight']   = "";
$_SESSION['VariantWeightId'] = "";
function FindOrInsertVariantWeight($db, $VariantWeight = "")
{
    if ($VariantWeight == "" && $_SESSION['VariantWeight'] == "") {
        $VariantWeight             = "Default";
        $_SESSION['VariantWeight'] = "Default";
    }
    if ($VariantWeight == "") {
        $VariantWeight = "Default";
    } else {
        $_SESSION['VariantWeight'] = $VariantWeight;
    }

    
    if($VariantWeight == "Default")
    {
        $VariantWeightId = -1;
    }
    else
    {
        $VariantWeight = $db->clean($VariantWeight);
        $VariantWeightId = $db->rp_getValue("weight", "id", "LOWER(name) = '" . strtolower($VariantWeight) . "' AND isDelete = 0 ",0);
        if (!isset($VariantWeightId) || $VariantWeightId == "" || $VariantWeightId == 0 || $VariantWeightId == null || !$VariantWeightId) {
            $VariantWeightId = $db->rp_insert("weight", array(
                $VariantWeight
            ), array(
                "name"
            ), 0);
        }
    }
    $_SESSION['VariantWeightId'] = $VariantWeightId;
    return $VariantWeightId;
}
if (isset($_POST['submit'])) {
    if (isset($_FILES['excel_upload'])) {
        $Fail      = false;
        $file      = $_FILES['excel_upload'];
        $TempFile  = $file['tmp_name'];
        $FileName  = $file['name'];
        $FileType  = $file['type'];
        $FileError = $file['error'];
        $FileSize  = $file['size'];
        if ($FileError == 0) {
            if ($FileType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || $FileType == 'application/vnd.ms-excel') {
                if ($FileSize <= 1024 * 1024 * 2) {
                    $UploadName = "product-upload-" . date("dmYhis") . "-" . $FileName;
                    $UploadURL  = "sheet_import/uploads/product/" . $UploadName;
                    move_uploaded_file($TempFile, $UploadURL);
                    include "PHPExcel/IOFactory.php";
                    try {
                        $objPHPExcel    = PHPExcel_IOFactory::load($UploadURL);
                        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                        ob_end_clean();
                        $arrayCount   = count($allDataInSheet);
                        $Member       = 0;
                        $Numbers      = array();
                        $SkippedArray = array();
                        if ($arrayCount > 2) {
                            if (strtolower($allDataInSheet[1]["A"]) != strtolower("Type") || 
                                strtolower($allDataInSheet[1]["B"]) != strtolower("Top Category") || 
                                strtolower($allDataInSheet[1]["C"]) != strtolower("Category") || 
                                strtolower($allDataInSheet[1]["D"]) != strtolower("sub Category") || 
                                strtolower($allDataInSheet[1]["E"]) != strtolower("name") || 
                                strtolower($allDataInSheet[1]["F"]) != strtolower("Sales Order Unit") || 
                                strtolower($allDataInSheet[1]["G"]) != strtolower("Display unit") || 
                                strtolower($allDataInSheet[1]["H"]) != strtolower("TAX") || 
                                strtolower($allDataInSheet[1]["I"]) != strtolower("HSN") || 
                                strtolower($allDataInSheet[1]["J"]) != strtolower("Description") || 
                                strtolower($allDataInSheet[1]["K"]) != strtolower("image path") || 
                                strtolower($allDataInSheet[1]["L"]) != strtolower("variant") || 
                                strtolower($allDataInSheet[1]["M"]) != strtolower("MRP") || 
                                strtolower($allDataInSheet[1]["N"]) != strtolower("Item code") || 
                                strtolower($allDataInSheet[1]["O"]) != strtolower("inner") || 
                                strtolower($allDataInSheet[1]["P"]) != strtolower("outer") || 
                                strtolower($allDataInSheet[1]["Q"]) != strtolower("Opeaning Stock") || 
                                strtolower($allDataInSheet[1]["R"]) != strtolower("Minmum Stock") || 
                                strtolower($allDataInSheet[1]["S"]) != strtolower("Maximum Stok") || 
                                strtolower($allDataInSheet[1]["T"]) != strtolower("weight") ||
                                strtolower($allDataInSheet[1]["U"]) != strtolower("Minimum Selling Price") ||
                                strtolower($allDataInSheet[1]["V"]) != strtolower("Customer Order Unit")
                                ) {
                                $Fail = true;
                                $db->addErrorMessage("Don't alter sample file!! Please maintain structure as it is.");
                                throw new Exception();
                            }
                        }
                        $CategoryIdArray = array();
                        $ProductIdArray  = array();
                        $detailArray     = array();
                        for ($i = 2; $i <= $arrayCount; $i++) {
                            // dbObj, topCatName
                            $TopCategoryId     = FindOrInsertTopCategory($db, trim($allDataInSheet[$i]["B"]));
                            // dbObj, catName, topCatId
                            $CategoryId        = FindOrInsertCategory($db, trim($allDataInSheet[$i]["C"]), $TopCategoryId);
                            $SubCategoryId     = 0;
                            

                            // **** WARNING *** //
                            // It is now reuire right now but it is important so do not delete it //
                                // dbObj, subCatName, catId, topCatId
                                    // $SubCategoryId = FindOrInsertSubCategory($db,trim($allDataInSheet[$i]["D"]),$CategoryId,$TopCategoryId);
                                // It is now reuire right now but it is important so do not delete it //
                            // **** WARNING *** //

                            // dbObj, orderUnit
                            $OrderUnitId       = $allDataInSheet[$i]["F"];//FindOrInsertOrderUnit($db, trim($allDataInSheet[$i]["F"]));
                            $customer_unit_id       = $allDataInSheet[$i]["V"];//FindOrInsertOrderUnit($db, trim($allDataInSheet[$i]["F"]));
                            // dbObj, displayUnit
                            $DisplayUnitId     = FindOrInsertDispayUnit($db, trim($allDataInSheet[$i]["G"]));
                            // dbObj, displayUnit
                            $ProDscr           = FindOrGetProductDscr(trim($allDataInSheet[$i]["J"]));
                            // dbObj, Type, productName, subcatId, catId, topcatId, orderUnitId, displayUnitId, TAX, HSN, Description, Image Path
                            $ProductId         = FindOrInsertProduct($db, trim($allDataInSheet[$i]["A"]), trim($allDataInSheet[$i]["E"]), $SubCategoryId, $CategoryId, $TopCategoryId, $OrderUnitId, $DisplayUnitId, trim($allDataInSheet[$i]["H"]), trim($allDataInSheet[$i]["I"]), $ProDscr, trim($allDataInSheet[$i]["K"]), $customer_unit_id);

                            // dbObj, weightName
                            $VariantWeightId   = FindOrInsertVariantWeight($db, trim($allDataInSheet[$i]["L"]));
                            // echo $VariantWeightId;exit;
                            $inner_unit        = $db->clean($allDataInSheet[$i]["F"]);
                            $outer_unit        = $db->clean($allDataInSheet[$i]["V"]);
                            $InnerSize         = $db->clean($allDataInSheet[$i]["O"]);
                            $OuterSize         = $db->clean($allDataInSheet[$i]["P"]);
                            $Catno             = $db->clean($allDataInSheet[$i]["N"]);
                            $Price             = $db->clean($allDataInSheet[$i]["M"]);
                            $stock_qty         = $db->clean($allDataInSheet[$i]["Q"]);
                            $sample_stock      = $db->clean($allDataInSheet[$i]["Q"]);
                            $opening_stock_qty = $db->clean($allDataInSheet[$i]["Q"]);
                            $min_qty           = $db->clean($allDataInSheet[$i]["R"]);
                            $weight_gm          = $db->clean($allDataInSheet[$i]["T"]);
                            $minimum_selling_price = $db->clean($allDataInSheet[$i]["U"]);
                            // if required
                            // $max_qty = $db->clean($allDataInSheet[$i]["S"]);
                            $SubProductData    = $db->rp_getData("product_weight_price", "*", "product_id='" . $ProductId . "' AND weight_id='" . $VariantWeightId . "'", "",0);
                            if (mysqli_num_rows($SubProductData) == 0) {
                               $weight_kg       = ($weight_gm)/1000;
                                $inner_unit=strtolower($inner_unit);
                                $outer_unit=strtolower($outer_unit);
                                if($inner_unit=="box")
                                {
                                    $inner_unit="-1";
                                }
                                else if($inner_unit=="strip")
                                {
                                    $inner_unit="-2";
                                }
                                else if($inner_unit=="pallet")
                                {
                                    $inner_unit="-3";
                                }
                                else if($inner_unit=="caret")
                                {
                                    $inner_unit="1";
                                }
                                else if($inner_unit=="big box")
                                {
                                    $inner_unit="2";
                                }
                                else if($inner_unit=="nos")
                                {
                                    $inner_unit="100";
                                }
                                else
                                {
                                    $inner_unit="";
                                }

                              
                                  if($outer_unit=="box")
                                {
                                    $outer_unit="-1";
                                }
                                else if($outer_unit=="strip")
                                {
                                    $outer_unit="-2";
                                }
                                else if($outer_unit=="pallet")
                                {
                                    $outer_unit="-3";
                                }
                                else if($outer_unit=="caret")
                                {
                                    $outer_unit="1";
                                }
                                else if($outer_unit=="big box")
                                {
                                    $outer_unit="2";
                                }
                                else if($outer_unit=="nos")
                                {
                                    $outer_unit="100";
                                }
                                else
                                {
                                    $outer_unit="";
                                }
                                $pweightPriceInserData = array(
                                    "product_id" => $ProductId,
                                    "weight_id" => $VariantWeightId,
                                    "inner_size" => $InnerSize,
                                    "outer_size" => $OuterSize,
                                    "catno" => $Catno,
                                    "price" => $Price,
                                    "stock_qty" => $stock_qty,
                                    "pro_weight" => $weight_gm,
                                    "minimum_selling_price" => $minimum_selling_price,
                                    //"weight_in_kg" => $weight_kg,
                                    // "sample_stock" => $sample_stock,
                                    "opening_stock_qty" => $opening_stock_qty,
                                    "min_qty" => $min_qty,
                                    "inner_unit"=>$inner_unit,
                                    "outer_unit"=>$outer_unit
                                );
                                $db->rp_insert("product_weight_price", array_values($pweightPriceInserData), array_keys($pweightPriceInserData), 0);
                            }
                            // echo "string";exit;
                        }
                        $db->addSuccessMessage("Product Upload Successfully");
                    }
                    catch (Exception $e) {
                        $Fail = true;
                        $db->addErrorMessage("File not supported to upload.");
                    }
                } else {
                    $Fail = true;
                    $db->addErrorMessage("Filesize must be less than 2 MB.");
                }
            } else {
                $Fail = true;
                $db->addErrorMessage("File type must be xls or xlsx.");
            }
        } else {
            $Fail = true;
            $db->addErrorMessage("File corrupted or not uploaded try again.");
        }
        if ($Fail) {
        }
    } else {
        $db->addErrorMessage("excel file required.");
    }
    $db->rp_location("new_common_import_product.php?mode=add");
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
                        <h1><a href="<?php echo "push_notification_manage.php";?>" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
                    <div class="row">
                        <div class="col-md-6 "> 
                            <form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">                
                                <div class="portlet box blue">
                                    <div class="portlet-body form">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Excel File<code>*</code></label>
                                                        <input data-validation-allowing="vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-validation-error-msg-size="You can not upload excel larger than 2MB"  data-validation-error-msg-mime="You can only upload xls and xlsx files" data-validation-max-size="2M" type="file"  name="excel_upload" id="excel_upload" data-validation="required">
                                                        <p class="help-block"></p>
                                                    </div>                           
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" name="submit" class="btn green submit_form">Submit</button>
                                            <br><br>
                                            <a download href="../crm_sample_sheet.xlsx" type="button" class="btn btn-success btn-sm" style="background-color: green;"><i class="fa fa-download"></i> Download Sample Excel </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("footer.php"); ?>
        <?php include("include_js.php"); ?> 
        <script type="text/javascript"> 
            $(".form-control").bind("keyup change",function() { 
                if($(this).parent().hasClass("has-error")) { 
                    $(this).parent().removeClass("has-error"); 
                    $(this).parent().find('p.help-block').html(""); 
                } 
            });
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
            $(".submit_form").on('click',function(){
                $("#loading-modal").modal("show");
            });
            $(function(){
                aj.imageHolder($("input[name=image_path]"),"","",
                    function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
                        isImageThumbnailLoaded=isImageThumbnailLoadedReply;
                        isImageThumbnailValidT=isImageThumbnailValidReply;
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
                    },
                    function(data){
                        isImageThumbnailLoadedReply
                    }
                );
            })
        </script> 
    </body>
</html>