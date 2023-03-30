<?php


function AuthorDB($userId)
{
    global $database,$text;
    $authorSql = 'SELECT ' . DB_PREFIX . 'user.userName, concat( ' . DB_PREFIX . 'person.name, " ", ' . DB_PREFIX . 'person.surname) as personName
            FROM ' . DB_PREFIX . 'user
            LEFT JOIN ' . DB_PREFIX . 'person ON ' . DB_PREFIX . 'user.personId = ' . DB_PREFIX . 'person.id
            WHERE ' . DB_PREFIX . 'user.userId=' . $userId;
    $authorQuery = mysqli_query($database, $authorSql);
    if (mysqli_num_rows($authorQuery) > 0) {
        $authorResult = mysqli_fetch_assoc($authorQuery);
        return Author(
            $authorResult['userName'],
            $authorResult['personName']
        );
    } else {
        return $text['warningInformationUnavailable'];
    }
}


function Author($userName,$personName)
{
    if ($personName) {
        return $personName;
    } else {
        return $userName;
    }
}


/**
 * get user details.
 *
 * @param string userId
 * @param mixed $userId
 *
 * @return array nw_user row
 *               TODO osetrit ze nevydim prava nizsi nez mam sam
 */
function userRead($userId): array
{
    global $database, $text, $user;
    $querySql = "SELECT * from ".DB_PREFIX."user where userDeleted <= ".$user['aclRoot']." AND userId=".$userId;
    $query = mysqli_query($database, $querySql);
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        $user[] = $text['zaznamnenalezen'];
    }

    return $user;
}

/**
 * user list array.
 *
 * @param string where where clause for SQL
 * @param string order order by clause for SQL
 * @param mixed $where
 *
 * @return array nw_user array
 *               TODO strankovani
 *               TODO osetrit ze nevydim prava nizsi nez mam sam
 */
function userList($where = 1): array
{
    global $database, $user, $text;

    if (mb_strlen($where) < 1) {
        $where = 1;
    }

    $sql = "SELECT * FROM ".DB_PREFIX."user  left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.personId=".DB_PREFIX."person.id WHERE userDeleted <= ".$user['aclRoot']." AND ($where) ".sortingGet('user', 'person');
    sortingGet('user', 'person');
    $query = mysqli_query($database, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($users = mysqli_fetch_assoc($query)) {
            if ($users['lastLogin'] < 1) {
                $users['lastLogin'] = $text['nikdy'];
            } else {
                $users['lastLogin'] = webdatetime($users['lastLogin']);
            }
            $userList[] = $users;
        }
    } else {
        $userList[] = $text['prazdnyvypis'];
    }

    return $userList;
}

/**
 * user change parameters.
 *
 * @param int userId
 * @param array data[key]=value
 * @param string success message
 * @param string failure message
 * @param mixed      $userId
 * @param mixed      $data
 * @param mixed|null $success
 * @param mixed|null $failure
 */
function userChange($userId, $data, $success = null, $failure = null): string
{
    global $database, $latteParameters;
    $chain = "";
    foreach ($data as $column => $value) {
        if (DBcolumnExist('user', $column) and mb_strlen(trim($value)) > 0) {
            $chain .= " $column = '".trim($value)."',";
        }
    }
    if (mb_strlen($chain) > 0) {
        $sql = "UPDATE ".DB_PREFIX."user SET ".rtrim($chain, ",")."  where userId=".$userId;
        mysqli_query($database, $sql);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $success;
        } else {
            $latteParameters['message'] = $failure;
        }
    }

    return $chain;
}
function listUsersSuitable()
{
    global $database;
    $listUsersSql = "(select userId, userName, concat(surname,', ',name,' [',username,']') as personName from nw_report
        left join nw_user on nw_report.reportOwner = nw_user.userId
        left join nw_person on nw_user.personId = nw_person.id
        where reportOwner > 0
        group by reportOwner)
        union distinct
        (select userId,  userName, concat(surname,', ',name,' [',username,']') as personName from nw_user
        left join nw_person on nw_user.personId = nw_person.id
        where userDeleted = 0
        group by userId)
        union distinct
        (select userId, userName, concat(surname,', ',name,' [',username,']') as personName from nw_c2s
        left join nw_user on nw_c2s.iduser = nw_user.userId
        left join nw_person on nw_user.personId = nw_person.id
        group by userId)
        order by userName";
    $listUsers = mysqli_query($database, $listUsersSql);
    if (mysqli_num_rows($listUsers) > 0) {
        while ($user = mysqli_fetch_assoc($listUsers)) {
            $users[] = $user;
        }
        return $users;
    } else {
        return false;
    }
}
