<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include classes.'Log.php';
include includes.'html_user.php';
include includes.'util_images.php';

force_authentication();
print_header('Modificar perfil');
print_tabs(TAB_PROFILE);
echo '<div id="main_sub">', "\n";
do_edit_profile();
echo '</div>', "\n";
print_footer();
///////////////////////////////////////////////////////////////
function do_edit_profile(){
	global $current_user, $settings;
	$user_id = 0;
	//Los administradores pueden editar el perfil de los usuarios
	if ($current_user->is_admin() && isset($_GET['id']))
		$user_id = (int)$_GET['id'];
	else
		$user_id = $current_user->id;

	//Recuperamos el usuario a modificar
	if ($user=UserManager::get_user($user_id)){
		print_user_right_side($user);		//Escribimos la parte derecha, con las estadísticas del usuario
		if (!isset($_POST['submitted'])){
			print_user_edit_form($user);
		}else{
			//Recogemos la nueva información del usuario y comprobamos si es correcta
			if (isset($_POST['submitted'])){
				$user_password = trim($_POST['password']);
				$user_password2 = trim($_POST['password2']);
				$user->url = clean_input_string($_POST['web']);
				//Comprobamos los datos introducidos
				$error_msg = check_user_edit_form_fields($user->login, $user_password, $user_password2, $user->url);
				if (count($error_msg)>0){
					print_user_edit_form($user, $error_msg);
					die;
				}else{
					//Recibimos la imagen del avatar
					$error_msg = upload_avatar($user->id);
					if (count($error_msg)>0){
						print_user_edit_form($user, $error_msg);
						die;
					}
					if (!empty($user_password))
						$user->password = encode_password($user_password);
				}
			}else{		//No se ha enviado nueva información de usuario, eliminamos la posibilidad de que se modifique la contraseña
				$user->password = '';
			}
			//Administrador modificando el nivel del usuario
			if (isset($_POST['level']) && $current_user->is_admin()){
				$user_level = clean_input_string($_POST['level']);
				$user->admin_text = clean_text(trim($_POST['admin_text']));
				if (is_valid_user_level($user_level))
					$user->level = $user_level;
				else
					$user->level = '';
			}
			//Actualizamos la información del usuario
			if ($user->store()){
				header('Location: .'.$settings['BASE_URL'].'user?id='.$user->id);
			}else{
				print_user_edit_form($user, "Error al actualizar los datos");
			}
		}
	}
}

/**
 * Función encargada de realizar el upload del avatar, comprobar si supera el límite de tamaño y redimensionarlo
 *
 * @param $user_id Id del usuario al que pertenece el avatar
 * @return Mensaje de error si lo hubiese
 */
function upload_avatar($user_id){
	global $settings;
	$error_msg = array();

	if (!empty($_FILES['avatar']['tmp_name'])){
		$avatar_path = get_avatars_local_path($user_id);	//Directorio donde se guardará el avatar
		//Creamos el directorio donde se copiará la imagen
		@mkdir($avatar_path, 0777, true);

		//Recogemos la imagen subida
		if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
			if ($_FILES['avatar']['size'] > $settings['MAX_UPLOAD_AVATAR_SIZE'])
				$error_msg["other"] = "Fichero demasiado grande.";
			else{
				$filename = $avatar_path.$user_id.'.img';
				if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filename)){
					//La redimensionamos a los distintos tamaños
					foreach ($settings['AVATAR_SIZES'] as $size){
						$dstfilename = $avatar_path.$user_id.'-'.$size.'.jpg';
						if (!resize_image($filename,$dstfilename,$size,$size)){
							$error_msg["other"] = "Error al redimensionar la imagen.";
							@unlink($filename);
							return $error_msg;
						}
					}
					@unlink($filename);
				}
			}
		}
	}
	return $error_msg;
}

function print_user_edit_form($user, $error_msg=''){
	global $current_user, $settings;
	$editing_me = $current_user->id == $user->id?true:false;

	echo '<div id="main_izq">', "\n";
	print_user_tabs(TAB_USER_PROFILE);
	echo '<form action="',$_SERVER['REQUEST_URI'],'" id="frmEdit" name="frmEdit"  method="post" enctype="multipart/form-data" class="data">', "\n";
	echo '<dl>', "\n";
	echo '	<dt><label>Usuario:</label></dt><dd>',$user->login,'</dd>',"\n";
	echo '	<dt><label>Email:</label></dt><dd>',$user->email,'&nbsp;</dd>',"\n";

	if ($editing_me){
		echo '	<dt><label for="password">Contraseña:</label></dt>';
		echo '	<dd><input type="password" name="password" id="password" maxlength="32" size="50" tabindex="1" />';
		if (!empty($error_msg["password"]))
			echo '<br/><span class="warning">',$error_msg["password"],'</span>', "\n";
		echo '	<br/><span class="grey">Si no introduces ningún dato se mantendrá la contraseña actual.</span></dd>', "\n";

		echo '	<dt><label for="password2">Repetir Contraseña:</label></dt>';
		echo '	<dd><input type="password" name="password2" id="password2" maxlength="32" size="50" tabindex="2" />';
		if (!empty($error_msg["password2"]))
			echo '<br/><span class="warning">',$error_msg["password2"],'</span>', "\n";
		echo '	</dd>', "\n";
		echo '	<dt><label for="web">Web:</label></dt>';
		echo '	<dd><input type="text" name="web" id="web" maxlength="255" size="50" tabindex="3" value="',$user->url,'"/></dd>';
		echo '	<dt><label for="avatar">Avatar:</label></dt>';
		echo '	<dd><input type="file" name="avatar" id="avatar" size="50" tabindex="4" /><br/><span class="grey">Debe ser una imagen cuadrada de no más de 100 KB.</span></dd>';
	}
	if ($current_user->is_admin()){
		echo '	<dt><label for="level">Nivel:</label></dt>';
		echo '<dd>';
		print_user_level_combobox($user->level);
		echo '</dd>', "\n";
		echo '	<dt></dt>';
		echo '<dd><textarea name="admin_text" id="admin_text" tabindex="6" rows="5" cols="90">',$user->admin_text,'</textarea>';
		echo '</dd>', "\n";
	}
	echo '	<dt>&nbsp;</dt>', "\n";
	echo '	<dd style="margin-top:12px;"><a href="javascript:document.frmEdit.submit();" class="bot" style="width:7em;">Guardar cambios</a>';
	echo ' <a href="',$settings['BASE_URL'],'user?',$_SERVER["QUERY_STRING"],'" rel="nofollow" class="bot" style="width:7em;">Cancelar</a></dd>', "\n";
	echo '</dl>', "\n";
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="',$settings['MAX_UPLOAD_AVATAR_SIZE'],'" />', "\n";
	echo '<input type="hidden" name="submitted" id="submitted" value="true" />', "\n";
	echo '<input type="submit" class="submit_hidden" />', "\n";
	echo '</form>', "\n";
	if (!empty($error_msg["other"]))
		echo '<p class="error">',$error_msg["other"],'</p>', "\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
}

function check_user_edit_form_fields($login, $password, $password2, $web) {
	$error_msg = array();
	/* TODO BAN?
	 if(check_ban_proxy()) {
		register_error(_("IP no permitida"));
		$rt=true;
		}*/

	if ($password){
		if (preg_match('/[ \']/', $password) || preg_match('/[ \']/', $password2))
			$error_msg["password"] = "Caracteres inválidos en la clave";

		else if (strlen($password) < 5 )
			$error_msg["password"] = "Clave demasiado corta, debe ser de 5 o más caracteres";

		else if (password_strength($password, $login) < 14 )
		$error_msg["password"] = "La clave introducida es muy fácil de adivinar.<br/>Prueba a usar mayúsculas, minúsculas y números.";

		else if ($password !== $password2)
			$error_msg["password2"] = "Las claves no coinciden";
	}
	return $error_msg;
}

?>
