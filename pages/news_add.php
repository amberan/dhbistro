<?php

use Tracy\Debugger;
    Debugger::enable(Debugger::DETECT,$config['folder_logs']);

latteDrawTemplate('sparklet');
latteDrawTemplate('news_add');