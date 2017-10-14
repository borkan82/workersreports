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

$a_date = Date("Y-m-h");
$godina = Date("Y");
$mjesec = Date("m", strtotime($date . " - 1 month"));
$defaultDateFrom = $godina . "-" . $mjesec . "-01";
$daysNum = date("Y-m-t", strtotime($a_date));
//$defaultDateTo = $daysNum;
$defaultDateTo = $godina . "-" . $mjesec . "-31";

$datum = Date("Y-m-d");
$monthLess = date('F', strtotime($date . " - 1 month"));

$subTeamList = $_main -> getSubTeams();

$userAdmin = false;
if ($userData['role'] == "A" || $userData['role'] == "S") {
    $userAdmin = true;
}

$userTeam = $userData['team'];
include INC_PATH . 'header.php';
?>
<body style="width:1280px; margin: auto">
<style>

    <?php echo file_get_contents("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"); ?>

</style>
<div class="main">
    <div class="headline" style="width:1300px;margin-top: 30px">
        <div class="name" style="line-height: 60px;"> Add user</div>

        <div class="hlright">

        </div>

    </div>
    <div class="subHeadline" style="width:1300px;margin-bottom: 40px">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><b><?= $userData['fullname'] ?>
                , <?= $userData['position'] ?></b></h4>
    </div>
    <div class="main">

        <div class="container col-md-12">

            <form action="" id="add-user-form">

                <div class="form-group">

                    <label for="add-user-name">Name</label>
                    <input id="add-user-name" class="form-control add-user-input" type="text">

                </div>

                <div class="form-group">
                    <label for="add-user-position">Position</label>
                    <select class="form-control add-user-input" id="add-user-position">

                        <?php

                        foreach ( $teams as $key => $value ) {

                            echo '<option value="'.$key.'" data-name="'.$value.'">'.$value.'</option>';

                        }

                        ?>

                    </select>
                </div>

                <div id="add-user-sub-position" class="form-group">

                    <label for="add-user-sub-position">Sub-team</label>

                    <select id="add-user-sub-position-input" class="form-control add-user-input">

                        <option value="-1">No sub-team</option>

                        <?php

                        foreach ( $subTeamList as $subTeam ) {

                            $subTeamId = $subTeam['id'];
                            $subTeamTitle = $subTeam['title'];

                            echo '<option value="'.$subTeamId.'" data-name="'.$subTeamTitle.'">'.$subTeamTitle.'</option>';

                        }

                        ?>

                    </select>

                </div>

                <div class="form-group">
                    <label for="add-user-role">Role</label>
                    <select class="form-control add-user-input" id="add-user-role">

                        <?php

                        foreach ( $roles as $key => $value ) {

                            echo '<option value="'.$key.'" data-name="'.$value.'">'.$value.'</option>';

                        }

                        ?>

                    </select>
                </div>

                <p id="message"></p>
                <p id="access-code" class="text-danger">Access code will be visible here when generated</p>

                <button id="add-user-button" class="newreport">Add user</button>

            </form>

            <button class="myreports" style="margin-top: 20px;" onclick="document.location = 'users.php?id=<?php echo $id; ?>'" >Back</button>

        </div>

    </div>

    <div style="clear:both"></div>
    <div class="tableHolder col-md-12"
         style="font-size:14px; margin-top:45px; margin-bottom: 20px; width:100%; text-align: center;">
        <i>Workers Performance</i>
    </div>
</div>

<script>

    $("title").html("Add User | Reports Panel");

    $(document).ready(function(){

        var designerCode = "DS";

        if ( $("#add-user-position").val() == designerCode ) {

            $("#add-user-sub-position").fadeIn();

        } else {

            $("#add-user-sub-position").fadeOut();

        }

        $("#add-user-position").change(function(){

            if ( $("#add-user-position").val() == designerCode ) {

                $("#add-user-sub-position").fadeIn();

            } else {

                $("#add-user-sub-position").fadeOut();

            }

        });

        $("#add-user-button").click(function(e){

            e.preventDefault();

            var username = $("#add-user-name").val();
            var teamSelect = document.getElementById('add-user-position');
            var team = teamSelect.options[teamSelect.selectedIndex].text;
            var teamCode = $("#add-user-position").val();
            var role = $("#add-user-role").val();
            var subTeam = "";

            if ( $("#add-user-position").val() == designerCode ) {

                subTeam = $("#add-user-sub-position-input").val();

            } else {

                subTeam = -1;

            }

            if ( username == "" || username == undefined || team == "" || team == undefined || teamCode == "" || teamCode == undefined || role == "" || role == undefined ) {

                $("#message").addClass("text-danger");
                $("#message").removeClass("text-success");
                $("#message").html( "Error gathering data. Please make sure all fields are filled out and properly selected." );

            } else {

                $.post('includes/adapter.php', { action:'addUser', username:username, team_code:teamCode, team:team, role:role, subteam:subTeam }, function(data){

                    var data = $.parseJSON(data);

                    if ( data['success'] == true ) {

                        $("#access-code").removeClass("text-danger");
                        $("#access-code").addClass("text-success");
                        $("#message").removeClass("text-danger");
                        $("#message").addClass("text-success");
                        $("#message").html(data['message']);
                        $("#access-code").html("The access code for " + username + " is " + data['accessCode']);

                    } else {


                        $("#access-code").addClass("text-danger");
                        $("#access-code").removeClass("text-success");
                        $("#message").addClass("text-danger");
                        $("#message").removeClass("text-success");
                        $("#message").html(data['message']);

                    }

                });

            }

        });

    });

</script>

</div>
</body>
</html>