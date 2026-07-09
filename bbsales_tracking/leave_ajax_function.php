<?php 
$page_id=589;$page_slug='customer_type';
include("connect.php");

if(isset($_REQUEST['m']) && $_REQUEST['m']!="")
{

	$m=$_REQUEST['m'];
	
	if($m=="create_item")
	{
	    
	    $sales_executive_id=(isset($_REQUEST['sales_executive_id']))?$_REQUEST['sales_executive_id']:"";
		$start_date=(isset($_REQUEST['start_date']))?$_REQUEST['start_date']:"";
		$start_time=(isset($_REQUEST['start_time']))?$_REQUEST['start_time']:"";
		$end_date=(isset($_REQUEST['end_date']))?$_REQUEST['end_date']:"";
		$end_time=(isset($_REQUEST['end_time']))?$_REQUEST['end_time']:"";
         
		//$employee_name = $db->rp_getValue("application_login","name","id='".$employee_id."'",0);
		 
		$tap_name_b = array();
		$proccess = array();
		if($sales_executive_id!=0)
		{
			
			$html="";
			$sd=str_replace(" ","", $start_date);
			$sd=str_replace(":","", $sd);
			$st=str_replace(" ","", $start_time);
			$st=str_replace(":","", $st);
			$et=str_replace(" ","", $end_time);
			$et=str_replace(":","", $et);
			
			$html.='<tr class="leave_'.$sd.$st.$et.'">';
			$html.='<td><input type="hidden" name="start_date[]" id="start_date" value="'.$start_date.'">'.$start_date.'</td>';
			$html.='<td><input type="hidden" name="start_time[]" id="start_time" value="'.$start_time.'">'.$start_time.'</td>';
			$html.='<td><input type="hidden" name="end_date[]" id="end_date" value="'.$end_date.'">'.$end_date.'</td>';
			$html.='<td><input type="hidden" name="end_time[]" id="end_time" value="'.$end_time.'">'.$end_time.'</td>';
			$html.="<td class='delete'><a class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></a></td>";
			$html.="</tr>";
			echo $html;
		}
	}

	else
	{
		$reply=array("ack"=>0,"ack_msg"=>"Service Unavailable","dmg"=>"Missing Service ER-2");
		echo json_encode($reply);
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Service Unavailable","dmg"=>"Missing parameters ER-1");
	echo json_encode($reply);
}
require_once "disconnect.php";
?>