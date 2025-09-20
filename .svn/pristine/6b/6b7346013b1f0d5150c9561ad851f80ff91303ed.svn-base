<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents_model extends Portal_Model
{
    private $tbl_pria_documents;
    private $tbl_pria_document_versions;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_pria_documents            = Portal_Model::PORTAL_TABLE_DOCUMENTS;
        $this->tbl_pria_document_versions    = Portal_Model::PORTAL_TABLE_DOCUMENT_VERSIONS;
    }

    /** SELECT FUNCTIONS */
    public function get_document($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_documents, FALSE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
    }

    public function get_documents($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_documents, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
    }


    public function get_max_version($where, $fields=array('*'))
    {
        try
        {

            $ret = $this->select_data($fields, $this->tbl_pria_documents, FALSE, $where);

            if($ret['max_version']){
                return $ret['max_version'] + 1;
            }else{
                return 1;
            }
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

	public function get_document_versions($where, $fields=array('*'))
	{
		try
		{
			return $this->select_data($fields, $this->tbl_pria_document_versions, TRUE, $where);
		}
		catch(PDOException $e)
		{
			throw $e;
		}
	}

    /** SELECT FUNCTIONS */
    public function insert_document($fields)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_documents, $fields, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_document_versions($fields)
    {
        try
        {
            return $this->insert_data($this->tbl_pria_document_versions, $fields, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** UPDATE FUNCTIONS */
    public function update_document($fields, $where)
    {
        try
        {
            return $this->update_data($this->tbl_pria_documents, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** DELETE FUNCTIONS */
    public function delete_document($where)
    {
        try
        {
            return $this->delete_data($this->tbl_pria_documents, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}