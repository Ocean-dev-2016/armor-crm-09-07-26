<?php
$page_id=554;$page_slug='page_employee';
include("connect.php");
require_once("../include/employee.class.php");
require_once("../include/class.log.php");
$employee=new Employee();
$log=new Log();
$ctable 	= "emp_salary_info";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
{
	$cid=$_REQUEST['cid'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="add_salary")
		{
			
			if(
				isset($_REQUEST['cid']) && $_REQUEST['cid']!="" 
					
			)
			{
				$cid=$_REQUEST['cid'];
				$year=$_REQUEST['year'];
				$month=$_REQUEST['month'];
				$basic=$_REQUEST['basic'];
				$hra=$_REQUEST['hra'];
				$medical=$_REQUEST['medical'];
				$conv=$_REQUEST['conv'];
				$wash=$_REQUEST['wash'];
				$edu=$_REQUEST['edu'];
				$lt=$_REQUEST['lt'];
				$spe=$_REQUEST['spe'];
				$gross=$_REQUEST['gross'];
				$it=$_REQUEST['it'];
				$pt=$_REQUEST['pt'];
				$pf=$_REQUEST['pf'];
				$net_payable=$_REQUEST['net_payable'];
				$remark=$_REQUEST['remark'];
				$adate	= date('Y-m-d H:i:s');
				
					$rows=array("emp_id",
						"basic",
						"year",
						"month",
						"hra",
						"medical",
						"conv",
						"wash",
						"edu",
						"lt",
						"spe",
						"gross",
						"it",
						"pt",
						"pf",
						"net_payable",
						"remark",
						"isActive",
						"adate");
					$values=array($cid,
						$basic,
						$year,
						$month,
						$hra,
						$medical,
						$conv,
						$wash,
						$edu,
						$lt,
						$spe,
						$gross,
						$it,
						$pt,
						$pf,
						$net_payable,
						$remark,
						1,
						$adate);
					$cbid=$db->rp_insert($ctable,$values,$rows,0);
					$log->insertLog($ctable,$cbid,"insert","Employee Salary Added By User");
					if($cbid!=0)
					{
						$response=array('ack'=>1,'ack_msg'=>'Salary added Successfully !!!');
						echo json_encode($response);
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Salary could not added !!!');
						echo json_encode($response);
					}				
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Contact Informations can not be empty !!!');
				echo json_encode($response);
			}
			
		}
		else if($service=="edit_salary")
		{
			if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
			{
				$year=$_REQUEST['year'];
				$month=$_REQUEST['month'];
				$basic=$_REQUEST['basic'];
				$hra=$_REQUEST['hra'];
				$medical=$_REQUEST['medical'];
				$conv=$_REQUEST['conv'];
				$wash=$_REQUEST['wash'];
				$edu=$_REQUEST['edu'];
				$lt=$_REQUEST['lt'];
				$spe=$_REQUEST['spe'];
				$gross=$_REQUEST['gross'];
				$it=$_REQUEST['it'];
				$pt=$_REQUEST['pt'];
				$pf=$_REQUEST['pf'];
				$net_payable=$_REQUEST['net_payable'];
				$remark=$_REQUEST['remark'];
				
				$rows 	= array(
							"year"			=> $year,
							"month"			=> $month,
							"basic"			=> $basic,
							"hra"			=> $hra,
							"medical"		=> $medical,
							"conv"			=> $conv,
							"wash"			=> $wash,
							"edu"			=> $edu,
							"lt"			=> $lt,
							"spe"			=> $spe,
							"gross"			=> $gross,
							"it"			=> $it,
							"pt"			=> $pt,
							"pf"			=> $pf,
							"net_payable"	=> $net_payable,
							"remark"		=> $remark,				
				);
				
					$where	= "id='".$_REQUEST['cpid']."'";
					$emp_salary_id=$db->rp_update($ctable,$rows,$where,0);
					$log->insertLog($ctable,$_REQUEST['cpid'],"update","Employee Salary Updated By User");
					if($emp_salary_id!=0)
					{
						$response=array('ack'=>1,'ack_msg'=>'Employee Salary inforamtion Update Successfully !!!');
						echo json_encode($response);
					}	
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Salary couldn\'t be edited try later !!!');
						echo json_encode($response);
					}
				
									
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Salary Information can\'t be empty or Salary not found !!!');
				echo json_encode($response);			
			}
		}
		else if($service=='delete_salary')
		{
			if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="" )
			{
				$rows 	= array(
					"isDelete"	=> "1"
				);
				$where	= "id='".$_REQUEST['cid']."'";
				$s_id=$db->rp_update($ctable,$rows,$where,0);
				$log->insertLog($ctable,$_REQUEST['cid'],"delete","Employee Salary Deleted By User");
				if($s_id!=0)
				{
					$response=array('ack'=>1,'ack_msg'=>'Salary removed Successfully !!!');
					echo json_encode($response);
				}	
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Salary can\'t be deleted !!!');
					echo json_encode($response);
				}					
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($response);				
			}
		}		
		else if($service=='get_salary')
		{
			if(isset($_REQUEST['emp_id']) && $_REQUEST['emp_id']!="" && isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
			{
				$emp_id=$_REQUEST['emp_id'];
				$salary_id=$_REQUEST['cid'];
				
				$salaryInfo=$employee->getSalaryInfo(array("emp_id"=>$emp_id,"id"=>$salary_id));
				if($salaryInfo['ack']==1)
				{
					$salaryInfo=$salaryInfo['result'];
					$response=array('ack'=>1,'ack_msg'=>'Salary Information successfully fetched!!',"result"=>$salaryInfo);
					echo json_encode($response);
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Salary Info Not Found try later!!');
					echo json_encode($response);
				}
				
					
				
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Contact not found try later!!');
				echo json_encode($response);
			}
			
		}
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
			echo json_encode($response);
		}
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
?>
<?php require_once 'disconnect.php';  ?>