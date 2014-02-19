<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Zone{
	var $id = 0;
  	var $name = '';
  	var $map_lat = '';
  	var $map_lng = '';
  	var $map_zoom = 0;
  	var $town_id = 0;
  	var $town_name = '';

	/**
	 * Contructor
	 */
	public function __construct() {

	}

	public function load($dbzone){
		$this->id = $dbzone->zone_id;
		if (isset($dbzone->zone_name))
			$this->name = $dbzone->zone_name;
		if (isset($dbzone->zone_map_lat))
			$this->map_lat = $dbzone->zone_map_lat;
		if (isset($dbzone->zone_map_lng))
			$this->map_lng = $dbzone->zone_map_lng;
		if (isset($dbzone->zone_map_zoom))
			$this->map_zoom = $dbzone->zone_map_zoom;
		if (isset($dbzone->zone_town_id))
			$this->town_id = $dbzone->zone_town_id;
		if (isset($dbzone->town_name))
			$this->town_name = $dbzone->town_name;
	}

	public function store(){
		global $db, $current_user;

		$zone_name = $db->escape($this->name);
		$zone_map_lat = $this->map_lat;
		$zone_map_lng = $this->map_lng;
		$zone_map_zoom = $this->map_zoom;
		$zone_town_id = $this->town_id;

		if ($this->id===0) {
			if ($db->query("INSERT INTO zones (zone_name, zone_map_lat, zone_map_lng, zone_map_zoom, zone_town_id) VALUES('$zone_name','$zone_map_lat','$zone_map_lng',$zone_map_zoom,$zone_town_id)")){
				$this->id = $db->insert_id;
				Log::new_zone($this->id, $current_user->id);
				return true;
			}
		} else {
			$db->query("UPDATE zones SET zone_name='$zone_name', zone_map_lat='$zone_map_lat', zone_map_lng='$zone_map_lng', zone_map_zoom=$zone_map_zoom, zone_town_id=$zone_town_id  WHERE zone_id=$this->id");
			return true;
		}
		return false;
	}
}
?>
