<?php
	include('connect_in.php');
?>
<!-- <link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<select class="js-data-example-ajax">
  <option>select item</option>
</select>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<!-- <script type="text/javascript" src="assets/global/plugins/select2/select2.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $('.js-data-example-ajax').select2({
  ajax: {
  	data : $('.js-data-example-ajax').val(),
    url: 'test2_dropdown.php',
    success: function(result) {
    		$('.js-data-example-ajax').select2("destroy");
			$('.js-data-example-ajax').html(result);
    		$('.js-data-example-ajax').select2();
	}
    // dataType: 'json'
    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
  }
});
</script>