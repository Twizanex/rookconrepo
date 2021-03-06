<?php
include('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if(isset($_GET['contactid']) && !empty($_GET['contactid'])) {
	$contactid = $_GET['contactid'];	
} else {
	$contactid = $_SESSION['contactid'];
}

$contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$contactid'"));

if($contact['category'] == 'Staff') {
	$field_config = '';
	$config_query = mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='Staff' AND `accordion` IS NOT NULL AND `order` IS NOT NULL ORDER BY `subtab`, `order`");
	while($config_row = mysqli_fetch_assoc($config_query)) {
		$field_config .= ','.$config_row['contacts'].',';
	}
	$field_config = explode(',',$field_config);
}

$fields_left = [
	[['Employee Number','Employee ID','Employee #'], '/img/id-card.png', $contactid],
	[['First Name','Last Name'], '/img/person.PNG', decryptIt($contact['first_name']).' '.decryptIt($contact['last_name'])],
	[['Position'], '/img/job.png', $contact['position']],
	[['Home Phone'], '/img/home_phone.PNG', decryptIt($contact['home_phone'])],
	[['Office Phone'], '/img/office_phone.PNG', decryptIt($contact['office_phone'])],
	[['Cell Phone'], '/img/cell_phone.PNG', decryptIt($contact['cell_phone'])],
	[['Email Address'], '/img/email.PNG', decryptIt($contact['email_address'])],
	[['Start Date'], '/img/calendar.png', $contact['start_date']]
];
foreach($fields_left as $key => $value) {
	if(!in_array_any($value[0], $field_config)) {
		unset($fields_left[$key]);
	}
}
$fields_right = [
	[['Business'], '/img/business.PNG', get_client($dbc, $contact['businessid'])],
	[['Name'], '/img/business.PNG', decryptIt($contact['name'])],
	[['Location'], '/img/address.PNG', $contact['con_location']],
	[['Business Address'], '/img/address.PNG', decryptIt($contact['business_street'])],
	[['Mailing Address'], '/img/address.PNG', $contact['mailing_address']],
	[['Address'], '/img/address.PNG', $contact['address']],
	[['Birth Date','Date of Birth'], '/img/birthday.png', $contact['birth_date'].(( $contact['birth_date']=='0000-00-00' || empty($contact['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($contact['birth_date']), date_create('now'))->y)],
	[['LinkedIn'], '/img/icons/social/linkedin.png', $contact['linkedin']],
	[['Facebook'], '/img/icons/social/facebook.png', $contact['facebook']],
	[['Twitter'], '/img/icons/social/twitter.png', $contact['twitter']],
	[['Google+'], '/img/icons/social/google+.png', $contact['google_plus']],
	[['Instagram'], '/img/icons/social/instagram.png', $contact['instagram']],
	[['Pinterest'], '/img/icons/social/pinterest.png', $contact['pinterest']],
	[['YouTube'], '/img/icons/social/youtube.png', $contact['youtube']],
	[['Blog'], '/img/icons/social/rss.png', $contact['blog']],
];
foreach($fields_right as $key => $value) {
	if(!in_array_any($value[0], $field_config)) {
		unset($fields_right[$key]);
	}
}

class MYPDF extends TCPDF {
    public function Header()
    {

    }
	public function footer() {

	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 0);

$html = '';
$profile_img = '';
/*$user_preset = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `preset_profile_picture` FROM `user_settings` WHERE `contactid` = '$contactid'"))['preset_profile_picture'];
if($user_preset != '') {
	$profile_img = WEBSITE_URL.'/img/avatars/'.$user_preset;
} else */
if(url_exists(WEBSITE_URL."/Profile/download/profile_pictures/".$contactid.".jpg")) {
	$profile_img = WEBSITE_URL."/Profile/download/profile_pictures/".$contactid.".jpg";
} else if(url_exists(WEBSITE_URL."/Staff/download/".$contactid.".jpg")) {
	$profile_img = WEBSITE_URL."/Staff/download/".$contactid.".jpg";
}
$html .= '<h1 style="text-align: center">';
$logo = WEBSITE_URL.'/Settings/download/'.get_config($dbc, 'logo_upload');
if(url_exists($logo)) {
	$html .= '<img src="'.$logo.'" style="height:25px;"> ';
}
$html .= get_contact($dbc, $contactid);
if($profile_img != '') {
	$html .= '<img src="'.$profile_img.'" style="height:25px;"> ';
}
$html .= '</h1>';

$html .= '<table cellpadding="2">';
$counter = 0;
while(!empty($fields_left) || !empty($fields_right)) {
	$value_left = array_shift($fields_left);
	$value_right = array_shift($fields_right);
	$html .= '<tr>';
	$html .= '<td width="50%" style="border: 1px solid black;">'.(!empty($value_left[1]) ? '<img src="'.WEBSITE_URL.$value_left[1].'" style="height: 7px;"> '.$value_left[2] : '').'</td>';
	$html .= '<td width="50%" style="border: 1px solid black;">'.(!empty($value_right[1]) ? '<img src="'.WEBSITE_URL.$value_right[1].'" style="height: 7px;"> '.$value_right[2] : '').'</td>';
	$html .= '</tr>';
}
$html .= '</table>';

$pdf->SetFont('helvetica', '', 7);
$pdf->AddPage('L', [85,55]);
$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
$pdf_url = 'download/'.$contactid.'_'.date('Y-m-d').'_idcard.pdf';
if(!file_exists('download')) {
    mkdir('download', 0777, true);
}
$pdf->Output($pdf_url, 'F'); ?>
<script>
window.location.replace("<?= $pdf_url ?>");
</script>