<?php
$page_id=572;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "general_credit_note";
$Where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
    // $customer_r=$db->rp_getData("invoice","DISTINCT customer_id","customer_name like '%".$_REQUEST['searchName']."%'","",0);
    $customer_r=$db->rp_getData("executive","id","company_name like '%".$_REQUEST['searchName']."%' OR cname like '%".$_REQUEST['searchName']."%'","",0);
    $cust_id=array();
    if($customer_r){
        while($customer_d=mysqli_fetch_assoc($customer_r))
        {
            $cust_id[]=$customer_d['id'];
        }
    }
    $cust_id=implode(",",$cust_id);
    $ctable_where .="customer_id IN (".$cust_id.") AND";
}
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']; 

$ctable_where .= " isDelete=0";



if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{

}
else if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']==12) 
{
    
}
else
{
    $ctable_where .="  AND sales_executive_id IN(".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
    $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
    $ctable_where .= " AND payment_type = '".$_REQUEST['type']."' ";
    $type = $_REQUEST['type'];
}

if(isset($_REQUEST['payment_status']) && $_REQUEST['payment_status']!="" && $_REQUEST['payment_status']!=NULL)
{
    $ctable_where .= " AND payment_status = '".$_REQUEST['payment_status']."' ";
    $payment_status = $_REQUEST['payment_status'];
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
    $ctable_where .= " AND payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
    $ctable_where .= " AND payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"payment_date DESC limit $page_position, $item_per_page",0);
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
                <h2>General Credit  Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
            </th>
        </tr>
        <tr>
           <th>No.</th>
                <th>Company Name</th>
                <th>Customer Name</th>
                <th>Discount Type</th>
                <th>Sales Person Name</th>
                <!-- <th>Bill No.</th> -->
                <th>Credit Note No.</th>
                <th>Payment by</th> 
                <th>Credit Note Date</th>
                <th>Cheque No</th>
                <th style="text-align:right;">Payment Amount</th>
                <th style="text-align:right;">Status</th>
                
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
                
                <td>
                <?php               
                $company_name=$db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'");
                ?>
                <span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($company_name); ?></span></td>
                <td><?php               
                echo $customer_name=$db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."'");
                ?></td>
                <td>
                    <?php 
                    $get_discount_type_r=$db->rp_getData("discount_type","name","isDelete=0 AND id IN(".$ctable_d['discount_type_id'].")");
                $get_names=array();
                while ($get_discount_type_d=mysqli_fetch_assoc($get_discount_type_r)) 
                {
                        $get_names[]=$get_discount_type_d["name"];
                }
                $get_names=implode("</br>", $get_names);
                echo $get_names;

                    ?>
                </td>
                <td><?php               
                echo $customer_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_executive_id']."'");
                ?></td>
                <!-- <td><?php
                    //$dispatch_bill_no=$db->rp_getValue("invoice","invoice_no","id='".$ctable_d['dispatch_id']."'");
                    //echo $dispatch_bill_no; ?>
                </td> -->
                <td><?php echo stripslashes($ctable_d['receipt_no']); ?></td>
                <td>
                    <?php
                    if($ctable_d['payment_type']==1)
                    {
                        $type = "By Cash"; 
                    }
                    else if ($ctable_d['payment_type']==2)
                    {
                        $type = "By Cheque"; 
                    }
                    else if ($ctable_d['payment_type']==3)
                    {
                        $type = "Online"; 
                    }
                    else if ($ctable_d['payment_type']==4)
                    {
                        $type = "Other"; 
                    }
                    echo $type;
                    ?>
                </td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
                <td><?php echo $ctable_d['cheque_no']; ?></td>
                <td align="right"><?php echo stripslashes(CURR.($db->rp_num($ctable_d['paid_amount']))); ?></td>
                <td>
                    <?php
                    if($ctable_d['payment_status']==0)
                    { 
                        ?>
                        <button type="button" name="submit" value="print"  onClick="approvepayment('<?php echo $ctable_d['id']; ?>','approve','Are you sure you want to Approve payment ?')" class="btn yellow btn-sm"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Payment Approve</button>
                        <?php 
                    }
                    else
                    {
                        ?>
                        <span class="text-success"><i class="fa fa-check-circle"></i>&nbsp;Approved</span>
                        <?php
                    }
                    ?>
                </td> 
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
<?php require_once 'disconnect.php';  ?>