<?php
/*
Saleser Listing
*/
include ('../include.php');
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('calllog');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">
		
		<?php
        if(config_visible_function($dbc, 'sales') == 1) {
            echo '<a href="field_config_sales.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
			echo '<span class="popover-examples list-inline pull-right" style="margin:15px 10px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
        }
		?>

        <h1 class="single-pad-bottom">Lead Source</h1>
		<div class="double-gap-bottom"><a href="sales.php" class="btn config-btn">Back to Dashboard</a></div>
		
        <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sales FROM field_config"));
        $value_config = ','.$get_field_config['sales'].',';

        echo "<a href='sales_funnel.php'><button type='button' class='btn brand-btn mobile-block mobile-100'>Sales Funnel</button></a>&nbsp;&nbsp;";
		if (strpos($value_config, ','."Today".',') !== FALSE) {
            echo "<a href='sales.php?type=today'><button type='button' class='btn brand-btn mobile-block '>Today</button></a>&nbsp;&nbsp;";
        }
        if (strpos($value_config, ','."This Week".',') !== FALSE) {
            echo "<a href='sales.php?type=week'><button type='button' class='btn brand-btn mobile-block '>This Week</button></a>&nbsp;&nbsp;";
        }
        if (strpos($value_config, ','."This Month".',') !== FALSE) {
            echo "<a href='sales.php?type=month'><button type='button' class='btn brand-btn mobile-block '>This Month</button></a>&nbsp;&nbsp;";
        }
        if (strpos($value_config, ','."Custom".',') !== FALSE) {
            echo "<a href='sales.php?type=custom'><button type='button' class='btn brand-btn mobile-block '>Custom</button></a>&nbsp;&nbsp;";
        }

        echo "<a href='sales_lead_source_report.php'><button type='button' class='btn brand-btn mobile-block active_tab'>Reports</button></a>&nbsp;&nbsp;";

        echo '<br>';

        echo "<a href='sales_lead_source_report.php?type=custom'><button type='button' class='btn brand-btn mobile-block'>Lead Source Report</button></a>&nbsp;&nbsp;";
        echo "<a href='sales_next_action_report.php'><button type='button' class='btn brand-btn mobile-block'>Next Action Report</button></a>&nbsp;&nbsp;";

        echo "<a href='sales_total_leads.php'><button type='button' class='btn brand-btn mobile-block active_tab'>Leads Added to Pipeline</button></a>&nbsp;&nbsp;";
        echo "<a href='sales_total_won_lost.php'><button type='button' class='btn brand-btn mobile-block'>Total Won/Lost</button></a>&nbsp;&nbsp;";
        echo "<a href='sales_pipeline_review.php'><button type='button' class='btn brand-btn mobile-block'>Pipeline Review</button></a>&nbsp;&nbsp;";
        ?>
        

        <form name="form_sites" method="post" action="" class="form-inline" role="form">
            <div class="pad-top pad-bottom clearfix">
                <?php
                if(vuaed_visible_function($dbc, 'sales') == 1) {
                    echo '<a href="add_sales.php" class="btn brand-btn mobile-block pull-right">Add Sales</a>';
					echo '<span class="popover-examples list-inline pull-right" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Add sales lead details here."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
                }
                ?>
            </div>

           <?php
            $starttime = date('Y-m-d');
            $endtime = date('Y-m-d');
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">From:</label>
                <div class="col-sm-8">
                    <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                </div>
            </div>

            <div class="form-group until">
                <label for="site_name" class="col-sm-4 control-label">Until:</label>
                <div class="col-sm-8" style="width:auto">
                    <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>"></p>
                </div>
            </div>

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <br>

            <div id="no-more-tables">
            <?php

            $query_check_credentials = "SELECT COUNT(salesid) AS total_source, primary_staff FROM sales WHERE (DATE(created_date) >= '".$starttime."' AND DATE(created_date) <= '".$endtime."') GROUP BY primary_staff";

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                echo "<table class='table table-bordered'>";
			    echo "<tr class='hidden-xs hidden-sm'>";
                    echo '<th>Sales Person</th>';
                    echo '<th>Total</th>';
                echo "</tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";
                echo '<td data-title="Lead#">' . get_staff($dbc, $row['primary_staff']) . '</td>';
                echo '<td data-title="Primary Phone">' . $row['total_source'] . '</td>';
                echo "</tr>";
            }

            echo '</table></div>';
            if(vuaed_visible_function($dbc, 'sales') == 1) {
				echo '<a href="add_sales.php" class="btn brand-btn mobile-block pull-right">Add Sales</a>';
				echo '<span class="popover-examples list-inline pull-right" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Add sales lead details here."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>