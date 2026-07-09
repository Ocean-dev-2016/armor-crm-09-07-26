<?php 
$page_id=581;$page_slug='manage_complain';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "account_transaction";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{  
  $todate= $_REQUEST['ToDate'];
  // $ctable_where .= " AND transaction_date <='".date('Y-m-d',strtotime($_REQUEST['ToDate']))."' ";
  $ctable_where .= " AND payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
} 
if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{ 
  $fromdate= $_REQUEST['FromDate'];
  $ctable_where .= " AND payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['account_id']) && $_REQUEST['account_id']!=""){
  $account_id=$db->rp_getValue("account","id","cid='".$_REQUEST['account_id']."'",0);
  $ctable_where .= " AND account_id='".$account_id."' ";
}  



$ctable_r = $db->rp_getData($ctable,"*","isDelete=0 AND isActive=1 AND payment_date!=0000-00-00".$ctable_where,"payment_date ASC",0);  
$AccountInfo=$system->GetAccountInfo($account_id);
if($account_id!="")
{ 
  $sales_executive_id = $db->rp_getValue("executive","sales_executive_id","id=".$AccountInfo['cid']."");
  $responsibleperson = $db->rp_getValue("sales_executive","name","id=".$sales_executive_id.""); 
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
<form action="" name="frm"  method="post">
    <table id="datatable_1" class="table table-striped table-bordered table-hover" width="100%">

      <tr>
      <td colspan="16" class="header-img">
        <img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>">
      </td>
    </tr>

      <tr>
        <td colspan="8" align="center"><h2><strong>Account Report</strong></h2></td>
      </tr>
      <tr>
        <!-- <td colspan="2"><b> Account No. -    </b><?php echo " :<b> ".$AccountInfo['acc_no']."</b><br/>"; ?></td> -->
        <!-- <td colspan="2"><b> Customer Name  - </b><?php echo " :<b> ".$AccountInfo['account_name']."</b>"; ?></td> -->
        <td colspan="2"><b> Customer Name  - </b><?php echo " :<b> ".$db->rp_getValue("executive","company_name","isDelete=0 AND id='".$AccountInfo['cid']."'")."</b>"; ?></td>
        <td colspan="2"><b>From: </b><?php echo $fromdate; ?></td>
        <td align="right" colspan="2"><b>To: </b><?php echo $todate; ?></td>
      </tr>
    </table>

    <table id="datatable_1" class="table table-striped table-bordered table-hover" width="100%">
      <thead>
        <tr>
              <?php
              // for today opening  
              $fDate = date('d-m-Y',strtotime($_REQUEST['FromDate']));  
              $today_opening_date = date('Y-m-d', strtotime($fDate. ' - 1 days')); 
              $today_opening_debit1 = $db->rp_getValue("account_transaction","SUM(debit)","account_id='".$account_id."' AND payment_date<='".$today_opening_date."' AND isDelete=0",0); 
              $today_opening_credit1 = $db->rp_getValue("account_transaction","SUM(credit)","account_id='".$account_id."' AND payment_date<='".$today_opening_date."' AND isDelete=0",0);
              
              $closing_amount1 = $today_opening_credit1 - (-0-$today_opening_debit1);
             
              if($closing_amount1>0 && $closing_amount1!=0)
              {  
                $today_opening_credit = $closing_amount1;
                $today_opening_debit = 0;
              }
              else if($closing_amount1!=0 && $closing_amount1<0)
              { 
                $today_opening_credit = 0;
                $today_opening_debit = -0-$closing_amount1; 
              }
              else
              { 
                $today_opening_credit = 0;
                $today_opening_debit = 0;
              }
              ?>
              <td></td>
              <td></td> 
              <td class="text-right"><strong>Opening Balance</strong></td>
              <td class="text-right"><strong><?= $today_opening_debit ?></strong></td><td class="text-right"><strong><?= $today_opening_credit ?></strong></td>
              <!-- <th class="text-center">Balance</th>-->
            </tr>
              <tr>
                  <th>Sr No.</th>
          <th>Transaction Date</th>
          <th>Description</th>
          <th>Debit</th>
          <th>Credit</th>     
        </tr>
          </thead>
          <tbody> 
            <?php 
              $count = 0;
              $grand_total=0;
        $total_credit=0;
        $total_debit=0;
        $total_debit1=0;
        $closing_amount=0;
        if($ctable_r)
        {  
                while($ctable_d = mysqli_fetch_array($ctable_r))
                {
                  ?>
                  <tr>
                    <td><?php echo ++$count;  ?></td>
              <td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
              <td> <?= $ctable_d['description'];  ?></td>
              <?php
              if($ctable_d['debit']<0)
              { 
                $total_debit+=$ctable_d['debit'];
                $total_debit1 = -0-$total_debit;
                ?> 
                <td align="right"><?php echo 0-$ctable_d['debit']; ?></td>
                <td> </td>
                <?php
              }
              if($ctable_d['credit']>0)
              { 
                $total_credit+=$ctable_d['credit'];
                ?> 
                <td> </td>
                <td align="right"><?php echo stripslashes($ctable_d['credit']); ?></td> 
                <?php
              }
              ?>
                  </tr> 
                <?php
              } 
          ?>
          <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td align="right"><b><?php echo round(-0-$total_debit,2);?></b></td>
            <td align="right"><b><?php echo $total_credit;?></b></td>
          </tr> 
          <tr>
            <td colspan=3 align="right"><b>Closing Balance</b></td>
            <?php 
            $closing_amount = ($total_credit + $today_opening_credit) - ($total_debit1 + $today_opening_debit); 
                ?>
                <td style="text-align: right!important;"><b><?= ($closing_amount<0)?-0-$closing_amount:""; ?></b></td>
                <td style="text-align: right!important;"><b><?= ($closing_amount>0)?$closing_amount:""; ?></b></td>
          </tr>
          <?php
        }
        else
        {
          ?>
          <tr><td colspan="5" class="text-center">No data found for this date!!</td></tr>
          <?php
        }
        ?>  
          </tbody> 
      </table> 
  </form>
  <?php
}
else
{ 
  ?> 
  <h3 class="text-center" style="margin: 0;padding: 10px;">Please Select Customer Too See Ledger</h3>
  <?php
}
?>
<?php require_once("disconnect.php"); ?>