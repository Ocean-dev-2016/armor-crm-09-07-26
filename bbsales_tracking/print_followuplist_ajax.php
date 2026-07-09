<?php
$page_id=583;$page_slug='future_followup_manage';
include("connect.php");
$ctable     = "followup";
$ctable1    = "visitor";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
    $ctable_where.="(";
    $phone_id = array();
    $exe_ids_r = $db->rp_getData("executive","*","mobile_no1 LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
    if($exe_ids_r)
    {
        while($exe_id_d = mysqli_fetch_assoc($exe_ids_r))
        {
          $phone_id[] = $exe_id_d['id'];
        }
        $phone_no_id = implode(",", $phone_id);
        $ctable_where.="visitor_id IN (".$phone_no_id.") "; 
    }
    $exe_ids_r1 = $db->rp_getData("no_order_inquiry","*","mobile_number LIKE '%".trim($_REQUEST['searchName'])."%' ","",0);
    if ($exe_ids_r1)
    {
        if($phone_id)
        {
            $ctable_where.=" OR ";
        } 
        while($exe_id_d1 = mysqli_fetch_assoc($exe_ids_r1))
        {
          $inqArr[] = $exe_id_d1['id'];
        }

        $inqids = implode(",", $inqArr);
        $ctable_where.=" reference_id IN (".$inqids.")"; 
    }
    $ctable_where.=") AND ";
    $isFillter = true;
}

if($_REQUEST['df']!="" && $_REQUEST['df']!=undefined)
{
    if(isset($_REQUEST['df']) && $_REQUEST['df']!="" && $_REQUEST['df']!=NULL && $_REQUEST['df']!=undefined)
    {
        //echo $_REQUEST['df'];exit;
        $date_filter_query = urldecode( $_REQUEST['df'] );
        $date_filter_query_ex=explode(" to ",$date_filter_query);
        $ctable_where .= " ( DATE(followup_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(followup_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}
else if($_REQUEST['df1']!="" && $_REQUEST['df1']!=undefined)
{
    if(isset($_REQUEST['df1']) && $_REQUEST['df1']!="" && $_REQUEST['df1']!=NULL && $_REQUEST['df1']!=undefined)
    {
        //echo $_REQUEST['df'];exit;
        $date_filter_query_1 = urldecode( $_REQUEST['df1'] );
        $date_filter_query_1_ex=explode(" to ",$date_filter_query_1);
        $ctable_where .= " ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_1_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_1_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}
else if($_REQUEST['df2']!="" && $_REQUEST['df2']!=undefined)
{
    if(isset($_REQUEST['df2']) && $_REQUEST['df2']!="" && $_REQUEST['df2']!=NULL && $_REQUEST['df2']!=undefined)
    {
        // echo $_REQUEST['df'];exit;
        $date_filter_query_2 = urldecode( $_REQUEST['df2'] );
        $date_filter_query_2_ex=explode(" to ",$date_filter_query_2);
        $ctable_where .= " ( DATE(response_date)>='".date("Y-m-d",strtotime($date_filter_query_2_ex['0']))."' AND DATE(response_date)<='".date("Y-m-d",strtotime($date_filter_query_2_ex['1']))."'  ) AND ";
        $isFillter = true;
    }
}
else
{
    if($_REQUEST['followup_type'] == "today")
    {
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) = '".$today."' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "future")
    {
        $future = date('Y-m-d');
        $ctable_where .= "DATE(followup_date) > '".$future."' AND response='' AND";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "pending")
    {
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) < '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "today,pending")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) <= '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "today,future")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " DATE(followup_date) >= '".$today."' AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "future,pending")
    { 
        $today = date('Y-m-d');
        $ctable_where .= " (DATE(followup_date) < '".$today."' OR DATE(followup_date) > '".$today."') AND response='' AND ";
        $isFillter = true;
    }

    if($_REQUEST['followup_type'] == "all")
    {
     $isFillter = true;
    }

    if ($_REQUEST['followup_type'] == "responsed") {
        $ctable_where .= " response!='' AND ";
        $isFillter = true;
    }
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate']!="01-01-1970") {
    $ctable_where .= "  DATE(followup_date) >= '" . $_REQUEST['todate'] . "' AND ";
    $isFillter = true;
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate']!="01-01-1970") {
    $ctable_where .= "  DATE(followup_date) <= '" .$_REQUEST['fromdate']. "' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['reference_media_id']) && $_REQUEST['reference_media_id']!="")
{
    $ctable_where .=" refrence_media_id='".$_REQUEST['reference_media_id']."' AND";
    $isFillter = true;
}

if(isset($_REQUEST['executive']) && $_REQUEST['executive']!="" && $_REQUEST['executive']!=NULL)
{
    $ctable_where .= " visitor_id= '".$_REQUEST['executive']."' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['through']) && $_REQUEST['through']!="" && $_REQUEST['through']!=NULL)
{
    $ctable_where .= "through= '".$_REQUEST['through']."' AND ";
    $isFillter = true;
}

if(isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive']!="" && $_REQUEST['sales_executive']!=NULL && $_REQUEST['sales_executive']!=undefined)
{
    $ctable_where .= " user_id= '".$_REQUEST['sales_executive']."' AND ";
    $sales_executive=$_REQUEST['sales_executive'];
    $isFillter = true;
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
    $loginid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
    if($rights['personal_flag']==1)
    {
        $ctable_where .= " (inquiry_assign_to='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR inquiry_created_by='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR user_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR created_by='".$loginid."') AND ";
        $sales_executive=$_REQUEST['sales_executive'];
    }
    else
    {
        if($rights['chain_vise_flag'] == 1)
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

            $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
            if ($get_sales_type== "sales_manager") 
            {
                $sales_executive_type = "Regional Sales Manager";
                $key="sm_id";
                $WhereCondition.=' ' .$key.'='.$check_id;
            }

            else if ($get_sales_type == "area_sales_manager") 
            {
                $sales_executive_type = "National Sales Manager";//Business Development Manager
                $key="asm_id";
                $WhereCondition.=' ' .$key.'='.$check_id;
            }

            else if ($get_sales_type == "sales_officer") 
            {
                $sales_executive_type = "Area Sales Manager";//Area Sales Manager
                $key="so_id";
                $WhereCondition.=' ' .$key.'='.$check_id;
            }
            else if ($get_sales_type == "sales_executive") 
            {
                $sales_executive_type = "Sales Officer";
                $key="se_id";
                $WhereCondition.=' ' .$key.'='.$check_id;
            }
            else
            {
                $WhereCondition.=' type = "service_engineer"';
            }

            $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

            $SALEID1=array();
            if($data)
            {
                while($data_d=mysqli_fetch_assoc($data))
                {
                    $SALEID1[]=$data_d['id'];
                }
            }
            if(!empty($SALEID1))
            {
                // echo "d0";
                $SALEID1=implode(",", $SALEID1);
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."') AND ";                   
            }
            else
            {
                // echo "der0";
                $ctable_where .= " status!=-1 AND (inquiry_assign_to IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") OR created_by='".$loginid."') AND "; 
            }
        }
        // else
        // {

        // }
    }
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= "user_id='".$check_id."' AND ";
// }

//service_executive user not show condition start --//
$CID=array();
$SEID=array();
$sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
while($sales_type_d = mysqli_fetch_array($sales_type_r))
{
    $SEID[] = $sales_type_d['id'];
}
$SEID=implode(",",$SEID);
if($SEID)
{ 
    $ctable_where .=" user_id NOT IN ('".$SEID."') AND ";
}
//service_executive user not show condition end --//

$ctable_where .= " isDelete=0";
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
?>
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
    text-transform: uppercase;
    margin-bottom: 0px;
}
</style>
<table id="datatable_1" class="table table-striped table-bordered table-hover table">
    <thead>
        <tr>
            <th colspan="15" class="center">
                <h2>Followup Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
            </th>
        </tr>
        <tr>
            <th style="width: 5%">No.</th>
             <th>Inq No / <br/>Bill No.</th>
            <th>Customer Name</th>
            <th>Mobile No</th>
            <th>Sales Officer Name</th>
            <th>Created Date and Time</th>
            <th>Followup Date and Time</th>
            <th>Description</th>
            <th>Through</th>
            <th>Type of Follow up</th>
            <th>Response Date</th>
            <th>Response</th>
            <th>Entry Type</th>
            <!-- <th>Response Entry Type</th> -->
            <!-- <th>Response Entry Update Type</th> -->
        </tr>
    </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
              $entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
              $response_entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
            $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                //$followupdate = date('d-m-Y',strtotime($ctable_d['followup_date']));
                //$responsedate = date('d-m-Y',strtotime($ctable_d['response_date']));
            
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>    
                 <?php
                if($ctable_d['reference_table']=="no_order_inquiry")
                { 
                ?>  
                <td><a target="_blank" href="followup.php?mode=inquiry_followup&inquiry_id=<?= $ctable_d['reference_id']?>&sales_id=<?=$ctable_d['user_id'] ?>"><?php echo "INQ/".$ctable_d['reference_id']; ?></a></td>  
                <?php
                }
                else if($ctable_d['reference_table']=="manual_invoice_import") 
                {
                ?>
                <td><?= $db->rp_getValue("manually_invoice_outstanding_import","bill_no","id='".$ctable_d['reference_id']."' AND isDelete=0"); ?></td>
                <?php 
                }
                else
                {
                ?>
                <td></td>
                <?php
                }
                ?>  
                <td>
                    <?php
                    if($ctable_d['reference_table']=="sales_executive")
                    {
                        $followup_flag="followup";
                        echo "<b>".$db->rp_getValue("executive","company_name","id='".$ctable_d['visitor_id']."'")."</b><br>".$db->rp_getValue("executive","cname","id='".$ctable_d['visitor_id']."'");
                    }
                    else if($ctable_d['reference_table']=="quotation_detail")
                    {
                        $followup_flag="quotation_followup";
                        $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d['reference_id']."'");
                        echo "<b>".$db->rp_getValue("executive","company_name","id='".$customer_id."'")."</b><br>".$db->rp_getValue("executive","cname","id='".$customer_id."'");
                    }
                    else if($ctable_d['reference_table']=="manual_invoice_import")
                    {
                        $followup_flag="manual_invoice_import";
                        $customer_id=$db->rp_getValue("manually_invoice_outstanding_import","customer_id","id='".$ctable_d['reference_id']."'");
                        echo "<b>".$db->rp_getValue("executive","company_name","id='".$customer_id."'")."</b><br>".$db->rp_getValue("executive","cname","id='".$customer_id."'");
                    }
                    else if($ctable_d['reference_table']=="no_order_inquiry")
                    {
                        $followup_flag="inquiry_followup";
                        echo  "<b>".$db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_d['reference_id']."'")."</b><br>".$db->rp_getValue("no_order_inquiry","person_name","id='".$ctable_d['reference_id']."'");
                    }
                    else if($ctable_d['reference_table']=="customer_inquiry")
                    {
                        $followup_flag="leads_followup";
                        echo "<b>".$db->rp_getValue("customer_inquiry","company_name","id='".$ctable_d['reference_id']."'")."</b><br>".$db->rp_getValue("no_order_inquiry","person_name","id='".$ctable_d['reference_id']."'");
                    }
                    else if($ctable_d['reference_table']=="executive")
                    {
                        $followup_flag="customer_followup";
                        echo "<b>".$db->rp_getValue("executive","company_name","id='".$ctable_d['reference_id']."'")."</b><br>".$db->rp_getValue("executive","cname","id='".$ctable_d['reference_id']."'"); 
                    }
                    ?>
                </td> 
                <td>
                    <?php 
                    if($ctable_d['reference_table']=="sales_executive")
                    {
                        echo $db->rp_getValue("executive","mobile_no1","id='".$ctable_d['visitor_id']."'");
                    }
                    else if($ctable_d['reference_table']=="no_order_inquiry")
                    {
                        echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
                    }
                    else if($ctable_d['reference_table']=="quotation_detail")
                    {
                        $cid = $db->rp_getValue("quotation_detail","customer_id","id='".$ctable_d['reference_id']."'",0);
                        echo $db->rp_getValue("executive","mobile_no1","id='".$cid."'",0);
                    }
                    else if($ctable_d['reference_table']=="manual_invoice_import")
                    {
                        $cid = $db->rp_getValue("manually_invoice_outstanding_import","customer_id","id='".$ctable_d['reference_id']."'",0);
                        echo $db->rp_getValue("executive","mobile_no1","id='".$cid."'",0);
                    }
                    else if($ctable_d['reference_table']=="customer_inquiry")
                    {
                        echo $db->rp_getValue("customer_inquiry","mobile_number","id='".$ctable_d['reference_id']."'");
                    }
                    else if($ctable_d['reference_table']=="executive")
                    {
                        echo $db->rp_getValue("executive","mobile_no1","id='".$ctable_d['reference_id']."'");
                    }
                    ?>
                </td>
                <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_d['user_id']."'");?></td>   

                <!-- followupdate-->
                <?php if($ctable_d['created_date']=="0000-00-00 00:00:00")
                {
                    ?>
                    <td></td>
                    <?php 
                }
                else
                {
                    ?>
                    <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_d['created_date'])); ?></td>
                    <?php 
                }
                ?> 
                <!-- followupdate--> 

                <!-- followupdate-->
                <?php if($ctable_d['followup_date']=="0000-00-00 00:00:00")
                {
                    ?>
                    <td></td>
                    <?php 
                }
                else
                {
                    ?>
                    <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_d['followup_date'])); ?></td>
                    <?php 
                }
                ?> 
                <!-- followupdate-->

                <td><?php echo $ctable_d['description']; ?></td>            
                <td><?php echo $msg[$ctable_d['through']]; ?></td>             

                <td>
                    <?php 
                    
                    if($ctable_d['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '0'",0))
                    {
                        $slagf = "Inquiry";
                    }

                    else if ($ctable_d['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '-1'",0)) {
                        $slagf= "Prospects";
                    }

                    else if ( $ctable_d['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$ctable_d['reference_id']."' AND inquiry_lead_flag = '1'",0)) {
                        $slagf= "Leads";
                    }

                    else if ($ctable_d['reference_table'] == "sales_executive") {
                        $slagf= "Sales Officer";
                    }
                    else if ($ctable_d['reference_table'] == "customer_inquiry") {
                        
                        $slagf= "Customer Inquiry";
                    }
                    else if ($ctable_d['reference_table'] == "quotation_followup") {
                        
                        $slagf= "Quotation";
                    }
                    else if ($ctable_d['reference_table'] == "manual_invoice_import") {
                        $slagf= "Manually Invoice Import";
                    }
                    else if ($ctable_d['reference_table'] == "executive") {
                        
                        $slagf= "Executive";
                    }
                    else if ($ctable_d['reference_table'] == "customer_inquiry") {
                        
                        $slagf= "Executive";
                    }
                    else if ($ctable_d['reference_table'] == "quotation_detail") {
                        
                        $slagf= "Quotation";
                    }
                    
                    echo $slagf;
                    ?>
                </td>


                <!-- responsedate-->
                <?php
                if($ctable_d['response_date']=="0000-00-00 00:00:00")
                {
                    ?>
                    <td></td>
                    <?php 
                } 
                else
                {
                    ?>
                    <td><?php echo date('d-m-Y',strtotime($ctable_d['response_date'])); ?></td>
                    <?php
                }
                ?> 
                 <!-- responsedate-->           
                <td><?php echo $ctable_d['response']; ?></td>
                <td><?php echo $entry_type_status[$ctable_d['entry_type']]; ?></td>
                <!-- <td><?php echo $response_entry_type_status[$ctable_d['response_entry_flag']]; ?></td>  -->
                <!-- <td><?php echo $response_entry_type_status[$ctable_d['response_update_flag']]; ?></td>  -->
             </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
<?php require_once("disconnect.php"); ?>
    
  