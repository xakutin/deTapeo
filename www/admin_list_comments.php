<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include classes.'BarManager.php';
include classes.'CommentManager.php';
include includes.'html_comment.php';

force_admin_user();
print_header('Admin comentarios');
print_tabs(TAB_ADMIN);
echo '<div id="main_sub">', "\n";
print_right_side();
echo '	<div id="main_izq">'."\n";
do_list_comments();
echo '	</div>'."\n";
echo '</div>', "\n";

print_footer();
////////////////////////////////////////////////////
function do_list_comments(){
	print_admin_tabs(TAB_ADMIN_COMMENTS);
	//Consultamos el nº de comentarios
	$comment_count = CommentManager::get_comments_count();
	if ($comment_count>0){
		$current_page = get_current_page();	//Recuperamos en la página donde nos encontramos

		$dbcomments = CommentManager::get_comments($current_page);
		if ($dbcomments){
			$bar_id=0;
			//Mostramos los comentarios
			foreach ($dbcomments as $dbcomment){
				//Consultamos el Bar si ha cambiado
				if ($bar_id != $dbcomment->comment_bar_id){
					if ($bar_id != 0)	echo '</ul>',"\n";
					$bar = BarManager::get_bar_with_address_info($dbcomment->comment_bar_id);
					if ($bar){
						if ($bar_id > 0) echo '<div class="hr"></div>';
						echo '<h1>',$bar->name,'</h1>',"\n";
						echo '<h2>',get_address_in_human_format($bar->street_type, $bar->street_name, $bar->street_number, $bar->town_name, $bar->province, $bar->postal_code),'</h2>',"\n";
						echo '<ul class="comment-list">',"\n";
						$bar_id = $bar->id;
					}
				}
				//Consultamos los datos del comentario
				$comment = CommentManager::get_bar_comment($dbcomment->comment_id);
				if ($comment){
					echo '<li id="comment-item-',$comment->id,'">';
					comment_print_summary($comment, true);
					echo '</li>',"\n";
				}
			}
			echo '</ul>',"\n";
			//Mostramos el paginador de comentarios
			print_comments_paginator($comment_count, $current_page, true);
		}
	}
}
?>
