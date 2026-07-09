<?php 
$page_id=581;$page_slug='manage_complain';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "lr_detail";
// $ctable_where.="isDelete=0";

$ctable_where = "";
// Get the total number of rows in the table

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $getR = $db->rp_getData("invoice_new","id","invoice_no LIKE '%".$Query."%' AND isDelete=0");
  if($getR)
  {
    $invoice_id = array();
    while($getD = mysqli_fetch_assoc($getR))
    {
      $invoice_id[] = $getD['id'];
    }
    $invoice_id = implode(",",$invoice_id);
    if($invoice_id!="")
    {
      $ctable_where.="  invoice_id IN (".$invoice_id.") AND ";  
    }
    else
    {
      $ctable_where.="  (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' ) AND ";   
    } 
  }
  else
  {
    $ctable_where.="  (id LIKE '%".$Query."%'  OR lr_number LIKE '%".$Query."%' ) AND ";
  }
}

$ctable_where .= " isDelete=0 ";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
  $page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
  $page_number = 1; //if there's no page number, set it to 1
}
// print_r($_REQUEST["sales_executive"]);exit;



$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id ASC",0);
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
        		<th colspan="18" class="center"><h2>LR Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
        	</tr>
        	<tr>
            <th>No.</th>
            <th>Invoice No</th>
            <th>LR Number</th>
            <th>Remark</th>
           
            <!-- <th>Update Entry Type</th>   -->
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($R = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
            $ENTRY_FLAG = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
            $status_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","-3"=>"Cancel");
            $complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
               <td><?php echo $invoice_no=$db->rp_getValue("invoice_new","invoice_no","isDelete=0 AND id='".$R['invoice_id']."'"); ?></td>
          <td><?php echo $R['lr_number']; ?></td>
          <td><?php echo $R['remark']; ?></td>
                
        
			</tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    <?php require_once 'disconnect.php';  ?>