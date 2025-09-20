<?php
// IMPORT
$lang['err_check_record'] 					= "Please check at least one item.";
$lang['err_import_empty'] 					= "File is empty";
$lang['success_import'] 					= "File successfully imported";
$lang['temp_success_import'] 				= "File successfully read and ready for import.";
$lang['err_valid_ag_code'] 					= "Please provide a valid AG Code";
$lang['err_valid_vendor_code'] 				= "Please provide a valid Vendor Code";
$lang['err_valid_cost_center_code'] 		= "Please provide a valid Cost Center Code";
$lang['err_valid_site_code'] 				= "Please provide a valid SLOC";
$lang['err_valid_puchasing_group_code'] 	= "Please provide a valid Purchasing Group Code";
$lang['err_valid_gl_account_code'] 			= "Please provide a valid GL Account Code";
$lang['err_valid_apv_status_code'] 			= "Please provide a valid APV Status Code";
$lang['err_valid_sheet_name'] 				= "Please don't modify the sheet name.";
$lang['err_valid_boq_no'] 					= "Please provide a valid BOQ Number";
$lang['err_valid_batch_upload']				= "Invalid batch upload!";
$lang['err_incomplete_fields']				= "Uploaded file does not match the template's record columns, please check missing or blank columns!";
$lang['err_mismatch_sheet']					= "Invalid import template file.";

//ERROR MESSAGES
$lang['err_unauthorized_direct_access']     = "Direct access not allowed.";
$lang['err_trans_no_access_tabs']           = "Sorry, you don't have permission to access any tabs under this page. Please contact the system administrator.";

//SUCCESS MESSAGES
$lang['succ_tagging_task_complete']         = "Task tagged as completed.";
$lang['succ_tagging_task_returned']         = "Task tagged as returned.";
$lang['succ_tagging_task_approved']         = "Task tagged as approved.";
$lang['succ_tagging_task_disapproved']       = "Task tagged as disapproved.";
$lang['succ_tagging_task_get']              = "Task successfully assigned.";

//AUDIT SCORE MESSAGE
$lang['err_audit_score_required']         	= "Please enter at least one audit score value.";

//FILE VERSIONING
$lang['succ_file_uploaded'] 				= "File sucessfully uploaded";

//AUDIT TRAIL
$lang['audit_trail_task_comment_add']		= "A comment has been added in %s.";
$lang['audit_trail_task_comment_update']	= "A comment has been updated in %s.";
$lang['audit_trail_task_comment_delete']	= "A comment has been deleted in %s.";
$lang['audit_trail_task_status_complete']	= "Task %s has been completed.";
$lang['audit_trail_task_status_return']	    = "Task %s has been returned.";
$lang['audit_trail_task_status_approve']	= "Task %s has been approved.";
$lang['audit_trail_task_status_disapprove']	= "Task %s has been disapproved.";
$lang['audit_trail_task_status_skipped']	= "Task %s has been skipped.";
$lang['audit_trail_task_status_assigned']	= "Task %s has been assigned.";

//SAVING SPECIFIC NAMES
$lang['data_spec_saved'] 		            = "%s was successfully saved.";
$lang['data_spec_updated'] 		            = "%s was successfully updated.";
$lang['data_spec_deleted'] 		            = "%s was successfully deleted.";


$lang['add_transaction_io']               = 'added an internal order';
$lang['add_transaction_soa']              = 'added SOA';
$lang['add_transaction_gr']               = 'added a goods receipt';
$lang['add_transaction_dr']               = 'added a delivery receipt';
$lang['add_transaction_pr']               = 'added a purchase request';
$lang['add_transaction_po']               = 'added a purchase order';
$lang['add_transaction_apv']              = 'added an APV';
$lang['add_transaction_site_nomination']  = 'nominated a site';
$lang['add_transaction_boq']              = 'added a BOQ';
$lang['add_transaction_project']          = 'added a project';
$lang['add_transaction_po_batch']         = 'uploaded a purchase order';
$lang['add_transaction_soa_batch']        = 'uploaded SOA';

$lang['add_transaction_contract_new']     = 'added a new contract';
$lang['add_transaction_contract_renewal'] = 'added a renewal';
$lang['update_transaction_contract']	  = 'updated a contract';
$lang['update_transaction_contract_renewal'] = 'updated a renewal';

$lang['update_transaction_io']            = 'updated an internal order number from <span class="purple-text">"%s"</span> to';
$lang['update_transaction_po_released']	  = 'updated released details of purchase order';

//REST API SPECIFIC ERROR MESSAGE
$lang['err_required_workflow_for_id']		= 'Workflow For ID is required.';
$lang['err_required_role_code']				= 'Role Code is required.';
$lang['err_required_org_code']				= 'Org Code is required.';
$lang['err_required_org_type_code']			= 'Org Type Code is required.';
$lang['err_required_user_id']				= 'User ID is required.';
$lang['err_required_pria_task_id']			= 'PRIA Task ID is required.';
$lang['err_required_pria_stage_id']			= 'PRIA Stage ID is required.';
$lang['err_required_task_type']				= 'Task Type is required.';
$lang['err_required_dr_num']				= 'DR Num is required.';
$lang['err_required_dr_amount']				= 'DR Amount is required.';
$lang['err_required_dr_recipient']			= 'DR Recipient is required.';
$lang['err_required_dr_date']				= 'DR Date is required.';
$lang['err_required_site_id']				= 'Site ID is required.';
$lang['err_required_account_group_code']	= 'Account Group Code is required.';
$lang['err_required_transaction_tab']	    = 'Transaction Tab is required.';
$lang['err_required_vendor_code']			= 'Vendor Code is required.';
$lang['err_required_fhr_document_num']		= 'FHR Document Number is required.';
$lang['err_required_fhr_date_submitted']	= 'FHR Date Submitted is required.';
$lang['err_required_fhr_file']				= 'FHR File is required.';
$lang['err_required_reference_number']		= 'Reference Number is required.';
$lang['err_required_document_type_code']	= 'Document Type Code is required.';
$lang['err_required_soa_file']				= 'SOA File is required.';
$lang['err_required_soa_sys_file_name']		= 'Sys File Name is required.';
$lang['err_reference_number_not_found']		= 'Reference Number not found. Please try again.';
$lang['err_duplicate_entry']				= 'Duplicate entry. Please try again.';
$lang['err_form_not_saved']                 = 'Please save the form first.';

//CONTRACTS
$lang['contract_exist'] 					= "There's an active contract";

//ATTACHMENT
$lang['err_required_attachment_file'] 		= "Attachment is required";


//TASKS
$lang['err_required_doc_dr_file'] 			= "DOC DR file is required";
$lang['err_required_remarks'] 				= "Remarks is required";

//LOGIN
$lang['err_required_username'] 				= "Username is required.";
$lang['err_required_username_or_email'] 	= "Username or email is required.";
$lang['err_required_password'] 				= "Password is required.";
$lang['err_required_user_does_not_exist'] 	= "User does not exist.";
$lang['err_required_invalid_role'] 			= "User does not have vendor role.";
$lang['err_required_credentials_invalid'] 	= "Username or email and password did not match.";
$lang['msg_credentials_valid'] 				= "User credentials are correct.";

$lang['prep_error']							= "Error: %s";
$lang['prep_warning']						= "Warning: %s";

$lang['error_import']						= "Import failed. Please see error/warning column for error/s.";
$lang['error_import_batch']					= "Batch Upload failed. Please see error/warning column for error/s.";

$lang['warning_import']						= "Import success with warning! Please see error/warning column for conflicting transaction/s.";
$lang['warning_import_batch']				= "Batch Upload success with warning! Please see error/warning column for conflicting file/s.";

$lang['invalid_transaction']				= "Transaction with reference number %s does not exists in the database. Please uncheck then try again.";

$lang['invalid_upload_file']				= "Invalid transaction file %s.";
$lang['invalid_transaction_file']			= "Transaction with reference number %s does not exists in the database.";
$lang['exist_transaction_file']				= "Transaction with reference number %s already has an uploaded file.";
$lang['exist_file_transaction_for']			= "Transaction with reference number %s already exists in the database.";

$lang['invalid_value']						= "Invalid %s";
$lang['invalid_data_for']					= "Invalid %s for %s";
$lang['exist_transaction_for']				= "Transaction with reference number %s already exists for %s";
$lang['not_exist_transaction_for']			= "Transaction with reference number %s does not exists for %s";
$lang['exist_transaction_not_approved_for']	= "Transaction with reference number %s is not yet approved for %s";

$lang['err_too_short']						= "Invalid %s. Required %s character/s.";
$lang['err_too_long']						= "Invalid %s. Only %s character/s is allowed.";
$lang['err_future_date']					= "Invalid %s. %s cannot be later than today.";
$lang['err_date_range']						= "Invalid %s range.";
$lang['invalid_value_against_value']		= "Invalid %s. %s must be a %s.";
$lang['err_date_order']						= "%s cannot be later than %s.";
$lang['err_io_cycle_num']					= "Invalid Cycle Number. Non-numeric character is not allowed. Cannot be less than " . IO_MIN_CYCLE_NUM . " or greater than " . IO_MAX_CYCLE_NUM . ".";


$lang['err_last_dr_exist']				    = "Last delivery already exists. Please untick the checkbox";

$lang['err_no_update']						= "No update for transaction.";
$lang['err_not_match']						= "Invalid %s. %s for %s did not match.";
$lang['err_record_exist']					= "Invalid %s. %s already exists.";
$lang['err_record_exist_temp']				= "Invalid %s. %s already exists in this import.";

$lang['err_trans_not_for_payment']			= "Invalid %s. Transaction is not yet completed.";
$lang['err_trans_not_for_payment2']			= "Invalid %s. Transaction is not for payment.";

$lang['notif_done'] 						= "Item was successfully marked as done.";

$lang['contract_status_update_message']		= "You may now create and upload your contract renewal recommendation.";






/*
* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The additional Lang option is added for the CDI BOM Approval
* @ReferencedBy: 
*/

$lang['add_transaction_bom_approval']  = 'Added a BOM for approval';



/*
* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The additional Lang option is added for the CDI Construction Plan
* @ReferencedBy: 
*/
$lang['add_transaction_construction_plan']  = 'Added a Construction Plan';


/*

* @Author      : Jhun Baria
* @Date        : 2023-08-11 22: 00: 00 
* @Desc        : The additional Lang option is added for the CDI Construction Plan
* @ReferencedBy: 
*/
$lang['add_transaction_payment']  = 'Added a Payment';