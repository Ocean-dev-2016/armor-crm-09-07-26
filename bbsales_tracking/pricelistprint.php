<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
$search_name = $_REQUEST['search'];
if( isset($search_name) && !empty($search_name) ) {
    $Query=$search_name;
    $Where.=" (id LIKE '%".$Query."%'  OR pricelist_name LIKE '%".$Query."%' ) AND ";
    $pricelist_r = $db->rp_getData("price_list","*",$Where."isDelete=0 AND isActive=1","pricelist_name ASC",0);
}
else
{
  $pricelist_r = $db->rp_getData("price_list","*","isDelete=0 AND isActive=1","pricelist_name ASC",0);
}
/*$ctable="product_price_list";
$maxdate=$db->rp_getMaxVal($ctable,"modified_date","isDelete=0");
$mindate=$db->rp_getMinVal($ctable,"modified_date","isDelete=0");
$date=date("d-m-Y",strtotime($mindate))." to ".date("d-m-Y",strtotime($maxdate));*/
$date=date("d-m-Y");
?>
<style>
table{
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
th.header { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
.uppercase,.table-heading
{
	text-transform: uppercase;
}
.m-b-0
{
  margin-bottom: 0px;
}
.price_table
  {
    width:190mm;
  }
</style>
<table class="price_table">
	<thead>
    <tr>
        <th class="header" align="center" colspan="10" ><h1 class="uppercase m-b-0"><b>Pricelist <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></b></h1></th>
    </tr>
    <tr class="table-heading">
      <th>Sr.no</th>
			<th>Pricelist Name</th>
			<!-- <th>User Name</th> -->
     <!--  <th>Last Udpated Date</th>  -->
		</tr>
    </thead>
    <tbody>
    	<?php
    	if($pricelist_r)
    	{
    		$cnt=0;
    		while($pricelist_d=mysqli_fetch_assoc($pricelist_r))
    		{ 
    			$cnt++;
    			$customer_data = $db->rp_getData("customer","*","price_list_id IN (".$pricelist_d['id'].") AND isDelete=0");
    			$customer_name = array();
    			while($customer_data_d = mysqli_fetch_assoc($customer_data))
    			{
    				$customer_name[] = $customer_data_d['company_name'];
    			}
    			$customer_name = implode("<br/>",$customer_name);
    		?>
    			<tr>
    				<td><?php echo $cnt; ?></td>
    				<td><?php echo $pricelist_d['pricelist_name']; ?></td>
    				<!-- <td><?php echo $customer_name; ?></td> -->
         <!--  <td>
              <?php
              $dt=$db->rp_getMaxVal("product_price_list","modified_date","price_list_id='".$pricelist_d['id']."' AND modified_date!='0000-00-00 00:00:00'");
                if($dt)
                {
                 echo date("d-m-Y h:i A",strtotime($dt)); 
                }
                ?>
            </td>  -->
    			</tr>
    		<?php
    		}
    	} 
    	?>
    </tbody>
</table>
<?php require_once 'disconnect.php';  ?>