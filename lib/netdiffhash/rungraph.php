<?php

require_once '../../config.php';
require_once '../jsonrpcphp/includes/jsonRPCClient.php';


$coinclient = new jsonRPCClient('http://'. RPC_USER .':'. RPC_PASS.'@'. RPC_SERVER .':'. RPC_PORT .'/');

$db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
mysql_select_db(MYSQL_DB, $db_link);

while(true) {
	$netstats = $coinclient->getmininginfo();

    //var_dump($netstats);

    $blocks = $netstats['blocks'];
    $diff = $netstats['difficulty'];
    $nethash = $netstats['networkhashps'];
    $nethashMbps = $nethash / 1000;
    $timestamp = time();

    $query = "INSERT INTO diff_records (blocknum, diff, nethash, timestamp) VALUES ($blocks, $diff, $nethash, $timestamp)";
    $result = mysql_query($query, $db_link);
    if(!$result) {
        echo "Database error:". var_dump($result) . PHP_EOL;
        echo "> query:". $query . PHP_EOL;
        die;
    }

	echo "Stats: block:". $blocks ." diff: ". $diff ." nethash: ". number_format($nethashMbps,2) . PHP_EOL;
	sleep(LOG_INTERVAL);
}

?>