<?php
	//Jonathan's Database Changes
	echo "Jonathan's DB Changes:<br />\n";
	$db_version_jonathan = get_config($dbc, 'db_version_jonathan');
	/*** USE THE FOLLOWING EXAMPLES: ***
	if(!mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `table_name` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`deleted` TINYINT(1) NOT NULL DEFAULT 0
	)")) {
		echo "Error: ".mysqli_error($dbc)."<br />\n";
	}
	if(!mysqli_query($dbc, "ALTER TABLE `table_name` ADD `column` VARCHAR(40) DEFAULT '' AFTER `exist_column`")) {
		echo "Error: ".mysqli_error($dbc)."<br />\n";
	} */
	
	if($db_version_jonathan < 7) {
		// June 16, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `notes` TEXT AFTER `order_number`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 18, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_manifests` ADD `revision` INT(11) UNSIGNED NOT NULL DEFAULT 1 AFTER `signature`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_manifests` ADD `history` TEXT AFTER `signature`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 20, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `estimate_scope` ADD `pricing` VARCHAR(20) AFTER `price`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 25, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `ref_card` TEXT AFTER `rate_card_name`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		// June 27, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_history` ADD `src` TEXT AFTER `userid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		set_config($dbc, 'db_version_jonathan', 7);
	}
	
	if($db_version_jonathan < 8) {
		// June 29, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 5, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `date_sent` TEXT AFTER `status_history`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `sent_by` TEXT AFTER `date_sent`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		set_config($dbc, 'db_version_jonathan', 7);
	}
	
	if(get_config($dbc, 'update_timesheet_config') < 1) {
		// July 9, 2018
		if(!mysqli_query($dbc, "UPDATE `field_config` SET `time_cards`=CONCAT(`time_cards`,',comment_box,')")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_timesheet_config', 1);
	}
	
	echo "Jonathan's DB Changes Done<br />\n";
?>