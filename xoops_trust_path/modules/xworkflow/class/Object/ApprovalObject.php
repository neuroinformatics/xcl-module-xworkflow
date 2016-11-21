<?php

namespace Xworkflow\Object;

use Xworkflow\Core\XoopsUtils;

/**
 * approval object.
 */
class ApprovalObject extends AbstractObject
{
    /**
     * constructor.
     *
     * @param string $dirname
     */
    public function __construct($dirname)
    {
        parent::__construct($dirname);
        $this->initVar('approval_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('gid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('dirname', XOBJ_DTYPE_STRING, null, true, 45);
        $this->initVar('dataname', XOBJ_DTYPE_STRING, null, true, 45);
        $this->initVar('step', XOBJ_DTYPE_INT, 10, true);
    }

    /**
     * count my item.
     *
     * @return int
     */
    public function countProgressItem()
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('dirname', $this->get('dirname')));
        $criteria->add(new \Criteria('dataname', $this->get('dataname')));
        $criteria->add(new \Criteria('step', $this->get('step')));
        $criteria->add(new \Criteria('status', \Lenum_WorkflowStatus::PROGRESS));
        $criteria->add(new \Criteria('deletetime', 0));
        $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);

        return $iHandler->getCount($criteria);
    }
}
