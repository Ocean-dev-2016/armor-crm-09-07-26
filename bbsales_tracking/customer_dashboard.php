<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$main_page = "home";
// echo $db->encrypt_decrypt('encrypt', "31-12-2023");exit;
$UsedPromotional=$db->rp_getTotalRecord("promotional_message_queue","sent=1",0);
/*$SingleTransactional=$db->rp_getTotalRecord("single_message","sent=1");*/
$SingleTransactional=$db->rp_getValue("single_message","SUM(total_sms)","sent=1");
$remainingPromotional=TRANSACTIONAL_SMS_COUNT-($UsedPromotional + $SingleTransactional);
$remainingPromotionalPer=round(100-($remainingPromotional*100/TRANSACTIONAL_SMS_COUNT),0,PHP_ROUND_HALF_UP);
$UsedPromotional = $UsedPromotional + $SingleTransactional;


$total_quotation_pending = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=0",0);
$total_quotation_approved = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=1",0);
$total_quotation_cancelled = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=3",0);
$total_quotation_cart = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=-1",0);
$total_quotation_genrated = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=4",0);
$total_quotation_lost = $db->rp_getTotalRecord("quotation_detail","isDelete=0 AND status=5",0);
$total_quotation = $db->rp_getTotalRecord("quotation_detail","isDelete=0",0);
$is_cp_dashboard = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_dashboard_cards = array();
$cp_recent_orders = array();
$cp_pending_orders = array();
if ($is_cp_dashboard) {
	$cp_id = function_exists('cp_get_login_channel_partner_id') ? (int) cp_get_login_channel_partner_id() : (int) $_SESSION[SITE_SESS . 'REFERANCE_ID'];
	$cp_dashboard_cards = array(
		array(
			'label' => 'My Customers',
			'value' => (int) $db->rp_getTotalRecord("channel_partner_customer", "channel_partner_id='" . $cp_id . "' AND isDelete=0", 0),
			'icon' => 'fa-users',
			'link' => 'channel_partner_customer_manage.php',
		),
		array(
			'label' => 'Customer Orders',
			'value' => (int) $db->rp_getTotalRecord("orders", "customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3)", 0),
			'icon' => 'fa-shopping-cart',
			'link' => 'channel_partner_order_manage.php',
		),
		array(
			'label' => 'Pending Payments',
			'value' => (int) $db->rp_getTotalRecord("orders", "customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3) AND (payment_received_flag=0 OR payment_received_flag IS NULL)", 0),
			'icon' => 'fa-money',
			'link' => 'channel_partner_payment.php',
		),
		array(
			'label' => 'Products In Stock',
			'value' => (int) $db->rp_getValue("customer_inward_stock", "COUNT(DISTINCT pro_id)", "customer_id='" . $cp_id . "' AND isDelete=0", 0),
			'icon' => 'fa-cubes',
			'link' => 'channel_partner_stock_manage.php',
		),
	);
	$recentOrderR = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,status,channel_partner_customer_id,payment_received_flag,payment_received_amount",
		"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3)",
		"id DESC LIMIT 5",
		0
	);
	if ($recentOrderR) {
		while ($row = mysqli_fetch_assoc($recentOrderR)) {
			$row['party_name'] = $db->rp_getValue("channel_partner_customer", "company_name", "id='" . (int) $row['channel_partner_customer_id'] . "'", 0);
			$cp_recent_orders[] = $row;
			if ((int) $row['payment_received_flag'] !== 1) {
				$cp_pending_orders[] = $row;
			}
		}
	}
	if (count($cp_pending_orders) < 5) {
		$pendingR = $db->rp_getData(
			"orders",
			"id,order_no,order_date,grand_total,status,channel_partner_customer_id,payment_received_flag,payment_received_amount",
			"customer_id='" . $cp_id . "' AND channel_partner_order_flag=1 AND channel_partner_customer_id>0 AND isDelete=0 AND status NOT IN (-2,3) AND (payment_received_flag=0 OR payment_received_flag IS NULL)",
			"id DESC LIMIT 5",
			0
		);
		$cp_pending_orders = array();
		if ($pendingR) {
			while ($row = mysqli_fetch_assoc($pendingR)) {
				$row['party_name'] = $db->rp_getValue("channel_partner_customer", "company_name", "id='" . (int) $row['channel_partner_customer_id'] . "'", 0);
				$cp_pending_orders[] = $row;
			}
		}
	}
}
// echo $total_quotation; exit();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Dashboard | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<style type="text/css">
#myList li{ 
	display:none;
}
#loadMore {
    color:green;
    cursor:pointer;
}
#loadMore:hover {
    color:black;
}
#showLess {
    color:red;
    cursor:pointer;
}
#showLess:hover {
    color:black;
}
.sty-tiles
{
	height: 95px;
	width: 95px;
	border-bottom: 1px solid;
}

.div-set-height
{
	height: 680px;
}

.cp-dash-hero {
	background: linear-gradient(135deg, #1f4e79 0%, #2f6f44 100%);
	color: #fff;
	border-radius: 12px;
	padding: 24px 28px;
	margin-bottom: 18px;
	box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}
.cp-dash-hero h2 {
	margin: 0 0 8px;
	font-size: 28px;
	font-weight: 700;
}
.cp-dash-hero p {
	margin: 0;
	font-size: 14px;
	opacity: 0.92;
}
.cp-dash-actions {
	margin-top: 16px;
}
.cp-dash-actions .btn {
	margin: 0 8px 8px 0;
	border-radius: 24px;
	border: none;
	background: rgba(255,255,255,0.92);
	color: #1f3d5a;
	font-weight: 600;
}
.cp-summary-card {
	background: #fff;
	border-radius: 12px;
	padding: 18px 18px 16px;
	margin-bottom: 18px;
	box-shadow: 0 8px 20px rgba(20, 35, 64, 0.08);
	border: 1px solid #e7edf5;
	min-height: 185px;
	position: relative;
}
.cp-summary-card .icon {
	width: 48px;
	height: 48px;
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	background: #eef5fb;
	color: #1f4e79;
	font-size: 20px;
	margin-bottom: 12px;
}
.cp-summary-card .value {
	font-size: 34px;
	font-weight: 700;
	color: #1f2d3d;
	line-height: 1.1;
	margin-bottom: 6px;
}
.cp-summary-card .label {
	display: block;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.08em;
	color: #6b7c93;
	margin: 0 0 16px;
}
.cp-summary-card a {
	font-weight: 600;
	position: absolute;
	left: 18px;
	bottom: 14px;
}
.cp-section-card {
	background: #fff;
	border: 1px solid #e7edf5;
	border-radius: 12px;
	box-shadow: 0 8px 20px rgba(20, 35, 64, 0.08);
	margin-bottom: 18px;
	overflow: hidden;
}
.cp-section-head {
	padding: 16px 18px;
	background: #f8fbff;
	border-bottom: 1px solid #e7edf5;
}
.cp-section-head h4 {
	margin: 0;
	font-size: 16px;
	font-weight: 700;
	color: #1f2d3d;
}
.cp-section-head p {
	margin: 4px 0 0;
	font-size: 12px;
	color: #6b7c93;
}
.cp-section-body {
	padding: 0;
}
.cp-mini-table {
	margin-bottom: 0;
}
.cp-mini-table th {
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.06em;
	color: #6b7c93;
	background: #fff;
}
.cp-mini-table td {
	vertical-align: middle !important;
}
.cp-badge {
	display: inline-block;
	padding: 4px 8px;
	border-radius: 999px;
	font-size: 11px;
	font-weight: 700;
}
.cp-badge.pending {
	background: #fff1f0;
	color: #c0392b;
}
.cp-badge.received {
	background: #ecf9f1;
	color: #1e8449;
}
</style>

<style type="text/css">
	.horizontal-scrollable > .row {
            overflow-x: auto ! important;
            white-space: nowrap ! important;
        }
          
        .horizontal-scrollable > .row > .col-lg-4 {
            display: inline-block ! important;
            float: none ! important;
        }
        /* Decorations */
          
        .col-lg-4 {
            color: white ! important;
            font-size: 24px ! important;
            padding-bottom: 20px ! important;
            padding-top: 18px ! important;
        }
          
        .col-lg-4:nth-child(2n+1) {
            background: green ! important;
        }
          
        .col-lg-4:nth-child(2n+2) {
            background: black ! important;
        }
</style>
<!-- <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" /> -->
<link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<!-- BEGIN PAGE CONTAINER -->
<div class="page-container">
	<div class="page-head">
		<div class="container">
			<!-- BEGIN PAGE TITLE -->
			<div class="page-title">
				<h1><?php echo $is_cp_dashboard ? 'Channel Partner Dashboard' : 'Customer Dashboard'; ?></h1>				
			</div>
			<!-- <div class="page-title pull-right">
				<a onclick="sendMailReports()" class="btn btn-success btn-circle"><i class="fa fa-paper-plane"></i> EMAIL DATABASE<div></div></a>
			</div> -->
		</div>
	</div>
	<!-- BEGIN PAGE CONTENT -->
	<div class="page-content">
		<div class="container">
			<?php 
			if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="1"){
			?>			
			<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Success! </b>Account details has been updated.
			</div>
			<?php
			}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="2"){
			?>
			<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Error! </b>There is an error in admin account updation process. Please try again.
			</div>
			<div class="row">
			<?php
			}
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="3"){
			?>
			<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
				<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
				<b>Error! </b>Current Password No Match.Please try again.
			</div>
			<?php
			}
			?>

			<?php
				
				include("../include/dashboard_var.php");
					
			?>

			<?php if ($is_cp_dashboard) { ?>
			<div class="cp-dash-hero">
				<h2>Welcome to Channel Partner Dashboard</h2>
				<p>Customer orders, stock, payments ane ledger na main shortcuts ahiya thi fast access ma malse.</p>
				<div class="cp-dash-actions">
					<a href="channel_partner_customer_manage.php" class="btn btn-default"><i class="fa fa-users"></i> My Customers</a>
					<a href="channel_partner_order_manage.php" class="btn btn-default"><i class="fa fa-shopping-cart"></i> Customer Orders</a>
					<a href="channel_partner_stock_manage.php" class="btn btn-default"><i class="fa fa-cubes"></i> My Stock</a>
					<a href="channel_partner_payment.php" class="btn btn-default"><i class="fa fa-money"></i> Receive Payment</a>
					<a href="channel_partner_ledger.php" class="btn btn-default"><i class="fa fa-book"></i> Customer Ledger</a>
				</div>
			</div>
			<div class="row">
				<?php foreach ($cp_dashboard_cards as $card) { ?>
				<div class="col-md-3 col-sm-6">
					<div class="cp-summary-card">
						<div class="icon"><i class="fa <?php echo $card['icon']; ?>"></i></div>
						<div class="value"><?php echo number_format((int) $card['value']); ?></div>
						<span class="label"><?php echo htmlspecialchars($card['label']); ?></span>
						<a href="<?php echo $card['link']; ?>">Open <i class="fa fa-arrow-right"></i></a>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="row">
				<div class="col-md-7">
					<div class="cp-section-card">
						<div class="cp-section-head">
							<h4>Recent Customer Orders</h4>
							<p>Latest CP customer orders with amount and current status.</p>
						</div>
						<div class="cp-section-body">
							<table class="table table-striped cp-mini-table">
								<thead>
									<tr>
										<th>Order No</th>
										<th>Party</th>
										<th>Date</th>
										<th class="text-right">Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($cp_recent_orders)) { foreach ($cp_recent_orders as $row) { ?>
									<tr>
										<td><a href="channel_partner_order_print.php?order_id=<?php echo (int) $row['id']; ?>"><?php echo htmlspecialchars($row['order_no']); ?></a></td>
										<td><?php echo htmlspecialchars($row['party_name'] ? $row['party_name'] : '-'); ?></td>
										<td><?php echo date('d-m-Y', strtotime($row['order_date'])); ?></td>
										<td class="text-right"><?php echo number_format((float) $row['grand_total'], 2); ?></td>
									</tr>
									<?php } } else { ?>
									<tr><td colspan="4" class="text-center">No customer orders yet.</td></tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="cp-section-card">
						<div class="cp-section-head">
							<h4>Payment Follow-up</h4>
							<p>Pending and latest payment status for customer orders.</p>
						</div>
						<div class="cp-section-body">
							<table class="table table-striped cp-mini-table">
								<thead>
									<tr>
										<th>Party</th>
										<th>Order</th>
										<th>Status</th>
										<th class="text-right">Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($cp_pending_orders)) { foreach ($cp_pending_orders as $row) { ?>
									<tr>
										<td><?php echo htmlspecialchars($row['party_name'] ? $row['party_name'] : '-'); ?></td>
										<td><?php echo htmlspecialchars($row['order_no']); ?></td>
										<td><?php if ((int) $row['payment_received_flag'] === 1) { ?><span class="cp-badge received">Received</span><?php } else { ?><span class="cp-badge pending">Pending</span><?php } ?></td>
										<td class="text-right"><?php echo number_format((float) $row['grand_total'], 2); ?></td>
									</tr>
									<?php } } else { ?>
									<tr><td colspan="4" class="text-center">No pending payments.</td></tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

	<?php if (!$is_cp_dashboard) { ?>
	<div class="row">
		<div class="col-md-12 col-sm-12" >
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption caption-md">
						<i class="icon-bar-chart font-dark hide"></i>
						<span class="caption-subject font-dark bold uppercase"> Search</span>
					</div>
					<div class="col-md-3" id="todate_div" >
								<label>To Date</label>
								<input type="text" name="todate_all" id="todate_all" value="<?= $_REQUEST['todate'] ?>" class="form-control b-3 datepicker" placeholder="dd-mm-yyyy" readonly>
					</div>
					<div class="col-md-3" id="todate_div">
								<label>From Date</label>
								<input type="text" name="fromdate_all" id="fromdate_all" value="<?= $_REQUEST['fromdate'] ?>" class="form-control b-3 datepicker" placeholder="dd-mm-yyyy" readonly>
					</div>
					<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id = $_REQUEST['quotation_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<label>Sales Person</label>
							<select class="form-control all_sales_executive" name="all_sales_executive" id="all_sales_executive" value="<?php echo $sales_id;?>" <?php echo $disabled; ?>>
								<option value="">Select Person</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1","name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
									?>
									<option <?php if($sales_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
									<?php
								}
								?>
							</select>
					</div>
						<span style="float: right;">
							<a href="javascript:;" onclick="getalldata(document.getElementById('todate_all').value,document.getElementById('fromdate_all').value,document.getElementById('all_sales_executive').value);"   class="btn btn-circle red-sunglo ">
							<i class="fa fa-refresh"></i>Refresh </a>
						</span>
				</div>
			</div>
		</div>	
	</div>
	<?php } ?>
			
			<div class="row">
					
				<!-- <div class="col-md-12 col-sm-12 co-xs-12 col-lg-12"> -->
					<?php if (!$is_cp_dashboard) { include("customer_data_tiles.php"); } ?>
				<!-- </div> -->
			</div>
			
 		<!-- 	<div class="row">
				<?php include("moving_product.php"); ?>
			</div> -->
			<div class="row">
				<!-- <?php
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] !=0)
				{
					?>
					<div class="col-md-6 col-sm-12 co-xs-12 col-lg-5">
					<?php
					$dash = 0;
					?>
					<?php
					foreach($dashboard_main_array as $arr){
						if($db->checkUserPermission($arr[4],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
							$type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
							if($type==0 && array_key_exists(5,$arr))
							{
								continue;
							}
						$dash++;
					?>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="dashboard-stat <?php echo $arr[0]; ?>">
							<a href="<?php echo $arr[3]; ?>">
							 <div class="visual"></div>
							 <div class="details">          
							  <div class="desc">
							   <h4><?php echo $arr[2]; ?></h4>
							   <div class="number" style="font-size:25px;padding-top: 0px;">
				                    <span data-counter="counterup" data-value="<?php echo $arr[1]; ?>"><?php echo $arr[1]; ?></span>
				                </div>
							  </div>
							 </div>
							  
							</a>
							</div>
						</div>
						
	                <?php
						
						}
					}	
					?> -->
					<!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="dashboard-stat <?php echo $arr[0]; ?>">
							<a href="attendance_manage.php">
							<div class="visual"></div>
							 	<div class="details">          
							  		<div class="desc">
							   		<h4>Today's Attendance</h4>
							   		<div class="number" style="font-size:25px;padding-top: 0px;">
				                    	<span data-counter="counterup" data-value="<?php echo $arr[1]; ?>"><?php echo $today_attandance; ?></span>
				                	</div>
							  		</div>
							 	</div>
							  	
							</a>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="dashboard-stat <?php echo $arr[0]; ?>">
							<a href="expense_manage.php">
							<div class="visual"></div>
							 	<div class="details">          
							  		<div class="desc">
							   		<h4>Today's Expense</h4>
							   		<div class="number" style="font-size:25px;padding-top: 0px;">
				                    	<span data-counter="counterup" data-value="<?php echo $arr[1]; ?>"><?php echo $today_expense; ?></span>
				                	</div>
							  		</div>
							 	</div>
							  	
							</a>
						</div>
					</div> -->
					<!-- <?php
					$type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
					if($type==0)
					{ ?> -->
					<!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="dashboard-stat <?php echo $arr[0]; ?>">
							<a href="">
							<div class="visual"></div>
							 	<div class="details">          
							  		<div class="desc">
							   		<h4>SMS</h4>
							   		<div class="number <?php echo ($remainingPromotionalPer>70)?"font-red":""?>" style="font-size:25px;padding-top: 0px;">
				                    	<span data-counter="counterup" data-value="<?php echo $remainingPromotional; ?>"><?php echo $remainingPromotional; ?></span>
				                	</div>
							  		</div>
							 	</div>
							  	
							</a>
						</div>
					</div> -->
					<!-- <?php } ?> -->
					<!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
	                    <div class="dashboard-stat2 ">	                    	
	                        <div class="display">
	                            <div class="number">
	                                <h3 class="<?php echo ($remainingPromotionalPer>70)?"font-red":"font-green-sharp"?>">
	                                    <span data-counter="counterup" data-value="<?php echo $remainingPromotional;?>"><?php echo $remainingPromotional;?></span>
	                                </h3>
	                                <small>PROMOTIONAL SMS</small>
	                            </div>
	                        </div>
	                    </div>
	                </div> -->
	            <!-- </div> -->
				<!-- <div class="col-md-6 col-sm-12 co-xs-12 col-lg-7">
					<?php include("graph.php"); ?>
				</div>   -->
				<!-- <?php
				}
				?>  -->
			</div>
			<div class="row">
				<?php 
				// if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] !=0)
				// {
					?>
					<!-- <div class="col-md-12 col-sm-12 co-xs-12 col-lg-12"> -->
					<?php 
					// include("pending_data_ajax.php");
					 ?>
					<!-- </div> -->

					<?php
				// }
				?>
				
			</div>
			<div class="row">

				<?php 
				// if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] !=0)
				// {
					?>
				<!-- <div class="col-md-12 col-sm-12 co-xs-12 col-lg-12">
					<?php include("sales_vs_customer_count.php"); ?>
				</div> -->

				<?php
				// }
				?>
			</div> 
		</div>	
	</div>
	<!-- END PAGE CONTENT -->
</div>
</div>
 <div id="loading-modal" data-backdrop="static" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h1>
                  <img src="../images/loading.gif" height="250px" width="250px" class="img-responsive center-block">
                  <p>Loading Data...</p>
                </h1>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END PAGE CONTAINER -->
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>


 <script type="text/javascript">
// $( "#prospect-data" ).load("prospect_data_get_ajax.php");
// $( "#inquiry-data" ).load("inquiry_data_get_ajax.php");
// // $( "#lead-data" ).load("lead_data_get_ajax.php");
// // $( "#quotation-data" ).load("quotation_data_get_ajax.php");
// $( "#order-data" ).load("order_data_get_ajax.php");
// // $( "#dispatch-data" ).load("dispatch_data_get_ajax.php");
// // $( "#invoice-data" ).load("invoice_data_get_ajax.php");
// // $( "#complain-data" ).load("complain_data_get_ajax.php");
// // $( "#visit-data" ).load("visit_data_get_ajax.php");
// // $( "#expense-data" ).load("expense_data_get_ajax.php");
// // $( "#leave-data" ).load("leave_data_get_ajax.php");
// // $( "#followup-data" ).load("followup_data_get_ajax.php");
// $( "#attendance-data" ).load("attendance_data_get_ajax.php");
// $( "#sales_person_target" ).load("sales_person_target.php");
// $( "#outstanding_data" ).load("outstanding_data_get_ajax.php");
// $( "#deep_freezer" ).load("deep_freezer_data_get_ajax.php");
</script> 

<!-- <script type="text/javascript">
	$(document).ready(function() {
    var reqheight = $(window).width() - 35;
    $("").css("width", reqheight - 120);//340
    
});
</script> -->

<script src="js/graph.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/sales_order_graph_chart.js"></script>
<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
<script type="text/javascript" src="js/amcharts/serial.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/core.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/charts.js"></script>
<script type="text/javascript" src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="<?= ADMINSITEURL;?>assets/global/plugins/countdown/jquery.countdown.min.js"></script>
<!-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> -->
<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<!-- <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script> -->
	
    <!-- <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDklPuT2SCmcmlflaoZ4B0WywYK_em79x4&callback=initMap">
    </script> -->
<script>

	$(".all_sales_executive").select2();
		var map;
		var markerCluster;
		var markers=[]
      	function initMap() {
		if(locations.length>0)
		{

			var location_count = locations.length;
			var j = 0;
			var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 19,
				center: {lat:locations[0].lat, lng: locations[0].lng},
			});
			var infowindow = new google.maps.InfoWindow();
			$.each(locations,function(i,v){
				
				var marker = new google.maps.Marker({
				position: {lat:locations[i].lat, lng: locations[i].lng},
				map: map,
			    icon: '<?php echo SITEURL; ?>/images/marker/green.png',
				title: locations[i]['type'],
				
				});


				/*var marker = new google.maps.Marker({
					position: {lat:locations[i].lat, lng: locations[i].lng},
					map: map,
				    icon: '<?php echo SITEURL; ?>/images/marker/'+locations[i]['icon'],
					title: locations[i]['type']
				});*/
				google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
				return function() {
					if(locations[i]['status']=="offline")
					{
						var color = "red";
					}
					else if(locations[i]['status']=="online")
					{
						var color = "green";
					}
				  infowindow.setContent("<h1>"+(i+1)+") "+locations[i]['date']+"</h1><p>Lat:"+locations[i]['lat']+"<br/> Long:"+locations[i]['lng']+" "+locations[i]['type']+"<br/><h4><b>Name:"+locations[i]['name']+"<br/><a style='color:"+color+"'>"+locations[i]['status']+"</a></b></h4></p>");
				  infowindow.open(map, marker);
				}
			  })(marker, i));
			})
			  var flightPath = new google.maps.Polyline({
				  //path: locations,
				  geodesic: true,
				  strokeColor: '#FF0000',
				  strokeOpacity: 1.0,
				  strokeWeight: 2
				});
				flightPath.setMap(map);
				// map.drawRoute({
				//   origin: location,
				//   destination: location,
				//   travelMode: 'driving',
				//   strokeColor: '#131540',
				//   strokeOpacity: 0.6,
				//   strokeWeight: 6
				// });
				// var circle = new google.maps.Circle({
				//   map: map,
				//   radius: 16093,    // 10 miles in metres
				//   fillColor: '#AA0000'
				// });
				// circle.bindTo('center', marker, 'position');
				for (var i = 0; i < location_count; i++) {
				 var cityCircle = new google.maps.Circle({
		            strokeColor: '#518FFB',
		            strokeOpacity: 0.8,
		            strokeWeight: 2,
		            fillColor: '#518FFB',
		            fillOpacity: 0.35,
		            map: map,
		            center: {lat:locations[i].lat, lng: locations[i].lng},
		            radius: 10
		          });
				}
		}
        else
		{
			var mapOptions = {
			    center: {'lat': 22.2939994, 'lng': 70.7892855},
			    zoom: 14
		 	};

		  	// Map object
		 	map = new google.maps.Map(document.getElementById('map'), mapOptions);
		}
      }
	  
     function clearMarker(){
		 locations=[];
		
		  for (var i = 0; i < markers.length; i++) {							  
			markers[i].setMap(map);			
		  }
		  if(markerCluster)
			markerCluster.clearMarkers();
	 }
    </script>
    <script type="text/javascript">
var locations=[];
var labels=[];
var status="";
var waypts = [];
var result= <?php echo json_encode($response); ?>;
var searchName="";
var data_url = "sales_executive_ajax_function.php";
var sid = <?php echo ($id!="")?$id:0; ?>

function searchByName(){
	searchName = $("#searchName").val();
	// displayRecordsmap();
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	// displayRecordsmap();
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadMap(){
	
	alert("Map Loading Please Wait");
}
function displayRecordsmap() {
	var date=$("#date").val();
	if(result.ack==1)
	{
		//clearMarker();
		var locs=result.result
		$.each(locs,function(i,v){
			locations.push({date:v.date,name:v.name,lat: parseFloat(v.lat), lng:  parseFloat(v.lng),type_slug:v.type_slug,type:v.type,icon:v.icon,status:v.status});
			labels.push(v.date);
		});
		initMap();
		toastr.success(result.ack_msg,"success");
	}
	else
	{
		clearMarker();
		toastr.error(result.ack_msg,"Sorry");
	}
}
$(document).ready(function() {
	
    size_li = $("#myList li").size();
    all=x=3;
    $('#myList li:lt('+x+')').show();
    $('#showLess').hide();
    $('#loadMore').click(function () {
        x= (x+5 <= size_li) ? x+5 : size_li;
        $('#myList li:lt('+x+')').show();
        $('#showLess').show();
        if(x >= size_li)
        {
        	$('#loadMore').hide();
        }
    });
    $('#showLess').click(function () {
        x=(x-5<0) ? 3 : x-5;
        if(x <= all)
        {
        	x=all;
        	$('#showLess').hide();
        }
        $('#myList li').not(':lt('+x+')').hide();
        $('#loadMore').show();
    });

	$('#panel').addClass("floating panel");
	$('#panel').css("display","none");
	$('#toggle').show();
	// displayRecordsmap();
	
});

$('#ToDate').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });

function getByDate(flag)
{
	id=$("#exicutive_id").val();
	date=$("#ToDate").val();
	
	if(flag == 'route')
	{
		if(id != "")
		{
			var link = 'maproute.php?id='+id+'&date='+date;
			window.open(link,'_blank');
		}
		else
		{
			toastr.error("please select exicutive");
		}
	}
	if(flag == 'punch')
	{
		window.location = 'dashboard.php?id='+id+'&date='+date;
	}
	
}
</script>
<script>
//FOR NOTIFICATION
$(function(){
	toastr.options = {
    timeOut: 0,
    extendedTimeOut: 0,
    tapToDismiss: false,
	}
	aj.getNotifications("ul.feeds");
	$('.scroller').each(function() {
	if ($(this).attr("data-initialized")) {
		return; // exit
	}

	var height;

	if ($(this).attr("data-height")) {
		height = $(this).attr("data-height");
	} else {
		height = $(this).css('height');
	}

	$(this).slimScroll({
		allowPageScroll: true, // allow page scroll when the element scroll is ended
		size: '7px',
		color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
		wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
		railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
		position: isRTL ? 'left' : 'right',
		height: height,
		alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
		railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
		disableFadeOut: true
	});

	$(this).attr("data-initialized", "1");
});
});

$('#toggle').click(function(e) {
	if($('#panel').css("display") != 'none')
	{
		$('#toggle').html("<img src='<?=SITEURL.'images/on.png'?>' width='20px;'>");
		$('#panel').hide();
	}
	else
	{
		$('#toggle').html("<img src='<?=SITEURL.'images/off.png'?>' width='20px;'>");
		$('#panel').show();
	}
});
</script>
<script type="text/javascript">
	function loadCustomerType(type) 
    {
        $.ajax({
            type:'POST',
            url:'customer_type_data_get_ajax.php',
            data:
            {
                type:type,
            },
            beforeSend:function () {
            	$("#loading-modal").modal('show');
            },
            success: function(data) {
            	$("#CustomerType").html(data);
            	$("#loading-modal").modal('hide');
        	}
        });
    }

    function filter_state(state_id, city = "") {
		
		$.ajax({
			type: "POST",
			url: "find_city.php",
			data: 'state_id=' + state_id + "&city=" + city,
			beforeSend: function() {
				 $("#loading-modal").modal('show');
				// $('.preloader').fadeIn('slow');
			},
			success: function(data) {
				$("#city").select2("destroy");
				$("#city").html(data);
				$("#city").select2();
				$("#loading-modal").modal('hide');
				// $('.preloader').fadeOut('slow');
			}
		});
	}

    function sendMailReports()
    {

        $.ajax({
            url:"local_email/local_mail_system_sql.php",
             //data: 'id='+id+'&fileUrl='+fileUrl,
            beforeSend:function(){
               $("#loading-modal").modal("show");
            },
            success:function(result)
            {
            	$("#loading-modal").modal("hide");
            	toastr.success("Mail Sent Successfully");
                
            },
            error:function(error)
            {
                $("#loading-modal").modal("hide");
                toastr.error("Mail Send Failed");
            }
        });
    }


//   $(document).ready(function(){
// $.ajax({ url: "quotation_tiles_ajax.php",
//         context: document.body,
//         success: function(){
//            alert("done");
//         }});
// });

function getcustomerData()
 		{
 			var customer_type = $(".customerType").find("span").html();

 			if (customer_type == 'Distributor') 
 			{
 				var type = '2';
 			}
 			else if (customer_type == 'Dealer')
 			{
 				var type = '3';
 			}
 			else
 			{
 				var type = "";
 			}

 			// if (customer_type == 'Dealer') 
 			// {
 			// 	var type = '3';
 			// }
 			// alert(type);
			var customer_id=$("#customer_id").val();
			var city=$("#city").val();
			var state=$("#state").val();
			
			$.ajax({
				type: "POST",
				url: "customer_type_data_get_ajax.php",
				data: 'customer_id=' + customer_id + '&state=' + state + '&city=' + city + '&type=' + type,

				beforeSend: function() {
					$("#loading-modal").modal('show');  
				},
				success: function(data) {
					$("#CustomerType").html(data);
					$("#loading-modal").modal('hide');
				}
			});
		}

function getquotation(todate_all="",fromdate_all="",all_sales_executive="") 
{
	if(all_sales_executive!= "")
	{
		var quotation_sales_id=all_sales_executive;

	}
	else
	{
		var quotation_sales_id=$("#quotation_sales_id").val();
	}


	if(todate_all != "")
	{
				// alert(todate_all);
				var quotation_todate=todate_all;
            	var quotation_fromdate=fromdate_all;
				
	} 
	else
	{
			var quotation_todate=$('#quotation_todate').val();
            var quotation_fromdate=$('#quotation_fromdate').val();
	}

	var quotation_year=$("#quotation_year").val();
	var quotation_month=$("#quotation_month").val();
	var quotation_customer_id=$("#quotation_customer_id").val();
	// var quotation_sales_id=$("#quotation_sales_id").val();
	// var quotation_todate=$('#quotation_todate').val();
 //    var quotation_fromdate=$('#quotation_fromdate').val();
	
	$.ajax({
		type: "POST",
		url: "quotation_data_get_ajax.php",
		data: 'quotation_year=' + quotation_year +'&quotation_month=' + quotation_month + '&quotation_customer_id=' + quotation_customer_id +'&quotation_sales_id=' + quotation_sales_id+'&quotation_todate=' + quotation_todate+'&quotation_fromdate=' + quotation_fromdate,
		beforeSend: function() {
		},
		success: function(data) 
		{
			$("#quotation-data").html(data);
			if (quotation_month == '' && quotation_todate=='' && quotation_fromdate =='') 
			{
				Graph1.init1();
			}
			else
			{	
				// alert(quotation_month);
				// alert("test");
				Graph2.init2();
			}
		}
	});
}

function getorder(todate_all="",fromdate_all="",all_sales_executive="")
 {
 			if(all_sales_executive!= "")
			{
				var order_sales_id=all_sales_executive;

			}
			else
			{
				var order_sales_id=$("#order_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var order_todate=todate_all;
            	var order_fromdate=fromdate_all;
				
			} 
			else
			{
			var order_todate=$('#order_todate').val();
            var order_fromdate=$('#order_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var order_year=$("#order_year").val();
			var order_month=$("#order_month").val();
			var order_customer_id=$("#order_customer_id").val();
			// var order_sales_id=$("#order_sales_id").val();
			// var order_todate=$('#order_todate').val();
   //  		var order_fromdate=$('#order_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "order_data_get_ajax.php",
				data: 'order_year=' + order_year +'&order_month=' + order_month + '&order_customer_id=' + order_customer_id +'&order_sales_id=' + order_sales_id+'&order_todate=' + order_todate+'&order_fromdate=' + order_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#order-data").html(data);
					
					if (order_month == '' && order_todate=='' && order_fromdate =='') 
						{
							graph_order_pie.init_order_pie();
						}
						else
						{
		    				Graph3.init3();
							
						}
		    			
		
					
				}
			});
		}

function getdispatch(todate_all="",fromdate_all="",all_sales_executive="")
 {				
 			if(all_sales_executive!= "")
			{
				var dispatch_sales_id=all_sales_executive;

			}
			else
			{
				var dispatch_sales_id=$("#dispatch_sales_id").val();
			}		


			if(todate_all != "")
			{
				// alert(todate_all);
				var dispatch_todate=todate_all;
            	var dispatch_fromdate=fromdate_all;
				
			} 
			else
			{
			var dispatch_todate=$('#dispatch_todate').val();
            var dispatch_fromdate=$('#dispatch_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var dispatch_year=$("#dispatch_year").val();
			var dispatch_month=$("#dispatch_month").val();
			var dispatch_customer_id=$("#dispatch_customer_id").val();
			// var dispatch_sales_id=$("#dispatch_sales_id").val();
			// var dispatch_todate=$('#dispatch_todate').val();
   //  		var dispatch_fromdate=$('#dispatch_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "dispatch_data_get_ajax.php",
				data: 'dispatch_year=' + dispatch_year +'&dispatch_month=' + dispatch_month + '&dispatch_customer_id=' + dispatch_customer_id +'&dispatch_sales_id=' + dispatch_sales_id+'&dispatch_todate=' + dispatch_todate+'&dispatch_fromdate=' + dispatch_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#dispatch-data").html(data);
						if (dispatch_month == '' && dispatch_todate=='' && dispatch_fromdate =='') 
						{
							graph_dispatch_pie.init_dispatch_pie();
						}
						else
						{
		    				
		    				Graph_dispatch.init_dispatch();
							
						}


		
					
				}
			});
		}


function getinvoice(todate_all="",fromdate_all="",all_sales_executive="")
 { 			if(all_sales_executive!= "")
			{
				var invoice_sales_id=all_sales_executive;

			}
			else
			{
				var invoice_sales_id=$("#invoice_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var invoice_todate=todate_all;
            	var invoice_fromdate=fromdate_all;
				
			} 
			else
			{
			var invoice_todate=$('#invoice_todate').val();
            var invoice_fromdate=$('#invoice_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var invoice_year=$("#invoice_year").val();
			var invoice_month=$("#invoice_month").val();
			var invoice_customer_id=$("#invoice_customer_id").val();
			// var invoice_sales_id=$("#invoice_sales_id").val();
			// var invoice_todate=$('#invoice_todate').val();
   //  		var invoice_fromdate=$('#invoice_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "invoice_data_get_ajax.php",
				data: 'invoice_year=' + invoice_year +'&invoice_month=' + invoice_month + '&invoice_customer_id=' + invoice_customer_id +'&invoice_sales_id=' + invoice_sales_id +'&invoice_todate=' + invoice_todate+'&invoice_fromdate=' + invoice_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#invoice-data").html(data);
						if (invoice_month == '' && invoice_todate=='' && invoice_fromdate =='') 
						{
							graph_invoice_pie.init_invoice_pie();
						}
						else
						{
		    				Graph_invoice.init_invoice();
							
						}


		    			
		
					
				}
			});
		}


		function getcomplain(todate_all="",fromdate_all="",all_sales_executive="")
		{
			if(all_sales_executive!= "")
			{
				var complain_sales_id=all_sales_executive;

			}
			else
			{
				var complain_sales_id=$("#complain_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var complain_todate=todate_all;
            	var complain_fromdate=fromdate_all;
				
			} 
			else
			{
			var complain_todate=$('#complain_todate').val();
            var complain_fromdate=$('#complain_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var complain_year=$("#complain_year").val();
			var complain_month=$("#complain_month").val();
			var complain_customer_id=$("#complain_customer_id").val();
			// var complain_sales_id=$("#complain_sales_id").val();
			// var complain_todate=$('#complain_todate').val();
   //  		var complain_fromdate=$('#complain_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "complain_data_get_ajax.php",
				data: 'complain_year=' + complain_year +'&complain_month=' + complain_month + '&complain_customer_id=' + complain_customer_id +'&complain_sales_id=' + complain_sales_id+'&complain_todate=' + complain_todate+'&complain_fromdate=' + complain_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#complain-data").html(data);
						
						if (complain_month == '' && complain_todate=='' && complain_fromdate =='') 
						{
							graph_complain_pie.init_complain_pie();
						}
						else
						{
		    				
		    			
		    			Graph_complain.init_complain();
							
						}
		    			
		
					
				}
			});
		}


		function getleave(todate_all="",fromdate_all="",all_sales_executive="")
		{
			if(all_sales_executive!= "")
			{
				var leave_sales_id=all_sales_executive;

			}
			else
			{
				var leave_sales_id=$("#leave_sales_id").val();
			}
			if(todate_all != "")
			{
				// alert(todate_all);
				var leave_todate=todate_all;
            	var leave_fromdate=fromdate_all;
				
			} 
			else
			{
			var leave_todate=$('#leave_todate').val();
            var leave_fromdate=$('#leave_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var leave_year=$("#leave_year").val();
			var leave_month=$("#leave_month").val();
			// var leave_customer_id=$("#leave_customer_id").val();
			// var leave_sales_id=$("#leave_sales_id").val();
			// var leave_todate=$('#leave_todate').val();
   //  		var leave_fromdate=$('#leave_fromdate').val();


			
			$.ajax({
				type: "POST",
				url: "leave_data_get_ajax.php",
				data: 'leave_year=' + leave_year +'&leave_month=' + leave_month  +'&leave_sales_id=' + leave_sales_id+'&leave_todate=' + leave_todate+'&leave_fromdate=' + leave_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#leave-data").html(data);
						
						if (leave_month == '' && leave_todate=='' && leave_fromdate =='') 
						{
							graph_leave_pie.init_leave_pie();
						}
						else
						{
		    				
		    				Graph_leave.init_leave();
							
						}
		    			
		
					
				}
			});
		}


		function getprospect(todate_all="",fromdate_all="",all_sales_executive="") {
			// alert(todate_all);
			// alert(fromdate_all);
				// customer_type = $("#customer_type").val();

			if(all_sales_executive!= "")
			{
				var prospect_inquiry_created_by=all_sales_executive;

			}
			else
			{
				var prospect_inquiry_created_by=$("#prospect_inquiry_created_by").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var todate=todate_all;
            	var fromdate=fromdate_all;
				
			} 
			else
			{
			var todate=$('#todate').val();
            var fromdate=$('#fromdate').val();
			}
			// alert(todate);
			// alert(fromdate);

			var prospect_year=$("#prospect_year").val();
			var prospect_month=$("#prospect_month").val();
			// var prospect_inquiry_created_by=$("#prospect_inquiry_created_by").val();
			var prospect_inquiry_assigned_to=$("#prospect_inquiry_assigned_to").val();

			$.ajax({
				type: "POST",
				url: "prospect_data_get_ajax.php",
				data: 'prospect_year=' + prospect_year +'&prospect_month=' + prospect_month +'&prospect_inquiry_created_by=' + prospect_inquiry_created_by +'&prospect_inquiry_assigned_to=' + prospect_inquiry_assigned_to+'&todate=' + todate+'&fromdate=' + fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#prospect-data").html(data);
						if (prospect_month == '' && todate=='' && fromdate =='') 
						{
							// alert(todate);
							graph_prospect_pie.init_prospect_pie();


						}
						else
						{
		    				
		    			Graph_prospect.init_prospect();
							
						}

		
					
				}
			});
		}

		function getinquiry(todate_all="",fromdate_all="",all_sales_executive="")
		 {
		 	if(all_sales_executive!= "")
			{
				var inquiry_inquiry_created_by=all_sales_executive;

			}
			else
			{
				var inquiry_inquiry_created_by=$("#inquiry_inquiry_created_by").val();
			}


				// alert(todate_all);
			if(todate_all != "")
			{
				
				var inquiry_todate=todate_all;
            	var inquiry_fromdate=fromdate_all;
				
			} 
			else
			{
			var inquiry_todate=$('#inquiry_todate').val();
            var inquiry_fromdate=$('#inquiry_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var inquiry_year=$("#inquiry_year").val();
			var inquiry_month=$("#inquiry_month").val();
			// var inquiry_inquiry_created_by=$("#inquiry_inquiry_created_by").val();
			var inquiry_inquiry_assigned_to=$("#inquiry_inquiry_assigned_to").val();
			// var inquiry_todate=$('#inquiry_todate').val();
   //          var inquiry_fromdate=$('#inquiry_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "inquiry_data_get_ajax.php",
				data: 'inquiry_year=' + inquiry_year +'&inquiry_month=' + inquiry_month+'&inquiry_inquiry_created_by=' + inquiry_inquiry_created_by +'&inquiry_inquiry_assigned_to=' + inquiry_inquiry_assigned_to+'&inquiry_todate=' + inquiry_todate+'&inquiry_fromdate=' + inquiry_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#inquiry-data").html(data);
						if (inquiry_month == '' && inquiry_todate=='' && inquiry_fromdate =='') 
						{
							graph_inquiry_pie.init_inquiry_pie();
						}
						else
						{
							Graph_inquiry.init_inquiry();
						}
				}
			});
		}


		function getlead(todate_all="",fromdate_all="",all_sales_executive="")
		 {
		 	if(all_sales_executive!= "")
			{
				var lead_inquiry_created_by=all_sales_executive;

			}
			else
			{
				var lead_inquiry_created_by=$("#lead_inquiry_created_by").val();
			}


			if(todate_all != "")
			{
				// alert(todate_all);
				var lead_todate=todate_all;
            	var lead_fromdate=fromdate_all;
				
			} 
			else
			{
			var lead_todate=$('#lead_todate').val();
            var lead_fromdate=$('#lead_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var lead_year=$("#lead_year").val();
			var lead_month=$("#lead_month").val();
			// var lead_inquiry_created_by=$("#lead_inquiry_created_by").val();
			var lead_inquiry_assigned_to=$("#lead_inquiry_assigned_to").val();
			// var lead_todate=$('#lead_todate').val();
   //          var lead_fromdate=$('#lead_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "lead_data_get_ajax.php",
				data: 'lead_year=' + lead_year +'&lead_month=' + lead_month+'&lead_inquiry_created_by=' + lead_inquiry_created_by +'&lead_inquiry_assigned_to=' + lead_inquiry_assigned_to+'&lead_todate=' + lead_todate+'&lead_fromdate=' + lead_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#lead-data").html(data);
						
						if (lead_month == '' && lead_todate=='' && lead_fromdate =='') 
						{
							graph_lead_pie.init_lead_pie();
						}
						else
						{
		    				
		    			
		    			Graph_lead.init_lead();
							
						}
		    			
		
					
				}
			});
		}


		function getvisit(todate_all="",fromdate_all="",all_sales_executive="")
		{
			if(all_sales_executive!= "")
			{
				var visit_sales_id=all_sales_executive;

			}
			else
			{
				var visit_sales_id=$("#visit_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var visit_todate=todate_all;
            	var visit_fromdate=fromdate_all;
				
			} 
			else
			{
			var visit_todate=$('#visit_todate').val();
            var visit_fromdate=$('#visit_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var visit_year=$("#visit_year").val();
			var visit_month=$("#visit_month").val();
			var visit_customer_id=$("#visit_customer_id").val();
			// var visit_sales_id=$("#visit_sales_id").val();
			// var visit_todate=$('#visit_todate').val();
   //  		var visit_fromdate=$('#visit_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "visit_data_get_ajax.php",
				data: 'visit_year=' + visit_year +'&visit_month=' + visit_month + '&visit_customer_id=' + visit_customer_id +'&visit_sales_id=' + visit_sales_id+'&visit_todate=' + visit_todate+'&visit_fromdate=' + visit_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#visit-data").html(data);
						if (visit_month == '' && visit_todate=='' && visit_fromdate =='') 
						{
							graph_visit_pie.init_visit_pie();
						}
						else
						{
		    				
		    			
		    			Graph_visit.init_visit();
							
						}
		    			
		
					
				}
			});
		}


		function getexpense(todate_all="",fromdate_all="",all_sales_executive="")
		{

			if(all_sales_executive!= "")
			{
				var expense_sales_id=all_sales_executive;

			}
			else
			{
				var expense_sales_id=$("#expense_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var expense_todate=todate_all;
            	var expense_fromdate=fromdate_all;
				
			} 
			else
			{
			var expense_todate=$('#expense_todate').val();
            var expense_fromdate=$('#expense_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var expense_year=$("#expense_year").val();
			var expense_month=$("#expense_month").val();
			var expense_customer_id=$("#expense_customer_id").val();
			// var expense_sales_id=$("#expense_sales_id").val();
			// var expense_todate=$('#expense_todate').val();
   //  		var expense_fromdate=$('#expense_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "expense_data_get_ajax.php",
				data: 'expense_year=' + expense_year +'&expense_month=' + expense_month + '&expense_customer_id=' + expense_customer_id +'&expense_sales_id=' + expense_sales_id+'&expense_todate=' + expense_todate+'&expense_fromdate=' + expense_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#expense-data").html(data);
						if (expense_month == '' && expense_todate=='' && expense_fromdate =='') 
						{
							graph_expense_pie.init_expense_pie();
						}
						else
						{
		    				
		    			
		    			Graph_expense.init_expense();
							
						}
				}
			});
		}


		function getfollowup(todate_all="",fromdate_all="",all_sales_executive="") 
		{
			if(all_sales_executive!= "")
			{
				var followup_sales_id=all_sales_executive;

			}
			else
			{
				var followup_sales_id=$("#followup_sales_id").val();
			}
			if(todate_all != "")
			{
				// alert(todate_all);
				var followup_todate=todate_all;
            	var followup_fromdate=fromdate_all;
				
			} 
			else
			{
			var followup_todate=$('#followup_todate').val();
            var followup_fromdate=$('#followup_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var followup_year=$("#followup_year").val();
			var followup_month=$("#followup_month").val();
			var followup_customer_id=$("#followup_customer_id").val();
			// var followup_sales_id=$("#followup_sales_id").val();
			// var followup_todate=$('#followup_todate').val();
   //  		var followup_fromdate=$('#followup_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "followup_data_get_ajax.php",
				data: 'followup_year=' + followup_year +'&followup_month=' + followup_month + '&followup_customer_id=' + followup_customer_id +'&followup_sales_id=' + followup_sales_id+'&followup_todate=' + followup_todate+'&followup_fromdate=' + followup_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#followup-data").html(data);
						
						if (followup_month == '' && followup_todate=='' && followup_fromdate =='') 
						{
							 graph_followup_pie.init_followup_pie();
						}
						else
						{
		    				
		    			
		    			Graph_followup.init_followup();
							
						}
		    			
		
					
				}
			});
		}
		function getattendance(todate_all="",fromdate_all="",all_sales_executive="")
		{

			if(all_sales_executive!= "")
			{
				var attendance_sales_id=all_sales_executive;

			}
			else
			{
				var attendance_sales_id=$("#attendance_sales_id").val();
			}
			if(todate_all != "")
			{
				// alert(todate_all);
				var attendance_todate=todate_all;
            	var attendance_fromdate=fromdate_all;
				
			} 
			else
			{
			var attendance_todate=$('#attendance_todate').val();
            var attendance_fromdate=$('#attendance_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var attendance_year=$("#attendance_year").val();
			var attendance_month=$("#attendance_month").val();
			// var attendance_customer_id=$("#attendance_customer_id").val();
			// var attendance_sales_id=$("#attendance_sales_id").val();
			// var attendance_todate=$('#attendance_todate').val();
   //  		var attendance_fromdate=$('#attendance_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "attendance_data_get_ajax.php",
				data: 'attendance_year=' + attendance_year +'&attendance_month=' + attendance_month +'&attendance_sales_id=' + attendance_sales_id+'&attendance_todate=' + attendance_todate+'&attendance_fromdate=' + attendance_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#attendance-data").html(data);
						
						if (attendance_month == '' && attendance_todate=='' && attendance_fromdate =='') 
						{
							 graph_attendance_pie.init_attendance_pie();
						}
						else
						{
		    				
		    			
		    			Graph_attendance.init_attendance();
							
						}
		    			
		
					
				}
			});
		}

		function gettarget(todate_all="",fromdate_all="",all_sales_executive="")
 		{
 			if(all_sales_executive!= "")
			{
				var target_sales_id=all_sales_executive;

			}
			else
			{
				var target_sales_id=$("#target_sales_id").val();
			}

			if(todate_all != "")
			{
				// alert(todate_all);
				var order_todate=todate_all;
            	var order_fromdate=fromdate_all;
				
			} 
			else
			{
				// var order_todate=$('#order_todate').val();
            	// var order_fromdate=$('#order_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var target_year=$("#target_year").val();
			var target_month=$("#target_month").val();
			var target_sales_id=$("#target_sales_id").val();
			// alert(target_month + target_year + target_sales_id );
			// var order_sales_id=$("#order_sales_id").val();
			// var order_todate=$('#order_todate').val();
   			//var order_fromdate=$('#order_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "sales_person_target.php",
				data: 'target_year=' + target_year +'&target_month=' + target_month + '&target_sales_id=' + target_sales_id,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#sales_person_target").html(data);
		    		Graph3.init3();
				}
			});
		}

		function getoutstanding(todate_all="",fromdate_all="",all_sales_executive="")
 		{
			var customer_id=$("#customer_id").val();
			// alert(target_month + target_year + target_sales_id );
			// var order_sales_id=$("#order_sales_id").val();
			// var order_todate=$('#order_todate').val();
   			//var order_fromdate=$('#order_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "outstanding_data_get_ajax.php",
				data: 'customer_id=' + customer_id,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#outstanding_data").html(data);
					Graph3.init3();
				}
			});
		}


		// jQuery(document).ready(function() {
		// 	// Graph_prospect.init_prospect();
		// 	graph_prospect_pie.init_prospect_pie();
		// 	// graph_inquiry_pie.init_inquiry_pie();
		// });


		function getalldata(todate_all="",fromdate_all="",all_sales_executive="")
		{


			// let current_datetime = new Date()
			// todate_all = todate_all.getFullYear() + "-" + (todate_all.getMonth() + 1) + "-" + todate_all.getDate();
			// console.log(formatted_date)
			// var todate_all=moment(todate_all).format('YYYY-MM-DD');
			// alert(all_sales_executive);
			// getprospect(todate_all,fromdate_all,all_sales_executive);
			// getinquiry(todate_all,fromdate_all,all_sales_executive);
			// getlead(todate_all,fromdate_all,all_sales_executive);
			// getquotation(todate_all,fromdate_all,all_sales_executive);
			// getorder(todate_all,fromdate_all,all_sales_executive);
			// getdispatch(todate_all,fromdate_all,all_sales_executive);
			// getinvoice(todate_all,fromdate_all,all_sales_executive);
			// getvisit(todate_all,fromdate_all,all_sales_executive);
			// getcomplain(todate_all,fromdate_all,all_sales_executive);
			// getexpense(todate_all,fromdate_all,all_sales_executive);
			// getleave(todate_all,fromdate_all,all_sales_executive);
			// getfollowup(todate_all,fromdate_all,all_sales_executive);
			getattendance(todate_all,fromdate_all,all_sales_executive);

			
			
		}

		function getdeepfreezer(todate_all="",fromdate_all="",all_sales_executive="")
		 {
		 	if(all_sales_executive!= "")
			{
				var inquiry_inquiry_created_by=all_sales_executive;

			}
			else
			{
				var inquiry_inquiry_created_by=$("#inquiry_inquiry_created_by").val();
			}


				// alert(todate_all);
			if(todate_all != "")
			{
				
				var inquiry_todate=todate_all;
            	var inquiry_fromdate=fromdate_all;
				
			} 
			else
			{
			var inquiry_todate=$('#inquiry_todate').val();
            var inquiry_fromdate=$('#inquiry_fromdate').val();
			}
				// customer_type = $("#customer_type").val();
			var inquiry_year=$("#inquiry_year").val();
			var inquiry_month=$("#inquiry_month").val();
			// var inquiry_inquiry_created_by=$("#inquiry_inquiry_created_by").val();
			var inquiry_inquiry_assigned_to=$("#inquiry_inquiry_assigned_to").val();
			// var inquiry_todate=$('#inquiry_todate').val();
   //          var inquiry_fromdate=$('#inquiry_fromdate').val();
			
			$.ajax({
				type: "POST",
				url: "inquiry_data_get_ajax.php",
				data: 'inquiry_year=' + inquiry_year +'&inquiry_month=' + inquiry_month+'&inquiry_inquiry_created_by=' + inquiry_inquiry_created_by +'&inquiry_inquiry_assigned_to=' + inquiry_inquiry_assigned_to+'&inquiry_todate=' + inquiry_todate+'&inquiry_fromdate=' + inquiry_fromdate,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#deep_freezer").html(data);
						if (inquiry_month == '' && inquiry_todate=='' && inquiry_fromdate =='') 
						{
							graph_inquiry_pie.init_inquiry_pie();
						}
						else
						{
							Graph_inquiry.init_inquiry();
						}
				}
			});
		}

		
</script>
<script>
        // $('#todate_all').datepicker({
        //     uiLibrary: 'bootstrap4'
        // });

        var date = new Date();
	date.setDate(date.getDate());

        $('#todate_all').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});

        $('#fromdate_all').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});
    </script>
<script type="text/javascript" src="js/sales_order_graph_dashboard.js"></script>

</body>
</html>