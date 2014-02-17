<?php

require_once '../../config.php';

$monthunix = 60 * 60 * 24 * 30; //60sec * 60min * 24hrs * 30days
$startTime = isset($_GET['startTime']) ? $_GET['startTime'] : (time() - $monthunix);
$endTime = isset($_GET['endTime']) ? $_GET['endTime'] : time();

$db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
mysql_select_db(MYSQL_DB, $db_link);

$query = "SELECT * FROM diff_records"; //WHERE timestamp > $startTime AND timestamp < $endTime
$result = mysql_query($query, $db_link);

if($result) {
    $blockHeightSeries = array("name" => "Block Height",
                               "yAxis" => 0,
                               "color" => '#ababab',
                               "lineWidth" => 2,
                               "data" => array());

    $diffSeries = array("name" => "Difficulty",
                        "yAxis" => 1,
                        "color" => '#ff0000',
                        "data" => array());

    $hashSeries = array("name" => "Hashrate",
                        "yAxis" => 2,
                        "color" => '#0000ff',
                        "data" => array());

    $timeSeries = array("name" => "timestamp",
                        "data" => array());

    while($row = mysql_fetch_array($result)) {
        array_push($blockHeightSeries['data'], intval($row['blocknum']));
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