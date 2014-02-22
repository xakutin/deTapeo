<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function print_user_profile($user=0, $is_me=false, $from=''){
	global $current_user, $settings;
	if ($user){
		echo '<div id="profile">'."\n";
		echo '	<img src="',get_avatar_url($user->id, 80),'" class="avatar" alt="',$user->login,'"/>'."\n";
		echo '	<dl>'."\n";
		echo '		<dt>Usuario:</dt><dd>',$user->login,'</dd>'."\n";
		if ($is_me || ($current_user && $current_user->is_admin()))
			echo '		<dt>Email:</dt><dd>',$user->email,'&nbsp;</dd>'."\n";
		if (!empty($user->url)){
			echo '		<dt>Web:</dt><dd><a href="',$user->url,'" rel="nofollow">',$user->url,'</a></dd>'."\n";
		}
		if ($current_user && $current_user->is_admin()){
			echo '		<dt>Fecha registro:</dt><dd>',get_date_time($user->creation_date),'</dd>'."\n";
			echo '		<dt>Fecha modificación:</dt><dd>',get_date_time($user->modification_date),'</dd>'."\n";
			echo '		<dt>Fecha validación:</dt><dd>',get_date_time($user->validation_date),'</dd>'."\n";
			echo '		<dt>Nivel:</dt><dd>',get_user_level_description($user->level),'</dd>'."\n";
			echo '		<dt>&nbsp;</dt><dd>',htmlentities($user->admin_text),'</dd>'."\n";
		}else{
			echo '		<dt>Fecha alta:</dt><dd>',get_date_time($user->validation_date),'</dd>'."\n";
			if ($is_me)
				echo '		<dt>Nivel:</dt><dd>',get_user_level_description($user->level),'</dd>'."\n";
			if (!empty($user->admin_text))
				echo '		<dt>Comentario:</dt><dd>',htmlentities($user->admin_text),'</dd>'."\n";
		}
		echo '	</dl>'."\n";
		echo '</div>'."\n";
		if ($is_me){
			echo '	<p style="clear:left;text-align:center;padding-top:20px;">', "\n";
			echo '		<a href="',$settings['BASE_URL'],'user_edit.php" class="bot" rel="nofollow">Modificar</a>', "\n";
			echo '	</p>', "\n";

		}else if ($current_user && $current_user->is_admin()){
			echo '	<p style="clear:left;text-align:center;padding-top:20px;">', "\n";
			echo '		<a href="',$settings['BASE_URL'],'user_edit.php?id=',$user->id,'" class="bot" rel="nofollow">Modificar</a>', "\n";
			echo '	</p>', "\n";
		}
	}
	if ($from=='validate')
		echo '<p class="ok"><strong>Cuenta activada.</strong></p>', "\n";
}


/**
 * Escribe la parte derecha de las páginas de usuario
 *
 * @param User $user Usuario
 */
function print_user_right_side($user=0){
	echo '	<div id="main_der">',"\n";
	print_add_bar_button();
	print_user_statistics($user);
	echo '	</div>',"\n";
}

/**
 * Escribe en la página las estadistivas de un usuario
 *
 * @param User $user Usuario
 */
function print_user_statistics($user=0){
	if ($user){
		$user->load_stats();
		echo '<div class="contenedor_zonas">'."\n";
		echo '	<h2>Estadísticas</h2>'."\n";
		echo '	<dl class="ficha">'."\n";
		echo '		<dt>Bares enviados:</dt><dd>',$user->total_bars,'</dd>'."\n";
		echo '		<dt>Bares publicados:</dt><dd>',$user->total_published_bars,'</dd>'."\n";
		echo '		<dt>Comentarios:</dt><dd>',$user->total_comments,'</dd>'."\n";
		echo '		<dt>Votos:</dt><dd>',$user->total_votes,'</dd>'."\n";
		echo '	</dl>'."\n";
		echo '</div>'."\n";
	}
}

function print_user_tabs($selected_tab){
	global $settings;

	//Construimos el query string de las páginas no seleccionadas
	$query_string = '';
	if (isset($_GET['id'])) $query_string='?id='.$_GET['id'];
	//Querystring de la página actual
	$actual_query_string = '';
	if (!empty($_SERVER["QUERY_STRING"])) $actual_query_string='?'.$_SERVER["QUERY_STRING"];

	echo '	<div id="pestanas_n2">'."\n";
	echo '		<ul>'."\n";
	if ($selected_tab == TAB_USER_PROFILE)
		echo '			<li><a href="',$settings['BASE_URL'],'user',$actual_query_string,'" class="current" rel="nofollow">Datos</a></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'user',$query_string,'" rel="nofollow">Datos</a></li>'."\n";

	if ($selected_tab == TAB_USER_BARS)
		echo '			<li><a href="',$settings['BASE_URL'],'user_bars_sended.php',$actual_query_string,'" class="current" rel="nofollow">Enviados</a></li>'."\n";
	else
		echo '			<li><a href="',$settings['BASE_URL'],'user_bars_sended.php',$query_string,'" rel="nofollow">Enviados</a></li>'."\n";

	if ($selected_tab == TAB_USER_VOTES)
		echo '			<li class="last"><a href="',$settings['BASE_URL'],'user_bars_voted.php',$actual_query_string,'" class="current" rel="nofollow">Votados</a></li>'."\n";
	else
		echo '			<li class="last"><a href="',$settings['BASE_URL'],'user_bars_voted.php',$query_string,'" rel="nofollow">Votados</a></li>'."\n";

	echo '		</ul>'."\n";
	echo '	</div>'."\n";
}

function print_user_level_combobox($level=LEVEL_NORMAL){
	echo '<select name="level" id="level" tabindex="5">';
	echo '	<option value="',LEVEL_BANNED,'" ',$level==LEVEL_BANNED?'selected="selected"':'','>Baneado</option>';
	echo '	<option value="',LEVEL_DISABLED,'" ',$level==LEVEL_DISABLED?'selected="selected"':'','>Deshabilitado</option>';
	echo '	<option value="',LEVEL_NORMAL,'" ',$level==LEVEL_NORMAL?'selected="selected"':'','>Normal</option>';
	echo '	<option value="',LEVEL_EDITOR,'" ',$level==LEVEL_EDITOR?'selected="selected"':'','>Editor</option>';
	echo '	<option value="',LEVEL_ADMIN,'" ',$level==LEVEL_ADMIN?'selected="selected"':'','>Administrador</option>';
	echo '</select>';
}

function get_user_level_description($level){
	switch ($level){
		case LEVEL_BANNED: return 'Baneado';
		case LEVEL_DISABLED: return 'Deshabilitado';
		case LEVEL_NORMAL: return 'Normal';
		case LEVEL_EDITOR: return 'Editor';
		case LEVEL_ADMIN: return 'Administrador';
	}
}
?>
