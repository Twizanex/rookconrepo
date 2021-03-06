<?php include_once('../include.php');
include_once('../function.php');
include_once('../tcpdf/tcpdf.php');
ob_clean();
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$form = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='".filter_var($_GET['form'],FILTER_SANITIZE_STRING)."'")->fetch_assoc();
if($_GET['revision'] > 0 && $form['pdf_name'] != '' && $ticketid > 0 && file_exists('download/'.config_safe_str($form['pdf_name']).'_'.$_GET['revision'].'_'.$ticketid.'.pdf')) {
	echo "<script> window.top.location.href = 'download/".config_safe_str($form['pdf_name']).'_'.$_GET['revision'].'_'.$ticketid.".pdf'; </script>";
} else if(!empty($_GET['form'])) {
	DEFINE("PDF_IMAGES", $form['pages']);

	class MYPDF extends TCPDF {
		public function Header() {
			$image = explode('#*#',PDF_IMAGES)[$this->page-1];
			if(!empty($image) && file_exists('pdf_contents/'.$image)) {
				$image = 'pdf_contents/'.$image;
				$this->Image($image, 0, 0, 216, 279, '', '', '', false, 300, '', false, false, 0);
			}
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	$pdf->SetMargins(10, 10, 10);
	$pdf->SetFooterMargin(10);
	$pdf->SetAutoPageBreak(FALSE, 0);
	$pdf->SetFont('helvetica', '', 8);
	$pdf->setPrintFooter(false);
	$revision = $_GET['revision'] > 0 ? $_GET['revision'] : 0;

	foreach(explode('#*#',$form['pages']) as $page => $img) {
		$pdf->AddPage();
		$fields = $dbc->query("SELECT `fields`.*, `values`.`field_value`, `values`.`revision` FROM `ticket_pdf_fields` `fields` LEFT JOIN `ticket_pdf_field_values` `values` ON `fields`.`field_name`=`values`.`field_name` LEFT JOIN `ticket_pdf_field_values` `older` ON `values`.`ticketid`=`older`.`ticketid` AND `values`.`pdf_type`=`older`.`pdf_type`AND `values`.`field_name`=`older`.`field_name` AND `values`.`id` < `older`.`id` WHERE (`older`.`id` IS NULL AND $revision=0 OR `values`.`revision`=$revision) AND `values`.`ticketid`='$ticketid' AND `fields`.`pdf_type`='{$form['id']}' AND `deleted`=0 AND `page`='".($page+1)."'");
		while($field = $fields->fetch_assoc()) {
			$revision = $field['revision'];
			$pdf->SetXY($field['x'],$field['y']);
			$pdf->SetFont('helvetica', '', $field['font_size']);
			if(in_array(explode(':',$field['default_value'])[0], ['checkbox','checkbox_residue','checkbox_other_products','checkbox_shipping_list'])) {
				$value_check = explode('-',explode(':',$field['default_value'])[1])[1];
				$pdf->TextField($field['field_name'], $field['width'], $field['height'], ['multiline'=>true,'lineWidth'=>0,'borderStyle'=>'none','defaultStyle'=>['textFont'=>['fontSize'=>'auto'],'textAlign'=>'center']], ['v'=>($field['field_value'] == $value_check ? 'X' : '')]);
			} else if($field['input_class'] == 'revisionField') {
				$pdf->writeHTMLCell($field['width'], $field['height'], $field['x'],$field['y'], $html='Revision # '.$revision, $border=0);
			} else if($field['input_class'] == 'editLink') {
				$pdf->setVisibility('screen');
				$pdf->Write('', 'Edit '.$form['pdf_name'], WEBSITE_URL.'/Ticket/index.php?custom_form='.$form['id'].'&ticketid='.$ticketid, false, 'L', true);
				$pdf->setVisibility('all');
			} else if(in_array('no-edit',explode(':',$field['options']))) {
				$pdf->Cell($field['width'], $field['height'], trim(html_entity_decode($field['field_value'],ENT_QUOTES)));
			} else {
				$pdf->TextField($field['field_name'], $field['width'], $field['height'], ['multiline'=>true,'lineWidth'=>0,'borderStyle'=>'none','defaultStyle'=>['textFont'=>['fontSize'=>'auto'],'textAlign'=>'center']], ['v'=>trim(html_entity_decode($field['field_value'],ENT_QUOTES))]);
			}
		}
	}

	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$file_name = 'download/'.config_safe_str($form['pdf_name']).'_'.$revision.'_'.$ticketid.'.pdf';
	$pdf->Output($file_name, 'F');
	echo "<script> window.top.location.replace('".$file_name."'); </script>";
}
