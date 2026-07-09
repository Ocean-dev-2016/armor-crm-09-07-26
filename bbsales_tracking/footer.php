<div class="page-footer">
	<div class="container">
		<?php echo date("Y"); ?> &copy; <?php echo SITENAME; ?> by <a href="<?= DESIGNBY_LINK ?>" target="_blank" title="Web, Mobile And Software Development Company" class="font-yellow"><?= DESIGNBY ?></a>
		
		<span style="float:right;"><b>&nbsp; &nbsp;   /  &nbsp; &nbsp;Financial Year : <?php echo FINANCIAL_YEAR?></b> </span>
		<span style="float:right;"><b>Licensed to : <?php echo SITENAME; ?></b> </span>
	</div>
</div>
<div class="scroll-to-top">
	<i class="icon-arrow-up"></i>
</div>
<?php
$db->disconnect();
// echo $db->rp_getValue("department","name","id=1");
?>