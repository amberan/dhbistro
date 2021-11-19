<?php
/*
 * ADD COLUMN
 */
$columnAdd['user']['aclBoard'] = "int(3) NOT NULL DEFAULT '0' AFTER aclUser";
$columnAdd['user']['aclNews'] = "int(3) NOT NULL DEFAULT '0' AFTER aclUser";
/*
 * RIGHTS TO UPDATE
 */
$rightsToUpdate['aclDeputy'] = ['aclNews', 'aclBoard'];
