INSERT INTO `dhbistrocz`.`nw_operation_type` (`id`, `name`) VALUES ('14', 'vyhledávání');

ALTER TABLE `nw_symbols` 
ADD COLUMN `search_lines` INT NOT NULL DEFAULT 0 AFTER `assigned`, 
ADD COLUMN `search_curves` INT NOT NULL DEFAULT 0 AFTER `search_lines`, 
ADD COLUMN `search_points` INT NOT NULL DEFAULT 0 AFTER `search_curves`, 
ADD COLUMN `search_geometricals` INT NOT NULL DEFAULT 0 AFTER `search_points`, 
ADD COLUMN `search_alphabets` INT NOT NULL DEFAULT 0 AFTER `search_geometricals`, 
ADD COLUMN `search_specialchars` INT NOT NULL DEFAULT 0 AFTER `search_alphabets`;