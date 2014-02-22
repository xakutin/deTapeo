<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+

/**
 * Escribe los tags meta de la página
 *
 */
function print_header_metas(){
	global $settings;
	echo '	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', "\n";
	echo '	<meta name="description" content="',$settings['DESCRIPTION'],'"/>', "\n";
	echo '	<meta name="keywords" content="',$settings['KEYWORDS'],'"/>', "\n";
	echo '	<meta name="robots" content="',$settings['ROBOTS'],'"/>', "\n";
	if ($settings['THUMBNAIL_URL'])
		echo '	<meta name="thumbnail_url" content="',$settings['THUMBNAIL_URL'],'"/>', "\n";
}

/**
 * Escribe los tags link de la cabecera de la página
 *
 */
function print_header_links(){
	global $settings;
	echo '	<link rel="icon" href="',$settings['BASE_URL'],'img/favicon.ico" type="image/x-icon"/>',"\n";
	echo '	<link rel="shortcut icon" href="',$settings['BASE_URL'],'img/favicon.ico" type="image/x-icon" />',"\n";
	if ($settings['THUMBNAIL_URL'])
		echo '	<link rel="image_src" href="',$settings['THUMBNAIL_URL'],'"/>', "\n";
}

/**
 * Escribe los enlaces a las hojas de estilo
 *
 */
function print_header_css(){
	global $settings;
	echo '	<link rel="stylesheet" type="text/css" href="',$settings['BASE_URL'],'css/default.pack.css?n=',AVOID_BROWSER_CACHE_NUM,'" media="screen" />',"\n";
	//echo '	<link rel="stylesheet" type="text/css" href="',$settings['BASE_URL'],'css/default.css" media="screen" />',"\n";
	echo '	<!--[if (gte IE 5.5)&(lte IE 6)]>',"\n";
	echo '		<style type="text/css" media="screen">td, img { behavior: url(',$settings['BASE_URL'],'css/iepngfix/iepngfix.htc)}</style>',"\n";
  echo '		<script src="',$settings['BASE_URL'],'js/pngfix.js" type="text/javascript"></script>',"\n";
	echo '	<![endif]-->',"\n";
}

/**
 * Escribe las contantes Javascript
 *
 */
function print_js_constants(){
	global $settings;
	echo '<script type="text/javascript">',"\n";
	echo '	var BASE_URL="',$settings['BASE_URL'],'";'."\n";
	echo '	var VOTE_OBSOLETE="',VOTE_OBSOLETE,'";'."\n";
	echo '	var VOTE_DUPLICATED="',VOTE_DUPLICATED,'";'."\n";
	echo '	var VOTE_NO_TAPA_BAR="',VOTE_NO_TAPA_BAR,'";'."\n";
	echo '	var VOTE_NO_EXISTS="',VOTE_NO_EXISTS,'";'."\n";
	echo '	var GMAP_LOAD_CLICK_LISTENER=false;'."\n";
	echo '	var GMAP_SEARCH_DIRECTION=false;'."\n";
	echo '	var GMAP_LAT=0;'."\n";
	echo '	var GMAP_LNG=0;'."\n";
	echo '</script>'."\n";
}

/**
 * Escribe los JS de la aplicación
 *
 * @param bool $inc_js_map Indica si hay que incluir el script de carga del mapa de google maps
 */
function print_include_js($inc_js_map=false){
	global $settings;
	echo '<script src="',$settings['BASE_URL'],'js/all.pack.js?n=',AVOID_BROWSER_CACHE_NUM,'" type="text/javascript"></script>',"\n";
	/*echo '	<script src="',$settings['BASE_URL'],'js/jquery-1.2.6.pack.js" type="text/javascript"></script>',"\n";
	echo '	<script src="',$settings['BASE_URL'],'js/jsoc-0.12.0.js" type="text/javascript"></script>',"\n";
	echo '	<script src="',$settings['BASE_URL'],'js/general.js" type="text/javascript"></script>',"\n";
	echo '	<script src="',$settings['BASE_URL'],'js/tooltip.js" type="text/javascript"></script>',"\n";
	*/
	if ($inc_js_map){
		echo '<script type="text/javascript" src="http://www.google.com/jsapi?key=',$settings['GMAP_KEY'],'"></script>',"\n";
		echo '<script src="',$settings['BASE_URL'],'js/gmap.pack.js?n=',AVOID_BROWSER_CACHE_NUM,'" type="text/javascript"></script>',"\n";
		//echo '	<script src="',$settings['BASE_URL'],'js/gmap.js" type="text/javascript"></script>',"\n";
	}
}

/**
 * Escribe los includes JS
 *
 * @param boolean $inc_js_map Indica si se debe incluir el JS de Google Maps
 */
function print_header_js($inc_js_map){
	//Ctes de Javascript
	print_js_constants();
	//Escribimos los scripts de la aplicación
	print_include_js($inc_js_map);
}

/**
 * Escribe la cabecera de la página
 *
 * @param string $title_text Título de la página
 * @param boolean $inc_js_map Indica si hay que incluir el js de google maps
 * @param string $text_searched Texto que se ha buscado
 */
function print_header($title_text='', $inc_js_map=false, $text_searched=''){
	global $current_user, $settings, $bot;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',"\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">',"\n";
	echo '<head>',"\n";
	if ($title_text)
		echo '	<title>',$title_text,'</title>',"\n";
	else
		echo '	<title>deTapeo</title>',"\n";
	print_header_metas();
	print_header_links();
	print_header_css();
	if (!$bot)
		print_header_js($inc_js_map);
	echo '</head>',"\n";
	echo '<body>',"\n";
	echo '	<div id="tooltip" onmouseover="tooltip.dontHide(event)" onmouseout="tooltip.hide(event);"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td id="tooltip_topleft" class="tooltip_cornertop"></td><td class="tooltip_top"></td><td id="tooltip_topright" class="tooltip_cornertop"></td></tr><tr><td class="tooltip_left"></td><td id="tooltip-text" valign="top"></td><td class="tooltip_right"></td></tr><tr><td class="tooltip_corner" id="tooltip_bottomleft"></td><td class="tooltip_bottom"></td><td id="tooltip_bottomright" class="tooltip_corner"></td></tr></tbody></table></div>';
	echo '	<div id="gradtop">',"\n";
	if (isset($current_user)){
		echo '			<div id="current_user">',"\n";
		echo '				<img src="',get_avatar_url($current_user->id, 22),'" class="avatar_small" alt="'.$current_user->login.'"/>',"\n";
		echo '				<a href="',$settings['BASE_URL'],'user" class="bold" rel="nofollow">'.$current_user->login.'</a> | <a href="',$settings['BASE_URL'],'user_logout.php" rel="nofollow">Cerrar sesión</a>',"\n";
		echo '			</div>',"\n";
	}

	if (!$bot){
		echo '			<div id="search">',"\n";
		echo '				<form method="get" action="'.$settings['BASE_URL'].'search.php" name="frmSearch">',"\n";
		echo '					<input type="text" name="q" id="textfield_search" value="',$text_searched,'" />',"\n";
		echo '				  <a href="javascript:document.frmSearch.submit();" class="bot_search">buscar</a>',"\n";
		echo '					<input type="submit" class="submit_hidden"/>', "\n";
		echo '				</form>',"\n";
		echo '			</div>',"\n";
	}
	echo '		</div>',"\n";
	echo '		<div id="main">',"\n";
	echo '			<div id="cont_logo"><a href="',$settings['BASE_URL'],'"><img src="',$settings['BASE_URL'],'img/logo.jpg" alt="deTapeo" border="0" title="deTapeo" /></a></div>',"\n";
}



/**
 * Escribe el pie de la página
 */
function print_footer(){
	global $settings, $bot;
	echo '	</div>',"\n";
	if (!$bot){
		echo '	<div id="gradbot">',"\n";
			echo '		<p><strong>deTapeo</strong> - Contenidos bajo licencia <a href="http://creativecommons.org/licenses/by-sa/3.0/deed.es" class="grey">CC 3.0</a> - Diseño: Kay</p>',"\n";
		echo '	</div>',"\n";
		echo '<span class="grey2">Versión: ',$settings['VERSION'], '</span>';
	}
	echo '</body>',"\n";
	echo '</html>',"\n";
}

/**
 * Escribe la parte derecha de la pantalla
 *
 */
function print_right_side(){
	global $bot;
	echo '	<div id="main_der">',"\n";
	if (!$bot){
		print_add_bar_button();
	}
	echo '	</div>',"\n";
}

/**
 * Escribe el botónde añadir un Bar
 *
 */
function print_add_bar_button(){
	global $settings;
	echo '		<div id="contenedor_login">',"\n";
	echo '			<a href="',$settings['BASE_URL'],'bar_data.php?op=new" id="anadir" rel="nofollow">Añadir bar</a>',"\n";
	echo '		</div>',"\n";
}

/**
 * Escribe en pantalla las pestañas de la página
 * Selecciona la pestaña que recibe por parámetro.
 *
 * @param string $selected_tab Pestaña a seleccionar, sino encuentra ninguna que corresponda la añade al final
 */
function print_tabs($selected_tab=''){
	global $current_user, $settings, $bot;
	$tab_found=false;
	echo '<div id="pestanas">',"\n";
	echo '	<ul>',"\n";

	//Pestaña de Inicio
	if (!$selected_tab){
		echo '		<li class="on"><a href="',$settings['BASE_URL'],'">Inicio</a></li>',"\n";
		$tab_found = true;
	}else{
		echo '		<li class="off"><a href="',$settings['BASE_URL'],'">Inicio</a></li>',"\n";
	}

	//Pestaña Top
	if ($selected_tab == TAB_TOP){
		echo '		<li class="on"><a href="',$settings['BASE_URL'],'list_top">Los mejores</a></li>',"\n";
		$tab_found = true;
	}else{
		echo '		<li class="off"><a href="',$settings['BASE_URL'],'list_top">Los mejores</a></li>',"\n";
	}

	if (isset($current_user)){
		//Pestaña Pendientes
		if ($current_user->is_editor()){
			if ($selected_tab == TAB_QUEUED){
				echo '		<li class="on"><a href="',$settings['BASE_URL'],'list_queued.php" rel="nofollow">Pendietes</a></li>',"\n";
				$tab_found = true;
			}else{
				echo '		<li class="off"><a href="',$settings['BASE_URL'],'list_queued.php" rel="nofollow">Pendietes</a></li>',"\n";
			}
		}

		//Pestaña Administración
		if ($current_user->is_admin()){
			if ($selected_tab == TAB_ADMIN){
				echo '		<li class="on"><a href="',$settings['BASE_URL'],'admin_list_bars.php" rel="nofollow">Administración</a></li>',"\n";
				$tab_found = true;
			}else{
				echo '		<li class="off"><a href="',$settings['BASE_URL'],'admin_list_bars.php" rel="nofollow">Administración</a></li>',"\n";
			}
		}

		//Mi perfil
		if ($selected_tab == TAB_PROFILE){
			echo '		<li class="on"><a href="',$settings['BASE_URL'],'user" rel="nofollow">Mi Perfil</a></li>',"\n";
			$tab_found = true;
		}else{
			echo '		<li class="off"><a href="',$settings['BASE_URL'],'user" rel="nofollow">Mi Perfil</a></li>',"\n";
		}

	}else{
		if ($selected_tab != TAB_LOGIN && !$bot)
			echo '		<li class="off"><a href="',$settings['BASE_URL'],'user_login.php" rel="nofollow">Login</a></li>',"\n";
	}

	//Login, Registro y recuperar contraseña. Fuera de la condición anterior por si acceden directamente
	if ($selected_tab == TAB_LOGIN){
		echo '		<li class="on"><a href="',$settings['BASE_URL'],'user_login.php" rel="nofollow">Login</a></li>',"\n";
		$tab_found = true;
	}else if ($selected_tab == TAB_REGISTER){
		echo '		<li class="on"><a href="',$settings['BASE_URL'],'user_register.php" rel="nofollow">Nueva cuenta</a></li>',"\n";
		$tab_found = true;
	}else if ($selected_tab == TAB_RECOVER){
		echo '		<li class="on"><a href="',$settings['BASE_URL'],'user_pass_recover.php" rel="nofollow">Recuperar contraseña</a></li>',"\n";
		$tab_found = true;
	}

	//Nueva pestaña
	if (!$tab_found)
		echo '		<li class="on"><a href="',$_SERVER['REQUEST_URI'],'">',$selected_tab,'</a></li>',"\n";

	echo '		<li id="pregunta"><a href="',$settings['BASE_URL'],'help">Qué es <strong>deTapeo</strong></a></li>',"\n";
	echo '	</ul>',"\n";
	echo '</div>',"\n";
}


/**
 * Escribe en pantalla un mensaje
 *
 * @param string $message Mensaje a mostrar en la pantalla
 */
function print_message($message){
	echo '<div id="main_izq">',"\n";
	echo $message,"\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>',"\n";
}


/**
 * Escribe el paginador del listado de bares
 *
 * @param int $current_page Página del listado a mostrar
 * @param int $bars_count Nº de bares en el listado
 * @param string $paginator_text Texto que indica el calificativo a dar a los bares
 */
function print_paginator($current_page, $bars_count, $paginator_text='publicado'){
	global $settings;
	$index_limit = 10;

	if ($bars_count <= $settings['PAGE_SIZE']){
		echo '<div class="clear"></div>', "\n";
	}else{
		//Recoger los parametros del Query string q no son el page
		$query_string=preg_replace('/page=[0-9]+/', '', $_SERVER['QUERY_STRING']);
		$query_string=preg_replace('/^&*(.*)&*$/', "$1", $query_string);
		if (!empty($query_string)) {
			$query_string = htmlspecialchars($query_string);
			$query_string = "&amp;$query_string";
		}
		//Calculamos el nº total de páginas y los indices de la primera y última a mostrar
		$total_pages = ceil($bars_count/$settings['PAGE_SIZE']);
		$start = max($current_page - intval($index_limit/2), 1);
		$end = $start + $index_limit -1;

		echo '<div id="cont_paginacion">', "\n";
		//Escribimos el nº de bares encontrados
		if ($bars_count ==1)
			echo '<span class="numero_bares">1 Bar ',$paginator_text,'</span>';
		else
			echo '<span class="numero_bares">', $bars_count, ' Bares ',$paginator_text,'s</span>';

		if ($bars_count>$settings['PAGE_SIZE']){

			//Botón Anterior
			if ($current_page==1) {
				echo '<span class="bot_disabled">anterior</span>';
			} else {
				$i = $current_page-1;
				echo '<a href="?page=',$i,$query_string,'" title="Anterior" class="botsnumeracion_no">anterior</a>';
			}
			//Mostrar la 1ª página si nos encontramos en una página mayor que 6
			if ($start>1) {
				$i = 1;
				echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
				echo '<span class="botsnumeracion_si">...</span>';
			}
			//Mostramos las páginas siguientes
			for ($i=$start; $i<=$end && $i<=$total_pages; ++$i) {
				if ($i==$current_page) {
					echo '<span class="botsnumeracion_si">', $i, '</span>';
				} else {
					echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
				}
			}
			//Mostramos la última página si no se ha enseñado antes
			if ($total_pages>$end) {
				$i = $total_pages;
				echo '<span class="botsnumeracion_si">...</span>';
				echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
			}
			//Botón siguiente
			if ($current_page<$total_pages) {
				$i = $current_page+1;
				echo '<a href="?page=',$i,$query_string,'" title="Siguiente" class="botsnumeracion_no">siguiente</a>';
			} else {
				if ($bars_count)
					echo '<span class="bot_disabled">siguiente</span>';
			}
		}
		echo '</div>', "\n";
	}
}


/**
 * Escribe las pestañas de administración
 *
 * @param unknown_type $selected_tab
 */
function print_admin_tabs($selected_tab){
	global $settings;
	echo '	<div id="pestanas_n2">'."\n";
	echo '		<ul>'."\n";
	if ($selected_tab == TAB_ADMIN_BARS)
		echo '			<li><span class="current">Bares</span></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'admin_list_bars.php" class="a_n2_off" rel="nofollow">Bares</a></li>'."\n";

	if ($selected_tab == TAB_ADMIN_COMMENTS)
		echo '			<li><span class="current">Comentarios</span></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'admin_list_comments.php" class="a_n2_off" rel="nofollow">Comentarios</a></li>'."\n";

	if ($selected_tab == TAB_ADMIN_BANS)
		echo '			<li><span class="current">Baneos</span></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'admin_list_bans.php" class="a_n2_off" rel="nofollow">Baneos</a></li>'."\n";

	if ($selected_tab == TAB_ADMIN_USERS)
		echo '			<li><span class="current">Usuarios</span></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'admin_list_users.php" class="a_n2_off" rel="nofollow">Usuarios</a></li>'."\n";

	if ($selected_tab == TAB_ADMIN_LOGS)
		echo '			<li class="last"><span class="current">Log</span></li>'."\n";
	else
		echo '			<li class="last"><a href="',$settings['BASE_URL'],'admin_show_log.php" class="a_n2_off" rel="nofollow">Log</a></li>'."\n";

	echo '		</ul>'."\n";
	echo '	</div>'."\n";
}


/**
 * Escribe el aviso legal
 *
 */
function print_legal_conditions(){
	echo "AVISO LEGAL / NORMAS DE USO\n";
	echo "El presente aviso legal regula el acceso y el uso del servicio del sitio web detapeo.net.\nLa utilización de este sitio web implica la plena aceptación de las disposiciones incluidas en este Aviso en la versión publicada en el momento en que el usuario acceda al sitio web.\n";
	echo "\n1. Protección de datos\ndetapeo.net en cumplimiento con la Ley Orgánica 15/1999 de Protección de Datos de Carácter Personal (LOPD), garantiza la total privacidad de los datos personales de los usuarios.\nTodos los datos enviados a través de los formularios que figuran en la web,forman parte de una base de datos protegida de acuerdo a dicha ley.\n\nEl titular podrá ejercitar los derechos de acceso, rectificación y cancelación de sus datos, así como el de revocación del consentimiento para la cesión de sus datos en los términos previstos en la LOPD.\nLos usuarios pueden realizar estas acciones editando sus propios datos en la web detapeo.net o enviando una solicitud a webmaster[arroba]detapeo.net.\n\nLos comentarios, información de bares y votos enviados voluntariamente por los usuarios no son datos personales de carácter privado sino públicos. Quedan relacionados con los votos y comentarios de otros usuarios. Los gestores de detapeo.net podrán conservar estos datos con el objetivo de mantener la coherencia de la información publicada y los envíos de todos los usuarios.\n";
	echo "\n2. Normas de uso\nEl usuario accede a hacer un uso correcto de detapeo.net y de los servicios que ofrece, con total sujeción a la Ley, a las buenas costumbres, a las presentes Condiciones Generales y manteniendo el debido respeto a los demás usuarios.\nQueda expresamente prohibido cualquier uso diferente a la finalidad de este Sitio Web.\n\nEl acceso y utilización del sitio no exige la previa suscripción o registro de los usuarios aunque, detapeo.net condiciona la utilización de algunos de los servicios a la previa cumplimentación del correspondiente registro de Usuario.\n\nEl usuario será el único responsable de las manifestaciones falsas o inexactas que haga y de los daños que pudiera causar a detapeo.net o a terceros por la información que proporcione.\nEl usuario accede a hacer un uso adecuado de los contenidos y servicios que detapeo.net ofrece y con carácter enunciativo pero no limitativo, a no emplearlos para:\n\n";
	echo "(i) Incurrir en actividades ilícitas, ilegales o contrarias a la buena fe y al orden público\n";
	echo "(ii) Difundir contenidos o propaganda de carácter racista, xenófobo, pornográfico-ilegal, de apología del terrorismo o atentatorio contra los derechos humanos\n";
	echo "(iii) Difundir información que infringe algún derecho de propiedad intelectual, de marca, de patente, secreto comercial, o cualquier otro derecho de tercero, que dicha información no tiene carácter confidencial y que dicha información no es perjudicial para terceros\n";
	echo "(iv) Provocar daños en los sistemas físicos y lógicos de detapeo.net, de sus proveedores o de terceras personas, introducir o difundir en la red virus informáticos o cualesquiera otros sistemas físicos o lógicos que sean susceptibles de provocar los daños anteriormente mencionados\n";
	echo "(v) Intentar acceder y, en su caso, utilizar las cuentas de correo electrónico de otros usuarios para realizar spamming o para modificar o manipular sus mensajes\n";
	echo "(vi) Crear múltiples cuentas con el fin de participar en discusiones simulando las opiniones de personas distintas (astroturfing), suplantar la identidad de otras personas o intentar alterar artificialmente los contadores de votos y crear múltiples usuarios con el único objetivo de eludir las restricciones y penalizaciones generales del sistema\n";
	echo "(vii) Intentar promocionar el voto desde sitios de terceros mediante el uso de técnicas informáticas distintas a las ofrecidas por detapeo.net\n";
	echo "\ndetapeo.net se reserva el derecho de retirar todos aquellos comentarios y aportaciones que vulneren el respeto a la dignidad de la persona, que sean discriminatorios, xenófobos, racistas, pornográficos, que atenten contra la juventud o la infancia, el orden o la seguridad pública o que, a su juicio, no resultaran adecuados para su publicación.\nEn cualquier caso, detapeo.net no será responsable de las opiniones vertidas por los usuarios.\n\nEl incumplimiento de las condiciones de uso podría significar el bloqueo de la cuenta de usuario y las medidas y denuncias adecuadas según las leyes españolas y europeas.\nCon el objetivo de mejorar el servicio y minimizar los problemas, detapeo.net se reserva el derecho a modificar y actualizar las condiciones de uso sin previo aviso.\n";
	echo "\n3. Limitación de responsabilidad\nLos enlaces (hipervínculos) o contenidos de terceros que aparecen en esta web se facilitan con la finalidad de ampliar la información o indicar otro punto de vista.\nSu inclusión no implica la aceptación de dichos contenidos, ni la asociación de los gestores de detapeo.net con los responsables de dichas páginas web, por lo que rechaza toda responsabilidad en relación con los mismos, así como por los daños que pudieran causarse por cualquier motivo en su sistema informático (equipo y aplicaciones), documentos o ficheros.\ndetapeo.net sólo podrá ser responsable por dichos contenidos conforme a lo establecido en la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico, en caso de que, habiendo tenido conocimiento efectivo de su ilicitud o de que lesiona los bienes o intereses de un tercero, no suprima o inutilice el enlace a los mismos.\n\ndetapeo.net no garantiza la fiabilidad, disponibilidad o continuidad de este sitio web ni de sus contenidos por motivos técnicos, de seguridad, control o mantenimiento del servicio, por fallos debidos al servidor que aloja los contenidos o de otros intermediarios o proveedores, por ataques contra el sistema informático, ni por cualesquiera otros motivos que se deriven de causas que escapen a su control, por lo que se exime de cualquier responsabilidad, directa o indirecta, por los mismos.\n\nLos gestores de detapeo.net excluyen toda responsabilidad por las informaciones publicadas por los usuarios.\n";
}
?>
