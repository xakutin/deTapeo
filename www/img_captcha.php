<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
include 'inc.settings.php';
include classes.'CaptchaSecurityImages.php';
putenv('GDFONTPATH='.fonts);
header('Content-Type: image/jpeg');
$captcha = new CaptchaSecurityImages(155,45,5);
?>
