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
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Comapny Name</b></label>
                <input type="text" class="form-control company_name" name="company_name" id="company_name" value="<?= $CustomerD['company_name'] ?>">
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Person Name</b></label>
                <input type="text" class="form-control person_name" name="person_name" id="person_name" value="<?= $CustomerD['cname'] ?>">
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Phone</b></label>
                <input type="text" class="form-control phone" name="phone" id="phone" value="<?= $CustomerD['phone'] ?>" maxlength="10">
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Email</b></label>
                <input type="text" class="form-control email" name="email" id="email" value="<?= $CustomerD['email'] ?>">
            </fieldset>
        </div>
    </div>  
    <div class="row">
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>State</b></label>
                <input type="text" class="form-control state" name="state" id="state" value="<?= $CustomerD['state'] ?>">
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>GST</b></label>
                <input type="text" class="form-control gst" name="gst" id="gst" value="<?= $CustomerD['gst'] ?>">
            </fieldset>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Address</b></label>
                <textarea class="form-control" id="address" rows="1" name="address" style="resize: vertical;"><?= $CustomerD['address'] ?></textarea>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Billing Address</b></label>
                <textarea class="form-control" id="billing_address_edit" rows="1" name="billing_address" style="resize: vertical;"><?= $CustomerD['billing_address'] ?></textarea>
            </fieldset>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-6">
            <fieldset class="form-group floating-label-form-group">
                <label for="email"><b>Shipping Address</b></label>
                <textarea class="form-control" id="shipping_address_edit" rows="1" name="shipping_address" style="resize: vertical;"><?= $CustomerD['shipping_address'] ?></textarea>
            </fieldset>
        </div>
    </div> -->
</div>

<?php
if($_REQUEST['mode']=="quotation")
{
    ?>
    <div class="modal-footer">
        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="upadte_customer" class="btn btn-success">Quotation</button>
        <button type="button" id="upadte_customer_btn" class="btn btn-primary">Quotation + Customer</button>
    </div>
    <?php
}
else if($_REQUEST['mode']=="order")
{
    ?>
    <div class="modal-footer">
        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="upadte_customer" class="btn btn-success">Order</button>
        <button type="button" id="upadte_customer_btn" class="btn btn-primary">Order + Customer</button>
    </div>
    <?php
}
else if($_REQUEST['mode']=="invoice")
{
    ?>
    <div class="modal-footer">
        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="upadte_customer" class="btn btn-success">Invoice</button>
        <button type="button" id="upadte_customer_btn" class="btn btn-primary">Invoice + Customer</button>
    </div>
    <?php
}
?>

<script type="text/javascript">
    $(function()
    {
        $("#upadte_customer").on('click',function(){
            UpdateCustomerDetails();
        });
    })

    $(function()
    {
        $("#upadte_customer_btn").on('click',function(){
            UpdateCustomerDetailsNew();
        });
    })

    function UpdateCustomerDetails()
    {
        var customer_id = '<?= $_REQUEST['customer_id'] ?>';
        var edit_id = '<?= $_REQUEST['id'] ?>';
        var mode = '<?= $_REQUEST['mode'] ?>';
        if(mode=="quotation")
        {
            var sub_mode = 'only_quotation';
        }
        else if(mode=="order")
        {
            var sub_mode = 'only_order';
        }
        else if(mode=="invoice")
        {
            var sub_mode = 'only_invoice';
        }
        var company_name = $(".company_name").val();
        var person_name = $("#person_name").val();
        var phone = $("#phone").val();
        var email = $("#email").val();
        var address = $("#address").val();
        var state = $("#state").val();
        var gst = $("#gst").val();
        var billing_address = $("#billing_address_edit").val();
        var shipping_address = $("#shipping_address_edit").val();
        $.ajax({
            type: "POST",
            url: "upadte_customer_data_ajax.php",
            data: {
                customer_id: customer_id,
                edit_id: edit_id,
                mode: mode,
                sub_mode: sub_mode,
                company_name: company_name,
                person_name: person_name,
                phone: phone,
                email: email,
                address: address,
                state: state,
                gst: gst,
                billing_address: billing_address,
                shipping_address: shipping_address,
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
                    $("#requesting_ajax").click();
                    location.reload();
                } 
                else 
                {
                    toastr.error(result.ack_msg);
                }
            }
        })
    }

    function UpdateCustomerDetailsNew()
    {
        var customer_id = '<?= $_REQUEST['customer_id'] ?>';
        var edit_id = '<?= $_REQUEST['id'] ?>';
        var mode = '<?= $_REQUEST['mode'] ?>';
        if(mode=="quotation")
        {
            var sub_mode = 'quotation_with_customer';
        }
        else if(mode=="order")
        {
            var sub_mode = 'order_with_customer';
        }
        else if(mode=="invoice")
        {
            var sub_mode = 'invoice_with_customer';
        }
        var company_name = $(".company_name").val();
        var person_name = $("#person_name").val();
        var phone = $("#phone").val();
        var email = $("#email").val();
        var address = $("#address").val();
        var state = $("#state").val();
        var gst = $("#gst").val();
        var billing_address = $("#billing_address_edit").val();
        var shipping_address = $("#shipping_address_edit").val();
        $.ajax({
            type: "POST",
            url: "upadte_customer_data_ajax.php",
            data: {
                customer_id: customer_id,
                edit_id: edit_id,
                mode: mode,
                sub_mode: sub_mode,
                company_name: company_name,
                person_name: person_name,
                phone: phone,
                email: email,
                address: address,
                state: state,
                gst: gst,
                billing_address: billing_address,
                shipping_address: shipping_address,
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
                    $("#requesting_ajax").click();
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