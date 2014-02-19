<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include '../inc.common.php';
include classes.'CommentManager.php';
include classes.'BarManager.php';
include classes.'Log.php';
include includes.'html_comment.php';

header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

//Recogemos los parámetros
$id = clean_input_string($_REQUEST["id"]);
$show_type = show_comment_type_to_user();

//Devuelve el texto completo de un comentario
if (is_numeric($id) && $id)
	if ($comment = CommentManager::get_bar_comment($id))
		echo get_comment_data($comment, $show_type, true, 0);
?>
