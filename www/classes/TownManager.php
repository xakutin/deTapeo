<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'Town.php';
class TownManager{
	/**
	 * Recupera una localidad por su nombre y su provincia
	 *
	 * @param string $town_name Nombre de la localidad
	 * @param string $town_province Provincia de la localidad
	 * @return Localidad encontrada
	 */
	public static function get_town_by_name_and_province($town_name, $town_province){
		global $db;
		$town = null;
		if (!empty($town_name) && !empty($town_province)){
			$town_name = mb_strtolower($town_name);
			if ($dbtown = $db->get_row("SELECT town_id, town_name FROM towns WHERE lower(town_name) = '$town_name' AND town_province='$town_province'  LIMIT 1")){
				$town = new Town();
				$town->load($dbtown);
			}
		}
		return $town;
	}

	/**
	 * Busca todos los pueblos de una provincia
	 *
	 * @param string $prov_id Id de la provincia
	 * @return Array con todos los pueblos de una provincia
	 */
	public static function get_towns($prov_id){
		global $db;
		$dbtowns = $db->get_results("SELECT town_id, town_name FROM towns WHERE town_province='$prov_id' ORDER BY town_name");
		return $dbtowns;
	}

	/**
	 * Comprueba si existe una localidad con el id que se recibe por parámetro
	 *
	 * @param int $town_id Id de la localidad
	 * @return true si existe, false en caso contrario
	 */
	public static function exists($town_id){
		global $db;
		if ($town_id>0)
			return (int)$db->get_var("SELECT count(*) FROM towns WHERE town_id=$town_id LIMIT 1");
		else
			return false;
	}
}
?>
