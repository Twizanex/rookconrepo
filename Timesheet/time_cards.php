<?php
include('../include.php');
include('../Calendar/calendar_functions_inc.php');
include 'config.php';
?>
<script type="text/javascript" src="timesheet.js"></script>
<style>
    .ui-datepicker-current:empty { display:none; }
</style>
</head>
<body>
<?php 
include_once ('../navigation.php');
checkAuthorised('timesheet');

if(!empty($_GET['export'])) {
	ob_clean();

	$layout = get_config($dbc, 'timesheet_layout');
	$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
	$value_config = explode(',',get_field_config($dbc, 'time_cards'));
	if(!in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config)) {
		$value_config = array_merge($value_config,['reg_hrs','extra_hrs','relief_hrs','sleep_hrs','sick_hrs','sick_used','stat_hrs','stat_used','vaca_hrs','vaca_used']);
	}
	$timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder');
	$timesheet_approval_status_comments = get_config($dbc, 'timesheet_approval_status_comments');
	$timesheet_rounding = get_config($dbc, 'timesheet_rounding');
	$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
	
	$mode = $_GET['export'];
	$search_staff = $_GET['search_staff'];
	$search_site = $_GET['search_site'];
	$search_project = $_GET['search_project'];
	$search_start_date = $_GET['search_start_date'];
	$search_end_date = $_GET['search_end_date'];

	if($layout == 'position' || $layout == 'ticket_task') {
		echo '<script type="text/javascript">window.location.href = "'.WEBSITE_URL.'/Timesheet/reporting.php?export=pdf&search_staff='.$search_staff.'&search_site='.$search_site.'&search_project='.$search_project.'&search_start_date='.$search_start_date.'&search_end_date='.$search_end_date.'"; </script>';
	} else {		
		// Get Staff Schedule
		$schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT `scheduled_hours`, `schedule_days` FROM `contacts` WHERE `contactid`='$search_staff'"));
		$schedule_hrs = explode('*',trim($schedule['scheduled_hours'],'*'));
		$schedule_days = explode(',',$schedule['schedule_days']);
		$schedule_list = [0=>'---',1=>'---',2=>'---',3=>'---',4=>'---',5=>'---',6=>'---'];
		foreach($schedule_days as $key => $day_of_week) {
			$schedule_list[$day_of_week] = $schedule_hrs[$key];
		}
		
		// Get Year to date totals
		$start_of_year = date('Y-01-01', strtotime($search_start_date));
		$sql = "SELECT IFNULL(SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)),0) SICK_HRS,
			IFNULL(SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)),0) STAT_AVAIL,
			IFNULL(SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)),0) STAT_HRS,
			IFNULL(SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)),0) VACA_AVAIL,
			IFNULL(SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)),0) VACA_HRS
			FROM `time_cards` WHERE `staff`='$search_staff' AND `date` < '$search_start_date' AND `date` >= '$start_of_year' AND `deleted`=0";
		$year_to_date = mysqli_fetch_array(mysqli_query($dbc, $sql));
		$stat_hours = $year_to_date['STAT_AVAIL'];
		$stat_taken = $year_to_date['STAT_HRS'];
		$vacation_hours = $year_to_date['VACA_AVAIL'];
		$vacation_taken = $year_to_date['VACA_HRS'];
		$sick_taken = $year_to_date['SICK_HRS'];

		// Get Signatures
	    $sql = "SELECT * FROM `time_cards_signature` WHERE `contactid` = '$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date'";
	    $result = mysqli_query($dbc, $sql);
	    $all_signatures = [];
	    while ($row = mysqli_fetch_array($result)) {
	    	$all_signatures[$row['date']] = $row['signature'];
	    }

		// Get Rows for hours
		$sql = "SELECT `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS, SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
			SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
			SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
			SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
			SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
			GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'') SEPARATOR '&lt;br /&gt;') COMMENTS, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS,
			SUM(`timer_tracked`) TRACKED_HRS,
			SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `start_time`, `end_time`, `manager_approvals`, `coord_approvals`, `manager_name`, `coordinator_name`, `ticket_attached_id`, `ticketid`, `time_cards_id` FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' AND (`projectid`='$search_project' OR '$search_project'='') AND `deleted`=0 GROUP BY `date`";
		if($layout == 'multi_line') {
			$sql .= ", `time_cards_id`";
		}
		$sql .= " ORDER BY `date`, IFNULL(STR_TO_DATE(`start_time`, '%l:%i %p'),STR_TO_DATE(`start_time`, '%H:%i')) ASC, IFNULL(STR_TO_DATE(`end_time`, '%l:%i %p'),STR_TO_DATE(`end_time`, '%H:%i')) ASC";
		$date = $search_start_date;
		$total = ['REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0];
		
		// Export as PDF
		if($mode == 'pdf') {
			include_once('../tcpdf/tcpdf.php');
			$logo = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT('download/',`value`) `logo` FROM `general_configuration` WHERE `name`='timesheet_pdf_logo' UNION SELECT '".WEBSITE_URL."/img/pdf_logo.png' `logo`"))['logo'];
			$bottom_border_set = [ 'B' => array('width' => 0.3) ];
			$timesheet_pdf_fields = ','.get_config($dbc, 'timesheet_pdf_fields').',';

			$staff_category = get_contact($dbc, $search_staff, 'category');
			DEFINE('HEADER_TEXT', ($staff_category == 'Staff' ? 'Employee' : $staff_category)." Time Sheet for ".get_contact($dbc, $search_staff));
			DEFINE('FOOTER_TEXT', ($staff_category == 'Staff' ? 'Employee' : $staff_category)." Time Sheet for ".get_contact($dbc, $search_staff)." from $search_start_date to $search_end_date");
			class MYPDF extends TCPDF {

				//Page header
				public function Header() {
					if($front_client_info != '') {
						if ($this->PageNo() > 1) {
							$this->setCellHeightRatio(0.7);
							$this->SetFont('helvetica', '', 8);
							$text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
							$this->writeHTMLCell(0, 0, 0 , 5, $text, 0, 0, false, "R", true);
						}
					} else {
						$this->setCellHeightRatio(0.7);
						$this->SetFont('helvetica', '', 8);
						$text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
						$this->writeHTMLCell(0, 0, 0 , 5, $text, 0, 0, false, "R", true);
					}
				}

				// Page footer
				public function Footer() {
					// Position at 15 mm from bottom
					$this->SetY(-15);
					$this->SetFont('helvetica', 'I', 8);
					$text = '<p style="text-align:right;">'.FOOTER_TEXT.' Page '.$this->getAliasNumPage().'</p>';
					$this->writeHTMLCell(0, 0, '', '', $text, 0, 0, false, "R", true);
				}
			}

			//Calculate widths to fit
			$comment_width_compare = ['total_tracked_hrs','reg_hrs','direct_hrs','indirect_hrs','extra_hrs','relief_hrs','sleep_hrs','training_hrs','sick_hrs','sick_used','stat_hrs','stat_used','vaca_hrs','vaca_used','payable_hrs','breaks','total_tracked_time'];
			$comment_width_compare_wide = ['start_time','end_time','show_hours','ticketid','planned_hrs','tracked_hrs'];
			$ytd_offset_compare = ['total_tracked_hrs','reg_hrs','direct_hrs','indirect_hrs','extra_hrs','relief_hrs','sleep_hrs','training_hrs','sick_hrs','payable_hrs','total_tracked_time'];
			$comment_width = 157 - (count(array_intersect($comment_width_compare, $value_config)) * 10);
			$comment_offset = 203 - (count(array_diff($comment_width_compare, $value_config)) * 10);
			$comment_width = $comment_width - (count(array_intersect($comment_width_compare_wide, $value_config)) * 22);
			$comment_offset = $comment_offset + (count(array_intersect($comment_width_compare_wide, $value_config)) * 22);
			$bfytd_offset = 18 + (count(array_intersect($comment_width_compare_wide, $value_config)) * 22);
			$ytd_offset = 110 - (count(array_diff($ytd_offset_compare, $value_config)) * 10);
			$hrs_offset = 13 + (count(array_intersect($comment_width_compare_wide, $value_config)) * 22);
			if(in_array('signature',$value_config) && !in_array('signature_pdf_hidden',$value_config)) {
				$comment_width -= 35;
			}
			if($comment_width <= 25) {
				$page_orientation = 'L';
				$landscape_width = 90;
			} else {
				$page_orientation = 'P';
				$landscape_width = 0;
			}
			$comment_width += $landscape_width;
			
			// $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf = new MYPDF($page_orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
			
			$pdf->SetFont('dejavusans', '', 8);
			$pdf->AddPage();
			
			$pdf->Image($logo, 30, 20, 30, 30, '', '', '', true, 150, '', false, false, 0, false, false, false);
			
			$pdf->setCellPaddings(1,1,1,1);
			$pdf->setCellMargins(0,0,0,0);
			$pdf->MultiCell(40, 4, 'TIME SHEET NAME', 0, 'C', 0, 0, 60 + $landscape_width);
			$pdf->MultiCell(15, 1, 'Name:', 0, 'R', 0, 0, '');
			$pdf->TextField('employee_name', 30, 5, array(), array('v'=>get_contact($dbc, $search_staff), 'dv'=>get_contact($dbc, $search_staff)));
			$pdf->MultiCell(25, 1, 'Employee No.:', 0, 'R', 0, 0, '');
			$pdf->TextField('employee_num', 20, 5, array(), array('v'=>get_contact($dbc, $search_staff, 'employee_num'), 'dv'=>get_contact($dbc, $search_staff, 'employee_num')));
			$pdf->ln();
			if(strpos($timesheet_pdf_fields, ',Location,') !== FALSE) {
				$pdf->MultiCell(15, 1, 'Location:', 0, 'R', 0, 0, 100 + $landscape_width);
				$pdf->TextField('timesheet_location', 75, 5);
				$pdf->ln();
			}
			$pdf->MultiCell(15, 1, 'Position:', 0, 'R', 0, 0, 100 + $landscape_width);
			$pdf->TextField('employee_position', 30, 5, array(), array('v'=>get_contact($dbc, $search_staff, 'position'), 'dv'=>get_contact($dbc, $search_staff, 'position')));
			$pdf->MultiCell(25, 1, 'Hrs/wk:', 0, 'R', 0, 0, '');
			$pdf->TextField('employee_hours', 20, 5);
			$pdf->ln();
			$pdf->MultiCell(40, 1, 'EMPLOYEE TIME SHEET', 0, 'C', 0, 0, 60 + $landscape_width);
			$pdf->MultiCell(35, 1, 'Commencement Date:', 0, 'R', 0, 0, '');
			$pdf->TextField('employee_start', 55, 5);
			$pdf->ln();
			$pdf->MultiCell(45, 1, 'Pay Period From:', 0, 'R', 0, 0, 85 + $landscape_width);
			$pdf->TextField('period_start', 25, 5, array(), array('v'=>$search_start_date, 'dv'=>$search_start_date));
			$pdf->MultiCell(10, 1, 'To:', 0, 'R', 0, 0, '');
			$pdf->TextField('period_end', 25, 5, array(), array('v'=>$search_end_date, 'dv'=>$search_end_date));
			
			// Time Sheet Headings
			$pdf->SetXY(15, 60);
			$pdf->setFillColor(200,200,200);
			$pdf->Cell($bfytd_offset,6,'Balance Forward Y.T.D.',1,0,'L');
			$pdf->Cell($ytd_offset,6,'',1,0,'C',1);
			if(in_array('sick_used',$value_config)) {
				$pdf->Cell(10,6,$sick_taken,1,0,'C');
			}
			if(in_array('stat_hrs',$value_config)) {
				$pdf->Cell(10,6,$stat_hours,1,0,'C');
			}
			if(in_array('stat_used',$value_config)) {
				$pdf->Cell(10,6,$stat_taken,1,0,'C');
			}
			if(in_array('vaca_hrs',$value_config)) {
				$pdf->Cell(10,6,$vacation_hours,1,0,'C');
			}
			if(in_array('vaca_used',$value_config)) {
				$pdf->Cell(10,6,$vacation_taken,1,0,'C');
			}
			if(in_array('breaks',$value_config)) {
				$pdf->Cell(10,6,'',1,0,'C');
			}
			$pdf->ln();
			$pdf->MultiCell(18, 1, 'Date', 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			if(in_array('ticketid',$value_config)) {
				$pdf->MultiCell(22, 1, TICKET_NOUN, 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			if(in_array('show_hours',$value_config)) {
				$pdf->MultiCell(22, 1, 'Hours', 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			if(in_array('start_time',$value_config)) {
				$pdf->MultiCell(22, 1, 'Start Time', 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			if(in_array('end_time',$value_config)) {
				$pdf->MultiCell(22, 1, 'End Time', 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			if(in_array('planned_hrs',$value_config)) {
				$pdf->MultiCell(22, 1, "Planned\nHours", 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			if(in_array('tracked_hrs',$value_config)) {
				$pdf->MultiCell(22, 1, "Tracked\nHours", 1, 'C', 0, 0, '', '', true, 0, false, true, 20, 'B');
			}
			$pdf->StartTransform();
			$pdf->Rotate(90);
			if(in_array('total_tracked_time',$value_config)) {
				$pdf->MultiCell(20,10,"Total\nTracked",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('total_tracked_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Tracked\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('payable_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Payable\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('reg_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Regular\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('direct_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Direct\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('indirect_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Indirect\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('extra_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Extra\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('relief_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Relief\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('sleep_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Sleep\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('training_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Training\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('sick_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Sick Time\nAdjustment",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('sick_used',$value_config)) {
				$pdf->MultiCell(20,10,"Sick Hrs.\nTaken",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('stat_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Stat Hours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('stat_used',$value_config)) {
				$pdf->MultiCell(20,10,"Stat. Hrs.\nTaken",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('vaca_hrs',$value_config)) {
				$pdf->MultiCell(20,10,"Vacation\nHours",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('vaca_used',$value_config)) {
				$pdf->MultiCell(20,10,"Vacation\nHrs. Taken",1,'L', 0, 1, $hrs_offset);
			}
			if(in_array('breaks',$value_config)) {
				$pdf->MultiCell(20,10,"Breaks",1,'L', 0, 1, $hrs_offset);
			}
			$pdf->StopTransform();
			$pdf->SetXY($comment_offset, 60);
			$pdf->MultiCell($comment_width, 1, 'Comments', 1, 'C', 0, 1, '', '', true, 0, false, true, 26, 'B');
			$pdf->SetXY($comment_offset + $comment_width, 60);
			if(in_array('signature',$value_config) && !in_array('signature_pdf_hidden',$value_config)) {
				$pdf->MultiCell(35, 1, "Parent/Guardian\nSignature", 1, 'C', 0, 1, '', '', true, 0, false, true, 26, 'B');
			}
			
			$pdf->SetXY(15, 86);
			$result = mysqli_query($dbc, $sql);
			$row = mysqli_fetch_array($result);
			while(strtotime($date) <= strtotime($search_end_date)) {
				$timecardid = 0;
				$start_time = '';
				$end_time = '';
				$approval_status = '';
				if($row['date'] == $date) {
					foreach($config['hours_types'] as $hours_type) {
						if($row[$hours_type] > 0) {
							switch($timesheet_rounding) {
								case 'up':
									$row[$hours_type] = ceil($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
									break;
								case 'down':
									$row[$hours_type] = floor($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
									break;
								case 'nearest':
									$row[$hours_type] = round($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
									break;
							}
						}
					}
					$hrs = ['REG'=>$row['REG_HRS'],'DIRECT'=>$row['DIRECT_HRS'],'INDIRECT'=>$row['INDIRECT_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],
						'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'TRACKED_HRS'=>$row['TRACKED_HRS'],'BREAKS'=>$row['BREAKS']];
					$comments = '';
					if(in_array('project',$value_config)) {
						foreach(explode(',',$row['PROJECTS']) as $projectid) {
							if($projectid > 0) {
								$comments .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `projects` WHERE `projectid`='$projectid'"))).'<br />';
							}
						}
					}
					if(in_array('search_client',$value_config)) {
						foreach(explode(',',$row['CLIENTS']) as $clientid) {
							if($clientid > 0) {
								$comments .= get_contact($dbc, $clientid).'<br />';
							}
						}
					}
					$comments .= html_entity_decode($row['COMMENTS']);
					foreach($total as $key => $value) {
						$total[$key] += $hrs[$key];
					}
					while(substr($comments,-6) == '<br />' || substr($comments,-4) == '<br>' || substr($comments,-1) == ' ') {
						if(substr($comments,-6) == '<br />') {
							$comments = substr($comments,0,-6);
						}
						if(substr($comments,-4) == '<br>') {
							$comments = substr($comments,0,-4);
						}
						if(substr($comments,-1) == ' ') {
							$comments = substr($comments,0,-1);
						}
					}
					$comments = trim($comments,' ');
											
					if($timesheet_approval_status_comments == 1) {
						if(!empty(trim($row['manager_approvals'],','))) {
							$approval_list = [];
							foreach(explode(',',$row['manager_approvals']) as $approval_manager) {
								if($approval_manager > 0) {
									$approval_list[] = get_contact($dbc, $approval_manager);
								}
							}
							$approval_status = 'Approved by '.implode(', ', $approval_list).'<br />';
						} else if(!empty($row['manager_name'])) {
							$approval_status = 'Approved by '.$row['manager_name'].'<br />';
						} else {
							$approval_status = 'Waiting for Approval<br />';
						}
					}
					$comments = $approval_status.$comments;

					$timecardid = $row['time_cards_id'];
					if(empty(strip_tags($comments))) {
						$comments = $timesheet_comment_placeholder;
					}
					$start_time = $row['start_time'];
					$end_time = $row['end_time'];

					if(in_array('training_hrs',$value_config) && $timecardid > 0) {
						if(is_training_hrs($dbc, $timecardid)) {
							$hrs['TRAINING'] = $hrs['REG'];
							$hrs['REG'] = 0;
							$total['REG'] -= $hrs['TRAINING'];
							$total['TRAINING'] += $hrs['TRAINING'];
						} else {
							$hrs['TRAINING'] = 0;
						}
					} else {
						$hrs['TRAINING'] = 0;
					}
					if(!in_array('comment_box',$value_config)) {
						$comments = '';
					}

					$row = mysqli_fetch_array($result);
				} else {
					$hrs = ['REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0];
					$comments = '';
				}
				$comment_height = ((!empty($approval_status) ? $pdf->getStringHeight($comment_width, $approval_status) : 0) + $pdf->getStringHeight($comment_width, $comments));
				if(file_get_contents('../Timesheet/download/'.$all_signatures[$date])) {
					$sig_height = $pdf->getStringHeight($comment_width, '<img src="../Timesheet/download/'.$all_signatures[$date].'" style="width: auto; height: 20px;">');
					$comment_height = $comment_height > $sig_height ? $comment_height : $sig_height;
				}
				$ticket_labels = get_ticket_labels($dbc, $date, $search_staff, $layout, $timecardid);
				$ticket_labels_height = $pdf->getStringHeight(22, $ticket_labels);
				if(in_array('ticketid',$value_config) && $ticket_labels_height > $comment_height) {
					$comment_height = $ticket_labels_height;
				}
				$planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff, $layout, $timecardid);
				$planned_hrs_height = $pdf->getStringHeight(22, $planned_hrs);
				if(in_array('planned_hrs',$value_config) && $planned_hrs_height > $comment_height) {
					$comment_height = $planned_hrs_height;
				}
				$tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff, $layout, $timecardid);
				$tracked_hrs_height = $pdf->getStringHeight(22, $tracked_hrs);
				if(in_array('tracked_hrs',$value_config) && $tracked_hrs_height > $comment_height) {
					$comment_height = $tracked_hrs_height;	
				}
				$total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff, $layout, $timecardid);
				$total_tracked_time_height = $pdf->getStringHeight(10, $total_tracked_time);
				if(in_array('tracked_hrs',$value_config) && $total_tracked_time_height > $comment_height) {
					$comment_height = $total_tracked_time_height;	
				}
				$pdf->Cell(18,$comment_height,$date,1,0,'C');
				if(in_array('ticketid',$value_config)) {
					$pdf->MultiCell(22,$comment_height,$ticket_labels,1,'L',false,0);
				}
				if(in_array('show_hours',$value_config)) {
					$pdf->Cell(22,$comment_height,$schedule_list[date('w',strtotime($date))],1,0,'C');
				}
				if(in_array('start_time',$value_config)) {
					$pdf->MultiCell(22,$comment_height,$start_time,1,'C',false,0);
				}
				if(in_array('end_time',$value_config)) {
					$pdf->MultiCell(22,$comment_height,$end_time,1,'C',false,0);
				}
				if(in_array('planned_hrs',$value_config)) {
					$pdf->MultiCell(22,$comment_height,$planned_hrs,1,'C',false,0);
				}
				if(in_array('tracked_hrs',$value_config)) {
					$pdf->MultiCell(22,$comment_height,$tracked_hrs,1,'C',false,0);
				}
				if(in_array('total_tracked_time',$value_config)) {
					$pdf->MultiCell(10,$comment_height,$total_tracked_time,1,'C',false,0);
				}
				if(in_array('total_tracked_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))),1,0,'C');
				}
				if(in_array('payable_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))),1,0,'C');
				}
				if(in_array('reg_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))),1,0,'C');
				}
				if(in_array('direct_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['DIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DIRECT'],2) : time_decimal2time($hrs['DIRECT']))),1,0,'C');
				}
				if(in_array('indirect_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['INDIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['INDIRECT'],2) : time_decimal2time($hrs['INDIRECT']))),1,0,'C');
				}
				if(in_array('extra_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['EXTRA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['EXTRA'],2) : time_decimal2time($hrs['EXTRA']))),1,0,'C');
				}
				if(in_array('relief_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['RELIEF']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['RELIEF'],2) : time_decimal2time($hrs['RELIEF']))),1,0,'C');
				}
				if(in_array('sleep_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['SLEEP']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SLEEP'],2) : time_decimal2time($hrs['SLEEP']))),1,0,'C');
				}
				if(in_array('training_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['TRAINING']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRAINING'],2) : time_decimal2time($hrs['TRAINING']))),1,0,'C');
				}
				if(in_array('sick_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['SICK_ADJ']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK_ADJ'],2) : time_decimal2time($hrs['SICK_ADJ']))),1,0,'C');
				}
				if(in_array('sick_used',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['SICK']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK'],2) : time_decimal2time($hrs['SICK']))),1,0,'C');
				}
				if(in_array('stat_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['STAT_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT_AVAIL'],2) : time_decimal2time($hrs['STAT_AVAIL']))),1,0,'C');
				}
				if(in_array('stat_used',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['STAT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT'],2) : time_decimal2time($hrs['STAT']))),1,0,'C');
				}
				if(in_array('vaca_hrs',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['VACA_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA_AVAIL'],2) : time_decimal2time($hrs['VACA_AVAIL']))),1,0,'C');
				}
				if(in_array('vaca_used',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['VACA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA'],2) : time_decimal2time($hrs['VACA']))),1,0,'C');
				}
				if(in_array('breaks',$value_config)) {
					$pdf->Cell(10,$comment_height,(empty($hrs['BREAKS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['BREAKS'],2) : time_decimal2time($hrs['BREAKS']))),1,0,'C');
				}
				if(in_array('comment_box',$value_config)) {
					$pdf->MultiCell($comment_width,$comment_height,$comments,1,'J',0,0,'','',true,0,true,true,0,'B');
				}
				if(in_array('signature',$value_config) && !in_array('signature_pdf_hidden',$value_config)) {
					$img = '';
					if(file_get_contents('../Timesheet/download/'.$all_signatures[$date])) {
						$img = '<img src="../Timesheet/download/'.$all_signatures[$date].'" style="width: auto; height: 20px;">';
					}
					$pdf->MultiCell(35,$comment_height,$img,1,'C',0,0,'','',true,0,true,true,0,'B');
				}
				$pdf->ln();
				if($layout != 'multi_line' || $date != $row['date']) {
					$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
				}
			}
			$pdf->Cell($bfytd_offset,0,"Totals",1,0,'L');
			if(in_array('total_tracked_time',$value_config)) {
				$pdf->Cell(10,0,'',1,0,'C');
			}
			if(in_array('total_tracked_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['TRACKED_HRS'],2) : time_decimal2time($total['TRACKED_HRS']))),1,0,'C');
			}
			if(in_array('payable_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG']))),1,0,'C');
			}
			if(in_array('reg_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG']))),1,0,'C');
			}
			if(in_array('direct_hrs',$value_config)) {;
				$pdf->Cell(10,0,(empty($total['DIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['DIRECT'],2) : time_decimal2time($total['DIRECT']))),1,0,'C');
			}
			if(in_array('indirect_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['INDIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['INDIRECT'],2) : time_decimal2time($total['INDIRECT']))),1,0,'C');
			}
			if(in_array('extra_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['EXTRA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA']))),1,0,'C');
			}
			if(in_array('relief_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['RELIEF']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF']))),1,0,'C');
			}
			if(in_array('sleep_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['SLEEP']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP']))),1,0,'C');
			}
			if(in_array('training_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['TRAINING']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING']))),1,0,'C');
			}
			if(in_array('sick_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['SICK_ADJ']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ']))),1,0,'C');
			}
			if(in_array('sick_used',$value_config)) {
				$pdf->Cell(10,0,(empty($total['SICK']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK']))),1,0,'C');
			}
			if(in_array('stat_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['STAT_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL']))),1,0,'C');
			}
			if(in_array('stat_used',$value_config)) {
				$pdf->Cell(10,0,(empty($total['STAT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT']))),1,0,'C');
			}
			if(in_array('vaca_hrs',$value_config)) {
				$pdf->Cell(10,0,(empty($total['VACA_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL']))),1,0,'C');
			}
			if(in_array('vaca_used',$value_config)) {
				$pdf->Cell(10,0,(empty($total['VACA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA']))),1,0,'C');
			}
			if(in_array('breaks',$value_config)) {
				$pdf->Cell(10,0,(empty($total['BREAKS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS']))),1,0,'C');
			}
			if(in_array('comment_box',$value_config)) {
				$pdf->Cell($comment_width,0,'',1,0,'C');
			}
			if(in_array('signature',$value_config) && !in_array('signature_pdf_hidden',$value_config)) {
				$pdf->Cell(35,0,'',1,0,'C');
			}
			$pdf->ln();
			$pdf->Cell($bfytd_offset,0,"Year-to-date Totals",1,0,'L');
			$pdf->Cell($ytd_offset,0,"",1,0,'C',1);
			if(in_array('sick_used',$value_config)) {
				$pdf->Cell(10,0,($timesheet_time_format == 'decimal' ? number_format($total['SICK']+$sick_taken,2) : time_decimal2time($total['SICK']+$sick_taken)),1,0,'C');
			}
			if(in_array('stat_hrs',$value_config)) {
				$pdf->Cell(10,0,($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL']+$stat_hours,2) : time_decimal2time($total['STAT_AVAIL']+$stat_hours)),1,0,'C');
			}
			if(in_array('stat_used',$value_config)) {
				$pdf->Cell(10,0,($timesheet_time_format == 'decimal' ? number_format($total['STAT']+$stat_taken,2) : time_decimal2time($total['STAT']+$stat_taken)),1,0,'C');
			}
			if(in_array('vaca_hrs',$value_config)) {
				$pdf->Cell(10,0,($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL']+$vacation_hours,2) : time_decimal2time($total['VACA_AVAIL']+$vacation_hours)),1,0,'C');
			}
			if(in_array('vaca_used',$value_config)) {
				$pdf->Cell(10,0,($timesheet_time_format == 'decimal' ? number_format($total['VACA']+$vacation_taken,2) : time_decimal2time($total['VACA']+$vacation_taken)),1,0,'C');
			}
			if(in_array('breaks',$value_config)) {
				$pdf->Cell(10,0,'',1,0,'C');
			}
			if(in_array('comment_box',$value_config)) {
				$pdf->Cell($comment_width,0,'',1,0,'C');
			}
			if(in_array('signature',$value_config) && !in_array('signature_pdf_hidden',$value_config)) {
				$pdf->Cell(35,0,'',1,0,'C');
			}
			
			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pos_offset = 40 + ($landscape_width / 2);
			if(strpos($timesheet_pdf_fields, ',Manager Initials,') !== FALSE) {
				$pdf->MultiCell($pos_offset, 1, 'Manager:', 0, 'R', 0, 0, '');
				$pdf->MultiCell(30, 1, '', $bottom_border_set, 'R', 0, (strpos($timesheet_pdf_fields, ',Coordinator Initials,') !== FALSE ? 0 : 1), '');
				$pos_offset = 30;
			}
			if(strpos($timesheet_pdf_fields, ',Coordinator Initials,') !== FALSE) {
				$pdf->MultiCell($pos_offset, 1, 'Co-ordinator:', 0, 'R', 0, 0, '');
				$pdf->MultiCell(30, 1, '', $bottom_border_set, 'R', 0, 1, '');
			}

			$pos_offset = 60 + ($landscape_width / 2);
			if(strpos($timesheet_pdf_fields, ',Manager Initials,') !== FALSE) {
				$pdf->MultiCell($pos_offset, 1, 'Initials', 0, 'R', 0, 0, '');
				$pos_offset = 60;
			}
			if(strpos($timesheet_pdf_fields, ',Coordinator Initials,') !== FALSE) {
				$pdf->MultiCell($pos_offset, 1, 'Initials', 0, 'R', 0, 0, '');
			}
			
			$filename = "timesheet_".strtolower(str_replace(' ','_',get_contact($dbc, $search_staff)))."_".$search_start_date.".pdf";
			$pdf->Output($filename, 'I');
			exit;
		}
		// Or Export as CSV
		else if($mode == 'csv') {
			
		}
	}
}
if(isset($_GET['export']) && $_GET['export']=='csv') {
	ob_clean();

	$search_staff = $_GET['search_staff'];
	$search_site = $_GET['search_site'];
	$search_start_date = $_GET['search_start_date'];
	$search_end_date = $_GET['search_end_date'];
	
	$filename = 'download/data.csv';
    $file = fopen($filename,"w");

	$options_array = array(
			'Regular Hrs.',
			'Extra Hrs.',
			'Relief Hrs.',
			'Sleep Hrs.',
			'Sick Time Adj.',
			'Sick Hrs.Taken',
			'Stat Hrs.',
			'Stat Hrs.Taken',
			'Vac Hrs.',
			'Vac Hrs.Taken'
		);
	
	$line = array('');
	fputcsv($file, $line);

	$line = array('', '', 'CLIENT NAME','', '', 'Name :',get_contact($dbc, $search_staff),'Employee No.');
	fputcsv($file, $line);

	$line = array('', '', '','', '', 'Location:',get_contact($dbc, $search_site, 'name'),'');
	fputcsv($file, $line);

	$line = array('', '', '','', '', 'Position:','','Hrs/wk:');
	fputcsv($file, $line);

	$line = array('', '', 'EMPLOYEE TIMESHEET','', '', 'Commencement Date:','','');
	fputcsv($file, $line);

	$line = array('','','','','','Pay Period From:',$search_start_date,'To:',$search_end_date);
	fputcsv($file, $line);

	$line = array('NET HOURS CARRIED');
	fputcsv($file, $line);

	$line = array('BALANCE FORWARD Y.T.D.');
	fputcsv($file, $line);

	$line = array('DATE','TIMES','REGULAR  HOURS','EXTRA HOURS','RELIEF HOURS', 'SLEEP HOURS', 'SICK TIME ADJUSTMENT', 'SICK HRS. TAKEN', 'STAT HOURS', 'STAT.HRS. TAKEN', 'VACATION HOURS', 'VACATION HRS. TAKEN', 'COMMENTS');
	fputcsv($file, $line);

	$query_check_credentials = "SELECT `date`, `staff`, `type_of_time`, MIN(`start_time`) `start_time`, MIN(`end_time`) `end_time`, SUM(`total_hrs`) `total_hrs`, GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'')) `comments` FROM time_cards WHERE `deleted`=0 AND `date` BETWEEN '$search_start_date' AND '$search_end_date' AND `staff`='$search_staff' AND '$search_site' IN (`business`,'') GROUP BY `type_of_time`, `date`, `staff`";
    $result = mysqli_query($dbc, $query_check_credentials);

	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {
		while($row = mysqli_fetch_array( $result ))
        {
        	$type = $row['type_of_time'];
        	$hours = $row['total_hrs'];
        	$hour_final = array();

        	foreach($options_array as $key=>$value) {
        		$hour_final[$key] = 0;	
        		if($type == $value) {
        			$hour_final[$key] = $hours;
        		}
        	}

			$line = array($row['date'], $row['start_time'].'-'.$row['end_time'], $hour_final[0], $hour_final[1], $hour_final[2], $hour_final[3], $hour_final[4], $hour_final[5], $hour_final[6], $hour_final[7], $hour_final[9], $hour_final[9], $row['comments']);
			fputcsv($file, $line);
		}
	}
    
    fclose($file);
    header("Location: $filename");
    exit;

}

$value = $config['settings']['Choose Fields for Time Sheets Dashboard'];
?>

<script type="text/javascript">
function viewTicket(a) {
	var ticketid = $(a).data('ticketid');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/Ticket/edit_tickets.php?edit='+ticketid+'&calendar_view=true','auto',false,true, $('#timesheet_div').outerHeight());
}
function addSignature(chk) {
	var td = $(chk).closest('td');
	var contactid = $('[name="staff_id"]').val();
	var date = chk.value;
	$('[name="time_cards_signature"]').closest('.sigPad').find('a[href="#clear"]').click();
	$(chk).prop('checked', false);
	$('#dialog-signature').dialog({
		resizable: false,
		height: "auto",
		width: ($(window).width() <= 500 ? $(window).width() : 500),
		modal: true,
		buttons: {
			"Submit": function() {
				$(this).dialog('close');
				$(td).html('Generating signature...');
				var signature = $('[name="time_cards_signature"]').val();
				$.ajax({
					url: '../Timesheet/time_cards_ajax.php?action=add_signature',
					method: 'POST',
					data: { contactid: contactid, date: date, signature: signature },
					success: function(response) {
						var img = '<img src="../Timesheet/download/'+response+'" style="height: 50%; width: auto;">';
						$(td).html(img);
					}
				});
			},
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
}
</script>

<div class="container triple-pad-bottom" id="timesheet_div">
	<div id="dialog-signature" title="Signature Box" style="display: none;">
		<?php $output_name = 'time_cards_signature';
		include('../phpsign/sign_multiple.php'); ?>
	</div>
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="timesheet_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row timesheet_div">
    	<input type="hidden" name="timesheet_time_format" value="<?= get_config($dbc, 'timesheet_time_format') ?>">
        <div class="col-md-12">

        <h1 class="">Time Sheets Dashboard
        <?php $security = get_security($dbc, 'timesheet');
        if($security['config'] > 0) {
            echo '<a href="field_config.php?from_url=time_cards.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        }
        ?>
        <img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""></h1>
		<div class="clearfix"></div>

        <form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php echo get_tabs('Time Sheets', 'Custom', array('db' => $dbc, 'field' => $value['config_field'])); ?>
        <br><br>
        <?php $search_site = '';
            $search_project = 0;
            $search_ticket = 0;
            $search_staff = $_SESSION['contactid'];
            if(!empty($_GET['search_staff'])) {
            	$search_staff = $_GET['search_staff'];
            }
            if(!empty($_GET['search_client'])) {
            	$search_staff = $_GET['search_client'];
            }
            $search_start_date = date('Y-m-01');
            $search_end_date = date('Y-m-t');

			if(!empty($_GET['search_project'])) {
				$search_project = $_GET['search_project'];    
			} 
			if(!empty($_GET['search_ticket'])) {
				$search_ticket = $_GET['search_ticket'];    
			} 
			if(!empty($_GET['search_site'])) {
				$search_site = $_GET['search_site'];    
			} 
			if(!empty($_GET['search_start_date'])) {
				$search_start_date = $_GET['search_start_date'];    
			}
			if(!empty($_GET['search_end_date'])) {
				$search_end_date = $_GET['search_end_date'];    
			}
			$current_period = isset($_GET['pay_period']) ? $_GET['pay_period'] : 0;
			$timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder');
			$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
			$timesheet_rounding = get_config($dbc, 'timesheet_rounding');
			$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;

			$value_config = explode(',',get_field_config($dbc, 'time_cards'));
			if(!in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config)) {
				$value_config = array_merge($value_config,['reg_hrs','extra_hrs','relief_hrs','sleep_hrs','sick_hrs','sick_used','stat_hrs','stat_used','vaca_hrs','vaca_used']);
			}
			include('pay_period_dates.php'); ?>  

				<?php if(in_array('search_staff',$value_config) && check_subtab_persmission($dbc, 'timesheet', ROLE, 'search_staff')) { ?>
	                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
	                  <label for="site_name" class="control-label">Search By Staff:</label>
	                </div>
	                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
	                  	<select name="search_staff" class="chosen-select-deselect">
	                  		<option></option>
	                  		<?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT distinct(`time_cards`.`staff`), `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`status` FROM `time_cards` LEFT JOIN `contacts` ON `contacts`.`contactid` = `time_cards`.`staff` WHERE `time_cards`.`staff` > 0 AND `contacts`.`deleted`=0 AND `contacts`.`category` IN (".STAFF_CATS.") AND `contacts`.`staff_category` NOT IN (".STAFF_CATS_HIDE.")"));
			                foreach($query as $staff_row) { ?>
			                    <option <?php if (strpos(','.$search_staff.',', ','.$staff_row['contactid'].',') !== FALSE) { echo " selected"; } ?> value='<?php echo  $staff_row['contactid']; ?>' ><?php echo $staff_row['full_name']; ?></option><?php
			                } ?>
	                  	</select>
	                  </div>
				<?php } ?>

                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                  <label for="site_name" class="control-label">Search By Start Date:</label>
                </div>
                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                      <input style="width: 100%;" name="search_start_date" value="<?php echo $search_start_date; ?>" type="text" class="form-control datepicker">
                  </div>

                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                  <label for="site_name" class="control-label">Search By End Date:</label>
                </div>
                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                      <input style="width: 100%;" name="search_end_date" value="<?php echo $search_end_date; ?>" type="text" class="form-control datepicker">
                  </div>

				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                  <label for="site_name" class="control-label">Search By Site:</label>
                </div>
                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                      <select data-placeholder="Select a Site" name="search_site" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                      <option value=""></option>
                      <?php
                        $query = mysqli_query($dbc,"SELECT `contactid`, CONCAT(IFNULL(`site_name`,''),IF(IFNULL(`site_name`,'') != '' AND IFNULL(`display_name`,'') != '',': ',''),IFNULL(`display_name`,'')) display_name FROM `contacts` WHERE `category`='Sites' AND `deleted`=0");
                        while($row1 = mysqli_fetch_array($query)) {
                        ?><option <?php if ($row1['contactid'] == $search_site) { echo " selected"; } ?> value='<?php echo  $row1['contactid']; ?>' ><?php echo $row1['display_name']; ?></option>
                    <?php   }
                    ?>
                    </select>
                  </div>

				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                  <label for="site_name" class="control-label">Search By <?= PROJECT_NOUN ?>:</label>
                </div>
                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                      <select data-placeholder="Select <?= PROJECT_NOUN ?>" name="search_project" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                      <option value="0"></option>
                      <?php
                        $query = mysqli_query($dbc,"SELECT `projectid`, `projecttype`, `project_name`, `status`, `businessid`, `clientid` FROM `project` WHERE `deleted`=0 AND `projectid` IN (SELECT `projectid` FROM `time_cards` WHERE `deleted`=0) ORDER BY `projectid`");
                        while($row1 = mysqli_fetch_array($query)) {
                        ?><option <?php if ($row1['projectid'] == $search_project) { echo " selected"; } ?> value='<?php echo  $row1['projectid']; ?>' ><?= get_project_label($dbc, $row1) ?></option>
                    <?php   }
                    ?>
                    </select>
                  </div>

				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
                  <label for="site_name" class="control-label">Search By <?= TICKET_NOUN ?>:</label>
                </div>
                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
                      <select data-placeholder="Select <?= TICKET_NOUN ?>" name="search_ticket" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                      <option value="0"></option>
                      <?php $query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `time_cards` WHERE `deleted`=0) ORDER BY `projectid`");
                        while($row1 = mysqli_fetch_array($query)) {
                        ?><option <?php if ($row1['ticketid'] == $search_ticket) { echo " selected"; } ?> value='<?php echo  $row1['ticketid']; ?>' ><?= get_ticket_label($dbc, $row1); ?></option>
                    <?php   }
                    ?>
                    </select>
                  </div>

                  <?php if(in_array('search_client', $value_config)) { ?>
					<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
	                  <label for="site_name" class="control-label">Search By Client:</label>
	                </div>
	                  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
	                      <select data-placeholder="Select a Client" name="search_client" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
	                      <option></option>
	                      <?php 
							$timesheet_client_category = get_config($dbc, 'timesheet_client_category');
							$client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '$timesheet_client_category' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
							foreach ($client_list as $client) {
								echo '<option value="'.$client.'" '.($_GET['search_client'] == $client ? 'selected' : '').'>'.(!empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client)).'</option>';
							} ?>
	                    ?>
	                    </select>
	                  </div>
                  <?php } ?>
				  
                <div class="form-group">
                    <a href="?pay_period=<?= $current_period + 1 ?>&search_site=<?= $search_site ?>&search_project=<?= $search_project ?>&search_ticket=<?= $search_ticket ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Next Pay Period</a>
                    <a href="?pay_period=<?= $current_period - 1 ?>&search_site=<?= $search_site ?>&search_project=<?= $search_project ?>&search_ticket=<?= $search_ticket ?>" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Prior Pay Period</a>
                    <a href="time_cards.php" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Display Default</a>
                    <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block pull-right">Search</button>
                </div>
			</form>

        <br><br>
		<?php $layout = get_config($dbc, 'timesheet_layout');
			$highlight = get_config($dbc, 'timesheet_highlight');
			$mg_highlight = get_config($dbc, 'timesheet_manager');
			$submit_mode = get_config($dbc, 'timesheet_submit_mode');
			$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
			$timesheet_approval_status_comments = get_config($dbc, 'timesheet_approval_status_comments');
			$schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT `scheduled_hours`, `schedule_days` FROM `contacts` WHERE `contactid`='$search_staff'"));
			$schedule_hrs = explode('*',$schedule['scheduled_hours']);
			$schedule_days = explode(',',$schedule['schedule_days']);
			$schedule_list = [0=>'---',1=>'---',2=>'---',3=>'---',4=>'---',5=>'---',6=>'---'];
			foreach($schedule_days as $key => $day_of_week) {
				$schedule_list[$day_of_week] = $schedule_hrs[$day_of_week];
			}
			
			$start_of_year = date('Y-01-01', strtotime($search_start_date));
			$sql = "SELECT IFNULL(SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)),0) SICK_HRS,
				IFNULL(SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)),0) STAT_AVAIL,
				IFNULL(SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)),0) STAT_HRS,
				IFNULL(SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)),0) VACA_AVAIL,
				IFNULL(SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)),0) VACA_HRS
				FROM `time_cards` WHERE `staff`='$search_staff' AND `date` < '$search_start_date' AND `date` >= '$start_of_year' AND `deleted`=0";
			$year_to_date = mysqli_fetch_array(mysqli_query($dbc, $sql));
			
			$stat_hours = $year_to_date['STAT_AVAIL'];
			$stat_taken = $year_to_date['STAT_HRS'];
			$vacation_hours = $year_to_date['VACA_AVAIL'];
			$vacation_taken = $year_to_date['VACA_HRS'];
			$sick_taken = $year_to_date['SICK_HRS']; ?>

			<div class="pull-right" style="height:1.5em; margin:0.5em;"><a target="_blank" href="time_cards.php?export=pdf&search_site=<?php echo $search_site; ?>&search_staff=<?php echo $search_staff; ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>" title="PDF"><img src="<?php echo WEBSITE_URL; ?>/img/pdf.png" style="height:100%; margin:0;" /></a>
			- <a href="time_cards.php?export=csv&search_site=<?php echo $search_site; ?>&search_staff=<?php echo $search_staff; ?>&search_start_date=<?php echo $search_start_date; ?>&search_end_date=<?php echo $search_end_date; ?>" title="CSV"><img src="<?php echo WEBSITE_URL; ?>/img/csv.png" style="height:100%; margin:0;" /></a></div>
			<div class="clearfix"></div>
            
            <form name="timesheet" method="POST" action="add_time_cards.php">
				<input type="hidden" name="staff_id" value="<?php echo $search_staff; ?>">
				<input type="hidden" name="site_id" value="<?php echo $search_site; ?>">
				<input type="hidden" name="projectid" value="<?php echo $search_project; ?>"><?php
                
                if($security['edit'] > 0 && $search_staff != '' && $layout != 'table_add_button'):
                    $approval_result = mysqli_query($dbc, "SELECT * FROM `field_config_supervisor` WHERE `staff_list` like '%,".$_SESSION['contactid'].",%'");
                    
                    if($approval_result = mysqli_fetch_array($approval_result)) {
                        $submit_label = 'for Approval';
                    } else {
                        $submit_label = 'to Payroll';
                    }
				
                    if ($layout == 'rate_card') {
                        $submit_approval = 'rate_approval';
                        $submit_timesheet = 'rate_timesheet';
                    } else if($layout == 'position_dropdown') {
						$submit_approval = 'position_approval';
						$submit_timesheet = 'positions';
                    } else {
                        $submit_approval = 'approval';
                        $submit_timesheet = 'timesheet';
                    }
                    
					if($submit_mode != 'auto') {
						echo '<button type="submit" value="'.$submit_approval.'" name="submit" class="btn brand-btn mobile-block pull-right">Submit Time Sheet '.$submit_label.'</button>';
					}
                    echo '<button type="submit" value="'.$submit_timesheet.'" name="submit" class="btn brand-btn mobile-block pull-right">Save Time Sheet</button>';
                    echo '<div class="clearfix"></div>';
                endif;
			
			if($layout == '' || $layout == 'multi_line'): ?>
				<script>
				$(document).ready(function() {
					$('.add-row').click(function() {
						var line = $(this).closest('tr');
						destroyInputs('#no-more-tables');
						var new_line = line.clone();
						new_line.find('input').val('');
						new_line.find('span').remove();
						line.after(new_line);
						initInputs('#no-more-tables');
					});
					$('.rem-row').click(function() {
						var line = $(this).closest('tr');
						line.find('[name^=deleted]').val(1).change();
						line.hide();
					});
					$('.comment-row').click(function() {
						var line = $(this).closest('tr');
						line.find('[name^=add_comment]').show();
					});
				});
				</script>
				<div id="no-more-tables">
					<table class='table table-bordered'>
						<tr class='hidden-xs hidden-sm'>
							<td colspan="<?= 1 + (in_array('ticketid',$value_config) ? 1 : 0) + (in_array('show_hours',$value_config) ? 1 : 0) + (in_array('total_tracked_hrs',$value_config) ? 1 : 0) + (in_array('start_time',$value_config) ? 1 : 0)
								+ (in_array('end_time',$value_config) ? 1 : 0) + (in_array('planned_hrs',$value_config) ? 1 : 0) + (in_array('tracked_hrs',$value_config) ? 1 : 0) + (in_array('total_tracked_time',$value_config) ? 1 : 0)
								+ (in_array('reg_hrs',$value_config) ? 1 : 0) + (in_array('payable_hrs',$value_config) ? 1 : 0) + (in_array('direct_hrs',$value_config) ? 1 : 0) + (in_array('indirect_hrs',$value_config) ? 1 : 0)
								+ (in_array('extra_hrs',$value_config) ? 1 : 0) + (in_array('relief_hrs',$value_config) ? 1 : 0) + (in_array('sleep_hrs',$value_config) ? 1 : 0) + (in_array('training_hrs',$value_config) ? 1 : 0)
								+ (in_array('sick_hrs',$value_config) ? 1 : 0) ?>">Balance Forward Y.T.D.</td>
							<?php if(in_array('sick_used',$value_config)) { ?><td style='text-align:center;'><?php echo $sick_taken; ?></td><?php } ?>
							<?php if(in_array('stat_hrs',$value_config)) { ?><td style='text-align:center;'><?php echo $stat_hours; ?></td><?php } ?>
							<?php if(in_array('stat_used',$value_config)) { ?><td style='text-align:center;'><?php echo $stat_taken; ?></td><?php } ?>
							<?php if(in_array('vaca_hrs',$value_config)) { ?><td style='text-align:center;'><?php echo $vacation_hours; ?></td><?php } ?>
							<?php if(in_array('vaca_used',$value_config)) { ?><td style='text-align:center;'><?php echo $vacation_taken; ?></td><?php } ?>
							<?php if(in_array('breaks',$value_config)) { ?><td style='text-align:center;'></td><?php } ?>
							<?php if(in_array('view_ticket',$value_config)) { ?><td style='text-align:center;'></td><?php } ?>
							<?php if(in_array('comment_box',$value_config)) { ?><td style='text-align:center;'></td><?php } ?>
							<?php if(in_array('signature',$value_config)) { ?><td style='text-align:center;'></td><?php } ?>
						</tr>
						<tr class='hidden-xs hidden-sm'>
							<th style='text-align:center; vertical-align:bottom; width:8em;'><div>Date</div></th>
							<?php if(in_array('ticketid',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div><?= TICKET_NOUN ?></div></th><?php } ?>
							<?php if(in_array('show_hours',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>Hours</div></th><?php } ?>
							<?php if(in_array('start_time',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>Start<br />Time</div></th><?php } ?>
							<?php if(in_array('end_time',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>End<br />Time</div></th><?php } ?>
							<?php if(in_array('total_tracked_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Total Tracked<br />Hours</div></th><?php } ?>
							<?php if(in_array('planned_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>Planned<br />Hours</div></th><?php } ?>
							<?php if(in_array('tracked_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>Tracked<br />Hours</div></th><?php } ?>
							<?php if(in_array('total_tracked_time',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Total Tracked<br />Time</div></th><?php } ?>
							<?php if(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div><?= in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular' ?><br />Hours</div></th><?php } ?>
							<?php if(in_array('direct_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Direct<br />Hours</div></th><?php } ?>
							<?php if(in_array('indirect_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Indirect<br />Hours</div></th><?php } ?>
							<?php if(in_array('extra_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Extra<br />Hours</div></th><?php } ?>
							<?php if(in_array('relief_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Relief<br />Hours</div></th><?php } ?>
							<?php if(in_array('sleep_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sleep<br />Hours</div></th><?php } ?>
							<?php if(in_array('training_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Training<br />Hours</div></th><?php } ?>
							<?php if(in_array('sick_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sick Time<br />Adjustment</div></th><?php } ?>
							<?php if(in_array('sick_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sick Hrs.<br />Taken</div></th><?php } ?>
							<?php if(in_array('stat_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Stat<br />Hours</div></th><?php } ?>
							<?php if(in_array('stat_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Stat. Hrs.<br />Taken</div></th><?php } ?>
							<?php if(in_array('vaca_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Vacation<br />Hours</div></th><?php } ?>
							<?php if(in_array('vaca_used',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Vacation<br />Hrs. Taken</div></th><?php } ?>
							<?php if(in_array('breaks',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Breaks</div></th><?php } ?>
							<?php if(in_array('view_ticket',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div><?= TICKET_NOUN ?></div></th><?php } ?>
							<?php if(in_array('comment_box',$value_config)) { ?><th style='text-align:center; vertical-align:bottom;'><div>Comments</div></th><?php } ?>
							<?php if(in_array('signature',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Parent/Guardian Signature</div></th><?php } ?>
						</tr><?php
                        $sql = "SELECT * FROM `time_cards_signature` WHERE `contactid` = '$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date'";
                        $result = mysqli_query($dbc, $sql);
                        $all_signatures = [];
                        while ($row = mysqli_fetch_array($result)) {
                        	$all_signatures[$row['date']] = $row['signature'];
                        }

                        if ( empty($search_site) ) {
                            $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
							SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
							SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
							SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
							SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
							SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
							SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
							GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'') SEPARATOR ', ') COMMENTS, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS,
							SUM(`timer_tracked`) TRACKED_HRS,
							SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `ticket_attached_id`, `manager_approvals`, `coord_approvals`, `manager_name`, `coordinator_name`, `ticketid`, `start_time`, `end_time` FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `deleted`=0 GROUP BY `date`";
                        } else {
                            $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
							SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
							SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
							SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
							SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
							SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
							SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
							GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'') SEPARATOR ', ') COMMENTS, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS,
							SUM(`timer_tracked`) TRACKED_HRS,
							SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `ticket_attached_id`, `manager_approvals`, `coord_approvals`, `manager_name`, `coordinator_name`, `ticketid`, `start_time`, `end_time` FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' AND `deleted`=0 GROUP BY `date`";
                        }
						if($layout == 'multi_line') {
							$sql .= ", `time_cards_id`";
						}
						$sql .= " ORDER BY `date`, IFNULL(STR_TO_DATE(`start_time`, '%l:%i %p'),STR_TO_DATE(`start_time`, '%H:%i')) ASC, IFNULL(STR_TO_DATE(`end_time`, '%l:%i %p'),STR_TO_DATE(`end_time`, '%H:%i')) ASC";
						$result = mysqli_query($dbc, $sql);
						$date = $search_start_date;
						$row = mysqli_fetch_array($result);
						$total = ['REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0];
						while(strtotime($date) <= strtotime($search_end_date)) {
							$attached_ticketid = 0;
							$timecardid = 0;
							$ticket_attached_id = 0;
							$approval_status = '';
							$hl_colour = '';
							$start_time = '';
							$end_time = '';
							if($row['date'] == $date) {
								$hl_colour = ($row['MANAGER'] > 0 && $mg_highlight != '#000000' && $mg_highlight != '' ? 'background-color:'.$mg_highlight.';' : ($row['HIGHLIGHT'] > 0 && $highlight != '#000000' && $highlight != '' ? 'background-color:'.$highlight.';' : ''));
								foreach($config['hours_types'] as $hours_type) {
									if($row[$hours_type] > 0) {
										switch($timesheet_rounding) {
											case 'up':
												$row[$hours_type] = ceil($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
												break;
											case 'down':
												$row[$hours_type] = floor($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
												break;
											case 'nearest':
												$row[$hours_type] = round($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
												break;
										}
									}
								}
								$hrs = ['REG'=>$row['REG_HRS'],'DIRECT'=>$row['DIRECT_HRS'],'INDIRECT'=>$row['INDIRECT_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],
									'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'TRACKED_HRS'=>$row['TRACKED_HRS'],'BREAKS'=>$row['BREAKS']];
								$comments = '';
								if(in_array('project',$value_config)) {
									foreach(explode(',',$row['PROJECTS']) as $projectid) {
										if($projectid > 0) {
											$comments .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"))).'<br />';
										}
									}
								}
								if(in_array('search_client',$value_config)) {
									foreach(explode(',',$row['CLIENTS']) as $clientid) {
										if($clientid > 0) {
											$comments .= get_contact($dbc, $clientid).'<br />';
										}
									}
								}
								$comments .= html_entity_decode($row['COMMENTS']);
								if(empty(strip_tags($comments))) {
									$comments = $timesheet_comment_placeholder;
								}
								foreach($total as $key => $value) {
									$total[$key] += $hrs[$key];
								}
								$timecardid = $row['time_cards_id'];
								$ticket_attached_id = $row['ticket_attached_id'];
								$attached_ticketid = $row['ticketid'];
								$start_time = $row['start_time'];
								$end_time = $row['end_time'];
											
								if($timesheet_approval_status_comments == 1) {
									if(!empty(trim($row['manager_approvals'],','))) {
										$approval_list = [];
										foreach(explode(',',$row['manager_approvals']) as $approval_manager) {
											if($approval_manager > 0) {
												$approval_list[] = get_contact($dbc, $approval_manager);
											}
										}
										$approval_status = 'Approved by '.implode(', ', $approval_list).'<br />';
									} else if(!empty($row['manager_name'])) {
										$approval_status = 'Approved by '.$row['manager_name'].'<br />';
									} else {
										$approval_status = 'Waiting for Approval<br />';
									}
								}

								if(in_array('training_hrs',$value_config) && $timecardid > 0) {
									if(is_training_hrs($dbc, $timecardid)) {
										$hrs['TRAINING'] = $hrs['REG'];
										$hrs['REG'] = 0;
										$total['REG'] -= $hrs['TRAINING'];
										$total['TRAINING'] += $hrs['TRAINING'];
									} else {
										$hrs['TRAINING'] = 0;
									}
								} else {
									$hrs['TRAINING'] = 0;
								}

								$row = mysqli_fetch_array($result);
							} else {
								$hrs = ['REG'=>0,'DIRECT'=>0,'INDIRECT'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0];
								$comments = '';
							}
							// $hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(`dayoff_type` != '',`dayoff_type`,CONCAT(`starttime`,' - ',`endtime`)) FROM `contacts_shifts` WHERE `deleted`=0 AND `contactid`='$search_staff' AND '$date' BETWEEN `startdate` AND `enddate` ORDER BY `startdate` DESC"))[0];
							$day_of_week = date('l', strtotime($date));
							$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date, 'all');
							if(!empty($shifts)) {
								$hours = '';
								$hours_off = '';
								foreach ($shifts as $shift) {
									$hours .= $shift['starttime'].' - '.$shift['endtime'].'<br>';
									$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];
									
								}
								$hours = $hours_off == '' ? $hours : $hours_off;
							} else {
								$hours = $schedule_list[date('w',strtotime($date))];
							}
							$mod = '';
							$mod_class = '';
							if($date < $last_period) {
								// $mod = 'readonly';
								// $mod_class = 'readonly-block ';
							}
							//Planned & Tracked Hours
							$ticket_labels = get_ticket_labels($dbc, $date, $search_staff, $layout, $timecardid);
							$planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff, $layout, $timecardid);
							$tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff, $layout, $timecardid);
							$total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff, $layout, $timecardid);
							echo '<tr style="'.$hl_colour.'">'.
								($layout == 'multi_line' ? '<input type="hidden" name="time_cards_id_'.date('Y_m_d', strtotime($date)).'[]" value="'.$timecardid.'"><input type="hidden" name="ticket_attached_id_'.date('Y-m-d', strtotime($date)).'[]" value="'.$ticket_attached_id.'">' : '').
								'<input type="hidden" name="deleted_'.date('Y_m_d', strtotime($date)).'[]" value="0">
								<td data-title="Date" style="text-align:center" class="theme-color-border-bottom">'.$date.'</td>
								'.(in_array('ticketid',$value_config) ? '<td data-title="'.TICKET_NOUN.'" class="theme-color-border-bottom">'.$ticket_labels.'</td>' : '').'
								'.(in_array('show_hours',$value_config) ? '<td data-title="Hours" style="text-align:center" class="theme-color-border-bottom">'.$hours.'</td>' : '').'
								'.(in_array('start_time',$value_config) ? '<td data-title="Start Time" style="text-align:center" class="theme-color-border-bottom">'.$start_time.'</td>' : '').'
								'.(in_array('end_time',$value_config) ? '<td data-title="End Time" style="text-align:center" class="theme-color-border-bottom">'.$end_time.'</td>' : '').'
								'.(in_array('total_tracked_hrs',$value_config) ? '<td data-title="Total Tracked Hours" style="text-align:center" class="theme-color-border-bottom">'.(empty($hrs['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))).'</td>' : '').'
								'.(in_array('planned_hrs',$value_config) ? '<td data-title="Planned Hours" style="text-align:center" class="theme-color-border-bottom">'.$planned_hrs.'</td>' : '').'
								'.(in_array('tracked_hrs',$value_config) ? '<td data-title="Tracked Hours" style="text-align:center" class="theme-color-border-bottom">'.$tracked_hrs.'</td>' : '').'
								'.(in_array('total_tracked_time',$value_config) ? '<td data-title="Total Tracked Time" style="text-align:center" class="theme-color-border-bottom">'.$total_tracked_time.'</td>' : '').'
								'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="regular_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).'" class="form-control '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('direct_hrs',$value_config) ? '<td data-title="Direct Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="direct_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['DIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DIRECT'],2) : time_decimal2time($hrs['DIRECT']))).'" class="form-control '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('indirect_hrs',$value_config) ? '<td data-title="Indirect Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="indirect_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['INDIRECT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['INDIRECT'],2) : time_decimal2time($hrs['INDIRECT']))).'" class="form-control '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="extra_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['EXTRA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['EXTRA'],2) : time_decimal2time($hrs['EXTRA']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="relief_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['RELIEF']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['RELIEF'],2) : time_decimal2time($hrs['RELIEF']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="sleep_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['SLEEP']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SLEEP'],2) : time_decimal2time($hrs['SLEEP']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="training_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['TRAINING']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRAINING'],2) : time_decimal2time($hrs['TRAINING']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="sickadj_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['SICK_ADJ']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK_ADJ'],2) : time_decimal2time($hrs['SICK_ADJ']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="sick_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['SICK']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK'],2) : time_decimal2time($hrs['SICK']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="statavail_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['STAT_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT_AVAIL'],2) : time_decimal2time($hrs['STAT_AVAIL']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="stat_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['STAT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT'],2) : time_decimal2time($hrs['STAT']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="vacavail_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['VACA_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA_AVAIL'],2) : time_decimal2time($hrs['VACA_AVAIL']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken" style="text-align:center" class="theme-color-border-bottom"><input type="text" '.$mod.' name="vaca_'.date('Y_m_d', strtotime($date)).'[]" value="'.(empty($hrs['VACA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA'],2) : time_decimal2time($hrs['VACA']))).'" class="form-control" '.$mod_class.($security['edit'] > 0 ? 'timepicker"' : '" readonly').'></td>' : '').'
								'.(in_array('breaks',$value_config) ? '<td data-title="Breaks" style="text-align:center" class="theme-color-border-bottom">'.(empty($hrs['BREAKS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['BREAKS'],2) : time_decimal2time($hrs['BREAKS']))).'</td>' : '').'
								'.(in_array('view_ticket',$value_config) ? '<td data-title="'.TICKET_NOUN.'" style="text-align:center" class="theme-color-border-bottom">'.(!empty($attached_ticketid) ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/edit_tickets.php?edit='.$attached_ticketid.'&calendar_view=true\',\'auto\',false,true, $(\'#timesheet_div\').outerHeight()); return false;" data-ticketid="'.$attached_ticketid.'" class="view_ticket" '.($attached_ticketid > 0 ? '' : 'style="display:none;"').'>View</a>' : '').'</td>' : '').'
								'.(in_array('comment_box',$value_config) ? '<td data-title="Comments" class="theme-color-border-bottom"><span>'.$approval_status.$comments.'</span>'.($layout == 'multi_line' && $security['edit'] > 0 ? '<img class="inline-img add-row pull-right" src="../img/icons/ROOK-add-icon.png"><img class="inline-img rem-row pull-right" src="../img/remove.png">' : '').'<img class="inline-img comment-row pull-right" src="../img/icons/ROOK-reply-icon.png"><input type="text" class="form-control" name="add_comment_'.date('Y_m_d', strtotime($date)).'[]" style="display:none;"></td>' : '').'
								'.(in_array('signature',$value_config) ? '<td data-title="Signature" style="text-align:center" class="theme-color-border-bottom">'.(!empty($all_signatures[$date]) ? '<img src="../Timesheet/download/'.$all_signatures[$date].'" style="height: 50%; width: auto;">' : ($security['edit'] > 0 ? '<label class="form-checkbox"><input type="checkbox" name="add_signature" onclick="addSignature(this);" value="'.$date.'"></label>' : '')).'</td>' : '').
							'</tr>';
                            
							if($layout != 'multi_line' || $date != $row['date']) {
								$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
							}
						}
						$colspan = 2;
						if(in_array('ticketid',$value_config)) {
							$colspan++;
						}
						if(in_array('planned_hrs',$value_config)) {
							$colspan++;
						}
						if(in_array('tracked_hrs',$value_config)) {
							$colspan++;
						}
						if(in_array('total_tracked_time',$value_config)) {
							$colspan++;
						}
						if(in_array('start_time',$value_config)) {
							$colspan++;
						}
						if(in_array('end_time',$value_config)) {
							$colspan++;
						}
						if(!in_array('show_hours',$value_config)) {
							$colspan--;
						}
						echo '<tr>
							<td data-title="" colspan="'.$colspan.'">Totals</td>
							'.(in_array('total_tracked_hrs',$value_config) ? '<td data-title="Total Tracked Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['TRACKED_HRS'],2) : time_decimal2time($total['TRACKED_HRS'])).'</td>' : '').'
							'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).'</td>' : '').'
							'.(in_array('direct_hrs',$value_config) ? '<td data-title="Direct Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['DIRECT'],2) : time_decimal2time($total['DIRECT'])).'</td>' : '').'
							'.(in_array('indirect_hrs',$value_config) ? '<td data-title="Indirect Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['INDIRECT'],2) : time_decimal2time($total['INDIRECT'])).'</td>' : '').'
							'.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA'])).'</td>' : '').'
							'.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF'])).'</td>' : '').'
							'.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP'])).'</td>' : '').'
							'.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING'])).'</td>' : '').'
							'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ'])).'</td>' : '').'
							'.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK'])).'</td>' : '').'
							'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL'])).'</td>' : '').'
							'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT'])).'</td>' : '').'
							'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL'])).'</td>' : '').'
							'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA'])).'</td>' : '').'
							'.(in_array('breaks',$value_config) ? '<td data-title="Breaks" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS'])).'</td>' : '').'
							'.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('comment_box',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('signature',$value_config) ? '<td data-title=""></td>' : '').'
						</tr>';
						echo '<tr>
							<td colspan="'.$colspan.'">Year-to-date Totals</td>
							'.(in_array('total_tracked_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('direct_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('indirect_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('extra_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('relief_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('sleep_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('training_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('sick_hrs',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK']+$sick_taken,2) : time_decimal2time($total['SICK']+$sick_taken)).'</td>' : '').'
							'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL']+$stat_hours,2) : time_decimal2time($total['STAT_AVAIL']+$stat_hours)).'</td>' : '').'
							'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT']+$stat_taken,2) : time_decimal2time($total['STAT']+$stat_taken)).'</td>' : '').'
							'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL']+$vacation_hours,2) : time_decimal2time($total['VACA_AVAIL']+$vacation_hours)).'</td>' : '').'
							'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken" class="time_string">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA']+$vacation_taken,2) : time_decimal2time($total['VACA']+$vacation_taken)).'</td>' : '').'
							'.(in_array('breaks',$value_config) ? '<td data-title="Breaks"></td>' : '').'
							'.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('comment_box',$value_config) ? '<td data-title=""></td>' : '').'
							'.(in_array('signature',$value_config) ? '<td></td>' : '').'
						</tr>'; ?>
					</table>

				<?php
				$tb_field = $value['config_field'];
				echo '</div>'; ?>
			<?php elseif($layout == 'position_columns'): ?>
			<?php elseif($layout == 'position_dropdown' || $layout == 'ticket_task'): ?>
				<script>
				$(document).ready(function() {
					checkTimeOverlaps();
					initLines();
				});
				$(document).on('change', '[name="start_time[]"],[name="end_time[]"]', function() { checkTimeOverlaps(); });
				function getTasks(sel) {
					var tasks = $(sel).find('option:selected').data('tasks');
					var tasks_sel = $(sel).closest('tr').find('[name="type_of_time[]"]');
					var tasks_html = '<option></option>';
					if(tasks != undefined) {
						tasks.forEach(function(task) {
							tasks_html += '<option value="'+task+'">'+task+'</option>';
						});
					}
					$(tasks_sel).html(tasks_html).trigger('change.select2');
					if($(sel).val() != undefined && $(sel).val() != '') {
						$(sel).closest('tr').find('.view_ticket').data('ticketid', $(sel).val()).show();
					} else {
						$(sel).closest('tr').find('.view_ticket').data('ticketid', '').hide();
					}
				}
				function checkDrivingTime(chk) {
					var block = $(chk).closest('tr');
					if($(chk).is(':checked')) {
						$(block).find('.ticket_task_td').each(function() {
							$(this).find('select').val('').trigger('change');
							$(this).addClass('readonly-block');
						});
					} else {
						$(block).find('.ticket_task_td').removeClass('readonly-block');
					}
				}
				function initLines() {
					$('.add-row').off('click').click(function() {
						var line = $(this).closest('tr');
						destroyInputs('#no-more-tables');
						var new_line = line.clone();
						new_line.find('input[name^=hours],select[name^=ticketid],select[name^=type_oof_time],input[name^=start_time],input[name^=end_time],input[name^=total_hrs]').val('');
						new_line.find('input.driving_time').prop('checked',false);
						new_line.find('.ticket_task_td').removeClass('readonly-block');
						new_line.find('select').val('');
						new_line.find('span').remove();
						line.after(new_line);
						initInputs('#no-more-tables');
						initLines();
					});
					$('.rem-row').off('click').click(function() {
						var line = $(this).closest('tr');
						line.find('[name^=deleted]').val(1).change();
						line.hide();
					});
					$('.comment-row').off('click').click(function() {
						var line = $(this).closest('tr');
						line.find('[name^=comment_box]').show().focus();
					});
				}
				function checkTimeOverlaps() {
					<?php if(in_array('time_overlaps',$value_config)) { ?>
						$('.timesheet_div table tr').css('background-color', '');
						var time_list = [];
						var date_list = [];
						$('.timesheet_div table').each(function() {
							$(this).find('tr').each(function() {
								var date = $(this).find('[name="date[]"]').val();
								if(time_list[date] == undefined) {
									time_list[date] = [];
								}
								if(date_list.indexOf(date) == -1) {
									date_list.push(date);
								}

								var start_time = '';
								var end_time = '';
								if($(this).find('[name="start_time[]"]').val() != undefined && $(this).find('[name="start_time[]"]').val() != '' && $(this).find('[name="end_time[]"]').val() != undefined && $(this).find('[name="end_time[]"]').val() != '') {
									time_list[date].push($(this));
								}
							});
						});
						date_list.forEach(function(date) {
							time_list[date].forEach(function(tr) {
								$(tr).data('current_row', 1);
								start_time = new Date(date+' '+$(tr).find('[name="start_time[]"]').val());
								end_time = new Date(date+' '+$(tr).find('[name="end_time[]"]').val());
								time_list[date].forEach(function(tr2) {
									if($(tr2).data('current_row') != 1) {
										start_time2 = new Date(date+' '+$(tr2).find('[name="start_time[]"]').val());
										end_time2 = new Date(date+' '+$(tr2).find('[name="end_time[]"]').val())
										if((start_time.getTime() > start_time2.getTime() && start_time.getTime() < end_time2.getTime()) || (end_time.getTime() > start_time2.getTime() && end_time.getTime() < end_time2.getTime())) {
											$(tr).css('background-color', 'red');
										}
									}
								});
								$(tr).data('current_row', 0);
							});
						});
					<?php } ?>
				}
				</script>
				<div id="no-more-tables">
					<table class='table table-bordered timesheet_table'>
						<tr class='hidden-xs hidden-sm'>
							<th style='text-align:center; vertical-align:bottom; width:<?= (in_array('editable_dates',$value_config) ? '15em;' : '7em;') ?>'><div>Date</div></th>
							<?php $total_colspan = 2; ?>
							<?php if(in_array('schedule',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:9em;'><div>Schedule</div></th><?php } ?>
							<?php if(in_array('scheduled',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div>Scheduled Hours</div></th><?php } ?>
							<?php if(in_array('start_time',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div>Start Time</div></th><?php } ?>
							<?php if(in_array('end_time',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div>End Time</div></th><?php } ?>
							<?php if(in_array('start_time_editable',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div>Start Time</div></th><?php } ?>
							<?php if(in_array('end_time_editable',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div>End Time</div></th><?php } ?>
							<?php if(in_array('start_day_tile',$value_config)) { $total_colspan++; ?><th style='text-align:center; vertical-align:bottom; width:10em;'><div><?= $timesheet_start_tile ?></div></th><?php } ?>
							<?php if($layout == 'ticket_task') { $total_colspan++; ?>
								<th style='text-align:center; vertical-align:bottom; width:12em;'><div><?= TICKET_NOUN ?></div></th>
								<th style='text-align:center; vertical-align:bottom; width:12em;'><div>Task</div></th>
							<?php } else { ?>
								<th style='text-align:center; vertical-align:bottom; width:12em;'><div>Position</div></th>
							<?php } ?>
							<?php if(in_array('total_tracked_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:6em;'><div>Time Tracked</div></th><?php } ?>
							<th style='text-align:center; vertical-align:bottom; width:6em;'><div>Hours</div></th>
							<?php if(in_array('vaca_hrs',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:6em;'><div>Vacation Hours</div></th><?php } ?>
							<?php if(in_array('view_ticket',$value_config)) { ?><th style='text-align:center; vertical-align:bottom; width:6em;'><div><?= TICKET_NOUN ?></div></th><?php } ?>
							<th style='text-align:center; vertical-align:bottom;'><div>Comments</div></th>
						</tr>
						<?php $position_list = $_SERVER['DBC']->query("SELECT `position` FROM (SELECT `name` `position` FROM `positions` WHERE `deleted`=0 UNION SELECT `type_of_time` `position` FROM `time_cards` WHERE `deleted`=0) `list` WHERE IFNULL(`position`,'') != '' GROUP BY `position` ORDER BY `position`")->fetch_all();
						$ticket_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted` = 0 AND `status` != 'Archive'"),MYSQLI_ASSOC);
						$total = 0;
						$total_vac = 0;
						$limits = "AND `staff`='$search_staff'";
						if($search_site > 0) {
							$limits .= " AND IFNULL(`business`,'') LIKE '%$search_site%'";
						}
						if($search_ticket > 0) {
							$limits .= " AND IFNULL(`ticketid`,'') = '$search_ticket'";
						}
						$result = get_time_sheet($search_start_date, $search_end_date, $limits, ', `staff`, `date`, `time_cards_id`');
						$date = $search_start_date;
						$i = 0;
						while(strtotime($date) <= strtotime($search_end_date)) {
							$timecardid = 0;
							$driving_time = '';
							$hl_colour = '';
							if($result[$i]['date'] == $date) {
								$row = $result[$i++];
								$hl_colour = ($row['MANAGER'] > 0 && $mg_highlight != '#000000' && $mg_highlight != '' ? 'background-color:'.$mg_highlight.';' : ($row['HIGHLIGHT'] > 0 && $highlight != '#000000' && $highlight != '' ? 'background-color:'.$highlight.';' : ''));
								$comments = '';
								if(in_array('project',$value_config)) {
									foreach(explode(',',$row['PROJECTS']) as $projectid) {
										if($projectid > 0) {
											$comments .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"))).'<br />';
										}
									}
								}
								if(in_array('search_client',$value_config)) {
									foreach(explode(',',$row['CLIENTS']) as $clientid) {
										if($clientid > 0) {
											$comments .= get_contact($dbc, $clientid).'<br />';
										}
									}
								}
								$comments .= html_entity_decode($row['COMMENTS']);
								if(empty(strip_tags($comments))) {
									$comments = $timesheet_comment_placeholder;
								}
								if($row['type_of_time'] == 'Vac Hrs.') {
									$total_vac += $row['hours'];
								} else {
									$total += $row['hours'];
								}
								$timecardid = $row['id'];
								if(empty($row['ticketid'])) {
									$driving_time = 'Driving Time';
								}
                                $show_separator = 0;
							} else {
								$row = '';
								$comments = '';
                                $show_separator = 1;
							}
							$day_of_week = date('l', strtotime($date));
							$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date, 'all');
							if(!empty($shifts)) {
								$hours = '';
								$hours_off = '';
								foreach ($shifts as $shift) {
									$hours .= $shift['starttime'].' - '.$shift['endtime'].'<br>';
									$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];
									
								}
								$hours = $hours_off == '' ? $hours : $hours_off;
							} else {
								$hours = $schedule_list[date('w',strtotime($date))];
							}
							$mod = '';
							if($date < $last_period) {
								$mod = 'readonly';
							} ?>
							<tr style="<?= $hl_colour ?>">
								<input type="hidden" name="time_cards_id[]" value="<?= $row['type_of_time'] == 'Vac Hrs.' ? '' : $row['id'] ?>">
								<input type="hidden" name="time_cards_id_vac[]" value="<?= $row['type_of_time'] == 'Vac Hrs.' ? $row['id'] : '' ?>">
								<input type="hidden" name="date[]" value="<?= empty($row['date']) ? $date : $row['date'] ?>">
								<input type="hidden" name="staff[]" value="<?= empty($row['staff']) ? $search_staff : $row['staff'] ?>">
								<td data-title="Date" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><?= (in_array('editable_dates',$value_config) ? '<input type="text" name="date_editable[]" value="'.$date.'" class="form-control datepicker">' : $date) ?></td>
								<?php if(in_array('schedule',$value_config)) { ?><td data-title="Schedule" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><?= $hours ?></td><?php } ?>
								<?php if(in_array('scheduled',$value_config)) { ?><td data-title="Scheduled Hours" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"></td><?php } ?>
								<?php if(in_array('start_time',$value_config)) { ?><td data-title="Start Time" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><?= $row['start_time'] ?></td><?php } ?>
								<?php if(in_array('end_time',$value_config)) { ?><td data-title="End Time" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><?= $row['end_time'] ?></td><?php } ?>
								<?php if(in_array('start_time_editable',$value_config)) { ?><td data-title="Start Time" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><input type="text" name="start_time[]" class="form-control datetimepicker" value="<?= $row['start_time'] ?>" <?= in_array('calculate_hours_start_end',$value_config) ? 'onchange="calculateHoursByStartEndTimes(this);"' : '' ?>></td><?php } else { ?><input type="hidden" name="start_time[]" value="<?= $row['start_time'] ?>"><?php } ?>
								<?php if(in_array('end_time_editable',$value_config)) { ?><td data-title="End Time" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><input type="text" name="end_time[]" class="form-control datetimepicker" value="<?= $row['end_time'] ?>" <?= in_array('calculate_hours_start_end',$value_config) ? 'onchange="calculateHoursByStartEndTimes(this);"' : '' ?>></td><?php } else { ?><input type="hidden" name="end_time[]" value="<?= $row['end_time'] ?>"><?php } ?>
								<?php if(in_array('start_day_tile',$value_config)) { ?><td data-title="<?= $timesheet_start_tile ?>" style="text-align: center;" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><label><input type="checkbox" value="Driving Time" <?= $driving_time == 'Driving Time' ? 'checked' : '' ?> class="driving_time" onchange="checkDrivingTime(this);"></label></span></td><?php } ?>
								<?php if($layout == 'ticket_task') { ?>
									<td data-title="<?= TICKET_NOUN ?>" class="ticket_task_td <?= in_array('start_day_tile',$value_config) && $driving_time == 'Driving Time' ? 'readonly-block' : '' ?> <?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><select name="ticketid[]" class="chosen-select-deselect" data-placeholder="Select a <?= TICKET_NOUN ?>"" onchange="getTasks(this);"><option></option>
										<?php foreach($ticket_list as $ticket) { ?>
											<option data-tasks='<?= json_encode(explode(',', $ticket['task_available'])) ?>' <?= $ticket['ticketid'] == $row['ticketid'] ? 'selected' : '' ?> value="<?= $ticket['ticketid'] ?>"><?= get_ticket_label($dbc, $ticket) ?></option>
										<?php } ?></select></td>
									<td data-title="Task" class="ticket_task_td <?= in_array('start_day_tile',$value_config) && $driving_time == 'Driving Time' ? 'readonly-block' : '' ?> <?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><select name="type_of_time[]" class="chosen-select-deselect" data-placeholder="Select a Task"><option></option>
										<?php $task_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"))['task_available'];
										foreach (explode(',',$task_list) as $task) { ?>
											<option <?= $row['type_of_time'] == $task ? 'selected' : '' ?> value="<?= $task ?>"><?= $task ?></option>
										<?php } ?>
									</select></td>
								<?php } else { ?>
									<td data-title="Position" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><select name="type_of_time[]" class="chosen-select-deselect" data-placeholder="Select Position"><option />
										<?php foreach($position_list as $position) { ?>
											<option <?= $position[0] == $row['type_of_time'] ? 'selected' : '' ?> value="<?= $position[0] ?>"><?= $position[0] ?></option>
										<?php } ?></select></td>
								<?php } ?>
								<?php if(in_array('total_tracked_hrs',$value_config)) { ?><td data-title="Time Tracked" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><?= $row['timer'] ?></td><?php } ?>
								<td data-title="Hours" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><input type="text" name="total_hrs[]" value="<?= (empty($row['hours']) || $row['type_of_time'] == 'Vac Hrs.' ? '' : ($timesheet_time_format == 'decimal' ? number_format($row['hours'],2) : time_decimal2time($row['hours']))) ?>" class="form-control <?= ($security['edit'] > 0 ? 'timepicker"' : '" readonly') ?>"></td>
								<?php if(in_array('vaca_hrs',$value_config)) { ?><td data-title="Vacation Hours" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><input type="text" name="total_hrs_vac[]" value="<?= (empty($row['hours']) || $row['type_of_time'] != 'Vac Hrs.' ? '' : ($timesheet_time_format == 'decimal' ? number_format($row['hours'],2) : time_decimal2time($row['hours']))) ?>" class="form-control <?= ($security['edit'] > 0 ? 'timepicker"' : '" readonly') ?>"></td><?php } ?>
								<?php if(in_array('view_ticket',$value_config)) { ?><td data-title="<?= TICKET_NOUN ?>" style="text-align: center;" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><a href="" onclick="viewTicket(this); return false;" data-ticketid="<?= $row['ticketid'] ?>" class="view_ticket" <?= $row['ticketid'] > 0 ? '' : 'style="display:none;"' ?>>View</a></td><?php } ?>
								<?php if(in_array('comment_box',$value_config)) { ?><td data-title="Comments" class="<?= $show_separator==1 ? 'theme-color-border-bottom' : '' ?>"><span><?= $comments ?></span><?= ($security['edit'] > 0 ? '<img class="inline-img add-row pull-right" src="../img/icons/ROOK-add-icon.png"><img class="inline-img rem-row pull-right" src="../img/remove.png">' : '') ?><img class="inline-img comment-row pull-right" src="../img/icons/ROOK-reply-icon.png"><input type="text" class="form-control" name="comment_box[]" value="<?= $row['COMMENTS'] ?>" style="display:none;"></td><?php } ?>
							</tr>
							<?php if($date != $row['date']) {
								$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
							}
						} ?>
                        
						<tr>
							<td data-title="" colspan="<?= $total_colspan ?>">Totals</td>
							<?php if(in_array('schedule',$value_config)) { ?><td></td><?php } ?>
							<?php if(in_array('scheduled',$value_config)) { ?><td></td><?php } ?>
							<?php if(in_array('total_tracked_hrs',$value_config)) { ?><td></td><?php } ?>
							<td data-title="Total Hours"><?= ($timesheet_time_format == 'decimal' ? number_format($total,2) : time_decimal2time($total)) ?></td>
							<?php if(in_array('vaca_hrs',$value_config)) { ?><td><?= ($timesheet_time_format == 'decimal' ? number_format($total_vac,2) : time_decimal2time($total_vac)) ?></td><?php } ?>
							<?php if(in_array('view_ticket',$value_config)) { ?><td></td><?php } ?>
							<?php if(in_array('comment_box',$value_config)) { ?><td></td><?php } ?>
						</tr>
					</table>

				<?php $tb_field = $value['config_field']; ?>
				</div>
			<?php elseif($layout == 'table_add_button'): ?>
				<?php if(vuaed_visible_function($dbc, 'time_cards') > 0) { ?>
					<a class="btn brand-btn pull-right" href="add_time_cards.php">Add Time</a>
				<?php } ?>
				<div id="no-more-tables">
					<table class="table table-bordered">
						<tr class="hidden-sm hidden-xs">
							<th>Date</th>
							<th>Staff</th>
							<th>Hours</th>
							<th>Type</th>
							<th>Function</th>
						</tr>
						<?php $time_cards = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' AND `deleted`=0");
						while($time_card = mysqli_fetch_assoc($time_cards)) { ?>
							<tr class="hidden-sm hidden-xs">
								<td data-title="Date"><?= $time_card['date'] ?></td>
								<td data-title="Staff"><?= get_contact($dbc, $time_card['staff']) ?></td>
								<td data-title="Hours"><?= $time_card['total_hours'] ?></td>
								<td data-title="Type"><?= $time_card['type_of_time'] ?></td>
								<td data-title="Function"><?= vuaed_visible_function($dbc, 'time_card') > 0 ? '<a href="add_time_cards.php?time_cards_id='.$time_card['time_cards_id'].'">Edit</a>' : '' ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>
				<?php if(vuaed_visible_function($dbc, 'time_cards') > 0) { ?>
					<a class="btn brand-btn pull-right" href="add_time_cards.php">Add Time</a>
				<?php } ?>
			<?php elseif($layout == 'rate_card' || $layout == 'rate_card_tickets'): ?>
				<script>
				$(document).ready(function() {
					$('.close_iframer').click(function(){
						$('.iframe_holder').hide();
		  				$('.hide_on_iframe').show();
					});

					$('iframe').load(function() {
						this.contentWindow.document.body.style.overflow = 'hidden';
						this.contentWindow.document.body.style.minHeight = '0';
						this.contentWindow.document.body.style.paddingBottom = '5em';
						this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
					});

					$('#save_description').click(function() {
						var desc_id = $('#desc_id').val();
						var desc_text = tinyMCE.activeEditor.getContent();
						$('#'+desc_id).val(desc_text);
						$('#'+desc_id).next('.comment_box_text').html(desc_text + "<p><a class='pull-right' href='#" + desc_id + "' onclick='expandArea(this)'>Edit Description</a></p>");
						$('.iframe_holder').hide();
		  				$('.hide_on_iframe').show();
		  				return false;
					});

					$('#cancel_description').click(function() {
						$('.iframe_holder').hide();
		  				$('.hide_on_iframe').show();
		  				return false;
					});

					$('[name="hours[]"]').change(function() {
						updateTotals($(this));
					});

					$('[name="day_checkbox[]"]').click(function() {
						updateTotals($(this));
					});
				});
				function updateTotals(sel) {
					var total = 0;
					sel.closest('.form-group').find('.category-block').each(function() {
						var cat_total = 0;
						$(this).find('[name="hours[]"]').each(function() {
							if($(this).data('rate-travel1') > 0 || $(this).data('rate-travel5') > 0 || $(this).data('rate-travel15') > 0) {
								if (this.value >= 5) {
									var row = this.value * $(this).data('rate-travel5');
									var travel_rate = 1 * $(this).data('rate-travel5');
								} else if (this.value < 5 && this.value >= 1) {
									var row = this.value * $(this).data('rate-travel15');
									var travel_rate = 1 * $(this).data('rate-travel15');
								} else {
									var row = this.value * $(this).data('rate-travel1');
									var travel_rate = 1 * $(this).data('rate-travel1');
								}
								$(this).attr('data-rate', travel_rate);
								$(this).closest('div').nextAll('.row-rate').first().html('$'+travel_rate.toFixed(2));
							} else {
								var row = this.value * $(this).data('rate');
							}
							$(this).closest('div').nextAll('.row-total').first().html('$'+row.toFixed(2));
							cat_total += row;
						});
						$(this).find('[name="day_checkbox[]"]').each(function() {
							if($(this).is(':checked')){
								var row = this.value * $(this).data('rate');
							} else {
								var row = 0;
							}
							$(this).closest('div').nextAll('.row-total').first().html('$'+row.toFixed(2));
							cat_total += row;
						});
						$(this).find('.cat-total').html('$'+cat_total.toFixed(2));
						total += cat_total;
					});
					sel.closest('.form-group').find('.day-total').html('$'+total.toFixed(2));

				}
				function expandArea(sel) {
					var desc_id = $(sel).data('target');
					var desc_text = $(desc_id).val();
					$('.iframe_holder').show();
					$('.iframe_holder').find('#desc_id').val(desc_id);
					$('#description_area').append(desc_text);
                    tinyMCE.get('description_area').execCommand('mceSetContent', false, desc_text);
		   			$('.hide_on_iframe').hide();
				}
				</script>

				<div class='iframe_holder' style='display:none;'>
					<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
					<input type='hidden' id='desc_id' value=''>
					<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
					<textarea name="description_area" rows="3" cols="50" class="iframe-textarea form-control"></textarea>
					<button id="save_description" class="btn brand-btn pull-right">Save</button>
					<button id="cancel_description" class="btn brand-btn pull-left">Cancel</button>
			    </div>
			    <div class="hide_on_iframe">
				<?php 
				$desc_inc = 0;
				for($date = $search_start_date; strtotime($date) <= strtotime($search_end_date); $date = date("Y-m-d", strtotime("+1 day", strtotime($date)))) {
					if($layout == 'rate_card_tickets') {
						$ticket_sql = "SELECT `tickets`.*, `osbn`.`item_id` `osbn` FROM `tickets` LEFT JOIN `ticket_attached` `osbn` ON `tickets`.`ticketid`=`osbn`.`ticketid` AND `osbn`.`src_table`='Staff' AND `osbn`.`deleted`=0 AND `osbn`.`position`='Team Lead' WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `time_cards` WHERE `deleted`=0 AND `staff`='$search_staff' AND `date`='$date' UNION SELECT `ticketid` FROM `tickets` WHERE CONCAT(',',`contactid`,',') LIKE '%,$search_staff,%' AND (`to_do_date`='$date' OR '$date' BETWEEN `to_do_date` AND `to_do_end_date` OR `internal_qa_date`='$date' OR `deliverable_date`='$date') AND `deleted`=0)";
					} else {
						$ticket_sql = "SELECT 0 `ticketid`";
					}
					$ticket_query = mysqli_query($dbc, $ticket_sql);
					$ticket = mysqli_fetch_assoc($ticket_query);
					do {
						$daily_total = 0;
						$cat_total = 0;
						$work_hours_sql = "SELECT IFNULL(SUM(`total_hrs`),0) hours, `category`, `work_desc`, `hourly`, `daily`, `color_code`, `location`, `customer`, `day`, `travel_range_1`, `travel_range_5`, `travel_range_1_5`, `comment_box` FROM `staff_rate_table` staff LEFT JOIN `time_cards` sheet ON CONCAT(',',staff.`staff_id`,',') LIKE CONCAT('%,',sheet.`staff`,',%') AND sheet.`type_of_time`=staff.`work_desc` AND sheet.`date`='$date' AND sheet.`deleted`=0 WHERE CONCAT(',',staff.`staff_id`,',') LIKE '%,$search_staff,%' AND staff.`deleted`=0 AND DATE(NOW()) BETWEEN staff.`start_date` AND IFNULL(NULLIF(staff.`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `category`, `work_desc` ORDER BY `category`, `sort_order`, `work_desc`, `hourly`";
						$work_result = mysqli_query($dbc, $work_hours_sql);
						$location = mysqli_fetch_array($work_result)['location'];
						$customer = mysqli_fetch_array($work_result)['customer'];
						$work_result = mysqli_query($dbc, $work_hours_sql);
						$day_of_week = date('l', strtotime($date));
						$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date);
						if(!empty($shifts)) {
							$shift = '';
							$hours_off = '';
							foreach ($shifts as $shift_detail) {
								$shift .= $shift_detail['starttime'].' - '.$shift_detail['endtime'].'<br>';
								$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];
								
							}
							$shift = $hours_off == '' ? $shift : $hours_off;
						} else {
							$shift = $schedule_list[date('w',strtotime($date))];
						}
						echo "<div class='form-group' style='border:solid black 1px; display:inline-block; margin:1em; width:30em;'>";
						echo "<div style='border:solid black 1px; padding:0.25em; width: 30em;'><div style='display:inline-block; width:12em;'>Date:</div><div style='display:inline-block; width:16em;'>$date</div>";
						if($shift != '') {
							echo "<div style='display:inline-block; width:12em;'>Hours:</div><div style='display:inline-block; width:16em;'>$shift</div>";
						}
						if($ticket['ticketid'] > 0) {
							echo "<div style='display:inline-block; width:12em;'>".TICKET_NOUN.":</div><div style='display:inline-block; width:16em;'>".get_ticket_label($dbc, $ticket).($ticket['osbn'] > 0 ? "<br />OSBN: ".get_contact($dbc, $ticket['osbn']) : '')."</div>";
						}
						echo "<div style='display:inline-block; width:11.7em;'>Customer:</div>"
						?>
						<div style='display:inline-block; width:16em;'>
							<input type='hidden' name='customer_date[]' value='<?php echo $date; ?>'>
							<select data-placeholder="Choose a Customer..." name="customer[]" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php
									$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category='Business' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
									foreach($query as $id) {
										$selected = '';
										$selected = $id == $customer ? 'selected = "selected"' : '';
										echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
									}
								?>
							</select>
						</div>
						<?php
						echo "<div style='display:inline-block; width:12em;'>Location:</div><div style='display:inline-block; width:16em;'><input type='hidden' name='location_date[]' value='$date'><input type='text' name='location[]' class='form-control'  value='$location'></div></div>";
						$category = '';
						while($hours = mysqli_fetch_array($work_result)) {
							if($hours['category'] != $category) {
								if($category != '') {
									echo "<div style='display:inline-block; width:12em;'>$category Total</div><div style='display:inline-block; text-align:right; width:16em;' class='cat-total'>$".number_format($cat_total,2)."</div>";
									echo "<div style='display:inline-block;  vertical-align:top; width:12em;'><img class='inline-img smaller' data-target='#desc_".$desc_inc."' onclick='expandArea(this);' src='../img/icons/ROOK-edit-icon.png'>$category Description</div><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='hidden' name='cat_comment[]' id='desc_".$desc_inc."' value='".$comment_box."'><div class='comment_box_text' style='display:inline-block; width:16em;'>".$comment_box;
									echo "<p style='display:none;'><a class='pull-right' href='' data-target='#desc_".$desc_inc."' onclick='expandArea(this)'>Edit Description</a></p></div></div>";
									$desc_inc++;
								}
								$category = $hours['category'];
								$cat_total = 0;
								echo "<div style='border:solid black 1px; padding:0.25em; width: 30em;' class='category-block'><div style='display:inline-block; width:12em;'>$category</div>
								<div style='display:inline-block; text-align:center; width:4em;'>Day</div><div style='display:inline-block; text-align:center; width:4em;'>Hours</div><div style='display:inline-block; text-align:center; width:4em;'>Rate</div><div style='display:inline-block; text-align:center; width:4em;'>Total</div>";
							}
							echo "<div style='background-color:".$hours['color_code'].";'>";
							echo "<div style='display:inline-block; width:12em;'>".$hours['work_desc']."</div><div style='display:inline-block; width:4em;'>";
							if ($hours['daily'] > 0) {
								$checked = '';
								if ($hours['day'] == 1) {
									$checked = 'checked';
								}
								echo "<input type='hidden' name='day_cat[]' value='$category'><input type='hidden' name='day_type[]' value='".$hours['work_desc']."'><input type='hidden' name='day_date[]' value='$date'><input type='hidden' name='day[]' value='1'>";
								echo "<input type='checkbox' data-rate='".$hours['daily']."' ".$checked." style='margin-left: 2em;' name='day_checkbox[]' value='1'></div>";
								echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' name='' data-rate='".$hours['hourly']."' class='form-control' disabled value=''></div>";
								echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-rate'>$".$hours['daily']."</div>";
								echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-total'>$";
								if ($checked == 'checked') {
									echo $hours['daily'];
								} else {
									echo '0.00';
								}
								echo "</div></div>";
								if ($checked != '') {
									$cat_total += $hours['daily'];
									$daily_total += $hours['daily'];
								}
							} else {
								echo "<input type='hidden' name='hours_cat[]' value='$category'><input type='hidden' name='hours_type[]' value='".$hours['work_desc']."'><input type='hidden' name='hours_date[]' value='$date'>";
								echo "</div>";
								$hourly_rate = $hours['hourly'];
								if ($hours['travel_range_1'] > 0 || $hours['travel_range_5'] > 0 || $hours['travel_range_1_5'] > 0) {
									if ($hours['hours'] >= 5) {
										$hourly_rate = $hours['travel_range_5'];
									} else if ($hours['hours'] < 5 && $hours['hours'] >= 1) {
										$hourly_rate = $hours['travel_range_1_5'];
									} else {
										$hourly_rate = $hours['travel_range_1'];
									}
									echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' ".($security['edit'] > 0 ? '' : 'readonly')." name='hours[]' data-rate='".$hourly_rate."' data-rate-travel1='".$hours['travel_range_1']."' data-rate-travel5='".$hours['travel_range_5']."' data-rate-travel15='".$hours['travel_range_1_5']."' class='form-control' value='".$hours['hours']."'></div>";
								} else {
									echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' ".($security['edit'] > 0 ? '' : 'readonly')." name='hours[]' data-rate='".$hourly_rate."' class='form-control' value='".$hours['hours']."'></div>";
								}
								echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-rate'>$".$hourly_rate."</div>";
								echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-total'>$".number_format($hourly_rate * $hours['hours'],2)."</div>";
								if($hours['comment_box'] != '' && in_array(['Comments','text','comment_box'],$config['settings']['Choose Fields for Time Sheets']['data']['General'])) {
									echo html_entity_decode($hours['comment_box']);
								}
								echo "</div>";
								$cat_total += $hours['hours'] * $hourly_rate;
								$daily_total += $hours['hours'] * $hourly_rate;
							}
							$comment_box = $hours['comment_box'];
						}
						if($category != '') {
							echo "<div style='display:inline-block; width:12em;'>$category Total</div><div style='display:inline-block; text-align:right; width:16em;' class='cat-total'>$".number_format($cat_total,2)."</div>";
							echo "<div style='display:inline-block; vertical-align:top; width:12em;'>$category Description</div><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='hidden' name='cat_comment[]' id='desc_".$desc_inc."' value='".$comment_box."'><div class='comment_box_text' style='display:inline-block; width:16em;'>".$comment_box;
							echo "<p><a class='pull-right' href='#desc_".$desc_inc."' onclick='expandArea(this)'>Edit Description</a></p></div></div>";
							$desc_inc++;
						}
						echo "<div style='border:solid black 1px; padding:0.25em; width:30em;'><div style='display:inline-block; width:12em;'>Daily Total</div><div style='display:inline-block; text-align:right; width:16em;' class='day-total'>$".number_format($daily_total,2)."</div></div></div>";
					} while($ticket = mysqli_fetch_assoc($ticket_query));
				}
			endif;

			if($security['edit'] > 0 && $search_staff != '' && $layout != 'table_add_button'):
				if ($layout == 'rate_card') {
					$submit_approval = 'rate_approval';
					$submit_timesheet = 'rate_timesheet';
				} else if($layout == 'position_dropdown') {
					$submit_approval = 'position_approval';
					$submit_timesheet = 'positions';
				} else if($layout == 'ticket_task') {
					$submit_approval = 'position_approval';
					$submit_timesheet = 'ticket_task';
				} else {
					$submit_approval = 'approval';
					$submit_timesheet = 'timesheet';
				}
			// 	echo '<div class="clearfix"></div>';
			// 	if($submit_mode != 'auto') {
			// 		echo '<button type="submit" value="'.$submit_approval.'" name="submit" class="btn brand-btn mobile-block pull-right">Submit Time Sheet '.$submit_label.'</button>';
			// 	}
			// 	echo '<button type="submit" value="'.$submit_timesheet.'" name="submit" class="btn brand-btn mobile-block pull-right">Save Time Sheet</button>';
			// endif;

			// if($security['edit'] > 0 && $search_staff != '' && $layout != 'table_add_button'):
				if ($layout == 'rate_card') { ?>
					<script>
						$('[name="hours[]"],[name="cat_comment[]"],[name="day_checkbox[]"]').change(saveField);
						function saveFieldMethod(field) {
							var line = $(field).closest('.form-group');
							$.post('time_cards_ajax.php?action=rate_time', {
								staff_id: '<?= $search_staff ?>',
								site_id: '<?= $search_site ?>',
								projectid: '<?= $search_project ?>',
								hours: field.value,
								hours_type: $(field).parent('div').prev('div').find('[name^=hours_type]'),
								hours_date: $(field).parent('div').prev('div').find('[name^=hours_date]'),
								day_type: $(field).parent('div').prev('div').find('[name^=day_type]'),
								day_date: $(field).parent('div').prev('div').find('[name^=day_date]'),
								day_checkbox: field.name.indexOf('day_checkbox') >= 0 && field.checked ? 1 : 0,
								location: line.find('[name^=location]').val(),
								customer: line.find('[name^=customer]').val(),
								comment: line.find('[name*=comment]').val()
							}, function(response) {
								doneSaving();
							});
						}
					</script>
				<?php } else if($layout == 'position_dropdown') { ?>
					<script>
						$('[name="total_hrs[]"],[name="total_hrs_vac[]"],[name="type_of_time[]"],[name="comment_box[]"],[name="date_editable[]"]').change(saveField);
						function saveFieldMethod(field) {
							var line = $(field).closest('tr');
							$.post('time_cards_ajax.php?action=position_time', {
								site_id: $('[name=site_id]').val(),
								projectid: $('[name=projectid]').val(),
								id: line.find('[name="time_cards_id[]"]').val(),
								id_vac: line.find('[name="time_cards_id_vac[]"]').val(),
								date: line.find('[name="date[]"]').val(),
								date_editable: line.find('[name="date_editable[]"]').val(),
								staff: line.find('[name="staff[]"]').val(),
								type_of_time: line.find('[name="type_of_time[]"]').val(),
								total_hrs: line.find('[name="total_hrs[]"]').val(),
								total_hrs_vac: line.find('[name="total_hrs_vac[]"]').val(),
								comment_box: line.find('[name="comment_box[]"]').val()
							}, function(response) {
								if(response != '') {
									ids = response.split(',');
									if(ids[0] > 0) {
										line.find('[name="time_cards_id[]"]').val(ids[0]);
									}
									if(ids[1] > 0) {
										line.find('[name="time_cards_id_vac[]"]').val(ids[1]);
									}
								}
								doneSaving();
							});
						}
					</script>
				<?php } else if($layout == 'ticket_task') { ?>
					<script>
						$('[name="start_time[]"],[name="end_time[]"],[name="total_hrs[]"],[name="total_hrs_vac[]"],[name="type_of_time[]"],[name="ticketid[]"],[name="comment_box[]"],[name="date_editable[]"]').change(saveField);
						function saveFieldMethod(field) {
							var line = $(field).closest('tr');
							$.post('time_cards_ajax.php?action=task_time', {
								site_id: $('[name=site_id]').val(),
								projectid: $('[name=projectid]').val(),
								id: line.find('[name="time_cards_id[]"]').val(),
								id_vac: line.find('[name="time_cards_id_vac[]"]').val(),
								date: line.find('[name="date[]"]').val(),
								date_editable: line.find('[name="date_editable[]"]').val(),
								start_time: line.find('[name="start_time[]"]').val(),
								end_time: line.find('[name="end_time[]"]').val(),
								staff: line.find('[name="staff[]"]').val(),
								type_of_time: line.find('[name="type_of_time[]"]').val(),
								ticketid: line.find('[name="ticketid[]"]').val(),
								total_hrs: line.find('[name="total_hrs[]"]').val(),
								total_hrs_vac: line.find('[name="total_hrs_vac[]"]').val(),
								comment_box: line.find('[name="comment_box[]"]').val()
							}, function(response) {
								if(line.find('[name="date_editable[]"]').val() != undefined && line.find('[name="date_editable[]"]').val() != '' && line.find('[name="date_editable[]"]').val() != line.find('[name="date[]"]').val()) {
									line.find('[name="date[]"]').val(line.find('[name="date_editable[]"]').val());
								}
								if(response != '') {
									ids = response.split(',');
									if(ids[0] > 0) {
										line.find('[name="time_cards_id[]"]').val(ids[0]);
									}
									if(ids[1] > 0) {
										line.find('[name="time_cards_id_vac[]"]').val(ids[1]);
									}
								}
								doneSaving();
							});
						}
					</script>
				<?php } else { ?>
					<script>
						$('[name^=regular],[name^=direct],[name^=indirect],[name^=extra],[name^=relief],[name^=sleep],[name^=sickadj],[name^=sick],[name^=statavail],[name^=stat],[name^=vacavail],[name^=vaca],[name^=time_cards_id_],[name^=deleted_],[name^=add_comment_],[name^=training]').change(saveField);
						function saveFieldMethod(field) {
							var line = $(field).closest('tr');
							comment = line.find('[name^=add_comment]').val();
							if(field.name.indexOf('add_comment') >= 0 || field.name.indexOf('deleted') >= 0) {
								if(field.name.indexOf('add_comment') >= 0) {
									field.value = '';
								}
								field = line.find('[name^=regular],[name^=extra],[name^=relief],[name^=sleep],[name^=sickadj],[name^=sick],[name^=statavail],[name^=stat],[name^=vacavail],[name^=vaca]').get(0);
							}
							$.post('time_cards_ajax.php?action=type_time', {
								id: line.find('[name^=time_cards_id]').val(),
								staff_id: '<?= $search_staff ?>',
								site_id: '<?= $search_site ?>',
								projectid: '<?= $search_project ?>',
								field: field.name,
								value: field.value,
								comment: comment,
								deleted: line.find('[name^=deleted]').val(),
								ticket_attached_id: line.find('[name^=ticket_attached_id]').val()
							}, function(response) {console.log(response);
								doneSaving();
							});
						}
					</script>
				<?php }
				echo '<div class="clearfix"></div>';
				if($submit_mode != 'auto') {
					echo '<button type="submit" value="'.$submit_approval.'" name="submit" class="btn brand-btn mobile-block pull-right">Submit Time Sheet '.$submit_label.'</button>';
				}
				echo '<button type="submit" value="'.$submit_timesheet.'" name="submit" class="btn brand-btn mobile-block pull-right">Save Time Sheet</button>';
			endif; ?>
			</div>
		</form>
        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
