<?php

namespace Xworkflow\Handler;

/**
 * history object handler.
 */
class HistoryObjectHandler extends AbstractObjectHandler
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
        $this->mTable = $db->prefix($dirname.'_history');
        $this->mPrimaryKey = 'progress_id';
    }
}
