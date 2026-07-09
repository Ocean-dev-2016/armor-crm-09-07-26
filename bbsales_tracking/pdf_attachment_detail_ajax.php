<?php
/*
 * @author Naveen Shingadiya
 */
$page_id=565;$page_slug='page_order';
include("connect_in.php");
$order_id=$_REQUEST['id'];
$pdf_attachment_ids =$_REQUEST['pdf_ids'];
$flag =$_REQUEST['flag'];
// echo $pdf_attachment_ids;exit;
?>

<style>
  table 
  {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 20px;
  }

  th, td 
  {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
  }

  th 
  {
      background-color: #f2f2f2;
  }
  .align-center
  {
  	text-align: center;
  }
</style>
<form role="form" action="" onSubmit="return check_form();"  method="post" enctype="multipart/form-data">
	<div class="form-body">
		<div class="row">
			<div class="col-md-12">
				<?php
					if ($flag != 'show') {
						?>
						<div class="form-group">
							<input type="hidden" name="order_id" id="order_id" value="<?=$order_id?>">
							<label for="#"><b>Add Attachment</b></label>
							<input data-image="<?php echo ($pdf_attachment!="" && file_exists(ORDERS_FILES.$pdf_attachment))?ORDERS_FILES.$pdf_attachment:"";?>" type="file" name="pdf_attachment[]" id="pdf_attachment" data-old-pdf-dom="old_pdf_attachment" data-old-pdf-path="<?php echo $pdf_attachment ?>" value="" multiple >	
						</div>
						<?php
					}
				?>
				<?php 
					if($pdf_attachment_ids!="")
					{
				?>	
						<table style="width: 100%!important;">
					    <tr>
					        <th>PDF Name</th>
					        <th class="align-center">Dowonload</th>
					        <th class="align-center">View</th>
					        <th class="align-center">Delete</th>
					    </tr>
				<?php

							$pdf = explode(",", $pdf_attachment_ids);
							for ($i=0; $i < sizeof($pdf); $i++)
							{ 
								$pdf_url = $db->rp_getValue("media","url","reference_id='".$_REQUEST["id"]."' AND id='".$pdf[$i]."'",0);
								$imagepath = "order_documents/".$pdf_url;
					?>		
						    <tr>
						        <td><?=$pdf_url;?></td>
						        <td class="align-center">
						        	<a href="<?= $imagepath ?>" download  class="text-warning" title="View">
												<i class="fa fa-download" style="font-size: 22px;"></i>
											</a>
										</td>
										<td class="align-center">
											<a href="<?= $imagepath ?>" target="_blank"  class="text-sucess" title="View">
												<i class="fa fa-eye" style="font-size: 22px;"></i>
											</a>
										</td>
										<td class="align-center">
											<a class='delete btn btn-danger btn-sm'  title='Delete' onclick="DeleteImage(<?= $order_id; ?>,'delete_pdf_attachment',<?=$pdf[$i];?>)">
												<i class='fa fa-times'></i>
	             			 	</a>
										</td>
						    </tr>         
					<?php
							}
					?>
    				</table>
    			<?php

						}						
	      	?>	
			</div>
		</div>
		<?php
		if ($flag != 'show') {
		?>
		<div class="form-actions text-center">
			<button type="button" name="uploadButton" class="btn green" id="uploadButton">Submit</button>
		</div>
		<?php
		}
		?>
	</div>					
</form>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[id=pdf_attachment]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})

	function check_form() {
				$(".form-body").children().removeClass("has-error");
				var isValid = true;
				if ($("#pdf_attachment").val() == "" || $("#pdf_attachment").val().split(" ").join("") == ""){
							vd = aj.error('pdf_attachment', "Please Select Image", "add_error");
							isValid = false;
						}
				if (isValid) {
					
				} else {
					return false;
				}
			}
</script>
<script type="text/javascript">
 	$("#uploadButton").on('click', function() 
 	{
	  var files = $("#pdf_attachment").prop("files");
	  var order_id = $("#order_id").val();
	  var form_data = new FormData();

	  // Check if files are selected
	  if (files.length === 0) {
	    alert("Please select one or more PDF files.");
	    return;
	  }

	  // Append each selected file to the form_data
	  for (var i = 0; i < files.length; i++) {
	    var file_data = files[i];
	    form_data.append("pdf_attachment[]", file_data); // Use array notation to send multiple files
	  }

  	form_data.append("order_id", order_id);

  	$.ajax({
    url: "upload_pdf_attachment_detail_ajax.php",
    type: "POST",
    data: form_data,
    contentType: false,
    cache: false,
    processData: false,
    success: function(data) {
      data = JSON.parse(data);
      if (data.ack == 1) {
        toastr.success(data.ack_msg);
        // $('#pdfattachment').hide();
      } else {
        toastr.error(data.ack_msg);
      }
      displayRecords(100,1);
      location.reload();
    }
  });
	});

	function DeleteImage(order_id,flag,media_id)
	{
		var r = confirm("Are you sure you want to delete?");
	 	if(r)
  	{
      $.ajax({
              url:"delete_multiple_pdf_attachment_ajax.php",
              type:"POST",
              data:{
                 
                  order_id:order_id,                
                  flag:flag,                
                  media_id:media_id,
              },
              beforeSend: function() {
								$('.preloader').fadeIn('slow');
							},
              success:function(result) 
              {
                  var result=JSON.parse(result);
                  $('.preloader').fadeOut('slow');
                  
                  if(result.ack==1)
                  {                       
                      toastr.success(result.ack_msg,"Success!!"); 
                      $("#requesting_ajax").click();
                      location.reload();
                  }
                  else
                  {
                      toastr.error(result.ack_msg, 'Error!!');
                      $("#requesting_ajax").click();
                  }
                  displayRecords(100,1);
              },            
          	})
    }
	}

</script>