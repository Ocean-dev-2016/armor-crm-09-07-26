<?php
$page             = $page_slug;
$ctableAPK        = "application_info";
//$apk_name         = $db->rp_getValue($ctableAPK, "file", "isActive=1 AND isDelete=0");
//s$current_apk_path = ADMINSITEURL . APK_PATH . $apk_name;
?>
<div class="page-header-menu">
	<div class="container">
		<div class="page-logo" style="width:auto;">
			<a href="<?php echo ADMINSITEURL; ?>">
				<?php
				// $logo_r=$db->rp_getValue("dealer_distributor_network","logo_detail","isDelete=0 AND admin_type=0");
				// if($logo_r!="")
				// {
				// 	$logo_d=$logo_r;
				// }
				// else
				// {
				// 	$logo_d="";
				// }
				?>
				<img src="../images/armore_logo.jpg" alt="logo" height="40" style="margin:-6px 50px 0 0;" class="logo-default"></a>
		</div>

		<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
		<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
		<div class="hor-menu">

			<ul class="nav navbar-nav">

				<li <?php if ($main_page == "home") { ?> class="menu-dropdown classic-menu-dropdown" <?php } ?>>
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						DASHBOARD <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<!-- 	<li>
							<a href="customer_dashboard.php">
								<i class="icon-list"></i>Customer Dash
							</a>
						</li> -->
						<li>
							<a href="main_dashboard.php">
								<i class="icon-list"></i>MIS Dash
							</a>
						</li>
						<li>
							<?php
							include("../include/top_var.php");
							/*if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) 
							{*/
							if (array_key_exists(0, $left_tracking_array)) {

								if ($db->checkUserPermission($left_tracking_array[0][3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
									$link = $left_tracking_array[0][2][1][2];
							?>

									<a href="<?php echo $link; ?>"><i class="icon-list"></i>Tracking Dash</a>
							<?php
								}
							}
							/*}*/
							?>

						</li>
						<li>
							<?php
							include("../include/top_var.php");
							
							// if (array_key_exists(0, $left_payment_follow_up_array)) {

							// 	if ($db->checkUserPermission($left_payment_follow_up_array[0][3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
							// 		$link = $left_payment_follow_up_array[0][2][1][2];
							?>

									<!-- <a href="<?php echo $link; ?>"><i class="icon-list"></i>Payment Follow Up</a> -->
							<?php
							// 	}
							// }
						
							?>

						</li>
						<!-- <li>
							<a href="statical_dashboard.php"><i class="icon-list"></i>Statistical Dash</a>
						</li> -->
					</ul>
				</li>



				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Sales & Marketing <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_sales_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
								$hasSubMenu = count($arr[2]) > 1;

						?>
								<li class="dropdown-submenu <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_inquiry1<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_inquiry1<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
									<?php if ($hasSubMenu) { ?>
									<ul class="dropdown-menu">
										<?php
										$oe = 0;
										foreach ($arr[2] as $trr) {
											$oe++;
										?>
											<li <?php if ($page == $trr[1]) { ?>class="active" <?php } ?>>
												<?php if ($oe == 1) { ?>
													<script>
														document.getElementById("mntp_inquiry1<?php echo $tp; ?>").setAttribute("href", "<?php echo $trr[2]; ?>");
													</script>
												<?php } ?>
												<a href="<?php echo $trr[2]; ?>">
													<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
											</li>
										<?php
										}
										?>
									</ul>
									<?php } ?>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li>

				<li class="menu-dropdown classic-menu-dropdown">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Order History <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<?php
						if ($db->checkUserPermission(565, $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
							$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
							$get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $check_id . "'", 0);
						?>
							<?php
							if ($get_customer_type == 1 || $_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 3) {
							?>
								<li class="">
									<a href="dealer_orders_manage.php?type=100" id="mntp_order20"><i class="icon-list"></i>All Orders</a>
									<!-- <script>
								document.getElementById("mntp_order11").setAttribute("href", "dealer_orders_manage.php?type=7")
							</script> -->
								</li>
								<!-- <li class="">
							<a href="dealer_orders_manage.php?type=1" id="mntp_order11"><i class="icon-list"></i>CP Orders</a>
							<script>
								document.getElementById("mntp_order11").setAttribute("href", "dealer_orders_manage.php?type=1")
							</script>
						</li> -->
							<?php
							}
							?>
							<?php
							/*if($get_customer_type!=3 || $_SESSION[SITE_SESS.'REFERANCE_TYPE']!=3)
						{ */
							?>
							<!-- <li class="">
							<a href="dealer_orders_manage.php?type=2" id="mntp_order12"><i class="icon-list"></i>Dealer Orders</a>
							<script>
								document.getElementById("mntp_order12").setAttribute("href", "dealer_orders_manage.php?type=2")
							</script>
						</li> -->
							<?php
							//	}
							?>
							<?php
							/*if($get_customer_type!=1 || $_SESSION[SITE_SESS.'REFERANCE_TYPE']!=3)
						{ 
						?>
						<li class="">
							<a href="dealer_orders_manage.php?type=3" id="mntp_order13"><i class="icon-list"></i>Sub Dealer Orders</a>
							<script>
								document.getElementById("mntp_order13").setAttribute("href", "dealer_orders_manage.php?type=3")
							</script>
						</li>
						<?php 
						}*/
							if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 3) {
							?>
								<li class="">
									<a href="dealer_orders_manage.php?type=4" id="mntp_order14"><i class="icon-list"></i> Government Office Orders</a>
									<script>
										document.getElementById("mntp_order14").setAttribute("href", "dealer_orders_manage.php?type=4")
									</script>
								</li>
								<!-- <li class="">
							<a href="dealer_orders_manage.php?type=6" id="mntp_order15"><i class="icon-list"></i> Trader Orders</a>
							<script>
								document.getElementById("mntp_order15").setAttribute("href", "dealer_orders_manage.php?type=6")
							</script>
						</li> -->
								<li class="">
									<a href="dealer_orders_manage.php?type=7" id="mntp_order15"><i class="icon-list"></i> Customer Orders</a>
									<script>
										document.getElementById("mntp_order15").setAttribute("href", "dealer_orders_manage.php?type=6")
									</script>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=9" id="mntp_order15"><i class="icon-list"></i> MEP Consultant Orders</a>
									<script>
										document.getElementById("mntp_order15").setAttribute("href", "dealer_orders_manage.php?type=6")
									</script>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=10" id="mntp_order15"><i class="icon-list"></i> Builder Orders</a>
									<script>
										document.getElementById("mntp_order15").setAttribute("href", "dealer_orders_manage.php?type=6")
									</script>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=11" id="mntp_order15"><i class="icon-list"></i> Brand Approval Visit Orders</a>
									<script>
										document.getElementById("mntp_order15").setAttribute("href", "dealer_orders_manage.php?type=6")
									</script>
								</li>
						<?php
							}
						}
						?>
					</ul>
				</li>

				<!-- <li class="menu-dropdown classic-menu-dropdown">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" >
						Order History <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<?php
						$arc = 0;
						$tp  = 0;
						foreach ($left_order_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
						?>
										<li class="<?php if ($main_page == $arr[1]) { ?>active <?php } ?>">
											<a href="javascript:;" id="mntp_order1<?php echo $tp; ?>">
												<i class="icon-list"></i>
													<?php echo $arr[0]; ?>
											</a>
											<script>
												document.getElementById("mntp_order1<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>")
											</script>
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
						$tp = 0;
						foreach ($left_hr_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_hr<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_hr<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
					<!-- </li>
				 <li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Production <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_production_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_pro<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_pro<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
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
						Master <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_main_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
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
						Sub Master <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_main_array_sub as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="smntp<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("smntp<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li>
				<li class="menu-dropdown classic-menu-dropdown hide ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Store <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						foreach ($left_store_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_store<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_store<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>

									<ul class="dropdown-menu">
										<?php
										$trc = 0;
										$oe = 0;
										foreach ($arr[2] as $trr) {
											$oe++;
										?>
											<li <?php if ($page == $trr[1]) { ?>class="active" <?php } ?>>
												<?php if ($oe == 1) { ?>
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
				</li>
				<li class="hide">
					<?php
					if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
						if (array_key_exists(0, $left_dispatch_array)) {

							if ($db->checkUserPermission($left_dispatch_array[0][3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
								$link = $left_dispatch_array[0][2][1][2];
					?>
								<a href="<?php echo $link; ?>">Dispatch</a>
					<?php
							}
						}
					}
					?>
				</li>

				<li>
					<?php
					if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
						$uid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
						$type = $db->rp_getValue("executive", "type_of_executive", "id=" . $uid . "", 0);
						if ($type == 'super_stockist') {
					?>
							<a href="executive_manage.php?type=<?php echo $type; ?>">My Customer</a>
						<?php
						} else if ($type == 'dealer') {
						?>
							<a href="executive_manage.php?type=<?php echo $type; ?>">My Customer</a>
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
						$tp = 0;
						foreach ($left_utility_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_utility<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_utility<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
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
						Reports <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_reports_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_report<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_report<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
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
						New Reports <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_new_reports_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="ssmntp_report<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("ssmntp_report<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li>


				<!-- Customer Reports -->

				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Customer Reports <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_Customer_reports_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="ccmntp_report<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("ccmntp_report<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li>

				<!-- Customer Reports -->

				<!-- Sales Team Reports -->
				<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Sales Team Reports <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($left_Sales_Team_reports_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="STmntp_report<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("STmntp_report<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li>
				<!-- Sales Team Reports -->


				<!-- 	<li class="menu-dropdown classic-menu-dropdown ">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Account <i class="fa fa-angle-down"></i>	
					</a>
					<ul class="dropdown-menu pull-left">

						<?php
						$arc = 0;
						$tp = 0;
						foreach ($right_account_array as $arr) {
							$tp++;
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {

						?>
								<li class="  <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="javascript:;" id="mntp_account<?php echo $tp; ?>">
										<i class="icon-list"></i>
										<?php echo $arr[0]; ?></a>
									<script>
										document.getElementById("mntp_account<?php echo $tp; ?>").setAttribute("href", "<?php echo $arr[2][1][2]; ?>");
									</script>
								</li>
						<?php
								$arc++;
							}
						}
						?>
					</ul>
				</li> -->
			</ul>
		</div>
		<?php
		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
			$location = "my_account.php";
		} else {
			$location = "my_account_executive.php";
		}
		?>

		<a href="javascript:;" class="menu-toggler"></a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!--<li>
					<div class="page-title pull-right" style="margin-top: 3px;padding: 1px 0px 1px 0px; ">
						<a target="_blank" href="https://contactkro.com/domain/rajcooler_dvb/ccdvb/dashboard.php" class="btn btn-primary" style="text-transform:none;">Exhibition</a>
					</div>
				</li>-->
				<?php
				if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
				?>
					<li id="header_notification_bar" class="dropdown dropdown-extended dropdown-notification">
						<a class="dropdown-toggle" href="notification_manage.php?mode=all">
							<i class="icon-bell"></i>
							<span class="badge badge-default notification-count"> <?php echo $count = $db->rp_getTotalRecord("notification", "isDelete=0 AND isActive=1"); ?></span>
						</a>
						<ul class="dropdown-menu">
							<li class="external">
								<h3>
									<span class="bold notification-status"> <?php echo $count; ?> pending</span>notifications
								</h3>
								<a href="notification_manage.php?mode=all">view all</a>
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
					</li>
				<?php
				}
				?>
				<li class="dropdown dropdown-user dropdown-dark">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<span class="username username-hide-mobile"><i class="icon-user"></i> <?php echo $_SESSION[SITE_SESS . 'SESS_NAME']; ?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<?php
						if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
						?>
							<li>
								<a href="admin_type_manage.php">
									<i class="icon-star"></i> Special Permissions </a>
							</li>
						<?php
						}
						?>
						<li>
							<a href="<?php echo $location; ?>">
								<i class="icon-user"></i> My Profile </a>
						</li>
						<?php
						if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) { ?>
							<li>
								<a href="api_manage.php">
									<i class="fa fa-android"></i> Web APIs </a>
							</li>
							<li>
								<a href="application_info_manage.php">
									<i class="fa fa-mobile"></i>Application Info </a>
							</li>
							<!-- <li>
								<a href="activity_log.php" >
									<i class="fa fa-mobile"></i>Log</a>
							</li>
							<li>
								<a href="<?php echo $current_apk_path; ?>">
									<i class="fa fa-download"></i>Download Application </a>
							</li> -->
							<li>
								<a href="security_manage.php">
									<i class="fa fa-times"></i> Blocked IP </a>
							</li>
							<li>
								<a href="database_backup_manage.php">
									<i class="fa fa-database"></i> Database Backup</a>
							</li>
							<li>
								<a href="licence_key_update.php"><i class="fa fa-key"></i> Encrypted Key</a>
							</li>
						<?php
						} ?>
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