var $base_url = $("#base_url").val();
$loader = '&nbsp;&nbsp;<img src="' + $base_url + 'static/images/ajax-loader-bar-black.gif"/>',
	PATH_USER_UPLOADS = $('#path_user_uploads').val(),
	PATH_IMAGES = $('#path_images').val(),
	PATH_SETTINGS_UPLOADS = $('#path_settings_upload').val(),
	PATH_FILE_UPLOADS = $('#path_file_uploads').val();

var csrf_name = $('meta[name = "csrf-name"]').attr('content'),
	csrf_token = $('meta[name = "csrf-token"]').attr('content');
pass_data = {};

pass_data[csrf_name] = csrf_token;

$.ajaxSetup({
	beforeSend: function (jqXHR, settings) {
		if (csrf_name) {
			if (settings.data)
				settings.data += '&' + csrf_name + '=' + csrf_token;
			else
				settings.data = csrf_name + '=' + csrf_token;
		}
	},
	data: pass_data
});




//  used whether if the system will check if the session has suddenly expire or not
//  will usually be set to false if the template has no authentication like portals.
var check_session = true;

function button_loader(id, active) {
	var loading_bar = (active === 1) ? $loader : "",
		btn = $("#" + id),
		active_btn_label = btn.data("btn-action"),
		default_btn_label = btn.html();

	if (active) {
		btn.html(active_btn_label + loading_bar); //updating
		btn.attr('disabled', 'disabled');
		btn.val(default_btn_label); // update
	} else {
		btn.removeAttr('disabled');
		btn.html(btn.val()); // update
		btn.val(default_btn_label.split("&nbsp;")[0]);
	}
}

function content_form(controller, module, id) {
	var module = module || "";
	id = id || "";
	path = module + "/" + controller + "/" + id;

	window.location.href = $base_url + path;
}

function content_delete(alert_text, param_1, param_2) {
	var param_2 = param_2 || "";

	$('#confirm_modal').confirmModal({
		topOffset: 0,
		onOkBut: function () {
			deleteObj.removeData({ param_1: param_1, param_2: param_2 });
		},
		onCancelBut: function () { },
		onLoad: function () {
			$('.confirmModal_content h4').html('Are you sure you want to delete this ' + alert_text + '?');
			$('.confirmModal_content p').html('This action will permanently delete this record from the database and cannot be undone.');
		},
		onClose: function () { }
	});
}

function alert_msg(type, msg) {
	Materialize.toast(msg, 5000, type);
}

function notification_msg(type, msg, old, extra_opt) {
	/*	$(".notify." + type + " p").html(msg);
		$(".notify." + type).notifyModal({
			duration : -1
		});*/

	if (old) {
		$(".notify." + type + " p").html(msg);

		if (type == "success") {
			$(".notify." + type).notifyModal({
				duration: 2500
			});
		}
		else {
			$(".notify." + type).notifyModal({
				duration: -1
			});
		}
	}
	else {
		var default_setting = $.extend({
			size: 'normal',
			title: type.toUpperCase(),
			msg: msg,
			delay: 15000
		}, extra_opt);

		Lobibox.notify(type, default_setting);
	}
}

function table_export(id, type, exclude, escape) {
	var escape = escape || 'false';
	ignore_col = exclude || "";
	arr = "";

	if (ignore_col != "") {
		var ignore_col = ignore_col.split(",");
		arr = ignore_col.map(Number);
	}


	$('#' + id).tableExport({
		type: type,
		escape: escape,
		ignoreColumn: arr
	});
}

function modal_init(data_id) {
	var data_id = data_id || '',
		jscroll;

	jscroll = modalObj.checkIfScroll();

	if (jscroll) {
		modalObj.loadViewJscroll({ id: data_id });
	} else {
		modalObj.loadView({ id: data_id });
	}

	return false;
}

function set_active_tab(module) {
	var active_tab = window.location.hash.replace('#', ''),
		controller = active_tab.replace('tab_', '');

	load_index(active_tab, controller, module);
}

function load_index(id, controller, module, func_callback) {
	if ($('.datepicker,.datepicker_start,.datepicker_end,.datetimepicker,.datetimepicker_start,.datetimepicker_end,.timepicker,.timepicker_start,.timepicker_end').length > 0)
		$('.datepicker,.datepicker_start,.datepicker_end,.datetimepicker,.datetimepicker_start,.datetimepicker_end,.timepicker,.timepicker_start,.timepicker_end').datetimepicker('destroy');

	var path = module + "/" + controller;

	start_loading();

	$(".tab-content").not("#" + id).html("");

	$("#" + id).load($base_url + path, function () {
		end_loading();
	}).fadeIn("slow").show();

	if (func_callback !== undefined && typeof (func_callback) === 'string') {
		eval(func_callback);
	}

	window.location.hash = id;
}

function load_index_post(id, controller, module, obj, func_callback, modal) {
	if ($('.datepicker,.datepicker_start,.datepicker_end,.datetimepicker,.datetimepicker_start,.datetimepicker_end,.timepicker,.timepicker_start,.timepicker_end').length > 0)
		$('.datepicker,.datepicker_start,.datepicker_end,.datetimepicker,.datetimepicker_start,.datetimepicker_end,.timepicker,.timepicker_start,.timepicker_end').datetimepicker('destroy');

	start_loading();

	var path = module + "/" + controller,
		ul = $('.tabs'),
		link = ul.find('li').find('a[href="#' + id + '"]'),
		post_data,
		data = {};

	if (link.length === 0) {
		link = $(obj);
	}

	post_data = link.attr('data-post');

	if (post_data != '') {
		data = JSON.parse(post_data);
	}

	data['tab_id'] = id;

	if (link.attr('data-form')) {
		var form_id = JSON.parse(link.attr('data-form'));

		if (form_id instanceof Array) {
			var f_i = 0,
				f_len = form_id.length;

			for (; f_i < f_len; f_i++) {
				var f_form = form_id[f_i];

				if ($(f_form).length !== 0) {
					var f_form_id = $(f_form).attr('id');

					var f_form_data = $(f_form).serializeArray();

					var f_f_i = 0,
						f_f_len = f_form_data.length;

					data[f_form_id] = {};
					var arr = [];
					var cur_key;

					for (; f_f_i < f_f_len; f_f_i++) {
						var regex = /[\[]/gi;
						var check = f_form_data[f_f_i].name.match(regex);

						if (check !== null) {
							var real_name = f_form_data[f_f_i].name.replace('[]', '');

							if (f_form_data[f_f_i].value != '') {
								arr.push(f_form_data[f_f_i].value);
							}

							if (data[f_form_id][real_name] !== undefined) {
								if (f_form_data[f_f_i].value != '') {
									data[f_form_id][real_name].push(f_form_data[f_f_i].value);
								}

							}
							else {
								data[f_form_id][real_name] = [];

								if (f_form_data[f_f_i].value != '') {
									data[f_form_id][real_name].push(f_form_data[f_f_i].value);
								}
							}

							if (f_form_data[f_f_i + 1] !== undefined) {
								cur_key = real_name;
							}

							if (cur_key != real_name) {
								arr = [];
							}
						}
						else {
							data[f_form_id][f_form_data[f_f_i].name] = f_form_data[f_f_i].value;
						}
					}
				}

			}
		}
		else {
			if ($(form_id).length !== 0) {
				var form_data = $(form_id).serializeArray();

				var i = 0,
					len = form_data.length;

				var arr = [];
				var cur_key;

				for (; i < len; i++) {
					var regex = /[\[]/gi;
					var check = form_data[i].name.match(regex);

					if (check !== null) {
						if (form_data[i].value != '') {
							arr.push(form_data[i].value);
						}

						if (data[form_data[i].name] !== undefined) {
							if (form_data[i].value != '') {
								data[form_data[i].name].push(form_data[i].value);
							}

						}
						else {
							data[form_data[i].name] = [];

							if (form_data[i].value != '') {
								data[form_data[i].name].push(form_data[i].value);
							}
						}

						if (form_data[i + 1] !== undefined) {
							cur_key = form_data[i + 1].name;
						}

						if (cur_key != form_data[i].name) {
							arr = [];
						}
					}
					else {
						data[form_data[i].name] = form_data[i].value;
					}
				}
			}
		}
	}

	$(".tab-content").not("#" + id).html("");
	$.post($base_url + path, data).promise().done(function (response) {

		$("#" + id).html(response).fadeIn("slow").show();

		create_avatar($('.letter-avatar'), { width: 45, height: 45, fontSize: 30 });

		end_loading();

		if (modal) {
			/*$("#" + modal).find('div.modal-content #content').jScrollPane({autoReinitialise: true, contentWidth: '0px'});

			  var api = $("#" + modal).find('div.modal-content #content').data('jsp');

			api.reinitialise();  */
		}

		if (func_callback !== undefined && typeof (func_callback) === 'string') {
			eval(func_callback);
		}

	});

	window.location.hash = id;
}

function search_wrapper(id, input, items, highlight) {

	var highlight = highlight || "";
	var highlight = (highlight === "") ? true : false;
	$(id).lookingfor({
		input: $(input),
		items: items,
		highlight: highlight
	});
}

function sort(id, order_by, wrapper, action, input_id, item) {
	var id = id.attr('id'),
		up_ico = 'flaticon-up151',
		down_ico = 'flaticon-down95';

	// RESET ACTIVE BUTTONS
	$('.sort-btn').not('#' + id).removeClass('active').find("i").switchClass(up_ico, down_ico, 1000, "easeInOutQuad");

	$('#' + id).toggleClass('active');

	if (($('#' + id).hasClass('active'))) {
		var order = 'ASC',
			class_from = down_ico,
			class_to = up_ico;
	} else {
		var order = 'DESC',
			class_from = up_ico;
		class_to = down_ico;
	}

	filter(order_by, order, wrapper, action, input_id, item);
	$('#' + id).find("i").switchClass(class_from, class_to, 1000, "easeInOutQuad");
}

function filter(filter_1, filter_2, wrapper, action, input_id, item) {

	var data = { filter_1: filter_1, filter_2: filter_2 };

	$(wrapper).isLoading();

	$.post($base_url + action, data, function (result) {
		$(wrapper).isLoading("hide").html(result);
		search_wrapper(wrapper, input_id, item, 1);
	}, 'json');
}

function generate_report(controller, extension, filter) {
	var filter = filter || "",
		url = $base_url + controller + "/" + extension + "/" + filter;

	window.open(url, '_blank');
}

/* Replaces all broken images with "avatar" class with the default no avatar image */
function avatar_fix() {
	$('img.avatar').error(function () {
		$(this).attr('src', $base_url + 'static/images/avatar/no_avatar.jpg');
	});
}

function help_text(form_id) {
	$.each($('.help', '#' + form_id), function () {
		var data = $(this).data(),
			text = data.helpText;

		$(this).append('<i class="help-tooltip material-icons titleModal" data-placement="right" title="' + text + '">help</i>');
	});
}

function do_process(role_code, proceeding_step, action, is_return, org_code) {
	var org_code = org_code || "";
	$('#confirm_modal').confirmModal({
		topOffset: 0,
		onOkBut: function () {
			workflowObj.workflowData({ role_code: role_code, proceeding_step: proceeding_step, is_return: is_return, org_code: org_code });
		},
		onCancelBut: function () { },
		onLoad: function () {
			$('.confirmModal_content h4').html(action);
			$('.confirmModal_content p').html('This action will submit the form and proceed to the specified stage of process. Are you sure?');
		},
		onClose: function () { }
	});
}

function ajax_loader(wrapper) {
	$(wrapper).isLoading({
		position: 'inside',
		tpl: '<div class="loader"></div>'

	});
}
/*--------------------------------------------------------------
  TABS by Doc
  - Loads initial tab: <li class="tab"><a class="active"></li>
		  If there's no "active" class, the default is the first tab.
  - Loads the same active tab when refreshed.
--------------------------------------------------------------*/

function load_initial_tab() {
	var tabs = $(".tabs-wrapper"),
		cnt = 0,
		len;

	if (tabs.length !== 0) {
		len = tabs.length;

		for (; cnt < len; cnt++) {
			var initial = $(tabs[cnt]).find('li a.active'),
				id;

			if (initial.length !== 0 && initial.length === 1) {
				initial.click();
			}
			else {
				initial.each(function () {
					id = $(this).attr('href');

					var div = $(this).parents('div.tabs-wrapper').parent();

					if (div.find('div' + id).not(':hidden').length !== 0) {
						$(this).click();
					}

				})
			}
		}
	}
}

function toggle(id, content_id, custom_func) {
	if ($("#" + id).is(':checked')) {
		$('#' + content_id).fadeIn('slow').show();
	} else {
		$('#' + content_id).fadeOut('slow').hide();
	}

	if (custom_func !== undefined) {
		if (typeof (custom_func) === 'string') {
			eval(custom_func);
		}
		else {
			custom_func.apply(null, [id, content_id]);
		}
	}
}

function password_constraints(constraints) {

	Array.prototype.frequencies = function () {
		var l = this.length, result = { all: [] };
		while (l--) {
			result[this[l]] = result[this[l]] ? ++result[this[l]] : 1;
		}
		// all pairs (label, frequencies) to an array of arrays(2)
		for (var l in result) {
			if (result.hasOwnProperty(l) && l !== 'all') {
				result.all.push([l, result[l]]);
			}
		}
		return result;
	};

	window.Parsley.addValidator('pass',
		function (input, data_val) {
			var input_copy = input;
			var input_count = input.length;
			var upper_count = input_copy.replace(/[^A-Z]/g, "").length;
			var digit_count = input_copy.replace(/[^0-9]/g, "").length;

			var rep_pass = input_copy.split('').frequencies();
			var check_repeat_pass = false;
			var check_pass_same = false;
			var pass_same_user = parseInt(constraints.pass_same);
			var user_repeat_pass = parseInt(constraints.repeat_pass);

			var $username = jQuery(data_val);

			/* if( Object.keys(rep_pass).length !== 0 )
			 {
				 var rep_arr 	= [];

				 for( var keys in rep_pass )
				 {

					 if( rep_pass[ keys ] instanceof Array === false && rep_pass[ keys ] != '' )
					 {
						 rep_arr.push( rep_pass[ keys ] );
					 }
				 }

				 var rep_max = Math.max.apply( null, rep_arr );

				 check_repeat_pass = ( user_repeat_pass !== 0 ) ? rep_max > 1 : false;
			 }*/

			var rep_pass = input_copy.match(/(.)\1+/g);
			check_repeat_pass = (user_repeat_pass !== 0) ? (rep_pass !== null && rep_pass.length !== 0) : false;

			var pass_length = parseInt(constraints.pass_length);
			var upper_length = parseInt(constraints.upper_length);
			var digit_length = parseInt(constraints.digit_length);

			if (pass_same_user != 0) {
				if ($username.length != 0 && $username.val() != '') {
					check_pass_same = input == $username.val();
				}
			}

			/*if( pass_length > 0 )
			{*/
			if (input_count < pass_length || upper_count < upper_length || digit_count < digit_length || check_repeat_pass || check_pass_same) {
				return false;
			}

			return true;
			// }

		}).addMessage('en', 'pass', constraints.pass_err);
}

function username_constraints(constraints) {

	/*Array.prototype.frequencies_user = function() {
		var l = this.length, result = {all:[]};
		while (l--){
		   result[this[l]] = result[this[l]] ? ++result[this[l]] : 1;
		}
		// all pairs (label, frequencies) to an array of arrays(2)
		for (var l in result){
		   if (result.hasOwnProperty(l) && l !== 'all'){
			  result.all.push([ l,result[l] ]);
		   }
		}
		return result;
	};*/

	window.Parsley.addValidator('username',
		function (input, data_val) {
			var input_copy = input;
			var input_count = input.length;
			var digit_count = input_copy.replace(/[^0-9]/g, "").length;


			var user_has_cons = parseInt(constraints.user_has_cons);
			var user_min_length = parseInt(constraints.user_min_length);
			var user_max_length = parseInt(constraints.user_max_length);
			var digit_length = parseInt(constraints.user_digit_length);

			var check_user_min = false;
			var check_user_max = false;
			var check_user_dig = false;

			if (input_count != 0 && user_has_cons != 0) {
				if (user_min_length != 0) {
					check_user_min = input_count < user_min_length;
				}

				if (user_max_length != 0) {
					check_user_max = input_count > user_max_length;
				}

				if (digit_length != 0) {
					check_user_dig = digit_count < digit_length;
				}

				if (check_user_min || check_user_max || check_user_dig) {
					return false;
				}

			}

			/*if( pass_length > 0 )
			{*/

			return true;
			// }

		}).addMessage('en', 'username', constraints.user_err);
}

function refresh_datatable(datatable_options, btn_id) {
	var button_id = btn_id || "#refresh_btn";
	var options = JSON.parse(datatable_options);

	$(button_id).on("click", function () {
		load_datatable(options);
	});
}

function force_logout(user_id) {
	$('#confirm_modal').confirmModal({
		topOffset: 0,
		onOkBut: function () {
			$.post($base_url + "user_management/users/force_sign_out", { user_id: user_id }, function (result) {
				if (result.flag == 0) {
					notification_msg("error", result.msg);
				}
				else {
					notification_msg("success", result.msg);
					$("#active_users_list").html(result.list);
				}
			}, 'json');
		},
		onCancelBut: function () { },
		onLoad: function () {
			$('.confirmModal_content h4').html('Are you sure you want to log out this user account?');
		},
		onClose: function () { }
	});
}

function get_action(elem) {
	var id = elem.id;
	document.getElementById("select").addEventListener("click", $("#" + id).val(), false);

}

function load_content(load_id, path, trigger_id) {
	var trigger_id = trigger_id || "";


	if (trigger_id != "") {
		$("#" + trigger_id).parent().parent().next("." + load_id).load($base_url + path);
		$("#" + trigger_id).addClass("active");
		$(".links").not("#" + trigger_id).removeClass("active");
	} else {
		$("#" + load_id).load($base_url + path);
	}
}

function start_loading() {
	/* 	$( "body" ).isLoading({
			text:       "<div class='loader'></div>",
			position:   "inside"
			});
	 */
	$.blockUI({
		message: '<img src="' + $base_url + 'static/images/loading.gif">',
		css: { zIndex: '9999' },
		overlayCSS: { zIndex: '9999' }
	});
}

function end_loading() {
	// $("body").isLoading("hide");
	$.unblockUI();
}

function is_json(str) {
	var check = true;

	try {
		// var my_json 	= JSON.stringify(str);
		var json = JSON.parse(str);

		if (typeof (str) == 'string') {
			/*if( str.length == 0)
			{
				check 	= false;
			}

				  var match 	= str.match(/^{/);

				  if( match === null || match.length == 0 )
				  {
						check 	= false;
				  }*/

		}
		else if (typeof (str) !== 'object') {
			check = false;
		}
	}
	catch (e) {
		check = false;
	}

	return check;
}

function toggle_fields(module_code) {
	$.post($base_url + "common/system_modules/toggle_fields", { module_code: module_code }, function (result) {
		for (var i = 0; i < result.length; i++) {
			var field_id = result[i];
			$("#" + field_id + "_wrapper").hide();
		}
	}, 'json');
}


function dynamic_parsley(fields, field_label, btn_obj) {
	var len = fields.length,
		cnt = 0,
		trig_modal = [],
		req_str = '',
		check = true;

	for (; cnt < len; cnt++) {
		var pars = $(fields[cnt]).parsley();

		if (pars !== undefined) {

			if ($(fields[cnt]).attr('data-parsley-required') === undefined) {
				$(fields[cnt]).attr('data-parsley-required', 'true');
			}

			pars.validate();

			if (pars.isValid()) {
				trig_modal.push(true);
			}
			else {
				trig_modal.push(false);

				req_str += '<b>' + field_label[cnt] + '</b>' + ' is required. <br/>';
			}
		}
	}

	if (trig_modal.indexOf(false) === -1) {
		$(btn_obj).click();
	}
	else {

		var br_len = '<br/>';

		notification_msg("error", req_str.substring(0, req_str.length - br_len.length));

		check = false;
	}

	return check;
}

function reload_datatable(selector) {
	var selector;

	if (selector !== undefined) {
		if (typeof (selector) === 'string') {
			selector = $(selector);
		}
		else if (selector instanceof jQuery) {
			selector = selector
		}
	}
	else {
		// selector 		= $('.color-picker');
	}

	if (selector !== undefined) {
		selector.DataTable().ajax.reload();
	}
}

function output_image(file, path) {
	var ajax = $.ajax({
		url: $base_url + 'Media/output_image',
		data: {
			file: file,
			path: path
		},
		success: function (response) {
			return response;
		},
		async: false,
		error: function () { }
	});

	if (CONSTANTS.CHECK_CUSTOM_UPLOAD_PATH) {
		var img = ajax.responseText;
	}
	else {
		var img = $base_url + path + file;
	}

	return ajax.responseText;
}

function load_editor(id, in_modal) {
	if (typeof (CKEDITOR) !== 'undefined' && CKEDITOR !== undefined) {
		if ($('#' + id) !== null && $('#' + id).length !== 0) {

			CKEDITOR.replace(id, { height: '300px' });

			if (in_modal) {
				CKEDITOR.on('dialogDefinition', function (e) {

					var dialogDefinition = e.data.definition;

					var dialogName = e.data.name;
					var editor = e.editor;

					/*dialogDefinition.onShow = function ()
					{
						var $body 	= $('body');
						  $body.removeClass('md-open');
						$body.removeAttr('style');
						ModalEffects.resetScroll();
					};

					dialogDefinition.onHide = function ()
					{
						ModalEffects.setScroll();
					};*/

					var $body = $('body');
					$body.removeClass('md-open');
					$body.removeAttr('style');

				});
			}
		}
	}
}

function update_editor() {
	if (typeof (CKEDITOR) !== 'undefined' && CKEDITOR !== undefined) {
		if (Object.keys(CKEDITOR.instances).length !== 0) {
			for (instance in CKEDITOR.instances) {
				CKEDITOR.instances[instance].updateElement();
			}
		}
	}
}

function expand_all() {
	$(".collapsible-header").addClass("active");
	$(".collapsible").collapsible({ accordion: false });
}

function collapse_all() {
	$(".collapsible-header").removeClass(function () {
		return "active";
	});
	$(".collapsible").collapsible({ accordion: true });
	$(".collapsible").collapsible({ accordion: false });
}

function load_sub_tab(path) {
	$(".sub-tab-content").load($base_url + path).fadeIn("slow").show();
}

function display_c() {
	var refresh = 1000; // Refresh rate in milli seconds
	mytime = setTimeout('display_time()', refresh)
}

function display_time() {
	var x = new Date();

	const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

	// date part ///
	var month = monthNames[x.getMonth()];

	var day = x.getDate();
	var year = x.getFullYear();
	//if (month <10 ){month='0' + month;}
	if (day < 10) { day = '0' + day; }
	var x3 = month + ' ' + day + ', ' + year;

	// time part //
	var hour = x.getHours();
	var minute = x.getMinutes();
	var second = x.getSeconds();
	var meridiem = hour >= 12 ? 'pm' : 'am';

	hour = hour % 12;
	hour = hour ? hour : 12; // the hour '0' should be '12'

	if (minute < 10) { minute = '0' + minute; }
	if (second < 10) { second = '0' + second; }
	var x3 = x3 + ' ' + hour + ':' + minute + ':' + second + ' ' + meridiem

	if (document.getElementById('clock_display') != undefined && document.getElementById('clock_display') != null) {
		document.getElementById('clock_display').innerHTML = x3;
	}

	display_c();
}

//For file upload. remove file uploaded.
//Added by Christian June 11, 2019
function remove_file_com() {
	$('#uploaded_file_div').html('');
	$('#file_btn').removeClass('none'); //remove attach button
	$('.file-path').removeClass('none'); //remove text field
	$('.file-path').val(''); //remove value
}

//For file upload. upload file
//Added by Christian June 11, 2019
function upload_file_com() {
	var name = document.getElementById('file_upload_btn');

	var re = /(?:\.([^.]+))?$/;
	var fileName = name.files.item(0).name;	//filename
	var ext = re.exec(fileName)[1]; 	//extension name
	var fileSize = name.files.item(0).size; 	//file size
	var dateNow = get_uploaded_date(); 		//date today

	//who uploaded

	$('#file_btn').addClass('none'); //remove attach button
	$('.file-path').addClass('none'); //remove text field

	$('#uploaded_file_div').html('<div class="file-wrapper ' + ext + '"><div class="type" data-file-type="' + ext + '"></div><div class="contents valign-top"><div class="filename truncate">' + fileName + '</div>uploaded ' + dateNow + ' by Juan dela Cruz. ' + Math.round(fileSize / 1024) + ' KB</div><div class="actions valign-middle right-align"><button onclick="remove_file_com()" type="button" id="delete"><i class="material-icons valign-middle">delete</i></button></div></div>');
}

//For file upload. get uploaded date and time
//Added by Christian June 11, 2019
function get_uploaded_date() {
	var dateNow = Date.now(); //date today

	var x = new Date();

	const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

	// date part ///
	var month = monthNames[x.getMonth()];

	var day = x.getDate();
	var year = x.getFullYear();
	//if (month <10 ){month='0' + month;}
	if (day < 10) { day = '0' + day; }
	var x3 = month + ' ' + day + ', ' + year;

	// time part //
	var hour = x.getHours();
	var minute = x.getMinutes();
	var second = x.getSeconds();
	var meridiem = hour >= 12 ? 'pm' : 'am';

	hour = hour % 12;
	hour = hour ? hour : 12; // the hour '0' should be '12'

	if (minute < 10) { minute = '0' + minute; }
	if (second < 10) { second = '0' + second; }
	var x3 = x3 + ' ' + hour + ':' + minute + ':' + second + ' ' + meridiem

	return x3;
}

$.fn.serializeObject = function () {
	var data = {};
	var f_form_data = this.serializeArray();

	var f_f_i = 0,
		f_f_len = f_form_data.length;

	var arr = [];
	var cur_key;

	for (; f_f_i < f_f_len; f_f_i++) {
		var regex = /[\[]/gi;
		var check = f_form_data[f_f_i].name.match(regex);

		if (check !== null) {
			var real_name = f_form_data[f_f_i].name.replace('[]', '');

			if (f_form_data[f_f_i].value != '') {
				arr.push(f_form_data[f_f_i].value);
			}

			if (data[real_name] !== undefined) {
				if (f_form_data[f_f_i].value != '') {
					data[real_name].push(f_form_data[f_f_i].value);
				}

			}
			else {
				data[real_name] = [];

				if (f_form_data[f_f_i].value != '') {
					data[real_name].push(f_form_data[f_f_i].value);
				}
			}

			if (f_form_data[f_f_i + 1] !== undefined) {
				cur_key = real_name;
			}

			if (cur_key != real_name) {
				arr = [];
			}
		}
		else {
			data[f_form_data[f_f_i].name] = f_form_data[f_f_i].value;
		}
	}
	return data;
};

function download_to_text(filename, text) {
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
	element.setAttribute('download', filename);
	element.style.display = 'none';
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element);
}

$(document).on('focus', ':input:not(input[name="username"]):not(input[name="password"])', function () {
	$(this).attr('autocomplete', 'off');
});


function remove_reminder(rem_id) {
	var rem_id = rem_id || $(".tl-icon input[name=rem_id]").val();
	$('#confirm_modal').confirmModal({
		topOffset: 0,
		onOkBut: function () {
			$.post($base_url + "dashboard/dashboard/remove_reminder", { rem_id: rem_id }, function (result) {
				if (result.flag == 0) {
					notification_msg("error", result.msg);
				}
				else {
					notification_msg("success", result.msg);
					window.location.reload(true);
					//$("#active_users_list").html(result.list);
				}
			}, 'json');
		},
		onCancelBut: function () { },
		onLoad: function () {
			$('.confirmModal_content h4').html('Are you sure you want to remove this reminder?');
		},
		onClose: function () { }
	});
}

function jsGoTo(page, title, url) {
	if ("undefined" !== typeof history.pushState) {
		history.pushState({ page: page }, title, url);
	} else {
		window.location.assign(url);
	}
}