<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

/** 
* get user details
* @param string userId
* @return array nw_user row
* TODO osetrit ze nevydim prava nizsi nez mam sam
*/
function userRead($userId): array
{
    global $database, $text, $user;

    $querySql = "SELECT * from ".DB_PREFIX."user where userDeleted <= ".$user['aclRoot']." AND userId=".$userId;

    $query = mysqli_query($database,$querySql);
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        $person[] = $text['zaznamnenalezen'];
    }

    return $person;
}

/** 
* user list array
* @param string where where clause for SQL
* @param string order order by clause for SQL
* @return array nw_user array
* TODO strankovani
* TODO osetrit ze nevydim prava nizsi nez mam sam
*/
function userList($where = 1): array
{
    global $database, $user, $text;

    if (mb_strlen($where) < 1) {
        $where = 1;
    }
    
    $sql = "SELECT * FROM ".DB_PREFIX."user  left outer join `".DB_PREFIX."person` on ".DB_PREFIX."user.personId=".DB_PREFIX."person.id WHERE userDeleted <= ".$user['aclRoot']." AND ($where) ".sortingGet('user','person');
    sortingGet('user','person');
    $query = mysqli_query($database,$sql);
    if (mysqli_num_rows($query) > 0) {
        while ($users = mysqli_fetch_assoc ($query)) {
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
 * user change parameters
 * @param int userId
 * @param array data[key]=value
 * @param string success message
 * @param string failure message
 */
function userChange($userId, $data, $success = null, $failure = null): string
{
    global $database, $latteParameters;
    $chain = "";
    foreach ($data as $column => $value) {
        if (DBcolumnExist('user',$column) AND mb_strlen($value) > 0) {
            $chain .= " $column = '$value',";
        }
    }
    if (mb_strlen($chain) > 0) {
        $sql = "UPDATE ".DB_PREFIX."user SET ".rtrim($chain, ",")."  where userId=".$userId;
        mysqli_query($database,$sql);
        if (mysqli_affected_rows($database) > 0) {
            $latteParameters['message'] = $success;
        } else {
            $latteParameters['message'] = $failure;
        }
    }

    return $chain;
}