<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_functions_inc.php');
include_once('../Calendar/calendar_settings_inc.php');
ob_clean();
$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields'));
$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
$config_type = $_POST['config_type'];
$new_today_date = $_POST['calendar_date'];
$day_of_week = date('l', strtotime($new_today_date));
$day_period = get_config($dbc, $config_type.'_increments');
$contact_id = $_POST['contact_id'];

$column = '';
if($_GET['type'] == 'schedule' && $_GET['view'] == 'summary') {
	include('monthly_display_equip_summary.php');
} else if($wait_list == 'shifts' || isset($_GET['shiftid']) || $_GET['mode'] == 'shift') {
	include('monthly_display_shift.php');
} else if($_GET['type'] == 'uni') {
	include('monthly_display_universal.php');
} else if($_GET['type'] == 'event') {
	include('monthly_display_events.php');
} else if($_GET['type'] == 'estimates') {
	include('monthly_display_estimates.php');
} else if($_GET['type'] == 'schedule') {
	include('monthly_display_equip.php');
} else if($wait_list == 'ticket') {
	include('monthly_display_tickets.php');
} else if($wait_list == 'appt') {
	include('monthly_display_appt.php');
}

echo $column;