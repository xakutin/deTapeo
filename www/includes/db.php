<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include_once(classes.'ezdb1-simple.php');
//include_once(classes.'ezdb1.php');
$db = new db($settings['DB_USER'], $settings['DB_PASSWORD'], $settings['DB_NAME'], $settings['DB_SERVER']);

$db->persistent = $settings['MYSQL_PERSISTENT'];
$db->query("SET NAMES 'utf8'");

$db->hide_errors();
//Desarrollo
//$db->show_errors();
//$db->trace=false;
?>
