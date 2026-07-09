<?php
$page_id=655;$page_slug='sales_executive_wise_report';
include("connect.php");
include('PHPExcel/IOFactory.php'); 
$file_name  = "Sales_Executive_Performance_Report"."_".date("d-m-Y")."_".strtotime("now").".xlsx";

$ctable   = "orders";
$ctable1  = "Orders";

// $Where = "";
$ctable_where = "";
$area         = $_REQUEST['area'];

$Where='';$Where1='';$Where2='';$Where3='';$Where4='';$Where5='';$Where6=''; $Where7='';$Where8=''; $Where11='';$Where12='';

$Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE '%".$db->clean($_REQUEST['searchName'])."%'":"";


if($_REQUEST['ToDate'] == "" || $_REQUEST['ToDate'] == NULL)
{
    $Where11=' AND MONTH(created_date)=MONTH(now()) ';
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type'] != 'null'){
    //$Where1 .= " AND id = '".$_REQUEST['type']."' ";
    $Where1 .= " AND FIND_IN_SET (type,'".$_REQUEST['type']."')";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id'] != NULL && $_REQUEST['sales_executive_id']!= "null"){
    $Where10 .= " AND id IN (".$_REQUEST['sales_executive_id'].") ";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state'] != NULL){
    $Where9 .= " AND state = '".$_REQUEST['state']."' ";
}

if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city'] != NULL){
    $Where9 .= " AND city = '".$_REQUEST['city']."' ";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id'] != NULL && $_REQUEST['sales_executive_id']!= "null"){
    $Where13 .= " AND id IN (".$_REQUEST['sales_executive_id'].") ";
}
// echo $Where; exit;

if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
    $Where2 = " AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where3 = " AND DATE(created_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where4 = " AND DATE(complain_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where5 = " AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where6 = " AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where7 .= " AND quotation_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where8 .= " AND DATE(followup_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where11 .= " AND DATE(created_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
    $Where12 .= " AND DATE(payment_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";


    // $Where8 .= " AND MONTH(created_date)=MONTH(now()) ";
}

if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
    $Where2 .= " AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where3 .= " AND DATE(created_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where4 .= " AND DATE(complain_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where5 .= " AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where6 .= " AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where7 .= " AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where8 .= " AND DATE(followup_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where11 .= " AND DATE(created_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
    $Where12 .= " AND DATE(payment_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";

    // $Where8 .= " AND MONTH(created_date)=MONTH(now()) ";

    // MONTH(date_joined)=MONTH(now()) AND YEAR(date_joined)=YEAR(now());
}
// echo $month = date('m'); exit();
$date = ($_REQUEST['FromDate']!="")?" - ".$_REQUEST['FromDate']." TO ":"";
$date .=($_REQUEST['ToDate']!="")?$_REQUEST['ToDate']:"";

//service_executive user not show condition start --//
    $CID=array();
    $SEID=array();
    $sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
    while($sales_type_d = mysqli_fetch_array($sales_type_r))
    {
        $SEID[] = $sales_type_d['name'];
    }
    $SEID=implode("','",$SEID);
    $Where .="  AND name NOT IN ('".$SEID."')  ";
//service_executive user not show condition end --//

$Query = "SELECT `sales_executive`.`id`, `sales_executive`.`name` AS name, `sales_executive`.`type` AS type, `sales_executive`.`state` AS state, `sales_executive`.`city` AS city, ( SELECT COUNT(*) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order, ( SELECT COUNT(*) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation, ( SELECT SUM(grand_total) FROM `orders` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_value, ( SELECT SUM(grand_total) FROM `orders` WHERE status=0 AND isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding_value, ( SELECT COUNT(*) FROM `orders` WHERE status=0 AND  isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where2." ) AS total_order_panding,( SELECT SUM(grand_total) FROM `quotation_detail` WHERE isDelete = 0 AND sales_id=`sales_executive`.`id` ".$Where7." ) AS total_quotation_value, ( SELECT COUNT(*) FROM `visit` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where3." ) AS total_visit, ( SELECT COUNT(*) FROM `complain` WHERE isDelete = 0 AND complain_assign_to=`sales_executive`.`id` ".$Where4." ) AS total_complain, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=0 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_inquiry, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=-1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_prospect, ( SELECT COUNT(*) FROM `no_order_inquiry` WHERE inquiry_lead_flag=1 AND  isDelete = 0 AND inquiry_assign_to=`sales_executive`.`id` ".$Where5." ) AS total_lead, ( SELECT COUNT(*) FROM `followup` WHERE isDelete = 0 AND user_id=`sales_executive`.`id` ".$Where8." ) AS total_followups,( SELECT COUNT(*) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice, ( SELECT SUM(grand_total) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_invoice_value ,( SELECT SUM(subtotal) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS subtotal_invoice_value ,( SELECT SUM(igst_amount) FROM `invoice_new` WHERE isDelete = 0 AND  FIND_IN_SET(`sales_executive`.`id`,sales_id) ".$Where6." ) AS total_igst_invoice_value, ( SELECT COUNT(*) FROM `executive` WHERE isDelete = 0 AND seid=`sales_executive`.`id` ".$Where11." ) AS new_customer_onbord, ( SELECT COUNT(*) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment, ( SELECT SUM(paid_amount) FROM `payment` WHERE isDelete = 0 AND sales_executive_id=`sales_executive`.`id` ".$Where12." ) AS total_payment_value FROM `sales_executive` WHERE isDelete = 0 ".$Where." ".$Where1." ".$Where9." ".$Where10." ORDER BY total_order_value DESC ;";
// echo $Query;exit();
$ctable_r = $db->rp_getQuery($Query,$Where);
// $ctable1_r = $db->rp_getData("executive","id,type_of_executive,price_list_id,company_name,cname,phone,mobile_no1,whatsapp_no,state,city,id",$ctable_where,"id DESC",0);

// Instantiate a new PHPExcel object 
$objPHPExcel = new PHPExcel();  

// Set the active Excel worksheet to sheet 0 
$objPHPExcel->setActiveSheetIndex(0);  
// Initialise the Excel row number 
$rowCount = 1;  

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
$column9  = 'J';
$column10  = 'K';
$column11  = 'L';
// $column12  = 'M';/**/
// $column12  = 'M';
// $column13  = 'N';

// $column14  = 'O';
// $column15  = 'P';
// $column16  = 'Q';
// $column17  = 'R';
// $column18  = 'S';
// $column19  = 'T';
// $column20  = 'U';
// $column21  = 'V';



 
  $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, "Sr No");
  $objPHPExcel->getActiveSheet()->setCellValue($column1.$rowCount, "Sales Person Type");
  // $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Price List");
  $objPHPExcel->getActiveSheet()->setCellValue($column2.$rowCount, "Sales Person Name");
  $objPHPExcel->getActiveSheet()->setCellValue($column3.$rowCount, "State");
  $objPHPExcel->getActiveSheet()->setCellValue($column4.$rowCount, "City");
  // $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount, "Total Prospect");
  // $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount, "Total Inquiry");
  // $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount, "Total Lead");
  // $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount, "Total Followups");
  $objPHPExcel->getActiveSheet()->setCellValue($column5.$rowCount,  "Total Quotation");

  $objPHPExcel->getActiveSheet()->setCellValue($column6.$rowCount,  "Total Quotation Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column7.$rowCount,  "Total Order");
  $objPHPExcel->getActiveSheet()->setCellValue($column8.$rowCount,  "Total Order Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column9.$rowCount,  "Total Invoice");

  $objPHPExcel->getActiveSheet()->setCellValue($column10.$rowCount,  "Total Invoice Without Gst Value");
  $objPHPExcel->getActiveSheet()->setCellValue($column11.$rowCount,  "Total Invoice With Gst Value");
  // $objPHPExcel->getActiveSheet()->setCellValue($column15.$rowCount,  "Total Pending Order");
  // $objPHPExcel->getActiveSheet()->setCellValue($column16.$rowCount,  "Total Pending Order Value");
  // $objPHPExcel->getActiveSheet()->setCellValue($column17.$rowCount,  "Total Visit");

  // $objPHPExcel->getActiveSheet()->setCellValue($column18.$rowCount,  "Total Complain");
  // $objPHPExcel->getActiveSheet()->setCellValue($column19.$rowCount,  "New-Customer-Onbord");
  // $objPHPExcel->getActiveSheet()->setCellValue($column20.$rowCount,  "Total Payment Collection");
  // $objPHPExcel->getActiveSheet()->setCellValue($column21.$rowCount,  "Total Payment Collection Value");
//end of adding column names  

$rowCount = 2; 
$count =0; 

while($row = mysqli_fetch_assoc($ctable_r))  
{  

             if($row['type_of_executive']=="1")
              {
                $type="Super Stockist";
              }
              else if($row['type_of_executive']=="2")
              {
                $type="Distributor";
              }
              else if($row['type_of_executive']=="3")
              {
                $type="Dealer";
              }
              else if($row['type_of_executive']=="4")
              {
                $type="B2B Customer";
              }
              else
              {
                $type="Dealer";
              }


              // if($ctable_d['type']=="sales_manager")
              // {
              //     $ctable_d['type']="Regional Sales Manager";
              // }

              // if($ctable_d['type']=="area_sales_manager")
              // {
              //     $ctable_d['type']="Business Development Manager";
              // }

              // if($ctable_d['type']=="sales_officer")
              // {
              //     $ctable_d['type']="Area Sales Manager";
              // }

              // if($ctable_d['type']=="sales_executive")
              // {
              //     $ctable_d['type']="Sales Officer";
              // }

              // $type_sales_executive_array = array(1=>"Super Stockist",2=>"Distributor",3=>"Dealer",4=>"B2B Customer");
              // echo stripslashes($ctable_d['type']);
   $status_array = array(1=>"Super Stockist",2=>"Distributor",3=>"Dealer",4=>"B2B Customer");
  // print_r($row);
  $count++;
  $column = 'A';
  for($j=0; $j<12;$j++)  
  {
    if($j==0)
    {
      $value = $count;
    }
    else if($j==1)
    {
      // $value =  stripslashes($type);
      // if($row['type']=="sales_manager")
      //   {
      //       $row['type']="Regional Sales Manager";
      //   }

      //   if($row['type']=="area_sales_manager")
      //   {
      //       $row['type']="Business Development Manager";
      //   }

      //   if($row['type']=="sales_officer")
      //   {
      //       $row['type']="Area Sales Manager";
      //   }

      //   if($row['type']=="sales_executive")
      //   {
      //       $row['type']="Sales Officer";
      //   }

      if($row['type']=="sales_manager")
      {
          $row['type']="Regional Sales Manager";
      }

      if($row['type']=="area_sales_manager")
      {
          $row['type']="Business Development Manager";
      }

      if($row['type']=="dispatch_sales_manager")
      {
          $row['type']="Dispatch Manager";
      }
      
      if($row['type']=="sales_officer")
      {
          $row['type']="Area Sales Manager";
      }
      
      if($row['type']=="sales_executive")
      {
          $row['type']="Sales Officer";
      }
      if($row['type']=="service_executive")
      {
          $row['type']="Service Executive";
      }
                        
      $value = $row['type'];
    } 
    // else if($j==2)
    // {
    //   $value=$db->rp_getValue("price_list","pricelist_name","id='".$row['price_list_id']."' AND isDelete=0");
    // }
    else if($j==2)
    {
      $value= stripslashes($row['name']);
       
    }
    else if($j==3)
    {
      $value= stripslashes($row['state']);
    }
    else if($j==4)
    {
      $value= stripslashes($row['city']);
    }
    // else if($j==5)
    // {
    //   $value=$row['total_prospect'];
    // }
    // else if($j==6)
    // {
    //   $value=$row['total_inquiry'];
    // }
    // else if($j==7)
    // {
    //   $value=$row['total_lead'];
    // }
    // else if($j==8)
    // {
    //   $value=$row['total_followups'];
    // }
    else if($j==5)
    {
      $value=$row['total_quotation'];
    }
    else if($j==6)
    {
      $value=$row['total_quotation_value'];
    }
    else if($j==7)
    {
      $value=$row['total_order'];
    }
    else if($j==8)
    {
      $value=$row['total_order_value'];
    }
    else if($j==9)
    {
      $value=$row['total_invoice'];
    }


    else if($j==10)
    {
      $value=$row['subtotal_invoice_value']-$row['total_igst_invoice_value'];
    }
    else if($j==11)
    {
      $value=$row['total_invoice_value'];
    }
    // else if($j==15)
    // {
    //   $value=$row['total_order_panding'];
    // }
    // else if($j==16)
    // {
    //   $value=$row['total_order_panding_value'];
    // }
    // else if($j==17)
    // {
    //   $value=$row['total_visit'];
    // }
    // else if($j==18)
    // {
    //   $value=$row['total_complain'];
    // }
    // else if($j==19)
    // {
    //   $value=$row['new_customer_onbord'];
    // }
    // else if($j==20)
    // {
    //   $value=$row['total_payment'];
    // }
    // else if($j==21)
    // {
    //   $value=$row['total_payment_value'];
    // }
    // else if($j==13)
    // {
    //   $value=$row['total_invoice'];
    // }

    // else if($j==9)
    // {
    //     $order_r=$db->rp_getData("orders","order_no,order_date,grand_total","customer_id='".$row['id']."' AND isDelete=0","id DESC","0",1);
    //       if($order_r)
    //       {
    //         $order_d=mysqli_fetch_assoc($order_r);
    //         $order_dt=date("d-m-Y",strtotime($order_d['order_date']));
    //          $value="#".$order_d['order_no']." , ".$order_dt." , ".$order_d['grand_total'];
    //       }
    //       else
    //       {
    //          $value="No Order Found";
    //       }
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
$arr = array("file_path"=>$file_path1);
require_once("disconnect.php");
echo json_encode($arr);
?>