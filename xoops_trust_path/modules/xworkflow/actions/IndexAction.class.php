<?php


require_once dirname(__FILE__).'/ItemListAction.class.php';

/**
 * item list action.
 */
class Xworkflow_IndexAction extends Xworkflow_ItemListAction
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_mIsMyTask = true;
    }

    /**
     * get handler.
     *
     * return {Trustdirname}_ItemHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'MyItemObject');

        return $handler;
    }
}
