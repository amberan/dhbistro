<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php';
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);
latteDrawTemplate("header");



    if (is_numeric($_REQUEST['rid'])) {
        $res = mysqli_query($database, "SELECT * FROM ".DB_PREFIX."group WHERE id=".$_REQUEST['rid']);
        if ($rec_g = mysqli_fetch_assoc($res)) {
            if (($rec_g['secret'] > $user['aclSecret']) || $rec_g['deleted'] == 1) {
                unauthorizedAccess(2, $rec_g['secret'], $rec_g['deleted'], $_REQUEST['rid']);
            }
            auditTrail(2, 1, $_REQUEST['rid']);

            $latteParameters['title'] = stripslashes($rec_g['title']);

            mainMenu();
            if ($usrinfo['right_text']) {
                $editbutton = ' <a href="editgroup.php?rid='.$_GET['rid'].'">upravit skupinu</a>';
            } else {
                $editbutton = '';
            }
            deleteUnread(2, $_REQUEST['rid']);
            sparklets('<a href="./groups/">skupiny</a> &raquo; <strong>'.stripslashes($rec_g['title']).'</strong>, '.$editbutton);


            //FILTER
            if (isset($_GET['sort'])) {
                sortingSet('group-member', $_GET['sort'], 'person');
            }
            if (isset($_POST['filter'])) {
                filterSet('group-member', @$_POST['filter']);
            }
            $filter = filterGet('group-member');


            $sqlFilter = DB_PREFIX."group.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."group.secret<=".$user['aclSecret'];



            echo '<div id="filter-wrapper">
                <form action="readgroup.php?rid='.$_GET['rid'].'" method="POST" id="filter" class="header-form-wrapper">
                    <input type="hidden" name="filter[placeholder]" />
                    <div class="header-switch">
                        <label class="toggle-control">
                            <input type="checkbox" name="filter[portrait]"';
            if (isset($filter['portrait']) and $filter['portrait'] == 'on') {
                echo ' checked ';
            }
            echo 'onchange="this.form.submit()" />
                            <span class="control"></span>
                        </label>'.$text['portrety'].'</div>
                    <div class="header-switch">
                        <label class="toggle-control">
                            <input type="checkbox" name="filter[notes]"';
            if (isset($filter['notes']) and $filter['notes'] == 'on') {
                echo ' checked ';
            }
            echo 'onchange="this.form.submit()" />
                            <span class="control"></span>
                        </label>'.$text['poznamky'].'</div>

                        </form>
</div>'; ?>




<div id="obsah">
    <h1><?php echo stripslashes($rec_g['title']); ?></h1>
    <fieldset>
        <legend><strong>Obecné informace</strong></legend>
        <div id="info"><?php
        if ($rec_g['secret'] == 1) { ?>
            <h2>TAJNÉ</h2><?php }
            if ($rec_g['archived'] == 1) { ?>
            <h2>ARCHIV</h2><?php }
            if ($rec_g['deleted'] == 1) { ?>
            <h2>SMAZANÝ ZÁZNAM</h2><?php } ?>
            <h3>Členové: </h3>
            <p><?php
            $sqlFilter = DB_PREFIX."person.deleted in (0,".$user['aclRoot'].") AND ".DB_PREFIX."person.secret<=".$user['aclSecret'];
            $sql = "SELECT ".DB_PREFIX."person.phone AS 'phone', ".DB_PREFIX."person.secret AS 'secret', ".DB_PREFIX."person.name AS 'name', ".DB_PREFIX."person.surname AS 'surname', ".DB_PREFIX."person.id AS 'id', ".DB_PREFIX."g2p.iduser
            FROM ".DB_PREFIX."person, ".DB_PREFIX."g2p
            WHERE $sqlFilter AND ".DB_PREFIX."g2p.idperson=".DB_PREFIX."person.id AND ".DB_PREFIX."g2p.idgroup=".$_REQUEST['rid']." ".sortingGet('group-member', 'person');

            // .$sqlFilter.sortingGet('group');
            $res = mysqli_query($database, $sql);
            if (mysqli_num_rows($res)) {
                echo '<div id=""><!-- je treba dostylovat -->
<table>
<thead>
	<tr>
'.((isset($filter['portrait']) and $filter['portrait'] == 'on') ? '<th>Portrét</th>' : '').'
	  <th>Jméno <a href="readgroup.php?rid='.$_GET['rid'].'&sort=surname">&#8661;</a></th>
	  <th>Telefon</th>
	</tr>
</thead>
<tbody>
';
                $even = 0;
                while ($rec = mysqli_fetch_assoc($res)) {
                    echo '<tr class="'.($even % 2 == 0 ? 'even' : 'odd').'">
'.((isset($filter['portrait']) and $filter['portrait'] == 'on') ? '<td><img src="file/portrait/'.$rec['id'].'" alt="portrét chybí" /></td>' : '').'
	<td>'.($rec['secret'] ? '<span class="secret"><a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a></span>' : '<a href="readperson.php?rid='.$rec['id'].'&amp;hidenotes=0">'.implode(', ', [stripslashes($rec['surname']), stripslashes($rec['name'])]).'</a>').'</td>
	<td><a href="tel:'.str_replace(' ', '', $rec['phone']).'">'.$rec['phone'].'</a></td>
</tr>';
                    $even++;
                }
                echo '</tbody>
</table>
</div>';
            } else { ?>
                <em>Do skupiny nejsou přiřazeny žádné osoby.</em><?php
        } ?></p>
        </div>
        <!-- end of #info -->
    </fieldset>

    <fieldset>
        <legend><strong>Popis</strong></legend>
        <div class="field-text"><?php echo stripslashes($rec_g['contents']); ?></div>
    </fieldset>

    <!-- následuje seznam přiložených souborů -->
    <?php //generování seznamu přiložených souborů
            $sqlFilter = DB_PREFIX."file.secret<=".$user['aclSecret']; //DB_PREFIX."case.deleted in (0,".$user['aclRoot'].") AND ".
            $sql = "SELECT mime,  ".DB_PREFIX."file.originalname AS 'title', ".DB_PREFIX."file.id AS 'id'
            FROM ".DB_PREFIX."file
            WHERE $sqlFilter AND ".DB_PREFIX."file.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."file.idtable=2
            ORDER BY ".DB_PREFIX."file.originalname ASC";
            $res = mysqli_query($database, $sql);
            $i = 0;
            while ($rec = mysqli_fetch_assoc($res)) {
                $i++;
                if ($i == 1) { ?>
    <fieldset>
        <legend><strong>Přiložené soubory</strong></legend>
        <ul id="prilozenadata">
            <?php } //zobrazovani obrazku i jako obrazky
                if (in_array($rec['mime'], $config['mime-image'], true)) { ?>
            <li><a href="file/attachement/<?php echo $rec['id']; ?>"><img width="300px" alt="<?php echo stripslashes($rec['title']); ?>" src="file/attachement/<?php echo $rec['id']; ?>"></a></li>
            <?php		} else { ?>
            <li><?php echo $rec['mime']; ?><a href="file/attachement/<?php echo $rec['id']; ?>"><?php echo stripslashes($rec['title']); ?></a></li>
            <?php } ?>
            <?php
            }
            if ($i != 0) { ?>
        </ul>
        <!-- end of #prilozenadata -->
    </fieldset>
    <?php
        }
            // konec seznamu přiložených souborů?>

    <?php //skryti poznamek
if ((isset($filter['notes']) and $filter['notes'] == 'on')) { ?>
    <!-- následuje seznam poznámek -->
    <?php // generování poznámek
        $sqlFilter = DB_PREFIX."note.deleted in (0,".$user['aclRoot'].") AND (".DB_PREFIX."note.secret<=".$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
        $sql_n = "SELECT ".DB_PREFIX."note.datum as date_created, ".DB_PREFIX."note.iduser AS 'iduser', ".DB_PREFIX."note.title AS 'title', ".DB_PREFIX."note.note AS 'note', ".DB_PREFIX."note.secret AS 'secret', ".DB_PREFIX."user.userName AS 'user', ".DB_PREFIX."note.id AS 'id'
        FROM ".DB_PREFIX."note, ".DB_PREFIX."user
        WHERE $sqlFilter AND ".DB_PREFIX."note.iduser=".DB_PREFIX."user.userId AND ".DB_PREFIX."note.iditem=".$_REQUEST['rid']." AND ".DB_PREFIX."note.idtable=2
        ORDER BY ".DB_PREFIX."note.datum DESC";
        $res_n = mysqli_query($database, $sql_n);
        $i = 0;
        while ($rec_n = mysqli_fetch_assoc($res_n)) {
            $i++;
            if ($i == 1) { ?>
    <fieldset>
        <legend><strong>Poznámky</strong></legend>
        <div id="poznamky"><?php
            }
            if ($i > 1) {?>
            <hr /><?php
            } ?>
            <div class="poznamka">
                <h4><?php echo stripslashes($rec_n['title']).' - '.stripslashes($rec_n['user']).' ['.webdate($rec_n['date_created']).']';
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
                <span
                      class="poznamka-edit-buttons"><?php
            if (($rec_n['iduser'] == $user['userId']) || ($usrinfo['right_text'])) {
                echo '<a class="edit" href="editnote.php?rid='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;idtable=2" title="upravit"><span class="button-text">upravit</span></a> ';
            }
            if (($rec_n['iduser'] == $user['userId']) || ($user['aclDirector'])) {
                echo '<a class="delete" href="procnote.php?deletenote='.$rec_n['id'].'&amp;personid='.$_REQUEST['rid'].'&amp;backurl=readgroup.php?rid='.$_GET['rid'].'" onclick="'."return confirm('Opravdu smazat poznámku &quot;".stripslashes($rec_n['title'])."&quot; náležící k osobě?');".'" title="smazat"><span class="button-text">smazat</span></a>';
            } ?>
                </span>
            </div>
            <!-- end of .poznamka -->
            <?php
        }
        if ($i != 0) { ?>
        </div>
        <!-- end of #poznamky -->
    </fieldset>
    <?php }
    // konec poznámek
    ?>
    <?php } ?>
</div>
<!-- end of #obsah -->
<?php
        } else {
            $_SESSION['message'] = "Skupina neexistuje!";
            header('location: index.php');
        }
    } else {
        $_SESSION['message'] = "Pokus o neoprávněný přístup zaznamenán!";
        header('location: index.php');
    }
    latteDrawTemplate("footer");
?>
