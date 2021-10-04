# BISTRO changelog


<!-- git log --no-merges 1.5.. | unexpand -a | sed -e 's/\s\s*$$//' | head -n 2070 | grep -v "Date\|commit\|Author\|typo\|.gitlab-ci.yml\|.codeclimate.yml" | sed -r '/^\s*$/d' | uniq
  -->

**1.8.2**
- zrychleni vypisu reportu a oprava trideni [#158](https://gitlab.alembiq.net/larp/bistro/issues/158)
- oprava ukladani novinek [#156](https://gitlab.alembiq.net/larp/bistro/issues/156)
- odstraneni prebytecnych sloupcu z tabulky dashboard [#160](https://gitlab.alembiq.net/larp/bistro/issues/160)
- odstraneni `commonmark` a kompletni nahrada pomoci `TUI editor` [#163](https://gitlab.alembiq.net/larp/bistro/issues/163)


**1.8.1**
- update knihoven
- filtrovani - skupiny [#59](https://gitlab.alembiq.net/larp/bistro/issues/59)
- zmena databazoveho engine na InnoDB [#150](https://gitlab.alembiq.net/larp/bistro/issues/150)
- konverze archive(int) na archived(timestamp) pro osoby [#140](https://gitlab.alembiq.net/larp/bistro/issues/140)
- smazani uzivatele systemu nejdou pridat jako ucastnici udalosti [#149](https://gitlab.alembiq.net/larp/bistro/issues/149)
- pridani udaje `strop` pro `osoba` [#40](https://gitlab.alembiq.net/larp/bistro/issues/40)
- WIP redesign skupiny [#118](https://gitlab.alembiq.net/larp/bistro/issues/117)

**1.8**
- release...

**1.7.6**
- drobne upravy pro odstraneni PHP warningu
- zobrazovani data vytvoreni u vyhledavani
- zobrazovany flagy utajeni a archivace u vyhledavani
- pripad nema datum vytvoreni [#51](https://gitlab.alembiq.net/larp/bistro/issues/51)
- skupina nema datum vytvoreni [#52](https://gitlab.alembiq.net/larp/bistro/issues/52)

**1.7.5**
- chybi prava pro reporty a symboly [#132](https://gitlab.alembiq.net/larp/bistro/issues/132)

**1.7.4**
- archivovatelne symboly [#22](https://gitlab.alembiq.net/larp/bistro/issues/22)
- modal window pro notifikace dostupny v starem UI

**1.7.3**
- nasazeni trideni na stare formulare [#87](https://gitlab.alembiq.net/larp/bistro/issues/87)
- WIP filtrovani - pripady [#59](https://gitlab.alembiq.net/larp/bistro/issues/59)
- WIP redesign pripady [#118](https://gitlab.alembiq.net/larp/bistro/issues/118)
- WIP odstaneni proccase.php [#106](https://gitlab.alembiq.net/larp/bistro/issues/106)
- CI rework nasazeni na test [#127](https://gitlab.alembiq.net/larp/bistro/issues/127)

**1.7.2** Access 2.0
- nova prava [#49](https://gitlab.alembiq.net/larp/bistro/issues/49)
- uprava spravy uzivatelu podle [#49](https://gitlab.alembiq.net/larp/bistro/issues/49) [#101](https://gitlab.alembiq.net/larp/bistro/issues/101)
- sessions overhelm [#89](https://gitlab.alembiq.net/larp/bistro/issues/89)
- THE LOOP podle [#49](https://gitlab.alembiq.net/larp/bistro/issues/49) a [#89](https://gitlab.alembiq.net/larp/bistro/issues/89)
- vypis nauzavrenych hlaseni/pripadu/ukolu uzivatele jako funkce [#104](https://gitlab.alembiq.net/larp/bistro/issues/104)

**1.7.1**
- trideni [#87](https://gitlab.alembiq.net/larp/bistro/issues/87)

**1.7.0** HLIDKY BREZEN 2020

**1.6.7**
- CODECLIMATE cleanup
- ve vyhledavani se zobrazuji flagy utajeni a smazani (s ohledem na prava uzivatele)[#53](https://gitlab.alembiq.net/larp/bistro/issues/53)[#85](https://gitlab.alembiq.net/larp/bistro/issues/85)
- sprava uzivatelu uz zobrazuje pouze neuzavrene pripady [#84](https://gitlab.alembiq.net/larp/bistro/issues/84)
- nastaveni nevyzaduje k ulozeni email [#83](https://gitlab.alembiq.net/larp/bistro/issues/83)
- oprava pridavani priloh [#86](https://gitlab.alembiq.net/larp/bistro/issues/86)
- prihlasovaci obrazovka v bootstrapu [#75](https://gitlab.alembiq.net/larp/bistro/issues/75)
- bootstrap zobrazeni zprav [#17](https://gitlab.alembiq.net/larp/bistro/issues/17)
- prepinani temat [#72](https://gitlab.alembiq.net/larp/bistro/issues/72)

**1.6.6**
- BUGFIX databaze

**1.6.5**
- v nastaveni link na sebe [#46](https://gitlab.alembiq.net/larp/bistro/issues/46)
- installer [#70] (https://gitlab.alembiq.net/larp/bistro/issues/70)
- BUGFIX pretikajici menu [#9](https://gitlab.alembiq.net/larp/bistro/issues/9)
- LATTE aktuality [#56](https://gitlab.alembiq.net/larp/bistro/issues/56)
- LATTE nastenka [#55](https://gitlab.alembiq.net/larp/bistro/issues/55)
- osobni nastaveni - odkaz na svoji kartu [#46](https://gitlab.alembiq.net/larp/bistro/issues/46)
- osobni nastaveni - email [#57](https://gitlab.alembiq.net/larp/bistro/issues/57)
- LATTE zalohovani [#58](https://gitlab.alembiq.net/larp/bistro/issues/58)

**1.6.4**
- oprava zobrazovani Tracy [#8](https://gitlab.alembiq.net/larp/bistro/issues/8)
- email uzivatele [#44](https://gitlab.alembiq.net/larp/bistro/issues/44)

**1.6.3**
- aktuality > MD (potrebuje dostylovat)
- novinky > MD (potrebuje dostylovat)
- [thephpleague/commonmark] (https://github.com/thephpleague/commonmark) markdown2html convertor
- ukladani nastenky [#13](https://gitlab.alembiq.net/larp/bistro/issues/13)

**1.6.2**
- uprava vyhledavani (odstraneni diakritiky z klice)
- .htaccess
- THE LOOP: nastaveni, uzivatele, zalohovani
- LATTE manualni zalohovani
- LATTE sprava uzivatelu
- editace uzivatelu nabizi jmeny seznam osob k napojeni
- konfigurace pro easy coding standard
- konfigurace pro gitlab ci
- aktualizace knihoven prez composer
- update databaze preveden na funkce
- mazani starych tabulek

**1.6.1**
- [TUI.editor](https://github.com/nhn/tui.editor) CDN verze
- Latte - Nastaveni
- [html-to-markdown](https://github.com/thephpleague/html-to-markdown) DB_PREFIX."users".plan > DB_PREFIX."users".plan_md
- vyhledavani v top menu
- skupiny zobrazuji prilozene obrazky

**1.6.0**
- funkce pro nacitani osob  (processing/person.php)
- automaticky pull z gitlabu
- promazani duplicitnich zaznamu v tabulce neprectenych
- zobrazovani poctu neprectenych polozek v menu
- obrazky presunuty do adresare images
- improved backup (fast restore)
- API login, logout, personRead
- rozepsani dokumentace
- SoapUI testsuite pro API
- updater - table rename - loggedin, map, backups, cases, data, groups, notes, persons, reports, tasks, symbols, users


**1.5.8 HLIDKY 3/2019**
- LATTE template - login, header, footer
- HTMLtoMD convertor
- update script - alter table funkcionalita
- BUGFIX vytvoreni noveho uzivatele
- sjednoceni zpracovani uzivatelu a sama sebe
- presmerovani vseho logovani do Tracy ./log/info.log
- unifikované formátování datumů
- BUGFIX nehleda ve smazanych poznamkach
- LATTE nahrazeni funkci PageStart a PageEnd

**1.5.7**
- rework vyhledavani - ignoruje diakritiku, mala/velka pismena, hleda casti slov
- BUGFIX injection vylepseni
- odstraneni fallbacku - pokud existuje update script, udela se zaloha a pak hned update
- TinyMCE 5.0
- jQuery 3.3.1
- Latte 2.4
- Tracy 2.5 - nahrazuje puvodni debug
- oddeleni db konektoru a konfigurace platformy
- nacitani custom textu
- priprava MD v databazi (*_md)

**1.5.6 HLIDKY 11/2018**
- nove nastylovani loginu, spravy uzivatelu
- moznost editace vsech typu prav
- mazani unread pro smazane uzivatele
- zobrazovani data vytvoreni a zmeny u jednotlivych objektu (osoby, vyhledavani, pripadu)
- zobrazovani priloh u hlaseni, osob, pripadu
- telefonni cisla jsou zobrazovany jako aktivni linky - volani mobilem primo z bistra
- mazani novinek
- zobrazovani datumu u poznamek
- BUGFIX: $_REQUEST,$_POST,$_GET vicerozmerne pole osetrejeni SQL injection
- BUGFIX: generovani hesel novych uzivatelu
- BUGFIX razeni reportu podle data/casu vyjezdu

**1.5.5 SUSPEND USER**
- novinky vytazeny z indexu
- ukladaji se i poznamky bez nazvu
- reset hesla, zamknuti a odemknuti uzivatele  -

**1.5.4 MD5 PASSWORD**
- md5 hesla
- odstraneni vsech GOTO
- html validni
- presunuti vsech css souboru do css adresare
- backup fallback na starou db
- odstraneni debug dat z priloh
- zaloha je komprimovana
- zpracovani nastaveni nove

**1.5.3 SESSION MANAGEMENT REWORK**
- prepsan session management (odpojena tabulka loggedin)
    vsechny inputy $_REQUEST osetreny na injection
- konfigurace db presunuta do $conf[]
- debug

**1.5.2 DATABASE UPDATE SCRIPT**
    $config[]
    oprava pocitadla delky zpracovani stranky
    procisteni HEAD
    zaznam o zaloze do db pouze pokud se povede, nasledne kontrola na update script

**1.5.1 BACKUP UPDATE**
    trhani func_main na kusy
    backup (INDEX>FULLTEXT)

**1.5 PHP7**
    PHP7 + MySQLi
