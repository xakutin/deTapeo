<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'Comment.php';
class CommentManager{
	/**
	 * Recupera un comentario de un Bar
	 *
	 * @param int $id Id del comentario
	 * @return Comentario de un Bar
	 */
	public static function get_bar_comment($id){
		global $db;
		$comment = null;

		if ($dbcomment = $db->get_row("SELECT comment_id, comment_type, comment_order, comment_bar_id, comment_user_id, comment_user_ip, UNIX_TIMESTAMP(comment_date) AS comment_date, comment_user_ip, comment_text, user_login FROM comments, users WHERE comment_id=$id AND comment_user_id=user_id")){
			$comment = new Comment();
			$comment->load($dbcomment);
		}
		return $comment;
	}

	/**
	 * Recupera un comentario público de un Bar, por su nº de orden
	 *
	 * @param int $order Orden del comentario
	 * @return Comentario de un Bar
	 */
	public static function get_bar_comment_by_order($order, $bar_id){
		global $db;
		$comment = null;

		if ($order){
			if ($dbcomment = $db->get_row("SELECT comment_id, comment_type, comment_order, comment_bar_id, comment_user_id, comment_user_ip, UNIX_TIMESTAMP(comment_date) AS comment_date, comment_user_ip, comment_text, user_login FROM comments, users WHERE comment_bar_id=$bar_id AND comment_order=$order AND (comment_type='normal' OR comment_type='admin') AND comment_user_id=user_id")){
				$comment = new Comment();
				$comment->load($dbcomment);
			}
		}
		return $comment;
	}

	/**
	 * Consulta el nº de comentarios sobre un Bar
	 *
	 * @param int $bar_id Id del Bar
	 * @return nº de comentarios sobre un Bar
	 */
	public static function get_bar_comments_count($bar_id){
		global $db;
		$sql = "SELECT count(*) FROM comments WHERE comment_bar_id=$bar_id";
		return $db->get_var($sql);
	}

	/**
	 * Consulta los Ids de los comentarios de un Bar
	 *
	 * @param int $bar_id Id del Bar
	 * @param int $current_page Página del listado a consultar
	 * @return Ids de los comentarios de un Bar
	 */
	public static function get_bar_comments_ids($bar_id, $current_page){
		global $db, $settings;
		$offset=($current_page -1) * $settings['COMMENTS_PAGE_SIZE'];
		$page_size = $settings['COMMENTS_PAGE_SIZE'];

		$sql = "SELECT comment_id FROM comments WHERE comment_bar_id=$bar_id ORDER BY comment_date LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de comentarios públicos (de tipo "normal" o "admin") sobre un Bar
	 *
	 * @param int $bar_id Id del Bar
	 * @return nº de comentarios públicos sobre un Bar
	 */
	public static function get_bar_public_comments_count($bar_id){
		global $db;
		$sql = "SELECT count(*) FROM comments WHERE comment_bar_id=$bar_id AND (comment_type='normal' OR comment_type='admin')";
		return $db->get_var($sql);
	}

	/**
	 * Consulta los ids de los comentarios públicos de un Bar
	 *
	 * @param int $bar_id Id del Bar
	 * @param int $current_page Página del listado de comentarios a consultar
	 * @return ids de los comentarios públicos de un Bar
	 */
	public static function get_bar_public_comments_ids($bar_id, $current_page){
		global $db, $settings;
		$offset=($current_page -1) * $settings['COMMENTS_PAGE_SIZE'];
		$page_size = $settings['COMMENTS_PAGE_SIZE'];

		$sql = "SELECT comment_id FROM comments WHERE comment_bar_id=$bar_id AND (comment_type='normal' OR comment_type='admin') ORDER BY comment_date LIMIT $offset,$page_size";
		return $db->get_col($sql);
	}

	/**
	 * Consulta el nº de comentarios enviados por un usuario
	 *
	 * @param int $user_id Id del Usuario
	 * @return nº de comentarios enviados por un usuario
	 */
	public static function get_user_comments_count($user_id){
		global $db;
		$sql = "SELECT count(*) FROM comments WHERE comment_user_id=$user_id";
		return $db->get_var($sql);
	}

	/**
	 * Consulta el nº de comentarios total
	 *
	 * @return nº de comentarios en la aplicación
	 */
	public static function get_comments_count(){
		global $db;
		$sql = "SELECT count(*) FROM comments";
		return $db->get_var($sql);
	}

	/**
	 * Consulta los ids de los comentarios y del bar al que pertenecen ordenado por fecha
	 *
	 * @param int $current_page Página del listado de comentarios a consultar
	 * @return ids de los comentarios y del bar al que pertenecen ordenado por fecha
	 */
	public static function get_comments($current_page){
		global $db, $settings;
		$offset=($current_page -1) * $settings['COMMENTS_PAGE_SIZE'];
		$page_size = $settings['COMMENTS_PAGE_SIZE'];

		$sql = "SELECT comment_id, comment_bar_id FROM comments ORDER BY comment_date LIMIT $offset,$page_size";
		return $db->get_results($sql);
	}

	/**
	 * Comprueba si un comentario ya se encuentra dado de alta
	 *
	 * @param Comment $comment Comentario a comprobar
	 * @return true si se encuentra dado de alta, false en caso contrario
	 */
	public static function exists($comment){
		global $db;

		//Comprobamos si es el mismo comentario que hemos insertado
		if (intval($db->get_var("SELECT count(*) FROM comments WHERE comment_bar_id = $comment->bar_id AND comment_user_id = $comment->user_id AND comment_randkey = $comment->randkey")))
			return true;
		else
			return false;
	}
}
?>
