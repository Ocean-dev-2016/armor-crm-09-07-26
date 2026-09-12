var ChannelAjax = function () {
	
	var orignal_container = $("#channel-container");
	var result_container = $("#followup-ajax-result-container-1");
	var ajax_url = "followup_grid_get_ajax.php";
	var modification_url = "#";
	var show_count = 100;
	var channel_name_filter = "";
	var interests = [];
	var locations = [];
	var page = 1;
	var handlersBound = false;

	var handleTable = function () {
		if (typeof CurrentView !== "undefined" && String(CurrentView) === "0") {
			ajax_url = "followup_get_ajax.php";
		} else {
			ajax_url = "followup_grid_get_ajax.php";
		}

		if (handlersBound) {
			return;
		}
		handlersBound = true;

		// Event delegation so pagination works after every AJAX reload
		$(result_container).on("click", ".paging_simple_numbers a, .pagination a", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var newPage = $(this).attr("data-page");
			if (typeof newPage === "undefined" || newPage === null || newPage === "") {
				return false;
			}
			page = parseInt(newPage, 10) || 1;
			getDataFromAJAX();
			return false;
		});

		$(result_container).on("change", "#numRecords, select.rowCountSpinner", function () {
			show_count = parseInt($(this).val(), 10) || 100;
			page = 1;
			getDataFromAJAX();
		});

		$(result_container).on("click", ".loadMoreBtn", function () {
			page = $(this).data("page");
			$(".loadMoreBtn").hide();
			getDataFromAJAX();
		});
	};

	var getDataFromAJAX = function () {
		var visitor_id = $("#visitor_id").val();
		var followup_flag = $("#followup_flag").val();
		var inquiry_id = $("#inquiry_id").val();
		var quotation_id = $("#quotation_id").val();
		var executive_id = $("#executive_id").val();
		var sales_id = (typeof window.followupSalesId !== "undefined") ? window.followupSalesId : "";

		// Prefer current dropdown value if already rendered
		var numSel = $(result_container).find("#numRecords").val();
		if (numSel) {
			show_count = parseInt(numSel, 10) || show_count;
		}

		$.ajax({
			url: ajax_url,
			type: "GET",
			data: {
				page: page,
				visitor_id: visitor_id,
				followup_flag: followup_flag,
				inquiry_id: inquiry_id,
				quotation_id: quotation_id,
				executive_id: executive_id,
				sales_id: sales_id,
				show: show_count,
				channel_name: channel_name_filter,
				interests: interests,
				locations: locations
			},
			success: function (result) {
				$(result_container).html(result);
			},
			error: function () {
				toastr.error("Failed to load followup list");
			}
		});
	};

	return {
		init: function () {
			page = 1;
			handleTable();
			getDataFromAJAX();
		},
		reload: function (resetPage) {
			if (resetPage) {
				page = 1;
			}
			getDataFromAJAX();
		},
		changeRowCount: function (num) {
			show_count = parseInt(num, 10) || 100;
			page = 1;
			getDataFromAJAX();
		},
		goToPage: function (num) {
			page = parseInt(num, 10) || 1;
			getDataFromAJAX();
		}
	};

}();

function changeDisplayRowCount(numRecords) {
	if (typeof ChannelAjax.changeRowCount === "function") {
		ChannelAjax.changeRowCount(numRecords);
	}
}

function displayRecords(numRecords, pageNum) {
	if (typeof numRecords !== "undefined" && numRecords) {
		ChannelAjax.changeRowCount(numRecords);
		return;
	}
	if (typeof pageNum !== "undefined" && pageNum) {
		ChannelAjax.goToPage(pageNum);
		return;
	}
	ChannelAjax.reload(false);
}

jQuery(document).ready(function () {
	ChannelAjax.init();
});
