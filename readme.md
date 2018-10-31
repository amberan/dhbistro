# BISTRO

### News and Updates
- **1.5.4**  
	odstraneni vsech GOTO  
	html validni
	presunuti vsech css souboru do css adresare
	backup fallback na starou db
	odstraneni debug dat z priloh
	zaloha je komprimovana
- **1.5.3**  
	prepsan session management (odpojena tabulka loggedin)  
    vsechny inputy $_REQUEST osetreny na injection  
	konfigurace db presunuta do $conf[]  
	debug   
- **1.5.2**  
    $config[]  
    oprava pocitadla delky zpracovani stranky  
    procisteni HEAD  
    zaznam o zaloze do db pouze pokud se povede, nasledne kontrola na update script  
- **1.5.1**  
    trhani func_main na kusy  
    backup (INDEX>FULLTEXT)  
- **1.5**  
    PHP7 + MySQLi

### TODO
- omezit audit na nejaky rozumne vykreslitelny pocet
- data processing musi byt includovany v func_main a nasledne smerovano do prehledu dane funkcionality
- hashovat hesla
- dopsat logiku pridavani indexu (sql/update)
- vytrhnout "aktuality" z indexu - wall
- sprava obrazku a priloh
    - zobrazovat nahledy obrazku - getfile
    - obrazky zobrazovat v novem okne, namisto stahovani
    - posterizace/solarizace.... symbolu
- markdown wysiwyg editor https://github.com/nhnent/tui.editor#easy-wysiwyg-mode
    - session counter na submitech
- loop?
    - runtime rework
- cislovani tabulek pretahnout na nazvy 
- wikimedia
    - audit bokem (audit, unauthorized)
    - vycisteni html tagu z textu v db (css styly, id, tagy) + konverze markdown syntaxe
        - https://github.com/thephpleague/html-to-markdown
- pri neuspesnem loginu se vypisuji chyby do logu

### OTAZKY
- nw_unread, nw_audit_traill nemaji zadny index, pritom jsou to ty nejvetsi tabulky
- je neco co se da zahodit jako celek? zlobody? dostupnost?
- potrebujeme $config['timeout'] - nestaci udaj v DB?

### OBSAH
soubor                  | obsah                                     | obsluhovany objekt
--- | --- | ---
inc/audit_trail.php     | generovani auditni stopy + zamezeni pristupu
inc/backup.php          | backup mechanism
inc/database.php        | database connection mechanism
inc/debug.php			| debug messaging
inc/footer.php          | fnc footer
inc/func_main.php       | knihovna balastu
inc/important.php       | heslo databaze
inc/menu.php            | generovani menu
inc/header.php          | fnc header
inc/session.php         | obsluha session uzivatele
inc/unread.php          | obsluha neprectenych objektu
addar2c.php             | uprava hlaseni - report 2 case            | report / pripad
addc2ar.php             | uprava hlaseni - case 2 report            | pripad / report
addp2ar.php             | uprava hlaseni - person 2 report          | osoba / report
addp2c.php              | uprava pripadu - person 2 case            | osoba / pripad
addp2g.php              | uprava skupiny - person 2 group           | osoba / skupina
addpersons.php          | pridani osoby                             | osoby
addreports.php          | pridani reportu                           | reporty
adds2c.php              | prirazeni symbolu - case                  | symbol / pripad
addsy2ar.php            | prirazeni symbolu - report                | symbol / report
addsy2c.php             | prirazeni symbolu - case                  | symbol / pripad
addsy2p.php             | prirazeni symbolu - person                | symbol / osoba
addsymbols.php          | pridani symbolu                           | symboly
audit.php               | audit
cases.php               | LIST pripadu                              | pripady
dashboard.php           | osobni nastenka
doodle.php              | dostupnost
editactrep.php          | uprava reportu                            | reporty
editcase.php            | uprava pripadu                            | pripady
editdashboard.php       | uprava nastenky
editgroup.php           | uprava skupiny                            | skupiny
editnote.php            | uprava poznamek                           | poznamky
editperson.php          | uprava osoby                              | osoby
editsymbol.php          | uprava symbolu                            | symboly
edituser.php            | uprava uzivatele                          | uzivatele
evilpoints.php          | zlobody
getfile.php             | stazeni prilohy
getportrait.php         | zobrazeni fotografie
groups.php              | LIST skupiny                              | skupiny
index.php               | sdilena nastenka
login.php               | prihlasovaci stranka
logout.php              |
mapagents.php           | mapa agentu
newactrep.php           | novy report                               | reporty
newcase.php             | novy pripad                               | pripady
newgroup.php            | nova skupina                              | skupiny
newnews.php             | nova novinka                              | novinky
newnote.php             | nova poznamka                             | poznamky
newperson.php           | nova osoba                                | osoby
newsymbol.php           | novy symbol                               | symboly
newuser.php             | novy uzivatel                             | uzivatele
orgperson.php           | ORG uprava osoby                          | osoby
persons.php             | LIST osoby                                | osoby
procactrep.php          | zpracovani reportu                        | reporty
proccase.php            | zpracovani pripadu                        | pripady
procgroup.php           | zpracovani skupiny                        | skupiny
procnews.php            | zpracovani novinek                        | novinky
procnote.php            | zpracovani poznamek                       | poznamky
procother.php           | zpracovani 
procperson.php          | zpracovani osob                           | osoby
procsettings.php        | zpracovani nastaveni                      | nastaveni
procuser.php            | zpracovani uzivatele                      | uzivatele
readactrep.php          | DETAIL reportu                            | reporty
readcase.php            | DETAIL pripadu                            | pripady
readgroup.php           | DETAIL skupiny                            | skupiny
readnote.php            | DETAIL poznamky                           | poznamky
readperson.php          | DETAIL osoby                              | osoby
readsymbol.php          | DETAIL symbolu                            | symboly
reports.php             | LIST reporty                              | reporty
search.php              | vyhledavani                               | vyhledavani
settings.php            | nastaveni                                 | nastaveni
symbol_search.php       | vyhledavani podle symbolu                 | vyhledavani
symbol_search_result.php| vyhledavani podle symbolu - vysledky      | vyhledavani
symbols.php             | LIST symboly                              | symboly
tasks.php               | LIST ukoly                                | ukoly
users.php               | LIST uzivatele                            | uzivatele

### NOTES
http://nhtestbistro.talmahera.eu/
ftp://www.talmahera.eu/nhtestbistro.talmahera.eu/error_log

```php
   $file_extension = strtolower(substr(strrchr($filename,"."),1));
            switch ($file_extension) {
                case "pdf": $ctype="application/pdf"; break;
                case "exe": $ctype="application/octet-stream"; break;
                case "zip": $ctype="application/zip"; break;
                case "doc": $ctype="application/msword"; break;
                case "xls": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpe": case "jpeg":
                case "jpg": $ctype="image/jpg"; break;
                default: $ctype="application/force-download";
            }
            
    $password = hash("SHA512", $_POST["password"] . 'sůůůl');            
    $password = mysql_real_escape_string((hash("SHA512", $_POST["password"] . 'sůůůl'));
```