<?php 
$page_id=666;$page_slug='manually_invoice_outstanding_import';
include("connect.php");
$ctable = "manually_invoice_outstanding_import"; 

// Get the total number of rows in the table

$ctable_where = "";
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= "(mobile_no1 like '%".$_REQUEST['searchName']."%' OR mobile_no2 like '%".$_REQUEST['searchName']."%' OR client_code like '%".$_REQUEST['searchName']."%' OR bill_no like '%".$_REQUEST['searchName']."%') AND ";
}
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="" && $_REQUEST['customer_id']!="undefined")
{
	$ctable_where .= "customer_id = '".$db->clean($_REQUEST['customer_id'])."' AND ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!="undefined")
{
	$ctable_where .= "sales_id = '".$db->clean($_REQUEST['sales_id'])."' AND ";
}
$ctable_where .= "isDelete=0";
  
$item_per_page =($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]))?intval($_REQUEST["show"]):500;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page");
?>
<style>
	.table-scrollable {
		width: auto;
		height: 450px;
		overflow-x: scroll;
		overflow-y: scroll;
		border: 1px solid #e7ecf1;
		margin: 10px 0 !important;
	}

	.fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
<div class="table-scrollable">
	<table id="example1" class="table table-bordered table-striped dataTable">
		<thead class="fix-th"> 
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th>
                	<select class="form-control input-small" id="customer_id" name="customer_id" onchange="displayRecords()">
						<option value="">Select Party</option>
		            	<?php 
		            	$cusR = $db->rp_getData("executive","company_name,cname,id","isDelete=0","",0);
		                if($cusR)
	                	{
		                	while ($cusD = mysqli_fetch_assoc($cusR))
		                	{
                		?>
            			<option value="<?=$cusD['id']?>" <?=($cusD['id']==$_REQUEST['customer_id'])?"selected":"";?>><?=$cusD['company_name']."-".$cusD['cname']?></option>
            			<?php 
		            		} 
	            		} 
	            		?>
				    </select>
                </th> 
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
                <th>  
                	<select class="form-control input-small" id="sales_id" onchange="displayRecords()">
	            		<option value="">Select Sales Person Name</option>
	                	<?php 
	                	$salesExe = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1","",0);
	                	if($salesExe)
                		{
	                		while ($salesD = mysqli_fetch_assoc($salesExe))
	                		{
            			?>
        				<option value="<?=$salesD['id']?>" <?=($salesD['id']==$_REQUEST['sales_id'])?"selected":"";?>><?=$salesD['name'];?></option>
        				<?php 
            				} 
            			} 
            			?> 
	            	</select>
            	</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th> 
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
			<tr> 
				<th class="fix-th1" style="width: 5%;">No.</th>
				<th class="fix-th1">Followup</th> 
				<th class="fix-th1">Whastapp</th> 
				<th class="fix-th1">Party Name</th> 
				<th class="fix-th1">Client Code</th>
				<th class="fix-th1">Bill No.</th>
				<th class="fix-th1">Bill Date</th>
				<th class="fix-th1">Due Days</th>
				<th class="fix-th1">Bill Amount</th>
				<th class="fix-th1">Balance Amount</th>
				<th class="fix-th1">Sales Person</th>
				<th class="fix-th1">Mobile-1</th>
				<th class="fix-th1">Mobile-2</th>
				<th class="fix-th1">E-Mail</th>
				<th class="fix-th1">Detail</th>
				<th class="fix-th1">PDC Chq No.</th>
				<th class="fix-th1">PDC Date</th>
				<th class="fix-th1">PDC Exp. Date</th>
				<th class="fix-th1">PDC Amount</th>
				<th class="fix-th1">Security Chq No.</th>
				<th class="fix-th1">Security Chq Amt</th>  
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
				<td><?php echo $count; ?></td> 
				<td class="text-center">
                    <?php
                    $SEID = $db->rp_getvalue("dealer_distributor_network", "sales_executive_id", "id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
                    ?>
                    <a class="followup_datails text-change-color" data-id="<?= stripslashes($ctable_d['id']); ?>" data-sales_id="<?= $SEID ?>" data-visitor_id="<?= $ctable_d['customer_id'] ?>" data-toggle="modal" data-target="#followup_datails">
                        <span class="text-success">
                            <i class="fa fa-eye" style="color:#000;font-size:16px"></i>
                        </span>
                    </a>
                </td>
                <td class="text-center"><a onClick="sendWhatsappMsg('<?= $ctable_d['id'] ?>')" class="text-success"><i class="fa fa-whatsapp" style="font-size:16px"></i></a></td>
				<td><?= $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'") ?></td>
				<!-- <td><?= $ctable_d['party_name']; ?></td>   -->
				<td><?= $ctable_d['client_code']; ?></td>
				<td><?= $ctable_d['bill_no']; ?></td> 
				<td><?= ($ctable_d['bill_date'] != '0000-00-00' && $ctable_d['bill_date']!='1970-01-01')?date('d-m-Y',strtotime($ctable_d['bill_date'])):""; ?></td>
				<td><?= $ctable_d['due_days']; ?></td>
				<td><?= number_format($ctable_d['bill_amount'],2); ?></td>
				<td><?= number_format($ctable_d['balance_amt'],2); ?></td>
				<td><?= $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'") ?></td>
				<td><?= $ctable_d['mobile_no1']; ?></td>  
				<td><?= $ctable_d['mobile_no2']; ?></td>  
				<td><?= $ctable_d['email']; ?></td>  
				<td><?= $ctable_d['detail']; ?></td>  
				<td><?= $ctable_d['pdc_check_no']; ?></td>  
				<td><?= ($ctable_d['pdc_date'] != '0000-00-00' && $ctable_d['pdc_date']!='1970-01-01')?date('d-m-Y',strtotime($ctable_d['pdc_date'])):""; ?></td>
				<td><?= ($ctable_d['pdc_exp_date'] != '0000-00-00' && $ctable_d['pdc_exp_date']!='1970-01-01')?date('d-m-Y',strtotime($ctable_d['pdc_exp_date'])):""; ?></td>
				<td><?= $ctable_d['pdc_amount']; ?></td>  
				<td><?= $ctable_d['security_chq_no']; ?></td>  
				<td><?= $ctable_d['security_chq_amt']; ?></td>  
			</tr>
		<?php
			}
		}
		?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="dataTables_info"> Rows Limit:
			<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
				<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
										echo ' selected="selected"';
									}  ?>>500</option>
				<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
										echo ' selected="selected"';
									}  ?>>1000</option>
				<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
											echo ' selected="selected"';
										}  ?>>2000</option>
				<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
											echo ' selected="selected"';
										}  ?>>5000</option>
			</select>
		</div>
	</div>
</div> 

<!-- Modal -->
<div id="followup_datails" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 970px; z-index: 9999;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <br>
                <div class="row">
                    <div class="col-xs-9">
                        <h4 class="modal-title"><b>Followup History</b></h4>
                    </div>
                    <div class="col-xs-3 text-right">
                        <a href="#createFollowup" class="btn btn-success" id="openCreateFollowupModal" target="#createFollowup" data-toggle="modal" title="Edit">
                            <i class="fa fa-plus"></i> Create Followup
                        </a>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div id="followup_data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$("#sales_id").select2();
	$("#customer_id").select2();

	$("#followup_datails").on("show.bs.modal", function(event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var invoice_id = $(button).data("id");
        var sales_id = $(button).data("sales_id");
        var visitor_id = $(button).data("visitor_id");
        $("#openCreateFollowupModal").data("id", invoice_id);
        $("#openCreateFollowupModal").data("visitor_id", visitor_id);


        $.ajax({
            url: "followup_data_ajax.php",
            data: {
                reference_id: invoice_id,
                followup_flag: "manual_invoice_import",
                sales_id: sales_id,
                visitor_id: visitor_id,
            },
            beforeSend: function() {
                $("#followup_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
            },
            success: function(result) {
                $("#followup_data").html(result);
            }
        });
    });
</script>
<?php require_once "disconnect.php"; ?>