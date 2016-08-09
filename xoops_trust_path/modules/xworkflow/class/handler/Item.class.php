<?php

/**
 * item object.
 */
class Xworkflow_ItemObject extends XoopsSimpleObject
{
    /**
     * histories.
     *
     * @var {Trustdirnaem}_HistoryObject[]
     */
    public $mHistory = array();

    /**
     * flag for history loaded.
     *
     * @var bool
     */
    protected $_mHistoryLoadedFlag = false;

    /**
     * constructor.
     * 
     * @param string $dirname
     */
    public function __construct()
    {
        $this->initVar('item_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('dirname', XOBJ_DTYPE_STRING, '', false, 45);
        $this->initVar('dataname', XOBJ_DTYPE_STRING, '', false, 45);
        $this->initVar('target_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('step', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('status', XOBJ_DTYPE_INT, Lenum_WorkflowStatus::PROGRESS, false);
        $this->initVar('revision', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('url', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('posttime', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('updatetime', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('deletetime', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * set first step.
     */
    public function setFirstStep()
    {
        $aHandler = Legacy_Utils::getModuleHandler('approval', $this->getDirname());
        $aObj = $aHandler->getNextApproval($this->get('dirname'), $this->get('dataname'), 0);
        if ($aObj) {
            $this->set('step', $aObj->get('step'));
        } else {
            $this->set('step', 0);
            $this->set('status', Lenum_WorkflowStatus::FINISHED);
        }
    }

    /**
     * increment revision.
     */
    public function incrementRevision()
    {
        $this->set('revision', $this->get('revision') + 1);
    }

    /**
     * load history.
     * 
     * @param string $order 'ASC' or 'DESC'
     */
    public function loadHistory($order = 'ASC')
    {
        if ($this->_mHistoryLoadedFlag == false) {
            $hHandler = Legacy_Utils::getModuleHandler('history', $this->getDirname());
            $cri = new Criteria('item_id', $this->get('item_id'));
            $cri->setSort('posttime', $order);
            $this->mHistory = &$hHandler->getObjects($cri);
            $this->_mHistoryLoadedFlag = true;
        }
    }

    /**
     * get show status.
     * 
     * @return string
     */
    public function getShowStatus()
    {
        $dirname = $this->getDirname();
        $constpref = '_MD_'.strtoupper($dirname);
        switch ($this->get('status')) {
        case Lenum_WorkflowStatus::DELETED:
            return constant($constpref.'_LANG_STATUS_DELETED');
            break;
        case Lenum_WorkflowStatus::REJECTED:
            return constant($constpref.'_LANG_STATUS_REJECTED');
            break;
        case Lenum_WorkflowStatus::PROGRESS:
            return constant($constpref.'_LANG_STATUS_PROGRESS');
            break;
        case Lenum_WorkflowStatus::FINISHED:
            return constant($constpref.'_LANG_STATUS_FINISHED');
            break;
        }
    }

    /**
     * check whether user is in progress.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function checkStep($uid)
    {
        $dirname = $this->getDirname();
        $trustDirname = Legacy_Utils::getTrustDirnameByDirname($dirname);
        $cnameUtils = ucfirst($trustDirname).'_Utils';
        $memberHandler = &$cnameUtils::getXoopsHandler('member');
        $gids = $memberHandler->getGroupsByUser($uid);
        $aObj = $this->getApprovalObject();
        if ($aObj === null) {
            return false;
        }
        $aUid = $aObj->get('uid');
        $aGid = $aObj->get('gid');
        if ($aUid > 0) {
            if ($aUid == $uid) {
                return true;
            }
        } elseif ($aGid > 0) {
            if (in_array($aGid, $gids)) {
                return true;
            }
        } else {
            $gid = $this->getTargetGroupId();
            if ($cnameUtils::isGroupAdmin($uid, $gid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * get approval object.
     *
     * @return {Trustdirname}_ApprovalObject
     */
    public function getApprovalObject()
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $this->get('dirname')));
        $criteria->add(new Criteria('dataname', $this->get('dataname')));
        $criteria->add(new Criteria('step', $this->get('step')));
        $aHandler = Legacy_Utils::getModuleHandler('approval', $this->getDirname());
        $aObjs = $aHandler->getObjects($criteria);
        if (empty($aObjs)) {
            return;
        }

        return array_shift($aObjs);
    }

    /**
     * get target group id.
     *
     * @return string
     */
    public function getTargetGroupId()
    {
        $dirname = $this->getDirname();
        $trustDirname = Legacy_Utils::getTrustDirnameByDirname($dirname);
        $cnameUtils = ucfirst($trustDirname).'_Utils';

        return $cnameUtils::getTargetGroupId($this->get('dirname'), $this->get('dataname'), $this->get('target_id'));
    }
}

/**
 * item object handler.
 */
class Xworkflow_ItemHandler extends XoopsObjectGenericHandler
{
    /**
     * table.
     *
     * @var string
     */
    public $mTable = '{dirname}_item';

    /**
     * primary id.
     *
     * @var string
     */
    public $mPrimary = 'item_id';

    /**
     * object class name.
     *
     * @var string
     */
    public $mClass = '';

    /**
     * dirname.
     *
     * @var string
     */
    public $mDirname = '';

    /**
     * trust dirname.
     *
     * @var string
     */
    public $mTrustDirname = '';

    /**
     * constructor.
     * 
     * @param XoopsDatabase &$db
     * @param string        $dirname
     */
    public function __construct(&$db, $dirname)
    {
        $this->mTable = strtr($this->mTable, array('{dirname}' => $dirname));
        $this->mDirname = $dirname;
        $this->mTrustDirname = Legacy_Utils::getTrustDirnameByDirname($dirname);
        $this->mClass = ucfirst($this->mTrustDirname).'_ItemObject';
        parent::__construct($db);
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
        $hHandler = Legacy_Utils::getModuleHandler('history', $this->mDirname);
        $hHandler->deleteAll(new Criteria('item_id', $obj->get('item_id')));

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
        $aHandler = Legacy_Utils::getModuleHandler('approval', $this->mDirname);
        $aObj = $aHandler->getNextApproval($obj->get('dirname'), $obj->get('dataname'), $obj->get('step'));
        if (is_object($aObj)) {
            $obj->set('step', $aObj->get('step'));
            $obj->set('status', Lenum_WorkflowStatus::PROGRESS);
        } else {
            $obj->set('status', Lenum_WorkflowStatus::FINISHED);
        }
        if ($this->insert($obj)) {
            $result = null;
            XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

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
        $cnameUtils = ucfirst($this->mTrustDirname).'_Utils';
        $cnameRevertTo = ucfirst($this->mTrustDirname).'_RevertTo';
        $revertTo = $cnameUtils::getModuleConfig($obj->getDirname(), 'revert_to');
        $aHandler = Legacy_Utils::getModuleHandler('approval', $obj->getDirname());
        $aObj = $aHandler->getPreviousApproval($obj->get('dirname'), $obj->get('dataname'), $obj->get('step'));
        if (!is_object($aObj) || $revertTo == $cnameRevertTo::ZERO) {
            $obj->set('step', 0);
            $obj->set('status', Lenum_WorkflowStatus::REJECTED);
        } else {
            $obj->set('step', $aObj->get('step'));
        }
        if ($this->insert($obj)) {
            $result = null;
            XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

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
        $cnameUtils = ucfirst($this->mTrustDirname).'_Utils';
        $aHandler = Legacy_Utils::getModuleHandler('approval', $this->mDirname);
        $memberHandler = &$cnameUtils::getXoopsHandler('member');
        $obj = $this->getProgressItemByTargetId($dirname, $dataname, $target_id);
        if ($obj === null) {
            return $uids;
        }
        $step = $obj->get('step');
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $dirname));
        $criteria->add(new Criteria('dataname', $dataname));
        $criteria->add(new Criteria('step', $step));
        $aObjs = $aHandler->getObjects($criteria);
        if (empty($aObjs)) {
            return $uids;
        }
        $aObj = array_shift($aObjs); // have to only one object
        $uid = $aObj->get('uid');
        $gid = $aObj->get('gid');
        if ($uid > 0) {
            $uids[] = $uid;
        } elseif ($gid  > 0) {
            $uids = $memberHandler->getUsersByGroup($gid, false);
        } else {
            $gid = $cnameUtils::getTargetGroupId($dirname, $dataname, $target_id);
            $uids = $cnameUtils::getGroupAdminUserIds($gid);
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
        $cnameUtils = ucfirst($this->mTrustDirname).'_Utils';
        $aHandler = Legacy_Utils::getModuleHandler('approval', $this->mDirname);
        $memberHandler = &$cnameUtils::getXoopsHandler('member');
        $gids = &$memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $ret = array();
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uid', $uid));
        if (!empty($gids)) {
            $criteria->add(new Criteria('gid', $gids, 'IN'), 'OR');
        }
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $ret = array_merge($ret, $this->getProgressItemsByApprovalObject($aObj));
        }
        // append group admin items
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uid', 0));
        $criteria->add(new Criteria('gid', 0));
        $aObjs = $aHandler->getObjects($criteria);
        foreach ($aObjs as $aObj) {
            $objs = $this->getProgressItemsByApprovalObject($aObj);
            foreach ($objs as $obj) {
                $gid = $obj->getTargetGroupId();
                if ($cnameUtils::isGroupAdmin($uid, $gid)) {
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
     * @return {Trustdirname}_ItemObject
     */
    public function getProgressItemByTargetId($dirname, $dataname, $target_id)
    {
        $objs = $this->_getProgressItems($dirname, $dataname, $target_id);
        if (empty($objs)) {
            return;
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
     * @return {Trustdirname}_ItemObject[]
     */
    private function _getProgressItems($dirname, $dataname, $target_id = false, $step = false)
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $dirname));
        $criteria->add(new Criteria('dataname', $dataname));
        $criteria->add(new Criteria('status', Lenum_WorkflowStatus::PROGRESS));
        if ($target_id !== false) {
            $criteria->add(new Criteria('target_id', $target_id));
        }
        if ($step !== false) {
            $criteria->add(new Criteria('step', $step));
        }
        $criteria->add(new Criteria('deletetime', 0));

        return $this->getObjects($criteria);
    }
}
