<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
include includes.'util_images.php';
include classes.'BarManager.php';
include classes.'PhotoManager.php';
include includes.'html_bar.php';
include includes.'ban.php';

//Solo los usuarios autenticados pueden enviar bares
force_authentication();
//Comprobamos si estamos en edición o en nuevo
$is_new_bar_page=false;
if (isset($_GET['op']) && $_GET['op']=="new")
	$is_new_bar_page=true;

//Mostramos la página
print_header('Fotos');
if ($is_new_bar_page)
	print_tabs("Nuevo Bar");
else
	print_tabs("Bar");
echo '<div id="main_sub">', "\n";

if (!$is_new_bar_page || (isset($_POST["finish"]) && $_POST["finish"] == "true"))
	print_right_side();
else
	bar_print_steps(3);

//Comprobamos si el usuario está baneado
if (!check_banned_ip())
	do_upload_photos();
echo '</div>', "\n";
print_footer();

////////////////////////////////////////////////////////////////////
function do_upload_photos(){
	global $is_new_bar_page, $settings;
	$dbphotos = null;
	$bar_id = 0;

	//Recogemos el id del bar
	if (isset($_GET['id'])) $bar_id = (int)$_GET['id'];

	//Consultamos el bar
	if ($bar_id>0 && $bar=BarManager::get_bar_with_cover_data($bar_id)){
		if ($bar->is_editable()){
			//Consultamos las imágenes que se hayan subido anteriormente
			$dbphotos = PhotoManager::get_bar_photos($bar);

			if (!isset($_POST["submitted"])){
				print_upload_form($bar, $dbphotos);

			}else{
				if (!empty($_FILES['uploaded_image']['tmp_name'])){
					$error_msg = upload_photos($bar);
					if ($error_msg){
						print_upload_form($bar, $dbphotos, $error_msg);
					}else{
						$dbphotos = PhotoManager::get_bar_photos($bar);
						print_upload_form($bar, $dbphotos);
					}

				}else if ($_POST["finish"]=="true"){		//Se ha pulsado el botón "Finalizar" del proceso de alta
					print_message('<p class="ok"><strong>Información guardada correctamente.</strong><br/>Si deseas modificar cualquier dato puedes hacerlo desde la opción <a href="'.$settings['BASE_URL'].'user_bars_sended.php" class="und" rel="nofollow">Enviados</a> de tu <a href="'.$settings['BASE_URL'].'user" class="und" rel="nofollow">perfil</a> de usuario.<br/>Recuerda que no aparecerá en la portada hasta que los editores aprueben su publicación.</p>');

				}else{			//Prevenimos errores en el upload de ficheros, p.ej. por seleccionar ficheros mayores de 1MB
					print_upload_form($bar, $dbphotos);
				}
			}
		}else{
			print_message('<p class="error"><strong>Acceso denegado.</strong> No posee los permisos necesarios para poder modificar este Bar.</p>');
		}
	}else{
		print_message('<p class="error">No se ha encontrado el Bar que desea modificar.</p>');
	}
}

/**
 * Función encargada de realizar el upload de los ficheros, comprobar si superan los límites
 * de nº y tamaño, redimensionarlos y guardar la información en la BD
 *
 * @param Bar $bar Bar al que pertenecerán las imágenes subidas
 * @return Mensaje de error si lo hubiese
 */
function upload_photos($bar){
	global $settings;

	$uploaded_images_count = 0;
	$uploaded_size = 0;
	$target_dir = get_photos_local_path($bar->id);	//Directorio donde se guardarán las fotos

	//Creamos el directorio donde se copiarán las fotos
	@mkdir($target_dir, 0777, true);

	//Recogemos las imágenes subidas
	foreach($_FILES as $key=>$value){
		if (is_uploaded_file($_FILES[$key]['tmp_name'])){
			if (!empty($_FILES[$key]['tmp_name'])) {

				//Comprobamos que no sobrepase el nº de imagenes que se pueden subir a la vez
				if ($uploaded_images_count==$settings['MAX_SIMULTANEOUS_UPLOADED_PHOTOS'])
					return "Se ha sobrepasado el nº máximo de fotos que se pueden subir a la vez.";

				//Sumamos el tamaño y comprobamos que no se pase del límite
				$uploaded_size+=$_FILES[$key]["size"];
				if ($uploaded_size > $settings['MAX_UPLOAD_PHOTOS_SIZE'])
					return "La foto seleccionada sobrepasa el límite de tamaño admitido, intente redimensionarla a ".$settings['IMG_BAR_LARGE_WIDTH']."x".$settings['IMG_BAR_LARGE_HEIGHT']." y pruebe otra vez.";

				//Movemos la imagen al directorio y la redimensionamos a los distintos formatos
				$image_name = get_photo_name($bar, $target_dir);
				$target_file = $target_dir.$image_name.'.img';
				if (move_uploaded_file($_FILES[$key]['tmp_name'], $target_file)){
					++$uploaded_images_count;
					//Redimensionamos la imagen
					$thumb_image_name = $image_name.'-s.jpg';
					$thumb_image = $target_dir.$thumb_image_name;
					$large_image_name = $image_name.'-l.jpg';
					$large_image = $target_dir.$large_image_name;
					if (!resize_image($target_file, $thumb_image, $settings['IMG_BAR_THUMBNAIL_WIDTH'], $settings['IMG_BAR_THUMBNAIL_HEIGHT'], IMG_RESIZE_CROP)){
						@unlink($target_file);
						return "Error al redimensionar la foto";
					}
					if (!resize_image($target_file, $large_image, $settings['IMG_BAR_LARGE_WIDTH'], $settings['IMG_BAR_LARGE_HEIGHT'], IMG_RESIZE_MAINTAIN_ASPECT_RATIO)){
						@unlink($target_file);
						return "Error al redimensionar la foto";
					}
					@unlink($target_file);
					//Guardamos en la BD la referencia a las fotos
					if (!store_photo_info($bar, $thumb_image_name, $large_image_name)){
						@unlink($thumb_image);
						@unlink($large_image);
						return "Error al guardar la información de la foto en la BD";
					}
				}else{
					return 'Error al subir el fichero: '.$_FILES[$key]['name'];
				}
			}
		}
	}
}

/**
 * Contruye el nombre de la foto, usando el nombre del bar, y un incremental si la foto ya existiese
 *
 * @param Bar $bar Bar al que pertenecerá la foto
 * @param string $photo_path Path donde se guardará la foto
 * @return Nombre de la foto sin la extensión
 */
function get_photo_name($bar, $photo_path){
	$photo_name = $bar->get_uri_name();
	if ($photo_name){
		$i=0;
		$file_name = $photo_name;
		while (file_exists($photo_path.$file_name.'-l.jpg')){
			$file_name = $photo_name.'-'.$i;
			++$i;
		}
		$photo_name = $file_name;
	}else{
		$photo_name = time();
	}
	return $photo_name;
}

/**
 * Guarda la informa de una foto de un Bar en la BD
 *
 * @param $bar Bar al que pertenece la foto
 * @param $small_image_name Nombre de la imagen pequeña
 * @param $large_image_name Nombre del la imagen grande
 * @return true si no se ha producido ningún error, false en caso contrario
 */
function store_photo_info($bar, $small_image_name, $large_image_name){
	global $current_user;
	$photo =  new Photo();
	$photo->thumbnail = $small_image_name;
	$photo->large = $large_image_name;
	$photo->bar_id = $bar->id;
	if (!$current_user->is_editor()) $bar->queued();
	if ($photo->store()){
		if ($bar->image_id==0){
			$bar->image_id=$photo->id;
			$bar->store_cover_image();
		}
		return true;
	}
	return false;
}

/**
 * Escribe el formulario para subir fotos a un Bar
 *
 * @param Bar $bar bar al que se le van a añadir fotos
 * @param Array $dbphotos Fotos del Bar
 * @param String $error_msg Mensaje de error
 */
function print_upload_form($bar, $dbphotos=null, $error_msg=''){
	global $is_new_bar_page, $settings;
	echo '<div id="main_izq">', "\n";
	if ($is_new_bar_page){
		echo '	<h2>Paso 3: Fotos</h2>', "\n";
		echo '	<p style="margin-bottom:20px;">Si tienes fotos del bar puedes añadirlas en esta página. Para que el envío de las fotos sea más rápido te recomendamos redimensionarlas a '.$settings['IMG_BAR_LARGE_WIDTH'].'x'.$settings['IMG_BAR_LARGE_HEIGHT'].' (si se van a distorsionar prueba a mantener las proporciones y fijar el alto en ',$settings['IMG_BAR_LARGE_HEIGHT'],'px).<br/>Las fotos no pueden ser más grandes de 1MB.</p>', "\n";
	}else{
		bar_print_edit_tabs_level2(TAB_EDIT_BAR_PHOTOS, $bar->id);
		echo '	<p style="margin-bottom:20px;">Para que el envío de las fotos sea más rápido te recomendamos redimensionarlas a '.$settings['IMG_BAR_LARGE_WIDTH'].'x'.$settings['IMG_BAR_LARGE_HEIGHT'].'(si se van a distorsionar prueba a mantener las proporciones y fijar el alto en ',$settings['IMG_BAR_LARGE_HEIGHT'],'px).<br/>Las fotos no pueden ser más grandes de 1MB.</p>', "\n";
	}
	echo '	<form id="frmUpload" name="frmUpload" action="',$_SERVER['REQUEST_URI'], '" method="post" enctype="multipart/form-data">', "\n";
	echo '		<input type="hidden" name="finish" value="false" />', "\n";
	echo '		<input type="hidden" name="submitted" value="true" />', "\n";
	echo '		<input type="hidden" name="MAX_FILE_SIZE" value="',$settings['MAX_UPLOAD_PHOTOS_SIZE'],'" />', "\n";
	echo '		<table id="images_table" border="0" cellpadding="4" cellspacing="0" class="bg"><tbody id="images_table_body">', "\n";
	echo '			<tr id="new_image"><td width="100px;" align="right"><label>Nueva imagen:</label></td><td colspan="2"><input id="file_image" type="file" name="uploaded_image" onchange="uploadPhoto();"/></td></tr>', "\n";
	echo '		</tbody></table>', "\n";
	echo '	</form>', "\n";

	if ($dbphotos){
		$photos_path = get_photos_path($bar->id);
		echo '	<ul id="images_list">', "\n";
		$counter = 1;
		foreach($dbphotos as $dbphoto){
			if ($counter == 1 || $counter == 5){
				$counter = 1;
				echo '<li><ul>';
			}
			echo '<li id="li', $dbphoto->photo_id, '"><img alt="foto" id="photo', $dbphoto->photo_id, '" src="', $photos_path, $dbphoto->photo_small_image_name, '" onclick="selectPhoto(', $dbphoto->photo_id, ',', $bar->id, ');" ';
			if ($dbphoto->photo_id == $bar->image_id)
				echo ' class="selected"';
			echo '/><a href="javascript:deletePhoto(', $dbphoto->photo_id, ',', $bar->id, ');" class="bot_del_photo">Borrar</a></li>', "\n";
			++$counter;
			if ($counter == 5) echo '</ul></li>';
		}
		if ($counter < 5)	echo '</ul></li>';
		echo '	</ul>', "\n";
	}

	if ($is_new_bar_page){
		echo '	<div style="text-align:right;margin-top:20px;">', "\n";
		echo '		<a href="',$settings['BASE_URL'],'bar_map.php?', $_SERVER["QUERY_STRING"], '" class="bot" rel="nofollow">Anterior</a>', "\n";
		echo '		<a href="javascript:finishUploadPhotos();" class="bot">Finalizar</a>', "\n";
		echo '	</div>', "\n";
	}
	if (!empty($error_msg))
		echo '	<p class="error">', $error_msg, '</p>', "\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
}

?>

