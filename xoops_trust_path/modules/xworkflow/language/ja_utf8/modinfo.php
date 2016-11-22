<?php

if (!isset($mydirname)) {
    exit();
}

$constpref = '_MI_'.strtoupper($mydirname);

if (defined($constpref.'_LOADED')) {
    return;
}

// system
define($constpref.'_LOADED', 1);

// install utilities
define($constpref.'_INSTALL_ERROR_MODULE_INSTALLED', 'モジュールのインストールができませんでした。');
define($constpref.'_INSTALL_ERROR_PERM_ADMIN_SET', 'モジュールの管理権限を付加できませんでした。');
define($constpref.'_INSTALL_ERROR_PERM_READ_SET', 'モジュールのアクセス権限を付加できませんでした。');
define($constpref.'_INSTALL_MSG_MODULE_INSTALLED', 'モジュール"{0}"をインストールしました。');
define($constpref.'_INSTALL_ERROR_SQL_FILE_NOT_FOUND', 'SQLファイル"{0}"が見つかりませんでした。');
define($constpref.'_INSTALL_MSG_DB_SETUP_FINISHED', 'データベースのセットアップが完了しました。');
define($constpref.'_INSTALL_MSG_SQL_SUCCESS', 'SQL success : {0}');
define($constpref.'_INSTALL_MSG_SQL_ERROR', 'SQL error : {0}');
define($constpref.'_INSTALL_MSG_TPL_INSTALLED', 'テンプレート"{0}"をインストールしました。');
define($constpref.'_INSTALL_ERROR_TPL_INSTALLED', 'テンプレート"{0}"のインストールができませんでした。');
define($constpref.'_INSTALL_ERROR_TPL_UNINSTALLED', 'テンプレート"{0}"のアンインストールができませんでした。');
define($constpref.'_INSTALL_MSG_BLOCK_INSTALLED', 'ブロック"{0}"をインストールしました。');
define($constpref.'_INSTALL_ERROR_BLOCK_COULD_NOT_LINK', 'ブロック"{0}"をモジュールと関連付けできませんでした。');
define($constpref.'_INSTALL_ERROR_PERM_COULD_NOT_SET', 'ブロック"{0}"に権限を付加できませんでした。');
define($constpref.'_INSTALL_ERROR_BLOCK_PERM_SET', 'ブロック"{0}"に権限を付加できませんでした。');
define($constpref.'_INSTALL_MSG_BLOCK_TPL_INSTALLED', 'ブロックテンプレート"{0}"をインストールしました。');
define($constpref.'_INSTALL_ERROR_BLOCK_TPL_INSTALLED', 'ブロックテンプレート"{0}"がインストールできませんでした。');
define($constpref.'_INSTALL_MSG_BLOCK_UNINSTALLED', 'ブロック"{0}"をアンインストールしました。');
define($constpref.'_INSTALL_ERROR_BLOCK_UNINSTALLED', 'ブロック"{0}"がアンインストールできませんでした。');
define($constpref.'_INSTALL_ERROR_BLOCK_PERM_DELETE', 'ブロック"{0}"の権限を削除できませんでした。');
define($constpref.'_INSTALL_MSG_BLOCK_UPDATED', 'ブロック"{0}"をアップデートしました。');
define($constpref.'_INSTALL_ERROR_BLOCK_UPDATED', 'ブロック"{0}"がアップデートできませんでした。');
define($constpref.'_INSTALL_ERROR_BLOCK_INSTALLED', 'ブロック"{0}"がインストールできませんでした。');
define($constpref.'_INSTALL_MSG_BLOCK_TPL_UNINSTALLED', 'ブロックテンプレート"{0}"をアンインストールしました。');
define($constpref.'_INSTALL_MSG_CONFIG_ADDED', '一般設定"{0}"を追加しました。');
define($constpref.'_INSTALL_ERROR_CONFIG_ADDED', '一般設定"{0}"が追加できませんでした。');
define($constpref.'_INSTALL_MSG_CONFIG_DELETED', '一般設定"{0}"を削除しました。');
define($constpref.'_INSTALL_ERROR_CONFIG_DELETED', '一般設定"{0}"が削除できませんでした。');
define($constpref.'_INSTALL_MSG_CONFIG_UPDATED', '一般設定"{0}"をアップデートしました。');
define($constpref.'_INSTALL_ERROR_CONFIG_UPDATED', '一般設定"{0}"がアップデートできませんでした。');
define($constpref.'_INSTALL_ERROR_CONFIG_NOT_FOUND', '一般設定が見つかりません。');
define($constpref.'_INSTALL_MSG_MODULE_INFORMATION_DELETED', 'モジュール情報を削除しました。');
define($constpref.'_INSTALL_ERROR_MODULE_INFORMATION_DELETED', 'モジュール情報が削除できませんでした。');
define($constpref.'_INSTALL_MSG_TABLE_DOROPPED', 'テーブル"{0}"を削除しました。');
define($constpref.'_INSTALL_ERROR_TABLE_DOROPPED', 'テーブル"{0}"が削除できませんでした。');
define($constpref.'_INSTALL_ERROR_BLOCK_TPL_DELETED', 'ブロックテンプレートが削除できませんでした。<br />{0}');
define($constpref.'_INSTALL_MSG_MODULE_UNINSTALLED', 'モジュール"{0}"をアンインストールしました。');
define($constpref.'_INSTALL_ERROR_MODULE_UNINSTALLED', 'モジュール"{0}"がアンインストールできませんでした。');
define($constpref.'_INSTALL_MSG_UPDATE_STARTED', 'モジュールのアップデートを開始します。');
define($constpref.'_INSTALL_MSG_UPDATE_FINISHED', 'モジュールのアップデートが終了しました。');
define($constpref.'_INSTALL_ERROR_UPDATE_FINISHED', 'モジュールのアップデートに失敗しました。');
define($constpref.'_INSTALL_MSG_MODULE_UPDATED', 'モジュール"{0}"をアップデートしました。');
define($constpref.'_INSTALL_ERROR_MODULE_UPDATED', 'モジュール"{0}"がアップデートできませんでした。');

// general information
define($constpref.'_NAME', 'ワークフロー');
define($constpref.'_DESC', '進捗管理モジュール (拡張版)');
define($constpref.'_AUTHOR', 'Neuroinformatics Japan Center, RIKEN BSI <https://nijc.brain.riken.jp/>');
define($constpref.'_CREDITS', 'XooNIps Project');

// templates
define($constpref.'_TPL_APPROVAL_LIST', '承認者一覧');
define($constpref.'_TPL_APPROVAL_EDIT', '承認者編集');
define($constpref.'_TPL_APPROVAL_DELETE', '承認者削除');
define($constpref.'_TPL_ITEM_LIST', 'タスク一覧');
define($constpref.'_TPL_ITEM_VIEW', 'タスク詳細');
define($constpref.'_TPL_HISTORY_EDIT', '履歴編集');
define($constpref.'_TPL_STYLE_CSS', 'スタイルシート');
define($constpref.'_TPL_INC_MENU', 'ヘッダメニュー');

// submenu
define($constpref.'_SUB_MYTASK_LIST', 'マイタスク');
define($constpref.'_SUB_ITEM_LIST', 'タスク一覧');
define($constpref.'_SUB_APPROVAL_LIST', '承認者一覧');
define($constpref.'_SUB_APPROVAL_ADD', '承認者追加');

// preferences
define($constpref.'_LANG_REVERT_TO', '却下の場合の戻し先');
define($constpref.'_DESC_REVERT_TO', '承認者が却下した場合の戻し先を指定');
define($constpref.'_REVERTTO_ZERO', '起案者まで戻す');
define($constpref.'_REVERTTO_FORMER', '一つ前の承認者に戻す');
