<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: Wed, 12 Dec 1990 12:12:12 GMT");
include 'includes/config.php';
include CLASS_PATH . 'classMain.php';
require INC_PATH . 'arrayDefines.php';


$_main = new Main($db);

$id = urldecode($_GET['id']);

$userData = $_main->getUserData($id);

if ($userData == 0 || $userData['role'] !== "S") {
    echo '<div class="headline" style="width:1100px;">Permission Denied</div>';
    exit;
}



$userAdmin = false;
if ($userData['role'] == "A" || $userData['role'] == "S") {
    $userAdmin = true;
}

// Default to the first day of the current month
$from = date( "d-m-Y", strtotime('first day of this month') );
$to = null;
$teamFilter = null;

if ( isset($_GET['from']) && !empty($_GET['from']) ) {

    $from = $_GET['from'];

}

if ( isset($_GET['to']) && !empty($_GET['to']) ) {

    $to = $_GET['to'];

}

if( isset( $_GET['team'] ) && !empty( $_GET['team'] )) {

    $teamFilter = $_GET['team'];

}

$userTeam = $userData['team'];

$typesMain = $_main -> getReportItems( $teamFilter, $from, $to );
$types = $typesMain[ 'types' ];
$dates = $typesMain[ 'dates' ];
$maxValue = $typesMain[ 'maxValue' ];


include INC_PATH . 'header.php';

?>
<body style="width:1280px; margin: auto">
<style>

    <?php echo file_get_contents("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"); ?>

    .main > .container .form-group > .add-user-input {

        height: 40px !important;

        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        border-radius: 0px;

        padding: 10px;

    }

    .typesTable table > thead > tr > th {
        
        min-width: 100px !important;
        
    }

</style>
<div class="main">
    <div class="headline" style="width:1300px;margin-top: 30px">
        <div class="name" style="line-height: 60px;">Task Stats</div>

        <div class="hlright">

        </div>

    </div>
    <div class="subHeadline" style="width:1300px;margin-bottom: 40px">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><b><?= $userData['fullname'] ?>
                , <?= $userData['position'] ?></b></h4>
    </div>
    <div class="main">
        
        <div class="container col-md-8" style="margin-bottom: 30px;">

            <h3 style="padding-bottom: 10px;font-weight: bold;">Filter</h3>

            <form action="<?php echo 'taskStats.php' ?>" type="GET" id="filter-type-form" onsubmit="cleanUrl()">
                
                <div class="form-group">

                    <label for="datumFrom">From:</label>

                    <input name="from" style="display: inline;" type="text" class="form-control add-user-input" id="datumFrom" placeholder="Date From" value="<?= $from ?>">
                    
                    <label for="datumTo">To:</label>

                    <input name="to" style="display: inline;" type="text" class="form-control add-user-input" id="datumTo" placeholder="Date To" value="<?= $to ?>">
                    
                </div>

                <div class="form-group">

                    <label for="filter-type-team">Team:</label>

                    <select class="form-control add-user-input" name="team" id="add-type-team">

                        <option value>Any team</option>

                        <?php

                        foreach ( $teams as $key => $value ) {

                            if ( $teamFilter != null && $teamFilter == $key ) {

                                echo '<option value="' . $key . '" data-name="' . $value . '" selected="selected">' . $value . '</option>';

                            } else {

                                echo '<option value="' . $key . '" data-name="' . $value . '">' . $value . '</option>';

                            }
                        }

                        ?>

                    </select>

                    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id">
                    
                </div>

                <button type="submit" class="search filter">Filter</button>

                <button class="search filter" class="form-control" id="clear-filter-form">Clear filter</button>
                
                <button class="myreports" type="button" style="margin-top: 20px;" onclick="document.location='users.php?id=<?php echo $id; ?>'" >Back</button>
                
            </form>

        </div>

        <div class="typesTable">

            <table class="typesView compact" id="statsTable" width="100%">

                <thead>

                <tr style="background-color: #ebebeb;height: 25px;font-weight: bold;">

                    <th>Date</th>
                    <?php

                    foreach ( $types as $type ) {

                        echo "<th>" . $type . "</th>";

                    }

                    ?>

                </tr>

                </thead>

                <tbody>

                <?php

                foreach ( $dates as $workHoursDate ) {

                    $key = key( $dates );

                    $key = date( "d-m-Y", strtotime( $key ) );

                    echo "<tr>";

                    echo "<td>" . $key . "</td>";

                    for( $i = 0; $i < count( $types ); $i++ ) {

                        echo "<td ";

                        if ( isset( $workHoursDate[$i] ) ) {
                            
                            $seconds = $_main -> timeToSeconds( $workHoursDate[$i] );
                            
                            $backgroundColor = $_main -> interpolateColor( $seconds, $maxValue );
                            
                            $colorAddon = "style='color:#fff;background-color:#" . $backgroundColor . ";'";

                            $hoursArray = explode( ":", $workHoursDate[$i] );

                            $timeString = $hoursArray[0] . ":" . $hoursArray[1];

                            echo $colorAddon . ">" . $timeString;

                        } else {

                            echo ">None";

                        }

                        echo "</td>";

                    }

                    echo "</tr>";

                    next( $dates );
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

    <script>

        $("title").html("Worker Stats | Task Stats");

        $("#datumFrom").datepicker({ dateFormat: 'dd-mm-yy' });

        $("#datumTo").datepicker({ dateFormat: 'dd-mm-yy' });

        $(".typesView").DataTable({

            "scrollX": true,
            "scrollY": true,
            fixedColumns: true,
            "autoWidth": false
            
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

            e.preventDefault();
            
            document.location = "taskStats.php?id=<?php echo $id; ?>";

        });

    </script>

</div>
