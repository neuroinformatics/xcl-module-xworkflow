<?php

use Xworkflow\Core\XoopsUtils;
use Xworkflow\Enum;
use Xworkflow\Installer\ModuleUninstaller;

/**
 * uninstaller class.
 */
class Xworkflow_Uninstaller extends ModuleUninstaller
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mHooks[] = 'onUninstallApproveAllItems';
    }

    /**
     * approve all items.
     *
     * @return bool
     */
    public function onUninstallApproveAllItems()
    {
        $dirname = $this->mXoopsModule->get('dirname');
        $uid = XoopsUtils::getUid();
        $this->mLog->addReport('Force approve all in progress workflow items.');
        $comment = 'Approved by the System Administrator';
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $dirname);
        $criteria = new \Criteria('status', Enum\WorkflowStatus::PROGRESS);
        if (!($res = $iHandler->open($criteria))) {
            $this->mLog->addError('Fatal Error in '.__FILE__.' at '.__LINE__);

            return false;
        }
        while ($iObj = $iHandler->getNext($res)) {
            if (!$iHandler->finishStep($iObj, Enum\Result::APPROVE, $uid, $comment)) {
                $this->mLog->addError('Failed to approve workflow item {dirname:'.$iObj->get('dirname').', dataname:'.$iObj->get('dataname').', target_id:'.$iObj->get('target_id').'}.');
                if (!$this->mForceMode) {
                    return false;
                }
            }
        }
        $iHandler->close($res);

        return true;
    }
}
