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

$teamFilter = null;

if( isset( $_GET['team'] ) && !empty( $_GET['team'] )) {
    
    $teamFilter = $_GET['team'];
    
}

$typesList = $_main -> getTypeList( $teamFilter );

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

    .main > .container .form-group > .add-user-input {

        height: 40px;

        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        border-radius: 0px;

        padding: 10px;

    }
    
</style>
<div class="main">
    <div class="headline" style="width:1300px;margin-top: 30px">
        <div class="name" style="line-height: 60px;">Page types</div>

        <div class="hlright">

        </div>

    </div>
    <div class="subHeadline" style="width:1300px;margin-bottom: 40px">
        <h4 style="margin-left:14px; margin-bottom: 10px; color: #405272; font-size: 16px!important"><b><?= $userData['fullname'] ?>
                , <?= $userData['position'] ?></b></h4>
    </div>
    <div class="main">

        <div class="container col-md-12" style="margin-bottom: 50px;">
            
            <h4><b>Add page type</b></h4>

            <hr>

            <form action="" id="add-type-form">

                <div class="form-group">

                    <label for="add-type-name">Title</label>
                    <input id="add-type-name" class="form-control add-user-input" type="text">

                </div>

                <div class="form-group">
                    <label for="add-type-team">Team</label>
                    <select class="form-control add-user-input" id="add-type-position">

                        <?php

                        foreach ( $teams as $key => $value ) {

                            echo '<option value="'.$key.'" data-name="'.$value.'">'.$value.'</option>';

                        }

                        ?>

                    </select>
                </div>

                <p id="message"></p>

                <button id="add-type-button" class="newreport">Add page type</button>

            </form>

            <button class="myreports" style="margin-top: 20px;" onclick="document.location = 'users.php?id=<?php echo $id; ?>'" >Back</button>
            
        </div>
        
        <div class="container col-md-4" style="margin-bottom: 30px;">
            
            <h4 style="padding-bottom: 10px;">Filter</h4>

            <form action="<?php echo 'pageTypes.php' ?>" type="GET" id="filter-type-form">

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

            </form>
            
        </div>
        
        <div class="dayTable">
            
            <table class="dayView" id="statsTable">

                <thead>

                    <tr style="background-color: #ebebeb;height: 25px;font-weight: bold;">

                        <td>#</td>
                        <td>ID</td>
                        <td>Code</td>
                        <td>Title</td>
                        <td>Team</td>
                        <td>Sub-team</td>

                    </tr>

                </thead>

                <tbody>

                    <?php
                    
                        if( count( $typesList ) > 0 ) {
                            
                            $counter = 1;

                            foreach ($typesList as $type) {

                                echo '<tr>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $counter . '</td>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $type["id"] . '</td>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $type["code"] . '</td>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $type["title"] . '</td>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $type["team"] . '</td>
                                           <td style="cursor: default;padding: 6px 6px 6px 15px;">' . $type["sub_team"] . '</td>
                                      </tr>';
                                
                                $counter++;

                            }
                        
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
</div>

<script>

    $("title").html("Page Types | Reports Panel");

    $("#statsTable").DataTable( {

        "paging" : false,
        "sDom": ""


    } );

    $(document).ready(function(){

        $("#add-type-button").click(function(e){

            e.preventDefault();

            var title = $("#add-type-name").val();
            var teamCode = $("#add-type-position").val();

            if ( title == "" || title == undefined || teamCode == "" || teamCode == undefined ) {

                $("#message").addClass("text-danger");
                $("#message").removeClass("text-success");
                $("#message").html( "Error gathering data. Please make sure all fields are filled out and properly selected." );

            } else {

                $.post('includes/adapter.php', { action:'addPageType', title:title, team_code:teamCode }, function(data){

                    var data = $.parseJSON(data);

                    if ( data['success'] == true ) {

                        $("#message").removeClass("text-danger");
                        $("#message").addClass("text-success");
                        $("#message").html(data['message']);

                    } else {
                        
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