<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Notes By: Gene | On : 2025-10-17
|------------------------------------------------------------------------------------------
| This is the base document_transmittal controller 
| This loads the TABS for the Document Transmittal Module ( Overview, Document Transmittal & Files. )
| 
|------------------------------------------------------------------------------------------
|
|------------------------------------------------------------------------------------------
*/

class Document_transmittal_mainpage extends Transaction_Controller
{
    private $moduleJsPath;

    public function __construct()
    {
        parent::__construct();
        $this->module_code    = MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL; // Value : PORTAL_DOCUMENT_TRANSMITTAL
        $this->module_folder  = PORTAL_TRANSACTIONS; // Value : transactions
        $this->controller     = strtolower(__CLASS__); // Value : document_transmittal
        $this->permissions    = check_permission($this->module_code);
        $this->moduleJsPath   = $this->system_js_path . $this->module_folder . DS . $this->controller; // Path : systems/portal/transactions/document_transmittal
    }

    public function index()
    {
        try {
            if (!$this->permissions[ACTION_VIEW]) {
                throw new Exception($this->lang->line('err_unauthorized_access'));
            }

            $common = $this->get_common_resources($this->module_code);

            $resources = [
                'load_css' => array_merge([
                    CSS_SELECTIZE,
                    CSS_DATETIMEPICKER,
                    CSS_UPLOAD,
                    CSS_LABELAUTY
                ], $common['css']),
                'load_js' => array_merge([
                    JS_SELECTIZE,
                    JS_DATETIMEPICKER,
                    JS_UPLOAD,
                    JS_LABELAUTY,
                    $this->module_task_js
                ], $common['js']),
                'loaded_init' => $common['init'],
                'load_materialize_modal' => $common['modal']
            ];

            $data = [
                'module' => $this->module_folder,
                'resources' => $resources,
                'sub_nav_right' => ['sidebar_close' => TRUE],
                'active_sub_menu' => MODULE_PORTAL_TRANSACTIONS,
                'page_title' => 'Document Transmittal'
            ];

            $tabs = $this->_construct_module_tabs($this->module_code, $this->module_folder); // Value : transactions, PORTAL_DOCUMENT_TRANSMITTAL
            if (EMPTY($tabs)) {
                throw new Exception($this->lang->line('err_trans_no_access_tabs'));
            }
            $data['tabs'] = $tabs;

            $this->construct_ajax_tabs($data);
        } catch (PDOException $e) {
            $this->error_index($this->get_user_message($e));
        } catch (Exception $e) {
            $this->error_index($e->getMessage());
        }
    }
}
