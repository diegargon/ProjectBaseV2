<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

$exampleTemplate_database[] = "
CREATE TABLE `" . DB_PREFIX . "example` (
  `example_id` int(32) NOT NULL,
  `example_field` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . ";
";

$exampleTemplate_database_uninstall[] = "
DROP TABLE `" . DB_PREFIX . "example`
";       