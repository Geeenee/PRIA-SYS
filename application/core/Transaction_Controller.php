<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction_Controller extends Portal_Controller
{
	private   $module;
	private   $module_qa_js;
	protected $permissions;

	protected static $system 	     		= SYSTEM_PORTAL;
    protected $system_js_path     	 		= HMVC_FOLDER.DS.SYSTEM_PORTAL.DS;

	protected $module_js_path 				= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/";
	protected $module_js_task_path 		    = HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/tasks/";
	protected $module_task_js		 		= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/task";
	protected $path_task_views       		= PORTAL_TRANSACTIONS.'/tasks/';

	protected $module_folder         		= PORTAL_TRANSACTIONS;

	protected $main_module 					= MODULE_PORTAL_TRANSACTIONS;

	public function __construct()
	{
		parent::__construct();

		$this->load->library('pria_workflow');
		$this->load->library('pria_notification');

		$this->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');
		$this->load->model('pria_workflow_model', 'pwm_model');
		$this->load->model('permissions_model');
	}

	public function _get_common_transaction_list_resource()
	{
		try
		{
			$resources = [];

			$resources['load_css'] 		= [];
			$resources['load_js'] 		= [];

			$resources['loaded_init'] 	= array(
					'Task.toggleFilter("task_filter");',
					'Task.init();',
					'Filter.init();'
			);

			$resources['load_materialize_modal'] =  [
					'modal_append_task' 	=> array(
					  'size' 					=> 'xs-w xs-h',
					  'custom_title' 			=> 'Add Task',
					  'post'					=> true,
					  'module' 					=> PORTAL_TRANSACTIONS,
					  'method' 					=> 'modal_append_task',
					  'controller' 				=> 'task',
					  'custom_button'	=> array(
								'Append' => array(
									'type' 		=> 'button',
									'action' 	=> 'Append'
								)
							)
					),
		 	       	'modal_add_soa' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'SOA Details',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_soa',
		 	         	'controller' 	=> 'soa/Soa',
		 	         	'custom_button'	=> array(
							'Save' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Save',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	),
					'modal_add_document_transmittal' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Document Transmittal Details',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_document_transmittal',
		 	         	'controller' 	=> 'doc_transmittal/Document_transmittal_modal',
		 	         	'custom_button'	=> array(
							'Save' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Save',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	),
		 	       	'modal_cancel_dr' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Delivery Receipts Cancellation',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_cancel_dr',
						'controller' 	=> 'dr/Dr',
						'post'			=> true,
		 	         	'custom_button'	=> array(
							'Send Request' 	=> array(
								'type' 			=> 'button',
								'action' 		=> 'Send Request',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	),
		 	       	'modal_add_pr' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Purchase Request',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_pr',
		 	         	'controller' 	=> 'pr/Pr',
		 	         	'custom_button'	=> array(
							'Save' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Add',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	),
		 	       	'modal_add_po' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Purchase Order',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_po',
		 	         	'controller' 	=> 'po/Po',
		 	         	'custom_button'	=> array(
							'Add Purchase Order' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Add',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	),
		 	       	'modal_add_renewal' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Recommend Renewal',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_renewal',
		 	         	'controller' 	=> 'renewal/Renewal',
		 	         /*	'custom_button'	=> array(
							'Add Recommendation' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Add',
								'class' 		=> 'green lighten-1'
							)
						)*/
		 	       	),
		 	       	'modal_add_contract' 	=> array(
		 	         	'size' 			=> 'sm-w lg-h',
		 	         	'title' 		=> 'Contract',
		 	         	'module' 		=> PORTAL_TRANSACTIONS,
		 	         	'method' 		=> 'modal_add_contract',
		 	         	'controller' 	=> 'contract/Contract',
		 	         	'custom_button'	=> array(
							'Add Contract' 		=> array(
								'type' 			=> 'button',
								'action' 		=> 'Add',
								'class' 		=> 'green lighten-1'
							)
						)
		 	       	)
			];

			return $resources;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}



    /**
     * @Author: Kevin Villarojo
     * @Date:   2019-05-21 17:23:58
     * @Desc:
     * @ReferencedBy:
     * @Params:
     *      $content -
     *      $initial - If TRUE, load the list with the main tab.
     *                 else load the list only ( Used for load more )
     */
    protected function _load_transaction_list($content, $initial=TRUE)
    {
        try
        {

            if($initial)
            {
				$list  = $this->load->view(PORTAL_TRANSACTIONS.'/transaction_list', $content['list'], TRUE);

				//Load more actions if it exists.
				$more_actions 	= $this->load->view(PORTAL_TRANSACTIONS.'/more_actions', $content['actions'], TRUE);

				$data = [
					'list' 			=> $list,
					'more_actions' 	=> (ISSET($more_actions)) ? $more_actions : ''
				];

				$this->load->view(PORTAL_TRANSACTIONS.'/transaction_tab', $data);

				$resources = $this->_get_common_transaction_list_resource();

				if( ! EMPTY($content['resources']))
				{
					$res = $content['resources'];

					/*
						$res['loaded_init'] 		   = array_merge($resources['loaded_init'], $res['loaded_init']);

						$res['load_materialize_modal'] = array_merge($resources['load_materialize_modal'], $res['load_materialize_modal']);
					*/

					if(ISSET($res['loaded_init']))
						$resources['loaded_init'] 		   	 = array_merge($resources['loaded_init'], $res['loaded_init']);

					if(ISSET($res['load_materialize_modal']))
						$resources['load_materialize_modal'] = array_merge($resources['load_materialize_modal'], $res['load_materialize_modal']);

					if(ISSET($res['load_js']))
						$resources['load_js']  				 = array_merge($resources['load_js'], $res['load_js']);

					if(ISSET($res['load_css']))
						$resources['load_css']				 = array_merge($resources['load_css'], $res['load_css']);

					if(ISSET($res['datatable']))
						$resources['datatable']				 = $res['datatable'];

					$content['resources']  = $resources;
				}
				else
				{
					$content['resources'] = $resources;
				}
            }
            else
            {

				$content['resources']['loaded_init'] = 'Task.initLoadMore();';

                $this->load->view(PORTAL_TRANSACTIONS.'/transaction_list', $content['list']);
            }

			//If $content['footer'] = TRUE, display the load_more. Else, Don't
			if( ISSET($content['footer']) )
			{
				$display	= ['display_scroll' => ($content['footer']) ? TRUE : FALSE];
				$footer 	= (is_array($content['footer'])) ? array_merge($content['footer'], $display) : $display;

				$this->load->view('common/tabs/tab_content_footer', $footer);
			}

			if( ! EMPTY($content['resources']))
			{
				//print_var_export($content['resources']);
				$this->load_resources->get_resource($content['resources']);
			}
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

   //  protected function _load_file_list($content, $initial=TRUE)
   //  {
   //      try
   //      {
   //          if($initial)
   //          {
			// 	$list  = $this->load->view(PORTAL_TRANSACTIONS.'/tasks/'.FOLDER_FILES.'/file_list', $content['list'], TRUE);

			// 	//Load more actions if it exists.
			// 	$more_actions 	= $this->load->view(PORTAL_TRANSACTIONS.'/more_actions', $content['actions'], TRUE);

			// 	$data = [
			// 		'list' 			=> $list,
			// 		'more_actions' 	=> (ISSET($more_actions)) ? $more_actions : ''
			// 	];

			// 	$this->load->view(PORTAL_TRANSACTIONS.'/transaction_tab', $data);

			// 	$resources = $this->_get_common_transaction_list_resource();

			// 	if( ! EMPTY($content['resources']))
			// 	{
			// 		$res = $content['resources'];

			// 		$res['loaded_init'] 		   = array_merge($resources['loaded_init'], $res['resources']['loaded_init']);
			// 		$res['load_materialize_modal'] = array_merge($resources['load_materialize_modal'], $res['resources']['load_materialize_modal']);

			// 		$content['resources']  = $res;
			// 	}
			// 	else
			// 	{
			// 		$content['resources'] = $resources;
			// 	}
   //          }
   //          else
   //          {
   //              // $this->load->view(PORTAL_TRANSACTIONS.'/transaction_list', $content['list']);
   //              $this->load->view(PORTAL_TRANSACTIONS.'/tasks/'.FOLDER_FILES.'/file_list', $content['list']);
   //          }

			// //If $content['footer'] = TRUE, display the load_more. Else, Don't
			// if( ISSET($content['footer']) )
			// {
			// 	$display	= ['display_scroll' => ($content['footer']) ? TRUE : FALSE];
			// 	$footer 	= (is_array($content['footer'])) ? array_merge($content['footer'], $display) : $display;

			// 	$this->load->view('common/tabs/tab_content_footer', $footer);
			// }

			// if( ! EMPTY($content['resources']))
			// {
			// 	//print_var_export($content['resources']);

			// 	$this->load_resources->get_resource($content['resources']);
			// }
   //      }
   //      catch(PDOException $e)
   //      {
   //          throw $e;
   //      }
   //      catch(Exception $e)
   //      {
   //          throw $e;
   //      }
   //  }

	public function construct_sub_nav($content = array(), $width = '200')
	{
		try
		{
			$data 				= $content;

			//Set the proper value for display
			$data['display'] 	= ISSET($content['display']) && !($content['display']) ? ' none': '';

			//Set the default for width
			$data['width']		= !ISSET($content['width']) ? 200 : $content['width'];

			$view_page 			= PORTAL_COMMON.'/nav/nav_wrapper';

			return $this->load->view($view_page, $data, TRUE);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	/**
	 * @Edited: Kevin Villarojo
	 * @Date: 2019-05-14 14:28:04
	 * @Desc:  How to construct ajax tabas under transactions module
	 * @ReferencedBy:
	 */
	public function construct_ajax_tabs($content = array(), $page = TRUE)
	{
		try
		{
			$resources	= $content['resources'];

			unset($content['resources']);

			$data 						= $content;
			$data['class']				= 'flat sm p-l-lg p-r-lg';

			//Get left and right nav configs
			$sub_nav_right_config  		= ( ! EMPTY($content['sub_nav_right'])) ? $content['sub_nav_right'] : [];
			$sub_nav_left_config   		= ( ! EMPTY($content['sub_nav_left']))  ? $content['sub_nav_left']  : [];

            if( ! EMPTY($sub_nav_right_config))
			    $data['sub_nav_right'] 	= $this->construct_filters($sub_nav_right_config);

			if( ! EMPTY($sub_nav_left_config))
			    $data['sub_nav_left'] 	= $this->construct_lists($sub_nav_left_config);


			//Load the page
			if($page)
			{
				$view_page	= PORTAL_COMMON.'/tabs/tabs_wrapper';

				$this->template->load($view_page, $data, $resources, Portal_Controller::$system);
			}
			else
			{
				$view_page	= PORTAL_COMMON.'/tabs/sub_tabs_wrapper';

				$html = $this->load->view($view_page, $data, TRUE);
				$html .= $this->load_resources->get_resource($resources, TRUE);

				return $html;
			}
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	public function construct_lists($content)
	{
		try
		{
			$default = [
				'id'             => 'sb_pr_list',
				'sidebar_toggle' => TRUE,
				'sidebar_close'  => FALSE,
				'position'       => SB_LEFT,
				'theme'          => SB_SKIN_LIGHT,
				'data'			 => [],
				'title'			 => 'left nav',
				'placeholder'	 => '',
				'display'		 => TRUE
			];

			$data               = array_merge($default, $content);

			$data['content'] 	= $this->load->view(PORTAL_COMMON.'/nav/nav_lists', $data, TRUE);

			return $this->construct_sub_nav($data);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	public function construct_filters($content)
	{
		try
		{
			$default = [
				'id'             => 'task_filter',
				'sidebar_toggle' => FALSE,
				'sidebar_close'  => TRUE,
				'position'       => SB_RIGHT,
				'theme'          => SB_SKIN_LIGHT,
				'width'			 => 300,
				'extra_filters'  => '',
				'keyword'		 =>	ISSET($_GET['keyword']) ? $_GET['keyword'] : '',
				'display'        => ISSET($_GET['keyword']) ? TRUE : FALSE,
			];

			$data               = array_merge($default, $content);
			$data['core_users']	= $this->tm_model->get_core_user(array(), array("user_id", "CONCAT_WS(' ', AGDEC(fname), AGDEC(mname), AGDEC(lname), AGDEC(ext_name)) full_name"), TRUE, array('full_name' => 'ASC'));


		/*
			$data['keyword']	= ISSET($_GET['keyword']) ? $_GET['keyword'] : '';
			$data['display']	= ISSET($_GET['keyword']) ? TRUE : FALSE;
 */
			$data['content'] 	= $this->load->view(PORTAL_COMMON.'/nav/nav_filters' , $data, TRUE);

			return $this->construct_sub_nav($data);
		}
		catch( PDOException $e )
		{
			$msg 	= $this->get_user_message($e);

			$this->error_index( $msg );
		}
		catch( Exception $e )
		{
			$msg  	= $this->rlog_error($e, TRUE);

			$this->error_index( $msg );
		}
	}

	public function _construct_module_tabs($parent_module, $module_folder, $param_1 = NULL, $param_2 = NULL)
	{
		try
		{
			$tabs			= array();
			$where			= array('hide_flag' => FALSE);

			$tab_modules	= $this->permissions_model->get_modules(NULL, NULL, $parent_module, $where);

			$param_1	= ($param_1 !== NULL)? $param_1: "0";
			$param_2	= ($param_2 !== NULL)? $param_2: "0";

			if(COUNT($tab_modules) > 0)
			{
				foreach($tab_modules AS $key => $tab_module)
				{
					if(check_permission($tab_module['module_code'], ACTION_VIEW))
					{
						$tab_name	= str_replace(' ', '_', $tab_module['module_name']);

						$tabs[]		= array(
								'title'			=> $tab_module['module_name'],
								'tab'			=> strtolower($tab_name),
								'module'		=> $module_folder,
								'controller'	=> $tab_module['link'] . '/index/' . encrypt_id($parent_module) . '/' . encrypt_id($tab_module['module_code']) . '/' . $param_1 . '/' . $param_2,
								'post_form'		=> json_encode(array("#filter_form"))
						);
					}
				}
			}

			return $tabs;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	protected function get_ag_code_per_module($module_code)
    {
    	try
    	{
	        switch($module_code)
	        {
	            case MODULE_PORTAL_TRANS_CONTRACT_GROWERS:
	                return [AG_CONTRACT_GROWERS];
	            break;
				case MODULE_PORTAL_TRANS_GOODS_GOODS:
					return [AG_GOODS_BAVI, AG_GOODS_BFFI];
				break;
				case MODULE_PORTAL_TRANS_GOODS_MARINADES:
					return [AG_GOODS_MARINADES];
				break;
				case MODULE_PORTAL_TRANS_TOLL_PARTNERS:
					return [AG_TOLL_PARTNERS];
				break;
				case MODULE_PORTAL_TRANS_FORWARDERS:
					return [AG_FORWARDERS];
				break;
				case MODULE_PORTAL_TRANS_INBOUND_TRUCKERS:
					return [AG_INBOUND_CENTRAL, AG_INBOUND_NORMAL];
				break;
				case MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS:
					return [AG_OUTBOUND];
				break;
				case MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS:
					return [AG_FEEDMILL];
				break;
				case MODULE_PORTAL_TRANS_CONTRACTORS:
					return [AG_CONTRACTORS];
				break;
				case MODULE_PORTAL_TRANS_LESSORS:
					return [AG_LESSORS];
				break;
				case MODULE_PORTAL_TRANS_MANPOWER:
					return [AG_MANPOWER];
				break;

				case MODULE_PORTAL_TRANS_SOA_BASED:
					return [AG_SOA_BASED];
				break;

				case MODULE_PORTAL_TRANS_DOCUMENT_TRANSMITTAL:
					return [AG_DOCUMENT_TRANSMITTAL];
				break;

	            default:
	                throw new Exception('Invalid module code.');
	        }
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	protected function get_ag_per_tab_module($tab_module_code)
	{
		try
		{
		  $row = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module_code], ['ag_code'], [], TRUE);

		  return array_column($row, 'ag_code');
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	protected function get_module_code_per_task_ag_code($ag_code)
	{
		try
		{
		  $row = $this->tm_model->get_module_account_group(['account_group_code' => $ag_code]);

		  return $row[0]['module_code'];
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	protected function get_workflow_per_module_tab($module_code, $tab_module_code)
    {
    	try
    	{ //kevin
			$where 		= ['tab_module_code' => $tab_module_code, 'parent_module_code' => $module_code];

			$workflows 	= $this->tm_model->get_tab_module($where, ['core_workflow_id'], [], TRUE);

			return array_column($workflows, 'core_workflow_id');

	/*         switch($module_code)
	        {
				case MODULE_PORTAL_TRANS_CONTRACT_GROWERS:
					if($tab_module_code == MODULE_PORTAL_TRANS_CG_IO)
						return [CORE_WORKFLOW_INTERNAL_ORDER];
					else
					return [];
				break;
				case MODULE_PORTAL_TRANS_GOODS_GOODS:
					if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_G_PR)
						return [CORE_WORKFLOW_PURCHASE_REQUEST_BAVI, CORE_WORKFLOW_PURCHASE_REQUEST_BFFI_MARINADES];
					else if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_G_PO)
						return [CORE_WORKFLOW_PURCHASE_ORDER_W_APPROVAL, CORE_WORKFLOW_PURCHASE_ORDER_WO_APPROVAL];
					else if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_G_SOA)
						return [CORE_WORKFLOW_GOODS_SOA];
					else
						return [];
				break;
				case MODULE_PORTAL_TRANS_GOODS_MARINADES:
					if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_M_PR)
						return [CORE_WORKFLOW_PURCHASE_REQUEST_BFFI_MARINADES];
					else if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_M_PO)
						return [CORE_WORKFLOW_PURCHASE_ORDER_W_APPROVAL, CORE_WORKFLOW_PURCHASE_ORDER_WO_APPROVAL];
					else if($tab_module_code == MODULE_PORTAL_TRANS_GOODS_M_SOA)
						return [CORE_WORKFLOW_GOODS_SOA];
					else
						return [];
				break;
				case MODULE_PORTAL_TRANS_TOLL_PARTNERS:
					//return [0];
					return [CORE_WORKFLOW_TRUCKER_CENTRAL];
				break;
				case MODULE_PORTAL_TRANS_FORWARDERS:
					return [CORE_WORKFLOW_FORWARDERS];
				break;
				case MODULE_PORTAL_TRANS_INBOUND_TRUCKERS:
					return [CORE_WORKFLOW_TRUCKER_CENTRAL,CORE_WORKFLOW_TRUCKER_NORMAL];
				break;
				case MODULE_PORTAL_TRANS_OUTBOUND_TRUCKERS:
					return [CORE_WORKFLOW_TRUCKER_NORMAL];
				break;
				case MODULE_PORTAL_TRANS_FEEDMILL_TRUCKERS:
					return [CORE_WORKFLOW_FEEDMILL];
				break;
				case MODULE_PORTAL_TRANS_CONTRACTORS:
					if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_SN)
						return [CORE_WORKFLOW_CONTRACTOR_SITE_NOMINATION];
					else if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_BOQ)
						return [CORE_WORKFLOW_CONTRACTOR_BOQ];
					else if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_PROJECTS)
						return [CORE_WORKFLOW_CONTRACTOR_PROJECT];
					else if($tab_module_code == MODULE_PORTAL_TRANS_CONTRACTORS_PO)
						return [CORE_WORKFLOW_PURCHASE_ORDER_W_APPROVAL, CORE_WORKFLOW_PURCHASE_ORDER_WO_APPROVAL];
					else
						return [];
				break;
				case MODULE_PORTAL_TRANS_LESSORS:
					return [CORE_WORKFLOW_LESSORS];
				break;
				case MODULE_PORTAL_TRANS_MANPOWER:
					//return [0];
					return [CORE_WORKFLOW_MANPOWER];
				break;

	            default:
	                throw new Exception('Invalid module tab code.');
	        } */
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    public function _construct_url_filter($filter_params)
    {
    	try
    	{
    		$filter_str			= "";
    		$counter			= 0;

    		if(COUNT($filter_params) > 0)
    		{
    			foreach($filter_params AS $key => $value)
    			{
    				$value		= is_array($value)? implode('_', $value): $value;

    				$filter_str	.= (($counter == 0)? "": "&").$key."=".$value;

    				$counter++;
    			}
    		}

    		return $filter_str;
    	}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    public function _construct_transactions_having($filter_params, $ag_codes=array())
    {
    	try
    	{
    		$having_str						= "";
    		$values							= array();

    		$counter						= 0;

    		if(!EMPTY($filter_params['filter-keyword']) OR (ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0) OR (ISSET($filter_params['filter-status']) AND COUNT($filter_params['filter-status']) > 0))
    		{

	    		$tbl_pria_tasks				= $this->tm_model->tbl_pria_tasks;
	    		$tbl_pria_workflow_stages	= $this->tm_model->tbl_pria_workflow_stages;
	    		$tbl_pria_workflows			= $this->tm_model->tbl_pria_workflows;

	    		$ag_values					= array();
	    		$ag_where = $ag_marks		= "";

	    		if(COUNT($ag_codes) > 0)
	    		{
	    			foreach ($ag_codes as $key => $ag_code)
	    			{
	    				$ag_marks			.= ($key > 0)? ", ?": "?";
	    				$ag_values[]		= $ag_code;
	    			}

	    			$ag_where				= "C.account_group_code IN ($ag_marks)";
	    		}

	    		$having_str					= "HAVING";

	    		if(!EMPTY($filter_params['filter-keyword']))
	    		{
	    			$having_str				.=<<<EOS
	    					(LOWER(display_num) LIKE ? OR LOWER(display_name) LIKE ? OR LOWER(display_extra) LIKE ?)
EOS;
	    			$values					= array_merge($values, array(
	    					"%".strtolower(filter_var($filter_params['filter-keyword'], FILTER_SANITIZE_STRING))."%",
	    					"%".strtolower(filter_var($filter_params['filter-keyword'], FILTER_SANITIZE_STRING))."%",
	    					"%".strtolower(filter_var($filter_params['filter-keyword'], FILTER_SANITIZE_STRING))."%")
	    			);
	    		}

	    		if((ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0)
	    		OR (ISSET($filter_params['filter-status']) AND COUNT($filter_params['filter-status']) > 0))
	    		{
		    		$having_str				.= (!EMPTY($filter_params['filter-keyword']))? " AND": "";

	    			$having_str				.= <<<EOS
		    				pria_workflow_id IN (
			    				SELECT DISTINCT(C.pria_workflow_id) FROM $tbl_pria_tasks A
								LEFT JOIN $tbl_pria_workflow_stages B ON A.pria_stage_id = B.pria_stage_id
								LEFT JOIN $tbl_pria_workflows C ON B.pria_workflow_id = C.pria_workflow_id
								WHERE $ag_where
EOS;
					$values					= array_merge($values, $ag_values);

		    		if(ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0)
		    		{
		    			$q_marks				= "";

		    			foreach($filter_params['filter-assign-to'] AS $key => $value)
		    			{
		    				$q_marks			.= ($key > 0)? ", ?": "?";
		    				$values[]			= $value;
		    			}

		    			$having_str				.=<<<EOS
		    				AND A.user_id IN ($q_marks)
EOS;
		    		}

		    		if(ISSET($filter_params['filter-status']) AND COUNT($filter_params['filter-status']) > 0)
		    		{
		    			$q_marks				= "";
		    			$status_null			= "";

		    			foreach($filter_params['filter-status'] AS $key => $value)
		    			{
		    				if($value != 0)
		    				{
			    				$q_marks		.= (!EMPTY($q_marks))? ", ?": "?";
			    				$values[]		= $value;
		    				}
		    				else
		    				{
		    					$status_null	= " A.task_status_id IS NULL";
		    				}
		    			}

		    			$status_where			= "(" . ((!EMPTY($q_marks))? ("A.task_status_id IN (" . $q_marks . ") " . ((!EMPTY($status_null))? "OR ": "")): "") . $status_null . ")";
		    			$having_str				.=<<<EOS
		    				AND $status_where
EOS;
		    		}

		    		$having_str					.=<<<EOS
		    				)
EOS;
		    	}
    		}

    		return array('having' => $having_str, 'values' => $values);
    	}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    public function _construct_tasks_having($filter_params)
    {
    	try
    	{
    		$having_str						= "";
    		$values							= array();

    		$counter						= 0;

    		if((ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0) OR (ISSET($filter_params['filter-status']) AND COUNT($filter_params['filter-status']) > 0))
    		{
	    		$having_str					= "HAVING";

	    		if(ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0)
	    		{
	    			$q_marks				= "";

	    			foreach($filter_params['filter-assign-to'] AS $key => $value)
	    			{
	    				$q_marks			.= ($key > 0)? ", ?": "?";
	    				$values[]			= $value;
	    			}

	    			$having_str				.=<<<EOS
	    				user_id IN ($q_marks)
EOS;
	    		}

	    		if(ISSET($filter_params['filter-status']) AND COUNT($filter_params['filter-status']) > 0)
	    		{
	    			$q_marks				= "";
	    			$status_null			= "";

	    			foreach($filter_params['filter-status'] AS $key => $value)
	    			{
	    				if($value != 0)
	    				{
		    				$q_marks		.= (!EMPTY($q_marks))? ", ?": "?";
		    				$values[]		= $value;
	    				}
	    				else
	    				{
	    					$status_null	= " task_status_id IS NULL";
	    				}
	    			}

	    			$status_where			= "(" . ((!EMPTY($q_marks))? ("task_status_id IN (" . $q_marks . ") " . ((!EMPTY($status_null))? "OR ": "")): "") . $status_null . ")";

	    			$having_str				.= ((ISSET($filter_params['filter-assign-to']) AND COUNT($filter_params['filter-assign-to']) > 0))? " AND": "";
	    			$having_str				.=<<<EOS
	    				$status_where
EOS;

	    		}
    		}

    		return array('having' => $having_str, 'values' => $values);
    	}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
    }

    public function _explode_filter($filter_params)
    {
    	try
    	{
    		foreach($filter_params AS $key => $value)
    		{
    			switch($key)
    			{
    				case 'filter-assign-to':
    				case 'filter-status':
    					$filter_params[$key]	= explode('_', $value);
    				break;
    				default:
    					$filter_params[$key]	= $value;
    				break;
    			}
    		}

    		return $filter_params;
    	}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	protected function get_workflow_id_by_tab_module($tab_module)
	{
		try
		{
			$result = $this->tm_model->get_tab_module(['tab_module_code' => $tab_module], ['core_workflow_id']);

			return $result['core_workflow_id'];
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function system_notification($task_id, $task_details = array(), $module_code = NULL, $roles = array(), $task_return_id = NULL, $user_id=NULL, $from_ret_flag = FALSE, $status_label = '', $task_orig_details = '')
	{
		try{
			$this->pria_notification->system_notification($task_id, $task_details, $module_code, $roles, $task_return_id, $user_id, $from_ret_flag, $status_label, $task_orig_details);

		/* 	$remarks = '';
			//Starts
			$where          = array('pre_pria_task_id' => $task_id);
			$predecessors   = $this->tm_model->get_next_predecessors($where);

			$user_id 		= ( ! EMPTY($user_id)) ? $user_id : $this->session->user_id;

			$where 				= array('pria_task_id' => $task_return_id);
        	$task_notif_info 	= $this->tm_model->get_task_notif_info($where);

        	if($task_orig_details['remarks']){
        		$remarks = "(Remarks: ".$task_orig_details['remarks'].")";
        	}

        	if($task_details['reference_num']){
        		$ref_n = "<font color='8F44A9'> ".$task_details['reference_num']." </font>";
        	}

        	//insert to notification
            if($from_ret_flag){
            	$encode_link    = '/'.$task_details['controller'].'?t='.base64_url_encode($task_notif_info['pria_task_id']);
            }else{
            	$encode_link    = '/'.$task_details['controller'].'?t='.base64_url_encode($task_id);
            }

            //insert to system notification
            //for return
            if($task_return_id){

            	//$notification   = "<font color='#e23b3b'>".$task_details['actor']."</font> <font color='#000000'>returned </font><font color='#8F44A9'>".$task_details['task_name']."</font> <font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";

            	$notification   = "<font color='#e23b3b'>".$task_notif_info['doc_name']."</font> ".$ref_n." <font color='#000000'>has been ".$status_label." </font> <font color='#e23b3b'>".$remarks."</font>";

            	$notify_who = array(
                    'notification_icon'         => 'speaker_notes',
                    'notification_mobile'       => NULL,
                    'notify_users'              => array($task_notif_info['user_id']),
                    'notify_orgs'               => array(), //no default data
                    'notify_roles'              => array(),
                    'module_code'               => $module_code,
                    'displayed_socket_flag'     => NO_FLAG,
                    'listed_flag'               => YES_FLAG
                );

                $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;

                if($roles){
                	//call private function for sending email
	            	$this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id);
                }else{
                	$this->notify->insert_notification($notification, $notify_who, $user_id);
                }

               	// else for completed
            }else{
            	$notification   = "<font color='#e23b3b'>".$task_details['doc_name']."</font> ".$ref_n." <font color='#000000'>has been ".$status_label." </font>";

            	//$notification   = "<font color='#e23b3b'>".$task_details['actor']."</font> <font color='#000000'>completed </font><font color='#8F44A9'>".$task_details['task_name']."</font> <font color='#e23b3b'>(List: ".$task_details['reference_num'].")</font>";

            	if($roles){
            		//call private function for sending email
            		$this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id);
            	}else{
            		foreach ($predecessors as $predecessor):
		                IF(($predecessor['pria_task_id'])){

		                    $where = array( 'pria_task_id' => $predecessor['pria_task_id']);
		                    $roles = $this->tm_model->get_task_roles($where);

		                    //call private function for sending email
		                    $this->_send_system_notification($roles, $module_code, $notification, $encode_link, $user_id);
		                }
		            endforeach;
            	}
            } */
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
/*

	private function _send_system_notification($roles = array(), $module_code = NULL, $notification = NULL, $encode_link = NULL, $user_id=NULL)
	{
		try{

			$prev_users = array();

			foreach ($roles as $role):
                //get user with this role $roles

                $role_arr = explode (",", $role['role_code']);

                if(!EMPTY($role_arr)){
	                foreach ($role_arr as $ra ) {

		                $where = array('role_code' => $ra);
		                $users = $this->tm_model->get_users_role($where);

		                if($users){

		                    foreach ($users as $user):

		                        if(!in_array($user['user_id'], $prev_users)){
		                            $notify_who = array(
		                                'notification_icon'         => 'speaker_notes',
		                                'notification_mobile'       => NULL,
		                                'notify_users'              => array($user['user_id']),
		                                'notify_orgs'               => array(), //no default data
		                                'notify_roles'              => array(),
		                                'module_code'               => $module_code,
		                                'displayed_socket_flag'     => NO_FLAG,
		                                'listed_flag'               => YES_FLAG
		                            );

		                            $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;

		                            $this->notify->insert_notification($notification, $notify_who, $user_id);
		                        }
		                        $prev_users[] = $user['user_id'];
		                    endforeach;
		                }
	            	}
            	}

            endforeach;
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
 */

	public function import_system_notification($roles = array(), $module_code = NULL, $notification = NULL, $message = NULL, $user_id=NULL)
	{
		try{
			$this->pria_notification->import_system_notification($roles, $module_code, $notification, $message, $user_id);
		/* 	$prev_users = array();

			foreach ($roles as $role):
                //get user with this role $roles
                $where = array('role_code' => $role);
                $users = $this->tm_model->get_users_role($where);

                if($users){

                    foreach ($users as $user):

                        if(!in_array($user['user_id'], $prev_users)){
                            $notify_who = array(
                                'notification_icon'         => 'speaker_notes',
                                'notification_mobile'       => NULL,
                                'notify_users'              => array($user['user_id']),
                                'notify_orgs'               => array(), //no default data
                                'notify_roles'              => array(),
                                'module_code'               => $module_code,
                                'displayed_socket_flag'     => NO_FLAG,
                                'listed_flag'               => YES_FLAG
                            );

                            // $notify_who['notification_html'] = base_url().PORTAL_TRANSACTIONS.$encode_link;
                            $notify_who['notification_html'] = $message;

                            $this->notify->insert_notification($notification, $notify_who, $user_id);
                        }
                        $prev_users[] = $user['user_id'];
                    endforeach;
                }

            endforeach; */
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}


	/** Below are the codes used for changing the status of the task */
	protected function tag_task($pria_task_id, $task_status_id, $columns=[], $user_id=NULL, $recipient_id = NULL)
	{
		try
		{

			$this->pria_workflow->tag_task($pria_task_id, $task_status_id, $columns, $user_id, $recipient_id);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
}