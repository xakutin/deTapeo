<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'User.php';
//Posiciones de los valores en la cookie key
define ("LOGIN_POS", 0);
define ("KEY_POS", 1);
define ("VERSION_POS",2);
define ("TIME_POS",3);
define ("REMEMBER_POS",4);
//Operaciones
define ("DELETE_COOKIES",0);
define ("UPDATE_COOKIE_KEY",1);
define ("CREATE_COOKIES",2);
//SQLs
define ("SQL_USER_ALL_FIELDS","SELECT user_id, user_login, user_pass, user_email, user_ip, user_url, user_level, UNIX_TIMESTAMP(user_validation_date) AS user_validation_date, UNIX_TIMESTAMP(user_creation_date) AS user_creation_date, UNIX_TIMESTAMP(user_modification_date) AS user_modification_date, user_trust, user_admin_text FROM users");

class UserManager{
	/**
	 * Recupera un usuario de la BD por su id
	 *
	 * @param $id id del usuario
	 * @return Usuario
	 */
	public static function get_user($id=0){
		global $db;
		$user = null;
		if ($id>0) $cond = "user_id = $id";
		if (!empty($cond) && ($dbuser = $db->get_row(SQL_USER_ALL_FIELDS." WHERE ".$cond." LIMIT 1"))) {
			$user = new User();
			$user->load($dbuser);
		}
		return $user;
	}

	/**
	 * Recupera un usuario de la BD por su id. Se recupera la información básica del mismo.
	 *
	 * @param int $id Id del usuario
	 * @return Usuario
	 */
	public static function get_user_width_summary_info($id){
		global $db;
		$user = null;

		$sql = "SELECT user_id, user_login FROM users WHERE user_id=$id LIMIT 1";
		if ($dbuser = $db->get_row($sql)) {
			$user = new User();
			$user->load($dbuser);
		}
		return $user;
	}

	/**
	 * Recupera un usuario de la BD por su login
	 *
	 * @param $login login del usuario
	 * @return Usuario
	 */
	public static function get_user_by_login($login){
		global $db;
		$user = null;
		if (!empty($login)) $cond = "user_login='".self::get_login_escaped($login)."'";
		if (!empty($cond) && ($dbuser = $db->get_row(SQL_USER_ALL_FIELDS." WHERE ".$cond." limit 1"))) {
			$user = new User();
			$user->load($dbuser);
		}
		return $user;
	}

	/**
	 * Recupera un usuario de la BD por su email
	 *
	 * @param $email Email del usuario
	 * @return Usuario
	 */
	public static function get_user_by_email($email){
		global $db;
		$user = null;
		if (!empty($email)) $cond = "user_email='".self::get_email_escaped($email)."' AND user_level != '".LEVEL_DISABLED."' AND user_level != '".LEVEL_BANNED."'";
		if (!empty($cond) && ($dbuser = $db->get_row(SQL_USER_ALL_FIELDS." WHERE ".$cond." limit 1"))) {
			$user = new User();
			$user->load($dbuser);
		}
		return $user;
	}

	/**
	 * Recupera el usuario que se autenticó en la aplicación
	 *
	 */
	public static function get_logged_user(){
		global $db, $now;

		//Recuperamos la información del usuario de las cookies
		if (!empty($_COOKIE[COOKIE_USER]) && !empty($_COOKIE[COOKIE_KEY])){
			$userInfo=explode(":", base64_decode($_COOKIE[COOKIE_KEY]));

			if ($_COOKIE[COOKIE_USER] === $userInfo[LOGIN_POS]) {	//Coincide el login de las 2 cookies
				$cookietime = (int) $userInfo[TIME_POS];
				$dbuserlogin = $db->escape($userInfo[LOGIN_POS]);

				//Consultamos el usuario y lo comprobamos
				$dbuser=$db->get_row(SQL_USER_ALL_FIELDS." WHERE user_login = '$dbuserlogin'");
				if (!$dbuser || !$dbuser->user_id > 0 || $dbuser->user_level == LEVEL_DISABLED || $dbuser->user_level == LEVEL_BANNED) {
					self::logout();
				}else{
					//Calculamos la clave de la cookie
					$key = self::calculate_cookie_key($userInfo, $dbuser, $cookietime);
					//Comprobamos la clave
					if ($key !== $userInfo[KEY_POS]){
						self::logout();
					}else{
						//Construimos el usuario
						$user = new User();
						$user->load($dbuser);
						//Actualizamos la cookie de clave cada hora
						if ($now - $cookietime > 3600){
							$remember = $userInfo[REMEMBER_POS] > 0 ? true : false;
							self::update_cookie(UPDATE_COOKIE_KEY, $user, $remember);
						}
						return $user;
					}
				}
			}
		}
	}

	/**
	 * Comprueba que la información de login introducida es correcta
	 *
	 * @param String $login Login del usuario
	 * @param String $password Contraseña del usuario
	 * @param boolean $remember Indica si se "recuerda" el usuario en el PC donde ha hecho login
	 * @return unknown
	 */
	public static function login($login, $password, $remember=false){
		global $db;
		$dblogin=$db->escape($login);
		$dbuser=$db->get_row(SQL_USER_ALL_FIELDS." WHERE user_login = '$dblogin'");
		//Comprobamos que es un usuario validado y que no se ha dado de baja o se ha baneado
		if ($dbuser && $dbuser->user_level != LEVEL_DISABLED && $dbuser->user_level != LEVEL_BANNED && !empty($dbuser->user_validation_date)){
			if (strlen($password) != 32)
				$password = encode_password($password);

			if ($dbuser->user_id > 0 && $dbuser->user_pass == $password) {
				$user = new User();
				$user->load($dbuser);
				self::update_cookie(CREATE_COOKIES, $user, $remember);
				return true;
			}
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $url
	 */
	public static function logout($url='./') {
		global $now;
		self::update_cookie(DELETE_COOKIES,null,false);
		//header("Pragma: no-cache");
		header("Cache-Control: no-cache, must-revalidate");
		header("Location: $url");
		header("Expires: " . gmdate("r", $now - 3600));
		header('ETag: "logingout' . $now . '"');
		die;
	}

	/**
	 * Crea un nuevo usuario, y envia el correo para la activación de la cuenta
	 *
	 * @param String $login Login del usuario
	 * @param String $password Contraseña del usuario
	 * @param String $email Email del usuario
	 * @return unknown
	 */
	public static function register($login, $password, $email) {
		global $db, $user_ip;
		//Insertamos los datos del nuevo usuario
		$user = new User();
		$user->login = $login;
		$user->password = encode_password($password);
		$user->email = $email;
		$user->ip = $user_ip;
		if ($user->store()){
			//Enviamos el mensaje de validación de usuario
			if (self::send_validation_email($user)){
				Log::new_user($user->id, $user->id);
				return $user->id;
			}else{
				//error_log("Error al enviar el mensaje de validación al email:" + $email);
			}
		}
		return false;
	}

	public static function recover($user) {
		global $user_ip_int;
		if (self::send_recover_email($user)){
			Log::recover_pass($user_ip_int,$user->id);
			return true;
		}
		return false;
	}


	public static function user_exists($dblogin, $dbpassword, $dbemail, $dbip){
		global $db;
		$dblogin = self::get_login_escaped($dblogin);
		$res=$db->get_var("SELECT user_id FROM users WHERE user_login='$dblogin' AND user_pass='$dbpassword' AND user_email='$dbemail' AND user_ip='$dbip' LIMIT 1");
		return $res;
	}

	/**
	 * Comprueba si un login de usuario ya está usado.
	 * Si se recibe un 2º parámetro con un id de usuario, se debe excluir ese usuario de la comprobación.
	 *
	 * @param  $user_login login del usuario a comprobar
	 * 				 $user_id Id de usuario a excluir en la búsqueda
	 * @return true si existe, false en caso contrario
	 */
	public static function login_exists($user_login, $user_id=0){
		global $db;
		$user_login = self::get_login_escaped($user_login);
		if ($user_id)
			$res=$db->get_var("SELECT count(*) FROM users WHERE user_login='$user_login' AND user_id<>$user_id LIMIT 1");
		else
			$res=$db->get_var("SELECT count(*) FROM users WHERE user_login='$user_login' LIMIT 1");
		if ($res>0) return true;
		return false;
	}

	/**
	 * Comprueba si un email de usuario ya está usado.
	 * Si se recibe un 2º parámetro con un id de usuario, se debe excluir ese usuario de la comprobación.
	 *
	 * @param  $email email del usuario
	 * 				 $user_id Id de usuario a excluir en la búsqueda
	 * @return true si existe, false en caso contrario
	 */
	public static function email_exists($email, $user_id=0) {
		global $db;

		$parts = explode('@', $email);
		$domain = $parts[1];
		$subparts = explode('+', $parts[0]); // Because we allow user+extension@gmail.com
		$user = $subparts[0];
		$user = $db->escape($user);
		$domain = $db->escape($domain);
		//Construimos la consulta
		$sql = "SELECT count(*) FROM users WHERE (user_email = '$user@$domain' or user_email LIKE '$user+%@$domain')";
		if ($user_id)
			$sql.=" AND user_id<>$user_id";
		$sql.=' LIMIT 1';
		$res=$db->get_var($sql);
		if ($res>0) return true;
		return false;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	/**
	 * Construye y envia el mensaje de validación de una nueva cuenta de usuario
	 *
	 * @param User $user Usuario al que enviar el correo
	 * @return true si se ha enviado el correo, false en caso contrario
	 */
	private static function send_validation_email($user){
		global $now, $settings;
		//Construimos la clave para verificar el mensaje
		$key = get_validation_user_key($user->id, $user->password, $now);
		$server_name =  get_server_name();
		$url = 'http://'.$server_name.$settings['BASE_URL'].'user_validate.php?o=validate&l='.$user->login.'&t='.$now.'&k='.$key;
		//Construimos el mensaje
		$subject = 'Bienvenido a deTapeo';
		$message = "Bienvenido a deTapeo\n";
		$message .= 'Para poder activar tu cuenta de usuario debes acceder a la siguiente dirección:';
		$message .= "\n\n$url\n\n";
		$message .= "un saludo\n";
		$message .= 'El equipo de deTapeo';
		return Mail::send($user->email,$subject,$message);
	}

	/**
	 * Construye y envia el mensaje para poder recuperar la contraseña
	 *
	 * @param User $user usuario al que enviar el correo de recuperación de contraseña
	 * @return true si se ha enviado el correo, false en caso contrario
	 */
	private static function send_recover_email($user){
		global $now, $settings;
		//Construimos la clave para verificar el mensaje
		$key = get_validation_user_key($user->id, $user->password, $now);
		$server_name =  get_server_name();
		$url = 'http://'.$server_name.$settings['BASE_URL'].'user_validate.php?o=recover&l='.$user->login.'&t='.$now.'&k='.$key;
		//Construimos el mensaje
		$subject = 'deTapeo::Recordar contraseña';
		$message = "Hola $user->login \nPara que puedas modificar tu contraseña de usuario debes acceder a la siguiente dirección:";
		$message .= "\n\n$url\n\n";
		$message .= "un saludo\n";
		$message .= 'El equipo de deTapeo';
		return Mail::send($user->email,$subject,$message);
	}

	private static function calculate_cookie_key($userInfo, $dbuser, $cookietime){
		global $now, $settings;
		switch ($userInfo[VERSION_POS]) {
			case '1':
			default:
				if (($now - $cookietime) > $settings['COOKIE_MAX_TIME']) $cookietime = 'expired';
				$key = md5($dbuser->user_email.$settings['SITE_KEY'].$dbuser->user_login.$dbuser->user_id.$cookietime);
				return $key;
		}
	}

	private static function update_cookie($op, $user, $remember){
		global $now, $settings;
		switch ($op) {
			case DELETE_COOKIES:	// Borra cookie, logout
				setcookie (COOKIE_USER, '', $now - 3600, $settings['BASE_URL']); // Expiramos el cookie
				setcookie (COOKIE_KEY, '', $now - 3600, $settings['BASE_URL']); // Expiramos el cookie
				break;

			case CREATE_COOKIES: // Crea las cookies
				if ($remember) $time = $now + 3600000; 	//Valido para 1000 horas
				else $time = 0;
				setcookie(COOKIE_USER, $user->login, $time, $settings['BASE_URL']);

			case UPDATE_COOKIE_KEY: // Actualizamos la cookie clave
				if (!isset($time)){
					if ($remember) $time = $now + 3600000; // Valid for 1000 hours
					else $time = 0;
				}
				//Contruimos la cookie clave
				$strCookie=base64_encode(
						$user->login.':'
						.md5($user->email.$settings['SITE_KEY'].$user->login.$user->id.$now).':'
						.$settings['COOKIE_KEY_VERSION'].':'
						.$now.':'
						.$time);
				setcookie(COOKIE_KEY, $strCookie, $time, $settings['BASE_URL'].'; HttpOnly');
				break;
		}
	}

	private static function get_login_escaped ($login){
		global $db;
		return mb_substr($db->escape($login),0,32);
	}

	private static function get_email_escaped ($email){
		global $db;
		return mb_substr($db->escape($email),0,64);
	}
}
?>
