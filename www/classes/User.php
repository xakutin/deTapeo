<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class User {
	var $loaded = false;

	var $id = 0;
	var $login = '';
	var $password = '';
	var $email = '';
	var $ip = '';
	var $url = '';
	var $level = LEVEL_NORMAL;
	var $trust= 1;
	var $admin_text = '';
	var $creation_date;
	var $modification_date;
	var $validation_date;

	//Datos estadísticos
	var $total_votes = 0;
	var $total_pos_votes = 0;
	var $total_neg_votes = 0;
	var $total_bars = 0;
	var $total_published_bars = 0;
	var $total_comments = 0;

	public function __construct() {
	}

	/**
	 * Carga la información de un usuario de la BD
	 */
	public function load($dbuser) {
		$this->id =$dbuser->user_id;
		$this->login = $dbuser->user_login;
		if (isset($dbuser->user_pass))
			$this->password = $dbuser->user_pass;
		if (isset($dbuser->user_email))
			$this->email = $dbuser->user_email;
		if (isset($dbuser->user_ip))
			$this->ip = $dbuser->user_ip;
		if (isset($dbuser->user_url))
			$this->url = $dbuser->user_url;
		if (isset($dbuser->user_level))
			$this->level = $dbuser->user_level;
		if (isset($dbuser->user_trust))
			$this->trust = $dbuser->user_trust;
		if (isset($dbuser->user_admin_text))
			$this->admin_text = $dbuser->user_admin_text;
		if (isset($dbuser->user_validation_date))
			$this->validation_date = $dbuser->user_validation_date;
		if (isset($dbuser->user_creation_date))
			$this->creation_date = $dbuser->user_creation_date;
		if (isset($dbuser->user_modification_date))
			$this->modification_date = $dbuser->user_modification_date;
		$this->loaded = true;
	}

	/**
	 * Guarda la información de un usuario en la BD
	 */
	public function store() {
		global $db, $current_user;

		$user_login = $db->escape($this->login);
		$user_pass = $db->escape($this->password);
		$user_email = $db->escape($this->email);
		$user_ip = $this->ip;
		$user_url = $db->escape(htmlentities($this->url));
		$user_level = $this->level;
	  	$user_trust = $this->trust;
	  	$user_admin_text = $this->admin_text;

		if ($this->id===0) {
			if ($db->query("INSERT INTO users (user_login, user_pass, user_email, user_ip, user_url, user_level, user_trust, user_creation_date) VALUES('$user_login','$user_pass','$user_email','$user_ip','$user_url','$user_level',$user_trust,now())")){
				$this->id = $db->insert_id;
				return true;
			}
		} else {
			$sql_values = ''; $count = 0;
			if (!empty($user_login)){
				$sql_values.= "user_login='$user_login'";
				++$count;
			}
			if (!empty($user_pass)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_pass='$user_pass'";
				++$count;
			}
			if (!empty($user_email)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_email='$user_email'";
				++$count;
			}
			if (!empty($user_ip)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_ip='$user_ip'";
				++$count;
			}
			if (!empty($user_url)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_url='$user_url'";
				++$count;
			}
			if (!empty($user_level)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_level='$user_level'";
				++$count;
			}
			if (!empty($user_trust)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_trust='$user_trust'";
				++$count;
			}
			if (!empty($user_admin_text)){
				if ($count>0) $sql_values.=', ';
				$sql_values.= "user_admin_text='$user_admin_text'";
				++$count;
			}
			$db->query("UPDATE users SET ".$sql_values." WHERE user_id=$this->id");
			Log::edit_user($this->id, $current_user->id);
			return true;
		}
		return false;
	}

	/**
	 * Deshabilita un usuario
	 */
	public function disable() {
		global $db;

		$this->level = LEVEL_DISABLED;
		return $this->store();
	}

	/**
	 * Valida un usuario, si no se ha validado anteriormente
	 *
	 * @return true si no se produce ningún error, false en caso contrario
	 */
	public function validate(){
		global $db;
		$res = true;
		if (!$db->get_var("SELECT count(*) FROM users WHERE user_id = $this->id AND user_validation_date IS NOT NULL LIMIT 1"))
			$res = $db->query("UPDATE users SET user_validation_date = now() WHERE user_id = $this->id AND user_validation_date IS NULL");
			if ($res){ //Creamos el avatar del usuario (por si no se creó en el registro)
				create_user_avatar($this->id, $this->email);
			}
		return $res;
	}

	/**
	 * Elimina un usuario que no está activado
	 *
	 * @return true si no se produce ningún error, false en caso contrario
	 */
	public function delete(){
		global $db, $settings;
		//Eliminamos el usuario
		if ($db->query("DELETE FROM users WHERE user_id = $this->id AND user_validation_date IS NULL")){
			//Eliminamos los avatares del usuario
			$avatar_path = get_avatars_local_path($this->id);
			foreach ($settings['AVATAR_SIZES'] as $size){
				@unlink($avatar_path.$this->id.'-'.$size.'.jpg');
			}
			return true;
		}
		return false;
	}

	/**
	 * Consulta las estadísticas de un usuario
	 */
	public function load_stats() {
		global $db;
		if ($this->id){
			//Consultamos los datos estadisticos del usuario
			$this->total_bars = (int) $db->get_var("SELECT count(*) FROM bars WHERE bar_author_id = $this->id LIMIT 1");
			$this->total_published_bars = (int) $db->get_var('SELECT count(*) FROM bars WHERE bar_author_id = '.$this->id.' AND bar_status=\''.STATUS_PUBLISHED.'\' LIMIT 1');
			$this->total_votes = (int) $db->get_var("SELECT count(*) FROM votes WHERE vote_user_id = $this->id LIMIT 1");
			$this->total_pos_votes = (int) $db->get_var("SELECT count(*) FROM votes WHERE vote_user_id = $this->id AND vote_value>0 LIMIT 1");
			$this->total_neg_votes = (int) $db->get_var("SELECT count(*) FROM votes WHERE vote_user_id = $this->id AND vote_value<0 LIMIT 1");
			$this->total_comments = (int) $db->get_var("SELECT count(*) FROM comments WHERE comment_user_id = $this->id LIMIT 1");
		}
	}

	/**
	 * Comprueba si el usuario tiene permisos de administrador
	 *
	 * @return true si tiene permisos de administrador, false en caso contrario
	 */
	public function is_admin(){
		if ($this->level == LEVEL_ADMIN)
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si el usuario tiene permisos de editor
	 *
	 * @return true si tiene permisos de editor, false en caso contrario
	 */
	public function is_editor(){
		if ($this->level == LEVEL_ADMIN || $this->level == LEVEL_EDITOR)
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si el usuario está deshabilitado
	 *
	 * @return true si el usuario está deshabilitado, false en caso contrario
	 */
	public function is_disabled(){
		if ($this->level == LEVEL_DISABLED)
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si el usuario está baneado
	 *
	 * @return true si el usuario está baneado, false en caso contrario
	 */
	public function is_banned(){
		if ($this->level == LEVEL_BANNED)
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si el usuario no tiene permisos especiales, es decir es un usuario normal
	 *
	 * @return true si es normal, false en caso contrario
	 */
	public function is_normal(){
		if ($this->level == LEVEL_NORMAL)
			return true;
		else
			return false;
	}
}
?>
