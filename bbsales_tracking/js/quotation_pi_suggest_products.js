/**
 * Suggested product range — Quotation / Sales Order (PI)
 */
var QpSuggest = {
	getCustomerId: function() {
		if (typeof qpSuggestCustomerResolver === 'function') {
			return qpSuggestCustomerResolver();
		}
		if ($('#edit_dealer_id').length && $('#edit_dealer_id').val()) {
			return $('#edit_dealer_id').val();
		}
		if ($('#dealer_id1').length && $('#dealer_id1').val()) {
			return $('#dealer_id1').val();
		}
		if ($('#dealer_id').length && $('#dealer_id').val()) {
			return $('#dealer_id').val();
		}
		if ($('#customer_id').length && $('#customer_id').val()) {
			return $('#customer_id').val();
		}
		return '';
	},
	getExcludeIds: function() {
		var ids = [];
		$('#datatable_1').find('input.product_id').each(function() {
			var v = parseInt($(this).val(), 10);
			if (v > 0 && ids.indexOf(v) === -1) {
				ids.push(v);
			}
		});
		return ids.join(',');
	},
	load: function() {
		var cid = QpSuggest.getCustomerId();
		if (!cid) {
			$('#qp_suggest_products_wrap').html('<div class="alert alert-warning">Select customer to view suggested products.</div>');
			return;
		}
		$('#qp_suggest_products_wrap').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
		$.post('quotation_pi_suggest_products_ajax.php', {
			customer_id: cid,
			exclude_ids: QpSuggest.getExcludeIds()
		}, function(res) {
			if (res.ack == 1) {
				$('#qp_suggest_products_wrap').html(res.html);
			} else {
				$('#qp_suggest_products_wrap').html(res.html || '<div class="alert alert-danger">Failed to load.</div>');
			}
		}, 'json');
	},
	addProduct: function(productId, weightId) {
		var cid = QpSuggest.getCustomerId();
		if (!cid) {
			toastr.error('Please select customer first.');
			return;
		}
		$.post('quotation_pi_suggest_product_detail_ajax.php', {
			customer_id: cid,
			product_id: productId,
			weight_id: weightId
		}, function(res) {
			if (res.ack != 1) {
				toastr.error(res.msg || 'Could not add product.');
				return;
			}
			if ($('#product_id option[value="' + res.option_value + '"]').length === 0) {
				$('#product_id').append(res.option_html);
			}
			$('#product_id').val(res.option_value);
			if ($('#order_item_brand option').length > 1 && !$('#order_item_brand').val()) {
				$('#order_item_brand').val($('#order_item_brand option:eq(1)').val());
			}
			$('#qty').val(1);
			$.post('ajax_get_order_unit_from_product.php', { pro_id: res.pro_id }, function(unitHtml) {
				$('#bag_box_id').html(unitHtml);
				if (res.item_order_unit !== '') {
					$('#bag_box_id').val(res.item_order_unit);
				}
				$('#add').trigger('click');
				setTimeout(function() { QpSuggest.load(); }, 400);
			});
		}, 'json');
	}
};

$(function() {
	if ($('#qp_suggest_products_wrap').length) {
		$(document).on('click', '.qp-suggest-add', function(e) {
			e.preventDefault();
			QpSuggest.addProduct($(this).data('product-id'), $(this).data('weight-id'));
		});
		$('#dealer_id, #dealer_id1, #edit_dealer_id, #customer_id').on('change', function() {
			QpSuggest.load();
		});
		$(document).on('click', '.delete', function() {
			setTimeout(function() { QpSuggest.load(); }, 400);
		});
		setTimeout(function() { QpSuggest.load(); }, 800);
	}
});
