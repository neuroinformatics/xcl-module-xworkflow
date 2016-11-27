<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\Functions;
use Xworkflow\Core\XoopsUtils;
use Xworkflow\Enum;

/**
 * workflow service handler.
 */
class WorkflowServiceHandler extends AbstractHandler
{
    /**
     * check whether user is approver.
     * this funciton will used to show links to workflow module.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function isApprover($uid)
    {
        $mHandler = &xoops_gethandler('member');
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
        $gids = $mHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $criteria = new \CriteriaCompo(new \Criteria('uid', $uid));
        if (!empty($gids)) {
            $criteria->add(new \Criteria('gid', $gids, 'IN'), 'OR');
        }
        if ($aHandler->getCount($criteria) > 0) {
            return true;
        }
        // check group admin items
        if ($iHandler->countInProgressItems($uid) > 0) {
            return true;
        }

        return false;
    }

    /**
     * get all item approver user ids.
     * this funciton will used to notify final result to all approver.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return array
     */
    public function getAllApproverUserIds($dirname, $dataname, $target_id)
    {
        $uids = array();
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $criteria = new \CriteriaCompo(new \Criteria('dirname', $dirname));
        $criteria->add(new \Criteria('dataname', $dataname));
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $uids = array_merge($uids, $this->_getApproverUserIds($aObj, $target_id));
        }
        $uids = array_unique($uids);
        sort($uids);

        return $uids;
    }

    /**
     * get current item approver user ids.
     * this funciton will used to request ceritify item to next approver.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return array
     */
    public function getCurrentApproverUserIds($dirname, $dataname, $target_id)
    {
        $uids = array();
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
        $iObj = $iHandler->getItem($dirname, $dataname, $target_id);
        if (is_object($iObj)) {
            return $uids;
        }
        $criteria = new \CriteriaCompo(new \Criteria('dirname', $dirname));
        $criteria->add(new \Criteria('dataname', $dataname));
        $criteria->add(new \Criteria('step', $iObj->get('step')));
        $aObjs = $aHandler->getObjects($criteria);
        if (empty($aObjs)) {
            trigger_error('No approver found for item {dirname:'.$dirname.', dataname:'.$dataname.', target_id:'.$target_id.'}', E_USER_WARNIG);

            return $uids;
        }
        $aObj = array_shift($aObjs);

        return $this->_getApproverUserIds($aObj, $target_id);
    }

    /**
     * count user's in progress items.
     * this funciton will used to show number of my task.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countInProgressItems($uid)
    {
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);

        return $iHandler->countInProgressItems($uid);
    }

    /**
     * check whether item is in progress.
     * this funciton will used to check result of addItem().
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return int
     */
    public function isInProgressItem($dirname, $dataname, $target_id)
    {
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
        $iObj = $iHandler->getItem($dirname, $dataname, $target_id);
        if (!is_object($iObj)) {
            return false;
        }

        return $iObj->get('status') == Enum\WorkflowStatus::PROGRESS;
    }

    /**
     * get approver user ids for target_id.
     *
     * @param Object\ApprovalObject $aObj
     * @param int                   $target_id
     *
     * @return array
     */
    private function _getApproverUserIds($aObj, $target_id)
    {
        $uids = array();
        $memberHandler = &xoops_gethandler('member');
        $uid = $aObj->get('uid');
        $gid = $aObj->get('gid');
        if ($uid > 0) {
            $uids[] = $uid;
        } elseif ($gid > 0) {
            $uids = array_merge($uids, $memberHandler->getUsersByGroup($gid, false));
        } else {
            $gid = Functions::getTargetGroupId($aObj->get('dirname'), $aObj->get('dataname'), $target_id);
            $uids = array_merge($uids, Functions::getGroupAdminUserIds($gid));
        }
        $uids = array_unique($uids);
        sort($uids);

        return $uids;
    }
}
