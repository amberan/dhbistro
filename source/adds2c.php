<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');


latteDrawTemplate("header");

$latteParameters['title'] = 'Prirazeni resitele k případu';
mainMenu();
sparklets('<a href="/cases/">případy</a> &raquo; <strong>úprava případu</strong> &raquo; <strong>přiřazení řešitelům</strong>');
if (is_numeric($_REQUEST['rid']) && $user['aclCase']) {
    $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."case WHERE id=".$_REQUEST['rid']);
    if ($rec = mysqli_fetch_assoc($res)) {
        ?>

<div id="obsah">
<p>
K případu můžete přiřadit řešitele.
</p>

<form action="addpersons.php" method="post" class="otherform">
<?php

    // vypis osob
    $sql = "SELECT ".DB_PREFIX."user.userId AS 'id', ".DB_PREFIX."user.userName AS 'login', ".DB_PREFIX."c2s.iduser
    FROM ".DB_PREFIX."user
    LEFT JOIN ".DB_PREFIX."c2s ON ".DB_PREFIX."c2s.idsolver=".DB_PREFIX."user.userId AND ".DB_PREFIX."c2s.idcase=".$_REQUEST['rid']."
    WHERE ".DB_PREFIX."user.userDeleted=0
    ORDER BY ".DB_PREFIX."user.userName ASC";
        $res = mysqli_query($database, $sql); ?>
<div id="in-form-table">
<?php
    if (mysqli_num_rows($res)) {
        echo '<table>
<thead>
	<tr>
	<th>#</th>
	  <th>Uživatel</th>
	</tr>
</thead>
<tbody>
';
        $even = 0;
        while ($rec = mysqli_fetch_assoc($res)) {
            echo '<tr class="'.(($even % 2 == 0) ? 'even' : 'odd').'"><td><input type="checkbox" name="solver[]" value="'.$rec['id'].'" class="checkbox"'.(($rec['iduser']) ? ' checked="checked"' : '').' /></td>';
            echo '<td>'.StripSlashes($rec['login'].' ').'</td></tr>';
            $even++;
        }
        echo '</tbody>
</table>';
    } ?>
<input type="hidden" name="caseid" value="<?php echo $_REQUEST['rid']; ?>" />
<input id="button-floating-uloz" type="submit" value="Uložit změny" name="addsolver" class="submitbutton" title="Uložit změny"/>
</div>
<!-- end of #obsah -->
</form>

</div>
<!-- end of #obsah -->
<?php
    } else {
        echo '<div id="obsah"><p>Případ neexistuje. Rid='.$_REQUEST['rid'].'</p></div>';
    }
} else {
    echo '<div id="obsah"><p>Tohle nezkoušejte.</p></div>';
}
latteDrawTemplate("footer");
?>
