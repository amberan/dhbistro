<?php
/**
 * ALTER COLUMN
 */
$columnAlter['symbol']['archiv'] = 'archived timestamp NULL AFTER deleted;';


$columnAlter['users']['sid'] = 'sid varchar(32) NULL AFTER id';
$columnAlter['users']['idperson'] = 'idperson int(11) NULL AFTER pwd';
$columnAlter['users']['lastlogon'] = 'lastlogon int(11) NULL AFTER idperson';
$columnAlter['users']['ip'] = 'ip varchar(50) NULL AFTER lastlogon';
$columnAlter['users']['zlobody'] = 'zlobody int(11) NOT NULL DEFAULT "0" AFTER suspended';
$columnAlter['users']['right_text'] = 'right_text int(11) NOT NULL DEFAULT "0" AFTER timeout';
$columnAlter['users']['right_power'] = 'right_power int(11) NOT NULL DEFAULT "0" AFTER right_text';
$columnAlter['users']['right_org'] = 'right_org int(11) NOT NULL DEFAULT "0" AFTER right_power';
$columnAlter['users']['right_aud'] = 'right_aud int(11) NOT NULL DEFAULT "0" AFTER right_org';
$columnAlter['users']['right_super'] = 'right_super int(11) NOT NULL DEFAULT "0" AFTER right_aud';
$columnAlter['users']['filter'] = 'filter text NULL AFTER right_super';
$columnAlter['users']['plan'] = 'plan text NULL AFTER right_super';
