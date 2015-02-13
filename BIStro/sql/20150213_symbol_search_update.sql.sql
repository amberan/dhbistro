INSERT INTO `dhbistrocz`.`nw_operation_type` (`id`, `name`) VALUES ('14', 'vyhledávání');

ALTER TABLE `nw_symbols` 
ADD `search_lines` INT NOT NULL DEFAULT \'0\' AFTER `assigned`, 
ADD `search_curves` INT NOT NULL DEFAULT \'0\' AFTER `search_lines`, 
ADD `search_points` INT NOT NULL DEFAULT \'0\' AFTER `search_curves`, 
ADD `search_geometricals` INT NOT NULL DEFAULT \'0\' AFTER `search_points`, 
ADD `search_alphabets` INT NOT NULL DEFAULT \'0\' AFTER `search_geometricals`, 
ADD `search_specialchars` INT NOT NULL DEFAULT \'0\' AFTER `search_alphabets`;