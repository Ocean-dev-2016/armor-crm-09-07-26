<?php
include("connect.php");
   
$cmp_ids=$db->rp_getData("executive","*","isDelete=0","",0);
$count = 0;
while($cmp_ids_d=mysqli_fetch_assoc($cmp_ids))
{
	$last_account_no=$db->getlastInsertId("account");  
  	$last_account_no=str_pad($last_account_no+1, 4, 0, STR_PAD_LEFT); 
    $check = $db->rp_getTotalRecord("account","cid='".$cmp_ids_d['id']."'");
    if($check==0)
    {
        $count++;
        $rows=array(
                "account_name",
                "cid",
                "acc_no",
            );
        $values=array(
                $cmp_ids_d['company_name'],
                $cmp_ids_d['id'],
                $last_account_no,
            );
        
        $account_info_insert=$db->rp_insert("account",$values,$rows,0);
    }     
}
echo $count ; 
?>