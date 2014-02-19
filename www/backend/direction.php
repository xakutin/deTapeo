<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include '../inc.common.php';
include classes.'TownManager.php';
include classes.'ZoneManager.php';
//Recogemos los parámetros
$op=$_GET["op"];
$id=$_GET["id"];

$json_objects = '';
//Consultamos todos los pueblos de una provincia
if ($op == "towns"){
	$dbtowns = TownManager::get_towns($id);
	if ($dbtowns){
		foreach ($dbtowns as $dbtown){
			if (!empty($json_objects))
				$json_objects.=', ';
			$json_objects.='{"id": "'.$dbtown->town_id.'", "name": "'.$dbtown->town_name.'"}';
		}
	}
//Consultamos todas las zonas de una localidad
}else if ($op == "zones"){
	$dbzones = ZoneManager::get_zones($id);
	if ($dbzones){
		foreach ($dbzones as $dbzone){
			if (!empty($json_objects))
				$json_objects.=', ';
			$json_objects.='{"id": "'.$dbzone->zone_id.'", "name": "'.$dbzone->zone_name.'"}';
		}
	}
}
//Eliminamos la caché
header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
echo "[$json_objects]";
?>
