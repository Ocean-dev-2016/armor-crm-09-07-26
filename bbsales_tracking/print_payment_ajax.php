<?php
$page_id=572;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "payment";
$Where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
    $customer_r=$db->rp_getData("executive","id","company_name like '%".$_REQUEST['searchName']."%' OR cname like '%".$_REQUEST['searchName']."%'","",0);
    $cust_id=array();
    if($customer_r)
    {
        while($customer_d=mysqli_fetch_assoc($customer_r))
        {
            $cust_id[]=$customer_d['id'];
        }
    }
    $cust_id=implode(",",$cust_id);
    $Where .="customer_id IN (".$cust_id.") AND";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL  && $_REQUEST['type']!="undefined")
{
    $Where .= "  payment_type = '".$_REQUEST['type']."' AND ";
    $type=$_REQUEST['type'];
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $Where .= "  payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' AND ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
    $Where .= "  payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND ";
}

$Where .= " isDelete=0";
$ctable_r = $db->rp_getData($ctable,"*",$Where,"payment_date DESC",0);
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
<table id="datatable_1" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="12" class="center">
                <h2>Payment Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
            </th>
        </tr>
        <tr>
            <th>Sr No.</th>
            <th>Company Name</th>
            <th>Customer Name</th>
            <th>Sales Person Name</th>
            <th>Receipt No.</th>
            <th>Payment by</th> 
            <th>Payment Date</th>
            <th style="text-align:right;">Payment Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
  	if(mysqli_num_rows($ctable_r)>0)
  	{
        $count = 0;
        while($ctable_d = mysqli_fetch_array($ctable_r))
        {
            ?>
            <tr>
                <td><?php echo ++$count; ?></td>
			    <td><?php $company_name=$db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'");?><?php echo stripslashes($company_name); ?></td>
                <td><?php echo $customer_name=$db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'");?></td>
                <td><?php echo $customer_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");?></td>
                <td><?php echo stripslashes($ctable_d['receipt_no']); ?></td>
                <td><?php if($ctable_d['payment_type']==1) {$type = "By Cash"; }else if ($ctable_d['payment_type']==2){$type = "By Cheque"; }else if ($ctable_d['payment_type']==3){$type = "Online"; }else if ($ctable_d['payment_type']==4){$type = "Other"; }echo $type;?></td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
                <td align="right"><?php echo stripslashes(CURR.($db->rp_num($ctable_d['paid_amount']))); ?></td>
    		</tr>
  		    <?php
        }
	}
	else
	{
		?>
		<tr>
			<th colspan="8" style="text-align: center;">No Data Found</th>
	    </tr>
	    <?php
	}
	?>
	</tbody>
</table>
<?php require_once("disconnect.php"); ?>