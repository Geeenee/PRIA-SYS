var basepath        = __dirname;
var path            = require('path');
var path_app        = path.dirname(basepath);

var config          = require('./app/config');
var constants       = require('constants');

var http            = require('http');
var fs              = require('fs');

if( config.ssl )
{
	var http 		= require('https');
}

/*var cmd             = require('./app/cmd');

cmd( path_app, path, basepath );*/

var app             = http.createServer(handler);

if( config.ssl )
{
    var options = {
        secureProtocol: "TLSv1_2_method",
        secureOptions: constants.SSL_OP_NO_SSLv2 | constants.SSL_OP_NO_SSLv3 | constants.SSL_OP_NO_TLSv1,
        key: fs.readFileSync('/etc/letsencrypt/live/bavipria.com/privkey.pem'),
        cert: fs.readFileSync('/etc/letsencrypt/live/bavipria.com/cert.pem'),
        ca: fs.readFileSync('/etc/letsencrypt/live/bavipria.com/chain.pem'),
        requestCert: false,
        ciphers: [
          "ECDHE-RSA-AES128-GCM-SHA256",
          "ECDHE-ECDSA-AES128-GCM-SHA256",
          "ECDHE-RSA-AES256-GCM-SHA384",
          "ECDHE-ECDSA-AES256-GCM-SHA384",
          "DHE-RSA-AES128-GCM-SHA256",
          "DHE-DSS-AES128-GCM-SHA256",
          "kEDH+AESGCM",
          "ECDHE-RSA-AES128-SHA256",
          "ECDHE-ECDSA-AES128-SHA256",
          "ECDHE-RSA-AES128-SHA",
          "ECDHE-ECDSA-AES128-SHA",
          "ECDHE-RSA-AES256-SHA384",
          "ECDHE-ECDSA-AES256-SHA384",
          "ECDHE-RSA-AES256-SHA",
          "ECDHE-ECDSA-AES256-SHA",
          "DHE-RSA-AES128-SHA256",
          "DHE-RSA-AES128-SHA",
          "DHE-DSS-AES128-SHA256",
          "DHE-RSA-AES256-SHA256",
          "DHE-DSS-AES256-SHA",
          "DHE-RSA-AES256-SHA",
          "!aNULL",
          "!eNULL",
          "!EXPORT",
          "!DES",
          "!RC4",
          "!3DES",
          "!MD5",
          "!PSK"
        ].join(':'),
        honorCipherOrder: true
    };
    var app = http.createServer(options, handler);
}

	/* if IIS/Windows */
	/*iosocket = require('socket.io')({
		'transports': [ 'xhr-polling' ],
		'resource': '/socket.io'
	}),
	io = iosocket.listen(app),*/
	/* else */
var io                      = require('socket.io').listen(app);

var Router                  = require('router');
var finalhandler            = require('finalhandler');

var router                  = Router();
var home                    = require('./app/home');
var qs 						= require('querystring');

 
var core_socket             = require('./app/core_socket')(io, router, qs, http);

// creating the server
/* if IIS/Windows */
//app.listen(process.env.PORT);
/* else */
app.listen(config.http.port);

// on server started we can load our generic html page
function handler(req, res) 
{
    router(req, res, finalhandler(req, res));
}

home( router, fs, basepath );
