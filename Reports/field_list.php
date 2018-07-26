<?php /* Report Field List
	Array index:
	0 => File Name
	1 => Title
	2 => Field config value
	3 => Security config value */

$operations_reports = [
	'Daysheet'=>['report_daysheet.php','Therapist Day Sheet','Daysheet','pt_daysheet'],
	'Therapist Stats'=>['report_stat.php','Therapist Stats','Therapist Stats','pt_stats'],
	'Block Booking vs Not Block Booking'=>['report_bb_vs_not_bb.php','Block Booking vs Not Block Booking','Block Booking vs Not Block Booking','bb_v_nbb'],
	'Injury Type'=>['report_injury.php','Injury Type','Injury Type','injury_type'],
	'Treatment Report'=>['report_treatment.php','Treatment Report','Treatment Report','treatment'],
	'Equipment List'=>['report_equipment_list.php','Equipment List','Equipment List','equipment_list'],
	'Equipment Transfer'=>['report_equip_transfer.php','Equipment Transfer History','Equipment Transfer','equip_transfer'],
	'Work Order'=>['report_work_order.php','Work Order','Work Order','work_order'],
	'Staff Tickets'=>['reports_staff_tickets.php','Staff '.TICKET_TILE,'Staff Tickets','staff_tickets'],
	'Day Sheet Report'=>['reports_daysheet_reports.php','Day Sheet Report','Day Sheet Report','day_sheet_report'],
	'Appointment Summary'=>['report_daily_appoint_summary.php','Appointment Summary','Appointment Summary','appt_summary'],
	'Patient Block Booking'=>['report_patient_block_booking.php','Block Booking','Patient Block Booking','block_booking'],
	'Assessment Tally Board'=>['report_tally_board.php','Assessment Tally Board','Assessment Tally Board','assessment_tallyboard'],
	'Assessment Follow Up'=>['report_assessment_followup.php','Assessment Follow Ups','Assessment Follow Up','assessment_followup'],
	'Field Jobs'=>['report_field_jobs.php','Field Jobs','Field Jobs','field_jobs'],
	'Shop Work Orders'=>['report_shop_work_orders.php','Shop Work Orders','Shop Work Orders','shop_work_orders'],
	'Shop Work Order Time'=>['report_operations_shop_time.php','Shop Work Order Time','Shop Work Order Time','shop_work_order_time'],
	'Shop Work Order Task Time'=>['report_operations_shop_task_time.php','Shop Work Order Time by Task','Shop Work Order Task Time','shop_work_order_task'],
	'Site Work Orders'=>['report_site_work_orders.php','Site Work Orders','Site Work Orders','site_work_orders'],
	'Scrum Business Productivity Summary'=>['reports_scrum_business_productivity_summary.php','Scrum Business Productivity Summary','Scrum Business Productivity Summary','scrum_business_productivity_summary'],
	'Scrum Staff Productivity Summary'=>['reports_scrum_staff_productivity_summary.php','Scrum Staff Productivity Summary','Scrum Staff Productivity Summary','scrum_staff_productivity_summary'],
	'Scrum Status Report'=>['reports_scrum_status_report.php','Scrum Status Report','Scrum Status Report','scrum_status_report'],
	'Drop Off Analysis'=>['report_drop_off_analysis.php','Drop Off Analysis','Drop Off Analysis','dropoff_analysis'],
	'Discharge Report'=>['report_discharge.php','Discharge Report','Discharge Report','discharge'],
	'Ticket Report'=>['report_ticket.php',TICKET_NOUN.' Report','Ticket Report','ticket_report'],
	'Action Item Summary'=>['report_action_item_summary.php','Action Item Summary','Action Item Summary','action_item_summary'],
	'Site Work Time'=>['report_site_work_time.php','Site Work Order Time on Site','Site Work Time','site_work_time'],
	'Site Work Driving'=>['report_site_work_driving.php','Site Work Order Driving Logs','Site Work Driving','operations'],
	'Purchase Orders'=>['reports_purchase_orders.php','Purchase Orders','Purchase Orders','purchase_orders'],
	'Inventory Log'=>['report_inventory_log.php','Inventory Log','Inventory Log','inventory_log'],
	'Point of Sale'=>['reports_pos.php',(!empty(get_tile_title($dbc)) ? get_tile_title($dbc) : 'Point of Sale'),'Point of Sale','point_of_sale'],
	'POS'=>['report_pos_advanced.php',(!empty(get_tile_title($dbc)) ? get_tile_title($dbc) : 'Point of Sale (Advanced)'),'POS','point_of_sale_advanced'],
	'Credit Card on File'=>['report_cc_on_file.php','Credit Card on File','Credit Card on File','credit_card_on_file'],
	'Checklist Time'=>['report_checklist_time.php','Checklist Time Tracking','Checklist Time','checklist_time'],
	'Ticket Time Summary'=>['report_ticket_time_summary.php',TICKET_NOUN.' Time Summary','Ticket Time Summary','ticket_time_summary'],
	'Service Usage Report'=>['report_operation_service_usage.php','% BREAKDOWN OF SERVICES SOLD','Service Usage Report','service_usage'],
	'Ticket Attached'=>['report_operations_ticket_attached.php','Attached to '.TICKET_TILE,'Ticket Attached','ticket_attached'],
	'Ticket Deleted Notes'=>['report_operation_ticket_notes.php','Archived '.TICKET_NOUN.' Notes','Ticket Deleted Notes','ticket_deleted_notes'],
	'Download Tracker'=>['report_download_tracking.php','Downloaded Reports Tracker','Download Tracker','report_download_tracker'],
	'Ticket Inventory Transport'=>['report_inventory_transport.php',TICKET_NOUN.' Transport of Inventory','Ticket Inventory Transport','ticket_inventory_transport'],
	'Dispatch Travel Time'=>['report_operation_ticket_dispatch_time.php','Dispatch '.TICKET_NOUN.' Travel Time','Dispatch Travel Time','dispatch_time'],
	'Time Sheet'=>['report_operations_time_sheet.php','Time Sheets Report','Time Sheet','time_sheet'],
	'Ticket by Task'=>['report_operation_ticket_by_task.php',TICKET_NOUN.' by Task','Ticket by Task','ticket_by_task'],
	'Ticket Activity Report'=>['report_operation_ticket_tasks.php',TICKET_NOUN.' Activity Report per Customer','Ticket Activity Report','ticket_activity_report'],
	'Rate Card Report'=>['report_operations_rate_cards.php','Rate Cards Report','Rate Card Report','rate_card_report'],
	'Import Summary'=>['report_import_summary.php','Import Summary Report','Import Summary','import_summary'],
	'Import Details'=>['report_import_details.php','Detailed Import Report','Import Details','import_details'],
	'Ticket Manifest Summary'=>['report_daily_manifest_summary.php','Manifest Daily Summary','Ticket Manifest Summary','ticket_manifest_summary']
];
$sales_reports = [
	'Validation by Therapist'=>['report_daily_validation.php','Validation by Therapist','Validation by Therapist','pt_validation'],
	'POS Validation'=>['report_pos_daily_validation.php','POS Validation','POS Validation','validation'],
	'POS Advanced Validation'=>['report_pos_advanced_daily_validation.php','POS Advanced Validation','POS Advanced Validation','validation_advanced'],
	'Daily Deposit Report'=>['report_daily_deposit.php','Daily Deposit Report','Daily Deposit Report','daily_deposit'],
	'Monthly Sales by Injury Type'=>['report_review_sales.php','Monthly Sales by Injury Type','Monthly Sales by Injury Type','sales_injury_monthly'],
	'Invoice Sales Summary'=>['report_invoice_sales_summary.php','Invoice Sales Summary','Invoice Sales Summary','sales_invoice'],
	'Sales by Customer Summary'=>['report_sales_by_customer_summary.php','Sales by Customer Summary','Sales by Customer Summary','sales_customer'],
	'Sales History by Customer'=>['report_sales_by_customer_detail.php','Sales History by Customer','Sales History by Customer','sales_customer_history'],
	'Sales by Service Summary'=>['report_sales_by_product_service_summary.php','Sales by Service Summary','Sales by Service Summary','sales_service'],
	'Sales by Service Category'=>['report_sales_by_product_service_category.php','Sales by Service Category','Sales by Service Category','sales_service_category'],
	'Sales by Inventory Summary'=>['report_sales_by_inventory_summary.php','Sales by Inventory Summary','Sales by Inventory Summary','sales_inventory'],
	'Sales Summary by Injury Type'=>['report_daily_sales_summary.php','Sales Summary by Injury Type','Sales Summary by Injury Type','sales_injury'],
	'Inventory Analysis'=>['report_general_inventory.php','Inventory Analysis','Inventory Analysis','inventory_analysis'],
	'Unassigned/Error Invoices'=>['report_unassigned_invoices.php','Unassigned/Error Invoices','Unassigned/Error Invoices','error_invoice'],
	'Staff Revenue Report'=>['report_revenue.php','Staff Revenue Report','Staff Revenue Report','staff_revenue'],
	'Expense Summary Report'=>['report_expenses.php','Expense Summary Report','Expense Summary Report','expense_report'],
	'Phone Communication'=>['report_phone_communication.php','Phone Communication','Phone Communication','project'],
	'Sales by Inventory/Service Detail'=>['report_sales_by_product_service_detail.php','Sales by Inventory/Service Detail','Sales by Inventory/Service Detail','sales_inv_service_detail'],
	'Payment Method List'=>['report_payment_method_list.php','Payment Method List','Payment Method List','pay_methods'],
	'Patient History'=>['report_patient_appoint_history.php','Customer History','Patient History','patient_history'],
	'Receipts Summary Report'=>['report_receipt_summary.php','Receipts Summary Report','Receipts Summary Report','sales_receipts'],
	'Estimate Item Closing By Quantity'=>['report_estimate_qty.php','Estimate Item Closing % By Quantity','Estimate Item Closing % By Quantity','sales_receipts'],
	'Gross Revenue by Staff'=>['report_gross_revenue_by_staff.php','Gross Revenue by Staff','Gross Revenue by Staff','staff_gross_revenue'],
	'Patient Invoices'=>['report_patient_unpaid_invoices.php','Customer Invoices','Patient Invoices','patient_invoice'],
	'POS Sales Summary'=>['report_pos_daily_sales_summary.php','POS Sales Summary','POS Sales Summary','sales_summary'],
	'POS Advanced Sales Summary'=>['report_pos_advanced_daily_sales_summary.php','POS Advanced Sales Summary','POS Advanced Sales Summary','sales_summary_advanced'],
	'Profit-Loss'=>['report_profit_loss.php','Profit-Loss','Profit-Loss','profit_loss'],
	'Profit-Loss POS Advanced'=>['report_profit_loss_pos_advanced.php','Profit-Loss (POS Advanced)','Profit-Loss POS Advanced','profit_loss_pos_advanced'],
	'Transaction List by Customer'=>['report_transaction_list_by_customer.php','Transaction List by Customer','Transaction List by Customer','transaction_list'],
	'Unbilled Invoices'=>['report_unbilled_charges.php','Unbilled Invoices','Unbilled Invoices','unbilled_invoices'],
	'Deposit Detail'=>['report_deposit_detail.php','Deposit Detail','Deposit Detail','deposit_detail'],
	'Sales Estimates'=>['report_sales_estimates.php','Sales Estimates','Sales Estimates','sales_estimates']
];
$ar_reports = [
	'A/R Aging Summary'=>['report_ar_aging_summary.php','A/R Aging Summary','A/R Aging Summary','ar_aging'],
	'Patient Aging Receivable Summary'=>['report_receivables_patient_summary.php','Customer Aging Receivable Summary','Patient Aging Receivable Summary','ar_patient_aging'],
	'Insurer Aging Receivable Summary'=>['report_receivables_summary.php','Insurer Aging Receivable Summary','Insurer Aging Receivable Summary','ar_insurer_aging'],
	'By Invoice'=>['report_receivables.php','By Invoice#','By Invoice#','ar_invoice'],
	'Customer Balance Summary'=>['report_account_receivable.php','Customer Balance Summary','Customer Balance Summary','ar_customer_balance'],
	'Customer Balance by Invoice'=>['report_customer_balance_detail.php','Customer Balance by Invoice','Customer Balance by Invoice','ar_customer_invoice'],
	'Collections Report by Customer'=>['report_collections_report.php','Collections Report by Customer','Collections Report by Customer','ar_customer_collections'],
	'Invoice List'=>['report_invoice_list.php','Invoice List','Invoice List','invoice_list'],
	'POS Receivables (Basic)'=>['report_pos_receivables.php','POS Receivables (Basic)','POS Receivables (Basic)','receivables'],
	'POS Receivables (Advanced)'=>['report_pos_receivables_advanced.php','POS Receivables (Advanced)','POS Receivables (Advanced)','receivables'],
	'UI Invoice Report'=>['ui_invoice_reports.php','UI Invoice Report','UI Invoice Report','ar_ui_invoice']
];
$pnl_reports = [
	'Revenue Receivables'=>['report_pnl_revenue_receivables.php','Revenue & Receivables','Revenue Receivables','revenue_receivables'],
	'Staff Compensation'=>['report_pnl_staff_compensation.php','Staff Compensation','Staff Compensation','staff_compensation'],
	'Expenses'=>['report_pnl_expenses.php','Expenses','Expenses','expenses'],
	'Costs'=>['report_pnl_costs.php','Costs','Costs','costs'],
	'Summary'=>['report_pnl_summary.php','Summary','Summary','summary'],
	'Labour Report'=>['report_pnl_labour.php','Labour Report','Labour Report','Labour Report'],
	'Dollars By Service'=>['report_pnl_service.php','Dollars By Service','Dollars By Service','Dollars By Service']
];
$marketing_reports = [
	'Customer Contact List'=>['report_customer_contact_list.php','Customer Contact List','Customer Contact List','customer_list'],
	'Customer Stats'=>['report_customer_stats.php','Customer Stats','Customer Stats','customer_stats'],
	'Demographics'=>['report_demographics.php','Demographics','Demographics','demographs'],
	'CRM Recommendations - By Date'=>['report_crm_recommend_date.php','CRM Recommendations - By Date','CRM Recommendations - By Date','crm_recommend_date'],
	'CRM Recommendations - By Customer'=>['report_crm_recommend_customer.php','CRM Recommendations - By Customer','CRM Recommendations - By Customer','crm_recommend_customer'],
	'POS Coupons'=>['report_pos_coupons.php','POS Coupons','POS Coupons','pos_coupons'],
	'Postal Code'=>['report_postalcode.php','Postal Code','Postal Code','postal_code'],
	'Referral'=>['report_referral.php','Referrals','Referral','referral'],
	'Web Referrals Report'=>['report_web_referral.php','Web Referrals','Web Referrals Report','web_referrals'],
	'Pro Bono Report'=>['report_marketing_pro_bono.php','Pro-Bono','Pro Bono Report','pro_bono'],
	'Net Promoter Score'=>['report_marketing_net_promoter_score.php','Net Promoter Score','Net Promoter Score','net_promoter_score'],
    'Driver Report'=>['reports_contact_driver.php','Driver Report','Driver Report','driver_report'],
	'Contact Report by Status'=>['report_contact_report_by_status.php','Contact Report by Status','Contact Report by Status','contact_report_by_status'],
	'Contact Postal Code'=>['report_marketing_contact_pc.php','Contact Postal Code','Contact Postal Code','contact_postal_code'],
	'Site visitors'=>['report_marketing_site_visitors.php','Website Visitors','Site Visitors','site_visitors'],
	'Cart Abandonment'=>['report_marketing_cart_abandonment.php','Cart Abandonment','Cart Abandonment','cart_abandonment']
];
$compensation_reports = [
	'Adjustment Compensation'=>['report_compensation_adjustments.php','Adjustment Compensation','Adjustment Compensation','compensation_adjust'],
	'Hourly Compensation'=>['report_hourly_compensation.php','Hourly Compensation','Hourly Compensation','compensation_hourly'],
	'Therapist Compensation'=>['report_compensation.php','Therapist Compensation','Therapist Compensation','compensation_pt'],
	'Statutory Holiday Pay Breakdown'=>['report_stat_holiday_pay.php','Statutory Holiday Pay Breakdown','Statutory Holiday Pay Breakdown','compensation_statutory_breakdown'],
	'Timesheet Payroll'=>['report_compensation_timesheet_payroll.php','Time Sheet Payroll','Timesheet Payroll','timesheet_payroll']
];
$customer_reports = [
	'Customer Sales by Customer Summary'=>['report_sales_by_customer_summary.php','Sales by Customer Summary','Customer Sales by Customer Summary','customer_sales_customer'],
	'Customer Sales History by Customer'=>['report_sales_by_customer_detail.php','Sales History by Customer','Customer Sales History by Customer','customer_sales_customer_history'],
	'Customer Patient Invoices'=>['report_patient_unpaid_invoices.php','Customer Invoices','Customer Patient Invoices','customer_patient_invoice'],
	'Customer Transaction List by Customer'=>['report_transaction_list_by_customer.php','Transaction List by Customer','Customer Transaction List by Customer','customer_transaction_list'],
	'Customer Patient History'=>['report_patient_appoint_history.php','Customer History','Customer Patient History','customer_patient_history'],
	'Customer Customer Balance Summary'=>['report_account_receivable.php','Customer Balance Summary','Customer Customer Balance Summary','customer_ar_customer_balance'],
	'Customer Customer Balance by Invoice'=>['report_customer_balance_detail.php','Customer Balance by Invoice','Customer Customer Balance by Invoice','customer_ar_customer_invoice'],
	'Customer Collections Report by Customer'=>['report_collections_report.php','Collections Report by Customer','Customer Collections Report by Customer','customer_ar_customer_collections'],
	'Customer Patient Aging Receivable Summary'=>['report_receivables_patient_summary.php','Customer Aging Receivable Summary','Customer Patient Aging Receivable Summary','customer_ar_patient_aging'],
	'Customer Customer Contact List'=>['report_customer_contact_list.php','Customer Contact List','Customer Customer Contact List','customer_customer_list'],
	'Customer Customer Stats'=>['report_customer_stats.php','Customer Stats','Customer Customer Stats','customer_customer_stats'],
	'Customer CRM Recommendations - By Customer'=>['report_crm_recommend_customer.php','CRM Recommendations - By Customer','Customer CRM Recommendations - By Customer','customer_crm_recommend_customer'],
	'Customer Postal Code'=>['report_postalcode.php','Postal Code','Customer Postal Code','customer_postal_code'],
	'Customer Service Rates'=>['report_contact_service_rates.php','Service Rates','Customer Service Rates','customer_service_rates']
];
$staff_reports = [
	'Staff Staff Tickets'=>['reports_staff_tickets.php','Staff '.TICKET_TILE,'Staff Staff Tickets','staff_staff_tickets'],
	'Staff Scrum Staff Productivity Summary'=>['reports_scrum_staff_productivity_summary.php','Scrum Staff Productivity Summary','Staff Scrum Staff Productivity Summary','staff_scrum_staff_productivity_summary'],
	'Staff Daysheet'=>['report_daysheet.php','Therapist Day Sheet','Staff Daysheet','staff_pt_daysheet'],
	'Staff Therapist Stats'=>['report_stat.php','Therapist Stats','Staff Therapist Stats','staff_pt_stats'],
	'Staff Day Sheet Report'=>['reports_daysheet_reports.php','Day Sheet Report','Staff Day Sheet Report','staff_day_sheet_report'],
	'Staff Staff Revenue Report'=>['report_revenue.php','Staff Revenue Report','Staff Staff Revenue Report','staff_staff_revenue'],
	'Staff Gross Revenue by Staff'=>['report_gross_revenue_by_staff.php','Gross Revenue by Staff','Staff Gross Revenue by Staff','staff_staff_gross_revenue'],
	'Staff Validation by Therapist'=>['report_daily_validation.php','Validation by Therapist','Staff Validation by Therapist','staff_pt_validation'],
	'Staff Staff Compensation'=>['report_pnl_staff_compensation.php','Staff Compensation','Staff Staff Compensation','staff_staff_compensation'],
];
$history_reports = [
	'History Staff History'=>['reports_staff_history.php','Staff History','History Staff History','history_staff_history'],
	'History Checklist History'=>['reports_checklist_history.php','Checklist History','History Checklist History','history_checklist_history'],
];

$report_list = array_merge($operations_reports, $sales_reports, $ar_reports, $pnl_reports, $marketing_reports, $compensation_reports, $customer_reports, $staff_reports, $history_reports);
