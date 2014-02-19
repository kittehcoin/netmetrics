<?php 

/*
	Base config for network-difficulty-hash graph
*/

define('MYSQL_DB', 'netdiffhash');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', 'root');
define('MYSQL_SERVER', '127.0.0.1');

define('RPC_USER', 'kittehcoinrpc');
define('RPC_PASS', '6W56i7eJg9TiHbdEmDCBhK2Qua2xHwnagx8SWtjFFgHK');
define('RPC_SERVER', '127.0.0.1');
define('RPC_PORT', '22565');

//interval at which data is captured from getminerinfo() and written to the DB, in SECONDS
define('SNAPSHOT_INTERVAL', 1);

//interval at which the web interface live-updates via set-interval
define('LIVEUPDATE_INTERVAL', 10);

?>