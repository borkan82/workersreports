<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: Wed, 12 Dec 1990 12:12:12 GMT");
  include 'includes/config.php';
  include CLASS_PATH.'classMain.php';
 
  $_main = new Main($db);

  $id = $_GET['id'];
  $extId = "";
  if (isset($_GET['extId']) && !empty($_GET['extId'])){
    $extId = $_GET['extId'];
  }



//Definisanje datuma
  $a_date = Date("Y-m-h");
  $godina = Date("Y");
  $mjesec = Date("m");
  $defaultDateFrom = $godina."-".$mjesec."-01";
  $daysNum = date("Y-m-t", strtotime($a_date));
  $defaultDateTo = $daysNum;

  $userAdminData = $_main->getUserData($extId);
  $userData = $_main->getUserData($id);

  if ($userData == 0){
    echo '<div class="headline" style="width:1280px;">Permission Denied</div>';
    exit;
  }

  $userAdmin = false;
  if ($userData['role'] == "A"){
      $userAdmin = true;
  }

/* date from query */
if(isset($_GET['from']) && !empty($_GET['from']))
{
    $from = mysql_real_escape_string(urldecode($_GET['from']));
    $dfQ = "";
}
else{

    $from = $defaultDateFrom;
    $dfQ = "";
}

/* date to query */
if(isset($_GET['to']) && !empty($_GET['to']))
{
    $to = mysql_real_escape_string(urldecode($_GET['to']));
    $dtQ = "";
}
else{

    $to = $defaultDateTo;
    $dtQ = "";
}

$Query = " 1 ";  //default
$Query .= $dfQ;  //date from
$Query .= $dtQ;  //date to

$homeWorks = "";
if (isset($_GET["home"])){
$reportlist = $_main->getHomeWorkList($userData['id'],$from,$to);
$homeWorks = "Yes";
} else {
$reportlist = $_main->getReportList($userData['id'],$from,$to);
$homeWorks = "";
}

$medical = 0;
$vacation = 0;
$homeWork = 0;
$totalWork = 0;

if (!empty($reportlist)){
  foreach ($reportlist as $row) {
    $report = $row['id'];
    $reportItems = $_main->getItemsByUser($userData['id'],$report,$homeWorks);

      foreach ($reportItems as $item){
        if ($item["type"] == "Medical") {
          $total = $item['timeTo'] - $item['timeFrom'];
            $medical = $medical + $total;
        } else if ($item["type"] == "Vacation") {
          $total = $item['timeTo'] - $item['timeFrom'];
            $vacation = $vacation + $total;
        } else if ($item["type"] == "Home Work") {
          $total =  explode(":",$item['duration']);
          $totalHome = $total[0]*3600 + $total[1]*60;
            $homeWork = $homeWork + $totalHome;
        } else {
          $duration = explode(":",$item['duration']);
          $totalDuration = $duration[0]*3600 + $duration[1]*60;
            $totalWork = $totalWork + $totalDuration;
        }
      }
  }
}

$satiHome = floor($homeWork/3600);
$minuteHome = floor(($homeWork%3600) / 60);

if($satiHome<10){
$strH .= "0";
$strH .= $satiHome;
$satiHome = $strH;
} 
if($minuteHome<10){
$str2H .= "0";
$str2H .= $minuteHome;
$minuteHome = $str2H;
} 


$sati = floor($totalWork/3600);
$minute = floor(($totalWork%3600) / 60);

if($sati<10){
$str .= "0";
$str .= $sati;
$sati = $str;
} 
if($minute<10){
$str2 .= "0";
$str2 .= $minute;
$minute = $str2;
} 


 //If the number is below 10, it will add a leading zero


include INC_PATH.'header.php';
?>
	<body>
		<div class="main" >

			<div class="headline">
				<form>
					<div class="hlleft" style="margin-top: 0px!important;"">
						<div class="name"><?=$userData['fullname'] ?></div>
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
					      echo '<button class="myreports" onclick="document.location = \'users.php?id='.$id.'\';">View All Workers</div>';
					    }
					    ?>
					</div>
					<div class="hlright">
						<h4>Report from:</h4>
						<input name="from" type="text" id="datumFrom" placeholder="" onclick="$(this).datepicker();" value="<?= $from ?>">
						<h4>to</h4>
						<input name="to" type="text" id="datumTo" placeholder="" onclick="$(this).datepicker();" value="<?= $to ?>">
						<input type="hidden" name="id" value="<?= $id ?>">
	                	<input type="hidden" name="extId" value="<?= $extId ?>">
						<button class="search" onclick="SearchFormSimple.search(this);">Filter results</button>
					</div>
				</form>
			</div>

			<div class="subheading">
				<?php
				if($homeWorks !== "Yes"){
				?>
					<div class="shleft">
						<h3>Total working hours:</h3>
						<div  style="border:none;" class="hours"><?php  echo $sati.":".$minute; ?> h</div>
					</div>
				<?php
				} 
				?>
				<div class="shright">
					<h3>Work from home (current month):</h3>
					<div class="hours" style="border:none;">
				    <?php
				      if($homeWorks == "Yes"){
				        echo $satiHome.":".$minuteHome." h </div>";
				      } else {
				      	echo $satiHome.":".$minuteHome." h";
				      }	
				    ?>
					</div>
				</div>
			</div>

			<br>
			<h4 style="margin-left:14px; margin-bottom: 10px; color: #405272;">Detailed reports</h4>




			<div class="dayTable">
			              <?php 
			              $countTab = 1;
			              $collapse = "";
			              $hideStyle = "";

			              if (!empty($reportlist)){
			                  foreach ($reportlist as $row) {
			                  $total = 0;
			                  $report = $row['id'];
			                  $vrijeme = date('H:i:s',strtotime($row['date']));
			                  $vrijemeKraj = date('H:i:s', strtotime('15:00:00')); 
			                  $datum = date('d.m.Y.',strtotime($row['date']));
			                  $earlyWriteStyle = '';
			                  if ($vrijeme < $vrijemeKraj){
			                  		$earlyWriteStyle = 'style="background-color: #ff3921;"';
								}
			                    echo '<table class="dayView" style="margin-bottom:15px;width:1280px;">';
			                    echo '<thead>';
			                    echo '<tr onclick="toogleCollapse(this);" class="tableup">';
			                    echo 	'<td style="width:20px;">'.$countTab.'</td>';
			                    echo 	'<td style="text-align:left; padding:0px!important;" colspan="7">';
			                    echo 		'<div class="tableup-left"><div class="datetable">'.$datum.'</div></div>';
			                    echo 		'<div class="tableup-right"><h4>Report posted:</h4><div class="hourstable" '.$earlyWriteStyle.'>'.$vrijeme.'</div></div>';
			                    echo 	'</td>';
			                    echo '</tr>';
			                    echo '</thead>';
			                    echo '<tbody class="reportHolder '.$collapse.'" '.$hideStyle.'>';
			                    echo '<tr class="dayTabRow" style="background-color: #ebebeb; height: 25px; font-weight: bold;">';
			                    echo '<td style="width:20px;"></td><td style="width:30px;"><strong>Hours</strong></td><td style="width:30px;"><strong>Type</strong></td><td style="width:20px;"><strong>Product</strong></td><td style="width:60px;"><strong>Country</strong></td><td><strong>Description</strong></td><td style="width:270px;"><strong>Forum/Trello URL</strong></td><td style="width:210px;"><strong>Page URL</strong></td>';
			                    echo '</tr>';
			                  $homeWorks = "";
			                    if (isset($_GET["home"])){
			                    $homeWorks = "Yes";
			                    } else {
			                    $homeWorks = "";
			                    }

			                    $reportItems = $_main->getItemsByUser($userData['id'],$report,$homeWorks); 
			                    foreach ($reportItems as $item){
			                   	  $trajanjest = explode(":",$item['duration']);
			                      $trajanje = $trajanjest[0]*3600 + $trajanjest[1]*60;
			                      $total = ($total + $trajanje);
			                      $shortLink1 = $item['thread'];
			                      $shortLink2 = $item['site'];

			                      if (strlen($shortLink1) > 30){
			                        $shortLink1 = substr($item['thread'], 0, 35)."...";
			                      }
			                      if (strlen($shortLink2) > 20){
			                        $shortLink2 = substr($item['site'], 0, 25)."...";
			                      }
			                        echo '<tr class="dayTabRow">';
			                        echo '<td class="dayTabRow"></td><td class="dayTabRow">'.$item['duration'].'</td>';
			                        echo '<td class="dayTabRow">'.$item['type'].'</td>';
			                        echo '<td class="dayTabRow">'.$item['title'].'</td>';
			                        echo '<td class="dayTabRow">'.$item['code'].'</td>';
			                        echo '<td class="dayTabRow" style="text-align:left;">'.$item['description'].'</td>';

			                        $threadArr = explode(",",$item['thread']);

			                        echo '<td class="dayTabRow" style="text-align:left;">';
			                        	foreach ($threadArr AS $_thread){
			                        		echo '<a href="'.$_thread.'" target="_blank">'.substr($_thread,0,35).'</a></BR>';
			                        	}
			                        echo '</td>';

			                        $siteArr = explode(",",$item['site']);

			                        echo '<td class="dayTabRow" style="text-align:left;">';
			                        	foreach ($siteArr AS $_site){
			                        		echo '<a href="'.$_site.'" target="_blank">'.substr($_site,0,25).'</a></BR>';
			                        	}
			                        echo '</td>';
			                        echo '</tr>';
			                    }
			                    echo '<tr><td colspan="8"><h4 style="margin-left:10px;">Total:'.gmdate('H:i:s', $total).' h</h4></td></tr>';
			                    echo '</tbody>';
			                    echo '</table>';
			                    $countTab++;
			                      $collapse = "collapse";
			                      $hideStyle = "style='display:none !important'";
			                  }
			              } else {
			                  echo '<tr>';
			                  echo '<td colspan="7"><span style="color:#f26100;font-weight: bold;">There are no Reports!</span></td>';
			                  echo '</tr>';
			              }



			              ?>
			    </div>								
			<div class="footer">
				<p>Daily reports app, v.1.03</p>
			</div>

		</div>
<script>
$('#datumFrom,#datumTo').datepicker({
          dateFormat: "yy-mm-dd"
      });

	$("title").html('<?php echo $userData['fullname']; ?> | Reports Panel');

      </script>
	</body>
	</html>