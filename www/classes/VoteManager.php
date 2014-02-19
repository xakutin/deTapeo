<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class VoteManager{

	/**
	 * Recupera el valor del voto dado de un usuario a un Bar
	 *
	 * @param int $user_id Id del usuario que votó
	 * @param int $bar_id Id del bar votado
	 * @return valor del voto
	 */
	public static function get_vote_value($user_id, $bar_id){
		global $db;
		return $db->get_var("SELECT vote_value FROM votes WHERE vote_bar_id=$bar_id AND vote_user_id=$user_id");
	}

	/**
	 * Inserta o actualiza el voto de un usuario a un bar
	 *
	 * @param int $user_id Id del usuario que vota
	 * @param string $user_ip Ip del usuario
	 * @param int  $bar_id Id del bar que se vota
	 * @param int $value valor del voto
	 * @return true si no ha ocurrido ningún error, false en caso contrario
	 */
	public static function vote($user_id, $user_ip, $bar_id, $value){
		if (!self::vote_exists($user_id, $bar_id))
			return self::new_vote($user_id, $user_ip, $bar_id, $value);
		else
			return self::update_vote($user_id, $user_ip, $bar_id, $value);
	}

	/**
	 * Inserta un voto en la BD
	 *
	 * @param int $user_id Id del usuario que vota
	 * @param string $user_ip Ip del usuario
	 * @param int $bar_id Id del bar votado
	 * @param int $value valor del voto
	 * @return true si no ha ocurrido ningún error, false en caso contrario
	 */
	public static function new_vote($user_id, $user_ip, $bar_id, $value){
		global $db;
		if ($db->query("INSERT HIGH_PRIORITY INTO votes (vote_bar_id, vote_user_id, vote_user_ip, vote_value) VALUES ($bar_id, $user_id, $user_ip, $value)")){
			Log::vote($bar_id, $user_id);
			return true;
		}
		return false;
	}

	/**
	 * Modifica un voto en la BD
	 *
	 * @param int $user_id Id del usuario que vota
	 * @param string $user_ip Ip del usuario
	 * @param int $bar_id Id del bar votado
	 * @param int $value valor del voto
	 * @return true si no ha ocurrido ningún error, false en caso contrario
	 */
	public static function update_vote($user_id, $user_ip, $bar_id, $value){
		global $db;
		if ($db->query("UPDATE votes SET vote_value = $value, vote_user_ip = $user_ip WHERE vote_bar_id = $bar_id AND vote_user_id = $user_id")){
			Log::vote($bar_id, $user_id);
			return true;
		}
		return false;
	}

	/**
	 * Comprueba si un usuario ha votado un bar
	 *
	 * @param int $user_id Id del usuario
	 * @param int $bar_id Id del bar
	 * @return 1 si el usuario ha votado, 0 en caso contrario
	 */
	public static function vote_exists($user_id, $bar_id){
		global $db;
		$count=$db->get_var("SELECT SQL_NO_CACHE count(*) FROM votes WHERE vote_bar_id = $bar_id AND vote_user_id = $user_id LIMIT 1");
		return $count;
	}

	/**
	 * Recupera el nº de votos positivos de un bar, así como las medias (real y ponderada) de los valores de los votos
	 *
	 * @param int $bar_id Id del Bar
	 * @return nº de votos positivos y sus medias
	 */
	public static function get_positive_votes_count_avgs($bar_id){
		global $db;
		$sql = "SELECT count(*) AS num_votes, AVG(vote_value) AS real_votes_avg, AVG(vote_value*user_trust) AS votes_avg FROM votes, users WHERE vote_bar_id=$bar_id AND vote_value>0 AND vote_user_id=user_id LIMIT 1";
		return $db->get_row($sql);
	}

	/**
	 * Recupera el nº de votos negativos, con su valor, que se han dado a un bar
	 * desde hace meses
	 *
	 * @param int $bar_id Id del bar
	 * @return nº de votos negativos con su valor
	 */
	public static function get_negative_votes_for_last_months($bar_id){
		global $db, $settings;

		$sql = "SELECT vote_value, count(*) AS num FROM votes WHERE vote_bar_id = $bar_id AND vote_value<0";
		if (!empty($settings['MONTHS_TO_GET_NEGATIVE_VOTES']))
		  $sql.= ' AND vote_date > date_sub(now(), interval '.$settings['MONTHS_TO_GET_NEGATIVE_VOTES'].' month)';
		$sql.= ' GROUP BY vote_value';
		return $db->get_results($sql);
	}

	/**
	 * Recupera el nº de votos positivos recibidos por un bar en los últimos meses
	 *
	 * @param int $bar_id Id del bar
	 * @return nº de votos positivos
	 */
	public static function get_positive_votes_count_for_last_months($bar_id){
		global $db, $settings;

		$sql = "SELECT count(*) FROM votes WHERE vote_bar_id = $bar_id AND vote_value>0";
		if (!empty($settings['MONTHS_TO_GET_NEGATIVE_VOTES']))
		  $sql.= ' AND vote_date > date_sub(now(), interval '.$settings['MONTHS_TO_GET_NEGATIVE_VOTES'].' month)';
		$sql.= ' LIMIT 1';
		return $db->get_var($sql);
	}
}
?>
