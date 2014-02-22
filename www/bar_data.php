<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
/**********************************************************************************************
 * Página de edición de los metadatos de un bar.
 * Vale tanto para Bares nuevos como para la modificación de bares existentes.
 * Parametros:	- op: si su valor es "new" nos encontramos en la creación de un nuevo Bar
 * 							- id: id del bar del que se quieren modificar sus metadatos.
 * 										Este valor se puede recibir en los dos casos (Nuevo y Edición)
 *********************************************************************************************/
include 'inc.common.php';
include classes.'BarManager.php';
include classes.'TownManager.php';
include classes.'ZoneManager.php';
include classes.'SpecialityManager.php';
include classes.'Log.php';
include classes.'Mail.php';
include classes.'VoteManager.php';
include includes.'html_stars.php';
include includes.'html_bar.php';
include includes.'ban.php';

//Solo los usuarios autenticados pueden enviar bares
force_authentication();
//Comprobamos si estamos en edición o en nuevo
$is_new_bar_page=false;
if (isset($_GET['op']) && $_GET['op']=="new")
	$is_new_bar_page=true;
//Recogemos el id del bar si se ha enviado
$bar_id = 0;
if (isset($_GET['id'])) $bar_id = (int)$_GET['id'];

print_header('Datos');
//Mostramos la página
if ($is_new_bar_page)
	print_tabs("Nuevo Bar");
else
	print_tabs("Bar");

echo '<div id="main_sub">',"\n";

if ($is_new_bar_page) bar_print_steps(1);
else print_right_side();

//Comprobamos si el usuario está baneado
if (!check_banned_ip()){
	//Comprobamos si se ha enviado la confirmación de que sea un bar duplicado
	if (isset($_POST['duple']) && $_POST['duple']=="true"){
		do_duple_bar();
	}else{
		do_bar();
	}
}
echo '</div>',"\n";
print_footer();

//TODO mensaje de confirmación de borrado de un bar
//Comprobar si se ha modificado algo y avisar si se quiere abandonar la página sin pulsar el botón guardar
//////////////////////////////////////////////////////////////////////////////////////////
function do_bar(){
	global $current_user, $bar_id;
	$bar = null;

	//Edit Bar
	if ($bar_id>0){
		if ($bar=BarManager::get_bar_with_editable_data($bar_id)){
			if ($bar->is_editable()){
				$bar->specialities = SpecialityManager::get_bar_specialities($bar);
				$bar_vote = VoteManager::get_vote_value($current_user->id, $bar->id);
				//Comprobamos si hemos recibido el formulario
				if (!isset($_POST['submitted'])){
					print_form($bar, $bar_vote);
				}else{
					store_submitted_data($bar);
				}
			}else{
				print_message('<p class="error"><strong>Acceso denegado.</strong> No posee los permisos necesarios para poder modificar este Bar.</p>');
			}
		}else{
			print_message('<p class="error">No se ha encontrado el Bar que desea modificar.</p>');
		}
	//New Bar
	}else{
		if (!isset($_POST['submitted'])){
			print_form($bar);
		}else{
			store_submitted_data($bar);
		}
	}
}

/**
 * Guarda la información enviada
 *
 * @param Bar $bar Bar que se va modificar
 */
function store_submitted_data($bar){
	global $settings, $is_new_bar_page;

	$bar = get_bar_with_posted_data($bar);
	$bar_vote = (int)(clean_input_string($_POST['vote']));
	$bar_randkey = (int)clean_input_string($_POST['randkey']);
	//Comprobamos los valores de los campos recibidos
	$error_msg = check_form_fields($bar, $bar_vote, $bar_randkey);
	if ($error_msg){
		print_form($bar, $bar_vote, $error_msg);

	}else{
		//Comprobamos si el Nuevo Bar puede ser un bar duplicado
		if (!isset($_POST['duple']) && $is_new_bar_page && $db_duple_bars = BarManager::get_possible_duplicated_bars($bar)){
			print_duple_form($bar, $bar_vote, $db_duple_bars);

		}else{
			if ($is_new_bar_page && BarManager::exists($bar)){	//Se ha enviado otra vez, no lo volvemos a guardar y redirigimos al mapa
				header('Location: .'.$settings['BASE_URL'].'bar_map.php?id='.$bar->id.'&op=new');

			}else{
				//Grabamos la nueva localidad y la nueva zona si corresponde
				$error_msg = check_save_new_town($bar);
				if (!$error_msg)
					$error_msg = check_save_new_zone($bar);

				//Comprobamos si se ha producido algún error
				if ($error_msg){
					print_form($bar, $bar_vote, $error_msg);
				}else{
					//Guardamos la información del bar
					if ($bar->store()){
						//Guardamos el voto
						if ($bar_vote>0) $bar->vote($bar_vote);
						//Guardamos las especialidades
						if (SpecialityManager::save_bar_specialities($bar)){
							if ($is_new_bar_page)
								header('Location: .'.$settings['BASE_URL'].'bar_map.php?id='.$bar->id.'&op=new');
							else{
								$info_msg = "Información actualizada correctamente.";
								print_form($bar, $bar_vote, $error_msg, $info_msg);
							}
						}else{
							$error_msg = "Ha ocurrido un error al guardar las tapas del Bar.";
							print_form($bar, $bar_vote, $error_msg);
						}
					}else{
						$error_msg = "Error al guardar la información del bar.";
						print_form($bar, $bar_vote, $error_msg);
					}
				}
			}
		}
	}
}

/**
 * Comprueba si se ha añadido una nueva localidad.
 * En caso afirmativo se recoge su información y se guarda en la BD
 *
 * @param Bar $bar Bar al que se le asigna la localidad
 * @return Mensaje de error si se ha producido un error al guardar la nueva localidad
 */
function check_save_new_town($bar){
	$error_msg = '';
	//Si se ha introducido una nueva localidad la guardamos
	if (!empty($bar->town_name)){
		//Comprobamos si existe
		if ($town = TownManager::get_town_by_name_and_province($bar->town_name, $bar->province)){
			$bar->town_id = $town->id;
		}else{
			//Insertamos la nueva localidad
			$town = new Town();
			$town->name = $bar->town_name;
			$town->province = $bar->province;
			if ($town->store())
				$bar->town_id = $town->id;
			else
				$error_msg = "Se ha producido un error al guardar la localidad:" + $bar->town_name;
		}
	}
	return $error_msg;
}

/**
 * Comprueba si se ha añadido una nueva Zona.
 * En caso afirmativo se recoge la información y se guarda en la BD
 *
 * @param Bar $bar Bar al que se le asigna la zona
 * @return Mensaje de error si se ha producido un error al guardar la nueva localidad
 */
function check_save_new_zone($bar){
	$error_msg = '';
	//Si se ha introducido una nueva zona la guardamos
	if (!empty($bar->zone_name)){
		//Comprobamos si existe
		if ($zone = ZoneManager::get_zone_by_name_and_town($bar->zone_name, $bar->town_id)){
			$bar->zone_id = $zone->id;
		}else{
			$zone = new Zone();
			$zone->name = $bar->zone_name;
			$zone->town_id = $bar->town_id;
			if ($zone->store())
				$bar->zone_id = $zone->id;
			else
				$error_msg = "Se ha producido un error al guardar la Zona:" + $bar->zone_name;
		}
	}
	return $error_msg;
}

/**
 * Si se esta modificando un Bar se marca como duplicado.
 * Se escribe el formulario con los datos enviados menos el nombre.
 *
 */
function do_duple_bar(){
	$bar = get_bar_with_posted_data();
	$bar_vote = (int)(clean_input_string($_POST['vote']));

	if ($bar->id>0){			//El bar ya existe en la BD, le cambiamos el estado a duplicado
		$bar->status = STATUS_DUPLICATED;
		$bar->store_status();
	}
	$bar->name='';
	print_form($bar, $bar_vote);	//Volvemos a mostrar el formulario con todos los datos por si se hubiese equivocado al pulsar el botón de duplicado
}

/**
 * Recoge la información enviada del formulario en un objeto de tipo Bar
 *
 * @param Bar $bar Bar donde recoger la información enviada
 * @return Bar con la información enviada del formulario
 */
function get_bar_with_posted_data($bar=null){
	global $current_user, $user_ip, $bar_id, $now, $is_new_bar_page;
	//Recogemos la información enviada
	if (!$bar)
		$bar = new Bar();

	$bar->id = $bar_id;
	if ($is_new_bar_page) $bar->randkey = intval($_POST['randkey']);
	$bar->name = clean_text(preg_replace('/(\w) *[;.,] *$/', "$1", $_POST['name']), 100);
	$bar->text = clean_text($_POST['description'],0,false,10000);
	$bar->street_type = clean_input_string($_POST['street_type']);
	$bar->street_name = clean_text($_POST['street_name'],0,true,255);
	$bar->street_number = clean_input_string($_POST['street_number'], 4);
	$bar->province = clean_text($_POST['province'], 0, true, 22);
	//Puede haberse seleccionado una localidad o añadir una nueva
	if (isset($_POST['town_id'])){
		$bar_town_id = clean_text($_POST['town_id'], 0, true, 100);
		$bar->town_id = $bar_town_id;
		if (!is_numeric($bar_town_id))
			$bar->town_name = $bar_town_id;
	}
	$bar->postal_code = clean_input_string($_POST['postal_code'], 5);
	$bar->phone = clean_input_string($_POST['phone'], 20);
	$bar->web_url = clean_input_url($_POST['web']);
	if ($bar->web_url == 'http://') $bar->web_url = '';
	$bar->beer_price = (float)(strtr(clean_input_string($_POST['beer_price']),',','.'));
	//Puede haberse seleccionado una zona o añadir una nueva
	if (isset($_POST['zone_id'])){
		$bar_zone_id = clean_text($_POST['zone_id']);
		$bar->zone_id = $bar_zone_id;
		if (!is_numeric($bar_zone_id))
			$bar->zone_name = $bar_zone_id;
	}
	$bar->specialities = normalize_string_comma_separated($_POST['specialities']);
	$bar_status = STATUS_QUEUED;
	if (isset($_POST['status']))
		$bar_status = clean_input_string($_POST['status']);
	$bar->status = get_new_status($bar_status);

	if ($current_user->is_editor()){
		if (isset($_POST['comments_closed'])){
			if ($_POST['comments_closed'] == 'true')
				$bar->comments_closed = true;
		}else{
			$bar->comments_closed = false;
		}
	}

	if ($bar_id==0){	//Es nuevo
		$bar->author_id = $current_user->id;
		$bar->author_ip = $user_ip;
	}else{						//Se está modificando
		if ($current_user->is_editor()){
			$bar->editor_id = $current_user->id;
			$bar->editor_ip = $user_ip;
			if ($bar->is_info_obsolete()){
				$bar->last_author_id = $current_user->id;
				$bar->last_author_ip = $user_ip;
			}
		}else{
			$bar->last_author_id = $current_user->id;
			$bar->last_author_ip = $user_ip;
		}
	}
	$bar->edition_date = $now;
	return $bar;
}

/**
 * Devuelve el estado que debe tener el Bar.
 * Si el usuario es editor el estado es el que ha seleccionado, sino será "En Cola"
 *
 * @param String $bar_status Estado seleccionado
 * @return nuevo estado del Bar
 */
function get_new_status($bar_status){
	global $current_user;
	$status = STATUS_QUEUED;

	//Si es editor el estado que haya seleccionado
	if ($current_user->is_editor())
		$status=$bar_status;

	return $status;
}

/**
 * Escribe el formulario para añadir/modificar un Bar
 *
 * @param Bar $bar Bar a editar
 * @param int $bar_vote Voto seleccionado por el usuario
 * @param String $error_msg Mensaje de error
 * @param String $info_msg Mensaje informativo
 */
function print_form($bar=null, $bar_vote=0, $error_msg='', $info_msg=''){
	global $current_user, $is_new_bar_page;
	if (empty($bar))
		$bar=new Bar();

	echo '<div id="main_izq">', "\n";
	if ($is_new_bar_page){
		echo '<h2>Paso 1: Datos del bar</h2>', "\n";
		echo '<p>Antes de dar de alta cualquier bar, comprueba si alguien lo ha añadido antes que tú.<br/>Además recuerda que esta web es para promocionar bares de tapas <b>gratis</b>, no se admiten aquellos bares en los que hay que pagar la tapa, bien directamante (pincho) o encareciendo la consumición.</p>', "\n";
	}else{
		bar_print_edit_tabs_level2(TAB_EDIT_BAR_METADATA, $bar->id);
		if (!$current_user->is_editor())
			echo '<p>Recuerda que al modificar los datos de un Bar este desaparecerá de la portada hasta que los editores aprueben los cambios.</p>', "\n";
		else
			echo '<p></p>', "\n";
	}
	echo '<form action="',$_SERVER['REQUEST_URI'], '" id="frmBar" name="frmBar" method="post" class="data">', "\n";
	echo '<table border="0" width="740" cellpadding="2" cellspacing="0">', "\n";
	echo '	<tr>', "\n";
	echo '		<td width="100px" align="right" valign="top"><label for="name"><span class="required">*</span>Nombre:</label></td>', "\n";
	echo '		<td colspan="3" valign="top"><input type="text" name="name" style="width:640px" tabindex="1" id="name" value="', $bar->name, '" maxlength="100" /></td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="description"><span class="required">*</span>Descripción:</label></td>', "\n";
	echo '		<td colspan="3" valign="top"><textarea name="description" tabindex="2" rows="10" cols="80" id="description" style="width:640px">', $bar->text, '</textarea></td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="street_name"><span class="required">*</span>Dirección:</label></td>', "\n";
	echo '		<td valign="top" colspan="2">', "\n";
	bar_print_form_type_street_combobox($bar->street_type);
	echo ' <input type="text" name="street_name" id="street_name" tabindex="4" style="width:69%;" value="', $bar->street_name, '" />', "\n";
	echo '		</td>', "\n";
	echo '		<td align="right" valign="top">', "\n";
	echo '			<label for="street_number"><span class="required">*</span>Nº:</label>', "\n";
	echo '			<input type="text" name="street_number" id="street_number" tabindex="5" size="4" maxlength="3" value="', $bar->street_number, '"/>', "\n";
	echo '		</td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="province"><span class="required">*</span>Provincia:</label></td>', "\n";
	echo '		<td valign="top">', "\n";
	bar_print_form_province_combobox($bar->province);
	echo '		</td><td valign="top" align="right"><label for="town_id"><span class="required">*</span>Localidad:</label></td>', "\n";
	echo '		<td valign="top" align="right">', "\n";
	bar_print_form_town_combobox($bar->town_id, $bar->town_name, $bar->province);
	echo '		</td>';
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="zone_id"><span class="required">*</span>Zona:</label></td>', "\n";
	echo '		<td colspan="2">';
	bar_print_form_zones_combobox($bar->zone_id, $bar->zone_name, $bar->town_id);
	echo '</td>', "\n";
	echo '		<td align="right" valign="top">', "\n";
	echo '			<label for="postal_code">Código Postal:</label>', "\n";
	echo '			<input type="text" name="postal_code" id="postal_code" tabindex="9" size="6" maxlength="5" value="', $bar->postal_code, '"/>', "\n";
	echo '		</td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="web">Web:</label></td>', "\n";
	if (empty($bar->web_url)) $bar->web_url = 'http://';
	echo '		<td valign="top" colspan="2"><input type="text" name="web" id="web" tabindex="10" style="width:100%" value="', $bar->web_url, '" maxlength="255" /></td>', "\n";
	echo '		<td align="right" valign="top">', "\n";
	echo '			<label for="phone">Teléfono:</label>', "\n";
	echo '			<input type="text" name="phone" id="phone" tabindex="11" size="10" maxlength="9" value="', $bar->phone, '" />', "\n";
	echo '		</td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="specialities"><span class="required">*</span>Tapas:</label></td>', "\n";
	echo '		<td colspan="3" valign="top"><input type="text" name="specialities" id="specialities" tabindex="12" style="width:640px" value="', $bar->specialities, '" /><br/><span class="grey">Lista separada por comas. Ej: aceitunas, chorizo frito</span></td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label for="beer_price"><span class="required">*</span>Precio caña:</label></td>', "\n";
	echo '		<td colspan="3" valign="top"><input type="text" name="beer_price" id="beer_price" tabindex="13" value="', $bar->beer_price, '" maxlength="5" size="5" /></td>', "\n";
	echo '	</tr>', "\n";
	echo '	<tr>', "\n";
	echo '		<td align="right" valign="top"><label><span class="required">*</span>Tu voto:</label></td>', "\n";
	echo '		<td colspan="3" valign="top">';
	print_form_stars($bar_vote);
	echo '		</td>', "\n";
	echo '	</tr>', "\n";
	if (!$is_new_bar_page && ($current_user->is_editor())){
		echo '	<tr>', "\n";
		echo '		<td align="right" valign="top"><label><span class="required"></span>Estado:</label></td>', "\n";
		echo '		<td colspan="3" valign="top">';
		bar_print_status_combobox($bar->status);
		echo ' <input type="checkbox" class="none" id="comments_closed" name="comments_closed" value="true" ',$bar->comments_closed?'checked':'','/><label for="comments_closed" class="normal">Comentarios cerrados</label>';
		echo '		</td>', "\n";
	}
	if ($is_new_bar_page){
		echo '	<tr><td colspan="4" align="right"><a href="#" onclick="document.frmBar.submit();return false;" class="bot" style="width:7em;">Siguiente</a></td></tr>', "\n";
	}else{
		if ($bar->is_never_published()){
			echo '	<tr><td colspan="4" align="right">';
//			echo '<a href="#" onclick="removeBar();return false;" class="bot" style="width:7em;">Eliminar</a> ';
			echo '<a href="#" onclick="document.frmBar.submit();return false;" class="bot" style="width:7em;">Guardar datos</a>';
			echo '</td></tr>', "\n";
		}else{
			echo '	<tr><td colspan="4" align="right"><a href="#" onclick="document.frmBar.submit();return false;" class="bot" style="width:7em;">Guardar datos</a></td></tr>', "\n";
		}
	}
	echo '</table>', "\n";
	echo '<input type="hidden" id="submitted" name="submitted" value="true" />', "\n";
	if ($is_new_bar_page)
		echo '<input type="hidden" name="randkey" value="'.rand(1000000,100000000).'" />'."\n";
	else
		echo '<input type="hidden" name="randkey" value="'.$bar->key().'" />'."\n";
	echo '<input type="hidden" name="remove" value="false" />'."\n";
	echo '</form>', "\n";
	if (!empty($error_msg))
		echo '<p class="error">', $error_msg, '</p>', "\n";
	if (!empty($info_msg))
		echo '<p class="ok">', $info_msg, '</p>', "\n";

	//Si es un editor mostramos los duplicados
	if ($current_user->is_editor()){
		$dbbars = BarManager::get_possible_duplicated_bars($bar);
		if (count($dbbars)>0){
			echo '<div style="margin:20px 0px 20px 0px;">', "\n";
			echo '	Posible bar duplicado. Se han encontrado varios bares con un nombre similar:',"\n";
			echo '	<table border="0" cellpadding="2" class="bg">', "\n";
			echo '		<tr><th>Nombre</th><th>Dirección</th><th>Localidad</th><th>Provincia</th><th>Estado</th></tr>', "\n";
			//Recorremos los posibles duplicados
			foreach ($dbbars as $dbbar){
				echo '		<tr><td>', $dbbar->bar_name, '</td><td>', $dbbar->bar_street_type, ' ', $dbbar->bar_street_name;
				if ($dbbar->bar_street_number)
					echo ' Nº ', $dbbar->bar_street_number, '</td>';
				echo '<td>', $dbbar->town_name, '</td>';
				echo '<td>', $dbbar->town_province, '</td>';
				echo '<td>', get_bar_status_description($dbbar->bar_status), '</td></tr>', "\n";
			}
			echo '	</table>', "\n";
			echo '</div>', "\n";
		}
	}
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
	echo '<script type="text/javascript">function removeBar(){if (confirm("¿Deseas eliminar este Bar?")){document.frmBar.remove.value="true";document.frmBar.submit();}}</script>', "\n";
}

/**
 * Escribe en pantalla el formulario con el listado de los Bares similares al que se está dando de alta.
 *
 * @param Bar $bar Bar que se está añadiendo
 * @param int $bar_vote Voto seleccionado por el usuario
 * @param Array $dbbars Array con los bares similares al que se está añadiendo
 */
function print_duple_form($bar, $bar_vote=0, $dbbars){
	echo '<div id="main_izq">', "\n";
	echo '<h2>Paso 1: Datos del bar</h2>', "\n";
	if (count($dbbars)==1)
		echo '<p>Se ha encontrado un bar con un nombre similar al que deseas enviar. Por favor, comprueba si se trata del mismo local:</p>', "\n";
	else
		echo '<p>Se han encontrado varios bares con un nombre similar al que deseas enviar. Por favor, comprueba si ya se encuentra dado de alta:</p>', "\n";
	echo '<form action="', $_SERVER['REQUEST_URI'], '" id="frmBar" name="frmBar" method="post" class="data">', "\n";
	echo '	<input type="hidden" id="name" name="name" value="', $bar->name, '" />', "\n";
	echo '	<input type="hidden" id="description" name="description" value="', $bar->text, '" />', "\n";
	echo '	<input type="hidden" id="street_type" name="street_type" value="', $bar->street_type, '" />', "\n";
	echo '	<input type="hidden" id="street_name" name="street_name" value="', $bar->street_name, '" />', "\n";
	echo '	<input type="hidden" id="street_number" name="street_number" value="', $bar->street_number, '" />', "\n";
	echo '	<input type="hidden" id="province" name="province" value="', $bar->province, '" />', "\n";
	echo '	<input type="hidden" id="town_id" name="town_id" value="', $bar->town_id, '" />', "\n";
	//echo '	<input type="hidden" id="town_name" name="town_name" value="', $bar->town_name, '" />', "\n";
	echo '	<input type="hidden" id="postal_code" name="postal_code" value="', $bar->postal_code, '" />', "\n";
	echo '	<input type="hidden" id="phone" name="phone" value="', $bar->phone, '" />', "\n";
	echo '	<input type="hidden" id="web" name="web" value="', $bar->web_url, '" />', "\n";
	echo '	<input type="hidden" id="beer_price" name="beer_price" value="', $bar->beer_price, '" />', "\n";
	echo '	<input type="hidden" id="zone_id" name="zone_id" value="', $bar->zone_id, '" />', "\n";
	//echo '	<input type="hidden" id="zone_name" name="zone_name" value="', $bar->zone_name, '" />', "\n";
	echo '	<input type="hidden" id="specialities" name="specialities" value="', $bar->specialities, '" />', "\n";
	echo '	<input type="hidden" id="vote" name="vote" value="', $bar_vote, '" />', "\n";
	echo '	<input type="hidden" id="status" name="status" value="', $bar->status, '" />', "\n";
	echo '	<input type="hidden" id="duple" name="duple" value="" />', "\n";
	echo '	<input type="hidden" id="comments_closed" name="comments_closed" value="',$bar->comments_closed,'" />', "\n";
	echo '	<input type="hidden" id="submitted" name="submitted" value="true" />', "\n";
	echo '	<input type="hidden" id="randkey" name="randkey" value="', $bar->randkey, '" />', "\n";
	echo '</form>', "\n";
	echo '<div style="margin:20px 0px 20px 0px;">', "\n";
	echo '	<table border="0" cellpadding="2" class="bg">', "\n";
	echo '		<tr><th>Nombre</th><th>Dirección</th><th>Localidad</th><th>Provincia</th><th>Estado</th></tr>', "\n";
	//Recorremos los posibles duplicados
	foreach ($dbbars as $dbbar){
		echo '		<tr><td>', $dbbar->bar_name, '</td><td>', $dbbar->bar_street_type, ' ', $dbbar->bar_street_name;
		if ($dbbar->bar_street_number)
			echo ' Nº ', $dbbar->bar_street_number, '</td>';
		echo '<td>', $dbbar->town_name, '</td>';
		echo '<td>', $dbbar->town_province, '</td>';
		echo '<td>', get_bar_status_description($dbbar->bar_status), '</td></tr>', "\n";
	}
	echo '	</table>', "\n";
	echo '</div>', "\n";
	echo '<div style="text-align:center;">', "\n";
	echo '	¿Has encontrado el bar que quieres añadir en el listado superior?<br/><br/>', "\n";
	echo '	<a href="javascript:send_duple_form(true);" class="bot">Sí, ya se había enviado</a>', "\n";
	echo '	<a href="javascript:send_duple_form(false);" class="bot">No, es un bar nuevo</a>', "\n";
	echo '</div>', "\n";
	echo '<script type="text/javascript">function send_duple_form(duple_value){document.frmBar.duple.value=duple_value;document.frmBar.submit();}</script>', "\n";
	echo '<div class="clear"></div>', "\n";
	echo '</div>', "\n";
}

/**
 * Valida los datos introducidos del Bar
 *
 * @param Bar $bar Bar que se está añadiendo/modificando
 * @param int $bar_vote Voto seleccionado por el usuario
 * @param int $bar_randkey Clave del Bar
 * @return Mensaje de error si hay algún dato que no es válido
 */
function check_form_fields($bar, $bar_vote, $bar_randkey) {
	global $is_new_bar_page;
	$error_msg = null;

	if (empty($bar->name))
		$error_msg = "Debes introducir el nombre del bar";

	else if (empty($bar->text))
		$error_msg = "Debes introducir la descripción del bar";

	else if (get_uppercase_ratio($bar->text) > 0.25 )
		$error_msg = "Demasiadas mayúsculas en la descripción del bar";

	else if (empty($bar->street_name))
		$error_msg = "Debes introducir la dirección del bar";

	else if (get_uppercase_ratio($bar->street_name) > 0.25 )
		$error_msg = "Demasiadas mayúsculas en el nombre de la calle";

	else if (!empty($bar->street_number) && !is_numeric($bar->street_number))
		$error_msg = "El nº de la calle debe ser un valor númerico";

	else if (empty($bar->province))
		$error_msg = "Debes seleccionar una provincia";

	else if (empty($bar->town_id) && empty($bar->town_name))
		$error_msg = "Debes seleccionar una localidad";

	else if (!empty($bar->town_name) && get_uppercase_ratio($bar->town_name) > 0.25)
		$error_msg = "Demasiadas mayúsculas en el nombre de la localidad";

	else if (is_numeric($bar->town_id) && !TownManager::exists($bar->town_id))
		$error_msg = "La localidad seleccionada no existe";

	else if (empty($bar->zone_id) && empty($bar->zone_name))
		$error_msg = "Debes seleccionar una zona";

	else if (is_numeric($bar->zone_id) && !ZoneManager::exists($bar->zone_id))
		$error_msg = "La zona seleccionada no existe";

	else if (!empty($bar->zone_name) && get_uppercase_ratio($bar->zone_name) > 0.25)
		$error_msg = "Demasiadas mayúsculas en el nombre de la zona";

	else if (!empty($bar->postal_code) && (!is_numeric($bar->postal_code) || strlen($bar->postal_code)!=5))
		$error_msg = "El código postal debe ser un valor númerico de 5 cifras";

	else if (!empty($bar->web_url) && !is_valid_url($bar->web_url))
		$error_msg = "La url de la web no es válida";

	else if (!empty($bar->phone) && !is_valid_phone($bar->phone))
		$error_msg = "Nº de teléfono incorrecto";

	else if (empty($bar->specialities))
		$error_msg = "Debes introducir las tapas que suelen poner.";

	else if (empty($bar->beer_price))
		$error_msg = "Debes introducir el precio de la caña";

	else if (!is_numeric($bar->beer_price))
		$error_msg = "El precio de la caña debe ser un valor númerico";

	else if ($bar->beer_price <= 0.10)
		$error_msg = "El precio de la caña debe ser un valor númerico <strong>creíble</strong>";

	else if ($bar->beer_price > 20.00)
		$error_msg = "Demasiado caro ¿no?, probablemente la caña sea algo más barata.";

	else if ($is_new_bar_page && empty($bar_vote))
		$error_msg = "Debes dar tu voto";

	else if ($is_new_bar_page && (!is_numeric($bar_vote) || $bar_vote<=0 || $bar_vote>10))
		$error_msg = "El voto debe ser un valor númerico";

	else if ($is_new_bar_page && !$bar->randkey)
		$error_msg = "Error inesperado, vuelva a intentarlo";

	else if (!$is_new_bar_page && ($bar_randkey != $bar->key()))
		$error_msg = "Error inesperado, vuelva a intentarlo";

	return $error_msg;
}
?>
