<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pr_model extends Portal_Model
{
    public $tbl_pria_workflows;
    public $tbl_pr;
    public $tbl_pria_tab_modules;
    public $tbl_core_users;
    public $tbl_core_user_roles;
    public $tbl_param_account_groups;
    public $tbl_pr_cost_center;
    public $tbl_param_gl_accounts;
    public $tbl_pr_projects;
    public $tbl_boq;
    public $tbl_projects;

    public function __construct()
    {
        parent::__construct();

        $this->tbl_pr                     = parent::PORTAL_TABLE_PURCHASE_REQUISITIONS;

        $this->tbl_pr_projects            = parent::PORTAL_TABLE_PURCHASE_REQUISITION_PROJECTS;
        $this->tbl_pr_cost_center         = parent::PORTAL_TABLE_PURCHASE_REQUEST_COST_CENTERS;

        $this->tbl_boq                    = parent::PORTAL_TABLE_PRIA_BOQ;
        $this->tbl_boq_pr                 = parent::PORTAL_TABLE_PRIA_BOQ_PR;
        $this->tbl_boq_asset_codes        = parent::PORTAL_TABLE_PRIA_BOQ_ASSET;

        $this->tbl_projects               = parent::PORTAL_TABLE_PROJECTS;

        $this->tbl_sites                  = parent::PORTAL_TABLE_SITES;

        $this->tbl_pria_tab_modules       = parent::PORTAL_TABLE_PRIA_TAB_MODULE;
        $this->tbl_pria_workflows         = parent::PORTAL_TABLE_PRIA_WORKFLOWS;

        $this->tbl_core_users             = parent::CORE_TABLE_USERS;
        $this->tbl_core_user_roles        = parent::CORE_USER_ROLES;

        $this->tbl_param_account_groups   = parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
        $this->tbl_param_gl_accounts      = parent::PORTAL_TABLE_PARAM_GL_ACCOUNTS;

    }

    /** SELECT FUNCTIONS */
    public function get_prs($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pr, TRUE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_ppr($where=array(), $fields=array('*'), $order=array(), $group=array(), $limit='')
    {
        try
        {
            return $this->select_data($fields, $this->tbl_pr, FALSE, $where, $order, $group, $limit);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    // public function get_tab_module($where=array(), $fields=array('*'), $order=array(), $multiple = FALSE)
    // {
    //     try
    //     {
    //         return $this->select_data($fields, $this->tbl_pria_tab_modules, $multiple, $where, $order);
    //     }
    //     catch(PDOException $e)
    //     {
    //         throw $e;
    //     }
    // }

    public function get_pr_requested_users()
    {

        try
        {

            $fields     = array(
                'A.fname', 'A.lname', 'A.email', 'A.mname', 'A.ext_name',
                'A.username', 'A.nickname', 'A.job_title', 'A.contact_no', 'A.mobile_no'
            );

            $decrypt    = aes_crypt($fields, FALSE);

            $query =
            '
                SELECT
                    A.user_id,
                    A.location_code,
                    A.org_code,
                    A.password,
                    A.salt,
                    A.reset_salt,
                    A.gender,
                    A.photo,
                    A.logged_in_flag,
                    A.mail_flag,
                    A.contact_flag,
                    A.status,
                    A.reason,
                    A.attempts,
                    A.initial_flag,
                    A.pw_email_flag,
                    A.last_logged_in_date,
                    A.built_in_flag,
                    ' . $decrypt . '

                FROM ' . $this->tbl_core_users . ' A
                JOIN ' . $this->tbl_core_user_roles . ' B ON A.user_id = B.user_id


            ';
                //HAVING B.role_code = 'BC_ADMIN';
            return $this->query($query, array(), TRUE, TRUE);


        }
        catch (PDOException $e)
        {
            throw $e;
        }
    }

    public function get_account_group_names($available_ag_codes)
    {
        try
        {
            $query =<<<EOS
            SELECT
                * FROM $this->tbl_param_account_groups
            WHERE account_group_code IN (?)
EOS;
            return $this->query($query, array($available_ag_codes));
        }
        catch (PDOException $e)
        {
            throw $e;
        }
    }

    /** INSERT FUNCTIONS */
    /**
     * @Author: Christian Aquino
     * @Date: 2019-06-29 14:13:05
     * @Desc:
     * @ReferencedBy:  Upload_soa.php
     */
    public function insert_pr(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_pr, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_pr_cost_center(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_pr_cost_center, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function insert_pr_project(array $fields, $return=TRUE)
    {
        try
        {
            return $this->insert_data($this->tbl_pr_projects, $fields, $return);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    /** UPDATE FUNCTIONS */
    /**
     * @Author: Christian Aquino
     * @Date: 2019-05-29 14:13:05
     * @Desc:
     * @ReferencedBy:  Pr.php
     */
    public function update_pr(array $where, array $fields)
    {
        try
        {
            $this->update_data($this->tbl_pr, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function update_workflow($fields, $where)
    {
        try
        {
            return $this->update_data($this->tbl_pria_workflows, $fields, $where);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_pr_cost_centers($pr_id)
    {
        try
        {
            $values = [$pr_id];
            $query  =<<<EOS
                SELECT
                    pr_id,
                    GROUP_CONCAT(A.cost_center_code SEPARATOR ', ') cost_centers,
                    GROUP_CONCAT(B.official_store_name SEPARATOR ', ') cost_center_names
                FROM $this->tbl_pr_cost_center A
                LEFT JOIN $this->tbl_sites B ON A.cost_center_code = B.cost_center_code
                WHERE A.pr_id = ?
                GROUP BY A.pr_id
EOS;
            return $this->query($query, $values, TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_pr_details($pr_id, $ag_code = NULL)
    {
        try
        {
            $select = $join = $group_by = "";
            $values = [$pr_id];

            $boq_url   = base_url().PORTAL_TRANSACTIONS.'/'.PORTAL_CONTRACTOR.'?keyword=%s#tab_boq';

            if($ag_code == AG_CONTRACTORS)
            {
                $select .= "
                    , B.boq_id
                    , CONCAT('<a href=', REPLACE('$boq_url', '%s', C.boq_code), '>', C.boq_code, '</a>') boq_code
                    , IFNULL(E.project_code, 'N/A') project_ref
                    , G.official_store_name
                    , G.org_code
                ";

                $join   .=<<<EOS
                    LEFT JOIN $this->tbl_boq_pr B ON A.pr_id = B.pr_id
                    LEFT JOIN $this->tbl_boq C ON B.boq_id = C.boq_id
                    LEFT JOIN $this->tbl_pr_projects D ON A.pr_id = D.pr_id
                    LEFT JOIN $this->tbl_projects E ON D.project_id = E.project_id
                    LEFT JOIN $this->tbl_sites G ON C.site_id = G.site_id
EOS;
            }
            else
            {
                $select .= ", B.gl_account_name";
                $join   .=<<<EOS
                    LEFT JOIN $this->tbl_param_gl_accounts B ON A.gl_account_code = B.gl_account_code
EOS;
            }

            $query  =<<<EOS
                SELECT
                    A.*
                    $select
                FROM $this->tbl_pr A
                $join
                WHERE A.pr_id = ?
                $group_by
EOS;

            return $this->query($query, $values, TRUE, FALSE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_pr_per_boq($boq_id)
    {
        try
        {
            $query  =<<<EOS
            SELECT pr.pr_id value, pr.pr_num text, pr.pr_id, pr.pr_num
            FROM $this->tbl_pr pr
            JOIN $this->tbl_boq_pr bp ON bp.pr_id = pr.pr_id
            WHERE bp.boq_id = ?
EOS;
            return $this->query($query, [$boq_id], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_prs_search($keyword)
    {
        try
        {
            $query  =<<<EOS
                SELECT
                    A.*
                FROM $this->tbl_pr A
                WHERE A.pr_num LIKE '%$keyword%'
EOS;

            return $this->query($query, [], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_bc_boq($org_code)
    {
        try
        {
            $query  =<<<EOS
                SELECT a.*
                FROM $this->tbl_boq a
                JOIN $this->tbl_sites b ON a.site_id = b.site_id
                WHERE a.status_code = ? AND b.org_code = ?
EOS;
            return $this->query($query, [STATUS_COMPLETED, $org_code], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public function get_bc_proj($org_code)
    {
        try
        {
            $query  =<<<EOS
                SELECT a.*
                FROM $this->tbl_projects a
                JOIN $this->tbl_sites b ON a.site_id = b.site_id
                WHERE b.org_code = ?
EOS;
            return $this->query($query, [$org_code], TRUE, TRUE);
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}