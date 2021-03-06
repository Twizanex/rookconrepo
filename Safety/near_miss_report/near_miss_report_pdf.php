<?php
function near_miss_report_pdf($dbc,$safetyid, $fieldlevelriskid) {
    $form_by = $_SESSION['contactid'];

    $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_near_miss_report WHERE fieldlevelriskid='$fieldlevelriskid'"));

    $tab = get_safety($dbc, $safetyid, 'tab');
    $form = get_safety($dbc, $safetyid, 'form');

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$form'"));
    $form_config = ','.$get_field_config['fields'].',';

    $get_pdf_logo = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pdf_logo FROM field_config_safety WHERE tab='$tab' AND form='$form'"));

    DEFINE('PDF_LOGO', $get_pdf_logo['pdf_logo']);
	DEFINE('PDF_HEADER', html_entity_decode($get_field_config['pdf_header']));
    DEFINE('PDF_FOOTER', html_entity_decode($get_field_config['pdf_footer']));
    $result_update_employee = mysqli_query($dbc, "UPDATE `safety_near_miss_report` SET `status` = 'Done' WHERE fieldlevelriskid='$fieldlevelriskid'");

    //$result_update_employee = mysqli_query($dbc, "UPDATE `safety_staff` SET `done` = 1 WHERE safetyid='$safetyid' AND staffid='$form_by' AND DATE(today_date) = CURDATE()");

    $today_date = $get_field_level['today_date'];
    $contactid = $get_field_level['contactid'];
    $location = $get_field_level['location'];
    $hazard_rating = $get_field_level['hazard_rating'];
    $action_timeline = $get_field_level['action_timeline'];
    $description = $get_field_level['description'];
    $action = $get_field_level['action'];
    $action_to = $get_field_level['action_to'];
    $est_comp = $get_field_level['est_comp'];
    $date_comp = $get_field_level['date_comp'];
    $analysis_to = $get_field_level['analysis_to'];
	$desc = $get_field_level['desc'];
	$desc1 = $get_field_level['desc1'];
	$desc2 = $get_field_level['desc2'];
	$fields = explode('**FFM**', $get_field_level['fields']);

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            if(PDF_LOGO != '') {
                $image_file = 'download/'.PDF_LOGO;
                $this->Image($image_file, 10, 10, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }

            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.PDF_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
            $this->SetY(-30);
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = PDF_FOOTER;
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    if(PDF_LOGO != '') {
        $pdf->SetMargins(PDF_MARGIN_LEFT, 55, PDF_MARGIN_RIGHT);
    } else {
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
    }
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

    $html_weekly = '<h2>AVS Hazard Identification</h2>';

    $html_weekly .= '<table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="25%">Date</th><th width="50%">Location</th><th width="25%">Reported By</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$today_date.'</td><td>'.$location.'</td><td>'.$contactid.'</td></tr>';

    if (strpos(','.$form_config.',', ',fields19,') !== FALSE) {
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="10%">Hazard ID</th><th width="10%">Time</th><th width="40%">Near Miss</th><th width="40%">Supervisor</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$fields[4].'</td><td>'.$fields[3].'</td><td>'.$fields[0].'</td><td>'.$fields[1].'</td></tr>';
    }

	$html_weekly .= '</table><br><br>';

    if (strpos(','.$form_config.',', ',fields4,') !== FALSE) {
    $html_weekly .= "<b>Hazard Rating</b> : ".$hazard_rating;
    }
    if (strpos(','.$form_config.',', ',fields5,') !== FALSE) {
    $html_weekly .= "<br><b>Action Timeline</b> : ".$action_timeline;
    }
    if (strpos(','.$form_config.',', ',fields6,') !== FALSE) {
    $html_weekly .= "<br><br><b>Description of Unsafe Acts/Conditions/Practices</b><br>".html_entity_decode($description);
    }
    if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
    $html_weekly .= "<br><br><b>Action To Be Taken</b><br>".html_entity_decode($action);
    }
    if (strpos(','.$form_config.',', ',fields8,') !== FALSE) {
    $html_weekly .= "<br><b>Action Assigned to</b> : ".$action_to;
    }
    if (strpos(','.$form_config.',', ',fields9,') !== FALSE) {
    $html_weekly .= "<br><b>Estimated Completion Date</b> : ".$est_comp;
    }
    if (strpos(','.$form_config.',', ',fields10,') !== FALSE) {
    $html_weekly .= "<br><b>Date Completed</b> : ".$date_comp;
    }
    if (strpos(','.$form_config.',', ',fields11,') !== FALSE) {
    $html_weekly .= "<br><b>Invenstigation/Root Cause Analysis Assigned To</b> : ".$analysis_to;
    }

	if (strpos(','.$form_config.',', ',fields15,') !== FALSE) {
    $html_weekly .= "<br><b>Hazardous Condition or Procedure</b> : ".html_entity_decode($desc);
    }

	if (strpos(','.$form_config.',', ',fields16,') !== FALSE) {
    $html_weekly .= "<br><b>Action Taken</b> : ".html_entity_decode($desc1);
    }
	if (strpos(','.$form_config.',', ',fields21,') !== FALSE) {
    $html_weekly .= "<br><b>Cause(s)</b> : ".html_entity_decode($desc2);
    }

    if (strpos(','.$form_config.',', ',fields22,') !== FALSE) {
    $html_weekly .= "<br><b>Corrective Action Taken by Whom</b> : ".$fields[5];
    }

    if (strpos(','.$form_config.',', ',fields17,') !== FALSE) {
    $html_weekly .= "<br><b>Date Hazardous Conditions or Procedures Corrected</b> : ".$fields[2];
    }

	$sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");

    $html_weekly .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        <th>Review</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        $staffcheck = $row_sa['staffcheck'];

        $html_weekly .= '<tr nobr="true">';
        $html_weekly .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';
        $html_weekly .= '<td data-title="Email"><img src="near_miss_report/download/safety_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $html_weekly .= '<td data-title="Email">'.$staffcheck.'</td>';
        $html_weekly .= '</tr>';
    }
    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');
    $pdf->Output('near_miss_report/download/hazard_'.$fieldlevelriskid.'.pdf', 'F');

    $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");
    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        unlink("near_miss_report/download/safety_".$assign_staff_id.".png");
    }
    echo '';
}
?>