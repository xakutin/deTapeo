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
include classes.'Log.php';
include includes.'html_stars.php';
header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

//Recogemos los parámetros
$bar_id = clean_input_string($_GET["id"]);
$value = clean_input_string($_GET["value"]);

//Guardamos el voto
if (is_numeric($bar_id) && is_numeric($value) && $value>=VOTE_NO_EXISTS && $value<=10){
	//Comprobamos si ha votado hace poco
	$last_vote_date = Log::get_last_vote_date($bar_id, $current_user->id);
	//Calculamos el tiempo que debe esperar para poder volver a votar
	$seconds_to_wait_for_vote = $settings['SECONDS_UPDATE_VOTE'] - ($now - $last_vote_date);
	if ($seconds_to_wait_for_vote>0){
		$result = array('success'=>'false','msg'=>'<p class="error">Debes esperar unos minutos<br/>para poder modificar tu voto.</p>', 'cache'=>$seconds_to_wait_for_vote * 1000);
	}else{
		$bar = BarManager::get_bar($bar_id);
		if ($bar && $bar->is_published()){
			if ($bar->vote($value)){
				$msg_ok='<p class="ok">Gracias por su voto</p>';
				if ($settings['DEMOCRACY'])
					$result = array('success'=>'true', 'msg'=>$msg_ok, 'stars'=>get_starts($bar->real_votes_avg), 'num_votes'=> $bar->num_votes);
				else
					$result = array('success'=>'true', 'msg'=>$msg_ok, 'stars'=>get_starts($bar->votes_avg),'num_votes'=> $bar->num_votes);
			}else{
				$result = array('success'=>'false','msg'=>'<p class="error">Se ha producido un error al guardar su voto.<br/>Intentelo más tarde.</p>');
			}
		}else{
			$result = array('success'=>'false','msg'=>'<p class="error">No se ha encontrado el bar a votar.<br/>Recuerde que solo se pueden votar bares publicados.</p>');
		}
	}
}else{
	$result = array('success'=>'false','msg'=>'<p class="error">Parámetros incorrectos.</p>');
}
echo json_encode($result);
?>
