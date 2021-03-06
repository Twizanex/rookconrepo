<?php include('../include.php');
include('../Calendar/calendar_functions_inc.php');
if($_POST['submit'] == 'start_day') {
	$time = time();
	$date = date('Y-m-d');
	$day_of_week = date('l', strtotime($date));
	$hour = date('H:i');
	$comment = filter_var($_POST['comment_start'],FILTER_SANITIZE_STRING);
	$day_tracking_type = filter_var($_POST['day_tracking_type'],FILTER_SANITIZE_STRING);
	foreach($_POST['staff_start'] as $key => $staff) {
		if($staff > 0) {
			$clientid = $_POST['client_start'][$key] > 0 ? $_POST['client_start'][$key] : 0;
			$shifts = checkShiftIntervals($dbc, $staff, $day_of_week, $date);
			$timesheet_track_shifts = get_config($dbc, 'timesheet_track_shifts');
			if($timesheet_track_shifts == '1' && !empty($shifts)) {
				$shift_tracked = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `time_cards` WHERE `staff` = '$staff' AND `date` = '$date' AND `shift_tracked` = 1 AND `deleted` = 0"))['num_rows'];
				if($shift_tracked == 0) {
					$total_hrs = 0;
					$max_time = strtotime($shifts[0]['starttime']);
					foreach ($shifts as $shift) {
						$start_time = strtotime($shift['starttime']);
						$end_time = strtotime($shift['endtime']);
						if($max_time > $start_time) {
							$start_time = $max_time;
						} else {
							$max_time = $start_time;
						}
						if($max_time > $end_time) {
							$end_time = $max_time;
						} else {
							$max_time = $end_time;
						}
						$total_hrs += ($end_time - $start_time);
					}
					$total_hrs = $total_hrs/3600;
					if($day_tracking_type != '') {
						$day_tracking_type = $shift['hours_type'];
					}
				} else {
					$total_hrs = '';
				}
				mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `total_hrs`, `shift_tracked`, `comment_box`, `day_tracking_type`, `clientid`) SELECT '$staff', '$date', '$hour', 'day_tracking', '$time', '$total_hrs', '1', '$comment', '$day_tracking_type', '$clientid' FROM (SELECT COUNT(*) `rows` FROM `time_cards` WHERE `staff`='$staff' AND `deleted`=0 AND `timer_start` > 0 AND `type_of_time`='day_tracking') `num` WHERE `num`.`rows`=0)");
				if($clientid > 0) {
					mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `total_hrs`, `shift_tracked`, `comment_box`, `day_tracking_type`, `created_by`) VALUES ('$clientid', '$date', '$hour', 'day_tracking', '$time', '$total_hrs', '1', '$comment', '$day_tracking_type', '$staff')");
				}
			} else {
				mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `comment_box`, `day_tracking_type`, `clientid`) SELECT '$staff', '$date', '$hour', 'day_tracking', '$time', '$comment', '$day_tracking_type', '$clientid' FROM (SELECT COUNT(*) `rows` FROM `time_cards` WHERE `staff`='$staff' AND `deleted`=0 AND `timer_start` > 0 AND `type_of_time`='day_tracking') `num` WHERE `num`.`rows`=0");
				if($clientid > 0) {
					mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `comment_box`, `day_tracking_type`, `created_by`) VALUES ('$clientid', '$date', '$hour', 'day_tracking', '$time', '$comment', '$day_tracking_type', '$staff')");
				}
			}
		}
	}
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 518400]);
		$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
	}
	$_SESSION['tile_list_updated'] = 0;
	session_write_close();
	echo "<script> window.location.replace('".WEBSITE_URL."/home.php'); </script>";
} else if($_POST['submit'] == 'end_day' || $_POST['submit'] == 'day_break' || $_POST['submit'] == 'day_resume' || $_POST['submit'] == 'end_break') {
	$time = time();
	$date = date('Y-m-d');
	$hour = date('H:i');
	$comment = filter_var($_POST['comment_end'],FILTER_SANITIZE_STRING);
	$time_minimum = get_config($dbc, 'ticket_min_hours');
	$time_interval = get_config($dbc, 'timesheet_hour_intervals');
	foreach($_POST['staff_end'] as $key => $staff) {
		if($staff > 0) {
			$all_contacts = [$staff];
			$clientid = filter_var($_POST['client_end'][$key],FILTER_SANITIZE_STRING);
			if($clientid > 0) {
				$all_contacts[] = $clientid;
			}
			foreach ($all_contacts as $staff) {
				mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($time - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($time - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$hour' WHERE `staff`='$staff'AND `type_of_time` != 'day_tracking' AND `timer_start` > 0");

				//Check Out of Tickets/Work Orders
				$all_attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Staff_Tasks' AND `item_id` = '".$staff."' AND `arrived` = 1 AND `completed` = 0 AND `deleted` = 0");
				while($attached = mysqli_fetch_array($all_attached)) {
					$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total_hrs`) FROM `time_cards` WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'"))[0];
					mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`='$hours' WHERE `id`='".$attached['id']."'");
					mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."' WHERE `id`='".$attached['id']."'");
					mysqli_query($dbc, "UPDATE `ticket_attached` SET `completed`=1 WHERE `id`='".$attached['id']."'");
				}

				//End Paused Day Tracking
		    $date_of_archival = date('Y-m-d');
			$dbc->query("UPDATE `time_cards` SET `day_tracking_type`='Unresumed Day Tracking', `deleted`=1, `date_of_archival` = '$date_of_archival', `timer_start`=0 WHERE `staff`='$staff' AND `deleted`=0 AND `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` LIKE 'Work:%'");

				//End Day
				$shifts = checkShiftIntervals($dbc, $staff, $day_of_week, $date);
				$timesheet_track_shifts = get_config($dbc, 'timesheet_track_shifts');
				$tracking_id = $dbc->query("SELECT MAX(`time_cards_id`) `id`, MAX(`timer_start`) `timer` FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff'")->fetch_assoc();
				$timer_start = date('Y-m-d H:i', $tracking_id['timer']);
				$tracking_id = $tracking_id['id'];
				if(!empty($shifts) && $timesheet_track_shifts == '1' && $tracking_id['id'] > 0) {
					$total = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total_hrs`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$date $hour' AND `staff`='$staff' AND `shift_tracked` = 0"));
					$total_tracked = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`timer_tracked`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$date $hour' AND `staff`='$staff' AND `shift_tracked` = 1"));
					$hours = ($total['hours'] + $total_tracked['hours']) * 1;
					$minimum = ($time_minimum > $hours ? $time_minimum - $hours : 0);
					$seconds = ($time > $total['timer'] + ($hours * 3600) ? $time +($hours * 3600) : $total['timer']);
					mysqli_query($dbc, "UPDATE `time_cards` SET `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$hour', `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'$comment') WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff'");
				} else if($tracking_id['id'] > 0) {
					$total = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total_hrs`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$timer_start' AND `staff`='$staff' AND `type_of_time` != 'day_tracking'"));
					$seconds = $time - ($total['hours'] * 3600);
					$hours = $total['hours'] * 1;
					$minimum = ($time_minimum > $hours ? $time_minimum - $hours : 0);
					// $seconds = ($time > $total['timer'] + ($hours * 3600) ? $time +($hours * 3600) : $total['timer']);
					mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$minimum'), `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$hour', `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'$comment') WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff'");
				}
			}

			// Go on Break or Resume from Break
			if($_POST['submit'] == 'day_break') {
				$dbc->query("INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `day_tracking_type`) VALUES ('$staff','$date','$hour','day_tracking','$time','Break:$tracking_id')");
			} else if($_POST['submit'] == 'day_resume') {
				$dbc->query("INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `day_tracking_type`) SELECT `staff`, '$date', '$hour', 'day_tracking', '$time', `type_of_time` FROM `time_cards` WHERE CONCAT('Break:',`time_cards_id`) IN (SELECT `day_tracking_type` FROM `time_cards` WHERE `time_cards_id`='$tracking_id')");
				$dbc->query("UPDATE `time_cards` SET `type_of_time`='Break', `comment_box`=CONCAT('Break for ',`total_hrs`,' hours') WHERE `type_of_time` LIKE 'Break%' AND `time_cards_id`='$tracking_id'");
			} else if($_POST['submit'] == 'end_break') {
				$dbc->query("UPDATE `time_cards` SET `type_of_time`='Break' WHERE `type_of_time` LIKE 'Break%' AND `time_cards_id`='$tracking_id'");
			}
		}
	}
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 518400]);
		$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
	}
	$_SESSION['tile_list_updated'] = 0;
	session_write_close();
	echo "<script> window.location.replace('".WEBSITE_URL."/home.php'); </script>";
}
include('../navigation.php');
$group_list = explode('#*#',get_config($dbc, 'ticket_groups'));
$timesheet_hide_others = get_config($dbc, 'timesheet_hide_others');
$timesheet_add_day_comment = get_config($dbc, 'timesheet_add_day_comment');
if($timesheet_hide_others == '1') {
	$hide_others_query = " AND `contactid` = '".$_SESSION['contactid']."'";
} else if($timesheet_hide_others == '2') {
	$group_ids = [];
	foreach($group_list as $group) {
		$group = array_filter(explode(',',$group),'is_id');
		if(in_array($_SESSION['contactid'],$group)) {
			$group_ids = array_merge($group_ids, $group);
		}
	}
	if(count($group_ids) > 0) {
		$hide_others_query = " AND `contactid` IN (".implode(',',$group_ids).")";
	}
}
$timesheet_direct_indirect = get_config($dbc, 'timesheet_direct_indirect');
$timesheet_hide_groups = get_config($dbc, 'timesheet_hide_groups');
$timesheet_track_clients = get_config($dbc, 'timesheet_track_clients');
$timesheet_client_category = get_config($dbc, 'timesheet_client_category');
$client_query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '$timesheet_client_category' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
$client_list = [];
foreach ($client_query as $client) {
	$client_list[$client] = (!empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client));
} ?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="staff_start[]"]').change(function() { displayClient(this); });
	$('[name="staff_start[]"]').each(function() { displayClient(this); });
	$('[name="staff_end[]"]').change(function() { disableClient(this); });
	$('[name="staff_end[]"]').each(function() { disableClient(this); });
});
function displayClient(chk) {
	if($(chk).is(':checked')) {
		$(chk).closest('.staff_block').find('.client_block').show();
		$(chk).closest('.staff_block').find('.client_block select[name="client_start[]"]').prop('disabled', false);
	} else {
		$(chk).closest('.staff_block').find('.client_block').hide();
		$(chk).closest('.staff_block').find('.client_block select[name="client_start[]"]').prop('disabled', true);
	}
}
function disableClient(chk) {
	if($(chk).is(':checked')) {
		$(chk).closest('.staff_block_end').find('.client_block_end').show();
		$(chk).closest('.staff_block_end').find('.client_block_end select[name="client_end[]"]').prop('disabled', false);
	} else {
		$(chk).closest('.staff_block_end').find('.client_block_end').hide();
		$(chk).closest('.staff_block_end').find('.client_block_end select[name="client_end[]"]').prop('disabled', true);
	}
}
</script>
<div class="container">
	<div class="row">
		<?php $security = get_security($dbc, 'timesheet');
        if($security['config'] > 0) {
            echo '<a href="field_config.php?tab=day_tracking&from_url=start_day.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        } ?>
		<form class="form-horizontal" action="" method="POST">
			<?php $timer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timer_start`, `type_of_time`, `day_tracking_type` FROM `time_cards` WHERE `type_of_time` IN ('day_tracking') AND `timer_start` > 0 AND `deleted`=0 AND `staff`='".$_SESSION['contactid']."'")); ?>
			<?php for($block = 0; $block < 2; $block++) {
				if(($block == 0 && !($timer['timer_start'] > 0)) || ($block == 1 && $timer['timer_start'] > 0)) {
					$start_time_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `finished` FROM `contacts` LEFT JOIN (SELECT MAX(CONCAT(`date`,' ',`end_time`)) `finished`, `staff` FROM `time_cards` GROUP BY `staff`) `timers` ON `contacts`.`contactid`=`timers`.`staff` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0 AND `contactid` NOT IN (SELECT `staff` FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `deleted`=0 AND `staff` > 0) $hide_others_query"));
					if(count($start_time_list) > 0) { ?>
						<h1><?= get_config($dbc, 'timesheet_start_tile') ?></h1>
						<?php if($timesheet_hide_others == 0 && $timesheet_hide_groups != 1) { ?>
							<div class="form-group">
								<?php foreach(explode('#*#',get_config($dbc, 'ticket_groups')) as $group) {
									$group = explode(',',$group);
									if(count($group) > 1) { ?>
										<button class="btn brand-btn" onclick="<?php foreach($group as $i => $staff) {
											if($staff > 0) {
												echo "$('[name^=staff_start][value=".$staff."]').prop('checked',true).change();";
											}
										} ?>return false;"><?= $group[0] ?></button>
									<?php }
								} ?>
							</div>
						<?php } ?>
						<?php foreach($start_time_list as $staff) { ?>
							<div class="staff_block col-xs-12 col-sm-6 col-md-4 col-lg-2">
								<label class="form-checkbox"><input type="checkbox" name="staff_start[]" <?= $_SESSION['contactid'] == $staff['contactid'] ? 'checked' : '' ?> value="<?= $staff['contactid'] ?>"> <?= $staff['first_name'].' '.$staff['last_name'].(empty($staff['finished']) ? '' : ' <small>(Signed Out Since '.(!empty($staff['finished']) ? date('Y-m-d g:i A',strtotime($staff['finished'])) : ' N/A').')</small>') ?></label>
								<?php if($timesheet_track_clients == 1) { ?>
									<div class="client_block" style="display: none;">
										<select name="client_start[]" class="chosen-select-deselect form-control" data-placeholder="Select a Client...">
											<option></option>
											<?php foreach ($client_list as $clientid => $client_label) {
												echo '<option value="'.$clientid.'">'.$client_label.'</option>';
											} ?>
										</select>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
						<div class="clearfix"></div>
						<?php if($timesheet_add_day_comment == 1) { ?>
							<div class="form-group">
								<label class="col-sm-4">Time Comment:</label>
								<div class="col-sm-8">
									<input type="text" name="comment_start" class="form-control">
								</div>
							</div>
						<?php } else if($timesheet_add_day_comment == 2) { ?>
							<input type="hidden" name="comment_start" class="form-control" value="<?= get_config($dbc, 'day_tracking_preset_note') ?>">
						<?php } else if($timesheet_add_day_comment == 3) { ?>
							<div class="form-group">
								<label class="col-sm-4">Task:</label>
								<div class="col-sm-8">
									<?php $groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
									if($groups->num_rows > 0) { ?>
										<select name="comment_start" data-placeholder="Select Task" class="chosen-select-deselect"><option />
											<?php while($task_group = $groups->fetch_assoc()) { ?>
												<optgroup label="<?= $task_group['category'] ?>">
													<?php $task_names = $dbc->query("SELECT `id`, `description` FROM `task_types` WHERE `deleted`=0 AND `category`='{$task_group['category']}' ORDER BY `sort`, `id`");
													while($task_name = $task_names->fetch_assoc()) { ?>
														<option value="<?= $task_name['description'] ?>"><?= $task_name['description'] ?></option>
													<?php } ?>
												</optgroup>
											<?php } ?>
										</select>
									<?php } else { ?>
										<input type="text" name="comment_start" class="form-control">
									<?php } ?>
								</div>
							</div>
						<?php } ?>
						<?php if($timesheet_direct_indirect == 2) { ?>
							<div class="form-group">
								<label class="col-sm-4">Position:</label>
								<div class="col-sm-8">
									<select name="day_tracking_type" class="chosen-select-deselect form-control" data-placeholder="Select Position...">
										<option></option>
										<?php $positions = $dbc->query("SELECT `name` FROM `positions` WHERE `deleted`=0 ORDER BY `name`");
										while($position = $positions->fetch_assoc()) {
											echo '<option value="'.$position['name'].'">'.$position['name'].'</option>';
										} ?>
									</select>
								</div>
							</div>
						<?php } else if($timesheet_direct_indirect == 1) { ?>
							<div class="form-group gap-top">
								<label class="col-sm-12">Tracking Type:</label>
								<div class="col-sm-12">
									<input type="radio" name="day_tracking_type" value="Direct Hrs." checked /> Direct Hours
									<input type="radio" name="day_tracking_type" value="Indirect Hrs." /> Indirect Hours
								</div>
							</div>
						<?php } ?>
						<button type="submit" name="submit" value="start_day" class="btn brand-btn pull-left">Start Tracking Time <?= $timesheet_hide_others == 1 ? '' : 'for Staff' ?></button>
					<?php }
				} else {
					$end_time_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name`, MAX(CONCAT(`time_cards`.`date`,' ',`time_cards`.`start_time`)) `started` FROM `contacts` LEFT JOIN `time_cards` ON `contacts`.`contactid` = `time_cards`.`staff` WHERE `contacts`.`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND `timer_start` > 0 AND `time_cards`.`deleted`=0 AND `time_cards`.`staff` > 0 AND `type_of_time`='day_tracking' $hide_others_query GROUP BY `contacts`.`contactid`"));
					if(count($end_time_list) > 0) { ?>
						<h1><?= get_config($dbc, 'timesheet_end_tile') ?></h1>
						<?php if($timesheet_hide_others == 0 && $timesheet_hide_groups != 1) { ?>
							<div class="form-group">
								<?php foreach($group_list as $group) {
									$group = explode(',',$group);
									if(count($group) > 1) { ?>
										<button class="btn brand-btn" onclick="<?php foreach($group as $i => $staff) {
											if($staff > 0) {
												echo "$('[name^=staff_end][value=".$staff."]').prop('checked',true).change();";
											}
										} ?>return false;"><?= $group[0] ?></button>
									<?php }
								} ?>
							</div>
						<?php } ?>
						<?php foreach($end_time_list as $staff) { ?>
							<div class="staff_block_end col-xs-12 col-sm-6 col-md-4 col-lg-2">
								<label class="form-checkbox"><input type="checkbox" name="staff_end[]" <?= $_SESSION['contactid'] == $staff['contactid'] ? 'checked' : '' ?> value="<?= $staff['contactid'] ?>"> <?= $staff['first_name'].' '.$staff['last_name'].(empty($staff['started']) ? '' : ' <small>(Signed In Since '.$staff['started'].')</small>') ?></label>
								<?php if($timesheet_track_clients == 1) {
									$clientid = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff` = '".$staff['contactid']."'"))['clientid']; ?>
									<div class="client_block_end" style="pointer-events: none; opacity:0.5; <?= $clientid > 0 ? '' : 'display: none;' ?>">
										<select name="client_end[]" class="chosen-select-deselect form-control" readonly>
											<option value="<?= $clientid ?>" selected><?= (!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid)) ?></option>
										</select>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
						<div class="clearfix"></div>
						<?php if($timesheet_add_day_comment == 1) { ?>
							<div class="form-group">
								<label class="col-sm-4">Time Comment:</label>
								<div class="col-sm-8">
									<input type="text" name="comment_end" class="form-control">
								</div>
							</div>
						<?php }
						if(get_config($dbc, 'timesheet_break_option') > 0) {
							if(substr($timer['day_tracking_type'],0,5) == 'Break') { ?>
								<button type="submit" name="submit" value="day_resume" class="btn brand-btn pull-left">Resume Tracking <?= $timesheet_hide_others == 1 ? '' : 'for Staff' ?></button>
							<?php } else { ?>
								<button type="submit" name="submit" value="day_break" class="btn brand-btn pull-left">Go on Break <?= $timesheet_hide_others == 1 ? '' : 'for Staff' ?></button>
							<?php } ?>
						<?php }
						if(substr($timer['day_tracking_type'],0,5) == 'Break') { ?>
							<button type="submit" name="submit" value="end_break" class="btn brand-btn pull-left">Finish Tracking Time <?= $timesheet_hide_others == 1 ? '' : 'for Staff' ?></button>
						<?php } else { ?>
							<button type="submit" name="submit" value="end_day" class="btn brand-btn pull-left">Finish Tracking Time <?= $timesheet_hide_others == 1 ? '' : 'for Staff' ?></button>
						<?php } ?>
					<?php }
				} ?>
				<div class="clearfix"></div>
			<?php } ?>
		</form>
	</div>
</div>
<?php include('../footer.php'); ?>
