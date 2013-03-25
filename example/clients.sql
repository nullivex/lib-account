CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `primary_contact_id` int(10) unsigned DEFAULT NULL,
  `password` varchar(60) COLLATE ascii_bin NOT NULL,
  `company` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` int(10) DEFAULT NULL,
  `created` int(10) NOT NULL,
  `deleted` int(10) DEFAULT NULL,
  PRIMARY KEY (`client_id`),
  KEY `primary_contact_id` (`primary_contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin AUTO_INCREMENT=1 ;
