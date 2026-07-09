<?php
$page_id=630;$page_slug='to_do_list';
include("connect.php");

$ctable  = "no_order_inquiry";
$ctable1 = "followup";
$ctable2 = "quotation_detail";
$ctable3 = "orders";
$ctable4 = "dispatch_detail";
$ctable5 = "packing_slip";
$ctable6 = "invoice_new";
$ctable7 = "payment";

$sales_id=$_REQUEST['sales_id'];
$date = date('Y-m-d');

$salesexename = $db->rp_getValue("sales_executive","name","id='".$_REQUEST["sales_id"]."'",0);
$salesexephone = $db->rp_getValue("sales_executive","phone","id='".$_REQUEST["sales_id"]."'",0);
$sm_id = $db->rp_getValue("sales_executive","sm_id","id='".$_REQUEST["sales_id"]."'",0);
$asm_id = $db->rp_getValue("sales_executive","asm_id","id='".$_REQUEST["sales_id"]."'",0);
$so_id = $db->rp_getValue("sales_executive","so_id","id='".$_REQUEST["sales_id"]."'",0);
$se_id = $db->rp_getValue("sales_executive","se_id","id='".$_REQUEST["sales_id"]."'",0);

/*Prospects*/
$ctable_where_prospect = "";
$ctable_where_prospect .="sales_executive_id=".$sales_id." AND isDelete=0 AND status = 0";
$ctable_where_prospect .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '-1'";
$ctable_prospect = $db->rp_getData($ctable,"*",$ctable_where_prospect,"id DESC",0);
/*Prospects*/

/*inquiry*/
$ctable_where_inquiry = "";
$ctable_where_inquiry .="sales_executive_id=".$sales_id." AND isDelete=0 AND status = 0";
$ctable_where_inquiry .= " AND DATE(inquiry_date) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '0'";
$ctable_inquiry = $db->rp_getData($ctable,"*",$ctable_where_inquiry,"id DESC",0);
/*inquiry*/

/*leads*/
$ctable_where_lead = "";
$ctable_where_lead .="sales_executive_id=".$sales_id." AND isDelete=0 AND status = 0";
$ctable_where_lead .= " AND DATE(inquiry_date) <= '".date("Y-m-d",strtotime($date))."' AND inquiry_lead_flag = '1'";
$ctable_lead = $db->rp_getData($ctable,"*",$ctable_where_lead,"id DESC",0);
/*leads*/

/*followup*/
$ctable_where_followup = "";
$ctable_where_followup .="user_id=".$sales_id." AND isDelete=0";
$ctable_where_followup .= " AND DATE(followup_date) <= '".date("Y-m-d",strtotime($date))."' AND response=''";
$ctable_followup = $db->rp_getData($ctable1,"*",$ctable_where_followup,"id DESC",0);
/*followup*/


/*quotation*/
$ctable_where_quotation = "";
$ctable_where_quotation .="sales_id=".$sales_id." AND isDelete=0 AND status=0";
$ctable_where_quotation .= " AND DATE(quotation_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_quotation = $db->rp_getData($ctable2,"*",$ctable_where_quotation,"id DESC",0);
/*quotation*/

/*orders*/
$ctable_where_orders = "";
$ctable_where_orders .="sales_id=".$sales_id." AND isDelete=0 AND status=0";
$ctable_where_orders .= " AND DATE(order_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_orders = $db->rp_getData($ctable3,"*",$ctable_where_orders,"id DESC",0);
/*orders*/

/*dispatch*/
$ctable_where_dispatch = "";
$ctable_where_dispatch .="sales_id=".$sales_id." AND isDelete=0 AND status=0";
$ctable_where_dispatch .= " AND DATE(dispatch_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_dispatch = $db->rp_getData($ctable4,"*",$ctable_where_dispatch,"id DESC",0);
/*dispatch*/

/*packing slip*/
$ctable_where_packingslip = "";
$sales_dispatch_ids=array();
$dispatch_r=$db->rp_getData("dispatch_detail","*","isDelete=0 AND sales_id='".$sales_id."' AND status=0");
while($dispatch_d=mysqli_fetch_assoc($dispatch_r))
{
  $sales_dispatch_ids[]=$dispatch_d['id'];
}
// $ctable_where_packingslip .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where_packingslip .=" dispatch_id IN (".implode(",",$sales_dispatch_ids).") AND isDelete=0 AND status=0";
$ctable_where_packingslip .= " AND DATE(packing_slip_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_packing_slip = $db->rp_getData($ctable5,"*",$ctable_where_packingslip,"id DESC",0);
/*packing slip*/


/*invoice*/
$ctable_where_invoice = "";
$ctable_where_invoice .="sales_id=".$sales_id." AND isDelete=0 AND status=0";
//$ctable_where_invoice .= " AND DATE(invoice_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_where_invoice .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_invoice = $db->rp_getData($ctable6,"*",$ctable_where_invoice,"id DESC",0);
/*invoice*/

/*customer receipt*/
$ctable_where_customer_receipt = "";
$ctable_where_customer_receipt .="sales_executive_id=".$sales_id." AND isDelete=0 AND payment_status=0";
$ctable_where_customer_receipt .= " AND DATE(payment_date) <= '".date("Y-m-d",strtotime($date))."'";
$ctable_customer_receipt = $db->rp_getData($ctable7,"*",$ctable_where_customer_receipt,"id DESC",0);
/*customer receipt*/


if($sm_id==0)
{
  $type="Regional Sales Manager";
}
else if($sm_id!=0 && $asm_id==0)
{
  $type="Business Development Manager";
}
else if($sm_id!=0 && $asm_id!=0 && $so_id==0)
{
  $type="Area Sales Manager";
}
else
{
  $type="Sales Officer";
}
?>
<style type="text/css">
  .no-border td
  {
    border:none!important;   
    font-size: 20px;
    /*font-weight: 300;*/
  }
  .no-border td.value
  {
     /*font-size: 20px;
    font-weight: 700;*/
  }
  .pad-0
  {
    padding: 0px!important;
  }
</style>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />

<div class="row" id="report_content1" style="margin:0;">
    <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin: 0">
      <h3><b>To Do List</b></h3>
    </div>

    <div class="col-md-12 col-xs-12 col-sm-12 pad-0">
        <div class="col-md-6 col-xs-6 col-sm-6 pad-0">
            <table class="table table-striped table-bordered mt-15 ">
                <tr class="no-border">
                    <td>Name</td>
                    <td>:-</td>
                    <td class="value"><?= ucfirst($salesexename);?></td>
                </tr>
                <tr class="no-border">
                  <td>Type</td>
                  <td>:-</td>
                  <td class="value"><?= $type;?></td>
                </tr>
                <tr class="no-border">
                  <td>Mobile No.</td>
                  <td>:-</td>
                  <td class="value"><?= $salesexephone;?></td>
                </tr>
            </table>
        </div>
        <!-- total Prospect -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Raw Data (<?= $db->rp_getTotalRecord($ctable,$ctable_where_prospect); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_prospect)>0)
            {
                $count = 0;
                while($ctable_prospect_D = mysqli_fetch_array($ctable_prospect))
                {
                    $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"","-1"=>"Not Interested");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td>#INQ/<?php  echo $ctable_prospect_D['id']; ?></td>
                        <td><?php echo $ctable_prospect_D['company_name'] ?></td>
                        <td><?php echo $ctable_prospect_D['person_name'] ?></td>              
                        <td><?php echo $ctable_prospect_D['mobile_number'] ?></td>              
                        <td><?php echo $ctable_prospect_D['country'] ?></td>              
                        <td><?php echo $ctable_prospect_D['state'] ?></td>              
                        <td><?php echo $ctable_prospect_D['city'] ?></td>              
                        <td><?php if($ctable_prospect_D['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_prospect_D['datetime'])); } else{ echo "";} ?></td>   
                        <td><?php echo $inquiry_status_array[$ctable_prospect_D['status']] ?></td> 
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="10" class="text-center">No Raw Data Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total Prospect -->
        <!-- total inqiury -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Inquiry (<?= $db->rp_getTotalRecord($ctable,$ctable_where_inquiry); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Inquiry No.</th>
                    <th>Company Name</th>
                    <th>Person Name</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Inquiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <?php
            if(mysqli_num_rows($ctable_inquiry)>0)
            {
                $count = 0;            
                while($ctable_inquiry_D = mysqli_fetch_array($ctable_inquiry))
                {
                    $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"","-1"=>"Not Interested");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td>                
                        <td>#INQ/<?php  echo $ctable_inquiry_D['id']; ?></td>
                        <td><?php echo $ctable_inquiry_D['company_name'] ?></td>
                        <td><?php echo $ctable_inquiry_D['person_name'] ?></td>              
                        <td><?php echo $ctable_inquiry_D['mobile_number'] ?></td>              
                        <td><?php echo $ctable_inquiry_D['country'] ?></td>              
                        <td><?php echo $ctable_inquiry_D['state'] ?></td>              
                        <td><?php echo $ctable_inquiry_D['city'] ?></td>              
                        <td><?php if($ctable_inquiry_D['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_inquiry_D['datetime'])); } else{ echo "";} ?></td>   
                        <td><?php echo $inquiry_status_array[$ctable_inquiry_D['status']] ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="10" class="text-center">No Inquiry Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total inqiury -->
        <!-- total leads -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Leads (<?= $db->rp_getTotalRecord($ctable,$ctable_where_lead); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_lead)>0)
            {
                $count = 0;            
                while($ctable_lead_D = mysqli_fetch_array($ctable_lead))
                {
                    $inquiry_status_array = array("0"=>"Generated","1"=>"In Followup","2"=>"Interested","3"=>"","-1"=>"Not Interested");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td>                
                        <td>#INQ/<?php  echo $ctable_lead_D['id']; ?></td>
                        <td><?php echo $ctable_lead_D['company_name'] ?></td>
                        <td><?php echo $ctable_lead_D['person_name'] ?></td>              
                        <td><?php echo $ctable_lead_D['mobile_number'] ?></td>              
                        <td><?php echo $ctable_lead_D['country'] ?></td>              
                        <td><?php echo $ctable_lead_D['state'] ?></td>              
                        <td><?php echo $ctable_lead_D['city'] ?></td>              
                        <td><?php if($ctable_lead_D['datetime']!="0000-00-00 00:00:00"){ echo date('d-m-Y',strtotime($ctable_lead_D['datetime'])); } else{ echo "";} ?></td>   
                        <td><?php echo $inquiry_status_array[$ctable_lead_D['status']] ?></td>        
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="10" class="text-center">No Leads Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total leads -->
        <!-- total Followup -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Followup (<?= $db->rp_getTotalRecord($ctable1,$ctable_where_followup); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Inq No</th>
                    <th>Customer Name</th>
                    <th>Mobile No </th>
                    <th>Sales Person Name</th>
                    <th>Description</th>
                    <th>Type of Follow up</th>
                    <th>Through</th>
                    <th>Date and Time</th>
                </tr>
            </thead>
            <?php
            if(mysqli_num_rows($ctable_followup)>0)
            {
                $count = 0;            
                while($ctable_followup_D = mysqli_fetch_array($ctable_followup))
                {
                    $msg = array("1"=>"Call","2"=>"Sms",3=>"Email");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td>
                        <?php
                        if($ctable_followup_D['reference_table']=="no_order_inquiry")
                        { 
                            ?>  
                            <td><a target="_blank" href="followup.php?mode=inquiry_followup&inquiry_id=<?= $ctable_followup_D['reference_id']?>&sales_id=<?=$ctable_followup_D['user_id'] ?>"><?php echo "INQ/".$ctable_followup_D['reference_id']; ?></a></td>  
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
                            if($ctable_followup_D['reference_table']=="sales_executive")
                            {
                              echo $db->rp_getValue("executive","cname","id='".$ctable_followup_D['visitor_id']."'");
                            }
                            else if($ctable_followup_D['reference_table']=="no_order_inquiry")
                            {
                              echo $db->rp_getValue("no_order_inquiry","company_name","id='".$ctable_followup_D['reference_id']."'",0);
                            }
                            else if($ctable_followup_D['reference_table']=="quotation_detail")
                            {
                                $followup_flag="quotation_followup";
                                $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_followup_D['reference_id']."'");
                                echo $db->rp_getValue("executive","cname","id='".$customer_id."'");
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if($ctable_followup_D['reference_table']=="sales_executive")
                            {
                              echo $db->rp_getValue("executive","phone","id='".$ctable_followup_D['visitor_id']."'");
                            }
                            else if($ctable_followup_D['reference_table']=="no_order_inquiry")
                            {
                              echo $db->rp_getValue("no_order_inquiry","mobile_number","id='".$ctable_followup_D['reference_id']."'",0);
                            }
                            else if($ctable_followup_D['reference_table']=="quotation_detail")
                            {
                                $followup_flag="quotation_followup";
                                $customer_id=$db->rp_getValue("quotation_detail","customer_id","id='".$ctable_followup_D['reference_id']."'");
                                echo $db->rp_getValue("executive","phone","id='".$customer_id."'");
                            }
                            ?>
                        </td>
                        <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_followup_D['user_id']."'");?></td> 
                        <td><?php echo $ctable_followup_D['description'] ?></td> 
                        <td>
                        <?php 
                        if($ctable_followup_D['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_followup_D['reference_id']."' AND inquiry_lead_flag = '0'",0))
                        {
                            $slagf = "Inquiry";
                        }
                        else if ($ctable_followup_D['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$ctable_followup_D['reference_id']."' AND inquiry_lead_flag = '-1'",0)) 
                        {
                            $slagf= "Prospects";
                        }
                        else if ( $ctable_followup_D['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$ctable_followup_D['reference_id']."' AND inquiry_lead_flag = '1'",0)) 
                        {
                            $slagf= "Leads";
                        }
                        else if ($ctable_followup_D['reference_table'] == "sales_executive") 
                        {
                            $slagf= "Sales Officer";
                        }
                        else if ($ctable_followup_D['reference_table'] == "customer_inquiry") 
                        {
                            $slagf= "Customer Inquiry";
                        }
                        else if ($ctable_followup_D['reference_table'] == "quotation_followup") 
                        {
                            $slagf= "Quotation";
                        }
                        else if ($ctable_followup_D['reference_table'] == "executive") 
                        {
                           $slagf= "Executive";
                        }
                        else if ($ctable_followup_D['reference_table'] == "customer_inquiry") 
                        {
                            $slagf= "Executive";
                        }
                        else if ($ctable_followup_D['reference_table'] == "quotation_detail") 
                        {
                            $slagf= "Quotation";
                        }  
                        echo $slagf;    
                        ?>  
                        </td>     
                        <td><?php echo $msg[$ctable_followup_D['through']]?></td>
                        <?php 
                        if($ctable_followup_D['followup_date']=="0000-00-00 00:00:00")
                        {
                            ?>
                            <td></td>
                            <?php 
                        } 
                        else 
                        { 
                            ?>
                            <td><?php echo date('d-m-Y H:i:s a',strtotime($ctable_followup_D['followup_date'])); ?></td>
                            <?php 
                        } 
                        ?>
                    </tr>
                    <?php
                }
            }
            else
            {
              ?>
              <tr>
                <td colspan="9" class="text-center">No Followup Found</td>
              </tr>
              <?php
            }
            ?>
        </table>
        <!-- total Followup -->
        <!-- total quotation -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Quotation (<?= $db->rp_getTotalRecord($ctable2,$ctable_where_quotation); ?>) --- Total Quotation Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable2,"SUM(grand_total)",$ctable_where_quotation),2); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_quotation)>0)
            {
                $count = 0;   
                while($ctable_quotation_D = mysqli_fetch_array($ctable_quotation))
                {
                    $Quotationarray = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","5"=>"Lost");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_quotation_D['id'] ?>"><span class="text-success"><?php echo stripslashes($ctable_quotation_D['quotation_no']); ?></span></a></td>
                        <td><a href="quotation_viewer.php?quotation_id=<?= $ctable_quotation_D['refrence_id'] ?>"><span class="text-success"><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_quotation_D['refrence_id']."'"); ?></span></a></td>
                        <td><?= "#INQ/" . $ctable_quotation_D['inquiry_id']; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($ctable_quotation_D['quotation_date'])); ?></td>
                        <td><?php echo $ctable_quotation_D['company_name']; ?></td>
                        <td><a target="_blank" href="https://api.whatsapp.com/send?phone=91<?php echo stripslashes($ctable_quotation_D['contact_number']); ?>&text=<?= $sms; ?>"><?php echo stripslashes($ctable_quotation_D['mobile_no']); ?><?php echo $ctable_quotation_D['contact_number']; ?></a></td>
                        <td><?php echo stripslashes($ctable_quotation_D['customer_name']); ?></td>
                        <td><?php echo $db->rp_getValue("sales_executive", "name", "id='" . $ctable_quotation_D['sales_id'] . "'"); ?></td>
                        <td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($ctable_quotation_D['grand_total']))); ?></td>
                        <td><?php echo $Quotationarray[$ctable_quotation_D['status']]; ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="11" class="text-center">No Quotation Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total quotation -->
        <!-- total orders -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Orders (<?= $db->rp_getTotalRecord($ctable3,$ctable_where_orders); ?>) --- Total Order Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable3,"SUM(grand_total)",$ctable_where_orders),2); ?> )</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_orders)>0)
            {
                $count = 0;   
                while($ctable_orders_D = mysqli_fetch_array($ctable_orders))
                {
                    $orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><span class="text-success"><a href="order_viewer.php?order_id=<?= $ctable_orders_D['id'] ?>"><?php echo stripslashes($ctable_orders_D['order_no']); ?></a></span></td>
                        <td><?php if($ctable_orders_D['quotation_id']!=0){echo "<a class='text-success' target='_blank' href='quotation_viewer.php?quotation_id=".$ctable_orders_D['quotation_id']."'>". $db->rp_getValue("quotation_detail","quotation_no","id='".$ctable_orders_D['quotation_id']."'")."</a>";}?></td>
                        <td><?php echo date('d-m-Y',strtotime($ctable_orders_D['order_date'])); ?></td>
                        <td><?php echo $ctable_orders_D['company_name'];?></td>
                        <td><?php echo stripslashes($ctable_orders_D['customer_name']); ?></td>
                        <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_orders_D['sales_id']."'"); ?></td>
                        <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_orders_D['grand_total']))); ?></td>
                        <td><?php echo $orders_status[$ctable_orders_D['status']]; ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="9" class="text-center">No Orders Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total orders -->
        <!-- total dispatch -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
          <h3><b>Dispatch (<?= $db->rp_getTotalRecord($ctable4,$ctable_where_dispatch); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_dispatch)>0)
            {
                $count = 0;   
                while($ctable_dispatch_D = mysqli_fetch_array($ctable_dispatch))
                {
                    $dispatch_status_array = array("0"=>"Pending","1"=>"Complete");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_dispatch_D['id'] ?>"><?php echo stripslashes($ctable_dispatch_D['dispatch_no']); ?></a></span></td>
                        <td><span class="text-success"><a target="_blank" href="order_viewer.php?order_id=<?= $ctable_dispatch_D['order_id'] ?>"><?php echo $db->rp_getValue("orders","order_no","isDelete=0 AND id='".$ctable_dispatch_D['order_id']."' "); ?></a></span></td>
                        <td><?php echo date('d-m-Y',strtotime($ctable_dispatch_D['dispatch_date'])); ?></td>
                        <td><?php echo $ctable_dispatch_D['company_name'];?></td>
                        <td><?php echo stripslashes($ctable_dispatch_D['customer_name']); ?></td>
                        <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_dispatch_D['sales_id']."'");; ?></td>
                        <td><?php echo $dispatch_status_array[$ctable_dispatch_D['status']]; ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="9" class="text-center">No Dispatch Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- total dispatch -->
        <!-- packing slip -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Packing Slip (<?= $db->rp_getTotalRecord($ctable5,$ctable_where_packingslip); ?>)</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
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
            </thead>
            <?php
            if(mysqli_num_rows($ctable_packing_slip)>0)
            {
                $count = 0;   
                while($ctable_packing_slip_D = mysqli_fetch_array($ctable_packing_slip))
                {
                    $packing_slip_status_array = array("0"=>"Pending","1"=>"Invoice Generate");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><span class="text-success"><a target="_blank" href="view_packing_slip.php?id=<?= $ctable_packing_slip_D['id'] ?>"><?php echo stripslashes($ctable_packing_slip_D['packing_slip_no']); ?></a></span></td>
                        <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_packing_slip_D['dispatch_id'] ?>"><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_packing_slip_D['dispatch_id']."' "); ?></a></span></td>
                        <td><?php echo date('d-m-Y',strtotime($ctable_packing_slip_D['packing_slip_date'])); ?></td>
                        <td><?php echo $db->rp_getValue("executive","company_name"," isDelete=0 AND id='".$ctable_packing_slip_D['customer_id']."' "); ?></td>
                        <td><?php echo $db->rp_getValue("executive","cname"," isDelete=0 AND id='".$ctable_packing_slip_D['customer_id']."' "); ?></td>
                        <?php 
                        $dispatch_sales_id=$db->rp_getValue("dispatch_detail","sales_id","isDelete=0 AND id='".$ctable_packing_slip_D['dispatch_id']."' ");
                        $dispatch_sales_name=$db->rp_getValue("sales_executive","name","id='".$dispatch_sales_id."'"); 
                        ?>
                        <td><?php echo $dispatch_sales_name; ?></td>
                        <td><?php echo $packing_slip_status_array[$ctable_packing_slip_D['status']]; ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="9" class="text-center">No Packing Slip Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- packing slip -->
        <!-- invoice -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Invoice (<?= $db->rp_getTotalRecord($ctable6,$ctable_where_invoice); ?>) --- Total Invoice Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable6,"SUM(grand_total)",$ctable_where_invoice),2); ?> )</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Invoice No.</th>
                    <th>Dispatch No.</th>
                    <th>Invoice Date</th>
                    <th>Company Name</th>
                    <th>Person Name</th>
                    <th>Sales Person Name</th>
                    <th>Invoice Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <?php
            if(mysqli_num_rows($ctable_invoice)>0)
            {
                $count = 0;   
                while($ctable_invoice_D = mysqli_fetch_array($ctable_invoice))
                {
                    $invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><span class="text-success"><a target="_blank" href="invoice_viewer.php?invoice_id=<?= $ctable_invoice_D['id'] ?>"><?php echo stripslashes($ctable_invoice_D['invoice_no']); ?></a></span></td>
                        <td><span class="text-success"><a target="_blank" href="view_dispatch.php?id=<?= $ctable_invoice_D['dispatch_ids'] ?>"><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","isDelete=0 AND id='".$ctable_invoice_D['dispatch_ids']."' "); ?></a></span></td>
                        <td>
                            <?php 
                            if($ctable_invoice_D['invoice_date']!="")
                            {
                                echo date('d-m-Y',strtotime($ctable_invoice_D['invoice_date'])); 
                            }
                            else
                            {
                                echo "";
                            }
                            ?>
                        </td>
                        <td><?php echo $ctable_invoice_D['company_name']; ?></td>
                        <td><?php echo $ctable_invoice_D['customer_name']; ?></td>
                        <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_invoice_D['sales_id']."'"); ?></td>
                        <td align="right"><?php echo stripslashes(CURR.$db->rp_num(round($ctable_invoice_D['grand_total']))); ?></td>
                        <td><?php echo $invoice_status[$ctable_invoice_D['status']]; ?></td>
                    </tr>
                    <?php
                }
            }
            else
            {
                ?>
                <tr>
                  <td colspan="9" class="text-center">No Invoice Found</td>
                </tr>
                <?php
            }
            ?>
        </table>
        <!-- invoice -->
        <!-- customer receipt -->
        <hr/>
        <div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
            <h3><b>Payment Colloection (<?= $db->rp_getTotalRecord($ctable7,$ctable_where_customer_receipt); ?>) --- Total Amount (<?= CURR .$db->rp_number_format($db->rp_getValue($ctable7,"SUM(paid_amount)",$ctable_where_customer_receipt),2); ?> )</b></h3>
        </div>
        <table id="datatable_1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Company Name</th>
                    <th>Customer Name</th>
                    <th>Sales Person Name</th>
                    <th>Receipt No.</th>
                    <th>Payment by</th>
                    <th>Payment Date</th>
                    <th>Payment Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <?php
            if(mysqli_num_rows($ctable_customer_receipt)>0)
            {
                $count = 0;
                while($ctable_customer_receipt_D = mysqli_fetch_array($ctable_customer_receipt))
                {
                    $payment_status = array("0"=>"Pending","1"=>"Approved");
                    $payment_type = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
                    ?>
                    <tr>
                        <td><?php echo ++$count; ?></td> 
                        <td><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_customer_receipt_D['customer_id']."'");?></td>
                        <td><?php echo $db->rp_getValue("executive","cname","id='".$ctable_customer_receipt_D['customer_id']."'");?></td>
                        <td><?php echo $db->rp_getValue("sales_executive","name","id='".$ctable_customer_receipt_D['sales_executive_id']."'");?></td>
                        <td><?php echo stripslashes($ctable_customer_receipt_D['receipt_no']); ?></td>
                        <td><?php echo stripslashes($payment_type[$ctable_customer_receipt_D['payment_type']]); ?></td>
                        <td><?php echo date('d-m-Y',strtotime($ctable_customer_receipt_D['payment_date'])); ?></td>
                        <td align="right"><?php echo stripslashes(CURR.($db->rp_num($ctable_customer_receipt_D['paid_amount']))); ?></td>
                        <td><?php echo stripslashes($payment_status[$ctable_customer_receipt_D['payment_status']]); ?></td>
                    </tr>
                    <?php
                } 
            }
            else
            {
                ?>
                <tr>
                  <td colspan="9" class="text-center">No Data Found</td>
                </tr>
                <?php
            }     
            ?>
        </table>
        <!-- customer receipt -->
    </div>
</div>
<?php require_once 'disconnect.php';  ?>

