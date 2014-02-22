<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function comment_print_summary($comment, $show_type=false){
	global $current_user, $settings;
	if ($comment->order) echo '<span id="comment-',$comment->order,'"></span>'."\n";
	echo '<div class="comment-avatar"><a href="',$settings['BASE_URL'],'user?id=',$comment->user_id,'" rel="nofollow" title="',$comment->user_login,'"><img src="',get_avatar_url($comment->user_id),'" alt="',$comment->user_login,'" border="0" /></a>';
	if ($comment->is_editable())
		echo '<br/><a href="javascript:editComment(',$comment->id,')" class="grey">editar</a>';
	echo '</div>'."\n";
	echo '<div class="comment-data" id="comment-id-',$comment->id,'">',"\n";
	echo get_comment_data($comment,$show_type);
	echo '</div>'."\n";
	if ($comment->order)
		echo '<div class="comment-number"><a href="#comment-',$comment->order,'">#',$comment->order,'</a></div>'."\n";
	else
		echo '<div class="comment-number"></div>'."\n";
}

function get_comment_data($comment, $show_type=false, $show_date=true, $text_length=COMMENT_NUM_CHARS_TO_SHOW){
	global $settings;
	$result = '	<a href="'.$settings['BASE_URL'].'user?id='.$comment->user_id.'" class="comment-author" rel="nofollow" title="'.$comment->user_login.'">'.$comment->user_login.':</a>'."\n";
	if ($show_date){
		$result.= '	<a href="#comment-'.$comment->order.'" class="comment-date">';
		if ($show_type && !$comment->is_normal())
			$result.= '<strong>['.$comment->type.']</strong>&nbsp;&nbsp;&nbsp;';
		$result.= get_date_in_long_human_format($comment->date).'</a>'."\n";
	}
	$result.= '	<div class="comment-text" id="comment-text-'.$comment->id.'">'.get_comment_text($comment,$text_length).'</div>'."\n";

	return $result;
}

function get_comment_text($comment, $length=0){
	$expand = '';
	if ($length>0 && mb_strlen($comment->text) > ($length + $length/2)) {
		$comment->text = preg_replace('/&\w*$/', '', mb_substr($comment->text, 0 , $length));
		$expand = '...&nbsp;&nbsp;<br/><a href="javascript:getComment('.$comment->id.');" title="resto del comentario" class="und">&#187;&nbsp;ver todo el comentario</a>';
	}
	return put_smileys(put_comment_tooltips(text_to_html($comment->text), $comment->bar_id)).$expand;
}

function put_comment_tooltips($str,$bar_id) {
	return preg_replace('/(^|[\(\s])#([1-9][0-9]*)/', "$1<a class='und' href=\"javascript:tooltip.hide(this);\" onmouseover=\"tooltip.comment(event, '$2', $bar_id);\" onmouseout=\"tooltip.hide(event);\">#$2</a>", $str);
}

function comment_print_type_combobox($status = '', $combo_name = 'comment_type'){
	echo '<select name="',$combo_name,'" id="',$combo_name,'" tabindex="2">';
	if (empty($status)){
		echo '	<option value="',COMMENT_TYPE_NORMAL,'" selected="selected">Normal</option>';
		echo '	<option value="',COMMENT_TYPE_PRIVATE,'" ',$status==COMMENT_TYPE_PRIVATE?'selected="selected"':'','>Privado</option>';
		echo '	<option value="',COMMENT_TYPE_ADMIN,'" ',$status==COMMENT_TYPE_ADMIN?'selected="selected"':'','>Administrativo</option>';

	}else if ($status==COMMENT_TYPE_NORMAL || $status==COMMENT_TYPE_ADMIN){
		echo '	<option value="',COMMENT_TYPE_NORMAL,'" ',$status==COMMENT_TYPE_NORMAL?'selected="selected"':'','>Normal</option>';
		echo '	<option value="',COMMENT_TYPE_CENSURED,'" ',$status==COMMENT_TYPE_CENSURED?'selected="selected"':'','>Censurado</option>';
		echo '	<option value="',COMMENT_TYPE_PRIVATE,'" ',$status==COMMENT_TYPE_PRIVATE?'selected="selected"':'','>Privado</option>';
		echo '	<option value="',COMMENT_TYPE_ADMIN,'" ',$status==COMMENT_TYPE_ADMIN?'selected="selected"':'','>Administrativo</option>';
	}
	echo '</select>';
}

function comment_print_edit_form($comment){
	global $current_user;
	$rows = min(40, max(substr_count($comment->text, "\n") * 2, 8));
	if ($current_user){
		echo '<form>', "\n";
		echo '<textarea id="ce_text_',$comment->id,'" style="width:99%" rows="',$rows,'">',$comment->text,'</textarea><br/>'."\n";
		echo '<p style="padding:2px 0px 2px 0px;text-align:right;">';
		if ($current_user->is_editor()){
			//Los censurados o privados no se pueden cambiar de tipo
			if ($comment->type!=COMMENT_TYPE_CENSURED && $comment->type!=COMMENT_TYPE_PRIVATE){
				echo '<label for="ce_type_',$comment->id,'">Tipo:</label> ';
				comment_print_type_combobox($comment->type, 'ce_type_'.$comment->id);
			}else{
				echo '<strong class="grey">['.$comment->type.']</strong>';
			}
			echo ' <a href="javascript:sendEditedComment(',$comment->id,',\'delete\');" class="bot" style="width:7em;">Eliminar</a> ';
		}
		echo '<a href="javascript:sendEditedComment(',$comment->id,',\'save\');" class="bot" style="width:7em;">Guardar</a>';
		echo '</p>';
		echo '<input type="hidden" id="ce_key_',$comment->id,'" value="'.$comment->key().'" />'."\n";
		echo '</form>'."\n";
	}
}

function comment_print_tooltip_text($comment){
	echo $comment->text."\n";
}

function print_comments_paginator($total, $current_page, $show_total=false, $reverse = false){
	global $settings;
	$index_limit = 10;

	if ($total > $settings['COMMENTS_PAGE_SIZE']){
		//Recoger los parametros del Query string
		$query_string=preg_replace('/page=[0-9]+/', '', $_SERVER['QUERY_STRING']);
		$query_string=preg_replace('/^&*(.*)&*$/', "$1", $query_string);
		$query_string=preg_replace('/(#.*)$/', '', $query_string);
		if (!empty($query_string)) {
			$query_string = htmlspecialchars($query_string);
			$query_string = "&amp;$query_string";
		}

		//Calculamos el nº total de páginas, la página actual y los indices de la 1ª y la última a mostrar
		$total_pages=ceil($total/$settings['COMMENTS_PAGE_SIZE']);
		if (!$current_page) {
			if ($reverse) $current_page = $total_pages;
			else $current_page = 1;
		}
		$start=max($current_page-intval($index_limit/2), 1);
		$end=$start+$index_limit-1;

		echo '<div id="cont_paginacion">';
		//Escribimos el nº de bares encontrados
		if ($show_total){
			if ($total ==1)
				echo '<span class="numero_bares">1 comentario</span>';
			else
				echo '<span class="numero_bares">', $total, ' comentarios</span>';
		}
		//Botón anterior
		if ($current_page==1) {
			echo '<span class="bot_disabled">anterior</span>';
		} else {
			$i = $current_page-1;
			echo '<a href="?page=',$i,$query_string,'" title="Anterior" class="botsnumeracion_no">anterior</a>';
		}
		//Mostrar la 1ª página si procede
		if ($start>1) {
			$i = 1;
			echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
		}

		//Mostramos las páginas siguientes
		for ($i=$start; $i<=$end && $i<=$total_pages; ++$i) {
			if ($i==$current_page) {
				echo '<span class="botsnumeracion_si">', $i, '</span>';
			} else {
				echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
			}
		}
		//Mostramos la última página si no se ha enseñado antes
		if ($total_pages>$end) {
			$i = $total_pages;
			echo '<span class="botsnumeracion_si">...</span>';
			echo '<a href="?page=',$i,$query_string,'" title="Ir a página ', $i, '" class="botsnumeracion_no">', $i, '</a>';
		}
		//Botón siguiente
		if ($current_page<$total_pages) {
			$i = $current_page+1;
			echo '<a href="?page=',$i,$query_string,'" title="Siguiente" class="botsnumeracion_no">siguiente</a>';
		} else {
			echo '<span class="bot_disabled">siguiente</span>';
		}
		echo "</div>\n";

	}else{
		echo '<div class="clear"></div>', "\n";
	}
}
?>
