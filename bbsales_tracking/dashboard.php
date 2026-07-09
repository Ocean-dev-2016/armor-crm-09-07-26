<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$main_page = "home";
/*echo $db->encrypt_decrypt('encrypt', "5-11-2022");exit;*/

// echo $total_quotation; exit();
?>
<!DOCTYPE html> 
<html lang="en" class="no-js"> 
  <head>
    <meta charset="utf-8">
    <title>Dashboard | <?php echo SITETITLE; ?></title>
    <?php include("include_css.php"); ?>
    <style>
    body, html {
      height: 100%;
      margin: 0;
    }

    .bg {
      /* The image used */
      background-image: url("../images/blank_image.png");
      /* Full height */
      height: 100%; 
      /* Center and scale the image nicely */
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
    }
    </style>
  </head>
  <body class="page-md">
    <?php include("header.php"); ?>
   	<hr>
  	<div class="bg"></div>

    <!-- END PAGE CONTAINER -->
    <?php include("footer.php"); ?>
    <?php include("include_js.php"); ?>

    <script type="text/javascript">
      <?php
      if($_REQUEST['notification_flag']==1)
      {
      ?>
      GenerateFcmToken();
      <?php 
      }
      ?>
      function GenerateFcmToken()
      {
        var config = {
          apiKey: "AIzaSyCrGaViP8w_D8hzkxSoFuO_fzs-fEH7Dfg",
          authDomain: "cmk-crm.firebaseapp.com",
          projectId: "cmk-crm",
          storageBucket: "cmk-crm.appspot.com",
          messagingSenderId: "345899882377",
          appId: "1:345899882377:web:5efbbdfd36a1f23671f358",
          measurementId: "G-2TS49WRQ29",
          
          // databaseURL: "https://craftbox-5d2bb.firebaseio.com",
        };
        if (!firebase.apps.length) {
          firebase.initializeApp({});
        }else {
          firebase.app(); // if already initialized, use that one
        }
        // Retrieve Firebase Messaging object.
        //  const messaging = firebase.messaging();
        const messaging = firebase.messaging();
        
        messaging.requestPermission().then(function() {
            getRegToken();
        }).catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        })
    }

    function getRegToken(argument) {      
      const messaging = firebase.messaging();
       messaging
         .requestPermission()
         .then(function () {
           //MsgElem.innerHTML = "Notification permission granted." 
           console.log("Notification permission granted.");
      
           // get the token in the form of promise
          return messaging.getToken()
         })
         .then(function(token) {
          //   alert(token);
          console.log( token);
          if(token){
          
          saveToken(token);
              
          }
           // print the token on the HTML page
          // TokenElem.innerHTML = "Device token is : <br>" + token
         })
         .catch(function (err) {
         //ErrElem.innerHTML = ErrElem.innerHTML + "; " + err
         console.log("Unable to get permission to notify.", err);
       });
    }

    function saveToken(currentToken) {
      var UpdateTo = "refresh_token_web";
      // alert(currentToken);
        $.ajax({
            url: 'ajax_update_token.php',
            method: 'post',
            data: {token : currentToken,update_to:UpdateTo},
        }).done(function(result){
            console.log(result);
            result = $.parseJSON(result);
        if (result.ack == 0) {
        toastr.error(result.ack_msg);
      } else {
        toastr.success(result.ack_msg);
        // window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
      }
        })

    }
    </script>
  </body>
</html>