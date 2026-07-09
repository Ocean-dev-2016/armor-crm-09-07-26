<?php
$page_id=585;$page_slug='meeting_master';
include("connect.php");
// $Where="isDelete=0";
$Where="";
$OrderBy="";
$Limit="";
$RequiredColumns="";
$RequestedData= $_REQUEST;
$customer_ids = array();
 
// Response Column Name Specify
$RequiredColumns = (isset($RequestedData['columns']))?$RequestedData['columns']:array(0=>"id",1=>"pricelist_name",);
// getting total number records without any search

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
	$Query=$_REQUEST['searchName'];
	
	$Where.=" (title LIKE '%".$Query."%' )" ;

	$customer_Data = $db->rp_getData("executive","id","company_name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	if($customer_Data)
	{
	    while($customer_Data_d=mysqli_fetch_assoc($customer_Data))
		{
			$customer_ids[]=$customer_Data_d['id'];
		}
		$customer_ids=implode(",",$customer_ids);
		$Where .= " OR customer_id IN (".$customer_ids.") AND";
	}
	else
	{
		$Where .= " customer_id IN ('') AND";	
	}

}

if(isset($_REQUEST["type"]) && $_REQUEST["type"]!="" && $_REQUEST["type"]!=undefined){
	$Where .= " meeting_type LIKE '%".trim($_REQUEST["type"])."%' AND";
}

$TotalFiltered = $db->rp_getTotalRecord("meeting",$Where);
if(isset($RequestedData['page']) && is_numeric($RequestedData['page']))
$PageNumber= filter_var($RequestedData["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$PageNumber=1;

if(isset($RequestedData['show']) && is_numeric($RequestedData['show']))
$LowerLimit= filter_var($RequestedData["show"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
else 
$LowerLimit=100;

if(isset($RequestedData['order']))
$OrderBy=$RequiredColumns[$RequestedData['order'][0]['column']]."   ".$RequiredColumns['order'][0]['dir'];
else
{
	$OrderBy="id DESC";
}
$UpperLimit=($PageNumber-1)*$LowerLimit;
if($UpperLimit!="" &&  $LowerLimit!="")
{
	$Limit=$UpperLimit." ,".$LowerLimit."   ";
	
}
else if($UpperLimit!="")
$Limit=$UpperLimit;
$RequiredColumns=implode(",",$RequiredColumns);
// $Results=$Pricelist->get_all($Where,$OrderBy,$Limit,$RequiredColumns);	

$Where .= " isDelete=0";

$Results=$db->rp_getData("meeting","*",$Where,$OrderBy,0,$Limit);
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
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
    	<tr>
	      <th colspan="12" class="center">
	        <h2>customer Meeting Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
	      </th>
	    </tr>
        <tr>

            <th>Sr.no</th>
			<th>Meeting Type</th> 
			<!-- <th>Meeting Host</th>  -->
			<th>Customer</th> 
			<th>Meeting Date & Time</th> 
			<th>Meeting Address</th> 
			<th>Gift Details</th> 
			<th>Expence</th> 
			
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{												
		  	// In Items there are all objects you need here are keys you will find in this array
			// id|name|slug														
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				// print_r($R);
				$cnt++;
				$totalPro=$db->rp_getValue("meeting_member","COUNT(id)","meeting_id='".$R['id']."' AND isDelete=0");
 		?>
	  	<tr class="">

			<td><?php echo $cnt; ?></td>
			<td><?php echo $db->rp_getValue("meeting_type","name","slug='".$R['meeting_type']."'",0); ?></td>
			<td><?php echo $db->rp_getValue("executive","company_name","id='".$R['customer_id']."'",0); ?></td>
			<td><?php echo date("d-m-Y h:i A",strtotime($R['meeting_date'])); ?></td>
			<td><?php echo $R['meeting_venue']; ?></td>
			<td><?php echo $R['gift_details']; ?></td>
			<td><?php echo $R['expence']; ?></td>
	 	</tr> 
		<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="8" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 
<?php require_once 'disconnect.php';  ?>
