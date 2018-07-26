<?php
/* Update Databases */

    //Baldwin's Database Changes
    echo "Baldwin's DB Changes:<br />\n";
    
    //2018-06-15 - TIcket #7838 - Calendar Lock Icon
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `calendar_history` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `calendar_history` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-15 - TIcket #7838 - Calendar Lock Icon

    //2018-06-18 - Ticket #7888 - Cleans
    $updated_already = get_config($dbc, 'updated_ticket7888_materials');
    if(empty($updated_already)) {
        $ticket_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE 'ticket_fields_%'"),MYSQLI_ASSOC);
        foreach ($ticket_types as $ticket_type) {
            $value_config = ','.$ticket_type['value'].',';
            $value_config = str_replace(',Material Category,',',Material Category,Material Subcategory,',$value_config);
            $value_config = trim($value_config, ',');
            set_config($dbc, $ticket_type['name'], $value_config);
        }
        $value_config = ','.get_field_config($dbc, 'tickets').',';
        $value_config = str_replace(',Material Category,',',Material Category,Material Subcategory,',$value_config);
        $value_config = trim($value_config, ',');
        mysqli_query($dbc, "UPDATE `field_config` SET `tickets` = '$value_config'");

        set_config($dbc, 'updated_ticket7888_materials', '1');
    }
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_templateid_loaded` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-18 - Ticket #7888 - Cleans

    //2018-06-19 - Ticket #7952 - Staff Subtabs & Fields
    $updated_already = get_config($dbc, 'updated_ticket7952_staff');
    if(empty($updated_already)) {
        include('Staff/field_list.php');
        $tabs = ['Profile','Staff'];
        foreach($tabs as $tab) {
            $new_fields = [];
            $staff_fields = mysqli_query($dbc, "SELECT * FROM `field_config_contacts` WHERE `tab` = '$tab' AND IFNULL(`accordion`,'') != '' AND IFNULL(`subtab`,'') != ''");
            while($row = mysqli_fetch_assoc($staff_fields)) {
                $value_config = array_filter(explode(',',$row['contacts']));
                foreach($value_config as $value) {
                    $field_found = false;
                    foreach($field_list as $label => $list) {
                        foreach($list as $subtab => $fields) {
                            foreach($fields as $field) {
                                if($value == $field) {
                                    $field_found = true;
                                    if($subtab == $row['subtab']) {
                                        if(!in_array($field, $new_fields[$subtab][$row['accordiion']])) {
                                            $new_fields[$subtab][$row['accordion']][] = $field;
                                        }
                                    } else {
                                        if(!in_array($field, $new_fields[$subtab][$label])) {
                                            $new_fields[$subtab][$label][] = $field;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!$field_found) {
                        if(!in_array($value, $new_fields['hidden']['hidden'])) {
                            $new_fields['hidden']['hidden'][] = $value;
                        }
                    }
                }
            }
            mysqli_query($dbc, "DELETE FROM `field_config_contacts` WHERE `tab` = '$tab' AND IFNULL(`accordion`,'') != '' AND IFNULL(`subtab`,'') != ''");
            foreach($new_fields as $subtab => $accordion) {
                foreach($accordion as $label => $fields) {
                    mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`, `subtab`, `accordion`, `contacts`) VALUES ('$tab', '$subtab', '$label', ',".implode(',', $fields).",')");
                }
            }
        }
        $staff_tabs = explode(',',get_config($dbc, 'staff_field_subtabs'));
        $staff_subtabs = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `subtab` FROM `field_config_contacts` WHERE `tab` = 'Staff' AND IFNULL(`subtab`,'') != ''"),MYSQLI_ASSOC),'subtab');
        if(in_array('staff_bio',$staff_subtabs)) {
            $staff_tabs[] = 'Staff Bio';
        }
        if(in_array('health_concerns',$staff_tabs)) {
            $staff_tabs[] = 'Health Concerns';
        }
        if(in_array('allergies',$staff_tabs)) {
            $staff_tabs[] = 'Allergies';
        }
        if(in_array('company_benefits',$staff_tabs)) {
            $staff_tabs[] = 'Company Benefits';
        }
        $staff_tabs = implode(',',array_filter($staff_tabs));
        set_config($dbc, 'staff_field_subtabs', $staff_tabs);
        set_config($dbc, 'updated_ticket7952_staff', 1);
    }
    //2018-06-19 - Ticket #7952 - Staff Subtabs & Fields

    //2018-06-20 - TIcket #7967 - Multiple Sites
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `main_siteid` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-20 - TIcket #7967 - Multiple Sites

    //2018-06-21 - Ticket #8000 - HR Default Email
    $updated_already = get_config($dbc, 'updated_ticket8000_emails');
    if(empty($updated_already)) {
        $manual_emails = mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE 'manual_%_email'");
        while($manual_email = mysqli_fetch_assoc($manual_emails)) {
            if($manual_email['value'] == 'dayanasanjay@yahoo.com') {
                set_config($dbc, $manual_email['name'], '');
            }
        }
        set_config($dbc, 'updated_ticket8000_emails', 1);
    }

    //2018-06-21 - Ticket #8000 - HR Default Email

    //2018-06-21 - Ticket #7736 - Shift Reports & My Shifts
    if(!mysqli_query($dbc, "ALTER TABLE `user_forms` ADD `attached_contacts` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `user_form_pdf` ADD `attached_contactid` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-21 - Ticket #7736 - Shift Reports & My Shifts

    //2018-06-26 - Ticket #7370 - Equipment Styling
    if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `equipment_image` VARCHAR(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-26 - Ticket #7370 - Equipment Styling

    //2018-06-26 - Ticket #7814 - Holidays Update Notifications
    if(!mysqli_query($dbc, "CREATE TABLE `holiday_update_reminders` (
        `reminderid` int(11) NOT NULL,
        `date` date NOT NULL,
        `sent` int(1) NOT NULL DEFAULT 1,
        `log` text NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        ADD PRIMARY KEY (`reminderid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        MODIFY `reminderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-26 - Ticket #7814 - Holidays Update Notifications

    //2018-06-28 - Ticket #7899 - Sessions Additions
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_total_time` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-28 - Ticket #7899 - Sessions Additions

    //2018-06-29 - Ticket #7898 - Clients Tile
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `comments_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `description_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `general_comments_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `notes_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-29 - Ticket #7898 - Clients Tile

    //2018-07-03 - Ticket #7549 - Mileage Sheet
    if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `mileage` text AFTER `labour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-03 - Ticket #7549 - Mileage Sheet

    //2018-07-04 - Ticket #7868 - Incident Reports Form Builder
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_incident_report` ADD `user_form_id` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `pdf_id` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-04 - Ticket #7868 - Incident Reports Form Builder

    //2018-07-04 - Ticket #8009 - Sessions Additions
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `guardianid` int(11) NOT NULL DEFAULT 0 AFTER `clientid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-04 - Ticket #8009 - Sessions Additions

    //2018-07-11 - Ticket #8060 - Estimate Templates VPL
    if(!mysqli_query($dbc, "ALTER TABLE `estimate_template_lines` ADD `product_pricing` varchar(500) AFTER `qty`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #8060 - Estimate Templates VPL

    //2018-07-11 - Ticket #8150 - Contacts Additions
    if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `total_estimated_hours` decimal(10,2) AFTER `total_price`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #8150 - Contacts Additions

    //2018-07-11 - Ticket #7997 - Certificates
    $updated_already = get_config($dbc, 'updated_ticket7997_certificates');
    if(empty($updated_already)) {
        $result = mysqli_query($dbc, "SELECT DISTINCT(`certificate_type`) FROM `certificate` WHERE `deleted` = 0 ORDER BY `certificate_type`");
        $certificate_types = [];
        while($row = mysqli_fetch_assoc($result)) {
            $certificate_types[] = $row['certificate_type'];
        }
        set_config($dbc, 'certificate_types', implode('#*#', $certificate_types));

        $result = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `certificate` WHERE `deleted` = 0 ORDER BY `category`");
        $certificate_categories = [];
        while($row = mysqli_fetch_assoc($result)) {
            $certificate_categories[] = $row['category'];
        }
        set_config($dbc, 'certificate_categories', implode('#*#', $certificate_categories));

        set_config($dbc, 'updated_ticket7997_certificates', 1);
    }
    if(!mysqli_query($dbc, "ALTER TABLE `certificate` CHANGE `certificate_reminder` `certificate_reminder` varchar(1000)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #7997 - Certificates

    //2018-07-13 - Task #6494 - AAFS Positions
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` CHANGE `position` `position` VARCHAR(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-13 - Task #6494 - AAFS Positions

    //2018-07-17 - Ticket #8311 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `teams` ADD `hide_days` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-17 - Ticket #8311 - Cleans Calendar

    //2018-07-17 - Ticket #8311 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_ticketid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `ticketcommid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-17 - Ticket #8311 - Cleans Calendar

    //2018-07-20 - Ticket #8352 - Sales Auto Archive
    if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `status_date` date NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `sales_status_date` BEFORE UPDATE ON `sales`
         FOR EACH ROW BEGIN
            IF NEW.`status` != OLD.`status` THEN
                SET NEW.`status_date` = CURDATE();
            END IF;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-20 - Ticket #8352 - Sales Auto Archive

    //2018-07-25 - Ticket #8413 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `teams` ADD `team_name` varchar(500) AFTER `teamid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `ticket_recurrences` (
        `id` int(11) NOT NULL,
        `ticketid` int(11) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `repeat_type` varchar(500),
        `repeat_interval` int(11) NOT NULL,
        `repeat_days` varchar(500),
        `last_added_date` date NOT NULL,
        `deleted` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_recurrences`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_recurrences`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-25 - Ticket #8413 - Cleans Calendar

    echo "Baldwin's DB Changes Done<br />\n";
?>