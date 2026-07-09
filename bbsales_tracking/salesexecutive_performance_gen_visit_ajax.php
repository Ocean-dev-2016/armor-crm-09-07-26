  <?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance".date('d_m_Y')."_".strtotime("now").".xlsx";
$sales_id = $_REQUEST['sales_id'];
$mode = $_REQUEST['mode'];



   if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
    {
        $VisitR = $db->rp_getData("visit","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'","id DESC",0);
    }
    else
    {
        $VisitR = $db->rp_getData("visit","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
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




$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Download  By : ".$downloadby."");



$rowCount = 2;  

//start of printing column names as names of mysqli fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';
$column5  = 'F';
// $column6  = 'G';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Customer Name");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Mobile No");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Date & Time");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Address");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Remark");







// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Status");

//end of adding column names  

$rowCount = 3; 
$count =0;
$status_array = array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","5"=>"Cold","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"11");
while($row = mysqli_fetch_assoc($VisitR))  
{ 
    $count++;
    $column = 'A';
    for($j=0; $j<6;$j++)  
    {
        if($j==0)
        {
            $value = $count;
        }
        else if($j==1)
        {
          $value = $db->rp_getValue("executive","cname","id='".$row['customer_id']."'");
        }
        else if($j==2)
        {
          $value = $db->rp_getValue("executive","phone","id='".$row['customer_id']."'");
        }
        else if($j==3)
        {
           
        $value =  date("d-m-Y H:i:s",strtotime($row['created_date']));
                
           
        }
        else if($j==4)
        {
          $value = $row['app_address'];
        }
        else if($j==5)
        {
          $value = $row['remark'];
        }
         
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
require_once 'disconnect.php'; 
echo json_encode($arr);
?>