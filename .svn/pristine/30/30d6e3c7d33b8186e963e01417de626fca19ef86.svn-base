<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aging_report_model extends Portal_Model
{
  private $tbl_payment_apvs;
  private $tbl_payments;
  private $tbl_param_account_groups;
  private $tbl_organizations;
  private $tbl_param_apv_status;
  private $tbl_pria_workflows;
  private $tbl_vendors;

  public function __construct()
  {
    parent::__construct();
    $this->tbl_payment_apvs           = parent::PORTAL_TABLE_PAYMENT_APVS;
    $this->tbl_payments               = parent::PORTAL_TABLE_PAYMENTS;
    $this->tbl_param_account_groups   = parent::PORTAL_TABLE_PARAM_ACCOUNT_GROUPS;
    $this->tbl_organizations          = parent::PORTAL_TABLE_ORGANIZATIONS;
    $this->tbl_param_apv_status       = parent::PORTAL_TABLE_PARAM_APV_STATUS;
    $this->tbl_pria_workflows         = parent::PORTAL_TABLE_PRIA_WORKFLOWS;
    $this->tbl_vendors                = parent::PORTAL_TABLE_VENDORS;

    $this->tbl_internal_orders        = parent::PORTAL_TABLE_INTERNAL_ORDERS;
    $this->tbl_purchase_orders        = parent::PORTAL_TABLE_PURCHASE_ORDERS;
    $this->tbl_soa                    = parent::PORTAL_TABLE_SOA;
    $this->tbl_contracts              = parent::PORTAL_TABLE_CONTRACTS;
  }

  public function get_aging_report_list($searches = NULL, $scope_details = [], $params = NULL)
  {
    try
    {
      $val = $filters = $filter_params = array();
      $and_where = $where = $order = $group_by = $limit = "";

      $val = [
          APV_REF_TYPE_CODE_IO, APV_REF_TYPE_CODE_PO, APV_REF_TYPE_CODE_SOA, APV_REF_TYPE_CODE_CONTRACTS,
          APV_REF_TYPE_CODE_IO, APV_REF_TYPE_CODE_PO, APV_REF_TYPE_CODE_SOA, APV_REF_TYPE_CODE_CONTRACTS, REL_TO_VENDOR,
          APV_REF_TYPE_CODE_IO, APV_REF_TYPE_CODE_PO, APV_REF_TYPE_CODE_SOA, APV_REF_TYPE_CODE_CONTRACTS
      ];

      if(!EMPTY($scope_details['scope'])){
        if($scope_details['scope'] = SCOPE_REGION){
          $having = $scope_details['having'];
        }  
      }

      //Account Group
      if($searches['account_group']){
        $and_where .= " AND account_group_code = ?";
        $val[] = $searches["account_group"];
      }

      //Business Center
      if($searches['business_center']){
        $and_where .= " AND org_code = ?";
        $val[] = $searches["business_center"];
      }

      // DATED
      if(!EMPTY($searches["date_from"]) AND !EMPTY($searches["date_to"])){
        $and_where .= " AND (DATE(reference_date) BETWEEN DATE(?) AND DATE(?) )";
        $val[] = $searches["date_from"];
        $val[] = $searches["date_to"];
      }
      
      if(!EMPTY($searches["date_from"]) AND EMPTY($searches["date_to"])){
        $and_where .= " AND DATE(reference_date) >= DATE(?)";
        $val[] = $searches["date_from"];
      }
      
      if(EMPTY($searches["date_from"]) AND !EMPTY($searches["date_to"])){
        $and_where .= " AND (DATE(reference_date) BETWEEN CURDATE() AND DATE(?) )";
        $val[] = $searches["date_to"];
      }

      //APV status
      if($searches['apv_status']){
        $and_where .= " AND apv_status_code = ?";
        $val[] = $searches["apv_status"];
      }

      if($params === NULL)
      {
        $select_fields  = 'COUNT(DISTINCT payment_id) total';
      }
      else
      {
        $select_fields = "
            SQL_CALC_FOUND_ROWS
            payment_id
            account_group_code,
            account_group_name,
            org_code,
            org_name,
            vendor_code,
            vendor_name,
            reference_num,
            reference_date,
            IFNULL(apv_num,'N/A') apv_num,
            IFNULL(apv_date,'N/A') apv_date,
            IFNULL(cv_num,'N/A') cv_num,
            IFNULL(due_date,'N/A') due_date,
            IFNULL(days_delayed_convert,'N/A') days_delayed_convert,
            apv_status_code apv_status_code,
            apv_status_name apv_status_name,
            timeliness timeliness
        ";
        
        $filters  = array(
            "account_group_name",
            "org_name",
            "vendor_name",
            "reference_num",
            "reference_date",
            "apv_num",
            "apv_date",
            "cv_num",
            "due_date",
            "days_delayed_convert",
            "apv_status_name",
            "timeliness"
        );
        
        $orders = array(
            "account_group_name",
            "org_name",
            "vendor_name",
            "reference_num",
            "reference_date",
            "apv_num",
            "apv_date",
            "cv_num",
            "due_date",
            "days_delayed_convert",
            "apv_status_name",
            "timeliness"
        );

        $filter = $this->filtering($filters, $params, TRUE);
        $order  = $this->ordering($orders, $params);
        $limit  = $this->paging($params);

        $where  = $filter["search_str"];
        $filter_params  = $filter["search_params"];

        // $group_by = "GROUP BY payment_id";
      }

      $query  = <<<EOS

      SELECT
        $select_fields
      FROM
      (
        SELECT
          payment_id,
          account_group_code,
          account_group_name,
          org_code,
          org_name,
          vendor_code,
          vendor_name,
          reference_num,
          reference_date,
          apv_num,
          apv_date,
          cv_num,
          payment_due_days,
          apv_status_code,
          apv_status_name,
          created_date,
          modified_date,
          due_date_reference,
          DATE_ADD(due_date_reference, INTERVAL payment_due_days DAY) due_date,
          released_date,
          DATEDIFF(IFNULL(released_date, CURDATE()), DATE_ADD(due_date_reference, INTERVAL payment_due_days DAY)) days_delayed,
          GREATEST(DATEDIFF(IFNULL(released_date, CURDATE()), DATE_ADD(due_date_reference, INTERVAL payment_due_days DAY)), 0) days_delayed_convert,
          IF(DATEDIFF(IFNULL(released_date, CURDATE()), DATE_ADD(due_date_reference, INTERVAL payment_due_days DAY)) > 0, 'Delayed', 'On-Time') timeliness
        FROM (
          SELECT
            a.payment_id,
            c.account_group_code,
            c.account_group_name,
            b.org_code,
            d.name org_name,
            b.vendor_code,
            f.vendor_name,
            b.reference_num,
            CASE
              WHEN b.reference_type_code = ? THEN DATE(g.created_date)
              WHEN b.reference_type_code = ? THEN DATE(h.po_date)
              WHEN b.reference_type_code = ? THEN DATE(i.soa_date)
              WHEN b.reference_type_code = ? THEN DATE(j.date_from)
              ELSE NULL
            END reference_date,
            a.apv_num,
            IF(a.apv_date IS NOT NULL, DATE(a.apv_date), NULL) apv_date,
            a.cv_num,
            c.payment_due_days,
            a.apv_status_code,
            e.apv_status_name,
            IF(a.created_date IS NOT NULL, DATE(a.created_date), NULL) created_date,
            IF(a.modified_date IS NOT NULL, DATE(a.modified_date), NULL) modified_date,
            DATE(IFNULL(a.apv_date,
              CASE
                WHEN b.reference_type_code = ? THEN DATE(g.created_date)
                WHEN b.reference_type_code = ? THEN DATE(h.po_date)
                WHEN b.reference_type_code = ? THEN DATE(i.soa_date)
                WHEN b.reference_type_code = ? THEN DATE(j.date_from)
                ELSE NULL
              END)
            ) due_date_reference,
            IF(a.apv_status_code = ?,
              DATE(IFNULL(a.modified_date, a.created_date)),
              NULL
            ) released_date
          FROM $this->tbl_payment_apvs a
          JOIN $this->tbl_payments b on a.payment_id = b.payment_id
          JOIN $this->tbl_param_account_groups c on b.account_group_code = c.account_group_code
          JOIN $this->tbl_organizations d on b.org_code = d.org_code
          JOIN $this->tbl_param_apv_status e on a.apv_status_code = e.apv_status_id
          JOIN $this->tbl_vendors f on b.vendor_code = f.vendor_code
          LEFT JOIN $this->tbl_internal_orders g ON b.reference_type_code = ? AND b.reference_id = g.io_id
          LEFT JOIN $this->tbl_purchase_orders h ON b.reference_type_code = ? AND b.reference_id = h.po_id
          LEFT JOIN $this->tbl_soa i ON b.reference_type_code = ? AND b.reference_id = i.soa_id
          LEFT JOIN $this->tbl_contracts j ON b.reference_type_code = ? AND b.reference_id = j.contract_id
        ) A
      ) B
      WHERE 1 = 1
      $and_where
      $where
      $group_by
      $order
      $having
      $limit
EOS;

      $val = array_merge($val, $filter_params);

      if($params === NULL)
      {
        $total = $this->query($query, $val, TRUE, FALSE);

        return $total['total'];
      }
      else
      {
        return array(
            'records'     => $this->query($query, $val),
            'display_records' => $this->_get_display_records()
        );
      }

    }
    catch(PDOException $e)
    {
        throw $e;
    }
  }

  public function _get_display_records()
  {
    try
    {
      $query = "SELECT FOUND_ROWS() cnt";

      $count = $this->query($query, NULL, TRUE, FALSE);

      return $count['cnt'];
    }
    catch (PDOException $e)
    {
      throw $e;
    }
  }

  public function get_total_delayed_apvs($scope_details)
  {
    try
    {
      $val = array();
      $and_where = $where = "";

      $val = [
          APV_REF_TYPE_CODE_IO, APV_REF_TYPE_CODE_PO, APV_REF_TYPE_CODE_SOA, APV_REF_TYPE_CODE_CONTRACTS, REL_TO_VENDOR,
          APV_REF_TYPE_CODE_IO, APV_REF_TYPE_CODE_PO, APV_REF_TYPE_CODE_SOA, APV_REF_TYPE_CODE_CONTRACTS, REL_TO_VENDOR
      ];

      if(!EMPTY($scope_details['scope'])){
        if($scope_details['scope'] = SCOPE_REGION){
          $having = $scope_details['having'];
        }  
      }

      $query  = <<<EOS
        SELECT
          COUNT(payment_apv_id) total
        FROM (
          SELECT
            a.payment_apv_id,
            a.apv_status_code,
            c.payment_due_days,
            DATE(IFNULL(a.apv_date,
              CASE
                WHEN b.reference_type_code = ? THEN DATE(g.created_date)
                WHEN b.reference_type_code = ? THEN DATE(h.po_date)
                WHEN b.reference_type_code = ? THEN DATE(i.soa_date)
                WHEN b.reference_type_code = ? THEN DATE(j.date_from)
                ELSE NULL
              END)
            ) due_date_reference,
            IF(a.apv_status_code = ?,
              DATE(IFNULL(a.modified_date, a.created_date)),
              NULL
            ) released_date
          FROM $this->tbl_payment_apvs a
          JOIN $this->tbl_payments b on a.payment_id = b.payment_id
          JOIN $this->tbl_param_account_groups c on b.account_group_code = c.account_group_code
          JOIN $this->tbl_organizations d on b.org_code = d.org_code
          JOIN $this->tbl_param_apv_status e on a.apv_status_code = e.apv_status_id
          JOIN $this->tbl_vendors f on b.vendor_code = f.vendor_code
          LEFT JOIN $this->tbl_internal_orders g ON b.reference_type_code = ? AND b.reference_id = g.io_id
          LEFT JOIN $this->tbl_purchase_orders h ON b.reference_type_code = ? AND b.reference_id = h.po_id
          LEFT JOIN $this->tbl_soa i ON b.reference_type_code = ? AND b.reference_id = i.soa_id
          LEFT JOIN $this->tbl_contracts j ON b.reference_type_code = ? AND b.reference_id = j.contract_id
        ) A
        WHERE (apv_status_code != ? OR apv_status_code IS NULL)
        AND GREATEST(DATEDIFF(IFNULL(released_date, CURDATE()), DATE_ADD(due_date_reference, INTERVAL payment_due_days DAY)), 0) > 1
        $having
EOS;

      $total = $this->query($query, $val, TRUE, FALSE);

      return $total['total'];
    }
    catch(PDOException $e)
    {
      throw $e;
    }
  }
}