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
$sada = time();
$sadavrijeme = Date('H:i:s');
$trisata = strtotime("2016-03-16 15:00:00");


$userData = $_main->getUserData($id);
$_states = $_main->getStates();

$_products = $_main->getProducts();

if ($userData == 0) {
    echo '<div class="headline" style="width:1100px;">Permission Denied</div>';
    exit;
}


if ($userData > 0) {

} else {
    echo "User not exists!";
    exit;
}

if( $extId == "" && isset( $userData['id'] ) ) {
    
    $_main -> addUserLog( $userData['id'] );

}

$uid = $userData['id'];
$userAdmin = false;
if ($userData['role'] == "A") {
    $userAdmin = true;
}
$userTeam = $userData['team'];
$userSubTeam = unserialize($userData['sub_team']);
$disabledField = "";

if ($userTeam !== "DS" && $userTeam !== "WR") {
    $disabledField = ' disabled';
    $disabledStyle = 'background-color:#eee;';
}
if ($userTeam == "DS") {
    $types = $_main->getTypeListBySubTeam($userTeam, $userSubTeam);
} elseif ($userTeam != "DS") {
    $types = $_main->getTypeList($userTeam);
}

if (in_array(1, $userSubTeam)) {
    if (count($userSubTeam) == 1) {
        $_products=[];
    }
    $array = [];
    $array['id'] = 26;
    $array['sku'] = 000;
    $array['productType'] = 000;
    $array['title'] = 'SMS Chat';
    $array['description'] = 'na';
    array_push($_products,$array);

} elseif (in_array(2, $userSubTeam)) {
    if (count($userSubTeam) == 1) {
        $_products=[];
    }
    $array = [];
    $array['id'] = 26;
    $array['sku'] = 000;
    $array['productType'] = 000;
    $array['title'] = 'SMS Club';
    $array['description'] = 'na';
    array_push($_products,$array);

} elseif (in_array(5, $userSubTeam)) {
    if (count($userSubTeam) == 1) {
        $_products=[];
    }
    $array = [];
    $array['id'] = 26;
    $array['sku'] = 000;
    $array['productType'] = 000;
    $array['title'] = 'Tarot';
    $array['description'] = 'na';
    array_push($_products,$array);
}
?>

<?php
include INC_PATH . 'header.php';
?>
<body>
<link href="css_/style.css" rel="stylesheet" type="text/css"/>
<style>
    ._description {
        height: 25px;
        resize: vertical;
        border: 1px solid #ccc;
    }

    .toolDate {
        display: none;
        margin-top: 6px;
        margin-left: 25px;
        float: left;
    }

    .chosen-container.chosen-container-single {
        width: 150px !important;
    }
</style>
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
                <input name="from" type="text" id="datumFrom" placeholder="" onclick="$(this).datepicker();"
                       value="<?= $from ?>">
                <h4>to</h4>
                <input name="to" type="text" id="datumTo" placeholder="" onclick="$(this).datepicker();"
                       value="<?= $to ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="extId" value="<?= $extId ?>">
                <button class="search" type="button" onclick="linkToReportSearch('<?= $id ?>');">Filter results</button>
            </div>
        </form>
    </div>
    <div class="subHeadline tableup"><span style="margin-left:15px;">New report for: <?php echo Date('d.m.Y'); ?></span><span
            style="margin-left:720px;font-weight:normal;font-size:24px;">Current time:</span><span
            style="margin-left:5px;"><?php echo Date('H:i:s'); ?></span></div>
    <div class="dayTable">
        <form id="forma">
            <table class="dayView">
                <thead>
                <tr>
                    <td>Hours</td>
                    <td>Product/Task</td>
                    <td>State</td>
                    <td>Type</td>
                    <td>Description</td>
                    <td>Forum / Trello URL</td>
                    <td>Page URL</td>

                </tr>
                </thead>
                <tbody id="items" style="line-height: 40px;">

                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onfocus="clearBox(this)" onblur="addNull(this)" onkeyup="countLoggedHours();"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {
                                //var_dump($product);
                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?> >
                            <!--option value="19">Choose state</option-->

                            <?php
                            foreach ($_states as $state) {
                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro2" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro3" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro4" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                <tr class="reportItem">
                    <td>
                        <div style="width:130px;"><input type="text" class="_timeHour" name="timeHour"
                                                         style="width:30px;" onkeyup="countLoggedHours();"
                                                         onfocus="clearBox(this)" onblur="addNull(this)" value="00"> h
                            <input type="text" class="_timeMin" name="timeMin" style="width:30px;margin-right:5px;"
                                   onkeyup="countLoggedHours();" onfocus="clearBox(this)" onblur="addNull(this)"
                                   value="00"> min.
                        </div>
                    </td>
                    <td>
                        <select id="pro5" class="_product chosen-select-products" name="product"
                                style="width:150px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <option value="26">Other (not product related)</option>
                            <?php
                            foreach ($_products as $product) {

                                echo '<option value="' . $product["id"] . '">' . $product["sku"] . '-' . $product["productType"] . ' ' . $product["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_state" name="state"
                                style="width:60px;<?= $disabledStyle ?>" <?= $disabledField ?>>
                            <!--option value="19">Choose state</option-->
                            <?php
                            foreach ($_states as $state) {

                                if ($state['code'] != 'n/a') {
                                    echo '<option value="' . $state["id"] . '">' . $state["code"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select class="_type" name="type" style="width:150px;">
                            <option value="">Choose type</option>
                            <?php
                            foreach ($types as $type) {

                                echo '<option value="' . $type["id"] . '">' . $type["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><textarea class="_description" name="description"></textarea></td>
                    <td><input type="text" class="_thread" name="thread"></td>
                    <td><input type="text" class="_site" name="site"></td>
                </tr>
                </tbody>
            </table>
            <div class="bottomTable">
                <div class="countHours">Total logged: 0 h</div>
                <div class="addRowBtn" onclick="addNewReportRow();" style="cursor:pointer;">Add more rows +</div>
            </div>
            <input type="hidden" value="<?php echo $uid ?>" id="idNum">
            <button type="button" id="addReport" class="bigOrder" style="width:260px;font-size: 20px;"
                    onclick="addRep();">Save report
            </button>
            <button type="button" id="viewReport" class="bigOrder" style="width:260px;font-size: 20px;display:none;"
                    onclick="document.location = 'viewReport.php?id=<?php echo $id ?>';">VIEW REPORTS
            </button>
        </form>
    </div>
    <div style="clear:both"></div>
    <div class="tableHolder"
         style="font-size:14px; margin-top:30px;width:1300px;text-align:center;background:none;border:none;position: fixed;bottom: 10px;">
       <i>Daily reports app, v.1.03</i>
    </div>
</div>
<?php include INC_PATH . 'footer.php'; ?>
<script>

    $('#datumFrom,#datumTo').datepicker({
        dateFormat: "yy-mm-dd"
    });

    //*********************************************************************
    //**********SNIMANJE REPORTA  *****************************************
    //*********************************************************************
    function addRep() {
        var userId = $('#idNum').val();
        var status = true;
        var akcija = "";
        var allData = new Array();
        var reportItem = {};
        var exception = 0;
        akcija = "addReport";
        var totalHours = 0;
        var totalMinutes = 0;

        $("#forma").find(".reportItem").each(function () {
            var reportItem = {};
            var $this = $(this);
            reportItem.timeHour = $this.find("._timeHour").val();
            reportItem.timeMin = $this.find("._timeMin").val();
            reportItem.state = $this.find("._state").val();
            reportItem.product = $this.find("._product").val();
            reportItem.description = $this.find("._description").val();
            reportItem.thread = $this.find("._thread").val();
            reportItem.site = $this.find("._site").val();
            reportItem.type = $this.find("._type").val();
            if (reportItem.type == "13" || reportItem.type == "32" || reportItem.type == "48" || reportItem.type == "64") {
                exception = 1;
            }
            if ((reportItem.timeHour !== "00" || reportItem.timeMin !== "00") && reportItem.type == "") {
                showWarning("Type field cannot be empty!");
                status = false;
                return false;
            }

            if ((reportItem.timeHour == "" || reportItem.timeMin == "") && reportItem.type !== "") {
                showWarning("Hours and minutes fields cannot be empty!");
                status = false;
                return false;
            }

            if ( reportItem.timeHour > 12 || ( reportItem.timeHour == 12 && reportItem.timeMin != 0 ) ) {

                showWarning("Max work time per item is 12 hours!");
                status = false;
                return false;

            }

            if ( reportItem.timeHour < 0 ) {

                showWarning("Negative value not allowed for hours!");
                status = false;
                return false;

            }

            if ( reportItem.timeMin > 59 || reportItem.timeMin < 0 ) {

                showWarning("Minutes should be submitted within values 0 - 59");
                status = false;
                return false;

            }

            if (reportItem.timeHour !== "00" || reportItem.timeMin !== "00") {

                totalMinutes += parseInt( reportItem.timeHour ) * 60;
                totalMinutes += parseInt( reportItem.timeMin );

                allData.push(reportItem);
            }

            if (allData == "") {
                showWarning("No reports are entered!");
                status = false;
                return false;
            }
        });

        if (status == true) {

            var r = true;

            totalHours = Math.floor( totalMinutes / 60 );
            totalMinutes = totalMinutes % 60;

            if ( totalHours != 8 ) {

                r = confirm("The amount of time you've reported is not 8:00h . Are you sure you want to continue?");

            }

            if ( parseInt( totalHours ) == 8 && parseInt( totalMinutes ) != 0 ) {

                r = confirm("The amount of time you've reported is not 8:00h . Are you sure you want to continue?");

            }

            if ( r == true ) {

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "includes/adapter.php",
                    data: {'action': akcija, 'uid': userId, 'myArray': allData, 'exception': exception},
                    success: function (data) {
                        if (data == "1") {
                            showSuccess("Report is successufuly entered!");
                            $('#addReport').hide('slow');
                            $('#viewReport').show('slow');
                        } else if (data == "-2") {
                            showWarning("Duplicate report is not allowed!");
                        } else {
                            showWarning("Some error occured!");
                        }
                    }
                });

            }
            return false;
        }
    }

    $(".chosen-select-products").chosen({no_results_text: "No results"});

    function addNewReportRow() {
        var repContent = $('.reportItem').html();


        var itemEntry = '<tr class="reportItem">' +
            repContent +
            '</tr>';

        $('#items').append(itemEntry);
        $(".chosen-select-products").last().chosen('destroy');
        $(".chosen-select-products").last().chosen({no_results_text: "No results"});
        //$('.chosen-select-products').last().trigger("chosen:updated");

    }

    function clearBox(o) {
        var inValue = o.value;
        if (inValue == "00") {
            o.value = "";
        }
    }

    function addNull(o) {
        var inValue = o.value;
        if (inValue == "") {
            o.value = "00";
        }
    }
</script>
</body>
</html>