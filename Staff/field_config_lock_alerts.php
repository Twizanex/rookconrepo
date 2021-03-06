<?php include_once('../include.php');
checkAuthorised('staff'); ?>
<script>
$(document).ready(function() {
	$('input,textarea,select').change(function() {
		var autolock = $('[name=staff_schedule_autolock]').prop('checked') ? '1' : '';
		var autolock_month = $('[name=staff_schedule_autolock_month]').val();
		if(autolock_month == 'custom') {
			var autolock_month = $('[name=staff_schedule_autolock_month_custom]').val();
		}
		var autolock_dayofmonth = $('[name=staff_schedule_autolock_dayofmonth]').val();
		var autolock_numdays = $('[name=staff_schedule_autolock_numdays]').val();

		var reminder_send = $('[name=staff_schedule_reminder_emails]').prop('checked') ? '1' : '';
		var reminder_dates = $('[name="staff_schedule_reminder_dates[]"]').val();
		var reminder_email = $('[name=staff_schedule_reminder_from]').val();
		var reminder_subject = $('[name=staff_schedule_reminder_subject]').val();
		var reminder_body = $('[name=staff_schedule_reminder_body]').val();

		var send = $('[name=staff_schedule_lock_alert_send]').prop('checked') ? 'send' : '';
		var email = $('[name=staff_schedule_lock_alert_from]').val();
		var subject = $('[name=staff_schedule_lock_alert_subject]').val();
		var body = $('[name=staff_schedule_lock_alert_body]').val();
		$.ajax({
			url: 'staff_ajax.php?action=staff_schedule_lock_fields',
			method: 'POST',
			data: {
				staff_schedule_autolock: autolock,
				staff_schedule_autolock_month: autolock_month,
				staff_schedule_autolock_dayofmonth: autolock_dayofmonth,
				staff_schedule_autolock_numdays: autolock_numdays,
				staff_schedule_reminder_emails: reminder_send,
				staff_schedule_reminder_dates: reminder_dates,
				staff_schedule_reminder_from: reminder_email,
				staff_schedule_reminder_subject: reminder_subject,
				staff_schedule_reminder_body: reminder_body,
				staff_schedule_lock_alert_send: send,
				staff_schedule_lock_alert_from: email,
				staff_schedule_lock_alert_subject: subject,
				staff_schedule_lock_alert_body: body
			}
		});
	});
});
$(document).on('change', 'select[name="staff_schedule_autolock_month"]', function() { displayCustomMonth(); });
function displayCustomMonth() {
	var month = $('select[name="staff_schedule_autolock_month"]').val();
	if(month == 'custom') {
		$('[name="staff_schedule_autolock_month_custom"]').show();
	} else {
		$('[name="staff_schedule_autolock_month_custom"]').hide();
	}
}
function displayAutoLock() {
	if($('[name="staff_schedule_autolock"]').is(':checked')) {
		$('#autolock_settings').show();
	} else {
		$('#autolock_settings').hide();
	}
}
function displayAutoLockReminder() {
	if($('[name="staff_schedule_reminder_emails"]').is(':checked')) {
		$('#autolock_reminder_settings').show();
	} else {
		$('#autolock_reminder_settings').hide();
	}
}
function displayLockAlerts() {
	if($('[name="staff_schedule_lock_alert_send"]').is(':checked')) {
		$('#lock_alert_settings').show();
	} else {
		$('#lock_alert_settings').hide();
	}
}
</script>
<h3>Auto-Lock Settings</h3>
<div class="form-group">
	<label class="col-sm-4 control-label">Staff Schedule Auto-Lock:</label>
	<div class="col-sm-8">
		<?php $staff_schedule_autolock = get_config($dbc, 'staff_schedule_autolock'); ?>
		<label class="form-checkbox"><input name="staff_schedule_autolock" type="checkbox" value="1" <?= $staff_schedule_autolock == '1' ? 'checked' : '' ?> onchange="displayAutoLock();"> Enable</label>
	</div>
</div>
<div id="autolock_settings" <?= $staff_schedule_autolock == 1 ? '' : 'style="display:none;"' ?>>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Auto-Lock Day of Month:<br><em>This will auto-lock on this day of each month.</em></label>
		<div class="col-sm-8">
			<select name="staff_schedule_autolock_dayofmonth" class="chosen-select-deselect form-control">
				<?php $staff_schedule_autolock_dayofmonth = get_config($dbc, 'staff_schedule_autolock_dayofmonth');
				for($i = 1; $i <= 31; $i++) {
					echo '<option value="'.$i.'" '.($staff_schedule_autolock_dayofmonth == $i ? 'selected' : '').'>'.$i.'</i>';
				} ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Auto-Lock Month:<br><em>This will auto-lock up to the following month.</em></label>
		<div class="col-sm-8">
			<select name="staff_schedule_autolock_month" class="chosen-select-deselect form-control">
				<?php $staff_schedule_autolock_month = get_config($dbc, 'staff_schedule_autolock_month');
				$staff_schedule_autolock_month = empty($staff_schedule_autolock_month) ? '0' : $staff_schedule_autolock_month;
				echo '<option value="0" '.($staff_schedule_autolock_month == 0 ? 'selected' : '').'>Current Month</option>';
				echo '<option value="1" '.($staff_schedule_autolock_month == 1 ? 'selected' : '').'>Next Month</option>';
				echo '<option value="custom" '.($staff_schedule_autolock_month > 1 ? 'selected' : '').'>Custom Amount (Number of months to skip)</option>';
				?>
			</select>
			<input type="number" name="staff_schedule_autolock_month_custom" class="form-control" value="<?= $staff_schedule_autolock_month ?>" <?= $staff_schedule_autolock_month > 1 ? '' : 'style="display: none;"' ?> onchange="if(parseInt(this.value) < 0) { $(this).val(0); }">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Auto-Lock Number of Days:<br><em>This will lock up to the day specified.</em></label>
		<div class="col-sm-8">
			<select name="staff_schedule_autolock_numdays" class="chosen-select-deselect form-control">
				<?php $staff_schedule_autolock_numdays = get_config($dbc, 'staff_schedule_autolock_numdays');
				for($i = 1; $i <= 31; $i++) {
					echo '<option value="'.$i.'" '.($staff_schedule_autolock_numdays == $i ? 'selected' : '').'>'.$i.'</i>';
				} ?>
			</select>
		</div>
	</div>
</div>
<h3>Auto-Lock Reminder Emails</h3>
<div class="form-group">
	<label class="col-sm-4 control-label">Staff Schedule Reminder Emails:</label>
	<div class="col-sm-8">
		<?php $staff_schedule_reminder_emails = get_config($dbc, 'staff_schedule_reminder_emails'); ?>
		<label class="form-checkbox"><input name="staff_schedule_reminder_emails" type="checkbox" value="1" <?= $staff_schedule_reminder_emails == '1' ? 'checked' : '' ?> onchange="displayAutoLockReminder();"> Enable</label>
	</div>
</div>
<div id="autolock_reminder_settings" <?= $staff_schedule_reminder_emails == 1 ? '' : 'style="display:none;"' ?>>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Reminder Dates:<br><em>This will send a reminder email on this day of the month. Choose multiple to send on multiple days.</em></label>
		<div class="col-sm-8">
			<select name="staff_schedule_reminder_dates[]" multiple class="chosen-select-deselect form-control">
				<?php $staff_schedule_reminder_dates = ','.get_config($dbc, 'staff_schedule_reminder_dates').',';
				for($i = 1; $i <= 31; $i++) {
					echo '<option value="'.$i.'" '.(strpos($staff_schedule_reminder_dates, ','.$i.',') !== FALSE ? 'selected' : '').'>'.$i.'</i>';
				} ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Reminder Sends From:</label>
		<div class="col-sm-8">
			<?php $staff_schedule_reminder_from = get_config($dbc, 'staff_schedule_reminder_from'); ?>
			<input name="staff_schedule_reminder_from" type="text" value="<?= $staff_schedule_reminder_from ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Reminder Subject:</label>
		<div class="col-sm-8">
			<?php $staff_schedule_reminder_subject = get_config($dbc, 'staff_schedule_reminder_subject'); ?>
			<input name="staff_schedule_reminder_subject" type="text" value="<?= $staff_schedule_reminder_subject ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Reminder Body:<br /><em>Use [LOCKDATE] to indicate the day the Staff Schedule gets locked. Use [STARTDATE] to indicate the first day of the locked month. Use [ENDDATE] to indicate the last day of the locked month.</em></label>
		<div class="col-sm-8">
			<?php $staff_schedule_reminder_body = html_entity_decode(get_config($dbc, 'staff_schedule_reminder_body')); ?>
			<textarea name="staff_schedule_reminder_body"><?= $staff_schedule_reminder_body ?></textarea>
		</div>
	</div>
</div>
<h3>Lock Alerts</h3>
<div class="form-group">
	<label class="col-sm-4 control-label">Staff Schedule Lock Triggers Email Alert:</label>
	<div class="col-sm-8">
		<?php $staff_schedule_lock_alert_send = get_config($dbc, 'staff_schedule_lock_alert_send'); ?>
		<label class="form-checkbox"><input name="staff_schedule_lock_alert_send" type="checkbox" value="send" <?= $staff_schedule_lock_alert_send == 'send' ? 'checked' : '' ?> onchange="displayLockAlerts();"> Send</label>
	</div>
</div>
<div id="lock_alert_settings" <?= $staff_schedule_lock_alert_send == 'send' ? '' : 'style="display:none;"' ?>>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Lock Alert Sends From:</label>
		<div class="col-sm-8">
			<?php $staff_schedule_lock_alert_from = get_config($dbc, 'staff_schedule_lock_alert_from'); ?>
			<input name="staff_schedule_lock_alert_from" type="text" value="<?= $staff_schedule_lock_alert_from ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Lock Alert Subject:</label>
		<div class="col-sm-8">
			<?php $staff_schedule_lock_alert_subject = get_config($dbc, 'staff_schedule_lock_alert_subject'); ?>
			<input name="staff_schedule_lock_alert_subject" type="text" value="<?= $staff_schedule_lock_alert_subject ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Schedule Lock Alert Body:<br /><em>Use [DATE] to indicate the date selected to lock.</em></label>
		<div class="col-sm-8">
			<?php $staff_schedule_lock_alert_body = html_entity_decode(get_config($dbc, 'staff_schedule_lock_alert_body')); ?>
			<textarea name="staff_schedule_lock_alert_body"><?= $staff_schedule_lock_alert_body ?></textarea>
		</div>
	</div>
</div>