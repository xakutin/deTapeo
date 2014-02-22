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
//No se debe indexar las páginas con parámetros
if (count($_REQUEST))
	$settings['ROBOTS'] = 'noindex, follow';
else
	$settings['ROBOTS'] = 'noarchive';

print_header();
print_tabs();
echo '<div id="main_sub">',"\n";
print_right_side();
echo '	<div id="main_izq">',"\n";
print_bars_list();
echo '	</div>',"\n";
echo '</div>',"\n";
print_footer();
////////////////////////////////////////////////////////////////////////////////////
function print_bars_list(){
	$current_page = get_current_page();	//Recuperamos en la página donde nos encontramos

	echo '<div id="contenedor_bares">', "\n";
	//Consultamos el nº de bares publicados
	$count = BarManager::get_published_bars_count();
	if ($count){
		//Consultamos el id de los bares publicados
		$dbbar_ids = BarManager::get_published_bar_ids($current_page);
		if ($dbbar_ids){
			echo '<ul>',"\n";
			foreach ($dbbar_ids as $dbbar_id){
				//Consultamos los datos de cada bar
				$bar = BarManager::get_bar_with_summary_info($dbbar_id);
				//$bar->specialities = SpecialityManager::get_bar_specialities($bar);
				//Buscamos la foto de cabecera
				$cover_photo = get_cover_photo($bar);
				//Buscamos la información del último usuario que modificó el Bar
				if ($bar->last_author_id>0)
					$author = UserManager::get_user_width_summary_info($bar->last_author_id);
				else
					$author = UserManager::get_user_width_summary_info($bar->author_id);

				//Mostramos la información del Bar
				if ($bar && $author){
					bar_print_sumary($bar, $cover_photo, $author);
				}
			}
			echo '</ul>',"\n";
		}
	}
	echo '</div>', "\n";
	print_paginator($current_page, $count,'publicado');
}

?>
