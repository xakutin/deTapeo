<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'UserManager.php';

mb_internal_encoding('UTF-8');

$current_user = UserManager::get_logged_user();
// Use proxy detecttion
if ($settings['CHECK_BEHIND_PROXY']) {
	include includes.'check_behind_proxy.php';
	$user_ip = check_ip_behind_proxy();
} else {
	$user_ip = $_SERVER["REMOTE_ADDR"];
}

// Warn, we shoud printf "%u" because PHP on 32 bits systems fails with high unsigned numbers
$user_ip_int = sprintf("%u", ip2long($user_ip));
$now = $_SERVER['REQUEST_TIME'];


// Check the user's referer.
if (!empty($_SERVER['HTTP_REFERER'])) {
	if (preg_match('/http:\/\/'.preg_quote($_SERVER['SERVER_NAME']).'/', $_SERVER['HTTP_REFERER'])) {
		$referer = 'local';
	} elseif (preg_match('/q=|search/', $_SERVER['HTTP_REFERER']) ) {
		$referer = 'search';
	} else {
		$referer = 'remote';
	}
} else {
	$referer = 'unknown';
}

// Check bots
$bot = false;
if (preg_match('/(bot|slurp|wget|libwww|\Wjava|\Wphp)\W/i', $_SERVER['HTTP_USER_AGENT'])) {
	$bot = true;
}
//$bot = true; //testing
?>
