<?php

use Xworkflow\Core\XoopsUtils;

require_once dirname(__FILE__).'/ItemListAction.class.php';

/**
 * item list action.
 */
class Xworkflow_IndexAction extends Xworkflow_ItemListAction
{
    /**
     * get default view.
     *
     * @return Enum
     */
    public function getDefaultView()
    {
        $this->mFilter = &$this->_getFilterForm();
        $this->mFilter->fetch();
        $uid = XoopsUtils::getUid();
        $this->mObjects = $this->_getHandler()->getProgressItemsByUserId($uid);
        $this->_mIsMyTask = true;

        return $this->_getFrameViewStatus('INDEX');
    }
}
