<?php
$page_id = 673;
$page_slug = 'quotation_pi_suggest_product';
$page_title = 'Quotation / PI Suggested Products';
$page_hierarchy = array(
	array('link' => '', 'title' => 'Sub Master'),
	array('link' => 'quotation_pi_suggest_product_manage.php', 'title' => $page_title),
);
include('connect.php');
require_once('../include/quotation_pi_suggest_products_helper.php');
armor_quotation_pi_suggest_ensure_table($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include('include_css.php'); ?>
<style type="text/css">
.qp-admin-hint {
	background: #e8f4fc;
	border-left: 4px solid #3598dc;
	padding: 10px 14px;
	margin-bottom: 16px;
	font-size: 13px;
}
.qp-admin-toolbar {
	margin-bottom: 12px;
}
.qp-admin-grid {
	border: 1px solid #ddd;
	border-radius: 4px;
	background: #fff;
	max-height: 620px;
	overflow-y: auto;
}
.qp-admin-item {
	display: flex;
	align-items: center;
	padding: 8px 12px;
	border-bottom: 1px solid #eee;
	cursor: move;
}
.qp-admin-item:last-child { border-bottom: none; }
.qp-admin-item.ui-sortable-helper {
	background: #f9fcff;
	box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.qp-admin-item .qp-drag {
	color: #999;
	width: 24px;
	text-align: center;
	margin-right: 8px;
}
.qp-admin-item .qp-check {
	width: 28px;
	margin-right: 8px;
}
.qp-admin-item .qp-thumb {
	width: 52px;
	height: 52px;
	margin-right: 12px;
	text-align: center;
	line-height: 50px;
	border: 1px solid #eee;
	background: #fafafa;
}
.qp-admin-item .qp-thumb img {
	max-width: 48px;
	max-height: 48px;
	vertical-align: middle;
}
.qp-admin-item .qp-info { flex: 1; }
.qp-admin-item .qp-code {
	font-weight: bold;
	font-size: 13px;
}
.qp-admin-item .qp-name {
	font-size: 12px;
	color: #666;
}
.qp-admin-item.not-found { opacity: 0.65; }
.qp-admin-item.not-selected { background: #fafafa; }
.qp-admin-count {
	font-weight: bold;
	color: #3598dc;
}
</style>
</head>
<body class="page-md">
<?php include('header.php'); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1>
					<a href="dashboard.php" class="primary"><i class="fa fa-arrow-circle-o-left" style="font-size:22px!important;"></i></a>
					&nbsp;<?php $db->pageBar($page_hierarchy); ?>
				</h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<?php $db->printErrorMessage(); ?>
			<?php $db->printSuccessMessage(); ?>

			<div class="qp-admin-hint">
				Select which products appear in the <strong>Suggested Product Range</strong> section on Quotation and PI (Sales Order).
				Checked products will show; drag rows to set display order. Only selected items appear in print.
			</div>

			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption"><i class="fa fa-th"></i> Manage Suggested Products</div>
					<div class="tools">
						<span class="qp-admin-count" id="qp_selected_count">0 selected</span>
					</div>
				</div>
				<div class="portlet-body">
					<div class="qp-admin-toolbar row">
						<div class="col-md-4">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-search"></i></span>
								<input type="text" class="form-control" id="qp_search" placeholder="Search by code or name...">
							</div>
						</div>
						<div class="col-md-4">
							<input type="text" class="form-control" id="qp_add_catno" placeholder="Add product by code (e.g. 2576)">
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-default btn-sm" id="qp_btn_select_all">Select All</button>
							<button type="button" class="btn btn-default btn-sm" id="qp_btn_clear_all">Clear All</button>
							<button type="button" class="btn btn-primary btn-sm" id="qp_btn_add_catno"><i class="fa fa-plus"></i> Add Code</button>
							<button type="button" class="btn green btn-sm" id="qp_btn_save"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
					<div class="loading-div" id="qp_loading" style="display:none;text-align:center;padding:30px;">
						<img src="assets/admin/layout/img/ajax-loader.gif" alt="">
					</div>
					<div class="qp-admin-grid" id="qp_admin_grid"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>
<?php include('include_js.php'); ?>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript">
(function () {
	var defaultImg = <?php echo json_encode(armor_quotation_pi_product_image_url('')); ?>;
	var extraCatnos = [];

	function updateCount() {
		var n = $('#qp_admin_grid .qp-item-check:checked').length;
		$('#qp_selected_count').text(n + ' selected');
	}

	function renderRows(rows) {
		var html = '';
		if (!rows || !rows.length) {
			html = '<div class="alert alert-info" style="margin:12px;">No products found.</div>';
		} else {
			for (var i = 0; i < rows.length; i++) {
				var r = rows[i];
				var cls = 'qp-admin-item';
				if (!r.found) cls += ' not-found';
				if (!r.is_selected) cls += ' not-selected';
				html += '<div class="' + cls + '" data-catno="' + r.catno + '" data-found="' + r.found + '">';
				html += '<div class="qp-drag"><i class="fa fa-bars"></i></div>';
				html += '<div class="qp-check"><input type="checkbox" class="qp-item-check" ' + (r.is_selected ? 'checked' : '') + '></div>';
				html += '<div class="qp-thumb"><img src="' + (r.image || defaultImg) + '" onerror="this.src=\'' + defaultImg + '\';"></div>';
				html += '<div class="qp-info"><div class="qp-code">' + r.catno + '</div><div class="qp-name">' + (r.name || 'Product not found in master') + '</div></div>';
				html += '</div>';
			}
		}
		$('#qp_admin_grid').html(html);
		$('#qp_admin_grid').sortable({
			handle: '.qp-drag',
			items: '.qp-admin-item',
			placeholder: 'qp-admin-item',
			forcePlaceholderSize: true
		});
		updateCount();
	}

	function loadRows() {
		$('#qp_loading').show();
		$.post('quotation_pi_suggest_product_list_ajax.php', { extra: extraCatnos.join(',') }, function (res) {
			$('#qp_loading').hide();
			if (res && res.ack == 1) {
				renderRows(res.rows || []);
			} else {
				toastr.error((res && res.ack_msg) ? res.ack_msg : 'Failed to load products.');
			}
		}, 'json').fail(function () {
			$('#qp_loading').hide();
			toastr.error('Failed to load products.');
		});
	}

	function filterRows() {
		var q = $.trim($('#qp_search').val() || '').toLowerCase();
		$('#qp_admin_grid .qp-admin-item').each(function () {
			var text = $(this).find('.qp-code').text() + ' ' + $(this).find('.qp-name').text();
			$(this).toggle(text.toLowerCase().indexOf(q) >= 0);
		});
	}

	function collectSelectedCatnos() {
		var list = [];
		$('#qp_admin_grid .qp-admin-item').each(function () {
			if ($(this).find('.qp-item-check').is(':checked')) {
				list.push($(this).data('catno'));
			}
		});
		return list;
	}

	$('#qp_search').on('keyup', filterRows);

	$('#qp_admin_grid').on('change', '.qp-item-check', function () {
		$(this).closest('.qp-admin-item').toggleClass('not-selected', !this.checked);
		updateCount();
	});

	$('#qp_btn_select_all').on('click', function () {
		$('#qp_admin_grid .qp-admin-item:visible .qp-item-check').prop('checked', true);
		$('#qp_admin_grid .qp-admin-item:visible').removeClass('not-selected');
		updateCount();
	});

	$('#qp_btn_clear_all').on('click', function () {
		$('#qp_admin_grid .qp-item-check').prop('checked', false);
		$('#qp_admin_grid .qp-admin-item').addClass('not-selected');
		updateCount();
	});

	$('#qp_btn_add_catno').on('click', function () {
		var code = $.trim($('#qp_add_catno').val() || '');
		if (code === '') {
			toastr.warning('Enter product code.');
			return;
		}
		if ($('#qp_admin_grid .qp-admin-item[data-catno="' + code + '"]').length) {
			toastr.info('Product code already in list.');
			$('#qp_add_catno').val('');
			return;
		}
		extraCatnos.push(code);
		$('#qp_add_catno').val('');
		loadRows();
	});

	$('#qp_btn_save').on('click', function () {
		var catnos = collectSelectedCatnos();
		if (!catnos.length) {
			toastr.warning('Select at least one product.');
			return;
		}
		$('#qp_btn_save').prop('disabled', true);
		$.post('quotation_pi_suggest_product_save_ajax.php', { catnos: catnos.join(',') }, function (res) {
			$('#qp_btn_save').prop('disabled', false);
			if (res && res.ack == 1) {
				toastr.success(res.ack_msg || 'Saved.');
				extraCatnos = [];
				loadRows();
			} else {
				toastr.error((res && res.ack_msg) ? res.ack_msg : 'Save failed.');
			}
		}, 'json').fail(function () {
			$('#qp_btn_save').prop('disabled', false);
			toastr.error('Save failed.');
		});
	});

	loadRows();
})();
</script>
</body>
</html>
<?php include('disconnect.php'); ?>
