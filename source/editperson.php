<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/func_main.php';

latteDrawTemplate("header");

$latteParameters['title'] = 'Zobrazení symbolu';

if (is_numeric($_REQUEST['rid']) && $user['aclPerson']) {
    $res = mysqli_query($database, "SELECT * FROM " . DB_PREFIX . "person WHERE id=" . $_REQUEST['rid']);
    if ($rec_p = mysqli_fetch_assoc($res)) {
        if (($rec_p['secret'] > $user['aclSecret']) || $rec_p['deleted'] == 1) {
            unauthorizedAccess(1, 1, $_REQUEST['rid']);
        }
        authorizedAccess(1, 1, $_REQUEST['rid']);
        $latteParameters['title'] = 'Úprava osoby';
        mainMenu();
        sparklets('<a href="/persons/">osoby</a> &raquo; <strong>úprava osoby</strong>'); ?>
<div id="obsah">
    <form action="/persons/" method="post" id="inputform" enctype="multipart/form-data">
<?php if ($user['aclGamemaster'] == 1) {
    $sql = 'SELECT ' . DB_PREFIX . 'person.name, ' . DB_PREFIX . 'person.surname, ' . DB_PREFIX . 'user.userName , ' . DB_PREFIX . 'user.userId
                    FROM ' . DB_PREFIX . 'user
                    JOIN ' . DB_PREFIX . 'person ON ' . DB_PREFIX . 'user.personId = ' . DB_PREFIX . 'person.id
                    ORDER BY ' . DB_PREFIX . 'user.userName ASC';
    $res = mysqli_query($database, $sql); ?>
    <fieldset><legend><strong>Organizační úprava osoby</strong></legend>
            <div id="info">
                <div class="clear">&nbsp;</div>
                <div>
                  <h3><label for="rdatum">Vytvořeno:</label></h3>
                  </div>
                <?php echo date_picker("rdatum", 1970, null, $rec_p['regdate']); //org?>
                <div class="clear">&nbsp;</div>
                <div>
                <h3><label for="regusr">Vytvořil:</label></h3>
                <select name="regusr" id="regusr">
<?php while ($rec = mysqli_fetch_assoc($res)) {
    echo '<option value="' . $rec['userId'] . '" ' . ($rec['userId'] == $rec_p['regid'] ? ' selected' : '') . '>' . stripslashes($rec['userName']) . '       -      ' . stripslashes($rec['surname']) . ', ' . stripslashes($rec['name']) . '</option>';
} ?>
                </select>
                </div>
                <div class="clear">&nbsp;</div>
                  <div>
                  <h3><label for="notnew">organizační změna: <br> (není nové)</label></h3>
                  <input type=checkbox name=notnew checked >
                  </div>

            </div>
        </fieldset>
<?php
} ?>
    <fieldset id="ramecek"><legend><strong>Úprava osoby: <?php echo stripslashes($rec_p['surname']) . ', ' . stripslashes($rec_p['name']); ?></strong></legend>
    <p id="top-text">Portréty nahrávejte pokud možno ve velikosti 100x130 bodů, symboly ve velikosti 100x100 bodů, budou se sice zvětšovat a zmenšovat na jeden z těch rozměrů, nebo oba, pokud bude správný poměr stran, ale chceme snad mít hezkou databázi. A nahrávejte opravdu jen portréty, o rozmazané postavy nebude nouze v přílohách. Symboly rovněž nahrávejte jasně rozeznatelné.</p>
        <fieldset><legend><strong>Základní údaje</strong></legend>
        <?php if ($rec_p['portrait'] == null) { ?><img src="#" alt="portrét chybí" title="portrét chybí" id="portraitimg" class="noname"/>
        <?php } else { ?><img  loading="lazy" src="file/portrait/<?php echo $_REQUEST['rid']; ?>" alt="<?php echo stripslashes($rec_p['name']) . ' ' . stripslashes($rec_p['surname']); ?>" id="portraitimg" />
        <?php } ?>
        <?php if ($rec_p['symbol'] == null) { ?><img src="#" alt="symbol chybí" title="symbol chybí" id="symbolimg" class="noname"/>
        <?php } else { ?><a href="readsymbol.php?rid=<?php echo $rec_p['symbol']; ?>"><img  loading="lazy" src="file/symbol/<?php echo $rec_p['symbol']; ?>" alt="<?php echo stripslashes($rec_p['name']) . ' ' . stripslashes($rec_p['surname']); ?>" id="symbolimg" /></a>
        <?php } ?>
        <?php if ($rec_p['symbol'] == null) { ?>
        <?php } else { ?><span class="info-delete-symbol"><a class="delete" title="odpojit" href="/persons/?deletesymbol=<?php echo $rec_p['symbol']; ?>&amp;personid=<?php echo $_REQUEST['rid']; ?>&amp;backurl=<?php echo urlencode('editperson.php?rid=' . $_REQUEST['rid']); ?>" onclick="return confirm('Opravdu odpojit symbol?')"><span class="button-text">smazat soubor</span></a></span>
        <?php } ?>
            <div id="info">
                <h3><label for="name">Jméno:</label></h3>
                <input type="text" name="name" id="name" value="<?php echo stripslashes($rec_p['name']); ?>" />
                <div class="clear">&nbsp;</div>
                <h3><label for="surname">Příjmení:</label></h3>
                <input type="text" name="surname" id="surname" value="<?php echo stripslashes($rec_p['surname']); ?>" />
                <div class="clear">&nbsp;</div>
                <h3><label for="side">Strana:</label></h3>
                    <select name="side" id="side">
                        <option value="0"<?php if ($rec_p['side'] == 0) {
                            echo ' selected="selected"';
                        } ?>>neznámá</option>
                        <option value="1"<?php if ($rec_p['side'] == 1) {
                            echo ' selected="selected"';
                        } ?>>světlo</option>
                        <option value="2"<?php if ($rec_p['side'] == 2) {
                            echo ' selected="selected"';
                        } ?>>tma</option>
                        <option value="3"<?php if ($rec_p['side'] == 3) {
                            echo ' selected="selected"';
                        } ?>>člověk</option>
                    </select>
                <div class="clear">&nbsp;</div>
                <h3><label for="power">Síla:</label></h3>
                    <select name="power" id="power">
                        <option value="0"<?php if ($rec_p['power'] == 0) {
                            echo ' selected="selected"';
                        } ?>>neznámá</option>
                        <option value="1"<?php if ($rec_p['power'] == 1) {
                            echo ' selected="selected"';
                        } ?>>1. kategorie</option>
                        <option value="2"<?php if ($rec_p['power'] == 2) {
                            echo ' selected="selected"';
                        } ?>>2. kategorie</option>
                        <option value="3"<?php if ($rec_p['power'] == 3) {
                            echo ' selected="selected"';
                        } ?>>3. kategorie</option>
                        <option value="4"<?php if ($rec_p['power'] == 4) {
                            echo ' selected="selected"';
                        } ?>>4. kategorie</option>
                        <option value="5"<?php if ($rec_p['power'] == 5) {
                            echo ' selected="selected"';
                        } ?>>5. kategorie</option>
                        <option value="6"<?php if ($rec_p['power'] == 6) {
                            echo ' selected="selected"';
                        } ?>>6. kategorie</option>
                        <option value="7"<?php if ($rec_p['power'] == 7) {
                            echo ' selected="selected"';
                        } ?>>7. kategorie</option>
                        <option value="8"<?php if ($rec_p['power'] == 8) {
                            echo ' selected="selected"';
                        } ?>>mimo kategorie</option>
                    </select>
                <div class="clear">&nbsp;</div>
                <h3><label for="spec">Specializace:</label></h3>
                    <select name="spec" id="spec">
                        <option value="0"<?php if ($rec_p['spec'] == 0) {
                            echo ' selected="selected"';
                        } ?>>neznámá</option>
                        <option value="1"<?php if ($rec_p['spec'] == 1) {
                            echo ' selected="selected"';
                        } ?>>bílý mág</option>
                        <option value="2"<?php if ($rec_p['spec'] == 2) {
                            echo ' selected="selected"';
                        } ?>>černý mág</option>
                        <option value="3"<?php if ($rec_p['spec'] == 3) {
                            echo ' selected="selected"';
                        } ?>>léčitel</option>
                        <option value="4"<?php if ($rec_p['spec'] == 4) {
                            echo ' selected="selected"';
                        } ?>>obrateň</option>
                        <option value="5"<?php if ($rec_p['spec'] == 5) {
                            echo ' selected="selected"';
                        } ?>>upír</option>
                        <option value="6"<?php if ($rec_p['spec'] == 6) {
                            echo ' selected="selected"';
                        } ?>>vlkodlak</option>
                        <option value="7"<?php if ($rec_p['spec'] == 7) {
                            echo ' selected="selected"';
                        } ?>>vědma</option>
                        <option value="8"<?php if ($rec_p['spec'] == 8) {
                            echo ' selected="selected"';
                        } ?>>zaříkávač</option>
                        <option value="9"<?php if ($rec_p['spec'] == 9) {
                            echo ' selected="selected"';
                        } ?>>vykladač</option>
                        <option value="10"<?php if ($rec_p['spec'] == 10) {
                            echo ' selected="selected"';
                        } ?>>jasnovidec</option>
                    </select>
                <div class="clear">&nbsp;</div>
                <h3><label for="phone">Telefon:</label></h3>
                <input type="text" name="phone" id="phone" value="<?php echo stripslashes($rec_p['phone']); ?>" />
                <div class="clear">&nbsp;</div>
                <h3><label for="portrait">Nový&nbsp;portrét:</label></h3>
                <input type="file" name="portrait" id="portrait" />
                <div class="clear">&nbsp;</div>
                <h3><label for="symbol">Nový&nbsp;symbol:</label></h3>
                <input type="file" name="symbol" id="symbol" />
                <div class="clear">&nbsp;</div>
                <h3><label for="secret">Stupeň utajení:</label></h3>
                    <select name="secret" id="secret">
                        <option value="0"<?php if ($rec_p['secret'] == 0) {
                            echo ' selected="selected"';
                        } ?>>0</option>
                        <option value="1"<?php if ($rec_p['secret'] == 1) {
                            echo ' selected="selected"';
                        } ?>>1</option>
                    </select>
                <div class="clear">&nbsp;</div>
                <h3><label for="dead">Mrtvá:</label></h3>
                    <input type="checkbox" name="dead" value=1 <?php if ($rec_p['dead'] == 1) { ?>checked="checked"<?php } ?>/><br/>
                <div class="clear">&nbsp;</div>
                <h3><label for="archiv">Archiv:</label></h3>
                    <input type="checkbox" name="archiv" value=1 <?php if ($rec_p['archived'] > 1) { ?>checked="checked"<?php } ?>/> <?php if ($rec_p['archived'] > 1) {
                        echo $rec_p['archived'];
                    } ?><br/>
                <div class="clear">&nbsp;</div>
                <h3><label for="personRoof">Strop:</label></h3>
                    <input type="checkbox" name="personRoof" <?php if ($rec_p['roof'] > null) {
                        echo "CHECKED";
                    } ?>/>   <?php if ($rec_p['roof'] > 1) {
                        echo $rec_p['roof'];
                    } ?></br>
                <div class="clear">&nbsp;</div>
            </div>
            <!-- end of #info -->
        </fieldset>
        <!-- náseduje popis osoby -->
        <fieldset><legend><strong>Popis osoby</strong></legend>
            <div class="field-text">
                <textarea cols="80" rows="30" name="contents" id="contents"><?php echo stripslashes($rec_p['contents']); ?></textarea>
            </div>
            <!-- end of .field-text -->
        </fieldset>
        <input type="hidden" name="personid" value="<?php echo $rec_p['id']; ?>" />
        <input type="submit" name="editperson" id="submitbutton" value="Uložit" title="Uložit změny"/>
    </form>

</fieldset>

    <div id="change-groups" class="otherform-wrap">
        <fieldset><legend><strong>Přiřazení skupiny</strong></legend>
        <p>Osobě můžete přiřadit skupiny, do kterých patří. Opačnou akci lze provést u skupiny, kde přiřazujete pro změnu osoby dané skupině. Akce jsou si rovnocenné a je tedy nutná pouze jedna z nich.</p>
        <form action="/persons/" method="post" class="otherform">
        <?php
           $sql = "SELECT " . DB_PREFIX . "group.secret AS 'secret', " . DB_PREFIX . "group.title AS 'title', " . DB_PREFIX . "group.id AS 'id', " . DB_PREFIX . "g2p.iduser
           FROM " . DB_PREFIX . "group
           LEFT JOIN " . DB_PREFIX . "g2p ON " . DB_PREFIX . "g2p.idgroup=" . DB_PREFIX . "group.id AND " . DB_PREFIX . "g2p.idperson=" . $_REQUEST['rid'] . "
           WHERE " . DB_PREFIX . "group.deleted in (0," . $user['aclRoot'] . ") AND " . DB_PREFIX . "group.secret<=" . $user['aclSecret'] . "
           ORDER BY " . DB_PREFIX . "group.title ASC";
        if ($user['aclPerson'] > 0 && $user['aclGroup'] > 0) {
            $res = mysqli_query($database, $sql);
            while ($rec = mysqli_fetch_assoc($res)) {
                echo '<div>
                    <input type="checkbox" name="group[]" value="' . $rec['id'] . '" class="checkbox"' . ($rec['iduser'] ? ' checked="checked"' : '') . ' />
                    <label>' . stripslashes($rec['title']) . '</label>
                </div>';
            }
        } ?>
            <div>
                <input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
                <input type="submit" value="Uložit změny" name="setgroups" class="submitbutton"  title="Uložit"/>
            </div>
        </form>
        </fieldset>
    </div>
    <!-- end of #change-groups .otherform-wrap -->

    <!-- následuje seznam přiložených souborů -->
    <fieldset><legend><strong>Přiložené soubory</strong></legend>
        <strong><em>K osobě je možné nahrát neomezené množství souborů, ale velikost jednoho souboru je omezena na 2 MB.</em></strong>
        <?php //generování seznamu přiložených souborů
                $sqlFilter = DB_PREFIX . "file.secret<=" . $user['aclSecret'];

        $sql = "SELECT " . DB_PREFIX . "file.iduser AS 'iduser', " . DB_PREFIX . "file.originalname AS 'title', " . DB_PREFIX . "file.secret AS 'secret', " . DB_PREFIX . "file.id AS 'id'
                FROM " . DB_PREFIX . "file
                WHERE $sqlFilter AND " . DB_PREFIX . "file.iditem=" . $_REQUEST['rid'] . " AND " . DB_PREFIX . "file.idtable=1
                ORDER BY " . DB_PREFIX . "file.originalname ASC";
        $res = mysqli_query($database, $sql);
        $i = 0;
        while ($rec_f = mysqli_fetch_assoc($res)) {
            $i++;
            if ($i == 1) { ?>
        <ul id="prilozenadata">
                <?php } ?>
            <li class="soubor"><a href="file/attachement/<?php echo $rec_f['id']; ?>" title=""><?php echo stripslashes($rec_f['title']); ?></a><?php if ($rec_f['secret'] == 1) { ?> (TAJNÝ)<?php } ?><span class="poznamka-edit-buttons"><?php
            if (($rec_f['iduser'] == $user['userId']) || ($user['aclPerson'] > 1)) {
                echo '<a class="delete" title="smazat" href="/persons/?deletefile=' . $rec_f['id'] . '&amp;personid=' . $_REQUEST['rid'] . '&amp;backurl=' . urlencode('editperson.php?rid=' . $_REQUEST['rid']) . '" onclick="return confirm(\'Opravdu odebrat soubor &quot;' . stripslashes($rec_f['title']) . '&quot; náležící k osobě?\')"><span class="button-text">smazat soubor</span></a>';
            } ?>
                </span></li><?php
        }
        if ($i != 0) { ?>
        </ul>
        <!-- end of #prilozenadata -->
        <?php
        } else {?><br />
        <em>bez přiložených souborů</em><?php
        }
        // konec seznamu přiložených souborů?>
    </fieldset>

    <div id="new-file" class="otherform-wrap">
        <fieldset><legend><strong>Nový soubor</strong></legend>
        <form action="/persons/" method="post" enctype="multipart/form-data" class="otherform">
            <div>
                <strong><label for="attachment">Soubor:</label></strong>
                <input type="file" name="attachment" id="attachment" />
            </div>
            <div>
                <strong><label for="usecret">Přísně tajné:</label></strong>
                  <?php if ($rec_p['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" value="0" checked="checked"/>ne&nbsp;/<?php } ?>
                &nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_p['secret'] == 1) { ?>checked="checked"<?php } ?>/>ano
            </div>
<?php if ($user['aclGamemaster'] == 1) {
    echo '
                <div>
                <strong><label for="fnotnew">Není nové</label></strong>
                    <input type="checkbox" name="fnotnew" checked/><br/>
                </div>';
} ?>
            <div>
                <input type="hidden" name="personid" value="<?php echo $_REQUEST['rid']; ?>" />
                <input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid=' . $_REQUEST['rid']; ?>" />
                <input type="submit" name="uploadfile" value="Nahrát soubor k osobě" class="submitbutton" title="Uložit"/>
            </div>
        </form>
        </fieldset>
    </div>
    <!-- end of #new-file .otherform-wrap -->

    <fieldset><legend><strong>Poznámky</strong></legend>
        <span class="poznamka-edit-buttons"><a class="new" href="newnote.php?rid=<?php echo $_REQUEST['rid']; ?>&amp;idtable=1&amp;s=<?php echo $rec_p['secret']; ?>" title="nová poznámka"><span class="button-text">nová poznámka</span></a><em style="font-size:smaller;"> (K případu si můžete připsat kolik chcete poznámek.)</em></span><br/><br/>
        <!-- následuje seznam poznámek -->
        <?php // generování poznámek
    $sqlFilter = DB_PREFIX . "note.deleted in (0," . $user['aclRoot'] . ") AND (" . DB_PREFIX . "note.secret<=" . $user['aclSecret'] . ' OR ' . DB_PREFIX . 'note.iduser=' . $user['userId'] . ' )';
        $sql = "SELECT " . DB_PREFIX . "note.iduser AS 'iduser', " . DB_PREFIX . "note.title AS 'title', " . DB_PREFIX . "note.note AS 'note', " . DB_PREFIX . "note.secret AS 'secret', " . DB_PREFIX . "user.userName AS 'user', " . DB_PREFIX . "note.id AS 'id'
                FROM " . DB_PREFIX . "note, " . DB_PREFIX . "user
                WHERE $sqlFilter AND " . DB_PREFIX . "note.iduser=" . DB_PREFIX . "user.userId AND " . DB_PREFIX . "note.iditem=" . $_REQUEST['rid'] . " AND " . DB_PREFIX . "note.idtable=1
                ORDER BY " . DB_PREFIX . "note.datum DESC";
        $res = mysqli_query($database, $sql);
        $i = 0;
        while ($rec_n = mysqli_fetch_assoc($res)) {
            $i++;
            if ($i == 1) { ?>
        <div id="poznamky"><?php
            }
            if ($i > 1) {?>
            <hr /><?php
            } ?>
            <div class="poznamka">
                <h4><?php echo stripslashes($rec_n['title']) . ' - ' . stripslashes($rec_n['user']);
            if ($rec_n['secret'] == 0) {
                echo ' (veřejná)';
            }
            if ($rec_n['secret'] == 1) {
                echo ' (tajná)';
            }
            if ($rec_n['secret'] == 2) {
                echo ' (soukromá)';
            } ?></h4>
                <div><?php echo stripslashes($rec_n['note']); ?></div>
                <span class="poznamka-edit-buttons"><?php
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclPerson'] > 0)) {
                echo '<a class="edit" href="editnote.php?rid=' . $rec_n['id'] . '&amp;itemid=' . $_REQUEST['rid'] . '&amp;idtable=1" title="upravit"><span class="button-text">upravit</span></a> ';
            }
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclPerson'] > 1)) {
                echo '<a class="delete" href="procnote.php?deletenote=' . $rec_n['id'] . '&amp;itemid=' . $_REQUEST['rid'] . '&amp;backurl=' . urlencode('editperson.php?rid=' . $_REQUEST['rid']) . '" onclick="' . "return confirm('Opravdu smazat poznámku &quot;" . stripslashes($rec_n['title']) . "&quot; náležící k osobě?');" . '" title="smazat"><span class="button-text">smazat</span></a>';
            } ?>
                </span>
            </div>
            <!-- end of .poznamka -->
        <?php
        }
        if ($i != 0) { ?>
        </div>
        <!-- end of #poznamky -->
        <?php
        } else {?><br />
        <em>bez poznámek</em><?php
        }
        // konec poznámek?>
    </fieldset>

    <div id="new-note" class="otherform-wrap">
        <fieldset><legend><strong>Nová poznámka</strong></legend>
        <form action="procnote.php" method="post" class="otherform">
            <div>
                <strong><label for="notetitle">Nadpis:</label></strong>
                <input type="text" name="title" id="notetitle" />
            </div>
            <div>
              <strong><label for="nsecret">Utajení:</label></strong>
                  <?php if ($rec_p['secret'] != 1) { ?>&nbsp;<input type="radio" name="secret" id="nsecret" value="0" checked="checked"/>veřejná&nbsp;/<?php } ?>
                &nbsp;<input type="radio" name="secret" value="1" <?php if ($rec_p['secret'] == 1) { ?>checked="checked"<?php } ?>/>tajná&nbsp;/
                &nbsp;<input type="radio" name="secret" value="2" />soukromá
            </div>
<?php if ($user['aclGamemaster'] == 1) {
    echo '
                <div>
                <strong><label for="nnotnew">Není nové</label></strong>
                    <input type="checkbox" name="nnotnew" checked/><br/>
                </div>';
} ?>
            <div>
                <!--  label for="notebody">Tělo poznámka:</label -->
                <textarea cols="80" rows="7" name="note" id="notebody"></textarea>
            </div>
            <div>
                <input type="hidden" name="itemid" value="<?php echo $_REQUEST['rid']; ?>" />
                <input type="hidden" name="backurl" value="<?php echo 'editperson.php?rid=' . $_REQUEST['rid']; ?>" />
                <input type="hidden" name="tableid" value="1" />
                <input type="submit" value="Uložit poznámku" name="setnote" class="submitbutton" title="Uložit"/>
            </div>
        </form>
        </fieldset>
    </div>
    <!-- end of #new-note .otherform-wrap -->
</div>
<!-- end of #obsah -->
<?php
    } else {
        $_SESSION['message'] = "Osoba neexistuje!";
        header('location: index.php');
    }
} else {
    $_SESSION['message'] = $text['notificationHttp401'];
    header('location: index.php');
}
latteDrawTemplate("footer");
?>
