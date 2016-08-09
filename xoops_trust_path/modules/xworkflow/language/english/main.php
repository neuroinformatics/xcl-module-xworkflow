<?php

if (!isset($mydirname)) {
    exit();
}

$constpref = '_MD_'.strtoupper($mydirname);

if (defined($constpref.'_LOADED')) {
    return;
}

// system
define($constpref.'_LOADED', 1);

define($constpref.'_ERROR_REQUIRED', '{0} is required.');
define($constpref.'_ERROR_MINLENGTH', 'Input {0} with {1} or more characters.');
define($constpref.'_ERROR_MAXLENGTH', 'Input {0} with {1} or less characters.');
define($constpref.'_ERROR_EXTENSION', 'Uploaded file\'s extension does not match any entry in the allowed list.');
define($constpref.'_ERROR_INTRANGE', 'Incorrect input on {0}.');
define($constpref.'_ERROR_MIN', 'Input {0} with {1} or more numeric value.');
define($constpref.'_ERROR_MAX', 'Input {0} with {1} or less numeric value.');
define($constpref.'_ERROR_OBJECTEXIST', 'Incorrect input on {0}.');
define($constpref.'_ERROR_DBUPDATE_FAILED', 'Failed updating database.');
define($constpref.'_ERROR_EMAIL', '{0} is an incorrect email address.');
define($constpref.'_ERROR_CONTENT_IS_NOT_FOUND', 'Content is not found');
define($constpref.'_ERROR_ITEM_REMAINS', 'This User has some tasks at his step. Try again after all of these tasks approval/reject');
define($constpref.'_ERROR_NO_PERMISSION', 'You don\'t have permission');
define($constpref.'_ERROR_DUPLICATED_STEP', 'This STEP is already assigned.');
define($constpref.'_LANG_ADD_A_NEW_APPROVAL', 'Add a new Approval User');
define($constpref.'_LANG_CHOOSE_FROM_HERE', '-- Choose From Here --');
define($constpref.'_LANG_APPROVAL_ID', 'Approval ID');
define($constpref.'_LANG_UID', 'Approval User');
define($constpref.'_LANG_GID', 'Approval Group');
define($constpref.'_LANG_CREATED_BY', 'Created by');
define($constpref.'_LANG_APPROVED_BY', 'Approved by');
define($constpref.'_LANG_TARGET_MODULE', 'Workflow');
define($constpref.'_LANG_DIRNAME', 'Module Name');
define($constpref.'_LANG_DATANAME', 'Workflow Name');
define($constpref.'_LANG_STEP', 'Step');
define($constpref.'_LANG_GROUP_ADMIN', '[Group Administrator]');
define($constpref.'_LANG_CONTROL', 'CONTROL');
define($constpref.'_LANG_APPROVAL_ADD', 'Approval Add');
define($constpref.'_LANG_APPROVAL_EDIT', 'Approval Edit');
define($constpref.'_LANG_APPROVAL_DELETE', 'Approval Delete');
define($constpref.'_LANG_ADD_A_NEW_ITEM', 'Add a new Task');
define($constpref.'_LANG_ITEM_ID', 'Task ID');
define($constpref.'_LANG_TARGET_ID', 'Item ID');
define($constpref.'_LANG_STATUS', 'Status');
define($constpref.'_LANG_POSTTIME', 'posted time');
define($constpref.'_LANG_APPROVEDTIME', 'Approved time');
define($constpref.'_LANG_DELETETIME', 'deleted time');
define($constpref.'_LANG_ITEM_VIEW', 'Task Detail');
define($constpref.'_LANG_ITEM_EDIT', 'Task Edit');
define($constpref.'_LANG_ITEM_DELETE', 'Task Delete');
define($constpref.'_LANG_ADD_A_NEW_HISTORY', 'Add a new history');
define($constpref.'_LANG_PROGRESS_ID', 'Progress ID');
define($constpref.'_LANG_RESULT', 'Result');
define($constpref.'_LANG_COMMENT', 'Comment');
define($constpref.'_LANG_HISTORY', 'History');
define($constpref.'_LANG_HISTORY_EDIT', 'History Edit');
define($constpref.'_LANG_HISTORY_DELETE', 'History Delete');
define($constpref.'_LANG_STATUS_DELETED', 'deleted');
define($constpref.'_LANG_STATUS_REJECTED', 'rejected');
define($constpref.'_LANG_STATUS_PROGRESS', 'In Progress');
define($constpref.'_LANG_STATUS_FINISHED', 'finished');
define($constpref.'_LANG_RESULT_HOLD', 'Hold');
define($constpref.'_LANG_RESULT_REJECT', 'Reject');
define($constpref.'_LANG_RESULT_APPROVE', 'Approve');
define($constpref.'_LANG_APPROVE_ITEM', 'Approve the Task');
define($constpref.'_LANG_TITLE', 'Title');
define($constpref.'_LANG_APPROVAL_LIST', 'Approval Users List');
define($constpref.'_LANG_ITEM_LIST', 'Task List');
define($constpref.'_LANG_HISTORY_LIST', 'History List');
define($constpref.'_LANG_MYTASK_LIST', 'My Task List');
define($constpref.'_LANG_NUMBER_OF_ITEMS', 'Number of Tasks');
define($constpref.'_MESSAGE_ADD_A_APPROVAL', 'At first, you should add approvals for each document.');
define($constpref.'_MESSAGE_CONFIRM_DELETE', 'Are you sure to delete?');
define($constpref.'_MESSAGE_NO_APPROVALS_EXIST', 'No approvals are registered.');
define($constpref.'_MESSAGE_NO_TASK', 'You have no task to approve.');
