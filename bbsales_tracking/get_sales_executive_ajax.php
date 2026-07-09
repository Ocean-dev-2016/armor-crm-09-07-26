<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$customer_id = $_POST["customer_id"];
$seid= $db->rp_getvalue("executive","seid","isDelete=0 AND id='".$customer_id."'",0);
// echo $seid; exit();



?>
<?php
	if(!empty($_POST["customer_id"]))
		{	
		
		$exe_r = $db->rp_getData("sales_executive","*","isDelete=0","",0);
		if(mysqli_num_rows($exe_r)>0)
		{
			?>

			<option value="">Select Sales Person</option>


			<?php 
			while($exe_d = mysqli_fetch_array($exe_r))
			{
				?>
			<option <?=($seid == $exe_d['id'])?"selected":"";?> value="<?php echo $exe_d['id']; ?>" ><?php echo $exe_d['name']; ?></option>
				<?php
			}
		}
																
		}
		else
		{
		?>
		<option value="">Select Person</option>
		 <?php
		}
		require_once("disconnect.php");
      ?>
      
      