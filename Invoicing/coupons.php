<?php
	/*
	 * Coupons for POS Touch
	 */

	error_reporting(0);
	include ('../include.php');
?>
</head>

<body><?php
	include_once ('../navigation.php');
checkAuthorised('invoicing'); ?>

	<div class="container triple-pad-bottom">		
		<div class="row">
		
			<div class="col-sm-10"><h1>Coupons Dashboard</h1></div>
			<div class="col-sm-2 double-gap-top"><?php
				if ( config_visible_function($dbc, 'pos') == 1 ) {
					echo '<a href="field_config_pos.php" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				} ?>
			</div>
			
			<div class="clearfix"></div>

			<div class='gap-left tab-container mobile-100-container'>
				<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'sell') === TRUE ) { ?>
					<?php
						$pos_layout	= get_config($dbc, 'pos_layout');
						
						if ( $pos_layout=='keyboard' ) { ?>
							<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell</button></a><?php
						} elseif  ( $pos_layout=='touch' ) { ?>
							<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell</button></a><?php
						} else { ?>
							<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell - Keyboard Input</button></a>
							<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell - Touch Input</button></a><?php
						}
					?>
				<?php } else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Sell</button>
				<?php } ?>

				<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'invoices') === TRUE ) { ?>
					<a href="point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Invoices</button></a>
				<?php } else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Invoices</button>
				<?php } ?>

				<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'returns') === TRUE ) { ?>
					<a href="returns.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Returns</button></a>
				<?php } else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Returns</button>
				<?php } ?>

				<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'unpaid') === TRUE ) { ?>
					<a href="unpaid_invoice.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Accounts Receivable</button></a>
				<?php } else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Accounts Receivable</button>
				<?php } ?>

				<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'voided') === TRUE ) { ?>
					<a href="voided.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Voided Invoices</button></a>
				<?php } else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Voided Invoices</button>
				<?php } ?>
			
				<?php if ( vuaed_visible_function ( $dbc, 'pos' ) == 1 ) { ?>
					<a href="coupons.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Coupons</button></a>
				<?php } else {
					echo '<script>
							alert("You do not have access to this page, please consult your software administrator (or settings) to gain access to this page.");
							window.location.replace("point_of_sell.php");
						</script>';
				} ?>
			</div><!-- .mobile-100-container -->

			<form name="form_coupons" method="post" action="" class="form-inline">
				<div class="pad-top pad-bottom">
					<div class="form-group gap-right">
						<label for="search_vendor" class="control-label">Search By Any:</label>
						<input type="text" name="search_term" class="form-control" value="<?php echo (isset($_POST['search_submit'])) ? $_POST['search_term'] : ''; ?>">
					</div>
					<div class="form-group gap-right double-gap-top">
						<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block mobile-100">Search</button>
					</div>
					<div class="form-group gap-right double-gap-top">
						<button type="submit" name="display_all_submit" value="Display All" class="btn brand-btn mobile-block mobile-100">Display All</button>
					</div>
				</div>
			
				<div class="gap-top double-gap-bottom clearfix">
					<div class="mobile-100-container"><a href="add_coupon.php" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Add Coupon</a></div>
				</div>

				<div id="no-more-tables"><?php
					// Search
					$search_term = '';
					if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_submit']) ) {
						$search_term = ( !empty ($_POST['search_term']) ) ? filter_var ($_POST['search_term'], FILTER_SANITIZE_STRING) : '';
					} else {
						$search_term = '';
					}

					/* Pagination Counting */
					$rowsPerPage = 25;
					$pageNum = 1;

					if ( isset($_GET['page']) ) {
						$pageNum = $_GET['page'];
					}

					$offset = ($pageNum - 1) * $rowsPerPage;
					
					if ( $search_term == '' ) {
						$query_check_credentials = "SELECT * FROM `pos_touch_coupons` WHERE `deleted` != 1 LIMIT $offset, $rowsPerPage";
						$query = "SELECT COUNT(*) AS numrows FROM `pos_touch_coupons` WHERE `deleted` != 1";
					} else {
						$query_check_credentials = "SELECT * FROM `pos_touch_coupons` WHERE `description` LIKE '%{$search_term}%' AND `deleted` != 1 ORDER BY `expiry_date` DESC LIMIT $offset, $rowsPerPage";
						$query = "SELECT COUNT(*) AS numrows FROM `pos_touch_coupons` WHERE `description` LIKE '%{$search_term}%' AND `deleted` != 1 ORDER BY `expiry_date` DESC";
					}

					$result		= mysqli_query($dbc, $query_check_credentials);
					$num_rows	= ($result) ? mysqli_num_rows($result) : 0;
					
					if ( $num_rows > 0 ) {
						
						echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
						
						$get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `pos_dashboard` FROM `field_config`" ) );
						$value_config		= ',' . $get_field_config['pos_dashboard'] . ',';

						echo '<table class="table table-bordered">';
							echo '<tr class="hidden-xs hidden-sm">';
								if ( strpos($value_config, ',Coupon ID,') !== FALSE ) {
									echo '<th>Coupon ID</th>';
								}
								if ( strpos($value_config, ',Coupon Title,') !== FALSE ) {
									echo '<th>Title</th>';
								}
								if ( strpos($value_config, ',Coupon Description,') !== FALSE ) {
									echo '<th>Description</th>';
								}
								if ( strpos($value_config, ',Discount Type,') !== FALSE ) {
									echo '<th>Discount Type</th>';
								}
								if ( strpos($value_config, ',Discount,') !== FALSE ) {
									echo '<th>Coupon Value</th>';
								}
								if ( strpos($value_config, ',Start Date,') !== FALSE ) {
									echo '<th>Start Date</th>';
								}
								if ( strpos($value_config, ',Expiry Date,') !== FALSE ) {
									echo '<th>Expiry Date</th>';
								}
								if ( strpos($value_config, ',# of Times Used,') !== FALSE ) {
									echo '<th># of Times Used</th>';
								}
								if ( strpos($value_config, ',Function,') !== FALSE ) {
									echo '<th>Function</th>';
								}
							echo '</tr>';

							while ( $row = mysqli_fetch_array($result) ) {
								echo '<tr>';
									if ( strpos($value_config, ',Coupon ID,') !== FALSE ) {
										echo '<td data-title="Coupon ID">' . $row['couponid'] . '</td>';
									}
									if ( strpos($value_config, ',Coupon Title,') !== FALSE ) {
										echo '<td data-title="Title">' . $row['title'] . '</td>';
									}
									if ( strpos($value_config, ',Coupon Description,') !== FALSE ) {
										echo '<td data-title="Description">' . html_entity_decode ( $row['description'] ) . '</td>';
									}
									if (strpos($value_config, ',Discount Type,') !== FALSE ) {
										echo '<td data-title="Discount Type">' . $row['discount_type'] . '</td>';
									}
									if (strpos($value_config, ',Discount,') !== FALSE ) {
										echo '<td data-title="Coupon Value">' . $row['discount'] . '</td>';
									}
									if (strpos($value_config, ',Start Date,') !== FALSE ) {
										echo '<td data-title="Start Date">' . $row['start_date'] . '</td>';
									}
									if (strpos($value_config, ',Expiry Date,') !== FALSE ) {
										echo '<td data-title="Expiry Date">' . $row['expiry_date'] . '</td>';
									}
									if (strpos($value_config, ',# of Times Used,') !== FALSE ) {
										echo '<td data-title="# of Times Used">' . $row['used_times'] . '</td>';
									}
									if ( strpos($value_config, ',Function,') !== FALSE ) {
										echo '
											<td data-title="Function" width="16%" nowrap>
												<a href="add_coupon.php?couponid=' . $row['couponid'] . '" title="Edit this submission">Edit</a> |
												<a href="../delete_restore.php?action=delete&couponid=' . $row['couponid'] . '" onclick="return confirm(\'Are you sure you want to delete?\')" title="Delete this submission">Delete</a>
											</td>';
									}
								echo '</tr>';
							}
						echo '</table>';
						
						echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
					
					} else {
						echo '<h2>No Records Found.</h2>';
					} ?>
				
				</div><!-- #no-more-tables -->
				
				<div class="double-gap-top clearfix">
					<div class="mobile-100-container"><a href="add_coupon.php" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Add Coupon</a></div>
				</div>

			</form>

		</div><!-- .row -->
	</div><!-- .container -->
	
<?php include ('../footer.php'); ?>