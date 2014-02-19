<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.common.php';

force_admin_user();
print_header('Admin usuarios');
print_tabs(TAB_ADMIN);
echo '<div id="main_sub">', "\n";
print_right_side();
echo '	<div id="main_izq">'."\n";
do_users();
echo '	</div>'."\n";
echo '</div>', "\n";
print_footer();
////////////////////////////////////////////////////
function do_users(){
	print_admin_tabs(TAB_ADMIN_USERS);
}
?>
