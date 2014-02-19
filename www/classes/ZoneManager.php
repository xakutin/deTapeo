<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include classes.'Zone.php';
class ZoneManager{
	/**
	 * Recupera una zona de una loacalidad cuyo nombre coincide con el que se recibe por parámetro
	 *
	 * @param String $zone_name Nombre de la zona
	 * @param int $town_id Id de la localidad
	 * @return unknown
	 */
	public static function get_zone_by_name_and_town($zone_name, $town_id){
		global $db;
		$zone = null;
		$zone_name = mb_strtolower($zone_name);
		if ($dbzone = $db->get_row("SELECT zone_id, zone_name FROM zones WHERE zone_town_id=$town_id AND lower(zone_name)='$zone_name' LIMIT 1")){
			$zone = new Zone();
			$zone->load($dbzone);
		}
		return $zone;
	}

	/**
	 * Consulta todas las zonas de una localidad
	 *
	 * @param int $town_id Id de la localidad
	 * @return Array con las zonas de una localidad
	 */
	public static function get_zones($town_id){
		global $db;
		$dbzones = $db->get_results("SELECT zone_id, zone_name FROM zones WHERE zone_town_id=$town_id ORDER BY zone_name");
		return $dbzones;
	}

	/**
	 * Comprueba si existe una zona con el id que se recibe por parámetro
	 *
	 * @param int $zone_id Id de la zona
	 * @return true si existe, false en caso contrario
	 */
	public static function exists($zone_id){
		global $db;
		if ($zone_id>0)
			return (int)$db->get_var("SELECT count(*) FROM zones WHERE zone_id=$zone_id LIMIT 1");
		else
			return false;
	}
}
?>
