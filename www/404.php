<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';
header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
print_header('Página no encontrada');
print_tabs("404");
echo '<div id="main_sub">', "\n";
echo '<div style="height:500px"><h1>Página no encontrada</h1></div>', "\n";
echo '</div>', "\n";
print_footer();
?>
