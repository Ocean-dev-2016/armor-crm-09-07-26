<?php
$page_id=406;$page_slug='page_admin';


include("connect.php");
$ctable 	= "dealer_distributor_network";

$TYPE=array(1=>"Customer",2=>"Sales Exective");

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
  $Where .= " (
              name like '%".$_REQUEST['searchName']."%'         
            ) AND ";
}

$Where .= " isDelete=0";
	
$ctable_r = $db->rp_getData($ctable,"*",$Where,"id DESC",0);

/*for log*/
$flag = "Web";
$module_name = "Category";
$log_description = $module_name." Printed By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$last_id = "";
$db->insertLog($ctable,$last_id,"print","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
/*for log*/
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
        	<h2> System User  <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
      	</th>
    	</tr>
    	<tr>
      		<th width="7%">Sr No.</th>
    		  <th>Name</th>
	      	<th>UserName</th>                			
          <th>Admin Type</th>                			
          <th>Type</th>                           
          <th>Email</th> 
    	</tr>
  	</thead>
 	<tbody>
  	<?php
	  	if(mysqli_num_rows($ctable_r)>0){
	    $count = 0;
	    
	    while($ctable_d = mysqli_fetch_array($ctable_r)){
	      $count++;
  	?>
    	<tr>
    
			<td width="7%"><?php echo $count; ?></td>
			<td>
				<?php 
				echo stripslashes($ctable_d['name']); 
				?>
			</td>
			 <td><?php echo stripslashes($ctable_d['username']); ?></td>				
        <td><?php echo $db->rp_getValue("admin_type","name","id='".$ctable_d['admin_type']."'",0); ?></td>	
        <td><?= $TYPE[$ctable_d['type']];?></td>			
        <td><?php echo stripslashes($ctable_d['email']); ?></td>
    	</tr>
	  <?php
	    }
	  }
	  ?>
  	</tbody>
</table>
 <?php require_once("disconnect.php"); ?>