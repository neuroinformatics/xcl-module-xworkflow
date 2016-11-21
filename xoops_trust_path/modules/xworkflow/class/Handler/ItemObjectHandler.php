<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\Functions;
use Xworkflow\Core\XoopsUtils;

/**
 * item object handler.
 */
class ItemObjectHandler extends AbstractObjectHandler
{
    /**
     * constructor.
     *
     * @param object &$db
     * @param string $dirname
     */
    public function __construct(&$db, $dirname)
    {
        parent::__construct($db, $dirname);
        $this->mTable = $db->prefix($dirname.'_item');
        $this->mPrimaryKey = 'item_id';
    }

    /**
     * delete.
     *
     * @param {Trustdirname}_ItemObject &$obj
     * @param bool                      $force
     *
     * @return bool
     */
    public function delete(&$obj, $force = false)
    {
        $hHandler = XoopsUtils::getModuleHandler('HistoryObject', $this->mDirname);
        $hHandler->deleteAll(new \Criteria('item_id', $obj->get('item_id')));

        return parent::delete($obj);
    }

    /**
     * proceed step.
     *
     * @param {Trustdirname}_ItemObject $obj
     *
     * @return bool
     */
    public function proceedStep($obj)
    {
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aObj = $aHandler->getNextApproval($obj->get('dirname'), $obj->get('dataname'), $obj->get('step'));
        if (is_object($aObj)) {
            $obj->set('step', $aObj->get('step'));
            $obj->set('status', \Lenum_WorkflowStatus::PROGRESS);
        } else {
            $obj->set('status', \Lenum_WorkflowStatus::FINISHED);
        }
        if ($this->insert($obj)) {
            $result = null;
            \XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

            return true;
        }

        return false;
    }

    /**
     * revert step.
     *
     * @param {Trustdirname}_ItemObject $obj
     *
     * @return bool
     */
    public function revertStep($obj)
    {
        $cnameRevertTo = ucfirst(XoopsUtils::getTrustDirname()).'_RevertTo';
        $revertTo = XoopsUtils::getModuleConfig($this->mDirname, 'revert_to');
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aObj = $aHandler->getPreviousApproval($obj->get('dirname'), $obj->get('dataname'), $obj->get('step'));
        if (!is_object($aObj) || $revertTo == $cnameRevertTo::ZERO) {
            $obj->set('step', 0);
            $obj->set('status', \Lenum_WorkflowStatus::REJECTED);
        } else {
            $obj->set('step', $aObj->get('step'));
        }
        if ($this->insert($obj)) {
            $result = null;
            \XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

            return true;
        }

        return false;
    }

    /**
     * count progress items.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countProgressItems($uid)
    {
        return count($this->getProgressItemsByUserId($uid));
    }

    /**
     * get current progress user ids.
     *
     * @param string $dirname
     * @param string $dataname
     * @param string $target_id
     *
     * @return int[]
     */
    public function getProgressUserIds($dirname, $dataname, $target_id)
    {
        $uids = array();
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $memberHandler = &xoops_gethandler('member');
        $obj = $this->getProgressItemByTargetId($dirname, $dataname, $target_id);
        if ($obj === null) {
            return $uids;
        }
        $step = $obj->get('step');
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('dirname', $dirname));
        $criteria->add(new \Criteria('dataname', $dataname));
        $criteria->add(new \Criteria('step', $step));
        $aObjs = $aHandler->getObjects($criteria);
        if (empty($aObjs)) {
            return $uids;
        }
        $aObj = array_shift($aObjs); // have to only one object
        $uid = $aObj->get('uid');
        $gid = $aObj->get('gid');
        if ($uid > 0) {
            $uids[] = $uid;
        } elseif ($gid > 0) {
            $uids = $memberHandler->getUsersByGroup($gid, false);
        } else {
            $gid = Functions::getTargetGroupId($dirname, $dataname, $target_id);
            $uids = Functions::getGroupAdminUserIds($gid);
        }

        return $uids;
    }

    /**
     * check whether item is in progress.
     *
     * @param string $dirname
     * @param string $dataname
     * @param string $target_id
     *
     * @return bool
     */
    public function isProgressItem($dirname, $dataname, $target_id)
    {
        $obj = $this->getProgressItemByTargetId($dirname, $dataname, $target_id);

        return $obj !== null;
    }

    /**
     * get objects by user id.
     *
     * @param int $uid
     *
     * @return {Trustdirname}_ItemObject[]
     */
    public function getProgressItemsByUserId($uid)
    {
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $memberHandler = &xoops_gethandler('member');
        $gids = &$memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $ret = array();
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('uid', $uid));
        if (!empty($gids)) {
            $criteria->add(new \Criteria('gid', $gids, 'IN'), 'OR');
        }
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $ret = array_merge($ret, $this->getProgressItemsByApprovalObject($aObj));
        }
        // append group admin items
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('uid', 0));
        $criteria->add(new \Criteria('gid', 0));
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $objs = $this->getProgressItemsByApprovalObject($aObj);
            foreach ($objs as $obj) {
                $gid = $obj->getTargetGroupId();
                if (Functions::isGroupAdmin($uid, $gid)) {
                    $ret[] = $obj;
                }
            }
        }

        return $ret;
    }

    /**
     * get progress item by target id.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return object
     */
    public function getProgressItemByTargetId($dirname, $dataname, $target_id)
    {
        $objs = $this->_getProgressItems($dirname, $dataname, $target_id);
        if (empty($objs)) {
            return null;
        }

        return array_shift($objs);
    }

    /**
     * get progress items by approval object.
     *
     * @param {Trustdirname}_ApprovalObject $obj
     *
     * @return {Trustdirname}_ItemObject[]
     */
    public function getProgressItemsByApprovalObject($obj)
    {
        return $this->_getProgressItems($obj->get('dirname'), $obj->get('dataname'), false, $obj->get('step'));
    }

    /**
     * get progress items.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     * @param int    $step
     *
     * @return array
     */
    private function _getProgressItems($dirname, $dataname, $target_id = false, $step = false)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('dirname', $dirname));
        $criteria->add(new \Criteria('dataname', $dataname));
        $criteria->add(new \Criteria('status', \Lenum_WorkflowStatus::PROGRESS));
        if ($target_id !== false) {
            $criteria->add(new \Criteria('target_id', $target_id));
        }
        if ($step !== false) {
            $criteria->add(new \Criteria('step', $step));
        }
        $criteria->add(new \Criteria('deletetime', 0));

        return $this->getObjects($criteria);
    }
}
