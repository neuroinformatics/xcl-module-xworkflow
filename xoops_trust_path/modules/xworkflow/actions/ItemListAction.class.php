<?php

use Xworkflow\Core\Functions;
use Xworkflow\Core\XoopsUtils;

require_once dirname(dirname(__FILE__)).'/class/AbstractListAction.class.php';

/**
 * item list action.
 */
class Xworkflow_ItemListAction extends Xworkflow_AbstractListAction
{
    /**
     * flag for my task.
     *
     * @var bool
     */
    protected $mIsMyTask = false;

    /**
     * data name.
     *
     * @var string
     */
    protected $mDataname = 'item';

    /**
     * get handler.
     *
     * return {Trustdirname}_ItemHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'ItemObject');

        return $handler;
    }

    /**
     * get filter form.
     *
     * return {Trustdirname}_ItemFilterForm
     */
    protected function &_getFilterForm()
    {
        $filter = &$this->mAsset->getObject('filter', 'Item', false);
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());

        return $filter;
    }

    /**
     * get base url.
     *
     * @return string
     */
    protected function _getBaseUrl()
    {
        return XoopsUtils::renderUri($this->mAsset->mDirname, 'item');
    }

    /**
     * execute view index.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_item_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('dataname', $this->mDataname);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('isMyTask', $this->mIsMyTask);
        $render->setAttribute('clients', Functions::getClients());
    }
}
