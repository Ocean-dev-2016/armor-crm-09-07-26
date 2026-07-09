<?php 
$page_id=583;$page_slug='future_followup_manage';
require_once("connect_in.php");
$company_id = $_REQUEST['id'];

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR mobile_no like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}

$ctable_where .= " isDelete=0";

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0){
	$ctable_where.=" AND company_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
}
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!=""){
	$date_filter_query=trim($db->clean($_REQUEST['ToDate']));
	$date_filter_query_ex=explode("-",$date_filter_query);
	$ctable_where = ($ctable_where=="")?" (
							DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."' ) AND ":$ctable_where." AND(
							DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";

	
}
if(isset($_REQUEST['category']) && $_REQUEST['category']!="" && $_REQUEST['category']!=NULL)
{

 $ctable_where .= " AND category_id= '".$_REQUEST['category']."' ";
}
if(isset($_REQUEST['visitor_count']) && $_REQUEST['visitor_count']!="")
{
	$ctable_where .=" AND visitor_counter='".$_REQUEST['visitor_count']."'";
}
$customer_details = $db->rp_getData("visitor","*",$ctable_where,"",0);

if($_REQUEST['ToDate']!=""){
	$date_filter_query=trim($db->clean($_REQUEST['ToDate']));
	$date_filter_query_ex=explode("-",$date_filter_query);
	$from_date=date("d-m-Y",strtotime($date_filter_query_ex['0']));
	$to_date=date("d-m-Y",strtotime($date_filter_query_ex['1']));
}
else{
	$from_date="";
	$to_date="";
}
?>
<style>
table{
    height: auto;	
    width:1200mm;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 12pt;
	margin:auto;
	padding:auto;
}
table{
    height: auto;	
    width:1200mm;
}
table , td, th {
 border: 1px solid #595959;
 border-collapse: collapse;
}
td, th {
 padding: 2px;
 width: 150px;
 height: 45px;
}
th {
}
.center{
	text-align:center;
}
.right{
	text-align:right;
	padding-right:15px;
}
</style>
 
<table>
 <tbody>
  <tr>

   <td colspan="16" class="right" style="border-right:none;"><b><center><h3>Visitor Information</h3></center></td>
  <!--<td colspan="3" style="border-left:none;"> <b>Date : </b><?php echo date('d-m-Y',strtotime($customer_detail['created_date'])) ?></td>-->
   
  </tr>
	<tr>
		<td class="left" colspan="16" ><h4>From Date: <?php echo $from_date; ?></h4>
		<h4>To Date: <?php echo $to_date; ?></h4></td>
	</tr>
	<tr>
		<td class="center"><b>No.</b></td>
	    <td class="center"><b>Name</b></td>
	    <td class="center"><b>Project Manager</b></td>
	    <td class="center"><b>Executive</b></td>
	   	<td class="center" ><b>Category</b></td>	     	   
	   	<td class="center"><b>Phone</b></td>
	   	<td class="center" ><b>Email</b></td> 
	   	<td class="center" ><b>Reference</b></td>	     	   
	   	<td class="center" ><b>Reference Media</b></td>	     	   
	   	<td class="center" colspan="2"><b>Detail</b></td>
	   	<td class="center"><b>Current Address</b></td>
	   	<td class="center"><b>Project</b></td>
		<td class="center" colspan="2"><b>Registered Date</b></td>	   
		<td class="center">Visitor Counter</td>
	   
	</tr>
  <?php
	$count=0;
  	while($customer_detail = mysqli_fetch_array($customer_details)){

  		/*for Project Manager*/
	  		if($customer_detail['project_manager_id']!=0)
	        $project_manager=$db->rp_getValue(CTABLE_ADMIN,"name","id='".$customer_detail['project_manager_id']."'");
	        else
	        $project_manager="All Manager";
    	/*for Project Manager*/


    	/*executive*/
	    	if($customer_detail['user_id']!=0)
	        $executive=$db->rp_getValue(CTABLE_ADMIN,"name","id='".$customer_detail['user_id']."'",0);
	        else
	        $executive="";
    	/*executive*/

	  	$category_ids=explode(",",$customer_detail['category_id']);
		$category_name=array();
		foreach($category_ids as $c_nm)
		{
			if($c_nm==0)
			{
				$category_name[]="Other";
			}
			else
			{
				$category_name[]=$db->rp_getValue("category","name","id='".$c_nm."' AND isDelete=0");
			}
		}
		$category_name=implode(", ", $category_name);
	  ?>
  <tr>
		<td class="center"><?php echo ++$count; ?></td>
		<td class="center"><?php echo $customer_detail['name'] ?></td>
		<td class="center"><?php echo stripslashes($project_manager); ?></td>
		<td class="center"><?php echo stripslashes($executive); ?></td>
		<td class="center"><?php echo $category_name ?></td>
		<td class="center"><?php echo $customer_detail['mobile_no'] ?></td>
		<td class="center"><?php echo $customer_detail['email'] ?></td>
		<td class="center"><?php echo html_entity_decode(stripslashes($customer_detail['reference'])); ?></td>
		<td class="center"><?php echo $db->rp_getValue("reference_media","name","id='".$customer_detail['reference_media_id']."'");?></td>
		<td class="center" colspan="2"><?php echo $customer_detail['detail'] ?></td>
		<td class="center"><?php echo $customer_detail['current_address']?>
				<?php
				if($customer_detail['visitor_city']!="")
				{
					echo ",".$customer_detail['visitor_city'];
				}
				if($customer_detail['visitor_state']!="")
				{
					echo ",".$customer_detail['visitor_state'];
				}
				if($customer_detail['visitor_country']!="")
				{
					echo ",".$customer_detail['visitor_country'];
				}
				?>
		</td>
		<td class="center"><?php echo $db->rp_getValue("project","title","id='".$customer_detail['project_id']."'"); ?></td>
		<td class="center" colspan="2"><?php echo date('d-m-Y',strtotime($customer_detail['created_date'])); ?></td>
		<td><?php echo $customer_detail['visitor_counter']; ?></td>
  </tr>
  <?php 
  
  } ?>
  </tbody>
  </table>
  <?php 
if(isset($_REQUEST['p']) && $_REQUEST['p']==1)
{
	echo "<script>window.print();window.close();</script>";
}
?>