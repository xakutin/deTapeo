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

$comment = null;
//Recogemos los parámetros
$id = clean_input_string($_REQUEST["id"]);

$msg = 'El comentario no se puede modificar';

//Mostrar el formulario de edición de un comentario
if (is_numeric($id) && $id){
	if ($current_user && $comment = CommentManager::get_bar_comment($id)){
		if ($comment->is_editable()){
			if (!isset($_POST['submitted'])){
				comment_print_edit_form($comment);
				die;

			}else{
				$op = clean_input_string($_POST["op"]);
				if ($op == 'delete'){
					if ($comment->remove()){
						echo json_encode(array('success'=>'true'));
						die;
					}
				}else{
					$comment_text = clean_text($_POST["text"], 0, false, 10000);
					if (isset($_POST["type"])) $comment_type = clean_input_string($_POST["type"]);
					$comment_key = clean_input_string($_POST["key"]);
					if ($comment_key == $comment->key()){
						$comment->text = $comment_text;
						if ($current_user->is_editor() && isset($comment_type) && is_valid_comment_type($comment_type))
							$comment->type = $comment_type;
						if ($comment->store()){
							$show_type = show_comment_type_to_user();
							$comment_data = get_comment_data($comment, $show_type, true, 0);
							echo json_encode(array('success'=>'true','msg'=>$comment_data));
							die;
						}
					}
				}
			}
		}
	}else{
		$msg='No se ha encontrado el comentario.';
	}
}
if (!isset($_POST['submitted'])){
	echo '<p class="warning" onclick="tooltip.hide(null);">',$msg,'</p>'."\n";
	if ($comment){
		$show_type = show_comment_type_to_user();
		echo get_comment_data($comment, $show_type, true, 0);
	}
}else{
	echo json_encode(array('success'=>'false','msg'=>'<p class="error">'.$msg.'</p>'));
}
?>
