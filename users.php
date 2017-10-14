<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: Wed, 12 Dec 1990 12:12:12 GMT");
include 'includes/config.php';
include CLASS_PATH . 'classMain.php';


$_main = new Main($db);

$id = urldecode($_GET['id']);

$userData = $_main->getUserData($id);

if ($userData == 0 || ($userData['role'] !== "A" && $userData['role'] !== "S")) {
    echo '<div class="headline" style="width:1100px;">Permission Denied</div>';
    exit;
}

$a_date = Date("Y-m-h");
$godina = Date("Y");
$mjesec = Date("m", strtotime($date . " - 1 month"));
$defaultDateFrom = $godina . "-" . $mjesec . "-01";
$daysNum = date("Y-m-t", strtotime($a_date));
//$defaultDateTo = $daysNum;
$defaultDateTo = $godina . "-" . $mjesec . "-31";

$datum = Date("Y-m-d");
$monthLess = date('F', strtotime($date . " - 1 month"));
$userslist = $_main->getUsersList();


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
        color: 444;
        cursor: pointer;
    }

    .clickHours:hover {
        color: 000;
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

    .report-status-warning {

        background-color: #ff8f0f;

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
        <div class="name" style="line-height: 60px;"> Worker List</div>
    </div>
    <div class="subHeadline" style="width:1300px;">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><?= $userData['fullname'] ?>
            , <?= $userData['position'] ?></h4></div>
    <div class="toolBar">
        <button class="search" onclick="document.location = 'performance.php?id=<?php echo $id ?>';"
                style="width:200px;">Product performance
        </button>

        <?php
        if ($userData['role'] !== "S") {
            ?>
            <button class="newreport" onclick="document.location = 'writeReport.php?id=<?php echo $id ?>';">NEW REPORT</button>
            <button class="search" onclick="document.location = 'viewReport.php?id=<?php echo $id ?>';">MY REPORTS</button>
            <?php
        }

        if( $userData['role'] == "S" ) {

            ?>

            <button class="search" onclick="document.location = 'userStats.php?id=<?php echo $id ?>';">User Statistics</button>
            
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
        }

        if ($userAdmin == true) {
            echo '<div class="subheading" style="height:38px;margin-top:10px; margin-bottom: 5px;"><span class="hours" style="font-size:30px;">VIEW ALL WORKERS</span></div>';
        }
        ?>
    </div>
    <div class="dayTable">
        <table class="dayView compact">
            <thead>
            <tr style="background-color: #ebebeb;height: 25px;font-weight: bold;">
                <td>#</td>
                <td>Name</td>
                <td>Position</td>
                <td>Team</td>
                <td>System Role</td>
                <td>Access Code</td>
                <td>Active</td>
                <td>Status</td>

                <?php

                    if ( $userData['role'] == "S" ) {

                        echo '<td style="text-align: center;">Home works for <?= $monthLess ?></td>
                        <td></td>
                        <td>Last payment</td>';

                    }
                ?>
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
                        $lastReportTime = date( 'H:i:s', strtotime( $lastReport[0]['date'] ) );
                        
                        
                        $yesterdayDate = date( 'Ymd', strtotime( 'last weekday' ) );
                        $yesterdayName = date( 'l', strtotime( 'last weekday' ) );
                        $statusHTML = "<div class='report-status report-status-bad' title='Missing last(" . $yesterdayName . ") report'></div>";
                        $statusSortingValue = 0;
                        
                        if ( $lastReportDate == $yesterdayDate ) {

                            $statusHTML = "<div class='report-status report-status-good' title='All reports in order'></div>";
                            $statusSortingValue = 2;

                        }

                        if ( $lastReport[0]['date'] ) {

                            $lastReportTimeSeconds = $_main -> timeToSeconds( $lastReportTime );
                            $threePMSeconds = $_main -> timeToSeconds( "15:00:00" );
                            $sixAMSeconds = $_main -> timeToSeconds( "06:00:00" );
                            
                            if ($lastReportTimeSeconds < $threePMSeconds && $lastReportTimeSeconds > $sixAMSeconds) {

                                $statusHTML = "<div class='report-status report-status-warning' title='Yesterdays report submitted early (" . $lastReportTime . ")'></div>";
                                $statusSortingValue = 1;
                                
                            }
                            
                        }

                        // END OF LAST REPORT DATA

                        $paymentDate = $_main->getPaymentDate($row['id']);
                        $peroidFrom = Date('Y-m-d', strtotime($paymentDate["paymentDate"] . '+1 day'));
                        $listHome = $_main->getHomeHours($row['id'], $peroidFrom, $defaultDateTo);


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
                            echo '<td>' . $countTab . '</td><td class="table-user-name" style="text-align:left;" onclick="window.open( \'viewReport.php?id=' . $designerId . '&extId=' . $id . '\', \'_blank\');">' . $row['fullname'] . '</td><td style="text-align:left;">' . $row['position'] . '</td><td style="text-align:left;">' . $row['team'] . '</td><td style="text-align:left;">' . $row['role'] . '</td><td style="text-align:left;">' . $designerId . '</td><td style="text-align:left;">Yes</td><td style="text-align:left;" data-sort="' . $statusSortingValue . '">' . $statusHTML . '</td>';

                            if( $userData['role'] == "S" ) {
                                echo '<td style="text-align:center;"><span class="clickHours" onclick="window.open( \'viewReport.php?from=' . $peroidFrom . '&to=' . $defaultDateTo . '&id=' . $designerId . '&extId=' . $id . '&home\', \'_blank\');">' . $hourNum . '</span></td><td>';

                                if ($totalHomeHours > 0) {
                                    echo '<div class="bigOrder paidButton" onclick="makePayment(' . $row["id"] . ',this)">Paid</div>';
                                }

                                echo '</td>';
                                echo '<td>' . $paymentDate["paymentDate"] . '</td>';
                                echo '</tr>';

                            }

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

    <div style="clear:both"></div>
    <div class="tableHolder"
         style="font-size:14px; margin-top:45px; margin-bottom: 20px; width:auto; text-align: center;">
       <i>Workers Performance</i>
    </div>
</div>
</body>
<script>


    $("title").html("Worker List | Reports Panel");
    
    $(".dayView").DataTable({
        
        paginate: false,
        sDom: ""
        
    });

    function makePayment(userId, e) {
        var r = confirm("Are you shure you want to mark hours as paid?");
        if (r == true) {
            var podaci = {action: "makePayment", id: userId};

            $.ajax({
                url: "includes/adapter.php",
                type: "POST",
                dataType: "JSON",
                data: podaci,
                async: true,
                success: function (data) {
                    if (data > 0) {
                        $(e).css('background-color', '#aaa');
                    } else {
                        showError("Error occured!");
                    }
                }
            });

        } else {
        }

    }

</script>
</html>