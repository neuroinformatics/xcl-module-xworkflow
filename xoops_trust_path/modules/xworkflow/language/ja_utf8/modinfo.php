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
$langman->set('NAME', 'ワークフロー');
$langman->set('DESC', '進捗管理モジュール (拡張版)');
$langman->set('AUTHOR', 'Neuroinformatics Japan Center, RIKEN BSI <https://nijc.brain.riken.jp/>');
$langman->set('CREDITS', 'XooNIps Project');

// templates
$langman->set('TPL_APPROVAL_LIST', '承認者一覧');
$langman->set('TPL_APPROVAL_EDIT', '承認者編集');
$langman->set('TPL_APPROVAL_DELETE', '承認者削除');
$langman->set('TPL_ITEM_LIST', 'タスク一覧');
$langman->set('TPL_ITEM_VIEW', 'タスク詳細');
$langman->set('TPL_HISTORY_EDIT', '履歴編集');
$langman->set('TPL_STYLE_CSS', 'スタイルシート');
$langman->set('TPL_INC_MENU', 'ヘッダメニュー');
$langman->set('TPL_INC_PAGENAVI', 'ページナビゲーション');

// submenu
$langman->set('SUB_MYTASK_LIST', 'マイタスク');
$langman->set('SUB_ITEM_LIST', 'タスク一覧');
$langman->set('SUB_APPROVAL_LIST', '承認者一覧');
$langman->set('SUB_APPROVAL_ADD', '承認者追加');

// preferences
$langman->set('LANG_REVERT_TO', '却下の場合の戻し先');
$langman->set('DESC_REVERT_TO', '承認者が却下した場合の戻し先を指定');
$langman->set('REVERTTO_ZERO', '起案者まで戻す');
$langman->set('REVERTTO_FORMER', '一つ前の承認者に戻す');
