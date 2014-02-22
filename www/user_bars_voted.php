<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include classes.'PhotoManager.php';
include classes.'BarManager.php';
include classes.'SpecialityManager.php';
include classes.'VoteManager.php';
include includes.'html_stars.php';
include includes.'html_bar.php';
include includes.'html_user.php';

//Consultamos el usuario
$user_id = 0;
$is_me = false;
$user = null;
//Recogemos el id del usuario
if (isset($_GET['id'])) $user_id = (int)$_GET['id'];
//Si no se ha recibido ningún id, se muestra la información del usuario actual (si ha entrado)
if (!$user_id && $current_user) $user_id = $current_user->id;
if ($user_id){
	$user=UserManager::get_user($user_id);
	if ($current_user) $is_me = ($user_id == $current_user->id);
}

print_header('Bares votados');
if ($is_me)
	print_tabs(TAB_PROFILE);
else if ($user)
	print_tabs($user->login);
echo '<div id="main_sub">'. "\n";
print_user_right_side($user);
echo '	<div id="main_izq">'."\n";
print_user_tabs(TAB_USER_VOTES);
if ($is_me)
	echo '<p class="warning_msg"><strong>Advertencia:</strong> La valoración de los bares es el voto que has dado.</p>'."\n";
else
	echo '<p class="warning_msg"><strong>Advertencia:</strong> La valoración de los bares es el voto dado por este usuario.</p>'."\n";
if ($user) print_user_voted_list($user);
echo '	</div>'."\n";
echo '</div>'. "\n";
print_footer();
/////////////////////////////////////////////////////////////////////////////////////////////////
function print_user_voted_list($user){
	$current_page = get_current_page();	//Recuperamos en la página donde nos encontramos
	echo '<div id="contenedor_bares">', "\n";
	//Consultamos el nº de bares
	$count = BarManager::get_user_voted_bars_count($user);
	if ($count){
		//Consultamos el id de los bares votados
		$dbbar_ids = BarManager::get_user_voted_bar_ids($user, $current_page);
		if ($dbbar_ids){
			echo '<ul>',"\n";
			foreach ($dbbar_ids as $dbbar_id){
				//Consultamos los datos de cada bar
				$bar = BarManager::get_bar_with_summary_info($dbbar_id, $user);
				if ($bar){
					//$bar->specialities = SpecialityManager::get_bar_specialities($bar);
					//Buscamos la foto de cabecera
					$cover_photo = get_cover_photo($bar);
					//Buscamos la información del último usuario que modificó el Bar
					if ($bar->last_author_id>0)
						$author = UserManager::get_user_width_summary_info($bar->last_author_id);
					else
						$author = UserManager::get_user_width_summary_info($bar->author_id);
					//Mostramos la información del Bar
					bar_print_sumary($bar, $cover_photo, $author, true, true, $user);
				}
			}
			echo '</ul>',"\n";
		}
	}
	echo '</div>', "\n";
	print_paginator($current_page, $count,'votado');
}
?>
