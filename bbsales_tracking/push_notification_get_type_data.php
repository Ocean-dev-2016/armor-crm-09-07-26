<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
$type 		= $_REQUEST["type"]; 
if($type==2)
{   
    $ctable_r = $db->rp_getData("sales_executive","id,name","isDelete=0 AND isActive=1","",0);
    if($ctable_r)
    {   
        ?>
        <option value="">Select Sales Person</option>
        <?php
    	while($ctable_d = mysqli_fetch_array($ctable_r))
        {
	       ?>
            <option value="<?php echo $ctable_d['id']; ?>"><?php echo $ctable_d['name']; ?></option>
            <?php
    	}
    }
    else
    {
        ?>
        <option value="">Select Sales Person</option>
        <?php
    }
}
else
{
    $ctable_r = $db->rp_getData("executive","company_name,phone,id","isDelete=0 AND isActive=1","",0);
    if($ctable_r)
    {
        ?>
        <option value="">Select Customer</option>
        <?php
        while($ctable_d = mysqli_fetch_array($ctable_r))
        {
            ?>
            <option value="<?php echo $ctable_d['id']; ?>"><?php echo $ctable_d['company_name']." (".$ctable_d['phone'].")"; ?></option>
            <?php
        }
    }
    else
    {
        ?>
        <option value="">Select Customer</option>
        <?php
    }
}
?>