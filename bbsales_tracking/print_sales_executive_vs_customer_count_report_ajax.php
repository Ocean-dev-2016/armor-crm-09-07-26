<?php 
$page_id=577;$page_slug='visit_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "sales_executive";
$ctable1 	= "User";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$ctable_where .= " ( name like '%".$db->clean($_REQUEST['searchName'])."%' ) AND ";
}

$ctable_where .= " isDelete=0";

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
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
</style>
<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
        	 <tr>
            <th colspan="12" class="center"><h2>Sales Officer Vs Customer Count Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
          </tr>
        	<tr>
                <th width="5%;">No.</th>
                <th>Sales Person Name</th>
                <th>Customer Count</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            //print_r($ctable_d);
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?= $ctable_d['name'] ?></td>
                <td><?= $db->rp_getTotalRecord("executive","isDelete=0 AND seid='".$ctable_d['id']."' ") ?></td>
			</tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
<?php require_once("disconnect.php"); ?>