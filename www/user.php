<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include includes.'html_user.php';

//Consultamos el usuario
$user_id = 0;
$is_me = false;
$user = null;
$from = '';
//Recogemos el id del usuario
if (isset($_GET['id'])) $user_id = (int)$_GET['id'];
if (isset($_GET['from'])) $from = $_GET['from'];			//Si viene de validación o de recuperar contraseña
//Si no se ha recibido ningún id, se muestra la información del usuario actual (si ha entrado)
if (!$user_id && $current_user) $user_id = $current_user->id;
if ($user_id){
	$user=UserManager::get_user($user_id);
	if ($current_user) $is_me = ($user_id == $current_user->id);
}

print_header('Perfil');
if ($is_me)
	print_tabs(TAB_PROFILE);
else if ($user)
	print_tabs($user->login);
echo '<div id="main_sub">', "\n";
print_user_right_side($user);
echo '	<div id="main_izq">'."\n";
print_user_tabs(TAB_USER_PROFILE);
print_user_profile($user, $is_me, $from);
echo '	<div class="clear"></div>', "\n";
echo '	</div>'."\n";
echo '</div>', "\n";
print_footer();
?>
