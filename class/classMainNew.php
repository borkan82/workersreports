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
				ORDER BY report.id DESC";
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
				AND (reportItem.type = 13 OR reportItem.type = 32 OR reportItem.type = 48 OR reportItem.type = 64 OR reportItem.type = 127)
				GROUP BY report.id
				ORDER BY report.id DESC";
		$results=$this->db->query($sql,2);
        return $results;
	}
/**********************************************************************
 * --------- Brojanje Homeworking hours ------------------- ---       *
 **********************************************************************/

	public function getHomeHours($uid,$dateFrom,$dateTo) {
		$sql = "SELECT report.id, report.date, DATE(report.date) as kratkiDatum, reportItem.timeFrom as timeFrom, reportItem.timeTo as timeTo FROM report
				INNER JOIN reportItem ON report.id = reportItem.reportId
				WHERE designer = $uid 
				AND DATE(date) >= '$dateFrom'
				AND DATE(date) <= '$dateTo'
				AND (reportItem.type = 13 OR reportItem.type = 32 OR reportItem.type = 48 OR reportItem.type = 64 OR reportItem.type = 127)";
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
 * --- Preuzimanje liste Tipova stranica ----------------------       *
 **********************************************************************/

	public function getTypeList($team) {
		$sql = "SELECT * FROM pageTypes
				WHERE team = '{$team}' ORDER BY code ASC";
		$results=$this->db->query($sql,2);
        return $results;
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


		$writekveri = "INSERT INTO report (`designer`)
                                   VALUES ('$uid')";
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

 	public function writeReportItem($rId,$timeFrom,$timeTo,$description,$thread,$site,$type) {

 		$description = mysql_real_escape_string($description, $this->db->_connect);
 		$site = mysql_real_escape_string($site, $this->db->_connect);
 		$thread = mysql_real_escape_string($thread, $this->db->_connect);
 		
		$writekveri = "INSERT INTO reportItem (`reportId`,`timeFrom`,`timeTo`,`description`,`thread`,`site`,`type`)
                                   VALUES ('$rId','$timeFrom','$timeTo','$description','$thread','$site',$type)";                          
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
				$homeQ = "AND (type = 13 OR type = 32 OR type = 48 OR type = 64 OR type = 127)";
			}

		$sqlIt = "SELECT DATE(report.date) as datum, timeFrom, timeTo, description, thread, site, pageTypes.title as type 
				FROM reportItem
				INNER JOIN report ON reportItem.reportId = report.id
				INNER JOIN pageTypes ON reportItem.type = pageTypes.id
				WHERE report.designer = $uid 
				{$homeQ}
				AND reportItem.reportId = $report";

		$results=$this->db->query($sqlIt,2);
        return $results;
	}
}
?>