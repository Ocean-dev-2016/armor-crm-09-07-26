<?php
$page_id=402;
$page_slug="logout";
include("connect.php");
// echo $db->encrypt_decrypt('encrypt', "19-01-2023");exit;
$main_page = "home";
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>404 Page Not Found | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link href="assets/admin/pages/css/error.css" rel="stylesheet" type="text/css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12 page-404">
                    <div class="number">
                        203 
                    </div>
                    <div class="details">
                        <h3>Oops! Something went wrong.</h3>
                        <p> You are blocked due to <b style="font-size: 25px;">licence expired</b>.<br/> Contact administrator for more information.<br/>If licence is renewed click on "return to home" button.</p>
                        <p><a href="index.php" class="btn red btn-outline"> Return home </a>
                        <br></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
</body>
</html>