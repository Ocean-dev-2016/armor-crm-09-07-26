<?php
$page_id=655;$page_slug='sales_executive_wise_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable       = "orders";
$ctable1      = "Orders";
$ctable_where = "";
$area         = $_REQUEST['area'];

$Where='';$Where1='';$Where2='';$Where3='';$Where4='';$Where5='';$Where6=''; $Where7='';$Where8=''; $Where11='';$Where12='';

$Where = (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "")?" AND name LIKE '%".$db->clean($_REQUEST['searchName'])."%'":"";


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
<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll !important">
   <thead>
            <tr>
                <td class="header" align="center" colspan="10" ><h3><b><?=date("F - Y")?></b></h3></td>
            </tr>
            <tr>
                <td class="header" align="left" colspan="10" ><b>Name</b> :- Test <br><b>Number</b> :- 9523687410</td>
                <!-- <td class="header" align="left" colspan="5" ></td> -->
            </tr>
            <div style="position: sticky;">                
                <tr class="tr">
                    <th class="th" style="position: sticky;">Sr No.</th>
                    <th class="th">Date</th>
                    <th class="th">City</th>
                    <th class="th">Route</th>
                    <th class="th">Distributor</th>
                    <th class="th">Call</th>
                    <th class="th">New Call</th>
                    <th class="th">Total Call</th>
                    <th class="th">Convert</th>
                    <th class="th">Order Amount</th>                         
                </tr>
            </div>
        </thead>
        <tbody>
            <tr class="tr" data-toggle="modal" data-id="1" data-target="#orderModal">
                <td class="td" style="width:5px;" >
                    <?php echo $count++; ?>
                </td> 
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
                <td class="td">Test</td>
            </tr>
        </tbody>
</table>
    <!-- <script type="text/javascript">
    $("#total-prospect").html("<?php echo $t_prospect; ?>");
    $("#total-inquiry").html("<?php echo $t_inquiry; ?>");
    $("#total-lead").html("<?php echo $t_lead; ?>");
    $("#total-followups").html("<?php echo $t_followups; ?>");
    $("#total-quotation").html("<?php echo $t_quotation; ?>");
    $("#total-quotation-value").html("<?php echo $db->rp_number_format(stripslashes($t_quotation_val),2); ?>");
    $("#total-order").html("<?php echo $t_order; ?>");
    $("#total-order-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_val),2); ?>");
    $("#total-visit").html("<?php echo $t_visit; ?>");
    $("#total-complain").html("<?php echo $t_complain; ?>");
    $("#total-invoice").html("<?php echo $t_invoice; ?>");
    $("#total-invoice-value").html("<?php echo $db->rp_number_format(stripslashes($t_invoice_value),2); ?>");
    $("#total-order-pandding").html("<?php echo $t_order_pandding; ?>");
    $("#total-order-pandding-value").html("<?php echo $db->rp_number_format(stripslashes($t_order_pandding_value),2); ?>");
    $("#total-New-Customer-Onbord").html("<?php echo $t_new_customer; ?>");
</script> -->
