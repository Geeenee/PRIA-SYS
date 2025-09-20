<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overview extends Transaction_Controller 
{
	public function __construct()
	{
        parent::__construct();
        
        $this->load->model('overview_model', 'om');
	}

    public function index($enc_module, $enc_tab_module)
    {
        try
        {
            $flag            = ERROR;
            $footer          = [];
            $display_scroll  = FALSE;
            $module_code     = decrypt_id($enc_module);
            $tab_module_code = decrypt_id($enc_tab_module);

            $scope_details  = get_scope_details($tab_module_code, '', TRUE);

            $created_from   = date(FORMAT_DB_DATETIME);
           
            $overview_list  = $this->om->get_overview_list($module_code, 0, SYS_SETTING_DISPLAY_LIST_NO, $scope_details['having'], $created_from);
         
            //$overview_num 	= $this->om->get_overviews(['module_code' => $module_code], ['1 as cnt']);
            $overview_num   = $this->om->get_overview_list($module_code, 0, SYS_SETTING_DISPLAY_LIST_NO, $scope_details['having'], $created_from, FALSE, TRUE);
            
            if($overview_num['count'] > SYS_SETTING_DISPLAY_LIST_NO)
			{

				$footer 	 = [
					'container' => '.list-timeline',
					'path'		=>  base_url().'transactions/tabs/overview/page/'.$created_from.'/'.base64_url_encode($module_code).'/',
					'append'	=> 'li',
					'last_page' => '#scroll-next-page'
                ]; 
                
                $display_scroll = TRUE;
			}
            
            $display	   = ['display_scroll' => ($display_scroll) ? TRUE : FALSE];
            $footer 	   = array_merge($footer, $display); 

            $list          = $this->load->view(PORTAL_TRANSACTIONS.'/overview_list', ['overview_list' => $overview_list, 'initial' => TRUE], TRUE);
            
            $this->load->view(PORTAL_TRANSACTIONS.'/overview_tab', ['html' => $list]);

            $this->load->view('common/tabs/tab_content_footer', $footer);

            $flag 	       = SUCCESS;
            $msg           = $this->lang->line('data_saved');
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

	public function page($created_from, $enc_module, $page_num=1)
	{
		try
		{
            $scope_details  = get_scope_details($this->main_module, '', TRUE);

            $module_code    = base64_url_decode($enc_module);
			$from           = ($page_num - 1) * SYS_SETTING_DISPLAY_LIST_NO;
            
            $overview_list  = $this->om->get_overview_list($module_code, $from, SYS_SETTING_DISPLAY_LIST_NO, $scope_details['having'], $created_from);

            $prev_list       = $this->om->get_overview_list($module_code, $from - 2, 1, $scope_details['having'], $created_from, TRUE);

            $prev_date       = $prev_list['created_date'];

			//Get the next batch of internal orders
			$from            = $page_num * SYS_SETTING_DISPLAY_LIST_NO;
            $next_ovr        = $this->om->get_overview_list($module_code, $from, SYS_SETTING_DISPLAY_LIST_NO, '', $created_from, TRUE);

			//Determines if this is the last page.
            $last_page 	 	= ( EMPTY($next_ovr)) ? TRUE : FALSE;
            
            $this->load->view(PORTAL_TRANSACTIONS.'/overview_list', ['overview_list' => $overview_list, 'last_page' => $last_page, 'prev_date' => $prev_date, 'initial' => FALSE]);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}
