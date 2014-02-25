<html>
    <head>
        <title>KittehCoin NetMetrics (last 48hrs initial dataset)</title>

        <!-- load jquery in head -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

        <script src="js/Highcharts-3.0.9/js/highcharts.js"></script>

        <script type="text/javascript">
        <?php
                require_once '../config.php';

                $startTime = time() - (60 * 60 * 5); //2days
                $endTime = time();

                $db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
                mysql_select_db(MYSQL_DB, $db_link);

                $query = "SELECT * FROM network_snapshot WHERE timestamp > $startTime AND timestamp < $endTime";
                $result = mysql_query($query);

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
                    $query = "SELECT * FROM block_time WHERE starttime > $startTime AND starttime < $endTime";
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

                }

                $db_link = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);
                mysql_select_db(MYSQL_DB, $db_link);

                $query = "SELECT * FROM block_time";
                $result = mysql_query($query);

                $blocks = array();
                while($row = mysql_fetch_array($result)) {
                    array_push($blocks, $row);
                }

                //store the start and end blocks so we can query subset data
                $startBlock = $blocks[0]['block'];
                $endBlock = $blocks[count($blocks)-1]['block'];
                $totalTracked = $endBlock - $startBlock;


            ?>

        function initGraph(blockheight, diff, hash, timestamps, blocktimes) {
            var a = new Date(timestamps.data[0] * 1000);
            var utc = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate(), a.getHours(), a.getMinutes(), a.getSeconds());

            $('#diffPlot').highcharts({
                chart: {
                    type: 'spline'
                },
                xAxis: [{
                    type: 'datetime',
                    maxZoom: 10 * 1000
                }],
                yAxis: [{
                    title: {
                        text: 'Block Height',
                        style: {
                            color: '#333333'
                        }
                    },
                    opposite: true},
                    {
                    title: {
                        text: 'Diff',
                        style: {
                            color: '#ff0000'
                        }
                    }},
                    {
                    title: {
                        text: 'Hash',
                        style: {
                            color: '#0000ff'
                        }
                    },
                    opposite: true}],
                series: [blockheight, diff, hash],
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    spline: {
                        lineWidth: 4,
                        states: {
                            hover: {
                                lineWidth: 5
                            }
                        },
                        marker: {
                            enabled: false
                        },
                        pointInterval: 1000, // 1s
                        pointStart: utc
                    }
                }
            });


        $('#timePlot').highcharts({
            chart: {
                type: 'spline'
            },
            xAxis: [{
                type: 'linear'
            }],
            yAxis: [{
                title: {
                    text: 'Block Time',
                    style: {
                        color: '#333333'
                    }
                },
                opposite: true}],
            series: [blocktimes],
            tooltip: {
                shared: true
            },
            plotOptions: {
                spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false
                    },
                    pointInterval: 1000, // 1s
                    pointStart: utc
                }
            }
        });
        }



        $(document).ready(function() {
           // loadGraphData(true);

            initGraph(<?php echo json_encode($blockHeightSeries); ?>, <?php echo json_encode($diffSeries); ?>, <?php echo json_encode($hashSeries); ?>, <?php echo json_encode($timeSeries); ?>, <?php echo json_encode($blockTimeSeries); ?>);

            $(function() {
                //find all form with class jqtransform and apply the plugin
               // $("form.jqtransform").jqTransform();
            });

            $('#loadDataBtn').click(function(e) {
                loadGraphData( $('#startBlock').val(), $('#endBlock').val());
            });
        });

        var diffPlot;

        function loadGraphData(startBlock, endBlock) {
            console.log("loadGraphData: " + startBlock + " to " + endBlock);
            $.get('ajax/getGraphData.php', {startBlock: startBlock, endBlock: endBlock}, function( data ) {
                var obj = $.parseJSON(data);
                initGraph(obj[0], obj[1], obj[2], obj[3], obj[4]);

                //setInterval(liveGraphData, 10000);
            });
        }

        function liveGraphData() {
            $.get('ajax/getLiveGraphData.php', function( data ) {
                var obj = $.parseJSON(data);
                console.debug(obj);
                addGraphPoints(obj[0], obj[1], obj[2], obj[3], obj[4]);
            });
        }

        function addGraphPoints(block, diff, net, time) {
            var chart = $('#diffPlot').highcharts();
            chart.series[0].addPoint(block.data[0]);
            chart.series[1].addPoint(diff.data[0]);
            chart.series[2].addPoint(net.data[0]);
        }

        </script>
    </head>

    <body>
        <div id="diffPlot"></div>
        <div id="timePlot"></div>
        <div id="controls">


            <form onsubmit="return false;">
                <label for="startBlock">Start Block: </label>
                <input name="startBlock" id="startBlock" type="text" width="200" />
                <label for="endBlock">End Block: </label>
                <input name="endBlock" id="endBlock" type="text" width="200" />
                <input id="loadDataBtn" type="submit" value="reload data"  />
            </form>

            <p>
                <b>Data loaded:</b><br />
                Start Block: <?php echo $startBlock; ?><br />
                End Block: <?php echo $endBlock; ?><br />
                Total Tracked: <?php echo $totalTracked; ?><br />
            </p>


        </div>
    </body>
</html>