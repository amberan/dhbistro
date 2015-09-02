ALTER TABLE  `nw_symbols` ADD  `secret` INT NOT NULL;
ALTER TABLE  `nw_symbols` ENGINE = MYISAM;
ALTER TABLE `nw_symbols` ADD fulltext (`desc`);
ALTER TABLE  `nw_groups` ADD  `archived` INT NOT NULL;
