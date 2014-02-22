<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function force_authentication() {
	global $current_user, $settings;

	if (!$current_user) {
		header('Location: .'.$settings['BASE_URL'].'user_login.php?return='.$_SERVER['REQUEST_URI']);
		die;
	}
	return true;
}

function force_editor_user() {
	global $current_user, $settings;

	if (force_authentication()) {
		if (!$current_user->is_editor()){
			header('Location: '.$settings['BASE_URL'].'403.php');
			die;
		}
	}
	return true;
}

function force_admin_user() {
	global $current_user, $settings;

	if (force_authentication()) {
		if (!$current_user->is_admin()){
			header('Location: '.$settings['BASE_URL'].'403.php');
			die;
		}
	}
	return true;
}
///////////////////////////////////////////////////////////////////////////////////
// Bans
///////////////////////////////////////////////////////////////////////////////////
function check_ban_list($what, $list) {
	if (!empty($list)) {
		$domains = preg_split("/[\s,]+/", $list);
		foreach ($domains as $domain) {
			if (preg_match("/$domain$/i", $what))
				return true;
		}
	}
	return false;
}

/**
 * Comprueba si la IP del usuario logado está baneada por abusos anteriores
 *
 * @return true si el usuario esta baneado, false en caso contrario
 */
function check_banned_ip(){
	global $is_new_bar_page;
	if (is_user_ip_banned()){
		$msg = '<p class="error">Acceso denegado. Dirección IP no permitida</p>';
		print_message($msg);
		return true;
	}
	return false;
}

function is_user_ip_banned(){
	global $user_ip;
	if (check_ban($user_ip, 'ip', true) || check_ban_proxy())
		return true;
	return false;
}
?>
