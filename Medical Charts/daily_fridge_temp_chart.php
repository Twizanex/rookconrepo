<?php
$value_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daily_fridge_temp` FROM `field_config`"));
$value_config = ','.$value_config['daily_fridge_temp'].',';
$charts_time_format = get_config($dbc, 'charts_time_format');

$date = date('Y-m-d');
if(!empty($_GET['fridge_record_choosedate'])) {
	$date = date('Y-m-d', strtotime($_GET['fridge_record_choosedate']));
} else if(!empty($_POST['fridge_record_choosedate'])) {
	$date = date('Y-m-d', strtotime($_POST['fridge_record_choosedate']));
} ?>
<script type="text/javascript">
$(window).on('load', function() {
	<?php if(isset($_GET['subtab']) && $_GET['subtab'] == 'Fridge Temp Chart') { ?>
		$('#nav_daily_fridge_temp').trigger('click');
	<?php } ?>
});
$(document).on('change', 'select[name="fridge_record_staff"]', function() { saveFieldFridgeTempRecord(this); });
$(document).on('change', 'select[name="fridge_record_fridge"]', function() { saveFieldFridgeTempRecord(this); });
function saveFieldFridgeTempRecord(field) {
	var row = $(field).closest('tr.fridge_record_row');
	var contactid = $(field).closest('table.fridge_record_table').data('contact');
	var id = $(row).attr('data-id');
	var date = $(row).find('[name="fridge_record_date"]').val();
	var time = $(row).find('[name="fridge_record_time"]').val();
	var fridge = $(row).find('[name="fridge_record_fridge"]').val();
	var temp = $(row).find('[name="fridge_record_temp"]').val();
	var note = $(row).find('[name="fridge_record_note"]').val();
	var staff = $(row).find('[name="fridge_record_staff"]').val();
	var history = $(row).find('[name="fridge_record_history"]').val();
	var data = { contactid: contactid, id: id, date: date, time: time, fridge: fridge, temp: temp, note: note, staff: staff, history: history };
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=daily_fridge_temp',
		type: 'POST',
		data: data,
		success: function(response) {
			if (id == 0) {
				$(row).removeAttr('data-id').attr('data-id', response);
			}
		}
	});
}
function addFridgeTempRecord() {
	destroyInputs('table.fridge_record_table:visible');
	var block = $('table.fridge_record_table:visible').find('tr.fridge_record_row').last();
	var clone = $(block).clone();

	clone.find('.form-control').val('');
    resetChosen(clone.find('select'));
    clone.find('[name="fridge_record_date"]').val($('#fridge_record_today').val());
 //    clone.find('[name="fridge_record_date"]').attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().datepicker({
	// 	changeMonth: true,
	// 	changeYear: true,
	// 	yearRange: '1920:2025',
	// 	dateFormat: 'yy-mm-dd',
	// });
 //    clone.find('[name="fridge_record_time"]').attr("id", "").removeClass('hasDatepicker').removeData('datetimepicker').unbind().timepicker({
	// 	controlType: 'select',
	// 	oneLine: true,
	// 	timeFormat: 'hh:mm tt'
 //    });
    clone.removeAttr('data-id').attr('data-id', 0);

    block.after(clone);
	initInputs('table.fridge_record_table:visible');
}
function deleteFridgeTempRecord(btn) {
	if($('table.fridge_record_table:visible').find('tr.fridge_record_row').length <= 1) {
		addFridgeTempRecord();
	}

	var id = $(btn).closest('tr.fridge_record_row').data('id');
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=daily_fridge_temp_delete',
		type: 'POST',
		data: { id: id },
		success: function(repsonse) {
			$(btn).closest('tr.fridge_record_row').remove();
		}
	});
}
function exportFridgeTempRecord() {
	var contactid = '<?= $_GET['edit']; ?>';
	var date = $('[name="fridge_record_choosedate"]').val();
	var url = '../Medical Charts/daily_fridge_temp_pdf.php?contactid='+contactid+'&date='+date;

	window.open(url, '_blank');
}
</script>

<input type="hidden" name="contactid" value="<?= $_GET['edit'] ?>">
<div class="form-group">
	<?php if(FOLDER_NAME == 'medical%20charts') { ?>
	<div class="col-sm-5">
	    <label class="col-sm-6 control-label"><span class="pull-right">Program:</span></label>
	    <div class="col-sm-6">
	        <select name="search_businessid" class="form-control chosen-select-deselect">
	        	<option></option>
		        <?php
		            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Business' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
		            foreach ($query as $id) { ?>
		                <option <?= ($id == $_GET['edit'] ? 'selected' : '') ?> value="<?= $id ?>"><?= get_client($dbc, $id) ?></option>
		            <?php }
		            if(!in_array($_GET['edit'],$query) && $_GET['edit'] > 0) { ?>
		            	<option selected value="<?= $_GET['edit'] ?>"><?= get_client($dbc, $_GET['edit']) ?></option>
		            <?php }
		        ?>
	        </select>
	    </div>
	</div>
	<?php } ?>
	<div class="col-sm-4 <?= (FOLDER_NAME == 'medical%20charts' ? 'col-sm-offset-1' : 'col-sm-offset-4') ?>">
		<label class="control-label">Date:</label>
		<input type="text" name="fridge_record_choosedate" value="<?= $date ?>" class="form-control inline datepicker" />
		<button tyep="submit" name="load_fridge_record_date" value="load_fridge_record_date" onclick="$('[name=subtab]').val('Fridge Temp Chart');" class="btn brand-btn mobile-block">Submit</button>
		<input type="hidden" name="edit" value="<?= $_GET['edit'] ?>" />
		<input type="hidden" name="category" value="<?= $_GET['category'] ?>" />
		<input type="hidden" name="subtab" value="Fridge Temp Chart" />
	</div>
	<div class="<?= (FOLDER_NAME == 'medical%20charts' ? 'col-sm-2' : 'col-sm-4') ?>">
		<button name="export_daily_fridge_temp" value="export_daily_fridge_temp" onclick="exportFridgeTempRecord(); return false;" class="btn brand-btn mobile-block pull-right">Export to PDF</button>
	</div>
</div>
<div class="clearfix"></div>

<?php if(!empty($_GET['edit'])) { ?>
	<div class="fridge_record_block" id="no-more-tables" style="overflow-y: auto;">
		<h4><?= date('F Y', strtotime($date)) ?></h4>
		<input type="hidden" id="fridge_record_today" value="<?= date('Y-m-d') ?>">
		<table class="table table-bordered fridge_record_table" data-contact="<?= $_GET['edit'] ?>">
			<tr class="hide-on-mobile">
				<th><div style="min-width: 10em;">Date</div></th>
				<?php if (strpos($value_config, ','."time".',') !== FALSE) { ?>
					<th><div style="min-width: 10em;">Time</div></th>
				<?php } ?>
				<?php if (strpos($value_config, ','."fridge".',') !== FALSE) { ?>
					<th><div style="min-width: 10em;">Fridge</div></th>
				<?php } ?>
				<?php if (strpos($value_config, ','."temp".',') !== FALSE) { ?>
					<th><div style="min-width: 10em;">Temperature</div></th>
				<?php } ?>
				<?php if (strpos($value_config, ','."note".',') !== FALSE) { ?>
					<th><div style="min-width: 10em;">Note</div></th>
				<?php } ?>
				<?php if (strpos($value_config, ','."staff".',') !== FALSE) { ?>
					<th>Staff</th>
				<?php } ?>
				<?php if (strpos($value_config, ','."history".',') !== FALSE) { ?>
					<th><div style="min-width: 10em;">History</div></th>
				<?php } ?>
				<th>Function</th>
			</tr>
			<?php
			$date_start = date('Y-m-01', strtotime($date));
			$date_end = date('Y-m-t', strtotime($date));
			$fridge_record_query = "SELECT * FROM `daily_fridge_temp` WHERE `business`='".$_GET['edit']."' AND `date` BETWEEN '$date_start' AND '$date_end' AND `deleted`=0 ORDER BY `date`, IFNULL(STR_TO_DATE(`time`, '%l:%i %p'),STR_TO_DATE(`time`, '%H:%i')) ASC";
			$fridge_record_result = mysqli_fetch_all(mysqli_query($dbc, $fridge_record_query),MYSQLI_ASSOC);
			if(empty($fridge_record_result)) {
				$fridge_record_result[0]['daily_fridge_temp_id'] = 0;
				$fridge_record_result[0]['date'] = date('Y-m-d');
				$fridge_record_result[0]['time'] = '';
				$fridge_record_result[0]['fridge'] = '';
				$fridge_record_result[0]['temp'] = '';
				$fridge_record_result[0]['note'] = '';
				$fridge_record_result[0]['staff'] = '';
				$fridge_record_result[0]['history'] = '';
			}
			$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0 AND `show_hide_user`=1"),MYSQLI_ASSOC));

			foreach ($fridge_record_result as $chart) {
				$daily_fridge_temp_id = $chart['daily_fridge_temp_id'];
				$fridge_date = $chart['date'];
				$time = $chart['time'];
				if($charts_time_format == '24h') {
					$time = date('H:i', strtotime(date('Y-m-d').' '.$time));
				} else {
					$time = date('h:i a', strtotime(date('Y-m-d').' '.$time));
				}
				$temp = $chart['temp'];
				$fridge_fridge = $chart['fridge'];
				$note = $chart['note'];
				$staff = $chart['staff'];
				$history = $chart['history']; ?>
				<tr class="fridge_record_row" data-id="<?= $daily_fridge_temp_id ?>">
					<td data-title="Date">
						<input type="text" name="fridge_record_date" class="form-control datepicker" value="<?= $fridge_date; ?>" onchange="saveFieldFridgeTempRecord(this);">
					</td>
					<?php if (strpos($value_config, ','."time".',') !== FALSE) { ?>
						<td data-title="Time">
							<input type="text" name="fridge_record_time" class="form-control <?= $charts_time_format == '24h' ? 'datetimepicker-24h' : 'datetimepicker' ?>" value="<?= $time; ?>" onchange="saveFieldFridgeTempRecord(this);">
						</td>
					<?php } ?>
					<?php if (strpos($value_config, ','."fridge".',') !== FALSE) { ?>
						<td data-title="Fridge">
							<select name="fridge_record_fridge" class="form-control chosen-select-deselect">
								<option></option>
								<?php $daily_fridge_temp_fridges = get_config($dbc, 'daily_fridge_temp_fridges');
								    if(empty($daily_fridge_temp_fridges)) {
							            $daily_fridge_temp_fridges = 'Kitchen Fridge,Storage Fridge,Client Fridge';
								    }
							    	$daily_fridge_temp_fridges = explode(',', $daily_fridge_temp_fridges);
							    	foreach ($daily_fridge_temp_fridges as $bus_fridge) {
							    		echo '<option value="'.$bus_fridge.'" '.($bus_fridge == $fridge_fridge ? 'selected' : '').'>'.$bus_fridge.'</option>';
							    	} ?>
							</select>
						</td>
					<?php } ?>
					<?php if (strpos($value_config, ','."temp".',') !== FALSE) { ?>
						<td data-title="Temperature">
							<input type="text" name="fridge_record_temp" class="form-control" value="<?= $temp; ?>" onchange="saveFieldFridgeTempRecord(this);">
						</td>
					<?php } ?>
					<?php if (strpos($value_config, ','."note".',') !== FALSE) { ?>
						<td data-title="Note">
							<input type="text" name="fridge_record_note" class="form-control" value="<?= strip_tags(html_entity_decode($note)); ?>" onchange="saveFieldFridgeTempRecord(this);">
						</td>
					<?php } ?>
					<?php if (strpos($value_config, ','."staff".',') !== FALSE) { ?>
						<td data-title="Staff">
							<select name="fridge_record_staff" class="chosen-select-deselect form-control">
								<option></option>
								<?php foreach($staff_list as $staffid) { ?>
									<option value="<?= $staffid ?>" <?= ($staffid == $staff ? 'selected' : '') ?>><?= get_contact($dbc, $staffid); ?></option>
								<?php } ?>
							</select>
						</td>
					<?php } ?>
					<?php if (strpos($value_config, ','."history".',') !== FALSE) { ?>
						<td data-title="History">
							<input type="text" name="fridge_record_history" class="form-control" value="<?= strip_tags(html_entity_decode($history)); ?>" onchange="saveFieldFridgeTempRecord(this);">
						</td>
					<?php } ?>
					<td data-title="Function">
						<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right hide-on-mobile" onclick="addFridgeTempRecord();">
						<img src="../img/remove.png" class="inline-img pull-right" onclick="deleteFridgeTempRecord(this);">
					</td>
				</tr>
			<?php } ?>
		</table>
		<div class="pull-right">
			<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addFridgeTempRecord();">
		</div>
	</div>
<?php } else {
	echo '<h1>No Program Selected</h1>';
} ?>