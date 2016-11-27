<?php

namespace Xworkflow\Object;

use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;
use Xworkflow\Enum;

/**
 * history object.
 */
class HistoryObject extends AbstractObject
{
    /**
     * item cache.
     *
     * @var Object\ItemObject
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
            $iHandler = &XoopsUtils::getModuleHandler('ItemObject', $this->mDirname);
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
        $langman = new LanguageManager($this->mDirname, 'main');
        switch ($this->get('result')) {
        case Enum\Result::HOLD:
            return $langman->get('LANG_RESULT_HOLD');
        case Enum\Result::REJECT:
            return $langman->get('LANG_RESULT_REJECT');
        case Enum\Result::APPROVE:
            return $langman->get('LANG_RESULT_APPROVE');
        }

        return '';
    }
}
