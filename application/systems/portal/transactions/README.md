# Portal Transactions Module – Workflow Mapping

This folder contains the source code for the Transactions module of the Portal system in the PRIA application. Each workflow is organized into its own MVC structure.

## Workflows & Their MVC Structure

## Extended Controllers ##

## Imported Models ##

### Generally Used by All Workflows Within the Portal Folder

### 0. Rerefences 
**constants**
- `portal/constants.php`
MODULE TABS FOLDER
	— 

PROJECT MODULES
	— references the module table from pria_core
	
ACCOUNT GROUPS
	— 

TRANSACTION DIRECTORY
	— 

DOCUMENT TYPES
	— 

database

portal_model

SYSAD_MODEL

### 1. Overview
**Controllers:** Base Path : portal/transactions/controllers
- `tabs/Overview.php`
	— Used by all Workflows to show recent actions done in a recent transaction.
	— Pulls data from the overview table
	— 

### 2. Files
**Controllers:** Base Path : portal/transactions/controllers
- `tabs/Files.php`
	— 
	— 

### 3. Tasks
**Controllers:** Base Path : portal/transactions/controllers
- `Task.php`
	— 
	— 

### Transaction Specific

### 1. Document Transmittal
**Controllers:** Base Path : portal/transactions/controllers
- `Document_transmittal_mainpage.php` 
	— Loads the tabs for the Document Transmittal module (Overview, Document Transmittal, Files).
	— Extends Transaction_controller

- `doc_transmittal/document_transmittal_modal.php` 
	— Controller for the "(+) Add Transmittal" button in the Transmittal tab.
	— Has a process() method that is responsible for calling the log_overview() method
	— Extends Task_controller

- `tabs/Document_transmittal_tab.php`
	— Controller responsible for the Transmittal tab.
	— Extends Transaction_controller

**Model** Baste Path : portal/transactions/models/doc_transmittal
- `Document_transmittal_model.php` 
	— 
	— 

**Views** Base Path : portal\transactions\views\
- `modals/add_document_transmittal.php`
	— The view for '+ Add Transmittal' modal





