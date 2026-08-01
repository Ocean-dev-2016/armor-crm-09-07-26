/**
 * Channel Partner — generate temp PDF on server, share as WhatsApp attachment when possible.
 * Mobile (Android/iOS): Web Share with PDF file → WhatsApp chat attachment.
 * Desktop: downloads PDF + opens CP WhatsApp chat (browser cannot auto-attach file).
 */
function cpShareOpenWhatsApp(phone, text) {
	var q = 'text=' + encodeURIComponent(text || '');
	if (phone) {
		q = 'phone=' + encodeURIComponent(phone) + '&' + q;
	}
	window.open('https://api.whatsapp.com/send?' + q, '_blank');
}

function cpShareDownloadBlob(blob, fileName) {
	var a = document.createElement('a');
	a.href = URL.createObjectURL(blob);
	a.download = fileName || 'document.pdf';
	document.body.appendChild(a);
	a.click();
	setTimeout(function () {
		URL.revokeObjectURL(a.href);
		if (a.parentNode) {
			a.parentNode.removeChild(a);
		}
	}, 1500);
}

function cpSharePdfWithFile(res, blob) {
	var fileName = res.file_name || 'document.pdf';
	var file;
	try {
		file = new File([blob], fileName, { type: 'application/pdf' });
	} catch (e) {
		file = null;
	}

	/* True attachment path (mobile browsers that support file share) */
	if (file && navigator.share && navigator.canShare) {
		var shareData = {
			files: [file],
			title: res.title || 'PDF',
			text: res.text || ''
		};
		if (navigator.canShare(shareData)) {
			navigator.share(shareData).then(function () {
				if (typeof toastr !== 'undefined') {
					toastr.success('PDF shared');
				}
			}).catch(function () {
				/* User cancelled or share failed — fallback */
				cpShareDownloadBlob(blob, fileName);
				cpShareOpenWhatsApp(
					res.phone,
					(res.text || res.title || 'PDF') + '\n\nPDF: ' + res.file_url + '\n\n(Please attach downloaded PDF if needed)'
				);
			});
			return;
		}
	}

	/* Desktop / unsupported: download PDF + open WhatsApp with PDF file URL */
	cpShareDownloadBlob(blob, fileName);
	cpShareOpenWhatsApp(
		res.phone,
		(res.text || res.title || 'PDF') + '\n\nPDF Attachment:\n' + res.file_url
	);
	if (typeof toastr !== 'undefined') {
		toastr.info('PDF downloaded. Attach it in WhatsApp chat if needed.');
	}
}

function cpSharePdfFile(opts) {
	var $btn = opts.$btn ? opts.$btn : null;
	var btnHtml = $btn ? $btn.html() : '';
	if ($btn) {
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Preparing PDF...');
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
			var res = null;
			try {
				res = (typeof raw === 'string') ? JSON.parse($.trim(raw)) : raw;
			} catch (e) {
				if ($btn) {
					$btn.prop('disabled', false).html(btnHtml);
				}
				alert('Share failed. Server response invalid. Re-upload channel_partner_share_pdf_ajax.php');
				return;
			}
			if (!res || parseInt(res.ack, 10) !== 1 || !res.file_url) {
				if ($btn) {
					$btn.prop('disabled', false).html(btnHtml);
				}
				alert((res && res.ack_msg) ? res.ack_msg : 'PDF create failed');
				return;
			}

			/* Fetch generated PDF as blob for attachment share */
			var xhr = new XMLHttpRequest();
			xhr.open('GET', res.file_url, true);
			xhr.responseType = 'blob';
			xhr.onload = function () {
				if ($btn) {
					$btn.prop('disabled', false).html(btnHtml);
				}
				if (xhr.status >= 200 && xhr.status < 300 && xhr.response) {
					cpSharePdfWithFile(res, xhr.response);
				} else {
					alert('PDF download failed from server temp folder.');
					cpShareOpenWhatsApp(
						res.phone,
						(res.text || res.title || 'PDF') + '\n\nPDF:\n' + res.file_url
					);
				}
			};
			xhr.onerror = function () {
				if ($btn) {
					$btn.prop('disabled', false).html(btnHtml);
				}
				cpShareOpenWhatsApp(
					res.phone,
					(res.text || res.title || 'PDF') + '\n\nPDF:\n' + res.file_url
				);
			};
			xhr.send();
		},
		error: function (xhr) {
			if ($btn) {
				$btn.prop('disabled', false).html(btnHtml);
			}
			var code = xhr && xhr.status ? xhr.status : '';
			alert('Share PDF request failed' + (code ? (' (' + code + ')') : '') + '. Upload ajax file on live.');
		}
	});
}
