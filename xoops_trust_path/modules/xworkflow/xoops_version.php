<?php

use Xworkflow\Core\XoopsUtils;

require_once __DIR__.'/include/common.php';

// force load modinfo message catalog
XCube_Root::getSingleton()->mLanguageManager->loadModinfoMessageCatalog($mydirname);

$constpref = '_MI_'.strtoupper($mydirname);
if (!defined($constpref.'_LOADED')) {
    // load modinfo.php by myself. probably this case will occured only
    // if this module is not installed yet. because trust language
    // resources are supported by the language manager of legacy 2.2
    // if a module is already installed.
    $fname = dirname(__FILE__).'/language/'.XCube_Root::getSingleton()->mLanguageManager->getLanguage().'/modinfo.php';
    if (!file_exists($fname)) {
        $fname = dirname(__FILE__).'/language/'.XCube_Root::getSingleton()->mLanguageManager->getFallbackLanguage().'/modinfo.php';
    }
    require_once $fname;
}

// Define a basic manifesto.

$modversion['name'] = constant($constpref.'_NAME');
$modversion['version'] = 2.0;
$modversion['description'] = constant($constpref.'_DESC');
$modversion['author'] = constant($constpref.'_AUTHOR');
$modversion['credits'] = constant($constpref.'_CREDITS');
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
    array('file' => 'approval_list.html', 'description' => constant($constpref.'_TPL_APPROVAL_LIST')),
    array('file' => 'approval_edit.html', 'description' => constant($constpref.'_TPL_APPROVAL_EDIT')),
    array('file' => 'approval_delete.html', 'description' => constant($constpref.'_TPL_APPROVAL_DELETE')),
    array('file' => 'item_list.html', 'description' => constant($constpref.'_TPL_ITEM_LIST')),
    array('file' => 'item_view.html', 'description' => constant($constpref.'_TPL_ITEM_VIEW')),
    array('file' => 'history_edit.html', 'description' => constant($constpref.'_TPL_HISTORY_EDIT')),
    array('file' => 'style.css', 'description' => constant($constpref.'_TPL_STYLE_CSS')),
    array('file' => 'inc_menu.html', 'description' => constant($constpref.'_TPL_INC_MENU')),
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
    'name' => constant($constpref.'_SUB_MYTASK_LIST'),
    'url' => 'index.php',
);
$modversion['sub'][] = array(
    'name' => constant($constpref.'_SUB_ITEM_LIST'),
    'url' => 'index.php?action=ItemList',
);
if (XoopsUtils::isAdmin(XoopsUtils::getUid(), $mydirname)) {
    $modversion['sub'][] = array(
        'name' => constant($constpref.'_SUB_APPROVAL_LIST'),
        'url' => 'index.php?action=ApprovalList',
    );
    $modversion['sub'][] = array(
        'name' => constant($constpref.'_SUB_APPROVAL_ADD'),
        'url' => 'index.php?action=ApprovalEdit',
    );
}

// Config setting

$cname = $mytrustdirname.'_RevertTo';
$modversion['config'] = array(
    array(
        'name' => 'revert_to',
        'title' => $constpref.'_LANG_REVERT_TO',
        'description' => $constpref.'_DESC_REVERT_TO',
        'formtype' => 'select',
        'valuetype' => 'string',
        'default' => '0',
        'options' => array(
            $constpref.'_REVERTTO_ZERO' => $cname::ZERO,
            $constpref.'_REVERTTO_FORMER' => $cname::FORMER,
        ),
    ),
);

// Block setting

$modversion['blocks'] = array();
