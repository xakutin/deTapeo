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
include classes.'SpecialityManager.php';
include classes.'VoteManager.php';
include classes.'PhotoManager.php';
include classes.'Log.php';
include includes.'html_stars.php';
include includes.'html_bar.php';
include includes.'html_comment.php';
$settings['ROBOTS'] = 'index, follow';
$bar_id = 0;

//Recogemos el id del bar
if (isset($_REQUEST['id'])){
	$bar_id = (int)$_REQUEST['id'];

}else if(!empty($_SERVER['PATH_INFO'])) {
	$url_args = preg_split('/\/+/', $_SERVER['PATH_INFO']);
	array_shift($url_args); // The first element is always a "/"
	$bar_id = (int)$url_args[0];
}

//Buscamos el bar
$bar=BarManager::get_bar($bar_id);

//Damos valor a los los Metas, Title
$title_text = '';				//Título de la página
if ($bar){
	add_bar_info_to_keywords($bar);
	$title_text = $bar->name.', '.$bar->town_name;
	$settings['DESCRIPTION'] = clean_text($bar->text, 0, true, 250);
	//Buscamos la carátula del Bar y lo añadimos al meta de thumbnail
	$cover_photo = get_cover_photo($bar);
	if ($cover_photo){
		$settings['THUMBNAIL_URL'] = 'http://'.get_server_name().get_photos_path($bar->id).$cover_photo->thumbnail;
	}
}
//Si no se ha encontrado o si es un robot y no está publicado redirigimos a página no encontrada
if (!$bar || ($bot && !$bar->is_published())){
	header('Location: http://'.get_server_name().$settings['BASE_URL'].'404.php');
	die;
}
print_header($title_text, true);
print_tabs("Bar");
echo '<div id="main_sub">',"\n";
print_right_side();
echo '	<div id="main_izq">',"\n";
print_detail();
echo '	</div>',"\n";
echo '</div>',"\n";
//Damos valores a las ctes javascript de latitud y longitud
if (!$bot && !empty($bar->map_lat) && !empty($bar->map_lng)){
	echo '<script type="text/javascript">GMAP_LAT=',$bar->map_lat, '; GMAP_LNG=',$bar->map_lng,';</script>', "\n";
}
print_footer();

////////////////////////////////////////////////////////////////////////////////////
function print_detail(){
	global $current_user, $bar_id, $settings, $bar, $bot;
	$another_user = null;
	$another_user_vote = 0;

	//Comprobamos si se ha enviado un nuevo comentario y lo guardamos
	if (isset($_POST['submitted']))
		store_submitted_comment($bar);

	//Consultamos la información del bar
	if ($bar){
		$bar->specialities = SpecialityManager::get_bar_specialities($bar);
		//Buscamos la información del último usuario que modificó el Bar
		if ($bar->last_author_id>0)
			$author = UserManager::get_user_width_summary_info($bar->last_author_id);
		else
			$author = UserManager::get_user_width_summary_info($bar->author_id);
		//Comprobamos si se quiere mostrar el voto de otro usuario
		if (isset($_GET['user'])){
			$another_user_id = $_GET['user'];
			if ($another_user_id == $author->id)
				$another_user = $author;
			else
				$another_user = UserManager::get_user_width_summary_info($another_user_id);
			if ($another_user) $another_user_vote = VoteManager::get_vote_value($another_user->id, $bar->id);
		}
		//Si el usuario está autenticado buscamos el Voto que dió a este Bar
		$current_user_vote = 0;
		if ($current_user)
			$current_user_vote = VoteManager::get_vote_value($current_user->id, $bar->id);
		//Consultamos por imágenes que se hayan subido anteriormente
		$dbphotos = PhotoManager::get_bar_photos($bar);

		//Escribimos la información del Bar
		echo '<div style="margin:0px 0px 10px 10px">',"\n";
		bar_print_detail($bar, $author, $current_user_vote, $dbphotos, $another_user, $another_user_vote);

		//Escribimos la parte de comentarios
		if (!$bot && $settings['SHOW_COMMENTS'])
			print_comments($bar);
		echo '</div>',"\n";
		if (!$bot && $dbphotos) print_preload_bar_photos($bar, $dbphotos);
	}
}

function print_preload_bar_photos($bar, $dbphotos){
	global $settings;
	echo '<script type="text/javascript"><!--',"\n";
	echo 'var img_urls=new Array();',"\n";
	$i=0;
	$photos_path = get_photos_path($bar->id);
	foreach ($dbphotos as $dbphoto){
		echo 'img_urls[',$i,'] = "http://',get_server_name(),$photos_path,$dbphoto->photo_large_image_name,'";';
		++$i;
	}
  echo 'preloadImages(img_urls); //--></script>',"\n";
}

function print_comments($bar){
	global $current_user, $settings;
	//Consultamos el nº de comentarios
	if ($current_user && $current_user->is_editor())
		$comment_count = CommentManager::get_bar_comments_count($bar->id);
	else
		$comment_count = CommentManager::get_bar_public_comments_count($bar->id);
	if ($comment_count==1)
		echo '<p id="comments">1 Comentario</p>',"\n";
	else
		echo '<p id="comments">',$comment_count,' Comentarios</p>',"\n";

	if ($comment_count>0){
		$current_page = get_current_page();	//Recuperamos en la página donde nos encontramos

		if ($current_user && $current_user->is_editor())
			$dbcomment_ids = CommentManager::get_bar_comments_ids($bar->id, $current_page);
		else
			$dbcomment_ids = CommentManager::get_bar_public_comments_ids($bar->id, $current_page);
		if ($dbcomment_ids){
			echo '<ul class="comment-list">',"\n";
			//Comprobamos si se tiene que mostrar el tipo de comentario
			$show_type = show_comment_type_to_user();
			//Mostramos los comentarios
			foreach ($dbcomment_ids as $dbcomment_id){
				//Consultamos los datos del comentario
				$comment = CommentManager::get_bar_comment($dbcomment_id);
				if ($comment){
					echo '<li id="comment-item-',$comment->id,'">';
					comment_print_summary($comment, $show_type);
					echo '</li>',"\n";
				}
			}
			echo '</ul>',"\n";
			//Mostramos el paginador de comentarios
			print_comments_paginator($comment_count, $current_page);
		}
	}
	//Mostramos el formulario de nuevo comentario
	if ($settings['COMMENTS_ENABLED'] && !$bar->is_comments_closed())
		if ($bar->is_published() || ($current_user && ($bar->is_author($current_user) || $current_user->is_editor())))
			print_form_new_comment();

}

function print_form_new_comment(){
	global $current_user, $settings;
	if ($current_user){
		//Escribimos el formulario de nuevo comentario
		$querystring = '';
		if (isset($_GET['user']))	$querystring="?user=".$_GET['user'];
		echo '<form name="frmComment" id="frmComment" method="post" action="',$querystring,'" style="margin-top:14px;">',"\n";
		echo '<span style="color: #666;font-size:14px; font-weight:bold;">Deja un comentario</span>',"\n";
		echo '<textarea id="comment_text" name="comment_text" style="width:99%;" rows="5" cols="80"></textarea>',"\n";
		echo '<p style="padding:2px 0px 2px 0px; text-align:right;">';
		if ($current_user->is_editor()){
			echo '<label for="comment_type">Tipo:</label> ';
			comment_print_type_combobox();
		}
		echo ' <a href="javascript:sendComment();" class="bot">Enviar Comentario</a>';
		echo '</p>',"\n";
		echo '<input type="hidden" name="randkey" value="'.rand(1000000,100000000).'" />'."\n";
		echo '<input type="hidden" name="submitted" value="true" />'."\n";
		echo '</form>',"\n";

	}else{
		echo 'Si deseas enviar un comentario necesitas <a class="nar_bold" href="',$settings['BASE_URL'],'user_login.php?return=',$_SERVER['REQUEST_URI'],'" rel="nofollow">autenticarte</a>.'."\n";
	}
}

/**
 * Guarda la información del Comentario en la BD
 */
function store_submitted_comment($bar){
	global $current_user, $settings;

	if ($settings['COMMENTS_ENABLED'] && $current_user && $bar && !$bar->is_comments_closed()){
		if ($bar->is_published() || $bar->is_author($current_user) || $current_user->is_editor()){
			$randkey = intval($_POST['randkey']);
			$text = clean_text($_POST['comment_text'], 0, false, 10000);
			$type = '';
			if (isset($_POST['comment_type']))
				$type = clean_input_string($_POST['comment_type']);

			if ($randkey>0 && mb_strlen($text)>0 && preg_match('/[a-zA-Z:-]/', $text)) {
				$comment = new Comment();
				if (is_valid_comment_type($type))
					if ($current_user->is_editor())
						$comment->type = $type;

				$comment->bar_id = $bar->id;
				$comment->user_id = $current_user->id;
				$comment->text = $text;
				$comment->randkey = $randkey;
				if (!CommentManager::exists($comment)){
					$comment->store();
				}
			}
		}
	}
}
?>
