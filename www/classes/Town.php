<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Town {
	var $id = 0;
	var $name = '';
	var $province = '';

	/**
	 * Contructor
	 */
	public function __construct() {

	}

	public function load($dbtown){
		$this->id = $dbtown->town_id;
		if (isset($dbtown->town_name))
			$this->name = $dbtown->town_name;
		if (isset($dbtown->town_province))
			$this->$province = $dbtown->town_province;
	}

	public function store(){
		global $db, $current_user;

		$town_name = $db->escape($this->name);
		$town_province = $db->escape($this->province);

		if ($this->id===0) {
			if ($db->query("INSERT INTO towns (town_name, town_province) VALUES('$town_name','$town_province')")){
				$this->id = $db->insert_id;
				Log::new_town($this->id, $current_user->id);
				return true;
			}
		} else {
			$db->query("UPDATE towns SET town_name='$town_name', town_province='$town_province' WHERE town_id=$this->id");
			return true;
		}
		return false;
	}
}
?>
