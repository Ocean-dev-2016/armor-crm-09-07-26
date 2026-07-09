<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php"); 
// $type=$_REQUEST['type'];
$sales_executive_id=$_REQUEST['sales_executive_id'];
$filter_month=$_REQUEST['filter_month'];
if($sales_executive_id!="")
{ 
    $ctable_r = $db->rp_getData("sales_executive","*","id = '".$sales_executive_id."' AND isDelete=0","",0); 
    $ctable_d=mysqli_fetch_assoc($ctable_r);
  
?>
<style type="text/css">
    .th{
        text-align: CENTER;
        VERTICAL-ALIGN: MIDDLE;
    }
    .top_value{
        font-weight: 700;
    }
    .repot_bg{
        background-color: #98989885;
    }
    /*td.repot_bg { background-color: gainsboro; }*/
</style>
<div class="table-responsive">
<form action="" name="frm" id="print_info" method="post">
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
        <thead> 
            <tr>
                <?php 
                $title1=date('d')."-".$filter_month."-".date('Y');
                $title1=date('F - Y',strtotime($title1));
                ?>
                <td class="header" align="left" colspan="10" ><h3 style="
                margin: 0"><b><?= $title1 ?><br/>Name :- <?= $ctable_d['name'] ?> <br>Number :- <?= $ctable_d['phone'] ?></b></h3></td>
                <!-- <td class="header" align="left" colspan="5" ></td> -->
            </tr>
            <div style="position: sticky;">                
                <tr class="tr"> 
                    <th class="th">Date</th>
                    <th class="th">City</th>
                    <th class="th">Route</th>
                    <th class="th">Distributor</th>
                    <th class="th">Call <br/><span style="font-size:11px">(Exisiting customer visit count)</span></th>
                    <th class="th">New Call<br/><span style="font-size:11px">(New customer add count)</span></th>
                    <th class="th">Total Call<br/><span style="font-size:11px">(Total visit count)</span></th>
                    <th class="th">Convert<br/><span style="font-size:11px">(Raw Data to customer convert count)</span></th>
                    <th class="th">Order Amount</th>                         
                </tr>
            </div>
        </thead>
        <tbody>
            <?php 
            $xx=date('d')."-".$filter_month."-".date('Y');
            $last_Date_of_month=date('t-m-Y',strtotime($xx));

            // echo $last_Date_of_month;
            $last_date =  date('d',strtotime($last_Date_of_month));
            // echo $last_date;
            $count=0;
            for($i=1;$i<=$last_date;$i++)
            {  
                $current_date = date('Y')."-".$filter_month."-".$i;
                // echo $current_date;
                // $current_date = date('Y')."-".date('m')."-".$i;

                $call_count=0;
                $total_count=0;
                // $customer_ids_arr="";
                $current_Date_visit_r = $db->rp_getData("visit","customer_id","user_id='".$ctable_d['id']."' AND DATE(created_date)='".$current_date."' AND isDelete=0 ",0);
                while($current_Date_visit_d = mysqli_fetch_assoc($current_Date_visit_r))
                { 
                    $customer_ids_arr[] = $current_Date_visit_d['customer_id'];

                    $total_count++;
                    $isExistingCustomer = $db->rp_getValue("executive","customer_flag","id='".$current_Date_visit_d['customer_id']."' AND isDelete=0 ",0);
                    if($isExistingCustomer==0)
                    {
                        $call_count++;
                        // $exisiting_customer_id[]=$current_Date_visit_d['id'];
                    } 
                    // $exisiting_customer_ids=implode(",",$exisiting_customer_id); 
                }
                $new_call_count = $db->rp_getTotalRecord("executive","seid='".$ctable_d['id']."' AND DATE(created_date)='".$current_date."' AND isDelete=0 AND customer_flag=0 AND isActive=1",0);

                $convert_customer_count = $db->rp_getTotalRecord("executive","seid='".$ctable_d['id']."' AND DATE(customer_flag_change_date)='".$current_date."' AND isDelete=0 AND isActive=1",0);

                // $total_count = $call_count+$new_call_count;
                $order_total_amount = $db->rp_getValue("orders","SUM(subtotal)","sales_id='".$ctable_d['id']."' AND order_date='".$current_date."' AND isDelete=0",0);


                $tot_call_count+=$call_count;
                $tot_new_call_count+=$new_call_count;
                $tot_call_count1+=$total_count;
                $tot_convert_customer_count+=$convert_customer_count;
                $tot_order_amt+=round($order_total_amount);
                
                $call_count=($call_count)?$call_count:"";
                $total_count=($total_count)?$total_count:"";
                $new_call_count=($new_call_count)?$new_call_count:"";
                $convert_customer_count=($convert_customer_count)?$convert_customer_count:"";
                $order_total_amount=($order_total_amount!=0)?round($order_total_amount,2):"";
 
                // get first city and area of customer
                if (!empty($customer_ids_arr)) 
                {
                   $customer_ids_arr = array_unique($customer_ids_arr); 
                   foreach ($customer_ids_arr as $cusid) {
                        $area_id_Arr[] = $db->rp_getValue("executive_map_area", "area_id","executive_id='".$cusid."' AND isDelete=0 LIMIT 1",0); 
                        $city_id_Arr[] = $db->rp_getValue("executive_map_area", "city_id","executive_id='".$cusid."' AND isDelete=0 LIMIT 1",0); 
                   } 
                }
               $area_id_Arr = array_unique($area_id_Arr);
               $city_id_Arr = array_unique($city_id_Arr);
                // get first city and area of customer

               // get distributor from customer
               $disName="";
               $dis_r = $db->rp_getData("executive_map_area", "executive_id","area_id IN(".implode(",",$area_id_Arr).") AND city_id IN(".implode(",",$city_id_Arr).") AND isDelete=0 AND executive_type=2 AND executive_id IN (".implode(",",$customer_ids_arr).")","",0);  
                while ($dis_d = mysqli_fetch_assoc($dis_r)) 
                {
                    if(!in_array($dis_d['executive_id'],$disIds))
                    { 
                        $disIds[] = $dis_d['executive_id'];
                        $disName.=$db->rp_getValue("executive","company_name","id='".$dis_d['executive_id']."' AND isDelete=0",0).",";
                    }
                }  
               // get distributor from customer
            ?>
            <tr class="tr"> 
                <td class="td"><?= date('d-m-Y',strtotime($current_date)) ?></td>
                <td class="td">
                    <?php  
                    foreach ($city_id_Arr as $cityid) {
                        echo $db->rp_getValue("city","name","id='".$cityid."' AND isDelete=0").", "; 
                    } 
                    ?>
                </td>
                <td class="td">
                    <?php
                    foreach ($area_id_Arr as $aid) {
                        echo $db->rp_getValue("area","name","id='".$aid."' AND isDelete=0").", "; 
                    }
                    ?>
                </td>
                <td class="td"><?= $disName; ?></td>
                <td class="td text-right"><?= $call_count; ?></td>
                <td class="td text-right"><?= $new_call_count; ?></td>
                <td class="td text-right"><?= $total_count; ?></td> 
                <td class="td text-right"><?= $convert_customer_count; ?></td>
                <td class="td text-right"><?= $order_total_amount ?></td>
            </tr>
            <?php 
                $area_id_Arr=[];
                $city_id_Arr=[];
                $customer_ids_arr=[];
                $disIds=[];
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="tr"> 
                <th class="td">Total</th>
                <th class="td"></th>
                <th class="td"></th>
                <th class="td"></th>
                <th class="td text-right"><?= $tot_call_count; ?></th>
                <th class="td text-right"><?= $tot_new_call_count; ?></th> 
                <th class="td text-right"><?= $tot_call_count1; ?></th>
                <th class="td text-right"><?= $tot_convert_customer_count; ?></th>
                <th class="td text-right"><?= round($tot_order_amt,2) ?></th>
            </tr>
        </tfoot>
    </table>
</form>
</div>
<?php 
}
else
{
    echo "<h3 class='text-center'><b>Please Select Sales Executive to See Result<b></h3>";
}
require_once("disconnect.php");
?>