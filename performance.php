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

  /* date from query */
if(isset($_GET['from']) && !empty($_GET['from']))
{
    $from = mysql_real_escape_string(urldecode($_GET['from']));
    $dfQ = " AND DATE(report.date) > '$from'";
}
else{

    $from = $defaultDateFrom;
    $dfQ =  " AND DATE(report.date) > '$from'";
}

/* date to query */
if(isset($_GET['to']) && !empty($_GET['to']))
{
    $to = mysql_real_escape_string(urldecode($_GET['to']));
    $dtQ = " AND DATE(report.date) < '$to'";
}
else{

    $to = $defaultDateTo;
    $dtQ = " AND DATE(report.date) < '$to'";
}

/* product query */
if(isset($_GET['product']) && !empty($_GET['product']))
{
    $product = mysql_real_escape_string(urldecode($_GET['product']));
    $pQ = " AND product = '{$product}'";
}
else{

    $product = 26;
    $pQ = " AND product = '{$product}'";
}

/* state query */
if(isset($_GET['state']) && !empty($_GET['state']))
{
    $state = mysql_real_escape_string(urldecode($_GET['state']));
    $sQ = " AND state = '{$state}'";
}
else{

//    $state = "19";
//    $sQ = " AND state = '{$state}'";
}

$Query = "  ";  //default
$Query .= $dfQ;  //date from
$Query .= $dtQ;  //date to
$Query .= $pQ;  //product
$Query .= $sQ;  //state


$_states = $_main->getStates();
$_products = $_main->getProducts();
$listDesigners = $_main->listDesigners();
$listAll = $_main->listAll($Query);
//var_dump($listAll);
$designers = Array();
foreach ($listDesigners AS $designer) {
	$designers[$designer['fullname']] = Array("banners"=>0,"sales"=>0,"presells"=>0,"mamuals"=>0,"print"=>0,"other"=>0);
}


$banners = Array();
$sales = Array();
$presells = Array();
$manuals = Array();
$other = Array();
$totals = Array();
foreach ($listAll AS $row) {
	if ($row['team'] == 'DS') {
			$duration = $row['timeDuration'];
			$durationStamp = strtotime("1970-01-01 $duration UTC");

			if ($row['type'] == "Banners") {
				$designers[$row['fullname']]['banners'] = $designers[$row['fullname']]['banners'] + $durationStamp;
				$banners[$row['fullname']] = $designers[$row['fullname']]['banners'];
				$totals['banner'] = $totals['banner'] + $durationStamp;
			} else if ($row['type'] == "Sales page") {
				$designers[$row['fullname']]['sales'] = $designers[$row['fullname']]['sales'] + $durationStamp;
				$sales[$row['fullname']] = $designers[$row['fullname']]['sales'];
				$totals['sale'] = $totals['sale'] + $durationStamp;
			} else if ($row['type'] == "Presell page") {
				$designers[$row['fullname']]['presells'] = $designers[$row['fullname']]['presells'] + $durationStamp;
				$presells[$row['fullname']] = $designers[$row['fullname']]['presells'];
				$totals['presell'] = $totals['presell'] + $durationStamp;
			} else if ($row['type'] == "Usermanual") {
				$designers[$row['fullname']]['manual'] = $designers[$row['fullname']]['manual'] + $durationStamp;
				$manual[$row['fullname']] = $designers[$row['fullname']]['manual'];
				$totals['manual'] = $totals['manual'] + $durationStamp;
			}else if ($row['type'] == "Print") {
				$designers[$row['fullname']]['print'] = $designers[$row['fullname']]['manual'] + $durationStamp;
				$print[$row['fullname']] = $designers[$row['fullname']]['print'];
				$totals['print'] = $totals['print'] + $durationStamp;
			} else {
				$designers[$row['fullname']]['other'] = $designers[$row['fullname']]['other'] + $durationStamp;
				$other[$row['fullname']] = $designers[$row['fullname']]['other'];
				$totals['other'] = $totals['other'] + $durationStamp;
			}
	}
}

//print_r($banners);



arsort($banners);
arsort($sales);
arsort($presells);
arsort($manual);
arsort($print);
arsort($other);

// echo '<pre>';
// print_r($banners);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="content-type" content="text/html;charset=utf-8">

	<html>
	<head>
		  <title>Reports Panel</title>
		  <link href="css_/performance.css" rel="stylesheet" type="text/css" />
		  <link href="<?= CSS_PATH.'chosen.css' ?>" rel="stylesheet">
		  <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
		  <script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
		  <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
		  <script type="text/javascript" src="<?= JS_PATH.'chosen.jquery.min.js' ?>"></script>
		  <script type="text/javascript" src="<?= JS_PATH.'functions.js' ?>"></script>
	</head>

	<body>
		<style>
	    .chosen-container.chosen-container-single {
	        /*width: 260px !important;*/
	        margin-top: 15px !important;
	    }
		</style>
		<div class="main">

			<div class="headline">
				<div class="name">Product performance tracking</div>
			</div>
<form>
			<div class="hlleft">
				<h4>Product selection:</h4>
				<select class="_type chosen-select-products" id="product" name="product" style="width:300px; height:30px; margin-right:10px; margin-left: 10px; margin-top: 15px;">
					<option value="26">Other (not product related)</option>
                        <?php
                        foreach ($_products as $_product){
                        	$selected = "";
                        	if ($product == $_product["id"]){
                        		$selected = "selected";
                        	}
                            echo '<option value="'.$_product["id"].'" '.$selected.'>'.$_product["sku"].'-'.$_product["productType"].' '.$_product["title"].'</option>';
                        }
                        ?>
				</select>


				<select class="_type chosen-select-products" id="state" name="state" style="width:70px; height: 30px; margin-right:10px; background-image: url(../daily/images_/open_arrow.png)!important; background-position: 0px 0px ">
					<option value="19">ALL</option>
                        <?php
                        foreach ($_states as $_state){
							if($_state['code']!='n/a') {
								$selected = "";
								if ($state == $_state["id"]) {
									$selected = "selected";
								}
								echo '<option value="' . $_state["id"] . '" ' . $selected . '>' . $_state["code"] . '</option>';
							}
                        }
                        ?>
				</select>
			</div>

			<div class="hlright">
				<h4>From:</h4>
				<input name="from" type="text" id="datumFrom" placeholder=""  onclick="$(this).datepicker();" value="<?= $from ?>">
				<h4>to</h4>
				<input name="to" type="text" id="datumTo" placeholder=""  onclick="$(this).datepicker();" value="<?= $to ?>">
				<button class="search" onclick="SearchFormSimple.search(this);">Filter results</button>
			</div>
</form>
			<h4 style="margin-left:10px; margin-bottom: 0px; margin-top: 35px; color: #405272; font-size: 16px!important;">Detailed reports for <span class="productInfo"></span></h4>

			<div class="subheading">
				<div class="shleft">
					<div class="subheading-hr">
						<h3>Banners:</h3>
						<div class="hours"><?php echo round($totals['banner']/3600, 0); ?> <span>h</span></div>
					</div>
					<div class="subheading-hr">
						<h3>Sales pages:</h3>
						<div class="hours"><?php echo round($totals['sale']/3600, 0); ?> <span>h</span></div>
					</div>
					<div class="subheading-hr">
						<h3>Presell:</h3>
						<div class="hours"><?php echo round($totals['presell']/3600, 0); ?> <span>h</span></div>
					</div>
					<div class="subheading-hr">
						<h3>User manuals:</h3>
						<div class="hours"><?php echo round($totals['manual']/3600, 0); ?> <span>h</span></div>
					</div>
					<div class="subheading-hr">
						<h3>Print:</h3>
						<div class="hours"><?php echo round($totals['print']/3600, 0); ?> <span>h</span></div>
					</div>
					<div class="subheading-hr" style="border:none;">
						<h3>Other:</h3>
						<div class="hours"><?php echo round($totals['other']/3600, 0); ?> <span>h</span></div>
					</div>
				</div>
				<div class="shright" style="width: auto;">
					<h3>Total hours spent:</h3>
					<div class="hours-rs"><?php echo round($totals['banner']/3600, 0) + round($totals['sale']/3600, 0) + round($totals['presell']/3600, 0) + round($totals['manual']/3600, 0) + round($totals['print']/3600, 0) + round($totals['other']/3600, 0); ?> <span>h</span></div>
				</div>
			</div>

				<br>

			<h4 style="margin-left:10px; margin-bottom: 10px; color: #405272; font-size: 16px!important;">Workers performance for <span class="productInfo"></span></h4>


				<div class="top-10" style="width: 400px;">
				<div class="performace-work">Banners<span style="float: right;margin-right: 5px;"><?=round($totals['banner']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
						<?php
							foreach ($banners AS $key=>$val) {
								$sati = round($val/3600, 0);
								echo '<tr>';
								echo '<td>'.$key.'</td>';
								echo '<td style="/*width: 68px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				</div>


				<div class="top-10">
			    <div class="performace-work">Sales pages<span style="float: right; margin-right: 5px;"><?=round($totals['sale']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
						<?php
							foreach ($sales AS $key=>$val) {
								$sati = round($val/3600, 0);
								echo '<tr>';
								echo '<td>'.$key.'</td>';
								echo '<td style="/*width: 68px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				</div>


				<div class="top-10">
				<div class="performace-work">Presell <span style="float: right; margin-right: 5px;"><?=round($totals['presell']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
						<?php
							foreach ($presells AS $key=>$val) {
								$sati = round($val/3600, 0);
								echo '<tr>';
								echo '<td>'.$key.'</td>';
								echo '<td style="/*width: 112px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				</div>

				<div style="width:100%; display:table">

				<div class="top-10">
				<div class="performace-work">User manuals<span style="float: right; margin-right: 5px;"><?=round($totals['manual']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
						<?php
							foreach ($manual AS $key=>$val) {
								$sati = round($val/3600, 0);
								echo '<tr>';
								echo '<td>'.$key.'</td>';
								echo '<td style="/*width: 97px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				</div>

			<div class="top-10">
				<div class="performace-work">Print <span style="float: right; margin-right: 5px;"><?=round($totals['print']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
					<?php
					foreach ($print AS $key=>$val) {
						$sati = round($val/3600, 0);
						echo '<tr>';
						echo '<td>'.$key.'</td>';
						echo '<td style="/*width: 97px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>


				<div class="top-10" style="margin-right:0 !important">
				<div class="performace-work">Other <span style="float: right; margin-right: 5px;"><?=round($totals['other']/3600, 0);?> h</span></div>

				<div class="table-head">
					<span class="th-left">Name</span>
					<span class="th-right">Total hours</span>
				</div>

				<table class="table-rows">
					<tbody>
						<?php
							foreach ($other AS $key=>$val) {
								$sati = round($val/3600, 0);
								echo '<tr>';
								echo '<td>'.$key.'</td>';
								echo '<td style="/*width: 68px;*/ text-align: right; line-height: 25px;">'.$sati.' h</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
				</div>
				</div>

			<div class="footer">
				<p>Daily reports app, v.1.03</p>
			</div>

		</div>
		<script>

			$("title").html("Product performance | Reports Panel");

		$('#datumFrom,#datumTo').datepicker({
          	dateFormat: "yy-mm-dd"
      	});

      	var drzavaSel = $('#state option:selected').text();
      	var productSel = $('#product option:selected').text();

      	var productHeading = productSel+" / "+drzavaSel;
      	$('.productInfo').empty();
      	$('.productInfo').append(productHeading);

      	 $(".chosen-select-products").chosen({no_results_text: "No results"});
      </script>
	</body>
	</html>