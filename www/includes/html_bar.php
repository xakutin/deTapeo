<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
/**
 * Escribe en pantalla la información de un Bar en un listado
 *
 * @param Bar $bar Bar a mostrar
 * @param Photo $cover_photo Foto de portada
 * @param User $author usuario que envió el bar
 * @param boolean $show_buttons Indica si se deben mostrar los botones de comentario, votar... o la información de estado del Bar
 * @param boolean $show_author_vote Indica si en el detalle del Bar de mostrará el voto del autor
 */
function bar_print_sumary($bar, $cover_photo, $author, $show_buttons=true, $show_user_vote=false, $user=null){
	global $settings, $current_user, $bot;
	$bar_detail_url = $bar->get_relative_permalink();

	//Se está mostrando la información de un usuario
	if ($show_user_vote && $user){
		$bar_detail_url .= '?user='.$user->id;	//En la página de detalle se mostrará el voto del usuario
		if ($bar->votes_avg<0)	//El usuario ha descartado el Bar
			$bar->future_status = get_bar_status_from_vote_value($bar->votes_avg);
	}
	//Dependiendo de la "democracia" se coge una media u otra
	if ($settings['DEMOCRACY'])
		$votes_avg = $bar->real_votes_avg;
	else
		$votes_avg = $bar->votes_avg;

	echo '<li class="clear_left">', "\n";
	//Aviso de si se ha informado de descartar el Bar
	//TODO: poner enlace a ayuda para indicar lo q deben hacer, votar positivo o negativo
	if (!$bot && $bar->is_future_discarded() && ($show_user_vote || $bar->is_published())){
		if ($show_user_vote && $user)
			echo '		<div class="warning_discarded">Este usuario ha ',bar_get_discard_description_by_status($bar->future_status),'</div>', "\n";
		else
			echo '		<div class="warning_discarded"><strong>Advertencia:</strong> varios usuarios nos han ',bar_get_discard_description_by_status($bar->future_status),'</div>', "\n";
	}

	echo '	<div class="contenedor_foto">', "\n";
	if ($cover_photo){
		echo '		<p><a href="',$bar_detail_url,'" title="',$bar->name,'"><img width="',$settings['IMG_BAR_THUMBNAIL_WIDTH'],'" height="',$settings['IMG_BAR_THUMBNAIL_HEIGHT'],'" src="',get_photos_path($bar->id),$cover_photo->thumbnail,'" class="foto_bar" alt="',$bar->name,'" border="0" /></a></p>', "\n";
	}else{
		//Ponemos una foto generica
		echo '		<p><a href="',$bar_detail_url,'" title="',$bar->name,'"><img width="',$settings['IMG_BAR_THUMBNAIL_WIDTH'],'" height="',$settings['IMG_BAR_THUMBNAIL_HEIGHT'],'" src="',get_photos_path(),get_generic_cover_photo($bar->id),'" class="foto_bar" alt="',$bar->name,'" border="0" /></a></p>', "\n";
	}
	if (!$bot){
		echo '		<p class="puntuacion" id="summary-stars-',$bar->id,'">';
		print_stars($votes_avg);
		echo '		</p>';
	}
	echo '	</div>', "\n";
	echo '	<div class="nombre_bar">', "\n";
	echo '		<h1><a href="',$bar_detail_url,'" title="',$bar->name,'">',$bar->name,'</a></h1>', "\n";
	echo '		<h2>',get_address_in_human_format($bar->street_type, $bar->street_name, $bar->street_number, $bar->town_name, $bar->province),'</h2>', "\n";
	echo '	</div>', "\n";
	echo '	<p class="tabla_bar">', "\n";
	echo '		<span class="gris">Zona</span>', "\n";
	echo '		<span class="respuesta" title="',$bar->zone_name,'">',text_to_summary($bar->zone_name,16),'</span>', "\n";
	echo '		<span class="gris">Caña</span>', "\n";
	echo '		<span class="respuesta" title="',$bar->beer_price,' &euro;">', number_format($bar->beer_price, 2, '.', ''), ' &euro;</span>', "\n";
	echo '	</p>', "\n";
	echo '	<span class="txt_resumen">', text_to_summary($bar->text, 250), '</span>', "\n";
	if (!$bot){
		echo '	<div class="bocadillo_cont">', "\n";
		echo '		<ul>', "\n";
		if ($bar->is_published() && $show_buttons){
			if ($bar->num_comments==1)
				echo '			<li class="ico_comment"><a href="',$bar_detail_url,'#comments" class="hund" >1 comentario</a></li>', "\n";
			else
				echo '			<li class="ico_comment"><a href="',$bar_detail_url,'#comments" class="hund" >',$bar->num_comments,' comentarios</a></li>', "\n";

			if ($current_user){
				echo '			<li class="ico_vote"><a href="#" id="btn-vote-',$bar->id,'" class="hund" onclick="tooltip.user_vote(event,',$bar->id,');return false;" onmouseover="tooltip.dontHide()" onmouseout="tooltip.hide(event);" >votar</a></li>', "\n";
				echo '			<li class="ico_discard"><a href="#" class="hund" onclick="tooltip.menu_discard(event,',$bar->id,');return false;" onmouseover="tooltip.dontHide()" onmouseout="tooltip.hide(event)" >advertir</a></li>', "\n";
			}else{
				echo '			<li class="ico_vote_large"><a href="#" id="btn-vote-',$bar->id,'" class="hund" onclick="tooltip.msg_login(event);return false;" onmouseover="tooltip.dontHide()" onmouseout="tooltip.hide(event)" >votar</a></li>', "\n";
			}
		}else{
			echo '			<li class="void_large"><strong>Estado: </strong>',get_bar_status_description($bar->status),'</li>', "\n";
		}
		echo '			<li class="fecha_anadido">', "\n";
		if ($bar->is_published()){
			echo '				<span class="anadido">Publicado el</span>', "\n";
			echo '				<span class="fecha">',date("d/m/Y",$bar->publication_date),'</span>', "\n";
		}else{
			echo '				<span class="anadido">Editado el</span>', "\n";
			echo '				<span class="fecha">',date("d/m/Y",$bar->edition_date),'</span>', "\n";
		}
		echo '			</li>', "\n";
		echo '			<li><a href="',$settings['BASE_URL'],'user?id=',$author->id,'" rel="nofollow" title="',$author->login,'"><img src="',get_avatar_url($author->id),'" class="avatar" alt="',$author->login,'"/></a></li>', "\n";
		echo '			<li class="bocadillo"></li>', "\n";
		if ($bar->is_editable())
			echo '			<li class="ico_edit"><a href="',$settings['BASE_URL'],'bar_data.php?id=',$bar->id,'" class="hund" rel="nofollow" >editar</a></li>', "\n";

		echo '		</ul>', "\n";
		echo '	</div>', "\n";
	}
	echo '</li>', "\n";
}

/**
 * Escribe en pantalla la información de detalle de un bar
 *
 * @param Bar $bar
 * @param User $author
 * @param int $current_user_vote
 * @param array $dbphotos
 * @param User $another_user
 * @param int $another_user_vote
 */
function bar_print_detail($bar, $author, $current_user_vote, $dbphotos=null, $another_user = null, $another_user_vote = null){
	global $current_user, $settings, $bot;
	$is_me = false;
	//Comprobamos si la información del usuario ha mostrar es la del mismo usuario que ha entrado
	if ($current_user && $another_user)
		if ($current_user->id == $another_user->id)
			$is_me=true;

	//Cogemos la media, dependiendo del tipo de "democracia"
	if ($settings['DEMOCRACY'])
		$votes_avg = $bar->real_votes_avg;
	else
		$votes_avg = $bar->votes_avg;

	if (!$bar->is_published())
		echo '		<div class="warning_discarded" style="width:97%"><strong>Aviso:</strong> este bar no está publicado. Estado: <strong>',get_bar_status_description($bar->status),'</strong></div>', "\n";
	else if (!$bot && $bar->is_future_discarded())
		echo '    <div class="warning_discarded" style="width:97%"><strong>Advertencia:</strong> ',bar_get_discard_description_by_status($bar->future_status),'</div>', "\n";

	echo '<h1>',$bar->name,'</h1>',"\n";
	echo '<h2>',get_address_in_human_format($bar->street_type, $bar->street_name, $bar->street_number, $bar->town_name, $bar->province, $bar->postal_code),'</h2>',"\n";
	echo '<span class="detail_text">',text_to_html($bar->text),'</span>',"\n";
	echo '<dl class="ficha">',"\n";
	if ($bar->zone_name)
		echo '<dt>Zona:</dt><dd>',$bar->zone_name,'</dd>',"\n";
	if ($bar->web_url)
		echo '<dt>Web:</dt><dd><a href="',$bar->web_url,'" rel="nofollow">',$bar->web_url,'</a></dd>',"\n";
	if ($bar->phone)
		echo '<dt>Teléfono:</dt><dd>',$bar->phone,'</dd>',"\n";

	echo '<dt>Precio Caña:</dt><dd>',number_format($bar->beer_price, 2, '.', ''),' &euro;</dd>',"\n";
	if ($bar->specialities)
		echo '<dt>Tapas:</dt><dd>',$bar->specialities,'</dd>',"\n";

	if (!$bot){
		//Votos
		if (isset($current_user)){
			echo '<dt>Tu voto:</dt><dd>';
			if ($current_user_vote<0)
				echo 'Nos has ', bar_get_discard_description_by_vote_value($current_user_vote);
			else
				print_stars_bar_detail($current_user_vote, $bar);

			echo '&nbsp;</dd>',"\n";
		}
		if (!$is_me && $another_user){
			echo '<dt>Voto de <a href="',$settings['BASE_URL'],'user?id=',$another_user->id,'" rel="nofollow" title="',$another_user->login,'" class="und">',$another_user->login,'</a>:</dt><dd>';
			if ($another_user_vote<0)
				echo 'Nos ha ', bar_get_discard_description_by_vote_value($another_user_vote);
			else
				print_stars($another_user_vote);
			echo '</dd>',"\n";
		}
		echo '<dt>Valoración:</dt><dd>';
		print_stars($votes_avg);
		if ($bar->num_votes==1)
			echo '<span id="votesCount" class="grey" style="margin-left:10px;vertical-align: top;">(1 voto)</span></dd>';
		else
			echo '<span id="votesCount" class="grey" style="margin-left:10px;vertical-align: top;">(',$bar->num_votes,' votos)</span></dd>';
		echo '</dl>',"\n";

		//Fotos y Mapa
		if ($dbphotos || count($dbphotos)>0){
			echo '<div id="photo_thumbs_container">',"\n";
			if (count($dbphotos)>6)
				echo '<a href="#" id="arrow_up" onclick="return false;" onmouseover="scrollUp();" onmouseout="stopScroll();"><img src="',$settings['BASE_URL'],'img/f_naranja_up.gif" border=0 alt="arriba" /></a>',"\n";
			else
				echo '<span style="height:11px">&nbsp;</span>',"\n";

			echo '<ul id="photo_thumbs">',"\n";
			$photos_path = get_photos_path($bar->id);
			foreach ($dbphotos as $dbphoto){
				echo '<li><a href="javascript:showPhoto(\'',$photos_path,$dbphoto->photo_large_image_name,'\');" class="photo_thumb" onmouseover="showPhoto(\'',$photos_path,$dbphoto->photo_large_image_name,'\');" onmouseout="hidePhoto();"><img src="',$photos_path,$dbphoto->photo_small_image_name,'" border="0" alt="',$bar->name,'" /></a></li>',"\n";
			}
			echo '</ul>',"\n";
			if (count($dbphotos)>6)
				echo '<a href="#" id="arrow_down" onclick="return false;" onmouseover="scrollDown();" onmouseout="stopScroll();"><img src="',$settings['BASE_URL'],'img/f_naranja_down.gif" border=0 alt="abajo"/></a>',"\n";
			echo '</div>',"\n";
			echo '<div id="map" class="map_with_photos"><div style="padding-top:201px;text-align:center;"><strong>Cargando</strong><br/><img src="',$settings['BASE_URL'],'img/busy.gif" alt="cargando..." width="32" height="32" /></div></div>',"\n";
			echo '<div id="photo_big_container"><img id="photo_big" src="" alt="',$bar->name,'" /></div>',"\n";

		}else{
			echo '<div id="map" class="map_without_photos"><div style="padding-top:184px;text-align:center;"><strong>Cargando</strong><br/><img src="',$settings['BASE_URL'],'img/busy.gif" alt="cargando..." width="32" height="32" /></div></div>',"\n";
		}
		if ($bar->is_editable()){
			echo '	<div style="text-align:right;clear:left;padding:2px 0px 0px 0px;">', "\n";
			echo '		<a href="',$settings['BASE_URL'],'bar_data.php?id=',$bar->id,'" class="bot" rel="nofollow">Editar</a>', "\n";
			echo '	</div>', "\n";
		}
	}else{
		echo '</dl>',"\n";
		//Fotos para el Bot
		if ($dbphotos || count($dbphotos)>0){
			echo '<p>',"\n";
			$photos_path = get_photos_path($bar->id);
			foreach ($dbphotos as $dbphoto){
				echo '<a href="',$photos_path,$dbphoto->photo_large_image_name,'"><img src="',$photos_path,$dbphoto->photo_small_image_name,'" border="0" alt="',$bar->name,'" /></a>',"\n";
			}
			echo '</p>',"\n";
		}
	}
}

/**
 * Devuelve el texto de aviso de posible descartado
 *
 * @param int $vote_value Voto
 * @return texto de aviso de posible descartado
 */
function bar_get_discard_description_by_vote_value($vote_value){
	switch ($vote_value){
		case VOTE_DUPLICATED:
			return 'avisado de que el bar está duplicado.';
		case VOTE_NO_EXISTS:
			return 'avisado de que el bar no existe.';
		case VOTE_NO_TAPA_BAR:
			return 'avisado de que el bar no pone tapas gratis.';
		case VOTE_OBSOLETE:
			return 'avisado de que la información del bar está obsoleta.';
	}
}

/**
 * Devuelve el texto de aviso de posible descartado
 *
 * @param string $future_status estado avisado
 * @return texto de aviso de posible descartado
 */
function bar_get_discard_description_by_status($future_status){
	switch ($future_status){
		case STATUS_DUPLICATED:
			return 'avisado de que este bar está duplicado.';
		case STATUS_NO_EXISTS:
			return 'avisado de que este bar no existe.';
		case STATUS_NO_TAPA_BAR:
			return 'avisado de que este bar no pone tapas gratis.';
		case STATUS_OBSOLETE:
			return 'avisado de que la información de este bar está obsoleta.';
	}
}

/**
 * Escribe el combobox para seleccionar el tipo de calle.
 * Se selecciona el valor recibido por parámetro
 *
 * @param string $type Tipo de calle a seleccionar en el combo
 */
function bar_print_form_type_street_combobox($type = 'Calle'){
	if (empty($type))	$type = 'Calle';
	$street_types = 'Alameda,Avenida,Calle,Camino,Carrer,Carretera,Cuesta,Glorieta,Kalea,Pasaje,Paseo,Plaça,Plaza,Rambla,Ronda,Rúa,Sector,Travesía,Urbanización,Vía';
	echo '<select name="street_type" id="street_type" tabindex="3">';
	$array_st_types = split(',', $street_types);
	foreach ($array_st_types as $street_type){
		$selected = '';
		if ($street_type == $type)
			$selected = 'selected="selected"';
		echo '<option value="', $street_type, '" ', $selected, '>', $street_type, '</option>';
	}
	echo '</select>';
}

/**
 * Escribe el combobox para seleccionar una localidad de una provincia.
 * Si recibe el id de la localidad se selecciona la localidad que corresponde.
 * Si se recibe un nuevo nombre se crea la opción y se deja seleccionada
 *
 * @param int $town_id Id de la localidad a seleccionar
 * @param string $town_name Nmbre de la localidad a añadir
 * @param int $prov_id Id de la provincia a la que pertenecen las localidades
 */
function bar_print_form_town_combobox($town_id=0, $town_name=null, $prov_id=''){
	$disabled = '';
	$founded = false;
	$town_name_lower = '';
	if (empty($prov_id)) $disabled = 'disabled="disabled"';

	echo '<select name="town_id" id="town_id" tabindex="7" style="width:100%" onchange="checkNewOption(this.id);loadZones(\'zone_id\',this.id);" ', $disabled, '>';
	echo '<option value="0"></option>';
	echo '<option value="-1">-- Nueva localidad --</option>';
	if ($prov_id){
		if ($dbtowns = TownManager::get_towns($prov_id)){
			if ($town_name)	$town_name_lower = mb_strtolower($town_name);
			foreach ($dbtowns as $dbtown){
				$selected = '';
				if ($dbtown->town_id == $town_id) $selected = 'selected="selected"';
				if (!empty($town_name) && mb_strtolower($dbtown->town_name) == $town_name_lower) $founded=true;
				echo '<option value="', $dbtown->town_id, '" ', $selected, '>', $dbtown->town_name, '</option>';
			}
		}
	}
	if (!$founded && !empty($town_name))
		echo '<option value="', $town_name, '" selected="selected">', $town_name, '</option>';
	echo '</select>';
}

/**
 * Escribe el combobox para seleccionar la zona de una localidad.
 * Si recibe el id de una zona se selecciona la zona que corresponde.
 * Si se recibe un nuevo nombre de Zona se crea la opción y se deja seleccionada
 *
 * @param int $zone_id Id de la zona a seleccionar
 * @param string $zone_name Nmbre de la zona a añadir
 * @param int $town_id Id de la localidad a la que pertenecen las zonas
 */
function bar_print_form_zones_combobox($zone_id=0, $zone_name=null, $town_id=''){
	$disabled = '';
	$founded = false;
	$zone_name_lower = '';
	if (empty($town_id)) $disabled = 'disabled="disabled"';

	echo '<select name="zone_id" id="zone_id" tabindex="8" style="width:100%" onchange="checkNewOption(this.id);" ', $disabled, '>';
	echo '<option value="0"></option>';
	echo '<option value="-1">-- Nueva zona --</option>';
	if ($town_id){
		if ($dbzones = ZoneManager::get_zones($town_id)){
			if ($zone_name)	$zone_name_lower = mb_strtolower($zone_name);
			foreach ($dbzones as $dbzone){
				$selected = '';
				if ($dbzone->zone_id == $zone_id) $selected = 'selected="selected"';
				if (!empty($zone_name) && mb_strtolower($dbzone->zone_name) == $zone_name_lower) $founded=true;
				echo '<option value="', $dbzone->zone_id, '" ', $selected, '>', $dbzone->zone_name, '</option>';
			}
		}
	}
	if (!$founded && !empty($zone_name))
		echo '<option value="', $zone_name, '" selected="selected">', $zone_name, '</option>';
	echo '</select>';
}

/**
 * Escribe el combobox de selección de provincias. Si recibe el id de una provincia la selecciona
 *
 * @param string $province_id Id de la provincia a seleccionar
 */
function bar_print_form_province_combobox($province_id=''){
	$provinces = "A Coruña,Álava/Araba,Albacete,Alicante,Almería,Asturias,Ávila,Badajoz,Barcelona,Burgos,Cáceres,Cádiz,Cantabria,Castellón,Ceuta,Ciudad Real,Córdoba,Cuenca,Girona,Granada,Guadalajara,Guipúzcoa/Gipuzkoa,Huelva,Huesca,Illes Balears,Jaén,La Rioja,Las Palmas,León,Lleida,Lugo,Madrid,Málaga,Melilla,Murcia,Navarra,Ourense,Palencia,Pontevedra,Salamanca,Santa Cruz de Tenerife,Segovia,Sevilla,Soria,Tarragona,Teruel,Toledo,Valencia,Valladolid,Vizcaya/Bizkaia,Zamora,Zaragoza";
	echo '<select name="province" id="province" tabindex="6" style="width:200px" onchange="loadTowns(\'town_id\',\'zone_id\',this.id);">';
	echo '<option value=""></option>';
	$array_provinces = split(",", $provinces);
	foreach ($array_provinces as $province){
		$selected = '';
		if ($province == $province_id)
			$selected = 'selected="selected"';
		echo '<option value="', $province, '" ', $selected, '>', $province, '</option>';
	}
	echo '</select>';
}

/**
 * Escribe el combobox de selección de estado. Se selecciona el estado que se recibe por parámetro
 *
 * @param string $status Estado a seleccionar
 */
function bar_print_status_combobox($status=STATUS_QUEUED){
	echo '<select name="status" id="status" tabindex="14">';
	echo '	<option value="',STATUS_DUPLICATED,'" ',$status==STATUS_DUPLICATED?'selected="selected"':'','>Duplicado</option>';
	echo '	<option value="',STATUS_NO_EXISTS,'" ',$status==STATUS_NO_EXISTS?'selected="selected"':'','>No existe</option>';
	echo '	<option value="',STATUS_NO_TAPA_BAR,'" ',$status==STATUS_NO_TAPA_BAR?'selected="selected"':'','>No pone tapas</option>';
	echo '	<option value="',STATUS_OBSOLETE,'" ',$status==STATUS_OBSOLETE?'selected="selected"':'','>Información obsoleta</option>';
	echo '  <option value="',STATUS_PUBLISHED,'" ',$status==STATUS_PUBLISHED?'selected="selected"':'','>Publicado</option>';
	echo '  <option value="',STATUS_QUEUED,'" ',$status==STATUS_QUEUED?'selected="selected"':'','>Pendiente</option>';
	echo '</select>';
}

function bar_print_edit_tabs_level2($tab_selected, $bar_id){
	global $settings;
	echo '<div id="pestanas_n2">', "\n";
	echo 	'	<ul>', "\n";

	if ($tab_selected==TAB_EDIT_BAR_METADATA)
		echo 	'		<li><a href="',$settings['BASE_URL'],'bar_data.php?id=', $bar_id, '" class="current" rel="nofollow">Datos</a></li>', "\n";
	else
		echo 	'		<li><a href="',$settings['BASE_URL'],'bar_data.php?id=', $bar_id, '" class="a_n2_off" rel="nofollow">Datos</a></li>', "\n";

	if ($tab_selected==TAB_EDIT_BAR_MAP)
		echo 	'		<li><a href="',$settings['BASE_URL'],'bar_map.php?id=', $bar_id, '" class="current" rel="nofollow">Mapa</a></li>', "\n";
	else
		echo 	'		<li><a href="',$settings['BASE_URL'],'bar_map.php?id=', $bar_id, '" class="a_n2_off" rel="nofollow">Mapa</a></li>', "\n";

	if ($tab_selected==TAB_EDIT_BAR_PHOTOS)
		echo 	'		<li class="last"><a href="',$settings['BASE_URL'],'bar_photos.php?id=', $bar_id, '" class="current" rel="nofollow">Fotos</a></li>', "\n";
	else
		echo 	'		<li class="last"><a href="',$settings['BASE_URL'],'bar_photos.php?id=', $bar_id, '" class="a_n2_off" rel="nofollow">Fotos</a></li>', "\n";
	echo 	'	</ul>', "\n";
	echo 	'</div>', "\n";
}

function bar_print_steps($num){
	$i=1;
	$steps = array ('Rellenar los datos del Bar','Localización en el mapa','Añadir fotos');
	echo '	<div id="main_der">', "\n";
	echo '		<div id="contenedor_login">',"\n";
	echo '			<h2>Pasos:</h2>',"\n";
	echo '			<ul>',"\n";
	foreach ($steps as $step){
		if ($i==$num)
			echo '<li class="s_bullet"><strong>',$step,'</strong></li>',"\n";
		else
			echo '<li class="s_bullet">',$step,'</li>',"\n";
		++$i;
	}
	echo '			</ul>',"\n";
	echo '		</div>',"\n";
	echo '	</div>',"\n";
}
?>
