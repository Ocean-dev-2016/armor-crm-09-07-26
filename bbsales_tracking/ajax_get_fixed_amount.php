<?php
/**
 * Expense subcategory options by category + sales person.
 * Falls back to category-only list when no sales-person mapping exists.
 */
$page_id = 400;
$page_slug = 'dashboard';
require_once("connect.php");

$id = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
$sales_id = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";

if ($id == "") {
	echo '<option value="">Select Expence Sub Category</option>';
	require_once 'disconnect.php';
	exit;
}

$expence_subcat_r = false;
if ($sales_id != "") {
	$expence_subcat_r = $db->rp_getData(
		"expence_sub_category",
		"*",
		"expense_category_id='" . $id . "' AND sales_executive_id='" . $sales_id . "' AND isDelete=0",
		"name ASC",
		0
	);
}

// Fallback: category-level sub categories (any sales person / shared)
if (!$expence_subcat_r) {
	$expence_subcat_r = $db->rp_getData(
		"expence_sub_category",
		"*",
		"expense_category_id='" . $id . "' AND isDelete=0",
		"name ASC",
		0
	);
}

if ($expence_subcat_r) {
	echo '<option value="">Select Expence Sub Category</option>';
	$seen = array();
	while ($expence_subcat_d = mysqli_fetch_assoc($expence_subcat_r)) {
		// Deduplicate by name when falling back across sales persons
		$key = strtolower(trim($expence_subcat_d['name']));
		if (isset($seen[$key])) {
			continue;
		}
		$seen[$key] = 1;
		?>
		<option data-expense_amount="<?php echo htmlspecialchars($expence_subcat_d['fix_amount']); ?>" value="<?php echo (int)$expence_subcat_d['id']; ?>"><?php echo htmlspecialchars($expence_subcat_d['name']); ?></option>
		<?php
	}
} else {
	echo '<option value="">No Sub Category found</option>';
}

require_once 'disconnect.php';
?>
