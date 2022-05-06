<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);


/**
 * list unfinished reports assigned to.
 *
 * @param int userId
 * @param mixed $userid
 *
 * @return array [id][name]
 */
function reportsAssignedTo($userid): array
{
    global $database;

    $unfinishedReports[] = [];

    $reportsListSql = 'SELECT reportId, reportName, date(reportEventDate) as reportEventDate
    FROM '.DB_PREFIX.'report
    WHERE '.DB_PREFIX.'report.reportOwner='.$userid.' AND '.DB_PREFIX.'report.reportStatus!=1
        AND ('.DB_PREFIX.'report.reportDeleted is null OR '.DB_PREFIX.'report.reportDeleted  < from_unixtime(1))
        AND ('.DB_PREFIX.'report.reportArchived is null OR '.DB_PREFIX.'report.reportArchived  < from_unixtime(1))
    ORDER BY '.DB_PREFIX.'report.reportName ASC';
    if ($reportsList = mysqli_query($database, $reportsListSql)) {
        while ($unfinishedReport = mysqli_fetch_assoc($reportsList)) {
            $unfinishedReports[] = [$unfinishedReport['reportId'], $unfinishedReport['reportName'], $unfinishedReport['reportEventDate']];
        }
    }
    return @$unfinishedReports;
}


    function reportStatus($role = null)
    {
        global $text;
        $list = array();
        $list[0] = $text['rozpracovane'];
        $list[1] = $text['dokoncene'];
        $list[2] = $text['analyzovane'];
        $list[3] = $text['archivovane'];
        $return = $list;
        if (isset($role) && is_numeric($role)) {
            $return = $list[$role];
        } elseif (isset($role) && is_string($role)) {
            $return = array_search($role, $list);
        }
        return $return;
    }

    function reportType($role = null)
    {
        global $text;
        $list = array();
        $list[1] = $text['vyjezd'];
        $list[2] = $text['vyslech'];
        $return = $list;
        if (isset($role) && is_numeric($role)) {
            $return = $list[$role];
        } elseif (isset($role) && is_string($role)) {
            $return = array_search($role, $list);
        }
        return $return;
    }


    function reportRole($role = null)
    {
        global $text;
        $list = array();
        $list[0] = $text['pritomny'];
        $list[1] = $text['vyslychajici'];
        $list[2] = $text['vyslychajici'];
        $list[3] = $text['zatceny'];
        $list[4] = $text['velitel'];
        $return = $list;
        if (isset($role) && is_numeric($role)) {
            $return = $list[$role];
        } elseif (isset($role) && is_string($role)) {
            $return = array_search($role, $list);
        }
        return $return;
    }

    function reportParticipants($reportId)
    {
        global $database,$user;
        $sqlFilter = '';
        if ($user['aclRoot'] < 1) {
            $sqlFilter = " AND ".DB_PREFIX."person.deleted = 0 ";
        }
        $participantSql = "SELECT
            concat(COALESCE(".DB_PREFIX."person.name,''),' ',COALESCE(".DB_PREFIX."person.surname,'')) as participantName,
            ".DB_PREFIX."ar2p.role as participantRole,
            ".DB_PREFIX."person.id as participantId
            FROM ".DB_PREFIX."person
            JOIN ".DB_PREFIX."ar2p on ".DB_PREFIX."person.id = ".DB_PREFIX."ar2p.idperson
            WHERE ".DB_PREFIX."ar2p.idreport = $reportId AND ".DB_PREFIX."person.secret <=".$user['aclSecret']." $sqlFilter
            ORDER BY ".DB_PREFIX."ar2p.role DESC";
        if ($participantList = mysqli_query($database, $participantSql)) {
            while ($participant = mysqli_fetch_assoc($participantList)) {
                $participants[] = array('participantId' => $participant['participantId'],
                                        'participantRole' => reportRole($participant['participantRole']),
                                        'participantName' => $participant['participantName']);
            }
        }
        if (isset($participants)) {
            return $participants;
        } else {
            return false;
        }
    }

    function reportsParticipants($reportIdList)
    {
        global $database,$user;
        $sqlFilter = '';
        if ($user['aclRoot'] < 1) {
            $sqlFilter = " AND ".DB_PREFIX."person.deleted = 0 ";
        }
        $participantSql = "SELECT
            concat(COALESCE(".DB_PREFIX."person.name,''),' ',COALESCE(".DB_PREFIX."person.surname,'')) as participantName,
            ".DB_PREFIX."ar2p.idreport as reportId,
            ".DB_PREFIX."ar2p.role as participantRole
            FROM ".DB_PREFIX."person
            JOIN ".DB_PREFIX."ar2p on ".DB_PREFIX."person.id = ".DB_PREFIX."ar2p.idperson
            WHERE ".DB_PREFIX."ar2p.idreport IN (" . implode(',', $reportIdList) . ") AND ".DB_PREFIX."person.secret <=".$user['aclSecret']." $sqlFilter
            ORDER BY ".DB_PREFIX."ar2p.idreport, ".DB_PREFIX."ar2p.role DESC";
        if ($participantList = mysqli_query($database, $participantSql)) {
            while ($participant = mysqli_fetch_assoc($participantList)) {
                $participants[] = array('reportId' => $participant['reportId'],
                                        'participantRole' => reportRole($participant['participantRole']),
                                        'participantName' => $participant['participantName']);
            }
        }
        if (isset($participants)) {
            return $participants;
        } else {
            return false;
        }
    }

    function reportCases($reportId)
    {
        global $database,$user;
        $sqlFilter = '';
        if ($user['aclRoot'] < 1) {
            $sqlFilter = " AND ".DB_PREFIX."case.deleted = 0 ";
        }
        $caseSql = "SELECT
            ".DB_PREFIX."case.*
            FROM ".DB_PREFIX."case
            JOIN ".DB_PREFIX."ar2c on ".DB_PREFIX."case.id = ".DB_PREFIX."ar2c.idcase
            WHERE ".DB_PREFIX."ar2c.idreport = $reportId $sqlFilter";
        if ($caseList = mysqli_query($database, $caseSql)) {
            while ($case = mysqli_fetch_assoc($caseList)) {
                $cases[] = array('caseId' => $case['id'],
                                 'caseName' => $case['title']);
            }
        }
        if (isset($cases)) {
            return $cases;
        } else {
            return false;
        }
    }

    function reportSymbols($reportId)
    {
        global $database,$user;
        $sqlFilter = ' AND '.DB_PREFIX.'symbol.secret <= '.$user['aclSecret'];
        if ($user['aclRoot'] < 1) {
            $sqlFilter = " AND ".DB_PREFIX."symbol.deleted = 0 ";
        }
        $symbolSql = "SELECT
            ".DB_PREFIX."symbol2all.*,
            ".DB_PREFIX."symbol.*,
            ".DB_PREFIX."user.userName,
            ".DB_PREFIX."person.*
            FROM ".DB_PREFIX."symbol2all
            JOIN ".DB_PREFIX."symbol on ".DB_PREFIX."symbol2all.idsymbol = ".DB_PREFIX."symbol.id
            JOIN ".DB_PREFIX."user ON ".DB_PREFIX."symbol.created_by = ".DB_PREFIX."user.userId
            JOIN ".DB_PREFIX."person ON ".DB_PREFIX."user.personId = ".DB_PREFIX."person.id
            WHERE
            ".DB_PREFIX."symbol.assigned=0
            AND ".DB_PREFIX."symbol2all.idrecord=".$reportId."
            AND ".DB_PREFIX."symbol2all.table=4 ".$sqlFilter;

        if ($symbolList = mysqli_query($database, $symbolSql)) {
            while ($symbol = mysqli_fetch_assoc($symbolList)) {
                $symbols[] = array( 'symbolId' => $symbol['id'],
                                    'symbolHash' => $symbol['symbol'],
                                    'symbolDeleted' => $symbol['deleted'],
                                    'symbolCreated' => webdateTime($symbol['created']),
                                    'symbolCreatedBy' => $symbol['created_by'],
                                    'symbolCreatedByPerson' => $symbol['name'].' '.$symbol['surname'],
                                    'symbolCreatedByUser' => $symbol['userName'],
                                    'symbolModified' => $symbol['modified'],
                                    'symbolModifiedBy' => $symbol['modified_by']);
            }
        }
        if (isset($symbols)) {
            return $symbols;
        } else {
            return false;
        }
    }

    function reportNotes($reportId)
    {
        global $database,$user;
        $sqlFilter = '';
//        $sqlFilter = 'AND ('.DB_PREFIX.'note.secret <= '.$user['aclSecret'].' OR '.DB_PREFIX.'note.iduser='.$user['userId'].' )';
        if ($user['aclRoot'] < 1) {
            $sqlFilter = " AND ".DB_PREFIX."note.deleted = 0 ";
        }
        $noteSql = "SELECT
            ".DB_PREFIX."note.*,
            ".DB_PREFIX."user.userName,
            ".DB_PREFIX."person.*
        FROM ".DB_PREFIX."note
        JOIN ".DB_PREFIX."user ON ".DB_PREFIX."note.iduser = ".DB_PREFIX."user.userId
        JOIN ".DB_PREFIX."person ON ".DB_PREFIX."user.personId = ".DB_PREFIX."person.id
        WHERE ".DB_PREFIX."note.iditem=$reportId AND ".DB_PREFIX."note.idtable=4 $sqlFilter
        ORDER BY ".DB_PREFIX."note.datum DESC";
        if ($noteList = mysqli_query($database, $noteSql)) {
            while ($note = mysqli_fetch_assoc($noteList)) {
                $notes[] = array(   'noteId' => $note['id'],
                                    'noteCreated' => webdateTime($note['datum']),
                                    'noteCreatedBy' => $note['iduser'],
                                    'noteCreatedByPerson' => $note['name'].' '.$note['surname'],
                                    'noteCreatedByUser' => $note['userName'],
                                    'noteTitle' => $note['title'],
                                    'noteNote' => $note['note'],
                                    'noteDeleted' => $note['deleted'],
                                    'noteSecret' => $note['secret']);
            }
        }
        if (isset($notes)) {
            return $notes;
        } else {
            return false;
        }
    }

    function reportFiles($reportId)
    {
        global $database,$user,$config;
        $sqlFilter = DB_PREFIX.'file.secret <= '.$user['aclSecret'];
        // if ($user['aclRoot'] < 1) {
        //     $sqlFilter = " AND ".DB_PREFIX."symbol.deleted = 0 ";
        // }
        $fileSql = "SELECT
            ".DB_PREFIX."file.*,
            ".DB_PREFIX."user.userName,
            ".DB_PREFIX."person.*
            FROM ".DB_PREFIX."file
            JOIN ".DB_PREFIX."user ON ".DB_PREFIX."file.iduser = ".DB_PREFIX."user.userId
            JOIN ".DB_PREFIX."person ON ".DB_PREFIX."user.personId = ".DB_PREFIX."person.id
            WHERE $sqlFilter AND ".DB_PREFIX."file.iditem=$reportId AND ".DB_PREFIX."file.idtable=4
            ORDER BY ".DB_PREFIX."file.datum ASC";
        if ($fileList = mysqli_query($database, $fileSql)) {
            while ($file = mysqli_fetch_assoc($fileList)) {
                $image = false;
                if (in_array($file['mime'], $config['mime-image'], true)) {
                    $image = true;
                }
                $files[] = array(   'fileId' => $file['id'],
                                    'fileMime' => $file['mime'],
                                    'fileSecret' => $file['secret'],
                                    'fileHas' => $file['uniquename'],
                                    'fileName' => $file['originalname'],
                                    'fileCreatedBy' => $file['iduser'],
                                    'fileCreatedByPerson' => $file['name'].' '.$file['surname'],
                                    'fileCreatedByUser' => $file['userName'],
                                    'fileCreated' => webdateTime($file['datum']),
                                    'fileIsImage' => $image);
            }
        }
        if (isset($files)) {
            return $files;
        } else {
            return false;
        }
    }
