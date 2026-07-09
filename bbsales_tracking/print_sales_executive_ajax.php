<?php
$page_id=572;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "sales_executive";
$Where = "";

if( isset($_REQUEST['searchName']) && !empty($_REQUEST['searchName']) ) {
  $Query=$_REQUEST['searchName'];
  $Where.= " (name like '%".$db->clean($_REQUEST['searchName'])."%' OR email like '%".$db->clean($_REQUEST['searchName'])."%' OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%' OR username  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
  ) AND ";
}

if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
  $Where .= " type = '".$_REQUEST['type']."' AND";
}

if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
  $Where .= " state = '".$_REQUEST['state']."' AND ";
}

if(isset($_REQUEST['main_city']) && $_REQUEST['main_city']!="" && $_REQUEST['main_city']!=NULL)
{
  $Where .= " main_city = '".$_REQUEST['main_city']."' AND";
}
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
  $Where .= " city = '".$_REQUEST['city']."' AND";
}

if(isset($_REQUEST['zone']) && $_REQUEST['zone']!="" && $_REQUEST['zone']!=NULL)
{
  $Where .= " zone = '".$_REQUEST['zone']."' AND";
}

$Where .= " isDelete=0";
	

$ctable_r = $db->rp_getData($ctable,"*",$Where,"id DESC",0);
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
        <h2>Sales Officer Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      </th>
    </tr>
    <tr>
      <th>Sr No.</th>
      <th>Sales Officer Type</th>
      <th>Name</th>
      <th>Username</th>
      <th>Email</th>
      <th>CUG No</th>
      <th>State</th>
      <th>City</th>
      <th>Route</th>
      <th>Zone</th>
    </tr>
  </thead>
  <tbody>
    <?php
  	if(mysqli_num_rows($ctable_r)>0)
  	{
      $count = 0;
      while($ctable_d = mysqli_fetch_array($ctable_r))
      {
        $sales_executive_type = "";
        if($ctable_d['type']=="sales_manager")
            {
        $sales_executive_type="M.D.";
      }

      if($ctable_d['type']=="area_sales_manager")
      {
        $sales_executive_type="General Manager";
      }

      if($ctable_d['type']=="dispatch_sales_manager")
      {
        $sales_executive_type="Dispatch Manager";
      }
      
      if($ctable_d['type']=="sales_officer")
      {
        $sales_executive_type="Regional Sales Manager";
      }
      
      if($ctable_d['type']=="sales_executive")
      {
        $sales_executive_type="Sales Officer";
      }
        if($ctable_d['type']=="area_manager")
      {
        $sales_executive_type="Area Sales Manager";
      }
      if($ctable_d['type']=="service_executive")
      {
        $sales_executive_type="Service Executive";
      }
  			?>
          <tr>
        		<td><?php echo ++$count; ?></td>
			      <td><?php echo $sales_executive_type; ?></td>
            <td><?php echo stripslashes($ctable_d['name']); ?></td>
            <td><?php echo stripslashes($ctable_d['username']); ?></td>
  					<td><?php echo stripslashes($ctable_d['email']); ?></td>
  					<td><?php echo stripslashes($ctable_d['phone']); ?></td>
  					<td><?php echo stripslashes($ctable_d['state']); ?></td>
  					<td><?php echo $ctable_d['main_city']; ?></td>
            <td><?php echo $ctable_d['city']; ?></td>
            <td> <?php echo $db->rp_getValue("zone","name","id='".$ctable_d['zone']."' AND isDelete=0",0); ?></td>
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