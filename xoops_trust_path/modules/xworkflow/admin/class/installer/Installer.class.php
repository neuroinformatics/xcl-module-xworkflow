<?php

use Xworkflow\Installer\ModuleInstaller;

/**
 * module installer class.
 */
class Xworkflow_Installer extends ModuleInstaller
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mHooks[] = 'onInstallModuleCheck';
    }

    /**
     * check module install.
     *
     * @return bool
     */
    public function onInstallModuleCheck()
    {
        if (defined('LEGACY_WORKFLOW_DIRNAME') && LEGACY_WORKFLOW_DIRNAME != $this->mXoopsModule->get('dirname')) {
            $this->mLog->addError('LEGACY_WORKFLOW module already available');

            return false;
        }

        return true;
    }
}
