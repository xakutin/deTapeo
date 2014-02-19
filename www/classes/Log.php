<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
define("LOG_TYPE_NEW_BAR", "bar_new");
define("LOG_TYPE_DISCARD_BAR", "bar_discard");
define("LOG_TYPE_EDIT_BAR", "bar_edit");
define("LOG_TYPE_PUBLISH_BAR", "bar_publish");

define("LOG_TYPE_NEW_USER", "user_new");
define("LOG_TYPE_EDIT_USER", "user_edit");
define("LOG_TYPE_LOGIN_FAILED", "user_login_failed");
define("LOG_TYPE_RECOVER_PASS", "user_recover_pass");

define("LOG_TYPE_NEW_COMMENT", "comment_new");
define("LOG_TYPE_EDIT_COMMENT", "comment_edit");
define("LOG_TYPE_DELETE_COMMENT", "comment_delete");
define("LOG_TYPE_CENSURE_COMMENT", "comment_censure");

define("LOG_TYPE_VOTE", "vote");

define("LOG_TYPE_SPAM_WARN", "spam_warn");
define("LOG_TYPE_NEW_TOWN", "town_new");
define("LOG_TYPE_NEW_ZONE", "zone_new");


class Log {
	public static function new_user($ref_id, $user_id=0){
		self::log_insert(LOG_TYPE_NEW_USER, $ref_id, $user_id);
	}

	public static function edit_user($user_edited_id, $user_id){
		self::log_insert(LOG_TYPE_EDIT_USER,$user_edited_id,$user_id);
	}

	public static function recover_pass($ref_id, $user_id=0){
		self::log_insert(LOG_TYPE_RECOVER_PASS, $ref_id, $user_id);
	}

	public static function login_failed($ip){
		self::log_insert(LOG_TYPE_LOGIN_FAILED, $ip, 0);
	}

	public static function get_previous_login_failed($ip, $seconds){
		return self::is_log_in_date_range(LOG_TYPE_LOGIN_FAILED, $ip, 0, $seconds);
	}

	public static function new_bar ($bar_id, $user_id){
		self::log_insert(LOG_TYPE_NEW_BAR,$bar_id,$user_id);
	}

	public static function edit_bar ($bar_id, $user_id){
		self::log_insert(LOG_TYPE_EDIT_BAR,$bar_id,$user_id);
	}

	public static function publish_bar ($bar_id, $user_id){
		self::log_insert(LOG_TYPE_PUBLISH_BAR,$bar_id,$user_id);
	}

	public static function new_town($town_id, $user_id){
		self::log_insert(LOG_TYPE_NEW_TOWN, $town_id, $user_id);
	}

	public static function new_zone($zone_id, $user_id){
		self::log_insert(LOG_TYPE_NEW_ZONE, $zone_id, $user_id);
	}

	public static function new_comment($comment_id, $user_id){
		self::log_insert(LOG_TYPE_NEW_COMMENT, $comment_id, $user_id);
	}

	public static function edit_comment($comment_id, $user_id, $seconds=30){
		self::log_conditional_insert(LOG_TYPE_EDIT_COMMENT, $comment_id, $user_id, $seconds);
	}

	public static function delete_comment($comment_id, $user_id){
		self::log_insert(LOG_TYPE_DELETE_COMMENT, $comment_id, $user_id);
	}

	public static function censure_comment($comment_id, $user_id){
		self::log_insert(LOG_TYPE_CENSURE_COMMENT, $comment_id, $user_id);
	}

	public static function vote($bar_id, $user_id){
		return self::log_insert(LOG_TYPE_VOTE, $bar_id, $user_id);
	}

	public static function is_previous_vote($bar_id, $user_id, $seconds){
		return self::is_log_in_date_range(LOG_TYPE_VOTE, $bar_id, $user_id, $seconds);
	}

	public static function get_last_vote_date($bar_id, $user_id){
		return self::log_get_date(LOG_TYPE_VOTE, $bar_id, $user_id);
	}

	///////////////////////////////////////////////////////////////////////////////////////
	private static function log_insert($type, $ref_id, $user_id=0) {
		global $db, $user_ip;
		return $db->query("insert into logs (log_date, log_type, log_ref_id, log_user_id, log_ip) values (now(), '$type', $ref_id, $user_id, '$user_ip')");
	}

	private static function log_conditional_insert($type, $ref_id, $user_id=0, $seconds=0) {
		if (!self::is_log_in_date_range($type, $ref_id, $user_id, $seconds)) {
			return self::log_insert($type, $ref_id, $user_id);
		}
		return false;
	}

	private static function is_log_in_date_range($type, $ref_id, $user_id=0, $seconds=0) {
		global $db;
		if ($seconds > 0) {
			$interval_cond = " AND log_date > DATE_SUB(now(), INTERVAL $seconds SECOND)";
		}
		return (int) $db->get_var("SELECT SQL_NO_CACHE count(*) FROM logs WHERE log_type='$type' AND log_ref_id = $ref_id AND log_user_id = $user_id $interval_cond ORDER BY log_date DESC LIMIT 1");
	}

	private static function log_get_date($type, $ref_id, $user_id=0) {
		global $db;
		return $db->get_var("SELECT SQL_NO_CACHE UNIX_TIMESTAMP(log_date) FROM logs WHERE log_type='$type' AND log_ref_id = $ref_id AND log_user_id = $user_id ORDER BY log_date DESC LIMIT 1");
	}
}
?>
