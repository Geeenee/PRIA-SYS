<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pria_overview {
    
    protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        
        $this->CI->load->model('pria_overview_model', 'pom');

        $this->CI->load->model(PORTAL_TRANSACTIONS.'/task_model', 'tm_model');
    }
    
    public function log_overview($module_code, $type, array $extra_data = [])
    {
        try
        {
            $overview_html = $this->_construct_html($type, $extra_data);

            $data = [
                'overview_html'         => $overview_html,
                'account_group_code'    => $extra_data['account_group_code'],
                'type'                  => $type,
                'reference'             => $extra_data['reference'],
                'module_code'           => $module_code,
                'core_workflow_id'      => ( ! EMPTY($extra_data['core_workflow_id'])) ? $extra_data['core_workflow_id'] : NULL,
                'created_by'            => $extra_data['created_by'],
                'created_date'          => (ISSET($extra_data['created_date'])) ? $extra_data['created_date'] : date(FORMAT_DB_DATETIME)
            ]; 
            
            $this->CI->pom->insert_overview($module_code, $data);
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


    public function delete_log($where)
    {
        try
        {
            $this->CI->pom->delete_overview($where);
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

    private function _construct_html($type, $data)
    {
        try
        {
            $action       = '';
            $detail       = '';
            
            $subtitle     = '';
            $name         = $this->CI->tm_model->get_user_fullname($data['created_by']);
            $path_user    = 'javascript:;';

            switch($type)
            {
                case OVERVIEW_TYPE_CHANGE_TASK_STATUS :
                   $action    = 'changed the status of task to';
                   $detail    = $data['task_status'];

                  // $subtitle = $this->_create_subtitle('task', $data);
                break;

                case OVERVIEW_TYPE_ADD_TASK_COMMENT :
                    $comment  = strip_tags($data['comment']);
                    $action   = 'added a comment';
                    $detail   = '<span class="purple-text">'.$comment.'</span>';

                   // $subtitle = $this->_create_subtitle('task', $data);
                break;

                case OVERVIEW_TYPE_EDIT_TASK_COMMENT :
                    $comment  = strip_tags($data['comment']);
                    $action   = 'edited a comment';
                    $detail   = '<span class="purple-text">'.$comment.'</span>';

                    //$subtitle = $this->_create_subtitle('task', $data);
                break;

                case OVERVIEW_TYPE_ADD_TASK_ATTACHMENT :
                    $filename   = strip_tags($data['filename']);
                    $action     = 'uploaded a file';
                    $detail     = '<span class="purple-text">'.$filename.'</span>';

                  //  $subtitle = $this->_create_subtitle('task', $data);
                break; 

                case OVERVIEW_TYPE_ADD_QUICK_ADD :
                    $transaction    = strip_tags($data['transaction']);
                    $action         = 'added a transaction';
                    $detail         = '<span class="purple-text">'.$transaction.'</span>';
                break; 

                case OVERVIEW_TYPE_UPLOAD_BATCH_FILES :
                case OVERVIEW_TYPE_ADD_TRANSACTION :
                    $transaction    = $data['transaction_num'];
                    $action         = $data['transaction_msg'];
                    $detail         = '<span class="purple-text">'.$transaction.'</span>';
                break; 
            }

            $other_details = $this->_get_other_details($type, $data);

            $subtitle      = $other_details['subtitle'];
            $link          = $other_details['link'];

            //Removed the ""
            if( ! EMPTY($detail))
                $detail =  '<span class="detail">"'.$detail.'"</span>';

            $tpl = <<<EOS
                <a href="$link" target="_blank">
                    <div class="tl-title black-text">
                        <span class="red-text">$name</span>

                        <span class="action">$action</span>  

                        $detail
                    </div>

                    <div class="tl-subtitle black-text">
                        $subtitle
                    </div>
                </a>    
EOS;


            return $tpl;
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

    //Gets the link and subtitle
    private function _get_other_details($type, $data)
    {
        switch($type)
        {
            case OVERVIEW_TYPE_EDIT_TASK_COMMENT:
            case OVERVIEW_TYPE_ADD_TASK_COMMENT:
                $link     = base_url().PORTAL_TRANSACTIONS.'/'.$data['controller'].'?t='.base64_url_encode($data['pria_task_id']).'#comment-'.$data['pria_task_comment_id'];  
                    
                $subtitle =<<<EOS
                {$data['task_name']}
                
                <span class="red-text font-thin">(LIST: {$data['reference_num']})</span>
EOS;
            break;
            
            case OVERVIEW_TYPE_ADD_TASK_ATTACHMENT:
                $link     = base_url().PORTAL_TRANSACTIONS.'/'.$data['controller'].'?t='.base64_url_encode($data['pria_task_id']).'#attachment-'.$data['pria_task_attachment_id'];
                    
                $subtitle =<<<EOS
                {$data['task_name']}
                
                <span class="red-text font-thin">(LIST: {$data['reference_num']})</span>
EOS;
            break;

            case OVERVIEW_TYPE_CHANGE_TASK_STATUS:
                $link     = base_url().PORTAL_TRANSACTIONS.'/'.$data['controller'].'?t='.base64_url_encode($data['pria_task_id']);
                
                $subtitle =<<<EOS
                {$data['task_name']}
                
                <span class="red-text font-thin">(LIST: {$data['reference_num']})</span>
EOS;
            break;
            
            case OVERVIEW_TYPE_ADD_TRANSACTION:
                $parent_mod_dets    = $this->CI->tm_model->get_module(['module_code' => $data['parent_module_code']], ['link']);
                $tab_mod_dets       = $this->CI->tm_model->get_module(['module_code' => $data['tab_module_code']], ['module_name']);
                
                $pmd                = explode('/', $parent_mod_dets['link']);

                $parent_mod_name    = str_replace(' ', '_', strtolower($pmd[1]));
                $tab_mod_name       = 'tab_'.str_replace(' ', '_', strtolower($tab_mod_dets['module_name']));
                
                $link     = base_url().PORTAL_TRANSACTIONS.'/'.$parent_mod_name.'?keyword='.$data['keyword'].'#'.$tab_mod_name;
            break;

            case OVERVIEW_TYPE_UPLOAD_BATCH_FILES:
                $link     = base_url().PORTAL_TRANSACTIONS.'/'.$data['controller'].'?t='.base64_url_encode($data['pria_task_id']);
            break;
        }

        return [
            'link'      => ISSET($link) ? $link : '#',
            'subtitle'  => ISSET($subtitle) ? $subtitle : ''
        ];
    }

/*     private function _create_subtitle($type, $data)
    {
        switch($type)
        {
            case 'task':
                $task_path =  base_url().PORTAL_TRANSACTIONS.'/'.$data['controller'].'?t='.base64_url_encode($data['pria_task_id']);

                return <<<EOS
                    {$data['task_name']}

                    <a href="$task_path">(LIST: {$data['reference_num']})</a>
EOS;
            break;
            
            default: 
                return '';
        }
    } */
}    