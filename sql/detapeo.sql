SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `bans` (
  `ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ban_type` enum('email','hostname','punished_hostname','ip','words','proxy') NOT NULL,
  `ban_text` char(64) NOT NULL,
  `ban_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ban_expire` timestamp NULL DEFAULT NULL,
  `ban_comment` char(100) DEFAULT NULL,
  PRIMARY KEY (`ban_id`),
  UNIQUE KEY `ban_type` (`ban_type`,`ban_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `bars` (
  `bar_id` int(20) NOT NULL AUTO_INCREMENT,
  `bar_name` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `bar_text` text COLLATE utf8_spanish_ci NOT NULL,
  `bar_image_id` int(20) DEFAULT '0',
  `bar_street_type` enum('Alameda','Avenida','Calle','Camino','Carrer','Carretera','Cuesta','Glorieta','Kalea','Pasaje','Paseo','Plaça','Plaza','Rambla','Ronda','Rúa','Sector','Travesía','Urbanización','Vía') CHARACTER SET utf8 NOT NULL,
  `bar_street_name` text COLLATE utf8_spanish_ci NOT NULL,
  `bar_street_number` int(4) DEFAULT NULL,
  `bar_town_id` int(10) NOT NULL DEFAULT '0',
  `bar_zone_id` int(10) NOT NULL DEFAULT '0',
  `bar_postal_code` char(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_phone` char(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_map_lat` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_map_lng` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_map_zoom` int(2) NOT NULL DEFAULT '8',
  `bar_web_url` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_beer_price` decimal(10,2) DEFAULT '0.00',
  `bar_author_id` int(20) NOT NULL DEFAULT '0',
  `bar_author_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_last_author_id` int(20) DEFAULT '0',
  `bar_last_author_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_editor_id` int(20) DEFAULT '0',
  `bar_editor_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bar_status` enum('queued','published','obsolete','duplicated','no_tapa_bar','no_exists') CHARACTER SET utf8 NOT NULL DEFAULT 'queued',
  `bar_comments_closed` tinyint(1) NOT NULL DEFAULT '0',
  `bar_probably_future_status` enum('obsolete','duplicated','no_tapa_bar','no_exists') CHARACTER SET utf8 DEFAULT NULL,
  `bar_num_votes` int(6) NOT NULL DEFAULT '0',
  `bar_votes_avg` float NOT NULL DEFAULT '0',
  `bar_votes_real_avg` float NOT NULL DEFAULT '0',
  `bar_num_comments` int(6) unsigned NOT NULL DEFAULT '0',
  `bar_negatives_votes` int(6) NOT NULL DEFAULT '0',
  `bar_randkey` int(20) NOT NULL DEFAULT '0',
  `bar_publication_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bar_edition_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bar_creation_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bar_modification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bar_id`),
  KEY `bar_author_id` (`bar_author_id`,`bar_modification_date`),
  KEY `bar_modification_date` (`bar_modification_date`),
  KEY `bar_id_town` (`bar_id`,`bar_town_id`),
  KEY `bar_id_town_zone` (`bar_id`,`bar_town_id`,`bar_zone_id`),
  KEY `bar_status_publidate` (`bar_status`,`bar_publication_date`),
  KEY `bar_status_editdate` (`bar_status`,`bar_edition_date`),
  KEY `bar_author` (`bar_author_id`,`bar_last_author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(20) NOT NULL AUTO_INCREMENT,
  `comment_type` enum('normal','admin','private','censured') CHARACTER SET utf8 NOT NULL DEFAULT 'normal',
  `comment_order` smallint(6) NOT NULL DEFAULT '0',
  `comment_randkey` int(20) NOT NULL DEFAULT '0',
  `comment_bar_id` int(20) NOT NULL DEFAULT '0',
  `comment_user_id` int(20) NOT NULL DEFAULT '0',
  `comment_user_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `comment_text` text COLLATE utf8_spanish_ci NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_modification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `comment_bar_id` (`comment_bar_id`),
  KEY `comment_bar_type` (`comment_bar_id`,`comment_type`),
  KEY `comment_bar_type_order` (`comment_bar_id`,`comment_order`,`comment_type`),
  KEY `comment_user_id` (`comment_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `log_type` enum('bar_new','bar_discard','bar_edit','bar_publish','user_new','user_edit','user_login_failed','user_recover_pass','comment_new','comment_edit','comment_delete','comment_censure','vote','spam_warn','town_new','zone_new') NOT NULL,
  `log_ref_id` int(11) unsigned NOT NULL,
  `log_user_id` int(11) NOT NULL,
  `log_ip` char(24) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_date` (`log_date`),
  KEY `log_type` (`log_type`,`log_ref_id`),
  KEY `log_type_2` (`log_type`,`log_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `photos` (
  `photo_id` int(20) NOT NULL AUTO_INCREMENT,
  `photo_small_image_name` varchar(255) NOT NULL,
  `photo_large_image_name` varchar(255) NOT NULL,
  `photo_bar_id` int(20) NOT NULL,
  `photo_randkey` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  KEY `photo_bar_id` (`photo_bar_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `specialities` (
  `speciality_id` int(10) NOT NULL AUTO_INCREMENT,
  `speciality_name` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `speciality_bar_id` int(20) NOT NULL,
  PRIMARY KEY (`speciality_id`),
  UNIQUE KEY `speciality_name_bar_id` (`speciality_name`,`speciality_bar_id`),
  KEY `speciality_bar_id` (`speciality_bar_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `towns` (
  `town_id` int(10) NOT NULL AUTO_INCREMENT,
  `town_name` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `town_province` enum('A Coruña','Álava/Araba','Albacete','Alicante','Almeria','Asturias','Ávila','Badajoz','Barcelona','Burgos','Cáceres','Cádiz','Cantabria','Castellón','Ceuta','Ciudad Real','Córdoba','Cuenca','Girona','Granada','Guadalajara','Guipúzcoa/Gipuzkoa','Huelva','Huesca','Illes Balears','Jaén','La Rioja','Las Palmas','León','Lleida','Lugo','Madrid','Málaga','Melilla','Murcia','Navarra','Ourense','Palencia','Pontevedra','Salamanca','Santa Cruz de Tenerife','Segovia','Sevilla','Soria','Tarragona','Teruel','Toledo','Valencia','Valladolid','Vizcaya/Bizkaia','Zamora','Zaragoza') CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`town_id`),
  UNIQUE KEY `town_name_province` (`town_name`,`town_province`),
  KEY `town_province` (`town_province`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

INSERT INTO `towns` (`town_name`, `town_province`) VALUES
('Madrid', 'Madrid'),
('Granada', 'Granada'),
('León', 'León'),
('Avila', 'Ávila');

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(20) NOT NULL AUTO_INCREMENT,
  `user_login` varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  `user_pass` char(64) COLLATE utf8_spanish_ci NOT NULL,
  `user_email` varchar(64) COLLATE utf8_spanish_ci NOT NULL,
  `user_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `user_url` char(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `user_level` enum('disabled','banned','normal','editor','admin') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'normal',
  `user_admin_text` text COLLATE utf8_spanish_ci,
  `user_trust` decimal(3,2) DEFAULT '1.00',
  `user_validation_date` timestamp NULL DEFAULT NULL,
  `user_creation_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_modification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  KEY `user_email` (`user_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `votes` (
  `vote_id` int(20) NOT NULL AUTO_INCREMENT,
  `vote_type` enum('bars') CHARACTER SET utf8 NOT NULL DEFAULT 'bars',
  `vote_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vote_bar_id` int(20) NOT NULL DEFAULT '0',
  `vote_user_id` int(20) NOT NULL DEFAULT '0',
  `vote_user_ip` char(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `vote_value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `vote_type` (`vote_type`,`vote_bar_id`,`vote_user_id`),
  KEY `vote_user_bar` (`vote_bar_id`,`vote_user_id`),
  KEY `vote_bar_id` (`vote_bar_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci PACK_KEYS=0 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `zones` (
  `zone_id` int(10) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `zone_map_lat` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `zone_map_lng` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `zone_map_zoom` int(2) NOT NULL DEFAULT '8',
  `zone_town_id` int(10) NOT NULL,
  PRIMARY KEY (`zone_id`),
  UNIQUE KEY `zone_name_town_id` (`zone_town_id`,`zone_name`),
  KEY `zone_town_id` (`zone_town_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;
