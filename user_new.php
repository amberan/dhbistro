<?php 
    $latteParameters['new_password'] = randomPassword();
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'user_add.latte', $latteParameters);
?>
