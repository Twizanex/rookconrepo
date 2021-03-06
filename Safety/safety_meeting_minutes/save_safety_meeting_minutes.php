 <?php
    $today_date = $_POST['today_date'];
    $contactid = $_POST['contactid'];

    $absent = filter_var($_POST['absent'],FILTER_SANITIZE_STRING);
    $follow_up_action = filter_var(htmlentities($_POST['follow_up_action']),FILTER_SANITIZE_STRING);
    $corrective_actions = filter_var(htmlentities($_POST['corrective_actions']),FILTER_SANITIZE_STRING);
    $vehicle_logs = filter_var(htmlentities($_POST['vehicle_logs']),FILTER_SANITIZE_STRING);
    $vehicle_update = filter_var(htmlentities($_POST['vehicle_update']),FILTER_SANITIZE_STRING);
    $training = filter_var(htmlentities($_POST['training']),FILTER_SANITIZE_STRING);
    $driving = filter_var(htmlentities($_POST['driving']),FILTER_SANITIZE_STRING);
    $safety_concerns = filter_var(htmlentities($_POST['safety_concerns']),FILTER_SANITIZE_STRING);
    $discussion_items = filter_var(htmlentities($_POST['discussion_items']),FILTER_SANITIZE_STRING);

    $fields = '';
    for($i=0; $i<=3; $i++) {
        $fields .= $_POST['fields_'.$i].'**FFM**';
    }

    $fields = filter_var(htmlentities($fields),FILTER_SANITIZE_STRING);
    $desc = filter_var(htmlentities($_POST['desc']),FILTER_SANITIZE_STRING);
    $desc1 = filter_var(htmlentities($_POST['desc1']),FILTER_SANITIZE_STRING);
    $desc2 = filter_var(htmlentities($_POST['desc2']),FILTER_SANITIZE_STRING);
    $desc3 = filter_var(htmlentities($_POST['desc3']),FILTER_SANITIZE_STRING);
	$all_task = '';
    $total_task = count($_POST['task']);
    for($i=0; $i<$total_task; $i++) {
        if($_POST['task'][$i] != '') {
            $all_task .= $_POST['task'][$i].'**'.$_POST['hazard'][$i].'**'.$_POST['hazard1'][$i].'**##**';
        }
    }
    $all_task = filter_var($all_task,FILTER_SANITIZE_STRING);

    $attendance_staff = implode(',',$_POST['attendance_staff']);
    $attendance_extra = $_POST['attendance_extra'];

    $url_redirect = '';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['fieldlevelriskid'])) {
        $query_insert_site = "INSERT INTO `safety_safety_meeting_minutes` (`safetyid`, `today_date`, `contactid`, `absent`, `follow_up_action`, `corrective_actions`, `vehicle_logs`, `vehicle_update`, `training`, `driving`, `safety_concerns`, `discussion_items`, `attendance_staff`, `attendance_extra`, `fields`, `desc`, `desc1`, `desc2`, `desc3`, `all_task`) VALUES	('$safetyid', '$today_date', '$contactid', '$absent', '$follow_up_action', '$corrective_actions', '$vehicle_logs', '$vehicle_update', '$training', '$driving', '$safety_concerns', '$discussion_items', '$attendance_staff', '$attendance_extra', '$fields', '$desc', '$desc1', '$desc2', '$desc3', '$all_task')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $fieldlevelriskid = mysqli_insert_id($dbc);

        $attendance_staff_each = $_POST['attendance_staff'];
        for($i = 0; $i < count($_POST['attendance_staff']); $i++) {
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$attendance_staff_each[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        for($i=1;$i<=$attendance_extra;$i++) {
            $att_ex = 'Extra '.$i;
            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`) VALUES ('$safetyid', '$fieldlevelriskid', '$att_ex')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

        $tab = get_safety($dbc, $safetyid, 'tab');
        if($tab == 'Form') {
            $assign_staff = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 1)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

            include ('safety_meeting_minutes_pdf.php');
            echo safety_meeting_minutes_pdf($dbc,$safetyid, $fieldlevelriskid);
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        } else if($tab == 'Toolbox' || $tab == 'Tailgate') {
            $assign_staff = 'Organizer: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

            $query_insert_upload = "INSERT INTO `safety_attendance` (`safetyid`, `fieldlevelriskid`, `assign_staff`, `done`) VALUES ('$safetyid', '$fieldlevelriskid', '$assign_staff', 0)";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }

    } else {
        $fieldlevelriskid = $_POST['fieldlevelriskid'];
        $query_update_employee = "UPDATE `safety_safety_meeting_minutes` SET `contactid` = '$contactid', `absent` = '$absent', `follow_up_action` = '$follow_up_action', `corrective_actions` = '$corrective_actions', `vehicle_logs` = '$vehicle_logs', `vehicle_update` = '$vehicle_update', `training` = '$training', `driving` = '$driving', `safety_concerns` = '$safety_concerns', `fields` = '$fields', `desc` = '$desc', `desc1` = '$desc1', `desc2` = '$desc2', `desc3` = '$desc3', `discussion_items` = '$discussion_items', `all_task` = CONCAT(all_task,'$all_task') WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

    	$sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid' AND done=0");
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_id = $row_sa['safetyattid'];

            if($_POST['sign_'.$assign_staff_id] != '') {
                $sign = $_POST['sign_'.$assign_staff_id];

                $img = sigJsonToImage($sign);
                imagepng($img, 'safety_meeting_minutes/download/safety_'.$assign_staff_id.'.png');

                $assign_staff = filter_var($_POST['assign_staff_'.$assign_staff_id],FILTER_SANITIZE_STRING);

                if($assign_staff != '') {
                    $query_update_employee = "UPDATE `safety_attendance` SET `assign_staff` = '$assign_staff', `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                } else {
                    $query_update_employee = "UPDATE `safety_attendance` SET `done` = 1 WHERE safetyattid='$assign_staff_id'";
                    $result_update_employee = mysqli_query($dbc, $query_update_employee);
                }
            }
        }

        $get_total_notdone = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(safetyattid) AS total_notdone FROM safety_attendance WHERE	fieldlevelriskid='$fieldlevelriskid' AND safetyid='$safetyid' AND done=0"));
        if($get_total_notdone['total_notdone'] == 0) {
            include ('safety_meeting_minutes_pdf.php');
            echo safety_meeting_minutes_pdf($dbc,$safetyid, $fieldlevelriskid);
            if(strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
				$url_redirect = 'index.php?type=safety&reports=view';
			} else {
				$url_redirect = 'manual_reporting.php?type=safety';
			}
        }
    }

    if($url_redirect == '' && strpos($_SERVER['SCRIPT_NAME'],'index.php') !== FALSE) {
        $url_redirect = 'index.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
    } else if($url_redirect == '') {
        $url_redirect = 'add_manual.php?safetyid='.$safetyid.'&action=view&formid='.$fieldlevelriskid.'';
	}

    if($field_level_hazard == 'field_level_hazard_save') {
        echo '<script type="text/javascript">  window.location.replace("safety.php?tab='.$get_manual['tab'].'&category='.$get_manual['category'].'"); </script>';
    } else {
        if(IFRAME_PAGE && strpos($url_redirect, 'reports') !== FALSE) {
            echo '<script type="text/javascript">
            top.window.location.replace("'.$url_redirect.'"); </script>';
        } else {
            if(IFRAME_PAGE) {
                $url_redirect .= '&mode=iframe';
            }
            echo '<script type="text/javascript">
            window.location.replace("'.$url_redirect.'"); </script>';
        }
    }