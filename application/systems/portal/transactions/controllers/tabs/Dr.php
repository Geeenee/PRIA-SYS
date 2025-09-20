<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dr extends Transaction_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->module_folder  		= PORTAL_TRANSACTIONS;
		$this->controller 	  		= strtolower(__CLASS__);
		$this->module_js 			= HMVC_FOLDER."/".SYSTEM_PORTAL."/".PORTAL_TRANSACTIONS."/".$this->controller;

		$this->load->model(FOLDER_DELIVERY_GOODS.'/dr_model');
	}

	public function index($encoded_module_code, $encoded_tab_module)
	{
		try
		{
			$data = $resources = array();

			$module_code				= decrypt_id($encoded_module_code);
			$tab_module					= decrypt_id($encoded_tab_module);

			$common_resource          	= $this->get_common_resources($module_code);

			$resources['load_css']    	= array_merge($common_resource['css'], array(CSS_DATATABLE_MATERIAL, CSS_SELECTIZE, CSS_DATETIMEPICKER));
			$resources['load_js']     	= array_merge($common_resource['js'], array(JS_DATATABLE, JS_DATATABLE_MATERIAL, JS_SELECTIZE, JS_DATETIMEPICKER, $this->module_js));

			$resources['loaded_init']	= array_merge(
				$common_resource['init'], [
					'Dr.selectAll();',
					//'Dr.cancel_dr();',
					'Dr.save();',
					'Dr.dr_approval();',
					'Dr.dr_disapproval();',
					'materialize_select_init();',
					'Dr.init();'
				]
				/* array('Dr.selectAll();'),
				array('Dr.cancel_dr();'),
				array('Dr.save();'),
				array('Dr.dr_approval();'),
				array('Dr.dr_disapproval();'),
				array("materialize_select_init();") */
			);

			$resources['load_materialize_modal'] =  [
					'modal_approval' 	=> array(
					  'size' 					=> 'sm-w md-h',
					  'custom_title' 			=> 'Approve DR', 
					  'post'					=> true,
					  'module' 					=> PORTAL_TRANSACTIONS.'/tabs',
					  'method' 					=> 'modal_approval',
					  'controller' 				=> 'Dr',
					  'custom_button'	=> array(
								'Approve' => array(
									'type' 		=> 'button',
									'action' 	=> 'Approve'
								)
							)
					),
					'modal_disapproval' 	=> array(
					  'size' 					=> 'sm-w md-h',
					  'custom_title' 			=> 'Disapprove DR', 
					  'post'					=> true,
					  'module' 					=> PORTAL_TRANSACTIONS.'/tabs',
					  'method' 					=> 'modal_disapproval',
					  'controller' 				=> 'Dr',
					  'custom_button'	=> array(
								'Disapprove' => array(
									'type' 		=> 'button',
									'action' 	=> 'Disapprove'
								)
							)
					),
					'modal_add_soa' 	=> array(
						'size' 			=> 'sm-w lg-h',
						'title' 		=> 'SOA Details',
						'module' 		=> PORTAL_TRANSACTIONS,
						'method' 		=> 'modal_add_soa',
						'controller' 	=> 'soa/Soa',
						'post'			=> true,
						'custom_button'	=> array(
						  'Save' 		=> array(
							  'type' 			=> 'button',
							  'action' 		=> 'Add',
							  'class' 		=> 'green lighten-1'
						  )
					  )
				  )
				];

			$params 			= get_params();
			$keyword			=  ISSET($params['filter_form']['filter-keyword']) && ! EMPTY($params['filter_form']['filter-keyword']) ? $params['filter_form']['filter-keyword'] : '';


			$datatable		= array(
					'path'				=> $this->module_folder .'/tabs/'. $this->controller .'/get_dr_list',
					'table_id'			=> 'tbl_drs',
					'advanced_filter'	=> TRUE,
					'post_data'			=> array('encoded_module_code' => $encoded_module_code, 'encoded_tab_module' => $encoded_tab_module, 'keyword' => $keyword),
					'sortable_index' 	=> [1,2,3,4,5,6],
					'order' 			=> 1, 
					'sort_order' 		=> 'desc'
			);
			
			//Set up additional actions in tab
			if(check_permission($tab_module, ACTION_ADD))
			{
				$buttons[]				= array(
					'target'		=> 'modal_quick_add',
					'icon'			=> 'unarchive',
					'label'			=> 'Import DR',
					'id'			=> 'import_dr',
					'class'			=> 'purple darken-1',
					'onclick'		=> 'modal_quick_add_init(\'temp_drs\',\'Import DR\')'
				);
				
				// $buttons[]				= array(
					// 		'target'		=> 'modal_add_soa',
					// 		'label'			=> 'Add SOA',
					// 		'class'			=> 'purple darken-1',
					// 		'id'			=> 'add_soa',
					// 		'onclick'		=> 'modal_add_soa_init(\''.$tab_module.'\',\'Add SOA\')'
					// );				
			}

			if(check_permission($tab_module, ACTION_EDIT))
			{
				$buttons[]				= array(
					'target'		=> '',
					// 'target'		=> 'modal_cancel_dr',
					'label'			=> 'Request Cancellation',
					'class'			=> 'cancel_dr_class purple darken-1',
					'id'			=> 'cancel_dr',
					'onclick'		=> 'Dr.cancel_dr()'
					// 'onclick'		=> 'modal_cancel_dr_init(\''.$tab_module.'\',\'Delivery Receipts Cancellation\')'
				);
            }	
            
            if (check_permission(MODULE_PORTAL_TRANS_FORWARDER_SOA, ACTION_ADD)) {
                $buttons[]				= array(
                'target'		=> '',
                'label'			=> 'Add SOA',
                'class'			=> 'purple darken-1',
                'id'			=> 'add_soa',
                //'onclick'		=> 'modal_add_soa_init(\''.PORTAL_FORWARDER_SOA.'\',\'Add SOA\')'
                'onclick'		=> 'Dr.add_soa()'
                );
            }

			$resources['datatable']			= $datatable;
			$list['list']['list'] 			= array();
			$list['list']['with_datatable'] = TRUE;

			//Consolidate data to be passed in views.
			$data						= array_merge($list, array(
					'footer'			=> NULL,
					'resources'			=> $resources,
					'actions'			=> array(
							'imports'	=> [],//$imports,
							'buttons'	=> $buttons,
							'hide_filter'	=> TRUE
					)
			));
			
			$data['keyword'] 			= $keyword;
			//Delivery Status
			$data['delivery_statuses']	= $this->dr_model->get_param_delivery_status();

			$this->_load_transaction_list($data);
			$this->load->view('tabs/'.PORTAL_TAB_DR, $data);
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

	public function get_dr_list()
	{
		$flag 				= 0;
		$total_records		= 0;
		$display_records 	= 0;
		$table_data 		= array();

		try
		{
			$wheres				= array();
			$params				= get_params();
				
			if( ! EMPTY($params['keyword'])) 
			{ 
				$params['ref_no'] = $params['keyword'];
				$params['action'] = 'filter';
			}	

			$checkbox 			= '';
			$actions			= "";

			$module_code		= decrypt_id($params['encoded_module_code']);
			$tab_module			= decrypt_id($params['encoded_tab_module']);

			//$scope_details 		= get_scope_details($this->main_module, NULL, TRUE);
			$scope_details 		= get_scope_details($tab_module);
			
			$wheres['ag_codes']	= $this->get_ag_code_per_module($module_code);
			$wheres['workflow_ids']	= $this->get_workflow_per_module_tab($module_code, $tab_module);

			$total_records		= $this->dr_model->get_dr_list($wheres, NULL, $scope_details['having']);
			$records_info 		= $this->dr_model->get_dr_list($wheres, $params, $scope_details['having']);

			$records			= $records_info['records'];
			$display_records	= $records_info['display_records'];

			foreach($records as $record)
			{
				//if($record['dr_status'] != DR_FOR_CANCELLATION)
				if( ! in_array($record['dr_status'], [DR_FOR_CANCELLATION, DR_CANCELLED, DR_SOA_APPROVED, DR_FOR_SOA]))
				{
					/* if($record['dr_status'] != DR_CANCELLED)*/
						$checkbox = "<input class='ind_checkbox_dr' type='checkbox' name='dr_checkbox[]' id='ind_checkbox_dr".$record['dr_gr_id']."' value='".$record['dr_gr_id']."' /><label for='ind_checkbox_dr".$record['dr_gr_id']."'></label>";
					
				}
				else
				{
					if(check_permission($tab_module, ACTION_APPROVE) AND $record['dr_status'] == DR_FOR_CANCELLATION)
					{ //Approve
						$approve_action = "modal_approval_init('".$record['dr_gr_id']."','Approve')";
						$actions.= "<a href='javascript:;' data-target='modal_approval' onclick=".$approve_action." class='tooltipped approve' data-tooltip='approve' data-position='bottom' data-delay='50' onclick=''><i class='material-icons'>check</i></a>";

						//Disapprove
						$disapprove_action = "modal_disapproval_init('".$record['dr_gr_id']."','Disapprove')";
						$actions.= "<a href='javascript:;' data-target='modal_disapproval' onclick=".$disapprove_action." class='tooltipped disapprove' data-tooltip='disapprove' data-position='bottom' data-delay='50'><i class='material-icons'>clear</i></a>";
					}
				}
				
				$table_data[]	= array(
						$checkbox,
						$record['date'],
						$record['business_center'],
						$record['ref_no'],
						$record['vendor'],
						$record['official_store_name'],
						$record['soa_num'],
						$record['soa_status'],
						$record['dr_remarks'],
						$record['dr_status_name'],
						"<div class='table-actions'>" . $actions . "</div>"
				);

				$actions = '';
				$checkbox = '';
			}

			$flag	= 1;
			$msg	= "";
		}
		catch(PDOException $e)
		{
			$e->getMessage();
			$msg = $this->get_user_message($e);
		}
		catch(Exception $e)
		{
			$msg = $this->rlog_error($e, TRUE);
		}

		echo json_encode(
			array(
				'aaData'				=> $table_data,
				'sEcho'					=> intval($params['sEcho']),
				'iTotalRecords'			=> $total_records,
				'iTotalDisplayRecords'	=> $display_records,
				'flag'					=> $flag,
				'msg'					=> $msg
			)
		);
	}

	public function modal_disapproval($dr_gr_id = NULL)
	{
		$where 		= array('dr_gr_id' => $dr_gr_id);
		$dr_info 	= $this->dr_model->get_delivery_goods_receipts($where);

		$data['dr_info'] = $dr_info;
		$this->load->view('modals/dr_approval', $data);
	}

	public function modal_approval($dr_gr_id = NULL)
	{
		$where 		= array('dr_gr_id' => $dr_gr_id);
		$dr_info 	= $this->dr_model->get_delivery_goods_receipts($where);

		$data['dr_info'] = $dr_info;
		$this->load->view('modals/dr_approval', $data);
	}
}