<?php include('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
ob_clean();
$tab_list = explode(',',get_config($dbc, 'invoice_tabs'));
$redirected = false;
foreach($tab_list as $tab_name) {
	if(!$redirected && check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', $_SESSION['role'], $tab_name)) {
		switch($tab_name) {
			case 'sell': header('Location: add_invoice.php');
				$redirected = true;
				break;
			case 'today': header('Location: today_invoice.php');
				$redirected = true;
				break;
			case 'all': header('Location: all_invoice.php');
				$redirected = true;
				break;
			case 'invoices': header('Location: invoice_list.php');
				$redirected = true;
				break;
			case 'refunds': header('Location: refund_invoices.php');
				$redirected = true;
				break;
			case 'unpaid': header('Location: unpaid_invoice_list.php');
				$redirected = true;
				break;
			case 'voided': header('Location: void_invoices.php');
				$redirected = true;
				break;
			case 'ui_report': header('Location: unpaid_insurer_invoice.php');
				$redirected = true;
				break;
			case 'cashout': header('Location: cashout.php');
				$redirected = true;
				break;
		}
	}
}