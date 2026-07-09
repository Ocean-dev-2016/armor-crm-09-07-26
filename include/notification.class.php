<?php
include("class.phpmailer.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Notification extends Functions
{
	/*
		*** Notification Function Developed By Jai Acharya :/ <<<
	*/
	private $mailer;
	private $mailer2;
	
	function __construct() {
		// $this->mailer = new PHPMailer();
		// $this->mailer2 = new PHPMailer();
	}
	public function rp_checkSMS($n,$sms) // Common SMS Function
    {
		$smsMsg = urlencode($sms);
		$apiurl = SMS_URL."&mobile=+91".$n."&message=".$smsMsg;
		return file_get_contents($apiurl);
    }
	
	public function rp_sendEmail($toemail,$subject="",$body="",$cc="",$files=array()) // Common Email Function
    {
		$from_name = EMAIL_FROM_NAME;
		$from_mail = EMAIL_FROM_MAIL;
		$this->mailer2->SetFrom($from_mail,$from_name); // From email ID and from name
		$this->mailer2->AddAddress(stripslashes($toemail));//$toemail
		$this->mailer2->AddAddress(EMAIL_CC);//$toemail		
		// Multiple Email In BCC
		$bccs=EMAIL_BCC;
		foreach($bccs as $e)
		{
			$this->mailer2->AddAddress($e);
		}
		if($cc!="")
		{
			$this->mailer2->AddAddress($cc);	
			$this->mailer2->AddReplyTo($cc, EMAIL_FROM_MAIL);
		}
		else
		{			
			$this->mailer2->AddReplyTo(EMAIL_REPLY_TO, EMAIL_FROM_NAME);
		}
		$this->mailer2->Subject = $subject;
		$this->mailer2->MsgHTML($body);
		$this->mailer2->SMTPSecure = 'ssl';
		if(!empty($files)){
			foreach($files as $file)
			{
				$this->mailer2->AddAttachment($file['upload_file_path'],$file['new_file_path']);
			
			}
			
		}
		$result=$this->mailer2->Send();
		
    }

    // for SMTP send email
	public function rp_sendEmailSmtp($Data)
	{ 
		$default_to_arr = explode(",",$Data['default_to']);
		$default_cc_arr = explode(",",$Data['default_cc']);
		$default_bcc_arr = explode(",",$Data['default_bcc']); 

		// Load Composer's autoloader
		require 'PHPmailer/vendor/autoload.php';
		 
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true); 
		try {
		    //Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                     // Enable verbose debug output
		    $mail->isSMTP();                                           // Send using SMTP
		    $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
		    $mail->Username   = $Data['from_mail'];                    // SMTP username 'bhoomi.craftbox@gmail.com';
		    $mail->Password   = $Data['from_email_password'];          // SMTP password 'Bhoomi@2442'
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
		    $mail->Port       = 587;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom($Data['from_mail'], $Data['from_name']);


		    foreach ($default_to_arr as $toname) {
		        $mail->addAddress($toname);   
		    } 

		    foreach ($default_cc_arr as $ccname) {
		        $mail->addCC($ccname);   
		    } 

		    foreach ($default_bcc_arr as $bccname) {
		        $mail->addBCC($bccname);   
		    } 
		   
		     // loop for multiple attachement
		    // echo $Data['attachment']['file_path'][1];exit;
	     	/*foreach ($Data['attachment']['file_path'] as $ft) { 
		        // echo  $ft; 
		        $mail->addAttachment(realpath($file_path),$daily_proreport_name);
		    } exit*/
		    
		    // loop for multiple attachement
		    // echo realpath($Data['file_path']);exit;
			$mail->addAttachment(realpath($Data['file_path']),$Data['filename']);

		    // Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = $Data['subject'];
		    $mail->Body    = $Data['body'];

		    // $mail->AltBody = 'This is the computer generated reports by craftbox technology'; // This is the body in plain text for non-HTML mail clients

		    $mail->SMTPDebug  = 0; 

		    $mail->SMTPOptions = array(
		        'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true
		        )
		    );
		            
		    $mail->send();  
		    return 1;
		    
		} catch (Exception $e) {
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    return $mail->ErrorInfo;
		}

	}
	// for SMTP send email

	// for SMTP send email
	public function rp_sendEmailOutstanding($Data)
	{ 
		$default_to_arr = explode(",",$Data['default_to']);
		$default_cc_arr = explode(",",$Data['default_cc']);
		$default_bcc_arr = explode(",",$Data['default_bcc']); 


		// Load Composer's autoloader
		require 'PHPmailer/vendor/autoload.php';
		 
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true); 
		try {
		    //Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                     // Enable verbose debug output
		    $mail->isSMTP();                                           // Send using SMTP
		    $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
		    $mail->Username   = $Data['from_mail'];                    // SMTP username 'bhoomi.craftbox@gmail.com';
		    $mail->Password   = $Data['from_email_password'];          // SMTP password 'Bhoomi@2442'
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
		    $mail->Port       = 587;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom($Data['from_mail'], $Data['from_name']);
		    foreach ($default_to_arr as $toname) {
		    	$mail->addAddress($toname);   
		    } 

		    foreach ($default_cc_arr as $ccname) {
		        $mail->addCC($ccname);   
		    } 

		    /*foreach ($default_bcc_arr as $bccname) {
		        $mail->addBCC($bccname);   
		    } */
		   //	$mail->addAttachment(realpath($Data['file_path']),$Data['filename']);

		   	// Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = $Data['subject'];
		    $mail->Body    = $Data['body'];

		    // $mail->AltBody = 'This is the computer generated reports by craftbox technology'; // This is the body in plain text for non-HTML mail clients

		    $mail->SMTPDebug  = 0; 

		    $mail->SMTPOptions = array(
		        'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true
		        )
		    );
		            
		    $mail->send();  
		    return 1;
		    
		} catch (Exception $e) {
		    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		    return $mail->ErrorInfo;
		}
	}
	// for SMTP send email
	
	public function aj_sendSecurityCode($toemail,$subject="",$body="") // Common Email Function
    {
		$from_name = EMAIL_FROM_NAME;
		$from_mail = EMAIL_FROM_MAIL;			
		$body = $body;
		$mail32 = new PHPMailer();
		$mail32->SetFrom($from_mail,$from_name); // From email ID and from name
		$mail32->AddAddress(stripslashes($toemail));
		$mail32->Subject = $subject;
		$mail32->MsgHTML($body);
		$mail32->Send();
		/*****************************************/
		
    }
	public function aj_sendSMSSecurity($n,$sms) // Common SMS Function
    {				
		$smsTo 	= $n;		
		$apiurl =SMS_URL."&mobile=+91".$smsTo."&message=".urlencode($sms);
		$msg_id = file_get_contents($apiurl);
		return $msg_id;
    }
	public function rp_getDeliveryReport($messageId) // Common SMS Function
    {		
		$apiurl = SMS_URL."&messageid=".urlencode($messageId);
		$delivery_report_string= file_get_contents($apiurl);
		$delivery_report=explode(",",$delivery_report_string);
		if($delivery_report[4]=='DELIV')
		{
			$ack=array("ack"=>1,"ack_msg"=>"SMS sent successfully on".$delivery_report[0],"extra"=>$delivery_report);
			
		}
		else if($delivery_report[4]=='EXPIRED')
		{
			$ack=array("ack"=>0,"ack_msg"=>"SMS sending failed on".$delivery_report[0],"reason"=>"Mobile switched off or out of coverage area!!","extra"=>$delivery_report);
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"SMS sending failed on".$delivery_report[0],"reason"=>"Mobile number not available","extra"=>$delivery_report);
		}
		return $ack;
    }
	public function getEmailBody($EMAIL_TYPE,$params)
	{
		
		switch($EMAIL_TYPE)
		{
			case "FORGET_PASSWORD":
			$url=ADMINSITEURL."email_body/email_template.php?name=".urlencode($params['name'])."&email=".urlencode($params['email'])."&activation_code=".urlencode($params['activation_code']);
			$body=file_get_contents($url);
			$subject="Forget Password For ".SITETITLE;
			return array("body"=>$body,"subject"=>$subject);
			break;			
		}
	} 
}
/*
	*** Notification Function Developed By Jai Acharya :/ <<<
*/
?>