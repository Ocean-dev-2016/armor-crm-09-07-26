<?php 
include('connect_in.php');

$main_page  = "utility";
$page       = "Encrypted Key";
$page_title = "Encrypted Key";
            
$where  = "id=1";
// $where  = "id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'";

if($_REQUEST['submit'])
{
    // echo "Ss";exit;
    $licence_key_date = $_REQUEST['licence_key_date'];
    $security_code = $_REQUEST['security_code'];
    if($licence_key_date!="" && $security_code!="")
    {
        if(LICENCE_SECURITY_CODE==$security_code)
        {
            $updated=$db->rp_update("licence_key",array("licence_key_date"=>$licence_key_date),$where,0);
            if($updated)
            {
                $db->rp_location("licence_key_update.php?ack=1");  
            }
        }
        else{
            $db->rp_location("licence_key_update.php?ack=3"); 
        }
    } 
    else
    {
        $db->rp_location("licence_key_update.php?ack=2");
    } 
}  
$licence_key_date = $db->rp_getValue("licence_key","licence_key_date",$where);
?>
<!DOCTYPE html> 
<html lang="en"> 
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
        <?php include("include_css.php"); ?> 
    </head>
    <body class="page-md">
        <?php include("header.php"); ?>
        <div class="page-container">
            <div class="page-head bg-grey">
                <div class="container">
                    <div class="page-title">
                        <h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <div class="container"> 
                    <?php if(isset($_REQUEST['ack']) && $_REQUEST['ack']!=""){  
                        if($_REQUEST['ack']==1){
                            $ack_msg = "<strong>Success! </strong>Detail Updated successfully!!";
                            $cls="alert-success";
                        }
                        else if($_REQUEST['ack']==2){
                            $ack_msg = "<strong>Success! </strong>Please Enter Encrypted Key Or Security Code!!";
                            $cls="alert-danger";
                        }
                        else if($_REQUEST['ack']==3){
                            $ack_msg = "<strong>Failed! </strong>Security Code does not match!!";
                            $cls="alert-danger";
                        }
                    ?>
                    <div class="alert <?= $cls; ?> alert-dismissable">
                        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button> 
                        <?= $ack_msg; ?>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light">
                                <form role="form" action="" method="post">
                                    <div class="form-group"> 
                                        <label>Security Code <code>*</code></label>
                                        <input type="password" name="security_code" class="form-control input-medium" id="security_code" value="<?php echo $security_code; ?>">
                                    </div>
                                    <div class="form-group">
                                         <label>Encrypted Key <code>*</code></label>
                                        <input type="text" name="licence_key_date" class="form-control input-large" id="licence_key_date" value="<?php echo $licence_key_date; ?>">
                                    </div>
                                    <div class="form-group">
                                        <input class="btn btn-danger btn-sm" name="submit" type="submit" value="Submit">
                                    </div>
                                </form>
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
