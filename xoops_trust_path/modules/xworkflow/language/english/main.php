<?php

use Xworkflow\Core\LanguageManager;

if (!isset($mydirname)) {
    exit();
}

$langman = new LanguageManager($mydirname, 'main');

if ($langman->exists('LOADED')) {
    return;
}

// system
$langman->set('LOADED', 1);

$langman->set('ERROR_REQUIRED', '{0} is required.');
$langman->set('ERROR_MINLENGTH', 'Input {0} with {1} or more characters.');
$langman->set('ERROR_MAXLENGTH', 'Input {0} with {1} or less characters.');
$langman->set('ERROR_EXTENSION', 'Uploaded file\'s extension does not match any entry in the allowed list.');
$langman->set('ERROR_INTRANGE', 'Incorrect input on {0}.');
$langman->set('ERROR_MIN', 'Input {0} with {1} or more numeric value.');
$langman->set('ERROR_MAX', 'Input {0} with {1} or less numeric value.');
$langman->set('ERROR_OBJECTEXIST', 'Incorrect input on {0}.');
$langman->set('ERROR_DBUPDATE_FAILED', 'Failed updating database.');
$langman->set('ERROR_EMAIL', '{0} is an incorrect email address.');
$langman->set('ERROR_CONTENT_IS_NOT_FOUND', 'Content is not found');
$langman->set('ERROR_ITEM_REMAINS', 'This User has some tasks at his step. Try again after all of these tasks approval/reject');
$langman->set('ERROR_NO_PERMISSION', 'You don\'t have permission');
$langman->set('ERROR_DUPLICATED_STEP', 'This STEP is already assigned.');
$langman->set('LANG_ADD_A_NEW_APPROVAL', 'Add a new Approval User');
$langman->set('LANG_CHOOSE_FROM_HERE', '-- Choose From Here --');
$langman->set('LANG_APPROVAL_ID', 'Approval ID');
$langman->set('LANG_UID', 'Approval User');
$langman->set('LANG_GID', 'Approval Group');
$langman->set('LANG_CREATED_BY', 'Created by');
$langman->set('LANG_APPROVED_BY', 'Approved by');
$langman->set('LANG_TARGET_MODULE', 'Workflow');
$langman->set('LANG_DIRNAME', 'Module Name');
$langman->set('LANG_DATANAME', 'Workflow Name');
$langman->set('LANG_STEP', 'Step');
$langman->set('LANG_GROUP_ADMIN', '[Group Administrator]');
$langman->set('LANG_CONTROL', 'CONTROL');
$langman->set('LANG_APPROVAL_ADD', 'Approval Add');
$langman->set('LANG_APPROVAL_EDIT', 'Approval Edit');
$langman->set('LANG_APPROVAL_DELETE', 'Approval Delete');
$langman->set('LANG_ADD_A_NEW_ITEM', 'Add a new Task');
$langman->set('LANG_ITEM_ID', 'Task ID');
$langman->set('LANG_TARGET_ID', 'Item ID');
$langman->set('LANG_STATUS', 'Status');
$langman->set('LANG_POSTTIME', 'posted time');
$langman->set('LANG_APPROVEDTIME', 'Approved time');
$langman->set('LANG_DELETETIME', 'deleted time');
$langman->set('LANG_ITEM_VIEW', 'Task Detail');
$langman->set('LANG_ITEM_EDIT', 'Task Edit');
$langman->set('LANG_ITEM_DELETE', 'Task Delete');
$langman->set('LANG_ADD_A_NEW_HISTORY', 'Add a new history');
$langman->set('LANG_PROGRESS_ID', 'Progress ID');
$langman->set('LANG_RESULT', 'Result');
$langman->set('LANG_COMMENT', 'Comment');
$langman->set('LANG_HISTORY', 'History');
$langman->set('LANG_HISTORY_EDIT', 'History Edit');
$langman->set('LANG_HISTORY_DELETE', 'History Delete');
$langman->set('LANG_STATUS_DELETED', 'deleted');
$langman->set('LANG_STATUS_REJECTED', 'rejected');
$langman->set('LANG_STATUS_PROGRESS', 'In Progress');
$langman->set('LANG_STATUS_FINISHED', 'finished');
$langman->set('LANG_RESULT_HOLD', 'Hold');
$langman->set('LANG_RESULT_REJECT', 'Reject');
$langman->set('LANG_RESULT_APPROVE', 'Approve');
$langman->set('LANG_APPROVE_ITEM', 'Approve the Task');
$langman->set('LANG_TITLE', 'Title');
$langman->set('LANG_APPROVAL_LIST', 'Approval Users List');
$langman->set('LANG_ITEM_LIST', 'Task List');
$langman->set('LANG_HISTORY_LIST', 'History List');
$langman->set('LANG_MYTASK_LIST', 'My Task List');
$langman->set('LANG_NUMBER_OF_ITEMS', 'Number of Tasks');
$langman->set('MESSAGE_ADD_A_APPROVAL', 'At first, you should add approvals for each document.');
$langman->set('MESSAGE_CONFIRM_DELETE', 'Are you sure to delete?');
$langman->set('MESSAGE_NO_APPROVALS_EXIST', 'No approvals are registered.');
$langman->set('MESSAGE_NO_TASK', 'You have no task to approve.');
