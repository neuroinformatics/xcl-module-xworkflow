<?php

use Xworkflow\Core\LanguageManager;

if (!isset($mydirname)) {
    exit();
}
// require to load common file.
require_once dirname(dirname(__DIR__)).'/include/common.php';
$langman = new LanguageManager($mydirname, 'modinfo');

if ($langman->exists('LOADED')) {
    return;
}

// system
$langman->set('LOADED', 1);

// general information
$langman->set('NAME', 'Workflow');
$langman->set('DESC', 'Extended Legacy_Workflow Module');
$langman->set('AUTHOR', 'Neuroinformatics Japan Center, RIKEN BSI <https://nijc.brain.riken.jp/>');
$langman->set('CREDITS', 'XooNIps Project');

// templates
$langman->set('TPL_APPROVAL_LIST', 'APPROVAL_LIST');
$langman->set('TPL_APPROVAL_EDIT', 'APPROVAL_EDIT');
$langman->set('TPL_APPROVAL_DELETE', 'APPROVAL_DELETE');
$langman->set('TPL_ITEM_LIST', 'TASK_LIST');
$langman->set('TPL_ITEM_VIEW', 'TASK_VIEW');
$langman->set('TPL_HISTORY_EDIT', 'HISTORY_EDIT');
$langman->set('TPL_STYLE_CSS', 'Cascading Style Sheets');
$langman->set('TPL_INC_MENU', 'Header Menu');

// submenu
$langman->set('SUB_MYTASK_LIST', 'My Task List');
$langman->set('SUB_ITEM_LIST', 'Task List');
$langman->set('SUB_APPROVAL_LIST', 'Approval Users List');
$langman->set('SUB_APPROVAL_ADD', 'Approval Add');

// preferences
$langman->set('LANG_REVERT_TO', 'Where does the task go back when it was reverted ?');
$langman->set('DESC_REVERT_TO', '');
$langman->set('REVERTTO_ZERO', 'To the poster');
$langman->set('REVERTTO_FORMER', 'To the former approval');
