<?php
/**
 * Ajax for BRM customer assign (Super Admin only).
 * Only touches executive.brm_admin_id — never seid / sales / other reports.
 */
$page_id = 678;
$page_slug = 'brm_customer_assign';
include("connect.php");
require_once dirname(__DIR__) . '/include/reorder_brm_helper.php';

$adminType = isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) ? (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] : -1;
if ($adminType !== 0) {
	header('Content-Type: application/json');
	echo json_encode(array('ok' => 0, 'msg' => 'Unauthorized'));
	include("disconnect.php");
	exit;
}

reorder_brm_ensure_customer_assign_column($db);
$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$ids = reorder_brm_login_ids($db);
$b1 = (int) $ids['brm1'];
$b2 = (int) $ids['brm2'];

if ($action === 'stats') {
	header('Content-Type: application/json');
	echo json_encode(array('ok' => 1, 'stats' => reorder_brm_assign_stats($db)));
	include("disconnect.php");
	exit;
}

if ($action === 'assign') {
	header('Content-Type: application/json');
	$cid = isset($_REQUEST['customer_id']) ? (int) $_REQUEST['customer_id'] : 0;
	$bid = isset($_REQUEST['brm_admin_id']) ? (int) $_REQUEST['brm_admin_id'] : 0;
	$ok = reorder_brm_set_customer_admin($db, $cid, $bid);
	if ($ok) {
		$label = ($bid === $b1) ? 'BRM1' : (($bid === $b2) ? 'BRM2' : 'Unassigned');
		echo json_encode(array('ok' => 1, 'msg' => 'Assigned to ' . $label, 'stats' => reorder_brm_assign_stats($db)));
	} else {
		echo json_encode(array('ok' => 0, 'msg' => 'Assign failed'));
	}
	include("disconnect.php");
	exit;
}

if ($action === 'bulk_assign') {
	header('Content-Type: application/json');
	$bid = isset($_REQUEST['brm_admin_id']) ? (int) $_REQUEST['brm_admin_id'] : 0;
	$raw = isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : '';
	if (is_array($raw)) {
		$idsIn = $raw;
	} else {
		$idsIn = preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);
	}
	$done = 0;
	$fail = 0;
	foreach ($idsIn as $cid) {
		$cid = (int) $cid;
		if ($cid <= 0) {
			continue;
		}
		if (reorder_brm_set_customer_admin($db, $cid, $bid)) {
			$done++;
		} else {
			$fail++;
		}
	}
	$label = ($bid === $b1) ? 'BRM1' : (($bid === $b2) ? 'BRM2' : 'Unassigned');
	if ($done > 0) {
		echo json_encode(array(
			'ok' => 1,
			'msg' => $done . ' customer(s) assigned to ' . $label,
			'stats' => reorder_brm_assign_stats($db)
		));
	} else {
		echo json_encode(array('ok' => 0, 'msg' => 'No customers updated'));
	}
	include("disconnect.php");
	exit;
}

/* list HTML — use cname (not person_name; column does not exist) */
$page = isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;
$search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
$filter = isset($_REQUEST['filter']) ? trim($_REQUEST['filter']) : '';

$where = "isDelete=0";
if ($filter === 'brm1' && $b1 > 0) {
	$where .= " AND brm_admin_id='" . $b1 . "'";
} else if ($filter === 'brm2' && $b2 > 0) {
	$where .= " AND brm_admin_id='" . $b2 . "'";
} else if ($filter === 'unassigned') {
	$where .= " AND IFNULL(brm_admin_id,0)=0";
}
if ($search !== '') {
	$s = mysqli_real_escape_string($db->myconn, $search);
	$where .= " AND (
		company_name LIKE '%{$s}%'
		OR cname LIKE '%{$s}%'
		OR mobile_no1 LIKE '%{$s}%'
		OR client_code LIKE '%{$s}%'
	)";
}

$total = (int) $db->rp_getTotalRecord("executive", $where, 0);
$totalPages = ($perPage > 0) ? (int) ceil($total / $perPage) : 1;
$r = $db->rp_getData(
	"executive",
	"id, company_name, cname, mobile_no1, client_code, seid, brm_admin_id",
	$where,
	"id DESC",
	0,
	$offset . ", " . $perPage
);

$rowCount = ($r && is_object($r)) ? (int) mysqli_num_rows($r) : 0;
?>
<input type="hidden" id="brm_page" value="<?php echo (int) $page; ?>">
<div class="row" style="margin-bottom:10px;" id="brm_bulk_bar">
	<div class="col-md-12">
		<span class="text-muted" style="margin-right:10px;">Selected: <b id="brm_selected_count">0</b></span>
		<button type="button" class="btn btn-sm blue" id="brm_bulk_brm1" <?php echo $b1 > 0 ? '' : 'disabled'; ?>>
			Assign selected → BRM1
		</button>
		<button type="button" class="btn btn-sm blue" id="brm_bulk_brm2" <?php echo $b2 > 0 ? '' : 'disabled'; ?>>
			Assign selected → BRM2
		</button>
		<button type="button" class="btn btn-sm default" id="brm_bulk_clear">Clear selection</button>
	</div>
</div>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th style="width:36px;"><input type="checkbox" id="brm_check_all" title="Select all on this page"></th>
				<th style="width:50px;">ID</th>
				<th>Customer</th>
				<th>Person</th>
				<th>Mobile</th>
				<th>Client Code</th>
				<th>Sales Person</th>
				<th style="min-width:160px;">BRM Assign (Exchange)</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($r && $rowCount > 0) {
			while ($row = mysqli_fetch_assoc($r)) {
				$seid = (int) $row['seid'];
				$salesName = $seid > 0 ? $db->rp_getValue("sales_executive", "name", "id='" . $seid . "'", 0) : '—';
				$bid = (int) $row['brm_admin_id'];
				$cid = (int) $row['id'];
				?>
				<tr>
					<td><input type="checkbox" class="brm-row-check" value="<?php echo $cid; ?>"></td>
					<td><?php echo $cid; ?></td>
					<td><b><?php echo htmlspecialchars($row['company_name']); ?></b></td>
					<td><?php echo htmlspecialchars($row['cname']); ?></td>
					<td><?php echo htmlspecialchars($row['mobile_no1']); ?></td>
					<td><?php echo htmlspecialchars($row['client_code']); ?></td>
					<td><?php echo htmlspecialchars($salesName); ?> <small class="text-muted">(unchanged)</small></td>
					<td>
						<select class="form-control input-sm brm-assign-select" data-cid="<?php echo $cid; ?>">
							<option value="0" <?php echo $bid === 0 ? 'selected' : ''; ?>>Unassigned</option>
							<option value="<?php echo $b1; ?>" <?php echo ($bid === $b1) ? 'selected' : ''; ?>>BRM1</option>
							<option value="<?php echo $b2; ?>" <?php echo ($bid === $b2) ? 'selected' : ''; ?>>BRM2</option>
						</select>
					</td>
				</tr>
				<?php
			}
		} else {
			echo '<tr><td colspan="8" class="text-center text-muted">No customers found.</td></tr>';
		}
		?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="col-md-6">
		<span class="text-muted">Total: <?php echo (int) $total; ?> &nbsp;|&nbsp; Page <?php echo (int) $page; ?> / <?php echo (int) $totalPages; ?></span>
	</div>
	<div class="col-md-6 text-right">
		<?php if ($page > 1) { ?>
			<a href="javascript:;" class="btn btn-sm blue brm-page-link" data-page="<?php echo $page - 1; ?>">Prev</a>
		<?php } ?>
		<?php if ($page < $totalPages) { ?>
			<a href="javascript:;" class="btn btn-sm blue brm-page-link" data-page="<?php echo $page + 1; ?>">Next</a>
		<?php } ?>
	</div>
</div>
<?php
include("disconnect.php");
