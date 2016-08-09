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

define($constpref.'_ERROR_REQUIRED', '{0}は必ず入力して下さい');
define($constpref.'_ERROR_MINLENGTH', '{0}は半角{1}文字以上にして下さい');
define($constpref.'_ERROR_MAXLENGTH', '{0}は半角{1}文字以内で入力して下さい');
define($constpref.'_ERROR_EXTENSION', 'アップロードされたファイルは許可された拡張子と一致しません');
define($constpref.'_ERROR_INTRANGE', '{0}の入力値が不正です');
define($constpref.'_ERROR_MIN', '{0}は{1}以上の数値を指定して下さい');
define($constpref.'_ERROR_MAX', '{0}は{1}以下の数値を指定して下さい');
define($constpref.'_ERROR_OBJECTEXIST', '{0}の入力値が不正です');
define($constpref.'_ERROR_DBUPDATE_FAILED', 'データベースの更新に失敗しました');
define($constpref.'_ERROR_EMAIL', '{0}は不正なメールアドレスです');
define($constpref.'_ERROR_DUPLICATED_STEP', 'このステップは既に設定されています。');
define($constpref.'_ERROR_CONTENT_IS_NOT_FOUND', 'コンテンツがありません');
define($constpref.'_ERROR_ITEM_REMAINS', '未処理のタスクがあります。すべてのタスクを承認または却下した後、再度お試しください。');
define($constpref.'_ERROR_NO_PERMISSION', '権限がありません');
define($constpref.'_LANG_ADD_A_NEW_APPROVAL', '承認者追加');
define($constpref.'_LANG_CHOOSE_FROM_HERE', '-- 選択して下さい --');
define($constpref.'_LANG_APPROVAL_ID', '承認者ID');
define($constpref.'_LANG_UID', 'ユーザー');
define($constpref.'_LANG_GID', 'グループ');
define($constpref.'_LANG_CREATED_BY', '起案者');
define($constpref.'_LANG_APPROVED_BY', '承認者');
define($constpref.'_LANG_TARGET_MODULE', '対象ワークフロー');
define($constpref.'_LANG_DIRNAME', 'モジュール名');
define($constpref.'_LANG_DATANAME', 'ワークフロー名');
define($constpref.'_LANG_STEP', '承認ステップ');
define($constpref.'_LANG_GROUP_ADMIN', '【グループ管理者】');
define($constpref.'_LANG_CONTROL', '操作');
define($constpref.'_LANG_APPROVAL_ADD', '承認者の追加');
define($constpref.'_LANG_APPROVAL_EDIT', '承認者の編集');
define($constpref.'_LANG_APPROVAL_DELETE', '承認者の削除');
define($constpref.'_LANG_ADD_A_NEW_ITEM', 'タスク追加');
define($constpref.'_LANG_ITEM_ID', 'タスクID');
define($constpref.'_LANG_TARGET_ID', 'アイテムID');
define($constpref.'_LANG_STATUS', 'ステータス');
define($constpref.'_LANG_POSTTIME', '追加日時');
define($constpref.'_LANG_APPROVEDTIME', '承認日時');
define($constpref.'_LANG_DELETETIME', '削除日時');
define($constpref.'_LANG_ITEM_VIEW', 'タスクの詳細');
define($constpref.'_LANG_ITEM_EDIT', 'タスクの編集');
define($constpref.'_LANG_ITEM_DELETE', 'タスクの削除');
define($constpref.'_LANG_ADD_A_NEW_HISTORY', '承認履歴の追加');
define($constpref.'_LANG_PROGRESS_ID', 'PROGRESS_ID');
define($constpref.'_LANG_RESULT', '結果');
define($constpref.'_LANG_COMMENT', 'コメント');
define($constpref.'_LANG_HISTORY', '承認履歴');
define($constpref.'_LANG_HISTORY_EDIT', '履歴の編集');
define($constpref.'_LANG_HISTORY_DELETE', '履歴の削除');
define($constpref.'_LANG_STATUS_DELETED', '削除');
define($constpref.'_LANG_STATUS_REJECTED', '却下');
define($constpref.'_LANG_STATUS_PROGRESS', '審査中');
define($constpref.'_LANG_STATUS_FINISHED', '完了');
define($constpref.'_LANG_RESULT_HOLD', '保留');
define($constpref.'_LANG_RESULT_REJECT', '却下');
define($constpref.'_LANG_RESULT_APPROVE', '承認');
define($constpref.'_LANG_APPROVE_ITEM', 'タスクの承認');
define($constpref.'_LANG_TITLE', 'タイトル');
define($constpref.'_LANG_APPROVAL_LIST', '承認者一覧');
define($constpref.'_LANG_ITEM_LIST', 'タスク一覧');
define($constpref.'_LANG_HISTORY_LIST', '承認履歴一覧');
define($constpref.'_LANG_MYTASK_LIST', 'マイタスク');
define($constpref.'_LANG_NUMBER_OF_ITEMS', '件数');
define($constpref.'_MESSAGE_ADD_A_APPROVAL', 'はじめに、承認者を設定する必要があります。');
define($constpref.'_MESSAGE_CONFIRM_DELETE', '以下のデータを本当に削除しますか？');
define($constpref.'_MESSAGE_NO_APPROVALS_EXIST', '承認者を追加してください。');
define($constpref.'_MESSAGE_NO_TASK', '承認が必要なタスクはありません。');
