<?php

namespace Xworkflow\Object;

use Xworkflow\Core\Functions;
use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;

/**
 * item object.
 */
class ItemObject extends AbstractObject
{
    /**
     * histories.
     *
     * @var array
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
    public function __construct($dirname)
    {
        parent::__construct($dirname);
        $this->initVar('item_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('title', XOBJ_DTYPE_STRING, null, true, 255);
        $this->initVar('dirname', XOBJ_DTYPE_STRING, null, true, 45);
        $this->initVar('dataname', XOBJ_DTYPE_STRING, null, true, 45);
        $this->initVar('target_id', XOBJ_DTYPE_INT, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('step', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_INT, \Lenum_WorkflowStatus::PROGRESS, true);
        $this->initVar('revision', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('url', XOBJ_DTYPE_TEXT, '', true);
        $this->initVar('posttime', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('updatetime', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('deletetime', XOBJ_DTYPE_INT, 0, true);
    }

    /**
     * set first step.
     */
    public function setFirstStep()
    {
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aObj = $aHandler->getNextApproval($this->get('dirname'), $this->get('dataname'), 0);
        if ($aObj) {
            $this->set('step', $aObj->get('step'));
        } else {
            $this->set('step', 0);
            $this->set('status', \Lenum_WorkflowStatus::FINISHED);
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
            $hHandler = &XoopsUtils::getModuleHandler('HistoryObject', $this->mDirname);
            $criteria = new \Criteria('item_id', $this->get('item_id'));
            $criteria->setSort('posttime', $order);
            $this->mHistory = $hHandler->getObjects($criteria);
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
        $langman = new LanguageManager($this->mDirname, 'main');
        switch ($this->get('status')) {
        case \Lenum_WorkflowStatus::DELETED:
            return $langman->get('LANG_STATUS_DELETED');
        case \Lenum_WorkflowStatus::REJECTED:
            return $langman->get('LANG_STATUS_REJECTED');
        case \Lenum_WorkflowStatus::PROGRESS:
            return $langman->get('LANG_STATUS_PROGRESS');
        case \Lenum_WorkflowStatus::FINISHED:
            return $langman->get('LANG_STATUS_FINISHED');
        }

        return '';
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
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);

        return $iHandler->isInProgress($this, $uid);
    }

    /**
     * get approval object.
     *
     * @return object
     */
    public function getApprovalObject()
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('dirname', $this->get('dirname')));
        $criteria->add(new \Criteria('dataname', $this->get('dataname')));
        $criteria->add(new \Criteria('step', $this->get('step')));
        $aHandler = &XoopsUtils::getModuleHandler('ApprovalObject', $this->mDirname);
        $aObjs = $aHandler->getObjects($criteria);
        if (empty($aObjs)) {
            return null;
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
        $trustDirname = XoopsUtils::getTrustDirname();

        return Functions::getTargetGroupId($this->get('dirname'), $this->get('dataname'), $this->get('target_id'));
    }
}
