<?php

//error_reporting( E_ALL );
//ini_set('display_errors', 'On');

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: Wed, 12 Dec 1990 12:12:12 GMT");
include 'includes/config.php';
include CLASS_PATH . 'classMain.php';

$_main = new Main($db);
$id = urldecode($_GET['id']);

$userData = $_main->getUserData($id);

if ($userData == 0) {
    echo '<div class="headline" style="width:1100px;">Permission Denied</div>';
    exit;
}

if ( $userData < 1 ) {

    echo '<div class="headline" style="width:1100px;">User does not exist</div>';
    exit;
    
}

$uid = $userData['id'];

// Default to the first day of the current month
$from = date( "d-m-Y", strtotime('first day of this month') );
$to = null;

if ( isset($_GET['from']) && !empty($_GET['from']) ) {
    
    $from = $_GET['from'];
    
}

if ( isset($_GET['to']) && !empty($_GET['to']) ) {

    $to = $_GET['to'];

}

$pageTypes = $_main -> getTypeList( $userData['team'] );
$workHoursByPageType = $_main -> getTotalWorkHoursGroupedByType( $uid, $from, $to);
$workHoursByTypeAndDate = $_main -> getTotalWorkHoursByTypeAndDate( $uid, $from, $to );

$workHoursTypes = $workHoursByTypeAndDate[0];
$workHoursDates = $workHoursByTypeAndDate[1];

$workHoursPairs = array();
$workHoursDateArray = array();

$inTable = array();
$dateInTable = array();



foreach ( $pageTypes as $pageType ) {

    $typeId = $pageType['id'];

    $inTable[$typeId]['inTable'] = false;
    $inTable[$typeId]['title'] = $pageType['title'];
    
    if ( $typeId != null) {

        $inTable[$typeId]['id'] = $typeId;
        
    }

}

?>

<?php
include INC_PATH . 'header.php';
?>

<body>
<link href="css_/style.css" rel="stylesheet" type="text/css"/>
<div class="main">

    <div class="headline" style="padding-top: 10px!important;">
        <form>
            <div class="hlleft" style="margin-top: 0px!important;">
                <div class="name"><?= $userData['fullname'] ?></div>
                <?php
                if ($extId == "") {
                    ?>

                    <button class="newreport toolActive" type="button"
                            onclick="document.location = 'writeReport.php?id=<?php echo $id ?>';" style="color:#fff">New
                        report
                    </button>
                    <button class="myreports" id="myreports" type="button" onclick="document.location = 'viewReport.php?id=<?php echo $id ?>';">My
                        reports
                    </button>

                    <button class="myreports" id="myreports" type="button" onclick="document.location = 'myStats.php?id=<?php echo $id ?>';">My Stats
                    </button>

                    <?php
                }
                if ($userAdmin == true && $extId == "") {
                    echo '<button class="myreports" onclick="window.location = \'users.php?id=' . $id . '\';">View All Workers</button>';
                }
                ?>
            </div>
            <div class="hlright">
                <h4>Report from:</h4>
                <form action="myStats.php" method="GET">
                    <input name="from" type="text" id="datumFrom" placeholder=""
                           value="<?= $from ?>">
                    <h4>to</h4>
                    <input name="to" type="text" id="datumTo" placeholder=""
                           value="<?= $to ?>">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="extId" value="<?= $extId ?>">
                    <button class="search" type="submit">Filter results</button>
                </form>
            </div>
        </form>
    </div>

    <!-- < TYPES CHART > -->

    <div id="types-chart-container"></div>

    <!-- < / TYPES CHART > -->
    
    <div class="typeTable">
        
        <h2>Time spent per Type</h2>
        
        <table class="typeView">
            
            <thead>
            
                <tr>
                    
                    <td>#</td>
                    <td>Type</td>
                    <td>Time</td>
                    
                </tr>
            
            </thead>
            
            <tbody>
            
                <?php
                
                $count = 1;
                
                if ( !empty( $workHoursByPageType ) ) {
                    
                    foreach ( $workHoursByPageType as $key => $value ) {
                        
                        echo "<tr><td>" . $count . "</td><td>" . $key . "</td><td>" . $value . "</td></tr>";
                        
                        $count++;
                        
                    }
                    
                }
                
                ?>
            
            </tbody>
            
        </table>
        
    </div>
    
    <div class="dateTypeTable">

        <h2>Time spent per Type per Day</h2>
        
        <table class="dateTypeView">
            
            <thead>
            
                <tr>
                    
                    <td>Date</td>
                    <?php

                        foreach ( $workHoursTypes as $type ) {
                            
                            echo "<td>" . $type . "</td>";
                            
                        }
                    
                    ?>
                    
                </tr>
            
            </thead>

            <tbody>

                <?php

                    foreach ( $workHoursDates as $workHoursDate ) {
                        
                        $key = key( $workHoursDates );
                        
                        $key = date( "d-m-Y", strtotime( $key ) );

                        echo "<tr>";
                        
                        echo "<td>" . $key . "</td>";
                        
                        for( $i = 0; $i < count( $workHoursTypes ); $i++ ) {
                            
                            echo "<td>";
                            
                            if ( isset( $workHoursDate[$i] ) ) {
                                
                                $hoursArray = explode( ":", $workHoursDate[$i] );
                                
                                $timeString = $hoursArray[0] . ":" . $hoursArray[1];
                                
                                echo $timeString;
                                
                            } else {
                                
                                echo "None";
                                
                            }
                            
                            echo "</td>";
                            
                        }
                        
                        echo "</tr>";
                        
                        next( $workHoursDates );
                    }
                
                ?>

            </tbody>
            
        </table>
        
    </div>
    
</div>

<div style="clear:both"></div>
<div class="tableHolder col-md-12"
     style="font-size:14px; margin-top:45px; margin-bottom: 20px; width:100%; text-align: center;">
    <i>Workers Performance</i>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.15/sorting/date-eu.js"></script>
<script>

    $("title").html("Worker Stats | My Stats");
    
    $("#datumFrom").datepicker({ dateFormat: 'dd-mm-yy' });
    
    $("#datumTo").datepicker({ dateFormat: 'dd-mm-yy' });
    
    var series = [

        <?php

        $fullOutput = "";
        
        $control = 0;

        foreach ( $workHoursTypes as $workHoursType ) {

            $fullOutput .= "{";

            $fullOutput .= "name: '" . $workHoursType . "', ";

            $fullOutput .= "data: [";
            
            $seriesData = "";

            $seriesArray = array();
                
            //$i = array_search( array_search( $workHoursType, $workHoursTypes ) , array_keys( $workHoursTypes ) );

            reset( $workHoursDates );
            
            foreach ( $workHoursDates as $workHoursDate ) {
                
                $key = key( $workHoursDates );

                if ( isset( $workHoursDate[$control] ) ) {

                    $date = strtotime( $key . " -1 weekday" ) * 1000;

                    $minutes = round( $_main -> timeToSeconds( $workHoursDate[$control] ) / 60 );

                    if ( $date != 0 ) {

                        if (isset($seriesArray[$date])) {

                            $newValueOnDate = $seriesArray[$date] + $minutes;

                            $seriesArray[$date] = $newValueOnDate;

                        } else {

                            $seriesArray[$date] = $minutes;

                        }
                    }
                }
                
                next( $workHoursDates );

            }

            ksort( $seriesArray );

            foreach ( $seriesArray as $key => $value ) {

                $seriesData .= "[". $key . "," . $value ."],";

            }

            $seriesData = rtrim( $seriesData, " ," );

            $fullOutput .= $seriesData;
            
            $seriesArray = array();

            $fullOutput .= "]},";
            
            $control++;

        }

        $fullOutput = rtrim( $fullOutput, "," );

        echo $fullOutput;

        ?>
        
    ];

    
    Highcharts.chart( 'types-chart-container', {

        title: {

            text: 'Types Hours Chart'

        },
        xAxis: {

            title: {

                text: 'Date'

            },
            type: 'datetime',
            labels: {

                formatter: function () {

                    var val = this.value;

                    var date = new Date( val );

                    return (date.getDate()) + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                }

            }

        },
        tooltip: {

            formatter: function () {

                var time = this.y;

                var hours = Math.floor(time / 60);
                var minutes = time % 60;

                var pointDate = new Date( this.x );

                if( minutes < 10 ) {

                    minutes = "0" + minutes;

                }

                return "Date: " + pointDate.getDate() + "-" + ( pointDate.getMonth() + 1) + "-" + pointDate.getFullYear() + "<br/>" + this.series.name + ": <b>" + hours + ":" + minutes + "h</b>";

            }

        },
        yAxis: {

            title: {
                enabled: true,
                text: 'Time'

            },
            type: 'datetime',

            labels: {

                formatter: function () {

                    var time = this.value;

                    var hours = Math.floor(time / 60);
                    var minutes = time % 60;

                    if( minutes < 10 ) {

                        minutes = "0" + minutes;

                    }

                    return hours + ":" + minutes + "h";

                }

            }

        },
        series: series

    });
    
    $(".typeView").DataTable({
        
        sDom: ""
        
    });
    
    $(".dateTypeView").DataTable({
        
        sDom: "",
        paginate: false,
        columnDefs: [
            
            { type: 'date-eu', targets: 0 }
            
        ]
        
    });
    
</script>

<?php include INC_PATH . 'footer.php'; ?>
