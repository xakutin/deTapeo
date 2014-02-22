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
include classes.'Mail.php';
include includes.'visiglyphs.php';

$error = true;
$error_msg = '';
//Recogemos los parámetros
$login = $_GET['l'];				//Login
$time = $_GET['t'];					//Tiempo
$key = $_GET['k'];					//Clave
$op = $_GET['o'];						//validate o recover

//Comprobamos los parámetros
if (!empty($login) && !empty($time) && !empty($key)){
	$time = intval($time);
	$user = UserManager::get_user_by_login($login);
	if ($user){
		//Activación de cuenta
		if ($op == 'validate'){
			if ($user->validation_date){		//Ya está validado
				$error = false;
			}else{
				$dbkey = get_validation_user_key($user->id, $user->password, $time);
				if ($time < $now && $time > ($now - $settings['MAX_SECONDS_TO_VALIDATE_ACCOUNT']) && $key==$dbkey){
					if ($user->validate())
						$error = false;
					else
						$error_msg = '<p class="error"><strong>Error al activar el usuario</strong><br/>Por favor intentelo más tarde y perdone las molestias.</p>';

				}else{
					$user->delete();
					$error_msg = '<p class="warning">Ha pasado el tiempo máximo para poder validar la cuenta con login <strong>'.$login.'</strong>.<br/>Intente volver a <a href="'.$settings['BASE_URL'].'user_register.php" class="und" rel="nofollow">registrarse</a>, y recuerde que tiene un tiempo limitado para acceder a la url de validación.</p>';
				}
			}

		//Recuperación de contraseña
		}else if ($op == 'recover'){
			$dbkey = get_validation_user_key($user->id, $user->password, $time);
			if ($time < $now && $time > ($now - $settings['MAX_SECONDS_TO_RECOVER_PASS']) && $key==$dbkey){
				$error = false;
			}else{
				$error_msg='<p class="warning">Ha pasado el tiempo máximo para poder usar la url de acceso a su perfil.<br/>Si desea volver a intentar recuperar su contraseña pinche <a href="'.$settings['BASE_URL'].'user_pass_recover.php" class="und" rel="nofollow">aquí</a></p>';
			}
		}
	}
}

if ($error){
	print_validate_error($error_msg, $op, $login);
}else{
	//Logamos al usuario
	if (UserManager::login($user->login, $user->password)){
		if ($op == 'validate')
			header('Location: .'.$settings['BASE_URL'].'user?from='.$op);
		else if ($op == 'recover')
			header('Location: .'.$settings['BASE_URL'].'user_edit.php');
	}else{
		$error_msg = "<p class='error'>Error al acceder a la cuenta con login <strong>$login</strong><br/>Por favor intentelo más tarde y perdone las molestias.</p>";
		print_validate_error($error_msg, $op, $login);
	}
}

/**
 * Escribe el mensaje de error de validación o de recuperación de contraseña
 *
 * @param String $error_msg Mensaje de error
 * @param String $op Acción que se está ejecutando
 */
function print_validate_error($error_msg, $op, $login){
	print_header('Error validación');
	if ($op == "validate")
		print_tabs(TAB_REGISTER);
	else
		print_tabs(TAB_RECOVER);

	echo '<div id="main_sub">', "\n";
	if (empty($error_msg)){
		if ($op == "validate")
			$error_msg = '<p class="error"><strong>Error al validar la cuenta</strong><br/>Se ha producido un error al validar la cuenta con login <strong>'.$login.'</strong><br/>Compruebe que la url a la que ha accedido es la misma que la del email que ha recibido.</p>';
		else
			$error_msg = '<p class="error"><strong>Error al recuperar la contraseña</strong><br/>Se ha producido un error al acceder a la cuenta con login <strong>'.$login.'</strong><br/>Compruebe que la url a la que ha accedido es la misma que la del email que ha recibido.</p>';
	}
	print_message($error_msg);
	echo '</div>', "\n";
	print_footer();
}
?>
