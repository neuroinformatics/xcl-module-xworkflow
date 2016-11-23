<?php

use Xworkflow\Core\LanguageManager;

if (!isset($mydirname)) {
    exit();
}

$langman = new LanguageManager($mydirname, 'main');

if ($langman->exists('LOADED')) {
    return;
}

if (!isset($mydirname)) {
    exit();
}

// system
$langman->set('LOADED', 1);

$langman->set('ERROR_REQUIRED', '{0}は必ず入力して下さい');
$langman->set('ERROR_MINLENGTH', '{0}は半角{1}文字以上にして下さい');
$langman->set('ERROR_MAXLENGTH', '{0}は半角{1}文字以内で入力して下さい');
$langman->set('ERROR_EXTENSION', 'アップロードされたファイルは許可された拡張子と一致しません');
$langman->set('ERROR_INTRANGE', '{0}の入力値が不正です');
$langman->set('ERROR_MIN', '{0}は{1}以上の数値を指定して下さい');
$langman->set('ERROR_MAX', '{0}は{1}以下の数値を指定して下さい');
$langman->set('ERROR_OBJECTEXIST', '{0}の入力値が不正です');
$langman->set('ERROR_DBUPDATE_FAILED', 'データベースの更新に失敗しました');
$langman->set('ERROR_EMAIL', '{0}は不正なメールアドレスです');
$langman->set('ERROR_DUPLICATED_STEP', 'このステップは既に設定されています。');
$langman->set('ERROR_CONTENT_IS_NOT_FOUND', 'コンテンツがありません');
$langman->set('ERROR_ITEM_REMAINS', '未処理のタスクがあります。すべてのタスクを承認または却下した後、再度お試しください。');
$langman->set('ERROR_NO_PERMISSION', '権限がありません');
$langman->set('LANG_ADD_A_NEW_APPROVAL', '承認者追加');
$langman->set('LANG_CHOOSE_FROM_HERE', '-- 選択して下さい --');
$langman->set('LANG_APPROVAL_ID', '承認者ID');
$langman->set('LANG_UID', 'ユーザー');
$langman->set('LANG_GID', 'グループ');
$langman->set('LANG_CREATED_BY', '起案者');
$langman->set('LANG_APPROVED_BY', '承認者');
$langman->set('LANG_TARGET_MODULE', '対象ワークフロー');
$langman->set('LANG_DIRNAME', 'モジュール名');
$langman->set('LANG_DATANAME', 'ワークフロー名');
$langman->set('LANG_STEP', '承認ステップ');
$langman->set('LANG_GROUP_ADMIN', '【グループ管理者】');
$langman->set('LANG_CONTROL', '操作');
$langman->set('LANG_APPROVAL_ADD', '承認者の追加');
$langman->set('LANG_APPROVAL_EDIT', '承認者の編集');
$langman->set('LANG_APPROVAL_DELETE', '承認者の削除');
$langman->set('LANG_ADD_A_NEW_ITEM', 'タスク追加');
$langman->set('LANG_ITEM_ID', 'タスクID');
$langman->set('LANG_TARGET_ID', 'アイテムID');
$langman->set('LANG_STATUS', 'ステータス');
$langman->set('LANG_POSTTIME', '追加日時');
$langman->set('LANG_APPROVEDTIME', '承認日時');
$langman->set('LANG_DELETETIME', '削除日時');
$langman->set('LANG_ITEM_VIEW', 'タスクの詳細');
$langman->set('LANG_ITEM_EDIT', 'タスクの編集');
$langman->set('LANG_ITEM_DELETE', 'タスクの削除');
$langman->set('LANG_ADD_A_NEW_HISTORY', '承認履歴の追加');
$langman->set('LANG_PROGRESS_ID', 'PROGRESS_ID');
$langman->set('LANG_RESULT', '結果');
$langman->set('LANG_COMMENT', 'コメント');
$langman->set('LANG_HISTORY', '承認履歴');
$langman->set('LANG_HISTORY_EDIT', '履歴の編集');
$langman->set('LANG_HISTORY_DELETE', '履歴の削除');
$langman->set('LANG_STATUS_DELETED', '削除');
$langman->set('LANG_STATUS_REJECTED', '却下');
$langman->set('LANG_STATUS_PROGRESS', '審査中');
$langman->set('LANG_STATUS_FINISHED', '完了');
$langman->set('LANG_RESULT_HOLD', '保留');
$langman->set('LANG_RESULT_REJECT', '却下');
$langman->set('LANG_RESULT_APPROVE', '承認');
$langman->set('LANG_APPROVE_ITEM', 'タスクの承認');
$langman->set('LANG_TITLE', 'タイトル');
$langman->set('LANG_APPROVAL_LIST', '承認者一覧');
$langman->set('LANG_ITEM_LIST', 'タスク一覧');
$langman->set('LANG_HISTORY_LIST', '承認履歴一覧');
$langman->set('LANG_MYTASK_LIST', 'マイタスク');
$langman->set('LANG_NUMBER_OF_ITEMS', '件数');
$langman->set('MESSAGE_ADD_A_APPROVAL', 'はじめに、承認者を設定する必要があります。');
$langman->set('MESSAGE_CONFIRM_DELETE', '以下のデータを本当に削除しますか？');
$langman->set('MESSAGE_NO_APPROVALS_EXIST', '承認者を追加してください。');
$langman->set('MESSAGE_NO_TASK', '承認が必要なタスクはありません。');
