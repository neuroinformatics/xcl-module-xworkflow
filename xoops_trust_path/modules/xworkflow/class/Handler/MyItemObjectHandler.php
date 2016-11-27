<?php

namespace Xworkflow\Handler;

use Xworkflow\Core\XoopsUtils;

/**
 * my item object handler.
 */
class MyItemObjectHandler extends ItemObjectHandler
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
        $this->mClassName = str_replace('MyItemObject', 'ItemObject', $this->mClassName);
    }

    /**
     * count objects.
     *
     * @param \CriteriaElement  $criteria
     * @param Core\JoinCriteria $join
     *
     * @return int
     */
    public function getCount($criteria = null, $join = null)
    {
        $uid = XoopsUtils::getUid();
        list($criteria, $join) = $this->_getInProgressItemCriteria($uid, $criteria);

        return parent::getCount($criteria, $join);
    }

    /**
     * get objects.
     *
     * @param \CriteriaElement  $criteria
     * @param string            $fieldlist
     * @param bool              $distinct
     * @param bool              $idAsKey
     * @param Core\JoinCriteria $join
     *
     * @return array
     */
    public function getObjects($criteria = null, $fieldlist = '', $distinct = false, $idAsKey = false, $join = null)
    {
        $uid = XoopsUtils::getUid();
        list($criteria, $join) = $this->_getInProgressItemCriteria($uid, $criteria);

        return parent::getObjects($criteria, $fieldlist, $distinct, $idAsKey, $join);
    }
}
