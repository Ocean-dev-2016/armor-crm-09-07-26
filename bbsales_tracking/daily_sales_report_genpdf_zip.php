<?php

/*
 * @author Ravi Patel
 */

$page_id=597;$page_slug='daily_sales_report_page';
require_once("connect_in.php");
require('mpdf60/mpdf.php');

// $date = date("Y-m-d-h-i-s");
$date = $_REQUEST['date'];
// $date1 = $_REQUEST['date1'];
// $sales_id = $_REQUEST['sales_id'];
// echo "hii"; exit;
$zip = new ZipArchive();

$get_sales=$db->rp_getData("sales_executive","*","isDelete=0","",0);
while($get_sales_d = mysqli_fetch_assoc($get_sales))
{
    if($get_sales_d['sm_id']==0)
    {
    $type="Regional Sales Manager";
    }
    else if($get_sales_d['sm_id']!=0 && $get_sales_d['asm_id']==0)
    {
    $type="Business Development Manager";
    }
    else if($get_sales_d['sm_id']!=0 && $get_sales_d['asm_id']!=0 && $get_sales_d['so_id']==0)
    {
      $type="Area Sales Manager";
    }
    else
    {
      $type="Sales Officer";
    }

    $ctable   = "attendance";
    $ctable1  = "visit";
    $ctable2  = "no_order_inquiry";
    $ctable3  = "followup";
    $ctable4  = "quotation_detail";
    $ctable5  = "orders";
    $ctable6  = "followup";
    $ctable7  = "dispatch_detail";
    $ctable8  = "packing_slip";
    $ctable9  = "invoice_new";
    $ctable10  = "complain";
    $ctable11  = "expense";
    $ctable12  = "leave_request";
    $ctable13  = "executive";

    /*attendance*/
    $ctable_where = "";
    $ctable_where .="sales_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where .= " AND DATE(date_time) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where .= " AND DATE(date_time) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
    /*attendance*/

    /*Prospects*/
    $ctable_where_prospect = "";
    $ctable_where_prospect .="sales_executive_id=".$get_sales_d['id']." ";
    $ctable_where_prospect .= " AND DATE(datetime) >= '".date("Y-m-d",strtotime($date))."' ";
    $ctable_where_prospect .= " AND DATE(datetime) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '-1'";
    $ctable_r3 = $db->rp_getData($ctable2,"*",$ctable_where_prospect,"id DESC",0);
    /*Prospects*/

    /*Prospects followup*/
    $ctable_where_Prospects_followup = "";
    $ctable_where_Prospects_followup .="user_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_Prospects_followup .= " AND DATE(followup_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_Prospects_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_r11 = $db->rp_getData($ctable6,"*",$ctable_where_Prospects_followup,"id DESC",0);
    /*Prospects followup*/

    /*inquiry*/
    $ctable_where_inquiry = "";
    $ctable_where_inquiry .="sales_executive_id=".$get_sales_d['id']." ";
    $ctable_where_inquiry .= " AND DATE(datetime) >= '".date("Y-m-d",strtotime($date))."' ";
    $ctable_where_inquiry .= " AND DATE(datetime) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '0'";
    $ctable_r4 = $db->rp_getData($ctable2,"*",$ctable_where_inquiry,"id DESC",0);
    /*inquiry*/

    /*inquiry followup*/
    $ctable_where_Inquiry_followup = "";
    $ctable_where_Inquiry_followup .="user_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_Inquiry_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_Inquiry_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_Inquiry_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_r9 = $db->rp_getData($ctable6,"*",$ctable_where_Inquiry_followup,"id DESC",0);
    /*inquiry followup*/

    /*leads*/
    $ctable_where_lead = "";
    $ctable_where_lead .="sales_executive_id=".$get_sales_d['id']." ";
    $ctable_where_lead .= " AND DATE(datetime) >= '".date("Y-m-d",strtotime($date))."' ";
    $ctable_where_lead .= " AND DATE(datetime) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '1'";
    $ctable_r5 = $db->rp_getData($ctable2,"*",$ctable_where_lead,"id DESC",0);
    /*leads*/

    /*leads followup*/
    $ctable_where_lead_followup = "";
    $ctable_where_lead_followup .="user_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_lead_followup .= " AND DATE(followup_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_lead_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_r10 = $db->rp_getData($ctable6,"*",$ctable_where_lead_followup,"id DESC",0);
    /*leads followup*/

    /*total followup*/
    $ctable_where_followup = "";
    $ctable_where_followup .="user_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_followup .= " AND DATE(followup_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r6 = $db->rp_getData($ctable3,"*",$ctable_where_followup,"id DESC",0);
    /*total followup*/

    /*quotation*/
    $ctable_where_quotation = "";
    $ctable_where_quotation .="sales_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_quotation .= " AND DATE(quotation_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_quotation .= " AND DATE(quotation_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r7 = $db->rp_getData($ctable4,"*",$ctable_where_quotation,"id DESC",0);
    /*quotation*/

    /*quotation followup*/
    $ctable_where_Q_followup = "";
    $ctable_where_Q_followup .="user_id=".$get_sales_d['id']." AND reference_table='quotation_detail' AND isDelete=0";
    $ctable_where_Q_followup .= " AND DATE(followup_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_Inquiry_followup .= " OR DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_r12 = $db->rp_getData($ctable6,"*",$ctable_where_Q_followup,"id DESC",0);
    /*quotation followup*/

    /*orders*/
    $ctable_where_orders = "";
    $ctable_where_orders .="sales_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_orders .= " AND DATE(order_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_orders .= " AND DATE(order_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r8 = $db->rp_getData($ctable5,"*",$ctable_where_orders,"id DESC",0);
    /*orders*/

    /*dispatch*/
    $ctable_where_dispatch = "";
    $ctable_where_dispatch .="sales_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_dispatch .= " AND DATE(dispatch_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_dispatch .= " AND DATE(dispatch_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r13 = $db->rp_getData($ctable7,"*",$ctable_where_dispatch,"id DESC",0);
    /*dispatch*/

    /*packing slip*/
    $ctable_where_packingslip = "";
    $sales_dispatch_ids=array();
    $dispatch_r=$db->rp_getData("dispatch_detail","*","isDelete=0 AND sales_id='".$get_sales_d['id']."' ");
    while($dispatch_d=mysqli_fetch_assoc($dispatch_r))
    {
      $sales_dispatch_ids[]=$dispatch_d['id'];
    }
    // $ctable_where_packingslip .="sales_id=".$sales_id." AND isDelete=0";
    $ctable_where_packingslip .=" dispatch_id IN (".implode(",",$sales_dispatch_ids).") AND isDelete=0";
    $ctable_where_packingslip .= " AND DATE(packing_slip_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_packingslip .= " AND DATE(packing_slip_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r14 = $db->rp_getData($ctable8,"*",$ctable_where_packingslip,"id DESC",0);
    /*packing slip*/

    /*invoice*/
    $ctable_where_invoice = "";
    $ctable_where_invoice .="sales_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_invoice .= " AND DATE(invoice_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_invoice .= " AND DATE(invoice_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r15 = $db->rp_getData($ctable9,"*",$ctable_where_invoice,"id DESC",0);
    /*invoice*/

    /*complain*/
    $ctable_where_complain = "";
    $ctable_where_complain .="user_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_complain .= " AND DATE(complain_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_complain .= " AND DATE(complain_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r16 = $db->rp_getData($ctable10,"*",$ctable_where_complain,"id DESC",0);
    /*complain*/

    /*visit*/
    $ctable_where_visit = "";
    $ctable_where_visit .="user_id=".$get_sales_d['id']." AND isDelete=0";
    // $ctable_where_visit .= " AND DATE(created_date) = '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_visit .= " AND DATE(created_date) >= '".date("Y-m-d",strtotime($date))."' ";
    $ctable_where_visit .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r2 = $db->rp_getData($ctable1,"*",$ctable_where_visit,"id DESC",0);
    /*visit*/

    /*expense*/
    $ctable_where_expense = "";
    $ctable_where_expense .="sales_executive_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_expense .= " AND DATE(expense_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_expense .= " AND DATE(expense_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r17 = $db->rp_getData($ctable11,"*",$ctable_where_expense,"id DESC",0);
    /*expense*/

    /*leave request*/
    $ctable_where_leaverequest = "";
    $ctable_where_leaverequest .="sales_executive_id=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_leaverequest .= " AND DATE(start_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_leaverequest .= " AND DATE(start_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r18 = $db->rp_getData($ctable12,"*",$ctable_where_leaverequest,"id DESC",0);
    /*leave request*/

    /*new customer*/
    $ctable_where_newcustomer = "";
    $ctable_where_newcustomer .="seid=".$get_sales_d['id']." AND isDelete=0";
    $ctable_where_newcustomer .= " AND DATE(created_date) >= '".date("Y-m-d",strtotime($date))."'";
    $ctable_where_newcustomer .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."'";
    $ctable_r19 = $db->rp_getData($ctable13,"*",$ctable_where_newcustomer,"id DESC",0);
    /*new customer*/


    $in_time = $db->rp_getData("attendance","date_time","sales_id='".$get_sales_d['id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($date))."' AND inout_status='In'","",0);
    $in = array();
    while($in_time_d = mysqli_fetch_assoc($in_time))
    {
        $in[] = $in_time_d['date_time'];
    }
    // print_r($in); 

    $out_time = $db->rp_getData("attendance","date_time","sales_id='".$get_sales_d['id']."' AND DATE(date_time)='".date('Y-m-d',strtotime($date))."' AND inout_status='Out'","",0);
    $out = array();
    while($out_time_d = mysqli_fetch_assoc($out_time))
    {
        $out[] = $out_time_d['date_time'];
    }
    // print_r($out);
    $diffrence ="00:00:00";
    $first_in=$in[0];
    $first_out=$out[0];
    $total_in=sizeof($in);
    $total_out=sizeof($out);
    $last_out=$out[$total_out-1];
    $total_in_out=$total_in+$total_out;

    for($i=0;$i<sizeof($in);$i++)
    {
        for($j=0;$j<sizeof($out);$j++)
        {
            if($i==$j)
            { 
                print_r($in[$i]); exit;
                $in_date = date("Y-m-d",strtotime($in[$i]));        
                $out_date = date("Y-m-d",strtotime($out[$j]));       
                if(strtotime($in_date)==strtotime($out_date))
                {

                    $in_time=$in[$i];
                    $out_time=$out[$j];                   
                    // Formulate the Difference between two dates                           
                    $diff = abs(strtotime($out_time) - strtotime($in_time));  
                    
                    // To get the year divide the resultant date into 
                    // total seconds in a year (365*60*60*24) 
                    $years = floor($diff / (365*60*60*24));  

                    // To get the month, subtract it with years and 
                    // divide the resultant date into 
                    // total seconds in a month (30*60*60*24) 
                    $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 

                    // To get the day, subtract it with years and  
                    // months and divide the resultant date into 
                    // total seconds in a days (60*60*24) 
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 

                    // To get the hour, subtract it with years,  
                    // months & seconds and divide the resultant 
                    // date into total seconds in a hours (60*60) 
                    $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  

                    // To get the minutes, subtract it with years, 
                    // months, seconds and hours and divide the  
                    // resultant date into total seconds i.e. 60 
                    $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  

                    // To get the minutes, subtract it with years, 
                    // months, seconds, hours and minutes  
                    $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  

                    $diff=$hours.":".$minutes.":".$seconds;
                    $secs = strtotime($diffrence)-strtotime("00:00:00");
                    $diffrence = date("H:i:s",strtotime($diff)+$secs)." ";
                }
            }           
        }
        if(sizeof($in)>sizeof($out) && $i+1==sizeof($in))  
        {
            $current_time=date("H:i:s");
            $running_in_time=date("H:i:s",strtotime($in[$i]));

            $diff = abs(strtotime($current_time) - strtotime($running_in_time));  
                
            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 
            $years = floor($diff / (365*60*60*24));  

            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 
            $months = floor(($diff - $years * 365*60*60*24)  / (30*60*60*24)); 

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 
            $hours = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24)/(60*60));  

            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 
            $minutes = floor(($diff - $years * 365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60)/ 60);  

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($diff-$years*365*60*60*24-$months*30*60*60*24-$days*60*60*24-$hours*60*60-$minutes*60));  

            $diff=$hours.":".$minutes.":".$seconds;


            $secs = strtotime($diffrence)-strtotime("00:00:00");
            $diffrence = date("H:i:s",strtotime($diff)+$secs)." Running";
        }
    }


    $html_code = '

    <html>
        <head>
            <style type="text/css">
                table{
                    width:100%
                }
                table , td, th {
                    border: 1px solid #595959;
                    border-collapse: collapse;
                }
                td, th{
                    padding:6px;
                    font-size: 12px;
                }
                .no-border td{
                    border:none!important;   
                }
            </style>
        </head>
        <body>

            <h4><b>Daily Report</b></h4>
            <table>
                <tr class="no-border">
                    <td>Name</td>
                    <td>:-</td>
                    <td>'.$get_sales_d["name"].'</td>
                    <td>First In</td>
                    <td>:-</td>
                    <td>';
                    if(!empty($in))
                    {
                        $html_code.=date("d-m-Y h:i A",strtotime($first_in));
                    }
                    else
                    {
                        $html_code.=" - ";
                    }
                    $html_code.='</td>
                </tr>
                <tr class="no-border">
                    <td>Type</td>
                    <td>:-</td>
                    <td>'.$type.'</td>
                    <td>Last Out</td>
                    <td>:-</td>
                    <td>';
                    if(!empty($out))
                    {
                        $html_code.=date("d-m-Y h:i A",strtotime($last_out));
                    }
                    else
                    {
                        $html_code.=" - ";
                    }
                    $html_code.='</td>
                </tr>
                <tr class="no-border">
                    <td>Mobile No.</td>
                    <td>:-</td>
                    <td>'.$get_sales_d["phone"].'</td>
                    <td>Total In & Out</td>
                    <td>:-</td>
                    <td>'.$total_in_out.'</td>
                </tr>
                <tr class="no-border">
                    <td>Total Visit</td>
                    <td>:-</td>
                    <td>'.$db->rp_getTotalRecord($ctable1,$ctable_where_visit).'</td>
                    <td>Total Working Time</td>
                    <td>:-</td>
                    <td>'.$diffrence.'</td>
                </tr>
            </table>';

            /*attendance*/
            $html_code.='<h4><b>Attendance</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>In/Out</th>
                        <th>Date and Time</th>
                        <th>Address</th>
                        <th>Image</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r)>0)
                {
                    $count = 0;  
                    while($ctable_d = mysqli_fetch_array($ctable_r))
                    {
                        if ($ctable_d['image_path']!="" && file_exists(ATTENDANCE.$ctable_d['image_path'])) {
                            $img = ATTENDANCE.$ctable_d['image_path'];
                        }
                        else
                        {
                            $img = $ctable_d['image_path'] = DEFAULTIMG;
                        }
                        $count++;

                        $html_code.='<tr>
                            <td>'.$count.'</td>
                            <td>'.$ctable_d['inout_status'].'</td>
                            <td>'.date("d-m-Y H:i:s a",strtotime($ctable_d['created_date'])).'</td>
                            <td>'.$ctable_d['app_address'].'</td>
                            <td><img src="'.$img.'" style="height: 80px;border-radius: 5px;border:1px solid #909090;"></td>
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="5" align="center">No Attendance Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*attendance*/

            /*prospect*/
            $html_code.='<h4><b>Prospects ('.$db->rp_getTotalRecord($ctable2,$ctable_where_prospect).')</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Company Name</th>
                        <th>Persoan Name</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Inquiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r3)>0)
                {
                    $count = 0;            
                    while($ctable_d3 = mysqli_fetch_array($ctable_r3))
                    {
                        $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"Dipson Working","-1"=>"Not Interested");
                        $count++;
                        $html_code.='<tr>
                            <td>'.$count.'</td>                
                            <td>#INQ/'.$ctable_d3['id'].'</td>
                            <td>'.$ctable_d3['company_name'].'</td>
                            <td>'.$ctable_d3['person_name'].'</td>              
                            <td>'.$ctable_d3['mobile_number'].'</td>              
                            <td>'.$ctable_d3['country'].'</td>              
                            <td>'.$ctable_d3['state'].'</td>              
                            <td>'.$ctable_d3['city'].'</td>              
                            <td>';
                                if($ctable_d3['datetime']!="0000-00-00 00:00:00")
                                { 
                                    $html_code.=date('d-m-Y',strtotime($ctable_d3['datetime'])); 
                                } 
                                else
                                { 
                                    $html_code.="";
                                }
                            $html_code.='</td>   
                            <td>'.$inquiry_status_array[$ctable_d3['status']].'</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="10" align="center">No Prospect Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*prospect*/

            /*prospect followup*/
            $html_code.='<h4><b>Prospects Followup</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Customer Name</th>
                        <th>Mobile No</th>
                        <th>Sales Persoan Name</th>
                        <th>Description</th>
                        <th>Through</th>
                        <th>Date and Time</th>
                        <th>Response</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r11)>0)
                {
                    $count = 0;            
                    while($ctable_dP = mysqli_fetch_array($ctable_r11))
                    {
                        $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                        $Prospects_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0);
                        if($Prospects_ID)
                        {
                            $html_code.='<tr>
                                <td>'. ++$count.'</td>                
                                <td>#INQ/'.$db->rp_getValue("no_order_inquiry","id","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0).'</td>
                                <td>';
                                    if($ctable_dP['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","cname","id='".$ctable_dP['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dP['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0);
                                    }
                                $html_code.='</td> 
                                <td>';
                                    if($ctable_dP['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","phone","id='".$ctable_dP['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dP['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dP['reference_id']."' AND inquiry_lead_flag = '-1'",0);
                                    }
                                $html_code.='</td>              
                                <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_dP['user_id']."'").'</td>              
                                <td>'.$ctable_dP['description'].'</td>              
                                <td>'.$msg[$ctable_dP['through']].'</td>              
                                <td>';
                                    if($ctable_dP['followup_date']=="0000-00-00 00:00:00")
                                    { 
                                        $html_code.="";
                                    } 
                                    else
                                    { 
                                        $html_code.=date('d-m-Y H:i:s a',strtotime($ctable_dP['followup_date'])); 
                                    }
                                $html_code.='</td>   
                                <td>';
                                    if($ctable_dP['reference_table']=="no_order_inquiry")
                                    {
                                        $html_code.=$ctable_dP['response']; 
                                    } 
                                $html_code.='</td>
                            </tr>';
                        }
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Prospect Followup Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*prospect followup*/

            /*inquiry*/
            $html_code.='<h4><b>Inquiry ('.$db->rp_getTotalRecord($ctable2,$ctable_where_inquiry).')</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Company Name</th>
                        <th>Persoan Name</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Inquiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r4)>0)
                {
                    $count = 0;            
                    while($ctable_d4 = mysqli_fetch_array($ctable_r4))
                    {
                        $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"Dipson Working","-1"=>"Not Interested");
                        $count++;
                        $html_code.='<tr>
                            <td>'.$count.'</td>                
                            <td>#INQ/'.$ctable_d4['id'].'</td>
                            <td>'.$ctable_d4['company_name'].'</td>
                            <td>'.$ctable_d4['person_name'].'</td>              
                            <td>'.$ctable_d4['mobile_number'].'</td>              
                            <td>'.$ctable_d4['country'].'</td>              
                            <td>'.$ctable_d4['state'].'</td>              
                            <td>'.$ctable_d4['city'].'</td>              
                            <td>';
                                if($ctable_d4['datetime']!="0000-00-00 00:00:00")
                                { 
                                    $html_code.=date('d-m-Y',strtotime($ctable_d4['datetime'])); 
                                } 
                                else
                                { 
                                    $html_code.="";
                                }
                            $html_code.='</td>   
                            <td>'.$inquiry_status_array[$ctable_d4['status']].'</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="10" align="center">No Inquiry Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*inquiry*/

            /*inquiry followup*/
            $html_code.='<h4><b>Inquiry Followup</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Customer Name</th>
                        <th>Mobile No</th>
                        <th>Sales Persoan Name</th>
                        <th>Description</th>
                        <th>Through</th>
                        <th>Date and Time</th>
                        <th>Response</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r9)>0)
                {
                    $count = 0;            
                    while($ctable_dF = mysqli_fetch_array($ctable_r9))
                    {
                        $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                        $Inquiry_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
                        if($Inquiry_ID)
                        {
                            $html_code.='<tr>
                                <td>'. ++$count.'</td>                
                                <td>#INQ/'.$db->rp_getValue("no_order_inquiry","id","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0).'</td>
                                <td>';
                                    if($ctable_dF['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","cname","id='".$ctable_dF['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dF['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
                                    }
                                $html_code.='</td> 
                                <td>';
                                    if($ctable_dF['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","phone","id='".$ctable_dF['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dF['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dF['reference_id']."' AND inquiry_lead_flag = '0'",0);
                                    }
                                $html_code.='</td>              
                                <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_dF['user_id']."'").'</td>              
                                <td>'.$ctable_dF['description'].'</td>              
                                <td>'.$msg[$ctable_dF['through']].'</td>              
                                <td>';
                                    if($ctable_dF['followup_date']=="0000-00-00 00:00:00")
                                    { 
                                        $html_code.="";
                                    } 
                                    else
                                    { 
                                        $html_code.=date('d-m-Y H:i:s a',strtotime($ctable_dF['followup_date'])); 
                                    }
                                $html_code.='</td>   
                                <td>';
                                    if($ctable_dF['reference_table']=="no_order_inquiry")
                                    {
                                        $html_code.=$ctable_dF['response']; 
                                    } 
                                $html_code.='</td>
                            </tr>';
                        }
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Inquiry Followup Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*inquiry followup*/

            /*leads*/
            $html_code.='<h4><b>Leads ('.$db->rp_getTotalRecord($ctable2,$ctable_where_lead).')</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Company Name</th>
                        <th>Persoan Name</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Inquiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r5)>0)
                {
                    $count = 0;            
                    while($ctable_d5 = mysqli_fetch_array($ctable_r5))
                    {
                        $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"Dipson Working","-1"=>"Not Interested");
                        $count++;
                        $html_code.='<tr>
                            <td>'.$count.'</td>                
                            <td>#INQ/'.$ctable_d5['id'].'</td>
                            <td>'.$ctable_d5['company_name'].'</td>
                            <td>'.$ctable_d5['person_name'].'</td>              
                            <td>'.$ctable_d5['mobile_number'].'</td>              
                            <td>'.$ctable_d5['country'].'</td>              
                            <td>'.$ctable_d5['state'].'</td>              
                            <td>'.$ctable_d5['city'].'</td>              
                            <td>';
                                if($ctable_d5['datetime']!="0000-00-00 00:00:00")
                                { 
                                    $html_code.=date('d-m-Y',strtotime($ctable_d5['datetime'])); 
                                } 
                                else
                                { 
                                    $html_code.="";
                                }
                            $html_code.='</td>   
                            <td>'.$inquiry_status_array[$ctable_d5['status']].'</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="10" align="center">No Leads Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*leads*/

            /*Leads followup*/
            $html_code.='<h4><b>Leads Followup</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Inquiry No.</th>
                        <th>Customer Name</th>
                        <th>Mobile No</th>
                        <th>Sales Persoan Name</th>
                        <th>Description</th>
                        <th>Through</th>
                        <th>Date and Time</th>
                        <th>Response</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r10)>0)
                {
                    $count = 0;            
                    while($ctable_dL = mysqli_fetch_array($ctable_r10))
                    {
                        $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                        $Leads_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
                        if($Leads_ID)
                        {
                            $html_code.='<tr>
                                <td>'. ++$count.'</td>                
                                <td>#INQ/'.$db->rp_getValue("no_order_inquiry","id","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0).'</td>
                                <td>';
                                    if($ctable_dL['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","cname","id='".$ctable_dL['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dL['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
                                    }
                                $html_code.='</td> 
                                <td>';
                                    if($ctable_dL['reference_table']=="sales_executive")
                                    { 
                                        $html_code.=$db->rp_getValue("executive","phone","id='".$ctable_dL['visitor_id']."'"); 
                                    } 
                                    else if($ctable_dL['reference_table']=="no_order_inquiry")
                                    { 
                                        $html_code.=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dL['reference_id']."' AND inquiry_lead_flag = '1'",0);
                                    }
                                $html_code.='</td>              
                                <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_dL['user_id']."'").'</td>              
                                <td>'.$ctable_dL['description'].'</td>              
                                <td>'.$msg[$ctable_dL['through']].'</td>              
                                <td>';
                                    if($ctable_dL['followup_date']=="0000-00-00 00:00:00")
                                    { 
                                        $html_code.="";
                                    } 
                                    else
                                    { 
                                        $html_code.=date('d-m-Y H:i:s a',strtotime($ctable_dL['followup_date'])); 
                                    }
                                $html_code.='</td>   
                                <td>';
                                    if($ctable_dL['reference_table']=="no_order_inquiry")
                                    {
                                        $html_code.=$ctable_dL['response']; 
                                    } 
                                $html_code.='</td>
                            </tr>';
                        }
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Leads Followup Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Leads followup*/

            /*Total followup*/
            $html_code.='<h4><b>Followup ('.$db->rp_getTotalRecord($ctable3,$ctable_where_followup).')</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer Name</th>
                        <th>Mobile No</th>
                        <th>Sales Persoan Name</th>
                        <th>Description</th>
                        <th>Through</th>
                        <th>Date and Time</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r6)>0)
                {
                    $count = 0;            
                    while($ctable_d6 = mysqli_fetch_array($ctable_r6))
                    {
                        $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                        $Prospects_ID = $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d6['reference_id']."' AND inquiry_lead_flag = '-1'",0);
                        $count++;

                        $html_code.='<tr>
                            <td>'.$count.'</td>                
                            
                            <td>';
                                if($ctable_d6['reference_table']=="sales_executive")
                                { 
                                    $html_code.=$db->rp_getValue("executive","cname","id='".$ctable_d6['visitor_id']."'"); 
                                } 
                                else if($ctable_d6['reference_table']=="no_order_inquiry")
                                { 
                                    $html_code.=$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d6['reference_id']."'",0);
                                }
                            $html_code.='</td> 
                            <td>';
                                if($ctable_d6['reference_table']=="sales_executive")
                                { 
                                    $html_code.=$db->rp_getValue("executive","phone","id='".$ctable_d6['visitor_id']."'"); 
                                } 
                                else if($ctable_d6['reference_table']=="no_order_inquiry")
                                { 
                                    $html_code.=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d6['reference_id']."'",0);
                                }
                            $html_code.='</td>              
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d6['user_id']."'").'</td>              
                            <td>'.$ctable_d6['description'].'</td>              
                            <td>'.$msg[$ctable_d6['through']].'</td>              
                            <td>';
                                if($ctable_d6['followup_date']=="0000-00-00 00:00:00")
                                { 
                                    $html_code.="";
                                } 
                                else
                                { 
                                    $html_code.=date('d-m-Y H:i:s a',strtotime($ctable_d6['followup_date'])); 
                                }
                            $html_code.='</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Followup Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Total followup*/

            /*quotation*/
            $html_code.='<h4><b>Quotation ('.$db->rp_getTotalRecord($ctable4,$ctable_where_quotation).')</h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Quotation No.</th>
                        <th>Revised From Quotation No.</th>
                        <th>Inquiry No.</th>
                        <th>Quotation Date</th>
                        <th>Company Name</th>
                        <th>Company Mobile No</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Quotation Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r7)>0)
                {
                    $count = 0;   
                    while($ctable_d7 = mysqli_fetch_array($ctable_r7))
                    {
                        $Quotationarray = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td>
                            <td>'.stripslashes($ctable_d7['quotation_no']).'</td>
                            <td>'.$db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_d7['refrence_id']."'").'</td>
                            <td>#INQ'.$ctable_d7['inquiry_id'].'</td>
                            <td>'.date('d-m-Y', strtotime($ctable_d7['quotation_date'])).'</td>
                            <td>'.$ctable_d7['company_name'].'</td>
                            <td>'.stripslashes($ctable_d7['contact_number']).'</td>
                            <td>'.stripslashes($ctable_d7['customer_name']).'</td>
                            <td>'.$db->rp_getValue("sales_executive", "name", "id='" . $ctable_d7['sales_id'] . "'").'</td>
                            <td>'.stripslashes(CURR . $db->rp_num(round($ctable_d7['grand_total']))).'</td>
                            <td>'.$Quotationarray[$ctable_d7['status']].'</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="11" align="center">No Quotation Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*quotation*/

            /*quotation followup*/
            $html_code.='<h4><b>Quotation Followup ('.$db->rp_getTotalRecord($ctable6,$ctable_where_Q_followup).')</b></h4>
            <table class="table table-striped table-bordered mt-10 ">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Quotation No. No.</th>
                        <th>Customer Name</th>
                        <th>Mobile No </th>
                        <th>Sales Person Name</th>
                        <th>Description</th>
                        <th>Through</th>
                        <th>Date and Time</th>
                        <th>Response</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r12)>0)
                {
                    $count = 0;            
                    while($ctable_dQ = mysqli_fetch_array($ctable_r12))
                    {
                        $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                        
                        $html_code.='<tr>
                            <td>'. ++$count.'</td>                
                            <td>#INQ/'.$db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_dQ['reference_id']."'",0).'</td>
                            <td>';
                                if($ctable_dQ['reference_table']=="sales_executive")
                                { 
                                    $html_code.=$db->rp_getValue("executive","cname","id='".$ctable_dQ['visitor_id']."'"); 
                                } 
                                else if($ctable_dQ['reference_table']=="quotation_detail")
                                { 
                                    $html_code.=$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_dQ['reference_id']."'",0);
                                }
                            $html_code.='</td> 
                            <td>';
                                if($ctable_dQ['reference_table']=="sales_executive")
                                { 
                                    $html_code.=$db->rp_getValue("executive","phone","id='".$ctable_dQ['visitor_id']."'"); 
                                } 
                                else if($ctable_dQ['reference_table']=="quotation_detail")
                                { 
                                    $html_code.=$db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_dQ['reference_id']."'",0);
                                }
                            $html_code.='</td>
                            <td>';
                            if($ctable_dQ['reference_table']=="quotation_detail")
                            {
                                $html_code.=$db->rp_getValue("sales_executive","name","id='".$ctable_dQ['user_id']."'"); 
                            } 
                            $html_code.='</td>
                            <td>'.$ctable_dQ['description'].'</td>              
                            <td>'.$msg[$ctable_dQ['through']].'</td>              
                            <td>';
                                if($ctable_dQ['followup_date']=="0000-00-00 00:00:00")
                                { 
                                    $html_code.="";
                                } 
                                else
                                { 
                                    $html_code.=date('d-m-Y H:i:s a',strtotime($ctable_dQ['followup_date'])); 
                                }
                            $html_code.='</td>   
                            <td>';
                                if($ctable_dQ['reference_table']=="quotation_detail")
                                {
                                    $html_code.=$ctable_dQ['response']; 
                                } 
                            $html_code.='</td>
                        </tr>';
                    }
                }           
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Quotation Followup Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*quotation followup*/

            /*orders*/
            $html_code.='<h4><b>Orders ('.$db->rp_getTotalRecord($ctable5,$ctable_where_orders).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Order No.</th>
                        <th>Quotation No.</th>
                        <th>Order Date</th>
                        <th>Company Name</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Order Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r8)>0)
                {
                    $count = 0;   
                    while($ctable_d8 = mysqli_fetch_array($ctable_r8))
                    {
                        $orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.stripslashes($ctable_d8['order_no']).'</td> 
                            <td>';
                            if($ctable_d8['quotation_id']!=0)
                            {
                               $html_code.=$db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_d8['quotation_id']."'");
                            }
                            $html_code.='</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d8['order_date'])).'</td> 
                            <td>'.$ctable_d8['company_name'].'</td> 
                            <td>'.stripslashes($ctable_d8['customer_name']).'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d8['sales_id']."'").'</td> 
                            <td>'.stripslashes(CURR.$db->rp_num(round($ctable_d8['grand_total']))).'</td> 
                            <td>'.$orders_status[$ctable_d8['status']].'</td> 
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Orders Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*orders*/

            /*dispatch*/
           /* $html_code.='<h4><b>Dispatch ('.$db->rp_getTotalRecord($ctable7,$ctable_where_dispatch).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Diapatch No.</th>
                        <th>Order No.</th>
                        <th>Dispatch Date</th>
                        <th>Company Name</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r13)>0)
                {
                    $count = 0;   
                    while($ctable_d13 = mysqli_fetch_array($ctable_r13))
                    {
                        $dispatch_status_array = array("0"=>"Pending","1"=>"Complete");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.stripslashes($ctable_d13['dispatch_no']).'</td> 
                            <td>'.$db->rp_getValue("orders","order_no","isDelete=0 AND id='".$ctable_d13['order_id']."' ").'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d13['dispatch_date'])).'</td> 
                            <td>'.$ctable_d13['company_name'].'</td> 
                            <td>'.stripslashes($ctable_d13['customer_name']).'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d13['sales_id']."'").'</td> 
                            <td>'.$dispatch_status_array[$ctable_d13['status']].'</td> 
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="8" align="center">No Dispatch Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';*/
            /*dispatch*/

            /*Packing Slip*/
           /* $html_code.='<h4><b>Packing Slip ('.$db->rp_getTotalRecord($ctable8,$ctable_where_packingslip).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Packing Slip No.</th>
                        <th>Diapatch No.</th>
                        <th>Packing Slip Date</th>
                        <th>Company Name</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r14)>0)
                {
                    $count = 0;   
                    while($ctable_d14 = mysqli_fetch_array($ctable_r14))
                    {
                        $packing_slip_status_array = array("0"=>"Pending","1"=>"Invoice Generate");
                        $dispatch_sales_id=$db->rp_getValue("dispatch_detail","sales_id","isDelete=0 AND id='".$ctable_d14['dispatch_id']."' ");
                        $dispatch_sales_name=$db->rp_getValue("sales_executive","name","id='".$dispatch_sales_id."'"); 
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.stripslashes($ctable_d14['packing_slip_no']).'</td> 
                            <td>'.$db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_d14['dispatch_id']."' ").'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d14['packing_slip_date'])).'</td> 
                            <td>'.$db->rp_getValue("executive","company_name"," isDelete=0 AND id='".$ctable_d14['customer_id']."' ").'</td> 
                            <td>'.$db->rp_getValue("executive","cname"," isDelete=0 AND id='".$ctable_d14['customer_id']."' ").'</td> 
                            <td>'.$dispatch_sales_name.'</td> 
                            <td>'.$packing_slip_status_array[$ctable_d14['status']].'</td> 
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Packing Slip Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';*/
            /*Packing Slip*/

            /*Invoice*/
            /*$html_code.='<h4><b>Invoice ('.$db->rp_getTotalRecord($ctable9,$ctable_where_invoice).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Invoice No.</th>
                        <th>Dispatch No.</th>
                        <th>Invoice Date</th>
                        <th>Company Name</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r15)>0)
                {
                    $count = 0;   
                    while($ctable_d15 = mysqli_fetch_array($ctable_r15))
                    {
                        $invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched"); 
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.stripslashes($ctable_d15['invoice_no']).'</td> 
                            <td>'.$db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_d15['dispatch_ids']."' ").'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d15['invoice_date'])).'</td> 
                            <td>'.$ctable_d15['company_name'].'</td> 
                            <td>'.$ctable_d15['customer_name'].'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d15['sales_id']."'").'</td> 
                            <td>'.$invoice_status[$ctable_d15['status']].'</td> 
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Invoice Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';*/
            /*Invoice*/

            /*Complain*/
            $html_code.='<h4><b>Complain ('.$db->rp_getTotalRecord($ctable10,$ctable_where_complain).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Complain No.</th>
                        <th>Complain Date</th>
                        <th>Customer Name</th>
                        <th>Sales Person Name</th>
                        <th>Source of Complain</th>
                        <th>Status</th>
                        <th>Complain Assign To</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r16)>0)
                {
                    $count = 0;   
                    while($ctable_d16 = mysqli_fetch_array($ctable_r16))
                    {
                       $complain_status = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
                       $complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp"); 
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.stripslashes($ctable_d16['complain_no']).'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d16['complain_date'])).'</td> 
                            <td>'.$db->rp_getValue("executive","cname","id='".$ctable_d16['customer_id']."'").'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d16['user_id']."'").'</td> 
                            <td>'.stripslashes($complain_type_array[$ctable_d16['complain_type']]).'</td> 
                            <td>'.$complain_status[$ctable_d16['status']].'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$ctable_d16['complain_assign_to']."'").'</td> 
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No Complain Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Complain*/

            /*Visit*/
            $html_code.='<h4><b>Visit ('.$db->rp_getTotalRecord($ctable1,$ctable_where_visit).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Visit Date</th>
                        <th>Customer Name</th>
                        <th>Sales Person Name</th>
                        <th>Address</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r2)>0)
                {
                    $count = 0;   
                    while($ctable_d2 = mysqli_fetch_array($ctable_r2))
                    {
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d2['created_date'])).'</td> 
                            <td>'.$db->rp_getValue("executive","cname","id='".$ctable_d2['customer_id']."'").'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d2['user_id']."'").'</td> 
                            <td>'.$ctable_d2['app_address'].'</td>
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="5" align="center">No Visit Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Visit*/

            /*Expense*/
            $html_code.='<h4><b>Expense ('.$db->rp_getTotalRecord($ctable11,$ctable_where_expense).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Expense Date</th>
                        <th>Sales Person Name</th>
                        <th>Category Name</th>
                        <th>Sub Category Name</th>
                        <th>Request Amount</th>
                        <th>Passed Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r17)>0)
                {
                    $count = 0;   
                    while($ctable_d17 = mysqli_fetch_array($ctable_r17))
                    {
                        $expense_status_array = array("0"=>"Pending","1"=>"Pass","2"=>"Reject");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d17['expense_date'])).'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d17['sales_executive_id']."'").'</td> 
                            <td>'.$db->rp_getValue("expence_category","name"," isDelete=0 AND id = '".$ctable_d17['category_id']."' ").'</td>
                            <td>'.$db->rp_getValue("expence_sub_category","name"," isDelete=0 AND id = '".$ctable_d17['subcategory_id']."' ").'</td>
                            <td>'.$ctable_d17['total'].'</td>
                            <td>'.$ctable_d17['pass_expense_amount'].'</td>
                            <td>'.$expense_status_array[$ctable_d17['expense_status']].'</td>
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="8" align="center">No Expense Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Expense*/

            /*Leave Request*/
            $html_code.='<h4><b>Leave Request ('.$db->rp_getTotalRecord($ctable12,$ctable_where_leaverequest).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Leave Request Date</th>
                        <th>Sales Person Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Cancel/Reject Reason</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r18)>0)
                {
                    $count = 0;   
                    while($ctable_d18 = mysqli_fetch_array($ctable_r18))
                    {
                        $leaverequest_status_array = array("0"=>"Pending","1"=>"Accepted","2"=>"Rejected","3"=>"Cancelled");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d18['created_date'])).'</td> 
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d18['sales_executive_id']."'").'</td> 
                            <td>'.$db->rp_getValue("leave_type","name","id='".$ctable_d18['leave_type']."'").'</td>
                            <td>'.date("d-m-Y",strtotime($ctable_d18['start_date'])). date("h:i a",strtotime($ctable_d18['start_time'])).'</td>
                            <td>'.date("d-m-Y",strtotime($ctable_d18['end_date'])). date("h:i a",strtotime($ctable_d18['end_time'])).'</td>
                            <td>'.$leaverequest_status_array[$ctable_d18['status']].'</td>
                            <td>'.$ctable_d18['cancel_reject_reason'].'</td>
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="8" align="center">No Leave Request Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*Leave Request*/

            /*New Customer*/
            $html_code.='<h4><b>New Customer ('.$db->rp_getTotalRecord($ctable13,$ctable_where_newcustomer).')</b></h4>
            <table class="table table-striped table-bordered mt-10">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer Create Date</th>
                        <th>Customer Type</th>
                        <th>Company Name</th>
                        <th>Person Name</th>
                        <th>Sales Person Name</th>
                        <th>Phone</th>
                        <th>State</th>
                        <th>City</th>
                    </tr>
                </thead>';
                $html_code.='<tbody>';

                if(mysqli_num_rows($ctable_r19)>0)
                {
                    $count = 0;   
                    while($ctable_d19 = mysqli_fetch_array($ctable_r19))
                    {
                        $newcustomer_status_array = array("1"=>"Super Stockist","2"=>"Distributor","3"=>"Dealer","4"=>"B2B Customer","5"=>"OEM","6"=>"B2C Customer","7"=>"Merchant Exports");
                        $html_code.='<tr>
                            <td>'. ++$count.'</td> 
                            <td>'.date('d-m-Y',strtotime($ctable_d19['created_date'])).'</td> 
                            <td>'.$newcustomer_status_array[$ctable_d19['type_of_executive']].'</td>
                            <td>'.$ctable_d19['company_name'].'</td>
                            <td>'.$ctable_d19['cname'].'</td>
                            <td>'.$db->rp_getValue("sales_executive","name","id='".$ctable_d19['seid']."'").'</td> 
                            <td>'.$ctable_d19['phone'].'</td>
                            <td>'.$ctable_d19['state'].'</td>
                            <td>'.$ctable_d19['city'].'</td>
                        </tr>';
                    }
                }
                else
                {
                    $html_code.='<tr><td colspan="9" align="center">No New Customer Found</td></tr>';
                }
                $html_code.='</tbody>
            </table>';
            /*New Customer*/


        $html_code.='</body>
    </html>';
// echo $html_code; exit;

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

    $mpdf->WriteHTML($html_code);
    // $date1 = date('Y-m-d-h-i-s');
    $date1 = date('Y-m-d-h-i');
    $fileName = $date1;
    $fileName1 = $db->rp_createSlug($get_sales_d['name']."_".$get_sales_d['phone']);
    if(!is_dir($fileName)){
        mkdir(DAILY_SALES_PDF.$fileName);
    }
    $pdf_file_path  = DAILY_SALES_PDF.$fileName."/".$fileName1.'.pdf';
    if(file_exists($pdf_file_path)){
        unlink($pdf_file_path);
    }
    $mpdf->Output($pdf_file_path);

    /*Zip Create*/
        
        // $Zipname = $_REQUEST['type'].DISCOUNT.$fileName.".zip";
        $Zipname = DAILY_SALES_PDF.$fileName.".zip";
        if($zip->open($Zipname, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$Zipname>n");
        }
        $zip->addFile($pdf_file_path);
        $zip->close();
    /*Zip Create*/
    ob_end_clean();
    $html_code = "";
    
    //$d=file_get_contents(ADMINSITEURL.'generate_discount.php?user_id=9');

}
// echo json_encode(array("url"=>SITEURL."discount/".$datedt.".zip","datedt"=>$datedt,"startlimit"=>$startlimit,"iscontinue"=>$iscontinue,"totalRecord"=>(int)$totalRecord,"type"=>$type));
echo json_encode(array("url"=>DAILY_SALES_PDF.$date1.".zip","datedt"=>$date1,"startlimit"=>$startlimit,"iscontinue"=>$iscontinue,"totalRecord"=>(int)$totalRecord,"type"=>$type));

?>