<?php
$page_id=633;$page_slug='manual_stock_page';
include("connect.php");

// echo $_REQUEST['planning_date']; exit;

// $Where = " AND planning_date = '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
if(isset($_REQUEST['planning_date']) && $_REQUEST['planning_date']!="" && $_REQUEST['planning_date']!=NULL)
{
 $Where .= "planning_date = '".date_format(date_create($_REQUEST['planning_date']), "Y-m-d") ."' AND isDelete=0";
}
else{
$Where="isDelete=0";
}

$Results=$db->rp_getData("inward_stock","*",$Where,"id DESC",0);
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

            <td class="header" align="center" colspan="8" >
            	<h2>
            		<b>Daily Products Report <?php echo $_REQUEST['planning_date'];?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></b>
            	</h2>
            </td>
        </tr>

        <tr>
            <th>Sr.no</th>
			<th>Product Name</th> 
			<th>Qty</th> 
			<th>Stock Added Date</th> 

			<!-- <th>Invoice No</th> 
			<th>Invoice Date</th>  -->
			<th>Warehouse</th> 

			<th>Remark</th> 
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{	
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{	


				$Warehouse_id = $R['warehouse_id'];
				$warehouseids = array();
				$warehouseR = $db->rp_getData("warehouse","*","id In (".$Warehouse_id.") AND isDelete=0","",0);
				while($warehouseD = mysqli_fetch_assoc($warehouseR))
				{
					$warehouseids[] = $warehouseD['name'];
				}
				$warehouse_name = implode(",", $warehouseids);

				$cnt++;
 				?>
			  	<tr class=""> 
					<td><?php echo $cnt; ?></td>
					<td><?php echo $R['pro_name']." - ".$db->rp_getValue("product_weight_price","catno","product_id='".$R['pro_id']."'"); ?></td>
					<td><?php echo $R['pro_qty']; ?></td>
					<td><?php echo date('d-m-Y',strtotime($R['planning_date'])); ?></td>

					<!-- <td><?php echo $R['invoice_no']; ?></td> -->
					<!-- <td><?php echo date('d-m-Y',strtotime($R['invoice_date'])); ?></td> -->

					<?php 
						if($R['invoice_date']!="" && $R['invoice_date']!="01-01-1970" && $R['invoice_date']!="0000-00-00" && $R['invoice_date']!="1970-01-01"){

							$invoice_date1 = date('d-m-Y',strtotime($R['invoice_date']));

						}else{
							$invoice_date1 = "";							
						}

					 ?>
					<!-- <td><?php echo $invoice_date1; ?></td> -->

					<td><?php echo $warehouse_name; ?></td>

					<td><?php echo $R['remark']; ?></td>
			 	</tr> 
				<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="6" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 
<?php require_once "disconnect.php"; ?>
