<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function get_date($epoch) {
	return date("Y-m-d", $epoch);
}

function get_date_time($epoch) {
	return date("d-m-Y H:i", $epoch);
}

function get_date_in_long_human_format($date_value){
	return strftime("%e %B %Y, a las %H:%M ",$date_value);
}

function txt_time_diff($from, $now=0){
	global $now;
	$txt = '';
	if($now==0) $now = $now;
	$diff=$now-$from;
	$days=intval($diff/86400);
	$diff=$diff%86400;
	$hours=intval($diff/3600);
	$diff=$diff%3600;
	$minutes=intval($diff/60);

	if($days>1) $txt  .= " $days "._('días');
	else if ($days==1) $txt  .= " $days "._('día');

	if($hours>1) $txt .= " $hours "._('horas');
	else if ($hours==1) $txt  .= " $hours "._('hora');

	if($minutes>1) $txt .= " $minutes "._('minutos');
	else if ($minutes==1) $txt  .= " $minutes "._('minuto');

	if($txt=='') $txt = ' '. _('pocos segundos');
	return $txt;
}
?>
