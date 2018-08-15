<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$multilang_database = [
    "CREATE TABLE `" . DB_PREFIX . "lang` (
  `lang_id` int(11) NOT NULL,
  `lang_name` varchar(11) NOT NULL,
  `iso_code` varchar(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";",
    
    "ALTER TABLE `" . DB_PREFIX . "lang`
  ADD PRIMARY KEY (`lang_id`),
  ADD UNIQUE KEY `lang_id` (`lang_id`);",
     
    "ALTER TABLE `" . DB_PREFIX . "lang`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT",
 
    "ALTER TABLE `" . DB_PREFIX . "lang` ADD UNIQUE(`iso_code`);",
    "ALTER TABLE `" . DB_PREFIX . "lang` ADD UNIQUE(`lang_name`);",
    
    "INSERT INTO `" . DB_PREFIX . "lang` (`lang_id`, `lang_name`, `iso_code`, `active`) 
    VALUES (1, 'Español', 'es', 1), (2, 'English', 'en', 1);",
    
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('Multilang', 'multilang_debug', '0');",
    "INSERT INTO `" . DB_PREFIX . "config` (`plugin`, `cfg_key`, `cfg_value`) VALUES ('Multilang', 'ml_set_to_visit_lang', '1');"
    
    ];

$multilang_database_uninstall = [
    "DROP TABLE `" . DB_PREFIX . "lang`",    
    "DELETE FROM `" . DB_PREFIX . "config` WHERE plugin = 'Multilang'"
];
