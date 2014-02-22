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

$settings['ROBOTS'] = 'noindex, nofollow';
//Recogemos el texto a buscar
$text_to_search = $_GET['q'];
$html_text_to_search = htmlspecialchars($text_to_search);

print_header('Búsqueda de: "'.$html_text_to_search.'"', false, $html_text_to_search);
print_tabs('Búsqueda');
echo '<div id="main_sub">',"\n";
print_right_side();
echo '	<div id="main_izq">',"\n";
print_search_results();
echo '	</div>',"\n";
echo '</div>',"\n";
print_footer();
////////////////////////////////////////////////////////////////////////////////////
function print_search_results(){
	global $text_to_search, $html_text_to_search;
	$current_page = get_current_page();	//Recuperamos en la página donde nos encontramos

	//Consultamos el nº de bares encontrados
	$count = BarManager::get_search_bars_count($text_to_search, STATUS_PUBLISHED);
	if ($count==1)
		echo '<p class="search_msg">Se ha encontrado 1 resultado, para la búsqueda: "<strong>'.$html_text_to_search.'</strong>"</p>'."\n";
	else
		echo '<p class="search_msg">Se han encontrado '.$count.' resultados, para la búsqueda: "<strong>'.$html_text_to_search.'</strong>"</p>'."\n";

	echo '<div id="contenedor_bares">', "\n";
	if ($count){
		//Consultamos el id de los bares encontrados
		$dbbar_ids = BarManager::get_search_bar_ids($text_to_search, $current_page, STATUS_PUBLISHED);
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
	print_paginator($current_page, $count,'encontrado');
}
?>
