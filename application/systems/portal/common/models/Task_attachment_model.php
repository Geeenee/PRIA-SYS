<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_attachment_model extends Portal_Model{
	
	public function __construct()
	{
		parent::__construct();
		
	}

	//Select function
	public function get_doc_info($table_name, $fields=array('*'), $multiple = FALSE, $where = array('*'), $order_arr = array())
	{
		try
		{
			return $this->select_data($fields, $table_name, $multiple, $where, $order_arr);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

	public function get_max_version($table_name = NULL, $where = NULL)
	{
		try{

			$val = NULL;
			
			$fields = array('MAX(version) max_version');
			
			$ret = $this->select_data($fields, $table_name, FALSE, $where);

			if($ret['max_version']){
				return $ret['max_version'] + 1;
			}else{
				return 1;
			}

		}catch(PDOException $e){
			throw $e;
		}
	}


	//Insert functions
	public function insert_doc_info($table_name,array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($table_name, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    //Update functions
    public function update_doc_info($table_name, $fields, $where)
	{
		try
		{
			return $this->update_data($table_name, $fields, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}
}