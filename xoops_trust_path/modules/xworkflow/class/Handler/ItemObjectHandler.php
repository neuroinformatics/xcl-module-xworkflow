<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\Functions;
use Xworkflow\Core\JoinCriteria;
use Xworkflow\Core\TableFieldCriteria;
use Xworkflow\Core\XoopsUtils;
use Xworkflow\Enum;

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
     * get item object.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $id
     *
     * @return object
     */
    public function getItem($dirname, $dataname, $target_id)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('dirname', $dirname));
        $criteria->add(new \Criteria('dataname', $dataname));
        $criteria->add(new \Criteria('target_id', $target_id));
        $criteria->add(new \Criteria('deletetime', 0));
        $objs = $this->getObjects($criteria);
        if (empty($objs)) {
            return null;
        }

        return array_shift($objs);
    }

    /**
     * add item.
     *
     * @param string $title
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     * @param string $url
     *
     * @return bool
     */
    public function addItem($title, $dirname, $dataname, $target_id,  $url)
    {
        $obj = $this->getItem($dirname, $dataname, $target_id);
        if (is_null($obj)) {
            $obj = $this->create();
            $obj->set('title', $title);
            $obj->set('dirname', $dirname);
            $obj->set('dataname', $dataname);
            $obj->set('target_id', $target_id);
            $obj->set('url', $url);
            $obj->set('uid', XoopsUtils::getUid());
            $obj->setFirstStep();
        } else {
            $obj->set('title', $title);
            $obj->set('status', Enum\WorkflowStatus::PROGRESS);
            $obj->set('url', $url);
            $obj->set('updatetime', time());
            $obj->setFirstStep();
            $obj->incrementRevision();
        }

        return $this->insert($obj);
    }

    /**
     * delete item.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return bool
     */
    public function deleteItem($dirname, $dataname, $target_id)
    {
        $obj = $this->getItem($dirname, $dataname, $target_id);
        if (!is_object($obj)) {
            return false;
        }

        return $this->delete($obj);
    }

    /**
     * get history.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     *
     * @return array
     */
    public static function getHistory($dirname, $dataname, $target_id)
    {
        static $hKeys = array('step', 'uid', 'result', 'comment', 'posttime');
        $obj = $this->getItem($dirname, $dataname, $target_id);
        if (!is_object($obj)) {
            return array();
        }
        $obj->loadHistory();

        return $obj->mHistory;
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
        $hHandler = &XoopsUtils::getModuleHandler('HistoryObject', $this->mDirname);
        $hHandler->deleteAll(new \Criteria($this->mPrimaryKey, $obj->get($this->mPrimaryKey)), $force);

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
            $obj->set('status', Enum\WorkflowStatus::PROGRESS);
            if ($aObj->get('uid') == 0 && $aObj->get('gid') == 0) {
                $target_gid = Functions::getTargetGroupId($obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'));
                if ($target_gid === false) {
                    return false;
                }
                $obj->set('target_gid', $target_gid);
            }
        } else {
            $obj->set('status', Enum\WorkflowStatus::FINISHED);
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
        $revertTo = XoopsUtils::getModuleConfig($this->mDirname, 'revert_to');
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aObj = $aHandler->getPreviousApproval($obj->get('dirname'), $obj->get('dataname'), $obj->get('step'));
        if (!is_object($aObj) || $revertTo == Enum\RevertTo::ZERO) {
            $obj->set('step', 0);
            $obj->set('status', Enum\WorkflowStatus::REJECTED);
        } else {
            $obj->set('step', $aObj->get('step'));
            if ($aObj->get('uid') == 0 && $aObj->get('gid') == 0) {
                $target_gid = Functions::getTargetGroupId($obj->get('dirname'), $obj->get('dataname'), $obj->get('target_id'));
                if ($target_gid === false) {
                    return false;
                }
                $obj->set('target_gid', $target_gid);
            }
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
     * @param object $obj
     * @param int    $uid
     *
     * @return bool
     */
    public function isInProgress($obj, $uid)
    {
        $criteria = new \Criteria($this->mPrimaryKey, $obj->get($this->mPrimaryKey), '=', $this->mTable);
        list($criteria, $join) = $this->_getInProgressItemCriteria($uid, $criteria);

        return parent::getCount($criteria, $join);
    }

    /**
     * count in progress items.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countInProgressItems($uid)
    {
        list($criteria, $join) = $this->_getInProgressItemCriteria($uid);

        return parent::getCount($criteria, $join);
    }

    /**
     * get progress item criteria.
     *
     * @param int    $uid
     * @param object $cri
     *
     * @return array
     */
    protected function _getInProgressItemCriteria($uid, $cri = null)
    {
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aTable = $aHandler->getTable();
        $memberHandler = &xoops_gethandler('member');
        $gids = $memberHandler->getGroupsByUser($uid);
        $gids = array_diff($gids, array(XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS));
        $aGids = Functions::getAdminGroupIds($uid);
        $join = new JoinCriteria('INNER', $aTable, 'dirname', $this->mTable, 'dirname');
        $criteria = new \CriteriaCompo(new \Criteria('status', Enum\WorkflowStatus::PROGRESS, '=', $this->mTable));
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
