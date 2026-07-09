  <?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance".date('d_m_Y')."_".strtotime("now").".xlsx";
$sales_id = $_REQUEST['sales_id'];
$mode = $_REQUEST['mode'];



   if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
    {
        $ComplainR = $db->rp_getData("complain","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
    }
    else
    {
        $ComplainR = $db->rp_getData("complain","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
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




$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
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
$column8  = 'I';

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
$objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Complain No.");
$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Date and Time");
$objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Customer Name");
$objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Source of complain");
$objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Complain Category");
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Complain Sub Category");
$objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Description");
$objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Status");



// $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Status");

//end of adding column names  

$rowCount = 3; 
$count =0;
$complin_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
while($ComplainD = mysqli_fetch_assoc($ComplainR))  
{ 
    $count++;
    $column = 'A';
    for($j=0; $j<9;$j++)  
    {
        if($j==0)
        {
            $value = $count;
        }
        else if($j==1)
        {
          $value ="#".$ComplainD['complain_no'];
        }
        else if($j==2)
        {
          $value = date("d-m-Y h:i A",strtotime($ComplainD['created_date']));
        }
        else if($j==3)
        {
           
        $value =  $db->rp_getValue("executive","cname","id='".$ComplainD['customer_id']."'").$db->rp_getValue("executive","phone","id='".$ComplainD['customer_id']."'");
                
           
        }
        else if($j==4)
        {
          $value = $complain_type_array[$ComplainD['complain_type']];
        }
        else if($j==5)
        {
          $value = $db->rp_getValue("complain_category","name","id='".$ComplainD['complain_cat_id']."'");
        }
         else if($j==6)
        {
          $value = $db->rp_getValue("complain_sub_category","name","id='".$ComplainD['complain_subcat_id']."'");
        }
         else if($j==7)
        {
          $value = $ComplainD['remark'];
        }
         else if($j==8)
        {
          $value = $complin_array[$ComplainD['status']];
        }
         
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
    $column++;
  }  
  $rowCount++;
}

// Redirect output to a client’s web browser (Excel5) 
header("Content-Type: applicat ion/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(INQUIRY_REPORT_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/inquiry_documents/".$file_name);
$arr = array("file_path"=>trim($file_path1));
require_once 'disconnect.php'; 
echo json_encode($arr);
?>
