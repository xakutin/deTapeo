<?php
// +-----------------------------------------------------------+
// | The source code packaged with this file is Free Software, |
// | licensed under the AFFERO GENERAL PUBLIC LICENSE.         |
// | Please see:                                               |
// |   http://www.affero.org/oagpl.html for more information.  |
// | Copyright: 2009 xakutin                                   |
// +-----------------------------------------------------------+
//Nombres de las cookies que se guardan al logarse en la aplicación
define("COOKIE_USER","user");
define("COOKIE_KEY","key");

//Niveles de usuario
define("LEVEL_BANNED","banned"); 											//Baneado
define("LEVEL_DISABLED","disabled"); 									//Deshabilitado
define("LEVEL_NORMAL","normal"); 											//Normal
define("LEVEL_EDITOR","editor"); 											//Editor
define("LEVEL_ADMIN","admin"); 												//Administrador

//Estados de un Bar
define("STATUS_QUEUED","queued"); 										//"Pendiente"
define("STATUS_PUBLISHED","published"); 							//"Publicado"
define("STATUS_OBSOLETE","obsolete"); 								//"Información obsoleta"
define("STATUS_DUPLICATED","duplicated"); 						//"Duplicado"
define("STATUS_NO_TAPA_BAR","no_tapa_bar"); 					//"No pone tapas"
define("STATUS_NO_EXISTS","no_exists"); 							//"No existe"

//Tipos de comentarios
define("COMMENT_TYPE_NORMAL","normal"); 							//Normal
define("COMMENT_TYPE_ADMIN","admin"); 								//de administración
define("COMMENT_TYPE_PRIVATE","private"); 						//privado
define("COMMENT_TYPE_CENSURED","censured"); 					//censurado

//Valores de los votos negativos
define("VOTE_OBSOLETE",-1); 													//Valor del voto "Información obsoleta"
define("VOTE_DUPLICATED",-2); 												//Valor del voto "Duplicado"
define("VOTE_NO_TAPA_BAR",-3); 												//Valor del voto "No pone tapas"
define("VOTE_NO_EXISTS",-4); 													//Valor del voto "No existe"

define("MAX_FILES_PER_DIR", 1000);										//Nº máximo de ficheros por directorio
define("JPEG_QUALITY", 80);														//Calidad del JPEG al redimensionar

//Pestañas de la aplicación
define("TAB_TOP",1); 																	//Los mejores
define("TAB_ZONES",2); 																//Zonas
define("TAB_PROFILE",3); 															//Perfil de usuario
define("TAB_LOGIN",4); 																//Login
define("TAB_REGISTER",5); 														//Nueva cuenta
define("TAB_RECOVER",6); 															//Recuperar contraseña
define("TAB_DISCARDED",7); 														//Descartados
define("TAB_QUEUED",8); 															//Pendientes
define("TAB_ADMIN",9); 																//Administración

//Pestañas en el perfil de usuario
define("TAB_USER_PROFILE",1); 												//Información del usuario
define("TAB_USER_BARS",2); 														//Bares enviados
define("TAB_USER_COMMENTS",3); 												//Comentarios enviados
define("TAB_USER_VOTES",4); 													//Votos

//Pestañas de administración
define("TAB_ADMIN_BARS",1); 	  											//Bares
define("TAB_ADMIN_COMMENTS",2); 	  									//Comentarios
define("TAB_ADMIN_BANS",3); 													//Baneos
define("TAB_ADMIN_USERS",4); 													//Usuarios
define("TAB_ADMIN_LOGS",5); 													//Logs

//Pestañas de modificación de un bar
define("TAB_EDIT_BAR_METADATA",1); 										//Datos
define("TAB_EDIT_BAR_MAP",2); 												//Mapa
define("TAB_EDIT_BAR_PHOTOS",3); 											//Fotos

//JS a incluir en las páginas
define("JS_MAP", 1);																	//js de Google Maps

define("AVOID_BROWSER_CACHE_NUM", 4);									//Nº que se usa para evitar la cache de js y css
define("COMMENT_NUM_CHARS_TO_SHOW", 600);							//Nº de caracteres a mostrar en un comentario, antes de poner el enlave "ver todo el comentario"
define("TOP_VOTES_AVG", 7);														//Media de los votos de los bares para que puedan mostrarse en la lista de "Los mejores"


?>
