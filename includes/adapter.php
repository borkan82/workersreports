<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Boris
 * Date: 9/7/15
 * Time: 8:42 AM
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);


include 'config.php';
include '../class/classMain.php';

$_main = new Main($db);

if (isset($_POST['action'])) {

    switch($_POST['action']){
        case "addReport":
        $uId = $_POST['uid'];
        $reportItem = $_POST['myArray'];
        $exception = $_POST['exception'];


        $writeReport = $_main->writeReport($uId, $exception);

        if($writeReport > 0) {
            foreach ($reportItem as $item){
                $timeHour = $item['timeHour'];
                $timeMin = $item['timeMin'];
                $description = nl2br($item['description']);
                $thread = $item['thread'];
                $site = $item['site'];
                $type = $item['type'];
                $state = $item['state'];
                $product = $item['product'];
                $duration = $timeHour.":".$timeMin;

                //var_dump($description);
                $writeReportItem = $_main->writeReportItem($writeReport,$description,$thread,$site,$type,$state,$product,$duration);
            }

            echo "1";
        }  else {
            echo "-1";
        }  
        break;
        
        case "addPageType":
            
            $title = $_POST['title'];
            $teamCode = $_POST['team_code'];
            
            $addPage = $_main -> addNewPageType( $title, $teamCode );
            
            if ( $addPage ) {
                
                echo json_encode( array( "success" => true, "message" => "Page type successfuly added!" ) );
                exit();
                
            }

            echo json_encode( array( "success" => false, "message" => "Error adding page type!" ) );
            exit();
            
            break;
        
        case "addVacation":
        $uId = $_POST['uid'];
        $reportItem = $_POST['myArray'];

        $diff = abs(strtotime($reportItem[0]['dateTo']) - strtotime($reportItem[0]['dateFrom']));
        $days = floor($diff / (60*60*24)) +1;

            for ($i=0; $i < $days; $i++){
                $date = date_create($reportItem[0]['dateFrom']);
                date_add($date, date_interval_create_from_date_string($i.' days'));
                $timestamp = $date->format('Y-m-d');
                $datum = new DateTime($timestamp);
                //var_dump($timestamp);exit;
                $day =  $datum->format("w");

               if($day != 6 && $day != 0) {
                    $writeReport = $_main->writeAbsence($uId,$date);

                     if($writeReport > 0) {
                         $writeReportItem = $_main->writeReportItem($writeReport,"7","15","Vacation","","","11");
                     }
                }    
            }
            echo "1";
        
        break;

        case 'addUser':

            if ( !isset($_POST['username']) || !isset($_POST['team_code']) || !isset($_POST['team']) || !isset($_POST['role']) || !isset($_POST['subteam'])) {

                echo json_encode(array('success' => false, 'message' => 'All fields must me filled'));
                exit();

            }

            $username = $_POST['username'];
            $teamCode = $_POST['team_code'];
            $team = $_POST['team'];
            $role = $_POST['role'];
            $subteam = $_POST['subteam'];
            $subteamJSON = "";
            $accessCode = md5(uniqid());
            $paymentsTableDate = date('Y-m-d', strtotime('last day of previous month'));

            if( $_POST['subteam'] != -1 && $_POST['team_code'] == "DS" ) {

                $subteamJSON = 'a:1:{i:0;i:'.$_POST['subteam'].';}';

            }

            $addUser = $db -> query( "INSERT INTO users (fullname, position, role, team, code, sub_team) 
                                      VALUES ( '$username', '$team', '$role', '$teamCode', '$accessCode', '$subteamJSON')" );


            if($addUser) {

                $user = $_main -> getUserData( $accessCode );
                $userId = $user['id'];

                $db -> query( "INSERT INTO payments( paymentDate, userId ) VALUES ( '$paymentsTableDate', '$userId' )" );

                echo json_encode( array('success' => true, 'message' => 'New user added successfuly', 'accessCode' => $accessCode) );

                exit();

            }

            echo json_encode(array('success' => false, 'message' => 'Error pushing user to database'));
            exit();

            break;

        case "makePayment":
        $uId = $_POST['id'];

        $godina = Date("Y");
        $mjesec = Date("m", strtotime($date . " - 1 month"));
        $defaultDateStart = $godina."-".$mjesec."-01";
        $dateStart = new DateTime($defaultDateStart);
        $dateStart->modify('last day of this month');
        $defaultDateTo = $dateStart->format('Y-m-d');
        //$defaultDateTo = $godina."-".$mjesec."-31";


                $date = Date('Y-m-d');
                $makePayment = $_main->makePayment($uId,$defaultDateTo);
                if($makePayment) {
                    echo "1";
                }
       
        break;


        case "addSickness":
        $uId = $_POST['uid'];
        $reportItem = $_POST['myArray'];

        $diff = abs(strtotime($reportItem[0]['dateTo']) - strtotime($reportItem[0]['dateFrom']));
        $days = floor($diff / (60*60*24)) +1;

            for ($i=0; $i < $days; $i++){
                $date = date_create($reportItem[0]['dateFrom']);
                date_add($date, date_interval_create_from_date_string($i.' days'));
                $timestamp = $date->format('Y-m-d');
                $datum = new DateTime($timestamp);
                //var_dump($timestamp);exit;
                $day =  $datum->format("w");

               if($day != 6 && $day != 0) {
                    $writeReport = $_main->writeAbsence($uId,$date);

                     if($writeReport > 0) {
                         $writeReportItem = $_main->writeReportItem($writeReport,"7","15","Sick Leave","","","12");
                     }
                }    
            }
            echo "1";
        break;
    exit;
    }
}
?>