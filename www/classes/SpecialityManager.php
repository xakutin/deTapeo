<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
class SpecialityManager{

	public static function save_bar_specialities($bar){
		global $db, $now;

		$words = preg_split('/[,]+/', $bar->specialities);
		if ($words) {
			$db->query("DELETE FROM specialities WHERE speciality_bar_id = $bar->id");
			$inserted = array();
			foreach ($words as $word) {
				$word=trim($word);
				if (!empty($word) && mb_strlen($word) >= 2 && empty($inserted[$word])){
					$word=mb_strtolower($word);
					$db->query("INSERT INTO specialities (speciality_name, speciality_bar_id) values ('$word', $bar->id)");
					$inserted[$word] = true;
				}
			}
			return true;
		}
		return false;
	}

	public static function get_bar_specialities($bar){
		global $db;
		$specialities = '';
		$counter = 0;
		$res = $db->get_var("SELECT SQL_NO_CACHE count(*) FROM specialities WHERE speciality_bar_id=$bar->id LIMIT 1");
		if ($res){
			foreach ($db->get_col("SELECT speciality_name FROM specialities WHERE speciality_bar_id=$bar->id") as $word) {
				if ($counter>0) $specialities .= ', ';
				$specialities .= $word;
				++$counter;
			}
		}
		return $specialities;
	}
}
?>
