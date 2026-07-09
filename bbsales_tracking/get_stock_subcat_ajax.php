<?php
    include("connect.php");

    if(!empty($_POST["id"]))
    {
        $catR =$db->rp_getData("category_master","id,name","tcid='".$_POST["id"]."'","",0,"");
?>
        <option value="">Select Sub Category</option>
<?php
        while ($catD = mysqli_fetch_assoc($catR))
        {
?>
            <option value="<?=$catD['id'];?>"><?=$catD['name'];?></option>
<?php        
        }
    }
     require_once "disconnect.php";
?>