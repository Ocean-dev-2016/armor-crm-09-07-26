<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport">
<meta name="robots" content="noindex">

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css">
<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css">
<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">

<link href="assets/global/css/components-md.css" id="style_components" rel="stylesheet" type="text/css">
<link href="assets/global/css/plugins-md.css" rel="stylesheet" type="text/css">
<link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css">
<link href="assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color">
<link href="assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/toastr.css"/>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
<script type="text/javascript" src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<link rel="manifest" href="manifest.json">
<style>
div.xdsoft_datetimepicker{
    z-index:500000;!important   
}
.preloader {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   z-index: 9999;
   background-image: url('../images/loading.gif');
   background-repeat: no-repeat; 
   background-color: #9c9c9c66;
   background-position: center;
   background-size: 100px !important;
}
/* Admin notification bell dropdown popup */
.admin-notif-dropdown {
	min-width: 320px !important;
	max-width: 380px;
	padding: 0;
	border: 1px solid #e7ecf1;
	box-shadow: 0 5px 20px rgba(0,0,0,.15);
}
.admin-notif-dropdown .external {
	background: #3598dc;
	padding: 12px 15px;
}
.admin-notif-dropdown .external h3 {
	color: #fff !important;
	margin: 0;
	font-size: 14px;
}
.admin-notif-dropdown .external a {
	color: #fff !important;
	opacity: 0.9;
}
.admin-notif-dropdown .notification-container {
	padding: 0;
	margin: 0;
	list-style: none;
}
.admin-notif-dropdown .notification-container > li {
	border-bottom: 1px solid #f0f0f0;
	padding: 10px 12px;
}
.admin-notif-dropdown .notification-container > li:hover {
	background: #f9f9f9;
}
.admin-notif-dropdown .notif-item-title {
	color: #333;
	font-weight: 600;
	font-size: 13px;
	margin-bottom: 4px;
}
.admin-notif-dropdown .notif-item-desc {
	color: #666;
	font-size: 12px;
	line-height: 1.4;
	margin-bottom: 6px;
}
.admin-notif-dropdown .notif-item-time {
	color: #999;
	font-size: 11px;
}
.admin-notif-dropdown .notif-item-actions {
	margin-top: 6px;
}
.admin-notif-dropdown .notif-empty {
	padding: 30px 15px;
	text-align: center;
	color: #999;
}
</style>