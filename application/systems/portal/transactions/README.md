
# Portal Transactions Module – Workflow Mapping

This folder contains the source code for the Transactions module of the Portal system in the PRIA application. Each workflow is organized into its own MVC structure.

## Workflows & Their MVC Structure

### 1. BOQ (Bill of Quantities)
- **Controllers:** `boq/Encode_asset.php`, `boq/Indicate_budget.php`, `boq/Indicate_contractor.php`, etc.
- **Model:** `boq/Boq_model.php`
- **Views:** `tasks/boq/boq_asset_codes.php`, `tasks/boq/encode_asset.php`, etc.

### 2. CDI Payment
- **Controllers:** `cdi_payment/Con_avail_rel_payment.php`, `cdi_payment/Con_mall_charges.php`, etc.
- **Model:** `cdi/Cdi_payments_model.php`
- **Views:** `tasks/cdi_payment/con_avail_rel_payment.php`, `tasks/cdi_payment/upload_soa.php`, etc.

### 3. Contract
- **Controller:** `contract/Contract.php`
- **Model:** `contracts/Contracts_model.php`
- **Views:** `tabs/contracts.php`

### 4. Delivery Receipt (DR)
- **Controllers:** `dr/Dr.php`, `dr/Encode_delivery_receipt.php`, etc.
- **Model:** `dr/Dr_model.php`, `dr/Delivery_goods_model.php`
- **Views:** `tasks/dr/encode_delivery_receipt.php`, `tasks/dr/transmit_dr.php`, `tabs/dr.php`

### 5. Files
- **Controllers:** `Files.php`, `files/Files.php`
- **Models:** `files/Files_model.php`, `files/File_list_model.php`
- **Views:** `tasks/files/file_list.php`

### 6. Internal Orders (IO)
- **Controllers:** `io/Doc_dr.php`, `io/Doc_gr.php`, etc.
- **Model:** `io/Internal_order_model.php`
- **Views:** `tasks/io/encode_audit_score.php`, `tasks/io/upload_doc_dr.php`, etc.

### 7. Purchase Orders (PO)
- **Controllers:** `po/Po.php`, `po/Release_po.php`, etc.
- **Models:** `po/Po_model.php`, `po/Purchase_orders_model.php`
- **Views:** `tasks/po/release_po.php`, `tasks/po/upload_po.php`

### 8. Purchase Requests (PR)
- **Controllers:** `pr/Pr.php`, `pr/Return_approve_pr.php`, etc.
- **Models:** `pr/Pr_model.php`, `pr/Purchase_requests_model.php`
- **Views:** `tasks/pr/upload_pr.php`, `tasks/pr/upload_pr_contractor.php`

### 9. Projects
- **Controllers:** `projects/Encode_projects.php`, `projects/Indicate_store_opening.php`, etc.
- **Model:** `projects/Projects_model.php`
- **Views:** `tasks/projects/encode_projects.php`, `tasks/projects/upload_project_completion.php`

### 10. Renewal
- **Controllers:** `renewal/Renewal.php`, `renewal/Renew_contract.php`, etc.
- **Model:** `renewal/Renewal_model.php`
- **Views:** `tasks/renewal/renew_contract.php`, `tasks/renewal/upload_contract.php`

### 11. Sites
- **Controllers:** `sites/Official_store_name.php`, etc.
- **Model:** `sites/Site_nominations_model.php`
- **Views:** `tasks/sites/official_store_name.php`, `tasks/sites/upload_site_nomination.php`

### 12. SOA (Statement of Account)
- **Controllers:** `soa/Soa.php`, `soa/Transmit_soa.php`, etc.
- **Models:** `soa/Soa_model.php`, `soa/Soa_model_back_up.php`, `soa/Toll_partners_model.php`
- **Views:** `tasks/soa/transmit_soa.php`, `tasks/soa/upload_soa.php`, `tabs/soa.php`

---

## Other Controllers (Not in Subfolders)
- `Contractors.php`, `Contract_growers.php`, `Documents.php`, `Feedmill_truckers.php`, `Files.php`, `Forwarders.php`, `Goods.php`, `Goods_marinades.php`, `Inbound_truckers.php`, `Lessors.php`, `Manpower.php`, `Outbound_truckers.php`, `Task.php`, `Task_comment.php`, `Toll_partners.php`
	- These may be shared utilities or workflow-specific controllers. Check their code for workflow association.

---

## How to Identify Workflow Association
- Each workflow has its own subfolder in `controllers`, `models`, and `views/tasks`.
- The file names and folder names match the workflow they belong to.
- For shared or utility controllers/models, review their code or usage to determine their workflow context.

---

**Note:** This module is part of a larger CodeIgniter-based PHP application. For more information, see the main project README.
