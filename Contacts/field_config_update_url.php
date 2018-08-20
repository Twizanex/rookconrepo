<?php error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).on('change', 'select[name="category[]"]', function() { filterCategories(); });
function filterCategories() {
	$('[name="contacts[]"] option').hide();
	$('[name="category[]"] option:selected').each(function() {
		$('[name="contacts[]"] option[data-category="'+this.value+'"]').show();
	});
	$('[name="contacts[]"] option[value="ALL_CONTACTS"]').show();

	$('[name="contacts[]"]').trigger('change.select2');
}
function updatePreview() {
	var body = $('[name="body"]').val();
	var expiry_date = $('[name="expiry_date"]').val();
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=update_url_get_preview',
		method: 'POST',
		data: { body: body, expiry_date: expiry_date },
		dataType: 'html',
		success: function(response) {
			$('.email_preview').html(response);
		}
	});
}
function sendEmails(btn) {
	if(confirm("Are you sure you want to send emails?")) {
		$(btn).prop('disabled', true);
		$(btn).text('Sending...');
		var categories = [];
		$('[name="category[]"] option:selected').each(function() {
			categories.push(this.value);
		});
		var contacts = [];
		$('[name="contacts[]"] option:selected').each(function() {
			contacts.push(this.value);
		});
		var security_level = $('[name="security_level"]').val();
		var expiry_date = $('[name="expiry_date"]').val();
		var subject = $('[name="subject"]').val();
		var body = $('[name="body"]').val();

		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=update_url_send_email',
			method: 'POST',
			data: { folder_name: '<?= FOLDER_NAME ?>', categories: categories, contacts: contacts, security_level: security_level, expiry_date: expiry_date, subject: subject, body: body },
			success: function(response) {
				alert(response);
				window.location.reload();
			}
		});
	}
}
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Update Profile Email:</h3>
</div>
<div class="standard-dashboard-body-content" style="height: 100%;">
    <div class="dashboard-item dashboard-item2 full-height">
        <div class="form-horizontal block-group block-group-noborder">
			<div class="notice double-gap-bottom popover-examples">
			    <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
			    <div class="col-sm-11">
			        <span class="notice-name">NOTE:</span>
			        This will send an email to all attached Contacts with a unique URL so the user can update their profile without needing a login account. Only Contacts with Emails will appear in the Contacts dropdown. Make sure to choose a Security Level that has edit or view access to the Contacts tile or they won't be able to access it.
			    </div>
			    <div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Categories:</label>
				<div class="col-sm-8">
					<select name="category[]" multiple class="chosen-select-deselect">
						<option></option>
						<?php $tabs = explode(',',get_config($dbc, FOLDER_NAME.'_tabs'));
						$staff = array_search('Staff',$tabs);
			            if($staff !== FALSE) {
			                unset($tabs[$staff]);
			            }
			            foreach($tabs as $tab) {
			            	echo '<option value="'.$tab.'">'.$tab.'</option>';
			            } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Contacts:</label>
				<div class="col-sm-8">
					<select name="contacts[]" multiple class="chosen-select-deselect">
						<option></option>
						<option value="ALL_CONTACTS">All Contacts</option>
						<?php $contacts = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE IFNULL(`email_address`,'') != '' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1 AND `category` IN ('".implode("','", $tabs)."')"));
						foreach($contacts as $contact) {
							echo '<option value="'.$contact['contactid'].'" data-category="'.$contact['category'].'">'.$contact['full_name'].'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a href="#" data-toggle="tooltip" data-placement="top" title="Set the Security Level that the Unique URL will use."><img src="../img/info.png" width="20"></a></span> Security Level:</label>
				<div class="col-sm-8">
					<select name="security_level" class="chosen-select-deselect">
						<option></option>
						<?php $on_security = get_security_levels($dbc);
						foreach($on_security as $security_name => $value) {
							echo '<option value="'.$value.'">'.$security_name.'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a href="#" data-toggle="tooltip" data-placement="top" title="Set the date the URL will expire and die. Leave blank to allow the URL to be accessed indefinitely."><img src="../img/info.png" width="20"></a></span> URL Expiration Date:</label>
				<div class="col-sm-8">
					<input type="text" name="expiry_date" class="form-control datepicker" onchange="updatePreview();">
				</div>
			</div>
			<?php $subject = "Please update your profile.";
			$body = "Hi [FULL_NAME].<br /><br />You are receiving this email as a reminder to verify your profile details and update your profile in ".get_config($dbc, 'company_name').". You have until [EXPIRY_DATE] to access your profile link."; ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" name="subject" class="form-control" value="<?= $subject ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body:</label>
				<div class="col-sm-8">
					<textarea name="body" class="form-control" onchange="updatePreview();"><?= $body ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Preview:</label>
				<div class="col-sm-8 email_preview">
					Hi <?= get_contact($dbc, $_SESSION['contactid']) ?>.<br /><br />You are receiving this email as a reminder to verify your profile details and update your profile in <?= get_config($dbc, 'company_name') ?>.You have until <?= date('Y-m-d') ?> to access your profile link.<br /><br />Click <a href="?">here</a> to access your profile.
				</div>
			</div>
			<button type="button" onclick="sendEmails(this); return false;" class="btn brand-btn pull-right">Send Emails</button>
        </div>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->