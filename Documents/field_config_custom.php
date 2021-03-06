<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
    checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
    checkAuthorised('documents_all');
}
include_once('document_settings.php');

if (isset($_POST['submit_custom_'.$_GET['settings']])) {
    $tab_name = $_POST['tab_name'];
    $custom_documents = implode(',',$_POST['custom_documents']);
    $custom_documents_dashboard = implode(',',$_POST['custom_documents_dashboard']);

    if (strpos(','.$custom_documents.',',','.'Custom Documents Type,Category,Title'.',') === false) {
        $custom_documents = 'Custom Documents Type,Category,Title,'.$custom_documents;
    }
    if (strpos(','.$custom_documents_dashboard.',',','.'Custom Documents Type,Category,Title'.',') === false) {
        $custom_documents_dashboard = 'Custom Documents Type,Category,Title,'.$custom_documents_dashboard;
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config_custom_documents WHERE tab_name = '$tab_name'"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config_custom_documents` SET `fields` = '$custom_documents', `dashboard` = '$custom_documents_dashboard' WHERE `tab_name` = '$tab_name'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_custom_documents` (`tab_name`, `fields`, `dashboard`) VALUES ('$tab_name', '$custom_documents', '$custom_documents_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("?tile_name='.$tile_name.'&settings='.$_GET['settings'].'"); </script>';

}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="tab_name" value="<?= $_GET['settings'] ?>">
	<h3>Fields</h3>
	<?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `fields` FROM `field_config_custom_documents` WHERE `tab_name` = '".$_GET['settings']."'"));
    $value_config = ','.$get_field_config['fields'].',';
    ?>

    <table class='table table-bordered'>
        <tr>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Custom Documents Type".',') !== FALSE) { echo " checked"; } ?> value="Custom Documents Type" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Document Type
            </td>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Category
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Heading
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Cost
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Description
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Quote Description
            </td>

        </tr>

        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Final Retail Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Admin Price
            </td>
            <td>
               <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Wholesale Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Commercial Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Price".',') !== FALSE) { echo " checked"; } ?> value="Custom Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Minimum Billable
            </td>
        </tr>

        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Estimated Hours
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Actual Hours
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;MSRP
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Documents Code".',') !== FALSE) {
                echo " checked"; } ?> value="Custom Documents Code" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Documents Code
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Invoice Description
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
            </td>
        </tr>

        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) {
                echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Name
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Fee
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Unit Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Unit Cost
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Rent Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Rental Days
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Rental Weeks
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Rental Months
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Rental Years
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Reminder/Alert
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Daily
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Weekly
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Monthly
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Annually
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;#Of Days
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;#Of Hours
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;#Of Kilometers
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;#Of Miles
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Title".',') !== FALSE) { echo " checked"; } ?> value="Title" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Title
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Uploader".',') !== FALSE) { echo " checked"; } ?> value="Uploader" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Uploader
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Link".',') !== FALSE) { echo " checked"; } ?> value="Link" style="height: 20px; width: 20px;" name="custom_documents[]">&nbsp;&nbsp;Link
            </td>
        </tr>
    </table>

    <h3>Dashboard Fields</h3>
    <?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `dashboard` FROM `field_config_custom_documents` WHERE `tab_name` = '".$_GET['settings']."'"));
    $value_config = ','.$get_field_config['dashboard'].',';
    ?>

    <table class='table table-bordered'>
        <tr>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Custom Documents Type".',') !== FALSE) { echo " checked"; } ?> value="Custom Documents Type" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Document Type
            </td>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Category
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Heading
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Cost
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Description
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Quote Description
            </td>

        </tr>

        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Final Retail Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Admin Price
            </td>
            <td>
               <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Wholesale Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Commercial Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Price".',') !== FALSE) { echo " checked"; } ?> value="Custom Price" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Price
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Minimum Billable
            </td>
        </tr>

        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Estimated Hours
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Actual Hours
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;MSRP
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Documents Code".',') !== FALSE) {
                echo " checked"; } ?> value="Custom Documents Code" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Documents Code
            </td>

            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Invoice Description
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) {
                echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Name
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Fee
            </td>
            <td>
                <input type="checkbox" disabled <?php if (strpos($value_config, ','."Title".',') !== FALSE) { echo " checked"; } ?> value="Title" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Title
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Uploader".',') !== FALSE) { echo " checked"; } ?> value="Uploader" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Uploader
            </td>
            <td>
                <input type="checkbox" <?php if (strpos($value_config, ','."Link".',') !== FALSE) { echo " checked"; } ?> value="Link" style="height: 20px; width: 20px;" name="custom_documents_dashboard[]">&nbsp;&nbsp;Link
            </td>
        </tr>
    </table>

	<div class="form-group">
	    <div class="col-sm-6">
	        <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your Document settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	        <a href="?tile_name=<?= $tab_name ?>&tab=<?= $_GET['settings'] ?>" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
	        <button	type="submit" name="submit_custom_<?= $_GET['settings'] ?>" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your Document settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	    </div>
	</div>

</form>