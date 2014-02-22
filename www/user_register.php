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
include includes.'visiglyphs.php';

print_header('Nueva cuenta de usuario');
print_tabs(TAB_REGISTER);
echo '<div id="main_sub">', "\n";
print_register_steps();
do_register();
echo '</div>', "\n";
print_footer();
///////////////////////////////////////////////////////////////
function do_register(){
	global $settings;
	if (!isset($_POST['submitted'])){
		print_form();

	}else{
		$login = mb_substr(clean_input_string($_POST['login']),0,32);
		$email = mb_substr(clean_input_string($_POST['email']),0,64);
		$password = mb_substr(trim($_POST['password']),0,32);
		$password2 = mb_substr(trim($_POST['password2']),0,32);
		if (isset($_POST['accept_rules']))
			$accept_rules = mb_substr($_POST['accept_rules'],0,6);
		else
			$accept_rules = '';

		//Comprobamos los campos
		$error_msg = check_form_fields($login, $email, $password, $password2, $accept_rules);
		if (count($error_msg)>0){
			print_form($login, $email, $accept_rules, $error_msg);
		}else{
			//Registramos el usuario
			if ($user_id=UserManager::register($login,$password,$email)){
				//Creamos el avatar del usuario
				create_user_avatar($user_id, $email);
				print_message('<p class=ok style="margin:10px"><strong>Usuario registrado correctamente.</strong><br/>Compruebe su cuenta de correo electrónico y acceda lo antes posible a la url de validación que le hemos enviado.<br/>Hasta que no valide su cuenta esta se encontrará desactivada.</p>');
			}else{
				print_message('<p class="error" style="margin:10px"><strong>Error al crear el usuario.</strong><br/>Se ha producido un error inesperado al dar de alta el usuario, por favor vuelva a intentarlo.</p>');
			}
		}
	}
}

//Escribe en pantalla los pasos para la creación de una nueva cuenta de usuario
function print_register_steps(){
	echo '	<div id="main_der">', "\n";
	echo '		<div id="contenedor_login">',"\n";
	echo '			<h2>Pasos:</h2>',"\n";
	echo '			<ul>',"\n";
	echo '				<li class="s_bullet">Rellenar los campos y pulsar el botón "Crear mi cuenta"</li>',"\n";
	echo '				<li class="s_bullet">Recibir correo electrónico de confirmación</li>',"\n";
	echo '				<li class="s_bullet">Acceder a la URL de validación</li>',"\n";
	echo '			</ul>',"\n";
	echo '		</div>',"\n";
	echo '	</div>',"\n";
}

//Escribe en pantalla el formulario de nueva cuenta de usuario
function print_form($login='', $email='', $accept_rules='', $error_msg=''){
	global $settings;
	$accepted='';
	if ($accept_rules == "accept")
		$accepted='checked';

	echo '<div id="main_izq">', "\n";
	echo '<h2>Nueva cuenta de usuario</h2>', "\n";
	echo '<p>Rellena los siguientes datos y pulsa el botón <strong>"Crear mi cuenta"</strong>. A continuación se te enviará un correo electrónico para que puedas activar tu usuario.</p>', "\n";
	echo '<form action="',$_SERVER['REQUEST_URI'],'" id="frmRegister" name="frmRegister" method="post" class="data">', "\n";
	echo '<dl>', "\n";
	echo '	<dt><label for="login">Usuario:</label></dt>', "\n";
	echo '	<dd id="loginContainer"><input type="text" name="login" size="50" maxlength="32" tabindex="1" id="login" value="',$login,'" autocomplete="off"/>';
	if (!empty($error_msg["login"]))
		echo '<br/><span id="loginWarning" class="warning">',$error_msg["login"],'</span>', "\n";
	echo '	</dd>', "\n";

	echo '	<dt><label for="email">Email:</label></dt>', "\n";
	echo '	<dd id="emailContainer"><input type="text" name="email" maxlength="64" size="50" tabindex="2" id="email" value="',$email,'" autocomplete="off"/>';
	if (!empty($error_msg["email"]))
		echo '<br/><span id="emailWarning" class="warning">',$error_msg["email"],'</span>', "\n";
	echo '	</dd>', "\n";

	echo '	<dt><label for="password">Contraseña:</label></dt>', "\n";
	echo '	<dd><input type="password" name="password" id="password" maxlength="32" tabindex="3" size="50" />';
	if (!empty($error_msg["password"]))
		echo '<br/><span class="warning">',$error_msg["password"],'</span>', "\n";
	echo '	</dd>', "\n";

	echo '	<dt><label for="password2">Repetir Contraseña:</label></dt>', "\n";
	echo '	<dd><input type="password" name="password2" id="password2" maxlength="32" tabindex="4" size="50" />';
	if (!empty($error_msg["password2"]))
		echo '<br/><span class="warning">',$error_msg["password2"],'</span>', "\n";
	echo '	</dd>', "\n";

	echo '	<dt><label for="security_code">Código seguridad:</label></dt>', "\n";
	echo '	<dd><img src="',$settings['BASE_URL'],'img_captcha.php" alt="captcha" width="155" height="45"/><br/>', "\n";
	echo '			<input id="security_code" name="security_code" type="text" tabindex="5" class="txt_login"  autocomplete="off"/>', "\n";
	if (!empty($error_msg["captcha"]))
		echo ' <span class="warning">',$error_msg["captcha"],'</span>', "\n";
	echo '	</dd>', "\n";
	echo '	<dt><label for="legal">Condiciones de uso:</label></dt>', "\n";
	echo '	<dd><textarea rows="5" name="legal" id="legal" cols="84" readonly="readonly" onfocus="this.rows=10" onblur="this.rows=5">';
	print_legal_conditions();
	echo '</textarea><br/>';
	echo '<input type="checkbox" name="accept_rules" value="accept" id="accept_rules" tabindex="6" class="none" ',$accepted,' />', "\n";
	echo '			<label for="accept_rules" class="normal">Al marcar esta casilla, indicas que has leído y aceptas las <strong>condiciones de uso del servicio</strong>.</label>', "\n";
	echo '	</dd>', "\n";
	echo '</dl>', "\n";
	echo '<p style="text-align:center;margin-top:14px;"><a id="btnRegister" href="javascript:document.frmRegister.submit()" class="bot" style="width:7em;">Crear mi cuenta</a></p>', "\n";
	echo '<input type="hidden" name="submitted" value="true" />', "\n";
	echo '<input type="submit" class="submit_hidden" />', "\n";
	echo '</form>', "\n";
	if (!empty($error_msg["other"]))
		echo '<p class="error">',$error_msg["other"],'</p>', "\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
}

//Comprueba que se han rellenado los datos correctamente, en caso contrario
//devuelve un array con la descripción del error referente a un dato
function check_form_fields($login, $email, $password, $password2, $accept_rules) {
	$error_msg = array(); //"login", "email", "password", "password2", "captcha", "accept");

	/* TODO Check Ban proxy
	 if(check_ban_proxy()) {
		register_error(_("IP no permitida"));
		$rt=true;
		}*/
	if ($accept_rules !== 'accept')
		$error_msg["other"] = "Debes aceptar las condiciones de uso";

	else if (!isset($login) || strlen($login) < 3)
		$error_msg["login"] = "El nombre de usuario debe ser de 3 o más caracteres";

	else if (!check_username($login))
		$error_msg["login"] = "Caracteres no admitidos o no comienzan con una letra";

	else if (UserManager::login_exists($login))
		$error_msg["login"] = "El usuario ya existe";

	else if (empty($email))
		$error_msg["email"] = "Debes introdir un correo electrónico.";

	else if (!check_email($email))
		$error_msg["email"] = "El correo electrónico no es correcto";

	else if (UserManager::email_exists($email))
		$error_msg["email"] = "Ya existe otro usuario con esa dirección de correo";

	else if (preg_match('/[ \']/', $password) || preg_match('/[ \']/', $password2))
		$error_msg["password"] = "Caracteres inválidos en la clave";

	else if (strlen($password) < 5 )
		$error_msg["password"] = "Clave demasiado corta, debe ser de 5 o más caracteres";

	else if (password_strength($password, $login) < 14 )
		$error_msg["password"] = "La clave introducida es muy fácil de adivinar.<br/>Prueba a usar mayúsculas, minúsculas y números.";

	else if ($password !== $password2)
		$error_msg["password2"] = "Las claves no coinciden";

	else if (!CaptchaSecurityImages::is_human())
		$error_msg["captcha"] = "Código de seguridad erroneo";

	return $error_msg;
}

?>
