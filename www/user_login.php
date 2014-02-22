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
print_header('Login');
print_tabs(TAB_LOGIN);
echo '<div id="main_sub">', "\n";
do_login();
print_register_message();
echo '</div>', "\n";
print_footer();

///////////////////////////////////////////////////////////////
function do_login(){
	global $user_ip_int, $settings;
	if (!isset($_POST['login'])){
		print_form('',false,false,'');

	}else{
		$login = clean_input_string(trim($_POST['login']));
		$password = trim($_POST['password']);
		$remember = false;
		if (isset($_POST['remember'])) $remember = true;

		//Comprobamos si ha habido errores anteriores de login
		$previous_login_failed_count = Log::get_previous_login_failed($user_ip_int, 300);
		$previous_login_failed = is_previous_login_failed($previous_login_failed_count);

		if ($previous_login_failed && !CaptchaSecurityImages::is_human()){
			$msg="El código de seguridad no es correcto";
			print_form($login,$remember,$previous_login_failed,$msg);
			Log::login_failed($user_ip_int);

		}else if (!$current_user=UserManager::login($login, $password, $remember)){
			$msg="Usuario o contraseña incorecto";
			Log::login_failed($user_ip_int);
			++$previous_login_failed_count;
			$previous_login_failed = is_previous_login_failed($previous_login_failed_count);
			print_form($login,$remember,$previous_login_failed,$msg);

		}else{
			if(!empty($_REQUEST['return'])) {
				header('Location: '.$_REQUEST['return']);
			} else {
				header('Location: .'.$settings['BASE_URL']);
			}
			die;
		}
	}
}

/**
 * Comprueba si se ha sobrepasado el nº de intentos de login fallidos
 *
 * @param int $previous_login_failed_count Nº de intentos fallidos
 * @return true si se ha sobrepasado el límite, false en caso contrario
 */
function is_previous_login_failed($previous_login_failed_count) {
	global $settings;
	if ($previous_login_failed_count > $settings['MAX_LOGIN_FAILED_TRIES_PREV_CAPTCHA'])
		return true;
	return false;
}

/**
 * Escribe el texto para que el usuario se registre
 *
 */
function print_register_message(){
	global $settings;
	echo '<div id="main_izq">',"\n";
	echo '	<strong>deTapeo</strong> es una web donde la gente puede compartir sus bares de tapas favoritos.',"\n";
	echo '	<p>Si quieres colaborar necesitas <a href="',$settings['BASE_URL'],'user_register.php" class="und">registrarte</a>, sino siempre podrás consultar los bares enviados y ver las opiniones de otros usuarios.<br/>Como usuario registrado podrás:</p>',"\n";
	echo '	<ul>',"\n";
	echo '		<li class="bullet">',"\n";
	echo '			Enviar los bares de tapas que conoces',"\n";
	echo '		</li>',"\n";
	echo '		<li class="bullet">',"\n";
	echo '			Votar para darnos tu opinión sobre la calidad de las tapas',"\n";
	echo '		</li>',"\n";
	echo '		<li class="bullet">',"\n";
	echo '			Ayudarnos a descartar aquellos bares que se han publicado por error',"\n";
	echo '		</li>',"\n";
	echo '		<li class="bullet">',"\n";
	echo '			Escribir comentarios',"\n";
	echo '		</li>',"\n";
	echo '		<li class="bullet">',"\n";
	echo '			Personalizar tu cuenta de usuario',"\n";
	echo '		</li>',"\n";
	echo '	</ul>',"\n";
	echo '	Si tienes alguna duda siempre puedes consultar la página de <a href="',$settings['BASE_URL'],'help" class="und">ayuda</a>, y si deseas ponerte en contacto con nosotros para enviarnos alguna queja, duda o sugerencia puedes mandarnos un correo a webmaster[arroba]detapeo.net.',"\n";
	echo '	<p class="center" style="margin-top:30px"><a href="',$settings['BASE_URL'],'user_register.php" class="bot" style="padding:0.5em 1.5em;width:10em;" rel="nofollow">Regístrate ahora</a></p>',"\n";
	echo '</div>', "\n";
}

/**
 * Escribe el formulario de login
 *
 * @param String $login Login del usuario
 * @param boolean $remember Indica si se recuerda el usuario en el PC
 * @param boolean $show_security_code Indica si se debe mostrar el captcha
 * @param String $error_msg Mensaje de error
 */
function print_form($login, $remember=false, $show_security_code=false, $error_msg){
	global $settings;
	echo '<div id="main_der">',"\n";
	echo '	<form action="user_login.php" id="frmLogin" name="frmLogin" method="post" onsubmit="return true;">',"\n";
	echo '	<div id="contenedor_login">',"\n";
	echo '			<h2>Login</h2>',"\n";
	echo '			<div id="login_introdatos">',"\n";
	echo '				<p><label for="login" class="normal">Usuario</label></p>',"\n";
	echo '				<input type="text" name="login" tabindex="1" id="login" value="',htmlentities($login),'" autocomplete="off" class="txt_login" maxlength="32"/>',"\n";
	echo '				<p><label for="password" class="normal">Contraseña</label></p>',"\n";
	echo '				<input type="password" name="password" id="password" tabindex="2" autocomplete="off" class="txt_login" maxlength="32"/>',"\n";
	if ($show_security_code){
		echo '			<p><label for="security_code" class="normal">Código seguridad</label></p>',"\n";
		echo '			<img src="',$settings['BASE_URL'],'img_captcha.php" alt="captcha" width="155" height="45" /><br />',"\n";
		echo '			<input id="security_code" name="security_code" type="text" tabindex="3" autocomplete="off" class="txt_login" />',"\n";
	}
	if (!empty($error_msg))
		echo '<p class="error">',$error_msg,'</p>',"\n";
	$checked = '';
	if ($remember) $checked = 'checked="checked"';
	echo '				<p><input type="checkbox" name="remember" id="remember" tabindex="4" ',$checked,' /> <label for="remember" class="normal">Recordarme</label></p>', "\n";
	echo '			</div>', "\n";
	echo '			<p><a href="javascript:document.frmLogin.submit();" class="bot_block">Entrar</a></p>', "\n";
	echo '		<p><a href="',$settings['BASE_URL'],'user_pass_recover.php" class="nar_bold_small" rel="nofollow">¿Has olvidado tú contraseña?</a></p>', "\n";
	echo '	</div>', "\n";
	$return = '';
	if (isset($_REQUEST['return']))
		$return = $_REQUEST['return'];
	echo '	<input type="hidden" name="return" value="',htmlspecialchars($return),'" />', "\n";
	echo '	<input type="submit" class="submit_hidden" />', "\n";
	echo '	</form>', "\n";
	echo '</div>', "\n";
}
?>
