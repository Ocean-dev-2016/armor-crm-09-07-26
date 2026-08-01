/**
 * Channel Partner — Share PDF via WhatsApp Web URL (no OS share dialog).
 * Opens: https://api.whatsapp.com/send?phone=CP_NUMBER&text=...PDF_URL...
 */
function cpSharePdfFile(opts) {
	var $btn = opts.$btn ? opts.$btn : null;
	var btnHtml = $btn ? $btn.html() : '';
	if ($btn) {
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Preparing PDF...');
	}
	$.ajax({
		url: 'channel_partner_share_pdf_ajax.php',
		type: 'POST',
		dataType: 'json',
		data: {
			type: opts.type,
			party_id: opts.party_id || 0,
			cp_id: opts.cp_id || 0
		},
		success: function (res) {
			if ($btn) {
				$btn.prop('disabled', false).html(btnHtml);
			}
			if (!res || parseInt(res.ack, 10) !== 1) {
				alert((res && res.ack_msg) ? res.ack_msg : 'PDF create failed');
				return;
			}
			/* WhatsApp Web share — text + PDF download URL (CP number) */
			var msg = (res.title || 'PDF') + "\n\n";
			if (res.text) {
				msg = res.text + "\n\n";
			}
			msg += "PDF Download:\n" + res.file_url;

			var q = 'text=' + encodeURIComponent(msg);
			if (res.phone) {
				q = 'phone=' + encodeURIComponent(res.phone) + '&' + q;
			}
			window.open('https://api.whatsapp.com/send?' + q, '_blank');
		},
		error: function () {
			if ($btn) {
				$btn.prop('disabled', false).html(btnHtml);
			}
			alert('Share PDF request failed');
		}
	});
}
