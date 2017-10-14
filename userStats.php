<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: Wed, 12 Dec 1990 12:12:12 GMT");
include 'includes/config.php';
include INC_PATH . 'arrayDefines.php';
include CLASS_PATH . 'classMain.php';


$_main = new Main($db);

$id = urldecode($_GET['id']);

$userData = $_main->getUserData($id);

if ($userData == 0 || $userData['role'] !== "S") {
    echo '<div class="headline" style="width:1100px;">Permission Denied</div>';
    exit;
}

$from = date( "d-m-Y", strtotime('first day of this month') );
$to;
$userslist;


if(isset($_GET['fromDate']) && !empty($_GET['fromDate']))
{

    $from = mysql_real_escape_string(urldecode($_GET['fromDate']));
}

/* date to query */
if(isset($_GET['toDate']) && !empty($_GET['toDate']))
{
    $to = mysql_real_escape_string(urldecode($_GET['toDate']));
}

if ( isset( $_GET['team'] ) && !empty( $_GET['team'] ) && $_GET['team'] !== 0 ) {

    $userslist = $_main -> getUsersListFromTeam( $_GET['team'] );

} else {

    $userslist = $_main->getUsersList();

}

$statsData = $_main -> getTotalHomeHoursGroupedByDate( $from, $to );

$a_date = Date("Y-m-h");
$godina = Date("Y");
$mjesec = Date("m", strtotime($date . " - 1 month"));
$defaultDateFrom = $godina . "-" . $mjesec . "-01";
$daysNum = date("Y-m-t", strtotime($a_date));
//$defaultDateTo = $daysNum;
$defaultDateTo = $godina . "-" . $mjesec . "-31";

$datum = Date("Y-m-d");
$monthLess = date('F', strtotime($date . " - 1 month"));


$userAdmin = false;
if ($userData['role'] == "A" || $userData['role'] == "S") {
    $userAdmin = true;
}
$userTeam = $userData['team'];
include INC_PATH . 'header.php';

?>
<body>
<style>


    .clickHours {
        color: #444;
        cursor: pointer;
    }

    .clickHours:hover {
        color: #000;
        text-decoration: underline;
    }

    .paidButton {
        float: left;
        width: 100px;
        height: 20px;
        font-size: 12px;
        margin: 0
        text-align: center;
        line-height: 20px;
        display: inline-block;
        border: 1px solid #89c3eb;
        font-family: arial, helvetica, sans-serif;
        font-weight: bold;
        color: #fff;
        text-align: center;
        background: #5ACE6D;
        cursor: pointer;
        border-radius: 1px;
    }

    .dayTable > .dayView > tbody > tr > td {

        cursor: default;

    }

    .dayTable > .dayView > tbody > tr > .table-user-name:hover {

        cursor: pointer;

    }

    /* Last report status CSS */

    .report-status {

        width: 15px;
        height: 15px;
        margin: auto;

    }

    .report-status-good {

        background-color: #5ACE6D;

    }

    .report-status-bad {

        background-color: #cc0000;

    }

    /* End of report status CSS */

    /* Dropdown CSS */

    .dropbtn {
        background-color: #789cda;
        color: white;
        width: 110px;
        height: 35px;
        padding: 1px 6px;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }


    .dropdown {
        position: relative;
        display: inline-block;
    }


    .dropdown-content {
        display: none;
        position: absolute;
        background-color: rgba( 0, 0, 0, .5 );
        min-width: 130px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }


    .dropdown-content .search {
        width: 130px;
        margin: 0;
        text-decoration: none;
        display: block;
    }


    .dropdown-content .search:hover {background-color: #4a628c}


    .dropdown:hover .dropdown-content {
        display: block;
    }


    .dropdown:hover .dropbtn {

        background-color: #4a628c;

    }

    /* End of dropdown CSS */

</style>
<div class="main">

    <div class="headline" style="width:1300px;margin-top: 20px;">
        <div class="name" style="line-height: 60px;"> Worker Stats</div>

        <div class="hlright">

        </div>

    </div>
    <div class="subHeadline" style="width:1300px;">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><?= $userData['fullname'] ?>
            , <?= $userData['position'] ?></h4></div>
    <div class="toolBar">
        <button class="search" onclick="document.location = 'performance.php?id=<?php echo $id ?>';"
                style="width:200px;">Product performance
        </button>

        <button class="search" onclick="document.location = 'users.php?id=<?php echo $id ?>'">Worker List</button>

        <button class="search" onclick="document.location = 'userLogs.php?id=<?php echo $id ?>';" >User Logs</button>

        <button class="search" onclick="document.location = 'taskStats.php?id=<?php echo $id ?>';" >Type Stats</button>

        <div class="dropdown">

            <button class="dropbtn">&#9660; Settings</button>

            <div class="dropdown-content">

                <button class="search" onclick="document.location = 'addUser.php?id=<?php echo $id; ?>'" > Add new User </button>
                <button class="search" onclick="document.location = 'pageTypes.php?id=<?php echo $id ?>'" >Add Page Type</button>


            </div>

        </div>

        <?php

        if ($userAdmin == true) {
            echo '<div class="subheading" style="height:38px;margin-top:10px; margin-bottom: 5px;"><span class="hours" style="font-size:30px;">VIEW ALL WORKERS</span></div>';
        }

        ?>

        <!-- < HOME WORK CHART > -->

        <div id="home-work-chart-container"></div>

        <!-- < / HOME WORK CHART > -->


        <h4>Filter:</h4>
        <form id="stats-filter-form" action="userStats.php" method="GET" onsubmit="cleanUrl()">

            <input name="fromDate" style="display: inline;" type="text" class="form-control" id="datumFrom" placeholder="Date From" value="<?= $from ?>">

            <input name="toDate" style="display: inline;" type="text" class="form-control" id="datumTo" placeholder="Date To" onclick="$(this).datepicker({ dateFormat: 'dd-mm-yy' });" value="<?= $to ?>">

            <select class="filter-dropdown" style="display: inline;" name="team" id="filter-team-select">

                <option value="0">Any team</option>

                <?php

                foreach ( $teams as $key => $value ) {

                    if ( isset($_GET['team']) && $key == $_GET['team'] ) {

                        echo "<option value='" . $key . "' selected='selected'>" . $value . "</option>";

                    } else {

                        echo "<option value='" . $key . "'>" . $value . "</option>";

                    }
                }

                ?>

            </select>

            <input style="display: inline;" type="hidden" name="id" value="<?= $_GET['id'] ?>">
            <button class="search filter" class="form-control" onclick="SearchFormSimple.search(this);">Filter results</button>
            <button class="search filter" class="form-control" id="clear-filter-form">Clear filter</button>
        </form>

    </div>
    <div class="dayTable" style="margin-top: 20px;">
        <table class="dayView" id="statsTable">
            <thead>
            <tr style="background-color: #ebebeb;height: 25px;font-weight: bold;">

                <td>#</td>
                <td>ID</td>
                <td>Name</td>
                <td>Team</td>
                <td>Office hours</td>
                <td>Home hours</td>
                <td>Total hours</td>
                <td>Total days</td>
                <td>Average Report submit time</td>
                <td>Average work hours per report</td>

            </tr>
            </thead>
            <tbody>
            <?php
            $countTab = 1;

            if (!empty($userslist)) {
                foreach ($userslist as $row) {
                    if ($row['active'] == 1) {
                        //var_dump($row['code']);
                        $hourNum = 0;
                        $minNum = 0;

                        // LAST REPORT DATA

                        // *** yesterday in $yesterdayDate and $yesterdayName refers to the last weekday
                        // *** not literally yesterday

                        $lastReport = $_main -> getLastReport( $row['id'] );
                        $lastReportDate = date( 'Ymd', strtotime ( $lastReport[0]['date'] ) );
                        $yesterdayDate = date( 'Ymd', strtotime( 'last weekday' ) );
                        $yesterdayName = date( 'l', strtotime( 'last weekday' ) );
                        $statusHTML = "<div class='report-status report-status-bad' title='Missing last(" . $yesterdayName . ") report'></div>";

                        if ( $lastReportDate == $yesterdayDate ) {

                            $statusHTML = "<div class='report-status report-status-good' title='All reports in order'></div>";

                        }

                        // END OF LAST REPORT DATA

                        $paymentDate = $_main->getPaymentDate($row['id']);
                        $peroidFrom = Date('Y-m-d', strtotime($paymentDate["paymentDate"] . '+1 day'));
                        $listHome = $_main->getHomeHours($row['id'], $peroidFrom, $defaultDateTo);

                        // WORKING HOURS

                        $fromDate = $from;
                        $toDate = $to;

                        /*if ( isset( $_GET['fromDate'] ) ) {

                            $fromDate = $_GET['fromDate'];

                        }

                        if ( isset( $_GET['toDate'] ) ) {

                            $toDate = $_GET['toDate'];

                        }*/

                        $officeHours = $_main -> getWorkingHours( $row['id'], $row['team'], 2, $fromDate, $toDate );
                        $homeHours = $_main -> getWorkingHours( $row['id'], $row['team'], 3, $fromDate, $toDate );
                        $totalHours = $_main -> getWorkingHours( $row['id'], $row['team'], 1,$fromDate, $toDate );
                        $totalDays = $_main -> getNumOfReports( $row['id'], $row['team'], $fromDate, $toDate );
                        $averageReportSubmitTime = $_main -> getAverageReportTime( $row['id'], $fromDate, $toDate );
                        $averageWorkHoursPerReport = round( $_main -> getWorkingHours( $row['id'], $row['team'], 2, $fromDate, $toDate, true)[ 'hours' ] / $totalDays, 2);

                        $homeHoursSort = str_replace( ":", "", $homeHours );
                        $officeHoursSort = str_replace( ":", "", $officeHours );
                        $totalHoursSort = str_replace( ":", "", $totalHours );

                        if ( $averageWorkHoursPerReport == 0 ) {

                            $averageWorkHoursPerReport = "N/A";

                        }

                        // A bug fix. Users with no reports had the average report submit time displayed as 01:00
                        if ( $averageReportSubmitTime == "01:00" ) {

                            if ( isset( $fromDate ) || isset( $toDate )) {

                                $averageReportSubmitTime = "None";

                            } else {

                                $averageReportSubmitTime = "Never";

                            }

                        }

                        // Zeroes are ugly, so for aesthetics...
                        if ( $officeHours == "0:00" ) {

                            $officeHours = "None";

                        }

                        if ( $homeHours == "0:00" ) {

                            $homeHours = "None";

                        }

                        if ( $totalHours == "0:00" ) {

                            $totalHours = "None";

                        }

                        // END OF WORKING HOURS DATA


                        foreach ($listHome as $hours) {
                            $divide = explode(":", $hours["duration"]);
                            $hourNum = $hourNum + $divide[0];
                            $minNum = $minNum + $divide[1];
                        }


                        $minToHour = round($minNum / 60);
                        $hourNum = $hourNum + $minToHour;
                        $totalHomeHours = $hourNum + $minToHour;

                        if ($userTeam == $row['team'] || $userData['role'] == "S") {
                            //$designerId = $_encrypt->encrypt($row['id']);
                            $designerId = $row['code'];

                            if ($hourNum == 0) {
                                $hourNum = "";
                            } else {
                                $hourNum .= " - (view)";
                            }
                            echo '<tr >';
                            echo '<td style="cursor: default;">' . $countTab . '</td><td style="cursor: default;">' . $row['id'] . '</td><td class="table-user-name" style="text-align:left; cursor:pointer;" onclick="window.open( \'viewReport.php?id=' . $designerId . '&extId=' . $id . '\', \'_blank\');">' . $row['fullname'] . '</td><td style="text-align:left;cursor: default;">' . $row['team'] . '</td><td style="text-align:left;cursor: default;" data-sort="' . $officeHoursSort . '">' . $officeHours . '</td><td style="text-align:left;cursor: default;" data-sort="' . $homeHoursSort . '">' . $homeHours . '</td><td style="text-align:left;cursor: default;" data-sort="' . $totalHoursSort .'">' . $totalHours . '</td><td style="text-align:left;cursor: default;">' . $totalDays . '</td><td style="text-align:left;cursor: default;">' . $averageReportSubmitTime . '</td><td style="text-align:left;cursor: default;">' . $averageWorkHoursPerReport . '</td>';

                            $countTab++;
                        }
                    }
                }
            } else {
                echo '<tr>';
                echo '<td colspan="7"><span style="color:#f26100;font-weight: bold;">There are no users!</span></td>';
                echo '</tr>';
            }


            ?>
            </tbody>
        </table>
    </div>

    <div class="tableHolder"
         style="font-size:14px; margin-top:45px; margin-bottom: 20px; width:auto; text-align: center;">
        <i>Workers Performance</i>
    </div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
</body>

</html><script>

    $("title").html("Worker Stats | Reports Panel");

    $("#statsTable").DataTable( {

        "paging" : false


    } );
    
    $("#datumFrom").datepicker({ dateFormat: 'dd-mm-yy' });
    $("#datumTo").datepicker({ dateFormat: 'dd-mm-yy' });

    Highcharts.chart('home-work-chart-container', {

        title: {

            text: 'Home Work Chart'

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

                    return date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
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
                text: 'Home Work Hours'

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
        series: [{

            name: 'Home Work Hours',

            data: [

                <?php

                $numberOfStats = count( $statsData );
                $i = 0;

                foreach ( $statsData as $key => $value ) {

                    $append = ",";

                    if ( ++$i === $numberOfStats ) {

                        $append = "";

                    }

                    echo "[" . $key . ", " . $value . "]" . $append . " ";

                }

                ?>

            ]
        }]

    });

    function cleanUrl() {

        var statsFilterForm = document.getElementById( 'stats-filter-form' );
        var inputs = statsFilterForm.getElementsByTagName( 'input' );
        var selects = statsFilterForm.getElementsByTagName( 'select' );

        // For the inputs
        for ( i = 0; input = inputs[i]; i++ ) {

            if ( input.getAttribute( "name" ) && !input.value ) {

                input.setAttribute( 'name', '' );

            }

        }

        // For the select
        for ( i = 0; select = selects[i]; i++ ) {

            if ( select.getAttribute( "name" ) && select.value == '0' ) {

                select.setAttribute( 'name', '' );

            }

        }
    }

    $("#clear-filter-form").click(function(e){

        var statsFilterForm = document.getElementById( 'stats-filter-form' );
        var inputs = statsFilterForm.getElementsByTagName( 'input' );
        var selects = statsFilterForm.getElementsByTagName( 'select' );

        // For the inputs
        for ( i = 0; input = inputs[i]; i++ ) {

            if ( input.getAttribute( 'type' ) != 'hidden' ) {

                input.setAttribute('value', '');

            }
        }


        $("#filter-team-select > option:eq(0)").prop('selected', true);

    });

</script>