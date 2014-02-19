<?php

require_once '../../config.php';
require_once '../jsonrpcphp/includes/jsonRPCClient.php';


$coinclient = new jsonRPCClient('http://'. RPC_USER .':'. RPC_PASS.'@'. RPC_SERVER .':'. RPC_PORT .'/');

$db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
mysql_select_db(MYSQL_DB, $db_link);

while(true) {
	$netstats = $coinclient->getmininginfo();

    //var_dump($netstats);

    //NETWORK SNAPSHOTS ===============
    $block = $netstats['blocks'];
    $diff = $netstats['difficulty'];
    $nethash = $netstats['networkhashps'];
    $nethashMbps = $nethash / 1000;
    $timestamp = time();

    $query = "INSERT INTO network_snapshot (block, diff, nethash, timestamp) VALUES ($block, $diff, $nethash, $timestamp)";
    $result = mysql_query($query);

    echo "Stats: block:". $block ." diff: ". $diff ." nethash: ". number_format($nethashMbps,2) . PHP_EOL;

    if(!checkForDBError($result)) {

        //BLOCK TRACKING ===============
        $query = "SELECT * FROM block_time WHERE starttime > 0 AND endtime = -1 ORDER BY id DESC LIMIT 1" ;
        $result = mysql_query($query);
        if(mysql_numrows($result) == 1) { //a block is being tracked, waiting for it to finish
            $row = mysql_fetch_array($result);
        }

        if($row && $row['block'] == $block-1) { //if the last block being tracked was current-1, it has been found

            if($row['block'] != $block) { //the block has been found as best as we can tell given the current snapshot interval, log it
                $block_time = $timestamp - $row['starttime'];
                $prevblock = $row['block'];
                $query = "UPDATE block_time SET endtime = $timestamp, totaltime = $block_time WHERE block = $prevblock";
                $result = mysql_query($query);
                checkForDBError($result);
                echo $row['block'] ." FOUND totaltime: ". $block_time . PHP_EOL;
            }
        }

        if(!$row || $row['block'] != $block) { //block has no record, start tracking its mining time
            $query = "INSERT INTO block_time (block, starttime) VALUES ($block, $timestamp)";
            $result = mysql_query($query);
            if(!checkForDBError($result))
                echo "New block entry created for: ". $block . PHP_EOL;
        }
    }
	sleep(SNAPSHOT_INTERVAL);
}

function checkForDBError($resource) {
    global $query;
    if(!$resource) {
        echo "Database error:". var_dump($resource) . PHP_EOL;
        echo "> query:". $query . PHP_EOL;
        die;
    }else{
        return false;
    }
}

?>