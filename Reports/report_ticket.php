<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $contactidpdf = $_POST['contactidpdf'];
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = TICKET_NOUN.' Report';
            $this->writeHTMLCell(0, 0, 0 , 40, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
            $this->SetY(-24);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

			// Position at 15 mm from bottom
			$this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
    	}

    }

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//$pdf->AddPage('L', 'LETTER');
    //$pdf->SetFont('helvetica', '', 9);

    //$html .= report_receivables($dbc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $html = '';
    if($contactidpdf == '') {
		$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(contactid), first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND role != 'super' AND first_name != '' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
        foreach($query as $row1) {
            $pdf->AddPage('L', 'LETTER');
            $pdf->SetFont('helvetica', '', 9);
            $html = '';
            $html .= '<h3>'.get_staff($dbc,$row1).'</h3>';
            $html .= report_receivables($dbc, $row1, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $starttimepdf, $endtimepdf);
            $pdf->writeHTML($html, true, false, true, false, '');
        }
    } else {
        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 9);
        $html = '';

        $html .= '<h3>'.get_staff($dbc,$contactidpdf).'</h3>';
        $html .= report_receivables($dbc, $contactidpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $starttimepdf, $endtimepdf);
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/ticket_report_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_ticket', 0, WEBSITE_URL.'/Reports/Download/ticket_report_'.$today_date.'.pdf', 'Ticket Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/ticket_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $contactid = $contactidpdf;
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

<script type="text/javascript">
function handleClick(sel) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=daysheet_report",
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

        <br><br>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $contactid = $_POST['contactid'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff..." name="contactid" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND role != 'super' AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $contactid ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="contactidpdf" value="<?php echo $contactid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
            if($contactid == '') {
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, ""),MYSQLI_ASSOC));
                $query = mysqli_query($dbc,"SELECT distinct(contactid), first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND role != 'super' AND first_name != '' AND deleted=0 AND status=1");
				foreach($query as $row1) {
                    echo '<h3>'.get_staff($dbc,$row1).'</h3>';
                    echo report_receivables($dbc, $row1, '', '', '', $starttime, $endtime);
                    echo "<br>";
                }
            } else {
                echo '<h3>'.get_staff($dbc,$contactid).'</h3>';
                echo report_receivables($dbc, $contactid, '', '', '', $starttime, $endtime);
                echo "<br>";
            }

               /*
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));

                for($selected_date = $start_date; $selected_date <= $end_date; $selected_date = date('Y-m-d', strtotime($selected_date. ' + 1 days')))
                {
                    echo '<h3>'.$selected_date.'</h3>';
                    echo report_receivables($dbc, $selected_date, '', '', '');
                    echo "<br>";
                }
                */

            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $contactid, $table_style, $table_row_style, $grand_total_style, $starttime, $endtime) {

    $report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="10%">Date</th>
    <th width="10%">'.TICKET_NOUN.'</th>
    <th width="70%">Heading</th>
    <th width="10%">Est Time</th>
    </tr>';

    $start_date = date('Y-m-d', strtotime($starttime));
    $end_date = date('Y-m-d', strtotime($endtime));

    for($selected_date = $start_date; $selected_date <= $end_date; $selected_date = date('Y-m-d', strtotime($selected_date. ' + 1 days')))
    {
        $cid = ','.$contactid.',';
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$selected_date.'</td>';

        $total_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(ticketid) AS total_ticket, GROUP_CONCAT(`ticketid` SEPARATOR ',') AS all_ticket FROM tickets WHERE ((contactid LIKE '%" . $cid . "%' AND to_do_date <= '".$selected_date."' AND to_do_end_date >= '".$selected_date."') OR (internal_qa_contactid LIKE '%" . $cid . "%' AND internal_qa_date = '".$selected_date."') OR (deliverable_contactid LIKE '%" . $cid . "%' AND deliverable_date = '".$selected_date."'))"));

        $each_tab = explode(',', $total_ticket['all_ticket']);
        $total_assigned_time = array();

        $ticket_no = '';
        $ticket_heading = '';
        $ticket_est_time = '';

        foreach ($each_tab as $ticketid) {

            $ticket_no .= "<a target= '_blank' href='../Ticket/index.php?edit=".$ticketid."'>#".$ticketid.'</a><br><br>';
            $ticket_heading .= get_tickets($dbc, $ticketid, 'heading').'<br><br>';

            if (strpos(get_tickets($dbc, $ticketid, 'contactid'), $cid) !== FALSE) {
                $est_time = get_tickets($dbc, $ticketid, 'max_time');
            } else if (strpos(get_tickets($dbc, $ticketid, 'internal_qa_contactid'), $cid) !== FALSE) {
                $est_time = get_tickets($dbc, $ticketid, 'max_qa_time');
            } else {
                $est_time = '00:00:00';
            }

            $ticket_est_time .= substr($est_time, 0, -3).'<br><br>';
        }

        $report_data .= '<td>'.$ticket_no.'</td>';
        $report_data .= '<td>'.$ticket_heading.'</td>';
        $report_data .= '<td>'.$ticket_est_time.'</td>';
        $report_data .= "</tr>";
    }

    $report_data .= "</table>";

    return $report_data;
}
?>