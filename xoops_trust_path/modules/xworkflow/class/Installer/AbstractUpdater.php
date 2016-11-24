<?php

namespace Xworkflow\Installer;

use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XCubeUtils;

/**
 * updater class.
 */
abstract class AbstractUpdater
{
    /**
     * module install log.
     *
     * @var object
     */
    public $mLog = null;

    /**
     * milestone.
     *
     * @var array
     */
    protected $mMileStone = array();

    /**
     * current xoops module.
     *
     * @var XoopsModule
     */
    protected $mCurrentXoopsModule = null;

    /**
     * target xoops module.
     *
     * @var XoopsModule
     */
    protected $mTargetXoopsModule = null;

    /**
     * current module version.
     *
     * @var int
     */
    protected $mCurrentVersion = 0;

    /**
     * target module version.
     *
     * @var int
     */
    protected $mTargetVersion = 0;

    /**
     * flag for force mode.
     *
     * @var bool
     */
    protected $mForceMode = false;

    /**
     * language manager.
     *
     * @var object
     */
    protected $mLangMan = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->mLog = new InstallLog();
    }

    /**
     * set force mode.
     *
     * @param bool $isForceMode
     */
    public function setForceMode($isForceMode)
    {
        $this->mForceMode = $isForceMode;
    }

    /**
     * set current xoops module.
     *
     * @param XoopsModule &$module
     */
    public function setCurrentXoopsModule(&$module)
    {
        $dirname = $module->get('dirname');
        $moduleHandler = &xoops_gethandler('module');
        $cloneModule = &$moduleHandler->create();
        $cloneModule->unsetNew();
        $cloneModule->set('mid', $module->get('mid'));
        $cloneModule->set('name', $module->get('name'));
        $cloneModule->set('version', $module->get('version'));
        $cloneModule->set('last_update', $module->get('last_update'));
        $cloneModule->set('weight', $module->get('weight'));
        $cloneModule->set('isactive', $module->get('isactive'));
        $cloneModule->set('dirname', $dirname);
        // $cloneModule->set('trust_dirname', $module->get('trust_dirname'));
        $cloneModule->set('hasmain', $module->get('hasmain'));
        $cloneModule->set('hasadmin', $module->get('hasadmin'));
        $cloneModule->set('hasconfig', $module->get('hasconfig'));
        $this->mCurrentXoopsModule = &$cloneModule;
        $this->mCurrentVersion = $cloneModule->get('version');
    }

    /**
     * set target xoops module.
     *
     * @param XoopsModule &$module
     */
    public function setTargetXoopsModule(&$module)
    {
        $this->mTargetXoopsModule = &$module;
        $this->mTargetVersion = $this->getTargetPhase();
    }

    /**
     * get current version.
     *
     * @return int
     */
    public function getCurrentVersion()
    {
        return intval($this->mCurrentVersion);
    }

    /**
     * get target phase.
     *
     * @return int
     */
    public function getTargetPhase()
    {
        ksort($this->mMileStone);
        foreach ($this->mMileStone as $tVer => $tMethod) {
            if ($tVer >= $this->getCurrentVersion()) {
                return intval($tVer);
            }
        }

        return $this->mTargetXoopsModule->get('version');
    }

    /**
     * check whether updater has phase update method.
     *
     * @return bool
     */
    public function hasUpgradeMethod()
    {
        ksort($this->mMileStone);
        foreach ($this->mMileStone as $tVer => $tMethod) {
            if ($tVer >= $this->getCurrentVersion() && is_callable(array($this, $tMethod))) {
                return true;
            }
        }

        return false;
    }

    /**
     * check whether it is latest update now.
     *
     * @return bool
     */
    public function isLatestUpgrade()
    {
        return $this->mTargetXoopsModule->get('version') == $this->getTargetPhase();
    }

    /**
     * update module templates.
     */
    protected function _updateModuleTemplates()
    {
        InstallUtils::uninstallAllOfModuleTemplates($this->mTargetXoopsModule, $this->mLog);
        InstallUtils::installAllOfModuleTemplates($this->mTargetXoopsModule, $this->mLog);
    }

    /**
     * update blocks.
     */
    protected function _updateBlocks()
    {
        InstallUtils::smartUpdateAllOfBlocks($this->mTargetXoopsModule, $this->mLog);
    }

    /**
     * update preferences.
     */
    protected function _updatePreferences()
    {
        InstallUtils::smartUpdateAllOfConfigs($this->mTargetXoopsModule, $this->mLog);
    }

    /**
     * execute upgrade.
     *
     * @return bool
     */
    public function executeUpgrade()
    {
        $dirname = $this->mCurrentXoopsModule->get('dirname');
        $this->mLangMan = new LanguageManager($dirname, 'install');
        $this->mLangMan->load();

        return $this->hasUpgradeMethod() ? $this->_callUpgradeMethod() : $this->executeAutomaticUpgrade();
    }

    /**
     * call upgrade method.
     *
     * @return bool
     */
    protected function _callUpgradeMethod()
    {
        ksort($this->mMileStone);
        foreach ($this->mMileStone as $tVer => $tMethod) {
            if ($tVer >= $this->getCurrentVersion() && is_callable(array($this, $tMethod))) {
                return $this->$tMethod();
            }
        }

        return false;
    }

    /**
     * execute automatic upgrade.
     *
     * @return bool
     */
    public function executeAutomaticUpgrade()
    {
        $this->mLog->addReport($this->mLangMan->get('INSTALL_MSG_UPDATE_STARTED'));
        $this->_updateModuleTemplates();
        if (!$this->mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_updateBlocks();
        if (!$this->mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_updatePreferences();
        if (!$this->mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->saveXoopsModule($this->mTargetXoopsModule);
        if (!$this->mForceMode && $this->mLog->hasError()) {
            $this->_processReport();

            return false;
        }
        $this->_processReport();

        return true;
    }

    /**
     * save xoops module.
     *
     * @param XoopsModule &$module
     */
    public function saveXoopsModule(&$module)
    {
        $moduleHandler = &xoops_gethandler('module');
        if ($moduleHandler->insert($module)) {
            $this->mLog->addReport($this->mLangMan->get('INSTALL_MSG_UPDATE_FINISHED'));
        } else {
            $this->mLog->addError($this->mLangMan->get('INSTALL_ERROR_UPDATE_FINISHED'));
        }
    }

    /**
     * process report.
     */
    protected function _processReport()
    {
        if (!$this->mLog->hasError()) {
            $this->mLog->addReport(XCubeUtils::formatString($this->mLangMan->get('INSTALL_MSG_MODULE_UPDATED'), $this->mCurrentXoopsModule->get('name')));
        } else {
            $this->mLog->addReport(XCubeUtils::formatString($this->mLangMan->get('INSTALL_ERROR_MODULE_UPDATED'), $this->mCurrentXoopsModule->get('name')));
        }
    }
}
