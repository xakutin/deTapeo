<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include includes.'util_date.php';
include includes.'util_access.php';
include includes.'util_string.php';

mb_internal_encoding('UTF-8');

function get_hex_color($color, $prefix = '') {
	return $prefix . substr(preg_replace('/[^a-f\d]/i', '', $color), 0, 6);
}

function check_email($email) {
	global $globals;
	//TODO check bans
	//require_once(includes.'ban.php');
	if (!is_email($email))
		return false;
	//if(check_ban(preg_replace('/^.*@/', '', $email), 'email') || check_ban_list($email, $globals['forbidden_email_domains'])) return false;
	return true;
}

/**
 * Comprueba que el parámetro recibido es una cuenta de correo
 *
 * @param $email dirección de correo a comprobar
 * @return true si es un email correcto, false en caso contrario
 */
function is_email($email){
	if (preg_match('/[a-zA-Z0-9_\-\.]+(\+[a-zA-Z0-9_\-\.]+)*@[a-zA-Z0-9_\-\.]+\.[a-zA-Z]{2,4}$/', $email))
		return true;
	else
		return false;
}

function url_clean($url) {
	$array = explode('#', $url, 1);
	return $array[0];
}

function check_username($name) {
	return (preg_match('/^[a-zçÇñÑáéíóúäëïöü][a-z0-9_\-\.çÇñÑáéíóúäëïöü·]+$/i', $name) && strlen($name) <= 24);
}

function check_integer($which) {
	if (isset($_REQUEST[$which]))
		if (is_numeric($_REQUEST[$which]))
			return intval($_REQUEST[$which]);
	return false;
}

function get_comment_page_suffix($page_size, $order, $total=0) {
	if ($page_size > 0) {
		if ($total && $total < $page_size) return '';
		return '/'.ceil($order/$page_size);
	}
	return '';
}

function get_current_page() {
	if(($var=check_integer('page'))) {
		return $var;
	} else {
		return 1;
	}
}

function get_server_name() {
	global $server_name;
	if ($_SERVER['SERVER_NAME']) return $_SERVER['SERVER_NAME'];
	else {
		if ($server_name) return $server_name;
		else return 'detapeo.net'; // Warn: did you put the right server name?
	}
}

function get_user_profile_uri($user, $view='') {
	global $globals;

	if (!empty($globals['base_user_url'])) {
		$uri= $globals['base_url'] . $globals['base_user_url'] . htmlspecialchars($user);
		if (!empty($view)) $uri .= "/$view";
	} else {
		$uri = $globals['base_url'].'user.php?login='.htmlspecialchars($user);
		if (!empty($view)) $uri .= "&amp;view=$view";
	}
	return $uri;
}

function post_get_base_url($option='') {
	global $globals;
	if (empty($globals['base_sneakme_url'])) {
		if (empty($option)) {
			return $globals['base_url'].'sneakme/';
		} else {
			return $globals['base_url'].'sneakme/?id='.$option;
		}
	} else {
		return $globals['base_url'].$globals['base_sneakme_url'].$option;
	}
}

function get_avatar_url($user_id, $size=35) {
	global $settings;
	$subdir = intval($user_id/MAX_FILES_PER_DIR);
	//return $base_url.'/img/avatars/'.$subdir.'/'.$user->id.'-'.$size.'.jpg';
	return $settings['BASE_URL'].'img/avatars/'.$subdir.'/'.$user_id.'-'.$size.'.jpg';
}

function get_no_avatar_url($size) {
	global $globals;
	return $globals['base_url'].'img/common/no-gravatar-2-'.$size.'.jpg';
}

function do_modified_headers($time, $tag) {
	header('Last-Modified: ' . date('r', $time));
	header('ETag: "'.$tag.'"');
	header('Cache-Control: max-age=5');
}

function get_if_modified() {
	// Get client headers - Apache only
	$request = apache_request_headers();
	if (isset($request['If-Modified-Since'])) {
	// Split the If-Modified-Since (Netscape < v6 gets this wrong)
		$modifiedSince = explode(';', $request['If-Modified-Since']);
		return strtotime($modifiedSince[0]);
	} else {
		return 0;
	}
}

function guess_user_id ($str) {
	global $db;

	if (preg_match('/^[0-9]+$/', $str)) {
		// It's a number, return it as id
		return (int) $str;
	} else {
		$str = $db->escape($str);
		$id = (int) $db->get_var("select user_id from users where user_login = '$str'");
		return $id;
	}
}

function print_simpleformat_buttons($textarea_id) {
	global $globals, $current_user;

	// To avoid too many bolds and italics from new users and trolls
	if ($current_user->user_karma < 6.001) return;

	echo '<img onclick="applyTag(\''.$textarea_id.'\', \'*\');" src="'.$globals['base_url'].'img/common/richeditor-bold-01.png" alt="bold" class="rich-edit-key" />';
	echo '<img onclick="applyTag(\''.$textarea_id.'\', \'_\');" src="'.$globals['base_url'].'img/common/richeditor-italic-01.png" alt="italic" class="rich-edit-key" />';
}

function put_smileys($str) {
	global $settings, $bot;

	if ($bot) return $str;

	$server_name = get_server_name();
	$str=preg_replace('/:-{0,1}\)(\s|$)/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/smile.gif" alt=":-)" title=":-)" width="15" height="15" />$1', $str);
	$str=preg_replace('/[^t];-{0,1}\)(\s|$)/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/wink.gif" alt=";)" title=";)"  width="15" height="15" />$1', $str);
	$str=preg_replace('/:-D|:grin:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/grin.gif" alt=":-D" title=":-D" width="15" height="15" />', $str);
	$str=preg_replace('/:oops:|&lt;:-{0,1}\(/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/blush.gif" alt="&lt;&#58;(" title="&#58;oops&#58; &lt;&#58;-("  width="15" height="15" />', $str);
	$str=preg_replace('/&gt;:-{0,1}\((\s|$)/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/angry.gif" alt="&gt;&#58;-(" title="&gt;&#58;-("  width="15" height="15" />$1', $str);
	$str=preg_replace('/\?(:-){0,1}\((\s|$)/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/huh.gif" alt="?&#58;-(" title="?&#58;-( ?(" width="15" height="15" />$1', $str);
	$str=preg_replace('/:-{0,1}\((\s|$)/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/sad.gif" alt=":-(" title=":-("  width="15" height="15" />$1', $str);
	$str=preg_replace('/:-O/', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/shocked.gif" alt=":-O" title=":-O"  width="15" height="15" />', $str);
	$str=preg_replace('/ 8-{0,1}[D\)]|:cool:/', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/cool.gif" alt="8-D" title=":cool: 8-D" width="15" height="15" />', $str);
	$str=preg_replace('/:roll:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/rolleyes.gif" alt=":roll:" title=":roll:"  width="15" height="15" />', $str);
	$str=preg_replace('/:-x/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/lipsrsealed.gif" alt=":-x" title=":-x"  width="15" height="15" />', $str);
	$str=preg_replace('/([^ps]):-{0,1}\//i', '$1 <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/undecided.gif" alt=":-/" title=":-/ :/"  width="15" height="15" />', $str);
	$str=preg_replace('/:\'\(|:cry:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/cry.gif" alt=":\'(" title=":cry: :\'("  width="15" height="15" />', $str);
	$str=preg_replace('/([^a-zA-Z]|^)[xX]D+|:lol:/', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/laugh.gif" alt=":lol:" title=":lol: xD"  width="15" height="15" />', $str);
	$str=preg_replace('/ :-S/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/undecided.gif" alt=":-S" title=":-S" width="15" height="15" />', $str);
	$str=preg_replace('/:-{0,1}\|/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/blank.gif" alt=":-|" title=":-| :|" width="15" height="15" />', $str);
	$str=preg_replace('/:beer:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/beer.gif" alt=":beer:" title=":beer:" width="20" height="17" />', $str);
	$str=preg_replace('/:angel:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/angel.gif" alt=":angel:" title=":angel:" width="15" height="17" />', $str);
	$str=preg_replace('/:beach:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/beach.gif" alt=":beach:" title=":beach:" width="21" height="15" />', $str);
	$str=preg_replace('/:devil:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/devil.gif" alt=":devil:" title=":devil:" width="19" height="16" />', $str);
	$str=preg_replace('/:stop:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/stop.gif" alt=":stop:" title=":stop:" width="20" height="15" />', $str);
	$str=preg_replace('/:up:|:ok:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/thumbsup.gif" alt=":up:" title=":up: :ok:" width="21" height="15" />', $str);
	$str=preg_replace('/:down:/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/thumbdown.gif" alt=":down:" title=":down:" width="23" height="15" />', $str);
	$str=preg_replace('/([^a-zA-Z]|^)[xX]-O/i', ' <img src="http://'.$server_name.$settings['BASE_URL'].'img/smileys/yawn.gif" alt="x-O" title="x-O" width="15" height="15" />', $str);
	return $str;
}

function meta_get_current() {
	global $globals, $db, $current_user;

	$globals['meta_current'] = 0;
	$globals['meta']  = clean_input_string($_REQUEST['meta']);

	//Check for personalisation
	// Authenticated users
	if ($current_user->user_id > 0) {
		$categories = $db->get_col("SELECT pref_value FROM prefs WHERE pref_user_id = $current_user->user_id and pref_key = 'category' order by pref_value");
		if ($categories) {
			$current_user->has_personal = true;
			$globals['meta_skip'] = '?meta=_all';
			if (! $globals['meta']) {
				$globals['meta_categories'] = implode(',', $categories);
				$globals['meta']= '_personal';
			}
		} else {
			$globals['meta_categories'] = false;
		}
	} elseif ($_COOKIE['mnm_user_meta']) {
		// anonymous users
		$meta = $db->escape(clean_input_string($_COOKIE['mnm_user_meta']));
		$globals['meta_skip'] = '?meta=_all';
		$globals['meta_user_default'] = $db->get_var("select category_id from categories where category_uri = '$meta' and category_parent = 0");
		// Anonymous can select metas by cookie
		// Select user default only if no category has been selected
		if(!$_REQUEST['category'] && !$globals['meta']) {
			$globals['meta_current'] = $globals['meta_user_default'];
		}
	}

	if ($_REQUEST['category']) {
		$_REQUEST['category'] = $cat = (int) $_REQUEST['category'];
		if ($globals['meta'][0] == '_') {
			$globals['meta_current'] = $globals['meta'];
		} else {
			$globals['meta_current'] = (int) $db->get_var("select category_parent from categories where category_id = $cat and category_parent > 0");
			$globals['meta'] = '';
		}
	} elseif ($globals['meta']) {
		// Special metas begin with _
		if ($globals['meta'][0] == '_') {
			return 0;
		}
		$meta = $db->escape($globals['meta']);
		$globals['meta_current'] = $db->get_var("select category_id from categories where category_uri = '$meta' and category_parent = 0");
		if ($globals['meta_current']) {
			$globals['meta'] = '';  // Security measure
		}
	}

	if ($globals['meta_current'] > 0) {
		$globals['meta_categories'] = meta_get_categories_list($globals['meta_current']);
		if (!$globals['meta_categories']) {
			$globals['meta_current'] = 0;
		}
	}
	//echo "meta_current: " . $globals['meta_current'] . "<br/>\n";
	return $globals['meta_current'];
}

function meta_get_categories_list($id) {
	global $db;
	$categories = $db->get_col("SELECT category_id FROM categories WHERE category_parent = $id order by category_id");
	if (!$categories) return false;
	return implode(',', $categories);
}

function meta_teaser($current, $default) {
	global $globals;
	if ($current == $default)
		return META_YES;
	else
		return META_NO;
}

function meta_teaser_item() {
	global $globals, $current_user;
	if ($globals['meta'][0] != '_' || $globals['meta'] == '_all') { // Ignore special metas
		echo '<li><a class="teaser" id="meta-'.$globals['meta_current'].'" href="javascript:get_votes(\'set_meta.php\',\''.$current_user->user_id.'\',\'meta-'.$globals['meta_current'].'\',0,\''.$globals['meta_current'].'\')">'.meta_teaser($globals['meta_current'], $globals['meta_user_default']).'</a></li>';
	}
}

function fork($uri) {
	global $globals;

	$sock = @fsockopen(get_server_name(), $_SERVER['SERVER_PORT'], $errno, $errstr, 0.01 );

	if ($sock) {
		@fputs($sock, "GET {$globals['base_url']}$uri HTTP/1.0\r\n" . "Host: {$_SERVER['HTTP_HOST']}\r\n\r\n");
		return true;
	}
	return false;
}

function stats_increment($type, $all=false) {
	global $globals, $db;

	if ($globals['save_pageloads']) {
		if(!$globals['bot'] || $all) {
			$db->query("insert into pageloads (date, type, counter) values (now(), '$type', 1) on duplicate key update counter=counter+1");
		} else {
			$db->query("insert into pageloads (date, type, counter) values (now(), 'bot', 1) on duplicate key update counter=counter+1");
		}
	}
}

// Json basic functions

function json_encode_single($dict) {
	$item = '{';
	$passed = 0;
	foreach ($dict as $key => $val) {
		if ($passed) $item .= ',';
		$item .= $key . ':"' . $val . '"';
		$passed = 1;
	}
	return $item . '}';
}

//
// Memcache functions
//

$memcache = false;

function memcache_minit () {
	global $memcache, $globals;

	if ($memcache) return true;
	if ($globals['memcache_host']) {
		$memcache = new Memcache;
		if (!isset($globals['memcache_port'])) $globals['memcache_port'] = 11211;
		if ( ! @$memcache->connect($globals['memcache_host'], $globals['memcache_port']) ) {
			$memcache = false;
			syslog(LOG_INFO, "detapeo: memcache init failed");
			return false;
		}
		return true;
	}
	return false;
}

function memcache_mget ($key) {
	global $memcache;

	if (memcache_minit()) return $memcache->get($key);
	return false;
}


function memcache_madd ($key, $str, $expire=0) {
	global $memcache;
	if (memcache_minit()) return $memcache->add($key, $str, false, $expire);
	return false;
}

function memcache_mprint ($key) {
	global $memcache;
	if (memcache_minit() && ($value = $memcache->get($key))) {
		echo $value;
		return true;
	}
	return false;
}

function memcache_mdelete ($key) {
	global $memcache;
	if (memcache_minit()) return $memcache->delete($key);
	return false;
}

function get_validation_user_key($user_id,$user_pass,$time){
	global $settings;
	$server_name = get_server_name();
	$key = md5($user_id.$user_pass.$time.$settings['SITE_KEY'].$server_name);
	return $key;
}

function get_address_in_gmap_format($street_type, $street_name, $street_number, $postal_code, $town_name){
	$address = $street_type.' '.$street_name;
	if (!empty($street_number))
		$address .= ' '.$street_number;
	if (!empty($postal_code))
		$address.= ', '.$postal_code;
	$address.= ', '.$town_name;
	return $address;
}

function get_address_in_human_format($street_type, $street_name, $street_number, $town_name, $province_name){
	$address = $street_type.' '.$street_name;
	if (!empty($street_number))
		$address .= ' '.$street_number;
	$address.= ', '.$town_name;
	$address.= ' ('.$province_name.')';
	return $address;
}

function get_address_in_human_format_with_postal_code($street_type, $street_name, $street_number, $town_name, $province_name, $postal_code){
	$address = $street_type.' '.$street_name;
	if (!empty($street_number))
		$address .= ' '.$street_number;
	$address.= ', '.$town_name;
	$address.= ' ('.$province_name.')';
	if (!empty($postal_code))
		$address.= 'CP '.$postal_code;
	return $address;
}

function get_avatars_local_path($user_id){
	$subdir = intval($user_id/MAX_FILES_PER_DIR);
	return path.'/img/avatars/'.$subdir.'/';
}

function get_photos_path($bar_id=0){
	global $settings;
	if ($bar_id){
		$subdir = intval($bar_id/MAX_FILES_PER_DIR);
		return $settings['BASE_URL'].'img/bars/'.$subdir.'/'.$bar_id.'/';
	}else{
		return $settings['BASE_URL'].'img/bars/';
	}
}

function get_generic_cover_photo($bar_id){
	global $settings;
	$file_name = $bar_id % $settings['COVER_PHOTOS_COUNT'];
	return $file_name.'.jpg';
}

function get_photos_local_path($bar_id){
	$subdir = intval($bar_id/MAX_FILES_PER_DIR);
	return path.'/img/bars/'.$subdir.'/'.$bar_id.'/';
}

function get_cover_photo($bar){
	if ($bar && $bar->image_id>0)
		$photo = PhotoManager::get_bar_cover_photo($bar);
	else
		$photo = null;

	return $photo;

}

/**
 * Devuelve el estado de un bar para el valor de un voto negativo
 *
 * @param int $vote_value Voto negativo
 * @return estado de un bar para el valor de un voto negativo
 */
function get_bar_status_from_vote_value($vote_value){
	switch ($vote_value){
		case VOTE_DUPLICATED:
			return STATUS_DUPLICATED;
		case VOTE_NO_EXISTS:
			return STATUS_NO_EXISTS;
		case VOTE_NO_TAPA_BAR:
			return STATUS_NO_TAPA_BAR;
		case VOTE_OBSOLETE:
			return STATUS_OBSOLETE;
	}
}

/**
 * Devuelve la descripción del estado de un Bar
 *
 * @param String $status Estado del Bar
 * @return descripción del estado de un Bar
 */
function get_bar_status_description($status){
	switch ($status){
		case STATUS_DUPLICATED:
			return 'Duplicado';
		case STATUS_NO_EXISTS:
			return 'No existe';
		case STATUS_NO_TAPA_BAR:
			return 'No pone tapas';
		case STATUS_OBSOLETE:
			return 'Información obsoleta';
		case STATUS_PUBLISHED:
			return 'Publicado';
		case STATUS_QUEUED:
			return 'Pendiente';
	}
}

/**
 * Crea el avatar de un usuario si no se había creado antes
 *
 * @param int $user_id Id del usuario
 * @param String $email Email del usuario
 */
function create_user_avatar($user_id, $email){
	global $settings;
	$avatar_path = get_avatars_local_path($user_id);
	@mkdir($avatar_path, 0777, true);
	if (!file_exists($avatar_path.$user_id.'-'.$settings['AVATAR_SIZES'][0].'.jpg'))
		create_glyphs($email, $settings['AVATAR_SIZES'], $avatar_path.$user_id);
}

/**
 * Comprueba si una URL existe realmente
 *
 * @param String $url URL a comprobar
 * @return true si existe, false en caso contrario
 */
function check_url_exists ($url){
	$url = @parse_url($url);
	if (!$url) return false;

	$url = array_map('trim', $url);
	$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
	$path = (isset($url['path'])) ? $url['path'] : '';

	if ($path == '') $path = '/';
	$path .= (isset($url['query'])) ? "?$url[query]" : '';

	if (isset($url['host']) AND $url['host']!=gethostbyname($url['host'])){
		if (PHP_VERSION >= 5 ){
			$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");

		}else{
			$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
			if (!$fp) return false;

			fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
			$headers = fread ( $fp, 128 );
			fclose ( $fp );
		}
		$headers = (is_array($headers)) ? implode ( "\n", $headers ) : $headers;
		return (bool) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
	}
	return false;
}

function add_bar_info_to_keywords($bar){
	global $settings;
	if ($bar){
		$settings['KEYWORDS'] = $bar->name;
		$settings['KEYWORDS'].= ', tapas '.$bar->town_name;
		$settings['KEYWORDS'].= ', tapas por '.$bar->town_name;
		$settings['KEYWORDS'].= ', tapas en '.$bar->town_name;
		$settings['KEYWORDS'].= ', tapas '.$bar->zone_name;
		$settings['KEYWORDS'].= ', tapas por '.$bar->zone_name;
		$settings['KEYWORDS'].= ', tapas en '.$bar->zone_name;
	}
}

function show_comment_type_to_user(){
	global $current_user;
	if ($current_user && $current_user->is_editor())
		return true;
	else
		return false;
}
?>
