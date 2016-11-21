<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\Functions;
use Xworkflow\Core\XoopsUtils;

/**
 * approval object handler.
 */
class ApprovalObjectHandler extends AbstractObjectHandler
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
        $this->mTable = $db->prefix($dirname.'_approval');
        $this->mPrimaryKey = 'approval_id';
    }

    /**
     * get next approval object.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $step
     *
     * @return object
     */
    public function getNextApproval($dirname, $dataname, $step)
    {
        $cri = new \CriteriaCompo();
        $cri->add(new \Criteria('dirname', $dirname));
        $cri->add(new \Criteria('dataname', $dataname));
        $cri->add(new \Criteria('step', $step, '>'));
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
     * @return object
     */
    public function getPreviousApproval($dirname, $dataname, $step)
    {
        $cri = new \CriteriaCompo();
        $cri->add(new \Criteria('dirname', $dirname));
        $cri->add(new \Criteria('dataname', $dataname));
        $cri->add(new \Criteria('step', $step, '<'));
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
     * @return array
     */
    public function getApprovalList($dirname, $dataname)
    {
        $cri = new \CriteriaCompo();
        $cri->add(new \Criteria('dirname', $dirname));
        $cri->add(new \Criteria('dataname', $dataname));
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
        $memberHandler = &xoops_gethandler('member');
        $iHandler = XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
        $gids = &$memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('uid', $uid));
        if (!empty($gids)) {
            $criteria->add(new \Criteria('gid', $gids, 'IN'), 'OR');
        }
        $cnt = $this->getCount($criteria);
        if ($cnt > 0) {
            return true;
        }
        // check group admin items
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('uid', 0));
        $criteria->add(new \Criteria('gid', 0));
        $objs = $this->getObjects($criteria);
        foreach ($objs as $obj) {
            $iObjs = $iHandler->getProgressItemsByApprovalObject($obj);
            foreach ($iObjs as $iObj) {
                $gid = $iObj->getTargetGroupId();
                if (Functions::isGroupAdmin($uid, $gid)) {
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
     * @return array
     */
    public function getApprovalUserIds($dirname, $dataname)
    {
        $uids = array();
        $memberHandler = &xoops_gethandler('member');
        $iHandler = XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
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
                    $uids = array_merge($uids, Functions::getGroupAdminUserIds($$gid));
                }
            }
        }
        $uids = array_unique($uids);
        sort($uids);

        return $uids;
    }
}
