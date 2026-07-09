<?php
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Self_Analysis_Report"."_".date("d-m-Y").".xlsx";
$ctable_where = "";
$ctable="self_analysis";

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){

$ctable_where1 .= " (first_name like '%".$_REQUEST['searchName']."%' OR middle_name like '%".$_REQUEST['searchName']."%' OR contact_no like '%".$_REQUEST['searchName']."%' OR surname like '%".$_REQUEST['searchName']."%') AND ";
$id_r=$db->rp_getValue("sales_executive_information","id",$ctable_where1."isDelete=0",0);
if($id_r!="")
{

  $ctable_where .= " (sales_executive_form_id like '%".$id_r."%') AND ";
}
else
{
  $ctable_where .= " (sales_executive_form_id like '%"."0"."%') AND ";
}

}

if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=null && $_REQUEST['sales_executive']!=undefined)
{
  $ctable_where .= "sales_executive_form_id='".$_REQUEST['sales_executive']."' AND ";
  $sales_executive=$_REQUEST['sales_executive'];
}


$ctable_where .= "isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;



if(isset($_REQUEST["page"])){

  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number

  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number

}else{

  $page_number = 1; //if there's no page number, set it to 1

}



$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages

$total_pages = ceil($get_total_rows/$item_per_page);



//get starting position to fetch the records

$page_position = (($page_number-1) * $item_per_page);



$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"",0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
$column   = 'A';
$column1  = 'B';
$column2  = 'C';
$column3  = 'D';
$column4  = 'E';

 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Questions ");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "Answers ");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "Created Date");
//end of adding column names  

$rowCount = 2; 
$count =0; 



while($row = mysqli_fetch_array($ctable_r))  
{ 
 // echo "sdjhjdkfhg";exit();
  $ques_r=explode(",", $row['questions_id']);
  $qans_r=explode(",",$row['answers']);
  $c_q=0;

  foreach ($ques_r as $quess) 
  {
        $count++;
        $column = 'A';
        for($j=0; $j<5;$j++)  
        {

          if($j==0)
          {
            $value = $count;
          }
          else if($j==1)
          {
            $sales_r=$db->rp_getData("sales_executive_information","*","isDelete=0 AND id='".$row['sales_executive_form_id']."'");
                $sales_d=mysqli_fetch_array($sales_r);

                $value= $sales_d['first_name']." ".$sales_d['middle_name']." ".$sales_d['surname']."-".$sales_d['contact_no'];
          } 
          else if($j==2)
          {
            $value = $db->rp_getValue("self_analysis_master","questions","isDelete=0 AND id='".$ques_r[$c_q]."'");
          }
          else if($j==3)
          {
            $value=$qans_r[$c_q];
             $c_q++;
          }
          else if($j==4)
          {
            $value = date('d-m-Y',strtotime($row['created_date']));
          }
           
          
          
          $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
          $column++;
        }  
        $rowCount++;
}

}
// Redirect output to a client’s web browser (Excel5) 

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=".$file_name);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(SELF_ANALYSIS_FILES.$file_name); 
$file_path1 = trim(ADMINFOLDER."/report/"."self_analysis/".$file_name);
$arr = array("file_path"=>$file_path1);
echo json_encode($arr);
?>