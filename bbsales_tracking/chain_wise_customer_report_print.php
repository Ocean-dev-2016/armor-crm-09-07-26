<?php
$page_id=654;$page_slug='chain_wise_customer_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable 	= "executive";
$ctable1 	= "Orders";
$ctable_where = "";
$distibutor_where = "";
$area=$_REQUEST['area'];
$ctable_where .= " isDelete=0 AND type_of_executive=1";	

// Get the total number of rows in the table


if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL && $_REQUEST['class_id']!="null")
{
	$state_r = $db->rp_getData("class","name","id in (".$_REQUEST['class_id'].")","",0);
	while($state_d = mysqli_fetch_array($state_r)) 
	{
		$state_str[] = "'".$state_d['name']."'";
	}
	$class_str = implode(",",$state_str);
	$ctable_where .= " AND  state IN (".$class_str.") ";
}
//for area----//
if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL && $_REQUEST['area']!="null")
{
	$city_r = $db->rp_getData("city","name","id in (".$_REQUEST['area'].")","",0);
	while($city_d = mysqli_fetch_array($city_r)) 
	{
		$city_str[] = "'".$city_d['name']."'";
	}
	// echo implode(",",$city_str);exit;
	$ctable_where .= " AND main_city IN (".implode(",",$city_str).") ";
			
}

if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="" && $_REQUEST['customer_type']!="null" &&  $_REQUEST['customer_type']!="0" )
{
	if($_REQUEST['customer_id'] == "")
	{
		$ctable_where.=" AND type_of_executive='".$_REQUEST['customer_type']."'";

	} 
}

if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!="null")
{
	$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='".$_REQUEST['customer_id']."'");

	if($get_customer_type == 2)
	{
		$super_stockist_id=$db->rp_getValue("executive","super_stockist_id","isDelete=0 AND id='".$_REQUEST['customer_id']."'");
		$ctable_where.=" AND id=".$super_stockist_id." ";
		$distibutor_where .=" AND id=".$_REQUEST['customer_id']." ";

	}else if($get_customer_type == 1)
	{
		$ctable_where.=" AND id=".$_REQUEST['customer_id']." ";
	} 
}
if(isset($_REQUEST['route']) && $_REQUEST['route']!="" && $_REQUEST['route']!=NULL && $_REQUEST['route']!="null")
{
	$area_r = $db->rp_getData("area","name","id in (".$_REQUEST['route'].")","",0);
	while($area_d = mysqli_fetch_array($area_r)) 
	{
		$area_str[] = "'".$area_d['name']."'";
	}
	// echo implode(",",$area_str);exit;
	$ctable_where .= " AND city IN (".implode(",",$area_str).") ";
			
}

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "" && $_REQUEST['searchName'] != NULL && $_REQUEST['searchName'] != "null")
{
	$ctable_where .= " AND company_name = '".$_REQUEST['searchName']."' ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);


$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
?>
<?php 
if($_REQUEST['customer_type']!="" && $_REQUEST['customer_id']!="")
{
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
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
        <thead>
			<tr>
				<td class="header" align="center" colspan="12" ><h3><b>Chain Wise Customer Report</b></h3></td>
			</tr>
			
			
            <tr class="tr">
				
                <th class="th">Sr.<br> No.</th>
                <th class="th">Super StockList.</th>
                <th class="th">Distributor</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 1;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
			
        	?>
            <tr class="tr">
                <td class="td" style="width:5px;"><?php echo $count++; ?></td>

                <td class="td"><?php echo $ctable_d['company_name'] ?></td>

				<td>
					<?php
						$distributor = $db->rp_getData("executive","company_name,id,dealer_distributor_id","type_of_executive=2 AND super_stockist_id='".$ctable_d['id']."'".$distibutor_where,"",0)
					?>
					<table style="width:100%;vertical-align: top;" class="table1 table-bordered table-striped dataTable">
						<tbody>
							<?php
								while($distributor_d = mysqli_fetch_array($distributor))
								{
									?>
									<tr class="tr1">
										<td class="td1">
											<?php 
												echo $distributor_d['company_name'];
												$retailer = $db->rp_getData("executive","company_name","type_of_executive=3 AND dealer_distributor_id='".$distributor_d['id']."'","",0)
											?>
										</td>
										<td>
											<table style="width:100%;vertical-align: top;" class="table1 table-bordered table-striped dataTable">
												<thead>
													<tr>
														<b>Retailer</b><br>
													</tr>
												</thead>
												<tbody>
													<tr>
														<?php
														while($retailer_d = mysqli_fetch_array($retailer)) {

															echo  $retailer_d['company_name']."<br>"; 
														}
														?>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<?php
								}
							?>
						</tbody>
					</table>
				</td>
			</tr>
        	<?php
            }
        }
		
        ?>
        </tbody>
    </table>

<?php 
}


?>

