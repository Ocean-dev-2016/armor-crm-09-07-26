<?php
// $_SESSION[SITE_SESS.'_ADMIN_TYPE'] = > customer type
    $customer_where = "isDelete=0 AND isActive=1 ";
    $order_where = "isDelete=0 AND isActive=1 AND status = 1 ";
    if ($_SESSION[SITE_SESS.'_ADMIN_TYPE'] == 1) //super stockist
    {
        $customer_where .= " AND super_stockist_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
        $order_where .= " AND super_stockist_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' AND customer_id != '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
    }
    else if ($_SESSION[SITE_SESS.'_ADMIN_TYPE'] == 2) //distributor
    {
        $customer_where .= " AND dealer_distributor_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
        $order_where .= " AND dealer_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' AND customer_id != '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
    }
    else
    {
        $order_where .= " AND customer_id ='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
        $customer_where .= "";
    }

    //chain wise customer lower chain count
    $login_lower_chain_count = $db->rp_getTotalRecord("executive",$customer_where);

    //Total Approved Order Login Customer
    $total_order_count = $db->rp_getTotalRecord("orders","isDelete=0 AND isActive=1 AND status=1 AND customer_id= '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ",0);

    //Total Approved Order lower chain
    $total_order_count_chain_wise = $db->rp_getTotalRecord("orders",$order_where);
?>
<style type="text/css">
.dashboard-stat .visual{
    width: 0!important;
    text-align: center!important;
    float: unset!important;
}
.card {
  height: 135px;
  width: 300px;
  background-color: #ec268f;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  overflow: hidden;
}

.header {
  background-color: #ec268f;
  color: #000000;
  padding: 20px;
  text-align: right;
  font-size: 23px;
  font-weight: 700;
}

.body {
    padding: 20px;
    font-size: 23px;
    font-weight: 600;
    text-align: center;
}

</style>
<div class="col-md-12 col-sm-12 text-center" style="margin-top: 20%;">
    <h1 class="text-danger" ><strong>The Dashboard is under development.</strong></h1>
    <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 " style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a>
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58;"><b><?php echo "Chain Wise Customer"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $login_lower_chain_count?>
                </div>
            </a>
        </div>
    </div> -->
    <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 " style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a href="executive_manage.php?customer_type=3" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58;"><b><?php echo "My Order"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $total_order_count;?>
                </div>
            </a>
        </div>
    </div> -->
    <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12" style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a href="executive_manage.php?customer_type=2" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58; padding-left: 10px;"><b><?php echo "Chain Wise Approved Order"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $total_order_count_chain_wise;?>
                </div>
            </a>
        </div>
    </div> -->
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12" style="background: none !important;">
        <div class="card">
          <div class="header">Chain Wise Customer</div>
          <div class="body">
           <?= $login_lower_chain_count?>
        </div>
          </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12" style="background: none !important;">
        <div class="card" style="cursor: pointer;">
          <div class="header">My Order</div>
          <div class="body">
           <?= $total_order_count;?>
          </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12" style="background: none !important;">
        <div class="card" onclick="location.href='test.php'" style="cursor: pointer;">
          <div class="header">Chain Wise Order</div>
          <div class="body">
            <?= $total_order_count_chain_wise;?>
          </div>
        </div>
    </div>
</div>
