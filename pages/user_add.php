<?php

use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

    $latteParameters['new_password'] = randomPassword();

    //seznam osob napojenych na uzivatele
    $personLinkedSql = "SELECT ".DB_PREFIX."user.personId FROM ".DB_PREFIX."user where personId != 0 ORDER BY personId";
    $personLinkedQuery = mysqli_query($database, $personLinkedSql);
    while ($personLinkedRecord = mysqli_fetch_assoc($personLinkedQuery)) {
        $personLinked[] = $personLinkedRecord['personId'];
    }

    //seznam osob
    $personList = personList('deleted=0 and  (archived is null OR archived  < from_unixtime(1))  and dead=0', 'surname');
    //odecteni napojenych od vsech
    foreach ($personList as $personList) {
        if (!in_array($personList['id'], $personLinked, true)) {
            $person[] = [$personList['id'], $personList['surname'], $personList['name']];
        }
    }
    $latteParameters['person'] = $person;

latteDrawTemplate('sparklet');
latteDrawTemplate('user_add');
