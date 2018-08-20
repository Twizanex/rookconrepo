<?php
/*
 * Sales Lead Main Page
 */
error_reporting(0);
include ('../include.php');

// Form submission from details.php
if (isset($_POST['add_sales'])) {
    echo '<script type="text/javascript"> window.location.replace("index.php");</script>';
} ?>

<script src="edit.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(window).resize(function() {
		var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_div .tile-container').offset().top - 1;
		if(available_height > 200) {
            $('#sales_div .tile-sidebar, #sales_div .tile-content').height(available_height);
            $('#sales_div .main-screen-white').height(available_height);
		}
	}).resize();

    //$('.main-screen-white').height($('.tile-content').height() - 96);
    $('.main-screen-white').css('overflow-x','hidden');
    var $sections = $('.accordion-block-details');
    $('.main-screen-white').on('scroll', function(){
        var currentScroll = $('.main-screen .tile-container').offset().top + $('.main-screen-white').find('.preview-block-header').height();
        var $currentSection;
        $sections.each(function(){
            var divPosition = $(this).offset().top;
            if( divPosition - 1 < currentScroll ){
				$('.tile-sidebar li').removeClass('active');
				$('.tile-sidebar [href=#'+$(this).attr('id')+'] li').addClass('active');
            } else if(divPosition < currentScroll + $('.main-screen .tile-container').height()) {
				$('.tile-sidebar [href=#'+$(this).attr('id')+'] li').addClass('active');
            }
        });
    });

    $('#nav_salespath').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='salespath' ) {
            echo 'window.location.replace("?p=salespath&id='.$_GET['id'].'");';
        } ?>
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
        $(this).addClass('active');
    });

    $('#nav_staffinfo').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=staffinfo");';
        } ?>
        $('#nav_salespath, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
        $(this).addClass('active');
    });

    $('#nav_leadinfo').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadinfo");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_services').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=services");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_products').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=products");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_leadsource').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadsource");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_refdocs').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=refdocs");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_marketing').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=marketing");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_infogathering').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=infogathering");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_estimate').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=estimate");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_quote').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=quote");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_nextaction').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=nextaction");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_leadstatus').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadstatus");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_leadnotes').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadnotes");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_tasks').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=tasks");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_time').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=time");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_history').removeClass('active');
    });

    $('#nav_history').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=history");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_leadsource, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadstatus, #nav_leadnotes, #nav_tasks, #nav_time').removeClass('active');
    });

    <?php
        if ( $_GET['p'] == 'details' && (!isset($_GET['a']) || $_GET['a']=='staffinfo') ) { echo "$('#nav_staffinfo').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadinfo' ) { echo "$('#nav_leadinfo').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='services' ) { echo "$('#nav_services').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='products' ) { echo "$('#nav_products').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadsource' ) { echo "$('#nav_leadsource').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='refdocs' ) { echo "$('#nav_refdocs').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='marketing' ) { echo "$('#nav_marketing').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='infogathering' ) { echo "$('#nav_infogathering').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='estimate' ) { echo "$('#nav_estimate').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='quote' ) { echo "$('#nav_quote').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='nextaction' ) { echo "$('#nav_nextaction').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadstatus' ) { echo "$('#nav_leadstatus').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadnotes' ) { echo "$('#nav_leadnotes').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='tasks' ) { echo "$('#nav_tasks').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='time' ) { echo "$('#nav_time').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='history' ) { echo "$('#nav_history').trigger('click');"; }
    ?>

    $('form').submit(function(e){
        if ($('[name=status]').length==0) {
            e.preventDefault();
            alert('Please select the Lead Status.');
        }
    });
});
</script>
</head>

<body>
<?php
    if($_GET['iframe_slider'] != 1) {
    	include_once ('../navigation.php');
    }
    checkAuthorised('sales');
    $statuses     = get_config($dbc, 'sales_lead_status');
    $next_actions = get_config($dbc, 'sales_next_action');
    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sales` FROM `field_config`"));
    $value_config = ','.$field_config['sales'].',';
?>

<div id="sales_div" class="container">
    <div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<iframe src=""></iframe>
		</div>
	</div>

    <div class="row">
		<div class="main-screen"><?php
            if($_GET['iframe_slider'] != 1) {
                include('tile_header.php');
            }

            $page      = preg_replace('/\PL/u', '', $_GET['p']);
            $salesid   = preg_replace('/[^0-9]/', '', $_GET['id']); ?>

            <!--<div class="tile-bar">
                <ul>
                    <li class="<?= ( $page=='details' ) ? 'active' : ''; ?>"><a href="?p=details&id=<?=$salesid;?>">Details</a></li>
                    <li class="<?= ( $page=='template' ) ? 'active' : ''; ?>"><a href="?p=template">Template</a></li>
                    <li class="<?= ( $page=='design' ) ? 'active' : ''; ?>"><a href="?p=design">Design</a></li>
                    <li class="<?= ( $page=='preview' ) ? 'active' : ''; ?>"><a href="?p=preview&id=<?=$salesid;?>">Preview</a></li>
                </ul>
            </div> .tile-bar -->

            <div class="tile-container">
                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile" <?= $_GET['iframe_slider'] == 1 ? 'style="display:none;"' : '' ?>>
                    <ul><?php
                        if (strpos($value_config, ',Sales Path,') !== false) { ?>
                            <a href="#salespath"><li class="collapsed cursor-hand <?= $_GET['p'] == 'salespath' ? 'active' : '' ?>" data-toggle="collapse" data-target="#collapse_salespath" id="nav_salespath"><?= SALES_NOUN ?> Path</li></a><?php
                        }
                        if (strpos($value_config, ',Staff Information,') !== false) { ?>
                            <a href="#staffinfo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_staff_information" id="nav_staffinfo">Staff Information</li></a><?php
                        }
                        if (strpos($value_config, ',Lead Information,') !== false) { ?>
                            <a href="#leadinfo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_lead_information" id="nav_leadinfo">Lead Information</li></a><?php
                        }
                        if (strpos($value_config, ',Service,') !== false) { ?>
                            <a href="#services"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_services" id="nav_services">Services</li></a><?php
                        }
                        if (strpos($value_config, ',Products,') !== false) { ?>
                            <a href="#products"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_products" id="nav_products">Products</li></a><?php
                        }
                        if (strpos($value_config, ',Lead Source,') !== false) { ?>
                            <a href="#leadsource"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_lead_source" id="nav_leadsource">Lead Source</li></a><?php
                        }
                        if (strpos($value_config, ',Reference Documents,') !== false) { ?>
                            <a href="#refdocs"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_reference_documents" id="nav_refdocs">Reference Documents</li></a><?php
                        }
                        if (strpos($value_config, ',Marketing Material,') !== false) { ?>
                            <a href="#marketing"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_reference_documents" id="nav_marketing">Marketing Material</li></a><?php
                        }
                        if (strpos($value_config, 'Information Gathering,') !== false) { ?>
                            <a href="#infogathering"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_information_gathering" id="nav_infogathering">Information Gathering</li></a><?php
                        }
                        if (strpos($value_config, ',Estimate,') !== false) { ?>
                            <a href="#estimate"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_estimate" id="nav_estimate">Estimate</li></a><?php
                        }
                        if (strpos($value_config, ',Next Action,') !== false) { ?>
                            <a href="#nextaction"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_next_action" id="nav_nextaction">Next Action</li></a><?php
                        }
                        if (strpos($value_config, ',Lead Status,') !== false) { ?>
                            <a href="#leadstatus"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_lead_status" id="nav_leadstatus">Lead Status</li></a><?php
                        }
                        if (strpos($value_config, ',Lead Notes,') !== false) { ?>
                            <a href="#leadnotes"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_lead_notes" id="nav_leadnotes">Lead Notes</li></a><?php
                        }
                        if (strpos($value_config, ',Tasks,') !== false) { ?>
                            <a href="#tasks"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_tasks" id="nav_tasks">Tasks</li></a><?php
                        }
                        if (strpos($value_config, ',Time,') !== false) { ?>
                            <a href="#time"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_time" id="nav_time">Time Tracking</li></a><?php
                        }
                        if (strpos($value_config, ',History,') !== false) { ?>
                            <a href="#history"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_history" id="nav_history">History</li></a><?php
                        } ?>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="scale-to-fill has-main-screen tile-content set-section-height"><?php
                    if ( $page=='preview' || empty($page) ) {
                        include('preview.php');
                    } elseif ( $page=='salespath' ) {
                        include('salespath.php');
                    } elseif ( $page=='details' ) {
                        include('details.php');
                    } else {
                        include('details.php');
                    } ?>
                </div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php if($_GET['iframe_slider'] != 1) {
    include ('../footer.php');
} ?>
