<?php

namespace Xworkflow\Handler;

/**
 * approval object handler.
 */
class ApprovalObjectHandler extends AbstractObjectHandler
{
    /**
     * constructor.
     *
     * @param \XoopsDatabase &$db
     * @param string         $dirname
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
     * @return Object\ApprovalObject
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
     * @return Object\ApprovalObject
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
}
