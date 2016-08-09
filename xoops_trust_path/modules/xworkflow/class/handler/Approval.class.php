<?php

/**
 * approval object.
 */
class Xworkflow_ApprovalObject extends XoopsSimpleObject
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->initVar('approval_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('uid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('gid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('dirname', XOBJ_DTYPE_STRING, '', false, 45);
        $this->initVar('dataname', XOBJ_DTYPE_STRING, '', false, 45);
        $this->initVar('step', XOBJ_DTYPE_INT, 10, false);
    }

    /**
     * count my item.
     * 
     * @return int
     */
    public function countProgressItem()
    {
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $this->get('dirname')));
        $cri->add(new Criteria('dataname', $this->get('dataname')));
        $cri->add(new Criteria('step', $this->get('step')));
        $cri->add(new Criteria('status', Lenum_WorkflowStatus::PROGRESS));
        $cri->add(new Criteria('deletetime', 0));

        return Legacy_Utils::getModuleHandler('item', $this->getDirname())->getCount($cri);
    }
}

/**
 * approval object handler.
 */
class Xworkflow_ApprovalHandler extends XoopsObjectGenericHandler
{
    /**
     * table.
     *
     * @var string
     */
    public $mTable = '{dirname}_approval';

    /**
     * primary id.
     *
     * @var string
     */
    public $mPrimary = 'approval_id';

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
        $this->mClass = ucfirst($this->mTrustDirname).'_ApprovalObject';
        parent::__construct($db);
    }

    /**
     * get next approval object.
     * 
     * @param string $dirname
     * @param string $dataname
     * @param int    $step
     *
     * @return {Trustdirname}_ApprovalObject
     */
    public function getNextApproval($dirname, $dataname, $step)
    {
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $dirname));
        $cri->add(new Criteria('dataname', $dataname));
        $cri->add(new Criteria('step', $step, '>'));
        $cri->setSort('step', 'ASC');
        $objs = $this->getObjects($cri);

        return (count($objs) > 0) ? array_shift($objs) : null;
    }

    /**
     * get previous approval object.
     * 
     * @param string $dirname
     * @param string $dataname
     * @param int    $step
     *
     * @return {Trustdirname}_ApprovalObject
     */
    public function getPreviousApproval($dirname, $dataname, $step)
    {
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $dirname));
        $cri->add(new Criteria('dataname', $dataname));
        $cri->add(new Criteria('step', $step, '<'));
        $cri->setSort('step', 'DESC');
        $objs = $this->getObjects($cri);

        return (count($objs) > 0) ? array_shift($objs) : null;
    }

    /**
     * get approval object list.
     * 
     * @param string $dirname
     * @param string $dataname
     *
     * @return {Trustdirname}_ApprovalObject[]
     */
    public function getApprovalList($dirname, $dataname)
    {
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $dirname));
        $cri->add(new Criteria('dataname', $dataname));
        $cri->setSort('step', 'ASC');

        return $this->getObjects($cri);
    }

    /**
     * check whehter if user is approval user.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function isApprovalUser($uid)
    {
        $cnameUtils = ucfirst($this->mTrustDirname).'_Utils';
        $memberHandler = &$cnameUtils::getXoopsHandler('member');
        $iHandler = Legacy_Utils::getModuleHandler('item', $this->mDirname);
        $gids = &$memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uid', $uid));
        if (!empty($gids)) {
            $criteria->add(new Criteria('gid', $gids, 'IN'), 'OR');
        }
        $cnt = $this->getCount($criteria);
        if ($cnt > 0) {
            return true;
        }
        // check group admin items
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('uid', 0));
        $criteria->add(new Criteria('gid', 0));
        $objs = $this->getObjects($criteria);
        foreach ($objs as $obj) {
            $iObjs = $iHandler->getProgressItemsByApprovalObject($obj);
            foreach ($iObjs as $iObj) {
                $gid = $iObj->getTargetGroupId();
                if ($cnameUtils::isGroupAdmin($uid, $gid)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * get approval user ids.
     *
     * @param string $dirname
     * @param string $dataname
     *
     * @return array(int) user ids
     */
    public function getApprovalUserIds($dirname, $dataname)
    {
        $uids = array();
        $cnameUtils = ucfirst($this->mTrustDirname).'_Utils';
        $memberHandler = &$cnameUtils::getXoopsHandler('member');
        $iHandler = Legacy_Utils::getModuleHandler('item', $this->mDirname);
        $objs = $this->getApprovalList($dirname, $dataname);
        foreach ($objs as $obj) {
            $uid = $obj->get('uid');
            $gid = $obj->get('gid');
            if ($uid > 0) {
                $uids[] = $uid;
            } elseif ($gid > 0) {
                $uids = array_merge($uids, $memberHandler->getUsersByGroup($gid, false));
            } else {
                $iObjs = $iHandler->getProgressItemsByApprovalObject($obj);
                foreach ($iObjs as $iObj) {
                    $gid = $iObj->getTargetGroupId();
                    $uids = array_merge($uids, $cnameUtils::getGroupAdminUserIds($$gid));
                }
            }
        }
        $uids = array_unique($uids);
        sort($uids);

        return $uids;
    }
}
