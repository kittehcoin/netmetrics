<?php

require_once '../../config.php';

if(isset($_GET['startBlock']))
    $startBlock = $_GET['startBlock'];

if(isset($_GET['endBlock']))
    $endBlock = $_GET['endBlock'];

$db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
mysql_select_db(MYSQL_DB, $db_link);

$query = "SELECT * FROM network_snapshot WHERE block > $startBlock AND block < $endBlock";
$result = mysql_query($query);

error_log("Query:" . $query);

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
        array_push($blockHeightSeries['data'], intval($row['block']));
        array_push($diffSeries['data'], floatval($row['diff']));
        array_push($hashSeries['data'], floatval($row['nethash']));
        array_push($timeSeries['data'], intval($row['timestamp']));
    }

    //gather info on block times
    $query = "SELECT * FROM block_time WHERE block > $startBlock AND block < $endBlock";
    $result = mysql_query($query);

    if($result) {
        $blockTimeSeries = array("name" => "Block Time",
            "xAxis" => 0,
            "yAxis" => 0,
            "color" => '#000066',
            "lineWidth" => 2,
            "data" => array());

        while($row = mysql_fetch_array($result)) {
            array_push($blockTimeSeries['data'], array(intval($row['block']), intval($row['totaltime'])));
        }

        $jsonResult = array($blockHeightSeries, $diffSeries, $hashSeries, $timeSeries, $blockTimeSeries);
    }

    echo json_encode($jsonResult);
}else{
    echo json_encode(array('error' => 'Database error: ' . var_dump($result)));
}


?>