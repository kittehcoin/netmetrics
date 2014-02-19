<?php

require_once '../../config.php';

$db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
mysql_select_db(MYSQL_DB, $db_link);

$query = "SELECT * FROM network_snapshot ORDER BY id DESC LIMIT 1;"; //WHERE timestamp > $startTime AND timestamp < $endTime
$result = mysql_query($query, $db_link);

if($result) {
    $blockHeightSeries = array("name" => "Block Height",
        "data" => array());

    $diffSeries = array("name" => "Difficulty",
        "data" => array());

    $hashSeries = array("name" => "Hashrate",
        "data" => array());

    $timeSeries = array("name" => "timestamp",
        "data" => array());

    while($row = mysql_fetch_array($result)) {
        array_push($blockHeightSeries['data'], intval($row['block']));
        array_push($diffSeries['data'], floatval($row['diff']));
        array_push($hashSeries['data'], floatval($row['nethash']));
        array_push($timeSeries['data'], intval($row['timestamp']));
    }

    $jsonResult = array($blockHeightSeries, $diffSeries, $hashSeries, $timeSeries);
    echo json_encode($jsonResult);
}else{
    echo json_encode(array('error' => 'Database error: ' . var_dump($result)));
}


?>