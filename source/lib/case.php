<?php

/**
 * Lists unfinished cases assigned to a specific user.
 *
 * @param  int   $userid the ID of the user to retrieve the cases for
 * @return array an array of unfinished cases, each containing the case ID and title
 */
function casesAssignedTo($userid): array
{
    global $database;

    $unfinishedcases[] = [];

    $casesListSql = "SELECT " . DB_PREFIX . "case.id AS 'id', " . DB_PREFIX . "case.title AS 'title'
		FROM " . DB_PREFIX . "c2s, " . DB_PREFIX . "case
		WHERE " . DB_PREFIX . "case.id=" . DB_PREFIX . "c2s.idcase AND " . DB_PREFIX . "case.status=0 AND " . DB_PREFIX . "c2s.idsolver=" . $userid . "
        ORDER BY " . DB_PREFIX . "case.title ASC";
    if ($casesList = mysqli_query($database, $casesListSql)) {
        while ($unfinishedcase = mysqli_fetch_assoc($casesList)) {
            $unfinishedcases[] = [$unfinishedcase['id'], $unfinishedcase['title']];
        }
    }

    return @$unfinishedcases;
}

//TODO vypis pripadu musi oznacovat NEW - if (@$filter['new'] == null || ($filter['new'] == on && searchRecord(3,$rec['id']))) {
//TODO pridat sloupec pro created
//TODO prejmenovat datum za edited

/**
 * Lists cases based on their status (open or closed).
 *
 * @param  bool|null   $closed Optional. If true, lists closed cases; if false, lists open cases; if null, lists all cases.
 * @return array|false an array of case records if cases are found, or false if no cases are found
 */
function listCases($closed = null)
{
    global $user,$database;
    if (!$closed) {
        $sqlFilter = DB_PREFIX . 'case.status = 0 AND ';
    }
    $sqlFilter .= DB_PREFIX . 'case.deleted <= ' . $user['aclRoot'] . ' AND ' . DB_PREFIX . 'case.secret <= ' . $user['aclSecret'];
    echo $caseListSql = 'SELECT
        ' . DB_PREFIX . 'case.*
        FROM ' . DB_PREFIX . 'case
        LEFT JOIN ' . DB_PREFIX . 'c2s as caseSolver on ' . DB_PREFIX . 'case.id = caseSolver.idcase
        LEFT JOIN ' . DB_PREFIX . 'user as caseSolverUser on caseSolver.iduser = caseSolverUser.userId
        LEFT JOIN ' . DB_PREFIX . 'person as caseSolverPerson on caseSolverUser.personId = caseSolverPerson.id AND caseSolverPerson.deleted = 0 AND caseSolverPerson.secret <= ' . $user['aclSecret'] . '
        WHERE ' . $sqlFilter;
    $caseList = mysqli_query($database, $caseListSql);
    if (mysqli_num_rows($caseList) > 0) {
        while ($case = mysqli_fetch_assoc($caseList)) {
            $cases[] = $case;
        }

        return $cases;
    } else {
        return false;
    }
}
