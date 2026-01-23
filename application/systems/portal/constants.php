<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 |---------------------------------------------------------------------
 | SYSTEMS DATABASE
 |---------------------------------------------------------------------
 | Define the name of database/s used in the system.
 */
//define('DB_PORTAL', PROJECT_CODE.'_launch');
define('DB_PORTAL', PROJECT_CODE);
// define('DB_PORTAL', 'dev_' . PROJECT_CODE);
// define('DB_PORTAL', 'qa_' . PROJECT_CODE);

/*
 |---------------------------------------------------------------------
 | SYSTEM CODE
 |---------------------------------------------------------------------
 | Define the system code, this will be used to tag which features and
 | modules are included in a system
 */
define('PORTAL', 'PORTAL');

/*
 |---------------------------------------------------------------------
 | SYSTEMS FOLDER
 |---------------------------------------------------------------------
 | These constants are used when defining the systems folder path of
 | your controller in the hyperlink or function.
 */
define('SYSTEM_PORTAL', 'portal');

/*
 |---------------------------------------------------------------------
 | MODULES FOLDER
 |---------------------------------------------------------------------
 | These constants are used when defining the modules folder path of
 | your controller in the hyperlink or function.
 */

define('PORTAL_DASHBOARD', 'dashboard');
define('PORTAL_COMMON', 'common');
define('PORTAL_TASK', 'tasks');
define('PORTAL_SUBMITTED_DOCUMENT', 'submitted_document');
define('PORTAL_PURCHASE_REQUEST', 'purchase_request');
define('PORTAL_CONTRACT_GROWER', 'contract_growers');
define('PORTAL_TOLL_PARTNERS', 'toll_partners');
define('PORTAL_GOODS_MARINADE', 'goods_marinades');
define('PORTAL_GOODS_GOOD', 'goods');
define('PORTAL_MANPOWERS', 'manpower');
define('PORTAL_LESSOR', 'lessors');
define('PORTAL_SOA_BASED', 'soa_based');
define('PORTAL_CONTRACTOR', 'contractors');
define('PORTAL_FEEDMILL_TRUCKER', 'feedmill_truckers');
define('PORTAL_OUTBOUND_TRUCKER', 'outbound_truckers');
define('PORTAL_INBOUND_TRUCKER', 'inbound_truckers');
define('PORTAL_FORWARDER', 'forwarders');
define('PORTAL_CODE_LIBRARIES', 'code_libraries');
define('PORTAL_VENDORS', 'vendors');
define('PORTAL_SITES', 'sites');
define('PORTAL_BUSINESS_CENTERS', 'business_centers');
define('PORTAL_QUICK_ADD', 'quick_add');
define('PORTAL_TRANSACTIONS', 'transactions');
define('PORTAL_FILE_VERSION', 'file_version');
define('PORTAL_TASK_ATTACHMENT', 'task_attachment');
define('PORTAL_IMPORT_REFERENCE', 'import_reference');
define('PORTAL_REPORTS', 'reports');

/*
 |---------------------------------------------------------------------
 | MODULE TABS FOLDER
 |---------------------------------------------------------------------
 | These constants are used when defining the module tabs folder path of
 | your controller in the hyperlink or function.
 */
 define('PORTAL_TAB_OVERVIEW', 'overview');
 define('PORTAL_TAB_IO', 'internal_orders');
 define('PORTAL_TAB_FILES', 'files');
 define('PORTAL_TAB_PURCHASE_REQUESTS', 'purchase_requests');
 define('PORTAL_TAB_PURCHASE_ORDERS', 'purchase_orders');
 define('PORTAL_TAB_SOA', 'soa');
 define('PORTAL_TAB_DR', 'dr');
 define('PORTAL_TAB_SITE_NOMINATIONS', 'site_nominations');
 define('PORTAL_TAB_BOQ', 'boq');
 define('PORTAL_TAB_PROJECTS', 'projects');
 define('PORTAL_TAB_CONTRACTS', 'contracts');
 define('PORTAL_TAB_RENEWAL', 'renewal');
 define('PORTAL_TAB_TRANSMITTAL', 'document_transmittal');

/*
 |---------------------------------------------------------------------
 | PROJECT MODULES
 |---------------------------------------------------------------------
 | Declare all the modules in your project.
 */
 define('MODULE_PORTAL_DASHBOARD','PORTAL_HOME');
 /* define('MODULE_PORTAL_TASK','PORTAL_TASK');
 define('MODULE_SUBMITTED_DOCUMENT','PORTAL_SUBMITTED_DOCUMENT');
 define('MODULE_PURCHASE_REQUEST','PORTAL_PURCHASE_REQUEST');
 define('MODULE_CONTRACT_GROWER','PORTAL_CONTRACT_GROWER'); */
 define('MODULE_PORTAL_CODE_LIBRARIES','PORTAL_CODE_LIBRARIES');
 define('MODULE_PORTAL_VENDORS','PORTAL_VENDORS');
 define('MODULE_PORTAL_SITES','PORTAL_SITES');
 define('MODULE_PORTAL_BUSINESS_CENTERS','PORTAL_BUSINESS_CENTERS');
 define('MODULE_PORTAL_QUICK_ADD','PORTAL_QUICK_ADD');
 define('MODULE_PORTAL_TRANSACTIONS','PORTAL_TRANSACTIONS');
 define('MODULE_PORTAL_FILE_VERSION','PORTAL_FILE_VERSION');
 define('MODULE_PORTAL_TASK_ATTACHMENT','PORTAL_TASK_ATTACHMENT');


 define('MODULE_PORTAL_TRANS_CONTRACT_GROWERS','PORTAL_CONTRACT_GROWERS');
 define('MODULE_PORTAL_TRANS_CONTRACTORS','PORTAL_CONTRACTORS');
 define('MODULE_PORTAL_TRANS_FORWARDERS','PORTAL_FORWARDERS');
 define('MODULE_PORTAL_TRANS_LESSORS','PORTAL_LESSORS');
 define('MODULE_PORTAL_TRANS_CONTRACT_GROWER_files','PORTAL_CONTRACT_GROWER_FILES');

 define('MODULE_PORTAL_TRANS_TOLL_PARTNERS','PORTAL_TOLL_PARTNERS');
 define('MODULE_PORTAL_TRANS_TOLL_PARTNERS_OVERVIEW','PORTAL_TOLL_OVERVIEW');
 define('MODULE_PORTAL_TRANS_TOLL_PARTNERS_SOA','PORTAL_TOLL_SOA');
 define('MODULE_PORTAL_TRANS_TOLL_PARTNERS_FILES','PORTAL_TOLL_FILES');

 define('MODULE_PORTAL_TRANS_INBOUND_TRUCKERS','PORTAL_INBOUND_TRUCKERS');
 define('MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_OVERVIEW','PORTAL_INBOUND_OVERVIEW');
 define('MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_SOA','PORTAL_INBOUND_SOA');
 define('MODULE_PORTAL_TRANS_INBOUND_TRUCKERS_FILES','PORTAL_INBOUND_FILES');

 define('MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS','PORTAL_OUTBOUND_TRUCKERS');
 define('MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_OVERVIEW','PORTAL_OUTBOUND_OVERVIEW');
 define('MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_SOA','PORTAL_OUTBOUND_SOA');
 define('MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS_FILES','PORTAL_OUTBOUND_FILES');

 define('MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS','PORTAL_FEEDMILL_TRUCKERS');
 define('MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_OVERVIEW','PORTAL_FEEDMILL_OVERVIEW');
 define('MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_SOA','PORTAL_FEEDMILL_SOA');
 define('MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS_FILES','PORTAL_FEEDMILL_FILES');

 define('MODULE_PORTAL_TRANS_GOODS_GOODS','PORTAL_GOODS_GOODS');
 define('MODULE_PORTAL_TRANS_GOODS_MARINADES','PORTAL_GOODS_MARINADES');

 define('MODULE_PORTAL_QA_IO','PORTAL_QA_IO');
 define('MODULE_PORTAL_QA_PO','PORTAL_QA_PO');
 define('MODULE_PORTAL_QA_GR','PORTAL_QA_GR');
 define('MODULE_PORTAL_QA_DR','PORTAL_QA_DR');
 define('MODULE_PORTAL_QA_APV','PORTAL_QA_APV');
 define('MODULE_PORTAL_QA_PO_BATCH','PORTAL_QA_PO_BATCH');
 define('MODULE_PORTAL_QA_PR','PORTAL_QA_PR');
 define('MODULE_PORTAL_QA_SOA','PORTAL_QA_SOA');
 define('MODULE_PORTAL_QA_SOA_BATCH','PORTAL_QA_SOA_BATCH');

 define('MODULE_PORTAL_TRANS_CG_OVERVIEW','PORTAL_CG_OVERVIEW');
 define('MODULE_PORTAL_TRANS_CG_IO','PORTAL_CG_IO');
 define('MODULE_PORTAL_TRANS_CG_FILES','PORTAL_CG_FILES');

 define('MODULE_PORTAL_TRANS_GOODS_G_OVERVIEW','PORTAL_GG_OVERVIEW');
 define('MODULE_PORTAL_TRANS_GOODS_G_PR','PORTAL_GG_PR');
 define('MODULE_PORTAL_TRANS_GOODS_G_PO','PORTAL_GG_PO');
 define('MODULE_PORTAL_TRANS_GOODS_G_SOA','PORTAL_GG_SOA');
 define('MODULE_PORTAL_TRANS_GOODS_G_FILES','PORTAL_GG_FILES');

 define('MODULE_PORTAL_TRANS_GOODS_M_OVERVIEW','PORTAL_GM_OVERVIEW');
 define('MODULE_PORTAL_TRANS_GOODS_M_PR','PORTAL_GM_PR');
 define('MODULE_PORTAL_TRANS_GOODS_M_PO','PORTAL_GM_PO');
 define('MODULE_PORTAL_TRANS_GOODS_M_DO','PORTAL_GM_DR');
 define('MODULE_PORTAL_TRANS_GOODS_M_SOA','PORTAL_GM_SOA');
 define('MODULE_PORTAL_TRANS_GOODS_M_FILES','PORTAL_GM_FILES');

 define('MODULE_PORTAL_TRANS_CONTRACTORS_OVERVIEW', 'PORTAL_CON_OVERVIEW');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_SN', 'PORTAL_CON_SITE_NOMINATION');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_BOQ', 'PORTAL_CON_BOQ');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS', 'PORTAL_CON_PROJECTS');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_PR', 'PORTAL_CON_PR');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_PO', 'PORTAL_CON_PO');
 define('MODULE_PORTAL_TRANS_CONTRACTORS_FILES', 'PORTAL_CON_FILES');

 define('MODULE_PORTAL_TRANS_LESSORS_OVERVIEW', 'PORTAL_LESSORS_OVERVIEW');
 define('MODULE_PORTAL_TRANS_LESSORS_CONTRACTS', 'PORTAL_LESSORS_CONTRACTS');
 define('MODULE_PORTAL_TRANS_LESSORS_RENEWAL', 'PORTAL_LESSORS_RENEWALS');
 define('MODULE_PORTAL_TRANS_LESSORS_FILES', 'PORTAL_LESSORS_FILES');

 define('MODULE_PORTAL_TRANS_MANPOWER','PORTAL_MANPOWER');
 define('MODULE_PORTAL_TRANS_MANPOWER_OVERVIEW','PORTAL_MANPOWER_OVERVIEW');
 define('MODULE_PORTAL_TRANS_MANPOWER_SOA','PORTAL_MANPOWER_SOA');
 define('MODULE_PORTAL_TRANS_MANPOWER_FILES','PORTAL_MANPOWER_FILES');

 define('MODULE_PORTAL_TRANS_FORWARDER_OVERVIEW', 'PORTAL_FORWARDER_OVERVIEW');
 define('MODULE_PORTAL_TRANS_FORWARDER_DR', 'PORTAL_FORWARDER_DR');
 define('MODULE_PORTAL_TRANS_FORWARDER_SOA', 'PORTAL_FORWARDER_SOA');
 define('MODULE_PORTAL_TRANS_FORWARDER_FILES', 'PORTAL_FORWARDER_FILES');

 define('MODULE_PORTAL_IMPORT_REFERENCE', 'PORTAL_IMPORT_REF');
 define('MODULE_PORTAL_IMPORT_REFERENCE_APV', 'PORTAL_IMPORT_REF_APV');
 define('MODULE_PORTAL_IMPORT_REFERENCE_PO', 'PORTAL_IMPORT_REF_PO');
 define('MODULE_PORTAL_IMPORT_REFERENCE_SOA', 'PORTAL_IMPORT_REF_SOA');

 //Additional transation for SOA based
 define('MODULE_PORTAL_TRANS_SOA_BASED','PORTAL_SOA_BASED');
 define('MODULE_PORTAL_TRANS_SOA_BASED_OVERVIEW','PORTAL_SOA_BASED_OVERVIEW');
 define('MODULE_PORTAL_TRANS_SOA_BASED_SOA','PORTAL_SOA_BASED_SOA');
 define('MODULE_PORTAL_TRANS_SOA_BASED_FILES','PORTAL_SOA_BASED_FILES');

 //Additional transaction for Document Transmittal
 define('MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL','PORTAL_DOCUMENT_TRANSMITTAL');
 define('MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL_OVERVIEW','PORTAL_DOCUMENT_TRANSMITTAL_OVERVIEW');
 define('MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL_DOCUMENTS','PORTAL_DOCUMENT_TRANSMITTAL_DOCUMENTS');
 define('MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL_FILES','PORTAL_DOCUMENT_TRANSMITTAL_FILES');

 //Reports
 define('MODULE_PORTAL_REPORTS','PORTAL_REPORTS');
 define('MODULE_PORTAL_REPORT_PAYMENT_STATUS','PORTAL_REPORT_PAYMENT_STATUS');
 define('MODULE_PORTAL_REPORT_AGING_REPORT','PORTAL_REPORT_AGING');


 /*
 |---------------------------------------------------------------------
 | FORMS
 |---------------------------------------------------------------------
 | Declare all the forms in your project.
 */
 define('FORM_INTERNAL_ORDERS','internal_orders');
 define('FORM_PURCHASE_ORDERS','purchase_orders');

 /*
 |---------------------------------------------------------------------
 | FORM TYPES
 |---------------------------------------------------------------------
 | Declare all the form types in your project.
 */
 define('TYPE_MEDVAC_DR','medvac_dr');
 define('TYPE_DOC_DR','doc_dr');
 define('TYPE_DOC_GR','doc_gr');
 define('TYPE_CLEANUP_REPORT','cleanup_report');
 define('TYPE_HARVEST_REPORT','harvest_report');
 define('TYPE_LIVES_SALES_REPORT','lives_sales_report');
 define('TYPE_FHR','fhr_report');

  define('TYPE_PR','pr');

 /*
 |---------------------------------------------------------------------
 | PORTAL ACTIONS
 |---------------------------------------------------------------------
 | Declare all the portal actions in your project.
 */
 define('PORTAL_ENCODE','1');
 define('PORTAL_UPLOAD','2');
 define('PORTAL_REVIEW','3');
 define('PORTAL_REVIEW_APPROVE','4');
 define('PORTAL_REVIEW_ENCODE','5');
 define('PORTAL_APPROVE','6');

 /*
 |---------------------------------------------------------------------
 | INITAIL FLAGS
 |---------------------------------------------------------------------
 | Declare all the initial flags in your project.
 */
 define( 'ACTIVE_FLAG', 1);
 define( 'INACTIVE_FLAG', 0);

 /*
 |---------------------------------------------------------------------
 | STATIC PATH
 |---------------------------------------------------------------------
 | These constants are used when defining the folder path of your css,
 | js, images and file upload
 */

 define('PATH_QA_TEMPLATE', PATH_UPLOADS.SYSTEM_PORTAL.'/templates/');
 define('PATH_QA_IMPORT_FILE', PATH_UPLOADS.SYSTEM_PORTAL.'/imported_files/');
 define('PATH_UPLOADED_FILES', PATH_UPLOADS.SYSTEM_PORTAL.'/uploaded_files/');
 define('PATH_UPLOADED_TMP_FILES', PATH_UPLOADS.SYSTEM_PORTAL.'/temp/');

 /*
 |---------------------------------------------------------------------
 | TEMPLATE FILENAMES
 |---------------------------------------------------------------------
 | These constants are used when defining the file name of the templates
 */

 define('TEMPLATE_QA_IO_LIST', 'io_list.xlsx');
 define('TEMPLATE_QA_PR_LIST', 'pr_list.xlsx');
 define('TEMPLATE_QA_SOA_LIST', 'soa_list.xlsx');
 define('TEMPLATE_QA_DR_LIST', 'dr_list.xlsx');
 define('TEMPLATE_QA_PO_LIST', 'po_list.xlsx');
 define('TEMPLATE_QA_PO_BATCH', 'po_batch.xlsx');
 define('TEMPLATE_QA_GR_LIST', 'gr_list.xlsx');
 define('TEMPLATE_QA_APV_LIST', 'apv_list.xlsx');
 define('TEMPLATE_QA_SOA_BATCH', 'soa_batch.xlsx');

 /*
 * ------------------------------
 * ERROR PAGE
 * ------------------------------
 * These contants are used to load the specific error pages
 */

 define('PORTAL_ERR_PAGE_LOAD', 'errors/html/error_exception');
 define('PORTAL_ERR_PAGE_MODAL', 'errors/html/error_exception');
 define('PORTAL_ERR_PAGE_TAB', 'errors/html/error_exception');

 /*
 * ------------------------------
 * QUICK ADD TEMP TABLES
 * ------------------------------
 * These contants are used for temp table declaration
 */

 define('PORTAL_TMP_QA_IO', 'temp_ios');
 define('PORTAL_TMP_QA_PO', 'temp_pos');
 define('PORTAL_TMP_QA_PR', 'temp_prs');
 define('PORTAL_TMP_QA_SOA', 'temp_soas');
 define('PORTAL_TMP_QA_DR', 'temp_drs');
 define('PORTAL_TMP_QA_GR', 'temp_grs');
 define('PORTAL_TMP_QA_APV', 'temp_apvs');

 define('PORTAL_TMP_QA_SOA_BATCH', 'temp_soa_batch');
 define('PORTAL_TMP_QA_PO_BATCH', 'temp_po_batch');

 /*
 * ------------------------------
 * QUICK ADD EXCEL TABLES
 * ------------------------------
 * These contants are used for sheet name in import functionality
 */

 define('PORTAL_SHEET_QA_IO', 'Internal Orders');
 define('PORTAL_SHEET_QA_PO', 'Purchase Orders');
 define('PORTAL_SHEET_QA_PR', 'Purchase Requests');
 define('PORTAL_SHEET_QA_SOA', 'Soa');
 define('PORTAL_SHEET_QA_DR', 'DR');
 define('PORTAL_SHEET_QA_GR', 'GR');
 define('PORTAL_SHEET_QA_APV', 'APV');
 define('PORTAL_SHEET_QA_SOA_BATCH', 'Soa Batch');
 define('PORTAL_SHEET_QA_PO_BATCH', 'Purchase Orders Batch');

 /*
 * ------------------------------
 * QUICK ADD ACTUAL TABLES
 * ------------------------------
 * These contants are used for actual table declaration
 */

 define('PORTAL_ACTUAL_QA_IO', 'internal_orders');
 define('PORTAL_ACTUAL_QA_PO', 'purchase_orders');
 define('PORTAL_ACTUAL_QA_PR', 'purchase_requisitions');
 define('PORTAL_ACTUAL_QA_SOA', 'soa');
 define('PORTAL_ACTUAL_QA_DR', 'delivery_goods_receipt');
 define('PORTAL_ACTUAL_QA_GR', 'delivery_goods_receipt');
 define('PORTAL_ACTUAL_QA_PAYMENTS', 'payments');
 define('PORTAL_ACTUAL_QA_PAYMENT_APVS', 'payment_apvs');
 define('PORTAL_ACTUAL_QA_SOA_BATCH', 'soa');
 define('PORTAL_ACTUAL_QA_PO_BATCH', 'purchase_orders');
 define('PORTAL_ACTUAL_QA_DOCUMENTS', 'documents');

/*
 * ------------------------------
 * Administrator user id
 * ------------------------------
 *
 */
define('ADMINISTRATOR_UID', 1);


/*
 * ------------------------------
 * PO Status Codes
 * ------------------------------
 *
 */
define('PO_ACTIVE', 'ACTIVE');
define('PO_CANCELLED', 'CANCELLED');
define('PO_CONVERTED', 'CONVERTED');
define('PO_RELEASED', 'RELEASED');

/*
 * ------------------------------
 * SOA Types
 * ------------------------------
 *
 */
define('SOA_CENTRAL', 'CENTRAL');
define('SOA_NORMAL', 'NORMAL');

/*
 * ------------------------------
 * IMPORT ERROR INDICATOR
 * ------------------------------
 *
 */
define('MSG_ERROR_TRUE', '1');
define('MSG_ERROR_FALSE', '0');

/*
 * ------------------------------
 * ERROR FIELD IN TEMP TABLE
 * ------------------------------
 *
 */
define('ERROR_FIELD', 'err_field');


/*
 * ------------------------------
 * ACCOUNT GROUPS
 * ------------------------------
 *
 */
define('AG_CONTRACT_GROWERS', 'CGL');
define('AG_CONTRACTORS', 'CTR');
define('AG_FEEDMILL', 'FMT');
define('AG_FORWARDERS', 'FWD');
define('AG_GOODS', 'GOODS');
define('AG_GOODS_BAVI', 'GBV');
define('AG_GOODS_BFFI', 'GBF');
define('AG_GOODS_MARINADES', 'GMR');
define('AG_INBOUND', 'INBOUND');
define('AG_INBOUND_CENTRAL', 'IBC');
define('AG_INBOUND_NORMAL', 'IBN');
define('AG_LESSORS', 'LSR');
define('AG_MANPOWER', 'MPW');
define('AG_OUTBOUND', 'OBT');
define('AG_TOLL_PARTNERS', 'TPR');
define('AG_SOA_BASED', 'SOA');
define('AG_DOCUMENT_TRANSMITTAL', 'DTR');

/*
* ------------------------------
 * SYSTEM SETTINGS
 * ------------------------------
 *
 */
define('SYS_SETTING_DISPLAY_LIST_NO', 15);


/*
* ------------------------------
 * TASK ACTION/STATUS
 * ------------------------------
 *
 */
define('TASK_STATUS_PENDING', null);
define('TASK_STATUS_ONGOING', 1);
define('TASK_STATUS_DONE', 2);
define('TASK_STATUS_RETURNED', 3);
define('TASK_STATUS_SKIPPED', 4);
define('TASK_STATUS_DISAPPROVED', 5);
define('TASK_STATUS_CANCELLED', 6);
define('TASK_STATUS_APPROVED', 7);

/*
 * ------------------------------
 * APV STATUS
 * ------------------------------
 *
 */
define('TRANS_TO_BGC', '1');
define('REC_BY_BGC', '2');
define('TRANS_TO_BC', '3');
define('REC_BY_BC', '4');
define('RET_BY_BGC', '5');
define('REL_TO_VENDOR', '6');
define('NOT_REC_BY_BC', '7');

/*
 * ------------------------------
 * DR Types
 * ------------------------------
 *
 */
define('DR_MEDVAC', 'MEDVAC');
define('DR_DOCDR', 'DOCDR');
define('DR_DOCGR', 'DOCGR');
define('DR_CLEANUP', 'CLEANUP');
define('DR_PO', 'PO');
define('DR_HARVEST', 'HARVEST');

/*
 * ------------------------------
 * STANDARD FORMATS
 * ------------------------------
 *
 */
define('FORMAT_DB_DATE', 'Y-m-d');
define('FORMAT_DB_DATE_YDM', 'Y-d-m');
define('FORMAT_DB_DATETIME', 'Y-m-d H:i:s');

define('FORMAT_DATE', 'M d, Y');
define('FORMAT_DATETIME', 'M d, Y h:i A');

//If date format ni mysql
define('FORMAT_DATE_DISPLAY_DB_SPRINTF', '%%b %%d, %%Y');
define('FORMAT_DATE_DISPLAY_DB', '%b %d, %Y');

define('FORMAT_DATEPICKER_DATE', 'm/d/Y');

define('FORMAT_DATETIME_FORMAT', 'F j, Y g:i a');


/*
 * ------------------------------
 * TRANSACTION DIRECTORY
 * ------------------------------
 *
 */
define('FOLDER_INTERNAL_ORDER', 'io');
define('FOLDER_SOA', 'soa');
define('FOLDER_DELIVERY_GOODS', 'dr');
define('FOLDER_PURCHASE_REQUESTS', 'pr');
define('FOLDER_PURCHASE_ORDERS', 'po');
define('FOLDER_FILES', 'files');
define('FOLDER_SITE_NOMINATIONS', 'sites');
define('FOLDER_BOQ', 'boq');
define('FOLDER_PROJECTS', 'projects');
define('FOLDER_CONTRACTS', 'contracts');
define('FOLDER_RENEWAL', 'renewal');
define('FOLDER_TRANSMITTAL', 'doc_transmittal');

/*
 * ------------------------------
 * CORE TASK IDS
 * ------------------------------
 *
 */
define('CORE_TASK_MED_VAC', 1);
define('CORE_TASK_DOC_DR', 2);
define('CORE_TASK_DOC_GR', 3);
define('CORE_TASK_CLEANUP', 4);
define('CORE_TASK_APPROVE_CLEANUP', 5);
define('CORE_TASK_HARVEST_REPORT', 6);
define('CORE_TASK_HARVEST_REPORT_APPROVAL', 7);
define('CORE_TASK_LIVE_SALES_REPORT', 8);
define('CORE_TASK_FHR', 9);
define('CORE_TASK_FHR_APPROVE', 66);
define('CORE_TASK_FHR_AUDIT_SCORE', 10);
define('CORE_TASK_FHR_FINAL_APPROVAL', 11);
define('CORE_TASK_PO_DR_BAVI_MARINADES', 56);
define('CORE_TASK_PO_DR_BFFI', 52);
define('CORE_TASK_PO_GR_BAVI_MARINADES', 58);
define('CORE_TASK_PO_TRANSMIT_BFFI', 55);
define('CORE_TASK_PO_GR_BFFI', 54);
define('CORE_TASK_PO_DR_BAVI', 56);
define('CORE_TASK_SOA_ACCEPT_CALAMBA', 20);
define('CORE_TASK_SOA_UPLOAD', 59);
define('CORE_TASK_SOA_APPROVE', 60);
define('CORE_TASK_SOA_UPLOAD_TRUCKERS_CENTRALIZED', 12);
define('CORE_TASK_SOA_UPLOAD_TRUCKERS_NORMAL', 14);
define('CORE_TASK_SOA_UPLOAD_TRUCKERS_FEEDMILL', 16);
define('CORE_TASK_SOA_UPLOAD_TRUCKERS_MANPOWER', 73);
define('CORE_TASK_SOA_UPLOAD_SOA_BASED', 76);
define('CORE_TASK_SOA_ACCEPT_TRUCKERS_CENTRALIZED', 13);
define('CORE_TASK_SOA_ACCEPT_TRUCKERS_NORMAL', 15);
define('CORE_TASK_SOA_ACCEPT_TRUCKERS_FEEDMILL', 17);
define('CORE_TASK_SOA_ACCEPT_TRUCKERS_MANPOWER', 74);
define('CORE_TASK_SOA_ACCEPT_SOA_BASED', 77);
define('CORE_TASK_CONTRACTS_UPLOAD', 22);
define('CORE_TASK_SOA_UPLOAD_FORWARDERS', 18);
define('CORE_TASK_SOA_TRANSMIT_CALAMBA', 19);
define('CORE_TASK_PR_UPLOAD_CONTRACTORS', 81);
define('CORE_TASK_PR_UPLOAD_BAVI', 64);
define('CORE_TASK_PR_UPLOAD_BFFI_MARINADES', 65);
define('CORE_TASK_PO_UPLOAD_APPROVAL', 48);
define('CORE_TASK_PO_UPLOAD_WO_APPROVAL', 51);
define('CORE_TASK_SITES_RECOM_SITE_NOMINATION', 30);
define('CORE_TASK_SITES_RECOM_SITE_REGIONAL', 31);
define('CORE_TASK_SITES_RECOM_SITE_PRES', 32);
define('CORE_TASK_PO_UPLOAD_APPROVED', 49);
define('CORE_TASK_PO_DR_APPROVE_BAVI_MARINADES', 57);
define('CORE_TASK_PO_DR_APPROVE_BFFI', 53);
define('CORE_TASK_PO_RELEASED', 50);
define('CORE_TASK_RENEWED_CONTRACT', 26);
define('CORE_TASK_PR_UPLOAD_BAVI_APPROVAL', 86);
define('CORE_TASK_PR_UPLOAD_CON_APPROVAL', 82);

define('CORE_TASK_UPLOAD_SITE_NOMINATION', 27);
define('CORE_TASK_STORE_OPENING', 28);
define('CORE_TASK_UPLOAD_IVIEW_MAP', 29);
define('CORE_TASK_ENCODE_PROFIT_COST', 34);
define('CORE_TASK_UPLOAD_BOQ', 35);
define('CORE_TASK_BOQ_REGIONAL_HEAD_APPROVED', 36);
define('CORE_TASK_BOQ_MCS_ENGINEERING', 37);
define('CORE_TASK_BOQ_INDICATE', 72);
define('CORE_TASK_BOQ_INDICATE_CONFIRM_CONTRACT', 67);
define('CORE_TASK_PROJECT_PROJECT_PLAN', 68);
define('CORE_TASK_PROJECT_TURNOVER_DATE', 69);
define('CORE_TASK_ENCODE_ASSET_CODE', 42);
define('CORE_TASK_ENCODE_BOQ_PROGRESS', 43);
define('CORE_TASK_OPENING_DATE', 45);
define('CORE_TASK_PROJECT_COMPLETION_FILE', 46);
define('CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION', 47);
define('CORE_TASK_DISAPPROVE_APPROVE_SITE_NOMINATION', 33);
define('CORE_TASK_BH_APPROVE_CONTRACT_RENEWAL', 23);
define('CORE_TASK_RH_APPROVE_CONTRACT_RENEWAL', 24);
define('CORE_TASK_PRES_APPROVE_CONTRACT_RENEWAL', 25);

define('CORE_TASK_RETURN_DELIVERY_RECEIPT', 53);
define('CORE_TASK_ENCODE_GOOD_RECEIPT', 54);

define('CORE_TASK_BOQ_MCS_APPROVED', 38);
define('CORE_TASK_BOQ_PRES_APPROVED', 39);
define('CORE_TASK_PROJ_BOQ_PROGRESS_APPROVED', 44);
define('CORE_TASK_PROJ_COMPLETION_APPROVED', 70);
define('CORE_TASK_PROJ_PO_UPLOAD_WO_APPROVAL', 75);
define('CORE_TASK_MED_VAC_APPEND', 61);
define('CORE_TASK_DOC_DR_APPEND', 62);
define('CORE_TASK_DOC_GR_APPEND', 63);

define('CORE_TASK_BOQ_STORE_LAYOUT_PLAN', 78);
define('CORE_TASK_BOQ_STORE_MOCKUP_DESIGN', 79);
define('CORE_TASK_BOQ_APPROVAL_STORE_MOCKUP_DESIGN', 80);
define('CORE_TASK_PROJECT_DISAPPROVE_APPROVE_PROJCT_COMPLETION_APPEND', 84);
define('CORE_TASK_PROJ_COMPLETION_APPROVED_APPEND', 85);


/*
 * ------------------------------
 * CORE STAGE IDS
 * ------------------------------
 *
 */
define('CORE_WORKFLOW_STAGE_MEDVAC', 1);
define('CORE_WORKFLOW_STAGE_DOC_DR', 2);
define('CORE_WORKFLOW_STAGE_CLEANUP', 3);
define('CORE_WORKFLOW_STAGE_HARVEST', 4);
define('CORE_WORKFLOW_STAGE_LIVESALES', 5);
define('CORE_WORKFLOW_STAGE_SITE_NOMINATION_APPROVAL', 15);
define('CORE_WORKFLOW_STAGE_DR_BFFI', 27);
define('CORE_WORKFLOW_STAGE_DR', 28);
define('CORE_WORKFLOW_STAGE_MEDVAC_APPEND', 30);
define('CORE_WORKFLOW_STAGE_DOC_DR_APPEND', 31);
define('CORE_WORKFLOW_STAGE_SOA_FWD', 10);


/*
 * ------------------------------
 * CORE WORKFLOW IDS
 * ------------------------------
 *
 */
define('CORE_WORKFLOW_INTERNAL_ORDER', 1);
define('CORE_WORKFLOW_TRUCKER_CENTRAL', 2);
define('CORE_WORKFLOW_TRUCKER_NORMAL', 3);
define('CORE_WORKFLOW_FEEDMILL', 4);
define('CORE_WORKFLOW_FORWARDERS', 5);
define('CORE_WORKFLOW_LESSORS', 6);
define('CORE_WORKFLOW_CONTRACTOR_SITE_NOMINATION', 7);
define('CORE_WORKFLOW_CONTRACTOR_BOQ', 8);
define('CORE_WORKFLOW_CONTRACTOR_PROJECT', 9);
define('CORE_WORKFLOW_PURCHASE_REQUEST_BAVI', 10);
define('CORE_WORKFLOW_PURCHASE_REQUEST_BFFI_MARINADES', 11);
define('CORE_WORKFLOW_PURCHASE_ORDER_W_APPROVAL', 12);
define('CORE_WORKFLOW_PURCHASE_ORDER_WO_APPROVAL', 13);
define('CORE_WORKFLOW_DELIVERY_W_TRANSMITTAL', 14);
define('CORE_WORKFLOW_DELIVERY_WO_TRANSMITTAL', 15);
define('CORE_WORKFLOW_GOODS_SOA', 16);
define('CORE_WORKFLOW_MEDVAC_DR', 17);
define('CORE_WORKFLOW_DOC_DR', 18);
define('CORE_WORKFLOW_MANPOWER', 19);
define('CORE_WORKFLOW_CONTRACTOR_PO', 20);


/*
 * ------------------------------
 * OVERVIEW TYPES
 * ------------------------------
 *
 */
define('OVERVIEW_TYPE_CHANGE_TASK_STATUS', 'CHANGE_STATUS');
define('OVERVIEW_TYPE_ADD_TASK_COMMENT', 'ADD_COMMENT');
define('OVERVIEW_TYPE_EDIT_TASK_COMMENT', 'EDIT_COMMENT');
define('OVERVIEW_TYPE_ADD_TASK_ATTACHMENT', 'ADD_TASK_ATTACHMENT');
define('OVERVIEW_TYPE_EDIT_TASK_ATTACHMENT', 'EDIT_TASK_ATTACHMENT');
define('OVERVIEW_TYPE_ADD_QUICK_ADD', 'ADD_QUICK_ADD');
define('OVERVIEW_TYPE_ADD_TRANSACTION', 'ADD_TRANSACTION');
define('OVERVIEW_TYPE_UPLOAD_BATCH_FILES', 'UPLOAD_BATCH_FILES');



/*
 * ------------------------------
 * CONTRACT STATUS
 * ------------------------------
 *
 */
define('CONTRACT_NEW', 'NEW');
define('CONTRACT_EXPIRED', 'EXPIRED');
define('CONTRACT_FOR_RENEWAL', 'FOR_RENEWAL');
define('CONTRACT_RENEWED', 'RENEWED');
define('CONTRACT_OVERDUE', 'OVERDUE');
define('CONTRACT_DUE_RENEWAL', 'DUE_RENEWAL');

/*
 * ------------------------------
 * SITE CODE LENGTH
 * ------------------------------
 *
 */
define('SITE_CODE_LENGTH', 8);

/*
 * ------------------------------
 * BOQ CODE LENGTH
 * ------------------------------
 *
 */
define('BOQ_CODE_LENGTH', 8);

/*
 * ------------------------------
 * BOQ CODE LENGTH
 * ------------------------------
 *
 */
define('PROJ_CODE_LENGTH', 8);

/*
 * ------------------------------
 * WORKFLOW FOR VALUES
 * ------------------------------
 *
 */
define('WORKFLOW_FOR_VENDOR', 'VENDOR');
define('WORKFLOW_FOR_BAVI', 'BAVI');


/*
 * ------------------------------
 * UPLOAD CONFIG
 * ------------------------------
 *
 */
define('PRIA_UPLOAD_MAX_FILE_SIZE', '13107200');
define('PRIA_TASK_ALLOWED_FILES', 'pdf,jpg,jpeg,png,xls,xlsx');
define('PRIA_TASK_ATTACHMENT', 'pdf,jpeg,jpg,png,xls,xlsx,doc,docx,zip');

/*
 * ------------------------------
 * DOCUMENT TYPES
 * ------------------------------
 *
 */
define('DOC_TYPE_DOC_DR', 'DOC_DR');
define('DOC_TYPE_FHR', 'DOC_FHR');
define('DOC_TYPE_HARVEST', 'DOC_HARVEST');
define('DOC_TYPE_LIVESALES', 'DOC_LIVESALES');
define('DOC_TYPE_PO', 'DOC_PO');
define('DOC_TYPE_SITE_FORM', 'DOC_SITE_FORM');
define('DOC_TYPE_SOA', 'DOC_SOA');
define('DOC_TYPE_TRANSMITTAL', 'DOC_TRANSMITTAL');
define('DOC_TYPE_IVIEW_MAP', 'DOC_IVIEW_MAP');
define('DOC_TYPE_BOQ', 'DOC_BOQ');
define('DOC_TYPE_RFA', 'DOC_RFA');
define('DOC_TYPE_PR', 'DOC_PR');
define('DOC_TYPE_GANTT', 'DOC_GANTT');
define('DOC_TYPE_BOQ_PROGRESS', 'DOC_BOQ_PROGRESS');
define('DOC_TYPE_AS_BUILT', 'DOC_AS_BUILT');
define('DOC_TYPE_BOQ_COMPLETION', 'DOC_BOQ_COMPLETION');
define('DOC_TYPE_SIGNED_COC', 'DOC_SIGNED_COC');
define('DOC_TYPE_RENEWAL', 'DOC_RENEWAL');
define('DOC_TYPE_CONTRACT', 'DOC_CONTRACT');
define('DOC_TYPE_SIGNED_RENEWAL', 'DOC_SIGNED_RENEWAL');
define('DOC_TYPE_LAYOUT_PLAN', 'DOC_LAYOUT_PLAN');
define('DOC_TYPE_SUPP_PICS', 'DOC_SUPP_PICS');
define('DOC_TYPE_MOCKUP_DESIGN', 'DOC_MOCKUP_DESIGN');

define('DOC_TYPE_TASK_ATTACHMENT', 'DOC_TASK_ATTACHMENT');

define('DOC_TYPE_DOCUMENT_TRANSMITTAL', 'DOC_TRANSMITTAL');

/*
 * ------------------------------
 * ORGANIZATION TYPES
 * ------------------------------
 *
 */
define('ORG_TYPE_COST_CENTER', 'CC');
define('ORG_TYPE_BUSINESS_CENTER', 'BC');
define('ORG_TYPE_REGION', 'REG');
define('ORG_TYPE_ORGANIZATION', 'ORG');



/*
 * ------------------------------
 * TASK DOCUMENT ACCESS
 * ------------------------------
 *
 */
define('DOCUMENT_ACCESS_ADD', 'ADD');
define('DOCUMENT_ACCESS_VIEW', 'VIEW');



/*
 * ------------------------------
 * PROJECT STATUS CODE
 * ------------------------------
 *
 */
define('PROJECT_STATUS_COMPLETED', 'COMPLETED');
define('PROJECT_STATUS_ONGOING', 'ONGOING');

/*
 * ------------------------------
 * SITE STATUS CODE
 * ------------------------------
 *
 */
define('SITE_STATUS_COMPLETED', 'COMPLETED');
define('SITE_STATUS_ONGOING', 'ONGOING');
define('SITE_STATUS_DELETED', 'DELETED');


/*
 * ------------------------------
 * ORGANIZATION TYPES
 * ------------------------------
 *
 */
define('DECIMAL_PLACES', 2);


/*
 * ------------------------------
 * APV REFERENCE TYPE CODE
 * ------------------------------
 *
*/
define('APV_REF_TYPE_CODE_IO', 'IO');
define('APV_REF_TYPE_CODE_PO', 'PO');
define('APV_REF_TYPE_CODE_SOA', 'SOA');
define('APV_REF_TYPE_CODE_CONTRACTS', 'CONTRACTS');


/*
 * ------------------------------
 * PRIA TASK TYPE
 * ------------------------------
 *
*/
define('TASK_TYPE_ENCODE_FHR', 'ENCODE_FHR');
define('TASK_TYPE_ENCODE_DR', 'ENCODE_DR');
define('TASK_TYPE_RETURN_APPROVE_SOA', 'RETURN_APPROVE_SOA');

define('YES_FLAG', 'Y');
define('NO_FLAG', 'N');

define('STATUS_ACTIVE_FLAG', 'STATUS_ACTIVE');


/*
 * ------------------------------
 * PR Item Types
 * ------------------------------
 *
 */
define('PR_NEW', 'NEW');
define('PR_OLD', 'EXISTING');

/*
 * ------------------------------
 * Root Modules
 * ------------------------------
 *
 */
define('ROOT_QA', 'PORTAL_QUICK_ADD');
define('ROOT_TRANSAC', 'PORTAL_TRANSACTIONS');


/*
 * ------------------------------
 * BOQ CATEGORIES
 * ------------------------------
 *
 */
define('BOQ_CATEGORY_NEW', 'NEW');
define('BOQ_CATEGORY_REHABILITATION', 'REHABILITATION');
define('BOQ_CATEGORY_RENOVATION', 'RENOVATION');
define('BOQ_CATEGORY_REPAIR', 'REPAIR');
define('BOQ_CATEGORY_SIGNAGE', 'SIGNAGE');


/*
 * ------------------------------
 * BOQ STATUS
 * ------------------------------
 *
 */
define('BOQ_STATUS_ONGOING', 'ONGOING');
define('BOQ_STATUS_COMPLETED', 'COMPLETED');




/*
 * ------------------------------
 * TASK UPDATE TYPES
 * ------------------------------
 *
 */
define('TASK_UPDATE_COMPLETE', 'COMPLETE');
define('TASK_UPDATE_RETURN', 'RETURN');
define('TASK_UPDATE_APPROVE', 'APPROVE');


/*
 * ------------------------------
 * Notifications Trigger
 * ------------------------------
 */
define('NOTIF_TRIGGER_ACTION', 'ACTION');
define('NOTIF_TRIGGER_SCHEDULE', 'SCHEDULE');


/*
 * ------------------------------
 * Email Notifications Type
 * ------------------------------
 */
define('EMAIL_NOTIF_TYPE_UPLOAD_LIST', 'UPLOAD_LIST');
define('EMAIL_NOTIF_TYPE_UPLOAD_FILE', 'UPLOAD_FILE');
define('EMAIL_NOTIF_TYPE_FOR_APPROVAL_SIMPLE', 'FOR_APPROVAL_SIMPLE');
define('EMAIL_NOTIF_TYPE_FOR_APPROVAL_REVIEW_DETAILED', 'FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_TYPE_FOR_APPROVAL_W_ACTION', 'FOR_APPROVAL_W_ACTION');
define('EMAIL_NOTIF_TYPE_FOR_APPROVAL_FINAL_W_ACTION', 'FOR_APPROVAL_FINAL_W_ACTION');
define('EMAIL_NOTIF_TYPE_FINAL_APPROVED_DETAILED', 'FINAL_APPROVED_DETAILED');
define('EMAIL_NOTIF_TYPE_FOR_ACCEPTANCE_W_ACTION', 'FOR_ACCEPTANCE_W_ACTION');
define('EMAIL_NOTIF_TYPE_FOR_ACCEPTANCE_W_ACTION_TAT', 'FOR_ACCEPTANCE_W_ACTION_TAT');
define('EMAIL_NOTIF_TYPE_TASK_COMPLETED', 'TASK_COMPLETED');
define('EMAIL_NOTIF_TYPE_TASK_RETURNED', 'TASK_RETURNED');
define('EMAIL_NOTIF_TYPE_TASK_COMPLETED_NEW', 'TASK_COMPLETED_NEW');
define('EMAIL_NOTIF_TYPE_TRANSACTION_W_ACTION', 'TRANSACTION_W_ACTION');
define('EMAIL_NOTIF_TYPE_TRANSACTION_IS_ADDTL_MSG', 'TRANSACTION_IS_ADDTL_MSG');

define('EMAIL_NOTIF_TYPE_DETAILS_TASK_COMPLETED', 'DETAILS_TASK_COMPLETED');
define('EMAIL_NOTIF_TYPE_DETAILS_TASK_COMPLETED_NEW', 'DETAILS_TASK_COMPLETED_NEW');
define('EMAIL_NOTIF_TYPE_DETAILS_IO_DEFAULT', 'DETAILS_IO_DEFAULT');
define('EMAIL_NOTIF_TYPE_DETAILS_IO_COMPLETED', 'DETAILS_IO_COMPLETED');
define('EMAIL_NOTIF_TYPE_DETAILS_PR', 'DETAILS_PR');
define('EMAIL_NOTIF_TYPE_DETAILS_CONTRACT', 'DETAILS_CONTRACT');
define('EMAIL_NOTIF_TYPE_DETAILS_PO_DEFAULT', 'DETAILS_PO_DEFAULT');
define('EMAIL_NOTIF_TYPE_DETAILS_PO_W_MSG', 'DETAILS_PO_W_MSG');
define('EMAIL_NOTIF_TYPE_DETAILS_SRR', 'DETAILS_SRR');
define('EMAIL_NOTIF_TYPE_DETAILS_SRR_W_MSG', 'DETAILS_SRR_W_MSG');
define('EMAIL_NOTIF_TYPE_DETAILS_SN', 'DETAILS_SN');
define('EMAIL_NOTIF_TYPE_DETAILS_SOA', 'DETAILS_SOA');
define('EMAIL_NOTIF_TYPE_DETAILS_PO_RELEASED', 'DETAILS_PO_RELEASED');
define('EMAIL_NOTIF_TYPE_DETAILS_PO', 'DETAILS_PO');

define('EMAIL_NOTIF_TYPE_DETAILS_SOA_CENTRAL', 'DETAILS_SOA_CENTRAL');

define('EMAIL_NOTIF_TYPE_DETAILS_SN_APPROVAL', 'DETAILS_SN_APPROVAL');
define('EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL', 'DETAILS_BOQ_APPROVAL');
define('EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_BUDGETED', 'DETAILS_BOQ_APPROVAL_BUDGETED');
define('EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_W_FINAL_AMOUNT', 'DETAILS_BOQ_APPROVAL_W_FINAL_AMOUNT');
define('EMAIL_NOTIF_TYPE_DETAILS_BOQ_APPROVAL_AWARDED', 'DETAILS_BOQ_APPROVAL_AWARDED');

define('EMAIL_NOTIF_TYPE_APV', 'DETAILS_APV');

define('EMAIL_NOTIF_TYPE_DTR', 'DETAILS_DTR'); // -?
/*
 * ------------------------------
 * Email Notifications Sub Type
 * ------------------------------
 */
define('EMAIL_NOTIF_SUB_IO_UPLOAD_LIST', 'IO_UPLOAD_LIST');
define('EMAIL_NOTIF_SUB_IO_TASK_COMPLETED', 'IO_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_PR_TASK_COMPLETED', 'PR_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_PO_TASK_COMPLETED', 'PO_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_PO_RELEASED', 'PO_RELEASED');
define('EMAIL_NOTIF_SUB_DR_TASK_COMPLETED', 'DR_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_GR_TASK_COMPLETED', 'GR_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_SOA_TASK_COMPLETED', 'SOA_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_SN_TASK_COMPLETED', 'SN_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED', 'BOQ_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_BOQ_TASK_COMPLETED_W_FINAL_AMOUNT', 'BOQ_TASK_COMPLETED_W_FINAL_AMOUNT');
define('EMAIL_NOTIF_SUB_PROJ_TASK_COMPLETED', 'PROJ_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_SRR_TASK_COMPLETED', 'SRR_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_PROJ_PO_TASK_COMPLETED', 'PROJ_PO_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_DR_CANCELLATION_TRANSACTION_W_ACTION', 'DR_CANCELLATION_TRANSACTION_W_ACTION');
define('EMAIL_NOTIF_SUB_CONTRACT_ADD_TRANSACTION_W_ACTION', 'CONTRACT_ADD_TRANSACTION_W_ACTION');
define('EMAIL_NOTIF_SUB_CONTRACT_TRANSACTION_IS_ADDTL_MSG', 'CONTRACT_TRANSACTION_IS_ADDTL_MSG');

define('EMAIL_NOTIF_SUB_PR_UPLOAD_FILE', 'PR_UPLOAD_FILE');
define('EMAIL_NOTIF_SUB_SN_UPLOAD_FILE', 'SN_UPLOAD_FILE');

define('EMAIL_NOTIF_SUB_PO_FOR_APPROVAL_SIMPLE', 'PO_FOR_APPROVAL_SIMPLE');

define('EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_REVIEW_DETAILED', 'SRR_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_REVIEW_DETAILED', 'SN_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_SOA_FOR_APPROVAL_REVIEW_DETAILED', 'SOA_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_PO_FOR_APPROVAL_REVIEW_DETAILED', 'PO_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_SN_TASK_SIMPLE', 'SN_TASK_SIMPLE');

define('EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_REVIEW_DETAILED', 'BOQ_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_BOQ_W_FINAL_AMOUNT', 'BOQ_W_FINAL_AMOUNT');

define('EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_W_ACTION', 'SRR_FOR_APPROVAL_W_ACTION');
define('EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_W_ACTION', 'SN_FOR_APPROVAL_W_ACTION');
define('EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_W_ACTION', 'BOQ_FOR_APPROVAL_W_ACTION');

define('EMAIL_NOTIF_SUB_SN_FOR_APPROVAL_FINAL_W_ACTION', 'SN_FOR_APPROVAL_FINAL_W_ACTION');
define('EMAIL_NOTIF_SUB_BOQ_FOR_APPROVAL_FINAL_W_ACTION', 'BOQ_FOR_APPROVAL_FINAL_W_ACTION');

define('EMAIL_NOTIF_SUB_SRR_FOR_APPROVAL_FINAL_W_ACTION', 'SRR_FOR_APPROVAL_FINAL_W_ACTION');

define('EMAIL_NOTIF_SUB_SRR_FINAL_APPROVED_DETAILED', 'SRR_FINAL_APPROVED_DETAILED');
define('EMAIL_NOTIF_SUB_SN_FINAL_APPROVED_DETAILED', 'SN_FINAL_APPROVED_DETAILED');

define('EMAIL_NOTIF_SUB_APV_APPROVAL', 'APV_DETAILED');

define('EMAIL_NOTIF_DAILY_FHR', 'IO_TASK_DAILY_FHR');
define('EMAIL_NOTIF_PO_RELEASED', 'RELEASED_PO');

define('EMAIL_NOTIF_SUB_DTR_TASK_COMPLETED', 'DTR_TASK_COMPLETED');
define('EMAIL_NOTIF_SUB_DTR_TASK_RETURNED', 'DTR_TASK_RETURNED');
define('EMAIL_NOTIF_SUB_DTR_FOR_APPROVAL_REVIEW_DETAILED', 'DTR_FOR_APPROVAL_REVIEW_DETAILED');
define('EMAIL_NOTIF_SUB_DTR_FOR_APPROVAL_REVIEW_DETAILED_RESUBMIT', 'DTR_FOR_APPROVAL_REVIEW_DETAILED_RESUBMIT');

//PO_TASK_COMPLETED
//PO_TASK_RETURNED
//PO_TASK_RELEASED
//DR_TASK_RETURNED

/*
 * ------------------------------
 * Email Notifications Details Extend
 * ------------------------------
 */
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_IO', 'DETAILS_EXTEND_TASK_COMPLETED_IO');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SOA', 'DETAILS_EXTEND_TASK_COMPLETED_SOA');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_INITIAL', 'DETAILS_EXTEND_TASK_COMPLETED_SN_INITIAL');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL', 'DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL_ONLY', 'DETAILS_EXTEND_TASK_COMPLETED_SN_OFFICIAL_ONLY');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_RECOMMENDED', 'DETAILS_EXTEND_TASK_COMPLETED_BOQ_RECOMMENDED');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_BUDGETED', 'DETAILS_EXTEND_TASK_COMPLETED_BOQ_BUDGETED');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_FINAL_AMOUNT', 'DETAILS_EXTEND_TASK_COMPLETED_BOQ_FINAL_AMOUNT');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_PO', 'DETAILS_EXTEND_TASK_COMPLETED_PROJ_PO');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO', 'DETAILS_EXTEND_TASK_COMPLETED_PO');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_RELEASED', 'DETAILS_EXTEND_TASK_COMPLETED_PO_RELEASED');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_DR', 'DETAILS_EXTEND_TASK_COMPLETED_PO_DR');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PO_GR', 'DETAILS_EXTEND_TASK_COMPLETED_PO_GR');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_BOQ_MOCK_UP', 'DETAILS_EXTEND_TASK_COMPLETED_BOQ_MOCK_UP');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_SN_CODES', 'DETAILS_EXTEND_TASK_COMPLETED_SN_CODES');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_MOBILIZATION', 'DETAILS_EXTEND_TASK_COMPLETED_PROJ_MOBILIZATION');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_TURNOVER', 'DETAILS_EXTEND_TASK_COMPLETED_PROJ_TURNOVER');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PROJ_OPENING', 'DETAILS_EXTEND_TASK_COMPLETED_PROJ_OPENING');
define('EMAIL_NOTIF_DETAILS_EXTEND_TASK_COMPLETED_PR_CON', 'DETAILS_EXTEND_TASK_COMPLETED_PR_CON');


/*
 * ------------------------------
 * Email Notifications Details Multi Usage Templates
 * ------------------------------
 */
define('EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK', 13);
define('EMAIL_TEMPLATE_DETAILS_EXTEND_TASK_COMPLETED_NEXT_TASK_DETAILS', 14);



/*
 * ------------------------------
 * Email Notifications Schedule
 * ------------------------------
 */
define('EMAIL_NOTIF_IMMEDIATE', 'IMMEDIATE');
define('EMAIL_NOTIF_DAILY', 'DAILY');
define('EMAIL_NOTIF_WEEKLY', 'WEEKLY');
define('EMAIL_NOTIF_MONTHLY', 'MONTHLY');
define('EMAIL_NOTIF_YEARLY', 'YEARLY');
define('EMAIL_NOTIF_TAT', 'TAT');
define('EMAIL_NOTIF_WI_TAT', 'WI_TAT');
define('EMAIL_NOTIF_ON_TAT', 'ON_TAT');
define('EMAIL_NOTIF_AFTER_TAT', 'AFTER_TAT');

/*
 * ------------------------------
 * Email Notifications Recipient
 * ------------------------------
 */
define('EMAIL_RECIPIENT_SPECIFIC_USER', 'SPECIFIC_USER');
define('EMAIL_RECIPIENT_SPECIFIC_ROLE', 'SPECIFIC_ROLE');
define('EMAIL_RECIPIENT_SPECIFIC_OFFICE', 'SPECIFIC_OFFICE');
define('EMAIL_RECIPIENT_ROLE', 'ROLE');
define('EMAIL_RECIPIENT_MAIN_ROLE', 'MAIN_ROLE');
define('EMAIL_RECIPIENT_REF_ROLE', 'REF_ROLE');
define('EMAIL_RECIPIENT_NEXT_ROLE', 'NEXT_ROLE');
define('EMAIL_RECIPIENT_NEXT_MAIN_ROLE', 'NEXT_MAIN_ROLE');
define('EMAIL_RECIPIENT_NEXT_REF_ROLE', 'NEXT_REF_ROLE');
define('EMAIL_RECIPIENT_PREV_ROLE', 'PREV_ROLE');
define('EMAIL_RECIPIENT_PREV_MAIN_ROLE', 'PREV_MAIN_ROLE');
define('EMAIL_RECIPIENT_PREV_REF_ROLE', 'PREV_REF_ROLE');
define('EMAIL_RECIPIENT_PRIA_TASK_STAGE', 'PRIA_TASK_STAGE');


/*
 * ------------------------------
 * Vendor Types
 * ------------------------------
 */
define('VT_VENDOR', 'VENDOR');
define('VT_LESSOR', 'LESSOR');

/*
 |---------------------------------------------------------------------
 | DELETED FLAGS
 |---------------------------------------------------------------------
 | Declare all the initial flags in your project.
 */
define( 'DEL_FLAG', 1);
define( 'NOT_DEL_FLAG', 0);

/*
 |---------------------------------------------------------------------
 | RENEWAL FLAGS
 |---------------------------------------------------------------------
 | Declare all the initial flags in your project.
 */
define( 'RENEWED_FLAG', 1);
define( 'NOT_RENEWED_FLAG', 0);


/*
 |---------------------------------------------------------------------
 | TASK ACTIONS
 |---------------------------------------------------------------------
 */
define('TASK_ACTION_APPROVED', 'APPROVED');
define('TASK_ACTION_RETURNED', 'RETURNED');
define('TASK_ACTION_DISAPPROVED', 'DISAPPROVED');
define('TASK_ACTION_RESUBMITTED', 'RESUBMITTED');


/*
 |---------------------------------------------------------------------
 | MAIL ACTION TYPES
 |---------------------------------------------------------------------
 */
define('MAIL_ACTION_TYPE_RETURN_ACCEPT', 'return_accept');


/*
 |---------------------------------------------------------------------
 | TASK ROLES
 |---------------------------------------------------------------------
 */
define('TASK_ROLE_VENDOR', 'VENDOR');

/*
 * ------------------------------
 * CONTRACTOR PROCESS TYPES
 * ------------------------------
 *
 */
define('CONTRACTOR_PROCESS_SITE', 'SITE_NOMINATION');
define('CONTRACTOR_PROCESS_BOQ', 'BOQ');
define('CONTRACTOR_PROCESS_PROJECT', 'PROJECTS');

/*
 * ------------------------------
 * PARAM STATUSES
 * ------------------------------
 *
 */
define('STATUS_ONGOING', 'ONGOING');
define('STATUS_COMPLETED', 'COMPLETED');
define('STATUS_CANCELLED', 'CANCELLED');

define('PARAM_STATUS_ONGOING', 'ONGOING');
define('PARAM_STATUS_COMPLETED', 'COMPLETED');
define('PARAM_STATUS_CANCELLED', 'CANCELLED');
define('PARAM_STATUS_DISAPPROVED', 'DISAPPROVED');


/*
 * ------------------------------
 * CONTRACTOR PROCESS CATEGORIES
 * ------------------------------
 *
 */
define('CON_PROCESS_CATEGORY_NEW', 'NEW');
define('CON_PROCESS_CATEGORY_REHABILITATION', 'REHABILITATION');
define('CON_PROCESS_CATEGORY_RENOVATION', 'RENOVATION');
define('CON_PROCESS_CATEGORY_REPAIR', 'REPAIR');
define('CON_PROCESS_CATEGORY_SIGNAGE', 'SIGNAGE');
define('CON_PROCESS_CATEGORY_SIGNAGE_REPAIR', 'SIGNAGE_REPAIR');
define('CON_PROCESS_CATEGORY_CONVERSOIN', 'CONVERSION');


/*
 * ------------------------------
 * SITE TYPES
 * ------------------------------
 *
 */
define('SITE_TYPE_DRESSING_PLANT', 'DP');
define('SITE_TYPE_FARM', 'FRM');
define('SITE_TYPE_STORE', 'STR');
define('SITE_TYPE_WAREHOUSE', 'WRH');
define('SITE_TYPE_COST_CENTER', 'CC');
define('SITE_TYPE_OFFICE', 'OFC');


/*
 * ------------------------------
 * Document Types
 * ------------------------------
 *
 */
define('DOC_MEDVAC', 'DOC_MEDVAC');
define('DOC_DR', 'DOC_DR');
define('DOC_GR', 'DOC_GR');
define('DOC_CLEANUP', 'DOC_CLEANUP');
define('DOC_HARVEST', 'DOC_HARVEST');

/*
 * ------------------------------
 * DR GR Types
 * ------------------------------
 *
 */
define('DR_TYPE', 'DR');
define('GR_TYPE', 'GR');

/*
 * ------------------------------
 * DATE VALUES
 * ------------------------------
 *
 */
define('DV_FRIDAY', '5');


/*
 * ------------------------------
 * ADDITIONAL ERROR TYPE
 * ------------------------------
 *
 */
define('WARNING', 'warning');


/*
 * ------------------------------
 * TRANSACTION TAB
 * ------------------------------
 */
define('TRANS_TAB_BOQ', 'BOQ');
define('TRANS_TAB_CONTRACTS', 'CONTRACTS');
define('TRANS_TAB_DR', 'DR');
define('TRANS_TAB_IO', 'IO');
define('TRANS_TAB_PO', 'PO');
define('TRANS_TAB_PR', 'PR');
define('TRANS_TAB_PROJ', 'PROJ');
define('TRANS_TAB_RENEWALS', 'RENEWALS');
define('TRANS_TAB_SITE_NOM', 'SITE_NOM');
define('TRANS_TAB_SOA', 'SOA');
define('TRANS_TAB_TRANSMITTAL', 'TRANSMITTAL');

/*
 * ------------------------------
 * ROLE FLAG
 * ------------------------------
 */
define('MAIN_ROLE_FLAG', 1);
define('REF_ROLE_FLAG', 0);

/*
 * ------------------------------
 * DELIVERY STATUS
 * ------------------------------
 */
define('DR_FOR_CANCELLATION', 'FOR_CANCELLATION');
define('DR_CANCELLED', 'CANCELLED');
define('DR_APPROVED', 'APPROVED');
define('DR_FOR_SOA', 'FOR_SOA');
define('DR_SOA_APPROVED', 'SOA_APPROVE');

/*
 |---------------------------------------------------------------------
 | MAIL FILTERED TYPE IN SYS PARAM
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_MAIL_FILTERED', 'MAIL_FILTERED');
define('SYS_PARAM_MAIL_FILTERED_VENDOR', 'MAIL_FILTERED_VENDOR');

/*
 |---------------------------------------------------------------------
 | SYS PARAM SOA WEEK PERIOD DAY RANGE
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_SOA_PERIOD_DAY', 'SOA_PERIOD_DAY');
define('SYS_PARAM_SOA_PERIOD_DAY_FROM', 'SOA_PERIOD_DAY_FROM');
define('SYS_PARAM_SOA_PERIOD_DAY_TO', 'SOA_PERIOD_DAY_TO');

/*
 |---------------------------------------------------------------------
 | SYS PARAM SOA WEEK PERIOD DAY RANGE
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_TYPE_CONTRACT_STATUS', 'CONTRACT_STATUS_MONTH');
define('SYS_PARAM_CONTRACT_DUE_RENEWAL', 'CONTRACT_DUE_RENEWAL');
define('SYS_PARAM_CONTRACT_OVERDUE', 'CONTRACT_OVERDUE');

/*
 |---------------------------------------------------------------------
 | SOA WEEK PERIOD DAY RANGE DEFAULT
 |---------------------------------------------------------------------
 */
define('DEFAULT_SOA_PERIOD_DAY_FROM', 'Sunday');
define('DEFAULT_SOA_PERIOD_DAY_TO', 'Saturday');

/*
 |---------------------------------------------------------------------
 | SOA WEEK PERIOD DAY RANGE DEFAULT
 |---------------------------------------------------------------------
 */
define('IO_MIN_CYCLE_NUM', 1);
define('IO_MAX_CYCLE_NUM', 15);



/*
 |---------------------------------------------------------------------
 | PARSLEY
 |---------------------------------------------------------------------
 */
define('PARSLEY_AMOUNT_REGEX', '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:(\.|,)\d+)?$/');


/*
 |---------------------------------------------------------------------
 | EMAIL FOOTER
 |---------------------------------------------------------------------
 */
define('EMAIL_FOOTER_PRIA', 'FOOTER_PRIA');
define('EMAIL_FOOTER_TASK', 'FOOTER_TASK');
define('EMAIL_FOOTER_APPROVAL', 'FOOTER_APPROVAL');

/*
 |---------------------------------------------------------------------
 | Sequence number of tasks
 |---------------------------------------------------------------------
 */
define('SEQUENCE_NO_THREE', 3);

/*
 |---------------------------------------------------------------------
 | Roles in the system
 |---------------------------------------------------------------------
 */
define('ROLE_CSS', 'CSS');
define('ROLE_PROD_FIN_PERS', 'PROD_FIN_PERS');
define('ROLE_BC_ADMIN', 'BC_ADMIN');
define('ROLE_BC_FIN_PERS', 'BC_FIN_PERS');
define('ROLE_FEEDS_FIN_PERS', 'FEEDS_FIN_PERS');
define('ROLE_PAY_FIN_PERS', 'PAY_FIN_PERS');
define('ROLE_BC_HEAD', 'BC_HEAD');
define('ROLE_PURCH_PERS', 'PURCH_PERS');
define('ROLE_PROJ_ENG', 'PROJ_ENG');
define('ROLE_CG_LIQ_FIN_PERS', 'CG_LIQ_FIN_PERS');
define('ROLE_PURCH_HEAD', 'PURCH_HEAD');
define('ROLE_ROH', 'ROH');
define('ROLE_ROTI_ADMIN', 'ROTI_ADMIN');
define('ROLE_DPIM_HEAD','DPIM_HEAD');
define('ROLE_DPIM','DPIM');
define('ROLE_CSS_HO','CSS_HO');
define('ROLE_HO_FINANCE','HO_FINANCE');

define('ROLE_SUPER_ADMIN','SUPER_ADMIN');
define('ROLE_SUP_CARE','SUP_CARE');
define('ROLE_SALES_FINANCE','SALES_FINANCE');
define('ROLE_RECIPIENT','RECIPIENT');
define('ROLE_PRESIDENT','PRESIDENT');
define('ROLE_PPEI','PPEI');
define('ROLE_MDC','MDC');
define('ROLE_MCS_ENG','MCS_ENG');
define('ROLE_LOG_PERS','LOG_PERS');
define('ROLE_ISSC','ISSC');
define('ROLE_FPA','FPA');
define('ROLE_FM_HEAD','FM_HEAD');
define('ROLE_FM_COOR','FM_COOR');
define('ROLE_FCSS','FCSS');
define('ROLE_DP_ASSIST','DP_ASSIST');
define('ROLE_DH','DH');
define('ROLE_CSS_HEAD','CSS_HEAD');
define('ROLE_CG_SUP','CG_SUP');
define('ROLE_BA_UNIT','BA_UNIT');
define('ROLE_LOG_HEAD','LOG_HEAD');
define('ROLE_HO_PAY_FIN','HO_PAY_FIN');
define('ROLE_BC_FIN_HEAD', 'BC_FIN_HEAD');
define('ROLE_ENG_HEAD', 'ENG_HEAD');
define('ROLE_PURCH_PERS_CON', 'PURCH_PERS_CON');
define('ROLE_PAY_FIN_CON', 'PAY_FIN_CON');

/*
 |---------------------------------------------------------------------
 | IMPORT LIST FILE EXTENSION
 |---------------------------------------------------------------------
 */
define('IMPORT_FILE_EXTENSION', 'xls,xlsx');

/*
 |---------------------------------------------------------------------
 | AG DUE DATES
 |---------------------------------------------------------------------
 */
define('REPORT_CG_DUE_DATE', 15);
define('REPORT_GBV_DUE_DATE', 30);
define('REPORT_GBF_DUE_DATE', 30);
define('REPORT_MAR_DUE_DATE', 30);
define('REPORT_TP_DUE_DATE', 30);
define('REPORT_CON_DUE_DATE', 30);
define('REPORT_FOR_DUE_DATE', 30);
define('REPORT_INC_DUE_DATE', 45);
define('REPORT_INN_DUE_DATE', 45);
define('REPORT_OUT_DUE_DATE', 45);
define('REPORT_FEED_DUE_DATE', 30);
define('REPORT_LESS_DUE_DATE', 30);
define('REPORT_MAN_DUE_DATE', 30);
define('REPORT_SOA_DUE_DATE', 30);

/*
 |---------------------------------------------------------------------
 | REPORTS REFRENCE A
 |---------------------------------------------------------------------
 */
define('REF_B_APV', 'APV');
define('REF_B_CV', 'CV');
define('REF_B_DR', 'DR');

/*
 |---------------------------------------------------------------------
 | ROLE FINANCE FLAG
 |---------------------------------------------------------------------
 */
define('FINANCE_YES_FLAG', 1);
define('FINANCE_NO_FLAG', 0);

/*
 |---------------------------------------------------------------------
 | CRON
 |---------------------------------------------------------------------
 */
define('CRON_SOA', 3);
define('CRON_CONTRACT', 5);

//Overdue
define('CRON_OVERDUE_CONTRACT', 1);

define('PAYMENT_EMPTY_INDICATOR', '---');

/*
 |---------------------------------------------------------------------
 | PAYMENT TERMS
 |---------------------------------------------------------------------
 */
define('PAYMENT_TERM_ANNUALLY', 'ANNUALLY');
define('PAYMENT_TERM_MONTHLY', 'MONTHLY');
define('PAYMENT_TERM_QUARTERLY', 'QUARTERLY');
define('PAYMENT_TERM_SEMI_ANNUALLY', 'SEMI_ANNUALLY');


/*
 |---------------------------------------------------------------------
 | REMINDER FOR CONTRACT IN SYS PARAM
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_TYPE_REMINDER', 'REMINDER');
define('SYS_PARAM_CONTRACT_NOTIF_MONTH', 'CONTRACT_NOTIF_MONTH');


/*
 |---------------------------------------------------------------------
 | CONTRACT STATUS
 |---------------------------------------------------------------------
 */
define('CONTRACT_STATUS_NEW', 'NEW');
define('CONTRACT_STATUS_FOR_RENEWAL', 'FOR_RENEWAL');
define('CONTRACT_STATUS_EXPIRED', 'EXPIRED');
define('CONTRACT_STATUS_DUE_RENEWAL', 'DUE_RENEWAL');
define('CONTRACT_STATUS_OVERDUE', 'OVERDUE');
define('CONTRACT_STATUS_RENEWED', 'RENEWED');

/*
 |---------------------------------------------------------------------
 | CRON SETTINGS
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_CRON_SETTINGS', 'CRON_SETTINGS');
define('SYS_PARAM_BASE_URL_CRON', 'BASE_URL_CRON');



define('SELECT_ALL', '_ALL_');

/*
 |---------------------------------------------------------------------
 | DASHBOARD PARAM
 |---------------------------------------------------------------------
 */
define('SYS_PARAM_DASHBOARD_PARAM', 'DASHBOARD_PARAM');
define('SYS_PARAM_DASHBOARD_REMINDER_INTERVAL', 'DASHBOARD_REMINDER_INTERVAL');

/*
 |---------------------------------------------------------------------
 | BOQ NEXT APPROVER
 |---------------------------------------------------------------------
 */
define('NEXT_RECO_APPROVERS', serialize(
    array(
        ROLE_ENG_HEAD   => 'Engineering Head',
        ROLE_BC_HEAD    => 'Business Center Head',
        ROLE_MCS_ENG    => 'MCS Engineer'
    )
));


// CDI STORE RENOVATION TABLES
define('PORTAL_TABLE_CDI_BOMS','cdi_boms');


// CDI STORE RENOVATION CONTROLLER CONSTANT
define('MODULE_PORTAL_TRANS_STORE_RENOVATION','PORTAL_CDI_STORE_RENOVATION');
define('AG_STORE_RENOVATION','CDISTRRNV');
define('FOLDER_BOM_APPROVAL', 'cdi_bom_approval');

// CDI CDI BOM APPROVALS CONTROLLER CONSTANT
define('MODULE_PORTAL_TRANS_STORE_RENOVATION_BOM','PORTAL_CDI_STRRNV_BOM');
define('PORTAL_TAB_STORE_RENOVATION_BOM', 'cdi_bom_approval');
define('FOLDER_CDI', 'cdi');


// CDI BOM APPROVALS MODEL CONSTANT
define('CORE_WORKFLOW_STRRNV_BOM_APPROVAL', 26);


define('DOC_TYPE_AWARD_NOTICE_DOC', 'AWARD_NOTICE_DOC');

define('DOC_TYPE_FLOOR_PLAN_DOC', 'FLOOR_PLAN_DOC');
define('DOC_TYPE_CCTV_LAYOUT_DOC', 'CCTV_LAYOUT_DOC');

define('DOC_TYPE_CEILING_PLAN_DOC', 'CEILING_PLAN_DOC');
define('DOC_TYPE_POWER_LAYOUT_DOC', 'POWER_LAYOUT_DOC');

define('DOC_TYPE_STORE_DESIGN_DOC', 'STORE_DESIGN_DOC');

define('DOC_TYPE_ENGR_PLANS_3RD_PT_DOC', 'ENGR_PLANS_3_DOC');
define('DOC_TYPE_ARCH_PLANS_DOC', 'ARCH_PLANS_DOC');
define('DOC_TYPE_ARCH_BOM_DOC', 'ARCH_BOM_DOC');
define('DOC_TYPE_PERSPECTIVE_DOC', 'PERSPECTIVE_DOC');

define('DOC_TYPE_ENGR_BOM_DOC', 'ENGR_BOM_DOC');


define('DOC_TYPE_MOM_DOC', 'MOM_DOC');
define('DOC_TYPE_ATTENDANCE_SHEET_DOC', 'ATTENDANCE_SHEET_DOC');
define('DOC_TYPE_TABULATED_BIDS_DOC', 'TABULATED_BIDS_DOC');
define('DOC_TYPE_AWRD_NOTE_CONTR_DOC', 'AWRD_NOTE_CONTR_DOC');

define('DOC_TYPE_REV_TAB_BIDS_DOC', 'REV_TAB_BIDS_DOC');

define('DOC_TYPE_ENDORSE_LETTER_DOC', 'ENDORSE_LETTER_DOC');
define('DOC_TYPE_WAIVER_DOC', 'WAIVER_DOC');


define('DOC_TYPE_SIGNAGE_BID_FORM_DOC', 'SIGNAGE_BID_FORM_DOC');
define('DOC_TYPE_TAB_BIDS_SIGN_DOC', 'TAB_BIDS_SIGN_DOC');
define('DOC_TYPE_REV_TAB_BID_SIGN_DOC', 'REV_TAB_BID_SIGN_DOC');


// CDI CONSTRUCTION PLANS MODEL CONSTANT

define('CORE_WORKFLOW_STRRNV_CON_PLANS', 27);
define('PORTAL_TABLE_CON_PLANS','cdi_construction_plans');
define('PORTAL_TAB_STORE_CON_PLANS', 'cdi_construction_plans');
define('MODULE_PORTAL_TRANS_STORE_RENOVATION_CON_PLANS','PORTAL_CDI_STRRNV_CONSTRUCTION');


define('PRES_BOM_APP_DATE_PRIA_TASK_ID', 101);


define('DOC_TYPE_CON_PLANS_DOC', 'CON_PLANS_DOC');
define('DOC_TYPE_APP_CON_PLANS_DOC', 'APP_CON_PLANS_DOC');


define('FOLDER_CON_PLANS', 'cdi_construction_plan');
define('MODULE_PORTAL_TRANS_STORE_RENOVATION_CONSTRUCTION','PORTAL_CDI_STRRNV_CONSTRUCTION');



define('DOC_TYPE_CHANGE_ORDER_DOC', 'CHANGE_ORDER_DOC');
define('DOC_TYPE_BUILD_PERMIT_REC_DOC', 'BUILD_PERMIT_REC_DOC');
define('DOC_TYPE_CARI_FILE_DOC', 'CARI_FILE_DOC');
define('DOC_TYPE_APP_LET_REQ_DOC', 'APP_LET_REQ_DOC');



define('FOLDER_STORE_RENOVATION', 'cdi_store_renovation');
define('TAB_BOM_APPROVAL', 'tab_bom_approval');




// CDI PAYMENTS MODEL CONSTANT

define('CORE_WORKFLOW_STRRNV_PAYMENTS_SEC_DEP_RENT_ADV', 29);
define('CORE_WORKFLOW_STRRNV_PAYMENTS_MALL_CHARGE', 32);
define('PORTAL_TABLE_CDI_PAYMENTS','cdi_payments');
define('PORTAL_TAB_STORE_PAYMENTS', 'cdi_payments');
define('MODULE_PORTAL_TRANS_STORE_RENOVATION_PAYMENTS','PORTAL_CDI_STRRNV_PAYMENT');
define('FOLDER_PAYMENTS', 'cdi_payment');


define('DOC_TYPE_SUPP_DOC', 'SUPP_DOC');
define('DOC_TYPE_CDI_SOA_DOC', 'CDI_SOA_DOC');
define('DOC_TYPE_DEP_SLIP_REC_DOC', 'DEP_SLIP_REC_DOC');

// WORKFLOW IDS
define('DOCUMENT_TRANSMITTAL_WORKFLOW_ID', 25);