[![pipeline status](https://gitlab.com/alembiq/bistro/badges/master/pipeline.svg)](https://gitlab.com/alembiq/bistro/commits/master)
[![license](https://img.shields.io/github/license/amberan/dhbistro.svg)](https://gitlab.com/alembiq/bistro/blob/master/LICENSE)
[![lines of code](https://tokei.rs/b1/github/amberan/dhbistro)](https://tokei.rs/b1/github/amberan/dhbistro)


# [BISTRO](https://gitlab.com/alembiq/bistro)

[Bistro](https://github.com/amberan/dhbistro/) was originally created to help players of Czech LARP [Pražská Hlídka](http://www.prazskahlidka.cz/). This cyclical larp is inspired by [Sergei Lukyanenko Night Watch](https://en.wikipedia.org/wiki/Night_Watch_(Lukyanenko_novel)).
In-game it's used by two teams (Day and Night watch) to keep tracks of characters and events especially between iterations of the game.

## Disclaimer
This was a learning PHP project for one of the first player, currently being reworked to more maintainable state.

## [Changelog](CHANGELOG.md)

## Folder structure

| folder            | purpose |
| ---               | --- |
| /API/             | prototype of rest API |
| /css/             | Cascade Style Sheets |
| /custom/          | customization for all the main instances of Bistro + text files |
| /doc/             | whatever little documentation that exists |
| /files/           | attachments from the user space |
| /files/backups/   | local db backups |
| /files/portraits/ | portraits for `person` |
| /files/symbols/   | symbols for `person` |
| /images/          | graphics used in site |
| /inc/             | legacy libraries and shared fnc |
| /js/              | javascript scripts |
| /processing/      | libraries |
| /sql/             | empty db and update configurations |
| /templates/       | [Latte templates](https://latte.nette.org/) |
| /testsuite/       | SoapUI test suite for REST API |
| /vendor/          | external libraries |

## Running your own Bistro

It is recommended to run Bistro on a LAMP server. You need a MySQL/MariaDB database (to be configured
in [inc/platform.php](https://gitlab.com/alembiq/bistro/raw/master/inc/platform.php), password for
database needs to be put into /inc/important.php (whole of the second line of the file is used as a
password).
If there conditions are met, Bistro should populate the database and create default user.

### Troubleshooting

**admin password recovery**
```UPDATE nw_user SET pwd=md5('newpassword') WHERE id='1';```

### [API](doc/api.md)

### [user permissions](doc/rights.md)

### [enumerators](doc/enums.md)
