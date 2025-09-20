var Po = function () {

	var $transactions = "transactions";
	var $module = "po";

	var save = function () {
		$("#submit_modal_add_po").off('click').on("click", function (e) {
			e.preventDefault();

			if ($('#form_modal_add_po').parsley().validate()) {
				var data = $("#form_modal_add_po").serialize();
				button_loader('submit_modal_add_po', 1);

				$.post($base_url + $transactions + "/" + $module + "/po/process", data, function (result) {

					if (result.flag == '1') {
						notification_msg(result.status, result.msg);

						$('a[href="#tab_purchase_orders"]').trigger('click');
						$("#modal_add_po").modal("close");
					} else {
						notification_msg(result.status, result.msg);
					}

					button_loader('submit_modal_add_po', 0);

				}, 'json');
			}
		});

		document.getElementById('business_center').selectize.on('change', function (ev) {

			if ($("#account_group").val()) {
				const data = $("#form_modal_add_po").serialize();

				$('#vendor')[0].selectize.clear();
				$('#vendor')[0].selectize.clearOptions();

				const options = {
					blockUI: true,
					body: data,
					path: $base_url + $transactions + "/" + $module + "/po/get_vendors",
					successFunc: function (response) {
						const elem_select = $('#vendor')[0].selectize;
						elem_select.clearOptions();
						elem_select.addOption(response);

						if (response.length == 1) {
							elem_select.setValue(response[0].value);
						}
					}
				};

				General.Fetch(options);
			}
			else {
				notification_msg($.CONSTANTS.ERROR, 'Please choose an account group.');

				var bcElem = document.getElementById('business_center');

				bcElem.selectize.clear(true);
			}
		});
		// $("#vendor").on('change', function(e){
		// 	e.preventDefault();

		// 	var data	= $("#form_modal_add_po").serialize();

		//           $('#business_center')[0].selectize.clear();
		//           $('#business_center')[0].selectize.clearOptions();

		// 	$.post($base_url + $transactions + "/" + $module + "/po/get_business_center", data, function(response){

		// 		var len = response.length;

		//               for( var i = 0; i<len; i++){
		//                   var id = response[i]['value'];
		//                   var name = response[i]['text'];

		//                   var $select 	= $('#business_center').selectize();
		// 			var selectize = $select[0].selectize;
		// 			selectize.addOption({value: id, text: name});
		// 			selectize.addItem(id);

		//               }
		//   	}, 'json');
		// });

		$("#account_group").off('change').on('change', function (e) {
			e.preventDefault();

			var data = $("#form_modal_add_po").serialize();

			$('#purchase_request')[0].selectize.clear();
			$('#purchase_request')[0].selectize.clearOptions();

			var elemReleaseDate = $('#released_date');

			if ($.inArray($(this).val(), [$.CONSTANTS.AG_GOODS_MARINADES, $.CONSTANTS.AG_GOODS_BFFI]) > -1) {
				elemReleaseDate.attr('data-parsley-required', true);
				elemReleaseDate.closest('div').find('label[for="released_date"]').addClass('required');
			}
			else {
				elemReleaseDate.attr('data-parsley-required', false);
				elemReleaseDate.closest('div').find('label[for="released_date"]').removeClass('required');
			}

			elemReleaseDate.parsley().reset();

			$.post($base_url + $transactions + "/" + $module + "/po/get_pr_options", data, function (response) {
				let elem_select = $("#purchase_request")[0].selectize;

				elem_select.clear();
				elem_select.clearOptions();
				elem_select.load(function (callback) {
					callback(response);

					if (response.length == 1) {
						elem_select.setValue(response[0].value);
					}
				});
			}, 'json');
		});


		if (document.getElementById('boq_ref')) {
			var boqSel = document.getElementById('boq_ref').selectize;

			boqSel.on('change', function (val) {
				var options = {
					blockUI: true,
					body: $.param({ boq_id: val }),
					path: $base_url + $transactions + "/" + $module + "/po/get_boq_bc_n_vendor",
					successFunc: function (response) {
						if (response.flag == $.CONSTANTS.SUCCESS) {
							var len = response.orgs.length;
							var $select = $('#business_center').selectize();
							var selectize = $select[0].selectize;

							selectize.clear();
							selectize.clearOptions();

							for (var i = 0; i < len; i++) {
								var id = response.orgs[i]['value'];
								var name = response.orgs[i]['title'];

								selectize.addOption({ value: id, text: name });

								if (len == 1 && i == 0)
									selectize.addItem(id, true);
							}


							var len = response.vendors.length;
							var $select = $('#vendor').selectize();
							var selectize = $select[0].selectize;

							selectize.clear();
							selectize.clearOptions();

							for (var i = 0; i < len; i++) {
								var id = response.vendors[i]['vendor_code'];
								var name = response.vendors[i]['vendor_name'];

								selectize.addOption({ value: id, text: name });

								if (len == 1 && i == 0)
									selectize.addItem(id);
							}
						}
						else {
							notification_msg(result.flag, result.msg);
						}
					}
				};
				General.Fetch(options);

				var options = {
					blockUI: true,
					body: $.param({ boq_id: val }),
					path: `${$base_url}${$transactions}/pr/pr/get_pr_per_boq/`,
					successFunc: function (response) {
						console.log('js response:', response);
						if (response.flag == $.CONSTANTS.SUCCESS) {
							var $select = $('#purchase_request').selectize();
							var selectize = $select[0].selectize;

							selectize.clear();
							selectize.clearOptions();

							for (var i = 0; i < response.data.length; i++) {
								const data = response.data[i];
								selectize.addOption({ value: data.pr_id, text: data.pr_num });
							}
						}
						else {
							notification_msg(result.flag, result.msg);
						}
					}
				};

				General.Fetch(options);
			});
		}
	}

	var initModal = () => {
		const po_controller = `${$base_url}transactions/po/po/`;
		/*
				$('#purchase_request').selectize({
					create: true,
					options: [],
					render: {
						option: function (item) {
							return `
								<div>
									${item.text}
								</div>
							`;
						}
					},
					load: function (query, callback) {
						if (!query.length) return callback();
						const controller_path = `${$base_url}transactions/pr/pr/search_pr/${query}`;
						const options = {
							path: controller_path,
							errorFunc: function () {
								callback();
							},
							successFunc: function (response) {
								callback(response.map((item) => {
									return {
										text: item.pr_num,
										value: item.pr_id
									};
								}));
							}
						};

						General.Fetch(options);
					}
				}); */
		const additionalFlagElem = document.getElementById('additional_flag');
		additionalFlagElem.selectize.on('change', (isAdditional) => {
			const options = {
				path: `${po_controller}get_filtered_boq`,
				body: $.param({ isAdditional }),
				errorFunc: function () {
				},
				successFunc: function (response) {
					var $select = $('#boq_ref').selectize();
					var selectize = $select[0].selectize;

					selectize.clear();
					selectize.clearOptions();

					if (response.flag == $.CONSTANTS.SUCCESS) {
						for (var i = 0; i < response.data.length; i++) {
							const data = response.data[i];
							selectize.addOption({ value: data.boq_id, text: data.boq_code });
						}
					}
				}
			};

			General.Fetch(options);
		});

	};
	return {
		save: function () {
			save();
		},
		initModal,
	}
}();
