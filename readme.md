# BISTRO

### News and Updates
1.5 - PHP7 + MySQLi

###TODO
- vythnout bokem nastaveni DB 
- vytrhnout bokem session management
    - odpojit druhou login tabulku
- vytrhnout bokem menu 
- header a footer samostatne
- loop?
- runtime rework
- sprava obrazku a priloh
    - zobrazovat nahledy obrazku
    - obrazky zobrazovat v novem okne, namisto stahovani
- wikimedia
    - audit bokem (audit, unauthorized)
    - vycisteni html tagu z textu v db (css styly, id, tagy) + konverze markdown syntaxe
    - backup samostatne
- reseni na upgrady databaze mezi verzemi
- session counter na submitech

### OTAZKY
- nw_unread nema zadny index, pritom se hleda podle uzivatele ?
- jak se resi uzivatele
- kde je audit
- co z funkcionality jeste mijim

### OBSAH
soubor                  | obsah                                     | obsluhovany objekt
--- | --- | ---
inc/func_main.php       | knihovna balastu
inc/important.php       |
addar2c.php             | uprava hlaseni - report 2 case 
addc2ar.php             | uprava hlaseni - person 2 report
addp2ar.php             | uprava hlaseni - case 2 person
addp2c.php              | uprava pripadu - case 2 solver
addp2g.php              | uprava skupiny - group 2 person
addpersons.php          | pridani osoby                             | osoby
addreports.php          | pridani reportu                           | reporty
adds2c.php              | uprava pripadu - solver 2 case
addsy2ar.php            | prirazeni symbolu hlaseni
addsy2c.php             | prirazeni symbolu pripadu
addsy2p.php             | prirazeni symbolu osobe
addsymbols.php          | pridani symbolu                           | symboly
asearch.php             |
asearch_search.php      |
audit.php               |
cases.php               | LIST pripadu                              | pripady
dashboard.php           | nastenka
doodle.php              | dostupnost
editactrep.php          |
editcase.php            | uprava pripadu                            | pripady
editdashboard.php       | uprava nastenky
editgroup.php           | uprava skupiny                            | skupiny
editnote.php            | uprava poznamek                           | poznamky
editperson.php          | uprava osoby                              | osoby
editsymbol.php          | uprava symbolu                            | symboly
edituser.php            | uprava uzivatele                          | uzivatele
evilpoints.php          | zlobody
getfile.php             | vraceni prilohy
getportrait.php         | vraceni fotografie
groups.php              | LIST skupiny                              | skupiny
index.php               | 
login.php               | prihlasovaci stranka
logout.php              |
mapagents.php           | mapa agentu
newactrep.php           | novy actor report
newcase.php             | novy pripad                               | pripady
newgroup.php            | nova skupina                              | skupiny
newnews.php             | nova novinka                              | novinky
newnote.php             | nova poznamka                             | poznamky
newperson.php           | nova osoba                                | osoby
newsymbol.php           | novy symbol                               | symboly
newuser.php             | novy uzivatel                             | uzivatele
orgperson.php           | ORG uprava osoby                          | osoby
persons.php             | LIST osoby                                | osoby
procactrep.php          | zpracovani ??? 
proccase.php            | zpracovani pripadu                        | pripady
procgroup.php           | zpracovani skupiny                        | skupiny
procnews.php            | zpracovani novinek                        | novinky
procnote.php            | zpracovani poznamek                       | poznamky
procother.php           | zpracovani
procperson.php          | zpracovani osob                           | osoby
procsettings.php        | zpracovani nastaveni                      | nastaveni
procuser.php            | zpracovani uzivatele                      | uzivatele
readactrep.php          |
readcase.php            | DETAIL pripadu                            | pripady
readgroup.php           | DETAIL skupiny                            | skupiny
readnote.php            | DETAIL poznamky                           | poznamky
readperson.php          | DETAIL osoby                              | osoby
readsymbol.php          | DETAIL symbolu                            | symboly
reports.php             | LIST reporty                              | reporty
search.php              | vyhledavani                               | vyhledavani
settings.php            | nastaveni                                 | nastaveni
symbolpaint_1.php       |
symbolpaint.php         |
symbol_search.php       | vyhledavani podle symbolu                 | vyhledavani
symbol_search_result.php| vyhledavani podle symbolu - vysledky      | vyhledavani
symbols.php             | LIST symboly                              | symboly
tasks.php               | LIST ukoly                                | ukoly
users.php               | LIST uzivatele                            | uzivatele

### NOTES
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