<?php //Calendar Helper Functions
function checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, $query_type, $clientid) {
	$contact_query = '';
	if (!empty($contact_id)) {
		$contact_query = ' AND `contactid` = '.$contact_id;
	}
	$client_query = '';
	if (!empty($clientid)) {
		$client_query = ' AND `clientid` = '.$clientid;
	}
	if ($query_type == 'daysoff') {
		$all_contacts_daysoff = "SELECT * FROM `contacts_shifts` WHERE `startdate` <= '".date('Y-m-d', strtotime($calendar_date))."' AND `startdate` != '0000-00-00' AND (`enddate` >= '".date('Y-m-d', strtotime($calendar_date))."' OR `enddate`='0000-00-00') AND `deleted` = 0 AND `dayoff_type` != '' AND `dayoff_type` IS NOT NULL AND (CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' OR `repeat_days` = '' OR `repeat_days` IS NULL) AND CONCAT(',',`hide_days`,',') NOT LIKE '%,".$calendar_date.",%'".$contact_query.$client_query." ORDER BY STR_TO_DATE(`starttime`, '%h:%m %p') ASC";
		$shifts_arr = mysqli_fetch_all(mysqli_query($dbc, $all_contacts_daysoff),MYSQLI_ASSOC);
	} else if ($query_type == 'all') {
		$all_contacts_shifts = "SELECT * FROM `contacts_shifts` WHERE `startdate` <= '".date('Y-m-d', strtotime($calendar_date))."' AND `startdate` != '0000-00-00' AND (`enddate` >= '".date('Y-m-d', strtotime($calendar_date))."' OR `enddate`='0000-00-00') AND (CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' OR `repeat_days` = '' OR `repeat_days` IS NULL) AND `deleted` = 0 AND CONCAT(',',`hide_days`,',') NOT LIKE '%,".$calendar_date.",%'".$contact_query.$client_query." ORDER BY STR_TO_DATE(`starttime`, '%h:%m %p') ASC";
		$shifts_arr = mysqli_fetch_all(mysqli_query($dbc, $all_contacts_shifts),MYSQLI_ASSOC);
	} else {
		$all_contacts_shifts = "SELECT * FROM `contacts_shifts` WHERE `startdate` <= '".date('Y-m-d', strtotime($calendar_date))."' AND `startdate` != '0000-00-00' AND (`enddate` >= '".date('Y-m-d', strtotime($calendar_date))."' OR `enddate`='0000-00-00') AND (CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' OR `repeat_days` = '' OR `repeat_days` IS NULL) AND `deleted` = 0 AND (`dayoff_type` = '' OR `dayoff_type` IS NULL) AND CONCAT(',',`hide_days`,',') NOT LIKE '%,".$calendar_date.",%'".$contact_query.$client_query." ORDER BY STR_TO_DATE(`starttime`, '%h:%m %p') ASC";
		$shifts_arr = mysqli_fetch_all(mysqli_query($dbc, $all_contacts_shifts),MYSQLI_ASSOC);
	}

	$shifts = [];

	foreach($shifts_arr as $key => $shift) {
		if($shift['availability'] == 'Available Anytime') {
			$shift['starttime'] = '12:00 AM';
			$shift['endtime'] = '11:59 PM';
		}
		$repeat_type = $shift['repeat_type'];
		switch($repeat_type) {
			case 'weekly':
				$repeat_type = 'W';
				$start_date = date('Y-m-d', strtotime('next Sunday -1 week', strtotime($shift['startdate'])));
				$start_date = new DateTime($start_date);
				$start_date->modify($day_of_week);
				$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 week')));
				break;
			case 'daily':
				$repeat_type = 'D';
				$start_date = date('Y-m-d', strtotime($shift['startdate']));
				$start_date = new DateTime($start_date);
				$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 day')));
				break;
			case 'monthly':
				$repeat_type = 'M';
				$start_date = date('Y-m-d', strtotime($shift['startdate']));
				$start_date = new DateTime($start_date);
				$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 month')));
				break;
		}
		$interval = $shift['repeat_interval'];
		if($interval > 1) {
			$interval = new DateInterval("P{$interval}{$repeat_type}");
			$period = new DatePeriod($start_date, $interval, $end_date);
			foreach($period as $period_date) {
				if (date('Y-m-d', strtotime($calendar_date)) == $period_date->format('Y-m-d')) {
					$shifts[] = $shift;
				}
			}
		} else {
			$shifts[] = $shift;
		}
	}

	return $shifts;
}
function getTeamName($dbc, $teamid) {
	$team_name = '';
	$contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid'"),MYSQLI_ASSOC);
	foreach ($contact_list as $contact) {
		$team_name .= get_contact($dbc, $contact['contactid']).', ';
	}
	$team_name = rtrim($team_name, ', ');

	return $team_name;
}
function getContactTeams($dbc, $contactid) {
	return $teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contactid' AND `deleted` = 0"))['teams_list'];
}
function getClassificationLogo($dbc, $classification, $logo_url) {
	if(!empty($logo_url)) {
		$logo_img = '<img data-classification="'.$classification.'" class="id-circle" src="'.$logo_url.'">';
	} else {
		$initials = '';
		foreach(explode(' ',$classification) as $class_text) {
			$initials .= substr(strtoupper($class_text),0,1);
		}
		if(empty($initials)) {
			$initials = '&nbsp;';
		}
		$logo_img = '<span data-classification="'.$classification.'" class="id-circle" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.$initials.'</span>';
	}
	return $logo_img;
}
function getContactLogo($dbc, $contactid) {
	if(!(intval($contactid) > 0)) {
		$contactid = 0;
	} else {
		$contactid = intval($contactid);
	}
	$contact_name = !empty(get_client($dbc, $contactid)) ? get_client($dbc, $contactid) : get_contact($dbc, $contactid);
	// Check for an avatar chosen by the user
	$profile_photo = WEBSITE_URL."/Profile/download/profile_pictures/".$contactid.".jpg";
	// Check if an image has been uploaded
	if(url_exists($profile_photo)) {
		$output = '<img data-contact="'.$contactid.'" class="id-circle" src="'.$profile_photo.'" title="'.$contact_name.'">';
	} else {
		// If no image has been uploaded, and an avatar has been selected, use the avatar
		$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `preset_profile_picture`, `first_name`, `last_name`, `initials`, `calendar_color` FROM `contacts` LEFT JOIN `user_settings` ON `contacts`.`contactid`=`user_settings`.`contactid` WHERE `contacts`.`contactid` = '$contactid'"));
		if(!empty($user['preset_profile_picture']) && url_exists(WEBSITE_URL.'/img/avatars/'.$user['preset_profile_picture'])) {
			$output = '<img data-contact="'.$contactid.'" class="id-circle" src="'.WEBSITE_URL.'/img/avatars/'.$user['preset_profile_picture'].'" title="'.$contact_name.'">';
		// If nothing else has been set, use the contact's initials
		} else {
			$initials = ($user['initials'] == '' ? ($user['first_name'].$user['last_name'] == '' ? $user : substr(decryptIt($user['first_name']),0,1).substr(decryptIt($user['last_name']),0,1)) : $user['initials']);
			$colour = ($user['calendar_color'] == '' ? '#6DCFF6' : $user['calendar_color']);
			$output = '<span data-contact="'.$contactid.'" class="id-circle" style="background-color:'.$colour.'; font-family: \'Open Sans\';" title="'.$contact_name.'">'.$initials.'</span>';
		}
	}
	return $output;
}
function getShiftConflicts($dbc, $contact_id, $calendar_date, $new_starttime = '', $new_endtime = '', $shiftid = '', $clientid = '') {
	$day_of_week = date('l', strtotime($calendar_date));
	if(!empty($contact_id)) {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date);
	} else if(!empty($clientid)) {
		$shifts = checkShiftIntervals($dbc, '', $day_of_week, $calendar_date, 'shifts', $clientid);
	}
	
	if(!empty($shiftid) && empty($new_starttime) && empty($new_endtime)) {
		$shift = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));
		$new_starttime = $shift['starttime'];
		$new_endtime = $shift['endtime'];
	}

	$conflicts = [];
	foreach($shifts as $shift) {
		$starttime = strtotime(date('Y-m-d').' '.$shift['starttime']);
		$endtime = strtotime(date('Y-m-d').' '.$shift['endtime']);
		if(!empty($new_starttime) && !empty($new_endtime)) {
			$check_starttime = strtotime(date('Y-m-d').' '.$new_starttime);
			$check_endtime = strtotime(date('Y-m-d').' '.$new_endtime);
			if($shift['shiftid'] != $shiftid && (($starttime > $check_starttime && $starttime < $check_endtime) || ($endtime > $check_starttime && $endtime < $check_endtime))) {
				$conflicts[] = $shift['shiftid'].'*#*'.$shiftid;
			}
		} else {
			foreach($shifts as $check_shift) {
				if($check_shift['shiftid'] != $shift['shiftid'] && !in_array($shift['shiftid'].'*#*'.$check_shift['shiftid'], $conflicts) && !in_array($check_shift['shiftid'].'*#*'.$shift['shiftid'], $conflicts)) {
					$check_starttime = strtotime(date('Y-m-d').' '.$check_shift['starttime']);
					$check_endtime = strtotime(date('Y-m-d').' '.$check_shift['endtime']);
					if(($starttime > $check_starttime && $starttime < $check_endtime) || ($endtime > $check_starttime && $endtime < $check_endtime)) {
						$conflicts[] = $shift['shiftid'].'*#*'.$check_shift['shiftid'];
					}
				}
			}
		}
	}
	return $conflicts;
}
function getEquipmentAssignmentBlock($dbc, $equipmentid, $view, $date) {
	$block_html = '';
	$active_equipment = array_filter(explode(',',get_user_settings()['appt_calendar_equipment']));
	$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
	switch($view) {
		case 'weekly':
			$calendar_start = $date;
			if($calendar_start == '') {
				$calendar_start = date('Y-m-d');
			} else {
				$calendar_start = date('Y-m-d', strtotime($calendar_start));
			}
			$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
			$client_type = get_config($dbc, 'scheduling_client_type');
			$calendar_type = get_config($dbc, 'scheduling_wait_list');
			if($calendar_type == 'ticket_multi') {
				$calendar_type = 'ticket';
			}
			$weekly_start = get_config($dbc, 'scheduling_weekly_start');
			if($weekly_start == 'Sunday') {
				$weekly_start = 1;
			} else {
				$weekly_start = 0;
			}
			$day = date('w', strtotime($calendar_start));
			$week_start_date = date('F j', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
			$week_end_date = date('F j, Y', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));
			$week_start_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
			$week_end_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));

			$weekly_days = explode(',',get_config($dbc, 'scheduling_weekly_days'));
			if (!empty($equipment_category)) {
				$equipment_category = 'Truck';
			}
			$clientids = [];
			$equipassign_weekly = "<div style='margin-top: 5px;'>";
			$equip_regions = [$equipment['region']];
			$equip_locations = [$equipment['location']];
			$equip_classifications = [$equipment['classifications']];
			for($day_i = 0; $day_i < 7; $day_i++) {
				$today_equip_assign = date('Y-m-d', strtotime($week_start_date_check.' +'.$day_i.' days'));
				$today_equip_assign_day = date('l', strtotime($today_equip_assign));
				if(in_array($today_equip_assign_day, $weekly_days)) {
					$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipmentid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND DATE(`start_date`) <= '$today_equip_assign' AND DATE(`end_date`) >= '$today_equip_assign' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$today_equip_assign,%' ORDER BY `start_date` DESC, `end_date` ASC"));
					$clientids[] = $equip_assign['clientid'];
					if(!empty($equip_assign)) {
						$today_checked = 'checked="checked"';
					} else {
						$today_checked = '';
					}
					$day_of_week_letter = substr($today_equip_assign_day, 0, 1);
					$equipassign_weekly .= "&nbsp;".$day_of_week_letter.' <input type="checkbox" '.$today_checked.' disabled>';
					$equip_regions[] = $equip_assign['region'];
					$equip_locations[] = $equip_assign['location'];
					$equip_classifications[] = $equip_assign['classifications'];
				}
			}
			$equip_regions = implode('*#*', array_filter(array_unique($equip_regions)));
			$equip_locations = implode('*#*', array_filter(array_unique($equip_locations)));
			$equip_classifications = implode('*#*', array_filter(array_unique($equip_classifications)));
			
			$equip_regions = implode('*#*', array_filter(array_unique(explode('*#*', $equip_regions))));
			$equip_locations = implode('*#*', array_filter(array_unique(explode('*#*', $equip_locations))));
			$equip_classifications = implode('*#*', array_filter(array_unique(explode('*#*', $equip_classifications))));
			$equipassign_weekly .= "</div>";
			$clientids = array_filter(array_unique($clientids));
			$clientids = implode(',',$clientids);

			$block_html = "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"\"); retrieve_items(this); return false;'><div class='block-item equip_assign_draggable ".(in_array($equipment['equipmentid'],$active_equipment) ? 'active' : '')."' data-blocktype='equipment' data-equipment='".$equipment['equipmentid']."' data-client='".$clientids."' data-region='".$equip_regions."' data-classification='".$equip_classifications."' data-location='".$equip_locations."'><img class='drag-handle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='float: right; width: 2em;'>".$equipment['label'].$equipassign_weekly."</div></a>";
			break;
		case 'daily':
		default:
			$calendar_start = $date;
			if($calendar_start == '') {
				$calendar_start = date('Y-m-d');
			} else {
				$calendar_start = date('Y-m-d', strtotime($calendar_start));
			}
			$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
			$client_type = get_config($dbc, 'scheduling_client_type');
			$calendar_type = get_config($dbc, 'scheduling_wait_list');
			if($calendar_type == 'ticket_multi') {
				$calendar_type = 'ticket';
			}
			if (!empty($equipment_category)) {
				$equipment_category = 'Truck';
			}

			$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipmentid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND DATE(`start_date`) <= '$calendar_start' AND DATE(`end_date`) >= '$calendar_start' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$calendar_start,%'"));
			$equip_regions = implode('*#*',array_filter(array_unique([$equipment['region'], $equip_assign['region']])));
			$equip_locations = implode('*#*',array_filter(array_unique([$equipment['location'], $equip_assign['location']])));
			$equip_classifications = implode('*#*',array_filter(array_unique([$equipment['classification'], $equip_assign['classification']])));
			
			$equip_regions = implode('*#*', array_filter(array_unique(explode('*#*', $equip_regions))));
			$equip_locations = implode('*#*', array_filter(array_unique(explode('*#*', $equip_locations))));
			$equip_classifications = implode('*#*', array_filter(array_unique(explode('*#*', $equip_classifications))));
			
			$block_html = "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"\"); retrieve_items(this); return false;'><div class='block-item equip_assign_draggable ".(in_array($equipment['equipmentid'],$active_equipment) ? 'active' : '')."' data-blocktype='equipment' data-equipment='".$equipment['equipmentid']."' data-equipassign='".$equip_assign['equipment_assignmentid']."' data-client='".$equip_assign['clientid']."' data-region='".$equip_regions."' data-classification='".$equip_locations."' data-location='".$equip_classifications."'><img class='drag-handle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='float: right; width: 2em;'>".$equipment['label'].(empty($equip_assign) ? ' (Not Assigned)' : '')."</div></a>";
			break;
	}
	return $block_html;
}