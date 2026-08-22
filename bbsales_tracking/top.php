<?php
$page             = $page_slug;
$ctableAPK        = "application_info";
//$apk_name         = $db->rp_getValue($ctableAPK, "file", "isActive=1 AND isDelete=0");
//s$current_apk_path = ADMINSITEURL . APK_PATH . $apk_name;
?>
<div class="page-header-menu">
	<div class="container">
		<div class="page-logo" style="width:auto;">
			<a href="dashboard.php">
				<img src="<?php echo CRM_LOGO_URL; ?>?v=<?php echo @filemtime(CRM_LOGO_PATH); ?>" alt="<?php echo SITETITLE; ?>" height="40" style="margin:-6px 50px 0 0;max-width:180px;object-fit:contain;" class="logo-default">
			</a>
		</div>

		<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
		<!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
		<div class="hor-menu">

			<ul class="nav navbar-nav">
			<?php
			$is_cp_menu = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
			if ($is_cp_menu) {
				/* Channel Partner portal — only menus from business flow */
			?>
				<li class="classic-menu-dropdown <?php if ($page == 'channel_partner_customer') { ?>active<?php } ?>">
					<a href="channel_partner_customer_manage.php"><i class="fa fa-users"></i> My Customers</a>
				</li>
				<li class="classic-menu-dropdown <?php if ($page == 'channel_partner_order') { ?>active<?php } ?>">
					<a href="channel_partner_order_manage.php"><i class="fa fa-shopping-cart"></i> Customer Order</a>
				</li>
				<li class="classic-menu-dropdown <?php if ($page == 'channel_partner_stock') { ?>active<?php } ?>">
					<a href="channel_partner_stock_manage.php"><i class="fa fa-cubes"></i> My Stock</a>
				</li>
				<li class="classic-menu-dropdown <?php if ($page == 'channel_partner_payment') { ?>active<?php } ?>">
					<a href="channel_partner_payment.php"><i class="fa fa-money"></i> Receive Payment</a>
				</li>
				<li class="classic-menu-dropdown <?php if ($page == 'channel_partner_ledger') { ?>active<?php } ?>">
					<a href="channel_partner_ledger.php"><i class="fa fa-book"></i> Party Ledger</a>
				</li>
				<li class="classic-menu-dropdown">
					<a href="channel_partner_print_settings.php"><i class="fa fa-file-text-o"></i> SO / PI Format</a>
				</li>
			<?php
			} else {
			?>

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
						$is_cp_menu = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
						foreach ($left_sales_array as $arr) {
							$tp++;
							// Channel Partner login: only own CP portal menus (not full Customer CRM)
							if ($is_cp_menu && (!isset($arr[1]) || $arr[1] != 'channel_partner')) {
								continue;
							}
							if ($db->checkUserPermission($arr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
								// CP login: only My Customers + My Stock under Channel Partner menu
								if ($is_cp_menu && isset($arr[1]) && $arr[1] == 'channel_partner') {
									$cp_menu_pages = array(
										array("My Customers", "channel_partner_customer", "channel_partner_customer_manage.php", 555),
										array("Customer Order", "channel_partner_order", "channel_partner_order_manage.php", 565),
										array("My Stock", "channel_partner_stock", "channel_partner_stock_manage.php", 650),
										array("SO/PI Header-Footer", "channel_partner_print", "channel_partner_print_settings.php", 650),
									);
						?>
								<li class="dropdown-submenu <?php if ($main_page == $arr[1]) { ?> active<?php } ?>">
									<a href="channel_partner_customer_manage.php" id="mntp_inquiry1<?php echo $tp; ?>">
										<i class="icon-list"></i> Channel Partner</a>
									<ul class="dropdown-menu">
										<?php foreach ($cp_menu_pages as $trr) {
											if (!$db->checkUserPermission($trr[3], $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
												continue;
											}
										?>
											<li <?php if ($page == $trr[1]) { ?>class="active" <?php } ?>>
												<a href="<?php echo $trr[2]; ?>">
													<i class="icon-arrow-right"></i> <?php echo $trr[0]; ?></a>
											</li>
										<?php } ?>
									</ul>
								</li>
						<?php
									$arc++;
									continue;
								}
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
							$is_cp_menu = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
							$get_customer_type = $db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $check_id . "'", 0);
							if ($is_cp_menu) {
						?>
								<li class="">
									<a href="dealer_orders_manage.php?type=channel_partner" id="mntp_order_cp"><i class="icon-list"></i>My Orders</a>
								</li>
						<?php
							} else if ($get_customer_type == 1 || $_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 3) {
						?>
								<li class="">
									<a href="dealer_orders_manage.php?type=100" id="mntp_order20"><i class="icon-list"></i>All Orders</a>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=channel_partner_portal" id="mntp_order_cp_portal"><i class="icon-list"></i>Channel Partner Portal Orders</a>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=channel_partner" id="mntp_order_cp"><i class="icon-list"></i>Channel Partner Orders</a>
								</li>
								<li class="">
									<a href="dealer_orders_manage.php?type=pending_payment" id="mntp_order_pending_pay"><i class="icon-list"></i>Pending Payment</a>
								</li>
								<li class="">
									<a href="armor_payment_receive.php" id="mntp_order_receive_pay"><i class="icon-list"></i>Receive Payment</a>
								</li>
						<?php
							}
							/* Hidden: Government Office / Customer / MEP / Builder / Brand Approval Visit Orders */
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

				<li class="classic-menu-dropdown <?php if (isset($main_page) && $main_page == 'employee_chat') { ?> active<?php } ?>">
					<a href="employee_chat_manage.php">
						Chat
						<?php
						$ecMenuUnread = 0;
						if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] > 0) {
							$ecMenuMe = (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
							$ecMenuR = @$db->rp_getData('employee_chat_thread', 'id', "isDelete=0 AND (user_one_id='{$ecMenuMe}' OR user_two_id='{$ecMenuMe}')", '', 0);
							if ($ecMenuR) {
								while ($ecMenuT = mysqli_fetch_assoc($ecMenuR)) {
									$ecMenuUnread += (int) $db->rp_getTotalRecord('employee_chat_message', "thread_id='" . (int) $ecMenuT['id'] . "' AND sender_id!='{$ecMenuMe}' AND is_read=0 AND isDelete=0", 0);
								}
							}
						}
						if ($ecMenuUnread > 0) {
							echo ' <span class="badge badge-danger chat-unread-count">' . (int) $ecMenuUnread . '</span>';
						} else {
							echo ' <span class="badge badge-danger chat-unread-count" style="display:none;">0</span>';
						}
						?>
					</a>
				</li>

				<!-- Remark Analysis Report -->
				<?php
				$rarShow = false;
				if (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0) {
					$rarShow = true;
				} else if (isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && $db->checkUserPermission(671, $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'], 'view')) {
					$rarShow = true;
				}
				if ($rarShow) {
				?>
				<li class="menu-dropdown classic-menu-dropdown <?php if (isset($main_page) && $main_page == 'remark_analysis_report') { ?> active<?php } ?>">
					<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;">
						Remark Analysis Report <i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu pull-left">
						<li class="<?php if (isset($page_slug) && $page_slug == 'remark_wise_report') { ?> active<?php } ?>">
							<a href="remark_wise_report.php">
								<i class="icon-list"></i> Remark Wise Report
							</a>
						</li>
					</ul>
				</li>
				<?php } ?>
				<!-- Remark Analysis Report -->

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
			<?php } /* end non-CP menus */ ?>
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
				$ecUnread = 0;
				$is_cp_top = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
				if (!$is_cp_top && isset($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']) && (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] > 0) {
					$ecMe = (int) $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
					$ecThreadR = @$db->rp_getData('employee_chat_thread', 'id', "isDelete=0 AND (user_one_id='{$ecMe}' OR user_two_id='{$ecMe}')", '', 0);
					if ($ecThreadR) {
						while ($ecT = mysqli_fetch_assoc($ecThreadR)) {
							$ecUnread += (int) $db->rp_getTotalRecord('employee_chat_message', "thread_id='" . (int) $ecT['id'] . "' AND sender_id!='{$ecMe}' AND is_read=0 AND isDelete=0", 0);
						}
					}
				}
				if (!$is_cp_top) {
				?>
				<li id="header_chat_bar" class="dropdown dropdown-extended dropdown-notification">
					<a class="dropdown-toggle" href="employee_chat_manage.php" title="Employee Chat">
						<i class="fa fa-comments"></i>
						<span class="badge badge-default chat-unread-count" style="<?php echo ($ecUnread > 0) ? '' : 'display:none;'; ?>"><?php echo (int) $ecUnread; ?></span>
					</a>
				</li>
				<?php } ?>
				<?php
				if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
				?>
					<li id="header_notification_bar" class="dropdown dropdown-extended dropdown-notification">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<i class="icon-bell"></i>
							<span class="badge badge-default notification-count"><?php echo $count = $db->rp_getTotalRecord("notification", "isDelete=0 AND isActive=1"); ?></span>
						</a>
						<ul class="dropdown-menu admin-notif-dropdown">
							<li class="external">
								<h3>
									<span class="bold notification-status"><?php echo $count; ?> pending</span> notifications
								</h3>
								<a href="notification_manage.php?mode=all">view all</a>
							</li>
							<li>
								<ul class="dropdown-menu-list scroller notification-container" style="height: 280px; overflow-y: auto;" data-handle-color="#637283">
								</ul>
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
							<a href="<?php echo ADMINSITEURL; ?>logout.php">
								<i class="icon-logout"></i> Sign out </a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>