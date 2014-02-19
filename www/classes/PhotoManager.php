<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'Photo.php';
class PhotoManager{
	/**
	 * Consulta todas las fotos de un Bar
	 *
	 * @param $bar Bar propietario de las fotos
	 * @return listado de las fotos de un bar
	 */
	public static function get_bar_photos($bar){
		global $db;
		$photos = null;

		if (isset($bar)){
			$sql="SELECT photo_id, photo_small_image_name,photo_large_image_name,photo_bar_id FROM photos WHERE photo_bar_id=$bar->id";
			$photos = $db->get_results($sql);
		}
		return $photos;
	}

	/**
	 * Devuelve la foto cuyo id coincide con el que se pasa por parámetro
	 *
	 * @param $id Id de la foto
	 * @return Foto encontrada, null si no se encuentra
	 */
	public static function get_bar_photo($id){
		global $db;
		$photo = null;

		if (isset($id)){
			$sql="SELECT photo_id, photo_small_image_name,photo_large_image_name,photo_bar_id FROM photos WHERE photo_id=$id";
			if ($dbphoto = $db->get_row($sql)){
				$photo =  new Photo();
				$photo->load($dbphoto);
			}
		}
		return $photo;
	}

	/**
	 * Devuelve la foto portada de un bar
	 *
	 * @param $bar Bar propietario de la foto
	 * @return Foto portada de un Bar, null si no se encuentra
	 */
	public static function get_bar_cover_photo($bar){
		global $db;
		$photo = null;

		if (isset($bar)){
			$sql="SELECT photo_id, photo_small_image_name, photo_large_image_name, photo_bar_id FROM photos WHERE photo_id=$bar->image_id";
			if ($dbphoto = $db->get_row($sql)){
				$photo =  new Photo();
				$photo->load($dbphoto);
			}
		}
		return $photo;
	}

	/**
	 * Elimina todas las fotos de un bar
	 *
	 * @param $bar Bar propietario de las fotos
	 * @return true si no ha ocurrido ningún error, false en caso contrario
	 */
	public static function del_bar_photos($bar){
		global $db;
		if (isset($bar)){
			$sql="DELETE FROM photos WHERE bar_id=$bar->id";
			if ($db->query($sql))
				return true;

		}
		return false;
	}

	/**
	 * Busca una nueva foto de portada para un bar
	 *
	 * @param $bar_id Id del bar al que pertenecerá la foto
	 * @return La foto encontrada o null si no se ha encontrado nada
	 */
	public static function get_new_bar_cover_image($bar_id){
		global $db;
		$photo = null;
		if (isset($bar_id)){
			$sql="SELECT photo_id FROM photos WHERE photo_bar_id=$bar_id LIMIT 1";
			if ($dbphoto = $db->get_row($sql)){
				$photo =  new Photo();
				$photo->load($dbphoto);
			}
		}
		return $photo;
	}

	/**
	 * Elimina una foto de un Bar
	 *
	 * @param $bar_id Id del Bar
	 * @param $photo_id Id de la foto
	 * @return el id de la imagen que es portada del bar, 0 si no hay ninguna y -1 si se ha producido algún error
	 */
	public static function del_bar_photo($bar_id, $photo_id){
		global $db, $current_user;
		if (isset($bar_id)){
			//Consultamos la foto que se desea eliminar
			if ($photo = self::get_bar_photo($photo_id)){
				//El usuario debe tener permiso sobre el bar para poder borrar sus fotos
				$sql="DELETE FROM photos WHERE photo_id=$photo_id AND photo_bar_id in (SELECT bar_id FROM bars WHERE bar_id=$bar_id ".BarManager::get_sql_cond_edition_rights().")";
				if ($db->query($sql)){
					//Eliminamos los ficheros
					@unlink (get_photos_local_path().'/'.$bar_id.'/'.$photo->thumbnail);
					@unlink (get_photos_local_path().'/'.$bar_id.'/'.$photo->large);
					//Comprobamos si la foto que se ha eliminado es la portada del bar
					if ($bar = BarManager::get_bar_with_cover_data($bar_id)){
						if ($bar->image_id==$photo_id || $bar->image_id==0){
							//Buscamos otra foto de cabecera
							if ($cover_photo = self::get_new_bar_cover_image($bar->id))
								$bar->image_id = $cover_photo->id;
							else
								$bar->image_id = 0;
							if ($bar->store_cover_image())
								return $bar->image_id;

						}else{
							return $bar->image_id;
						}
					}
				}
			}
		}
		return -1;
	}

	/**
	 * Comprueba si una foto existe
	 *
	 * @param $id Id de la foto
	 * @return 1 si se encuentra, 0 si no se encuentra
	 */
	public static function photo_exists($id){
		global $db;
		return $db->get_var("SELECT SQL_NO_CACHE count(*) FROM photos WHERE photo_id=$id LIMIT 1");
	}

	/**
	 * Cambia la foto de portada de un Bar
	 *
	 * @param $bar_id Id del Bar
	 * @param $photo_id Id de la foto que será portada
	 * @return id de la foto q es portada del bar, -1 si ha habido algún error
	 */
	public static function change_bar_cover_photo($bar_id, $photo_id){
		//Recuperamos el bar con la información de su foto portada
		if ($bar = BarManager::get_bar_with_cover_data($bar_id)){
			if ($bar->image_id!=$photo_id){
				//Comprobamos que la foto existe
				if (self::photo_exists($photo_id)){
					//Actualizamos la foto de portada
					$bar->image_id=$photo_id;
					$bar->store_cover_image();
					return $bar->image_id;
				}
			}else{
				return $bar->image_id;
			}
		}
		return -1;
	}
}
?>
