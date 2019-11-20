<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/inc/func_main.php');
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT,$config['folder_logs']);
$latte = new Latte\Engine;
$latte->setTempDirectory($config['folder_cache']);
$latteParameters['current_location'] = $_SERVER["PHP_SELF"];;
$latteParameters['menu'] = $menu;
$latteParameters['menu2'] = $menu2;

//echo "<xmp>"; print_r ($URL); echo "</xmp>";
if ( strpos($URL[1],'.php') == null) { //THE LOOP 
    if ($URL[1] == 'settings') {
        include($_SERVER['DOCUMENT_ROOT'].'/settings.php');
    } elseif ($usrinfo['right_super'] > 0 and $URL[1] == 'backup') { //BACKUP
        $latteParameters['title'] = $text['zalohovani'];
         include($_SERVER['DOCUMENT_ROOT'].'/backup.php');
    } elseif ($URL[1] == 'users') {
        if ($usrinfo['right_power']<1) {
            unauthorizedAccess(8, 1, 0, 0);
        } else { //USER MANAGEMENT
            $latteParameters['title'] = $text['spravauzivatelu'];
            auditTrail(8, 1, 0);
            $latteParameters['actions'][] = array("/users/new",$text['vytvorituzivatele']);
            if ($URL[2] == 'new') { //ADD USER
                $latteParameters['subtitle'] = $text['vytvorituzivatele']; 
                include($_SERVER['DOCUMENT_ROOT'].'/user_new.php');
            } elseif ($URL[2] == 'edit') { //EDIT USER
                $latteParameters['subtitle'] = $text['upravituzivatele'];
                include($_SERVER['DOCUMENT_ROOT'].'/user_edit.php');
            } else { //LIST USERS
                include($_SERVER['DOCUMENT_ROOT'].'/users.php');
            }
        }
    } else { //spatny odkaz
        $latteParameters['title'] = $text['http401'];
        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'headerMD.latte', $latteParameters);
        $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'menu.latte', $latteParameters);    
        echo "<h1>".$text['http401']."</h1>";
    }
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footerMD.latte', $latteParameters);




} else { // stare jadro
    $latteParameters['title'] = 'Aktuality';
    $latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'header.latte', $latteParameters);
    if (isset($_SESSION['sid'])) {
                auditTrail(5, 1, 0);
        }
        mainMenu (1);
        deleteUnread (5,0);
        sparklets ('<strong>aktuality</strong>',(($usrinfo['right_power'])?'<a href="dashboard.php">zobrazit nástěnku</a>; <a href="newnews.php">přidat aktualitu</a>':'<a href="dashboard.php">zobrazit nástěnku</a>'));
        // zpracovani filtru
        if (!isset($_REQUEST['kategorie'])) {
          $f_cat=0;
        } else {
          $f_cat=$_REQUEST['kategorie'];
        }
        if (!isset($_REQUEST['sort'])) {
          $f_sort=1;
        } else {
          $f_sort=$_REQUEST['sort'];
        }
        switch ($f_cat) {
          case 0: $fsql_cat=''; break;
          case 1: $fsql_cat=' AND '.DB_PREFIX.'news.kategorie=1 '; break;
          case 2: $fsql_cat=' AND '.DB_PREFIX.'news.kategorie=2 '; break;
          default: $fsql_cat='';
        }
        switch ($f_sort) {
          case 1: $fsql_sort=' '.DB_PREFIX.'news.datum DESC '; break;
          case 2: $fsql_sort=' '.DB_PREFIX.'news.datum ASC '; break;
          case 3: $fsql_sort=' '.DB_PREFIX.'user.login ASC '; break;
          case 4: $fsql_sort=' '.DB_PREFIX.'user.login DESC '; break;
          default: $fsql_sort=' '.DB_PREFIX.'news.datum DESC ';
        }
        //
        function filter () {
          global $database,$f_cat,$f_sort;
          echo '<form action="index.php" method="post" id="filter">
<!-- FILTR DOCASNE ZRUSEN, ABY SE OTESTOVALO, JESTLI JE VUBEC POTREBA
        <fieldset>
          <legend>Filtr</legend>
          <p>Vypsat <select name="kategorie">
        <option value="0"'.(($f_cat==0)?' selected="selected"':'').'>všechny</option>
        <option value="1"'.(($f_cat==1)?' selected="selected"':'').'>herní</option>
        <option value="2"'.(($f_cat==2)?' selected="selected"':'').'>systémové</option>
</select> aktuality a seřadit je podle <select name="sort">
        <option value="1"'.(($f_sort==1)?' selected="selected"':'').'>data sestupně</option>
        <option value="2"'.(($f_sort==2)?' selected="selected"':'').'>data vzestupně</option>
        <option value="3"'.(($f_sort==3)?' selected="selected"':'').'>jména autora vzestupně</option>
        <option value="4"'.(($f_sort==4)?' selected="selected"':'').'>jména autora sestupně</option>
</select>.</p>
          <div id="filtersubmit"><input type="submit" name="filter" value="Filtrovat" /></div>
        </fieldset>
-->
</form>';
        }
// dashboard
?>
<div id="dashboard">
    <fieldset>
        <legend><strong>Osobní nástěnka</strong></legend>
        <table>
            <tr>
                <td>
                    <h3>Rozpracovaná nedokončená hlášení: <?php
                                $sql_r="SELECT ".DB_PREFIX."report.secret AS 'secret', ".DB_PREFIX."report.label AS 'label', ".DB_PREFIX."report.id AS 'id' FROM ".DB_PREFIX."report WHERE ".DB_PREFIX."report.iduser=".$usrinfo['id']." AND ".DB_PREFIX."report.status=0 AND ".DB_PREFIX."report.deleted=0 ORDER BY ".DB_PREFIX."report.label ASC";
                                $res_r=mysqli_query ($database,$sql_r);
                                $rec_count = mysqli_num_rows ($res_r);
                                echo $rec_count
                                ?>
                    </h3>
                    <p>
                        <?php
                                if (mysqli_num_rows ($res_r)) {
                                        $reports=Array();
                                        while ($rec_r=mysqli_fetch_assoc ($res_r)) {
                                                $reports[]='<a href="./readactrep.php?rid='.$rec_r['id'].'&hidenotes=0&truenames=0">'.StripSlashes ($rec_r['label']).'</a>';
                                        }
                                        echo implode ($reports,'<br />');
                                } else {
                                        echo 'Nemáte žádná nedokončená hlášení.';
                                } ?></p>
                    <div class="clear">&nbsp;</div>
                    <h3>Přiřazené neuzavřené případy: <?php
                        $sql="SELECT ".DB_PREFIX."case.id AS 'id', ".DB_PREFIX."case.title AS 'title' FROM ".DB_PREFIX."c2s, ".DB_PREFIX."case WHERE ".DB_PREFIX."case.id=".DB_PREFIX."c2s.idcase AND ".DB_PREFIX."c2s.idsolver=".$usrinfo['id']." AND ".DB_PREFIX."case.status<>1 AND ".DB_PREFIX."case.deleted=0 ORDER BY ".DB_PREFIX."case.title ASC";
                        $pers=mysqli_query ($database,$sql);
                        $rec_count = mysqli_num_rows ($pers);
                        echo $rec_count
                        ?>
                    </h3>
                    <p>
                        <?php
                        $cases=Array();
                        while ($perc=mysqli_fetch_assoc ($pers)) {
                                $cases[]='<a href="./readcase.php?rid='.$perc['id'].'&hidenotes=0">'.StripSlashes ($perc['title']).'</a>';
                        }
                        echo ((implode($cases, '<br />')<>"")?implode($cases, '<br />'):'<em>Nemáte žádný přiřazený neuzavřený případ.</em>');
                        ?></p>
                </td>
                <td>
                    <h3>Nedokončené úkoly: <?php
                        $sql_r="SELECT * FROM ".DB_PREFIX."task WHERE ".DB_PREFIX."task.iduser=".$usrinfo['id']." AND ".DB_PREFIX."task.status=0 ORDER BY ".DB_PREFIX."task.created ASC";
                        $res_r=mysqli_query ($database,$sql_r);
                        $rec_count = mysqli_num_rows ($res_r);
                        echo $rec_count
                        ?>
                    </h3>
                    <p>
                        <?php
                        if (mysqli_num_rows ($res_r)) {
                                $tasks=Array();
                                while ($rec_r=mysqli_fetch_assoc ($res_r)) {
                                        $tasks[]=StripSlashes ($rec_r['task']).' ('.getAuthor($rec_r['created_by'],2).') | <a href="procother.php?fnshtask='.$rec_r['id'].'">hotovo</a>';
                                }
                                echo implode ($tasks,'<br />');
                        } else {
                                echo 'Nemáte žádné nedokončené úkoly.';
                        } ?></p>
                </td>
            </tr>
        </table>
        <div class="clear">&nbsp;</div>
    </fieldset>
</div>
<?php
include ($_SERVER['DOCUMENT_ROOT'].'/news.php');
$latte->render($_SERVER['DOCUMENT_ROOT'].'/templates/'.'footer.latte', $latteParameters);
}
?>
