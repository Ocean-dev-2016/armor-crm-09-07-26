/**
 * Channel Partner — Share via WhatsApp Web URL (PHP 5 / live safe).
 * Opens: https://api.whatsapp.com/send?phone=CP_NUMBER&text=...PDF_OR_PRINT_URL...
 */
function cpSharePdfFile(opts) {
	var $btn = opts.$btn ? opts.$btn : null;
	var btnHtml = $btn ? $btn.html() : '';
	if ($btn) {
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Preparing...');
	}
	$.ajax({
		url: 'channel_partner_share_pdf_ajax.php',
		type: 'POST',
		dataType: 'text',
		data: {
			type: opts.type,
			party_id: opts.party_id || 0,
			cp_id: opts.cp_id || 0
		},
		success: function (raw) {
			if ($btn) {
				$btn.prop('disabled', false).html(btnHtml);
			}
			var res = null;
			try {
				res = (typeof raw === 'string') ? JSON.parse(raw) : raw;
			} catch (e) {
				alert('Share failed. Server response invalid. Please re-upload channel_partner_share_pdf_ajax.php');
				return;
			}
			if (!res || parseInt(res.ack, 10) !== 1) {
				alert((res && res.ack_msg) ? res.ack_msg : 'Share failed');
				return;
			}
			var msg = (res.text ? res.text : (res.title || 'Document')) + "\n\n";
			if (parseInt(res.pdf_ok, 10) === 1) {
				msg += "PDF Download:\n" + res.file_url;
			} else {
				msg += "Open / Print PDF:\n" + res.file_url;
			}
			var q = 'text=' + encodeURIComponent(msg);
			if (res.phone) {
				q = 'phone=' + encodeURIComponent(res.phone) + '&' + q;
			}
			window.open('https://api.whatsapp.com/send?' + q, '_blank');
		},
		error: function (xhr) {
			if ($btn) {
				$btn.prop('disabled', false).html(btnHtml);
			}
			var code = xhr && xhr.status ? xhr.status : '';
			alert('Share PDF request failed' + (code ? (' (' + code + ')') : '') + '. Check file uploaded on live.');
		}
	});
}
