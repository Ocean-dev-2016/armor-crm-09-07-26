<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
if (!empty($_POST["keyword"])) {
	if ($_POST["mode"]=="member_phone") {
	    $result = $db->rp_getData("member","*","mobile_no LIKE '".$_POST["keyword"]."%'","mobile_no",0,"0,6");
	    if (!empty($result)) {
	?>
	<ul class="member-list">
	<?php
	        while ($member = mysqli_fetch_assoc($result)) {
	?>
	<li data-name="<?= $member['name']?>" onClick="selectmember('<?php
	            echo $member["mobile_no"];
	?>');"><?php
	            echo $member["mobile_no"];
	?></li>
	<?php
	        }
	?>
	</ul>
	<?php
	    }
	}
	
}
?>
<?php require_once 'disconnect.php';  ?>