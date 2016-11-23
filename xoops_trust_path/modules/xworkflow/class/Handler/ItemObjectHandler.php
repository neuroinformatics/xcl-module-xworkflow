<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\Functions;
use Xworkflow\Core\JoinCriteria;
use Xworkflow\Core\TableFieldCriteria;
use Xworkflow\Core\XoopsUtils;

/**
 * item object handler.
 */
class ItemObjectHandler extends AbstractObjectHandler
{
    /**
     * flag for my task item.
     *
     * @var bool
     */
    private $mIsMyTask = false;

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
     * set my task.
     *
     * @param bool $isMyTask
     */
    public function setMyTask($isMyTask)
    {
        $this->mIsMyTask = $isMyTask;
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
            \XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new \XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

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
            \XCube_DelegateUtils::call('Legacy_WorkflowClient.UpdateStatus', new \XCube_Ref($result), $obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'), $obj->get('status'));

            return true;
        }

        return false;
    }

    /**
     * check whether item id is in progess.
     *
     * @param int $itemId
     * @param int $uid
     *
     * @return bool
     */
    public function checkInProgress($itemId, $uid)
    {
        $criteria = new \Criteria('item_id', $itemId, '=', $this->mTable);
        list($criteria, $join) = $this->_getProgressItemCriteria($uid, $criteria);

        return parent::getCount($criteria, $join);
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
        $uid = XoopsUtils::getUid();
        list($criteria, $join) = $this->_getProgressItemCriteria($uid);

        return parent::getCount($criteria, $join);
    }

    /**
     * count progress items.
     *
     * @param object $criteria
     * @param object $join
     *
     * @return int
     */
    public function getCount($criteria = null, $join = null)
    {
        if ($this->mIsMyTask) {
            $uid = XoopsUtils::getUid();
            list($criteria, $join) = $this->_getProgressItemCriteria($uid, $criteria);
        }

        return parent::getCount($criteria, $join);
    }

    /**
     * get progress items.
     *
     * @param object $criteria
     * @param string $fieldlist
     * @param bool   $distinct
     * @param object $join
     * @param bool   $idAsKey
     * @param object $join
     *
     * @return array
     */
    public function getObjects($criteria = null, $fieldlist = '', $distinct = false, $idAsKey = false, $join = null)
    {
        if ($this->mIsMyTask) {
            $uid = XoopsUtils::getUid();
            list($criteria, $join) = $this->_getProgressItemCriteria($uid, $criteria);
        }

        return parent::getObjects($criteria, $fieldlist, $distinct, $idAsKey, $join);
    }

    /**
     * get progress item criteria.
     *
     * @param int    $uid
     * @param object $cri
     *
     * @return array
     */
    private function _getProgressItemCriteria($uid, $cri = null)
    {
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aTable = $aHandler->getTable();
        $memberHandler = &xoops_gethandler('member');
        $gids = $memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $aGids = Functions::getAdminGroupIds($uid);
        $join = new JoinCriteria('INNER', $aTable, 'dirname', $this->mTable, 'dirname');
        $criteria = new \CriteriaCompo(new \Criteria('status', \Lenum_WorkflowStatus::PROGRESS, '=', $this->mTable));
        $criteria->add(new TableFieldCriteria($this->mTable, 'dataname', $aTable, 'dataname'));
        $criteria->add(new TableFieldCriteria($this->mTable, 'step', $aTable, 'step'));
        $criteria2 = new \CriteriaCompo(new \Criteria('uid', $uid, '=', $aTable));
        if (!empty($gids)) {
            $criteria2->add(new \Criteria('gid', $gids, 'IN', $aTable), 'OR');
        }
        if (!empty($aGids)) {
            if (XoopsUtils::getModuleVersion($this->mDirname) >= 200) {
                $criteria3 = new \CriteriaCompo(new \Criteria('target_gid', $aGids, 'IN', $this->mTable));
                $criteria3->add(new \Criteria('uid', 0, '=', $aTable));
                $criteria3->add(new \Criteria('gid', 0, '=', $aTable));
                $criteria2->add($criteria3, 'OR');
            }
        }
        $criteria->add($criteria2);
        if (is_object($cri)) {
            if (method_exists($cri, 'getSorts')) {
                foreach ($cri->getSorts() as $sort) {
                    $criteria->addSort($sort['sort'], $sort['order']);
                }
            } else {
                $criteria->setSort($cri->getSort(), $cri->getOrder());
            }
            $criteria->setLimit($cri->getLimit());
            $criteria->setStart($cri->getStart());
            if (!($cri instanceof \CriteriaElement)) {
                $criteria->add($cri);
            }
        }

        return array($criteria, $join);
    }
}
