<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
function print_form_stars($vote_value){
	echo '<ul class="stars">', "\n";
	for ($i=1;$i<=10;++$i){
		if ($i==$vote_value)
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'_sel" rel="',$i,'" onclick=\'$("#vote").val(this.rel);blur();return false;\' onmouseout=\'outStar($("#vote").val());\' onmouseover=\'overStar($("#vote").val());\' >',number_format(($i/2), 1, '.', ''),'</a></li>';
		else
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'" rel="',$i,'" onclick=\'$("#vote").val(this.rel);blur();return false;\' onmouseout=\'outStar($("#vote").val());\' onmouseover=\'overStar($("#vote").val());\'>',number_format(($i/2), 1, '.', ''),'</a></li>';
	}
	echo '</ul>',"\n";
	echo '<input type="hidden" id="vote" name="vote" value="', $vote_value, '" />', "\n";
}

function print_stars_bar_detail($vote_value, $bar){
	echo '<ul class="stars">', "\n";
	for ($i=1;$i<=10;++$i){
		if ($i==$vote_value)
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'_sel" rel="',$i,'" onclick=\'vote(this.rel,',$bar->id,', false);blur();return false;\' onmouseout=\'outStar($("#vote").val());\' onmouseover=\'overStar($("#vote").val());\' >',number_format(($i/2), 1, '.', ''),'</a></li>';
		else
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'" rel="',$i,'" onclick=\'vote(this.rel,',$bar->id,', false);blur();return false;\' onmouseout=\'outStar($("#vote").val());\' onmouseover=\'overStar($("#vote").val());\'>',number_format(($i/2), 1, '.', ''),'</a></li>';
	}
	echo '</ul>',"\n";
	echo '<form action="" method="post"><input type="hidden" id="vote" name="vote" value="', $vote_value, '" /></form>', "\n";
	echo '<div id="vote_msg"></div>', "\n";
}

function print_starts_tooltip($value, $bar_id){
	echo '<ul class="stars">', "\n";
	for ($i=1;$i<=10;++$i){
		if ($i==$value)
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'_sel" rel="',$i,'" onclick="vote_delayed(this.rel,',$bar_id,');blur();return false;" onmouseout="outStar(',$value,');" onmouseover="overStar(',$value,');">',number_format(($i/2), 1, '.', ''),'</a></li>';
		else
			echo '<li><a href="#" id="star',$i,'" class="star',$i,'" rel="',$i,'" onclick="vote_delayed(this.rel,',$bar_id,');blur();return false;" onmouseout="outStar(',$value,');" onmouseover="overStar(',$value,');">',number_format(($i/2), 1, '.', ''),'</a></li>';
	}
	echo '</ul>',"\n";
}

function print_stars($value){
	echo get_starts($value);
}

function get_starts($value){
	global $settings;
	if (empty($value) || $value<0) $value=0;
	$value = number_format($value, 0, ',', '');		//Quitamos los decimales
	$val = number_format(($value/2), 1, ',', '');	//Dividimos entre 2 pq la valoración mayor es 5, y el valor que llega es entre 0 y 10
	$result = '';

	if ($value>0 && $value<=10){
		for ($i=1;$i<=10;++$i){
			if (($i%2)==0){
				if ($i<=$value)
					$result.='<img src="'.$settings['BASE_URL'].'img/puntuacion_si.gif" />';
				else
					$result.='<img src="'.$settings['BASE_URL'].'img/puntuacion_no.gif" />';
			}else{
				if ($i==$value){
					$result.='<img src="'.$settings['BASE_URL'].'img/puntuacion_sn.gif" />';
					$i+=2;
				}
			}
		}
	}else if ($value==0){
		for ($i=0;$i<5;++$i)
			$result.='<img src="'.$settings['BASE_URL'].'img/puntuacion_no.gif" />';
	}
	return $result;
}
?>
