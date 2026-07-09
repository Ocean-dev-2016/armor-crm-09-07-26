  <?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance".date('d_m_Y')."_".strtotime("now").".xlsx";
$sales_id = $_REQUEST['sales_id'];
$mode = $_REQUEST['mode'];

if($mode=="prospect")
{
    $ctable1_r = $db->rp_getData("no_order_inquiry","*","sales_executive_id='".$_REQUEST['sales_id']."' AND inquiry_lead_flag='-1' AND isDelete=0","id DESC",0);
}
else if($mode=="inquiry")
{
    $ctable1_r = $db->rp_getData("no_order_inquiry","*","sales_executive_id='".$_REQUEST['sales_id']."' AND inquiry_lead_flag='0' AND isDelete=0","id DESC",0);
}
else
{
    $ctable1_r = $db->rp_getData("no_order_inquiry","*","sales_executive_id='".$_REQUEST['sales_id']."' AND inquiry_lead_flag='1' AND isDelete=0","id DESC",0);
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




$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:G1');
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

$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
if($mode=="prospect")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Raw Data No");
}
else if($mode=="inquiry")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Inquiry No");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Lead No");
}

$objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Source Of Medium");

if($mode=="prospect")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Raw Data Date");
}
else if($mode=="inquiry")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Inquiry Date");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Lead Date");
}

if($mode=="prospect")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Raw Data Taken By");
}
else if($mode=="inquiry")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Inquiry Taken By");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Lead Taken By");
}

if($mode=="prospect")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Raw Data Assign To");
}
else if($mode=="inquiry")
{
    $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Inquiry Assign To");
}
else
{
    $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Lead Assign To");
}
$objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Status");

//end of adding column names  

$rowCount = 3; 
$count =0;
$status_array = array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","5"=>"Cold","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");
while($row = mysqli_fetch_assoc($ctable1_r))  
{ 
    $count++;
    $column = 'A';
    for($j=0; $j<7;$j++)  
    {
        if($j==0)
        {
            $value = $count;
        }
        else if($j==1)
        {
          $value = "#INQ/".$row['id'];
        }
        else if($j==2)
        {
          $value = $db->rp_getValue("source_of_inquiry","name","id='".$row['source_of_inquiry']."'");
        }
        else if($j==3)
        {
            if($mode=="prospect")
            {
                if($row['created_date']!="1970-01-01" && $row['created_date']!="0000-00-00" )
                { 
                    $value =  date('d-m-Y',strtotime($row['created_date']));
                }
                else
                {
                    $value = ""; 
                }
            }
            else
            {
                if($row['inquiry_date']!="1970-01-01" && $row['inquiry_date']!="0000-00-00" )
                { 
                    $value =  date('d-m-Y',strtotime($row['inquiry_date']));
                }
                else
                {
                    $value = ""; 
                }      
            }
        }
        else if($j==4)
        {
          $value = $db->rp_getValue("sales_executive","name","id='".$row['inquiry_created_by']."'");
        }
        else if($j==5)
        {
          $value = $db->rp_getValue("sales_executive","name","id='".$row['inquiry_assign_to']."'");
        }
         else if($j==6)
        {
          $value = $status_array[$row['status']];
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
require_once("disconnect.php");
echo json_encode($arr);
?>