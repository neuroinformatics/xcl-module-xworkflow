<?php

namespace Xworkflow\Object;

use Xworkflow\Core\XoopsUtils;

/**
 * history object.
 */
class HistoryObject extends AbstractObject
{
    /**
     * item cache.
     *
     * @var object
     */
    public $mItem = null;

    /**
     * constructor.
     *
     * @param string $dirname
     */
    public function __construct($dirname)
    {
        parent::__construct($dirname);
        $this->initVar('progress_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('step', XOBJ_DTYPE_INT, null, true);
        $this->initVar('result', XOBJ_DTYPE_INT, null, true);
        $this->initVar('comment', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('posttime', XOBJ_DTYPE_INT, time(), true);
    }

    /**
     * load item.
     */
    public function loadItem()
    {
        if (is_null($this->mItem)) {
            $iHandler = XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
            $this->mItem = $iHandler->get($this->get('item_id'));
        }
    }

    /**
     * get show result.
     *
     * @return string
     */
    public function getShowResult()
    {
        $trustDirname = XoopsUtils::getTrustDirname();
        $cnameResult = ucfirst($trustDirname).'_Result';
        switch ($this->get('result')) {
        case $cnameResult::HOLD:
            return XoopsUtils::getModuleConstant('main', $this->mDirname, 'LANG_RESULT_HOLD');
        case $cnameResult::REJECT:
            return XoopsUtils::getModuleConstant('main', $this->mDirname, 'LANG_RESULT_REJECT');
        case $cnameResult::APPROVE:
            return XoopsUtils::getModuleConstant('main', $this->mDirname, 'LANG_RESULT_APPROVE');
        }

        return '';
    }
}
