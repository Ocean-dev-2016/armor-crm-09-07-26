<?php
include("connect_in.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require('mpdf60/mpdf.php');
//$today = date('Y-m-d');
$today = "2021-12-24";
	
$mpdf = new mPDF('',    // mode - default ''

'A4-P',    // format - A4, for example, default ''

13,     // font size - default 0

'sans-serif',    // default font family

10,    // margin_left

10,    // margin right

10,     // margin top

10,    // margin bottom

0,     // margin header

0,     // margin footer

'P'
);  // L - landscape, P - portrait

$d=file_get_contents(ADMINSITEURL.'pending_followup_report_mail.php?date='.$today);
//echo $d; exit;
$mpdf->WriteHTML($d);
$fileName = $today."_".strtotime("now");
$fileName1 = "Followup Pending Report";
if(!is_dir($fileName)){
	mkdir(PENDING_FOLLOWUP_REPORT_MAIL.$fileName);
}
$pdf_file_path	= PENDING_FOLLOWUP_REPORT_MAIL.$fileName."/".$fileName1.'.pdf';
if(file_exists($pdf_file_path))
{
	unlink($pdf_file_path);
}
$mpdf->Output($pdf_file_path);
//echo $pdf_file_path;exit;

/*$Data['from_mail'] = $db->rp_getValue(CTABLE_ADMIN,"email","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0);
$Data['from_email_password'] = $db->rp_getValue(CTABLE_ADMIN,"email_password","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0); 
$Data['from_name'] = $db->rp_getValue(CTABLE_ADMIN,"name","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'","",0); */

/*send mail code*/
require 'PHPmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->isSMTP();
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Host       = 'smtp.gmail.com';                      
    $mail->SMTPAuth   = true;                              
    $mail->Username   = 'ravisiroya.cb@gmail.com';
    $mail->Password   = 'Ravi@2442';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = '587'; 

    //Recipients
    $mail->setFrom('crmadmin@cmkindia.com', 'CMK CRM Admin');
    $mail->addAddress('bhoomi.craftbox@gmail.com');
    

    // Attachments    
    $mail->addAttachment(realpath(dirname(__FILE__).'/'.$pdf_file_path),$fileName1.'pdf'); 

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'CMK CRM CRON MAIL';
    $mail->Body    = 'This is the computer generated reports';
    $mail->AltBody = 'This is the computer generated reports by Ocean Infotech';
    $mail->SMTPDebug  = 0; 
    $mail->send();
    // echo 'Message has been sent';
    
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
/*send mail code*/
?>