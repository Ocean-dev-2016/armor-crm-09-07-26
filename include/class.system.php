<?php
class System extends Functions
{
	public $db;
	public $user_id;
	public $notifications;
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;	
		//$this->notifications=(isset($_SESSION[SESS_NOTIFICATION]))?$_SESSION[SESS_NOTIFICATION]:array();
		$this->user_id=isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID'])?$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']:"";		
		$notification_type=array("1"=>"Expense Message","2"=>"Admin Message");
	} 
	public function setNotification($notification_id,$user_id,$notification_title,$notification_type,$type_slug,$notification_description,$notification_icon="fa fa-notification",$notification_extra="",$respective_date,$referance_id,$referance_type,$user_type)
	{
		$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
			"user_id",
			"notification_title",
			"notification_type",
			"type_slug",
			"notification_description",
			"notification_icon",
			"notification_extra",
			"respective_date",
			"isDelete",
			"isActive",
			"referance_id",
			"referance_type",
			"user_type",
		);

		$values = array(
			$user_id,
			$notification_title,		
			$notification_type,		
			$type_slug,		
			$notification_description,		
			$notification_icon,
			$notification_extra,						
			$respective_date,
			0,
			1,
			$referance_id,
			$referance_type,
			$user_type,
		);
		$uid = $this->db->rp_insert("notification",$values,$rows,0);
		return $uid;
	}

	public function getNotification($notification_id)
	{
		if(array_key_exists($notification_id,$this->notifications))
		{
			return $this->notifications[$notification_id];
		}
		else
		{
			return false;
		}
	}

	public function getQuickNotifications()
	{

		$where = "isDelete=0  AND isActive=1";
		$ctable_r = $this->db->rp_getData("notification","*",$where,"",0);
		$result=array();
		if($ctable_r){
			while($ctable_d = mysqli_fetch_assoc($ctable_r)){
				$result[]=$ctable_d;
			}
		}
		if(!empty($result))
		{
			return $result;
		}
		else
		{
			return false;
		}
	}

	public function getNotifications($user_id="",$user_type='')
	{
		$limit=$this->getLimit();
		if($user_id!="")$this->user_id=$user_id;
		if($user_id!=""){
			$where = " user_id='".$this->user_id."' AND user_type!='customer' AND isDelete=0";
		}
		else{
			$where = " isDelete=0";
		}

		$ctable_r = $this->db->rp_getData("notification","id,user_id,notification_title,notification_description,notification_extra,notification_type,type_slug,respective_date,modified_date,created_date",$where,"id DESC",0,$limit);
		if($ctable_r)
		{
			$result=array();
			while($ctable_d = mysqli_fetch_assoc($ctable_r)){
                if($ctable_d['respective_date']!="0000-00-00 00:00:00")
                {
                    $ctable_d['respective_date']=date('d-m-Y',strtotime($ctable_d['respective_date']));    
                }
                else
                {
                    $ctable_d['respective_date']="";
                }
				
                if($ctable_d['modified_date']!="0000-00-00 00:00:00")
                {
                    $ctable_d['modified_date']=date('d-m-Y',strtotime($ctable_d['modified_date']));
                }
                else
                {
                    $ctable_d['modified_date']="";
                }
				
                if($ctable_d['created_date']!="0000-00-00 00:00:00")
                {
                    $ctable_d['created_date']=date('d-m-Y',strtotime($ctable_d['created_date']));
                }
                else
                {
                    $ctable_d['created_date']="";    
                }
                $result[]=$ctable_d;
            }
        }
        //print_r($result);
        if(!empty($result))
        {
        	return $result;
        }
        else
        {
        	return false;
        }
    }

	public function deleteNotifications($notification_id)
	{
		$isDeleted=$this->db->rp_delete("notification","id='".$notification_id."'",0);
		if(!empty($isDeleted))
		{
			return $isDeleted;
		}
		else
		{
			return false;
		}
	}

	function fetchState($country_id)
    {
        $country_id=$this->db->rp_getValue("country","id","name='".$country_id."'");
        if($country_id!="") 
        {
            $state_r = $this->db->rp_getData("state","*","country_id = '".$country_id."'","",0);
            ?>
            <option value="">Select State</option>
            <?php
            while($state_d = mysqli_fetch_array($state_r))
            {
            ?>
                <option value="<?php echo $state_d['name']; ?>" <?php if($state_d['name']==$state){?> selected <?php } ?>><?php echo $state_d['name']; ?></option>
            <?php
            }
        }
        else
        {
            echo "<option value=''>Select first Country</option>";
        }
    }
    function fetchCity($state_id)
    {
    	if($state_id!="")
    	{
    		$state_id=$this->db->rp_getValue("state","id","name='".$state_id."'");
    		$city_r = $this->db->rp_getData("city","*","state_id = '".$state_id."'","",0);
    	?>
    		<option value="">Select City</option>
	    	<?php
	    	while($city_d = mysqli_fetch_assoc($city_r))
	    	{
	    	?>
	    		<option value="<?php echo $city_d['name']; ?>" <?php if($city_d['name']==$city){?>selected <?php }?>><?php echo $city_d['name']; ?></option>
	    	<?php
	    	}
    	}
    	else
    	{
    		?>
    		<option value="">Select City</option>
    	<?php
    	}
    }
      function getAllStateDetail ($required_columns=array())
    {
    	$required_columns=$this->getRequiredColumns($required_columns);
    	if($_REQUEST['country_id']!="")
    	{
    		$result1=$this->db->rp_getData("class",$required_columns,"country_id='".$_REQUEST['country_id']."' AND isDelete=0","",0);
    	}
    	else
    	{
    		$result1=$this->db->rp_getData("class",$required_columns,"isDelete=0","",0);
    	}
    	if($result1)
    	{    		
	    	while($detail=mysqli_fetch_assoc($result1))
	    	{
	    		$p[]=$detail;
	    	}
	    	$reply=array("ack"=>1,"developer_msg"=>"State detail found","ack_msg"=>"State detail found.","result"=>$p);
    	}
    	else
    	{
    		$reply=array("ack"=>0,"developer_msg"=>"State detail not found","ack_msg"=>"State detail not found.");
    	}
    	return $reply;
    	//print_r(result);
    }
	 function getAllRoutDetail($required_columns=array())
	    {
	    	$required_columns=$this->getRequiredColumns($required_columns);
	    	if($_REQUEST['city_id']!="")
	    	{
	    		$result=$this->db->rp_getData("area",$required_columns,"city_id='".$_REQUEST['city_id']."' AND isDelete=0","name ASC",0);
	    	}
	    	else
	    	{
	    		$result=$this->db->rp_getData("area",$required_columns,"isDelete=0","name ASC",0);
	    	}
	    	while($detail=mysqli_fetch_assoc($result))
	    	{
	    		$p[]=$detail;
	    	}
	    	$reply=array("ack"=>1,"developer_msg"=>"City detail found","ack_msg"=>"City detail found.","result"=>$p);
	    	return $reply;
	    	//print_r(result);
	    }

     function getAllCityDetail($required_columns=array())
    {
    	$required_columns=$this->getRequiredColumns($required_columns);
    	if($_REQUEST['state_id']!="")
    	{
    		$result=$this->db->rp_getData("city",$required_columns,"state_id='".$_REQUEST['state_id']."' AND isDelete=0","name ASC",0);
    	}
    	else
    	{
    		$result=$this->db->rp_getData("city",$required_columns,"isDelete=0","name ASC",0);
    	}
    	while($detail=mysqli_fetch_assoc($result))
    	{
    		$p[]=$detail;
    	}
    	$reply=array("ack"=>1,"developer_msg"=>"City detail found","ack_msg"=>"City detail found.","result"=>$p);
    	return $reply;
    	//print_r(result);
    }
	function getAllClassDetailCustomer($required_columns=array())
	{

		$customer_id=(isset($_REQUEST['customer_id']))?$_REQUEST['customer_id']:"";
		if($customer_id!="")
		{
				$class_ids=array();
				$class_d=$this->db->rp_getData("executive_map_area","class_id","executive_id='".$customer_id."' GROUP BY class_id","id ASC ",0);
				if($class_d)
				{
					while($class=mysqli_fetch_assoc($class_d))
					{
						$class_ids[]=$class['class_id'];
					}
				}
				else
				{
					$class_ids[]=0;
				}
				$class_ids=implode(",",$class_ids); 
				$where="id IN(".$class_ids.") AND isDelete=0";			
		}
		else
		{
			$where="isDelete=0";
		}
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("class",$required_columns,$where,"",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			$p[]=$detail;
		}
		$reply=array("ack"=>1,"developer_msg"=>"Class detail found","ack_msg"=>"Class detail found.","result"=>$p);
		return $reply;
		//print_r(result);
	}
	function getAllClassDetail($required_columns=array())
	{

		$sales_id=(isset($_REQUEST['sales_id']))?$_REQUEST['sales_id']:"";
		if($sales_id!="")
		{
			$sales_type = $this->db->rp_getValue("sales_executive","type","id='".$sales_id."' AND isDelete=0",0);
			if($sales_type!='service_executive')
			{
				$class_ids=array();
				$class_d=$this->db->rp_getData("sales_executive_map_area","class_id","sales_executive_id='".$sales_id."' GROUP BY class_id","id ASC ",0);
				if($class_d)
				{
					while($class=mysqli_fetch_assoc($class_d))
					{
						$class_ids[]=$class['class_id'];
					}
				}
				else
				{
					$class_ids[]=0;
				}
				$class_ids=implode(",",$class_ids); 
				$where="id IN(".$class_ids.") AND isDelete=0";
			}
			else
			{
				$where="isDelete=0";
			}
		}
		else
		{
			$where="isDelete=0";
		}
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("class",$required_columns,$where,"",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			$p[]=$detail;
		}
		$reply=array("ack"=>1,"developer_msg"=>"Class detail found","ack_msg"=>"Class detail found.","result"=>$p);
		return $reply;
		//print_r(result);
	}

	function getAllAreaDetail($required_columns=array())
	{
		$sales_id=(isset($_REQUEST['sales_id']))?$_REQUEST['sales_id']:"";
		if($sales_id!="")
		{
			$area_ids=array();
			$area_d=$this->db->rp_getData("sales_executive_map_area","area_id","sales_executive_id='".$sales_id."'");
			if($area_d)
			{
				while($area=mysqli_fetch_assoc($area_d))
				{
					$area_ids[]=$area['area_id'];
				}
			}
			else
			{
				$area_ids[]=0;
			}
			$area_ids=implode(",",$area_ids); 
			$where="id IN(".$area_ids.") AND isDelete=0";
		}
		else
		{
			$where="isDelete=0";
		}
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("area",$required_columns,$where,"",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			$p[]=$detail;
		}
		$reply=array("ack"=>1,"developer_msg"=>"Area detail found","ack_msg"=>"Area detail found.","result"=>$p);
		return $reply;
		//print_r(result);
	}

	function getCountry($required_columns=array())
	{

		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("country",$required_columns,"","",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			$p[]=$detail;
		}
		$reply=array("ack"=>1,"developer_msg"=>"Country detail found","ack_msg"=>"Country detail found.","result"=>$p);
		return $reply;
		//print_r(result);
	}

	function getTopCategory($required_columns=array())
	{
		$sales_id=(isset($_REQUEST['sales_id']))?$_REQUEST['sales_id']:"";
		$customer_id=(isset($_REQUEST['customer_id']))?$_REQUEST['customer_id']:"";
		if($sales_id != "" && $sales_id!=0)
		{
			$get_top_ids=$this->db->rp_getValue("sales_executive","top_category_id","isDelete=0 AND id='".$sales_id."'");
			$sales_where.=" AND id IN(".$get_top_ids.")";
		}else if($customer_id != "" && $customer_id!=0)
		{
			$get_top_ids=$this->db->rp_getValue("executive","top_category_id","isDelete=0 AND id='".$customer_id."'");
			$sales_where.=" AND id IN(".$get_top_ids.")";
		}
		else
		{
			$sales_where.="";
		}
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("top_category_master",$required_columns,"isDelete=0 AND isActive=1".$sales_where,"display_order ASC",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			if ($detail['image_path']!="") {
				$detail['image_path']= SITEURL.TOP_CATEGORY.$detail['image_path'];
			}
			else
			{
				$detail['image_path'] = "";	
			}
			$p[]=$detail;
		}
		if(!empty($result))
		{
			$reply=array("ack"=>1,"developer_msg"=>"Top Category detail found","ack_msg"=>"Top Category detail found.","result"=>$p);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Top Category detail Not found","ack_msg"=>"Top Category detail Not found.");
			return $reply;
		}
		//print_r(result);
	}

	function getCategory($required_columns=array())
	{

		$tcid=(isset($_REQUEST['tcid']))?$_REQUEST['tcid']:"";
		$sales_id=(isset($_REQUEST['sales_id']))?$_REQUEST['sales_id']:"";
		$customer_id=(isset($_REQUEST['customer_id']))?$_REQUEST['customer_id']:"";
		if($tcid!="")
		{
			$Where = "tcid='".$tcid."' AND isDelete=0 AND isActive=1 ";
		}
		else
		{
			$Where = "isDelete=0 AND isActive=1 ";
		}

		/*if($sales_id != "" && $sales_id!=0)
		{
			$get_top_ids=$this->db->rp_getValue("sales_executive","category_id","isDelete=0 AND id='".$sales_id."'");
			$sales_where.=" AND id IN(".$get_top_ids.")";
		}else if($customer_id != "" && $customer_id!=0)
		{
			$get_top_ids=$this->db->rp_getValue("executive","category_id","isDelete=0 AND id='".$customer_id."'",0);
			$sales_where.=" AND id IN(".$get_top_ids.")";
		}
		else
		{
			$sales_where.="";
		}*/
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("category_master",$required_columns,$Where/*.$sales_where*/,"display_order ASC",0);
		while($detail=mysqli_fetch_assoc($result))
		{
			$detail['top_cat_name']=$this->db->rp_getValue("top_category_master","name","isDelete=0 AND id='".$detail['tcid']."'");

			if ($detail['image_path']!="") {
				$detail['image_path']= SITEURL.CATEGORY.$detail['image_path'];
			}
			else
			{
				$detail['image_path'] = "";	
			}	
			$p[]=$detail;
		}
		if(!empty($p)){
			$reply=array("ack"=>1,"developer_msg"=>"Category detail found","ack_msg"=>"Category detail found.","result"=>$p);
		}else{
			$reply=array("ack"=>0,"developer_msg"=>"Category detail not found","ack_msg"=>"No data found.");

		}
        return $reply;
    }

	function getRequiredColumns($required_columns=array())
	{
		if(!empty($required_columns))
		{
			$required_columns_string=implode(",",$required_columns);
			return $required_columns_string;
		}
		else
		{
			return "*";
		}
	}

	function getLimit($limit=array())
	{
		$limit=$this->db->getLimit();	
		if(!empty($limit) && array_key_exists("ul",$limit))
		{
			$ul=$limit['ul'];
			if(array_key_exists("ll",$limit) && $limit['ll']!="")
			{
				$ll=$limit['ll'];
			}
			else
			{
				$ll="18446744073709551615";
			}			
			$limit_string="".$ul.",".$ll;
			return $limit_string;
		}
		else
		{
			return "";
		}
	}

	function getUpdateInfo($last_sync_time)
	{
		$table_code=array();
		$table_slug=array();
		
		if($last_sync_time!="")
		{
			$last_sync_time=date("Y-m-d H:i:s",strtotime($last_sync_time));
			$res=$this->db->rp_getData(CTABLE_INFORMATION_SCHEMA,"*","last_modify_date>='".$last_sync_time."'","",0,"");
			if($res){
				while($r=mysqli_fetch_assoc($res))
				{
					$table_code[]=$r['table_code'];
					$table_slug[]=$r['table_slug'];
				}
			}
		}
		if(!empty($table_code))
		{
			$reply=array("ack"=>1,"result"=>$table_code,"table_name"=>$table_slug,"developer_msg"=>"Here is Your Update List","ack_msg"=>"Great !! Update List Found!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No Updates Found!!","ack_msg"=>"No Update Found!!");
			return $reply;
		}
	}

	function getNoOrderAction($required_columns=array())
	{
		$required_columns=$this->getRequiredColumns($required_columns);
		$result=$this->db->rp_getData("no_order_inquiry_action",$required_columns,"","",0);
		if($result){
			while($detail=mysqli_fetch_assoc($result))
			{
				$p[]=$detail;
			}
			$reply=array("ack"=>1,"developer_msg"=>"Action detail found","ack_msg"=>"Action detail found.","result"=>$p);
			return $reply;
		}else{
			$reply=array("ack"=>0,"developer_msg"=>"No Action detail found","ack_msg"=>"No Action detail found.");
			return $reply;
		}
	}

    function getUpdates($table_code,$user_id,$last_sync_date)
    {
    	$result=array();
    	if($last_sync_date!="")
    	{
    		$last_sync_date=date("Y-m-d H:i:s",strtotime($last_sync_date));
    		$res=$this->db->rp_getData(CTABLE_INFORMATION_SCHEMA,"*","table_code='".$table_code."'","",0,"");
    		if($res)
    		{
    			$r=mysqli_fetch_assoc($res);
    			$table_slug=$r['table_slug'];
    			$result=array();
    			if($table_slug=="orders")
    			{
    				$p=new Product();
    				$result_r=$this->db->rp_getData($table_slug,"*","(created_date>='".$last_sync_date."' OR modify_date>='".$last_sync_date."') AND sales_id='".$user_id."'","",0);
    				if($result_r)
    				{
    					while($r=mysqli_fetch_assoc($result_r))
    					{
    						$order_ack=$p->getOrders_forItem($r['id']);
    						if($order_ack['ack']==1)
    						{
    							$r=$order_ack['result'];
    							$r['server_id']=$r['id'];
    							$result[]=$r;
    						}
    					}
    				}
    			}
    			else if($table_slug=="product")
    			{
    				$result_r=$this->db->rp_getData($table_slug,"*","(created_date>='".$last_sync_date."' OR modify_date>='".$last_sync_date."') AND isDelete=0","",0);
    				if($result_r)
    				{
    					while($r=mysqli_fetch_assoc($result_r))
    					{
    						$result[]=$r;
    					}
    				}
    			}
    			else if($table_slug=="product_weight_price")
    			{
    				$result_r=$this->db->rp_getData($table_slug,"*","(created_date>='".$last_sync_date."' OR modify_date>='".$last_sync_date."') AND isDelete=0","",0);
    				if($result_r)
    				{
    					while($r=mysqli_fetch_assoc($result_r))
    					{
    						$result[]=$r;
    					}
    				}
    			}
    			else if($table_slug=="no_order_inquiry")
    			{
    				$result=array();
    				$result_r=$this->db->rp_getData($table_slug,"*","(created_date>='".$last_sync_date."' OR modify_date>='".$last_sync_date."') AND isDelete=0 AND sales_executive_id='".$user_id."'","",0);
    				if($result_r)
    				{
    					while($r=mysqli_fetch_assoc($result_r))
    					{
    						$r['sales_name'] = $this->db->rp_getValue("sales_executive","name","id='".$r['sales_id']."' AND type='".$r['sales_type']."'",0);

    						$r['country_slug'] = $r['country'];
    						$r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);
    						$r['state_slug'] = $r['state'];		
    						$r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);
    						$r['city_slug'] =  $r['city'];
    						$r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);
    						$r['action_slug'] =  $r['action'];
    						$r['action'] = $this->db->rp_getValue("no_order_inquiry_action","name","id='".$r['action']."'",0);
    						$r['created_date']=date('d-m-Y',strtotime($r['created_date']));
    						$r['datetime']=date('d-m-Y',strtotime($r['datetime']));
    						$result[] = $r; 
    					}
    				}
    			}
    			else if($table_slug=="executive")
    			{
    				$result=array();
    				$customer_id=array();
    				$sales_area_id=array();
    				$sales_area_r=$this->db->rp_getData("sales_executive_map_area","*","sales_executive_id=".$user_id."","",0);
    				if($sales_area_r)
    				{
    					while($sales_area_d=mysqli_fetch_assoc($sales_area_r))
    					{
    						$sales_area_id[]=$sales_area_d['area_id'];
    					}
    					if(!empty($sales_area_id))
    					{
    						$area_ids=implode(",",$sales_area_id);
    						//find area list for outlet and get outlet ids---------------------//
    						$outlet_area_r=$this->db->rp_getData("executive_map_area","*","area_id IN (".$area_ids.")","",0);
    						while($outlet_area_d=mysqli_fetch_assoc($outlet_area_r))
    						{
    							$customer_id[]=$outlet_area_d['executive_id'];
    						}
    					}
    					if(!empty($customer_id))
    					{
    						$ids=implode(",",$customer_id);
    						$result_r=$this->db->rp_getData($table_slug,"*","id IN (".$ids.") AND isDelete=0 AND (created_date>='".$last_sync_date."' OR modify_date>='".$last_sync_date."') ","",0);
    						if($result_r)
    						{
    							while($r=mysqli_fetch_assoc($result_r))
    							{
    								$first_area=$this->db->rp_getData("executive_map_area","area_id","executive_id='".$r['id']."'","id ASC LIMIT 1");
    								if($first_area)
    								{
    									$first_area=mysqli_fetch_assoc($first_area);
    									$first_area=$first_area['area_id'];
    								}
    									$r['area_id'] 		= $first_area;
    									$r['other_contact'] 		= $r['mobile_no1'];
    									//$r['country']=$this->db->rp_getValue("country","name","id=".$r['country']."");
    									//$r['state']=$this->db->rp_getValue("state","name","id=".$r['state']."");
    									//$r['city']=$this->db->rp_getValue("city","name","id=".$r['city']."");
    									///// get all detail of executive_map_area
    									$executive_areas=$this->db->rp_getData("executive_map_area","*","executive_id='".$r['id']."'","id ASC");
    									if($executive_areas)
    									{
    										$area=array();
    										while($executive_area=mysqli_fetch_assoc($executive_areas)){
    											$areas['id']=$executive_area['id'];
    											$areas['class_id']=$executive_area['class_id'];
    											$areas['area_id']=$executive_area['area_id'];
    											$areas['executive_id']=$executive_area['executive_id'];
    											$areas['isDelete']=$executive_area['isDelete'];
    											$areas['isActive']=$executive_area['isActive'];
    											$areas['executive_type']=$executive_area['executive_type'];
    											$area[]=$areas;
    										}
    										$r['area']=$area;
    									}
    									$result[]=$r;
    								}
    							}
    						}
    					}
    				}
    				else
    				{
    					$result_r=$this->db->rp_getData($table_slug,"*","1=1");
    					if($result_r)
    					{
    						while($r=mysqli_fetch_assoc($result_r))
    						{
    							$result[]=$r;
    						}
    					}
    				}
    			}
    		}
    		if(!empty($result))
    		{
    			$reply=array("ack"=>1,"result"=>$result,"developer_msg"=>"Here is Your Updates","ack_msg"=>"Great !! Updates List Found!!","last_sync_date"=>date("Y-m-d H:i:s"));
    			return $reply;
    		}
    		else
    		{
    			$reply=array("ack"=>0,"developer_msg"=>"No Updates Found!!","ack_msg"=>"No Updates Found!!","last_sync_date"=>date("Y-m-d H:i:s"),"result"=>array());
    			return $reply;
    		}
    	}

		/*function addFollowupNotification1(){
			$area_ids=array();
			//$inquiries=$this->db->rp_getData("no_order_inquiry","*","DATE(datetime)='".date("Y-m-d")."'");
			$inquiries=$this->db->rp_getData("followup","*","DATE(followup_date)='".date("Y-m-d")."' AND isDelete
			 	=0 AND isActive=1","",0);
			if($inquiries)
			{
				$count=0;
				while($inquiry=mysqli_fetch_assoc($inquiries))
				{
					$sales_executive_id=$inquiry['user_id'];
					$sales_name=$this->db->rp_getValue("sales_executive","username","id=".$sales_executive_id."");
					$customer_name=$this->db->rp_getValue("executive","company_name","id=".$sales_executive_id."");
				    $mobile_no =$this->db->rp_getValue("executive","phone","id=".$sales_executive_id."");
				    $refreshToken=$this->db->rp_getValue("sales_executive","refreshToken","id=".$sales_executive_id."",0);
				    $refreshTokens[]=$refreshToken;
				    //Notification Message in data
				    $data=array("sales_executive_id"=>$sales_executive_id,"username"=>$sales_name,"type"=>2,"msg"=>"Take followup for <b>inquiry#".$inquiry['id']."</b> of customer <b>".$customer_name."</b>.Mobile Number <b>".$mobile_no."</b>.");
				    $title_description="Take followup for <b>inquiry#".$inquiry['id']."</b> of customer <b>".$customer_name."</b>.Mobile Number <b>".$mobile_no."</b>.";
				    //$notification=$this->setNotification(0,$sales_executive_id,"Followup",2,"Followup Message",$title_description,"","",date("Y-m-d H:i:s"));
					$notification=$this->setNotification(0,$sales_executive_id,"Followup",2,"Followup Message",$title_description,"","",date("Y-m-d H:i:s"),$inquiry[' id'],"followup","");
					$this->db->send_notification($data,$refreshTokens);
					$count++;
				}
				$reply=array("ack"=>1,"developer_msg"=>"followup notification for ".$count." fired!!","ack_msg"=>"followup notification for ".$count." fired!!");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No followup found","ack_msg"=>"No followup found");
				return $reply;
			}
		}*/

		function addFollowupNotification()
		{
		    include('push_notification.class.php');
	        $PushNotification=new PushNotification();
	        $followusps=$this->db->rp_getData("followup","*","DATE_FORMAT(followup_date, '%Y-%m-%d %H')='".date('Y-m-d H',strtotime('+1 hour'))."' AND status=0 AND next_action!= -1 AND isDelete=0 AND is_notification_send=0","",0);
			if($followusps){
				while ($followusp=mysqli_fetch_assoc($followusps)) {
					// print_r($followusp);exit;
					///// send notification And Add notification
					$status=array("1"=>"Call","2"=>"SMS","3"=>"Email");
					$visitor_name=$this->db->rp_getValue("executive","company_name","id='".$followusp['user_id']."'");
					$notification_title="Followup required for ".$visitor_name." by ".$status[$followusp['through']]." on ".date("H:i",strtotime($followusp['followup_date']));
					$notification_description=$followusp['description'];
					$notification_type="1";
					$notification_type="1";
					$type_slug="";
					$rows 	= array(
							"user_id",
							"referance_id",
							"referance_type",
							"notification_title",
							"notification_description",
							"notification_type",
							"type_slug",
						);
					$values = array(
							$followusp['user_id'],
							$followusp['id'],
							"followup",
							$notification_title,
							$notification_description,
							$notification_type,
							$type_slug,
						);
					$this->db->rp_insert("notification",$values,$rows,0);
					$msg = array(
							//"type"		     => $notification_type,
							"type"		     => 'followup',
							"title"		     => $notification_title,
							"description"    => $notification_description,
							"user_id"        => $followusp['user_id'],
							"reference_id"   => $followusp['id'],
							"reference_type"  => 'followup',
							);
					
					$user_id = $this->db->rp_getData("sales_executive","*","id!='".$followusp['user_id']."' AND isDelete=0","",0);
					if($user_id)
					{
						while($v=mysqli_fetch_assoc($user_id))
						{
							$user[]=$v['id'];
						}
					}
					// print_r($user); exit;
					/*$refresh_tokens=$this->db->rp_getData("sales_executive","refreshToken","user_id='".$followusp['user_id']."' OR user_id IN ('".implode(",",$user)."')","",1);
							if($refresh_tokens){
								$tokens=array();
								while($refresh_token=mysqli_fetch_assoc($refresh_tokens)){
									$tokens[]=$refresh_token['refreshtoken'];
								}
								//$result=$this->db->send_notification($msg,$tokens);
								$result=$PushNotification->send_notification1($msg,$tokens,1);
								//print_r($result);
							}*/
						    $where="refreshToken!='' AND id='".$followusp['user_id']."'";
					        $refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);
					        $result=$PushNotification->send_notification1($msg,$refreshTokens,1);
					        $this->db->rp_update("followup",array("is_notification_send"=>1),"id='".$followusp['id']."'");
				}
				$reply=array("ack"=>1,"developer_msg"=>"Notification send successfully!","ack_msg"=>"Notification send successfully!");
					return $reply;
			}else{
			$reply=array("ack"=>0,"developer_msg"=>"Followup not found","ack_msg"=>"Followup not found");
				return $reply;
			}
		}

	
	function addArea($areas,$sales_id="")
	{
		$area_return=array();
		$area_sales=array();
		if(!empty($areas))
		{
			foreach($areas as $area)
			{
				$area=$area['nameValuePairs'];

				$city_id=$area['city_id'];
				$area_where = "class_id = '".$area['class_id']."' AND  city_id = '".$area['city_id']."' AND  name = '".$this->db->clean($area['name'])."'  AND isDelete=0";
					$area_dupcheck = $this->db->rp_dupCheck("area",$area_where,0);

				if($area_dupcheck>0){
					$reply=array("ack"=>0,"developer_msg"=>"Already available","ack_msg"=>"Already available");
					return $reply;

				}else{
					$area_id=$this->db->rp_insert("area",array($this->db->clean($area['name']),$area['class_id'],$area['city_id'],0,1),array("name","class_id","city_id","isDelete","isActive"),0);
					if($sales_id!="")
					{
						$sales_type_slug=$this->db->rp_getValue("sales_executive","type","id='".$sales_id."'");
						$sales_type=$sales_type_slug;
						$area_sales[]=$this->db->rp_insert("sales_executive_map_area",array($area['class_id'],$area['city_id'],$area_id,$sales_id,$sales_type,$sales_type_slug,0,1),array("class_id","city_id","area_id","sales_executive_id","executive_type","type_slug","isDelete","isActive"),0);
					}
				}
				$area_return[]=array("local_id"=>$area['local_id'],"server_id"=>$area_id);
			}
			$reply=array("ack"=>1,"developer_msg"=>"Area submiited","ack_msg"=>"Area submiited","result"=>$area_return,"map_with_sales"=>$area_sales);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No Area found","ack_msg"=>"No Area found.");
			return $reply;
		}
	}

	function generateCode($length=6)
	{
		$characters='0123456789';
		$randStr="";
		for($i=1;$i<=$length;$i++)
		{
			$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
		}
		//echo $randStr;exit;
		return $randStr;
	}

	public function GetAccountInfo($aid)
	{
		$AccountInfo=$this->db->rp_getData("account","*","id='".$aid."'","",0);
		if($AccountInfo)
		{
			$AccountInfo=mysqli_fetch_assoc($AccountInfo);
			$Credit=$this->db->rp_getValue("account_transaction","SUM(credit)","account_id='".$AccountInfo['id']."' AND isDelete=0",0);
			$Debit=$this->db->rp_getValue("account_transaction","SUM(debit)","account_id='".$AccountInfo['id']."'  AND isDelete=0");
			$ClosingBalance=$Credit+$Debit;
			$AccountInfo['overall_closing_balance']=$ClosingBalance;
			$AccountInfo['actual_closing_balance']=$ClosingBalance;
			return $AccountInfo;
		}
		else
		{
			return false;
		}
	}


	public function GetEmployeeAccountInfo($aid)
	{
		$AccountInfo=$this->db->rp_getData("sales_executive","*","id='".$aid."'","",0);
		if($AccountInfo)
		{
			$AccountInfo=mysqli_fetch_assoc($AccountInfo);

			// echo "<pre>"; print_r($AccountInfo); exit;
			$Credit=$this->db->rp_getValue("employee_account_transaction ","SUM(credit)","sales_id='".$AccountInfo['id']."' AND isDelete=0",0);
			$Debit=$this->db->rp_getValue("employee_account_transaction ","SUM(debit)","sales_id='".$AccountInfo['id']."'  AND isDelete=0");
			$ClosingBalance=$Credit+$Debit;
			$AccountInfo['overall_closing_balance']=$ClosingBalance;
			$AccountInfo['actual_closing_balance']=$ClosingBalance;
			return $AccountInfo;
		}
		else
		{
			return false;
		}
	}

}
?>