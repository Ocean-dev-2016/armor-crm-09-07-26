  <?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance".date('d_m_Y')."_".strtotime("now").".xlsx";
$sales_id = $_REQUEST['sales_id'];
$mode = $_REQUEST['mode'];



   $FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 ","id DESC",0);
  if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
  {
    $FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND followup_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND followup_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
  }
  else
  {
    $FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 ","id DESC",0);
  }


// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 








$rowCount = 1; 
$column   = 'A';


  if($_REQUEST['sales_id']!="")
   {
      // echo "string";exit();
      // $source_id= $_REQUEST['source_id'];
  $SalesExecutiveType = $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'");  
      // echo "$source";exit();
        // echo "Source Medium=".$source.",";
   }
   if($_REQUEST['sales_id']!="")
   {
    // echo $_REQUEST['df'];
      $SalesExecutiveName= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'");
        // echo "Inquiry Date = ".$date.",";
   }

   
            if($SalesExecutiveName!="")
            {
              $SalesExecutiveName_r=" ".$SalesExecutiveName." , ";
            }
            $date=  date("d-m-Y h:i a");
            if($date!="")
            {
                // echo $df;exit();

              $date_r=" Date & Time = ".$date."  ";
            }
            // if($SalesExecutiveCity!="")
            // {
            //   $SalesExecutiveCity_r=" Sales Officer City  =".$SalesExecutiveCity." , ";
            // }
            
             
            

$downloadby = $SalesExecutiveType_r."  ".$SalesExecutiveName_r."  ".$date_r."  ".$SalesExecutiveCity_r."  ".$inquiryassignto_r."  ".$customer_type_r."  ".$industry_type_r."  ".$country_r." ".$state_r." ".$city_r."  ".$Query_desc;




$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:H1');
$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Download  By : ".$downloadby."");





$rowCount = 2;  

//start of printing column names as names of mysqli fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
$column6  = 'G';
$column7  = 'H';
// $column8  = 'I';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Date and Time");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Description");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Through");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Response Date");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Response");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "status");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Type of Follow up");
// $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Status");







// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Status");

//end of adding column names  






$rowCount = 3; 
$count =0;
$Followup_status_array = array("0"=>"Cancel","1"=>"Responded");
$followupthrough_array = array("1"=>"Call","2"=>"Sms","3"=>"Email");
while($FollowupD = mysqli_fetch_assoc($FollowupR))  
{ 

  if($FollowupD['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '0'",0))
                      {
                          $slag = "Inquiry";
                      }

              else if ($FollowupD['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '-1'",0)) {
                  $slag= "Prospects";
              }

              else if ( $FollowupD['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '1'",0)) {
                  $slag= "Leads";
              }

            else if ($FollowupD['reference_table'] == "sales_executive") {
              $slag = "Sales Officer";
            }
            else if ($FollowupD['reference_table'] == "customer_inquiry") {
              
              $slag = "Customer Inquiry";
            }
            else if ($FollowupD['reference_table'] == "quotation_followup") {
              
              $slag = "Quotation";
            }
            else if ($FollowupD['reference_table'] == "executive") {
              
              $slag = "Executive";
            }
            else if ($FollowupD['reference_table'] == "customer_inquiry") {
              
              $slag = "Executive";
            }
            else if ($FollowupD['reference_table'] == "quotation_detail") {
              
              $slag = "Quotation";
            }
    $count++;
    $column = 'A';
    for($j=0; $j<8;$j++)  
    {
        if($j==0)
        {
            $value = $count;
        }
        else if($j==1)
        {
          if($FollowupD['followup_date']!="1970-01-01" && $FollowupD['followup_date']!="0000-00-00" ){ $value = date('d-m-Y',strtotime($FollowupD['followup_date']));}else{$value = ""; }
        }
        else if($j==2)
        {
          $value = $FollowupD['description']; 
        }
        else if($j==3)
        {
           
        $value =   $followupthrough_array[$FollowupD['through']];
                
           
        }
        else if($j==4)
        {
           if($FollowupD['response_date']!="1970-01-01" && $FollowupD['response_date']!="0000-00-00 00:00:00" ){ $value = date('d-m-Y',strtotime($FollowupD['response_date']));}else{$value = ""; }
        }
        else if($j==5)
        {
          $value = $FollowupD['response'];
        }
         else if($j==6)
        {
          $value = $Followup_status_array[$FollowupD['status']];
        }
         else if($j==7)
        {
          $value = $slag;
        }
        //  else if($j==8)
        // {
        //   $value = $complin_array[$ComplainD['status']];
        // }
         
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  }  
  $rowCount++;
}

// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>trim($file_path1));
require_once("disconnect.php");
echo json_encode($arr);
?>