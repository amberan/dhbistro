<?php
/**
 * Retrieves the author's name from the database based on the provided user ID.
 *
 * @param  int    $userId The ID of the user to retrieve the author's name for
 * @return string the author's name or a message indicating that the information is unknown
 */
function AuthorDB($userId)
{
    global $database,$text,$user;
    if ($userId > 0) {
        $authorSql = 'SELECT ' . DB_PREFIX . 'user.userName,
                    concat( ' . DB_PREFIX . 'person.name, " ", ' . DB_PREFIX . 'person.surname) as personName,
                    ' . DB_PREFIX . 'person.secret
                FROM ' . DB_PREFIX . 'user
                LEFT JOIN ' . DB_PREFIX . 'person ON ' . DB_PREFIX . 'user.personId = ' . DB_PREFIX . 'person.id
                WHERE ' . DB_PREFIX . 'user.userId=' . $userId;
        $authorQuery = mysqli_query($database, $authorSql);
        if (mysqli_num_rows($authorQuery) > 0) {
            $authorResult = mysqli_fetch_assoc($authorQuery);
            if ($authorResult['secret'] > $user['aclSecret']) {
                $authorResult['personName'] = '';
            }

            return Author(
                $authorResult['userName'],
                $authorResult['personName']
            );
        } else {
            return $text['notificationInformationUnknown'];
        }
    } else {
        return $text['notificationInformationUnknown'];
    }
}
/**
 * Returns the author's name based on the provided user name and person name prefered is personName.
 *
 * @param  string      $userName   the user name
 * @param  string|null $personName the person name, which can be null or an empty string
 * @return string      the author's name, which is either the `$personName` or the `$userName`
 */
function Author($userName, $personName)
{
    if (isset($personName) && strlen($personName) > 0) {
        return $personName;
    } else {
        return $userName;
    }
}

/**
 * Retrieves a user record from the database based on the provided user ID.
 *
 * @param  int          $userId the ID of the user to retrieve
 * @return array|string the user record as an associative array if found, or a string message indicating that the record was not found
 */
function userRead($userId): array|string
{
    global $database, $text, $user;
    $querySql = "SELECT * from " . DB_PREFIX . "user where userDeleted <= " . $user['aclRoot'] . " AND userId=" . $userId;
    $query = mysqli_query($database, $querySql);
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        $user[] = $text['notificationRecordNotFound'];
    }

    return $user;
}

/**
 * Retrieves a list of users from the database based on the provided conditions.
 *
 * @param  string $where Optional. A SQL WHERE condition to filter the user records. Defaults to "1".
 * @return array  An array of user records. If no users are found, it returns an array with a single element indicating that the list is empty.
 */
function userList($where = "1"): array
{
    global $database, $user, $text;

    if (mb_strlen($where) <= 1) {
        $where = 1;
    }

    $sql = "SELECT * FROM " . DB_PREFIX . "user  left outer join `" . DB_PREFIX . "person` on " . DB_PREFIX . "user.personId=" . DB_PREFIX . "person.id WHERE userDeleted <= " . $user['aclRoot'] . " AND ($where) " . sortingGet('user', 'person');
    sortingGet('user', 'person');
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        $userList = [];
        while ($users = mysqli_fetch_assoc($query)) {
            if ($users['lastLogin'] < 1) {
                $users['lastLogin'] = $text['notificationInformationUnknown'];
            } else {
                $users['lastLogin'] = webdatetime($users['lastLogin']);
            }
            $userList[] = $users;
        }
    } else {
        $userList[] = $text['notificationListEmpty'];
    }

    return $userList;
}

/**
 * Updates user data in the database.
 *
 * @param  int         $userId  The ID of the user to update
 * @param  array       $data    An associative array where the keys are column names and the values are the new data to set
 * @param  string|null $success Optional. A success message to set if the update is successful.
 * @param  string|null $failure Optional. A failure message to set if the update fails.
 * @return string      the constructed SQL SET clause, or an empty string if no columns were updated
 */
function userChange($userId, $data, $success = null, $failure = null): string
{
    global $database, $latteParameters;
    $chain = "";
    foreach ($data as $column => $value) {
        if (DBcolumnExist('user', $column) and mb_strlen(trim($value . '')) > 0) {
            $chain .= " $column = '" . trim($value) . "',";
        }
    }
    if (mb_strlen($chain) > 0) {
        $sql = "UPDATE " . DB_PREFIX . "user SET " . rtrim($chain, ",") . "  where userId=" . $userId;
        mysqli_query($database, $sql);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $success;
        } else {
            $latteParameters['message'] = $failure;
        }
    }

    return $chain;
}
/**
 * Retrieves a list of suitable users from the database.
 *
 * @return array|false an array of user records if users are found, or false if no users are found
 */
function listUsersSuitable()
{
    global $database;
    $listUsersSql = "(select userId, userName, concat(surname,', ',name,' [',username,']') as personName from " . DB_PREFIX . "report
        left join " . DB_PREFIX . "user on " . DB_PREFIX . "report.reportOwner = " . DB_PREFIX . "user.userId
        left join " . DB_PREFIX . "person on " . DB_PREFIX . "user.personId = " . DB_PREFIX . "person.id
        where reportOwner > 0
        group by reportOwner)
        union distinct
        (select userId,  userName, concat(surname,', ',name,' [',username,']') as personName from " . DB_PREFIX . "user
        left join " . DB_PREFIX . "person on " . DB_PREFIX . "user.personId = " . DB_PREFIX . "person.id
        where userDeleted = 0
        group by userId)
        union distinct
        (select userId, userName, concat(surname,', ',name,' [',username,']') as personName from " . DB_PREFIX . "c2s
        left join " . DB_PREFIX . "user on " . DB_PREFIX . "c2s.iduser = " . DB_PREFIX . "user.userId
        left join " . DB_PREFIX . "person on " . DB_PREFIX . "user.personId = " . DB_PREFIX . "person.id
        group by userId)
        order by userName";
    $listUsers = mysqli_query($database, $listUsersSql);
    if (mysqli_num_rows($listUsers) > 0) {
        $users = [];
        while ($user = mysqli_fetch_assoc($listUsers)) {
            $users[] = $user;
        }

        return $users;
    } else {
        return false;
    }
}
