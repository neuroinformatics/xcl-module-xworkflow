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

// install utilities
$langman->set('INSTALL_ERROR_MODULE_INSTALLED', 'Module not installed.');
$langman->set('INSTALL_ERROR_PERM_ADMIN_SET', 'Module admin permission could not set.');
$langman->set('INSTALL_ERROR_PERM_READ_SET', 'Module read permission could not set.');
$langman->set('INSTALL_MSG_MODULE_INSTALLED', 'Module "{0}" has installed.');
$langman->set('INSTALL_ERROR_SQL_FILE_NOT_FOUND', 'SQL file "{0}" is not found.');
$langman->set('INSTALL_MSG_DB_SETUP_FINISHED', 'Database setup is finished.');
$langman->set('INSTALL_MSG_SQL_SUCCESS', 'SQL success : {0}');
$langman->set('INSTALL_MSG_SQL_ERROR', 'SQL error : {0}');
$langman->set('INSTALL_MSG_TPL_INSTALLED', 'Template "{0}" is installed.');
$langman->set('INSTALL_ERROR_TPL_INSTALLED', 'Template "{0}" could not installed.');
$langman->set('INSTALL_ERROR_TPL_UNINSTALLED', 'Template "{0}" could not uninstalled.');
$langman->set('INSTALL_MSG_BLOCK_INSTALLED', 'Block "{0}" is installed.');
$langman->set('INSTALL_ERROR_BLOCK_COULD_NOT_LINK', 'Block "{0}" could not link to module.');
$langman->set('INSTALL_ERROR_PERM_COULD_NOT_SET', 'Block permission of "{0}" could not set.');
$langman->set('INSTALL_ERROR_BLOCK_PERM_SET', 'Block permission of "{0}" could not set.');
$langman->set('INSTALL_MSG_BLOCK_TPL_INSTALLED', 'Block template "{0}" is installed.');
$langman->set('INSTALL_ERROR_BLOCK_TPL_INSTALLED', 'Block template "{0}" could not installed.');
$langman->set('INSTALL_MSG_BLOCK_UNINSTALLED', 'Block "{0}" is uninstalled.');
$langman->set('INSTALL_ERROR_BLOCK_UNINSTALLED', 'Block "{0}" could not uninstalled.');
$langman->set('INSTALL_ERROR_BLOCK_PERM_DELETE', 'Block permission of "{0}" could not deleted.');
$langman->set('INSTALL_MSG_BLOCK_UPDATED', 'Block "{0}" is updated.');
$langman->set('INSTALL_ERROR_BLOCK_UPDATED', 'Block "{0}" could not updated.');
$langman->set('INSTALL_ERROR_BLOCK_INSTALLED', 'Block "{0}" could not installed.');
$langman->set('INSTALL_MSG_BLOCK_TPL_UNINSTALLED', 'Block template "{0}" is uninstalled.');
$langman->set('INSTALL_MSG_CONFIG_ADDED', 'Config "{0}" is added.');
$langman->set('INSTALL_ERROR_CONFIG_ADDED', 'Config "{0}" could not added.');
$langman->set('INSTALL_MSG_CONFIG_DELETED', 'Config "{0}" is deleted.');
$langman->set('INSTALL_ERROR_CONFIG_DELETED', 'Config "{0}" could not deleted.');
$langman->set('INSTALL_MSG_CONFIG_UPDATED', 'Config "{0}" is updated.');
$langman->set('INSTALL_ERROR_CONFIG_UPDATED', 'Config "{0}" could not updated.');
$langman->set('INSTALL_ERROR_CONFIG_NOT_FOUND', 'Config is not found.');
$langman->set('INSTALL_MSG_MODULE_INFORMATION_DELETED', 'Module information is deleted.');
$langman->set('INSTALL_ERROR_MODULE_INFORMATION_DELETED', 'Module information could not deleted.');
$langman->set('INSTALL_MSG_TABLE_DOROPPED', 'Table "{0}" is doropped.');
$langman->set('INSTALL_ERROR_TABLE_DOROPPED', 'Table "{0}" could not doropped.');
$langman->set('INSTALL_ERROR_BLOCK_TPL_DELETED', 'Block template could not deleted.<br />{0}');
$langman->set('INSTALL_MSG_MODULE_UNINSTALLED', 'Module "{0}" is uninstalled.');
$langman->set('INSTALL_ERROR_MODULE_UNINSTALLED', 'Module "{0}" could not uninstalled.');
$langman->set('INSTALL_MSG_UPDATE_STARTED', 'Module update started.');
$langman->set('INSTALL_MSG_UPDATE_FINISHED', 'Module update is finished.');
$langman->set('INSTALL_ERROR_UPDATE_FINISHED', 'Module could not updated.');
$langman->set('INSTALL_MSG_MODULE_UPDATED', 'Module "{0}" is updated.');
$langman->set('INSTALL_ERROR_MODULE_UPDATED', 'Module "{0}" could not updated.');

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
