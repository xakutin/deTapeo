<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include '../inc.common.php';
header("Pragma: public");
header("Expires: 0"); // set expiration time
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

//Recogemos los parámetros
$value = clean_input_string($_REQUEST["value"]);
$op = clean_input_string($_REQUEST["op"]);
if ($op && $value){
	if ($op == "login")
		echo UserManager::login_exists($value);
	else if ($op == "email")
		echo UserManager::email_exists($value);
}else{
	echo false;
}
?>
