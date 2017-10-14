<?php

/**********************************************************************
 *																	  *
 * --------- Main klasa za pozive sa homepagea ----------------       *
 * 																	  *
 * 	@Author Boris  													  *
 *  10/2015															  *
 **********************************************************************/
class Main {
	/**********************************************************************
	 * ------------------------ Priprema klase --------------------       *
	 **********************************************************************/

	public function __construct($db) {
		if ($db) {
			$this->db = $db;
		}
	}

	/**********************************************************************
	 * --------- Preuzimanje liste za designers report ------------       *
	 **********************************************************************/

	public function getReportList($uid,$dateFrom,$dateTo) {
		$sql = "SELECT report.id, report.date, DATE(report.date) as kratkiDatum FROM report
				WHERE designer = $uid 
				AND DATE(date) >= '$dateFrom'
				AND DATE(date) <= '$dateTo'
				ORDER BY report.date DESC";
		$results=$this->db->query($sql,2);
		return $results;
	}
	/**********************************************************************
	 * --------- Preuzimanje Homework liste za designers report ---       *
	 **********************************************************************/

	public function getHomeWorkList($uid,$dateFrom,$dateTo) {
		$sql = "SELECT report.id, report.date, DATE(report.date) as kratkiDatum FROM report
				INNER JOIN reportItem ON report.id = reportItem.reportId
				WHERE designer = $uid 
				AND DATE(date) >= '$dateFrom'
				AND DATE(date) <= '$dateTo'
				AND (reportItem.type = 13 OR reportItem.type = 32 OR reportItem.type = 48 OR reportItem.type = 64)
				GROUP BY report.id
				ORDER BY report.id DESC";
		$results=$this->db->query($sql,2);
		return $results;
	}
	/**********************************************************************
	 * --------- Brojanje Homeworking hours ------------------- ---       *
	 **********************************************************************/

	public function getHomeHours($uid,$dateFrom,$dateTo) {
		$sql = "SELECT report.id, report.date, DATE(report.date) as kratkiDatum, reportItem.timeFrom as timeFrom,
						 reportItem.timeTo as timeTo, reportItem.duration as duration 
				FROM report
				INNER JOIN reportItem ON report.id = reportItem.reportId
				WHERE designer = $uid 
				AND DATE(date) >= '$dateFrom'
				AND DATE(date) <= '$dateTo'
				AND (reportItem.type = 13 OR reportItem.type = 32 OR reportItem.type = 48 OR reportItem.type = 64)";

		$results=$this->db->query($sql,2);
		return $results;
	}
	/**********************************************************************
	 * --- Preuzimanje liste svih korisnika /dizajnera ------------       *
	 **********************************************************************/

	public function getUsersList() {
		$sql = "SELECT * FROM users
				WHERE 1";
		$results=$this->db->query($sql,2);
		return $results;
	}

	/**********************************************************************
	 * -------- Preuzimanje liste korisnika na osnovu tima -------------- *
	 **********************************************************************/

	public function getUsersListFromTeam( $team ) {

		$sql = "SELECT * FROM users
				WHERE team='" . $team . "'";

		$results = $this -> db -> query( $sql, 2 );

		return $results;

	}

	/**********************************************************************
	 * --- Preuzimanje liste Tipova stranica ----------------------       *
	 **********************************************************************/

	public function getTypeList( $team = null) {
		
		$sql = "SELECT * FROM pageTypes WHERE 1";
		
		if ( $team != null ) {

			$sql = "SELECT * FROM pageTypes
					WHERE team = '{$team}' ORDER BY code ASC";
		
		}
			
		$results=$this->db->query($sql,2);
		
		return $results;
	
	}

	/**********************************************************************
	 * -------- Preuzimanje imena tipa na osnovu IDa ------------------   *
	 **********************************************************************/

	public function getTypeName( $tid ) {

		if ( $tid == null ) {

			return false;

		}

		$sql = "SELECT title FROM pageTypes WHERE id='" . $tid . "'" ;

		$result = $this -> db -> query ( $sql, 3 );

		return $result;

	}

	/**********************************************************************
	 * ---------- Preuzimanje time kojem pripada tip -------------------- *
	 *********************************************************************/

	public function getTypeTeam( $tid ) {

		if ( $tid == null ) {

			return false;

		}
		
		$sql = "SELECT team FROM pageTypes WHERE id='" . $tid . "'";
		
		$result = $this -> db -> query ( $sql, 3 );
		
		return $result;

	}

	/**********************************************************************
	 * --- Preuzimanje liste Tipova stranica zavisno od subtima ---       *
	 **********************************************************************/

	public function getTypeListBySubTeam($team,$subteam) {
		$sql = "SELECT * FROM pageTypes
				WHERE team = '{$team}' ORDER BY code ASC";
		$results=$this->db->query($sql,2);
		$arrayToReturn = [];
		foreach($results as $pageType){
			$sub_team = unserialize($pageType['sub_team']);
			if(count(array_intersect($subteam,$sub_team))>0){
				$arrayToReturn[]=$pageType;
			}
		}

		return $arrayToReturn;
	}
	/**********************************************************************
	 * --- Uzimanje podataka korisnika -----------------------------       *
	 **********************************************************************/

	public function getUserData($id) {
		$sql = "SELECT * FROM users
				WHERE code='$id'";
		$results=$this->db->query($sql,3);
		return $results;
	}

	/*********************************************************************
	 * --------- Uzimanje podataka korisnika ------------------------    *
	 ********************************************************************/
	public function realGetUserData( $id ) {
		
		$sql = "SELECT * FROM users WHERE id='" . $id ."'";
		
		$results = $this -> db -> query( $sql, 3 );
		
		return $results;
		
	}
	
	
	/**********************************************************************
	 * --- Upis reporta -------------------------------------------       *
	 **********************************************************************/

	public function writeReport($uid, $exception) {
		$danasnji = strval(Date('Y-m-d'));

		$sql = "SELECT * FROM report
				     WHERE DATE(report.date) LIKE '{$danasnji}' AND designer={$uid}";

		$exist = $this->db->query($sql,3);
		if($exist>0 && $exception == "0"){
			echo "-2";
			exit;
		} else {
			$today = Date('Y-m-d H:i:s');
			$yesterday = Date('Y-m-d H:i:s', strtotime('-1 days'));
			$time = Date("H:i");

			// Ako korisnik pise report izmedju 00:00 i 06:30 ujutro, upisi report na prethodni dan
			if ($time > "00:00" && $time < "06:30"){
				$timeToEnter = $yesterday;
			} else {
				$timeToEnter = $today;
			}

			$writekveri = "INSERT INTO report (`date`,`designer`)
                                   VALUES ('$timeToEnter','$uid')";
			$this->db->query($writekveri,1);
			$upis = mysql_insert_id();
			return $upis;
		}
	}
	/**********************************************************************
	 * --- Upis odsustva ------------------------------------------       *
	 **********************************************************************/

	public function writeAbsence($uid,$date) {
		$timestamp = $date->format('Y-m-d H:i:s');

		// 	$sql = "SELECT * FROM report
		// 		     WHERE DATE(report.date) LIKE '{$danasnji}' AND designer={$uid}";

		// 	$exist = $this->db->query($sql,3);
		// if($exist>0){
		// 	echo "-2";
		// 	exit;
		// } else {


		$writekveri = "INSERT INTO report (`designer`, `date`)
                                   VALUES ('$uid', '$timestamp')";
		$this->db->query($writekveri,1);
		$upis = mysql_insert_id();
		return $upis;
		//}
	}
	/**********************************************************************
	 * --- Upis report Itema --------------------------------------       *
	 **********************************************************************/

	public function writeReportItem($rId,$description,$thread,$site,$type,$state,$product,$duration) {

		$description = mysql_real_escape_string($description, $this->db->_connect);
		$site = mysql_real_escape_string($site, $this->db->_connect);
		$thread = mysql_real_escape_string($thread, $this->db->_connect);

		$writekveri = "INSERT INTO reportItem (`reportId`,`description`,`thread`,`site`,`type`,`state`,`duration`,`product`,`timeDuration`)
                                   VALUES ('$rId','$description','$thread','$site',$type, $state, '$duration', $product, '$duration')";
		$this->db->query($writekveri,1);
		$upis = mysql_insert_id();
		return $upis;
	}
	/**********************************************************************
	 * --- Report Itemi pojedinacnog usera ------------------------       *
	 **********************************************************************/
	public function getItemsByUser($uid,$report,$homeWork) {

		$homeQ = "";
		if ($homeWork == "Yes"){
			$homeQ = "AND (type = 13 OR type = 32 OR type = 48 OR type = 64)";
		}

		$sqlIt = "SELECT DATE(report.date) as datum, duration, products.title AS title, states.code AS code, reportItem.description AS description, thread, site, pageTypes.title as type 
				FROM reportItem
				INNER JOIN report ON reportItem.reportId = report.id
				INNER JOIN pageTypes ON reportItem.type = pageTypes.id
				INNER JOIN states ON reportItem.state = states.id
				INNER JOIN products ON reportItem.product = products.id
				WHERE report.designer = $uid 
				{$homeQ}
				AND reportItem.reportId = $report";

		$results=$this->db->query($sqlIt,2);
		return $results;
	}
	/**********************************************************************
	 * --- Spisak drzava -----------------------------       *
	 **********************************************************************/

	public function getStates($id) {
		$sql = "SELECT * FROM states
				WHERE 1";
		$results=$this->db->query($sql,3);
		return $results;
	}
	/**********************************************************************
	 * --- Spisak proizvoda -----------------------------       *
	 **********************************************************************/

	public function getProducts($id) {
		$sql = "SELECT * FROM products
				WHERE 1";
		$results=$this->db->query($sql,3);
		return $results;
	}
	/**********************************************************************
	 * --- Lista za sabiranje broja sati --------------------------       *
	 **********************************************************************/

	public function listAll($kvery) {

		$sqlIt = "SELECT DATE(report.date) as datum, duration, products.title AS title, states.code AS code, 
				reportItem.description AS description, thread, site, pageTypes.title as type, users.team AS team, users.fullname AS fullname, timeDuration
				FROM reportItem 
				INNER JOIN report ON reportItem.reportId = report.id
				INNER JOIN pageTypes ON reportItem.type = pageTypes.id
				INNER JOIN states ON reportItem.state = states.id
				INNER JOIN products ON reportItem.product = products.id
				LEFT JOIN users ON report.designer = users.id
				WHERE 1 $kvery";
		$results=$this->db->query($sqlIt,2);
		return $results;
	}
	/**********************************************************************
	 * --- Spisak dizajnera -----------------------------       *
	 **********************************************************************/

	public function listDesigners() {
		$sqlIt = "SELECT fullname FROM users WHERE team LIKE 'DS'";
		$results=$this->db->query($sqlIt,2);
		return $results;
	}


	/**********************************************************************
	 * --- Uplata za prekovremene sate-----------------------------       *
	 **********************************************************************/

	public function makePayment($uID, $datum) {

		$writekveri = "INSERT INTO payments (`paymentDate`,`userId`)
                                   VALUES ('$datum','$uID')";
		$this->db->query($writekveri,1);
		$upis = mysql_insert_id();
		return $upis;
	}

	/**********************************************************************
	 * --- Zadnja uplata -----------------------------       *
	 **********************************************************************/
	public function getPaymentDate($uID){
		$sql = "SELECT * FROM payments
					WHERE userId='$uID' ORDER BY id DESC LIMIT 1";

		$results=$this->db->query($sql,3);

		return $results;
	}


	/***********************************************************************
	 *
	 * ------------------------ Lista pod-timova ---------------------------
	 *
	 **********************************************************************/
	public function getSubTeams() {

		$sql = "SELECT * FROM sub_teams WHERE 1";

		$results = $this -> db -> query($sql, 2);

		return $results;

	}

	/**********************************************************************
	 *
	 * -------- Poslednji report zaposlenika na osnovu user IDa -----------
	 *
	 **********************************************************************/
	public function getLastReport( $uid ) {

		$sql = "SELECT * FROM report WHERE designer='$uid' ORDER BY date DESC LIMIT 1";

		$result = $this -> db -> query( $sql, 2 );

		return $result;

	}

	/***********************************************************************
	 *
	 *
	 * -------- Radni sati radnika ---------------------------
	 *
	 *
	 ***********************************************************************/
	public function getWorkingHours ( $uid, $team, $mode = 1, $dateFrom = null, $dateTo = null, $raw = false ) {

		/**
		 *
		 * *********************************
		 * The $mode values
		 * 1 - get ALL hours
		 * 2 - get OFFICE hours
		 * 3 - get HOME hours
		 *
		 * Should not use magic numbers...
		 * *********************************
		 *
		 * $team is required to fetch the ID of the 'pageType' from database
		 * because the type column in the reportItem table is populated with
		 * the 'pageType' ID, not the Code. It's set up to use standardized
		 * codes but it's not using them
		 *
		 * If $raw is true, returns the value->key array with
		 * "Hours", "Minutes", "Seconds", if not, it formats the
		 * data that it has into a presentable string
		 *
		 */

		$totalHours = array( "hours" => 0, "minutes" => 0);

		$typeSQL = "SELECT id FROM pageTypes WHERE code = '22' AND team = '" . $team . "' LIMIT 1";
		$typeResult = $this -> db -> query( $typeSQL, 2 );

		$type = $typeResult[0]['id'];

		// Some SQL to append to the overall query depending on what the mode is set to
		$modeAppend = "";

		$trailingZero = "";

		if ( $mode == 2 ) {

			$modeAppend = " AND type <>'" . $type . "'";

		} else if ( $mode == 3 ) {

			$modeAppend = " AND type ='" . $type . "'";

		}

		$sql = "SELECT duration FROM reportItem INNER JOIN report ON reportItem.reportId = report.id WHERE report.designer='$uid'" . $modeAppend ;

		$dateArray = $this -> dateControl( $dateFrom, $dateTo );

		$dateFrom = $dateArray[ 'dateFrom' ];
		$dateTo = $dateArray[ 'dateTo' ];

		if ( $dateFrom != null ) {

			$sql .= " AND report.date >='" . $dateFrom . "'";

		}

		if ( $dateTo != null ) {

			$sql .= " AND report.date <='" . $dateTo . "'";

		}

		$result = $this -> db -> query( $sql, 2 );

		foreach ( $result as $hourset ) {

			list( $hours, $minutes) = explode(":", $hourset['duration']);

			$totalHours[ "hours" ] += $hours;
			$totalHours[ "minutes" ] += $minutes;

		}

		$hoursFromMinutes = intval( $totalHours['minutes'] / 60 );
		$totalHours['hours'] += $hoursFromMinutes;
		$totalHours['minutes'] -= $hoursFromMinutes * 60;

		if ( $totalHours['minutes'] < 10 ) {

			$trailingZero = "0";

		}

		if ( !$raw ) {

			return $totalHours[ "hours" ] . ":" . $totalHours[ "minutes" ] . $trailingZero;

		}

		return $totalHours;

	}


	/***********************************************************************
	 *
	 *
	 * ------- Broj dana kada je korisnik submitovao report ---------------
	 * 				Not calculating Home Work days
	 * 			Any report containing even one item
	 * 		from the 'Home Work' category is not calculated
	 *
	 *
	 **********************************************************************/
	public function getNumOfReports( $uid, $team, $dateFrom = null, $dateTo = null ) {

		$type = $this -> getHomeWorkCode( $team );

		$sql = "SELECT * FROM report WHERE report.designer='" . $uid ."'";

		$dateArray = $this -> dateControl( $dateFrom, $dateTo );

		$dateFrom = $dateArray[ 'dateFrom' ];
		$dateTo = $dateArray[ 'dateTo' ];

		if ( $dateFrom != null ) {

			$sql .= " AND report.date >='" . $dateFrom . "'";

		}

		if ( $dateTo != null ) {

			$sql .= " AND report.date <='" . $dateTo . "'";

		}

		$typeCode = $this -> getHomeWorkCode( $team );

		// Report items from "report" table
		$result = $this -> db -> query( $sql, 2 );

		$reportArray = array();

		$numOfReports = 0;

		foreach ( $result as $report ) {

			$reportItemsSQL = "SELECT * FROM reportItem WHERE reportId='" . $report['id'] . "' AND `type` <> '" . $typeCode . "'";

			$reportItemsResult = $this -> db -> query( $reportItemsSQL, 2 );

			$goodReport = true;

			foreach ( $reportItemsResult as $reportItem ) {

				if ( $reportItem['reportId'] != $report['id'] || $reportItem['type'] == $typeCode ) {

					$goodReport = false;

				}

			}

			if ( $goodReport ) {

				$numOfReports++;

			}

		}

		return $numOfReports;

	}

	/*********************************************************************
	 *
	 *
	 * ------------- Prosječno vrijeme submita reporta -------------------
	 *
	 *
	 *********************************************************************/

	public function getAverageReportTime( $uid, $team, $dateFrom = null, $dateTo = null ) {

		$type = $this -> getHomeWorkCode( $team );

		$sql = "SELECT `date` FROM report INNER JOIN reportItem ON report.id = reportItem.reportId WHERE report.designer='" . $uid . "' AND reportItem.type<>'" . $type . "'";

		$dateArray = $this -> dateControl( $dateFrom, $dateTo );

		$dateFrom = $dateArray[ 'dateFrom' ];
		$dateTo = $dateArray[ 'dateTo' ];

		if ( $dateFrom != null ) {

			$sql .= " AND report.date >='" . $dateFrom . "'";

		}

		if ( $dateTo != null ) {

			$sql .= " AND report.date <='" . $dateTo . "'";

		}

		$result = $this -> db -> query( $sql, 2 );

		$timeArray = array();

		foreach ( $result as $time ) {

			$readyTime = date('H:i', strtotime( $time['date']));

			if ( $readyTime != "00:00" ) {

				array_push($timeArray, $readyTime);

			}

		}

		$average = date("H:i", array_sum( array_map( 'strtotime', $timeArray ) ) / count( $timeArray ));

		return $average;

	}

	public function getTotalHomeHoursGroupedByDate( $dateFrom = null, $dateTo = null ) {

		$dateArray = $this -> dateControl( $dateFrom, $dateTo );

		$sqlAddon = "";

		$dateFrom = $dateArray[ 'dateFrom' ];
		$dateTo = $dateArray[ 'dateTo' ];
		
		// Force the first day of current month as $dateFrom, if no from-date has been specified
		if( $dateFrom == null ) { $dateFrom = date( 'Y-m-01 00:00:00' ); }

		if ( $dateFrom != null ) { $sqlAddon .= " AND DATE(report.date) >='" . $dateFrom . "'"; }

		if ( $dateTo != null ) { $sqlAddon .= " AND DATE(report.date) <='" . $dateTo . "'"; }

		$sqlTime = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( timeDuration ) ) ) FROM reportItem 
					LEFT JOIN report ON reportItem.reportId = report.id 
					WHERE 1" . $sqlAddon . " AND (reportItem.type = 13 
					OR reportItem.type = 32 
					OR reportItem.type = 64 
					OR reportItem.type = 48 
					OR reportItem.type = 71 
					OR reportItem.type = 22 ) 
					GROUP BY DATE(report.date)";

		$sqlDate = "SELECT DATE(date) from report 
					LEFT JOIN reportItem ON report.id = reportItem.reportId 
					WHERE 1 " . $sqlAddon . " AND (reportItem.type = 13 
					OR reportItem.type = 32 OR reportItem.type = 64 
					OR reportItem.type = 48 OR reportItem.type = 71 
					OR reportItem.type = 22 )  
					GROUP BY DATE(date)";

		$resultsTime = $this -> db -> query( $sqlTime, 2 );

		$resultsDate = $this -> db -> query( $sqlDate, 2 );

		$homeHoursChartData = array();

		// Number of element from both queries has to match,
		// otherwise the code could break in the for-loop below
		if ( count( $resultsTime ) != count( $resultsDate ) ) {

			return false;

		}

		for( $i = 0; $i < count( $resultsDate ); $i++ ) {

			$resultDate = $resultsDate[$i];
			$resultTime = $resultsTime[$i];

			$dataResultDate = ( strtotime( array_pop( $resultDate ) ) ) * 1000;
			$dataResultTime = array_pop( $resultTime );

			$dateResultTimeSegments = explode( ":", $dataResultTime );
			$dateResultTimeHours = $dateResultTimeSegments[0];
			$dateResultTimeMinutes = $dateResultTimeSegments[1] + $dateResultTimeHours * 60;

			$homeHoursChartData[$dataResultDate] = $dateResultTimeMinutes;

		}

		return $homeHoursChartData;

	}

	private function dateControl ( $dateFrom, $dateTo, $nofix = false ) {
		
		if ( $dateFrom != null ) {

			$dateFrom = date( 'Y-m-d', strtotime( $dateFrom ) );
			
		}
		
		if ( $dateTo != null ){

			$dateTo = date( 'Y-m-d', strtotime( $dateTo ) );
			
		}

		if ( $dateFrom != null && $dateTo != null ) {

			// Bugfix when a single day is selected
			if ( $dateFrom == $dateTo && !$nofix ) {

				$dateTo = date("Y-m-d", strtotime($dateTo . " + 1 day"));

			}

			// Swap dates if they're reverse
			if ( strtotime( $dateTo ) < strtotime( $dateFrom ) ) {

				$temp = $dateTo;

				$dateTo = $dateFrom;

				$dateFrom = $temp;

			}
		}

		return array( "dateFrom" => $dateFrom, "dateTo" => $dateTo );

	}

	/********************************************************************
	 *
	 *
	 * ------------ HARDCODED HOME WORK CODE IS '22' --------------------
	 * ------- ADJUST THE LOGIC WHEN THE HOME WORK CODE CHANGES ---------
	 *
	 *
	 ********************************************************************/

	private function getHomeWorkCode ( $team ) {

		$typeSQL = "SELECT id FROM pageTypes WHERE code = '22' AND team = '" . $team . "' LIMIT 1";

		$typeResult = $this -> db -> query( $typeSQL, 2 );

		return $typeResult[0]['id'];

	}

	/********************************************************************
	 * 
	 * 
	 * -- Gets highest value of a code that belongs to the given team ---
	 * ---- and is not one of the codes that need to be the highest -----
	 * ---------------------- and adds one to it ------------------------
	 * 
	 * Values used in the query to leave out the highest codes:
	 * - 'Vacation'
	 * - 'Medical'
	 * - 'Home Work'
	 * 
	 * 
	 *******************************************************************/
	public function getNewPageTypeCode ( $team ) {

		$sql = "SELECT MAX(code) FROM pageTypes WHERE team='" . $team . "' AND title<>'Vacation' AND title<>'Medical' AND title<>'Home Work'";
		
		$result = $this -> db -> query( $sql, 2 );
		
		$number = false;
		
		if ( count( $result ) > 0 ) {

			$number = array_pop( $result[0] );
			$number += 1;
			
		}
		
		return $number;

	}

	public function addUserLog( $uid ) {
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "INSERT INTO userLogTbl( userId, logDateTime, ip ) VALUES ( '" . $uid . "', NOW(), '" . $ip . "' )";
		
		$this -> db -> query ( $sql );
		
	}
	
	public function getUserLogs( $uid = null , $dateFrom = null, $dateTo = null ) {
		
		$dates = $this -> dateControl( $dateFrom, $dateTo );

		$dateFrom = $dates['dateFrom'];
		$dateTo = $dates['dateTo'];

		$sqlAddon = "";

		// Force the first day of current month as $dateFrom, if no from-date has been specified
		if( $dateFrom == null ) { $dateFrom = date( 'Y-m-01 00:00:00' ); }

		if ( $dateFrom != null ) { $dateFrom = date( 'Y-m-d', strtotime( $dateFrom ) ); $sqlAddon .= " AND logDateTime >='" . $dateFrom . "'"; }

		if ( $dateTo != null ) { $dateTo = date( 'Y-m-d', strtotime( $dateTo ) ); $sqlAddon .= " AND logDateTime <='" . $dateTo . "'"; }

		if ( $uid != null ) { $sqlAddon .= " AND userId='" . $uid . "' "; }

		$sql = "SELECT * FROM userLogTbl WHERE 1 ".$sqlAddon;
		
		$result = $this -> db -> query ( $sql, 2 );
		
		return $result;
		
	}
	
	public function addNewPageType ( $title, $team ) {
		
		$code = $this -> getNewPageTypeCode( $team );
		
		$sql = "INSERT INTO pageTypes( code, title, team ) VALUES ( '" . $code . "', '" . $title . "', '" . $team . "' )";
		
		$result = $this -> db -> query( $sql );
		
		if ( $result ) {
			
			return true;
			
		}
		
		return false;
		
	}

	public function getTotalWorkHoursGroupedByType ( $uid, $from, $to ) {

		$dateArray = $this -> dateControl( $from, $to );
		
		$dateFrom = $dateArray['dateFrom'];
		$dateTo = $dateArray['dateTo'];
		
		$sqlAddon = "";

		// Force the first day of current month as $dateFrom, if no from-date has been specified
		if( $dateFrom == null ) { $dateFrom = date( 'Y-m-01 00:00:00' ); }

		if ( $dateFrom != null ) { $sqlAddon .= " AND DATE(report.date) >='" . $dateFrom . "'"; }

		if ( $dateTo != null ) { $sqlAddon .= " AND DATE(report.date) <='" . $dateTo . "'"; }
		
		$result = $this -> db -> query( "SELECT * FROM reportItem LEFT JOIN report ON reportItem.reportId = report.id WHERE report.designer='" . $uid . "'" . $sqlAddon, 2 );
		
		$returnArray = array();
		
		foreach ( $result as $item ) {

			$typeId = $item[ 'type' ];
			$typeTitle = $this -> getTypeName( $typeId )['title'];
			$timeDuration = $item['timeDuration'];
			
			if ( !array_key_exists(  $typeTitle, $returnArray ) ) {
				
				$returnArray[ $typeTitle ] = "00:00:00";

			}
			
			if ( isset( $returnArray[ $typeTitle ] ) ) {
				
				$existingTime = $returnArray[ $typeTitle ];
				
				$newValue = $this -> timeToSeconds( $existingTime ) + $this -> timeToSeconds( $timeDuration );
				
				$newTime = $this -> secondsToTime( $newValue );
				
				$returnArray[ $typeTitle ] = $newTime;
				
			}
			
		}
		
		return $returnArray;

	}

	public function getTotalWorkHoursByTypeAndDate( $uid, $dateFrom, $dateTo ) {

		$dateArray = $this -> dateControl( $dateFrom, $dateTo );

		$dateFrom = $dateArray['dateFrom'];
		$dateTo = $dateArray['dateTo'];

		$sqlAddon = "";

		// Force the first day of current month as $dateFrom, if no from-date has been specified
		if( $dateFrom == null ) { $dateFrom = date( 'Y-m-01 00:00:00' ); }

		if ( $dateFrom != null ) { $sqlAddon .= " AND DATE(report.date) >='" . $dateFrom . "'"; }

		if ( $dateTo != null ) { $sqlAddon .= " AND DATE(report.date) <='" . $dateTo . "'"; }

		$result = $this -> db -> query ("SELECT * FROM reportItem LEFT JOIN report ON reportItem.reportId = report.id WHERE report.designer='" . $uid . "'" . $sqlAddon , 2);

		/*
		 *
		 * 1. Create an array of types that appear in the query
		 * 2. Create an array of dates that appear in the query
		 * 3. Loop through all the report items
		 * 4. Loop through the types while looping through report items
		 *    if report item matches the type, assign it to that position
		 *    in the return array( ex. 'other' goes on position 2, 'OMG' goes to position 4 etc. )
		 *    inside it's date's sub-array
		 *
		 */

		$typesArray = array();
		$datesArray = array();
		$returnArray = array();

		foreach ( $result as $row ) {

			$positionOfType = 0;

			// The id that is stored inside every report Item
			$typeId = $row['type'];

			// The date that is stored inside every report item
			// formatted
			$date = date( 'Y-m-d', strtotime( $row['date'] ) );

			// The time it took to complete the task
			$reportItemTime = $row['timeDuration'];

			// Check if the types array has that type already stored
			// If not, store it next
			if ( !array_key_exists(  $typeId, $typesArray ) ) {

				$typeTitle = $this -> getTypeName( $typeId );

				$typesArray[ $typeId ] = $typeTitle['title'];

			}

			// Get the index of the report item's type
			
			$positionOfType = array_search( $typeId, array_keys( $typesArray ) );

			// Check if the dates array contains the report item's
			// date as an array key. If not, add the current date as
			// key and initialize an array in it's position
			if( !array_key_exists( $date, $datesArray ) ) {

				$datesArray[ $date ] = array();

			}


			// Check if there is already an some time stored for the type
			// and date found
			if ( isset( $datesArray[ $date ][ $positionOfType ] ) ) {

				$existingValue = $datesArray[ $date ][ $positionOfType ];
				
				$newValue = $this -> timeToSeconds( $existingValue ) + $this -> timeToSeconds( $reportItemTime );
				
				$newTime = $this -> secondsToTime( $newValue );
				
				$datesArray[ $date ][ $positionOfType ] = $newTime;

			} else {

				$datesArray[ $date ][ $positionOfType ] = $reportItemTime;

			}

		}

		array_push( $returnArray, $typesArray );
		array_push( $returnArray, $datesArray );
		
		return $returnArray;

	}

	public function getReportItems( $team = null, $dateFrom = null, $dateTo = null ) {

		$dateArray = $this -> dateControl( $dateFrom, $dateTo, true );

		$dateFrom = $dateArray['dateFrom'];
		$dateTo = $dateArray['dateTo'];

		$sqlAddon = "";

		// Team needs to be done first
		if ( $team != null ) { $sqlAddon .= " LEFT JOIN report ON reportItem.reportId = report.id 
												LEFT JOIN users ON report.designer = users.id 
												WHERE users.team='" . $team . "'"; }

		// Force the first day of current month as $dateFrom, if no from-date has been specified
		if( $dateFrom == null ) { $dateFrom = date( 'Y-m-01 00:00:00' ); }

		if ( $dateFrom != null ) { $sqlAddon .= " AND DATE(date) >='" . $dateFrom . "'"; }

		if ( $dateTo != null ) { $sqlAddon .= " AND DATE(date) <='" . $dateTo . "'"; }

		if ( $team == null ) {

			$sql = "SELECT type, timeDuration, DATE(report.date) FROM reportItem 
				LEFT JOIN report ON reportItem.reportId = report.id 
				WHERE 1" . $sqlAddon;
		
		} else {

			$sql = "SELECT type, timeDuration, DATE(report.date) FROM reportItem" . $sqlAddon;
			
		}
		
		$result = $this -> db -> query ( $sql, 2 );
		
		$maxValue = 0;
		
		// Types are gathered here, in the typeId => typeTitle patten
		$typeArray = array();
		
		// Dates are used as indexes for sub-arrays containing the
		// number of hours spent on a task on that specific date
		$datesArray = array();

		$returnArray = array();


		// $row represents a reportItem entry, not a row in the table
		foreach ( $result as $row ) {
			
			$typeId = $row[ 'type' ];
			
			$typeTitle = $this -> getTypeName( $typeId )['title'];
			
			if ( $team == null ) {
				
				$typeTitle .= " (" . $this -> getTypeTeam( $typeId )['team'] . ")";
				
			}
			
			$date = $row[ 'DATE(report.date)' ];
			$duration = $row[ 'timeDuration' ];
			
			// Add the task type to typeArray if it does not exist
			if ( !array_key_exists( $typeId, $typeArray ) ) {
				
				$typeArray[ $typeId ] = $typeTitle;
				
			}

			$positionOfType = array_search( $typeId, array_keys( $typeArray ) );

			// Check if the dates array contains the report item's
			// date as an array key. If not, add the current date as
			// key and initialize an array in it's position
			if( !array_key_exists( $date, $datesArray ) ) {

				$datesArray[ $date ] = array();

			}


			// Check if there is already an some time stored for the type
			// and date found
			if ( isset( $datesArray[ $date ][ $positionOfType ] ) ) {

				$existingValue = $datesArray[ $date ][ $positionOfType ];

				$newValue = $this -> timeToSeconds( $existingValue ) + $this -> timeToSeconds( $duration );

				$newTime = $this -> secondsToTime( $newValue );

				$datesArray[ $date ][ $positionOfType ] = $newTime;

			} else {

				$datesArray[ $date ][ $positionOfType ] = $duration;

			}


			if ( $maxValue < $this -> timeToSeconds( $datesArray[ $date ][ $positionOfType ] ) ) {

				$maxValue = $this -> timeToSeconds( $datesArray[ $date ][ $positionOfType ] );

			}
			
		}

		$returnArray['types'] = $typeArray;
		$returnArray['dates'] = $datesArray;
		$returnArray['maxValue'] = $maxValue;

		return $returnArray;

	}

	// Only accepts HH:MM:SS format...
	public function timeToSeconds( $time ) {

		$time = preg_replace( "/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time );

		sscanf( $time, "%d:%d:%d", $hours, $minutes, $seconds );

		$seconds = $hours * 3600 + $minutes * 60 + $seconds;

		return $seconds;

	}


	public function secondsToTime( $seconds ) {

		$seconds = round( $seconds );

		$time = sprintf( "%02d:%02d:%02d", ( $seconds / 3600 ), ( $seconds / 60 % 60 ), $seconds % 60 );
		
		return $time;

	}

	// Defaults to shades of green
	// Does not support negative values
	// All values get abs()'d
	public function interpolateColor( $value, $max, $brightest = 0x92FC92, $darkest = 0x0A5E0A ) {

		$value = abs( $value / $max );
		
		// Get separate decimal values of color for $brightest
		// and $darkest
		$redBrightest = $brightest & 0xFF0000;
		$greenBrightest = $brightest & 0x00FF00;
		$blueBrightest = $brightest & 0x0000FF;

		$redDarkest = $darkest & 0xFF0000;
		$greenDarkest = $darkest & 0x00FF00;
		$blueDarkest = $darkest & 0x0000FF;

		// Calculate separate color chanel return values
		$returnRed = $redBrightest + ( ( $redDarkest - $redBrightest ) * $value ) & 0xFF0000;
		$returnGreen = $greenBrightest + ( ( $greenDarkest - $greenBrightest ) * $value ) & 0x00FF00;
		$returnBlue = $blueBrightest + ( ( $blueDarkest - $blueBrightest ) * $value ) & 0x0000FF;


		$result = dechex( $returnRed | $returnGreen | $returnBlue );
		
		// If leading zeroes were removed
		while ( strlen( $result ) < 6 ) {
			
			$result = "0" . $result;
			
		}

		return $result;

	}

}
?>