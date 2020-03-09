<?php

// *** RENAME TABLE
$tableRename['loggedin'] = "loggedin_deleted";
$tableRename['map'] = "map_deleted";
$tableRename['backups'] = "backup";
$tableRename['cases'] = "case";
$tableRename['data'] = "file";
$tableRename['groups'] = "group";
$tableRename['notes'] = "note";
$tableRename['persons'] = "person";
$tableRename['reports'] = "report";
$tableRename['symbols'] = "symbol";
$tableRename['tasks'] = "task";
$tableRename['users'] = "user";

// *** ADD COLUMN
$columnAdd['backup']['version'] = "varchar(50) NOT NULL DEFAULT '".$config['version']." =<'";
$columnAdd['user']['sid'] = "VARCHAR(32) NOT NULL AFTER `id`";
$columnAdd['user']['user_agent'] = "VARCHAR(256) NULL AFTER `ip`";
$columnAdd['user']['suspended'] = "INT NOT NULL DEFAULT '0' AFTER `deleted`";
$columnAdd['news']['deleted'] = "INT(3) NOT NULL DEFAULT '0' AFTER `kategorie`";
$columnAdd['case']['contents_md'] = "TEXT NULL";
$columnAdd['dashboard']['content_md'] = "TEXT NULL";
$columnAdd['group']['contents_md'] = "TEXT NULL";
$columnAdd['news']['obsah_md'] = "TEXT NULL";
$columnAdd['note']['note_md'] = "TEXT NULL";
$columnAdd['person']['contents_md'] = "TEXT NULL";
$columnAdd['report']['summary_md'] = "TEXT NULL";
$columnAdd['report']['impacts_md'] = "TEXT NULL";
$columnAdd['report']['details_md'] = "TEXT NULL";
$columnAdd['report']['energy_md'] = "TEXT NULL";
$columnAdd['report']['inputs_md'] = "TEXT NULL";
$columnAdd['symbol']['desc_md'] = "TEXT NULL";
$columnAdd['user']['plan_md'] = "TEXT NULL";
$columnAdd['task']['task_md'] = "TEXT NULL";

// *** ADD FULLTEXT INDEX
$columnAddFulltext['case'] = ['contents_md'];
$columnAddFulltext['dashboard'] = ['content_md'];
$columnAddFulltext['group'] = ['contents_md'];
$columnAddFulltext['news'] = ['obsah_md'];
$columnAddFulltext['note'] = ['note_md'];
$columnAddFulltext['person'] = ['contents_md'];
$columnAddFulltext['report'] = ['summary_md', 'impacts_md', 'details_md', 'energy_md', 'inputs_md'];
$columnAddFulltext['symbol'] = ['desc_md'];

// *** ALTER COLUMN
$columnAlter['user']['sid'] = " `sid` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `id`";
$columnAlter['user']['idperson'] = " `idperson` int(11) NULL AFTER `pwd`";
$columnAlter['user']['lastlogon'] = " `lastlogon` int(11) NULL AFTER `idperson`";
$columnAlter['user']['ip'] = " `ip` varchar(50) COLLATE 'utf8_general_ci' NULL AFTER `lastlogon`";
$columnAlter['user']['user_agent'] = " `user_agent` varchar(256) COLLATE 'utf8_general_ci' NULL AFTER `ip`";
$columnAlter['user']['deleted'] = "deleted int(3) NOT NULL DEFAULT '0'";
$columnAlter['user']['suspended'] = " `suspended` int(11) NOT NULL DEFAULT '0' AFTER `deleted`";
$columnAlter['user']['zlobody'] = " `zlobody` int(11) NOT NULL DEFAULT '0' AFTER `suspended`";
$columnAlter['user']['right_text'] = " `right_text` int(11) NOT NULL DEFAULT '0' AFTER `timeout`";
$columnAlter['user']['right_power'] = " `right_power` int(11) NOT NULL DEFAULT '0' AFTER `right_text`";
$columnAlter['user']['right_org'] = " `right_org` int(11) NOT NULL DEFAULT '0' AFTER `right_power`";
$columnAlter['user']['right_aud'] = " `right_aud` int(11) NOT NULL DEFAULT '0' AFTER `right_org`";
$columnAlter['user']['right_super'] = " `right_super` int(11) NOT NULL DEFAULT '0' AFTER `right_aud`";
$columnAlter['user']['filter'] = " `filter` text COLLATE 'utf8_general_ci' NULL AFTER `right_super`";
$columnAlter['user']['plan'] = " `plan` text COLLATE 'utf8_general_ci' NULL AFTER `right_super`";

// *** CONVERT TO MD
$columnToMD[] = ['user', 'id', 'plan', 'plan_md'];
$columnToMD[] = ['dashboard', 'id', 'content', 'content_md'];
$columnToMD[] = ['news', 'id', 'obsah', 'obsah_md'];

// *** DROP TABLE
$tableDrop[] = 'loggedin_deleted';
$tableDrop[] = 'map_deleted';

use Tracy\Debugger;
Debugger::enable(Debugger::PRODUCTION,$config['folder_logs']);
require_once ( 'update-function.php');

$counterTableRename = bistroDBTableRename($tableRename);
$counterColumnAlter = bistroDBColumnAlter($columnAlter);
$counterColumnAdd = bistroDBColumnAdd($columnAdd);
$counterPasswordEncrypt = bistroDBPasswordEncrypt();
$counterColumnMarkdown = bistroDBColumnMarkdown($columnToMD);
$counterFulltextAdd = bistroDBFulltextAdd($columnAddFulltext);
$counterTableDrop = bistroDBTableDrop($tableDrop);

//pokud zmeny probehly, prejmenovat tento soubor
if ($counterColumnAdd + $counterColumnAlter + $counterColumnMarkdown + $counterFulltextAdd + $counterPasswordEncrypt + $counterTableRename + $counterTableDrop > 0) {
    rename(__FILE__,__FILE__.".old");
}

?>
