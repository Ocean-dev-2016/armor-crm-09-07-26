<?php
$api=isset($_REQUEST['api'])?$_REQUEST['api']:"";
if($api==1)
{
	include('../service/connect.php');
	$mobile_number = trim($_REQUEST["mobile_number"]);
    $mobile_or_name = $_REQUEST["mobile_or_name"];
}
else
{
	$page_id=400;$page_slug='dashboard';
	include("connect.php");
	$mobile_number = trim($_POST["mobile_number"]);
	$mobile_or_name = $_POST["mobile_or_name"];
}

$type = $_POST["type"];

// print_r($_POST);exit;
// print_r($_REQUEST);exit;
$customer = array();
$GROUP_BY = "";
$GROUP_BY1 = "";
if($mobile_or_name==1)
{
	$where = " phone = '".$mobile_number."' AND isDelete=0 ";
	$GROUP_BY = " GROUP BY phone";
	$where1 = " mobile_number = '".$mobile_number."' AND isDelete=0 ";
	$GROUP_BY1 = " GROUP BY mobile_number";
}
else if($mobile_or_name==2)
{
	$where = " company_name = '".$mobile_number."' AND isDelete=0 ";
	$GROUP_BY = " GROUP BY company_name";
	$where1 = " company_name = '".$mobile_number."' AND isDelete=0 ";
	$GROUP_BY1 = " GROUP BY company_name";
}
/*$where = "phone LIKE '%".$mobile_number."%' OR company_name LIKE '%".$mobile_number."%' AND isDelete=0";

$where1 = "mobile_number LIKE '%".$mobile_number."%' OR company_name LIKE '%".$mobile_number."%' AND isDelete=0";*/

$customer_r = $db->rp_getData("executive","*",$where.$GROUP_BY,"",0);
if(mysqli_num_rows($customer_r)==0)
{
	$customer_r = $db->rp_getData("no_order_inquiry","*",$where1.$GROUP_BY1,"",0);
}

if(mysqli_num_rows($customer_r)>0)
{
    $customer = mysqli_fetch_assoc($customer_r);
}

$customer['mobile_number'] = (isset($customer['mobile_no1']) && $customer['mobile_no1']!="")?$customer['mobile_no1']:$customer['mobile_number'];
$customer['gst_no'] = (isset($customer['gst_no']) && $customer['gst_no']!="")?$customer['gst_no']:$customer['gst'];
$customer['executive_type'] = (isset($customer['executive_type']) && $customer['executive_type']!="")?$customer['executive_type']:$customer['type_of_executive'];
$customer['email'] = (isset($customer['email_address']) && $customer['email_address']!="")?$customer['email_address']:$customer['email'];
$customer['other_mobile_no'] = (isset($customer['whatsapp_no']) && $customer['whatsapp_no']!="")?$customer['whatsapp_no']:$customer['other_mobile_no'];
$customer['cname'] = (isset($customer['person_name']) && $customer['person_name']!="")?$customer['person_name']:$customer['cname'];

if($type=="-1")
{
    $where1 .= " AND inquiry_lead_flag = '-1' ";
}
else if($type=="0")
{
    $where1 .= " AND inquiry_lead_flag = '0' ";
}
else
{
    $where1 .= " AND inquiry_lead_flag = '1' ";
}
$past_data = "";
$inqDataR = $db->rp_getData("no_order_inquiry","*,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `no_order_inquiry`.`sales_executive_id` ) AS sales_executive_id_a,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `no_order_inquiry`.`inquiry_assign_to` ) AS inquiry_assign_to",$where1,"",0);
$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business");
if(mysqli_num_rows($inqDataR)>0)
{
	while ( $inqData = mysqli_fetch_assoc($inqDataR))
	{
		$inqData['inquiry_date'] = ($inqData['inquiry_date']!="0000-00-00" && $inqData['inquiry_date']!="1970-01-01")?date("d-m-Y",strtotime($inqData['inquiry_date'])):"";

		$past_data1 = array();
		$past_data1[] = "<tr>";
		$past_data1[] = "<td>"."#INQ/".$inqData['id']."</td>";
		$past_data1[] = "<td>".$inqData['person_name']."</td>";
		$past_data1[] = "<td>".$inqData['company_name']."</td>";
		$past_data1[] = "<td>".$inqData['mobile_number']."</td>";
		$past_data1[] = "<td>".$inqData['inquiry_date']."</td>";
		$past_data1[] = "<td>".$inqData['other_mobile_no']."</td>";
		$past_data1[] = "<td>".$inqData['email_address']."</td>";
		$past_data1[] = "<td>".$status_array[$inqData['status']]."</td>";
		$past_data1[] = "<td>".$inqData['sales_executive_id_a']."</td>";
		$past_data1[] = "<td>".$inqData['inquiry_assign_to']."</td>";
		$past_data1[] = "</tr>";
		$past_data .= implode("", $past_data1);

	}
}
else
{
	$past_data = "<tr><td colspan='8' class='text-center'>No Data Found!!</td></tr>";
}
$customer['past_data'] = $past_data;
if($api==1)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Customer Found!!","developer_msg"=>"Customer Found!!","result"=>$customer);
		$db->printJSON($ack);
	}
	else
	{
		echo json_encode($customer);
	}
	require_once 'disconnect.php'; 
?>