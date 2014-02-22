<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function txt_shorter($string, $len=70) {
	if (strlen($string) > $len)
	$string = substr($string, 0, $len-3) . "...";
	return $string;
}

// Used to get the text content for stories and comments
function clean_text($string, $wrap=0, $replace_nl=true, $maxlength=0) {
	$string = stripslashes(trim($string));
	$string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
	if ($wrap>0) $string = wordwrap($string, $wrap, " ", 1);
	if ($replace_nl) $string = preg_replace('/[\n\t\r]+/s', ' ', $string);
	if ($maxlength > 0) $string = mb_substr($string, 0, $maxlength);
	return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}

function clean_lines($string) {
	return preg_replace('/[\n\r]{6,}/', "\n\n", $string);
}

function save_text_to_html($string) {
	$string = text_to_html($string);
	$string = preg_replace("/\r\n|\r|\n/", "\n<br />\n", $string);
	return $string;
}

function text_to_summary($string, $length=50) {
	if (mb_strlen($string) <= $length)
	return $string;
	else
	return text_to_html(preg_replace('/&\w*$/', '', mb_substr(preg_replace("/^(.{1,$length}[^\&;])([\s].*$|$)/", '$1', preg_replace("/[\r\n\t]+/", ' ', $string)), 0, $length)), false).' ...';
}

function text_to_html($string, $do_links = true, $cr_to_html=true) {
	if ($do_links) {
		$string = preg_replace('/([;\(\[:\.\s]|^)(https*:\/\/)([^ \t\n\r\]\&]{5,70})([^ \t\n\r\]]*)([^ .\t,\n\r\(\)\"\'\]\?])/', '$1<a href="$2$3$4$5" title="$2$3$4$5" rel="nofollow">$3$5</a>', $string);
	}
	$string = preg_replace('/\b_([^\s<>]+)_\b/', "<em>$1</em>", $string);
	$string = preg_replace('/(^|[\(¡;,:¿\s])\*([^\s<>]+)\*/', "$1<strong>$2</strong>", $string);
	if ($cr_to_html)
	$string = preg_replace("/\r\n|\r|\n/", "\n<br />\n", $string);
	return $string;
}

// Clean all special chars and html/utf entities
function text_sanitize($string) {
	$string = preg_replace('/&[^ ;]{1,8};/', ' ', $string);
	$string = preg_replace('/(^|[\(¡;,:\s])[_\*]([^\s<>]+)[_\*]/', ' $2 ', $string);
	return $string;
}

function clean_search_text($string) {
	$string = preg_replace('/[\n\t\r]+/s', ' ', trim(stripslashes($string)));
	$string = preg_replace('/[\%]/', '\\\\\%', $string);
	return $string;
}

function htmlentities2unicodeentities ($input) {
	$htmlEntities = array_values (get_html_translation_table (HTML_ENTITIES, ENT_QUOTES));
	$entitiesDecoded = array_keys  (get_html_translation_table (HTML_ENTITIES, ENT_QUOTES));
	$num = count ($entitiesDecoded);
	for ($u = 0; $u < $num; ++$u) {
		$utf8Entities[$u] = '&#'.ord($entitiesDecoded[$u]).';';
	}
	return str_replace ($htmlEntities, $utf8Entities, $input);
}

function clean_input_url($string) {
	$string = preg_replace('/ /', '+', trim(stripslashes($string)));
	$string = preg_replace('/^http:\/\/http:\/\//', 'http://', $string);
	$string = preg_replace('/#.*$/', '', $string);
	return preg_replace('/[<>\r\n\t]/', '', $string);
}

function clean_input_string($string, $maxlength=0) {
	$string = preg_replace('/[ <>\'\"\r\n\t\(\)]/', '', stripslashes($string));
	if ($maxlength) $string = mb_substr($string, 0, $maxlength);
	return $string;
}

function utf8_substr($str,$start)
{
	preg_match_all("/./su", $str, $ar);

	if(func_num_args() >= 3) {
		$end = func_get_arg(2);
		return join("",array_slice($ar[0],$start,$end));
	} else {
		return join("",array_slice($ar[0],$start));
	}
}

function get_uppercase_ratio($str) {
	$str = trim(htmlspecialchars_decode($str));
	$len = mb_strlen($str);
	$uppers = preg_match_all('/[A-Z]/', $str, $matches);
	if ($uppers > 0 && $len > 0) {
		return $uppers/$len;
	}
	return 0;
}

function normalize_string_comma_separated($string) {
	$string = html_entity_decode(trim($string), ENT_COMPAT, 'UTF-8');
	$string = preg_replace('/-+/', '-', $string); // Don't allow a sequence of more than a "-"
	$string = preg_replace('/ +,/', ',', $string); // Avoid errors like " ,"
	$string = preg_replace('/[\n\t\r]+/s', ' ', $string);
	$string = preg_replace('/[\.\,] *$/', '', $string);
	// Clean strange characteres, there are feed reader (including feedburner) that are just too strict and complain loudly
	$string = preg_replace('/[<>;"\'\]\[&]/', '', $string);
	return htmlspecialchars(mb_substr(mb_strtolower($string, 'UTF-8'), 0, 500), ENT_COMPAT, 'UTF-8');
}

function escape_comma($string){
	$string = preg_replace('/"/', '\"', $string);
	return $string;
}

function encode_password($pass){
	return md5($pass);
}

function is_valid_comment_type($type){
	if ($type == COMMENT_TYPE_ADMIN || $type == COMMENT_TYPE_CENSURED || $type == COMMENT_TYPE_NORMAL || $type == COMMENT_TYPE_PRIVATE)
		return true;
	return false;
}

function is_valid_user_level($level){
	if ($level == LEVEL_DISABLED || $level == LEVEL_BANNED || $level == LEVEL_NORMAL || $level == LEVEL_ADMIN || $level == LEVEL_EDITOR)
	return true;
	return false;
}

/**
 * Comprueba si un string es un nº telefónico válido
 *
 * @param String $str Número a comprobar
 * @return true si es válido, false en caso contrario
 */
function is_valid_phone($str){
	return preg_match('/^(9|6)\d{8}$/', $str);
}

/**
 * Comprueba si un string es una URL válida
 *
 * @param String $url URL a comprobar
 * @return true si es una URL válida, false en caso contrario
 */
function is_valid_url($url){
	return preg_match('/^http:\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url);
}

function get_uri($title) {
	$title = strip_tags($title);
	$title = mb_strtolower($title, 'UTF-8');
	$title = strtolower($title);
	$title = remove_accents($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = preg_replace('/[^a-z0-9,;:\]\[\(\)\. _-]/', '', $title);
	$title = preg_replace('/[\s,;:\]\[\(\)]+/', '-', $title);
	$title = preg_replace('/\.+$|^\.+/', '', $title);
	$title = preg_replace('/\.+-|-\.+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');
  return substr($title, 0, 70);
}

function remove_accents($string) {
	$chars = array(
	// Decompositions for Latin-1 Supplement
   	chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
   	chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
   	chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
   	chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
   	chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
   	chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
   	chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
   	chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
   	chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
   	chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
   	chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
   	chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
   	chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
   	chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
   	chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
   	chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
   	chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
   	chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
   	chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
   	chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
   	chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
   	chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
   	chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
   	chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
   	chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
   	chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
   	chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
   	chr(195).chr(191) => 'y',
   	// Decompositions for Latin Extended-A
   	chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
   	chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
   	chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
   	chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
   	chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
   	chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
   	chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
   	chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
   	chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
   	chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
   	chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
   	chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
   	chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
   	chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
   	chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
   	chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
   	chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
   	chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
   	chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
   	chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
   	chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
   	chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
   	chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
   	chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
   	chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
   	chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
   	chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
   	chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
   	chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
   	chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
   	chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
   	chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
   	chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
   	chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
   	chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
   	chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
   	chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
   	chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
   	chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
   	chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
   	chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
   	chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
   	chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
   	chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
   	chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
   	chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
   	chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
   	chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
   	chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
   	chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
   	chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
   	chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
   	chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
   	chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
   	chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
   	chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
   	chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
   	chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
   	chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
   	chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
   	chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
   	chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
   	chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
   	chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
   	// Euro Sign
   	chr(226).chr(130).chr(172) => 'E');

   	$string = strtr($string, $chars);
	return $string;
}

/**
 * Comprueba la fortaleza de una contraseña.
 * 	0 < score < 34 Mala contraseña
 * 34 < score < 68 Buena contraseña
 * 68 < score < 100 Muy buena
 * Basado en "PHP Password Strength Algorithm" (http://www.alixaxel.com/wordpress/2007/06/09/php-password-strength-algorithm/)
 *
 * @param string $password
 * @param string $username
 * @return int con fortaleza de una contraseña de 0 a 100
 */
function password_strength($password, $username){
	//Se quita el nombre del usuario en la contraseña
	if (!empty($username)){
		$password = str_ireplace($username, '', $password);
	}
	$strength = 0;
	$password_length = strlen($password);

	//Contraseña superior a 4 caracteres
	if ($password_length < 4)
		return $strength;
	else
		$strength = $password_length * 4;

	//Comprobamos caracteres únicos (mínimo 2)
	$temp = str_split($password);
	if (count(array_unique($temp))<2)
		return 0;

	//Comprobamos caracteres contiguos (por lo menos tiene que haber 2 cambios )
	$temp = 0;
	for ($i=0, $j=1;$i<$password_length; ++$i,++$j){
		$ord1 = ord($password[$i]);
		if ($j<$password_length){
			$ord2 = ord($password[$j]);
			if (abs($ord1-$ord2)>1)
				++$temp;
		}
  }
  if ($temp<2) return 0;

	for ($i = 2; $i <= 4; ++$i){
		$temp = str_split($password, $i);
		$strength -= (ceil($password_length / $i) - count(array_unique($temp)));
	}

	preg_match_all('/[0-9]/', $password, $numbers);

	if (!empty($numbers)){
		$numbers = count($numbers[0]);
		if ($numbers >= 3)
			$strength += 5;
	}else{
		$numbers = 0;
	}

	preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^¨\\\]/', $password, $symbols);

	if (!empty($symbols)){
		$symbols = count($symbols[0]);
		if ($symbols >= 2){
			$strength += 5;
		}
	}else{
		$symbols = 0;
	}

	preg_match_all('/[a-z]/', $password, $lowercase_characters);
	preg_match_all('/[A-Z]/', $password, $uppercase_characters);


	if (!empty($lowercase_characters)){
		$lowercase_characters = count($lowercase_characters[0]);
	}else{
		$lowercase_characters = 0;
	}

	if (!empty($uppercase_characters)){
		$uppercase_characters = count($uppercase_characters[0]);
	}else{
		$uppercase_characters = 0;
	}

	if (($lowercase_characters > 0) && ($uppercase_characters > 0)){
		$strength += 10;
	}
	$characters = $lowercase_characters + $uppercase_characters;

	if (($numbers > 0) && ($symbols > 0)){
		$strength += 15;
	}

	if (($numbers > 0) && ($characters > 0)){
		$strength += 15;
	}

	if (($symbols > 0) && ($characters > 0)){
		$strength += 15;
	}

	if (($numbers == 0) && ($symbols == 0)){
		$strength -= 10;
	}

	if (($symbols == 0) && ($characters == 0)){
		$strength -= 10;
	}

	if ($strength < 0){
		$strength = 0;
	}

	if ($strength > 100){
		$strength = 100;
	}

	return $strength;
}
?>
