<?php
$page=$page_slug;
$ctableAPK 	= "application_info";
$apk_name=$db->rp_getValue($ctableAPK,"file","isActive=1 AND isDelete=0");
$current_apk_path=ADMINSITEURL.APK_PATH.$apk_name;
?>
<div class="page-header-menu">
	<div class="container">
		<div class="page-logo">
				<a href="<?php echo ADMINSITEURL; ?>"><img src="../images/ic_launcher.png" alt="logo" height="40" style="margin:-6px 50px 0 0" class="logo-default"></a>
			</div>
			
		<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
		<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
		<div class="hor-menu ">
		
			<ul class="nav navbar-nav">
				<li <?php if($main_page=="home"){ ?> class="active"<?php } ?>>
					<a href="dashboard.php">Mis Dashboard</a>
				</li>
				<li>
					<?php 
					include("../include/top_var.php");		
					if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
					if(array_key_exists(0,$left_tracking_array)){
										
						if($db->checkUserPermission($left_tracking_array[0][3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
							$link=$left_tracking_array[0][2][1][2];
							?>
							<a href="<?php echo $link; ?>">Tracking Dashboard</a>
							<?php
						}
						/*else
						{
							$link="";
						}*/
					}
					}
					?>
					
				</li>

				<li>
					<a href="product_manage.php">Product Management</a>
				</li>

				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Sales & Marketing <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<?php
							include("../include/top_var.php");		
						?>
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_main_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
							<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
								<a href="javascript:;" id="mntp<?php echo $tp; ?>">
								<i class="icon-list"></i>
								<?php echo $arr[0]; ?></a>
								<script>
								document.getElementById("mntp<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
								</script>
							</li>
							<?php
							$arc++;
							}
						}	
						?>
					</ul>
				</li>

				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Master <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_main_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<!--<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> -->
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>
				<!-- <li>
					<a href="visit_manage.php">Visit</a>
						
				</li> -->

				<li>
					<a href="expense_manage.php">Expense</a>
						
				</li>
			<!--	<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Sales <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_sales_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_inquiry1<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_inquiry1<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_inquiry1<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>
			-->	<!--<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Production <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_production_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_production<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_production<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_production<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>-->
			<!--	<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Purchase <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_purchase_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_purchase<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_purchase<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_purchase<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>-->	
			 <!-- <li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Store <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						foreach($left_store_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_store<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_store<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_store<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li> -->
				<!-- <li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Orders<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_order_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_orders<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_orders<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li> -->
				<li>
					<?php 
					if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
					if(array_key_exists(0,$left_dispatch_array)){
										
						if($db->checkUserPermission($left_dispatch_array[0][3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
							$link=$left_dispatch_array[0][2][1][2];
							?>
							<a href="<?php echo $link; ?>">Dispatch</a>
							<?php
						}
						/*else
						{
							$link="";
						}*/
					}
					}
					?>
					
				</li>
				<li>
					<?php 
					if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
					if(array_key_exists(0,$left_inquiry_array)){
										
						if($db->checkUserPermission($left_order_array[0][3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
							$link=$left_inquiry_array[0][2][1][2];
							?>
							<a href="<?php echo $link; ?>">Inquiry</a>
							<?php
						}
						/*else
						{
							$link="";
						}*/
					}
					}
					?>
					
				</li>
				<!--li>
					<?php 
					/*if(array_key_exists(0,$left_product_stock_array)){
										
						if($db->checkUserPermission($left_product_stock_array[0][3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
							$link=$left_product_stock_array[0][2][1][2];
							?>
							<a href="<?php echo $link; ?>">Product Stock</a>
							<?php
						}
						/*else
						{
							$link="";
						}*/
					//}
					?>
					
				</li-->
				<!--li>
					<a href="product_final_manage.php">Product Stock</a>
				</li-->
				<!-- <li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Account<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_account_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_account<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_account<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_account<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul>
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li> -->
					<!-- <li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Reports <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_reports_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<?php 
						if(sizeof($arr[2])!=0)
						{ $class="dropdown-submenu"; }else {$class="";} ?>
						<li class="<?php echo $class; if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_reports<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_reports<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[4]; ?>");
							</script>
							
							<ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_reports<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li> -->
				
				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					HR <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_hr_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_hr<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_hr<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<!--ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_hr<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul--> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>
				<li>
					<a href="dealer_orders_manage.php">Order</a>
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
					$type=$db->rp_getValue("executive","type_of_executive","id=".$uid."",0);
					if($type=='super_stockist')
					{
						?>
						<a href="ss_orders_manage.php">Order</a>
						<?php
					}
					else if($type=='dealer')
					{
						?>
						<a href="dealer_orders_manage.php">Order</a>
						<?php
					}
					else if($type=='outlets')
					{
						?>
						<a href="outlet_orders_manage.php">Order</a>
						<?php
					}
				?>
				<?php
				}
				?>
				</li>
				
				<li>
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
					$type=$db->rp_getValue("executive","type_of_executive","id=".$uid."",0);
					if($type=='super_stockist')
					{
						?>
						<a href="executive_manage.php?type=<?php echo $type;?>">My Customer</a>
						<?php
					}
					else if($type=='dealer')
					{
						?>
						<a href="executive_manage.php?type=<?php echo $type;?>">My Customer</a>
						<?php
					}
					
				?>
				<?php
				}
				?>
				</li>
				
				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
					Utility <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						
						<?php
						$arc = 0;
						$tp=0;
						foreach($left_utility_array as $arr){
						$tp++;						
						if($db->checkUserPermission($arr[3],$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'],'view'))
						{
					
						?>
						<li class=" dropdown-submenu <?php if($main_page==$arr[1]){ ?> active<?php } ?>">
							<a href="javascript:;" id="mntp_utility<?php echo $tp; ?>">
							<i class="icon-list"></i>
							<?php echo $arr[0]; ?></a>
							<script>
							document.getElementById("mntp_utility<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
							</script>
							
							<!--ul class="dropdown-menu">
								<?php 
								$trc = 0;
								$oe=0;
								foreach($arr[2] as $trr){
								$oe++;
								?>
								<li <?php if($page==$trr[1]){ ?>class="active"<?php } ?>>
									<?php if($oe==1){ ?>
									<script>
									document.getElementById("mntp_utility<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
									</script>
									<?php } ?>
									<a href="<?php echo $trr[2]; ?>">
									<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
								</li>
								<?php
								$trc++;
								}
								?>
							</ul--> 
						</li>
						<?php
						$arc++;
						}
						}	
						?>
					</ul>
				</li>
				
				
			</ul>
		</div>
		<?php
		if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
		{
			$location="my_account.php";
		}
		else
		{
			$location="my_account_executive.php";
		}
		?>
		
		<a href="javascript:;" class="menu-toggler"></a>
		<div class="top-menu">
		
				<ul class="nav navbar-nav pull-right">
				<?php 
			if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
			{
			?>
					<li id="header_notification_bar" class="dropdown dropdown-extended dropdown-notification">
						<a class="dropdown-toggle" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
						<i class="icon-bell"></i>
						<span class="badge badge-default notification-count"> <?php echo $count=$db->rp_getTotalRecord("notification","isDelete=0 AND isActive=1"); ?></span>
						</a>
						<ul class="dropdown-menu">
							<li class="external">
							<h3>
							<span class="bold notification-status"> <?php echo $count; ?> pending</span>
							notifications
							</h3>
							<a href="notification_manage.php?mode=all">view all</a>
							</li>
					</li>
						
					<li>
						<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;">
						<ul class="dropdown-menu-list scroller notification-container" style="height: 250px; overflow: hidden; width: auto;" data-handle-color="#637283" data-initialized="1">
							
						</ul>
							<div class="slimScrollBar" style="background: rgb(99, 114, 131) none repeat scroll 0% 0%; width: 7px; position: absolute; top: 116px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 112.41px;"></div>
							<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
						</div>
					</li>
				</ul>
			<?php
			}
			?>
				<!--FOr User Login/Permissions -->
					<li class="dropdown dropdown-user dropdown-dark">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<span class="username username-hide-mobile"><i class="icon-user"></i> <?php echo $_SESSION[SITE_SESS.'SESS_NAME']; ?></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-default">
						<?php
						if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
						{
						?>
							<li>
								<a href="admin_type_manage.php">
								<i class="icon-star"></i> Special Permissions </a>
							</li>
						<?php
						}
						?>
							<li>
								<a href="<?php echo $location;?>">
								<i class="icon-user"></i> My Profile </a>
							</li>
							<li>
								<a href="api_manage.php">
								<i class="fa fa-android"></i> Web APIs </a>
							</li>
							<li>
								<a href="application_info_manage.php">
								<i class="fa fa-mobile"></i>Application Info </a>
							</li>
							<li>
								<a href="<?php echo $current_apk_path; ?>">
								<i class="fa fa-download"></i>Download Application </a>
							</li>
							<li>
								<a href="security_manage.php">
								<i class="fa fa-times"></i> Blocked IP </a>
							</li>
							<li>
								<a href="database_backup_manage.php">
								<i class="fa fa-database"></i> Database Backup</a>
							</li>
							<li>
								<a href="logout.php">
								<i class="icon-logout"></i> Sign out </a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
	</div>
</div>