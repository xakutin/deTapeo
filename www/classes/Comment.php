<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Comment{
	var $id = 0;
	var $type = 'normal';
	var $order = 0;
	var $bar_id = 0;
	var $user_id = 0;
	var $user_ip = '';
	var $user_login = '';
	var $date = 0;
	var $text = '';
	var $randkey = 0;

	public function __construct() {
	}

	/**
	 * Carga las propiedades del objeto.
	 *
	 * @param unknown_type $dbcomment Objeto con los valores del comentario
	 */
	public function load($dbcomment){
		$this->id = $dbcomment->comment_id;
		$this->type = $dbcomment->comment_type;
		$this->order = $dbcomment->comment_order;
		$this->bar_id = $dbcomment->comment_bar_id;
		$this->date = $dbcomment->comment_date;
		if (isset($dbcomment->comment_randkey))
			$this->randkey = $dbcomment->comment_randkey;
		$this->text = $dbcomment->comment_text;
		$this->user_id = $dbcomment->comment_user_id;
		$this->user_ip = $dbcomment->comment_user_ip;
		if (isset($dbcomment->user_login))
			$this->user_login = $dbcomment->user_login;
	}

	/**
	 * Guarda la información del comentario en la BD
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function store() {
		global $db, $current_user, $user_ip, $now;

		$comment_type = $this->type;
		$comment_bar_id = $this->bar_id;
		$comment_user_id = $current_user->id;
		$comment_user_ip = $user_ip;
		$comment_randkey = $this->randkey;
		$comment_text = $db->escape(clean_lines($this->text));

		if ($this->id===0) {
			if ($db->query("INSERT INTO comments (comment_type, comment_randkey, comment_bar_id, comment_user_id, comment_user_ip, comment_text, comment_date) VALUES ('$comment_type', $comment_randkey, $comment_bar_id, $comment_user_id, '$comment_user_ip','$comment_text', FROM_UNIXTIME($now))")){
				$this->id = $db->insert_id;
				$this->update_bar_comments_count();
				$this->update_order();
				Log::new_comment($this->id, $current_user->id);
				return true;
			}
		} else {
			$db->query("UPDATE comments SET comment_type='$comment_type', comment_user_ip='$comment_user_ip', comment_text='$comment_text' WHERE comment_id=$this->id");
			$this->update_bar_comments_count();
			$this->update_order();
			Log::edit_comment($this->id, $current_user->id);
			return true;
		}
		return false;
	}

	/**
	 * Modifica el orden del comentario
	 */
	function update_order() {
		global $db;

		if ($this->is_public()){
			if ($this->id){
				$order = intval($db->get_var("SELECT count(*) FROM comments WHERE comment_bar_id=$this->bar_id AND comment_id < $this->id  AND (comment_type='normal' OR comment_type='admin')"))+1;
				if ($order != $this->order) {
					$this->order = $order;
					$db->query("UPDATE comments SET comment_order=$this->order WHERE comment_id=$this->id");
				}
			}
		}else{
			if ($db->query("UPDATE comments SET comment_order=0 WHERE comment_id=$this->id"))
				$this->update_next_comments_order();
		}
	}

	/**
	 * Modifica el orden de los comentarios posteriores al actual
	 */
	function update_next_comments_order(){
		global $db;
		if ($this->order)
			$db->query("UPDATE comments SET comment_order=comment_order-1 WHERE comment_id>$this->id AND comment_order>0");
	}

	/**
	 * Elimina un comentario de la BD
	 * @return true si no ha habido ningún error, false en caso contrario
	 */
	function remove(){
		global $db, $current_user;
		if ($db->query("DELETE FROM comments WHERE comment_id=$this->id")){
			$this->update_bar_comments_count();
			$this->update_next_comments_order();
			Log::delete_comment($this->id, $current_user->id);
			return true;
		}
		return false;
	}

	function key(){
		global $settings;
		return md5($this->randkey.$settings['SITE_KEY']);
	}

	function is_editable(){
		global $current_user, $now, $settings;
		if ($current_user){
			if ($current_user->is_editor() || ($this->user_id == $current_user->id && ($now-$this->date < $settings['COMMENT_EDIT_TIME']) && $this->is_normal()))
				return true;
		}
		return false;
	}

	function is_normal(){
		return ($this->type == COMMENT_TYPE_NORMAL);
	}

	function is_public(){
		if ($this->type == COMMENT_TYPE_NORMAL || $this->type == COMMENT_TYPE_ADMIN)
			return true;
		else
			return false;
	}

	//
	// Actualiza el contador de comentarios de un Bar
	//
	private function update_bar_comments_count(){
		$bar = new Bar();
		$bar->id = $this->bar_id;
		return $bar->update_comments_count();
	}
}
?>
