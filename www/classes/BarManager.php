<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'Bar.php';
class BarManager{
	/**
	 * Recupera un Bar con toda su información
	 *
	 * @param int $id Id del Bar
	 * @return Bar con toda su información
	 */
	public static function get_bar($id=0){
		global $db;
		$bar = null;
		$all_field = 'bar_id, bar_name, bar_text, bar_image_id, bar_street_type, bar_street_name, bar_street_number, bar_town_id, bar_zone_id, bar_postal_code, bar_phone, bar_map_lat, bar_map_lng, bar_web_url, bar_beer_price, bar_author_id, bar_author_ip, bar_last_author_id, bar_last_author_ip, bar_editor_id, bar_editor_ip, bar_status, bar_comments_closed, bar_probably_future_status, bar_num_votes, bar_votes_avg, bar_votes_real_avg, bar_num_comments, bar_negatives_votes, UNIX_TIMESTAMP(bar_publication_date) AS bar_publication_date, bar_randkey, UNIX_TIMESTAMP(bar_creation_date) AS bar_creation_date, UNIX_TIMESTAMP(bar_edition_date) AS bar_edition_date, town_name, town_province, zone_name';
		$sql = "SELECT $all_field FROM bars, towns, zones WHERE bar_id=$id AND bar_town_id=town_id AND bar_zone_id=zone_id LIMIT 1";
		if ($dbbar = $db->get_row($sql)){
			$bar = new Bar();
			$bar->load($dbbar);
		}
		return $bar;
	}

	/**
	 * Recupera un bar con su información de resumen
	 * Si se recibe un usuario por parámetro las medias de los votos serán el voto que ha dado ese usuario.
	 *
	 * @param int $id Id del bar
	 * @param User $user usuario
	 * @return Bar con su información de resumen
	 */
	public static function get_bar_with_summary_info($id=0, $user=null){
		global $db;
		$bar = null;
		if ($user)		//Si se ha recibido un usuario las medias serán el voto del usuario
			$sql = "SELECT bar_id, bar_name, bar_text, bar_image_id, bar_street_type, bar_street_name, bar_street_number, bar_town_id, bar_zone_id, bar_beer_price, bar_author_id, bar_last_author_id, bar_status, bar_probably_future_status, bar_num_votes, vote_value AS bar_votes_avg, vote_value AS bar_votes_real_avg, bar_num_comments, bar_negatives_votes, UNIX_TIMESTAMP(bar_publication_date) AS bar_publication_date, UNIX_TIMESTAMP(bar_creation_date) AS bar_creation_date, UNIX_TIMESTAMP(bar_edition_date) AS bar_edition_date, town_name, town_province, zone_name FROM bars, towns, zones, votes WHERE bar_id=$id AND bar_town_id=town_id AND bar_zone_id=zone_id AND vote_bar_id=$id AND vote_user_id=$user->id LIMIT 1";
		else
			$sql = "SELECT bar_id, bar_name, bar_text, bar_image_id, bar_street_type, bar_street_name, bar_street_number, bar_town_id, bar_zone_id, bar_beer_price, bar_author_id, bar_last_author_id, bar_status, bar_probably_future_status, bar_num_votes, bar_votes_avg, bar_votes_real_avg, bar_num_comments, bar_negatives_votes, UNIX_TIMESTAMP(bar_publication_date) AS bar_publication_date, UNIX_TIMESTAMP(bar_creation_date) AS bar_creation_date, UNIX_TIMESTAMP(bar_edition_date) AS bar_edition_date, town_name, town_province, zone_name FROM bars, towns, zones WHERE bar_id=$id AND bar_town_id=town_id AND bar_zone_id=zone_id LIMIT 1";
		if ($dbbar = $db->get_row($sql)){
			$bar = new Bar();
			$bar->load($dbbar);
		}
		return $bar;
	}

	/**
	 * Devuelve un Bar con los metadatos que se pueden modificar
	 *
	 * @param $id id del Bar
	 * @return Bar con los metadatos que se pueden modificar
	 */
	public static function get_bar_with_editable_data($id=0){
		global $db;
		$bar=null;

		if ($dbbar = $db->get_row("SELECT bar_id, bar_name, bar_text, bar_street_type, bar_street_name, bar_street_number, town_province, bar_town_id, bar_zone_id, bar_postal_code, bar_phone, bar_web_url, bar_beer_price, bar_status, bar_comments_closed, bar_author_id, bar_last_author_id, UNIX_TIMESTAMP(bar_publication_date) AS bar_publication_date FROM bars, towns WHERE bar_id=$id AND bar_town_id=town_id ". self::get_sql_cond_edition_rights())){
			$bar = new Bar();
			$bar->load($dbbar);
		}
		return $bar;
	}

	/**
	 * Devuelve un Bar con la información de su dirección
	 *
	 * @param $id id del Bar
	 * @return Bar con la dirección del mismo
	 */
	public static function get_bar_with_address_info($id=0){
		global $db;
		$bar=null;

		if ($dbbar = $db->get_row("SELECT bar_id, bar_randkey, bar_name, bar_map_lat, bar_map_lng, bar_street_type, bar_street_name, bar_street_number, town_name, town_province, bar_status, bar_author_id, bar_last_author_id FROM bars, towns WHERE bar_id=$id AND bar_town_id = town_id".self::get_sql_cond_edition_rights())){
			$bar = new Bar();
			$bar->load($dbbar);
		}
		return $bar;
	}

	/**
	 * Devuelve un Bar con la información de su imagen de portada
	 *
	 * @param $id Id del bar
	 * @return Bar con la información de su imagen de portada
	 */
	public static function get_bar_with_cover_data($id=0){
		global $db;
		$bar=null;

		if ($dbbar = $db->get_row("SELECT bar_id, bar_name, bar_image_id, bar_status, bar_author_id, bar_last_author_id FROM bars WHERE bar_id=$id ".self::get_sql_cond_edition_rights())){
			$bar = new Bar();
			$bar->load($dbbar);
		}
		return $bar;
	}

	/**
	 * Busca una lista de bares cuyo nombre sea similar al del bar que se pasa por parámetro
	 *
	 * @param $bar Bar con el nombre a buscar
	 * @return lista de bares o null si no se encuentra ninguno
	 */
	public static function get_possible_duplicated_bars($bar){
		global $db;
		//Eliminamos los prefijos típicos para intentar sacar el verdadero nombre del bar
		$bar_name = preg_replace('/bar|restaurante|rte|rte\.|meson|mesón|cafeteria|cafetería|cafe|café|tabernilla|taberna|cerveceria|cervecería|pulperia|pulpería|casa|los|las|el|la|del|de/', '', strtolower(stripslashes($bar->name)));
		//Creamos la condición
		$bar_chunks_name = split(' ',trim($bar_name));
		$sql_cond = '';
		foreach ($bar_chunks_name as $bar_chunk){
			if (!empty($bar_chunk))
				$sql_cond.=' AND bar_name LIKE "%'.clean_input_string($bar_chunk).'%"';
		}
		//Realizamos la consulta
		$sql="SELECT bar_id, bar_name, bar_status, bar_street_type, bar_street_name, bar_street_number, town_name, town_province FROM bars, towns WHERE bar_id<>$bar->id AND bar_town_id=$bar->town_id AND bar_town_id = town_id $sql_cond";
		return $db->get_results($sql);
	}

	/**
	 * Devuelve la condición SQL necesaria para controlar los permisos de edición sobre un Bar
	 *
	 * @return condición SQL necesaria para controlar los permisos de edición sobre un Bar
	 */
	public static function get_sql_cond_edition_rights(){
		global $current_user;
		$cond='';
		if ($current_user->is_normal()){
			$cond=" AND (bar_author_id=$current_user->id OR bar_last_author_id=$current_user->id OR bar_status='obsolete')";
		}
		return $cond;
	}

	public static function get_sql_cond_property_rights(){
		global $current_user;
		$cond='';
		if ($current_user->is_normal()){
			$cond=" AND (bar_author_id=$current_user->id OR bar_last_author_id=$current_user->id)";
		}
		return $cond;
	}

	/**
	 * Consulta el nº de bares dados de alta
	 *
	 * @return Nº de bares dados de alta
	 */
	public static function get_bars_count(){
		global $db;
		return $db->get_var('SELECT count(*) FROM bars');
	}

	/**
	 * Consulta un rango de Ids de bares
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares
	 */
	public static function get_bar_ids($current_page){
		global $db, $settings;
		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];
		$sql = "SELECT bar_id FROM bars ORDER BY bar_modification_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares publicados
	 *
	 * @return Nº de bares publicados
	 */
	public static function get_published_bars_count(){
		global $db;
		$sql = "SELECT count(*) FROM bars WHERE bar_status='published'";
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares publicados
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares publicados
	 */
	public static function get_published_bar_ids($current_page){
		global $db, $settings;
		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];
		$sql = "SELECT bar_id FROM bars WHERE bar_status='published' ORDER BY bar_publication_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Construye la condición de la query de búsqueda
	 *
	 * @param string $text Texto a buscar
	 * @param string $status Estado de los bares a buscar
	 * @return String con la condición de la query de búsqueda
	 */
	public static function get_sql_cond_search($text, $status){
		global $db;
		$sql_cond = '';
		if ($status)
			$sql_cond = "bar_status='$status' ";

		$text = clean_search_text($text);
		if ($text){
			if ($sql_cond)
				$sql_cond .= 'AND bar_name LIKE \'%'.$db->escape($text).'%\'';
			else
				$sql_cond .= 'bar_name LIKE \'%'.$db->escape($text).'%\'';
		}
		return $sql_cond;
	}

	/**
	 * Consulta el nº de bares cuyo nombre y estado coincide con el criterio de búsqueda
	 *
	 * @param string $text Texto a buscar
	 * @param string $status Estado de los bares a buscar
	 * @return nº de bares publicados cuyo nombre coincide con el texto a buscar
	 */
	public static function get_search_bars_count($text, $status = ''){
		global $db;
		$sql_cond = self::get_sql_cond_search($text, $status);
		$sql = 'SELECT count(*) FROM bars WHERE '.$sql_cond;
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares cuyo nombre y estado coincide con el criterio de búsqueda
	 *
	 * @param string $text Texto a buscar
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @param string $status Estado de los bares a buscar
	 * @return rango de Ids de bares publicados cuyo nombre coincide con el texto a buscar
	 */
	public static function get_search_bar_ids($text, $current_page, $status = ''){
		global $db, $settings;
		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];
		$sql_cond = self::get_sql_cond_search($text, $status);
		$sql = 'SELECT bar_id FROM bars WHERE '.$sql_cond." ORDER BY bar_publication_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares publicados con una valoración alta
	 *
	 * @return Nº de bares en el top
	 */
	public static function get_top_bars_count(){
		global $db, $settings;
		if ($settings["DEMOCRACY"])
			$top_cond = ' AND ROUND(bar_votes_real_avg)>'.TOP_VOTES_AVG;
		else
			$top_cond = ' AND ROUND(bar_votes_avg)>'.TOP_VOTES_AVG;

		$sql = "SELECT count(*) FROM bars WHERE bar_status='published' $top_cond";
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares del top
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares en el top
	 */
	public static function get_top_bar_ids($current_page){
		global $db, $settings;
		if ($settings["DEMOCRACY"])
			$top_cond = ' AND ROUND(bar_votes_real_avg)>'.TOP_VOTES_AVG;
		else
			$top_cond = ' AND ROUND(bar_votes_avg)>'.TOP_VOTES_AVG;
		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];
		$sql = "SELECT bar_id FROM bars WHERE bar_status='published' $top_cond ORDER BY bar_publication_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}
	/**
	 * Consulta el nº de bares no publicados. Si el parámetro $check_property es true se incluye la
	 * condición de los permisos de usuario autenticado
	 *
	 * @param boolean $check_property si su valor es true se debe comprobar los permisos del usuario autenticado
	 * @return nº de bares no publicados
	 */
	public static function get_not_published_bars_count($check_property=false){
		global $db;
		$cond_property = '';
		if ($check_property)
			$cond_property = self::get_sql_cond_property_rights();

		$sql = "SELECT count(*) FROM bars WHERE bar_status<>'published' $cond_property";
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares no publicados.
	 * Si el parámetro $check_property es true se debe comprobar los permisos del usuario autenticado.
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @param boolean $check_property si su valor es true se debe comprobar los permisos del usuario autenticado
	 * @return rango de Ids de bares no publicados
	 */
	public static function get_not_published_bar_ids($current_page, $check_property=false){
		global $db, $settings;
		$cond_property = '';

		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];

		if ($check_property)
			$cond_property = self::get_sql_cond_property_rights();

		$sql = "SELECT bar_id FROM bars WHERE bar_status<>'published' $cond_property ORDER BY bar_edition_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares en estado "pendiente".
	 *
	 * @return nº de bares en cola
	 */
	public static function get_queued_bars_count(){
		global $db;

		$sql = "SELECT count(*) FROM bars WHERE bar_status='queued' LIMIT 1";
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares en estado "pendiente".
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares pendientes
	 */
	public static function get_queued_bar_ids($current_page){
		global $db, $settings;

		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];

		$sql = "SELECT bar_id FROM bars WHERE bar_status='queued' ORDER BY bar_edition_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares descartados
	 *
	 * @return nº de bares descartados
	 */
	public static function get_discarded_bars_count($check_property=false){
		global $db;

		$sql = "SELECT count(*) FROM bars WHERE bar_status='obsolete' OR bar_status='duplicated' OR bar_status='no_tapa_bar' OR bar_status='no_exists' LIMIT 1";
		return $db->get_var($sql);
	}

	/**
	 * Consulta un rango de Ids de bares descartados.
	 *
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares descartados
	 */
	public static function get_discarded_bar_ids($current_page){
		global $db, $settings;
		$cond_property = '';

		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];

		$sql = "SELECT bar_id FROM bars WHERE bar_status='obsolete' OR bar_status='duplicated' OR bar_status='no_tapa_bar' OR bar_status='no_exists' ORDER BY bar_edition_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares enviados por un usuario
	 *
	 * @param User $user Usuario que ha enviado los bares
	 * @return nº de bares enviados por un usuario
	 */
	public static function get_user_sended_bars_count($user){
		global $db;
		if ($user){
			$sql = "SELECT count(*) FROM bars WHERE bar_author_id=$user->id OR bar_last_author_id=$user->id LIMIT 1";
			return $db->get_var($sql);

		}else{
			return 0;
		}
	}

	/**
	 * Consulta un rango de Ids de bares enviados por un usuario
	 *
	 * @param User $user Usuario que ha enviado los bares
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares enviados por un usuario
	 */
	public static function get_user_sended_bar_ids($user, $current_page){
		global $db, $settings;

		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];

		$sql = "SELECT bar_id FROM bars WHERE bar_author_id=$user->id OR bar_last_author_id=$user->id ORDER BY bar_edition_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de bares que se encuentran publicados votados por un usuario
	 *
	 * @param User $user Usuario que ha votado los bares
	 * @return nº de bares votados por un usuario
	 */
	public static function get_user_voted_bars_count($user){
		global $db;
		if ($user){
			$sql = "SELECT count(*) FROM bars WHERE bar_id IN (SELECT vote_bar_id FROM votes WHERE vote_user_id=$user->id AND vote_value<>0) LIMIT 1";
			return $db->get_var($sql);

		}else{
			return 0;
		}
	}

	/**
	 * Consulta un rango de Ids de bares votados por un usuario
	 *
	 * @param User $user Usuario que ha votado los bares
	 * @param int $current_page Nº de la página que identifica el rango de resultados a devolver
	 * @return rango de Ids de bares votados por un usuario
	 */
	public static function get_user_voted_bar_ids($user, $current_page){
		global $db, $settings;

		$offset=($current_page -1) * $settings['PAGE_SIZE'];
		$page_size = $settings['PAGE_SIZE'];

		$sql = "SELECT bar_id FROM bars WHERE bar_id IN (SELECT vote_bar_id FROM votes WHERE vote_user_id=$user->id AND vote_value<>0) ORDER BY bar_publication_date DESC LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Comprueba si un Bar ya se encuentra dado de alta
	 *
	 * @param Bar $bar Bar a comprobar
	 * @return true si se encuentra dado de alta, false en caso contrario
	 */
	public static function exists($bar){
		global $db;
		return (int)$db->get_var("SELECT count(*) FROM bars WHERE bar_name LIKE '$bar->name' AND bar_randkey = $bar->randkey LIMIT 1");
	}

	/**
	 * Comprueba si el bar está descartado
	 *
	 * @param Bar $bar
	 * @return true si está descartado, false en caso contrario
	 */
	public static function is_discarded($bar){
		global $db;
		return (int)$db->get_var("SELECT count(*) FROM bars WHERE bar_id=$bar->id AND bar_status IN ('".STATUS_OBSOLETE."', '".STATUS_DUPLICATED."', '".STATUS_NO_EXISTS."', '".STATUS_NO_TAPA_BAR."') LIMIT 1");
	}

	/**
	 * Recupera el estado de un bar en la BD
	 *
	 * @param Bar $bar
	 * @return estado del bar en la BD
	 */
	public static function get_status($bar){
		global $db;
		return $db->get_var("SELECT bar_status FROM bars WHERE bar_id=$bar->id LIMIT 1");
	}
}
?>
