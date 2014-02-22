<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
date_default_timezone_set("Europe/Madrid");
setlocale(LC_TIME, 'es_ES', 'spanish');

//Best Practice
//Do serialize application settings like paths into an associative array and cache or serialize that array after first execution.
//Paths
define("path", dirname(__FILE__));
define("includes", dirname(__FILE__).'/includes/');
define("fonts", dirname(__FILE__).'/includes/fonts/');
define("classes", dirname(__FILE__).'/classes/');
ini_set("include_path", '.:'.path.':'.includes.':'.classes);

$settings = Array();
$settings['BASE_URL'] = '/';
$settings['SITE_KEY'] = '';
$settings['CHECK_BEHIND_PROXY'] = false;

//Listados de Bares
$settings['PAGE_SIZE'] = 5;																//Nº de bares a mostrar en un listado

//Parametros de la BD
$settings['DB_SERVER'] = 'mysql.detapeo.net';
$settings['DB_NAME'] = 'detapeo';
$settings['DB_USER'] = 'dbwebuser';
$settings['DB_PASSWORD'] = '';
$settings['MYSQL_PERSISTENT'] = true;

//Parametros de Correo
$settings['SMTP_HOST'] = 'mail.detapeo.net';
$settings['SMTP_PORT'] = 25;
$settings['SMTP_SSL'] = false;
$settings['SMTP_USER'] = 'webmaster@detapeo.net';
$settings['SMTP_PASSWORD'] = '';

//Avatares y Fotos
$settings['AVATAR_SIZES'] = Array(80,35,22);  						//Los tamaños de imagen de avatar que se usa en la aplicación
$settings['MAX_UPLOAD_AVATAR_SIZE'] = 102400;							//Max nº de Bytes que puede pesar la imagen de avatar que se desea subir (100KB)
$settings['MAX_SIMULTANEOUS_UPLOADED_PHOTOS'] = 1;
$settings['MAX_UPLOAD_PHOTOS_SIZE'] = 1024000;						//Max nº de Bytes que puede pesar una foto de un bar que se desea subir (1MB)
$settings['IMG_BAR_THUMBNAIL_WIDTH'] = 135;								//Ancho de la imagen en miniatura de una foto
$settings['IMG_BAR_THUMBNAIL_HEIGHT'] = 102;							//Alto de la imagen en miniatura de una foto
$settings['IMG_BAR_LARGE_WIDTH'] = 552;										//Ancho de las fotos
$settings['IMG_BAR_LARGE_HEIGHT'] = 414;									//Alto de las fotos
$settings['COVER_PHOTOS_COUNT'] = 1;											//Nº de fotos genericas para usar en bares sin carátula

//Login, validar, recuperar
$settings['COOKIE_KEY_VERSION'] = 1;
$settings['COOKIE_MAX_TIME'] = 864000;										//(10 días) Nº max de segundos que puede durar la cookie de inicio de sesión
$settings['MAX_LOGIN_FAILED_TRIES_PREV_CAPTCHA'] = 3;
$settings['MAX_SECONDS_TO_VALIDATE_ACCOUNT'] = 172800;		//48 horas
$settings['MAX_SECONDS_TO_RECOVER_PASS'] = 7200; 					//2 horas

//Comentarios
$settings['SHOW_COMMENTS'] = true;
$settings['COMMENTS_ENABLED'] = true;
$settings['COMMENT_EDIT_TIME'] = 300;											//Segundos para modificar un comentario (5 minutos)
$settings['COMMENTS_PAGE_SIZE'] = 20;											//Nº de comentarios a mostrar

//Votos
$settings['DEMOCRACY'] = true;
$settings['MONTHS_TO_GET_NEGATIVE_VOTES'] = 0;								//0 Desde siempre
$settings['MAX_NEGATIVE_VOTES_TO_CHANGE_FUTURE_STATUS'] = 1;
$settings['MAX_NEGATIVE_VOTES_TO_CHANGE_STATUS'] = 2;
$settings['SECONDS_UPDATE_VOTE'] = 300; 											//(5min) Nº de segundos que deben esperar los usuarios para poder volver a modificar su voto

//Metas
$settings['KEYWORDS'] = 'tapa, tapas, tapas bar, tapas gratis, tapa gratis, de tapas gratis, bares de tapas, spanish tapas, pinchos tapas';
$settings['DESCRIPTION'] = 'Detapeo es una web colaborativa en la que podrás compartir, votar, opinar y descubrir los mejores bares de tapas gratis.';
$settings['ROBOTS'] = 'noindex, follow';
$settings['THUMBNAIL_URL'] = '';

$settings['BASE_BAR_URL'] = 'bar/';
$settings['BASE_SEARCH_URL'] = 'search/';
$settings['VERSION'] = '0.1.1';

//Avisos por email
$settings['NOTIFICATION_EMAIL']='';				//Cuentas de correo a las que avisar sobre gestión de bares
$settings['MAIL_ADVICE_NEW_BAR']= false;											//Indica si hay que avisar de que se ha añadido un nuevo bar
$settings['MAIL_ADVICE_EDIT_BAR']= false;										//Indica si hay que avisar de que se ha modificado un bar

//Google key
$settings['GMAP_KEY']= '';
?>
