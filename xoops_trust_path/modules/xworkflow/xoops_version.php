<?php

use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;

$langman = new LanguageManager($mydirname, 'modinfo');

// Define a basic manifesto.

$modversion['name'] = $langman->get('NAME');
$modversion['version'] = 1.9;
$modversion['description'] = $langman->get('DESC');
$modversion['author'] = $langman->get('AUTHOR');
$modversion['credits'] = $langman->get('CREDITS');
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'module_icon.php';
$modversion['dirname'] = $mydirname;
$modversion['trust_dirname'] = $mytrustdirname;
$modversion['role'] = 'workflow';

$modversion['cube_style'] = true;
$modversion['legacy_installer'] = array(
    'installer' => array(
        'class' => 'Installer',
        'namespace' => ucfirst($mytrustdirname),
        'filepath' => XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/class/installer/Installer.class.php',
    ),
    'uninstaller' => array(
        'class' => 'Uninstaller',
        'namespace' => ucfirst($mytrustdirname),
        'filepath' => XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/class/installer/Uninstaller.class.php',
    ),
    'updater' => array(
        'class' => 'Updater',
        'namespace' => ucfirst($mytrustdirname),
        'filepath' => XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/class/installer/Updater.class.php',
    ),
);
$modversion['disable_legacy_2nd_installer'] = false;

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = array(
    '{prefix}_{dirname}_approval',
    '{prefix}_{dirname}_history',
    '{prefix}_{dirname}_item',
);

// Templates

$modversion['templates'] = array(
    array('file' => 'approval_list.html', 'description' => $langman->get('TPL_APPROVAL_LIST')),
    array('file' => 'approval_edit.html', 'description' => $langman->get('TPL_APPROVAL_EDIT')),
    array('file' => 'approval_delete.html', 'description' => $langman->get('TPL_APPROVAL_DELETE')),
    array('file' => 'item_list.html', 'description' => $langman->get('TPL_ITEM_LIST')),
    array('file' => 'item_view.html', 'description' => $langman->get('TPL_ITEM_VIEW')),
    array('file' => 'history_edit.html', 'description' => $langman->get('TPL_HISTORY_EDIT')),
    array('file' => 'style.css', 'description' => $langman->get('TPL_STYLE_CSS')),
    array('file' => 'inc_menu.html', 'description' => $langman->get('TPL_INC_MENU')),
);

// Admin panel setting

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = array();

// Public side control setting

$modversion['hasMain'] = 1;
$modversion['hasSearch'] = 0;
$modversion['sub'] = array();
$modversion['sub'][] = array(
    'name' => $langman->get('SUB_MYTASK_LIST'),
    'url' => 'index.php',
);
$modversion['sub'][] = array(
    'name' => $langman->get('SUB_ITEM_LIST'),
    'url' => 'index.php?action=ItemList',
);
if (XoopsUtils::isAdmin(XoopsUtils::getUid(), $mydirname)) {
    $modversion['sub'][] = array(
        'name' => $langman->get('SUB_APPROVAL_LIST'),
        'url' => 'index.php?action=ApprovalList',
    );
    $modversion['sub'][] = array(
        'name' => $langman->get('SUB_APPROVAL_ADD'),
        'url' => 'index.php?action=ApprovalEdit',
    );
}

// Config setting

$cname = $mytrustdirname.'_RevertTo';
$modversion['config'] = array(
    array(
        'name' => 'revert_to',
        'title' => $langman->getName('LANG_REVERT_TO'),
        'description' => $langman->getName('DESC_REVERT_TO'),
        'formtype' => 'select',
        'valuetype' => 'string',
        'default' => '0',
        'options' => array(
            $langman->getName('REVERTTO_ZERO') => $cname::ZERO,
            $langman->getName('REVERTTO_FORMER') => $cname::FORMER,
        ),
    ),
);

// Block setting

$modversion['blocks'] = array();
