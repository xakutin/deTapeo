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
include classes.'VoteManager.php';
include includes.'html_stars.php';

header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//Recogemos los parámetros
$bar_id = clean_input_string($_GET["id"]);

if ($current_user){
	if (is_numeric($bar_id)){
		$vote_value = VoteManager::get_vote_value($current_user->id, $bar_id);
		$vote_value = intval($vote_value);
		print_starts_tooltip($vote_value, $bar_id);
	}else{
		echo '<p class="error" onclick="tooltip.hide(null);">Parámetros incorrectos.</p>';
	}
}else{
	echo '<p class="info" onclick="tooltip.hide(null);"><a href="',$settings['BASE_URL'],'user_login.php" class="nar_bold" rel="nofollow">Autenticate</a> para poder votar.</p>';
}
?>
