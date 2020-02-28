<?php
use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);



    $latteParameters['new_password'] = randomPassword();

    //seznam osob napojenych na uzivatele
    $personLinkedSql = "SELECT ".DB_PREFIX."user.idperson FROM ".DB_PREFIX."user where idperson != 0 ORDER BY idperson";
    $personLinkedQuery = mysqli_query ($database,$personLinkedSql);
    while ($personLinkedRecord = mysqli_fetch_assoc($personLinkedQuery)) {
        $personLinked[] = $personLinkedRecord['idperson'];
    }

    //seznam osob
    $personList = personList('deleted=0 and archiv=0 and dead=0','surname');
    //odecteni napojenych od vsech
    foreach ($personList as $personList) {
        if (!in_array($personList['id'],$personLinked)) {
            $person[] = array ($personList['id'], $personList['surname'], $personList['name']);
        }
    }
    $latteParameters['person'] = $person;

    $latte->render($config['folder_templates'].'sparklet.latte', $latteParameters);
    $latte->render($config['folder_templates'].'user_add.latte', $latteParameters);
?>
