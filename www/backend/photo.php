<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include '../inc.common.php';
include classes.'BarManager.php';
include classes.'PhotoManager.php';

header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

//Recogemos los parámetros
$op = $_GET['op'];
$id = $_GET['id'];
$bar_id = $_GET['bar_id'];

if ($op=="select"){
	echo PhotoManager::change_bar_cover_photo($bar_id, $id);

}else if ($op=="delete"){
	echo PhotoManager::del_bar_photo($bar_id, $id);
}
?>
