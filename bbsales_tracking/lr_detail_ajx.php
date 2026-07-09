<?php
// $page_id=569;$page_slug='page_order_ajax';
// include("connect.php");
// $dispatch_id=$_REQUEST['id'];

?>
<!-- <form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-10">					
			<div class="form-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<input type="hidden" name="dispatch_id" value="<?=$dispatch_id?>">
							<label for="#"><b>Add Attachment</b></label>
							<input data-image="<?php echo ($image_path!="" && file_exists(LRCOPY_A.$image_path))?LRCOPY_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" multiple >									
						</div>
					</div>
				</div>
				<div class="form-actions text-center">
					<button type="submit" name="submit" class="btn green">Submit</button>
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
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
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
</script> -->

<?php
/*
 * @author Dinesh Lodhavi
 */
include("connect_in.php");
$order_id=$_REQUEST['id'];
// print_r($_REQUEST);die;
?>
<form role="form" action="" onSubmit="return check_form();"  method="post" enctype="multipart/form-data">
	<div class="form-body">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<input type="hidden" name="order_id" value="<?=$order_id?>">
					<!-- <label for="#"><b>Add Attachment</b></label> -->
					<input data-image="<?php echo ($image_path!="" && file_exists(LRCOPY_A.$image_path))?LRCOPY_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" multiple >									
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					<label>Lr Number <code>*</code></label>
					<input type="text" class="form-control" name="lr_number" id="lr_number" value="<?php echo $lr_number; ?>">
					<p class="help-block"></p>
				</div>
			</div>
		</div>
		<div class="form-actions text-center">
			<button type="submit" name="lr_add_submit" class="btn green" id="submit-lr-attchament">Submit</button>
			<!-- <button type="button" class="btn btn-default" onClick="window.location.href='lr_manage.php'">Back</button> -->
		</div>
	</div>					
</form>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
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
                 if ($("#lr_number").val() == "" || $("#lr_number").val().split(" ").join("") == ""){
							vd = aj.error('lr_number', "Please Enter Lr Number", "add_error");
							isValid = false;
						} 
				if ($("#image_path").val() == "" || $("#image_path").val().split(" ").join("") == ""){
							vd = aj.error('image_path', "Please Select Image", "add_error");
							isValid = false;
						}
				if (isValid) {
					
				} else {
					return false;
				}
			}
</script>