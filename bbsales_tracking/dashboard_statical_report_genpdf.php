<?php

/*
 * @author Ravi Patel
 */


$page_id=627;$page_slug='dashboard_statical_report_page';
require_once("connect_in.php");

$date = $_REQUEST['date'];
$date1 = $_REQUEST['date1'];
$sales_id = $_REQUEST['sales_id'];

$relCertFileNames = array();
$merge_file = array();
$string ="<style>table{width:100%} table,tr,th,td{border:1px solid #000;border-collapse: collapse;} td,th{padding:10px;font-size:12px;} h3{font-size:18px;} h2{font-size:20px;text-align:center;} .col-md-12{margin-bottom:10px;}</style>";


//$d=file_get_contents(ADMINSITEURL."print_purchase_order.php?id=".$id);

// $d=file_get_contents(ADMINSITEURL.'quotation_view_new_quotation.php?quotation_id='.$quotation_id.'');
// $d=file_get_contents(ADMINSITEURL.'quotation_view_new_quotation_new.php?quotation_id='.$quotation_id.'');

// $d=file_get_contents(ADMINSITEURL.'dashboard_statical_report_get_ajax.php?date='.$date.'&date1='.$date1.'&sales_id='.$sales_id);
$d = html_entity_decode($_REQUEST['rc']);
$d.=$string;
require('mpdf60/mpdf.php');



$mpdf = new mPDF('',    // mode - default ''

 'A4',    // format - A4, for example, default ''

 15,     // font size - default 0

 'sans-serif',    // default font family

 1,    // margin_left

 3,    // margin right

 3,     // margin top

 3,    // margin bottom

 0,     // margin header

 0,     // margin footer

 'P');  // L - landscape, P - portrait

$mpdf->WriteHTML($d);

$last_id = $quotation_id;
$quotation_no = $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'");
$flag = "Web";
$ctable = "quotation_detail";
$module_name = "Quotation";
$log_description = $module_name." ".$quotation_no." PDF Download By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,"","");

$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("quotation_detail","company_name","id='".$quotation_id."'",0)));
$quotation_no	= str_replace("/","-",stripslashes($db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'",0)));
 
	
//$fileName = "Quotation_".SITENAME."_".date('d_m_Y')."_".$quotation_no."_".$uname.'.pdf'; 
// $fileName = date('d_m_Y')."_"."Quotation_".$quotation_no.'pdf'; 
$fileName = "Dashboard_Statical_Report_".date('d_m_Y'); 
 

if(!is_dir($fileName)){

	mkdir(DASHBOARD_STATICAL_PDF.$fileName);

}

$pdf_file_path	= DASHBOARD_STATICAL_PDF.$fileName."/".$fileName.'.pdf';


if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

echo $pdf_file_path;


?>