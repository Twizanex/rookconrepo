<?php
/*
 * Sale Tile Main Page
 */
error_reporting(0);
include ('../include.php');
?>
<script type="text/javascript">
    $(document).ready(function(){
        if($(window).width() > 767) {
            resizeScreen();
            $(window).resize(function() {
                $('.double-scroller div').width($('.main-screen-white:visible').get(0).scrollWidth);
                $('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
                $('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
                resizeScreen();
            });
        }
        $('.dashboard-container').css('height', 'calc(100% - '+$('.double-scroller').height()+'px)');
    });
    
	function doubleScroll() {
		$('.main-screen-white:visible').scrollLeft(this.scrollLeft).scroll();
	}
	function setDoubleScroll() {
		$('.double-scroller').scrollLeft(this.scrollLeft);
	}
    
    function searchLeads(string) {
		$('[data-searchable]').hide();
		$('[data-searchable*="'+(string == '' ? ' ' : string)+'" i]').show();
	}
    function changeLeadStatus(sel) {
        var sid     = sel.id;
        var arr     = sid.split('_');
        var salesid = arr[1];
        var status  = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadStatus&salesid="+salesid+"&status="+status,
            dataType: "html",
            success: function(response){
                window.location.reload();
            }
        });
    }

    function changeLeadNextAction(sel) {
        var sid        = sel.id;
        var arr        = sid.split('_');
        var salesid    = arr[1];
        var nextaction = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadNextAction&salesid="+salesid+"&nextaction="+nextaction,
            dataType: "html",
            success: function(response){}
        });
    }

    function changeLeadFollowUpDate(sel) {
        var sid          = sel.id;
        var arr          = sid.split('_');
        var salesid      = arr[1];
        var followupdate = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadFollowUpDate&salesid="+salesid+"&followupdate="+followupdate,
            dataType: "html",
            success: function(response){}
        });
    }

    function resizeScreen() {
        var view_height = $(window).height() > 500 ? $(window).height() : 500;
        $('#sales_div .scale-to-fill,#sales_div .scale-to-fill .main-screen,#sales_div .tile-sidebar').height($('#sales_div .tile-container').height());
    }
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('sales');
    $config_access = config_visible_function($dbc, 'sales');
    $statuses      = get_config($dbc, 'sales_lead_status');
    $next_actions  = get_config($dbc, 'sales_next_action');
    $dashboard     = preg_replace('/[^0-9]/', '', $_GET['dashboard']);

    if ( !empty($dashboard) ) {
        $query_mod = " AND (`primary_staff`='{$dashboard}' OR CONCAT(',',`share_lead`,',') LIKE '%,{$dashboard},%')";
    } else {
        $query_mod = '';
    }
?>

<div id="sales_div" class="container">
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">

                <!-- Notice --><?php
                $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE `tile`='sales' AND subtab='sales_sales'"));
                $note = $notes['note'];
                    
                if ( !empty($note) && 1 == 0 ) { ?>
                    <div class="notice double-gap-bottom popover-examples">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11">
                            <span class="notice-name">NOTE:</span>
                            <?= $note; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

                <!-- Sales Stats --><?php
                /* $lead_status = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='active_lead'"));
                $lead_statuses = explode(",", $lead_status['value']);
                $status_check = join("','",$lead_statuses); */
                $lead_status_won = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='active_lead_won'"))['value'];
                $lead_status_lost = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='active_lead_lost'"))['value'];
                
                $oppotunities = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`lead_value`) `value` FROM `sales` WHERE (`status` != '$lead_status_won' AND `status` != '$lead_status_lost' AND `status` <> '') AND (`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')" . $query_mod) );
                $closed = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`lead_value`) `value` FROM `sales` WHERE `status`='$lead_status_won' AND (`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')" . $query_mod) );
                $tasks_total = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(`t`.`tasklistid`) `count` FROM `tasklist` `t`, `sales` `s` WHERE (`t`.`clientid`>0 AND `t`.`clientid` IN (`s`.`contactid`)) AND (`s`.`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')") );
                $estimates_total = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(`e`.`estimateid`) `count` FROM `estimate` `e`, `sales` `s` WHERE `e`.`clientid` IN (`s`.`contactid`) AND (`s`.`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')") ); ?>

                <div class="col-xs-12 collapsible-horizontal collapsed" id="summary-div">
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $oppotunities['count'] > 0 ) ? $oppotunities['count'] : 0; ?></div>
                            <div>Total Opportunities in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $oppotunities['value'] > 0 ) ? number_format($oppotunities['value'], 2) : '0.00'; ?></div>
                            <div>Total Open Opportunities in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $closed['count'] > 0 ) ? $closed['count'] : 0; ?></div>
                            <div>Closed Successfully in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $closed['value'] > 0 ) ? number_format($closed['value'], 2) : '0.00'; ?></div>
                            <div>Total Value of Closed in <?= date('F') ?></div>
                        </div>
						<!--<img class="pull-right inline-img" src="../img/icons/ROOK-minus-icon.png" onclick="$('#summary-div').hide();">-->
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $tasks_total['count'] > 0 ) ? $tasks_total['count'] : 0; ?></div>
                            <div>Total Tasks in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $estimates_total['count'] > 0 ) ? $estimates_total['count'] : 0; ?></div>
                            <div>Total Estimates in <?= date('F') ?></div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <?php $page = preg_replace('/\PL/u', '', $_GET['p']); ?>

                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar hide-titles-mob overflow-y">
                    <ul>
						<li class="standard-sidebar-searchbox search-box"><input type="text" class="search-text form-control" placeholder="Search <?= SALES_TILE ?> Leads" onkeyup="searchLeads(this.value);"></li>
                        <li class="<?= ( $page=='dashboard' || empty($page) ) ? 'active' : '' ?>"><a href="index.php">Dashboard</a></li>
                        <li class="sidebar-higher-level"><a class="<?= in_array($_GET['s'],explode(',',$statuses)) ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_status">Status<span class="arrow"></span></a>
							<ul id="collapse_status" class="collapse"><?php
								// Get Lead Statuses added in Settings->Lead Status accordion
								foreach ( explode(',', $statuses) as $status ) {
									if ( trim($_GET['s']==$status) ) { ?>
										<script>
											$(document).ready(function() {
												$('#collapse_status').collapse('show');
											});
										</script><?php
									}
									$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `status`='$status' AND `deleted` = 0 " . $query_mod))['count'];
									echo '<li class="'.($_GET['s'] == $status ? 'active' : '').'"><a href="?p=filter&s='. $status .'">'. $status .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
								} ?>
							</ul>
						</li>
                        <li class="sidebar-higher-level"><a class="<?= !empty($_GET['contactid']) ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_staff">Staff<span class="arrow"></span></a>
                            <ul id="collapse_staff" class="collapse"><?php
                                // Get Staff
                                $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
                                foreach($staff_list as $staffid) {
                                    if ( trim($_GET['contactid']==$staffid) ) { ?>
                                        <script>
                                            $(document).ready(function() {
                                                $('#collapse_staff').collapse('show');
                                            });
                                        </script><?php
                                    }
                                    $row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE (CONCAT(',',`share_lead`,',') LIKE '%,$staffid,%' OR `primary_staff` = '$staffid') AND `deleted` = 0"))['count'];
                                    echo '<li class="'.($_GET['contactid'] == $staffid ? 'active' : '').'"><a href="?p=filter&contactid='. $staffid .'">'. get_contact($dbc, $staffid) .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
                                } ?>
                            </ul>
                        </li>
						<?php $regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
						if(count($regions) > 0) { ?>
							<li class="sidebar-higher-level"sor-hand" data-toggle="collapse" data-target="#collapse_region">Region<span class="arrow"></span></a>
								<ul id="collapse_region" class="collapse"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $regions as $region ) {
										if ( trim($_GET['r'] == $region) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_region').collapse('show');
												});
											</script><?php
										}
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `region`='$region' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['r'] == $region ? 'active' : '').'"><a href="?p=filter&r='. $region .'">'. $region .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $locations =  array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
						if(count($locations) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['l'],$locations) ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_location">Location<span class="arrow"></span></a>
								<ul id="collapse_location" class="collapse"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $locations as $location ) {
										if ( trim($_GET['l'] == $location) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_location').collapse('show');
												});
											</script><?php
										}
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `location`='$location' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['l'] == $location ? 'active' : '').'"><a href="?p=filter&l='. $location .'">'. $location .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $classifications = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0])));
						if(count($classifications) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['c'],$classifications) ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_classification">Classification<span class="arrow"></span></a>
								<ul id="collapse_classification" class="collapse"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $classifications as $classification ) {
										if ( trim($_GET['c'] == $classification) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_classification').collapse('show');
												});
											</script><?php
										}
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `classification`='$classification' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['c'] == $classification ? 'active' : '').'"><a href="?p=filter&c='. $classification .'">'. $classification .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="double-scroller"><div></div></div>
                <div class="scale-to-fill tile-content hide-titles-mob set-section-height"><?php
                    if ( $page=='filter' ) {
                        include('status.php');
                    } else {
                        include('dashboard.php');
                    } ?>
                </div>
                <div class="col-xs-12 show-on-mob"><?php
					include('status_mobile.php');
				?></div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

            <div class="clearfix"></div>

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
