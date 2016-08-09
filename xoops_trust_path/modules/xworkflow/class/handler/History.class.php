<?php

/**
 * history object.
 */
class Xworkflow_HistoryObject extends XoopsSimpleObject
{
    /**
     * flag for item loaded.
     *
     * @var bool
     */
    protected $_mItemLoadedFlag = false;

    /**
     * item cache.
     *
     * @var {Trustdirname}_ItemObject
     */
    public $mItem = null;

    /**
     * constructor.
     * 
     * @param string $dirname
     */
    public function __construct()
    {
        $this->initVar('progress_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('item_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('uid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('step', XOBJ_DTYPE_INT, '', false);
        $this->initVar('result', XOBJ_DTYPE_INT, '', false);
        $this->initVar('comment', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('posttime', XOBJ_DTYPE_INT, time(), false);
    }

    /**
     * load item.
     */
    public function loadItem()
    {
        if ($this->_mItemLoadedFlag == false) {
            $handler = Legacy_Utils::getModuleHandler('item', $this->getDirname());
            $this->mItem = &$handler->get($this->get('item_id'));
            $this->_mItemLoadedFlag = true;
        }
    }

    /**
     * get show result.
     * 
     * @return string
     */
    public function getShowResult()
    {
        $dirname = $this->getDirname();
        $trustDirname = Legacy_Utils::getTrustDirnameByDirname($dirname);
        $constpref = '_MD_'.strtoupper($dirname);
        $cnameResult = ucfirst($trustDirname).'_Result';
        switch ($this->get('result')) {
        case $cnameResult::HOLD:
            return constant($constpref.'_LANG_RESULT_HOLD');
        case $cnameResult::REJECT:
            return constant($constpref.'_LANG_RESULT_REJECT');
        case $cnameResult::APPROVE:
            return constant($constpref.'_LANG_RESULT_APPROVE');
        }
    }
}

/**
 * history object handler.
 */
class Xworkflow_HistoryHandler extends XoopsObjectGenericHandler
{
    /**
     * table.
     *
     * @var string
     */
    public $mTable = '{dirname}_history';

    /**
     * primary id.
     *
     * @var string
     */
    public $mPrimary = 'progress_id';

    /**
     * object class name.
     *
     * @var string
     */
    public $mClass = '';

    /**
     * dirname.
     *
     * @var string
     */
    public $mDirname = '';

    /**
     * trust dirname.
     *
     * @var string
     */
    public $mTrustDirname = '';

    /**
     * constructor.
     * 
     * @param XoopsDatabase &$db
     * @param string        $dirname
     */
    public function __construct(&$db, $dirname)
    {
        $this->mTable = strtr($this->mTable, array('{dirname}' => $dirname));
        $this->mDirname = $dirname;
        $this->mTrustDirname = Legacy_Utils::getTrustDirnameByDirname($dirname);
        $this->mClass = ucfirst($this->mTrustDirname).'_HistoryObject';
        parent::__construct($db);
    }
}
