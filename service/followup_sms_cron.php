<?php
include('connect.php'); 
// $nt=new Notification;
function send_whatsapp($to, $msg)
{
	
	/*$url = 'https://wa.jobcreator.in/api/create-message';
	$post = array(
	  'appkey' => '589a279e-b6e3-4c0f-a2b8-9fad6e569b96',
	  'authkey' => 'dyC9mxFlJuoFmu3gvuU85eEBkGrNqvZFvKvLCO29ICP9cQuwvu',
	  'to' => $to,
	  'message' => $msg,
	  'sandbox' => 'false'
	);

	$headers = array( 							
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//////// SSL Verifier False ////////
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );
	$result = curl_exec( $ch );

	//print_r($result); exit;

	curl_close( $ch );
	return $result;*/ 

	// echo $msg;exit;
	$curl = curl_init();
	curl_setopt_array($curl, array(
	     CURLOPT_URL => 'http://whatsapp.hakimisolution.com/api/v1/sendMessage',
	     CURLOPT_RETURNTRANSFER => true,
	     CURLOPT_ENCODING => '',     
	     CURLOPT_MAXREDIRS => 10,
	     CURLOPT_TIMEOUT => 0, 
	     CURLOPT_FOLLOWLOCATION => true,      
	     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	     CURLOPT_CUSTOMREQUEST => 'POST',
	     CURLOPT_POSTFIELDS =>
	        '{
	          "key" : "849f3acc36384a5992964acbd1e9464f",
	          "to" : "'.$to.'",
	          "message" : "'.$msg.'",
	          "IsUrgent" : true,
	         }',
	    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	// echo $response; exit;
	return $response;
}

$today_date=date('Y-m-d');
// $next_date = date('Y-m-d', strtotime($today_date. ' + 1 days'));
$next_date = date('Y-m-d', strtotime($today_date)); //add by nilesh

$dataR=$db->rp_getData("followup","user_id","isDelete=0 AND response='' AND DATE(followup_date) <= '".$next_date."'  GROUP BY user_id","",0);
while ($dataD=mysqli_fetch_assoc($dataR)) { 
	$sales_person_name =  $db->rp_getValue("sales_executive","name","id='".$dataD['user_id']."'");
	$sales_phone =  $db->rp_getValue("sales_executive","phone","id='".$dataD['user_id']."'");
		$sms1 = "
		Dear ".$sales_person_name.",
		Kindly Find the list of follow-up you required tomorrow";

	$cnt=0;
	// $ctable_r=$db->rp_getData("followup","*","isDelete=0 AND DATE(followup_date) <= '".$next_date."' AND response=''","followup_date ASC",0);
	$ctable_r=$db->rp_getData("followup","*","isDelete=0 AND DATE(followup_date) <= '".$next_date."' AND response='' AND user_id='".$dataD['user_id']."'","followup_date ASC",0);
	 
	$total_rows = mysqli_num_rows($ctable_r);

	while ($ctable_d=mysqli_fetch_assoc($ctable_r)) { 
		$cnt++;

		if($cnt==1)
		{
		$sms = $sms1;
		}
		else
		{
		$sms="";
		}

		if($ctable_d['through']=='1')
	    {
	        $through_name="call";
	    }
	    else if($ctable_d['through']=='2')
	    {
	        $through_name="sms";
	    }
	    else if($ctable_d['through']=='3')
	    {
	        $through_name="email";
	    }
	 
	    if($ctable_d['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '0'",0))
		{
			$typeOfFollowUp = "Inquiry (INQ/".$ctable_d['reference_id'].")";
		} 
		else if ($ctable_d['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '-1'",0)) {
			$typeOfFollowUp= "Prospects (INQ/".$ctable_d['reference_id'].")";
		} 
		else if ( $ctable_d['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '1'",0)) {
			$typeOfFollowUp= "Leads (INQ/".$ctable_d['reference_id'].")";
		} 
		else if ($ctable_d['reference_table'] == "sales_executive") {
			$typeOfFollowUp= "Sales Executive";
		}
		else if ($ctable_d['reference_table'] == "customer_inquiry") {

			$typeOfFollowUp= "Customer Inquiry";
		}
		else if ($ctable_d['reference_table'] == "quotation_followup" || $ctable_d['reference_table'] == "quotation_detail") { 
	        $quotationNo= $db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_d['reference_id']."'");
			$typeOfFollowUp= "Quotation (".$quotationNo.")";
		}
		else if ($ctable_d['reference_table'] == "executive" || $ctable_d['reference_table'] == "customer_inquiry") 
		{    
			$typeOfFollowUp= "Customer";
		}


		if($ctable_d['reference_table']=="sales_executive")
	    { 
	        $customer_name= $db->rp_getValue("executive","cname","id='".$ctable_d['visitor_id']."'");
	        $mobile_number=$db->rp_getValue("executive","phone","id='".$ctable_d['visitor_id']."'");
	    }
	     else if($ctable_d['reference_table']=="quotation_detail")
	    { 
	        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d['reference_id']."'");
	        $customer_name= $db->rp_getValue("executive","cname","id='".$customer_id."'");
	        $mobile_number=$db->rp_getValue("executive","phone","id='".$customer_id."'");
	    }
	    else if($ctable_d['reference_table']=="no_order_inquiry")
	    { 
	        $customer_name= $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d['reference_id']."'");
	        $mobile_number=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
	    }
	    else if($ctable_d['reference_table']=="customer_inquiry")
	    { 
	        $customer_name= $db->rp_getValue("customer_inquiry","company_name","id='".$ctable_d['reference_id']."'");
	        $mobile_number= $db->rp_getValue("customer_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
	    }
	    else if($ctable_d['reference_table']=="executive")
	    {
	        $customer_name= $db->rp_getValue("executive","company_name","id='".$ctable_d['reference_id']."'");
	    	$mobile_number=$db->rp_getValue("executive","phone","id='".$customer_id."'");
	    }


		$sms .="

		".$cnt.") ".$customer_name." |  ".$mobile_number."
		".$ctable_d['description']." , ".date('d-m-Y h:i A',strtotime($ctable_d['followup_date']))." By ".$through_name."
		- ".$typeOfFollowUp; 

		if($total_rows==$cnt)
		{			
		$sms.="

		Thank You 
		".SITENAME."

		Note : This Is autogenerated Msg from CRM for Information only 
		final statues on application only ";
		}
		// echo $sms;
		// $sms="hi";
	    // 	$smsNumber="91"."6352585333";
		$smsNumber="91".$sales_phone;
		if(WHATSAPP_SMS_SEND)
		{ 
			echo send_whatsapp($smsNumber,$sms);
		}
	}

	// echo $cnt;
}
?>