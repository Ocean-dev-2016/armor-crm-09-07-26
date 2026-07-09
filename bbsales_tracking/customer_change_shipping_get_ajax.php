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
        <div class="col-md-12">
            <fieldset class="form-group floating-label-form-group">


                <label for="shipping_address_change">Shipping Address</label>
                <select name="shipping_address_change" id="shipping_address_change" class="form-control shipping_address_change">
                    <option value="">--Select Shipping Address--</option>
                    <?php
                    $country_r = $db->rp_getData("customer_vs_shipping_address", "*","customer_id='".$CustomerD['id']."' AND isDelete=0","",0);
                    if (mysqli_num_rows($country_r) > 0) 
                    {
                        while ($country_d = mysqli_fetch_array($country_r)) 
                        {
                            ?>
                            <option value="<?php echo htmlentities($country_d['shipping_address']); ?>" <?php if (htmlentities($country_d['shipping_address']) == htmlentities($country)) { ?> selected <?php } ?>><?php echo htmlentities($country_d['shipping_address']); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>

            </fieldset>
        </div>
    </div>
</div>

<?php
if($_REQUEST['mode']=="quotation_change_shipping")
{
    ?>
    <div class="modal-footer">
        <button type="button" id="upadte_customer_btn" class="btn btn-primary" data-dismiss="modal">Add</button>
    </div>
    <?php
}
?>
<script type="text/javascript">

    $(".shipping_address_change").select2();

    $(function()
    {
        $("#upadte_customer_btn").on('click',function(){
            UpdateCustomerDetailsNew();
        });
    })

    function UpdateCustomerDetailsNew()
    {

        var shipping_address = $('#shipping_address_change').val();
        // alert(shipping_address);
        $('#shipping_address').val(shipping_address);
    }
</script>
<?php require_once 'disconnect.php';  ?>