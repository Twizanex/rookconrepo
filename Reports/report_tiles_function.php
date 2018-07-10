<?php
/*
 * List all function here.
 * Update Reports switch-case in root/tile_data.php whenever this page updates
 */
function reports_tiles($dbc) {
    include('../Reports/field_list.php'); ?>
    <script>
    $(document).ready(function() {
        $('[name=select_report]').off('change').change(function() {
            if(this.value != '') {
                window.location.replace(this.value);
            }
        });
    });
    </script>
    <div class="main-screen">
        <div class="tile-header standard-header">
            <div class="pull-right settings-block">
                <?php if(config_visible_function($dbc, 'report') == 1) {
                    echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                } ?>
            </div>
            <div class="scale-to-fill">
                <h1 class="gap-left"><a href="report_tiles.php">Reports</a></h1>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php $value_config = ','.get_config($dbc, 'reports_dashboard').',';
        $report_tabs = !empty(get_config($dbc, 'report_tabs')) ? get_config($dbc, 'report_tabs') : 'operations,sales,ar,marketing,compensation,pnl,customer,staff';
        $report_tabs = explode(',', $report_tabs);
        if(empty($_GET['type'])) {
            $_GET['type'] = $report_tabs[0];
        } ?>

        <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
            <?php if(in_array('operations',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_operations">
                                Operations<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_operations" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=operations&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('sales',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_sales">
                                Sales<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_sales" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=sales&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('ar',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_ar">
                                Accounts Receivable<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_ar" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('marketing',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_marketing">
                                Marketing<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_marketing" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('compensation',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_compensation">
                                Compensation<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_compensation" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('pnl',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_pnl">
                                Profit & Loss<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_pnl" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('customer',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_customer">
                                Customer<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_customer" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('staff',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_staff">
                                Staff<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_staff" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    	<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
            <ul>
                <?php if(in_array('operations',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=operations"><li <?= $_GET['type']=='operations' || empty($_GET['type']) ? 'class="active"' : '' ?>>Operations</li></a>
                <?php } ?>
                <?php if(in_array('sales',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=sales"><li <?= $_GET['type']=='sales' ? 'class="active"' : '' ?>>Sales</li></a>
                <?php } ?>
                <?php if(in_array('ar',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=ar"><li <?= $_GET['type']=='ar' ? 'class="active"' : '' ?>>Accounts Receivable</li></a>
                <?php } ?>
                <?php if(in_array('marketing',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=marketing"><li <?= $_GET['type']=='marketing' ? 'class="active"' : '' ?>>Marketing</li></a>
                <?php } ?>
                <?php if(in_array('compensation',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=compensation"><li <?= $_GET['type']=='compensation' ? 'class="active"' : '' ?>>Compensation</li></a>
                <?php } ?>
                <?php if(in_array('pnl',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=pnl"><li <?= $_GET['type']=='pnl' ? 'class="active"' : '' ?>>Profit &amp; Loss</li></a>
                <?php } ?>
                <?php if(in_array('customer',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=customer"><li <?= $_GET['type']=='customer' ? 'class="active"' : '' ?>>Customer</li></a>
                <?php } ?>
                <?php if(in_array('staff',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=staff"><li <?= $_GET['type']=='staff' ? 'class="active"' : '' ?>>Staff</li></a>
                <?php } ?>
            </ul>
    	</div>

        <div class="scale-to-fill has-main-screen hide-titles-mob">
            <div class="main-screen standard-body form-horizontal">
                <div class="standard-body-title">
                    <?php $title = '';
                    switch($_GET['type']) {
                        case 'sales':
                            $title = 'Sales';
                            break;
                        case 'ar':
                            $title = 'Accounts Receivables';
                            break;
                        case 'marketing':
                            $title = 'Marketing';
                            break;
                        case 'compensation':
                            $title = 'Compensation';
                            break;
                        case 'pnl':
                            $title = 'Profit & Loss';
                            break;
                        case 'customer':
                            $title = 'Customer';
                            break;
                        case 'staff':
                            $title = 'Staff';
                            break;
                        case 'operations':
                        default:
                            $title = 'Operations';
                            break;
                    }
                    if(!empty($_GET['report'])) {
                        $title .= ': '.$report_list[$_GET['report']][1];
                    } ?>
                    <h3><?= $title ?></h3>
                </div>

                <div class="standard-body" style="padding: 0.5em;">
                    <?php reports_tiles_content($dbc); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<?php }
function reports_tiles_content($dbc) {
    include('../Reports/field_list.php');
    $value_config = ','.get_config($dbc, 'reports_dashboard').',';
    $report_tabs = !empty(get_config($dbc, 'report_tabs')) ? get_config($dbc, 'report_tabs') : 'operations,sales,ar,marketing,compensation,pnl,customer,staff';
    $report_tabs = explode(',', $report_tabs);
    if(empty($_GET['type'])) {
        $_GET['type'] = $report_tabs[0];
    }
    if($_GET['mobile_view'] == 'true') { ?>
        <script>
        $(document).ready(function() {
            $('[name=select_report]').off('change').change(function() {
                var panel = $(this).closest('.panel').find('.panel-body');
                if(this.value != '') {
                    $.ajax({
                        url: this.value+'&mobile_view=true',
                        method: 'POST',
                        response: 'html',
                        success: function(response) {
                            panel.html(response);
                        }
                    });
                }
            });
        });
        </script>
    <?php } ?>
    <div class="form-group form-horizontal">
        <label class="col-sm-4 control-label">Report:</label>
        <div class="col-sm-8">
            <select class="chosen-select-deselect" data-placeholder="Select Report" name="select_report">
                <option></option>
                <?php
                    /* Hide Kristi from accessing Profit & Loss report on SEA (temp fix)
                     * Code also added on report_profit_loss.php */
                    $contactid = $_SESSION['contactid'];
                    if ( $_SERVER['SERVER_NAME'] == 'sea-alberta.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-regina.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-saskatoon.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-vancouver.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea.freshfocussoftware.com' ) {
                        $results = mysqli_query ( $dbc, "SELECT `user_name` FROM `contacts` WHERE `contactid`='$contactid'");
                        while ( $row = mysqli_fetch_assoc ( $results) ) {
                            $user_name = $row[ 'user_name' ];
                            if ( $user_name == 'kristi' ) {
                                $sea_kristi = true;
                                break;
                            }
                        }
                    }

                // Operations
                if($_GET['type'] == 'operations' || empty($_GET['type'])) {
                    foreach($operations_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=operations&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Sales
                else if($_GET['type'] == 'sales') {
                    foreach($sales_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=sales&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Accounts Receivables
                else if($_GET['type'] == 'ar') {
                    foreach($ar_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=ar&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Profit & Loss
                else if ( $_GET['type']=='pnl' ) {
                    foreach($pnl_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=pnl&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Marketing
                else if($_GET['type'] == 'marketing') {
                    foreach($marketing_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=marketing&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Compensation
                else if($_GET['type'] == 'compensation') {
                    foreach($compensation_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=compensation&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Customer
                else if($_GET['type'] == 'customer') {
                    foreach($customer_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=customer&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                // Staff
                else if($_GET['type'] == 'staff') {
                    foreach($staff_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== FALSE && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === TRUE) {
                            echo '<option data-file="'.$report[0].'" value="?type=staff&report='.$key.'" '.($_GET['report'] == $key ? 'selected' : '').'>'.$report[1].'</option>';
                        }
                    }
                }
                else { ?>
                    <option selected value="report_tiles.php">Please Select a Tab to view the Reports</option><?php
                } ?>
        </select>
    </div><div class="clearfix"></div>
    <?php include('../Reports/'.$report_list[$_GET['report']][0]);
}