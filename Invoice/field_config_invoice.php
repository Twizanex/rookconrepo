<?php
/*
Dashboard
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
error_reporting(0);
$invoice_ux = FOLDER_NAME.'_ux';

if (isset($_POST['submit'])) {

    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_logo'"));
    if($get_config['configid'] > 0) {
		if($logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='invoice_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_logo', '$logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo

    //Design
    $invoice_tabs = implode(',',$_POST['invoice_tabs']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_tabs' WHERE name='invoice_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_tabs', '$invoice_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Design

	//Interface
    $invoice_ux_opt = implode(',',$_POST[$invoice_ux]);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='$invoice_ux'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_ux_opt' WHERE name='$invoice_ux'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('$invoice_ux', '$invoice_ux_opt')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
	//Interface

    //Design
    $invoice_design = $_POST['invoice_design'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_design'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_design' WHERE name='invoice_design'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_design', '$invoice_design')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Design

    //Fields
    $invoice_fields = implode(',',$_POST['invoice_fields']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_fields'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_fields' WHERE name='invoice_fields'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_fields', '$invoice_fields')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Fields

    //Payment Types
    $invoice_payment_types = implode(',',$_POST['invoice_payment_types']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_payment_types'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_payment_types' WHERE name='invoice_payment_types'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_payment_types', '$invoice_payment_types')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Payment Types

    //Purchasing Contacts
    $invoice_purchase_contact = implode(',',$_POST['invoice_purchase_contact']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_purchase_contact'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_purchase_contact' WHERE name='invoice_purchase_contact'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_purchase_contact', '$invoice_purchase_contact')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Purchasing Contacts

    //Third Party Contacts
    $invoice_payer_contact = implode(',',$_POST['invoice_payer_contact']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_payer_contact'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_payer_contact' WHERE name='invoice_payer_contact'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_payer_contact', '$invoice_payer_contact')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Third Party Contacts

    //Tax
    $invoice_tax = '';
    for($i = 0; $i < count($_POST['invoice_tax_name']); $i++) {
        if($_POST['invoice_tax_name'][$i] != '') {
            $invoice_tax .= filter_var($_POST['invoice_tax_name'][$i],FILTER_SANITIZE_STRING).'**'.$_POST['invoice_tax_rate'][$i].'**'.$_POST['invoice_tax_number'][$i].'**'.$_POST['invoice_tax_exemption_'.$i].'*#*';
        }
    }

    $invoice_tax = rtrim($invoice_tax, "*#*'");
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_tax'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_tax' WHERE name='invoice_tax'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_tax', '$invoice_tax')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //MVA Claim Max $ for Inventory
    $mva_claim_price = filter_var($_POST['mva_claim_price'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='mva_claim_price'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$mva_claim_price' WHERE name='mva_claim_price'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('mva_claim_price', '$mva_claim_price')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    mysqli_query($dbc, "ALTER TABLE `patient_injury` CHANGE `mva_claim_price` `mva_claim_price` DECIMAL(10,2) NOT NULL DEFAULT '$mva_claim_price'");
    //MVA Claim Max $ for Inventory

    //Promotion
    $enable_promotion = filter_var($_POST['enable_promotion'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='enable_promotion'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$enable_promotion' WHERE name='enable_promotion'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('enable_promotion', '$enable_promotion')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Promotion

    //Header & Footer
    $invoice_header = filter_var(htmlentities($_POST['invoice_header']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_header'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_header' WHERE name='invoice_header'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_header', '$invoice_header')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $invoice_footer = filter_var(htmlentities($_POST['invoice_footer']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_footer' WHERE name='invoice_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_footer', '$invoice_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $invoice_unpaid_footer = filter_var(htmlentities($_POST['invoice_unpaid_footer']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_unpaid_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_unpaid_footer' WHERE name='invoice_unpaid_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_unpaid_footer', '$invoice_unpaid_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Header & Footer

    echo '<script type="text/javascript"> window.location.replace("field_config_invoice.php"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $('#add_tax_button').on( 'click', function () {
        var clone = $('.additional_tax').clone();

        var numItems = $('.tax_exemption_div').length;
        clone.find('.form-control').val('');
        clone.find('.rate').val('0');
        clone.removeClass("additional_tax");
        $('#add_here_new_tax').append(clone);
        return false;
    });
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Payment Tile Settings</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="invoice_main.php" class="btn config-btn">Back to Dashboard</a></div>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <div class="panel-group" id="accordion">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_tabs" >
                        Tile Tabs<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_tabs" class="panel-collapse collapse">
                <div class="panel-body">
					<h3>Select Tabs to Display</h3>
					<?php $tab_list = explode(',', get_config($dbc, 'invoice_tabs')); ?>
					<label class="form-checkbox"><input <?= (in_array('sell',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="sell"> Create Invoice</label>
					<label class="form-checkbox"><input <?= (in_array('today',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="today"> Today's Invoices</label>
					<label class="form-checkbox"><input <?= (in_array('all',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="all"> All Invoices</label>
					<label class="form-checkbox"><input <?= (in_array('invoices',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="invoices"> Invoices</label>
					<label class="form-checkbox"><input <?= (in_array('unpaid',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="unpaid"> Accounts Receivable</label>
					<label class="form-checkbox"><input <?= (in_array('voided',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="voided"> Voided Invoices</label>
					<label class="form-checkbox"><input <?= (in_array('refunds',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="refunds"> Refund / Adjustments</label>
					<label class="form-checkbox"><input <?= (in_array('ui_report',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="ui_report"> Unpaid Insurer Invoice Report</label>
					<label class="form-checkbox"><input <?= (in_array('cashout',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="cashout"> Cash Out</label>
					<label class="form-checkbox"><input <?= (in_array('gf',$tab_list) ? 'checked' : '') ?> type="checkbox" name="invoice_tabs[]" value="gf"> Gift Card</label>
					<h3>Select Interface Styles</h3>
					<?php $ux_fields = array_filter(explode(',',get_config($dbc, $invoice_ux))); ?>
					<label class="form-checkbox"><input <?= (count($ux_fields) == 0 || in_array('standard',$ux_fields) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_ux ?>[]" value="standard"> Standard Interface</label>
					<label class="form-checkbox"><input <?= (in_array('touch',$ux_fields) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_ux ?>[]" value="touch"> Touch Interface</label>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_pdf" >
                        PDF Settings<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_pdf" class="panel-collapse collapse">
                <div class="panel-body">

					<?php $invoice_design = get_config($dbc, 'invoice_design'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Select Invoice Design:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == '1') { echo 'checked'; } ?> type="radio" name="invoice_design" value="1">
								Layout 1<br /><a target="_blank" href="../img/invoice_design1.png"><img src="../img/invoice_design1.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == '2') { echo 'checked'; } ?> type="radio" name="invoice_design" value="2">
								Layout 2<br /><a target="_blank" href="../img/invoice_design2.png"><img src="../img/invoice_design2.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == '3') { echo 'checked'; } ?> type="radio" name="invoice_design" value="3">
								Layout 3<br /><a target="_blank" href="../img/invoice_design3.png"><img src="../img/invoice_design3.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == '4') { echo 'checked'; } ?> type="radio" name="invoice_design" value="4">
								Layout 4<br /><a target="_blank" href="../img/invoice_design4.png"><img src="../img/invoice_design4.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == 'service') { echo 'checked'; } ?> type="radio" name="invoice_design" value="service">
								Service Record<br /><a target="_blank" href="../img/invoice_design_service.png"><img src="../img/invoice_design_service.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == '5') { echo 'checked'; } ?> type="radio" name="invoice_design" value="5">
								Miniature<br /><a target="_blank" href="../img/invoice_design_small.png"><img src="../img/invoice_design_small.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == 'pink') { echo 'checked'; } ?> type="radio" name="invoice_design" value="pink">
								Pink<br /><a target="_blank" href="../img/invoice_design_pink.png"><img src="../img/invoice_design_pink.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == 'cnt1') { echo 'checked'; } ?> type="radio" name="invoice_design" value="cnt1">
								Contractor Design 1<br /><a target="_blank" href="../img/invoice_contractor1.png"><img src="../img/invoice_contractor1.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == 'cnt2') { echo 'checked'; } ?> type="radio" name="invoice_design" value="cnt2">
								Contractor Design 2<br /><a target="_blank" href="../img/invoice_contractor2.png"><img src="../img/invoice_contractor2.png" width="100" height="100" border="0" alt=""></a></label>
							<label class="form-checkbox"><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($invoice_design == 'cnt3') { echo 'checked'; } ?> type="radio" name="invoice_design" value="cnt3">
								Contractor Design 3<br /><a target="_blank" href="../img/invoice_contractor3.png"><img src="../img/invoice_contractor3.png" width="100" height="100" border="0" alt=""></a></label>
						</div>
					</div>

                    <?php $logo = get_config($dbc, 'invoice_logo'); ?>
                    <div class="form-group">
                    <label for="file[]" class="col-sm-4 control-label">Upload Logo:
                    <span class="popover-examples list-inline">&nbsp;
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                    </span>
                    :</label>
                    <div class="col-sm-8">
                    <?php if($logo != '') {
                        echo '<a href="download/'.$logo.'" target="_blank">View</a>';
                        ?>
                        <input type="hidden" name="logo_file" value="<?php echo $logo; ?>" />
                        <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
                      <?php } else { ?>
                      <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
                      <?php } ?>
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Header:</label>
                    <div class="col-sm-8">
                        <textarea name="invoice_header" rows="5" cols="50" class="form-control"><?php echo get_config($dbc, 'invoice_header'); ?></textarea>
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Footer for Customer and Third Party Invoices:</label>
                    <div class="col-sm-8">
                        <textarea name="invoice_footer" rows="5" cols="50" class="form-control"><?php echo get_config($dbc, 'invoice_footer'); ?></textarea>
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Footer for Unpaid Third Party Invoices:</label>
                    <div class="col-sm-8">
                        <textarea name="invoice_unpaid_footer" rows="5" cols="50" class="form-control"><?php echo get_config($dbc, 'invoice_unpaid_footer'); ?></textarea>
                    </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_tax" >
                        Set Tax Names & Rates<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_tax" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    $value_config = get_config($dbc, 'invoice_tax');
                    ?>

                    <div class="form-group clearfix">
                        <label class="col-sm-2 text-center">Name</label>
                        <label class="col-sm-2 text-center">Rate(%)<br><em>add a numeric value, not a %</em></label>
                        <label class="col-sm-2 text-center">Tax Number</label>
                    </div>

                    <?php
                    $invoice_tax = explode('*#*',$value_config);

                    $total_count = mb_substr_count($value_config,'*#*');
                    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                        $invoice_tax_name_rate = explode('**',$invoice_tax[$eq_loop]);
                    ?>
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                          <div class="col-sm-2">
                                <input name="invoice_tax_name[]" value="<?php echo $invoice_tax_name_rate[0];?>" type="text" class="form-control quantity" />
                            </div>
                            <div class="col-sm-2">
                                <input name="invoice_tax_rate[]" value="<?php echo $invoice_tax_name_rate[1]; ?>" type="text" class="form-control category" />
                            </div>
                            <div class="col-sm-2">
                                <input name="invoice_tax_number[]" value="<?php echo $invoice_tax_name_rate[2]; ?>" type="text" class="form-control category" />
                            </div>
                        </div>
                    <?php } ?>

                    <div class="additional_tax">
                    <div class="clearfix"></div>
                    <div class="form-group clearfix" width="100%">
                        <div class="col-sm-2">
                            <input name="invoice_tax_name[]" type="text" class="form-control price" />
                        </div>
                        <div class="col-sm-2">
                            <input name="invoice_tax_rate[]" value="0" type="text" class="form-control rate" />
                        </div>
                        <div class="col-sm-2">
                            <input name="invoice_tax_number[]" type="text" class="form-control" />
                        </div>
                    </div>

                    </div>

                    <div id="add_here_new_tax"></div>

                    <div class="col-sm-8 col-sm-offset-4 triple-gap-bottom">
                        <button id="add_tax_button" class="btn brand-btn mobile-block">Add</button>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_survey" >
                        MVA Claim Max $ for Inventory<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_survey" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label"><h4>MVA Claim Max $ for Inventory</h4></label>
                    <div class="col-sm-8">
                      <input name="mva_claim_price" value="<?php echo get_config($dbc, 'mva_claim_price'); ?>" type="text" class="form-control">
                    </div>
                    </div>

                    <div class="form-group double-gap-top">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse_dashboard" >
						Choose Fields for Dashboards<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_dashboard" class="panel-collapse collapse">
				<div class="panel-body" id="no-more-tables">
					<?php $value_config = ','.get_config($dbc,'invoice_dashboard').','; ?>

					<table border='2' cellpadding='10' class='table'>
						<tr>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."invoiceid".',') !== FALSE) { echo " checked"; } ?> value="invoiceid" name="invoice_dashboard[]">&nbsp;&nbsp;Invoice #
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."invoice_date".',') !== FALSE) { echo " checked"; } ?> value="invoice_date" name="invoice_dashboard[]">&nbsp;&nbsp;Invoice Date
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."customer".',') !== FALSE) { echo " checked"; } ?> value="customer" name="invoice_dashboard[]">&nbsp;&nbsp;Customer
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."total_price".',') !== FALSE) { echo " checked"; } ?> value="total_price" name="invoice_dashboard[]">&nbsp;&nbsp;Total Price
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."payment_type".',') !== FALSE) { echo " checked"; } ?> value="payment_type" name="invoice_dashboard[]">&nbsp;&nbsp;Payment Type
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) { echo " checked"; } ?> value="invoice_pdf" name="invoice_dashboard[]">&nbsp;&nbsp;Invoice PDF
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."comment".',') !== FALSE) { echo " checked"; } ?> value="comment" name="invoice_dashboard[]">&nbsp;&nbsp;Comment
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."status".',') !== FALSE) { echo " checked"; } ?> value="status" name="invoice_dashboard[]">&nbsp;&nbsp;Status
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."send") !== FALSE) { echo " checked"; } ?> value="send" name="invoice_dashboard[]">&nbsp;&nbsp;Send
							</td>
							<td>
								<input type="checkbox" <?php if (strpos($value_config, ','."delivery".',') !== FALSE) { echo " checked"; } ?> value="delivery" name="invoice_dashboard[]">&nbsp;&nbsp;Delivery/Shipping Type
							</td>

						</tr>
					</table>
				</div>
			</div>
		</div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_invoice_fields" >
                        Choose Fields for Invoices<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_invoice_fields" class="panel-collapse collapse">
                <div class="panel-body">
					<?php $invoice_fields = explode(',',get_config($dbc, 'invoice_fields')); ?>
					<label class="form-checkbox"><input <?= (in_array('invoice_type',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="invoice_type"> Invoice Type</label>
					<label class="form-checkbox"><input <?= (in_array('customer',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="customer"> Customer</label>
					<label class="form-checkbox"><input <?= (in_array('injury',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="injury"> Injury</label>
					<label class="form-checkbox"><input <?= (in_array('staff',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="staff"> Staff (Providing Service)</label>
					<label class="form-checkbox"><input <?= (in_array('appt_type',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="appt_type"> Appointment Type</label>
					<label class="form-checkbox"><input <?= (in_array('treatment',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="treatment"> Treatment Plan</label>
					<label class="form-checkbox"><input <?= (in_array('service_date',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_date"> Service Date</label>
					<label class="form-checkbox"><input <?= (in_array('invoice_date',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="invoice_date"> Invoice Date</label>
					<label class="form-checkbox"><input <?= (in_array('pay_mode',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="pay_mode"> Payment Method</label>
					<label class="form-checkbox"><input <?= (in_array('pricing',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="pricing"> Pricing</label>
					<label class="form-checkbox"><input <?= (in_array('price_client',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_client"> Pricing - Client Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_admin',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_admin"> Pricing - Admin Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_commercial',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_commercial"> Pricing - Commercial Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_wholesale',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_wholesale"> Pricing - Wholesale Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_retail',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_retail"> Pricing - Final Retail Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_preferred',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_preferred"> Pricing - Preferred Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_po',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_po"> Pricing - Purchase Order Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_sales',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_sales"> Pricing - <?= SALES_ORDER_NOUN ?> Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_web',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_web"> Pricing - Web Price</label>
					<label class="form-checkbox"><input <?= (in_array('price_distributor',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="price_distributor"> Pricing - Distributor Price</label>
					<label class="form-checkbox"><input <?= (in_array('send_invoice',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="send_invoice"> Send Outbound Invoice</label>
					<label class="form-checkbox"><input <?= (in_array('discount',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="discount"> Discount</label>
					<label class="form-checkbox"><input <?= (in_array('coupon',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="coupon"> Coupon</label>
					<label class="form-checkbox"><input <?= (in_array('delivery',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="delivery"> Delivery / Pickup</label>
					<label class="form-checkbox"><input <?= (in_array('assembly',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="assembly"> Assembly</label>
					<label class="form-checkbox"><input <?= (in_array('comment',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="comment"> Comment</label>
					<label class="form-checkbox"><input <?= (in_array('tax_exempt',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="tax_exempt"> Tax Exemption</label>
					<label class="form-checkbox"><input <?= (in_array('created_by',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="created_by"> Created / Sold By</label>
					<label class="form-checkbox"><input <?= (in_array('ship_date',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="ship_date"> Ship Date</label>
					<label class="form-checkbox"><input <?= (in_array('services',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="services"> Services</label>
					<label class="form-checkbox"><input <?= (in_array('service_cat',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_cat"> Service - Category</label>
					<label class="form-checkbox"><input <?= (in_array('service_head',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_head"> Service - Heading</label>
					<label class="form-checkbox"><input <?= (in_array('service_price',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_price"> Service - Price</label>
					<label class="form-checkbox"><input <?= (in_array('service_qty',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_qty"> Service - Quantity</label>
					<label class="form-checkbox"><input <?= (in_array('products',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="products"> Products</label>
					<label class="form-checkbox"><input <?= (in_array('product_cat',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="product_cat"> Product - Category</label>
					<label class="form-checkbox"><input <?= (in_array('product_head',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="product_head"> Product - Heading</label>
					<label class="form-checkbox"><input <?= (in_array('product_price',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="product_price"> Product - Price</label>
					<label class="form-checkbox"><input <?= (in_array('product_qty',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="product_qty"> Product - Quantity</label>
					<label class="form-checkbox"><input <?= (in_array('inventory',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="inventory"> Inventory</label>
					<label class="form-checkbox"><input <?= (in_array('inventory_cat',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="inventory_cat"> Inventory - Category</label>
					<label class="form-checkbox"><input <?= (in_array('inventory_part',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="inventory_part"> Inventory - Part #</label>
					<label class="form-checkbox"><input <?= (in_array('inventory_type',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="inventory_type"> Inventory - Type</label>
					<label class="form-checkbox"><input <?= (in_array('inventory_price',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="inventory_price"> Inventory - Price</label>
					<label class="form-checkbox"><input <?= (in_array('packages',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="packages"> Packages</label>
					<label class="form-checkbox"><input <?= (in_array('packages_cat',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="packages_cat"> Packages - Category</label>
					<label class="form-checkbox"><input <?= (in_array('packages_name',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="packages_name"> Packages - Name</label>
					<label class="form-checkbox"><input <?= (in_array('packages_fee',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="packages_fee"> Packages - Fee</label>
					<label class="form-checkbox"><input <?= (in_array('misc_items',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="misc_items"> Misc Items</label>
					<label class="form-checkbox"><input <?= (in_array('deposit_paid',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="deposit_paid"> Deposit Paid</label>
					<label class="form-checkbox"><input <?= (in_array('due_date',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="due_date"> Due Date</label>
					<label class="form-checkbox"><input <?= (in_array('service_queue',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="service_queue"> Service Queue</label>
					<label class="form-checkbox"><input <?= (in_array('promo',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="promo"> Promotion</label>
					<label class="form-checkbox"><input <?= (in_array('tips',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="tips"> Gratuity</label>
					<label class="form-checkbox"><input <?= (in_array('next_appt',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="next_appt"> Next Appointment</label>
					<label class="form-checkbox"><input <?= (in_array('survey',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="survey"> Send Survey</label>
					<label class="form-checkbox"><input <?= (in_array('request_recommend',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="request_recommend"> Request Recommendation Report</label>
					<label class="form-checkbox"><input <?= (in_array('followup',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="followup"> Send Follow Up Email</label>
                    <label class="form-checkbox"><input <?= (in_array('giftcard',$invoice_fields) ? 'checked' : '') ?> type="checkbox" name="invoice_fields[]" value="giftcard"> Gift Card</label>

                    <div class="form-group double-gap-top">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse_pay" >
					   Payment Type Options<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_pay" class="panel-collapse collapse">
				<div class="panel-body">

					<div class="form-group">
						<label for="office_country" class="col-sm-4 control-label">Payment Type Options:<br><em>(separate by a comma)</em></label>
						<div class="col-sm-8">
							<?php $invoice_payment_types = explode(',',get_config($dbc, 'invoice_payment_types')); ?>
							<label class="form-checkbox"><input <?= (in_array('Pay Now',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="Pay Now"> Pay Now</label>
							<label class="form-checkbox"><input <?= (in_array('Net 30',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="Net 30"> Net 30</label>
							<label class="form-checkbox"><input <?= (in_array('Net 60',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="Net 60"> Net 60</label>
							<label class="form-checkbox"><input <?= (in_array('Net 90',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="Net 90"> Net 90</label>
							<label class="form-checkbox"><input <?= (in_array('Net 120',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="Net 120"> Net 120</label>
							<label class="form-checkbox"><input <?= (in_array('On Account',$invoice_payment_types) ? 'checked' : '') ?> type="checkbox" name="invoice_payment_types[]" value="On Account"> On Account</label>
							<?php $invoice_payment_types = trim(str_replace(['Pay Now','Net 30','Net 60','Net 90','Net 120','On Account',',,,,,',',,,,',',,,',',,'], ',', implode(',',$invoice_payment_types)),','); ?>
						  <input name="invoice_payment_types[]" value="<?php echo $invoice_payment_types; ?>" type="text" class="form-control"/>
						</div>
					</div>

                    <div class="form-group double-gap-top">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

				</div>
			</div>
		</div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_categories" >
                        Invoice Contact Categories<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_categories" class="panel-collapse collapse">
                <div class="panel-body">
					<?php $category_list = explode(',',get_config($dbc, 'contacts_tabs')); ?>
					<h3>Purchasing Contact Categories</h3>
					<?php $invoice_purchase_contact = explode(',',get_config($dbc, 'invoice_purchase_contact'));
					foreach($category_list as $contact_category) { ?>
						<label class="form-checkbox"><input <?= (in_array($contact_category,$invoice_purchase_contact) ? 'checked' : '') ?> type="checkbox" name="invoice_purchase_contact[]" value="<?= $contact_category ?>"> <?= $contact_category ?></label>
					<?php } ?>
					<h3>Third Party Payer Categories</h3>
					<?php $invoice_payer_contact = explode(',',get_config($dbc, 'invoice_payer_contact'));
					foreach($category_list as $contact_category) { ?>
						<label class="form-checkbox"><input <?= (in_array($contact_category,$invoice_payer_contact) ? 'checked' : '') ?> type="checkbox" name="invoice_payer_contact[]" value="<?= $contact_category ?>"> <?= $contact_category ?></label>
					<?php } ?>

                    <div class="form-group double-gap-top">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_pro" >
                        Enable Promotion<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_pro" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (get_config($dbc, 'enable_promotion') == 1) { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="enable_promotion">&nbsp;&nbsp;Enable Promotion

                    <div class="form-group double-gap-top">
                        <div class="col-sm-6">
                            <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
                        </div>
                        <div class="col-sm-6">
                            <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="today_invoice.php" class="btn config-btn btn-lg">Back</a>
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>
