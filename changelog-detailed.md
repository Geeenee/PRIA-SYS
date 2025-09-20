# Change Log
> All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

- **[x.y.z.]**	major.minor.patch
- **YYYY-MM-DD** date format
- **Added** for new features.
- **Changed** for changes in existing functionality.
- **Updated** for updated external plugins
- **Deprecated** for once-stable features removed in upcoming releases.
- **Removed** for deprecated features removed in this release.
- **Fixed** for any bug fixes.

----------------------------------------------------------------------------------

## [0.1.1] 2017-06-29 by Meg Vibal
### Updated
#### Materialize front-end framework v0.97.7 to v0.99.0
#### *static/js/materialize.js*
#### *static/css/materialize.css*

### Changed
#### Modified materialize css to remove conflicts from other plugin 
#### *static/css/materialize.css*
* Added :not(.labelauty) selector to all [type="radio"] and [type="checkbox"]
* Added :not(.cd-side-nav):not(.cd-nav) selector to all nav tags
* Changed all @font-face rule font path: from '../fonts' to '../font' on line 4396
* Added new css rule for fixed header modal after .modal.modal-fixed-footer .modal-content (line 5638)
  
  ** Line 5646
  .modal.modal-fixed-header .modal-content {
	height: calc(100% - 116px);
  }
	
### Deprecated
#### Refactored Modal Plugin
* Existing .leanModal() and .openModal() function was refactored on the latest update

### Changed
#### Modified materialize modal initialization 
#### *static/js/script.js*
* Changed modal initialization to .modal() instead of the dynamic object function (see loadModal() function on line 280)

  ** Line 285 
  *** ORIGINAL
  target_id = (vars.modal_type === 'lean') ? "." + vars.modal_id + "_trigger" : "#" + vars.modal_id;
		
  var $function = vars.modal_type + 'Modal';
  $(target_id )[$function]({
 	...
  });
	
  *** REVISED
  target_id = "#" + vars.modal_id;
	
  $(target_id ).modal({
	...
  });


----------------------------------------------------------------------------------

## [0.1.0]
### Added
- Initial asiagate_php_core
