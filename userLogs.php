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

$from = date( "d-m-Y", strtotime('first day of this month') );
$to = null;
$uid = null;

if ( isset( $_GET['dateFrom'] ) && !empty( $_GET['dateFrom'] ) ) {
    
    $from = urldecode( $_GET['dateFrom'] );
    
}

if ( isset( $_GET['dateTo'] ) && !empty( $_GET['dateTo'] ) ) {

    $to = urldecode( $_GET['dateTo'] );

}

if ( isset( $_GET['uid'] ) && !empty( $_GET['uid'] ) ) {

    $uid = $_GET['uid'];

}

$logs = $_main -> getUserLogs( $uid, $from, $to );
$users = $_main -> getUsersList();

include INC_PATH . 'header.php';
?>
<body style="width:1280px; margin: auto">
<style>

    <?php echo file_get_contents("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"); ?>

</style>
<div class="main">
    <div class="headline" style="width:1300px;margin-top: 30px">
        <div class="name" style="line-height: 60px;"> User Logs</div>

        <div class="hlright">

        </div>

    </div>
    <div class="subHeadline" style="width:1300px;margin-bottom: 40px">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><b><?= $userData['fullname'] ?>
                , <?= $userData['position'] ?></b></h4>
    </div>
    <div class="main">

        <div class="container col-md-12">

            <form action="userLogs.php" method="GET">

                <div class="form-row">
                    
                    <div class="form-group col-md-6">


                        <label for="filter-from" class="col-form-label">From</label>
                        <input id="filter-from" name="dateFrom" type="text" class="form-control add-user-input" value="<?php if ( $from != null ) { echo $from; } ?>">
                        
                    </div>
                    
                    <div class="form-group col-md-6">

                        <label for="filter-to" class="col-form-label">To</label>
                        <input type="text" name="dateTo" id="filter-to" class="form-control add-user-input" value="<?php if ( $to != null ) { echo $to; } ?>">
                        
                    </div>
                    
                    <div class="form-group col-md-12">

                        <label for="filter-user" class="col-form-label">User</label>
                        <select name="uid" id="filter-user" class="form-control add-user-input">

                            <option value="">All users</option>
                            
                            <?php
                                foreach ( $users as $user ) {
                                    
                                    if ( $user['active'] ) {
                                        
                                        $selected = "";
                                        
                                        if( $uid != null && $user['id'] == $uid ) {
                                            
                                            $selected = "selected='selected'";
                                            
                                        }
                                        
                                        echo "<option value=" . $user['id'] . " " . $selected . ">" . $user['fullname'] . "</option>";
                                        
                                    }
                                    
                                }
                            
                            
                            ?>
                            
                        </select>
                        
                    </div>

                    <div class="form-group col-md-2">

                        <input type="submit" class="search filter" value="Filter">
                        <input type="button" class="search" value="Back" onclick="document.location = 'users.php?id=<?php echo $id; ?>'">

                    </div>
                    
                </div>

                <input type="hidden" value="<?php echo $id ?>" name="id">

            </form></br>

            <!--<button class="myreports" style="margin-top: 20px;" onclick="document.location = 'users.php?id=<?php echo $id; ?>'" >Back</button>-->
            
            <div class="logsTable">

                <table class="logsView compact">

                    <thead>

                        <tr>

                            <td>ID</td>
                            <td>User</td>
                            <td>Time</td>
                            <td>IP</td>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        

                            foreach ( $logs as $log ) {

                                $logUser = $_main -> realGetUserData( $log['userId'] );

                                echo "<tr>";

                                echo "<td>" . $log['id'] . "</td>";

                                echo "<td>" . $logUser['fullname'] . "</td>";

                                echo "<td>" . $log['logDateTime'] . "</td>";
                                
                                echo "<td>" . $log['ip'] . "</td>";

                                echo "</tr>";

                            }

                        ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div style="clear:both"></div>
    <div class="tableHolder col-md-12"
         style="font-size:14px; margin-top:45px; margin-bottom: 20px; width:100%; text-align: center;">
       <i>Workers Performance</i>
    </div>
</div>

<script>

    $("title").html("User Logs | Reports Panel");
    
    $("#filter-from").datepicker({ dateFormat: 'dd-mm-yy' });
    
    $("#filter-to").datepicker({ dateFormat: 'dd-mm-yy' });
    
    $(".logsView").DataTable({
        
        paginate: false,
        sDom: ""
        
    });
    

</script>

</div>
</body>
</html>