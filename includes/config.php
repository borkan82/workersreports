<?php
define("URL", "//ROOT URL *****/");
define("ROOT", "/var/www/sites/*******ROOTPATH/");
define("CLASS_PATH", ROOT."class/");
define('IMG_PATH', URL.'images_/');
define('JS_PATH', URL.'js_/');
define('CSS_PATH', URL.'css_/');
define('INC_PATH', ROOT.'includes/');

include '_DB.php';

/**************DB CONNECT**************/
$_CONFIG_DATABASE = array
					(
						'type'		=>	'mysql5',
						'address'	=>	'',
						'port'		=>	3306,
						'username'	=>	'',
						'password'	=>	'',
						'database'	=>	''
					);
 $db = new Database($_CONFIG_DATABASE, true);
 $sql="SET NAMES utf8";
 $db->query($sql, 1);
