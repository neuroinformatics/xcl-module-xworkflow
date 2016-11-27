<?php

use Xworkflow\Core\Functions;
use Xworkflow\Core\XCubeUtils;
use Xworkflow\Core\XoopsUtils;
use Xworkflow\Enum;
use Xworkflow\Installer\ModuleUpdater;
use Xworkflow\Installer\InstallUtils;

/**
 * updater class.
 */
class Xworkflow_Updater extends ModuleUpdater
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mHooks[200] = 'onUpdateToVersion200';
    }

    /**
     * update version to 2.00.
     *
     * @return bool
     */
    public function onUpdateToVersion200()
    {
        $this->mLog->addReport('Start to apply changes since verion 2.00.');
        $dirname = $this->mCurrentXoopsModule->get('dirname');
        $sql = 'ALTER TABLE `{prefix}_{dirname}_item` ADD `target_gid` int(10) unsigned NOT NULL AFTER `target_id`';
        if (!InstallUtils::DBquery($sql, $this->mCurrentXoopsModule, $this->mLog)) {
            return false;
        }
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $dirname);
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $dirname);
        $criteria = new \CriteriaCompo(new \Criteria('uid', 0));
        $criteria->add(new \Criteria('gid', 0));
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $dirname = $aObj->get('dirname');
            $dataname = $aObj->get('dataname');
            $criteria = new \CriteriaCompo(new \Criteria('dirname', $dirname));
            $criteria->add(new \Criteria('dataname', $dataname));
            $criteria->add(new \Criteria('step', $aObj->get('step')));
            $criteria->add(new \Criteria('status', Enum\WorkflowStatus::PROGRESS));
            if (!$res = $iHandler->open($criteria)) {
                $this->mLog->addError(XCubeUtils::formatString($this->mLangMan->get('INSTALL_ERROR_TABLE_UPDATED'), $aHandler->getTable()));

                return false;
            }
            while ($iObj = $iHandler->getNext($res)) {
                $target_gid = Functions::getTargetGroupId($dirname, $dataname, $iObj->get('target_id'));
                if ($target_gid === false) {
                    $this->mLog->addError(XCubeUtils::formatString($this->mLangMan->get('INSTALL_ERROR_TABLE_UPDATED'), $aHandler->getTable()));

                    return false;
                }
                $iObj->set('target_gid', $target_gid);
                if (!$iHandler->insert($iObj)) {
                    $this->mLog->addError(CubeUtils::formatString($this->mLangMan->get('INSTALL_ERROR_TABLE_UPDATED'), $aHandler->getTable()));

                    return false;
                }
            }
            $iHandler->close($res);
        }

        return true;
    }
}
