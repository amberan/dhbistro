 # Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
<!--
,and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

git log --no-merges 1.5.. | unexpand -a | sed -e 's/\s\s*$$//' | head -n 2070 | grep -v "Date\|commit\|Author\|typo\|.gitlab-ci.yml\|.codeclimate.yml" | sed -r '/^\s*$/d' | uniq

https://github.com/orhun/git-cliff
git-cliff 0e4e3abba94636c210fde5e7d02089298589f26d..HEAD --output CHANGELOG.md

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security
 -->

## 1.8.7 - 2021-12-01
### Changed
- Refactored:ed: installer data restore #107
### Removed
- Removed: tasks #215

## 1.8.6 - 2021-11-23
### Changed
- Refactored: separation of index.php and func_main.php #217
- Refactored: installer update #107
### Fixed
- Fixed: assigned items on dashboard #214
### Security
- Refactored: storing platform configuration #82

## 1.8.5 - 2021-11-19
### Added
- Styled: recover icon #191
- Styled: unread icon #187
### Changed
- Refactored: upgrade procesure #213
- Refactored: menu on mobile #190

## 1.8.4 - 2021-11-16
### Added
- Styled: news vs board separation #189
### Changed
- Refactored: user edit page #181 #180 #179 #178 #177
### Fixed
- Fixed: archived person filtering #209
- Fixed: linking person to case #211
### Security
- Refactored: cleanup from the old permissions #2121

## 1.8.3 - 2021-11-12
### Added
- Feature: link from note to parent #201
- Feature: help wanted #175
- Feature: groups status #174
- Feature: search polish special characters #205
### Changed
- Refactored: antidating person #183
- Refactored: aclDirector permission to aclUser #178
- Styled: marking of unread items #173
### Removed
- Refactored: aclDirector permission to aclUser #178
### Fixed
- Fixed: listing groups on edit person #206
- Fixed: unread listed for every unread record #204
- Fixed: files on case SQL #197
- Fixed: edit report date #196
- Fixed: assigning people to case #195
- Fixed: archived flag on persons #171
- Fixed: assigning persons to users #172

## 1.8.2 - 2021-11-12
### Changed
- Styled: cases listing #118
- CI: reconfigured easy coding standard #151
- Refactored: faster report listing and fixed sorting #158
- Refactored: `commonmark` replaced with `TUI editor` #163
- Refactored: group detail filtering #164
### Deprecated
- CI: moved to gitlab.com #166
- Refactored: `commonmark` replaced with `TUI editor` #163
### Removed
- Database: removal of old columns from dashboard table #160### Fixed
- Fixed: saving news #156
- Fixed: filtering symbols #154
- Fixed: person listing on report #161
### Security
- Refactored: validation of permission secret #165

## 1.8.1]
### Added
- Feature: persons `roof` #40
### Changed
- Database: change all tables to InnoDB #150
- Database: persons archived flag changed from int to timestamp #140
- Refactored: groups filtering #59
- Styled: groups listing #118
### Fixed
- Fixed: adding removed persons to report #149

## 1.7.6]
### Added
- Feature: displaying date of creation in search
- Feature: showing flags in search
- Feature: case date of creation #51
- Feature: group date of creation #52
### Security
- Refactored: PHP warnings cleanup

## 1.7.5 - 2021-03-11
### Added
- Feature: new permissions for reports and symbols #132

## 1.7.4 - 2021-03-02
### Added
- Feature: symbols can be archived #22

## 1.7.3 - 2021-02-24
### Added
- Feature: sorting on object listings #87
### Changed
- Refactored: cases filtering #59
- Styled: cases listing #118
### Deprecated
- Refactored: WIP removal of proccase.php #106
### Security
- CI: new test deployment #127


## 1.7.2 - 2020-03-27
### Added
- Refactored: new permissions #49
- Feature: THE LOOP #49 #89
### Changed
- Refactored: dashboard listing #104
### Security
- Refactored: updated user management #49 #101
- Refactored: sessions #89

## 1.7.1 - 2020-03-22
### Added
- Feature: sorting #87

## 1.7.0 - 2020-03-08

## 1.6.7]
### Added
- Feature: search displays flags #53 #85
- Styled: notifications #17
- Feature: themes #72
### Changed
- Styled: login screen #75
### Fixed
- Fixed: user management displayes only unclosed cases #84
- Fixed: email not mandatory in settings #83
- Fixed: adding attachements #86
### Security
- CI: CODECLIMATE cleanup


## 1.6.6 - 2020-02-15
### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security
- db: minor fix

## 1.6.5]
### Added
- Feature: installer #70
- Feature: email in settings #57
### Changed
- Styled: news #56
- Styled: board #55
- Styled: backups #58
### Fixed
- Fixed: overflowing menu #9
### Security
- Feature: self link in settings #46

## 1.6.4 - 2020-01-12
### Added
- Feature: user email #44
### Fixed
- Fixed: displaying Tracy #8

## 1.6.3 - 2020-01-02
### Added
- CI: added [thephpleague/commonmark](https://github.com/thephpleague/commonmark) markdown2html convertor
### Changed
- Refactored: news, board to MD
### Fixed
- Fixed: saving board #13

## 1.6.2 - 2019-12-28
### Added
- Feature: .htaccess
- Feature: THE LOOP: settings, user management, backup
- CI: easy coding standard
- CI: gitlab ci
### Changed
- Refactored: search
- Styled: manual backup
- Styled: user management
### Removed
- Database: removal of unused tables
### Fixed
- Fix: linking users to people

## 1.6.1 - 2019-11-19
### Added
- Feature: search in menu
- Feature: groups display attached pictures
- Feature: CDN [TUI.editor](https://github.com/nhn/tui.editor)
### Changed
- Styled: settings
- Refactored: user plans to MD [html-to-markdown](https://github.com/thephpleague/html-to-markdown)

## 1.6.0 - 2019-06-26
### Added
- doc: init
- Feature: functions for reading persons (processing/person.php)
- Feature: pull trigger
- Feature: unread numbers in menu
- Feature: API login, logout, personRead
- test: SoapUI testsuite
### Changed
- Database: table rename - loggedin, map, backups, cases, data, groups, notes, persons, reports, tasks, symbols, users
- Refactored: unread duplicity cleanup
- Refactored: style images to /images/
- Refactored: backup

## 1.5.8 - 2019-03-25
### Added
- Feature: HTMLtoMD convertor
- Feature: updater: alter table func
- Feature: Tracy logging /log/info.log
### Changed
- Styled: login, header, footer
- Refactored: same func for users and myself
- Refactored: unified datum formating
- Styled: replacement of PageStart and PageEnd
### Fixed
- Fixed: new user
- Fixed: search in deleted notes

## 1.5.7 - 2019-02-27
### Added
- Database: markdown columns
- CI: latte, tracy, jquery, tinyMCE
### Changed
- Refactored: backup & update
- Refactored: search - ignore national characters, lowercase/uppercase
- Refactored: separation of db connetion and login
- Refactored: custom texts
### Security
- Security: sql injection

## 1.5.6 - 2018-11-28
### Added
- Feature: user mangement permissions editable
- Feature: phone numbers as active links
- Feature: news delete
- Feature: creation date of news
- Feature: date of creation and modification of person, search, case displayed
### Changed
- Styled: login
- Styled: user management
### Removed
- Performance: removal od unread for deleted users
### Fixed
- Fixed: showing attachements to report, person, case
- Fixed: genarating new passwords
- Fixed: sorting reports by time
### Security
- Security: sql injection $_REQUEST,$_POST,$_GET

## 1.5.5 - 2018-11-11
### Added
- Feature: password reset
- Feature: user lock, unlock
### Changed
- Refactored: news extracted from index.php
### Fixed
- Fixed: saving notes with empty name

## 1.5.4 - 2018-11-02
### Added
- Security: md5 password
- Feature: backup compression
### Changed
- Refactored: valid html
- Refactored: css files to /css/
- Refactored: backup fallback for older db
- Refactored: debug data on attachements
- Refactored: settings
### Removed
- Refactored: `goto` removed
### Security
- Security: md5 password

## 1.5.3 - 2018-10-28
### Added
- Feature: debug mode
### Changed
- Refactored: configurations to $conf[]
### Security
- Refactored: session management
- Security: sql injection for $_REQUEST

## 1.5.2 - 2018-10-23
### Added
- Database: db updater
### Changed
- Refactored: <head /> cleanup
### Fixed
- Fixed: page processing timer


## 1.5.1]
### Changed
- Refactored: separation of /inc/func_main.php
- Refactored: backup (INDEX>FULLTEXT)

## 1.5 - 2018-10-20
### Changed
- Refactored: PHP7 + MySQLi
### Security
- Refactored: PHP7 + MySQLi
