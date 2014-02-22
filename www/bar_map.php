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
include classes.'Log.php';
include includes.'html_bar.php';
include includes.'ban.php';

//Solo los usuarios autenticados pueden enviar bares
force_authentication();
//Comprobamos si estamos en edición o en nuevo
$is_new_bar_page=false;
if (isset($_GET['op']) && $_GET['op']=="new")
	$is_new_bar_page=true;

//Mostramos la página
print_header('Mapa', true);
if ($is_new_bar_page)
	print_tabs("Nuevo Bar");
else
	print_tabs("Bar");
echo '<div id="main_sub">', "\n";
if ($is_new_bar_page) bar_print_steps(2);
else print_right_side();
//Comprobamos si el usuario está baneado
if (!check_banned_ip())
	do_map();
echo '</div>', "\n";
print_footer();

////////////////////////////////////////////////////////////////////
function do_map(){
	global $settings, $is_new_bar_page, $current_user;
	$bar_id = 0;
	//Recogemos el id del bar
	if (isset($_GET['id'])) $bar_id = (int)$_GET['id'];

	//Consultamos el bar
	if ($bar_id>0 && $bar=BarManager::get_bar_with_address_info($bar_id)){
		if ($bar->is_editable()){
			if (!isset($_POST['submitted'])){
				print_map($bar);
			}else{
				//Recogemos la latitud y la longitud seleccionadas
				$bar_map_lat = clean_input_string($_POST['map_lat']);
				$bar_map_lng = clean_input_string($_POST['map_lng']);
				$bar_randkey = clean_input_string($_POST['randkey']);
				//Comprobamos los valores recibidos
				$error_msg = check_form_fields($bar, $bar_map_lat, $bar_map_lng, $bar_randkey);
				if ($error_msg){
					print_map($bar, $error_msg);
				}else{
					//Comprobamos si se ha modificado la información de localización
					if ($bar->map_lat != $bar_map_lat || $bar->map_lng != $bar_map_lng){
						//Guardamos la información del mapa
						$bar->map_lat = $bar_map_lat;
						$bar->map_lng = $bar_map_lng;
						if (!$bar->store_map_info()){
							if (!$current_user->is_editor()) $bar->queued();
							$error_msg="Error al guardar el punto seleccionado en el mapa";
							print_map($bar, $error_msg);
							die;
						}
					}
					//Dependiendo de si es nuevo o no, redirigimos a las fotos o no
					if ($is_new_bar_page){
						header('Location: .'.$settings['BASE_URL'].'bar_photos.php?op=new&id='.$bar->id);
					}else{
						$info_msg='Información guardada correctamente';
						print_map($bar, '', $info_msg);
					}
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
 * Escribe en pantalla el Mapa con la localización del Bar
 *
 * @param Bar $bar Bar a posicionar en el mapa
 * @param String $error_msg Mensaje de error
 * @param String $info_msg Mensaje informativo
 */
function print_map($bar, $error_msg='', $info_msg=''){
	global $is_new_bar_page, $settings;

	$address = get_address_in_gmap_format($bar->street_type, $bar->street_name, $bar->street_number, $bar->postal_code, $bar->town_name);
	echo '<div id="main_izq">', "\n";
	if ($is_new_bar_page){
		echo '<h2>Paso 2: Localización en el mapa</h2>', "\n";
		echo '<p>Comprueba que la situación en el mapa es la correcta. Si quieres modificarla haz click en el callejero.</p>', "\n";
	}else{
		bar_print_edit_tabs_level2(TAB_EDIT_BAR_MAP, $bar->id);
		echo '<p>Comprueba que la situación en el mapa es la correcta. Si quieres modificarla haz click en el callejero.</p>', "\n";
	}
	echo '<form id="frmSearch" name="frmSearch" method="post" class="data" action="" onsubmit="searchDirection();return false;">', "\n";
	echo '	<label for="address">Dirección:</label> <input type="text" id="address" name="address" value="', $address, '" class="large" /><a href="javascript:searchDirection();" class="bot" style="width:15em;">Buscar</a>', "\n";
	echo '	<div id="map" class="map_big"></div>', "\n";
	if ($is_new_bar_page){
		echo '	<div style="text-align:right;padding:0px 16px 4px 0px;">', "\n";
		echo '		<a href="',$settings['BASE_URL'],'bar_data.php?', $_SERVER["QUERY_STRING"], '" class="bot" rel="nofollow">Anterior</a>', "\n";
		echo '		<a href="javascript:document.frmBar.submit();" class="bot">Siguiente</a>', "\n";
		echo '	</div>', "\n";
	}else{
		echo '	<div style="text-align:right;padding:0px 16px 4px 0px;">', "\n";
		echo '		<a href="javascript:document.frmBar.submit();" class="bot">Guardar</a>', "\n";
		echo '	</div>', "\n";
	}
	echo '</form>', "\n";
	echo '<form action="', $_SERVER['REQUEST_URI'], '" id="frmBar" name="frmBar" method="post">', "\n";
	echo '	<input type="hidden" id="map_lat" name="map_lat" value="', $bar->map_lat, '" />', "\n";
	echo '	<input type="hidden" id="map_lng" name="map_lng" value="', $bar->map_lng, '" />', "\n";
	echo '	<input type="hidden" id="randkey" name="randkey" value="', $bar->key(), '" />', "\n";
	echo '	<input type="hidden" id="submitted" name="submitted" value="true" />', "\n";
	echo '</form>', "\n";
	if (!empty($error_msg))
		echo '<p class="error">', $error_msg, '</p>', "\n";
	if (!empty($error_msg))
		echo '<p class="ok">', $info_msg, '</p>', "\n";

	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
	echo '<script type="text/javascript">GMAP_LOAD_CLICK_LISTENER=true;GMAP_SEARCH_DIRECTION=true;';
	if (!empty($bar->map_lat) && !empty($bar->map_lng)){
		echo 'GMAP_LAT=',$bar->map_lat, '; GMAP_LNG=',$bar->map_lng,';';
	}
	echo '</script>', "\n";
}

/**
 * Comprueba que los valores recibidos del formulario son correctos
 *
 * @param Bar $bar Bar a modificar
 * @param int $bar_map_lat Latitud del pto en el mapa
 * @param int $bar_map_lng Longitud del pto en el mapa
 * @param int $bar_randkey Clave del Bar
 * @return Devuelve el mensaje de error si lo hubiese
 */
function check_form_fields($bar, $bar_map_lat, $bar_map_lng, $bar_randkey){
	$error_msg = null;
	if (empty($bar_map_lat) || !is_numeric($bar_map_lat) || empty($bar_map_lng) || !is_numeric($bar_map_lng))
		$error_msg="El punto seleccionado en el mapa no es correcto.";

	else if ($bar_randkey != $bar->key())
		$error_msg = "Error inesperado, vuelva a intentarlo";
	return $error_msg;
}
