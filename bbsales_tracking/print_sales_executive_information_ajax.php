<?php
$page_id=572;$page_slug='no_order_inquiry_page';
include("connect.php");
$ctable 	= "sales_executive";
$Where = "";


$Where = "id='".$_REQUEST['id']."' AND isDelete=0 ";
	

$ctable_r = $db->rp_getData("sales_executive","*",$Where,"id DESC",0);
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
        <h2>Sales Officer Information Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      </th>
    </tr>
    <tr>
      <th>Sr No.</th>
      
      <th>Name</th>
      <th>Sales Excecutive Type</th>
      <th>Phone</th>
      <th>Email</th>
      <th>Address</th>
      <th>Pin Code</th>
      <th>Country</th>
      <th>State</th>
      <th>City</th>
      <!-- <th>IMEI</th> -->
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
        
      
      
  			?>
          <tr>
        		<td><?php echo ++$count; ?></td>
			      <td><?php echo $ctable_d['name'];  ?></td>
            <td><?php echo  $ctable_d['type']; ?></td>
  					<td><?php echo $ctable_d['phone']; ?></td>
  					<td><?php echo $ctable_d['email']; ?></td>
  					<td><?php echo $ctable_d['address']; ?></td>
  					<td><?php echo $ctable_d['zip']; ?></td>
            <td><?php echo $ctable_d['country']; ?></td>
            <td><?php echo $ctable_d['state']; ?></td>
            <td><?php echo $ctable_d['city']; ?></td>
            <!-- <td><?php echo $ctable_d['imei']; ?></td> -->
           
    			</tr>
  		    <?php
      }
	  }
	  else
	  {
		 ?>
		  <tr>
			  <th colspan="11" style="text-align: center;">No Data Found</th>
		  </tr>
	    <?php
	  }
	  ?>
	</tbody>
</table>
<?php require_once("disconnect.php"); ?>