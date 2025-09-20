var config 		= {};

config.database 	= {};
config.database.core 	= {};

config.http 		= {};
config.app 		= {};

config.ssl 		= true;

config.database.core.connectionLimit 	= 500;
config.database.core.host 		= '192.168.64.51';
config.database.core.user 		= 'pria';
config.database.core.password 		= 'R7WVbJEVe=FztMNC';
config.database.core.database 		= 'pria_core';
config.database.core.debug 		= false;
config.database.core.port 		= 3306;

config.http.port 	= 8000;

module.exports  	= config;
