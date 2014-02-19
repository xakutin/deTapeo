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
include includes.'html_comment.php';

header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

$bar_id = clean_input_string($_GET["bar_id"]);
$comment_order = clean_input_string($_GET["id"]);
if ($comment = CommentManager::get_bar_comment_by_order($comment_order, $bar_id))
	echo get_comment_data($comment, false, false);
else
	echo '<p class="error">Comentario no encontrado</p>';
?>
