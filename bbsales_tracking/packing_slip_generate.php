<?php

/*
 * @author Ravi Patel
 */

// if($_REQUEST['staic']==2)
// {
	$page_id = 420;$page_slug  = 'packing_slip';
	require_once("connect_in.php");
	$dispatch_id	= $_REQUEST['id'];
// }
$relCertFileNames = array();
$merge_file = array();
$string ="<style>span{padding-right:70px}</style>";

//$d=file_get_contents(ADMINSITEURL."print_purchase_order.php?id=".$id);
$d=file_get_contents(ADMINSITEURL.'packing_slip_format_pdf.php?id='.$dispatch_id.'');
//$d.=$string;
require('mpdf60/mpdf.php');



$mpdf = new mPDF('',    // mode - default ''

 'A4',    // format - A4, for example, default ''

 15,     // font size - default 0

 'sans-serif',    // default font family

 3,    // margin_left

 3,    // margin right

 3,     // margin top

 3,    // margin bottom

 0,     // margin header

 0,     // margin footer

 'P');  // L - landscape, P - portrait

$mpdf->WriteHTML($d);

$last_id = $dispatch_id;
$dispatch_no = $db->rp_getValue("packing_slip","packing_slip_no","id='".$dispatch_id."'");
$exe_id = $db->rp_getValue("packing_slip","customer_id","isDelete=0 AND id='".$dispatch_id."' ");
$flag = "Web";
$ctable = "packing_slip";
$module_name = "Packing Slip";
$log_description = $module_name." ".$dispatch_no." PDF Download By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$user_id,$exe_id);

$exe_id = $db->rp_getValue("packing_slip","customer_id","isDelete=0 AND id='".$dispatch_id."' ");
$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("executive","company_name","id='".$exe_id."'",0)));
$packing_slip_no	= str_replace("/","-",stripslashes($db->rp_getValue("packing_slip","packing_slip_no","id='".$dispatch_id."'",0)));

// $fileName = "Packing_Slip_".SITENAME."_".date('d_m_Y')."_".$dis_no."_".$uname.'.pdf';
$fileName = $uname."_".date('d_m_Y')."_"."Packing_Slip_".$packing_slip_no.'pdf';   
 

if(!is_dir($fileName)){

	mkdir(DISPATCH_PDF.$fileName);

}

$pdf_file_path	= DISPATCH_PDF.$fileName."/".$fileName.'.pdf';

if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

if($_REQUEST['staic']==2)
{
	echo $pdf_file_path;
}
else
{
	$file_path = $pdf_file_path;
}
?>