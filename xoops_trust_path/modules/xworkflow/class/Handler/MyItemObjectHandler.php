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
     * @param object &$db
     * @param string $dirname
     */
    public function __construct(&$db, $dirname)
    {
        parent::__construct($db, $dirname);
        $this->mClassName = str_replace('MyItemObject', 'ItemObject', $this->mClassName);
    }

    /**
     * count objects.
     *
     * @param object $criteria
     * @param object $join
     *
     * @return int
     */
    public function getCount($criteria = null, $join = null)
    {
        $uid = XoopsUtils::getUid();
        list($criteria, $join) = $this->_getProgressItemCriteria($uid, $criteria);

        return parent::getCount($criteria, $join);
    }

    /**
     * get objects.
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
        $uid = XoopsUtils::getUid();
        list($criteria, $join) = $this->_getProgressItemCriteria($uid, $criteria);

        return parent::getObjects($criteria, $fieldlist, $distinct, $idAsKey, $join);
    }
}
