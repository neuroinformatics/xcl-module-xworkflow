<?php

use Xworkflow\Installer\AbstractInstaller;

/**
 * module installer class.
 */
class Xworkflow_Installer extends AbstractInstaller
{
    /**
     * execute install.
     *
     * @return bool
     */
    public function executeInstall()
    {
        if (!$this->mForceMode && defined('LEGACY_WORKFLOW_DIRNAME') && LEGACY_WORKFLOW_DIRNAME != $this->mXoopsModule->get('dirname')) {
            $this->mLog->addError('LEGACY_WORKFLOW module already available');
            $this->_processReport();

            return false;
        }

        return parent::executeInstall();
    }
}
