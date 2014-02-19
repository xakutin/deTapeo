<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class Photo{
	var $id =0;
	var $thumbnail = '';
	var $large = '';
	var $bar_id = 0;
	public function __construct() {

	}

	public function load($dbphoto){
		$this->id = $dbphoto->photo_id;
		$this->thumbnail = $dbphoto->photo_small_image_name;
		$this->large = $dbphoto->photo_large_image_name;
		$this->bar_id = $dbphoto->photo_bar_id;
	}

	public function store(){
		global $db, $current_user, $user_ip_int, $now;


		$photo_small_image_name = $this->thumbnail;
		$photo_large_image_name = $this->large;
		$photo_bar_id = $this->bar_id;

		if ($this->id===0) {
			if ($db->query("INSERT INTO photos (photo_small_image_name, photo_large_image_name, photo_bar_id) VALUES('$photo_small_image_name', '$photo_large_image_name', $photo_bar_id)")){
				$this->id = $db->insert_id;
				return true;
			}
		} else {
			$db->query("UPDATE photos SET photo_small_image_name='$photo_small_image_name', photo_large_image_name='$photo_large_image_name', photo_bar_id=$photo_bar_id WHERE photo_id=$this->id");
			return true;
		}
		return false;
	}
}
?>
