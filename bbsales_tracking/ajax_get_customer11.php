<?php
$page_id=631;$page_slug='route_page';
require_once("connect.php");
$customer_type 		= $_POST["customer_type"];
$customer_id      = $_POST["customer_id"];
// echo $customer_id;exit();
// $selected_value      = $_POST["selected_value"];

$where .= "isDelete=0 AND type_of_executive = '$customer_type'";


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
  if($rights['personal_flag']==1)
  {
    $where .=" AND seid='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
  }
  else
  { 
    if($rights['chain_vise_flag'] == 1)
    {
      $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
      $get_sales_typer=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
        if ($get_sales_typer== "sales_manager") 
        {
            $sales_executive_type = "Regional Sales Manager";
            $key="sm_id";
            $WhereConditionr.=' ' .$key.'='.$check_id;
        }
        else if ($get_sales_typer == "area_sales_manager") 
        {
            $sales_executive_type = "National Sales Manager";//Business Development Manager
            $key="asm_id";
            $WhereConditionr.=' ' .$key.'='.$check_id;
        }
        else if ($get_sales_typer == "sales_officer") 
        {
            $sales_executive_type = "Area Sales Manager";//Area Sales Manager
            $key="so_id";
            $WhereConditionr.=' ' .$key.'='.$check_id;
        }
        else if ($get_sales_typer == "sales_executive") 
        {
            $sales_executive_type = "Sales Officer";
            $key="se_id";
            $WhereConditionr.=' ' .$key.'='.$check_id;
        }
        else
        {
          $WhereConditionr.=' type = "service_engineer"';
        }

        $data_r = $db->rp_getData("sales_executive","id",$WhereConditionr,"",0);

        $SALEID2=array();
      if($data_r)
      {
        while($data_dd=mysqli_fetch_assoc($data_r))
        {
          $SALEID2[]=$data_dd['id'];
        }
      }
      if(!empty($SALEID2))
      {
        $SALEID2=implode(",", $SALEID2);
        $where .= " AND seid IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") "; 
      }
      else
      {
        $where .= " AND seid IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") ";    
      }
    }
    else
    {
      // $where .= " isDelete=0 ";
    }
  }
}
$customer_r = $db->rp_getData("executive","*",$where,"",0);
if($customer_r>0)
{

	?>
    <option value="">Select Customer</option>
    <?php
    while($customer_d = mysqli_fetch_array($customer_r))
    {
      $total_records++;
        // print_r($customer_d);exit();
        
        

       
        // $customer_type1=$db->rp_getValue("customer_type","name","id='".$customer_d['type_of_executive']."'");
        // echo "ggkk";exit();
       ?>
        <option <?php if($customer_d['id']==$customer_id){ echo "selected"; }  ?> value="<?php echo $customer_d['id']; ?>"       ><?php echo $customer_d['company_name']." - ".$customer_d['cname']. " - ". $customer_d['phone']. " - ". $customer_d['main_city']. " - ". $customer_d['city']; ?></option>

        <?php
        // echo "ggkk";exit();
    }
}
else
{
	?>
    <option value="">Select Customer</option>
    <?php
}
?>
<?php require_once 'disconnect.php';  ?>