<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

include 'includes/config.php';
include CLASS_PATH . 'classMain.php';

$_main = new Main($db);

$from = null;
$to = null;

if ( isset( $_GET['fromDate'] ) && !empty( $_GET['fromDate'] ) ) {

    $from = $_GET['fromDate'];

}

if ( isset( $_GET['toDate'] ) && !empty( $_GET['toDate'] ) ) {

    $from = $_GET['toDate'];

}

echo $_main -> getTotalHomeHoursGroupedByDate( $from, $to ); 