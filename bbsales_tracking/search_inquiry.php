<?php 
$api=isset($_REQUEST['api'])?$_REQUEST['api']:"";
if($api==1)
{
	include('../service/connect.php');
}
else
{
	$page_id=400;$page_slug='dashboard';
	include("connect.php");
}
$query = $_REQUEST['term'];
$flag = $_REQUEST['flag'];
if($flag=="mobile")
{
	$where = " phone LIKE '".$query."%' OR mobile_no1 LIKE '".$query."%' OR whatsapp_no LIKE '".$query."%' AND isDelete=0 ";
	$GROUP_BY = " ";
	$where1 = " mobile_number LIKE '%".$query."%' AND isDelete=0 ";
	$GROUP_BY1 = " GROUP BY mobile_number ORDER BY id DESC";
}
else if($flag=="customer")
{
	$where = " company_name LIKE '%".$query."%' AND isDelete=0 ";
	$GROUP_BY = " ";
	$where1 = " company_name LIKE '%".$query."%' AND isDelete=0 ";
	$GROUP_BY1 = " GROUP BY company_name ORDER BY id DESC";
}
$data=$db->rp_getData("executive","*",$where.$GROUP_BY,"",0);
if(mysqli_num_rows($data)==0)
{
	$data=$db->rp_getData("no_order_inquiry","*",$where1.$GROUP_BY1,"",0);
}
if($data)
{
	$status_array = array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working","-2"=>"Cancel","4"=>"Hot","5"=>"Cold","6"=>"Warm","7"=>"Wrong Call","8"=>"Will Interested");
	$result= array();
	while($data_r = mysqli_fetch_assoc($data))
	{
		$phone = "";
		if(isset($data_r['phone']) && $data_r['phone']!="")
		{
			if($_REQUEST['flag']=="mobile")
			{
				$phone = $data_r['phone'];
			}
			else
			{
				$phone = $data_r['company_name'];
			}
		}
		if($phone=="")
		{
			if($_REQUEST['flag']=="mobile")
			{
				$phone = $data_r['mobile_number'];
			}
			else
			{
				$phone = $data_r['company_name'];
			}
		}

		$data_r['value'] = $phone;
		//$data_r['phone'] = $phone;
		if(isset($data_r['person_name']))
		{
			$data_r['cname'] = $data_r['person_name'];
		}
		if(isset($data_r['other_mobile_no']))
		{
			$data_r['whatsapp_no'] = $data_r['other_mobile_no'];
		}
		if(isset($data_r['mobile_number']))
		{
			$data_r['phone'] = $data_r['mobile_number'];
		}
		if(isset($data_r['inquiry_type']))
		{
			$data_r['type_of_executive'] = $data_r['inquiry_type'];
		}
		if(isset($data_r['email']))
		{
		 	$data_r['email_address'] = $data_r['email'];
		}if(isset($data_r['gst_no']))
		{
		 	$data_r['gst'] = $data_r['gst_no'];
		}
		if(isset($data_r['designation']))
		{
			$data_r['designation'] = $data_r['designation'];
		}else{
			$data_r['designation'] = "";
		}
		if(isset($data_r['zone']))
		{
			$data_r['zone'] = $data_r['zone'];
		}else{
			$data_r['zone'] = "";
		}
		$data_r['inq_date'] = date('d-m-Y',strtotime($data_r['inquiry_date']));
		$data_r['inq_status'] = $status_array[$data_r['status']];
		$data_r['inq_created_by'] = $db->rp_getValue("sales_executive","name","id='".$data_r['inquiry_created_by']."' AND isDelete=0");
		$data_r['inquiry_assign_to'] = $db->rp_getValue("sales_executive","name","id='".$data_r['inquiry_assign_to']."' AND isDelete=0");
		
		$data_r['country_id']=$db->rp_getValue("country","id","name='".$data_r['country']."' AND isDelete=0");
		$data_r['state_id']=$db->rp_getValue("class","id","name='".$data_r['state']."' AND isDelete=0");
		$data_r['main_city_id']=$db->rp_getValue("city","id","name='".$data_r['main_city']."' AND isDelete=0");
		$data_r['city_id']=$db->rp_getValue("area","id","name='".$data_r['city']."' AND isDelete=0");
		$data_r['inq_no'] = "#".$data_r['id'];
		$result[] = $data_r;
	}
	if($api==1)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Customer Found!!","developer_msg"=>"Customer Found!!","result"=>$result);
		$db->printJSON($ack);
	}
	else
	{
		echo json_encode($result);	
	}
}
?>