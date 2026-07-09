<?php
    $type= $db->rp_getValue("executive","type_of_executive","isDelete=0 AND isActive=1",0)
    ?> 


<!-- prospect details End from here -->

<?php
// if($db->checkUserPermission(593,$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
// {
?>
<div class="col-md-6 col-sm-6">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 " style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a href="dealer_orders_manage.php?type=<?= $type ?>customer_id=<?=$_SESSION[SITE_SESS.'REFERANCE_ID'];?>&chain=1&customer_panel=1" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58;"><b><?php echo "My Chain Orders"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $db->rp_getTotalRecord("orders","type_of_executive='".$type."' AND isDelete=0 AND isActive=1");?>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 " style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
             <a href="dealer_orders_manage.php?type=<?= $type ?>customer_id=<?=$_SESSION[SITE_SESS.'REFERANCE_ID'];?>&chain=0&customer_panel=1" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58;"><b><?php echo "My Own Orders"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $db->rp_getTotalRecord("orders","type_of_executive=3 AND isDelete=0 AND isActive=1 AND customer_flag=0");?>
                </div>
            </a>
        </div>
    </div>
  <!--   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a href="executive_manage.php?customer_type=2" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58; padding-left: 10px;"><b><?php echo "Total Distributor"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $db->rp_getTotalRecord("executive","type_of_executive=2 AND isDelete=0 AND isActive=1 AND customer_flag=0");?>
                </div>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="    background: none !important;">
        <div class="dashboard-stat" style="background-color:#ec268f;">
            <a href="deep_freezer_scheme_manage.php" target="_blank">
                <div class="details">          
                    <div class="desc">
                        <h3 style="margin-top: 13px;color: black; font-size-adjust: 0.58;"><b><?php echo "Total Freeze Booked"; ?></b></h3>
                    </div>
                </div>
                <br><br>
                <div class="visual" style="color: black;font-size-adjust: 0.58;">
                    <?= $db->rp_getTotalRecord("freezer_scheme","isDelete=0 AND isActive=1",0)?>
                </div>
            </a>
        </div>
    </div> -->
    <div class="col-lg-12 col-md-4 col-sm-4 col-xs-12" id="CustomerType"></div>
</div>
<?php
// }
?>










