<?php
include ('include.php');
include ('database_connection_htg.php');
$no_display = true;
include('tiles.php');
$all_tiles_list = array_merge([
        'admin_settings',
        'software_config',
        'security',
        'contacts',
        'contacts_inbox',
        'contacts3',
        'client_info',
        'contacts_rolodex',
        'staff',
        'documents',
        'orientation',
        'infogathering',
        'agenda_meeting',
        'sales',
        'certificate',
        'marketing_material',
        'internal_documents',
        'client_documents',
        'contracts',
        'driving_log',
        'hr',
        'preformance_review'],
	$hr_tile_list,
	[ 'package',
		'promotion',
		'services',
		'products',
		'labour',
		'material',
		'inventory',
		'vpl',
		'assets',
		'equipment',
		'custom',
		'intake',
		'pos',
		'posadvanced',
		'invoicing',
		'service_queue',
		'incident_report',
		'policy_procedure',
		'ops_manual',
		'emp_handbook',
		'how_to_checklist',
		'safety',
		'rate_card',
		'estimate',
		'field_ticket_estimates',
		'project',
		'calendar_rook',
        'interactive_calendar',
        'properties',
        'training_quiz'],
	$project_type_tile_list,
	[ 'client_projects',
		'project_workflow', ],
	$project_workflow_type_tile_list,
	[ 'shop_work_orders',
		'site_work_orders',
		'ticket' ],
	$ticket_type_tile_list,
	[ 'daysheet',
		'time_tracking',
		'calllog',
		'budget',
		'profit_loss',
		'gao',
		'checklist',
		'tasks',
		'scrum',
		'communication',
		'communication_schedule',
		'email_communication',
		'phone_communication',
		'punch_card',
		'sign_in_time',
		'payroll',
		'purchase_order',
		'sales_order',
		'newsboard',
		'field_job',
		'expense',
		'payables',
		'billing',
		'report',
		'passwords',
		'gantt_chart',
		'client_documentation',
		'medication',
		'individual_support_plan',
		'social_story',
		'routine',
		'day_program',
		'match',
		'fund_development',
		'how_to_guide',
		'charts',
		'daily_log_notes',
		'timesheet',
        'software_guide',
		'helpdesk',
		'archiveddata',
		'ffmsupport',
		'appointment_calendar',
		'booking',
		'check_in',
		'check_out',
		'treatment_charts',
		'accounts_receivables',
		'therapist',
		'exercise_library',
		'notifications',
		'reactivation',
		'goals_compensation',
		'crm',
        'drop_off_analysis',
        'injury',
		'manual',
		'confirm',
        'website',
        'staff_documents',
        'safety_manual',
		'members',
		'non_verbal_communication',
		'form_builder',
        'vendors',
		'quote',
		'cost_estimate',
		'documents_all' ],
	$documents_all_tile_list);
?>