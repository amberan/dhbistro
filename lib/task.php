<?php
use Tracy\Debugger;

Debugger::enable(Debugger::DETECT, $config['folder_logs']);

/**
 * list unfinished tasks assigned to.
 *
 * @param int userId
 * @param mixed $userid
 *
 * @return array [id][name]
 */
function tasksAssignedTo($userid)
{
    global $database;

    $unfinishedtasks[] = [];

    $tasksListSql = "SELECT *
    FROM ".DB_PREFIX."task
    WHERE ".DB_PREFIX."task.iduser=".$userid." AND ".DB_PREFIX."task.status=0
    ORDER BY ".DB_PREFIX."task.created ASC";
    $tasksList = mysqli_query($database, $tasksListSql);
    while ($unfinishedtask = mysqli_fetch_assoc($tasksList)) {
        $unfinishedtasks[] = [$unfinishedtask['id'], $unfinishedtask['task']];
    }
    return @$unfinishedtasks;
}
