<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Bar {
	var $id = 0;
	var $randkey = 0;
	var $name = '';
	var $text = '';
	var $image_id = 0;
	var $image_name = '';
	var $street_type = '';
	var $street_name = '';
	var $street_number = 1;
	var $town_id = 0;
	var $town_name = '';
	var $province = '';
	var $zone_id = 0;
	var $zone_name = '';
	var $postal_code= '';
	var $phone= '';
	var $map_lat= '';
	var $map_lng= '';
	var $web_url= '';
	var $beer_price = 0.00;
	var $specialities = '';
	var $author_id = 0;
	var $author_ip= '';
	var $last_author_id = 0;
	var	$last_author_ip = '';
	var $editor_id = 0;
	var $editor_ip= '';
	var $user_login = '';
	var $status = '';
	var $comments_closed = false;
	var $future_status = '';
	var $publication_date = 0;
	var $edition_date = 0;
	var $creation_date = 0;
	var $modification_date = 0;

	var $num_votes = 0;
	var $votes_avg = 0;
	var $real_votes_avg = 0;
	var $num_comments = 0;
	var $negative_votes = 0;


	/**
	 * Contructor
	 */
	public function __construct() {

	}

	/**
	 * Carga la información recibida en las propiedades del objeto
	 * @param $dbbar Objeto con la información del bar
	 */
	function load($dbbar) {
		$this->id = $dbbar->bar_id;
		if (isset($dbbar->bar_name))
			$this->name = $dbbar->bar_name;
		if (isset($dbbar->bar_text))
			$this->text = $dbbar->bar_text;
		if (isset($dbbar->bar_image_id))
			$this->image_id = $dbbar->bar_image_id;
		if (isset($dbbar->photo_small_image_name))
			$this->image_name = $dbbar->photo_small_image_name;
		if (isset($dbbar->bar_street_type))
			$this->street_type = $dbbar->bar_street_type;
		if (isset($dbbar->bar_street_name))
			$this->street_name = $dbbar->bar_street_name;
		if (isset($dbbar->bar_street_number))
			$this->street_number = $dbbar->bar_street_number;
		if (isset($dbbar->town_province))
			$this->province = $dbbar->town_province;
		if (isset($dbbar->bar_town_id))
			$this->town_id = $dbbar->bar_town_id;
		if (isset($dbbar->town_name))
			$this->town_name = $dbbar->town_name;
		if (isset($dbbar->bar_zone_id))
			$this->zone_id = $dbbar->bar_zone_id;
		if (isset($dbbar->zone_name))
			$this->zone_name = $dbbar->zone_name;
		if (isset($dbbar->bar_postal_code))
			$this->postal_code = $dbbar->bar_postal_code;
		if (isset($dbbar->bar_phone))
			$this->phone = $dbbar->bar_phone;
		if (isset($dbbar->bar_map_lat))
			$this->map_lat = $dbbar->bar_map_lat;
		if (isset($dbbar->bar_map_lng))
			$this->map_lng = $dbbar->bar_map_lng;
		if (isset($dbbar->bar_web_url))
			$this->web_url = $dbbar->bar_web_url;
		if (isset($dbbar->bar_beer_price))
			$this->beer_price = $dbbar->bar_beer_price;
		if (isset($dbbar->bar_author_id))
			$this->author_id = $dbbar->bar_author_id;
		if (isset($dbbar->bar_author_ip))
			$this->author_ip = $dbbar->bar_author_ip;
		if (isset($dbbar->bar_last_author_id))
			$this->last_author_id = $dbbar->bar_last_author_id;
		if (isset($dbbar->bar_last_author_ip))
			$this->last_author_ip = $dbbar->bar_last_author_ip;
		if (isset($dbbar->bar_editor_id))
			$this->editor_id = $dbbar->bar_editor_id;
		if (isset($dbbar->bar_editor_ip))
			$this->editor_ip = $dbbar->bar_editor_ip;
		if (isset($dbbar->user_login))
			$this->user_login = $dbbar->user_login;
		if (isset($dbbar->bar_status))
			$this->status = $dbbar->bar_status;
		if (isset($dbbar->bar_probably_future_status))
			$this->future_status = $dbbar->bar_probably_future_status;
		if (isset($dbbar->bar_comments_closed))
			$this->comments_closed = $dbbar->bar_comments_closed;
		if (isset($dbbar->bar_num_votes))
			$this->num_votes = $dbbar->bar_num_votes;
		if (isset($dbbar->bar_votes_avg))
			$this->votes_avg = $dbbar->bar_votes_avg;
		if (isset($dbbar->bar_votes_real_avg))
			$this->real_votes_avg = $dbbar->bar_votes_real_avg;
		if (isset($dbbar->bar_num_comments))
			$this->num_comments = $dbbar->bar_num_comments;
		if (isset($dbbar->bar_negatives_votes))
			$this->negative_votes = $dbbar->bar_negatives_votes;
		if (isset($dbbar->bar_publication_date))
			$this->publication_date = $dbbar->bar_publication_date;
		if (isset($dbbar->bar_edition_date))
			$this->edition_date = $dbbar->bar_edition_date;
		if (isset($dbbar->bar_creation_date))
			$this->creation_date = $dbbar->bar_creation_date;
		if (isset($dbbar->bar_modification_date))
			$this->modification_date = $dbbar->bar_modification_date;
	}

	/**
	 * Guarda la información del Bar en la BD
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store() {
		global $db, $current_user, $user_ip_int, $now, $settings;

		$bar_name = $db->escape($this->name);
		$bar_text = $db->escape($this->text);
		$bar_randkey = $this->randkey;
		$bar_image_id = $this->image_id;
		$bar_street_type = $this->street_type;
		$bar_street_name = $db->escape($this->street_name);
		$bar_street_number = $this->street_number;
		if (empty($bar_street_number))
			$bar_street_number = 1;
		$bar_town_id = $this->town_id;
		if (empty($bar_town_id))
			$bar_town_id = 0;
		$bar_zone_id = $this->zone_id;
		if (empty($bar_zone_id))
			$bar_zone_id = 0;
		$bar_postal_code = $this->postal_code;
		$bar_phone = $this->phone;
		$bar_map_lat = $this->map_lat;
		$bar_map_lng = $this->map_lng;
		$bar_web_url = $this->web_url;
		$bar_beer_price = $this->beer_price;
		if (empty($bar_beer_price))
			$bar_beer_price = 0.00;
		$bar_author_id = $this->author_id;
		$bar_author_ip = $this->author_ip;
		$bar_last_author_id = $this->last_author_id;
		$bar_last_author_ip = $this->last_author_ip;
		$bar_editor_id = $this->editor_id;
		$bar_editor_ip = $this->editor_ip;
		$bar_status = $this->status;
		$bar_comments_closed = $this->comments_closed?1:0;
		$bar_edition_date = $this->edition_date;

		if ($this->id===0) {
			if ($db->query("INSERT INTO bars (bar_name, bar_text, bar_randkey, bar_image_id, bar_street_type, bar_street_name, bar_street_number, bar_town_id, bar_zone_id, bar_postal_code, bar_phone, bar_map_lat, bar_map_lng, bar_web_url, bar_beer_price, bar_author_id, bar_author_ip, bar_status, bar_creation_date, bar_edition_date) VALUES('$bar_name', '$bar_text', $bar_randkey, $bar_image_id, '$bar_street_type', '$bar_street_name', $bar_street_number, $bar_town_id, $bar_zone_id, '$bar_postal_code', '$bar_phone', '$bar_map_lat', '$bar_map_lng', '$bar_web_url', $bar_beer_price, $bar_author_id, '$bar_author_ip','$bar_status', FROM_UNIXTIME($now), FROM_UNIXTIME($now))")){
				$this->id = $db->insert_id;
				Log::new_bar($this->id, $current_user->id);
				if ($settings['MAIL_ADVICE_NEW_BAR'])
					$this->send_advice_email(true);
				return true;
			}

		} else {
			$sql_values = 'bar_comments_closed='.$bar_comments_closed;
			if (!empty($bar_name))	$sql_values.= ", bar_name='$bar_name'";
			if (!empty($bar_text))	$sql_values.= ", bar_text='$bar_text'";
			if (!empty($bar_image_id)) 		$sql_values.= ", bar_image_id=$bar_image_id";
			if (!empty($bar_street_type))	$sql_values.= ", bar_street_type='$bar_street_type'";
			if (!empty($bar_street_name))	$sql_values.= ", bar_street_name='$bar_street_name'";
			if (!empty($bar_street_number))	$sql_values.= ", bar_street_number=$bar_street_number";
			if (!empty($bar_town_id))	$sql_values.= ", bar_town_id=$bar_town_id";
			if (!empty($bar_zone_id))	$sql_values.= ", bar_zone_id=$bar_zone_id";
			if (!empty($bar_postal_code))	$sql_values.= ", bar_postal_code='$bar_postal_code'";
			if (!empty($bar_phone))	$sql_values.= ", bar_phone='$bar_phone'";
			if (!empty($bar_map_lat))	$sql_values.= ", bar_map_lat='$bar_map_lat'";
			if (!empty($bar_map_lng))	$sql_values.= ", bar_map_lng='$bar_map_lng'";
			if (!empty($bar_web_url))	$sql_values.= ", bar_web_url='$bar_web_url'";
			if (!empty($bar_beer_price))	$sql_values.= ", bar_beer_price=$bar_beer_price";
			if (!empty($bar_last_author_id))	$sql_values.= ", bar_last_author_id=$bar_last_author_id";
			if (!empty($bar_last_author_ip))	$sql_values.= ", bar_last_author_ip='$bar_last_author_ip'";
			if (!empty($bar_editor_id))	$sql_values.= ", bar_editor_id=$bar_editor_id";
			if (!empty($bar_editor_ip))	$sql_values.= ", bar_editor_ip='$bar_editor_ip'";
			if (!$this->is_published() && !empty($bar_status))	$sql_values.= ", bar_status='$bar_status'";
			if (!empty($bar_edition_date))	$sql_values.= ", bar_edition_date = FROM_UNIXTIME($bar_edition_date)";

			$sql = "UPDATE bars SET ".$sql_values." WHERE bar_id=$this->id ".BarManager::get_sql_cond_edition_rights();
			if ($db->query($sql)){
				Log::edit_bar($this->id, $current_user->id);
				if ($this->is_published()) $this->publish();
				if ($settings['MAIL_ADVICE_EDIT_BAR'])
					$this->send_advice_email(false);
			}
			return true;
		}
		return false;
	}

	/**
	 * Guarda la información relativa a la posición en el mapa del bar
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store_map_info(){
		global $db, $current_user, $now;

		$bar_map_lat = $this->map_lat;
		$bar_map_lng = $this->map_lng;

		$sql = "UPDATE bars SET bar_map_lat='$bar_map_lat', bar_map_lng='$bar_map_lng', bar_edition_date = FROM_UNIXTIME($now) WHERE bar_id=$this->id ".BarManager::get_sql_cond_edition_rights();
		if ($db->query($sql)){
			Log::edit_bar($this->id, $current_user->id);
			return true;
		}
		return false;
	}

	/**
	 * Modifica el estado de un Bar a "En cola"
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function queued(){
		if (!$this->is_queued()){
			$this->status = STATUS_QUEUED;
			return $this->store_status(true);
		}else{
			return true;
		}
	}

	/**
	 * Modifica el estado de un bar a publicado
	 *
	 */
	function publish(){
		global $db, $now, $current_user;
		$old_status = BarManager::get_status($this);
		if ($old_status != STATUS_PUBLISHED){
			$sql = "UPDATE bars SET bar_status='".STATUS_PUBLISHED."', bar_publication_date = FROM_UNIXTIME($now) WHERE bar_id=$this->id";
			if ($db->query($sql)){
				Log::publish_bar($this->id, $current_user->id);
				//Comprobamos si estaba descartado
				if ($old_status==STATUS_DUPLICATED || $old_status==STATUS_NO_EXISTS || $old_status==STATUS_NO_TAPA_BAR || $old_status==STATUS_OBSOLETE){
					//Reiniciamos los votos negativos a 0
					$sql = "UPDATE votes SET vote_value=0 WHERE vote_value<0 AND vote_bar_id=$this->id";
					$db->query($sql);
					//Eliminamos la información de los votos negativos
					$sql = "UPDATE bars SET bar_probably_future_status=NULL, bar_negatives_votes=0 WHERE bar_id=$this->id";
					$db->query($sql);
				}
			}
		}
	}

	/**
	 * Actualiza el estado del bar
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store_status($change_edition_date=false){
		global $db, $current_user, $now;

		$bar_status = $this->status;
		$sql="UPDATE bars SET bar_status='$bar_status'";
		if ($change_edition_date)
			$sql.=", bar_edition_date = FROM_UNIXTIME($now)";
		$sql.=" WHERE bar_id=$this->id";
		if ($db->query($sql)){
			return true;
		}
		return false;
	}

	/**
	 * Modifica la caratula de un Bar
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store_cover_image(){
		global $db, $current_user;

		$bar_image_id = $this->image_id;
		$sql="UPDATE bars SET bar_image_id=$bar_image_id WHERE bar_id=$this->id ".BarManager::get_sql_cond_edition_rights();
		if ($db->query($sql)){
			return true;
		}
		return false;
	}

	/**
	 * Comprueba que el usuario recibido por parámetro es autor del Bar
	 *
	 * @param Usuario $user
	 * @return true si es autor, false en caso contrario
	 */
	function is_author($user){
		if ($this->author_id===$user->id || $this->last_author_id===$user->id)
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si el estado del Bar es "información obsoleta"
	 *
	 * @return true si la información es obsoleta, false en caso contrario
	 */
	function is_info_obsolete(){
		return ($this->status==STATUS_OBSOLETE);
	}

	/**
	 * Comprueba si el estado del Bar es "Publicado"
	 *
	 * @return true si está publicado, false en caso contrario
	 */
	function is_published(){
		return ($this->status==STATUS_PUBLISHED);
	}

	/**
	 * Comprueba si el Bar está pendiende de publicación
	 *
	 * @return true si está en cola, false en caso contrario
	 */
	function is_queued(){
		return ($this->status==STATUS_QUEUED);
	}

	/**
	 * Comprueba si el Bar no se ha publicado nunca
	 *
	 * @return true si no se ha publicado nunca, false en caso contrario
	 */
	function is_never_published(){
		return (!$this->publication_date>0);
	}

	/**
	 * Comprueba si se ha marcado como descartable
	 *
	 * @return true si es descartable, false en caso contrario
	 */
	function is_future_discarded(){
		return (!empty($this->future_status));
	}

	/**
	 * Comprueba si el usuario puede editar este bar
	 *
	 * @return true si puede editarlo, false en caso contrario
	 */
	function is_editable(){
		global $current_user;
		if ($current_user && ($current_user->is_editor() || $this->is_info_obsolete() || ($this->is_author($current_user) && ($this->is_published() || $this->is_queued()))))
			return true;
		else
			return false;
	}

	/**
	 * Comprueba si los comentarios estan cerrados
	 *
	 * @return true si están cerrados, false en caso contrario
	 */
	function is_comments_closed(){
		if ($this->comments_closed)
			return true;
		else
			return false;
	}

	/**
	 * Guarda el voto del usuario actual a este Bar
	 *
	 * @param int $value Valor del voto
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function vote($value){
		global $db, $current_user, $user_ip_int, $settings;

		if ($current_user && is_numeric($value) && $value<=10){
			//Guardamos el voto
			VoteManager::vote($current_user->id, $user_ip_int, $this->id, $value);
			//Si se ha votado negativo o si está marcado como descartado futurible
			//comprobamos si hay que cambiarle el estado
			if ($value<0 || $this->is_future_discarded()){
				$dbnegvotes = VoteManager::get_negative_votes_for_last_months($this->id);
				$dbposvotes = VoteManager::get_positive_votes_count_for_last_months($this->id);
				$this->negative_votes = 0;
				foreach ($dbnegvotes as $dbnegvote){
					$this->negative_votes += $dbnegvote->num;
					$dbnegvote->num -= $dbposvotes;	//le restamos los positivos
					if ($dbnegvote->num >= $settings['MAX_NEGATIVE_VOTES_TO_CHANGE_FUTURE_STATUS']){
						$this->future_status = get_bar_status_from_vote_value($dbnegvote->vote_value);
						$bar_future_status = $this->future_status;
					}
					if ($dbnegvote->num >= $settings['MAX_NEGATIVE_VOTES_TO_CHANGE_STATUS']){
						$this->status = get_bar_status_from_vote_value($dbnegvote->vote_value);
						$bar_status = $this->status;
					}
				}
				$bar_negative_votes = $this->negative_votes;
				//Si el bar estaba marcado como descartable, pero ha pasado a no descartable hay q actualizar el estado
				if ($this->is_future_discarded() && !isset($bar_future_status))
					$bar_future_status = '';
			}
			//Consultamos el nº de votos del bar, y las dos medias
			if ($dbvotes = VoteManager::get_positive_votes_count_avgs($this->id)){
				$this->num_votes = $dbvotes->num_votes;
				$this->real_votes_avg = $dbvotes->real_votes_avg;
				if (!$this->real_votes_avg) $this->real_votes_avg=0;
				$this->votes_avg = $dbvotes->votes_avg;
				if (!$this->votes_avg) $this->votes_avg=0;
				//Guardamos esta información
				$sql = "UPDATE bars SET bar_num_votes=$this->num_votes, bar_votes_avg=$this->votes_avg, bar_votes_real_avg=$this->real_votes_avg";
				if (isset($bar_negative_votes))
					$sql.=", bar_negatives_votes = $bar_negative_votes";
				if (isset($bar_future_status))
					$sql.=", bar_probably_future_status = '$bar_future_status'";
				if (isset($bar_status))
					$sql.=", bar_status = '$bar_status'";
				$sql.=" WHERE bar_id=$this->id";

				$db->query($sql);
				return true;
			}
		}
		return false;
	}

	/**
	 * Actualiza el nº de comentarios del Bar
	 *
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function update_comments_count(){
		global $db;
		$comments_count = CommentManager::get_bar_public_comments_count($this->id);
		$sql = "UPDATE bars SET bar_num_comments=$comments_count WHERE bar_id=$this->id";
		if ($db->query($sql))
			return true;
		else
			return false;
	}

	/**
	 * Encripta la clave aleatoria
	 *
	 * @return la clave aleatoria encriptada
	 */
	function key(){
		global $settings;
		return md5($this->randkey.$settings['SITE_KEY']);
	}

	function get_uri_name(){
		return get_uri($this->name);
	}

	function get_relative_permalink() {
		global $settings;
		if (!empty($settings['BASE_BAR_URL']))
			return $settings['BASE_URL'].$settings['BASE_BAR_URL'].$this->id.'/'.get_uri($this->town_name).'/'.get_uri($this->name);
		else
			return $settings['BASE_URL'].'bar.php?id='.$this->id;
	}

	function get_permalink(){
		return 'http://'.get_server_name().$this->get_relative_permalink();
	}

	/**
	 * Construye y envia un mensaje de aviso sobre si se ha añadido o modificado un bar
	 *
	 * @param $new_bar Indica si el mensaje es porque se ha añadido un bar o porque se ha modificado
	 */
	function send_advice_email($new_bar=true){
		global $settings;
		if (!empty($settings['NOTIFICATION_EMAIL'])){
			$subject = '';
			$message = '';
			//Construimos el mensaje
			if ($new_bar){
				$subject = 'deTapeo::Nuevo bar';
				$message = "Se ha añadido el Bar:\n";
			}else{
				$subject = 'deTapeo::Bar modificado';
				$message = "Se ha modificado la información del Bar:\n";
			}
			$message .= "       $this->name\n";
			$message .= '       '.get_address_in_human_format($this->street_type, $this->street_name, $this->street_number, $this->town_name, $this->province)."\n";
			$message .= '       '.$this->get_permalink();
			Mail::send($settings['NOTIFICATION_EMAIL'],$subject,$message);
		}
	}
}
