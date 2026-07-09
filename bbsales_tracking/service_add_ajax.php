<?php
include('connect_in.php');

$mode = isset($_REQUEST['mode']) ? $db->clean($_REQUEST['mode']) : '';
$complain_id = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : '';

// FOR ADD
$product = isset($_REQUEST['product']) ? $db->clean($_REQUEST['product']) : '';
$psc = isset($_REQUEST['psc']) ? $db->clean($_REQUEST['psc']) : '';

if ($mode == 'type_of_product') {
    /* Complain Data Get */
    $GetOutlet_R = $db->rp_getData("complain", "*", "id='" . $complain_id . "' AND isDelete=0", "", 0);
    $GetOutlet_D = mysqli_fetch_assoc($GetOutlet_R);
    $complain_assign_to = $db->rp_getValue("complain", "complain_assign_to", "id='" . $GetOutlet_D['id'] . "' AND isDelete=0", 0);

    //Product Sub Category
    $productSubCatIds = $GetOutlet_D['product_sub_category'];
    if ($productSubCatIds != "" && $productSubCatIds != NULL && $productSubCatIds != null && isset($productSubCatIds) && !empty($productSubCatIds)) {
        $productSubCatIdsArr = explode(",", $productSubCatIds);
        $productSubCatIdsArr = is_array($productSubCatIdsArr) ? $productSubCatIdsArr : array();
    }
    ?>
    <div class="portlet-body">
        <span style="color: red; font-size: 15px;"><strong>NOTE: The Selected Product And Type Of Product Is Already Added In The Service</strong></span>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="test">Product Sub Category</label>
                            <select class="form-control b-3" id="product_sub_category" name="product_sub_category[]" multiple>
                                <option value="">Select Sub Category</option>
                                <?php
                                $category_master_r = $db->rp_getData("category_master", "*", "isDelete=0 AND isActive=1");
                                if ($category_master_r) {
                                    while ($category_master_d = mysqli_fetch_assoc($category_master_r)) {
                                        ?>
                                        <option value="<?= $category_master_d['id'] ?>" <?= in_array($category_master_d['id'], $productSubCatIdsArr) ? "selected" : ""; ?>><?= $category_master_d['name'] ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                            <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="test">Product Name</label>
                            <select class="form-control b-3" id="product_id" name="product_id[]" multiple>
                                <option value="">Select Product</option>

                            </select>
                            <p class="help-block"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" name="submit" class="btn green" id="add-service-item-btn">Submit</button>
    </div>
<?php
} else if ($mode == 'add') {
    $values = array("product_id" => $product, "product_sub_category" => $psc);
    $is_update = $db->rp_update("complain", $values, "isDelete=0 AND id='" . $complain_id . "'", 0);
    if ($is_update) {
        $ack = array("ack" => 1, "ack_msg" => "Service Item Added Successfully");
    } else {
        $ack = array("ack" => 0, "ack_msg" => "Service Item Not Added!!!", "developer_msg" => "Database error!!!");
    }
    $db->printJSON($ack);
    include('disconnect.php');
    exit;
}
?>

<?php
if ($mode == 'type_of_product') {
    ?>
    <script type="text/javascript">
        $("#product_sub_category").fSelect();
        $("#product_id").fSelect();

        $("#product_sub_category").change(function() {
            var product_sub_category = String($("#product_sub_category").val());
            var product = '<?= $GetOutlet_D['product_id'] ?>';
            $.ajax({
                type: 'POST',
                url: 'sub_category_wise_product_ajax.php',
                data: {
                    psc_id: product_sub_category,
                    product: product
                },
                beforeSend: function() {
                    $("#loading-modal").modal('show');
                },
                success: function(result) {
                    $("#product_id").fSelect("destroy");
                    $("#product_id").val("");
                    $("#product_id").html(result);
                    $("#product_id").fSelect("create");
                    $("#loading-modal").modal('hide');
                }
            });

        });

        $(document).ready(function() {
            $("#product_sub_category").trigger('change');
        });
    </script>
<?php
}
?>
<script type="text/javascript">
    $("#add-service-item-btn").on('click',function(){
        // alert("sdfd");
        var product_sub_category    = String($("#product_sub_category").val());
        var product                             = String($("#product_id").val());
        var complain_id                     = '<?= $_REQUEST['complain_id'] ?>';
        $.ajax({
            type:'POST',
            url:'service_add_ajax.php?mode=add',
            data:{
                psc:product_sub_category,
                product:product,
                complain_id:complain_id
            },
            beforeSend:function(){
                $("#loading-modal").modal('show');
            },
            success:function(result){
                // debugger;
                $("#loading-modal").modal('hide');
                 result = JSON.parse(result);
                if (result.ack==1) {
                    toastr.success(result.ack_msg);
                } else {
                    toastr.error(result.ack_msg);
                }
                location.reload();
            }
        });
    });
</script>
