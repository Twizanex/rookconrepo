<?php
//This is the list of all accordions in Tickets. This will be the default order of accordions in a Ticket. Each different Ticket type will have it's own setting for accordion sort order. The setting for the sort order will be compared against this array and if there are any missing fields in the sort order config it will add it to the list so no accordions will be missed.
$accordion_list = [
	'Customer History' => ['Customer History Business Ticket Type','Customer History Business Project Type','Customer History Business Ticket Project Type','Customer History Customer Ticket Type','Customer History Customer Project Type','Customer History Customer Ticket Project Type','Customer History Field Display Notes','Customer History Field Service Template'],
	'Information' => ['PI Business','PI Name','PI Guardian','PI AFE','PI Project','PI Pieces','PI Sites','PI Rate Card','PI Customer Order','PI Sales Order','PI Invoice','PI Order','PI Purchase Order','PI WTS Order','PI Cross Ref','PI Invoiced Out','PI Work Order','PI Scheduled Date','PI Date of Entry','PI Time of Entry','PI Agent','PI Status','PI Ban','PI Vendor','PI Operator','PI Waste Manifest','PI Reference Ticket','PI TDG Doc Num','PI VTI Num','PI TEXT FIELD'],
	'Purchase Order List' => ['PO List','PO Slider Icons'],
	'Customer Orders' => ['CO List','CO Slider Icons'],
	'Details' => ['Detail Business','Detail Project','Detail Contact','Detail Contact Phone','Detail Rate Card','Detail Heading','Detail Date','Detail Staff','Detail Staff Times','Detail Member Times','Detail Times','Detail Duration','Detail Notes','Detail Image','Detail Max Capacity','Detail Staff Capacity','Detail Status','Detail Total Budget Time'],
	'Contact Notes' => ['Attached Business Notes','Attached Contact Notes','Attached Contact Notes Add Note','Attached Contact Notes Anyone Can Add'],
	'Individuals' => [],
	'Path & Milestone' => [],
	'Fees' => [],
	'Location' => ['Location Site','Location Site Info','Location Notes','Emergency','Location Notes Anyone Can Add','Location Filter By Client'],
	'Members ID' => ['Members ID Age','Members ID Parental Guardian Family Contact','Members ID Emergency Contact','Members ID Medications'],
	'Mileage' => [],
	'Staff' => ['Staff Position','Staff Rate Positions','Staff Rate','Staff Start','Staff Set Hours','Staff Hours','Staff Estimate','Staff Overtime','Staff Travel','Staff Subsistence','Staff Subsistence Options','Staff Check In','Staff Set Hours Time Sheet','Staff Billing','Staff Anyone Can Add'],
	'Staff Tasks' => [],
	'Members' => ['Contact Set Hours','Members Profile','Members Parental Guardian Family Contact','Members Emergency Contact','Members Medical Details','Members Key Methodologies','Members Daily Log Notes'],
	'Clients' => ['Contact Set Hours'],
	'Wait List' => ['Wait List Members Medications','Wait List Members Guardians','Wait List Members Emergency Contacts','Wait List Members Key Methodologies','Wait List Members Daily Log Notes'],
	'Check In' => ['Checkin Hide All Button','Checkin Staff','Checkin Staff_Tasks','Checkin Delivery','Checkin Delivery Require','Checkin Clients','Checkin Members','Checkin material','Checkin equipment','Checkin Get To Work'],
	'Medication' => [],
	'Ticket Details' => ['Help Desk','Service Category','Service Type','Service Heading','Service Quantity','Service Total Time','Service # of Rooms','Service Rate Card','Service Estimated Hours','Service Fuel Charge','Service Preferred Staff','Service Total Price','Service Total Estimated Hours','Details Heading','Service Description','Details Where','Details Who','Details Why','Details What','Details Position','Details Checklist'],
	'Service Staff Checklist' => ['Service Staff Checklist Group Cat Type','Service Staff Checklist Another Room','Service Staff Checklist Another Room Copy Values','Service Staff Checklist Extra Billing','Service Staff Checklist Scroll To Accordion','Service Staff Checklist History','Service Staff Checklist One Service Template Only','Service Staff Checklist Checked In Staff','Service Staff Checklist Default Customer Template'],
	'Service Extra Billing' => ['Service Extra Billing Display Only If Exists','Service Extra Billing Add Option'],
	'Equipment' => ['Equipment Inline','Equipment Category','Equipment Make','Equipment Model','Equipment Unit','Equipment Residue','Equipment Hours','Equipment Volume','Equipment Rate','Equipment Rate Options','Equipment Cost','Equipment Status'],
	'Checklist' => [],
	'Checklist Items' => [],
	'Charts' => [],
	'Safety' => [],
	'Timer' => ['Time Tracking Estimate Complete','Time Tracking Estimate QA','Time Tracking Time Allotted','Time Tracking Current Time','Time Tracking Timer','Time Tracking Timer Manual'],
	'Materials' => ['Material Inline','Material Category','Material Subcategory','Material Type','Material Manual','Material Quantity','Material Volume','Material Rate','Auto Check In Materials','Auto Check Out Materials'],
	'Location Details' => ['Location Details From','Location Details From Notes','Location Details To','Location Details To Notes','Location Details Volume'],
	'Residue' => ['Residue Type','Residue Quantity','Residue Volume','Residue Rate'],
	'Other List' => ['Other Type','Other Quantity','Other Volume','Other Rate'],
	'Inventory' => ['Inventory Basic Inline','Inventory Basic Category','Inventory Basic Part','Inventory Basic Inventory','Inventory Basic Price','Inventory Basic Quantity','Inventory Basic Total','Inventory Basic Piece Type','Inventory Basic PO Line','Inventory Basic PO Line Sort','Inventory Basic Vendor','Inventory Basic Weight','Inventory Basic Units','Inventory Basic Dimensions','Inventory Basic Dimension Units','Inventory Basic Used','Inventory Basic Received','Inventory Basic Discrepancy','Inventory Basic Back Order','Inventory Basic Location','Inventory Basic Billing'],
	'Inventory General' => ['Inventory General Piece Count Type','Inventory General All Copy','Inventory General Piece Copy','Inventory General Piece','Inventory General Piece Type','Inventory General PO Number','Inventory General PO Item','Inventory General PO Line Item','Inventory General PO Dropdown','Inventory General PO Line Read','Inventory General PO Line Sort','Inventory General Piece Dim Weight','Inventory General Weight','Inventory General Units','Inventory General Dimensions','Inventory General Dimension Units','Inventory General Shipment Count Weight','Inventory General Site','Inventory General Complete','Inventory General Total Count Weight','Inventory General Notes','Inventory General Detail','Inventory General Detail by Pallet','Inventory General Total Summary','Inventory General Manual Add Pieces','Inventory General Manual Remove Pieces','Inventory General Pallet Default Locked','Customer Complete Exits Ticket'],
	'Inventory Detail' => ['Inventory Detail Category','Inventory Detail Unique','Inventory Detail Quantity','Inventory Detail Site','Inventory Detail Piece Type','Inventory Detail Customer Order','Inventory Detail PO Num','Inventory Detail PO Line','Inventory Detail PO Dropdown','Inventory Detail PO Read','Inventory Detail PO Sort','Inventory Detail Vendor','Inventory Detail Weight','Inventory Detail Units','Inventory Detail Net Weight','Inventory Detail Net Units','Inventory Detail Gross Weight','Inventory Detail Gross Units','Inventory Detail Dimensions','Inventory Detail Dimension Units','Inventory Detail Used','Inventory Detail Received','Inventory Detail Discrepancy','Inventory Detail Discrepancy Yes No','Inventory Detail Back Order','Inventory Detail Location','Inventory Detail Manual Add'],
	'Inventory Return' => ['Inventory Return Same','Inventory Return Item','Inventory Return Details','Inventory Return ATA'],
	'Miscellaneous' => ['Miscellaneous Inline','Miscellaneous Name','Miscellaneous Price','Miscellaneous Quantity','Miscellaneous Total','Miscellaneous Billing'],
	'Purchase Orders' => [],
	'Attached Purchase Orders' => ['PO Name','PO 3rd Party','PO Invoice','PO Price','PO Mark Up','PO Total'],
	'Delivery' => ['Assigned Equipment','Delivery Stops','Delivery Stops Order','Delivery Stops Volume','Delivery Pickup Equipment Category','Delivery Pickup Equipment Make','Delivery Pickup Equipment Model','Delivery Pickup Equipment','Delivery Pickup','Delivery Pickup Address','Delivery Pickup Coordinates','Delivery Pickup Client','Delivery Pickup Customer','Delivery Pickup Phone','Delivery Pickup Type','Delivery Pickup Volume','Delivery Pickup Cube','Delivery Pickup ETA','Delivery Pickup Customer Est Time','Delivery Pickup Date','Delivery Pickup Order','Delivery Pickup Timeframe','Delivery Pickup Arrival','Delivery Pickup Departure','Delivery Pickup Description','Delivery Pickup Upload','Delivery Pickup Service List','Delivery Pickup Default Services','Delivery Pickup Warehouse Only','Delivery Pickup Populate Warehouse Address','Delivery Pickup Status','Delivery Pickup Populate Google Link','Delivery Pickup Notes','Delivery Pickup Dropoff Map','Delivery Calendar History'],
	'Transport' => ['Transport Origin Contact','Transport Origin Name','Transport Origin','Transport Origin Country','Transport Origin Save Contact','Transport Origin Link','Transport Origin Warehouse','Transport Origin Arrival','Transport Origin Departure','Transport Destination Contact','Transport Destination Name','Transport Destination','Transport Destination Country','Transport Destination Save Contact','Transport Destination Link','Transport Destination Warehouse','Transport Destination Arrival','Transport Destination Departure','Transport Carrier','Transport Type','Transport Number','Transport Billed','Transport Container','Transport Manifest','Transport Ship Date','Transport Arrive Date','Transport Warehouse'],
	'Reading' => ['Readings CO','Readings O2','Readings LEL','Readings H2S','Readings Bump','Readings Arrival','Readings Departure'],
	'Tank Reading' => ['Tank Readings Tank #','Tank Readings Opening','Tank Readings Closing','Tank Readings Watercut','Tank Readings Oil'],
	'Shipping List' => ['Shipping List Type','Shipping List Class','Shipping List Subclass','Shipping List Unit','Shipping List PG','Shipping List Quantity','Shipping List Volume','Shipping List Rate'],
	'Documents' => ['Project Docs','Contact Docs','Documents Docs','Documents Links'],
	'Check Out' => ['Checkout Hide All Button','Checkout Show Checked In Only','Checkout Staff','Checkout Staff_Tasks','Checkout Delivery','Checkout Clients','Checkout Members','Checkout material','Checkout equipment','Checkout Notes'],
	'Staff Check Out' => ['Staff Checkout Hide All Button','Staff Checkout Staff','Staff Checkout Notes'],
	'Deliverables' => ['Deliverable Status','Deliverable To Do','Deliverable Repeat','Deliverable Internal','Deliverable Customer'],
	'Billing' => ['Billing Services','Billing Staff','Billing Inventory','Billing Misc','Billing Discount','Billing Total Discount','Billing Total'],
	'Addendum' => [],
	'Client Log' => [],
	'Debrief' => [],
	'Member Log Notes' => [],
	'Cancellation' => ['Cancellation Reason','Cancellation Notes'],
	'Customer Notes' => ['Customer Stop Status','Customer Property Damage','Customer Product Damage','Customer Rate','Customer Delivery Rate','Customer Recommend','Customer Recommend Likely','Customer Add Details','Customer Sign','Customer Complete','Customer Slider','Customer Sign Off Complete Status','Customer Complete Exits Ticket'],
	'Custom Notes' => [],
	'Notes' => [],
	'Summary' => ['Summary Times','No Track Time Sheets','Time Tracking','Time Tracking Hours','Time Tracking Date Time','Time Tracking Date','Time Tracking Time','Time Tracking Current','Time Tracking Set','Time Tracking Hrs','Time Tracking Edit Past Date','Time Tasks','Summary Materials Summary','Summary Notes','Planned Tracked Payable Staff','Planned Tracked Payable Members','Total Time Tracked Staff','Total Time Tracked Members','Total Time Tracked Clients'],
	'Multi-Disciplinary Summary Report' => ['Child Name','Date of Birth','Date of Report','Background Information','Progress','Clinical Impacts','Proposed Goal Areas','Recommendations'],
	'Complete' => ['Complete Sign & Force Complete'],
	'Notifications' => ['Notify Business','Notify Client','Notify Staff','Notify List','Notify PDF','Notify Anyone Can Add'],
	'Region Location Classification' => ['Con Region','Con Location','Con Classification'],
	'Incident Reports' => [],
	'Pressure' => ['Pressure Pressure Test','Pressure PSV SET','Pressure Purge Closed'],
	'Chemicals' => ['Chemical Location','Chemical Hours','Chemical Hrs Cost','Chemical Volume','Chemical Vol Cost','Chemical Total Cost'],
	'Intake' => [],
	'History' => [],
	'Work History' => ['Work History Services','Work History Service Sub Totals','Work History Staff Tasks','Work History Materials']
];

$custom_accordion_list = [
	'Detail Business' => 'Business',
	'Detail Contact' => 'Contact',
	'Detail Project' => PROJECT_NOUN,
	'Detail Heading' => TICKET_NOUN.' Name',
	'Detail Date' => 'Date',
	'Detail Staff' => 'Staff',
	'Detail Staff Times' => 'Staff Times',
	'Detail Member Times' => 'Member Times',
	'Detail Times' => TICKET_NOUN.' Times',
	'Detail Notes' => 'Notes',
	'Detail Max Capacity' => 'Max Capacity',
	'Detail Image' => 'Attached Image',
	'PI AFE' => 'AFE#',
	'PI Pieces' => 'Piece Work',
	'PI Sites' => 'Site',
	'PI Customer Order' => 'Customer Order #',
	'PI Sales Order' => 'Sales Order Invoice #',
	'PI Invoice' => 'Invoice #',
	'PI WTS Order' => 'WTS Order #',
	'PI Cross Ref' => 'Cross Reference #',
	'PI Invoiced Out' => 'Invoiced (Y/N)',
	'PI Work Order' => 'Work Order #',
	'PI Date of Entry' => 'Date of Entry',
	'PI Agent' => 'Additional Contact',
	'Cancellation Reason' => 'Cancellation',
	'Cancellation Notes' => 'Cancellation Notes',
	'FFMCUSTOM Fees' => 'Fees',
	'FFMCUSTOM Path & Milestone' => PROJECT_NOUN.' Path & Milestone',
	'Con Region' => 'Region',
	'Con Location' => 'Location',
	'Con Classification' => 'Classification',
	'FFMCUSTOM Day Tracking' => 'Day Tracking',
	'FFMCUSTOM Ticket Deliverables' => TICKET_NOUN.' Deliverables'
];

$action_mode_ignore_fields = ['TEMPLATE Work Ticket','Hide New Ticketid','Send Emails','Tag Notes','Additional','Export Ticket Log','Send Archive Email','Ticket Edit Cutoff','Quick Reminder Button','Business Set Delivery','Force All Caps','Ticket Tasks Add Button','Ticket Tasks Auto Check In','Ticket Tasks Projects','Ticket Tasks Ticket Type','Ticket Tasks Groups','Task Extra Billing','Extra Billing Create New','Heading Blank','Heading Business Invoice','Heading Bus Invoice Date','Heading Project Invoice Date','Heading Business Date','Heading Contact Date','Heading Business','Heading Contact','Heading Date','Heading Milestone Date','Heading Assigned','Service Inline','Service Multiple','Notes Limit','Complete Sign & Force Complete','Complete Default Session User','Service Group Cat Type All Services','Service Group Cat Type All Services Combine Checklist','Service Inline','Service Multiple','Service Staff Checklist Group Cat Type','Service Staff Checklist Extra Billing','Service Staff Checklist Another Room','Service Staff Checklist Another Room Copy Values','Service Extra Billing Display Only If Exists','Complete Do Not Require Notes','Edit Section Options','Service Limit Service Category','Hide Trash Icon','Complete Hide Sign & Complete','Finish Create Recurring Ticket','Service Staff Checklist Scroll To Accordion','Service Staff Checklist One Service Template Only','Service Staff Checklist Checked In Staff','Customer Complete Exits Ticket','Staff Anyone Can Add','Location Notes Anyone Can Add','Location Filter By Client','Attached Contact Notes Anyone Can Add','Notes Anyone Can Add','Notes Alert','Notes Email Default On','Complete Combine Checkout Summary','Service Staff Checklist Default Customer Template','Complete Email Users On Complete','Notify Anyone Can Add'];