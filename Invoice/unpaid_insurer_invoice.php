<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');

if (isset($_POST['printpdf'])) {
    include_once ('print_unpaid_invoice.php');
}
?>
<script type="text/javascript" src="invoice.js"></script>
</head>
<body>
<?php include_once ('../navigation.php');
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
?>
<div class="container triple-pad-bottom">
    <div class="row">
        <h2><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>
        <?php
            echo '<a href="field_config_invoice.php" class="btn mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        ?>
        </h2>

		<?php $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
		?><div class='mobile-100-container'><?php
		foreach($tab_list as $tab_name) {
			if(check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', ROLE, $tab_name) === TRUE) {
				switch($tab_name) {
					case 'sell':
						if(in_array('touch',$ux_options)) { ?>
							<a href='add_invoice.php' class="btn brand-btn mobile-block mobile-100">Create Invoice (Keyboard)</a>
							<a href='touch_main.php' class="btn brand-btn mobile-block mobile-100">Create Invoice (Touchscreen)</a>
						<?php } else { ?>
							<a href='add_invoice.php' class="btn brand-btn mobile-block mobile-100">Create Invoice</a>
						<?php }
						break;
					case 'today': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Invoices created today."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='today_invoice.php' class="btn brand-btn mobile-block mobile-100">Today's Invoices</a>
						<?php break;
					case 'all': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Complete history of all Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='all_invoice.php' class="btn brand-btn mobile-block mobile-100">All Invoices</a>
						<?php break;
					case 'invoices': ?>
						<a href='invoice_list.php' class="btn brand-btn mobile-block mobile-100">Invoices</a>
						<?php break;
					case 'unpaid': ?>
						<a href='unpaid_invoice_list.php' class="btn brand-btn mobile-block mobile-100">Accounts Receivable</a>
						<?php break;
					case 'voided': ?>
						<a href='void_invoices.php' class="btn brand-btn mobile-block mobile-100">Voided Invoices</a>
						<?php break;
					case 'refunds': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Find invoices in order to issue Refunds or Create Adjustment Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='refund_invoices.php' class="btn brand-btn mobile-block mobile-100">Refund / Adjustments</a>
						<?php break;
					case 'ui_report': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="In this section you can create Invoices for insurers."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='unpaid_insurer_invoice.php' class="btn brand-btn mobile-block mobile-100 active_tab">Unpaid Insurer Invoice Report</a>
						<?php break;
					case 'cashout': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Daily front desk Cashout."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='cashout.php' class="btn brand-btn mobile-block mobile-100">Cash Out</a>
						<?php break;
                            case 'gf': ?>
                                <a href='giftcards.php' class="btn brand-btn mobile-block mobile-100">Gift Card</a>
                                <?php break;
				}
			}
		}
		?></div>
        <br><br>

        <form name="invoice" method="post" action="" class="form-inline" role="form" style="overflow-x:visible;overflow-y:visible;">

		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			Generate Unpaid Insurer reports from here. Select the customer's name from the drop down menu, then select the insurer from the drop down menu to display all unpaid insurer invoices related to that customer and insurer.</div>
			<div class="clearfix"></div>
		</div>

            <?php
            if(isset($_POST['search_user_submit'])) {
                $search_user = $_POST['search_user'];
            } else {
                $search_user = '';
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_user = '';
			}
            ?>
            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label">Search by Customer:</label>
              <div class="col-sm-8">
                  <select data-placeholder="Select a Customer" name="search_user" id="search_user" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice WHERE paid = 'Waiting on Insurer' AND final_price IS NOT NULL AND serviceid IS NOT NULL AND serviceid != ','");
                    while($row = mysqli_fetch_array($query)) {
                    ?><option <?php if ($row['patientid'] == $search_user) { echo " selected"; } ?> value='<?php echo  $row['patientid']; ?>' ><?php echo get_contact($dbc, $row['patientid']); ?></option>
                <?php	}
                ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="patientpdf" value="<?php echo $search_user; ?>">

            <div class="form-group">
                <button type="submit" name="search_user_submit" id="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                <!--<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>-->
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select an insurer from the dropdown, then click on the checkbox at the end of each row to select the item(s) you wish to print before you click the Print button."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
                <button type="submit" name="printpdf" id="printpdf" value="Print Report" class="btn brand-btn pull-right">Print</button>
            </div>

            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label">Select Insurer:</label>
              <div class="col-sm-8">
                <select data-placeholder="Select an Insurer" id="insurerid" name="insurerid" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $insurerid = get_all_form_contact($dbc, $search_user, 'insurerid');

                    $parts = explode(',', $insurerid);
                    echo '<option value=""></option>';
                    foreach ($parts as $key) {
                        if($key != '') {
                            echo "<option value='". $key."'>".get_all_form_contact($dbc, $key, 'name').'</option>';
                        }
                    }
                    ?>
                </select>
              </div>
            </div>

            <br><br>

            <?php
            if($search_user != '') {
                $query_check_credentials = "SELECT `invoice`.* FROM invoice LEFT JOIN `invoice` isrc ON `invoice`.`invoiceid_src`=isrc.`invoiceid` WHERE `invoice`.patientid='$search_user' AND `invoice`.paid IN ('Waiting on Insurer','No') AND `invoice`.final_price IS NOT NULL AND `invoice`.serviceid IS NOT NULL AND `invoice`.serviceid != ',' ORDER BY IFNULL(isrc.`service_date`,`invoice`.`service_date`), `invoice`.`invoiceid`";

                $result = mysqli_query($dbc, $query_check_credentials);

                $serviceid = 0;
                $j = 0;
                $final_total = 0;
                $gst_sub = 0;
                $gst_grand = 0;
                $grand_total = 0;
                $sub_total = 0;
                $invoice_service = ',';
                $total_qty = 0;
                while($row = mysqli_fetch_array($result))
                {
                    if($serviceid != $row['serviceid']) {
                        if($j != 0) {
                            echo "<tr>";
                            echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                            echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                            echo "</tr>";
                            echo '</table>';

                            $final_total = 0;
                            $sub_total = 0;
                            $gst_sub = 0;
                            $total_qty = 0;
                        }
                        echo "<br><br><table border='1' cellpadding='10' class='table' style='margin:0px; width:100%;'>";
                        echo "<tr>"; ?>
						<th style='width:4%;'>Invoice#</th>
                        <th style='width:10%;'>Date</th>
                        <th style='width:35%;'>Service : Fee</th>
                        <th style='width:20%;'>Professional</th>
                        <th style='width:3%;'>Qty</th>
                        <th style='width:7%;'>Sub Total</th>
                        <th style='width:5%;'>GST</th>
                        <th style='width:10%;'>Invoice Total</th>
                        <th style='width:5%;'><span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the checkbox at the end of each row to select the item(s) you wish to print, then click the Print button."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span></th>
                        </tr><?php
                        $serviceid = $row['serviceid'];
                    }

                    $patientid = $row['patientid'];

                    echo '<tr>';
                    echo '<td>#' . $row['invoiceid'] .($row['invoiceid_src'] > 0 ? '<br />'.$row['invoice_type'].' for Invoice #'.$row['invoiceid_src'] : ''). '</td>';
                    echo '<td>' . $row['service_date'] . '</td>';

                    echo '<td>';
                    $parts = explode(',', $row['serviceid']);
                    $parts_ff = explode(',', $row['fee']);
                    $m = 0;
                    $total_service = 0;
                    foreach ($parts as $key) {
                        if($key != '') {
                            echo get_all_from_service($dbc, $key, 'service_code').' : '.get_all_from_service($dbc, $key, 'heading'). ' : $'.$parts_ff[$m].'<br>';
                            $gst_exempt = get_all_from_service($dbc, $key, 'gst_exempt');
                            $total_service += $parts_ff[$m];
                        }
                        $m++;
                    }
                    echo '</td>';

                    echo '<td>' . get_contact($dbc, $row['therapistsid']).'('.get_all_form_contact($dbc, $row['therapistsid'], 'license').')'. '</td>';
                    echo '<td>1</td>';

                    echo '<td>$' . $total_service . '</td>';

                    if($gst_exempt == 1) {
                        //echo '<td>$0</td>';
                    } else {
                        //echo '<td>$'.number_format((float)($total_service*0.05), 2, '.', '').'</td>';
                        $gst_sub += ($total_service*0.05);
                    }
                    echo '<td></td>';

                    /*if($row['final_price'] != $row['total_price']) {
                        echo '<td>$'.($row['final_price']-$row['total_price']).'</td>';
                    } else {
                        echo '<td>-</td>';
                    }
                    */

 //                 echo '<td>$' . ($row['final_price']) . '</td>';
                    //echo '<td>$' . number_format((float)($total_service+$gst_sub), 2, '.', '') .
                    //'</td>';
                    echo '<td></td>';

                    echo '<td><input type="checkbox" style="width: 20px; height: 20px;" value="'.$row['invoiceid'].'" name="invoice_checked[]"></td>';

                    //$final_total += $row['final_price'];
                    $final_total += ($total_service+$gst_sub);
                    $sub_total += $total_service;
                    //$gst_sub += ($row['final_price']-$row['total_price']);
                    $grand_total += $row['final_price'];
                    $total_qty++;

                    echo "</tr>";
                    $j++;
                    $invoice_service .= $row['invoiceid'].',';
                }

                $query_check_credentials = "SELECT * FROM invoice WHERE patientid='$search_user' AND paid = 'Waiting on Insurer' AND final_price IS NOT NULL AND inventoryid IS NOT NULL AND inventoryid != ',' ORDER BY inventoryid";

                $result = mysqli_query($dbc, $query_check_credentials);

                $inventoryid = 0;
                $m = 0;
                while($row = mysqli_fetch_array($result))
                {
                    if($inventoryid != $row['inventoryid']) {
                        if($m == 0) {
                            echo "<tr>";
                            echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                            echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                            echo "</tr>";
                            echo '</table>';
                        }
                        echo "<br><br><table border='1' cellpadding='10' class='table' style='margin:0px; width:100%;'>";
                        ?><tr>
						<th style='width:4%;'>Invoice#</th>
                        <th style='width:10%;'>Date</th>
                        <th style='width:35%;'>Inventory : Sell Price</th>
                        <th style='width:20%;'>Inventory</th>
                        <th style='width:3%;'>Qty</th>
                        <th style='width:7%;'>Sub Total</th>
                        <th style='width:5%;'>GST</th>
                        <th style='width:10%;'>Invoice Total</th>
                        <th style='width:5%;'><span class="popover-examples list-inline">
								<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the checkbox at the end of each row to select the item(s) you wish to print, then click the Print button."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
							</span></th></th>
                        </tr><?php
                        $inventoryid = $row['inventoryid'];
                    }

                    $patientid = $row['patientid'];

                    echo '<tr>';
                    echo '<td>' . $row['invoiceid'] .($row['invoiceid_src'] > 0 ? ' ('.$row['invoice_type'].' for #'.$row['invoiceid_src'].')' : ''). '</td>';
                    echo '<td>' . $row['service_date'] . '</td>';

                    echo '<td>';
                    $parts1 = explode(',', $row['inventoryid']);
                    $invtype = explode(',', $row['invtype']);
                    $sell_price = explode(',', $row['sell_price']);
                    $k = 0;
                    $total_inv = 0;
                    foreach ($parts1 as $key1) {
                        if($key1 != '') {
                            echo $invtype[$k].' : '.get_all_from_inventory($dbc, $key1 , 'name'). ' : $'.$sell_price[$k].'<br>';

                            $total_inv += $sell_price[$k];
                        }
                        $k++;
                    }

                    echo '</td>';
                    echo '<td>Nose Creek</td>';
                    echo '<td>1</td>';
                    echo '<td>$' . number_format((float)$total_inv, 2, '.', '') . '</td>';

                    echo '<td>$'.number_format((float)($total_inv*0.05), 2, '.', '').'</td>';

                    //echo '<td>$' . ($row['final_price']) . '</td>';
                    echo '<td>$' . number_format((float)($total_inv + ($total_inv*0.05)), 2, '.', '') . '</td>';

                    echo '<td>';
                    if (strpos($invoice_service, ','.$row['invoiceid'].',') !== FALSE) {
                        echo '-';
                    } else {
                        echo '<input type="checkbox" style="width: 20px; height: 20px;" value="'.$row['invoiceid'].'" name="invoice_checked[]">';
                        $grand_total += $row['final_price'];
                    }
                    echo '</td>';

                    echo "</tr>";
                    $m++;
                }
                if($m == 0) {
                    echo "<tr>";
                    echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                    echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                    echo "</tr>";
                    echo '</table>';
                }
                echo "<table border='2' cellpadding='10' class='table' style='margin:0px;'>";
                echo "<tr>";
                echo "<th>Grand Total : $".number_format((float)$grand_total, 2, '.', '')."</th>";
                echo "</tr>";
                echo '</table>';
            }
            ?>

        
        </form>

	</div>
	<a href="<?= WEBSITE_URL ?>/home.php" class="btn brand-btn">Back</a>
</div>
<?php include ('../footer.php'); ?>
