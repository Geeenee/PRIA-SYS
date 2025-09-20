var CONSTANTS;

var options = {
	url 	: $base_url + 'Params/get_constants_ajax',
	//async 	: false,
	success : function(response)
	{
		CONSTANTS 	= response;

	    Object.freeze(CONSTANTS);

	    $.CONSTANTS =  CONSTANTS;
	},
	dataType  : 'json'
};

$.ajax( options );