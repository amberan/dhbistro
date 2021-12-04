[![pipeline status](https://gitlab.com/alembiq/bistro/badges/master/pipeline.svg)](https://gitlab.com/alembiq/bistro/commits/master)
[![license](https://img.shields.io/github/license/amberan/dhbistro.svg)](https://gitlab.com/alembiq/bistro/blob/master/LICENSE)
[![lines of code](https://tokei.rs/b1/github/amberan/dhbistro)](https://tokei.rs/b1/github/amberan/dhbistro)


# [BISTRO](https://gitlab.com/alembiq/bistro)

**Are you one of the Others? A member of the Night Watch or Day Watch? Bistro is here for you and will try to keep you from getting lost in  all the gloom related informations!**


[Bistro](https://github.com/amberan/dhbistro/) was originally created to help players of Czech LARP [Pražská Hlídka](http://www.prazskahlidka.cz/). This cyclical larp is inspired by [Sergei Lukyanenko Night Watch](https://en.wikipedia.org/wiki/Night_Watch_(Lukyanenko_novel)).
In-game it's used by two teams (Day and Night watch) to keep tracks of characters and events especially between iterations of the game.

    # Bistro tě potřebuje!
    Jak sis určitě všiml Bistro není žádný ucelený profesionální produkt, navíc v tuto chvíli vypadají jednotlivé části úplně jinak než ostatní. To je způsobeno naší snahou přepsat grafické rozhraní do podoby použitelné i na mobilních telefonech. Snaha přepsat uživatelský interface nám ale ukázala, že spousta kódu potřebuje přepsat.

    A to je přesně místo kde se ukazuje, že přepisovat Bistro ve dvou (jeden PHP, druhý HTML/CSS) trvá mnohem déle, než by se nám líbilo a výrazně to snižuje komfort uživatelů. Proto teď hledáme pomoc, hodili by se nám další programátoři ale i stálí testeři.

    Počátek historie tohoto projektu leží někdy v roce 2006 pro první Hlídky, ještě jako Dies Irae larp. Tehdy Ethan napsal základ funkcionality v PHP (tehdy ve verzi 5). Když Bistro převzal Ambeřan začal verzovat zdrojový kód. Spolu s Atlanem přidal spoustu další funkcionality. O pár let později se přidal Ernedar. Koncem roku 2018 Charles upravil kód aby fungoval s PHP 7 a ve snaze pochopit fungování celého systému začal s refaktorizací celého kódu. S tím právě přišla i myšlenka na nové UI, na kterém se pomalu začalo pracovat a právě tam jsme teď.

    V tuto chvílí cílíme na tyto technologie PHP 7, MariaDB, Bootstrap, Latte a tui.editor. Samozřejmě, že by šlo vše napsat na zelené louce s mobilní aplikací a vším, ale na to čas a energii opravdu nemáme.

    Umíš s PHP? Pomůžeš? Více informací o stavu projektu je k dohledání na GitLabu.

    P.S. pokud neumíš programovat, ale i tak chceš pomoci, vždycky nám můžeš pomoct s testováním.

## running BiStro
It's a simple php website, you need just the basics - LAMP :)
### Prerequisities
- Apache2
    apache modules:
    - headers
    - rewrite
- MariaDB
- PHP7
    php modules:
    - php-mysql
    - php-xml
    - php-gd
    - php-zip
    - php-curl
    - php-cli
    - php-mbstring
### Installation
- clone repository & run composer
```bash
git clone git@gitlab.com:alembiq/bistro.git && cd bistro && composer install && composer update
```
- point apache to the BiStro folder
- create database & user
- open BiStro in your browser - you'll get installer :)

## [Changelog](CHANGELOG.md)

## [Contributing](CONTRIBUTING.md)

## Documentation
There is none:( docs below are just notes created during reverse engeneering phase.
- [enumerators](doc/enums.md)
- [files & folder structure](doc/files.md)
- [reverse engineered user permissions](doc/rights.md)
- [API prototype](doc/api.md)

## FAQ

- Lost all passwords?
```sql
UPDATE nw_user SET pwd=md5('newpassword') WHERE id='1';
```
- Prepare data for testing
```sql
DELETE FROM nw_case WHERE secret>0;
DELETE FROM nw_file WHERE secret>0;
DELETE FROM nw_group WHERE secret>0;
DELETE FROM nw_note WHERE secret>0;
DELETE FROM nw_person WHERE secret>0;
DELETE FROM nw_report WHERE secret>0;
DELETE FROM nw_symbol WHERE secret>0;

UPDATE nw_case SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_file SET secret=1 WHERE RAND() < 0.3;
UPDATE nw_group SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_note SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_person SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_report SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;
UPDATE nw_symbol SET secret=1 WHERE deleted = 0 AND RAND() < 0.3;

UPDATE nw_user SET aclAPI=1, aclAudit=1, aclCase=2, aclUser=1, aclNews=1, aclBoard=1, aclGamemaster=1, aclGroup=2,
aclHunt=1, aclPerson=2, aclRoot=1, aclSecret=2, aclReport=2, aclSymbol=2 where userSuspended=0 and userDeleted=0;
```
