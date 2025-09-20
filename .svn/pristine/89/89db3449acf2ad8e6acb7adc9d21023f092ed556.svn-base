<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Params extends SYSAD_Controller 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function get_constants_ajax()
    {
        $constants_arr          = array();

        try
        {
        
            /* Get all the constants that starts with CORE_ */
            $core_module_folder     = $this->get_constants('^CORE_');
            $core_stat_active       = $this->get_constants('ACTIVE');
            $core_stat_pending      = $this->get_constants('PENDING');
            $core_stat_inactive     = $this->get_constants('INACTIVE');
            $core_stat_approved     = $this->get_constants('APPROVED');
            $core_stat_disapp       = $this->get_constants('DISAPPROVED');
            $core_stat_deleted      = $this->get_constants('DELETED');
            $core_stat_blocked      = $this->get_constants('BLOCKED');
            $core_stat_draft        = $this->get_constants('DRAFT');
            $core_stat_expired      = $this->get_constants('EXPIRED');

            $constants_arr          = array_merge( $constants_arr, $core_module_folder );
            $constants_arr          = array_merge( $constants_arr, $core_stat_active );
            $constants_arr          = array_merge( $constants_arr, $core_stat_pending );
            $constants_arr          = array_merge( $constants_arr, $core_stat_inactive );
            $constants_arr          = array_merge( $constants_arr, $core_stat_approved );
            $constants_arr          = array_merge( $constants_arr, $core_stat_disapp );
            $constants_arr          = array_merge( $constants_arr, $core_stat_deleted );
            $constants_arr          = array_merge( $constants_arr, $core_stat_blocked );
            $constants_arr          = array_merge( $constants_arr, $core_stat_draft );
            $constants_arr          = array_merge( $constants_arr, $core_stat_expired );

            // For root path
            // Use this constant to get the correct base path or root path where the uploaded file will be stored
            $change_upload_path     = get_setting(MEDIA_SETTINGS, "change_upload_path");

            $checked_upload_path    = ( !EMPTY( $change_upload_path ) ) ? true : false;

            $constants_arr['ROOT_PATH']                             = $this->get_root_path();
            $constants_arr['CHECK_CUSTOM_UPLOAD_PATH']              = $checked_upload_path;
            /* Other parameters can be defined here. Thinking if some sys_params are applicable */
            $constants_arr['SUCCESS']                               = SUCCESS;
            $constants_arr['ERROR']                                 = ERROR;
            /* Task status */
            $constants_arr['TASK_STATUS_ONGOING']                   = TASK_STATUS_ONGOING;
            $constants_arr['TASK_STATUS_DONE']                      = TASK_STATUS_DONE;
            $constants_arr['TASK_STATUS_RETURNED']                  = TASK_STATUS_RETURNED;
            $constants_arr['TASK_STATUS_SKIPPED']                   = TASK_STATUS_SKIPPED;
            $constants_arr['TASK_STATUS_DISAPPROVED']               = TASK_STATUS_DISAPPROVED;
            $constants_arr['TASK_STATUS_CANCELLED']                 = TASK_STATUS_CANCELLED;
            $constants_arr['TASK_STATUS_APPROVED']                  = TASK_STATUS_APPROVED;
            /*  ORGANIZATION TYPES*/
            $constants_arr['ORG_TYPE_ORGANIZATION']                 = ORG_TYPE_ORGANIZATION;
            /*  SITE TYPES*/
            $constants_arr['SITE_TYPE_DRESSING_PLANT']              = SITE_TYPE_DRESSING_PLANT;
            $constants_arr['SITE_TYPE_FARM']                        = SITE_TYPE_FARM;
            $constants_arr['SITE_TYPE_STORE']                       = SITE_TYPE_STORE;
            $constants_arr['SITE_TYPE_WAREHOUSE']                   = SITE_TYPE_WAREHOUSE;
            $constants_arr['SITE_TYPE_COST_CENTER']                 = SITE_TYPE_COST_CENTER;
            $constants_arr['SITE_TYPE_OFFICE']                      = SITE_TYPE_OFFICE;
            /* AG CODES */
            $constants_arr['AG_FORWARDERS']                         = AG_FORWARDERS;
            $constants_arr['AG_GOODS_BFFI']                         = AG_GOODS_BFFI;
            $constants_arr['AG_GOODS_MARINADES']                    = AG_GOODS_MARINADES;
            /* TAB MODULES */
            $constants_arr['MODULE_TAB_FORWARDERS_DR']              = MODULE_PORTAL_TRANS_FORWARDER_DR;
            $constants_arr['REF_B_APV']                             = REF_B_APV;
            $constants_arr['REF_B_CV']                              = REF_B_CV;
        }
        catch( PDOException $e )
        {
            $msg    = $this->get_user_message($e);

            $this->rlog_error($e);
        }
        catch( Exception $e )
        {
            $this->rlog_error($e);
        }

        echo json_encode( $constants_arr );
    }
}