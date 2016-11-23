<?php

use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XCubeUtils;
use Xworkflow\Installer\InstallUtils;

/**
 * module installer class.
 */
class Xworkflow_Installer
{
    /**
     * module log.
     *
     * @var Legacy_ModuleInstallLog
     */
    public $mLog = null;

    /**
     * flag for force mode.
     *
     * @var bool
     */
    private $_mForceMode = false;

    /**
     * xoops module.
     *
     * @var XoopsModule
     */
    private $_mXoopsModule = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->mLog = new Legacy_ModuleInstallLog();
    }

    /**
     * set current xoops module.
     *
     * @param XoopsModule &$xoopsModule
     */
    public function setCurrentXoopsModule(&$xoopsModule)
    {
        $this->_mXoopsModule = &$xoopsModule;
    }

    /**
     * set force mode.
     *
     * @param bool $isForceMode
     */
    public function setForceMode($isForceMode)
    {
        $this->_mForceMode = $isForceMode;
    }

    /**
     * install tables information.
     *
     * @return bool
     */
    private function _installTables()
    {
        InstallUtils::installSQLAutomatically($this->_mXoopsModule, $this->mLog);
    }

    /**
     * install module information.
     *
     * @return bool
     */
    private function _installModule()
    {
        $dirname = $this->_mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'modinfo');
        $moduleHandler = &xoops_gethandler('module');
        if (!$moduleHandler->insert($this->_mXoopsModule)) {
            $this->mLog->addError($langman->get('INSTALL_ERROR_MODULE_INSTALLED'));

            return false;
        }
        $gpermHandler = &xoops_gethandler('groupperm');
        if ($this->_mXoopsModule->getInfo('hasAdmin')) {
            $adminPerm = &$this->_createPermission(XOOPS_GROUP_ADMIN);
            $adminPerm->set('gperm_name', 'module_admin');
            if (!$gpermHandler->insert($adminPerm)) {
                $this->mLog->addError($langman->get('INSTALL_ERROR_PERM_ADMIN_SET'));
            }
        }
        if ($this->_mXoopsModule->getInfo('hasMain')) {
            if ($this->_mXoopsModule->getInfo('read_any')) {
                $memberHandler = &xoops_gethandler('member');
                $groupObjects = &$memberHandler->getGroups();
                foreach ($groupObjects as $group) {
                    $readPerm = &$this->_createPermission($group->get('groupid'));
                    $readPerm->set('gperm_name', 'module_read');
                    if (!$gpermHandler->insert($readPerm)) {
                        $this->mLog->addError($langman->get('INSTALL_ERROR_PERM_READ_SET'));
                    }
                }
            } else {
                foreach (array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS) as $group) {
                    $readPerm = &$this->_createPermission($group);
                    $readPerm->set('gperm_name', 'module_read');
                    if (!$gpermHandler->insert($readPerm)) {
                        $this->mLog->addError($langman->get('INSTALL_ERROR_PERM_READ_SET'));
                    }
                }
            }
        }

        return true;
    }

    /**
     * create permission.
     *
     * @param int $gid
     *
     * @return XoopsGroupPerm&
     */
    private function &_createPermission($gid)
    {
        $gpermHandler = &xoops_gethandler('groupperm');
        $perm = &$gpermHandler->create();
        $perm->set('gperm_groupid', $gid);
        $perm->set('gperm_itemid', $this->_mXoopsModule->get('mid'));
        $perm->set('gperm_modid', 1);

        return $perm;
    }

    /**
     * install templates.
     */
    private function _installTemplates()
    {
        InstallUtils::installAllOfModuleTemplates($this->_mXoopsModule, $this->mLog);
    }

    /**
     * install blocks.
     */
    private function _installBlocks()
    {
        InstallUtils::installAllOfBlocks($this->_mXoopsModule, $this->mLog);
    }

    /**
     * install preferences.
     */
    private function _installPreferences()
    {
        InstallUtils::installAllOfConfigs($this->_mXoopsModule, $this->mLog);
    }

    /**
     * process report.
     */
    private function _processReport()
    {
        $dirname = $this->_mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'modinfo');
        if (!$this->mLog->hasError()) {
            $this->mLog->add(XCubeUtils::formatString($langman->get('INSTALL_MSG_MODULE_INSTALLED'), $this->_mXoopsModule->getInfo('name')));
        } elseif (is_object($this->_mXoopsModule)) {
            $this->mLog->addError(XCubeUtils::formatString($langman->get('INSTALL_ERROR_MODULE_INSTALLED'), $this->_mXoopsModule->getInfo('name')));
        } else {
            $this->mLog->addError(XCubeUtils::formatString($langman->get('INSTALL_ERROR_MODULE_INSTALLED'), 'something'));
        }
    }

    /**
     * execute install.
     *
     * @return bool
     */
    public function executeInstall()
    {
        if (!$this->_mForceMode && defined('LEGACY_WORKFLOW_DIRNAME') && LEGACY_WORKFLOW_DIRNAME != $this->_mXoopsModule->get('dirname')) {
            $this->mLog->addError('LEGACY_WORKFLOW module already available');
            $this->_processReport();

            return false;
        }
        $this->_installTables();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_installModule();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_installTemplates();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_installBlocks();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_installPreferences();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_processReport();

        return true;
    }
}
