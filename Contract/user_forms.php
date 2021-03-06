<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['add_manual'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $form_id = $_POST['form_id'];
    $assign_id = $_POST['assign_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");
    $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
    $pdf_id = mysqli_insert_id($dbc);
    if(mysqli_num_rows($result)) {
        $assign_id = mysqli_fetch_array($result)['assign_id'];
        mysqli_query($dbc, "UPDATE `user_form_assign` SET `completed_date`=CURRENT_TIMESTAMP, `pdf_id`='$pdf_id' WHERE `assign_id`='$assign_id'");
    } else {
        mysqli_query($dbc, "INSERT INTO `user_form_assign` (`form_id`, `user_id`, `completed_date`, `pdf_id`) VALUES ('$form_id', '$user_id', CURRENT_TIMESTAMP, '$pdf_id')");
        $assign_id = mysqli_insert_id($dbc);
    }

    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
    $pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_'.$assign_id.'.pdf';
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");

    include('../Form Builder/generate_form_pdf.php');

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    
    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $pdf->Output('download/'.$pdf_name, 'F');

    $today_date = date('Y-m-d');
    $businessid = $_POST['contract_businessid'];
    $query_insert_site = "INSERT INTO `contracts_completed` (`contractid`, `businessid`, `staffid`, `contract_file`, `today_date`) VALUES ('$contractid', '$businessid', '".$_SESSION['contactid']."', 'download/".$pdf_name."', '$today_date')";
    $result_insert_site = mysqli_query($dbc, $query_insert_site);
    $infopdfid = mysqli_insert_id($dbc);
    
	$redirect = '?tab='.$_GET['tab'];
    echo '<script> window.location.replace("'.$redirect.'"); window.open("download/'.$pdf_name.'", "_blank"); </script>';
} else if(isset($_GET['blank_pdf'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $contractid = $_GET['contractid'];
    $form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts` WHERE `contractid` = '$contractid'"))['user_form_id'];
    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
    $pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_blank.pdf';

    include('../Form Builder/generate_form_pdf.php');

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    
    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $pdf->Output('download/'.$pdf_name, 'F');

    echo '<script> window.location.replace("download/'.$pdf_name.'"); </script>';
} else {
    $form_id = $user_form_id;
    $default_collapse = 'in';
    include('../Form Builder/generate_form_contents.php');
} ?>
<script>
$(document).ready(function () {
    $('[name="manual_btn"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>