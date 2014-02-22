<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include classes.'CaptchaSecurityImages.php';
include classes.'Log.php';
include classes.'Mail.php';

print_header('Recuperar contraseña');
print_tabs(TAB_RECOVER);
echo '<div id="main_sub">', "\n";
print_right_side();
do_recover();
echo '</div>', "\n";
print_footer();
///////////////////////////////////////////////////////////////
function do_recover(){
	if (!isset($_POST['user_data'])){
		print_form();

	}else{
		$user_data = clean_input_string($_POST['user_data']);

		//Comprobamos los campos
		$error_msg = check_form_fields($user_data);
		if (count($error_msg)>0){
			print_form($user_data, $error_msg);

		}else{
			if (is_email($user_data)){
				$user = UserManager::get_user_by_email($user_data);
			}else{
				$user = UserManager::get_user_by_login($user_data);
			}
			if (!$user){
				$error_msg = array ("user_data" => "No existe ningún usuario con ese login o email");
				print_form($user_data, $error_msg);

			}else{
				if (UserManager::recover($user))
					print_message('<p class="ok">Se ha enviado un correo electrónico a tu cuenta de email, en él encontrarás una dirección que te permitirá acceder a tu perfil de usuario para modificar tu contraseña.</p>');
				else
					print_message('<p class="error">Se ha producido un error inesperado al intentar recuperar tu contraseña. <br/>Por favor intentelo más tarde y perdone las molestias</p>');
			}
		}
	}
}

/**
 * Escribe el formulario de recuperación de contraseña
 *
 * @param String $user_data login o email que ha introducido el usuario
 * @param array $error_msg Array con mensajes de error
 */
function print_form($user_data='', $error_msg=''){
	global $settings;
	echo '<div id="main_izq">', "\n";
	echo '<h2>Recuperar contraseña</h2>', "\n";
	echo '<p>Introduce tu <b>usuario</b> o tu <b>email</b> y te enviaremos un correo electrónico para que puedas acceder a tu perfil y modificar tu contraseña.</p>', "\n";
	echo '	<form action="user_pass_recover.php" id="frmRecover" name="frmRecover" method="post" class="data">', "\n";
	echo '	<dl>', "\n";
	echo '		<dt><label for="user_data">Usuario o Email:</label></dt>', "\n";
	echo '		<dd><input type="text" name="user_data" tabindex="1" size="50" id="user_data" value="',$user_data,'" autocomplete="off" />', "\n";
	if (!empty($error_msg["user_data"]))
		echo '<br/><span class="warning">',$error_msg["user_data"],'</span>', "\n";
	echo '		</dd>', "\n";

	echo '		<dt><label for="security_code">Código seguridad:</label></dt>', "\n";
	echo '		<dd><img src="',$settings['BASE_URL'],'img_captcha.php" alt="captcha" width="155" height="45" /><br />', "\n";
	echo '				<input id="security_code" name="security_code" type="text" tabindex="2" class="txt_login"  autocomplete="off" />', "\n";
	if (!empty($error_msg["captcha"]))
		echo '		<span class="warning">',$error_msg["captcha"],'</span>', "\n";
	echo '		</dd>', "\n";

	echo '		<dt>&nbsp;</dt>', "\n";
	echo '		<dd><a href="javascript:document.frmRecover.submit();" class="bot" style="width:7em;">Aceptar</a></dd>', "\n";
	echo '	</dl>', "\n";
	echo '	<input type="submit" class="submit_hidden" />', "\n";
	echo '	</form>', "\n";
	if (!empty($error_msg["other"]))
		echo '<p class="error">',$error_msg["other"],'</p>', "\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
}

function check_form_fields($user_data) {
	$error_msg = array();

	if (empty($user_data))
		$error_msg["user_data"] = "Debes introducir un usuario o un email";

	else if (!CaptchaSecurityImages::is_human())
		$error_msg["captcha"] = "Código de seguridad erroneo";

	return $error_msg;
}
?>
