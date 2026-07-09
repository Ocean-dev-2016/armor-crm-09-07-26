<?php

require_once("function.class.php");

require_once("class.log.php");

class FollowupRequest extends Functions

{

	public $db,$log;

	public $ctable="followup_request";

	public $ctableVisitor="visitor";

	function __construct($id="") 

	{

		$db = new Functions();

		$conn = $db->connect();

		$this->db=$db;		   

		$this->log=new Log();		   

    } 

	public function CreateFollowup($user_id,$visitor_id,$description,$through,$followup_date,$followup_flag,$request_id)
	{
		if($visitor_id!="")
		{
			$followup_detail=$this->db->rp_getData($this->ctable,"*","visitor_id='".$visitor_id."' AND isDelete=0 AND next_action=1","id DESC",0);
		}
		else
		{
			$followup_detail=$this->db->rp_getData($this->ctable,"*","request_id='".$request_id."' AND isDelete=0 AND next_action=1","id DESC",0);
		}

		if($followup_detail)
		{
			$followup_detail=mysqli_fetch_assoc($followup_detail);
			if($followup_detail['next_action']==1)
			{
				$next_followup_id=$followup_detail['id'];
			}else
			{
				$next_followup_id=0;
			}
		}
		else
		{
			$next_followup_id=0;
		}

		/*$refrence_media_id = $this->db->rp_getValue("executive","reference_media_id","id='".$visitor_id."'",0);

		$project_manager_id = $this->db->rp_getValue("executive","project_manager_id","id='".$visitor_id."'",0);*/

		$refrence_media_id="";

		$project_manager_id="";

		if($followup_flag=="request_followup")
		{
			$refrence_table = "request";
		}

		if($followup_flag=="followup")
		{
			$refrence_table = "sales_executive";
		}

		$count = $this->db->rp_getTotalRecord("followup_request","request_id='".$request_id."' AND isDelete=0",0);
		//echo $count; exit;

		$Values=array($refrence_table,$user_id,$visitor_id,$request_id,$project_manager_id,$description,$through,date("Y-m-d H:i:s",strtotime($followup_date)),0,1,$next_followup_id,$refrence_media_id);

		$Columns=array("refrence_table","user_id","visitor_id","request_id","project_manager_id","description","through","followup_date","isDelete","isActive","next_followup_id","refrence_media_id");

		$ContentID=$this->db->rp_insert($this->ctable,$Values,$Columns,0);

		if($ContentID)
		{
			if($count==0)
			{
				$Update = $this->db->rp_update("request",array("status"=>1),"id='".$request_id."'",0); 
			}
			$reply=array("a"=>1,"dmg"=>"Followup Successfully Created","mg"=>" Followup Successfully Created","followup_id"=>$ContentID);	
		}
		else
		{
			$reply=array("a"=>0,"dmg"=>"Create Followup Failed!!","mg"=>"Create Followup Failed!!");
		}
		return $reply;
	}

	public function GetFollowupContent($visitor_id,$request_id)
	{
		$result=array();
		$Content=array();
		$limit=self::getLimit();
		$status=array("1"=>"Call","2"=>"SMS","3"=>"Email");
		$status_followup=array("1"=>"Responsed","0"=>"Followup Created");
		$refrence_media_id = $_REQUEST['refrence_media_id'];

		if($_REQUEST['followup_type']==1)
		{
			$followup_type = "sales_executive";
			/*$followup_type = "request";*/
			$followup_type_where.= "refrence_table='".$followup_type."' AND ";
		}
		else if($_REQUEST['followup_type']==2)
		{
			$followup_type = "request";	
			/*$followup_type = "sales_executive";*/	
			$followup_type_where.= "refrence_table='".$followup_type."' AND ";
		}
		else
		{
			$followup_type_where.= "";
		}

		if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
		{
		  $Where.= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($_REQUEST['ToDate']))."' AND isDelete=0";
		}

		if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
		{
			$Where.= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($_REQUEST['FromDate']))."' AND isDelete=0 ";
		}
		
		if($_REQUEST['customer_id']=="" && $_REQUEST['request_id']=="")
		{
			$Contents=$this->db->rp_getTotalRecord($this->ctable,$followup_type_where."isDelete='0' AND isActive='1' AND user_id='".$_REQUEST['sales_id']."'",0);
		}
		else if($_REQUEST['sales_id']!="")
		{
			$Contents=$this->db->rp_getTotalRecord($this->ctable,"isDelete='0' AND isActive='1' AND visitor_id='".$visitor_id."' AND user_id='".$_REQUEST['sales_id']."'",0);
		}
		else if($_REQUEST['request_id']!="")
		{
			$Contents=$this->db->rp_getTotalRecord($this->ctable,"isDelete='0' AND isActive='1' AND request_id='".$_REQUEST['request_id']."' AND user_id='".$_REQUEST['sales_id']."'",0);
		}
		else 
		{
			$Contents=$this->db->rp_getTotalRecord($this->ctable,"isDelete='0' AND isActive='1' AND visitor_id='".$visitor_id."'",0);
		}

		//print_r($Contents); exit;

		if($Contents>0)
		{	
			if($_REQUEST['customer_id']!="")
			{
				$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND visitor_id='".$visitor_id."' AND user_id='".$_REQUEST['sales_id']."' ".$Where,"followup_date ASC",0,$limit);
			}
			else if($_REQUEST['customer_id']=="" && $_REQUEST['request_id']=="")
			{
				$FollowupContent=$this->db->rp_getData($this->ctable,"*",$followup_type_where."isDelete='0' AND isActive='1' AND user_id='".$_REQUEST['sales_id']."' ".$Where,"followup_date ASC",0,$limit);
			}
			else
			{
				$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND user_id='".$_REQUEST['sales_id']."' AND request_id='".$_REQUEST['request_id']."'".$Where,"followup_date ASC",0,$limit);
				}
			
			if($FollowupContent)
			{
				while($FollowupContent_d=mysqli_fetch_assoc($FollowupContent))
				{

					if($FollowupContent_d['refrence_table']=="sales_executive")
					{
						$FollowupContent_d['followup_slug'] = "Followup";
					}
					else
					{
						$FollowupContent_d['followup_slug'] = "Request Followup";
					}
					$FollowupContent_d['type_slug']=$status[$FollowupContent_d['through']];
					$FollowupContent_d['status_slug']=$status_followup[$FollowupContent_d['status']];
					if($FollowupContent_d['next_action'] == -1)
					{
						$FollowupContent_d['status_slug'] = "Followup End";
					}
					$FollowupContent_d['refrence_media_id']=$FollowupContent_d['refrence_media_id'];
					$FollowupContent_d['refrence_media_id'] = $this->db->rp_getValue("reference_media","name","id='".$FollowupContent_d['refrence_media_id']."'",0);
					$FollowupContent_d['followup_date']=($FollowupContent_d['followup_date']!="0000-00-00 00:00:00")?date('d F Y H:i',strtotime($FollowupContent_d['followup_date'])):"";
					$FollowupContent_d['future_date']=($FollowupContent_d['future_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['future_date'])):"";
					$FollowupContent_d['response_date']=($FollowupContent_d['response_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['response_date'])):"";
					$FollowupContent_d['created_date']=($FollowupContent_d['created_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['created_date'])):"";
					$FollowupContent_d['day']=  date('l', strtotime($FollowupContent_d['followup_date']));
					$getName = $this->db->rp_getValue("sales_executive","name","id='".$FollowupContent_d['user_id']."'",0);
					$FollowupContent_d['user_name']=$getName;
					$category_id = $this->db->rp_getValue("visitor","category_id","id='".$FollowupContent_d['visitor_id']."' and isDelete=0","",0);
					if($category_id!=0)
					{
						$category_name = $this->db->rp_getValue("category","name","id='".$category_id."' and isDelete=0","",0);
					}
					else
					{
						$category_name = "Other";
					}
					$FollowupContent_d['category_name']=$category_name;
					
					if($FollowupContent_d['refrence_table']=="request")
					{
						$visitor_detail = $this->db->rp_getData("request","*","id='".$FollowupContent_d['request_id']."'");

						if($visitor_detail){
							$visitor_detail=mysqli_fetch_assoc($visitor_detail);
							$FollowupContent_d['name']=$this->db->rp_getValue("executive","cname","id='".$visitor_detail['customer_id']."'");
							$FollowupContent_d['mobile_no']=$this->db->rp_getValue("executive","phone","id='".$visitor_detail['customer_id']."'");
							$FollowupContent_d['email']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
						else
						{
							$FollowupContent_d['name']="";
							$FollowupContent_d['email']="";
							$FollowupContent_d['mobile_no']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
					}
					else
					{
						$visitor_detail=$this->db->rp_getData("executive","*","id='".$FollowupContent_d['visitor_id']."'");

						if($visitor_detail){
							$visitor_detail=mysqli_fetch_assoc($visitor_detail);
							$FollowupContent_d['name']=$visitor_detail['cname'];
							$FollowupContent_d['email']=$visitor_detail['email'];
							$FollowupContent_d['mobile_no']=$visitor_detail['phone'];
							$FollowupContent_d['rating']=$visitor_detail['rating'];
							$FollowupContent_d['remark']=$visitor_detail['remark'];
						}
						else
						{
							$FollowupContent_d['name']="";
							$FollowupContent_d['email']="";
							$FollowupContent_d['mobile_no']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
					}
					$Content[]=$FollowupContent_d;
				}
				$reply=array("ack"=>1,"developer_msg"=>"Followup Get Sussess!!","ack_msg"=>"Followup Get Sussess!!","result"=>$Content);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Followup Not Found!!","ack_msg"=>"Followup Not Found!!");
			}

		}
		else
		{

			$reply=array("ack"=>0,"developer_msg"=>"Followup Not Found!!","ack_msg"=>"Followup Not Found!!");
		}

		return $reply;

	}

	public function AddFollowupResponse($response,$followup_action,$followup_id,$followup_future_date)

	{

		$result=array();

		/*$Count=$this->db->rp_getTotalRecord($this->ctable,"response='".$response."' AND isDelete='0' AND isActive='1'",0);

		if($Count<=0)

		{*/

			$Values=array("response"=>$response,"next_action"=>$followup_action,"response_date"=>date("Y-m-d H:i:s"),"status"=>1,"future_date"=>$followup_future_date);

			$ContentID=$this->db->rp_update($this->ctable,$Values,"id='".$followup_id."'",0);

			if($ContentID)

			{

				if($followup_action==-1){

					$visitor_id=$this->db->rp_getValue($this->ctable,"visitor_id","id='".$followup_id."'",0);

					//$this->db->rp_update("visitor",array("isActive"=>0),"id='".$visitor_id."'",0);
				}

				$reply=array("a"=>1,"dmg"=>"Response Successfully Added","mg"=>"Response Successfully Added","followup_id"=>$followup_id);	
			}

			else

			{

				$reply=array("a"=>0,"dmg"=>"Response Added Failed!!","mg"=>"Response Added Failed!!");

			}

			

	/*	}

		else

		{

			$reply=array("a"=>0,"dmg"=>"This Followup Response Already Exist","mg"=>"This Followup Response Already Exist");

		}*/

		

		return $reply;

	}

	public function GetTodayFollowup($user_id,$limit="")

	{

		$vistors=array();
		$request=array();

		$admin_type=$this->db->rp_getValue("sales_executive","type","id='".$user_id."'");
		/*if($admin_type==0)
		{
			$visitorsR=$this->db->rp_getData("executive","id,cname","isDelete=0 AND isActive=1","",0);
		}
		else if($admin_type==2)
		{
			$visitorsR=$this->db->rp_getData($this->ctableVisitor,"id,name","user_id='".$user_id."' AND isDelete=0 AND isActive=1","",0);	
		}
		else 
		{
			$visitorsR=$this->db->rp_getData($this->ctableVisitor,"id,name","project_id='".$project_id."' AND ( user_id='".$user_id."' OR project_manager_id=0 OR project_manager_id='".$user_id."' ) AND isDelete=0 AND isActive=1","",0);	
		}*/

		$visitorsR=$this->db->rp_getData("executive","id,company_name","isDelete=0 AND isActive=1","",0);
		if($visitorsR)
		{
			while($v=mysqli_fetch_assoc($visitorsR))
			{

				$vistors[]=$v['id'];
			}
		}

		$RequestR=$this->db->rp_getData("request","id","isDelete=0 AND isActive=1","",0);
		if($RequestR)
		{
			while($REQUEST_D=mysqli_fetch_assoc($RequestR))
			{

				$request[]=$REQUEST_D['id'];
			}
		}



		if($_REQUEST['executive_id']!='')
		{
			$executive_id=$_REQUEST['executive_id'];
			$visitorsR=$this->db->rp_getData($this->ctableVisitor,"id,name","user_id='".$executive_id."' AND isDelete=0 AND isActive=1","",0);
			$vistors=array();
			if($visitorsR)
			{
				while($v=mysqli_fetch_assoc($visitorsR))
				{
					$vistors[]=$v['id'];
				}
			}
		}


		$result=array();
		$Content=array();
		$limit=self::getLimit();
		$status=array("1"=>"Call","2"=>"SMS","3"=>"Email");
		$status_followup=array("1"=>"Responsed","0"=>"Followup Created");
		if(!empty($vistors))
		{
			$admin_type=$this->db->rp_getValue("sales_executive","type","id='".$user_id."'");
			$where1="isDelete='0' AND isActive='1' AND visitor_id IN (".implode(",",$vistors).") OR request_id IN (".implode(",", $request).")";
			if($admin_type==0)
			{
				$where .=" AND isDelete=0";
			}
			else if($admin_type==2)
			{
				$where.=" AND isDelete=0";
			}
			else
			{

				$where.=" AND (user_id='".$user_id."' OR project_manager_id='".$user_id."' OR project_manager_id=0)";
			}

			$Contents=$this->db->rp_getTotalRecord($this->ctable,$where1,0);
			//$Contents=$this->db->rp_getTotalRecord($this->ctable,"user_id ='".$user_id."' AND isDelete=0 AND isActive=1",0);
			if($Contents>0)
			{	
				//$today = date("Y-m-d");
				$today = $_REQUEST['todate']?date('Y-m-d',strtotime($_REQUEST['todate'])):"";
				$fromdate = $_REQUEST['fromdate']?date('Y-m-d',strtotime($_REQUEST['fromdate'])):"";
				$refrence_media_id = $_REQUEST['refrence_media_id'];

				if($_REQUEST['mode'] =='future')
				{

					if($refrence_media_id=='' && $today=='' && $fromdate=='')
					{

						$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND DATE(followup_date)>'".date('Y-m-d')."' AND visitor_id IN (".implode(",",$vistors).")".$where,"followup_date ASC",0,$limit);

					}

					else if($refrence_media_id=='')

					{

						$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND DATE(followup_date)<='".$today."' AND DATE(followup_date)>='".$fromdate."' AND visitor_id IN (".implode(",",$vistors).")".$where,"followup_date ASC",0,$limit);

					}

					else

					{

						$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1'  AND DATE(followup_date)<='".$today."' AND DATE(followup_date)>='".$fromdate."' AND refrence_media_id=".$refrence_media_id." AND visitor_id IN (".implode(",",$vistors).")".$where,"followup_date ASC",0,$limit);

					}

				}

				else if($refrence_media_id!='')

				{

					// echo $refrence_media_id ; exit;

					$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND DATE(followup_date)='".date("Y-m-d")."' AND refrence_media_id=".$refrence_media_id." AND visitor_id IN (".implode(",",$vistors).")".$where,"followup_date ASC",0,$limit);

				}

				else

				{
					if($_REQUEST['followup_type']==1)
					{
						$followup_type = "sales_executive";
						/*$followup_type = "request";*/
						$followup_type_where.= "refrence_table='".$followup_type."' AND ";
					}
					else if($_REQUEST['followup_type']==2)
					{
						$followup_type = "request";	
						/*$followup_type = "sales_executive";	*/
						$followup_type_where.= "refrence_table='".$followup_type."' AND ";
					}

					if($_REQUEST['sales_id']!="")
					{
						$FollowupContent=$this->db->rp_getData($this->ctable,"*",$followup_type_where."isDelete='0' AND isActive='1' AND DATE(followup_date)='".date("Y-m-d")."' AND user_id='".$_REQUEST['sales_id']."' AND (visitor_id IN (".implode(",",$vistors).") OR request_id IN (".implode(",", $request)."))".$where,"followup_date ASC",0,$limit);
					}
					else
					{
						

						$FollowupContent=$this->db->rp_getData($this->ctable,"*",$followup_type_where."isDelete='0' AND isActive='1' AND DATE(followup_date)='".date("Y-m-d")."' AND  (visitor_id IN (".implode(",",$vistors).") OR request_id IN (".implode(",", $request)."))".$where,"followup_date ASC",1,$limit);	
					}
					

				}

				

				if($FollowupContent)

				{

					while($FollowupContent_d=mysqli_fetch_assoc($FollowupContent))

					{

						if($FollowupContent_d['refrence_table']=="sales_executive")
						{
							$FollowupContent_d['followup_slug'] = "Followup";
						}
						else
						{
							$FollowupContent_d['followup_slug'] = "Request Followup";
						}
						$FollowupContent_d['customer_id']=$FollowupContent_d['visitor_id'];

						$FollowupContent_d['type_slug']=$status[$FollowupContent_d['through']];

						$FollowupContent_d['status_slug']=$status_followup[$FollowupContent_d['status']];

						if($FollowupContent_d['next_action'] == -1)

						{

							$FollowupContent_d['status_slug'] = "Followup End";

						}

						$FollowupContent_d['refrence_media_id']=$FollowupContent_d['refrence_media_id'];

						$FollowupContent_d['refrence_media_id'] = $this->db->rp_getValue("reference_media","name","id='".$FollowupContent_d['refrence_media_id']."'",0);

						$FollowupContent_d['followup_date']=($FollowupContent_d['followup_date']!="0000-00-00 00:00:00")?date('d F Y H:i',strtotime($FollowupContent_d['followup_date'])):"";

						$FollowupContent_d['future_date']=($FollowupContent_d['future_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['future_date'])):"";

						$FollowupContent_d['response_date']=($FollowupContent_d['response_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['response_date'])):"";

						$FollowupContent_d['created_date']=($FollowupContent_d['created_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i',strtotime($FollowupContent_d['created_date'])):"";

						$FollowupContent_d['day']=  date('l', strtotime($FollowupContent_d['followup_date']));
                        
					

					$FollowupContent_d['user_name']=$getName;

					//$visitor_detail=$this->db->rp_getData($this->ctableVisitor,"*","id='".$FollowupContent_d['visitor_id']."'");
					//$visitor_detail=$this->db->rp_getData("executive","*","id='".$FollowupContent_d['visitor_id']."'");

					$getName = $this->db->rp_getValue("sales_executive","name","id='".$FollowupContent_d['user_id']."'",0	);

					$FollowupContent_d['user_name']=$getName;

					

					if($FollowupContent_d['refrence_table']=="request")
					{
						$visitor_detail = $this->db->rp_getData("request","*","id='".$FollowupContent_d['request_id']."'");

						if($visitor_detail){
							$visitor_detail=mysqli_fetch_assoc($visitor_detail);
							$FollowupContent_d['name']=$this->db->rp_getValue("executive","cname","id='".$visitor_detail['customer_id']."'");
							$FollowupContent_d['mobile_no']=$this->db->rp_getValue("executive","phone","id='".$visitor_detail['customer_id']."'");
							$FollowupContent_d['email']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
						else
						{
							$FollowupContent_d['name']="";
							$FollowupContent_d['email']="";
							$FollowupContent_d['mobile_no']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
					}
					else
					{
						$visitor_detail=$this->db->rp_getData("executive","*","id='".$FollowupContent_d['visitor_id']."'");

						if($visitor_detail){
							$visitor_detail=mysqli_fetch_assoc($visitor_detail);
							$FollowupContent_d['name']=$visitor_detail['cname'];
							$FollowupContent_d['email']=$visitor_detail['email'];
							$FollowupContent_d['mobile_no']=$visitor_detail['phone'];
							$FollowupContent_d['rating']=$visitor_detail['rating'];
							$FollowupContent_d['remark']=$visitor_detail['remark'];
						}
						else
						{
							$FollowupContent_d['name']="";
							$FollowupContent_d['email']="";
							$FollowupContent_d['mobile_no']="";
							$FollowupContent_d['rating']="";
							$FollowupContent_d['remark']="";
						}
					}

					

					$category_id = $this->db->rp_getValue("visitor","category_id","id='".$FollowupContent_d['visitor_id']."' and isDelete=0","",0);

				

					if($category_id!=0)

					{

						$category_name = $this->db->rp_getValue("category","name","id='".$category_id."' and isDelete=0","",0);

					}

					else

					{

						$category_name = "Other";

					}

					

					$FollowupContent_d['category_name']=$category_name;

					$Content[]=$FollowupContent_d;

					}

				}

				if(!empty($Content))

				{

					$reply=array("ack"=>1,"developer_msg"=>"Todays Followup Get Sussess!!","ack_msg"=>"Followup Get Sussess!!","result"=>$Content);

				}

				else

				{

					$reply=array("ack"=>0,"developer_msg"=>"Todays Followup Not Found!!","ack_msg"=>"Followup Not Found!!");

				}

			}

			else

			{

				$reply=array("ack"=>0,"developer_msg"=>"Todays Followup Not Found!!","ack_msg"=>"Followup Not Found!!");

			}

		}

		else

		{

			$reply=array("ack"=>0,"developer_msg"=>"Todays Followup Not Found!!","ack_msg"=>"Followup Not Found!!");

		}

		return $reply;

	}

	public function GetFollowupDetail($followup_id)

	{

		$Content=array();

		$status=array("1"=>"Call","2"=>"SMS","3"=>"Email");

		$status_followup=array("1"=>"Responsed","0"=>"Followup Created");

			$FollowupContent=$this->db->rp_getData($this->ctable,"*","isDelete='0' AND isActive='1' AND id='".$followup_id."'","",0);

			if($FollowupContent)

			{

				$FollowupContent_d=mysqli_fetch_assoc($FollowupContent);

					if($FollowupContent_d['refrence_table']=="sales_executive")
					{
						$FollowupContent_d['followup_slug'] = "followup";
					}
					else
					{
						$FollowupContent_d['followup_slug'] = "request_followup";
					}

					$FollowupContent_d['type_slug']=$status[$FollowupContent_d['through']];

					$FollowupContent_d['status_slug']=$status_followup[$FollowupContent_d['status']];



					$FollowupContent_d['followup_date']=($FollowupContent_d['followup_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i:s',strtotime($FollowupContent_d['followup_date'])):"";

					$FollowupContent_d['future_date']=($FollowupContent_d['future_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i:s',strtotime($FollowupContent_d['future_date'])):"";

					$FollowupContent_d['response_date']=($FollowupContent_d['response_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i:s',strtotime($FollowupContent_d['response_date'])):"";

					$FollowupContent_d['created_date']=($FollowupContent_d['created_date']!="0000-00-00 00:00:00")?date('d-m-Y H:i:s',strtotime($FollowupContent_d['created_date'])):"";

					if($FollowupContent_d['refrence_table']=="request")
					{
						$FollowupContent_d['visitor_name'] = $this->rp_getValue("request","company_name","id='".$FollowupContent_d['request_id']."'");
					}
					else{
						$FollowupContent_d['visitor_name'] = $this->rp_getValue("executive","cname","id='".$FollowupContent_d['visitor_id']."'");
					}

					

					$FollowupContent_d['visitor_mobile'] = $this->rp_getValue("executive","phone","id='".$FollowupContent_d['visitor_id']."'");



					$Content=$FollowupContent_d;





					$reply=array("ack"=>1,"developer_msg"=>"Followup Get Successfully!!","ack_msg"=>"Followup Get Successfully!!","result"=>$Content);

			}else{

				$reply=array("ack"=>0,"developer_msg"=>"Followup Not Found!!","ack_msg"=>"Followup Not Found!!");

			}

			

		

		return $reply;

	}

	public function EditFollowupResponse($response,$followup_id)

	{

			$result=array();

		

			$Values=array("response"=>$response,"response_date"=>date("Y-m-d H:i:s"));

			$ContentID=$this->db->rp_update($this->ctable,$Values,"id='".$followup_id."'",0);

			if($ContentID)

			{

				$reply=array("a"=>1,"dmg"=>"Response Successfully Updated","mg"=>"Response Successfully Updated","followup_id"=>$followup_id);	

			}

			else

			{

				$reply=array("a"=>0,"dmg"=>"Response Added Failed!!","mg"=>"Response Added Failed!!");

			}

		return $reply;

	}

	function getLimit(){

			$limit=array();

			if(isset($_REQUEST['ul']))

			{

				$limit['ul']=$_REQUEST['ul'];

			}

			if(isset($_REQUEST['ll']))

			{

				$limit['ll']=$_REQUEST['ll'];

			}

			if($limit!="" && !empty($limit) && array_key_exists("ul",$limit) && array_key_exists("ll",$limit))

			{

			   return $limit['ul'].",".$limit['ll'];

			}

			else

			{

				return "";

			}

			

		}

}



?>