<?php
    include("connect.php");
    // print_r($_REQUEST);exit;
    if(!empty($_POST["sub_cat_id"]) && $_POST["sub_cat_id"]!=0)
    {
        // $catR =$db->rp_getData("category_master","id,name","tcid='".$_POST["id"]."'","",0,"");
        $product_data_r= $db->rp_getData("product","id,name","cid IN(".$_POST
        ["sub_cat_id"].")","",0);

?>
        <option value="">Item Name</option>
<?php
        while ($product_data_d = mysqli_fetch_assoc($product_data_r))
        {
                $product_weight_r = $db->rp_getData("product_weight_price","weight_id,catno","product_id='".$product_data_d['id']."' AND isDelete=0","",0);

                while ($product_weightD = mysqli_fetch_assoc($product_weight_r))
                {
                        $weight_name = $db->rp_getValue("weight","name","id='".$product_weightD['weight_id']."' AND isDelete=0");
?>
                        <option value="<?=$product_data_d['name']?>" >
                                <?=$product_data_d['name']." - ".$weight_name." - ".$product_weightD['catno']?>
                                        
                        </option>
<?php        
                }
        }
    }
require_once "disconnect.php";
?>