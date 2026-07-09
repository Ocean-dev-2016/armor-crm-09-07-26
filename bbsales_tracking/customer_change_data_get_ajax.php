<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$customer_id = $_REQUEST['customer_id'];
$quotation_id = $_REQUEST['id'];
$CustomerR = $db->rp_getData("executive","*","id='".$customer_id."' AND isDelete=0","",0);
$CustomerD  = mysqli_fetch_assoc($CustomerR);
?>
<div class="modal-body">
    <div class="row">
        <h5 style="color: red;font-weight: bold;margin-right: 77px;margin-top: -11px;font-size: 15px;text-align: center;">NOTE : This Change Will Not Effect In Product , Price And Pricelist..</h5>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Select Customer Type</b></label>
                <select class="form-control cust_type" id="customer_type" name="customer_type" onchange="getCustomer(this.value)">
                    <option value="">Select Customer Type </option>
                    <?php
                    $cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
                    if ($cust_R) 
                    {
                        while ($C = mysqli_fetch_assoc($cust_R)) 
                        {
                            ?>
                            <option <?= ($customer_type == $C['id']) ? "selected" : ""; ?> value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Select Customer</b></label>
                <select class="form-control new_cust_id" name="cust_id" placeholder="Select Customer" id="dealer_id12" type="text">
                    <option value="">Select Customer</option>
                </select>
            </fieldset>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
    <button type="button" id="change_customer" class="btn btn-success">Update</button>
</div>

<script type="text/javascript">
$(".cust_type").select2();
$(".new_cust_id").select2();

function getCustomer(ctype) 
{
    $.ajax({
        type: "post",
        url: "ajax_get_customer.php",
        data: "customer_type=" + ctype,
        beforeSend: function() {
            $('.preloader').fadeIn('slow');
        },
        success: function(result) 
        {
            setTimeout(function() 
            {
                $('#dealer_id12').html(result);
                $('.preloader').fadeOut('slow');
            });
        }
    })
}

$(function()
{
    $("#change_customer").on('click',function(){
        ChangeCustomerDetails();
    });
})

function ChangeCustomerDetails()
{
    var customer_type = $("#customer_type").val();
    var customer_id   = $("#dealer_id12").val();
    var edit_id       = '<?= $_REQUEST['id'] ?>';
    var mode          = '<?= $_REQUEST['mode'] ?>';
    $.ajax({
        type: "POST",
        url: "change_customer_data_ajax.php",
        data: {
            customer_type: customer_type,
            edit_id: edit_id,
            mode: mode,
            customer_id: customer_id,
        },
        beforeSend: function() {
            $(".transCover").fadeIn(800);
        },
        success: function(result) 
        {
            var result = $.parseJSON(result);
            if (result.ack == 1)
            { 
                $(".transCover").fadeOut(100);
                toastr.success(result.ack_msg);
                $("#requesting_ajax_chnage_customer").click();
                location.reload();
            } 
            else 
            {
                toastr.error(result.ack_msg);
            }
        }
    })
}
</script>
<?php require_once 'disconnect.php';  ?>